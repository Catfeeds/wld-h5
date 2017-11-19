<?php

namespace Base\Controller;

use Think\Controller;

/**
 * 支付宝生活号api接口
 */
class AlapiController extends Controller {
    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    private $AopClient;

    //初始化
    public function _initialize() {
        vendor('AntAlipay.Gateway');
        vendor('AntAlipay.Message');
        $this->AopClient = new \AopClient();
    }

    //支付宝验证网关入口
    public function index() {
    	$gw = new \Gateway();
        $HttpRequest = new \HttpRequest();
    	$getresult = $gw->checkAlipay();
    	if ($getresult == "Message") {
    		//处理收到的信息
            $Message = new \Message();
            $biz_content = $HttpRequest->getRequest("biz_content");
            // $Message->response($biz_content);
    	}

    }


    //授权回调
    public function auth()
    {
        //接收保存回调地址
        if(isset($_GET['returnurl'])){
            session('alipay_returnurl',$_GET['returnurl']);
        }

        //接收保存获取信息类型
        if(isset($_GET['auth_type'])){
            session('alipay_auth_type',$_GET['auth_type']);
        }

        $config = C('ALIPAYCONFIG');
        //授权类型,如果需要静默授权请传值过来否则默认为auth_user
        $auth_type = isset($_GET['auth_type'])  ? $_GET['auth_type']  : session('alipay_auth_type');
        $auth_type = $auth_type ? $auth_type  :'auth_user';

        $alipay_appid = $config['app_id'];
        $alipay_auth_url = $config['alipay_auth_url'];
        $auth_url = $config['auth_url'];

        if (!isset($_GET['auth_code'])) {
            $url = $auth_url ."?app_id={$alipay_appid}&scope={$auth_type}&redirect_uri={$alipay_auth_url}&state=1";
            header("Location:$url");exit;
        } else {
            $auth_code = $_GET['auth_code'];
            $http_query = array(
                'app_id' => $alipay_appid,
                'method' => 'alipay.system.oauth.token',
                'charset'=> 'GBK',
                'sign_type'=>'RSA2',
                'timestamp'=>date('Y-m-d H:i:s'),
                'version'=> 1.0,
                'grant_type'=>'authorization_code',
                'code'=>$auth_code
            );
            $user_url = $config['gatewayUrl'];
            $this->AopClient->rsaPrivateKey = $config['merchant_private_key'];
            $sign = $this->AopClient->rsaSign($http_query,'RSA2');
            $http_query['sign'] = $sign;

            $res_arr = $this->simple_post($user_url,$http_query);

            //授权出错 终止授权
            if (isset($res_arr['error_response'])||(isset($res_arr['alipay_system_oauth_token_response'])
                && isset($res_arr['alipay_system_oauth_token_response']['code']))) {
                $err_code = isset($res_arr['error_response'])? $res_arr['error_response']['code'] : $res_arr['alipay_system_oauth_token_response']['code'];
                die("支付宝授权出错【出错码{$err_code}】");
            }

            //静默授权直接拼接数据
            if ($auth_type=='auth_base') {
                $info['openid'] = $res_arr['alipay_system_oauth_token_response']['user_id'];
                $info['nickname'] = '';
                $info['headimgurl'] = '';
                $info['sex'] = '';
            } else {
                $access_token = $res_arr['alipay_system_oauth_token_response']['access_token'];
                $user_query = array(
                    'method'=>'alipay.user.info.share',
                    'timestamp'=> date('Y-m-d H:i:s'),
                    'app_id'=>$alipay_appid,
                    'auth_token'=>$access_token,
                    'charset'=>'GBK',
                    'version'=>'1.0',
                    'sign_type'=>'RSA2',
                    'grant_type'=>'authorization_code',
                    'code'=>$auth_code
                );

                $usersign = $this->AopClient->rsaSign($user_query,'RSA2');
                $user_query['sign'] = $usersign;
                $user_info = $this->simple_post($user_url,$user_query);dump($user_info);
                if( isset($user_info['error_response']) || (isset($user_info['alipay_user_info_share_response'])
                    && isset($user_info['alipay_user_info_share_response']['code']))){
                    $err_code = isset($user_info['error_response'])? $user_info['error_response']['code'] : $user_info['alipay_user_info_share_response']['code'];
                }
                $info = array(
                    'openid'  => $user_info['alipay_user_info_share_response']['user_id'],
                    'nickname'=> $user_info['alipay_user_info_share_response']['nick_name'],
                    'headimgurl' =>$user_info['alipay_user_info_share_response']['avatar'],
                    'sex' =>$user_info['alipay_user_info_share_response']['gender'] == 'F' ? '女' : '男',
                );
            }

            session('alipay_authinfo',$info);
            $redirect_url = decodeurl(session('alipay_returnurl'));
            header("Location:".$redirect_url);exit;
        }
    }

    // //简单的curl、post提交数据
    public function simple_post ($url,$data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 数据为string类型时，超时；
        // 如果是array('param' => $data_string)就没问题。
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = iconv('GBK','UTF-8',$result);
        $resultdata = json_decode($result);
        return objarray_to_array($resultdata);
    }



}
