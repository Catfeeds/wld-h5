<?php

/**
 * 	抢购活动中心
 *
 */
class BuyingActivity {

	/**
	 * 立即抢购
	 * @param ucode,pid,aid
	 */
	function ImmediatelyBuy($parr)
	{
		$ucode = $parr['ucode'];
		if (empty($ucode)) {
			return Message(1009,'请登录后再操作');
		}

		// 查询活动
		$activitywhere['c_id'] = $parr['aid'];
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(2000, '活动已结束！');
        }

        //查询奖品
        $prizewhere['c_state'] = 1;
        $prizewhere['c_type'] = 2;
        $prizewhere['c_num'] = array('GT', 0);
        $prizewhere['c_aid'] = $activitydata['c_id'];
        $prizewhere['c_id'] = $parr['pid'];
        $prizedata = M('Activity_prize')->where($prizewhere)->find();
        if (!$prizedata) {
        	return Message(2001, '该商品已被抢购完，感谢您的参与！');
        }

        // 查询本期活动是否已中奖
        $prizelogwhere['c_aid'] = $activitydata['c_id'];
        $prizelogwhere['c_ucode'] = $ucode;
        $prizelog = M('Activity_log')->where($prizelogwhere)->find();
        if ($prizelog) {
        	return Message(2002, '本期活动您已参与一次抢购，请等待第二期抢购！');
        }

        $db = M('');
        $db->startTrans();
        //抢购生成订单
        $result = IGD('Actoder','Order')->CreataOrderInfo($ucode,$activitydata,$prizedata);
		if ($result['code'] != 0) {
			$db->rollback(); //不成功，则回滚
			return $result;
		}
		$prizedata['orderid'] = $result['data'];

        $result = M('Activity_prize')->where($prizewhere)->setDec('c_num',1);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '扣除奖项库存失败！');
        }

        //写入中奖记录
		$prizelogdata['c_ucode'] = $parr['ucode'];
        $prizelogdata['c_pcode'] = $prizedata['c_pcode'];
        $prizelogdata['c_aid'] = $prizedata['c_aid'];
        $prizelogdata['c_pid'] = $prizedata['c_id'];
        $prizelogdata['c_value'] = $prizedata['c_value'];
        $prizelogdata['c_type'] = $prizedata['c_type'];
		$result = IGD('Activity','Activity')->WriteActlog($prizelogdata);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '记录写入失败');
        }

        $db->commit();
        // 写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
      	$msgdata['type'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $prizedata['orderid'];
        $msgdata['content'] = '您在'.$activitydata['c_activityname'].'活动中获得一个奖品：' . $prizedata['c_name'] . '，已转入订单';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $prizedata['orderid'];
   		$msgresult = $Msgcentre->CreateMessege($msgdata);

        return Message(0,'抢购成功,该产品已转入您的订单');
	}

	/**
	 * 查询发起的活动
	 * @param ucode,pageindex,pagesize
	 */
	function ReferMyacivity($parr)
	{
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
		$join = 'left join t_activity as b on a.c_aid=b.c_id';
		$where['a.c_acode'] = $parr['ucode'];
		$where['a.c_state'] = 1;
		$where['b.c_show'] = 1;
		$where['b.c_state'] = 1;
		$where['b.c_sign'] = 2;
		$where[] = array('b.c_activitytype <> 2 and b.c_activitytype <> 6 and b.c_activitytype <> 7');
		$field = 'a.*,b.c_activityname,b.c_activitystarttime,b.c_activityendtime,b.c_pimg,b.c_activitytype';
		$limit = $countPage.','.$pageSize;
		$order = 'a.c_addtime desc';
		$list = M('Activity_prize as a')->join($join)->where($where)->field($field)->limit($limit)->order($order)->select();
		if (count($list) == 0) {
			return MessageInfo(0, "查询成功", $list);
		}
		foreach ($list as $key => $value) {
			$nickwhere['c_ucode'] = $value['c_acode'];
			$list[$key]['c_nickname'] = M('Users')->where($nickwhere)->getField('c_nickname');
			if (strtotime($value['c_activitystarttime']) > time()) {
                $list[$key]['progress'] = 0;
            } else if (strtotime($value['c_activitystarttime']) <= time() && strtotime($value['c_activityendtime']) >= time()) {
                $list[$key]['progress'] = 1;
            } else {
                $list[$key]['progress'] = 2;
            }
		}
		$count = M('Activity_prize as a')->join($join)->where($where)->count();
		$pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
	}

	/**
	 * 查询发起的活动详情
	 * @param ucode,pid
	 */
	function ReferMyacivityDetail($parr)
	{
		$where['a.c_id'] = $parr['pid'];
		$join = 'left join t_users as b on a.c_acode=b.c_ucode';
		$field = 'a.*,b.c_nickname,b.c_headimg';
		$data = M('Activity_prize as a')->join($join)->where($where)->field($field)->find();
		$data['c_headimg'] = GetHost().'/'.$data['c_headimg'];
		$imgpath = explode('|',$data['c_imgpath']);
		foreach ($imgpath as $key => $value) {
			$data['c_imgsrc'][$key] = GetHost().'/'.$value;
		}
		$activitywhere['c_id'] = $data['c_aid'];
		$data['theme'] = M('Activity')->where($activitywhere)->find();
		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 发起活动上传活动奖品
	 * @param aid,ucode,name,value,num,type,imgpath
	 */
	function JoinActivity($parr)
	{
		$add['c_aid'] = $parr['aid'];
		$add['c_acode'] = $parr['ucode'];
		if ($parr['type'] == 2) {
			$add['c_pcode'] = time();
		}
		$add['c_name'] = $parr['name'];
		$add['c_value'] = $parr['value'];
		$add['c_totalnum'] = $parr['num'];
		$add['c_num'] = $parr['num'];
		$add['c_state'] = 1;
		$add['c_type'] = $parr['type'];
		$add['c_imgpath'] = $parr['imgpath'];
		$add['c_addtime'] = date('Y-m-d H:i:s');
		$result = M('Activity_prize')->add($add);
		if (!$result) {
			return Message(1000,'发起失败');
		}
		return Message(0,'发起成功');
	}

	/**
	 * 查询获奖名单列表
	 * @param pid
	 */
	function GetWinList($parr)
	{
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
		$join = 'left join t_activity_prize as b on a.c_pid=b.c_id';
		$where['a.c_pid'] = $parr['pid'];
		$field = 'b.*,a.c_addtime as gettime,a.c_ucode';
		$limit = $countPage.','.$pageSize;
		$order = 'a.c_addtime desc';
		$list = M('Activity_log as a')->join($join)->where($where)->field($field)->limit($limit)->order($order)->select();
		if (count($list) == 0) {
			return MessageInfo(0, "查询成功", $list);
		}
		foreach ($list as $key => $value) {
			$list[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
			$nickwhere['c_ucode'] = $value['c_ucode'];
			$list[$key]['c_headimg'] = GetHost().'/'.M('Users')->where($nickwhere)->getField('c_headimg');
		}
		$count = M('Activity_log as a')->join($join)->where($where)->count();
		$pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
	}

	/**
	 * 查询我参与的活动列表
	 * @param ucode,pageindex,pagesize
	 */
	function MyJoinList($parr)
	{
		$ucode = $parr['ucode'];
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $join = 'left join t_activity as b on a.c_aid=b.c_id';
		$where[] = array("a.c_id in (select c_pid from t_activity_log where c_ucode='$ucode' and c_type=2 group by c_pid)");
		$where['b.c_show'] = 1;
		$where['b.c_sign'] = 2;
		$where[] = array('b.c_activitytype <> 2 and b.c_activitytype <> 6 and b.c_activitytype <> 7');
		$field = 'a.*,b.c_activityname,b.c_activitystarttime,b.c_activityendtime,b.c_activitytype';
		$limit = $countPage.','.$pageSize;
		$order = 'a.c_addtime desc';
		$list = M('Activity_prize as a')->join($join)->where($where)->field($field)->limit($limit)->order($order)->select();
		if (count($list) == 0) {
			return MessageInfo(0, "查询成功", $list);
		}
		foreach ($list as $key => $value) {
			$nickwhere['c_ucode'] = $value['c_acode'];
			$list[$key]['c_nickname'] = M('Users')->where($nickwhere)->getField('c_nickname');
			$list[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
			if (strtotime($value['c_activitystarttime']) > time()) {
                $list[$key]['progress'] = 0;
            } else if (strtotime($value['c_activitystarttime']) <= time() && strtotime($value['c_activityendtime']) >= time()) {
                $list[$key]['progress'] = 1;
            } else {
                $list[$key]['progress'] = 2;
            }
		}
		$count = M('Activity_prize as a')->join($join)->where($where)->count();
		$pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
	}

}