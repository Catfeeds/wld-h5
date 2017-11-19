<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**收银管理**/
class CashierController extends BaseController {

	//首页
	public function index(){

		$this->display();
	}

	//收银员
	public function cstaff(){

		$this->display();
	}

	//获取收银员列表
    public function GetCashierList()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Cashier','Store')->GetCashierList($parr);
        $this->ajaxReturn($result);
    }

	//添加收银员
	public function addstaff(){

		$this->display();
	}

	//获取用户信息
    public function UserInfo()
    {
        $phone = I('phone');
        $result = IGD('Cashier','Store')->UserInfo($phone);
        $this->ajaxReturn($result);
    }

	//邀请收银员操作
	public function InviteCashier()
	{
		$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
		$parr['name'] = $data['name'];
		$parr['phone'] = $data['phone'];
		$result = IGD('Cashier','Store')->InviteCashier($parr);
		$this->ajaxReturn($result);
	}

	//停用与启用(删除)收银员
	public function OptionCashier()
	{
		$parr['ucode'] = session('USER.ucode');
		$parr['cashid'] = I('cashid');
		$parr['delete'] = I('delete1');		//1删除，2不删除
		$parr['status'] = I('status');		//(1启用，2停用)
        $result = IGD('Cashier','Store')->OptionCashier($parr);
        $this->ajaxReturn($result);
	}

	//收款明细
	public function caccount(){
		$this->cashid = I('cid');
		$this->display();
	}

	//收银台管理
	public function cashierdesk(){
		$parr['ucode'] = session('USER.ucode');
		$result = IGD('Cashier','Store')->GetDeskList($parr);
		$this->desklist = $result['data'];
		$this->countlist = count($result['data']);
		$this->display();
	}
	
	/*获取收银台列表*/
	public function GetDeskList(){
		$parr['ucode'] = session('USER.ucode');
		$result = IGD('Cashier','Store')->GetDeskList($parr);
		$this->ajaxReturn($result);
	}
	
	//添加收银台
	public function AddDesk()
	{
		$parr['name'] = I('name');
		$parr['ucode'] = session('USER.ucode');
		$result = IGD('Cashier','Store')->AddDesk($parr);
		$this->ajaxReturn($result);
	}

	//下班
    public function LeaveCashierDesk()
    {
        $parr['ucode'] = I('cashucode');
        $parr['cashid'] = I('cashid');
        $parr['deskid'] = I('deskid');
        $result = IGD('Cashier','User')->LeaveCashierDesk($parr);
        $this->ajaxReturn($result);
    }

	//收银台明细
	public function cincome(){
		$parr['ucode'] = session('USER.ucode');
		$parr['deskid'] = I('deskid');
		$parr['info'] = 1;
		$result = IGD('Cashier','Store')->GetDeskInfo($parr);			
		$this->deskdata = $result['data'];	
		
		$this->deskid = I('deskid');	
		$this->display();
	}

	//收银台统计数据
	public function LineChartdata()
	{
		$parr['timetype'] = I('timetype');		//1-过去7天,2-过去30天
		$parr['deskid'] = I('deskid');
		$parr['ucode'] = session('USER.ucode');
		$result = IGD('Cashier','Store')->LineChartdata($parr);
		$this->ajaxReturn($result);
	}

	//收银台(收银员)收支明细
	public function GetDeskIncome()
	{
		$parr['cashid'] = I('cashid');		
		$parr['deskid'] = I('deskid');
		$parr['time'] = I('time');		//(2017-06,2017-06-12)
		$parr['pageindex'] = I('pageindex');
		$parr['pagesize'] = 20;
		$parr['ucode'] = session('USER.ucode');
		$result = IGD('Cashier','Store')->GetDeskIncome($parr);
		$this->ajaxReturn($result);
	}

	//收银台二维码
	public function ewcode(){
		
		$parr['ucode'] = session('USER.ucode');
		$parr['deskid'] = I('deskid');
		$result = IGD('Cashier','Store')->GetDeskInfo($parr);			
		$this->deskdata = $result['data'];
		
		$this->display();
	}



}