<?php

namespace Agency\Controller;

// use Think\Controller;
use Base\Controller\AuthController;

/**
 * 微商管理系统代理产品部分
 */
class IndexController extends AuthController {
	
	//首页
    public function index(){
    	$this->ucode = session('USER.ucode');

    	// 查询平台产品分类
    	$result = IGD('Common','Info')->GetCategory();
    	$this->category = $result['data'];
    	$this->show();
    }

    //代理商城 商品列表
	public function MallProducts(){
		$parr['ucode'] = session('USER.ucode');
		$parr['pageindex'] = I('pageindex');
		$parr['pname'] = I('pname');
		$parr['categoryid'] = I('categoryid');
		$parr['pagesize'] = 20;
		$result = IGD('Agencymall','Store')->MallProducts($parr);
		$this->ajaxReturn($result);
	}
	
	/*代理商品详情*/
	public function agprode(){
		$this->pcode = I('pcode');
		$this->ucode = session('USER.ucode');
		//获得产品信息
		$parr['ucode'] = session('USER.ucode');
	    $parr['pcode'] = I('pcode');
	    $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
	    $this->data = $result['data'];

	    //获取商家信息
	    $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $this->data['c_ucode'];
        $result = IGD('Smallshop', 'Store')->GetOneShopInfo($parr);
        $this->user = $result['data']['user'];
        $this->info = $result['data']['info'];
	    
	    //获取评论信息
	    $result = IGD('Productinfo','Store')->GetScore($parr);
	    $this->comment = $result['data'];
		$this->show();
	}
	
	/*代理详情*/
	public function agentde(){
		$this->ucode = session('USER.ucode');
		$this->acode = I('acode');
		$this->pcode = I('pcode');
		//获取商家信息
	    $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = I('acode');
        $result = IGD('Smallshop', 'Store')->GetOneShopInfo($parr);
        $this->user = $result['data']['user'];
        $this->info = $result['data']['info'];

        //查询产品包列表
        $parr['bag_code'] = I('bag_code');
        $result = IGD('Agencymall', 'Store')->AgencyBag($parr);
        $this->baglist = $result['data'];

        //购物车总数
		$carparr1['ucode'] = $this->ucode;
		$carparr1['acode'] = I('acode');
		$this->carcount =  IGD('Agencycar','User')->GetCount($carparr1)['data']['count'];
		
		$this->show();
	}

	//产品包 产品列表
	public function AgencyBagProduct(){
        $parr['bag_code'] = I('bag_code');
        $parr['acode'] = I('acode');
        $parr['grade'] = I('grade');
        $parr['pageindex'] = I('pageindex');
        $parr['pcode'] = I('pcode');
        $parr['pagesize'] = 10;
        $result = IGD('Agencymall', 'Store')->AgencyBagProduct($parr);
        $this->ajaxReturn($result);
	}
	
	/*商品详情*/
	public function pdetail(){

		$this->pcode = I('pcode');
		$this->ucode = session('USER.ucode');
		//获得产品信息
		$parr['ucode'] = session('USER.ucode');
	    $parr['pcode'] = $this->pcode;
	    $parr['isagent'] = 1;
	    $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
	    $this->data = $result['data'];
    	
	    //获取评论信息
	    $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $this->data['c_ucode'];
	    $result = IGD('Productinfo','Store')->GetScore($parr);
	    $this->comment = $result['data'];
	    
	    //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->data['share_url'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->data['share_img'];    
        $weixinshare["c_sharetitle"] = $this->data['share_title'];
        $weixinshare["c_discript"] = $this->data['share_desc'];
        $this->assign('weixinshare',$weixinshare);  
		$this->show();
	}

	//添加到购物车
    public function AddCar()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['num'] = I('num');
        $parr['pucode'] = I('pucode');
        $parr['pmname'] = I('pmname');
        $parr['mcode'] = I('mcode');

        $storecar = IGD('Agencycar', 'User');
        $result = $storecar->AddCar($parr);
        $this->ajaxReturn($result);
    }

   	//删除购物车商品
    public function Delecar() {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['acode'] = I('acode');
        $parr['mcode'] = I('mcode');

        $storecar = IGD('Agencycar', 'User');
        $result = $storecar->DeleCar($parr);
        $this->ajaxReturn($result);
    }

	//购物车页面
    public function agentcart()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = I('acode');

        $mycart = IGD('Agencycar', 'User');
        $result = $mycart->GetCar($parr);
        $datainfo = $result['data'];
        $this->assign('datainfo',$datainfo[0]['list']);

        //查询用户代理级别  获取代理最低价格
		$agparr['ucode'] = I('acode');
		$agparr['agentucode'] = $parr['ucode'];
		$result = IGD('Agency','Store')->AgencyGrade($agparr);
		$levelarr = $result['data'];
		$agent['levelname'] = '';$agent['agentprice'] = '0.00';
		foreach ($levelarr as $k1 => $v1) {
			if ($k1 == 0) {  //获取代理最低消费价格
				$agent['agentprice'] = $v1['c_jy_money'];
			}
			if ($v1['level'] == $v1['c_grade']) {
				$agent['levelname'] = $v1['c_grade_name'];
			}
		}

        $this->agent = $agent;
        $this->show();
    }

	
	/*品牌详情*/
	public function branddesc(){
		$parr['ucode'] = I('acode');
        $parr['bag_code'] = I('bag_code');
        $result = IGD('Agency', 'Store')->GetOneBagsInfo($parr);
        $this->data = $result['data'];
		$this->show();
	}
	
	//等级详情
    public function levelinfo()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['agentucode'] = I('acode');
        $result = IGD('Agency', 'Store')->AgencyGrade($parr);
        $this->data = $result['data'];
        $this->show();
    }

    //产品评论列表
    public function comment()
    {
    	$this->show();
    }
}