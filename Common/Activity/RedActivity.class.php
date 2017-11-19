<?php

/**
 * 	商家红包活动
 *
 */
class RedActivity {

	/**
	 *  添加商家红包
	 *  @param c_ucode,c_pcode,money
	 */
	function AddRedNum($parr,$acode)
	{
		//查询红包活动
        $result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return Message(3000,'活动还没有开始');
        }
        $activitydata = $result['data'];

        //判断均分金额
        if ($parr['money'] <= 0.5) {
            $num = 1;
        } else if ($parr['money'] > 0.5 && $parr['money'] <= 1) {
            $num = 2;
        } else if ($parr['money'] > 1 && $parr['money'] <= 2) {
            $num = 3;
        } else if ($parr['money'] > 2 && $parr['money'] <= 5) {
            $num = 4;
        } else if ($parr['money'] > 5 && $parr['money'] <= 10) {
            $num = 5;
        } else if ($parr['money'] > 10 && $parr['money'] <= 20) {
            $num = 6;
        } else if ($parr['money'] > 20 && $parr['money'] <= 30) {
            $num = 7;
        } else if ($parr['money'] > 30 && $parr['money'] <= 50) {
            $num = 8;
        } else if ($parr['money'] > 50 && $parr['money'] <= 70) {
            $num = 10;
        } else if ($parr['money'] > 70 && $parr['money'] <= 100) {
            $num = 12;
        } else if ($parr['money'] > 100 && $parr['money'] <= 200)  {
            $num = 15;
        } else if ($parr['money'] > 200 && $parr['money'] <= 400)  {
            $num = 25;
        } else {
            $num = 50;
        }

        $money = bcdiv($parr['money'], $num, 2);

        // 增加现金奖品
        $addinfo['c_joinaid'] = $activitydata['c_id'];
        if (empty($parr['c_pcode'])) {
            $addinfo['c_remark'] = '扫码支付';
            $addinfo['c_scode'] = $acode;
            $addinfo['c_name'] = $parr['c_pname'].',商家红包';
        } else {
            $addinfo['c_remark'] = '普通消费';
            $addinfo['c_scode'] = $parr['c_ucode'];
            $addinfo['c_pid'] = $parr['c_pcode'];
            $addinfo['c_name'] = '产品:'.$parr['c_pname'].',商家红包';
        }

        $addinfo['c_money'] = $parr['money'];
        $addinfo['c_remain_money'] = $parr['money'];
        $addinfo['c_value'] = $money;
        $addinfo['c_maxvalue'] = $money;
        $addinfo['c_totalnum'] = $num;
        $addinfo['c_num'] = $num;
        $addinfo['c_status'] = 1;
        $addinfo['c_marks'] = 1;
        $addinfo['c_type'] = 1;
        $addinfo['c_img'] = $parr['c_pimg'];
        $addinfo['c_addtime'] = gdtime();
        $result = M('A_redprize')->add($addinfo);
        if (!$result) {
            return Message(1026, "添加商家红包失败");
        }

        // $userwhere['c_ucode'] = $acode;
        // $shopname = M('Users')->where($userwhere)->getField('c_nickname');
        // //添加公告消息
        // $content = '在小蜜，发现【'.$shopname.'】送出的商家红包';
        // $weburl = GetHost(1).'/index.php/Home/Fullmoon/redinfo?aid='.$activitydata['c_id'].'&pcode='.$parr['c_pcode'].'&ucode='.$parr['c_ucode'].'&acode='.$acode;
        // $Msgcentre = D('Msgcentre', 'Service');
        // $msgdata['type'] = 0;
        // $msgdata['platform'] = 1;
        // $msgdata['sendnum'] = 1;
        // $msgdata['title'] = '系统消息';
        // $msgdata['content'] = $content;
        // $msgdata['tag'] = 2;
        // $msgdata['weburl'] = $weburl;
        // $msgdata['tagvalue'] = $weburl;
        // $Msgcentre->CreateMessegeInfo($msgdata);

        return Message(0,'添加成功');
	}

    /**
     * 产品商家红包信息详情
     * @return ucode,aid,pid,fromucode
     */
    function GetRedInfo($parr)
    {
        // 查询奖品
        $prizewhere['c_id'] = $parr['pid'];
        $prizewhere['c_aid'] = $parr['aid'];
        $prizedata = M('Activity_prize')->where($prizewhere)->find();

        //查询产品
        $producewhere['c_pcode'] = $prizedata['c_pcode'];
        $prizedata['produce'] = M('Product')->where($producewhere)->find();
        if ($prizedata['produce']) {
            $shopucode = $prizedata['produce']['c_ucode'];
            $prizedata['produce']['c_pimg'] = GetHost().'/'.$prizedata['produce']['c_pimg'];
        } else {
            $shopucode = $prizedata['c_acode'];
        }

        //查询商家信息
        $userwhere['u.c_ucode'] = $shopucode;
        $join = 'left join t_users_date as d on d.c_ucode=u.c_ucode';
        $field = 'u.c_ucode,u.c_headimg,u.c_nickname,u.c_signature,u.c_isagent,u.c_shop,d.c_pv,d.c_attention';
        $prizedata['shopinfo'] = M('Users as u')->join($join)->where($userwhere)->field($field)->find();
        $prizedata['shopinfo']['c_headimg'] = GetHost().'/'. $prizedata['shopinfo']['c_headimg'];
        $prizedata['shopinfo']['c_pv'] = isset($prizedata['shopinfo']['c_pv'])?$prizedata['shopinfo']['c_pv']:0;
        $prizedata['shopinfo']['c_attention'] = isset($prizedata['shopinfo']['c_attention'])?$prizedata['shopinfo']['c_attention']:0;
        $prizedata['shopinfo']['is_attention'] = D('Resourcev2','Service')->is_attention($parr['ucode'],$prizedata['shopinfo']['c_ucode']);

        $prizedata['recivename'] = M('Users')->where(array('c_ucode'=>$parr['fromucode']))->getField('c_nickname');
        //查询红包领取总个数
        $rednumwhere['c_type'] = 1;
        $rednumwhere['c_aid'] = $parr['aid'];
        $prizedata['rednum'] = 5*M('Activity_log')->where($rednumwhere)->count();
        return MessageInfo(0,'查询成功',$prizedata);
    }

    /**
     * 领取商家红包
     * @param ucode
     */
    function RecieveRed($parr)
    {
        $redstate = cookie('redstate');
        if ($redstate == 1) {
            return Message(2000,'请从小蜜中继续发现');
        }
        //查询活动
        $activitywhere['c_show'] = 1;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 6;
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(2000,'商家红包活动未开始');
        }

        $aconf = D('Redis', 'Service')->Gethash('activity')['data'];
        $redstatus = $aconf['redstatus'];
        if ($redstatus != 1) {
            return Message(2000,'商家红包还在积累中未发放');
        }

        //查询用户是否有记录可领取
        $where['c_sign'] = 2;
        $where['c_ucode'] = $parr['ucode'];
        $findlog = M('Activity_findlog1')->where($where)->order('c_id desc')->find();
        if(!$findlog) {
            return Message(2001, '请从小蜜中继续发现');
        }

        // 查询是否领取过红包
        $findlogwhere['c_ucode'] = $parr['ucode'];
        $findlogwhere['c_fid'] = $findlog['c_id'];
        $result = M('Activity_log')->where($findlogwhere)->count();
        if($result != 0) {
            return Message(2001, '您已经领取过该红包');
        }

        // 查询可领取红包
        $prizewhere['c_type'] = 1;
        $prizewhere['c_aid'] = $activitydata['c_id'];
        $prizewhere['c_state'] = 1;
        $prizewhere['c_num'] = array('GT', 0);
        $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
        if (!$prizedata) {
            return Message(2004,'商家红包已被抢光');
        }

        //查询用户是否已领取同样商家红包
        // $fanwhere['c_ucode'] = $parr['ucode'];
        // $fanwhere['c_pid'] = $prizedata['c_id'];
        // $faninfo = M('Activity_log')->where($fanwhere)->find();
        // if ($faninfo) {
        //     return Message(2004,'商家红包已被抢光');
        // }

        $db = M('');
        $db->startTrans();

        //开始领取红包
        $addlog['c_ucode'] = $parr['ucode'];
        $addlog['c_aid'] = $activitydata['c_id'];
        $addlog['c_pid'] = $prizedata['c_id'];
        $addlog['c_fid'] = $findlog['c_id'];
        $addlog['c_pcode'] = $prizedata['c_pcode'];
        $addlog['c_value'] = $prizedata['c_value'];
        $addlog['c_type'] = $prizedata['c_type'];
        $addlog['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Activity_log')->add($addlog);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1029, "领取红包失败，请稍后再试");
        }

        $regionid = $result;

        //扣除剩余数量
        $whereprize['c_id'] = $prizedata['c_id'];
        $whereprize['c_aid'] = $activitydata['c_id'];
        $whereprize['c_num'] = array('GT', 0);
        $result = M('Activity_prize')->where($whereprize)->setDec('c_num', 1);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1030, "领取红包失败，请稍后再试");
        }

        // 写入用户余额
        $rebatemoney = D('Money', 'User');
        $moneyparr['ucode'] = $parr['ucode'];
        $moneyparr['money'] = $prizedata['c_value'];
        $moneyparr['source'] = 3;
        $moneyparr['key'] = $activitydata['c_activityname'];
        $moneyparr['desc'] = "您在点击发现活动中，发现商家红包并领取金额";
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

        //查询产品信息
        $producewhere['c_pcode'] = $prizedata['c_pcode'];
        $prizedata['produce'] = M('Product')->where($producewhere)->find();
        if ($prizedata['produce']) {
            $shopucode = $prizedata['produce']['c_ucode'];
            $prizedata['produce']['c_pimg'] = GetHost().'/'.$prizedata['produce']['c_pimg'];
        } else {
            $shopucode = $prizedata['c_acode'];
        }

        $userwhere['u.c_ucode'] = $shopucode;
        $join = 'left join t_users_date as d on d.c_ucode=u.c_ucode';
        $field = 'u.c_ucode,u.c_headimg,u.c_nickname,u.c_signature,u.c_isagent,u.c_shop,d.c_pv,d.c_attention';
        $prizedata['shopinfo'] = M('Users as u')->join($join)->where($userwhere)->field($field)->find();
        $prizedata['shopinfo']['c_headimg'] = GetHost().'/'. $prizedata['shopinfo']['c_headimg'];
        $prizedata['shopinfo']['c_pv'] = isset($prizedata['shopinfo']['c_pv'])?$prizedata['shopinfo']['c_pv']:0;
        $prizedata['shopinfo']['c_attention'] = isset($prizedata['shopinfo']['c_attention'])?$prizedata['shopinfo']['c_attention']:0;
        $prizedata['shopinfo']['is_attention'] = D('Resourcev2','Service')->is_attention($parr['ucode'],$prizedata['shopinfo']['c_ucode']);

        $rednumwhere['c_type'] = 1;
        $rednumwhere['c_aid'] = $activitydata['c_id'];
        $prizedata['rednum'] = 5*M('Activity_log')->where($rednumwhere)->count();

        $redamount = D('Common', 'Service')->Rediesgetucode('ACT_redamount');
        D('Common', 'Service')->RediesStoreSram('ACT_redamount', $redamount + 1,0);
        $db->commit();
        cookie('redstate',1);

        //添加5公里商圈记录
        //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
        $blogdata['ucode'] = $parr['ucode'];
        $blogdata['behavior'] = 3;
        $blogdata['regionid'] = $regionid;
        $blogdata['tag'] = 10000;
        $blogdata['tagvalue'] = '';

        //查询自己位置信息
        $result1 = D('Servecentre','Service')->GetLocation($parr['ucode']);
        $localtion = $result1['data'];

        $longitude = $localtion['longitude'];
        $latitude = $localtion['latitude'];
        $address = $localtion['address'];

        $blogdata['longitude'] = $longitude;
        $blogdata['latitude'] = $latitude;
        $blogdata['address'] = $address;
        $blogdata['addtime'] = date('Y-m-d H:i:s', time());

        $result = D('Servecentre','Service')->Addlogs($blogdata);

        // 写入消息中心
        $Msgcentre = D('Msgcentre', 'Service');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您发现的一个商家红包，金额为￥' . $prizedata['c_value'] . '，领取成功已转入余额';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgresult = $Msgcentre->CreateMessege($msgdata);

        return MessageInfo(0,'领取成功',$prizedata);
    }

    /**
     * 商家红包展示页面信息
     * @param aid,pcode,buyucode
     *
     */
    function GetRedInfoShow($parr)
    {
        // 查询商家红包来源信息
        $producewhere['a.c_pcode'] = $parr['pcode'];
        $join = 'left join t_users as b on b.c_ucode=a.c_ucode';
        $data = M('Product as a')->join($join)->where($producewhere)->field('a.c_name,b.c_nickname as shopname')->find();
        $userwhere['c_ucode'] = $parr['buyucode'];
        $data['buyname'] = M('Users')->where($userwhere)->getField('c_nickname');

        $rednumwhere['c_type'] = 1;
        $rednumwhere['c_aid'] = $parr['aid'];
        $data['rednum'] = 5*M('Activity_log')->where($rednumwhere)->count();
        //查询最新20条获奖数据

        $join = 'inner join t_activity_prize as b on a.c_pid=b.c_id';
        $join .= ' left join t_product as c on c.c_pcode=b.c_pcode';
        $join .= ' left join t_users as d on d.c_ucode=c.c_ucode';
        $where[] = array('a.c_aid in (select c_id from t_activity where c_activitytype=6)');
        $where['a.c_type'] = array('neq',3);
        $field = 'a.c_id,a.c_ucode,a.c_addtime as time,b.c_name,b.c_value,b.c_type,d.c_nickname as shopname';
        $list = M('Activity_log as a')->join($join)->where($where)->field($field)->order('a.c_id desc')->limit(20)->select();

        foreach ($list as $key => $value) {
            $userwhere['c_ucode'] = $value['c_ucode'];
            $list[$key]['name'] = M('Users')->where($userwhere)->getField('c_nickname');
        }

        $data['loglist'] = $list;
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 扫码支付商家红包展示页面信息
     * @param aid,acode,buyucode
     *
     */
    function GetScanpayRedInfoShow($parr)
    {
        // 查询商家红包来源信息
        $producewhere['c_ucode'] = $parr['acode'];
        $data = M('Users')->join($join)->where($producewhere)->field('c_nickname as shopname')->find();
        $userwhere['c_ucode'] = $parr['buyucode'];
        $data['buyname'] = M('Users')->where($userwhere)->getField('c_nickname');

        $rednumwhere['c_type'] = 1;
        $rednumwhere['c_aid'] = $parr['aid'];
        $data['rednum'] = 5*M('Activity_log')->where($rednumwhere)->count();

        //查询最新20条获奖数据
        $join = 'inner join t_activity_prize as b on a.c_pid=b.c_id';
        $join .= ' left join t_users as d on d.c_ucode=b.c_acode';
        $where[] = array('a.c_aid in (select c_id from t_activity where c_activitytype=6)');
        $where['a.c_type'] = array('neq',3);
        $field = 'a.c_id,a.c_ucode,a.c_addtime as time,b.c_name,b.c_value,b.c_type,d.c_nickname as shopname';
        $list = M('Activity_log as a')->join($join)->where($where)->field($field)->order('a.c_id desc')->limit(20)->select();

        foreach ($list as $key => $value) {
            $userwhere['c_ucode'] = $value['c_ucode'];
            $list[$key]['name'] = M('Users')->where($userwhere)->getField('c_nickname');
        }

        $data['loglist'] = $list;
        return MessageInfo(0,'查询成功',$data);
    }


}