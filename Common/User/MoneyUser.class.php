<?php

/**
 * 操作用户金额接口
 */
class MoneyUser {

    /**
     *  用户金额操作
     *  @param ucode,money,source,key,desc,state,type,isagent,joinaid,showimg,showtext,(bkmoney,xmmoney),cashid,deskid,flag
     *  flag 是否参与分润 1 创建分润记录  2 创建记录并实时分发
     */
    function OptionMoney($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009,'请先登录再操作');
        }

        $isagent = $parr['isagent'];
        $type = $parr['type'];
        $source = $parr['source'];

        if ($type == 0) {
            //风控出金
            $ufparr['ucode'] = $parr['ucode'];
            $ufparr['sign'] = 2;
            $result = IGD('Funds','Info')->GetUseFunds($ufparr);
            if ($result['code'] != 0) {
                return $result;
            }
        }

        //查询联盟店id
        $unionresult = IGD('Chain','Store')->GetUnionInfo($parr);
        $unioninfo = $unionresult['data'];
        if (!$unioninfo['c_pid']) {
            $unioninfo['c_pid'] = $unioninfo['c_id'];
        }
        if (!$unioninfo['shopcode']) {
            $unioninfo['shopcode'] = $unioninfo['c_ucode'];
        }

        $utype = 1;    //1营收收入，2跨界收入
        if ($source == 1 || $source == 4 || $source == 5 || $source == 15) {
            $usign = 1;   //线上订单
            if ($source == 5 || $source == 15) {
                $utype = 2;
            }
        } else if ($source == 9 || $source == 12) {
            $usign = 2;   //线下扫码
            if ($source == 12) {
                $utype = 2;
            }
        }
        if ($unionresult['code'] == 0 && $type != 0 && !empty($usign)) {   //有归属联盟店
            $uparr['ucode'] = $parr['ucode'];
            $uparr['pid'] = $unioninfo['c_pid'];
            $uparr['federationid'] = $unioninfo['c_id'];
            $uparr['money'] = $parr['money'];
            $uparr['type'] = $utype;
            $uparr['sign'] = $usign;
            $result = $this->DateUnionCount($uparr);
            if ($result['code'] != 0) {
                return $result;
            }
        }


        $User = M('Users');

        //查询用户信息
        if ($unioninfo['c_type'] == 1 && !empty($usign)) {   //连锁店金额进入总店
            $userwhere['c_ucode'] = $unioninfo['shopcode'];
        } else {
            $userwhere['c_ucode'] = $parr['ucode'];
        }
        $Userinfo = $User->where($userwhere)->field('c_money,c_isagent,c_shop')->find();

        if (count($Userinfo) == 0) {
            return Message(1002, '用户不存在');
        }

        $moneylogdata['c_ucode'] = $parr['ucode'];
        if ($type == 0) {//支出余额
            $moneylogdata['c_money'] = '-' . $parr['money'];
            if (empty($parr['showimg'])) {
                $parr['showimg'] = 'Uploads/settlementshow/qit.png';
                $parr['showtext'] = '其他';
            }
        } else {//收入余额
            $moneylogdata['c_money'] = $parr['money'];
            if (empty($parr['showimg'])) {
                $parr['showimg'] = 'Uploads/settlementshow/qi.png';
                $parr['showtext'] = '其他';
            }
        }

        //操作用户余额
        if ($type == 0) {
            if ($Userinfo['c_money'] < $parr['money']) {
                return Message(1003, '您的金额不够');
            }
            $userwhere['c_money'] = array('EGT',$parr['money']);
            $result = $User->where($userwhere)->setDec('c_money', $parr['money']);
            if(!$result){
                return Message(1004, '金额减少失败');
            }
        } else {
            if ($unioninfo['c_type'] == 1 && !empty($usign)) {   //连锁店金额进入总店
                $userwhere['c_ucode'] = $unioninfo['shopcode'];
            }

            $bcmoney = bcsub($parr['money'],$parr['bkmoney'],2);
            if ($parr['xmmoney'] > 0 || $bcmoney <= 0) {
                $result = $User->where($userwhere)->setInc('c_money', $parr['xmmoney']);
                if(!$result && $parr['xmmoney'] > 0){
                    return Message(1004, '金额增加失败');
                }
            } else {
                $result = $User->where($userwhere)->setInc('c_money', $parr['money']);
                if(!$result){
                    return Message(1004, '金额增加失败');
                }
            }
        }

        //非连锁店写入金额记录
        if ($unioninfo['c_type'] != 1 || ($unioninfo['c_type'] == 1 && empty($usign)) || ($unioninfo['c_type'] == 1 && $type == 0) || $unioninfo['c_sign'] == 1) {
            //加入每日统计
            $result = $this->DateCount($parr);
            if ($result['code'] != 0) {
                return $result;
            }
        }

        // 写入金额记录
        $moneylogdata['c_showimg'] = $parr['showimg'];
        $moneylogdata['c_showtext'] = $parr['showtext'];
        $moneylogdata['c_joinaid'] = $parr['joinaid'];
        $moneylogdata['c_balance'] = $Userinfo['c_money'];
        $moneylogdata['c_source'] = $parr['source'];
        $moneylogdata['c_key'] = $parr['key'];
        $moneylogdata['c_desc'] = $parr['desc'];
        $moneylogdata['c_state'] = $parr['state'];
        if ($unioninfo['c_type'] == 1 && !empty($usign)) {   //连锁店金额进入总店
            $moneylogdata['c_ucode'] = $unioninfo['shopcode'];
        } else {
            $moneylogdata['c_ucode'] = $parr['ucode'];
        }
        $moneylogdata['c_isagent'] = $parr['isagent'];
        $moneylogdata['c_addtime'] = date('Y-m-d H:i:s');

        //新增
        $moneylogdata['c_bkmoney'] = $parr['bkmoney'];
        $moneylogdata['c_xmmoney'] = $parr['xmmoney'];
        $moneylogdata['c_cashierid'] = $parr['cashid'];
        $moneylogdata['c_deskid'] = $parr['deskid'];
        $moneylogdata['c_pfederationid'] = $unioninfo['c_pid'];
        $moneylogdata['c_federationid'] = $unioninfo['c_id'];

        $result = M('Users_moneylog')->add($moneylogdata);
        if (!$result) {
            return Message(1001, '增加金额记录失败');
        }

        //代付代扣相关操作
        $result = $this->SplittingProfit($parr,$Userinfo,$unioninfo,$usign,$utype,$result);
        if ($result['code'] != 0) {
            return $result;
        }

        return Message(0,'记录成功');
    }

    //代扣、代付部分相关操作
    function SplittingProfit($parr,$userinfo,$unioninfo,$usign,$utype,$money_log_id)
    {
        $type = $parr['type'];
        $source = $parr['source'];
        //source来源:1普城订单,2后台,3活动,4蜜城订单,5普城跨界,6提现,7注册,8老注册,9扫码,10转发,11绑定,12跨界扫码
        //13普城购返,14普城推返,15蜜城跨界,16普通退款,17蜜城退款,18红包

        if ($type == 1) {   // 代付
            $arr['sign'] = 1;
        } else {    //代扣
            $arr['sign'] = 2; 
        }

        $arr['type'] = 1; // 1  实时结算  2 按日结算  3 按月结算
        $arr['settled'] = 1;   //1已结算 2待结算
        $arr['settledtime'] = gdtime();
        if (($usign == 1 || $usign == 2) && $type == 1) {   //来源订单部分 （默认结算周期一天）
            $arr['type'] = 2;
            $arr['settled'] = 2;
            $arr['settledtime'] = '1';           
            if ($userinfo['c_isagent'] != 0) {      //代理商推广佣金按月结算
                $arr['type'] = 3;
            }

            //商家分润部分变更已结算
            if (($source == 1 && strpos($parr['desc'], '卖出商品') !== false) || ($source == 9 &&  strpos($parr['desc'], '用户扫码支付获得') !== false)) {
                $arr['status'] = 1;
                $arr['settledtime'] = date('Y-m-d 12:00:00',strtotime('+1 days',time()));
            }

        } 

        //取消订单实时结算
        if (strpos($parr['desc'], '取消订单余额支付') !== false) {
            $arr['type'] = 1;
        }

        //提现金额走代扣即时完成
        if ($source == 6 && $type == 0) {
            $arr['status'] = 1;
        }

        //退款返回商家邮费及时完成
        if ($source == 16 && strpos($parr['desc'], '已经退款成功，返回邮费') !== false) {
            $arr['status'] = 1;
        }

        if ($unioninfo['c_type'] == 1 && !empty($usign)) {   //连锁店金额进入总店
            $yesparr['ucode'] = $unioninfo['shopcode'];
        } else {
            $yesparr['ucode'] = $parr['ucode'];
        }     

        //查询商户开户情况
        $result = IGD('Ysepay','Scanpay')->GetYsedata($yesparr);
        if ($result['code'] != 0) {
            return Message(0,'操作成功');
        }
        $yseinfo = $result['data'];
        
        $arr['ucode'] = $parr['ucode'];
        $arr['orderid'] = CreateOrder("f");
        $arr['money'] = $parr['money'];
        $arr['source'] = $parr['source'];
        $arr['key'] = $parr['key'];
        $arr['desc'] = $parr['desc'];
        $arr['money_log_id'] = $money_log_id;
        $arr['joinaid'] = $parr['joinaid'];
        $arr['scode'] = '';
        $arr['bcode'] = '';
        $arr['total_money'] = '';
        $result = IGD('Splitting','Order')->CreateRecord($arr);
        if($result['code'] != 0){
            return $result;
        }    
        
        //操作结算款
        $result = $this->OptionSettleMoney($parr,$userinfo,$unioninfo,$usign,$utype,$money_log_id,$yseinfo);
        if($result['code'] != 0){
            return $result;
        }

        return Message(0,'操作成功');
    }

    //操作结算款
    function OptionSettleMoney($parr,$userinfo,$unioninfo,$usign,$utype,$money_log_id,$yseinfo)
    {
        $type = $parr['type'];
        $source = $parr['source'];

        if ($unioninfo['c_type'] == 1 && !empty($usign)) {   //连锁店金额进入总店
            $where['c_ucode'] = $unioninfo['shopcode'];
            $parr1['ucode'] = $unioninfo['shopcode'];
        } else {
            $where['c_ucode'] = $parr['ucode'];
            $parr1['ucode'] = $parr['ucode'];
        }
        
        $stmoneyinfo = M('Users_yesmoney')->where($where)->find();
        if ($stmoneyinfo) {
            //操作结算款
            if ($type == 1) {  //收入
                if ($usign == 1 || $usign == 2) {   //来源订单部分
                    $result = M('Users_yesmoney')->where($where)->setInc('c_stmoney',$parr['money']);
                    if (!$result) {
                        return Message(3000,'操作结算款失败');
                    }
                }
            }
            
            // $moneyinfo = $this->GetYesMoney($parr1,$yseinfo);
            // $ysmsave['c_ysmoney'] = $moneyinfo['zmoney'];
            // $ysmsave['c_ysdrmoney'] = $moneyinfo['drmoney'];
            // $ysmsave['c_ysstmoney'] = $moneyinfo['stmoney'];
            // $ysmsave['c_money'] = M('Users')->where(array('c_ucode'=>$parr1['ucode']))->getField('c_money');
            // $ysmsave['c_updatetime'] = gdtime();
            // $result = M('Users_yesmoney')->where($where)->save($ysmsave);   
        } else {
            if ($type == 1) {  //收入
                if ($usign == 1 || $usign == 2) {   //来源订单部分
                    $stmoney = $parr['money'];
                } else {
                    $stmoney = 0;
                }
            }
            
            $stadd['c_ucode'] = $parr1['ucode'];
            $moneyinfo = $this->GetYesMoney($parr1,$yseinfo);
            $stadd['c_ysmoney'] = $moneyinfo['zmoney'];
            $stadd['c_ysdrmoney'] = $moneyinfo['drmoney'];
            $stadd['c_ysstmoney'] = $moneyinfo['stmoney'];
            $stadd['c_money'] = M('Users')->where(array('c_ucode'=>$parr1['ucode']))->getField('c_money');
            $stadd['c_stmoney'] = $stmoney;
            $stadd['c_updatetime'] = gdtime();
            $result = M('Users_yesmoney')->add($stadd);
            if (!$result) {
                return Message(3000,'操作结算款失败');
            }
        }

        return Message(0,'操作成功');
    }

    /**
     * 定时器还原结算款到可提现金额
     * @param ucode,money
     */
    function BackStmoney()
    {
        $gdtime = gdtime();
        $where['c_settled'] = 2;
        $where['c_sign'] = 1;
        $where[] = array("c_settledtime<='$gdtime' and c_settledtime is not null and c_settledtime<>'0000-00-00 00:00:00'");
        $field = '*,sum(c_money) as tpmoney';
        $data = M('Users_order_splitting')->where($where)->group('c_ucode')->field($field)->order('c_id desc')->limit(100)->select();
        $n = 0;$m = 0;
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $dateweek = date("w");
                if ($dateweek != 6 && $dateweek != 0) {
                    $db = M('');
                    $db->startTrans();
                    $n++;

                    //更改结算状态
                    $where['c_ucode'] = $value['c_ucode'];
                    $bsave['c_settled'] = 1;
                    $result = M('Users_order_splitting')->where($where)->save($bsave);
                    if (!$result) {
                        $db->rollback();
                    }

                    //改变结算金额
                    $stw['c_ucode'] = $value['c_ucode'];
                    $stw['c_stmoney'] = array('EGT',$value['tpmoney']);
                    $result = M('Users_yesmoney')->where($stw)->setDec('c_stmoney',$value['tpmoney']);
                    if (!$result) {
                        $db->rollback();                    
                    } else {
                        $m++;
                    }

                    $db->commit();
                }
            }
        }

        return MessageInfo(0,'操作成功','总计：'.$n.'，结算成功：'.$m);
    }

    /**
     * 查询不可提现金额
     * @param ucode
     */
    function GetMoneyInfo($parr)
    {
        //查询开户情况
        $yw['c_ucode'] = $parr['ucode'];
        $yw['c_openaccount'] = 1;
        $field = 'c_ucode,c_openaccount,c_reason,c_person,c_personphone,c_username';
        $yseinfo = M('User_yspay')->where($yw)->field($field)->find(); 
        if (!$yseinfo) {
            return Message(3000,'开户记录不存在');
        } 

        $where['c_ucode'] = $parr['ucode'];
        $data = M('Users_yesmoney')->where($where)->find();

        //没有数据查询并写入数据
        if (empty($data)) {
            $stadd['c_ucode'] = $parr['ucode'];
            $moneyinfo = $this->GetYesMoney($parr,$yseinfo);
            $stadd['c_ysmoney'] = $moneyinfo['zmoney'];
            $stadd['c_ysdrmoney'] = $moneyinfo['drmoney'];
            $stadd['c_ysstmoney'] = $moneyinfo['stmoney'];
            $stadd['c_money'] = M('Users')->where(array('c_ucode'=>$parr['ucode']))->getField('c_money');
            $stadd['c_stmoney'] = 0;
            $stadd['c_updatetime'] = gdtime();
            $result = M('Users_yesmoney')->add($stadd);
            $stadd['c_id'] = $result;
            $data = $stadd;
        }

        return MessageInfo(0,'查询成功',$data);
    }

    //获取银盛账户金额
    function GetYesMoney($parr,$yseinfo)
    {
        // Vendor('Ysepay.Yse_pay');
        // $pay = new \Yse_pay();
        // $parr['partner_id'] = "wld17375717292";// 合作商户号
        // $parr['merchant_usercode'] = $yseinfo['c_username'];  //商户账号
        // $data = $pay->query_money($parr);
        // $result = $pay->curl_query_money($data);
        // $data1 = $result['ysepay_merchant_balance_query_response'];
        
        if (empty($data1)) {
            $redata['zmoney'] = '0.00';
            $redata['drmoney'] = '0.00';
            $redata['stmoney'] = '0.00';
        } else {
            $redata['zmoney'] = $data1['account_total_amount'];
            $redata['drmoney'] = $data1['account_detail']['0']['account_amount'];
            $redata['stmoney'] = $data1['account_detail']['1']['account_amount'];
        }
        
        return $redata;
    }

    /**
     * 加入联盟店统计数据
     * @param ucode,pid,federationid,money,type,sign
     */
    function DateUnionCount($parr)
    {
        $moneycount['c_ucode'] = $parr['ucode'];
        $moneycount['c_pid'] = $parr['pid'];
        $moneycount['c_federationid'] = $parr['federationid'];
        $moneycount['c_money'] = $parr['money'];
        $moneycount['c_type'] = $parr['type'];
        $moneycount['c_sign'] = $parr['sign'];
        $moneycount['c_datetime'] = date('Y-m-d');
        $moneycount['c_updatetime'] = gdtime();

        //查询记录是否存在
        $countwhere['c_type'] = $parr['type'];
        $countwhere['c_sign'] = $parr['sign'];
        $countwhere['c_ucode'] = $parr['ucode'];
        $countwhere['c_datetime'] = date('Y-m-d');
        $countinfo = M('A_federation_moneylog')->where($countwhere)->find();
        $locks = IGD('Common','Redis')->Rediesgetucode('A_federation_moneylog-'.$moneycount['c_sign'].'-'.$moneycount['c_type'].'-'.$parr['ucode'].'-'.date('Y-m-d'));
        if (!$countinfo && !$locks) {
            IGD('Common', 'Redis')->RediesStoreSram('A_federation_moneylog-'.$moneycount['c_sign'].'-'.$moneycount['c_type'].'-'.$parr['ucode'].'-'.date('Y-m-d'),1,20);
            $result = M('A_federation_moneylog')->add($moneycount);
        } else {
            $moneycount['c_money'] = $moneycount['c_money'] + $countinfo['c_money'];
            $result = M('A_federation_moneylog')->where($countwhere)->save($moneycount);
        }


        if (!$result) {
            IGD('Common', 'Redis')->RediesStoreSram('A_federation_moneylog-'.$moneycount['c_sign'].'-'.$moneycount['c_type'].'-'.$parr['ucode'].'-'.date('Y-m-d'),null);
            return Message(1001, '统计记录失败');
        }

        return Message(0, '统计记录成功');
    }

    /**
     * 加入用户每日金额统计
     * @param ucode,money,source,key,desc,state,type,isagent,joinaid
     */
    function DateCount($parr)
    {
        if ($parr['type'] == 0) {//支出余额
            $countwhere['c_sign'] = 2;
            $moneycount['c_sign'] = 2;
            $moneycount['c_money'] = '-' . $parr['money'];
        } else {//收入余额
            $countwhere['c_sign'] = 1;
            $moneycount['c_sign'] = 1;
            $moneycount['c_money'] = $parr['money'];
        }

        $moneycount['c_joinaid'] = $parr['joinaid'];
        $moneycount['c_ucode'] = $parr['ucode'];
        $moneycount['c_datetime'] = date('Y-m-d');
        $moneycount['c_updatetime'] = date('Y-m-d H:i:s');

        //查询金额类型
        if ($parr['source'] == 1 || $parr['source'] == 4 || $parr['source'] == 5
            || $parr['source'] == 13 || $parr['source'] == 14 || $parr['source'] == 15
            || $parr['source'] == 16 || $parr['source'] == 17) {//线上订单
            $countwhere['c_type'] = 3;
            $moneycount['c_type'] = 3;
        } else if ($parr['source'] == 9 || $parr['source'] == 12) {  //线下扫码
            $countwhere['c_type'] = 2;
            $moneycount['c_type'] = 2;
        } else if ($parr['source'] == 6) {  //提现
            $countwhere['c_type'] = 4;
            $moneycount['c_type'] = 4;
            if ($countwhere['c_sign'] == 1) {
                $countwhere['c_type'] = 1;
                $moneycount['c_type'] = 1;
            }
        } else if ($parr['source'] == 3) {  //来源活动部分
            //查询活动规则
            $actwhere['c_id'] = $parr['joinaid'];
            $shopactinfo = M('Actjoin_moneylog')->where($actwhere)->find();
            if ($shopactinfo && ($shopactinfo['c_activitytype'] == 1 || $shopactinfo['c_activitytype'] == 6 ||
                    $shopactinfo['c_activitytype'] == 18 || $shopactinfo['c_activitytype'] == 19)
                && $countwhere['c_sign'] == 1) {  //红包
                $countwhere['c_type'] = 4;
                $moneycount['c_type'] = 4;
            }
        } else if ($parr['source'] == 18) {
            $countwhere['c_type'] = 4;
            $moneycount['c_type'] = 4;
            if ($countwhere['c_sign'] == 2) {
                $countwhere['c_type'] = 1;
                $moneycount['c_type'] = 1;
            }
        }

        if (empty($countwhere['c_type'])) {
            $countwhere['c_type'] = 1;
            $moneycount['c_type'] = 1;
        }

        //查询记录是否存在
        $countwhere['c_ucode'] = $parr['ucode'];
        $countwhere['c_datetime'] = date('Y-m-d');
        $countinfo = M('Users_moneydate')->where($countwhere)->find();
        $locks = IGD('Common','Redis')->Rediesgetucode('Users_moneydate-'.$moneycount['c_sign'].'-'.$moneycount['c_type'].'-'.$parr['ucode'].'-'.date('Y-m-d'));
        if (!$countinfo && !$locks) {
            IGD('Common', 'Redis')->RediesStoreSram('Users_moneydate-'.$moneycount['c_sign'].'-'.$moneycount['c_type'].'-'.$parr['ucode'].'-'.date('Y-m-d'),1,20);
            $result = M('Users_moneydate')->add($moneycount);
        } else {
            $moneycount['c_money'] = $moneycount['c_money'] + $countinfo['c_money'];
            $result = M('Users_moneydate')->where($countwhere)->save($moneycount);
        }

        if (!$result) {
            IGD('Common', 'Redis')->RediesStoreSram('Users_moneydate-'.$moneycount['c_sign'].'-'.$moneycount['c_type'].'-'.$parr['ucode'].'-'.date('Y-m-d'),null);
            return Message(1001, '每日金额记录失败');
        }

        return Message(0, '记录成功');
    }

    /**
     *  送出用户金额操作
     *  @param orderid,state,reason
     */
    function SendUserMoney($parr) {

        //查询记录
        $moneylogwhere['c_source'] = $parr['source'];
        $moneylogwhere['c_key'] = $parr['orderid'];
        $moneylogwhere['c_state'] = 0;
        $moneylog = M('Users_moneylog')->where($moneylogwhere)->select();
        if (count($moneylog) == 0) {
            return Message(1000, '金额记录不存在');
        }

        $state = $parr['state'];
        foreach ($moneylog as $key => $value) {

            $userwhere['c_ucode'] = $value['ucode'];
            $isagent = $value['c_isagent'];
            if ($isagent == 0) {
                $User = M('Users');
            } else {
                $User = M('Agent');
            }

            switch ($state) {
                case 1:
                    if ($value['c_money'] > 0) {
                        $result = $User->where($userwhere)->setInc('c_money', $value['c_money']);
                        if (!$result) {
                            return Message(1005, "修改用户金额失败");
                        }
                    }
                    $wherelog['c_id'] = $value['c_id'];
                    $saveinfo['c_state'] = 1;
                    M('Users_moneylog')->where($wherelog)->save($saveinfo);
                    if (!$result) {
                        return Message(1006, "修改记录状态失败");
                    }
                case 2:
                    if ($value['c_money'] < 0) {
                        // $userwhere['c_money'] = array('EGT', $value['c_money']);
                        $result = $User->where($userwhere)->setDec('c_money', $value['c_money']);
                        if (!$result) {
                            return Message(1005, "修改用户金额失败");
                        }
                    }

                    $wherelog['c_id'] = $value['c_id'];
                    $saveinfo['c_state'] = 2;
                    M('Users_moneylog')->where($wherelog)->save($saveinfo);
                    if (!$result) {
                        return Message(1006, "修改记录状态失败");
                    }
                    break;
            }
        }
        return Message(0, '操作成功');
    }

    /**
     *  获取平台返利系统设置
     *
     */
    function GetSystemSet() {
        $where['state'] = 1;
        $data = M('System_settings')->where($where)->order('c_addtime desc')->find();
        if (!$data) {
            return Message(1000, '数据不存在');
        }
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 计算邮费
     * @param acode,area,num
     */
    function CalculationFree($acode, $area, $num) {
        $where['c_ucode'] = $acode;
        $count = M('Users_free')->where($where)->count();
        if ($count > 0) {
            $where['c_city'] = array('like', '%' . $area . '%');
            $data = M('Users_free')->where($where)->find();
            if ($data) {
                if ($num > $data['c_num']) {
                    $single = bcdiv($data['c_addfreemoney'], $data['c_addnum'], 2);
                    $othernum = $num - $data['c_num'];
                    $otherfree = bcmul($single, $othernum, 2);
                    $free = bcadd($data['c_freemoney'], $otherfree, 2);
                } else {
                    $free = $data['c_freemoney'];
                }
            } else {
                $_where['c_ucode'] = $acode;
                $_where['c_isdefault'] = 1;
                $_data = M('Users_free')->where($_where)->find();
                if ($_data) {
                    if ($num > $_data['c_num']) {
                        $single = bcdiv($_data['c_addfreemoney'], $_data['c_addnum'], 2);
                        $othernum = $num - $_data['c_num'];
                        $otherfree = bcmul($single, $othernum, 2);
                        $free = bcadd($_data['c_freemoney'], $otherfree, 2);
                    } else {
                        $free = $_data['c_freemoney'];
                    }
                } else {
                    $free = 0;
                }
            }
        } else {
            $free = 0;
        }
        return $free;
    }

    /**
     *  查询平台品类
     */
    function GetCategory() {
        $data = M('Category')->select();
        if (count($data) == 0) {
            return MessageInfo(0, "查询成功", "");
        }
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  查询标签列表
     *  @param pageindex,pagesize
     */
    function GetLablist($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $list = M('Label')->limit($countPage, $pageSize)->order('c_sort desc')->select();
        if (!$list) {
            return MessageInfo(0, "查询成功", "");
        }

        $count = M('Label')->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //获取行业信息
    function GetIndustry() {

        $list = M('Industry')->select();

        if (count($list) == 0) {
            return MessageInfo(0, "查询成功", "");
        }
        return MessageInfo(0, '查询成功', $list);
    }

    function GetRegion($parentid) {
        $where['parent_id'] = $parentid;
        $field = 'region_id,parent_id,region_name,region_type';
        $order = 'region_id asc';
        $list = M('region')->where($where)->field($field)->order($order)->select();
        return $list;
    }

    function GetRegion1($parentid) {
        $where['region_type'] = $parentid;
        $field = 'region_id,parent_id,region_name,region_type';
        $order = 'region_id asc';
        $list = M('region')->where($where)->field($field)->order($order)->select();
        return $list;
    }

    function GetAllRegion() {

        $parentid = 1;
        $province = $this->GetRegion($parentid);
        foreach ($province as $key => $value) {

            //查询市
            $city = $this->GetRegion($value['region_id']);
            foreach ($city as $key1 => $value1) {
                $region = $this->GetRegion($value1['region_id']);
                $city[$key1]['region'] = $region;
            }
            $province[$key]['city'] = $city;
        }

        return $province;
    }

    //获取banner列表
    function get_banner($parr) {
        $Banner = M('Banner');
        $_where = " c_state=0 ";

        $source = $parr['source'];
        $tag = $parr['tag'];

        $_where.=" and c_source=" . $source;

        if (!empty($tag) && $tag==2) {
            $field = "c_id,c_title,c_weburl,c_tag,c_tagvalue,c_sort,c_img";
        }else{
            $field = "c_id,c_title,c_tag,c_weburl,c_sort,c_img";
        }

        $data = $Banner->field($field)->where($_where)->order('c_sort desc,c_id desc')->select();

        foreach ($data as $key => $value) {
            $data[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
        }
        return MessageInfo(0,'查询成功',$data);
    }


    /**
     * 用户余额支付订单模块
     * @param [type] $orderinfo [description]
     * @param [type] $money     [description]
     */
    public function BalancePay($orderinfo,$money,$bid,$bmoney) {
        $ucode = $orderinfo['c_ucode'];
        $acode = $orderinfo['c_acode'];
        $orderid = $orderinfo['c_orderid'];
        $totprice = $orderinfo['c_total_price'];
        $free = $orderinfo['c_free'];
        $delivery = $orderinfo['c_delivery'];
        $isagent = $orderinfo['c_isagent'];

        if ($orderinfo['c_actual_price'] > 0) {
            return Message(0,'已经抵扣');
        }

        //查询开户商户可用金额
        $mparr['ucode'] = $ucode;
        $result = IGD('Balance','User')->GetSyncYesMoney($mparr);
        if ($result['code'] == 0 && $result['data']['c_ysdrmoney'] < $money && $money > 0) {
            return Message(3001,'可抵扣金额不足');
        }

        $db = M('');
        $db->startTrans();

        //转入卡劵抵扣支付
        if (!empty($bid) && !empty($bmoney) && empty($orderinfo['c_money'])) {
            $result = $this->CouponPay($orderinfo,$bid,$bmoney);
            if ($result['code'] != 10086 && $result['code'] != 0) {
                $db->rollback();
                return $result;
            }
            if ($result['code'] == 10086) {
                $db->commit();
                return MessageInfo(10086, "余额支付订单完成",$orderid);
            }

            if ($money <= 0) {
                $db->commit();
                return Message(0,'卡劵抵扣支付成功');
            }

            $actual_price = $result['data'];
        } else {
            //订单已支付金额
            $actual_price = $orderinfo['c_actual_price'];
        }

        if ($delivery == 2) {
            $free = 0;
        }

        $balance = 0;
        $tempcount = $totprice + $free;

        $paystatu = 0;
        //计算订单金额
        if($actual_price == 0){
            if ($money >= $tempcount) {
                $paystatu = 1;
                $balance = $tempcount;
                $actprice = $tempcount;
            } else {
                $balance = $money;
                $actprice = $money;
            }
        }else{
            $surplus = bcsub($tempcount,bcadd($actual_price,$money,2),2);
            if($surplus == 0){
                $paystatu = 1;
                $balance = $money;
                $actprice = $tempcount;
            } elseif ($surplus > 0){
                $balance = $money;
                $actprice = bcadd($actual_price,$money,2);
            } else {
                $paystatu = 1;
                $balance = bcadd($money,$surplus,2);
                $actprice = bcsub($tempcount,$actual_price,2);
            }
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        if ($balance > 0) {
            $aorderinfo['c_actual_price'] = $actprice;
            if (substr($orderid,0,1) == 's') {
                $parr['source'] = 4;
            } else {
                $parr['source'] = 1;
            }

            $parr['showtext'] = '普通商城购物';
            if(substr($orderid,0,1) == 'l') {
                $parr['showtext'] = '代理商城购物';
            }

            //用户功勋操作
            $parr['ucode'] = $ucode;
            $parr['money'] = $balance;
            $parr['key'] = $orderid;
            $parr['desc'] = "余额支付";
            $parr['state'] = 1;
            $parr['type'] = 0;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/gou1.png';
            $result = $this->OptionMoney($parr);

            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
            //用户支付记录操作
            $result = IGD('Order', 'Order')->paylog($orderid, 4, $balance, "");
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
            //给用户发信息
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您提交的订单号：' . $orderid . '，抵扣余额￥' . $balance . '成功';
            $msgdata['tag'] = 2;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/index/budget';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/index/budget';
            $Msgcentre->CreateMessege($msgdata);
        }

        if($paystatu == 1){
            $aorderinfo['c_pay_state'] = 1;
            $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

            //计算详情
            //获取订单详情信息
            $isdaili = 0;
            if (substr($orderid,0,1) == 's') {
                $orderdb = IGD('Supplyorder', 'Agorder');
                $detaildb = M('Supplier_order_details');
            } else if(substr($orderid,0,1) == 't') {
                $orderdb = IGD('Storeorder', 'Order');
                $detaildb = M('Order_details');
            } else if(substr($orderid,0,1) == 'l') {
                $orderdb = IGD('Agorder', 'Order');
                $detaildb = M('Order_details');
                $isdaili = 1;
            } else {
                $orderdb = IGD('Order', 'Order');
                $detaildb = M('Order_details');
            }

            //不是代理商城的产品判断返利
            if ($isdaili == 0) {
                $wheredetail['c_orderid'] = $orderid;
                $detailslist = $detaildb->where($wheredetail)->select();
                $result = $orderdb->CheckRebate($detailslist, $ucode, $acode,$orderinfo['c_activity_id']);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return $result;
                }

                $shopprofit = $result['data'];
                /*****   新增银盛支付代付代扣   ******/ 
                //当商家到账的利润大于支付利润采用代扣
                //当商家到账的利润小于支付利润采用代付
                if ($shopprofit > 0) {
                    $arr['sign'] = 1; // 1 代付 2 代扣
                    $opmoney = $shopprofit;
                    $arr['type'] = 2; // 1  实时结算  2 按日结算  3 按月结算
                    $arr['ucode'] = $orderinfo['c_acode']; // 分润人
                    $arr['scode'] = $orderinfo['c_acode'];
                    $arr['bcode'] = $orderinfo['c_ucode'];
                    $arr['orderid'] = CreateOrder('f');
                    $arr['key'] = $orderinfo['c_orderid'];
                    $arr['desc'] = '平台余额支付订单交易资金操作';
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
                
            } else {   //是代理商品,判断是否可代理
                $agparr['orderid'] = $orderid;
                $result = $orderdb->AgencyGoods($agparr);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return $result;
                }
            }

            //代理商品转交订单
            if ($orderinfo['c_isagent'] == 1) {
                $result = IGD('Supplier','Agorder')->CreatAgentOrder($orderinfo);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return $result;
                }
            }

            //给用户发送相关消息
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

            $msgdata['ucode'] = $acode;
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

        $aorderinfo['c_pay_rule'] = 4;
        $orderwhere['c_orderid'] = $orderid;
        $result = M('Order')->where($orderwhere)->save($aorderinfo);
        if (!$result) {
            $db->rollback();
            return Message(3005, "余额支付订单失败");
        }

        $db->commit();

        if($paystatu == 1){
            return MessageInfo(10086, "余额支付订单完成",$orderid);
        }

        return Message(0, "余额支付订单成功");
    }

    /**
     * 卡劵抵扣支付
     * @param string $value [description]
     */
    function CouponPay($orderinfo,$bid,$bmoney)
    {
        $ucode = $orderinfo['c_ucode'];
        $acode = $orderinfo['c_acode'];
        $orderid = $orderinfo['c_orderid'];
        $totprice = $orderinfo['c_total_price'];
        $free = $orderinfo['c_free'];

        //订单已支付金额
        $actual_price = $orderinfo['c_actual_price'];

        $tempcount = $totprice + $free;
        $paystatu = 0;
        $balance = 0;
        if($actual_price == 0){
            if ($bmoney >= $tempcount) {
                $paystatu = 1;
                $balance = $tempcount;
                $actprice = $tempcount;
            } else {
                $balance = $bmoney;
                $actprice = $bmoney;
            }
        }else{
            $surplus = bcsub($tempcount,bcadd($actual_price,$bmoney,2),2);
            if($surplus == 0){
                $paystatu = 1;
                $balance = $bmoney;
                $actprice = $tempcount;
            } elseif ($surplus > 0){
                $balance = $bmoney;
                $actprice = bcadd($actual_price,$bmoney,2);
            } else {
                $paystatu = 1;
                $balance = bcadd($bmoney,$surplus,2);
                $actprice = bcsub($tempcount,$actual_price,2);
            }
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        $aorderinfo['c_actual_price'] = $actprice;
        if($paystatu == 1){
            $aorderinfo['c_pay_state'] = 1;
            $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

            //获取订单详情信息
            $isdaili = 0;
            if (substr($orderid,0,1) == 's') {
                $orderdb = IGD('Supplyorder', 'Agorder');
                $detaildb = M('Supplier_order_details');
            } else if(substr($orderid,0,1) == 't') {
                $orderdb = IGD('Storeorder', 'Order');
                $detaildb = M('Order_details');
            } else if(substr($orderid,0,1) == 'l') {
                $orderdb = IGD('Agorder', 'Order');
                $detaildb = M('Order_details');
                $isdaili = 1;
            } else {
                $orderdb = IGD('Order', 'Order');
                $detaildb = M('Order_details');
            }

            //不是代理商城的产品判断返利
            if ($isdaili == 0) {
                $wheredetail['c_orderid'] = $orderid;
                $detailslist = $detaildb->where($wheredetail)->select();
                $result = $orderdb->CheckRebate($detailslist, $ucode, $acode,$orderinfo['c_activity_id']);
                if ($result['code'] != 0) {
                    return $result;
                }
            } else {   //是代理商品,判断是否可代理
                $agparr['orderid'] = $orderid;
                $result = $orderdb->AgencyGoods($agparr);
                if ($result['code'] != 0) {
                    return $result;
                }
            }

            //代理商品转交订单
            if ($orderinfo['c_isagent'] == 1) {
                $result = IGD('Supplier','Agorder')->CreatAgentOrder($orderinfo);
                if ($result['code'] != 0) {
                    return $result;
                }
            }

            //给用户发送相关消息
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

            $msgdata['ucode'] = $acode;
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

        $aorderinfo['c_pay_rule'] = 5;
        $aorderinfo['c_bid'] = $bid;
        $aorderinfo['c_bmoney'] = $balance;
        $orderwhere['c_orderid'] = $orderid;
        $result = M('Order')->where($orderwhere)->save($aorderinfo);
        if (!$result) {
            return Message(3005, "卡劵抵扣订单金额失败");
        }

        //改变卡劵使用状态
        $param['ucode'] = $ucode;
        $param['bid'] = $bid;
        $param['desc'] = '线上订单抵扣';
        $param['used_state'] = 1;
        $result = IGD('Coupon','Newact')->UseCoupon($param);
        if ($result['code'] != 0) {
            return Message(3005, "使用卡劵失败");
        }

        if($paystatu == 1){
            return MessageInfo(10086, "卡劵抵扣订单支付完成",$orderid);
        }

        return MessageInfo(0,'卡劵抵扣订单金额成功',$actprice);
    }

}