<?php

/**
 * 线上商家店铺管理第二版
 */
class Businessv2Store {

    //添加新产品 第一步（商品名称，与描述）
    public function NewProudct($parr) {

        if (empty($parr['name']) || empty($parr['desc'])) {
            return Message(1001, '填写型号信息有误');
        }

        //生成商品编码
        $pcode = time();

        $addinfo['c_pcode'] = $pcode;
        $addinfo['c_ucode'] = $parr['ucode'];
        $addinfo['c_name'] = $parr['name'];
        $addinfo['c_desc'] = $parr['desc'];
        $addinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $addinfo['c_updatetime'] = date('Y-m-d H:i:s', time());

        $result = M('Product')->add($addinfo);
        if ($result <= 0) {
            return Message(1002, '添加产品失败');
        }

        $data['pcode'] = $pcode;
        return MessageInfo(0, '添加产品成功', $data);
    }

    //添加产品图片 第二步、第三步（主图，商品详情图）
    public function UploadImgs($parr) {
        $pcode = $parr['pcode'];

        if (empty($pcode)) {
            return Message(1004, '商品编码缺失');
        }

        $sign = $parr['sign'];
        $imglist = $parr['imglist'];

        if ($sign == 1) {
            $mainimg = '';
        }

        $db = M('');
        $db->startTrans();

        //清空当前图片
        $whereinfo['c_pcode'] = $pcode;
        $whereinfo['c_sign'] = $sign;

        $result = M('Product_img')->where($whereinfo)->delete();
        if ($result < 0) {
            $db->rollback();
            return Message(1002, '删除图片失败');
        }

        foreach ($imglist as $key1 => $value1) {
            $imginfo = array();

            if ($sign == 1) {
                if ($key1 == 0) {
                    $mainimg = $value1;
                }
            }

            $imginfo['c_pcode'] = $pcode;
            $imginfo['c_pimgepath'] = $value1;
            $imginfo['c_sign'] = $sign;
            $imginfo['c_createtime'] = date('Y-m-d H:i:s', time());
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('Product_img')->add($imginfo);

            if ($result <= 0) {
                $db->rollback();
                return Message(1005, '添加图片失败');
            }
        }

        if (!empty($mainimg)) {
            $w['c_pcode'] = $pcode;
            $sdate['c_pimg'] = $mainimg;
            $result = M('Product')->where($w)->save($sdate);
            if ($result < 0) {
                $db->rollback();
                return Message(1005, '添加产品表图片失败');
            }
        }

        $db->commit();
        $data['pcode'] = $pcode;
        return MessageInfo(0, '添加图片成功', $data);
    }

    //添加产品模型 第四步（型号，批发价）
    public function AddModel($parr) {
        $pcode = $parr['pcode'];

        if (empty($pcode)) {
            return Message(1004, '商品编码缺失');
        }

        $modellist = $parr['modellist'];
        if (count($modellist) == 0) {
            return Message(1005, '至少添加一个模型');
        }

        $db = M('');
        $db->startTrans();

        $pricetemp = 0; //商品价格 取模型价格最小值
        $numzong = 0; //商品总库存
        $count1 = 1; //模型编码标识

        foreach ($modellist as $key => $value) {
            if (empty($value['mname']) || empty($value['mnum']) || $value['mprice'] <= 0) {
                return Message(1006, '填写型号信息有误');
            }

            //写入模型信息
            $modelinfo = array();
            $model = $count1 . time();
            $modelinfo['c_mcode'] = $model;
            $modelinfo['c_pcode'] = $pcode;
            $modelinfo['c_name'] = $value['mname'];
            if($value['price1']){
                $modelinfo['c_price'] = $value['price1'];
            }else{
                $modelinfo['c_price'] = $value['mprice'];
            }
            $modelinfo['c_num'] = $value['mnum'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Product_model')->add($modelinfo);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1007, '添加模型信息失败');
            }

            //收集商品信息
            if ($pricetemp == 0) {
                $pricetemp = $value['mprice'];
            } else {
                if ($value['mprice'] < $pricetemp) {
                    $pricetemp = $value['mprice'];
                }
            }

            $numzong+=$value['mnum'];

            $count1++;
        }

        //写入商品信息
        $pinfo['c_ismodel'] = 1;
        $pinfo['c_price'] = $pricetemp;
        $pinfo['c_num'] = $numzong;

        $w['c_pcode'] = $pcode;
        $result = M('Product')->where($w)->save($pinfo);

        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1011, '产品信息保存失败');
        }

        $db->commit();
        $data['pcode'] = $pcode;
        return MessageInfo(0, '添加型号成功', $data);
    }

    //添加新产品 第五步（分类、邮费、上下架）
    public function AddElse($parr) {
        $pcode = $parr['pcode'];
        if (empty($pcode)) {
            return Message(1004, '商品编码缺失');
        }

        if (empty($parr['categoryid'])) {
            return Message(1012, '分类必须选择');
        }

        $pinfo['c_categoryid'] = $parr['categoryid'];
        if (empty($parr['freeprice']) || $parr['freeprice'] == 0) {
            $pinfo['c_isfree'] = 1;
            $pinfo['c_freeprice'] = 0;
        } else {
            $pinfo['c_isfree'] = 2;
            $pinfo['c_freeprice'] = $parr['freeprice'];
        }
        $pinfo['c_ishow'] = $parr['ishow'];

        if ($parr['ishow'] != 1) {
            $where['c_pcode'] = $pcode;
            $where['c_isdel']=2;
            $where['c_state']=array("neq",2);
            $act =M('Shopact_product')->where($where)->find();
            if(!empty($act)){
                return Message(1020,'该商品已参与活动，不能下架');
            }
        }

        $w['c_pcode'] = $pcode;
        $result = M('Product')->where($w)->save($pinfo);

        if ($result < 0) {
            return Message(1013, '添加产品失败');
        }

        //添加5公里商圈记录
        //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
        $blogdata['ucode'] = $parr['ucode'];
        $blogdata['behavior'] = 2;
        $blogdata['regionid'] = $pcode;
        $blogdata['tag'] = 4;
        $blogdata['tagvalue'] = $pcode;

        //查询自己位置信息
        $result1 = IGD('Servecentre','Serve')->GetLocation($parr['ucode']);
        $localtion = $result1['data'];

        $longitude = $localtion['longitude'];
        $latitude = $localtion['latitude'];
        $address = $localtion['address'];

        $blogdata['longitude'] = $longitude;
        $blogdata['latitude'] = $latitude;
        $blogdata['address'] = $address;
        $blogdata['addtime'] = date('Y-m-d H:i:s', time());

        $result = IGD('Servecentre','Serve')->Addlogs($blogdata);

        return Message(0, '添加产品成功');
    }

    //查询商品信息
    public function GetProductInfo($parr) {
        $where['a.c_ucode'] = $parr['ucode'];
        $where['a.c_pcode'] = $parr['pcode'];

        $join = 'LEFT JOIN t_category as b on a.c_categoryid=b.c_id ';
        $field = 'a.c_pcode,a.c_ucode,a.c_name,a.c_desc,a.c_price,a.c_num,a.c_categoryid,a.c_freeprice,a.c_ishow,a.c_isagent,b.c_category_name';

        $productinfo = M('Product as a')->join($join)->where($where)->field($field)->find();
        if (!$productinfo) {
            return Message(1022, '查询产品信息失败');
        }

        //查询产品模型
        $modelwhere['c_pcode'] = $parr['pcode'];

        $modellist = M('Product_model')->where($modelwhere)->select();

//        if (count($modellist) == 0) {
//            return Message(1023, '查询产品型号失败');
//        }

        $productinfo['modellist'] = $modellist;

        //获取主图信息
        $imgwher['c_pcode'] = $parr['pcode'];
        $imgwher['c_sign'] = 1;
        $imglist = M('Product_img')->field('c_pimgepath')->where($imgwher)->select();

        if(count($imglist) == 0){
            $iw['c_pcode'] = $parr['pcode'];
            $imglist = M('Product_img')->field('c_pimgepath')->where($iw)->order('c_id asc')->limit(3)->select();
        }

        foreach ($imglist as $key2 => $value2) {
            $imglist[$key2]['c_pimgepath'] = GetHost() . '/' . $value2['c_pimgepath'];
        }

        $productinfo["mainimgs"] = $imglist;

        //获取商品图片信息
        $imgwher1['c_pcode'] = $parr['pcode'];
        $imgwher1['c_sign'] = 0;
        $imglist1 = M('Product_img')->field('c_pimgepath')->where($imgwher1)->select();

        foreach ($imglist1 as $key2 => $value2) {
            $imglist1[$key2]['c_pimgepath'] = GetHost() . '/' . $value2['c_pimgepath'];
        }

        $productinfo["imglist"] = $imglist1;
        // 分享数据
        $productinfo['sharetit'] = $productinfo['c_name'];
        $productinfo['sharedesc'] = $productinfo['c_desc'];
        $productinfo['shareimg'] = $imglist[0]['c_pimgepath'];
        $productinfo['shareurl'] = GetHost(1) . '/index.php/Shopping/Index/detail?pcode=' . $productinfo['c_pcode'].'&pucode='.$parr['ucode'];
        return MessageInfo(0, '查询成功', $productinfo);
    }

    //编辑商品步骤一
    public function SaveProudct($parr) {
        $pcode = $parr['pcode'];
        $name = $parr['name'];
        $desc = $parr['desc'];

        //查询产品信息
        $prowhere['c_ucode'] = $parr['ucode'];
        $prowhere['c_pcode'] = $pcode;
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1001, '该产品不存在');
        }

        if($produceinfo['c_isagent'] == 1){
            return Message(1002, '代理商品暂时不能修改');
        }

        if (empty($pcode) || empty($name) || empty($desc)) {
            return Message(1003, '保存信息有误');
        }

        $saveinfo['c_name'] = $name;
        $saveinfo['c_desc'] = $desc;
        $result = M('Product')->where($prowhere)->save($saveinfo);
        if ($result < 0) {
            return Message(1004, '保存产品失败');
        }

        return Message(0, '保存产品成功');
    }

    //编辑商品步骤二、三
    public function EditImgs($parr) {
        $pcode = $parr['pcode'];
        $sign = $parr['sign'];
        $imglist = $parr['imglist'];

        //查询产品信息
        $prowhere['c_ucode'] = $parr['ucode'];
        $prowhere['c_pcode'] = $pcode;
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1001, '该产品不存在');
        }

        $db = M('');
        $db->startTrans();

        //清空当前图片
        $whereinfo['c_pcode'] = $pcode;
        $whereinfo['c_sign'] = $sign;

        // $templistimg = M('Product_img')->where($whereinfo)->select();

        $result = M('Product_img')->where($whereinfo)->delete();
        if ($result < 0) {
            $db->rollback();
            return Message(1002, '删除图片失败');
        }

        if ($sign == 1) {
            $mainimg = '';
        }

        foreach ($imglist as $key => $value) {
            $imginfo = array();

            if ($sign == 1) {
                if ($key == 0) {
                    $mainimg = $value;
                }
            }

            $imginfo['c_pcode'] = $pcode;
            $imginfo['c_pimgepath'] = $value;
            $imginfo['c_sign'] = $sign;
            $imginfo['c_createtime'] = date('Y-m-d H:i:s', time());
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('Product_img')->add($imginfo);

            if ($result <= 0) {
                $db->rollback();
                return Message(1003, '添加图片失败');
            }
        }

        if (!empty($mainimg)) {
            $w['c_pcode'] = $pcode;
            $sdate['c_pimg'] = $mainimg;
            $result = M('Product')->where($w)->save($sdate);
            if ($result < 0) {
                $db->rollback();
                return Message(1005, '编辑产品表图片失败');
            }
        }

        $db->commit();
        return Message(0, "编辑图片成功");
    }

    //编辑商品步骤四
    public function EditModel($parr) {
        //查询产品型号
        $tempwhere['c_pcode'] = $parr['pcode'];
        $modellist = M('Product_model')->where($tempwhere)->select();

        $modeladd = array();
        $modelupdate = array();
        $modeldele = array();

        $tempmodellist = $parr['modellist'];
        if (count($tempmodellist) > 0) {
            foreach ($tempmodellist as $key => $value) {
                if (empty($value['mcode'])) {
                    $modeladd[] = $value;
                }

                $tag = 0;
                foreach ($modellist as $key1 => $value1) {
                    if ($value['mcode'] == $value1['c_mcode']) {
                        $modelupdate[] = $value;
                        $tag = 1;
                    }
                }
            }
        } else {
            return Message(1002, "请至少添加一个型号");
        }

        //判断删除的型号
        foreach ($modellist as $key2 => $value2) {
            $tag = 0;
            foreach ($tempmodellist as $key3 => $value3) {
                if ($value3['mcode'] == $value2['c_mcode']) {
                    $tag = 1;
                }
            }
            if ($tag == 0) {
                $modeldele[] = $value2;
            }
        }

        //修改产品型号信息
        $db = M('');
        $db->startTrans();

        //添加新模型
        $result = $this->AddModel1($parr['pcode'], $modeladd);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $pricetemp = 0;
        $temp1price = $result['data']['pricetemp'];
        $temp1numzong = $result['data']['numzong'];

        if ($temp1price > 0) {
            $pricetemp = $temp1price;
        }

        //编辑模型
        $result = $this->EditModel1($parr['pcode'], $modelupdate);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }
        $temp2price = $result['data']['pricetemp'];
        $temp2numzong = $result['data']['numzong'];

        if ($temp2price > 0) {
            if ($temp1price > 0) {
                if ($temp1price > $temp2price) {
                    $pricetemp = $temp2price;
                }
            } else {
                $pricetemp = $temp2price;
            }
        }
        $numzong = $temp1numzong + $temp2numzong;

        //删除原有模型
        $result = $this->DeleteModel($parr['pcode'], $modeldele);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //修改产品价格与库存
        $pinfo['c_ismodel'] = 1;
        $pinfo['c_price'] = $pricetemp;
        $pinfo['c_num'] = $numzong;
        $result = M('Product')->where($tempwhere)->save($pinfo);
        if ($result < 0) {
            $db->rollback();
            return Message(1010, '修改产品信息失败！');
        }
        //提交事务
        $db->commit();
        return Message(0, '编辑产品模型成功');
    }

    //新增模型
    private function AddModel1($pcode, $modelist) {
        $pricetemp = 0; //商品价格 取模型价格最小值
        $numzong = 0; //商品总库存
        $count1 = 1; //模型编码标识

        foreach ($modelist as $key => $value) {
            if (empty($value['mname']) || empty($value['mnum']) || $value['mprice'] <= 0) {
                return Message(1006, '填写型号信息有误');
            }

            //写入模型信息
            $modelinfo = array();
            $model = $count1 . time();
            $modelinfo['c_mcode'] = $model;
            $modelinfo['c_pcode'] = $pcode;
            $modelinfo['c_name'] = $value['mname'];
            if($value['price1']){
                $modelinfo['c_price'] = $value['price1'];
            }else{
                $modelinfo['c_price'] = $value['mprice'];
            }
            $modelinfo['c_num'] = $value['mnum'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Product_model')->add($modelinfo);

            if ($result <= 0) {
                return Message(1007, '添加模型信息失败');
            }

            //收集商品信息
            if ($pricetemp == 0) {
                $pricetemp = $value['mprice'];
            } else {
                if ($value['mprice'] < $pricetemp) {
                    $pricetemp = $value['mprice'];
                }
            }

            $numzong+=$value['mnum'];
            $count1++;
        }

        //写入商品信息
        $data['pricetemp'] = $pricetemp;
        $data['numzong'] = $numzong;

        return MessageInfo(0, '新增成功', $data);
    }

    //编辑产品模型
    private function EditModel1($pcode, $modelist) {
        //开始写入模型信息
        $pricetemp = 0;
        $numzong = 0;
        foreach ($modelist as $key => $value) {
            if (empty($value['mname']) || empty($value['mnum']) || $value['mprice'] <= 0) {
                return Message(1006, '填写型号信息有误');
            }

            //写入模型信息
            $modelinfo = array();
            $modelinfo['c_name'] = $value['mname'];
            if($value['price1']){
                $modelinfo['c_price'] = $value['price1'];
            }else{
                $modelinfo['c_price'] = $value['mprice'];
            }
            $modelinfo['c_num'] = $value['mnum'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $tempwhere['c_pcode'] = $pcode;
            $tempwhere['c_mcode'] = $value['mcode'];
            $result = M('Product_model')->where($tempwhere)->save($modelinfo);

            if ($result < 0) {
                return Message(1023, '模型信息修改失败');
            }

            //收集商品信息
            if ($pricetemp == 0) {
                $pricetemp = $value['mprice'];
            } else {
                if ($value['mprice'] < $pricetemp) {
                    $pricetemp = $value['mprice'];
                }
            }

            $numzong+=$value['mnum'];
        }

        $data['pricetemp'] = $pricetemp;
        $data['numzong'] = $numzong;
        return MessageInfo(0, '修改成功', $data);
    }

    //删除阶梯价
    private function DeleteModel($pcode, $modelist) {

        foreach ($modelist as $key => $value) {
            $tempwhere['c_pcode'] = $value['c_pcode'];
            $tempwhere['c_mcode'] = $value['c_mcode'];
            $result = M('Product_model')->where($tempwhere)->delete();

            if ($result <= 0) {
                return Message(1025, '删除模型成功');
            }
        }
        return Message(0, '删除成功');
    }

    //产品下架
    public function showProduct($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];

        if (!empty($parr['ishow']) && $parr['ishow'] == 1) {
            //判断商品基本信息是否为空
            $productinfo = M('Product')->field('c_name,c_desc,c_pimg,c_price,c_num')->where($where)->find();
            $info_values = array_values($productinfo);
            foreach ($info_values as $key => $value) {
                if (empty($value)) {
                    return Message(1021, "不能上架，产品基本信息不全");
                }
            }
            if($productinfo['c_num']<1){
                return Message(1020,'该商品库存不足，不能上架');
            }

            $w['c_pcode'] = $parr['pcode'];
            //判断是否存在模型
            $imgs = M('Product_img')->where($w)->select();
            if (count($imgs) == 0) {
                return Message(1022, "不能上架，产品图片没上传");
            }

//            //判断是否存在模型
//            $models = M('Product_model')->where($w)->select();
//            if (count($models) == 0) {
//                return Message(1023, "不能上架，产品不存在模型");
//            }

            $save['c_ishow'] = 1;
        } else {
            $where['c_isdel']=2;
            $where['c_state']=array("neq",2);
            $act =M('Shopact_product')->where($where)->find();
            if(!empty($act)){
                return Message(1020,'该商品已参与活动，不能下架');
            }
            $save['c_ishow'] = 2;
        }

        $result = M('Product')->where($where)->save($save);
        if ($result < 0) {
            return Message(1028, "操作失败");
        }
        return Message(0, "操作成功");
    }

    //商品批量上下架
    public function allshowProduct($parr){

        $ucode =$parr['ucode'];
        $ids =$parr['ids'];
        $ishow =$parr['ishow'];
        $arr =json_decode($ids,true);
        $w['c_ucode']=$ucode;
        $w['c_id']=array("in",$arr);

        if(count($arr) !== count($arr,1)){
           return Message(1020,'传值格式不正确');
        }

        $db =M('Product');
        $productInfo = $db->field('c_id,c_pcode,c_name,c_desc,c_pimg,c_price,c_num')->where($w)->select();

        if($ishow==1){  //执行上架
            for($i=0;$i<count($productInfo);$i++){
                $arr =$productInfo[$i];
                $list =array_values($arr);
                foreach($list as $key =>$value){
                    if (empty($value)) {
                        return Message(1021, "不能上架，产品基本信息不全");
                    }
                }

                if($arr['c_num']<1){  //库存不能小于1
                    return Message(1020, "不能上架，该产品库存不足");
                }

                $con['c_pcode']=$arr['c_pcode'];
                //判断是否存在图片
                $imgs = M('Product_img')->where($con)->select();
                if (count($imgs) == 0) {
                    return Message(1022, "不能上架，产品图片没上传");
                }

                //判断是否存在模型
//                $models = M('Product_model')->where($con)->select();
//                if (count($models) == 0) {
//                    return Message(1023, "不能上架，产品不存在模型");
//                }

                $save['c_ishow'] = 1;
                $a['c_id']=$arr['c_id'];
                $a['c_ucode']=$ucode;
                $result= $db->where($a)->save($save);
                if(!$result){
                    return Message(1024,'上架失败');
                }
            }
        }else{  //执行下架
            $save1['c_ishow'] = 2;
            $result= $db->where($w)->save($save1);
            if(!$result){
                return Message(1024,'下架失败');
            }
        }
        return Message(0,'操作成功');

    }


    //营销中心产品数据列表
    public function MarketProducts($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $rtype = $parr['rtype'];

        $where['c_ucode'] = $parr['ucode'];
        $where['c_isagent'] = 0;
        $where['c_isdele'] = 1;
        //1-购买优惠，2-分享佣金
        if ($rtype == 1) {
            $field = 'c_id,c_pcode,c_ucode,c_name,c_price,c_pimg,c_isfree,c_ishow,c_isagent,c_isrebate,c_rebate_proportion';
        } else {
            $field = 'c_id,c_pcode,c_ucode,c_name,c_price,c_pimg,c_isfree,c_ishow,c_isagent,c_isspread,c_spread_proportion';
        }

        $order = 'c_id desc';

        $count = M('Product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

        $productinfo = M('Product')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        if (!$productinfo) {
            $productinfo = array();
            $data = Page($pageIndex, $pageCount, $count, $productinfo);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($productinfo as $key => $value) {
            $productinfo[$key]['c_pimg'] = GetHost() . '/' . $productinfo[$key]['c_pimg'];
            if (empty($value['c_rebate_proportion'])) {
                $productinfo[$key]['c_rebate_proportion'] = '0';
            }
            if (empty($value['c_spread_proportion'])) {
                $productinfo[$key]['c_spread_proportion'] = '0';
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $productinfo);
        return MessageInfo(0, '查询成功', $data);
    }

    //营销中心 单个产品数据
    public function MarketPro($parr) {
        $rtype = $parr['rtype'];

        $where['c_ucode'] = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];
        if ($rtype == 1) {
            $field = 'c_pcode,c_num,c_ucode,c_name,c_price,c_pimg,c_isfree,c_ishow,c_isagent,c_isrebate,c_rebate_proportion,c_source';
        } else {
            $field = 'c_pcode,c_num,c_ucode,c_name,c_price,c_pimg,c_isfree,c_ishow,c_isagent,c_isspread,c_spread_proportion,c_source';
        }

        $productinfo = M('Product')->where($where)->field($field)->find();
        if (!$productinfo) {
            return Message(1022, '查询产品信息失败');
        }

        $productinfo['c_pimg'] = GetHost() . '/' . $productinfo['c_pimg'];
        if (empty($productinfo['c_rebate_proportion'])) {
            $productinfo['c_rebate_proportion'] = '0';
        }
        if (empty($productinfo['c_spread_proportion'])) {
            $productinfo['c_spread_proportion'] = '0';
        }

        //查询产品模型
        $modelwhere['c_pcode'] = $productinfo['c_pcode'];
        $modellist = M('Product_model')->where($modelwhere)->select();
        if (!$modellist) {
            //虚构一个型号
            $modellist[0]['c_id'] = 1;
            $modellist[0]['c_mcode'] = 'xn'.$productinfo['c_pcode'];
            $modellist[0]['c_pcode'] = $productinfo['c_pcode'];
            $modellist[0]['c_name'] = $productinfo['c_name'];
            $modellist[0]['c_price'] = $productinfo['c_price'];
            $modellist[0]['c_num'] = $productinfo['c_num'];
        }

        $productinfo['modellist'] = $modellist;
        return MessageInfo(0, '查询成功', $productinfo);
    }

    //分享佣金
    public function ProductSpread($parr) {
        $pcode = $parr['pcode'];
        $ucode = $parr['ucode'];

        //查询产品信息
        $prowhere['c_pcode'] = $pcode;
        $prowhere['c_ucode'] = $ucode;
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1001, '该产品不存在');
        }

        if ($parr['isspread'] == 1) {
            if (empty($parr['spread_proportion']) || $parr['spread_proportion'] == 0) {
                return Message(1002, '分享佣金比例参数有误');
            }

            if (($parr['spread_proportion']<0) || ($parr['spread_proportion']>20)) {
                return Message(1003, '百分比必须设置为0~20之间');
            }

            $save['c_isspread'] = $parr['isspread'];
            $save['c_spread_proportion'] = $parr['spread_proportion'];
        } else {
            $save['c_isspread'] = $parr['isspread'];
            // $save['c_spread_proportion'] = '0';
        }

        $result = M('Product')->where($prowhere)->save($save);
        if ($result < 0) {
            return Message(1028, "操作失败");
        }
        return Message(0, "操作成功");
    }

    //购买优惠
    public function ProductRebate($parr) {
        $pcode = $parr['pcode'];
        $ucode = $parr['ucode'];

        //查询产品信息
        $prowhere['c_pcode'] = $pcode;
        $prowhere['c_ucode'] = $ucode;
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1001, '该产品不存在');
        }


        if ($parr['isrebate'] == 1) {
            if (empty($parr['rebate_proportion']) || $parr['rebate_proportion'] == 0) {
                return Message(1002, '购买优惠比例参数有误');
            }

            if (($parr['rebate_proportion']<0) || ($parr['rebate_proportion']>20)) {
                return Message(1003, '百分比必须设置为0~100之间');
            }

            $save['c_isrebate'] = $parr['isrebate'];
            $save['c_rebate_proportion'] = $parr['rebate_proportion'];
        } else {
            $save['c_isrebate'] = $parr['isrebate'];
            // $save['c_rebate_proportion'] = '0';
        }

        $result = M('Product')->where($prowhere)->save($save);
        if ($result < 0) {
            return Message(1028, "操作失败");
        }
        return Message(0, "操作成功");
    }

    /*     * ******************H5**************************** */

    //获取商家信息
    public function GetProductList($parr) {

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_source'] = 1;
        $where['c_isdele'] = 1;
        $where['c_ucode'] = $parr['ucode'];

        $field = '*';
        $order = 'c_id desc';

        $list = M('Product')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?pcode=" . $value['c_pcode'];
        }

        $count = M('Product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //添加产品信息
    public function AddProudct($parr) {

        $ucode = $parr['ucode'];
        $whereproduct['c_ucode'] = $ucode;
        $whereproduct['c_isdele'] = 1;
        $count = M('Product')->where($whereproduct)->count();

//        if ($count >= 1) {
//            return Message(1022, '您已经上传了一款商品，不能在上传');
//        }
//        
        //开始写入产品信息
        $db = M('');
        $db->startTrans();

        $pricetemp = 0;
        $numzong = 0;
        $pcode = time();
        $pimgpath = "";
        $count1 = 1;
        //开始写入模型信息
        $modellist = $parr['modellist'];
        if (count($modellist) == 0) {
            return Message(1022, '至少添加一个模型');
        }
        foreach ($modellist as $key => $value) {

            $price1 = $value['price1'];
            $price2 = $value['price2'];
            $price3 = $value['price3'];

            /*收集商品信息*/
            if ($pricetemp == 0) {
                $pricetemp = $value['mprice'];
            } else {
                if ($value['mprice'] < $pricetemp) {
                    $pricetemp = $value['mprice'];
                }
            }

            // if ($price2 < $pricetemp) {
            //     $pricetemp = $price2;
            // }
            // if ($price3 < $pricetemp) {
            //     $pricetemp = $price3;
            // }

            $numzong+=$value['num'];

            $num1 = $value['maxnum1'];
            $num2 = $value['maxnum2'];
            //empty($price1) || empty($price2) || empty($price3) || empty($num1) || empty($num2) || 
            if (empty($value['mname']) || empty($value['num']) || empty($value['mprice'])) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, '填写型号信息有误');
            }

            //写入模型信息
            $modelinfo = array();
            $model = $count1 . time();
            $modelinfo['c_mcode'] = $model;
            $modelinfo['c_pcode'] = $pcode;
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['mprice'];
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Product_model')->add($modelinfo);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1023, '添加模型信息失败');
            }

            $count1++;
        }


        //写入图片信息
        $imglist = $parr['imglist'];

        $count2 = 1;
        $sign = 0;
        foreach ($imglist as $key1 => $value1) {

            $imginfo = array();

            if ($value1['sign'] == 1) {
                $imginfo['c_sign'] = 1;
                if ($sign == 0) {
                    $pimgpath = $value1['img'];
                    $sign = 1;
                }
            } else {
                $imginfo['c_sign'] = 0;
            }

            $imginfo['c_pcode'] = $pcode;
            $imginfo['c_pimgepath'] = $value1['img'];
            $imginfo['c_createtime'] = date('Y-m-d H:i:s', time());
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('Product_img')->add($imginfo);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1024, '添加图片失败');
            }
        }

        $addinfo['c_pcode'] = $pcode;
        $addinfo['c_ucode'] = $ucode;
        $addinfo['c_name'] = $parr['name'];
        $addinfo['c_desc'] = $parr['desc'];
        $addinfo['c_ismodel'] = 1;
        $addinfo['c_pimg'] = $pimgpath;
        $addinfo['c_price'] = $pricetemp;
        $addinfo['c_num'] = $numzong;
        $addinfo['c_categoryid'] = $parr['categoryid'];

        if (!empty($parr['freeprice']) || $parr['freeprice'] == 0) {
            $addinfo['c_isfree'] = 2;
            $addinfo['c_freeprice'] = $parr['freeprice'];
        } else {
            $addinfo['c_isfree'] = 1;
            $addinfo['c_freeprice'] = 0;
        }

        if (!empty($parr['ishow']) && $parr['ishow'] == 1) {
            $addinfo['c_ishow'] = 1;
        } else {
            $addinfo['c_ishow'] = 2;
        }


        // if (!empty($parr['rebate'])) {
        //     $addinfo['c_isrebate'] = 1;
        //     $addinfo['c_rebate_proportion'] = $parr['rebate'];
        // }
        // if (!empty($parr['spread'])) {
        //     $addinfo['c_isspread'] = 1;
        //     $addinfo['c_spread_proportion'] = $parr['spread'];
        // }

        $addinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $addinfo['c_updatetime'] = date('Y-m-d H:i:s', time());

        $result = M('Product')->add($addinfo);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, '产品信息操上传失败');
        }

        //添加5公里商圈记录
        //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
        $blogdata['ucode'] = $ucode;
        $blogdata['behavior'] = 2;
        $blogdata['regionid'] = $pcode;
        $blogdata['tag'] = 4;
        $blogdata['tagvalue'] = $pcode;

        //查询自己位置信息
        $result1 = IGD('Servecentre','Serve')->GetLocation($ucode);
        $localtion = $result1['data'];

        $longitude = $localtion['longitude'];
        $latitude = $localtion['latitude'];
        $address = $localtion['address'];

        $blogdata['longitude'] = $longitude;
        $blogdata['latitude'] = $latitude;
        $blogdata['address'] = $address;
        $blogdata['addtime'] = date('Y-m-d H:i:s', time());

        $result = IGD('Servecentre','Serve')->Addlogs($blogdata);

        //提交事务
        $db->commit();
        return Message(0, '添加产品成功');
    }

    //获取产品信息
    public function GetProductInfoH5($parr) {

        $where['a.c_ucode'] = $parr['ucode'];
        $where['a.c_pcode'] = $parr['pcode'];

        $join = 'LEFT JOIN t_category as b on a.c_categoryid=b.c_id ';
        $field = 'a.*,b.c_category_name';

        $productinfo = M('Product as a')->join($join)->where($where)->field($field)->find();
        $productinfo['c_pimg'] = GetHost() . '/' . $productinfo['c_pimg'];
        if (!$productinfo) {
            return Message(1022, '查询产品信息失败');
        }

        //查询产品模型
        $modelwhere['c_pcode'] = $parr['pcode'];

        $modellist = M('Product_model')->where($modelwhere)->select();

        if (count($productinfo) == 0) {
            return Message(1023, '查询产品型号失败');
        }

        $productinfo['modellist'] = $modellist;

        //获取图片信息
        $imgwher['c_pcode'] = $parr['pcode'];
        $imglist = M('Product_img')->where($imgwher)->select();

        foreach ($imglist as $key2 => $value2) {
            $imglist[$key2]['c_pimgepath'] = GetHost() . '/' . $value2['c_pimgepath'];
        }

        $productinfo["imglist"] = $imglist;
        return MessageInfo(0, '查询成功', $productinfo);
    }

    //编辑产品
    public function UpdateProductInfo($parr) {
        //查询产品信息
        $prowhere['c_pcode'] = $parr['pcode'];
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1024, '该产品不存在');
        }

        if ($produceinfo['c_isagent'] != 1) {
            //查询产品型号
            $tempwhere['c_pcode'] = $parr['pcode'];

            $modellist = M('Product_model')->where($tempwhere)->select();
            $tempmodellist = $parr['modellist'];

            $modeladd = array();
            $modelupdate = array();
            $modeldele = array();
            if (count($tempmodellist) > 0) {
                foreach ($tempmodellist as $key => $value) {
                    if (empty($value['mcode'])) {
                        $modeladd[] = $value;
                    }

                    $tag = 0;
                    foreach ($modellist as $key1 => $value1) {
                        if ($value['mcode'] == $value1['c_mcode']) {
                            $modelupdate[] = $value;
                            $tag = 1;
                        }
                    }
                }
            } else {
                return Message(1025, "请至少添加一个型号");
            }

            //判断删除的型号
            foreach ($modellist as $key2 => $value2) {
                $tag = 0;
                foreach ($tempmodellist as $key3 => $value3) {
                    if ($value3['mcode'] == $value2['c_mcode']) {
                        $tag = 1;
                    }
                }
                if ($tag == 0) {
                    $modeldele[] = $value2;
                }
            }

            //修改产品型号信息
            //开始写入产品信息
            $db = M('');
            $db->startTrans();

            $result = $this->AddModelH5($parr['pcode'], $modeladd);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }


            $pricetemp = 0;
            $temp1price = $result['data']['pricetemp'];
            $temp1numzong = $result['data']['numzong'];

            $result = $this->EditModelH5($parr['pcode'], $modelupdate);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }
            $temp2price = $result['data']['pricetemp'];
            $temp2numzong = $result['data']['numzong'];
            if ($temp1price > 0) {
                $pricetemp = $temp1price;
            }

            if ($temp2price > 0) {
                if ($temp1price > 0) {
                    if ($temp1price > $temp2price) {
                        $pricetemp = $temp2price;
                    }
                } else {
                    $pricetemp = $temp2price;
                }
            }
            $numzong = $temp1numzong + $temp2numzong;

            $result = $this->DeleteModel($parr['pcode'], $modeldele);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            //修改图片
            $result = $this->UpdateImg($parr['pcode'], $parr['imglist']);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }

            //获取主图信息
            $pimgpath = $result['data']['imgpath'];

            //修改产品信息
            $addinfo['c_name'] = $parr['name'];
            $addinfo['c_desc'] = $parr['desc'];
            $addinfo['c_ismodel'] = 1;

            $addinfo['c_pimg'] = $pimgpath;
            $addinfo['c_price'] = $pricetemp;
            $addinfo['c_num'] = $numzong;
            $addinfo['c_categoryid'] = $parr['categoryid'];

            if (!empty($parr['freeprice']) || $parr['freeprice'] == 0) {
                $addinfo['c_isfree'] = 2;
                $addinfo['c_freeprice'] = $parr['freeprice'];
            } else {
                $addinfo['c_isfree'] = 1;
                $addinfo['c_freeprice'] = 0;
            }

            if (!empty($parr['ishow']) && $parr['ishow'] == 1) {
                $addinfo['c_ishow'] = 1;
            } else {
                $addinfo['c_ishow'] = 2;
            }


            // if (!empty($parr['rebate'])) {
            //     $addinfo['c_isrebate'] = 1;
            //     $addinfo['c_rebate_proportion'] = $parr['rebate'];
            // }
            // if (!empty($parr['spread'])) {
            //     $addinfo['c_isspread'] = 1;
            //     $addinfo['c_spread_proportion'] = $parr['spread'];
            // }

            $addinfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $prowhere['c_pcode'] = $parr['pcode'];
            $result = M('Product')->where($prowhere)->save($addinfo);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1022, '产品信息操上传失败');
            }
            //提交事务
            $db->commit();
        } else {

            if (!empty($parr['ishow']) && $parr['ishow'] == 1) {
                $addinfo['c_ishow'] = 1;
            } else {
                $addinfo['c_ishow'] = 2;
            }


            // if (!empty($parr['rebate'])) {
            //     $addinfo['c_isrebate'] = 1;
            //     $addinfo['c_rebate_proportion'] = $parr['rebate'];
            // }
            // if (!empty($parr['spread'])) {
            //     $addinfo['c_isspread'] = 1;
            //     $addinfo['c_spread_proportion'] = $parr['spread'];
            // }

            $addinfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $prowhere['c_pcode'] = $parr['pcode'];
            $result = M('Product')->where($prowhere)->save($addinfo);
            if ($result <= 0) {
                return Message(1022, '产品信息操上传失败');
            }
        }
        return Message(0, '编辑产品成功');
    }

    //删除产品
    public function DeleteProduct($parr) {

        $con['c_ucode']=$parr['ucode'];
        $con['c_pcode']=$parr['pcode'];
        $con['c_state']=array('neq',2);
        $info =M('Shopact_product')->where($con)->find();
        if(!empty($info)){
            return Message(1027,"该商品正在参与活动，不能删除");
        }

        $where['c_ucode'] = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];

        $save['c_isdele'] = 2;
        $result = M('Product')->where($where)->save($save);

        if ($result <= 0) {
            return Message(1028, "删除失败");
        }
        return Message(0, "删除成功");
    }

    //新增模型
    private function AddModelH5($pcode, $modelist) {
        //开始写入模型信息
        $count1 = 1;
        $pricetemp = 0;
        $numzong = 0;
        foreach ($modelist as $key => $value) {

            $price1 = $value['price1'];
            $price2 = $value['price2'];
            $price3 = $value['price3'];
            $num1 = $value['maxnum1'];
            $num2 = $value['maxnum2'];
            /*收集商品信息*/
            if ($pricetemp == 0) {
                $pricetemp = $value['mprice'];
            } else {
                if ($value['mprice'] < $pricetemp) {
                    $pricetemp = $value['mprice'];
                }
            }

            // if ($price2 < $pricetemp) {
            //     $pricetemp = $price2;
            // }
            // if ($price3 < $pricetemp) {
            //     $pricetemp = $price3;
            // }
            //empty($price1) || empty($price2) || empty($price3) || empty($num1) || empty($num2) ||
            if (empty($value['mname']) || empty($value['num']) || $value['mprice'] <= 0) {
                return Message(1023, '填写型号信息有误');
            }

            $numzong += $value['num'];

            //写入模型信息
            $modelinfo = array();
            $model = $count1 . time();
            $modelinfo['c_mcode'] = $model;
            $modelinfo['c_pcode'] = $pcode;
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['mprice'];
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Product_model')->add($modelinfo);

            if ($result <= 0) {
                return Message(1023, '添加模型信息失败');
            }

            $count1++;
        }

        $data['pricetemp'] = $pricetemp;
        $data['numzong'] = $numzong;
        return MessageInfo(0, '新增成功', $data);
    }

    //编辑产品模型
    private function EditModelH5($pcode, $modelist) {
        //开始写入模型信息
        $pricetemp = 0;
        $numzong = 0;
        foreach ($modelist as $key => $value) {

            $price1 = $value['price1'];
            $price2 = $value['price2'];
            $price3 = $value['price3'];
            $num1 = $value['maxnum1'];
            $num2 = $value['maxnum2'];

            if ($pricetemp == 0) {
                $pricetemp = $value['mprice'];
            } else {
                if ($value['mprice'] < $pricetemp) {
                    $pricetemp = $value['mprice'];
                }
            }

            // if ($price2 < $pricetemp) {
            //     $pricetemp = $price2;
            // }
            // if ($price3 < $pricetemp) {
            //     $pricetemp = $price3;
            // }
            //empty($price1) || empty($price2) || empty($price3) || empty($num1) || empty($num2) || 
            if (empty($value['mname']) || empty($value['num']) || $value['mprice'] <= 0) {
                return Message(1023, '填写型号信息有误');
            }

            $numzong+= $value['num'];
            //写入模型信息
            $modelinfo = array();
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['mprice'];
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $tempwhere['c_pcode'] = $pcode;
            $tempwhere['c_mcode'] = $value['mcode'];
            $result = M('Product_model')->where($tempwhere)->save($modelinfo);

            // if ($result <= 0) {
            //     return Message(1023, '添加模型信息修改失败');
            // }
        }
        $data['pricetemp'] = $pricetemp;
        $data['numzong'] = $numzong;
        return MessageInfo(0, '修改成功', $data);
    }

    //更新图片
    private function UpdateImg($pcode, $imglist) {
        //清空当前图片
        $count2 = 1;
        $pimgpath = "";

        $whereinfo['c_pcode'] = $pcode;

        $templistimg = M('Product_img')->where($whereinfo)->select();

        // foreach ($templistimg as $key2 => $value2) {
        //     unlink($value2['c_pimgepath']);
        // }

        $result = M('Product_img')->where($whereinfo)->delete();

        // if ($result <= 0) {
        //     return Message(1027, '修改图片失败');
        // }
        $sign = 0;
        foreach ($imglist as $key1 => $value1) {

            $imginfo = array();

            if ($value1['sign'] == 1) {
                $imginfo['c_sign'] = 1;
                if ($sign == 0) {
                    $pimgpath = $value1['img'];
                    $sign = 1;
                }
            } else {
                $imginfo['c_sign'] = 0;
            }

            $imginfo['c_pcode'] = $pcode;
            $imginfo['c_pimgepath'] = $value1['img'];
            $imginfo['c_createtime'] = date('Y-m-d H:i:s', time());
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('Product_img')->add($imginfo);

            if ($result <= 0) {

                return Message(1024, '添加图片失败');
            }
        }

        $data['imgpath'] = $pimgpath;
        return MessageInfo(0, "修改图片成功", $data);
    }

}
