<?php

namespace Shopping\Controller;

// use Think\Controller;
use Base\Controller\ComController;

/**
 * 商城模块及线上购物车
 */
class IndexController extends ComController {
	
	/*商城*/
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');

		/*是否有消息*/
        $parr1['ucode']= session('USER.ucode');
        $ordernum = IGD('Msgcentre','Message')->Getmsgnum($parr1);
        $this->msgtag = 0;
        if (($ordernum['order_msg'] + $ordernum['sys_msg']) > 0) {
            $this->msgtag = 1;
        }

        $parr['source'] = 3; //1-商城，2-小蜜商城，3新版商城
        $parr['tag'] = 1; //终端标识 1-Web 2-APP
        $result = IGD('Common','Info')->get_banner($parr);
        $this->banner = $result['data'];

        //商城内容模块
        $parr['state'] = 2;
        $result = IGD('Mall','User')->GetMallHompage($parr);
        $this->home = $result['data'];
    	$this->display();
    } 
    
	/*获取首页推荐商品*/
    public function ProductTjList()
    {       
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10; 
        $result = IGD('Mall','User')->ProductTjList($parr);
        $this->ajaxReturn($result);
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
    
    /*购物车页面*/
    public function mycart(){    	
    	$ucode = session('USER.ucode');
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $mycart = IGD('Shoppingcar', 'User');
        $result = $mycart->GetCar($ucode);
        $data = $result['data'];

        foreach ($data as $key => $value) {
            $data[$key]['totalprice'] = 0;

            foreach ($value['list'] as $k => $v) {
                $data[$key]['totalprice'] += sprintf("%1\$.2f", $v['c_price']*$v['c_num']);
            }
        }
        $result['data'] = $data;
        $this->cartinfo = $result['data'];
        
    	$this->display();    	
    }

    /**
     * 添加购物车
     *
     */
    public function AddCar() {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['mcode'] = I('mcode');
        $parr['pucode'] = I('pucode');
        $parr['pmname'] = I('pmname');
        $parr['num'] = I('num');
        $mycart = IGD('Shoppingcar', 'User');
        $result = $mycart->AddCar($parr);
        $this->ajaxReturn($result);
    }
    
    /*清除购物车商品*/
    public function DeleCar()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['mcode'] = I('mcode');
        $mycart = IGD('Shoppingcar', 'User');
        $result = $mycart->DeleCar($parr);
        $this->ajaxReturn($result);
    }
    
    public function comment(){
    	$this->ucode = session('USER.ucode');
    	$this->acode = I('acode');
    	$this->pcode = I('pcode');
    	$this->show();
    }
    
    /**
	 *  获取全部商品评论信息
	 *  @param openid,acode,pcode
	 *
	 */
    public function GetProductAllScore()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['acode'] = I('acode');
        $parr['pcode'] = I('pcode');

        $result = IGD('Productinfo','Store')->GetProductAllScore($parr);
        $this->ajaxReturn($result);
    }  

    /*商城商品列表*/
    public function mlist(){

        // 查询平台产品分类
        $result = IGD('Common','Info')->GetCategory();
        $this->category = $result['data'];

        $this->display();
    }

    //所有线上商品分类列表及搜索
    public function AllOnlineProductList(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $parr['pname'] = I('pname');
        $parr['source'] = 1;
        $parr['categoryid'] = I('categoryid');

        $result = IGD('Mall','User')->AllProductList($parr);
        $this->ajaxReturn($result);
    }

    /*商城商品搜索结果*/
    public function msearch(){
        $this->key = I('key');

        // 查询平台产品分类
        $result = IGD('Common','Info')->GetCategory();
        $this->category = $result['data'];

        $this->display();
    }

    //所有商品分类列表及搜索
    public function AllProductList(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $parr['pname'] = I('pname');
        $parr['categoryid'] = I('categoryid');

        $result = IGD('Mall','User')->AllProductList($parr);
        $this->ajaxReturn($result);
    }

   /*商城附近*/
    public function mnearby(){

        // 查询平台产品分类
        $result = IGD('Common','Info')->GetCategory();
        $this->category = $result['data'];

        $this->display();
    }

    //附近线下商品分类列表及搜索
    public function NearbyProductList(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $parr['pname'] = I('pname');
        $parr['categoryid'] = I('categoryid');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('Mall','User')->NearbyProductList($parr);
        $this->ajaxReturn($result);
    }

    /*商城猜你喜欢*/
    public function mguess(){
        $this->display();
    }

    public function seladdress(){
        $this->display();
    }
}