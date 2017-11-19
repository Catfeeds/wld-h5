<?php

/**
 * 实体店铺购物车操作
 */
class StorecarUser {

    private $gouwu = "stgouwu";//实体店铺 购物车用户标识前缀
    private $xiaochutime = "604800";//一周

    /*获取某个店铺购物车商品数量
        ucode,acode
    */
    public function GetCount($parr){
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        $keycode = $this->gouwu . $ucode;
        // $list = S($keycode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keycode);

        $num = 0;
        $price = '0.00';
        if (!$list) {
            $count = 0;
        }else{
            foreach ($list as $key => $value) {
               if($value['c_acode'] == $acode){
                    $num = $value['c_num'] + $num;
                    $price = $value['c_price']*$value['c_num'] + $price;
                }
            }
        }

        $data['count']=$num;
        $data['price']=$price;
        return MessageInfo(0,"查询成功",$data);
    }

    /*获取某个商品在购物车数量
        ucode,pcode
    */
    public function Getprocount($parr){
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];

        $keycode = $this->gouwu . $ucode;
        // $list = S($keycode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keycode);

        $num = 0;
        if (!$list) {
            $count = 0;
        }else{
            foreach ($list as $key => $value) {
               if($value['c_pcode'] == $pcode){
                    $num = $value['c_num'];
                }
            }
        }

        $data['count'] = $num;
        return MessageInfo(0,"查询成功",$data);
    }

    //获取购物车缓存 ucode,acode
    public function GetCar($parr) {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        $keycode = $this->gouwu . $ucode;
        // $list = S($keycode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keycode);
        if (count($list) == 0) {
            // $list = array();
            return MessageInfo(0, "查询购物车成功", $list);
        }

        $thislist = array();
        $carinfo = array();
        $tempcount = 0;
        foreach ($list as $key => $value) {
            if($value['c_acode'] == $acode){
                $thislist['acode'] = $value['c_acode'];
                $thislist['nickname'] = $value['c_nickname'];
                $carinfo[$tempcount] = $value;
                $thislist['list'] = $carinfo;
                $tempcount++;
            }
        }

        if(count($thislist) == 0){
            $thislist = null;
            return MessageInfo(0, "查询购物车成功", $thislist);
        }

        $data[] =  $thislist;
        return MessageInfo(0, "查询购物车成功", $data);
    }

    //添加购物车
    public function AddCar($parr) {
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];
        $num = $parr['num'];
        $pucode = $parr['pucode'];

        if ($num <= 0) {
            return Message(1017, "加入购物车数量必须大于0");
        }

        $where['c_pcode'] = $pcode;
        $where['c_ishow'] = 1;
        $where['c_isdele'] = 1;
        $where['c_source'] = 2;//实体商家
        $join = 'INNER JOIN  t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname';
        $productinfo = M('Product as a')->join($join)->where($where)->field($field)->find();

        if (count($productinfo) == 0) {
            return Message(1018, "该产品已下线或已删除");
        }

        //判断购物车是否存在
        $acode = $productinfo['c_ucode'];
        $keyucode = $this->gouwu . $ucode;
        // $list = S($keyucode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keyucode);
        foreach ($list as $key => $value) {
            if($value['c_acode'] == $acode){
                if($value['c_pcode'] == $pcode){
                    $list[$key]['c_num'] = $num;
                    $list[$key]['c_total'] = bcmul($productinfo['c_price'], $num, 2);
                    // S($keyucode, $list, $this->xiaochutime);
                    IGD('Common', 'Redis')->RediesStoreSram($keyucode, $list, $this->xiaochutime);

                    $parrs['ucode'] = $ucode;
                    $parrs['acode'] = $acode;
                    $result = $this->GetCount($parrs);
                    $data['count'] = $result['data']['count'];
                    $data['num'] = $num;
                    $data['price'] = $result['data']['price'];
                    return MessageInfo(0, "添加成功",$data);
                }
            }
        }

        $temp['c_pcode'] = $productinfo['c_pcode'];
        $temp['c_name'] = $productinfo['c_name'];
        $temp['c_img'] = GetHost() . '/' . $productinfo['c_pimg'];
        $temp['c_price'] = $productinfo['c_price'];
        $temp['c_pucode'] = $pucode;
        $temp['c_num'] = $num;
        $temp['c_kcnum'] = $productinfo['c_num'];
        $temp['c_isfree'] = $productinfo['c_isfree'];
        $temp['c_freeprice'] = $productinfo['c_freeprice'];
        $temp['c_total'] = bcmul($productinfo['c_price'], $num, 2);
        $temp['c_nickname'] = $productinfo['c_nickname'];
        $temp['c_acode'] = $productinfo['c_ucode'];
        $list[] = $temp;

        // S($keyucode, $list, $this->xiaochutime);
        IGD('Common', 'Redis')->RediesStoreSram($keyucode, $list, $this->xiaochutime);

        $parrs['ucode'] = $ucode;
        $parrs['acode'] = $acode;
        $result = $this->GetCount($parrs);
        $data['count'] = $result['data']['count'];
        $data['num'] = $num;
        $data['price'] = $result['data']['price'];
        return MessageInfo(0, "添加成功",$data);
    }

    //删除购物车某件商品
    public function DeleCar($parr) {
        $acode = $parr['acode'];
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];

        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        // $list = S($keyucode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keyucode);

        if (count($list) == 0) {
            return Message(1015, "购物车中没有产品");
        }

        // $templist=array();
        foreach ($list as $key => $value) {
            if ($value['c_pcode'] == $pcode) {

            }else{
                $templist[]=$value;
            }
        }

        // S($keyucode, $templist, $this->xiaochutime);
        IGD('Common', 'Redis')->RediesStoreSram($keyucode, $templist, $this->xiaochutime);
        if($acode){
            $parrs['ucode'] = $ucode;
            $parrs['acode'] = $acode;
            $result = $this->GetCount($parrs);
            $data['count'] = $result['data']['count'];
            $data['price'] = $result['data']['price'];
            return MessageInfo(0, "删除成功",$data);
        }else{
            return Message(0, "删除成功");
        }
    }
    //清空购物车
    public function Clearcar($parr) {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        //判断购物车是否存在
        $keyucode = $this->gouwu . $ucode;
        // $list = S($keyucode);
        $list = IGD('Common', 'Redis')->Rediesgetucode($keyucode);

        if (count($list) == 0) {
            return Message(1015, "购物车中没有产品");
        }

        // $templist=array();
        foreach ($list as $key => $value) {
            if ($value['c_acode'] == $acode) {

            }else{
                $templist[]=$value;
            }
        }

        // S($keyucode, $templist, $this->xiaochutime);
        IGD('Common', 'Redis')->RediesStoreSram($keyucode, $templist, $this->xiaochutime);
        return Message(0, "修改成功");
    }
}
