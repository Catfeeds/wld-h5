<?php

/**
 * 首页活动数据交互相关接口
 */
class IndexNewact {

    /**
     * 查询店单个平台发起的活动详情
     * @param rule[20店铺红包,21店铺券,22宝箱,23气球,24购物抽奖,25转盘,26拼团,27砍价,28抢购,29商家福利]
     * @param sign[1不需要判断时间,2需要判断时间]
     */
    function GetPlathavingAct($rule,$sign)
    {
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitytype'] = $rule;     //活动规则
        if ($sign != 1) {
            $activitywhere['c_activitystarttime'] = array('ELT', gdtime());
            $activitywhere['c_activityendtime'] = array('EGT', gdtime());
        }
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(3002, '平台尚未开放此活动！');
        }

        return MessageInfo(0,'查询成功',$activitydata);
    }

    /**
     * 查询单个平台活动详情
     * @param
     */
    function GetPlatActInfo($rule,$sign)
    {
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitytype'] = $rule;     //活动规则
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(3002, '平台尚未开放此活动！');
        }

        $where[] = array("c_acode is null or c_acode=''");
        $where['c_aid'] = $activitydata['c_id'];
        $where['c_state'] = 1;
        $where['c_activitytype'] = $rule;     //活动规则
        if ($sign != 1) {
            $where['c_activitystarttime'] = array('ELT', gdtime());
            $where['c_activityendtime'] = array('EGT', gdtime());
        }
        
        $data = M('Actjoin_moneylog')->where($where)->order('c_id desc')->find();
        if (!$data) {
            return Message(3000, '活动信息不存在！');
        }
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 查询单个商家活动详情
     * @param aid,acode,sign[1不需要判断时间,2需要判断时间]
     */
    function GetShopActivity($sign,$joinaid,$aid,$acode)
    {
        if ($joinaid) {
            $where['c_id'] = $joinaid;
        } else {
            $where['c_aid'] = $aid;
            $where['c_acode'] = $acode;   
        }
        $where['c_state'] = 1;
        if ($sign != 1) {
            $where['c_activitystarttime'] = array('ELT', gdtime());
            $where['c_activityendtime'] = array('EGT', gdtime());
        }
        
        $data = M('Actjoin_moneylog')->where($where)->order('c_id desc')->find();
        if (!$data) {
            return Message(3000, '活动信息不存在！');
        }
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 查询商家可发起的活动列表
     * @param pageindex,pagesize
     */
    function JoinActivityList($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $activitywhere['c_sign'] = 2;
        $activitywhere['c_state'] = 1;
        $activitywhere['c_show'] = 1;
        $activitydata = M('Activity')->where($activitywhere)->limit($countPage, $pageSize)->order('c_id desc')->select();
        if (!$activitydata) {
            return Message(3000, '平台活动数据为空！');
        }

        foreach ($activitydata as $key => $value) {
            $activitydata[$key]['c_listimg'] = isset($value['c_listimg'])?$value['c_listimg']:GetHost() . '/' . $value['c_listimg'];
            $activitydata[$key]['c_pimg'] = isset($value['c_pimg'])?$value['c_pimg']:GetHost() . '/' . $value['c_pimg'];
            $activitydata[$key]['c_img'] = isset($value['c_img'])?$value['c_img']:GetHost() . '/' . $value['c_img'];
        }

        $count = M('Activity')->where($activitywhere)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$activitydata);
    }

    /**
     * 查询商家活动中心列表
     * @param ucode,pageindex,pagesize
     */
    function ShopActivityList($parr)
    {
        $acode = $parr['ucode'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where[] = array("c_acode is null or c_acode='$acode' or c_acode=''");
        $where['c_state'] = 1;
        $where['c_show'] = 1;
        $aidstr = '20,21,26,27,28,29';
        $where[] = array("c_activitytype>20 and c_activitytype not in ('$aidstr')");
        $limit = $countPage.','.$pageSize;        
        $order = 'case when ifnull(c_acode,"")="" then 0 else 1 end asc,c_istop desc,c_ishot desc,c_activityendtime desc';
        $list = M('Actjoin_moneylog')->where($where)->limit($limit)->order($order)->group('c_activitytype')->select();

        $count = M('Actjoin_moneylog')->where($where)->group('c_activitytype')->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
            if (strtotime($value['c_activitystarttime']) > time()) {
                $list[$key]['progress'] = 0;    //未开始
            } else if (strtotime($value['c_activitystarttime']) <= time() && strtotime($value['c_activityendtime']) >= time()) {
                $list[$key]['progress'] = 1;    //进行中
            } else {
                $list[$key]['progress'] = 2;    //已结束
            }

            $list[$key]['c_listimg'] = GetHost() . '/' . $value['c_listimg'];
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            // 20店铺红包,21店铺券,22宝箱,23气球,24购物抽奖,25转盘,26拼团,27砍价,28抢购,29商家福利
            switch ($value['c_activitytype']) {                
                case '22':  //宝箱
                    $list[$key]['remind'] = '该商家已将宝箱投放至发现首页';
                    $list[$key]['url'] = '';
                    break;
                case '23':  //气球
                    $list[$key]['remind'] = '该商家已将热气球投放至发现首页';
                    $list[$key]['url'] = '';
                    break;
                case '24': //购物抽奖
                    $list[$key]['remind'] = '';
                    $list[$key]['url'] = GetHost(1).'/index.php/Activity/Index/lottery';
                    break;
                case '25':  //转盘
                    $list[$key]['remind'] = '';
                    $list[$key]['url'] = GetHost(1).'/index.php/Activity/Index/roulette';
                    break;
                default:
                    $list[$key]['remind'] = '';
                    $list[$key]['url'] = '';
                    break;
            }
        }

        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 新增领取记录
     * @param ucode,pid,orderid,joinaid,awid,acode,name,img,value,marks,type,state
     */
    function WriteReciveLog($parr)
    {
        $log['c_ucode'] = $parr['ucode'];
        $log['c_pid'] = $parr['pid'];
        $log['c_orderid'] = $parr['orderid'];
        $log['c_joinaid'] = $parr['joinaid'];
        $log['c_awid'] = $parr['awid'];
        $log['c_acode'] = $parr['acode'];
        $log['c_name'] = $parr['name'];
        $log['c_img'] = $parr['img'];
        $log['c_value'] = $parr['value'];
        $log['c_marks'] = $parr['marks'];
        $log['c_type'] = $parr['type'];
        $log['c_state'] = $parr['state'];
        $log['c_addtime'] = gdtime();
        $result = M('A_actlog')->add($log);
        if (!$result) {
            return Message(3003, '领取记录添加失败！');
        }
        return Message(0, '领取记录成功！');
    }

    /**
     * 新增红包领取记录
     * @param ucode,pid,orderid,joinaid,awid,acode,name,img,value,marks,type,state
     */
    function WriteRedReciveLog($parr)
    {
        $log['c_ucode'] = $parr['ucode'];
        $log['c_pid'] = $parr['pid'];
        $log['c_orderid'] = $parr['orderid'];
        $log['c_joinaid'] = $parr['joinaid'];
        $log['c_awid'] = $parr['awid'];
        $log['c_acode'] = $parr['acode'];
        $log['c_name'] = $parr['name'];
        $log['c_img'] = $parr['img'];
        $log['c_value'] = $parr['value'];
        $log['c_marks'] = $parr['marks'];
        $log['c_type'] = $parr['type'];
        $log['c_state'] = $parr['state'];
        $log['c_addtime'] = gdtime();
        $result = M('A_actredlog')->add($log);
        if (!$result) {
            return Message(3003, '领取记录添加失败！');
        }
        return Message(0, '领取记录成功！');
    }

    /**
     * 领取宝箱
     * @param joinaid,acode,ucode,longitude,latitude
     */
    function ReceiveBox($parr)
    {
        $acode = $parr['acode'];
        $ucode = $parr['ucode'];
        $joinaid = $parr['joinaid'];
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        //查询宝箱活动
        $actwhere['c_id'] = $joinaid;
        $actwhere['c_state'] = 1;
        $actwhere['c_activitytype'] = 22;
        $actdata = M('Actjoin_moneylog')->where($actwhere)->find();
        if (!$actdata) {
            return Message(3000, '没有相关数据');
        }

        //查询宝箱出现记录
        $stw['c_ucode'] = $ucode;
        $stw['c_type'] = 2;
        $stw['c_acode'] = $acode;
        $stw['c_status'] = 0;
        $startlog = M('A_start_log')->where($stw)->find();
        if (!$startlog) {
            return Message(3001, '没有相关发现记录');
        }

        //获取活动相关配置
        $clickconf = IGD('Redis', 'Redis')->Gethash('newact')['data'];
     
        $db = M('');
        $db->startTrans();
        $sign = '1';
        if ($clickconf['airrand']) {
            $randnum = rand(1, 100);
            $airrand = explode('|', $clickconf['airrand']);
            $rand_arr2 = explode('-', $airrand[0]);
            $rand_arr3 = explode('-', $airrand[1]);
            $rand_arr4 = explode('-', $airrand[2]);
            //根据概率领取奖项
            $rsign = 0;     //获奖标志
            if ($randnum > $rand_arr2[0] && $randnum <= $rand_arr2[1]) {  //红包
                $result = $this->ReceiveMoney($parr,$actdata);$rsign = 1;$sign = '2';
            } else if ($randnum > $rand_arr3[0] && $randnum <= $rand_arr3[1]) {  //卡劵
                $result = $this->ReceiveCard($parr,$actdata);$rsign = 1;$sign = '3';
            } else if ($randnum > $rand_arr4[0] && $randnum <= $rand_arr4[1]) {  //商品
                $result = $this->ReceiveGoods($parr,$actdata);$rsign = 1;$sign = '4';
            }
            if ($result['code'] != 0 && $rsign == 1) {
                $db->rollback();
                return $result;
            }
            $prizedata = $result['data'];
        }

        if (empty($prizedata)) {
            $sign = '1';
            //获取奖项
            $prizewhere['c_type'] = 1;
            $prizewhere['c_status'] = 1;
            $prizewhere['c_delete'] = 2;
            $prizewhere['c_joinaid'] = $actdata['c_id'];
            $prizedata = M('A_actprize')->where($prizewhere)->order('rand()')->find();
            if (!$prizedata) {
                $db->rollback();
                return Message(3000, '没有相关数据');
            }
        }

        //改变发现表领取状态
        if ($sign != 4) {
            $logsave['c_status'] = 3;
            $logsave['c_ptype'] = $sign;
            $logsave['c_awid'] = $prizedata['c_id'];
            $logsave['c_value'] = $prizedata['c_value'];
            $logsave['c_maxvalue'] = $prizedata['c_maxvalue'];
            $logsave['c_pcode'] = $prizedata['c_pid'];
            $logsave['c_receivetime'] = gdtime();
            $logsave['c_updatetime'] = gdtime();
            $result = M('A_start_log')->where($stw)->save($logsave);
            if (!$result) {
                return Message(3001, '领取失败');
            }
        }

        $db->commit();

        $retarr = $this->CombineData($parr,$sign,$prizedata);
        return MessageInfo(0,'领取成功',$retarr);
    }

     /**
     * 领取气球
     * @param joinaid,acode,ucode,longitude,latitude
     */
    function ReceiveAir($parr)
    {
        $acode = $parr['acode'];
        $ucode = $parr['ucode'];
        $joinaid = $parr['joinaid'];
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        //查询宝箱活动
        $actwhere['c_id'] = $joinaid;
        $actwhere['c_state'] = 1;
        $actwhere['c_activitytype'] = 23;
        $actdata = M('Actjoin_moneylog')->where($actwhere)->find();
        if (!$actdata) {
            return Message(3000, '没有相关数据');
        }

        //查询气球出现记录
        $stw['c_ucode'] = $ucode;
        $stw['c_type'] = 3;
        $stw['c_acode'] = $acode;
        $stw['c_status'] = 0;
        $startlog = M('A_start_log')->where($stw)->order('c_id desc')->find();
        if (!$startlog) {
            return Message(3001, '没有相关发现记录');
        }

        //获取活动相关配置
        $clickconf = IGD('Redis', 'Redis')->Gethash('newact')['data'];

        $db = M('');
        $db->startTrans();
        $sign = '1';
        if ($clickconf['boxrand']) {
            $randnum = rand(1, 100);
            $boxrand = explode('|', $clickconf['boxrand']);
            $rand_arr2 = explode('-', $boxrand[0]);
            $rand_arr3 = explode('-', $boxrand[1]);
            $rand_arr4 = explode('-', $boxrand[2]);
            //根据概率领取奖项
            $rsign = 0;     //获奖标志
            if ($randnum > $rand_arr2[0] && $randnum <= $rand_arr2[1]) {  //红包
                $result = $this->ReceiveMoney($parr,$actdata);$rsign = 1;$sign = '2';
            } else if ($randnum > $rand_arr3[0] && $randnum <= $rand_arr3[1]) {  //卡劵
                $result = $this->ReceiveCard($parr,$actdata);$rsign = 1;$sign = '3';
            } else if ($randnum > $rand_arr4[0] && $randnum <= $rand_arr4[1]) {  //商品
                $result = $this->ReceiveGoods($parr,$actdata);$rsign = 1;$sign = '4';
            }
            if ($result['code'] != 0 && $rsign == 1) {
                $db->rollback();
                return $result;
            }
            $prizedata = $result['data'];
        }

        if (empty($prizedata)) {
            $sign = '1';
            //获取奖项
            $prizewhere['c_type'] = 1;
            $prizewhere['c_status'] = 1;
            $prizewhere['c_delete'] = 2;
            $prizewhere['c_joinaid'] = $actdata['c_id'];
            $prizedata = M('A_actprize')->where($prizewhere)->order('rand()')->find();
            if (!$prizedata) {
                $db->rollback();
                return Message(3000, '没有相关数据');
            }
        }

        //改变发现表领取状态
        if ($sign != 4) {
            $logsave['c_status'] = 3;
            $logsave['c_ptype'] = $sign;
            $logsave['c_awid'] = $prizedata['c_id'];
            $logsave['c_value'] = $prizedata['c_value'];
            $logsave['c_maxvalue'] = $prizedata['c_maxvalue'];
            $logsave['c_pcode'] = $prizedata['c_pid'];
            $logsave['c_receivetime'] = gdtime();
            $logsave['c_updatetime'] = gdtime();
            $result = M('A_start_log')->where($stw)->save($logsave);
            if (!$result) {
                return Message(3001, '领取失败');
            }
        }
        
        $db->commit();

        $retarr = $this->CombineData($parr,$sign,$prizedata);
        return MessageInfo(0,'领取成功',$retarr);
    }

    //组装返回数据
    function CombineData($parr,$sign,$prizedata)
    {
        $acode = $parr['acode'];
        $ucode = $parr['ucode'];
        $joinaid = $parr['joinaid'];
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        $uw['c_ucode'] = $acode;
        $userinfo = M('Users')->where($uw)->find();
        $userinfo['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

        //组装返回数据
        $retarr['rightact'] = '3';
        $retarr['rightkey'] = '13';               
        switch ($sign) {
            case '1':   //空奖
                $retarr['ktype'] = '';         
                $retarr['title'] = '';
                $retarr['rightname'] = '进店逛逛';
                $retarr['signature'] = $userinfo['c_signature'];
                $retarr['img'] = '';
                $retarr['money'] = $prizedata['c_value'];
                $retarr['gsname'] = $prizedata['c_name'];
                $retarr['times'] = '';
                break;
            case '2':   //红包
                $retarr['title'] = '恭喜您获得一个现金红包';
                $retarr['rightname'] = '进店逛逛';
                $retarr['signature'] = $userinfo['c_signature'];
                $retarr['img'] = '';
                $retarr['money'] = $prizedata['c_value'];
                $retarr['gsname'] = $prizedata['c_name'];
                $retarr['times'] = '';
                break;
            case '3':   //卡劵
                $retarr['ktype'] = $prizedata['c_marks'];
                $retarr['title'] = '恭喜您获得一张卡劵';
                $retarr['rightname'] = '进店使用';
                $retarr['signature'] = 'APP专属';
                $retarr['img'] = '';
                if ($prizedata['c_marks'] == 1) {
                    $retarr['money'] = '￥'.round($prizedata['c_value']);
                    $retarr['gsname'] = '满'.round($prizedata['c_maxvalue']).'元可用';
                } else {
                    $retarr['money'] = round($prizedata['c_value'],1).'折';
                    $retarr['gsname'] = '最高折扣'.round($prizedata['c_maxvalue']).'元';
                }
                $retarr['times'] = '有效期'.date('Y.m.d',strtotime($prizedata['c_starttime'])).'-'.date('Y.m.d',strtotime($prizedata['c_endtime']));
                break;
            case '4':   //实物
                $retarr['title'] = '恭喜您获得一个可领取的商品';
                $retarr['rightname'] = '进店领取';
                $retarr['signature'] = $userinfo['c_signature'];
                $retarr['img'] =  GetHost().'/'.$prizedata['c_img'];
                $retarr['money'] = $prizedata['c_maxvalue'];
                $retarr['gsname'] = $prizedata['c_name'];
                $retarr['times'] = '兑换截至:'.date('Y-m-d H:i:s');
                $retarr['rightact'] = '3';
                $retarr['rightkey'] = '35';
                $retarr['ktype'] = $prizedata['exchangeid'];
                break;
            default:
                break;
        }

        $retarr['sign'] = $sign;    
        $retarr['keyvalue'] = $userinfo['c_ucode'];    
        $retarr['nickname'] = $userinfo['c_nickname'];
        $retarr['headimg'] = $userinfo['c_headimg'];        
        $retarr['distance'] = IGD('Start','Newact')->plandistance($longitude, $latitude, $userinfo['c_longitude1'], $userinfo['c_latitude1']);
        if (empty($userinfo['c_address1'])) {
            $retarr['address'] = "该用户正在附近潜水，点TA看看";
        } else {
            $retarr['address'] = $userinfo['c_address1'];
        }
        $retarr['leftname'] = '继续寻宝';
        $retarr['leftact'] = '3';
        $retarr['leftkey'] = '10000';
        return $retarr;
    }

    /**
     * 领取卡劵操作
     * @param actdata,ucode,acode
     */
    function ReceiveCard($parr,$actdata)
    {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        //查询卡劵奖项信息
        $prizewhere['c_joinaid'] = $actdata['c_id'];
        $prizewhere['c_type'] = 3;
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;
        $prizedata = M('A_actprize')->where($prizewhere)->order('rand()')->find();
        if (!$prizedata) {
            return Message(0, '没有相关数据');
        }

        //领取卡劵
        if ($prizedata['c_pid']) {
            $parr['cid'] = $prizedata['c_pid'];
            $parr['joinaid'] = $prizedata['c_joinaid'];
            $parr['remark'] = $prizedata['c_remark'];
            $result = IGD('Coupon','Newact')->ReceiveCoupon($parr);
            if ($result['code'] != 0) {
                return $result;
            }
        } else {
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
                return Message(3002,'领取失败');
            }
        }

        //写入领取记录
        $parr['joinaid'] = $prizedata['c_joinaid'];
        $parr['awid'] = $prizedata['c_id'];
        $parr['pid'] = $prizedata['c_pid'];
        $parr['acode'] = $acode;
        $parr['name'] = $prizedata['c_name'];
        $parr['img'] = $prizedata['c_img'];
        $parr['value'] = $prizedata['c_value'];
        $parr['marks'] = $prizedata['c_remark'];
        $parr['type'] = 3;
        $parr['state'] = 1;
        $result = $this->WriteReciveLog($parr);
        if ($result['code'] != 0) {
            return $result;
        }

        $prizewhere['c_id'] = $prizedata['c_id'];
        $result = M('A_actprize')->where($prizewhere)->setDec('c_num',1);
        if (!$result) {
            return Message(3002, '扣除奖项剩余数量失败');
        }

        //写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 27;
        $msgdata['content'] = '您发现的：'.$prizedata['c_name'].'，领取成功已转入卡劵包';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgresult = $Msgcentre->CreateMessege($msgdata);

        return MessageInfo(0,'领取成功',$prizedata);
    }

    /**
     * 领取红包操作
     * @param actdata,ucode,acode
     */
    function ReceiveMoney($parr,$actdata)
    {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        $prizewhere['c_joinaid'] = $actdata['c_id'];
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;        
        $prizedata = M('A_redprize')->where($prizewhere)->order('rand()')->find();
        if (!$prizedata) {
            return Message(0, '红包已被抢光，进店逛逛吧~');
        }
        
        //计算红包金额
        if ($prizedata['c_type'] == 2) {
            $money_arr = IGD('Red','Newact')->randBonus($prizedata['c_remain_money'],$prizedata['c_num'],1);
            $money = $money_arr[0];
        } else if ($prizedata['c_type'] == 1) {
            $money = sprintf('%.2f',$prizedata['c_money']/$prizedata['c_totalnum']);
        } else {
            //查询商家红包
            $money = rand($prizedata['c_value']*100,$prizedata['c_maxvalue']*100)/100;
        }

        //写入领取记录
        $parr['joinaid'] = $prizedata['c_joinaid'];
        $parr['pid'] = $prizedata['c_pid'];
        $parr['awid'] = $prizedata['c_id'];
        $parr['acode'] = $acode;
        $parr['name'] = $prizedata['c_name'];
        $parr['img'] = $prizedata['c_img'];
        $parr['value'] = $money;
        $parr['marks'] = $prizedata['c_remark'];
        $parr['type'] = 2;
        $parr['state'] = 1;
        $result = $this->WriteRedReciveLog($parr);
        if ($result['code'] != 0) {
            return $result;
        }

        //扣除奖项数量
        $prizewhere['c_id'] = $prizedata['c_id'];
        $prisave['c_remain_money'] = $prizedata['c_remain_money'] - $money;
        $prisave['c_num'] = $prizedata['c_num'] - 1;
        $result = M('A_redprize')->where($prizewhere)->save($prisave);
        if (!$result) {
            return Message(3002, '扣除奖项剩余数量失败');
        }

        if ($prizedata['c_pid']) {
            //修改红包信息
            $result = IGD('Red','Newact')->DecCouponCard($prizedata['c_pid'],1,1);
            if($result['code'] != 0){
                return Message(1001,"扣除红包数量失败");
            }

            //扣除红包管理总金额
            $redwhere['c_id'] = $prizedata['c_pid'];
            $result = M('A_actred')->where($redwhere)->setDec('c_remain_money',$money);
            if(!$result){
                return Message(1001,"扣除红包总金额失败");
            }   
        }

        if ($actdata['c_activitytype'] == 22) {
            $showsign = '宝箱';
        } else if ($actdata['c_activitytype'] == 23) {
            $showsign = '热气球';
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
        $param['showtext'] = '领取'.$showsign.'红包';    
        $result = IGD('Money','User')->OptionMoney($param);
        if($result['code'] != 0){
            return Message(3002,'修改用户余额失败');
        }

        //写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您发现的'.$showsign.'红包金额为￥' . $money . '，领取成功已转入余额';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgresult = $Msgcentre->CreateMessege($msgdata);

        $prizedata['c_value'] = $money;
        return MessageInfo(0,'领取成功',$prizedata);
    }

    /**
     * 领取实物（商品）操作
     * @param actdata,ucode,acode
     */
    function ReceiveGoods($parr,$actdata)
    {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        //查询商品奖项信息
        $prizewhere['c_joinaid'] = $actdata['c_id'];
        $prizewhere[] = array('c_pid is not null');
        $prizewhere['c_type'] = 4;
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;
        $prizedata = M('A_actprize')->where($prizewhere)->order('rand()')->find();
        if (!$prizedata) {
            return Message(0, '没有相关数据');
        }

        if ($actdata['c_activitytype'] == 22) {
            $type = 2;
        } else if ($actdata['c_activitytype'] == 23) {
            $type = 3;
        }

        //记录实物奖项
        $stw['c_ucode'] = $ucode;
        $stw['c_acode'] = $acode;
        $stw['c_type'] = $type;
        $stw['c_status'] = 0;
        $startlog = M('A_start_log')->where($stw)->order('c_id desc')->find();
        if (!$startlog) {
            return Message(0, '记录不存在');
        }

        $stw['c_id'] = $startlog['c_id'];
        $stsave['c_ptype'] = 4;
        $stsave['c_pcode'] = $prizedata['c_pid'];
        $stsave['c_awid'] = $prizedata['c_id'];
        $stsave['c_value'] = $prizedata['c_value'];
        $stsave['c_maxvalue'] = $prizedata['c_maxvalue'];
        $stsave['c_updatetime'] = gdtime();
        $result = M('A_start_log')->where($stw)->save($stsave);
        if (!$prizedata) {
            return Message(3000, '领取失败');
        }

        $prizedata['exchangeid'] = $startlog['c_id'];
        return MessageInfo(0,'领取成功',$prizedata);
    }

}