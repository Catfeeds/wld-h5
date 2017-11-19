<?php

namespace Activity\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 红包管理
 */

class RednewactController extends BaseController {
	/*红包管理首页*/
    public function index(){
    	$this->show();
    }   

    //撤回红包
    public function RecallRed()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["rid"] = I('rid');
        $result = IGD('Red','Newact')->RecallRed($parr);
        $this->ajaxReturn($result);
    } 

    //查询红包列表
    public function RedList()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 10;
        $result = IGD('Red','Newact')->RedList($parr);
        $this->ajaxReturn($result);
    }

    //创建红包
    public function addred()
    {
        $this->joinaid = I('joinaid');
        $backurl = I('url');
        $this->backurl = decodeurl($backurl);
        $this->returnurl = encodeurl("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    	/*获取提现安全密码*/
        $ucode = session('USER.ucode');
        $userinfo = IGD('Login','Login')->GetUserByCode($ucode);
        $userdata = $userinfo['data'];
        $this->safepwd = $userdata['c_safepwd'];
        $this->balancemon = $userdata['c_money'];
        $this->rtype = I("rtype");
        $this->show();
    }
    
    /*创建红包*/
   public function AddRedCard(){
        $parr['joinaid'] = I('joinaid');
   		$parr["ucode"] = session('USER.ucode');
   		$parr["name"] = I('name');
   		$parr["totalnum"] = I('totalnum');
   		$parr['money'] = I('money');
   		$parr['type'] = I('type');
        $result = IGD('Red','Newact')->AddRedCard($parr);
        $this->ajaxReturn($result);
   }

    //红包详情
    public function reddetail()
    {
        $this->rid = I('rid');
        $this->statu = I('statu');
        $parr["ucode"] = session('USER.ucode');
        $parr["rid"] = I('rid');
        $result = IGD('Red','Newact')->OneRedInfo($parr);
        $this->redinfo = $result['data'];

        //查询活动详情
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 100;
        $result = IGD('Red','Newact')->RedActivityLog($parr);
        $this->actdata = $result['data']['list'];
        
        $this->show();
    }

    //获取详情数据
    public function getreddetail()
    {
        $type = I('statu');
        $parr["ucode"] = session('USER.ucode');
        $parr["rid"] = I('rid');
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 20;
        if ($type == 1) {            
            $result = IGD('Red','Newact')->RedReceiveLog($parr);
        } else {
            $result = IGD('Red','Newact')->RedActivityLog($parr);
        }
        
        $this->ajaxReturn($result);
    }

    //发放记录
    public function redrecord()
    {
        $parr["ucode"] = session('USER.ucode');
        $result = IGD('Red','Newact')->MyRedCard($parr);
        $this->redrecord = $result['data'];
        
        $this->show();
    }

    //查询发放红包记录
    public function MyRedCardLog()
    {
        $parr["ucode"] = session('USER.ucode');
        $parr["pageindex"] = I('pageindex');
        $parr["pagesize"] = 20;
        $result = IGD('Red','Newact')->MyRedCardLog($parr);
        $this->ajaxReturn($result);
    }
}