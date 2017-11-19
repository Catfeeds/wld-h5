<?php
namespace Home\Controller;
use Think\Controller;

class ActivityController extends BaseController{
	//获取活动类型与活动名称对应
	function get_activity(){
        $activitys = C('ActivityName');
		$ackey = array_keys($activitys);
		$acvaule = array_values($activitys);

		for($i = 0;$i < count($activitys);$i++){
			$activity[$i]['id'] = $ackey[$i];
			$activity[$i]['name'] = $acvaule[$i];
		}

		return $activity;
	}

	//活动列表
	public function activity_list(){
		$activitys = $this->get_activity();
		$db = M('activity as a');
		//条件
    	$activityname = trim(I('activityname'));
        if (!empty($activityname)) {
           $w['a.c_activityname'] = array('like', "%{$activityname}%");
        }
        $activitytype = trim(I('activitytype'));
        if (!empty($activitytype)) {
           $w['a.c_activitytype'] = $activitytype;
        }
        $state = trim(I('state'));
        if (!empty($state)) {
           $w['a.c_state'] = $state;
        }
        $sign = trim(I('sign'));
        if (!empty($sign)) {
           $w['a.c_sign'] = $sign;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,ad.c_username';
        $panrn['join'] = 'left join t_admin_users as ad on a.c_lastupdateuserid=ad.c_id';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);

		foreach ($date['list'] as $key => $value) {
			foreach ($activitys as $k => $v) {
				if($value['c_activitytype'] == $activitys[$k]['id']){
					$value['c_activitytype1'] = $activitys[$k]['name'];
				}
			}

			$value['action'] = D('Activity','Behind')->GetAction($value['c_id'],$value['c_activitytype']);
			$data_list[$key] = $value;
		}
		// var_dump($data_list);die;
		$this->list = $data_list;
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->activity = $activitys;
		$this->display();
	}

	//ajax 修改活动状态
	public function activity_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_state'] = $state;
	    $data['c_lastupdateuserid'] = $_SESSION['zongwld']['c_id'];
	    $data['c_lastupdatetime'] = Date('Y-m-d H:i:s');
	    $result = M('activity')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//ajax 修改活动是否在活动中心展示
	public function activity_show(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_show'] = $state;
	    $data['c_lastupdateuserid'] = $_SESSION['zongwld']['c_id'];
	    $data['c_lastupdatetime'] = Date('Y-m-d H:i:s');
	    $result = M('activity')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	public function checkfrom($data){
		if (empty($data['activityname'])) {
			$this->error("活动名称不能为空");
		}
		if (empty($data['remark'])) {
			$this->error("备注不能为空");
		}

		$starttime = $data['activitystarttime'];
		if (empty($starttime)) {
			$this->error("活动开始时间不能为空");
		}
		$endtime = $data['activityendtime'];
		if (empty($data['activityendtime'])) {
			$this->error("活动结束时间不能为空");
		}

		if(strtotime($starttime) > strtotime($endtime)){
			$this->error("活动开始时间不能晚于活动结束时间");
		}

		if (empty($data['activitytype'])) {
			$this->error("活动类型未选择");
		}
		if (empty($data['state'])) {
			$this->error("活动状态未选择");
		}
		if (empty($data['show'])) {
			$this->error("活动中心是否展示");
		}
	}

	//活动添加
	public function activity_add(){
		$this->action = 'activity_add';
		$joinaid = I('joinaid');
		$this->joinaid = $joinaid;
		if(IS_POST){
			$db = M('Activity');
		    $this->checkfrom($_POST);
		    $activitytype = $_POST['activitytype'];

	    	if(empty($_FILES['listimg']['name'])){
    			$this->error("活动列表展示图必须上传");
    		}
    		if(empty($_FILES['pimg']['name'])){
    			$this->error("首页展示图必须上传");
    		}
		    if($activitytype == 2 || $activitytype == 4 || $activitytype == 8 || $activitytype == 25){
		    	if(empty($_FILES['img']['name'])){
	    			$this->error("活动底图必须上传");
	    		}
		    }
		    $fileresult = uploadimg('activity');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

		    $data['c_activityname'] = $_POST['activityname'];
		    $data['c_remark'] = $_POST['remark'];
		    $data['c_activitystarttime'] = $_POST['activitystarttime'];
		    $data['c_activityendtime'] = $_POST['activityendtime'];
		    $data['c_activitytype'] = $activitytype;
		    $data['c_istop'] = $_POST['istop'];
		    $data['c_ishot'] = $_POST['ishot'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_sign'] = $_POST['sign'];
		    $data['c_show'] = $_POST['show'];
		    $data['c_listimg'] = $fileresult['data']['listimg'];
		    $data['c_pimg'] = $fileresult['data']['pimg'];
		   	$data['c_img'] = $fileresult['data']['img'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');
		    $data['c_lastupdateuserid'] = $_SESSION['zongwld']['c_id'];
		    $data['c_lastupdatetime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_list';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->activity = $this->get_activity();
		$this->display();
	}

	//活动编辑
	public function activity_edit(){
		$Id = I('Id');
		$this->action = 'activity_edit';
		$db = M('activity');

		$where['c_id'] = $Id;
		$this->vo = $db->where($where)->find();
		if(IS_POST){
		    $this->checkfrom($_POST);

		    $fileresult = uploadimg('activity');
		    if(!empty($_FILES['listimg']['name'])){
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_listimg'] = $fileresult['data']['listimg'];
		    }

		    if(!empty($_FILES['pimg']['name'])){
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_pimg'] = $fileresult['data']['pimg'];
		    }

		    if(!empty($_FILES['img']['name'])){
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img'] = $fileresult['data']['img'];
		    }

		    $data['c_activityname'] = $_POST['activityname'];
		    $data['c_remark'] = $_POST['remark'];
		    $data['c_activitystarttime'] = $_POST['activitystarttime'];
		    $data['c_activityendtime'] = $_POST['activityendtime'];
		    $data['c_activitytype'] = $_POST['activitytype'];
		    $data['c_istop'] = $_POST['istop'];
		    $data['c_ishot'] = $_POST['ishot'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_sign'] = $_POST['sign'];
		    $data['c_show'] = $_POST['show'];
		    $data['c_lastupdateuserid'] = $_SESSION['zongwld']['c_id'];
		    $data['c_lastupdatetime'] = Date('Y-m-d H:i:s');

		    $where['c_id'] = $_POST['Id'];
		    $result = $db->where($where)->save($data);

	    	if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->activity = $this->get_activity();
		$this->display('activity_add');
	}

	//活动删除
    public function activity_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('activity')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //活动奖金池
	public function activity_money(){
		$db = M('activity_money');

    	$aid = I('aid');
        $w['c_id'] = $aid;
        $activity_info = M('activity')->where($w)->field('c_activityname,c_activitytype')->find();

      	$this->activityname = $activity_info['c_activityname'];
      	$this->activitytype = $activity_info['c_activitytype'];

        $w1['c_aid'] = $aid;

      	$data= $db->where($w1)->select();
      	if(count($data) == 0){
      		$this->count = 0;
      	}else{
      		$this->count = count($data);
      	}
      	$this->list = $data;
      	$this->root_url = GetHost()."/";
      	$this->aid = $aid;
		$this->display();
	}

	//ajax 修改活动奖金池发放状态
	public function money_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_state'] = $state;

	    $result = M('activity_money')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	public function checkfrom_money($data,$activitytype){
	    if (empty($_POST['money'])) {
	    	$this->error("奖池总额不能为空");
		}
		if ($_POST['remain'] == '') {
	    	$this->error("剩余总额不能为空");
		}

		if (empty($_POST['rule'])) {
	    	$this->error("规则类型未选择");
		}
		if (empty($_POST['state'])) {
		    $this->error("发放状态未选择");
		}

		if($activitytype == 15){
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

	//活动奖金池添加
	public function money_add(){
		$this->action = 'money_add';
		$this->aid = I('aid');

		$w['c_id'] = $this->aid;
		$activitytype = M('activity')->where($w)->getField('c_activitytype');
		$this->activitytype = $activitytype;
		if(IS_POST){
			$db = M('activity_money');

		    $aid = I('aid');
		    $w['c_aid'] = $aid;
		    $is_exist = $db->where($w)->count();
		    if($is_exist > 0){
		    	$this->error("该活动奖金池已经存在，无法再添加!");
		    }

		    $this->checkfrom_money($_POST,$activitytype);

		    if(empty($_FILES['imgpath']['name'])){
	    		$this->error("展示图片必须上传");
	    	}

	    	if (!empty($_POST['min_money'])) {
	        	$data['c_min_money'] = $_POST['min_money'];
	    	}
	    	if (!empty($_POST['max_money'])) {
	        	$data['c_max_money'] = $_POST['max_money'];
	    	}

		    $fileresult = uploadimg('activity');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}
			$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    $data['c_aid'] = $aid;
		    $data['c_money'] = $_POST['money'];
		    $data['c_remain'] = $_POST['remain'];
		    $data['c_rule'] = $_POST['rule'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_money?aid='.$aid;
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//活动奖金池编辑
	public function money_edit(){
		$this->action = 'money_edit';

		$Id = I('Id');
		$w['c_id'] = $Id;
		$this->vo = M('activity_money')->where($w)->find();
		$this->aid = I('aid');

		$w['c_id'] = $this->aid;
		$activitytype = M('activity')->where($w)->getField('c_activitytype');
		$this->activitytype = $activitytype;
		if(IS_POST){
			$db = M('activity_money');

			$this->checkfrom_money($_POST,$activitytype);

			if (!empty($_POST['min_money'])) {
		    	$data['c_min_money'] = $_POST['min_money'];
			}
			if (!empty($_POST['max_money'])) {
		    	$data['c_max_money'] = $_POST['max_money'];
			}

		    if(!empty($_FILES['imgpath']['name'])){
		    	$fileresult = uploadimg('activity');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    }

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_money'] = $_POST['money'];
		    $data['c_remain'] = $_POST['remain'];
		    $data['c_rule'] = $_POST['rule'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_money?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('money_add');
	}

	//活动奖品列表
	public function activity_prize(){
		$db = M('activity_prize as p');
		//条件
    	$activityname = trim(I('activityname'));
        if (!empty($activityname)) {
           $w['a.c_activityname'] = array('like', "%{$activityname}%");
        }
        $aid = trim(I('aid'));
        if (!empty($aid)) {
           $w['p.c_aid'] = $aid;
           $where['c_id'] = $aid;
           $activitytype = M('Activity')->where($where)->getField('c_activitytype');
           $this->pfalg = $activitytype;
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['p.c_type'] = $prizetype;
        }
        $state = trim(I('state'));
        if (!empty($state)) {
           $w['p.c_state'] = $state;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname,a.c_activitytype,u.c_nickname';
        $panrn['join'] = 'left join t_activity as a on a.c_id=p.c_aid';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=p.c_acode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$template = D('Activity','Behind')->GetTemplate($activitytype);
		if($template){
			$this->display($template);
		}else{
			$this->display();
		}
	}

	//ajax 修改活动奖品是否参加活动状态
	public function prize_state(){
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
		if (empty($data['aid'])) {
		    	$this->error("活动名称未选择");
			}
		$aid = $data['aid'];
		$w['c_id'] = $aid;
		$activitytype = M('Activity')->where($w)->getField('c_activitytype');
		if($activitytype == 2){
			if (empty($data['today_prize'])) {
	    		$this->error("是否为今日奖品必须选择");
			}
			if (empty($data['marks'])) {
	    		$this->error("奖项标志必须选择");
			}
			if($data['type'] == 1){
				if (empty($data['bargainprice'])) {
	    			$this->error("现金时必须填写最小值");
				}
			}
		}

		if($activitytype == 4){
			if ($data['marketprice'] == '') {
	    		$this->error("市场价格必须填写");
			}
		}

		if($activitytype == 5){
			if (empty($data['bargainnum'])) {
	    		$this->error("答对题个数必须填写");
			}
		}

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

		if($data['type'] == 2){
			if (empty($data['ucode'])) {
	    		$this->error("实物奖品必须选择商家");
			}
		}
		if (empty($data['state'])) {
		    $this->error("奖品状态未选择");
		}
	}

	/**
     *  生成产品编码
     *  @param
     */
    function CreateUcode($prefix) {
        //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 4);
        $uuid .= substr($str, 8, 2);
        $uuid .= substr($str, 12, 3);
        $uuid .= substr($str, 16, 2);
        $uuid .= substr($str, 20, 5);
        return $prefix . $uuid;
    }

	//活动奖品添加
	public function prize_add(){
		$this->pfalg = I('pfalg');
		$this->action = 'prize_add';
		$this->activitys = M('activity')->field('c_id,c_activityname')->select();

		if(IS_POST){
			$db = M('activity_prize');

		   	$this->pcheckfrom($_POST);
		    if(empty($_FILES['imgpath']['name'])){
	    		$this->error("展示图片必须上传");
	    	}
		    $fileresult = uploadimg('activity');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$pfalg = $_POST['pfalg'];

			$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    $data['c_aid'] = $_POST['aid'];
		    $data['c_acode'] = $_POST['ucode'];
		    $data['c_pcode'] = $this->CreateUcode("ap");
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_bargainprice'] = $_POST['bargainprice'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_today_prize'] = $_POST['today_prize'];
		    $data['c_marks'] = $_POST['marks'];
		    $data['c_marketprice'] = $_POST['marketprice'];
		    $data['c_bargainnum'] = $_POST['bargainnum'];
		    if($pfalg == 4){
		        $aconf = C('Activity');    //活动配入数据
		        $tempnum = $aconf['bargainnum'];
		        if (!$aconf) {
		            $tempnum = 15;
		        }

		        //判断该商品的每次砍价金额
		        $zhekou = bcsub($_POST['marketprice'], $_POST['value'], 2);
		        $bargainprice = bcdiv($zhekou, $tempnum, 2);
		    	$data['c_bargainnum'] = $tempnum;
		        $data['c_bargainprice'] = $bargainprice;
		    }
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_prize?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//活动奖品编辑
	public function prize_edit(){
		$this->action = 'prize_edit';
		$this->pfalg = I('pfalg');
		$this->activitys = M('activity')->field('c_id,c_activityname')->select();
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('activity_prize')->where($w)->find();
		$arr['c_ucode'] = $arr['c_acode'];
		$w1['c_ucode'] = $arr['c_acode'];
		$arr['c_nickname'] = M('users')->where($w1)->getField('c_nickname');
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

		    $pfalg = $_POST['pfalg'];

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_acode'] = $_POST['ucode'];
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_bargainprice'] = $_POST['bargainprice'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_today_prize'] = $_POST['today_prize'];
		    $data['c_marks'] = $_POST['marks'];
		    $data['c_marketprice'] = $_POST['marketprice'];
		    $data['c_bargainnum'] = $_POST['bargainnum'];
		    if($pfalg == 4){
		    	$aconf = C('Activity');    //活动配入数据
		        $tempnum = $aconf['bargainnum'];
		        if (!$aconf) {
		            $tempnum = 15;
		        }

		        //判断该商品的每次砍价金额
		        $zhekou = bcsub($_POST['marketprice'], $_POST['value'], 2);
		        $bargainprice = bcdiv($zhekou, $tempnum, 2);
		    	$data['c_bargainnum'] = $tempnum;
		        $data['c_bargainprice'] = $bargainprice;
		    }

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_prize?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('prize_add');
	}

	//活动奖品删除
    public function prize_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('activity_prize')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //中奖记录
    public function activity_log(){
		$db = M('activity_log as l');
		//条件
		$aid = trim(I('aid'));
        if (!empty($aid)) {
           $w['l.c_aid'] = $aid;
        }
        $where['c_id'] = $aid;
        $activitytype = M('Activity')->where($where)->getField('c_activitytype');

    	$activityname = trim(I('activityname'));
        if (!empty($activityname)) {
           $w['a.c_activityname'] = array('like', "%{$activityname}%");
        }
        $pid = trim(I('pid'));
        if (!empty($pid)) {
           $w['l.c_pid'] = $pid;
        }
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
           $w['l.c_orderid'] = $orderid;
        }
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
           $w['p.c_name'] = array('like', "%{$c_name}%");
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['p.c_type'] = $prizetype;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

     	$w['l.c_type'] = array('neq', 3);

		$panrn['where'] = $w;
		$parent = I('param.');
		if($activitytype == 16){
			$panrn['order'] = 'l.c_score asc';//排序
		}else{
			$panrn['order'] = 'l.c_id desc';//排序
		}

		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,a.c_activityname,a.c_activitytype,p.c_name as proname,p.c_imgpath,u.c_nickname as username';
        $panrn['join'] = 'left join t_activity as a on a.c_id=l.c_aid';
        $panrn['join1'] = 'left join t_activity_prize as p on p.c_id=l.c_pid';
        $panrn['join2'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$template = D('Activity','Behind')->GetLogTemplate($activitytype);
		if($template){
			$this->display($template);
		}else{
			$this->display();
		}
    }

    //活动红包领取记录
    public function activity_moneylog(){
		$db = M('activity_moneylog as l');
		//条件
		$aid = trim(I('aid'));
        if (!empty($aid)) {
           $w['l.c_aid'] = $aid;
        }
        $where['c_id'] = $aid;
        $activitytype = M('Activity')->where($where)->getField('c_activitytype');

    	$activityname = trim(I('activityname'));
        if (!empty($activityname)) {
           $w['a.c_activityname'] = array('like', "%{$activityname}%");
        }
        $pid = trim(I('pid'));
        if (!empty($pid)) {
           $w['l.c_pid'] = $pid;
        }
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
           $w['l.c_orderid'] = $orderid;
        }
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
           $w['p.c_name'] = array('like', "%{$c_name}%");
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['p.c_type'] = $prizetype;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

     	$w['l.c_type'] = array('neq', 3);

		$panrn['where'] = $w;
		$parent = I('param.');
		if($activitytype == 16){
			$panrn['order'] = 'l.c_score asc';//排序
		}else{
			$panrn['order'] = 'l.c_id desc';//排序
		}

		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,a.c_activityname,a.c_activitytype,p.c_name as proname,p.c_imgpath,u.c_nickname as username';
        $panrn['join'] = 'left join t_activity as a on a.c_id=l.c_aid';
        $panrn['join1'] = 'left join t_activity_prize as p on p.c_id=l.c_pid';
        $panrn['join2'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;

		$this->display('activity_log');
    }

    //砍价记录
    public function bargin_log(){
    	$db = M('activity_bargin as b');
		//条件
		$aid = trim(I('aid'));
        if (!empty($aid)) {
           $w['b.c_aid'] = $aid;
        }
    	$pname = trim(I('pname'));
        if (!empty($pname)) {
           $w['p.c_name'] = array('like', "%{$pname}%");
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['b.c_wxname'] = array('like', "%{$nickname}%");
        }
        $pid = trim(I('pid'));
        if (!empty($pid)) {
           $w['b.c_pid'] = $pid;
        }
        $barginid = trim(I('barginid'));
        if (!empty($barginid)) {
           $w['b.c_barginid'] = $barginid;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'b.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'b.*,a.c_activityname,p.c_name as proname,p.c_imgpath';
        $panrn['join'] = 'left join t_activity as a on a.c_id=b.c_aid';
        $panrn['join1'] = 'left join t_activity_prize as p on p.c_id=b.c_pid';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
    }

    //活动祝福记录
    public function wish_log(){
    	$db = M('activity_wish as w');
		//条件
		$aid = trim(I('aid'));
        if (!empty($aid)) {
           $w['w.c_aid'] = $aid;
        }

        $sendname = trim(I('sendname'));
        if (!empty($sendname)) {
           $w['w.c_sendname'] = array('like', "%{$sendname}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'w.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'w.*,a.c_activityname';
        $panrn['join'] = 'left join t_activity as a on a.c_id=w.c_aid';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
    }

    //领取红包
    public function consentData(){
    	$id = I('Id');
    	if(empty($id)){
    		$this->ajaxReturn(Message(1011,'参数错误！'));
    	}

    	$w['c_id'] = $id;
    	$loginfo = M('Activity_log')->field('c_ucode,c_value,c_type,c_openid,c_nickname,c_state')->where($w)->find();

    	if(!empty($loginfo['c_openid']) && !empty($loginfo['c_nickname'])){
    		$openid = $loginfo['c_openid'];
    		$username = $loginfo['c_nickname'];
    	}else{
    		$w1['c_ucode'] = $loginfo['c_ucode'];
    		$userinfo = M('Users_auth')->where($w1)->field('c_openid,c_name')->find();
    		$openid = $userinfo['c_openid'];
    		$username = $userinfo['c_name'];
    	}

    	if(empty($openid) || empty($username)){
    		$this->ajaxReturn(Message(1012,'获取用户信息有误！'));
    	}

    	$amount = $loginfo['c_value'];
    	$trade_no = 'httk'.time();
    	$check_name = "NO_CHECK";

    	$result = IGD('WxEnterprisepay','Weixin')->Pay($trade_no,$openid,$amount,$username,$check_name);

        if($result['code']==0){
        	$save['c_state'] = 1;
        	$result1 = M('Activity_log')->where($w)->save($save);
        }

        $this->ajaxReturn($result);
    }

    //活动奖品添加
	public function findprize_add(){
		$this->pfalg = I('pfalg');
		$this->action = 'findprize_add';
		$this->activitys = M('activity')->field('c_id,c_activityname')->select();

		if(IS_POST){
			$db = M('activity_prize');

		   	$this->pcheckfrom($_POST);
		    if(empty($_FILES['imgpath']['name'])){
	    		$this->error("展示图片必须上传");
	    	}
		    $fileresult = uploadimg('activity');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$pfalg = $_POST['pfalg'];

			$data['c_imgpath'] = $fileresult['data']['imgpath'];
		    $data['c_aid'] = $_POST['aid'];
		    $data['c_pcode'] = $this->CreateUcode("ap");
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = 1;
		    $data['c_state'] = $_POST['state'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_prize?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//活动奖品编辑
	public function findprize_edit(){
		$this->action = 'findprize_edit';
		$this->pfalg = I('pfalg');
		$this->activitys = M('activity')->field('c_id,c_activityname')->select();
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('activity_prize')->where($w)->find();

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

		    $pfalg = $_POST['pfalg'];

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_name'] = $_POST['c_name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = 1;
		    $data['c_state'] = $_POST['state'];
		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_prize?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('findprize_add');
	}

	//找你妹分配奖项
	public function allot_prize(){
		$Id = I('Id');
		$aid = I('aid');

		$prize_where['c_aid'] = $aid;
		$prize_where['c_state'] = 1;
		$this->prizes = M('Activity_prize')->where($prize_where)->field('c_id,c_name')->select();

		$w['c_id'] = $Id;
		$arr = M('Activity_log')->where($w)->find();

		$ucode = $arr['c_ucode'];
		$w1['c_ucode'] = $ucode;
		$this->nickname = M('Users')->where($w1)->getField('c_nickname');
		$this->aid = $aid;
		$this->vo = $arr;
		if(IS_POST){
			$logid = I('Id');
			$pid = I('pid');
			$aid = I('aid');

			$result = D('Activity','Behind')->allot_prize($logid,$pid);

		    if($result['code'] == 0){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activity/activity_log?aid='.$aid;
	          echo '<script language="javascript">alert("分配成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error($result['msg']);
	        }
		}
		$this->display();
	}
    


    //奖项审核
    public function moneylog(){
		$db = M('A_actprize as p');
		//条件
    	$c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
           $w['u.c_nickname'] = array('like', "%{$c_nickname}%");
        }

        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
           $w['u.c_phone'] = $c_phone;
        }

        $c_status = trim(I('c_status'));
        if (!empty($c_status)) {
           $w['p.c_status'] = array('like', "%{$c_status}%");
        }

        // $c_status = trim(I('c_status'));
        // if (!empty($c_status)) {
        //     if($c_status == 'sqz'){
        //        $w['p.c_status'] = 0;
        //     }else{
        //         $w['p.c_status'] = $c_status;
        //     }
        // }

        $w['p.c_delete'] =2;
        $w['p.c_type'] = 4;
        $w[] = array("a.c_activitytype=23 or a.c_activitytype=24");
        // $w['p.c_status'] =3;
		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname,u.c_nickname,u.c_phone,s.c_name as c_realname';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on p.c_joinaid=a.c_id';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=p.c_acode';
        $panrn['join2'] = 'left join t_check_shopinfo as s on u.c_ucode=s.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);

		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$template = D('Activity','Behind')->GetTemplate($activitytype);
		if($template){
			$this->display($template);
		}else{
			$this->display();
		}
	}
     

    //ajax 修改活动开始
	public function dealactivity(){
		$handle = I('handle');
		$Id = I('txcode');
		if(empty($Id) || empty($handle)) $this->ajaxReturn(Message(1011,'参数错误！'));		

		$db =  M('A_actprize');
		$db->startTrans();

		//查询奖项
		$rw['c_id'] = $Id;
		$redinfo = $db->where($rw)->find();
		if (!$redinfo || empty($redinfo['c_acode'])) {
			$db->rollback();
			$this->ajaxReturn(Message(1013,'记录不存在'));
		}

		//查询活动
		$aw['c_id'] = $redinfo['c_joinaid'];
		$actdata = M('Actjoin_moneylog')->where($aw)->find();
		if (!$actdata) {
			$db->rollback();
			$this->ajaxReturn(Message(1014,'活动不存在'));
		}		

		switch ($handle) {
			case '1':	//通过
				$where['c_id'] = $Id;
				$where['c_status'] = 3;
			    $data['c_status'] = 4;
		        $data['c_updatetime'] = Date('Y-m-d H:i:s');
			    $result = M('A_actprize')->where($where)->save($data);
			    if(!$result){
			    	$db->rollback();
			      	$this->ajaxReturn(Message(1012,'操作失败！'));
			    }

			    if ($actdata['c_activitytype'] == 22) {  //宝箱
			    	$weburl = GetHost(1).'/index.php/Activity/Chests/index';
			    	$content = '您提交的宝箱奖项：'.$redinfo['c_name'].'，已通过平台审核，您可以去投放啦！';
			    } else if ($actdata['c_activitytype'] == 23) {	//气球
			    	$weburl = GetHost(1).'/index.php/Activity/Balloon/index';
			    	$content = '您提交的热气球奖项：'.$redinfo['c_name'].'，已通过平台审核，您可以去投放啦！';
			    }			    

			    //通过审核发送消息
			    $Msgcentre = IGD('Msgcentre', 'Message');
			    $msgdata['ucode'] = $redinfo['c_acode'];
			    $msgdata['type'] = 0;
			    $msgdata['platform'] = 1;
			    $msgdata['sendnum'] = 1;
			    $msgdata['title'] = '系统消息';
			    $msgdata['content'] =  $content;
			    $msgdata['tag'] = 2;
			    $msgdata['tagvalue'] = $weburl;
			    $msgdata['weburl'] = $weburl;
			    $Msgcentre->CreateMessege($msgdata);

				break;
			case '2':  //不通过
				if (empty($redinfo['c_orderid'])) {   //判断是否写入不通过的理由
					$db->rollback();
			      	$this->ajaxReturn(Message(1014,'请先填写不通过的原因'));
				}

				$where['c_id'] = $Id;
				$where['c_status'] = 3;
			    $data['c_status'] = 2;
		        $data['c_updatetime'] = Date('Y-m-d H:i:s');
			    $result = M('A_actprize')->where($where)->save($data);
			    if(!$result){
			    	$db->rollback();
			       	$this->ajaxReturn(Message(1012,'操作失败！'));
			    }

			    if ($actdata['c_activitytype'] == 22) {  //宝箱
			    	$weburl = GetHost(1).'/index.php/Activity/Chests/index';
			    	$content = '您提交的宝箱奖项：'.$redinfo['c_name'].'，平台不通过，理由：'.$redinfo['c_orderid'].'。';
			    } else if ($actdata['c_activitytype'] == 23) {	//气球
			    	$weburl = GetHost(1).'/index.php/Activity/Balloon/index';
			    	$content = '您提交的热气球奖项：'.$redinfo['c_name'].'，平台不通过，理由：'.$redinfo['c_orderid'].'。';
			    }			    

			    //通过审核发送消息
			    $Msgcentre = IGD('Msgcentre', 'Message');
			    $msgdata['ucode'] = $redinfo['c_acode'];
			    $msgdata['type'] = 0;
			    $msgdata['platform'] = 1;
			    $msgdata['sendnum'] = 1;
			    $msgdata['title'] = '系统消息';
			    $msgdata['content'] =  $content;
			    $msgdata['tag'] = 2;
			    $msgdata['tagvalue'] = $weburl;
			    $msgdata['weburl'] = $weburl;
			    $Msgcentre->CreateMessege($msgdata);

				break;
			case '3':
				$idstr = str_replace('|', ',', $Id);
				$where['c_id'] = array('in',$idstr);
		        $data['c_delete'] = 1;
				$result = M('A_actprize')->where($where)->save($data);
				if(!$result){
					$db->rollback();
					$this->ajaxReturn(Message(1013,'操作失败！'));
				}
			break;
			default:
				break;
		}

		$db->commit();
		$this->ajaxReturn(Message(0,'操作成功！'));	    
	}

	public function save_thirdparty(){
		$Id = I("Id");

		if(empty($Id)) $this->ajaxReturn(Message(1011,'参数错误！'));

		$where['c_id'] = $Id;
		$data['c_orderid'] = I('thirdparty');
        $data['c_updatetime'] = Date('Y-m-d H:i:s');
	    $result = M('A_actprize')->where($where)->save($data);
	    if(!$result){
	       $this->ajaxReturn(Message(1012,'操作失败！'));
	    }
		$this->ajaxReturn(Message(0,'操作成功！'));
	}

   
}