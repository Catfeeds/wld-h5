<?php

namespace Base\Controller;

use Think\Controller;

/**
 * 微信静态授权
 * 需要登录的基类
 */
class BaseController extends AuthController {

    public function __construct() {
        parent::__construct();
        if (is_weixin()) {
            if (!session('USER.ucode')) {
                $this->GetOpenid();
            }
        } else if (is_aliApp()) {
            if (!session('USER.ucode')) {
                $this->GetAlOpenid();
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();
            }
        }
    }

    // 获取用户openid
    public function GetOpenid()
    {
        $tempredirect_uri = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (!empty($_GET['code'])) {
            $code = $_GET['code'];
            $weixininfo = $this->getgetOpenid($code);
            $abs_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$weixininfo['access_token']."&openid=".$weixininfo['openid']."&lang=zh_CN";
            $abs_url_data = file_get_contents($abs_url);
            $obj_data = objarray_to_array(json_decode($abs_url_data));
            session('openid',$weixininfo['openid']);
            session('unionid',$weixininfo['unionid']);
            session('nickname',$obj_data['nickname']);
            session('headimgurl',$obj_data['headimgurl']);
            session('AuthInfo',$obj_data);

            // 查询用户是否授权
            $parr['unionid'] = $weixininfo['unionid'];
            $parr['openid'] = $weixininfo['openid'];
            $parr['type'] = 1;  //1微信授权
            $result = IGD('Impower','Login')->AuthSeacher($parr);
            if ($result['code'] != 0) {
                $this->userlogin();die();
            }

            //完善授权信息
            // if ((empty($result['data']['c_name']) || empty($result['data']['c_headimg'])
            //     || empty($result['data']['c_unionid']))
            //     && (!$obj_data['nickname']) || !empty($obj_data['headimgurl'])
            //     || !empty($weixininfo['unionid'])) {
            //     $authparr['type'] = 1;
            //     $authparr['openid'] = $weixininfo['openid'];
            //     $authparr['unionid'] = $weixininfo['unionid'];
            //     $authparr['nickname'] = $obj_data['nickname'];
            //     $authparr['headimgurl'] = $obj_data['headimgurl'];
            //     IGD('Impower','Login')->PerfectAuthInfo($authparr);
            // }

            session('USER.ucode',$result['data']['c_ucode']);
        } else {
            $redirectUrl = urlencode($tempredirect_uri);
            $url = $this->createOauthUrlForCode($redirectUrl,'snsapi_base');
            header("Location:" . $url);die;
        }
    }

    //获取支付宝授权信息
    public function GetAlOpenid()
    {
        if (!session('alipay_authinfo')) {
            $config = C('ALIPAYCONFIG');
            $returnurl = encodeurl("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $url = $config['alipay_auth_url'].'?auth_type=auth_base&returnurl='.$returnurl;
            header("Location:$url");die;
        }

        if (!session('USER.ucode')) {
            $alipayinfo = session('alipay_authinfo');

            // 查询用户是否授权
            $parr['openid'] = $alipayinfo['openid'];
            $parr['type'] = 2;  //2支付宝授权
            $result = IGD('Impower','Login')->AuthSeacher($parr);
            if ($result['code'] != 0) {
                $this->userlogin();die();
            }

            //完善授权信息
            // if ((empty($result['data']['c_name']) || empty($result['data']['c_headimg'])
            //     || empty($result['data']['c_unionid']))
            //     && (!$alipayinfo['nickname']) || !empty($alipayinfo['headimgurl'])
            //     || !empty($alipayinfo['unionid'])) {
            //     $authparr['type'] = 2;
            //     $authparr['openid'] = $alipayinfo['openid'];
            //     $authparr['unionid'] = $alipayinfo['unionid'];
            //     $authparr['nickname'] = $alipayinfo['nickname'];
            //     $authparr['headimgurl'] = $alipayinfo['headimgurl'];
            //     IGD('Impower','Login')->PerfectAuthInfo($authparr);
            // }
            session('USER.ucode',$result['data']['c_ucode']);
        }
    }

}
