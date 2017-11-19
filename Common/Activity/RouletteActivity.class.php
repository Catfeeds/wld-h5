<?php

/**
 * 	大转盘活动管理中心
 *
 */
class RouletteActivity {

	/**
	 * 点击开始抽奖
	 * @param ucode
	 */
	function RouletteRun($parr)
	{
		$ucode = $parr['ucode'];
		if (empty($ucode)) {
			return Message(1009,'请登录后再操作');
		}

		//获取剩余的参与次数
		$lotterywhere['c_ucode'] = $parr['ucode'];
		$lotterywhere['c_rule'] = 2;
		$chance = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');
		if ($chance <= 0) {
			return Message(3001,'您的抽奖机会已经用完！');
		}

		//查询转盘活动
        $result = IGD('Index','Newact')->GetPlatActInfo(25);
        if ($result['code'] != 0) {
			return Message(3000,'活动还没有开始');
		}
		$activitydata = $result['data'];

		// 获取转盘活动配置
		$rouletteconf = IGD('Redis','Redis')->Gethash('roulette')['data'];
		if (!$rouletteconf) {
			return Message(3000,'活动还没有开始');
		}

		// 获取本期转盘总抽奖量
		$actlogwhere['c_joinaid'] = $activitydata['c_id'];
		$count = M('A_actlog')->where($actlogwhere)->count();

		$db = M('');
		$db->startTrans();

		//判断是否中奖
		$parr['count'] = $count;
		$resultprize = $this->ClickWinPrize($parr);
		// 防止用户在同一活动中多次中奖
		if ($rouletteconf['repeat'] == 2) {
			$seacherwhere['c_ucode'] = $ucode;
			$seacherwhere['c_joinaid'] = $activitydata['c_id'];
			$seacherwhere['c_type'] = 4;
			$seacherlog = M('A_actlog')->where($seacherwhere)->find();
			if ($seacherlog) {
				$resultprize['data'] = 6; //6六等奖,5五等奖,4四等奖,3三等奖,2二等奖,1一等奖
			}
		}

		//查询奖项
		$prizewhere['c_joinaid'] = $activitydata['c_id'];
		$prizewhere['c_today_prize'] = 1;
		$prizewhere['c_status'] = 1;
		$prizewhere['c_delete'] = 2;
		$prizewhere['c_num'] = array('GT',0);
		$prizewhere['c_marks'] = $resultprize['data'];
		$prizedata = M('A_actprize')->where($prizewhere)->find();
		if (!$prizedata) {
			$prizewhere['c_marks'] = 6;
			$prizedata = M('A_actprize')->where($prizewhere)->find();
		}
		if ($prizedata['c_type'] == 2) {  //中现金
			//随机金额
			$movalue = rand($prizedata['c_value']*100,$prizedata['c_maxvalue']*100)/100;
			$prizedataresult = $this->Winmoney($activitydata,$ucode,$prizedata,$movalue);
			if ($prizedataresult['code'] != 0) {
				$db->rollback(); //不成功，则回滚
				return $prizedataresult;
			}
			$prizedata['c_value'] = $movalue;
		} else if ($prizedata['c_type'] == 4) { //中实物
	        $prizedataresult = $this->WinMatter($activitydata,$ucode,$prizedata);
			if ($prizedataresult['code'] != 0) {
				$db->rollback(); //不成功，则回滚
				return $prizedataresult;
			}
			$prizedata = $prizedataresult['data'];
		}

		if (!$prizedata) {
			$db->rollback(); //不成功，则回滚
			return Message(3004,'活动奖品已派完');
		}

        //写入领取记录
        $parr['orderid'] = $prizedata['orderid'];
        $parr['awid'] = $prizedata['c_id'];
		$parr['joinaid'] = $prizedata['c_joinaid'];
		$parr['pid'] = $prizedata['c_pid'];
		$parr['acode'] = $prizedata['c_acode'];
		$parr['name'] = $prizedata['c_name'];
		$parr['img'] = $prizedata['c_img'];
		$parr['value'] = $prizedata['c_value'];
		$parr['marks'] = $prizedata['c_remark'];
		$parr['type'] = $prizedata['c_type'];
		$parr['state'] = 1;
		$result = IGD('Index','Newact')->WriteReciveLog($parr);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}

        //扣除奖品数量
        $decwhere['c_id'] = $prizedata['c_id'];
        $result =  M('A_actprize')->where($decwhere)->setDec('c_num',1);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1034, '扣除奖品数量失败');
        }

		//次数减一
		$result = M('Activity_lotterynum')->where($lotterywhere)->setDec('c_num',1);
		if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1035, '扣除用户抽奖次数失败');
        }		

		$prizearr['Id'] = $prizedata['c_id'];
		if ($prizedata['c_type'] == 2) {
			$prizearr['name'] = '获得'.$prizedata['c_name'].'￥'.$movalue.'已存入余额';
		} else if ($prizedata['c_type'] == 4) {
			$prizearr['name'] = '获得'.$prizedata['c_name'].'已存入订单等待发货';
		} else {
			$prizearr['name'] = $prizedata['c_name'];
		}
		$prizearr['num'] = $chance - 1;
		//查询角度
		$sign = 'prize'.$prizedata['c_marks'];
		$anglearr = explode(',',$rouletteconf[$sign]);
		$prizearr['angle'] = rand($anglearr[0]+10,$anglearr[1]-10);
		// 写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        if ($prizedata['c_type'] == 2) {
       	 	$msgdata['title'] = '系统消息';
        	$msgdata['type'] = 0;
        	$msgdata['tag'] = 2;
            $msgdata['content'] = '您在'.$activitydata['c_activityname'].'活动中获得红包金额为￥' . $movalue . '，领取成功已转入余额';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgresult = $Msgcentre->CreateMessegeInfo($msgdata);
        } else if ($prizedata['c_type'] == 4) {
        	$msgdata['type'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $prizedata['orderid'];
            $msgdata['content'] = '您在'.$activitydata['c_activityname'].'活动中获得一个奖品：' . $prizedata['c_name'] . '，已转入订单';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $prizedata['orderid'];
       		$msgresult = $Msgcentre->CreateMessegeInfo($msgdata);
        }
		$prizearr['c_type'] = $prizedata['c_type'];
        $prizearr['c_name'] = $prizedata['c_name'];
		$prizearr['c_value'] = $prizedata['c_value'];
		$prizearr['c_img'] = GetHost().'/'.$prizedata['c_img'];

		$db->commit();
		return MessageInfo(0,'获取成功',$prizearr);
	}

	//中现金操作
	function Winmoney($activitydata,$ucode,$prizedata,$movalue)
	{
		// 写入用户余额
        $rebatemoney = IGD('Money', 'User');
        $moneyparr['ucode'] = $ucode;
        $moneyparr['money'] = $movalue;
        $moneyparr['source'] = 3;
        $moneyparr['key'] = $activitydata['c_activityname'];
        $moneyparr['desc'] = "您在".$activitydata['c_activityname']."中活动中奖金额";
        $moneyparr['state'] = 1;  //完成状态
        $moneyparr['type'] = 1;
        $moneyparr['isagent'] = 0;
        $moneyparr['joinaid'] = $activitydata['c_id'];
        $moneyparr['showimg'] = 'Uploads/settlementshow/huo.png';
        $moneyparr['showtext'] = '活动';
        $result = $rebatemoney->OptionMoney($moneyparr);
        if ($result['code'] != 0) {
            return Message(1033, '修改用户余额失败！');
        }
        return MessageInfo(0, '查询成功',$prizedata);
	}

	//中实物奖操作
	function WinMatter($activitydata,$ucode,$prizedata)
	{
		//进行转化订单操作
		$result = IGD('Actoder','Activity')->CreataOrderInfo($ucode,$activitydata,$prizedata);
		if ($result['code'] != 0) {
			return $result;
		}
		$prizedata['orderid'] = $result['data'];
        return MessageInfo(0, '查询成功',$prizedata);
	}
	/**
     *  根据配置的点击次数中奖
     *  @param count
     *  @return type:6六等奖,5五等奖,4四等奖,3三等奖,2二等奖,1一等奖
     */
    function ClickWinPrize($parr) {
        $count = $parr['count'];
        $ucode = $parr['ucode'];
        $rouletteconf = IGD('Redis','Redis')->Gethash('roulette')['data'];
        $clicknum1arr = explode('|', $rouletteconf['maxclick']);
        $clicknum2arr = explode('|', $rouletteconf['minclick']);
        $clicknum3arr = explode('|', $rouletteconf['redmaxclick']);
        $clicknum4arr = explode('|', $rouletteconf['redminclick']);
        $clicknum5arr = explode('|', $rouletteconf['midclick']);
        $type = 6;

        $nowtime = time();
        $toptime = date('Y-m-d');
        $timearr = explode('|', $rouletteconf['limittime']);
        foreach ($timearr as $key => $value) {
            $limittime = explode('-', $value);
            $begain = strtotime($toptime . ' ' . $limittime[0]);
            $stops = strtotime($toptime . ' ' . $limittime[1]);
            //特输时间段获奖处理
            if ($nowtime >= $begain && $nowtime <= $stops) {
                $clicknum1 = isset($clicknum1arr[1])?$clicknum1arr[1]:$clicknum1arr[0];
		        $clicknum2 = isset($clicknum2arr[1])?$clicknum2arr[1]:$clicknum2arr[0];
		        $clicknum3 = isset($clicknum3arr[1])?$clicknum3arr[1]:$clicknum3arr[0];
		        $clicknum4 = isset($clicknum4arr[1])?$clicknum4arr[1]:$clicknum4arr[0];
		        $clicknum5 = isset($clicknum5arr[1])?$clicknum5arr[1]:$clicknum5arr[0];
            } else {
            	$clicknum1 = $clicknum1arr[0];
		        $clicknum2 = $clicknum2arr[0];
		        $clicknum3 = $clicknum3arr[0];
		        $clicknum4 = $clicknum4arr[0];
		        $clicknum5 = $clicknum5arr[0];
            }
        }

        if (($count % $clicknum1) == 0) {
            $type = 1;
        }
        if (($count % $clicknum2) == 0) {
            $type = 2;
        }
        if (($count % $clicknum3) == 0) {
            $type = 3;
        }
        if (($count % $clicknum4) == 0) {
            $type = 4;
        }
        if (($count % $clicknum5) == 0) {
            $type = 5;
        }

      	//指定用户中奖实物
        if (!empty($rouletteconf['luckuser'])) {
        	$luckuser = explode('|', $rouletteconf['luckuser']);
        	$keys = array_search($ucode, $luckuser);
        	if ($key) {
    			foreach ($luckuser as $key => $value) {
    				if ($key != $keys) {
    					$luckarr[] = $value;
    				}
    			}
    			$rouletteconf['luckuser'] = implode('|',$luckarr);
        		$this->roulette_setting($rouletteconf);
        		$type = rand(1,5);
        	}
        }
        return MessageInfo(0,'参与成功',$type);
    }

	/**
	 *  获取大转盘活动及奖品列表
	 *  @param ucode
	 */
	function GetPrizeList($parr)
	{
		//赋予用户对应抽奖次数
		$lotterywhere['c_ucode'] = $parr['ucode'];
		$lotterywhere['c_rule'] = 2;
		$lotteryUser = M('Activity_lotterynum')->where($lotterywhere)->order('c_addtime desc')->find();
		$rouletteconf = IGD('Redis','Redis')->Gethash('roulette')['data'];
		if (!empty($parr['ucode']) && !$lotteryUser) {
			$lotterydata['c_ucode'] = $parr['ucode'];
			$lotterydata['c_num'] = 0;
			$lotterydata['c_rule'] = 2;
			$lotterydata['c_addtime'] = date('Y-m-d H:i:s');
			$result = M('Activity_lotterynum')->add($lotterydata);
			if (!$result) {
				return Message(3000,'用户次数获取失败');
			}
		}

		//用户抽奖次数
		$num = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');

        $result = IGD('Index','Newact')->GetPlatActInfo(25,1);
        if ($result['code'] != 0) {
			return Message(3000,'没有相关数据');
		}
		$activitydata = $result['data'];

		$activitydata['c_pimg'] = GetHost().'/'.$activitydata['c_pimg'];
		$activitydata['c_img'] = GetHost().'/'.$activitydata['c_img'];
		$activitydata['num'] = $num;

		$where['c_joinaid'] = $activitydata['c_id'];
		$where['c_today_prize'] = 1;
		$where['c_status'] = 1;
		$where['c_type'] = array('eq',4);
		$field = 'c_id,c_name,c_totalnum,c_img,c_value';
		$data = M('A_actprize')->where($where)->field($field)->order('c_marks asc')->limit(6)->select();
		if (!$data) {
			return Message(3000,'没有相关数据');
		}

		foreach ($data as $key => $value) {
			$data[$key]['c_img'] = GetHost().'/'.$value['c_img'];
		}

		$list['theme'] = $activitydata;
		$list['prizelist'] = $data;
		return MessageInfo(0,'查询成功',$list);
	}

	/**
	 *  获取单个奖品详情
	 *  @param pid
	 */
	function GetPrizeOne($parr)
	{
		$where['c_id'] = $parr['pid'];
		$data = M('A_actprize')->where($where)->find();
		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 *  获取最新的20条大转盘获奖数据
	 *  @param
	 */
	function GetAwardList($parr)
	{
		$result = IGD('Index','Newact')->GetPlatActInfo(25,1);
        if ($result['code'] != 0) {
            return Message(3000,'没有相关数据');
        }
        $activitydata = $result['data'];

        $join = 'inner join t_a_actprize as b on a.c_awid=b.c_id';
        $where['a.c_joinaid'] = $activitydata['c_id'];
		$where['a.c_type'] = array('neq',1);
		$field = 'a.c_id,a.c_ucode,a.c_addtime as time,b.c_name,a.c_value,b.c_type';
		$data = M('A_actlog as a')->join($join)->where($where)->field($field)->order('a.c_addtime desc')->limit(20)->select();
		if (!$data) {
			return Message(3000,'没有相关数据');
		}
		foreach ($data as $key => $value) {
			$userwhere['c_ucode'] = $value['c_ucode'];
			$data[$key]['name'] = M('Users')->where($userwhere)->getField('c_nickname');
			$data[$key]['time'] = date('Y-m-d',strtotime($value['time']));
			if ($value['c_type'] == 2) {
				$data[$key]['praisecontent'] = $value['c_name'].'￥'.$value['c_value'];
			} else {
				$data[$key]['praisecontent'] = $value['c_name'];
			}

		}
		return MessageInfo(0,'查询成功',$data);
	}

	// 获取中奖总人数
	function GetAwardCount()
	{
		$result = IGD('Index','Newact')->GetPlatActInfo(24,1);
        if ($result['code'] != 0) {
            return Message(3000,'没有相关数据');
        }
        $activitydata = $result['data'];

        $join = 'inner join t_a_actprize as b on a.c_awid=b.c_id';
        $where['a.c_joinaid'] = $activitydata['c_id'];
		$where['a.c_type'] = array('neq',1);
		$count = M('A_actlog as a')->join($join)->where($where)->count();
		if (!$count) {
			$count = 0;
		}
		return $count;
	}

	//大转盘活动配置信息设置
    public function roulette_setting($parr){
        $a = array(
            'statu' => $parr['statu'],
            'num' => $parr['num'],
            'prize1' => $parr['prize1'],
            'prize2' => $parr['prize2'],
            'prize3' => $parr['prize3'],
            'prize4' => $parr['prize4'],
            'prize5' => $parr['prize5'],
            'prize6' => $parr['prize6'],
            'repeat' => $parr['repeat'],
            'luckuser' => $parr['luckuser'],
            'minclick' => $parr['minclick'],
            'maxclick' => $parr['maxclick'],
            'redminclick' => $parr['redminclick'],
            'redmaxclick' => $parr['redmaxclick'],
            'limittime' => $parr['limittime'],
            'midclick' => $parr['midclick']
            );
        $setfile = './data/config/roulette.php';
        $result = $this->update_config($a,$setfile);
        $result = IGD('Redis','Redis')->Sethash('roulette',$a);
    }

    //修改配置文件方法
    protected function update_config($new_config, $config_file)
    {
        if (is_writable($config_file)) {
            $config = require $config_file;
            $config = array_merge($config, $new_config);
            file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);
            @unlink(RUNTIME_FILE);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 将大转盘次数设为一
     * @param ucode,rule,num
     */
    function EditRouletteNum($parr)
    {
    	$lotterywhere['c_ucode'] = $parr['ucode'];
        $lotterywhere['c_rule'] = isset($parr['rule'])?$parr['rule']:2;
        $lotteryUser = M('Activity_lotterynum')->where($lotterywhere)->order('c_addtime desc')->find();
        if ($lotteryUser['c_num'] > 0) {
	        $lotterydata['c_num'] = $parr['num'];
	        $result = M('Activity_lotterynum')->where($lotterywhere)->save($lotterydata);
	        if (!$result) {
	            return Message(3000,'修改失败');
	        }
        }
        return Message(0,'修改成功');
    }

    /**
     * 写入大转盘转发记录
     * @param ucode,url
     */
    function AddShareLog($parr)
    {
    	if (empty($parr['ucode']))  {
    		return Message(1009,'请登录再转发操作');
    	}

    	//查询大转盘活动
    	$result = IGD('Index','Newact')->GetPlatActInfo(25,1);
        if ($result['code'] != 0) {
            return Message(3000,'没有相关数据');
        }
        $activitydata = $result['data'];

        //查询转发记录
        $sw['c_ucode'] = $parr['ucode'];
        $sw['c_datetime'] = date('Y-m-d');
        $sw['c_aid'] = $activitydata['c_id'];
        $shareinfo = M('Share_log')->where($sw)->find();

    	//写入转发记录
        $userwhere['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($userwhere)->find();
        $add['c_ucode'] = $userinfo['c_ucode'];
        $add['c_nickname'] = $userinfo['c_nickname'];
        $add['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
        $add['c_updatetime'] = gdtime();
      	
      	if (!$shareinfo) {   
      		$add['c_datetime'] = date('Y-m-d');   	 
	        $add['c_vcode'] = CreateUcode('fx');
	        $add['c_sign'] = 1;
	        $add['c_aid'] = $activitydata['c_id'];
	        $add['c_url'] = $parr['url'];
	        $add['c_addtime'] = gdtime();
	        $result = M('Share_log')->add($add);
	        $vcode = $add['c_vcode'];
        } else {
        	$result = M('Share_log')->where($sw)->save($add);
        	$vcode = $shareinfo['c_vcode'];
        }
        
        return MessageInfo(0,'转发成功',$vcode);
    }

    /**
     * 浏览链接分享人获得抽奖次数
     * @param openid,nickname,headimg(ucode),vcode,acode
     */
    function ViewShareLog($parr)
    {
    	$openid = $parr['openid'];
    	$ucode = $parr['ucode']; 

    	// 获取转盘活动配置
		$rouletteconf = IGD('Redis','Redis')->Gethash('roulette')['data'];
		if (!$rouletteconf) {
			return Message(3000,'活动还没有开始');
		}

        $viewnum = 10;                     //分享浏览人数
        $maxnum = 101;                     //最大浏览人数
        if (empty($parr['openid'])) {
            return Message(1003,'请从微信访问链接');
        }

        //查询大转盘活动
    	$result = IGD('Index','Newact')->GetPlatActInfo(25,1);
        if ($result['code'] != 0) {
            return Message(3000,'没有相关数据');
        }
        $activitydata = $result['data'];

        //查询转发记录
        $sw['c_vcode'] = $parr['vcode'];
        $sw['c_aid'] = $activitydata['c_id'];
        $shareinfo = M('Share_log')->where($sw)->find();
        if (!$shareinfo) {
        	return Message(3001,'转发记录不存在');
        }


        //查询当天浏览总人数
        $viewwhere['c_vcode'] = $parr['vcode'];
        $viewwhere[] = array("c_addtime>='".date('Y-m-d 00:00:00')."' and c_addtime<='".date('Y-m-d 23:59:59')."'");
        $countview = M('Share_viewlog')->where($viewwhere)->count();

        //查询当天浏览记录
        $viewwhere[] = array("c_ucode='$ucode' or c_openid='$openid'");
        $shareview = M('Share_viewlog')->where($viewwhere)->find();
        if (!$shareview) {

        	//写入浏览记录
        	$add['c_vcode'] = $parr['vcode'];
        	$add['c_updatetime'] = gdtime();
        	if ($ucode) {
        		$userwhere['c_ucode'] = $parr['ucode'];
		        $userinfo = M('Users')->where($userwhere)->find();
		        $add['c_ucode'] = $userinfo['c_ucode'];
		        $add['c_nickname'] = $userinfo['c_nickname'];
		        $add['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
        	} else {
	            $add['c_openid'] = $parr['openid'];
	            $add['c_nickname'] = $parr['nickname'];
	            $add['c_headimg'] = $parr['headimg'];
        	}
        	

            $db = M('');
            $db->startTrans();
           
           	$add['c_addtime'] = gdtime();
            $result = M('Share_viewlog')->add($add);
            if (!$result) {
                $db->rollback();
                return Message(3002,'记录写入失败');
            }

            if ($activitydata && (($countview%$viewnum) == 0) && $countview < $maxnum && $countview > 0) {

            	//增加大转盘抽奖机会
            	$parr1['ucode'] = $shareinfo['c_ucode'];
            	$parr1['rule'] = 2;
            	$result = IGD('Advert','Newact')->AddActNum($parr1,1);
            	if ($result['code'] != 0) {
            		$db->rollback();
                	return $result;
            	}
                 
                // 写入消息中心
                $Msgcentre = IGD('Msgcentre', 'Message');
                $msgdata['ucode'] = $shareinfo['c_ucode'];
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['type'] = 0;
                $msgdata['tag'] = 2;
                $msgdata['content'] = '分享链接，【'.$parr['nickname'].'】...等10位好友浏览，获得一个大转盘抽奖机会';
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Activity/Index/roulette';
                $msgdata['weburl'] = GetHost(1) . '/index.php/Activity/Index/roulette';
                $msgresult = $Msgcentre->CreateMessege($msgdata);
            }

            $db->commit();
        }

        return Message(0,'记录成功');
    }
}