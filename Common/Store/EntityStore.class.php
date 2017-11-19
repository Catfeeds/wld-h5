<?php

/**
 * 	实体商家产品添加与查询
 *
 */
class EntityStore {

	/**
	 * 添加产品 第一步（商品名称，与描述）
	 * @param ucode,name,desc,(pcode)
	 */
    function AddProduceInfo1($parr)
    {
    	if (empty($parr['ucode'])) {
			return Message(1009,'请登录后，再操作');
		}

    	if (empty($parr['name']) || empty($parr['desc'])) {
    		return Message(1002, '信息未完善');
    	}

    	//查询产品信息
    	if (!empty($parr['pcode'])) {
	        $prowhere['c_ucode'] = $parr['ucode'];
	        $prowhere['c_pcode'] = $parr['pcode'];
	        $produceinfo = M('Product')->where($prowhere)->find();
	    }

        //生成商品编码
        $pcode = 'e'.time();
        $addinfo['c_ucode'] = $parr['ucode'];
        $addinfo['c_name'] = $parr['name'];
        $addinfo['c_desc'] = $parr['desc'];
        $addinfo['c_source'] = 2;
        $addinfo['c_updatetime'] = date('Y-m-d H:i:s');
        if ($produceinfo) {
        	$pcode = $produceinfo['c_pcode'];
        	$result = M('Product')->where($prowhere)->save($addinfo);
        } else {
        	$addinfo['c_pcode'] = $pcode;
        	$addinfo['c_addtime'] = date('Y-m-d H:i:s');
        	$result = M('Product')->add($addinfo);
        }

        if (!$result) {
            return Message(1002, '操作失败');
        }

        $data['pcode'] = $pcode;
        return MessageInfo(0, '操作成功', $data);
    }

    /**
     * 编辑商品步骤二、三
     * @param ucode,pcode,sign,imglist(主图1，附图0)
     */
    function AddProduceInfo2($parr)
    {
        //查询产品信息
        $prowhere['c_pcode'] = $parr['pcode'];
        $prowhere['c_ucode'] = $parr['ucode'];
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1001, '该产品不存在');
        }

        $db = M('');
        $db->startTrans();

        //清空对应图片
        $whereinfo['c_pcode'] = $parr['pcode'];
        $whereinfo['c_sign'] = $parr['sign'];
        $result = M('Product_img')->where($whereinfo)->delete();

        //循环写入图片
        $imglist = $parr['imglist'];
        foreach ($imglist as $key => $value) {
            if ($parr['sign'] == 1 && $key == 0) {
                $mainimg = $value;
            }

            $imginfo['c_pcode'] = $parr['pcode'];
            $imginfo['c_pimgepath'] = $value;
            $imginfo['c_sign'] = $parr['sign'];
            $imginfo['c_createtime'] = date('Y-m-d H:i:s');
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s');
            $result = M('Product_img')->add($imginfo);
            if (!$result) {
                $db->rollback();
                return Message(1003, '操作图片失败');
            }
        }

        //写入产品标志图
        if (!empty($mainimg)) {
            $sdate['c_pimg'] = $mainimg;
            $sdate['c_updatetime'] = date('Y-m-d H:i:s');
            $result = M('Product')->where($prowhere)->save($sdate);
            if (!$result) {
                $db->rollback();
                return Message(1005, '操作产品图片失败');
            }
        }

        $db->commit();
        $data['pcode'] = $parr['pcode'];
        return MessageInfo(0, "操作成功",$data);
    }

    /**
     * 编辑产品第四步（添加价格库存）
     * @param ucode,pcode,price,num
     */
    function AddProduceInfo4($parr)
    {
    	//查询产品信息
        $prowhere['c_pcode'] = $parr['pcode'];
        $prowhere['c_ucode'] = $parr['ucode'];
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1001, '该产品不存在');
        }

        if (empty($parr['price']) || empty($parr['num'])) {
        	return Message(1002, '请完善信息');
        }

    	//修改产品价格与库存
        $pinfo['c_price'] = $parr['price'];
        $pinfo['c_num'] = $parr['num'];
        $pinfo['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Product')->where($prowhere)->save($pinfo);
        if (!$result) {
            return Message(1010, '操作失败！');
        }

        $data['pcode'] = $parr['pcode'];
        return MessageInfo(0, "操作成功",$data);
    }

    /**
     * 编辑产品第五步
     * @param ucode,pcode,categoryid,freeprice,ishow
     */
    function AddProduceInfo5($parr)
    {
    	//查询产品信息
        $prowhere['c_pcode'] = $parr['pcode'];
        $prowhere['c_ucode'] = $parr['ucode'];
        $produceinfo = M('Product')->where($prowhere)->find();
        if (!$produceinfo) {
            return Message(1001, '该产品不存在');
        }

        if (empty($parr['categoryid'])) {
            return Message(1012, '分类必须选择');
        }

        if (empty($parr['freeprice']) || $parr['freeprice'] == 0) {
            $pinfo['c_isfree'] = 1;
            $pinfo['c_freeprice'] = 0;
        } else {
            $pinfo['c_isfree'] = 2;
            $pinfo['c_freeprice'] = $parr['freeprice'];
        }

        if ($parr['ishow'] != 1) {
            $where['c_pcode'] = $pcode;
            $where['c_isdel']=2;
            $where['c_state']=array("neq",2);
            $act =M('Shopact_product')->where($where)->find();
            if(!empty($act)){
                return Message(1020,'该商品已参与活动，不能下架');
            }
        }

        $pinfo['c_categoryid'] = $parr['categoryid'];
        $pinfo['c_ishow'] = $parr['ishow'];
        $pinfo['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Product')->where($prowhere)->save($pinfo);
        if (!$result) {
            return Message(1013, '操作失败');
        }

        //添加5公里商圈记录
        //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
        $blogdata['ucode'] = $parr['ucode'];
        $blogdata['behavior'] = 2;
        $blogdata['regionid'] = $parr['pcode'];
        $blogdata['tag'] = 4;
        $blogdata['tagvalue'] = $parr['pcode'];

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

        $data['pcode'] = $parr['pcode'];
        return MessageInfo(0, "操作成功",$data);
    }

    /**
     * 产品上下架处理
     * @param  ucode,pcode,ishow(1上架,2下架)
     *
     */
    function showProduct($parr)
    {
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

            $w['c_pcode'] = $parr['pcode'];
            //判断是否存在图片
            $imgs = M('Product_img')->where($w)->select();
            if (count($imgs) == 0) {
                return Message(1022, "不能上架，产品图片没上传");
            }

            $save['c_ishow'] = 1;
        } else {
            $save['c_ishow'] = 2;
        }
        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Product')->where($where)->save($save);
        if (!$result) {
            return Message(1028, "操作失败");
        }

        return Message(0, "操作成功");
    }

    /**
     * 查询商品信息
     * @param ucode,pcode
     */
    function GetProductInfo($parr)
    {
        // $where['a.c_ucode'] = $parr['ucode'];
        $where['a.c_pcode'] = $parr['pcode'];
        $join = 'LEFT JOIN t_category as b on a.c_categoryid=b.c_id ';
        $field = 'a.*,b.c_category_name';
        $productinfo = M('Product as a')->join($join)->where($where)->field($field)->find();
        if (!$productinfo) {
            return Message(1022, '查询产品信息失败');
        }
        //产品在购物车数量
        $carparr['ucode'] = $parr['ucode'];
        $carparr['pcode'] = $productinfo['c_pcode'];
        $productinfo['carnum'] =  IGD('Storecar','User')->Getprocount($carparr)['data']['count'];

        //购物车总数
        $carparr1['ucode'] = $parr['ucode'];
        $carparr1['acode'] = $productinfo['c_ucode'];
        $productinfo['carcount'] =  IGD('Storecar','User')->GetCount($carparr1)['data']['count'];

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
        $productinfo['shareurl'] = GetHost(1) . '/index.php/Shopping/Entitymap/details?pcode=' . $productinfo['c_pcode'].'&pucode='.$parr['ucode'];
        return MessageInfo(0, '查询成功', $productinfo);
    }

    /**
     * 删除产品
     * @param ucode,pcode
     */
    function DeleteProduct($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];

        $save['c_isdele'] = 2;
        $result = M('Product')->where($where)->save($save);
        if (!$result) {
            return Message(1028, "删除失败");
        }

        return Message(0, "删除成功");
    }

    /**
     * 网页添加产品与编辑产品信息
     * @param ucode,(pcode),name,desc,imglist,price,num,categoryid,freeprice,ishow
     */
    function AddProudct($parr)
    {
        $prowhere['c_ucode'] = $parr['ucode'];
        $prowhere['c_pcode'] = $parr['pcode'];
        $prowhere['c_isdele'] = 1;
        $produceinfo = M('Product')->where($prowhere)->find();

        $db = M('');
        $db->startTrans();

        //开始写入产品信息
        $pcode = 'e'.time();
        $addinfo['c_ucode'] = $parr['ucode'];
        $addinfo['c_name'] = $parr['name'];
        $addinfo['c_desc'] = $parr['desc'];
        $addinfo['c_ismodel'] = 1;
        $addinfo['c_price'] = $parr['price'];
        $addinfo['c_num'] = $parr['num'];
        $addinfo['c_source'] = 2;
        $addinfo['c_categoryid'] = $parr['categoryid'];
        $addinfo['c_updatetime'] = date('Y-m-d H:i:s');

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

        if ($produceinfo) {
        	$pcode = $produceinfo['c_pcode'];
        	$result = M('Product')->where($prowhere)->save($addinfo);
        } else {
        	$addinfo['c_pcode'] = $pcode;
        	$addinfo['c_addtime'] = date('Y-m-d H:i:s');
        	$result = M('Product')->add($addinfo);
        }

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, '产品信息操作失败');
        }

        //删除当前产品图片信息
        $pcodewhere['c_pcode'] = $pcode;
        $result = M('Product_img')->where($pcodewhere)->delete();

        //写入图片信息
        $imglist = $parr['imglist'];$ks = 0;
        foreach ($imglist as $key1 => $value1) {
        	$imginfo['c_sign'] = 0;
            if ($value1['sign'] == 1) {
                $imginfo['c_sign'] = 1;
                if ($ks == 0) {
                	$pimgpath = $value1['img'];
                }
                $ks++;
            }

            $imginfo['c_pcode'] = $pcode;
            $imginfo['c_pimgepath'] = $value1['img'];
            $imginfo['c_createtime'] = date('Y-m-d H:i:s', time());
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('Product_img')->add($imginfo);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1024, '添加图片失败');
            }
        }

        //保存产品标志图
        $addsave['c_pimg'] = $pimgpath;
        $addsave['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Product')->where($pcodewhere)->save($addsave);
        // if (!$result) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(1022, '操作产品图片失败');
        // }

        if ($parr['ishow'] != 1) {
            $where['c_pcode'] = $pcode;
            $where['c_isdel']=2;
            $where['c_state']=array("neq",2);
            $act =M('Shopact_product')->where($where)->find();
            if(!empty($act)){
                $db->commit();
                return Message(1020,'该商品已参与活动，不能下架');
            }
        }

        //提交事务
        $db->commit();
        return Message(0, '操作成功');
    }


}