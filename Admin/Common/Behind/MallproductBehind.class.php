<?php
namespace Common\Behind;

class MallproductBehind {
	
   //同步商城中的商品信息
    public function Syn_product($parr) {
    	//替换商品编码
    	$w['c_isagent'] = 1;
    	$w['c_agent_pcode'] = $parr['pcode'];

    	$pcode_arr = M('product')->field('c_pcode')->where($w)->select();

    	foreach($pcode_arr as $k1=>$v1){
    		$parr['pcode'] = $v1['c_pcode'];
    		$this->UpdateProductInfo($parr);
    	}

    }

    public function UpdateProductInfo($parr) {
        //查询产品型号
        $tempwhere['c_pcode'] = $parr['pcode'];

        $modellist = M('product_model')->where($tempwhere)->select();
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
            return Message(1025, "同步请至少添加一个型号");
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
        $result = M('product')->where($prowhere)->save($addinfo);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1022, '同步产品信息操上传失败');
        }

        //提交事务
        $db->commit();
        return Message(0, '同步商城产品信息成功');
    }

    //新增模型
    private function AddModel($pcode, $modelist) {
        //开始写入模型信息
        $count1 = 'm';
        $pricetemp = 0;
        $numzong = 0;
        foreach ($modelist as $key => $value) {

            $price1 = $value['minprice1'];
            $price2 = $value['minprice2'];
            $price3 = $value['minprice3'];
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

            if (empty($price1) || empty($price2) || empty($price3) || $num1 == '' || $num2 == '' || empty($value['mname']) || $value['num'] == '') {
                return Message(1023, '同步填写型号信息有误');
            }

            $numzong += $value['num'];

            //写入模型信息
            $modelinfo = array();
            $model = $count1 . time();
            $modelinfo['c_mcode'] = $model;
            $modelinfo['c_pcode'] = $pcode;
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['minprice1'];
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('product_model')->add($modelinfo);

            if ($result <= 0) {
                return Message(1023, '同步添加模型信息失败');
            }

            if ($num1 >= $num2) {
                return Message(1024, '同步型号的阶梯数量填写错误');
            }

            $info1['c_pcode'] = $pcode;
            $info1['c_mcode'] = $model;
            $info1['c_minnum'] = 1;
            $info1['c_maxnum'] = $num1;
            $info1['c_price'] = $price1;

            $result = M('product_ladderprice')->add($info1);
            if ($result <= 0) {
                return Message(1023, '同步添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $model;
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;

            $result = M('product_ladderprice')->add($info2);
            if ($result <= 0) {
                return Message(1023, '同步添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $model;
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;

            $result = M('product_ladderprice')->add($info3);
            if ($result <= 0) {
                return Message(1023, '同步添加阶梯价格失败');
            }

            $count1++;
        }

        $data['pricetemp'] = $pricetemp;
        $data['numzong'] = $numzong;
        return MessageInfo(0, '同步新增成功', $data);
    }

    //编辑产品模型
    private function EditModel($pcode, $modelist) {
        //开始写入模型信息
        $pricetemp = 0;
        $numzong = 0;
        foreach ($modelist as $key => $value) {

            $price1 = $value['minprice1'];
            $price2 = $value['minprice2'];
            $price3 = $value['minprice3'];
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

            if (empty($price1) || empty($price2) || empty($price3) || $num1 == '' || $num2 == '' || empty($value['mname']) || $value['num'] == '') {
                return Message(1023, '同步填写型号信息有误');
            }

            $numzong+= $value['num'];
            //写入模型信息
            $modelinfo = array();
            $modelinfo['c_name'] = $value['mname'];
            $modelinfo['c_price'] = $value['minprice1'];
            $modelinfo['c_num'] = $value['num'];
            $modelinfo['c_addtime'] = date('Y-m-d H:i:s', time());

            $tempwhere['c_pcode'] = $pcode;
            $tempwhere['c_mcode'] = $value['mcode'];
            $result = M('product_model')->where($tempwhere)->save($modelinfo);

            // if ($result <= 0) {
            //     return Message(1023, '添加模型信息修改失败');
            // }

            if ($num1 >= $num2) {
                return Message(1024, '同步型号的阶梯数量填写错误');
            }

            //删除阶梯价格
            $result = M('product_ladderprice')->where($tempwhere)->delete();

            if ($result < 0) {
                return Message(1025, '同步阶梯价格操作失败');
            }

            $info1['c_pcode'] = $pcode;
            $info1['c_mcode'] = $value['mcode'];
            $info1['c_minnum'] = 1;
            $info1['c_maxnum'] = $num1;
            $info1['c_price'] = $price1;

            $result = M('product_ladderprice')->add($info1);
            if ($result <= 0) {
                return Message(1023, '同步添加阶梯价格失败');
            }

            $info2['c_pcode'] = $pcode;
            $info2['c_mcode'] = $value['mcode'];
            $info2['c_minnum'] = $num1 + 1;
            $info2['c_maxnum'] = $num2;
            $info2['c_price'] = $price2;

            $result = M('product_ladderprice')->add($info2);
            if ($result <= 0) {
                return Message(1023, '同步添加阶梯价格失败');
            }

            $info3['c_pcode'] = $pcode;
            $info3['c_mcode'] = $value['mcode'];
            $info3['c_minnum'] = $num2 + 1;
            $info3['c_maxnum'] = 100000;
            $info3['c_price'] = $price3;

            $result = M('product_ladderprice')->add($info3);
            if ($result <= 0) {
                return Message(1023, '同步添加阶梯价格失败');
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
            $result = M('product_model')->where($tempwhere)->delete();

            if ($result <= 0) {
                return Message(1025, '同步删除模型成功');
            }

            //删除阶梯价格
            $result = M('product_ladderprice')->where($tempwhere)->delete();

            // if ($result <= 0) {
            //     return Message(1026, '删除模型阶梯价失败');
            // }
        }

        return Message(0, '同步删除成功');
    }

     //更新图片
    private function UpdateImg($pcode, $imglist) {
        //清空当前图片
        $count2 = 1;
        $pimgpath = "";

        $whereinfo['c_pcode'] = $pcode;

        $result = M('product_img')->where($whereinfo)->delete();

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
            $result = M('product_img')->add($imginfo);

            if ($result <= 0) {

                return Message(1024, '同步添加图片失败');
            }
        }

        $data['imgpath'] = $pimgpath;
        return MessageInfo(0, "同步修改图片成功", $data);
    }
}