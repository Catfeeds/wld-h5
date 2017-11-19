<?php

namespace Adposition\Controller;

use Base\Controller\ComController;

/**
 * 推广位模块
 */
class IndexController extends ComController {
	
	/*推广位首页*/
    public function index(){
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
    	$this->ucode = session('USER.ucode');
    	$this->$returnurl = I('returnurl');
    	
    	$parr['ucode'] = $this->ucode;

        $result = IGD('Advert','Newact')->GetGratisPrize($parr);
        $this->spandnum = $result['data'];

        //获得头部推广机会数据
		$result = IGD('Advert','Newact')->AdvertHead($parr);
		$this->data = $result['data'];
    	
    	$this->display();
    }
    
    /*广告牌列表*/
    public function CardList(){
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
    	$parr['pageindex'] = I('pageindex');
    	$parr['pagesize'] = 20;
        $parr['gettype'] = I('gettype');
    	$parr['ucode'] = session('USER.ucode');
		$result = IGD('Advert','Newact')->CardList($parr);
		$this->ajaxReturn($result);
    }
    
    /*创建推广位页面*/
    public function creatad(){
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
    	$fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        
    	$this->display();
    }
	
	/*提交创建推广位信息*/
	public function SetupCard(){
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
		$parr['ucode'] = session('USER.ucode');
		$parr['ctype'] = I('ctype');
		$parr['pid'] = I('pid');
		$parr['num'] = I('numb');
		if(empty($parr['ctype']) || empty($parr['pid']) || empty($parr['num'])){
            $this->ajaxReturn(Message(3000,'请完善全部信息！'));			
		}
		if (floor($parr['num'])!=$parr['num']) {
            $this->ajaxReturn(Message(3000,'添加数量必须为整数！'));
        }
		$result = IGD('Advert','Newact')->SetupCard($parr);
        $this->ajaxReturn($result);
	} 

	/*广告牌详情页面*/
	public function detail(){
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
		/*广告牌详情*/
    	$this->cardid = I('cardid');	   	
        $parr['cardid'] = $this->cardid;
        $result = IGD('Advert','Newact')->CardInfo($parr);
//      if($result['data']['c_type']==2){
//      	$result['data']['c_money'] = sprintf('%.1f', (float)$result['data']['c_money']); 
//      }
        $this->data = $result['data'];
        
        /*适用商品列表*/
       	$parrpro['cardid'] = $this->cardid;
        $resultpro = IGD('Advert','Newact')->CardUsePro($parrpro);
        //$this->ajaxReturn($resultpro);
		$this->productlist = $resultpro['data'];
       	
       	/*投放记录*/
		$parrlog['cardid'] = $this->cardid;
		$parrlog['pageindex'] = 1;
		$parrlog['pagesize'] = 100;
        $resultlog = IGD('Advert','Newact')->CardGetList($parrlog);
		$this->datalog = $resultlog['data']['list'];

	   	$this->display();		  	
	} 
	
    //删除广告位
    public function CardDel()
    {
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['cardid'] = I('cardid');   
        $result = IGD('Advert','Newact')->CardDel($parr);
        $this->ajaxReturn($result);
    }	
    
    /*撤回*/
    public function AdvertRecall(){
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
        $parr['cardid'] = I('cardid');   
        $result = IGD('Advert','Newact')->AdvertRecall($parr);
        $this->ajaxReturn($result);
    }
    
    /*领取页面*/
    public function getcoupon(){
        //接收参数  
        $this->cardid = I('cardid');
        $this->acode = I('acode');
        $this->vid = I('vid');
        $this->type = I('type');
        //广告牌详情 
        $parr['cardid'] = $this->cardid;
        $result = IGD('Advert','Newact')->CardInfo($parr);
        $this->cardinfo = $result['data'];
        $acode = $this->cardinfo['c_ucode'];

    	//获取店铺头部信息
        $params['perucode'] = $acode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalDate($params);
        $this->data = $result['data'];
        
        //获取店铺信息
        $parr['acode'] = $acode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->storeinfo = $result['data'];
        
        $this->ucode = session('USER.ucode');
        $this->acode = $acode;
        
        /*推荐商品列表*/        
        $resultpro = IGD('Advert','Newact')->TuijianProduce($acode,6);
        $this->productlist = $resultpro['data'];
     
        /*领取还是预览*/
        $this->previewval = I('preview');
        if ($this->previewval != 1 && $this->vid && $this->acode && $this->cardid && $this->type) {
            // 增加浏览记录
            $logparr['ucode'] = session('USER.ucode');
            $logparr['acode'] = $this->acode;
            $logparr['adid'] = $this->cardid;
            $logparr['vid'] = $this->vid;
            $logparr['type'] = $this->type;
            $result = IGD('Advert','Newact')->AddScanlog($logparr);
        }
        
        $this->display();
    }

    //广告牌领取
    public function ReceiveAdvert()
    {
        if (!session('USER.ucode')) {
            $this->ajaxReturn(Message(1009,'请先授权登录'));
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = I('acode');   
        $parr['cardid'] = I('cardid');   
        $parr['vid'] = I('vid');   
        $parr['type'] = I('type'); 
        if (!$parr['acode'] || !$parr['cardid'] || !$parr['vid'] || !$parr['type']) {
            $this->ajaxReturn(Message(3000,'非法领取操作'));
        }  
        $result = IGD('Advert','Newact')->ReceiveAdvert($parr);
        $this->ajaxReturn($result);
    }
    
    /*推广位页面*/
    public function adserving(){
    	$this->ucode = session('USER.ucode');
    	$this->$returnurl = I('$returnurl');
    	/*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;

        //查询行业列表
        $result = IGD('Common','Info')->GetIndustry();
        $this->industy = $result['data'];

    	$this->display();
    }
    
    /*广告位数量列表*/
    public function AdvertSite(){
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['shoptrade'] = I('shoptrade');
        $parr['condition'] = I('condition');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;       
        $result = IGD('Advert','Newact')->AdvertSite($parr);
        $this->ajaxReturn($result);
    }
    
    /*投放详情页面*/
    public function addetail(){
        $this->acode = I('acode');
    	$parr['acode'] = I('acode');
        $result = IGD('Advert','Newact')->UserAdevert($parr);
        $this->data = $result['data'];
    	$this->display();
    }

    //指定商家广告位查询
    public function UserAdevert($parr){
        $parr['acode'] = I('acode');
        $result = IGD('Advert','Newact')->UserAdevert($parr);
        $this->ajaxReturn($result);
    }

    //投放广告
    public function PutinAdevert(){
        if (!session('USER.ucode')) {
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['cardid'] = I('cardid');
        $parr['type'] = I('type');
        $parr['order'] = I('order');
        $parr['acode'] = I('acode');
        $result = IGD('Advert','Newact')->PutinAdevert($parr);
        $this->ajaxReturn($result);   
    }

}