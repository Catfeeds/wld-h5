<?php

namespace Balance\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 结算中心模块的待结算
 */
class StaysettleController extends BaseController {
	
  	// 首页
    public function index()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Settlement','User')->Summary($parr);
        $this->data = $result['data'];

    	$this->show();
    }

    //查看待结算记录
    public function GetStaydata()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pagesize'] = 15;
        $parr['pageindex'] = I('pageindex');
        $result = IGD('Settlement','User')->GetYsMoneyLog($parr);
        $this->ajaxReturn($result);
    }

    /*待结算记录详情*/
    public function detail()
    {
        $this->apptype = I('apptype');
        if (!$this->apptype) {
            if (is_weixin()) {
                $this->apptype = 4;
            } else {
                $this->apptype = get_device_type();
            }
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = I('id');
        $result = IGD('Settlement','User')->GetYsDetails($parr);
        $budget = $result['data'];
        $source = $budget['c_source'];
        if ($source == 1) {  //普通商城订单(c_orderid)
            $this->showtype = 1;
//            $result = IGD('Settlement','User')->ordertemplate($parr);
//            if ($result['code'] != 0) {
//                $result = IGD('Settlement','User')->details($parr);
//            }
        } else if ($source == 5 || $source == 13 || $source == 14) { //普通商城优惠类(c_detailid)
            $this->showtype = 2;
//            $result = IGD('Settlement','User')->details($parr);
        } else if ($source == 4) {  //小蜜商城订单(c_orderid)
            $this->showtype = 3;
//            $result = IGD('Settlement','User')->supplierordert($parr);
        } else if ($source == 15) { //小蜜商城优惠类(c_detailid)
            $this->showtype = 4;
//            $result = IGD('Settlement','User')->supplierdetails($parr);
        } else if ($source == 9 || $source == 12) {   //扫码订单
            $this->showtype = 5;
//            $result = IGD('Settlement','User')->scanpaytemplate($parr);
        } else if ($source == 6) {    //提现
            $this->showtype = 6;
//            $result = IGD('Settlement','User')->tixiantemplate($parr);
        } else if ($source == 16) {  //普通商城退款
            $this->showtype = 7;
//            $result = IGD('Settlement','User')->orderrefundinfor($parr);
        } else if ($source == 17) {  //小蜜商城退款
            $this->showtype = 8;
//            $result = IGD('Settlement','User')->supplierorderrefundinfor($parr);
        } else if ($source == 2) {   //后台
            $this->showtype = 9;
        } else {   //活动
            $this->showtype = 10;
//            $parr['joinaid'] = $budget['c_joinaid'];
//            $result = IGD('Settlement','User')->findActivty($parr);
        }
        $s_time = date('Y-m-d H:i:s');
        $result['data']['s_time'] = $s_time;
        $this->source = $source;
        $this->budget = $budget;
        $this->data = $result['data'];
//         var_dump($this->showtype);
//        var_dump($this->data);die;
//        var_dump($source);
        $this->show();
    }


}