<?php

/**
 * 	聚宝活动
 *
 */
class JubaoActivity {

	/**
	 * 积累宝库
	 * @param ucode,pcode
	 */
	function OpenDotey($parr)
	{
		if (empty($parr['ucode'])) {
			return Message(1009,'请先登录，再操作！');
		}

		//查询聚宝记录
		$doteywhere['c_ucode'] = $parr['ucode'];
		$doteyinfo = M('Activity_dotey')->where($doteywhere)->find();
		if ($doteyinfo) {
			return Message(3001,'您已参与一次');
		}

		//查询平台活动是否存在
        $activitydata = $this->GetPlathavingAct('15')['data'];
        if (!$activitydata) {
            return Message(3002, '平台尚未开放此活动！');
        }

        //查询宝库
		$moneywhere['c_aid'] = $activitydata['c_id'];
		$moneydata = M('Activity_money')->where($moneywhere)->find();
		if (!$moneydata) {
			return Message(3002, '平台尚未开放此活动！');
		}

		$starttime = strtotime($moneydata['c_starttime']);
		$endtime = strtotime($moneydata['c_endtime']);
		if ($starttime <= time()) {
			return Message(3002, '金额发放中，暂不能集！');
		}
		if ($endtime <= time()) {
			return Message(3002, '金额发放已结束，暂不能集！');
		}

        $db = M();
		$db->startTrans();

		//随机增加聚宝库总金额
		$moneyvalue = rand($moneydata['c_min_money']*100,$moneydata['c_max_money']*100)/100;
		$result = M('Activity_money')->where($moneywhere)->setInc('c_money',$moneyvalue);
		if (!$result) {
			$db->rollback();
            return Message(3005, '金额增加失败！');
        }

		//添加聚宝记录
		$parr['aid'] = $activitydata['c_id'];
		$parr['portion'] = 1;
		$result = $this->AddDoteyLog($parr);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}

		//判断多余次数
		$doteycount = M('Activity_dotey')->where($doteywhere)->count();
		if ($doteycount > 1) {
			$db->rollback();
			return Message(3001,'您已参与一次');
		}

		//给分享人增加聚宝份数
		if (!empty($parr['pcode'])) {
			$sharewhere['c_ucode'] = $parr['pcode'];
			$pusercode = M('Users')->where($sharewhere)->getField('c_ucode');
			if ($pusercode && $pusercode != $parr['ucode']) {
				$sharewhere['c_aid'] = $activitydata['c_id'];
				$shareucodeinfo = M('Activity_dotey')->where($sharewhere)->find();
				if ($shareucodeinfo) {
					$portionlimit = IGD('Redis', 'Redis')->Gethash('activity')['data']['portionlimit'];
					if ($shareucodeinfo['c_portion'] < $portionlimit && !empty($portionlimit)) {
						$result = M('Activity_dotey')->where($sharewhere)->setInc('c_portion',1);
						if (!$result) {
							$db->rollback();
				            return Message(3003, '记录增加失败！');
				        }
					}
				} else {
					$parr1['ucode'] = $parr['pcode'];
					$parr1['aid'] = $activitydata['c_id'];
					$parr1['portion'] = 1;
					$result = $this->AddDoteyLog($parr1);
					if ($result['code'] != 0) {
						$db->rollback();
						return $result;
					}
				}
			}

		}

		$db->commit();
		return MessageInfo(0,'参与成功',$moneyvalue);
	}

	/**
	 * 增加聚宝记录
	 * @param ucode,pcode,aid,portion
	 */
	public function AddDoteyLog($parr)
	{
		//添加寻宝记录
		$doteydata['c_ucode'] = $parr['ucode'];
		$doteydata['c_pcode'] = $parr['pcode'];
		$doteydata['c_aid'] = $parr['aid'];
		$doteydata['c_portion'] = $parr['portion'];
		$doteydata['c_addtime'] = date('Y-m-d H:i:s');
		$result = M('Activity_dotey')->add($doteydata);
		if (!$result) {
			return Message(3000, '记录失败！');
		}
		return Message(0, '记录成功！');
	}

	/**
	 * 领取对应份数金额
	 * @param ucode,aid
	 */
	function GetPointGif($parr)
	{
		if (empty($parr['ucode'])) {
			return Message(1009,'请先登录，再操作！');
		}

		//查询平台活动是否存在
		$activitywhere['c_id'] = $parr['aid'];     //活动开启状态
        $activitywhere['c_state'] = 1;            //活动开启状态
        $activitywhere['c_activitytype'] = 15;     //聚宝活动标识
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(3002, '活动未开始！');
        }

        //查询金额表
        $moneywhere['c_aid'] = $parr['aid'];
        $moneywhere['c_starttime'] = array('ELT', date('Y-m-d H:i:s'));
        $moneywhere['c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
		$moneydata = M('Activity_money')->where($moneywhere)->find();
		if (!$moneydata) {
            return Message(3003, '非领取时间，不能领取！');
        }

        if ($moneydata['c_money'] <= 0) {
        	return Message(1000, '金额已领取完！');
        }

		//查询聚宝记录
		$doteywhere['c_ucode'] = $parr['ucode'];
		$doteywhere['c_aid'] = $parr['aid'];
		$doteyinfo = M('Activity_dotey')->where($doteywhere)->find();
		if (!$doteyinfo) {
			return Message(3001,'您尚未参与该活动');
		}

		if ($doteyinfo['c_state'] == 1) {
			return Message(3001,'您已领取！');
		}

		//查询奖品表
        $prizewhere['c_state'] = 1;
        $prizewhere['c_num'] = array('EGT', $doteyinfo['c_portion']);
        $prizewhere['c_aid'] = $activitydata['c_id'];
        $prizewhere['c_type'] = 1;
        $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
        if (!$prizedata) {    //奖项送完清空金额
        	$savemoney['c_money'] = 0;
        	$result = M('Activity_money')->where($moneywhere)->save($savemoney);
			return Message(3001,'金额已领完');
        }

        //查询未领取总人数 修改总金额
		$sumwhere['c_state'] = 0;
		$sumwhere['c_aid'] = $parr['aid'];
		$doteysum = M('Activity_dotey')->where($sumwhere)->field('sum(c_portion) as psum')->select();

        $db = M();
		$db->startTrans();

        //改变领取状态
        $getmoney = $doteyinfo['c_portion']*rand($prizedata['c_bargainprice']*100,$prizedata['c_value']*100)/100;
        $doteysave['c_value'] = $getmoney;
        $doteysave['c_state'] = 1;
		$result = M('Activity_dotey')->where($doteywhere)->save($doteysave);
		if (!$result) {
			$db->rollback(); //不成功，则回滚
			return Message(3001,'改变领取状态失败');
		}

		//查询未领取总人数 修改总金额
		$remoney = ($doteyinfo['c_portion']/$doteysum[0]['psum'])*$moneydata['c_money'];
		if ($moneydata['c_money'] < $remoney) {
			$remoney = $moneydata['c_money'];
		}

		$moneywhere['c_money'] = array('EGT',$remoney);
		$result = M('Activity_money')->where($moneywhere)->setDec('c_money',$remoney);
		if (!$result) {
			$db->rollback(); //不成功，则回滚
			return Message(3002,'总金额扣除失败');
		}

        //写入活动记录
        $prizelogdata['c_ucode'] = $parr['ucode'];
        $prizelogdata['c_pcode'] = $prizedata['c_pcode'];
        $prizelogdata['c_pid'] = $prizedata['c_id'];
        $prizelogdata['c_value'] = $getmoney;
        $prizelogdata['c_type'] = $prizedata['c_type'];
        $prizelogdata['c_aid'] = $activitydata['c_id'];
        $prizelogdata['c_ucode'] = $parr['ucode'];
        $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_log')->add($prizelogdata);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '领取记录失败！');
        }

        // 写入用户余额
        $rebatemoney = D('Money', 'User');
        $moneyparr['ucode'] = $parr['ucode'];
        $moneyparr['money'] = $getmoney;
        $moneyparr['source'] = 3;
        $moneyparr['key'] = $activitydata['c_activityname'];
        $moneyparr['desc'] = "您在".$activitydata['c_activityname']."活动中领取金额";
        $moneyparr['state'] = 1;  //完成状态
        $moneyparr['type'] = 1;
        $moneyparr['isagent'] = 0;
        $moneyparr['joinaid'] = $activitydata['c_id'];
        $moneyparr['showimg'] = 'Uploads/settlementshow/huo.png';
        $moneyparr['showtext'] = '活动';
        $result = $rebatemoney->OptionMoney($moneyparr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '修改用户余额失败！');
        }

        // 扣除奖项库存
        $jxwhere['c_id'] = $prizedata['c_id'];
        $jxwhere['c_num'] = array('EGT',$doteyinfo['c_portion']);
        $result = M('Activity_prize')->where($jxwhere)->setDec('c_num', $doteyinfo['c_portion']);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '领取数量失败！');
        }

        // 写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您在'.$activitydata['c_activityname'].'活动中获得'.$doteyinfo['c_portion'].'份惊喜，总金额￥'.$getmoney.'，已成功转入余额';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgresult = $Msgcentre->CreateMessege($msgdata);
        if ($msgresult['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '创建消息失败！');
        }

		$db->commit();
		return MessageInfo(0,'领取成功',$prizelogdata);
	}

	/**
	 * 查询宝库信息
	 * @param aid,ucode
	 */
	function GetDoteyInfo($parr)
	{
        $activitywhere['c_id'] = $parr['aid'];
        $activitydata = M('Activity')->where($activitywhere)->find();

		$moneywhere['c_aid'] = $parr['aid'];
		$moneydata = M('Activity_money')->where($moneywhere)->find();
		$moneydata['starttime'] = strtotime($moneydata['c_starttime']) - time();
		$moneydata['endtime'] = strtotime($moneydata['c_endtime']) - time();

		//查询聚宝记录
		$doteywhere['c_ucode'] = $parr['ucode'];
		$doteyinfo = M('Activity_dotey')->where($doteywhere)->find();

		$data['activity'] = $activitydata;
		$data['moneydata'] = $moneydata;
		$data['doteyinfo'] = $doteyinfo;
		return MessageInfo(0,'参与成功',$data);
	}


	/**
	 * 查询寻宝最新20条日志
	 * @param ucode
	 */
	function GetDoteyLog()
	{
        $join = 'inner join t_activity_prize as b on a.c_pid=b.c_id';
		$where[] = array('a.c_aid in (select c_id from t_activity where c_activitytype=15)');
		$where['a.c_type'] = array('neq',3);
		$field = 'a.c_id,a.c_ucode,a.c_addtime as time,b.c_name,a.c_value,b.c_type';
		$data = M('Activity_log as a')->join($join)->where($where)->field($field)->order('a.c_addtime desc')->limit(20)->select();
		if (!$data) {
			return Message(3000,'没有相关数据');
		}
		foreach ($data as $key => $value) {
			$userwhere['c_ucode'] = $value['c_ucode'];
			$data[$key]['name'] = M('Users')->where($userwhere)->getField('c_nickname');
			$data[$key]['time'] = date('Y-m-d',strtotime($value['time']));
			if ($value['c_type'] == 1) {
				$data[$key]['praisecontent'] = '现金红包￥'.$value['c_value'];
			} else {
				$data[$key]['praisecontent'] = $value['c_name'];
			}
		}
		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 查询好友帮助集宝记录
	 * @param ucode,pageindex,pagesize
	 */
	function HelpDoteyLog($parr)
	{
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

		$doteywhere['a.c_pcode'] = $parr['ucode'];
		$join = 'left join t_users as b on a.c_ucode=b.c_ucode';
		$field = 'a.c_addtime,b.c_nickname,b.c_headimg';
		$list = M('Activity_dotey as a')->join($join)->where($doteywhere)->order('a.c_id desc')
		->limit($countPage, $pageSize)->field($field)->select();
		if (!$list) {
			return Message(3000,'没有相关数据');
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
			$list[$key]['c_addtime'] = date('Y/m/d H:i:s',strtotime($value['c_addtime']));
		}

		$count = M('Activity_dotey as a')->join($join)->where($doteywhere)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 按规则查询平台进行的活动
	 * @param rule
	 */
	function GetPlathavingAct($rule,$sign)
	{
        $activitywhere['c_state'] = 1;             //活动开启状态
        $activitywhere['c_activitytype'] = $rule;     //聚宝活动标识
        if ($sign != 1) {
	        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
	        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        }
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(3002, '平台尚未开放此活动！');
        }
        return MessageInfo(0,'查询成功',$activitydata);
	}
}