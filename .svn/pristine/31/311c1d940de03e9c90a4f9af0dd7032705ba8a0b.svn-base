<?php

namespace Home\Controller;

use Base\Controller\CheckController;

/**
 *实体店铺  购物车信息
 *
 */
class StorecarController extends CheckController {

    //获取某个用户在某个店铺的购物车商品总数量
    public function Getcount() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');

        $Coalition = IGD('Storecar', 'User');
        $result = $Coalition->GetCount($parr);
        $this->ajaxReturn($result);
    }

    //获取某个商品在购物车数量
    public function Getprocount() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');

        $Coalition = IGD('Storecar', 'User');
        $result = $Coalition->Getprocount($parr);
        $this->ajaxReturn($result);
    }

    //获取购物车信息
    public function Getcar() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');

        $Coalition = IGD('Storecar', 'User');
        $result = $Coalition->GetCar($parr);
        $this->ajaxReturn($result);
    }

    //添加购物车
    public function Addcar() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $num = I('num');
        $acode = I('acode');

        $parr['ucode'] = $ucode;
        $parr['acode'] = $acode;
        $parr['pcode'] = I('pcode');
        $parr['num'] = $num;
        $parr['pucode'] = I('pucode');

        $Coalition = IGD('Storecar', 'User');
        if($num == 0){
            $result = $Coalition->DeleCar($parr);
        }else{
            $result = $Coalition->AddCar($parr);
        }

        $this->ajaxReturn($result);
    }

    public function Delecar() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');

        $Coalition = IGD('Storecar', 'User');
        $result = $Coalition->DeleCar($parr);
        $this->ajaxReturn($result);
    }

    //清除购物车
    public function Clearcar() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $Coalition = IGD('Storecar', 'User');
        $result = $Coalition->Clearcar($parr);
        $this->ajaxReturn($result);
    }
}
