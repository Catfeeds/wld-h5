<?php

namespace Home\Controller;

use Think\Controller;
/**
 * 信息
 */

class InfoController extends Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    //初始化引入微信分享类
    public function _initialize() {
        vendor('Wxshare.wxshare');
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));        
        $signPackage = $wxshare->GetSignPackage();      
        $this->assign('signPackage',$signPackage);
    }

    public function index(){     	
    	$this->show();
    }

    /*公告详情*/
    public function details()
    {
//  	$Id = I('id');
//  	$result = IGD('Index',"App")->Getdetails($Id);
//  	$rt = $result['data'];
//  	$this->list = $rt; 
//      
//      $this->title = $this->list["c_title"].'--'.C('WEB_NAME').'--微资讯';         
//      $this->description = C('WEB_NAME').'--微资讯';
//      if (is_weixin()) {
//          $this->apptype = 4;
//      } else {
//          $this->apptype = get_device_type();
//      }   
//
//      $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
//      $signPackage = $wxshare->GetSignPackage();  
//      $signPackage['url'] = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];   
//      $this->assign('signPackage',$signPackage);
//      
//      $weixinshare["c_pthumbnail"] = WEB_HOST."/Resource/Common/img/newsshare.png";    
//      $weixinshare["c_sharetitle"] = $rt['c_title'];
//      $weixinshare["c_discript"] = C('WEB_NAME').'--微资讯';
//      $this->assign('weixinshare',$weixinshare); 

    	$this->display();
    }
    // 公告
    public function affiche(){   
//  	$ucode = session('USER.ucode');
//      if(!session('USER.ucode')){
//          $this->userlogin();die;
//      }     
		$where['c_id'] = I('Id'); 		
 		$vo = M('Check_info')->where($where)->find();		
 		$this->assign('list',$vo);
 		 		
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];   
        $this->assign('signPackage',$signPackage);
        
        $weixinshare["c_pthumbnail"] = WEB_HOST."/Resource/Common/img/logo.png";    
        $weixinshare["c_sharetitle"] = $vo['c_title'];
        $weixinshare["c_discript"] = C('WEB_NAME').'--微资讯';
        $this->assign('weixinshare',$weixinshare);  		 		
 		 		
        $this->show();
    }

    /*通告信息*/
    public  function  pubnotice(){
        $this->show();
    }
}