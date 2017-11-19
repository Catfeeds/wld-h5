<?php

/**
 * 代理商城购物车操作
 */
class SupplycarUser {

    private $gouwu = "xiaomi";
    private $xiaochutime = "86400";

    /*获取购物车商品数量*/
    public function GetCount($ucode){
         $keycode = $this->gouwu . $ucode;
        $list = S($keycode);
        if (!$list) {
            $count = 0;
        }else{
            $count=count($list);
        }

        $data['count']=$count;
        return MessageInfo(0,"查询成功",$data);
    }
    //获取购物车缓存

    public function GetCar($ucode) {
        $keycode = $this->gouwu . $ucode;
        $list = S($keycode);

        if (count($list) == 0) {
            return MessageInfo(0, "查询购物车成功", $list);
        }

        $shop = array();
        foreach ($list as $key => $value) {
            $shop[$value['c_acode']]['ziying'][] = $value;
        }


        $shopacodearr = array_values($shop);

        $shop1 = array();
        foreach ($shopacodearr as $key1 => $value1) {
            $shoptemp = array();
            if (in_array('ziying', array_keys($value1))) {
                $shoptemp1 = $value1['ziying'];
                $carinfo = array();
                foreach ($shoptemp1 as $key2 => $value2) {
                    $carinfo['c_acode'] = $value2['c_acode'];
                    $carinfo['c_nickname'] = $value2['c_nickname'];
                    break;
                }
                $carinfo['list'] = $shoptemp1;
                $shop1[] = $carinfo;
            }
        }

        return MessageInfo(0, "查询购物车成功", $shop1);
    }

    //添加购物车
    public function AddCar($parr) {
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];
        $mcode = $parr['mcode'];
        $pucode = $parr['pucode'];
        $pmname = $parr['pmname'];
        $severtype = $parr['severtype'];
        $num = $parr['num'];


        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        $list = S($keyucode);
        foreach ($list as $key => $value) {
            if ($value['c_pcode'] == $pcode && $value['c_mcode'] == $mcode) {
                $temp1 = $num;
                $count = IGD('Supplyorder', 'Agorder')->Getoldproduct($ucode, $pcode);
                $temp2 = $temp1 + $count;
                //判断是否存在阶梯价格
                $wherejieti['c_pcode'] = $value['c_pcode'];
                $wherejieti['c_mcode'] = $value['c_mcode'];
                $wherejieti['c_minnum'] = array('ELT', $temp2);
                $wherejieti['c_maxnum'] = array('EGT', $temp2);
                $jietiinfo = M('Supplier_product_ladderprice')->where($wherejieti)->find();


                if (count($jietiinfo) > 0) {
                    $price = $jietiinfo['c_price'];
                    $list[$key]['c_price'] = $price;
                }

                $list[$key]['c_num'] = $temp1;
                $list[$key]['c_total'] = bcmul($price, $temp1, 2);
                $tag = 1;
            }
        }

        if ($tag == 1) {

            S($keyucode, $list, $this->xiaochutime);
            $data['buynum'] = count($list);
            return MessageInfo(0, "添加成功", $data);
        }


        $where['c_pcode'] = $pcode;
        $where['c_ishow'] = 1;
        $where['c_isdele'] = 1;
        $join = 'INNER JOIN t_supplier as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_name as c_nickname';
        $productinfo = M('Supplier_product as a')->join($join)->where($where)->field($field)->find();



        if (count($productinfo) == 0) {
            return Message(1018, "该产品已下线或已删除");
        }

        if (!empty($parr['mcode'])) {
            //查询商品价格
            $wheremodel['c_pcode'] = $parr['pcode'];
            $wheremodel['c_mcode'] = $parr['mcode'];
            $ProductModel = M('Supplier_product_model')->where($wheremodel)->find();
            if (count($ProductModel) <= 0) {
                return Message(1015, "没有查询到该产品型号");
            } else {
                $price = $ProductModel['c_price'];
                $num = $ProductModel['c_num'];
                $pmname = $ProductModel['c_name'];
            }
        }

        $oldcount = IGD('Supplyorder', 'Agorder')->Getoldproduct($ucode, $pcode);
        $temp2 = $parr['num'] + $oldcount;

        //判断是否存在阶梯价格
        $wherejieti['c_pcode'] = $productinfo['c_pcode'];
        $wherejieti['c_mcode'] = $parr['mcode'];
        $wherejieti['c_minnum'] = array('ELT', $temp2);
        $wherejieti['c_maxnum'] = array('EGT', $temp2);
        $jietiinfo = M('Supplier_product_ladderprice')->where($wherejieti)->find();
        if (count($jietiinfo) > 0) {
            $price = $jietiinfo['c_price'];
        }

        $temp['c_pcode'] = $productinfo['c_pcode'];
        $temp['c_name'] = $productinfo['c_name'];
        $temp['c_mcode'] = $parr['mcode'];
        $temp['c_pmname'] = $pmname;
        $temp['c_pucode'] = $pucode;
        $temp['c_img'] = GetHost() . '/' . $productinfo['c_pimg'];
        $temp['c_price'] = $price;
        $temp['c_num'] = $parr['num'];
        $temp['c_isfree'] = $productinfo['c_isfree'];
        $temp['c_freeprice'] = $productinfo['c_freeprice'];
        $temp['c_total'] = bcmul($price, $parr['num'], 2);
        $temp['c_nickname'] = $productinfo['c_nickname'];
        $temp['c_acode'] = $productinfo['c_ucode'];
        $temp['c_severtype'] = $severtype;
        $list[] = $temp;


        S($keyucode, $list, $this->xiaochutime);
        $data['buynum'] = count($list);
        return MessageInfo(0, "添加成功", $data);
    }

    //删除数据
    public function DeleCar($parr) {
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];
        $mcode = $parr['mcode'];

        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        $list = S($keyucode);

        if (count($list) == 0) {
            return Message(1015, "购物车中没有产品");
        }

        $templist=array();
        foreach ($list as $key => $value) {
            if ($value['c_pcode'] == $pcode && $value['c_mcode'] == $mcode) {

            }else{
                $templist[]=$value;
            }
        }

        S($keyucode, $templist, $this->xiaochutime);
        $data['buynum'] = count($templist);
        return MessageInfo(0, "修改成功", $data);
    }

    public function Clearcar($parr) {
        $ucode = $parr['ucode'];
        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        S($keyucode, null);
        return Message(0, "修改成功");
    }


}
