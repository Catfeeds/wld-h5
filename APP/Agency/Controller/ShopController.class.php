<?php

namespace Agency\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 微商管理系统商家管理部分
 */
class ShopController extends BaseController {
	
	//首页
    public function index(){
    	$this->ucode = session('USER.ucode');

    	$ucode = session('USER.ucode');
        $userinfo = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $userinfo['data'];
    	$this->show();
    }

    //代理等级首页
    public function level()
    {
    	$this->ucode = session('USER.ucode');
        $parr['ucode'] = $this->ucode;
        $result = IGD('Agency', 'Store')->AgencyGrade($parr);
        $this->data = $result['data'];
    	$this->show();
    }

    //代理级别管理
    public function leveladd()
    {
    	$this->level = I('level');
    	$this->ucode = session('USER.ucode');
        $parr['ucode'] = $this->ucode;
        $parr['grade'] = $this->level;
        $result = IGD('Agency', 'Store')->FindOneGrade($parr);
        $this->data = $result['data'];
    	$this->show();
    }

    //保存等级
    public function saveLevel()
    {
    	$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        $parr['ucode'] = session('USER.ucode');
        $parr['grade_name'] = $data['name'];
        if (count(arr_split_zh($parr['grade_name']))>5) {
            $this->ajaxReturn(Message(3000,'等级名不能超过五个字！'));
        }
        $parr['jy_money'] = $data['money'];
        $parr['desc'] = $data['desc'];
        $parr['grade'] = $data['levelid'];
        $result = IGD('Agency', 'Store')->AddAgencyGrade($parr);
        $this->ajaxReturn($result);
    }

    //分销商管理
    public function reseller()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Agency', 'Store')->GetAgencyInfo($parr);
        $this->reseinfo = $result['data'];
        $this->show();
    }

    //分销商列表
    public function AgencyMember(){
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Agency', 'Store')->AgencyMember($parr);
        $this->ajaxReturn($result);
    }

    //分销商详情
    public function reselinfo()
    {
        $this->acode = I('acode');
        $parr['agentucode'] = $this->acode;
        $result = IGD('Agency', 'Store')->GetOneReseller($parr);
        $this->reseinfo = $result['data'];
        $this->show();
    }

    //分销商代理商品列表
    public function AgencyMemberProduct(){
        $parr['ucode'] = session('USER.ucode');
        $parr['agentucode'] = I('agentucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Agency', 'Store')->AgencyMemberProduct($parr);
        $this->ajaxReturn($result);
    }

    //等级详情
    public function levelinfo()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['agentucode'] = I('acode');
        $result = IGD('Agency', 'Store')->AgencyGrade($parr);
        $this->data = $result['data'];
        $this->show();
    }

    //分销商购买全部详情
    public function prdetail()
    {
        $this->pcode = I('pcode');
        $this->acode = I('acode');

        $parr['ucode'] = session('USER.ucode');
        $parr['agentucode'] = $this->acode;
        $parr['pcode'] = $this->pcode;
        $result = IGD('Agency', 'Store')->GetOneProductinfo($parr);
        $this->data = $result['data'];
        $this->show();
    }

    //代理商购买全部记录
    public function BuyProductList(){
        $parr['ucode'] = session('USER.ucode');

        $parr['pcode'] = I('pcode');
        $parr['agentucode'] = I('acode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $result = IGD('Agency', 'Store')->BuyProductList($parr);
        $this->ajaxReturn($result, 'JSON');
    }
}