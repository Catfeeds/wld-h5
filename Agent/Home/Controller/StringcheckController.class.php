<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  串码审核管理
 */
class StringcheckController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

   	// 列表
	public function index()
	{
		$parr['ucode'] = session('_ADMIN_UCODE');
		$result = D('Codecheck','Service')->GetCheckNum($parr);
		$this->info = $result['data'];
		$this->display();
	}

	//查询信息列表
	public function GetCodeCheckList()
	{
		$parr['pageindex'] = I('pageindex');
		$parr['pagesize'] = 20;
		$parr['ucode'] = session('_ADMIN_UCODE');
		$result = D('Codecheck','Service')->GetCodeCheckList($parr);
		$this->ajaxReturn($result);
	}

	//审核激活码
	public function CheckCode()
	{
		$parr['ucode'] = session('_ADMIN_UCODE');
		$result = D('Common','Service')->GetStatuMessage($parr);
		if ($result['code'] != 0) {
			$this->ajaxReturn($result);
		}

		$parr['cid'] = I('cid');
		$parr['isfree'] = I('isfree');
		$result = D('Codecheck','Service')->CheckCode($parr);
		$this->ajaxReturn($result);
	}

}