<?php

/**
 * 用户订单中心接口
 */
class OrderOrder {

    /**
     *  用户提交订单接口(本版本采用单个商家的同种类型进行提交订单)
     *  @param  ucode,delivery,addressid,postscript,money,couponid,model
     *  produce 用户选择的产品信息json格式(acode,mcode,pcode,num,pucode)
     *
     */
    public function CreataOrder($parr) {
        $produce = objarray_to_array(json_decode($parr['produce']));
        $ucode = $parr['ucode'];
        $delivery = $parr['delivery'];
        $addressid = $parr['addressid'];
        $postscript = $parr['postscript'];
        $money = $parr['money'];
        $couponid = $parr['couponid'];
        $model = $parr['model'];

        if (empty($ucode)) {
            return Message(1009, "请先登录再操作");
        }

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
        $isagent = $shop['isagent'];
        $acode = $shop['acode'];

        if (empty($info)) {
            return Message(1015, "产品信息为空");
        }

        // if ($isagent == 1) {
        //     $free = 0;
        // }

        if ($delivery == 2) {
            $free = 0;
        }

        $db = M('');
        $db->startTrans();

        $orderid = CreateOrder();
        $result = $this->CreataOrderdetails($info, $orderid, $delivery);

        $totprice = 0;
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $totprice = $result['data']['totprice'];
        $balance = 0;
        $tempcount = $totprice + $free;

        $paystatu = 0;
        if ($money > 0) {
            if ($money >= $tempcount) {
                $paystatu = 1;
                $balance = $tempcount;
                $money = bcsub($money, $balance, 2);
            } else {
                $balance = $money;
                $money = 0;
            }
        }

        //生成订单
        $result = $this->CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free, $isagent, $delivery, $acode, $model);
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

        $countprice = $totprice + $free;
        if ($balance >= $countprice) {
            //代理商品转交订单
            if ($isagent == 1) {
                $result = IGD('Supplier','Agorder')->CreatAgentOrder($aorderinfo);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }
            }
        }
        
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
        $data['paystatu'] = $paystatu;

        //提交事务
        $db->commit();

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
            $productinfo = M('Product')->where($whereinfo)->find();

            if (count($productinfo) <= 0) {
                return Message(1015, "产品信息不存在，不能生成订单");
            }

            $price = $productinfo['c_price'];
            $num = $productinfo['c_num'];
            if (!empty($value['mcode'])) {
                //查询商品价格
                $wheremodel['c_pcode'] = $value['pcode'];
                $wheremodel['c_mcode'] = $value['mcode'];
                $ProductModel = M('Product_model')->where($wheremodel)->find();
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

            // $oldcount = IGD('Order', 'Order')->Getoldproduct($ucode, $value['pcode']);
            // $temp2 = $value['num'] + $oldcount;
            // //判断是否存在阶梯价格
            // $wherejieti['c_pcode'] = $productinfo['c_pcode'];
            // $wherejieti['c_mcode'] = $value['mcode'];
            // $wherejieti['c_minnum'] = array('ELT', $temp2);
            // $wherejieti['c_maxnum'] = array('EGT', $temp2);
            // $jietiinfo = M('Product_ladderprice')->where($wherejieti)->find();

            // if (count($jietiinfo) > 0) {
            //     $price = $jietiinfo['c_price'];
            // }
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
            $info['isagent'] = $productinfo['c_isagent'];
            $info['agent_pcode'] = $productinfo['c_agent_pcode'];

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
            $shop['isagent'] = $productinfo['c_isagent'];


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
            $temp = "d" . $int;
            $int++;
            $detailid = CreateOrder($temp);

            //减少库存
            $result = $this->Reduceinventory($v1['pcode'], $v1['pmodel'], $v1['agent_pcode'], $v1['isagent'], $v1['num']);
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
            $tempdetails['c_pmodel'] = $v1['pmodel'];
            $tempdetails['c_pmodel_name'] = $v1['pmodel_name'];
            $tempdetails['c_pnum'] = $v1['num'];
            $tempdetails['c_ptotal'] = $singletotle;
            $tempdetails['c_pimg'] = $v1['pimg'];
            $tempdetails['c_pucode'] = $v1['pucode'];
            $tempdetails['c_isagent'] = $v1['isagent'];
            $tempdetails['c_agent_pcode'] = $v1['agent_pcode'];
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
    protected function CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free, $isagent, $delivery, $acode, $model) {

        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $ucode;

        if ($balance > 0) {
            $aorderinfo['c_actual_price'] = $balance;
            //用户功勋操作
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $ucode;
            $parr['money'] = $balance;
            $parr['source'] = 1;
            $parr['key'] = $orderid;
            $parr['desc'] = "余额支付";
            $parr['state'] = 1;
            $parr['type'] = 0;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/gou1.png';
            $parr['showtext'] = '购物';
            $result = $rebatemoney->OptionMoney($parr);

            if ($result['code'] != 0) {
                return $result;
            }
            //用户支付记录操作
            $result = $this->paylog($orderid, 4, $balance, "");
            if ($result['code'] != 0) {
                return $result;
            }
        }

        $countprice = $totprice + $free;
        if ($balance >= $countprice) {
            $aorderinfo['c_pay_state'] = 1;
            $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

            //计算详情
            //获取订单详情信息
            $wheredetail['c_orderid'] = $orderid;
            $detailslist = M('Order_details')->where($wheredetail)->select();
            $result = $this->CheckRebate($detailslist, $ucode, $acode);

            if ($result['code'] != 0) {
                return $result;
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '我还以为你从来都不会选我了，我们马上就可以团聚啦！';
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);

            $msgdata['ucode'] =$acode;
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '陛下，您有新的订单需要审阅，别让文武百官等太久，祝您万福安康~';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);
        }

        $aorderinfo['c_acode'] = $acode;
        $aorderinfo['c_pay_rule'] = 4;
        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_deliverystate'] = 0;
        $aorderinfo['c_free'] = $free;
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = $delivery;
        $aorderinfo['c_postscript'] = $postscript;
        $aorderinfo['c_isagent'] = $isagent;
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
    protected function Reduceinventory($pcode, $pmodel, $agentpcode, $isagent, $num) {
        $result = 0;
        switch ($isagent) {
            case 0 :
                // 减少总库存
                $productinfo['c_pcode'] = $pcode;
                $productinfo['c_num'] = array('egt', $num);
                $result = M('Product')->where($productinfo)->setDec('c_num', $num);
                if (!$result) {
                    return Message(1013, "库存扣除失败");
                }

                // 减少型号库存
                $productinfo['c_mcode'] = $pmodel;
                $productinfo['c_num'] = array('egt', $num);
                $result = M('Product_model')->where($productinfo)->setDec('c_num', $num);
                if (!$result) {
                    return Message(1014, "库存扣除失败");
                }

                break;
            case 2 :     //用户代理的商品
                // 减少总库存
                $productinfo['c_pcode'] = $pcode;
                $productinfo['c_num'] = array('egt', $num);
                $result = M('Product')->where($productinfo)->setDec('c_num', $num);
                if (!$result) {
                    return Message(1013, "库存扣除失败");
                }

                if (!empty($pmodel) && strpos($pmodel, 'xn') === false) {
                    // 减少型号库存
                    $productinfo['c_mcode'] = $pmodel;
                    $productinfo['c_num'] = array('egt', $num);
                    $result = M('Product_model')->where($productinfo)->setDec('c_num', $num);
                    if (!$result) {
                        return Message(1014, "库存扣除失败");
                    }
                }

                break;
            case 1 :
                // 减少总库存
                $productinfo['c_pcode'] = $agentpcode;
                $productinfo['c_num'] = array('egt', $num);
                $result = M('Supplier_product')->where($productinfo)->setDec('c_num', $num);

                if ($result <= 0) {
                    return Message(1011, "扣除库存失败");
                }


                //扣除所有代理产品的库存
                $tempwhere['c_agent_pcode'] = $agentpcode;
                if (M('Product')->where($tempwhere)->getField('c_id')) {
                    $tempwhere['c_num'] = array('egt', $num);
                    $result = M('Product')->where($tempwhere)->setDec('c_num', $num);

                    if ($result <= 0) {
                        return Message(1012, "库存扣除失败");
                    }

                    //扣除所有代理产品的库存
                    $tempwhere5['c_mcode'] = $pmodel;
                    $tempwhere5['c_num'] = array('egt', $num);
                    $result = M('Product_model')->where($tempwhere5)->setDec('c_num', $num);
                    if ($result <= 0) {
                        return Message(1012, "库存扣除失败");
                    }
                }

                // 减少型号库存
                $productinfo['c_mcode'] = $pmodel;
                $productinfo['c_num'] = array('egt', $num);
                $result = M('Supplier_product_model')->where($productinfo)->setDec('c_num', $num);
                if ($result <= 0) {
                    return Message(1011, "扣除库存失败11");
                }

                break;
            default :
                return Message(1015, "没有需要修改的库存");
                break;
        }
        return Message(0, "库存修改成功");
    }

    //支付记录操作
    public function paylog($orderid, $payrule, $money, $thirdparty,$source,$payorderid) {

        $addinfo['c_orderid'] = $orderid;
        $addinfo['c_payrule'] = $payrule;
        $addinfo['c_money'] = $money;
        $addinfo['c_thirdparty'] = $thirdparty;
        $addinfo['c_payorderid'] = $payorderid;
        $addinfo['c_source'] = isset($source)?$source:1;
        $addinfo['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Order_paylog')->add($addinfo);

        if ($result > 0) {
            return Message(0, "写入记录成功");
        }

        return Message(1015, "写入记录失败");
    }

    //支付订单
    public function PayOrder($parr) {
        $upay = $parr['upay'];
        $orderid = $parr['orderid'];

        //友收宝支付回调
        if ($upay == 1) {
            $payorderid = $orderid;
            $result = $this->GetSystemOrder($orderid);
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

        if ($orderinfo['c_pay_state'] != 0) {
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
            $result = $this->SavePayorder($payorderid,1);
            if ($result['code'] != 0) { 
                $db->rollback(); //不成功，则回滚
                return $result;
            }
        }

        //用户支付记录操作
        $result = $this->paylog($orderid, $payrule, $actualprice, $thirdpartynum,1,$payorderid);
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

        //代理商品转交订单
        if ($orderinfo['c_isagent'] == 1) {
            $result = IGD('Supplier','Agorder')->CreatAgentOrder($orderinfo);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
        } 

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
            $arr['desc'] = '平台微商订单交易资金操作';
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
        $msgdata['content'] = '我还以为你从来都不会选我了，我们马上就可以团聚啦！';
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

        if ($tag == 0 && empty($activity_id)) {
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
        $settinginfo = IGD('Common', 'Info')->GetSystemSet();
        $setting = $settinginfo['data'];
        if ($settinginfo['code'] != 0) {
            return Message(1017, "系统配置不存在");
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        $zongprice = '0.00';$shopprofit = '0.00';
        foreach ($detaillist as $key => $value) {
            $pname = $value['c_pname'];

            if (!empty($activity_id)) {
                $commission['commission'] = 0;
                $commission['decprice'] = $value['c_ptotal'];
            } else {
                $commission = $this->calculation($value, $setting, $tag);
            }

            //计算返利
            $rebate = 0;
            if ($value['c_isrebate'] == 1 && empty($activity_id)) {
                if ($value['c_isagent'] == 1) {
                    $rebate = bcmul($commission['decprice'], bcdiv($value['c_rebate_proportion'], 100, 4), 2);
                } else {
                    $rebate = bcmul($value['c_ptotal'], bcdiv($value['c_rebate_proportion'], 100, 4), 2);
                }
            }

            //计算推广
            $spread = 0;
            if ($value['c_isspread'] == 1 && empty($activity_id)) {
                if ($value['c_isagent'] == 1) {
                    $spread = bcmul($commission['decprice'], bcdiv($value['c_spread_proportion'], 100, 4), 2);
                } else {
                    $spread = bcmul($value['c_ptotal'], bcdiv($value['c_spread_proportion'], 100, 4), 2);
                }
            }

            $source = 5;
            $detailid = $value['c_detailid'];

            //修改订单详情状态
            $orderdetel['c_detailid'] = $value['c_detailid'];
            if ($value['c_isagent'] == 1) {
                $save['c_profit'] = $commission['decprice'] - $spread - $rebate;
            } else {
                //计算商家获得利润
                $lirun = $value['c_ptotal'] - $commission['commission'] - $spread - $rebate;
                if ($lirun < 0) {
                    return Message(1017, "支付商家利润为0");
                }
                $save['c_profit'] = $lirun;
            }

            if (!empty($activity_id)) {
                $save['c_profit'] = $value['c_profit'];
            }

            $save['c_commission'] = $commission['commission'];
            $save['c_rebate'] = $rebate;
            $save['c_spread'] = $spread;
            $result = M('Order_details')->where($orderdetel)->save($save);
            // if ($result <= 0) {
            //     return Message(1017, "订单详情操作失败");
            // }
            
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

    //返利操作
    public function faliRebate($ucode, $rebate, $source, $detailid, $beizhu, $isagent,$bkmoney,$xmmoney,$cashid,$deskid) {
        $rebatemoney = IGD('Money', 'User');
        if ($rebate > 0) {
            //用户功勋操作
            $parr['ucode'] = $ucode;
            $parr['money'] = $rebate;
            $parr['source'] = $source;
            $parr['key'] = $detailid;
            $parr['desc'] = $beizhu;
            $parr['state'] = 1;
            $parr['type'] = 1;
            $parr['isagent'] = $isagent;
            $parr['bkmoney'] = $bkmoney;
            $parr['xmmoney'] = $xmmoney;
            $parr['cashid'] = $cashid;
            $parr['deskid'] = $deskid;
            if ($source == 13) {
                $parr['showimg'] = 'Uploads/settlementshow/gou.png';
                $parr['showtext'] = '购物优惠';
            } else if ($source == 14) {
                $parr['showimg'] = 'Uploads/settlementshow/tuig.png';
                $parr['showtext'] = '推广佣金';
            } else if ($source == 9) {
                $parr['showimg'] = 'Uploads/settlementshow/sao.png';
                $parr['showtext'] = '扫码收入';
            } else if ($source == 1) {
                $parr['showimg'] = 'Uploads/settlementshow/ding.png';
                $parr['showtext'] = '订单收入';
            } else {
                $parr['showimg'] = 'Uploads/settlementshow/kua.png';
                $parr['showtext'] = '跨界佣金';
            }
            $result = $rebatemoney->OptionMoney($parr);
            if ($result['code'] != 0) {
                return $result;
            }
        }
        return Message(0, "返佣成功");
    }

    //计算佣金
    protected function calculation($value, $setting, $tag) {
        //直营产品计算方式
        if ($value['c_isagent'] == 0) {
            $shopscale = $setting['c_shop_scale'];
            if ($tag == 0 || $tag == 1) {
                $data['commission'] = 0;
            } else {
                if ($shopscale > 0) {
                    $data['commission'] = bcmul($value['c_ptotal'], bcdiv($shopscale, 100, 4), 2);
                }
            }
            $data['decprice'] = 0;
        } else {
            $shopscale = $setting['c_agent_scale'];

            //查询代理产品的价格
            $tempwhere['c_pcode'] = $value['c_agent_pcode'];
            $agentproduct = M('Supplier_product')->where($tempwhere)->find();
            if (count($agentproduct) == 0) {
                $price = 0;
            } else {
                $price = $agentproduct['c_price'];
            }
            if (!empty($value['c_pmodel'])) {
                $tempwhere['c_pcode'] = $value['c_agent_pcode'];
                $tempwhere['c_mcode'] = $value['c_pmodel'];
                $agentproduct = M('Supplier_product_model')->where($tempwhere)->find();
                if (count($agentproduct) == 0) {
                    $price = 0;
                } else {
                    $price = $agentproduct['c_price'];
                }

                $oldcount = IGD('Supplyorder','Agorder')->Getoldproduct($value['c_ucode'], $value['c_agent_pcode']);
                $temp2 = $value['c_pnum'] + $oldcount;
                //判断是否存在阶梯价格
                $wherejieti['c_pcode'] = $value['c_pcode'];
                $wherejieti['c_mcode'] = $value['c_pmodel'];
                $wherejieti['c_minnum'] = array('ELT', $temp2);
                $wherejieti['c_maxnum'] = array('EGT', $temp2);
                $jietiinfo = M('Supplier_product_ladderprice')->where($wherejieti)->find();
                if (count($jietiinfo) > 0) {
                    $price = $jietiinfo['c_price'];
                }
            }
            $dailizong = bcmul($price, $value['c_pnum'], 2);
            $decprice = bcsub($value['c_ptotal'], $dailizong, 2);
            $data['commission'] = bcmul($decprice, bcdiv($shopscale, 100, 4), 2);
            if ($tag == 0 || $tag == 1) {
                $data['commission'] = 0;
            }
            $data['decprice'] = bcsub($decprice, $data['commission'], 2);
        }
        return $data;
    }

    //查询以前购买产品的数量
    public function Getoldproduct($ucode, $pcode) {

        $join = 'INNER JOIN t_order as b on a.c_orderid=b.c_orderid';
        $where['b.c_pay_state'] = 1;
        $where['b.c_order_state'] = 2;
        $where['b.c_deliverystate'] = 5;
        $where['b.c_ucode'] = $ucode;
        $where['a.c_pcode'] = $pcode;
        $count = M('Order_details as a')->join($join)->where($where)->sum('c_pnum');

        if (empty($count)) {
            $count = 0;
        }
        return $count;
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

        //查询余额抵扣
        $where['c_orderid'] = $orderid;
        $where['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($where)->sum('c_money');
        $orderinfo['banlace'] = ($banlacemoney<=0)?'0.00':$banlacemoney;

        //获取可抵扣优惠券数量
        $result = IGD('Coupon','Newact')->GetUseCouponNum($parr);
        $orderinfo['couponnum'] = $result['data'];
        if ($orderinfo['couponnum'] > 0) {
            $orderinfo['c_bid'] = $orderinfo['couponnum'];
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
        return MessageInfo(0, "订单创建成功", $orderinfo);
    }

    //订单详情，作为订单展示使用
    public function GetOrderInfo($parr) {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $orderid = $parr['orderid'];

        if (!empty($ucode)) {
            $join = 'left join t_users as b on a.c_acode=b.c_ucode left join t_user_part as c on b.c_ucode=c.c_ucode';
            $whereinfo['a.c_ucode'] = $ucode;
        }

        if (!empty($acode)) {
            $join = 'left join t_users as b on a.c_ucode=b.c_ucode left join t_user_part as c on b.c_ucode=c.c_ucode';
            $whereinfo['a.c_acode'] = $acode;
        }

        $field = 'a.*,b.c_nickname,c.c_rongyun_token';

        $whereinfo['c_orderid'] = $orderid;
        $orderinfo = M('Order as a')->join($join)->where($whereinfo)->field($field)->find();

        if (count($orderinfo) <= 0) {
            return Message(0, "该订单信息不存在");
        }

        //查询余额抵扣
        $where['c_orderid'] = $orderid;
        $where['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($where)->sum('c_money');
        $orderinfo['banlace'] = ($banlacemoney<=0)?'0.00':$banlacemoney;

        $whereinfo1['c_productstatus'] = 0;
        $whereinfo1['c_orderid'] = $orderinfo['c_orderid'];
        $detail = M('Order_details')->where($whereinfo1)->select();
        $orderinfo['scorestatu'] = 0;
        $po = 0;
        foreach ($detail as $key => $value) {
            if ($value['c_isevaluate'] == 1) {
                $po++;
            }
            if (empty($value['c_pmodel'])) {
                $detail[$key]['c_pmodel_name'] = '默认';
            }
            $detail[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }
        if ($po == count($detail)) {
            $orderinfo['scorestatu'] = 1;
        }

        //查询订单位置信息
        $localwhere['c_orderid'] = $orderid;
        $localinfo = M('Order_delivery')->where($localwhere)->find();
        $orderinfo['longitude'] = $localinfo['c_longitude'];
        $orderinfo['latitude'] = $localinfo['c_latitude'];
        $orderinfo['slongitude'] = $localinfo['c_slongitude'];
        $orderinfo['slatitude'] = $localinfo['c_slatitude'];
        $orderinfo['klongitude'] = $localinfo['c_klongitude'];
        $orderinfo['klatitude'] = $localinfo['c_klatitude'];


        $orderinfo['detail'] = $detail;
        $orderinfo['address'] = $this->GetOrderAddress($orderid);

        if ($orderinfo['c_delivery'] == 3) {
            //查询商家位置信息
            $acdw['c_ucode'] = $orderinfo['c_acode'];
            $acodeinfo = M('Store')->where($acdw)->find();
            $phone = M('Users')->where($acdw)->getField('c_phone');
            $acodeinfo['c_phone'] = $phone;
			//查询买家电话
            $uinfow['c_ucode'] = $orderinfo['c_ucode'];
            $userins = M('Users')->where($uinfow)->field('c_phone,c_nickname')->find();
            $acodeinfo['user_phone'] = $userins['c_phone'];
            $acodeinfo['user_name'] = $userins['c_nickname'];
            
            $orderinfo['acodeinfo'] = $acodeinfo;
            //查询提货码信息
            $thw['c_orderid'] = $orderinfo['c_orderid'];
            $thinfo = M('Tihuo_log')->where($thw)->find();
            $orderinfo['thinfo'] = $thinfo;

        }
        $orderinfo['desc'] ="24小时内未付款，系统将自动取消";

        return MessageInfo(0, "订单创建成功", $orderinfo);
    }

    public function GetOrderAddress($orderid) {


        $address['c_orderid'] = $orderid;
        $addressinfo = M('Order_address')->where($address)->find();
        return $addressinfo;
    }

    //查询订单列表
    public function GetOrderList($parr) {

        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $pcode = $parr['pcode']; //新增产品编码查询
        $flag  = $parr['flag'];  // 1 未提货  2 已提货
        $keys   = $parr['keys']; // 提货码 模糊搜索

        $type = $parr['type'];
        // $source = $parr['source'];
        $whereinfo['c_order_state'] = array('lt',4);
        
        switch($type){
        	case 6:
        		$whereinfo['c_order_state']=1;
        		$whereinfo['c_pay_state']=0;
        		$whereinfo['c_delivery']=array('neq',3);
        	break;
        	case 1:
        		$whereinfo['c_order_state']=2;
        		$whereinfo['c_pay_state']=0;
        		$whereinfo['c_delivery']=array('neq',3);
        	break;
        	case 2:
        		$whereinfo['c_order_state']=2;
        		$whereinfo['c_pay_state']=1;
        		$whereinfo['c_deliverystate']=0;
        		$whereinfo['c_delivery']=array('neq',3);
        	break;
        	case 3:
        		$whereinfo['c_order_state']=2;
        		$whereinfo['c_pay_state']=1;
        		$whereinfo['c_deliverystate']=2;
        		$whereinfo['c_delivery']=array('neq',3);
        	break;
        	case 4:
        		$whereinfo['c_order_state']=2;
        		$whereinfo['c_pay_state']=1;
        		$whereinfo['c_deliverystate']=5;
        		$whereinfo['c_delivery']=array('neq',3);
        	break;
        	case 7:
        		if($flag==1){
        			$whereinfo['c_deliverystate']=array('eq','0');
        		}elseif($flag==2){
        			$whereinfo['c_deliverystate']=array('eq','5');;        		
        		}
        		$whereinfo['c_order_state']=2;
        		$whereinfo['c_pay_state']=1;
        		$whereinfo['c_delivery']=3;
        	break;
        	default:
        		$whereinfo['c_delivery']=array('neq',3);
        	break;
        }

        if (!empty($ucode)) {
        	$whereinfo['c_ucode']=$ucode;           
        }

        if (!empty($acode)) {
        	$whereinfo['c_acode']=$acode;
        	$whereinfo['c_ishand']=1;          
        }

        if (!empty($pcode)) {
            //根据产品编码查询
            $dew['c_pcode'] = $pcode;
            $orderidarr = M('Order_details')->where($dew)->field('c_orderid')->group('c_orderid')->select();
            $whereinfo['c_orderid']=array('in',$orderidarr);           
        }
       
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if(!empty($keys)){
        	$w[] = array("c_tcode like '%$keys%'");
        	$idarr =M('Tihuo_log')->where($w)->field('c_orderid')->select();
        	if($idarr) {
                foreach ($idarr as $key => $value) {
                    if ($key == 0) {
                        $acodestr .= $value['c_orderid'];
                    } else {
                        $acodestr .= ','.$value['c_orderid'];
                    }
                }
            }else{
            	$acodestr ='aaaaaaaaa';
            }
        	
        	$whereinfo['c_orderid'] =array('in',$acodestr);
        	
        }
        	$list = M('Order')->where($whereinfo)->limit($countPage,$pageSize)->order('c_addtime desc')->select();
       
     
        if (!$list) {
            return MessageInfo(0, '查询成功');
        }

        foreach ($list as $key1 => $value1) {
            $wheretail['c_orderid'] = $value1['c_orderid'];
            $wheretail['c_productstatus'] = 0;
            if (!empty($ucode)) {
                $whereuser['c_ucode'] = $value1['c_acode'];
                if ($value1['ordertype'] == 1) {
                    $list[$key1]['c_nickname'] = M('Supplier')->where($whereuser)->getField('c_name');
                } else {
                    $list[$key1]['c_nickname'] = M('Users')->where($whereuser)->getField('c_nickname');
                }
            }

            if (!empty($acode)) {
                $whereuser['c_ucode'] = $value1['c_ucode'];
                $list[$key1]['c_nickname'] = M('Users')->where($whereuser)->getField('c_nickname');
            }
            if ($value1['ordertype'] == 1) {
                $ordermoder = M('Supplier_order_details');
            } else {
                $ordermoder = M('Order_details');
            }

            $detail = $ordermoder->where($wheretail)->select();
            $list[$key1]['scorestatu'] = 0;
            $po = 0;
            foreach ($detail as $key => $value) {
                if ($value['c_isevaluate'] == 1) {
                    $po++;
                }
                if (empty($value['c_pmodel'])) {
                   $value['c_pmodel_name'] = '默认';
                }
                $list[$key1]['detail'][$key] = $value;
                $list[$key1]['detail'][$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            }
            if ($po == count($detail)) {
                $list[$key1]['scorestatu'] = 1;
            }

            $list[$key1]['actsign'] = '';
            if (!empty($value1['c_activity_id'])) {
                $activitywhere['c_id'] = $value1['c_activity_id'];
                $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
                if ($activitydata['c_activitytype'] == 26) {
                    $list[$key1]['actsign'] = '拼团';
                } else if ($activitydata['c_activitytype'] == 27) {
                    $list[$key1]['actsign'] = '砍价';
                } else if ($activitydata['c_activitytype'] == 28) {
                    $list[$key1]['actsign'] = '秒杀';
                }
            }

            //查询订单位置信息
            $localwhere['c_orderid'] = $value1['c_orderid'];
            $localinfo = M('Order_delivery')->where($localwhere)->find();
            $list[$key1]['longitude'] = $localinfo['c_longitude'];
            $list[$key1]['latitude'] = $localinfo['c_latitude'];
            $list[$key1]['slongitude'] = $localinfo['c_slongitude'];
            $list[$key1]['slatitude'] = $localinfo['c_slatitude'];
            $list[$key1]['klongitude'] = $localinfo['c_klongitude'];
            $list[$key1]['klatitude'] = $localinfo['c_klatitude'];
			
            //查询提货码信息
            if ($type == 7) {            	
                //查询商家位置信息
                $acdw['c_ucode'] = $value1['c_acode'];
                $acodeinfo = M('Store')->where($acdw)->find();
                $phone = M('Users')->where($acdw)->getField('c_phone');
                $acodeinfo['c_phone'] = $phone;
                //查询买家电话
                $uinfow['c_ucode'] = $value1['c_ucode'];
                $userins = M('Users')->where($uinfow)->field('c_phone,c_nickname')->find();
                $acodeinfo['user_phone'] = $userins['c_phone'];
                $acodeinfo['user_name'] = $userins['c_nickname'];
                
                $list[$key1]['acodeinfo'] = $acodeinfo;

				//查询提货码信息
            	$thw['c_orderid'] = $value1['c_orderid'];
            	$thinfo = M('Tihuo_log')->where($thw)->find();
            	$list[$key1]['thinfo'] = $thinfo;
				
            }            
        }

        $count = M('Order')->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);	
    }

    //取消订单
    public function CancelOrder($parr) {

        $orderid = $parr['orderid'];
        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($whereorder)->find();

        if (count($orderinfo) == 0) {
            return Message(1000, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 0 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1001, '该订单无法取消');
        }

        $db = M('');
        $db->startTrans();

        $update['c_order_state'] = 1;
        $whereorder1['c_orderid'] = $orderid;
        $whereorder1['c_order_state'] = array('neq',1);
        $result = M('Order')->where($whereorder1)->save($update);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1001, '修改订单状态失败');
        }

        $ucode = $orderinfo['c_ucode'];
        $acode = $orderinfo['c_acode'];
        $backmoney = bcsub($orderinfo['c_actual_price'], $orderinfo['c_bmoney'],2);
        //返回用户余额
        if ($backmoney > 0) {
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $ucode;
            $parr['money'] = $backmoney;
            $parr['source'] = 1;
            $parr['key'] = $orderid;
            $parr['desc'] = "取消订单余额支付";
            $parr['state'] = 1;
            $parr['type'] = 1;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/ding.png';
            $parr['showtext'] = '订单取消';
            $result = $rebatemoney->OptionMoney($parr);

            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您的订单号：' . $orderid . '已取消成功，退还抵扣余额￥' . $orderinfo['c_actual_price'] . '成功';
            $msgdata['tag'] = 2;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $Msgcentre->CreateMessege($msgdata);
        }

        //获取订单详情信息
        $detailslist = M('Order_details')->where($whereorder)->select();
        foreach ($detailslist as $key => $value) {

            //返回代理产品库存
            if ($value['c_isagent'] == 1) {
                $atproductwhere['c_pcode'] = $value['c_agent_pcode'];
                $result = M('Supplier_product')->where($atproductwhere)->setInc('c_num', $value['c_pnum']);
                if (!$result) {
                    return Message(3007, '返回代理产品库存失败');
                }

                $pmproductwhere['c_mcode'] = $value['c_pmodel'];
                $result = M('Supplier_product_model')->where($pmproductwhere)->setInc('c_num', $value['c_pnum']);
                if (!$result) {
                    return Message(3007, '返回代理型号库存失败');
                }

                //返回所有代理产品的库存
                $agtempwhere['c_agent_pcode'] = $value['c_agent_pcode'];
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
            } else {
                // 返回总库存
                $productwhere['c_pcode'] = $value['c_pcode'];
                $result = M('Product')->where($productwhere)->setInc('c_num', $value['c_pnum']);
                if (!$result) {
                    $db->rollback(); //不成功，则回滚
                    return Message(3007, '返回产品总库存失败');
                }

                // 返回型号库存
                $productwhere['c_mcode'] = $value['c_pmodel'];
                $result = M('Product_model')->where($productwhere)->setInc('c_num', $value['c_pnum']);
                // if (!$result) {
                //     $db->rollback(); //不成功，则回滚
                //     return Message(3007, '返回产品型号库存失败');
                // }
            }

        }

        //同步代理产品取消订单
        // if ($orderinfo['c_isagent'] == 1) {
        //     $result = IGD('Supplyorder','Order')->OptionCancelOrder($parr);
        //     if ($result['code'] != 0) {
        //         return $result;
        //     }
        // }       

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '也许我们不适合，但是我会努力让自己配得上你！';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        $msgdata['ucode'] = $acode;
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '陛下，您的订单【' . $orderid . '】已被取消，急需您来安抚宝贝的心。';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $Msgcentre->CreateMessege($msgdata);

        $db->commit();
        return Message(0, '订单取消成功');
    }

    // 发货  c_deliverystate =5

    public function delivery($parr){

        $orderid = $parr['orderid'];
        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1000, '该订单不存在');
        }

        //判断订单是否可以进行确认订单
        if ($orderinfo['c_pay_state'] != 1 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1001, '该订单无法确认');
        }

        $db = M('');
        $db->startTrans();

        $opwh['c_orderid'] = $orderid;
        $opwh['c_deliverystate'] = 0;
        $save['c_deliverystate'] = 5;
        $save['c_confirmtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->where($opwh)->save($save);

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
        $settinginfo = IGD('Common', 'Info')->GetSystemSet();
        $setting = $settinginfo['data'];

        if ($settinginfo['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1024, '系统配置不存在');
        } else {
            $areascale = $setting['c_area_scale'];
            $cityscale = $setting['c_city_scale'];
        }

        //获取订单详情信息
        $wheredetail['c_orderid'] = $orderid;
        $wheredetail['c_productstatus'] = 0;
        $detailslist = M('Order_details')->where($whereorder)->select();
        $Msgcentre = IGD('Msgcentre', 'Message');
        foreach ($detailslist as $key => $value) {
            if ($value['c_isagent'] == 1) {
                $shopscale = $setting['c_agent_scale'];
                $shoprefreescale = $setting['c_agent_refreescale'];
            } else {
                $shopscale = $setting['c_shop_scale'];
                $shoprefreescale = $setting['c_shop_refreescale'];
            }
            $source = 5;
            $pname = $value['c_pname'];
            /* ----开始返利------- */
            if ($value['c_rebate'] > 0 && empty($orderinfo['c_activity_id'])) {
                $beizhu = "您购买商品" . $pname;
                $result = $this->faliRebate($orderinfo['c_ucode'], $value['c_rebate'], 13, $value['c_detailid'], $beizhu, 0);
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
                $result = $this->faliRebate($value['c_pucode'], $value['c_spread'], 14, $value['c_detailid'], $beizhu, 0);
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
            $mchtype = 2;    //普通
            if ($commission > 0 && $tag == 2 && empty($orderinfo['c_activity_id'])) {
                $mchtype = 1;    //跨界
                //给推荐人提成
                $tuijianucode = $userinfotuijian['c_pcode'];
                if ($shoprefreescale > 0 && !empty($tuijianucode)) {
                    $tuijianmoney = bcmul($commission, bcdiv($shoprefreescale, 100, 4), 2);
                    if ($tuijianmoney > 0) {
                        $beizhu = "您推荐的用户跨界购买产品：" . $pname;
                        $result = $this->faliRebate($tuijianucode, $tuijianmoney, $source, $value['c_detailid'], $beizhu, 0);
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
                if ($cityscale > 0) {
                    if (count($buserinfo) > 0 && !empty($buserinfo['c_acode'])) {
                        $citycode = $buserinfo['c_acode'];
                        $citymoney = bcmul($commission, bcdiv($cityscale, 100, 4), 2);
                        if ($citymoney > 0) {
                            $beizhu = "您的微商【".$buserinfo['c_nickname']."】推荐的会员跨界购买产品：" . $pname;
                            $result = $this->faliRebate($citycode, $citymoney, $source, $value['c_detailid'], $beizhu, 0);
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
                if ($areascale > 0) {
                    if (count($quserinfo) > 0 && !empty($quserinfo['c_acode'])) {
                        $areacode = $quserinfo['c_acode'];
                        $areamoney = bcmul($commission, bcdiv($areascale, 100, 4), 2);
                        if ($areamoney > 0) {
                            $beizhu = "您旗下的微商【".$buserinfo['c_nickname']."】推荐的会员跨界购买产品：" . $pname;
                            $result = $this->faliRebate($areacode, $areamoney, $source, $value['c_detailid'], $beizhu, 0);
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
            $red_scale = $setting['c_red_scale'];
            $redprice = bcmul($commission, bcdiv($red_scale, 100, 4), 2);
            if ($redprice > 0) {
                $value['money'] = $redprice;
                $result = IGD('Red','Activity')->AddRedNum($value,$orderinfo['c_acode']);
            }

            $bkmoneybt = 0;$xmmoneybt = 0;
            //查询跨界费率商户号
            // $result = IGD('Upay','Scanpay')->GetShopMchid($orderinfo['c_acode'],$mchtype);
            // if ($result['code'] == 0) {
            //     $xmmoneybt = $result['data']['c_billrate']/1000;   //到账小蜜余额比例
            //     $bkmoneybt = 1 - $xmmoneybt;    //到账银行卡比例
            // }

            //给商家结算
            $shopprofit = $value['c_profit'];
            if ($shopprofit > 0) {
                if ($mchtype == 1) {  //跨界
                    $bkmoney = $shopprofit;
                } else {  //普通
                    $bkmoney = round($bkmoneybt*$shopprofit,2);
                    $xmmoney = round($xmmoneybt*$shopprofit,2);
                }
                $beizhu = "卖出商品" . $pname;
                $result = $this->faliRebate($orderinfo['c_acode'], $shopprofit, 1, $value['c_detailid'], $beizhu, 0,$bkmoney,$xmmoney);
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

            if (empty($orderinfo['c_activity_id'])) {
                //增加销量
                $saleproductwhere['c_pcode'] = $value['c_pcode'];
                $result = M('Product')->where($saleproductwhere)->setInc('c_salesnum', $value['c_pnum']);
                if (!$result) {
                    return Message(1016, "销量增加失败");
                }
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
            $result = $this->faliRebate($orderinfo['c_acode'], $btfree, 1, $value['c_detailid'], $beizhu, 0);
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



        //同步代理产品确认收货
        if ($orderinfo['c_isagent'] == 1) {
            $result = IGD('Supplyorder','Agorder')->OptionConfirmorder($parr);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
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

        $opwh['c_orderid'] = $orderid;
        $opwh['c_deliverystate'] = 2;
        $save['c_deliverystate'] = 5;
        $save['c_confirmtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->where($opwh)->save($save);

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
        $settinginfo = IGD('Common', 'Info')->GetSystemSet();
        $setting = $settinginfo['data'];

        if ($settinginfo['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1024, '系统配置不存在');
        } else {
            $areascale = $setting['c_area_scale'];
            $cityscale = $setting['c_city_scale'];
        }        

        //获取订单详情信息
        $wheredetail['c_orderid'] = $orderid;
        $wheredetail['c_productstatus'] = 0;
        $detailslist = M('Order_details')->where($whereorder)->select();
        $Msgcentre = IGD('Msgcentre', 'Message');
        foreach ($detailslist as $key => $value) {
            if ($value['c_isagent'] == 1) {
                $shopscale = $setting['c_agent_scale'];
                $shoprefreescale = $setting['c_agent_refreescale'];
            } else {
                $shopscale = $setting['c_shop_scale'];
                $shoprefreescale = $setting['c_shop_refreescale'];
            }
            $source = 5;
            $pname = $value['c_pname'];
            /* ----开始返利------- */
            if ($value['c_rebate'] > 0 && empty($orderinfo['c_activity_id'])) {
                $beizhu = "您购买商品" . $pname;
                $result = $this->faliRebate($orderinfo['c_ucode'], $value['c_rebate'], 13, $value['c_detailid'], $beizhu, 0);
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
                $result = $this->faliRebate($value['c_pucode'], $value['c_spread'], 14, $value['c_detailid'], $beizhu, 0);
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
            $mchtype = 2;    //普通
            if ($commission > 0 && $tag == 2 && empty($orderinfo['c_activity_id'])) {
                $mchtype = 1;    //跨界
                //给推荐人提成
                $tuijianucode = $userinfotuijian['c_pcode'];
                if ($shoprefreescale > 0 && !empty($tuijianucode)) {
                    $tuijianmoney = bcmul($commission, bcdiv($shoprefreescale, 100, 4), 2);
                    if ($tuijianmoney > 0) {
                        $beizhu = "您推荐的用户跨界购买产品：" . $pname;
                        $result = $this->faliRebate($tuijianucode, $tuijianmoney, $source, $value['c_detailid'], $beizhu, 0);
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
                if ($cityscale > 0) {
                    if (count($buserinfo) > 0 && !empty($buserinfo['c_acode'])) {
                        $citycode = $buserinfo['c_acode'];
                        $citymoney = bcmul($commission, bcdiv($cityscale, 100, 4), 2);
                        if ($citymoney > 0) {
                            $beizhu = "您的微商【".$buserinfo['c_nickname']."】推荐的会员跨界购买产品：" . $pname;
                            $result = $this->faliRebate($citycode, $citymoney, $source, $value['c_detailid'], $beizhu, 0);
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
                if ($areascale > 0) {
                    if (count($quserinfo) > 0 && !empty($quserinfo['c_acode'])) {
                        $areacode = $quserinfo['c_acode'];
                        $areamoney = bcmul($commission, bcdiv($areascale, 100, 4), 2);
                        if ($areamoney > 0) {
                            $beizhu = "您旗下的微商【".$buserinfo['c_nickname']."】推荐的会员跨界购买产品：" . $pname;
                            $result = $this->faliRebate($areacode, $areamoney, $source, $value['c_detailid'], $beizhu, 0);
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
            $red_scale = $setting['c_red_scale'];
            $redprice = bcmul($commission, bcdiv($red_scale, 100, 4), 2);
            if ($redprice > 0) {
                $value['money'] = $redprice;
                $result = IGD('Red','Activity')->AddRedNum($value,$orderinfo['c_acode']);
            }

            $bkmoneybt = 0;$xmmoneybt = 0;
            //查询跨界费率商户号
            // $result = IGD('Upay','Scanpay')->GetShopMchid($orderinfo['c_acode'],$mchtype);
            // if ($result['code'] == 0) {
            //     $xmmoneybt = $result['data']['c_billrate']/1000;   //到账小蜜余额比例
            //     $bkmoneybt = 1 - $xmmoneybt;    //到账银行卡比例
            // }

            //给商家结算
            $shopprofit = $value['c_profit'];
            if ($shopprofit > 0) {
                if ($mchtype == 1) {  //跨界
                    $bkmoney = round($bkmoneybt*$shopprofit,2);
                    $xmmoney = $shopprofit - $bkmoney;
                } else {  //普通 
                    $bkmoney = round($bkmoneybt*$shopprofit,2);
                    $xmmoney = $shopprofit - $bkmoney;  
                } 
                $beizhu = "卖出商品" . $pname;
                $result = $this->faliRebate($orderinfo['c_acode'], $shopprofit, 1, $value['c_detailid'], $beizhu, 0,$bkmoney,$xmmoney);
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

            if (empty($orderinfo['c_activity_id'])) {                
                //增加销量
                $saleproductwhere['c_pcode'] = $value['c_pcode'];
                $result = M('Product')->where($saleproductwhere)->setInc('c_salesnum', $value['c_pnum']);
                if (!$result) {
                    return Message(1016, "销量增加失败");
                }
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
            $result = $this->faliRebate($orderinfo['c_acode'], $btfree, 1, $value['c_detailid'], $beizhu, 0);
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


        //同步代理产品确认收货
        if ($orderinfo['c_isagent'] == 1) {
            $result = IGD('Supplyorder','Agorder')->OptionConfirmorder($parr);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
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

        //同步代理产品发货
        if ($result['data']['c_isagent'] == 1) {
            $result = IGD('Supplyorder','Agorder')->OptionSenddelivery($parr);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        }
       
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
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderinfo['c_orderid'];
        $Msgcentre->CreateMessege($msgdata);

        $db->commit();
        return $result;
    }

    //订单发货操作
    public function OptionSenddelivery($parr) {
        $orderid = $parr['orderid'];
        $whereorder['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1021, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 1 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1022, '该订单不能进行发货');
        }

        if ($orderinfo['c_ishand'] != 1) {
            return Message(1022, '暂不能发货，等待系统开奖处理');
        }

        $save['c_deliverytime'] = date('Y-m-d H:i:s');
        $save['c_deliverystate'] = 2;
        $save['c_expressname'] = $parr['expressname'];
        $save['c_expressnum'] = $parr['expressnum'];
        $result = M('Order')->where($whereorder)->save($save);

        if (!$result) {
            return Message(1023, '订单发货失败');
        }

        return MessageInfo(0, '订单发货成功',$orderinfo);
    }

    /**
     *  查询单个订单详情
     *  @param detailid
     */
    public function FindDetailOne($parr) {
        $where['a.c_detailid'] = $parr['detailid'];
        $join = 'left join t_order as b on a.c_orderid=b.c_orderid';
        $field = 'a.*,b.c_acode';
        $data = M('Order_details as a')->join($join)->where($where)->field($field)->find();
        if (count($data) == 0) {
            return Message(1000, '产品详情不存在');
        }
        $data['c_pimg'] = GetHost() . '/' . $data['c_pimg'];
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  商品评价接口
     *  @param detailid,score,content,img,acode
     */
    public function ProductScore($parr) {
        // 查询订单产品详情信息
        $where['c_detailid'] = $parr['detailid'];
        if (substr($parr['detailid'],0,1) == 's') {
            $detailinfo = M('Supplier_order_details')->where($where)->find();
        } else {
            $detailinfo = M('Order_details')->where($where)->find();
            $data['c_agent_pcode'] = $detailinfo['c_agent_pcode'];
        }
        if (count($detailinfo) == 0) {
            return Message(1000, '产品详情不存在');
        }

        if ($detailinfo['c_isevaluate'] == 1) {
            return Message(1001, '产品已经评论');
        }

        if (empty($parr['content'])) {
            return Message(1002, '请输入评论内容');
        }

        if (empty($parr['score']) || $parr['score'] == 0) {
            $parr['score'] = 5;
        }

        $data['c_ucode'] = $detailinfo['c_ucode'];
        $data['c_pcode'] = $detailinfo['c_pcode'];
        $data['c_pname'] = $detailinfo['c_pname'];
        $data['c_pimg'] = $detailinfo['c_pimg'];
        $data['c_orderid'] = $detailinfo['c_orderid'];
        $data['c_detailid'] = $detailinfo['c_detailid'];
        $data['c_score'] = $parr['score'];
        $data['c_acode'] = $parr['acode'];
        $data['c_content'] = $parr['content'];
        $data['c_object'] = 1;
        $data['c_addtime'] = date('Y-m-d H:i:s');

        $db = M('');
        $db->startTrans();
        $result = M('Product_score')->add($data);
        $pid = $result;
        if (!$result) {
            $db->rollback();
            return Message(1000, "评价失败，请稍后再试");
        }

        //保存图片
        $imglist = $parr['img'];
        $imgdata['c_regionid'] = $pid;
        $imgdata['c_sourceid'] = 3;
        $imgdata['c_addtime'] = date('Y-m-d H:i:s');
        foreach ($imglist as $key => $value) {
            $imgdata['c_img'] = $value;
            $imgdata['c_thumbnail_img'] = $value;
            $result = M('Resource_img')->add($imgdata);
            if (!$result) {
                $db->rollback();
                return Message(1000, "图片存储失败");
            }
        }

        $save['c_isevaluate'] = 1;
        if (substr($parr['detailid'],0,1) == 's') {
            $result = M('Supplier_order_details')->where($where)->save($save);
        } else {
            $result = M('Order_details')->where($where)->save($save);
        }
        if (!$result) {
            $db->rollback();
            return Message(1000, "评价失败，请稍后再试");
        }

        $db->commit();
        return Message(0, "评价成功");
    }

    //获取订单数量
    public function orderCountInfo($parr) {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        $model = M('');
        //待付款数量
        if (!empty($ucode)) {
            $where = "c_ucode='$ucode'";
            $whereinfo = "c_ucode='$ucode' and c_refundstate<>3";
        }
        if (!empty($acode)) {
            $where = "c_acode='$acode' and c_ishand=1";
            $whereinfo = "c_acode='$acode' and c_refundstate<>3";
        }

        //统计待支付与待发货与待收货数量
        $sql = "select count(case when c_order_state=2 and c_pay_state=0 and c_delivery<>3 then 1 else null end) as waitpay,"
             ."count(case when c_order_state=2 and c_pay_state=1 and c_deliverystate=0 and c_delivery<>3 then 1 else null end) as waitsend,"
             ."count(case when c_order_state=2 and c_pay_state=1 and c_deliverystate=2 and c_delivery<>3 then 1 else null end) as waitreceiving,"
             ."count(case when c_order_state=2 and c_pay_state=1 and c_delivery=3 then 1 else null end) as tihuo"
             ." from t_order where $where";     
        $tjnum = $model->query($sql);

        if (!empty($ucode)) {
            $sql1 = "select a.c_id from t_order_details as a left join t_order as b on a.c_orderid=b.c_orderid"
                  ." where a.c_productstatus=0 and a.c_isevaluate=0 and b.c_pay_state=1 and b.c_order_state=2"
                  ." and b.c_deliverystate=5 and a.c_ucode='$ucode' group by a.c_orderid";
            $waitcount = $model->query($sql1);
        }

        //退款售后
        $sql2 = "select count(c_id) as saleafter from t_order_refund where $whereinfo"; 
        $countnum = $model->query($sql2);
        $data['saleafter'] = $countnum[0]['saleafter'];

        $data['waitpay'] = ($tjnum[0]['waitpay']>0)?$tjnum[0]['waitpay']:'0';
        $data['waitsend'] = ($tjnum[0]['waitsend']>0)?$tjnum[0]['waitsend']:'0';
        $data['waitreceiving'] = ($tjnum[0]['waitreceiving']>0)?$tjnum[0]['waitreceiving']:'0';
        $data['tihuo'] = ($tjnum[0]['tihuo']>0)?$tjnum[0]['tihuo']:'0';
        $data['waitevalue'] = count($waitcount);

        return MessageInfo(0, "查询成功", $data);
    }

    function ordersql($whereinfo)
    {
        $sql1 = "select count(*) as tc from (select c_id,c_orderid,'' as c_scode,c_ucode,c_acode,c_pay_state,c_order_state,"
            ."c_deliverystate,c_free,c_total_price,c_actual_price,c_pay_rule,c_delivery,c_paytime,c_deliverytime,"
            ."c_confirmtime,c_postscript,c_isagent,c_activity_id,c_activity_name,'' as c_agent_orderid,"
            ."'' as c_severtype,c_addtime,0 as ordertype from t_order where $whereinfo union select c_id,c_orderid,c_scode,"
            ."c_ucode,c_acode,c_pay_state,c_order_state,c_deliverystate,c_free,c_total_price,c_actual_price,"
            ."c_pay_rule,c_delivery,c_paytime,c_deliverytime,c_confirmtime,c_postscript,'' as c_isagent,"
            ."'' as c_activity_id,'' as c_activity_name,c_agent_orderid,c_severtype,c_addtime,1 as ordertype from t_supplier_order where $whereinfo and (c_scode is null or c_scode='')"
            .") alias order by c_addtime desc";
        return $sql1;
    }

    // 提醒发货
    function RemindDeliver($parr) {
        $orderid = $parr['orderid'];
        $whereorder['c_orderid'] = $orderid;

        $orderinfo = M('Order')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1021, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 1 || $orderinfo['c_order_state'] != 2 || $orderinfo['c_deliverystate'] != 0) {
            return Message(1022, '该订单不能进行提醒发货');
        }

        if ($orderinfo['c_ishand'] != 1) {
            return Message(1022, '不能提醒发货，等待系统开奖处理');
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_acode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '订单号为：' . $orderid . '的买家已提醒您发货，请尽快发货，提升服务品质！';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/detail?orderid=' . $orderid;
        $result = $Msgcentre->CreateMessege($msgdata);

        if ($result['code'] != 0) {
            return Message(1002, '操作失败');
        }

        return Message(0, '操作成功');
    }


    // 修改订单运费

    /**
     * @param $parr
     * @return array
     */
    function SaveFreeMoney($parr)
    {
        $where['c_acode'] = $parr['ucode'];
        $where['c_orderid'] = $parr['orderid'];
        $orderinfo = M('Order')->where($where)->find();
        if (count($orderinfo) == 0) {
            return Message(1021, '该订单不存在');
        }

        if ($parr['free'] < 0) {
            return Message(1022, '邮费必须大于0');
        }

        $db = M('');
        $db->startTrans();

        //获取订单详情信息
        $wheredetail['c_orderid'] = $parr['orderid'];
        $wheredetail['c_productstatus'] = 0;
        $detailslist = M('Order_details')->where($wheredetail)->select();
        $free = $orderinfo['c_free'] - $parr['free'];
        //循环扣除产品邮费
        $detailfee = 0;$fsign = 0;
        foreach ($detailslist as $key => $value) {
            $detailfee = $detailfee + $value['c_free'];
            $diffee = $free - $detailfee;
            if ($diffee <= 0) {
                if ($fsign == 1) {
                    $detailsave['c_free'] = 0;
                } else {
                    $fsign = 1;
                    $detailsave['c_free'] = -$diffee; 
                }
            } else {    //计算第一次小于0
                $detailsave['c_free'] = $value['c_free'];
            }

            $detailwhere['c_id'] = $value['c_id'];
            $result = M('Order_details')->where($detailwhere)->save($detailsave);
            if (!$result) {
                $db->rollback();
                return Message(1002, '修改产品邮费失败');
            }

        //     if ($free > 0) {
        //         $subfree =  $free - $value['c_free'];
        //         if ($subfree > 0) {
        //             $detailsave['c_free'] = $value['c_free'];
        //             $free = $subfree;
        //         } else {
        //             $detailsave['c_free'] = $value['c_free'] - $free;
        //             $free = 0;
        //         }
        //         $detailwhere['c_id'] = $value['c_id'];
        //         $result = M('Order_details')->where($detailwhere)->save($detailsave);
        //         if (!$result) {
        //             $db->rollback();
        //             return Message(1002, '修改产品邮费失败');
        //         }
        //     } else if ($free < 0) {
        //         $detailsave['c_free'] = $value['c_free'] - $free;
        //         $detailwhere['c_id'] = $value['c_id'];
        //         $result = M('Order_details')->where($detailwhere)->save($detailsave);
        //         if (!$result) {
        //             $db->rollback();
        //             return Message(1002, '修改产品邮费失败');
        //         }
        //     }
        }

        $save['c_free'] = $parr['free'];
        $result = M('Order')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(1002, '修改失败');
        }

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '您的订单号：' . $orderinfo['c_orderid'] . '，卖家已修改运费，请及时支付';
        $msgdata['tag'] = 3;
        $msgdata['tagvalue'] = $orderinfo['c_orderid'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $orderinfo['c_orderid'];
        $Msgcentre->CreateMessege($msgdata);

        $db->commit();

        return Message(0, '修改成功');
    }

    /**
     * 供支付选择面对面收货方式
     * @param longitude,latitude,ucode,acode
     * 
     */
    function SelectDelivery($parr)
    {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        //查询商家信息
        $acodew['a.c_ucode'] = $acode;
        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $field = 'b.c_longitude,b.c_latitude,a.c_nickname';
        $acodeinfo = M('Users as a')->join($join)->where($acodew)->field($field)->find();
        if (!$acodeinfo) {
            return Message(3000,'商家不存在');
        }

        //判断兑换距离
        $str1 = GetDistance($longitude, $latitude, $acodeinfo['c_longitude'], $acodeinfo['c_latitude']);
        $str1 = sprintf("%.2f", $str1);   //单位千米
        if ($str1 > 3) {   //需离商家3千米距离之类
            return Message(3001,'面对面收货方式需离商家3公里距离之内');
        }

        return Message(0,'可以选择');
    }

    /**
     * 根据支付临时单号查询系统订单号
     * @param payorderid
     */
    function GetSystemOrder($payorderid)
    {
        $where['c_payorderid'] = $payorderid;
        $data = M('Order_payorderid')->where($where)->find();
        if (!$data) {
            return Message(3000,'订单不存在');
        }

        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 根据系统订单号生成临时支付订单号
     * @param orderid
     */
    function CreatePayorder($orderid)
    {
        $sign = substr($orderid,0,1).rand(1,9);
        $payorderid = CreateOrder($sign);

        $db = M('');
        $db->startTrans();

        //写入临时订单记录表
        $add['c_orderid'] = $orderid;
        $add['c_payorderid'] = $payorderid;
        $add['c_pay_state'] = 0;
        $add['c_addtime'] = gdtime();
        $result = M('Order_payorderid')->add($add);
        if (!$result) {
            $db->rollback();
            return Message(3000,'记录订单号失败');
        }

        //保存临时订单到订单表
        $ow['c_orderid'] = $orderid;
        $osave['c_payorderid'] = $payorderid;
        $result = M('Order')->where($ow)->save($osave);
        if (!$result) {
            $db->rollback();
            return Message(3000,'记录订单号失败');
        }

        $db->commit();
        return MessageInfo(0,'生成订单成功',$payorderid);
    }

    /**
     * 改变临时订单支付状态
     * @param payorderid
     */
    function SavePayorder($payorderid,$pay_state)
    {
        $where['c_payorderid'] = $payorderid;
        $save['c_pay_state'] = $pay_state;
        $result = M('Order_payorderid')->where($where)->save($save);
        if (!$result) {
            return Message(3000,'操作失败');
        }

        return Message(0,'操作成功');
    }

    /**
     * 面对面收货商家、用户、送货员地理位置同步
     * @param orderid,longitude,latitude,slongitude,slatitude,klongitude,klatitude
     */
    function LocalShop($parr)
    {
        //查询记录
        $where['c_orderid'] = $parr['orderid'];
        $localinfo = M('Order_delivery')->where($where)->find();

        $localadd['c_orderid'] = $parr['orderid'];
        //用户经纬度
        if ($localinfo['c_longitude'] <= 0) {
            $localadd['c_longitude'] = $parr['longitude'];
        }
        if ($localinfo['c_latitude'] <= 0) {
            $localadd['c_latitude'] = $parr['latitude'];
        }

        //商家经纬度
        if ($localinfo['c_slongitude'] <= 0) {
             $localadd['c_slongitude'] = $parr['slongitude'];
        }
        if ($localinfo['c_slatitude'] <= 0) {
            $localadd['c_slatitude'] = $parr['slatitude'];
        }

        //送货员经纬度
        if ($localinfo['c_klongitude'] <= 0) {
            $localadd['c_klongitude'] = $parr['klongitude'];
        }
        if ($localinfo['c_klatitude'] <= 0) {
            $localadd['c_klatitude'] = $parr['klatitude'];
        }

        $localadd['c_updatetime'] = gdtime();
        if (!$localinfo) {
            $localadd['c_addtime'] = gdtime();
            $result = M('Order_delivery')->add($localadd);
        } else {
            $result = M('Order_delivery')->where($where)->save($localadd);
        }

        if (!$result) {
            return Message(3000,'记录失败');
        }

        return Message(0,'记录成功');
    }


    //自动取消订单  用户下单超过3天未支付系统将自动取消订单，释放产品库存和余额退回操作并给用户发送相关消息

    function OrderAutoCancel(){

        $orderDb = M('Order');
        $detailsDb =M('Order_details');

        $day =date("Y-m-d H:i:s",strtotime("-1 day"));
        $w['c_pay_state'] =0;
        $w['c_order_state'] =2;
        $w['c_deliverystate'] =0;
        $w['c_addtime'] =array("elt",$day);
        $list =$orderDb->where($w)->limit(10)->select(); //获取符合条件的订单

        if(!$list){
            return Message(1001,'暂时还没有符合条件的订单');
        }

        $db =M('');
        $db->startTrans();
        foreach($list as $value){
            $orderid =$value['c_orderid'];
            $details =$detailsDb->where(array('c_orderid'=>$orderid))->select();
            foreach($details as $key=>$val){   //操作订单详情信息
                //返回代理产品库存
                if ($val['c_isagent'] == 1) {
                    $atproductwhere['c_pcode'] = $val['c_agent_pcode'];
                    $result = M('Supplier_product')->where($atproductwhere)->setInc('c_num', $val['c_pnum']);
                    if (!$result) {
                        return Message(3007, '返回代理产品库存失败');
                    }
                    $pmproductwhere['c_mcode'] = $val['c_pmodel'];
                    $result = M('Supplier_product_model')->where($pmproductwhere)->setInc('c_num', $val['c_pnum']);
                    if (!$result) {
                        return Message(3007, '返回代理型号库存失败');
                    }
                    //返回所有代理产品的库存
                    $agtempwhere['c_agent_pcode'] = $val['c_agent_pcode'];
                    if (M('Product')->where($agtempwhere)->getField('c_id')) {
                        $result = M('Product')->where($agtempwhere)->setInc('c_num', $val['c_pnum']);
                        if (!$result) {
                            return Message(1012, "代理库存返回失败");
                        }

                        //返回所有代理产品的库存
                        if($val['c_pmodel']){
                            $agtempwhere5['c_mcode'] = $val['c_pmodel'];
                            $result = M('Product_model')->where($agtempwhere5)->setInc('c_num', $val['c_pnum']);
                            if (!$result) {
                                return Message(1012, "代理库存返回失败");
                            }
                        }

                    }
                } else {
                    // 返回总库存
                    $productwhere['c_pcode'] = $val['c_pcode'];
                    $result = M('Product')->where($productwhere)->setInc('c_num', $val['c_pnum']);
                    if (!$result) {
                        $db->rollback(); //不成功，则回滚
                        return Message(3007, '返回产品总库存失败');
                    }
                    // 返回型号库存
                    if($val['c_pmodel']){
                        $productwhere['c_mcode'] = $val['c_pmodel'];
                        $result = M('Product_model')->where($productwhere)->setInc('c_num', $val['c_pnum']);
//                        if (!$result) {
//                            $db->rollback(); //不成功，则回滚
//                            return Message(3007, '返回产品型号库存失败');
//                        }
                    }
                }
            }
            $update['c_order_state'] = 1;
            $where['c_orderid'] = $orderid;
            $where['c_order_state'] = array('neq',1);
            $result = M('Order')->where($where)->save($update);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1001, '修改订单状态失败');
            }

            $ucode = $value['c_ucode'];
            $backmoney = bcsub($value['c_actual_price'], $value['c_bmoney'],2);
            //返回用户余额
            if ($backmoney > 0) {
                $rebatemoney = IGD('Money', 'User');
                $parr['ucode'] = $ucode;
                $parr['money'] = $backmoney;
                $parr['source'] = 1;
                $parr['key'] = $orderid;
                $parr['desc'] = "取消订单余额支付";
                $parr['state'] = 1;
                $parr['type'] = 1;
                $parr['isagent'] = 0;
                $parr['showimg'] = 'Uploads/settlementshow/ding.png';
                $parr['showtext'] = '订单取消';
                $result = $rebatemoney->OptionMoney($parr);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }
            }
        }
        $db->commit();

        return Message(0, '自动取消成功');

    }



}


