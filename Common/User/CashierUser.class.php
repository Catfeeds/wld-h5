<?php

/**
 * 收银员
 *
 */
class CashierUser {

    /**
     * 同意成为收银员
     * @param ucode,acode,name
     */
    function AgreeCashier($parr)
    {
        //查询商家是否存在
        $auw['c_ucode'] = $parr['acode'];
        $acodeinfo = M('Users')->where($auw)->field('c_id')->find();
        if (!$acodeinfo) {
            return Message(3000,'该邀请无效');
        }
        
        // 查询用户
        $uw['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($uw)->field('c_nickname')->find();
        if (!$userinfo) {
            return Message(1009,'登录状态失效');
        }

        $where['c_status'] = 1;
        $where['c_ucode'] = $parr['ucode'];
        $casherinfo = M('A_cashier')->where($where)->find();
        if ($casherinfo) {
            return Message(3000,'已经是收银员不能再同意');
        }

        $add['c_ucode'] = $parr['ucode'];
        $add['c_acode'] = $parr['acode'];
        $add['c_name'] = $parr['name'];
        $add['c_status'] = 1;
        $add['c_addtime'] = gdtime();
        $add['c_updatetime'] = gdtime();
        $result = M('A_cashier')->add($add);
        if (!$result) {
            return Message(3000,'操作失败');
        }

        //操作成功给商家发送消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['acode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '【'.$userinfo['c_nickname'].'】已接受成为您的收银员。';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Store/Cashier/index';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Store/Cashier/index';
        $msgresult = $Msgcentre->CreateMessegeInfo($msgdata);

        return Message(0,'操作成功');
    }
    
    /**
     * 收银员基本信息
     * @param ucode
     */
    function GetCashierInfo($parr)
    {
        $where['c_status'] = 1;
        $where['c_delete'] = 2;
        $where['c_ucode'] = $parr['ucode'];
        $data = M('A_cashier')->where($where)->find();
        if (!$data) {
            return Message(3000,'资料信息不存在');
        }

        $uw['c_ucode'] = $data['c_acode'];
        $shopinfo = M('Users')->where($uw)->field('c_nickname')->find();

        $data['shopname'] = $shopinfo['c_nickname'];
        
        $parr['cashid'] = $data['c_id'];
        $this->CheckSign($parr);
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 获取获取收银员每日收款总记录
     * @param ucode,datetime 格式2017-03-01,pageindex,pagesize,cashid(deskid)
     */
    function GetdateLog($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if (!empty($parr['datetime'])) {
            $startime = $parr['datetime'].' 00:00:00';
            $endtime = $parr['datetime'].' 23:59:59';
            $where[] = array("c_addtime>='$startime' and c_addtime<='$endtime'");
        }
        
        $where['c_pay_state']  = 1;
        if (!empty($parr['deskid'])) {
            $where['c_deskid']  = $parr['deskid'];
        }
        
        if (!empty($parr['cashid'])) {
            $where['c_cashierid']  = $parr['cashid'];
        }

        $field = '*';
        $order = 'c_id desc';
        $list = M('Scanpay')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();
       
        $count = M('Scanpay')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        $datesign = '';
        foreach ($list as $key => $value) {
            $list[$key]['time'] = date("H:i:s", strtotime($value['c_addtime']));
            $list[$key]['remarktime'] = date("m月d日", strtotime($value['c_addtime']));
            if ($datesign != $list[$key]['remarktime']) {
                $datesign = $list[$key]['remarktime'];
                $parr['datetime'] = date("Y-m-d", strtotime($value['c_addtime']));
                $list[$key]['moneycount'] = $this->CountDatePaylog($parr);
                $list[$key]['commission'] = $this->CountCommission($parr);
            }
            if ($value['c_pay_rule'] == 1) {
                $list[$key]['text'] = '支付宝支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/alpay.png';
            } else if ($value['c_pay_rule'] == 2 || $value['c_pay_rule'] == 3) {
                $list[$key]['text'] = '微信支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/wxpay.png';
            } else {
                $list[$key]['text'] = '余额支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/xmpay.png';
            }
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 统计每日收款记录
     * @param cashid,datetime 格式2017-03-01
     */
    function CountDatePaylog($parr)
    {
        $startime = $parr['datetime'].' 00:00:00';
        $endtime = $parr['datetime'].' 23:59:59';
        $where[] = array("c_addtime>='$startime' and c_addtime<='$endtime'");
        $where['c_pay_state']  = 1;
        $where['c_cashierid']  = $parr['cashid'];
        if (!empty($parr['deskid'])) {
            $where['c_deskid']  = $parr['deskid'];
        }
        
        if (!empty($parr['cashid'])) {
            $where['c_cashierid']  = $parr['cashid'];
        }
        $count = M('Scanpay')->where($where)->sum('c_money');
        if ($count <= 0) {
            $count = 0;
        }
        return round($count,2);
    }

    /**
     * 统计每日跨界支出
     * @param cashid,datetime 格式2017-03-01
     */
    function CountCommission($parr)
    {
        $startime = $parr['datetime'].' 00:00:00';
        $endtime = $parr['datetime'].' 23:59:59';
        $where[] = array("c_addtime>='$startime' and c_addtime<='$endtime'");
        $where['c_pay_state']  = 1;
        $where['c_cashierid']  = $parr['cashid'];
        if (!empty($parr['deskid'])) {
            $where['c_deskid']  = $parr['deskid'];
        }
        
        if (!empty($parr['cashid'])) {
            $where['c_cashierid']  = $parr['cashid'];
        }
        $count = M('Scanpay')->where($where)->sum('c_commission');
        if ($count <= 0) {
            $count = 0;
        }
        return round($count,2);
    }

    /**
     * 给收款订单加备注
     * @param ucode,cashid,ncode,desc
     */
    function OrderRemarks($parr)
    {
        //查询订单
        $where['c_ncode'] = $parr['ncode'];
        $where['c_cashierid'] = $parr['cashid'];
        $where['c_pay_state']  = 1;
        $scandata = M('Scanpay')->where($where)->find();
        if (!$scandata) {
            return Message(3000,'该交易不存在');
        }   

        $db = M('');
        $db->startTrans();

        $save['c_cashierdesc'] = $parr['desc'];
        $result = M('Scanpay')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(3001,'备注失败');
        }

        //同步金额记录表备注
        $save['c_cashierdesc'] = $parr['desc'];
        $mw['c_source'] = 9;
        $mw['c_key'] = $scandata['c_ncode'];
        $mw['c_cashierid'] = $parr['cashid'];
        $result = M('Users_moneylog')->where($mw)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(3001,'备注失败');
        }

        $db->commit();
        return Message(0,'备注成功');
    }

    /**
     * 获取收款订单详情
     * @param ncode,cashid
     */
    function CashierOrderInfo($parr)
    {
        $where['c_ncode'] = $parr['ncode'];
        $where['c_cashierid'] = $parr['cashid'];
        $data = M('Scanpay')->where($where)->find();
        if (!$data) {
            return Message(3000, '订单查询失败');
        }
        //查询商家名称
        $where1['c_ucode'] = $data['c_acode'];
        $shopinfo = M('Users')->where($where1)->field('c_nickname')->find();

        $lwhere['c_orderid'] = $parr['ncode'];
        $lwhere['c_source'] = 2;
        $lwhere['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($lwhere)->sum('c_money');
        $data['banlace'] = empty($banlacemoney)?0:$banlacemoney;
        $data['paymoney'] = bcsub($data['c_money'], $banlacemoney, 2);
        $data['c_anickname'] = $shopinfo['c_nickname'];
        return MessageInfo(0, '订单查询成功', $data);
    }

    /**
     * 获取商家收银台列表
     * @param acode
     */
    function GetCashierDesk($parr)
    {
        $where['c_delete'] = 2;
        $where['c_status'] = 2;
        $where['c_ucode'] = $parr['acode'];
        $data = M('A_cashier_desk')->where($where)->select();
        if (!$data) {
            return Message(3000,'没有相关收银台');
        }

        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 选择收银台上班
     * @param ucode,deskid
     */
    function CheckCashierDesk($parr)
    {
        //查询收银台
        $where['c_delete'] = 2;
        $where['c_status'] = 2;
        $where['c_id'] = $parr['deskid'];
        $deskinfo = M('A_cashier_desk')->where($where)->find();
        if (!$deskinfo) {
            return Message(3000,'收银台选择失败');
        }

        //查询收银员信息
        $cw['c_ucode'] = $parr['ucode'];
        $cw['c_status'] = 1;
        $casherinfo = M('A_cashier')->where($cw)->find();
        if (!$casherinfo) {
            return Message(3000,'收银员信息不存在');
        }

        $db = M('');
        $db->startTrans();

        //写入上班记录
        $parr['cashid'] = $casherinfo['c_id'];
        $parr['sign'] = 1;
        $result = $this->AddCashSigninfo($parr);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        //选择收银台
        $cw['c_id'] = $casherinfo['c_id'];
        $csave['c_deskid'] = $parr['deskid'];
        $csave['c_work'] = 1;
        $csave['c_updatetime'] = gdtime();
        $result = M('A_cashier')->where($cw)->save($csave);
        if (!$result) {
            $db->rollback();
            return Message(3001,'选择收银台失败');
        }

        //改变收银台营业状态
        $dsave['c_status'] = 1;
        $dsave['c_updatetime'] = gdtime();
        $result = M('A_cashier_desk')->where($where)->save($dsave);
        if (!$result) {
            $db->rollback();
            return Message(3002,'选择收银台失败');
        }

        $db->commit();
        return Message(0,'选择成功');
    }

    /**
     * 收银员下班
     * @param ucode,deskid,cashid
     */
    function LeaveCashierDesk($parr)
    {
        //查询收银员信息
        $cw['c_ucode'] = $parr['ucode'];
        $cw['c_id'] = $parr['cashid'];
        // $cw['c_status'] = 1;
        $casherinfo = M('A_cashier')->where($cw)->find();
        if (!$casherinfo) {
            return Message(3000,'收银员信息不存在');
        }


        $db = M('');
        $db->startTrans();

        //写入下班记录
        $parr['sign'] = 2;
        $result = $this->AddCashSigninfo($parr);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        //选择收银台
        $csave['c_deskid'] = $parr['deskid'];
        $csave['c_work'] = 2;
        $csave['c_updatetime'] = gdtime();
        $result = M('A_cashier')->where($cw)->save($csave);
        if (!$result) {
            $db->rollback();
            return Message(3001,'下班台失败');
        }

        //改变收银台营业状态
        $where['c_delete'] = 2;
        $where['c_status'] = 1;
        $where['c_id'] = $parr['deskid'];
        $dsave['c_status'] = 2;
        $dsave['c_updatetime'] = gdtime();
        $result = M('A_cashier_desk')->where($where)->save($dsave);
        if (!$result) {
            $db->rollback();
            return Message(3002,'下班失败');
        }

        $db->commit();
        return Message(0,'下班成功');
    }

    /**
     * 写入上下班记录
     * @param cashid,deskid,sign(1上班，2下班)
     */
    function AddCashSigninfo($parr)
    {
        if ($parr['sign'] == 2) {
            $where['c_cashierid'] = $parr['cashid'];
            $where['c_deskid'] = $parr['deskid'];
            $where['c_datetime'] = date('Y-m-d');
            $where[] = array("c_signtime is not null");
            $where[] = array("c_leavetime is null");
            $signinfo = M('A_cashier_sign')->where($where)->find();
            if (!$signinfo) {
                return Message(3000,'下班前请先上班打卡');
            }
        }

        $add['c_cashierid'] = $parr['cashid'];
        $add['c_deskid'] = $parr['deskid'];
        $add['c_datetime'] = date('Y-m-d');
        if ($parr['sign'] == 1) {
            $add['c_signtime'] = gdtime();
        } else {
            $add['c_leavetime'] = gdtime();
        }
        
        if ($signinfo) {
            $result = M('A_cashier_sign')->where($where)->save($add);
        } else {
            $result = M('A_cashier_sign')->add($add);
        }
        
        if (!$result) {
            return Message(3000,'操作失败');
        }

        return Message(0,'操作成功');
    }

    /**
     * 查询上下班记录
     * @param ucode,pageindex,pagesize,cashid,deskid
     */
    function SignLog($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if (!empty($parr['deskid'])) {
            $where['c_deskid']  = $parr['deskid'];
        }
        
        if (!empty($parr['cashid'])) {
            $where['c_cashierid']  = $parr['cashid'];
        }

        $where[] = array('c_leavetime is not null');
        $order = 'c_id desc';
        $list = M('A_cashier_sign')->where($where)->limit($countPage, $pageSize)->order($order)->select();
     
        $count = M('A_cashier_sign')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['timetit'] = date("m月d日", strtotime($value['c_datetime']));
            $list[$key]['signtime'] = date("H:i:s", strtotime($value['c_signtime']));
            $list[$key]['leavetime'] = date("H:i:s", strtotime($value['c_leavetime']));
            $list[$key]['hours'] = $this->sec2time(strtotime($value['c_leavetime'])-strtotime($value['c_signtime']));
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 检测超过凌晨12点，结束上班
     * @param ucode,cashid
     */
    function CheckSign($parr)
    {
        //查询上班记录
        $swhere['c_cashierid'] = $parr['cashid'];
        $swhere[] = array('c_leavetime is null');
        $signlog = M('A_cashier_sign')->where($swhere)->find();
        if (!$signlog) {
            return Message(3000,'没有相关记录');
        }

        if (strtotime($signlog['c_datetime'].''.'23:59:59') < time()) {
            $db = M('');
            $db->startTrans();

            $ws['c_id'] = $signlog['c_id'];
            $save['c_leavetime'] = date('Y-m-d 23:59:59',strtotime($signlog['c_signtime']));
            $result = M('A_cashier_sign')->where($ws)->save($save);
            if (!$result) {
                $db->rollback();
                return Message(3001,'操作记录失败');
            }

            //查询收银员是否是激活的工作状态
            $cw['c_id'] = $signlog['c_cashierid'];
            $cw['c_work'] = 1;
            $cw['c_status'] = 1;
            $cw['c_delete'] = 2;
            $result = M('A_cashier')->where($cw)->find();
            if ($result) {
                //新增上班记录
                $add['c_cashierid'] = $signlog['c_cashierid'];
                $add['c_deskid'] = $signlog['c_deskid'];
                $add['c_datetime'] = date('Y-m-d');
                $add['c_signtime'] = gdtime();
                $result = M('A_cashier_sign')->add($add);
                if (!$result) {
                    $db->rollback();
                    return Message(3000,'操作失败');
                }
            } else {
                // 改变收银台状态
                $where['c_delete'] = 2;
                $where['c_status'] = 1;
                $where['c_id'] = $signlog['c_deskid'];
                $dsave['c_status'] = 2;
                $dsave['c_updatetime'] = gdtime();
                $result = M('A_cashier_desk')->where($where)->save($dsave);
                //if (!$result) {
                    //$db->rollback();
                    //return Message(3001,'下班台失败');
                //}

                //改变上班状态
                $cw['c_id'] = $parr['cashid'];
                $csave['c_work'] = 2;
                $csave['c_updatetime'] = gdtime();
                $result = M('A_cashier')->where($cw)->save($csave);
                //if (!$result) {
                    //$db->rollback();
                    //return Message(3001,'下班台失败');
                //}
            }

            $db->commit();
            return Message(0,'操作成功');
        }
        return Message(3000,'没有相关记录');
    }

    /**
     * 定时器检测超过凌晨12点，结束上班，立马上班
     * @param 
     */
    function TimersCheckSign()
    {
        //查询上班记录
        $swhere[] = array('c_leavetime is null');
        $signlog = M('A_cashier_sign')->where($swhere)->limit(100)->select();
        if (!$signlog) {
            return Message(3000,'没有相关记录');
        }

        $ct = 0;
        foreach ($signlog as $key => $value) {
            if (strtotime($value['c_datetime'].''.'23:59:59') < time()) {
                $db = M('');
                $db->startTrans();

                $ws['c_id'] = $value['c_id'];
                $save['c_leavetime'] = date('Y-m-d 23:59:59',strtotime($value['c_signtime']));
                $result = M('A_cashier_sign')->where($ws)->save($save);
                if (!$result) {
                    $db->rollback();
                    return Message(3001,'操作记录失败');
                }

                //查询收银员是否是激活的工作状态
                $cw['c_id'] = $value['c_cashierid'];
                $cw['c_work'] = 1;
                $cw['c_status'] = 1;
                $cw['c_delete'] = 2;
                $result = M('A_cashier')->where($cw)->find();
                if ($result) {
                    //新增上班记录
                    $add['c_cashierid'] = $value['c_cashierid'];
                    $add['c_deskid'] = $value['c_deskid'];
                    $add['c_datetime'] = date('Y-m-d');
                    $add['c_signtime'] = gdtime();
                    $result = M('A_cashier_sign')->add($add);
                    if (!$result) {
                        $db->rollback();
                        return Message(3000,'操作失败');
                    }
                } else {
                    // 改变收银台状态
                    $where['c_delete'] = 2;
                    $where['c_status'] = 1;
                    $where['c_id'] = $value['c_deskid'];
                    $dsave['c_status'] = 2;
                    $dsave['c_updatetime'] = gdtime();
                    $result = M('A_cashier_desk')->where($where)->save($dsave);
                    // if (!$result) {
                        // $db->rollback();
                        // return Message(3000,'下班台失败');
                    // }

                    //改变上班状态
                    $cw['c_id'] = $value['c_cashierid'];
                    $csave['c_work'] = 2;
                    $csave['c_updatetime'] = gdtime();
                    $result = M('A_cashier')->where($cw)->save($csave);
                    //if (!$result) {
                        //$db->rollback();
                        //return Message(3001,'下班台失败');
                    //}
                }

                $ct++;
                $db->commit();
            }
        }
        return MessageInfo(0,'操作成功',$ct);
    }

    function sec2time($sec){
        $sec = round($sec/60);
        if ($sec >= 60){
            $hour = floor($sec/60);
            $min = $sec%60;
            $res = $hour.'小时';
            $min != 0  &&  $res .= $min.'分';
        }else{
            $res = $sec.'分钟';
        }
        return $res;
    }

}
