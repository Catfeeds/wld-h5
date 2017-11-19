<?php

/* 供货商城生成订单接口
 *
 *  */

class SupplyorderAgorder {

    //生成订单
    public function CreataOrder($parr) {
        $produce = objarray_to_array(json_decode($parr['produce']));
        $ucode = $parr['ucode'];
        $delivery = $parr['delivery'];
        $addressid = $parr['addressid'];
        $postscript = $parr['postscript'];
        $money = $parr['money'];
        $severtype = $parr['severtype'];

        $result = $this->splitProduct($produce, $ucode);

        if ($result["code"] != 0) {
            return $result;
        }
        $shop = $result['data'];

        if (empty($shop)) {
            return Message(1015, "您没有传入产品信息");
        }

        $info = $shop['value'];
        $free = $shop['freeprice'];
        $acode = $shop['acode'];

        if (empty($info)) {
            return Message(1015, "产品信息为空");
        }

        if ($delivery == 2) {
            $free = 0;
        }

        $db = M('');
        $db->startTrans();

        $orderid = CreateOrder('s');
        $result = $this->CreataOrderdetails($info, $orderid, $delivery);

        $totprice = 0;
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $totprice = $result['data']['totprice'];
        $balance = 0;
        $tempcount = $totprice + $free;

        if ($money > 0) {
            if ($money >= $tempcount) {
                $balance = $tempcount;
                $money = bcsub($money, $balance, 2);
            } else {
                $balance = $money;
                $money = 0;
            }
        }

        //生成订单
        $result = $this->CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free, $delivery, $acode,$severtype);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //生成订单地址
        $result = $this->CreataOrderAddress($orderid, $addressid);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $paystatu = 0;
        //支付成功代理产品
        if ($balance >= $tempcount) {
            $paystatu = 1;
            $where['c_orderid'] = $orderid;
            $orderinfo = M('Supplier_order')->where($where)->find();
            $orderdatail = M('Supplier_order_details')->where($where)->select();
            foreach ($orderdatail as $key => $value) {
                $whereinfo['c_ishow'] = 1;
                $whereinfo['c_isdele'] = 1;
                $whereinfo['c_pcode'] = $value['c_pcode'];
                $productinfo = M('Supplier_product')->where($whereinfo)->find();
                //一键代理产品
                if ($value['c_pnum'] >= $productinfo['c_piece'] && $productinfo['c_isagent'] == 1 && empty($orderinfo['c_scode'])) {
                    $result = IGD('Supplier','Agorder')->AgentAddproduct($productinfo, $orderinfo['c_ucode'], $count, $orderinfo['c_severtype']);
                    if ($result['code'] != 0) {
                        $db->rollback(); //不成功，则回滚
                        return $result;
                    } else {
                        $sign = 2;
                    }
                    $count++;
                }
            }
        }

        //提交事务
        $db->commit();

        if ($balance > 0) {
            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您提交的订单号：' . $orderid . '，抵扣余额￥' . $balance . '成功';
            $msgdata['tag'] = 2;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $Msgcentre->CreateMessege($msgdata);
        }
        $data['orderid'] = $orderid;
        $data['sign'] = $sign;
        $data['paystatu'] = $paystatu;
        return MessageInfo(0, "生成订单成功", $data);
    }

    //拆分商家和产品信息并计算阶梯价格
    public function splitProduct($product, $ucode) {

        //查询出所有产品信息
        $shop = array();
        $freeprice = 0;
        //进行拆分订单
        foreach ($product as $key => $value) {
            $whereinfo['c_pcode'] = $value['pcode'];
            $whereinfo['c_ishow'] = 1;
            $whereinfo['c_isdele'] = 1;
            $productinfo = M('Supplier_product')->where($whereinfo)->find();

            if (count($productinfo) <= 0) {
                return Message(1015, "产品信息不存在，不能生成订单");
            }

            $price = $productinfo['c_price'];
            $num = $productinfo['c_num'];
            if (!empty($value['mcode'])) {
                //查询商品价格
                $wheremodel['c_pcode'] = $value['pcode'];
                $wheremodel['c_mcode'] = $value['mcode'];
                $ProductModel = M('Supplier_product_model')->where($wheremodel)->find();
                if (count($ProductModel) <= 0) {
                    return Message(1015, "没有查询到该产品型号");
                } else {
                    $price = $ProductModel['c_price'];
                    $num = $ProductModel['c_num'];
                }
            }

            // 判断产品库存
            if ($num < $value['num']) {
                return Message(1015, "产品库存不足");
            }

            $oldcount = $this->Getoldproduct($ucode, $value['pcode']);
            $temp2 = $value['num'] + $oldcount;
            //判断是否存在阶梯价格
            $wherejieti['c_pcode'] = $productinfo['c_pcode'];
            $wherejieti['c_mcode'] = $value['mcode'];
            $wherejieti['c_minnum'] = array('ELT', $temp2);
            $wherejieti['c_maxnum'] = array('EGT', $temp2);
            $jietiinfo = M('Supplier_product_ladderprice')->where($wherejieti)->find();

            if (count($jietiinfo) > 0) {
                $price = $jietiinfo['c_price'];
            }

            $info = array();
            $info['pcode'] = $productinfo['c_pcode'];
            $info['ucode'] = $ucode;
            $info['price'] = $price;
            $info['pname'] = $productinfo['c_name'];
            $info['pmodel'] = $value['mcode'];
            $info['pmodel_name'] = $ProductModel['c_name'];
            $info['num'] = $value['num'];
            $info['pimg'] = $productinfo['c_pimg'];
            $info['pucode'] = $value['pucode'];

            $tempcount = $price * $value['num'];
            $singletotle = sprintf("%.2f", $tempcount);
            $info['singletotle'] = $singletotle;

            $info['isrebate'] = $productinfo['c_isrebate'];
            $info['rebate_proportion'] = $productinfo['c_rebate_proportion'];

            $info['freeprice'] = $productinfo['c_freeprice'];
            $shop['acode'] = $productinfo['c_ucode'];
            $shop['value'][] = $info;
            $shop['isagent'] = $productinfo['c_isagent'];

            if ($productinfo['c_isfree'] == 2) {
                $freeprice += $productinfo['c_freeprice'] * $value['num'];
            }
        }

        $shop['freeprice'] = $freeprice;
        return MessageInfo(0, "产品拆分成功", $shop);
    }

    //订单详情，作为订单展示使用
    public function GetOrderInfo($parr) {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $orderid = $parr['orderid'];

        if (!empty($ucode)) {
            $join = 'left join t_supplier as b on a.c_acode=b.c_ucode';
            $whereinfo['a.c_ucode'] = $ucode;
            $field = 'a.*,c_name as c_nickname,"" as c_rongyun_token';
        }

        if (!empty($acode)) {
            $join = 'left join t_users as b on a.c_ucode=b.c_ucode left join t_user_part as c on b.c_ucode=c.c_ucode';
            $whereinfo['a.c_acode'] = $acode;
            $field = 'a.*,b.c_nickname,c.c_rongyun_token';
        }

        $whereinfo['c_orderid'] = $orderid;
        $orderinfo = M('Supplier_order as a')->join($join)->where($whereinfo)->field($field)->find();

        if (count($orderinfo) <= 0) {
            return Message(0, "该订单信息不存在");
        }

        $whereinfo1['c_productstatus'] = 0;
        $whereinfo1['c_orderid'] = $orderinfo['c_orderid'];
        $detail = M('Supplier_order_details')->where($whereinfo1)->select();
        $orderinfo['scorestatu'] = 0;
        $po = 0;
        foreach ($detail as $key => $value) {
            if ($value['c_isevaluate'] == 1) {
                $po++;
            }
            $detail[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }
        if ($po == count($detail)) {
            $orderinfo['scorestatu'] = 1;
        }

        $orderinfo['detail'] = $detail;
        $whereaddress['c_orderid'] = $orderid;
        $orderinfo['address'] = M('Supplier_order_address')->where($whereaddress)->find();
        $orderinfo['desc'] ="24小时内未付款，系统将自动取消";

        return MessageInfo(0, "订单查询成功", $orderinfo);
    }

    /**
     *  查询单个订单详情
     *  @param detailid
     */
    public function FindDetailOne($parr) {
        $where['a.c_detailid'] = $parr['detailid'];
        $join = 'left join t_supplier_order as b on a.c_orderid=b.c_orderid';
        $field = 'a.*,b.c_acode';
        $data = M('Supplier_order_details as a')->join($join)->where($where)->field($field)->find();
        if (count($data) == 0) {
            return Message(1000, '产品详情不存在');
        }
        $data['c_pimg'] = GetHost() . '/' . $data['c_pimg'];
        return MessageInfo(0, '查询成功', $data);
    }

    //查询以前购买产品的数量
    public function Getoldproduct($ucode, $pcode) {
        $join = 'INNER JOIN t_supplier_order as b on a.c_orderid=b.c_orderid';
        $where['b.c_pay_state'] = 1;
        $where['b.c_order_state'] = 2;
        $where['b.c_deliverystate'] = 5;
        $where['b.c_ucode'] = $ucode;
        $where['a.c_pcode'] = $pcode;
        $count = M('Supplier_order_details as a')->join($join)->where($where)->sum('c_pnum');

        if (empty($count)) {
            $count = 0;
        }
        return $count;
    }

    //获取作为支付使用订单信息
    public function GetPayorderinfo($parr) {

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
        $orderinfo = M('Supplier_order')->where($whereinfo)->find();

        if (count($orderinfo) <= 0) {
            return Message(0, "该订单信息不存在");
        }

        $detail = M('Supplier_order_details')->where($whereinfo)->select();

        foreach ($detail as $key => $value) {
            $detail[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }

        $orderinfo['detail'] = $detail;
        return MessageInfo(0, "订单查询成功", $orderinfo);
    }

    //生成订单详情
    protected function CreataOrderdetails($product, $orderid, $delivery) {
        $int = 0;
        $totprice = 0;
        foreach ($product as $key => $v1) {
            $singletotle = $v1['singletotle'];
            $totprice += $singletotle;
            $temp = "sd" . $int;
            $int++;
            $detailid = CreateOrder($temp);

             //扣除产品的数量
            $tempwhere1['c_pcode'] = $v1['pcode'];
            $tempwhere1['c_num'] = array('egt', $v1['num']);
            $result = M('Supplier_product')->where($tempwhere1)->setDec('c_num', $v1['num']);
            if (!$result) {
                return Message(3005, "库存扣除失败");
            }

            //扣除所有代理产品的库存
            $agtempwhere['c_agent_pcode'] = $v1['pcode'];
            if (M('Product')->where($agtempwhere)->getField('c_id')) {
                $agtempwhere['c_num'] = array('egt', $v1['num']);
                $result = M('Product')->where($agtempwhere)->setDec('c_num', $v1['num']);
                if (!$result) {
                    return Message(1012, "代理库存扣除失败");
                }

                //扣除所有代理产品的库存
                $agtempwhere5['c_mcode'] = $v1['pmodel'];
                $agtempwhere5['c_num'] = array('egt', $v1['num']);
                $result = M('Product_model')->where($agtempwhere5)->setDec('c_num', $v1['num']);
                if (!$result) {
                    return Message(1012, "代理库存扣除失败");
                }

            }

            $tempwhere['c_pcode'] = $v1['pcode'];
            $tempwhere['c_mcode'] = $v1['pmodel'];
            $tempwhere['c_num'] = array('egt', $v1['num']);
            $result = M('Supplier_product_model')->where($tempwhere)->setDec('c_num', $v1['num']);
            if (!$result) {
                return Message(3005, "型号库存扣除失败");
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
            $tempdetails['c_pname'] = $v1['pname'];
            $tempdetails['c_pprice'] = $v1['price'];
            $tempdetails['c_pmodel'] = $v1['pmodel'];
            $tempdetails['c_pmodel_name'] = $v1['pmodel_name'];
            $tempdetails['c_pnum'] = $v1['num'];
            $tempdetails['c_ptotal'] = $singletotle;
            $tempdetails['c_pimg'] = $v1['pimg'];
            $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Supplier_order_details')->add($tempdetails);
            if (!$result) {
                return Message(3003, "生成订单详情失败");
            }
        }

        $date['totprice'] = $totprice;
        return MessageInfo(0, "订单详情生成成功", $date);
    }

    //创建订单信息
    protected function CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free, $delivery, $acode,$severtype) {

        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $ucode;
        $aorderinfo['c_acode'] = $acode;

        if ($balance > 0) {
            $aorderinfo['c_actual_price'] = $balance;
            //用户积分记录操作
            $rebatemoney = IGD('Common', 'User');
            $parr['ucode'] = $ucode;
            $parr['money'] = $balance;
            $parr['source'] = 4;
            $parr['key'] = $orderid;
            $parr['desc'] = "小蜜商城余额支付";
            $parr['state'] = 1;
            $parr['type'] = 0;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/gou1.png';
            $parr['showtext'] = '购物';
            $result = $rebatemoney->OptionMoney($parr);
            if ($result['code'] != 0) {
                return $result;
            }

            //写入支付记录表
            $paylog['c_orderid'] = $orderid;
            $paylog['c_payrule'] = 4;
            $paylog['c_money'] = $balance;
            $paylog['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Supplier_order_paylog')->add($paylog);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(3009, "支付记录操作失败");
            }
        }

        $countprice = $totprice + $free;
        if ($balance >= $countprice) {
            $aorderinfo['c_pay_state'] = 1;
            $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

            //计算详情
            //查询系统配置
            $settinginfo = IGD('Common', 'Info')->GetSystemSet();
            if ($settinginfo['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1001, '系统配置不存在');
            }
            $setting = $settinginfo['data'];

            //获取订单详情信息
            $wheredetail['c_orderid'] = $orderid;
            $orderdatail = M('Supplier_order_details')->where($wheredetail)->select();
            foreach ($orderdatail as $key => $value) {
                $supplyscale = bcmul($value['c_ptotal'],$setting['c_agent_scale'],2);
                $detailsave['c_commission'] = bcdiv($supplyscale,100,2);
                $detailsave['c_profit'] = $value['c_ptotal'] - bcdiv($supplyscale,100,2) + $value['c_free'];
                $detailwhere['c_detailid'] = $value['c_detailid'];
                $result = M('Supplier_order_details')->where($detailwhere)->save($detailsave);
                if (!$result) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1001, '修改订单详情失败');
                }
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '您提交的订单号：' . $orderid . '，已支付成功';
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);
        }

        $aorderinfo['c_pay_rule'] = 4;
        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_deliverystate'] = 0;
        $aorderinfo['c_free'] = $free;
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = $delivery;
        $aorderinfo['c_postscript'] = $postscript;
        $aorderinfo['c_severtype'] = $severtype;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Supplier_order')->add($aorderinfo);
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
        $result = M('Supplier_order_address')->add($orderaddress);
        if (!$result) {
            return Message(3007, '生成订单地址失败');
        }
        return Message(0, "订单地址生成成功");
    }

    //取消订单
    public function CancelOrder($parr) {
        $db = M('');
        $db->startTrans();

        $result = $this->OptionCancelOrder($parr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $db->commit();

        $agentorder['c_orderid'] = $parr['orderid'];
        $orderinfo = M('Supplier_order')->where($agentorder)->find();
        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '您的订单号：' . $parr['orderid'] . '，已取消成功';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $parr['orderid'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $parr['orderid'];
        $Msgcentre->CreateMessege($msgdata);
        return $result;
    }

    //取消订单操作
    public function OptionCancelOrder($parr)
    {
        if (substr($parr['orderid'],0,1) != 's') {
            $agentorder['c_agent_orderid'] = $parr['orderid'];
            $parr['orderid'] = M('Supplier_order')->where($agentorder)->getField('c_orderid');
            $asign = 1;
        }
        $whereorder['c_orderid'] = $parr['orderid'];
        $orderinfo = M('Supplier_order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1000, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 0 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1001, '该订单无法取消');
        }

        $update['c_order_state'] = 1;
        $result = M('Supplier_order')->where($whereorder)->save($update);
        if (!$result) {
            return Message(1001, '修改订单状态失败');
        }

        //返回用户余额
        if ($orderinfo['c_actual_price'] > 0 && $asign != 1) {
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $orderinfo['c_ucode'];
            $parr['money'] = $orderinfo['c_actual_price'];
            $parr['source'] = 4;
            $parr['key'] = $orderinfo['c_orderid'];
            $parr['desc'] = "取消订单余额支付";
            $parr['state'] = 1;
            $parr['type'] = 1;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/ding.png';
            $parr['showtext'] = '订单取消';
            $result = $rebatemoney->OptionMoney($parr);
            if ($result['code'] != 0) {
                return $result;
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $orderinfo['c_ucode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您的订单号：' . $parr['c_orderid'] . '已取消成功，退还抵扣余额￥' . $orderinfo['c_actual_price'] . '成功';
            $msgdata['tag'] = 2;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $Msgcentre->CreateMessege($msgdata);
        }

        //获取订单详情信息
        $detailslist = M('Supplier_order_details')->where($whereorder)->select();
        foreach ($detailslist as $key => $value) {
            //返回库存
            $productwhere['c_pcode'] = $value['c_pcode'];
            $result = M('Supplier_product')->where($productwhere)->setInc('c_num', $value['c_pnum']);
            if (!$result) {
                return Message(3007, '返回产品库存失败');
            }

            //返回所有代理产品的库存
            $agtempwhere['c_agent_pcode'] = $value['c_pcode'];
            if (M('Product')->where($agtempwhere)->getField('c_id')) {
                $result = M('Product')->where($agtempwhere)->setInc('c_num', $value['c_pnum']);
                if (!$result) {
                    return Message(1012, "代理库存返回失败");
                }

                //返回所有代理产品的库存
                $agtempwhere5['c_mcode'] = $value['c_pmodel'];
                $result = M('Product_model')->where($agtempwhere5)->setInc('c_num', $value['c_pnum']);
                if (!$result) {
                    return Message(1012, "代理库存返回失败");
                }
            }

            $productwhere['c_mcode'] = $value['c_pmodel'];
            $result = M('Supplier_product_model')->where($productwhere)->setInc('c_num', $value['c_pnum']);
            if (!$result) {
                return Message(3007, '返回型号库存失败');
            }
        }
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
        $orderinfo = M('Supplier_order')->where($whereorder)->find();

        if (count($orderinfo) == 0) {
            return Message(1000, '订单查询失败');
        }

        if ($orderinfo['c_pay_state'] == 1  || $orderinfo['c_order_state'] == 1) {
            return Message(1001, '订单已经支付或已取消');
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
        $orderinfo['c_actual_price'] = $countzong;
        $orderinfo['c_pay_state'] = 1;
        $orderinfo['c_paytime'] = date('Y-m-d H:i:s', time());
        $result = M('Supplier_order')->where($whereorder)->save($orderinfo);
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
        $result = M('Supplier_order_paylog')->add($paylog);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(3009, "支付记录操作失败");
        }

        $where['c_orderid'] = $parr['orderid'];
        $orderdatail = M('Supplier_order_details')->where($where)->select();

        //查询系统配置
        $settinginfo = IGD('Common', 'Info')->GetSystemSet();
        if ($settinginfo['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1001, '系统配置不存在');
        }
        $count = 1;
        $setting = $settinginfo['data'];
        foreach ($orderdatail as $key => $value) {
            $supplyscale = bcmul($value['c_ptotal'],$setting['c_agent_scale'],2);
            $detailsave['c_commission'] = bcdiv($supplyscale,100,2);
            $detailsave['c_profit'] = $value['c_ptotal'] - bcdiv($supplyscale,100,2) + $value['c_free'];
            $detailwhere['c_detailid'] = $value['c_detailid'];
            $result = M('Supplier_order_details')->where($detailwhere)->save($detailsave);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1001, '修改订单详情失败');
            }

            $whereinfo['c_ishow'] = 1;
            $whereinfo['c_isdele'] = 1;
            $whereinfo['c_pcode'] = $value['c_pcode'];
            $productinfo = M('Supplier_product')->where($whereinfo)->find();

            //一键代理产品
            if ($value['c_pnum'] >= $productinfo['c_piece'] && $productinfo['c_isagent'] == 1 && empty($orderinfo['c_scode'])) {
                $result = IGD('Supplier','Agorder')->AgentAddproduct($productinfo, $orderinfo['c_ucode'], $count, $orderinfo['c_severtype']);
                if ($result['code'] != 0) {
                    return $result;
                } else {
                    $sign = 2;
                }
                $count++;
            }
        }

        $db->commit();

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '您提交的订单号：' . $orderid . '，已支付成功';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);
        $data['sign'] = $sign;
        return MessageInfo(0, '支付成功',$data);
    }

    //确认订单
    public function Confirmorder($parr) {
        $db = M('');
        $db->startTrans();

        $result = $this->OptionConfirmorder($parr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $db->commit();
        $agentorder['c_orderid'] = $parr['orderid'];
        $orderinfo = M('Supplier_order')->where($agentorder)->find();
        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '您的订单号：' . $parr['orderid'] . '，已确认收货成功';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $parr['orderid'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $parr['orderid'];
        $Msgcentre->CreateMessege($msgdata);
        return $result;
    }

    //确认收货操作
    public function OptionConfirmorder($parr)
    {
        if (substr($parr['orderid'],0,1) != 's') {
            $agentorder['c_agent_orderid'] = $parr['orderid'];
            $parr['orderid'] = M('Supplier_order')->where($agentorder)->getField('c_orderid');
            $parr['sign'] = 0;
        } else {
            $parr['sign'] = 1;
        }
        //获取订单信息
        $whereorder['c_orderid'] = $parr['orderid'];
        $orderinfo = M('Supplier_order')->where($whereorder)->find();

        if (count($orderinfo) == 0) {
            return Message(3009, "没有查询到该订单");
        }

        if ($orderinfo['c_pay_state'] != 1 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 2) {
            return Message(3010, "该订单未支付，不能进行确认");
        }

        $whereorder['c_orderid'] = $parr['orderid'];
        $update['c_deliverystate'] = 5;
        $update['c_confirmtime'] = date('Y-m-d H:i:s');
        $result = M('Supplier_order')->where($whereorder)->save($update);
        if (!$result) {
            return Message(1001, '修改订单状态失败');
        }

        //查询系统配置
        $settinginfo = IGD('Common', 'Info')->GetSystemSet();
        $setting = $settinginfo['data'];
        if ($settinginfo['code'] != 0) {
            return Message(1001, '系统配置不存在');
        } else {
            $areascale = $setting['c_area_scale'];
            $cityscale = $setting['c_city_scale'];
        }


        $where['c_orderid'] = $parr['orderid'];
        $orderdatail = M('Supplier_order_details')->where($where)->select();
        $count = 1;
        $Msgcentre = IGD('Msgcentre', 'Message');
        foreach ($orderdatail as $key => $value) {
            $whereinfo['c_ishow'] = 1;
            $whereinfo['c_isdele'] = 1;
            $whereinfo['c_pcode'] = $value['c_pcode'];
            $productinfo = M('Supplier_product')->where($whereinfo)->find();

            //一键代理产品
            if ($value['c_pnum'] >= $productinfo['c_piece'] && $productinfo['c_isagent'] == 1 && empty($orderinfo['c_scode'])) {
                $result = IGD('Supplier','Agorder')->AgentAddproduct($productinfo, $orderinfo['c_ucode'], $count, $orderinfo['c_severtype']);
                if ($result['code'] != 0) {
                    return $result;
                }
                $count++;
            }

            //分配供货商利润
            if ($value['c_profit'] > 0) {
                $supplyparr['ucode'] = $orderinfo['c_acode'];
                $supplyparr['money'] = $value['c_profit'];
                $supplyparr['source'] = 4;
                $supplyparr['key'] = $value['c_detailid'];
                $supplyparr['desc'] = '小蜜商城用户购买商品获得';
                $supplyparr['state'] = 1;
                $supplyparr['type'] = 1;
                $supplyparr['showimg'] = 'Uploads/settlementshow/ding.png';
                $supplyparr['showtext'] = '订单收入';
                $result = IGD('Supplier','Agorder')->OptionMoney($supplyparr);
                if ($result['code'] != 0) {
                    return $result;
                }
            }
            $commission = $value['c_commission'];
            $source = 15; $pname = $value['c_pname'];
            if ($commission > 0) {
                //给商家推荐人分成
                if ($parr['sign'] == 1) {
                    $userinfowhere['c_ucode'] = $orderinfo['c_ucode'];
                } else {
                    $userinfowhere['c_ucode'] = $orderinfo['c_scode'];
                }
                $userinfotuijian = M('Users_tuijian')->where($userinfowhere)->find();
                if (count($userinfotuijian) != 0) {
                    $tuijianmoney = bcmul($commission, bcdiv($setting['c_agent_refreescale'], 100, 2), 2);
                    $tuijianucode = $userinfotuijian['c_pcode'];
                    $beizhu = '推荐的商家在小蜜商城购买产品'.$pname;
                    $result = IGD('Order','Order')->faliRebate($tuijianucode, $tuijianmoney, $source, $value['c_detailid'], $beizhu, 0);
                    if ($result['code'] != 0) {
                        return $result;
                    }

                    //给用户发送相关消息
                    $msgdata['ucode'] = $tuijianucode;
                    $msgdata['type'] = 0;
                    $msgdata['platform'] = 1;
                    $msgdata['sendnum'] = 1;
                    $msgdata['title'] = '系统消息';
                    $msgdata['content'] = '您推荐的微商购买产品' . $pname . '，您获得推荐佣金￥' . $tuijianmoney . '，已成功转入余额';
                    $msgdata['tag'] = 2;
                    $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
                    $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
                    $Msgcentre->CreateMessege($msgdata);
                }

                //给商家代理商提成
                if ($parr['sign'] == 1) {
                    $temp2['c_ucode'] = $orderinfo['c_ucode'];
                } else {
                    $temp2['c_ucode'] = $orderinfo['c_scode'];
                }
                $buserinfo = M('Users')->where($temp2)->find();
                if ($cityscale > 0) {
                    if (count($buserinfo) > 0 && !empty($buserinfo['c_acode'])) {
                        $citycode = $buserinfo['c_acode'];
                        $citymoney = bcmul($commission, bcdiv($cityscale, 100, 2), 2);
                        $beizhu = "微商在小蜜商城购买产品" . $pname;
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
                        $msgdata['content'] = '用户在您推荐的微商下购买产品' . $pname . '，您获得提成佣金￥' . $citymoney . '，已成功转入余额';
                        $msgdata['tag'] = 2;
                        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
                        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
                        $Msgcentre->CreateMessege($msgdata);
                    }
                }

                //给商家区域代理商提成
                $temp3['c_ucode'] = $buserinfo['c_acode'];
                $quserinfo = M('Users')->where($temp3)->find();
                if ($areascale > 0) {
                    if (count($quserinfo) > 0 && !empty($quserinfo['c_acode'])) {
                        $areacode = $quserinfo['c_acode'];
                        $areamoney = bcmul($commission, bcdiv($areascale, 100, 2), 2);
                        $beizhu = "微商在小蜜商城购买产品" . $pname;
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
                        $msgdata['content'] = '用户在您推荐的代理商下的微商里购买产品' . $pname . '，您获得提成佣金￥' . $areamoney . '，已成功转入余额';
                        $msgdata['tag'] = 2;
                        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
                        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
                        $Msgcentre->CreateMessege($msgdata);
                    }
                }
            }


            //增加销量
            $saleproductwhere['c_pcode'] = $value['c_pcode'];
            $result = M('Supplier_product')->where($saleproductwhere)->setInc('c_salesnum', $value['c_pnum']);
            if (!$result) {
                return Message(1016, "销量增加失败");
            }

        }

        return Message(0, '确认收货成功');
    }

    //订单发货
    public function Senddelivery($parr) {
        $db = M('');
        $db->startTrans();

        $result = $this->OptionSenddelivery($parr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
        $orderinfo = $result['data'];

        // 同步商城发货
        if (!empty($result['data']['c_agent_orderid'])) {
            $parr['orderid'] = $result['data']['c_agent_orderid'];
            $result = IGD('Order','Order')->OptionSenddelivery($parr);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
            $orderinfo = $result['data'];
        }

        $db->commit();

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '您的订单号：' . $orderinfo['c_orderid'] . '，卖家已经发货，请注意查收';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $orderinfo['c_orderid'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderinfo['c_orderid'];
        $Msgcentre->CreateMessege($msgdata);
        return $result;
    }

    //订单发货操作
    public function OptionSenddelivery($parr) {
        if (substr($parr['orderid'],0,1) != 's') {
            $agentorder['c_agent_orderid'] = $parr['orderid'];
            $parr['orderid'] = M('Supplier_order')->where($agentorder)->getField('c_orderid');
        }
        $orderid = $parr['orderid'];
        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Supplier_order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1022, '该订单不存在');
        }
        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 1 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1022, '该订单不能进行发货');
        }

        $save['c_deliverytime'] = date('Y-m-d H:i:s');
        $save['c_deliverystate'] = 2;
        $save['c_expressname'] = $parr['expressname'];
        $save['c_expressnum'] = $parr['expressnum'];
        $result = M('Supplier_order')->where($whereorder)->save($save);

        if ($result <= 0) {
            return Message(1023, '订单发货失败');
        }

        return MessageInfo(0, '订单发货成功',$orderinfo);
    }

    // 提醒发货
    function RemindDeliver($parr) {
        $orderid = $parr['orderid'];
        $whereorder['c_orderid'] = $orderid;

        $orderinfo = M('Supplier_order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1021, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 1 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1022, '该订单不能进行提醒发货');
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_acode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '订单号为：' . $orderid . '的买家已提醒您发货，请尽快发货，提升服务品质！';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
        $result = $Msgcentre->CreateMessege($msgdata);

        if ($result['code'] != 0) {
            return Message(1002, '操作失败');
        }

        return Message(0, '操作成功');
    }

    //判断商品返利
    public function CheckRebate($detaillist, $ucode, $acode,$activity_id) {
        //查询系统配置
        $settinginfo = IGD('Common', 'Info')->GetSystemSet();
        if ($settinginfo['code'] != 0) {
            return Message(1001, '系统配置不存在');
        }
        $setting = $settinginfo['data'];

        foreach ($detaillist as $key => $value) {
            $supplyscale = bcmul($value['c_ptotal'],$setting['c_agent_scale'],2);
            $detailsave['c_commission'] = bcdiv($supplyscale,100,2);
            $detailsave['c_profit'] = $value['c_ptotal'] - bcdiv($supplyscale,100,2) + $value['c_free'];
            $detailwhere['c_detailid'] = $value['c_detailid'];
            $result = M('Supplier_order_details')->where($detailwhere)->save($detailsave);
            if (!$result) {
                return Message(1001, '修改订单详情失败');
            }
        }
        return Message(0,'返佣成功');
    }
}
