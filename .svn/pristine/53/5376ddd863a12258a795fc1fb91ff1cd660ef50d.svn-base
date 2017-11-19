<?php

namespace Home\Controller;
use Think\Controller;

/**
*   技术问题处理相关操作
*/
class ProblemController extends BaseController {

	// 问题列表
	public function index()
	{
		$Admin_problem = M('Admin_problem');
		$count = $Admin_problem->count();
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;
		$this->data = $Admin_problem->limit($limit)->order('c_id desc')->select();
		$this->page = $page->show();
		$this->assign('count',$count);
		$this->display();
	}

	//问题添加
	public function pro_add()
	{
		$this->action = 'Problem/pro_add';
		if(IS_AJAX){
	     	$Admin_problem = M('Admin_problem');
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['c_name'])) {
	     		$this->ajaxReturn(Message(1001,'问题名字不能为空'));
	     	}
	     	if (empty($data['c_desc'])) {
	     		$this->ajaxReturn(Message(1001,'问题描述不能为空'));
	     	}
	     	if (empty($data['c_sign'])) {
	     		$this->ajaxReturn(Message(1001,'操作类型不能为空'));
	     	}
	     	if (empty($data['c_option'])) {
	     		$this->ajaxReturn(Message(1001,'操作链接方法不能为空'));
	     	}
	      	$_data['c_name'] = $data['c_name'];
	      	$_data['c_desc'] = $data['c_desc'];
	      	$_data['c_sign'] = $data['c_sign'];
	      	$_data['c_option'] = $data['c_option'];
	        $_data['c_addtime'] = date('Y-m-d H:i:s',time());
	        $_data['c_updatetime'] = date('Y-m-d H:i:s',time());
	        $result = $Admin_problem->add($_data);
	        if($result) {
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1000,'添加失败'));
            }
	    }
		$this->show();
	}

	//问题编辑
  	public function pro_edit(){
  		$this->action = 'Problem/pro_edit';
	    if(IS_AJAX){
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['c_name'])) {
	     		$this->ajaxReturn(Message(1001,'问题名字不能为空'));
	     	}
	     	if (empty($data['c_desc'])) {
	     		$this->ajaxReturn(Message(1001,'问题描述不能为空'));
	     	}
	     	if (empty($data['c_sign'])) {
	     		$this->ajaxReturn(Message(1001,'操作类型不能为空'));
	     	}
	     	if (empty($data['c_option'])) {
	     		$this->ajaxReturn(Message(1001,'操作链接方法不能为空'));
	     	}
	     	$where['c_id'] = $data['Id'];
	      	$_data['c_name'] = $data['c_name'];
	      	$_data['c_desc'] = $data['c_desc'];
	      	$_data['c_sign'] = $data['c_sign'];
	      	$_data['c_option'] = $data['c_option'];
	        $_data['c_updatetime'] = date('Y-m-d H:i:s',time());
	        $result = M('Admin_problem')->where($where)->save($_data);
	        if($result) {
                $this->ajaxReturn(Message(0,'编辑成功'));
            }else{
                $this->ajaxReturn(Message(1000,'编辑失败'));
            }
	    }
	    $id = $_GET['Id'];
    	$this->vo = M('Admin_problem')->where('c_id='.$id)->find();
	    $this->display('pro_add');
  	}

  	// 问题删除
  	public function pro_delete()
  	{
  		$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('Admin_problem')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
  	}

  	//注销用户商家身份
  	public function shop_cancel()
  	{
  		$this->actname = '注销商家身份';
  		$this->action = 'Problem/shop_cancel';
		if(IS_AJAX){
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['c_phone'])) {
	     		$this->ajaxReturn(Message(1001,'用户手机号不能为空'));
	     	}

	     	$userinfo = M('Users')->where(array('c_phone'=>$data['c_phone']))->field('c_ucode')->find();
	     	if (!$userinfo || empty($userinfo['c_ucode'])) {
	     		$this->ajaxReturn(Message(1002,'用户信息不存在'));
	     	}
	     	
	     	$where['c_ucode'] = $userinfo['c_ucode'];
	     	//相关操作
	     	$db = M();
	     	$db->startTrans();

	     	//更改商家基本信息
	     	$usave['c_acode'] = '';
	     	$usave['c_shop'] = 0;
	     	$usave['c_invitationcode'] = '';
	     	$usave['c_num'] = '';
	     	$usave['c_isfixed1'] = 0;
	     	$result = M('Users')->where($where)->save($usave);

	     	//更改位置信息
	     	$lsave['c_isfixed'] = 0;
	     	$lsave['c_updatetime'] = date('Y-m-d H:i:s');
	     	$result = M('User_local')->where($where)->save($lsave);

	     	//清除商家申请记录
	     	$result = M('Check_shopinfo')->where($where)->delete();
	     	$result = M('User_yspay')->where($where)->delete();
	     	$result = M('Merchant')->where($where)->delete();

	     	//清除绑定邀请码
	     	$cdsave['c_ucode'] = '';
	     	$cdsave['c_state'] = 2;
	     	$result = M('Check_codelist')->where($where)->save($cdsave);

	     	//清除绑定会员
	     	$thw['c_pcode'] = $userinfo['c_ucode'];
	     	$result = M('Users_tuijian')->where($thw)->delete();

	     	//删除相关产品
	     	$psave['c_isdele'] = 2;
	     	$result = M('Product')->where($where)->save($psave);

            $db->commit();
            $this->ajaxReturn(Message(0,'操作成功'));
	    }
  		$this->display();
  	}

  	//更换手机号
  	public function changce_phone()
  	{
  		$this->actname = '更换手机号';
  		$this->action = 'Problem/changce_phone';
		if(IS_AJAX){
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['phone1'])) {
	     		$this->ajaxReturn(Message(1001,'需更改的手机号不能为空'));
	     	}
	     	if (empty($data['phone2'])) {
	     		$this->ajaxReturn(Message(1001,'需替换的手机号不能为空'));
	     	}

	     	$ucode1 = M('Users')->where(array('c_phone'=>$data['phone1']))->getField('c_ucode');
	     	$ucode2 = M('Users')->where(array('c_phone'=>$data['phone2']))->getField('c_ucode');
			
			//相关操作
	     	$db = M();
	     	$db->startTrans();

			if (empty($ucode1)) {
				$this->ajaxReturn(Message(1003,'该手机号用户未注册'));
			} else if (!empty($ucode1) && empty($ucode2)) {
				$where['c_ucode'] = $ucode1;
				$usave['c_phone'] = $data['phone2'];
				$result = M('Users')->where($where)->save($usave);
				if (!$result) {
					$db->rollback();
					$this->ajaxReturn(Message(1004,'替换失败'));
				}

			} else if (!empty($ucode1) && !empty($ucode2)) {

				$w1['c_ucode'] = $ucode1;
				$s1['c_phone'] = 'h'.$data['phone1'];
				$result = M('Users')->where($w1)->save($s1);
				if (!$result) {
					$db->rollback();
					$this->ajaxReturn(Message(1004,'替换失败'));
				}

				$w2['c_ucode'] = $ucode2;
				$s2['c_phone'] = 'h'.$data['phone2'];
				$result = M('Users')->where($w2)->save($s2);
				if (!$result) {
					$db->rollback();
					$this->ajaxReturn(Message(1004,'替换失败'));
				}

				$w3['c_ucode'] = $ucode1;
				$s3['c_phone'] = $data['phone2'];
				$result = M('Users')->where($w3)->save($s3);
				if (!$result) {
					$db->rollback();
					$this->ajaxReturn(Message(1004,'替换失败'));
				}

				$w4['c_ucode'] = $ucode2;
				$s4['c_phone'] = $data['phone1'];
				$result = M('Users')->where($w4)->save($s4);
				if (!$result) {
					$db->rollback();
					$this->ajaxReturn(Message(1004,'替换失败'));
				}
			}

            $db->commit();
            $this->ajaxReturn(Message(0,'操作成功'));
	    }
  		$this->display();
  	}


}