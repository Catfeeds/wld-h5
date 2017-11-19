<?php

/**
 * 口令红包接口
 */
class WordredActivity {

    /**
     * 添加口令红包
     * @param pid,ucode(openid,unionid,nickname,headimgurl),type
     */
    public function AddWordred($parr) {

        //查询平台活动是否存在
        $activitydata = D('Jubao','Service')->GetPlathavingAct('19')['data'];
        if (!$activitydata) {
            return Message(3000, '口令红包还没准备好！');
        }

        //查询钱库
        $moneywhere['c_aid'] = $activitydata['c_id'];
        $moneydata = M('Activity_money')->where($moneywhere)->find();
        if ($moneydata['c_remain'] <= 0) {
            return Message(3001, '口令红包还在来的路上！');
        }

        //查询口令库
        $infowhere['c_status'] = 1;
        $infowhere['c_aid'] = $activitydata['c_id'];
        $infowhere['c_type'] = 1;
        if (!empty($parr['type'])) {
            $infowhere['c_type'] = $parr['type'];
        }
        $wordinfo = M('Word')->where($infowhere)->order('c_id desc')->find();
        if (!$wordinfo) {
            return Message(3002, '口令还没准备好！');
        }

        //限制每日口令领取次数
        if (!empty($parr['ucode'])) {
            $userwhere['c_ucode'] = $parr['ucode'];
            $userinfo = M('Users')->where($userwhere)->find();
            $add['c_ucode'] = $userinfo['c_ucode'];
            $add['c_username'] = $userinfo['c_nickname'];
            $add['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
            $limitwhere['c_ucode'] = $userinfo['c_ucode'];
        } else {
            if (empty($parr['openid'])) {
                return Message(3004, '请使用微信授权再参与！');
            }
            $add['c_openid'] = $parr['openid'];
            $add['c_unionid'] = $parr['unionid'];
            $add['c_username'] = $parr['nickname'];
            $add['c_headimg'] = $parr['headimgurl'];
            $limitwhere['c_openid'] = $parr['openid'];
        }
        $limitwhere['c_aid'] = $activitydata['c_id'];
        $limitwhere['c_type'] = 1;
        $limitwhere['c_addtime'] = array('EGT',date('Y-m-d 00:00:00'));
        $limitwhere['c_addtime'] = array('ELT',date('Y-m-d 23:59:59'));
        $limitnum = M('Word_red')->where($limitwhere)->count('c_id');
        if ($limitnum >= $wordinfo['c_limit']) {
            return Message(3005, '您今日已不能再获取口令！');
        }

        //生成随机口令
        $length = rand($wordinfo['c_min'],$wordinfo['c_max']);
        $chararr = arr_split_zh($wordinfo['c_string']);
        $max = count($chararr) - 1;
        for ($i=0; $i < $length; $i++) {
            $hash .= $chararr[mt_rand(0, $max)];
        }

        //判断分享人口令有效性
        if (!empty($parr['pid'])) {
            //查询分享人口令是否存在
            $proshare = M('Word_red')->where(array('c_id'=>$parr['pid']))->find();
            if (!$proshare) {
                return Message(2000,'分享人不存在');
            }
            $hash = $proshare['c_name'];

            $wordwhere['c_aid'] = $activitydata['c_id'];
            $wordwhere['c_pid'] = $parr['pid'];
            $wordcount = M('Word_red')->where($wordwhere)->count('c_id');
            if ($wordcount >= $wordinfo['c_num']) {
                return Message(3006, '该口令已失效！');
            }
        }

        //生成随机金额 扣除总金额
        $moneyvalue = rand($moneydata['c_min_money']*100,$moneydata['c_max_money']*100)/100;
        $moneywhere['c_remain'] = array('EGT',$moneyvalue);
        $result = M('Activity_money')->where($moneywhere)->setDec('c_remain',$moneyvalue);
        if (!$result) {
            return Message(3007, '口令对应金额失败！');
        }

        $add['c_pid'] = $parr['pid'];
        $add['c_name'] = $hash;
        $add['c_pucode'] = $proshare['c_ucode'];
        $add['c_aid'] = $activitydata['c_id'];
        $add['c_money'] = $moneyvalue;
        $add['c_updatetime'] = date('Y-m-d H:i:s');
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Word_red')->add($add);
        if (!$result) {
            return Message(3008, '添加失败');
        }

        $pid = $result;
        if ($parr['type'] == 2 && !empty($parr['ucode'])) {
            //发送对应消息
            $Msgcentre = D('Msgcentre', 'Service');
            $msgdata['ucode'] = $parr['ucode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['tag'] = 2;
            $msgdata['content'] = '您在'.$activitydata['c_activityname'].'活动中获得一个口令，点击进入领取';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Wordred/index?pid='.$pid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Wordred/index?pid='.$pid;
            $msgresult = $Msgcentre->CreateMessege($msgdata);
        }

        return MessageInfo(0, '添加成功',$add);
    }

    /**
     * 输入口令生成口令红包
     * @param name,ucode,openid,pid
     */
    function WordCreateRed($parr)
    {
        $ucode = $parr['ucode'];
        $openid = $parr['openid'];

        // 查询用户信息
        $userwhere['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($userwhere)->find();
        if (!$userinfo) {
            return Message(1009, '用户信息不存在！');
        }
        $add['c_ucode'] = $userinfo['c_ucode'];
        $add['c_username'] = $userinfo['c_nickname'];
        $add['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        $add['c_updatetime'] = date('Y-m-d H:i:s');

        //查询平台活动是否存在
        $activitydata = D('Jubao','Service')->GetPlathavingAct('19')['data'];
        if (!$activitydata) {
            return Message(3000, '口令红包还没准备好！');
        }

        //查询钱库
        $moneywhere['c_aid'] = $activitydata['c_id'];
        $moneydata = M('Activity_money')->where($moneywhere)->find();
        if ($moneydata['c_remain'] <= 0) {
            return Message(3001, '口令红包还在来的路上！');
        }

        //查询固定口令红包
        $wordwhere['c_aid'] = $activitydata['c_id'];
        $wordwhere['c_status'] = 1;
        $wordwhere['c_name'] = $parr['name'];
        $fixedword = M('Word_fixed')->where($wordwhere)->find();

        //查询用户是否生成口令
        $wordwhere1['c_aid'] = $activitydata['c_id'];
        $wordwhere1['c_name'] = $parr['name'];
        $wordwhere1[] = array("c_ucode='$ucode' or c_openid='$openid'");
        $generalword = M('Word_red')->where($wordwhere1)->find();

        //查询用户是否来源分享口令
        $sharewhere['c_type'] = 1;
        $sharewhere['c_id'] = $parr['pid'];
        $sharewhere['c_pid'] = 0;
        $shareword = M('Word_red')->where($sharewhere)->find();
        if ($generalword) {
            $savewhere['c_id'] = $generalword['c_id'];
            $result = M('Word_red')->where($savewhere)->save($add);
            if (!$result) {
                return Message(3007, '口令匹配失败！');
            }
            return Message(0, '口令匹配成功！');
        }

        /*新增口令部分*/
        $db = M('');
        $db->startTrans();

        if ($fixedword && !$shareword) {        //固定口令红包
            $moneyvalue = $fixedword['c_money'];
            //扣除总金额
            if ($moneyvalue <= 0) {
                $moneyvalue = rand($moneydata['c_min_money']*100,$moneydata['c_max_money']*100)/100;
            }
            $moneywhere['c_remain'] = array('EGT',$moneyvalue);
            $result = M('Activity_money')->where($moneywhere)->setDec('c_remain',$moneyvalue);
            if (!$result) {
                $db->rollback();
                return Message(3007, '口令对应金额失败！');
            }

            //减少固定口令数量
            $decwhere['c_id'] = $fixedword['c_id'];
            $decwhere['c_remainnum'] = array('GT',0);
            $result = M('Word_fixed')->where($decwhere)->setDec('c_remainnum',1);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(3000,'口令已失效！');
            }

            //新增口令红包
            $add['c_type'] = 2;
            $add['c_name'] = $parr['name'];
            $add['c_aid'] = $activitydata['c_id'];
            $add['c_money'] = $moneyvalue;
            $add['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Word_red')->add($add);
        } else if (!$fixedword && $shareword) {   //分享口令
            if ($shareword['c_ucode'] == $ucode) {
                $db->rollback();
                return Message(3005, '您已领取该口令红包！');
            }

            //查询口令库
            $infowhere['c_status'] = 1;
            $infowhere['c_type'] = 2;
            $infowhere['c_aid'] = $activitydata['c_id'];
            $wordinfo = M('Word')->where($infowhere)->order('c_id desc')->find();
            if (!$wordinfo) {
                $db->rollback();
                return Message(3002, '口令还没准备好！');
            }


            //限制用户每日领取次数
            $limitwhere[] = array("c_ucode='$ucode' or c_openid='$openid'");
            $limitwhere['c_aid'] = $activitydata['c_id'];
            $limitwhere['c_type'] = 1;
            $limstart = date('Y-m-d 00:00:00');
            $limend = date('Y-m-d 23:59:59');
            $limitwhere[] = array("c_addtime>='$limstart' and c_addtime<='$limend'");
            $limitnum = M('Word_red')->where($limitwhere)->count('c_id');
            if ($limitnum >= $wordinfo['c_limit']) {
                $db->rollback();
                return Message(3005, '您今日已不能再领取！');
            }

            //判断分享人口令有效性
            $wordwhere2['c_aid'] = $activitydata['c_id'];
            $wordwhere2['c_pid'] = $parr['pid'];
            $wordcount = M('Word_red')->where($wordwhere2)->count('c_id');
            if ($wordcount >= $wordinfo['c_num']) {
                $db->rollback();
                return Message(3006, '该口令已失效！');
            }

            //扣除总金额
            $moneyvalue = rand($moneydata['c_min_money']*100,$moneydata['c_max_money']*100)/100;
            $moneywhere['c_remain'] = array('EGT',$moneyvalue);
            $result = M('Activity_money')->where($moneywhere)->setDec('c_remain',$moneyvalue);
            if (!$result) {
                $db->rollback();
                return Message(3007, '口令对应金额失败！');
            }

            //新增口令红包
            $add['c_pid'] = $parr['pid'];
            $add['c_name'] = $shareword['c_name'];
            $add['c_pucode'] = $shareword['c_ucode'];
            $add['c_aid'] = $activitydata['c_id'];
            $add['c_money'] = $moneyvalue;
            $add['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Word_red')->add($add);
        }

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(3008, '口令解析失败！');
        }

        $db->commit();
        return Message(0, '口令解析成功！');
    }

    /**
     * 领取口令红包
     * @param ucode,name,aid
     */
    function RecieveWordRed($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009, '请登录后再操作！');
        }

        //查询平台活动是否存在
        $activitydata = D('Jubao','Service')->GetPlathavingAct('19')['data'];
        if (!$activitydata) {
            return Message(3000, '口令红包还没准备好！');
        }

        //查询口令红包是否存在
        $wordwhere['c_ucode'] = $parr['ucode'];
        $wordwhere['c_name'] = $parr['name'];
        $redword = M('Word_red')->where($wordwhere)->order('c_id desc')->find();
        if (!$redword) {
            return Message(3000, '您输入的口令有误！');
        }

        if ($redword['c_status'] == 1) {
            return Message(3002, '您已领取该口令红包！');
        }

        $db = M('');
        $db->startTrans();

        // 写入用户余额
        $rebatemoney = D('Common', 'Service');
        $moneyparr['ucode'] = $parr['ucode'];
        $moneyparr['money'] = $redword['c_money'];
        $moneyparr['source'] = 3;
        $moneyparr['key'] = $activitydata['c_activityname'];
        $moneyparr['desc'] = "您在".$activitydata['c_activityname']."活动中获得口令红包";
        $moneyparr['state'] = 1;  //完成状态
        $moneyparr['type'] = 1;
        $moneyparr['isagent'] = 0;
        $moneyparr['joinaid'] = $activitydata['c_id'];
        $moneyparr['showimg'] = 'Uploads/settlementshow/hong.png';
        $moneyparr['showtext'] = '红包';
        $result = $rebatemoney->OptionMoney($moneyparr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '修改用户余额失败！');
        }

        //改变领取状态
        $wordwhere['c_aid'] = $activitydata['c_id'];
        $wordwhere['c_ucode'] = $parr['ucode'];
        $wordsave['c_status'] = 1;
        $wordsave['c_recivetime'] = date('Y-m-d H:i:s');
        $result = M('Word_red')->where($wordwhere)->save($wordsave);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1034, '修改用户余额失败！');
        }

        $db->commit();

        // 写入消息中心
        $Msgcentre = D('Msgcentre', 'Service');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您在'.$activitydata['c_activityname'].'活动中，成功领取口令红包，总金额￥'.$redword['c_money'].'，已成功转入余额';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgresult = $Msgcentre->CreateMessege($msgdata);
        return MessageInfo(0,'领取成功！',$redword);
    }

    /**
     * 查询用户是否有对应的口令红包
     * @param ucode,pid(fid),aid
     */
    function FindWordRed($parr)
    {
        $wordwhere['c_aid'] = $parr['aid'];
        $field = 'c_id,c_aid,c_name';
        if (empty($parr['pid']) && !empty($parr['fid'])) {
            $wordwhere['c_id'] = $parr['fid'];
            $Word_red = M('Word_fixed');
        } else {
            $wordwhere['c_id'] = $parr['pid'];
            $wordwhere['c_type'] = 1;
            $Word_red = M('Word_red');
        }
        $redword = $Word_red->where($wordwhere)->order('c_id desc')->field($field)->find();
        return MessageInfo(0, '查询成功！',$redword);
    }

    /**
     * 查询个人口令红包
     * @param ucode,openid
     */
    function GetmyRedInfo($parr)
    {
        $ucode = $parr['ucode'];
        $openid = $parr['openid'];

        //查询平台活动是否存在
        $activitydata = D('Jubao','Service')->GetPlathavingAct('19')['data'];
        if (!$activitydata) {
            return Message(3000,'活动不存在');
        }

        $wordwhere['c_status'] = 0;
        $wordwhere['c_aid'] = $activitydata['c_id'];
        $wordwhere[] = array("c_ucode='$ucode' or c_openid='$openid'");
        $result = M('Word_red')->where($wordwhere)->find();
        if (!$result) {
            return Message(3001,'没有相关记录');
        }

        return MessageInfo(0, '查询成功！',$result);
    }

    /**
     * 查询领取记录
     * @param string $value [description]
     */
    public function GetRedlog($parr)
    {
        //查询平台活动是否存在
        $activitydata = D('Jubao','Service')->GetPlathavingAct('19')['data'];
        if (!$activitydata) {
            return Message(3000,'活动不存在');
        }

        $wordwhere['c_status'] = 1;
        $wordwhere['c_aid'] = $activitydata['c_id'];
        $list = M('Word_red')->where($wordwhere)->limit(20)->order('c_id desc')->select();

        return MessageInfo(0, '查询成功！',$list);
    }
}
