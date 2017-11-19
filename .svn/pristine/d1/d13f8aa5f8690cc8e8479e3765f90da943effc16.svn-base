<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 活动订单模块
 */
class BargorderController extends BaseController {
	

    public function index() {

        /*提交订单信息*/
        $ucode = session('USER.ucode');  
        $this->ucode = $ucode;

        $this->groupcode = I('groupcode');

        /*获取商品详情，购物车，提交的商品信息*/
        $this->product= json_encode($product);
        $proinfo = IGD('Bargain', 'Newact');
        $prolist = $proinfo->splitProduct($this->groupcode, $ucode);
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
    //ucode,groupcode,delivery,addressid,postscript,freeprice
    public function CreataOrder() {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));   

        $parr['ucode'] = session('USER.ucode');
        $parr['groupcode'] = $data['groupcode'];
        $parr['delivery'] = $data['delivery'];
        $parr['addressid'] = $data['addressid'];
        $parr['postscript'] = $data['postscript'];
        $parr['freeprice'] = $data['freeprice'];
        $orderdb = IGD('Bargain', 'Newact');
        $result = $orderdb->CreateOrders($parr);
        $this->ajaxReturn($result);
    }

}