<?php
namespace Agent\Controller;
use Think\Controller;
class BaseController extends Controller{

  	//公共处理器   判断是否登录
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
		$str = $_GET;
        if (!empty($str['openid'])) {
            if (Verification($str)) {
                $this->CheckApp($str);
            } else {
                $this->userlogin();die();
            }
        }

        //移动设备浏览，则切换模板
        if (ismobile()) {
            //设置默认默认主题为 Mobile
            C('DEFAULT_THEME','Mobile');
        }

        //判断页面是否登录
        $admin = session('_AGENT_UCODE');
        if(empty($admin)){
            header("Location:" . WEB_HOST.'/agent.php/Home/Login/index');
        }
    }

	public function _initialize(){  
		
	}	

    //跳转用户登录
    function userlogin() {
        header("Location:" . WEB_HOST.'/agent.php/Home/Login/index');
    }

    //判断是否App进入
    function CheckApp($str) {
        $key = $str['openid'];
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        if ($ucode) {
            IGD('Common', 'Redis')->RediesStoreSram($str['openid'], $ucode,86400);
            session('start');
            session('_AGENT_UCODE', $ucode);  //设置session

            $url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            $stra = "";
            foreach ($str as $k => $v) {
                if ($k!= "token" && $k != "time" && $k != '_URL_' && $k != 'openid') {
                    $stra .= $k."=".$v."&";
                }
            }
            if (strlen($stra) > 0) {
                $stra = mb_substr($stra, 0, strlen($stra) - 1,'utf8');
            }
            if (strlen($stra) > 0) {
                $temp = "?" . $stra;
                $url.=$temp;
            }
            header("Location:" . $url);die();
        } else {
            $this->userlogin();die();
        }
    }
 
	
}