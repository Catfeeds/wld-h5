<?php

/* 供货商城接口
 *
 */

class SupplierAgorder {

    //获取代理产品列表
    public function GetproductList($pageindex, $pagesize, $type) {

        if ($pageindex == 0 || $pageindex == '') {
            $pageindex = 1;
        }

        $countPage = ($pageindex - 1) * $pagesize;

        $whereinfo = 'c_ishow =1 and c_isdele=1';
        $orderid = '';
        if (!empty($type) && $type != -1) {
            $whereinfo .= ' and c_categoryid="'.$type.'"';
        } else if ($type == -1) {
        	$orderid .= 'c_salesnum desc,';
        }

        $orderid .= 'c_id desc';
        $list = M('Supplier_product')->where($whereinfo)->order($orderid)->limit($countPage, $pagesize)->select();


        if (count($list) <= 0) {
            return MessageInfo(2003,'数据为空',$list);
        }

        foreach ($list as $k => $v) {
            $list[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimg'];
        }

        $dataCount = M('Supplier_product')->where($whereinfo)->count();
        $pageCount = ceil($dataCount / $pagesize);

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return MessageInfo(0,'查询成功',$listinfo);
    }


    //代理商品详情
    public function GetProductInfo($parr) {
        $pcode = $parr['pcode'];
        $ucode = $parr['ucode'];
        if (empty($pcode)) {
            return Message(2000,'请输入产品编号');
        }

        $whereinfo = "c_ishow =1 and c_isdele=1";

        $whereinfo.=" and c_pcode='$pcode'";
        $productinfo = M('Supplier_product')->where($whereinfo)->find();

        if (count($productinfo) <= 0) {
            return Message(2001,'产品不存在');;
        }

        $productinfo['c_pimg'] = GetHost() . '/' . $productinfo['c_pimg'];

        //查询产品图片列表
        $imgwhere['c_pcode'] = $pcode;
        $imglist = M('Supplier_product_img')->where($imgwhere)->select();


        $count = 0;
        foreach ($imglist as $k => $v) {
            $imglist[$k]['c_pimgepath'] = GetHost() . '/' . $v['c_pimgepath'];

            if ($count < 3) {
                $bannerlist[$count]['img'] = GetHost() . '/' . $v['c_pimgepath'];
                $count++;
            }
        }
        $productinfo['bannerimg'] = $bannerlist;

        $productinfo['imglist'] = $imglist;
        //查询产品型号
        $modlewhere['c_pcode'] = $pcode;
        $modellist = M('Supplier_product_model')->where($modlewhere)->select();


        if (count($modellist) > 0) {
            foreach ($modellist as $key1 => $value1) {
                $where1['c_pcode'] = $pcode;
                $where1['c_mcode'] = $value1['c_mcode'];
                $ladderprice = M('Supplier_product_ladderprice')->where($where1)->select();
                $str = "";
                $tempint = count($ladderprice);
                for ($x = 0; $x < $tempint; $x++) {
                    $min = $ladderprice[$x]['c_minnum'];
                    $max = $ladderprice[$x]['c_maxnum'];
                    $price = $ladderprice[$x]['c_price'];

                    if ($x == 2) {
                        $temp = "累计购买" . $min . "个或" . $min . "以上：单价￥" . $price;
                        $str.=$temp;
                        break;
                    } else {
                        $temp = "累计购买" . $min . "-" . $max . "个：单价￥" . $price . "|";
                        $str .=$temp;
                    }
                }
                $modellist[$key1]['ladderprice'] = $ladderprice;
                $modellist[$key1]['pricestr'] = $str;
            }
            $productinfo['modellist'] = $modellist;
        }

        $count = 0;
        if (!empty($ucode)) {
            $count = IGD('Supplyorder', 'Agorder')->Getoldproduct($ucode, $pcode);
        }
        $productinfo['cumulative'] = $count;
        return MessageInfo(0,'查询成功',$productinfo);
    }

    //查询用户是否代理某产品
    public function SupplyProduce($parr)
    {
    	$where['c_ucode'] = $parr['ucode'];
    	$where['c_pcode'] = $parr['pcode'];
        $where['c_isdele'] = 1;
    	$result = M('Product')->where($where)->find();
    	if (!$result) {
    		return Message(2000,'没有数据');
    	}
    	return MessageInfo(0,'查询成功',$result);
    }

     //获取商品评论信息
    public function GetScore($parr) {
        $pcode = $parr['pcode'];
        $whereinfo[] = array("a.c_agent_pcode='".$pcode."' or a.c_pcode='".$pcode."'");

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $order = 'a.c_addtime desc';
        $list = M('product_score as a')->join($join)->where($whereinfo)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {

            //修改评论时间
            $list[$key]['c_addtime'] = date('Y-m-d', strtotime($value['c_addtime']));

            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];

            $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            //查询评论图片
            $where1['c_regionid'] = $value['c_id'];
            $where1['c_sourceid'] = 3;
            $field = 'c_img,c_thumbnail_img';
            $imglist = M('Resource_img')->where($where1)->field($field)->select();

            foreach ($imglist as $key1 => $value1) {
                $imglist[$key1]['c_img'] = GetHost() . '/' . $value1['c_img'];
                $imglist[$key1]['c_thumbnail_img'] = GetHost() . '/' . $value1['c_thumbnail_img'];
            }
            $list[$key]["imglist"] = $imglist;
        }

        $count = M('product_score as a')->join($join)->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //一键创建代理订单
    public function CreatAgentOrder($orderinfo) {
        $orderid = 's'.$orderinfo['c_orderid'];
        $otwhere['c_orderid'] = $orderinfo['c_orderid'];
        $orderinfo = M('Order')->where($otwhere)->find();
        if (!$orderinfo) {
            return Message(3000, "订单不存在");
        }
        $wheredetails['c_orderid'] = $orderinfo['c_orderid'];
        //获取订单详情
        $detalilist = M('Order_details')->where($wheredetails)->select();

        //计算订单的总价
        $totprice = 0;
        foreach ($detalilist as $d => $v1) {
            $singletotle = 0;
            $price = 0;
            //查询代理商的产品价格
            $tempwhere['c_pcode'] = $v1['c_agent_pcode'];
            $productinfo = M('Supplier_product')->where($tempwhere)->find();
            $scode = $productinfo['c_ucode'];
            if (count($productinfo) == 0) {
                return Message(3001, "没有查询到产品信息");
            }
            $price = $productinfo['c_price'];

            $agntproduce['c_pcode'] = $v1['c_pcode'];
            $severtype = M('Product')->where($agntproduce)->getField('c_severtype');

           if (!empty($v1['c_pmodel'])) {
                $tempwhere['c_pcode'] = $v1['c_agent_pcode'];
                $tempwhere['c_mcode'] = $v1['c_pmodel'];
                $productinfomodel = M('Supplier_product_model')->where($tempwhere)->find();

                if (count($productinfomodel) == 0) {
                    return Message(3002, "没有查询到该产品的模型信息");
                }
                $price = $productinfomodel['c_price'];
            }

            $oldcount = IGD('Supplyorder','Agorder')->Getoldproduct($v1['c_ucode'], $v1['c_agent_pcode']);
            $temp2 = $v1['c_pnum'] + $oldcount;
            //判断是否存在阶梯价格
            $wherejieti['c_pcode'] = $v1['c_pcode'];
            $wherejieti['c_mcode'] = $v1['c_pmodel'];
            $wherejieti['c_minnum'] = array('ELT', $temp2);
            $wherejieti['c_maxnum'] = array('EGT', $temp2);
            $jietiinfo = M('Supplier_product_ladderprice')->where($wherejieti)->find();
            if (count($jietiinfo) > 0) {
                $price = $jietiinfo['c_price'];
            }

            $tempcount = $price * $v1['c_pnum'];
            $singletotle = sprintf("%.2f", $tempcount);
            //生成订单详情
            $tempdetails = array();
            $tempdetails['c_orderid'] = $orderid;
            $tempdetails['c_detailid'] = 's'.$v1['c_detailid'];
            $tempdetails['c_ucode'] = $v1['c_ucode'];
            $tempdetails['c_pname'] = $v1['c_pname'];
            $tempdetails['c_pcode'] = $v1['c_agent_pcode'];
            $tempdetails['c_pprice'] = $price;
            $tempdetails['c_pmodel'] = $v1['c_pmodel'];
            $tempdetails['c_pmodel_name'] = $v1['c_pmodel_name'];
            $tempdetails['c_pnum'] = $v1['c_pnum'];
            $tempdetails['c_ptotal'] = $singletotle;
            $tempdetails['c_pimg'] = $v1['c_pimg'];
            $tempdetails['c_free'] = $v1['c_free'];
            //查询系统配置
            $settinginfo = IGD('Common', 'Info')->GetSystemSet();
            $setting = $settinginfo['data'];
            if ($settinginfo['code'] != 0) {
                return Message(1024, '系统配置不存在');
            }

            $supplyscale = bcmul($singletotle,$setting['c_agent_scale'],2);
            $tempdetails['c_commission'] = bcdiv($supplyscale,100,2);
            $tempdetails['c_profit'] = $singletotle - bcdiv($supplyscale,100,2) + $v1['c_free'];
            $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Supplier_order_details')->add($tempdetails);

            if (!$result) {
                return Message(3003, "生成订单详情失败");
            }

            $totprice +=$singletotle;
        }

        //生成订单地址
        $temporderid = $orderinfo['c_orderid'];
        $sql = "insert into t_supplier_order_address (c_orderid,c_consignee,c_telphone,c_address,c_province,c_cityname,c_district,c_provinceid,c_cityid,c_districtid) "
                . "select '$orderid',c_consignee,c_telphone,c_address,c_province,c_cityname,c_district,c_provinceid,"
                . "c_cityid,c_districtid from t_order_address where c_orderid='$temporderid'";
        $db = M('');
        $result = $db->execute($sql);
        if (!$result) {
            return Message(3004, "创建用户地址失败");
        }

        //生成订单
        $aorderinfo = array();
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_acode'] = $scode;
        $aorderinfo['c_scode'] = $orderinfo['c_acode'];
        $aorderinfo['c_pay_state'] = 1;
        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_pay_rule'] = $orderinfo['c_pay_rule'];
        $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());
        $aorderinfo['c_deliverystate'] = 0;
        $aorderinfo['c_free'] = $orderinfo['c_free'];
        $aorderinfo['c_ucode'] = $orderinfo['c_ucode'];
        $aorderinfo['c_actual_price'] = $totprice;
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = 1;
        $aorderinfo['c_postscript'] = $orderinfo['c_postscript'];
        $aorderinfo['c_agent_orderid'] = $orderinfo['c_orderid'];
        $aorderinfo['c_severtype'] = $severtype;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('Supplier_order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }

        return Message(0, "创建订单成功");
    }

    //一键代理添加产品
    public function AgentAddproduct($v, $ucode, $count,$servertype) {
        $pcode = 's'.$count.time();

        //查询是否代理
        $agentprowhere['c_ucode'] = $ucode;
        $agentprowhere['c_agent_pcode'] = $v['c_pcode'];
        $agentprowhere['c_isdele'] = 1;
        $agentproinfo = M('Product')->where($agentprowhere)->find();
        if ($agentproinfo) {
            return Message(0,'代理成功');
        }
        $info = array();
        $info['c_pcode'] = $pcode;
        $info['c_ucode'] = $ucode;
        $info['c_name'] = $v['c_name'];
        $info['c_desc'] = $v['c_desc'];
        $info['c_ismodel'] = $v['c_ismodel'];
        $info['c_pimg'] = $v['c_pimg'];
        $info['c_num'] = $v['c_num'];
        $info['c_price'] = $v['c_minprice'];
        $info['c_categoryid'] = $v['c_categoryid'];
        $info['c_isfree'] = $v['c_isfree'];
        $info['c_freeprice'] = $v['c_freeprice'];
        $info['c_ishow'] = 1;
        $info['c_isdele'] = $v['c_isdele'];
        $info['c_isshoptuijian'] = 0;
        $info['c_isrebate'] = 0;
        $info['c_isspread'] = 0;
        $info['c_isagent'] = 1;
        $info['c_agent_pcode'] = $v['c_pcode'];
        $info['c_severtype'] = $servertype;
        $info['c_addtime'] = date('Y-m-d H:i:s', time());
        $info['c_updatetime'] = date('Y-m-d H:i:s', time());

        $result = M('Product')->add($info);

        if (!$result) {
            return Message(3006, "一键代理添加产品失败");
        }
        $count ++;
        $db = M('');
        $dailipcode = $v['c_pcode'];
        //插入图片信息
        $sql = "insert into t_product_img (c_pcode,c_pimgepath,c_sign,c_createtime,c_updatetime) "
                . " select '$pcode',c_pimgepath,c_sign,c_createtime,c_updatetime from t_supplier_product_img where c_pcode='$dailipcode'";
        $result = $db->execute($sql);

        if (!$result) {
            return Message(3007, "一键代理添加产品图片失败");
        }

        //插入类型
        $sql1 = "insert into t_product_model (c_mcode,c_pcode,c_name,c_price,c_num,c_addtime) "
                . " select c_mcode,'$pcode',c_name,c_minprice,c_num,c_addtime from t_supplier_product_model where c_pcode='$dailipcode'";
        $result = $db->execute($sql1);

        if (!$result) {
            return Message(3008, "一键代理添加产品型号失败");
        }

        //插入阶梯价
        $sql2 = "insert into t_product_ladderprice (c_pcode,c_mcode,c_minnum,c_maxnum,c_price) "
                . " select '$pcode',c_mcode,c_minnum,c_maxnum,c_minprice from t_supplier_product_ladderprice where c_pcode='$dailipcode'";

        $result = $db->execute($sql2);

        if (!$result) {
            return Message(3008, "一键代理添加产品阶梯价失败");
        }

        //代理人数加一
        $agentnumwhere['c_pcode'] = $dailipcode;
        $result = M('Supplier_product')->where($agentnumwhere)->setInc('c_agentnum',1);
        if (!$result) {
            return Message(3009, "代理数增加失败");
        }

        return Message(0,'代理成功');
    }


    /**
     *  供货商金额操作
     *  @param ucode,money,source,key,desc,state,type
     */
    public function OptionMoney($parr) {

        $isagent = $parr['isagent'];
        $type = $parr['type'];

        $User = M('Supplier');

        //查询用户信息
        $userwhere['c_ucode'] = $parr['ucode'];
        $Userinfo = $User->where($userwhere)->field('c_money')->find();

        if (count($Userinfo) == 0) {
            return Message(1002, '用户不存在');
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
            if ($Userinfo['c_money'] < $parr['money']) {
                return Message(1003, '您的金额不够');
            }
            $result = $User->where($userwhere)->setDec('c_money', $parr['money']);
            if(!$result){
                return Message(1004, '金额减少失败');
            }
        } else {
            $result = $User->where($userwhere)->setInc('c_money', $parr['money']);
            if(!$result){
                return Message(1004, '金额增加失败');
            }
        }

        return Message(0, '修改成功');
    }
}
