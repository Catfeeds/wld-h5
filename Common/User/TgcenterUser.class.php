<?php

/**
 * 用户推广中心接口
 */
class TgcenterUser {

    //所有具有推广佣金属性的产品
    public function Allproduct($parr) {
        $ucode = $parr['ucode'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        //查询条件
        $pwhere['a.c_ishow'] = 1;
        $pwhere['a.c_isdele'] = 1;
        $pwhere['a.c_isspread'] = 1;
        $pwhere['a.c_spread_proportion'] = array('gt',0); 

        if (!empty($parr['name'])) {
            $parrname = $parr['name'];
            $pwhere[] = array("a.c_name like '%$parrname%'");
        }

        $order = 'a.c_id desc';

        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $list = M('Product as a')->join($join)->where($pwhere)->order($order)->limit($countPage, $pageSize)->select();

        $count =  M('Product as a')->join($join)->where($pwhere)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];

            $list[$key]['sharetit'] = $value['c_name'];
            $list[$key]['sharedesc'] = empty($value['c_desc'])?$value['c_name']:$value['c_desc'];
            $list[$key]['shareimg'] = GetHost() . '/' . $value['c_pimg'];
            if($value['c_source'] == 1){
                $list[$key]['shareurl'] = GetHost(1) . "/index.php/Shopping/Index/detail?pcode=".$value['c_pcode']."&pucode=".$ucode;
            }else{
                $list[$key]['shareurl'] = GetHost(1) . "/index.php/Shopping/Entitymap/detail?pcode=".$value['c_pcode']."&pucode=".$ucode;
            }

            $list[$key]['returnurl'] = GetHost(1) . "/index.php/Home/Expand/Spreadlog?pcode=".$value['c_pcode'];
            $list[$key]['apireturnurl'] = GetHost(2) . "/api.php/Store/Product/Spreadlog?pcode=".$value['c_pcode'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$data);      
    }

    //所有我的推广产品
    public function My_Allproduct($parr){
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $ucode = $parr['ucode'];
        $sql = "select a.*,b.c_tgnum from t_product as a inner join t_users_spread as b on a.c_pcode=b.c_pcode where a.c_ishow=1 and a.c_isdele=1 and b.c_ucode='$ucode' and b.c_isdele=1 order by a.c_id desc limit $countPage,$pageSize";

        $list = M()->query($sql);

        $sql1 = "select count(*) as count from t_product as a inner join t_users_spread as b on a.c_pcode=b.c_pcode where a.c_ishow=1 and a.c_isdele=1 and b.c_ucode='$ucode' and b.c_isdele=1";
        $countarr = M()->query($sql1);
        $count = $countarr[0]['count'];
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];

            $list[$key]['sharetit'] = $value['c_name'];
            $list[$key]['sharedesc'] = $value['c_desc'];
            $list[$key]['shareimg'] = GetHost() . '/' . $value['c_pimg'];
            if($value['c_source'] == 1){
                $list[$key]['shareurl'] = GetHost(1) . "/index.php/Shopping/Index/detail?pcode=".$value['c_pcode']."&pucode=".$ucode;
            }else{
                $list[$key]['shareurl'] = GetHost(1) . "/index.php/Shopping/Entitymap/detail?pcode=".$value['c_pcode']."&pucode=".$ucode;
            }

            $list[$key]['returnurl'] = GetHost(1) . "/index.php/Home/Expand/Spreadlog?pcode=".$value['c_pcode'];
            $list[$key]['apireturnurl'] = GetHost(2) . "/api.php/Store/Product/Spreadlog?pcode=".$value['c_pcode'];

            $pcode = $value['c_pcode'];
            $sql = "select IFNULL(sum(a.c_spread),0) as rebates from t_order_details as a inner join t_order as b on a.c_orderid=b.c_orderid where a.c_pcode='$pcode' and a.c_pucode='$ucode' and b.c_deliverystate=5 ";           

            $result = M()->query($sql);

            $list[$key]['rebates'] = $result[0]['rebates'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

    //删除我的推广商品
    public function Myproduct_del($parr){
        $ucode = $parr['ucode'];
        $pcode = $parr['pcode'];

        $pwhere['c_ucode'] = $ucode;
        $pwhere['c_pcode'] = $pcode;

        $sdata['c_isdele'] = 2;

        $result = M('Users_spread')->where($pwhere)->save($sdata);
        if($result < 0){
            return Message(1001, "删除失败");
        }

        return Message(0, "删除成功");
    }
}
