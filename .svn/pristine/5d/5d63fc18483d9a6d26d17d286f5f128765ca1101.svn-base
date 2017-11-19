<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\ComController;
/**
 * 用户个人空间
 */

class MyspaceController extends ComController {
	/*店家个人空间*/
    public function index(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
    	/*获取个人信息*/
    	$parr['fromucode'] = $fromucode;
		$userinfo = IGD('Coalition','Trade');
		$result = $userinfo->GetUserInfo($parr);
		$this->myspace=$result['data'];
        $mytab = $result['data']['c_tab'];
        if ($mytab != null) {
            $this->mylabel = explode('|', $mytab);
        }

		/*关注信息*/
    	$params['perucode'] = $fromucode;
        $params['ucode'] = session('USER.ucode');
		$result = IGD('Resourcev2','Trade')->PersonalDate($params);
        $this->data = $result['data'];

		$this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');
    	$this->show();
    }

    //获取个人资源列表
    public function GetResourceList()
    {
        $parr['issue_ucode'] = I('issue_ucode');
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Resource','Trade')->GetResourceList($parr);
        $this->ajaxReturn($result);
    }

    /*非店家个人空间*/
    public function resource()
    {
    	/*获取个人信息*/
    	$parr['fromucode'] = I('fromucode');
		$userinfo = IGD('Coalition','Trade');
		$result = $userinfo->GetUserInfo($parr);
		$this->myspace=$result['data'];

    	$this->show();
    }

    // 我的店
    public function store()
    {
        /*获取个人信息*/
        $parr['fromucode'] = I('fromucode');
        $userinfo = IGD('Coalition','Trade');
        $result = $userinfo->GetUserInfo($parr);
        $this->myspace=$result['data'];
        $mytab = $result['data']['c_tab'];
        if ($mytab != null) {
            $this->mylabel = explode('|', $mytab);
        }

        /*关注信息*/
        $params['perucode'] = I('fromucode');
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resourcev2','Trade')->PersonalShop($params);
        $this->data = $result['data'];
        $this->issue_ucode = I('fromucode');
        $this->ucode = session('USER.ucode');
        $this->apptype = session('USER.types');
        $this->show();
    }

    // 获取店铺商品数据
    public function GetproductList()
    {
        $parr['type'] = I('statu');
        $parr['shop_ucode'] = I('issue_ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Resourcev2','Trade')->GetproductList($parr);
        $this->ajaxReturn($result);
    }

    public function myattention()
    {
        if (IS_AJAX) {
            $parr['ucode'] = session('USER.ucode');
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 20;
            $parr['type'] = 1;
            $result = IGD('Resourcev2','Trade')->Myattention($parr);
            $this->ajaxReturn($result);
        }
        $this->show();
    }

    public function myfans()
    {
        if (IS_AJAX) {
            $parr['ucode'] = session('USER.ucode');
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 20;
            $parr['type'] = 2;
            $result = IGD('Resourcev2','Trade')->Myattention($parr);
            $this->ajaxReturn($result);
        }
        $this->show();
    }

}