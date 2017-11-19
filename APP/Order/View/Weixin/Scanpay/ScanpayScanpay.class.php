<?php

/**
 * 扫码支付接口
 */
class ScanpayScanpay {

    /**
     * 生成扫码支付订单
     * @param money,可选(ucode,openid),acode,balance(可传),payrule,type(订单来源类型1微信。2支付宝),(deskid收银台编号),(appsign)
     */
    function CreateScanpayOrder($parr) {
        //查询商家信息
        $acodewhere['c_ucode'] = $parr['acode'];
        $acodedata = M('Users')->where($acodewhere)->find();
        if (!$acodedata) {
            return Message(3000, '该商家信息不存在');
        }

        //查询联盟信息
        $cwh['c_ucode'] = $parr['acode'];
        $unioninfo = M('A_federation')->where($cwh)->find();
        if (empty($unioninfo['c_pid'])) {
            $scandata['c_pfederationid'] = $unioninfo['c_id'];
        } else {
            $scandata['c_pfederationid'] = $unioninfo['c_pid'];
        }
        $scandata['c_federationid'] = $unioninfo['c_id'];

        //查询收银员信息
        if (!empty($parr['deskid'])) {
            $csw['c_status'] = 1;
            $csw['c_work'] = 1;
            $csw['c_delete'] = 2;
            $csw['c_deskid'] = $parr['deskid'];
            $csdata = M('A_cashier')->where($csw)->find();
            $scandata['c_deskid'] = $parr['deskid'];
            $scandata['c_cashierid'] = $csdata['c_id'];
        }

        //查询商家禁用信息
        $limitparr['ucode'] = $parr['acode'];
        $limitparr['sign'] = 2;
        $result = IGD('Login', 'Login')->GetUserLimit($limitparr);
        if ($result['code'] != 0) {
            return $result;
        }

        $flag = 0;
        $ncode = CreateOrder('n');
        if (!empty($parr['ucode'])) {
            $userwhere['c_ucode'] = $parr['ucode'];
            $userinfo = M('Users')->where($userwhere)->find();
            $scandata['c_ucode'] = $userinfo['c_ucode'];
            $scandata['c_nickname'] = $userinfo['c_nickname'];
            $scandata['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
            $flag = 1;
        } else {
            if (empty($parr['openid'])) {
                return Message(3004, '扫码授权失败');
            }

            $scandata['c_type'] = $parr['type'];
            $scandata['c_openid'] = $parr['openid'];
            $scandata['c_nickname'] = filterEmoji($parr['nickname']);
            $scandata['c_headimg'] = $parr['headimgurl'];
            $scandata['c_unionid'] = $parr['unionid'];
        }        

        if ($parr['balance'] > 0 && $parr['balance'] < $parr['money']) {
            $scandata['c_actual_price'] = $parr['balance'];
            $scandata['c_pay_rule'] = $parr['payrule'];
        } else if ($parr['balance'] >= $parr['money']) {
            $scandata['c_actual_price'] = $parr['money'];
            $scandata['c_pay_rule'] = 4;
        }else if(empty($parr['balance']) || $parr['balance'] == 0){
        	$scandata['c_pay_rule'] = $parr['payrule'];
        }

        $db = M('');
        $db->startTrans();

        $ucodetj = $this->FindUcodeLock($scandata['c_ucode']);
        $openidtj = $this->FindOpenidLock($scandata['c_openid']);
        if ($ucodetj || $openidtj) {
            //查询行业平台抽成
            $settingwhere['b.c_ucode'] = $parr['acode'];
            $join = 'left join t_users as b on b.c_shoptrade=a.c_id '
                  . 'left join t_user_local as c on c.c_ucode=b.c_ucode';
            $setting = M('Shop_industry as a')->join($join)->where($settingwhere)->field('a.*,c.c_isfixed')->find();
            if (!$setting) {
                return Message(1017, "系统配置不存在");
            }
        }

        $scandata['c_ncode'] = $ncode;
        $scandata['c_acode'] = $parr['acode'];
        $scandata['c_money'] = $parr['money'];
        $scandata['c_addtime'] = date('Y-m-d H:i:s');
        if (($ucodetj['c_pcode'] == $parr['acode']) || ($openidtj['c_pcode'] == $parr['acode']) || (!$ucodetj && !$openidtj)
            || ($scandata['c_pfederationid'] == $openidtj['c_pfederationid'] && !empty($openidtj['c_pfederationid']) && $unioninfo['c_type'] == 1)
            || ($scandata['c_pfederationid'] == $ucodetj['c_pfederationid'] && !empty($ucodetj['c_pfederationid']) && $unioninfo['c_type'] == 1)
            || ($userinfo['c_isagent'] != 0 && !empty($userinfo))) {
            $scandata['c_commission'] = 0.00;
            $mchtype = 2;   //普通
        } else {
            if ($setting['c_isfixed'] == 1) {
                $scandata['c_commission'] = bcmul($parr['money'], bcdiv($setting['c_scanpay_shoprake'], 100, 4), 2);
            } else {
               $scandata['c_commission'] = bcmul($parr['money'], bcdiv($setting['c_online_shoprake'], 100, 4), 2);
            }
            $mchtype = 1;   //跨界
        }

        //查询商户号
        $result = IGD('Upay','Scanpay')->GetShopMchid($parr['acode'],$mchtype);
        $mch_id = $result['data']['c_merchantid'];

        if ($mch_id && $parr['appsign'] != 1) {
            $scandata['c_mchsign'] = 2;   //标识商家商户号支付
        } else {
            $scandata['c_mchsign'] = 1;   //标识平台商户号支付  
            //随机获取线下系统商户号
            $mchidarr = explode(',', C('OFFLINEMICH'));
            $mch_id = $mchidarr[rand(0,(count($mchidarr)-1))];
        }

        $scandata['c_profit'] = bcsub($parr['money'], $scandata['c_commission'], 2);
        $sid = M('Scanpay')->add($scandata);
        if (!$sid) {
            $db->rollback();
            return Message(3003, '添加失败');
        }

        if ($parr['balance'] > 0 && $parr['balance'] < $parr['money']) {
            $scandata['c_actual_price'] = $parr['balance'];
            $result = $this->BalanceOrder($ncode, 0);
            if ($result['code'] != 0) {
	            $db->rollback();
	            return $result;
        	}
        } else if ($parr['balance'] >= $parr['money']) {
            $scandata['c_actual_price'] = $parr['money'];
            $result = $this->BalanceOrder($ncode, 1);
            if ($result['code'] != 0) {
	            $db->rollback();
	            return $result;
        	}
        }

        //新增临时写入统计表
        // $countwhere['c_sign'] = 1;
        // $countwhere['c_type'] = 2;
        // $countwhere['c_ucode'] = $parr['acode'];
        // $countwhere['c_datetime'] = date('Y-m-d');
        // $countinfo = M('Users_moneydate')->where($countwhere)->find();
        // if (!$countinfo) {
        //     $countwhere['c_updatetime'] = date('Y-m-d H:i:s');
        //     $result = M('Users_moneydate')->add($countwhere);
        // }

        // if($flag == 1){
        //     //添加5公里商圈记录
        //     //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表 ,13店铺详情，14扫码支付）
        //     $blogdata['ucode'] = $parr['ucode'];
        //     $blogdata['behavior'] = 5;
        //     $blogdata['regionid'] = $sid;
        //     $blogdata['tag'] = 14;
        //     $blogdata['tagvalue'] = $parr['acode'];

        //     //查询自己位置信息
        //     $result1 = IGD('Servecentre','Serve')->GetLocation($parr['ucode']);
        //     $localtion = $result1['data'];

        //     $longitude = $localtion['longitude'];
        //     $latitude = $localtion['latitude'];
        //     $address = $localtion['address'];

        //     $blogdata['longitude'] = $longitude;
        //     $blogdata['latitude'] = $latitude;
        //     $blogdata['address'] = $address;
        //     $blogdata['addtime'] = date('Y-m-d H:i:s', time());

        //     $result = IGD('Servecentre','Serve')->Addlogs($blogdata);
        // }

        $db->commit();
        $returninfo['mch_id'] = $mch_id;
        $returninfo['ncode'] = $ncode;
        return MessageInfo(0, '添加成功', $returninfo);
    }

    /**
     * 余额及订单操作
     * @param ncode,sign
     */
    function BalanceOrder($ncode, $sign) {
        $w['c_ncode'] = $ncode;
        $orderinfo = M('Scanpay')->where($w)->find();
        if (!$orderinfo) {
            return Message('1001', '该订单状态不存在');
        }
        //用户功勋操作
        $rebatemoney = IGD('Money', 'User');
        $parr['ucode'] = $orderinfo['c_ucode'];
        $parr['money'] = $orderinfo['c_actual_price'];
        $parr['source'] = 9;
        $parr['key'] = $ncode;
        $parr['desc'] = "扫码余额支付";
        $parr['state'] = 1;
        $parr['type'] = 0;
        $parr['isagent'] = 0;
        $parr['showimg'] = 'Uploads/settlementshow/sao1.png';
        $parr['showtext'] = '扫码支付';
        $result = $rebatemoney->OptionMoney($parr);

        if ($result['code'] != 0) {
            return $result;
        }

        //用户支付记录操作
        $result = IGD('Order', 'Order')->paylog($ncode, 4, $orderinfo['c_actual_price'], '', 2);
        if ($result['code'] != 0) {
            return $result;
        }

        if ($sign == 1) {
            //修改订单状态
            $scandata['c_pay_state'] = 1;
            $result = M('Scanpay')->where($w)->save($scandata);

            if (!$result) {
                return Message('1002', '修改订单状态失败');
            }

            //开始返利操作
            $ucodetj = $this->FindUcodeLock($orderinfo['c_ucode']);
            $openidtj = $this->FindOpenidLock($orderinfo['c_openid']);
            if ($orderinfo['c_commission'] > 0 && ($ucodetj || $openidtj)) {
                $result = $this->CheckRebate($orderinfo, $ucodetj, $openidtj);
                if ($result['code'] != 0) {
                    return $result;
                }
            } else if (!$ucodetj && !$openidtj) {
                //绑定用户关系
                if (!empty($orderinfo['c_ucode'])) {
                    $result = $this->AddUcodeLock($orderinfo['c_ucode'], $orderinfo['c_acode']);
                    if ($result['code'] != 0) {
                        return $result;
                    }
                } else {
                    $result = $this->AddOpenidLock($orderinfo['c_openid'], $orderinfo['c_acode'],$orderinfo['c_unionid'],$orderinfo['c_type']);
                    if ($result['code'] != 0) {
                        return $result;
                    }
                }
            }

            //商家获得利润
            $beizhu = "用户扫码支付获得";
            $shopprofit = $orderinfo['c_profit'];
            $result = IGD('Order', 'Order')->faliRebate($orderinfo['c_acode'], $shopprofit, 9, $orderinfo['c_ncode'],$beizhu, 0,$bkmoney,$xmmoney,$orderinfo['c_cashierid'],$orderinfo['c_deskid']);
            if ($result['code'] != 0) {
                return $result;
            }

            $msgdata['ucode'] = $orderinfo['c_acode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '收到金额' . $orderinfo['c_money'] . '元，来自小蜜扫码支付';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['issound'] = 1;
            IGD('Msgcentre', 'Message')->CreateMessege($msgdata);

            if (!empty($orderinfo['c_cashierid'])) {     //给收银员推送消息
                //获取收银员基本信息
                $where['c_status'] = 1;
                $where['c_delete'] = 2;
                $where['c_id'] = $orderinfo['c_cashierid'];
                $cashinfo = M('A_cashier')->where($where)->find();
                if ($cashinfo) {
                    $msgdata['ucode'] = $cashinfo['c_ucode'];
                    $msgdata['type'] = 0;
                    $msgdata['platform'] = 1;
                    $msgdata['sendnum'] = 1;
                    $msgdata['title'] = '系统消息';
                    $msgdata['content'] = '收款金额' . $orderinfo['c_money'] . '元，来自小蜜扫码支付';
                    $msgdata['tag'] = 2;
                    $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Cashier/sdetail?ncode='.$orderinfo['c_ncode'].'&cashid='.$orderinfo['c_cashierid'];
                    $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Cashier/sdetail?ncode='.$orderinfo['c_ncode'].'&cashid='.$orderinfo['c_cashierid'];
                    $msgdata['issound'] = 1;
                    IGD('Msgcentre', 'Message')->CreateMessege($msgdata);
                }
            }
        }
        return MessageInfo(0, '操作成功', $orderinfo);
    }

    /**
     * 支付扫码订单
     * @param
     */
    function PayOrder($parr) {
        $orderid = $parr['orderid'];
        $payrule = $parr['payrule'];
        $actualprice = $parr['actualprice'];
        $thirdpartynum = $parr['thirdpartynum'];
        $upay = $parr['upay'];

        $db = M('');
        $db->startTrans();

        $orderinfo = $this->FindScanpayOrder($orderid)['data'];

        if (!$orderinfo) {
            return Message(3001, '该订单不存在');
        }

        if ($orderinfo['c_pay_state'] == 1) {
            return Message(3000, "该订单已支付");
        }

        //计算订单的总价
        $countprice = $orderinfo['c_money'];

        //计算支付总价
        $payzong = bcadd($actualprice, $orderinfo['c_actual_price'], 2);
        if ($payzong < $countprice) {
            return Message(1016, "您支付的金额不足");
        }

        //操作扫码支付订单
        $where['c_ncode'] = $orderid;
        $where['c_pay_state'] = 0;
        $save['c_actual_price'] = $countprice;
        $save['c_pay_rule'] = $payrule;
        $save['c_pay_state'] = 1;
        $result = M('Scanpay')->where($where)->save($save);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(3001, '操作订单状态失败');
        }

        //用户支付记录操作
        $result = IGD('Order', 'Order')->paylog($orderid, $payrule, $actualprice, $thirdpartynum, 2);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //开始返利操作
        $ucodetj = $this->FindUcodeLock($orderinfo['c_ucode']);
        $openidtj = $this->FindOpenidLock($orderinfo['c_openid']);
        if ($orderinfo['c_commission'] > 0 && ($ucodetj || $openidtj)) {
            $result = $this->CheckRebate($orderinfo, $ucodetj, $openidtj);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
            $mchtype = 1;   //跨界
        } else if (!$ucodetj && !$openidtj) {
            //绑定用户关系
            if (!empty($orderinfo['c_ucode'])) {
                $result = $this->AddUcodeLock($orderinfo['c_ucode'], $orderinfo['c_acode']);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }
            } else {
                $result = $this->AddOpenidLock($orderinfo['c_openid'], $orderinfo['c_acode'],$orderinfo['c_unionid'],$orderinfo['c_type']);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }
            }
            $mchtype = 2;   //普通 
        }

        $bkmoneybt = 0;$xmmoneybt = 0;
        //查询跨界费率商户号
        $result = IGD('Upay','Scanpay')->GetShopMchid($orderinfo['c_acode'],$mchtype);
        if ($result['code'] == 0) {
            $xmmoneybt = $result['data']['c_billrate']/1000;   //到账小蜜余额比例
            $bkmoneybt = 1 - $xmmoneybt;    //到账银行卡比例
        }

        //商家获得利润
        $beizhu = "用户扫码支付获得";
        $shopprofit = $orderinfo['c_profit'];
        if($shopprofit > 0){
            //商家自身商户号收款分别到账银行卡与小蜜余额
            if ($orderinfo['c_mchsign'] == 2) {
                if ($mchtype == 1) {  //跨界
                    $bkmoney = round($bkmoneybt*$actualprice,2);
                    $xmmoney = $shopprofit - $bkmoney;
                } else {  //普通
                    $bkmoney = round($bkmoneybt*$actualprice,2);
                    $xmmoney = $shopprofit - $bkmoney;    
                }
            }
            
        	$result = IGD('Order', 'Order')->faliRebate($orderinfo['c_acode'], $shopprofit, 9, $orderinfo['c_ncode'], $beizhu, 0,$bkmoney,$xmmoney,$orderinfo['c_cashierid'],$orderinfo['c_deskid']);
	        if ($result['code'] != 0) {
	            $db->rollback(); //不成功，则回滚
	            return $result;
	        }

            if ($payrule == 1) {
                $paycontent = '收到金额' . $orderinfo['c_money']  . '元，来自支付宝扫码支付';
            } else if ($payrule == 2 || $payrule == 3) {
                $paycontent = '收到金额' . $orderinfo['c_money']  . '元，来自微信扫码支付';
            } else {
                $paycontent = '收到金额' . $orderinfo['c_money']  . '元，来自小蜜扫码支付';
            }

	        $msgdata['ucode'] = $orderinfo['c_acode'];
	        $msgdata['type'] = 0;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['content'] = $paycontent;
	        $msgdata['tag'] = 2;
            $msgdata['issound'] = 1;
	        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        IGD('Msgcentre', 'Message')->CreateMessege($msgdata);
        }

        if (!empty($orderinfo['c_cashierid'])) {     //给收银员推送消息
            //获取收银员基本信息
            $where['c_status'] = 1;
            $where['c_delete'] = 2;
            $where['c_id'] = $orderinfo['c_cashierid'];
            $cashinfo = M('A_cashier')->where($where)->find();
            if ($cashinfo) {
                $msgdata['ucode'] = $cashinfo['c_ucode'];
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['content'] = '收款金额' . $orderinfo['c_money'] . '元，来自小蜜扫码支付';
                $msgdata['tag'] = 2;
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Cashier/sdetail?ncode='.$orderinfo['c_ncode'].'&cashid='.$orderinfo['c_cashierid'];
                $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Cashier/sdetail?ncode='.$orderinfo['c_ncode'].'&cashid='.$orderinfo['c_cashierid'];
                $msgdata['issound'] = 1;
                IGD('Msgcentre', 'Message')->CreateMessege($msgdata);
            }
        }

        $db->commit();

        $date = $this->FindScanpayOrder($orderid)['data'];
        return MessageInfo(0, '支付成功', $date);
    }

    /**
     *  返利操作
     *  @param
     */
    function CheckRebate($orderinfo, $ucodetj, $openidtj) {
        $ncode = $orderinfo['c_ncode'];
        $source = 12;      //金额来源（扫码支付标志）

        //查询行业平台抽成
        $settingwhere['b.c_ucode'] = $orderinfo['c_acode'];
        $join = 'left join t_users as b on b.c_shoptrade=a.c_id '
              . 'left join t_user_local as c on c.c_ucode=b.c_ucode';
        $setting = M('Shop_industry as a')->join($join)->where($settingwhere)->field('a.*,c.c_isfixed')->find();
        if (!$setting) {
            return Message(1017, "系统配置不存在");
        }

        if ($ucodetj) {
            $tuijianucode = $ucodetj['c_pcode'];
        } else {
            $tuijianucode = $openidtj['c_pcode'];
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        //给推荐人提成
        $commission = $orderinfo['c_commission'];
        if ($setting['c_isfixed'] == 1) {
            $scanpay_tjprofit = $setting['c_scanpay_tjprofit'];
        } else {
            $scanpay_tjprofit = $setting['c_online_tjprofit'];
        }
        if ($scanpay_tjprofit > 0 && !empty($tuijianucode)) {
            $tuijianmoney = bcmul($commission, bcdiv($scanpay_tjprofit, 100, 4), 2);
            if($tuijianmoney > 0){
            	$beizhu = "您推荐的用户跨界扫码支付返佣";
	            $result = IGD('Order', 'Order')->faliRebate($tuijianucode, $tuijianmoney, $source, $ncode, $beizhu, 0);
	            if ($result['code'] != 0) {
	                return $result;
	            }
	            //给用户发送相关消息
	            $msgdata['ucode'] = $tuijianucode;
	            $msgdata['type'] = 0;
	            $msgdata['platform'] = 1;
	            $msgdata['sendnum'] = 1;
	            $msgdata['title'] = '系统消息';
	            $msgdata['content'] = '您推荐的用户跨界消费，已通过扫码支付成功，您获得推广佣金' . $tuijianmoney . '元，已成功转入余额';
	            $msgdata['tag'] = 2;
	            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
	            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
	            $Msgcentre->CreateMessege($msgdata);
            }
        }

        //推荐人代理商提成
        $temp2['c_ucode'] = $tuijianucode;
        $buserinfo = M('Users')->where($temp2)->find();
        if ($setting['c_isfixed'] == 1) {
            $scanpay_cityprofit = $setting['c_scanpay_cityprofit'];
        } else {
            $scanpay_cityprofit = $setting['c_online_cityprofit'];
        }
        if ($scanpay_cityprofit > 0) {
            if (count($buserinfo) > 0 && !empty($buserinfo['c_acode'])) {
                $citycode = $buserinfo['c_acode'];
                $citymoney = bcmul($commission, bcdiv($scanpay_cityprofit, 100, 4), 2);
                if($citymoney > 0){
                	$beizhu = "您的微商【" . $buserinfo['c_nickname'] . "】推荐的会员跨界扫码支付返佣";
	                $result = IGD('Order', 'Order')->faliRebate($citycode, $citymoney, $source, $ncode, $beizhu, 0);
	                if ($result['code'] != 0) {
	                    return $result;
	                }

	                //给用户发送相关消息
	                $msgdata['ucode'] = $citycode;
	                $msgdata['type'] = 0;
	                $msgdata['platform'] = 1;
	                $msgdata['sendnum'] = 1;
	                $msgdata['title'] = '系统消息';
	                $msgdata['content'] = '您的微商【' . $buserinfo['c_nickname'] . '】推荐的会员跨界消费，已通过扫码支付成功，您获得推广佣金' . $citymoney . '元，已成功转入余额';
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
        if ($setting['c_isfixed'] == 1) {
            $scanpay_areaprofit = $setting['c_scanpay_areaprofit'];
        } else {
            $scanpay_areaprofit = $setting['c_online_areaprofit'];
        }
        if ($scanpay_areaprofit > 0) {
            if (count($quserinfo) > 0 && !empty($quserinfo['c_acode'])) {
                $areacode = $quserinfo['c_acode'];
                $areamoney = bcmul($commission, bcdiv($scanpay_areaprofit, 100, 4), 2);
                if($areamoney > 0){
                	$beizhu = "您旗下的微商【" . $buserinfo['c_nickname'] . "】推荐的会员跨界扫码支付返佣";
	                $result = IGD('Order', 'Order')->faliRebate($areacode, $areamoney, $source, $ncode, $beizhu, 0);
	                if ($result['code'] != 0) {
	                    return $result;
	                }

	                //给用户发送相关消息
	                $msgdata['ucode'] = $areacode;
	                $msgdata['type'] = 0;
	                $msgdata['platform'] = 1;
	                $msgdata['sendnum'] = 1;
	                $msgdata['title'] = '系统消息';
	                $msgdata['content'] = '您旗下的微商【' . $buserinfo['c_nickname'] . '】推荐的会员跨界消费，已通过扫码支付成功，您获得推广佣金' . $areamoney . '元，已成功转入余额';
	                $msgdata['tag'] = 2;
	                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
	                $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
	                $Msgcentre->CreateMessege($msgdata);
                }
            }
        }

        //商家红包
        if ($setting['c_isfixed'] == 1) {
            $red_scale = $setting['c_scanpay_redscale'];
        } else {
            $red_scale = $setting['c_online_redscale'];
        }
        $redprice = bcmul($commission, bcdiv($red_scale, 100, 4), 2);
        if ($redprice > 0) {
            $redvalue['c_ucode'] = $orderinfo['c_ucode'];
            $redvalue['c_pname'] = '扫码支付';
            $redvalue['c_pimg'] = 'Uploads/Activity/shopimg/signimg8.png';
            $redvalue['money'] = $redprice;
            $result = IGD('Red','Activity')->AddRedNum($redvalue,$orderinfo['c_acode']);
        }

        return Message(0, '返佣成功');
    }

    //查询扫码订单
    function FindScanpayOrder($ncode) {
        $result = M('Scanpay')->where(array('c_ncode' => $ncode))->find();
        if (!$result) {
            return Message(3000, '订单查询失败');
        }
        //查询商家名称
        $where1['c_ucode'] = $result['c_acode'];
        $shopinfo = M('Users')->where($where1)->field('c_nickname')->find();

        $where['c_orderid'] = $ncode;
        $where['c_source'] = 2;
        $where['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($where)->sum('c_money');
        $result['banlace'] = empty($banlacemoney)?0:$banlacemoney;
        $result['paymoney'] = bcsub($result['c_money'], $banlacemoney, 2);
        $result['c_anickname'] = $shopinfo['c_nickname'];
        return MessageInfo(0, '订单查询成功', $result);
    }

    //查询微信openid是否被锁定
    function FindOpenidLock($openid) {
        if (empty($openid)) {
            return false;
        }
        $result = M('Scanpay_tuijian')->where(array('c_openid' => $openid))->order('c_addtime desc')->find();
        return $result;
    }

    //锁定微信openid
    function AddOpenidLock($openid, $acode,$unionid,$type) {

        //查询联盟信息
        $cwh['c_ucode'] = $acode;
        $unioninfo = M('A_federation')->where($cwh)->find();
        if (empty($unioninfo['c_pid'])) {
            $pfederationid = $unioninfo['c_id'];
        } else {
            $pfederationid = $unioninfo['c_pid'];
        }
        $federationid = $unioninfo['c_id'];
        $add['c_pfederationid'] = $pfederationid;
        $add['c_federationid'] = $federationid;

        $add['c_openid'] = $openid;
        $add['c_pcode'] = $acode;
        $add['c_unionid'] = $unionid;
        $add['c_type'] = $type;
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Scanpay_tuijian')->add($add);
        if (!$result) {
            return Message(3000, '锁定用户关系失败');
        }
        return Message(0, '锁定用户关系成功');
    }

    //查询用户是否被锁定
    function FindUcodeLock($ucode) {
        if (empty($ucode)) {
            return false;
        }
        $result = M('Users_tuijian')->where(array('c_ucode' => $ucode))->order('c_addtime desc')->find();
        return $result;
    }

    //锁定用户
    function AddUcodeLock($ucode, $acode,$openid) {
        if ($ucode == $acode) {
            //清空微信推荐关系
            if (!empty($openid)) {
                $openidwhere['c_openid'] = $openid;
                $result = M('Scanpay_tuijian')->where($openidwhere)->delete();
            }
            return Message(0, '自己不能绑定自己');
        }
        $userinfowhere['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($userinfowhere)->field('c_isagent,c_acode')->find();
        if ($userinfo['c_isagent'] > 0) {
            return Message(0, '锁定用户关系成功');
        }

        //查询联盟信息
        $cwh['c_ucode'] = $acode;
        $unioninfo = M('A_federation')->where($cwh)->find();
        if (empty($unioninfo['c_pid'])) {
            $pfederationid = $unioninfo['c_id'];
        } else {
            $pfederationid = $unioninfo['c_pid'];
        }
        $federationid = $unioninfo['c_id'];
        $add['c_pfederationid'] = $pfederationid;
        $add['c_federationid'] = $federationid;

        $add['c_ucode'] = $ucode;
        $add['c_pcode'] = $acode;
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $add['c_source'] = 3;
        $result = M('Users_tuijian')->add($add);
        if (!$result) {
            return Message(3000, '锁定用户关系失败');
        }

        $whereacodeinfo['c_ucode'] = $acode;
        $acodeinfo = M('Users')->where($whereacodeinfo)->getField('c_acode');
        if (!$acodeinfo) {
            return Message(1016, "未有所属代理商");
        }

        if (!$userinfo['c_acode']) {
            $datauserinfo['c_acode'] = $acodeinfo;
            $result = M('Users')->where($userinfowhere)->save($datauserinfo);
            if (!$result) {
                return Message(1016, "纳入代理商失败");
            }
        }

        //改变锁定状态
        $openidwhere['c_openid'] = $openid;
        $openidsave['c_lock'] = 1;
        $result = M('Scanpay_tuijian')->where($openidwhere)->save($openidsave);
        // if (!$result) {
        //     return Message(1017, "锁定关系改变失败");
        // }


        return Message(0, '锁定用户关系成功');
    }

    /**
     * 绑定快捷注册
     * @param phone,pwd,acode,type,openid,nickname,headimgurl
     */
    function BindRegister($parr) {
        $phone = $parr['phone'];
        $pwd = $parr['pwd'];
        $acode = $parr['acode'];

        $db = M('');
        $db->startTrans(); /* 开启事务 */

        $wherephone['c_phone'] = $phone;
        $ucode = M('Users')->where($wherephone)->getField('c_ucode');
        if ($ucode) {
            $sendmsg = 0;
        } else {
            $ucode = CreateUcode(); /* 创建用户编码 */
            if ($parr['sex'] == 1) {
                $useradd['c_sex'] = '男';
            } else if ($parr['sex'] == 2) {
                $useradd['c_sex'] = '女';
            }

            //微信头像保存服务端
            $pathdir = SYS_PATH . 'Uploads' . DS . 'headimg' . DS . 'weixin' . DS . $ucode . '.jpg'; //头像保存路径
            if (checkDir($pathdir)) {
                if (!is_file($pathdir) && !empty($parr['headimgurl'])) {
                    $http = new \Org\Net\Http;
                    $tx = $http->curlDownload($parr['headimgurl'], $pathdir);
                }
            }

            if (is_file($pathdir)) {
                $useradd['c_headimg'] = 'Uploads/headimg/weixin/' . $ucode . '.jpg';
                //保存远程图片
                $result = qiniu_syn_files($useradd['c_headimg'], $useradd['c_headimg']);
                if (!$result) {
                    $db->rollback();
                    return Message(1007, '远程上传头像失败');
                }
            } else {
                $useradd['c_headimg'] = 'data/userheadimg/' . rand(11, 20) . '.jpg';
            }

            $getnickname = filterEmoji($parr['nickname']);

            $useradd['c_ucode'] = $ucode;
            $useradd['c_phone'] = $phone;
            $useradd['c_nickname'] = !empty($getnickname)?$getnickname:'小蜜用户'.time();
            $useradd['c_signature'] = '蜜主很懒，没有什么个性签名！';
            $useradd['c_password'] = encrypt($pwd, C('ENCRYPT_KEY'));
            $useradd['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Users')->add($useradd);
            if (!$result) {
                $db->rollback();
                return Message(1002, '注册失败！');
            }
            $sendmsg = 1;
        }

        // 判断微信用户是否授权小蜜帐号
        $deletwhere['c_type'] = $parr['type'];
        $deletwhere['c_openid'] = $parr['openid'];
        $result = M('Users_auth')->where($deletwhere)->select();
        if ($result) {
            $db->rollback();
            return Message(1005,'微信帐号已绑定小蜜帐号');
        }

        //判断小蜜帐号是否授权微信
        $authucode['c_ucode'] = $ucode;
        $authucode['c_type'] = $parr['type'];
        $result = M('Users_auth')->where($authucode)->select();
        if ($result) {
            $db->rollback();
            return Message(1005,'小蜜帐号已授权其他微信帐号');
        }

        //写入授权信息
        $authdata['c_ucode'] = $ucode;
        $authdata['c_openid'] = $parr['openid'];
        $authdata['c_unionid'] = $parr['unionid'];
        $authdata['c_name'] = filterEmoji($parr['nickname']);
        $authdata['c_headimg'] = $parr['headimgurl'];
        $authdata['c_type'] = $parr['type'];
        $authdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Users_auth')->add($authdata);
        if (!$result) {
            $db->rollback();
            return Message(1004, '授权失败');
        }

        // 保存第三方token
        $tokenresult = IGD('UserProcess', 'Rongcloud')->token($ucode);
        if ($tokenresult['code'] != 0) {
            $db->rollback();
            return Message(1005, '保存第三方token失败！');
        }

        if (!empty($acode)) {
            //查询用户推荐关系是否存在
            $tuijianwhere['c_ucode'] = $ucode;
            $tuijiandata = M('Users_tuijian')->where($tuijianwhere)->find();
            if (!$tuijiandata) {
                $pdacode = $this->FindOpenidLock($parr['openid']);
                if ($pdacode) {
                    $result = $this->AddUcodeLock($ucode, $pdacode['c_pcode'],$parr['openid']);
                } else {
                    $result = $this->AddUcodeLock($ucode, $acode,$parr['openid']);
                }
                if ($result['code'] != 0) {
                    $db->rollback();
                    return $result;
                }
            }
        }
        
        if ($sendmsg == 1) {
            $sendparr['telephone'] = $phone;
            $sendparr['type'] = 1000;
            $sendparr['userid'] = C('TEl_USER');
            $sendparr['account'] = C('TEl_ACCESS');
            $sendparr['password'] = C('TEl_PASSWORD');
            $sendparr['content'] = "【微领地小蜜】您的动态登录密码为：" . $pwd . "，工作人员不会向您索取登录密码，关注VIP小蜜，了解小蜜最新资讯";
            $returndata = IGD('Sendmsg','Login')->SendVerify($sendparr);
        }

        $db->commit();
        return MessageInfo(0, "添加成功", $ucode);
    }

     /**
     * 绑定有礼
     * @param ucode,burl
     */

    public function BindGift($parr){
        $ucode = $parr['ucode'];
        $burl = $parr['burl'];

        $db = M('');
        $db->startTrans(); /* 开启事务 */

        //判断上个页面来源
        $trueurl = 'Scanpay/bindtel';
        $result = strstr($burl, $trueurl);
        if(!$result){
            return Message(1001,'链接来源错误！');
        }

        //判断微信授权时间
        $where['c_ucode'] = $ucode;
        $authdata = M('Users_auth')->where($where)->find();
        if(!$authdata){
            return Message(1002,'查询授权信息失败！');
        }

        $w['c_activitytype'] = 17;
        $activity_info = M('Activity')->where($w)->find();

        $activitystarttime = strtotime($activity_info['c_activitystarttime']);
        $activityendtime = strtotime($activity_info['c_activityendtime']);
        $authtime = strtotime($authdata['c_addtime']);
        if(($authtime < $activitystarttime) && ($authtime > $activityendtime)){
           return Message(1003,'不在活动时间内！');
        }

        //判断是否已经领取过绑定有礼红包
        $logwhere['c_ucode'] = $ucode;
        $logwhere['c_aid'] = $activity_info['c_id'];
        $loginfo = M('Activity_log')->where($logwhere)->find();
        if($loginfo){
            return Message(1004,'您已经领取过该礼物！');
        }

        /* 开始发送红包 */

        //查询金额池表
        $moneywhere['c_state'] = 1;
        $moneywhere['c_ucode'] = $ucode;
        $moneywhere['c_remain'] = array('GT', 0);
        $moneywhere['c_aid'] = $activity_info['c_id'];
        $moneydata = M('Activity_money')->where($moneywhere)->find();

        if(!$moneydata){
            return Message(1005,'礼品不存在或者奖金少于0！');
        }

        //分配红包金额
        $moneyprize = rand($moneydata['c_min_money'] * 100, $moneydata['c_max_money'] * 100);
        $prizelogdata['c_value'] = bcdiv($moneyprize, 100, 2);
        if (($moneydata['c_remain'] - $prizelogdata['c_value']) < 0) {
            $prizelogdata['c_value'] = $moneydata['c_remain'];
        }
        $prizelogdata['c_type'] = 1;
        $prizelogdata['c_aid'] = $activity_info['c_id'];
        $prizelogdata['c_ucode'] = $ucode;
        $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');

        $result = M('Activity_log')->add($prizelogdata);
        if(!$result){
            $db->rollback(); //不成功，则回滚
            return Message(1006, '奖品领取记录失败！');
        }

        // 写入用户余额
        $moneyparr['ucode'] = $ucode;
        $moneyparr['money'] = $prizelogdata['c_value'];
        $moneyparr['source'] = 11;
        $moneyparr['key'] = $activity_info['c_activityname'];
        $moneyparr['desc'] = "扫码支付绑定账号！";
        $moneyparr['state'] = 1;  //完成状态
        $moneyparr['type'] = 1;
        $moneyparr['isagent'] = 0;
        $moneyparr['showimg'] = 'Uploads/settlementshow/huo.png';
        $moneyparr['showtext'] = '活动';
        $result =  IGD('Money', 'User')->OptionMoney($moneyparr);
        if ($result['code'] !== 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1007, '修改用户余额失败！');
        }

        // 扣除奖池总额
        $moneywhere['c_remain'] = array('EGT', $prizelogdata['c_value']);
        $moneysave['c_remain'] = bcsub($moneydata['c_remain'], $prizelogdata['c_value'],2);
        $result = M('Activity_money')->where($moneywhere)->save($moneysave);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1008, '扣除奖池总额失败！');
        }

        $db->commit();

        // 写入消息中心
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您好，小蜜为您献上注册红包' . $prizelogdata['c_value'] . '元已成功转入您的余额账户！';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';

        $msgresult = IGD('Msgcentre', 'Message')->CreateMessegeInfo($msgdata);

        $returndata['money'] = $prizelogdata['c_value'];
        return MessageInfo(0, '派送红包成功', $returndata);
    }

    /**
     * 取消订单
     * @param ncode,ucode
     */
    public function CancelOrder($parr) {
        $ncode = $parr['ncode'];
        $ucode = $parr['ucode'];
        $whereorder['c_ncode'] = $ncode;
        $whereorder['c_ucode'] = $ucode;

        $orderinfo = M('Scanpay')->where($whereorder)->find();
        if (count($orderinfo) == 0) {
            return Message(1000, '该订单不存在');
        }

        //判断订单是否可以进行取消订单
        if ($orderinfo['c_pay_state'] != 0) {

            return Message(1001, '该订单无法取消');
        }

        $db = M('');
        $db->startTrans();

        $update['c_pay_state'] = -1;
        $result = M('Scanpay')->where($whereorder)->save($update);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1001, '修改订单状态失败');
        }

        //返回用户余额
        if ($orderinfo['c_actual_price'] > 0) {
            $rebatemoney = IGD('Money', 'User');
            $parr['ucode'] = $orderinfo['c_ucode'];
            $parr['money'] = $orderinfo['c_actual_price'];
            $parr['source'] = 9;
            $parr['key'] = $ncode;
            $parr['desc'] = "取消订单余额支付";
            $parr['state'] = 1;
            $parr['type'] = 1;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/sao.png';
            $parr['showtext'] = '扫码取消';
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
            $msgdata['content'] = '您的订单号：' . $ncode . '已取消成功，退还抵扣余额' . $orderinfo['c_actual_price'] . '元成功';
            $msgdata['tag'] = 2;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
            $Msgcentre->CreateMessege($msgdata);
        }

        $db->commit();

        return Message(0, '操作成功');
    }

    /**
     * 微信用户绑定帐号同步绑定扫码订单
     * @param ucode,openid
     */
    function BindScanpayOrder($ucode,$openid)
    {
        //查询用户信息
        $userwhere['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($userwhere)->field('c_nickname,c_headimg,c_ucode')->find();
        if (!$userinfo) {
            return Message(1000,'绑定用户不存在');
        }
        $where['c_openid'] = $openid;
        $scandata['c_ucode'] = $userinfo['c_ucode'];
        $scandata['c_nickname'] = $userinfo['c_nickname'];
        $scandata['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        $result = M('Scanpay')->where($where)->save($scandata);
        if (!$result) {
            return Message(1002,'绑定失败');
        }

        return Message(0,'绑定成功');
    }
}
