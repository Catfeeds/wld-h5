<?php

/**
 * 兑换中心相关接口
 */
class ExchangeNewact {

	/**
	 * 查询兑换中心数据列表
	 * @param ucode,pageindex,pagesize,type(1线下兑换,2线上兑换),longitude,latitude
	 */
	function GetExchangeList($parr)
	{
		if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }
		$longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$type = 3;
		if ($parr['type'] == 1) {
			$type = 2;
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$where['a.c_ucode'] = $parr['ucode'];
		$where['a.c_type'] = $type;
		$where['a.c_ptype'] = 4;
		// $where['b.c_ishow'] = 1;
		// $where['b.c_isdele'] = 1;
		// $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
		$field = 'a.*';
		$order = 'a.c_status asc,a.c_id desc';

		$list = M('A_start_log as a')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

		$count = M('A_start_log as a')->where($where)->count();
		$pageCount = ceil($count / $pageSize);

		if (count($list) == 0) {
		    $list = array();
		    $data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, '查询成功', $data);
		}

		foreach ($list as $key => $value) {
			//查询产品信息
			$pw['c_pcode'] = $value['c_pcode'];
			$pinfo = M('Product')->where($pw)->field('c_name,c_pimg')->find();

			$list[$key]['c_name'] = $pinfo['c_name'];
		    $list[$key]['c_pimg'] = GetHost() . '/' . $pinfo['c_pimg'];
		    $list[$key]['times'] = date('Y-m-d',strtotime($value['c_addtime'])).'至'.date('Y-m-d',strtotime('+1 days',strtotime($value['c_addtime'])));
		    
		    //查询商家信息
		    $userparr['ucode'] = $value['c_ucode'];
		    $userparr['acode'] = $value['c_acode'];
		    $result = IGD('Red','Newact')->GetShopBaseInfo($userparr);
		    $userinfo = $result['data'];
		    $list[$key]['distance'] = IGD('Start','Newact')->plandistance($longitude, $latitude, $userinfo['c_longitude1'], $userinfo['c_latitude1']);
			$list[$key]['c_nickname'] = $userinfo['c_nickname'];
			$list[$key]['c_headimg'] = $userinfo['c_headimg'];
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 查询单个兑换详情
	 * @param sid,longitude,latitude,ucode
	 */
	function GetExchangeInfo($parr)
	{
		if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }
		$longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        $where['a.c_ucode'] = $parr['ucode'];
		$where['a.c_id'] = $parr['sid'];
		// $where['b.c_ishow'] = 1;
		// $where['b.c_isdele'] = 1;
		// $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
		$field = 'a.*';
		$data = M('A_start_log as a')->where($where)->field($field)->find();
		if (!$data) {
			return Message(3000,'没有相关信息');
		}

		//查询产品信息
		$pw['c_pcode'] = $data['c_pcode'];
		$pinfo = M('Product')->where($pw)->field('c_name,c_pimg,c_ishow,c_isdele')->find();
		// if ($pinfo['c_ishow'] != 1 || $pinfo['c_isdele'] != 1) {
		// 	return Message(3000,'商家该产品已经下架');
		// }

		$data['c_name'] = $pinfo['c_name'];
	    $data['c_pimg'] = GetHost() . '/' . $pinfo['c_pimg'];
	    $data['times'] = date('Y-m-d',strtotime($data['c_addtime'])).'至'.date('Y-m-d',strtotime('+1 days',strtotime($data['c_addtime'])));
	    
	    //查询商家信息
	    $userparr['ucode'] = $data['c_ucode'];
	    $userparr['acode'] = $data['c_acode'];
	    $result = IGD('Red','Newact')->GetShopBaseInfo($userparr);
	    $userinfo = $result['data'];

	    //查询距离
	    $str1 = GetDistance($longitude, $latitude, $userinfo['c_longitude1'], $userinfo['c_latitude1']);
        $str1 = sprintf("%.2f", $str1);   //单位千米
        if ($str1 < 1) {
            $a = bcmul($str1, 1000, 2);
            if ($a <= 10) {
                $strb = "＜10m";
            } else if ($a > 10 && $a <= 100) {
                $strb = "＜100m";
            } else {
                $strb = sprintf("%.0f", $a) . "m";
            }
        } else {
            $strb = $str1 . "km";
        }

        if ($data['c_type'] == 2) {
	        $data['isexchage'] = 0;
	        if ($str1 < 1) {   //线下小于一千米可兑换
	        	$data['isexchage'] = 1;
	        }
        } else {
        	$data['isexchage'] = 1;
        }

	    $data['distance'] = '距您'.$strb;
		$data['c_nickname'] = $userinfo['c_nickname'];
		$data['c_headimg'] = $userinfo['c_headimg'];
		$data['c_pv'] = $userinfo['c_pv'];

		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 线下相关兑换步骤
	 * @param ucode,sid,status(1,2,3,4),longitude,latitude
	 */
	function OfflineExchange($parr)
	{
		$status = $parr['status'];
		if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }
		$longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        $where['a.c_status'] = $status - 1;
		$where['a.c_id'] = $parr['sid'];
		if ($status != 2) {
			$where['a.c_ucode'] = $parr['ucode'];
		}
		// $where['b.c_ishow'] = 1;
		// $where['b.c_isdele'] = 1;
		// $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
		$field = 'a.*';
		$startlog = M('A_start_log as a')->where($where)->field($field)->find();
		if (!$startlog) {
			return Message(3000,'信息不存在');
		}

		//查询产品信息
		$pw['c_pcode'] = $startlog['c_pcode'];
		$pinfo = M('Product')->where($pw)->field('c_name,c_pimg,c_ishow,c_isdele')->find();
		if ($pinfo['c_ishow'] != 1 || $pinfo['c_isdele'] != 1) {
			return Message(3000,'商家该产品已经下架');
		}


		if ($status != 2) {
			//查询商家信息
			$userinfo = M('Users')->where(array('c_ucode'=>$startlog['c_acode']))->find();

			//判断兑换距离
			$str1 = GetDistance($longitude, $latitude, $userinfo['c_longitude1'], $userinfo['c_latitude1']);	//单位千米
	        $a = bcmul($str1, 1000, 2);
	        if ($a > 50) {   //兑换需离商家50米距离之类
	        	return Message(3001,'兑换需离商家10米距离之内');
	        }
	    }

		$db = M('');
		$db->startTrans();

		//修改状态
		$stw['c_id'] = $parr['sid'];
		$save['c_updatetime'] = gdtime();
		$save['c_status'] = $parr['status'];
		if ($parr['status'] == 3) {
			$save['c_receivetime'] = gdtime();
		}

		$result = M('A_start_log')->where($stw)->save($save);
		if (!$result) {
			$db->rollback();
			return Message(3000,'操作失败');
		}

		if ($parr['status'] == 3) {
			//改变奖项库存
			$pw['c_id'] = $startlog['c_awid'];
			$pw['c_status'] = 1;
			$pw['c_delete'] = 2;
			$pw['c_num'] = array('GT',0);
			$result = M('A_actprize')->where($pw)->setDec('c_num',1);
			if (!$result) {
				$db->rollback();
				return Message(3001,'奖项库存不足，请联系商家添加库存');
			}

			//写入领取记录
	        $parr['joinaid'] = $startlog['c_joinaid'];
	        $parr['awid'] = $startlog['c_awid'];
	        $parr['pid'] = $startlog['c_pcode'];
	        $parr['acode'] = $startlog['c_acode'];
	        $parr['name'] = $startlog['c_name'];
	        $parr['img'] = $startlog['c_pimg'];
	        $parr['value'] = $startlog['c_value'];
	        $parr['marks'] = '宝箱';
	        $parr['type'] = 4;
	        $parr['state'] = 1;
	        $result = IGD('Index','Newact')->WriteReciveLog($parr);
	        if ($result['code'] != 0) {
	        	$db->rollback();
	            return $result;
	        }
	    }

	    $Msgcentre = IGD('Msgcentre', 'Message');

	    if ($parr['status'] == 1) {
	    	//给商家发信息
	        $msgdata['ucode'] = $startlog['c_acode'];
	    	$msgdata['type'] = 0;
	    	$msgdata['platform'] = 1;
	    	$msgdata['sendnum'] = 1;
	    	$msgdata['title'] = '系统消息';
	    	$msgdata['content'] = '用户在您发起的宝箱活动中，发起商品：'.$startlog['c_name'].'，兑换申请';
	    	$msgdata['tag'] = 2;
	    	$msgdata['tagvalue'] = GetHost(1).'/index.php/Activity/Chests/record?joinaid='.$startlog['c_joinaid'];
	    	$msgdata['weburl'] = GetHost(1).'/index.php/Activity/Chests/record?joinaid='.$startlog['c_joinaid'];
	    	$Msgcentre->CreateMessege($msgdata);
	    } else if ($parr['status'] == 2) {
	    	//给用户发信息
	        $msgdata['ucode'] = $startlog['c_ucode'];
	        $msgdata['type'] = 0;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['content'] = '您在兑换中心，申请兑换商品：'.$startlog['c_name'].'，商家已同意';
	        $msgdata['tag'] = 35;
	        $msgdata['tagvalue'] = $startlog['c_id'];
	    	$msgdata['weburl'] = GetHost(1).'/index.php/Home/Exchange/exget?sid='.$startlog['c_id'];
	        $Msgcentre->CreateMessege($msgdata);
	    } else if ($parr['status'] == 3) {
	    	//给商家发信息
	        $msgdata['ucode'] = $startlog['c_acode'];
	    	$msgdata['type'] = 0;
	    	$msgdata['platform'] = 1;
	    	$msgdata['sendnum'] = 1;
	    	$msgdata['title'] = '系统消息';
	    	$msgdata['content'] = '产品：'.$startlog['c_name'].'，已确认兑换成功';
	    	$msgdata['tag'] = 2;
	    	$msgdata['tagvalue'] = GetHost(1).'/index.php/Activity/Chests/record?joinaid='.$startlog['c_joinaid'];
	    	$msgdata['weburl'] = GetHost(1).'/index.php/Activity/Chests/record?joinaid='.$startlog['c_joinaid'];
	    	$Msgcentre->CreateMessege($msgdata);
	    }

        $db->commit();
		return Message(0,'操作成功');
	}

	/**
	 * 线上兑换
	 * @param ucode,sid,addressid,postscript
	 */
	function OnlineExchange($parr)
	{
		$addressid = $parr['addressid'];
		$postscript = $parr['postscript'];
		$ucode = $parr['ucode'];

		$where['a.c_id'] = $parr['sid'];
		$where['a.c_ucode'] = $parr['ucode'];
		$where['a.c_status'] = 0;
		// $where['b.c_ishow'] = 1;
		// $where['b.c_isdele'] = 1;
		// $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
		$field = 'a.*';
		$startlog = M('A_start_log as a')->where($where)->field($field)->find();
		if (!$startlog) {
			return Message(3000,'信息不存在');
		}

		//查询产品信息
		$pw['c_pcode'] = $startlog['c_pcode'];
		$pinfo = M('Product')->where($pw)->field('c_name,c_pimg,c_ishow,c_isdele')->find();
		if ($pinfo['c_ishow'] != 1 || $pinfo['c_isdele'] != 1) {
			return Message(3000,'商家该产品已经下架');
		}


		$db = M('');
		$db->startTrans();

		$result = $this->CreataOrderInfo($ucode,$startlog,$addressid,$postscript);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}
		$orderid = $result['data'];

		//改变领取状态
		$stw['c_id'] = $parr['sid'];
		$stw['c_ucode'] = $parr['ucode'];
		$save['c_updatetime'] = gdtime();
		$save['c_status'] = 3;
		$save['c_receivetime'] = gdtime();
		$result = M('A_start_log')->where($stw)->save($save);
		if (!$result) {
			$db->rollback();
			return Message(3001,'领取失败');
		}

		//改变奖项库存
		$pw['c_id'] = $startlog['c_awid'];
		$pw['c_status'] = 1;
		$pw['c_delete'] = 2;
		$pw['c_num'] = array('GT',0);
		$result = M('A_actprize')->where($pw)->setDec('c_num',1);
		if (!$result) {
			$db->rollback();
			return Message(3001,'奖项库存不足，请联系商家添加库存');
		}

		//写入领取记录
        $parr['joinaid'] = $startlog['c_joinaid'];
        $parr['awid'] = $startlog['c_awid'];
        $parr['pid'] = $startlog['c_pcode'];
        $parr['acode'] = $startlog['c_acode'];
        $parr['name'] = $startlog['c_name'];
        $parr['img'] = $startlog['c_pimg'];
        $parr['value'] = $startlog['c_value'];
        $parr['marks'] = '热气球';
        $parr['type'] = 4;
        $parr['state'] = 1;
        $result = IGD('Index','Newact')->WriteReciveLog($parr);
        if ($result['code'] != 0) {
        	$db->rollback();
            return $result;
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        //给用户发信息
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '您在兑换中心，成功兑换商品：'.$startlog['c_name'];
        $msgdata['tag'] = 3;
        $msgdata['weburl'] = GetHost(1).'/index.php/Order/Index/detail?orderid='.$orderid;
        $msgdata['tagvalue'] =  $orderid;
        $Msgcentre->CreateMessege($msgdata);


        //给商家发信息
        $msgdata['ucode'] = $startlog['c_acode'];
    	$msgdata['type'] = 1;
    	$msgdata['platform'] = 1;
    	$msgdata['sendnum'] = 1;
    	$msgdata['title'] = '订单消息';
    	$msgdata['content'] = '用户在您发起的热气球活动中，成功兑换商品：'.$startlog['c_name'];
    	$msgdata['tag'] = 2;
    	$msgdata['tagvalue'] = GetHost(1).'/index.php/Order/Storeorder/detail?orderid='.$orderid;
    	$msgdata['weburl'] = GetHost(1).'/index.php/Order/Storeorder/detail?orderid='.$orderid;
    	$Msgcentre->CreateMessege($msgdata);

		$db->commit();
		return Message(0,'领取成功');
	}


	//创建订单信息
    function CreataOrderInfo($ucode,$startlog,$addressid,$postscript)
    {
    	$orderid = CreateOrder('x');
    	$aorderinfo['c_orderid'] = $orderid;
    	$aorderinfo['c_ucode'] = $ucode;

    	if ($startlog['c_value'] <= 0) {
    		$aorderinfo['c_pay_state'] = 1;
	        $aorderinfo['c_order_state'] = 2;
	        $aorderinfo['c_deliverystate'] = 0;
	        $aorderinfo['c_pay_rule'] = 5;
	        $aorderinfo['c_actual_price'] = 0;
	        $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());            
    	} else {
    		$aorderinfo['c_pay_state'] = 0;
	        $aorderinfo['c_order_state'] = 2;
	        $aorderinfo['c_deliverystate'] = 0;
	        $aorderinfo['c_free'] = $startlog['c_value'];
    	}

    	if (!empty($startlog['c_acode'])) {
            // $userwhere['c_ucode'] = $ucode;
            // $userinfo = M('Users')->where($userwhere)->field('c_isagent,c_acode')->find();
            // //商家活动产品绑定归属代理商
            // if ($userinfo['c_isagent'] == 0 && empty($userinfo['c_acode'])) {
            //     $agentwhere['c_ucode'] = $startlog['c_acode'];
            //     $usersave['c_acode'] =  M('Users')->where($agentwhere)->getField('c_acode');
            //     $result = M('Users')->where($userwhere)->save($usersave);
            //     if (!$result) {
            //         return Message(3001, "归属代理商失败");
            //     }
            // }

            // $usertuijian = M('Users_tuijian')->where($userwhere)->find();
            // //商家的活动产品绑定用户
            // if (count($usertuijian) <= 0) {
            //     $add['c_ucode'] = $ucode;
            //     $add['c_pcode'] = $startlog['c_acode'];
            //     $add['c_addtime'] = date('Y-m-d H:i:s', time());
            //     $result = M('Users_tuijian')->add($add);
            //     if (!$result) {
            //         return Message(3002, "建立用户关系失败");
            //     }
            // }
        }

    	$aorderinfo['c_activity_id'] = $startlog['c_joinaid'];
    	$aorderinfo['c_activity_name'] = M('Actjoin_moneylog')->where(array('c_id'=>$startlog['c_joinaid']))->getField('c_activityname');
    	$aorderinfo['c_acode'] = $startlog['c_acode'];
        $aorderinfo['c_total_price'] = '0.00';
        $aorderinfo['c_delivery'] = 1;
        $aorderinfo['c_postscript'] = $postscript;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }

        //创建订单详情
        $result = $this->CreataOrderdetails($ucode,$orderid,$startlog);
        if ($result['code'] != 0) {
            return $result;
        }

        //创建订单地址
        $result = $this->CreataOrderAddress($ucode,$orderid,$addressid);
        if ($result['code'] != 0) {
            return $result;
        }

        return MessageInfo(0, "订单创建成功",$orderid);
    }

	//生成订单详情
    function CreataOrderdetails($ucode,$orderid,$startlog) {
    	$detailid = CreateOrder('dx');

    	//根据产品编码查询产品信息
    	$productinfo = M('Product')->where(array('c_pcode'=>$startlog['c_pcode']))->find();
        $tempdetails['c_orderid'] = $orderid;
        $tempdetails['c_detailid'] = $detailid;
        $tempdetails['c_ucode'] = $ucode;
        $tempdetails['c_pcode'] = $startlog['c_pcode'];
        $tempdetails['c_pprice'] = $startlog['c_maxvalue'];
        $tempdetails['c_pname'] = $productinfo['c_name'];
        $tempdetails['c_pmodel_name'] = $productinfo['c_name'];
        $tempdetails['c_pnum'] = 1;
        $tempdetails['c_ptotal'] = 0;
        $tempdetails['c_free'] = $startlog['c_value'];
        $tempdetails['c_profit'] = 0;
        $tempdetails['c_pimg'] = $productinfo['c_pimg'];
        $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order_details')->add($tempdetails);
        if (!$result) {
            return Message(1000, "生成订单详情失败");
        }

        return MessageInfo(0, "订单详情生成成功");
    }


    //生成订单地址
    function CreataOrderAddress($ucode,$orderid,$addressid) {
        $addresswhere['c_id'] = $addressid;
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

    /**
     * 定时器检测领取时间过期放弃领取
     * @param 
     */
    function RecallGoods()
    {
    	$where[] = array("c_type=2 or c_type=3");
        $where['c_ptype'] = 4;
		$where['c_status'] = 0;
		$where['c_addtime'] = array('ELT',date('Y-m-d H:i:s',strtotime('-1 days')));
		$list = M('A_start_log')->where($where)->limit(20)->order('c_addtime desc')->select();
		if (!$list) {
            return Message(0, '没有记录');
        }

        $num = 0;
        foreach ($list as $key => $value) {
        	$stw['c_id'] = $value['c_id'];
        	$stwsave['c_status'] = 4;
        	$stwsave['c_updatetime'] = gdtime();
			$result = M('A_start_log')->where($stw)->save($stwsave);
			if ($result) {
				$num++;
			}
        }
        return MessageInfo(0, '操作成功',$num);
    }


}