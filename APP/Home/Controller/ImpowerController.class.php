<?php

namespace Home\Controller;

use Think\Controller;
/**
 *  第三方授权页面
 */
class ImpowerController extends Controller {

 	public function __construct()
    {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    //老用户注册
    public function regress()
    {
        if (IS_AJAX) {
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
            $parr['pwd'] = encrypt($pwd,C('ENCRYPT_KEY'));
            $parr['incode'] = I('incode');

            //新增注册赠送红包
            $parr['sourceapp'] = 1;
            $a = array('2.88','3.88','4.88');
            $parr['money'] = $a[array_rand($a,1)];
            $parr['desc'] = '微领地老用户注册赠送';
            $parr['content'] = '小蜜为鼓励微领地老用户回归，赠送您红包￥'.$parr['money'].'，已存入您的余额';

            $parr['url'] = I('url');
            $register = IGD('Login','Login');
            $userinfo = $register->register($parr);
            if (empty($parr['url'])) {
                $returninfo['url'] = WEB_HOST.'/index.php/Home/Appversion/index?money='.$parr['money'];
            } else {
                $returninfo['url'] = $url;
            }

            if($userinfo['code'] == 0) {
                // 设置session()
                session('start');
                session('USER.ucode', $userinfo['data']['c_ucode']);  //设置session
                $this->ajaxReturn(MessageInfo(0, "注册成功",$returninfo));
            } else {
                $this->ajaxReturn($userinfo);
            }
        }

        $url = I('url');
        $url = str_replace('amp;','',$url);
        $this->url = urldecode($url);
        $this->incode = I('incode');
        $this->show();
    }

    //推荐注册页面
    public function invite()
    {
    	//$this->display(T('Base@Common/404'));
        if (IS_AJAX) {
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
            $parr['pwd'] = encrypt($pwd,C('ENCRYPT_KEY'));
            // $parr['incode'] = I('incode');
            $parr['url'] = I('url');
            $register = IGD('Login','Login');
            $userinfo = $register->register($parr);
            $returninfo['url'] = $url;
            if($userinfo['code'] == 0) {
                // 设置session()
                session('start');
                session('USER.ucode', $userinfo['data']['c_ucode']);  //设置session
                $this->ajaxReturn(MessageInfo(0, "注册成功",$returninfo));
            } else {
                $this->ajaxReturn($userinfo);
            }
        }

        $url = I('url');
        $url = str_replace('amp;','',$url);
        if (empty($url)) {
           $this->url = WEB_HOST.'/index.php/Home/Index/index';
        } else {
           $this->url = urldecode($url);
        }

        $this->cururl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->incode = decrypt(I('incode'),C('ENCRYPT_KEY'));
        $parr['incode'] = $this->incode;
        $result = IGD('Impower','Login')->GetUserByInvite($parr);
        $this->data = $result['data'];
        $this->show();
    }
}
