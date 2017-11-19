<?php

/**
 * 	活动生成订单中心
 *
 */
class ActoderOrder {

	//创建订单信息
    function CreataOrderInfo($ucode,$activitydata,$prizedata)
    {
    	$orderid = CreateOrder('a');
    	$aorderinfo['c_orderid'] = $orderid;
    	$aorderinfo['c_ucode'] = $ucode;
    	//大转盘活动及老虎机活动
    	if ($activitydata['c_activitytype'] == 24 || $activitydata['c_activitytype'] == 25) {
    		$aorderinfo['c_pay_state'] = 1;
	        $aorderinfo['c_order_state'] = 2;
	        $aorderinfo['c_deliverystate'] = 0;
	        $aorderinfo['c_pay_rule'] = 5;
	        $aorderinfo['c_actual_price'] = 0;
	        $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

            if (!empty($prizedata['c_acode']) && $activitydata['c_activitytype'] == 3) {
                $userwhere['c_ucode'] = $ucode;
                $userinfo = M('Users')->where($userwhere)->field('c_isagent,c_acode')->find();
                //商家活动绑定归属代理商
                if ($userinfo['c_isagent'] == 0 && empty($userinfo['c_acode'])) {
                    $agentwhere['c_ucode'] = $prizedata['c_acode'];
                    $usersave['c_acode'] =  M('Users')->where($agentwhere)->getField('c_acode');
                    $result = M('Users')->where($userwhere)->save($usersave);
                    if (!$result) {
                        return Message(3001, "归属代理商失败");
                    }
                }

                $usertuijian = M('Users_tuijian')->where($userwhere)->find();
                //商家的活动产品绑定用户
                if (count($usertuijian) == 0) {
                    $add['c_ucode'] = $ucode;
                    $add['c_pcode'] = $prizedata['c_acode'];
                    $add['c_addtime'] = date('Y-m-d H:i:s', time());
                    $result = M('Users_tuijian')->add($add);
                    if (!$result) {
                        return Message(3002, "建立用户关系失败");
                    }
                }
            }
    	} else {
    		$aorderinfo['c_pay_state'] = 0;
	        $aorderinfo['c_order_state'] = 2;
	        $aorderinfo['c_deliverystate'] = 0;
    	}

    	$aorderinfo['c_activity_id'] = $activitydata['c_id'];
    	$aorderinfo['c_activity_name'] = $activitydata['c_activityname'];
    	$aorderinfo['c_acode'] = $prizedata['c_acode'];
        $aorderinfo['c_total_price'] = $prizedata['c_value'];
        $aorderinfo['c_delivery'] = 1;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }

        //创建订单详情
        $result = $this->CreataOrderdetails($ucode,$orderid,$prizedata);
        if ($result['code'] != 0) {
            return $result;
        }

        //创建订单地址
        $result = $this->CreataOrderAddress($ucode,$orderid);
        if ($result['code'] != 0) {
            return $result;
        }

        return MessageInfo(0, "订单创建成功",$orderid);
    }

	//生成订单详情
    function CreataOrderdetails($ucode,$orderid,$prizedata) {
    	$detailid = CreateOrder('da');
        $tempdetails['c_orderid'] = $orderid;
        $tempdetails['c_detailid'] = $detailid;
        $tempdetails['c_ucode'] = $ucode;
        $tempdetails['c_pcode'] = $prizedata['c_pcode'];
        $tempdetails['c_pprice'] = $prizedata['c_value'];
        $tempdetails['c_pname'] = $prizedata['c_name'];
        $tempdetails['c_pmodel_name'] = $prizedata['c_name'];
        $tempdetails['c_pnum'] = 1;
        $tempdetails['c_ptotal'] = $prizedata['c_value'];
        $imgpath = explode('|',$prizedata['c_img']);
        $tempdetails['c_pimg'] = $imgpath[0];
        $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order_details')->add($tempdetails);
        if (!$result) {
            return Message(1000, "生成订单详情失败");
        }

        return MessageInfo(0, "订单详情生成成功");
    }


    //生成订单地址
    function CreataOrderAddress($ucode,$orderid) {
        $addresswhere['c_is_default'] = 1;
        $addresswhere['c_ucode'] = $ucode;
        $useraddress = M('Users_address')->where($addresswhere)->order('c_addtime desc')->find();
        if (count($useraddress) == 0) {
            return Message(3006, '请传入用户地址');
        }

        $orderaddress['c_orderid'] = $orderid;
        $orderaddress['c_consignee'] = $useraddress['c_consignee'];
        $orderaddress['c_telphone'] = $useraddress['c_mobile'];
        $orderaddress['c_address'] = $useraddress['c_address'];
        $orderaddress['c_province'] = $useraddress['c_provincename'];
        $orderaddress['c_cityname'] = $useraddress['c_cityname'];
        $orderaddress['c_district'] = $useraddress['c_districtname'];
        $orderaddress['c_provinceid'] = $useraddress['c_province'];
        $orderaddress['c_cityid'] = $useraddress['c_city'];
        $orderaddress['c_districtid'] = $useraddress['c_district'];
        $result = M('Order_address')->add($orderaddress);
        if (!$result) {
            return Message(3007, '生成订单地址失败');
        }
        return Message(0, "订单地址生成成功");
    }

}