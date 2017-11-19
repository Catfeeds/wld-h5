<?php

/*
  微信支付类
 */

class Wxpay {

    const URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    private $_config = array();

    public function __construct($info) {
        //生成配置参数
        $this->_makeConfig($info);
    }

    /*
      运行方法
     */

    public function run() {

        //将config 转换为xml格式数据
        $xml_str = '';
        $xml_str = '<xml>';
        foreach ($this->_config as $k => $v) {
            $xml_str .= '<' . $k . '>' . $v . '</' . $k . '>';
        }
        $xml_str .= '</xml>';
        return $this->_postXmlCurl($xml_str, self::URL);
    }

    /*
      生成配置文件
     */

    private function _makeConfig($info) {
        if (!is_array($info))
            exit('非法传参');

        //固定参数
        $fix_config = array(
            'appid' => strtolower(C('WXAPPID')),
            'mch_id' => strtolower(C('WXMCHID')),
            'nonce_str' => strtolower($this->_makeRandom()),
            'spbill_create_ip' => strtolower(get_client_ip()),
            'trade_type' => 'APP',
        );

        $tmp_config = array_merge($fix_config, $info);

        $this->_config = $this->_sortConfig($tmp_config);

        $this->_makeSign();
    }

    /*
      生成随机字符串
     */

    private function _makeRandom() {
        return md5(uniqid());
    }

    /*
      对配置文件进行排序
     */

    private function _sortConfig($arr) {
        $new_arr = array();
        foreach ($arr as $key => $value) {
            if (empty($value))
                continue;

            $new_arr[$key] = $value;
        }
        ksort($new_arr);
        return $new_arr;
    }

    /*
      生成签名
     */

    private function _makeSign() {
        //第一步，将config参数生成 & 分割的字符串
        $str_config = '';
        foreach ($this->_config as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $str_config .= $key . '=' . $value . '&';
        }
        //拼接API密钥
        $str_config .= 'key=' . C('WXAPPPWD');
        //md5 加密，并转为大写
        $sign_info = strtoupper(md5($str_config));

        $this->_config['sign'] = $sign_info;
    }

    /**
     * 以post方式提交xml到对应的接口url
     * 
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     */
    private function _postXmlCurl($xml, $url, $useCert = false, $second = 30) {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

//        if ($useCert == true) {
//            dump("123123213");
//            //设置证书
//            //使用证书：cert 与 key 分别属于两个.pem文件
//            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
//            curl_setopt($ch, CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
//            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
//            curl_setopt($ch, CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
//        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);

            curl_close($ch);

            return false;
        }
    }

}
