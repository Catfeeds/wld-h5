<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\MoreController;

/**
 * 线上商家订单模块
 */
class IndexController extends MoreController {
	
    public function __construct() {
        parent::__construct();
        header('Content-Type: text/html;charset=utf-8');
        if (!session('USER.ucode')) {
            $this->userlogin();die();
        }
    }
    public function index() {

        /*提交订单信息*/
        $ucode = session('USER.ucode');

        /*未登录前已选的商品信息提交，读取cookies*/
        $pcode = explode("|", $_POST['confirm-pcode']);
        $num = explode("|", $_POST['confirm-num']);
        $pucode = explode("|", $_POST['confirm-pucode']);
        $mcode = explode("|", $_POST['confirm-mcode']);
        for ($i=0;$i < count($pcode);$i++) {
            $product[$i]['pcode'] = $pcode[$i];
            $product[$i]['num'] = $num[$i];
            $product[$i]['pucode'] = $pucode[$i];
            $product[$i]['mcode'] = $mcode[$i];
        }
        /*获取商品详情，购物车，提交的商品信息*/
        $this->product= json_encode($product);
        $proinfo = IGD('Order', 'Order');
        $prolist = $proinfo->splitProduct($product, $ucode);
        if ($prolist['code']!=0) {
            $this->procode = $prolist['code'];
            $this->prosqmsg = $prolist['msg'];
        }
        $this->prodata = $prolist['data']['value'];
        $prolist['data']['totalprice'] = 0;

        foreach ($prolist['data']['value'] as $key => $val) {
            $prolist['data']['totalprice'] += $val['singletotle'];
        }
        $this->freeprice = $prolist['data']['freeprice'];
        $this->countprice = sprintf("%1\$.2f", $prolist['data']['totalprice']);


        // 获取地址列表
        $parr['ucode'] = $ucode;
        $addressinfo = IGD('User', 'User');
        $addressdata = $addressinfo->GetUserAddress($parr);
        $this->addresslist = $addressdata['data'];

        /*获取默认地址*/
        $defaultaddr = IGD('User', 'User');
        $defaults = $defaultaddr->Getdefaultaddress($parr);
        $this->defaultdz = $defaults['data'];

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province=$province_list;

        /*获取可用余额*/
        $parru['ucode'] = session('USER.ucode');
        $balance = IGD('User', 'User');
        $b_money = $balance->GetUserMoney($parru);
        $this->cmoney = $b_money['data']['c_money'];

        $this->show();
    }
	
	/*线上订单首页*/
	public function orderindex(){		
        $parr['ucode'] = session('USER.ucode');
        $ordernum = IGD('Order','Order')->orderCountInfo($parr);        
        $this->data = $ordernum['data'];
		$this->show();
	}

    //供支付选择面对面收货方式
    public function SelectDelivery()
    {
        $ucode = $ucode = session('USER.ucode');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }
        
        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $result = IGD('Order', 'Order')->SelectDelivery($parr);
        $this->ajaxReturn($result);
    }
	        

    /**
     * 获取商家来源
     */
    public function get_shoptype($pcode){
        $where['c_pcode'] = $pcode;
        $shoptype = M('Product')->where($where)->getField('c_source');
        return $shoptype;
    }
    /**
     * 生成订单
     */
    public function CreataOrder() {
        if (IS_AJAX) {
            $ucode = session('USER.ucode');
            $parr['ucode'] = session('USER.ucode');
            $attrbul = I('attrbul');
            $attrbul = str_replace('&quot;', '"', $attrbul);
            $data = objarray_to_array(json_decode($attrbul));
            $parr['delivery'] = $data['delivery'];
            $parr['addressid'] = $data['addressid'];
            $parr['postscript'] = urldecode($data['postscript']);
            $parr['money'] = $data['money'];
            $parr['produce'] = urldecode($data['produce']);
            $parr['model'] = 3;//1安卓，2IOS，3微信

            $produce = objarray_to_array(json_decode($parr['produce']));
            //获取商家类型
            $source = $this->get_shoptype($produce[0]['pcode']);

            if($source == 2){
                $orderdb = IGD('Storeorder', 'Order');
                $cardb = IGD('Storecar', 'User');
            }else{
                $orderdb = IGD('Order', 'Order');
                $cardb = IGD('Shoppingcar', 'User');
            }

            $result = $orderdb->CreataOrder($parr);

            if ($result['code'] != 0) {
                $this->ajaxReturn($result);
            }
            $info = $result['data'];

            $parr1['orderid'] = $info['orderid'];
            $parr1['ucode'] = $ucode;
            $result = $orderdb->GetPayorderinfo($parr1);
            //清空购物车
            $parrc['ucode'] = session('USER.ucode');
            foreach ($produce as $key => $value) {
                $parrc['pcode'] = $value['pcode'];
                $parrc['mcode'] = $value['mcode'];
                $cardb->DeleCar($parrc);
            }

            $this->ajaxReturn($result);

            // $attrbul = I('attrbul');
            // $attrbul = str_replace('&quot;', '"', $attrbul);
            // $data = objarray_to_array(json_decode($attrbul));
            // $parr['ucode'] = session('USER.ucode');
            // $parr['produce'] = $data['produce'];
            // $parr['addressid'] = $data['addressid'];
            // $parr['delivery']= $data['delivery'];
            // $parr['postscript'] = $data['postscript'];
            // $parr['money'] = $data['money'];
            // $orderinfo = D('Order', 'Service');
            // $result = $orderinfo->CreataOrder($parr);
            // $parrc['ucode'] = session('USER.ucode');
            // $produce = objarray_to_array(json_decode($parr['produce']));
            // foreach ($produce as $key => $value) {
            //    $parrc['mcode'] = $value['mcode'];
            //    $parrc['pcode'] = $value['pcode'];
            //    $delecar = D('Shoppingcar','Service')->DeleCar($parrc);
            // }
            // $this->ajaxReturn($result);
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

        $backurl = I('url');
        $this->backurl = decodeurl($backurl);
        $this->returnurl = encodeurl("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        $parra['ucode'] = $orderinfo['c_acode'];
        $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
        $yseinfo = $result['data'];
        if ($yseinfo['c_openaccount'] != 1) {
            $this->display('payment8');
        } else {
            $jumpurl = WEB_HOST . '/index.php/Order/Scanpay/payment?orderid=' . I('orderid');
            header("Location:" . $jumpurl);die();
            // $this->display('payment2');
        }
    }

    /**
     * 余额支付模块
     */
    public function BalancePay()
    {
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        $parr['money'] = I('money');
        $parr['payrule'] = I('payrule');
        $parr['bid'] = I('bid');
        $parr['bmoney'] = I('bmoney');
        if ($parr['money'] > 0 || (!empty($parr['bid']) && $parr['bmoney']>0)) {   //输入的余额大于0,启用余额支付            
            if (substr($parr['orderid'],0,1) == 'g') {   //拼团订单
                $mpinfo = IGD('Groupbuy','Newact')->BalancePay($parr);
            } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $mpinfo = IGD('Seckill','Newact')->BalancePay($parr);
            } else {
                //获取订单信息
                $payment = IGD('Order', 'Order');
                $result = $payment->GetPayorderinfo($parr);
                $orderinfo = $result['data'];

                $money = I('money');
                $balance = IGD('Money','User');
                $mpinfo = $balance->BalancePay($orderinfo,$money);
            }
        }

        if ($mpinfo['code'] == 0) {    //余额支付成功 
            $this->ajaxReturn($this->GetPayInfo($parr));
        } else {
            $this->ajaxReturn($mpinfo);
        }
    }

    //新版支付获取支付参数
    public function GetPayInfo($parr)
    {
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
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
        if ($result['code'] != 0) {
            return Message(3000,'订单信息不存在');
        }
        $orderinfo = $result['data'];
        // $mch_id = $orderinfo['mch_id'];

        $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);
        $money = bcsub($sum, $orderinfo['c_actual_price'], 2);
        if ($money <= 0) {
            return Message(3001,'该订单不能进行支付');
        }
        $tempmoney = (string) ($money * 100);

        $tempproductname = "";
        $details = $orderinfo['detail'];
        for ($i = 0; $i < count($details); $i++) {
            $tempproductname = $tempproductname . $details[$i]['c_pname'] . "|";
        }

        //微信支付宝授权openid
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $openid = $alipayinfo['openid'];
        } else {
            $openid = session('openid');
        }

        $result = $payment->CreatePayorder($orderid);
        if ($result['code'] != 0) {
            return Message(3002,'支付参数错误');
        }
        $payorderid = $result['data'];

        if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
            if (is_weixin()) {
                if ($mch_id) {
                    //组装内容信息
                    $orderinfo1['total_fee'] = $tempmoney;
                    $orderinfo1['body'] = $tempproductname;
                    $orderinfo1['out_trade_no'] = $payorderid;
                    $orderinfo1['mch_id'] = $mch_id;
                    $orderinfo1['sub_openid'] = $openid;
                    $orderinfo1['service'] = "pay.weixin.jspay";

                    //开始进行签名
                    ksort($orderinfo1);
                    $str = "";
                    $num = 0;
                    foreach ($orderinfo1 as $key => $value) {
                        if ($num == 0) {
                            $str = $key . "=" . $value;
                        } else {
                            $str = $str . "&" . $key . "=" . $value;
                        }
                        $num++;
                    }
                    $str1 = $str . C('UPAYSECRET');
                    $strmd5 = md5($str1);
                    $orderinfo1['sign'] = $strmd5;
                    $tempstr = json_encode($orderinfo1);

                    //请求获取支付参数
                    $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                    $data1 = $this->simple_post($remote_server, $tempstr);
                    if ($data1['status'] == '0' && $data1['status'] != '') {
                        $datainfo['appId'] = $data1['appId'];
                        $datainfo['timeStamp'] = $data1['timeStamp'];
                        $datainfo['signType'] = $data1['signType'];
                        $datainfo['package'] = $data1['package'];
                        $datainfo['nonceStr'] = $data1['nonceStr'];
                        $datainfo['paySign'] = $data1['paySign'];
                        $datainfo['orderid'] = $orderid;
                        return MessageInfo(0, '生成订单成功', $datainfo);
                    }
                    return Message($data1['status'], $data1['msg']);
                } else {
                    vendor('WxPayPubHelper.WxPayPubHelper');
                    $jsApi = new \JsApi_pub();
                    $unifiedOrder = new \UnifiedOrder_pub();
                    // $unifiedOrder->setParameter("sub_appid", C('APPID')); //子商户appid
                    // $unifiedOrder->setParameter("sub_openid", $parr['openid']); //子商户号获取用户openid

                    $unifiedOrder->setParameter("openid", $openid); //用户openid
                    $unifiedOrder->setParameter("sub_mch_id", C('SUB_MCHID')); //子商户号
                    $unifiedOrder->setParameter("body", $tempproductname); //商品描述
                    $unifiedOrder->setParameter("out_trade_no", $payorderid); //商户订单号
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
                    $data1 = objarray_to_array(json_decode($jsApiParameters));

                    $datainfo['appId'] = $data1['appId'];
                    $datainfo['timeStamp'] = $data1['timeStamp'];
                    $datainfo['signType'] = $data1['signType'];
                    $datainfo['package'] = $data1['package'];
                    $datainfo['nonceStr'] = $data1['nonceStr'];
                    $datainfo['paySign'] = $data1['paySign'];
                    $datainfo['orderid'] = $orderid;
                    return MessageInfo(0, '生成订单成功', $datainfo);
                }  
            }
        }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
            if (is_aliApp()) {
                //组装内容信息
                $orderinfo1['total_fee'] = $tempmoney;
                $orderinfo1['body'] = $tempproductname;
                $orderinfo1['out_trade_no'] = $payorderid;
                $orderinfo1['mch_id'] = $mch_id;
                $orderinfo1['buyer_id'] = $openid;
                $orderinfo1['service'] = "pay.alipay.jspay";

                //开始进行签名
                ksort($orderinfo1);
                $str = "";
                $num = 0;
                foreach ($orderinfo1 as $key => $value) {
                    if ($num == 0) {
                        $str = $key . "=" . $value;
                    } else {
                        $str = $str . "&" . $key . "=" . $value;
                    }
                    $num++;
                }
                $str1 = $str . C('UPAYSECRET');
                $strmd5 = md5($str1);
                $orderinfo1['sign'] = $strmd5;
                $tempstr = json_encode($orderinfo1);

                //请求获取支付参数
                $remote_server = "https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPay";
                $data1 = $this->simple_post($remote_server, $tempstr);
                if ($data1['status'] == 0) {
                    $datainfo['tradeNO'] = $data1['tradeNO'];
                    $datainfo['orderid'] = $orderid;
                    return MessageInfo(0, '生成订单成功', $datainfo);
                }
                return Message($data1['status'], $data1['msg']);
            }             
        }

        return MessageInfo(0,'生成订单成功',$orderid);
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

    /**
     * 新版银盛余额支付模块
     */
    public function BalancePay2()
    {
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        $parr['money'] = I('money');
        $parr['payrule'] = I('payrule');
        $parr['bid'] = I('bid');
        $parr['bmoney'] = I('bmoney');
        if ($parr['money'] > 0 || (!empty($parr['bid']) && $parr['bmoney']>0)) {   //输入的余额大于0,启用余额支付            
            if (substr($parr['orderid'],0,1) == 'g') {   //拼团订单
                $mpinfo = IGD('Groupbuy','Newact')->BalancePay($parr);
            } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $mpinfo = IGD('Seckill','Newact')->BalancePay($parr);
            } else {
                //获取订单信息
                $payment = IGD('Order', 'Order');
                $result = $payment->GetPayorderinfo($parr);
                $orderinfo = $result['data'];

                $money = I('money');
                $balance = IGD('Money','User');
                $mpinfo = $balance->BalancePay($orderinfo,$money);
            }
        }

        if ($mpinfo['code'] == 0) {    //余额支付成功 
            $this->ajaxReturn($this->GetPayInfo2($parr));
        } else {
            $this->ajaxReturn($mpinfo);
        }
    }

    //新版银盛支付获取支付参数
    public function GetPayInfo2($parr)
    {
        Vendor('Ysepay.Yse_pay');
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
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
        if ($result['code'] != 0) {
            return Message(3000,'订单信息不存在');
        }
        $orderinfo = $result['data'];
        $mch_id = $orderinfo['mch_id'];

        $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);
        $money = bcsub($sum, $orderinfo['c_actual_price'], 2);
        if ($money <= 0) {
            return Message(3001,'该订单不能进行支付');
        }
        $tempmoney = (string) ($money * 100);

        $tempproductname = "";
        $details = $orderinfo['detail'];
        for ($i = 0; $i < count($details); $i++) {
            $tempproductname = $tempproductname . $details[$i]['c_pname'] . "|";
        }

        //查询商家开户信息
        $parra['ucode'] = $orderinfo['c_acode'];
        $result = IGD('Ysepay','Scanpay')->PayGetYsedata($parra);
        if ($result['code'] != 0) {
            $this->ajaxReturn(Message(3000,'资料审核中，暂不能支付'));
        }
        $yseinfo = $result['data'];
        

        //微信支付宝授权openid
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $openid = $alipayinfo['openid'];
        } else {
            $openid = session('openid');
        }

        $result = $payment->CreatePayorder($orderid);
        if ($result['code'] != 0) {
            return Message(3002,'支付参数错误');
        }
        $payorderid = $result['data'];

        if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
            if (is_weixin()) {
                //组装内容信息
                $pay = new \Yse_pay();
                $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/respond_notify";
                $params['out_trade_no'] = $payorderid;
                $params['total_amount'] = $money;
                $params['subject'] = $tempproductname;
                $params['seller_id'] = $yseinfo['c_username']; // 收款方银盛支付用户名
                $params['seller_name'] = $yseinfo['c_person']; // 授权方银盛支付客户名
                $params['sub_openid'] =  'oTCJiuD1lQgZcqeHXMD3B7g640Zo';//$parr['openid'];//

                $data = $pay->weixin_pay($params);
                $result = $pay->curl_https_appid($data);
                $data1 =json_decode($result['ysepay_online_jsapi_pay_response']['jsapi_pay_info'],true);
                $datainfo['appId'] = $data1['appId'];
                $datainfo['timeStamp'] = $data1['timeStamp'];
                $datainfo['signType'] = $data1['signType'];
                $datainfo['package'] = $data1['package'];
                $datainfo['nonceStr'] = $data1['nonceStr'];
                $datainfo['paySign'] = $data1['paySign'];
                $datainfo['orderid'] = $orderid;
                if($data1==false){
                    $this->ajaxReturn(Message(3001,'验证失败'));
                }else{
                    $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
                }
            }    
        }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
            if (is_aliApp()) {
                //组装内容信息
                $pay = new \Yse_pay();
                $parr['partner_id'] ="wld17375717292";
                $parr['notify_url'] = GetHost(1)."/index.php/Order/Alipay/respond_alipay_notify";
                $parr['return_url'] = GetHost(1)."/index.php/Order/Index/achieve?orderid=".$orderid;
                $parr['out_trade_no'] = $payorderid;             //订单号,自行生成;
                $parr['subject'] = $tempproductname;
                $parr['total_amount'] = $money;          //交易金额
                $parr['seller_id'] = $yseinfo['c_username']; // 收款方银盛支付用户名
                $parr['seller_name'] = $yseinfo['c_person']; // 授权方银盛支付客户名
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

        return MessageInfo(0,'生成订单成功',$orderid);
    }

    // 订单列表
    public function orderlist() {
        $this->statu = I('statu');
        $this->show();
    }

    // 获取订单列表
    public function GetOrderList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['type'] = I('type');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Order','Order')->GetOrderList($parr);
        $this->ajaxReturn($result);
    }

    // 取消订单
    public function CancelOrder() {
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        if (substr($parr['orderid'],0,1) == 's') {
            $result = IGD('Supplyorder', 'Agorder')->CancelOrder($parr);
        } else {
            $result = IGD('Order','Order')->CancelOrder($parr);
        }
        $this->ajaxReturn($result);
    }

    // 确认订单
    public function Confirmorder()
    {
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        if (substr($parr['orderid'],0,1) == 's') {
            $result = IGD('Supplyorder', 'Agorder')->Confirmorder($parr);
        } else {
            $result = IGD('Order','Order')->Confirmorder($parr);
        }
        $this->ajaxReturn($result);
    }

    // 订单详情
    public function detail() {
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        if (substr($parr['orderid'],0,1) == 's') {
            $result = IGD('Supplyorder', 'Agorder')->GetOrderInfo($parr);
        } else {
            $result = IGD('Order','Order')->GetOrderInfo($parr);
        }
        $this->statu = I('statu');   
        $this->assign('data',$result['data']);
        $this->show();
    }

    //申请售后
    public function aftersale() {
//        if (IS_POST && session('time') == $_POST['time']) {
//            if ($_FILES) {
//                $imgresult = uploadimg('aftersale');
//                if ($imgresult['code'] != 0) {
//                    $resultdata = $imgresult['msg'];
//                    echo "<script >mui.toast('$resultdata','frame')</script>";
//                    return;
//                }
//                $parr1['img'] = implode('|',$imgresult['data']);
//            }
//            $parr1['detailid'] = I('detailid');
//            $parr1['status'] = I('status');
//            $parr1['type'] = I('type');
//            $parr1['reason'] = I('reason');
//            $parr1['remarks'] = I('remarks');
//            if (substr($parr1['detailid'],0,1) == 's') {
//                $result = IGD('Supplyrefund', 'Agorder')->Refundinfor($parr1);
//            } else {
//                $result = IGD('Refund','Order')->Refundinfor($parr1);
//            }
//
//            $resultdata = $result['msg'];
//            if ($result['code'] != 0) {
//                echo "<script >mui.toast('$resultdata','frame')</script>";
//                return;
//            }
//            session('time', null);
//            echo "<script>mui.toast('$resultdata','frame')</script>";
//            echo "<script>setTimeout(function(){window.location.href = 'javascript:history.go(-2)';},2000);</script>";
//            return;
//        }
        // 查询订单信息
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        $detailid = I('detailid');
        if (substr($parr['orderid'],0,1) == 's') {
            $orderresult = IGD('Supplyorder','Agorder')->GetPayorderinfo($parr);
        } else {
            $orderresult = IGD('Order','Order')->GetPayorderinfo($parr);
        }
        $orderinfo = $orderresult['data'];
        foreach ($orderinfo['detail'] as $key => $value) {
            if ($value['c_detailid'] == $detailid) {
                $this->detail = $value;
            }
        }
        $this->assign('orderinfo',$orderinfo);
        $this->show();
    }

    /**
     * 申请售后提交
     */
    public function Refundinfor(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr1['detailid'] = $data['detailid'];
        $parr1['status'] = $data['status'];
        $parr1['type'] = $data['type'];
        $parr1['reason'] = $data['reason'];
        $parr1['remarks'] = $data['remarks'];
        if(!empty($data['imglist'])){
            //$imglist =	explode("|", $data['imglist']);
            $parr['img'] = $data['imglist'];
        }
        if (substr($parr1['detailid'],0,1) == 's') {
            $result = IGD('Supplyrefund', 'Agorder')->Refundinfor($parr1);
        } else {
            $result = IGD('Refund','Order')->Refundinfor($parr1);
        }
        $this->ajaxReturn($result);
    }
    // 产品评价
    public function evaluate()
    {
//      if (IS_POST && session('time') == $_POST['time']) {            
//          if ($_FILES) {
//              $imgresult = uploadimg('score');
//              if ($imgresult['code'] != 0) {
//                  $resultdata = $imgresult['msg'];
//                  echo "<script >mui.toast('$resultdata','frame')</script>";
//                  return;
//              }
//              $parr['img'] = array_values($imgresult['data']);
//          }
//
//          $parr['detailid'] = I('detailid');
//          $parr['score'] = I('score');
//          $parr['acode'] = I('acode');
//          $parr['content'] = I('content');
//          $result = IGD('Order','Order')->ProductScore($parr);
//          $resultdata = $result['msg'];
//          if ($result['code'] != 0) {
//              echo "<script >mui.toast('$resultdata','frame')</script>";
//              return;
//          }
//          session('time', null);
//          echo "<script>mui.toast('$resultdata','frame')</script>";
//          echo "<script>setTimeout(function(){window.location.href = 'javascript:history.go(-2)';},2000);</script>";
//          return;
//      }
        $this->detailid = I('detailid');
        $parr1['detailid'] = $this->detailid;
        if (substr($parr1['detailid'],0,1) == 's') {
            $detailresult = IGD('Supplyorder', 'Agorder')->FindDetailOne($parr1);
        } else {
            $detailresult = IGD('Order','Order')->FindDetailOne($parr1);
        }
        $this->detail = $detailresult['data'];

        $time = time();
        session('time', $time);    // 防止重复提交
        $this->assign('time', $time);
        $this->show();
    }
	
	/*商品评价*/
	public function ProductScore(){
		$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        $parr['detailid'] = $data['detailid'];
        $parr['score'] = $data['score'];
        $parr['acode'] = $data['acode'];
        $parr['content'] = $data['content'];
        if(!empty($data['imglist'])){
        	$imglist =	explode("|", $data['imglist']);
        	$parr['img'] = $imglist;
        }
        $result = IGD('Order','Order')->ProductScore($parr);
        $this->ajaxReturn($result);
	}

    //售后列表
    public function warranty() {
        $this->show();
    }

    // 获取维权列表
    public function Getrefundlist()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Refund','Order')->Getrefundlist($parr);
        $this->ajaxReturn($result);
    }

    // 售后详情
    public function warranty_info()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['rcode'] = I('rcode');
        if (substr($parr['rcode'],0,1) == 's') {
            $result = IGD('Supplyrefund', 'Agorder')->GetrefundInfo($parr);
        } else {
            $result = IGD('Refund','Order')->GetrefundInfo($parr);
        }
        $data = $result['data'];
        $this->subtime = strtotime('+1 weeks',strtotime($data['c_addtime'])) - time();
        $this->assign('data',$data);
        $this->show();
    }

    // 提交快递单号
    public function Updateexpress()
    {
        $parr['rcode'] = I('rcode');
        $parr['transcompany'] = I('transcompany');
        $parr['transno'] = I('transno');
        if (substr($parr['rcode'],0,1) == 's') {
            $result = IGD('Supplyrefund', 'Agorder')->Updateexpress($parr);
        } else {
            $result = IGD('Refund','Order')->Updateexpress($parr);
        }
        $this->ajaxReturn($result);
    }

    //协商详情
    public function confer_info()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['rcode'] = I('rcode');
        if (substr($parr['rcode'],0,1) == 's') {
            $result = IGD('Supplyrefund', 'Agorder')->Getrefundlog($parr);
        } else {
            $result = IGD('Refund','Order')->Getrefundlog($parr);
        }
        $this->assign('data',$result['data']);
        $this->show();
    }

    // 提醒发货
    public function RemindDeliver()
    {
        $orderid = I('orderid');
        $sign = S('orderid'.$orderid);
        if ($sign == 1) {
            $this->ajaxReturn(Message(0,'您已经提醒卖家发货了，请不要重复点击'));
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['orderid'] = $orderid;
        if (substr($orderid,0,1) == 's') {
            $result = IGD('Supplyorder', 'Agorder')->RemindDeliver($parr);
        } else {
            $result = IGD('Order','Order')->RemindDeliver($parr);
        }
        if ($result['code'] == 0) {
            S('orderid'.$orderid,1,3600);
        }
        $this->ajaxReturn($result);
    }
    
    //获取可抵扣订单的卡劵
    public function CouponUseList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['orderid'] = I('orderid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->CouponUseList($parr);
        $this->ajaxReturn($result);
    }   

    /*支付完成*/
    public function achieve(){
    	/*卡券轮播*/
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;

        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $fromucode;
        $parr['type'] = 2;
        $result = IGD('Advert','Newact')->GetShopAdvert($parr);
        $this->couponlist = $result['data'];
        
        /*支付完成页面获取订单详情*/  
        $parr1['ucode'] = session('USER.ucode');
        $parr1['orderid'] = I('orderid');
        if (substr($parr1['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder','Agorder');
            $result_a = $orderdb->GetOrderInfo($parr1);            
        	$this->isinfo = 0;
        } else if (substr($parr1['orderid'],0,1) == 'g') { //拼团订单
            $orderdb = IGD('Groupbuy','Newact');
            $result_a = $orderdb->AchievePayorderInfo($parr1);
            $this->isinfo = 1;/*区分是拼团订单0显示收货地址，1显示商家信息，同时配送方式为3*/
        } else if (substr($parr1['orderid'],0,1) == 'm') { //秒杀订单
            $orderdb = IGD('Order','Order');
            $result_a = $orderdb->GetOrderInfo($parr1);            
        	$this->isinfo = 0;
        } else {
            $orderdb = IGD('Order', 'Order');
            $result_a = $orderdb->GetOrderInfo($parr1);            
        	$this->isinfo = 0;
        }
        
        $this->orderdata = $result_a['data'];

        // if ($this->orderdata['c_pay_state'] != 1) {
        //     $jumpurl = GetHost(1) . '/index.php/Order/Index/payment?orderid=' . $this->orderdata['c_orderid'];
        //     header("Location:" . $jumpurl);die();
        // }
        
        if(empty($result_a['data']['c_bmoney'])){
        	$bmoney = 0.00;
        }else{
        	$bmoney = $result_a['data']['c_bmoney'];
        }if(empty($result_a['data']['banlace'])){
        	$banlace = 0.00;
        }else{
        	$banlace = $result_a['data']['banlace'];
        }
        
        $this->payth = round($result_a['data']['c_actual_price']-$result_a['data']['banlace']-$result_a['data']['c_bmoney'],2);
        
        
        $this->show();    	
    }

    /*银盛支付中转页面*/
    public function payachieve(){
        $parr['orderid'] = I('orderid');
        $parr['payrule'] = I('payrule');
        $result = IGD('Ysepay','Scanpay')->ChangeOrderPay($parr);       
        
        /*支付完成页面获取订单详情*/  
        $parr1['ucode'] = session('USER.ucode');
        $parr1['orderid'] = I('orderid');
        if (substr($parr1['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder','Agorder');
            $result_a = $orderdb->GetOrderInfo($parr1);            
            $this->isinfo = 0;
        } else if (substr($parr1['orderid'],0,1) == 'g') { //拼团订单
            $orderdb = IGD('Groupbuy','Newact');
            $result_a = $orderdb->AchievePayorderInfo($parr1);
            $this->isinfo = 1;/*区分是拼团订单0显示收货地址，1显示商家信息，同时配送方式为3*/
        } else if (substr($parr1['orderid'],0,1) == 'm') { //秒杀订单
            $orderdb = IGD('Order','Order');
            $result_a = $orderdb->GetOrderInfo($parr1);            
            $this->isinfo = 0;
        } else {
            $orderdb = IGD('Order', 'Order');
            $result_a = $orderdb->GetOrderInfo($parr1);            
            $this->isinfo = 0;
        }
        
        $this->orderdata = $result_a['data'];

        /*卡券轮播*/
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $this->orderdata['c_acode'];
        $parr['type'] = 2;
        $result = IGD('Advert','Newact')->GetShopAdvert($parr);
        $this->couponlist = $result['data'];
        
        if(empty($result_a['data']['c_bmoney'])){
            $bmoney = 0.00;
        }else{
            $bmoney = $result_a['data']['c_bmoney'];
        }if(empty($result_a['data']['banlace'])){
            $banlace = 0.00;
        }else{
            $banlace = $result_a['data']['banlace'];
        }
        
        $this->payth = round($result_a['data']['c_actual_price']-$result_a['data']['banlace']-$result_a['data']['c_bmoney'],2);
        
        
        $this->display('achieve');      
    }

    /*平安确认支付*/
    public function payment1() {
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

        $this->show();
    }

    //新版平安支付获取支付参数
    public function GetPayInfo1($parr)
    {
    	vendor('Papay.webApp');
		$webApp = new \webApp(C('ONPAOPEN_ID'),C('ONPAOPEN_KEY'));   
        
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
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
        if ($result['code'] != 0) {
            return Message(3000,'订单信息不存在');
        }
        $orderinfo = $result['data'];
        // $mch_id = $orderinfo['mch_id'];

        $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);
        $money = bcsub($sum, $orderinfo['c_actual_price'], 2);
        if ($money <= 0) {
            return Message(3001,'该订单不能进行支付');
        }
        $tempmoney = (string) ($money * 100);

        $tempproductname = "";
        $details = $orderinfo['detail'];
        for ($i = 0; $i < count($details); $i++) {
            $tempproductname = $tempproductname . $details[$i]['c_pname'] . "|";
        }

        //微信支付宝授权openid
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $openid = $alipayinfo['openid'];
        } else {
            $openid = session('openid');
        }

        $result = $payment->CreatePayorder($orderid);
        if ($result['code'] != 0) {
            return Message(3002,'支付参数错误');
        }
        $payorderid = $result['data'];

        if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
            $pmt_tag = 'Weixin';
            $notify_url = GetHost(1).'/index.php/Order/Weixin/papaynotify';
        }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
            $pmt_tag = 'Alipay2';
            $notify_url = GetHost(1).'/index.php/Order/Alipay/papaynotify';
            // if (is_aliApp()) {
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $orderid));
            // }
        } else {
            $this->ajaxReturn(Message(2002,'非法支付'));
        }
        
        $data['open_id'] = $openid;   //用户唯一标识
        $data['timestamp'] = time();   //时间戳
        $data['out_no'] = $payorderid; //订单号
        $data['pmt_tag'] = $pmt_tag;    //支付标识 Weixin,Alipay2
        $data['pmt_name'] = '微领地小蜜';    //自定义付款方式名 
        $data['ord_name'] = $tempproductname;    //订单描述
        $data['original_amount'] = $tempmoney;
        $data['trade_amount'] = $tempmoney;   //实际交易金额(分为单位)   
        $data['notify_url'] = $notify_url;
        $data['jump_url'] = GetHost(1).'/index.php/Order/Index/achieve?orderid='.$orderid;
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

    /**
     * 平安余额支付模块
     */
    public function BalancePay1()
    {
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        $parr['money'] = I('money');
        $parr['payrule'] = I('payrule');
        $parr['bid'] = I('bid');
        $parr['bmoney'] = I('bmoney');
        if ($parr['money'] > 0 || (!empty($parr['bid']) && $parr['bmoney']>0)) {   //输入的余额大于0,启用余额支付            
            if (substr($parr['orderid'],0,1) == 'g') {   //拼团订单
                $mpinfo = IGD('Groupbuy','Newact')->BalancePay($parr);
            } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $mpinfo = IGD('Seckill','Newact')->BalancePay($parr);
            } else {
                //获取订单信息
                $payment = IGD('Order', 'Order');
                $result = $payment->GetPayorderinfo($parr);
                $orderinfo = $result['data'];

                $money = I('money');
                $balance = IGD('Money','User');
                $mpinfo = $balance->BalancePay($orderinfo,$money);
            }
        }

        if ($mpinfo['code'] == 0) {    //余额支付成功 
            $this->ajaxReturn($this->GetPayInfo1($parr));
        } else {
            $this->ajaxReturn($mpinfo);
        }
    }

    /*中信银行确认支付*/
    public function payment6() {
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

        $this->show();
    }

    //中信银行获取支付参数
    public function GetPayInfo6($parr)
    {
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
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
        if ($result['code'] != 0) {
            return Message(3000,'订单信息不存在');
        }
        $orderinfo = $result['data'];
        // $mch_id = $orderinfo['mch_id'];

        $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);
        $money = bcsub($sum, $orderinfo['c_actual_price'], 2);
        if ($money <= 0) {
            return Message(3001,'该订单不能进行支付');
        }
        $tempmoney = (string) ($money * 100);

        $tempproductname = "";
        $details = $orderinfo['detail'];
        for ($i = 0; $i < count($details); $i++) {
            $tempproductname = $tempproductname . $details[$i]['c_pname'] . "|";
        }

        //微信支付宝授权openid
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $openid = $alipayinfo['openid'];
        } else {
            $openid = session('openid');
        }

        $result = $payment->CreatePayorder($orderid);
        if ($result['code'] != 0) {
            return Message(3002,'支付参数错误');
        }
        $payorderid = $result['data'];

        if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
            vendor('zxinpay.weixin.Request');
            $Request = new \Request();
            $params['method'] = 'submitOrderInfo';
            $params['is_raw'] = 1;
            $params['limit_credit_pay'] = 1;
            $params['body'] = $tempproductname;  //商品描述
            $params['mch_create_ip'] = get_client_ip();  //客户端ip地址
            $params['out_trade_no'] = $payorderid;  //商户单号
            $params['sub_appid'] = C('APPID');
            $params['sub_openid'] = $openid;     //用户openid  $openid
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
                $params['buyer_id'] = $openid;     //用户openid  
                $params['limit_credit_pay'] = 1;
                $params['body'] = $tempproductname;  //商品描述
                $params['mch_create_ip'] = get_client_ip();  //客户端ip地址
                $params['out_trade_no'] = $payorderid;  //商户单号
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
        $this->ajaxReturn(MessageInfo(0,'生成订单成功',$orderid));
    }

    /**
     * 中信银行余额支付模块
     */
    public function BalancePay6()
    {
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        $parr['money'] = I('money');
        $parr['payrule'] = I('payrule');
        $parr['bid'] = I('bid');
        $parr['bmoney'] = I('bmoney');
        if ($parr['money'] > 0 || (!empty($parr['bid']) && $parr['bmoney']>0)) {   //输入的余额大于0,启用余额支付            
            if (substr($parr['orderid'],0,1) == 'g') {   //拼团订单
                $mpinfo = IGD('Groupbuy','Newact')->BalancePay($parr);
            } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $mpinfo = IGD('Seckill','Newact')->BalancePay($parr);
            } else {
                //获取订单信息
                $payment = IGD('Order', 'Order');
                $result = $payment->GetPayorderinfo($parr);
                $orderinfo = $result['data'];

                $money = I('money');
                $balance = IGD('Money','User');
                $mpinfo = $balance->BalancePay($orderinfo,$money);
            }
        }

        if ($mpinfo['code'] == 0) {    //余额支付成功 
            $this->ajaxReturn($this->GetPayInfo6($parr));
        } else {
            $this->ajaxReturn($mpinfo);
        }
    }

    /*微众确认支付*/
    public function payment8() {
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

        $this->show();
    }

    //微众获取支付参数
    public function GetPayInfo8($parr)
    {
        vendor('Beecldpay.Pay');
        $Pay = new \Pay();
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
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
        if ($result['code'] != 0) {
            return Message(3000,'订单信息不存在');
        }
        $orderinfo = $result['data'];
        // $mch_id = $orderinfo['mch_id'];

        $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);
        $money = bcsub($sum, $orderinfo['c_actual_price'], 2);
        if ($money <= 0) {
            return Message(3001,'该订单不能进行支付');
        }
        $tempmoney = intval(bcmul($money,100,2));

        $tempproductname = "";
        $details = $orderinfo['detail'];
        for ($i = 0; $i < count($details); $i++) {
            $tempproductname = $tempproductname . $details[$i]['c_pname'] . "|";
        }

        $tempproductname = subtext($tempproductname,12);

        //微信支付宝授权openid
        $alipayinfo = session('alipay_authinfo');
        if ($alipayinfo) {
            $openid = $alipayinfo['openid'];
        } else {
            $openid = session('openid');
        }

        $result = $payment->CreatePayorder($orderid);
        if ($result['code'] != 0) {
            return Message(3002,'支付参数错误');
        }
        $payorderid = $result['data'];

        if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
            $params['title'] = $tempproductname;  //商品描述
            $params['bill_no'] = $payorderid;  //商户单号
            $params['openid'] = $openid;     //用户openid  $parr['openid']
            $params['total_fee'] = $tempmoney;   //总金 额
            $params['notify_url'] = GetHost(1)."/index.php/Order/Weixin/weizhongnotify";
            $params['type'] = 'WX_JSAPI';
            $result = $Pay->createorderinfo($params);
            $data1 = objarray_to_array($result);

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
                $params['bill_no'] = $payorderid;  //商户单号
                $params['openid'] = $openid;     //用户openid  $parr['openid']
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
        $this->ajaxReturn(MessageInfo(0,'生成订单成功',$orderid));
    }

    /**
     * 微众余额支付模块
     */
    public function BalancePay8()
    {
        header('Content-type: text/json');
        $parr['orderid'] = I('orderid');
        $parr['ucode'] = session('USER.ucode');
        $parr['money'] = I('money');
        $parr['payrule'] = I('payrule');
        $parr['bid'] = I('bid');
        $parr['bmoney'] = I('bmoney');
        if ($parr['money'] > 0 || (!empty($parr['bid']) && $parr['bmoney']>0)) {   //输入的余额大于0,启用余额支付            
            if (substr($parr['orderid'],0,1) == 'g') {   //拼团订单
                $mpinfo = IGD('Groupbuy','Newact')->BalancePay($parr);
            } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $mpinfo = IGD('Seckill','Newact')->BalancePay($parr);
            } else {
                //获取订单信息
                $payment = IGD('Order', 'Order');
                $result = $payment->GetPayorderinfo($parr);
                $orderinfo = $result['data'];

                $money = I('money');
                $balance = IGD('Money','User');
                $mpinfo = $balance->BalancePay($orderinfo,$money);
            }
        }

        if ($mpinfo['code'] == 0) {    //余额支付成功 
            $this->ajaxReturn($this->GetPayInfo8($parr));
        } else {
            $this->ajaxReturn($mpinfo);
        }
    }


    // 用户删除订单  逻辑删除
    public function DelOrder(){
        $ucode = session('USER.ucode');
        $orderid = I('orderid');
        $w['c_ucode']=$ucode;
        $w['c_orderid'] =$orderid;
        $find =M('Order')->where($w)->find();

        if(($find['c_order_state']==1 && $find['c_pay_state']==0 && $find['c_delivery']!=3) ||
            ($find['c_order_state'] ==2 && $find['c_pay_state']==1 && $find['c_deliverystate']==5 && $find['c_delivery']!=3)){
            $save['c_order_state'] =4;
            $res =M('Order')->where($w)->save($save);
            if(!$res){
                $this->ajaxReturn(Message(1001,"删除失败"));
            }
        }else{
            $this->ajaxReturn(Message(0,"该订单不能删除"));
        }

        $this->ajaxReturn(Message(0,"删除成功"));


    }

}