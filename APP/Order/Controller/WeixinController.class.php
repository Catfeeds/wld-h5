<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\AuthController;


/**
 * 微信支付
 */
class WeixinController extends AuthController {

    public function __construct() {
        parent::__construct();
        ob_end_clean(); //清除缓冲区,避免乱码
        header('Content-Type:text/html; charset=utf-8');
    }
    
    //初始化
    public function _initialize() {
        vendor('WxPayPubHelper.WxPayPubHelper');
    }

    //支付入口
    public function jsApiCall() {
        $this->storename = C('WEB_NAME');
        //使用jsapi接口
        $jsApi = new \JsApi_pub();
        $tempredirect_uri = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (!empty($_GET['code'])) {
            $code = $_GET['code'];
            $weixininfo = $this->getgetOpenid($code);
            $orderid = $_GET['orderid'];
            $parr['orderid'] = $orderid;
            if (substr($orderid,0,1) == 'n') {   //扫码支付信息获取
                $result = IGD('Scanpay', 'Scanpay')->FindScanpayOrder($orderid);
                $resultdata = $result['data'];
                if ($resultdata['c_pay_state'] != 0) {
                    $this->error("该订单不能进行支付");return;
                }
                $money = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
                if ($money <= 0) {
                    $this->error("该订单不能进行支付");return;
                }
                $tempproductname = '小蜜扫码支付';
            } else {

                if (substr($parr['orderid'],0,1) == 's') {      //旧版小蜜商城订单
                    $orderdb = IGD('Supplyorder','Agorder');
                } else if (substr($parr['orderid'],0,1) == 'g') { //拼团订单
                    $orderdb = IGD('Groupbuy','Newact');
                } else if (substr($parr['orderid'],0,1) == 'm') { //秒杀订单
                    $orderdb = IGD('Seckill','Newact');
                } else {        //普通订单
                    $orderdb = IGD('Order', 'Order');
                }

                $result = $orderdb->GetPayorderinfo($parr);
                $resultdata = $result['data'];
                if ($result['code'] != 0) {
                    $this->error("订单查询失败");
                    return;
                }
                if ($resultdata['c_pay_state'] != 0) {
                    $this->error("该订单不能进行支付");
                    return;
                }

                $tempproductname = "";
                $details = $resultdata['detail'];
                for ($i = 0; $i < count($details); $i++) {
                    $tempproductname = $tempproductname . $details[$i]['c_pname'] . "|";
                }

                $sum = bcadd($resultdata['c_total_price'], $resultdata['c_free'], 2);
                $money = bcsub($sum, $resultdata['c_actual_price'], 2);
                if ($money <= 0) {
                    $this->error("该订单不能进行支付");
                }

            }

            $tempmoney = (string) ($money * 100);
            $jsApi->setCode($code);
            $openid = $jsApi->getOpenId();

            //=========步骤2：使用统一支付接口，获取prepay_id============
            //使用统一支付接口
            $unifiedOrder = new \UnifiedOrder_pub();
            $unifiedOrder->setParameter("sub_appid", C('APPID')); //子商户appid
            $unifiedOrder->setParameter("sub_openid", $openid); //子商户号获取用户openid

            $unifiedOrder->setParameter("sub_mch_id", C('SUB_MCHID')); //子商户号
            // $unifiedOrder->setParameter("openid", $openid); //商品描述
            $unifiedOrder->setParameter("body", $tempproductname); //商品描述

            $out_trade_no = $orderid;
            $unifiedOrder->setParameter("out_trade_no", $out_trade_no); //商户订单号
            $unifiedOrder->setParameter("total_fee", $tempmoney); //总金额
            $unifiedOrder->setParameter("notify_url", C('NOTIFY_URL')); //通知地址
            $unifiedOrder->setParameter("trade_type", "JSAPI"); //交易类型
            $unifiedOrder->setParameter("limit_pay", "no_credit"); //上传此参数no_credit--可限制用户不能使用信用卡支付
            //非必填参数，商户可根据实际情况选填
            //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
            //$unifiedOrder->setParameter("device_info","XXXX");//设备号
            //$unifiedOrder->setParameter("attach","XXXX");//附加数据
            //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
            //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
            //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
            //$unifiedOrder->setParameter("openid","XXXX");//用户标识
            //$unifiedOrder->setParameter("product_id","XXXX");//商品ID

            $prepay_id = $unifiedOrder->getPrepayId();
            //=========步骤3：使用jsapi调起支付============
            $jsApi->setPrepayId($prepay_id);

            $jsApiParameters = $jsApi->getParameters();
            $this->assign('orderid', $orderid);
            $this->assign('money', $money);
            $this->assign('jsApiParameters', $jsApiParameters);
            $this->display('pay');
        } else {
            $redirectUrl = urlencode($tempredirect_uri);
            $url = $this->createOauthUrlForCode($redirectUrl,'snsapi_base');
            header("Location:" . $url);die;
        }
    }

    public function notify() {
        //使用通用通知接口
        $notify = new \Notify_pub();

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码

            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name, "【通信出错】:\n" . $xml . "\n");
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name, "【业务出错】:\n" . $xml . "\n");
            } else {
                $param['upay'] = 1;
                $param['orderid'] = $notify->data["out_trade_no"];
                $param['payrule'] = 3;
                $param['actualprice'] = $notify->data["total_fee"] / 100;
                $param['thirdpartynum'] = $notify->data["transaction_id"];
                if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 's') {  //旧版小蜜商城订单
                    $result = IGD('Supplyorder', 'Agorder')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Order')->PayOrder($param);
                }  else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Order')->PayOrder($param);
                }  else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($param);
                }

                if ($result['code'] == 0) {
                    if (!empty($result['data']['sign'])) {
                        $sign = $result['data']['sign'];
                    }
                    $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码
                    $returnXml = $notify->returnXml();
                    if (substr($param['orderid'],0,1) == 'n') {
                        $this->redirect('Order/Scanpay/success',array('orderid' => $param['orderid']));
                    } else {
                        $this->redirect('Order/Index/achieve',array('orderid' => $param['orderid']));
                    }
                } else {
                    // $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
                    // $notify->setReturnParameter("return_msg", "订单操作失败"); //返回信息
                    // $this->redirect('Common/fail',array('orderid' => $param['orderid']));
                }
            }
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
    }

    public function Shoujinotify() {

        vendor('WxSjPayPubHelper.WeixinNotify');
        //使用通用通知接口
        $notify = new \Wxpay();

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $str = "签名失败";
            \Think\Log::record($str);


            $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码

            if ($notify->data["return_code"] == "FAIL") {
                $str = "通信出错";
                \Think\Log::record($str);
                //此处应该更新一下订单状态，商户自行增删操作
                log_result($log_name, "【通信出错】:\n" . $xml . "\n");
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $str = "业务出错";
                \Think\Log::record($str);
                log_result($log_name, "【业务出错】:\n" . $xml . "\n");
            } else {

                $param['orderid'] = $notify->data["out_trade_no"];
                $param['payrule'] = 2;
                $param['actualprice'] = $notify->data["total_fee"] / 100;
                $param['thirdpartynum'] = $notify->data["transaction_id"];
                if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 's') {  //旧版小蜜商城订单
                    $result = IGD('Supplyorder', 'Agorder')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
                }  else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($param);
                }  else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($param);
                }

                if ($result['code'] == 0) {
                    $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码
                    //$this->redirect('Common/success');
                } else {
                    $str = "自己服务操作失败";
                    \Think\Log::record($str);
                    $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
                    $notify->setReturnParameter("return_msg", "订单操作失败"); //返回信息
//                    $this->error($result['msg'], U('Common/fail', array('orderid' => $param['orderid'])));
                }
            }
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
    }


    //友收宝支付回调
    public function youshounotify() {

        //获取post传过来的json
        $json = file_get_contents("php://input");
        $resultjson = json_decode($json);
        $resultdata = objarray_to_array($resultjson);

        if ($resultdata['result_code'] == 0 && $resultdata['pay_result'] == 0) {
            // \Think\Log::record("支付成功了");
            $param['orderid'] = $resultdata["out_trade_no"];
            if ($resultdata['trade_type'] == "pay.weixin.jspay") {   //微信支付
                $param['payrule'] = 3;
            } elseif ($resultdata['trade_type'] == "pay.alipay.jspay") {  //支付宝支付
                $param['payrule'] = 1;
            } else {
                die('FAIL');
            }

            $param['actualprice'] = $resultdata['total_fee'] / 100;
            $param['thirdpartynum'] = $resultdata['out_transaction_id'];
            $param['upay'] = 1;
            if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                $result = IGD('Agorder', 'Order')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                $result = IGD('Storeorder', 'Order')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                $result = IGD('Seckill', 'Newact')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
                $result = IGD('Agent', 'Order')->PayOrder($param);
            } else {   //普通线上订单
                $result = IGD('Order', 'Order')->PayOrder($param);
            }

            if ($result['code'] != 0) {
                die('FAIL');
            }

            die('SUCCESS');
        }
    }

	//银盛支付微信支付异步响应操作
    function respond_notify()
    {
        Vendor('Ysepay.Yse_pay');
        $pay =new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $params = $_POST;
        unset($params['sign']);
        ksort($params);
        $url = "";
        foreach ($params as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/ysweixinpay.txt";

        /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            // G('begin');
            if($params['trade_status']=="TRADE_SUCCESS"){
                $param['orderid'] = $params["out_trade_no"];
                $param['payrule'] = 3;
                $param['actualprice'] = $params['total_amount'];
                $param['thirdpartynum'] = $params['trade_no'];
                $param['upay'] = 1;
                if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($param);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($param);
                }
                // G('end');
                
                if ($result['code'] != 0) {
                    if (substr($param['orderid'],0,1) == 'n') {
                        $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($params["out_trade_no"]);
                    } else if (substr($param['orderid'],0,1) == 's') {
                        $orderdb = IGD('Supplyorder','Agorder');
                        $result = $orderdb->GetOrderInfo($param);            
                    } else if (substr($param['orderid'],0,1) == 'g') { //拼团订单
                        $orderdb = IGD('Groupbuy','Newact');
                        $result = $orderdb->AchievePayorderInfo($param);
                    } else if (substr($param['orderid'],0,1) == 'm') { //秒杀订单
                        $orderdb = IGD('Order','Order');
                        $result = $orderdb->GetOrderInfo($param);            
                    } else {
                        $orderdb = IGD('Order', 'Order');
                        $result = $orderdb->GetOrderInfo($param);            
                    }

                    $orderinfo = $result['data'];
                    if ($orderinfo['c_pay_state'] == 1) {
                        echo 'success';
                    } else {
                        echo "error";
                    }
                } else {
                    // file_put_contents($file,gdtime().'回调执行时间'.G('begin','end').'s'.$data);
                    echo 'success';
                }
            }else{
                echo "error";
            }
        } else {
            echo "error";
        }
    }

    //平安支付回调
    public function papaynotify() {
        vendor('Papay.webApp');

        //查询订单信息
        if (substr($_GET["out_no"],0,1) == 'n') {   //扫码订单
            $orderid = $_GET["out_no"];
            $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($orderid);
            $orderinfo = $result['data'];
            $open_info = C('PAOPEN_INFO');
            $open_id = $orderinfo['c_headimg'];

            foreach ($open_info as $key => $value) {
                if ($value['PAOPEN_ID'] == $open_id) {
                    $open_key = $value['PAOPEN_KEY'];
                }
            }
        } else {
            $open_id = C('ONPAOPEN_ID');
            $open_key = C('ONPAOPEN_KEY');
        }
        
        $webApp = new \webApp($open_id,$open_key);

        $map = $_GET;
        $result = $webApp->notify($map);
        if ($result) {
            $data['out_no'] = $map["out_no"];
            $result = $webApp->api("paystatus",$data);
            if ($result['errcode'] == 0 && $result['data']['status'] == 1) {
                $actualprice = $result['data']['trade_amount'];
            } else {
                echo 'notify_error';
            }

            if ($actualprice > 0) {
                $param['payrule'] = 3;
                $param['orderid'] = $map["out_no"];
                $param['actualprice'] = $actualprice / 100;
                $param['thirdpartynum'] = $map['ord_no'];
                $param['upay'] = 1;            

                if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($param);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($param);
                }

                if ($result['code'] != 0) {
                    echo 'notify_error';return;
                }

                echo 'notify_success';
            } else {
                echo 'notify_error';
            }
        } else {
            echo 'check sign error';
        }
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

    //中信银行微信支付回调
    public function zxinnotify()
    {
        vendor('zxinpay.weixin.Request');
        $Request = new \Request();

        $xml = file_get_contents('php://input');
        // $xml = '<xml><bank_type><![CDATA[CFT]]></bank_type><charset><![CDATA[UTF-8]]></charset><fee_type><![CDATA[CNY]]></fee_type><is_subscribe><![CDATA[N]]></is_subscribe><mch_id><![CDATA[102583849207]]></mch_id><nonce_str><![CDATA[1507796799151]]></nonce_str><openid><![CDATA[oMJGHs27b8CNMeQbWWrBtbAzot74]]></openid><out_trade_no><![CDATA[n1710121622200803]]></out_trade_no><out_transaction_id><![CDATA[4200000009201710127622087584]]></out_transaction_id><pay_result><![CDATA[0]]></pay_result><result_code><![CDATA[0]]></result_code><sign><![CDATA[342B22A967374B3273FEA3A05B4CC4CF]]></sign><sign_type><![CDATA[MD5]]></sign_type><status><![CDATA[0]]></status><sub_appid><![CDATA[wx862dd3d79978e035]]></sub_appid><sub_is_subscribe><![CDATA[Y]]></sub_is_subscribe><sub_openid><![CDATA[oTCJiuD1lQgZcqeHXMD3B7g640Zo]]></sub_openid><time_end><![CDATA[20171012162225]]></time_end><total_fee><![CDATA[10]]></total_fee><trade_type><![CDATA[pay.weixin.jspay]]></trade_type><transaction_id><![CDATA[102583849207201710122176086145]]></transaction_id><version><![CDATA[2.0]]></version></xml>';
        $Request->resHandler->setContent($xml);

        //记录回调返回信息
        // $file = "data/weixinzxinnotify.txt";
        // file_put_contents($file, json_encode($Request->resHandler->getAllParameters()));
        
        //验证签名
        $params = $Request->resHandler->getAllParameters();   //返回数组
        $Request->resHandler->setKey($Request->cfg->C('key',$params['mch_id']));
        if($Request->resHandler->isTenpaySign()){
            if($Request->resHandler->getParameter('status') == 0 && $Request->resHandler->getParameter('result_code') == 0){
                //此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
                //更改订单状态
               
                $param['orderid'] = $params['out_trade_no'];
                $param['payrule'] = 3;
                $param['actualprice'] = $params['total_fee']/100;
                $param['thirdpartynum'] = $params['transaction_id'];
                $param['upay'] = 1;
                if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($param);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($param);
                }

                if ($result['code'] != 0) {
                    echo "回调操作失败";
                } else {
                    echo 'success';
                }
            }else{
                echo "支付状态失败";
            }
        }else{
            echo "验证签名失败";
        }
    }

    //微众支付回调
    public function weizhongnotify()
    {
        vendor('Beecldpay.Pay');
        $jsonStr = file_get_contents("php://input");
        // $jsonStr = '{"bill_fee":1,"discount":0,"channelType":"BC","buyer_id":"","channel_transaction_id":"U1710311135380000000000002830561","coupon_id":"","tradeSuccess":true,"id":"cbf9b30e-d57d-45a1-aafc-a7574c01dd9d","channel_type":"BC","app_id":"4b03fdbb-7ff1-4cb2-8411-b245e8368ec4","retryCounter":3,"transaction_id":"n1710311135370631","retry_counter":0,"transaction_fee":1,"sub_channel_type":"BC_WX_JSAPI","optional":{"app_id":"4b03fdbb-7ff1-4cb2-8411-b245e8368ec4"},"transaction_type":"PAY","notify_url":"https://ceshiapi.iweilingdi.com/index.php/Order/Weixin/weizhongnotify","transactionId":"n1710311135370631","transactionType":"PAY","transactionFee":1,"notifyUrl":"https://ceshiapi.iweilingdi.com/index.php/Order/Weixin/weizhongnotify","messageDetail":{"cashFee":"0.01","couponFee":"0","totalAmount":"0.01","channelNo":"4200000038201710311433541106","payTime":"2017-10-31 11:35:43","orderId":"n1710311135370631","bankType":"CFT","outTradeNo":"U1710311135380000000000002830561","tradeStatus":"01","buyerId":"oTCJiuD1lQgZcqeHXMD3B7g640Zo","isSubscribe":"N","tradeSuccess":true,"transactionFee":1},"message_detail":{"cashFee":"0.01","couponFee":"0","totalAmount":"0.01","channelNo":"4200000038201710311433541106","payTime":"2017-10-31 11:35:43","orderId":"n1710311135370631","bankType":"CFT","outTradeNo":"U1710311135380000000000002830561","tradeStatus":"01","buyerId":"oTCJiuD1lQgZcqeHXMD3B7g640Zo","isSubscribe":"N","tradeSuccess":true,"transactionFee":1},"trade_success":true,"signature":"352f24a67ac03e9a4ff1aa38ef4d838f","sign":"7f9260eff32cde0bbc8a27d77b6c9c8d","signAll":"12409ea2ecbb702fe4ab26d939a94246","timestamp":1509420900000}';
        $msg = json_decode($jsonStr);

        $params = objarray_to_array($msg);
        $optional = $params['optional'];

        $wxptsign = $optional['wxptsign'];
        vendor('Beecldpay.Pay'.$wxptsign);
        $paystr = 'Pay'.$wxptsign;
        $Pay = new $paystr($optional['app_id']);
        
        // $file = "data/weizhongnotify.txt";
        // file_put_contents($file, $jsonStr);
        
        $result = $Pay->CheckSign($msg);
        if ($result) {
            if ($params['trade_success']) {  //支付成功

                if ($params['sub_channel_type'] == "BC_WX_JSAPI") {   //微信支付
                    $param['payrule'] = 3;
                } elseif ($params['sub_channel_type'] == "BC_ALI_WEB") {  //支付宝支付
                    $param['payrule'] = 1;
                } else {
                    die('FAIL');
                }

                $param['orderid'] = $params['transaction_id'];
                $param['actualprice'] = $params['transaction_fee']/100;
                $param['thirdpartynum'] = $params['channel_transaction_id'];
                $param['upay'] = 1;
                if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($param);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($param);
                }

                if ($result['code'] != 0) {
                    if (substr($param['orderid'],0,1) == 'n') {
                        $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($param["orderid"]);
                    } else if (substr($param['orderid'],0,1) == 's') {
                        $orderdb = IGD('Supplyorder','Agorder');
                        $result = $orderdb->GetOrderInfo($param);            
                    } else if (substr($param['orderid'],0,1) == 'g') { //拼团订单
                        $orderdb = IGD('Groupbuy','Newact');
                        $result = $orderdb->AchievePayorderInfo($param);
                    } else if (substr($param['orderid'],0,1) == 'm') { //秒杀订单
                        $orderdb = IGD('Order','Order');
                        $result = $orderdb->GetOrderInfo($param);            
                    } else {
                        $orderdb = IGD('Order', 'Order');
                        $result = $orderdb->GetOrderInfo($param);            
                    }

                    $orderinfo = $result['data'];
                    if ($orderinfo['c_pay_state'] == 1) {
                        echo 'success';
                    } else {
                        echo "error";
                    }
                } else {
                    echo 'success';
                }
            } else {
                echo "支付状态失败";
            }            
        } else {
            echo "验证签名失败";
        }
    }

    public function hengfengnotify()
    {
        vendor('Hengdapay.Pay');
        $Pay = new \Pay();

        $jsonStr = file_get_contents("php://input");
        // $jsonStr = '{"bill_fee":1,"discount":0,"channelType":"BC","buyer_id":"","channel_transaction_id":"U1710311135380000000000002830561","coupon_id":"","tradeSuccess":true,"id":"cbf9b30e-d57d-45a1-aafc-a7574c01dd9d","channel_type":"BC","app_id":"4b03fdbb-7ff1-4cb2-8411-b245e8368ec4","retryCounter":3,"transaction_id":"n1710311135370631","retry_counter":0,"transaction_fee":1,"sub_channel_type":"BC_WX_JSAPI","optional":{"app_id":"4b03fdbb-7ff1-4cb2-8411-b245e8368ec4"},"transaction_type":"PAY","notify_url":"https://ceshiapi.iweilingdi.com/index.php/Order/Weixin/weizhongnotify","transactionId":"n1710311135370631","transactionType":"PAY","transactionFee":1,"notifyUrl":"https://ceshiapi.iweilingdi.com/index.php/Order/Weixin/weizhongnotify","messageDetail":{"cashFee":"0.01","couponFee":"0","totalAmount":"0.01","channelNo":"4200000038201710311433541106","payTime":"2017-10-31 11:35:43","orderId":"n1710311135370631","bankType":"CFT","outTradeNo":"U1710311135380000000000002830561","tradeStatus":"01","buyerId":"oTCJiuD1lQgZcqeHXMD3B7g640Zo","isSubscribe":"N","tradeSuccess":true,"transactionFee":1},"message_detail":{"cashFee":"0.01","couponFee":"0","totalAmount":"0.01","channelNo":"4200000038201710311433541106","payTime":"2017-10-31 11:35:43","orderId":"n1710311135370631","bankType":"CFT","outTradeNo":"U1710311135380000000000002830561","tradeStatus":"01","buyerId":"oTCJiuD1lQgZcqeHXMD3B7g640Zo","isSubscribe":"N","tradeSuccess":true,"transactionFee":1},"trade_success":true,"signature":"352f24a67ac03e9a4ff1aa38ef4d838f","sign":"7f9260eff32cde0bbc8a27d77b6c9c8d","signAll":"12409ea2ecbb702fe4ab26d939a94246","timestamp":1509420900000}';
        $msg = json_decode($jsonStr);

        $params = objarray_to_array($msg);
        $optional = $params['optional'];

        $wxptsign = $optional['wxptsign'];
        vendor('Beecldpay.Pay'.$wxptsign);
        $paystr = 'Pay'.$wxptsign;
        $Pay = new $paystr($optional['app_id']);
        
        $file = "data/hengfengnotify.txt";
        file_put_contents($file, $jsonStr);
        
        $result = $Pay->CheckSign($msg);
        if ($result) {
            if ($params['trade_success']) {  //支付成功

                if ($params['sub_channel_type'] == "BC_WX_JSAPI") {   //微信支付
                    $param['payrule'] = 3;
                } elseif ($params['sub_channel_type'] == "BC_ALI_WEB") {  //支付宝支付
                    $param['payrule'] = 1;
                } else {
                    die('FAIL');
                }

                $param['orderid'] = $params['transaction_id'];
                $param['actualprice'] = $params['transaction_fee']/100;
                $param['thirdpartynum'] = $params['channel_transaction_id'];
                $param['upay'] = 1;
                if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($param);
                } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($param);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($param);
                }

                if ($result['code'] != 0) {
                    if (substr($param['orderid'],0,1) == 'n') {
                        $result = IGD('Scanpay','Scanpay')->FindScanpayOrder($param["orderid"]);
                    } else if (substr($param['orderid'],0,1) == 's') {
                        $orderdb = IGD('Supplyorder','Agorder');
                        $result = $orderdb->GetOrderInfo($param);            
                    } else if (substr($param['orderid'],0,1) == 'g') { //拼团订单
                        $orderdb = IGD('Groupbuy','Newact');
                        $result = $orderdb->AchievePayorderInfo($param);
                    } else if (substr($param['orderid'],0,1) == 'm') { //秒杀订单
                        $orderdb = IGD('Order','Order');
                        $result = $orderdb->GetOrderInfo($param);            
                    } else {
                        $orderdb = IGD('Order', 'Order');
                        $result = $orderdb->GetOrderInfo($param);            
                    }

                    $orderinfo = $result['data'];
                    if ($orderinfo['c_pay_state'] == 1) {
                        echo 'success';
                    } else {
                        echo "error";
                    }
                } else {
                    echo 'SUCCESS';
                }
            } else {
                echo "支付状态失败";
            }            
        } else {
            echo "验证签名失败";
        }
    }

}
?>

