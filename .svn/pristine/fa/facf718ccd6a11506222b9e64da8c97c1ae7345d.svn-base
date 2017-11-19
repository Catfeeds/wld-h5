<?php

namespace Base\Controller;

use Think\Controller;

/**
 * 扫码支付多个公众号并存收款
 * 微信(支付宝)静态授权
 * 不需要登录的基类
 */
class MoreController extends Controller {
	public $appid = '';
	public $appsecret = '';
	public $wxptsign = '';     //微信公众标识  0长沙微领地网络,1微领地测试

	//公众号APPID集合
    public $appidarr = array(
    	'wxad3908de92b91218',   //长沙微领地网络
    	'wx6a609b3984079e1a',   //微领地测试
    	'wx862dd3d79978e035',   //微领地小蜜
        'wxad3908de92b91218',   //长沙微领地网络
    );

    //公众号APPID对应APPSECRET参数
    public $appid_getkey = array(
        'wxad3908de92b91218'=>array('0','81f4914d308d19527503fe79cc59a83f'),   //长沙微领地网络
    	'wx6a609b3984079e1a'=>array('1','a77367dcb324f9c64afde6153560da51'),   //微领地测试 
    	'wx862dd3d79978e035'=>array('2','64288ef5964529aabd038062b895bdea'),   //微领地小蜜 
        'wxad3908de92b91218'=>array('3','81f4914d308d19527503fe79cc59a83f'),   //长沙微领地网络
    );

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

        if (is_weixin()) {
            //查询数据
            $actname = ACTION_NAME;
            $acode = I('acode');
            if (strpos($_SERVER['HTTP_HOST'], 'iweilingdi.com')) {
                $randnum = 1;
                session('wxptsign',$randnum);
            }
            if (strpos($actname,'payment') !== false) {
                $randnum = 2;
                session('wxptsign',$randnum);
                $wxddsign = session('wxddsign');
                if ($wxddsign != 1) {   //非同一公众号清除相关信息重新授权
                    session('openid',null);
                    session('USER.ucode',null);
                    session('wxddsign',1);
                }
            } else {
                $wxddsign = session('wxddsign');
                if ($wxddsign != 2) {   //非同一公众号清除相关信息重新授权
                    session('openid',null);
                    session('USER.ucode',null);
                    session('wxddsign',2);
                }
            }
            if (!session('wxptsign')) {
                // if (strpos($actname,'index') !== false || strpos($actname,'success') !== false) {
                    // if ($acode == 'wldb1c9f76e86684bed' || $acode == 'xmwde5355c819a63292' || $acode == 'wlda00f07e651a06944') {
                        //查询商户银盛开户情况
                        $parr['ucode'] = $acode;
                        $result = IGD('Ysepay','Scanpay')->PayGetYsedata($parr);
                        $yseinfo = $result['data'];
                        if ($yseinfo['c_openaccount'] == 1) {
                            $randnum = 2;
                        } else {
                            $randnum = 3;
                        }
                        
                    // } else {
                    //     $randnum = 2;
                    // }
                    if ($acode == 'wldb1c9f76e86684bed' || $acode == 'xmwde5355c819a63292' || $acode == 'wlda00f07e651a06944') {
                        $randnum = 3;
                    } 
                // } else {
                //     $randnum = 2;
                // }
                
                if (strpos($actname,'payment') === false) {
                    session('wxddsign',2);
                }

                session('wxptsign',$randnum);
            } else {
                $randnum = session('wxptsign');
            }

            //随机公众号
            // if (session('wxptsign')) {
            //  $randnum = session('wxptsign');
            // } else {
            //  $randnum = rand(0,(count($this->appidarr)-1));
            //  session('wxptsign',$randnum);
            // }
            $this->appid = $this->appidarr[$randnum];  
            $this->appsecret = $this->appid_getkey[$this->appid][1];
            // $this->wxptsign = $this->appid_getkey[$this->appid][0];
            
            if (!session('openid')) {
                if (!session('USER.ucode')) {
                    if (empty($_GET['code'])) {
                        $url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        session('ReturnUrl',$url);
                    }
                    $this->GetOpenid();
                }
            }
        } else if (is_aliApp()) {
            $this->GetAlOpenid();
        }
    }

    //初始化引入微信分享类
    public function _initialize() {
        vendor('Wxshare.wxshare');
        $wxshare = new \Wxshare($this->appid,$this->appsecret);        
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
        $urlObj["appid"] = $this->appid;
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
        $urlObj["appid"] = $this->appid;
        $urlObj["secret"] =$this->appsecret;
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
            // session('wxptsign',$this->wxptsign);

            // 查询用户是否授权
            $parr['unionid'] = $weixininfo['unionid'];
            $parr['openid'] = $weixininfo['openid'];
            $parr['type'] = 1;  //1微信授权
            $result = IGD('Impower','Login')->AuthSeacher($parr);
            if ($result['code'] != 0 && !session('openid')) {
                header("Location:" . session('ReturnUrl'));die;
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
            if ($result['code'] == 0) {
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

}
