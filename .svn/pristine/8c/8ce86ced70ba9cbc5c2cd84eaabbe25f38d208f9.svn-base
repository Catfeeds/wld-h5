<?php

/**
 * 卡劵管理相关接口
 */
class CouponNewact {

	/**
	 * 卡劵列表(活动卡劵列表)
	 * @param ucode,type(1可领用2已领取3已过期4可发放)
	 */
	function CouponList($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        switch ($parr['type']) {
        	case '1':
        		$where['c_endtime'] = array('GT',gdtime());
        		$where['c_num'] = array('GT',0);
        		break;
        	case '2':
        		$where['c_endtime'] = array('GT',gdtime());
        		$where['c_status'] = 1;
        		break;
        	case '3':
        		$where['c_endtime'] = array('LT',gdtime());
        		break;
        	case '4':
        		$where['c_starttime'] = array('ELT',gdtime());
        		$where['c_endtime'] = array('EGT',gdtime());
        		$where['c_actnum'] = array('GT',0);
        		break;
        	default:
        		break;
        }

		$where['c_ucode'] = $parr['ucode'];
		$where['c_delete'] = 2;
		$list = M('A_actcard')->where($where)->limit($countPage, $pageSize)->order('c_id desc')->select();

		$count = M('A_actcard')->where($where)->count();
        $pageCount = ceil($count / $pageSize);        
		if (!$list) {
			$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $list[$key]['c_starttime'] = str_replace('-', '.', $value['c_starttime']);
            $list[$key]['c_endtime'] = str_replace('-', '.', $value['c_endtime']);
            $list[$key]['c_limit_money'] = sprintf("%.0f",round($value['c_limit_money']));
            $showstr = '全部商品';
        	if (!empty($value['c_pcodearr'])) {
        		$showstr = '部分商品';
        	}
            if ($value['c_type'] == 1) {
            	$list[$key]['c_money'] = sprintf("%.1f",round($value['c_money']));
            	$list[$key]['showstr'] = $showstr.'|'.'满'.$list[$key]['c_limit_money'].'元可用';
            } else if ($value['c_type'] == 2) {
            	$list[$key]['c_money'] = sprintf("%.1f",round($value['c_money'],1));
            	$list[$key]['showstr'] = $showstr.'|'.'最高折扣'.$list[$key]['c_limit_money'].'元';
            } 
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 查询店铺发放卡劵列表
	 * @param acode,ucode,pageindex,pagesize,type
	 */
	function ShopCouponList($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		//获取平台卡劵活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(21,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $couponwhere['a.c_joinaid'] = $activitydata['c_id'];
        $couponwhere['a.c_acode'] = $parr['acode'];
        $couponwhere['a.c_type'] = 3;
        if ($parr['type'] == 1) {
        	$couponwhere['a.c_num'] = array('GT',0);
        }
        $couponwhere['a.c_status'] = 1;
        $couponwhere['a.c_delete'] = 2;
        $couponwhere[] = array('b.c_id is not null');
        $join = 'left join t_a_actcard as b on a.c_pid=b.c_id';
        $field = 'b.*,a.c_id as awid,a.c_totalnum as tnum,a.c_num as snum';
        $order = 'a.c_id desc';
        $list = M('A_actprize as a')->join($join)->where($couponwhere)->order($order)->field($field)->limit($countPage, $pageSize)->select();

        $count = M('A_actprize as a')->join($join)->where($couponwhere)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
        	$data = Page($pageIndex, $pageCount, $count, $list);
        	return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['c_starttime'] = str_replace('-', '.', $value['c_starttime']);
            $list[$key]['c_endtime'] = str_replace('-', '.', $value['c_endtime']);

            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $list[$key]['receive'] = 0;   //0未领取,1已领取

            //查询用户是否领取
            $logwhere['c_ucode'] = $parr['ucode'];
            $logwhere['c_awid'] = $value['awid'];
            $logwhere['c_state'] = 1;
            $logfield = 'c_id';
            $receivedata = M('A_actlog')->where($logwhere)->field($logfield)->find();
            if ($receivedata) {
            	$list[$key]['receive'] = 1;
            }

            $list[$key]['c_limit_money'] = sprintf("%.0f",round($value['c_limit_money']));
            $showstr = '全部商品';
        	if (!empty($value['c_pcodearr'])) {
        		$showstr = '部分商品';
        	}
            if ($value['c_type'] == 1) {
            	$list[$key]['c_money'] = sprintf("%.1f",round($value['c_money']));
            	$list[$key]['showstr'] = $showstr.'|'.'满'.$list[$key]['c_limit_money'].'元可用';
            } else if ($value['c_type'] == 2) {
            	$list[$key]['c_money'] = sprintf("%.1f",round($value['c_money'],1));
            	$list[$key]['showstr'] = $showstr.'|'.'最高折扣'.$list[$key]['c_limit_money'].'元';
            }

            $userwhere['c_ucode'] = $value['c_ucode'];
        	$userfield = 'c_ucode,c_headimg,c_nickname';
            $userinfo = M('Users')->where($userwhere)->field($userfield)->find();
            $list[$key]['c_nickname'] = $userinfo['c_nickname'];
            $list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 店铺发放卡劵
	 * @param cid,num,ucode
	 */
	function GrantCoupon($parr)
	{
        //获取平台卡劵活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(21,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $db = M('');
        $db->startTrans();

        $result = $this->GrantOptionCoupon($parr,$activitydata,'店铺');
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        $db->commit();
        return Message(0, '发放成功');
	}

    /**
     * 发放活动卡劵操作
     * @param ucode,num,cid,$activitydata
     */
    function GrantOptionCoupon($parr,$activitydata,$remark)
    {
        $infowhere['c_id'] = $parr['cid'];
        $infowhere['c_ucode'] = $parr['ucode'];
        $data = M('A_actcard')->where($infowhere)->find();
        if (!$data) {
            return Message(3000, '没有相关卡劵');
        }

        if ($parr['num'] > $data['c_actnum']) {
            return Message(3001, '发放数量不能大于可发放总数量');
        }

        if ($data['c_type'] == 1) {
            $kaname = round($data['c_money']).'元代金劵';
        } else if ($data['c_type'] == 2) {
            $kaname = round($data['c_money'],1).'折折扣劵';
        }

        // 新增奖项记录
        $add['c_acode'] = $parr['ucode'];
        $add['c_joinaid'] = $activitydata['c_id'];
        $add['c_name'] = $kaname;
        $add['c_type'] = 3;
        $add['c_img'] = $data['c_img'];
        $add['c_pid'] = $data['c_id'];
        $add['c_value'] = $data['c_money'];
        $add['c_maxvalue'] = $data['c_limit_money'];
        $add['c_starttime'] = $data['c_starttime'];
        $add['c_endtime'] = $data['c_endtime'];
        $add['c_marks'] = $data['c_type'];
        $add['c_totalnum'] = $parr['num'];
        $add['c_num'] = $parr['num'];
        $add['c_remark'] = $remark;
        $add['c_status'] = 1;
        $add['c_updatetime'] = gdtime();
        $add['c_addtime'] = gdtime();
        $result = M('A_actprize')->add($add);
        if (!$result) {
            return Message(3002,'添加记录失败');
        }

        //扣除卡劵活动数量
        $result = $this->DecCouponCard($parr['cid'],2,$parr['num']);
        if ($result['code'] != 0) {
            return $result;
        }

        //添加商家活动记录
        $shopact_log['c_aid'] = $activitydata['c_aid'];
        $shopact_log['c_ucode'] = $parr['ucode'];
        $shopact_log['c_couponid'] = $data['c_id'];
        $shopact_log['c_acttype'] = 2;
        $shopact_log['c_coupontype'] = $data['c_type'];
        $shopact_log['c_money'] = $data['c_money'];
        $shopact_log['c_limit_money'] = $data['c_limit_money'];
        $shopact_log['c_starttime'] = $data['c_starttime'];
        $shopact_log['c_endtime'] = $data['c_endtime'];
        $shopact_log['c_weburl'] = GetHost(1)."/index.php/Store/Index/couponlist?fromucode=".$parr['ucode'];
        $shopact_log['c_addtime'] = gdtime();

        $result = M('Circle_shopact')->add($shopact_log);

        if (!$result) {
            return Message(3003,'添加商圈活动记录失败');
        }
    }

	/**
	 * 取消发放卡劵
	 * @param awid,ucode
	 */
	function CancelCouponCard($parr)
	{
		//查询卡劵奖项信息
		$prizewhere['c_id'] = $parr['awid'];
        $prizewhere['c_type'] = 3;
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
		$prizedata = M('A_actprize')->where($prizewhere)->find();
		if (!$prizedata) {
			return Message(3000, '没有相关数据');
		}

		$db = M('');
        $db->startTrans();

        //改变活动状态
        $prizesave['c_delete'] = 1;
        $prizesave['c_status'] = 2;
        $prizesave['c_updatetime'] = gdtime();
        $result = M('A_actprize')->where($prizewhere)->save($prizesave);
        if (!$result) {
        	$db->rollback();
        	return Message(3001,'改变活动删除状态失败');
        }

        //返回活动库存
        $where['c_id'] = $prizedata['c_pid'];
		$result = M('A_actcard')->where($where)->setInc('c_actnum',$prizedata['c_num']);
		if (!$result) {
			$db->rollback();
			return Message(3000,'操作库存失败');
		}

		$db->commit();
        return Message(0, '操作成功');
	}

	/**
	 * 店铺领取卡劵
	 * @param awid,ucode
	 */
	function ReceiveShopCoupon($parr)
	{
		//查询卡劵奖项信息
		$prizewhere['c_id'] = $parr['awid'];
        $prizewhere['c_type'] = 3;
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;
		$prizedata = M('A_actprize')->where($prizewhere)->find();
		if (!$prizedata) {
			return Message(3000, '没有相关数据');
		}

		//领取卡劵
		if ($prizedata['c_pid']) {
			//查询用户是否已领取
			$logwhere['c_ucode'] = $parr['ucode'];
	        $logwhere['c_awid'] = $prizedata['c_id'];
	        $logwhere['c_state'] = 1;
	        $logfield = 'c_id';
	        $receivedata = M('A_actlog')->where($logwhere)->field($logfield)->find();
	        if ($receivedata) {
	        	return Message(3001, '您已经领取过该卡劵');
	        }

        	$db = M('');
			$db->startTrans();
			$parr['cid'] = $prizedata['c_pid'];
			$parr['joinaid'] = $prizedata['c_joinaid'];
			$parr['remark'] = $prizedata['c_remark'];
			$result = $this->ReceiveCoupon($parr);
			if ($result['code'] != 0) {
				$db->rollback();
				return $result;
			}
		} else {
			$db = M('');
			$db->startTrans();
			// 新增领取记录
			$add['c_ucode'] = $parr['ucode'];
			$add['c_joinaid'] = $prizedata['c_joinaid'];
			$add['c_remark'] = $prizedata['c_remark'];
			$add['c_name'] = $prizedata['c_name'];
			$add['c_sign'] = 1;
			$add['c_type'] = $prizedata['c_marks'];
			$add['c_img'] = $prizedata['c_img'];
			$add['c_money'] = $prizedata['c_value'];
			$add['c_limit_money'] = $prizedata['c_maxvalue'];
			$add['c_starttime'] = $prizedata['c_starttime'];
			$add['c_endtime'] = $prizedata['c_endtime'];
			$add['c_used_state'] = 0;
			$add['c_addtime'] = gdtime();
			$result = M('A_user_coupons')->add($add);
			if (!$result) {
				$db->rollback();
				return Message(3002,'领取失败');
			}
		}

		//写入领取记录
		$parr['joinaid'] = $prizedata['c_joinaid'];
		$parr['pid'] = $prizedata['c_pid'];
		$parr['acode'] = $prizedata['c_acode'];
		$parr['name'] = $prizedata['c_name'];
		$parr['img'] = $prizedata['c_img'];
		$parr['value'] = $prizedata['c_value'];
		$parr['marks'] = $prizedata['c_remark'];
		$parr['type'] = 3;
		$parr['state'] = 1;
		$result = IGD('Index','Newact')->WriteReciveLog($parr);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}

		$result = M('A_actprize')->where($prizewhere)->setDec('c_num',1);
		if (!$result) {
			$db->rollback();
			return Message(3002, '扣除奖项剩余数量失败');
		}

		$db->commit();
		return Message(0,'领取成功');
	}

	/**
	 * 查询店铺卡劵领取记录
	 * @param ucode,pagesize,pageindex
	 */
	function GetReceviLog($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		//获取平台卡劵活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(21,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $logwhere['c_type'] = 3;
        $logwhere['c_acode'] = $parr['ucode'];

        $order = 'c_id desc';
        $list = M('A_actlog')->where($logwhere)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('A_actlog')->where($logwhere)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
        	$data = Page($pageIndex, $pageCount, $count, $list);
        	return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
        	$userwhere['c_ucode'] = $value['c_ucode'];
        	$userfield = 'c_ucode,c_headimg,c_nickname';
            $userinfo = M('Users')->where($userwhere)->field($userfield)->find();
            $list[$key]['c_nickname'] = $userinfo['c_nickname'];
            $list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

            $infowhere['c_id'] = $value['c_pid'];
            $couponinfo = M('A_actcard')->where($infowhere)->find();
            $list[$key]['katype'] = $couponinfo['c_type'];
            if ($couponinfo['c_type'] == 1) {
            	$list[$key]['signstr'] = '代金券';
            	$list[$key]['signpro'] = round($couponinfo['c_money']).'元';
            } else if ($couponinfo['c_type'] == 2) {            	
            	$list[$key]['signstr'] = '折扣券';
            	$list[$key]['signpro'] = round($couponinfo['c_money'],1).'折';
            }
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 查询卡劵信息详情
	 * @param cid
	 */
	function GetCouponInfo($parr)
	{
		$infowhere['c_id'] = $parr['cid'];
        $data = M('A_actcard')->where($infowhere)->find();
        if (!$data) {
            return Message(3000, '没有相关数据');
        }

        $pcodestr = str_replace('|', ',', $data['c_pcodearr']);
        $where['c_pcode'] = array('in',$pcodestr);
        $where['c_isdele'] = 1;
        $where['c_ishow'] = 1;
        $field = 'c_pcode,c_name,c_pimg,c_price,c_source';
        $productlist = M('Product')->where($where)->field($field)->select();
        foreach ($productlist as $key => $value) {
        	$productlist[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }

        $data['c_limit_money'] = round($data['c_limit_money']);       
        if ($data['c_type'] == 1) {
            $data['c_money'] = round($data['c_money']);
        } else if ($data['c_type'] == 2) {
            $data['c_money'] = round($data['c_money'],1);
        }

        $data['productlist'] = $productlist;
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 查询卡劵详情关联相关活动
	 * @param ucode,cid
	 */
	function ActivityCoupon($parr)
	{
		$where['a.c_pid'] = $parr['cid'];
		$where['a.c_delete'] = 2;
		$join = 'left join t_actjoin_moneylog as b on a.c_joinaid=b.c_id';
		$field = 'a.*,b.c_remark,b.c_activityname,b.c_listimg as actimg,b.c_activitystarttime,b.c_activityendtime';
		$list = M('A_actprize as a')->join($join)->where($where)->field($field)->order('a.c_id desc')->group('a.c_joinaid')->select();
		if (!$list) {
            return Message(3000, '没有相关数据');
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $list[$key]['actimg'] = GetHost() . '/' . $value['actimg'];
            if (empty($value['c_activitystarttime']) && empty($value['c_activityendtime'])) {
            	$list[$key]['progress'] = 1;
            } else {
            	if (strtotime($value['c_activitystarttime']) > time()) {
	                $list[$key]['progress'] = 0;    //未开始
	            } else if (strtotime($value['c_activitystarttime']) <= time() && strtotime($value['c_activityendtime']) >= time()) {
	                $list[$key]['progress'] = 1;    //进行中
	            } else {
	                $list[$key]['progress'] = 2;    //已结束
	            }
            }
        }

        $infowhere['c_id'] = $parr['cid'];
        $data['list'] = $list;
        $data['info'] = M('A_actcard')->where($infowhere)->find();
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 删除卡劵操作
	 * @param cid,ucode
	 */
	function DeleteCoupon($parr)
	{
		// 查询卡劵信息
		$infowhere['c_id'] = $parr['cid'];
		$infowhere['c_ucode'] = $parr['ucode'];
        $data = M('A_actcard')->where($infowhere)->find();
        if (!$data) {
            return Message(3000, '没有相关数据');
        }

        $db = M('');
        $db->startTrans();

        //改变所以活动库存
        $prizewhere['c_type'] = 3;  //3表示卡劵
        $prizewhere['c_pid'] = $data['c_id'];
        $prizewhere['c_delete'] = 2;
        $count = M('A_actprize')->where($prizewhere)->count();
        if ($count > 0) {
	        $prizesave['c_delete'] = 1;
	        $prizesave['c_updatetime'] = gdtime();
	        $result = M('A_actprize')->where($prizewhere)->save($prizesave);
	        if (!$result) {
	        	$db->rollback();
	        	return Message(3001,'改变活动删除状态失败');
	        }
        }

		//改变卡劵删除状态
		$cardparr['cid'] = $data['c_id'];
		$cardparr['delete'] = 1;
		$result = $this->OptionCouponCard($cardparr);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}

        //删除所有推广位相关数据
        $result = IGD('Advert','Newact')->DeleteAllAdcert($parr);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

		$db->commit();
		return Message(0,'删除成功');
	}

	/**
	 * 查询卡劵领取详情
	 * @param ucode,cid,type(1领取详情,2使用详情)
	 */
	function CouponReceiveInfo($parr)
	{
		$where['a.c_cid'] = $parr['cid'];
		$join = 'left join t_users as b on a.c_ucode=b.c_ucode';
		$field = 'a.*,b.c_nickname,b.c_headimg';
		$list = M('A_user_coupons as a')->join($join)->where($where)->field($field)->select();
		if (!$list) {
            return Message(0, '没有相关数据');
        }

        $usednum = 0;
        foreach ($list as $key => $value) {
        	if ($value['c_used_state'] == 2) {
        		$usednum++;
        	}
        	$list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
        }

        $infowhere['c_id'] = $parr['cid'];
        $data['list'] = $list;
        $data['info'] = M('A_actcard')->where($infowhere)->find();
        if ($parr['type'] == 1) {
        	$data['info']['leftnum'] = $data['info']['c_totalnum'] - $data['info']['c_num'];
        	$data['info']['rightnum'] = $data['info']['c_num'];	
		} else {
			$data['info']['leftnum'] = $usednum;
        	$data['info']['rightnum'] = count($list) - $usednum;	
		}
		$data['pageCount'] = 1;
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 新增与编辑卡劵
	 * @param ucode,name,totalnum,num,actnum,sign,type,img,money,limit_money,starttime,endtime,pcodestr(joinaid)
	 */
	function AddCouponCard($parr)
	{
        if ($parr['type'] == 1) {  //抵扣劵
            if ($parr['money'] <= 0 || $parr['money'] > 1000) {
                return Message(3000,'现金劵的设置范围只能在1~1000之间');
            }
        }

        $db = M('');
        $db->startTrans();

		$add['c_ucode'] = $parr['ucode'];
		$add['c_name'] = $parr['name'];
		$add['c_totalnum'] = $parr['totalnum'];
		$add['c_num'] = $parr['num'];
		$add['c_actnum'] = $parr['actnum'];
		$add['c_sign'] = $parr['sign'];  //1平台,2商家
		$add['c_type'] = $parr['type'];
		$add['c_img'] = $parr['img'];
		$add['c_money'] = $parr['money'];
		$add['c_limit_money'] = $parr['limit_money'];
		$add['c_delete'] = 2;
		$add['c_pcodearr'] = $parr['pcodearr'];
		$add['c_starttime'] = $parr['starttime'];
		$add['c_endtime'] = $parr['endtime'];
		$add['c_updatetime'] = gdtime();
		$add['c_addtime'] = gdtime();

		$result = M('A_actcard')->add($add);
        if (!$result) {
            return Message(3000,'操作失败');
        }
		$cardid = $result;

        //添加对应的活动红包
        $joinaid = $parr['joinaid'];
        if (!empty($joinaid)) {
            //查询相关活动
            $result = IGD('Index','Newact')->GetShopActivity(1,$joinaid);
            if ($result['code'] != 0) {
                return Message(3000,'活动信息不存在！');
            }
            $activitydata = $result['data'];
            if ($activitydata['c_activitytype'] == 21) {
                $remark = '店铺';
            } else if ($activitydata['c_activitytype'] == 22) {
                $remark = '宝箱';
            } else if ($activitydata['c_activitytype'] == 23) {
                $remark = '气球';
            }

            //添加数据
            $add_data['c_joinaid'] = $joinaid;
            $add_data['c_name'] = $parr['name'];
            $add_data['c_type'] = 3;
            $add_data['c_pid'] = $cardid;
            $add_data['c_img'] = $parr['pimg'];
            $add_data['c_acode'] = $parr['ucode'];
            $add_data['c_value'] = $parr['money'];
            $add_data['c_maxvalue'] = $parr['limit_money'];
            $add_data['c_totalnum'] = $parr['num'];
            $add_data['c_num'] = $parr['num'];
            $add_data['c_status'] = 1;
            $add_data['c_marks'] = $parr['type'];
            $add_data['c_remark'] = $remark;
            $add_data['c_starttime'] = $parr['starttime'];
            $add_data['c_endtime'] = $parr['endtime'];
            $add_data['c_addtime'] = gdtime();
            $add_data['c_updatetime'] = gdtime();
            
            $result = M('A_actprize')->add($add_data);
            if(!$result){
                $db->rollback();
                return Message(1002,"添加失败");
            }

            //扣除卡劵活动库存
            $result = $this->DecCouponCard($cardid,2,$parr['num']);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        }

		$db->commit();
		return MessageInfo(0,'操作成功',$add);
	}

	/**
	 * 操作卡劵(领取中)
	 * @param cid,status(delete)
	 */
	function OptionCouponCard($parr)
	{
        if (!empty($parr['status'])) {
            if ($parr['status'] == 1) {
                $save['c_status'] = 1;
            } else {
                $save['c_status'] = 2;
            }
        }
		
        if (!empty($parr['delete'])) {
            if ($parr['delete'] == 1) {
                $save['c_delete'] = 1;
            } else {
                $save['c_delete'] = 2;
            }
        }
		
		$where['c_id'] = $parr['cid'];
		$result = M('A_actcard')->where($where)->save($save);
		if (!$result) {
			return Message(3000,'操作失败');
		}
		return Message(0,'操作成功');
	}

	/**
	 * 扣除卡劵数量(1扣除剩余数量,2扣除活动剩余数量)
	 * @param cid,type
	 */
	function DecCouponCard($cid,$type,$num)
	{
		$where['c_id'] = $cid;
		if ($type == 2) {
			$where['c_actnum'] = array('EGT',$num);
			$result = M('A_actcard')->where($where)->setDec('c_actnum',$num);
		} else {
			$where['c_num'] = array('EGT',$num);
			$result = M('A_actcard')->where($where)->setDec('c_num',$num);
		}
		if (!$result) {
			return Message(3000,'操作失败');
		}
		return Message(0,'操作成功');
	}

	/**
	 * 领取卡劵
	 * @param ucode,cid,joinaid,remark,sourceid
	 */
	function ReceiveCoupon($parr)
	{
		//查询对应卡劵
		$where['c_id'] = $parr['cid'];
		$where['c_delete'] = 2;
		$couponinfo = M('A_actcard')->where($where)->find();
		if (!$couponinfo) {
			return  Message(3000,'不存在相关卡劵信息');
		}

		// 新增领取记录
        $add['c_sourceid'] = $parr['sourceid']; 
		$add['c_ucode'] = $parr['ucode'];
		$add['c_joinaid'] = $parr['joinaid'];
		$add['c_remark'] = $parr['remark'];
		$add['c_cid'] = $parr['cid'];
		$add['c_acode'] = $couponinfo['c_ucode'];
		$add['c_pcodearr'] = $couponinfo['c_pcodearr'];
		$add['c_name'] = $couponinfo['c_name'];
		$add['c_sign'] = $couponinfo['c_sign'];
		$add['c_type'] = $couponinfo['c_type'];
		$add['c_img'] = $couponinfo['c_img'];
		$add['c_money'] = $couponinfo['c_money'];
		$add['c_limit_money'] = $couponinfo['c_limit_money'];
		$add['c_starttime'] = $couponinfo['c_starttime'];
		$add['c_endtime'] = $couponinfo['c_endtime'];
		$add['c_used_state'] = 0;
		$add['c_addtime'] = gdtime();
		$result = M('A_user_coupons')->add($add);
		if (!$result) {
			return Message(3001,'领取失败');
		}
		$bid = $result;

		//扣除卡劵数量
		$result = $this->DecCouponCard($parr['cid'],1,1);
		if ($result['code'] != 0) {
			return $result;
		}

		// 使用范围添加
		if (!empty($couponinfo['c_pcodearr'])) {
			$pcodearr = explode('|', $couponinfo['c_pcodearr']);
			foreach ($pcodearr as $key => $value) {
				$producedata['c_bid'] = $bid;
				$producedata['c_pcode'] = $value;
				$producedata['c_addtime'] = gdtime();
				$result = M('A_card_in_product')->add($producedata);
				if (!$result) {
					return Message(3002,'使用范围记录失败');
				}
			}
		}

		//改变卡劵领取中状态
		$cardparr['cid'] = $parr['cid'];
		$cardparr['status'] = 1;
		$result = $this->OptionCouponCard($cardparr);
		return MessageInfo(0,'领取成功',$add);
	}

	/**
	 * 查询我的卡劵包
	 * @param ucode,pageindex,pagesize,type(1未过期,2已过期)
	 */
	function MyCouponCard($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_used_state'] = 0;
		$where['c_ucode'] = $parr['ucode'];
		if ($parr['type'] == 2) {
			$where['c_endtime'] = array('LT',gdtime());
		} else {
			$where['c_endtime'] = array('EGT',gdtime());
		}
		$list = M('A_user_coupons')->where($where)->limit($countPage, $pageSize)->order('c_id desc')->select();
		$count = M('A_user_coupons')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        
		if (!$list) {
			$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $userwhere['c_ucode'] = $value['c_acode'];
            $field = 'c_nickname,c_ucode';
            $shopinfo = M('Users')->where($userwhere)->field($field)->find();
            $list[$key]['shopname'] = '所有商家';
            $list[$key]['shopucode'] = '';
            if ($shopinfo) {
            	$list[$key]['shopucode'] = $shopinfo['c_ucode'];
            	$list[$key]['shopname'] = $shopinfo['c_nickname'];
            }
            $list[$key]['couponsign'] = null;
            if (strtotime($value['c_starttime']) > time()) {
                $list[$key]['couponsign'] = '未开始';    //未开始
            }
            $list[$key]['c_starttime'] = str_replace('-', '.', $value['c_starttime']);
            $list[$key]['c_endtime'] = str_replace('-', '.', $value['c_endtime']);
            $showstr = '全部商品';
        	if (!empty($value['c_pcodearr'])) {
        		$showstr = '部分商品';
        	}

            $list[$key]['c_limit_money'] = round($list[$key]['c_limit_money']);
            if ($value['c_type'] == 1) {
            	$list[$key]['c_money'] = sprintf("%.1f",round($value['c_money']));
            	$list[$key]['showstr'] = $showstr.'|'.'满'.$list[$key]['c_limit_money'].'元可用';
            } else if ($value['c_type'] == 2) {
            	$list[$key]['c_money'] = sprintf("%.1f",round($value['c_money'],1));
            	$list[$key]['showstr'] = $showstr.'|'.'最高折扣'.$list[$key]['c_limit_money'].'元';
            } 
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 查询卡劵详情
	 * @param ucode,bid
	 */
	function CouponInfo($parr)
	{
		$where['c_ucode'] = $parr['ucode'];
		$where['c_id'] = $parr['bid'];
		$data = M('A_user_coupons')->where($where)->find();
		if (!$data) {
            return Message(3000, '没有相关数据');
        }

        $data['couponsign'] = null;
        if (strtotime($data['c_starttime']) > time()) {
            $data['couponsign'] = '未开始';    //未开始
        }

        $data['c_limit_money'] = round($data['c_limit_money']);
        $showstr = '全部商品';
    	if (!empty($data['c_pcodearr'])) {
    		$showstr = '部分商品';
    	}
        if ($data['c_type'] == 1) {
        	$data['c_money'] = round($data['c_money']);
        	$data['showstr'] = $showstr.'|'.'满'.$data['c_limit_money'].'元可用';
        } else if ($value['c_type'] == 2) {
        	$data['c_money'] = round($data['c_money'],1);
        	$data['showstr'] = $showstr.'|'.'最高折扣'.$data['c_limit_money'].'元';
        } 

        $userwhere['c_ucode'] = $data['c_acode'];
        $field = 'c_nickname,c_ucode,c_headimg';
        $data['shopinfo'] = M('Users')->where($userwhere)->field($field)->find();
        if ($data['shopinfo']) {
        	$data['shopinfo']['c_headimg'] = GetHost().'/'.$data['shopinfo']['c_headimg'];
        }

        //查询使用产品列表
    	$join = 'left join t_product as b on a.c_pcode=b.c_pcode';
        $prowhere['a.c_bid'] = $data['c_id'];
        $field = 'a.c_pcode,b.c_name,b.c_pimg,b.c_price,b.c_source';
        $productlist = M('A_card_in_product as a')->join($join)->where($prowhere)->field($field)->select();
        foreach ($productlist as $key => $value) {
        	$productlist[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }

        $data['productlist'] = $productlist;

        $data['shoptj'] = $this->TuijianShop(3);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 使用优惠券,返回使用优惠券
	 * @param ucode,bid,desc,used_state(0返回使用，1已使用)
	 */
	function UseCoupon($parr)
	{
		$where['c_ucode'] = $parr['ucode'];
		$where['c_id'] = $parr['bid'];
		$save['c_used_state'] = $parr['used_state'];
		$save['c_used_time'] = gdtime();
		$save['c_desc'] = $parr['desc'];
		$result = M('A_user_coupons')->where($where)->save($save);
		if (!$result) {
			return Message(3000,'操作失败');
		}

		return Message(0,'操作成功');
	}

	/**
	 * 查询推荐商家
	 * @param num
	 */
	function TuijianShop($num)
	{
		$join = 'left join t_users as b on a.c_ucode=b.c_ucode';
		$field = 'a.c_pv,a.c_attention,b.c_ucode,b.c_nickname,b.c_shop,b.c_isfixed1 as c_isfixed,b.c_headimg';
		$list = M('Users_date as a')->join($join)->limit($num)->field($field)->order('a.c_pv desc,a.c_attention')->select();
		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
		}
		return $list;
	}

	/**
	 * 查询订单可使用的优惠券列表（活动订单不可使用卡劵）
	 * @param orderid,pageindex,pagesize
	 */
	function CouponUseList($parr)
	{
		$orderid = $parr['orderid'];

        //查询订单信息
        $orwhere['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($orwhere)->find();
        if (!$orderinfo || !empty($orderinfo['c_activity_id'])) {
            $list = array();
            $data = Page(1, 0, 0, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        if (!empty($orderinfo['c_bid']) || $orderinfo['c_actual_price'] > 0) {
            $list = array();
            $data = Page(1, 0, 0, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        $acode = $orderinfo['c_acode'];

        $ordermoney = $orderinfo['c_total_price'] + $orderinfo['c_free'];

        //查询订单详情
        $orderdetail = M('Order_details')->where($orwhere)->select();
        $pcodesql = 'c_pcodearr is null';
        foreach ($orderdetail as $key => $value) {
            $vpcode = $value['c_pcode'];
            $pcodesql .= " or c_pcodearr like '%$vpcode%'";
            
            $pcodeprice[$vpcode] = $value['c_ptotal'];
        }

        $where[] = array($pcodesql);
        $where[] = array("(c_type='1' and c_limit_money<='$ordermoney') or (c_type='2')");
        $pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_ucode'] = $orderinfo['c_ucode'];
        $where['c_acode'] = $acode;
        $where['c_used_state'] = 0;
        $where['c_endtime'] = array('GT',gdtime());
        $list = M('A_user_coupons')->where($where)->limit($countPage, $pageSize)->order('c_id desc')->select();
        $count = M('A_user_coupons')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $userwhere['c_ucode'] = $value['c_acode'];
            $field = 'c_nickname,c_ucode';
            $shopinfo = M('Users')->where($userwhere)->field($field)->find();
            $list[$key]['shopname'] = '所有商家';
            $list[$key]['shopucode'] = '';
            if ($shopinfo) {
                $list[$key]['shopucode'] = $shopinfo['c_ucode'];
                $list[$key]['shopname'] = $shopinfo['c_nickname'];
            }
            $list[$key]['couponsign'] = null;
            if (strtotime($value['c_starttime']) > time()) {
                $list[$key]['couponsign'] = '未开始';    //未开始
            }
            $list[$key]['c_starttime'] = str_replace('-', '.', $value['c_starttime']);
            $list[$key]['c_endtime'] = str_replace('-', '.', $value['c_endtime']);
            $showstr = '全部商品';
            $zongjia = '';
            if (!empty($value['c_pcodearr'])) {
                $showstr = '部分商品';

                //拆分计算可抵扣总价
                $pcodearr = explode('|', $value['c_pcodearr']);
                $zongjia = '0.00';
                foreach ($pcodearr as $k => $v) {
                    $zongjia += $pcodeprice[$v]; 
                }
            }

            $list[$key]['c_limit_money'] = round($list[$key]['c_limit_money']);
            if ($value['c_type'] == 1) {
                $list[$key]['c_money'] = sprintf("%.1f",round($value['c_money']));
                $list[$key]['showstr'] = $showstr.'|'.'满'.$list[$key]['c_limit_money'].'元可用';
                if (!empty($zongjia)) {
                    $zkzongjia = $zongjia;
                } else {
                    $zkzongjia = $ordermoney;
                }

                if ($zkzongjia >= $list[$key]['c_money']) {
                    $list[$key]['dkmoney'] = $list[$key]['c_money'];
                } else {
                    $list[$key]['dkmoney'] = $zkzongjia;
                }
            } else if ($value['c_type'] == 2) {
                $list[$key]['c_money'] = sprintf("%.1f",round($value['c_money'],1));
                $list[$key]['showstr'] = $showstr.'|'.'最高折扣'.$list[$key]['c_limit_money'].'元';

                if (!empty($zongjia)) {
                    $zkzongjia = round($zongjia*$list[$key]['c_money']);
                } else {
                    $zkzongjia = round($ordermoney*$list[$key]['c_money']);
                }

                if ($zkzongjia >= $list[$key]['c_limit_money']) {
                    $list[$key]['dkmoney'] = $list[$key]['c_limit_money'];
                } else {
                    $list[$key]['dkmoney'] = $zkzongjia;
                }
            }

            if ($list[$key]['dkmoney'] > $ordermoney) {
                $list[$key]['dkmoney'] = $ordermoney;
            }


            if ($orderinfo['c_actual_price'] > 0) {
                $ordermoney = $ordermoney - $orderinfo['c_actual_price'];
                if ($list[$key]['dkmoney'] > $ordermoney) {
                    $list[$key]['dkmoney'] = $ordermoney;
                }
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

    /**
     * 获取订单可用的卡劵总数
     * @param orderid
     */
    function GetUseCouponNum($parr)
    {
        $orderid = $parr['orderid'];

        //查询订单信息
        $orwhere['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($orwhere)->find();
        if (!$orderinfo || !empty($orderinfo['c_activity_id'])) {
            return MessageInfo(0, '查询成功','0');
        }

        if (!empty($orderinfo['c_bid']) || $orderinfo['c_actual_price'] > 0) {
            return MessageInfo(0, '查询成功','0');
        }
        $acode = $orderinfo['c_acode'];

        $ordermoney = $orderinfo['c_total_price'] + $orderinfo['c_free'];

        //查询订单详情
        $orderdetail = M('Order_details')->where($orwhere)->select();
        $pcodesql = 'c_pcodearr is null';
        foreach ($orderdetail as $key => $value) {
            $vpcode = $value['c_pcode'];
            $pcodesql .= " or c_pcodearr like '%$vpcode%'";
        }

        $where[] = array($pcodesql);
        $where[] = array("(c_type='1' and c_limit_money<='$ordermoney') or (c_type='2')");
        $where['c_ucode'] = $orderinfo['c_ucode'];
        $where['c_acode'] = $acode;
        $where['c_used_state'] = 0;
        $where['c_endtime'] = array('GT',gdtime());
        $count = M('A_user_coupons')->where($where)->count();

        $count = ($count <= 0)?'0':$count;
        return MessageInfo(0, '查询成功',$count);
    }
}