<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\MoreController;

/**
 * 扫码支付
 */
class ScanpayController extends MoreController {

    //初始化
    // public function _initialize() {        
    //     vendor('Wxshare.wxshare');
    //     $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));
    //     $signPackage = $wxshare->GetSignPackage();
    //     $this->assign('signPackage',$signPackage);
    // }

    //扫码后首页
    public function index() {
        //查询开户信息
        $parr['ucode'] = I('acode');
        $result = IGD('Ysepay','Scanpay')->PayGetYsedata($parr);
        $yseinfo = $result['data'];
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
         //    if ($yseinfo['c_openaccount'] != 1) {
	        //     $this->display('pubnotice');die;
	        // }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            if ($yseinfo['c_openaccount'] != 1) {
                $this->display('pubnotice');die;
            }
            
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        //查询新商户号
        // if ($userdata['c_isfixed'] == 1) {
        //     $type = 1;
        // } else {
        //     $type = 2;
        // }
        // $result = IGD('Upay','Scanpay')->GetShopMchid($this->acode,$type);
        // if ($result['code'] == 0) {
        //     $this->mch_id = $result['data']['c_merchantid'];
        // }
        // $this->mch_id = '199550164838';
        $this->userdata = $userinfo['data'];

        $this->yseinfo = $yseinfo;
        if ($yseinfo['c_openaccount'] != 1) {
            $this->display('index8');
        } else {
            $this->display('index1');
        }
        // $countwhere['c_type'] = 2;
        // $countwhere['c_ucode'] = $this->acode;
        // $countwhere['c_datetime'] = date('Y-m-d');
        // $countinfo = M('Users_moneydate')->where($countwhere)->find();
        // if (!$countinfo) {
        //     $countwhere['c_updatetime'] = date('Y-m-d H:i:s');
        //     $result = M('Users_moneydate')->add($countwhere);
        // }

        // $db->commit();

        // $this->show();
        // $this->display('papay');
    }

    //生成扫码支付订单
    public function CreateScanpayOrder()
    {        
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            // $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = (string) ($money * 100);

            if (!$mch_id) {     //新商户号不存在启用旧版微信服务商支付
                if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                    vendor('WxPayPubHelper.WxPayPubHelper');
                    $jsApi = new \JsApi_pub();
                    $unifiedOrder = new \UnifiedOrder_pub();
                    // $unifiedOrder->setParameter("sub_appid", C('APPID')); //子商户appid
                    // $unifiedOrder->setParameter("sub_openid", $parr['openid']); //子商户号获取用户openid

                    $unifiedOrder->setParameter("openid", session('openid')); //用户openid
                    $unifiedOrder->setParameter("sub_mch_id", C('SUB_MCHID')); //子商户号
                    $unifiedOrder->setParameter("body", trim($userdata['c_nickname']).'的小店线下订单'); //商品描述
                    $unifiedOrder->setParameter("out_trade_no", $orderid); //商户订单号
                    $unifiedOrder->setParameter("total_fee", $tempmoney); //总金额
                    $unifiedOrder->setParameter("notify_url", C('NOTIFY_URL')); //通知地址
                    $unifiedOrder->setParameter("trade_type", "JSAPI"); //交易类型
                    $unifiedOrder->setParameter("limit_pay", "no_credit"); //上传此参数no_credit--可限制用户不能使用信用卡支付
                    $prepay_id = $unifiedOrder->getPrepayId();
                    if (empty($prepay_id)) {
                        $this->ajaxReturn(Message(2002,'输入错误，起调支付失败'));
                    }

                    $jsApi->setPrepayId($prepay_id);
                    $jsApiParameters = $jsApi->getParameters();
                    $data['jsApiParameters'] = json_decode($jsApiParameters);
                    $data['orderid'] = $orderid;
                    $this->ajaxReturn(MessageInfo(0,'生成订单成功',$data));
                } else if ($payrule == 1) {
                    $this->ajaxReturn(MessageInfo(0,'创建订单成功',$orderid));
                }
            } else {    //新商户号存在启用友收包支付
                if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                    //组装内容信息
                    $orderinfo['total_fee'] = $tempmoney;
                    $orderinfo['body'] = trim($userdata['c_nickname']) . '的小店线下订单';
                    $orderinfo['out_trade_no'] = $orderid;
                    $orderinfo['mch_id'] = $mch_id;
                    $orderinfo['sub_openid'] = $parr['openid'];
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
                    $str1 = $str . C('UPAYSECRET');
                    $strmd5 = md5($str1);
                    $orderinfo['sign'] = $strmd5;
                    $tempstr = json_encode($orderinfo);

                    //请求获取支付参数
                    $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                    $data1 = $this->simple_post($remote_server, $tempstr);
                    if ($data1['status'] == 0 && $data1['status'] != '') {
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
                }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                    //组装内容信息
                    $orderinfo['total_fee'] = $tempmoney;
                    $orderinfo['body'] = trim($userdata['c_nickname']) . '的小店线下订单';
                    $orderinfo['out_trade_no'] = $orderid;
                    $orderinfo['mch_id'] = $mch_id;
                    $orderinfo['buyer_id'] = $parr['openid'];
                    $orderinfo['service'] = "pay.alipay.jspay";

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
                    $str1 = $str . C('UPAYSECRET');
                    $strmd5 = md5($str1);
                    $orderinfo['sign'] = $strmd5;
                    $tempstr = json_encode($orderinfo);

                    //请求获取支付参数
                    $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                    $data1 = $this->simple_post($remote_server, $tempstr);
                    if ($data1['status'] == 0) {
                        $datainfo['tradeNO'] = $data1['tradeNO'];
                        $datainfo['orderid'] = $orderid;
                        $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                    }

                    $errinfo = Message($data1['status'], $data1['msg']);
                    $this->ajaxReturn($errinfo);
                }
            }
        }

        $this->ajaxReturn($result);
    }

    //支付成功页面
    public function success()
    {
        $this->ucode = session('USER.ucode');
        $orderid = I('orderid');
        $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
        $this->data = $result['data'];

        $userinfo = IGD('Login','Login')->GetUserByCode($this->data['c_acode']);
        $this->userdata = $userinfo['data'];

        if ($this->data['c_pay_state'] != 1) {
            $jumpurl = GetHost(1) . '/index.php/Order/Scanpay/index?acode='.$this->data['c_acode']."&deskid=".$this->data['c_deskid'];
            header("Location:" . $jumpurl);die();
        }
        $this->show();
    }

    //银盛支付中转页面
    public function paysuccess()
    {
        $parr['orderid'] = I('orderid');
        $parr['payrule'] = I('payrule');
        $result = IGD('Ysepay','Scanpay')->ChangeOrderPay($parr);

        $this->ucode = session('USER.ucode');
        $orderid = I('orderid');
        $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
        $this->data = $result['data'];

        $userinfo = IGD('Login','Login')->GetUserByCode($this->data['c_acode']);
        $this->userdata = $userinfo['data'];

        $this->display('success');
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
        $register = IGD('Login', 'Login');
        $returndata = $register->SendVerify($parr);
        if ($returndata['code'] == 0) {
            // S($parr['telephone'],$regnum, 3600);
            IGD('Common', 'Redis')->RediesStoreSram($parr['telephone'], $regnum, 3600);
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

        $verifyid = IGD('Common', 'Redis')->Rediesgetucode($parr['phone']);
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

        $result = IGD('Scanpay','Scanpay')->BindRegister($parr);
        session('USER.ucode',$result['data']);

        //同步绑定扫码订单
        // $result1 = IGD('Scanpay','Scanpay')->BindScanpayOrder($result['data'],$parr['openid']);
        $this->ajaxReturn($result);
    }

    //绑定手机号成功页面
    public function binded()
    {
        $ucode = I('ucode');
        // $burl = $_SERVER['HTTP_REFERER'];
        $burl = 'Scanpay/bindtel';
        if (empty($ucode)) {
            $ucode = session('USER.ucode');
        }

        $parr['ucode'] = $ucode;
        $parr['burl'] = $burl;
        $result = IGD('Scanpay','Scanpay')->BindGift($parr);

        if($result['code'] == 0){
            $this->redpacket = $result['data']['money'];
        }

        $userinfo = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $userinfo['data'];
        $this->show();
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

    /*本地测试回调*/
    public function postBack()
    {
        $param['orderid'] = I('orderid');
        $param['payrule'] = I('py');
        $param['actualprice'] = I('price');
        $param['thirdpartynum'] = I('td');
        $result = IGD('Scanpay','Scanpay')->PayOrder($param);
        $this->ajaxReturn($result);
    }

    //生成银盛扫码支付订单
    public function CreateScanpayOrder1()
    {
        Vendor('Ysepay.Yse_pay');
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家开户信息
        $parra['ucode'] = I('acode');
        $result = IGD('Ysepay','Scanpay')->PayGetYsedata($parra);
        if ($result['code'] != 0) {
            $this->ajaxReturn(Message(3000,'资料审核中，暂不能支付'));
        }
        $yseinfo = $result['data'];

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }

            $tempmoney = intval(bcmul($money,100,2));
            $tempproductname = subtext(trim($userdata['c_nickname']).'的小店线下订单',12);
            $tempproductname = str_replace("\r", "", $tempproductname);
            $tempproductname = str_replace("\n", "", $tempproductname);
            $tempproductname = str_replace("&", "", $tempproductname);
            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                //组装内容信息
                $pay = new \Yse_pay();
                $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/respond_notify";
                $params['out_trade_no'] = $orderid;
                $params['total_amount'] = $money;
                $params['subject'] = $tempproductname;
                $params['seller_id'] = $yseinfo['c_username']; // 收款方银盛支付用户名
                $params['seller_name'] = $yseinfo['c_person']; // 授权方银盛支付客户名
                // $params['seller_id'] = "wld17375717292";
                // $params['seller_name'] = "长沙微领地网络科技有限公司";
                $params['sub_openid'] = $parr['openid'];//$parr['openid'];//

                $data = $pay->weixin_pay($params);
                $result = $pay->curl_https_appid($data);
                // dump($result);die;
                $data1 =json_decode($result['ysepay_online_jsapi_pay_response']['jsapi_pay_info'],true);
                $datainfo['appId'] = $data1['appId'];
                $datainfo['timeStamp'] = $data1['timeStamp'];
                $datainfo['signType'] = $data1['signType'];
                $datainfo['package'] = $data1['package'];
                $datainfo['nonceStr'] = $data1['nonceStr'];
                $datainfo['paySign'] = $data1['paySign'];
                $datainfo['orderid'] = $orderid;
                if(empty($data1['appId']) || empty($data1['timeStamp']) || empty($data1['signType']) || empty($data1['package'])){
                    // $this->ajaxReturn(Message(3001,'验证失败'));
                    $this->ajaxReturn(Message(3001,$result['ysepay_online_jsapi_pay_response']['sub_msg']));
                }else{
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                }
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                if (is_aliApp()) {
                    $pay = new \Yse_pay();
                    $parr['partner_id'] ="wld17375717292";
                    $parr['notify_url'] = GetHost(1)."/index.php/Order/Alipay/respond_alipay_notify";
                    $parr['return_url'] = GetHost(1)."/index.php/Order/Scanpay/success?orderid=".$orderid;
                    $parr['out_trade_no'] = $orderid;             //订单号,自行生成;
                    $parr['subject'] = $tempproductname;
                    $parr['total_amount'] = $money;          //交易金额
                    $parr['seller_id'] = $yseinfo['c_username']; // 收款方银盛支付用户名
                    $parr['seller_name'] = $yseinfo['c_person']; // 授权方银盛支付客户名
                    // $parr['seller_id'] = "wld17375717292";
                    // $parr['seller_name'] = "长沙微领地网络科技有限公司";
                    $data =$pay->alipay_H5($parr);

                    $action = "https://openapi.ysepay.com/gateway.do";
                    $def_url = "<form style='text-align:center;' method=post action='" . $action . "' id='alipayform'>";
                    while ($param = each($data)) {
                        $def_url .= "<input type = 'hidden' id='" . $param['key'] . "' name='" . $param['key'] . "' value='" . $param['value'] . "' />";
                    }
                    // $def_url .= "<input style='font-size: 24px;color: red;' type=submit value='点击提交' " . @$GLOBALS['_LANG']['pay_button'] . "'>";
                    $def_url .= "</form>";

                    $this->ajaxReturn(MessageInfo(0,'创建订单成功',$def_url));
                }
            }
        }

        $this->ajaxReturn(MessageInfo(0,'创建订单成功',$orderid));
    }

    //扫码后首页
    public function index1() {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            // $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        //查询新商户号
        if ($userdata['c_isfixed'] == 1) {
            $type = 1;
        } else {
            $type = 2;
        }
        $result = IGD('Upay','Scanpay')->GetShopMchid($this->acode,$type);
        if ($result['code'] == 0) {
            $this->mch_id = $result['data']['c_merchantid'];
        }
        $this->mch_id = '199550164838';
        $this->userdata = $userinfo['data'];

        $this->show();
    }

    //扫码后首页
    public function index2() {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        //查询新商户号
        if ($userdata['c_isfixed'] == 1) {
            $type = 1;
        } else {
            $type = 2;
        }
        // $result = IGD('Upay','Scanpay')->GetShopMchid($this->acode,$type);
        // if ($result['code'] == 0) {
        //     $this->mch_id = $result['data']['c_merchantid'];
        // }
        // $this->mch_id = '199550164838';
        $this->userdata = $userinfo['data'];

        //新增操作写入分库记录
        // $db = M('');
        // $db->startTrans('1');

        // //新增临时写入统计表
        // $countwhere['c_sign'] = 1;
        // $countwhere['c_type'] = 2;
        // $countwhere['c_ucode'] = $this->acode;
        // $countwhere['c_datetime'] = date('Y-m-d');
        // $countinfo = M('Users_moneydate')->where($countwhere)->find();
        // if (!$countinfo) {
        //     $countwhere['c_updatetime'] = date('Y-m-d H:i:s');
        //     $result = M('Users_moneydate')->add($countwhere);
        // }

        // $db->commit();

        $this->show();
    }

    //生成扫码支付订单
    public function CreateScanpayOrder2()
    {
    	vendor('WxPayPubHelper.WxPayPubHelper');
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            //$mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = (string) ($money * 100);

            if (!$mch_id) {     //新商户号不存在启用旧版微信服务商支付
                if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                    $jsApi = new \JsApi_pub();
                    $unifiedOrder = new \UnifiedOrder_pub();
                    // $unifiedOrder->setParameter("sub_appid", C('APPID')); //子商户appid
                    // $unifiedOrder->setParameter("sub_openid", $parr['openid']); //子商户号获取用户openid

                    $unifiedOrder->setParameter("openid", session('openid')); //用户openid
                    $unifiedOrder->setParameter("sub_mch_id", C('SUB_MCHID')); //子商户号
                    $unifiedOrder->setParameter("body", trim($userdata['c_nickname']).'的小店线下订单'); //商品描述
                    $unifiedOrder->setParameter("out_trade_no", $orderid); //商户订单号
                    $unifiedOrder->setParameter("total_fee", $tempmoney); //总金额
                    $unifiedOrder->setParameter("notify_url", C('NOTIFY_URL')); //通知地址
                    $unifiedOrder->setParameter("trade_type", "JSAPI"); //交易类型
                    $unifiedOrder->setParameter("limit_pay", "no_credit"); //上传此参数no_credit--可限制用户不能使用信用卡支付
                    $prepay_id = $unifiedOrder->getPrepayId();
                    if (empty($prepay_id)) {
                        $this->ajaxReturn(Message(2002,'输入错误，起调支付失败'));
                    }

                    $jsApi->setPrepayId($prepay_id);
                    $jsApiParameters = $jsApi->getParameters();
                    $data['jsApiParameters'] = json_decode($jsApiParameters);
                    $data['orderid'] = $orderid;
                    $this->ajaxReturn(MessageInfo(0,'生成订单成功',$data));
                } else if ($payrule == 1) {
                    $this->ajaxReturn(MessageInfo(0,'创建订单成功',$orderid));
                }
            } else {    //新商户号存在启用友收包支付
                if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                    //组装内容信息
                    $orderinfo['total_fee'] = $tempmoney;
                    $orderinfo['body'] = trim($userdata['c_nickname']) . '的小店线下订单';
                    $orderinfo['out_trade_no'] = $orderid;
                    $orderinfo['mch_id'] = $mch_id;
                    $orderinfo['sub_openid'] = $parr['openid'];
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
                    $str1 = $str . C('UPAYSECRET');
                    $strmd5 = md5($str1);
                    $orderinfo['sign'] = $strmd5;
                    $tempstr = json_encode($orderinfo);

                    //请求获取支付参数
                    $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                    $data1 = $this->simple_post($remote_server, $tempstr);
                    if ($data1['status'] == 0 && $data1['status'] != '') {
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
                }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                    //组装内容信息
                    $orderinfo['total_fee'] = $tempmoney;
                    $orderinfo['body'] = trim($userdata['c_nickname']) . '的小店线下订单';
                    $orderinfo['out_trade_no'] = $orderid;
                    $orderinfo['mch_id'] = $mch_id;
                    $orderinfo['buyer_id'] = $parr['openid'];
                    $orderinfo['service'] = "pay.alipay.jspay";

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
                    $str1 = $str . C('UPAYSECRET');
                    $strmd5 = md5($str1);
                    $orderinfo['sign'] = $strmd5;
                    $tempstr = json_encode($orderinfo);

                    //请求获取支付参数
                    $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                    $data1 = $this->simple_post($remote_server, $tempstr);
                    if ($data1['status'] == 0) {
                        $datainfo['tradeNO'] = $data1['tradeNO'];
                        $datainfo['orderid'] = $orderid;
                        $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                    }

                    $errinfo = Message($data1['status'], $data1['msg']);
                    $this->ajaxReturn($errinfo);
                }
            }
        }

        $this->ajaxReturn($result);
    }


    //友收宝支付扫码后首页
    public function index3() {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        //查询新商户号
        if ($userdata['c_isfixed'] == 1) {
            $type = 1;
        } else {
            $type = 2;
        }
        // $result = IGD('Upay','Scanpay')->GetShopMchid($this->acode,$type);
        // if ($result['code'] == 0) {
        //     $this->mch_id = $result['data']['c_merchantid'];
        // }
        $this->mch_id = '199550164838';
        $this->userdata = $userinfo['data'];

        //新增操作写入分库记录
        // $db = M('');
        // $db->startTrans('1');

        // //新增临时写入统计表
        // $countwhere['c_sign'] = 1;
        // $countwhere['c_type'] = 2;
        // $countwhere['c_ucode'] = $this->acode;
        // $countwhere['c_datetime'] = date('Y-m-d');
        // $countinfo = M('Users_moneydate')->where($countwhere)->find();
        // if (!$countinfo) {
        //     $countwhere['c_updatetime'] = date('Y-m-d H:i:s');
        //     $result = M('Users_moneydate')->add($countwhere);
        // }

        // $db->commit();

        $this->show();
    }

    //友收宝支付生成扫码支付订单
    public function CreateScanpayOrder3()
    {        
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            $mch_id =  '199510303680';    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = (string) ($money * 100);

            if (!$mch_id) {     //新商户号不存在启用旧版微信服务商支付
                if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                    vendor('WxPayPubHelper.WxPayPubHelper');
                    $jsApi = new \JsApi_pub();
                    $unifiedOrder = new \UnifiedOrder_pub();
                    // $unifiedOrder->setParameter("sub_appid", C('APPID')); //子商户appid
                    // $unifiedOrder->setParameter("sub_openid", $parr['openid']); //子商户号获取用户openid

                    $unifiedOrder->setParameter("openid", session('openid')); //用户openid
                    $unifiedOrder->setParameter("sub_mch_id", C('SUB_MCHID')); //子商户号
                    $unifiedOrder->setParameter("body", trim($userdata['c_nickname']).'的小店线下订单'); //商品描述
                    $unifiedOrder->setParameter("out_trade_no", $orderid); //商户订单号
                    $unifiedOrder->setParameter("total_fee", $tempmoney); //总金额
                    $unifiedOrder->setParameter("notify_url", C('NOTIFY_URL')); //通知地址
                    $unifiedOrder->setParameter("trade_type", "JSAPI"); //交易类型
                    $unifiedOrder->setParameter("limit_pay", "no_credit"); //上传此参数no_credit--可限制用户不能使用信用卡支付
                    $prepay_id = $unifiedOrder->getPrepayId();
                    if (empty($prepay_id)) {
                        $this->ajaxReturn(Message(2002,'输入错误，起调支付失败'));
                    }

                    $jsApi->setPrepayId($prepay_id);
                    $jsApiParameters = $jsApi->getParameters();
                    $data['jsApiParameters'] = json_decode($jsApiParameters);
                    $data['orderid'] = $orderid;
                    $this->ajaxReturn(MessageInfo(0,'生成订单成功',$data));
                } else if ($payrule == 1) {
                    $this->ajaxReturn(MessageInfo(0,'创建订单成功',$orderid));
                }
            } else {    //新商户号存在启用友收包支付
                if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                    //组装内容信息
                    $orderinfo['total_fee'] = $tempmoney;
                    $orderinfo['body'] = trim($userdata['c_nickname']) . '的小店线下订单';
                    $orderinfo['out_trade_no'] = $orderid;
                    $orderinfo['mch_id'] = $mch_id;
                    $orderinfo['sub_openid'] = $parr['openid'];
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
                    $str1 = $str . C('UPAYSECRET');
                    $strmd5 = md5($str1);
                    $orderinfo['sign'] = $strmd5;
                    $tempstr = json_encode($orderinfo);

                    //请求获取支付参数
                    $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                    $data1 = $this->simple_post($remote_server, $tempstr);
                    if ($data1['status'] == 0 && $data1['status'] != '') {
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
                }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                    //组装内容信息
                    $orderinfo['total_fee'] = $tempmoney;
                    $orderinfo['body'] = trim($userdata['c_nickname']) . '的小店线下订单';
                    $orderinfo['out_trade_no'] = $orderid;
                    $orderinfo['mch_id'] = $mch_id;
                    $orderinfo['buyer_id'] = $parr['openid'];
                    $orderinfo['service'] = "pay.alipay.jspay";

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
                    $str1 = $str . C('UPAYSECRET');
                    $strmd5 = md5($str1);
                    $orderinfo['sign'] = $strmd5;
                    $tempstr = json_encode($orderinfo);

                    //请求获取支付参数
                    $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                    $data1 = $this->simple_post($remote_server, $tempstr);
                    if ($data1['status'] == 0) {
                        $datainfo['tradeNO'] = $data1['tradeNO'];
                        $datainfo['orderid'] = $orderid;
                        $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                    }

                    $errinfo = Message($data1['status'], $data1['msg']);
                    $this->ajaxReturn($errinfo);
                }
            }
        }

        $this->ajaxReturn($result);
    }

    //扫码后首页
    public function papay() {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            // $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        //查询新商户号
        if ($userdata['c_isfixed'] == 1) {
            $type = 1;
        } else {
            $type = 2;
        }
        // $result = IGD('Upay','Scanpay')->GetShopMchid($this->acode,$type);
        // if ($result['code'] == 0) {
        //     $this->mch_id = $result['data']['c_merchantid'];
        // }
        // $this->mch_id = '199550164838';
        $this->userdata = $userinfo['data'];

        $this->show();
    }

    //平安银行支付
    public function index4()
    {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            // $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        $this->userdata = $userinfo['data'];
        $this->display();
    }
    
    //生成扫码支付订单
    public function CreateScanpayOrder4()
    {   
        vendor('Papay.webApp');
        $open_info = C('PAOPEN_INFO');
        $oprand = rand(0,(count($open_info)-1));
        $open_id = $open_info[$oprand]['PAOPEN_ID'];
        $open_key = $open_info[$oprand]['PAOPEN_KEY'];

        // $open_id = 'ea615d406b466c331fe09affa914c45a';
        // $open_key = '9e961cfc701dc361de0da2f6ea9b8ed9';
        $webApp = new \webApp($open_id,$open_key);

        $parr['open_id'] = $open_id;
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            // $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = (string) ($money * 100);

            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $pmt_tag = 'Weixin';
                $notify_url = GetHost(1).'/index.php/Order/Weixin/papaynotify';
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                $pmt_tag = 'Alipay2';
                $notify_url = GetHost(1).'/index.php/Order/Alipay/papaynotify';
            } else {
                $this->ajaxReturn(Message(2002,'非法支付'));
            }
            
            $data['open_id'] = $parr['openid'];   //用户唯一标识
            $data['timestamp'] = time();   //时间戳
            $data['out_no'] = $orderid; //订单号
            $data['pmt_tag'] = $pmt_tag;    //支付标识 Weixin,Alipay2
            $data['pmt_name'] = trim($userdata['c_nickname']);    //自定义付款方式名 
            $data['ord_name'] = trim($userdata['c_nickname']) . '的小店线下订单';    //订单描述
            $data['original_amount'] = $tempmoney;
            $data['trade_amount'] = $tempmoney;   //实际交易金额(分为单位)   
            $data['notify_url'] = $notify_url;
            $data['jump_url'] = GetHost(1).'/index.php/Order/Scanpay/success?orderid='.$orderid;
            $result = $webApp->api("payorder",$data); 

            if ($result['errcode'] != 0) {
                $this->ajaxReturn(Message(3002,$result['msg']));
            }

            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $data1 = objarray_to_array(json_decode($result['data']['trade_result']));
                $prepay_id = $data1['prepay_id'];
                $datainfo['appId'] = $data1['appid'];
                $datainfo['timeStamp'] = $result['timestamp'];
                $datainfo['signType'] = 'MD5';
                $datainfo['package'] = "prepay_id=$prepay_id";
                $datainfo['nonceStr'] = $data1['nonce_str'];
                $datainfo['paySign'] = $data1['sign'];
                $datainfo['orderid'] = $orderid;
                $datainfo['jsapi_pay_url'] = $result['data']['jsapi_pay_url'];
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                $data1 = objarray_to_array(json_decode($result['data']['trade_result']));
                $datainfo['tradeNO'] = $data1['alipay_trade_precreate_response']['out_trade_no'];
                $datainfo['orderid'] = $orderid;
                $datainfo['jsapi_pay_url'] = $result['data']['jsapi_pay_url'];
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
            }            
        }

        $this->ajaxReturn($result);
    }

    //测试平安支付生成扫码支付订单
    public function CreateScanpayOrder5()
    {   
        vendor('Papay.webApp');
        $open_info = C('PAOPEN_INFO');
        $oprand = rand(0,(count($open_info)-1));
        // $open_id = $open_info[$oprand]['PAOPEN_ID'];
        // $open_key = $open_info[$oprand]['PAOPEN_KEY'];

        $open_id = 'ea615d406b466c331fe09affa914c45a';
        $open_key = '9e961cfc701dc361de0da2f6ea9b8ed9';
        $webApp = new \webApp($open_id,$open_key);

        $parr['open_id'] = $open_id;
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            // $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = (string) ($money * 100);

            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $pmt_tag = 'Weixin';
                $notify_url = GetHost(1).'/index.php/Order/Weixin/papaynotify';
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                $pmt_tag = 'Alipay2';
                $notify_url = GetHost(1).'/index.php/Order/Alipay/papaynotify';
            } else {
                $this->ajaxReturn(Message(2002,'非法支付'));
            }
            
            $data['open_id'] = $parr['openid'];   //用户唯一标识
            $data['timestamp'] = time();   //时间戳
            $data['out_no'] = $orderid; //订单号
            $data['pmt_tag'] = $pmt_tag;    //支付标识 Weixin,Alipay2
            $data['pmt_name'] = trim($userdata['c_nickname']);    //自定义付款方式名 
            $data['ord_name'] = trim($userdata['c_nickname']) . '的小店线下订单';    //订单描述
            $data['original_amount'] = $tempmoney;
            $data['trade_amount'] = $tempmoney;   //实际交易金额(分为单位)   
            $data['notify_url'] = $notify_url;
            $data['jump_url'] = GetHost(1).'/index.php/Order/Scanpay/success?orderid='.$orderid;
            $result = $webApp->api("payorder",$data); 

            if ($result['errcode'] != 0) {
                $this->ajaxReturn(Message(3002,$result['msg']));
            }

            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $data1 = objarray_to_array(json_decode($result['data']['trade_result']));
                $prepay_id = $data1['prepay_id'];
                $datainfo['appId'] = $data1['appid'];
                $datainfo['timeStamp'] = $result['timestamp'];
                $datainfo['signType'] = 'MD5';
                $datainfo['package'] = "prepay_id=$prepay_id";
                $datainfo['nonceStr'] = $data1['nonce_str'];
                $datainfo['paySign'] = $data1['sign'];
                $datainfo['orderid'] = $orderid;
                $datainfo['jsapi_pay_url'] = $result['data']['jsapi_pay_url'];
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                $data1 = objarray_to_array(json_decode($result['data']['trade_result']));
                $datainfo['tradeNO'] = $data1['alipay_trade_precreate_response']['out_trade_no'];
                $datainfo['orderid'] = $orderid;
                $datainfo['jsapi_pay_url'] = $result['data']['jsapi_pay_url'];
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
            }            
        }

        $this->ajaxReturn($result);
    }    

    /*确认支付*/
    public function payment() {
        if (is_weixin()) {
            if (!session('openid')) {
                $this->GetOpenid();die;
            }
        } else if (is_aliApp()) {
            if (!session('alipay_authinfo')) {
                $this->GetAlOpenid();die;
            }
        }
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        if (substr($parr['orderid'],0,1) == 's') {    //旧版小蜜商城订单
            $payment = IGD('Supplyorder','Agorder');
        } else if (substr($parr['orderid'],0,1) == 'g') {  //拼团订单
            $payment = IGD('Groupbuy','Newact');
        } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
            $payment = IGD('Seckill','Newact');
        } else {
            $payment = IGD('Order', 'Order');
        }
        $result = $payment->GetPayorderinfo($parr);
        $orderinfo = $result['data'];
        if ($orderinfo['c_pay_state']==1) {
            $this->redirect('achieve',array('orderid' => $parr['orderid']));die;
        }

        //面对面收货提交用户位置
        if ($orderinfo['c_delivery'] == 2) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
            $localinfo = IGD('Order','Order')->LocalShop($parr);
        }

        $this->orderdata = $orderinfo;
        $this->proinfo = $orderinfo['detail'];
        $sum = bcadd($result['data']['c_total_price'], $result['data']['c_free'], 2);
        $shenyu = bcsub($sum, $result['data']['c_actual_price'], 2);
        $shenyu_1 = bcsub($shenyu, $result['data']['c_bmoney'], 2);
        $this->actual = $shenyu_1;
        $this->orderid = I('orderid');

        /*获取可用余额*/
        $parru['ucode'] = session('USER.ucode');
        $balance = IGD('User', 'User');
        $b_money = $balance->GetUserMoney($parru);
        $this->cmoney = $b_money['data']['c_money'];

        $parra['ucode'] = $orderinfo['c_acode'];
        $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
        $yseinfo = $result['data'];
        $this->display();
    }

    //中信银行支付
    public function index6()
    {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            // $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        $this->userdata = $userinfo['data'];
        $this->display('index7');
    }

    //中信银行支付生成扫码支付订单
    public function CreateScanpayOrder6()
    {  
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            // $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = (string) ($money * 100);

            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                vendor('zxinpay.weixin.Request');
                $Request = new \Request();
                $params['method'] = 'submitOrderInfo';
                $params['is_raw'] = 1;
                $params['limit_credit_pay'] = 1;
                $params['body'] = trim($userdata['c_nickname']).'的小店线下订单';  //商品描述
                $params['mch_create_ip'] = get_client_ip();  //客户端ip地址
                $params['out_trade_no'] = $orderid;  //商户单号
                $params['sub_appid'] = C('APPID');
                $params['sub_openid'] = $parr['openid'];     //用户openid  $parr['openid']
                $params['total_fee'] = $tempmoney;   //总金 额
                $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/zxinnotify";
                //$params['callback_url'] = GetHost(1).'/index.php/Order/Scanpay/success?orderid='.$orderid;
                $result = $Request->submitOrderInfo($params);
                $data = objarray_to_array(json_decode($result));

                $data1 = objarray_to_array(json_decode($data['pay_info']));
                if ($data1) {
                    $prepay_id = $data1['prepay_id'];
                    $datainfo['appId'] = $data1['appId'];
                    $datainfo['timeStamp'] = $data1['timeStamp'];
                    $datainfo['signType'] = $data1['signType'];
                    $datainfo['package'] = $data1['package'];
                    $datainfo['nonceStr'] = $data1['nonceStr'];
                    $datainfo['paySign'] = $data1['paySign'];
                    $datainfo['orderid'] = $orderid;
                    $datainfo['jsapi_pay_url'] = $result['data']['jsapi_pay_url'];
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                } else {
                    $this->ajaxReturn(MessageInfo(3002, $data['msg']));
                }
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                if (is_aliApp()) {
                    vendor('zxinpay.alipay.Request');
                    $Request = new \Request();
                    $params['method'] = 'submitOrderInfo';
                    $params['buyer_id'] = $parr['openid'];     //用户openid  $parr['openid']
                    $params['limit_credit_pay'] = 1;
                    $params['body'] = trim($userdata['c_nickname']).'的小店线下订单';  //商品描述
                    $params['mch_create_ip'] = get_client_ip();  //客户端ip地址
                    $params['out_trade_no'] = $orderid;  //商户单号
                    $params['total_fee'] = $tempmoney;   //总金 额
                    $params['notify_url'] = GetHost(1)."/index.php/Order/Alipay/zxinnotify";
                    //$params['callback_url'] = GetHost(1).'/index.php/Order/Scanpay/success?orderid='.$orderid;
                    $result = $Request->submitOrderInfo($params);
                    $data = objarray_to_array(json_decode($result));

                    $data1 = objarray_to_array(json_decode($data['pay_info']));
                    if ($data1) {
                        $datainfo['tradeNO'] = $data1['tradeNO'];
                        $datainfo['orderid'] = $orderid;
                        $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                    } else {
                        $this->ajaxReturn(MessageInfo(3002, $data['msg']));
                    }
                }
            }
        }
        $this->ajaxReturn(MessageInfo(0,'生成订单成功',$orderid));
    }

    //中信银行支付生成扫码支付订单测试
    public function CreateScanpayOrder7()
    {  
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            // $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = (string) ($money * 100);

            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                vendor('zxinpay.weixin.Request');
                $Request = new \Request('102593849206');
                $params['method'] = 'submitOrderInfo';
                $params['is_raw'] = 1;
                $params['limit_credit_pay'] = 1;
                $params['body'] = trim($userdata['c_nickname']).'的小店线下订单';  //商品描述
                $params['mch_create_ip'] = get_client_ip();  //客户端ip地址
                $params['out_trade_no'] = $orderid;  //商户单号
                $params['sub_appid'] = C('APPID');
                $params['sub_openid'] = $parr['openid'];     //用户openid  $parr['openid']
                $params['total_fee'] = $tempmoney;   //总金 额
                $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/zxinnotify";
                //$params['callback_url'] = GetHost(1).'/index.php/Order/Scanpay/success?orderid='.$orderid;
                $result = $Request->submitOrderInfo($params);
                $data = objarray_to_array(json_decode($result));

                $data1 = objarray_to_array(json_decode($data['pay_info']));
                if ($data1) {
                    $prepay_id = $data1['prepay_id'];
                    $datainfo['appId'] = $data1['appId'];
                    $datainfo['timeStamp'] = $data1['timeStamp'];
                    $datainfo['signType'] = $data1['signType'];
                    $datainfo['package'] = $data1['package'];
                    $datainfo['nonceStr'] = $data1['nonceStr'];
                    $datainfo['paySign'] = $data1['paySign'];
                    $datainfo['orderid'] = $orderid;
                    $datainfo['jsapi_pay_url'] = $result['data']['jsapi_pay_url'];
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                } else {
                    $this->ajaxReturn(MessageInfo(3002, $data['msg']));
                }
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                if (is_aliApp()) {
                    vendor('zxinpay.alipay.Request');
                    $Request = new \Request('102553849209');
                    $params['method'] = 'submitOrderInfo';
                    $params['buyer_id'] = $parr['openid'];     //用户openid  $parr['openid']
                    $params['limit_credit_pay'] = 1;
                    $params['body'] = trim($userdata['c_nickname']).'的小店线下订单';  //商品描述
                    $params['mch_create_ip'] = get_client_ip();  //客户端ip地址
                    $params['out_trade_no'] = $orderid;  //商户单号
                    $params['total_fee'] = $tempmoney;   //总金 额
                    $params['notify_url'] = GetHost(1)."/index.php/Order/Alipay/zxinnotify";
                    //$params['callback_url'] = GetHost(1).'/index.php/Order/Scanpay/success?orderid='.$orderid;
                    $result = $Request->submitOrderInfo($params);
                    $data = objarray_to_array(json_decode($result));

                    $data1 = objarray_to_array(json_decode($data['pay_info']));
                    if ($data1) {
                        $datainfo['tradeNO'] = $data1['tradeNO'];
                        $datainfo['orderid'] = $orderid;
                        $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                    } else {
                        $this->ajaxReturn(MessageInfo(3002, $data['msg']));
                    }
                }
            }
        }
        $this->ajaxReturn(MessageInfo(0,'生成订单成功',$orderid));
    }

    //微众支付
    public function index8()
    {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            // $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        $this->userdata = $userinfo['data'];
        $this->display();
    }

    //微众支付生成扫码支付订单
    public function CreateScanpayOrder8()
    {  
        header('Content-type: text/json');
        $wxptsign = session('wxptsign');
        vendor('Beecldpay.Pay'.$wxptsign);
        $paystr = 'Pay'.$wxptsign;
        $Pay = new $paystr();
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            // $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = intval(bcmul($money,100,2));
            $tempproductname = subtext(trim($userdata['c_nickname']).'的小店线下订单',12);
            $tempproductname = str_replace("\r", "", $tempproductname);
            $tempproductname = str_replace("\n", "", $tempproductname);
            $tempproductname = str_replace("...", "", $tempproductname);
            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $params['title'] = $tempproductname;  //商品描述
                $params['bill_no'] = $orderid;  //商户单号
                $params['openid'] = $parr['openid'];     //用户openid  $parr['openid']
                $params['total_fee'] = $tempmoney;   //总金 额
                $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/weizhongnotify";
                $params['type'] = 'WX_JSAPI';
                $params['wxptsign'] = session('wxptsign');
                $result = $Pay->createorderinfo($params);
                $data1 = objarray_to_array($result);
                // if ($parr['acode'] == 'wldb1c9f76e86684bed' || $parr['acode'] == 'xmwde5355c819a63292' || $parr['acode'] == 'wlda00f07e651a06944') {
                //     dump($params); 
                //     dump($data1);die;
                // } 

                if ($data1['result_msg'] == 'OK') {
                    $datainfo['appId'] = $data1['app_id'];
                    $datainfo['timeStamp'] = $data1['timestamp'];
                    $datainfo['signType'] = $data1['sign_type'];
                    $datainfo['package'] = $data1['package'];
                    $datainfo['nonceStr'] = $data1['nonce_str'];
                    $datainfo['paySign'] = $data1['pay_sign'];
                    $datainfo['orderid'] = $orderid;
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                } else {
                    $this->ajaxReturn(MessageInfo(3002, $data1['result_msg']));
                }
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                if (is_aliApp()) {
                    $params['title'] = $tempproductname;  //商品描述
                    $params['bill_no'] = $orderid;  //商户单号
                    $params['openid'] = $parr['openid'];     //用户openid  $parr['openid']
                    $params['total_fee'] = $tempmoney;   //总金 额
                    $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/weizhongnotify";
                    $params['type'] = 'ALI_WEB';
                    $result = $Pay->createorderinfo($params);
                    $data = objarray_to_array($result);

                    $data1 = objarray_to_array(json_decode($data['pay_info']));
                    if ($data1) {
                        $datainfo['tradeNO'] = $data1['tradeNO'];
                        $datainfo['orderid'] = $orderid;
                        $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                    } else {
                        $this->ajaxReturn(MessageInfo(3002, $data['msg']));
                    }
                }
            }
        }
        $this->ajaxReturn(MessageInfo(0,'生成订单成功',$orderid));
    }

    //微众支付中转页面
    public function wzsuccess()
    {
        // $wxptsign = session('wxptsign');
        // vendor('Beecldpay.Pay'.$wxptsign);
        // $paystr = 'Pay'.$wxptsign;
        // $Pay = new $paystr();
        // $parr['bill_no'] = I('orderid');
        // $parr['payrule'] = I('payrule');
        // $result = $Pay->query_order($parr);
        // $data = objarray_to_array($result);
        // $optional = objarray_to_array(json_decode($data['optional']));

        // $Pay = new \Pay($optional['app_id']);
        // if ($data['result_code'] == 0) {
        //     $bills = $data['bills'][0];
        //     if ($bills['spay_result']) {  //支付成功

        //         if ($bills['sub_channel'] == "BC_WX_JSAPI") {   //微信支付
        //             $param['payrule'] = 3;
        //         } elseif ($bills['sub_channel'] == "BC_ALI_WEB") {  //支付宝支付
        //             $param['payrule'] = 1;
        //         } else {
        //             die('FAIL');
        //         }

        //         $param['orderid'] = $bills['bill_no'];
        //         $param['actualprice'] = $bills['bill_fee']/100;
        //         $param['thirdpartynum'] = $bills['trade_no'];
        //         $param['upay'] = 1;
        //         if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
        //             $result = IGD('Agorder', 'Order')->PayOrder($param);
        //         } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
        //             $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
        //         } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
        //             $result = IGD('Storeorder', 'Order')->PayOrder($param);
        //         } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
        //             $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
        //         } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
        //             $result = IGD('Seckill', 'Newact')->PayOrder($param);
        //         } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
        //             $result = IGD('Agent', 'Order')->PayOrder($param);
        //         } else {   //普通线上订单
        //             $result = IGD('Order', 'Order')->PayOrder($param);
        //         }
        //     }  
        // }

        $this->ucode = session('USER.ucode');
        $orderid = I('orderid');
        $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
        $this->data = $result['data'];

        $userinfo = IGD('Login','Login')->GetUserByCode($this->data['c_acode']);
        $this->userdata = $userinfo['data'];

        $this->display('success');
    }

    //恒丰支付
    public function index9()
    {
        $this->is_weixin = 0;
        if (is_weixin()) {
            if (!session('openid')) {
                die('授权失败，请重新打开');
            }
            $this->is_weixin = 1;
        } else if (is_aliApp()) {
            // $this->display('pubnotice');die;
            if (!session('alipay_authinfo')) {
                die('授权失败，请重新打开');
            }
        } else {
            if (!session('USER.ucode')) {
                $this->userlogin();die;
            }
        }
        $this->acode = I('acode');
        $this->deskid = I('deskid');   //收银台编号
        $userinfo = IGD('Login','Login')->GetUserByCode($this->acode);
        $userdata = $userinfo['data'];

        $this->userdata = $userinfo['data'];
        $this->display();
    }

    //恒丰支付生成扫码支付订单
    public function CreateScanpayOrder9()
    {  
        header('Content-type: text/json');
        vendor('Hengdapay.Pay');
        $Pay = new \Pay();
        $payrule = I('payrule');
        $parr['ucode'] = session('USER.ucode');
        $parr['payrule'] = $payrule;
        $parr['money'] = I('money');
        $parr['acode'] = I('acode');
        $parr['deskid'] = I('deskid');
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

        //查询商家信息
        $userinfo = IGD('Login','Login')->GetUserByCode($parr['acode']);
        $userdata = $userinfo['data'];

        //提交资料生成订单
        $result = IGD('Scanpay','Scanpay')->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        if ($result['code'] == 0) {    
            //查询订单信息参数
            $orderid = $result['data']['ncode'];
            // $mch_id =  $result['data']['mch_id'];    //新商户号
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->ajaxReturn(Message(2000,'该订单不能进行支付'));
            }
            $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($money <= 0) {
                $this->ajaxReturn(Message(2001,'该订单金额小于0元'));
            }
            $tempmoney = intval(bcmul($money,100,2));
            $tempproductname = subtext(trim($userdata['c_nickname']).'的小店线下订单',12);
            $tempproductname = str_replace("\r", "", $tempproductname);
            $tempproductname = str_replace("\n", "", $tempproductname);
            $tempproductname = str_replace("...", "", $tempproductname);
            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $params["tradeType"] = 'cs.pay.submit';  //交易类型
                $params["version"] = '1.3';      //版本
                $params["channel"] = 'wxPub';        //支付渠道  
                $params["mchId"] = '000100002005';         //代理商号
                $params["subMchId"] = '000100002005000002';         //商户号
                $params["body"] = $tempproductname;                //商品描述
                $params["outTradeNo"] = $orderid;    //商户订单号
                $params["amount"] = $money;          //交易金额
                $params["description"] = '';//附加数据
                $params["limitPay"] = 'no_credit';               //禁掉微信信用卡
                $params["subAppId"] = 'wx862dd3d79978e035';               //禁掉微信信用卡
                $params["subOpenId"] = 'oTCJiuD1lQgZcqeHXMD3B7g640Zo';//$parr['openid'];            //用户微信唯一标识
                $params["notifyUrl"] = GetHost(1)."/index.php/Order/Weixin/hengfengnotify";        //异步回调地址
                $params["isRaw"] = 1;        //0:待封装;1:原生公众号(返回json串给jsapi拉起支付)
                $params['callbackUrl'] = '';             //同步回调地址
                $result = $Pay->createorderinfo($params);
                $data = objarray_to_array($result);
                $data1 = objarray_to_array(json_decode($data['payCode']));

                if ($data['returnMsg'] == 'OK' && !empty($data1)) {

                    $datainfo['appId'] = $data1['appId'];
                    $datainfo['timeStamp'] = $data1['timeStamp'];
                    $datainfo['signType'] = $data1['signType'];
                    $datainfo['package'] = $data1['package'];
                    $datainfo['nonceStr'] = $data1['nonceStr'];
                    $datainfo['paySign'] = $data1['paySign'];
                    $datainfo['orderid'] = $orderid;
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                } else {
                    $this->ajaxReturn(MessageInfo(3002, $data1['result_msg']));
                }
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                if (is_aliApp()) {
                    $params['title'] = $tempproductname;  //商品描述
                    $params['bill_no'] = $orderid;  //商户单号
                    $params['openid'] = $parr['openid'];     //用户openid  $parr['openid']
                    $params['total_fee'] = $tempmoney;   //总金 额
                    $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/hengfengnotify";
                    $params['type'] = 'ALI_WEB';
                    $result = $Pay->createorderinfo($params);
                    $data = objarray_to_array($result);

                    $data1 = objarray_to_array(json_decode($data['pay_info']));
                    if ($data1) {
                        $datainfo['tradeNO'] = $data1['tradeNO'];
                        $datainfo['orderid'] = $orderid;
                        $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                    } else {
                        $this->ajaxReturn(MessageInfo(3002, $data['msg']));
                    }
                }
            }
        }
        $this->ajaxReturn(MessageInfo(0,'生成订单成功',$orderid));
    }
}
