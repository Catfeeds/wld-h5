<?php

/**
 * 用户余额金额业务逻辑
 *
 */
class BalanceUser {

    /*
	获取用户余额
	@param string $ucode
	*/
    function GetBalance($ucode)
    {
        $result = '';
        $where['c_ucode'] = $ucode;
        $money = M('users')->where($where)->field('c_money')->find();
        if(!empty($money['c_money'])){
        	  $result = $money['c_money'];
        }
        return $result;
    }

    /*
    获取用户余额记录
    @param string $ucode
    @param int    $pageIndex
    @param int    $pageCount
    */
    function GetBalanceLog($ucode,$pageIndex,$pageCount)
    {
        $count = 0;
        if ($pageIndex > 0) {
            $count = ($pageIndex - 1) * $pageCount;
        }

        $sql = "select * from t_users_moneylog where c_ucode='" . $ucode ."'";
        $sql.= " order by c_id desc limit " . $count . "," . $pageCount;

        $balanceinfo = M('');
        $list = $balanceinfo->query($sql);
        return $list;
    }

    /*
    获取用户余额记录总条数
    @param string $ucode
    */
    function GetBalanceLogCount($ucode) {
        $balanceinfo = M('users_moneylog');
        $where['c_ucode'] = $ucode;
        $count = $balanceinfo->where($where)->count();
        return $count;
    }

    /*
    获取用户推荐会员人数数量
    @param string $ucode
    */
    function TjCount($ucode){
        $tuijianinfo = M('users_tuijian');
        $where['c_ucode'] = $ucode;
        $count = $tuijianinfo->where($where)->count();
        return $count;
    }

    /*
    获取用户推荐会员信息
    @param string $ucode
    @param int    $pageIndex
    @param int    $pageCount
    */
   function GetTuijian($ucode,$pageIndex,$pageCount){
        $count = 0;
        if ($pageIndex > 0) {
            $count = ($pageIndex - 1) * $pageCount;
        }

        $sql  = "select t.*,u.c_nickname from t_users_tuijian as t ";
        $sql .= "left join t_users as u on u.c_ucode=t.c_pcode ";
        $sql .= "where t.c_ucode='" . $ucode ."'";
        $sql .= " order by t.c_id desc limit " . $count . "," . $pageCount;

        $balanceinfo = M('');
        $list = $balanceinfo->query($sql);
        return $list;
   }

   /*
   用户绑定兑换功勋账号
   @param array $par
   */
   function AddBack($par){
        $date['c_ucode'] = $par['c_ucode'];
        $date['c_bankname'] = $par['c_bankname'];
        $date['c_uname'] = $par['c_uname'];
        $date['c_banksn'] = $par['c_banksn'];
        $date['c_update'] = date('Y-m-d H:i:s',time());

        //暂时一个用户只能绑定一个账号，验证该用户是否已经绑定账号
        $where['c_ucode'] = $par['c_ucode'];
        $cout = M('t_users_bank')->where($where)->cout();
        if($cout > 0){
            return 1;
        }

        $result = M('Users_bank')->add($date);
        if($result){
            return 0;//添加成功
        }else{
            return 2;//添加失败
        }
   }

   /*
   用户绑定兑换功勋账号修改
   @param array $par
   */
   function EditBack($par){
        $date['c_id'] = $par['c_id'];
        $date['c_ucode'] = $par['c_ucode'];
        $date['c_bankname'] = $par['c_bankname'];
        $date['c_uname'] = $par['c_uname'];
        $date['c_banksn'] = $par['c_banksn'];
        $date['c_update'] = date('Y-m-d H:i:s',time());

        $result = M('Users_bank')->save($date);
        if($result){
            return 0;//编辑成功
        }else{
            return 2;//编辑失败
        }
   }

    /**
    * 同步用户银盛余额
    * @param ucode
    */
    function SyncYesMoney($parr)
    {
        Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();

        //查询开户情况
        $yw['c_ucode'] = $parr['ucode'];
        $yw['c_openaccount'] = 1;
        $field = 'c_ucode,c_openaccount,c_reason,c_person,c_personphone,c_username';
        $yseinfo = M('User_yspay')->where($yw)->field($field)->find(); 
        if (!$yseinfo) {
            return Message(3000,'开户记录不存在');
        } 


        $parr['partner_id'] = "wld17375717292";// 合作商户号
        $parr['merchant_usercode'] = $yseinfo['c_username'];  //商户账号
        $data = $pay->query_money($parr);
        $result = $pay->curl_query_money($data);
        $data1 = $result['ysepay_merchant_balance_query_response'];
        if (empty($data1)) {
            $moneyinfo['zmoney'] = '0.00';
            $moneyinfo['drmoney'] = '0.00';
            $moneyinfo['stmoney'] = '0.00';
        } else {
            $moneyinfo['zmoney'] = $data1['account_total_amount'];
            $moneyinfo['drmoney'] = $data1['account_detail']['0']['account_amount'];
            $moneyinfo['stmoney'] = $data1['account_detail']['1']['account_amount'];
        }

        $where['c_ucode'] = $parr['ucode'];
        $stmoneyinfo = M('Users_yesmoney')->where($where)->find();
        if ($stmoneyinfo) {
            $ysmsave['c_ysmoney'] = $moneyinfo['zmoney'];
            $ysmsave['c_ysdrmoney'] = $moneyinfo['drmoney'];
            $ysmsave['c_ysstmoney'] = $moneyinfo['stmoney'];
            $ysmsave['c_money'] = M('Users')->where(array('c_ucode'=>$where['c_ucode']))->getField('c_money');
            $ysmsave['c_updatetime'] = gdtime();
            $result = M('Users_yesmoney')->where($where)->save($ysmsave);   
        } else {
            $stadd['c_ucode'] = $parr['ucode'];
            $stadd['c_ysmoney'] = $moneyinfo['zmoney'];
            $stadd['c_ysdrmoney'] = $moneyinfo['drmoney'];
            $stadd['c_ysstmoney'] = $moneyinfo['stmoney'];
            $stadd['c_money'] = M('Users')->where(array('c_ucode'=>$where['c_ucode']))->getField('c_money');
            $stadd['c_stmoney'] = $stmoney;
            $stadd['c_updatetime'] = gdtime();
            $result = M('Users_yesmoney')->add($stadd);
        }

        if (!$result) {
            return Message(3000,'同步失败');
        }

        return Message(0,'操作成功');
    }

    //查询银盛金额并代付金额
    function dfYesmoney($yseinfo)
    {
        Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();

        $parr['partner_id'] = "wld17375717292";// 合作商户号
        $parr['merchant_usercode'] = $yseinfo['c_username'];  //商户账号
        $data = $pay->query_money($parr);
        $result = $pay->curl_query_money($data);
        $data1 = $result['ysepay_merchant_balance_query_response'];
        if (!empty($data1)) {
            $moneyinfo['zmoney'] = $data1['account_total_amount'];
            $moneyinfo['drmoney'] = $data1['account_detail']['0']['account_amount'];
            $moneyinfo['stmoney'] = $data1['account_detail']['1']['account_amount'];

            //获取用户当前金额
            $nowmoney = M('Users')->where(array('c_ucode'=>$yseinfo['c_ucode']))->getField('c_money');            

            $where['c_ucode'] = $yseinfo['c_ucode'];
            $stmoneyinfo = M('Users_yesmoney')->where($where)->find();
            if ($stmoneyinfo) {
                $ysmsave['c_ysmoney'] = $moneyinfo['zmoney'];
                $ysmsave['c_ysdrmoney'] = $moneyinfo['drmoney'];
                $ysmsave['c_ysstmoney'] = $moneyinfo['stmoney'];
                $ysmsave['c_money'] = $nowmoney;
                $ysmsave['c_updatetime'] = gdtime();
                $result = M('Users_yesmoney')->where($where)->save($ysmsave);   
            } else {
                $stadd['c_ucode'] = $yseinfo['c_ucode'];
                $stadd['c_ysmoney'] = $moneyinfo['zmoney'];
                $stadd['c_ysdrmoney'] = $moneyinfo['drmoney'];
                $stadd['c_ysstmoney'] = $moneyinfo['stmoney'];
                $stadd['c_money'] = $nowmoney;
                $stadd['c_updatetime'] = gdtime();
                $result = M('Users_yesmoney')->add($stadd);
            }

            if (!$result) {
                return Message(3000,'同步金额失败');
            }

            //金额大于0，代付到银盛账户
            $diffmoney = $nowmoney - $moneyinfo['zmoney'];
            if ($diffmoney > 0) {
                //创建代付记录
                $tcode = CreateOrder('uf');
                $data['c_sign'] = 1; //1 代付 2 代扣
                $data['c_type'] = 1; // 1 实时结算 2 按日结算  3 按月结算
                $data['c_ucode'] = $yseinfo['c_ucode'];
                $data['c_orderid'] = CreateOrder('f');
                $data['c_tcode'] = $tcode;
                $data['c_money'] = $diffmoney;
                $data['c_source'] = 20; //平台代付充值
                $data['c_desc'] = '小蜜平台代付充值金额';
                $data['c_addtime'] = date('Y-m-d H:i:s');
                $data['c_status'] = 1;
                $data['c_settled'] = 1;
                $data['c_settledtime'] = gdtime();
                $data['c_updatetime'] = gdtime();
                $result = M('Users_order_splitting')->add($data);  //添加记录
                if ($result) {
                    //代付虚拟账户
                    $parrf['notify_url'] = GetHost(1).'/index.php/Order/Backorder/paynotify_Split';
                    $parrf['proxy_password'] = $pay->ECBEncrypt('xm2017wld1q2w3e', 'wld17375');
                    $parrf['out_trade_no'] = $tcode;
                    $parrf['total_amount'] = $diffmoney;
                    $parrf['subject'] = '小蜜平台代付充值金额';
                    $parrf['merchant_usercode']='wld17375717292';        //扣款方
                    $parrf['payee_user_code'] = $yseinfo['c_username'];  //收款方
                    $parrf['payee_cust_name'] = $yseinfo['c_person'];
                    $result = $pay->curl_inner_df($pay->get_inner_df($parrf));
                    // $result = objarray_to_array(json_decode($result));
                    $trade_status = $result['ysepay_df_single_quick_inner_accept_response']['trade_status'];
                    if ($trade_status == 'TRADE_ACCEPT_SUCCESS' || $trade_status == 'TRADE_SUCCESS') {   //交易受理成功
                        return Message(0,'操作受理成功');
                    } else {
                        return Message(3002,'操作受理失败');
                    }
                }
            }

            return Message(0,'金额操作成功');
        } else {
            return Message(3001,'账户不存在');
        }
    }

    /**
    * 同步用户银盛余额并返回查询数据
    * @param ucode
    */
    function GetSyncYesMoney($parr)
    {
        Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();

        //查询开户情况
        $yw['c_ucode'] = $parr['ucode'];
        $yw['c_openaccount'] = 1;
        $field = 'c_ucode,c_openaccount,c_reason,c_person,c_personphone,c_username';
        $yseinfo = M('User_yspay')->where($yw)->field($field)->find(); 
        if (!$yseinfo) {
            return Message(3000,'开户记录不存在');
        } 

        $db = M('');
        $db->startTrans();

        $parr['partner_id'] = "wld17375717292";// 合作商户号
        $parr['merchant_usercode'] = $yseinfo['c_username'];  //商户账号
        $data = $pay->query_money($parr);
        $result = $pay->curl_query_money($data);
        $data1 = $result['ysepay_merchant_balance_query_response'];
        if (empty($data1)) {
            $moneyinfo['zmoney'] = '0.00';
            $moneyinfo['drmoney'] = '0.00';
            $moneyinfo['stmoney'] = '0.00';
        } else {
            $moneyinfo['zmoney'] = $data1['account_total_amount'];
            $moneyinfo['drmoney'] = $data1['account_detail']['0']['account_amount'];
            $moneyinfo['stmoney'] = $data1['account_detail']['1']['account_amount'];
        }

        $where['c_ucode'] = $parr['ucode'];
        $stmoneyinfo = M('Users_yesmoney')->where($where)->find();
        if ($stmoneyinfo) {
            $ysmsave['c_ysmoney'] = $moneyinfo['zmoney'];
            $ysmsave['c_ysdrmoney'] = $moneyinfo['drmoney'];
            $ysmsave['c_ysstmoney'] = $moneyinfo['stmoney'];
            $ysmsave['c_money'] = M('Users')->where(array('c_ucode'=>$where['c_ucode']))->getField('c_money');
            $ysmsave['c_updatetime'] = gdtime();
            $result = M('Users_yesmoney')->where($where)->save($ysmsave);   
        } else {
            $stadd['c_ucode'] = $parr['ucode'];
            $stadd['c_ysmoney'] = $moneyinfo['zmoney'];
            $stadd['c_ysdrmoney'] = $moneyinfo['drmoney'];
            $stadd['c_ysstmoney'] = $moneyinfo['stmoney'];
            $stadd['c_money'] = M('Users')->where(array('c_ucode'=>$where['c_ucode']))->getField('c_money');
            $stadd['c_stmoney'] = $stmoney;
            $stadd['c_updatetime'] = gdtime();
            $result = M('Users_yesmoney')->add($stadd);
        }

        $apdata =  M('Users_yesmoney')->where($where)->find();
        $db->commit();
        return MessageInfo(0,'操作成功',$apdata);
    }
}
