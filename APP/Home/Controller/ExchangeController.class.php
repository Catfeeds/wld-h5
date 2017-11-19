<?php

namespace Home\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 兑换中心
 */

class ExchangeController extends BaseController {
	/*兑换首页*/
    public function index(){

    	$this->show();
    }

    /* 到店领取兑换 */
    public function exget(){
        $this->sid = I('sid');
        $this->ucode = session('USER.ucode');

        $parr['ucode'] = $this->ucode;
        $parr['sid'] = $this->sid;
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('Exchange', 'Newact')->GetExchangeInfo($parr);
        $this->info = $result['data'];

    	$this->show();
    }

    /* 到店领取兑换-地址 */
    public function exaddress(){
        $this->sid = I('sid');
        $this->ucode = session('USER.ucode');

        $parr['ucode'] = $this->ucode;
        $parr['sid'] = $this->sid;
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('Exchange', 'Newact')->GetExchangeInfo($parr);
        $this->info = $result['data'];

        // 获取地址列表
        $parr['ucode'] = $this->ucode;
        $addressinfo = IGD('User', 'User');
        $addressdata = $addressinfo->GetUserAddress($parr);
        $this->addresslist = $addressdata['data'];

        /*获取默认地址*/
        $defaultaddr = IGD('User', 'User');
        $defaults = $defaultaddr->Getdefaultaddress($parr);
        $this->defaultdz = $defaults['data'];

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province=$province_list;

    	$this->show();
    }

    /* 兑换成功 */
    public function exsuccess(){

    	$this->show();
    }

    /*兑换首页*/
    public function GetExchangeList(){
        $this->ucode = session('USER.ucode');

        $parr['ucode'] = $this->ucode;
        $parr['type'] = I('type');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('Exchange', 'Newact')->GetExchangeList($parr);
        $this->ajaxReturn($result);
    }


    //线上兑换
    public function OnlineExchange()
    {
        $this->ucode = session('USER.ucode');

        $parr['ucode'] = $this->ucode;
        $parr['sid'] = I('sid');
        $parr['addressid'] = I('addressid');
        $parr['postscript'] = I('postscript');
        $result = IGD('Exchange', 'Newact')->OnlineExchange($parr);
        $this->ajaxReturn($result);
    }

    //线下兑换
    public function OfflineExchange()
    {
        $this->ucode = session('USER.ucode');

        $parr['ucode'] = $this->ucode;
        $parr['sid'] = I('sid');
        $parr['status'] = I('status');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }
        $result = IGD('Exchange', 'Newact')->OfflineExchange($parr);
        $this->ajaxReturn($result);
    }


    /* 分享气球 */
    public function shareballoon(){

        $this->show();
    }

    /* 分享挖到宝了 */
    public function sharechests(){

        $this->show();
    }

}