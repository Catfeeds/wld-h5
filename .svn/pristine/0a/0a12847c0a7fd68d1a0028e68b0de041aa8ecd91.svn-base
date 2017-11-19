<?php

/**
 * 	新年积福活动
 *
 */
class CollectActivity {

    /**
     *  点击发现,分发红包雨
     *  @param   parr 传递变量数组
     *  @param   aconf 活动配置数组
     *  @param   activitydata 活动信息
     *  @param   arr 返回数组
     *  @param   tempcount 数组下标量
     */
    function CollectSomething($parr, $aconf, $activitydata, $arr, $tempcount) {
        //红包雨显示红包个数
        $shownum = $aconf['collectnum'];
        //红包雨红包出现位置范围

        $distance = $this->Getmaplevel($parr);
        $localpointarr = returnSquarePoint($parr['longitude'], $parr['latitude'], $distance);

        $lefttop = $localpointarr['left-top'];
        $rightbottom = $localpointarr['right-bottom'];

        $falg = 0;
        while ($shownum > 0) {
            $falg = 1;
            $arr[$tempcount]['type'] = 5;
            $arr[$tempcount]['longitude'] = sprintf("%.6f", rand($lefttop['lng'] * 1000000, $rightbottom['lng'] * 1000000) / 1000000);
            $arr[$tempcount]['latitude'] = sprintf("%.6f", rand($rightbottom['lat'] * 1000000, $lefttop['lat'] * 1000000) / 1000000);
            ;
            $arr[$tempcount]['name'] = "新春红包";
            $arr[$tempcount]['remarks'] = "";
            $arr[$tempcount]['isshop'] = 0;
            $arr[$tempcount]['shopnumimg'] = "";
            $arr[$tempcount]['signature'] = "";
            $arr[$tempcount]['address'] = "";
            $arr[$tempcount]['distance'] = "";
            $arr[$tempcount]['jumptype'] = 6;
            $arr[$tempcount]['keyvalue'] = GetHost(2) . '/index.php/Api/Activityv2/recievecollect';
            $arr[$tempcount]['imgtype'] = 6;
            $arr[$tempcount]['img'] = GetHost() . '/' . $activitydata['c_pimg'];
            $arr[$tempcount]['basemap'] = '';
            $arr[$tempcount]['signimg'] = '';
            $tempcount++;
            $shownum--;
        }

        if ($falg == 1) {
            return MessageInfo(0, "获取成功", $arr);
        }

        return Message(1037, '没有红包');
    }

    //领取接口
    public function ReceiveCollect($parr) {
        $ucode = $parr['ucode'];
        $calltime = $parr['calltime'];
        if (empty($ucode)) {
            return Message(1009, '您尚未登录，不能领取！');
        }

        $win_falg = 0; //中奖标识 0-空奖，1-红包，2-福字
        //查询活动是否开始
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 18;
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(2000, '活动未开始');
        }

        //开始分配奖品
        $randnum = rand(1, 100);

        //获取活动配置
        $aconf = IGD('Redis', 'Redis')->Gethash('activity')['data'];

        //判断活动时间是否已过
        $falg = 0;
        $nowtime = time();
        $toptime = date('Y-m-d');
        $timearr = explode('|', $aconf['collecttime']);
        foreach ($timearr as $key => $value1) {
            $limittime = explode('-', $value1);
            $begain = strtotime($toptime . ' ' . $limittime[0]);
            $stops = strtotime($toptime . ' ' . $limittime[1]);
            if ($nowtime >= $begain && $nowtime <= $stops) {
                $falg = 1;
            }
        }
        if($falg == 0){
            return Message(2001, '对不起，今日活动已结束！');
        }

        if ($aconf['redrand']) {
            $redrand_arr = explode('|', $aconf['redrand']);

            if ($randnum >= $redrand_arr[0] && $randnum <= $redrand_arr[1]) {
                $win_falg = 1;
            }
        }

        if ($aconf['resrand']) {
            $resrand_arr = explode('|', $aconf['resrand']);

            if ($randnum >= $resrand_arr[0] && $randnum <= $resrand_arr[1]) {
                $win_falg = 2;
            }
        }

        //判断接口请求时间，防止刷接口
        // $pjtime = strtotime('-1 Minute', time());  //中奖一分钟未领失效
        // $sign = $calltime > $pjtime ? 0 : 1;
        // if($sign == 0){
        // 	$win_falg = 0;
        // }

        //随机出红包奖项
        if ($win_falg == 1) {
            $result = $this->ReceiveRed($ucode, $activitydata);
            if ($result['code'] == 0) {
                return $result;
            }

            $win_falg = 0;
        }

        //随机出福字奖项
        if ($win_falg == 2) {
            $result = $this->ReceiveRes($ucode, $activitydata);
            if ($result['code'] == 0) {
                return $result;
            }

            $win_falg = 0;
        }

        if ($win_falg == 0) {
            $collectwhere['c_type'] = 1;
            $collectwhere['c_aid'] = $activitydata['c_id'];
            $collectdata = M('Collect_prize')->where($collectwhere)->order('rand()')->find();

            if (!$collectdata) {
                return Message(2002, '数据错误！');
            }

            // 写入活动记录表
            $prizelogdata['c_ucode'] = $ucode;
            $prizelogdata['c_cid'] = $collectdata['c_id'];
            $prizelogdata['c_aid'] = $activitydata['c_id'];
            $prizelogdata['c_type'] = 3;
            $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Activity_nulllog')->add($prizelogdata);

            //返回数据
            $returndata['win_falg'] = 0;
            $returndata['money'] = "";
            $returndata['hint'] = "";
            $returndata['acthint'] = "";
            $returndata['img'] = GetHost() . '/' . $collectdata['c_imgpath'];
            $returndata['url'] =  '';

            return MessageInfo(0, '领取成功', $returndata);
        }
    }

    //领取红包
    public function ReceiveRed($ucode, $activitydata) {
        //查询金额池表
        $moneywhere['c_state'] = 1;
        $moneywhere['c_remain'] = array('GT', 0);
        $moneywhere['c_aid'] = $activitydata['c_id'];
        $moneydata = M('Activity_money')->where($moneywhere)->find();

        if (!$moneydata) {
            return Message(1035, "没有红包金额");
        }

        if ($moneydata['c_rule'] == 2) {
            //查询奖品表
            $prizewhere['c_type'] = 1;
            $prizewhere['c_state'] = 1;
            $prizewhere['c_num'] = array('GT', 0);
            $prizewhere['c_aid'] = $activitydata['c_id'];
            $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
        }

        $db = M('');
        $db->startTrans();

        // 写入活动记录表
        if ($prizedata) {
            $prizelogdata['c_pcode'] = $prizedata['c_pcode'];
            $prizelogdata['c_pid'] = $prizedata['c_id'];
            $prizelogdata['c_value'] = $prizedata['c_value'];
            $prizelogdata['c_type'] = $prizedata['c_type'];
        } else {
            $moneyprize = rand($moneydata['c_min_money'] * 100, $moneydata['c_max_money'] * 100);
            $prizelogdata['c_value'] = bcdiv($moneyprize, 100, 2);
            if (($moneydata['c_remain'] - $prizelogdata['c_value']) < 0) {
                $prizelogdata['c_value'] = $moneydata['c_remain'];
            }
            $prizelogdata['c_type'] = 1;
        }

        $prizelogdata['c_aid'] = $activitydata['c_id'];
        $prizelogdata['c_ucode'] = $ucode;
        $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_moneylog')->add($prizelogdata);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '奖品领取记录失败！');
        }
        $regionid = $result;

        // 写入用户余额
        $rebatemoney = IGD('Money', 'User');
        $moneyparr['ucode'] = $ucode;
        $moneyparr['money'] = $prizelogdata['c_value'];
        $moneyparr['source'] = 3;
        $moneyparr['key'] = $activitydata['c_activityname'];
        $moneyparr['desc'] = "您在《" . $activitydata['c_activityname'] . "》活动中，发现红包并领取金额";
        $moneyparr['state'] = 1;  //完成状态
        $moneyparr['type'] = 1;
        $moneyparr['isagent'] = 0;
        $moneyparr['joinaid'] = $activitydata['c_id'];
        $moneyparr['showimg'] = 'Uploads/settlementshow/hong.png';
        $moneyparr['showtext'] = '红包';
        $result = $rebatemoney->OptionMoney($moneyparr);
        if ($result['code'] !== 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '修改用户余额失败！');
        }

        if ($prizedata) {
            // 扣除奖项库存
            $jxwhere['c_id'] = $prizedata['c_id'];
            $jxwhere['c_num'] = array('GT', 0);
            $result = M('Activity_prize')->where($jxwhere)->setDec('c_num', 1);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1033, '扣除奖项库存失败！');
            }
        } else {
            // 扣除奖池总额
            $moneywhere1['c_state'] = 1;
            $moneywhere1['c_remain'] = array('GT', $prizelogdata['c_value']);
            $moneywhere1['c_aid'] = $activitydata['c_id'];
            $result = M('Activity_money')->where($moneywhere1)->setDec('c_remain', $prizelogdata['c_value']);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1033, '扣除奖池总额失败！');
            }
        }

        // 写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您发现的红包金额为￥' . $prizelogdata['c_value'] . '，领取成功已转入余额';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';


        $msgresult = $Msgcentre->CreateMessege($msgdata);
        if ($msgresult['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '创建消息失败！');
        }

        $redamount = IGD('Common', 'Redis')->Rediesgetucode('ACT_redamount');
        IGD('Common', 'Redis')->RediesStoreSram('ACT_redamount', $redamount + 1,0);
        $db->commit();

        $returndata['win_falg'] = 1;
        $returndata['money'] = $prizelogdata['c_value'];
        $returndata['hint'] = '已放入您的口袋,快去体验吧!';
        $returndata['acthint'] = "亲可以在\"服务中心-结算中心\"\n找到现金红包哦!";
        $returndata['img'] = "";
        $returndata['url'] = "";

        //添加5公里商圈记录
        //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
        $blogdata['ucode'] = $ucode;
        $blogdata['behavior'] = 3;
        $blogdata['regionid'] = $regionid;
        $blogdata['tag'] = 10000;
        $blogdata['tagvalue'] = '1';

        //查询自己位置信息
        $result1 = IGD('Servecentre', 'Serve')->GetLocation($ucode);
        $localtion = $result1['data'];

        $longitude = $localtion['longitude'];
        $latitude = $localtion['latitude'];
        $address = $localtion['address'];

        $blogdata['longitude'] = $longitude;
        $blogdata['latitude'] = $latitude;
        $blogdata['address'] = $address;
        $blogdata['addtime'] = date('Y-m-d H:i:s', time());

        $result = IGD('Servecentre', 'Serve')->Addlogs($blogdata);

        return MessageInfo(0, '领取成功', $returndata);
    }

    //领取福字
    public function ReceiveRes($ucode, $activitydata) {
        //查询是否存在满足条件的福字
        $collectwhere['c_type'] = 2;
        $collectwhere['c_state'] = 1;
        $collectwhere['c_num'] = array('GT', 0);
        $collectwhere['c_aid'] = $activitydata['c_id'];
        $collectwhere['c_starttime'] = array('ELT', date('Y-m-d H:i:s'));
        $collectwhere['c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
        $collectcount = M('Collect_prize')->where($collectwhere)->select();
        $collectdata = $collectcount[rand(0,(count($collectcount)-1))];
        if (!$collectdata) {
            return Message(2001, '没有相应的碎片');
        }

        //查询用户是否已经领取20个
        $prizepcode['c_ucode'] = $ucode;
        $prizepcode['c_aid'] = $activitydata['c_id'];
        $prizepcode['c_cpid'] = $collectdata['c_id'];
        $usercollect = M('Collect_log')->where($prizepcode)->find();

        $falg = 1;
        if ($usercollect) {
            //查询总记录
            $wherelog['c_ucode'] = $ucode;
            $wherelog['c_aid'] = $activitydata['c_id'];
            $wherelog['c_cid'] = $collectdata['c_id'];
            $zongnum = M('Activity_log')->where($wherelog)->count();
            if ($zongnum >= 9) {
                return Message(2002, '您已经领取完');
            }
        } else {
            $falg = 0;
        }

        $db = M('');
        $db->startTrans();

        //开始领取福字
        if ($falg == 0) {
            $addlog['c_ucode'] = $ucode;
            $addlog['c_aid'] = $activitydata['c_id'];
            $addlog['c_cpid'] = $collectdata['c_id'];
            $addlog['c_num'] = 1;
            $addlog['c_updatetime'] = date('Y-m-d H:i:s', time());
            $addlog['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Collect_log')->add($addlog);
        } else {
            $savelog['c_updatetime'] = date('Y-m-d H:i:s', time());
            $savelog['c_num'] = $usercollect['c_num'] + 1;

            $cw['c_id'] = $usercollect['c_id'];
            $result = M('Collect_log')->where($cw)->save($savelog);
        }

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1029, "领取失败，请稍后再试");
        }

        //扣除剩余数量
        $whereprize['c_id'] = $collectdata['c_id'];
        $whereprize['c_aid'] = $activitydata['c_id'];
        $whereprize['c_num'] = array('GT', 0);
        $result = M('Collect_prize')->where($whereprize)->setDec('c_num', 1);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1030, "领取失败，请稍后再试");
        }

        // 写入活动记录表
        $prizelogdata['c_ucode'] = $ucode;
        $prizelogdata['c_cid'] = $collectdata['c_id'];
        $prizelogdata['c_aid'] = $activitydata['c_id'];
        $prizelogdata['c_type'] = 2;
        $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_log')->add($prizelogdata);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1031, '领取记录添加失败！');
        }

        $db->commit();

        $returndata['win_falg'] = 2;
        $returndata['money'] = "";
        $returndata['hint'] = "";
        $returndata['acthint'] = "";
        $returndata['img'] = GetHost() . '/' . $collectdata['c_imgpath'];
        $returndata['url'] = GetHost(1) . '/index.php/Home/Valentines/index';

        return MessageInfo(0, '领取成功', $returndata);
    }

    //根据地图级别显示不同红包显示距离
    public function Getmaplevel($parr) {

        $distance = 1000;
        $maplevel = $parr['maplevel'];
        if (empty($maplevel)) {
            return $distance;
        }

        $maplevel = floor($maplevel);

        switch ($maplevel) {
            case 1:
                $distance = 10000;
                break;
            case 2:
                $distance = 5000;
                break;
            case 3:
                $distance = 2000;
                break;
            case 4:
                $distance = 1000;
                break;
            case 5:
                $distance = 500;
                break;
            case 6:
                $distance = 200;
                break;
            case 7:
                $distance = 100;
                break;
            case 8:
                $distance = 50;
                break;
            case 9:
                $distance = 25;
                break;
            case 10:
                $distance = 20;
                break;
            case 11:
                $distance = 10;
                break;
            case 12:
                $distance = 5;
                break;
            case 13:
                $distance = 2;
                break;
            case 14:
                $distance = 1;
                break;
            case 15:
                $distance = 0.5;
                break;
            case 16:
                $distance = 0.2;
                break;
            case 17:
                $distance = 0.1;
                break;
            case 18:
                $distance = 0.05;
                break;
            case 19:
                $distance = 0.02;
                break;
            default:
                $distance =0.5;
                break;
        }

       $distance = $distance * 4000;
        return $distance;
    }


    /**
     * 查询我的福包
     * @param ucode,pageindex,pagesize,(aid)
     */
    function GetmyPackage($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $limit = $countPage . ',' . $pageSize;

        // 获取所有福
        if (!empty($parr['aid'])) {
            $where['c_aid'] = $parr['aid'];
        }
        $where['c_type'] = 2;
        $where['c_state'] = 1;
        $ucode = $parr['ucode'];
        $field = "c_id as cpid,c_name,c_imgpath,c_pic1,c_pic2,(select c_num from t_collect_log where c_ucode='$ucode' and c_cpid=cpid) as num";
        $order = 'c_id asc';
        $list = M('Collect_prize')->where($where)->field($field)
              ->order($order)->limit($limit)->select();
        foreach ($list as $key => $value) {
            $list[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
            $list[$key]['c_pic1'] = GetHost().'/'.$value['c_pic1'];
            $list[$key]['c_pic2'] = GetHost().'/'.$value['c_pic2'];
            if ($value['num'] <= 0) {
                $list[$key]['num'] = 0;
            }
        }

        $count = M('Collect_prize')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 查询每日可兑物品
     * @param ucode
     */
    function GetDailyChange($parr)
    {
        $where['c_starttime'] = array('ELT', date('Y-m-d H:i:s'));
        $where['c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
        $field = 'c_aid,c_exchange_rule,c_pid';
        $changeinfo = M('Collect_dh')->where($where)->order('c_id desc')->field($field)->find();
        if (!$changeinfo) {
            return Message(2000, '今日没有相关兑换信息');
        }
        $spidarr = objarray_to_array(json_decode($changeinfo['c_exchange_rule']));

        //查询对应的奖项
        $prizewhere['c_state'] = 1;
        // $prizewhere['c_num'] = array('GT', 0);
        $prizewhere['c_id'] = $changeinfo['c_pid'];
        $prizewhere['c_aid'] = $changeinfo['c_aid'];
        $field = 'c_id,c_pcode,c_name,c_value,c_type,c_imgpath';
        $prizedata = M('Activity_prize')->where($prizewhere)->field($field)->find();
        if (!$prizedata) {
            return Message(2000, '今日奖项已兑换完');
        }
        $prizedata['c_imgpath'] = GetHost().'/'.$prizedata['c_imgpath'];

        $changesign = 0;
        foreach (array_values($spidarr) as $k => $v) {
            //查询所有兑换详情
            $field = "c_id,c_name,c_imgpath,c_pic1,c_pic2";
            $fuwhere['c_id'] = array_keys($spidarr)[$k];
            $fulist[$k] = M('Collect_prize')->where($fuwhere)->field($field)->find();
            $fulist[$k]['c_imgpath'] = GetHost().'/'.$fulist[$k]['c_imgpath'];
            $fulist[$k]['c_pic1'] = GetHost().'/'.$fulist[$k]['c_pic1'];
            $fulist[$k]['c_pic2'] = GetHost().'/'.$fulist[$k]['c_pic2'];
            $fulist[$k]['own'] = 0;
            $fulist[$k]['num'] = array_values($spidarr)[$k];

            //查询用户是否拥有对应数量
            $fulogwhere['c_ucode'] = $parr['ucode'];
            $fulogwhere['c_cpid'] = array_keys($spidarr)[$k];
            $fulog = M('Collect_log')->where($fulogwhere)->find();
            if ($fulog['c_num'] >= array_values($spidarr)[$k]) {
                $fulist[$k]['own'] = 1;
                $changesign++;
            }

        }

        $changeinfo['ischange'] = 0;
        if ($changesign == count($spidarr)) {
            $changeinfo['ischange'] = 1;
        }
        $changeinfo['fulist'] = $fulist;
        $changeinfo['prizedata'] = $prizedata;
        $changeinfo['c_exchange_rule'] = objarray_to_array(json_decode($changeinfo['c_exchange_rule']));

        return MessageInfo(0, '查询成功', $changeinfo);
    }

    /**
     * 兑换福
     * @param ucode(pid)
     */
    function ExchangeGift($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009,'请先登录再操作');
        }

        //查询今日兑换规则
        $where['c_starttime'] = array('ELT', date('Y-m-d H:i:s'));
        $where['c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
        if (!empty($parr['pid'])) {
            $where['c_pid'] = $parr['pid'];
        }
        $field = 'c_aid,c_exchange_rule,c_pid';
        $changeinfo = M('Collect_dh')->where($where)->order('c_id desc')->field($field)->find();
        if (!$changeinfo) {
            return Message(2000, '今日没有相关兑换信息');
        }
        $spidarr = objarray_to_array(json_decode($changeinfo['c_exchange_rule']));

        //查询活动
        $activitywhere['c_aid'] = $changeinfo['c_aid'];
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitytype'] = 18;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(3001,'该活动未开始或已结束');
        }

        //查询对应的奖项
        $prizewhere['c_state'] = 1;
        $prizewhere['c_num'] = array('GT', 0);
        $prizewhere['c_id'] = $changeinfo['c_pid'];
        $prizewhere['c_aid'] = $changeinfo['c_aid'];
        $field = '*';
        $prizedata = M('Activity_prize')->where($prizewhere)->field($field)->find();
        if (!$prizedata) {
            return Message(2000, '今日奖项已兑换完');
        }

        //查询领取记录
        if (!empty($parr['pid'])) {
            $plogwhere['c_aid'] = $changeinfo['c_aid'];
            $plogwhere['c_pid'] = $changeinfo['c_pid'];
            $plogwhere['c_ucode'] = $parr['ucode'];
            $result = M('Activity_log')->where($plogwhere)->find();
            if ($result) {
                return Message(2001, '您已兑换完该奖项');
            }
        }


        $db = M('');
        $db->startTrans();

        //循环扣除对应福
        $changesign = 0;
        foreach (array_values($spidarr) as $k => $v) {
            $fulogwhere['c_ucode'] = $parr['ucode'];
            $fulogwhere['c_cpid'] = array_keys($spidarr)[$k];
            $fulogwhere['c_num'] = array('EGT',array_values($spidarr)[$k]);
            $result = M('Collect_log')->where($fulogwhere)->setDec('c_num',array_values($spidarr)[$k]);
            if ($result) {
                $changesign++;
            }
        }

        if ($changesign != count($spidarr)) {
            $db->rollback();
            return Message(2003, '兑换失败');
        }

        //随机金额  写入中奖记录
        $movalue = rand($prizedata['c_bargainprice']*100,$prizedata['c_value']*100)/100;
        $prizedata['c_value'] = $movalue;
        $prizelogdata['c_ucode'] = $parr['ucode'];
        $prizelogdata['c_pcode'] = $prizedata['c_pcode'];
        $prizelogdata['c_aid'] = $prizedata['c_aid'];
        $prizelogdata['c_pid'] = $prizedata['c_id'];
        $prizelogdata['c_value'] = $prizedata['c_value'];
        $prizelogdata['c_type'] = $prizedata['c_type'];
        $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_log')->add($prizelogdata);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '记录写入失败');
        }

        if ($prizedata['c_type'] == 1) {
            // 写入用户余额
            $rebatemoney = IGD('Money', 'User');
            $moneyparr['ucode'] = $parr['ucode'];
            $moneyparr['money'] = $prizelogdata['c_value'];
            $moneyparr['source'] = 3;
            $moneyparr['key'] = $activitydata['c_activityname'];
            $moneyparr['desc'] = "您在".$activitydata['c_activityname']."中活动兑换金额";
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
        } else if ($prizedata['c_type'] == 2) {
            //进行转化订单操作
            $result = IGD('Actoder','Order')->CreataOrderInfo($parr['ucode'],$activitydata,$prizedata);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
            $prizedata['orderid'] = $result['data'];
        }

        //扣除奖品数量
        $result = M('Activity_prize')->where($prizewhere)->setDec('c_num',1);
        if (!$result) {
            $db->rollback();
            return Message(3010,'扣除奖项数量失败');
        }

        $db->commit();

        //给用户推送相应消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        if ($prizedata['c_type'] == 1) {
            $msgdata['title'] = '系统消息';
            $msgdata['type'] = 0;
            $msgdata['tag'] = 2;
            $msgdata['content'] = '您在'.$activitydata['c_activityname'].'中成功兑换金额￥' . $prizelogdata['c_value'] . '，已转入余额';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
        } else if ($prizedata['c_type'] == 2) {
            $msgdata['type'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $prizedata['orderid'];
            $msgdata['content'] = '您在'.$activitydata['c_activityname'].'中兑换一个奖品：' . $prizedata['c_name'] . '，已转入订单';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $prizedata['orderid'];
        }
        $msgresult = $Msgcentre->CreateMessege($msgdata);
        $prizedata['c_imgpath'] = GetHost().'/'.$prizedata['c_imgpath'];
        return MessageInfo(0,'兑换成功',$prizedata);
    }

    /**
     * 查询所有活动兑换物品
     * @param ucode,aid
     */
    function GetAllChange($parr)
    {
        // $where['c_starttime'] = array('ELT', date('Y-m-d H:i:s'));
        // $where['c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
        $where['c_aid'] = $parr['aid'];
        $field = 'c_aid,c_exchange_rule,c_pid';
        $changeinfo = M('Collect_dh')->where($where)->field($field)->select();
        if (!$changeinfo) {
            return Message(2000, '没有相关兑换信息');
        }

        $changecount = 0;
        foreach ($changeinfo as $key => $value) {
            $spidarr = objarray_to_array(json_decode($value['c_exchange_rule']));
            //查询对应的奖项
            $prizewhere['c_state'] = 1;
            $prizewhere['c_id'] = $value['c_pid'];
            $prizewhere['c_aid'] = $value['c_aid'];
            $field = 'c_id,c_pcode,c_name,c_value,c_type,c_imgpath';
            $prizedata = M('Activity_prize')->where($prizewhere)->field($field)->find();
            if (!$prizedata) {
                return Message(2000, '今日奖项已兑换完');
            }
            $prizedata['c_imgpath'] = GetHost().'/'.$prizedata['c_imgpath'];

            //查询奖项是否兑换
            $prizedata['isget'] = 0;
            $plogwhere['c_ucode'] = $parr['ucode'];
            $plogwhere['c_aid'] = $value['c_aid'];
            $plogwhere['c_pid'] = $value['c_pid'];
            $prizelogdata = M('Activity_log')->where($plogwhere)->find();
            if ($prizelogdata) {
                $prizedata['isget'] = 1;
                $changecount++;
            }

            $changesign = 0;
            foreach (array_values($spidarr) as $k => $v) {
                //查询所有兑换详情
                $field = "c_id,c_name,c_imgpath,c_pic1,c_pic2";
                $fuwhere['c_id'] = array_keys($spidarr)[$k];
                $fulist[$k] = M('Collect_prize')->where($fuwhere)->field($field)->find();
                $fulist[$k]['c_imgpath'] = GetHost().'/'.$fulist[$k]['c_imgpath'];
                $fulist[$k]['c_pic1'] = GetHost().'/'.$fulist[$k]['c_pic1'];
                $fulist[$k]['c_pic2'] = GetHost().'/'.$fulist[$k]['c_pic2'];
                $fulist[$k]['own'] = 0;
                $fulist[$k]['num'] = array_values($spidarr)[$k];

                //查询用户是否拥有对应数量
                $fulogwhere['c_ucode'] = $parr['ucode'];
                $fulogwhere['c_cpid'] = array_keys($spidarr)[$k];
                $fulog = M('Collect_log')->where($fulogwhere)->find();
                if ($fulog['c_num'] >= array_values($spidarr)[$k]) {
                    $fulist[$k]['own'] = 1;
                    $changesign++;
                }

            }

            $changeinfo[$key]['ischange'] = 0;
            if ($changesign == count($spidarr)) {
                $changeinfo[$key]['ischange'] = 1;
            }
            $changeinfo[$key]['fulist'] = $fulist;
            $changeinfo[$key]['prizedata'] = $prizedata;
            $changeinfo[$key]['c_exchange_rule'] = objarray_to_array(json_decode($value['c_exchange_rule']));
        }

        $data['changeinfo'] = $changeinfo;
        $data['ischange'] = 0;
        if ($changecount >= count($changeinfo)) {
            $data['ischange'] = 1;
        }

        return MessageInfo(0, '查询成功', $data);
    }

}
