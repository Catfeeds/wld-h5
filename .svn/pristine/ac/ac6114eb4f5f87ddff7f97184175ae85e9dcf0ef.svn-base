<?php

namespace Home\Controller;

use Base\Controller\BaseController;
/**
 * 服务中心
 */
class ServeController extends BaseController {

    //获取服务中心菜单
    public function getMenu()
    {
        $key = I('openid');
        $ucode = IGD('Common','Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['terminal'] = I('type'); //1-Android,2-IOS,3-web
        $parr['version'] = I('version_number');//v3.0.0
        $result = IGD('MenuInfo', 'Serve')->GetAppMenu($parr);
        $this->ajaxReturn($result);
    }

    //获取服务中心菜单
    public function getMenu1()
    {
        $key = I('openid');
        $ucode = IGD('Common','Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['terminal'] = I('type'); //1-Android,2-IOS,3-web
        $parr['version'] = I('version_number');//v3.0.0
        $result = IGD('MenuInfo', 'Serve')->GetAppMenu1($parr);
        $this->ajaxReturn($result);
    }

    //获取服务中心菜单
    public function getMenu2()
    {
        $key = I('openid');
        $ucode = IGD('Common','Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['terminal'] = I('type'); //1-Android,2-IOS,3-web
        $parr['version'] = I('version_number');//v3.0.0
        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');
        $result = IGD('MenuInfo', 'Serve')->GetAppMenu2($parr);
        $this->ajaxReturn($result);
    }

    //获取服务中心头部数据
    public function getTopInfo()
    {
    	$key = I('openid');
        $ucode = IGD('Common','Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['terminal'] = I('type');
    	$result = IGD('MenuInfo', 'Serve')->GetTopInfo($parr);
        $this->ajaxReturn($result);
    }

    //获取邀请信息
    public function GetAsk()
    {
        $key = I('openid');
        $ucode = IGD('Common','Redis')->Rediesgetucode($key);
        $parr['app_version'] =I('app_version');
        $parr['ucode'] = $ucode;
        $result = IGD('Ask','App')->GetAsk($parr);
        $this->ajaxReturn($result);
    }

    //读取邀请信息
    public function ReadAskinfo()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        
        $parr['ucode'] = $ucode;
        $parr['askid'] = I('askid');
        $result = IGD('Ask','App')->ReadAskinfo($parr);
        $this->ajaxReturn($result);
    }

    // 获取最新版本列表
    public function Getversion() { 
        $parr['type'] = I('type');
        $parr['state'] = 1;
        $result = IGD('Index','App')->Getversion($parr);
        $this->ajaxReturn($result);
    }

}