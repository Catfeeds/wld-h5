<?php

class YspayYspay
{
    /**
     * 构造函数
     */
    function __construct()
    {
        $this->config();
        date_default_timezone_set('PRC');
        define('BASE_PATH', str_replace('\\', '/', realpath(dirname(__FILE__) . '/')) . "/");
    }
    /**
     * 仅为实例化商户加密证书 银盛公钥 商户加密证书密码
     */
    function config()
    {
        $this->param = array();
        $this->param['pfxpath'] = 'http://' . $_SERVER['HTTP_HOST'] . "/MiLib/Library/Vendor/Ysepay/certs/wld17375717292.pfx";
        $this->param['businessgatecerpath'] = 'http://' . $_SERVER['HTTP_HOST'] . "/MiLib/Library/Vendor/Ysepay/certs/businessgate.cer";
        $this->param['pfxpassword'] = "821321";
    }

    /**
     * PC收银台接口 测试环境仅需使用pc收银台->网银支付,作为商户测试环境校验.
     */
    function get_code($order)
    {
        $myParams = array();
        $myParams['business_code'] = '01000010';
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.online.directpay.createbyuser';
        $myParams['notify_url'] = 'https://testapi.iweilingdi.com/api.php/Pay/Test/respond_notify';//'http://yspay.ngrok.cc/pay/notify.php';
        $myParams['out_trade_no'] = $order;
        $myParams['partner_id'] = 'wld17375717292';
        $myParams['return_url'] = 'https://testapi.iweilingdi.com/api.php/Pay/Test/respond_return';//'http://yspay.ngrok.cc/pay/respond.php';
        $myParams['seller_id'] = 'wld17375717292';//'wld17375717292';
        $myParams['seller_name'] = '长沙微领地网络科技有限公司';
        $myParams['sign_type'] = 'RSA';
        $myParams['subject'] = '支付测试';
        $myParams['timeout_express'] = '1d';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['total_amount'] = '0.01';
        $myParams['version'] = '3.0';

        ksort($myParams);
        $data = $myParams;
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        dump($myParams);
        //$action ="https://mertest.ysepay.com/openapi_gateway/gateway.do";
        $action = "https://openapi.ysepay.com/gateway.do";
        $def_url = "<br /><form style='text-align:center;' method=post action='" . $action . "' target='_blank'>";
        while ($param = each($myParams)) {
            $def_url .= "<input type = 'hidden' id='" . $param['key'] . "' name='" . $param['key'] . "' value='" . $param['value'] . "' />";
        }
        $def_url .= "<input style='font-size: 24px;color: red;' type=submit value='点击提交' " . @$GLOBALS['_LANG']['pay_button'] . "'>";
        $def_url .= "</form>";

        return $def_url;
    }

    /**
     * 说明 余额查询接口
     */
    function get_money($parr)
    {
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.online.user.account.get';
        $myParams['partner_id'] = $parr['partner_id'];
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';

        if(empty($parr['account_no'])){
            $biz_content_arr = array(
                "user_code" => $parr['user_code'],
                "user_name" => $parr['user_name']
            );
        }else{
            $biz_content_arr = array(
                "user_code" => $parr['user_code'],
                "user_name" => $parr['user_name'],
                "account_no" => "0000400033388070"
            );
        }

        $myParams['biz_content'] = json_encode($biz_content_arr, JSON_UNESCAPED_UNICODE);//构造字符串
        ksort($myParams);
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        return $myParams;
        // var_dump($myParams);
    }

    /**
     * 说明:单笔代付加急接口
     */
    function get_dfjj($parr)
    {
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.df.single.quick.accept';
        $myParams['notify_url'] = $parr['notify_url'];//'http://yspay.ngrok.cc/pay/respond_notify.php';
        $myParams['partner_id'] = 'wld17375717292';
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';
        $biz_content_arr = array(
            "out_trade_no" => $parr['out_trade_no'],
            "business_code" => "2010002", //业务编号
            "currency" => "CNY",
            "total_amount" => $parr['total_amount'],
            "subject" =>$parr['subject'],
            "bank_name" =>$parr['bank_name'],//"建设银行恒达支行",//"望城坡支行",//
            "bank_city" => $parr['bank_city'],
            "bank_account_no" => $parr['bank_account_no'],//"6217002920125089541",//"6232512920277176",//"6217002920132384174",//
            "bank_account_name" => $parr['bank_account_name'],//"李泰然",
            "bank_account_type" => $parr['bank_account_type'],// corporate :对公账户; personal :对私账户
            "bank_card_type" => $parr['bank_card_type']   //  debit:借记卡;credit:信用卡 unit:单位结算卡
        );
        $myParams['biz_content'] = json_encode($biz_content_arr, JSON_UNESCAPED_UNICODE);//构造字符串
        // var_dump($myParams);
        ksort($myParams);
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        // var_dump($signStr);
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        // var_dump($myParams);
        return $myParams;
    }


    /**
     * 单笔代付 订单查询
     */

    function searchOrder($parr){
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.df.single.query';
        $myParams['partner_id'] = 'wld17375717292';
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';
        $biz_content_arr = array(
            "out_trade_no" => $parr['out_trade_no'], // 订单号
        );
        $myParams['biz_content'] = json_encode($biz_content_arr);//构造字符串
        ksort($myParams);
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        // var_dump($signStr);
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        return $myParams;
    }

    /**
     *  支付宝二维码接口 测试环境无法模拟真实场景 仅作同步验签 商户自行修改商户号 商户名等参数
     */
    function get_alipay($order)
    {
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.online.qrcodepay';
        $myParams['partner_id'] = 'shanghu_test';
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';
        $myParams['return_url'] = 'http://yspay.ngrok.cc/pay/respond.php';
        $myParams['notify_url'] = 'http://yspay.ngrok.cc/pay/respond_notify.php';
        $biz_content_arr = array(
            "out_trade_no" => "$order",
            "subject" => "测试扫码",
            "total_amount" => "10",
            "seller_id" => "shanghu_test",
            "seller_name" => "银盛支付商户测试公司",
            "timeout_express" => "24h",
            "business_code" => "01000010",
            "bank_type" => "1903000"
        );
        $myParams['biz_content'] = json_encode($biz_content_arr, JSON_UNESCAPED_UNICODE);//构造字符串
        ksort($myParams);
        var_dump($myParams);
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        var_dump($signStr);
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        var_dump($myParams['sign'] );
        return $myParams;
        var_dump($myParams);
    }

    /**
     * 代收签约接口 传入已加密的证件号码
     */
    function get_inner($no){
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.ds.protocol.single.accept';
        $myParams['partner_id'] = 'shanghu_test';
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';
        $myParams['notify_url'] = 'http://yspay.ngrok.cc/pay/respond_notify.php';
        $biz_content_arr = array(
            "protocol_no" => 'DS' .$this->datetime2string(date('Y-m-d H:i:s')),
            "business_code" => "1010004",
            /* 生效和失效时间由商户和客户进行协商,生效期内无需重复签约,使用协议号进行交易操作即可 */
            "effect_date" => "20180101",
            "expire_date" => "20180701",
            "bank_account_type" => "personal",
            "bank_card_type" => "debit",
            "bank_name" => "工商银行深圳支行",
            "bank_account_no" => "621483782233747700",
            "bank_account_name" => "工行",
            "bank_city" => "深圳市",
            "bank_telephone_no" => "13821382138",
            "cert_type" => "00",
            "cert_no" => $no,
            "cert_expire"=>"20200808",
            "month_num_limit"=>"10000",
            "month_amount_limit"=>"1000000"
        );
        $myParams['biz_content'] = json_encode($biz_content_arr,320);//构造字符串
        ksort($myParams);
        var_dump($myParams);
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        var_dump($signStr);
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        var_dump($myParams['sign'] );
        return $myParams;
        var_dump($myParams);
    }
    /**
     * 支付宝 H5 支付参数获取
     */
    function alipay_H5($parr){
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.online.wap.directpay.createbyuser';
        $myParams['partner_id'] = $parr['partner_id'];
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';
        $myParams['notify_url'] = $parr['notify_url'];  //yspay.ngrok.cc/pay/respond_notify.php';
        $myParams['return_url'] = $parr['return_url'];//'https://testapi.iweilingdi.com/api.php/Pay/Test/return_alipay'; // 跳转页面
        $myParams['out_trade_no'] =$parr['out_trade_no'];
        $myParams['subject'] = $parr['subject'];
        $myParams['total_amount'] =$parr['total_amount'];
        $myParams['seller_id'] =$parr['seller_id'];
        $myParams['seller_name'] =$parr['seller_name'];
        $myParams['timeout_express'] ="96h";
        $myParams['pay_mode'] = "native";
        $myParams['bank_type'] ="1903000";
        $myParams['business_code'] ="3010002";
        ksort($myParams);
        $data = $myParams;
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        dump($myParams);
        $action = "https://openapi.ysepay.com/gateway.do";
        $def_url = "<br /><form style='text-align:center;' method=post action='" . $action . "' target='_blank'>";
        while ($param = each($myParams)) {
            $def_url .= "<input type = 'hidden' id='" . $param['key'] . "' name='" . $param['key'] . "' value='" . $param['value'] . "' />";
        }
        $def_url .= "<input style='font-size: 24px;color: red;' type=submit value='点击提交' " . @$GLOBALS['_LANG']['pay_button'] . "'>";
        $def_url .= "</form>";
        return $def_url;
    }


    /**
     * 支付宝 扫码
     */
    function scan_pay($parr){
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.online.qrcodepay';
        $myParams['partner_id'] =$parr['partner_id'];
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';
        $myParams['notify_url'] = $parr['notify_url'];  //yspay.ngrok.cc/pay/respond_notify.php';
        $myParams['return_url'] = $parr['return_url'];//'https://testapi.iweilingdi.com/api.php/Pay/Test/return_alipay'; // 跳转页面
        $biz_content_arr = array(
            "out_trade_no" => $parr['out_trade_no'], // 订单号
            "subject" => $parr['subject'], // 产品名
            "total_amount" => $parr['total_amount'], //总金额
            "seller_id" => $parr['seller_id'], // 收款方银盛支付用户名
            "seller_name" => $parr['seller_name'], // 授权方银盛支付客户名
            "timeout_express" => "96h",  // 超时时间
            "business_code" => "01000009",
            "currency" => "CNY",
            "bank_type" => $parr['bank_type'], // 1902000 微信  1903000 支付宝
        );
        $myParams['biz_content'] = json_encode($biz_content_arr);//构造字符串
        ksort($myParams);
        $data = $myParams;
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        return $myParams;
    }


    /**
     * 平台内代付(银行卡)
     */
    function get_inner_df_card($parr)
    {
        $myParams = array();
        $myParams['charset'] = 'utf-8';
        $myParams['method'] = 'ysepay.df.single.quick.accept';
        $myParams['partner_id'] = 'wld17375717292';
        $myParams['sign_type'] = 'RSA';
        $myParams['timestamp'] = date('Y-m-d H:i:s', time());
        $myParams['version'] = '3.0';
        $myParams['notify_url'] = $parr['notify_url'];
        $myParams['proxy_password'] = $parr['proxy_password'];
        $myParams['merchant_usercode'] = $parr['merchant_usercode'];  //扣款方
        $biz_content = array(
            "out_trade_no" => $parr['out_trade_no'],
            "shopdate" => $this->datetime2string(date('Ymd')),
            "business_code" => "2010002",
            "currency" => "CNY",
            "total_amount" => $parr['total_amount'],
            "subject" =>"平台内代付测试",
            "bank_name" =>$parr['bank_name'],
            "bank_city"=>$parr['bank_city'],
            "bank_account_no" => $parr['bank_account_no'],
            "bank_account_name"=>$parr['bank_account_name'],
            "bank_card_type"=>"debit",
            "bank_account_type"=>"personal"
        );
        $myParams['biz_content'] = json_encode($biz_content, 320);//构造字符串
        ksort($myParams);
        //var_dump($myParams);
        $signStr = "";
        foreach ($myParams as $key => $val) {
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        //var_dump($signStr);
        $sign = $this->sign_encrypt(array('data' => $signStr));
        $myParams['sign'] = trim($sign['check']);
        //var_dump($myParams['sign']);
        //var_dump($myParams);
        return $myParams;
    }


    /** curl 获取 https 请求 平台内单笔代付(银行卡)
     * @param qa 要发送的数据
     */
    function curl_inner_df_card($qa)
    {
        $ch = curl_init("https://df.ysepay.com/gateway.do");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($qa));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            var_dump($ch);
        } else {
            $response = json_decode($response, true);
            return $response;
            $sign = $response['sign'];
            //echo $sign;
            $data = json_encode($response['ysepay_df_single_quick_accept_response'], JSON_UNESCAPED_UNICODE);
            $data = $this->arrayToString($data);
            // var_dump($data);
            /* 验证签名 仅作基础验证*/
            if ($this->sign_check($sign, $data) == true) {
                echo "验证签名成功!";
            } else {
                echo '验证签名失败!';
            }
        }
    }



    /**
     *日期转字符
     *输入参数：yyyy-MM-dd HH:mm:ss
     *输出参数：yyyyMMddHHmmss
     */
    public function datetime2string($datetime)
    {
        return preg_replace('/\-*\:*\s*/', '', $datetime);
    }

    /**
     * 验签转明码
     * @param input check
     * @param input msg
     * @return data
     * @return success
     */

    function sign_check($sign, $data)
    {
        $publickeyFile = $this->param['businessgatecerpath']; //公钥
        $certificateCAcerContent = file_get_contents($publickeyFile);
        $certificateCApemContent = '-----BEGIN CERTIFICATE-----' . PHP_EOL . chunk_split(base64_encode($certificateCAcerContent), 64, PHP_EOL) . '-----END CERTIFICATE-----' . PHP_EOL;
        // 签名验证
        $success = openssl_verify($data, base64_decode($sign), openssl_get_publickey($certificateCApemContent), OPENSSL_ALGO_SHA1);
        return $success;
    }

    /**
     * 签名加密
     * @param input data
     * @return success
     * @return check
     * @return msg
     */
    function sign_encrypt($input)
    {
        $return = array('success' => 0, 'msg' => '', 'check' => '');
        $pkcs12 = file_get_contents($this->param['pfxpath']); //私钥
        if (openssl_pkcs12_read($pkcs12, $certs, $this->param['pfxpassword'])) {
            $privateKey = $certs['pkey'];
            $publicKey = $certs['cert'];
            $signedMsg = "";
            if (openssl_sign($input['data'], $signedMsg, $privateKey, OPENSSL_ALGO_SHA1)) {
                $return['success'] = 1;
                $return['check'] = base64_encode($signedMsg);
                $return['msg'] = base64_encode($input['data']);
            }
        }

        return $return;
    }

    /**
     * 数组转字符串
     */
    function arrayToString($arr)
    {
        if (is_array($arr)) {
            return implode(',', array_map('arrayToString', $arr));
        }
        return $arr;
    }

    /**
     * DES加密方法
     * @param $data 传入需要加密的证件号码
     * @param $key key为商户号前八位.不足八位的需在商户号末尾补0
     * @return string 返回加密后的字符串
     */
    function ECBEncrypt($data,$key)
    {
        $encrypted = openssl_encrypt($data,'DES-ECB',$key,1);
        return base64_encode($encrypted);
    }

    /**
     * DES解密方法
     * @param $data 传入需要解密的字符串
     * @param $key key为商户号前八位.不足八位的需在商户号末尾补0
     * @return string 返回解密后的证件号码
     */
    function doECBDecrypt($data,$key)
    {
        $encrypted = base64_decode($data);
        $decrypted = openssl_decrypt($encrypted, 'DES-ECB', $key, 1);
        return $decrypted;
    }
    /** curl 获取 https 请求 余额查询
     * @param qa 要发送的数据
     */
    function curl_https($qa)
    {
        $ch = curl_init("https://openapi.ysepay.com/gateway.do");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($qa));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            dump($ch);
        } else {
            $response = json_decode($response, true);
            $sign = $response['sign'];
            $data = json_encode($response['ysepay_online_user_account_get_response'], JSON_UNESCAPED_UNICODE);
            $data = $this->arrayToString($data);
            /* 验证签名 仅作基础验证*/
            if ($this->sign_check($sign, $data) == true) {
                return $response; //echo "验证签名成功!";
            } else {
                echo '验证签名失败!';
            }
        }

    }



    /** curl 获取 https 请求 扫码返回
     * @param qa 要发送的数据
     */
    function curl_https_scan($qa)
    {
        $ch = curl_init("https://qrcode.ysepay.com/gateway.do");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($qa));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            dump($ch);
            return false;
        } else {
            return json_decode($response, true);

        }

    }


    /** curl 获取 https 请求 单笔代付加急
     * @param qa 要发送的数据
     */
    function curl_https_df($qa)
    {
        $ch = curl_init("https://df.ysepay.com/gateway.do");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($qa));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        // var_dump($response);
        if (curl_errno($ch)) {
            dump($ch);
        } else {
            $response = json_decode($response, true);
            $sign = $response['sign'];
            $data = json_encode($response['ysepay_df_single_quick_accept_response'], JSON_UNESCAPED_UNICODE);
            $data = $this->arrayToString($data);
            /* 验证签名 仅作基础验证*/
            if ($this->sign_check($sign, $data) == true) {
                return $response; //echo "验证签名成功!";
            } else {
                echo '验证签名失败!';
            }
        }
    }


    /** curl 获取 https 请求 单笔代付订单查询
     * @param qa 要发送的数据
     */
    function curl_https_df_search($qa)
    {
        $ch = curl_init("https://searchdf.ysepay.com/gateway.do");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($qa));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            dump($ch);
            return false;
        } else {
            return json_decode($response, true);

        }
    }

}
