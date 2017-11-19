<?php

namespace Home\Controller;

use Base\Controller\BaseController;

/**
 * 代理商模块
 */
class SignupController extends BaseController {


    //获取用户签到信息
    public function GetUserSignInfo(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $result =IGD('Signup', 'Activity')->GetSignInfo($parr);
        $this->ajaxReturn($result);
    }

    //用户签到
    public function Signup(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;

        $result =IGD('Signup', 'Activity')->SignUp($parr);
        $this->ajaxReturn($result);
    }

    //获取用户签到记录

    public function GetSignRecord(){
        $key = I('openid');
        $pageindex =I('pageindex');
        $pagesize =I('pagesize');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['pageindex']=$pageindex;
        $parr['pagesize']=$pagesize;
        $result =IGD('Signup', 'Activity')->GetSignRecord($parr);
        $this->ajaxReturn($result);
    }

    //判断用户今天是否签到

    public function JudgeSign(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $result =IGD('Signup','Activity')->JudgeUserSign($parr);
        $this->ajaxReturn($result);
    }
}
