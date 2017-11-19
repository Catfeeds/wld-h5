<?php

namespace com\cskj\pay\demo\action;
header("Content-Type:text/html;charset=utf8");
use com\cskj\pay\demo\common\ConfigUtil;
use com\cskj\pay\demo\common\HttpUtils;
use com\cskj\pay\demo\common\SignUtil;
use com\cskj\pay\demo\common\TDESUtil;
include '../common/ConfigUtil.php';
include '../common/SignUtil.php';
include '../common/HttpUtils.php';
include '../common/TDESUtil.php';
class ClientOrder{
	public function execute(){
		$param;

		if($_POST["returnCode"] != null && $_POST["returnCode"]!=""){
		    $param["returnCode"]=$_POST["returnCode"];
		}
		if($_POST["resultCode"] != null && $_POST["resultCode"]!=""){
		    $param["resultCode"]=$_POST["resultCode"];
		}
		if($_POST["sign"] != null && $_POST["sign"]!=""){
		    $param["sign"]=$_POST["sign"];
		}
		if($_POST["status"] != null && $_POST["status"]!=""){
		    $param["status"]=$_POST["status"];
		}
		if($_POST["channel"] != null && $_POST["channel"]!=""){
		    $param["channel"]=$_POST["channel"];
		}
		if($_POST["body"] != null && $_POST["body"]!=""){
		    $param["body"]=$_POST["body"];
		}
		if($_POST["outTradeNo"] != null && $_POST["outTradeNo"]!=""){
		    $param["outTradeNo"]=$_POST["outTradeNo"];
		}
		if($_POST["description"] != null && $_POST["description"]!=""){
		    $param["description"]=$_POST["description"];
		}
		if($_POST["amount"] != null && $_POST["amount"]!=""){
		    $param["amount"]=$_POST["amount"];
		}
		if($_POST["currency"] != null && $_POST["currency"]!=""){
		    $param["currency"]=$_POST["currency"];
		}
		if($_POST["transTime"] != null && $_POST["transTime"]!=""){
		    $param["transTime"]=$_POST["transTime"];
		}
		if($_POST["payChannelType"] != null && $_POST["payChannelType"]!=""){
		    $param["payChannelType"]=$_POST["payChannelType"];
		}
		
echo $param["body"];
		//echo  $_POST["currency"];
// 		$desKey = ConfigUtil::get_val_by_key("desKey");

		$jsonStr=json_encode($param,JSON_UNESCAPED_UNICODE);
		echo $jsonStr;
		$serverPayUrl="http://localhost:86/com/cskj/pay/demo/action/CallBack.php";

		$httputil = new HttpUtils();
		list ( $return_code, $return_content )  = $httputil->http_post_data($serverPayUrl, $jsonStr);
		echo $return_content;
		
	
	
	}
	
}
error_reporting(0);
$m = new ClientOrder();
$m->execute();

?>