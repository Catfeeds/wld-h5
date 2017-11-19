<?php

/**
 * 促销管理
 */
class PromoteActivity {

    /**
     * 添加促销产品
     * @param ucode,pcode,rule,price,discount,num,joinnum,startime,endtime
     */
    function AddPromoteProduce($parr)
    {
        //查询产品是否存在
        $where['c_ishow'] = 1;
        $where['c_isdele'] = 1;
        $where['c_ucode'] = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];
        $productinfo = M('Product')->where($where)->find();
        if (!$productinfo) {
            return Message(2000,'选择的产品已删除或已下架');
        }

        $totalnum = ($parr['num'] > $productinfo['c_num'])?$productinfo['c_num']:$parr['num'];
        //查询产品是否在活动中
        $promowhere['c_pcode'] = $parr['pcode'];
        $promowhere['c_delete'] = 0;
        $promoteinfo = M('Product_promote')->where($promowhere)->find();
        $promodata['c_state'] = 0;
        $promodata['c_reason'] = null;
        $promodata['c_pcode'] = $parr['pcode'];
        $promodata['c_ucode'] = $parr['ucode'];
        $promodata['c_rule'] = $parr['rule'];
        $promodata['c_price'] = $parr['price'];
        $promodata['c_discount'] = $parr['discount'];
        $promodata['c_totalnum'] = $totalnum;
        $promodata['c_num'] = $totalnum;
        $promodata['c_joinnum'] = $parr['joinnum'];
        $promodata['c_penum'] = $parr['penum'];
        $promodata['c_startime'] = date('Y-m-d H:i:s',strtotime($parr['startime']));
        $promodata['c_endtime'] = date('Y-m-d H:i:s',strtotime($parr['endtime']));
        $promodata['c_edittime'] = date('Y-m-d H:i:s');
        if ($promoteinfo) {
            $result = M('Product_promote')->where($promowhere)->save($promodata);
        } else {
            $promodata['c_createtime'] = date('Y-m-d H:i:s');
            $result = M('Product_promote')->add($promodata);
        }

        if (!$result) {
            return Message(2004,'保存失败');
        }

        return Message(0,'保存成功');
    }

    /**
     * 按规则查询平台活动
     * @param rule,(aid)
     */
    function GetPlatformActivity($parr)
    {
        $where['c_activitytype'] = $parr['rule'];
        $data = M('Activity')->where($where)->order('c_id desc')->find();
        return $data;
    }
    /**
     * 审核与删除促销产品
     * @param gettype(1审核，2删除),pid,(ucode),reason
     */
    function HandPromoteProduce($parr)
    {
        $promowhere1['a.c_id'] = $parr['pid'];
        if ($parr['gettype'] == 2) {
            $promowhere1['b.c_ucode'] = $parr['ucode'];
        }
        $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
        $promoteinfo = M('Product_promote as a')->join($join)->where($promowhere1)->field('a.*')->find();
        if (!$promoteinfo) {
            return Message(2003,'该促销产品不存在');
        }

        $promowhere['c_id'] = $parr['pid'];
        if ($parr['gettype'] == 1) {
            $promodata['c_reason'] = $parr['reason'];
            $promodata['c_state'] = 1;
            $promodata['c_checktime'] = date('Y-m-d H:i:s');
        } else if ($parr['gettype'] == 2) {
            $promodata['c_delete'] = 1;
        }
        $result = M('Product_promote')->where($promowhere)->save($promodata);
        if (!$result) {
            return Message(2004,'操作失败');
        }

        return Message(0,'操作成功');
    }

    /**
     * 查询用户可参与促销活动的产品列表
     * @param  pageindex,pagesize,ucode,
     */
    function GetJoinProduce($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['a.c_ishow'] = 1;
        $where['a.c_isdele'] = 1;
        $where['a.c_isagent'] = 0;
        $where['a.c_ucode'] = $parr['ucode'];
        $where[] = array("(b.c_id is null or b.c_id='') or (b.c_id is not null and b.c_delete=1)");

        $join = 'left join t_product_promote as b on a.c_pcode=b.c_pcode';
        $field = 'a.*';
        $order = 'a.c_id desc';
        $list = M('Product as a')->join($join)->where($where)->field($field)
                ->order($order)->limit($countPage, $pageSize)->select();

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }

        $count = M('Product as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

	/**
	 * 查询促销产品列表
	 * @param rule(12好友拼团,13限时抢购,14群龙夺宝),state
	 */
    function GetPromoteList($parr)
    {
    	if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        //查询平台活动是否存在
        $activitywhere['c_state'] = 1;            //活动开启状态
        $activitywhere['c_activitytype'] = $parr['rule'];     //发现活动类型标识
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(1018, '平台尚未开放此活动！');
        }

        if ($parr['state'] == 1) {
            $where['a.c_state'] = 1;
            $order = "pnum=0 asc,case when if(date_format(a.c_endtime,'%Y-%m-%d %H:%i:%s')<=now(),1,0)=0 then 0 else 1 end asc,a.c_startime asc,a.c_endtime desc";
        } else {
            $where['b.c_ucode'] = $parr['ucode'];
            $order = 'c_state asc,c_id desc';
        }
    	$where['a.c_delete'] = 0;
    	$where['a.c_rule'] = $parr['rule'];
    	$where['b.c_ishow'] = 1;
        $where['b.c_isdele'] = 1;

    	$join = 'left join t_product as b on a.c_pcode=b.c_pcode';
    	$field = 'b.*,a.c_rule,a.c_price as pprice,a.c_num as pnum,a.c_joinnum,a.c_startime,a.c_penum'
    		  .',a.c_endtime,a.c_state,a.c_delete,a.c_createtime,a.c_id as pid,a.c_discount,a.c_totalnum,a.c_reason';
    	$list = M('Product_promote as a')->join($join)->where($where)->field($field)
    		  ->order($order)->limit($countPage, $pageSize)->select();
    	if (!$list) {
    		return Message(1002,'没有相关数据');
    	}

        foreach ($list as $key => $value) {
            if ($value['c_rule'] == 14) {
                $list[$key]['c_promoteprice'] = $value['pprice'];
            } else {
                $list[$key]['c_promoteprice'] = bcmul($value['c_price'],bcdiv($value['c_discount'],10,2),2);
            }

        	$qian = array(" ","　","\t","\n","\r");
	        $hou = array("","","","","");
	        $list[$key]['c_desc'] = str_replace($qian,$hou,$value['c_desc']);
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['url'] = GetHost(1) . '/' . "index.php/Home/Shop/details?pcode=" . $value['c_pcode'];

            //组装数据
            $list[$key]['starttime'] =  strtotime($value['c_startime']) - time();
            $list[$key]['endtime'] =  strtotime($value['c_endtime']) - time();

            $list[$key]['timestart'] = date('m月d日 H:i',strtotime($value['c_startime']));
            $list[$key]['timeend'] = date('m月d日 H:i',strtotime($value['c_endtime']));
            if ($value['pnum'] > 0) {
                $list[$key]['substatus'] = 1;
            } else {
                $list[$key]['substatus'] = 0;
            }

            $joinedjoin = 'left join t_order as b on a.c_orderid=b.c_orderid';
            $orderlogwhere['a.c_pcode'] = $value['c_pcode'];
            $orderlogwhere['a.c_promoteid'] = $value['pid'];
            if ($value['c_rule'] == 14) {
                $orderlogwhere['b.c_pay_state'] = 1;
            }
            $list[$key]['joined'] = M('Order_details as a')->join($joinedjoin)->where($orderlogwhere)->count();

            $orderlogwhere['a.c_ucode'] = $parr['ucode'];
            $promorderlog = M('Order_details as a')->join($joinedjoin)->where($orderlogwhere)->count();
            if ($promorderlog > $list[$key]['c_penum']) {
                $list[$key]['substatus'] = 2;
            }

            if ($list[$key]['starttime'] > 0) {
                $list[$key]['startstatu'] = 0;
            } else if ($list[$key]['starttime'] < 0 && $list[$key]['endtime'] > 0) {
                $list[$key]['startstatu'] = 1;
            } else {
                $list[$key]['startstatu'] = 2;
            }
        }

        $count = M('Product_promote as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 首页查询促销产品
     * @param rule(12好友拼团,13限时抢购,14群龙夺宝),num
     */
    function GetPromoteIndex($parr)
    {
        //查询平台活动是否存在
        $activitywhere['c_state'] = 1;            //活动开启状态
        $activitywhere['c_activitytype'] = $parr['rule'];     //发现活动类型标识
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(1018, '平台尚未开放此活动！');
        }

        $where['a.c_state'] = 1;
        $where['a.c_delete'] = 0;
        $where['a.c_rule'] = $parr['rule'];
        $where['b.c_ishow'] = 1;
        $where['b.c_isdele'] = 1;
        // $where['a.c_startime'] = array('ELT', date('Y-m-d H:i:s'));
        // $where['a.c_endtime'] = array('EGT', date('Y-m-d H:i:s'));

        $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
        $field = 'b.*,a.c_rule,a.c_price as pprice,a.c_num as pnum,a.c_joinnum,a.c_startime,a.c_penum'
              .',a.c_endtime,a.c_state,a.c_delete,a.c_createtime,a.c_id as pid,a.c_discount,a.c_totalnum';
        $order = "pnum=0 asc,case when if(date_format(a.c_endtime,'%Y-%m-%d %H:%i:%s')<=now(),1,0)=0 then 0 else 1 end asc,a.c_startime asc,a.c_endtime desc";
        $list = M('Product_promote as a')->join($join)->where($where)->field($field)
              ->order($order)->limit($parr['num'])->select();
        if (!$list) {
            return Message(1002,'没有相关数据');
        }

        foreach ($list as $key => $value) {
            if ($value['c_rule'] == 14) {
                $list[$key]['c_promoteprice'] = $value['pprice'];
            } else {
                $list[$key]['c_promoteprice'] = bcmul($value['c_price'],bcdiv($value['c_discount'],10,2),2);
            }

            $qian = array(" ","　","\t","\n","\r");
            $hou = array("","","","","");
            $list[$key]['c_desc'] = str_replace($qian,$hou,$value['c_desc']);
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];

            //组装数据
            $list[$key]['starttime'] =  strtotime($value['c_startime']) - time();
            $list[$key]['endtime'] =  strtotime($value['c_endtime']) - time();
            if ($value['pnum'] > 0) {
                $list[$key]['substatus'] = 1;
            } else {
                $list[$key]['substatus'] = 0;
            }

            $joinedjoin = 'left join t_order as b on a.c_orderid=b.c_orderid';
            $orderlogwhere['a.c_pcode'] = $value['c_pcode'];
            $orderlogwhere['a.c_promoteid'] = $value['pid'];
            if ($value['c_rule'] == 14) {
                $orderlogwhere['b.c_pay_state'] = 1;
            }
            $list[$key]['joined'] = M('Order_details as a')->join($joinedjoin)->where($orderlogwhere)->count();

            $orderlogwhere['a.c_ucode'] = $parr['ucode'];
            $promorderlog = M('Order_details as a')->join($joinedjoin)->where($orderlogwhere)->count();
            if ($promorderlog >= $value['c_penum']) {
                $list[$key]['substatus'] = 2;
            }

            if ($list[$key]['starttime'] > 0) {
                $list[$key]['startstatu'] = 0;
            } else if ($list[$key]['starttime'] < 0 && $list[$key]['endtime'] > 0) {
                $list[$key]['startstatu'] = 1;
            } else {
                $list[$key]['startstatu'] = 2;
            }
        }

        return MessageInfo(0, '查询成功', $list);
    }

    /**
     * 查询单个促销产品详情
     * @param pid,gettype
     */
    function GetPromoteInfo($parr)
    {
    	$where['a.c_id'] = $parr['pid'];
        if (empty($parr['gettype'])) {
            $where['a.c_state'] = 1;
            $where['a.c_delete'] = 0;
            $where['a.c_startime'] = array('ELT', date('Y-m-d H:i:s'));
            $where['a.c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
        }
    	$where['b.c_ishow'] = 1;
        $where['b.c_isdele'] = 1;
    	$join = 'left join t_product as b on a.c_pcode=b.c_pcode';
    	$field = 'b.*,a.c_rule,a.c_price as pprice,a.c_num as pnum,a.c_joinnum,a.c_startime,a.c_penum'
    		  .',a.c_endtime,a.c_state,a.c_delete,a.c_createtime,a.c_id as pid,a.c_discount,a.c_totalnum';
    	$data = M('Product_promote as a')->join($join)->where($where)->field($field)->find();
        if (!$data) {
            return Message(1017, '该活动产品信息不存在');
        }

        //查询平台活动是否存在
        $activitywhere['c_state'] = 1;            //活动开启状态
        $activitywhere['c_activitytype'] = $data['c_rule'];     //发现活动类型标识
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(1018, '平台尚未开放此活动！');
        }

        $data['actiname'] = $activitydata['c_activityname'];
        $data['oldprice'] = $data['c_price'];
        $data['c_num'] = $data['pnum'];
        if ($data['c_rule'] == 14) {
            $data['c_price'] = $data['pprice'];
        } else {
            $data['c_price'] = bcmul($data['c_price'],bcdiv($data['c_discount'],10,2),2);
        }
        $data['c_pimg'] = GetHost() . '/' . $data['c_pimg'];
        $qian = array(" ","　","\t","\n","\r");
        $hou = array("","","","","");
        $data['c_desc'] = str_replace($qian,$hou,$data['c_desc']);
        $data['url'] = GetHost(1) . "/index.php/Home/Promote/detail?pid=" . $data['pid'];


        if ($data['pnum'] > 0) {
            $data['substatus'] = 1;
        } else {
            $data['substatus'] = 0;
        }

        $joinedjoin = 'left join t_order as b on a.c_orderid=b.c_orderid';
        $orderlogwhere['a.c_pcode'] = $data['c_pcode'];
        $orderlogwhere['a.c_promoteid'] = $data['pid'];
        if ($data['c_rule'] == 14) {
            $orderlogwhere['b.c_pay_state'] = 1;
            $orderlogwhere['b.c_pay_rule'] = array('neq',5);
        }
        $data['joined'] = M('Order_details as a')->join($joinedjoin)->where($orderlogwhere)->count();

        $orderlogwhere['a.c_ucode'] = $parr['ucode'];
        $promorderlog = M('Order_details as a')->join($joinedjoin)->where($orderlogwhere)->count();
        if ($promorderlog >= $data['c_penum']) {
            $data['substatus'] = 2;
        }

        $imgwhere['c_pcode'] = $data['c_pcode'];
        $imgs = M('Product_img')->where($imgwhere)->field('c_pimgepath,c_sign')->select();

        $count = 0;
        $count1 = 0;
        foreach ($imgs as $key => $value) {
            if ($value['c_sign'] == 1) {
                $bannerlist[$count]['img'] = GetHost() . '/' . $value['c_pimgepath'];
                $count++;
            } else {
                $delist[$count1]['c_pimgepath'] = GetHost() . '/' . $value['c_pimgepath'];
                $count1++;
            }
        }

        $count = 0;
        if (count($bannerlist) == 0) {
            foreach ($imgs as $key => $value) {
                if ($count >= 3) {
                    break;
                }
                $bannerlist[$count]['img'] = GetHost() . '/' . $value['c_pimgepath'];
                $count++;
            }
        }

        $data['bannerimg'] = $bannerlist;
        $data['imglist'] = $delist;

        $modelwhere['c_pcode'] = $data['c_pcode'];
        $modellist = M('Product_model')->where($modelwhere)->select();
        $data['modellist'] = null;
        if (count($modellist) > 0) {
            foreach ($modellist as $key1 => $value1) {
                $where1['c_pcode'] = $parr['pcode'];
                $where1['c_mcode'] = $value1['c_mcode'];
                $ladderprice = M('Product_ladderprice')->where($where1)->select();
                if ($data['c_rule'] == 14) {
                    $promoteprice = $data['pprice'];
                } else {
                    $promoteprice = bcmul($value1['c_price'],bcdiv($data['c_discount'],10,2),2);
                }
                $modellist[$key1]['c_price'] = $promoteprice;
                $modellist[$key1]['c_num'] = $data['pnum'];
                if (!$ladderprice) {
                    $ladderprice[0]['c_id'] = "0";
                    $ladderprice[0]['c_pcode'] = $value1['c_pcode'];
                    $ladderprice[0]['c_mcode'] = $value1['c_mcode'];
                    $ladderprice[0]['c_minnum'] = 1;
                    $ladderprice[0]['c_maxnum'] = $value1['c_num'];
                    $ladderprice[0]['c_price'] = $promoteprice;
                } else {
                    foreach ($ladderprice as $k => $v) {
                        if ($data['c_rule'] == 14) {
                            $ladpromoteprice = $data['pprice'];
                        } else {
                            $ladpromoteprice = bcmul($v['c_price'],bcdiv($data['c_discount'],10,2),2);
                        }
                        $ladderprice[$k]['c_price'] = $ladpromoteprice;
                    }
                }
                $modellist[$key1]['ladderprice'] = $ladderprice;
            }
            $data['modellist'] = $modellist;
        }

        return MessageInfo(0, '查询成功', $data);
    }

    //拆分商家和产品信息并计算价格
    function splitProduct($product,$ucode)
    {
        //查询出所有产品信息
        $freeprice = 0;
        foreach ($product as $key => $value) {
            $where['a.c_startime'] = array('ELT', date('Y-m-d H:i:s'));
            $where['a.c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
            $where['a.c_pcode'] = $value['pcode'];
            $where['a.c_state'] = 1;
            $where['a.c_delete'] = 0;
            $where['a.c_id'] = $value['pid'];
            $where['b.c_ishow'] = 1;
            $where['b.c_isdele'] = 1;
            $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
            $field = 'b.*,a.c_rule,a.c_price as pprice,a.c_num as pnum,a.c_joinnum,a.c_startime,a.c_penum'
                  .',a.c_endtime,a.c_state,a.c_delete,a.c_createtime,a.c_id as pid,a.c_discount,a.c_totalnum';
            $productinfo = M('Product_promote as a')->join($join)->where($where)->field($field)->find();
            if (count($productinfo) <= 0) {
                return Message(1015, "该活动产品信息不存在");
            }

            if ($value['num'] > $productinfo['pnum']) {
                return Message(1016, "该活动产品已售完");
            }

            $price = $productinfo['pprice'];
            $num = $productinfo['c_num'];
            if (!empty($value['mcode'])) {
                //查询商品价格
                $wheremodel['c_pcode'] = $value['pcode'];
                $wheremodel['c_mcode'] = $value['mcode'];
                $ProductModel = M('Product_model')->where($wheremodel)->find();
                if (count($ProductModel) <= 0) {
                    return Message(1017, "该活动产品已售完");
                } else {
                    $price = $ProductModel['c_price'];
                    $num = $ProductModel['c_num'];
                }
            } else {
                $price = $productinfo['c_price'];
            }

            // 判断产品库存
            if ($num < $value['num']) {
                return Message(1018, "该活动产品已售完");
            }

            //判断是否存在阶梯价格
            $temp2 = $value['num'];
            $wherejieti['c_pcode'] = $productinfo['c_pcode'];
            $wherejieti['c_mcode'] = $value['mcode'];
            $wherejieti['c_minnum'] = array('ELT', $temp2);
            $wherejieti['c_maxnum'] = array('EGT', $temp2);
            $jietiinfo = M('Product_ladderprice')->where($wherejieti)->find();
            if (count($jietiinfo) > 0) {
                $price = $jietiinfo['c_price'];
            }

            if ($productinfo['c_rule'] == 14) {
                $price = $productinfo['pprice'];
            } else {
                $price = bcmul($price,bcdiv($productinfo['c_discount'],10,2),2);
            }

            $info['pcode'] = $productinfo['c_pcode'];
            $info['ucode'] = $ucode;
            $info['price'] = $price;
            $info['pname'] = $productinfo['c_name'];
            $info['pmodel'] = $value['mcode'];
            $info['pmodel_name'] = $ProductModel['c_name'];
            $info['num'] = $value['num'];
            $info['pimg'] = $productinfo['c_pimg'];
            $info['rule'] = $productinfo['c_rule'];
            $info['pid'] = $productinfo['pid'];

            $tempcount = $price * $value['num'];
            $singletotle = sprintf("%.2f", $tempcount);
            $info['singletotle'] = $singletotle;
            if ($info['rule'] == 14 || $info['rule'] == 13) {
                $info['freeprice'] = '0.00';
            } else {
                $info['freeprice'] = $productinfo['c_freeprice'];
            }

            $shop['acode'] = $productinfo['c_ucode'];
            $shop['value'][] = $info;
            $shop['isagent'] = $productinfo['c_isagent'];

            if ($productinfo['c_isfree'] == 2 && $info['rule'] != 14 && $info['rule'] != 13) {
                $freeprice += $productinfo['c_freeprice'] * $value['num'];
            }
            $rule = $productinfo['c_rule'];
        }

        $shop['freeprice'] = $freeprice;
        $shop['rule'] = $rule;
        return MessageInfo(0, "产品查询成功", $shop);
    }

    //生成订单
    function CreataOrder($parr) {
        $produce = objarray_to_array(json_decode($parr['produce']));
        $ucode = $parr['ucode'];
        $delivery = $parr['delivery'];
        $addressid = $parr['addressid'];
        $postscript = $parr['postscript'];
        $money = $parr['money'];
        $couponid = $parr['couponid'];

        $result = $this->splitProduct($produce, $ucode);
        if ($result["code"] != 0) {
            return $result;
        }

        $shop = $result['data'];
        $info = $shop['value'];
        $free = $shop['freeprice'];
        $isagent = $shop['isagent'];
        $acode = $shop['acode'];
        $rule = $shop['rule'];
        if (empty($info)) {
            return Message(1015, "产品信息为空");
        }

        if ($delivery == 2) {
            $free = 0;
        }

        $orderid = CreateOrder("p");
        $db = M('');
        $db->startTrans();


        //生成订单详情
        $result = $this->CreataOrderdetails($info, $orderid, $delivery);
        $totprice = 0;
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $totprice = $result['data']['totprice'];
        $balance = 0;
        $tempcount = $totprice + $free;

        $paystatu = 0;
        if ($money > 0) {
            if ($money >= $tempcount) {
                $paystatu = 1;
                $balance = $tempcount;
                $money = bcsub($money, $balance, 2);
            } else {
                $balance = $money;
                $money = 0;
            }
        }

        //查询平台活动是否存在
        $activitywhere['c_state'] = 1;            //活动开启状态
        $activitywhere['c_activitytype'] = $rule;     //发现活动类型标识
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            $db->rollback(); //不成功，则回滚
            return Message(1018, '此活动已结束！');
        }

        //限制用户每天每个活动参与次数
        // $orderlogwhere['c_rule'] = $rule;
        // $orderlogwhere['c_addtime'] = array('ELT', date('Y-m-d 00:00:00'));
        // $orderlogwhere['c_addtime'] = array('EGT', date('Y-m-d 23:59:59'));
        // $promorderlog = M('Order_details')->where($orderlogwhere)->count();
        // if ($promorderlog > 10) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(1018, '此次活动您已达参与上限！');
        // }


        //生成订单地址
        $result = IGD('Order','Order')->CreataOrderAddress($orderid, $addressid);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        //生成订单
        $result = $this->CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free, $isagent, $delivery, $acode,$rule,$activitydata);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        // 查询推荐人编码
        $tjuserwhere['c_ucode'] = $ucode;
        $tjuserucode = M('Users_tuijian')->where($tjuserwhere)->getField('c_pcode');

        //添加老虎机抽奖机会
        if ($paystatu == 1 && $tempcount > 10 && $acode != $tjuserucode)  {
            //查询用户每天的机会
            $lotterywhere['c_ucode'] = $parr['ucode'];
            $lotterywhere['c_rule'] = 4;
            $lhjihui1 = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');
            $lhjihui1 = isset($lhjihui1)?$lhjihui1:0;
            $lhjoin = 'left join t_activity as b on a.c_aid=b.c_id';
            $lhwhere['a.c_ucode'] = $parr['ucode'];
            $lhwhere['b.c_activitytype'] = 11;
            $lhwhere[] = array("a.c_addtime>='".date('Y-m-d 00:00:00')."' and a.c_addtime<='".date('Y-m-d 23:59:59')."'");
            $lhjihui2 = M('Activity_log as a')->join($lhjoin)->where($lhwhere)->count();
            if (($lhjihui1+$lhjihui2) < 5) {
                $parr['rule'] = 4;
                $result = IGD('Question','Activity')->AddRouletteNum($parr,1);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return $result;
                }
            }
        }

        //提交事务
        $db->commit();

        $Msgcentre = IGD('Msgcentre', 'Message');
        if ($balance > 0) {
            //给用户发送相关消息
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] = '您提交的订单号：' . $orderid . '，抵扣余额￥' . $balance . '成功';
            $msgdata['tag'] = 2;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $Msgcentre->CreateMessege($msgdata);
        }

        if ($paystatu == 1 && $tempcount > 10 && $acode != $tjuserucode) {
            if (($lhjihui1+$lhjihui2) < 5) {
                //给用户发送抽奖机会消息
                $msgdata['ucode'] = $ucode;
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['content'] = '您在平台内完成一笔大于10元的订单，获得一次抽奖机会';
                $msgdata['tag'] = 2;
                $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Promote/lottery';
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Promote/lottery';
                $Msgcentre->CreateMessege($msgdata);
            }
        }

        //给商家发送相关消息
        if ($rule != 14) {
            $msgdata['ucode'] = $acode;
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = '有用户在您的促销活动中下了个单，可及时联系买家哦~，订单未支付前可修改运费。';
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);
        }

        $data['orderid'] = $orderid;
        $data['paystatu'] = $paystatu;
        return MessageInfo(0, "生成订单成功", $data);
    }

    //生成订单详情
    function CreataOrderdetails($product, $orderid, $delivery) {
        $totprice = 0;
        $int = 0;
        foreach ($product as $key => $v1) {
            $singletotle = $v1['singletotle'];
            $totprice+=$singletotle;
            $temp = "dp" . $int;
            $int++;
            $detailid = CreateOrder($temp);

            //查询产品活动
            $produceactwhere['c_pcode'] = $v1['pcode'];
            $produceactwhere['c_id'] = $v1['pid'];
            $produceactwhere['c_startime'] = array('ELT', date('Y-m-d H:i:s'));
            $produceactwhere['c_endtime'] = array('EGT', date('Y-m-d H:i:s'));
            $produceact = M('Product_promote')->where($produceactwhere)->find();
            if (!$produceact) {
                return Message(1013, "该活动产品未开始或已结束");
            }

            //判断用户是否达限购次数
            $joinedjoin = 'left join t_order as b on a.c_orderid=b.c_orderid';
            $orderlogwhere['a.c_pcode'] = $v1['pcode'];
            $orderlogwhere['a.c_promoteid'] = $v1['pid'];
            if ($v1['rule'] == 14) {
                $orderlogwhere['b.c_pay_state'] = 1;
                $orderlogwhere['b.c_pay_rule'] = array('neq',5);
            }
            $orderlogwhere['a.c_ucode'] = $v1['ucode'];
            $promorderlog = M('Order_details as a')->join($joinedjoin)->where($orderlogwhere)->count();
            if ($promorderlog >= $produceact['c_penum']) {
                return Message(1013, "购买该活动产品已达限购次数");
            }

            if ($v1['rule'] != 14) {
                // 减少总库存
                $productinfo['c_pcode'] = $v1['pcode'];
                $productinfo['c_num'] = array('egt', $v1['num']);
                $result = M('Product')->where($productinfo)->setDec('c_num', $v1['num']);
                if (!$result) {
                    return Message(1013, "库存扣除失败");
                }

                //减少活动产品库
                $result = M('Product_promote')->where($productinfo)->setDec('c_num', $v1['num']);
                if (!$result) {
                    return Message(1014, "库存扣除失败");
                }

                if (!empty($v1['pmodel'])) {
                    // 减少型号库存
                    $productinfo['c_mcode'] = $v1['pmodel'];
                    $result = M('Product_model')->where($productinfo)->setDec('c_num', $v1['num']);
                    if (!$result) {
                        return Message(1015, "库存扣除失败");
                    }
                }
                $tempdetails['c_pname'] = $v1['pname'];
            } else {
                $tempdetails['c_pname'] = '【夺宝机会】'.$v1['pname'];
            }

            //写入订单详情表
            if ($delivery == 2) {
                $tempdetails['c_free'] = 0;
            } else {
                $tempdetails['c_free'] = $v1['num'] * $v1['freeprice'];
            }
            $tempdetails['c_orderid'] = $orderid;
            $tempdetails['c_detailid'] = $detailid;
            $tempdetails['c_pcode'] = $v1['pcode'];
            $tempdetails['c_ucode'] = $v1['ucode'];
            $tempdetails['c_pprice'] = $v1['price'];
            $tempdetails['c_pmodel'] = $v1['pmodel'];
            $tempdetails['c_pmodel_name'] = empty($v1['pmodel_name'])?$v1['pname']:$v1['pmodel_name'];
            $tempdetails['c_pnum'] = $v1['num'];
            $tempdetails['c_ptotal'] = $singletotle;
            $tempdetails['c_pimg'] = $v1['pimg'];
            $tempdetails['c_promoteid'] = $v1['pid'];
            $tempdetails['c_rule'] = $v1['rule'];
            $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Order_details')->add($tempdetails);
            if (!$result) {
                return Message(1000, "生成订单详情失败");
            }
        }

        $date['totprice'] = $totprice;
        return MessageInfo(0, "订单详情生成成功", $date);
    }

    //创建订单信息
    function CreataOrderInfo($orderid, $ucode, $totprice, $postscript, $balance, $free, $isagent, $delivery, $acode,$rule,$activitydata) {

        $aorderinfo['c_activity_name'] = $activitydata['c_activityname'];
        $aorderinfo['c_activity_id'] = $activitydata['c_id'];
        $aorderinfo['c_orderid'] = $orderid;
        $aorderinfo['c_ucode'] = $ucode;

        if ($balance > 0) {
            $aorderinfo['c_actual_price'] = $balance;
            //用户功勋操作
            $rebatemoney = D('Money', 'User');
            $parr['ucode'] = $ucode;
            $parr['money'] = $balance;
            $parr['source'] = 1;
            $parr['key'] = $orderid;
            $parr['desc'] = "余额支付";
            $parr['state'] = 1;
            $parr['type'] = 0;
            $parr['isagent'] = 0;
            $parr['showimg'] = 'Uploads/settlementshow/gou.png';
            $parr['showtext'] = '购物';
            $result = $rebatemoney->OptionMoney($parr);

            if ($result['code'] != 0) {
                return $result;
            }
            //用户支付记录操作
            $result = IGD('Order','Order')->paylog($orderid, 4, $balance, "");
            if ($result['code'] != 0) {
                return $result;
            }
        }

        if ($rule == 14) {
            $aorderinfo['c_ishand'] = 0;
            $userremind = '您在【'.$activitydata['c_activityname'].'】中，成功购买一次夺宝机会，等待系统自动开奖。';
        } else {
            $aorderinfo['c_ishand'] = 1;
            $userremind = '您在【'.$activitydata['c_activityname'].'】中，抢购宝贝成功，并生成订单等待卖家发货。';
        }
        $countprice = $totprice + $free;
        if ($balance >= $countprice) {
            if ($rule == 14) {
                $aorderinfo['c_deliverystate'] = 5;
                $aorderinfo['c_deliverytime'] = date('Y-m-d H:i:s');
                $aorderinfo['c_confirmtime'] = date('Y-m-d H:i:s');
            }
            $aorderinfo['c_pay_state'] = 1;
            $aorderinfo['c_paytime'] = date('Y-m-d H:i:s', time());

            //获取订单详情信息
            $wheredetail['c_orderid'] = $orderid;
            $detailslist = M('Order_details')->where($wheredetail)->select();
            $result = IGD('Order','Order')->CheckRebate($detailslist, $ucode, $acode,$activitydata['c_id']);
            if ($result['code'] != 0) {
                return $result;
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = $userremind;
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderid;
            $Msgcentre->CreateMessege($msgdata);

            if ($rule != 14) {
                $msgdata['ucode'] = $acode;
                $msgdata['type'] = 1;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '订单消息';
                $msgdata['content'] = '陛下，在【'.$activitydata['c_activityname'].'】中您有新的订单需要审阅，别让文武百官等太久，祝您万福安康~';
                $msgdata['tag'] = 2;
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
                $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
                $Msgcentre->CreateMessege($msgdata);
            }
        }

        $aorderinfo['c_acode'] = $acode;
        $aorderinfo['c_pay_rule'] = 4;
        $aorderinfo['c_order_state'] = 2;
        $aorderinfo['c_free'] = $free;
        $aorderinfo['c_total_price'] = $totprice;
        $aorderinfo['c_delivery'] = $delivery;
        $aorderinfo['c_postscript'] = $postscript;
        $aorderinfo['c_isagent'] = $isagent;
        $aorderinfo['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Order')->add($aorderinfo);
        if (!$result) {
            return Message(3005, "创建订单失败");
        }

        //群龙夺宝订单开奖处理
        if ($rule == 14) {
            $result = $this->StartSeize($aorderinfo,$activitydata);
            if ($result['code'] != 0) {
                return $result;
            }
        }

        return MessageInfo(0, "订单创建成功",$aorderinfo);
    }

    //群龙夺宝开奖
    function StartSeize($orderinfo,$activitydata)
    {
        //查询订单详情
        $delistwhere['c_orderid'] = $orderinfo['c_orderid'];
        $detaillist = M('Order_details')->where($delistwhere)->find();

        //查询是否已达开奖人数
        $promotewhere['c_id'] = $detaillist['c_promoteid'];
        $promotewhere['c_state'] = 1;
        $promotewhere['c_delete'] = 0;
        $promotedata = M('Product_promote')->where($promotewhere)->find();

        $orderlogwhere['a.c_pcode'] = $detaillist['c_pcode'];
        $orderlogwhere['a.c_promoteid'] = $detaillist['c_promoteid'];
        $orderlogwhere['b.c_ishand'] = 0;
        $orderlogwhere['b.c_pay_state'] = 1;
        $join = 'left join t_order as b on a.c_orderid=b.c_orderid';
        $field = 'a.c_orderid,a.c_ucode';
        $joinedinfo = M('Order_details as a')->join($join)->where($orderlogwhere)
        ->field($field)->order('a.c_addtime asc')->select();
        if (count($joinedinfo) >= $promotedata['c_joinnum']) {
            $randnum = rand(0,($promotedata['c_joinnum']-1));
            for ($i=0; $i < $promotedata['c_joinnum']; $i++) {
                $orderidarr[$i] = $joinedinfo[$i]['c_orderid'];
                $ucodearr[$i] = $joinedinfo[$i]['c_ucode'];
            }

            // 修改订单状态失败
            $orderwhere['c_orderid'] = array('in',implode(',', $orderidarr));
            $ordersave['c_ishand'] = 1;
            $result = M('Order')->where($orderwhere)->save($ordersave);
            if (!$result) {
                return Message(3001,'修改开奖状态失败');
            }

            //创建订单
            $luckyorder = $orderidarr[$randnum];
            $luckyuser = $ucodearr[$randnum];
            $lirunmoney = $promotedata['c_price']*$promotedata['c_joinnum'];
            $result = $this->CreatPromoteOrder($luckyorder,$luckyuser,$detaillist['c_pcode'],$detaillist['c_pmodel'],$lirunmoney);
            if ($result['code'] != 0) {
                return $result;
            }
            $orderid = $result['data'];

            // 减少总库存
            $productinfo['c_pcode'] = $detaillist['c_pcode'];
            $productinfo['c_num'] = array('egt', $detaillist['c_pnum']);
            $result = M('Product')->where($productinfo)->setDec('c_num', $detaillist['c_pnum']);
            if (!$result) {
                return Message(1013, "库存扣除失败");
            }

            //减少活动产品库
            $result = M('Product_promote')->where($productinfo)->setDec('c_num', $detaillist['c_pnum']);
            if (!$result) {
                return Message(1014, "库存扣除失败");
            }

            // 减少型号库存
            $productinfo['c_mcode'] = $detaillist['c_pmodel'];
            $result = M('Product_model')->where($productinfo)->setDec('c_num', $detaillist['c_pnum']);
            if (!$result) {
                return Message(1015, "库存扣除失败");
            }

            $Msgcentre = IGD('Msgcentre', 'Message');

            //给商家发送消息
            $msgdata['ucode'] = $orderinfo['c_acode'];
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = "您发起的【".$activitydata['c_activityname']."】系统自动开奖，已为您生成一笔订单，请尽快给幸运用户发货，用户确认收货您可获得产品利润￥".$lirunmoney;
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Store/detail?orderid=' . $orderid;
            $result = $Msgcentre->CreateMessegeInfo($msgdata);
            if ($result['code'] != 0) {
                return $result;
            }

            $luckname = str_replace('【夺宝机会】', '', $detaillist['c_pname']);
            //给获奖用户推送消息
            $msgdata['ucode'] = $luckyuser;
            $msgdata['type'] = 1;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '订单消息';
            $msgdata['content'] = "恭喜您在【".$activitydata['c_activityname']."】中，夺得宝贝【".$luckname."】，系统已自动生成订单";
            $msgdata['tag'] = 3;
            $msgdata['tagvalue'] = $orderid;
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderid;
            $result = $Msgcentre->CreateMessegeInfo($msgdata);
            if ($result['code'] != 0) {
                return $result;
            }

            //给未中奖的用户推送消息
            for ($j=0; $j < count($ucodearr); $j++) {
                if ($ucodearr[$j] != $luckyuser) {
                    $msgdata['ucode'] = $ucodearr[$j];
                    $msgdata['type'] = 1;
                    $msgdata['platform'] = 1;
                    $msgdata['sendnum'] = 1;
                    $msgdata['title'] = '订单消息';
                    $msgdata['content'] = "很遗憾，在【".$activitydata['c_activityname']."】中差一点就夺得宝贝【".$luckname."】，继续加油！";
                    $msgdata['tag'] = 3;
                    $msgdata['tagvalue'] = $orderidarr[$j];
                    $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Order/detail?orderid=' . $orderidarr[$j];
                    $result = $Msgcentre->CreateMessegeInfo($msgdata);
                    if ($result['code'] != 0) {
                        return $result;
                    }
                }
            }

        }

        return Message(0,'修改成功');
    }

    //一键创建夺宝订单
    function CreatPromoteOrder($temporderid,$ucode,$pcode,$pmodel,$lirunmoney) {
        $orderid = CreateOrder('c');
        $detailid = CreateOrder('dc');
        $addtime = date('Y-m-d H:i:s');

        //查询产品价格
        $producewhere['c_pcode'] = $pcode;
        $price = M('Product')->where($producewhere)->getField('c_price');
        if ($pmodel) {
            $producewhere['c_mcode'] = $pmodel;
            $price1 = M('Product')->where($producewhere)->getField('c_price');
            if ($price1) {
                $price = $price1;
            }
        }

        $db = M('');

        //生成订单信息
        $sql = "insert into t_order (c_orderid,c_ucode,c_acode,c_pay_state,c_order_state,c_deliverystate,"
            . "c_free,c_total_price,c_actual_price,c_pay_rule,c_delivery,c_paytime,c_postscript,"
            . "c_activity_id,c_activity_name,c_ishand,c_addtime) "
            . "select '$orderid',c_ucode,c_acode,'1','2','0',c_free,'$price','0',"
            . "'5',c_delivery,'$addtime',c_postscript,c_activity_id,c_activity_name,'1','$addtime' from t_order "
            . "where c_orderid='$temporderid' and c_ucode='$ucode'";
        $result = $db->execute($sql);
        if (!$result) {
            return Message(3001, "创建用户订单详情失败");
        }

        //生成订单详情
        $sql = "insert into t_order_details (c_detailid,c_orderid,c_ucode,c_pcode,c_pname,c_pprice,c_pmodel,"
            . "c_pmodel_name,c_pnum,c_ptotal,c_pimg,c_free,c_profit,c_promoteid,c_rule,c_addtime) "
            . "select '$detailid','$orderid',c_ucode,c_pcode,REPLACE(c_pname,'【夺宝机会】','') as c_pname,'$price',c_pmodel,"
            . "c_pmodel_name,c_pnum,'$price',c_pimg,c_free,'$lirunmoney',c_promoteid,c_rule,'$addtime' from t_order_details "
            . "where c_orderid='$temporderid' and c_ucode='$ucode'";
        $result = $db->execute($sql);
        if (!$result) {
            return Message(3002, "创建用户订单详情失败");
        }

        //生成订单地址
        $sql = "insert into t_order_address (c_orderid,c_consignee,c_telphone,c_address,c_province,c_cityname,c_district,c_provinceid,c_cityid,c_districtid) "
            . "select '$orderid',c_consignee,c_telphone,c_address,c_province,c_cityname,c_district,c_provinceid,"
            . "c_cityid,c_districtid from t_order_address where c_orderid='$temporderid'";
        $result = $db->execute($sql);
        if (!$result) {
            return Message(3003, "创建用户地址失败");
        }

        return MessageInfo(0, "创建订单成功",$orderid);
    }

    //定时器处理群龙夺宝数据
    function TimerTaskPromote()
    {
        $db = M('');
        $db->startTrans();

        $protewhere['c_rule'] = 1;
        $protewhere['c_endtime'] = array('ELT',date('Y-m-d H:i:s'));
        $promotelist = M('Product_promote')->where($protewhere)->select();
        foreach ($promotelist as $key => $value) {
            $orderlogwhere['a.c_pcode'] = $value['c_pcode'];
            $orderlogwhere['a.c_promoteid'] = $value['c_id'];
            $orderlogwhere['b.c_ishand'] = 0;
            $join = 'left join t_order as b on a.c_orderid=b.c_orderid';
            $field = 'a.c_orderid,a.c_ucode,a.c_pname,b.c_total_price';
            $joinedinfo = M('Order_details as a')->join($join)->where($orderlogwhere)
            ->field($field)->order('a.c_addtime asc')->select();
            foreach ($joinedinfo as $k => $v) {
                // 修改订单开奖状态
                $orderwhere['c_orderid'] = $v['c_orderid'];
                $ordersave['c_ishand'] = 1;
                $result = M('Order')->where($orderwhere)->save($ordersave);
                if (!$result) {
                    $db->rollback();
                    return Message(3001,'修改开奖状态失败');
                }

                //返回用户金额
                $pname = str_replace('【夺宝机会】', '', $v['c_pname']);
                $beizhu = '您参与的【'.$pname.'】夺宝活动结束，未达开奖人数，系统自动退款';
                $result = IGD('Order','Order')->faliRebate($v['c_ucode'],$v['c_total_price'],3,$v['c_activity_name'], $beizhu, 0);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }

                //给用户推送消息
                $Msgcentre = IGD('Msgcentre', 'Message');
                $msgdata['ucode'] = $v['c_ucode'];
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['tag'] = 2;
                $msgdata['content'] = '您参与的【'.$pname.'】夺宝活动结束，未达开奖人数，系统已自动退款，退款金额：￥'.$v['c_total_price'].'，已存入余额。';
                $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
                $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
                $result = $Msgcentre->CreateMessegeInfo($msgdata);
                if ($result['code'] != 0) {
                    $db->rollback(); //不成功，则回滚
                    return $result;
                }
            }
        }

        $db->commit();
        return Message(0,'操作成功');
    }

}
