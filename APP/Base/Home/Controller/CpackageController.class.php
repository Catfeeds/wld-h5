<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 卡券包，我的优惠券
 */
class CpackageController extends BaseController {
	
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');

        $type = I('type');
        $this->type = 1;
        if ($type == 2) {
            $this->type = 2;
        }
	   	
    	$this->show();
    }  

    //获取我的列表
    public function MyCouponCard()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['type'] = I('type');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->MyCouponCard($parr);
        $this->ajaxReturn($result);
    }
      
    /*卡券详情*/ 
    public function detail(){
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
	   	$this->bid = I('bid');

        $parr['ucode'] = session('USER.ucode');
        $parr['bid'] = $this->bid;
        $result = IGD('Coupon','Newact')->CouponInfo($parr);
        $this->data = $result['data'];
    	$this->show();
    } 

    /*帮助*/ 
    public function help(){
        
        $this->show();
    }  
}