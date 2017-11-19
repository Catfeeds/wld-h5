<?php

namespace Login\Controller;

use Base\Controller\BaseController;
/**
 * 登录模块
 */
class IndexController extends BaseController {

    //根据会员key获取用户信息
    public function GetUserByCode()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');

        if (empty($ucode)) {
            $this->ajaxReturn(Message(1009, "验证失效，请重新登录"));
        }

        if (!empty($parr['app_client'])) {
            $userinfo = IGD('Login', 'Login')->GetUserByCode1($parr);
        } else {
            $userinfo = IGD('Login', 'Login')->GetUserByCode($ucode);
        }
        
        $this->ajaxReturn($userinfo);
    }

	// 用户登录
    public function login()
    {
        $phone = I('phone');
        $pwd = I('pwd');
        $parr['type'] = I('type');
        $parr['unionid'] = I('unionid');
        $parr['headimg'] = I('headimg');
        $parr['nickname'] = I('nickname');
        $parr['sex'] = I('sex');
        $parr['app_version']= $_GET['app_version'];
        $parr['app_client'] = $_GET['app_client'];
        if (empty($parr['type']) || empty($parr['unionid'])) {
            if (!checkMobile($phone)) {
                $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
            }

            if (empty($phone) || empty($pwd)) {
                $this->ajaxReturn(Message(1004, "请检查您的账号或密码"));
            }
        }

        $parr['phone'] = $phone;
        $parr['pwd'] = $pwd;
        $result = IGD('Login', 'Login')->login($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }

        $data['openid'] = $result['data']['openid'];
        $this->ajaxReturn(MessageInfo(0,'登录成功',$data));
	}

    //手机短信验证
    public function sendVerify()
    {
        // 生成6位数验证码
        $regnum = rand(100000, 999999);
        $parr['telephone'] = I('phone');
        $parr['type'] = I('type');
        $parr['userid'] = C('TEl_USER');
        $parr['account'] = C('TEl_ACCESS');
        $parr['password'] = C('TEl_PASSWORD');
        $parr['content'] = "【微领地小蜜】尊敬的会员您好，验证码为：".$regnum."有效期120s，为保证您的账号安全，请勿外泄。感谢您的申请！";
        $returndata = IGD('Sendmsg', 'Login')->SendVerify($parr);
        if ($returndata['code'] == 0) {
            IGD('Common', 'Redis')->RediesStoreSram($parr['telephone'], $regnum, 3600);
        }
        $this->ajaxReturn($returndata);
    }

    //注册
    public function registerApi()
    {
        $parr['type'] = I('type');
        $parr['unionid'] = I('unionid');
        $parr['headimg'] = I('headimg');
        $parr['nickname'] = I('nickname');
        $parr['sex'] = I('sex');

        $phone = I('phone');
        if (!checkMobile($phone)) {
            $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        }

        $verifyid = IGD('Common', 'Redis')->Rediesgetucode($phone);
        if ($verifyid == '') {
            $this->ajaxReturn(Message(1000, "验证码失效，请重新获取验证码！"));
        }

        $regverify = I('verify');
        if ($regverify != $verifyid) {
            $this->ajaxReturn(Message(1001, "验证码错误！"));
        }

        $pwd = I('pwd');
        $url = I('url');
        $url = str_replace('amp;','',$url);
        if (!checkPwd($pwd) || empty($pwd)) {
            $this->ajaxReturn(Message(1003, "请设置6到16位字符！"));
        }

        $parr['phone'] = $phone;
        $parr['pwd'] = trim($pwd);
        $parr['incode'] = I('incode');
        $userinfo = IGD('Login', 'Login')->register($parr);
        $returninfo['url'] = $url;
        if($userinfo['code'] != 0) {
            $this->ajaxReturn($userinfo);
        }

        $this->ajaxReturn(MessageInfo(0, "注册成功",$userinfo['data']));
    }

    // 验证邀请码
    public function checkIncode()
    {
        $parr['incode'] = I('incode');
        if (!empty($parr['incode'])) {
            $result = IGD('Impower', 'Login')->GetUserByInvite($parr);
            if ($result['code'] != 0) {
                $this->ajaxReturn(Message(10086,'邀请码错误'));
            }
        }

        $parr['type'] = I('type');
        $parr['phone'] = I('phone');
        $parr['unionid'] = I('unionid');
        $parr['headimg'] = I('headimg');
        $parr['nickname'] = I('nickname');
        $parr['sex'] = I('sex');
        $result = IGD('Login', 'Login')->CheckXmphone($parr);
        $this->ajaxReturn($result);
    }

    //忘记密码
    public function forgetpwdApi()
    {
        $phone = I('phone');
        if (!checkMobile($phone)) {
            $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        }

        $verifyid = IGD('Common', 'Redis')->Rediesgetucode($phone);
        if ($verifyid == '') {
            $this->ajaxReturn(Message(1000, "验证码失效，请重新获取验证码！"));
        }

        $regverify = I('verify');
        if ($regverify != $verifyid) {
            $this->ajaxReturn(Message(1001, "验证码错误！"));
        }

        $pwd = I('pwd');
        if (!checkPwd($pwd) || empty($pwd)) {
            $this->ajaxReturn(Message(1003, "密码过于简单，请设置6到16位字符！"));
        }

        $parr['telephone'] = $phone;
        $parr['pwd'] = trim($pwd);
        $userinfo = IGD('Login', 'Login')->forgetpwd($parr);
        $this->ajaxReturn($userinfo);
    }

    //退出登录
    public function loginout(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['openid'] = $key;
        $parr['ucode'] = $ucode;
        $parr['app_client'] = I('app_client');
        $parr['app_version'] = I('app_version');
        $result = IGD('Login', 'Login')->Loginout($parr);
        $this ->ajaxReturn($result);
    }

}