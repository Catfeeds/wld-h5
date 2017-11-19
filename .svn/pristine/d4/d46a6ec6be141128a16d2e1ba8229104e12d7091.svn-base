<?php

/* 短信验证
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SubmitMsg {

    private $_config = array();

    public function __construct() {
        //生成配置参数
    }

    //发送短信
    public function SendMsg($sentinfo) {
      	//创蓝接口参数
		$postArr = array (
				          'un' => 'N4342851',
				          'pw' => 'i2ZeJufRv4a816',
				          'msg' => $sentinfo['content'],
				          'phone' => $sentinfo['mobile'],
				          'rd' => 1
                     );
		
		$url='http://sms.253.com/msg/send';
		
		$postFields = http_build_query($postArr);
		
		
		$ch = curl_init();
		$this_header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
		curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
		
    }

    public function FutureSendMsg($parr){
		header("Content-Type:text/html;charset=utf-8");
		// 发送短信地址，以下为示例地址，具体地址询问网关获取
		$url_send_sms = "http://43.243.130.33:8860/sendSms";
		// 用户账号，必填
		$cust_code = "300406";
		// 用户密码，必填
		$cust_pwd = "OT7GDBIZ27";
		// 短信内容，必填
		$content = $parr['content'];
		// 接收号码，必填，同时发送给多个号码时,号码之间用英文半角逗号分隔
		$destMobiles = $parr['mobile'];
		// 业务标识，选填，由客户自行填写不超过20位的数字
		$uid = "";
		// 长号码，选填
		$sp_code = "";
		// 是否需要状态报告
		$need_report = "yes";
		// 签名，签名内容根据 “短信内容+客户密码”进行MD5编码后获得
		$sign = $content.$cust_pwd;
		$sign = md5($sign);
		$ch = curl_init();
		/* 设置验证方式 */
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','charset=utf-8'));
		/* 设置返回结果为流 */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		/* 设置超时时间*/
		curl_setopt($ch, CURLOPT_TIMEOUT, 300);
		/* 设置通信方式 */
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// 发送短信
		$data=array('cust_code'=>$cust_code,'sp_code'=>$sp_code,'content'=>$content,'destMobiles'=>$destMobiles,'uid'=>$uid,'need_report'=>$need_report,'sign'=>$sign);
		$json_data = json_encode($data);

		curl_setopt ($ch, CURLOPT_URL, $url_send_sms);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		$result = curl_exec($ch);
		curl_close($ch);

		$resultarr = objarray_to_array(json_decode($result))['result'];

		foreach ($resultarr as $key => $value) {
			if($value['code'] != 0){
				$log_str = "本次短信发送失败,msgid：".$value['msgid']."|手机号码：".$value['mobile']."|失败原因：【".$value['msg']."】,发送时间：".Date('Y-m-d H:i:s');
				\Think\Log::record($log_str);	
			}		
		}

		if($resultarr[0]['code'] == 0){
			return Message(0,"发送成功！");
		}else{
			return MessageInfo(0,"发送失败！",$resultarr[0]['msg']);
		}
	}

}
?>

