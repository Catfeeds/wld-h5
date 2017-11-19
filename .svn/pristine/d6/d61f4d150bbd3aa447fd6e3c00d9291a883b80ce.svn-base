<?php

namespace Order\Controller;

use Think\Controller;

/**
 * 支付宝支付模块
 */
class AlipayController extends Controller {

    //功能：手机网站支付接口接入页
    public function __construct() {
        parent::__construct();
        ob_end_clean(); //清除缓冲区,避免乱码
        header('Content-Type:text/html; charset=utf-8');
    }

    //初始化
    public function _initialize() {
        vendor('Alipay.Corefunction');
        vendor('Alipay.RSAfunction');
        vendor('Alipay.Notify');
        vendor('Alipay.Submit');
        vendor('AntAlipay.AlipayTradeService');
        vendor('AntAlipay.request.AlipayTradeWapPayContentBuilder');
    }

    //支付入口
    public function doalipay() {
        //获取订单信息
        $orderid = I('orderid');
        $parr['orderid'] = I('orderid');
        if (substr($orderid,0,1) == 'n') {   //扫码支付信息获取
            $result = IGD('Scanpay', 'Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->error("该订单不能进行支付");return;
            }
            $activityprice = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($activityprice <= 0) {
                $this->error("该订单不能进行支付");return;
            }
            $tempproductname = $resultdata['c_anickname'].'的小店线下订单';
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
            if ($result['code'] != 0) {
                $this->error("订单查询失败");
                return;
            }

            if ($result['code'] != 0) {
                $this->error($result['msg']);
                return;
            }

            $orderinfo = $result['data'];

            if ($orderinfo['c_pay_state'] != 0 && $orderinfo['c_pay_state'] != 2) {
                return Message(1021, "该订单已经支付或已经取消");
            }

            $detail = $orderinfo['detail'];

            $tempproductname = "";

            foreach ($detail as $key => $value) {
                $tempproductname = $tempproductname . $value['c_pname'];
            }

            //计算价格
            $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);
            $shenyu = bcsub($sum, $orderinfo['c_actual_price'], 2);
            $activityprice = $shenyu;
            //dump($sum);die;
            if ($activityprice <= 0) {

                $this->error("该订单不能进行支付");
            }
        }


        $productname = urldecode($tempproductname);
        $bodydetails = urldecode($tempproductname);
        $producturl = "https://www.iweilingdi.com/";

        $out_trade_no = $orderid;            //商户订单号
        $subject = $productname;     //订单名称，必填
        $total_amount = $activityprice;             //付款金额，必填
        $body = $bodydetails;     //商品描述，可空
        $timeout_express = "1m";      //超时时间

        $config = C('ALIPAYCONFIG');


        $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);

        //建立请求
        $payResponse = new \AlipayTradeService($config);
        $result = $payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
    }

    //app支付宝支付回调
    public function sjalinotify_url() {

        //计算得出通知验证结果
        $alipay_config = C('alipay_config');
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            $total_fee = $_POST['total_fee'];


            if ($_POST['trade_status'] == 'TRADE_FINISHED') {

                $parr['orderid'] = $out_trade_no;
                $parr['payrule'] = 1;
                $parr['actualprice'] = $total_fee;
                $parr['thirdpartynum'] = $trade_no;
                if (substr($parr['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 's') {  //旧版小蜜商城订单
                    $result = IGD('Supplyorder', 'Agorder')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($parr);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($parr);
                }

                if ($result['code'] == 0) {
                    echo "success";
                } else {
                    echo "fail";
                }

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {

                $parr['orderid'] = $out_trade_no;
                $parr['payrule'] = 1;
                $parr['actualprice'] = $total_fee;
                $parr['thirdpartynum'] = $trade_no;

                if (substr($parr['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 's') {  //旧版小蜜商城订单
                    $result = IGD('Supplyorder', 'Agorder')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($parr);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($parr);
                }

                if ($result['code'] == 0) {
                    echo "success";
                } else {
                    echo "fail";
                }

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";
        } else {
            //验证失败
            echo "fail";
        }
    }

    //网页支付宝支付回调
    public function alinotify_url() {

        //计算得出通知验证结果
        $arr = $_POST;$config = C('ALIPAYCONFIG');
        $alipaySevice = new \AlipayTradeService($config);
        $verify_result = $alipaySevice->check($arr);
        if ($verify_result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            $total_fee = $_POST['total_amount'];


            if ($_POST['trade_status'] == 'TRADE_FINISHED') {

                $parr['orderid'] = $out_trade_no;
                $parr['payrule'] = 1;
                $parr['actualprice'] = $total_fee;
                $parr['thirdpartynum'] = $trade_no;
                if (substr($parr['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 's') {  //旧版小蜜商城订单
                    $result = IGD('Supplyorder', 'Agorder')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($parr);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($parr);
                }

                if ($result['code'] == 0) {
                    echo "success";
                } else {
                    echo "fail";
                }

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {

                $parr['orderid'] = $out_trade_no;
                $parr['payrule'] = 1;
                $parr['actualprice'] = $total_fee;
                $parr['thirdpartynum'] = $trade_no;

                if (substr($parr['orderid'],0,1) == 'l') {  //代理商城订单
                    $result = IGD('Agorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 's') {  //旧版小蜜商城订单
                    $result = IGD('Supplyorder', 'Agorder')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'n') {   //扫码订单
                    $result = IGD('Scanpay', 'Scanpay')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 't') {   //普通线下订单
                    $result = IGD('Storeorder', 'Order')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'g') {  //拼团订单
                    $result = IGD('Groupbuy', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                    $result = IGD('Seckill', 'Newact')->PayOrder($parr);
                } else if (substr($parr['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($parr);
                } else {   //普通线上订单
                    $result = IGD('Order', 'Order')->PayOrder($parr);
                }

                if ($result['code'] == 0) {
                    echo "success";
                } else {
                    echo "fail";
                }

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";
        } else {
            //验证失败
            echo "fail";
        }
    }

    /*
     * 功能：支付宝页面跳转同步通知页面
     *
     *
     *
     */

    public function alireturn_url() {
        $arr = $_GET;$config = C('ALIPAYCONFIG');
        $alipaySevice = new \AlipayTradeService($config);
        $verify_result = $alipaySevice->check($arr);
        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号
            $trade_no = $_GET['trade_no'];

            //交易状态
            $trade_status = $_GET['trade_status'];
            $total_fee = $_GET['total_amount'];

            $parr['orderid'] = $out_trade_no;
            $parr['payrule'] = 1;
            $parr['actualprice'] = $total_fee;
            $parr['thirdpartynum'] = $trade_no;
            if (substr($parr['orderid'],0,1) == 'l') {  //代理商城订单
                $result = IGD('Agorder', 'Order')->PayOrder($parr);
            } else if (substr($parr['orderid'],0,1) == 's') {  //旧版小蜜商城订单
                $result = IGD('Supplyorder', 'Agorder')->PayOrder($parr);
            } else if (substr($parr['orderid'],0,1) == 'n') {   //扫码订单
                $result = IGD('Scanpay', 'Scanpay')->PayOrder($parr);
            } else if (substr($parr['orderid'],0,1) == 't') {   //普通线下订单
                $result = IGD('Storeorder', 'Order')->PayOrder($parr);
            } else if (substr($parr['orderid'],0,1) == 'g') {  //拼团订单
                $result = IGD('Storeorder', 'Newact')->PayOrder($parr);
            }  else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $result = IGD('Storeorder', 'Newact')->PayOrder($parr);
            } else if (substr($parr['orderid'],0,1) == 'f') {  //微商服务费订单
                    $result = IGD('Agent', 'Order')->PayOrder($parr);
            } else {   //普通线上订单
                $result = IGD('Order', 'Order')->PayOrder($parr);
            }

            if ($result['code'] == 0) {
                if (!empty($result['data']['sign'])) {
                    $sign = $result['data']['sign'];
                }
                if (substr($parr['orderid'],0,1) == 'n') {
                    $this->redirect('Order/Scanpay/success',array('orderid' => $parr['orderid']));
                } else {
                    $this->redirect('Order/Index/achieve',array('orderid' => $parr['orderid']));
                }
            } else {
                if (!empty($result['data']['sign'])) {
                    $sign = $result['data']['sign'];
                }
                if (substr($parr['orderid'],0,1) == 'n') {
                    $this->redirect('Order/Scanpay/success',array('orderid' => $parr['orderid']));
                } else {
                    $this->redirect('Order/Index/achieve',array('orderid' => $parr['orderid']));
                }
            }

        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            $this->error("服务器验证失败", U('Order/orderlist'));
        }
    }

    //银盛支付支付宝异步响应操作
    function respond_alipay_notify()
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
        $file = "data/alipay.txt";
        /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            if($params['trade_status']=="TRADE_SUCCESS"){
                $param['orderid'] = $params["out_trade_no"];
                $param['payrule'] = 1;
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

    //中信银行支付宝支付回调
    public function zxinnotify()
    {
        vendor('zxinpay.alipay.Request');
        $Request = new \Request();

        $xml = file_get_contents('php://input');
        $Request->resHandler->setContent($xml);

        //记录回调返回信息
        // $file = "data/alipayzxinnotify.txt";
        // file_put_contents($file, json_encode($Request->resHandler->getAllParameters()));
        
        //验证签名
        $params = $Request->resHandler->getAllParameters();   //返回数组
        $Request->resHandler->setKey($Request->cfg->C('key',$params['mch_id']));
        if($Request->resHandler->isTenpaySign()){
            if($Request->resHandler->getParameter('status') == 0 && $Request->resHandler->getParameter('result_code') == 0){
                //此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
                //更改订单状态
               
                $param['orderid'] = $params['out_trade_no'];
                $param['payrule'] = 1;
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

}
