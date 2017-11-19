<?php

namespace Home\Controller;

use Base\Controller\BaseController;

/**
 *  我的优惠券
 */
class CpackageController extends BaseController {
	
    //获取我的卡劵包列表
    public function MyCouponCard()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        
        $parr['ucode'] = $ucode;
        $parr['type'] = I('type');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->MyCouponCard($parr);
        $this->ajaxReturn($result);
    }

    //获取可抵扣订单的卡劵
    public function CouponUseList()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        
        $parr['ucode'] = $ucode;
        $parr['orderid'] = I('orderid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->CouponUseList($parr);
        $this->ajaxReturn($result);
    }
      
    /*卡券详情*/ 
    public function CouponInfo(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        
        $parr['ucode'] = $ucode;
        $parr['bid'] = I('bid');
        $result = IGD('Coupon','Newact')->CouponInfo($parr);
        $this->ajaxReturn($result);
    } 
}