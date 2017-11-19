<?php

namespace Agency\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 微商管理系统用户管理部分
 */
class UserController extends BaseController {
	
	//首页
    public function index(){
    	$this->ucode = session('USER.ucode');

        $parr['ucode'] = $this->ucode;
        $result = IGD('Smallshop','Store')->FindAgentlog($parr);
        if ($result['code'] != 0) {
            $this->display('emptydata');die;
        }
    	
        $userinfo = IGD('Smallshop','Store')->MySmallShop($parr);
        $this->userdata = $userinfo['data'];
    	$this->show();
    }

    //没有代理商品模板
    public function emptydata()
    {
    	$this->show();
    }

    // 我的微店
    public function myshop()
    {
        $this->ucode = session('USER.ucode');
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = $this->ucode;
        }
        $this->fromucode = $fromucode;
    	$parr['ucode'] = $fromucode;
        $userinfo = IGD('Smallshop','Store')->MySmallShop($parr);
        $this->userdata = $userinfo['data'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Agency/User/myshop?fromucode='.$fromucode;   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->userdata['c_headimg'];    
        $weixinshare["c_sharetitle"] = "这家小蜜微店不错，分享给大家看看";
        $weixinshare["c_discript"] = $this->userdata['c_nickname'];
        $this->assign('weixinshare',$weixinshare);

    	$this->show();
    }

    //代理商品列表
    public function SmallShopProduct()
    {
        $parr['ucode'] = I('fromucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Smallshop', 'Store')->SmallShopProduct($parr);
        $this->ajaxReturn($result);
    }

    //代理过的商家列表
    public function AgencyMerchant()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Smallshop', 'Store')->AgencyMerchant($parr);
        $this->ajaxReturn($result);
    }

    //商品管理商家列表
    public function ctrgoods()
    {
    	$this->show();
    }

    //商品管理商品列表
    public function goodslist()
    {
    	$this->pucode = I('pucode');
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $this->pucode;
        $result = IGD('Smallshop', 'Store')->GetOneShopInfo($parr);
        $this->user = $result['data']['user'];
        $this->info = $result['data']['info'];
    	$this->show();
    }

    //查询用户在单个代理商下所代理的产品列表
    public function AgencyProduct()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = I('acode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Smallshop', 'Store')->AgencyProduct($parr);
        $this->ajaxReturn($result);
    }

    //修改库存
    public function EditNum()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['num'] = I('num');
        $result = IGD('Smallshop', 'Store')->EditNum($parr);
        $this->ajaxReturn($result);
    }

    //删除商品
    public function ProductDel()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $result = IGD('Smallshop', 'Store')->ProductDel($parr);
        $this->ajaxReturn($result);
    }

    //等级详情
    public function levelinfo()
    {
    	$parr['ucode'] = I('pucode');
        $parr['agentucode'] = session('USER.ucode');
        $result = IGD('Agency', 'Store')->AgencyGrade($parr);
        $this->data = $result['data'];
        $this->show();
    }

    //商品详情
    public function detail()
    {   
        $pcode = I('pcode');

        $this->pcode = $pcode;
        $this->ucode = session('USER.ucode');
        $this->pucode = I('pucode');
        //获得产品信息
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = $this->pcode;
        $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
        $this->data = $result['data'];
        
        /*商品单条评论*/
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 1;
        $parr['pcode'] = $pcode;
        if(empty($parr['pageindex'])){
            $result = IGD('Agency', 'Store')->GetAllScore($parr);
        }else{
            $result = IGD('Agency', 'Store')->GetScore($parr);
        }   
        $this->proscore = $result['data']['list'][0];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->data['share_url'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->data['share_img'];    
        $weixinshare["c_sharetitle"] = $this->data['share_title'];
        $weixinshare["c_discript"] = $this->data['share_desc'];
        $this->assign('weixinshare',$weixinshare);  
        
        $this->display('pdetail');
    }

}