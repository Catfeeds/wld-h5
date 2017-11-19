<?php

/**
 * 砍价活动页面
 */
class BargainActivity {

    //新增砍价活动产品
    function InserBargeinproduct($parr) {

        $pname = $parr['pname'];
        $mprice = $parr['mprice'];
        $minprice = $parr['minprice'];
        $num = $parr['num'];
        $aid = $parr['aid'];
        $acode = $parr['ucode'];
        $img = $parr['img'];

        //判断活动是否已经结束
        $where['c_id'] = $aid;
        $where['c_activitytype'] = 4;
        $where['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s', time()));
        $where['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s', time()));
        $activity = M('Activity')->where($where)->find();

        if (!$activity) {
            return Message(1024, "该活动未开始，或者该活动已结束");
        }

        if (empty($num) || $num > 3) {
            return Message(1024, "每款产品只能填写1-3个数量");
        }

        if ($mprice <= $minprice) {
            return Message(1025, "市场价格应该高于活动价格");
        }

        //判断用户是否添加产品
        $whereinfo['c_acode'] = $acode;
        $whereinfo['c_aid'] = $aid;

        $count = M('Activity_prize')->where($whereinfo)->count();
        if ($count > 0) {
            return Message(1027, "您只能上传一款活动产品");
        }

        $aconf = C('Activity');    //活动配入数据
        $tempnum = $aconf['bargainnum'];
        if (!$aconf) {
            $tempnum = 15;
        }

        if (empty($tempnum)) {
            $tempnum = 15;
        }

        //判断该商品的每次砍价金额
        $zhekou = bcsub($mprice, $minprice, 2);

        $bargainprice = ceil($zhekou/$tempnum);
        $addinfo['c_aid'] = $aid;
        $addinfo['c_acode'] = $acode;
        $addinfo['c_name'] = $pname;
        $addinfo['c_value'] = $minprice;
        $addinfo['c_totalnum'] = $num;
        $addinfo['c_num'] = $num;
        $addinfo['c_state'] = 1;
        $addinfo['c_type'] = 2;
        $addinfo['c_today_prize'] = 2;
        $addinfo['c_imgpath'] = $img;
        $addinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $addinfo['c_marketprice'] = $mprice;
        $addinfo['c_bargainnum'] = $tempnum;
        $addinfo['c_bargainprice'] = $bargainprice;

        $result = M('Activity_prize')->add($addinfo);

        if (!$result) {
            return Message(1026, "添加产品失败");
        }
        return Message(0, "添加产品成功");
    }

    //查询活动产品
    function SearchActivity($parr) {

        $aid = $parr['aid'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_activitytype'] = 4;
        $where['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s', time()));
        $where['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s', time()));
        if (!empty($aid)) {
            $where['c_id'] = $aid;
        }

        $activity = M('Activity')->where($where)->find();

        if (!$activity) {
            return Message(1026, "该活动已结束");
        }
        $aid = $activity['c_id'];
        $prizewhere['c_aid'] = $aid;
        $list = M('Activity_prize')->where($prizewhere)->limit($countPage, $pageSize)->select();
        if (!$list) {
            return MessageInfo(0, '查询成功', $list);
        }

        $count = M('Activity_prize')->where($prizewhere)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //领取奖品
    function Receive($parr) {
        $ucode = $parr['ucode'];
        $pid = $parr['pid'];
        $aid = $parr['aid'];

        $where['c_activitytype'] = 4;
        $where['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s', time()));
        $where['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s', time()));
        if (!empty($aid)) {
            $where['c_id'] = $aid;
        }
        $activity = M('Activity')->where($where)->find();
        if (!$activity) {
            return Message(1026, "该活动已结束");
        }
        $aid = $activity['c_id'];


        //判断该用户是否领取过
        $wherelog['c_aid'] = $aid;
        $wherelog['c_ucode'] = $ucode;
        $countinfo = M('Activity_log')->where($wherelog)->find();

        if ($countinfo) {
            $data['barginid'] = $countinfo['c_id'];
            return MessageInfo(2028, "您已经领取过本次活动的奖品， 您不能再领取了", $data);
        }

        // 查找商品
        $whereprize['c_id'] = $pid;
        $whereprize['c_aid'] = $aid;
        $prize = M('Activity_prize')->where($whereprize)->find();

        if (!$prize) {
            return Message(1027, "该奖品不存在");
        }

        if ($prize['c_num'] <= 0) {
            return Message(1028, "该奖品数量为0");
        }

        //判断用户是否有默认地址
        $addresswhere['c_ucode'] = $parr['ucode'];
        $addresswhere['c_is_default'] = 1;
        $useraddress = M('Users_address')->where($addresswhere)->find();
        if (count($useraddress) == 0) {
            return Message(3006, '请传入用户地址');
        }

        $db = M('');
        $db->startTrans();

        //开始领取奖品
        $addlog['c_ucode'] = $ucode;
        $addlog['c_aid'] = $aid;
        $addlog['c_pid'] = $pid;
        $addlog['c_value'] = $prize['c_marketprice'];
        $addlog['c_type'] = $prize['c_type'];
        $addlog['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Activity_log')->add($addlog);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1029, "领取产品失败，请稍后再试");
        }
        $barginid = $result;

        //扣除剩余数量
        $whereprize['c_id'] = $pid;
        $whereprize['c_aid'] = $aid;
        $whereprize['c_num'] = array('GT', 0);
        $result = M('Activity_prize')->where($whereprize)->setDec('c_num', 1);

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1030, "领取奖品失败，请稍后再试");
        }

        $data['barginid'] = $barginid;
        //提交事务
        $db->commit();
        return MessageInfo(0, "领取成功", $data);
    }

    // 领取奖品展现
    function Getprize($parr) {
        $barginid = $parr['barginid'];

        $where['a.c_id'] = $barginid;
        $where['c.c_activitytype'] = 4;

        $join = 'inner join t_activity_prize as b on a.c_pid=b.c_id inner join t_activity as c on a.c_aid=c.c_id';
        $field = "a.c_ucode,a.c_value as surpluvalue,a.c_pid,a.c_aid,a.c_orderid,b.c_acode,b.c_name,b.c_value,b.c_num,b.c_imgpath,c_marketprice,b.c_bargainnum,b.c_bargainprice,c.c_activityname,c.c_activitystarttime,c.c_activityendtime,c.c_id as activityid";

        $info = M('Activity_log as a')->join($join)->where($where)->field($field)->find();

        if (!$info) {
            return Message(1024, "该奖品不存在");
        }

        //计算砍价百分比
        $kanjiazong = bcsub($info['surpluvalue'], $info['c_value'], 2);
        $zhekou = bcsub($info['c_marketprice'], $info['c_value'], 2);
        $baifenbi = bcsub(100, bcmul(bcdiv($kanjiazong, $zhekou, 2), 100, 2), 2);
        $info['baifenbi'] = $baifenbi;

        return MessageInfo(0, "查询成功", $info);
    }

    //获取砍价记录
    function GetBargin($parr) {

        $barginid = $parr['barginid'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_barginid'] = $barginid;
        $list = M('Activity_bargin')->where($where)->order('c_id desc')->limit($countPage, $pageSize)->select();

        if (!$list) {
            return Message(0, '没有查询到数据');
        }

        $count = M('Activity_bargin')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //开始砍价
    function Bargin($parr) {
        $barginid = $parr['barginid'];

        $openid = $parr['openid'];
        $headerimg = $parr['headerimg'];
        $wxname = $parr['wxname'];

        $return = $this->Getprize($parr);

        if ($return['code'] != 0) {
            return $return;
        }
        $info = $return['data'];

        //判断砍价时间
        $begintime = strtotime($info['c_activitystarttime']);
        $endtime = strtotime($info['c_activityendtime']);

        $time = time();

        if ($time < $begintime || $time > $endtime) {
            return Message(1025, '活动已结束');
        }

        if ($info['surpluvalue'] == $info['c_value']) {
            return Message(1025, '该商品已经完成了集花粉');
        }

        if (!empty($info['c_orderid'])) {
            return Message(1026, '该商品已经完成了集花粉');
        }

        //判断是否已经砍过价格了
        $whereinfo['c_barginid'] = $barginid;
        $whereinfo['c_openid'] = $openid;

        $count = M('Activity_bargin')->where($whereinfo)->count();

        if ($count > 0) {
            return Message(1027, '该商品您已经集花粉了');
        }

        $db = M('');
        $db->startTrans();

        $kanjia = $info['c_bargainprice'];
        $surpluvalue = $info['surpluvalue'];

        $shenyu = bcsub($surpluvalue, $kanjia, 2);
        $value = $info['c_value'];

        //判断砍价金额是否小于最低价
        if ($shenyu <= $value) {
            $shenyu = $value;
            $kanjia = bcsub($surpluvalue, $shenyu, 2);
        }

        //写入砍价记录表
        $bargin['c_pid'] = $info['c_pid'];
        $bargin['c_aid'] = $info['c_aid'];
        $bargin['c_barginid'] = $barginid;
        $bargin['c_openid'] = $openid;
        $bargin['c_headerimg'] = $headerimg;
        $bargin['c_wxname'] = $wxname;
        $bargin['c_barginprice'] = $kanjia;
        $bargin['c_bargintime'] = date('Y-m-d H:i:s', time());

        $result = M('Activity_bargin')->add($bargin);

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1026, "集花粉失败，请稍后在尝试");
        }

        //砍价后金额
        if ($shenyu <= $value) {
            //生成订单
            $parr['ucode'] = $info['c_ucode'];
            $parr['acode'] = $info['c_acode'];
            $parr['marketprice'] = $info['c_marketprice'];
            $parr['name'] = $info['c_name'];
            $parr['total'] = $value;
            $parr['c_imgpath'] = $info['c_imgpath'];
            $parr['c_imgpath'] = $info['c_imgpath'];
            $parr['activityid'] = $info['activityid'];
            $parr['activityname'] = $info['c_activityname'];
            $result = $this->CreateOrder($parr);

            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            $save['c_orderid'] = $result['data']['orderid'];
        }

        $save['c_value'] = $shenyu;
        $where['c_id'] = $barginid;
        $result = M('Activity_log')->where($where)->save($save);

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1028, "集花粉失败，请稍后在尝试");
        }
        //提交事务
        $db->commit();

        $return = $this->Getprize($parr);
        $info = $return['data'];
        $kanjiazong = bcsub($info['surpluvalue'], $info['c_value'], 2);
        $zhekou = bcsub($info['c_marketprice'], $info['c_value'], 2);
        $baifenbi = bcsub(100, bcmul(bcdiv($kanjiazong, $zhekou, 2), 100, 2), 2);

        $data['kanjia'] = $kanjia;
        $data['surpluvalue'] = $shenyu;
        $data['baifenbi'] = $baifenbi;
        $data['headerimg'] = $headerimg;
        $data['wxname'] = $wxname;
        $data['time'] = date('Y-m-d H:i:s', time());
        return MessageInfo(0, "集花粉成功", $data);
    }

    //砍价到一半生成订单
    function TempBargin($parr) {
        $barginid = $parr['barginid'];
        $return = $this->Getprize($parr);

        if ($return['code'] != 0) {
            return $return;
        }

        $info = $return['data'];

        //判断砍价时间
        $begintime = strtotime($info['c_activitystarttime']);
        $endtime = strtotime($info['c_activityendtime']);

        $time = time();

        if ($time < $begintime || $time > $endtime) {
            return Message(1025, '活动已结束');
        }


        if (!empty($info['c_orderid'])) {
            return Message(1026, '该商品已经完成了集花粉');
        }

        $db = M('');
        $db->startTrans();

        //生成订单
        $parr['ucode'] = $info['c_ucode'];
        $parr['acode'] = $info['c_acode'];
        $parr['marketprice'] = $info['c_marketprice'];
        $parr['name'] = $info['c_name'];
        $parr['total'] = $info['surpluvalue'];
        $parr['c_imgpath'] = $info['c_imgpath'];
        $parr['c_imgpath'] = $info['c_imgpath'];
        $parr['activityid'] = $info['activityid'];
        $parr['activityname'] = $info['c_activityname'];
        $result = $this->CreateOrder($parr);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //修改记录表
        $save['c_orderid'] = $result['data']['orderid'];
        $where['c_id'] = $barginid;
        $result = M('Activity_log')->where($where)->save($save);

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1028, "集花粉失败，请稍后在尝试");
        }
        //提交事务
        $db->commit();

        $returninfo['orderid'] = $save['c_orderid'];
        return MessageInfo(0, "生成订单成功", $returninfo);
    }

    //生成订单
    function CreateOrder($parr) {

        $orderid = CreateOrder("1");

        //生成订单详情
        $return = $this->CreataOrderdetails($parr, $orderid);

        if ($return['code'] != 0) {
            return $return;
        }

        //生成订单
        $return = $this->CreataOrderInfo($parr, $orderid);

        if ($return['code'] != 0) {
            return $return;
        }
        //生成订单地址
        $return = $this->CreataOrderAddress($parr, $orderid);
        if ($return['code'] != 0) {
            return $return;
        }

        $data['orderid'] = $orderid;
        return MessageInfo(0, "订单生成成功", $data);
    }

    //生成订单详情
    protected function CreataOrderdetails($parr, $orderid) {

        $detailid = CreateOrder("d");
        $tempdetails['c_orderid'] = $orderid;
        $tempdetails['c_detailid'] = $detailid;
        $tempdetails['c_ucode'] = $parr['ucode'];
        $tempdetails['c_pprice'] = $parr['marketprice'];
        $tempdetails['c_pname'] = $parr['name'];
        $tempdetails['c_pmodel_name'] = $parr['name'];
        $tempdetails['c_pnum'] = 1;
        $tempdetails['c_ptotal'] = $parr['total'];
        $tempdetails['c_pimg'] = $parr['c_imgpath'];
        $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order_details')->add($tempdetails);
        if (!$result) {
            return Message(1000, "生成订单详情失败");
        }
        return MessageInfo(0, "订单详情生成成功", $tempdetails);
    }

    //创建订单信息
    protected function CreataOrderInfo($parr, $orderid) {

        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $parr['ucode'];
        $aorderinfo['c_acode'] = $parr['acode'];
        $aorderinfo['c_free'] = 0;
        $aorderinfo['c_total_price'] = $parr['total'];
        $aorderinfo['c_activity_id'] = $parr['activityid'];
        $aorderinfo['c_activity_name'] = $parr['activityname'];
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());

        //如果支付价格为0的处理方式
        if ($parr['total'] == 0) {
            $aorderinfo['c_pay_state'] = 1;
            $aorderinfo['c_pay_rule'] = 5;
            $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

            //判断用户是否需要和商家绑定
            $userwhere['c_ucode'] = $parr['ucode'];
            $usertuijian = M('Users_tuijian')->where($userwhere)->find();

            if (count($usertuijian) == 0) {
                $add['c_ucode'] = $parr['ucode'];
                $add['c_pcode'] = $parr['acode'];
                $add['c_addtime'] = date('Y-m-d H:i:s', time());
                $result = M('Users_tuijian')->add($add);
                if (!$result) {
                    return Message(3002, "建立用户关系失败");
                }

                //判断用户是否绑定代理
                $userinfo = M('Users')->where($userwhere)->field('c_isagent,c_acode')->find();
                if ($userinfo['c_isagent'] == 0 && empty($userinfo['c_acode'])) {
                    $agentwhere['c_ucode'] = $parr['acode'];
                    $usersave['c_acode'] = M('Users')->where($agentwhere)->getField('c_acode');
                    $result = M('Users')->where($userwhere)->save($usersave);
                    if (!$result) {
                        return Message(3001, "归属代理商失败");
                    }
                }
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $parr['ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '您提交的订单号：' . $orderid . '，已支付成功';
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);

            $msgdata['ucode'] = $parr['acode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '订单号：' . $orderid . '，已支付成功，请安排尽快发货';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);
        } else {
            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $parr['ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '您提交的订单号：' . $orderid . '，已经生成，请查收';
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);
        }


        $result = M('Order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }
        return Message(0, "订单创建成功");
    }

    //生成订单地址
    protected function CreataOrderAddress($parr, $orderid) {
        $addresswhere['c_ucode'] = $parr['ucode'];
        $addresswhere['c_is_default'] = 1;
        $useraddress = M('Users_address')->where($addresswhere)->find();
        if (count($useraddress) == 0) {
            return Message(3006, '请传入用户地址');
        }

        $orderaddress['c_orderid'] = $orderid;
        $orderaddress['c_consignee'] = $useraddress['c_consignee'];
        $orderaddress['c_telphone'] = $useraddress['c_mobile'];
        $orderaddress['c_address'] = $useraddress['c_address'];
        $orderaddress['c_province'] = $useraddress['c_provincename'];
        $orderaddress['c_cityname'] = $useraddress['c_cityname'];
        $orderaddress['c_district'] = $useraddress['c_districtname'];
        $orderaddress['c_provinceid'] = $useraddress['c_province'];
        $orderaddress['c_cityid'] = $useraddress['c_city'];
        $orderaddress['c_districtid'] = $useraddress['c_district'];
        $result = M('Order_address')->add($orderaddress);
        if (!$result) {
            return Message(3007, '生成订单地址失败');
        }
        return Message(0, "订单地址生成成功");
    }

    //查询中奖记录
    function Winning($parr){
        $ucode = $parr['ucode'];

        $w['l.c_ucode'] = $ucode;
        $w['a.c_activitytype'] = 4;

        $data = M('Activity_log as l')->join('t_activity as a on l.c_aid=a.c_id')->where($w)->field('l.*')->find();
        if (!$data) {
             return MessageInfo(1000,"查询失败");
        }
        return MessageInfo(0,"查询成功",$data);
    }

}
