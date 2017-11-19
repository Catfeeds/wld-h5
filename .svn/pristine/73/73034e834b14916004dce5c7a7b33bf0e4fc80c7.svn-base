<?php
namespace com\cskj\pay\demo\action;
use com\cskj\pay\demo\common\ConfigUtil;
use com\cskj\pay\demo\common\HttpUtils;
use com\cskj\pay\demo\common\SignUtil;
use com\cskj\pay\demo\common\TDESUtil;
include '../common/ConfigUtil.php';
include '../common/SignUtil.php';
include '../common/HttpUtils.php';
include '../common/TDESUtil.php';
class QueryOrder{
	public function execute(){
	

		if($_POST["tradeType"] != null && $_POST["tradeType"]!=""){
		    $param["tradeType"]=$_POST["tradeType"];
		}
		if($_POST["version"] != null && $_POST["version"]!=""){
		    $param["version"]=$_POST["version"];
		}
		if($_POST["mchId"] != null && $_POST["mchId"]!=""){
		    $param["mchId"]=$_POST["mchId"];
		}
		if($_POST["outTradeNo"] != null && $_POST["outTradeNo"]!=""){
		    $param["outTradeNo"]=$_POST["outTradeNo"];
		}
		if($_POST["oriTradeNo"] != null && $_POST["oriTradeNo"]!=""){
		    $param["oriTradeNo"]=$_POST["oriTradeNo"];
		}
		if($_POST["queryType"] != null && $_POST["queryType"]!=""){
		    $param["queryType"]=$_POST["queryType"];
		}
		
		$oriUrl = $_POST["saveUrl"];
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
error_reporting(0);
$m=new QueryOrder();
$m->execute();
?>