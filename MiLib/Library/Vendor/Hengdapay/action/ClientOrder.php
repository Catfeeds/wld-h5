<?php

use com\cskj\pay\demo\common\ConfigUtil;
use com\cskj\pay\demo\common\HttpUtils;
use com\cskj\pay\demo\common\SignUtil;
use com\cskj\pay\demo\common\TDESUtil;
include '../common/ConfigUtil.php';
include '../common/SignUtil.php';
include '../common/HttpUtils.php';
include '../common/TDESUtil.php';
class ClientOrder{
	public function execute($param){
		$unSignKeyList = array ("sign");

		//echo  $_POST["currency"];
// 		$desKey = ConfigUtil::get_val_by_key("desKey");
		$sign = SignUtil::signMD5($param, $unSignKeyList);
		$param["sign"] = $sign;
		$jsonStr=json_encode($param);
		echo $jsonStr;
		$serverPayUrl=ConfigUtil::get_val_by_key("serverPayUrl");

		$httputil = new HttpUtils();
		list ( $return_code, $return_content )  = $httputil->http_post_data($serverPayUrl, $jsonStr);
		echo $return_content;
		$respJson=json_decode($return_content);
		echo $respJson;
		$respSign = SignUtil::signMD5($respJson, $unSignKeyList);
		echo $respSign;
		
		if($respSign !=  $respJson['sign']){
			echo '验签失败！';
		}else{
			if($responseJson['returnCode'] == '0' && $responseJson['resultCode'] == '0'){
				echo $responseJson['payCode'];
			}else{
				echo $return_content;
			}
			
		}
	
	}
	
}

?>