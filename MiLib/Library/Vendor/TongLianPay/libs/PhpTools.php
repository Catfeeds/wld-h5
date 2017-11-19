<?php 

include_once 'ArrayXml.php';
class PhpTools{
	 
	
	public $certFile;//通联公钥证书
	public $privateKeyFile;//商户私钥证书
	public $password;//商户私钥密码以及用户密码
	// const  apiUrl = 'http://172.16.1.11:8080/aipg/ProcessServlet';//通联系统对接请求地址（内网）
	public $arrayXml;
	// const apiUrl = 'http://113.108.182.4:8083/aipg/ProcessServlet';//通联系统对接请求地址（外网）
	public $apiUrl;//（生产环境地址）
	
	public function __construct($mchid)      
    { 

    	switch ($mchid) {   //判断商户号
    		case '200551000014254':
    			$this->certFile = './data/tlpay/allinpay-pds.pem';//微领地商户
    			$this->privateKeyFile = './data/tlpay/20055100001425404.pem';//微领地商户
    			break;
    		case '200551000014294':
    			$this->certFile = './data/tlpay/allinpay.pem';//湖南腾茂商户
    			$this->privateKeyFile = './data/tlpay/20055100001429404.pem'; //湖南腾茂商户
    			break;
    		case '200551000014296':
    			$this->certFile = './data/tlpay/allinpay.pem';//湖南十月商户
    			$this->privateKeyFile = './data/tlpay/20055100001429604.pem'; //湖南十月商户
    			break;
    		case '200551000014314':
    			$this->certFile = './data/tlpay/allinpay-sanyue.pem';//长沙三月三百货贸易商户
    			$this->privateKeyFile = './data/tlpay/20055100001431404.pem'; //长沙三月三百货贸易商户
    			break;
    		case '200551000014354':
    			$this->certFile = './data/tlpay/allinpay-xld.pem';//深圳新领地游艇服务有限公司
    			$this->privateKeyFile = './data/tlpay/20055100001435404.pem'; //深圳新领地游艇服务有限公司
    			break;
    		case '200551000014334':
    			$this->certFile = './data/tlpay/allinpay-sqsm.pem';//湖南松乔生命科技有限责任公司
    			$this->privateKeyFile = './data/tlpay/20055100001433404.pem'; //湖南松乔生命科技有限责任公司
    			break;
    		default:
    			$this->certFile = './data/tlpay/allinpay-pds.pem';//微领地商户
    			$this->privateKeyFile = './data/tlpay/20055100001425404.pem';//微领地商户
    			break;
    	}

    	$this->password = '111111';
    	$this->apiUrl = 'https://tlt.allinpay.com/aipg/ProcessServlet';
        $this->arrayXml = new \ArrayAndXml();
    }     
	
	/**
	 * PHP版本低于 5.4.1 的在通联返回的是 GBK编码环境使用
	 * 但是本地文件编码是 UTF-8
	 *
	 * @param string $hexstr
	 * @return binary string
	 */
	public function hextobin($hexstr) {
	    $n = strlen($hexstr);
	    $sbin = "";
	    $i = 0;
	
	    while($i < $n) {
	        $a = substr($hexstr, $i, 2);
	        $c = pack("H*",$a);
	        if ($i==0) {
	            $sbin = $c;
	        } else {
	            $sbin .= $c;
	        }
	
	        $i+=2;
	    }
	
	    return $sbin;
	}
	
	/**
	 * 验签
	 */
	public function verifyXml($xmlResponse){
			
		// 本地反馈结果验证签名开始
		$signature = '';
		if (preg_match('/<SIGNED_MSG>(.*)<\/SIGNED_MSG>/i', $xmlResponse, $matches)) {
		    $signature = $matches[1];
		}
		
		$xmlResponseSrc = preg_replace('/<SIGNED_MSG>.*<\/SIGNED_MSG>/i', '', $xmlResponse);
		
		$pubKeyId = openssl_get_publickey(file_get_contents($this->certFile));
		$flag = (bool) openssl_verify($xmlResponseSrc, hex2bin($signature), $pubKeyId);
		openssl_free_key($pubKeyId);
		if ($flag) {   //通过验证
		    // 变成数组，做自己相关业务逻辑
		    $xmlResponse = mb_convert_encoding(str_replace('<?xml version="1.0" encoding="GBK"?>', '<?xml version="1.0" encoding="UTF-8"?>', $xmlResponseSrc), 'UTF-8', 'GBK');
		    $results = $this->arrayXml->parseString( $xmlResponse , TRUE);
		    return $results;
		} else {
//		    echo '<br/>Verified: <font color=red>Failed</font>.';
		    return FALSE;
		}
	}
	
	/**
	 * 签名
	 */
	public function signXml($params){
		 
		$xmlSignSrc = $this->arrayXml->toXmlGBK($params, 'AIPG');
		$xmlSignSrc=str_replace("TRANS_DETAIL2", "TRANS_DETAIL",$xmlSignSrc);
		$privateKey = file_get_contents($this->privateKeyFile);
		
		$pKeyId = openssl_pkey_get_private($privateKey, $this->password);
		openssl_sign($xmlSignSrc, $signature, $pKeyId);
		openssl_free_key($pKeyId);
		$params['SIGNED_MSG'] = bin2hex($signature);
		$xmlSignPost = $this->arrayXml->toXmlGBK($params, 'AIPG');

		return  $xmlSignPost;
	}
	/**
	 * 发送请求
	 */
	public function send($params){
		$xmlSignPost=$this->signXml($params);
		$xmlSignPost=str_replace("TRANS_DETAIL2", "TRANS_DETAIL",$xmlSignPost);
		$response = cURL::factory()->post($this->apiUrl, $xmlSignPost);
		if (! isset($response['body'])) {
		    die('Error: HTTPS REQUEST Bad.');
		}
		
		//获取返回报文
		$xmlResponse = $response['body'];
		 //验证返回报文
		$result = $this->verifyXml($xmlResponse);
		return $result;
	}

	public function xmlToArray($xml){  
	    //考虑到xml文档中可能会包含<![CDATA[]]>标签，第三个参数设置为LIBXML_NOCDATA  
	    if (file_exists($xml)) {  
	        libxml_disable_entity_loader(false);  
	        $xml_string = simplexml_load_file($xml,'SimpleXMLElement', LIBXML_NOCDATA);  
	    }else{  
	        libxml_disable_entity_loader(true);  
	        $xml_string = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);  
	    }  
	    $result = json_decode(json_encode($xml_string),true);  
	    return $result;  
	}  
}

?>