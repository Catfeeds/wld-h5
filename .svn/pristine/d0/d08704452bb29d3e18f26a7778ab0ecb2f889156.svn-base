<?php

namespace Home\Controller;

use Think\Controller;
//use Base\Controller\BaseController;

/**
 * 服务中心
 */

class IndexController extends Controller {
	/*服务中心首页*/
    public function index(){
    	$ucode = session('USER.ucode');
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $result['data'];
    	
    	/*是否有消息*/
        $parr1['ucode']= session('USER.ucode');
        $ordernum = IGD('Msgcentre','Message')->Getmsgnum($parr1);
        $this->msgtag = 0;
        if (($ordernum['order_msg'] + $ordernum['sys_msg']) > 0) {
            $this->msgtag = 1;
        }
    	
    	$this->ucode = session('USER.ucode');

        $parr['terminal'] = 3;
        $parr['ucode'] = $ucode;
        $result = IGD('MenuInfo', 'Serve')->GetTopInfo($parr);
        $this->menu = $result['data'];
    	
    	$this->show();
    }
    
    /*获取服务中心菜单*/
    public function GetServerMenu(){    	
    	$parr['ucode'] = session('USER.ucode');
        $parr['terminal'] = 3;//I('type'); //1-Android,2-IOS
        $parr['version'] = 'v3.0.0';//I('version_number');
        $result = IGD('MenuInfo', 'Serve')->GetAppMenu($parr);
        $this->ajaxReturn($result);
    }

    //设置页面
    public function system()
    {
        $this->show();
    }
        
}