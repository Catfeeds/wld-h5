<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\AuthController;


/**
 * 通联平安支付相关
 */
class TlpayController extends AuthController {

	public function queryorder()
	{
		vendor('Papay.webApp');
		$open_info = C('PAOPEN_INFO');
		// $open_id = $open_info[rand(0,(count($open_info)-1))]['PAOPEN_ID'];
		// $open_key = $open_info[rand(0,(count($open_info)-1))]['PAOPEN_KEY'];

		$open_id = '46a020ffa7db42d6fc1a22f9d0b9fd77';
		$open_key = '8332000c9ac618231e3fe1e4248d3a35';
		$webApp = new \webApp($open_id,$open_key);
		
		$data['out_no'] = 't1506759151';
        $result = $webApp->api("paystatus",$data);
        dump($result);
	}

	public function papay()
	{
		vendor('Papay.webApp');
		// $open_info = C('PAOPEN_INFO');
		// $open_id = $open_info[rand(0,(count($open_info)-1))]['PAOPEN_ID'];
		// $open_key = $open_info[rand(0,(count($open_info)-1))]['PAOPEN_KEY'];
		// $webApp = new \webApp($open_id,$open_key);

		$open_id = '46a020ffa7db42d6fc1a22f9d0b9fd77';
		$open_key = '8332000c9ac618231e3fe1e4248d3a35';
		$webApp = new \webApp($open_id,$open_key);

		$data['open_id'] = '029d15ae0c95ce1c80fa6100abf0259b';   //用户唯一标识
		$data['timestamp'] = time();   //时间戳
		$data['out_no'] = 't'.time(); //订单号
		$data['pmt_tag'] = "Weixin";	//支付标识 Weixin,Alipay2
		$data['pmt_name'] = "微领地小蜜";    //自定义付款方式名 
		$data['ord_name'] = "测试";    //订单描述
		$data['original_amount'] = "2";
		$data['trade_amount'] = "2";   //实际交易金额(分为单位)	

		// $data['discount_amount'] = "1"; 
		// $data['ignore_amount'] = "1"; 
		//$data['trade_account'] = "1"; 
		/* $data['trade_no'] = "123123"; 
		$data['trade_result'] = "test"; 
		$data['remark'] = "test"; 
		$data['auth_code'] = "123009484768733";   
		$data['tag'] = "123"; */
		//$data['jump_url'] = "http://www.xxx.com";
		$data['notify_url'] = GetHost(1).'/index.php/Order/Tlpay/papaynotify';
		$result = $webApp->api("payorder",$data); 


		if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $pmt_tag = 'Weixin';
                $notify_url = GetHost(1).'/index.php/Order/Weixin/papaynotify';
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                $pmt_tag = 'Alipay2';
                $notify_url = GetHost(1).'/index.php/Order/Alipay/papaynotify';
            } else {
                $this->ajaxReturn(Message(2002,'非法支付'));
            }

            $data['open_id'] = $parr['openid'];   //用户唯一标识
            $data['timestamp'] = time();   //时间戳
            $data['out_no'] = $orderid; //订单号
            $data['pmt_tag'] = $pmt_tag;    //支付标识 Weixin,Alipay2
            $data['pmt_name'] = trim($userdata['c_nickname']);    //自定义付款方式名 
            $data['ord_name'] = trim($userdata['c_nickname']) . '的小店线下订单';    //订单描述
            $data['original_amount'] = $tempmoney;
            $data['trade_amount'] = $tempmoney;   //实际交易金额(分为单位)   
            $data['notify_url'] = $notify_url;
            $data['jump_url'] = GetHost(1).'/index.php/Order/Scanpay/success?orderid='.$orderid;
            $result = $webApp->api("payorder",$data); 

            if ($result['errcode'] != 0) {
                $this->ajaxReturn(Message(3002,$result['msg']));
            }

            if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
                $data1 = objarray_to_array(json_decode($result['data']['trade_result']));
                $prepay_id = $data1['prepay_id'];
                $datainfo['appId'] = $data1['appid'];
                $datainfo['timeStamp'] = $result['timestamp'];
                $datainfo['signType'] = 'MD5';
                $datainfo['package'] = "prepay_id=$prepay_id";
                $datainfo['nonceStr'] = $data1['nonce_str'];
                $datainfo['paySign'] = $data1['sign'];
                $datainfo['orderid'] = $orderid;
                $datainfo['jsapi_pay_url'] = $result['data']['jsapi_pay_url'];
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
            }  else if ($payrule == 1) {  //选择支付宝支付生成对应支付参数
                $data1 = objarray_to_array(json_decode($result['data']['trade_result']));
                $datainfo['tradeNO'] = $data1['alipay_trade_precreate_response']['out_trade_no'];
                $datainfo['orderid'] = $orderid;
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
            }            

		dump($result);
	}

	//平安支付回调
	public function papaynotify()
	{
		vendor('Papay.webApp');
		$open_info = C('PAOPEN_INFO');
		// $open_id = $open_info[rand(0,(count($open_info)-1))]['PAOPEN_ID'];
		// $open_key = $open_info[rand(0,(count($open_info)-1))]['PAOPEN_KEY'];

		$open_id = '46a020ffa7db42d6fc1a22f9d0b9fd77';
		$open_key = '8332000c9ac618231e3fe1e4248d3a35';
		$webApp = new \webApp($open_id,$open_key);

		$map = $_GET;
		$result = $webApp->notify($map);
		if($result){			
			//$ord_no = $map['ord_no'];
			//先判断订单号ord_no，存不存在，不存在平台可以调用查询订单接口把订单更多信息插入自己的库中。


			echo 'notify_success';
		}else{
			echo 'check sign error';	
		}
	}

	function pay(){
		if ($payrule == 2 || $payrule == 3) {  //选择微信支付生成对应支付参数
			$paytype = 'W02';
		} else if ($payrule == 1) {
			$paytype = 'A02';
		} else {
			$this->ajaxReturn(Message(3000,'支付方式错误'));
		}
		$params = array();
		$params["cusid"] = C('TLCUSID');
	    $params["appid"] = C('TLAPPID');
	    $params["version"] = C('TLAPIVERSION');
	    $params["trxamt"] = $tempmoney;		//金额，单位为分
	    $params["reqsn"] = $orderid;    //订单号,自行生成
	    $params["paytype"] = $paytype;		//W02：微信JS支付 A02：支付宝JS支付  Q02：QQ钱包JS支付
	    $params["randomstr"] = time();  //随机参数
	    $params["body"] = trim($userdata['c_nickname']) . '的小店线下订单';
	    $params["remark"] = "";
	    $params["acct"] = 'oTCJiuD1lQgZcqeHXMD3B7g640Zo';//$parr['openid'];			 //微信支付宝获取的标识
	    $params["limit_pay"] = "no_credit";  //只对微信支付有效,仅支持no_credit
        $params["notify_url"] = C('TLNOTIFY');
	    $params["sign"] = SignArray($params,C('TLAPPKEY'));//签名
	    
	    $paramsStr = ToUrlParams($params);
	    $url = C('TLAPIURL') . "/pay";
	    $rsp = TLrequest($url, $paramsStr);
	    $rspArray = json_decode($rsp, true); 
	    if(ScvalidSign($rspArray)){		//验签正确,进行业务处理	    	
	    	$data1 = $rspArray['payinfo'];
	    	if ($payrule == 2 || $payrule == 3) {
	            $datainfo['appId'] = $data1['appId'];
	            $datainfo['timeStamp'] = $data1['timeStamp'];
	            $datainfo['signType'] = $data1['signType'];
	            $datainfo['package'] = $data1['package'];
	            $datainfo['nonceStr'] = $data1['nonceStr'];
	            $datainfo['paySign'] = $data1['paySign'];
	            $datainfo['orderid'] = $orderid;
	            $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
	        } else if ($payrule == 1) {
				$datainfo['tradeNO'] = $data1;
                $datainfo['orderid'] = $orderid;
                $this->ajaxReturn(MessageInfo(0, '生成订单成功', $datainfo));
			} 
	    } else {
	    	$this->ajaxReturn(Message(3001,'验证签名失败'));
	    }
	}
	
	//当天交易用撤销
	function cancel(){
		$params = array();
		$params["cusid"] = C('TLCUSID');
	    $params["appid"] = C('TLAPPID');
	    $params["version"] = C('TLAPIVERSION');
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "123456788";
	    $params["oldreqsn"] = "123456";//原来订单号
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = SignArray($params,C('TLAPPKEY'));//签名
	    $paramsStr = ToUrlParams($params);
	    $url = C('TLAPIURL') . "/cancel";
	    $rsp = TLrequest($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(ScvalidSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	
	//当天交易请用撤销,非当天交易才用此退货接口
	function refund(){
		$params = array();
		$params["cusid"] = C('TLCUSID');
	    $params["appid"] = C('TLAPPID');
	    $params["version"] = C('TLAPIVERSION');
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "1234567889";
	    $params["oldreqsn"] = "123456";//原来订单号
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = SignArray($params,C('TLAPPKEY'));//签名
	    $paramsStr = ToUrlParams($params);
	    $url = C('TLAPIURL') . "/refund";
	    $rsp = TLrequest($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(ScvalidSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	
	function query(){
		$params = array();
		$params["cusid"] = C('TLCUSID');
	    $params["appid"] = C('TLAPPID');
	    $params["version"] = C('TLAPIVERSION');
	    $params["reqsn"] = "123456";
	    $params["randomstr"] = time();//
	    $params["sign"] = SignArray($params,C('TLAPPKEY'));//签名
	    $paramsStr = ToUrlParams($params);
	    $url = C('TLAPIURL') . "/query";
	    $rsp = TLrequest($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(ScvalidSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	

    //通联支付回调
    public function CusNotify() {
        $params = array();
        foreach($_POST as $key=>$val) {//动态遍历获取所有收到的参数,此步非常关键,因为收银宝以后可能会加字段,动态获取可以兼容由于收银宝加字段而引起的签名异常
            $params[$key] = $val;
        }
        if(count($params)<1){//如果参数为空,则不进行处理
            echo "error";exit();
        }
        if(ValidSign($params,C('TLAPPKEY'))){//验签成功
            //此处进行业务逻辑处理
            // \Think\Log::record("支付成功了");
            $param['orderid'] = $params["cusorderid"];
            if ($params['trxcode'] == "W02") {   //微信支付
                $param['payrule'] = 3;
            } elseif ($params['trade_type'] == "A02") {  //支付宝支付
                $param['payrule'] = 1;
            } else {
                echo "error";exit();
            }

            $param['actualprice'] = $params['trxamt'] / 100;
            $param['thirdpartynum'] = $params['chnltrxid'];
            $param['upay'] = 1;
            if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
                $result = IGD('Agorder', 'Order')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
                $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
                $result = IGD('Storeorder', 'Order')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
                $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
                $result = IGD('Seckill', 'Newact')->PayOrder($param);
            } else if (substr($param['orderid'],0,1) == 'f') {  //微商服务费订单
                $result = IGD('Agent', 'Order')->PayOrder($param);
            } else {   //普通线上订单
                $result = IGD('Order', 'Order')->PayOrder($param);
            }

            if ($result['code'] != 0) {
                echo "error";exit();
            }
            echo "success";exit();
        } else{
            echo "error";exit();
        }
    }


}
?>

