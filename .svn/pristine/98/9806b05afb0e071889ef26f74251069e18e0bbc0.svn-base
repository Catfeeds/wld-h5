<?php

namespace Home\Controller;

use Base\Controller\CheckController;

/**
 *  购物车信息
 *
 */
class ShopcarController extends CheckController {

    //获取购物车信息
    public function Getcar() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $Coalition = IGD('Shoppingcar', 'User');
        $result = $Coalition->GetCar($ucode);
        $this->ajaxReturn($result);
    }

    //添加购物车
    public function Addcar() {

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['mcode'] = I('mcode');
        $parr['pucode'] = I('pucode');
        $parr['pmname'] = I('pmname');
        $parr['num'] = I('num');

        $Coalition = IGD('Shoppingcar', 'User');
        $result = $Coalition->AddCar($parr);
        $this->ajaxReturn($result);
    }

    public function Delecar() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['mcode'] = I('mcode');

        $Coalition = IGD('Shoppingcar', 'User');
        $result = $Coalition->DeleCar($parr);
        $this->ajaxReturn($result);
    }

    //清除购物车
    public function Clearcar() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $Coalition = IGD('Shoppingcar', 'User');
        $result = $Coalition->Clearcar($parr);
        $this->ajaxReturn($result);
    }

}
