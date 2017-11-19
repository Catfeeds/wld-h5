<?php

namespace Order\Controller;

use Base\Controller\CheckController;

/**
*  代理商微商服务费相关接口
*/
class AgentController extends CheckController
{
	//创建订单
	public function CreataOrder()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $money = I('money'); //需要支付总金额
        $balance = I('balance'); //支付余额余额
        $acode = I('acode');
        $payrule = I('payrule'); //1-全余额，2-微信支付，3-支付宝支付

        $parr['ucode'] = $ucode;
        $parr['acode'] = $acode;
        $parr['money'] = $money;
        $parr['balance'] = $balance;

        if ($payrule == 2) {
            $payrule = 2;
        } else if ($payrule == 3) {
            $payrule = 1;
        } else {
            $payrule = 4;
        }

        if ($balance >= $money) {
            $payrule = 4;
        }

        $parr['payrule'] = $payrule;
        $orderdb = IGD('Agent', 'Order');
        $result = $orderdb->CreateScanpayOrder($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        $ncode = $result['data']['ncode'];

        //获取订单详情
        $result = $orderdb->GetOrderInfo($ncode);
        $orderinfo = $result['data'];
        if ($payrule == 4) {
            $this->ajaxReturn($result);
        }

        if ($orderinfo['c_pay_state'] != 0) {
            $this->ajaxReturn(Message(1021, "该订单已经支付或已经取消"));
        }

        $shenyu = bcsub($orderinfo['c_money'], $orderinfo['c_actual_price'], 2);
        if ($shenyu <= 0) {
            $this->ajaxReturn(Message(1022, "该订单不能进行支付"));
        }
        $activityprice = $shenyu;
        $tempproductname = '小蜜微商服务费';

        if ($payrule == 1) {
            vendor('AntAlipay.Gateway');
            vendor('AntAlipay.Message');
            $AopClient = new \AopClient();
            $config = C('ALIPAYCONFIG');

            //构造返回数据
            $retunr['app_id'] = $config['app_id'];
            $retunr['method'] = 'alipay.trade.app.pay';
            $retunr['charset'] = 'utf-8';
            $retunr['sign_type'] = 'RSA2';
            $retunr['timestamp'] = gdtime();
            $retunr['version'] = '1.0';
            $retunr['notify_url'] = C('zhifubaoReturnNotify');
            $retunr['biz_content'] = $this->_makeContent($activityprice,$tempproductname,$tempproductname,$orderinfo['c_orderid']);

            //生成签名
            $AopClient->rsaPrivateKey = $config['merchant_private_key'];
            $sign = $AopClient->rsaSign($retunr,'RSA2');
            $retunr['sign'] = $sign;

            $askurl = $this->_makeData(urlencode($retunr['app_id']),urlencode($retunr['biz_content']),urlencode($retunr['notify_url']),urlencode($retunr['timestamp']),urlencode($sign));
            $retunr['askurl'] = $askurl;
          
            $result = MessageInfo(0, "订单状态查询成功", $retunr);
            $this->ajaxReturn($result);
        }

        if ($payrule == 2) {
            $tempmoney = (string) ($activityprice * 100);

            //随机获取线下系统商户号
            $mchidarr = explode(',', C('OFFLINEMICH'));
            $mch_id = $mchidarr[rand(0,(count($mchidarr)-1))];

            //组装内容信息            
            $porderinfo['total_fee'] = $tempmoney;
            $porderinfo['body'] = $tempproductname;
            $porderinfo['out_trade_no'] = $ncode;
            $porderinfo['mch_id'] = $mch_id;
            $porderinfo['service'] = "pay.weixin.raw.app";
            $porderinfo['appid'] = C('WXAPPID');

            //开始进行签名
            ksort($porderinfo);
            $str = "";
            $num = 0;
            foreach ($porderinfo as $key => $value) {
                if ($num == 0) {
                    $str = $key . "=" . $value;
                } else {
                    $str = $str . "&" . $key . "=" . $value;
                }
                $num++;
            }
            $str1 = $str . C('UPAYSECRET');
            $strmd5 = md5($str1);
            $porderinfo['sign'] = $strmd5;
            $tempstr = json_encode($porderinfo);
            echo $tempstr;

            //请求获取支付参数
            $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
            $data1 = $this->simple_post($remote_server, $tempstr);dump($data1);die;
            if ($data1['status'] == 0 && $data1['status'] != '') {
                $matches_id = explode('prepay_id=', $data1['package']);
                $prepay_id = $matches_id[1];
                $returndata["appid"] = $data1['appId'];
                $returndata["mchid"] = $mch_id;
                $returndata["appwd"] = C('WXAPPPWD');
                $returndata["prepay_id"] = $prepay_id;
                $returndata["noncestr"] = $data1['nonceStr'];
                $returndata["time"] = $data1['timeStamp'];
                $returndata["sing"] = $data1['paySign'];
                $returndata['orderid'] = $ncode;
                $returndata["yfmoney"] = $activityprice;
                $returnstr = MessageInfo(0, "生成与支付订单成功", $returndata);

                $this->ajaxReturn($returnstr, 'JSON');
            }

            $errinfo = Message($data1['status'], $data1['msg']);
            $this->ajaxReturn($errinfo);
        }
    }

    private function _makeContent($total_amount,$subject,$body,$out_trade_no)
    {
        header('Content-Type:text/html; charset=utf-8');
        $str_data = '{"timeout_express":"30m","product_code":"QUICK_MSECURITY_PAY","total_amount":"'.$total_amount.'","subject":"'.$subject.'","body":"'.$body.'","out_trade_no":"'.$out_trade_no.'"}';
        return $str_data;
    }

    private function _makeData($appid,$biz_content,$notify_url,$timestamp,$sign)
    {
        header('Content-Type:text/html; charset=utf-8');
        $str_data = "app_id=$appid&method=alipay.trade.app.pay&charset=utf-8&sign_type=RSA2&timestamp=$timestamp&version=1.0&notify_url=$notify_url&biz_content=$biz_content&sign=$sign";
        return $str_data;
    }

    private function _makeSign($appid, $mchid, $noncestr, $prepayid, $timestamp, $key) {
        //拼接API密钥
        $str_config = "appid=$appid&noncestr=$noncestr&package=Sign=WXPay&partnerid=$mchid&prepayid=$prepayid&timestamp=$timestamp&key=$key";
        $sign_info = strtoupper(md5($str_config));
        return $sign_info;
    }

    /**
     * 查询码订单信息
     * @param orderid
     */
    public function GetOrderInfo() 
    {
    	$orderid = I('orderid');
    	$result = IGD('Agent','Order')->GetOrderInfo($orderid);
    	$this->ajaxReturn($result);
    }

    /**
     * 取消订单
     * @param orderid
     */
    public function CancelOrder() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ncode'] = I('ncode');
        $parr['ucode'] = $ucode;

        $orderdb = IGD('Agent', 'Order');
        $result = $orderdb->CancelOrder($parr);
        $this->ajaxReturn($result);
    }

}


