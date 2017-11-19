<?php

namespace Order\Controller;

use Base\Controller\CheckController;

/**
 * 用户订单模块
 */
class IndexController extends CheckController {

    //获取线上订单首页数量信息
    public function GetOrderNum()
    {
        $ucode = $this->ucode;
        $parr['ucode'] = $ucode;
        $result = IGD('Order', 'Order')->orderCountInfo($parr);
        $this->ajaxReturn($result);
    }

    //供支付选择面对面收货方式
    public function SelectDelivery()
    {
        $ucode = $this->ucode;
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

    //创建订单
    public function CreataOrder() {
        $ucode = $this->ucode;

        $parr['ucode'] = $ucode;
        $parr['delivery'] = I('delivery');
        $parr['addressid'] = I('addressid');
        $parr['postscript'] = urldecode($_POST['postscript']);
        $parr['money'] = I('money');
        $parr['produce'] = urldecode($_POST['produce']);
        $parr['model'] = I('model');//1安卓，2IOS，3微信
        $parr['bid'] = I('bid');

        $produce = objarray_to_array(json_decode($parr['produce']));
        //获取商家类型
        $pcode = $produce[0]['pcode'];
        $source = IGD('Storeorder', 'Order')->get_shoptype($pcode);

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
        $parrc['ucode'] = $ucode;
        foreach ($produce as $key => $value) {
            $parrc['pcode'] = $value['pcode'];
            $parrc['mcode'] = $value['mcode'];
            $cardb->DeleCar($parrc);
        }

        $this->ajaxReturn($result);
    }

    //获取用户订单详情
    public function GetOrderinfo() {
        $ucode = $this->ucode;
        $parr['ucode'] = $ucode;

        $parr['orderid'] = I('orderid');
        if (substr($parr['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder', 'Agorder');
        } else {
            $orderdb = IGD('Order', 'Order');
        }
        $result = $orderdb->GetOrderInfo($parr);
        $this->ajaxReturn($result);
    }

    //获取订单列表
    public function GetOrderList() {
        $ucode = $this->ucode;
        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['type'] = I('type');
        $source = I('source');
        $parr['source'] = !empty($source) ? $source : 1;
        $orderdb = IGD('Order', 'Order');
        $result = $orderdb->GetOrderList($parr);
        $this->ajaxReturn($result);
    }

    //获取用户订单详情仅支付使用
    public function GetPayorderinfo()
    {
        $ucode = $this->ucode;
        $parr['ucode'] = $ucode;

        $parr['orderid'] = I('orderid');
        if (substr($parr['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder','Agorder');
        } else if (substr($parr['orderid'],0,1) == 'g') { //拼团订单
            $orderdb = IGD('Groupbuy','Newact');
        } else if (substr($parr['orderid'],0,1) == 'm') { //秒杀订单
            $orderdb = IGD('Seckill','Newact');
        } else {
            $orderdb = IGD('Order', 'Order');
        }

        $result = $orderdb->GetPayorderinfo($parr);
        $this->ajaxReturn($result);
    }

    //获取用户订单详情仅支付使用
    public function AchievePayorderInfo()
    {
        $ucode = $this->ucode;
        $parr['ucode'] = $ucode;

        //银盛支付手动回调支付成功
        $orderid = I('orderid');
        $payrule = I('payrule');
        if (!empty($payrule)) {
            $parr1['orderid'] = $orderid;
            $parr1['payrule'] = $payrule;
            $result = IGD('Ysepay','Scanpay')->ChangeOrderPay($parr1);
        }

        $parr['orderid'] = I('orderid');
        if (substr($parr['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder','Agorder');
            $result = $orderdb->GetOrderInfo($parr);
        } else if (substr($parr['orderid'],0,1) == 'g') { //拼团订单
            $orderdb = IGD('Groupbuy','Newact');
            $result = $orderdb->AchievePayorderInfo($parr);
        } else if (substr($parr['orderid'],0,1) == 'm') { //秒杀订单
            $orderdb = IGD('Order','Order');
            $result = $orderdb->GetOrderInfo($parr);
        } else {
            $orderdb = IGD('Order', 'Order');
            $result = $orderdb->GetOrderInfo($parr);
        }
        
        $this->ajaxReturn($result);
    }

    //微信支付
    public function WeixinPay() {
        $ucode = $this->ucode;

        $orderid = I('orderid');
        $parr['ucode'] = $ucode;
        $parr['orderid'] = $orderid;
        $money = I('money');

        //卡劵信息
        $bid = I('bid');
        $bmoney = I('bmoney');

        if (substr($parr['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder','Agorder');
        } else if (substr($parr['orderid'],0,1) == 'g') { //拼团订单
            $orderdb = IGD('Groupbuy','Newact');
        } else if (substr($parr['orderid'],0,1) == 'm') { //秒杀订单
            $orderdb = IGD('Seckill','Newact');
        } else {
            $orderdb = IGD('Order', 'Order');
        }

        $result = $orderdb->GetPayorderinfo($parr);

        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }

        $orderinfo = $result['data'];

        if ($orderinfo['c_pay_state'] != 0 && $orderinfo['c_pay_state'] != 2) {
            $this->ajaxReturn(Message(1021, "该订单已经支付或已经取消"));
        }
        //余额支付判断
        if($money > 0 || (!empty($bid) && !empty($bmoney))){
            if (substr($parr['orderid'],0,1) == 'g') {   //拼团订单
                $parr['money'] = $money;
                $result = IGD('Groupbuy','Newact')->BalancePay($parr);
            } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $parr['money'] = $money;
                $result = IGD('Seckill','Newact')->BalancePay($parr);
            } else {
                $result = IGD('Money', 'User')->BalancePay($orderinfo,$money,$bid,$bmoney);
            }

            if($result['code'] != 0 && $result['code'] != 10086) {
                $this->ajaxReturn($result);
            }

            if($result['code'] == 10086){
                $returndata["appid"] = C('WXAPPID');
                $returndata["mchid"] = C('WXMCHID');
                $returndata["appwd"] = C('WXAPPPWD');
                $returndata["prepay_id"] = $orderid;
                $returndata["noncestr"] = "";
                $returndata["time"] = $time;
                $returndata["sing"] = $sing;
                $returndata["yfmoney"] = '0';

                $this->ajaxReturn(MessageInfo(0, "余额支付订单完成", $returndata));
            }

            //重新查询订单信息
            $result = $orderdb->GetPayorderinfo($parr);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result);
            }

            $orderinfo = $result['data'];
        }

        if ($orderinfo['c_pay_state'] != 0 && $orderinfo['c_pay_state'] != 2) {
            $this->ajaxReturn(Message(1021, "该订单已经支付或已经取消"));
        }

        $detail = $orderinfo['detail'];

        $tempproductname = "";

        foreach ($detail as $key => $value) {
            $tempproductname = $tempproductname . $value['c_pname'] . ",";
        }

        //计算价格
        $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);

        $shenyu = bcsub($sum, $orderinfo['c_actual_price'], 2);
        $tempmoney = (string) ($shenyu * 100);

        $returnnotify = C('WXNotify');

        //引入微信支付
        vendor('WxSjPayPubHelper.Weixinpay');

        $wx_obj = new \Wxpay(array(
            'body' => $tempproductname, //商品名
            'out_trade_no' => $orderid, //订单号
            'total_fee' => $tempmoney,
            'attach' => $tempproductname,
            'limit_pay'=>'no_credit',
            'notify_url' => $returnnotify //异步通知页面url
        ));

        $wx_return_data = $wx_obj->run();

//        //日志记录
//        $wx_log_path = RUNTIME_PATH . '/Wx_log/';
//
//        if (!is_dir($wx_log_path))
//            mkdir($wx_log_path, 0777, true);
//
//        $wx_log_dir = $wx_log_path . date('Y-m-d') . '.log';
        //Log::write('订单编号为 ' . $need_info['order_index'] . '的订单，请求微信prepay_id支付，返回xml为：' . $wx_return_data, Log::DEBUG, Log::FILE, $wx_log_dir);
        //获取需要的值
        if ($wx_return_data === false) {
            $returnstr = Message(1001, "请求微信接口失败");
            $this->ajaxReturn($returnstr, 'JSON');
        }

        //匹配出return_code
        $pattern = '/<return_code><!\[CDATA\[([A-Z]{7})\]\]><\/return_code>/iU';
        $info = preg_match_all($pattern, $wx_return_data, $matches_code);


        if ($info) {
            if ($matches_code[1][0] == 'SUCCESS') {
                $pattern = '/<prepay_id><!\[CDATA\[([\w]+)\]\]><\/prepay_id>/iU';
                $info_id = preg_match_all($pattern, $wx_return_data, $matches_id);
                if ($info_id) {
                    $prepay_id = $matches_id[1][0];
                    $returndata["appid"] = C('WXAPPID');
                    $returndata["mchid"] = C('WXMCHID');
                    $returndata["appwd"] = C('WXAPPPWD');
                    $returndata["prepay_id"] = $prepay_id;

                    $noncestr = md5(uniqid());
                    $time = time();
                    $sing = $this->_makeSign(C('WXAPPID'), C('WXMCHID'), $noncestr, $prepay_id, $time, C('WXAPPPWD'));

                    $returndata["noncestr"] = $noncestr;
                    $returndata["time"] = $time;
                    $returndata["sing"] = $sing;
                    $returndata["yfmoney"] = $shenyu;
                    $returnstr = MessageInfo(0, "生成与支付订单成功", $returndata);

                    $this->ajaxReturn($returnstr, 'JSON');
                } else
                    $returnstr = Message(1022, "匹配prepay_id失败");
                $this->ajaxReturn($returnstr, 'JSON');
            } else {
                $returnstr = Message(1022, "微信同一订单请求失败");
                $this->ajaxReturn($returnstr, 'JSON');
            }
        } else {
            $returnstr = Message(1022, "匹配状态码失败");
            $this->ajaxReturn($returnstr, 'JSON');
        }
    }

    //支付宝支付
    public function zhifubaoPay() {
        vendor('AntAlipay.Gateway');
        vendor('AntAlipay.Message');
        $AopClient = new \AopClient();
        $config = C('ALIPAYCONFIG');

        $ucode = $this->ucode;

        $parr['ucode'] = $ucode;
        $parr['orderid'] = I('orderid');
        $money = I('money');

        //卡劵信息
        $bid = I('bid');
        $bmoney = I('bmoney');

        if (substr($parr['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder','Agorder');
        } else if (substr($parr['orderid'],0,1) == 'g') { //拼团订单
            $orderdb = IGD('Groupbuy','Newact');
        } else if (substr($parr['orderid'],0,1) == 'm') { //秒杀订单
            $orderdb = IGD('Seckill','Newact');
        } else {
            $orderdb = IGD('Order', 'Order');
        }
        $result = $orderdb->GetPayorderinfo($parr);

        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }

        $orderinfo = $result['data'];
        if ($orderinfo['c_pay_state'] != 0 && $orderinfo['c_pay_state'] != 2) {
            $this->ajaxReturn(Message(1021, "该订单已经支付或已经取消"));
        }
        //余额支付判断
        if($money > 0 || (!empty($bid) && !empty($bmoney))){
            if (substr($parr['orderid'],0,1) == 'g') {   //拼团订单
                $parr['money'] = $money;
                $result = IGD('Groupbuy','Newact')->BalancePay($parr);
            } else if (substr($parr['orderid'],0,1) == 'm') {  //秒杀订单
                $parr['money'] = $money;
                $result = IGD('Seckill','Newact')->BalancePay($parr);
            } else {
                $result = IGD('Money', 'User')->BalancePay($orderinfo,$money,$bid,$bmoney);
            }

            if($result['code'] != 0 && $result['code'] != 10086) {
                $this->ajaxReturn($result);
            }

            if($result['code'] == 10086){
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

                //无关数据
                $retunr['activityprice'] = "";
                $retunr['orderid'] = $orderinfo['c_orderid'];
                $retunr['productname'] = "";
                $retunr['desc'] = "";
                $retunr['notify_url'] = "";
                $retunr['yfmoney'] = '0';

                $askurl = $this->_makeData(urlencode($retunr['app_id']),urlencode($retunr['biz_content']),urlencode($retunr['notify_url']),urlencode($retunr['timestamp']),urlencode($sign));
                $retunr['askurl'] = $askurl;

                $result = MessageInfo(0, "订单状态查询成功", $retunr);
                $this->ajaxReturn($result);
            }

            //重新查询订单信息
            $result = $orderdb->GetPayorderinfo($parr);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result);
            }

            $orderinfo = $result['data'];
        }

        if ($orderinfo['c_pay_state'] != 0 && $orderinfo['c_pay_state'] != 2) {
            $this->ajaxReturn(Message(1021, "该订单已经支付或已经取消"));
        }

        $detail = $orderinfo['detail'];

        $tempproductname = "";

        foreach ($detail as $key => $value) {
            $tempproductname = $tempproductname . $value['c_pname'] . ",";
        }

        //计算价格
        $sum = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);
        $shenyu = bcsub($sum, $orderinfo['c_actual_price'], 2);
        $activityprice = $shenyu;

        //新版银盛开户商家支付宝支付
        $payparr['ucode'] = $orderinfo['c_acode'];
        $result = IGD('Ysepay','Scanpay')->PayGetYsedata($payparr);
        if ($result['code'] == 0) {
            //无关数据
            $retunr['activityprice'] = $activityprice;
            $retunr['orderid'] = $orderinfo['c_orderid'];
            $retunr['productname'] = $tempproductname;
            $retunr['desc'] = $tempproductname;
            $retunr['yfmoney'] = $activityprice;

            $retunr['orderid'] = $orderinfo['c_orderid'];
            $retunr['open_url'] = GetHost(1).'/index.php/Order/Apppay/index?orderid='.$orderinfo['c_orderid'];
            $this->ajaxReturn(MessageInfo(0, "订单状态查询成功", $retunr));
        }

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

        //无关数据
        $retunr['activityprice'] = $activityprice;
        $retunr['orderid'] = $orderinfo['c_orderid'];
        $retunr['productname'] = $tempproductname;
        $retunr['desc'] = $tempproductname;
        $retunr['yfmoney'] = $activityprice;

        $result = MessageInfo(0, "订单状态查询成功", $retunr);
        $this->ajaxReturn($result);
    }

    //取消订单
    public function CancelOrder() {

        $ucode = $this->ucode;

        $parr['orderid'] = I('orderid');
        $parr['ucode'] = $ucode;

        if (substr($parr['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder', 'Agorder');
        } else {
            $orderdb = IGD('Order', 'Order');
        }
        $result = $orderdb->CancelOrder($parr);
        $this->ajaxReturn($result);
    }

    //确认订单
    public function Confirmorder() {
        $ucode = $this->ucode;

        $parr['orderid'] = I('orderid');
        $parr['ucode'] = $ucode;

        if (substr($parr['orderid'],0,1) == 's') {
            $orderdb = IGD('Supplyorder', 'Agorder');
        } else {
            $orderdb = IGD('Order', 'Order');
        }
        $result = $orderdb->Confirmorder($parr);
        $this->ajaxReturn($result);
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

    //拼接API密钥
    private function _makeSign($appid, $mchid, $noncestr, $prepayid, $timestamp, $key) {
        $str_config = "appid=$appid&noncestr=$noncestr&package=Sign=WXPay&partnerid=$mchid&prepayid=$prepayid&timestamp=$timestamp&key=$key";
        $sign_info = strtoupper(md5($str_config));
        return $sign_info;
    }

    // 提醒发货
    public function RemindDeliver()
    {
        $ucode = $this->ucode;

        $orderid = I('orderid');
        $sign = IGD('Common', 'Redis')->Rediesgetucode('orderid'.$orderid);
        if ($sign == 1) {
            $this->ajaxReturn(Message(0,'您已经提醒卖家发货了，请不要重复点击'));
        }
        $parr['ucode'] = $ucode;
        $parr['orderid'] = $orderid;
        if (substr($orderid,0,1) == 's') {
            $result = IGD('Supplyorder', 'Order')->RemindDeliver($parr);
        } else {
            $result = IGD('Order','Order')->RemindDeliver($parr);
        }
        if ($result['code'] == 0) {
            IGD('Common', 'Redis')->RediesStoreSram('orderid'.$orderid,1,3600);
        }
        $this->ajaxReturn($result);
    }

    public function LocalShop()
    {
        $parr['orderid'] = I('orderid');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['slongitude'] = I('slongitude');
        $parr['slatitude'] = I('slatitude');
        $parr['klongitude'] = I('klongitude');
        $parr['klatitude'] = I('klatitude');
        $result = IGD('Order','Order')->LocalShop($parr);
        $this->ajaxReturn($result);
    }

    // 用户删除订单  逻辑删除
    public function DelOrder(){
        $ucode = $this->ucode;
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
