<?php 

/**
*  银盛支付相关接口
*/
class YsepayScanpay {

    /**
     * 查询用户银盛开户信息
     * @param ucode
     */
    function GetYsedata($parr)
    {
        // if ($parr['ucode'] != 'xmwde5355c819a63292' && $parr['ucode'] != 'wldbb398381d68728ca' && $parr['ucode'] != 'wld9313b0df9716b355') {
        //     return Message(3000,'没有相关信息');
        // }
        
        $where['c_ucode'] = $parr['ucode'];
        $where['c_openaccount'] = 1;
        $field = 'c_ucode,c_openaccount,c_reason,c_person,c_personphone,c_username';
        $data = M('User_yspay')->where($where)->field($field)->find();
        if (!$data) {
            return Message(3000,'没有相关信息');
        }

        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 支付时查询用户银盛开户信息
     * @param ucode
     */
    function PayGetYsedata($parr)
    {
        // if ($parr['ucode'] != 'xmwde5355c819a63292' && $parr['ucode'] != 'wldbb398381d68728ca' && $parr['ucode'] != 'wld9313b0df9716b355') {
        //     return Message(3000,'没有相关信息');
        // }

        //查询加盟连锁信息
        $unionresult = IGD('Chain','Store')->GetUnionInfo($parr);
        $unioninfo = $unionresult['data'];
        if (!$unioninfo['shopcode']) {
            $unioninfo['shopcode'] = $unioninfo['c_ucode'];
        }
        if ($unioninfo['c_type'] == 1) {   //连锁店支付收入进入总店
            $where['c_ucode'] = $unioninfo['shopcode'];
        } else {
            $where['c_ucode'] = $parr['ucode'];
        }
        
        $where['c_openaccount'] = 1;
        $field = 'c_ucode,c_openaccount,c_reason,c_person,c_personphone,c_username';
        $data = M('User_yspay')->where($where)->field($field)->find();
        if (!$data) {
            return Message(3000,'没有相关信息');
        }

        return MessageInfo(0,'查询成功',$data);
    }

    //代付提现手续费给商家
    function PayFeeTouser($parr)
    {
        $arr['sign'] = 1; // 1 代付 2 代扣
        $arr['type'] = 1; // 1  实时结算  2 按日结算  3 按月结算
        $arr['ucode'] = $parr['c_ucode'];  //操作用户编码
        $arr['orderid'] = CreateOrder('f');
        $arr['key'] = $parr['c_tx_code'];
        $arr['desc'] = '平台补回提现手续费';
        $arr['money'] = $this->calculateFee($parr['c_money']);
        $arr['source'] = 21; // 来源:1普城订单,2后台,3活动,4蜜城订单,5普城跨界,6提现,7注册,8老注册,9扫码,10转发,11绑定,12跨界扫码,13普城购返,14普城推返,15蜜城跨界,16普通退款,17蜜城退款,18红包,20充值,21手续费
        $res = IGD('Splitting','Order')->CreateRecord($arr);
        if($res['code']!=0){
            return Message(1001,'创建代扣失败');
        }

        return Message(0,'创建成功');
    }

    /**
     * 计算代付手续费
     */
    function calculateFee($money){
        if ($money<10000) {
            return 1;
        } else if($money>=10000 && $money<50000){
            return 0.8;
        } else {
            return 0.5;
        }
    }

    //查询订单状态并回调订单成功
    function ChangeOrderPay($parr)
    {
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];

        if (empty($orderid) || empty($payrule)) {
            return Message(3000,'查询参数异常');
        }
        Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
        $oparr['out_trade_no'] = $orderid;
        $data = $pay->searchOnlineOrder($oparr);
        $result = $pay->curl_query_order($data);

        $params = $result['ysepay_online_trade_query_response'];
        if ($params['trade_status'] == 'TRADE_SUCCESS') {
            $param['orderid'] = $params["out_trade_no"];
            $param['payrule'] = $payrule;
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

            if ($result['code'] == 0) {
                return MessageInfo(0,'支付成功',$orderid);
            } else {
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
                    return MessageInfo(0,'支付成功',$orderid);
                } else {
                    return MessageInfo(3002,'支付回调失败',$orderid);
                }
            }
        } else {
            return MessageInfo(3001,'订单未支付',$orderid);
        }
    }

}

?>