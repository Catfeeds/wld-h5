<?php

/**
 * 代理商城订单接口
 */
class AgorderOrder {

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
        $model = $parr['model'];

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

        if ($delivery == 2) {
            $free = 0;
        }

        $db = M('');
        $db->startTrans();

        $orderid = 'l'.CreateOrder();
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
        $result = IGD('Order', 'Order')->CreataOrderAddress($orderid, $addressid);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $countprice = $totprice + $free;       

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

    //拆分商家和产品信息并计算代理价格
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
            if (!empty($value['mcode']) && strpos($value['mcode'], 'xn') === false) {
                //查询商品价格
                $wheremodel['c_pcode'] = $value['pcode'];
                $wheremodel['c_mcode'] = $value['mcode'];
                $ProductModel = M('Product_model')->where($wheremodel)->find();
                if (count($ProductModel) <= 0) {
                    return Message(1015, "没有查询到该产品型号");
                } else {
                    $price = $ProductModel['c_price'];
                    // $num = $ProductModel['c_num'];
                }
            }

            // 判断产品库存
            if ($num < $value['num']) {
                return Message(1015, "产品库存不足");
            }

            //查询代理级别
            $pw['c_acode'] = $productinfo['c_ucode'];
            $pw['c_ucode'] = $ucode;
            $level = M('Agency_member')->where($pw)->getField('c_grade');
            if ($level) {
                //查询代理产品等级价格优惠
                $disw['c_grade'] = $level;
                $disw['c_pcode'] = $productinfo['c_pcode'];
                $discount = M('Agency_product_dis')->where($disw)->getField('c_discount');
                if ($discount > 0) {
                    $price = sprintf("%.2f", $price*$discount/10);
                }
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
            $temp = "dl" . $int;
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
            $tempdetails['c_profit'] = $singletotle;
            $tempdetails['c_pimg'] = $v1['pimg'];
            $tempdetails['c_pucode'] = $v1['pucode'];
            $tempdetails['c_isagent'] = $v1['isagent'];
            $tempdetails['c_agent_pcode'] = $v1['agent_pcode'];
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
        $aorderinfo['c_acode'] = $acode;
        $aorderinfo['c_pay_rule'] = 4;
        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_deliverystate'] = 0;
        $aorderinfo['c_free'] = $free;
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = $delivery;
        $aorderinfo['c_postscript'] = $postscript;
        $aorderinfo['c_source'] = 3;
        $aorderinfo['c_isagent'] = $isagent;
        $aorderinfo['c_model'] = $model;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }

        return MessageInfo(0, "订单创建成功",$aorderinfo);
    }

    //减少商品库存
    protected function Reduceinventory($pcode, $pmodel, $agentpcode, $isagent, $num) {
        $productinfo['c_pcode'] = $pcode;
        $productinfo['c_num'] = array('egt', $num);
        $result = M('Product')->where($productinfo)->setDec('c_num', $num);
        if (!$result) {
            return Message(1013, "库存扣除失败");
        }

        // 减少型号库存
        // $productinfo['c_mcode'] = $pmodel;
        // $productinfo['c_num'] = array('egt', $num);
        // $result = M('Product_model')->where($productinfo)->setDec('c_num', $num);
        // if (!$result) {
        //     return Message(1014, "库存扣除失败");
        // }
            
        return Message(0, "库存修改成功");
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
        $orderinfo = M('Order')->where($whereinfo)->find();

        if (count($orderinfo) <= 0) {
            return Message(0, "该订单信息不存在");
        }
        //获取可抵扣优惠券数量
        $result = IGD('Coupon','Newact')->GetUseCouponNum($parr);
        $orderinfo['couponnum'] = $result['data'];

        $detail = M('Order_details')->where($whereinfo)->select();

        foreach ($detail as $key => $value) {
            $detail[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }

        //随机获取线上系统商户号
        $mchidarr = explode(',', C('LINEMICH'));
        $mch_id = $mchidarr[rand(0,(count($mchidarr)-1))];

        $orderinfo['detail'] = $detail;
        $orderinfo['mch_id'] = $mch_id;
        return MessageInfo(0, "订单查询成功", $orderinfo);
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
            $result = IGD('Order','Order')->SavePayorder($payorderid,1);
            if ($result['code'] != 0) { 
                $db->rollback(); //不成功，则回滚
                return $result;
            }
        }

        //用户支付记录操作
        $result = IGD('Order', 'Order')->paylog($orderid, $payrule, $actualprice, $thirdpartynum,3,$payorderid);
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

        //代理商品
        $agparr['orderid'] = $orderid;
        $result = $this->AgencyGoods($agparr);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
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
            $arr['desc'] = '平台代理订单交易资金操作';
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
        $shopprofit = 0;
        foreach ($detaillist as $key => $value) {

            //修改订单详情状态
            $orderdetel['c_detailid'] = $value['c_detailid'];
            $save['c_profit'] = $value['c_ptotal'];
            $save['c_commission'] = 0;
            $result = M('Order_details')->where($orderdetel)->save($save);
            // if ($result <= 0) {
            //     return Message(1017, "订单详情操作失败");
            // }
            
            $shopprofit = bcadd($shopprofit,$save['c_profit'],2);
        }

        return MessageInfo(0, "操作成功",$shopprofit);
    }

    /**
     * 代理商品入口
     * @param orderid
     */
    public function AgencyGoods($parr)
    {
        $orderid = $parr['orderid'];

        //查询订单信息
        $orderwhere['c_orderid'] = $orderid;
        $orderinfo = M('Order')->where($orderwhere)->find();
        $tempcount = $orderinfo['c_total_price'] + $orderinfo['c_free'];        

        //查询代理等级列表
        $agparr['ucode'] = $orderinfo['c_acode'];
        $agparr['agentucode'] = $orderinfo['c_ucode'];
        $result = IGD('Agency','Store')->AgencyGrade($agparr);
        $levellist = $result['data'];$isagent = 0;
        if (count($levellist) > 0) {
            foreach ($levellist as $key => $value) {
                $level = $value['level'];
                $money = $value['money'];
                $newmoney = $money + $tempcount;
                if ($newmoney >= $value['c_jy_money']) {
                    $grade = $value['c_grade'];
                    $grade_name = $value['c_grade_name'];
                    $isagent = 1;
                }
            }

            if ($isagent == 1) {
                //修改代理记录
                $aglog['c_updatetime'] = gdtime();
                $aglog['c_acode'] = $orderinfo['c_acode'];
                $aglog['c_ucode'] = $orderinfo['c_ucode'];
                $aglog['c_money'] = $newmoney;
                $aglog['c_grade'] = $grade;
                $aglog['c_grade_name'] = $grade_name;
                if ($level) {
                    $result = M('Agency_member')->where($aw)->save($aglog);
                } else {
                    $aglog['c_addtime'] = gdtime();
                    $result = M('Agency_member')->add($aglog);
                }

                //代理订单详情的所有产品
                $wheredetail['c_orderid'] = $orderid;
                $detailslist = M('Order_details')->where($wheredetail)->select();
                foreach ($detailslist as $k => $v) {
                    
                    //查询产品信息
                    $prowhere['c_pcode'] = $v['c_pcode'];
                    $productinfo = M('Product')->where($prowhere)->find();

                    //代理产品
                    $result = $this->AgentAddproduct($productinfo, $orderinfo['c_ucode'],$k+1,$v['c_pnum'],$v['c_pmodel']);
                    if ($result['code'] != 0) {
                        return $result;
                    }

                    //添加代理消费记录
                    $v['c_acode'] = $orderinfo['c_acode'];
                    $result = $this->Addjylog($v);
                    if ($result['code'] != 0) {
                        return $result;
                    }
                }
            }
        }

        return Message(0,'代理成功');
    }

    //添加代理消费记录
    public function Addjylog($orderdetial)
    {
        $jylog['c_ucode'] = $orderdetial['c_ucode'];
        $jylog['c_acode'] = $orderdetial['c_acode'];
        $jylog['c_pcode'] = $orderdetial['c_pcode'];
        $jylog['c_mcode'] = $orderdetial['c_pmodel'];
        $jylog['c_price'] = $orderdetial['c_pprice'];
        $jylog['c_num'] = $orderdetial['c_pnum'];
        $jylog['c_money'] = $orderdetial['c_ptotal'] + $orderdetial['c_free'];
        $jylog['c_addtime'] = gdtime();
        $result = M('Agency_jylog')->add($jylog);
        if (!$result) {
            return Message(3000, "添加记录失败");
        }

        return Message(0,'添加成功');
    }

    //修改代理产品库存
    public function EditProductNum($pinfo)
    {
        $prowhere['c_isagent'] = 2;
        $prowhere['c_pcode'] = $pinfo['c_pcode'];
        $save['c_agency_num'] = $pinfo['c_agency_num'] + $pinfo['num'];
        $save['c_num'] = $pinfo['c_num'] + $pinfo['num'];
        $save['c_updatetime'] = gdtime();
        $save['c_ishow'] = 1;
        $save['c_isdele'] = 1;
        $result = M('Product')->where($prowhere)->save($save);
        if (!$result) {
            return Message(3000, "修改代理库存失败");
        }

        return Message(0,'操作成功');
    }

    //一键代理添加产品
    public function AgentAddproduct($v, $ucode, $count,$num,$pmodel) {
        $pcode = 'dl'.$count.time();
        $db = M('');
        $dailipcode = $v['c_pcode'];
        $gettime = gdtime();

        //查询是否代理
        $agentprowhere['c_isagent'] = 2;
        $agentprowhere['c_ucode'] = $ucode;
        $agentprowhere['c_agent_pcode'] = $v['c_pcode'];
        $agentprowhere['c_isdele'] = 1;
        $agentproinfo = M('Product')->where($agentprowhere)->find();
        if (!$agentproinfo) {
            $info = array();
            $info['c_pcode'] = $pcode;
            $info['c_ucode'] = $ucode;
            $info['c_name'] = $v['c_name'];
            $info['c_desc'] = $v['c_desc'];
            $info['c_ismodel'] = $v['c_ismodel'];
            $info['c_pimg'] = $v['c_pimg'];
            $info['c_agency_num'] = $num;
            $info['c_num'] = $num;
            $info['c_price'] = $v['c_price'];
            $info['c_categoryid'] = $v['c_categoryid'];
            $info['c_isfree'] = $v['c_isfree'];
            $info['c_freeprice'] = $v['c_freeprice'];
            $info['c_ishow'] = $v['c_ishow'];
            $info['c_isdele'] = $v['c_isdele'];
            $info['c_isshoptuijian'] = 0;
            $info['c_source'] = $v['c_source'];
            $info['c_isrebate'] = 0;
            $info['c_isspread'] = 0;
            $info['c_isagent'] = 2;
            $info['c_agent_pcode'] = $v['c_pcode'];
            $info['c_addtime'] = date('Y-m-d H:i:s', time());
            $info['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('Product')->add($info);
            if (!$result) {
                return Message(3006, "一键代理添加产品失败");
            }

            //插入图片信息
            $sql = "insert into t_product_img (c_pcode,c_pimgepath,c_sign,c_createtime,c_updatetime) "
                    . " select '$pcode',c_pimgepath,c_sign,'$gettime','$gettime' from t_product_img where c_pcode='$dailipcode'";
            $result = $db->execute($sql);
            if (!$result) {
                return Message(3007, "一键代理添加产品图片失败");
            } 
        } else {
            $pcode = $agentproinfo['c_pcode'];
            $result = M('Product')->where($agentprowhere)->setInc('c_num',$num);
            if (!$result) {
                return Message(3005,'添加产品库存失败');
            }
        }

        //查询要代理的型号是否存在
        $dlpmw['c_pcode'] = $dailipcode;
        $dlpmw['c_mcode'] = $pmodel;
        $dlpmodl = M('Product_model')->where($dlpmw)->find();
        if ($dlpmodl) {
            //查询型号是否已代理
            $pmw['c_pcode'] = $pcode;
            $pmw['c_mcode'] = $pmodel;
            $pmodelinfo = M('Product_model')->where($pmw)->find();
            if ($pmodelinfo) {
                $result = M('Product_model')->where($pmw)->setInc('c_num',$num);
            } else {
                $madd['c_pcode'] = $pcode;
                $madd['c_mcode'] = $pmodel;
                $madd['c_num'] = $num;
                $madd['c_price'] = $dlpmodl['c_price'];
                $madd['c_name'] = $dlpmodl['c_name'];
                $madd['c_addtime'] = gdtime();
                $result = M('Product_model')->add($madd);
            }

            if (!$result) {
                return Message(3008, "一键代理添加型号失败");
            }
        }

        return Message(0,'代理成功');
    }


}
