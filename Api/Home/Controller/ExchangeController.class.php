<?php

namespace Home\Controller;

use Base\Controller\CheckController;

/**
 * 兑换中心
 */

class ExchangeController extends CheckController {
	
    /*兑换首页*/
    public function GetExchangeList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
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

    /*兑换详情*/
    public function GetExchangeInfo(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['sid'] = I('sid');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('Exchange', 'Newact')->GetExchangeInfo($parr);
        $this->ajaxReturn($result);
    }

    //线上兑换
    public function OnlineExchange()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['sid'] = I('sid');
        $parr['addressid'] = I('addressid');
        $parr['postscript'] = I('postscript');
        $result = IGD('Exchange', 'Newact')->OnlineExchange($parr);
        $this->ajaxReturn($result);
    }

    //线下兑换
    public function OfflineExchange()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
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

}