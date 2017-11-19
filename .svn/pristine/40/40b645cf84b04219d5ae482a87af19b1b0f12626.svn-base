<?php
namespace Home\Controller;
use Think\Controller;

class AutoController extends Controller {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

	//自动取消订单
	public function cancel_order(){
		//查询符合条件的订单信息
		$db = M('');

		$sql = "SELECT c_orderid FROM t_order where c_order_state=2 and c_pay_state=0  and (c_activity_id IS NULL OR c_activity_id='') and datediff(NOW(),c_addtime)>1 LIMIT 5";
		$data = $db->query($sql);

		if(count($data) !== 0){
			foreach ($data as $k => $v) {
				$panrn['orderid'] = $data[$k]['c_orderid'];
				$res = IGD('Order','Order')->CancelOrder($panrn);
			}

			$log_str = "本次后台操作自动取消订单，订单号分别是：".$data[0]['c_orderid']."|".$data[1]['c_orderid']."|".$data[2]['c_orderid']."|".$data[3]['c_orderid']."|".$data[4]['c_orderid'].",操作时间：".Date('Y-m-d H:i:s');
			\Think\Log::record($log_str);
		}

		//取消扫码订单
		$nowtm = date('Y-m-d H:i:s',strtotime('-1 days'));
		$sql1 = "SELECT c_ncode,c_ucode FROM t_scanpay where c_pay_state=0 and c_addtime<='$nowtm' and c_actual_price>'0' LIMIT 5";
		$data1 = $db->query($sql1);
		foreach ($data1 as $key => $value) {
			$parrs['ncode'] = $value['c_ncode'];
	        $parrs['ucode'] = $value['c_ucode'];
	        $result = IGD('Scanpay', 'Scanpay')->CancelOrder($parrs);

	        $log_str = "本次后台操作自动取消扫码订单，订单号分别是：".$value['c_ncode'].",操作时间：".Date('Y-m-d H:i:s');
			\Think\Log::record($log_str);
		}
	}

	//自动确认订单
	public function confirm_order(){
		//查询符合条件的订单信息
		$db = M('');

		$sql = "SELECT c_orderid FROM t_order where c_order_state=2 and c_pay_state=1 and c_deliverystate=2 and datediff(NOW(),c_deliverytime)>7 LIMIT 5";
		$data = $db->query($sql);

		if(count($data) !== 0){
			foreach ($data as $k => $v) {
				$panrn['orderid'] = $data[$k]['c_orderid'];
				$res = IGD('Order','Order')->Confirmorder($panrn);
			}

			$log_str = "本次后台操作自动确定订单，订单号分别是：".$data[0]['c_orderid']."|".$data[1]['c_orderid']."|".$data[2]['c_orderid']."|".$data[3]['c_orderid']."|".$data[4]['c_orderid'].",操作时间：".Date('Y-m-d H:i:s');
			\Think\Log::record($log_str);
		}
	}

	//定时器查询受理结果
	public function CheckDrawing1()
	{
		vendor('TongLianPay.libs.ArrayXml');
		vendor('TongLianPay.libs.cURL');
		vendor('TongLianPay.libs.PhpTools');
		// $mchid = '200551000014254';//微领地商户
        // $mchid = '200551000014294';//湖南腾茂商商户
        // $mchid = '200551000014296';//湖南腾十月商户
        $mchid = '200551000014314';//长沙三月三百货贸易商户
		$tools = new \PhpTools($mchid);

		$where['c_state'] = 0;
		// $where['c_remarks'] = 'tlpay';
		$where['c_tx_code'] = I('tx_code');
		// $where['c_addtime'] = array('LT',date('Y-m-d H:i:s',strtotime('-120 seconds')));
		$drawinglist = M('Users_drawing')->where($where)->limit(100)->order('c_id asc')->select();
		$num = 0;$pnum = 0;
		foreach ($drawinglist as $key => $value) {
			if (!empty($value['c_thirdparty_code'])) {
				$thirdparty_code = $value['c_thirdparty_code'];
				//查询受理结果
				$params = array(
				    'INFO' => array(
				        'TRX_CODE' => '200004',
				        'VERSION' => '03',
				        'DATA_TYPE' => '2',
				        'LEVEL' => '5',
	                	'USER_NAME' => $mchid.'04',
		            	'USER_PASS' => '111111',
				        'REQ_SN' => $thirdparty_code,
				    ),
				    'QTRANSREQ' => array(
				        'QUERY_SN' => $thirdparty_code,
	                    'MERCHANT_ID' => $mchid,
				        'STATUS' => '2',
				        'TYPE' => '1',
				        'START_DAY' => '',
				        'END_DAY' => ''
				    ), 
				); 
				$result = $tools->send($params);
				$data = $result['AIPG']['QTRANSRSP']['QTDETAIL'];
				dump($result);
				if (!empty($data)) {
					if ($data['RET_CODE'] != '0000') {  //交易失败
						$w['c_tx_code'] = $value['c_tx_code'];
		                $save_data['c_state'] = 0;
		                $save_data['c_thirdparty_code'] = subtext($data['ERR_MSG'],100);
		                // $save_data['c_updatetime'] = gdtime();
		                $save_data['c_remarks'] = 'tlpay-error';
		                $result = M('Users_drawing')->where($w)->save($save_data);
		                if ($result) {
		                	$num++;
		                }
					} else {
						$w['c_tx_code'] = $value['c_tx_code'];
		                // $save_data['c_updatetime'] = gdtime();
		                $save_data['c_state'] = 1;
		                $save_data['c_remarks'] = 'tlpay-success';
		                $result = M('Users_drawing')->where($w)->save($save_data);
		                if ($result) {
		                	$pnum++;
		                }
					}
				}
			}
		}

		dump(MessageInfo(0,'查询成功','成功笔数:'.$pnum.',失败笔数:'.$num));
	}

	//定时器查询受理结果
	public function CheckDrawing2()
	{
		vendor('TongLianPay.libs.ArrayXml');
		vendor('TongLianPay.libs.cURL');
		vendor('TongLianPay.libs.PhpTools');
		// $mchid = '200551000014254';//微领地商户
        // $mchid = '200551000014294';//湖南腾茂商商户
        $mchid = '200551000014296';//湖南腾十月商户
        // $mchid = '200551000014314';//长沙三月三百货贸易商户
		$tools = new \PhpTools($mchid);

		$where['c_state'] = 1;
		$where['c_remarks'] = 'tlpay';
		// $where['c_updatetime'] = array('LT','2017-10-18 16:06:00');
		$drawinglist = M('Users_drawing')->where($where)->limit(500)->order('c_id asc')->select();
		$num = 0;$pnum = 0;
		foreach ($drawinglist as $key => $value) {
			dump($value['c_thirdparty_code']);
			if (!empty($value['c_thirdparty_code'])) {
				$thirdparty_code = $value['c_thirdparty_code'];
				//查询受理结果
				$params = array(
				    'INFO' => array(
				        'TRX_CODE' => '200004',
				        'VERSION' => '03',
				        'DATA_TYPE' => '2',
				        'LEVEL' => '5',
	                	'USER_NAME' => $mchid.'04',
		            	'USER_PASS' => '111111',
				        'REQ_SN' => $thirdparty_code,
				    ),
				    'QTRANSREQ' => array(
				        'QUERY_SN' => $thirdparty_code,
	                    'MERCHANT_ID' => $mchid,
				        'STATUS' => '2',
				        'TYPE' => '1',
				        'START_DAY' => '',
				        'END_DAY' => ''
				    ), 
				); 
				$result = $tools->send($params);
				$data = $result['AIPG']['QTRANSRSP']['QTDETAIL'];
				dump($result);
				if (!empty($data)) {
					if ($data['RET_CODE'] != '0000') {  //交易失败
						$w['c_tx_code'] = $value['c_tx_code'];
		                $save_data['c_state'] = 0;
		                $save_data['c_thirdparty_code'] = subtext($data['ERR_MSG'],100);
		                // $save_data['c_updatetime'] = gdtime();
		                $save_data['c_remarks'] = 'tlpay-error';
		                $result = M('Users_drawing')->where($w)->save($save_data);
		                if ($result) {
		                	$num++;
		                }
					} else {
						$w['c_tx_code'] = $value['c_tx_code'];
		                // $save_data['c_updatetime'] = gdtime();
		                $save_data['c_remarks'] = 'tlpay-success';
		                $result = M('Users_drawing')->where($w)->save($save_data);
		                if ($result) {
		                	$pnum++;
		                }
					}
				}
			}
		}

		dump(MessageInfo(0,'查询成功','成功笔数:'.$pnum.',失败笔数:'.$num));
	}

	//定时器查询受理结果
	public function CheckDrawing222()
	{
		vendor('TongLianPay.libs.ArrayXml');
		vendor('TongLianPay.libs.cURL');
		vendor('TongLianPay.libs.PhpTools');
		// $mchid = '200551000014254';//微领地商户
        $mchid = '200551000014294';//湖南腾茂商商户
        // $mchid = '200551000014296';//湖南腾十月商户
        // $mchid = '200551000014314';//长沙三月三百货贸易商户
		$tools = new \PhpTools($mchid);

		$where['c_state'] = 1;
		$where['c_remarks'] = 'tlpay';
		$where['c_addtime'] = array('LT',date('Y-m-d H:i:s',strtotime('-20 minutes')));
		$drawinglist = M('Users_drawing')->where($where)->limit(500)->order('c_id asc')->select();
		$num = 0;$pnum = 0;
		foreach ($drawinglist as $key => $value) {
			if (!empty($value['c_thirdparty_code'])) {
				$thirdparty_code = $value['c_thirdparty_code'];
				//查询受理结果
				$params = array(
				    'INFO' => array(
				        'TRX_CODE' => '200004',
				        'VERSION' => '03',
				        'DATA_TYPE' => '2',
				        'LEVEL' => '5',
	                	'USER_NAME' => $mchid.'04',
		            	'USER_PASS' => '111111',
				        'REQ_SN' => $thirdparty_code,
				    ),
				    'QTRANSREQ' => array(
				        'QUERY_SN' => $thirdparty_code,
	                    'MERCHANT_ID' => $mchid,
				        'STATUS' => '2',
				        'TYPE' => '1',
				        'START_DAY' => '',
				        'END_DAY' => ''
				    ), 
				); 
				$result = $tools->send($params);
				$data = $result['AIPG']['QTRANSRSP']['QTDETAIL'];
				if (!empty($data)) {
					if ($data['RET_CODE'] != '0000') {  //交易失败
						$w['c_tx_code'] = $value['c_tx_code'];
		                $save_data['c_state'] = 0;
		                $save_data['c_thirdparty_code'] = subtext($data['ERR_MSG'],100);
		                // $save_data['c_updatetime'] = gdtime();
		                $save_data['c_remarks'] = 'tlpay-error';
		                $result = M('Users_drawing')->where($w)->save($save_data);
		                if ($result) {
		                	$num++;
		                }
					} else {
						$w['c_tx_code'] = $value['c_tx_code'];
		                // $save_data['c_updatetime'] = gdtime();
		                $save_data['c_remarks'] = 'tlpay-success';
		                $result = M('Users_drawing')->where($w)->save($save_data);
		                if ($result) {
		                	$pnum++;
		                }
					}
				}
			}
		}

		$this->ajaxReturn(MessageInfo(0,'查询成功','成功笔数:'.$pnum.',失败笔数:'.$num));
	}

	//定时器调用发送未发送定时消息
	public function Senddsmsg(){
		//查询未发送消息
		$field = "c_id,c_ucode,c_title,c_txcode,c_tag,c_tagvalue,c_platform,c_content,c_timer";
		$where['c_state'] = 0;
        $where['c_timer'] = array('ELT', date('Y-m-d H:i:s'));
        $where['c_istimer'] = 1;

        $message_list = M('Users_msg')->field($field)->where($where)->order('c_id asc')->limit(10)->select();

		$zs = count($message_list);
		$i = 0;
		if($zs > 0){
			foreach ($message_list as $row) {
				$row['c_tagvalue'] = $row['c_id'];
				$result = IGD('JPush','Jpush')->push_notification($row);
				if($result['code'] == 0){
					$i++;
				}
			}

			$log_str = "本次发送消息共".$zs."条，其中成功".$i."条！操作时间：".Date('Y-m-d H:i:s');
			\Think\Log::record($log_str);
		}
	}

	//定时器调用发送未发送消息
	public function Sendmsg(){
		//查询未发送消息
		$sql = "SELECT c_id,c_ucode,c_title,c_txcode,c_tag,c_tagvalue,c_platform,c_content FROM t_users_msg WHERE c_state=0 and c_istimer=0 and c_ucode is not null ORDER BY c_id ASC LIMIT 10";

		$db = M('');
		$message_list = $db->query($sql);

		$zs = count($message_list);
		$i = 0;
		if($zs > 0){
			foreach ($message_list as $row) {
				$row['c_tagvalue'] = $row['c_id'];
				$result = IGD('JPush','Jpush')->push_notification($row);
				if($result['code'] == 0){
					$i++;
				}
			}

			$log_str = "本次发送消息共".$zs."条，其中成功".$i."条！操作时间：".Date('Y-m-d H:i:s');
			\Think\Log::record($log_str);
		}
	}

	//小蜜商城 自动取消订单
	public function supplier_cancel_order(){
		//查询符合条件的订单信息
		$db = M('');

		$sql = "SELECT c_orderid FROM t_supplier_order where c_order_state=2 and c_pay_state=0  and (c_agent_orderid IS NULL OR c_agent_orderid='') and datediff(NOW(),c_addtime)>1 LIMIT 5";
		$data = $db->query($sql);

		if(count($data) !== 0){
			foreach ($data as $k => $v) {
				$panrn['orderid'] = $data[$k]['c_orderid'];
				IGD('Supplyorder','Agorder')->CancelOrder($panrn);
			}

			$log_str = "本次后台操作自动取消小蜜商城订单，订单号分别是：".$data[0]['c_orderid']."|".$data[1]['c_orderid']."|".$data[2]['c_orderid']."|".$data[3]['c_orderid']."|".$data[4]['c_orderid'].",操作时间：".Date('Y-m-d H:i:s');
			\Think\Log::record($log_str);
		}
	}

	//小蜜商城  自动确认订单
	public function supplier_confirm_order(){
		//查询符合条件的订单信息
		$db = M('');

		$sql = "SELECT c_orderid FROM t_supplier_order where c_order_state=2 and c_pay_state=1 and c_deliverystate=2  and (c_agent_orderid IS NULL OR c_agent_orderid='') and datediff(NOW(),c_deliverytime)>7 LIMIT 5";
		$data = $db->query($sql);

		if(count($data) !== 0){
			foreach ($data as $k => $v) {
				$panrn['orderid'] = $data[$k]['c_orderid'];
				IGD('Supplyorder','Agorder')->Confirmorder($panrn);
			}

			$log_str = "本次后台操作自动确定小蜜商城订单，订单号分别是：".$data[0]['c_orderid']."|".$data[1]['c_orderid']."|".$data[2]['c_orderid']."|".$data[3]['c_orderid']."|".$data[4]['c_orderid'].",操作时间：".Date('Y-m-d H:i:s');
			\Think\Log::record($log_str);
		}
	}

	//出现报警给后台管理员发送短信
	public function SendWarningMsg($phone,$content){
		$parr['telephone'] = $phone;
        $parr['type'] = 1000;
        $parr['userid'] = C('TEl_USER');
        $parr['account'] = C('TEl_ACCESS');
        $parr['password'] = C('TEl_PASSWORD');
        $parr['content'] = $content;

        $returndata = IGD('Sendmsg', 'Login')->SendVerify($parr);

        return $returndata;
	}

	//定点查询用户账户是否出现负数
	public function CheckingMinus(){
		//查询用户账户是否出现负数
		$db = M('');

		$sql = "SELECT c_money FROM t_users WHERE c_money<0";
		$data = $db->query($sql);

		if(count($data) !== 2){
			//发送短信信息
			$phone = '15084803903,15573005460';
			$content = "【微领地小蜜】系统后台发出警告：出现用户金额为负数，请及时处理！";
			$returndata = $this->SendWarningMsg($phone,$content);
		}else{
			//测试阶段（后可去掉）   发送短信信息
			$phone = '15084803903,15573005460';
			$content = "【微领地小蜜】后台核对检查用户账户是否出现负数，结果正常！";
			$returndata = $this->SendWarningMsg($phone,$content);
		}

		//填写日志
		if ($returndata['code'] == 0) {
		    $log_str = "本次后台操作定点查询用户账户是否出现负数,短信提示成功,操作时间：".Date('Y-m-d H:i:s');
		}else{
			$log_str = "本次后台操作定点查询用户账户是否出现负数,出现短信发送失败,操作时间：".Date('Y-m-d H:i:s');
		}

		\Think\Log::record($log_str);
	}

	//定点查询系统账目收支总数是否一致
	public function CheckingSum(){
		//定点查询系统账目收支总数是否一致
		$sql = "select sum(case when c_money<0 then c_money else 0 end) as a,
		        sum(case when c_money>0 then c_money else 0 end) as b,
		        sum(case when c_bkmoney>0 then c_bkmoney else 0 end) as bk from t_users_moneylog ";
        $sql1 = "select sum(c_money) as c from t_users ";
        $model = M();
        $data = $model->query($sql);
        $data1 = $model->query($sql1);

        $outcome = $data[0]['a'];
        $income = $data[0]['b'];
        $remain = $data1[0]['c'];
        $bkmoney = $data[0]['bk'];

        $result = sprintf("%.2f", ($remain-$outcome)-($income-$bkmoney));

		if($result != 0){
			//发送短信信息
			$phone = '15573005460';
			$content = "【微领地小蜜】系统后台发出警告：总收入减总支出不等于总余额。相差：".$result."(元)！";
			$returndata = $this->SendWarningMsg($phone,$content);
		}else{
			//测试阶段（后可去掉）   发送短信信息
			$phone = '15573005460';
			$content = "【微领地小蜜】后台核对总收入减总支出是否等于总余额,结果正常！";
			$returndata = $this->SendWarningMsg($phone,$content);
		}

		//填写日志
        if ($returndata['code'] == 0) {
            $log_str = "本次后台操作定点查询总收入减总支出不等于总余额,短信提示成功,操作时间：".Date('Y-m-d H:i:s');
        }else{
        	$log_str = "本次后台操作定点查询总收入减总支出不等于总余额,出现短信发送失败,操作时间：".Date('Y-m-d H:i:s');
        }

        \Think\Log::record($log_str);
	}

	//对比前一日微信账单数据
	public function CheckingWx(){
		//引用类库
		vendor('WxPayPubHelper.WxPayPubHelper');

		//对账单日期
		$time_string = date("Y-m-d",strtotime("-1 day"));
		$time_arr = explode("-",$time_string);
        $bill_date = $time_arr[0].$time_arr[1].$time_arr[2];

        //使用对账单接口
        $downloadBill = new \DownloadBill_pub();
        $downloadBill->setParameter("sub_mch_id", C('SUB_MCHID'));//子商户号
        $downloadBill->setParameter("bill_date","$bill_date");//对账单日期
        $downloadBill->setParameter("bill_type","ALL");//账单类型

        //对账单接口结果
        $downloadBillResult = $downloadBill->getResult();
        //$log为微信返回的对账单结果(string),取出每日交易总额;
        $log = str_replace(","," ",$downloadBill->response);//,号替换成空白
        $log = explode("`",$log);//根据`分割成数组
        $arrnum = count($log)-4;

        $totalmoney = $log[$arrnum];

        //查询系统后台微信账单总
		$db = M('');

		$betime = date("Y-m-d",time() - ( 1  *  24  *  60  *  60 ));

		$sql = "SELECT SUM(c_money) as a FROM t_order_paylog where c_payrule=3 and DATE_FORMAT(c_addtime,'%Y-%m-%d')='$betime'";
		$sql1 = "SELECT SUM(c_money) as b FROM t_supplier_order_paylog where c_payrule=3 and  DATE_FORMAT(c_addtime,'%Y-%m-%d')='$betime'";
		$data = $db->query($sql);
		$data1 = $db->query($sql1);

		$totalmoney1 = $data[0]['a'] + $data1[0]['b'];

		//判断是否准确
		$difference = $totalmoney-$totalmoney1;
		if($difference != 0){
			//发送短信信息
			$phone = '15573005460';
			$content = "【微领地小蜜】系统后台发出警告：微信账单与后台账单出现差值。相差：".$difference."(元)！";
			$returndata = $this->SendWarningMsg($phone,$content);
	    }else{
	    	//测试阶段（后可去掉）   发送短信信息
	    	$phone = '15573005460';
	    	$content = "【微领地小蜜】后台核对微信账单与后台账单是否出现差值,结果正常！";
	    	$returndata = $this->SendWarningMsg($phone,$content);
	    }

	    //填写日志
	    if ($returndata['code'] == 0) {
            $log_str = "本次后台操作定点查询微信账单与后台账单出现差值,短信提示成功,操作时间：".Date('Y-m-d H:i:s');
        }else{
        	$log_str = "本次后台操作定点查询微信账单与后台账单出现差值,出现短信发送失败,操作时间：".Date('Y-m-d H:i:s');
        }

        \Think\Log::record($log_str);
	}

	//对比前一日支付宝账单数据
	public function CheckingAli(){
		//账单类型，商户通过接口或商户经开放平台授权后其所属服务商通过接口可以获取以下账单类型：trade、signcustomer；
		//trade指商户基于支付宝交易收单的业务账单；signcustomer是指基于商户支付宝余额收入及支出等资金变动的帐务账单；
	    $bill_type = 'trade';
	    //账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM。
	    $bill_date = '2017-02-20';

	    $alipay_config = C('alipay_config');

	    $RequestBuilder = new \Com\Alipay\buildermodel\AlipayDataDataserviceBillDownloadurlQueryContentBuilder();
	    $RequestBuilder->setBillType($bill_type);
	    $RequestBuilder->setBillDate($bill_date);
	    $Response = new \Com\Alipay\service\AlipayTradeService($alipay_config);
	    $result=$Response->downloadurlQuery($RequestBuilder);
	    return $result;
	}
}