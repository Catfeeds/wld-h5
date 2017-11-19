<?php

/**
 * 线上商家店铺管理第一版
 */
class BusinessStore {

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


            if ($pricetemp == 0) {
                $pricetemp = $price1;
            }

            if ($price1 < $pricetemp) {
                $pricetemp = $price1;
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

            if (empty($price1) || empty($price2) || empty($price3) || empty($num1) || empty($num2) || empty($value['mname']) || empty($value['num'])) {
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

            if ($num1 >= $num2) {
                $db->rollback(); //不成功，则回滚
                return Message(1024, '型号的阶梯数量填写错误');
            }

            $info1['c_pcode'] = $pcode;
            $info1['c_mcode'] = $model;
            $info1['c_minnum'] = 1;
            $info1['c_maxnum'] = $num1;
            $info1['c_price'] = $price1;

            $result = M('Product_ladderprice')->add($info1);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1023, '添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $model;
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;

            $result = M('Product_ladderprice')->add($info2);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1023, '添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $model;
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;

            $result = M('Product_ladderprice')->add($info3);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1023, '添加阶梯价格失败');
            }

            $count1++;
        }


        //写入图片信息
        $imglist = $parr['imglist'];

        $count2 = 1;
        foreach ($imglist as $key1 => $value1) {

            $imginfo = array();

            if ($value1['sign'] == 1) {
                $imginfo['c_sign'] = 1;
                $pimgpath = $value1['img'];
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
    public function GetProductInfo($parr) {

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

        foreach ($modellist as $key1 => $value1) {

            //获取阶梯价格
            $ladderpricewhere['c_pcode'] = $value1['c_pcode'];
            $ladderpricewhere['c_mcode'] = $value1['c_mcode'];

            $productladder = M('Product_ladderprice')->where($ladderpricewhere)->select();

            $price1 = $productladder[0]['c_price'];
            $price2 = $productladder[1]['c_price'];
            $price3 = $productladder[2]['c_price'];
            $maxnum1 = $productladder[0]['c_maxnum'];
            $maxnum2 = $productladder[1]['c_maxnum'];

            $modellist[$key1]['price1'] = $price1;
            $modellist[$key1]['price2'] = $price2;
            $modellist[$key1]['price3'] = $price3;
            $modellist[$key1]['maxnum1'] = $maxnum1;
            $modellist[$key1]['maxnum2'] = $maxnum2;

            $modellist[$key1]['ladderprice'] = $productladder;
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
            return Message(1024,'该产品不存在');
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
                        break;
                    }

                    $tag = 0;
                    foreach ($modellist as $key1 => $value1) {
                        if ($value['mcode'] == $value1['c_mcode']) {
                            $modelupdate[] = $value;
                            $tag = 1;
                            break;
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
                        break;
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

            $result = $this->AddModel($parr['pcode'], $modeladd);
            if ($result['code'] != 0) {
                $db->rollback(); //不成功，则回滚
                return $result;
            }


            $pricetemp = 0;
            $temp1price = $result['data']['pricetemp'];
            $temp1numzong = $result['data']['numzong'];

            $result = $this->EditModel($parr['pcode'], $modelupdate);
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

        $where['c_ucode'] = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];

        $save['c_isdele'] = 2;
        $result = M('Product')->where($where)->save($save);

        if ($result <= 0) {
            return Message(1028, "删除失败");
        }
        return Message(0, "删除成功");
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

            $w['c_pcode'] = $parr['pcode'];
            //判断是否存在模型
            $imgs = M('Product_img')->where($w)->select();
            if (count($imgs) == 0) {
                return Message(1022, "不能上架，产品图片没上传");
            }

            //判断是否存在模型
            $models = M('Product_model')->where($w)->select();
            if (count($models) == 0) {
                return Message(1023, "不能上架，产品不存在模型");
            }

            $save['c_ishow'] = 1;
        } else {
            $save['c_ishow'] = 2;
        }

        $result = M('Product')->where($where)->save($save);
        if ($result < 0) {
            return Message(1028, "操作失败");
        }
        return Message(0, "操作成功");
    }

    //新增模型
    private function AddModel($pcode, $modelist) {
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

            if ($pricetemp == 0) {
                $pricetemp = $price1;
            }

            if ($price1 < $pricetemp) {
                $pricetemp = $price1;
            }

            // if ($price2 < $pricetemp) {
            //     $pricetemp = $price2;
            // }

            // if ($price3 < $pricetemp) {
            //     $pricetemp = $price3;
            // }

            if (empty($price1) || empty($price2) || empty($price3) || empty($num1) || empty($num2) || empty($value['mname']) || empty($value['num'])) {
                return Message(1023, '填写型号信息有误');
            }

            $numzong += $value['num'];

            //写入模型信息
            $modelinfo = array();
            $model = $count1 . time();
            $modelinfo['c_mcode'] = $model;
            $modelinfo['c_pcode'] = $pcode;
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['price1'];
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Product_model')->add($modelinfo);

            if ($result <= 0) {
                return Message(1023, '添加模型信息失败');
            }

            if ($num1 >= $num2) {
                return Message(1024, '型号的阶梯数量填写错误');
            }

            $info1['c_pcode'] = $pcode;
            $info1['c_mcode'] = $model;
            $info1['c_minnum'] = 1;
            $info1['c_maxnum'] = $num1;
            $info1['c_price'] = $price1;

            $result = M('Product_ladderprice')->add($info1);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $model;
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;

            $result = M('Product_ladderprice')->add($info2);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $model;
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;

            $result = M('Product_ladderprice')->add($info3);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $count1++;
        }

        $data['pricetemp'] = $pricetemp;
        $data['numzong'] = $numzong;
        return MessageInfo(0, '新增成功', $data);
    }

    //编辑产品模型
    private function EditModel($pcode, $modelist) {
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
                $pricetemp = $price1;
            }

            if ($price1 < $pricetemp) {
                $pricetemp = $price1;
            }

            // if ($price2 < $pricetemp) {
            //     $pricetemp = $price2;
            // }

            // if ($price3 < $pricetemp) {
            //     $pricetemp = $price3;
            // }

            if (empty($price1) || empty($price2) || empty($price3) || empty($num1) || empty($num2) || empty($value['mname']) || empty($value['num'])) {
                return Message(1023, '填写型号信息有误');
            }

            $numzong+= $value['num'];
            //写入模型信息
            $modelinfo = array();
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['price1'];
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $tempwhere['c_pcode'] = $pcode;
            $tempwhere['c_mcode'] = $value['mcode'];
            $result = M('Product_model')->where($tempwhere)->save($modelinfo);

            // if ($result <= 0) {
            //     return Message(1023, '添加模型信息修改失败');
            // }

            if ($num1 >= $num2) {
                return Message(1024, '型号的阶梯数量填写错误');
            }

            //删除阶梯价格
            $result = M('product_ladderprice')->where($tempwhere)->delete();

            if ($result < 0) {
                return Message(1025, '阶梯价格操作失败');
            }

            $info1['c_pcode'] = $pcode;
            $info1['c_mcode'] = $value['mcode'];
            $info1['c_minnum'] = 1;
            $info1['c_maxnum'] = $num1;
            $info1['c_price'] = $price1;

            $result = M('Product_ladderprice')->add($info1);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $value['mcode'];
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;

            $result = M('Product_ladderprice')->add($info2);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $value['mcode'];
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;

            $result = M('Product_ladderprice')->add($info3);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }
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

            //删除阶梯价格
            $result = M('product_ladderprice')->where($tempwhere)->delete();

            // if ($result <= 0) {
            //     return Message(1026, '删除模型阶梯价失败');
            // }
        }

        return Message(0, '删除成功');
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

        foreach ($imglist as $key1 => $value1) {

            $imginfo = array();

            if ($value1['sign'] == 1) {
                $imginfo['c_sign'] = 1;
                $pimgpath = $value1['img'];
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
