<?php

namespace Test\Controller;

use Base\Controller\BaseController;

/**
 * 商圈模块
 */
class TestController extends BaseController{

	public function _initialize(){
		// header("Content-Type: text/html; charset=UTF-8");
		vendor('Ysepay.ysepay_service');
	}

	

	public function index(){
		$parr['ucode'] = 'wldcd609d5a390b34b4';
		$result = IGD('Pinshouqi','Activity')->PinRun($parr);
		
	}

	public function Test(){

		$result = $this->index();
		dump($result);
	}


	public function daifu(){
		$obj_ysepay_service = new \ysepay_service();

		$return = $obj_ysepay_service->S1032_ysepay();
		$data = $return['data'];
		$uri = $obj_ysepay_service->param['xmlbackmsg_url'];

		if ($return['success'] == 1)
		{
			//发送请求
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $uri );
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt($ch, CURLOPT_TIMEOUT,60);
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
			$return = curl_exec ( $ch );
			curl_close ( $ch );
			
			//处理回执
			if (strlen($return)<=313) {
			 	print_r("银盛支付回执信息不完整".$return);
			}
			$src=substr($return,0,20);
			$msgCode=substr($return,20,5);
			$msgid=substr($return,25,32);
			$check=substr($return,57,256);
			$msg=substr($return,313);
			$unsign=$obj_ysepay_service->unsign_crypt(array("check"=>trim($check),"msg"=>trim($msg)));

			$file = "data/agentpay.txt";
	        /* 验证签名 仅作基础验证*/
	        file_put_contents($file,$unsign."#  failure",FILE_APPEND);

			if(trim($msgCode)==="R1032"){
				ereg("<RefundOrderId>(.*)</RefundOrderId>",$unsign['data'],$RefundOrderId);
				file_put_contents("Response/R3107/"."S3107_".date(YmdHis)."_".$RefundOrderId[1].".txt",$unsign['data']);
				ereg("<Code>(.*)</Code>",$unsign['data'],$Code);
				ereg("<Note>(.*)</Note>",$unsign['data'],$Note);
				ereg("<RefundAmount>(.*)</RefundAmount>",$unsign['data'],$RefundAmount);
				ereg("<TradeSN>(.*)</TradeSN>",$unsign['data'],$TradeSN);
				if($Code[1]==="0000")
				//退款成功，处理自己的业务..
				print_r("状态：".iconv("GBK","UTF-8//IGNORE",$Note[1])." 订单号：".$RefundOrderId[1]." 交易流水：".$TradeSN[1]." 金额：".$RefundAmount[1]);
				else 
				//出现异常，处理自己的业务..
				print_r("返回码：".$Code[1]." 返回码说明：".iconv("GBK","UTF-8//IGNORE",$Note[1]));
			}else if(trim($msgCode)==="R9001"){//报文校验不合法
				var_dump($check);
				echo "111";
				
			}
		}
		else
		echo "result: ", $return['success'] ,$return['url'] ,$return['msg'];
	}


}