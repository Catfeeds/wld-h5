<?php

namespace Store\Controller;

use Base\Controller\ComController;

/**
 * 线上线下店铺模块
 */
class IndexController extends ComController {
    //店铺首页
    public function index()
    {
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');

        //获取店铺模板
        $parr['acode'] = $fromucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetShopTpl($parr);

        $this->compareid = $result['data']['compareid'];/*已应用的模板*/
        $this->tplid = $result['data']['tplid'];/*生成的模板*/

        $parr['tplid'] = $this->tplid;
        $parr['app_version'] = '3.0.5';
        $parr['isprew'] = 2;
        $result = IGD('Store','Store')->GetShopTplContent($parr);

        foreach ($result['data'] as $key => $value) {
           if ($value['c_types'] == 1) {   //头部
                $topimg[] = $value['c_img'];
            } else if ($value['c_types'] == 2) {  //banner
                $bannerimg[] = $value['c_img'];
            } else if ($value['c_types'] == 3) {    //卡劵
                $cardimg[] = $value['c_img'];
            }
        }
        $this->topimg = $topimg;
        $this->bannerimg = $bannerimg;
        $this->cardimg = $cardimg;
        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parrs['acode'] = $this->issue_ucode;
        $parrs['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parrs);
        $this->storeinfo = $result['data'];
        $this->imglist = $result['data']['imglist'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->storeinfo['shareurl'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->storeinfo['shareimg'];    
        $weixinshare["c_sharetitle"] = $this->storeinfo['sharetit'];
        $weixinshare["c_discript"] = $this->storeinfo['sharedesc'];
        $this->assign('weixinshare',$weixinshare);  

        //获取红包信息
        $result = IGD('Red','Newact')->ViewShopRed($parrs);
        $this->prizedata = $result['data'];

        $this->display('temppage'.$this->compareid);
    }

    //领取红包
    public function ReceiveRed()
    {
        $parr['awid'] = I('awid');
        $parr['sid'] = I('sid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Red','Newact')->ReceiveRed($parr);
        $this->ajaxReturn($result);
    }

	/*商圈首页*/
    public function source(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
		$this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');

        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parr['acode'] = $this->issue_ucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->storeinfo = $result['data'];

    	$this->display('index');
    }

    
    /*线下头部*/
    public function offlinetop(){
    	$this->display();
    }
    
    /*线上头部*/
    public function onlinetop(){
    	$this->display();
    }  
      
    /**
     * 店铺相册
     */
    public function photo()
    {
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $this->issue_ucode;
        $result = IGD('Store','Store')->GetStoreInfo($parr);

        $this->datainfo = $result['data'];
        $this->imglist = $result['data']['imglist'];
        $this->show();
    }

	/*产品列表*/
    public function productlist(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');

        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parr['acode'] = $this->issue_ucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->storeinfo = $result['data'];
        $this->ucode = session('USER.ucode');
    	$this->display();
    }

    //查询微商产品列表
    public function GetProduceList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = I('acode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['type'] = 1;
        $parr['gettype'] = 1;
        $isfixed = I('isfixed');
        if ($isfixed == 1) {
            $result = IGD('Store','Store')->GetProduceList($parr);
        } else {
            $result = IGD('Shop','Store')->GetProduceList($parr);
        }
        $this->ajaxReturn($result);
    }
    
    //店铺领取优惠券页面
    public function couponlist(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;

        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $fromucode;
        $parr['type'] = 1;
        $result = IGD('Advert','Newact')->GetShopAdvert($parr);
        $this->couponlist = $result['data'];
        $this->show();
    }

    // 活动列表
    public function activitylist(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');
        $this->loginurl =  GetHost(1) . '/index.php/Login/Index/index?url=' . $url;

        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parr['acode'] = $this->issue_ucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->storeinfo = $result['data'];
        $this->display();
    }

    //获取商家活动中心列表
    public function ShopActivityList()
    {
        $parr['ucode'] = I('acode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Index','Newact')->ShopActivityList($parr);
        $this->ajaxReturn($result);
    }

    //评论列表
    public function comment()
    {
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');
        $this->display();
    }

    /*分享页面*/
    public function redshare(){
        $this->awid = I('awid');
        //查询红包信息
        $parr["ucode"] = session('USER.ucode');
        $parr["awid"] = I('awid');
        $result = IGD('Red','Newact')->OneRedAwidInfo($parr);
        $this->redinfo = $result['data'];

        //查询商家信息
        $parr["acode"] = $this->redinfo['c_acode'];
        $result = IGD('Red','Newact')->GetShopBaseInfo($parr);
        $this->user = $result['data'];

        //商家信息
        $w['c_ucode'] = $parr["acode"];
        $shopinfo = M('Users')->where($w)->find();

        //生成店铺二维码
        $parr1['ucode'] = session('USER.ucode');
        $parr1['qrcode_type'] = 2;
        $parr1['bgcolor'] = '#f52f46';
        $result = IGD('Qrcode', 'Store')->CreateQrcode($parr1,$shopinfo);
        $this->qcode = $result['data'];

        $this->show();
    }

    //优惠专区
    public function profit()
    {
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');
        $this->show();
    }

    //获取优惠专区数据
    public function ShopProductList()
    {
        $parr['acode'] = I('acode');
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Groupbuy','Newact')->ShopProductList($parr);
        $this->ajaxReturn($result);
    }
    
    /*商品模板*/
   public function temproduct(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        //获取店铺模板
        $this->ucode = session('USER.ucode');
        $parr['ucode'] = $fromucode;
        $result = IGD('Store','Store')->GetShopTpl($parr);
        $this->tplid = $result['data']['tplid'];
        
        $parr['tplid'] = $this->tplid;
       
        $result = IGD('Store','Store')->GetShopTplContent($parr);
        $this->tpl = $result['data'];

        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parrs['acode'] = $this->issue_ucode;
        $parrs['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parrs);
        $this->storeinfo = $result['data'];
        //dump($this->storeinfo);die;
        $this->imglist = $result['data']['imglist'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->storeinfo['shareurl'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->storeinfo['shareimg'];    
        $weixinshare["c_sharetitle"] = $this->storeinfo['sharetit'];
        $weixinshare["c_discript"] = $this->storeinfo['sharedesc'];
        $this->assign('weixinshare',$weixinshare);  

        //获取红包信息
        $result = IGD('Red','Newact')->ViewShopRed($parrs);
        $this->prizedata = $result['data'];
   	
   		$this->show();
   }

    /*线上商家分类页面*/
    public function procategory(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;

        /*查询所有分类*/
        $parr['acode'] = $fromucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Category','Store')->cateList($parr);

        $this->typelist = $result["data"];

        $this->display();
    }

    /*线上商家根据分类查询商品信息*/
    public function allshop(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->cateid = I('cateid');
        $this->display();
    }


    /**
     * 查询分类,商品
     */
      public function GetCategory(){
          $fromucode = I('fromucode');
          $parr['acode'] = $fromucode;
          $parr['ucode'] = session('USER.ucode');
          $result = IGD('Store','Store')->GetCategory($parr);
          $this->ajaxReturn($result);
      }

    /**
     * 查询分类列表
     */
    public function CateList(){
        $parr['acode'] = I('fromucode');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Category','Store')->cateList($parr);
        $this->ajaxReturn($result);
    }

    //店铺领取卡劵
    public function ReceiveShopCoupon()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['awid'] = I('awid');
        $result = IGD('Coupon','Newact')->ReceiveShopCoupon($parr);
        $this->ajaxReturn($result);
    }

    //查询店铺发放卡劵列表
    public function ShopCouponList()
    {
        $parr['acode'] = I('acode');
        $parr['ucode'] = session('USER.ucode');
        // $parr['type'] = 1;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Coupon','Newact')->ShopCouponList($parr);
        $this->ajaxReturn($result);
    }

    //查询店铺首页发放卡劵列表
    public function GetCouponList()
    {
        $fromucode = I('fromucode');
        $parr['acode'] = $fromucode;
        $parr['ucode'] = session('USER.ucode');
        $parr['type'] = 1;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 4;
        $result = IGD('Coupon','Newact')->ShopCouponList($parr);
        $this->ajaxReturn($result);
    }

    //根据店铺模板Id获取模板内容
    public function GetShopTplContent()
    {
        $parr['tempid'] = I('tempid');/*生成的模板id*/
        $parr['tplid'] = I('tplid');/*已应用的模板id*/
        $result = IGD('Store','Store')->GetShopTplContent($parr);
        $this->ajaxReturn($result);
    }
    
    //根据商品分类查询商品列表
    public function GetCategoryInfo(){
//        $acode = I('fromucode');
//        if (empty($acode)) {
//            $acode = session('USER.ucode');
//        }
        $parr['acode'] = I('fromucode');
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = I('cateid');
        $parr['pageindex'] = I('pageindex');
        $result = IGD('Store','Store')->GetCategoryInfo($parr);
        $this->ajaxReturn($result);
    }
    //查询分类
    public function categorypro(){
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = I('acode');
        $result = IGD('Store','Store')->GetCategory($parr);
        $this->ajaxReturn($result);

    }

    /*测试页面*/
    public function temppage1111(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');

        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parr['acode'] = $this->issue_ucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->storeinfo = $result['data'];
        $this->ucode = session('USER.ucode');
        $this->display();
    }
}