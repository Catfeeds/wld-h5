<?php
namespace Home\Controller;
use Think\Controller;
/**
*   验证登录类管理
*/
class BaseController extends Controller {
	public function _initialize(){  //判断页面是否登录
		$admin=session('zongwld');
		if(empty($admin)){
			 $this->redirect('Index/login');
			 echo '<script language="javascript">top.location="'.U('Index/login').'";</script>';
		}
	}

    public function __construct()
    {
    	parent::__construct();
    	ob_end_clean();	//清除缓冲区,避免乱码
		header('Content-Type:text/html; charset=utf-8');
		// $Index = A('Index');
		// $Index->AdminSession();
    }


}