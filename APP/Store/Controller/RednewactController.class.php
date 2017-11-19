<?php

namespace Store\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 发放红包
 */

class RednewactController extends BaseController {
	/*发放红包首页*/
    public function index(){
        $result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        $this->joinaid = $result['data']['c_id'];
    	$this->show();
    }

    //查询发放红包列表
    public function ShopRedList()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 10;
        $result = IGD('Red','Newact')->ShopRedList($parr);
        $this->ajaxReturn($result);
    }  

    //发放红包页面
    public function sendred()
    {
        $this->show();
    }

    //查询红包列表
    public function RedList()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 10;
        $parr['sign'] = 1;
        $result = IGD('Red','Newact')->RedList($parr);
        $this->ajaxReturn($result);
    }

    //发放红包
    public function GrantRed()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["rid"] = I('rid');
        $parr['num'] = I('num');
        $result = IGD('Red','Newact')->GrantRed($parr);
        $this->ajaxReturn($result);
    }

    //红包详情
    public function reddetail()
    {
        $this->awid = I('awid');
        $parr["ucode"] = session('USER.ucode');
        $parr["awid"] = I('awid');
        $result = IGD('Red','Newact')->OneRedAwidInfo($parr);
        $this->redinfo = $result['data'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Store/Index/redshare?awid='.$this->awid;   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = GetHost().'/Uploads/logo.png';    
        $weixinshare["c_sharetitle"] = '店主“壕”无人性，居然给我免费送红包！';
        $weixinshare["c_discript"] = $this->redinfo['c_name'];
        $this->assign('weixinshare',$weixinshare);  

        $this->show();
    }

    //获取详情数据
    public function getreddetail()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["sign"] = 1;
        $parr["awid"] = I('awid');
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 20;
        $result = IGD('Red','Newact')->RedReceiveLog($parr);
        $this->ajaxReturn($result);
    }

    //发放记录
    public function redrecord()
    {
        $parr["ucode"] = session('USER.ucode');
        $result = IGD('Red','Newact')->ShopRedCard($parr);
        $this->redrecord = $result['data'];        
        $this->show();
    }

    //查询发放红包记录
    public function MyRedCardLog()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 20;
        $result = IGD('Red','Newact')->ShopRedCardLog($parr);
        $this->ajaxReturn($result);
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

        //生成店铺二维码
        $parr1['ucode'] = session('USER.ucode');
        $parr1['qrcode_type'] = 2;
        $parr1['bgcolor'] = '#f52f46';
        $result = IGD('Qrcode', 'Store')->CreateQrcode($parr1);
        $this->qcode = $result['data'];

        $this->show();
   }
  
}