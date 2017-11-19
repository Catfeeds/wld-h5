<?php
/**
 * 普通商城购物车操作
 */
class ShoppingcarUser {

    private $gouwu = "gouwu";
    private $xiaochutime = "86400";

    /*获取购物车商品数量*/
    public function GetCount($ucode){
        $keycode = $this->gouwu . $ucode;
        // $list = S($keycode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keycode);
        if (!$list) {
            $count = 0;
        }else{
            $count=count($list);
        }

        $data['count'] = $count;
        return MessageInfo(0,"查询成功",$data);
    }

    //获取购物车缓存
    public function GetCar($ucode) {
        $keycode = $this->gouwu . $ucode;
        // $list = S($keycode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keycode);

        if (count($list) == 0) {
            return MessageInfo(0, "查询购物车成功", $list);
        }

        $shop = array();
        foreach ($list as $key => $value) {
            if ($value['c_isagent'] == 0) {
                $shop[$value['c_acode']]['ziying'][] = $value;
            } else {
                $shop[$value['c_acode']]['daili'][] = $value;
            }
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

            if (in_array('daili', array_keys($value1))) {
                $shoptemp1 = $value1['daili'];
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
        $num = $parr['num'];

        if ($num <= 0) {
            return Message(1017, "加入购物车数量必须大于0");
        }


        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        // $list = S($keyucode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keyucode);
        foreach ($list as $key => $value) {
            if ($value['c_pcode'] == $pcode && $value['c_mcode'] == $mcode) {
                $list[$key]['c_num'] = $num;
                $list[$key]['c_total'] = bcmul($price, $num, 2);
                $tag = 1;
            }
        }

        if ($tag == 1) {
            // S($keyucode, $list, $this->xiaochutime);
            IGD('Common', 'Redis')->RediesStoreSram($keyucode, $list, $this->xiaochutime);
            $data['buynum'] = count($list);
            $data['pnum'] = $parr['num'];
            return MessageInfo(0, "添加成功", $data);
        }


        $where['c_pcode'] = $pcode;
        $where['c_ishow'] = 1;
        $where['c_isdele'] = 1;
        $join = 'INNER JOIN  t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname';
        $productinfo = M('Product as a')->join($join)->where($where)->field($field)->find();
        $kcnum = $productinfo['c_num'];

        if (count($productinfo) == 0) {
            return Message(1018, "该产品已下线或已删除");
        }

        if (!empty($parr['mcode'])) {
            //查询商品价格
            $wheremodel['c_pcode'] = $parr['pcode'];
            $wheremodel['c_mcode'] = $parr['mcode'];
            $ProductModel = M('Product_model')->where($wheremodel)->find();
            if (count($ProductModel) <= 0) {
                return Message(1015, "没有查询到该产品型号");
            } else {
                $price = $ProductModel['c_price'];
                $kcnum = $ProductModel['c_num'];
                $pmname = $ProductModel['c_name'];
            }
        }

        $temp['c_kcnum'] = $kcnum;
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
        $temp['c_isagent'] = $productinfo['c_isagent'];
        $temp['c_total'] = bcmul($price, $parr['num'], 2);
        $temp['c_nickname'] = $productinfo['c_nickname'];
        $temp['c_acode'] = $productinfo['c_ucode'];
        $list[] = $temp;


        // S($keyucode, $list, $this->xiaochutime);
        IGD('Common', 'Redis')->RediesStoreSram($keyucode, $list, $this->xiaochutime);
        $data['buynum'] = count($list);
        $data['pnum'] = $parr['num'];
        return MessageInfo(0, "添加成功", $data);
    }

    //删除数据
    public function DeleCar($parr) {
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];
        $mcode = $parr['mcode'];

        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        // $list = S($keyucode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keyucode);

        if (count($list) == 0) {
            return Message(1015, "购物车中没有产品");
        }

        // $templist=array();
        foreach ($list as $key => $value) {
            if ($value['c_pcode'] == $pcode && $value['c_mcode'] == $mcode) {

            }else{
                $templist[]=$value;
            }
        }

        // S($keyucode, $templist, $this->xiaochutime);
        IGD('Common', 'Redis')->RediesStoreSram($keyucode, $templist, $this->xiaochutime);
        $data['buynum'] = count($templist);
        return MessageInfo(0, "修改成功", $data);
    }

    public function Clearcar($parr) {
        $ucode = $parr['ucode'];
        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        // S($keyucode, null);
        IGD('Common', 'Redis')->RediesStoreSram($keyucode,null);
        return Message(0, "修改成功");
    }

    //产品分享记录
    public function Spreadlog($parr){
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];

        $whereinfo['c_ucode'] = $ucode;
        $whereinfo['c_pcode'] = $pcode;
        $isexist = M('users_spread')->where($whereinfo)->count();

        if($isexist > 0){
            $result = M('users_spread')->where($whereinfo)->setInc('c_tgnum',1);
            $save['c_isdele'] = 1;
            $result = M('users_spread')->where($whereinfo)->save($save);
            return Message(0,'该推广记录已经存在！');
        }

        $data['c_ucode'] = $ucode;
        $data['c_pcode'] = $pcode;
        $data['c_isdele'] = 1;
        $data['c_addtime'] = date('Y-m-d H:i:s',time());

        $result = M('users_spread')->add($data);

        if($result){
            return Message(0,'推广成功！');
        }else{
            return Message(1012,'推广失败！');
        }
    }
}
