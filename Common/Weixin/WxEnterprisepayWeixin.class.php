<?php

class WxEnterprisepayWeixin{

    public function Pay($trade_no,$openid,$amount,$username,$check_name) {
        vendor('WxEnterprisePay.WxHongBaoHelper');
        vendor('WxEnterprisePay.CommonUtil');

        $pkey = 'syyp29434526syyp29434526syyp2948';
        $amount =  $amount * 100;

        $commonUtil = new \CommonUtil();
        $wxHongBaoHelper = new \WxHongBaoHelper($pkey);
        $actioncode = 0; //返回值

        if (empty($trade_no) || empty($openid) || empty($username) || empty($amount) || empty($check_name)) {
            return Message(1002, "请输入正确的参数");
        }

       //=======================给客户转钱全过程
        $wxHongBaoHelper->setParameter("nonce_str", $commonUtil->create_noncestr()); //随机字符串
        $wxHongBaoHelper->setParameter("partner_trade_no", $trade_no); //交易号
        $wxHongBaoHelper->setParameter("mchid", '1278932101');
        $wxHongBaoHelper->setParameter("mch_appid", C('APPID'));
        $wxHongBaoHelper->setParameter("openid", $openid);
        $wxHongBaoHelper->setParameter("check_name", $check_name);
        $wxHongBaoHelper->setParameter("amount", $amount);
        $wxHongBaoHelper->setParameter("re_user_name", $username);
        $wxHongBaoHelper->setParameter("desc", "微领地企业付款"); //描述
        $wxHongBaoHelper->setParameter("spbill_create_ip",'120.24.224.110');
        $postXml = $wxHongBaoHelper->create_hongbao_xml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //企业付款接口，POST，需要证书
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml); //发送数据，并接收返回数据

        $responseObj = simplexml_load_string($responseXml); //分解返回数据

        $xmlstring = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);

        if ($responseObj->result_code == "SUCCESS" && $responseObj->return_code == "SUCCESS") {//付款成功，往红包记录表中插入一条数据
            return MessageInfo(0,"支付成功",$val);
        } else {//返回数据不正常的时候
            $wxHongBaoHelper->create_file("log.txt", "", $responseXml); //记录日志
            return MessageInfo(1001,$val['err_code_des'],$val);
        }
    }

}
