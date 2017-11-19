<?php
namespace Pay\Controller;

use Base\Controller\BaseController;

class TestController extends BaseController
{
    function _initialize(){
        Vendor('Ysepay.demo');
    }

    // pc 订单支付
    public function test()
    {
        $pay =new \demo();
        $orderid =$pay->datetime2string(date('Y-m-d H:i:s'));

        $result =$pay->get_code($orderid);
        // $result =$pay->get_dfjj($orderid);
        echo $result;

    }
  // 查看账户余额
    public function get_money()
    {
        $pay =new \demo();
        $parr['partner_id'] ="wld17375717292";//"wld17375717292";
        $parr['user_code'] ="wld17375717292";//"801551088883618";
        $parr['user_name'] ="银盛支付商户测试公司";//"长沙微领地网络科技有限公司";
        $parr['account_no'] ="";
        $data =$pay->get_money();
        $result = $pay->curl_https($data);
        dump($result);
    }

    // 单笔快速支付
    public function quick_pay(){
        $pay =new \demo();
        $orderid =$pay->datetime2string(date('Y-m-d H:i:s'));
        $data =$pay->get_dfjj($orderid);
        $result =$pay->curl_https_df($data);

        dump($result);

    }

    // 调起微信支付 获取相关参数

    public function weixin_pay(){
        $pay =new \demo();
        $orderId =$pay->datetime2string(date('Y-m-d H:i:s'));
        $data =$pay->get_weixin($orderId);
        $result =$pay->curl_https_pay($data);
        dump($result);

    }

    // app 调起支付
    public function app_pay(){
        $pay =new \demo();
        $orderId =$pay->datetime2string(date('Y-m-d H:i:s'));
        $data =$pay->app_pay($orderId);
        $result =$pay->curl_https_pay($data);
        dump($orderId);
        dump($result);
    }

    // 支付宝 H5 支付
    public function alipay_H5(){
        $pay =new \demo();
        $orderId =$pay->datetime2string(date('Y-m-d H:i:s'));
        $parr['partner_id'] ="wld17375717292";
        $parr['notify_url'] =GetHost()."/api.php/Pay/Test/respond_notify";
        $parr['return_url'] =GetHost()."/api.php/Pay/Test/return_alipay";
        $parr['out_trade_no'] =$orderId;
        $parr['subject'] ="测试商品";
        $parr['total_amount'] ="1.00";
        $parr['seller_id'] ="wld17375717292";
        $parr['seller_name'] ="长沙微领地网络科技有限公司";
        $data =$pay->alipay_H5($parr);
        //$result =$pay->curl_https_pay($data);
        //dump($orderId);
       echo  $data;
    }

    // 扫码订单
    public function scan_order(){
        $pay =new \demo();
        $orderId =$pay->datetime2string(date('Y-m-d H:i:s'));
        $data =$pay->alipay_H5($orderId);
        echo $data;
    }


    public function search_order(){
        $pay =new \demo();
        //$parr['out_trade_no'] ="20170906101447";
        $data =$pay->searchOrder();
        $result =$pay->curl_https_df($data);
        dump($result);
    }

    // 支付宝 H5 支付 跳转
    function return_alipay(){
        $pay =new \demo();
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
     * 异步响应操作
     */
    function respond_notify()
    {
        $pay =new \demo();
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
        $file = "data/notify.txt";
        /* 验证签名 仅作基础验证*/
        if ($pay->sign_check($sign,$data) == true) {
            file_put_contents($file, "\r\n", FILE_APPEND);
            file_put_contents($file,$data,FILE_APPEND);
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
