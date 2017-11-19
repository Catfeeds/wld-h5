<?php

namespace Shopping\Controller;

use Base\Controller\ComController;

/**
 * 	实体商品及购物车
 */
class EntitymapController extends ComController {

	/**
     * 首页
     */
    public function index()
    {
        $this->show();
    }

    //实体店商品详情
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
        $parr['pagesize'] = 10;
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

        $this->display('edetail');
    }


   /**
     * 添加到购物车
     */
    public function AddCar()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['num'] = I('num');
        $parr['pucode'] = I('pucode');

        $storecar = IGD('Storecar', 'User');
        $result = $storecar->AddCar($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 删除购物车商品
     */
    public function Delecar() {

        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['acode'] = I('acode');

        $storecar = IGD('Storecar', 'User');
        $result = $storecar->DeleCar($parr);
        $this->ajaxReturn($result);
    }

    /**
     *购物车页面
     */
    public function mycart()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = I('acode');

        $mycart = IGD('Storecar', 'User');
        $result = $mycart->GetCar($parr);
        $datainfo = $result['data'];

        $this->assign('datainfo',$datainfo);

        $this->show();
    }

    /**
     * 商品在购物车数量
     */
    public function Getprocount() {
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');

        $mycart = IGD('Storecar', 'User');
        $result = $mycart->Getprocount($parr);
        $this->ajaxReturn($result);
    }

}