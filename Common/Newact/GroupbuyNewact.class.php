<?php
/**
 * 商家活动 拼团活动（需设置定时器实时监控活动状态）
 */
class GroupbuyNewact {

	/**
	* 商家拼团管理
	*/

	/**
	 *  创建拼团产品 选择产品
	 *  @param ucode,pagesize,pageindex
	 */
	public function ProductList($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        //查询活动id
        $result = IGD('Index','Newact')->GetPlathavingAct(26);
        $aid = $result['data']['c_id'];

        //查询是否有产品正在拼团
        $actpw['c_aid'] = $aid;
        $actpw['c_ucode'] = $parr['ucode'];
        $actpw['c_state'] = array('neq',2);
        $actpw['c_isdel'] = 2;
        
        $pcode_arr = M('Shopact_product')->where($actpw)->field('c_pcode')->select();

        $pcode_str = arr_to_str($pcode_arr);
        if (!empty($pcode_str)) {
        	$w['c_pcode'] = array('not in',$pcode_str);
        }
		$w['c_ucode'] = $parr['ucode'];
		$w['c_isagent'] = 0;
		$w['c_isdele'] = 1;
		$w['c_ishow'] = 1;

		$field = "c_pcode,c_name,c_pimg,c_price,c_num";

		$list = M('Product')->field($field)->where($w)->limit($countPage, $pageSize)->order('c_id desc')->select();

		$count = M('Product')->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 *  创建拼团产品 选择产品型号
	 *  @param pcode
	 */
	public function ProductModel($parr){
		$mw['c_pcode'] = $parr['pcode'];

		$result = M('Product_model')->where($mw)->select();
		if (!$result) {
			$data = M('Product')->where($mw)->find();
			//虚构一个型号
			$result[0]['c_id'] = 1;
			$result[0]['c_mcode'] = 'xn'.$data['c_pcode'];
			$result[0]['c_pcode'] = $data['c_pcode'];
			$result[0]['c_name'] = $data['c_name'];
			$result[0]['c_price'] = $data['c_price'];
			$result[0]['c_num'] = $data['c_num'];
		}

		return MessageInfo(0,"查询成功",$result);
	}

	/**
	 *  创建拼团产品
	 *  @param ucode,pcode,mcode,actprice,targetnum,usernum,totalnum,starttime,endtime,types
	 */
	public function CreateProduct($parr){
		//查询活动信息
		$result = IGD('Index','Newact')->GetPlathavingAct(26);
		if($result['code'] != 0){
			return Message(2000, "活动不存在或者未开启");
		}
		$aid = $result['data']['c_id'];

		//查询商品信息
		$pw['c_pcode'] = $parr['pcode'];
		$productinfo = M('Product')->field('c_name,c_pimg,c_price,c_num')->where($pw)->find();
		if(!$productinfo){
			return Message(1001,"产品信息不存在");
		}

		if ($parr['actprice'] >= $productinfo['c_price']  || $parr['actprice'] <= 0) {
			return Message(1001,"活动价不能为0或高于产品原价");
		}

		$totalnum = $parr['totalnum'];
		if($productinfo['c_num'] < $totalnum){
			return Message(1002,"活动库存不能大于产品总库存");
		}

		if (strtotime($parr['endtime'].':00') <= strtotime('+3 days',strtotime($parr['starttime'].':00'))) {
			return Message(1003,"结束时间必须大于开始时间3天");
		}

		//添加产品
		$add['c_act_pcode'] = CreateUcode('actp');
		$add['c_aid'] = $aid;
		$add['c_ucode'] = $parr['ucode'];
		$add['c_pcode'] = $parr['pcode'];
		$add['c_mcode'] = $parr['mcode'];
		$add['c_imgpath'] = $productinfo['c_pimg'];
		$add['c_name'] = $productinfo['c_name'];
		$add['c_value'] = $productinfo['c_price'];
		$add['c_actprice'] = $parr['actprice'];
		$add['c_totalnum'] = $parr['totalnum'];
		$add['c_num'] = $parr['totalnum'];
		$add['c_usernum'] = $parr['usernum'];
		$add['c_targetnum'] = $parr['targetnum'];
		$add['c_starttime'] = $parr['starttime'];
		$add['c_endtime'] = $parr['endtime'];
		$add['c_types']   = $parr['types'];
		$add['c_addtime'] = gdtime();

		$db = M('');
		$db -> startTrans();

		$result = M('Shopact_product')->add($add);
		if(!$result){
			$db->rollback();
			return Message(1003,'添加失败');
		}

		//扣除产品库存
		$result = M('Product')->where($pw)->setDec('c_num',$totalnum);
		if(!$result){
			$db->rollback();
			return Message(1004,'扣除商品库存失败');
		}

		//添加商家活动记录
        $shopact_log['c_aid'] 		= $aid;
        $shopact_log['c_ucode'] 	= $parr['ucode'];
        $shopact_log['c_act_pcode'] = $add['c_act_pcode'];
        $shopact_log['c_pname'] 	= $productinfo['c_name'];
        $shopact_log['c_acttype'] 	= 1;
        $shopact_log['c_starttime'] = $parr['starttime'];
        $shopact_log['c_endtime'] 	= $parr['endtime'];
        $shopact_log['c_weburl']  	= GetHost(1)."/index.php/Shopping/Collage/pdetail?act_pcode=".$add['c_act_pcode'];
        $shopact_log['c_addtime'] 	= gdtime();
        $result = M('Circle_shopact')->add($shopact_log);
        if (!$result) {
        	$db->rollback();
            return Message(3003,'添加商圈活动记录失败');
        }

		$db->commit();
		return Message(0,"添加成功");
	}

	/**
	 * 商家拼团商品管理
	 * @param pageindex,ucode,useraction(0-正在进行，1-未开始，2-已结束，3-全部)
	 */
	public function MyProductList($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$result = IGD('Index','Newact')->GetPlathavingAct(26);		
		$aid = $result['data']['c_id'];

		$w['c_aid'] = $aid;
		$w['c_ucode'] = $parr['ucode'];
		$w['c_isdel'] = 2;

		if($parr['useraction'] == 1){
			$w['c_state'] = 0;
		}else if($parr['useraction'] == 2){
			$w['c_state'] = 2;
		}else if ($parr['useraction'] == 3) {

		}else {
			$w['c_state'] = 1;
		}

		$list = M('Shopact_product')->where($w)->order("c_endtime desc")->limit($countPage, $pageSize)->select();

		$count = M('Shopact_product')->where($w)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, "查询成功", $data);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];

			$list[$key]['c_starttime'] = date('y/m/d H:i',strtotime($value['c_starttime']));
			$list[$key]['c_endtime'] = date('y/m/d H:i',strtotime($value['c_endtime']));
			$list[$key]['stale'] = 0;
			if ($value['c_num'] <= 0) {
				$list[$key]['stale'] = 1;
			}

			// if($value['c_state'] == 0){
				$list[$key]['sharetit'] = $value['c_name'];;
				$list[$key]['shareurl'] = GetHost(1).'/index.php/Shopping/Collage/pdetail?act_pcode='.$value['c_act_pcode'];
				$list[$key]['shareimg'] = GetHost().'/'.$value['c_imgpath'];
				$list[$key]['sharedesc'] = "在小蜜，一起拼团买好货，实惠划算。";
			// }
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 删除开团的商品
	 * @param ucode,act_pcode,useraction(1-删除未开始团，2-删除已结束团)
	 */
	public function DelMyProduct($parr){
		$result = IGD('Index','Newact')->GetPlathavingAct(26);
		$aid = $result['data']['c_id'];
		if($parr['useraction'] == 1){
			$w['c_ucode'] = $parr['ucode'];
			$w['c_act_pcode'] = $parr['act_pcode'];
        	$w['c_state'] = 0;
        	$w['c_aid'] = $aid;

        	$pinfo = M('Shopact_product')->where($w)->find();

        	if(!$pinfo){
        		return Message(1001,"没有查询到相关商品信息！");
        	}

        	$db = M('');
        	$db -> startTrans();

        	//删除活动商品
        	$save_data['c_isdel'] = 1;
        	$result = M('Shopact_product')->where($w)->save($save_data);
        	if(!$result){
        		$db->rollback();
        		return Message(1002,"删除活动商品失败");
        	}

        	//返回商品库存
        	$pw['c_ucode'] = $parr['ucode'];
        	$pw['c_pcode'] = $pinfo['c_pcode'];
        	$result = M('Product')->where($pw)->setInc('c_num',$pinfo['c_totalnum']);
        	if(!$result){
        		$db->rollback();
        		return Message(1003,"返回商品库存失败");
        	}

        	$db->commit();
		} else if ($parr['useraction'] == 2) {
			$w['c_ucode'] = $parr['ucode'];
			$w['c_act_pcode'] = $parr['act_pcode'];
        	$w['c_state'] = 2;
        	$w['c_aid'] = $aid;

        	$pinfo = M('Shopact_product')->where($w)->find();

        	if(!$pinfo){
        		return Message(1001,"没有查询到相关商品信息！");
        	}

        	//删除活动商品
        	$save_data['c_isdel'] = 1;
        	$result = M('Shopact_product')->where($w)->save($save_data);

        	if(!$result){
        		return Message(1002,"删除活动商品失败");
        	}
		} else {
			return Message(3000,"正在活动中不能删除");
		}

		return Message(0,"删除成功");
	}

	/**
	 * 拼团团记录
	 * @param pageindex,act_pcode,logtype(0-待成团，1-已成团，2-拼团失败)
	 */
	public function MyProductGroup($parr){
		//查询商品信息
		$pw['c_act_pcode'] = $parr['act_pcode'];
		$pinfo = M('Shopact_product')->where($pw)->find();

		if(!$pinfo){
			return Message(1001,"没有查询到相关商品信息！");
		}

		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$logtype = $parr['logtype'];

		$w['a.c_act_pcode'] = $parr['act_pcode'];
		// $w['a.c_isfound'] = 1;
		$w['a.c_pay_state'] = 1;

		if($logtype == 0){
			$w['a.c_state'] = 0;
		}else if($logtype == 1){
			$w['a.c_state'] = 1;
		}else{
			$w['a.c_state'] = 2;
		}			

		$field = "a.*,u.c_nickname as groupname, u.c_headimg";
		$join = "left join t_users as u on u.c_ucode=a.c_ucode";
		$order = "a.c_havenum desc";

		$list = M('Shopact_log as a')->field($field)->where($w)->join($join)->order($order)->limit($countPage, $pageSize)->select();

		$count = M('Shopact_log as a')->where($w)->join($join)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, "查询成功", $data);
		}

		//统计订单金额，订单数
		$money = 0.00;
		$num = 0;
		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];

			//成团差人数
			$list[$key]['difnum'] = $value['c_targetnum'] - $value['c_havenum'];

			//转换时间
			$list[$key]['c_addtime'] = date('Y-m-d H:i',strtotime($value['c_addtime']));
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
	    return MessageInfo(0, '查询成功', $data);
	}

	//查询统计数目
	//act_pcode,logtype(0-待成团，1-已成团，2-拼团失败)
	// function TongJiNum($parr)
	// {
	// 	$w['c_act_pcode'] = $parr['act_pcode'];
	// 	$w['c_state'] = $parr['logtype'];
	// 	$field = 'c_id,c_groupcode,sum(c_actual_price) as money';
	// 	$data = M('Shopact_log')->field($field)->where($w)->select();
	// 	$data['money'] = $money;
	// 	$data['num'] = $num;
	// 	return MessageInfo(0, '查询成功', $data);
	// }

	/**
	* 店铺拼团	
	*/

	/**
	 * 店铺开团商品列表
	 * @param pageindex,acode
	 */
	public function ShopProductList($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		if (!empty($parr['acode'])) {
			$w['a.c_ucode'] = $parr['acode'];
		} else {
			$result = IGD('Index','Newact')->GetPlathavingAct(26);
			$aid = $result['data']['c_id'];
			if(!$aid){
				$list = array();
				$data = Page(1, 0, 0, $list);
				return MessageInfo(0, "查询成功", $data);
			}
			$w['a.c_aid'] = $aid;
		}
        
        $w['a.c_state'] = array('neq',2);
        $w['a.c_isdel'] = 2;
        $w['a.c_num'] = array('GT',0);

        $w['c.c_ishow'] = 1;
        $w['c.c_isdele'] = 1;
        $join = 'left join t_activity as b on a.c_aid=b.c_id left join t_product as c on a.c_pcode=c.c_pcode';
        $field = 'a.*,b.c_activitytype';
        $list = M('Shopact_product as a')->join($join)->where($w)->order('a.c_id desc')->limit($countPage, $pageSize)->field($field)->select();

        $count = M('Shopact_product as a')->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
        	$list = array();
        	$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
			$acttype = $value['c_activitytype'];
			if ($acttype == 26) { //秒杀
				$list[$key]['jumpurl'] = GetHost(1) . '/index.php/Shopping/Collage/pdetail?act_pcode='.$value['c_act_pcode'];
			} else if ($acttype == 27) {  //砍价
				$list[$key]['jumpurl'] = GetHost(1) . '/index.php/Shopping/Bargain/pdetail?act_pcode='.$value['c_act_pcode'];
			} else if ($acttype == 28) {  //秒杀
				$list[$key]['jumpurl'] = GetHost(1) . '/index.php/Shopping/Seckill/pdetail?act_pcode='.$value['c_act_pcode'];
			}
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 开团商品详情 推荐可参团数据
	 * @param act_pcode
	 */
	public function Tjgroup($parr){
		$w['a.c_act_pcode'] = $parr['act_pcode'];
		$w['a.c_isfound'] = 1;
		$w['a.c_state'] = 0;
		$w['a.c_pay_state'] = 1;
		$w['a.c_ucode'] = array('neq',$parr['ucode']);
		$w['a.c_endtime'] = array('GT',gdtime());

		$field = "a.*,u.c_nickname as groupname, u.c_headimg";
		$join = "left join t_users as u on u.c_ucode=a.c_ucode";
		$order = "a.c_havenum desc";

		$list = M('Shopact_log as a')->field($field)->where($w)->join($join)->order($order)->limit(1)->select();

		if (!$list) {
			$list = array();
		    return MessageInfo(0, "查询成功",$list);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];

			//成团差人数
			$list[$key]['difnum'] = $value['c_targetnum'] - $value['c_havenum'];

			//距离结束时间
			$list[$key]['diftime'] = date('H:i:s',strtotime($value['c_endtime'])-time());
			$list[$key]['stattime'] = strtotime($value['c_starttime'])-time();
			$list[$key]['endtime'] = strtotime($value['c_endtime'])-time();
		}

        return MessageInfo(0, '查询成功', $list[0]);
	}

	/**
	 * 开团商品详情 可参团列表
	 * @param act_pcode,pageindex,ucode
	 */
	public function TjgroupList($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$w['a.c_act_pcode'] = $parr['act_pcode'];
		$w['a.c_isfound'] = 1;
		$w['a.c_state'] = 0;
		$w['a.c_pay_state'] = 1;
		$w['a.c_ucode'] = array('neq',$parr['ucode']);
		$w['a.c_endtime'] = array('GT',gdtime());

		$field = "a.*,u.c_nickname as groupname, u.c_headimg";
		$join = "left join t_users as u on u.c_ucode=a.c_ucode";
		$order = "a.c_havenum desc";

		$list = M('Shopact_log as a')->field($field)->where($w)->join($join)->order($order)->limit($countPage, $pageSize)->select();

		$count = M('Shopact_log as a')->where($w)->join($join)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, "查询成功", $data);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];

			//成团差人数
			$list[$key]['difnum'] = $value['c_targetnum'] - $value['c_havenum'];

			//距离结束时间
			$list[$key]['diftime'] = date('H:i:s',strtotime($value['c_endtime'])-time());
			$list[$key]['stattime'] = strtotime($value['c_starttime'])-time();
			$list[$key]['endtime'] = strtotime($value['c_endtime'])-time();
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 拼团商品详情
	 * @param act_pcode
	 */
	public function ProductInfo($parr){
		$w['c_act_pcode'] = $parr['act_pcode'];

		$productinfo = M('Shopact_product')->where($w)->find();

		if(!$productinfo){
			return Message(1001,"没有查询到相关商品信息！");
		}

		$productinfo['c_imgpath'] = GetHost().'/'.$productinfo['c_imgpath'];

		$productinfo['save_money'] = sprintf('%.2f',$productinfo['c_value'] - $productinfo['c_actprice']);

		return MessageInfo(0,"查询成功",$productinfo);
	}

	/**
	 * 开团团详情
	 * @param groupcode
	 */
	public function GroupInfo($parr){
		$w['a.c_groupcode'] = $parr['groupcode'];
		$w['a.c_pay_state'] = 1;

		$field = "a.*,u.c_headimg";
		$join = "left join t_users as u on a.c_ucode=u.c_ucode";

		$list = M('Shopact_log as a')->field($field)->join($join)->where($w)->order('c_isfound desc')->select();

		if(!$list){
			return Message(1001,"没有查询到相关拼团信息！");
		}

		$data = array();
		//成团差人数
		$data['difnum'] = $list[0]['c_targetnum'] - count($list);
		$data['c_starttime'] = $list[0]['c_starttime'];
		//距离结束时间
		$data['stattime'] = strtotime($list[0]['c_starttime'])-time();
		$data['endtime'] = strtotime($list[0]['c_endtime'])-time();

		$data['memeber_num'] = count($list);
		$data['state'] = $list[0]['c_state'];
		$data['pay_state'] = $list[0]['c_pay_state'];
		$data['delivery'] = $list[0]['c_delivery'];
		$memeber_list = array();
		foreach ($list as $key => $value) {
			$memeber_list[$key]['c_ucode'] = $value['c_ucode'];
			$memeber_list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
		}

		$data['memeber_list'] = $memeber_list;

		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 参团 选择型号页面
	 * @param act_pcode
	 */
	public function ProductNature($parr){
		$w['c_act_pcode'] = $parr['act_pcode'];
		$pinfo = M('Shopact_product')->where($w)->find();

		if(!$pinfo){
			return Message(1001,"没有查询到相关商品信息！");
		}

		//型号信息
		if($pinfo['c_mcode']){
			$mcode_arr = explode('|',$pinfo['c_mcode']);

			foreach ($mcode_arr as $key => $value) {
				$mw['c_pcode'] = $pinfo['c_pcode'];
				$mw['c_mcode'] = $value['c_mcode'];

				$minfo = M('Product_model')->field('c_name,c_num')->where($mw)->find();

				$mcode_arr[$key]['model_num'] = $minfo['c_num'];
				$mcode_arr[$key]['model_name'] = $minfo['c_name'];
			}

			$pinfo['mcode'] = $mcode_arr;
		}

		$pinfo['c_imgpath'] = GetHost().'/'.$pinfo['c_imgpath'];

		return MessageInfo(0,"查询成功",$pinfo);
	}

	//拆分商家和产品信息并计算价格
    public function splitProduct($product, $ucode,$groupcode) {

    	//该团购信息
    	if (!empty($groupcode)) {
    		$gw['c_groupcode'] = $groupcode;
			$ginfo = M('Shopact_log')->where($gw)->find();
			if ($ginfo['c_state'] == 1 || $ginfo['c_state'] == 2) {
				return Message(1015, "该团购已经完成");
			}	
    	}
        //查询出所有产品信息
        $shop = array();
        $freeprice = 0;
        //进行拆分订单
        foreach ($product as $key => $value) {
        	//查询产品信息
            $whereinfo['c_pcode'] = $value['pcode'];
            $whereinfo['c_ishow'] = 1;
            $whereinfo['c_isdele'] = 1;
            $productinfo = M('Product')->where($whereinfo)->find();
            if (count($productinfo) <= 0) {
                return Message(1015, "产品信息不存在，不能生成订单");
            }

            //查询活动产品信息
            $w['c_act_pcode'] = $value['actpcode'];
            $w['c_isdel'] = 2;
            $w['c_state'] = array('neq',2);
			$pinfo = M('Shopact_product')->where($w)->find();
			if (!$pinfo) {
				return Message(1016, "该活动已结束");
			}
            $price = $pinfo['c_actprice'];
            $num = $pinfo['c_num'];  

            if (!empty($value['mcode']) && strpos($value['mcode'], 'xn') === false) {
                //查询商品价格
                $wheremodel['c_pcode'] = $value['pcode'];
                $wheremodel['c_mcode'] = $value['mcode'];
                $ProductModel = M('Product_model')->where($wheremodel)->find();
                if (count($ProductModel) <= 0) {
                    return Message(1015, "没有查询到该产品型号");
                } else {
                    $num = $ProductModel['c_num'];
                }
                $pmodel = $ProductModel['c_mcode'];
            	$pmodel_name = $ProductModel['c_name'];
            } else {
            	$pmodel = '';
            	$pmodel_name = $productinfo['c_name'];
            }                     

            if (!$groupcode) {
            	// 判断产品库存
	            if ($num < $value['num']) {
	                return Message(1015, "产品型号库存不足");
	            }
            }
            
            $info = array();
            $info['pcode'] = $productinfo['c_pcode'];
            $info['ucode'] = $ucode;
            $info['price'] = $price;
            $info['pname'] = $productinfo['c_name'];
            $info['pmodel'] = $pmodel;
            $info['pmodel_name'] = $pmodel_name;
            $info['num'] = $value['num'];
            $info['pimg'] = $productinfo['c_pimg'];
            $info['pucode'] = $value['pucode'];
            $info['isagent'] = $productinfo['c_isagent'];
            $info['types'] = $pinfo['c_types'];
            $info['agent_pcode'] = $productinfo['c_agent_pcode'];

            $tempcount = $price * $value['num'];
            $singletotle = sprintf("%.2f", $tempcount);
            $info['singletotle'] = $singletotle;
            
            $info['freeprice'] = $productinfo['c_freeprice'];
            $shop['acode'] = $productinfo['c_ucode'];
            $shop['value'][] = $info;
            $shop['isagent'] = $productinfo['c_isagent'];
            if ($productinfo['c_isfree'] == 2) {
                $freeprice+=$productinfo['c_freeprice'] * $value['num'];
            }
        }
		//查询商家位置信息
        $acdw['c_ucode'] = $shop['acode'];
        $acodeinfo = M('Store')->where($acdw)->find();
        $phone = M('Users')->where($acdw)->getField('c_phone');
        $acodeinfo['c_phone'] = $phone;
        $shop['freeprice'] = $freeprice;
        $shop['acodeinfo'] = $acodeinfo;
        return MessageInfo(0, "产品拆分成功", $shop);
    }

	/**
	 *  用户提交拼团订单接口
	 *  @param  ucode,act_pcode,mcode,delivery,freeprice,addressid,money,pnum,groupcode(参团时必填参数),postscript
	 *
	 */
	public function CreataActOrder($parr) {
		$w['c_act_pcode'] = $parr['act_pcode'];
		$pinfo = M('Shopact_product')->where($w)->find();

		if(!$pinfo){
			return Message(1011,"没有查询到相关商品信息！");
		}

		//开团时扣除整个团的库存
		if(empty($parr['groupcode'])){
			//判断库存
			if($pinfo['c_targetnum'] > $pinfo['c_num']){
				return Message(1012, "活动产品库存不足".$pinfo['c_targetnum']."人团!");
			}

			$groupcode = CreateUcode("g");
			$isfound = 1;
			$havenum = 0;

			$starttime = gdtime();
			$endtime = date('Y-m-d H:i:s',strtotime('+1 day'));
		}else{
			$groupcode = $parr['groupcode'];
			$isfound = 0;

			//该团购信息
			$gw['c_groupcode'] = $groupcode;
			$gw['c_isfound'] = 1;

			$ginfo = M('Shopact_log')->where($gw)->find();

			$havenum = $ginfo['c_havenum'];
			if($havenum >= $ginfo['c_targetnum']){
				return Message(1014, "该团已满!");
			}

			$starttime = $ginfo['c_starttime'];
			$endtime = $ginfo['c_endtime'];
		}

		//判断商品型号信息
		$model_name = $pinfo['c_name'];
		if(!empty($parr['mcode']) && strpos($parr['mcode'], 'xn') === false){
			$wheremodel['c_pcode'] = $pinfo['c_pcode'];
			$wheremodel['c_mcode'] = $parr['mcode'];
			$ProductModel = M('Product_model')->where($wheremodel)->find();

			if (count($ProductModel) <= 0) {
			    return Message(1013, "没有查询到该产品型号!");
			}

			//判断商品型号库存
			if($parr['pnum'] > $ProductModel['c_num']){
				return Message(1014, "活动产品型号库存不足!");
			}
			$model_name = $ProductModel['c_name'];
		}

		$result = IGD('Index','Newact')->GetPlathavingAct(26);
		$aid = $result['data']['c_id'];
		if (!$aid) {
			return Message(1008,'活动不存在');
		}

	    $db = M('');
	    $db->startTrans();

	    //生成临时的活动订单
	    $orderid = CreateOrder('g');

	    $add['c_aid'] = $aid;
	    $add['c_ucode'] = $parr['ucode'];
	    $add['c_acode'] = $pinfo['c_ucode'];
	    $add['c_act_pcode'] = $pinfo['c_act_pcode'];
	    $add['c_groupcode'] = $groupcode;
	    $add['c_pcode'] = $pinfo['c_pcode'];
	    $add['c_pname'] = $pinfo['c_name'];
	    $add['c_mcode'] = $parr['mcode'];
	    $add['c_model_name'] = $model_name;
	    $add['c_imgpath'] = $pinfo['c_imgpath'];
	    $add['c_value'] = $pinfo['c_actprice'];
	    $add['c_pnum'] = $parr['pnum'];
	    $add['c_orderid'] = $orderid;
	    $add['c_total_price'] = $pinfo['c_actprice']*$parr['pnum'];
	    $add['c_addressid'] = $parr['addressid'];
	    $add['c_delivery'] = $parr['delivery'];
	    $add['c_postscript'] = $parr['postscript'];
	    if ($add['c_delivery'] == 1) {
	    	$add['c_free'] = $parr['freeprice'];
	    }
	    $add['c_isfound'] = $isfound;
	    $add['c_targetnum'] = $pinfo['c_targetnum'];
	    $add['c_havenum'] = $havenum;
	    $add['c_starttime'] = $starttime;
	    $add['c_endtime'] = $endtime;
	    $add['c_addtime'] = gdtime();
	    
	    $result = M('Shopact_log')->add($add);

	    if(!$result){
	    	$db->rollback();
	    	return Message(1015,"创建订单失败");
	    }
	    if ($parr['delivery'] == 2) {
		    $tihuo['c_tcode'] = time();
		    $tihuo['c_orderid'] = $orderid;
		    $tihuo['c_addtime']  = gdtime();
		    $tihuo  = M('Tihuo_log')->add($tihuo);
		    if(!$tihuo){
		    	$db->rollback();
		    	return Message(1015,"创建订单失败");
	  		 }
	    }
	  

	    //扣除活动库存
	    // if(empty($parr['groupcode'])){
	    	$result = M('Shopact_product')->where($w)->setDec('c_num',1);
	    	if(!$result){
		    	$db->rollback();
		    	return Message(1016,"扣除活动库存失败");
		    }
		// }

		//扣除型号库存
		if(!empty($parr['mcode']) && strpos($parr['mcode'], 'xn') === false){
			$wheremodel['c_num'] = array('EGT',$parr['pnum']);
			$result = M('Product_model')->where($wheremodel)->setDec('c_num',$parr['pnum']);
			if (!$result) {
			    $db->rollback();
		    	return Message(1016,"扣除型号库存失败");
			}
		}

		if ($add['c_delivery'] == 3) {
			//生成提货码	   
			$tcode = rand(1,9).time().rand(0,9);
			$tiadd['c_orderid'] = $orderid;
			$tiadd['c_tcode'] = $tcode;
			$tiadd['c_addtime'] = gdtime();
			$result = M('Tihuo_log')->add($tiadd);
			if (!$result) {
				$db->rollback();
			    return Message(1017,"生成提货码失败");
			}
		}
		
	    //提交事务
	    $db->commit();

	    $data['orderid'] = $orderid;
	    return MessageInfo(0, "生成订单成功", $data);
	}	

	//构造订单信息作为支付使用
    public function GetPayorderinfo($parr) {
    	$db = M('');
        $db->startTrans(); /* 开启事务 */

        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $orderid = $parr['orderid'];

        if (!empty($ucode)) {
            $whereinfo['c_ucode'] = $ucode;
        }

        if (!empty($acode)) {
            $whereinfo['c_acode'] = $acode;
        }

        $whereinfo['c_orderid'] = $orderid;
        $orderinfo = M('Shopact_log')->where($whereinfo)->find();
        if (count($orderinfo) <= 0) {
            return Message(0, "该订单信息不存在");
        }

        //查询余额抵扣
        $where['c_orderid'] = $orderid;
        $where['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($where)->sum('c_money');
        $orderinfo['banlace'] = ($banlacemoney<=0)?'0.00':$banlacemoney;


        //获取可抵扣优惠券数量
        $result = IGD('Coupon','Newact')->GetUseCouponNum($parr);
        $orderinfo['couponnum'] = $result['data'];

        $detail[0]['c_pimg'] = GetHost().'/'.$orderinfo['c_imgpath'];
        $detail[0]['c_pname'] = $orderinfo['c_pname'];
        $detail[0]['c_pmodel_name'] = $orderinfo['c_model_name'];
        $detail[0]['c_pprice'] = $orderinfo['c_total_price']/$orderinfo['c_pnum'];
        $detail[0]['c_pnum'] = $orderinfo['c_pnum'];

        //随机获取线上系统商户号
        $mchidarr = explode(',', C('LINEMICH'));
        $mch_id = $mchidarr[rand(0,(count($mchidarr)-1))];        
        
        //查询商家位置信息
        $acdw['c_ucode'] = $orderinfo['c_acode'];
        $acodeinfo = M('Store')->where($acdw)->find();
        $phone = M('Users')->where($acdw)->getField('c_phone');
        $acodeinfo['c_phone'] = $phone;

        //查询提货码信息
        $thw['c_orderid'] = $orderinfo['c_orderid'];
        $thinfo = M('Tihuo_log')->where($thw)->find();

        $orderinfo['detail'] = $detail;
        $orderinfo['mch_id'] = $mch_id;
        $orderinfo['acodeinfo'] = $acodeinfo;
        $orderinfo['thinfo'] = $thinfo;

        $db->commit();
        return MessageInfo(0, "订单查询成功", $orderinfo);
    }

    //构造订单信息作为支付使用
    public function AchievePayorderInfo($parr) {

        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $orderid = $parr['orderid'];

        if (!empty($ucode)) {
            $whereinfo['c_ucode'] = $ucode;
        }

        if (!empty($acode)) {
            $whereinfo['c_acode'] = $acode;
        }

        $whereinfo['c_orderid'] = $orderid;
        $orderinfo = M('Shopact_log')->where($whereinfo)->find();
        if (count($orderinfo) <= 0) {
            return Message(0, "该订单信息不存在");
        }

        //查询余额抵扣
        $where['c_orderid'] = $orderid;
        $where['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($where)->sum('c_money');
        $orderinfo['banlace'] = ($banlacemoney<=0)?'0.00':$banlacemoney;
        $orderinfo['pturl'] = GetHost(1).'/index.php/Home/Myacts/detail?act_pcode='.$orderinfo['c_act_pcode'].'&groupcode='.$orderinfo['c_groupcode'].'&orderid='.$orderinfo['c_orderid'];

        //查询订单详情信息
        $detail[0]['c_pimg'] = GetHost().'/'.$orderinfo['c_imgpath'];
        $detail[0]['c_pname'] = $orderinfo['c_pname'];
        $detail[0]['c_pmodel_name'] = $orderinfo['c_model_name'];
        $detail[0]['c_pprice'] = $orderinfo['c_total_price']/$orderinfo['c_pnum'];
        $detail[0]['c_pnum'] = $orderinfo['c_pnum'];
        $orderinfo['detail'] = $detail;

        //查询订单地址信息
        $addressinfo = M('Users_address')->where(array('c_id'=>$orderinfo['c_addressid']))->find();
        $address['c_consignee'] = $addressinfo['c_consignee'];
        $address['c_telphone'] = $addressinfo['c_mobile'];
        $address['c_province'] = $addressinfo['c_provincename'];
        $address['c_provinceid'] = $addressinfo['c_province'];
        $address['c_cityname'] = $addressinfo['c_cityname'];
        $address['c_cityid'] = $addressinfo['c_city'];
        $address['c_district'] = $addressinfo['c_districtname'];
        $address['c_districtid'] = $addressinfo['c_district'];
        $address['c_address'] = $addressinfo['c_address'];
        $orderinfo['address'] = $address;

        //查询商家位置信息
        $acdw['c_ucode'] = $orderinfo['c_acode'];
        $acodeinfo = M('Store')->where($acdw)->find();
        $phone = M('Users')->where($acdw)->getField('c_phone');
        $acodeinfo['c_phone'] = $phone;
        $orderinfo['acodeinfo'] = $acodeinfo;

        //查询提货码信息
        $thw['c_orderid'] = $orderinfo['c_orderid'];
        $thinfo = M('Tihuo_log')->where($thw)->find();
        $orderinfo['thinfo'] = $thinfo;
        return MessageInfo(0, "订单查询成功", $orderinfo);
    }

	/**
	 *  余额支付模块
	 *  @param  ucode,orderid,money
	 *
	 */
	public function BalancePay($parr){
		$ucode = $parr['ucode'];
		$orderid = $parr['orderid'];

		$w['c_ucode'] = $ucode;
	    $w['c_orderid'] = $parr['orderid'];

	    $orderinfo = M('Shopact_log')->where($w)->find();
	    if(!$orderinfo){
	    	return Message(1001,"订单信息不存在");
	    }

	    if ($orderinfo['c_pay_state'] != 0) {
            return Message(1016, "该订单已支付");
        }
	    //订单已支付金额
	    $actual_price = $orderinfo['c_actual_price'];
	    $money = $parr['money'];

	    $balance = 0;
	    $tempcount = $orderinfo['c_total_price'] + $orderinfo['c_free'];

	    $paystatu = 0;
	    //计算订单金额
	    if($actual_price == 0){
	        if ($money >= $tempcount) {
	            $paystatu = 1;
	            $balance = $tempcount;
	            $actprice = $tempcount;
	        } else {
	            $balance = $money;
	            $actprice = $money;
	        }
	    }else{
	        $surplus = bcsub($tempcount,bcadd($actual_price,$money,2),2);
	        if($surplus == 0){
	            $paystatu = 1;
	            $balance = $money;
	            $actprice = $tempcount;
	        } elseif ($surplus > 0){
	            $balance = $money;
	            $actprice = bcadd($actual_price,$money,2);
	        } else {
	            $paystatu = 1;
	            $balance = bcadd($money,$surplus,2);
	            $actprice = bcsub($tempcount,$actual_price,2);
	        }
	    }

	    $db = M('');
	    $db->startTrans();

	    $Msgcentre = IGD('Msgcentre', 'Message');
	    if ($balance > 0) {
	        $aorderinfo['c_actual_price'] = $actprice;

	        //用户功勋操作
	        $parr['ucode'] = $ucode;
	        $parr['money'] = $balance;
	        $parr['key'] = $orderid;
	        $parr['desc'] = "余额支付";
	        $parr['source'] = 3;
	        $parr['state'] = 1;
	        $parr['type'] = 0;
	        $parr['isagent'] = 0;
	        $parr['showimg'] = 'Uploads/settlementshow/gou1.png';
	        $parr['showtext'] = '购物';
	        $result = IGD('Money','User')->OptionMoney($parr);

	        if ($result['code'] != 0) {
	            $db->rollback();
	            return $result;
	        }
	        //用户支付记录操作
	        $result = IGD('Order','Order')->paylog($orderid,4, $balance, "",5);
	        if ($result['code'] != 0) {
	            $db->rollback();
	            return $result;
	        }
	        //给用户发信息
	        $msgdata['ucode'] = $ucode;
	        $msgdata['type'] = 2;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['content'] = '您参与拼团活动，抵扣余额￥' . $balance . '成功';
	        $msgdata['tag'] = 2;
	        $msgdata['weburl'] = GetHost(1).'/index.php/Home/Myacts/detail?act_pcode='.$orderinfo['c_act_pcode'].'&groupcode='.$orderinfo['c_groupcode'].'&orderid='.$orderinfo['c_orderid'];
	        $msgdata['tagvalue'] = GetHost(1).'/index.php/Home/Myacts/detail?act_pcode='.$orderinfo['c_act_pcode'].'&groupcode='.$orderinfo['c_groupcode'].'&orderid='.$orderinfo['c_orderid'];
	        $Msgcentre->CreateMessege($msgdata);
	    }


	    if($paystatu == 1){
	        $aorderinfo['c_pay_state'] = 1;
	        $aorderinfo['c_paytime'] = gdtime();

	        //增加拼团人数
	        $havenum = $orderinfo['c_havenum'] + 1;
	        $aorderinfo['c_havenum'] = $havenum;

	        //给用户发送相关消息
	        $msgdata['ucode'] = $ucode;
	        $msgdata['type'] = 2;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['content'] = '您参与拼团活动支付成功，等待最终';
	        $msgdata['tag'] = 2;
	        $msgdata['tagvalue'] = GetHost(1).'/index.php/Home/Myacts/index';
	        $msgdata['weburl'] = GetHost(1).'/index.php/Home/Myacts/index';
	        $Msgcentre->CreateMessege($msgdata);
	    }

	    $orderwhere['c_orderid'] = $orderid;
	    $result = M('Shopact_log')->where($orderwhere)->save($aorderinfo);
	    if (!$result) {
	        $db->rollback();
	        return Message(3005, "余额支付订单失败");
	    }

	    //如果拼团人数达到目标
        if($paystatu == 1){
        	//改变所有团的拼团人数
        	$groupwhere['c_groupcode'] = $orderinfo['c_groupcode'];
        	$groupwhere['c_pay_state'] = 1;
        	$groupsave['c_havenum'] = $havenum;
        	$groupsave['c_updatetime'] = gdtime();
        	$result = M('Shopact_log')->where($groupwhere)->save($groupsave);
        	if (!$result) {
            	$db->rollback();
            	return Message(3000,'修改拼团人数失败');
        	}

			//修改活动进行状态
			$save_data['c_state'] = 1;
			$spw['c_act_pcode'] = $orderinfo['c_act_pcode'];
        	$result = M('Shopact_product')->where($spw)->save($save_data);	

        	if ($havenum >= $orderinfo['c_targetnum']) {
        		$groupsave['c_state'] = 1;	
        		$result = M('Shopact_log')->where($groupwhere)->save($groupsave);
	        	if (!$result) {
	            	$db->rollback();
	            	return Message(3000,'修改拼团状态失败');
	        	}
	        	$param['groupcode'] = $orderinfo['c_groupcode'];
	        	$param['payrule'] = 4;
	        	$result = $this->CreateOrders($param);
	        	if ($result['code'] != 0) {
	            	$db->rollback();
	            	return $result;
	        	}	        	

	        	$msgdata['ucode'] = $orderinfo['c_acode'];
	        	$msgdata['type'] = 2;
	        	$msgdata['platform'] = 1;
	        	$msgdata['sendnum'] = 1;
	        	$msgdata['title'] = '订单消息';
	        	$msgdata['content'] = '您有拼团活动被拼成功，请安排尽快发货';
	        	$msgdata['tag'] = 2;
	        	$msgdata['tagvalue'] = GetHost(1).'/index.php/Order/Storeorder/detail?orderid='.$orderinfo['c_orderid'];
	        	$msgdata['weburl'] = GetHost(1).'/index.php/Order/Storeorder/detail?orderid='.$orderinfo['c_orderid'];
	        	$Msgcentre->CreateMessege($msgdata);
        	}

        	$shopprofit = $tempcount;
            /*****   新增银盛支付代付代扣   ******/ 
            //当商家到账的利润大于支付利润采用代扣
            //当商家到账的利润小于支付利润采用代付
            if ($shopprofit > 0) {
                $arr['sign'] = 1; // 1 代付 2 代扣
                $opmoney = $shopprofit;
                $arr['type'] = 2; // 1  实时结算  2 按日结算  3 按月结算
                $arr['ucode'] = $orderinfo['c_acode']; // 分润人
                $arr['scode'] = $orderinfo['c_acode'];
                $arr['bcode'] = $orderinfo['c_ucode'];
                $arr['orderid'] = CreateOrder('f');
                $arr['key'] = $orderinfo['c_orderid'];
                $arr['desc'] = '平台余额支付订单交易资金操作';
                $arr['total_money'] = $orderinfo['c_total_price'];
                $arr['money'] = $opmoney;
                $arr['source'] = 1; // 1普城订单,2后台,3活动,4蜜城订单,5普城跨界,6提现,7注册,8老注册,9扫码,10转发,11绑定,12跨界扫码,13普城购返,14普城推返,15蜜城跨界,16普通退款,17蜜城退款,18红包',
                $res = IGD('Splitting','Order')->CreateRecord($arr);
                if($res['code']!=0){
                    $db->rollback(); //不成功，则回滚
                    return Message(1001,'创建代扣失败');
                }
            }
            /*****   新增银盛支付代付代扣   ******/
        }

	    $db->commit();

	    if($paystatu == 1){
	        return MessageInfo(10086, "余额支付订单完成",$orderid);
	    }

	    return Message(0, "余额支付订单成功");
	}

	/**
	 *  用户支付订单用于回调函数
	 *  @param  orderid
	 *
	 */
	public function PayOrder($parr){
		$upay = $parr['upay'];
        $orderid = $parr['orderid'];

        //友收宝支付回调
        if ($upay == 1) {
            $payorderid = $orderid;
            $result = $this->GetSystemOrder($orderid);
            if ($result['code'] != 0) {
                return $result;
            }
            $orderid = $result['data']['c_orderid'];
        }

		$payrule = $parr['payrule'];
		$actualprice = $parr['actualprice'];
		$thirdpartynum = $parr['thirdpartynum'];

		$orderwhere['c_orderid'] = $orderid;
		$orderinfo = M('Shopact_log')->where($orderwhere)->find();
		if (count($orderinfo) == 0) {
            return Message(1016, "没有查询到该订单");
        }

        $ucode = $orderinfo['c_ucode'];

        if ($orderinfo['c_pay_state'] != 0) {
            return Message(1016, "该订单已支付");
        }

        $totprice = $orderinfo['c_total_price'];
        $free = $orderinfo['c_free'];
        $actual = $orderinfo['c_actual_price'];
        //计算订单的总价
        $countprice = bcadd($totprice, $free, 2);
        
        //计算支付总价
        $payzong = bcadd($actualprice, $actual, 2);
        if ($payzong < $countprice) {
            return Message(1016, "您支付的金额不足");
        }

        $db = M('');
        $db->startTrans();

        $havenum = $orderinfo['c_havenum'] + 1;

        //开始修改订单信息
        $aorderinfo['c_havenum'] = $havenum;
        $aorderinfo['c_pay_state'] = 1;
        $aorderinfo['c_pay_rule'] = $payrule;
        $aorderinfo['c_actual_price'] = $payzong;
        $aorderinfo['c_paytime'] = gdtime();
        $result = M('Shopact_log')->where($orderwhere)->save($aorderinfo);
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1017, "订单信息操作失败");
        }

        //友收宝支付回调修改支付状态
        if ($upay == 1) {
            $result = $this->SavePayorder($payorderid,1);
            if ($result['code'] != 0) { 
                $db->rollback(); //不成功，则回滚
                return $result;
            }
        }

        //修改活动进行状态
		$save_data['c_state'] = 1;
		$spw['c_act_pcode'] = $orderinfo['c_act_pcode'];
    	$result = M('Shopact_product')->where($spw)->save($save_data);

        //改变所有团的拼团人数
    	$groupwhere['c_groupcode'] = $orderinfo['c_groupcode'];
    	$groupwhere['c_pay_state'] = 1;
    	$groupsave['c_havenum'] = $havenum;
    	$groupsave['c_updatetime'] = gdtime();
    	$result = M('Shopact_log')->where($groupwhere)->save($groupsave);
    	if (!$result) {
        	$db->rollback();
        	return Message(3000,'修改拼团人数失败');
    	}

        //用户支付记录操作
        $result = IGD('Order', 'Order')->paylog($orderid, $payrule, $actualprice, $thirdpartynum,5,$payorderid);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return $result;
        }

        $Msgcentre = IGD('Msgcentre', 'Message');

        //如果拼团人数达到目标
        if($havenum >= $orderinfo['c_targetnum']){
        	$groupsave['c_state'] = 1;	
    		$result = M('Shopact_log')->where($groupwhere)->save($groupsave);
        	if (!$result) {
            	$db->rollback();
            	return Message(3000,'修改拼团状态失败');
        	}

        	$param['groupcode'] = $orderinfo['c_groupcode'];
        	$param['payrule'] = 4;

        	$result = $this->CreateOrders($param);
        	if ($result['code'] != 0) {
            	$db->rollback();
            	return $result;
        	}

        	$msgdata['ucode'] = $orderinfo['c_acode'];
        	$msgdata['type'] = 2;
        	$msgdata['platform'] = 1;
        	$msgdata['sendnum'] = 1;
        	$msgdata['title'] = '订单消息';
        	$msgdata['content'] = '您有拼团活动被拼成功，请安排尽快发货';
        	$msgdata['tag'] = 2;
        	$msgdata['tagvalue'] =  GetHost(1).'/Order/Storeorder/detail?orderid='.$orderinfo['c_orderid'];
        	$msgdata['weburl'] =  GetHost(1).'/Order/Storeorder/detail?orderid='.$orderinfo['c_orderid'];
        	$Msgcentre->CreateMessege($msgdata);
        }

        $shopprofit = $countprice;
        /*****   新增银盛支付代付代扣   ******/ 
        //当商家到账的利润大于支付利润采用代扣
        //当商家到账的利润小于支付利润采用代付
        if ($actualprice != $shopprofit) {
            if ($actualprice > $shopprofit) {
                $arr['sign'] = 2; // 1 代付 2 代扣
                $opmoney = bcsub($actualprice,$shopprofit,2);
            } else {
                $arr['sign'] = 1; // 1 代付 2 代扣
                $opmoney = bcsub($shopprofit,$actualprice,2);
            }

            $arr['type'] = 2; // 1  实时结算  2 按日结算  3 按月结算
            $arr['ucode'] = $orderinfo['c_acode']; // 分润人
            $arr['scode'] = $orderinfo['c_acode'];
            $arr['bcode'] = $orderinfo['c_ucode'];
            $arr['orderid'] = CreateOrder('f');
            $arr['key'] = $orderinfo['c_orderid'];
            $arr['desc'] = '平台拼团活动订单交易资金操作';
            $arr['total_money'] = $orderinfo['c_total_price'];
            $arr['money'] = $opmoney;
            $arr['source'] = 1; // 1普城订单,2后台,3活动,4蜜城订单,5普城跨界,6提现,7注册,8老注册,9扫码,10转发,11绑定,12跨界扫码,13普城购返,14普城推返,15蜜城跨界,16普通退款,17蜜城退款,18红包',
            $res = IGD('Splitting','Order')->CreateRecord($arr);
            if($res['code']!=0){
                $db->rollback(); //不成功，则回滚
                return Message(1001,'创建代扣失败');
            }
        }
        /*****   新增银盛支付代付代扣   ******/ 

        //给用户发送相关消息
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 2;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = '您参与拼团活动支付成功，等待最终';
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1).'/index.php/Home/Myacts/detail?act_pcode='.$orderinfo['c_act_pcode'].'&groupcode='.$orderinfo['c_groupcode'].'&orderid='.$orderinfo['c_orderid'];
        $msgdata['weburl'] = GetHost(1).'/index.php/Home/Myacts/detail?act_pcode='.$orderinfo['c_act_pcode'].'&groupcode='.$orderinfo['c_groupcode'].'&orderid='.$orderinfo['c_orderid'];
        $Msgcentre->CreateMessege($msgdata);

        //提交事务
        $db->commit();

        return Message(0, "支付成功");
	}

	/**
	 *  创建订单
	 *  @param  groupcode,pay_rule
	 *
	 */
	public function CreateOrders($parr){
		$w['c_groupcode'] = $parr['groupcode'];
		$w['c_pay_state'] = 1;

		$loginfo = M('Shopact_log')->where($w)->select();
		foreach ($loginfo as $key => $value) {
			//生成订单详情
			$return = $this->CreataOrderdetails($value);

			if ($return['code'] != 0) {
			    return $return;
			}

			//生成订单
			$return = $this->CreataOrderInfo($value);
			if ($return['code'] != 0) {
			    return $return;
			}

			//快递方式收货生成地址
			if ($value['c_delivery'] != 3) {
				//生成订单地址
	        	$result = IGD('Order','Order')->CreataOrderAddress($value['c_orderid'], $value['c_addressid']);
	        	if ($result['code'] != 0) {
	        	    return $result;
	        	}
			} else {
				// 查用户相关信息	
				$uwh['c_ucode'] = $value['c_ucode'];
				$userphone = M('Users')->where($uwh)->getField('c_phone');
				$twh['c_orderid'] = $value['c_orderid'];
				$tcode = M('Tihuo_log')->where($twh)->getField('c_tcode');

				//查询商家位置信息
	            $acdw['c_ucode'] = $value['c_acode'];
	            $acodeinfo = M('Store')->where($acdw)->find();
	            $acodephone = M('Users')->where($acdw)->getField('c_phone');
	            $address = $acodeinfo['c_provice'].$acodeinfo['c_city'].$acodeinfo['c_district'].$acodeinfo['c_address'];

	            //发送短信通知用户
	            if ($userphone && $tcode && $acodeinfo && $acodephone && $address) {
					$sendparr['telephone'] = $userphone;
		            $sendparr['type'] = 1000;
		            $sendparr['userid'] = C('TEl_USER');
		            $sendparr['account'] = C('TEl_ACCESS');
		            $sendparr['password'] = C('TEl_PASSWORD');
		            $sendparr['content'] = "【微领地小蜜】您的".$value['c_pname']."拼团已成功,请凭提货码:".$tcode.",到店自提 ,店铺名称:".$acodeinfo['c_name'].",店铺地址:".$address."店铺电话:".$acodephone.", 查看订单：https://m.weilingdi.com";
		            $returndata = IGD('Sendmsg','Login')->SendVerify($sendparr);
	            }
	        }
		}

		return Message(0, "订单生成成功");
	}

	//生成订单详情
	protected function CreataOrderdetails($parr) {
	    $detailid = CreateOrder("dg");
	    $tempdetails['c_orderid'] = $parr['c_orderid'];
	    $tempdetails['c_detailid'] = $detailid;
	    $tempdetails['c_ucode'] = $parr['c_ucode'];
	    $tempdetails['c_pcode'] = $parr['c_pcode'];
	    $tempdetails['c_pname'] = $parr['c_pname'];
	    $tempdetails['c_pprice'] = sprintf('%.2f',$parr['c_total_price']/$parr['c_pnum']);
	    $tempdetails['c_pmodel'] = $parr['c_mcode'];
	    $tempdetails['c_pmodel_name'] = $parr['c_model_name'];
	    $tempdetails['c_pnum'] = $parr['c_pnum'];
	    $tempdetails['c_ptotal'] = $parr['c_total_price'];
	    $tempdetails['c_pimg'] = $parr['c_imgpath'];
	    $tempdetails['c_free'] = $parr['c_free'];
	    $tempdetails['c_profit'] = bcsub($parr['c_actual_price'], $parr['c_free'],2);
	    $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());
	    $result = M('Order_details')->add($tempdetails);
	    if (!$result) {
	        return Message(1000, "生成订单详情失败");
	    }
	    return MessageInfo(0, "订单详情生成成功", $tempdetails);
	}

	//创建订单信息
	protected function CreataOrderInfo($parr) {
		$result = IGD('Index','Newact')->GetPlathavingAct(26);
		$actvitydata = $result['data'];
		if (!$actvitydata) {
			return Message(1008,'活动不存在');
		}

	    $aorderinfo = array();
	    $aorderinfo['c_orderid'] = $parr['c_orderid'];
	    $aorderinfo['c_ucode'] = $parr['c_ucode'];
	    $aorderinfo['c_acode'] = $parr['c_acode'];
	    $aorderinfo['c_pay_state'] = 1;
	    $aorderinfo['c_order_state'] = 2;
	    $aorderinfo['c_total_price'] = $parr['c_total_price'];
	    $aorderinfo['c_actual_price'] = $parr['c_actual_price'];
	    $aorderinfo['c_free'] = $parr['c_free'];
	    $aorderinfo['c_pay_rule'] = $parr['c_pay_rule'];
	    $aorderinfo['c_delivery'] = $parr['c_delivery'];
	    $aorderinfo['c_paytime'] = gdtime();
	    $aorderinfo['c_activity_id'] = $actvitydata['c_id'];
	    $aorderinfo['c_activity_name'] = $actvitydata['c_activityname'];
	    $aorderinfo['c_source'] = 5;
	    $aorderinfo['c_addtime'] = gdtime();

	    $result = M('Order')->add($aorderinfo);
	    if (!$result) {
	        return Message(3005, "创建订单失败");
	    }

	    //给用户发送相关消息 
	    $Msgcentre = IGD('Msgcentre', 'Message');
	    $msgdata['ucode'] = $parr['c_ucode'];
	    $msgdata['type'] = 2;
	    $msgdata['platform'] = 1;
	    $msgdata['sendnum'] = 1;
	    $msgdata['title'] = '订单消息';
	    $msgdata['content'] = '您的拼团订单，已经生成，请查收';
	    $msgdata['tag'] = 3;
	    $msgdata['tagvalue'] = $parr['c_orderid'];
	    $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $parr['c_orderid'];
	    $Msgcentre->CreateMessege($msgdata);

	    return Message(0, "订单创建成功");
	}

	/**
	* 我的活动 拼团	
	*/

	/**
	 * 我参与的拼团记录
	 * @param pageindex,ucode
	 */
	public function MyJoinGroup($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$result = IGD('Index','Newact')->GetPlathavingAct(26);
		$aid = $result['data']['c_id'];

		$w['a.c_ucode'] = $parr['ucode'];
		$w['a.c_actual_price'] = array('GT',0);
		$w['a.c_aid'] = $aid;

		$field = "a.*,p.c_name,p.c_value as price,p.c_actprice";
		$join = "left join t_shopact_product as p on p.c_act_pcode=a.c_act_pcode";
		$order = "a.c_addtime desc";

		$list = M('Shopact_log as a')->field($field)->where($w)->join($join)->order($order)->limit($countPage, $pageSize)->select();

		$count = M('Shopact_log as a')->where($w)->join($join)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, "查询成功", $data);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];

			//距离结束时间
			$list[$key]['diftime'] = date('H:i:s',strtotime($value['c_endtime'])-time());
			$list[$key]['stattime'] = strtotime($value['c_starttime'])-time();
			$list[$key]['endtime'] = strtotime($value['c_endtime'])-time();

			// if($value['c_state'] == 0){
				$list[$key]['sharetit'] = "我在小蜜，参与团购，快来一起参团";
				$list[$key]['shareurl'] =  GetHost(1).'/index.php/Shopping/Collage/pjoin?groupcode='.$value['c_groupcode'].'&act_pcode='.$value['c_act_pcode'];
				$list[$key]['shareimg'] = GetHost().'/'.$value['c_imgpath'];
				$list[$key]['sharedesc'] = "我在小蜜，一起拼团买好货，实惠划算。";
			// }
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
	    return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 定时器检测拼团订单失败退款
	 * @param
	 */
	function CheckGroupbuyOrder()
	{
		$act = IGD('Index','Newact')->GetPlathavingAct(26);
		$actvitydata = $act['data'];

		$where['c_state'] = 0;
		$where['c_actual_price'] = array('GT',0);
		$where['c_aid'] = $actvitydata['c_id'];
		$where['c_endtime'] = array('LT',gdtime());
		$list = M('Shopact_log')->where($where)->limit(20)->select();

		foreach ($list as $key => $value) {
			$db = M('');
			$db->startTrans();

			//改变支付状态为退款
			$save['c_state'] = 2;
			$save['c_pay_state'] = 2;
			$save['c_updatetime'] = gdtime();
			$w['c_id'] = $value['c_id'];
			$w['c_state'] = 0;
			$result = M('Shopact_log')->where($w)->save($save);
			if (!$result) {
				$db->rollback();
				return Message(3000,'操作失败');
			}

			//退款到用户余额
	        $rebatemoney = IGD('Money', 'User');
	        $parr['ucode'] = $value['c_ucode'];
	        $parr['money'] = $value['c_actual_price'];
	        $parr['source'] = 2;
	        $parr['key'] = $value['c_orderid'];
	        $parr['desc'] = "您参与【" . $value['c_pname'] . "】拼团失败，退款成功";
	        $parr['state'] = 1;
	        $parr['type'] = 1;
	        $parr['isagent'] = 0;
	        $parr['showimg'] = 'Uploads/settlementshow/tuik.png';
	        $parr['showtext'] = '退款';
	        $result = $rebatemoney->OptionMoney($parr);
	        if ($result['code'] != 0) {
	            $db->rollback(); //不成功，则回滚
	            return $result;
	        }


	        //给用户发送相关消息
	        $Msgcentre = IGD('Msgcentre', 'Message');
	        $msgdata['ucode'] = $value['c_ucode'];
	        $msgdata['type'] = 2;
	        $msgdata['platform'] = 1;
	        $msgdata['sendnum'] = 1;
	        $msgdata['title'] = '系统消息';
	        $msgdata['content'] = '您参与【'.$value['c_pname'].'】拼团失败，支付金额￥' .$value['c_actual_price']. '，已退回余额';
	        $msgdata['tag'] = 2;
	        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
	        $Msgcentre->CreateMessege($msgdata);

	        // 查用户相关信息	
			$uwh['c_ucode'] = $value['c_ucode'];
			$userphone = M('Users')->where($uwh)->getField('c_phone');

            //发送短信通知用户
            if ($userphone) {
				$sendparr['telephone'] = $userphone;
	            $sendparr['type'] = 1000;
	            $sendparr['userid'] = C('TEl_USER');
	            $sendparr['account'] = C('TEl_ACCESS');
	            $sendparr['password'] = C('TEl_PASSWORD');
	            $sendparr['content'] = "【微领地小蜜】您的".$value['c_pname']."拼团已失败,系统已将支付金额退款至您的小蜜账户中，请注意查收！如有问题请拨400-9603-400";
	            $returndata = IGD('Sendmsg','Login')->SendVerify($sendparr);
            }
			
			$db->commit();
		}


		return MessageInfo(0,'操作成功',$result);
	}

	/**
	 * 定时器检测所有活动产品到期返回库存
	 * @param 
	 */
	function CheckGroupbuy()
	{
		$where['c_state'] = array('NEQ',2);
		$nowtime = gdtime();
		$where[] = array("c_endtime<'$nowtime'");
		$list = M('Shopact_product')->where($where)->limit(20)->select();

		foreach ($list as $key => $value) {
			$db = M('');
			$db->startTrans();

			//改变活动产品状态
			$save['c_state'] = 2;
			$w['c_id'] = $value['c_id'];
			$result = M('Shopact_product')->where($w)->save($save);
			if (!$result) {
				$db->rollback();
				return Message(3000,'操作失败');
			}

			if ($value['c_num'] > 0) {
				//返回产品库存
				$w1['c_pcode'] = $value['c_pcode'];
				$result = M('Product')->where($w1)->setInc('c_num',$value['c_num']);
				if (!$result) {
					$db->rollback();
					return Message(3001,'操作失败');
				}
			}
			$db->commit();
		}

		return MessageInfo(0,'操作成功',$result);
	}

	/**
     * 根据支付临时单号查询系统订单号
     * @param payorderid
     */
    function GetSystemOrder($payorderid)
    {
        $where['c_payorderid'] = $payorderid;
        $data = M('Order_payorderid')->where($where)->find();
        if (!$data) {
            return Message(3000,'订单不存在');
        }

        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 根据系统订单号生成临时支付订单号
     * @param orderid
     */
    function CreatePayorder($orderid)
    {
        $sign = substr($orderid,0,1).rand(1,9);
        $payorderid = CreateOrder($sign);

        $db = M('');
        $db->startTrans();

        //写入临时订单记录表
        $add['c_orderid'] = $orderid;
        $add['c_payorderid'] = $payorderid;
        $add['c_pay_state'] = 0;
        $add['c_addtime'] = gdtime();
        $result = M('Order_payorderid')->add($add);
        if (!$result) {
            $db->rollback();
            return Message(3000,'记录订单号失败');
        }

        //保存临时订单到订单表
        $ow['c_orderid'] = $orderid;
        $osave['c_payorderid'] = $payorderid;
        $result = M('Shopact_log')->where($ow)->save($osave);
        if (!$result) {
            $db->rollback();
            return Message(3000,'记录订单号失败');
        }

        $db->commit();
        return MessageInfo(0,'生成订单成功',$payorderid);
    }

    /**
     * 改变临时订单支付状态
     * @param payorderid
     */
    function SavePayorder($payorderid,$pay_state)
    {
        $where['c_payorderid'] = $payorderid;
        $save['c_pay_state'] = $pay_state;
        $result = M('Order_payorderid')->where($where)->save($save);
        if (!$result) {
            return Message(3000,'操作失败');
        }

        return Message(0,'操作成功');
    }
	
	/**
	 * 查询商家店铺位置信息
	 */
	function GetStoreLocal($parr){
		//查询商家位置信息
	    $acdw['c_ucode'] = $parr['ucode'];
	    $acodeinfo = M('Store')->where($acdw)->find();	    
		return MessageInfo(0,'查询成功',$acodeinfo);		
	}
}