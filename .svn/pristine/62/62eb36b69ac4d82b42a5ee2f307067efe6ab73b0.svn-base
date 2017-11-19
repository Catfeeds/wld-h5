<?php
/**
 * 	商品分类
 *
 */
class CategoryStore {

    /**创建分类
     * ucode 商家用户编码
     * categoryname  分类名称
    */

    function createCate($parr){
        $ucode =$parr['ucode'];
        $name =$parr['name'];
        if(empty($ucode) || empty($name)){
            return Message(1001,'参数缺失');
        }
        $db =M('Product_category');
        $con =array(
            'c_ucode'=>$ucode,
            'c_category_name'=>$name,
            'c_isdel'=>0
        );
        $res =$db->where($con)->find();
        $big =$db->where(array('c_ucode'=>$ucode))->max('c_order'); //获取最大的排序
        if(!empty($res)){
            return Message(1001,'该分类名已经存在');
        }
        $add['c_ucode']=$ucode;
        $add['c_category_name']=$name;
        if(!$big){
            $add['c_order']=1;
        }else{
            $add['c_order']=$big+1;
        }
        $add['c_addtime']=date('Y-m-d H:i:s');
        $result =$db->add($add);
        if($result<0){
            return Message(1002,'创建分类失败');
        }
        $data =$this->getDetail($ucode,$result);

        return MessageInfo(0,'创建分类成功',$data);
    }
   /**分类列表
    * ucode  商家用户编码
   */
    function cateList($parr){
        $ucode =$parr['ucode'];
//        if(empty($ucode)){
//            return Message(1001,'参数缺失');
//        }
        $acode =$parr['acode'];

        if(!empty($acode)){
            $con['c_ucode']=$acode;
            $w['c_ucode']=$acode;
            $arr['c_ucode']=$acode;
        }else{
            $con['c_ucode']=$ucode;
            $w['c_ucode']=$ucode;
            $arr['c_ucode']=$ucode;
        }

        $db =M('Product_category');
        $field ="c_id,c_category_name";
        $con['c_isdel']=0;
        $list =$db->where($con)->field($field)->order('c_order asc')->select();

        $w['c_product_category']=array('eq',0);
        $w['c_isdele']=1;
        $w['c_isagent']=0;
        $Ntotal =M('Product')->where($w)->count();

        if(empty($list)){
            return $this->MessageInfo(0,'查询成功',$Ntotal,array());
        }

        foreach($list as $key=>$value){
            $arr['c_product_category']=$value['c_id'];
            $arr['c_isdele']=1;
            $arr['c_isagent']=0;
            $total =count(M('Product')->where($arr)->select());
            if(!$total){
                $list[$key]['total']=0;
            }else{
                $list[$key]['total']=$total;
            }
        }
        return $this->MessageInfo(0,'查询成功',$Ntotal,$list);
    }


    function MessageInfo($code, $message, $total,$data) {
        $msg = array();
        $msg["code"] = $code;
        $msg["msg"] = $message;
        $msg['total'] =$total;
        $msg["data"] = $data;
        return $msg;
    }

    /**
     * 分类编辑
     * ucode 商家编码
     * id  分类id
     * name  分类名
    */

    function cateEdit($parr){
        $ucode =$parr['ucode'];
        $id =$parr['id'];
        $name =$parr['name'];
        if(empty($ucode) || empty($id)){
            return Message(1001,'参数缺失');
        }

        if(empty($name)){
            return Message(1001,"商品分类名不能为空");
        }

        $db =M('Product_category');
         $con =array(
             'c_ucode'=>$ucode,
             'c_category_name'=>$name,
             'c_isdel'=>0
         );
        $res =$db->where($con)->find();
        if(!empty($res)){
            return Message(1001,'该分类名已经存在哦');
        }
        $save['c_category_name']=$name;
        $save['c_updatetime']=date('Y-m-d H:i:s');
        $result =$db->where(array('c_id'=>$id))->save($save);

        if($result<0){
            return Message(1001,'编辑分类失败');
        }

        $data =$this->getDetail($ucode,$id);
        return MessageInfo(0,'编辑分类成功',$data);
    }
    /**
     * 分类删除
     * id 分类id
     * ucode 商家ucode
    */
    function cateDel($parr){
        $ucode =$parr['ucode'];
        $id =$parr['id'];
        if(empty($ucode) || empty($id)){
            return Message(1001,'参数缺失');
        }
        $a =M('');
        $a->startTrans();
        $db =M('Product_category');
        $w['c_id']=$id;
        $w['c_isdel']=0;
        $w['c_ucode']=$ucode;
        $result =$db->where($w)->save(array('c_isdel'=>1));
        if(!$result){
            $a->rollback();
            return Message(1001,'删除分类失败！');
        }
        $con =array(
            'c_ucode'=>$ucode,
            'c_product_category'=>$id
        );
        $data =M('Product')->where($con)->count();
        if($data>0){
            $res =M('Product')->where(array('c_ucode'=>$ucode,'c_product_category'=>$id))->save(array('c_product_category'=>0));
            if(!$res){
                $a->rollback();
                return Message(1002,'删除分类失败');
            }
        }
        $a->commit();
        return MessageInfo(0,'删除分类成功',$id);

    }
    /**
     * 分类上下移动
     * id 分类id
    */
    function cateMove($parr){
        $ucode =$parr['ucode'];
        $id1 =$parr['id1'];
        $id2 =$parr['id2'];
        if(empty($ucode) ||empty($id1)||empty($id2)){
            return Message(1001,'参数缺失');
        }
        $con1=array(
            'c_ucode'=>$ucode,
            'c_id'=>$id1
        );
        $con2=array(
            'c_ucode'=>$ucode,
            'c_id'=>$id2
        );
        $a =M('');
        $a->startTrans();
        $db =M('Product_category');
        $info1 =$db->where($con1)->find();
        $info2 =$db->where($con2)->find();
        $save1 =$db->where($con2)->save(array('c_order'=>$info1['c_order']));
        $save2 =$db->where($con1)->save(array('c_order'=>$info2['c_order']));
        if($save1 && $save2){
            $a->commit();
        }else{
            $a->rollback();
            return Message(1001,'移动失败');
        }
        $data['id1']=$id1;
        $data['id2']=$id2;
        return MessageInfo(0,'移动成功',$data);
    }
    //获取分类的详情
    function getDetail($ucode,$id){
        $db =M('Product_category');
        $field ="c_id,c_category_name";
        $con=array(
            'c_ucode'=>$ucode,
            'c_id'=>$id
        );
        $info =$db->where($con)->field($field)->find();
        $info['total']=count(M('Product')->where(array('c_product_category'=>$id,'c_ucode'=>$ucode))->select());

        return $info;
    }

    //执行商品分类 批量
    function doCategory($parr){
        $ucode =$parr['ucode'];
        $id =$parr['id'];
        $ids =$parr['ids'];
        if(empty($ucode) ||empty($ids) ||$id==""){
            return Message(1001,'参数缺失');
        }


        $db =M('Product');
        $w['c_id'] =array("in",json_decode($ids,True));
        $w['c_ucode']=$ucode;
        $result =$db->where($w)->save(array('c_product_category'=>$id));
        if($result<0){
            return Message(1001,'分类失败');
        }
        return Message(0,'分类成功');
    }

    //根据分类id查商品列表
    function getCateData($parr){
        $ucode =$parr['ucode'];
        $acode =$parr['acode'];
        $id =$parr['id'];
        $order =$parr['order'];
        $key =$parr['key'];
        $pageSize =10;

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;


//        if(empty($ucode)){
//            return Message(1001,'参数缺失');
//        }
        if(!empty($key)){
            $where[] = array("c_name like '%$key%'");
        }

        if($order==2) // 新品
            $orderby ="c_addtime desc";
        elseif($order==3)  //价格高到低
            $orderby ="c_price desc";
        elseif($order==4) //价格低到高
            $orderby ="c_price asc";
        elseif($order==5) //销量 高到低
            $orderby ="c_salesnum desc";
        elseif($order==6) //销量低到高
            $orderby ="c_salesnum asc";
        else              // 综合
            $orderby ="c_addtime desc,c_salesnum desc,c_price desc";

        $where['c_isagent'] = 0;
        $where['c_isdele'] = 1;
        $where['c_ishow'] = 1;

        if(!empty($acode))  //传了acode  就查对应商家的产品信息
            $where['c_ucode'] = $acode;
        else                // 否则 就查当前登录用户的信息
            $where['c_ucode'] = $ucode;

        if($id!==""){
            $where['c_product_category'] = $id;
        }

        $field = "*,'' as c_longitude,'' as c_latitude,'' as local_time";
        $list = M('Product')->where($where)->field($field)->order($orderby)->limit($countPage, $pageSize)->select();
        $count = M('Product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $buy =$this->getBuyInfo($value['c_pcode']);
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['c_addtime'] =substr($value['c_addtime'], 0, 4)."/".substr($value['c_addtime'], 5, 2)."/".substr($value['c_addtime'], 8, 2);
            $list[$key]['c_distance'] = 0;
            $list[$key]['url'] =  GetHost(1) . '/index.php/Shopping/Index/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
            $list[$key]['sharetit'] = $value['c_name'];
            $list[$key]['sharedesc'] = $value['c_desc'];
            $list[$key]['shareimg'] = $list[$key]['c_pimg'];
            $list[$key]['shareurl'] =  GetHost(1) . '/index.php/Shopping/Index/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
            $list[$key]['buyUserList'] =$buy['list'];
            $list[$key]['buyTotal'] =$buy['total'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$data);

    }

    //获取买过的产品的用户信息 add by james
    function getBuyInfo($pcode){
        $w['c.c_pcode']=$pcode;
        $w['b.c_pay_state']=1;
        $field ="a.c_headimg,a.c_ucode";
        $join1 ="t_order as b on b.c_ucode=a.c_ucode";
        $join2 ="t_order_details as c on c.c_orderid =b.c_orderid";
        $list =M('Users as a')->distinct('a.c_ucode')->join($join1)->join($join2)->field($field)->where($w)->select();
        $count =count($list);
        foreach($list as $key=> $value){
            $data['list'][$key]['headimg'] =GetHost() . '/' .$value['c_headimg'];
            $data['list'][$key]['ucode'] =$value['c_ucode'];
        }
        $data['total'] =$count;
        return $data;
    }

}