<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 商家卡券管理
 */
class CouponController extends BaseController {
	//商家卡券列表
	public function coupon_list(){
		$db = M('A_actcard as a');
		//条件
		$w['a.c_delete'] = 2;

    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['a.c_name'] = array('like', "%{$name}%");
        }
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
           $w['a.c_ucode'] = $ucode;
           $this->ucode = $ucode;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = $nickname;
        }
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
           $w['u.c_phone'] = $c_phone;
        }
        $status = trim(I('status'));
        if (!empty($status)) {
           $w['a.c_status'] = $status;
        }
        $sign = trim(I('sign'));
        if (!empty($sign)) {
           $w['a.c_sign'] = $sign;
        }
        $type = trim(I('type'));
        if (!empty($type)) {
           $w['a.c_type'] = $type;
        }
        $rule = trim(I('rule'));
        if (!empty($rule)) {
           $w['a.c_rule'] = $rule;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg,u.c_phone';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);

		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->activity = $activitys;
		$this->display();
	}

		//ajax 修改卡券领取状态
	public function coupon_status(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;
	    $data['c_status'] = $state;

	    $result = M('A_actcard')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//表单数据验证
	function pcheckfrom($data){
		if (empty($data['name'])) {
	    	$this->error("卡券名称不能为空");
		}
		if ($data['totalnum'] == '') {
	    	$this->error("卡券数量不能为空");
		}
		if ($data['num'] == '') {
	    	$this->error("卡券剩余数量不能为空");
		}
		if ($data['actnum'] == '') {
	    	$this->error("卡券活动数量不能为空");
		}
		if ($data['money'] == '') {
	    	$this->error("卡劵金额/折扣比列不能为空");
		}
		if ($data['limit_money'] == '') {
	    	$this->error("限制金额不能为空");
		}
	
    	if (empty($data['starttime'])) {
	    	$this->error("有效开始时间不能为空");
		}
		if (empty($data['endtime'])) {
	    	$this->error("有效结束时间不能为空");
		}

		if(strtotime($data['endtime']) < strtotime($data['starttime'])){
			$this->error("有效结束时间不能早于有效开始时间");
		}
	}

	//优惠券添加
	public function coupon_add(){
		$this->action = 'coupon_add';

		$ucode = I('ucode');
		$this->ucode = $ucode;

		if($ucode){
			$where['c_isdele'] = 1;
			$where['c_ucode'] = $ucode;
			$where['c_source'] = 1;

			$this->productarr = M('Product')->field('c_pcode,c_name,c_pimg,c_price')->where($where)->select();
		}

		$this->root_url = GetHost()."/";
		if(IS_POST){
			$db = M('A_actcard');
		   	$this->pcheckfrom($_POST);

			$data['c_name'] = $_POST['name'];
		    $data['c_ucode'] = $_POST['ucode'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_actnum'] = $_POST['actnum'];
		    $data['c_sign'] = $_POST['sign'];

		    $data['c_type'] = $_POST['type'];
		    $data['c_rule'] = $_POST['rule'];
		    $data['c_money'] = $_POST['money'];
		    $data['c_limit_money'] = $_POST['limit_money'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_pcodearr'] = $_POST['pcodearr'];

		    $data['c_starttime'] = $_POST['starttime'];
			$data['c_endtime'] = $_POST['endtime'];

		    $data['c_addtime'] = Date('Y-m-d H:i:s');
		    $data['c_updatetime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Coupon/coupon_list?ucode='.$_POST['ucode'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//卡券编辑
	public function coupon_edit(){
		$this->action = 'coupon_edit';	

		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('A_actcard')->where($w)->find();

		$ucode = I('ucode');
		$this->ucode = $ucode;

		if($arr['c_pcodearr']){
			$proarr_list = explode('|',$arr['c_pcodearr']);

			foreach ($proarr_list as $key => $value) {
				$proarr[$key]['c_pcode'] = $value;
			}

			$this->proarr = $proarr;

			$arr['range'] = 2;
		}else{
			$arr['range'] = 1;
		}
		
		$this->vo = $arr;

		if($ucode){
			$where['c_isdele'] = 1;
			$where['c_ucode'] = $ucode;
			$where['c_source'] = 1;

			$this->productarr = M('Product')->field('c_pcode,c_name,c_pimg,c_price')->where($where)->select();
		}
		
		if(IS_POST){
			$db = M('A_actcard');
		   	$this->pcheckfrom($_POST);

		   	if($_POST['pcode']){
		   		$pcode= implode('|',$_POST['pcode']);
		   	}
		   	
			$data['c_name'] = $_POST['name'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_actnum'] = $_POST['actnum'];
		    $data['c_sign'] = $_POST['sign'];

		    $data['c_type'] = $_POST['type'];
		    $data['c_rule'] = $_POST['rule'];
		    $data['c_money'] = $_POST['money'];
		    $data['c_limit_money'] = $_POST['limit_money'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_pcodearr'] = $pcode;

		    $data['c_starttime'] = $_POST['starttime'];
			$data['c_endtime'] = $_POST['endtime'];

		    $data['c_updatetime'] = Date('Y-m-d H:i:s');

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);

		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Coupon/coupon_list?ucode='.$_POST['ucode'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('coupon_add');
	}

	//卡券领取记录
    public function user_coupons()
    {
		$db = M('A_user_coupons as a');
		//条件
		$Id = trim(I('Id'));
		if (!empty($Id)) {
			$w['a.c_cid'] = $Id;
		}

		$ucode = trim(I('ucode'));
		if (!empty($ucode)) {
			$w['a.c_ucode'] = $ucode;
		}
		$nickname = trim(I('nickname'));
		if (!empty($nickname)) {
			$w['u.c_nickname'] = array('like', "%{$nickname}%");
		}
		$phone = trim(I('phone'));
		if (!empty($phone)) {
			$w['u.c_phone'] = $phone;
		}

		$c_name = trim(I('c_name'));
		if (!empty($c_name)) {
			$w['a.c_name'] = array('like', "%{$c_name}%");
		}
		$used_state = trim(I('used_state'));
		if (!empty($used_state)) {
			if($used_state == 10){
				$used_state = 0;
			}else{
				$used_state = $used_state;
			}
		}		

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_addtime asc';//排序
		$panrn['limit'] = 25;//分页数

		//分页显示数据
		$panrn['field'] = 'u.c_nickname,u.c_headimg,u.c_phone,a.*';
		$panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
		$list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
		$this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
    }

}