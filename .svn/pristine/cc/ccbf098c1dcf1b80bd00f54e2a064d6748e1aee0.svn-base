<?php

namespace Login\Controller;

use Think\Controller;

/**
 * 登录模块
 */
class IndexController extends Controller {

    public function index(){
    	$url = I('url');
        if (empty($url)) {
           $this->url = WEB_HOST.'/index.php/Home/Index/index';
        } else {
            $this->url = decodeurl($url);
        }
    	$this->display();
    }

    //根据会员key获取用户信息
    public function GetUserByCode()
    {
        $key = I('openid');
        if (empty($key)) {
            $this->ajaxReturn(Message(1001, "请求错误"));
        }

        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        if (empty($ucode)) {
            $this->ajaxReturn(Message(1009, "验证失效，请重新登录"));
        }

        $userinfo = IGD('Login', 'Login')->GetUserByCode($ucode);
        $this->ajaxReturn($userinfo);
    }

	// 用户登录
    public function login()
    {
    	$phone = I('phone');
        if (!checkMobile($phone)) {
            $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        }

        $pwd = I('pwd');
        if (empty($phone) || empty($pwd)) {
            $this->ajaxReturn(Message(1004, "请检查您的账号或密码"));
        }

        $parr['phone'] = $phone;
        $parr['pwd'] = $pwd;
        $result = IGD('Login', 'Login')->login($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        $data['openid'] = $result['data']['openid'];
        session('USER.ucode', $result['data']['c_ucode']);  //设置session
        $this->ajaxReturn(MessageInfo(0,'登录成功',$result['data']));
	}

    //手机短信验证
    public function sendVerify()
    {
    	if(IS_AJAX) {
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

	    $this->ajaxReturn(Message(1000,'非法操作'));
    }

    // 注册
    public function register()
    {
        $url = I('url');
        $url = str_replace('amp;','',$url);
        if (empty($url)) {
           $this->url = WEB_HOST.'/index.php/Home/Index/index';
        } else {
           $this->url = decodeurl($url);
        }
        $this->incode = I('incode');

        $this->action = I('action');

        $this->show();
    }

    public function registerApi()
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
        $url = I('url');
        $url = str_replace('amp;','',$url);
        if (!checkPwd($pwd) || empty($pwd)) {
            $this->ajaxReturn(Message(1003, "请设置6到16位字符！"));
        }

        $parr['phone'] = $phone;
        $parr['pwd'] = trim($pwd);
        $parr['incode'] = I('incode');
        $parr['url'] = I('url');
        $userinfo = IGD('Login', 'Login')->register($parr);
        $returninfo['url'] = $url;
        if($userinfo['code'] != 0) {
            $this->ajaxReturn($userinfo);
        }

        Optionval('USER.ucode', $userinfo['data']['c_ucode']);  //设置session
        $this->ajaxReturn(MessageInfo(0, "注册成功",$returninfo));
    }

    //忘记密码
    public function forgetpwd()
    {
        $this->show();
    }

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

    /*阅读协议*/
    public function read()
    {
        $this->apptype=I('type');
        $this->show();
    }

    //退出登录
    public function loginout(){
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Login', 'Login')->Loginout($parr);
        if ($result['code'] == 0) {
        	session('USER.ucode',null);
        	$this->redirect('Index/index');
        }
    }

    // 验证邀请码
    public function checkIncode()
    {
        $parr['incode'] = I('code');
        $result = IGD('Impower', 'Login')->GetUserByInvite($parr);
        $this->ajaxReturn($result);
    }

}