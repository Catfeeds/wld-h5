<?php

/**
 * 结算中心
 *
 */
class SettlementUser {

    //查询结算中心余额数据
    /*
      ucode
     *      */

    public function Summary($parr) {

        $ucode = $parr['ucode'];
        $User = IGD('Balance', 'User');
        $balance = $User->GetBalance($ucode);

        $transaction = $this->Gettransaction($ucode);
        $drawing = $this->Getdrawing($ucode);
        $stmoney = $this->GetYstmoney($ucode);
        $datainfo['balance'] = $balance;
        $datainfo['transaction'] = $transaction;
        $datainfo['drawing'] = $drawing;

        $datainfo['stmoney'] = $stmoney;
        $datainfo['drawmoney'] = bcsub($balance,$stmoney,2);
        return MessageInfo(0, "查询成功", $datainfo);
    }

    //查询银盛结算中的金额
    public function GetYstmoney($ucode){
        if (!empty($ucode)) {
            $where['c_ucode'] = $ucode;
            $stmoney = M('Users_yesmoney')->where($where)->sum('c_stmoney');
            if (empty($stmoney)) {
                $stmoney=0;
            }
            return $stmoney;
        }
        return Message(1001, "缺少参数");
    }

    //查看带结算记录
    public function GetSettled($ucode){
        if (!empty($ucode)) {
            $where['c_ucode'] = $ucode;
            $where['c_settled'] = 2;
            $result = M('Users_order_splitting')->where($where)->select();
            if (!$result) {
                return Message(1001, "查询失败");
            }
            return Message(0, "查询成功",$result);
        }
        return Message(1001, "缺少参数");

    }


    //查看提现记录
    /*
      pageindex,pagesize,ucode
     *      */
    public function GetdrawingList($parr) {

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_ucode'] = $parr['ucode'];

        $field = 'c_money,c_addtime,c_updatetime,c_state,c_remarks';
        $order = 'c_id desc';
        $list = M('Users_drawing')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('Users_drawing')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //查询收支记录
    /*
      pageindex,pagesize,type,ucode
     *      */
    public function GetMoneyLog($parr) {

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if ($parr['type'] == 1) {
            $where['c_money'] = array('GT', 0);
        } else if ($parr['type'] == 2) {
            $where['c_money'] = array('LT', 0);
        }
       
        //source 1扫码，2线上订单，3红包，4提现，5跨界，6其他
        switch ($parr['source']) {
            case '1':
                $where['c_source'] = array('in','9,12');
                break;
            case '2':
                $where['c_source'] = array('in','1,5,14,15,16');
                break;
            case '3':
                $where['c_source'] = array('in','7,8,18');
                break;
            case '4':
                $where['c_source'] = array('in','6');
                break;
            case '5':
                $where['c_source'] = array('in','5,12');
                break;
            case '6':
                $where['c_source'] = array('in','2');
                break;
            default:
                # code...
                break;
        }
            
        

        if (!empty($parr['c_time'])) {
            $begintime = $parr['c_time'].'-01';
            $endtime = date('Y-m-d',strtotime('+'.($i+1).' months',strtotime($begintime)));
            $where[] = "c_addtime between '".$begintime."' and '".$endtime."'";
        }
        
        $where['c_ucode'] = $parr['ucode'];
        $field = 'c_money,c_desc,c_addtime,c_state,c_source,c_key,c_joinaid,c_showimg,c_showtext,c_id';
        $order = 'c_addtime desc';
        $list = M('Users_moneylog')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();
        foreach ($list as $key => $value) {
            $datetime = strtotime($value['c_addtime']);
            $list[$key]['ifdate'] = strtotime(date('Y-m-d 00:00:00', $datetime));
            $list[$key]['showyears'] = date('Y', $datetime);
            $list[$key]['showmoths'] = date('m', $datetime);
            $list[$key]['showweek'] = $this->getTimeWeek($datetime);
            $list[$key]['showtime'] = date('H:i', $datetime);
            $list[$key]['showdate'] = date('m-d', $datetime);
            $list[$key]['c_showimg'] = GetHost() . '/' . $value['c_showimg'];
            $w['c_tx_code'] = $value['c_key'];
            $res = M('Users_drawing')->field("c_state")->where($w)->find();
            $list[$key]['c_signstate'] = $res['c_state'];

            $idwhere['c_money_log_id'] = $value['c_id'];
            $info = M('Users_order_splitting')->where($idwhere)->getField('c_settled');
            if (isset($info)) {
               $list[$key]['c_status'] = $info;
            }else{
                $list[$key]['c_status'] = 1;
            }
            

            if ($value['c_money']>0) {
                $list[$key]['c_money'] = '+'.$value['c_money'];
            }

        }
        
        $count = M('Users_moneylog')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
       
        if ($parr['type']==2) {

            //支出
            $data["money_out"]= M('Users_moneylog')->where($where)->sum('c_money');
            
            $data["money_out"] = round($data["money_out"]);
        }else{
           
            //总收入和支出
            $money_all = M('Users_moneylog')->where($where)->sum('c_money');
            //收入

            $where['c_money'] = array('GT', 0);
            $data["money_income"] = M('Users_moneylog')->where($where)->sum('c_money');
            $data["money_income"] =  round($data["money_income"]);
            //支出
            $data["money_out"] = $money_all-$data["money_income"];
            $data["money_out"] = round($data["money_out"]);
        }

        if ($data["money_out"]<0) {
            $data["money_out"] = substr($data["money_out"],1);
        }
        
        if (empty($data["money_out"])) {
            $data["money_out"] = 0;
        }

        if (empty($data["money_income"])) {
            $data["money_income"] = 0;
        }
        return MessageInfo(0, '查询成功', $data);
    }

    //查询银盛待结算记录
     /*
      pageindex,pagesize,type,ucode
     *      */
    public function GetYsMoneyLog($parr) {

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

       
        //source 1扫码，2线上订单，3红包，4提现，5跨界，6其他
        switch ($parr['source']) {
            case '1':
                $where['c_source'] = array('in','9,12');
                break;
            case '2':
                $where['c_source'] = array('in','1,5,14,15,16');
                break;
            case '3':
                $where['c_source'] = array('in','7,8,18');
                break;
            case '4':
                $where['c_source'] = array('in','6');
                break;
            case '5':
                $where['c_source'] = array('in','5,12');
                break;
            case '6':
                $where['c_source'] = array('in','2');
                break;
            default:
                # code...
                break;
        }

        // if (!empty($parr['c_time'])) {
        //     $begintime = $parr['c_time'].'-01';
        //     $endtime = date('Y-m-d',strtotime('+'.($i+1).' months',strtotime($begintime)));
        //     $where[] = "c_addtime between '".$begintime."' and '".$endtime."'";
        // }
        
        $where['c_ucode'] = $parr['ucode'];
        $where['c_sign'] = 1;
        $where['c_settled'] = 2;
        $order = 'c_id desc';
        $list = M('Users_order_splitting')->where($where)->order($order)->limit($countPage, $pageSize)->select();
        foreach ($list as $key => $value) {

            //查询相关详情
            $source = $value['c_source'];
            if ($source == 1 || $source == 4 || $source == 5 || $source == 15) {
                $usign = 1;   //线上订单
                $sourcename = '货款进账';
                if ($source == 5) {
                    $sourcename = '购物优惠';
                } else if ($source == 4) {
                    $sourcename = '跨界佣金';
                } else if ($source == 15) {
                    $sourcename = '推广佣金';
                }
            } else if ($source == 9 || $source == 12) {
                $usign = 2;   //线下扫码
                $sourcename = '扫码进账';
                if ($source == 12) {
                    $sourcename = '跨界佣金';
                }
            }

            $list[$key]['usign'] = $usign;
            $list[$key]['sourcename'] = $sourcename;

            if ($usign == 1) {
                $owh['c_detailid'] = $value['c_key'];
                $info = M('Order_details')->where($owh)->field('c_orderid,c_addtime,c_pname,c_pimg,c_ptotal,c_pnum')->find();
                $list[$key]['orderid'] = $info['c_orderid'];
                $list[$key]['ttime'] = $info['c_addtime'];
                $list[$key]['pname'] = $info['c_pname'];
                $list[$key]['pimg'] = GetHost().'/'.$info['c_pimg'];
                $list[$key]['tprice'] = $info['c_ptotal'];
                $list[$key]['pnum'] = $info['c_pnum'];
            } else {
                $owh['c_ncode'] = $value['c_key'];
                $info = M('Scanpay')->where($owh)->find();

                $list[$key]['orderid'] = $info['c_ncode'];
                $list[$key]['ttime'] = $info['c_addtime'];
                $list[$key]['tprice'] = $info['c_actual_price'];
                $list[$key]['pnum'] = 1;

                //判断是否有用户
                if (!empty($info['c_ucode'])) {
                    $whereinfo['c_ucode'] = $info['c_ucode'];
                    $userinfo = M('Users')->where($whereinfo)->field('c_nickname,c_headimg')->find();
                    $list[$key]['pname'] = $userinfo['c_nickname'].'线下扫码';
                    $list[$key]['pimg'] = GetHost() . '/' . $userinfo['c_headimg'];
                } else {
                    $where['c_unionid'] = $info['c_unionid'];
                    $scanpaylog = M('Scanpay_tuijian')->where($where)->find();
                    $list[$key]['pimg'] = GetHost() . '/data/useimg/wxpay.png';
                    if ($scanpaylog['c_type'] == 1) {
                        $list[$key]['pname'] = '微信用户'.$scanpaylog['c_id'].'线下扫码';
                    } else if ($scanpaylog['c_type'] == 2) {
                        $list[$key]['pname'] = '支付宝用户'.$scanpaylog['c_id'].'线下扫码';
                        $list[$key]['pimg'] = GetHost() . '/data/useimg/alpay.png';
                    }
                }
            }
        }
        
        $count = M('Users_order_splitting')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
       
        return MessageInfo(0, '查询成功', $data);
    }

    //查询带结算详情
    /*
      id
     **/
    public function GetYsDetails($parr){

        if (empty($parr['id'])) {
           return MessageInfo(1004, '参数错误');
        }

        $where['c_id'] = $parr['id'];
        $data = M('Users_order_splitting')->where($where)->find();
        
        if (!empty($data)) {
            
        
            if ($data['c_source'] == 9 || $data['c_source'] == 12) {
                $w['c_ncode'] = $data['c_key'];
                $info = M('Scanpay')->where($w)->find();
                $data['details'] = $info;

                $s['c_ucode'] = $info['c_acode'];
                $uinfo = M('Users')->where($s)->field('c_nickname,c_headimg')->find();
                $data['nickname'] = $uinfo['c_headimg'];
                $data['headimg'] = GetHost() . '/' . $uinfo['c_headimg'];

            }else{
                $w['c_detailid'] = $data['c_key'];
                $info = M('Order_details')->where($w)->find();
                $img = GetHost() . '/' . $info['c_pimg'];
                $info['c_pimg'] = $img;
                $data['details'] = $info;

                $worder['c_orderid'] = $info['c_orderid'];
                $acode = M('Order')->where($worder)->getField('c_acode');

                //判断是否有该用户
                if (!empty($acode)) {
                    $s['c_ucode'] = $acode;
                    $uinfo = M('Users')->where($s)->field('c_nickname,c_headimg')->find();
                    $data['nickname'] = $uinfo['c_headimg'];
                    $data['headimg'] = GetHost() . '/' . $uinfo['c_headimg'];
                }
               
            }

        }
        return MessageInfo(0, '查询成功', $data);
    }

    function getTimeWeek($time, $i = 0) {
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        $oneD = 24 * 60 * 60;
        return "周" . $weekarray[date("w", $time + $oneD * $i)];
    }

    /**
     *  绑定身份证
     *  @param ucode,carid,uname
     */
    public function bindidcard($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $cardinfo = M('Users_bank')->where($where)->find();
        if (!$cardinfo) {
            $userinfo = M('Users')->where($where)->field('c_isagent,c_acode')->find();
            $idwhere['c_carid'] = $parr['carid'];
            $carducode = M('Users_bank')->where($idwhere)->getField('c_ucode');

            if ($carducode) {
                if ($userinfo['c_isagent'] == 0) {
                    //查询绑定的人是否是自己直属代理
                    $cardwhere['c_ucode'] = $carducode;
                    $carduserinfo = M('Users')->where($cardwhere)->field('c_acode,c_ucode,c_isagent')->find();
                    if ($carduserinfo['c_isagent'] == 0 || empty($userinfo['c_acode'])) {
                        return Message(1023, "您输入的身份证号已被人绑定");
                    } else {
                        if ($carduserinfo['c_acode'] != $userinfo['c_acode'] && $carduserinfo['c_ucode'] != $userinfo['c_acode']) {
                            return Message(1023, "您输入的身份证号已被人绑定");
                        }
                    }
                } else {
                    //查询是否被其他非旗下代理绑定
                    $cardwhere['c_ucode'] = $carducode;
                    $carduserinfo = M('Users')->where($cardwhere)->field('c_acode,c_ucode,c_isagent')->find();
                    if ($userinfo['c_isagent'] == 1) {
                        if ($carduserinfo['c_isagent'] == 1) {
                            return Message(1023, "您输入的身份证号已被人绑定");
                        } else if ($carduserinfo['c_isagent'] == 2) {
                            if ($carduserinfo['c_acode'] != $userinfo['c_ucode']) {
                                return Message(1023, "您输入的身份证号已被人绑定");
                            }
                        } else {
                            $areawhere['c_ucode'] = $carduserinfo['c_acode'];
                            $areaucode = M('Users')->where($areawhere)->getField('c_acode');
                            if ($areaucode != $userinfo['c_ucode']) {
                                return Message(1023, "您输入的身份证号已被人绑定");
                            }
                        }
                    } else {
                        if ($carduserinfo['c_isagent'] == 1) {
                            if ($carduserinfo['c_ucode'] != $userinfo['c_acode']) {
                                return Message(1023, "您输入的身份证号已被人绑定");
                            }
                        } else if ($carduserinfo['c_isagent'] == 2) {
                            if ($carduserinfo['c_acode'] != $userinfo['c_acode']) {
                                return Message(1023, "您输入的身份证号已被人绑定");
                            }
                        } else {
                            if ($carduserinfo['c_acode'] != $userinfo['c_ucode']) {
                                return Message(1023, "您输入的身份证号已被人绑定");
                            }
                        }
                    }
                }
            }


            $add['c_ucode'] = $parr['ucode'];
            $add['c_carid'] = $parr['carid'];
            $add['c_uname'] = $parr['uname'];
            $add['c_update'] = date('Y-m-d H:i:s');
            $result = M('Users_bank')->add($add);
            if (!$result) {
                return Message(2016, "绑定身份证失败");
            }
        } else {
            return Message(2016, "您已绑定身份证");
        }

        return Message(0, "身份证绑定成功");
    }

    /**
     * 绑定银行卡
     * @param  ucode,bankname,banksn,sub_bankname
     */
    public function bindingbank($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $cardinfo = M('Users_bank')->where($where)->find();
        if (!$cardinfo) {
            return Message(1020, '请先进行身份证绑定');
        }

        if ($cardinfo['c_banksn'] == $parr['banksn'] && $cardinfo['c_sub_bankname'] == $parr['sub_bankname']) {
            return Message(1021, "该银行卡已绑定");
        }

        if (empty($parr['sub_bankname']) || empty($parr['bankname']) || empty($parr['banksn']) || empty($parr['province']) || empty($parr['city'])) {
            return Message(1022,'请先完善银行卡相关信息');
        }

        $save['c_sub_bankname'] = $parr['sub_bankname'];
        $save['c_bankname'] = $parr['bankname'];
        $save['c_banksn'] = $parr['banksn'];
        $save['c_update'] = date('Y-m-d H:i:s');
        $save['c_province'] = $parr['province'];
        $save['c_city'] = $parr['city'];
        $result = M('Users_bank')->where($where)->save($save);
        if (!$result) {
            return Message(1022, "银行卡绑定失败");
        }

        return Message(0, "银行卡绑定成功");
    }

    /**
     * 绑定支付宝
     * @param  ucode,alipayname,alipaycard
     */
    public function bindzfbbank($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $cardinfo = M('Users_bank')->where($where)->find();
        if (!$cardinfo) {
            return Message(1020, '请先进行身份证绑定');
        }

        if ($cardinfo['c_alipaycard'] == $parr['alipaycard']) {
            return Message(1021, "该支付宝号已经绑定");
        }

        $save['c_alipayname'] = $parr['alipayname'];
        $save['c_alipaycard'] = $parr['alipaycard'];
        $save['c_update'] = date('Y-m-d H:i:s');
        $result = M('Users_bank')->where($where)->save($save);
        if (!$result) {
            return Message(1022, "支付宝号绑定失败");
        }

        return Message(0, "支付宝号绑定成功");
    }

    /**
     * 绑定微信
     * @param  ucode,wxname,wxcard
     */
    public function bindwxbank($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $cardinfo = M('Users_bank')->where($where)->find();
        if (!$cardinfo) {
            return Message(1020, '请先进行身份证绑定');
        }

        if ($cardinfo['c_wxcard'] == $parr['wxcard']) {
            return Message(1021, "该微信已绑定");
        }

        $save['c_wxname'] = $parr['wxname'];
        $save['c_wxcard'] = $parr['wxcard'];
        $save['c_update'] = date('Y-m-d H:i:s');
        $result = M('Users_bank')->where($where)->save($save);
        if (!$result) {
            return Message(1022, "微信绑定失败");
        }

        return Message(0, "微信绑定成功");
    }

    /**
     * 查询绑定信息
     * @param  ucode
     */
    public function Getbank($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $info = M('Users_bank')->where($where)->find();
        if ($info) {
            //判断用户是否绑定微信
            $userwhere1['c_type'] = 1;
            $userwhere1['c_ucode'] = $info['c_ucode'];
            $countweixin = M('Users_auth')->where($userwhere1)->count();
            $info['iswx_auth'] = 0;
            if ($countweixin > 0) {
                $info['iswx_auth'] = 1;
            }
        }
        return MessageInfo(0, "查询成功", $info);
    }

    /**
     * 提现申请
     * @param ucode,money,pwd,sign(1银行卡,2微信,3支付宝)
     */
    //ucode,money,pwd(银行卡id)
    public function drawing($parr) {
        $ucode = $parr['ucode'];

        //查询用户禁用信息
        $limitparr['ucode'] = $ucode;
        $limitparr['sign'] = 3;
        $result = IGD('Login', 'Login')->GetUserLimit($limitparr);
        if ($result['code'] != 0) {
            return $result;
        }

        //风控交易状态
        $ufparr['ucode'] = $ucode;
        $ufparr['sign'] = 2;
        $result = IGD('Funds','Info')->GetUseFunds($ufparr);
        if ($result['code'] != 0) {
            return $result;
        }

        //风控单笔提现部分
        $ruleparr['ucode'] = $ucode;
        $result = IGD('Funds','Info')->GetFundsRule($ruleparr);
        $fundsrule = $result['data'];
        if ($parr['money'] > $fundsrule['c_spenextract'] && $fundsrule['c_spenextract'] > 0) {
            return Message(3005,'单笔提现金额不能超过'.$fundsrule['c_spenextract']);
        }     

        //风控单日提现部分
        $countwhere['c_sign'] = 2;
        $countwhere['c_type'] = 4;
        $countwhere['c_ucode'] = $parr['acode'];
        $countwhere['c_datetime'] = date('Y-m-d');
        $countinfo = M('Users_moneydate')->where($countwhere)->find();
        if ($countinfo['c_money'] > $fundsrule['c_sdayextract'] && $fundsrule['c_sdayextract'] > 0) {
            return Message(3006,'单日提现金额不能超过'.$fundsrule['c_sdayextract']);
        }

        $User = IGD('Balance', 'User');
        $balance = $User->GetBalance($ucode);

        $money = $parr['money'];

        $stwhere['c_ucode'] = $ucode;
        $moneyinfo = M('Users_yesmoney')->where($stwhere)->find();
        $stmoney = $moneyinfo['c_stmoney'];
        if (($balance - $stmoney) < $money) {
            return Message(2016, "您的提现额度不足");
        }

        if ($money < 50 && $ucode!='xmwde5355c819a63292' && $ucode!='wldfd3ebad02d250d7f' && $ucode!='wld48666b7d31931fb3') {
            return Message(2016, "提现金额最少50噢，继续努力吧");
        }

        if ($moneyinfo['c_ysmoney'] > 0) {
            //判断快速提现金额
            // $ktxmoney = bcadd(bcsub($money, $moneyinfo['c_ysdrmoney'],2),1,2);
            // if ($ktxmoney > 0) {
            //     return Message(2019, "快速提现金额不足，可尝试减少提现金额");
            // }
            if (($moneyinfo['c_ysdrmoney']-$money) < 1) {
                return Message(2019, "快速提现金额不足，可尝试减少提现金额");
            } 
        }

        if ($money > 49000) {
            return Message(2017, "单笔提现不可超过49000");
        }

        $where['c_ucode'] = $parr['ucode'];
        $info = M('Users_bank')->where($where)->find();

        if (count($info) == 0) {
            return Message(2016, "您还没有绑定身份证");
        }

        if ($parr['sign'] == 2) {   //微信
            return Message(3000,'不能微信提现');
            //判断用户是否绑定微信
            $userwhere1['c_type'] = 1;
            $userwhere1['c_ucode'] = $info['c_ucode'];
            $countweixin = M('Users_auth')->where($userwhere1)->count();
            if (empty($info['c_wxname']) || empty($info['c_wxcard']) || $countweixin <= 0) {
                return Message(2016, "您还没有绑定微信，请先绑定微信");
            }

            //判断提现金额
            $limitmoney = $this->drawnowzong($parr);
            if (($limitmoney + $money) >= 20000) {
                return Message(2016, "您今天的微信提现额度已超");
            }
            $tixianlog['c_bankname'] = '微信';
            $tixianlog['c_uname'] = $info['c_wxname'];
            $tixianlog['c_banksn'] = $info['c_wxcard'];
            $tixianlog['c_sign'] = 2;
        } else if ($parr['sign'] == 3) {   //支付宝
            return Message(3000,'不能支付宝提现');
            if (empty($info['c_alipayname']) || empty($info['c_alipaycard'])) {
                return Message(2016, "您还没有绑定支付宝，请先绑定支付宝");
            }
            $tixianlog['c_bankname'] = '支付宝';
            $tixianlog['c_uname'] = $info['c_alipayname'];
            $tixianlog['c_banksn'] = $info['c_alipaycard'];
            $tixianlog['c_sign'] = 3;
        } else {   //银行卡            
            if (empty($info['c_bankname']) || empty($info['c_banksn']) || empty($info['c_sub_bankname']) || empty($info['c_province']) || empty($info['c_city'])) {
                return Message(2016, "您还没有完善银行卡信息，请先完善信息！");
            }

            //已经在银盛开户 则执行银盛的自动代付到银行卡
            if ($parr['sign'] == 1) {
                $result = IGD('Ysepay','Scanpay')->GetYsedata($parr);
                if ($result['code'] == 0) {
                    $tixianlog['c_remarks'] = '银盛快速提现';
                }
            }
                      
            $tixianlog['c_bankname'] = $info['c_bankname'];
            $tixianlog['c_uname'] = $info['c_uname'];
            $tixianlog['c_banksn'] = $info['c_banksn'];
            $tixianlog['c_sub_bankname'] = $info['c_sub_bankname'];
            $tixianlog['c_sign'] = 1;
            $tixianlog['c_province'] = $info['c_province'];
            $tixianlog['c_city'] = $info['c_city'];
        }


        $db = M('');
        $db->startTrans();

        //写入提现记录表
        $tixianlog['c_tx_code'] = CreateOrder();
        $tixianlog['c_ucode'] = $ucode;
        $tixianlog['c_money'] = $money;
        $tixianlog['c_state'] = 0;
        $tixianlog['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Users_drawing')->add($tixianlog);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(2016, "提现申请提交失败");
        }

        $User1 = IGD('Money', 'User');
        $parr1['ucode'] = $ucode;
        $parr1['money'] = $money;
        $parr1['source'] = 6;
        $parr1['key'] = $tixianlog['c_tx_code'];
        $parr1['desc'] = "余额提现";
        $parr1['state'] = 1;
        $parr1['type'] = 0;
        $parr1['isagent'] = 0;
        $parr1['showimg'] = 'Uploads/settlementshow/ti.png';
        $parr1['showtext'] = '提现';
        $result = $User1->OptionMoney($parr1);

        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //已经在银盛开户 则执行银盛的自动代付到银行卡
        if ($parr['sign'] == 1) {
            $result = IGD('Ysepay','Scanpay')->GetYsedata($parr);
            if ($result['code'] == 0) {
                $result = $this->GetYspayData($result['data'],$tixianlog['c_tx_code']);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }
            }
        }

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = '您提现余额￥' . $money . '申请已提交，请等待系统转账处理';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/drawinglog';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/drawinglog';
        $Msgcentre->CreateMessege($msgdata);

        $db->commit();
        return Message(0, "提现申请成功");
    }


    //银盛虚户提现到银行卡

    public function GetYspayData($ysdata,$txcode){
        vendor('Ysepay.Yse_pay');

        $w['c_tx_code'] = $txcode;
        $w['c_state'] = 0;
        $drawing_info = M('Users_drawing')->where($w)->find();
        $ucode = $drawing_info['c_ucode'];
        $money = $drawing_info['c_money'];

        if(empty($ucode) || $money <= 0 || !$drawing_info['c_sub_bankname'] || !$drawing_info['c_city'] || !$drawing_info['c_banksn'] || !$drawing_info['c_uname']){
            return Message(1001,"提现信息错误");
        }       
        
        $pay = new \Yse_pay();
        $parr['out_trade_no'] = $txcode;
        $parr['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
        $parr['notify_url'] = GetHost(1)."/index.php/Balance/Applyfor/pay_respond_notify";
        $parr['total_amount'] = $money;
        $parr['merchant_usercode'] = $ysdata['c_username'];   //扣款方
        $parr['bank_name'] = $drawing_info['c_sub_bankname']; //"中国工商银行股份有限公司岳阳奇家岭支行";////收款方
        $parr['bank_city'] = $drawing_info['c_city'];
        $parr['bank_account_no'] = $drawing_info['c_banksn'];//"6222021907005326066";//
        $parr['bank_account_name'] = $drawing_info['c_uname'];
        $parr['subject'] = '申请提现金额：'.$money.'元';
        $data = $pay->get_inner_df_card($parr);
        $result = $pay->curl_inner_df_card($data);
        $response = $result['ysepay_df_single_quick_accept_response'];
        if ($response['trade_status'] == 'TRADE_ACCEPT_SUCCESS') {
            // $save_data['c_thirdparty_code'] = $result['trade_no'];
            $save_data['c_state'] = 1;
            $save_data['c_updatetime'] = date('Y-m-d H:i:s');
            $result = M('Users_drawing')->where($w)->save($save_data);
            if (!$result) {
                return Message(3000,"提现金额到账失败");
            }
            return Message(0,"提现金额到账成功");
        } else {
            return Message(3000,$response['sub_msg']);
        }
    }



    //获取交易总额
    private function Gettransaction($ucode) {
        $sql = "select ifnull(SUM(c_profit), 0) as zong from t_order_details as a INNER JOIN t_order as b on a.c_orderid=b.c_orderid where "
                . "a.c_productstatus=0 and b.c_pay_state=1 and b.c_order_state=2 and (b.c_deliverystate=0 or b.c_deliverystate=1 or b.c_deliverystate=2) and b.c_acode='$ucode'";
        $db = M();
        $result = $db->query($sql);
        $huokuan = $result[0]['zong'];

        $sql = "select ifnull(SUM(c_rebate), 0) as zong from t_order_details as a INNER JOIN t_order as b on a.c_orderid=b.c_orderid where "
                . "a.c_productstatus=0 and b.c_pay_state=1 and b.c_order_state=2 and (b.c_deliverystate=0 or b.c_deliverystate=1 or b.c_deliverystate=2) and a.c_ucode='$ucode'";
        $db = M();
        $result = $db->query($sql);
        $fanli = $result[0]['zong'];

        $sql = "select ifnull(SUM(c_spread), 0) as zong from t_order_details as a INNER JOIN t_order as b on a.c_orderid=b.c_orderid where "
                . "a.c_productstatus=0 and b.c_pay_state=1 and b.c_order_state=2 and (b.c_deliverystate=0 or b.c_deliverystate=1 or b.c_deliverystate=2) and a.c_pucode='$ucode'";
        $db = M();
        $result = $db->query($sql);
        $tuikuang = $result[0]['zong'];

        $zong = $huokuan + $fanli + $tuikuang;
        return $zong;
    }

    //查询提现总额
    public function Getdrawing($ucode) {
        $sql = "select ifnull(SUM(c_money), 0) as zong from t_users_drawing where c_state=1 and c_ucode='$ucode'";
        $db = M();
        $result = $db->query($sql);

        if (empty($result)) {
            return 0;
        }
        $zong = $result[0]['zong'];
        return $zong;
    }

    //订单状态转换
    public function get_state($oid, $pid, $sid) {
        $mystatus = '';
        if (($oid == 2) && ($pid == 0)) {
            $mystatus = "未支付";
        } else if (($oid == 2) && ($pid == 1) && ($sid == 0)) {
            $mystatus = "未发货";
        } else if (($oid == 2) && ($pid == 1) && ($sid == 1)) {
            $mystatus = "未发货";
        } else if (($oid == 2) && ($pid == 1) && ($sid == 2)) {
            $mystatus = "待收货";
        } else if (($oid == 2) && ($pid == 1) && ($sid == 5)) {
            $mystatus = "已收货";
        } else if ($oid == 0) {
            $mystatus = "已取消";
        }
        return $mystatus;
    }

    //订单详情
    public function get_details($orderid, $type) {
        $whereinfo['c_orderid'] = $orderid;

        $field = '';
        if ($type == 1) {
            $whereinfo['c_productstatus'] = 0;
            $field = 'c_pimg,c_pname,c_pmodel_name,c_pnum,c_pprice,c_profit';
        } else if ($type == 2) {
            $whereinfo['c_productstatus'] = 0;
            $whereinfo['c_isrebate'] = 1;
            $field = 'c_pimg,c_pname,c_pmodel_name,c_pnum,c_pprice,c_rebate as c_profit';
        } else if ($type == 3) {
            $whereinfo['c_productstatus'] = 0;
            $whereinfo['c_isspread'] = 1;
            $field = 'c_pimg,c_pname,c_pmodel_name,c_pnum,c_pprice,c_spread as c_profit';
        }

        $details = M('order_details')->where($whereinfo)->field($field)->select();

        foreach ($details as $k => $v) {
            if (empty($v['c_pmodel_name'])) {
                $details[$k]['c_pmodel_name'] = '默认';
            }
            $details[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimg'];
        }

        return $details;
    }

    //查询正在交易中的货款进账
    public function Goodspayment($parr) {

        $ucode = $parr['ucode'];
        $type = $parr['type'];

        $whereinfo = "a.c_acode='$ucode' ";

        if ($type == 1) {//正在交易中
            $whereinfo .= "and a.c_order_state=2 and a.c_pay_state=1 and (a.c_deliverystate=0 or a.c_deliverystate=2)  ";
        } else if ($type == 2) {//未支付
            $whereinfo .= "and a.c_order_state=2 and a.c_pay_state=0 ";
        } else if ($type == 3) {//已支付，未发货
            $whereinfo .= "and a.c_order_state=2 and a.c_pay_state=1 and a.c_deliverystate=0 ";
        } else if ($type == 4) {//已支付，待收货
            $whereinfo .= "and a.c_order_state=2 and a.c_pay_state=1 and a.c_deliverystate=2 ";
        } else if ($type == 5) {//已支付，已收货
            $whereinfo .= "and a.c_order_state=2 and a.c_pay_state=1 and a.c_deliverystate=5 ";
        } else if ($type == 6) {//已取消
            $whereinfo .= "and a.c_order_state=0 ";
        }

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $field = 'a.c_orderid,a.c_pay_state,a.c_order_state,a.c_deliverystate,a.c_addtime';
        $order = 'a.c_id desc';
        $mydata = M('Order as a')->where($whereinfo)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        $list = array();
        if (!empty($mydata)) {
            foreach ($mydata as $row) {
                $list[] = array(
                    'c_orderid' => $row['c_orderid'],
                    'order_state' => $this->get_state($row['c_order_state'], $row['c_pay_state'], $row['c_deliverystate']),
                    'c_addtime' => $row['c_addtime'],
                    'detail' => $this->get_details($row['c_orderid'], 1)
                );
            }
        }

        $count = M('Order as a')->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //查询购买优惠
    public function Buyrebate($parr) {

        $ucode = $parr['ucode'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];

        $countPage = ($pageIndex - 1) * $pageSize;
        $sql = "SELECT c_orderid,c_pay_state,c_order_state,c_deliverystate,c_addtime FROM t_order  WHERE c_orderid IN (SELECT c_orderid  FROM  t_order_details WHERE c_ucode='$ucode' AND c_isrebate=1 AND c_productstatus=0 and c_rebate > 0) AND c_pay_state=1 AND c_order_state=2 AND (c_deliverystate=0 or c_deliverystate=2) order by c_id desc limit $countPage,$pageSize";

        $Model = M('');
        $mydata = $Model->query($sql);

        if (!empty($mydata)) {
            foreach ($mydata as $row) {
                $list[] = array(
                    'c_orderid' => $row['c_orderid'],
                    'order_state' => $this->get_state($row['c_order_state'], $row['c_pay_state'], $row['c_deliverystate']),
                    'c_addtime' => $row['c_addtime'],
                    'detail' => $this->get_details($row['c_orderid'], 2)
                );
            }
        }

        $sql1 = "SELECT count(c_id) as cout FROM t_order  WHERE c_orderid IN (SELECT c_orderid  FROM  t_order_details WHERE c_ucode='$ucode' AND c_isrebate=1 AND c_productstatus=0  and c_rebate > 0)  AND c_pay_state=1 AND c_order_state=2 AND (c_deliverystate=0 or c_deliverystate=2)";
        $num = $Model->query($sql1);

        $count = $num[0]['cout'];
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //查询推广佣金
    public function Spreadrebate($parr) {

        $ucode = $parr['ucode'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $sql = "SELECT c_orderid,c_pay_state,c_order_state,c_deliverystate,c_addtime FROM t_order  WHERE c_orderid IN (SELECT c_orderid  FROM  t_order_details WHERE c_pucode='$ucode' AND c_isspread=1 AND c_productstatus=0  and c_spread > 0)  AND c_pay_state=1 AND c_order_state=2 AND (c_deliverystate=0 or c_deliverystate=2) order by c_id desc limit $countPage,$pageSize";

        $Model = M('');
        $mydata = $Model->query($sql);

        $list = array();
        if (!empty($mydata)) {
            foreach ($mydata as $row) {
                $list[] = array(
                    'c_orderid' => $row['c_orderid'],
                    'order_state' => $this->get_state($row['c_order_state'], $row['c_pay_state'], $row['c_deliverystate']),
                    'c_addtime' => $row['c_addtime'],
                    'detail' => $this->get_details($row['c_orderid'], 3)
                );
            }
        }

        $sql1 = "SELECT count(c_id) as cout FROM t_order  WHERE c_orderid IN (SELECT c_orderid  FROM  t_order_details WHERE c_pucode='$ucode' AND c_isspread=1 AND c_productstatus=0  and c_spread > 0)  AND c_pay_state=1 AND c_order_state=2 AND (c_deliverystate=0 or c_deliverystate=2)";
        $num = $Model->query($sql1);

        $count = $num[0]['cout'];
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 查询当天分类提现金额总数
     * @param ucode,sign(1银行卡,2微信,3支付宝)
     */
    public function drawnowzong($parr) {
        $ucode = $parr['ucode'];
        $sign = $parr['sign'];
        $lttime = date('Y-m-d 23:59:59');
        $gttime = date('Y-m-d 00:00:00');

        $sql = "select ifnull(SUM(c_money), 0) as zong from t_users_drawing where c_sign='$sign' "
                . "and c_ucode='$ucode' and c_addtime>='$gttime' and c_addtime<='$lttime'";
        $db = M();
        $result = $db->query($sql);

        if (empty($result)) {
            return 0;
        }
        $zong = $result[0]['zong'];
        return $zong;
    }

    //每日分类型统计(分类型统计，主要用于饼状体)
    /*
     * ucode 用户编码
     * time 时间 格式2017-03-06
     * sign 1代表收入，2代表支出
     *      */
    public function dayincome($parr) {

        $ucode = $parr['ucode'];
        $time = $parr['time'];
        $sign = $parr['sign'];
        $source = $parr['source'];

        if (empty($sign)) {
            $sign = 1;
        }

        if (empty($ucode)) {
            return Message(1009, "该用户没有登陆，请登陆后查看");
        }

        $whereinfo['c_ucode'] = $ucode;
        $whereinfo['c_datetime'] = $time;
        $whereinfo['c_sign'] = $sign;
       if (!empty($parr['source'])) {

            //source 1扫码，2线上订单，3红包，4提现，5跨界，6其他
            switch ($parr['source']) {
                case '1':
                    $whereinfo['c_source'] = array('in','9,12');
                    break;
                case '2':
                    $whereinfo['c_source'] = array('in','1,5,14,15,16');
                    break;
                case '3':
                    $whereinfo['c_source'] = array('in','7,8,18');
                    break;
                case '4':
                    $whereinfo['c_source'] = array('in','6');
                    break;
                case '5':
                    $whereinfo['c_source'] = array('in','5,12');
                    break;
                case '6':
                    $whereinfo['c_source'] = array('in','2');
                    break;
                
                default:
                    # code...
                    break;
            }
            
        }
        $moneydata = M('Users_moneydate')->where($whereinfo)->field('c_money,c_type')->select();


        $data = array();
        $countmoney = '0.00';
        $dateout = '0.00';
        if ($sign == 1) {
            $parr['sign'] = 2;
            $parr['begintime'] = $time;
            $parr['endtime'] = $time;
            $dateoutresult = $this->timeslotexpenditure($parr);
            $dateout = !$dateoutresult['data']['c_money'] ? '0.00' : $dateoutresult['data']['c_money'];
        } else {
            $parr['sign'] = 1;
            $parr['begintime'] = $time;
            $parr['endtime'] = $time;
            $dateoutresult = $this->timeslotexpenditure($parr);
            $countmoney = !$dateoutresult['data']['c_money'] ? '0.00' : $dateoutresult['data']['c_money'];
        }

        //当没有查询到数据的时候
        if (!$moneydata) {

            for ($x = 0; $x <= 3; $x++) {
                $temp = $x + 1;
                $data[$x]['c_type'] = $temp;
                $data[$x]['c_money'] = 0;
            }

            $list['dateincome'] = $countmoney;
            $list['dateout'] = $dateout;
            $list['list'] = $data;
            return MessageInfo(0, "查询成功", $list);
        }

        for ($x = 0; $x <= 3; $x++) {
            $temp = $x + 1;
            $money = 0;
            foreach ($moneydata as $key => $value) {
                if ($temp == $value['c_type']) {
                    $money = $value['c_money'];
                    continue;
                }
            }
            $data[$x]['c_type'] = $temp;
            $data[$x]['c_money'] = abs($money);
            if ($sign == 1) {
                $countmoney += abs($money);
            } else {
                $dateout += abs($money);
            }
        }

        $list['dateincome'] = $countmoney;
        $list['dateout'] = $dateout;
        $list['list'] = $data;
        return MessageInfo(0, "查询成功", $list);
    }

    //时间段收支出统计（总统计，不分类型）
    /* ucode 用户编码
     * begintime 开始时间 时间 格式2017-03-06
     * endtime  支出时间
     *  sign 1代表收入，2代表支出
     *  */
    public function timeslotexpenditure($parr) {

        $ucode = $parr['ucode'];
        $begintime = $parr['begintime'];
        $endtime = $parr['endtime'];
        $sign = $parr['sign'];

        if (empty($sign)) {
            $sign = 1;
        }

        if (empty($ucode)) {
            return Message(1009, "该用户没有登陆，请登陆后查看");
        }

        $whereinfo['c_ucode'] = $ucode;
        $whereinfo['c_datetime'] = array('between', array($begintime, $endtime));

        $whereinfo['c_sign'] = $sign;
        $summoney = M('Users_moneydate')->where($whereinfo)->sum('c_money');

        $money = 0;
        if ($summoney) {
            $money = abs($summoney);
        }
        $data['c_money'] = $money;
        return MessageInfo(0, "查询成功", $data);
    }

    //时间段查询（分日期总统计。主要用户每日的折线图）
    /* ucode 用户编码
     * begintime 开始时间 时间 格式2017-03-06
     * endtime  支出时间
     * sign 1代表收入，2代表支出
     */
    public function broken($parr) {

        $ucode = $parr['ucode'];
        $begintime = $parr['begintime'];
        $endtime = $parr['endtime'];
        $sign = $parr['sign'];

        if (empty($sign)) {
            $sign = 1;
        }


        if (empty($ucode)) {
            return Message(1009, "该用户没有登陆，请登陆后查看");
        }
        $countday = $this->count_days($parr);

        if ($countday == 0) {
            return Message(1025, "该日期不在一个时间段");
        }
        $countday = $countday + 1;

        $sql = "select ifnull(SUM(c_money), 0) as c_money,c_datetime from t_users_moneydate where c_ucode='$ucode' and c_datetime>='$begintime' and  c_datetime<='$endtime' and c_sign=$sign GROUP BY c_datetime order by c_datetime asc ";

        $db = M();
        $result = $db->query($sql);

        $data = array();

        if (!$result) {
            for ($x = 0; $x < $countday; $x++) {
                if ($x == 0) {
                    $temptime = date("Y-m-d", strtotime("$begintime +$x day"));
                    ;
                } else {
                    $temptime = date("Y-m-d", strtotime("$begintime +$x day"));
                }
                $data[$x]['time'] = $temptime;
                $data[$x]['day'] = date("d号", strtotime("$temptime"));
                $data[$x]['c_money'] = 0;
            }
            return MessageInfo(0, "查询成", $data);
        }

        //匹配当前时间
        for ($x = 0; $x < $countday; $x++) {
            if ($x == 0) {
                $temptime = $begintime;
            } else {
                $temptime = date("Y-m-d", strtotime("$begintime +$x day"));
            }

            $money = 0;
            foreach ($result as $key => $value) {
                if ($temptime == $value['c_datetime']) {
                    $money = $value['c_money'];
                    continue;
                }
            }

            $data[$x]['time'] = $temptime;
            $data[$x]['day'] = date("d号", strtotime($temptime));
            $data[$x]['c_money'] = $money;
        }

        return MessageInfo(0, "查询成", $data);
    }

    //时间段收支统计(分类型统计，主要用于饼状体)
    /* begintime 开始时间 时间 格式2017-03-06
     * endtime  结束时间
     * sign 1代表收入，2代表支出
     */
    public function timeslotincome($parr) {
        $ucode = $parr['ucode'];
        $begintime = $parr['begintime'];
        $endtime = $parr['endtime'];
        $sign = $parr['sign'];

        if (empty($sign)) {
            $sign = 1;
        }
        if (empty($ucode)) {
            return Message(1009, "该用户没有登陆，请登陆后查看");
        }

        $sql = "select ifnull(SUM(c_money), 0) as c_money,c_type from t_users_moneydate where c_ucode='$ucode' and c_datetime>='$begintime' and  c_datetime<='$endtime' and c_sign=$sign GROUP BY c_type ";

        $db = M();
        $result = $db->query($sql);

        $countmoney = '0.00';
        $dateout = '0.00';
        if ($sign == 1) {
            $parr['sign'] = 2;
            $dateoutresult = $this->timeslotexpenditure($parr);
            $dateout = !$dateoutresult['data']['c_money'] ? '0.00' : $dateoutresult['data']['c_money'];
        } else {
            $parr['sign'] = 1;
            $dateoutresult = $this->timeslotexpenditure($parr);
            $countmoney = !$dateoutresult['data']['c_money'] ? '0.00' : $dateoutresult['data']['c_money'];
        }

        $data = array();

        if (!$result) {
            for ($x = 0; $x <= 3; $x++) {
                $temp = $x + 1;
                $data[$x]['c_type'] = $temp;
                $data[$x]['c_money'] = 0;
            }

            $list['dateincome'] = $countmoney;
            $list['dateout'] = $dateout;
            $list['list'] = $data;
            return MessageInfo(0, "查询成功", $list);
        }

        for ($x = 0; $x <= 3; $x++) {
            $temp = $x + 1;
            $money = 0;

            foreach ($result as $key => $value) {
                if ($temp == $value['c_type']) {
                    $money = $value['c_money'];
                    continue;
                }
            }
            $data[$x]['c_type'] = $temp;
            $data[$x]['c_money'] = abs($money);
            if ($sign == 1) {
                $countmoney += abs($money);
            } else {
                $dateout += abs($money);
            }
        }

        $list['dateincome'] = $countmoney;
        $list['dateout'] = $dateout;
        $list['list'] = $data;
        return MessageInfo(0, "查询成功", $list);
    }

    //计算两个日期的天数
    public function count_days($parr) {
        $time1 = $parr['begintime'];
        $time2 = $parr['endtime'];
        $d1 = strtotime($time1);
        $d2 = strtotime($time2);
        $day = round(($d2 - $d1) / 3600 / 24);
        return $day;
    }

    /*     * *********************获取收支详情************************* */

    //普通订单模板
    /* ucode 用户编码
     * source 类型（该方法只接收活动的订单，现阶段只支持2、3、7、8、10、11）
     * key 获取关键字
     * id 消息id
     */
    public function template($parr) {

        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];


        // $whereinfo['c_ucode'] = $ucode;
        $whereinfo['c_id'] = $id;

        $result = M('Users_moneylog')->where($whereinfo)->find();

        if (!$result) {
            return Message(1025, "没有查询到相关数据");
        }
        return MessageInfo(0, "查询成功", $result);
    }

    /* 根据订单查询商城订单 */
    /* ucode 用户编码
     * source 类型（）
     * key 获取关键字
     * id 消息id
     */

    public function ordertemplate($parr) {

        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        $db = M('Order as a');
        $whereorder['c_orderid'] = $key;

        $data = $db->join('inner join t_users as b on a.c_acode=b.c_ucode')->where($whereorder)->field("a.*,b.c_nickname,'' as usernickname")->find();

        if (!$data) {
            return Message(1024, "订单查询失败");
        }

        //判断是否有用户
        if (!empty($data['c_ucode'])) {

            $whereinfo['c_ucode'] = $data['c_ucode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname,c_headimg,c_shop')->find();
            $data['usernickname'] = $userinfo['c_nickname'];
            $data['shop'] = $userinfo['c_shop'];
            $data['headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        }

        //查询订单详情
        $wheredetial['c_orderid'] = $key;
        $details = M('Order_details')->where($wheredetial)->field('c_detailid,c_orderid,c_pname,c_pprice,c_pmodel_name,c_ptotal,c_pimg,c_free,c_profit,c_commission,c_addtime,c_productstatus')->select();
        $data['details'] = $details;

        //查询支付记录
        $whereinpaylog['c_orderid'] = $key;
        $paylog = M('Order_paylog')->where($whereinpaylog)->field('c_orderid,c_payrule,c_money,c_thirdparty,c_source,c_addtime')->select();
        $w['c_money_log_id'] = $id;
        $status = M('Users_order_splitting')->where($w)->getField('c_settled');
        if (!isset($status)) {
            $status = 1;
        }
        $data['c_status'] = $status;
        $data['paylog'] = $paylog;
        return MessageInfo(0, "查询成功", $data);
    }

    //根据订单查询小蜜商城订单
    public function supplierordert($parr) {

        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        $db = M('supplier_order as a');
        $whereorder['c_orderid'] = $key;

        $data = $data = $db->join('inner join t_supplier as b on a.c_acode=b.c_ucode')->where($whereorder)->field("a.*,b.c_person_name,'' as c_nickname,'' as usernickname")->find();

        if (!$data) {
            return Message(1024, "订单查询失败");
        }

        //查找商家
        if (!empty($data['c_scode'])) {
            $whereinfo['c_ucode'] = $data['c_scode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname')->find();
            $data['c_nickname'] = $userinfo['c_nickname'];
            $data['c_acode'] = $userinfo['c_scode'];
        } else {
            $whereinfo['c_ucode'] = $data['c_acode'];

            $userinfo = M('Supplier')->where($whereinfo)->field('c_name')->find();
            $data['c_nickname'] = $userinfo['c_name'];
        }

        //查找用户
        if (!empty($data['c_ucode'])) {
            $whereinfo['c_ucode'] = $data['c_ucode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname,c_headimg,c_shop')->find();
            $data['usernickname'] = $userinfo['c_nickname'];
            $data['shop'] = $userinfo['c_shop'];
            $data['headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        }

        //查询订单详情
        $wheredetial['c_orderid'] = $key;
        $details = M('Supplier_order_details')->where($wheredetial)->field('c_detailid,c_orderid,c_pname,c_pprice,c_pmodel_name,c_ptotal,c_pimg,c_free,c_profit,c_commission,c_addtime,c_productstatus')->select();
        $data['details'] = $details;
        $w['c_money_log_id'] = $id;
        $status = M('Users_order_splitting')->where($w)->getField('c_settled');
        if (!isset($status)) {
            $status = 1;
        }
        $data['c_status'] = $status;

        //查询支付记录
        $whereinpaylog['c_orderid'] = $key;
        $paylog = M('Order_paylog')->where($whereinpaylog)->field('c_orderid,c_payrule,c_money,c_thirdparty,c_source,c_addtime')->select();

        $data['paylog'] = $paylog;
        return MessageInfo(0, "查询成功", $data);
    }

    /* 扫码支付 */

    public function scanpaytemplate($parr) {
        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        $sw['c_ncode'] = $key;
        $data = M('Scanpay')->where($sw)->find();
        if (!$data) {
            return Message(1024, "获取订单失败");
        }

        $uw['c_ucode'] = $data['c_acode'];
        $data['c_nickname'] = M('Users')->where($uw)->getField('c_nickname');


        //判断是否有用户
        if (!empty($data['c_ucode'])) {

            $whereinfo['c_ucode'] = $data['c_ucode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname,c_headimg')->find();
            $data['usernickname'] = $userinfo['c_nickname'];
            $data['headimg'] = GetHost() . '/' .$userinfo['c_headimg'];
        } else {
            $where['c_unionid'] = $data['c_unionid'];
            $scanpaylog = M('Scanpay_tuijian')->where($where)->find();
            if ($scanpaylog['c_type'] == 1) {
                $data['usernickname'] = '微信用户'.$scanpaylog['c_id'];
            } else if ($scanpaylog['c_type'] == 2) {
                $data['usernickname'] = '支付宝用户'.$scanpaylog['c_id'];
            }
        }


        //查询支付记录
        $whereinpaylog['c_orderid'] = $data['c_ncode'];
        $paylog = M('Order_paylog')->where($whereinpaylog)->field('c_orderid,c_payrule,c_money,c_thirdparty,c_source,c_addtime')->select();

        $data['paylog'] = $paylog;
        
        $w['c_money_log_id'] = $id;
        $status = M('Users_order_splitting')->where($w)->getField('c_settled');
        if (!isset($status)) {
            $status = 1;
        }
        $data['c_status'] = $status;
        return MessageInfo(0, "查询成功", $data);
    }

    //提现模板
    public function tixiantemplate($parr) {

        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        $whereinfo['c_tx_code'] = $key;
        $whereinfo['c_ucode'] = $ucode;
        $data = M('Users_drawing')->where($whereinfo)->find();
        
        if (!$data) {
            return Message(1024, "没查询到数据");
        }
        return MessageInfo(0, "查询成功", $data);
    }

    //根据订单详情获取普通订单
    public function details($parr) {
        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        //查询订单详情
        $wheredetial['c_detailid'] = $key;
        $details = M('Order_details')->where($wheredetial)->field('c_detailid,c_orderid,c_pname,c_pprice,c_pmodel_name,c_ptotal,c_pimg,c_free,c_profit,c_commission,c_addtime,c_productstatus')->find();


        if (!$details) {
            return Message(1025, "订单详情获取失败");
        }

        $db = M('Order as a');
        $whereorder['c_orderid'] = $details['c_orderid'];

        $data = $db->join('inner join t_users as b on a.c_acode=b.c_ucode')->where($whereorder)->field("a.*,b.c_nickname,'' as usernickname")->find();

        if (!$data) {
            return Message(1024, "订单查询失败");
        }

        //查询订单详情
        $data['details'] = $details;

        //判断是否有用户
        if (!empty($data['c_ucode'])) {
            $whereinfo['c_ucode'] = $data['c_ucode'];
            $userinfo = M('Users')->where($whereinfo)->field('c_nickname,c_headimg,c_shop')->find();
            $data['usernickname'] = $userinfo['c_nickname'];
            $data['shop'] = $userinfo['c_shop'];
            $data['headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        }
        $w['c_money_log_id'] = $id;
        $status = M('Users_order_splitting')->where($w)->getField('c_settled');
        if (!isset($status)) {
            $status = 1;
        }
        $data['c_status'] = $status;

        return MessageInfo(0, "查询成功", $data);
    }

    //根据订单详情获取小蜜商城订单信息
    public function supplierdetails($parr) {

        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        //查询订单详情
        $wheredetial['c_detailid'] = $key;
        $details = M('Supplier_order_details')->where($wheredetial)->field('c_detailid,c_orderid,c_pname,c_pprice,c_pmodel_name,c_ptotal,c_pimg,c_free,c_profit,c_commission,c_addtime,c_productstatus')->find();

        if (!$details) {
            return Message(1025, "订单详情获取失败");
        }

        $db = M('Supplier_order as a');
        $whereorder['c_orderid'] = $details['c_orderid'];

        $data = $db->join('inner join t_supplier as b on a.c_acode=b.c_ucode')->where($whereorder)->field("a.*,b.c_person_name,'' as c_nickname,'' as usernickname")->find();

        if (!$data) {
            return Message(1024, "订单查询失败");
        }

        //查询订单详情
        $data['details'] = $details;

        $w['c_money_log_id'] = $id;
        $status = M('Users_order_splitting')->where($w)->getField('c_settled');
        if (!isset($status)) {
            $status = 1;
        }
        $data['c_status'] = $status;

        //查找商家
        if (!empty($data['c_scode'])) {
            $whereinfo['c_ucode'] = $data['c_scode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname,c_shop')->find();
            $data['c_nickname'] = $userinfo['c_nickname'];
            $data['shop'] = $userinfo['c_shop'];
            $data['c_acode'] = $userinfo['c_scode'];
        } else {
            $whereinfo['c_ucode'] = $data['c_acode'];

            $userinfo = M('Supplier')->where($whereinfo)->field('c_name')->find();
            $data['c_nickname'] = $userinfo['c_name'];
        }

        //查找用户
        if (!empty($data['c_ucode'])) {
            $whereinfo['c_ucode'] = $data['c_ucode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname,c_headimg')->find();
            $data['usernickname'] = $userinfo['c_nickname'];
            $data['headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        }

        return MessageInfo(0, "查询成功", $data);
    }

    //根据维权单号查询订单信息
    public function orderrefundinfor($parr) {

        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        $whereinfo['c_refundcode'] = $key;
        $data = M('Order_refund')->where($whereinfo)->field('c_refundcode,c_ucode,c_acode,c_orderid,c_orderdetailid,c_pname,c_pmname,c_pprice,c_pnum,c_free,c_ptotal,c_total,c_type,c_goods_status,c_reason,c_remarks,c_img,c_refundcode,c_handletime,
c_transno,c_transno,c_addtime')->find();

        if (!$data) {
            return Message(1024, "没有获取到维权信息");
        }

        //查找商家
        if (!empty($data['c_acode'])) {
            $whereinfo['c_ucode'] = $data['c_acode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname')->find();
            $data['c_nickname'] = $userinfo['c_nickname'];
        }
        return MessageInfo(0, "查询成功", $data);
    }

    //根据维权单号查询订单信息
    public function supplierorderrefundinfor($parr) {

        $ucode = $parr['ucode'];
        $source = $parr['source'];
        $key = $parr['key'];
        $id = $parr['id'];

        $whereinfo['c_refundcode'] = $key;
        $data = M('Supplier_order_refund')->where($whereinfo)->field('c_refundcode,c_ucode,c_acode,c_orderid,c_orderdetailid,c_pname,c_pmname,c_pprice,c_pnum,c_free,c_ptotal,c_total,c_type,c_goods_status,c_reason,c_remarks,c_img,c_refundcode,c_handletime,
c_transno,c_transno,c_addtime,c_scode')->find();

        if (!$data) {
            return Message(1024, "没有获取到维权信息");
        }

        //查找商家
        if (!empty($data['c_scode'])) {
            $whereinfo['c_ucode'] = $data['c_scode'];

            $userinfo = M('Users')->where($whereinfo)->field('c_nickname')->find();
            $data['c_nickname'] = $userinfo['c_nickname'];
        } else {
            $whereinfo['c_ucode'] = $data['c_acode'];

            $userinfo = M('Supplier')->where($whereinfo)->field('c_name')->find();
            $data['c_nickname'] = $userinfo['c_name'];
        }

        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 查询相关活动
     * @param joinaid
     */
    public function findActivty($parr) {
        $where['c_id'] = $parr['joinaid'];
        $data = M('Actjoin_moneylog')->where($where)->find();
        if (!$data) {
            return Message(1024, "没有相关活动数据");
        }
        $data['initiator'] = '小蜜';

        if (!empty($data['c_acode'])) {
            $userwhere['c_ucode'] = $data['c_acode'];
            $data['initiator'] = M('Users')->where($userwhere)->getField('c_nickname');
            $idwhere['c_id'] = $data['c_aid'];
            $data['c_activityname'] = M('Activity')->where($idwhere)->getField('c_activityname');
        }

        return MessageInfo(0, "查询成功", $data);
    }

     /**
     * 查询扫码支付月统计账单
     * @param ucode,time 格式2017-03
     */
    public function GetdataTally($parr)
    {
        $ucode = $parr['ucode'];
        $begintime = $parr['time'].'-01';
        $endtime = date('Y-m-d',strtotime('+'.($i+1).' months',strtotime($begintime)));
        $sign = 1;  //1收入

        $db = M();
        $sql = "select ifnull(SUM(c_money), 0) as c_money,c_datetime from t_users_moneydate where c_ucode='$ucode' and c_datetime>='$begintime' and  c_datetime<'$endtime' and c_sign=$sign and c_type=2 GROUP BY c_datetime order by c_datetime desc";
        $list = $db->query($sql);
        $money = '0.00';
        $num = '0';
        foreach ($list as $key => $value) {
            if ($value['c_datetime'] == date('Y-m-d')) {
                $list[$key]['time'] = '今日';
            } else if ($value['c_datetime'] == date('Y-m-d',strtotime('-1 days'))) {
                $list[$key]['time'] = '昨日';
            } else {
                $list[$key]['time'] = date("m月d日", strtotime($value['c_datetime']));
            }

            $money += $value['c_money'];

            // 获取每日交易笔数
            $parr['datetime'] = $value['c_datetime'];
            $list[$key]['count'] = $this->CountdateNum($parr);
            $num += $list[$key]['count'];
        }

        $data['money'] = $money;
        $data['num'] = $num;
        $data['list'] = $list;
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 获取用户每日扫码收款总数目
     * @param ucode,datetime 格式2017-03-01
     */
    public function CountdateNum($parr)
    {
        $startime = $parr['datetime'].' 00:00:00';
        $endtime = $parr['datetime'].' 23:59:59';
        $where[] = array("c_addtime>='$startime' and c_addtime<'$endtime'");
        $where['c_ucode'] = $parr['ucode'];
        $where['c_money'] = array('GT',0);
        $where[] = array('c_source=9 or c_source=12');
        $count = M('Users_moneylog')->where($where)->count();
        if ($count <= 0) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 获取用户每日扫码收款总记录
     * @param ucode,datetime 格式2017-03-01,pageindex,pagesize,type(1到账小蜜，2到账余额)
     */
    public function GetdateLog($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        if ($parr['type'] == 1) {   //到账小蜜记录
            //$where[] = array("c_bkmoney<=0 or c_xmmoney>0");
        } else if ($parr['type'] == 2) {    //到账银行卡记录
            $where['c_bkmoney'] = array('GT',0);
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $startime = $parr['datetime'].' 00:00:00';
        $endtime = $parr['datetime'].' 23:59:59';
        $where[] = array("c_addtime>='$startime' and c_addtime<'$endtime'");
        $where['c_ucode'] = $parr['ucode'];
        $where['c_money'] = array('GT',0);
        $where[] = array('c_source=9 or c_source=12');
        $field = 'c_money,c_desc,c_addtime,c_state,c_source,c_key,c_joinaid,c_showimg,c_showtext,c_id,c_bkmoney,c_xmmoney';
        $order = 'c_addtime desc';
        $list = M('Users_moneylog')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();
       
        $count = $this->CountdateNum($parr);
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['time'] = date("H:i", strtotime($value['c_addtime']));
            $list[$key]['c_showimg'] = GetHost() . '/' . $value['c_showimg'];
            //查询扫码订单交易方式
            $plogwhere['c_source'] = 2;
            $plogwhere['c_orderid'] = $value['c_key'];
            $paylog = M('Order_paylog')->where($plogwhere)->find();
            if ($paylog['c_payrule'] == 1) {
                $list[$key]['text'] = '支付宝支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/alpay.png';
            } else if ($paylog['c_payrule'] == 2 || $paylog['c_payrule'] == 3) {
                $list[$key]['text'] = '微信支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/wxpay.png';
            } else {
                $list[$key]['text'] = '余额支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/xmpay.png';
            }

            if (empty($value['c_xmmoney'])) {
                $list[$key]['c_xmmoney'] = $value['c_money'] - $value['c_bkmoney'];
            }
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 获取用户每日扫码到账小蜜与银行卡金额
    * @param ucode,datetime 格式2017-03-01
     */
    public function CountdateMoney($parr)
    {
        $startime = $parr['datetime'].' 00:00:00';
        $endtime = $parr['datetime'].' 23:59:59';
        $where[] = array("c_addtime>='$startime' and c_addtime<'$endtime'");
        $where['c_ucode'] = $parr['ucode'];
        $where['c_money'] = array('GT',0);
        $where[] = array('c_source=9 or c_source=12');
        $field = 'sum(c_money) as zmoney,sum(c_bkmoney) as bkmoney';
        $countmoney = M('Users_moneylog')->where($where)->field($field)->select();

        $zmoney = ($countmoney[0]['zmoney'] > 0)?$countmoney[0]['zmoney']:'0.00';
        $bkmoney = ($countmoney[0]['bkmoney'] > 0)?$countmoney[0]['bkmoney']:'0.00';
        
        $data['xmmoney'] = $zmoney - $bkmoney;
        $data['bkmoney'] = $bkmoney;
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 解绑银行卡
     * @param ucode
     */
    public function RelieveBank($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $cardinfo = M('Users_bank')->where($where)->find();
        if (!$cardinfo) {
            return Message(1020, '请先进行身份证绑定');
        }

        if (empty($cardinfo['c_banksn']) || empty($cardinfo['c_bankname']) || empty($cardinfo['c_sub_bankname'])) {
            return Message(1021, "暂未绑定银行卡");
        }

        $save['c_sub_bankname'] = '';
        $save['c_bankname'] = '';
        $save['c_banksn'] = '';
        $save['c_province'] = '';
        $save['c_city'] = '';
        $save['c_update'] = date('Y-m-d H:i:s');

        $result = M('Users_bank')->where($where)->save($save);
        if (!$result) {
            return Message(1022, "银行卡解绑失败");
        }

        return Message(0, "银行卡解绑成功");
    }

}
