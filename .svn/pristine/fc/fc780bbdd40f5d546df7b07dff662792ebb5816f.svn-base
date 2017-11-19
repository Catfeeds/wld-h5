<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\ComController;

/**
 * 扫码支付
 */
class Scanpay1Controller extends ComController {

    //初始化
    public function _initialize() {
        vendor('WxPayPubHelper.WxPayPubHelper');
        vendor('Wxshare.wxshare');
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));
        $signPackage = $wxshare->GetSignPackage();
        $this->assign('signPackage',$signPackage);
    }

    //扫码后首页
    public function index() {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
		
		
	
		  //生成支付订单
            $orderinfo['total_fee'] =1;
            $orderinfo['body'] = '涛哥的小店线下订单';
            $orderinfo['out_trade_no'] = time();
            $orderinfo['mch_id'] = "199560304742";
            $orderinfo['sub_openid'] = session('openid');
            $orderinfo['service'] = "pay.weixin.jspay";

                //开始进行签名
             ksort($orderinfo);

            $str = "";
            $num = 0;
            foreach ($orderinfo as $key => $value) {

            if ($num == 0) {
                $str = $key . "=" . $value;
            } else {
                $str = $str . "&" . $key . "=" . $value;
            }
              $num++;
             }

                $str1 = $str . "9f39d96dc89526a3275c99974cc1d040";

                $strmd5 = md5($str1);
                $orderinfo['sign'] = $strmd5;

                $tempstr = json_encode($orderinfo);
                $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";

                $data1 = $this->simple_post($remote_server, $tempstr);

                if ($data1['status'] == 0) {
                    $datainfo['appId'] = $data1['appId'];
                    $datainfo['timeStamp'] = $data1['timeStamp'];
                    $datainfo['signType'] = $data1['signType'];
                    $datainfo['package'] = $data1['package'];
                    $datainfo['nonceStr'] = $data1['nonceStr'];
                    $datainfo['paySign'] = $data1['paySign'];
                    $datainfo['orderid'] = $orderid;
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                }

                $errinfo = Message($data1['status'], $data1['msg']);
                $this->ajaxReturn($errinfo);
		
    }

    //生成扫码支付订单
    public function CreateScanpayOrder()
    {
        $payrule = I('payrule');
    	$parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
    	$parr['money'] = I('money');
    	$parr['acode'] = I('acode');
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $parr['type'] = 2;
            $parr['openid'] = $alipayinfo['openid'];
            $parr['unionid'] = $alipayinfo['unionid'];
            $parr['nickname'] = $alipayinfo['nickname'];
            $parr['headimgurl'] = $alipayinfo['headimgurl'];
        } else {
            $parr['type'] = 1;
            $parr['openid'] = session('openid');
            $parr['unionid'] = session('unionid');
            $parr['nickname'] = session('nickname');
            $parr['headimgurl'] = session('headimgurl');
        }
    	$result = D('Scanpay','Service')->CreateScanpayOrder($parr);

        $userinfo = D('Login','Service')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //生成订单成功，选择微信支付设置对应参数返回
        if ($result['code'] == 0) {
            if ($payrule == 2) {
                //设置支付参数
                $orderid = $result['data'];
                $result = D('Scanpay', 'Service')->FindScanpayOrder($orderid);
                $resultdata = $result['data'];
                if ($resultdata['c_pay_state'] != 0) {
                    $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
                }
                $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
                if ($money <= 0) {
                    $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
                }
				
				$tempmoney = (string) ($money * 100);
                //生成支付订单
                $orderinfo['total_fee'] = $tempmoney;
                $orderinfo['body'] = trim($userdata['c_nickname']) . '的小店线下订单';
                $orderinfo['out_trade_no'] = $orderid;
                $orderinfo['mch_id'] = "199540249260";
                $orderinfo['sub_openid'] = session('openid');
                $orderinfo['service'] = "pay.weixin.jspay";

                //开始进行签名
                ksort($orderinfo);

                $str = "";
                $num = 0;
                foreach ($orderinfo as $key => $value) {

                    if ($num == 0) {
                        $str = $key . "=" . $value;
                    } else {
                        $str = $str . "&" . $key . "=" . $value;
                    }
                    $num++;
                }

                $str1 = $str . "9f39d96dc89526a3275c99974cc1d040";

                $strmd5 = md5($str1);
                $orderinfo['sign'] = $strmd5;

                $tempstr = json_encode($orderinfo);
                $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";

                $data1 = $this->simple_post($remote_server, $tempstr);

                if ($data1['status'] == 0) {
                    $datainfo['appId'] = $data1['appId'];
                    $datainfo['timeStamp'] = $data1['timeStamp'];
                    $datainfo['signType'] = $data1['signType'];
                    $datainfo['package'] = $data1['package'];
                    $datainfo['nonceStr'] = $data1['nonceStr'];
                    $datainfo['paySign'] = $data1['paySign'];
                    $datainfo['orderid'] = $orderid;
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                }

                $errinfo = Message($data1['status'], $data1['msg']);
                $this->ajaxReturn($errinfo);
            }
        }

    	$this->ajaxReturn($result);
    }
	
	  // //简单的curl、post提交数据
    public function simple_post($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );

        $result = curl_exec($ch);
        $resultdata = json_decode($result);
        return objarray_to_array($resultdata);
    }

    //支付成功页面
    public function success()
    {
    	$this->ucode = session('USER.ucode');
    	$orderid = I('orderid');
    	$result = D('Scanpay', 'Service')->FindScanpayOrder($orderid);
        $this->data = $result['data'];

        $userinfo = D('Login','Service')->GetUserByCode($this->data['c_acode']);
        $this->userdata = $userinfo['data'];
        /*口令红包*/
        $parr['ucode'] = session('USER.ucode');
        $wordred = D('Wordred','Service')->GetmyRedInfo($parr);
        $this->worddata = $wordred['data'];
        $this->pid = $wordred['data']['c_id'];

    	$this->show();
    }

    //绑定手机号页面
    public function bindtel()
    {
    	$this->acode = I('acode');
    	$this->show();
    }

    //发送短信验证码
    public function SendVerify()
    {
        $regpass = I('regpass');
        // 生成6位数验证码
        $regnum = rand(100000, 999999);
        $parr['telephone'] = I('phone');
        $parr['type'] = 1000;
        $parr['userid'] = C('TEl_USER');
        $parr['account'] = C('TEl_ACCESS');
        $parr['password'] = C('TEl_PASSWORD');
        $parr['content'] = "【微领地小蜜】尊敬的会员您好，验证码为：".$regnum."有效期120s，为保证您的账号安全，请勿外泄。感谢您的申请！";
        $register = D('Login', 'Service');
        $returndata = $register->SendVerify($parr);
        if ($returndata['code'] == 0) {
            // S($parr['telephone'],$regnum, 3600);
            D('Common', 'Service')->RediesStoreSram($parr['telephone'], $regnum, 3600);
        }
        $this->ajaxReturn($returndata);
    }


    // 绑定手机快捷注册
    public function BindRegister()
    {
        //注册基本信息
        $parr['phone'] = I('phone');
        if (!checkMobile($parr['phone'])) {
            $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        }

        $verifyid = D('Common', 'Service')->Rediesgetucode($parr['phone']);
        if ($verifyid == '') {
            $this->ajaxReturn(Message(1000, "验证码失效，请重新获取验证码！"));
        }

        $regverify = I('verify');
        if ($regverify != $verifyid) {
            $this->ajaxReturn(Message(1001, "验证码错误！"));
        }

        $pwd = rand(100000, 999999);
        $parr['acode'] = I('acode');
        $parr['pwd'] = 'm'.$pwd;
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $parr['type'] = 2;
            $parr['openid'] = $alipayinfo['openid'];
            $parr['nickname'] = $alipayinfo['nickname'];
            $parr['headimgurl'] = $alipayinfo['headimgurl'];
        } else {
            if (!session('openid')) {
                $this->ajaxReturn(Message(1004, "请从微信进入绑定"));
            }
            $parr['type'] = 1;
            $parr['openid'] = session('openid');
            $parr['nickname'] = session('nickname');
            $parr['headimgurl'] = session('headimgurl');
        }

        $result = D('Scanpay','Service')->BindRegister($parr);
        session('USER.ucode',$result['data']);

        //同步绑定扫码订单
        $result1 = D('Scanpay','Service')->BindScanpayOrder($result['data'],$parr['openid']);
        $this->ajaxReturn($result);
    }

    //绑定手机号成功页面
    public function binded()
    {
    	$ucode = I('ucode');
        // $burl = $_SERVER['HTTP_REFERER'];
        $burl = 'Scanpay/bindtel';

        $parr['ucode'] = $ucode;
        $parr['burl'] = $burl;
        $result = D('Scanpay','Service')->BindGift($parr);

        if($result['code'] == 0){
            $this->redpacket = $result['data']['money'];
        }

    	$userinfo = D('Login','Service')->GetUserByCode($ucode);
        $this->userdata = $userinfo['data'];
    	$this->show();
    }

    /*本地测试回调*/
    public function postBack()
    {
        $param['orderid'] = 'n1705241516030261';
        $param['payrule'] = 1;
        $param['actualprice'] = '1000.00';
        $param['thirdpartynum'] = '2017052421001004440234671933';
        $result = D('Scanpay', 'Service')->PayOrder($param);
        $this->ajaxReturn($result);
    }
}
