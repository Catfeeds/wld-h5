<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\AuthController;


/**
 * 支付确认页面
 */
class ApppayController extends AuthController {

    //初始化
    public function index() {
    	Vendor('Ysepay.Yse_pay');
		$pay = new \Yse_pay();

		//获取订单信息
        $orderid = I('orderid');
        $parr['orderid'] = I('orderid');
        if (substr($orderid,0,1) == 'n') {   //扫码支付信息获取
            $result = IGD('Scanpay', 'Scanpay')->FindScanpayOrder($orderid);
            $resultdata = $result['data'];
            if ($resultdata['c_pay_state'] != 0) {
                $this->error("该订单不能进行支付");return;
            }
            $orderinfo = $resultdata;
            $activityprice = bcsub($resultdata['c_money'], $resultdata['c_actual_price'], 2);
            if ($activityprice <= 0) {
                $this->error("该订单不能进行支付");return;
            }
            $tempproductname = trim($resultdata['c_anickname']).'的小店线下订单';
            $payorderid = $orderid;
            $return_url = GetHost(1)."/index.php/Order/Scanpay/success?orderid=".$orderid;
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

            //生成临时支付单号
            $result = $orderdb->CreatePayorder($orderid);
            if ($result['code'] != 0) {
                return Message(3002,'支付参数错误');
            }
            $payorderid = $result['data'];
            $return_url = GetHost(1)."/index.php/Order/Index/achieve?orderid=".$orderid;
        }

        $money = $activityprice;              //付款金额，必填

        //查询商家开户信息
        $parra['ucode'] = $orderinfo['c_acode'];
        $result = IGD('Ysepay','Scanpay')->PayGetYsedata($parra);
        if ($result['code'] != 0) {
            $this->error('商家资料审核中，暂不能支付');
        }
        $yseinfo = $result['data'];

        $alparr['partner_id'] = "wld17375717292";
        $alparr['notify_url'] = GetHost(1)."/index.php/Order/Alipay/respond_alipay_notify";
        $alparr['return_url'] = $return_url;
        $alparr['out_trade_no'] = $payorderid;             //订单号,自行生成;
        $alparr['subject'] = $tempproductname;
        $alparr['total_amount'] = $money;          //交易金额
        $alparr['seller_id'] = $yseinfo['c_username']; // 收款方银盛支付用户名
        $alparr['seller_name'] = $yseinfo['c_person']; // 授权方银盛支付客户名
        // $alparr['seller_id'] = "wld17375717292";
        // $alparr['seller_name'] = "长沙微领地网络科技有限公司";
        $data = $pay->alipay_H5($alparr);

        $def_url = "";
        while ($param = each($data)) {
            $def_url .= "<input type = 'hidden' id='" . $param['key'] . "' name='" . $param['key'] . "' value='" . $param['value'] . "' />";
        }
        
        $this->def_url = $def_url;
        $this->money = $money;
        $this->storename = '微领地小蜜';
        $this->orderid = $orderid;
        $this->action = "https://openapi.ysepay.com/gateway.do";
        $this->show();
    }

}
?>

