<?php

namespace Home\Controller;

// use Think\Controller;
use Base\Controller\WxAuthController;
/**
 *  第三方授权页面-2016-12-27WxAuth
 */
class AccreditController extends WxAuthController {

 	public function __construct()
    {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    //授权页面
    public function index()
    {
    	if (session('USER.ucode')) {
    		$url = GetHost(1)."/index.php/Order/Scanpay/binded?ucode=".session('USER.ucode');
    		header("Location:" . $url);die;
    	}
   	    $this->show();
    }

    //用户微信授权页面
    public function AuthUser()
    {
        //基本信息
        $parr['phone'] = I('phone');
        if (!checkMobile($parr['phone'])) {
            $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        }

        $verifyid = IGD('Common', 'Redis')->Rediesgetucode($parr['phone']);
        if ($verifyid == '') {
            $this->ajaxReturn(Message(1000, "验证码失效，请重新获取验证码！"));
        }

        $regverify = I('verify');
        if ($regverify != $verifyid) {
            $this->ajaxReturn(Message(1001, "验证码错误！"));
        }

        $parr['phone'] = I('phone');
        $parr['incode'] = I('incode');
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $parr['type'] = 2;
            $parr['openid'] = $alipayinfo['openid'];
            $parr['unionid'] = $alipayinfo['unionid'];
            $parr['nickname'] = $alipayinfo['nickname'];
            $parr['headimgurl'] = $alipayinfo['headimgurl'];
        } else {
            if (!session('openid')) {
                $this->ajaxReturn(Message(1004, "请从微信进入绑定"));
            }
            $parr['type'] = 1;
            $parr['openid'] = session('openid');
            $parr['unionid'] = session('unionid');
            $parr['nickname'] = session('nickname');
            $parr['headimgurl'] = session('headimgurl');
        }

        $result = IGD('Impower','Login')->AuthUser($parr);
        session('USER.ucode',$result['data']);
        $this->ajaxReturn($result);
    }
}
