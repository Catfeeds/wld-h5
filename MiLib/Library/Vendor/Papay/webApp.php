<?php
/*admin
 * 2016-11-28
 * 外部应用加解密demo
 * */
 class webApp{
    #密码必须经过sha1 加密
    #金额不会用小数点表示 以分为单位
    #参数名称及加密后字符，默认使用小写，除非有特别约定的地方会有特殊说明
    #所有接口请求数据必须要传open_id,timestamp,sign,data 四个参数，data 参数是aes 加密后的json 数据(为用户要提交的请求数据)。
	public $DEBUG				=false;
	

    public $open_id = '46a020ffa7db42d6fc1a22f9d0b9fd77'; //测试open_id
    public $open_key = '8332000c9ac618231e3fe1e4248d3a35';//测试open_key
	public $open_url = 'https://api.pabcs.com/mct1/';//测试地址 

	public function __construct($open_id, $open_key) {
		if (!empty($open_id)) {
			$this->open_id = $open_id;
		}
		if (!empty($open_key)) {
			$this->open_key = $open_key;
		}
	}

    #签名
    public function signs($array){
        $signature = array();
        foreach($array as $key=>$value){
            $signature[$key]=$key.'='.$value;
        }
        $signature['open_key']='open_key'.'='.$this->open_key;
        ksort($signature);
        #先sha1加密 在md5加密
        $sign_str = md5(sha1(implode('&', $signature)));
        return $sign_str;
    }

    #使用post的传输
    public function api($url,$post){
		#必填参数
        $data = [
            'open_id'=>$this->open_id,
            'timestamp'=>time(),
        ];
		$data['data']= $post;
		if($this->DEBUG){if($data){$this->debug('接口调用：'.$url,http_build_query($data));}else{$this->debug('接口调用：'.$url,'');}}
		if(is_array($data)){
		if($this->DEBUG){$this->debug('加密前字符串',json_encode($data));}
		$data['data'] = $this->encrypt(json_encode($post),$this->open_key);
		$data['sign'] = $this->signs($data);
		if($this->DEBUG){$this->debug('加密后字符串',json_encode($data));}
		}else{
		$data=null;
		}
		$result = $this->CURL($url,$data);
		if(isset($result['data'])){
			if($this->DEBUG){$this->debug('解密前字符串',$result['data']);}
			$result['data']=$this->decrypt($result['data'], $this->open_key);
			if($this->DEBUG){$this->debug('解密后字符串',$result['data']);}	
			$result['data']=json_decode($result['data'],true);
			if($this->DEBUG){if(is_array($result['data'])){$this->debug('JSON转数组成功','成功');}else{$this->debug('JSON转数组成功','失败');}}
		}
		unset($result['sign']);
		return $result;
    }
	
	 #使用post的传输
    public function CURL($url,$data){
        //启动一个CURL会话
        $ch = curl_init();
        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        //忽略证书
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL,$this->open_url.$url);
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_HEADER,0);//是否需要头部信息（否）
        // 执行操作
        $result = curl_exec($ch);
        
        if($this->DEBUG){
		$this->debug('接口返回数据',$result);
		}
		if($result){
		curl_close($ch);
		#将返回json转换为数组
		$arr_result=json_decode($result,true);
		if(!is_array($arr_result)){
			$arr_result['errcode']=1;
			$arr_result['msg']='服务器繁忙，请稍候重试';
			if($this->DEBUG){
				$this->debug('服务器返回数据格式错误',$result);
			}
		
		}		
		}else{
		$err_str=curl_error($ch);
		curl_close($ch);	
		$arr_result['errcode']=1;
		$arr_result['msg']='服务器繁忙，请稍候重试';
		if($this->DEBUG){
			$this->debug('服务器无响应',$err_str);
		}
		}
	#返回数据
	return $arr_result;

    }
	
    #@todo AES加解密
    #加密
    public static function encrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = strtoupper(bin2hex($data));
        return $data;
    }

    private static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    //解密
    public static function decrypt($sStr, $sKey) {
        $sStr=hex2bin($sStr);
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            $sStr,
            MCRYPT_MODE_ECB
        );

        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
	#日志记录
	protected function debug($tempType,$tempStr){
		$log_name = 'data/papaylog.txt';
		$tempStr=date('Y-m-d H:i:s').' '.$tempType."\r\n".$tempStr."\r\n\r\n";
		$myfile = fopen($log_name, "a");
		fwrite($myfile, $tempStr);
		fclose($myfile);
	}
	#消息回调
	public function notify($map){
		return $this->check_sign($map);
	}
	#验签过程
	protected function check_sign($array){
		if($this->DEBUG){$this->debug('验签数据',http_build_query($array));}
		if(empty($array['sign'])){
			return false;
			exit();
		}
		$sign=$array['sign'];#得到返回签名字符串
		unset($array['sign']);#去掉sign节点
		$sign_str = array ();
		foreach ($array as $key => $val) {
			$sign_str[]=$key.'='.$val;
		}
		$sign_str['open_key']='open_key'.'='.$this->open_key;	
		ksort($sign_str);#排序
		$sign_str = md5(sha1(implode('&', $sign_str)));			
		if($sign_str==$sign){
			if($this->DEBUG){$this->debug('验签成功',$sign_str);}
			return true;
		}else{
			if($this->DEBUG){$this->debug('验签失败',$sign_str);}
			return false;
		}
	}	
}

