<?php

/**
 * 抽签活动及老虎机活动接口
 */
class BallotActivity {

    /**
     * 点击获取签文
     * @param openid,nickname,headimg(ucode),aid
     */
    function ClickGetBallot($parr)
    {
        //查询签文活动
        $activitywhere['c_id'] = $parr['aid'];
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitytype'] = 10;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(3000,'活动未开始或已结束');
        }

        //查询今天是否已参与
        $ballotlog = $this->BallotNowLog($parr);
        if ($ballotlog['code'] == 0) {
            $ballotwhere['c_id'] = $ballotlog['data']['c_bid'];
        }
        $ballotwhere['c_aid'] = $parr['aid'];
        $ballotdata = M('Ballot')->where($ballotwhere)->order('rand()')->find();
        $ballotdata['c_desc'] = explode('|',$ballotdata['c_desc']);
        if (!$ballotdata) {
            return Message(3001,'没有相应签文');
        }

        if ($ballotlog['code'] == 0) {
            $ballotdata['bid'] = $ballotlog['data']['c_id'];
            return MessageInfo(0,'今日已抽签',$ballotdata);
        }

        $db = M('');
        $db->startTrans();

        //添加老虎机抽奖次数
        if (!empty($parr['ucode'])) {
            $parr['rule'] = 4;
            $result = IGD('Question','Activity')->AddRouletteNum($parr,1);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }

            $userwhere['c_ucode'] = $parr['ucode'];
            $userinfo = M('Users')->where($userwhere)->find();
            $add['c_ucode'] = $userinfo['c_ucode'];
            $add['c_nickname'] = $userinfo['c_nickname'];
            $add['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
        } else {
            $result = $this->AddSlotNum($parr,1);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
            $add['c_openid'] = $parr['openid'];
            $add['c_nickname'] = $parr['nickname'];
            $add['c_headimg'] = $parr['headimg'];
        }

        //存入签文记录
        $add['c_aid'] = $ballotdata['c_aid'];
        $add['c_bid'] = $ballotdata['c_id'];
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Ballot_log')->add($add);
        if (!$result) {
            $db->rollback();
            return Message(3002,'签文记录写入失败');
        }

        $ballotdata['bid'] = $result;
        $db->commit();
        return MessageInfo(0,'抽签成功',$ballotdata);
    }

    //查询单个签文
    function GetBallotOne($bid)
    {
        $ballotwhere['b.c_id'] = $bid;
        $join = 'left join t_ballot_log as b on b.c_bid=a.c_id';
        $field = 'a.*,b.c_ucode,b.c_openid,b.c_nickname,b.c_headimg';
        $ballotdata = M('Ballot as a')->join($join)->where($ballotwhere)->field($field)->find();
        if (!$ballotdata) {
            return Message(3000,'没有相应签文');
        }
        $ballotdata['c_bimg'] = GetHost().'/'.$ballotdata['c_bimg'];
        $ballotdata['c_desc'] = explode('|',$ballotdata['c_desc']);
        return MessageInfo(0,'查询成功',$ballotdata);
    }

    //查询今天参与记录
    function BallotNowLog($parr)
    {
        $ballotlogwhere['c_aid'] = $parr['aid'];
        $ballotlogwhere[] = array("c_ucode='".$parr['ucode']."' or c_openid='".$parr['openid']."'");
        $ballotlogwhere[] = array("c_addtime>='".date('Y-m-d 00:00:00')."' and c_addtime<='".date('Y-m-d 23:59:59')."'");
        $ballotlog = M('Ballot_log')->where($ballotlogwhere)->order('c_id desc')->find();
        if (!$ballotlog) {
            return Message(3000,'没有相关记录');
        }

        return MessageInfo(0,'查询成功',$ballotlog);
    }

    /**
     * 老虎机点击开始抽奖
     * @param aid,(ucode,openid),nickname
     */
    function SlotRun($parr)
    {
        //查询老虎机活动
        $result = IGD('Index','Newact')->GetPlatActInfo(24);
        if ($result['code'] != 0) {
            return Message(3000,'活动还没有开始');
        }
        $activitydata = $result['data'];

        //获取剩余的参与次数
        if (!empty($parr['ucode'])) {
            $lotterywhere['c_ucode'] = $parr['ucode'];
            $lotterywhere['c_rule'] = 4;
            $chance = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');
        } else {
            if (!$parr['openid']) {
                return Message(3002,'请使用微信参与');
            }
            $num = S('SLOT'.$parr['openid']);
            $chance = isset($num)?explode('|', $num)[0]:0;
        }

        if ($chance <= 0) {
            return Message(3001,'您的抽奖机会已经用完');
        }

        //判断是否中奖
        $resultprize = $this->ClickWinPrize($parr);
        // 防止用户在同一活动中多次中奖
        if ($rouletteconf['repeat'] == 2) {
            $seacherwhere['c_ucode'] = $ucode;
            $seacherwhere['c_joinaid'] = $activitydata['c_id'];
            $seacherwhere['c_type'] = 4;
            $seacherlog = M('A_actlog')->where($seacherwhere)->find();
            if ($seacherlog) {
                $resultprize['data'] = 8; //8等奖,7等奖,6六等奖,5五等奖,4四等奖,3三等奖,2二等奖,1一等奖
            }
        }

        //查询奖项
        $prizewhere['c_joinaid'] = $activitydata['c_id'];
        $prizewhere['c_today_prize'] = 1;
        $prizewhere['c_state'] = 1;
        $prizewhere['c_delete'] = 2;
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_marks'] = $resultprize['data'];
        $prizedata = M('A_actprize')->where($prizewhere)->find();
        if (!$prizedata) {
            $prizewhere['c_marks'] = 8;
            $prizedata = M('A_actprize')->where($prizewhere)->find();
        }
        $prizedata['c_img'] = GetHost().'/'.$prizedata['c_img'];

        if ($prizedata['c_type'] == 2) {  //中现金
            //随机金额
            $movalue = rand($prizedata['c_value']*100,$prizedata['c_maxvalue']*100)/100;
            $prizedata['c_value'] = $movalue;
            
        } else if ($prizedata['c_type'] == 2) { //中实物
            
        }

        if (!$prizedata) {
            return Message(3004,'活动奖品已派完');
        }

        $db = M('');
        $db->startTrans();

        //写入中奖记录
        $log['c_ucode'] = $parr['ucode'];
        $log['c_pid'] = $prizedata['c_pid'];
        $log['c_joinaid'] = $prizedata['c_joinaid'];
        $log['c_awid'] = $prizedata['c_id'];
        $log['c_acode'] = $prizedata['c_acode'];
        $log['c_name'] = $prizedata['c_name'];
        $log['c_img'] = $prizedata['c_img'];
        $log['c_value'] = $prizedata['c_value'];
        $log['c_marks'] = $prizedata['c_marks'];
        $log['c_type'] = $prizedata['c_type'];
        $log['c_state'] = 0;
        $log['c_addtime'] = gdtime();
        $result = M('A_actlog')->add($log);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '记录写入失败');
        }

        $prizedata['lid'] = $result;
        //改变用户参与机会
        if (!empty($parr['ucode'])) {
            $result = M('Activity_lotterynum')->where($lotterywhere)->setDec('c_num',1);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1035, '扣除用户抽奖次数失败');
            }
        } else {
            $numarr = explode('|', $num);
            $numtime = ($numarr[0] - 1).'|'.$numarr[1];
            S('SLOT'.$parr['openid'],$numtime,86400);
        }

        $db->commit();
        return MessageInfo(0,'获取成功',$prizedata);
    }

    /**
     *  随机中奖项
     *  @param count
     *  @return type:8八等奖,7七等奖,6六等奖,5五等奖,4四等奖,3三等奖,2二等奖,1一等奖
     */
    function ClickWinPrize($parr) {
        $type = 8;

        $rom = rand(1,1000);
        if ($rom == 28) {
            $type = 1;
        } else if ($rom > 25 && $rom <= 27) {
            $type = 2;
        } else if ($rom > 22 && $rom <= 25) {
            $type = 3;
        } else if ($rom > 18 && $rom <= 22) {
            $type = 4;
        } else if ($rom > 13 && $rom <= 18) {
            $type = 5;
        } else if ($rom > 7 && $rom <= 13) {
            $type = 6;
        } else if ($rom > 0 && $rom <= 7) {
            $type = 7;
        }

        return MessageInfo(0,'参与成功',$type);
    }

    /**
     * 领取奖项操作
     * @param ucode,openid,lid
     */
    function RecieveWinPrize($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009,'请先登录再操作');
        }

        $where['c_id'] = $parr['lid'];
        $prizelogdata = M('A_actlog')->where($where)->find();
        if ($prizelogdata['c_state'] == 1) {
            return Message(3000,'您已经领取该奖项');
        }

        //查询老虎机活动
        $result = IGD('Index','Newact')->GetPlatActInfo(24);
        if ($result['code'] != 0) {
            return Message(3000,'活动已结束');
        }
        $activitydata = $result['data'];

        //查询奖项
        $prizewhere['c_id'] = $prizelogdata['c_awid'];
        $prizewhere['c_state'] = 1;
        $prizewhere['c_delete'] = 2;
        $prizewhere['c_num'] = array('GT',0);
        $prizedata = M('A_actprize')->where($prizewhere)->find();
        if (!$prizedata) {
            return Message(3002,'该奖品已被领完');
        }

        $db = M('');
        $db->startTrans();

        if ($prizedata['c_type'] == 2) {
            // 写入用户余额
            $rebatemoney = IGD('Money', 'User');
            $moneyparr['ucode'] = $parr['ucode'];
            $moneyparr['money'] = $prizelogdata['c_value'];
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
                $db->rollback();
                return $result;
            }
            
            $prizedata['c_value'] = $prizelogdata['c_value'];
        } else if ($prizedata['c_type'] == 4) {
            //进行转化订单操作
            $result = IGD('Actoder','Order')->CreataOrderInfo($parr['ucode'],$activitydata,$prizedata);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
            $prizedata['orderid'] = $result['data'];
        }

        //改变领取状态
        $save['c_state'] = 1;
        $save['c_orderid'] = $prizedata['orderid'];
        $save['c_ucode'] = $parr['ucode'];
        $result = M('A_actlog')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(3009,'领取记录操作失败');
        }

        //扣除奖品数量
        $result = M('A_actprize')->where($prizewhere)->setDec('c_num',1);
        if (!$result) {
            $db->rollback();
            return Message(3010,'扣除奖项数量失败');
        }

        $db->commit();

        // 写入消息中心
        if (!empty($parr['ucode'])) {
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $parr['ucode'];
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            if ($prizedata['c_type'] == 2) {
                $msgdata['title'] = '系统消息';
                $msgdata['type'] = 0;
                $msgdata['tag'] = 2;
                $msgdata['content'] = '您在'.$activitydata['c_activityname'].'中获得红包金额为￥' . $prizelogdata['c_value'] . '，领取成功已转入余额';
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $msgresult = $Msgcentre->CreateMessege($msgdata);
            } else if ($prizedata['c_type'] == 4) {
                $msgdata['type'] = 1;
                $msgdata['title'] = '订单消息';
                $msgdata['tag'] = 3;
                $msgdata['tagvalue'] = $prizedata['orderid'];
                $msgdata['content'] = '您在'.$activitydata['c_activityname'].'中获得一个奖品：' . $prizedata['c_name'] . '，已转入订单';
                $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $prizedata['orderid'];
                $msgresult = $Msgcentre->CreateMessege($msgdata);
            }
        }

        return MessageInfo(0, '领取成功,详细信息请登入小蜜APP',$prizedata);
    }

    /**
     * 获取老虎机奖品数据
     * @param aid,(ucode,openid),(sign)
     */
   function GetSlotPrize($parr)
   {
        // 查询活动
        $result = IGD('Index','Newact')->GetPlatActInfo(24,1);
        if ($result['code'] != 0) {
            return Message(3000,'没有相关数据');
        }
        $activitydata = $result['data'];

        if (!empty($parr['ucode'])) {
            $lotterywhere['c_ucode'] = $parr['ucode'];
            $lotterywhere['c_rule'] = 4;
            $num = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');
            $num = isset($num)?$num:0;
        } else {
            if (empty($parr['sign'])) {
                if (!$parr['openid']) {
                    return Message(3002,'请使用微信参与');
                }
                $num = S('SLOT'.$parr['openid']);
                $num = isset($num)?explode('|', $num)[0]:0;
            } else {
                $num = 0;
            }

        }

        //获取奖品信息
        $activitydata['c_pimg'] = GetHost().'/'.$activitydata['c_pimg'];
        $activitydata['c_img'] = GetHost().'/'.$activitydata['c_img'];
        $activitydata['num'] = $num;

        $where['c_joinaid'] = $activitydata['c_id'];
        $where['c_today_prize'] = 1;
        $where['c_status'] = 1;
        $field = 'c_id,c_name,c_totalnum,c_img,c_value';
        $data = M('A_actprize')->where($where)->field($field)->order('c_marks asc')->limit(8)->select();
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
     * 查询老虎机获奖记录20条
     * @param
     */
    function GetSlotLog($parr)
    {
        $result = IGD('Index','Newact')->GetPlatActInfo(24,1);
        if ($result['code'] != 0) {
            return Message(3000,'没有相关数据');
        }
        $activitydata = $result['data'];

        $join = 'inner join t_a_actprize as b on a.c_awid=b.c_id';
        $where['a.c_joinaid'] = $activitydata['c_id'];
        $where['a.c_type'] = array('neq',1);
        $where['a.c_state'] = 1;
        $field = 'a.c_id,a.c_ucode,a.c_addtime as time,b.c_name,a.c_value,b.c_type,a.c_nickname';
        $data = M('A_actlog as a')->join($join)->where($where)->field($field)->order('a.c_addtime desc')->limit(20)->select();
        if (!$data) {
            return Message(3000,'没有相关数据');
        }
        foreach ($data as $key => $value) {
            if ($value['c_nickname']) {
                $data[$key]['name'] = $value['c_nickname'];
            } else {
                $userwhere['c_ucode'] = $value['c_ucode'];
                $data[$key]['name'] = M('Users')->where($userwhere)->getField('c_nickname');
            }

            $data[$key]['time'] = date('Y-m-d',strtotime($value['time']));
            if ($value['c_type'] == 2) {
                $data[$key]['praisecontent'] = $value['c_name'].'￥'.$value['c_value'];
            } else {
                $data[$key]['praisecontent'] = $value['c_name'];
            }

        }
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 微信用户转发赠送一次抽奖机会
     * @param openid,num
     */
    function AddSlotNum($parr,$num,$sign='SLOT')
    {
        if (!$parr['openid']) {
            return Message(3002,'请在微信端操作');
        }

        //非会员缓存次数
        $numstr = S($sign.$parr['openid']);
        $numarr = explode('|', $numstr);

        if (!empty($numstr)) {
            $newnum = $numarr[0] + $num;
            $numtime = $newnum.'|'.$numarr[1];
            S($sign.$parr['openid'],$numtime,86400);
        } else {
            $time1 = date('Y-m-d', time());
            $numtime = $num.'|'.$time1;
            S($sign.$parr['openid'],$numtime,86400);
        }

        return Message(0,'赠送成功');
    }

    /**
     * 转发浏览获得现金
     * @param openid,nickname,headimg(ucode),aid,url,vcode
     */
    function ShareGetMoney($parr)
    {
        if (!empty($parr['ucode'])) {
            $userwhere['c_ucode'] = $parr['ucode'];
            $userinfo = M('Users')->where($userwhere)->find();
            $add['c_ucode'] = $userinfo['c_ucode'];
            $add['c_nickname'] = $userinfo['c_nickname'];
            $add['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
        } else {
            if (empty($parr['openid'])) {
                return Message(3000,'请从微信转发');
            }
            $add['c_openid'] = $parr['openid'];
            $add['c_nickname'] = $parr['nickname'];
            $add['c_headimg'] = $parr['headimg'];
        }

        $where['c_vcode'] = $parr['vcode'];
        $result = M('Share_log')->where($where)->find();
        if ($result) {
            return Message(3001,'您已经分享');
        }

        //添加转发记录
        $add['c_vcode'] = $parr['vcode'];
        $add['c_aid'] = $parr['aid'];
        $add['c_url'] = $parr['url'];
        $add['c_sign'] = 2;
        $add['c_state'] = 1;
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Share_log')->add($add);
        if (!$result) {
            return Message(3002,'转发记录添加失败');
        }

        return Message(0,'转发成功');
    }

    /**
     * 转发获得抽奖次数
     * @param openid,nickname,headimg(ucode),aid,url,vcode
     */
    function ShareGetJoinnum($parr,$num)
    {
        //查询活动
        $activitywhere['c_id'] = $parr['aid'];
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();

        //查询转发记录
        $sharelogwhere['c_sign'] = 1;
        $sharelogwhere[] = array("c_ucode='".$parr['ucode']."' or c_openid='".$parr['openid']."'");
        $sharelogwhere[] = array("c_addtime>='".date('Y-m-d 00:00:00')."' and c_addtime<='".date('Y-m-d 23:59:59')."'");
        $sharelog = M('Share_log')->where($sharelogwhere)->select();
        $sign = 0;
        if (count($sharelog) == $num) {
            $sign = 1;
        }

        $db = M('');
        $db->startTrans();

        if ($sign == 1 && $activitydata) {
            //添加老虎机抽奖次数
            if (!empty($parr['ucode'])) {
                $parr['rule'] = 4;
                $result = IGD('Question','Activity')->AddRouletteNum($parr,1);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return $result;
                }
            } else {
                $result = $this->AddSlotNum($parr,1);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return $result;
                }
            }
            $add['c_state'] = 1;
        }

        //写入转发记录
        if (!empty($parr['ucode']))  {
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
        $add['c_vcode'] = $parr['vcode'];
        $add['c_sign'] = 1;
        $add['c_aid'] = $parr['aid'];
        $add['c_url'] = $parr['url'];
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Share_log')->add($add);
        if (!$result) {
            $db->rollback();
            return Message(3002,'记录写入失败');
        }

        $db->commit();
        return Message(0,'转发成功');
    }

    /**
     * 浏览链接分享人获得金额
     * @param openid,nickname,headimg(ucode),vcode,aid,sign
     */
    function ViewShareLog($parr)
    {
        $viewnum = 9;                     //分享浏览人数
        $money = rand(100,150)/100;       //分享浏览赠送金额
        if (empty($parr['openid'])) {
            return Message(1003,'请从微信访问链接');
        }

        //查询分享记录信息
        $sharewhere['c_vcode'] = $parr['vcode'];
        $sharedata = M('Share_log')->where($sharewhere)->find();
        if (!$sharedata) {
            return Message(3000,'分享人记录不存在');
        }

        //查询浏览记录
        $viewwhere['c_vcode'] = $parr['vcode'];
        $countview = M('Share_viewlog')->where($viewwhere)->count();
        $viewwhere['c_openid'] = $parr['openid'];
        $shareview = M('Share_viewlog')->where($viewwhere)->find();
        if (!$shareview) {
            //查询活动
            $activitywhere['c_id'] = $parr['aid'];
            $activitywhere['c_state'] = 1;
            $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
            $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
            $activitydata = M('Activity')->where($activitywhere)->find();

            $db = M('');
            $db->startTrans();

            //写入浏览记录
            $add['c_vcode'] = $parr['vcode'];
            $add['c_openid'] = $parr['openid'];
            $add['c_nickname'] = $parr['nickname'];
            $add['c_headimg'] = $parr['headimg'];
            $add['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Share_viewlog')->add($add);
            if (!$result) {
                $db->rollback();
                return Message(3002,'记录写入失败');
            }

            if ($activitydata && (($countview%$viewnum) == 0) && $parr['sign'] == 1 && $countview < 100 && $countview > 0) {
                //增加记录表金额
                $result = M('Share_log')->where($sharewhere)->setInc('c_money',$money);
                if (!$result) {
                    $db->rollback();
                    return Message(3003,'记录金额增加失败');
                }

                if (empty($sharedata['c_openid'])) {
                    // 写入用户余额
                    $rebatemoney = D('Money', 'User');
                    $moneyparr['ucode'] = $sharedata['c_ucode'];
                    $moneyparr['money'] = $money;
                    $moneyparr['source'] = 10;
                    $moneyparr['key'] = $parr['vcode'];
                    $moneyparr['desc'] = "分享链接，10位好友浏览获得分享金额";
                    $moneyparr['state'] = 1;  //完成状态
                    $moneyparr['type'] = 1;
                    $moneyparr['isagent'] = 0;
                    $moneyparr['joinaid'] = $activitydata['c_id'];
                    $moneyparr['showimg'] = 'Uploads/settlementshow/huo.png';
                    $moneyparr['showtext'] = '活动';
                    $result = $rebatemoney->OptionMoney($moneyparr);
                    if ($result['code'] != 0) {
                        $db->rollback();
                        return $result;
                    }

                    // 写入消息中心
                    $Msgcentre = IGD('Msgcentre', 'Message');
                    $msgdata['ucode'] = $sharedata['c_ucode'];
                    $msgdata['platform'] = 1;
                    $msgdata['sendnum'] = 1;
                    $msgdata['title'] = '系统消息';
                    $msgdata['type'] = 0;
                    $msgdata['tag'] = 2;
                    $msgdata['content'] = '分享链接，【'.$parr['nickname'].'】...等10位好友浏览，获得金额为￥' . $money . '，领取成功已转入余额';
                    $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
                    $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
                    $msgresult = $Msgcentre->CreateMessege($msgdata);

                } else {
                    //企业打款现金
                    $trade_no = CreateOrder('dk');
                    $openid = $sharedata['c_openid'];
                    $amount = $money;
                    $username = $parr['nickname'];
                    $result = IGD('WxEnterprisepay','Weixin')->Pay($trade_no,$openid,$amount,$username);
                    if ($result['code'] != 0) {
                        // $db->rollback();
                        return $result;
                    }
                }
            }

            $db->commit();
        }

        return Message(0,'记录成功');
    }

}
