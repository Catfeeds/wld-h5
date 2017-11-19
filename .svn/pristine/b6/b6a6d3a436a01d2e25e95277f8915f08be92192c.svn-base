<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

    //跳转页面
    public function index()
    {   
        $admin1 = session('_ADMIN_UCODE');
        $admin2 = session('_AGENT_UCODE');
        $admin3 = session('_SHOP_UCODE');
        if(!empty($admin1)){
            header("Location:" . WEB_HOST.'/agent.php/Home/Information/index');
        } else if (!empty($admin2)) {
            header("Location:" . WEB_HOST.'/agent.php/Agent/Information/index');
        } else if (!empty($admin3)) {
            header("Location:" . WEB_HOST.'/agent.php/Shop/Information/index');
        } else {
            $this->redirect('Login/index');
        }
    }
	
}