<?php
namespace Home\Controller;
use Think\Controller;

class Activityv2Controller extends BaseController{
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
		$db = M('Actjoin_moneylog as a');
		//条件
		$acode = trim(I('ucode'));
        if (!empty($acode)) {
           $w['u.c_acode'] = $acode;
        }
		$nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
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
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg';       
        $panrn['join'] = 'left join t_users as u on a.c_acode=u.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);

		foreach ($date['list'] as $key => $value) {
			foreach ($activitys as $k => $v) {
				if($value['c_activitytype'] == $activitys[$k]['id']){
					$value['c_activitytype1'] = $activitys[$k]['name'];
				}
			}
			$value['action'] = D('Activity','Behind')->GetActionv2($value['c_id'],$value['c_activitytype']);
			$data_list[$key] = $value;
		}
		
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

	    $activity_info = M('Actjoin_moneylog')->where($where)->find();
	    if(!($activity_info['c_activitystarttime']) || !($activity_info['c_activityendtime'])){
	    	die("活动时间不能为空！");
	    }
	    if($activity_info['c_sign'] == 2 && !($activity_info['c_acode'])){
	    	die("商家活动没有参与商家");
	    }

	    $data['c_state'] = $state;
	    $result = M('Actjoin_moneylog')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//ajax 修改活动是否在首页展示
	public function activity_show(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_show'] = $state;
	    $result = M('Actjoin_moneylog')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//获取平台活动模板列表
	function platformact_list(){
		$activitywhere['c_state'] = 1;
		// $activitywhere['c_activitystarttime'] = array('ELT', gdtime());
		// $activitywhere['c_activityendtime'] = array('EGT', gdtime());

		$activitydata = M('Activity')->field('c_id,c_activityname')->where($activitywhere)->order('c_id desc')->select();
   
		return $activitydata;
	}

	//验证表单
	public function checkfrom($data){
		
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
	}

	//活动添加
	public function activity_add(){
		$this->action = 'activity_add';
		if(IS_POST){
			$db = M('Actjoin_moneylog');
		    if (empty($_POST['aid'])) {
				$this->error("平台活动模板未选择");
			}
		    if (empty($_POST['sign'])) {
				$this->error("活动类型未选择");
			}
			if($_POST['sign'] == 2){
				if(empty($_POST['acode'])){
					$this->error("商家活动选择参与商家");
				}
			}

		    $aid = $_POST['aid'];

		    $w['c_id'] = $aid;
			$actinfo = M('Activity')->where($w)->find();

			if(!$actinfo){
				$this->error("没有查询到相关平台活动模板信息");
			}

			$data['c_activityname'] = $actinfo['c_activityname'];
			$data['c_remark'] = $actinfo['c_remark'];
			$data['c_aid'] = $_POST['aid'];
			$data['c_sign'] = $_POST['sign'];
			$data['c_show'] = 2;
			$data['c_listimg'] = $actinfo['c_listimg'];
			$data['c_pimg'] = $actinfo['c_pimg'];
			$data['c_img'] = $actinfo['c_img'];
			$data['c_istop'] = $actinfo['c_istop'];
			$data['c_ishot'] = $actinfo['c_ishot'];
			$data['c_activitytype'] = $actinfo['c_activitytype'];

			if($_POST['c_sign'] == 1){
				$data['c_activitystarttime'] = $actinfo['c_activitystarttime'];
				$data['c_activityendtime'] = $actinfo['c_activityendtime'];
				$data['c_state'] = $actinfo['c_state'];
			}else{
				$data['c_acode'] = $_POST['ucode'];
				if(!empty($_POST['address'])){
					$data['c_address'] = $_POST['address'];
					$data['c_latitude'] = $_POST['latitude'];
					$data['c_longitude'] = $_POST['longitude'];
				}				
				$data['c_state'] = 2;
			}			

		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/activity_list';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->pactivity = $this->platformact_list();
		$this->display();
	}

	//活动编辑
	public function activity_edit(){
		$Id = I('Id');
		$this->action = 'activity_edit';
		$db = M('Actjoin_moneylog');

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

		   if(!empty($_POST['address'])){
				$data['c_address'] = $_POST['address'];
				$data['c_latitude'] = $_POST['latitude'];
				$data['c_longitude'] = $_POST['longitude'];
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
		    $data['c_addtime'] = Date('Y-m-d H:i:s');
		   
		    $where['c_id'] = $_POST['Id'];
		    $result = $db->where($where)->save($data);

	    	if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/activity_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->pactivity = $this->platformact_list();
		$this->activity = $this->get_activity();
		$this->display();
	}

	//活动删除
    public function activity_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Actjoin_moneylog')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

	//转盘类活动奖品列表
	public function roulette_prize(){
		$db = M('A_actprize as p');
		//条件
    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['p.c_name'] = array('like', "%{$name}%");
        }
        $joinaid = trim(I('joinaid'));
        if (!empty($joinaid)) {
           $w['p.c_joinaid'] = $joinaid;
           $this->joinaid = $joinaid;
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['p.c_type'] = $prizetype;
        }
        $status = trim(I('status'));
        if (!empty($status)) {
           $w['p.c_status'] = $status;
        }

        $w['p.c_delete'] = 2;

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname,a.c_activitytype,u.c_nickname';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on a.c_id=p.c_joinaid';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=p.c_acode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//ajax 修改活动奖品是否参加活动状态
	public function prize_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_status'] = $state;

	    $result = M('A_actprize')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//活动奖品验证数据
	function pcheckfrom($data){
		if (empty($data['name'])) {
	    	$this->error("奖品名称不能为空");
		}

		if ($data['value'] == '') {
	    	$this->error("奖品价值不能为空");
		}
		if ($data['totalnum'] == '') {
	    	$this->error("总数量不能为空");
		}

		if ($data['num'] == '') {
	    	$this->error("剩余数量不能为空");
		}

		if($data['type'] == 4){
			if (empty($data['ucode'])) {
	    		$this->error("实物奖品必须选择商家");
			}
		}
		if (empty($data['status'])) {
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
	public function roulette_prize_add(){
		$this->action = 'roulette_prize_add';
		$joinaid = I('joinaid');

		$this->joinaid = $joinaid;
		if(IS_POST){
			$db = M('A_actprize');

		   	$this->pcheckfrom($_POST);
		    if(empty($_FILES['img']['name'])){
	    		$this->error("展示图片必须上传");
	    	}
		    $fileresult = uploadimg('activity');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$data['c_img'] = $fileresult['data']['img'];
		    $data['c_joinaid'] = $_POST['joinaid'];
		    $data['c_acode'] = $_POST['ucode'];
		    $data['c_pid'] = $this->CreateUcode("ap");
		    $data['c_name'] = $_POST['name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_today_prize'] = $_POST['today_prize'];
		    $data['c_marks'] = $_POST['marks'];
		    
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/roulette_prize?joinaid='.$_POST['joinaid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//活动奖品编辑
	public function roulette_prize_edit(){
		$this->action = 'roulette_prize_edit';
		
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('A_actprize')->where($w)->find();
		$arr['c_ucode'] = $arr['c_acode'];

		$w1['c_ucode'] = $arr['c_acode'];
		$arr['c_nickname'] = M('users')->where($w1)->getField('c_nickname');

		$this->joinaid = $arr['c_joinaid'];
		$this->vo = $arr;
		if(IS_POST){
			$db = M('A_actprize');
		    $this->pcheckfrom($_POST);

		    if(!empty($_FILES['img']['name'])){
		    	$fileresult = uploadimg('activity');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img'] = $fileresult['data']['img'];
		    }

		    $data['c_acode'] = $_POST['ucode'];
		    $data['c_name'] = $_POST['name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_today_prize'] = $_POST['today_prize'];
		    $data['c_marks'] = $_POST['marks'];
		    $data['c_updatetime'] = Date('Y-m-d H:i:s');

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/roulette_prize?joinaid='.$_POST['joinaid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('roulette_prize_add');
	}

	//活动奖品删除
    public function prize_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);

        $save_data['c_delete'] = 1;
        $result = M('A_actprize')->where($where)->save($save_data);
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

     //中奖记录
    public function activity_log(){
		$db = M('A_actlog as l');
		//条件
		$joinaid = trim(I('joinaid'));
        if (!empty($joinaid)) {
           $w['l.c_joinaid'] = $joinaid;
        }

    	// $activityname = trim(I('activityname'));
     //    if (!empty($activityname)) {
     //       $w['a.c_activityname'] = array('like', "%{$activityname}%");
     //    }
        $pid = trim(I('pid'));
        if (!empty($pid)) {
           $w['l.c_pid'] = $pid;
        }
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
           $w['l.c_orderid'] = $orderid;
        }
        $c_name = trim(I('name'));
        if (!empty($c_name)) {
           $w['l.c_name'] = array('like', "%{$name}%");
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['l.c_type'] = $prizetype;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

     	$w['l.c_type'] = array('neq', 1);

		$panrn['where'] = $w;
		$parent = I('param.');		
		$panrn['order'] = 'l.c_id desc';//排序		

		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,a.c_activityname,a.c_activitytype,u.c_nickname as username';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on a.c_id=l.c_joinaid';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		
		$this->display();		
    }

    //商家福利活动奖品列表
	public function welfaree_prize(){
		$db = M('A_actprize as p');
		//条件
    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['p.c_name'] = array('like', "%{$name}%");
        }
        $joinaid = trim(I('joinaid'));
        if (!empty($joinaid)) {
           $w['p.c_joinaid'] = $joinaid;
           $this->joinaid = $joinaid;
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['p.c_type'] = $prizetype;
        }
        $status = trim(I('status'));
        if (!empty($status)) {
           $w['p.c_status'] = $status;
        }

        $w['p.c_delete'] = 2;

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname,a.c_activitytype,u.c_nickname';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on a.c_id=p.c_joinaid';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=p.c_acode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//检查数据格式
	public function pcheckfrom1($data){
		if (empty($data['name'])) {
	    	$this->error("奖品名称不能为空");
		}
		
		if ($data['totalnum'] == '') {
	    	$this->error("总数量不能为空");
		}

		if ($data['num'] == '') {
	    	$this->error("剩余数量不能为空");
		}

		if (empty($data['status'])) {
		    $this->error("奖品状态未选择");
		}
		
		$type = $data['type'];
		if($type == 3){
			if ($data['maxvalue'] == '') {
	    		$this->error("限制使用金额不能为空");
			}
		}elseif($type == 5){
			
		}else{
			if ($data['value'] == '') {
    			$this->error("奖品价值不能为空");
			}
		}
	}

	//商家福利奖品添加
	public function welfaree_prize_add(){
		$this->action = 'welfaree_prize_add';
		$joinaid = I('joinaid');

		$this->joinaid = $joinaid;
		if(IS_POST){
			$db = M('A_actprize');

			$this->pcheckfrom1($_POST);		  

		    $data['c_joinaid'] = $_POST['joinaid'];
		    $data['c_name'] = $_POST['name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_maxvalue'] = $_POST['maxvalue'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_remark'] = "福利";
		    
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/welfaree_prize?joinaid='.$_POST['joinaid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//商家福利奖品编辑
	public function welfaree_prize_edit(){
		$this->action = 'welfaree_prize_edit';
		
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('A_actprize')->where($w)->find();
		$arr['c_ucode'] = $arr['c_acode'];

		$w1['c_ucode'] = $arr['c_acode'];
		$arr['c_nickname'] = M('users')->where($w1)->getField('c_nickname');

		$this->joinaid = $arr['c_joinaid'];
		$this->vo = $arr;
		if(IS_POST){
			$db = M('A_actprize');
		    $this->pcheckfrom1($_POST);

		    $data['c_name'] = $_POST['name'];
		    $data['c_value'] = $_POST['value'];
		    $data['c_maxvalue'] = $_POST['maxvalue'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/welfaree_prize?joinaid='.$_POST['joinaid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('welfaree_prize_add');
	}

	//宝箱热气球活动奖品列表
	public function airbox_prize(){
		$db = M('A_actprize as p');
		//条件
    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['p.c_name'] = array('like', "%{$name}%");
        }
        $joinaid = trim(I('joinaid'));
        if (!empty($joinaid)) {
           $w['p.c_joinaid'] = $joinaid;
           $this->joinaid = $joinaid;
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['p.c_type'] = $prizetype;
        }
        $status = trim(I('status'));
        if (!empty($status)) {
           $w['p.c_status'] = $status;
        }

        $w['p.c_delete'] = 2;
        $w['_string'] = 'p.c_type=3 or p.c_type=4';

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname,a.c_activitytype,u.c_nickname';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on a.c_id=p.c_joinaid';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=p.c_acode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//宝箱热气球活动红包列表
	public function airbox_red(){
		$db = M('A_redprize as r');
		//条件
        $joinaid = trim(I('joinaid'));
        if (!empty($joinaid)) {
           $w['r.c_joinaid'] = $joinaid;
           $this->joinaid = $joinaid;
        }

        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['r.c_type'] = $prizetype;
        }

        $status = trim(I('status'));
        if (!empty($status)) {
           $w['r.c_status'] = $status;
        }

        $w['r.c_delete'] = 2;

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'r.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'r.*,a.c_activityname,a.c_activitytype,u.c_nickname';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on a.c_id=r.c_joinaid';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=r.c_acode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];

		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//首页发现中奖记录
    public function start_log(){
		$db = M('A_start_log as l');
		//条件
		$joinaid = trim(I('joinaid'));
        if (!empty($joinaid)) {
           $w['l.c_joinaid'] = $joinaid;
        }

        $awid = trim(I('awid'));
        if (!empty($awid)) {
           $w['l.c_awid'] = $awid;
        }
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
           $w['l.c_orderid'] = $orderid;
        }
        $c_name = trim(I('name'));
        if (!empty($c_name)) {
           $w['l.c_name'] = array('like', "%{$name}%");
        }
        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['l.c_type'] = $prizetype;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

     	$w['l.c_type'] = array('neq', 1);

		$panrn['where'] = $w;
		$parent = I('param.');		
		$panrn['order'] = 'l.c_id desc';//排序		

		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,a.c_activityname,a.c_activitytype,u.c_nickname as username';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on a.c_id=l.c_joinaid';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		
		$this->display();		
    }

    //红包库列表
	public function red_list(){
		$db = M('A_redprize as r');
		//条件
		$joinaid = trim(I('joinaid'));
        if (!empty($joinaid)) {
           $w['r.c_joinaid'] = $joinaid;
           $this->joinaid = $joinaid;
        }

        $name = trim(I('name'));
        if (!empty($name)) {
           $w['r.c_name'] = $name;
        }

        $prizetype = trim(I('prizetype'));
        if (!empty($prizetype)) {
           $w['r.c_type'] = $prizetype;
        }

        $status = trim(I('status'));
        if (!empty($status)) {
           $w['r.c_status'] = $status;
        }

        $w['r.c_delete'] = 2;

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'r.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'r.*,a.c_activityname,a.c_activitytype';
        $panrn['join'] = 'left join t_actjoin_moneylog as a on a.c_id=r.c_joinaid';
        // $panrn['join1'] = 'left join t_users as u on u.c_ucode=r.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//检查数据格式
	public function checkred($data){
		if (empty($data['name'])) {
	    	$this->error("红包名称不能为空");
		}
		
		if ($data['totalnum'] == '') {
	    	$this->error("总数量不能为空");
		}

		if ($data['num'] == '') {
	    	$this->error("剩余数量不能为空");
		}

		if ($data['money'] == '') {
	    	$this->error("总金额不能为空");
		}

		if ($data['remain_money'] == '') {
	    	$this->error("剩余金额不能为空");
		}

		if (empty($data['status'])) {
		    $this->error("奖品状态未选择");
		}

		if($data['type'] == 1){
			if (empty($data['value'])) {
			    $this->error("普通红包必须填写普通红包金额");
			}
		}
	}

	//红包库红包添加
	public function red_add(){
		$this->action = 'red_add';
		
		$joinaid = I('joinaid');
		$this->joinaid = $joinaid;
		if(IS_POST){
			$db = M('A_redprize');

			$this->checkred($_POST);		  

			$data['c_joinaid'] = $_POST['joinaid'];
		    $data['c_name'] = $_POST['name'];
		    if($_POST['type'] == 1){
		    	$data['c_value'] = $_POST['value'];
		    }		    
		    $data['c_money'] = $_POST['money'];
		    $data['c_remain_money'] = $_POST['remain_money'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_marks'] = 2;
		    $data['c_remark'] = "平台红包";
		    $data['c_updatetime'] = Date('Y-m-d H:i:s');
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/red_list?joinaid='.$_POST['joinaid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//红包库红包编辑
	public function red_edit(){
		$this->action = 'red_edit';
		
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('A_redprize')->where($w)->find();
		
		$this->joinaid = $arr['c_joinaid'];
		$this->vo = $arr;
		if(IS_POST){
			$db = M('A_redprize');
		    $this->checkred($_POST);

		    $data['c_name'] = $_POST['name'];
		    if($_POST['type'] == 1){
		    	$data['c_value'] = $_POST['value'];
		    }		    
		    $data['c_money'] = $_POST['money'];
		    $data['c_remain_money'] = $_POST['remain_money'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_updatetime'] = Date('Y-m-d H:i:s');

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Activityv2/red_list?joinaid='.$_POST['joinaid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('red_add');
	}

	 

}