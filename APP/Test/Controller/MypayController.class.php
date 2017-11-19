<?php
namespace Test\Controller;

use Base\Controller\BaseController;

class MypayController extends BaseController
{
    function _initialize(){
        Vendor('Ysepay.Yse_pay');
    }

    // PC收银台接口
    public function pc_test()
    {
        $pay =new \Yse_pay();
        $orderid =$pay->datetime2string(date('Y-m-d H:i:s'));
        $result =$pay->get_code($orderid);
        echo $result;
    }
  // 查看账户余额
    public function getTotalMoney()
    {
        $pay =new \Yse_pay();
        $parr['partner_id'] ="wld17375717292";//"wld17375717292";
        $parr['user_code'] ="wld17375717292";// "801551088883618";
        $parr['user_name'] ="长沙微领地网络科技有限公司";//"长沙微领地网络科技有限公司";
        $parr['account_no'] ="0000400033388070";  //为空  则查当前账户下所有账号余额  否则查对应账号余额
        $data =$pay->get_money($parr);
        $result = $pay->curl_https($data);
        dump($result);
    }

    // 单笔快速支付
    public function quick_pay(){

        $pay =new \Yse_pay();
        $orderid =$pay->datetime2string(date('Y-m-d H:i:s'));
        $parr['notify_url']=GetHost()."/api.php/Pay/Yspay/respond_notify";
        $parr['out_trade_no']=$orderid;
        $parr['total_amount']="1.00";
        $parr['subject']="测试";
        $parr['bank_name']="建设银行华兴支行";
        $parr['bank_city']="长沙市";
        $parr['bank_account_no']="6217002920123595952";
        $parr['bank_account_name']="童向";
        $parr['bank_account_type']="personal"; //corporate :对公账户; personal :对私账户
        $parr['bank_card_type']="debit"; //  debit:借记卡;credit:信用卡 unit:单位结算卡
        $data =$pay->get_dfjj($parr);
        $result =$pay->curl_https_df($data);
        dump($result);

    }
    //  单笔代付订单查询
    public function search_df_order(){
        $pay =new \Yse_pay();
        $parr['out_trade_no'] ="20170906150313";
        $data =$pay->searchOrder($parr);
        $result =$pay->curl_https_df_search($data);
        dump($result);
    }


    // 支付宝 H5 支付
    public function alipay_H5(){
        $pay =new \Yse_pay();
        $orderId =$pay->datetime2string(date('Y-m-d H:i:s'));
        $parr['partner_id'] ="wld17375717292";
        $parr['notify_url'] =GetHost()."/api.php/Pay/Yspay/respond_alipay_notify";
        $parr['return_url'] =GetHost()."/api.php/Pay/Yspay/return_alipay";
        $parr['out_trade_no'] =$orderId;
        $parr['subject'] ="测试商品";
        $parr['total_amount'] ="0.01";
        $parr['seller_id'] ="wld17375717292";
        $parr['seller_name'] ="长沙微领地网络科技有限公司";
        $data =$pay->alipay_H5($parr);

        $action = "https://openapi.ysepay.com/gateway.do";
        $def_url = "<br /><form style='text-align:center;' method=post action='" . $action . "' target='_blank'>";
        while ($param = each($data)) {
            $def_url .= "<input type = 'hidden' id='" . $param['key'] . "' name='" . $param['key'] . "' value='" . $param['value'] . "' />";
        }
        $def_url .= "<input style='font-size: 24px;color: red;' type=submit value='点击提交' " . @$GLOBALS['_LANG']['pay_button'] . "'>";
        $def_url .= "</form>";
       echo $def_url;
    }

    // 扫码订单
    public function scan_order(){
        $pay =new \Yse_pay();
        $orderId =$pay->datetime2string(date('Y-m-d H:i:s'));
        $parr['partner_id'] ="wld17375717292";
        $parr['notify_url'] =GetHost()."/api.php/Pay/Yspay/scan_notify";
        $parr['return_url'] =GetHost()."/api.php/Pay/Yspay/return_alipay";
        $parr['out_trade_no'] =$orderId;
        $parr['subject'] ="测试商品";
        $parr['total_amount'] ="0.01";
        $parr['seller_id'] ="wld17375717292";
        $parr['seller_name'] ="长沙微领地网络科技有限公司";
        $parr['bank_type'] ="1903000";//1902000 微信  1903000 支付宝
        $data =$pay->scan_pay($parr);
        $result = $pay->curl_https_scan($data);
        dump($result);
    }


    public function weixin_pay(){
        $pay =new \Yse_pay();
        $orderId =$pay->datetime2string(date('Y-m-d H:i:s'));
        $params['notify_url'] = GetHost()."/api.php/Pay/Yspay/respond_notify";
        $params['out_trade_no'] =$orderId;
        $params['total_amount'] ="0.01";
        $params['subject'] ="商品名";
        $params['seller_id'] ="wld17375717292"; // 收款方银盛支付用户名
        $params['seller_name'] ="长沙微领地网络科技有限公司"; // 授权方银盛支付客户名
        $params['sub_openid'] ="oTCJiuGnc2FhJ4ZgddI0o3AXZab4";
        $data =$pay->weixin_pay($params);
        $result = $pay->curl_https_appid($data);

        $data1 =json_decode($result['ysepay_online_jsapi_pay_response']['jsapi_pay_info'],true);
        $datainfo['appId'] = $data1['appId'];
        $datainfo['timeStamp'] = $data1['timeStamp'];
        $datainfo['signType'] = $data1['signType'];
        $datainfo['package'] = $data1['package'];
        $datainfo['nonceStr'] = $data1['nonceStr'];
        $datainfo['paySign'] = $data1['paySign'];
        $datainfo['orderid'] = $result['ysepay_online_jsapi_pay_response']['out_trade_no'];
        dump($datainfo);
       // $this->ajaxReturn(MessageInfo(1,'返回成功',$datainfo));
//        $arr =$result['ysepay_online_jsapi_pay_response']['jsapi_pay_info'];
//        $aa =json_decode($arr);
//        if($result==false){
//            $this->ajaxReturn(Message(1001,'签名验证失败'));
//        }else{
//            $this->ajaxReturn(MessageInfo(1,'返回成功',$aa));
//        }

       // dump($result);
    }


    // 支付宝 H5 支付 跳转
    function return_alipay(){
        $pay =new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $result = $_POST;
        unset($result['sign']);
        ksort($result);
        $url = "";
        foreach ($result as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/jump.txt";
        file_put_contents($file, "\r\n", FILE_APPEND);
        file_put_contents($file, "return|data:" . $data . "|sign:" . $sign, FILE_APPEND);
        /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            file_put_contents($file, "\r\n", FILE_APPEND);
            file_put_contents($file,"success:".$data,FILE_APPEND);
        }else{
            file_put_contents($file, "\r\n", FILE_APPEND);
            file_put_contents($file, "failure:" . $data . "|sign:" . $sign, FILE_APPEND);
        }

    }

    /**
     * 同步响应操作
     */
    function respond_return()
    {
        //返回的数据处理
        $sign = trim($_POST['sign']);
        $result = $_POST;
        unset($result['sign']);
        ksort($result);
        $url = "";
        foreach ($result as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/respond.txt";
        file_put_contents($file, "\r\n", FILE_APPEND);
        file_put_contents($file, "return|data:" . $data . "|sign:" . $sign, FILE_APPEND);
        /* 验证签名 仅作基础验证*/
        if (IGD('Ysepay','Scanpay')->sign_check($sign, $data) == true) {
            echo "验证签名成功!";
        } else {
            echo '验证签名失败!';
        }
        /* 验证签名,并更改订单状态*/
//    if($this->sign_check($sign,$data)  != true){
//        echo "验证签名失败！";
//        exit;
//    }
//    if($result['trade_status'] == 'TRADE_SUCCESS'){
//        /* 改变订单状态 */
//        order_paid($result['out_trade_no']);
//        return true;
//    }else{
//        return false;
//    }

    }

    /**
     *  支付宝 扫码 异步响应操作
     */
    function scan_notify()
    {
        $pay =new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $result = $_POST;
        unset($result['sign']);
        ksort($result);
        $url = "";
        foreach ($result as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/scan_pay.txt";
        /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {

            if(strstr($data,"TRADE_SUCCESS")){
                $orderId = explode("=",explode("&",$data)[3])[1];  // 获取支付成功后的返回的订单号
                file_put_contents($file, "\r\n", FILE_APPEND);
                file_put_contents($file,$orderId."##SUCCESS##".$data,FILE_APPEND);
            }else{
                file_put_contents($file, "\r\n", FILE_APPEND);
                file_put_contents($file,"##FAILURE##".$data,FILE_APPEND);
            }
        } else {
            file_put_contents($file, "\r\n", FILE_APPEND);
            file_put_contents($file, "Validation failure!|notify|:" . $data . "|sign:" . $sign, FILE_APPEND);
        }
        echo 'success';
        exit;


    }
    /**
     * 异步响应操作
     */
    function respond_alipay_notify()
    {
        $pay =new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $result = $_POST;
        unset($result['sign']);
        ksort($result);
        $url = "";
        foreach ($result as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/alipay.txt";
        /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {

            if(strstr($data,"TRADE_SUCCESS")){
                file_put_contents($file, "\r\n", FILE_APPEND);
                file_put_contents($file,"##SUCCESS##".$data,FILE_APPEND);
            }else{
                file_put_contents($file, "\r\n", FILE_APPEND);
                file_put_contents($file,"##FAILURE##".$data,FILE_APPEND);
            }
        } else {
            file_put_contents($file, "\r\n", FILE_APPEND);
            file_put_contents($file, "Validation failure!|notify|:" . $data . "|sign:" . $sign, FILE_APPEND);
        }
        echo 'success';
        exit;

    }


    /**
     * 异步响应操作
     */
    function respond_notify()
    {
        $pay =new \Yse_pay();
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $result = $_POST;
        unset($result['sign']);
        ksort($result);
        $url = "";
        foreach ($result as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/alipay.txt";
        /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            $aa =explode("=",explode("&",$data)[8]);
            if($aa[1]=="TRADE_SUCCESS"){
                file_put_contents($file, "\r\n", FILE_APPEND);
                file_put_contents($file,$data."#  success",FILE_APPEND);
            }else{
                file_put_contents($file, "\r\n", FILE_APPEND);
                file_put_contents($file,$data."#  failure",FILE_APPEND);
            }
        } else {
            file_put_contents($file, "\r\n", FILE_APPEND);
            file_put_contents($file, "Validation failure!|notify|:" . $data . "|sign:" . $sign, FILE_APPEND);
        }
        echo 'success';
        exit;


    }


}
