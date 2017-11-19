<?php

/**
 *  答题活动 找你妹活动 接口
 */
class QuestionActivity {

	/**
	 *  根据活动分配用户题目
	 *  @param ucode,aid
	 */
    public function GetQuestion($parr) {
    	$aid = $parr['aid'];
    	$ucode = $parr['ucode'];

    	//查询用户今天答题个数
    	$questionwhere['c_ucode'] = $parr['ucode'];
    	$questionwhere['c_aid'] = $parr['aid'];
    	$questionwhere['c_addtime'] = array('ELT',date('Y-m-d 00:00:00', strtotime('+1 day',time())));
    	$questionwhere['c_addtime'] = array('EGT',date('Y-m-d 00:00:00', time()));
    	$questionlog = M('questions_log')->where($questionwhere)->order('c_addtime asc')->select();
    	$aconf = C('Activity');    //活动配入数据
        // $aconf = IGD('Redis','Redis')->Gethash('activity')['data'];
    	$questionnum = $aconf['questionnum'];  //配置的答题个数

        $qnum = $parr['qnum'];
        if (!empty($qnum)) {
            $where1['c_id'] = $questionlog[$qnum-1]['c_qid'];
            $data = M('Questions')->where($where1)->find();
            if ($data) {
                $field = 'c_id,c_aid,c_name,c_qid,c_addtime';
                $answerwhere['c_qid'] = $data['c_id'];
                $data['answer'] = M('Questions')->where($answerwhere)->field($field)->select();
                $data['num'] = $qnum;
                return MessageInfo(1002,'请从发现中继续发现新题目',$data);
            }
        }

    	$responsenum = count($questionlog);
    	if ($responsenum >= $questionnum) {
            $where['c_id'] = $questionlog[$responsenum-1]['c_qid'];
            $data = M('Questions')->where($where)->find();
            $field = 'c_id,c_aid,c_name,c_qid,c_addtime';
            $answerwhere['c_qid'] = $data['c_id'];
            $data['answer'] = M('Questions')->where($answerwhere)->field($field)->select();
            $data['num'] = $responsenum;
    		return MessageInfo(1002,'你今天已经答题完毕，明天再来吧',$data);
    	}

    	//查询用户今天是否答题完毕
    	// $activitywhere['c_ucode'] = $ucode;
    	// $activitywhere['c_aid'] = $aid;
    	// $activitywhere['c_addtime'] = array('ELT',date('Y-m-d 00:00:00', strtotime('+1 day',time())));
    	// $activitywhere['c_addtime'] = array('EGT',date('Y-m-d 00:00:00', time()));
    	// $result = M('Activity_log')->where($activitywhere)->find();
    	// if ($result) {
    	// 	return Message(1002,'你今天已经答题完毕，明天再来吧');
    	// }

    	//获取随机不同的题目
    	$answersql = 'select c_qid from t_questions_log where c_aid='.$aid.' and c_ucode="'.$ucode.'"';
    	$where[] = array('c_id not in ('.$answersql.')');
    	$where['c_qid'] = 0;
        $where['c_aid'] = $aid;
    	$data = M('Questions')->where($where)->order('rand()')->find();
    	$answerwhere['c_qid'] = $data['c_id'];
    	$field = 'c_id,c_aid,c_name,c_qid,c_addtime';
    	$data['answer'] = M('Questions')->where($answerwhere)->field($field)->select();
    	$data['num'] = $responsenum + 1;
    	if (!$data) {
    		return MessageInfo(1000,'查询失败');
    	}
    	return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 用户提交答案
     * @param ucode,aid,qid,answerid
     */
    public function SubmitAnswer($parr)
    {
        // 查询活动是否开始
        $activitywhere['c_state'] = 1;            //活动开启状态
        $activitywhere['c_id'] = $parr['aid'];     //发现活动类型标识
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));

        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(1000, '活动尚未开始');
        }

        //查询用户是否已打完该题
        $rewhere['c_ucode'] = $parr['ucode'];
        $rewhere['c_qid'] = $parr['qid'];
        $result = M('Questions_log')->where($rewhere)->find();
        if ($result) {
            return MessageInfo(1001,'该题已经答完，不能修改答案');
        }

    	//查询用户今天答题个数
    	$questionwhere['c_ucode'] = $parr['ucode'];
    	$questionwhere['c_aid'] = $parr['aid'];
    	$questionwhere['c_addtime'] = array('ELT',date('Y-m-d 00:00:00', strtotime('+1 day',time())));
    	$questionwhere['c_addtime'] = array('EGT',date('Y-m-d 00:00:00', time()));
    	$questionlog = M('Questions_log')->where($questionwhere)->select();
    	$aconf = C('Activity');    //活动配入数据
        // $aconf = IGD('Redis','Redis')->Gethash('activity')['data'];
    	$questionnum = $aconf['questionnum'];  //配置的答题个数

    	$responsenum = count($questionlog);
    	if ($responsenum >= $questionnum) {
    		return Message(1002,'你今天已经答题完毕，明天再来吧');
    	}

    	$db = M('');
    	$db->startTrans();

        //查询题目的正确答案
        $where['c_qid'] = $parr['qid'];
        $where['c_sign'] = 1;
        $trueanswer = M('Questions')->where($where)->find();
        if ($trueanswer['c_id'] == $parr['answerid']) {
            $count = 1;
            // 答对题目赠送大转盘抽奖次数一次
            $result = $this->AddRouletteNum($parr,1);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        } else {
            $count = 0;
        }


    	//完成题目，分配红包
    	if (($responsenum+1) >= $questionnum) {
    		// 统计答对个数
    		foreach ($questionlog as $key => $value) {
    			$truewhere['c_id'] = $value['c_answerid'];
    			$turesign = M('Questions')->where($truewhere)->getField('c_sign');
    			if ($turesign == 1) {
    				$count++;
    			}
    		}

    		//领取红包
    		// $parr['count'] = $count;
    		// $result = $this->ReceiveQuestionMoney($parr);
    		// if ($result['code'] != 0) {
    		// 	$db->rollback();
    		// 	return $result;
    		// }
    		// $prizedata = $result['data'];
    	}

    	//写入答题记录
    	$datalog['c_aid'] = $parr['aid'];
    	$datalog['c_ucode'] = $parr['ucode'];
    	$datalog['c_qid'] = $parr['qid'];
    	$datalog['c_answerid'] = $parr['answerid'];
    	$datalog['c_addtime'] = date('Y-m-d H:i:s');
    	$result = M('Questions_log')->add($datalog);
    	if (!$result) {
    		$db->rollback();
    		return Message(1003,'答题记录失败');
    	}

    	$data['prizedata'] = $prizedata;
    	$data['answer'] = $trueanswer;
    	$db->commit();
    	return MessageInfo(0,'答题成功',$data);
    }

    /**
     * 赠送活动抽奖次数
     * @param ucode,num,rule(2大转盘,4老虎机)
     */
    public function AddRouletteNum($parr,$num)
    {
        $lotterywhere['c_ucode'] = $parr['ucode'];
        $lotterywhere['c_rule'] = isset($parr['rule'])?$parr['rule']:2;
        $lotteryUser = M('Activity_lotterynum')->where($lotterywhere)->order('c_addtime desc')->find();
        $rouletteconf = C('Roulette');
        if (!empty($parr['ucode'])) {
            if (!$lotteryUser) {
                $lotterydata['c_ucode'] = $parr['ucode'];
                $lotterydata['c_num'] = $num;
                $lotterydata['c_rule'] = isset($parr['rule'])?$parr['rule']:2;
                $lotterydata['c_addtime'] = date('Y-m-d H:i:s');
                $result = M('Activity_lotterynum')->add($lotterydata);
                if (!$result) {
                    return Message(3000,'用户次数新增失败');
                }
            } else {
                $result = M('Activity_lotterynum')->where($lotterywhere)->setInc('c_num',$num);
                if (!$result) {
                    return Message(3000,'用户次数增加失败');
                }

            }
        }
        return Message(0,'赠送成功');
    }

    /**
     * 领取答题红包
     * @param count
     */
    public function ReceiveQuestionMoney($parr)
    {
    	//查询奖品表
        $prizewhere['c_state'] = 1;
        $prizewhere['c_num'] = array('GT', 0);
        $prizewhere['c_aid'] = $parr['aid'];
        $prizewhere['c_type'] = 1;
        $prizewhere['c_bargainnum'] = $parr['count'];
        $prizedata = M('Activity_prize')->where($prizewhere)->find();
        if (!$prizedata) {
			return Message(1004,'今天的您的答题红包已被领光，明天再来吧！');
        }

        $prizelogdata['c_ucode'] = $parr['ucode'];
        $prizelogdata['c_pid'] = $prizedata['c_id'];
        $prizelogdata['c_value'] = $prizedata['c_value'];
        $prizelogdata['c_type'] = $prizedata['c_type'];
        $prizelogdata['c_aid'] = $prizedata['c_aid'];
        $result = IGD('Activity','Activity')->WriteActlog($prizelogdata);
        if ($result['code'] != 0) {
            return Message(1033, '奖品领取记录失败！');
        }

        // 写入用户余额
        $rebatemoney = D('Money', 'User');
        $moneyparr['ucode'] = $parr['ucode'];
        $moneyparr['money'] = $prizedata['c_value'];
        $moneyparr['source'] = 3;
        $moneyparr['key'] = M('Activity')->where('c_id='.$parr['aid'])->getField('c_activityname');
        $moneyparr['desc'] = "您参与答题活动领取".$prizedata['c_name'];
        $moneyparr['state'] = 1;  //完成状态
        $moneyparr['type'] = 1;
        $moneyparr['isagent'] = 0;
        $moneyparr['joinaid'] = $parr['aid'];
        $moneyparr['showimg'] = 'Uploads/settlementshow/huo.png';
        $moneyparr['showtext'] = '活动';
        $result = $rebatemoney->OptionMoney($moneyparr);
        if ($result['code'] !== 0) {
            return Message(1033, '修改用户余额失败！');
        }

        // 扣除奖项库存
        $jxwhere['c_id'] = $prizedata['c_id'];
        $result = M('Activity_prize')->where($jxwhere)->setDec('c_num', 1);
        if (!$result) {
            return Message(1033, '扣除奖项库存失败！');
        }

        // 写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您参与答题活动领取'.$prizedata['c_name'].',金额￥' . $prizedata['c_value'] . '已自动转入余额';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgresult = $Msgcentre->CreateMessege($msgdata);
        if ($msgresult['code'] != 0) {
            return Message(1033, '创建消息失败！');
        }
        return MessageInfo(0,'领取成功',$prizedata);
    }

    /**
     *  获取最新的20条获奖数据
     *  @param
     */
    function GetAwardList($parr)
    {
        $join = 'inner join t_activity_prize as b on a.c_pid=b.c_id';
        $where[] = array('a.c_aid in (select c_id from t_activity where c_activitytype=5)');
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
                $data[$key]['praisecontent'] = $value['c_name'];
            }

        }
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     *  找你妹 添加活动记录
     *  @param ucode,score
     */

    public function find_addlog($parr){
        $ucode = $parr['ucode'];
        $score = $parr['score'];

        $db = M('');
        $db->startTrans();

        //添加到游戏分数记录表
        $score_data['c_ucode'] = $ucode;
        $score_data['c_score'] = $score;
        $score_data['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Activity_score')->add($score_data);
        if(!$result){
            $db->rollback();
            return Message(1001,'添加到游戏分数记录表失败');
        }

        //最佳成绩记录到活动记录表
        $awhere['c_activitytype'] = 16;
        $aid = M('Activity')->where($awhere)->getField('c_id');

        $logwhere['c_ucode'] = $ucode;
        $logwhere['c_aid'] = $aid;
        $loginfo = M('Activity_log')->where($logwhere)->find();
        if(!$loginfo){
            $score_data['c_aid'] = $aid;
            $result = M('Activity_log')->add($score_data);
            if(!$result){
                $db->rollback();
                return Message(1002,'最佳成绩记录到活动记录表失败');
            }
        }else{
            if($score < $loginfo['c_score']){
                $chang_score['c_score'] = $score;
                $chang_score['c_addtime'] = date('Y-m-d H:i:s', time());
                $result = M('Activity_log')->where($logwhere)->save($chang_score);
                if(!$result){
                    $db->rollback();
                    return Message(1003,'刷新成绩失败');
                }
            }
        }

        $db->commit();
        return MessageInfo(0,'添加成功',$score);
    }

     /**
     *  找你妹 排行榜
     *  @param ucode
     */
    public function find_ranking($parr){
        $ucode = $parr['ucode'];

        $awhere['c_activitytype'] = 16;
        $aid = M('Activity')->where($awhere)->getField('c_id');

        $w['a.c_aid'] = $aid;
        $field = "u.c_nickname,a.c_score";
        $join = "left join t_users as u on u.c_ucode=a.c_ucode";
        $data['ranking'] = M('Activity_log as a')->field($field)->join($join)->where($w)->order('c_score asc')->select();
        if(!$data){
            return Message(1001,'查询排行榜失败！');
        }

        $w1['a.c_aid'] = $aid;
        $w1['a.c_ucode'] = $ucode;
        $field = "u.c_nickname,a.c_score";
        $join = "left join t_users as u on u.c_ucode=a.c_ucode";
        $data['my_score'] = M('Activity_log as a')->field($field)->join($join)->where($w1)->order('c_score asc')->find();

        $db = M('');
        $sql = "select count(*) as rank from t_activity_log a  where a.c_aid=".$aid." and  a.c_score <= (select b.c_score from t_activity_log b  where b.c_aid=".$aid." and b.c_ucode = '".$ucode."')";

        $rank = $db->query($sql);
        $data['my_score']['rank'] = $rank[0]['rank'];

        return MessageInfo(0,'查询成功！',$data);
    }

     /**
     *  找你妹 奖项排行
     *  @param
     */
    public function find_pranking(){
        $awhere['c_activitytype'] = 16;
        $aid = M('Activity')->where($awhere)->getField('c_id');

        $w['c_aid'] = $aid;
        $w['c_state'] = 1;
        $w['c_type'] = 1;
        $field = "c_id,c_imgpath";
        $pranking = M('Activity_prize')->field($field)->where($w)->order('c_value desc')->select();
        foreach ($pranking as $key => $value) {
            $pranking[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
        }
        return MessageInfo(0,'查询成功！',$pranking);
    }




}
