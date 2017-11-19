<?php
/**
 * 	微商扫码产品管理
 *
 */
class NewStore {

    /**
     * 选取商品列表
     * @param
     *
    */
    function selectProduct($parr){

        $ucode =$parr['ucode'];
        if(empty($ucode)){
            return Message(1009,'请先登录');
        }
        $pageSize =10;

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['c_source'] =1; //线上商家的商品
        $w['c_isdele'] =1; //不删除
        $w['c_ishow'] =1; //上架
        $w['c_ucode']=$ucode;
        $field ="c_id,c_ucode,c_pcode,c_name,c_price,c_desc,c_pimg,c_num,c_salesnum,c_ismodel";
        $list =M('Product')->where($w)->limit($countPage,$pageSize)->field($field)->select();
        $count =M('Product')->where($w)->count();

        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $arr = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $arr);
        }

        foreach($list as $key =>$val){
            $list[$key]['c_pimg'] = GetHost().'/'.$val['c_pimg']; //商品图片
            if($val['c_ismodel']==1){
                $list[$key]['modelList'] = M('Product_model')->where(array('c_pcode'=>$val['c_pcode']))->field("c_name,c_mcode,c_num")->select();
            }
        }

        $arr = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$arr);


    }

    /**
     * 添加商品
     * @param  ucode,
    */

    function addProduct($parr){
        $ucode =$parr['ucode'];
        $free =$parr['free'];
        $num =$parr['num'];
        $arr =$parr['arr'];

        if(empty($ucode)){
            return Message(1009,'请先登录');
        }
        if(empty($num)){
            return Message(1001,'库存不能为空');
        }
        if(!$arr){
            return Message(1001,'请选择商品');
        }
        $data['c_ucode'] =$ucode;
        $data['c_xpcode'] ='N'.time();
        $data['c_num'] =$num;
        $data['c_addtime']=date('Y-m-d H:i:s');
        $data['c_free'] =$free;
        $data['c_arrs'] =$arr;
        $result =M('Store_scan_product')->add($data);

        if(!$result){
            return Message(1001,"添加失败");
        }
        return Message(0,"添加成功");

    }


    /**
     * 编辑店铺信息
     * @param acode desc tel
    */

    function EditStore($parr){
        $ucode =$parr['ucode'];
        $acode =$parr['acode'];
        $desc =$parr['desc'];
        $tel =$parr['tel'];
        if(empty($acode)){
            return Message(1001,"商家code不能为空");
        }
        $save['c_desc'] =$desc;
        $save['c_tel'] =$tel;
        $save['c_updatetime'] =date('Y-m-d H:i:s');
        $result =M('Store')->where(array('c_ucode'=>$acode))->save($save);
        if($result<0){
            return Message(1002,"编辑失败");
        }
        return Message(1003,"编辑成功");

    }


    /**商品列表(展示精品商品)
     * ucode 商家用户编码
     *
    */

    function getProList($parr){
        $ucode =$parr['ucode'];
        $acode =$parr['acode'];

        if(empty($ucode)){
            return Message(1009,'请先登录');
        }
        $pageSize =10;

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;
        $where['c_isdel'] = 0;
        $where['c_num'] = array("GT",0);
        $where['c_ucode'] =$acode;

        $list =M('Store_scan_product')->where($where)->limit($countPage, $pageSize)->select();
        $count = M('Store_scan_product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach($list as $key=> $value){
            $array =json_decode($value['c_arrs']);
            $price =0;
            foreach($array as $k=>$v){
                $price +=$array[$k]['c_price'];
            }
            $aa[$key]['c_id'] =$value['c_id'];
            $aa[$key]['c_xpcode'] =$value['c_xpcode'];
            $aa[$key]['c_ucode'] =$value['c_ucode'];
            $aa[$key]['c_num'] =$value['c_num'];
            $aa[$key]['c_free'] =$value['c_free'];
            $aa[$key]['c_buynum'] =$value['c_buynum'];
            $aa[$key]['c_name'] ="组合商品";
            $aa[$key]['c_price'] =$price;
            $aa[$key]['c_imgpath'] =GetHost();
        }

        $data = Page($pageIndex, $pageCount, $count, $aa);
        return MessageInfo(0,'查询成功',$data);
    }

}