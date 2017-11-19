<?php
/**
 * 红包管理相关接口
 */
class RedNewact {
	/**
	 * 红包管理 红包列表
	 * @param ucode,pageindex,pagesize,sign
	 */
	function RedList($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        if ($parr['sign'] == 1) {
        	$where['c_actnum'] = array('GT',0);
        }
		$where['c_ucode'] = $parr['ucode'];
		$where['c_status'] = 1;

		$list = M('A_actred')->where($where)->limit($countPage, $pagesize)->order('c_id desc')->select();

		$count = M('A_actred')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['ydq_num'] = $value['c_totalnum'] - $value['c_num'];
        	$list[$key]['c_addtime'] = date('Y-m-d H:i',strtotime($value['c_addtime']));
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 查询店铺红包发放列表
	 * @param ucode,pageindex,pagesize
	 */
	function ShopRedList($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		//获取平台红包活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $couponwhere['a.c_joinaid'] = $activitydata['c_id'];
        $couponwhere['a.c_acode'] = $parr['ucode'];
        $couponwhere['a.c_status'] = 1;
        // $couponwhere['a.c_delete'] = 2;
        $couponwhere[] = array('b.c_id is not null');
        $join = 'left join t_a_actred as b on a.c_pid=b.c_id';
        $field = 'a.*';
        $order = 'a.c_delete desc,a.c_id desc';
        $list = M('A_redprize as a')->join($join)->where($couponwhere)->order($order)->field($field)->limit($countPage, $pageSize)->select();

        $count = M('A_redprize as a')->join($join)->where($couponwhere)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
        	$list = array();
        	$data = Page($pageIndex, $pageCount, $count, $list);
        	return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['ydq_num'] = $value['c_totalnum'] - $value['c_num'];
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $list[$key]['c_addtime'] = date('Y-m-d H:i',strtotime($value['c_addtime']));
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}	

	/**
	 * 新增红包
	 * @param ucode,name,totalnum,type,money(joinaid)
	 */
	function AddRedCard($parr)
	{	
		//查询开户商户可用金额
		$result = IGD('Balance','User')->GetSyncYesMoney($parr);
        if ($result['code'] == 0 && $result['data']['c_ysdrmoney'] < $parr['money']) {
        	return Message(3001,'发放红包可用金额不足');
        }

		$db = M('');
		$db->startTrans();
		//创建红包
		$add['c_ucode'] = $parr['ucode'];
		$add['c_name'] = $parr['name'];
		$add['c_totalnum'] = $parr['totalnum'];
		$add['c_money'] = $parr['money'];
		$add['c_remain_money'] = $parr['money'];
		$add['c_num'] = $parr['totalnum'];
		$add['c_actnum'] = $parr['totalnum'];
		$add['c_type'] = $parr['type'];
		$add['c_status'] = 1;
		$add['c_updatetime'] = gdtime();
		$add['c_addtime'] = gdtime();
		$result = M('A_actred')->add($add);
		if (!$result) {
			$db->rollback();
			return Message(3000,'操作失败');
		}
		$redid = $result;

		//操作用户余额
		$param['type'] = 0;
		$param['ucode'] = $parr['ucode'];
		$param['money'] = $parr['money'];
		$param['source'] = 18;
		$param['key'] = $result;
		$param['desc'] = "创建红包";
		$param['state'] = 1;
		$param['isagent'] = 0;
		$param['showimg'] = 'Uploads/settlementshow/qit.png';
        $param['showtext'] = '创建红包';	
		$result = IGD('Money','User')->OptionMoney($param);
		if($result['code'] != 0){
			$db->rollback();
			return Message(3001,'扣除用户余额失败');
		}

		//添加对应的活动红包
		$joinaid = $parr['joinaid'];
		if (!empty($joinaid)) {
			//查询相关活动
			$result = IGD('Index','Newact')->GetShopActivity(1,$joinaid);
			if ($result['code'] != 0) {
				return Message(3000,'活动信息不存在！');
			}
			$activitydata = $result['data'];
			if ($activitydata['c_activitytype'] == 20) {
				$remark = '店铺';
			} else if ($activitydata['c_activitytype'] == 22) {
				$remark = '宝箱';
			} else if ($activitydata['c_activitytype'] == 23) {
				$remark = '气球';
			}

			//添加数据
			$add_data['c_joinaid'] = $joinaid;
			$add_data['c_name'] = $parr['name'];
			$add_data['c_type'] = $parr['type'];
			$add_data['c_pid'] = $redid;
			$add_data['c_acode'] = $parr['ucode'];
			$add_data['c_totalnum'] = $parr['totalnum'];
			$add_data['c_num'] = $parr['totalnum'];
			$add_data['c_status'] = 1;
			$add_data['c_marks'] = 3;
			$add_data['c_remark'] = $remark;
			$add_data['c_money'] = $parr['money'];
			$add_data['c_remain_money'] = $parr['money'];
			$add_data['c_addtime'] = gdtime();
			$add_data['c_updatetime'] = gdtime();

			$result = M('A_redprize')->add($add_data);
			if(!$result){
				$db->rollback();
				return Message(1002,"添加失败");
			}

			//扣除卡劵活动库存
			$result = $this->DecCouponCard($redid,2,$parr['totalnum']);
			if ($result['code'] != 0) {
				$db->rollback();
				return $result;
			}
		}

		$db->commit();
		return Message(0,'操作成功');
	}

	/**
	 * 撤回红包
	 * @param rid,ucode
	 */
	function RecallRed($parr){
		$redw['c_id'] = $parr['rid'];
		$redw['c_ucode'] = $parr['ucode'];
		$redinfo = M('A_actred')->where($redw)->find();
		if (!$redinfo) {
			return Message(3000,'红包信息不存在');
		}

		//查询红包奖项
		$rpid['c_pid'] = $redinfo['c_id'];
		$rpinfo = M('A_redprize')->where($rpid)->find();
		if (!$rpinfo) {
			return Message(3000,'红包信息不存在');
		}

		$db = M('');
		$db->startTrans();
		//修改红包状态
		$save_data['c_status'] = 2;
		$save_data['c_updatetime'] = gdtime();
		$result = M('A_actred')->where($redw)->save($save_data);
		if(!$result){
			$db->rollback();
			return Message(1001,"修改红包状态失败");
		}

		//改变所以活动库存
        $prizewhere['c_pid'] = $redinfo['c_id'];
        $prizewhere['c_delete'] = 2;
        $count = M('A_redprize')->where($prizewhere)->count();
        if ($count > 0) {
	        $prizesave['c_delete'] = 1;
	        $prizesave['c_updatetime'] = gdtime();
	        $result = M('A_redprize')->where($prizewhere)->save($prizesave);
	        if (!$result) {
	        	$db->rollback();
	        	return Message(3001,'改变活动删除状态失败');
	        }
        }

        if ($rpinfo['c_remain_money'] > 0) {
			//将剩余红包金额退回到用户余额
			$param['type'] = 1;
			$param['ucode'] = $parr['ucode'];
			$param['money'] = $rpinfo['c_remain_money'];
			$param['source'] = 18;
			$param['key'] = $rpinfo['c_id'];
			$param['desc'] = "红包撤回金额";
			$param['state'] = 1;
			$param['isagent'] = 0;
			$param['showimg'] = 'Uploads/settlementshow/hong.png';
	        $param['showtext'] = '红包撤回金额';		
			$result = IGD('Money','User')->OptionMoney($param);
			if($result['code'] != 0){
				$db->rollback();
				return Message(3001,'修改用户余额失败');
			}

			if (empty($parr['desc'])) {
				$parr['desc'] = '您撤回红包成功，系统已将剩余金额自动退回余额';
			}
			//给用户发送相关消息
	        $Msgcentre = IGD('Msgcentre', 'Message');
	        $msgdata['ucode'] = $parr['ucode'];
	        $msgdata['type'] = 0;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['content'] = $parr['desc'];
	        $msgdata['tag'] = 2;
	        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        $Msgcentre->CreateMessege($msgdata);
        }
		$db->commit();

		return Message(0,'操作成功');
	}

	/**
	 * 查询发放红包总金额、总个数
	 * @param ucode
	 */
	function MyRedCard($parr)
	{
		$redw['c_ucode'] = $parr['ucode'];
		$redw['c_status'] = 1;
		$redlog = M('A_actred')->where($redw)->select();

		$all_money = 0;
		$remain_money = 0;

		$all_num = 0;
		$remain_num = 0;
		foreach ($redlog as $key => $value) {
			$all_money = sprintf('%.2f',$all_money + $value['c_money']);
			$remain_money = sprintf('%.2f',$remain_money + $value['c_remain_money']);

			$all_num = $all_num + $value['c_totalnum'];
			$remain_num = $remain_num + $value['c_num'];
		}

		$data['all_money'] = $all_money;
		$data['all_num'] = $all_num;
		$data['money'] = sprintf('%.2f',$all_money - $remain_money);
		$data['num'] = $all_num - $remain_num;

		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 查询发放红包记录
	 * @param ucode
	 */
	function MyRedCardLog($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$where['c_ucode'] = $parr['ucode'];
		$where['c_status'] = 1;
		$list = M('A_actred')->where($where)->limit($countPage, $pagesize)->order('c_id desc')->select();

		$count = M('A_actred')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $list);
        }

        foreach ($list as $key => $value) {
            $list[$key]['money'] = sprintf('%.2f',$value['c_money'] - $value['c_remain_money']);
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 查询店铺发放红包总金额、总个数
	 * @param ucode
	 */
	function ShopRedCard($parr)
	{
		$redw['c_acode'] = $parr['ucode'];
		$redw['c_status'] = 1;
		$redlog = M('A_redprize')->where($redw)->select();

		$all_money = 0;
		$remain_money = 0;

		$all_num = 0;
		$remain_num = 0;
		foreach ($redlog as $key => $value) {
			$all_money = sprintf('%.2f',$all_money + $value['c_money']);
			$remain_money = sprintf('%.2f',$remain_money + $value['c_remain_money']);

			$all_num = $all_num + $value['c_totalnum'];
			$remain_num = $remain_num + $value['c_num'];
		}

		$data['all_money'] = $all_money;
		$data['all_num'] = $all_num;
		$data['money'] = sprintf('%.2f',$all_money - $remain_money);
		$data['num'] = $all_num - $remain_num;

		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 查询店铺红包发放记录
	 * @param ucode
	 */
	function ShopRedCardLog($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        //获取平台红包活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $where['c_joinaid'] = $activitydata['c_id'];
        $where[] = array("c_pid is not null or c_pid<>''");
		$where['c_acode'] = $parr['ucode'];
		$where['c_status'] = 1;
		$list = M('A_redprize')->where($where)->limit($countPage, $pagesize)->order('c_id desc')->select();

		$count = M('A_redprize')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $list);
        }

        foreach ($list as $key => $value) {
            $list[$key]['money'] = sprintf('%.2f',$value['c_money'] - $value['c_remain_money']);
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 红包领取详情 红包信息
	 * @param rid
	 */
	function OneRedInfo($parr)
	{
		$where['c_id'] = $parr['rid'];
		$data = M('A_actred')->where($where)->find();
		if (!$data) {
            return Message(3000, '没有相关数据');
        }
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 获取单个店铺红包详情
	 * @param awid
	 */
	function OneRedAwidInfo($parr)
	{
		$where['c_id'] = $parr['awid'];
		$data = M('A_redprize')->where($where)->find();
		if (!$data) {
            return Message(3000, '没有相关数据');
        }
        return MessageInfo(0, '查询成功', $data);
	}
	
	/**
	 * 红包领取详情 领取记录
	 * @param ucode,rid,pageindex,pagesize,(sign,awid)
	 */
	function RedReceiveLog($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        if ($parr['sign'] == 1) {
        	$where['a.c_awid'] = $parr['awid'];
        } else {
        	$where['a.c_pid'] = $parr['rid'];
        }

		// $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
		$field = 'a.*';

		$list = M('A_actredlog as a')->field($field)->where($where)->limit($countPage, $pagesize)->select();

		$count = M('A_actredlog as a')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $list);
        }

        foreach ($list as $key => $value) {
        	$uw['c_ucode'] = $value['c_ucode'];
        	$userinfo = M('Users')->where($uw)->field('c_nickname,c_headimg')->find();
        	$list[$key]['c_nickname'] = $userinfo['c_nickname'];
        	$list[$key]['newtime'] = date('m/d H:i',strtotime($value['c_addtime']));
            $list[$key]['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 红包领取详情 参与活动记录
	 * @param ucode,rid,pageindex,pagesize
	 */
	function RedActivityLog($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$where['a.c_pid'] = $parr['rid'];
		$where['a.c_delete'] = 2;
		$join = 'left join t_actjoin_moneylog as b on a.c_joinaid=b.c_id';
		$field = 'a.*,b.c_remark,b.c_activityname,b.c_listimg as actimg,b.c_activitystarttime,b.c_activityendtime';
		$list = M('A_redprize as a')->join($join)->where($where)->limit($countPage, $pagesize)->field($field)->order('a.c_id desc')->group('a.c_joinaid')->select();
		
		$count = M('A_redprize as a')->join($join)->where($where)->group('a.c_joinaid')->count();
        $pageCount = ceil($count / $pageSize);
		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
            return Message(3000, '没有相关数据');
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $list[$key]['actimg'] = GetHost() . '/' . $value['actimg'];
            $list[$key]['ydq_num'] = $value['c_totalnum'] - $value['c_num'];
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

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 扣除红包数量(1扣除剩余数量,2扣除活动剩余数量)
	 * @param rid,type
	 */
	function DecCouponCard($rid,$type,$num)
	{
		$where['c_id'] = $rid;
		if ($type == 2) {
			$where['c_actnum'] = array('EGT',$num);
			$result = M('A_actred')->where($where)->setDec('c_actnum',$num);
		} else {
			$where['c_num'] = array('EGT',$num);
			$result = M('A_actred')->where($where)->setDec('c_num',$num);
		}
		if (!$result) {
			return Message(3000,'操作失败');
		}
		return Message(0,'操作成功');
	}

	/**
	 * 发放店铺红包
	 * @param ucode,rid,num
	 */
	function GrantRed($parr)
	{
        //获取平台红包活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $db = M('');
        $db->startTrans();

        $result = $this->GrantOptionRed($parr,$activitydata);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        $db->commit();
        return Message(0, '发放成功');
	}

	/**
	 * 发放活动红包操作
	 * @param ucode,rid,num,$activitydata
	 */
	function GrantOptionRed($parr,$activitydata){
		$redw['c_id'] = $parr['rid'];
		$redw['c_ucode'] = $parr['ucode'];
		$data = M('A_actred')->where($redw)->find();
		if(!$data){
			return Message(3000,"红包已发放完或者已领取完");
		}

		if ($parr['num'] > $data['c_actnum']) {
            return Message(3001, '发放数量不能大于可发放总数量');
        }

		//修改红包状态
		$result = $this->DecCouponCard($parr['rid'],2,$parr['num']);
		if($result['code'] != 0){
			return Message(1001,"扣除红包数量失败");
		}

		// 新增奖项记录
		$fmoney = sprintf('%.2f',$data['c_money']*($parr['num']/$data['c_totalnum']));
        $add['c_acode'] = $parr['ucode'];
        $add['c_joinaid'] = $activitydata['c_id'];
        $add['c_name'] = $data['c_name'];
        $add['c_type'] = $data['c_type'];
        $add['c_img'] = $data['c_img'];
        $add['c_pid'] = $data['c_id'];        
        $add['c_money'] = $fmoney;
        $add['c_remain_money'] = $fmoney;
        $add['c_marks'] = 3;
        $add['c_totalnum'] = $parr['num'];
        $add['c_num'] = $parr['num'];
        $add['c_remark'] = '店铺';
        $add['c_status'] = 1;
        $add['c_updatetime'] = gdtime();
        $add['c_addtime'] = gdtime();
        $result = M('A_redprize')->add($add);
		if(!$result){
			return Message(1002,"发放失败");
		}

		return Message(0,"发放成功");
	}

	/**
	 * 查询红包
	 * @param ucode,acode
	 */
	function ViewShopRed($parr)
	{
		$ucode = $parr['ucode'];
		// if (empty($ucode)) {
		// 	return Message(1009, '请先登录');
		// }
		$acode = $parr['acode'];

		//获取平台红包活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        //获取是否在首页发现记录
        $result = IGD('Start','Newact')->GetShowLog($ucode,$acode,1,0,1);
        $showlog = $result['data'];        
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;
        $prizewhere['c_joinaid'] = $activitydata['c_id'];
        if ($showlog['c_acode'] == $acode) {
        	//查询卡劵奖项信息
			$prizewhere['c_id'] = $showlog['c_awid'];
			$prizedata = M('A_redprize')->where($prizewhere)->find();
			if (!$prizedata) {
				return Message(3000, '没有相关数据');
			}
        } else {
        	//查询卡劵奖项信息
        	$prizewhere['c_acode'] = $acode;
			$prizedata = M('A_redprize')->where($prizewhere)->order('rand()')->find();
			if (!$prizedata) {
				return Message(3000, '没有相关数据');
			}
        }

        if ($prizedata['c_pid']) {
			//查询用户是否已领取
			$logwhere['c_ucode'] = $parr['ucode'];
	        $logwhere['c_awid'] = $prizedata['c_id'];
	        $logwhere['c_state'] = 1;
	        $logwhere['c_addtime'] = array('between',array(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')));
	        $logfield = 'c_id';
	        $receivedata = M('A_actredlog')->where($logwhere)->field($logfield)->find();
	        if ($receivedata) {
	        	return Message(3001, '已经领取过该红包，试试其他的');
	        }
		}

		$prizedata['sid'] = $showlog['c_id'];
        return MessageInfo(0,'查询成功',$prizedata);
	}

	/**
	 * 领取红包
	 * @param ucode,awid,sid
	 */
	function ReceiveRed($parr)
	{	
		$ucode = $parr['ucode'];
		$app_client = $parr['app_client'];
		if (empty($ucode)) {

			if ($app_client == 'IOS') {
				$data['types'] = '';
				$data['name'] = '福旺财旺人气旺，山高水长福寿长';
				return MessageInfo(1009, '请先登录',$data);
			}
			return Message(1009, '请先登录');
			
		}
		$awid = $parr['awid'];

		//获取平台红包活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $db = M('');
		$db->startTrans();
       
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;
		$prizewhere['c_id'] = $awid;
		$prizewhere['c_joinaid'] = $activitydata['c_id'];
		$prizedata = M('A_redprize')->where($prizewhere)->find();
		if (!$prizedata) {
			if ($app_client == 'IOS') {
				$data['types'] = '';
				$data['name'] = '福旺财旺人气旺，山高水长福寿长';
				$db->rollback();
				return MessageInfo(3000, '红包已被抢光，进店逛逛吧~',$data);
				
			}
			$db->rollback();
			return Message(3000, '红包已被抢光，进店逛逛吧~');
		}
        
		//领取红包
		if ($prizedata['c_acode']) {
			//查询用户是否已领取
			$logwhere['c_ucode'] = $parr['ucode'];
	        $logwhere['c_awid'] = $prizedata['c_id'];
	        $logwhere['c_state'] = 1;
	        $logwhere['c_addtime'] = array('between',array(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')));
	        $logfield = 'c_id';
	        $receivedata = M('A_actredlog')->where($logwhere)->field($logfield)->find();
	        if ($receivedata) {

	        	if ( $app_client == 'IOS' ) {
	        		$data['types'] = '';
		        	$data['name'] = '福旺财旺人气旺，山高水长福寿长';
		        	$db->rollback();
		        	return MessageInfo(3001, '已经领取过该红包，试试其他的',$data);
	        	}
	        	$db->rollback();
		        return Message(3001, '已经领取过该红包，试试其他的');
	        	
	        }
		}

		//计算红包金额
		if ($prizedata['c_type'] == 2) {   //拼手气红包
			$money_arr = $this->randBonus($prizedata['c_remain_money'],$prizedata['c_num'],1);
			$money = $money_arr[0];
		} else if ($prizedata['c_type'] == 1) {		//普通红包
			$money = bcdiv($prizedata['c_money'], $prizedata['c_totalnum'],2);
		} else {	//随机红包
			$money = rand($prizedata['c_value']*100,$prizedata['c_maxvalue']*100)/100;
		}

		if ($prizedata['c_marks'] == 3) {	//商家创建红包
			$premark = '店铺红包';
		} else if ($prizedata['c_marks'] == 2) {		//平台普通红包
			$premark = '平台红包';
		} else {	//商家红包
			$premark = '商家红包';
			$money = $prizedata['c_value'];
		}

		//写入领取记录
		$parr['joinaid'] = $prizedata['c_joinaid'];
		$parr['pid'] = $prizedata['c_pid'];
		$parr['acode'] = $prizedata['c_acode'];
		$parr['name'] = $prizedata['c_name'];
		$parr['img'] = $prizedata['c_img'];
		$parr['value'] = $money;
		$parr['marks'] = $premark;
		$parr['type'] = 2;
		$parr['state'] = 1;
		$result = IGD('Index','Newact')->WriteRedReciveLog($parr);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}

		//扣除奖项数量
		$prisave['c_remain_money'] = bcsub($prizedata['c_remain_money'], $money,2);
		$prisave['c_num'] = $prizedata['c_num'] - 1;
		$prizewhere['c_remain_money'] = array('EGT',$money);
		$result = M('A_redprize')->where($prizewhere)->save($prisave);
		if (!$result) {
			if ( $app_client == 'IOS' ) {
				$data['types'] = '';
				$data['name'] = '福旺财旺人气旺，山高水长福寿长';
				$db->rollback();
				return MessageInfo(3002, '扣除奖项剩余数量失败',$data);
			}
			$db->rollback();
			return Message(3002, '扣除奖项剩余数量失败');
		}

		if ($prizedata['c_acode'] && $prizedata['c_pid']) {
			/*//修改红包信息
			$result = $this->DecCouponCard($prizedata['c_pid'],1,1);
			if($result['code'] != 0){
				$db->rollback();
				return Message(1001,"扣除红包数量失败");
			}

			//扣除红包管理总金额
			$redwhere['c_id'] = $prizedata['c_pid'];
			$result = M('A_actred')->where($redwhere)->setDec('c_remain_money',$money);
			if(!$result){
				$db->rollback();
				return Message(1001,"扣除红包总金额失败");
			}	*/
		}

		if (!empty($parr['sid'])) {
			//改变发现领取状态
			$stw['c_id'] = $parr['sid'];
			$logsave['c_status'] = 3;
			$logsave['c_ptype'] = 2;
			$logsave['c_value'] = $money;
	        $logsave['c_receivetime'] = gdtime();
	        $logsave['c_updatetime'] = gdtime();
	        $result = M('A_start_log')->where($stw)->save($logsave);
	        if (!$result) {
	        	if ( $app_client == 'IOS' ) {
	        		$data['types'] = '';
		        	$data['name'] = '福旺财旺人气旺，山高水长福寿长';
		        	$db->rollback();
		            return MessageInfo(3001, '领取失败',$data);
	        	}
	        	$db->rollback();
		        return Message(3001, '领取失败',$data);
	        	
	        }
		}
		

		//操作用户余额
		$param['type'] = 1;
		$param['ucode'] = $ucode;
		$param['money'] = $money;
		$param['source'] = 18;
		$param['key'] = $prizedata['c_id'];
		$param['desc'] = '在店铺中领取商家发出的红包';
		$param['state'] = 1;
		$param['isagent'] = 0;
		$param['joinaid']= $prizedata['c_joinaid'];
		$param['showimg'] = 'Uploads/settlementshow/hong.png';
        $param['showtext'] = '领取红包';	
		$result = IGD('Money','User')->OptionMoney($param);
		if($result['code'] != 0){

			if ( $app_client == 'IOS' ) {
				$data['types'] = '';
				$data['name'] = '福旺财旺人气旺，山高水长福寿长';
				$db->rollback();
				return MessageInfo(3002,'修改用户余额失败',$data);
			}
			$db->rollback();
			return Message(3002,'修改用户余额失败');
			
		}

		//给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = '成功领取红包金额￥' .$money. '，已存入余额';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $Msgcentre->CreateMessege($msgdata);

		$db->commit();

		if ($prizedata['c_marks'] == 2) {		//平台普通红包
			$parr['name'] = '恭喜您!成功领取普通红包';
		} else {	//商家红包
			$parr['name'] = '恭喜您!成功领取商家红包';
		}
		return MessageInfo(0,'领取成功',$parr);
	}

	//分取金额
	function randBonus($total=0, $count=3, $type=1){
		$avg = number_format($total/$count, 2);
		if($type==1 && $avg>0.01){
			$input     = range(0.01, $total, 0.01);
			if($count>1){
				$rand_keys = (array) array_rand($input, $count-1);
				$last    = 0;
				foreach($rand_keys as $i=>$key){
					$current  = $input[$key]-$last;
					$items[]  = round($current,2);
					$last    = $input[$key];
				}
			}
			$items[]    = $total-array_sum($items);
		}else{
			$i       = 0;
			while($i<$count){
			  	$items[]  = $i<$count-1?$avg:number_format(($total-array_sum($items)),2);
			  	$i++;
			}
		}
		return $items;
	}

	/**
	 * 获取商家基本信息
	 * @param acode,ucode
	 */
	function GetShopBaseInfo($parr)
	{
		//查询商家访问信息
		$join = 'left join t_users_date as b on a.c_ucode=b.c_ucode';
		$usw['a.c_ucode'] = $parr['acode'];
		$field = 'a.c_nickname,a.c_signature,a.c_shop,a.c_isfixed1 as c_isfixed,a.c_headimg,a.c_latitude1,a.c_longitude1,b.c_pv,b.c_attention';
		$user = M('Users as a')->join($join)->where($usw)->field($field)->find();
		$user['c_headimg'] = GetHost().'/'.$user['c_headimg'];
		$user['c_pv'] = changenum($user['c_pv']);
		$user['c_attention'] = changenum($user['c_attention']);

		//查询是否关注
		$aw['c_ucode'] = $parr['ucode'];
        $aw['c_attention_ucode'] = $parr['acode'];
        $count = M('Users_attention')->where($aw)->count();
        if($count == 0){
        	$user['is_attention'] = 0;
        }else{
        	$user['is_attention'] = 1;
        }

		return MessageInfo(0,"查询成功",$user);
	}

	/**
	 * 定时器返回店铺红包24小时红包
	 * @param
	 */
	function RecallShopRed()
	{
		//获取店铺红包活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $where['c_joinaid'] = $activitydata['c_id'];
        $where[] = array("c_pid is not null or c_pid<>''");
        $where[] = array("c_acode is not null or c_acode<>''");
		$where['c_delete'] = 2;
		$where['c_remain_money'] = array('GT',0);
		$where['c_addtime'] = array('ELT',date('Y-m-d H:i:s',strtotime('-1 days')));
		$list = M('A_redprize')->where($where)->limit(20)->order('c_id desc')->select();
		if (!$list) {
            return Message(0, '没有记录');
        }

        $num = 0;
        foreach ($list as $key => $value) {
        	$rparr['rid'] = $value['c_pid'];
			$rparr['ucode'] = $value['c_acode'];
			$rparr['desc'] = '店铺红包24小时未领完，系统已将剩余金额自动退回余额';
			$result = $this->RecallRed($rparr);
			if ($result['code'] == 0) {
				$num++;
			}
        }
        return MessageInfo(0, '操作成功',$num);
	}


	/**
	 * 红包领取完毕改变状态
	 * @param string $value [description]
	 */
	function ChangceRed()
	{
		//获取店铺红包活动详情
		$result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        $where['c_joinaid'] = $activitydata['c_id'];
        $where[] = array("c_pid is not null or c_pid<>''");
        $where[] = array("c_acode is not null or c_acode<>''");
		$where['c_delete'] = 2;
		$where['c_remain_money'] = array('EQ',0);
		$where['c_num'] = array('GT',0);
		// $where['c_addtime'] = array('ELT',date('Y-m-d H:i:s',strtotime('-1 days')));
		$list = M('A_redprize')->where($where)->limit(20)->order('c_id desc')->select();
		if (!$list) {
            return Message(0, '没有记录');
        }

        $num = 0;
        foreach ($list as $key => $value) {
        	$pw['c_id'] = $value['c_id'];
        	$psave['c_num'] = 0;
        	$result = M('A_redprize')->where($pw)->save($psave);
			if ($result) {
				$num++;
			}
        }

        return MessageInfo(0, '操作成功',$num);
	}
	
}