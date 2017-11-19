<?php 

require_once("common/ConfigUtil.php");
require_once("common/SignUtil.php");
require_once("common/HttpUtils.php");
require_once("common/TDESUtil.php");

/**
* 支付入口
*/
class Pay {
	public $ConfigUtil;
	public $SignUtil;
	public $HttpUtils;
	public $TDESUtil;

	public $desKey = '22ca253390934577b6df07034da3c513';  //秘钥

	//初始化
	function __construct()
	{
		$this->ConfigUtil = new \com\cskj\pay\demo\common\ConfigUtil();
		$this->HttpUtils = new \com\cskj\pay\demo\common\HttpUtils();
		$this->TDESUtil = new \com\cskj\pay\demo\common\TDESUtil();
		$this->SignUtil = new \com\cskj\pay\demo\common\SignUtil();
	}

	public function createorderinfo($param){
		$unSignKeyList = array("sign");
		$sign = $this->SignUtil->signMD5($param, $unSignKeyList, $this->desKey);
		$param["sign"] = $sign;
		// dump($param);
		$jsonStr = json_encode($param);
		// $serverPayUrl = $this->ConfigUtil->get_val_by_key("serverPayUrl");
		$serverPayUrl = 'https://apihf.onepaypass.com/aps/cloudplatform/api/trade.html';

		list ($return_code, $return_content)  = $this->HttpUtils->http_post_data($serverPayUrl, $jsonStr);
		$respJson = json_decode($return_content);

		return $respJson;
		$respSign = $this->SignUtil->signMD5($respJson, $unSignKeyList, $this->desKey);
		
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