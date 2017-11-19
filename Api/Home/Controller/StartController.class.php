<?php

namespace Home\Controller;

use Base\Controller\BaseController;

/**
 * 发现首页接口
 */
class StartController extends BaseController {

    //点击发现接口
    public function index() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['shop'] = I('shop');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['pageindex'] = I('pageindex');
        $parr['version'] = I('version');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $Activity = IGD('Start', 'Newact');
        $result = $Activity->StartClick($parr);

        $this->ajaxReturn($result);
    }

    //领取气球
    public function ReceiveBox() {
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['joinaid'] = I('joinaid');
        $parr['acode'] = I('acode');
        $result = IGD('Index', 'Newact')->ReceiveBox($parr);
        $this->ajaxReturn($result);
    }

    // 领取气球
    public function ReceiveAir() {
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['joinaid'] = I('joinaid');
        $parr['acode'] = I('acode');
        $result = IGD('Index', 'Newact')->ReceiveAir($parr);
        $this->ajaxReturn($result);
    }

  
}
