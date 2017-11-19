<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 实体店铺订单模块
 */
class StorderController extends BaseController {
	
    public function __construct() {
        parent::__construct();
        header('Content-Type: text/html;charset=utf-8');
    }
    public function index() {

        /*提交订单信息*/
        $ucode = session('USER.ucode');
       
        /*未登录前已选的商品信息提交，读取cookies*/
        $pcode = explode("|", $_POST['confirm-pcode']);
        $num = explode("|", $_POST['confirm-num']);
        $pucode = explode("|", $_POST['confirm-pucode']);
        $mcode = explode("|", $_POST['confirm-mcode']);
        for ($i=0;$i < count($pcode);$i++) {
            $product[$i]['pcode'] = $pcode[$i];
            $product[$i]['num'] = $num[$i];
            $product[$i]['pucode'] = $pucode[$i];
            $product[$i]['mcode'] = $mcode[$i];
        }

        /*获取商品详情，购物车，提交的商品信息*/
        $this->product= json_encode($product);
        $proinfo = IGD('Storeorder', 'Order');
        $prolist = $proinfo->splitProduct($product, $ucode);
        if ($prolist['code']!=0) {
            $this->procode = $prolist['code'];
            $this->prosqmsg = $prolist['msg'];
        }
        $this->prodata = $prolist['data']['value'];
        $prolist['data']['totalprice'] = 0;

        foreach ($prolist['data']['value'] as $key => $val) {
            $prolist['data']['totalprice'] += $val['singletotle'];
        }
        $this->freeprice = $prolist['data']['freeprice'];
        $this->countprice = sprintf("%1\$.2f", $prolist['data']['totalprice']);


        // 获取地址列表
        $parr['ucode'] = $ucode;
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

        /*获取可用余额*/
        $parru['ucode'] = session('USER.ucode');
        $balance = IGD('User', 'User');
        $b_money = $balance->GetUserMoney($parru);
        $this->cmoney = $b_money['data']['c_money'];

        $this->show();
    }

    //生成订单
    public function CreataOrder() {
        if (IS_AJAX) {
            $parr['ucode'] = session('USER.ucode');
            $attrbul = I('attrbul');
            $attrbul = str_replace('&quot;', '"', $attrbul);
            $data = objarray_to_array(json_decode($attrbul));
            $parr['delivery'] = $data['delivery'];
            $parr['addressid'] = $data['addressid'];
            $parr['postscript'] = urldecode($data['postscript']);
            $parr['money'] = $data['money'];
            $parr['produce'] = urldecode($data['produce']);
            $parr['model'] = 3;//1安卓，2IOS，3微信
            $produce = objarray_to_array(json_decode($parr['produce']));

            $orderdb = IGD('Storeorder', 'Order');
            $cardb = IGD('Storecar', 'User');
            $result = $orderdb->CreataOrder($parr);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result);
            }
            $info = $result['data'];

            //清空购物车
            $parrc['ucode'] = session('USER.ucode');
            foreach ($produce as $key => $value) {
                $parrc['pcode'] = $value['pcode'];
                $cardb->DeleCar($parrc);
            }

            $parr1['orderid'] = $info['orderid'];
            $parr1['ucode'] = session('USER.ucode');
            $result = $orderdb->GetPayorderinfo($parr1);
            $this->ajaxReturn($result);
        }
    }

}