<?php

/**
 * 	集商品碎片活动
 *
 */
class ChipActivity {
    /**
     * APP领取碎片
     * @param ucode,chipid
     */
    function AppRecieveChip($parr)
    {
        $ucode = $parr['ucode'];
        $chipid = $parr['chipid'];

        //查询活动是否开始
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 7;
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(2000,'集碎片活动未开始');
        }

        //查询碎片
        $prizewhere['c_id'] = $chipid;
        $prizedata = M('Activity_prize')->where($prizewhere)->find();
        if (!$prizedata) {
            return Message(2001,'没有相应的碎片');
        }

        //查询用户是否领取
        $prizepcode['c_ucode'] = $ucode;
        $prizepcode['c_pid'] = $chipid;
        $userchip = M('Activity_log')->where($prizepcode)->find();
        if ($userchip) {
            return Message(2002,'您已经领取该碎片');
        }

        $db = M('');
        $db->startTrans();

        //开始领取碎片
        $addlog['c_ucode'] = $ucode;
        $addlog['c_aid'] = $activitydata['c_id'];
        $addlog['c_pid'] = $prizedata['c_id'];
        $addlog['c_fid'] = $findlog['c_id'];
        $addlog['c_pcode'] = $prizedata['c_pcode'];
        $addlog['c_type'] = $prizedata['c_type'];
        $addlog['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Activity_log')->add($addlog);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1029, "领取产品失败，请稍后再试");
        }

        //扣除剩余数量
        $whereprize['c_id'] = $prizedata['c_id'];
        $whereprize['c_aid'] = $activitydata['c_id'];
        $whereprize['c_num'] = array('GT', 0);
        $result = M('Activity_prize')->where($whereprize)->setDec('c_num', 1);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1030, "领取奖品失败，请稍后再试");
        }

        $db->commit();
        return Message(0,'领取成功');
    }

	/**
	 * 获取碎片展示信息
	 * @param ucode,
	 */
	function GetChipInfo($parr)
	{
		//查询活动
		$activitywhere['c_state'] = 1;
        $activitywhere['c_activitytype'] = 7;
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
        	return Message(2000,'集碎片活动未开始');
        }

        $activitydata['starttime'] = strtotime($activitydata['c_activitystarttime']) - time();
        $activitydata['endtime'] = strtotime($activitydata['c_activityendtime']) - time();

        // 查询碎片信息
        $prizewhere['c_pid'] = array('eq',0);
        $prizewhere['c_aid'] = $activitydata['c_id'];
        $prizewhere['c_type'] = 2;
        $chipproduce = M('Activity_prize')->where($prizewhere)->select();
        if (!$chipproduce) {
            return Message(2001,'没有相关碎片商品');
        }

        foreach ($chipproduce as $key => $value) {
            $prizepcode['c_ucode'] = $parr['ucode'];
            $prizepcode['c_pcode'] = $value['c_pcode'];
            $prizepcode['c_aid'] = $activitydata['c_id'];
            $deprizelog = M('Activity_log')->where($prizepcode)->group('c_pid')->select();
            $chipproduce[$key]['orderstatu'] = 0;

            $chipidarr = array();
            foreach ($deprizelog as $k => $v) {
                $chipidarr[$k] = $v['c_pid'];
                if (!empty($v['c_orderid'])) {
                    $chipproduce[$key]['orderstatu'] = 2;
                }
            }

            $chipwhere['c_pcode'] = $value['c_pcode'];
            $chipwhere['c_aid'] = $activitydata['c_id'];
            $prizewhere['c_pid'] = $value['c_id'];
            $chipwhere['c_id'] = array('in',implode(',',$chipidarr));
            $chipproduce[$key]['chip'] = M('Activity_prize')->where($chipwhere)->field('c_marks')->group('c_marks')->select();
            $chipproduce[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
            if (count($chipproduce[$key]['chip']) == 9 && $chipproduce[$key]['orderstatu'] != 2) {
                $chipproduce[$key]['orderstatu'] = 1;
            }
        }

        $activitydata['chipproduce'] = $chipproduce;
        return MessageInfo(0,'查询成功',$activitydata);
	}

	/**
	 * 兑换碎片
	 * @param aid,pcode,ucode
	 */
	function ExchangeChip($parr)
	{
		//查询活动
		$activitywhere['c_id'] = $parr['aid'];
        $activitywhere['c_activitytype'] = 7;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
        	return Message(2000,'集碎片活动未开始或已结束');
        }

        // 计算该碎片是否集齐
        $countwhere['c_pcode'] = $parr['pcode'];
        $countwhere['c_ucode'] = $parr['ucode'];
        $countwhere['c_aid'] = $activitydata['c_id'];
        $shenyu = M('Activity_log')->where($countwhere)->group('c_pid')->select();
        foreach ($shenyu as $key => $value) {
        	if (!empty($value['c_orderid'])) {
        		return Message(2001,'该碎片已兑换商品');
        	}
        }

        if (count($shenyu) < 9) {
        	return Message(2002,'该商品碎片未集齐');
        }

        //查询碎片产品
        $prwhere['c_state'] = 1;
        $prwhere['c_type'] = 2;
        $prwhere['c_pid'] = array('eq',0);
        $prwhere['c_pcode'] = $parr['pcode'];
        $prwhere['c_aid'] = $activitydata['c_id'];
    	$info = M('Activity_prize')->where($prwhere)->find();
    	if (!$info) {
    		return Message(2003,'该碎片兑换的商品已不存在');
    	}

		$db = M('');
        $db->startTrans();

		//生成订单
        $parr['ucode'] = $parr['ucode'];
        $parr['acode'] = $info['c_acode'];
        $parr['marketprice'] = $info['c_value'];
        $parr['name'] = $info['c_name'];
        $parr['total'] = $info['c_value'];
        $parr['c_imgpath'] = $info['c_imgpath'];
        $parr['activityid'] = $activitydata['c_id'];
        $parr['activityname'] = $activitydata['c_activityname'];
        $result = $this->CreateOrder($parr);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //修改记录表
        $save['c_orderid'] = $result['data']['orderid'];
        $where['c_ucode'] = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];
        $where['c_aid'] = $activitydata['c_id'];
        $result = M('Activity_log')->where($where)->save($save);
        if (!$result) {
        	$db->rollback(); //不成功，则回滚
        	return Message(2004,'该商品碎片转入订单失败');
        }

        //扣除碎片商品库存
        $prwhere['c_num'] = array('GT', 0);
        $result = M('Activity_prize')->where($prwhere)->setDec('c_num', 1);;
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(2004,'该商品已被领取完');
        }

        $db->commit();
        $returninfo['orderid'] = $save['c_orderid'];
        return MessageInfo(0,'兑换成功',$returninfo);
	}

	/**
     *  获取最新的20条获奖数据
     *  @param
     */
    function GetAwardList($parr)
    {
        $join = 'inner join t_activity_prize as b on a.c_pid=b.c_id';
        $where[] = array('a.c_aid in (select c_id from t_activity where c_activitytype=7)');
        $where['a.c_type'] = array('neq',3);
        $field = 'a.c_id,a.c_ucode,a.c_addtime as time,b.c_name,b.c_value,b.c_type';
        $data = M('Activity_log as a')->join($join)->where($where)->field($field)->order('a.c_addtime desc')->limit(20)->select();
        if (!$data) {
            return Message(3000,'没有相关数据');
        }
        foreach ($data as $key => $value) {
            $userwhere['c_ucode'] = $value['c_ucode'];
            $data[$key]['name'] = M('Users')->where($userwhere)->getField('c_nickname');
            $data[$key]['time'] = date('Y-m-d',strtotime($value['time']));
            if ($value['c_type'] == 1) {
                $data[$key]['praisecontent'] = $value['c_name'].'￥'.$value['c_value'];
            } else {
                $data[$key]['praisecontent'] = '获得一张'.$value['c_name'];
            }

        }
        return MessageInfo(0,'查询成功',$data);
    }

    //生成订单
    function CreateOrder($parr) {

        $orderid = CreateOrder("1");

        //生成订单详情
        $return = $this->CreataOrderdetails($parr, $orderid);

        if ($return['code'] != 0) {
            return $return;
        }

        //生成订单
        $return = $this->CreataOrderInfo($parr, $orderid);

        if ($return['code'] != 0) {
            return $return;
        }
        //生成订单地址
        $return = $this->CreataOrderAddress($parr, $orderid);
        if ($return['code'] != 0) {
            return $return;
        }

        $data['orderid'] = $orderid;
        return MessageInfo(0, "兑换成功", $data);
    }

    //生成订单详情
    protected function CreataOrderdetails($parr, $orderid) {

        $detailid = CreateOrder("d");
        $tempdetails['c_orderid'] = $orderid;
        $tempdetails['c_detailid'] = $detailid;
        $tempdetails['c_ucode'] = $parr['ucode'];
        $tempdetails['c_pcode'] = $parr['pcode'];
        $tempdetails['c_pprice'] = $parr['marketprice'];
        $tempdetails['c_pname'] = $parr['name'];
        $tempdetails['c_pmodel_name'] = $parr['name'];
        $tempdetails['c_pnum'] = 1;
        $tempdetails['c_ptotal'] = $parr['total'];
        $tempdetails['c_pimg'] = $parr['c_imgpath'];
        $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order_details')->add($tempdetails);
        if (!$result) {
            return Message(1000, "生成订单详情失败");
        }
        return MessageInfo(0, "订单详情生成成功", $tempdetails);
    }

    //创建订单信息
    protected function CreataOrderInfo($parr, $orderid) {
        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $parr['ucode'];
        $aorderinfo['c_acode'] = $parr['acode'];
        $aorderinfo['c_free'] = 0;
        $aorderinfo['c_total_price'] = $parr['total'];
        $aorderinfo['c_actual_price'] = '0.00';
        $aorderinfo['c_activity_id'] = $parr['activityid'];
        $aorderinfo['c_activity_name'] = $parr['activityname'];
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $aorderinfo['c_pay_state'] = 1;
        $aorderinfo['c_pay_rule'] = 5;
        $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());


        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '您在'.$parr['activityname'].'中收集的【'.$parr['name'].'】碎片，已兑换商品成功';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        $result = M('Order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }
        return Message(0, "订单创建成功");
    }

    //生成订单地址
    protected function CreataOrderAddress($parr, $orderid) {
        $addresswhere['c_ucode'] = $parr['ucode'];
        $addresswhere['c_is_default'] = 1;
        $useraddress = M('Users_address')->where($addresswhere)->find();
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