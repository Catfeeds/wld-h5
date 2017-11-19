<?php

namespace Base\Controller;

use Think\Controller;

/**
 * 最底层基类
 * APP授权相关方法封装
 * 微信授权相关方法封装
 */
class AuthController extends Controller {

    public function __construct() {
        parent::__construct();
        ob_end_clean(); //清除缓冲区,避免乱码
        header('Content-Type:text/html; charset=utf-8');
        $str = $_GET;
        if (!empty($str['openid'])) {
            if (Verification($str)) {
                $this->CheckApp($str);
            } else {
                $this->userlogin();die();
            }
        }
    }

    //初始化引入微信分享类
    public function _initialize() {
        vendor('Wxshare.wxshare');
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));        
        $signPackage = $wxshare->GetSignPackage();      
        $this->assign('signPackage',$signPackage);
    }

    //跳转用户登录
    function userlogin() {
        $url = encodeurl("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $jumpurl = WEB_HOST . '/index.php/Login/Index/index?url=' . $url;
        header("Location:" . $jumpurl);die();
    }

    //判断是否App进入
    function CheckApp($str) {
        $key = $str['openid'];
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        if ($ucode) {
            IGD('Common', 'Redis')->RediesStoreSram($str['openid'], $ucode,86400);
            session('start');
            session('USER.ucode', $ucode);  //设置session

            $url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            $stra = "";
            foreach ($str as $k => $v) {
                if ($k!= "token" && $k != "time" && $k != '_URL_' && $k != 'openid') {
                    $stra .= $k."=".$v."&";
                }
            }
            if (strlen($stra) > 0) {
                $imgstr = mb_substr($imgstr, 0, strlen($imgstr) - 1,'utf8');
            }
            if (strlen($stra) > 0) {
                $temp = "?" . $stra;
                $url.=$temp;
            }
            header("Location:" . $url);die();
        } else {
            $this->userlogin();die();
        }
    }

    /**
     *  作用：生成可以获得code的url
     *  @param $scope:snsapi_base静态授权,snsapi_userinfo需确认授权
     */
    public function createOauthUrlForCode($redirectUrl,$scope)
    {
        $urlObj["appid"] = C('APPID');
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "$scope";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }


    /**
     *  作用：通过curl向微信提交code，以获取openid
     */
    public function getgetOpenid($code)
    {
        $url = $this->createOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOP_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res,true);
        return $data;
    }

    /**
     *  作用：生成可以获得openid的url
     */
    public function createOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = C('APPID');
        $urlObj["secret"] = C('APPSECRET');
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

}
