<?php

namespace Home\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 收银员
 */

class CashierController extends BaseController {
	/*首页*/
    public function index(){
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        $cashinfo = $result['data'];

        if ($cashinfo['c_work'] == 1) {
            $this->redirect('offcashier');die;
        }

        $parr['acode'] = $cashinfo['c_acode'];
        $result = IGD('Cashier','User')->GetCashierDesk($parr);
        $this->deskinfo = $result['data'];

        $this->cashinfo = $cashinfo;
    	$this->show();
    }

    //同意成为收银员
    public function AgreeCashier()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $data['acode'];
        $parr['name'] = $data['name'];
        $result = IGD('Cashier','User')->AgreeCashier($parr);
        $this->ajaxReturn($result);
    }

    //选择收银台上班
    public function CheckCashierDesk()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['deskid'] = I('deskid');
        $result = IGD('Cashier','User')->CheckCashierDesk($parr);
        $this->ajaxReturn($result);
    }

    /*下班*/
    public function offcashier(){

        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        $cashinfo = $result['data'];
        if ($cashinfo['c_work'] == 2) {
            $this->redirect('index');die;
        }

        $this->cashinfo = $cashinfo;
    	$this->show();
    }

    //下班
    public function LeaveCashierDesk()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['cashid'] = I('cashid');
        $parr['deskid'] = I('deskid');
        $result = IGD('Cashier','User')->LeaveCashierDesk($parr);
        $this->ajaxReturn($result);
    }

    /*我的*/
    public function mycashier(){

    	$this->show();
    }

    //收银员信息
    public function myinfo()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        $cashinfo = $result['data'];
        $this->cashinfo = $cashinfo;
        $this->show();
    }

    //签到记录
    public function signinfo()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        $cashinfo = $result['data'];
        $this->cashinfo = $cashinfo;
        $this->show();
    }

    //获取上下班记录
    public function SignLog()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['cashid'] = I('cashid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Cashier','User')->SignLog($parr);
        $this->ajaxReturn($result);
    }

    //收款记录
    public function mlog()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        $cashinfo = $result['data'];
        $this->cashinfo = $cashinfo;
        $this->show();
    }

    //获取收款记录
    public function GetdateLog()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['datetime'] = I('datetime');
        $parr['cashid'] = I('cashid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Cashier','User')->GetdateLog($parr);
        $this->ajaxReturn($result);
    }

    //备注
    public function OrderRemarks()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['ncode'] = I('ncode');
        $parr['cashid'] = I('cashid');
        $parr['desc'] = I('desc');
        $result = IGD('Cashier','User')->OrderRemarks($parr);
        $this->ajaxReturn($result);
    }

    //收款详情
    public function sdetail()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['ncode'] = I('ncode');
        $parr['cashid'] = I('cashid');
        $result = IGD('Cashier','User')->CashierOrderInfo($parr);
        $this->data = $result['data'];
        $this->show();
    }
	
	/*同意成为收银员*/
	public function ask(){
       
        $parr['ucode'] = session('USER.ucode');
        $parr['askid'] = I('askid');

        //获取邀请详情
        $result = IGD('Ask','App')->GetAskInfo($parr);
        $this->data = $result['data'];
        
		$this->display();
	}
}