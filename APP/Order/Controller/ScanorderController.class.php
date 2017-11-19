<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 扫码订单
 */
class ScanorderController extends BaseController {
	
    public function __construct() {
        parent::__construct();
        header('Content-Type: text/html;charset=utf-8');
    }
    public function index() {

        $this->display('scanlist');
    }

    //扫码支付列表页面
    public function scanlist()
    {
        $this->statu = I('statu');
        $this->show();
    }

    // 扫码支付详情
    public function scandetail()
    {
        $parr['ncode'] = I('ncode');
        $result = IGD('Scanpayorder','Scanpay')->ScanpayOrderInfo($parr);
        $this->data = $result['data'];
        $this->show();
    }

    //扫码支付订单列表
    public function ScanpayOrderList()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['state'] = I('state');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Scanpayorder','Scanpay')->ScanpayOrderList($parr);
        $this->ajaxReturn($result);
    }

    //评价扫码订单
    public function scaneval()
    {
         if (IS_POST && session('time') == $_POST['time']) {
            echo '<script type="text/javascript" src="'.C('TMPL_PARSE_STRING.__COMMON__').'/js/jquery.js"></script><script type="text/javascript" src="'.C('TMPL_PARSE_STRING.__COMMON__').'/js/jquery_dialog.js"></script><link rel="stylesheet" type="text/css" href="'.C('TMPL_PARSE_STRING.__COMMON__').'/css/jquery_dialog.css">';
            if ($_FILES) {
                $imgresult = uploadimg('score');
                if ($imgresult['code'] != 0) {
                    $resultdata = $imgresult['msg'];
                    echo "<script >JqueryDialog.Show('$resultdata','frame')</script>";
                    return;
                }
                $parr['imglist'] = array_values($imgresult['data']);
            }

            $parr['ncode'] = I('ncode');
            $parr['score'] = I('score');
            $parr['content'] = I('content');
            $result = IGD('Scanpayorder','Scanpay')->EvaluateScanOrder($parr);
            $resultdata = $result['msg'];
            if ($result['code'] != 0) {
                echo "<script >JqueryDialog.Show('$resultdata','frame')</script>";
                return;
            }
            session('time', null);
            echo "<script>JqueryDialog.Show('$resultdata','frame')</script>";
            echo "<script>setTimeout(function(){window.parent.location.href = '".GetHost(1)."/index.php/Order/Scanorder/index';},2000);</script>";
            return;
        }
        $this->ncode = I('ncode');

        $parr['ncode'] = I('ncode');
        $result = IGD('Scanpayorder','Scanpay')->ScanpayOrderInfo($parr);
        $this->detail = $result['data'];
        $time = time();
        session('time', $time);    // 防止重复提交
        $this->assign('time', $time);
        $this->show();
    }

    
}