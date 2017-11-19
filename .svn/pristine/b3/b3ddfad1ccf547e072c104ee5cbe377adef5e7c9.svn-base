<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\AuthController;

/**
 * 异常订单
 */
class BackorderController extends AuthController {
	
	//查询订单订单是否支付成功
	public function ScanOrderInfo()
	{
		$orderid = I('oid');
		$mch_id = I('mid');
		$transaction_id = I('tid');		

		// $mch_id = array('299590007230','299590008034','199560304742');
		// foreach ($mch_id as $key => $value) {
			// $askdata['mch_id'] = $value;	
			$askdata['mch_id'] = $mch_id;
			$askdata['out_trade_no'] = $orderid;
			$askdata['transaction_id'] = $transaction_id;
			$askjson = json_encode($askdata);
			$url = 'https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=queryOrder';
			$result = IGD('Upay','Scanpay')->httpRequst($url,$askjson);
			$this->ajaxReturn($result);
		// }
	}

	//单笔订单支付成功回调
	public function RebackOrder()
	{
		$orderid = I('oid');
		$mch_id = I('mid');
		$transaction_id = I('tid');		

		// $mch_id = array('299590007230','299590008034','199560304742');
		// foreach ($mch_id as $key => $value) {
			// $askdata['mch_id'] = $value;	
			$askdata['mch_id'] = $mch_id;
			$askdata['out_trade_no'] = $orderid;
			$askdata['transaction_id'] = $transaction_id;
			$askjson = json_encode($askdata);
			$url = 'https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=queryOrder';
			$result = IGD('Upay','Scanpay')->httpRequst($url,$askjson);
			$data = $result['data'];
			if ($result['status'] == 200) {
				//查询订单是否支付
				$orderinfo = M('Scanpay')->where(array('c_ncode' => $orderid))->find();
				if ($orderinfo['c_pay_state'] == 1) {
					$this->ajaxReturn(MessageInfo(3000,'该订单已支付',$data));
				} else {					
					if ($data['trade_state'] == 'SUCCESS' || $data['trade_state'] == 'CLOSED') {
						if ($data['trade_type'] == 'pay.alipay.jspay') {	//通过支付宝支付
							$param['payrule'] = 1;
						} else if ($data['trade_type'] == 'pay.weixin.jspay') {	//通过微信支付
							$param['payrule'] = 3;
						}

						$param['orderid'] = $data['out_trade_no'];
				        $param['actualprice'] = $data['total_fee']/100;
				        $param['thirdpartynum'] = $data['out_transaction_id'];
				        $param['upay'] = I('up');
						if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
				            $result = IGD('Agorder', 'Order')->PayOrder($param);
				        } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
				            $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
				        } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
				            $result = IGD('Storeorder', 'Order')->PayOrder($param);
				        } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
				            $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
				        }  else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
				            $result = IGD('Seckill', 'Newact')->PayOrder($param);
				        }  else {   //普通线上订单
				            $result = IGD('Order', 'Order')->PayOrder($param);
				        }
				        $this->ajaxReturn($result);
					}
				}
			}
			$this->ajaxReturn(MessageInfo(3001,'没有相关信息',$data));
		// }
	}

	//批量回调操作
	public function MolOptionBac()
	{
		$where['c_pay_state'] = 0;
		$where['c_addtime'] = array('BETWEEN',array('2017-09-01 05:00:00','2017-09-01 10:00:00'));
		$orderlist = M('Scanpay')->where($where)->order('rand()')->limit(800)->select();
		$errnum = 0;$succnum = 0;$option = 0;
		foreach ($orderlist as $key => $value) {
			$askdata['out_trade_no'] = $value['c_ncode'];
			$askjson = json_encode($askdata);
			$url = 'https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=queryOrder';
			$result = IGD('Upay','Scanpay')->httpRequst($url,$askjson);
			$data = $result['data'];
			if ($result['status'] == 200) {				
				if ($data['trade_state'] == 'SUCCESS') {  //支付成功返回
					$succnum++;
					if ($data['trade_type'] == 'pay.alipay.jspay') {	//通过支付宝支付
						$param['payrule'] = 1;
					} else if ($data['trade_type'] == 'pay.weixin.jspay') {	//通过微信支付
						$param['payrule'] = 3;
					}

					$param['orderid'] = $data['out_trade_no'];
			        $param['actualprice'] = $data['total_fee']/100;
			        $param['thirdpartynum'] = $data['out_transaction_id'];
			        $param['upay'] = I('up');
					if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
			            $result = IGD('Agorder', 'Order')->PayOrder($param);
			        } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
			            $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
			        } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
			            $result = IGD('Storeorder', 'Order')->PayOrder($param);
			        } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
			            $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
			        }  else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
			            $result = IGD('Seckill', 'Newact')->PayOrder($param);
			        }  else {   //普通线上订单
			            $result = IGD('Order', 'Order')->PayOrder($param);
			        }
			        if ($result['code'] == 0) {
			        	$option++;
			        }
				} else {
					$errnum++;
				}
			} else {
				$errnum++;
			}
		}
		
		$reback['errnum'] = '未支付订单笔数：'.$errnum;
		$reback['succnum'] = '支付成功笔数：'.$succnum;
		$reback['option'] = '回调成功笔数：'.$option;
		$this->ajaxReturn(MessageInfo(0,'操作成功',$reback));
	}

	//订单支付回调操作
    public function testhd()
    {
        $param['orderid'] = I('oid');
        $param['payrule'] = I('pr');
        $param['actualprice'] = I('fe');
        $param['thirdpartynum'] = I('toid');
        $param['upay'] = I('up');
        if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
            $result = IGD('Agorder', 'Order')->PayOrder($param);
        } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
            $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
        } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
            $result = IGD('Storeorder', 'Order')->PayOrder($param);
        } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
            $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
        }  else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
            $result = IGD('Seckill', 'Newact')->PayOrder($param);
        }  else {   //普通线上订单
            $result = IGD('Order', 'Order')->PayOrder($param);
        }
        $this->ajaxReturn($result);
    }

    //订单退款接口
	public function RefundOrder()
	{
		$orderid = I('oid');
		$mch_id = I('mid');
		$transaction_id = I('tid');
		$refund_fee = I('rfe');	
		$total_fee = I('fe');	
	
		$askdata['refund_fee'] = $refund_fee;
		$askdata['total_fee'] = $total_fee;
		$askdata['mch_id'] = $mch_id;
		$askdata['out_trade_no'] = $orderid;
		$askdata['transaction_id'] = $transaction_id;
		$askjson = json_encode($askdata);
		$url = 'https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiRefund';
		$result = IGD('Upay','Scanpay')->httpRequst($url,$askjson);
		$this->ajaxReturn($result);
	}
	
	//代付金额异步回调
	public function notify_Split()
	{
		Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $params = $_POST;
        unset($params['sign']);
        ksort($params);
        $url = "";
        foreach ($params as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
         /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            if($params['trade_status'] == "TRADE_SUCCESS"){
            	$db = M('');
        		$db->startTrans();

                $w['c_tcode'] = $params['out_trade_no'];
	       		$save_data['c_trade_no'] = $params['trade_no'];
                $save_data['c_status'] = 1;
                $save_data['c_settledtime'] = gdtime();
	       		$result = M('Users_order_splitting')->where($w)->save($save_data);
	       		if (!$result) {
	       			$db->rollback();
	       			echo "error";exit();
	       		}

	       		$info = M('Users_order_splitting')->where($w)->field('*,sum(c_money) as tpmoney')->find();
	       		$parr['ucode'] = $info['c_ucode'];
	       		$parr['money'] = $info['tpmoney'];
	       		$result = IGD('Balance','User')->SyncYesMoney($parr);
	       		if ($result['code'] != 0) {
	       			$db->rollback();
	       			echo "error";exit();
	       		}

	       		$db->commit();
	       		echo "success";exit();
            }else{
            	$w['c_tcode'] = $params['out_trade_no'];
	       		$save_data['c_status'] = 0;
                $save_data['c_remarks'] = $params['trade_status_description'];
	       		$result = M('Users_order_splitting')->where($w)->save($save_data);
                echo "error";exit();
            }
        } else {
            if (!empty($params['out_trade_no'])) {
                $w['c_tcode'] = $params['out_trade_no'];
                $save_data['c_status'] = 0;
                $save_data['c_remarks'] = '支付回调出错';
                $result = M('Users_order_splitting')->where($w)->save($save_data);
            }
            echo "error";exit();
        }
	}

	//代扣金额异步回调
	public function kou_notify_Split()
	{
		Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $params = $_POST;
        unset($params['sign']);
        ksort($params);
        $url = "";
        foreach ($params as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
         /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            if($params['trade_status'] == "TRADE_SUCCESS"){
            	$db = M('');
        		$db->startTrans();

                $w['c_tcode'] = $params['out_trade_no'];
	       		$save_data['c_trade_no'] = $params['trade_no'];
                $save_data['c_status'] = 1;
	       		$result = M('Users_order_splitting')->where($w)->save($save_data);
	       		if (!$result) {
	       			$db->rollback();
	       			echo "error";exit();
	       		}

	       		$info = M('Users_order_splitting')->where($w)->field('*,sum(c_money) as tpmoney')->find();
	       		$parr['ucode'] = $info['c_ucode'];
	       		$parr['money'] = $info['tpmoney'];
	       		$result = IGD('Balance','User')->SyncYesMoney($parr);
	       		if ($result['code'] != 0) {
	       			$db->rollback();
	       			echo "error";exit();
	       		}

	       		$db->commit();
	       		echo "success";exit();
            }else{
            	$w['c_tcode'] = $params['out_trade_no'];
	       		$save_data['c_status'] = 0;
                $save_data['c_remarks'] = $params['trade_status_description'];
	       		$result = M('Users_order_splitting')->where($w)->save($save_data);
                echo "error";exit();
            }
        } else {
            if (!empty($params['out_trade_no'])) {
                $w['c_tcode'] = $params['out_trade_no'];
                $save_data['c_status'] = 0;
                $save_data['c_remarks'] = '支付回调出错';
                $result = M('Users_order_splitting')->where($w)->save($save_data);
            }
            echo "error";exit();
        }
	}

	//查询订单状态并回调订单成功
	public function ChangeOrderPay()
	{
		$orderid = I('orderid');
		$payrule = I('payrule');

		if (empty($orderid) || empty($payrule)) {
			$this->ajaxReturn(Message(3000,'查询参数异常'));
		}
		$parr['orderid'] = $orderid;
        $parr['payrule'] = $payrule;
        $result = IGD('Ysepay','Scanpay')->ChangeOrderPay($parr);
        $this->ajaxReturn($result);
	}

	//手动触发用户提现
	public function GetYspayData(){
		$txcode = I('txcode');
		if (empty($txcode)) {
			$this->ajaxReturn(Message(3000,'查询参数异常'));
		}

		$w['c_tx_code'] = $txcode;
        $w['c_state'] = 0;
        $drawing_info = M('Users_drawing')->where($w)->find();
        if (!$drawing_info) {
        	$this->ajaxReturn(Message(3002,'提现记录不存在'));
        }

        $parr['ucode'] = $drawing_info['c_ucode'];
        $result = IGD('Ysepay','Scanpay')->GetYsedata($parr);
        if ($result['code'] == 0) {
            $result = IGD('Settlement','User')->GetYspayData($result['data'],$txcode);
            $this->ajaxReturn($result);
        } else {
        	$this->ajaxReturn(Message(3001,'账户不存在，无法快速提现'));
        }
	}

	//代付充值异步回调
	public function paynotify_Split()
	{
		Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $params = $_POST;
        unset($params['sign']);
        ksort($params);
        $url = "";
        foreach ($params as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');

        $w['c_tcode'] = $params['out_trade_no'];
        $dfinfo = M('Users_order_splitting')->where($w)->find();
         /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            if($params['trade_status'] == "TRADE_SUCCESS"){
            	$db = M('');
        		$db->startTrans();

                $w['c_tcode'] = $params['out_trade_no'];
	       		$save_data['c_trade_no'] = $params['trade_no'];
                $save_data['c_status'] = 1;
                $save_data['c_settledtime'] = gdtime();
	       		$result = M('Users_order_splitting')->where($w)->save($save_data);
	       		if (!$result) {
	       			$db->rollback();
	       			echo "error";exit();
	       		}

	       		//发送开户成功短信
	       		//发送短信通知
	            $phone = M('Users')->where(array('c_ucode'=>$dfinfo['c_ucode']))->getField('c_phone');
	            $content1 = "【微领地小蜜】尊敬的小蜜商家，您在小蜜系统中的商家信息已通过认证，已为您切换新的支付渠道,感谢您的配合！";
	            $returndata = $this->SendWarningMsgyspay($phone,$content1);

	            if (!empty($dfinfo['c_ucode'])) {
	            	//通过请求发送消息
		            $content = '您在小蜜系统中的商家信息已通过认证，已为您切换新的支付渠道,感谢您的配合！';
		            $Msgcentre = IGD('Msgcentre', 'Message');
		            $msgdata['ucode'] = $dfinfo['c_ucode'];
		            $msgdata['type'] = 0;
		            $msgdata['platform'] = 1;
		            $msgdata['sendnum'] = 1;
		            $msgdata['title'] = '系统消息';
		            $msgdata['content'] =  $content;
		            $msgdata['tag'] = 10000;
		            $msgdata['tagvalue'] = '';
		            $msgdata['weburl'] = '';
		            $Msgcentre->CreateMessege($msgdata);
		        }

	            //同步银盛余额
	            $baparr['ucode'] = $dfinfo['c_ucode'];
	            IGD('Balance','User')->SyncYesMoney($baparr);
	       		$db->commit();
	       		echo "success";exit();
            }
        }

        //金额转入失败返回开户状态        
        $save['c_openaccount'] = 0;
		$save['c_updatetime'] = date('Y-m-d H:i:s');
		$ysw['c_ucode'] = $dfinfo['c_ucode'];
    	$result = M('User_yspay')->where($ysw)->save($save);
        echo "error";exit();
	}

	//发送短信
    public function SendWarningMsgyspay($phone,$content){
        $parr['telephone'] = $phone;
        $parr['type'] = 1000;
        $parr['userid'] = C('TEl_USER');
        $parr['account'] = C('TEl_ACCESS');
        $parr['password'] = C('TEl_PASSWORD');
        $parr['content'] = $content;

        $returndata = IGD('Sendmsg', 'Login')->SendVerify($parr);

        return $returndata;
    }

    //银盛金额监控
    function Moneymonitoring()
    {
    	Vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();

        $parr['partner_id'] = "wld17375717292";// 合作商户号
        $parr['merchant_usercode'] = "wld17375717292";  //商户账号
        $data = $pay->query_money($parr);
        $result = $pay->curl_query_money($data);
        $data1 = $result['ysepay_merchant_balance_query_response'];
      	
      	dump($data1);
        $zmoney = $data1['account_total_amount'];
        $drmoney = $data1['account_detail']['0']['account_amount'];
        $moneyinfo['stmoney'] = $data1['account_detail']['1']['account_amount'];
    	
    	//发送短信
    	if ($zmoney < '100000') {
    		$phone = '15573005460';
            $content1 = "【微领地小蜜】银盛账户金额预警，银盛账户金额：".$zmoney."！";
            $returndata = $this->SendWarningMsgyspay($phone,$content1);
    	}
    }
}

?>