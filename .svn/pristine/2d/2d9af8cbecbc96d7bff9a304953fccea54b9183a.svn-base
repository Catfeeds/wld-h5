<?php
/**
 * 商家活动 砍价活动
 */
class BargainNewact {

	/**
	* 商家砍价管理
	*/

	/**
	 *  创建砍价产品 选择产品
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
        $result = IGD('Index','Newact')->GetPlathavingAct(27);
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
	 *  创建砍价产品 选择产品型号
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
	 *  创建砍价产品
	 *  @param ucode,pcode,mcode,actprice,israndom(1随机,2不随机),usernum,targetnum,totalnum,starttime,endtime
	 */
	public function CreateProduct($parr){
		//查询活动信息
		$result = IGD('Index','Newact')->GetPlathavingAct(27);
        $aid = $result['data']['c_id'];
		if(!$aid){
			return Message(2000, "活动不存在或者未开启");
		}

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
		$add['c_act_pcode'] = CreateUcode('actk');
		$add['c_aid'] = $aid;
		$add['c_ucode'] = $parr['ucode'];
		$add['c_pcode'] = $parr['pcode'];
		$add['c_mcode'] = $parr['mcode'];
		$add['c_imgpath'] = $productinfo['c_pimg'];
		$add['c_name'] = $productinfo['c_name'];
		$add['c_value'] = $productinfo['c_price'];
		$add['c_actprice'] = $parr['actprice'];
		$add['c_targetnum'] = $parr['targetnum'];
		$add['c_totalnum'] = $totalnum;
		$add['c_num'] = $totalnum;
		$add['c_usernum'] = $parr['usernum'];
		if($parr['israndom'] == 2){ //每次砍价金额不随机
			$zhekou = bcsub($productinfo['c_price'],$parr['actprice'], 2);
			$add['c_bargainprice'] = bcdiv($zhekou, $parr['targetnum'],2);
		}
		$add['c_starttime'] = $parr['starttime'];
		$add['c_endtime'] = $parr['endtime'];

		$add['c_addtime'] = gdtime();

		$db = M('');
		$db->startTrans();

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
        $shopact_log['c_aid'] = $aid;
        $shopact_log['c_ucode'] = $parr['ucode'];
        $shopact_log['c_act_pcode'] = $add['c_act_pcode'];
        $shopact_log['c_pname'] = $productinfo['c_name'];
        $shopact_log['c_acttype'] = 4;
        $shopact_log['c_starttime'] = $parr['starttime'];
        $shopact_log['c_endtime'] = $parr['endtime'];
        $shopact_log['c_weburl'] = GetHost(1)."/index.php/Shopping/Bargain/pdetail?act_pcode=".$add['c_act_pcode'];
        $shopact_log['c_addtime'] = gdtime();

        $result = M('Circle_shopact')->add($shopact_log);

        if (!$result) {
        	$db->rollback();
            return Message(3003,'添加商圈活动记录失败');
        }

		$db->commit();
		return Message(0,"添加成功");
	}

	/**
	 * 商家砍价商品管理
	 * @param pageindex,ucode,useraction(0-正在进行，1-未开始，2-已结束)
	 */
	public function MyProductList($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$result = IGD('Index','Newact')->GetPlathavingAct(27);
        $aid = $result['data']['c_id'];

		$w['c_aid'] = $aid;
		$w['c_ucode'] = $parr['ucode'];

		if($parr['useraction'] == 1){
			$w['c_state'] = 0;
		}else if($parr['useraction'] == 2){
			$w['c_state'] = 2;
		}else{
			$w['c_state'] = 1;
		}

		$w['c_isdel'] = 2;
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
				$list[$key]['shareurl'] =  GetHost(1).'/index.php/Shopping/Bargain/pdetail?act_pcode='.$value['c_act_pcode'];
				$list[$key]['shareimg'] = GetHost().'/'.$value['c_imgpath'];
				$list[$key]['sharedesc'] = "来小蜜，一起拼享受实惠划算的好货。";
			// }
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 删除砍价的商品
	 * @param ucode,act_pcode,useraction(1-删除未砍价商品，2-删除已结束砍价商品)
	 */
	public function DelMyProduct($parr){
		$result = IGD('Index','Newact')->GetPlathavingAct(27);
        $aid = $result['data']['c_id'];
		if($parr['useraction'] ==1){
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
	 * 砍价记录
	 * @param pageindex,act_pcode,logtype(0-进行记录，1-成功记录，2-失败记录)
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
		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];

			//成团差人数
			$list[$key]['difnum'] = $value['c_targetnum'] - $value['c_havenum'];

			//转换时间
			$list[$key]['c_addtime'] = date('Y-m-d H:i',strtotime($value['c_addtime']));

			//砍价是否随机
			$list[$key]['change'] = 1;
			$moneyinfo = M('Shopact_product')->where(array('c_act_pcode'=>$value['c_act_pcode']))->getField('c_bargainprice');
			if ($moneyinfo > 0) {
				$list[$key]['change'] = 0;
			}
			
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
	    return MessageInfo(0, '查询成功', $data);
	}

	/**
	* 店铺 砍价
	*/

	/**
	 * 店铺砍价商品列表
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
			$result = IGD('Index','Newact')->GetPlathavingAct(27);
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
	 * 领取商品
	 * @param ucode,act_pcode,mcode,pnum
	 */
	public function Receive($parr){
		$result = IGD('Index','Newact')->GetPlathavingAct(27);
        $aid = $result['data']['c_id'];
		if(!$aid){
			return Message(1001, "活动不存在或者已过期");
		}

		//判断该用户是否领取过
		$lw['c_ucode'] = $parr['ucode'];
		$lw['c_aid'] = $aid;
		$lw['c_act_pcode'] = $parr['act_pcode'];
		$loginfo = M('Shopact_log')->where($lw)->find();
		if($loginfo){
			return Message(1002,"您已经参加过此产品的砍价活动，不能重复参与！");
		}

		//查询商品信息
		$pw['c_aid'] = $aid;
		$pw['c_act_pcode'] = $parr['act_pcode'];
		$pinfo = M('Shopact_product')->where($pw)->find();
		if(!$pinfo){
			return Message(1003,"没有查询到相关商品信息！");
		}

		if($parr['pnum'] > $pinfo['c_num']){
			return Message(1003,"您来晚了，商品数量不足！");
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

		$db = M('');
		$db->startTrans();

		$groupcode = CreateUcode("k");
		$orderid = CreateOrder('k');

		//生成领取记录
		$add['c_aid'] = $aid;
		$add['c_ucode'] = $parr['ucode'];
		$add['c_acode'] = $pinfo['c_ucode'];
		$add['c_act_pcode'] = $pinfo['c_act_pcode'];
		$add['c_groupcode'] = $groupcode;	//砍价记录编码
		$add['c_pcode'] = $pinfo['c_pcode'];
		$add['c_pname'] = $pinfo['c_name'];
		$add['c_mcode'] = $parr['mcode'];  //型号
	    $add['c_model_name'] = $model_name;
		$add['c_imgpath'] = $pinfo['c_imgpath'];
		$add['c_value'] = $pinfo['c_value'];
		$add['c_pnum'] = $parr['pnum'];
		$add['c_orderid'] = $orderid;
		$add['c_total_price'] = $pinfo['c_actprice'];//活动砍价底价
		$add['c_actual_price'] = $pinfo['c_value'];//用于砍价剩余金额
		$add['c_targetnum'] = $pinfo['c_targetnum'];//砍价次数
		$add['c_havenum'] = 0;//已砍价次数
		$add['c_starttime'] = gdtime();
		$add['c_endtime'] = date('Y-m-d H:i:s',strtotime('+1 day'));
		$add['c_addtime'] = gdtime();
		$result = M('Shopact_log')->add($add);
		if(!$result){
			$db->rollback();
			return Message(1015,"领取失败");
		}

		//扣除活动库存并修改活动进行状态
		$save_data['c_state'] = 1;
		$save_data['c_num'] = $pinfo['c_num'] - $parr['pnum'];
    	$result = M('Shopact_product')->where($pw)->save($save_data);
    	if(!$result){
	    	$db->rollback();
	    	return Message(1016,"扣除活动库存失败");
	    }

		$db->commit();
		return MessageInfo(0,"领取砍价商品成功",$add);
	}

	/**
	 * 砍价后商品记录详情
	 * @param groupcode,ucode,openid  c_value(市场价)、c_total_price(砍价底价)、c_actual_price(砍后剩余价)
	 */
	public function ProductDetails($parr){
		$ucode = $parr['ucode'];
		$openid = $parr['openid'];

		$w['c_groupcode'] = $parr['groupcode'];

		$loginfo = M('Shopact_log')->where($w)->find();

		if(!$loginfo){
			return Message(1001,"没有查询到相关记录信息！");
		}		

		$loginfo['c_imgpath'] = GetHost().'/'.$loginfo['c_imgpath'];

		//累计砍价
		$loginfo['ljkj'] = bcsub($loginfo['c_value'],$loginfo['c_actual_price'], 2);
		//还需砍价
		$loginfo['hxkj'] = bcsub($loginfo['c_actual_price'],$loginfo['c_total_price'], 2);

		//计算砍价百分比
		$kanjiazong = bcsub($loginfo['c_value'], $loginfo['c_actual_price'], 2);
		$zhekou = bcsub($loginfo['c_value'], $loginfo['c_total_price'], 2);
		$baifenbi = bcmul(bcdiv($kanjiazong, $zhekou, 2), 100, 2);
		$loginfo['baifenbi'] = $baifenbi;

		//距离活动结束时间
		$loginfo['diftime'] = date('H:i:s',strtotime($loginfo['c_endtime'])-time());
		//距离结束时间
		$loginfo['stattime'] = strtotime($loginfo['c_starttime'])-time();
		$loginfo['endtime'] = strtotime($loginfo['c_endtime'])-time();

		//用户信息
		$uw['c_ucode'] = $loginfo['c_ucode'];
		$uinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();
		$loginfo['c_nickname'] = $uinfo['c_nickname'];
		$loginfo['c_headimg'] = GetHost().'/'.$uinfo['c_headimg'];

		$loginfo['bgstate'] = 0;
		//查询砍价记录
		$lw['c_groupcode'] = $parr['groupcode'];
		$lw[] = array("c_ucode='$ucode' or c_openid='$openid'");
		$log = M('Shopact_bargin')->where($lw)->find();
		if ($log) {  //已帮忙砍价
			$loginfo['bgstate'] = 1;
		}

		//分享数据
		$loginfo['sharetit'] = "我在小蜜领了一款产品，朋友们快来帮我砍价！";
		$loginfo['shareurl'] = GetHost(1)."/index.php/Shopping/Bargain/bshare?groupcode=".$loginfo['c_groupcode'];
		$loginfo['shareimg'] = GetHost().'/'.$loginfo['c_imgpath'];
		$loginfo['sharedesc'] = $loginfo['c_pname'];
		return MessageInfo(0,"查询成功",$loginfo);
	}

	/**
	 * 砍价记录
	 * @param groupcode,pageindex
	 */
	public function BarginLog($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$w['c_groupcode'] = $parr['groupcode'];

		$list = M('Shopact_bargin')->where($w)->limit($countPage, $pageSize)->order('c_id desc')->select();

		$count = M('Shopact_bargin')->where($w)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$list) {
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, "查询成功", $data);
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 用户、微商用户砍价
	 * @param ucode,openid,username,headimg,groupcode
	 */
	public function Bargin($parr){
		$w['c_groupcode'] = $parr['groupcode'];

		$loginfo = M('Shopact_log')->where($w)->find();
		if(!$loginfo){
			return Message(1001,"没有查询到相关记录信息！");
		}

		if ($loginfo['c_state'] == 1 || strtotime($loginfo['c_endtime']) < time()) {
			return Message(1002,"该砍价活动已过期！");
		}		

		//查询商品信息
		$pw['c_aid'] = $loginfo['c_aid'];
		$pw['c_act_pcode'] = $loginfo['c_act_pcode'];
		$pinfo = M('Shopact_product')->where($pw)->find();
		if(!$pinfo){
			return Message(1003,"没有查询到相关商品信息！");
		}

		$kekmoney = bcsub($loginfo['c_actual_price'], $loginfo['c_total_price'], 2);   //可砍金额		

		//砍价金额
		if($pinfo['c_bargainprice']){
			$kanjia = $pinfo['c_bargainprice'];
		}else{
			$synum =  bcsub($loginfo['c_targetnum'],$loginfo['c_havenum'],2);		  //剩余可砍人数
			$money = IGD('Red','Newact')->randBonus($kekmoney,$synum,1);
			$kanjia = $money[0];//随机金额
		}

		//查询用户是否已经砍价
		$bws['c_groupcode'] = $loginfo['c_groupcode'];
		if (!empty($parr['ucode'])) {
			$bws['c_ucode'] = $parr['ucode'];
		} else {
			$bws['c_openid'] = $parr['openid'];
			if (empty($parr['openid'])) {
				return Message(3001,'请从微信进入砍价');
			}
		}

		$db = M('');
		$db->startTrans();
		
		$result = M('Shopact_bargin')->where($bws)->getField('c_id');
		if ($result) {
			$db->rollback();
			return Message(1005,'您已经帮忙砍过');
		}

		//修改记录信息
		$save['c_havenum'] = $loginfo['c_havenum'] + 1;  //累计砍价次数

		$falg = 0;
		if($kekmoney <= $kanjia){//已砍到底价
			$kanjia = $kekmoney;
			$save['c_actual_price'] = bcsub($loginfo['c_actual_price'], $kanjia, 2);
			$save['c_state'] = 1;
			$falg = 1;
		} else {
			$save['c_actual_price'] = bcsub($loginfo['c_actual_price'], $kanjia, 2);
		}

		$result = M("Shopact_log")->where($w)->save($save);

		if(!$result){
			$db->rollback();
			return Message(1004,"砍价失败");
		}

		//添加砍价记录
		if(!empty($parr['ucode'])){
			$uw['c_ucode'] = $parr['ucode'];
			$userinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();

			$blog['c_ucode'] =$parr['ucode'];
			$blog['c_wxname'] = $userinfo['c_nickname'];
			$blog['c_headerimg'] = GetHost().'/'.$userinfo['c_headimg'];
		}else{
			$blog['c_openid'] = $parr['openid'];
			$blog['c_wxname'] = $parr['username'];
			$blog['c_headerimg'] = $parr['headimg'];
		}

		$blog['c_groupcode'] = $loginfo['c_groupcode'];
		$blog['c_aid'] = $loginfo['c_aid'];
		$blog['c_barginprice'] = $kanjia;
		$blog['c_bargintime'] = gdtime();

		$result = M('Shopact_bargin')->add($blog);

		if(!$result){
			$db->rollback();
			return Message(1016,"添加记录失败");
		}

		$data['kanjia'] = $kanjia;//砍价金额

		if($falg == 1){
			$Msgcentre = IGD('Msgcentre', 'Message');

			//给用户发送相关消息
			$msgdata['ucode'] = $loginfo['c_ucode'];
			$msgdata['type'] = 2;
			$msgdata['platform'] = 1;
			$msgdata['sendnum'] = 1;
			$msgdata['title'] = '系统消息';
			$msgdata['content'] = '您参与的【'.$loginfo['c_pname'].'】砍价活动已成功砍到底价，赶紧去支付吧';
			$msgdata['tag'] = 2;
			$msgdata['tagvalue'] = GetHost(1).'/index.php/Home/Myacts/bdetail?groupcode='.$loginfo['c_groupcode'];
			$msgdata['weburl'] = GetHost(1).'/index.php/Home/Myacts/bdetail?groupcode='.$loginfo['c_groupcode'];
			$Msgcentre->CreateMessege($msgdata);
		}

		$db->commit();
		return MessageInfo(0,"砍价成功",$blog);
	}

	/**
	 * 我参与的砍价活动
	 * @param pageindex,ucode
	 */
	public function MyJoinBargin($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$result = IGD('Index','Newact')->GetPlathavingAct(27);
        $aid = $result['data']['c_id'];

		$w['a.c_ucode'] = $parr['ucode'];
		// $w['a.c_pay_state'] = 1;
		$w['a.c_aid'] = $aid;

		$field = "a.*,p.c_imgpath,p.c_name,p.c_value as price,p.c_actprice";
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
				$list[$key]['sharetit'] = "我在小蜜领了一款产品，朋友们快来帮我砍价！";
				$list[$key]['shareurl'] = GetHost(1)."/index.php/Shopping/Bargain/bshare?groupcode=".$value['c_groupcode'];
				$list[$key]['shareimg'] = GetHost().'/'.$value['c_imgpath'];
				$list[$key]['sharedesc'] = $value['c_name'];
			// }
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
	    return MessageInfo(0, '查询成功', $data);
	}

	//拆分商家和产品信息并计算价格
    public function splitProduct($groupcode, $ucode) {
        //查询砍价数据信息
        $actwhere['c_groupcode'] = $groupcode;
        $actwhere['c_ucode'] = $ucode;
    	$product = M('Shopact_log')->where($actwhere)->select();
    	if (!$product) {
    		return Message(1010, "砍价产品信息不存在");
    	}

    	//查询出所有产品信息
        $shop = array();
        $freeprice = 0;
        //进行拆分订单
        foreach ($product as $key => $value) {
        	//查询产品信息
            $whereinfo['c_pcode'] = $value['c_pcode'];
            $whereinfo['c_ishow'] = 1;
            $whereinfo['c_isdele'] = 1;
            $productinfo = M('Product')->where($whereinfo)->find();
            if (count($productinfo) <= 0) {
                return Message(1015, "产品信息不存在，不能生成订单");
            }

            //查询活动产品信息
            $w['c_act_pcode'] = $value['c_act_pcode'];
            $w['c_isdel'] = 2;
            // $w['c_state'] = array('neq',2);
			$pinfo = M('Shopact_product')->where($w)->find();
			if (!$pinfo) {
				return Message(1016, "该活动已结束");
			}
            $price = $value['c_actual_price'];
            $num = $pinfo['c_num'];  

            if (!empty($value['c_mcode']) && strpos($value['c_mcode'], 'xn') === false) {
                //查询商品价格
                $wheremodel['c_pcode'] = $value['c_pcode'];
                $wheremodel['c_mcode'] = $value['c_mcode'];
                $ProductModel = M('Product_model')->where($wheremodel)->find();
                if (count($ProductModel) <= 0) {
                    return Message(1015, "没有查询到该产品型号");
                } else {
                    $num = $ProductModel['c_num'];
                    // 判断产品库存
		            if ($num < $value['c_pnum']) {
		                return Message(1015, "产品型号库存不足");
		            }
                }
                $pmodel = $ProductModel['c_mcode'];
            	$pmodel_name = $ProductModel['c_name'];
            } else {
            	$pmodel = '';
            	$pmodel_name = $productinfo['c_name'];
            }                     

            $info = array();
            $info['pcode'] = $productinfo['c_pcode'];
            $info['ucode'] = $ucode;
            $info['price'] = $price;
            $info['pname'] = $productinfo['c_name'];
            $info['pmodel'] = $pmodel;
            $info['pmodel_name'] = $pmodel_name;
            $info['num'] = $value['c_pnum'];
            $info['pimg'] = $productinfo['c_pimg'];
            $info['isagent'] = $productinfo['c_isagent'];
            $info['agent_pcode'] = $productinfo['c_agent_pcode'];

            $tempcount = $price * $value['c_pnum'];
            $singletotle = sprintf("%.2f", $tempcount);
            $info['singletotle'] = $singletotle;
            
            $info['freeprice'] = $productinfo['c_freeprice'];

            $shop['orderid'] = $value['c_orderid'];
            $shop['acode'] = $productinfo['c_ucode'];
            $shop['value'][] = $info;
            $shop['isagent'] = $productinfo['c_isagent'];
            if ($productinfo['c_isfree'] == 2) {
                $freeprice+=$productinfo['c_freeprice'] * $value['c_pnum'];
            }
        }

        $shop['freeprice'] = $freeprice;
        return MessageInfo(0, "产品拆分成功", $shop);
    }

	/**
	 * 创建订单
	 * @param ucode,groupcode,delivery,addressid,postscript,freeprice
	 */
	public function CreateOrders($parr){
		$addressid = $parr['addressid'];
		$ucode = $parr['ucode'];
		$groupcode = $parr['groupcode'];
		$delivery = $parr['delivery'];
		$postscript = $parr['postscript'];
		$freeprice = $parr['freeprice'];

		//查询砍价数据信息
        $result = $this->splitProduct($groupcode, $ucode);
    	if ($result['code'] != 0) {
    		return $result;
    	}
    	$shop = $result['data'];
        if (empty($shop)) {
            return Message(1015, "您没有传入产品信息");
        }

    	$info = $shop['value'];
        $free = $shop['freeprice'];
        $isagent = $shop['isagent'];
        $acode = $shop['acode'];
        if (empty($info)) {
            return Message(1015, "产品信息为空");
        }
        $actpinfo = $info[0];

		$orderid = $shop['orderid'];

		//生成订单
		$param['c_orderid'] = $orderid;
	    $param['c_ucode'] = $ucode;
	    $param['c_acode'] = $acode;
	    $param['c_pcode'] = $actpinfo['pcode'];
	    $param['c_pname'] = $actpinfo['pname'];
	    $param['c_pprice'] = $actpinfo['price'];
	    $param['c_pmodel'] = $actpinfo['pmodel'];
	    $param['c_pmodel_name'] = $actpinfo['pmodel_name'];
	    $param['c_pnum'] = $actpinfo['num'];
	    $param['c_ptotal'] = $actpinfo['singletotle'];
	    $param['c_pimg'] = $actpinfo['pimg'];
	    $param['c_delivery'] = $delivery;
	    $param['c_postscript'] = $postscript;
	    $param['c_freeprice'] = $freeprice;

		$db = M('');
		$db->startTrans();

		//生成订单详情
		$return = $this->CreataOrderdetails($param);
		if ($return['code'] != 0) {
			$db->rollback();
		    return $return;
		}

		//生成订单
		$return = $this->CreataOrderInfo($param);
		if ($return['code'] != 0) {
			$db->rollback();
		    return $return;
		}

		//生成订单地址
    	$result = IGD('Order','Order')->CreataOrderAddress($orderid, $addressid);
    	if ($result['code'] != 0) {
    	    $db->rollback(); //不成功，则回滚
    	    return $result;
    	}

    	//扣除型号库存
		if(!empty($actpinfo['pmodel']) && strpos($actpinfo['pmodel'], 'xn') === false){
			$wheremodel['c_pcode'] = $actpinfo['pcode'];
			$wheremodel['c_mcode'] = $actpinfo['pmodel'];
			$wheremodel['c_num'] = array('EGT',$actpinfo['num']);
			$result = M('Product_model')->where($wheremodel)->setDec('c_num',$actpinfo['num']);
			if (!$result) {
			    $db->rollback();
		    	return Message(1016,"扣除型号库存失败");
			}
		}

		//修改活动进行状态
    	$groupwhere['c_groupcode'] = $groupcode;
    	$groupsave['c_postscript'] = $postscript;
    	$groupsave['c_addressid'] = $addressid;
    	$groupsave['c_pay_state'] = 1;
    	$groupsave['c_state'] = 1;
    	$groupsave['c_paytime'] = gdtime();
    	$groupsave['c_updatetime'] = gdtime();
    	$result = M('Shopact_log')->where($groupwhere)->save($groupsave);
    	if (!$result) {
        	$db->rollback();
        	return Message(3000,'修改拼团人数失败');
    	}



		$db->commit();

		$data['orderid'] = $orderid;
	    return MessageInfo(0, "生成订单成功", $data);
	}

	//生成订单详情
	protected function CreataOrderdetails($parr) {
	    $detailid = CreateOrder("dk");
	    $tempdetails['c_orderid'] = $parr['c_orderid'];
	    $tempdetails['c_detailid'] = $detailid;
	    $tempdetails['c_ucode'] = $parr['c_ucode'];
	    $tempdetails['c_pcode'] = $parr['c_pcode'];
	    $tempdetails['c_pname'] = $parr['c_pname'];
	    $tempdetails['c_pprice'] = $parr['c_pprice'];
	    $tempdetails['c_pmodel'] = $parr['c_pmodel'];
	    $tempdetails['c_pmodel_name'] = $parr['c_pmodel_name'];
	    $tempdetails['c_pnum'] = $parr['c_pnum'];
	    $tempdetails['c_ptotal'] = $parr['c_ptotal'];
	    $tempdetails['c_pimg'] = $parr['c_pimg'];
	    $tempdetails['c_free'] = $parr['c_freeprice'];
	    $tempdetails['c_profit'] = $parr['c_ptotal'];
	    $tempdetails['c_addtime'] = date('Y-m-d H:i:s', time());

	    $result = M('Order_details')->add($tempdetails);
	    if (!$result) {
	        return Message(1000, "生成订单详情失败");
	    }
	    return MessageInfo(0, "订单详情生成成功", $tempdetails);
	}

	//创建订单信息
	protected function CreataOrderInfo($parr) {
		$result = IGD('Index','Newact')->GetPlathavingAct(27);
		$actvitydata = $result['data'];
		if (!$actvitydata) {
			return Message(1008,'活动不存在');
		}

		$result = M('Order')->where(array('c_orderid'=>$parr['c_orderid']))->getField('c_id');
		if ($result) {
			return Message(1007,'该砍价订单已生成');
		}

	    $aorderinfo = array();
	    $aorderinfo['c_orderid'] = $parr['c_orderid'];
	    $aorderinfo['c_ucode'] = $parr['c_ucode'];
	    $aorderinfo['c_acode'] = $parr['c_acode'];
	    $aorderinfo['c_pay_state'] = 0;
	    $aorderinfo['c_order_state'] = 2;
	    $aorderinfo['c_total_price'] = $parr['c_ptotal'];
	    $aorderinfo['c_delivery'] = $parr['c_delivery'];
	    $aorderinfo['c_postscript'] = $parr['c_postscript'];
	    if ($aorderinfo['c_delivery'] == 1) {
	    	$aorderinfo['c_free'] = $parr['c_freeprice'];
	    }
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
	    $msgdata['content'] = '您的砍价订单，已经生成，请立即支付';
	    $msgdata['tag'] = 3;
	    $msgdata['tagvalue'] = $parr['c_orderid'];
	    $msgdata['weburl'] = GetHost(1) . '/index.php/Order/Index/detail?orderid=' . $parr['c_orderid'];
	    $Msgcentre->CreateMessege($msgdata);

	    return Message(0, "订单创建成功");
	}
}