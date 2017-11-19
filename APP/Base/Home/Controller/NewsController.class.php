<?php

namespace Home\Controller;

use Think\Controller;
/**
 * 资讯中心
 */

class NewsController extends Controller {

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

    /*获取微域资讯列表*/
    public function news_list()
    {
        $parr['pageIndex'] = I('pageindex');
        $parr['pagesize'] = 15;
    	$result = IGD('Index',"App")->NewsList($parr);    	        
    	$this->ajaxReturn($result);
    }

    /*资讯详情*/
    public function details()
    {
    	$Id = I('id');
    	$result = IGD('Index',"App")->Getdetails($Id);
    	$rt = $result['data'];
    	$this->list = $rt; 
        
        $this->title = $this->list["c_title"].'--'.C('WEB_NAME').'--微资讯';         
        $this->description = C('WEB_NAME').'--微资讯';
        if (is_weixin()) {
            $this->apptype = 4;
        } else {
            $this->apptype = get_device_type();
        }   

        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];   
        $this->assign('signPackage',$signPackage);
        
        $weixinshare["c_pthumbnail"] = WEB_HOST."/Resource/Common/img/newsshare.png";    
        $weixinshare["c_sharetitle"] = $rt['c_title'];
        $weixinshare["c_discript"] = C('WEB_NAME').'--微资讯';
        $this->assign('weixinshare',$weixinshare); 

    	$this->display();
    }


    
}