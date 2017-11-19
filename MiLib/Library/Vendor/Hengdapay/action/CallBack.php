<?php
namespace com\cskj\pay\demo\action;

use com\cskj\pay\demo\common\ConfigUtil;
use com\cskj\pay\demo\common\TDESUtil;
use com\cskj\pay\demo\common\SignUtil;
use com\cskj\pay\demo\common\RSAUtils;
include '../common/ConfigUtil.php';
include '../common/TDESUtil.php';
include '../common/SignUtil.php';

class CallBack{
	public function execute(){
	    $param;

		/*
		$desKey = ConfigUtil::get_val_by_key("desKey");
		
		$keys = base64_decode($desKey);
		
		if($_GET["returnCode"] != null && $_GET["returnCode"]!=""){
			$param["returnCode"]=TDESUtil::decrypt4HexStr($keys, $_GET["returnCode"]);
		}
		echo $_GET["returnCode"];
		if($_GET["resultCode"] != null && $_GET["resultCode"]!=""){
			$param["resultCode"]=TDESUtil::decrypt4HexStr($keys, $_GET["resultCode"]);
		}
		if($_GET["sign"] != null && $_GET["sign"]!=""){
			$param["sign"]=TDESUtil::decrypt4HexStr($keys, $_GET["sign"]);
		}
		if($_GET["status"] != null && $_GET["status"]!=""){
			$param["status"]=TDESUtil::decrypt4HexStr($keys, $_GET["status"]);
		}
		if($_GET["channel"] != null && $_GET["channel"]!=""){
			$param["channel"]=TDESUtil::decrypt4HexStr($keys, $_GET["channel"]);
		}
		if($_GET["body"] != null && $_GET["body"]!=""){
			$param["body"]=TDESUtil::decrypt4HexStr($keys, $_GET["body"]);
		}
		if($_GET["outTradeNo"] != null && $_GET["outTradeNo"]!=""){
			$param["outTradeNo"]=TDESUtil::decrypt4HexStr($keys, $_GET["outTradeNo"]);
		}
		if($_GET["amount"] != null && $_GET["amount"]!=""){
			$param["amount"]=TDESUtil::decrypt4HexStr($keys, $_GET["amount"]);
		}
		if($_GET["currency"] != null && $_GET["currency"]!=""){
			$param["currency"]=TDESUtil::decrypt4HexStr($keys, $_GET["currency"]);
		}
		if($_GET["transTime"] != null && $_GET["transTime"]!=""){
			$param["transTime"]=TDESUtil::decrypt4HexStr($keys, $_GET["transTime"]);
		}
		if($_GET["payChannelType"] != null && $_GET["payChannelType"]!=""){
			$param["payChannelType"]=TDESUtil::decrypt4HexStr($keys, $_GET["payChannelType"]);
		}
		echo $param;*/
		$sign =  $_GET["sign"];
		
		//-------------------
		$unSignKeyList = array ("sign");
		 
		//echo  $_GET["currency"];
		// 		$desKey = ConfigUtil::get_val_by_key("desKey");
       $param=json_encode(file_get_contents('php://input'));
	   $sign = $param['sign'];//SignUtil::signMD5($param, $unSignKeyList);
		//echo $param;
        //echo $param;
        $respJson=json_decode(json_decode($param,true),true);
		//echo gettype($respJson);
 		//echo $respJson;
 		$sign = $respJson['sign'];
		echo "====";
		echo $sign;
        $respSign = SignUtil::signMD5($respJson, $unSignKeyList);
		//echo "respSign=";
		echo $respSign;
		
		if($sign!=$respSign){
			echo "验证签名失败！";
		}else{
			echo "验证签名成功！";
			$_SESSION["tradeResultRes"]=$param;
			header("location:../page/success.php");
		}
		
	}
	
	
}
error_reporting(0);
$m = new CallBack();
$m->execute();
?>