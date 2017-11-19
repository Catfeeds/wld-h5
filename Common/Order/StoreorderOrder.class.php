<?php

/**
 * 实体店铺订单相关接口
 */
class StoreorderOrder {

    //获取商家类型（微商或者实体店铺）
    public function get_shoptype($pcode){
        $where['c_pcode'] = $pcode;
        $shoptype = M('Product')->where($where)->getField('c_source');
        return $shoptype;
    }
    /**
     *  用户提交订单接口(本版本采用单个商家的同种类型进行提交订单)
     *  @param  ucode,delivery,addressid,postscript,money,model
     *  produce 用户选择的产品信息json格式(acode,pcode,num,pucode)
     *
     */
    public function CreataOrder($parr) {
        $produce = objarray_to_array(json_decode($parr['produce']));
        $ucode = $parr['ucode'];
        $delivery = $parr['delivery'];
        $addressid = $parr['addressid'];
        $postscript = $parr['postscript'];
        $money = $parr['money'];
        $model = $parr['model'];

        $result = $this->splitProduct($produce, $ucode);
        if ($result["code"] != 0) {
            return $result;
        }
        $shop = $result['data'];

        if (empty($shop)) {
            return Message(1015, "您没有传入产品信息");
        }

        $info = $shop['value'];//商品信息
        $free = $shop['freeprice'];//邮费
        // $isagent = $shop['isagent'];
        $acode = $shop['acode'];//商家编码

        if (empty($info)) {
            return Message(1015, "产品信息为空");
        }

        if ($delivery == 2) {
            $free = 0;
        }

        $db = M('');
        $db->startTrans();

        $orderid = CreateOrder('t');
        $result = $this->CreataOrderdetails($info, $orderid, $delivery);

        $totprice = 0;
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $totprice = $result['data']['totprice'];

        //生成订单
        $result = $this->CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $free, $delivery, $acode, $model);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
        $aorderinfo = $result['data'];

        //生成订单地址
        $result = $this->CreataOrderAddress($orderid, $addressid);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        // 查询推荐人编码
        $tjuserwhere['c_ucode'] = $ucode;
        $tjuserucode = M('Users_tuijian')->where($tjuserwhere)->getField('c_pcode');

        $Msgcentre = IGD('Msgcentre', 'Message');

        //给商家发送相关消息
        $msgdata['ucode'] = $acode;
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '陛下，有用户在您的店下了个单，可及时联系买家哦~，订单未支付前可修改运费。';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        $data['orderid'] = $orderid;

        //提交事务
        $db->commit();
        return MessageInfo(0, "生成订单成功", $data);
    }

    //拆分商家和产品信息
    public function splitProduct($product, $ucode) {
        //查询出所有产品信息
        $shop = array();
        $freeprice = 0;
        //进行拆分订单
        foreach ($product as $key => $value) {
            //判断产品是否存在
            $whereinfo['c_pcode'] = $value['pcode'];
            $whereinfo['c_ishow'] = 1;
            $whereinfo['c_isdele'] = 1;
            $whereinfo['c_source'] = 2;
            $productinfo = M('Product')->where($whereinfo)->find();

            if (count($productinfo) <= 0) {
                return Message(1015, "产品信息不存在，不能生成订单");
            }
            // 判断产品库存
            $price = $productinfo['c_price'];
            $num = $productinfo['c_num'];
            if ($num < $value['num']) {
                return Message(1015, "产品库存不足");
            }

            //产品信息
            $info = array();
            $info['pcode'] = $productinfo['c_pcode'];
            $info['ucode'] = $ucode;
            $info['price'] = $productinfo['c_price'];
            $info['pname'] = $productinfo['c_name'];
            $info['num'] = $value['num'];
            $info['pucode'] = $value['pucode'];
            $info['pimg'] = $productinfo['c_pimg'];

            $tempcount = $price * $value['num'];
            $singletotle = sprintf("%.2f", $tempcount);
            $info['singletotle'] = $singletotle;

            $info['isrebate'] = $productinfo['c_isrebate'];
            $info['rebate_proportion'] = $productinfo['c_rebate_proportion'];

            if ($productinfo['c_isspread'] == 1 && !empty($value['pucode'])) {
                $info['isspread'] = 1;
            } else {
                $info['isspread'] = 0;
            }

            $info['spread_proportion'] = $productinfo['c_spread_proportion'];
            $info['freeprice'] = $productinfo['c_freeprice'];

            $shop['acode'] = $productinfo['c_ucode'];
            $shop['value'][] = $info;
            // $shop['isagent'] = $productinfo['c_isagent'];

            if ($productinfo['c_isfree'] == 2) {
                $freeprice+=$productinfo['c_freeprice'] * $value['num'];
            }
        }

        $shop['freeprice'] = $freeprice;
        return MessageInfo(0, "产品拆分成功", $shop);
    }

    //生成订单详情
    protected function CreataOrderdetails($product, $orderid, $delivery) {
        $totprice = 0;
        $int = 0;
        foreach ($product as $key => $v1) {
            $singletotle = $v1['singletotle'];
            $totprice+=$singletotle;
            $temp = "dt" . $int;
            $int++;
            $detailid = CreateOrder($temp);

            //减少库存
            $result = $this->Reduceinventory($v1['pcode'], $v1['num']);
            if ($result['code'] != 0) {
                return $result;
            }

            //写入订单详情表
            $tempdetails = array();
            if ($delivery == 2) {
                $tempdetails['c_free'] = 0;
            } else {
                $tempdetails['c_free'] = $v1['num'] * $v1['freeprice'];
            }
            $tempdetails['c_orderid'] = $orderid;
            $tempdetails['c_detailid'] = $detailid;
            $tempdetails['c_pcode'] = $v1['pcode'];
            $tempdetails['c_ucode'] = $v1['ucode'];
            $tempdetails['c_pprice'] = $v1['price'];
            $tempdetails['c_pname'] = $v1['pname'];
            $tempdetails['c_pnum'] = $v1['num'];
            $tempdetails['c_ptotal'] = $singletotle;
            $tempdetails['c_pimg'] = $v1['pimg'];
            $tempdetails['c_pucode'] = $v1['pucode'];
            $tempdetails['c_isrebate'] = $v1['isrebate'];
            $tempdetails['c_rebate_proportion'] = $v1['rebate_proportion'];
            $tempdetails['c_isspread'] = $v1['isspread'];
            $tempdetails['c_spread_proportion'] = $v1['spread_proportion'];
            $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Order_details')->add($tempdetails);
            if (!$result) {
                return Message(1000, "生成订单详情失败");
            }
        }

        $date['totprice'] = $totprice;
        return MessageInfo(0, "订单详情生成成功", $date);
    }

    //创建订单信息
    protected function CreataOrderInfo($orderid, $ucode, $totprice, $postscript,$free, $delivery, $acode, $model) {
        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $ucode;

        $countprice = $totprice + $free;

        $aorderinfo['c_acode'] = $acode;
        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_deliverystate'] = 0;
        $aorderinfo['c_free'] = $free;
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = $delivery;
        $aorderinfo['c_postscript'] = $postscript;
        $aorderinfo['c_source'] = 2;
        $aorderinfo['c_model'] = $model;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }

        return MessageInfo(0, "订单创建成功",$aorderinfo);
    }

    //生成订单地址
    function CreataOrderAddress($orderid, $addressid) {
        $addresswhere['c_id'] = $addressid;
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

    //减少商品库存
    protected function Reduceinventory($pcode,$num) {
        // 减少总库存
        $productinfo['c_pcode'] = $pcode;
        $productinfo['c_num'] = array('egt', $num);
        $result = M('Product')->where($productinfo)->setDec('c_num', $num);
        if (!$result) {
            return Message(1013, "库存扣除失败");
        }

        return Message(0, "库存修改成功");
    }

    //获取作为支付使用订单信息
    public function GetPayorderinfo($parr) {
        $db = M('');
        $db->startTrans(); /* 开启事务 */

        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $orderid = $parr['orderid'];

        if (!empty($ucode)) {
            $whereinfo['c_ucode'] = $ucode;
        }

        if (!empty($acode)) {
            $whereinfo['c_acode'] = $acode;
        }

        $whereinfo['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($whereinfo)->find();

        if (count($orderinfo) <= 0) {
            return Message(0, "该订单信息不存在");
        }

        $detail = M('Order_details')->where($whereinfo)->select();

        foreach ($detail as $key => $value) {
            $detail[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }

        //随机获取线上系统商户号
        $mchidarr = explode(',', C('LINEMICH'));
        $mch_id = $mchidarr[rand(0,(count($mchidarr)-1))];

        $orderinfo['detail'] = $detail;
        $orderinfo['mch_id'] = $mch_id;

        $db->commit();
        return MessageInfo(0, "创建订单成功！", $orderinfo);
    }

    //支付订单
    public function PayOrder($parr) {
        $upay = $parr['upay'];
        $orderid = $parr['orderid'];

        //友收宝支付回调
        if ($upay == 1) {
            $payorderid = $orderid;
            $result = IGD('Order','Order')->GetSystemOrder($orderid);
            if ($result['code'] != 0) {
                return $result;
            }
            $orderid = $result['data']['c_orderid'];
        }
        
        $payrule = $parr['payrule'];
        $actualprice = $parr['actualprice'];
        $thirdpartynum = $parr['thirdpartynum'];

        $orderwhere['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($orderwhere)->find();

        if (count($orderinfo) == 0) {
            return Message(1016, "没有查询到该订单");
        }

        if ($orderinfo['c_pay_state'] == 1 || $orderinfo['c_order_state'] == 1) {
            return Message(1016, "该订单已支付");
        }

        $totprice = $orderinfo['c_total_price'];
        $free = $orderinfo['c_free'];
        $actual = $orderinfo['c_actual_price'];
        //计算订单的总价
        $countprice = bcadd($totprice, $free, 2);

        //计算支付总价
        $payzong = bcadd($actualprice, $actual, 2);
        if ($payzong < $countprice) {
            return Message(1016, "您支付的金额不足");
        }

        $db = M('');
        $db->startTrans();

        //开始修改订单信息
        $aorderinfo['c_pay_state'] = 1;
        $aorderinfo['c_pay_rule'] = $payrule;
        $aorderinfo['c_actual_price'] = $payzong;
        $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->where($orderwhere)->save($aorderinfo);
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1017, "订单信息操作失败");
        }

        //友收宝支付回调修改支付状态
        if ($upay == 1) {
            $result = IGD('Order','Order')->SavePayorder($payorderid,1);
            if ($result['code'] != 0) { 
                $db->rollback(); //不成功，则回滚
                return $result;
            }
        }

        //用户支付记录操作
        $result = IGD('Order','Order')->paylog($orderid, $payrule, $actualprice, $thirdpartynum,1,$payorderid);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //判断返利
        $detaillist = M('Order_details')->where($orderwhere)->select();
        if (count($detaillist) == 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1017, "没有查询到订单详情");
        }

        $result = $this->CheckRebate($detaillist, $orderinfo['c_ucode'], $orderinfo['c_acode'],$orderinfo['c_activity_id']);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
        $shopprofit = $result['data'];

        /*****   新增银盛支付代付代扣   ******/ 
        //当商家到账的利润大于支付利润采用代扣
        //当商家到账的利润小于支付利润采用代付
        if ($actualprice != $shopprofit) {
            if ($actualprice > $shopprofit) {
                $arr['sign'] = 2; // 1 代付 2 代扣
                $opmoney = bcsub($actualprice,$shopprofit,2);
            } else {
                $arr['sign'] = 1; // 1 代付 2 代扣
                $opmoney = bcsub($shopprofit,$actualprice,2);
            }

            $arr['type'] = 2; // 1  实时结算  2 按日结算  3 按月结算
            $arr['ucode'] = $orderinfo['c_acode']; // 分润人
            $arr['scode'] = $orderinfo['c_acode'];
            $arr['bcode'] = $orderinfo['c_ucode'];
            $arr['orderid'] = CreateOrder('f');
            $arr['key'] = $orderinfo['c_orderid'];
            $arr['desc'] = '平台实体店订单交易资金操作';
            $arr['total_money'] = $orderinfo['c_total_price'];
            $arr['money'] = $opmoney;
            $arr['source'] = 1; // 1普城订单,2后台,3活动,4蜜城订单,5普城跨界,6提现,7注册,8老注册,9扫码,10转发,11绑定,12跨界扫码,13普城购返,14普城推返,15蜜城跨界,16普通退款,17蜜城退款,18红包',
            $res = IGD('Splitting','Order')->CreateRecord($arr);
            if($res['code']!=0){
                $db->rollback(); //不成功，则回滚
                return Message(1001,'创建代扣失败');
            }
        }
        /*****   新增银盛支付代付代扣   ******/ 
   
        $Msgcentre = IGD('Msgcentre', 'Message');

        //给用户发送相关消息
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = $userremind;
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        //给商家发送消息
        $msgdata['ucode'] = $orderinfo['c_acode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '陛下，您有新的订单需要审阅，别让文武百官等太久，祝您万福安康~';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        //提交事务
        $db->commit();
        return Message(0, "支付成功");
    }

    //获取实体店铺返利配置
    public function getSetting($acode){
        //查询行业平台抽成
        $settingwhere['b.c_ucode'] = $acode;
        $join = 'left join t_users as b on b.c_shoptrade=a.c_id ';
        $setting = M('Shop_industry as a')->join($join)->where($settingwhere)->field('a.*')->find();
        if (!$setting) {
            return Message(1017, "系统配置不存在");
        }

        return MessageInfo(0,"查询配置成功",$setting);
    }

    //判断商品返利
    public function CheckRebate($detaillist, $ucode, $acode,$activity_id) {
        //判断用户是否是直属关系
        $userinfowhere['c_ucode'] = $ucode;
        $userinfotuijian = M('Users_tuijian')->where($userinfowhere)->find();

        //查询联盟信息
        $cwh['c_ucode'] = $acode;
        $unioninfo = M('A_federation')->where($cwh)->find();
        if (empty($unioninfo['c_pfederationid'])) {
            $pfederationid = $unioninfo['c_id'];
        } else {
            $pfederationid = $unioninfo['c_pid'];
        }

        $tag = 0;    //判断用户是否有关系，0代表还没有建立起来关系，1代表直属关系，2代表间接关系
        $userinfo = M('Users')->where($userinfowhere)->field('c_isagent,c_acode')->find();
        if ($ucode == $acode) {
            $tag = 1;
        } else {
            if (count($userinfotuijian) > 0) {               

                if ($userinfotuijian['c_pcode'] == $acode || ($userinfotuijian['c_pfederationid'] == $pfederationid && !empty($pfederationid))) {
                    $pftype = M('A_federation')->where(array('c_id'=>$pfederationid))->getField('c_type');
                    if ($pftype == 1) {
                        $tag = 1;
                    } else {
                        $tag = 2;
                    }
                    if ($userinfotuijian['c_pcode'] == $acode) {
                       $tag = 1;
                    }
                } else {
                    $tag = 2;
                }
            } else {
                if ($userinfo['c_isagent'] > 0) {
                    $tag = 1;
                }
            }
        }

        if ($tag == 0) {
            //建立直属关系
            $add['c_ucode'] = $ucode;
            $add['c_pcode'] = $acode;
            $add['c_source'] = 2;
            $add['c_pfederationid'] = $pfederationid;
            $add['c_federationid'] = $unioninfo['c_id'];
            $add['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Users_tuijian')->add($add);
            if ($result <= 0) {
                return Message(1016, "建立用户关系失败");
            }

            $whereacodeinfo['c_ucode'] = $acode;
            $acodeinfo = M('Users')->where($whereacodeinfo)->getField('c_acode');
            if (!$acodeinfo) {
                return Message(1016, "未有所属代理商");
            }

            if (empty($userinfo['c_acode'])) {
                $whereuserinfo['c_ucode'] = $ucode;
                $datauserinfo['c_acode'] = $acodeinfo;
                $result = M('Users')->where($whereuserinfo)->save($datauserinfo);
                if (!$result) {
                    return Message(1016, "纳入代理商失败");
                }
            }
        }

        //查询系统配入
        $settinginfo = $this->getSetting($acode);
        if ($settinginfo['code'] != 0) {
            return $settinginfo;
        }
        $setting = $settinginfo['data'];

        $Msgcentre = IGD('Msgcentre', 'Message');
        $zongprice = '0.00';$shopprofit = '0.00';
        foreach ($detaillist as $key => $value) {
            //计算平台抽成
            $scanpay_shoprake = $setting['c_scanpay_shoprake'];
            if ($tag == 0 || $tag == 1 || !empty($activity_id)) {
                $commission = 0;
            } else {
                if ($scanpay_shoprake > 0) {
                    $commission = bcmul($value['c_ptotal'], bcdiv($scanpay_shoprake, 100, 2), 2);
                }
            }

            //计算购买优惠
            $rebate = 0;
            if ($value['c_isrebate'] == 1 && empty($activity_id)) {
                $rebate = bcmul($value['c_ptotal'], bcdiv($value['c_rebate_proportion'], 100, 2), 2);
            }

            //计算推广佣金
            $spread = 0;
            if ($value['c_isspread'] == 1 && empty($activity_id)) {
                $spread = bcmul($value['c_ptotal'], bcdiv($value['c_spread_proportion'], 100, 2), 2);
            }

            //修改订单详情状态
            $orderdetel['c_detailid'] = $value['c_detailid'];
            //计算商家获得利润
            $lirun = $value['c_ptotal'] - $commission - $spread - $rebate;
            if ($lirun < 0) {
                return Message(1017, "支付商家利润为0");
            }

            if (!empty($activity_id)) {
                $lirun = $value['c_profit'];
            }

            $save['c_profit'] = $lirun;
            $save['c_commission'] = $commission;
            $save['c_rebate'] = $rebate;
            $save['c_spread'] = $spread;
            $result = M('Order_details')->where($orderdetel)->save($save);

            $shopprofit = bcadd($shopprofit,$save['c_profit'],2);
            $zongprice = $zongprice + $value['c_ptotal'];
        }

        //添加购物抽奖机会
        if ($zongprice > 10 && $tag == 2)  {
            //查询用户每天的机会
            $lotterywhere['c_ucode'] = $ucode;
            $lotterywhere['c_rule'] = 4;
            $lhjihui1 = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');
            $lhjihui1 = isset($lhjihui1)?$lhjihui1:0;
            $lhjoin = 'left join t_actjoin_moneylog as b on a.c_joinaid=b.c_id';
            $lhwhere['a.c_ucode'] = $parr['ucode'];
            $lhwhere['b.c_activitytype'] = 24;
            $lhwhere[] = array("a.c_addtime>='".date('Y-m-d 00:00:00')."' and a.c_addtime<='".date('Y-m-d 23:59:59')."'");
            $lhjihui2 = M('A_actlog as a')->join($lhjoin)->where($lhwhere)->count();
            if (($lhjihui1+$lhjihui2) < 5) {
                $actparr['ucode'] = $ucode;
                $actparr['rule'] = 4;
                $result = IGD('Advert','Newact')->AddActNum($actparr,1);
                if ($result['code'] == 0) {
                   //成功用户发送消息
                    $Msgcentre = IGD('Msgcentre', 'Message');
                    $msgdata['ucode'] = $ucode;
                    $msgdata['type'] = 0;
                    $msgdata['platform'] = 1;
                    $msgdata['sendnum'] = 1;
                    $msgdata['title'] = '系统消息';
                    $msgdata['content'] = '您在平台内完成一笔大于10元的订单，获得一次抽奖机会';
                    $msgdata['tag'] = 2;
                    $msgdata['weburl'] = GetHost(1) . '/index.php/Activity/Index/lottery';
                    $msgdata['tagvalue'] = GetHost(1) . '/index.php/Activity/Index/lottery';
                    $Msgcentre->CreateMessegeInfo($msgdata);
                }
            }
        }
        return MessageInfo(0, "操作成功",$shopprofit);
    }

    //确认订单
    public function Confirmorder($parr) {
        $orderid = $parr['orderid'];
        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1000, '该订单不存在');
        }

        //判断订单是否可以进行确认订单
        if ($orderinfo['c_pay_state'] != 1 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 2) {
            return Message(1001, '该订单无法确认');
        }

        $db = M('');
        $db->startTrans();

        //修改订单状态
        $save['c_deliverystate'] = 5;
        $save['c_confirmtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->where($whereorder)->save($save);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1021, '订单确认失败');
        }

        //判断用户是否是直属关系
        $userinfowhere['c_ucode'] = $orderinfo['c_ucode'];
        $userinfotuijian = M('Users_tuijian')->where($userinfowhere)->find();
        $acode = $orderinfo['c_acode'];
        $tag = 2;    //判断用户是否有关系，0代表还没有建立起来关系，1代表直属关系，2代表间接关系

        //判断用户自己购买自己的产品
        if ($acode == $orderinfo['c_ucode']) {
            $tag = 1;
        } else {
            if (count($userinfotuijian) > 0) {
                if ($userinfotuijian['c_pcode'] == $acode) {
                    $tag = 1;
                } else {
                    $tag = 2;
                }
            } else {
                $userinfo = M('Users')->where($userinfowhere)->field('c_isagent')->find();

                if ($userinfo['c_isagent'] > 0) {
                    $tag = 1;
                }
            }
        }

        //查询系统配置
        $settinginfo = $this->getSetting($acode);
        if($settinginfo['code'] != 0){
            $db->rollback(); //不成功，则回滚
            return $settinginfo;
        }

        $setting = $settinginfo['data'];

        $scanpay_areaprofit = $setting['c_scanpay_areaprofit'];//区代分红比例
        $scanpay_cityprofit = $setting['c_scanpay_cityprofit'];//市代分红比例
        $scanpay_shoprake = $setting['c_scanpay_cityprofit'];//抽取商家比例
        $scanpay_tjprofit = $setting['c_scanpay_tjprofit'];//推荐人分红比例

        //获取订单详情信息
        $wheredetail['c_orderid'] = $orderid;
        $wheredetail['c_productstatus'] = 0;
        $detailslist = M('Order_details')->where($whereorder)->select();

        $Msgcentre = IGD('Msgcentre', 'Message');
        foreach ($detailslist as $key => $value) {
            $source = 5;
            $pname = $value['c_pname'];
            /* ----开始返利------- */
            if ($value['c_rebate'] > 0) {
                $beizhu = "您购买商品" . $pname;
                $result = IGD('Order','Order')->faliRebate($orderinfo['c_ucode'], $value['c_rebate'], $source, $value['c_detailid'], $beizhu, 0);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }

                //给用户发送相关消息
                $msgdata['ucode'] = $orderinfo['c_ucode'];
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['content'] = '您购买商品' . $pname . '完成，您获得返佣￥' . $value['c_rebate'] . '，已成功转入余额';
                $msgdata['tag'] = 2;
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $Msgcentre->CreateMessege($msgdata);
            }

            //返利给推广人
            if ($value['c_spread'] > 0 && !empty($value['c_pucode']) && empty($orderinfo['c_activity_id'])) {
                $beizhu = "您推荐的产品" . $pname . "已经被购买";
                $result = IGD('Order','Oder')->faliRebate($value['c_pucode'], $value['c_spread'], $source, $value['c_detailid'], $beizhu, 0);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }

                //给用户发送相关消息
                $msgdata['ucode'] = $value['c_pucode'];
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['content'] = '您推荐的产品' . $pname . '已经被购买，您获得推广佣金￥' . $value['c_spread'] . '，已成功转入余额';
                $msgdata['tag'] = 2;
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $Msgcentre->CreateMessege($msgdata);
            }

            $commission = $value['c_commission'];

            // 提成分成
            if ($commission > 0 && $tag == 2 && empty($orderinfo['c_activity_id'])) {
                //给推荐人提成
                $tuijianucode = $userinfotuijian['c_pcode'];
                if ($scanpay_tjprofit > 0 && !empty($tuijianucode)) {
                    $tuijianmoney = bcmul($commission, bcdiv($scanpay_tjprofit, 100, 2), 2);
                    if ($tuijianmoney > 0) {
                        $beizhu = "您推荐的用户跨界购买产品：" . $pname;
                        $result = IGD('Order','Order')->faliRebate($tuijianucode, $tuijianmoney, $source, $value['c_detailid'], $beizhu, 0);
                        if ($result['code'] != 0) {
                            $db->rollback(); //不成功，则回滚
                            return $result;
                        }
                        //给用户发送相关消息
                        $msgdata['ucode'] = $tuijianucode;
                        $msgdata['type'] = 0;
                        $msgdata['platform'] = 1;
                        $msgdata['sendnum'] = 1;
                        $msgdata['title'] = '系统消息';
                        $msgdata['content'] = '您推荐的用户跨界购买产品：' . $pname . '，您获得推广佣金￥' . $tuijianmoney . '，已成功转入余额';
                        $msgdata['tag'] = 2;
                        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
                        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
                        $Msgcentre->CreateMessege($msgdata);
                    }
                }

                //推荐人代理商提成
                $temp2['c_ucode'] = $tuijianucode;
                $buserinfo = M('Users')->where($temp2)->find();
                if ($scanpay_cityprofit > 0) {
                    if (count($buserinfo) > 0 && !empty($buserinfo['c_acode'])) {
                        $citycode = $buserinfo['c_acode'];
                        $citymoney = bcmul($commission, bcdiv($scanpay_cityprofit, 100, 2), 2);
                        if ($citymoney > 0) {
                            $beizhu = "您的微商【".$buserinfo['c_nickname']."】推荐的会员跨界购买产品：" . $pname;
                            $result = IGD('Order','Order')->faliRebate($citycode, $citymoney, $source, $value['c_detailid'], $beizhu, 0);
                            if ($result['code'] != 0) {
                                $db->rollback(); //不成功，则回滚
                                return $result;
                            }

                            //给用户发送相关消息
                            $msgdata['ucode'] = $citycode;
                            $msgdata['type'] = 0;
                            $msgdata['platform'] = 1;
                            $msgdata['sendnum'] = 1;
                            $msgdata['title'] = '系统消息';
                            $msgdata['content'] = '您的微商【'.$buserinfo['c_nickname'].'】推荐的会员跨界购买产品：' . $pname . '，您获得推广佣金￥' . $citymoney . '，已成功转入余额';
                            $msgdata['tag'] = 2;
                            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
                            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
                            $Msgcentre->CreateMessege($msgdata);
                        }
                    }
                }

                //推荐人区域代理商提成
                $temp3['c_ucode'] = $buserinfo['c_acode'];
                $quserinfo = M('Users')->where($temp3)->find();
                if ($scanpay_areaprofit > 0) {
                    if (count($quserinfo) > 0 && !empty($quserinfo['c_acode'])) {
                        $areacode = $quserinfo['c_acode'];
                        $areamoney = bcmul($commission, bcdiv($scanpay_areaprofit, 100, 2), 2);
                        if ($areamoney > 0) {
                            $beizhu = "您旗下的微商【".$buserinfo['c_nickname']."】推荐的会员跨界购买产品：" . $pname;
                            $result = IGD('Order','Order')->faliRebate($areacode, $areamoney, $source, $value['c_detailid'], $beizhu, 0);
                            if ($result['code'] != 0) {
                                $db->rollback(); //不成功，则回滚
                                return $result;
                            }

                            //给用户发送相关消息
                            $msgdata['ucode'] = $areacode;
                            $msgdata['type'] = 0;
                            $msgdata['platform'] = 1;
                            $msgdata['sendnum'] = 1;
                            $msgdata['title'] = '系统消息';
                            $msgdata['content'] = '您旗下的微商【'.$buserinfo['c_nickname'].'】推荐的会员跨界购买产品：' . $pname . '，您获得推广佣金￥' . $areamoney . '，已成功转入余额';
                            $msgdata['tag'] = 2;
                            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
                            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
                            $Msgcentre->CreateMessege($msgdata);
                        }
                    }
                }
            }

            //商家红包
             //查询系统配置
            $result = IGD('Common', 'Info')->GetSystemSet();
            if($result['code'] != 0){
                $db->rollback(); //不成功，则回滚
                return Message(1024, '系统配置不存在');
            }
            $sys_setting = $settinginfo['data'];

            $red_scale = $sys_setting['c_red_scale'];
            $redprice = bcmul($commission, bcdiv($red_scale, 100, 2), 2);
            if ($redprice > 0) {
                $value['money'] = $redprice;
                $result = IGD('Red','Activity')->AddRedNum($value,$orderinfo['c_acode']);
            }

            //给商家结算
            $shopprofit = $value['c_profit'];
            if ($shopprofit > 0) {

                $beizhu = "卖出商品" . $pname;
                $result = IGD('Order','Order')->faliRebate($orderinfo['c_acode'], $shopprofit, $source, $value['c_detailid'], $beizhu, 0);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }

                $msgdata['ucode'] = $orderinfo['c_acode'];
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['content'] = '您的产品：' . $pname . '，已经被购买确定成功，您获得利润￥' . $shopprofit . '，已成功转入余额';
                $msgdata['tag'] = 2;
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
                $Msgcentre->CreateMessege($msgdata);
            }

            //增加销量
            $saleproductwhere['c_pcode'] = $value['c_pcode'];
            $result = M('Product')->where($saleproductwhere)->setInc('c_salesnum', $value['c_pnum']);
            if (!$result) {
                return Message(1016, "销量增加失败");
            }

            //添加5公里商圈记录
            //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
            // $blogdata['ucode'] = $orderinfo['c_ucode'];
            // $blogdata['behavior'] = 4;
            // $blogdata['regionid'] = $value['c_pcode'];
            // $blogdata['tag'] = 4;
            // $blogdata['tagvalue'] = $value['c_pcode'];

            // //查询自己位置信息
            // $result1 = IGD('Servecentre','Serve')->GetLocation($orderinfo['c_ucode']);
            // $localtion = $result1['data'];

            // $longitude = $localtion['longitude'];
            // $latitude = $localtion['latitude'];
            // $address = $localtion['address'];

            // $blogdata['longitude'] = $longitude;
            // $blogdata['latitude'] = $latitude;
            // $blogdata['address'] = $address;
            // $blogdata['addtime'] = date('Y-m-d H:i:s', time());

            // $result = IGD('Servecentre','Serve')->Addlogs($blogdata);
        } 


        //扣除卡劵金额
        if ($orderinfo['c_bmoney'] > 0) {
            //查询卡劵
            $bw['c_id'] = $orderinfo['c_bid'];
            $binfo = M('A_user_coupons')->where($bw)->field('c_sign')->find();
            if ($binfo['c_sign'] == 2) {
                $User1 = IGD('Money', 'User');
                $parr1['ucode'] = $orderinfo['c_acode'];
                $parr1['money'] = $orderinfo['c_bmoney'];
                $parr1['source'] = 1;
                $parr1['key'] = $orderinfo['c_orderid'];
                $parr1['desc'] = "扣除用户生成订单使用卡劵抵扣的金额";
                $parr1['state'] = 1;
                $parr1['type'] = 0;
                $parr1['isagent'] = 0;
                $parr1['showimg'] = 'Uploads/settlementshow/gou1.png';
                $parr1['showtext'] = '扣除卡劵抵扣';
                $result = $User1->OptionMoney($parr1);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }
            }
        }

        //结算商家订单邮费
        $btfree = $orderinfo['c_free'];
        if ($btfree > 0) {            
            $beizhu = '卖出商品订单：'.$orderid.',返回邮费';
            $result = IGD('Order','Order')->faliRebate($orderinfo['c_acode'], $btfree, 1, $value['c_detailid'], $beizhu, 0);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            $msgdata['ucode'] = $orderinfo['c_acode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '卖出商品订单：'.$orderid.',返回邮费'.$btfree.'元';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $Msgcentre->CreateMessege($msgdata);
        }

        //写入平台利润记录
        $profitparr['orderid'] = $orderinfo['c_orderid'];
        $profitparr['acode'] = $orderinfo['c_acode'];
        $profitparr['type'] = 1;
        $result = IGD('System','Order')->WriteProfit($profitparr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }


        //给用户发送相关消息
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '“为你我用了半生的精力，漂洋过海来看你。”快把宝贝安全到达的消息告诉商家吧~';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        $msgdata['ucode'] = $orderinfo['c_acode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '“把一个人的温暖，转移到另一个人的胸膛。”您已有一笔订单确认收货~';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        $db->commit();
        return Message(0, '订单确认成功');
    }
}
