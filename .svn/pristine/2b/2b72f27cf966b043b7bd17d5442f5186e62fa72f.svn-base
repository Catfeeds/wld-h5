<?php


/**
 *  小蜜商城维权处理接口
 */
class SupplyrefundAgorder {

    //售后退款退货
    public function Refundinfor($parr) {
    	$db = M('');
        $db->startTrans();

        $result = $this->OptionRefundinfor($parr);
        if ($result['code'] != 0) {
        	$db->rollback();
        	return $result;
        }

        //提交事务
        $db->commit();
        //给供货商发送相关消息

        return Message(0, "提交申请成功");
    }

    //售后退款退货操作
    public function OptionRefundinfor($parr)
    {
    	//查询产品详情
        $detailswhere['c_detailid'] = $parr['detailid'];
        $detailinfo = M('Supplier_order_details')->where($detailswhere)->find();

        if (count($detailinfo) == 0) {
            return Message(1021, "该订单不存在该产品");
        }

        $orderwhere['c_orderid'] = $detailinfo['c_orderid'];
        $orderinfo = M('Supplier_order')->where($orderwhere)->find();

        if (count($orderinfo) == 0) {
            return Message(1021, "该订单不存在");
        }

        if ($detailinfo['c_productstatus'] != 0) {
            return Message(1021, "该产品已处于维权状态，不能再次进行维权");
        }

        if ($orderinfo['c_pay_state'] != 1) {
            return Message(1021, "该订单未支付，不能进行退款操作");
        }

        if ($parr['type'] == 1) {
            if ($orderinfo['c_deliverystate'] != 0) {
                return Message(1021, "该订单不能进行退款维权");
            }
            $tag = 5;
            $remarks = "申请了退款";
            $total = $detailinfo['c_ptotal'] + $detailinfo['c_free'];
        } else {
            $tag = 6;
            $remarks = "申请了退款退货";
            $total = $detailinfo['c_ptotal'];
        }

        $refundinfo['c_refundcode'] = CreateOrder('st');
        $refundinfo['c_scode'] = $orderinfo['c_scode'];
        $refundinfo['c_ucode'] = $orderinfo['c_ucode'];
        $refundinfo['c_acode'] = $orderinfo['c_acode'];
        $refundinfo['c_orderid'] = $detailinfo['c_orderid'];
        $refundinfo['c_orderdetailid'] = $detailinfo['c_detailid'];
        $refundinfo['c_pcode'] = $detailinfo['c_pcode'];
        $refundinfo['c_mcode'] = $detailinfo['c_pmodel'];
        $refundinfo['c_pname'] = $detailinfo['c_pname'];
        $refundinfo['c_pimg'] = $detailinfo['c_pimg'];
        $refundinfo['c_ptotal'] = $detailinfo['c_ptotal'];
        $refundinfo['c_free'] = $detailinfo['c_free'];
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

        $result = M('Supplier_order_refund')->add($refundinfo);
        if (!$result) {
            return Message(1022, "维权信息操作失败");
        }

        //维权记录
        $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $orderinfo['c_ucode']);
        if ($result['code'] != 0) {
            return $result;
        }

        //修改产品订单状态
        $savedetail['c_productstatus'] = $parr['type'];
        $result = M('Supplier_order_details')->where($detailswhere)->save($savedetail);
        if ($result <= 0) {
            return Message(1022, "修改产品信息失败");
        }

        //判断是否还有正常的产品信息
        $detailswhere1['c_orderid'] = $orderinfo['c_orderid'];
        $detailswhere1['c_productstatus'] = 0;
        $tempdetailinfo = M('Supplier_order_details')->where($detailswhere1)->select();
        if (count($tempdetailinfo) == 0) {
            $save2['c_order_state'] = $tag;
            $result = M('Supplier_order')->where($orderwhere)->save($save2);
            if (!$result) {
                return Message(1022, "修改产品信息失败");
            }
        }

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

        $result = M('Supplier_order_refund_log')->add($info);
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
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();

        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        $db = M('');
        $db->startTrans();
        if ($refundinfo['c_type'] == 1) {
            //修改维权信息
            $save1['c_refundstate'] = 3;
            $save1['c_handletime'] = date('Y-m-d H:i:s', time());
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];

            $save2['c_productstatus'] = 6;

            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "订单详情操作失败");
            }

            //判断是否还有正常的产品信息
            $detailswhere1['c_orderid'] = $refundinfo['c_orderid'];
            $detailswhere1['c_productstatus'] = array('eq', 0);
            $tempdetailinfo = M('Supplier_order_details')->where($detailswhere1)->select();

            if (count($tempdetailinfo) == 0) {
                $save3['c_order_state'] = 4;
                $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
                $result = M('Supplier_order')->where($orderwhere)->save($save3);
                if (!$result) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1022, "修改订单信息失败");
                }
            }

            //写入用户余额
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $refundinfo['c_ucode'];
            $parr['money'] = $refundinfo['c_total'];
            $parr['source'] = 17;
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

            $remarks = "已同意您的产品已经退款";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);

            if ($result['code'] !== 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
            //提交事务
            $db->commit();

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '卖家已通过您的退款申请，已退款成功';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $Msgcentre->CreateMessege($msgdata);

            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您提交的退款申请已成功，余额￥' . $refundinfo['c_total'] . '已到帐';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $Msgcentre->CreateMessege($msgdata);

            return Message(0, "退款成功");
        } else {
            //修改维权信息
            $save1['c_refundstate'] = 1;
            $save1['c_addressid'] = $parr['addressid'];
            $save1['c_handletime'] = date('Y-m-d H:i:s');
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 4;
            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);

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

            //提交事务
            $db->commit();

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '卖家已同意您的退款退货申请，请您尽快填写退货快递信息';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $Msgcentre->CreateMessege($msgdata);
            return Message(0, "同意退款退货");
        }
    }

    // 操作同意维权操作
    public function OptionAgreeRefund($parr)
    {
    	//查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();
        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        if ($refundinfo['c_type'] == 1) {
            //修改维权信息
            $save1['c_refundstate'] = 3;
            $save1['c_handletime'] = date('Y-m-d H:i:s', time());
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);
            if (!$result) {
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 6;
            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);
            if (!$result) {
                return Message(1022, "订单详情操作失败");
            }

            //判断是否还有正常的产品信息
            $detailswhere1['c_orderid'] = $refundinfo['c_orderid'];
            $detailswhere1['c_productstatus'] = array('eq', 0);
            $tempdetailinfo = M('Supplier_order_details')->where($detailswhere1)->select();
            if (count($tempdetailinfo) == 0) {
                $save3['c_order_state'] = 4;
                $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
                $result = M('Supplier_order')->where($orderwhere)->save($save3);
                if (!$result) {
                    return Message(1022, "修改订单信息失败");
                }
            }

            $remarks = "已同意买家的产品退款";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);
            if ($result['code'] != 0) {
                return $result;
            }
  			return Message(0,'操作退款成功');
        } else {
            //修改维权信息
            $save1['c_refundstate'] = 1;
            $save1['c_addressid'] = $parr['addressid'];
            $save1['c_handletime'] = date('Y-m-d H:i:s');
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);
            if (!$result) {
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 4;
            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);
            if (!$result) {
                return Message(1022, "订单详情操作失败");
            }

            $remarks = "已同意买家的产品退货";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);
            if ($result['code'] != 0) {
                return $result;
            }

            $addresswhere['c_id'] = $parr['addressid'];
            $addressdata = M('Users_address')->where($addresswhere)->find();
            $remarks = "退货相关信息：退货人：" . $addressdata['c_consignee'] . "，联系电话：" . $addressdata['c_mobile'] . "，退货地址：" . $addressdata['c_provincename'] . $addressdata['c_cityname'] . $addressdata['c_districtname'] . $addressdata['c_address'];
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);
            if ($result['code'] != 0) {
                return $result;
            }
            return Message(0, "操作同意退款退货成功");
        }
    }

    //确认退款退货
    public function Refundreturn($parr) {

        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();

        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        $db = M('');
        $db->startTrans();


        //修改维权信息
        $save1['c_refundstate'] = 3;
        $save1['c_handletime'] = date('Y-m-d H:i:s');
        $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, "商家操作失败");
        }

        //修改产品信息操作
        $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];

        $save2['c_productstatus'] = 6;
        $result = M('Supplier_order_details')->where($detailinfo)->save($save2);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, "订单详情操作失败");
        }

        //判断是否还有正常的产品信息
        $detailswhere1['c_orderid'] = $refundinfo['c_orderid'];
        $detailswhere1['c_productstatus'] = array('eq', 0);
        $tempdetailinfo = M('Supplier_order_details')->where($detailswhere1)->select();

        if (count($tempdetailinfo) == 0) {
            $save3['c_order_state'] = 4;
            $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
            $result = M('Supplier_order')->where($orderwhere)->save($save3);

            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "修改订单信息失败");
            }
        }

        //写入用户余额
        $rebatemoney = IGD('Money', 'User');
        $parr['ucode'] = $refundinfo['c_ucode'];
        $parr['money'] = $refundinfo['c_total'];
        $parr['source'] = 17;
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
        //提交事务
        $db->commit();

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $refundinfo['c_ucode'];
        $msgdata['type'] = 1;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '订单消息';
        $msgdata['content'] = '卖家已收到您的退货，退款退货成功';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
        $Msgcentre->CreateMessege($msgdata);

        $msgdata['ucode'] = $refundinfo['c_ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = '您提交的退款退货申请已成功，余额￥' . $refundinfo['c_total'] . '已到帐';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
        $Msgcentre->CreateMessege($msgdata);

        return Message(0, "退款退货成功");
    }

    //操作确认退款退货
    public function OptionRefundreturn($parr) {
        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();
        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        //修改维权信息
        $save1['c_refundstate'] = 3;
        $save1['c_handletime'] = date('Y-m-d H:i:s');
        $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);
        if (!$result) {
            return Message(1022, "商家操作失败");
        }

        //修改产品信息操作
        $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
        $save2['c_productstatus'] = 6;
        $result = M('Supplier_order_details')->where($detailinfo)->save($save2);
        if (!$result) {
            return Message(1022, "订单详情操作失败");
        }

        //判断是否还有正常的产品信息
        $detailswhere1['c_orderid'] = $refundinfo['c_orderid'];
        $detailswhere1['c_productstatus'] = array('eq', 0);
        $tempdetailinfo = M('Supplier_order_details')->where($detailswhere1)->select();
        if (count($tempdetailinfo) == 0) {
            $save3['c_order_state'] = 4;
            $orderwhere['c_orderid'] = $refundinfo['c_orderid'];
            $result = M('Supplier_order')->where($orderwhere)->save($save3);
            if (!$result) {
                return Message(1022, "修改订单信息失败");
            }
        }

        $remarks = "已同意您的产品已经退款";
        $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);
        if ($result['code'] != 0) {
            return $result;
        }
        return Message(0, "操作退款退货成功");
    }

    //不同意提款
    public function disagreeAgree($parr) {
        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();

        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        $db = M('');
        $db->startTrans();

        if ($refundinfo['c_type'] == 1) {

            //修改维权信息
            $save1['c_refundstate'] = 2;
            $save1['c_handletime'] = date('Y-m-d H:i:s');
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 5;
            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);

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
            //提交事务
            $db->commit();

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '卖家不同意您的退款申请，请您及时与卖家联系协商';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $Msgcentre->CreateMessege($msgdata);

            return Message(0, "不同意成功");
        } else {
            //修改维权信息
            $save1['c_refundstate'] = 2;
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 4;
            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);

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

            //提交事务
            $db->commit();

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $refundinfo['c_ucode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '卖家不同意您的退款退货申请，请您及时与卖家联系协商';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/warranty_info?rcode=' . $parr['rcode'];
            $Msgcentre->CreateMessege($msgdata);

            return Message(0, "商家不同意退款退货");
        }
    }

    //操作不同意提款
    public function OptiondisagreeAgree($parr) {
        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();
        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        if ($refundinfo['c_type'] == 1) {
            //修改维权信息
            $save1['c_refundstate'] = 2;
            $save1['c_handletime'] = date('Y-m-d H:i:s');
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);
            if ($result <= 0) {
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 5;
            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);
            if ($result <= 0) {
                return Message(1022, "订单详情操作失败");
            }

            $remarks = "不同意您的产品进行退款";
            $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_acode']);
            if ($result['code'] != 0) {
                return $result;
            }
            return Message(0, "不同意成功");
        } else {
            //修改维权信息
            $save1['c_refundstate'] = 2;
            $result = M('Supplier_order_refund')->where($whereinfo)->save($save1);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, "商家操作失败");
            }

            //修改产品信息操作
            $detailinfo['c_detailid'] = $refundinfo['c_orderdetailid'];
            $save2['c_productstatus'] = 4;
            $result = M('Supplier_order_details')->where($detailinfo)->save($save2);
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
            return Message(0, "商家不同意退款退货");
        }
    }

    //填写快递单号
    public function Updateexpress($parr) {
        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();

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

        $result = M('Supplier_order_refund')->where($whereinfo)->save($save);

        if ($result <= 0) {
            return Message(1022, "修改维权信息失败");
        }

        $remarks = "快递单号已经填写";
        $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_ucode']);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //提交事务
        $db->commit();

        //给供货商发送相关消息


        return Message(0, "提交成功");
    }

    //操作填写快递单号
    public function OptionUpdateexpress($parr) {
        //查询维权单号
        $whereinfo['c_refundcode'] = $parr['rcode'];
        $refundinfo = M('Supplier_order_refund')->where($whereinfo)->find();

        if (count($refundinfo) == 0) {
            return Message(1022, "没有查询到该维权单号");
        }

        if ($refundinfo['c_refundstate'] != 1) {
            return Message(1022, "该信息商家没有同意，不能进行下一步操作");
        }

        $whereinfo['c_refundcode'] = $parr['rcode'];
        $save['c_transcompany'] = $parr['transcompany'];
        $save['c_transno'] = $parr['transno'];
        $result = M('Supplier_order_refund')->where($whereinfo)->save($save);
        if ($result <= 0) {
            return Message(1022, "修改维权信息失败");
        }

        $remarks = "快递单号已经填写";
        $result = $this->RefundLog($refundinfo['c_refundcode'], $remarks, $refundinfo['c_ucode']);
        if ($result['code'] != 0) {
            return $result;
        }
        return Message(0, "提交成功");
    }

    //查询维权详情
    public function GetrefundInfo($parr) {
        $rcode = $parr['rcode'];
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        if (!empty($ucode)) {
            $join = 'left join t_supplier as b on a.c_acode=b.c_ucode';
            $whereinfo['a.c_ucode'] = $ucode;
            $field = 'a.*,b.c_name as c_nickname';
        }

        if (!empty($acode)) {
            $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
            $whereinfo['a.c_acode'] = $acode;
            $field = 'a.*,b.c_nickname';
        }

        if (!empty($rcode)) {
            $whereinfo['a.c_refundcode'] = $rcode;
        }


        $info = M('Supplier_order_refund as a')->join($join)->where($whereinfo)->field($field)->find();

        if (count($info) == 0) {
            return Message(1021, '没有查询到该维权信息');
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
        $info = M('Supplier_order_refund_log')->where($where)->order('c_addtime asc')->select();
        return MessageInfo(0, "查询成功", $info);
    }

}
