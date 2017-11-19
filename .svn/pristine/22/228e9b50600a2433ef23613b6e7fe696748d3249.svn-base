<?php

namespace Balance\Controller;

use Think\Controller;
// use Base\Controller\BaseController;

/**
 * 结算中心模块
 */
class ApplyforController extends Controller {

    //后台银盛代付回调
	public function respond_notify()
    {
    	vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
       
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $params = $_POST;
        unset($params['sign']);
        ksort($params);
        $url = "";
        foreach ($params as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/daifu.txt";
        /* 验证签名 仅作基础验证*/
	   // file_put_contents($file,$data."#  failure",FILE_APPEND);
	   if ($pay->sign_check($sign,$data) == true) {
            if($params['trade_status'] == "TRADE_SUCCESS"){
                $w['c_tx_code'] = $params['out_trade_no'];
	       		$save_data['c_thirdparty_code'] = $params['trade_no'];
                $save_data['c_state'] = 1;
	       		$result = M('Users_drawing')->where($w)->save($save_data);
	       		// if (!$result) {
	       		// 	$w['c_tx_code'] = $params['out_trade_no'];
		       	// 	$save_data['c_state'] = 0;
		       	// 	$result = M('Users_drawing')->where($w)->save($save_data);
	       		// 	echo "error";exit();
	       		// }
	       		echo "success";exit();
            }else{
            	$w['c_tx_code'] = $params['out_trade_no'];
	       		$save_data['c_state'] = 0;
                $save_data['c_remarks'] = $params['trade_status_description'];
	       		$result = M('Users_drawing')->where($w)->save($save_data);
                echo "error";exit();
            }
        } else {
            if (!empty($params['out_trade_no'])) {
                $w['c_tx_code'] = $params['out_trade_no'];
                $save_data['c_state'] = 0;
                $save_data['c_remarks'] = '支付回调出错';
                $result = M('Users_drawing')->where($w)->save($save_data);
            }
            echo "error";exit();
        }
    }

    //结算中心银盛代付回调
    public function pay_respond_notify()
    {
        vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
       
        //返回的数据处理
        @$sign = trim($_POST['sign']);
        $params = $_POST;
        unset($params['sign']);
        ksort($params);
        $url = "";
        foreach ($params as $key => $val) {
            if ($val) $url .= $key . '=' . $val . '&';
        }
        $data = trim($url, '&');
        /*写入日志*/
        $file = "data/daifu.txt";
        /* 验证签名 仅作基础验证*/
       // file_put_contents($file,$data."#  failure",FILE_APPEND);
       if ($pay->sign_check($sign,$data) == true) {
            if($params['trade_status'] == "TRADE_SUCCESS"){
                $w['c_tx_code'] = $params['out_trade_no'];
                $save_data['c_thirdparty_code'] = $params['trade_no'];
                $save_data['c_state'] = 1;
                $result = M('Users_drawing')->where($w)->save($save_data);
                // if (!$result) {
                //  $w['c_tx_code'] = $params['out_trade_no'];
                //  $save_data['c_state'] = 0;
                //  $result = M('Users_drawing')->where($w)->save($save_data);
                //  echo "error";exit();
                // }
                
                //添加代付手续费
                // $drawinfo = M('Users_drawing')->where($w)->find();
                // $result = IGD('Ysepay','Scanpay')->PayFeeTouser($drawinfo);
                echo "success";exit();
            }else{
                $w['c_tx_code'] = $params['out_trade_no'];
                $save_data['c_state'] = 0;
                $save_data['c_remarks'] = $params['trade_status_description'];
                $result = M('Users_drawing')->where($w)->save($save_data);

                //添加代扣提现金额
                $drawinfo = M('Users_drawing')->where($w)->find();
                $arr['type'] = 1; // 1  实时结算  2 按日结算  3 按月结算
                $arr['settled'] = 1;   //1已结算 2待结算
                $arr['settledtime'] = gdtime();
                $arr['ucode'] = $drawinfo['c_ucode'];
                $arr['orderid'] = CreateOrder("f");
                $arr['money'] = $drawinfo['c_money'];
                $arr['source'] = 6;
                $arr['key'] = $params['out_trade_no'];
                $arr['desc'] = '提现失败,代扣金额由平台代付提现';
                $result = IGD('Splitting','Order')->CreateRecord($arr);
                echo "error";exit();
            }
        } else {
            if (!empty($params['out_trade_no'])) {
                $w['c_tx_code'] = $params['out_trade_no'];
                $save_data['c_state'] = 0;
                $save_data['c_remarks'] = '支付回调出错';
                $result = M('Users_drawing')->where($w)->save($save_data);

                //添加代扣提现金额
                $drawinfo = M('Users_drawing')->where($w)->find();
                $arr['type'] = 1; // 1  实时结算  2 按日结算  3 按月结算
                $arr['settled'] = 1;   //1已结算 2待结算
                $arr['settledtime'] = gdtime();
                $arr['ucode'] = $drawinfo['c_ucode'];
                $arr['orderid'] = CreateOrder("f");
                $arr['money'] = $drawinfo['c_money'];
                $arr['source'] = 6;
                $arr['key'] = $params['out_trade_no'];
                $arr['desc'] = '提现失败,代扣金额由平台代付提现';
                $result = IGD('Splitting','Order')->CreateRecord($arr);
                echo "error";exit();
            }
            echo "error";exit();
        }
    }
}