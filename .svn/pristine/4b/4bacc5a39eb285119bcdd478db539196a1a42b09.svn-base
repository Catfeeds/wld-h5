<?php
namespace Common\Behind;
//活动管理
class ActivityBehind{
	//活动操作
	public function GetAction($aid,$activitytype){
		$action = '<a title="编辑" href="javascript:;" onclick="activity_edit('."'编辑'".','."'Activity/activity_edit?Id=".$aid."'".',780)" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>'.
			'<a title="删除" href="javascript:;" onclick="activity_del(this,'.$aid.')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>';
		switch ($activitytype) {
			case 1:
				$action = '<a title="奖金池" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_money?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.'<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 2:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 3:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 4:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 5:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 6:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Productchip/red_packet" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 7:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Productchip/chip_list" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 8:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Shopactivity/mckit_list" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 9:
				$action = '<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/wish_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 10:
				$action = '<a title="签文库" href="'.WEB_HOST.'/admin.php/Home/Ballot/ballot_list?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.GetHost().'/admin.php/Home/Ballot/ballot_log" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 11:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 12:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 13:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Event/Buylimit" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Order/index?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 14:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Event/snatch?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Order/index?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 15:
				$action = '<a title="奖金池" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_money?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.'<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Dotey/index?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 16:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 17:
				$action = '<a title="奖金池" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_money?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 18:
				$action = '<a title="奖金池" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_money?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.'<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_prize?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.
					'<a title="红包记录详情" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_moneylog?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6b7;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 19:
				$action = '<a title="奖金池" href="'.WEB_HOST.'/admin.php/Home/Activity/activity_money?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.'<a title="固定口令红包列表" href="'.WEB_HOST.'/admin.php/Home/Wordred/fixedword?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.'<a title="随机口令红包列表" href="'.WEB_HOST.'/admin.php/Home/Wordred/randomword?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6b7;</i></a>&nbsp;&nbsp;'.
					'<a title="领取记录详情" href="'.WEB_HOST.'/admin.php/Home/Wordred/wordred_log?aid='.$aid.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			default:
				# code...
				break;
		}

		return $action;
	}

	//活动操作
	public function GetActionv2($id,$activitytype){
		$action = '<a title="编辑" href="javascript:;" onclick="activity_edit('."'编辑'".','."'Activityv2/activity_edit?Id=".$id."'".',780)" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>'.
			'<a title="删除" href="javascript:;" onclick="activity_del(this,'.$id.')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>';
		switch ($activitytype) {
			case 20:
				$action = '<a title="红包库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/red_list?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.$action;
				// '<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activityv2/activity_log?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 21:
				break;
			case 22:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/airbox_prize?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
				'<a title="红包库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/airbox_red?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.
				'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activityv2/activity_log?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 23:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/airbox_prize?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
				'<a title="红包库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/airbox_red?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe63a;</i></a>&nbsp;&nbsp;'.
				'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activityv2/activity_log?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 24:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/roulette_prize?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activityv2/activity_log?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 25:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/roulette_prize?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activityv2/activity_log?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
			case 26:
				break;
			case 27:
				break;
			case 28:
				break;
			case 29:
				$action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/welfaree_prize?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
					'<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activityv2/activity_log?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
				break;
            case 30:
                $action = '<a title="奖品库" href="'.WEB_HOST.'/admin.php/Home/Activityv2/roulette_prize?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bb;</i></a>&nbsp;&nbsp;'.
                    '<a title="记录详情" href="'.WEB_HOST.'/admin.php/Home/Activityv2/activity_log?joinaid='.$id.'" style="text-decoration:none"><i class="Hui-iconfont">&#xe6bf;</i></a>&nbsp;&nbsp;'.$action;
                break;
			default:
				# code...
				break;
		}

		return $action;
	}

	//活动奖品列表
	public function GetTemplate($activitytype){
		$template = '';
		switch ($activitytype) {
			case 16:
				$template = 'find_redlist';
				break;
			
			default:
				# code...
				break;
		}
		return $template;
	}

	//活动记录列表
	public function GetLogTemplate($activitytype){
		$template = '';
		switch ($activitytype) {
			case 16:
				$template = 'findactivity_log';
				break;
			
			default:
				# code...
				break;
		}
		return $template;
	}

	//分配奖项
	public function allot_prize($logid,$pid){
		//判断是否已经分配过奖项
		$logwhere['c_id'] = $logid;
		$loginfo = M('Activity_log')->where($logwhere)->find();
		if(!$loginfo || $loginfo['c_pid']){
			return Message(1001,'已经分配过奖项，无法重新分配！');
		}

		//判断奖品状态是否正常
		$prizewhere['c_id'] = $pid;
		$prizewhere['c_num'] = array('GT', 0);
		$prizewhere['c_state'] = 1;
		$prizeinfo = M('Activity_prize')->where($prizewhere)->find();
		if(!$prizewhere){
			return Message(1002,'奖项不存在或者奖项少于0！');
		}

		$db = M('');
        $db->startTrans(); /* 开启事务 */

		//修改活动记录
		$logsave['c_pid'] = $pid;
		$logsave['c_value'] = $prizeinfo['c_value'];
		$logsave['c_type'] = 1;
		$logsave['c_state'] = 1;
		$result = M('Activity_log')->where($logwhere)->save($logsave);
		if(!$result){
            $db->rollback(); //不成功，则回滚
            return Message(1003, '奖品领取记录失败！');
        }

        // 写入用户余额
        $moneyparr['ucode'] = $loginfo['c_ucode'];
        $moneyparr['money'] = $prizeinfo['c_value'];
        $moneyparr['source'] = 3;
        $moneyparr['key'] = '爱我你就快一点';
        $moneyparr['desc'] = "爱我你就快一点活动中奖";
        $moneyparr['state'] = 1;  //完成状态
        $moneyparr['type'] = 1;
        $moneyparr['isagent'] = 0;
        
        $result =  D('Common', 'Service')->OptionMoney($moneyparr);
        if ($result['code'] !== 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1004, '修改用户余额失败！');
        }

        // 扣除奖池总额
        $w['c_id'] = $pid;
        $result = M('Activity_prize')->where($w)->setDec('c_num', 1);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1005, '扣除奖项数量失败！');
        }

        $db->commit();

        // 写入消息中心
        $msgdata['ucode'] = $loginfo['c_ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;       
        $msgdata['content'] = '恭喜您，您在小蜜《爱我你就快一点》游戏中获得了'.$prizeinfo['c_name'].',小蜜蜂已将奖励￥' . $prizeinfo['c_value'] . '成功转入您的余额账户！';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';

        $msgresult = D('Msgcentre', 'Service')->CreateMessegeInfo($msgdata);
        
        return Message(0, '分配奖项成功');
	}

	//限时抢购、群龙夺宝  商家提交商品审核
	public function pcheck($parr){
		$id = $parr['id'];
		$where['c_id'] = $id;
		$where['c_delete'] = 0;
		$productinfo = M('Product_promote')->where($where)->find();

		if(!$productinfo){
			return Message(1001,'该商品不存在！');
		}

		if($productinfo['c_rule'] == 13){
			$activity = "小蜜-特惠风暴";
			$weburl = GetHost(1) . '/index.php/Home/Promote/prolist?rtype=1';
		}else if($productinfo['c_rule'] == 14){
			$activity = "小蜜-夺宝大作战";
			$weburl = GetHost(1) . '/index.php/Home/Promote/prolist?rtype=2';
		}

		$stateInfo['c_state'] = $parr['state'];
		$stateInfo['c_checktime'] = Date('Y-m-d H:i:s');
		if($parr['state'] ==1){
			$sendmsg = "您在【".$activity."】中提交的活动申请已经通过审核。";
		}else{
			$stateInfo['c_reason'] = $parr['reason'];
			$sendmsg = "您在【".$activity."】中提交的活动申请未通过审核，可能存在以下问题：".$parr['reason'];
		}

		$w['c_id'] = $id;
		$result = M('Product_promote')->where($w)->save($stateInfo);
		if(!$result){
			return Message(1002,'修改商品审核状态失败！');
		}

		if(!empty($productinfo['c_ucode'])){
			// 写入消息中心
	        $msgdata['ucode'] = $productinfo['c_ucode'];
	        $msgdata['type'] = 0;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['tag'] = 2;       
	        $msgdata['content'] = $sendmsg;
	        $msgdata['tagvalue'] = $weburl;
	        $msgdata['weburl'] = $weburl;

        	$msgresult = D('Msgcentre', 'Service')->CreateMessegeInfo($msgdata);
		}
        
        return Message(0, '商品审核完成');
    }
}