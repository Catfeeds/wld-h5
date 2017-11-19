<?php
/**
 * 创建订单后给 不同用户分润
 */

class SplittingOrder{

    /**
     *   创建分润记录
     *   @param ucode,scode,bcode,orderid,total_money,money,source,desc,joinaid，money_log_id
     *   tag(1 分润人ucode 2 商家ucode 3 买家ucode 4 订单号 5 订单总金额 6 分润金额 7 来源 8 描述 9 参与的活动id 10 关联金额记录id)
     *
     */
    public function CreateRecord($parr)
    {
        $source = $parr['source'];
        if ($source == 1 || $source == 4 || $source == 5 || $source == 15 || $source == 9 || $source == 12) { 
            //查询加盟连锁信息
            $unionresult = IGD('Chain','Store')->GetUnionInfo($parr);
            $unioninfo = $unionresult['data'];
            if (!$unioninfo['c_pid']) {
                $unioninfo['c_pid'] = $unioninfo['c_id'];
            }
            if (!$unioninfo['shopcode']) {
                $unioninfo['shopcode'] = $unioninfo['c_ucode'];
            }

            if ($unioninfo['c_type'] == 1) {   //连锁店订单收入进入总店
                $parr['ucode'] = $unioninfo['shopcode'];
            }
        }

        $result = IGD('Ysepay','Scanpay')->GetYsedata($parr);
        if ($result['code'] != 0) {
            return Message(0,'创建成功');
        }
        $data['c_sign'] =$parr['sign']; //1 代付 2 代扣
        $data['c_type'] =$parr['type']; // 1 实时结算 2 按日结算  3 按月结算
        $data['c_ucode'] = $parr['ucode'];
        $data['c_scode'] = $parr['scode'];
        $data['c_bcode'] = $parr['bcode'];
        $data['c_orderid'] = $parr['orderid'];
        $data['c_total_money'] = $parr['total_money'];
        $data['c_money'] = $parr['money'];
        $data['c_source'] = $parr['source'];
        $data['c_key'] =$parr['key'];
        $data['c_desc'] = $parr['desc'];
        $data['c_joinaid'] = $parr['joinaid'];
        $data['c_money_log_id'] = $parr['money_log_id'];
        $data['c_addtime'] = date('Y-m-d H:i:s');
        if ($parr['status'] == 1) {
            $data['c_status'] = 1;
        }

        if (!empty($parr['settled'])) {
            $data['c_settled'] = $parr['settled'];
        } else {
            $data['c_settled'] = 1;
        }

        if (!empty($parr['settledtime'])) {
            if ($data['c_settledtime'] != '1') {
                $data['c_settledtime'] = $parr['settledtime'];
            }
        } else {
            $data['c_settledtime'] = gdtime();
        }

        $result = M('Users_order_splitting')->add($data);  //添加记录
        if (!$result) {
            return Message(1002,'创建分润失败');
        }
        return Message(0,'创建成功');
    }

    /**
     *   创建分润记录 并调用银盛代付实时分发
     *   @param ucode,scode,bcode,orderid,total_money,money,source,desc,joinaid，money_log_id
     *   tag(1 分润人ucode 2 商家ucode 3 买家ucode 4 订单号 5 订单总金额 6 分润金额 7 来源 8 描述 9 参与的活动id 10 关联金额记录id)
     *
     */
    public function CreateRecordDo($parr)
    {
        $source = $parr['source'];
        if ($source == 1 || $source == 4 || $source == 5 || $source == 15 || $source == 9 || $source == 12) { 
            //查询加盟连锁信息
            $cwh['c_ucode'] = $parr['ucode'];
            $unioninfo = M('A_federation')->where($cwh)->find();
            if ($unioninfo['c_type'] == 1) {   //连锁店订单收入进入总店
                $parr['ucode'] = $unioninfo['shopcode'];
            }
        }

        $result = IGD('Ysepay','Scanpay')->GetYsedata($parr);
        if ($result['code'] != 0) {
            return Message(0,'创建成功');
        }
        $data['c_sign'] =$parr['sign']; //1 代付 2 代扣
        $data['c_type'] =$parr['type']; // 1 实时结算 2 按日结算  3 按月结算
        $data['c_ucode'] = $parr['ucode'];
        $data['c_scode'] = $parr['scode'];
        $data['c_bcode'] = $parr['bcode'];
        $data['c_orderid'] = $parr['orderid'];
        $data['c_total_money'] = $parr['total_money'];
        $data['c_money'] = $parr['money'];
        $data['c_source'] = $parr['source'];
        $data['c_desc'] = $parr['desc'];
        $data['c_joinaid'] = $parr['joinaid'];
        $data['c_money_log_id'] = $parr['money_log_id'];
        $data['c_addtime'] = date('Y-m-d H:i:s');
        if ($parr['status'] == 1) {
            $data['c_status'] = 1;
        }

        if (!empty($parr['settled'])) {
            $data['c_settled'] = $parr['settled'];
        } else {
            $data['c_settled'] = 1;
        }

        if (!empty($parr['settledtime'])) {
            $data['c_settledtime'] = $parr['settledtime'];
        } else {
            $data['c_settledtime'] = gdtime();
        }

        $result = M('Users_order_splitting')->add($data);  //添加记录
        if ($result) {
            // 调用 代付接口 通过银盛商户给商家账号 代付款
            $pay =IGD('Yspay','Yspay');
            $parr['notify_url'] = GetHost() . "/api.php/Pay/Yspay/respond_notify";
            $parr['out_trade_no'] = $pay->datetime2string(date('Y-m-d H:i:s') . rand(100, 999));
            $parr['total_amount'] = "1.00";
            $parr['subject'] = "测试";
            $parr['bank_name'] = "建设银行华兴支行";
            $parr['bank_city'] = "长沙市";
            $parr['bank_account_no'] = "6217002920123595952";
            $parr['bank_account_name'] = "童向";
            $parr['bank_account_type'] = "personal"; //corporate :对公账户; personal :对私账户
            $parr['bank_card_type'] = "debit"; //  debit:借记卡;credit:信用卡 unit:单位结算卡
            $data = $pay->get_dfjj($parr);
            $result = $pay->curl_https_df($data);
            if($result['ysepay_df_single_quick_accept_response']['code']=='10000'){
                return Message(0,'分润成功');
            }
        }else{
            return Message(1001,'创建失败');
        }

    }

    /*****  代付金额  ******/

    //定时器按月结算代付金额
    public function SettlementByMonth()
    {
        $htime = date('H');
        if ($htime > 12) {  //每天下午12点开始清算
            Vendor('Ysepay.Yse_pay');
            $pay = new \Yse_pay();

            $top = date('Y-m',strtotime('-1 months'));
            $where['c_type'] = 3;
            $where['c_sign'] = 1;
            $where['c_status'] = 0;
            // $where['c_addtime'] = array('EGT',$top.'-01 00:00:00');
            // $where['c_addtime'] = array('LT',date('Y-m-d 00:00:00',strtotime('+1 months',strtotime($top.'-01 00:00:00'))));
            $bentimes = $top.'-01 00:00:00';
            $endtimes = date('Y-m-d 00:00:00',strtotime('+1 months',strtotime($top.'-01 00:00:00')));
            $where['c_addtime'] = array('BETWEEN',array($bentimes,$endtimes));
            $field = '*,sum(c_money) as tpmoney';
            $data = M('Users_order_splitting')->where($where)->group('c_ucode')->field($field)->order('c_id desc')->limit(100)->select();
            $n = 0;$m = 0;
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    //查询商家开户信息
                    $parra['ucode'] = $value['c_ucode'];
                    $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
                    if ($result['code'] == 0) {
                        $yseinfo = $result['data'];

                        $db = M('');
                        $db->startTrans();
                        if (empty($value['c_tcode'])) {
                            //补充统一订单号
                            $tcode = CreateOrder('uf');
                            $where['c_ucode'] = $value['c_ucode'];
                            $save['c_tcode'] = $tcode;
                            $result = M('Users_order_splitting')->where($where)->save($save);
                        } else {
                            $tcode = $value['c_tcode'];
                        }

                        if ($result) {   //操作成功开始代付

                            //代付虚拟账户
                            $parr['notify_url'] = GetHost(1).'/index.php/Order/Backorder/notify_Split';
                            $parr['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
                            $parr['out_trade_no'] = $tcode;
                            $parr['total_amount'] = $value['tpmoney'];
                            $parr['subject'] = '代理商跨界金额月结';
                            $parr['merchant_usercode']='wld17375717292';        //扣款方
                            $parr['payee_user_code'] = $yseinfo['c_username'];  //收款方
                            $parr['payee_cust_name'] = $yseinfo['c_person'];
                            $result = $pay->curl_inner_df($pay->get_inner_df($parr));
                            // $result = objarray_to_array(json_decode($result));
                            $trade_status = $result['ysepay_df_single_quick_inner_accept_response']['trade_status'];
                            if ($trade_status == 'TRADE_ACCEPT_SUCCESS' || $trade_status == 'TRADE_SUCCESS') {   //交易受理成功
                                $w['c_tcode'] = $tcode;
                                $save_data['c_status'] = 1;
                                $save_data['c_updatetime'] = gdtime();
                                $result = M('Users_order_splitting')->where($w)->save($save_data);
                                $m++;
                            }
                        }

                        $db->commit();
                        $n++;
                    }
                }
            }
        }

        return MessageInfo(0,'操作成功','总计：'.$n.'，受理成功：'.$m);
    }
    
    //定时器按天结算代付金额
    public function SettlementByDate()
    {
        $htime = date('H');
        if ($htime > 12) {  //每天下午12点开始清算
            Vendor('Ysepay.Yse_pay');
            $pay = new \Yse_pay();
            $top = date('Y-m-d',strtotime('-1 day'));
            $where['c_type'] = 2;
            $where['c_sign'] = 1;
            $where['c_status'] = 0;
            // $where['c_addtime'] = array('EGT',$top.' 00:00:00');
            // $where['c_addtime'] = array('ELT',$top.' 23:59:59');
            $where['c_addtime'] = array('BETWEEN',array($top.' 00:00:00',$top.' 23:59:59'));
            $field = '*,sum(c_money) as tpmoney';
            $data = M('Users_order_splitting')->where($where)->group('c_ucode')->field($field)->order('c_id desc')->limit(100)->select();
            $n = 0;$m = 0;
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    //查询商家开户信息
                    $parra['ucode'] = $value['c_ucode'];
                    $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
                    if ($result['code'] == 0) {
                        $yseinfo = $result['data'];

                        $db = M('');
                        $db->startTrans();
                        if (empty($value['c_tcode'])) {
                            //补充统一订单号
                            $tcode = CreateOrder('uf');
                            $where['c_ucode'] = $value['c_ucode'];
                            $save['c_tcode'] = $tcode;
                            $result = M('Users_order_splitting')->where($where)->save($save);
                        } else {
                            $tcode = $value['c_tcode'];
                        }

                        if ($result) {   //操作成功开始代付

                            //代付虚拟账户
                            $parr['notify_url'] = GetHost(1).'/index.php/Order/Backorder/notify_Split';
                            $parr['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
                            $parr['out_trade_no'] = $tcode;
                            $parr['total_amount'] = $value['tpmoney'];
                            $parr['subject'] = '商家按天结算金额';
                            $parr['merchant_usercode']='wld17375717292';        //扣款方
                            $parr['payee_user_code'] = $yseinfo['c_username'];  //收款方
                            $parr['payee_cust_name'] = $yseinfo['c_person'];
                            $result = $pay->curl_inner_df($pay->get_inner_df($parr));
                            $trade_status = $result['ysepay_df_single_quick_inner_accept_response']['trade_status'];
                            if ($trade_status == 'TRADE_ACCEPT_SUCCESS' || $trade_status == 'TRADE_SUCCESS') {   //交易受理成功
                                $w['c_tcode'] = $tcode;
                                $save_data['c_status'] = 1;
                                $save_data['c_updatetime'] = gdtime();
                                $result = M('Users_order_splitting')->where($w)->save($save_data);
                                $m++;
                            }
                        }

                        $db->commit();
                        $n++;
                    }
                }
            }
        }

        return MessageInfo(0,'操作成功','总计：'.$n.'，受理成功：'.$m);
    }

    //定时器按秒结算代付金额
    public function SettlementBySecond()
    {
        Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();

        $where['c_type'] = 1;
        $where['c_sign'] = 1;
        $where['c_status'] = 0;
        $field = '*,c_money as tpmoney';
        $data = M('Users_order_splitting')->where($where)->field($field)->order('c_id desc')->limit(100)->select();
        $n = 0;$m = 0;
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                //查询商家开户信息
                $parra['ucode'] = $value['c_ucode'];
                $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
                if ($result['code'] == 0) {
                    $yseinfo = $result['data'];

                    $db = M('');
                    $db->startTrans();
                    if (empty($value['c_tcode'])) {
                        //补充统一订单号
                        $tcode = CreateOrder('uf');
                        $where['c_ucode'] = $value['c_ucode'];
                        $where['c_id'] = $value['c_id'];
                        $save['c_tcode'] = $tcode;
                        $result = M('Users_order_splitting')->where($where)->save($save);
                    } else {
                        $tcode = $value['c_tcode'];
                    }

                    if ($result) {   //操作成功开始代付

                        //代付虚拟账户
                        $parr['notify_url'] = GetHost(1).'/index.php/Order/Backorder/notify_Split';
                        $parr['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
                        $parr['out_trade_no'] = $tcode;
                        $parr['total_amount'] = $value['tpmoney'];
                        $parr['subject'] = $value['c_desc'];
                        $parr['merchant_usercode']='wld17375717292';        //扣款方
                        $parr['payee_user_code'] = $yseinfo['c_username'];  //收款方
                        $parr['payee_cust_name'] = $yseinfo['c_person'];
                        $result = $pay->curl_inner_df($pay->get_inner_df($parr));
                        $trade_status = $result['ysepay_df_single_quick_inner_accept_response']['trade_status'];
                        if ($trade_status == 'TRADE_ACCEPT_SUCCESS' || $trade_status == 'TRADE_SUCCESS') {   //交易受理成功
                            $w['c_tcode'] = $tcode;
                            $save_data['c_status'] = 1;
                            $save_data['c_updatetime'] = gdtime();
                            $result = M('Users_order_splitting')->where($w)->save($save_data);
                            $m++;
                        }
                    }

                    $db->commit();
                    $n++;
                }
            }
        }

        return MessageInfo(0,'操作成功','总计：'.$n.'，受理成功：'.$m);
    }

    /*****  代扣金额  ******/

    //定时器按月结算代扣金额
    public function KouSettlementByMonth()
    {
        $htime = date('H');
        if ($htime > 12) {  //每天下午12点开始清算
            Vendor('Ysepay.Yse_pay');
            $pay = new \Yse_pay();

            $top = date('Y-m',strtotime('-1 months'));
            $where['c_type'] = 3;
            $where['c_sign'] = 2;
            $where['c_status'] = 0;
            // $where['c_addtime'] = array('EGT',$top.'-01 00:00:00');
            // $where['c_addtime'] = array('LT',date('Y-m-d 00:00:00',strtotime('+1 months',strtotime($top.'-01 00:00:00'))));
            $bentimes = $top.'-01 00:00:00';
            $endtimes = date('Y-m-d 00:00:00',strtotime('+1 months',strtotime($top.'-01 00:00:00')));
            $where['c_addtime'] = array('BETWEEN',array($bentimes,$endtimes));
            $field = '*,sum(c_money) as tpmoney';
            $data = M('Users_order_splitting')->where($where)->group('c_ucode')->field($field)->order('c_id desc')->limit(100)->select();
            $n = 0;$m = 0;
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    //查询商家开户信息
                    $parra['ucode'] = $value['c_ucode'];
                    $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
                    if ($result['code'] == 0) {
                        $yseinfo = $result['data'];

                        $db = M('');
                        $db->startTrans();
                        if (empty($value['c_tcode'])) {
                            //补充统一订单号
                            $tcode = CreateOrder('uf');
                            $where['c_ucode'] = $value['c_ucode'];
                            $save['c_tcode'] = $tcode;
                            $result = M('Users_order_splitting')->where($where)->save($save);
                        } else {
                            $tcode = $value['c_tcode'];
                        }

                        if ($result) {   //操作成功开始代扣

                            //代扣虚拟账户
                            $parr['notify_url'] = GetHost(1).'/index.php/Order/Backorder/kou_notify_Split';
                            $parr['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
                            $parr['out_trade_no'] = $tcode;
                            $parr['total_amount'] = $value['tpmoney'];
                            $parr['subject'] = '按月代扣相关金额';
                            $parr['merchant_usercode']= $yseinfo['c_username'];        //扣款方
                            $parr['payee_user_code'] = 'wld17375717292';        //收款方
                            $parr['payee_cust_name'] = '长沙微领地网络科技有限公司';
                            $result = $pay->curl_inner_df($pay->get_inner_df($parr));
                            $trade_status = $result['ysepay_df_single_quick_inner_accept_response']['trade_status'];
                            if ($trade_status == 'TRADE_ACCEPT_SUCCESS' || $trade_status == 'TRADE_SUCCESS') {   //交易受理成功
                                $w['c_tcode'] = $tcode;
                                $save_data['c_status'] = 1;
                                $save_data['c_updatetime'] = gdtime();
                                $result = M('Users_order_splitting')->where($w)->save($save_data);
                                $m++;
                            }
                        }

                        $db->commit();
                        $n++;
                    }
                }
            }
        }

        return MessageInfo(0,'操作成功','总计：'.$n.'，受理成功：'.$m);
    }
    
    //定时器按天结算代扣金额
    public function KouSettlementByDate()
    {
       $htime = date('H');
        if ($htime > 12) {  //每天下午12点开始清算
            Vendor('Ysepay.Yse_pay');
            $pay = new \Yse_pay();
            $top = date('Y-m-d',strtotime('-1 day'));
            $where['c_type'] = 2;
            $where['c_sign'] = 2;
            $where['c_status'] = 0;
            // $where['c_addtime'] = array('EGT',$top.' 00:00:00');
            // $where['c_addtime'] = array('ELT',$top.' 23:59:59');
            $where['c_addtime'] = array('BETWEEN',array($top.' 00:00:00',$top.' 23:59:59'));
            $field = '*,sum(c_money) as tpmoney';
            $data = M('Users_order_splitting')->where($where)->group('c_ucode')->field($field)->order('c_id desc')->limit(100)->select();
            $n = 0;$m = 0;
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    //查询商家开户信息
                    $parra['ucode'] = $value['c_ucode'];
                    $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
                    if ($result['code'] == 0) {
                        $yseinfo = $result['data'];

                        $db = M('');
                        $db->startTrans();
                        if (empty($value['c_tcode'])) {
                            //补充统一订单号
                            $tcode = CreateOrder('uf');
                            $where['c_ucode'] = $value['c_ucode'];
                            $save['c_tcode'] = $tcode;
                            $result = M('Users_order_splitting')->where($where)->save($save);
                        } else {
                            $tcode = $value['c_tcode'];
                        }

                        if ($result) {   //操作成功开始代付

                            //代付虚拟账户
                            $parr['notify_url'] = GetHost(1).'/index.php/Order/Backorder/kou_notify_Split';
                            $parr['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
                            $parr['out_trade_no'] = $tcode;
                            $parr['total_amount'] = $value['tpmoney'];
                            $parr['subject'] = '商家按天代扣金额';
                            $parr['merchant_usercode']= $yseinfo['c_username'];        //扣款方
                            $parr['payee_user_code'] = 'wld17375717292';        //收款方
                            $parr['payee_cust_name'] = '长沙微领地网络科技有限公司';
                            $result = $pay->curl_inner_df($pay->get_inner_df($parr));
                            $trade_status = $result['ysepay_df_single_quick_inner_accept_response']['trade_status'];
                            if ($trade_status == 'TRADE_ACCEPT_SUCCESS' || $trade_status == 'TRADE_SUCCESS') {   //交易受理成功
                                $w['c_tcode'] = $tcode;
                                $save_data['c_status'] = 1;
                                $save_data['c_updatetime'] = gdtime();
                                $result = M('Users_order_splitting')->where($w)->save($save_data);
                                $m++;
                            }
                        }

                        $db->commit();
                        $n++;
                    }
                }
            }
        }

        return MessageInfo(0,'操作成功','总计：'.$n.'，受理成功：'.$m);
    }

    //定时器按秒结算代扣金额
    public function KouSettlementBySecond()
    {
        Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();

        $where['c_type'] = 1;
        $where['c_sign'] = 2;
        $where['c_status'] = 0;
        $field = '*,c_money as tpmoney';
        $data = M('Users_order_splitting')->where($where)->field($field)->order('c_id desc')->limit(100)->select();
        $n = 0;$m = 0;
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                //查询商家开户信息
                $parra['ucode'] = $value['c_ucode'];
                $result = IGD('Ysepay','Scanpay')->GetYsedata($parra);
                if ($result['code'] == 0) {
                    $yseinfo = $result['data'];

                    $db = M('');
                    $db->startTrans();
                    if (empty($value['c_tcode'])) {
                        //补充统一订单号
                        $tcode = CreateOrder('uf');
                        $where['c_ucode'] = $value['c_ucode'];
                        $where['c_id'] = $value['c_id'];
                        $save['c_tcode'] = $tcode;
                        $result = M('Users_order_splitting')->where($where)->save($save);
                    } else {
                        $tcode = $value['c_tcode'];
                    }

                    if ($result) {   //操作成功开始代付

                        //代付虚拟账户
                        $parr['notify_url'] = GetHost(1).'/index.php/Order/Backorder/kou_notify_Split';
                        $parr['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
                        $parr['out_trade_no'] = $tcode;
                        $parr['total_amount'] = $value['tpmoney'];
                        $parr['subject'] = $value['c_desc'];
                        $parr['merchant_usercode']= $yseinfo['c_username'];        //扣款方
                        $parr['payee_user_code'] = 'wld17375717292';        //收款方
                        $parr['payee_cust_name'] = '长沙微领地网络科技有限公司';
                        $result = $pay->curl_inner_df($pay->get_inner_df($parr));
                        $trade_status = $result['ysepay_df_single_quick_inner_accept_response']['trade_status'];
                        if ($trade_status == 'TRADE_ACCEPT_SUCCESS' || $trade_status == 'TRADE_SUCCESS') {   //交易受理成功
                            $w['c_tcode'] = $tcode;
                            $save_data['c_status'] = 1;
                            $save_data['c_updatetime'] = gdtime();
                            $result = M('Users_order_splitting')->where($w)->save($save_data);
                            $m++;
                        }
                    }

                    $db->commit();
                    $n++;
                }
            }
        }

        return MessageInfo(0,'操作成功','总计：'.$n.'，受理成功：'.$m);
    }
}