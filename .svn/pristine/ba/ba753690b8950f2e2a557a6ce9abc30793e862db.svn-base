<?php

/**
 * 代理商微商服务费相关接口
 */
class AgentOrder {

	/**
	 * 创建订单
     * @param money,ucode,acode,balance,payrule,sid
	 */
    function CreateScanpayOrder($parr) {
        //查询商家信息
        $acodewhere['c_ucode'] = $parr['acode'];
        $acodedata = M('Users')->where($acodewhere)->find();
        if (!$acodedata) {
            return Message(3000, '该代理商信息不存在');
        }

        $ncode = CreateOrder('f');
        if (empty($parr['ucode'])) {
            return Message(1009, '清先登录再操作');
        }

        $userwhere['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($userwhere)->find();
        if ($userinfo['c_isagent'] != 2) {
            return Message(3001, '代理身份才能提交服务费');
        }

        if ($userinfo['c_acode'] != $parr['acode']) {
            return Message(3002,'上级代理编码不符合');
        }
        $scandata['c_ucode'] = $userinfo['c_ucode'];
   

        if ($parr['balance'] > 0 && $parr['balance'] < $parr['money']) {
            $scandata['c_actual_price'] = $parr['balance'];
            $scandata['c_pay_rule'] = $parr['payrule'];
        } else if ($parr['balance'] >= $parr['money']) {
            $scandata['c_actual_price'] = $parr['money'];
            $scandata['c_pay_rule'] = 4;
        }else if(empty($parr['balance']) || $parr['balance'] == 0){
        	$scandata['c_pay_rule'] = $parr['payrule'];
        }

        //查询商家资料信息
        $agw['c_id'] = $parr['sid'];
        $angentinfo = M('Check_shopinfo')->where($agw)->find();
        if ($angentinfo['c_checked'] != 0) {
            return Message(3003,'您已通过审核，不能再操作');
        }

        $db = M('');
        $db->startTrans();

        //计算利润
        $scandata['c_commission'] = 0; 
        $scandata['c_profit'] = $parr['money']; 

        //写入订单信息
        $scandata['c_orderid'] = $ncode;
        $scandata['c_sid'] = $parr['sid'];
        $scandata['c_acode'] = $parr['acode'];
        $scandata['c_money'] = $parr['money'];
        $scandata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Order_agent')->add($scandata);
        if (!$result) {
            $db->rollback();
            return Message(3003, '生成订单失败');
        }

        if ($parr['balance'] > 0 && $parr['balance'] < $parr['money']) {
            $scandata['c_actual_price'] = $parr['balance'];
            $result = $this->BalanceOrder($ncode, 0);
            if ($result['code'] != 0) {
	            $db->rollback();
	            return $result;
        	}
        } else if ($parr['balance'] >= $parr['money']) {
            $scandata['c_actual_price'] = $parr['money'];
            $result = $this->BalanceOrder($ncode, 1);
            if ($result['code'] != 0) {
	            $db->rollback();
	            return $result;
        	}
        }

        $db->commit();
        $returninfo['ncode'] = $ncode;
        return MessageInfo(0, '添加成功', $returninfo);
    }

	/**
	 * 订单余额支付
	 * @param ncode,sign(1已支付)
	 */
    function BalanceOrder($ncode, $sign) {
        $w['c_orderid'] = $ncode;
        $orderinfo = M('Order_agent')->where($w)->find();
        if (!$orderinfo) {
            return Message('1001', '该订单不存在');
        }

        //用户金额操作
        $rebatemoney = IGD('Money', 'User');
        $parr['ucode'] = $orderinfo['c_ucode'];
        $parr['money'] = $orderinfo['c_actual_price'];
        $parr['source'] = 19;
        $parr['key'] = $ncode;
        $parr['desc'] = "支付微商服务费";
        $parr['state'] = 1;
        $parr['type'] = 0;
        $parr['isagent'] = 0;
        $parr['showimg'] = 'Uploads/settlementshow/gou1.png';
        $parr['showtext'] = '微商服务费';
        $result = $rebatemoney->OptionMoney($parr);
        if ($result['code'] != 0) {
            return $result;
        }

        //用户支付记录操作
        $result = IGD('Order', 'Order')->paylog($ncode, 4, $orderinfo['c_actual_price'], '', 6);
        if ($result['code'] != 0) {
            return $result;
        }

        if ($sign == 1) {
            //修改订单状态
            $scandata['c_pay_state'] = 1;
            $result = M('Order_agent')->where($w)->save($scandata);

            if (!$result) {
                return Message('1002', '修改订单状态失败');
            }

            $aparr['ucode'] = $orderinfo['c_ucode'];
            $aparr['sid'] = $orderinfo['c_sid'];
            $aparr['checked'] = 2;
            $result = $this->AgentCheckShop($aparr);
            if ($result['code'] != 0) {
                return $result;
            }

            $msgdata['ucode'] = $orderinfo['c_acode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '收到代理提交微商服务费:' . $orderinfo['c_actual_price'] . '元';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['issound'] = 1;
            IGD('Msgcentre', 'Message')->CreateMessege($msgdata);
        }
        return MessageInfo(0, '操作成功', $orderinfo);
    }

	/**
     * 第三方支付订单
     * @param
     */
    function PayOrder($parr)
    {
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
        $actualprice = $parr['actualprice'];
        $thirdpartynum = $parr['thirdpartynum'];
        $upay = $parr['upay'];

        $db = M('');
        $db->startTrans();

        $orderinfo = $this->GetOrderInfo($orderid)['data'];
        if (!$orderinfo) {
            return Message(3001, '该订单不存在');
        }

        if ($orderinfo['c_pay_state'] == 1) {
            return Message(3000, "该订单已支付");
        }

        //计算订单的总价
        $countprice = $orderinfo['c_money'];

        //计算支付总价
        $payzong = bcadd($actualprice, $orderinfo['c_actual_price'], 2);
        if ($payzong < $countprice) {
            return Message(1016, "您支付的金额不足");
        }

        //操作扫码支付订单
        $where['c_orderid'] = $orderid;
        $where['c_pay_state'] = 0;
        $save['c_actual_price'] = $countprice;
        $save['c_pay_rule'] = $payrule;
        $save['c_pay_state'] = 1;
        $result = M('Order_agent')->where($where)->save($save);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(3001, '操作订单状态失败');
        }

        //用户支付记录操作
        $result = IGD('Order', 'Order')->paylog($orderid, $payrule, $actualprice, $thirdpartynum, 6);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //商家获得利润
        $money = $orderinfo['c_money'];
        $paycontent = "收到代理提交微商服务费:". $money ."元，请尽快进行微商审核";
        if($money > 0){
	        $msgdata['ucode'] = $orderinfo['c_acode'];
	        $msgdata['type'] = 0;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['content'] = $paycontent;
	        $msgdata['tag'] = 2;
            $msgdata['issound'] = 1;
	        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        IGD('Msgcentre', 'Message')->CreateMessege($msgdata);
        }

        $aparr['ucode'] = $orderinfo['c_ucode'];
        $aparr['sid'] = $orderinfo['c_sid'];
        $aparr['checked'] = 2;
        $result = $this->AgentCheckShop($aparr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $date = $this->GetOrderInfo($orderid)['data'];
        $db->commit();
        return MessageInfo(0, '支付成功', $date);
    }

    /**
     * 市代审核微商
     * @param sid,(checked),ucode
     */
    function AgentCheckShop($parr)
    {
        $where['c_id'] = $parr['sid'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if ($angentinfo['c_checked'] != 0) {
            return Message(3003,'您已通过审核，不能再操作');
        }

        if ($parr['checked']==1) {
            $save['c_checked'] = 1;
        }elseif($parr['checked']==2){
            $save['c_checked'] = 2;
        }

        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($save);
        if (!$result) {
            return Message(2004,'操作失败');
        }

        // 创建区代消息
        $join = 'left join t_invite_code as b on a.c_fcode=b.c_code';
        $agwhere['a.c_ucode'] = $parr['ucode'];
        $agucode = M('Invite_code as a')->join($join)->where($agwhere)->getField('b.c_ucode');
        if (!$agucode) {
            return Message(2005,'代理商查询失败');
        }

        if ($angentinfo['c_type'] == 2) {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交微商申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交微商申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/info_3?isfixed='.$angentinfo['c_isfixed'].'&ctype='.$angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='".$agucode."'")->getField('c_phone');
                $parr1['ptitle'] = '企业【'.$angentinfo['c_company'].'】申请微商,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的微商提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3).'/agent.php/Home/Shopcheck/details?Id='.$parr['sid'];
            }
        } else {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交微商申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交微商申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/info_3?isfixed='.$angentinfo['c_isfixed'].'&ctype='.$angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='".$agucode."'")->getField('c_phone');
                $parr1['ptitle'] = '个人【'.$angentinfo['c_name'].'】申请微商,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的微商提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3).'/agent.php/Home/Shopcheck/details?Id='.$parr['sid'];
            }
        }


        $result = IGD('Infomation','Agent')->Create_information($parr1);
        if ($result['code'] != 0) {
            return Message(1000,'创建信息失败');
        }

        // 发送短信通知
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = IGD('Login', 'Agent');
        $returndata = $register->SendVerify($separr);
        return Message(0,'操作成功');
    }

    /**
     * 查询订单信息
     * @param orderid
     */
    function GetOrderInfo($orderid) 
    {
    	$where['c_orderid'] = $orderid;
    	$result = M('Order_agent')->where($where)->find();
    	if (!$result) {
    		return Message(3000,'订单信息不存');
    	}

        //查询商家名称
        $where1['c_ucode'] = $result['c_acode'];
        $shopinfo = M('Users')->where($where1)->field('c_nickname')->find();

        $where['c_orderid'] = $orderid;
        $where['c_source'] = 6;
        $where['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($where)->sum('c_money');
        $result['banlace'] = empty($banlacemoney)?0:$banlacemoney;
        $result['paymoney'] = bcsub($result['c_money'], $banlacemoney, 2);
        $result['c_anickname'] = $shopinfo['c_nickname'];
        return MessageInfo(0, '订单查询成功', $result);
    }

    /**
     * 取消订单
     * @param orderid
     */
    function CancelOrder($parr) {
        $ncode = $parr['orderid'];
        $ucode = $parr['ucode'];

        $whereorder['c_orderid'] = $ncode;
        $whereorder['c_ucode'] = $ucode;
        $orderinfo = M('Order_agent')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1000, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 0) {
            return Message(1001, '该订单无法取消');
        }

        $db = M('');
        $db->startTrans();

        $update['c_pay_state'] = -1;
        $whereorder['c_pay_state'] = array('NEQ','-1');
        $result = M('Order_agent')->where($whereorder)->save($update);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1001, '修改订单状态失败');
        }

        //返回用户余额
        if ($orderinfo['c_actual_price'] > 0) {
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $orderinfo['c_ucode'];
            $parr['money'] = $orderinfo['c_actual_price'];
            $parr['source'] = 9;
            $parr['key'] = $ncode;
            $parr['desc'] = "取消微商服务费余额支付";
            $parr['state'] = 1;
            $parr['type'] = 1;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/gou.png';
            $parr['showtext'] = '服务费返回';
            $result = $rebatemoney->OptionMoney($parr);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您的订单号：' . $ncode . '已取消成功，退还抵扣微商服务费' . $orderinfo['c_actual_price'] . '元成功';
            $msgdata['tag'] = 2;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $Msgcentre->CreateMessege($msgdata);
        }

        $db->commit();
        return Message(0, '操作成功');
    }

}
