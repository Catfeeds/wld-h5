<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 金鸡报喜，迎新纳福 福字库管理
 */
class NewyearController extends BaseController{
	//福字列表
	public function fu_list(){
	    $db = M('Collect_prize as p');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname';
        $panrn['join'] = 'left join t_activity as a on a.c_id=p.c_aid';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->display();
	}

	//ajax 修改活动奖品是否参加活动状态
	public function prize_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_state'] = $state;

	    $result = M('Collect_prize')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//活动奖品验证数据
	function pcheckfrom($data){
		if (empty($data['aid'])) {
		    $this->error("活动名称未选择");
		}
		$aid = $data['aid'];

		$type = $data['type'];
		if($type == 2){
			if (empty($data['totalnum'])) {
				$this->error("总数量不能为空");
			}

			if ($data['num'] == '') {
		    	$this->error("剩余数量不能为空");
			}

			if (empty($data['starttime'])) {
		    	$this->error("发放开始时间不能为空");
			}

			if (empty($data['endtime'])) {
		    	$this->error("发放结束时间不能为空");
			}

			if(strtotime($starttime) > strtotime($endtime)){
				$this->error("发放开始时间不能晚于发放结束时间");
			}
		}
	}

	//活动奖品添加
	public function fu_add(){
		$this->action = 'fu_add';
		$this->activitys = M('activity')->field('c_id,c_activityname')->select();

		if(IS_POST){
			$db = M('Collect_prize');

		   	$this->pcheckfrom($_POST);

		    if(empty($_FILES['imgpath']['name'])){
	    		$this->error("弹框图片必须上传");
	    	}

	    	if($_POST['type'] == 2){
			    if(empty($_FILES['pic1']['name'])){
		    		$this->error("背包亮图必须上传");
		    	}
		    	if(empty($_FILES['pic2']['name'])){
		    		$this->error("背包暗图必须上传");
		    	}
	    	}
		    $fileresult = uploadimg('collect');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$data['c_imgpath'] = $fileresult['data']['imgpath'];
			$data['c_pic1'] = $fileresult['data']['pic1'];
			$data['c_pic2'] = $fileresult['data']['pic2'];

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_name'] = $_POST['name'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Newyear/fu_list';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//活动奖品编辑
	public function fu_edit(){
		$this->action = 'fu_edit';
		$this->activitys = M('activity')->field('c_id,c_activityname')->select();
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('Collect_prize')->where($w)->find();
		$this->vo = $arr;
		if(IS_POST){
			$db = M('Collect_prize');

		    $this->pcheckfrom($_POST);

		    if(!empty($_FILES['imgpath']['name'])){
		    	$fileresult = uploadimg('collect');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    }
		    if(!empty($_FILES['pic1']['name'])){
		    	$fileresult = uploadimg('collect');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_pic1'] = $fileresult['data']['pic1'];
		    }
		    if(!empty($_FILES['pic2']['name'])){
		    	$fileresult = uploadimg('collect');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_pic2'] = $fileresult['data']['pic2'];
		    }


		    $data['c_aid'] = $_POST['aid'];
		    $data['c_name'] = $_POST['name'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];
		    $data['c_state'] = $_POST['state'];

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Newyear/fu_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('fu_add');
	}

	//活动奖品删除
    public function fu_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Collect_prize')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

	//领取记录列表
	public function get_log(){
		$db = M('Collect_log as l');
		//条件
		$falg = 0;
    	$cpid = trim(I('cpid'));
        if (!empty($cpid)) {
           $w['l.c_cpid'] = $cpid;
           $falg = 1;
        }

        $this->falg = $falg;

        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

        $phone = trim(I('phone'));
        if (!empty($phone)) {
           $w['u.c_phone'] = $phone;
        }

        $cpid1 = trim(I('cpid1'));
        if (!empty($cpid1)) {
           $w['l.c_cpid'] = $cpid1;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'l.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,u.c_nickname,u.c_headimg,p.c_name';
        $panrn['join'] = 'left join t_users as u on l.c_ucode=u.c_ucode';
        $panrn['join1'] = 'left join t_collect_prize as p on l.c_cpid=p.c_id';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);

		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->prizes = M('Collect_prize')->field('c_id,c_name')->select();
		$this->display();
	}

	//新年福兑换规则列表
	public function exchange(){
	    $db = M('Collect_dh as d');
		$panrn['order'] = 'd.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'd.*,a.c_activityname,p.c_name,p.c_value';
        $panrn['join'] = 'left join t_activity as a on a.c_id=d.c_aid';
        $panrn['join1'] = 'left join t_activity_prize as p on p.c_id=d.c_pid';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);

		$rules = $date['list'];
		foreach ($rules as $key => $value) {
			$data = objarray_to_array(json_decode($value['c_exchange_rule']));
			$arr = array();
			foreach (array_values($data) as $key1 => $value1) {
				$where['c_id'] = array_keys($data)[$key1];
				$pinfo = M('Collect_prize')->field('c_name')->where($where)->find();
				$arr[$key1]['pname'] = $pinfo['c_name'];
				$arr[$key1]['num'] = array_values($data)[$key1];
			}
			$rules[$key]['rules'] = $arr;
		}
		$this->list = $rules;
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->display();
	}

	//规则验证数据
	function pcheckfrom1($data){
		if (empty($data['aid'])) {
		    $this->error("对应活动名称未选择");
		}

		if (empty($data['pid'])) {
		    $this->error("兑换奖项未选择");
		}

		if (empty($data['starttime'])) {
	    	$this->error("兑换开始时间不能为空");
		}

		if (empty($data['endtime'])) {
	    	$this->error("兑换结束时间不能为空");
		}

		if(strtotime($starttime) > strtotime($endtime)){
			$this->error("兑换开始时间不能晚于兑换结束时间");
		}

		if (empty($data['cpid'])) {
			$this->error("福字名称未选择");
		}else{
			foreach ($data['cpid'] as $key => $value) {
				if($value == 0 || $value == ''){
					$this->error("有福字名称未选择");
				}
			}
			$arr_num = array_count_values($data['cpid']);
			foreach ($arr_num as $key => $value) {
				if($value > 1){
					$this->error("选择了同样的福字");
				}
			}
		}

		if (empty($data['num'])) {
	    	$this->error("数量不能为空");
		}else{
			foreach ($data['num'] as $key => $value) {
				if($value == 0 || $value == ''){
					$this->error("有福字数量为空");
				}
			}
		}
	}

	//添加兑换规则
	public function exchange_add(){
		$this->action = 'exchange_add';
		$this->activitys = M('Activity')->field('c_id,c_activityname')->select();

		$aw['c_state'] = 1;
		$aw['c_activitytype'] = 18;
		// $aw['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
  //       $aw['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
		$aid = M('Activity')->where($aw)->order('c_id desc')->getField('c_id');

		$pw['c_aid'] = $aid;
		$this->prizes = M('Activity_prize')->where($pw)->field('c_id,c_name')->select();

		$cw['c_aid'] = $aid;
		$cw['c_state'] = 1;
		$cw['c_type'] = 2;
		$this->collects = M('Collect_prize')->where($cw)->field('c_id,c_name')->select();

		if(IS_POST){
			$db = M('Collect_dh');

		   	$this->pcheckfrom1($_POST);

		   	$all = count($_POST['cpid']);
		   	$i = 0;
		   	while($all > 0){
		   		$k = $_POST['cpid'][$i];
		   		$v = $_POST['num'][$i];
		   		$arr[$k] = $v;
		   		$i++;
		   		$all--;
		   	}
		   	$rules = json_encode($arr);

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_exchange_rule'] = $rules;
		    $data['c_pid'] = $_POST['pid'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Newyear/exchange';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//编辑兑换规则
	public function exchange_edit(){
		$this->action = 'exchange_edit';
		$this->activitys = M('Activity')->field('c_id,c_activityname')->select();

		$aw['c_activitytype'] = 18;
		$aid = M('Activity')->where($aw)->getField('c_id');

		$pw['c_aid'] = $aid;
		$this->prizes = M('Activity_prize')->where($pw)->field('c_id,c_name')->select();

		$cw['c_aid'] = $aid;
		$cw['c_state'] = 1;
		$cw['c_type'] = 2;
		$this->collects = M('Collect_prize')->where($cw)->field('c_id,c_name')->select();

		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('Collect_dh')->where($w)->find();

		$data = objarray_to_array(json_decode($arr['c_exchange_rule']));
		$cpid = array();
		foreach (array_values($data) as $key1 => $value1) {
			$cpid[$key1]['cpid'] = array_keys($data)[$key1];
			$cpid[$key1]['num'] = array_values($data)[$key1];
		}
		$arr['rules'] = $cpid;
		$this->vo = $arr;

		if(IS_POST){
			$db = M('Collect_dh');

		   	$this->pcheckfrom1($_POST);

		   	$all = count($_POST['cpid']);
		   	$i = 0;
		   	while($all > 0){
		   		$k = $_POST['cpid'][$i];
		   		$v = $_POST['num'][$i];
		   		$arrs[$k] = $v;
		   		$i++;
		   		$all--;
		   	}
		   	$rules = json_encode($arrs);

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_exchange_rule'] = $rules;
		    $data['c_pid'] = $_POST['pid'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Newyear/exchange';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display('exchange_add');
	}

	//活动奖品删除
    public function exchange_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Collect_dh')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

}