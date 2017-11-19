<?php
namespace Home\Controller;
use Think\Controller;
/**
* 集碎片活动
*/
class ProductchipController extends BaseController {

	//根据c_activitytype 得到 活动c_id
	public function get_aid($activitytype){
		$w['c_activitytype'] = $activitytype;
		$aid = M('activity')->where($w)->getField('c_id');
		return $aid;
	}

	// 碎片列表
	public function chip_list()
	{
		$Chips = M('activity_prize as a');
		$where['a.c_pid'] = 0;
		$where['a.c_aid'] = $this->get_aid(7);
		$count = $Chips->where($where)->count();
		$page = getpage($count,5);
		$limit = $page->firstRow.','.$page->listRows;
		$field = 'a.c_id,a.c_pid,a.c_aid,a.c_name,a.c_pcode,a.c_imgpath,a.c_pic,c_value,a.c_totalnum,a.c_num,a.c_marks,a.c_state,a.c_today_prize,a.c_address,a.c_longitude,a.c_latitude,a.c_addtime,u.c_nickname';
		$join = 'left join t_users as u on u.c_ucode=a.c_acode';
		$data = $Chips->field($field)->join($join)->where($where)->limit($limit)->order('a.c_id desc')->select();
		foreach ($data as $key => $value) {
			$data[$key]['c_activityname'] = M('Activity')->where('c_id='.$value['c_aid'])->getField('c_activityname');
			$data[$key]['child'] = $Chips->field($field)->join($join)->where('a.c_pid='.$value['c_id'])->select();
		}
		$this->data = $data;
		$this->root_url = GetHost()."/";
		$this->page = $page->show();
		$this->assign('count',$count);
		$this->display();
	}

	//ajax 修改活动奖品是否参加活动状态
	public function chip_state(){
		$Id = I('gid');
	    $state = I('active');

	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;
	    $data['c_state'] = $state;
	    $result = M('activity_prize')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//活动奖品验证数据
	function pcheckfrom($data){
		$pid = $data['pid'];
		if(!empty($pid)){
			if ($data['marks'] == '') {
		    	$this->error("碎片标识不能为空");
			}
			if (empty($data['today_prize'])) {
	    		$this->error("碎片必须选择碎片类型");
			}
			if ($data['address'] == '') {
		    	$this->error("碎片所在地址不能为空");
			}
			if ($data['longitude'] == '') {
		    	$this->error("碎片所在经度不能为空");
			}
			if ($data['latitude'] == '') {
		    	$this->error("碎片所在纬度不能为空");
			}
		}

		if (empty($data['c_name'])) {
	    	$this->error("名称不能为空");
		}
		if(empty($pid)){
			if ($data['value'] == '') {
		    	$this->error("商品价值不能为空");
			}
			if ($data['acode'] == '') {
		    	$this->error("所属商家不能为空");
			}
		}
		if (empty($data['totalnum'])) {
	    	$this->error("总数量不能为空");
		}

		if ($data['num'] == '') {
	    	$this->error("剩余数量不能为空");
		}

		if (empty($data['state'])) {
		    $this->error("状态未选择");
		}
	}

	//活动奖品添加
	public function chip_add(){
		$this->action = 'chip_add';
		$aid = $this->get_aid(7);
		$w['c_pid'] = 0;
		$w['c_aid'] = $aid;
		$this->pro_list = M('activity_prize')->field('c_id,c_pid,c_name')->where($w)->select();

		if(IS_POST){
			$db = M('activity_prize');

		   	$this->pcheckfrom($_POST);
		    if(empty($_FILES['imgpath']['name'])){
	    		$this->error("图片必须上传");
	    	}
	    	if(empty($_POST['pid'])){
	    		if(empty($_FILES['pic']['name'])){
	    			$this->error("碎片首页展示图片必须上传");
	    		}
	    	}
		    $fileresult = uploadimg('activity');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$pid = $_POST['pid'];
			if(!empty($pid)){
				$w['c_id'] = $pid;
				$pinfo = $db->where($w)->field('c_pcode,c_acode')->find();
				$data['c_acode'] = $pinfo['c_acode'];
				$data['c_pcode'] = $pinfo['c_pcode'];
			}else{
				$data['c_pcode'] = 'c'.time();
				$data['c_acode'] = $_POST['acode'];
			}

			$data['c_imgpath'] = $fileresult['data']['imgpath'];
			$data['c_pic'] = $fileresult['data']['pic'];
			$data['c_pid'] = $pid;
		    $data['c_aid'] = $this->get_aid(7);
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = 2;
		    $data['c_state'] = $_POST['state'];
		    $data['c_marks'] = $_POST['marks'];
		    $data['c_address'] = $_POST['address'];
		    $data['c_longitude'] = $_POST['longitude'];
		    $data['c_latitude'] = $_POST['latitude'];
		    $data['c_today_prize'] = $_POST['today_prize'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Productchip/chip_list';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//活动奖品编辑
	public function chip_edit(){
		$this->action = 'chip_edit';
		$Id = I('Id');
		$w['a.c_id'] = $Id;
		$field = 'a.*,u.c_nickname';
		$join = 'left join t_users as u on u.c_ucode=a.c_acode';
		$arr = M('activity_prize as a')->field($field)->join($join)->where($w)->find();

		$aid = $this->get_aid(7);
		$w1['c_pid'] = 0;
		$w1['c_aid'] = $aid;
		$this->pro_list = M('activity_prize')->field('c_id,c_pid,c_name')->where($w1)->select();

		$this->vo = $arr;
		if(IS_POST){
			$db = M('activity_prize');
		    $this->pcheckfrom($_POST);
		    if(!empty($_FILES['imgpath']['name'])){
		    	$fileresult = uploadimg('activity');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    }

		    if(empty($_POST['pid'])){
	    		if(!empty($_FILES['pic']['name'])){
	    			$fileresult1 = uploadimg('activity');
					if ($fileresult1['code'] != 0) {
					  $this->error($fileresult1['msg']);
					}
					$data['c_pic'] = $fileresult1['data']['pic'];
		    	}
	    	}

		    $data['c_pid'] = $_POST['pid'];
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_marks'] = $_POST['marks'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_address'] = $_POST['address'];
		    $data['c_longitude'] = $_POST['longitude'];
		    $data['c_latitude'] = $_POST['latitude'];
		    $data['c_today_prize'] = $_POST['today_prize'];

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Productchip/chip_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('chip_add');
	}

	//活动奖品删除
    public function chip_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $pinfo = M('activity_prize')->where($where)->find();
        if($pinfo['c_pid'] == 0){
        	$w['c_id'] = $pinfo['c_id'];
        	$result = M('activity_prize')->where($w)->delete();
        	if(!$result){
            	$this->ajaxReturn(Message(1000,'删除失败'));
        	}
        }

        $result = M('activity_prize')->where($where)->delete();
        if(!$result){
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $this->ajaxReturn(Message(0,'删除成功'));
    }

    //碎片领取统计
    public function chip_log(){
    	$flag = 0;
    	//排序
        $orderby = " ORDER BY num DESC ";
        //条件
        $comd = array();
        $aid = $this->get_aid(7);
        $comd[] = "l.c_aid=$aid";
        $pcode = I('pcode');
        $comd[] = "l.c_pcode='".$pcode."'";

        $nickname = I('nickname');
		if(isset($nickname)&&!empty($nickname)){
            $comd[] = "u.c_nickname LIKE '%".trim($nickname)."%'";
        }

        $num = I('num');
		if(isset($num)&&!empty($num)){
			$flag = 1;
        }

        $parent = I('param.');
        $this->post = $parent;
        $w = "";
        if(!empty($comd)){
            $w = ' WHERE '.@implode(' AND ',$comd);
        }
        $model = M('');

        if($flag == 0){
        	//数据
	        $sql = "SELECT l.c_ucode,u.c_nickname,u.c_headimg,count(DISTINCT(l.c_pid))as num FROM t_activity_log as l
				LEFT JOIN  t_users as u ON u.c_ucode=l.c_ucode
				$w GROUP BY l.c_ucode ";
	        $rt = $model->query($sql);
			$limit = 25;
			$Page = getpage(count($rt),$limit);
			$limits = $Page->firstRow.','.$Page->listRows;

			$sql = "SELECT l.c_ucode,u.c_nickname,u.c_headimg,count(DISTINCT(l.c_pid))as num FROM t_activity_log as l
				LEFT JOIN  t_users as u ON u.c_ucode=l.c_ucode
				$w GROUP BY l.c_ucode $orderby LIMIT ".$limits;
	        $list = $model->query($sql);
	        $this->page = $Page->show();// 分页显示输出
	        $this->count = count($rt);
        }else{
        	$sql = "SELECT l.c_ucode,u.c_nickname,u.c_headimg,count(DISTINCT(l.c_pid))as num FROM t_activity_log as l
				LEFT JOIN  t_users as u ON u.c_ucode=l.c_ucode
				$w GROUP BY l.c_ucode $orderby ";
	        $date = $model->query($sql);

	        for($i = 0;$i <sizeof($date);$i++){
				if($date[$i]["num"] == $num){
					$list[] = $date[$i];
				}
       		}
       		$this->count = count($list);
       	}

        $this->list = $list;
		$this->root_url = GetHost().'/';
        $this->pcode = $pcode;

		$this->display();
    }

    //商家返利红包

    //商家返利红包 奖品列表
    public function red_packet(){
    	$Red = M('activity_prize as p');

		$where['p.c_pid'] = 0;
		$where['p.c_aid'] = $this->get_aid(6);

		$pname = trim(I('pname'));
        if (!empty($pname)) {
           $where['p.c_name'] = array('like', "%{$pname}%");
        }

        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $where['u.c_nickname'] = array('like', "%{$nickname}%");
        }

		$field = 'p.c_id,p.c_pid,p.c_aid,p.c_name,p.c_pcode,p.c_imgpath,p.c_value,p.c_totalnum,p.c_num,p.c_state,p.c_acode,p.c_addtime,pro.c_name as pname,u.c_nickname';
		$join = 'left join t_product as pro on pro.c_pcode=p.c_pcode';
		$join1 = 'left join t_users as u on u.c_ucode=p.c_acode';

		$count = $Red->join($join)->join($join1)->where($where)->count();
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;

		$data = $Red->field($field)->join($join)->join($join1)->where($where)->limit($limit)->order('p.c_id desc')->select();
		foreach ($data as $key => $value) {
			$data[$key]['c_activityname'] = M('Activity')->where('c_id='.$value['c_aid'])->getField('c_activityname');
		}

		$this->list = $data;
		$this->root_url = GetHost()."/";
		$this->page = $page->show();
		$this->assign('count',$count);
		$this->display();
    }

	//活动奖品验证数据
	function pcheckfrom1($data){
		if (empty($data['c_name'])) {
	    	$this->error("奖品名称不能为空");
		}

		if ($data['value'] == '') {
	    	$this->error("奖品价值不能为空");
		}

		if (empty($data['totalnum'])) {
	    	$this->error("总数量不能为空");
		}

		if ($data['num'] == '') {
	    	$this->error("剩余数量不能为空");
		}

		if (empty($data['ucode'])) {
    		$this->error("必须选择购买人");
		}

		if (empty($data['pcode'])) {
    		$this->error("必须选择商品");
		}

		if (empty($data['state'])) {
		    $this->error("奖品状态未选择");
		}
	}

	//活动奖品添加
	public function redprize_add(){
		$this->action = 'redprize_add';

		if(IS_POST){
			$db = M('activity_prize');

		   	$this->pcheckfrom1($_POST);
		    if(empty($_FILES['imgpath']['name'])){
	    		$this->error("展示图片必须上传");
	    	}
		    $fileresult = uploadimg('activity');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$pfalg = $_POST['pfalg'];

			$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    $data['c_aid'] = $this->get_aid(6);
		    $data['c_acode'] = $_POST['ucode'];
		    $data['c_pcode'] = $_POST['pcode'];
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = 1;
		    $data['c_state'] = $_POST['state'];

		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		        // 写入消息中心
		  //       $w['c_pcode'] = $_POST['pcode'];
				// $dzucode = M('product')->where($w)->getField('c_ucode');
				// $w1['c_ucode'] = $dzucode;
				// $shopname = M('users')->where($w1)->getField('c_nickname');
		  //       $content = '在小蜜，发现【'.$shopname.'】送出的返利红包';
		  //       $weburl = GetHost(1).'/index.php/Home/Fullmoon/redinfo?aid='.$this->get_aid(6).'&pcode='.$_POST['pcode'].'&ucode='.$_POST['ucode'];
		  //       $Msgcentre = IGD('Msgcentre', 'Message');
		  //       $msgdata['type'] = 0;
		  //       $msgdata['platform'] = 1;
		  //       $msgdata['sendnum'] = 1;
		  //       $msgdata['title'] = '系统消息';
		  //       $msgdata['content'] = $content;
		  //       $msgdata['tag'] = 1;
		  //       $msgdata['weburl'] = $weburl;
		  //       $msgdata['tagvalue'] = $weburl;
		  //       $Msgcentre->CreateMessege($msgdata);

		      	$back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Productchip/red_packet';
	          	echo '<script language="javascript">alert("添加成功");</script>';
	         	 echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//活动奖品编辑
	public function redprize_edit(){
		$this->action = 'redprize_edit';
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('activity_prize')->where($w)->find();

		$arr['c_ucode'] = $arr['c_acode'];
		$w1['c_ucode'] = $arr['c_acode'];
		$arr['c_nickname'] = M('users')->where($w1)->getField('c_nickname');

		$w2['c_pcode'] = $arr['c_pcode'];
		$arr['c_pname'] = M('product')->where($w2)->getField('c_name');

		$this->vo = $arr;
		if(IS_POST){
			$db = M('activity_prize');
		    $this->pcheckfrom1($_POST);
		    if(!empty($_FILES['imgpath']['name'])){
		    	$fileresult = uploadimg('activity');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    }

		    $pfalg = $_POST['pfalg'];

		    $data['c_pcode'] = $_POST['pcode'];
		    $data['c_acode'] = $_POST['ucode'];
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = 1;
		    $data['c_state'] = $_POST['state'];

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Productchip/red_packet';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('redprize_add');
	}
}