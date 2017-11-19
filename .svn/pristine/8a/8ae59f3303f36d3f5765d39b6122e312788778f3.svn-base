<?php

/* 代理商城接口
 *
 */
class AgentAgorder {

    //获取代理产品列表
    public function GetproductList($pageindex, $pagesize, $type) {

        if ($pageindex == 0 || $pageindex == '') {
            $pageindex = 1;
        }

        $countPage = ($pageindex - 1) * $pagesize;

        $whereinfo = "c_ishow =1 and c_isdele=1";
        if (!empty($type)) {
            $whereinfo = " and c_categoryid=$type";
        }

        $list = M('Agent_product')->where($whereinfo)->order('c_id desc')->limit($countPage, $pagesize)->select();


        if (count($list) <= 0) {
            return false;
        }

        foreach ($list as $k => $v) {
            $list[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimg'];
        }

        $dataCount = M('Agent_product')->where($whereinfo)->count();
        $pageCount = ceil($dataCount / $pagesize);

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return $listinfo;
    }

    //代理商品详情
    public function GetProductInfo($pcode) {

        if (empty($pcode)) {
            return false;
        }

        $whereinfo = "c_ishow =1 and c_isdele=1";

        $whereinfo.=" and c_pcode=$pcode";
        $productinfo = M('Agent_product')->where($whereinfo)->find();

        if (count($productinfo) <= 0) {
            return false;
        }

        $productinfo['c_pimg'] = GetHost() . '/' . $productinfo['c_pimg'];

        //查询产品图片列表
        $imgwhere['c_pcode'] = $pcode;
        $imglist = M('Agent_product_img')->where($imgwhere)->select();

        foreach ($imglist as $k => $v) {
            $imglist[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimgepath'];
        }

        $productinfo['imglist'] = $imglist;
        //查询产品型号
        $modlewhere['c_pcode'] = $pcode;
        $modellist = M('Agent_product_model')->where($modlewhere)->select();
        $productinfo['modellist'] = $modellist;

        return $productinfo;
    }

    //一键代理产品
    public function GetProductAgent($product, $ucode) {

        $whereinfo['c_ishow'] = 1;
        $whereinfo['c_isdele'] = 1;
        $whereinfo['c_pcode'] = array('in', $product);

        $list = M('Agent_product')->where($whereinfo)->select();

        $db = M('');
        $db->startTrans();

        $count = 1;
        foreach ($list as $k => $v) {

            $result = $this->AgentAddproduct($v, $ucode, $count);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            $count++;
        }

        //提交事务
        $db->commit();
        return Message(0, "代理产品成功");
    }

    //生成订单
    public function CreataOrder($product, $ucode, $balance, $postscript, $addressid) {
        $free = 0;
        //判断用户余额是否充足
        $userwhere['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($userwhere)->field('c_ucode,c_shop,c_money')->find();

        if ($userinfo['c_shop'] != 1) {
            return Message(3010, "您还没有开店，暂时不能到进行购买");
        }

        if ($userinfo['c_money'] < $balance) {
            return Message(3011, "您的余额不足");
        }

        $orderid = CreateOrder(2);
        $db = M('');
        $db->startTrans();
        //创建订单详情
        $result = $this->CreataOrderdetails($product, $orderid);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
        $totprice = $result['data'];
        // //生成订单
        $result = $this->CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
        //创建订单地址
        $result = $this->CreataOrderAddress($orderid, $addressid);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
        $db->commit();
        return Message(0, '生成订单成功');
    }

    //取消订单
    public function CancelOrder($orderid) {

        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Agent_order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1000, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 0 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1001, '该订单无法取消');
        }

        $orderdb = M('');
        $orderdb->startTrans();
        $update['c_order_state'] = 1;
        $result = M('Agent_order')->where($whereorder)->save($update);
        if (!$result) {
            $orderdb->rollback(); //不成功，则回滚
            return Message(1001, '修改订单状态失败');
        }

        //获取订单详情信息
        $detailslist = M('Agent_order_details')->where($whereorder)->select();
        foreach ($detailslist as $key => $value) {
            //返回库存
            $productwhere['c_pcode'] = $value['c_pcode'];
            if (empty($value['c_pmodel'])) {
                $result = M('Agent_product')->where($productwhere)->setInc('c_num', $value['c_pnum']);
            } else {
                $productwhere['c_mcode'] = $value['c_pmodel'];
                $result = M('Agent_product_model')->where($productwhere)->setInc('c_num', $value['c_pnum']);
            }

            if (!$result) {
                $orderdb->rollback(); //不成功，则回滚
                return Message(3007, '返回产品库存失败');
            }
        }

        //返回功勋orderid,state,reason
        $rebatemoney = IGD('Money', 'User');
        $parr['orderid'] = $orderid;
        $parr['state'] = 2;
        $parr['reason'] = "取消订单";
        $parr['showimg'] = 'Uploads/settlementshow/ding.png';
        $parr['showtext'] = '订单取消';
        $result = $rebatemoney->OptionMoney($parr);
        if ($result['code'] != 0) {
            return $result;
        }

        $orderdb->commit();
        return Message(0, '订单取消成功');
    }

    //支付订单
    public function PayOrder($parr) {

        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
        $actualprice = $parr['actualprice'];
        $thirdpartynum = $parr['thirdpartynum'];

        //获取订单信息
        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Agent_order')->where($whereorder)->find();

        if (count($orderinfo) == 0) {
            return Message(1000, '订单查询失败');
        }

        if ($orderinfo['c_pay_state'] == 1 || $orderinfo['c_pay_state'] == 2) {
            return Message(1001, '订单已经支付');
        }

        $db = M('');
        $db->startTrans();
        $countzong = bcadd($actualprice, $orderinfo['c_actual_price'], 2);
        $shijizong = bcadd($orderinfo['c_total_price'], $orderinfo['c_free'], 2);

        if ($countzong < $shijizong) {
            $db->rollback(); //不成功，则回滚
            return Message(3008, "您支付的余额不足");
        }

        //修改用户订单状态
        $orderinfo['c_pay_rule'] = $payrule;
        $orderinfo['c_pay_state'] = 1;
        $orderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

        $result = M('Agent_order')->where($whereorder)->save($orderinfo);

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(3008, "订单状态修改失败");
        }

        //写入支付记录表
        $paylog['c_orderid'] = $orderid;
        $paylog['c_payrule'] = $payrule;
        $paylog['c_money'] = $actualprice;
        $paylog['c_thirdparty'] = $thirdpartynum;
        $paylog['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Agent_order_paylog')->add($paylog);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(3009, "支付记录操作失败");
        }
        $db->commit();
        return Message(0, '生成订单成功');
    }

    //确认订单
    public function Confirmorder($parr) {

        //获取订单信息
        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Agent_order')->where($whereorder)->find();

        if (count($orderinfo) == 0) {
            return Message(3009, "没有查询到该订单");
        }

        if ($orderinfo['c_pay_state'] != 1) {
            return Message(3010, "该订单未支付，不能进行确认");
        }

        $db = M('');
        $db->startTrans();

        $whereorder['c_orderid'] = $parr['orderid'];
        $update['c_deliverystate'] = 5;
        $update['c_confirmtime'] = date('Y-m-d H:i:s');
        $result = M('Agent_order')->where($whereorder)->save($update);
        if (!$result) {
            $orderdb->rollback(); //不成功，则回滚
            return Message(1001, '修改订单状态失败');
        }

        // 送出用户返利金额
        $param['orderid'] = $parr['orderid'];
        $param['state'] = 1;
        $rebatemoney = IGD('Money', 'User')->SendUserMoney($param);
        if ($rebatedata['code'] != 0) {
            $orderdb->rollback(); //不成功，则回滚
            return Message(1001, '用户返利金额送出失败');
        }

        $orderdb->commit();
        return Message(0, '确认收货成功');
    }

    //一件提交订单
    public function SubmitOrder($orderid) {

        $whereinfo['c_orderid'] = array('in', $orderid);

        $list = M('Order')->where($whereinfo)->select();

        $db = M('');
        $db->startTrans();

        $count = 1;
        foreach ($list as $k => $v) {
            //判断订单是否符合规范
            if ($v['c_pay_state'] != 1 || $v['c_order_state'] != 2 || $v['c_order_state'] == 0) {
                $db->rollback(); //不成功，则回滚
                return 1;
            }
            $prefix = "1" . $count;
            $result = $this->CreatAgentOrder($v, $prefix);

            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
            $count++;
        }

        //提交事务
        $db->commit();
        return Message(0, "生成订单成功");
    }

    //一键代理添加产品
    protected function AgentAddproduct($v, $ucode, $count) {

        $pcode = CreateOrder($count);
        $info = array();
        $info['c_pcode'] = $pcode;
        $info['c_ucode'] = $ucode;
        $info['c_name'] = $v['c_name'];
        $info['c_desc'] = $v['c_desc'];
        $info['c_ismodel'] = $v['c_ismodel'];
        $info['c_pimg'] = $v['c_pimg'];
        $info['c_num'] = $v['c_num'];
        $info['c_price'] = $v['c_minprice'];
        $info['c_price'] = $v['c_minprice'];
        $info['c_categoryid'] = $v['c_categoryid'];
        $info['c_isfree'] = $v['c_isfree'];
        $info['c_isdele'] = $v['c_isdele'];
        $info['c_isshoptuijian'] = 0;
        $info['c_isrebate'] = 0;
        $info['c_isspread'] = 0;
        $info['c_isagent'] = 1;
        $info['c_agent_pcode'] = $v['c_pcode'];
        $info['c_addtime'] = date('Y-m-d H:i:s', time());
        $info['c_updatetime'] = date('Y-m-d H:i:s', time());

        $result = M('Product')->add($info);

        if (!$result) {
            return Message(3006, "一键代理添加产品失败");
        }
        $count ++;

        $dailipcode = $v['c_pcode'];
        //插入图片信息
        $sql = "insert into t_product_img (c_pcode,c_pimgepath,c_sign,c_createtime,c_updatetime) "
                . " select $pcode,c_pimgepath,c_sign,c_createtime,c_updatetime from t_agent_product_img where c_pcode='$dailipcode'";
        $result = $db->execute($sql);

        if (!$result) {
            return Message(3007, "一键代理添加产品图片失败");
        }

        //插入类型
        $sql1 = "insert into t_product_model (c_mcode,c_pcode,c_name,c_price,c_num,c_addtime) "
                . " select c_mcode,$pcode,c_name,c_price,c_num,c_addtime from t_agent_product_model where c_pcode='$dailipcode'";

        $result = $db->execute($sql1);

        if (!$result) {
            return Message(3008, "一键代理添加产品型号失败");
        }
    }

    //一键创建代理订单
    protected function CreatAgentOrder($orderinfo, $prefix) {

        $orderid = CreateOrder($prefix);

        $wheredetails['c_orderid'] = $orderinfo['c_orderid'];
        //获取订单详情
        $detalilist = M('Order_details')->where($wheredetails)->select();

        //计算订单的总价
        $totprice = 0;

        foreach ($detalilist as $d => $v1) {

            $singletotle = 0;
            $price = 0;
            //查询代理商的产品价格
            if (empty($v1['c_pmodel'])) {
                $tempwhere['c_pcode'] = $v1['c_agent_pcode'];
                $productinfo = M('Agent_product')->where($tempwhere)->find();

                if (count($productinfo) == 0) {
                    return Message(3001, "没有查询到产品信息");
                }
                $price = $productinfo['c_price'];
            } else {

                $tempwhere['c_pcode'] = $v1['c_agent_pcode'];
                $tempwhere['c_mcode'] = $v1['c_pmodel'];
                $productinfomodel = M('Agent_product_model')->where($tempwhere)->find();

                if (count($productinfomodel) == 0) {
                    return Message(3002, "没有查询到该产品的模型信息");
                }
                $price = $productinfomodel['c_price'];
            }

            $tempcount = $price * $v1['c_pnum'];
            $singletotle = sprintf("%.2f", $tempcount);
            //生成订单详情
            $tempdetails = array();
            $tempdetails['c_orderid'] = $orderid;
            $tempdetails['c_pcode'] = $v1['c_agent_pcode'];
            $tempdetails['c_pprice'] = $price;
            $tempdetails['c_pmodel'] = $v1['c_pmodel'];
            $tempdetails['c_pmodel_name'] = $v1['c_pmodel_name'];
            $tempdetails['c_pnum'] = $v1['c_pnum'];
            $tempdetails['c_ptotal'] = $singletotle;
            $tempdetails['c_pimg'] = $v1['c_pimg'];
            $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Agent_order_details')->add($tempdetails);

            if (!$result) {
                return Message(3003, "生成订单详情失败");
            }

            //生成订单地址
            $temporderid = $v1['c_orderid'];
            $sql = "insert into t_agent_order_address (c_orderid,c_consignee,c_telphone,c_address,c_province,c_cityname,c_district,c_provinceid,c_cityid,c_districtid) "
                    . "select $orderid,c_consignee,c_telphone,c_address,c_province,c_cityname,c_district,c_provinceid,"
                    . "c_cityid,c_districtid from t_order_address where c_orderid='$temporderid'";

            $result = $db->execute($sql1);
            if (!$result) {
                return Message(3004, "创建用户地址失败");
            }

            $totprice +=$singletotle;
        }

        //生成订单
        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $orderinfo['c_acode'];
        $aorderinfo['c_pay_state'] = 0;
        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_deliverystate'] = 0;
        $aorderinfo['c_free'] = 0;
        $aorderinfo['c_ucode'] = $orderinfo['c_acode'];
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = 1;
        $aorderinfo['c_postscript'] = $orderinfo['c_postscript'];
        $aorderinfo['c_agent_orderid'] = $orderinfo['c_orderid'];
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Agent_order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }

        return Message(0, "创建订单成功");
    }

    //生成订单详情
    protected function CreataOrderdetails($product, $orderid) {
        $totprice = 0;
        foreach ($product as $key => $v1) {
            $singletotle = 0;
            $price = 0;
            //查询代理商的产品价格
            $tempwhere1['c_pcode'] = $v1['c_pcode'];
            $productinfo = M('Agent_product')->where($tempwhere1)->find();
            if (count($productinfo) == 0) {
                return Message(3001, "没有查询到产品信息");
            }

            if (empty($v1['c_mcode'])) {
                $price = $productinfo['c_price'];

                if ($v1['num'] > $productinfo['c_num']) {
                    return Message(3004, "产品库存不足");
                }
                //扣除产品的数量
                $result = M('Agent_product')->where($tempwhere1)->setDec('c_num', $v1['num']);

                if (!$result) {
                    return Message(3005, "库存扣除失败");
                }
            } else {
                $tempwhere['c_pcode'] = $v1['c_pcode'];
                $tempwhere['c_mcode'] = $v1['c_pmodel'];
                $productinfomodel = M('Agent_product_model')->where($tempwhere)->find();

                if (count($productinfomodel) == 0) {
                    return Message(3002, "没有查询到该产品的型号信息");
                }

                if ($v1['num'] > $productinfomodel['c_num']) {
                    return Message(3004, "产品库存不足");
                }

                $result = M('Agent_product_model')->where($tempwhere)->setDec('c_num', $v1['num']);
                if (!$result) {
                    return Message(3005, "库存扣除失败");
                }
                $price = $productinfomodel['c_price'];
            }

            //计算价格
            $tempcount = $price * $v1['num'];
            $singletotle = sprintf("%.2f", $tempcount);
            $totprice+=$singletotle;
            //写入订单详情表
            $tempdetails = array();
            $tempdetails['c_orderid'] = $orderid;
            $tempdetails['c_pcode'] = $productinfo['c_pcode'];
            $tempdetails['c_pprice'] = $price;
            $tempdetails['c_pmodel'] = $v1['c_pmodel'];
            $tempdetails['c_pmodel_name'] = $v1['c_pmodel_name'];
            $tempdetails['c_pnum'] = $v1['c_pnum'];
            $tempdetails['c_ptotal'] = $singletotle;
            $tempdetails['c_pimg'] = $productinfo['c_pimg'];
            $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Agent_order_details')->add($tempdetails);
            if (!$result) {
                return Message(3003, "生成订单详情失败");
            }
        }

        return MessageInfo(0, "订单详情生成成功", $totprice);
    }

    //创建订单信息
    protected function CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free) {

        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $ucode;

        if ($balance > 0) {
            $aorderinfo['c_actual_price'] = $balance;
            //用户积分记录操作
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $ucode;
            $parr['money'] = $balance;
            $parr['source'] = 4;
            $parr['key'] = $orderid;
            $parr['desc'] = "余额支付";
            $parr['state'] = 0;
            $parr['type'] = 0;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/gou1.png';
            $parr['showtext'] = '购物';
            $result = $rebatemoney->OptionMoney($parr);
            if ($result['code'] != 0) {
                return $result;
            }
        }

        $countprice = $totprice + $free;
        if ($balance >= $countprice) {
            $aorderinfo['c_pay_state'] = 1;
            $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());
        }

        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_deliverystate'] = 0;
        $aorderinfo['c_free'] = 0;
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = 1;
        $aorderinfo['c_postscript'] = $postscript;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Agent_order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }
        return Message(0, "订单创建成功");
    }

    //生成订单地址
    protected function CreataOrderAddress($orderid, $addressid) {
        $addresswhere['c_id'] = $addressid;
        $useraddress = M('Users_address')->where($addresswhere)->find();
        if (count($useraddress) == 0) {
            return Message(3006, '查询用户地址失败');
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
        $result = M('Agent_order_address')->add($orderaddress);
        if (!$result) {
            return Message(3007, '生成订单地址失败');
        }
        return Message(0, "订单地址生成成功");
    }

}
