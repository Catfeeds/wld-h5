<?php

/**
 * 用户产品维权相关接口
 */
class RefundOrder {

    //售后退款退货操作
    public function Refundinfor($parr) {
        //查询产品详情
        $detailswhere['c_detailid'] = $parr['detailid'];
        $detailinfo = M('Order_details')->where($detailswhere)->find();

        if (count($detailinfo) == 0) {
            return Message(1021, "该订单不存在该产品");
        }

        $orderwhere['c_orderid'] = $detailinfo['c_orderid'];
        $orderinfo = M('Order')->where($orderwhere)->find();

        if (count($orderinfo) == 0) {
            return Message(1021, "该订单不存在");
        }

        if ($detailinfo['c_productstatus'] != 0) {
            return Message(1021, "该产品已处于维权状态，不能再次进行维权");
        }

        //活动抵扣支付产品不能维权处理
        if ($orderinfo['c_pay_rule'] == 5) {
            return Message(1021, "活动抵扣支付产品不能维权处理");
        }

        if ($orderinfo['c_pay_state'] != 1) {
            return Message(1021, "该订单未支付，不能进行退款操作");
        }

        $db = M('');
        $db->startTrans();

        //同步代理产品维权操作
        if ($orderinfo['c_isagent'] == 1) {
            $parr['detailid'] = 's'.$parr['detailid'];
            $result = IGD('Supplyrefund','Agorder')->OptionRefundinfor($parr);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
        }

        //修改产品订单状态
        $savedetail['c_productstatus'] = $parr['type'];
        $result = M('Order_details')->where($detailswhere)->save($savedetail);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, "修改产品信息失败");
        }


        //判断是否还有正常的产品信息
        $detailswhere1['c_orderid'] = $orderinfo['c_orderid'];
        $detailswhere1['c_productstatus'] = 0;
        $tempdetailinfo = M('Order_details')->where($detailswhere1)->select();
        $dfree = '0.00';
        if (count($tempdetailinfo) == 0) {
            $dfree = $orderinfo['c_free'];
            $save2['c_order_state'] = $tag;
            $result = M('Order')->where($orderwhere)->save($save2);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "修改产品信息失败");
            }
        } 

        if ($parr['type'] == 1) {
            if ($orderinfo['c_deliverystate'] != 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1021, "该订单不能进行退款维权");
            }
            $tag = 5;
            $remarks = "申请了退款";
            $total = $detailinfo['c_ptotal'] + $dfree - $orderinfo['c_bmoney'];
        } else {
            $tag = 6;
            $remarks = "申请了退款退货";
            $total = $detailinfo['c_ptotal'] - $orderinfo['c_bmoney'];
        }

        if ($total <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1021, "该订单使用优惠劵全额抵扣，不能进行退款操作");
        }

        $refundinfo['c_refundcode'] = CreateOrder('t');
        $refundinfo['c_ucode'] = $orderinfo['c_ucode'];
        $refundinfo['c_acode'] = $orderinfo['c_acode'];
        $refundinfo['c_orderid'] = $detailinfo['c_orderid'];
        $refundinfo['c_orderdetailid'] = $detailinfo['c_detailid'];
        $refundinfo['c_pcode'] = $detailinfo['c_pcode'];
        $refundinfo['c_mcode'] = $detailinfo['c_pmodel'];
        $refundinfo['c_pname'] = $detailinfo['c_pname'];
        $refundinfo['c_pimg'] = $detailinfo['c_pimg'];
        $refundinfo['c_ptotal'] = $detailinfo['c_ptotal'];
        $refundinfo['c_free'] = $dfree;
        $refundinfo['c_pmname'] = $detailinfo['c_pmodel_name'];
        $refundinfo['c_pprice'] = $detailinfo['c_pprice'];
        $refundinfo['c_pnum'] = $detailinfo['c_pnum'];
        $refundinfo['c_total'] = $total;
        $refundinfo['c_type'] = $parr['type'];
        $refundinfo['c_reason'] = $parr['reason'];
        $refundinfo['c_goods_status'] = $parr['status'];
        $refundinfo['c_remarks'] = $parr['remarks'];
        $refundinfo['c_img'] = $parr['img'];
        $refundinfo['c_refundstate'] = 0;
        $refundinfo['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Order_refund')->add($refundinfo);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, "维权信息操作失败");
        }



        $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $orderinfo['c_ucode']);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
              

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $orderinfo['c_acode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        if ($parr['type'] == 1) {
            $msgdata['content'] = '您的订单【' . $orderinfo['c_orderid'] . '】的宝贝和新主人缘分未到，已被申请退款，请及时处理。';
        } else {
            $msgdata['content'] = '您的订单【' . $orderinfo['c_orderid'] . '】的宝贝和新主人相处不融洽，已被申请退货，请及时处理。';
        }

        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/warranty_info?rcode=' . $refundinfo['c_refundcode'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/warranty_info?rcode=' . $refundinfo['c_refundcode'];
        $Msgcentre->CreateMessege($msgdata);

        //提交事务
        $db->commit();
        return Message(0, "提交申请成功");
    }

    //写入操作记录表
    public function RefundLog($rcode, $remarks, $ucode) {

        //获取用户信息
        $userinfowhere['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($userinfowhere)->field('c_nickname')->find();

        $tempremark = $userinfo['c_nickname'] . $remarks;
        $info['c_refundcode'] = $rcode;
        $info['c_remarks'] = $tempremark;
        $info['c_ucode'] = $ucode;
        $info['c_uname'] = $userinfo['c_nickname'];
        $info['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Order_refund_log')->add($info);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, "维权日志信息操作失败");
        }

        return Message(0, "操作成功");
    }

    //商家同意操作
    public function AgreeRefund($parr) {

        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Order_refund')->where($whereinfo)->find();
        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        $db = M('');
        $db->startTrans();

        $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
        $orderinfo = M('Order')->where($orderwhere)->find();
        //同步代理产品同意操作
        if ($orderinfo['c_isagent'] == 1) {
            $suparr['c_orderdetailid'] = 's'.$refundinfo['c_orderdetailid'];
            $parr['rcode'] = M('Supplier_order_refund')->where($suparr)->getField('c_refundcode');
            $result = IGD('Supplyrefund','Agorder')->OptionAgreeRefund($parr);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        }

        if ($refundinfo['c_type'] == 1) {

            //修改维权信息
            $whereinfo['c_refundstate'] = array('NEQ',3);
            $save1['c_refundstate'] = 3;
            $save1['c_handletime'] = date('Y-m-d H:i:s', time());
            $result = M('Order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];

            $save2['c_productstatus'] = 6;

            $result = M('Order_details')->where($detailinfo)->save($save2);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "订单详情操作失败");
            }

            //判断是否还有正常的产品信息
            $detailswhere1['c_orderid'] = $refundinfo['c_orderid'];
            $detailswhere1['c_productstatus'] = array('eq', 0);
            $tempdetailinfo = M('Order_details')->where($detailswhere1)->select();

            if (count($tempdetailinfo) == 0) {
                $save3['c_pay_state'] = 2;
                $save3['c_order_state'] = 4;
                $save3['c_confirmtime'] = date('Y-m-d H:i:s');
                $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
                $result = M('Order')->where($orderwhere)->save($save3);
                if (!$result) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1022, "修改订单信息失败");
                }
            }

            //写入用户余额
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $refundinfo['c_ucode'];
            $parr['money'] = $refundinfo['c_total'];
            $parr['source'] = 16;
            $parr['key'] = $refundinfo['c_refundcode'];
            $parr['desc'] = "您购买的产品" . $refundinfo['c_pname'] . "已经退款成功";
            $parr['state'] = 1; //完成状态
            $parr['type'] = 1;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/tuik.png';
            $parr['showtext'] = '退款';
            $result = $rebatemoney->OptionMoney($parr);

            if ($result['code'] !== 0) {
                $db->rollback(); //不成功，则回滚
                return $result; //修改用户余额失败
            }

            $remarks = "已同意您的产品退款";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);

            if ($result['code'] !== 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            //扣除商家银盛账户金额
            /*****   新增银盛支付代付代扣   ******/ 
            //当商家到账的利润大于支付利润采用代扣
            //当商家到账的利润小于支付利润采用代付
            if ($refundinfo['c_total'] > 0) {
                $arr['sign'] = 2; // 1 代付 2 代扣
                $opmoney = $refundinfo['c_total'];
                $arr['type'] = 2; // 1  实时结算  2 按日结算  3 按月结算
                $arr['ucode'] = $refundinfo['c_acode'];  //操作用户编码
                $arr['scode'] = $refundinfo['c_acode'];  //商家编码    
                $arr['bcode'] = $refundinfo['c_ucode'];  //用户编码
                $arr['orderid'] = CreateOrder('f');
                $arr['key'] = $refundinfo['c_refundcode'];
                $arr['desc'] = '平台用户订单退款资金操作';
                $arr['total_money'] = $refundinfo['c_total'];
                $arr['money'] = $opmoney;
                $arr['source'] = 1; // 1普城订单,2后台,3活动,4蜜城订单,5普城跨界,6提现,7注册,8老注册,9扫码,10转发,11绑定,12跨界扫码,13普城购返,14普城推返,15蜜城跨界,16普通退款,17蜜城退款,18红包',
                $res = IGD('Splitting','Order')->CreateRecord($arr);
                if($res['code']!=0){
                    $db->rollback(); //不成功，则回滚
                    return Message(1001,'创建代扣失败');
                }
            }
            /*****   新增银盛支付代付代扣   ******/

            //退回商家邮费
            // if ($refundinfo['c_free'] > 0) {
            //     $parr['ucode'] = $refundinfo['c_acode'];
            //     $parr['money'] = $refundinfo['c_free'];
            //     $parr['source'] = 16;
            //     $parr['key'] = $refundinfo['c_refundcode'];
            //     $parr['desc'] = "用户购买购买的产品：" . $refundinfo['c_pname'] . "，已经退款成功，返回邮费";
            //     $parr['state'] = 1; //完成状态
            //     $parr['type'] = 1;
            //     $parr['isagent'] = 0;
            //     $parr['showimg'] = 'Uploads/settlementshow/tuik.png';
            //     $parr['showtext'] = '退款';
            //     $result = $rebatemoney->OptionMoney($parr);
            //     if ($result['code'] !== 0) {
            //         $db->rollback(); //不成功，则回滚
            //         return $result; //修改用户余额失败
            //     }
            // }
           
            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '“众里寻他千百度，我就在灯火阑珊处。”您提交的退款申请已成功受理。';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' .$refundinfo['c_refundcode'];
            $Msgcentre->CreateMessege($msgdata);

            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您提交的退款申请已成功，余额￥' . $refundinfo['c_total'] . '已到帐';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $Msgcentre->CreateMessege($msgdata);
             //提交事务
            $db->commit();
            return Message(0, "退款成功");
        } else {
            //修改维权信息
            $save1['c_refundstate'] = 1;
            $save1['c_addressid'] = $parr['addressid'];
            $save1['c_handletime'] = date('Y-m-d H:i:s');
            $result = M('Order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 4;
            $result = M('Order_details')->where($detailinfo)->save($save2);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "订单详情操作失败");
            }

            $remarks = "已同意您的产品退货";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);

            if ($result['code'] !== 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            $addresswhere['c_id'] = $parr['addressid'];
            $addressdata = M('Users_address')->where($addresswhere)->find();
            $remarks = "退货相关信息：退货人：" . $addressdata['c_consignee'] . "，联系电话：" . $addressdata['c_mobile'] . "，退货地址：" . $addressdata['c_provincename'] . $addressdata['c_cityname'] . $addressdata['c_districtname'] . $addressdata['c_address'];
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);

            if ($result['code'] !== 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }            

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '您的宝贝退款退货申请已受理，请尽快将宝贝寄回到商家所提供的地址';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
            $Msgcentre->CreateMessege($msgdata);
            //提交事务
            $db->commit();
            return Message(0, "同意退款退货");
        }
    }

    //确认退款退货
    public function Refundreturn($parr) {

        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Order_refund')->where($whereinfo)->find();

        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        $db = M('');
        $db->startTrans();

        $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
        $orderinfo = M('Order')->where($orderwhere)->find();
        //同步代理产品同意操作
        if ($orderinfo['c_isagent'] == 1) {
            $suparr['c_orderdetailid'] = 's'.$refundinfo['c_orderdetailid'];
            $parr['rcode'] = M('Supplier_order_refund')->where($suparr)->getField('c_refundcode');
            $result = IGD('Supplyrefund','Agorder')->OptionRefundreturn($parr);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        }

        //修改维权信息
        $whereinfo['c_refundstate'] = array('NEQ',3);
        $save1['c_refundstate'] = 3;
        $save1['c_handletime'] = date('Y-m-d H:i:s');
        $result = M('Order_refund')->where($whereinfo)->save($save1);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, "商家操作失败");
        }

        //修改产品信息操作
        $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];

        $save2['c_productstatus'] = 6;
        $result = M('Order_details')->where($detailinfo)->save($save2);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, "订单详情操作失败");
        }

        //判断是否还有正常的产品信息
        $detailswhere1['c_orderid'] = $refundinfo['c_orderid'];
        $detailswhere1['c_productstatus'] = array('eq', 0);
        $tempdetailinfo = M('Order_details')->where($detailswhere1)->select();

        if (count($tempdetailinfo) == 0) {
            $save3['c_order_state'] = 4;
            $save3['c_confirmtime'] = date('Y-m-d H:i:s');
            $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
            $result = M('Order')->where($orderwhere)->save($save3);

            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "修改订单信息失败");
            }
        }

        //写入用户余额
        $rebatemoney = IGD('Money', 'User');
        $parr['ucode'] = $refundinfo['c_ucode'];
        $parr['money'] = $refundinfo['c_total'];
        $parr['source'] = 16;
        $parr['key'] = $refundinfo['c_refundcode'];
        $parr['desc'] = "您购买的产品" . $refundinfo['c_pname'] . "已经退款成功";
        $parr['state'] = 1;
        $parr['type'] = 1;
        $parr['isagent'] = 0;
        $parr['showimg'] = 'Uploads/settlementshow/tuik.png';
        $parr['showtext'] = '退款';
        $result = $rebatemoney->OptionMoney($parr);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $remarks = "已同意您的产品已经退款";
        $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //扣除商家银盛账户金额
        /*****   新增银盛支付代付代扣   ******/ 
        //当商家到账的利润大于支付利润采用代扣
        //当商家到账的利润小于支付利润采用代付
        if ($refundinfo['c_total'] > 0) {
            $arr['sign'] = 2; // 1 代付 2 代扣
            $opmoney = $refundinfo['c_total'];
            $arr['type'] = 2; // 1  实时结算  2 按日结算  3 按月结算
            $arr['ucode'] = $refundinfo['c_acode'];  //操作用户编码
            $arr['scode'] = $refundinfo['c_acode'];  //商家编码    
            $arr['bcode'] = $refundinfo['c_ucode'];  //用户编码
            $arr['orderid'] = CreateOrder('f');
            $arr['key'] = $refundinfo['c_refundcode'];
            $arr['desc'] = '平台用户订单退款资金操作';
            $arr['total_money'] = $refundinfo['c_total'];
            $arr['money'] = $opmoney;
            $arr['source'] = 1; // 1普城订单,2后台,3活动,4蜜城订单,5普城跨界,6提现,7注册,8老注册,9扫码,10转发,11绑定,12跨界扫码,13普城购返,14普城推返,15蜜城跨界,16普通退款,17蜜城退款,18红包',
            $res = IGD('Splitting','Order')->CreateRecord($arr);
            if($res['code']!=0){
                $db->rollback(); //不成功，则回滚
                return Message(1001,'创建代扣失败');
            }
        }
        /*****   新增银盛支付代付代扣   ******/

        //退回商家邮费
        if ($refundinfo['c_free'] > 0) {
            $parr['ucode'] = $refundinfo['c_acode'];
            $parr['money'] = $refundinfo['c_free'];
            $parr['source'] = 16;
            $parr['key'] = $refundinfo['c_refundcode'];
            $parr['desc'] = "用户购买购买的产品：" . $refundinfo['c_pname'] . "，已经退款成功，返回邮费";
            $parr['state'] = 1; //完成状态
            $parr['type'] = 1;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/tuik.png';
            $parr['showtext'] = '退款';
            $result = $rebatemoney->OptionMoney($parr);
            if ($result['code'] !== 0) {
                $db->rollback(); //不成功，则回滚
                return $result; //修改用户余额失败
            }
        }
       
        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $refundinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '卖家已收到您的退货，退款退货成功';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
        $Msgcentre->CreateMessege($msgdata);

        $msgdata['ucode'] = $refundinfo['c_ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = '您提交的退款退货申请已成功，余额￥' . $refundinfo['c_total'] . '已到帐';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $Msgcentre->CreateMessege($msgdata);

         //提交事务
        $db->commit();
        return Message(0, "退款退货成功");
    }

    //不同意提款
    public function disagreeAgree($parr) {

        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Order_refund')->where($whereinfo)->find();

        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        $db = M('');
        $db->startTrans();

        $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
        $orderinfo = M('Order')->where($orderwhere)->find();
        //同步代理产品同意操作
        if ($orderinfo['c_isagent'] == 1) {
            $suparr['c_orderdetailid'] = 's'.$refundinfo['c_orderdetailid'];
            $parr['rcode'] = M('Supplier_order_refund')->where($suparr)->getField('c_refundcode');
            $result = IGD('Supplyrefund','Agorder')->OptiondisagreeAgree($parr);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        }

        if ($refundinfo['c_type'] == 1) {

            //修改维权信息
            $save1['c_refundstate'] = 2;
            $save1['c_handletime'] = date('Y-m-d H:i:s');
            $result = M('Order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 5;
            $result = M('Order_details')->where($detailinfo)->save($save2);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "订单详情操作失败");
            }

            $remarks = "不同意您的产品进行退款";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);

            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
            
            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '卖家不同意您的退款申请，请您及时与卖家联系协商';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
            $Msgcentre->CreateMessege($msgdata);

            //提交事务
            $db->commit();
            return Message(0, "不同意成功");
        } else {
            //修改维权信息
            $save1['c_refundstate'] = 2;
            $result = M('Order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 4;
            $result = M('Order_details')->where($detailinfo)->save($save2);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "订单详情操作失败");
            }


            $remarks = "不同意您的产品进行退货";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);

            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '卖家不同意您的退款退货申请，请您及时与卖家联系协商';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/warranty_info?rcode=' . $refundinfo['c_refundcode'];
            $Msgcentre->CreateMessege($msgdata);

            //提交事务
            $db->commit();
            return Message(0, "商家不同意退款退货");
        }
    }

    //填写快递单号
    public function Updateexpress($parr) {

        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Order_refund')->where($whereinfo)->find();

        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        if ($refundinfo['c_refundstate'] != 1) {
            return Message(1022, "该信息商家没有同意，不能进行下一步操作");
        }

        $db = M('');
        $db->startTrans();
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $save['c_transcompany'] = $parr['transcompany'];
        $save['c_transno'] = $parr['transno'];

        $result = M('Order_refund')->where($whereinfo)->save($save);

        if ($result <= 0) {
            return Message(1022, "修改维权信息失败");
        }

        $remarks = "快递单号已经填写";
        $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_ucode']);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
        $orderinfo = M('Order')->where($orderwhere)->find();
        //同步代理产品填写快递单号
        if ($orderinfo['c_isagent'] == 1) {
            $suparr['c_orderdetailid'] = 's'.$refundinfo['c_orderdetailid'];
            $parr['rcode'] = M('Supplier_order_refund')->where($suparr)->getField('c_refundcode');
            $result = IGD('Supplyrefund','Agorder')->OptionUpdateexpress($parr);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        }

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $refundinfo['c_acode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '有买家已提交退货快递信息，请注意查收快递并确认买家的退款退货申请';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Order/Storeorder/warranty_info?rcode=' . $refundinfo['c_refundcode'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Storeorder/warranty_info?rcode=' . $refundinfo['c_refundcode'];
        $Msgcentre->CreateMessege($msgdata);

        //提交事务
        $db->commit();
        return Message(0, "提交成功");
    }

    //查询维权列表
    public function Getrefundlist($parr) {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        $type = $parr['type'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if (!empty($ucode)) {
            $whereinfo = "c_ucode='$ucode'";
        }

        if (!empty($acode)) {
            $whereinfo = "c_acode='$acode'";
        }

        $sql = "select * from (select c_id,c_refundcode,'' as c_scode,c_ucode,c_acode,c_orderid,c_orderdetailid,"
            ."c_pcode,c_mcode,c_pname,c_pimg,c_pmname,c_pprice,c_pnum,c_free,"
            ."c_ptotal,c_total,c_type,c_goods_status,c_reason,c_remarks,c_img,c_refundstate,c_handletime,"
            ."c_addressid,c_transcompany,c_transno,c_addtime,0 as ordertype from t_order_refund where $whereinfo union select c_id,"
            ."c_refundcode,c_scode,c_ucode,c_acode,c_orderid,c_orderdetailid,c_pcode,c_mcode,c_pname,c_pimg,c_pmname,"
            ."c_pprice,c_pnum,c_free,c_ptotal,c_total,c_type,c_goods_status,c_reason,c_remarks,c_img,c_refundstate,c_handletime,"
            ."c_addressid,c_transcompany,c_transno,c_addtime,1 as ordertype from t_supplier_order_refund where $whereinfo and (c_scode is null or c_scode='')"
            .") alias order by c_addtime desc limit $countPage,$pageSize";
        $model = M('');
        $list = $model->query($sql);

        if (!$list) {
            return MessageInfo(0, '查询成功');
        }

        foreach ($list as $key => $value1) {
            if(empty($value1['c_pmname'])){
                $list[$key]['c_pmname'] = '默认';
            }
            $list[$key]['c_pimg'] = GetHost() . '/' . $value1['c_pimg'];

            //拆分图片
            if (!empty($value1['c_img'])) {
                $arr = explode("|", $value1['c_img']);
                foreach ($arr as $key1 => $value2) {
                    $img['img'] = GetHost() . '/' . $value2;
                    $imglist[] = $img;
                }
            }
            $list[$key]['c_img'] = $imglist;

            if (!empty($ucode)) {
                $whereuser['c_ucode'] = $value1['c_acode'];
                if ($value1['ordertype'] == 1) {
                    $list[$key]['c_nickname'] = M('Supplier')->where($whereuser)->getField('c_name');
                } else {
                    $list[$key]['c_nickname'] = M('Users')->where($whereuser)->getField('c_nickname');
                }
            }

            if (!empty($acode)) {
                $whereuser['c_ucode'] = $value1['c_ucode'];
                $list[$key]['c_nickname'] = M('Users')->where($whereuser)->getField('c_nickname');
            }
        }

        //查询维权订单总数
        $sql1 = "select count(*) as tc from (select c_id,c_refundcode,'' as c_scode,c_ucode,c_acode,c_orderid,c_orderdetailid,"
            ."c_pcode,c_mcode,c_pname,c_pimg,c_pmname,c_pprice,c_pnum,c_free,"
            ."c_ptotal,c_total,c_type,c_goods_status,c_reason,c_remarks,c_img,c_refundstate,c_handletime,"
            ."c_addressid,c_transcompany,c_transno,c_addtime,0 as ordertype from t_order_refund where $whereinfo union select c_id,"
            ."c_refundcode,c_scode,c_ucode,c_acode,c_orderid,c_orderdetailid,c_pcode,c_mcode,c_pname,c_pimg,c_pmname,"
            ."c_pprice,c_pnum,c_free,c_ptotal,c_total,c_type,c_goods_status,c_reason,c_remarks,c_img,c_refundstate,c_handletime,"
            ."c_addressid,c_transcompany,c_transno,c_addtime,1 as ordertype from t_supplier_order_refund where $whereinfo and (c_scode is null or c_scode='')"
            .") alias";
        $countnum = $model->query($sql1);
        $count = $countnum[0]['tc'];
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //查询维权详情
    public function GetrefundInfo($parr) {
        $rcode = $parr['rcode'];
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        if (!empty($ucode)) {
            $join = 'left join t_users as b on a.c_acode=b.c_ucode';
            $whereinfo['a.c_ucode'] = $ucode;
        }

        if (!empty($acode)) {
            $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
            $whereinfo['a.c_acode'] = $acode;
        }

        if (!empty($rcode)) {
            $whereinfo['a.c_refundcode'] = $rcode;
        }

        $field = 'a.*,b.c_nickname';

        $info = M('Order_refund as a')->join($join)->where($whereinfo)->field($field)->find();

        if (count($info) == 0) {
            return Message(1021, '没有查询到该维权信息');
        }

        if(empty($info['c_pmname'])){
            $info['c_pmname'] = '默认';
        }

        $info['c_pimg'] = GetHost() . '/' . $info['c_pimg'];
        $addresswhere['c_id'] = $info['c_addressid'];
        $info['address'] = M('Users_address')->where($addresswhere)->find();

        //拆分图片
        if (!empty($info['c_img'])) {
            $arr = explode("|", $info['c_img']);
            foreach ($arr as $key => $value1) {
                $imgurl = GetHost() . '/' . $value1;
                $imglist[] = $imgurl;
            }
        }
        $info['c_img'] = $imglist;

        return MessageInfo(0, "维权信息查询成功", $info);
    }

    //查询用户维权详细信息
    public function Getrefundlog($parr) {
        $where['c_refundcode'] = $parr['rcode'];
        $info = M('Order_refund_log')->where($where)->order('c_addtime asc')->select();
        return MessageInfo(0, "查询成功", $info);
    }

}
