<?php
// 小蜜商城
namespace Common\Behind;

class MallBehind {
     //添加产品信息
    public function AddProudct($parr) {
        $ucode = $parr['ucode'];
       
        //开始写入产品信息
        $db = M('');
        $db->startTrans();

        $pricetemp = 0;
        $numzong = 0;
        $pcode = 'p'.time();
        $pimgpath = "";
        $count1 = 'm';
        //开始写入模型信息
        $modellist = $parr['modellist'];
        if (count($modellist) == 0) {
            return Message(1022, '至少添加一个模型');
        }
        foreach ($modellist as $key => $value) {

            $price1 = $value['price1'];
            $price2 = $value['price2'];
            $price3 = $value['price3'];

            $minprice1 = $value['minprice1'];
            $minprice2 = $value['minprice2'];
            $minprice3 = $value['minprice3'];


            if ($pricetemp == 0) {
                $pricetemp = $price1;
            }

            if ($price1 < $pricetemp) {
                $pricetemp = $price1;
            }

            if ($price2 < $pricetemp) {
                $pricetemp = $price2;
            }

            if ($price3 < $pricetemp) {
                $pricetemp = $price3;
            }

            $numzong+=$value['num'];

            $num1 = $value['maxnum1'];
            $num2 = $value['maxnum2'];

            if (empty($price1) || empty($price2) || empty($price3) || empty($minprice1) || empty($minprice2) || empty($minprice3) || $num1 == '' || $num2 == '' || empty($value['mname']) || $value['num'] == '') {
                $db->rollback(); //不成功，则回滚
                return Message(1022, '填写型号信息有误');
            }

            //写入模型信息
            $modelinfo = array();
            $model = $count1 . time();
            $modelinfo['c_mcode'] = $model;
            $modelinfo['c_pcode'] = $pcode;
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['price1'];
            $modelinfo['c_minprice'] = $minprice1;
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Supplier_product_model')->add($modelinfo);

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
            $info1['c_minprice'] = $minprice1;

            $result = M('Supplier_product_ladderprice')->add($info1);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1023, '添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $model;
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;
            $info2['c_minprice'] = $minprice2;

            $result = M('Supplier_product_ladderprice')->add($info2);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1023, '添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $model;
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;
            $info3['c_minprice'] = $minprice3;

            $result = M('Supplier_product_ladderprice')->add($info3);
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

            if ($key1 == 0) {
                $imginfo['c_sign'] = 1;
                $pimgpath = $value1;
            } else {
                $imginfo['c_sign'] = 0;
            }

            $imginfo['c_pcode'] = $pcode;
            $imginfo['c_pimgepath'] = $value1;
            $imginfo['c_createtime'] = date('Y-m-d H:i:s', time());
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('Supplier_product_img')->add($imginfo);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1024, '添加图片失败');
            }
        }

        $addinfo['c_pcode'] = $pcode;
        $addinfo['c_ucode'] = $ucode;
        $addinfo['c_name'] = $parr['name'];
        $addinfo['c_desc'] = $parr['desc'];
        $addinfo['c_minprice'] = $parr['pminprice'];
        $addinfo['c_isagent'] = $parr['isagent'];
        $addinfo['c_piece'] = $parr['piece'];
        $addinfo['c_ismodel'] = 1;
        $addinfo['c_pimg'] = $pimgpath;
        $addinfo['c_price'] = $pricetemp;
        $addinfo['c_num'] = $numzong;
        $addinfo['c_categoryid'] = $parr['categoryid'];
        $addinfo['c_salesnum'] = $parr['salesnum'];

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

        $result = M('Supplier_product')->add($addinfo);

        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, '产品信息操上传失败');
        }

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

        $productinfo = M('supplier_product as a')->join($join)->where($where)->field($field)->find();
        $productinfo['c_pimg'] = GetHost() . '/' . $productinfo['c_pimg'];
        if (!$productinfo) {
            return Message(1022, '查询产品信息失败');
        }

        //查询供货商信息
        $ucode = $parr['ucode'];
        $w['c_ucode'] = $ucode;
        $productinfo['s_name'] = M('supplier')->where($w)->getField('c_name');

        //查询产品模型
        $modelwhere['c_pcode'] = $parr['pcode'];

        $modellist = M('supplier_product_model')->where($modelwhere)->select();

        if (count($productinfo) == 0) {
            return Message(1023, '查询产品型号失败');
        }

        foreach ($modellist as $key1 => $value1) {

            //获取阶梯价格
            $ladderpricewhere['c_pcode'] = $value1['c_pcode'];
            $ladderpricewhere['c_mcode'] = $value1['c_mcode'];

            $productladder = M('supplier_product_ladderprice')->where($ladderpricewhere)->select();

            $price1 = $productladder[0]['c_price'];
            $price2 = $productladder[1]['c_price'];
            $price3 = $productladder[2]['c_price'];
            $minprice1 = $productladder[0]['c_minprice'];
            $minprice2 = $productladder[1]['c_minprice'];
            $minprice3 = $productladder[2]['c_minprice'];
            $maxnum1 = $productladder[0]['c_maxnum'];
            $maxnum2 = $productladder[1]['c_maxnum'];

            $modellist[$key1]['price1'] = $price1;
            $modellist[$key1]['price2'] = $price2;
            $modellist[$key1]['price3'] = $price3;
            $modellist[$key1]['minprice1'] = $minprice1;
            $modellist[$key1]['minprice2'] = $minprice2;
            $modellist[$key1]['minprice3'] = $minprice3;
            $modellist[$key1]['maxnum1'] = $maxnum1;
            $modellist[$key1]['maxnum2'] = $maxnum2;

            $modellist[$key1]['ladderprice'] = $productladder;
        }

        $productinfo['modellist'] = $modellist;

        //获取图片信息
        $imgwher['c_pcode'] = $parr['pcode'];
        $imglist = M('supplier_product_img')->where($imgwher)->select();

        foreach ($imglist as $key2 => $value2) {
            $imglist[$key2]['c_pimgepath'] = GetHost() . '/' . $value2['c_pimgepath'];
        }

        $productinfo["imglist"] = $imglist;
        return MessageInfo(0, '查询成功', $productinfo);
    }

    //编辑产品
    public function UpdateProductInfo($parr) {
        //查询产品型号
        $tempwhere['c_pcode'] = $parr['pcode'];

        $modellist = M('supplier_product_model')->where($tempwhere)->select();
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
        $addinfo['c_minprice'] = $parr['pminprice'];
        $addinfo['c_isagent'] = $parr['isagent'];
        $addinfo['c_piece'] = $parr['piece'];
        $addinfo['c_ismodel'] = 1;

        $addinfo['c_pimg'] = $pimgpath;
        $addinfo['c_price'] = $pricetemp;
        $addinfo['c_num'] = $numzong;
        $addinfo['c_categoryid'] = $parr['categoryid'];
        $addinfo['c_salesnum'] = $parr['salesnum'];

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
        $result = M('supplier_product')->where($prowhere)->save($addinfo);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, '产品信息操上传失败');
        }

        //同步商城产品信息
        $result = D('Mallproduct','Behind')->Syn_product($parr);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //提交事务
        $db->commit();
        return Message(0, '编辑产品成功');
    }

    //新增模型
    private function AddModel($pcode, $modelist) {
        //开始写入模型信息
        $count1 = 'm';
        $pricetemp = 0;
        $numzong = 0;
        foreach ($modelist as $key => $value) {

            $price1 = $value['price1'];
            $price2 = $value['price2'];
            $price3 = $value['price3'];
            $minprice1 = $value['minprice1'];
            $minprice2 = $value['minprice2'];
            $minprice3 = $value['minprice3'];
            $num1 = $value['maxnum1'];
            $num2 = $value['maxnum2'];

            if ($pricetemp == 0) {
                $pricetemp = $price1;
            }

            if ($price1 < $pricetemp) {
                $pricetemp = $price1;
            }

            if ($price2 < $pricetemp) {
                $pricetemp = $price2;
            }

            if ($price3 < $pricetemp) {
                $pricetemp = $price3;
            }

            if (empty($price1) || empty($price2) || empty($price3) || empty($minprice1) || empty($minprice2) || empty($minprice3) || $num1 == '' || $num2 == '' || empty($value['mname']) || $value['num'] == '') {
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
            $modelinfo['c_minprice'] = $minprice1;
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('supplier_product_model')->add($modelinfo);

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
            $info1['c_minprice'] = $minprice1;

            $result = M('supplier_product_ladderprice')->add($info1);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $model;
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;
            $info2['c_minprice'] = $minprice2;

            $result = M('supplier_product_ladderprice')->add($info2);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $model;
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;
            $info3['c_minprice'] = $minprice3;

            $result = M('supplier_product_ladderprice')->add($info3);
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
            $minprice1 = $value['minprice1'];
            $minprice2 = $value['minprice2'];
            $minprice3 = $value['minprice3'];
            $num1 = $value['maxnum1'];
            $num2 = $value['maxnum2'];

            if ($pricetemp == 0) {
                $pricetemp = $price1;
            }

            if ($price1 < $pricetemp) {
                $pricetemp = $price1;
            }

            if ($price2 < $pricetemp) {
                $pricetemp = $price2;
            }

            if ($price3 < $pricetemp) {
                $pricetemp = $price3;
            }

            if (empty($price1) || empty($price2) || empty($price3) || empty($minprice1) || empty($minprice2) || empty($minprice3) || $num1 == '' || $num2 == '' || empty($value['mname']) || $value['num'] == '') {
                return Message(1023, '填写型号信息有误');
            }

            $numzong+= $value['num'];
            //写入模型信息
            $modelinfo = array();
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['price1'];
            $modelinfo['c_minprice'] = $minprice1;
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $tempwhere['c_pcode'] = $pcode;
            $tempwhere['c_mcode'] = $value['mcode'];
            $result = M('supplier_product_model')->where($tempwhere)->save($modelinfo);

            // if ($result <= 0) {
            //     return Message(1023, '添加模型信息修改失败');
            // }

            if ($num1 >= $num2) {
                return Message(1024, '型号的阶梯数量填写错误');
            }

            //删除阶梯价格
            $result = M('supplier_product_ladderprice')->where($tempwhere)->delete();

            if ($result < 0) {
                return Message(1025, '阶梯价格操作失败');
            }

            $info1['c_pcode'] = $pcode;
            $info1['c_mcode'] = $value['mcode'];
            $info1['c_minnum'] = 1;
            $info1['c_maxnum'] = $num1;
            $info1['c_price'] = $price1;
            $info1['c_minprice'] = $minprice1;

            $result = M('supplier_product_ladderprice')->add($info1);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $value['mcode'];
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;
            $info2['c_minprice'] = $minprice2;

            $result = M('supplier_product_ladderprice')->add($info2);
            if ($result <= 0) {
                return Message(1023, '添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $value['mcode'];
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;
            $info3['c_minprice'] = $minprice3;            

            $result = M('supplier_product_ladderprice')->add($info3);
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
            $result = M('supplier_product_model')->where($tempwhere)->delete();

            if ($result <= 0) {
                return Message(1025, '删除模型成功');
            }

            //删除阶梯价格
            $result = M('supplier_product_ladderprice')->where($tempwhere)->delete();

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

        $result = M('supplier_product_img')->where($whereinfo)->delete();

        foreach ($imglist as $key1 => $value1) {

            $imginfo = array();

            if ($key1 == 0) {
                $imginfo['c_sign'] = 1;
                $pimgpath = $value1;
            } else {
                $imginfo['c_sign'] = 0;
            }

            $imginfo['c_pcode'] = $pcode;
            $imginfo['c_pimgepath'] = $value1;
            $imginfo['c_createtime'] = date('Y-m-d H:i:s', time());
            $imginfo['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('supplier_product_img')->add($imginfo);

            if ($result <= 0) {

                return Message(1024, '添加图片失败');
            }
        }

        $data['imgpath'] = $pimgpath;
        return MessageInfo(0, "修改图片成功", $data);
    }

     /**
     *  供货商金额操作
     *  @param ucode,money,source,key,desc,state,type，isagent
     */
    function OptionMoney($parr) {
        $type = $parr['type'];
        
        $Supplier = M('Supplier');

        //查询供货商信息
        $where['c_ucode'] = $parr['ucode'];
        $Supplier_info = $Supplier->where($where)->field('c_money')->find();

        if (count($Supplier_info) == 0) {
            return Message(1002, '供货商不存在');
        }

        $moneylogdata['c_ucode'] = $parr['ucode'];
        if ($type == 0) {//支出余额
            $moneylogdata['c_money'] = '-' . $parr['money'];
        } else {//收入余额
            $moneylogdata['c_money'] = $parr['money'];
        }

        $moneylogdata['c_source'] = $parr['source'];
        $moneylogdata['c_key'] = $parr['key'];
        $moneylogdata['c_desc'] = $parr['desc'];
        $moneylogdata['c_state'] = $parr['state'];
        $moneylogdata['c_ucode'] = $parr['ucode'];
        $moneylogdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Supplier_moneylog')->add($moneylogdata);

        if (!$result) {
            return Message(1001, '增加金额记录失败');
        }

        if ($type == 0) {
            if ($Supplier_info['c_money'] < $parr['money']) {
                return Message(1003, '您的金额不够');
            }
            $result = $Supplier->where($where)->setDec('c_money', $parr['money']);
            if(!$result){
                return Message(1004, '金额减少失败');
            }
        } else {
            $result = $Supplier->where($where)->setInc('c_money', $parr['money']);
            if(!$result){
                return Message(1004, '金额增加失败');
            }
        }

        return Message(0, '修改成功');
    }

    //订单记录导出Excel表格
    function sheetIndexnt(){
        $w = array();
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w[] = "s.c_ucode = $c_ucode ";
        }

        $c_username = trim(I('c_name'));
        if (!empty($c_username)) {
            $w[] = "s.c_name LIKE '%".$c_username."%' ";
        }

        $c_tx_code = trim(I('c_tx_code'));
        if (!empty($c_tx_code)) {
            $w[] = "d.c_tx_code LIKE '%".$c_tx_code."%' ";
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');   
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "d.c_addtime between '".$begintime."' and '".$endtime."' ";
        }
       
        $state = trim(I('c_state'));
        if (!empty($state)) {
            if($state == 'sqz'){
               $w[] = "d.c_state = 0 ";
            }else{
               $w[] = "d.c_state = $state "; 
            }
        }

        $w[] = 'd.c_issupplier = 1 ';

        //数据数量
        $least=trim(I('least'));
        if(!empty($least)){
            $rise=$least;//起
        }
        $maximum=trim(I('maximum'));
        if(!empty($maximum)){
            $to=$maximum;//终至
        }
        $total=25;//总行
        $s=ceil($rise * $total) - $total;//当前页，第几条开始
        $scope=intval($to - $rise) + 1;
        $conud=ceil($scope * $total); //多少条

        $parent = I('param.');
        if(!empty($w)){
            $w1 = ' WHERE '.@implode('AND ',$w);
        }

        $sql="select d.*,s.c_name,s.c_ucode,s.c_phone from t_users_drawing as d left join t_supplier as s on s.c_ucode=d.c_ucode $w1 ORDER BY d.c_id desc LIMIT $s,$conud";
        
        $model = M('');
        $data = $model->query($sql);

        foreach ($data as $k=>$v) {
            switch ($v['c_state']) {
                case 0:
                    $data[$k]['c_state'] = '申请中';
                    break;
                case 1:
                    $data[$k]['c_state'] = '申请成功';
                    break;
                default:
                    $data[$k]['c_state'] = '申请失败';
                    break;
            }
        }
        
        $date[0][0]=array("提款编码","供货商姓名","注册手机","银行卡姓名","银行名称","银行卡号","提现金额","申请状态","备注","第三方流水号","申请时间");
        $k1=1;
        foreach($data as $k=>$v){
            $k1++;
            $date[$k1][0] = array(
                '\''.$v['c_tx_code'],
                $v['c_name'],
                $v['c_phone'],
                $v['c_uname'],
                $v['c_bankname'],
                '\''.$v['c_banksn'],
                '￥'.$v['c_money'],
                $v['c_state'],
                $v['c_remarks'],
                '\''.$v['c_thirdparty_code'],
                $v['c_addtime'],
            );
        }
        //导入PHPExcel类库
        import("Common.Org.PHPExcel");
        import("Common.Org.PHPExcel.Writer.Excel5");
        import("Common.Org.PHPExcel.IOFactory.php");
        $filename="提款记录";
        $this->getExcel($filename,$date);
    }
    
    //调用phpExcel
    private function getExcel($fileName,$data){
        //对数据进行检验
        if(empty($data)||!is_array($data)){
            die("data must be a array");
        }
        $date=date("Y_m_d",time());
        $fileName.="_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel=new \PHPExcel();
        $objProps=$objPHPExcel->getProperties();
        $column=1;
        $objActSheet=$objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);//设置默认高度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('5');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth('35');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('22');//设置列宽
    
        //设置边框
        $sharedStyle1=new \PHPExcel_Style();
        $sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
        foreach ($data as $ke=>$row){
            foreach($row as $key=>$rows){
                if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:K{$column}");//设置边框
                    array_unshift($rows,$rows['0']);
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$column}:L{$column}");//合并单元格
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getFont()->setSize(12);//字体
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getFont()->setBold(true);//粗体
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
                }else{
                    if(!empty($rows)){
                        array_unshift($rows,$key+1);
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:L{$column}");//设置边框
                    }
                }
                if($rows['1']=='提款编码'){
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
                }
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:L{$column}")->getAlignment()->setWrapText(true);//换行
                //行写入
                $span = ord("A");
    
                foreach($rows as $keyName=>$value){
                    // 列写入
                    $j=chr($span);
                    $objActSheet->setCellValue($j.$column, $value);
                    $span++;
                }
                $column++;
                    
            }
        }
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }

    //代理商品商品上下架状态同步
    public function Sync_state($pcode,$state){
        $pw['c_agent_pcode'] = $pcode;
        $productinfo = M('Product')->where($pw)->select();
        if(!$productinfo){
            return Message(0,'不存在代理商品!');
        }

        $save_data['c_ishow'] = $state;
        $result = M('Product')->where($pw)->save($save_data);
        if(!$result){
            return Message(1001,'同步商品状态失败!');
        }

        return Message(0,'同步成功!');
    }

    //代理商品商品删除状态同步
    public function Sync_del($pcode){
        $pw['c_agent_pcode'] = $pcode;
        $productinfo = M('Product')->where($pw)->select();
        if(!$productinfo){
            return Message(0,'不存在代理商品!');
        }

        $save_data['c_isdele'] = 2;
        $result = M('Product')->where($pw)->save($save_data);
        if(!$result){
            return Message(1001,'同步商品状态失败!');
        }

        return Message(0,'同步成功!');
    }
}
