<?php
namespace Shop\Controller;
use Think\Controller;
/**
 *  登录注册管理
 */
class LoginController extends Controller{

  	//公共处理器   判断是否登录
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

    Public function _initialize(){
        //移动设备浏览，则切换模板
        if (ismobile()) {
            //设置默认默认主题为 Mobile
            C('DEFAULT_THEME','Mobile');
        }
        //............你的更多代码.......
    }	
    /*登录地址*/
    public function index()
    {
        header("Location:" . WEB_HOST.'/agent.php/Home/Login/index');
    }
    
    //退出登录
    public function logout(){
        cookie('_SHOP_UCODE',null);
        cookie('_SHOP_NAME',null);
        session('_SHOP_UCODE',null);
        session('_SHOP_NAME',null);
        header("Location:" . WEB_HOST.'/agent.php/Home/Login/index');
    }
	
}