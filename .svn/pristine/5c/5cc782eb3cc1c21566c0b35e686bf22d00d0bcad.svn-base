<?php

namespace Activity\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 卡券管理
 */
class CouponController extends BaseController {
	
	/*首页*/
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
        $this->statu = I('statu');
    	$this->display();
    }

    //获取卡劵列表
    public function CouponList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['type'] = I('type');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->CouponList($parr);
        $this->ajaxReturn($result);
    }

    /*添加卡券信息页面*/
    public function addcoupon(){
        $this->joinaid = I('joinaid');
        $backurl = I('url');
        $this->backurl = decodeurl($backurl);
    	$this->display();
    }

    //添加与编辑卡劵
    public function AddCouponCard()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['joinaid'] = $data['joinaid'];
        $parr['ucode'] = session('USER.ucode');
        $parr['cid'] = $data['cid'];        
        $parr['totalnum'] = $data['num'];
        $parr['num'] = $data['num'];
        $parr['actnum'] = $data['num'];
        $parr['sign'] = 2;
        $parr['type'] = $data['type'];
        if ($parr['type'] == 1) {
            $parr['money'] = $data['money1'];
            $parr['limit_money'] = $data['limit_money1'];
            $parr['name'] = $parr['money'].'元代金劵';
            if (floor($parr['money'])!=$parr['money']) {
                $this->ajaxReturn(Message(3000,'请输入整数劵面金额'));
            }if(floor($parr['money'])>floor($parr['limit_money'])){
            	$this->ajaxReturn(Message(3000,'用券最低订单金额不能小于券面金额'));
            }
        } else if ($parr['type'] == 2) {
            $parr['money'] = $data['money2'];
            $parr['limit_money'] = $data['limit_money2'];
            $parr['name'] = $parr['money'].'折折扣劵';
            if(!preg_match("/^[0-9]+(\.[0-9]{1})?$/",$parr['money'])) {
                $this->ajaxReturn(Message(3000,'请输入带有一位小数折扣比例'));
            }
        }
        if ($data['checksign'] != 1) {
            $parr['pcodearr'] = $data['pcodestr'];
        }

        if (floor($parr['limit_money'])!=$parr['limit_money']) {
            $this->ajaxReturn(Message(3000,'请输入整数限制金额'));
        }

        $qian = array("/","年","月","日");
        $hou = array("-","-","-","-");
        $parr['starttime'] = str_replace($qian, $hou, $data['hid_starttime']);
        $parr['endtime'] = str_replace($qian, $hou, $data['hid_endtime']);
        if (empty($parr['num'])||empty($parr['limit_money'])||
            empty($parr['type'])||empty($parr['money'])||!strtotime($parr['starttime'])||!strtotime($parr['endtime'])) {
            $this->ajaxReturn(Message(3000,'信息未完善或时间格式不对'));
        }

        if (strtotime($parr['starttime']) >= strtotime($parr['endtime'])) {
            $this->ajaxReturn(Message(3001,'结束时间不能小于开始时间'));
        }

        if (strtotime('+3 days') >= strtotime($parr['endtime'])) {
            $this->ajaxReturn(Message(3001,'结束时间需大于当前时间3天'));
        }
        $result = IGD('Coupon','Newact')->AddCouponCard($parr);
        $this->ajaxReturn($result);
    }
    
    /*卡劵详情页面*/
    public function kqdetail(){   	
	   	$this->cid = I('cid');
	   	
        $parr['cid'] = $this->cid;
        $result = IGD('Coupon','Newact')->GetCouponInfo($parr);
        $this->data = $result['data'];

        $result = IGD('Coupon','Newact')->ActivityCoupon($parr);
        $this->act = $result['data']['list'];
	   	$this->display();
    }

    //删除卡劵
    public function DeleteCoupon()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['cid'] = I('cid');   
        $result = IGD('Coupon','Newact')->DeleteCoupon($parr);
        $this->ajaxReturn($result);
    }
   
	/*领取详情*/
	public function getdetail(){
        $this->cid = I('cid');
        
        $parr['cid'] = $this->cid;
        $result = IGD('Coupon','Newact')->GetCouponInfo($parr);
        $this->data = $result['data'];

        $result = IGD('Coupon','Newact')->ActivityCoupon($parr);
        $this->act = $result['data']['list'];

		$this->display();
	}

    //查询卡劵领取与使用详情
    public function CouponReceiveInfo()
    {
        $parr['pageindex'] = I('pageindex');
        if ($parr['pageindex'] > 1) {
            $this->ajaxReturn(Message(3000,'数据为空'));
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['cid'] = I('cid'); 
        $parr['type'] = I('type');  
        $result = IGD('Coupon','Newact')->CouponReceiveInfo($parr);
        $this->ajaxReturn($result);
    }

    /*卡券管理*/
    public function help(){
        
        $this->display();
    }

	
}