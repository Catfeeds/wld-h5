<?php 

/**
*  宝箱热气球相关管理接口
*/
class AirboxNewact
{
	/**
	 * 商家进入服务中心页面自动添加活动
	 * @param ucode
	 */
	function AddActjoin($parr){
		$ucode = $parr['ucode'];

		//查询商家信息
		$field = 'u.c_shop,l.*';
		$join = "left join t_user_local as l on u.c_ucode=l.c_ucode";
		$w['u.c_ucode'] = $parr['ucode'];

		$userinfo = M('Users as u')->field($field)->join($join)->where($w)->find();

		if(!$userinfo){
			return Message(1001,"没有查询到商家信息！");
		}

		if($userinfo['c_shop'] == 0){
			return Message(1002,"用户身份有误！");
		}

		//活动类型
		if($userinfo['c_isfixed'] == 1){
			$rule = 22; //宝箱活动
		}else{
			$rule = 23; //热热气球活动
		}

		//查询平台活动是否开启
		$Newact = IGD('Index','Newact');
		
		$result = $Newact->GetPlathavingAct($rule,2);

		if($result['code'] != 0){
			return $result;
		}

		$activitydata = $result['data'];
		if ($activitydata['c_activitytype'] == 22) {
			$remark = '宝箱';
		} else if ($activitydata['c_activitytype'] == 23) {
			$remark = '热气球';
		}

		$aid = $activitydata['c_id'];

		//查询商家活动信息
		$result = $Newact->GetShopActivity(2,null,$aid,$ucode);

		if($result['code'] != 0){
			$isexist = 0;
		}else{
			$isexist = 1;
			$joinaid = $result['data']['c_id'];
		}

		$db = M('');
		$db -> startTrans();

		//添加或者同步商家活动信息
		if($isexist == 0){
			$add_data['c_acode'] = $ucode;
			$add_data['c_address'] = $userinfo['c_address'];
			$add_data['c_latitude'] = $userinfo['c_latitude'];
			$add_data['c_longitude'] = $userinfo['c_longitude'];
			$add_data['c_aid'] = $aid;
			$add_data['c_activityname'] = $activitydata['c_activityname'];
			$add_data['c_remark'] = $activitydata['c_remark'];
			$add_data['c_state'] = 1;
			$add_data['c_sign'] = 2;
			$add_data['c_activitytype'] = $activitydata['c_activitytype'];
			$add_data['c_activitystarttime'] = $activitydata['c_activitystarttime'];
			$add_data['c_activityendtime'] = $activitydata['c_activityendtime'];
			$add_data['c_listimg'] = $activitydata['c_listimg'];
			$add_data['c_pimg'] = $activitydata['c_pimg'];
			$add_data['c_img'] = $activitydata['c_img'];
			$add_data['c_istop'] = $activitydata['c_istop'];
			$add_data['c_ishot'] = $activitydata['c_ishot'];
			$add_data['c_addtime'] = gdtime();

			$result = M('Actjoin_moneylog')->add($add_data);

			$newaid = $result;
		}else{
			$add_data['c_address'] = $userinfo['c_address'];
			$save_data['c_latitude'] = $userinfo['c_latitude'];
			$save_data['c_longitude'] = $userinfo['c_longitude'];

			$save_data['c_activityname'] = $activitydata['c_activityname'];
			$save_data['c_remark'] = $activitydata['c_remark'];
			$save_data['c_activitystarttime'] = $activitydata['c_activitystarttime'];
			$save_data['c_activityendtime'] = $activitydata['c_activityendtime'];
			$save_data['c_listimg'] = $activitydata['c_listimg'];
			$save_data['c_pimg'] = $activitydata['c_pimg'];
			$save_data['c_img'] = $activitydata['c_img'];
			$save_data['c_istop'] = $activitydata['c_istop'];
			$save_data['c_ishot'] = $activitydata['c_ishot'];

			$aw['c_ucode'] = $ucode;
			$aw['c_aid'] = $aid;

			$result = M('Actjoin_moneylog')->where($aw)->save($save_data);
		}

		if($result<0){
			$db->rollback();
			return Message(1003,"活动信息更新失败");
		}
		
		//添加活动空项
		if($newaid){
			$add['c_acode'] = $ucode;
			$add['c_joinaid'] = $newaid;
			$add['c_type'] = 1;
			$add['c_aid'] = $aid;
			$add['c_status'] = 1;
			$add['c_name'] = "好运连连！心想事成！";
			$add['c_remark'] = $remark;
			$add['c_addtime'] = gdtime();

			$result = M('A_actprize')->add($add);

			if(!$result){
				$db->rollback();
				return Message(1004,"空奖项添加失败");
			}

			$joinaid = $newaid;
		}

		$db->commit();

		$data['joinaid'] = $joinaid;
		$data['activitytype'] = $rule;
		return MessageInfo(0,"操作成功",$data);
	}
	
	/**
	 * 获取宝箱热气球商品奖项列表
	 * @param joinaid,ucode
	 */
	function GetGoodsList($parr)
	{
		$w['c_acode'] = $parr['ucode'];
		$w['c_type'] = 4;
		$w['c_joinaid'] = $parr['joinaid'];
		//$w['c_status'] = 1;
		$w['c_delete'] = 2;

		$goodslist = M('A_actprize')->where($w)->limit(6)->select();

		if(!$goodslist){
			$goodslist = array();
			return MessageInfo(0,"查询成功",$goodslist);
		}

		foreach ($goodslist as $key => $value) {
			$goodslist[$key]['c_img'] = GetHost().'/'.$value['c_img'];
		}

		return MessageInfo(0,"查询成功",$goodslist);
	}

	/**
	 * 获取宝箱热气球卡劵奖项列表
	 * @param joinaid,ucode
	 */
	function GetCouponList($parr)
	{
		$w['c_acode'] = $parr['ucode'];
		$w['c_type'] = 3;
		$w['c_joinaid'] = $parr['joinaid'];
		$w['c_status'] = 1;
		$w['c_delete'] = 2;

		$couponlist = M('A_actprize')->where($w)->limit(6)->select();

		if(!$couponlist){
			$couponlist = array();
			return MessageInfo(0,"查询成功",$couponlist);
		}

		foreach ($couponlist as $key => $value) {
			$w1['c_id'] = $value['c_pid'];
			$couponlist[$key]['coupontype'] = M('A_actcard')->where($w1)->getField('c_type');
		}

		return MessageInfo(0,"查询成功",$couponlist);
	}

	/**
	 * 获取宝箱热气球红包奖项列表
	 * @param joinaid,ucode
	 */
	function GetRedsList($parr)
	{
		$w['c_acode'] = $parr['ucode'];
		$w['c_joinaid'] = $parr['joinaid'];
		$w['c_status'] = 1;
		$w['c_delete'] = 2;

		$redslist = M('A_redprize')->where($w)->limit(6)->select();

		if(!$redslist){
			$redslist = array();
		}		

		return MessageInfo(0,"查询成功",$redslist);
	}

	/**
	 * 获取宝箱热气球祝福语奖项
	 * @param joinaid,ucode
	 */
	function GetSpeak($parr)
	{
		$w['c_acode'] = $parr['ucode'];
		$w['c_type'] = 1;
		$w['c_joinaid'] = $parr['joinaid'];
		$w['c_status'] = 1;
		$w['c_delete'] = 2;

		$speak = M('A_actprize')->where($w)->find();

		return MessageInfo(0,"查询成功",$speak);
	}

	/**
	 * 宝箱热气球空奖祝福语编辑提交
	 * @param speakid,ucode,blessing
	 */
	function SpeakEditSubmit($parr){
		$w['c_id'] = $parr['speakid'];
		$w['c_acode'] = $parr['ucode'];

		$speakinfo = M('A_actprize')->where($w)->find();

		if(!$speakinfo){
			return Message(1001,"没有查询到相关祝福信息");
		}

		//修改活动空奖祝福语
		$save_data['c_name'] = $parr['blessing'];

		$result = M('A_actprize')->where($w)->save($save_data);
		
		if($result<0){
			return Message(1002,"修改失败");
		}

		return Message(0,"修改成功");
	}

	/**
	 * 获取宝箱热气球商品奖项详情
	 * @param pageindex,pagesize,joinaid,ucode
	 */
	function GetGoodsDetails($parr)
	{	
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$w['a.c_acode'] = $parr['ucode'];
		$w['a.c_type'] = 4;
		$w['a.c_joinaid'] = $parr['joinaid'];
		// $w['a.c_status'] = 1;
		$w['a.c_delete'] = 2;

		$field = "a.*,p.c_pcode,p.c_num as kcnum,p.c_freeprice";
		$join = "inner join t_product as p on a.c_pid=p.c_pcode";
		$order = "a.c_id desc";		

		$goodslist = M('A_actprize as a')->field($field)->join($join)->where($w)->limit($countPage, $pageSize)->order($order)->select();

		$count = M('A_actprize as a')->join($join)->where($w)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$goodslist) {
			$goodslist = array();
			$data = Page($pageIndex, $pageCount, $count, $goodslist);
			return MessageInfo(0, '查询成功', $data);
		}		

		foreach ($goodslist as $key => $value) {
			$goodslist[$key]['c_img'] = GetHost().'/'.$value['c_img'];

			$goodslist[$key]['starttime'] = date("Y年m月d日",strtotime($value['c_starttime']));
			$goodslist[$key]['endtime'] = date("Y年m月d日",strtotime($value['c_endtime']));
		}

		$data = Page($pageIndex, $pageCount, $count, $goodslist);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 宝箱热气球商品编辑提交
	 * @param goodid,ucode,num,freeprice,starttime,endtime
	 */
	function GoodEditSubmit($parr){
		$w['c_id'] = $parr['goodid'];
		$w['c_acode'] = $parr['ucode'];

		$goodinfo = M('A_actprize')->where($w)->find();

		if(!$goodinfo){
			return Message(1001,"没有查询到相关活动商品信息");
		}

		$db = M('');
		$db -> startTrans();

		//修改活动商品信息
		$save_data['c_totalnum'] = $parr['num'];
		$save_data['c_num'] = $parr['num'];
		$save_data['c_starttime'] = $parr['starttime'];
		$save_data['c_endtime'] = $parr['endtime'];
		$result = M('A_actprize')->where($w)->save($save_data);	

		if($result<0){
			$db->rollback();
			return MessageInfo(1002,"修改活动商品信息失败");
		}
		//修改商品库存
		$remain_num = $goodinfo['c_num'];
		if($remain_num > $parr['num']){
			$difnum = $remain_num - $parr['num'];//多余需退回商品库存

			$pw['c_ucode'] = $parr['ucode'];
			$pw['c_pcode'] = $goodinfo['c_pid'];

			$result = M('Product')->where($pw)->setInc('c_num',$difnum);

			if(!$result){
				$db->rollback();
				return Message(1003,"修改商品库存失败");
			}
		}if($remain_num < $parr['num']){
			$difnum = $parr['num'] - $remain_num;//少的扣商品库存

			$pw['c_ucode'] = $parr['ucode'];
			$pw['c_pcode'] = $goodinfo['c_pid'];

			$result = M('Product')->where($pw)->setDec('c_num',$difnum);

			if(!$result){
				$db->rollback();
				return Message(1003,"修改商品库存失败");
			}
		}		
		
		/*修改邮费*/
		$fpw['c_ucode'] = $parr['ucode'];
		$fpw['c_pcode'] = $goodinfo['c_pid'];
		$freepw['c_freeprice'] = $parr['freeprice'];
		$result = M('Product')->where($fpw)->save($freepw);
		if($result<0){
			$db->rollback();
			return Message(1006,"修改商品邮费失败");
		}
		
		$db->commit();
		return Message(0,"保存成功");
	}

	/**
	 * 宝箱热气球商品撤回
	 * @param goodid,ucode
	 */
	function GooddDelete($parr){
		$w['c_id'] = $parr['goodid'];
		$w['c_acode'] = $parr['ucode'];

		$goodinfo = M('A_actprize')->where($w)->find();

		if(!$goodinfo){
			return Message(1001,"没有查询到相关活动商品信息");
		}

		$db = M('');
		$db -> startTrans();

		//修改活动商品信息
		$save_data['c_delete'] = 1;
		
		$result = M('A_actprize')->where($w)->save($save_data);

		if(!$result){
			$db->rollback();
			return Message(1002,"修改活动商品信息失败");
		}

		//修改商品库存
		$remain_num = $goodinfo['c_num'];
		
		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_pcode'] = $goodinfo['c_pid'];

		$result = M('Product')->where($pw)->setInc('c_num',$remain_num);

		if(!$result){
			$db->rollback();
			return Message(1003,"修改商品库存失败");
		}
		
		
		$db->commit();
		return Message(0,"撤回成功");
	}

	/**
	 * 获取宝箱热气球卡券奖项详情
	 * @param pageindex,pagesize,joinaid,ucode
	 */
	function GetCouponDetails($parr)
	{
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$w['c_acode'] = $parr['ucode'];
		$w['c_type'] = 3;
		$w['c_joinaid'] = $parr['joinaid'];
		$w['c_status'] = 1;
		$w['c_delete'] = 2;

		$couponlist = M('A_actprize')->where($w)->limit($countPage, $pageSize)->select();

		$count = M('A_actprize')->where($w)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$couponlist) {
			$couponlist = array();
			$data = Page($pageIndex, $pageCount, $count, $couponlist);
			return MessageInfo(0, '查询成功', $data);
		}		

		foreach ($couponlist as $key => $value) {
			$w1['c_id'] = $value['c_pid'];
			//M('A_actcard')->where($w1)->getField('c_type');
			$field = 'c_type,c_pcodearr,c_sign,c_actnum';
			$actcard = M('A_actcard')->field($field)->where($w1)->find();					
			$couponlist[$key]['coupontype'] = $actcard['c_type'];
			$couponlist[$key]['pcodearr'] = $actcard['c_pcodearr'];
			$couponlist[$key]['sign'] = $actcard['c_sign'];
			$couponlist[$key]['cactnum'] = $actcard['c_actnum'];
			$couponlist[$key]['starttime'] = date("Y.m.d",strtotime($value['c_starttime']));
			$couponlist[$key]['endtime'] = date("Y.m.d",strtotime($value['c_endtime']));			
		}

		$data = Page($pageIndex, $pageCount, $count, $couponlist);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 宝箱热气球卡券编辑提交
	 * @param couponid,ucode,num
	 */
	function CouponEditSubmit($parr){
		$w['c_id'] = $parr['couponid'];
		$w['c_acode'] = $parr['ucode'];

		$couponinfo = M('A_actprize')->where($w)->find();

		if(!$couponinfo){
			return Message(1001,"没有查询到相关活动卡券信息");
		}

		$db = M('');
		$db -> startTrans();

		//修改活动卡券信息
		$save_data['c_totalnum'] = $parr['num'];
		$save_data['c_num'] = $parr['num'];
		
		$result = M('A_actprize')->where($w)->save($save_data);

		if(!$result){
			$db->rollback();
			return Message(1002,"修改活动卡券信息失败");
		}

		//修改活动卡券库存
		$remain_num = $couponinfo['c_num'];
		if($remain_num > $parr['num']){
			$difnum = $remain_num - $parr['num'];//多余需退回活动卡券的活动库存

			$pw['c_ucode'] = $parr['ucode'];
			$pw['c_id'] = $couponinfo['c_pid'];

			$result = M('A_actcard')->where($pw)->setInc('c_actnum',$difnum);

			if(!$result){
				$db->rollback();
				return Message(1003,"修改卡券活动库存失败");
			}
		}else if($remain_num < $parr['num']){
			$difnum = $parr['num'] - $remain_num;//少的扣活动卡券的活动库存

			$pw['c_ucode'] = $parr['ucode'];
			$pw['c_id'] = $couponinfo['c_pid'];

			$result = M('A_actcard')->where($pw)->setDec('c_actnum',$difnum);

			if(!$result){
				$db->rollback();
				return Message(1003,"修改活动卡券库存失败");
			}
		}
		
		$db->commit();
		return Message(0,"保存成功");
	}

	/**
	 * 宝箱热气球卡券撤回
	 * @param couponid,ucode
	 */
	function CoupondDelete($parr){
		$w['c_id'] = $parr['couponid'];
		$w['c_acode'] = $parr['ucode'];

		$couponinfo = M('A_actprize')->where($w)->find();

		if(!$couponinfo){
			return Message(1001,"没有查询到相关活动卡券信息");
		}

		$db = M('');
		$db -> startTrans();

		//修改活动卡券信息
		$save_data['c_delete'] = 1;
		
		$result = M('A_actprize')->where($w)->save($save_data);

		if(!$result){
			$db->rollback();
			return Message(1002,"修改活动卡券信息失败");
		}

		//修改卡券库存
		$remain_num = $couponinfo['c_num'];
		
		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_id'] = $couponinfo['c_pid'];

		$result = M('A_actcard')->where($pw)->setInc('c_num',$remain_num);

		if(!$result){
			$db->rollback();
			return Message(1003,"修改卡券库存失败");
		}		
		
		$db->commit();
		return Message(0,"撤回成功");
	}

	/**
	 * 获取宝箱热气球红包奖项详情
	 * @param pageindex,pagesize,joinaid,ucode
	 */
	function GetRedsDetails($parr)
	{	
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$w['c_acode'] = $parr['ucode'];
		$w['c_joinaid'] = $parr['joinaid'];
		$w['c_status'] = 1;
		$w['c_delete'] = 2;

		$redslist = M('A_redprize')->where($w)->limit($countPage, $pageSize)->select();

		$count = M('A_redprize')->where($w)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$redslist) {
			$redslist = array();
			$data = Page($pageIndex, $pageCount, $count, $redslist);
			return MessageInfo(0,"查询成功",$data);
		}
						
		foreach ($redslist as $key => $value) {			
			$wr['c_id'] = $value['c_pid'];
			$field = 'c_type,c_addtime,c_actnum';			
			$actred = M('A_actred')->field($field)->where($wr)->find();								
			$redslist[$key]['rtype'] = $actred['c_type'];
			$redslist[$key]['ractnum'] = $actred['c_actnum'];
			$redslist[$key]['starttime'] = date("Y.m.d H:i:s",strtotime($actred['c_addtime']));		
		}
		
		$data = Page($pageIndex, $pageCount, $count, $redslist);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 宝箱热气球红包编辑提交
	 * @param redid,ucode,num
	 */
	function RedEditSubmit($parr){
		$w['c_id'] = $parr['redid'];
		$w['c_acode'] = $parr['ucode'];

		$redinfo = M('A_redprize')->where($w)->find();

		if(!$redinfo){
			return Message(1001,"没有查询到相关活动红包信息");
		}

		$db = M('');
		$db -> startTrans();

		//修改活动红包信息
		$save_data['c_totalnum'] = $parr['num'];
		$save_data['c_num'] = $parr['num'];

		$result = M('A_redprize')->where($w)->save($save_data);

		if(!$result){
			$db->rollback();
			return Message(1002,"修改活动红包信息失败");
		}

		//修改活动红包库存
		$remain_num = $redinfo['c_num'];
		if($remain_num > $parr['num']){
			$difnum = $remain_num - $parr['num'];//多余需退回活动红包的活动库存

			$pw['c_ucode'] = $parr['ucode'];
			$pw['c_id'] = $redinfo['c_pid'];

			$result = M('A_actred')->where($pw)->setInc('c_actnum',$difnum);

			if(!$result){
				$db->rollback();
				return Message(1003,"修改红包活动库存失败");
			}
		}else if($remain_num < $parr['num']){
			$difnum = $parr['num'] - $remain_num;//少的扣活动红包的活动库存

			$pw['c_ucode'] = $parr['ucode'];
			$pw['c_id'] = $redinfo['c_pid'];

			$result = M('A_actred')->where($pw)->setDec('c_actnum',$difnum);

			if(!$result){
				$db->rollback();
				return Message(1003,"修改活动红包库存失败");
			}
		}
		
		$db->commit();
		return Message(0,"保存成功");
	}

	/**
	 * 宝箱热气球红包撤回
	 * @param redid,ucode
	 */
	function RedDelete($parr){
		$w['c_id'] = $parr['redid'];
		$w['c_acode'] = $parr['ucode'];

		$redinfo = M('A_redprize')->where($w)->find();
		if(!$redinfo){
			return Message(1001,"没有查询到相关活动红包信息");
		}
		$result = IGD('Index','Newact')->GetShopActivity(1,$redinfo['c_joinaid']);
		if ($result['code'] != 0) {
			return Message(3000,'活动信息不存在！');
		}
		$activitydata = $result['data'];
		if ($activitydata['c_activitytype'] == 22) {
			$remark = '撤回宝箱红包成功，系统已将剩余金额自动返回余额';
		} else if ($activitydata['c_activitytype'] == 23) {
			$remark = '撤回热气球红包成功，系统已将剩余金额自动返回余额';
		}

		$rparr['rid'] = $redinfo['c_pid'];
		$rparr['ucode'] = $redinfo['c_acode'];
		$rparr['desc'] = $remark;
		$result = IGD('Red','Newact')->RecallRed($rparr);
		if ($result['code'] != 0) {
			return $result;
		}

		// $db = M('');
		// $db -> startTrans();

		// //修改活动红包信息
		// $save_data['c_delete'] = 1;
		
		// $result = M('A_redprize')->where($w)->save($save_data);

		// if(!$result){
		// 	$db->rollback();
		// 	return Message(1002,"修改活动红包信息失败");
		// }

		// //修改活动红包库存
		// $remain_num = $redinfo['c_num'];
		
		// $pw['c_ucode'] = $parr['ucode'];
		// $pw['c_id'] = $redinfo['c_pid'];

		// $result = M('A_actred')->where($pw)->setInc('c_num',$remain_num);

		// if(!$result){
		// 	$db->rollback();
		// 	return Message(1003,"修改活动红包库存失败");
		// }		
		
		// $db->commit();
		return Message(0,"撤回成功");
	}

	/**
	 *  创建宝箱热气球商品 选择产品
	 *  @param joinaid,ucode,pagesize,pageindex
	 */
	function MyGoodsList($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        //查询是否有产品正在参加该活动
        $actpw['c_joinaid'] = $parr['joinaid'];
        $actpw['c_acode'] = $parr['ucode'];
        $actpw['c_type'] = 4;
        // $actpw['c_status'] = 1;
        $actpw['c_delete'] = 2;
        
        $pcode_arr = M('A_actprize')->where($actpw)->field('c_pid')->select();

        $pcode_str = arr_to_str($pcode_arr);
        if (!empty($pcode_str)) {
        	$w['c_pcode'] = array('not in',$pcode_str);
        }
		$w['c_ucode'] = $parr['ucode'];
		$w['c_isagent'] = 0;
		$w['c_isdele'] = 1;
		$w['c_ishow'] = 1;
		$w['c_num'] = array('GT',0);

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
        	$list[$key]['pimg'] = $value['c_pimg'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 宝箱热气球商品添加提交
	 * @param ucode,joinaid,pinfo(pcode,name,pimg,price,num,actnum,freeprice,starttime,endtime)
	 */
	function GoodsAddSubmit($parr){
		$ucode = $parr['ucode'];
		$pinfo = $parr['pinfo'];
		$joinaid = $parr['joinaid'];

		$result = IGD('Index','Newact')->GetShopActivity(1,$joinaid);
		if ($result['code'] != 0) {
			return Message(3000,'活动信息不存在！');
		}
		$activitydata = $result['data'];
		if ($activitydata['c_activitytype'] == 22) {
			$remark = '宝箱';
		} else if ($activitydata['c_activitytype'] == 23) {
			$remark = '热气球';
		}

		//查询商家活动信息
		$actw['c_id'] = $joinaid;

		$actinfo = M('Actjoin_moneylog')->where($actw)->find();

		if(!$actinfo){
			return Message(1001,"没有查询到相关活动信息");
		}

		$endtime = strtotime($actinfo['c_endtime']);

		//检验数据并添加数据
		$db = M('');
		$db -> startTrans();
		foreach ($pinfo as $key => $value) {
			
			//防止超出活动时间
			if(strtotime($value['c_endtime']) > $endtime){
				$pinfo[$key]['endtime'] = $actinfo['c_endtime'];
			}

			//查询产品信息
			$pw['c_ucode'] = $ucode;
			$pw['c_pcode'] = $value['pcode'];
			$pw['c_num'] = array('EGT',$value['actnum']);
			$produce = M('Product')->where($pw)->find();
			if (!$produce) {
				$db->rollback();
				return Message(3000,'产品库存不足');
			}

			//添加数据
			$add_data['c_joinaid'] = $joinaid;
			$add_data['c_name'] = $value['name'];
			$add_data['c_type'] = 4;
			$add_data['c_pid'] = $value['pcode'];
			$add_data['c_img'] = $value['pimg'];
			$add_data['c_acode'] = $ucode;
			$add_data['c_value'] = $value['freeprice'];
			$add_data['c_maxvalue'] = $produce['c_price'];
			$add_data['c_totalnum'] = $value['actnum'];
			$add_data['c_num'] = $value['actnum'];
			$add_data['c_status'] = 3;
			$add_data['c_remark'] = $remark;
			$add_data['c_starttime'] = $value['starttime'];
			$add_data['c_endtime'] = $value['endtime'];
			$add_data['c_addtime'] = gdtime();
			$add_data['c_updatetime'] = gdtime();
			
			$result = M('A_actprize')->add($add_data);

			if(!$result){
				$db->rollback();
				return Message(1002,"添加失败");break;
			}

			//扣除商品库存
			$result = M('Product')->where($pw)->setDec('c_num',$value['actnum']);
			if(!$result){
				$db->rollback();
				return Message(1003,"添加失败");break;
			}
		}

		$db->commit();
		return Message(0,"添加成功"); 
	}

	/**
	 *  创建宝箱热气球卡券 选择卡券
	 *  @param joinaid,ucode,pagesize,pageindex
	 */
	function MyCoupondList($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        //查询是否有卡券正在参加该活动
        $actpw['c_joinaid'] = $parr['joinaid'];
        $actpw['c_acode'] = $parr['ucode'];
        $actpw['c_type'] = 3;
        $actpw['c_status'] = 1;
        $actpw['c_delete'] = 2;
        
        $pid_arr = M('A_actprize')->where($actpw)->field('c_pid')->select();

        $pid_str = arr_to_str($pid_arr);
        if (!empty($pid_str)) {
        	$w['c_id'] = array('not in',$pid_str);
        }
		$w['c_ucode'] = $parr['ucode'];
		//$w['c_sign'] = 1;
		$w['c_rule'] = 1;
		//$w['c_status'] = 1;
		$w['c_delete'] = 2;
		$w['c_actnum'] = array('GT',0);

		$list = M('A_actcard')->where($w)->limit($countPage, $pageSize)->order('c_id desc')->select();
		
		$count = M('A_actcard')->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['c_img'] = GetHost().'/'.$value['c_img'];
        }
	
        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 宝箱热气球卡券添加提交
	 * @param ucode,joinaid,coupondinfo(id,name,actnum,type,money,limit_money,starttime,endtime)
	 */
	function CoupondAddSubmit($parr){
		$ucode = $parr['ucode'];
		$coupondinfo = $parr['coupondinfo'];
		$joinaid = $parr['joinaid'];

		$result = IGD('Index','Newact')->GetShopActivity(1,$joinaid);
		if ($result['code'] != 0) {
			return Message(3000,'活动信息不存在！');
		}
		$activitydata = $result['data'];
		if ($activitydata['c_activitytype'] == 22) {
			$remark = '宝箱';
		} else if ($activitydata['c_activitytype'] == 23) {
			$remark = '热气球';
		}

		//检验数据并添加数据
		$db = M('');
		$db -> startTrans();
		
		foreach ($coupondinfo as $key => $value) {
			//添加数据
			$add_data['c_joinaid'] = $joinaid;
			$add_data['c_name'] = $value['name'];
			$add_data['c_type'] = 3;
			$add_data['c_pid'] = $value['id'];
			$add_data['c_img'] = $value['pimg'];
			$add_data['c_acode'] = $ucode;
			$add_data['c_value'] = $value['money'];
			$add_data['c_maxvalue'] = $value['limit_money'];
			$add_data['c_totalnum'] = $value['actnum'];
			$add_data['c_num'] = $value['actnum'];
			$add_data['c_status'] = 1;
			$add_data['c_marks'] = $value['type'];
			$add_data['c_remark'] = $remark;
			$add_data['c_starttime'] = $value['starttime'];
			$add_data['c_endtime'] = $value['endtime'];
			$add_data['c_addtime'] = gdtime();
			$add_data['c_updatetime'] = gdtime();
			
			$result = M('A_actprize')->add($add_data);

			if(!$result){
				$db->rollback();
				return Message(1002,"添加失败");break;
			}

			//扣除卡券活动库存
			$pw['c_ucode'] = $ucode;
			$pw['c_id'] = $value['id'];
			
			$result = M('A_actcard')->where($pw)->setDec('c_actnum',$value['actnum']);

			if(!$result){
				$db->rollback();
				return Message(1003,"添加失败");break;
			}
		}

		$db->commit();
		return Message(0,"添加成功"); 
	}


	/**
	 *  创建宝箱热气球红包 选择红包
	 *  @param joinaid,ucode,pagesize,pageindex
	 */
	function MyRedList($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        //查询是否有红包正在参加该活动
        $actpw['c_joinaid'] = $parr['joinaid'];
        $actpw['c_acode'] = $parr['ucode'];
        $actpw['c_status'] = 1;
        $actpw['c_delete'] = 2;
        
        $pid_arr = M('A_redprize')->where($actpw)->field('c_pid')->select();

        $pid_str = arr_to_str($pid_arr);
        if (!empty($pid_str)) {
        	$w['c_id'] = array('not in',$pid_str);
        }
		$w['c_ucode'] = $parr['ucode'];
		$w['c_status'] = 1;
		$w['c_actnum'] = array('GT',0);

		$list = M('A_actred')->where($w)->limit($countPage, $pageSize)->order('c_id desc')->select();

		$count = M('A_actred')->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 宝箱热气球红包添加提交
	 * @param ucode,joinaid,redinfo(id,name,actnum,type,money,remain_money)
	 */
	function RedAddSubmit($parr){
		$ucode = $parr['ucode'];
		$redinfo = $parr['redinfo'];
		$joinaid = $parr['joinaid'];

		$result = IGD('Index','Newact')->GetShopActivity(1,$joinaid);
		if ($result['code'] != 0) {
			return Message(3000,'活动信息不存在！');
		}
		$activitydata = $result['data'];
		if ($activitydata['c_activitytype'] == 22) {
			$remark = '宝箱';
		} else if ($activitydata['c_activitytype'] == 23) {
			$remark = '热气球';
		}

		//检验数据并添加数据
		$db = M('');
		$db -> startTrans();

		foreach ($redinfo as $key => $value) {

			$redw['c_id'] = $value['id'];
			$redw['c_ucode'] = $ucode;
			$data = M('A_actred')->where($redw)->find();
			if(!$data){
				$db->rollback();
				return Message(3000,"红包已发放完或者已领取完");
			}

			if ($value['actnum'] > $data['c_actnum']) {
				$db->rollback();
	            return Message(3001, '发放数量不能大于可发放总数量');
	        }

			//修改红包状态
			$result = IGD('Red','Newact')->DecCouponCard($value['id'],2,$value['actnum']);
			if($result['code'] != 0){
				$db->rollback();
				return Message(1001,"扣除红包数量失败");
			}

			$fmoney = sprintf('%.2f',$data['c_money']*($value['actnum']/$data['c_totalnum']));

			//添加数据
			$add_data['c_joinaid'] = $joinaid;
			$add_data['c_name'] = $value['name'];
			$add_data['c_type'] = $value['type'];
			$add_data['c_pid'] = $value['id'];
			$add_data['c_acode'] = $ucode;
			$add_data['c_totalnum'] = $value['actnum'];
			$add_data['c_num'] = $value['actnum'];
			$add_data['c_status'] = 1;
			$add_data['c_marks'] = 3;
			$add_data['c_remark'] = $remark;
			$add_data['c_money'] = $fmoney;
			$add_data['c_remain_money'] = $fmoney;
			$add_data['c_addtime'] = gdtime();
			$add_data['c_updatetime'] = gdtime();
			
			$result = M('A_redprize')->add($add_data);
			if(!$result){
				$db->rollback();
				return Message(1002,"添加失败");
			}
			
		}

		$db->commit();
		return Message(0,"添加成功"); 
	}

	/**
	 * 宝箱热气球商品用户参与记录统计数据
	 * @param ucode,joinaid
	 */
	function GoodsLogTj($parr){
		$time = gdtime();

		$model = M();

        //统计待兑换与待领取数据
		$sql = "select count(case when c_status=0 then 1 else null end) as ddh,
		    count(case when c_status=2 then 1 else null end) as dlq		    
		    from t_a_start_log where c_acode='".$parr['ucode']."' and c_ptype=4 and c_joinaid='".$parr['joinaid']."'";
		$tjdata = $model->query($sql);

		

        //统计共发放与已领取数据
		$sql = "select sum(c_totalnum) as gff,
			sum(c_num) as wlq from t_a_actprize where c_acode='".$parr['ucode']."' and c_type=4 and c_joinaid='".$parr['joinaid']."'";		
		$tjnum = $model->query($sql);


		$data['gff'] = ($tjnum[0]['gff']>0)?$tjnum[0]['gff']:'0';//共发放
		$data['ddh'] = ($tjdata[0]['ddh']>0)?$tjdata[0]['ddh']:'0';//待兑换
		$data['dlq'] = ($tjdata[0]['dlq']>0)?$tjdata[0]['dlq']:'0';//待领取
		
		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 宝箱热气球商品用户参与记录
	 * @param ucode,joinaid,pagesize,pageindex
	 */
	function GoodsLog($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['c_acode'] = $parr['ucode'];
        $w['c_joinaid'] = $parr['joinaid'];
        $w['c_ptype'] = 4;
        $order = "atate desc,c_status asc,c_id desc";
        $field = '*,case when c_status=1 then 1 else 0 end as atate';
        $list = M('A_start_log')->where($w)->order($order)->limit($countPage, $pageSize)->field($field)->select();

        $count = M('A_start_log')->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (count($list) == 0) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
        	//查询商品信息
        	$pw['c_pcode'] = $value['c_pcode'];
        	$pw['c_ucode'] = $value['c_acode'];

        	$pinfo = M('Product')->where($pw)->field('c_name,c_pimg')->find();

        	$list[$key]['pname'] = $pinfo['c_name'];
        	$list[$key]['pimg'] = GetHost().'/'.$pinfo['c_pimg'];

        	//查询用户信息
        	$uw['c_ucode'] = $value['c_ucode'];

        	$uinfo = M('Users')->where($uw)->field('c_nickname,c_headimg')->find();

        	$list[$key]['nickname'] = $uinfo['c_nickname'];
        	$list[$key]['headimg'] = GetHost().'/'.$uinfo['c_headimg'];

        	//有效期
        	$list[$key]['times'] = date('Y-m-d',strtotime($value['c_addtime'])).'至'.date('Y-m-d',strtotime('+1 days',strtotime($value['c_addtime'])));
        }

		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 宝箱热气球卡券用户参与记录统计数据
	 * @param ucode,joinaid
	 */
	function CoupondLogTj($parr){
		$time = gdtime();

		$model = M();

        //统计共发放与已领取数据
		$sql = "select sum(c_totalnum) as gff,
			sum(c_num) as wlq from t_a_actprize where c_acode='".$parr['ucode']."' and c_type=3 and c_joinaid='".$parr['joinaid']."'";		
		$tjdata = $model->query($sql);

		//统计待使用与已过期
		$sql = "select sum(case when c_used_state=0 then 1 else null end) as dsy,
			sum(case when c_used_state=0 and c_endtime<'$time' then 1 else null end) as ygq from t_a_user_coupons where c_acode='".$parr['ucode']."' and c_type=3 and c_joinaid='".$parr['joinaid']."'";		
		$tjnum = $model->query($sql);

		$data['gff'] = ($tjdata[0]['gff']>0)?$tjdata[0]['gff']:'0';//共发放
		$data['dsy'] = ($tjnum[0]['dsy']>0)?$tjnum[0]['dsy']:'0';//待使用
		$data['wlq'] = ($tjdata[0]['wlq']>0)?$tjdata[0]['wlq']:'0';//未领取
		$data['ygq'] = ($tjnum[0]['ygq']>0)?$tjdata[0]['ygq']:'0';//已过期

		return MessageInfo(0, '查询成功', $data);
	}
 
	/**
	 * 宝箱热气球卡券用户参与记录
	 * @param ucode,joinaid
	 */
	function CoupondProLog($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['a.c_acode'] = $parr['ucode'];
        $w['a.c_joinaid'] = $parr['joinaid'];
        $w['a.c_ptype'] = 3;

        $field = "a.*";
        // $field = "a.*,b.c_name,b.c_type as coupond_type,b.c_money,b.c_limit_money,b.c_starttime,b.c_endtime,b.c_pcodearr,b.c_sign,b.c_ucode as shopucode,u.c_nickname";
        // $join = "left join t_a_actcard as b on a.c_pcode=b.c_id left join t_users as u on u.c_ucode=b.c_ucode";

        $list = M('A_start_log as a')->field($field)->where($w)->order("a.c_id desc")->limit($countPage, $pageSize)->select();

        $count = M('A_start_log as a')->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (count($list) == 0) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
        	//查询相关详情
        	$awh['c_id'] = $value['c_pcode'];
        	$airdata = M('A_actcard')->where($awh)->find();

        	$list[$key]['c_name'] = $airdata['c_name'];
        	$list[$key]['coupond_type'] = $airdata['c_type'];
        	$list[$key]['c_money'] = $airdata['c_money'];
        	$list[$key]['c_limit_money'] = $airdata['c_limit_money'];
        	$list[$key]['c_starttime'] = $airdata['c_starttime'];
        	$list[$key]['c_endtime'] = $airdata['c_endtime'];
        	$list[$key]['c_pcodearr'] = $airdata['c_pcodearr'];
        	$list[$key]['c_sign'] = $airdata['c_sign'];
        	$list[$key]['shopucode'] = $airdata['c_ucode'];

        	//查询用户信息
        	$uw['c_ucode'] = $airdata['c_ucode'];
        	$userinfo = M('Users')->where($uw)->field('c_nickname')->find();
        	$list[$key]['c_nickname'] = $userinfo['c_nickname'];

        	//有效期
        	$list[$key]['times'] = date('Y-m-d',strtotime($airdata['c_starttime'])).'-'.date('Y-m-d',strtotime($airdata['c_endtime']));
        	
        	$list[$key]['fadestate'] = 0;    //未过期
        	if (strtotime($airdata['c_endtime']) < time()) {
        		$list[$key]['fadestate'] = 1;    //已过期
        	}
        }

		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 宝箱热气球红包用户参与记录统计数据
	 * @param ucode,joinaid
	 */
	function RedLogTj($parr){
        //统计数据
		$sql = "select sum(c_money) as tfze,sum(c_remain_money) as syje
		    from t_a_redprize where c_acode='".$parr['ucode']."' and c_status=1 and c_joinaid='".$parr['joinaid']."'";
		$model = M();
		$tjdata = $model->query($sql);

		$data['tfze'] = $tjdata[0]['tfze'];//投放总额
		$data['syje'] = $tjdata[0]['syje'];//剩余金额

		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 宝箱热气球红包用户参与记录
	 * @param ucode,joinaid,month(格式：2017-05)
	 */
	function CoupondLog($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $day =$parr['day'];
        if(!empty($day)){   // 按日期刷选
            $start = $day." 00:00:00";
            $end = $day." 23:59:59";
            $w[] = array("c_addtime>='$start' and c_addtime<'$end'");
        }
        $w['c_acode'] = $parr['ucode'];
        $w['c_joinaid'] = $parr['joinaid'];
        $w['c_ptype'] = 2;

        $list = M('A_start_log')->where($w)->order("c_addtime desc")->limit($countPage, $pageSize)->select();
		
        $count = M('A_start_log')->where($w)->count();
        $pageCount = ceil($count / $pageSize);
        if (count($list) == 0) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
        	//查询用户信息
        	$uw['c_ucode'] = $value['c_ucode'];
        	$uinfo = M('Users')->where($uw)->field('c_nickname,c_headimg')->find();

        	$list[$key]['c_name'] = $uinfo['c_nickname'];
        	$list[$key]['headimg'] = GetHost().'/'.$uinfo['c_headimg'];
//        	$list[$key]['todays'] = date('d日',strtotime($value['c_addtime']));
//        	$list[$key]['desctime'] = date('H:i',strtotime($value['c_addtime']));
            $list[$key]['date'] =$this->exchangeTime($value['c_addtime'],1);
            $list[$key]['time'] =substr($value['c_addtime'], 11, 5);
        }

        $data = Page($pageIndex, $pageCount, $count, $list);		
		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 获取奖项详情
	 * @param pid
	 */
	function GetPrizeDetail($parr)
	{
		$w['a.c_id'] = $parr['pid'];
		$field = "a.*,p.c_pcode,p.c_num as kcnum,p.c_freeprice";
		$join = "inner join t_product as p on a.c_pid=p.c_pcode";
		$data = M('A_actprize as a')->field($field)->join($join)->where($w)->find();
		if (!$data) {
			return Message(3000,'记录不存在');
		}

		$data['c_img'] =  GetHost().'/'.$data['c_img'];
		return MessageInfo(0,'查询成功',$data);
	}
	
	/**
	 * 立即投放
	 * @param pid
	 */
	function ChangePrize($parr)
	{
		$w['c_id'] = $parr['pid'];
		$w['c_status'] = 4;
		$wsave['c_status'] = 1;
		$result = M('A_actprize')->where($w)->save($wsave);		
		if (!$result) {
			return Message(3000,'投放失败');
		}
		return Message(0,'投放成功');
	}	

    //时间判断
    public function exchangeTime($date,$flag=null){
        $today =date('Y-m-d');
        $newdate =substr($date,0,10);
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);
        $ttime = substr($date, 11, 5);


        $d1 = strtotime($newdate);
        $d2 = strtotime($today);
        $Days = round(($d2-$d1)/3600/24);
        if(empty($flag) || $flag==null){
            if($Days==0){
                return $ttime;
            }elseif($Days>0 && $Days<7){
                return $this->get_week($newdate);
            }else{
                return $year .'/'.$month .'/'.$day;
            }
        }elseif($flag || $flag==1){

            if($Days==0){
                return '今日 ';
            }elseif($Days>0 && $Days<7){
                return $this->get_week($newdate);
            }else{
                return $month .'月'.$day.'日';
            }
        }

    }


    public function   get_week($date){
        //强制转换日期格式
        $date_str=date('Y-m-d',strtotime($date));

        //封装成数组
        $arr=explode("-", $date_str);

        //参数赋值
        //年
        $year=$arr[0];

        //月，输出2位整型，不够2位右对齐
        $month=sprintf('%02d',$arr[1]);

        //日，输出2位整型，不够2位右对齐
        $day=sprintf('%02d',$arr[2]);

        //时分秒默认赋值为0；
        $hour = $minute = $second = 0;

        //转换成时间戳
        $strap = mktime($hour,$minute,$second,$month,$day,$year);

        //获取数字型星期几
        $number_wk=date("w",$strap);

        //自定义星期数组
        $weekArr=array("周日","周一","周二","周三","周四","周五","周六");

        //获取数字对应的星期
        return $weekArr[$number_wk];
    }

	/**
	 * 检测活动改变状态
	 * @param joinaid
	 */
	function CheckUserActivity($parr)
	{
		$where['c_id'] = $parr['joinaid'];
		$list = M('Actjoin_moneylog')->where($where)->select();
		foreach ($list as $key => $value) {
			$psign = 0;
			//查询活动奖项是否为空
			$pw1['c_joinaid'] = $value['c_id'];
			$pw1['c_status'] = 1;
			$pw1['c_delete'] = 2;
			$pw1['c_num'] = array('GT',0);
			$pw1['c_type'] = array('NEQ',1);
			$p1 = M('A_actprize')->where($pw1)->find();
			if ($p1) {
				$psign = 1;
			}

			$pw2['c_joinaid'] = $value['c_id'];
			$pw2['c_status'] = 1;
			$pw2['c_num'] = array('GT',0);
			$pw2['c_delete'] = 2;
			$p2 = M('A_redprize')->where($pw2)->find();
			if ($p2) {
				$psign = 1;
			}
			
			$save['c_ishot'] = $psign;
			$swhere['c_id'] = $value['c_id'];
			$result =  M('Actjoin_moneylog')->where($swhere)->save($save);
			
		}
		return Message(0,'操作成功');
	}


	/**
	 * 定时器检测活动改变热门状态
	 * @param string $value [description]
	 */
	function CheckActivity()
	{
		$where[] = array("c_activitytype=22 or c_activitytype=23");
		$where[] = array('c_acode is not null');
		$list = M('Actjoin_moneylog')->where($where)->limit(20)->order('rand()')->select();
		foreach ($list as $key => $value) {
			$psign = 0;
			//查询活动奖项是否为空
			$pw1['c_joinaid'] = $value['c_id'];
			$pw1['c_status'] = 1;
			$pw1['c_delete'] = 2;
			$pw1['c_num'] = array('GT',0);
			$pw1['c_type'] = array('NEQ',1);
			$p1 = M('A_actprize')->where($pw1)->find();
			if ($p1) {
				$psign = 1;
			}

			$pw2['c_joinaid'] = $value['c_id'];
			$pw2['c_status'] = 1;
			$pw2['c_num'] = array('GT',0);
			$pw2['c_delete'] = 2;
			$p2 = M('A_redprize')->where($pw2)->find();
			if ($p2) {
				$psign = 1;
			}
			
			$save['c_ishot'] = $psign;
			$swhere['c_id'] = $value['c_id'];
			$result =  M('Actjoin_moneylog')->where($swhere)->save($save);
			
		}

		return Message(0,'操作成功');

	}
}
	
?>