<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 拼团活动订单模块
 */
class ActorderController extends BaseController {
	

    public function index() {

        /*提交订单信息*/
        $ucode = session('USER.ucode');
       
        /*未登录前已选的商品信息提交，读取cookies*/
        $actpcode = explode("|", $_POST['confirm-actpcode']);
        $pcode = explode("|", $_POST['confirm-pcode']);
        $num = explode("|", $_POST['confirm-num']);
        $pucode = explode("|", $_POST['confirm-pucode']);
        $mcode = explode("|", $_POST['confirm-mcode']);
        for ($i=0;$i < count($pcode);$i++) {
            $product[$i]['actpcode'] = $actpcode[$i];
            $product[$i]['pcode'] = $pcode[$i];
            $product[$i]['num'] = $num[$i];
            $product[$i]['pucode'] = $pucode[$i];
            $product[$i]['mcode'] = $mcode[$i];
        }

        $this->groupcode = $_POST['groupcode'];

        /*获取商品详情，购物车，提交的商品信息*/
        $this->product= json_encode($product);
        $proinfo = IGD('Groupbuy', 'Newact');
        $prolist = $proinfo->splitProduct($product, $ucode,$this->groupcode);
        if ($prolist['code']!=0) {
            $this->procode = $prolist['code'];
            $this->prosqmsg = $prolist['msg'];
        }        
        $this->prodata = $prolist['data']['value'];
                
        $prolist['data']['totalprice'] = 0;
        foreach ($prolist['data']['value'] as $key => $val) {
            $prolist['data']['totalprice'] += $val['singletotle'];
            $prolist['data']['types'] = $val['types'];
        }
        $this->freeprice = $prolist['data']['freeprice'];
        $this->countprice = sprintf("%1\$.2f", $prolist['data']['totalprice']);
        $this->types = $prolist['data']['types'];
        
		/*商家信息*/
		$this->acodeinfo = $prolist['data']['acodeinfo'];

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
    //ucode,act_pcode,mcode,delivery,addressid,money,pnum,groupcode,freeprice
    public function CreataOrder() {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));   
        $product = objarray_to_array(json_decode($data['produce']));        

        $parr['ucode'] = session('USER.ucode');
        $parr['act_pcode'] = $product[0]['actpcode'];
        $parr['mcode'] = $product[0]['mcode'];
        $parr['delivery'] = $data['delivery'];
        $parr['addressid'] = $data['addressid'];
        $parr['postscript'] = $data['postscript'];
        $parr['pnum'] = $product[0]['num'];
        $parr['groupcode'] = $data['groupcode'];
        $parr['freeprice'] = $data['freeprice'];
        $orderdb = IGD('Groupbuy', 'Newact');
        $result = $orderdb->CreataActOrder($parr);
        $this->ajaxReturn($result);
    }

}