<?php
/**
 * 广告位相关接口
 */
class AdvertNewact {
	/**
	 * 推广位首页头部
	 * @param ucode
	 */
	public function AdvertHead($parr){
		$w['a.c_ucode'] = $parr['ucode'];
		$w['b.c_status'] = 2;

		$join = "left join t_a_advert as b on a.c_id=b.c_adid";
		$advertnum = M('A_advert_card as a')->join($join)->where($w)->count();

		$data['advertnum'] = $advertnum;

		$w1['c_ucode'] = $parr['ucode'];
		$w1['c_rule'] = 5;

		$data['tjchance'] = M('Activity_lotterynum')->where($w1)->getField('c_num');
		if(!$data['tjchance']){
			$data['tjchance'] = 0;
		}
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 创建广告牌
	 * @param ucode,pid,num,ctype
	 */
	public function SetupCard($parr){
		$w['c_id'] = $parr['pid'];

		$couponinfo = M('A_actcard')->where($w)->find();
		if(!$couponinfo){
			return Message(1001,"该卡券信息不存在！");
		}

		$num = $parr['num'];
		if($couponinfo['c_actnum'] < $num){
			return Message(1002,"该卡券数量不够！");
		}

		$db = M('');
		$db->startTrans();

		$adata['c_ucode'] = $parr['ucode'];
		$adata['c_pid'] = $parr['pid'];
		$adata['c_name'] = $couponinfo['c_name'];
		$adata['c_img'] = $couponinfo['c_img'];
		$adata['c_totalnum'] = $num;
		$adata['c_num'] = $num;
		$adata['c_scannum'] = 0;
		$adata['c_type'] = $parr['ctype'];
		$adata['c_duetime'] = $couponinfo['c_endtime'];
		$adata['c_addtime'] = gdtime();

		$result = M('A_advert_card')->add($adata);

		if(!$result){
			$db->rollback();
			return Message(1003,"创建失败");
		}

		$result = IGD('Coupon','Newact')->DecCouponCard($parr['pid'],2,$num);

		if($result['code'] != 0){
			$db->rollback();
			return Message(1004,"扣除卡劵数量失败");
		}

		$db->commit();
		return Message(0,"创建成功");
	}

	/**
	 * 广告牌列表
	 * @param ucode,pageindex,gettype （1-包括不可投放的 2-可投放的）
	 */
	public function CardList($parr){
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        if($parr['gettype'] == 2){
        	$w['a.c_status'] = 2;
        	$w['a.c_duetime'] = array('GT',gdtime());
        	$w['a.c_num'] = array('GT',0);
        }

        $w['a.c_ucode'] = $parr['ucode'];
        $w['a.c_delete'] = 2;
        $w['b.c_delete'] = 2;
        $field = 'a.c_id,a.c_ucode,a.c_pid,a.c_totalnum,a.c_num,a.c_status,b.c_type,b.c_money,b.c_limit_money,b.c_starttime,b.c_endtime,b.c_pcodearr';
        $join = 'inner join t_a_actcard as b on b.c_id=a.c_pid';
        $order = 'a.c_status desc,a.c_num desc,a.c_id desc';

        $cardlist = M('A_advert_card as a')->field($field)->join($join)->where($w)->order($order)->limit($countPage, $pagesize)->select();

        $count = M('A_advert_card as a')->field($field)->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if(!$cardlist){
        	$cardlist = array();
        	$data = Page($pageIndex, $pageCount, $count, $cardlist);
        	return MessageInfo(0,'查询成功',$data);
        }

        foreach ($cardlist as $key => $value) {
        	//投放次数
        	$tfwhere['c_adid'] = $value['c_id'];
        	$tfnum = M('A_advert')->where($tfwhere)->count();
        	$cardlist[$key]['putin_num'] = $tfnum;
        	if($value['c_num'] == 0){
        		$cardlist[$key]['lootall'] = 1;//已抢光
        	}else{
        		$cardlist[$key]['lootall'] = 0;
        	}

        	$cardlist[$key]['c_starttime'] = str_replace('-', '.', $value['c_starttime']);
            $cardlist[$key]['c_endtime'] = str_replace('-', '.', $value['c_endtime']);
        	$cardlist[$key]['c_limit_money'] = round($value['c_limit_money']);
            if ($value['c_type'] == 1) {
            	$cardlist[$key]['c_money'] = round($value['c_money']);
            } else if ($value['c_type'] == 2) {
            	$cardlist[$key]['c_money'] = round($value['c_money'],1);
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $cardlist);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 广告牌详情 广告牌信息
	 * @param cardid
	 */
	public function CardInfo($parr){
		//查询推广信息
		$cardid = $parr['cardid'];

		$w['a.c_id'] = $cardid;
		$w['b.c_delete'] = 2;
		$w['a.c_status'] = 2;

		$field ="a.c_totalnum,a.c_num,a.c_ucode,b.c_type,b.c_money,b.c_limit_money,b.c_starttime,b.c_endtime,b.c_pcodearr,b.c_sign";
		$join = 'inner join t_a_actcard as b on b.c_id=a.c_pid';

		$cardlist = M('A_advert_card as a')->field($field)->join($join)->where($w)->find();

		if(!$cardlist){
			return Message(1001,'该推广信息不存在');
		}

		$cardlist['c_limit_money'] = round($cardlist['c_limit_money']);       
        if ($cardlist['c_type'] == 1) {
            $cardlist['c_money'] = round($cardlist['c_money']);
        } else if ($cardlist['c_type'] == 2) {
            $cardlist['c_money'] = round($cardlist['c_money'],1);
        }

		return MessageInfo(0,"查询成功",$cardlist);
	}

	/**
	 * 广告牌详情 广告牌投放记录
	 * @param cardid,pageindex
	 */
	public function CardGetList($parr){		
		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$w['a.c_adid'] = $parr['cardid'];
		$w[] =array("a.c_status=2");

		$field ="a.*,u.c_nickname,u.c_headimg";
		$join = 'inner join t_users as u on a.c_ucode=u.c_ucode'
				.' left join t_a_advert_card as c on a.c_adid=c.c_id';

		$order = 'a.c_status desc,a.c_id desc';
		$cardlist = M('A_advert as a')->field($field)->join($join)->where($w)->limit($countPage, $pagesize)->order($order)->select();

		$count = M('A_advert as a')->field($field)->join($join)->where($w)->count();
		$pageCount = ceil($count / $pageSize);

		if(!$cardlist){
			$cardlist = array();
        	$data = Page($pageIndex, $pageCount, $count, $cardlist);
        	return MessageInfo(0,'查询成功',$data);
		}

		foreach ($cardlist as $key => $value) {
			$cardlist[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];

			//统计领取数量
			$getwhere['c_adid'] = $value['c_adid']; 
			$getwhere['c_vid'] = $value['c_id'];

			$getnum = M('A_adcard_getlog')->where($getwhere)->count();

			$cardlist[$key]['getnum'] = ($getnum>0)?$getnum:'0';

			//浏览记录统计
			$sql = "select sum(c_num) as seenum from t_a_adcard_seelog where c_adid=".$value['c_adid']." and c_vid=".$value['c_id'];
			$seedata = M('')->query($sql);
			$cardlist[$key]['seenum'] = ($seedata[0]['seenum'] > 0)?$seedata[0]['seenum']:'0';
		}

		$data = Page($pageIndex, $pageCount, $count, $cardlist);
		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 广告牌详情 使用范围
	 * @param cardid
	 */
	public function CardUsePro($parr){
		$w['a.c_id'] = $parr['cardid'];
		$field ="b.c_pcodearr";
		$join = 'inner join t_a_actcard as b on b.c_id=a.c_pid';

		$cardlist = M('A_advert_card as a')->field($field)->join($join)->where($w)->find();

		if(!$cardlist){
			return Message(1001,'该推广信息不存在');
		}

		if(empty($cardlist['c_pcodearr'])){
			return Message(0,'适应全部商品');
		}

		$pcode_str = str_replace("|",',',$cardlist['c_pcodearr']);

		$pw['c_isdele'] = 1; 
		$pw['c_isagent'] = 0; 
		if (!empty($pcode_str)) {
			$pw['c_pcode'] = array('in',$pcode_str);
		}
	
		$plist = M('Product')->field('c_ucode,c_pcode,c_name,c_pimg,c_price')->where($pw)->select();

		if(!$plist){
			$plist = array();
        	return MessageInfo(0,'查询成功',$plist);
		}

		foreach ($plist as $key => $value) {
			$plist[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];
		}

		return MessageInfo(0, '查询成功', $plist);
	}

	/**
	 * 广告牌详情 广告撤回
	 * @param cardid(是广告位id)
	 */
	public function AdvertRecall($parr){
		//查询该广告位是否存在		
		$w['c_id'] = $parr['cardid'];
		$w['c_status'] = 2;
		$cardinfo = M('A_advert')->where($w)->find();
		if (!$cardinfo) {
			return Message(3000,'推广物料不存在或已撤回');
		}

		$db = M('');
		$db->startTrans();

		//撤回广告牌
		$sdata1['c_status'] = 1;
		$w1['c_id'] = $cardinfo['c_id'];
		$result = M('A_advert')->where($w1)->save($sdata1);
		if(!$result){
			$db->rollback();
			return Message(1002,'删除失败');
		}

		//退回商家被投放广告位数量
		$ws['c_ucode'] = $cardinfo['c_ucode'];
		$result = M('Users')->where($ws)->setDec('c_advertnum',1);
		if (!$result) {
			$db->rollback();
			return Message(3001,'操作失败');
		}

	    $db->commit();
		return Message(0,'操作成功！');
	}

	/**
	 * 广告牌详情 广告牌删除
	 * @param cardid
	 */
	public function CardDel($parr){
		$cardid = $parr['cardid'];

		$w['c_id'] = $cardid;
		$w['c_ucode'] = $parr['ucode'];
		$w['c_delete'] = 2;
		$cardinfo = M('A_advert_card')->where($w)->find();
		if (!$cardinfo) {
			return Message(3000,'推广物料不存在或已删除');
		}

		$db = M('');
		$db->startTrans();
		//修改广告牌的删除状态
		$sdata['c_delete'] = 1;
		$result = M('A_advert_card')->where($w)->save($sdata);
		if(!$result){
			$db->rollback();
			return Message(1001,'删除失败');
		}

		$w1['c_adid'] = $cardid;
		$w1['c_status'] = 2;
		$cdinfo = M('A_advert')->where($w1)->select();
		foreach ($cdinfo as $key => $value) {
			//撤回所有已经投放出去的广告牌
			$sdata1['c_status'] = 1;
			$w1['c_id'] = $value['c_id'];
			$result = M('A_advert')->where($w1)->save($sdata1);
			if(!$result){
				$db->rollback();
				return Message(1002,'删除失败');
			}

			//退回商家被投放广告位数量
			$ws['c_ucode'] = $value['c_ucode'];
			$result = M('Users')->where($ws)->setDec('c_advertnum',1);
			if (!$result) {
				$db->rollback();
				return Message(3001,'操作失败');
			}
		}
		
		if($cardinfo['c_num'] > 0){
			$where['c_ucode'] = $parr['ucode'];
			$where['c_id'] = $cardinfo['c_pid'];
			$result = M('A_actcard')->where($where)->setInc('c_actnum',$cardinfo['c_num']);
			if(!$result){
				$db->rollback();
				return Message(1003,'回收数量失败');
			}
		}

		$db->commit();
		return Message(0,'操作成功');
	}

	/**
	 * 删除卡劵同步删除推广位,推广物料
	 * @param ucode,cid
	 */
	public function DeleteAllAdcert($parr)
	{
		$dw['c_ucode'] = $parr['ucode'];
		$dw['c_pid'] = $parr['cid'];
		$dw['c_type'] = 1;
		$dw['c_delete'] = 2;
		$list = M('A_advert_card')->where($dw)->select();
		foreach ($list as $k => $v) {
			//修改广告牌的删除状态
			$sdata['c_delete'] = 1;
			$dw['c_id'] = $v['c_id'];
			$result = M('A_advert_card')->where($dw)->save($sdata);
			if(!$result){
				return Message(1001,'删除失败');
			}

			$w1['c_adid'] = $v['c_id'];
			$w1['c_status'] = 2;
			$cdinfo = M('A_advert')->where($w1)->select();
			foreach ($cdinfo as $key => $value) {
				//撤回所有已经投放出去的广告牌
				$sdata1['c_status'] = 1;
				$w1['c_id'] = $value['c_id'];
				$result = M('A_advert')->where($w1)->save($sdata1);
				if(!$result){
					return Message(1002,'删除失败');
				}

				//退回商家被投放广告位数量
				$ws['c_ucode'] = $value['c_ucode'];
				$result = M('Users')->where($ws)->setDec('c_advertnum',1);
				if (!$result) {
					return Message(3001,'操作失败');
				}
			}
		}

		return Message(0,'操作成功');
	}

	/**
	 * 广告位数量列表
	 * @param province,city,shoptrade,condition(搜索条件)
	 */
	public function AdvertSite($parr){
		$province = $parr['province'];
		$city = $parr['city'];
		$condition = $parr['condition'];
		$shoptrade = $parr['shoptrade'];

		if (empty($parr['pageindex'])){
		    $pageIndex = 1;
		}else{
		    $pageIndex = $parr['pageindex'];
		}
		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		//判断是否输入地区
		$flag = 0;
		if(!empty($province) && !empty($city)){
			$flag = 1;
			$param['province'] = $province;
			$param['city'] = $city;
			$result = IGD('Circle','Trade')->Getcirclecode($param);
			if($result['code'] != 0){
			    $flag = 2;
			}
			$provincecode = $result['data']['provincecode'];
			$citycode = $result['data']['citycode'];
		}

		if($flag == 1){
			$shopw['c_provincecode'] = $provincecode;
			$shopw['c_citycode'] = $citycode;

			$ucodes = M('Circle_shop')->field('c_ucode')->where($shopw)->select();

			if(count($ucodes) == 0){
			    $arr = array();
			    $data = Page(1, 0, 0, $arr);
			    return MessageInfo(0, "查询成功", $data);
			}

			$ucode_str = arr_to_str($ucodes);

			$where['a.c_ucode'] = array('in',$ucode_str);
		}

		//判断是否行业id
		if(!empty($shoptrade)){
			$where['a.c_shoptrade'] = $shoptrade;
		}

		//判断是否输入条件
		if(!empty($condition)){
			$where['a.c_nickname'] = array('like', "%{$condition}%");
		}

		//判断商家是否还拥有广告位
		$nwhere['c_type'] = 1;
		$limit_num = M('A_advert_set')->where($nwhere)->getField('c_num');
		$nwhere1['c_type'] = 2;
		$limit_num1 = M('A_advert_set')->where($nwhere1)->getField('c_num');

		$total_num = $limit_num + $limit_num1;

		$where['a.c_advertnum'] = array('elt',$total_num);
		$where['a.c_shop'] = 1;

		$field = "a.c_ucode,a.c_nickname,a.c_headimg,a.c_advertnum,b.c_attention,b.c_pv,a.c_id";
		$join = "left join t_users_date as b on a.c_ucode=b.c_ucode";
		$order = "a.c_advertnum asc";
		 
		$list = M('Users as a')->where($where)->field($field)->join($join)->order($order)->limit($countPage, $pageSize)->select();

		$count = M('Users as a')->where($where)->join($join)->count();
		$pageCount = ceil($count / $pageSize);

		if (!$list) {
		    $list = array();
		    $data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, "查询成功", $data);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
			$list[$key]['num'] = $total_num - $value['c_advertnum'];
			$list[$key]['c_pv'] = changenum($value['c_pv'])?changenum($value['c_pv']):0;
			$list[$key]['c_attention'] = changenum($value['c_attention'])?changenum($value['c_attention']):0;
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0, "查询成功", $data);
	}

	/**
	 * 指定商家广告位查询
	 * @param acode
	 */
	public function UserAdevert($parr){
		$acode = $parr['acode'];

		$data['isputin'] = 0;
		$data['isputin1'] = 0;

		//店铺卡劵区 广告位限制数量
		$nwhere['c_type'] = 1;
		$limit_num = M('A_advert_set')->where($nwhere)->getField('c_num');

		$w['c_ucode'] = $acode;
		$w['c_status'] = 2;
		$w['c_type'] = 1;
		$yetnum = M('A_advert')->where($w)->count();

		$data['sort'] = $yetnum + 1;

		$num = $limit_num - $yetnum;
		if($num > 0){
			$data['isputin'] = $num;
		}

		//订单完成区 广告位限制数量
		$nwhere1['c_type'] = 2;
		$limit_num1 = M('A_advert_set')->where($nwhere1)->getField('c_num');

		$w1['c_ucode'] = $acode;
		$w1['c_status'] = 2;
		$w1['c_type'] = 2;
		$yetnum1 = M('A_advert')->where($w1)->count();

		$data['sort1'] = $yetnum1 + 1;

		$num1 = $limit_num1 - $yetnum1;
		if($num1 > 0){
			$data['isputin1'] = $num1;
		}

		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 投放广告
	 * @param acode,cardid,type,order
	 */
	public function PutinAdevert($parr){
		$w['c_id'] = $parr['cardid'];
		$cardinfo = M('A_advert_card')->where($w)->find();
		if (!$cardinfo) {
			return Message(3000,'广告牌不存在');
		}

		//查询是否还有推广位
		$nwhere['c_type'] = $parr['type'];
		$setinfo = M('A_advert_set')->where($nwhere)->find();

		$w['c_ucode'] = $parr['acode'];
		$w['c_status'] = 2;
		$w['c_type'] = $parr['type'];
		$yetnum = M('A_advert')->where($w)->count();
		if (($setinfo['c_num']-$yetnum) <= 0) {
			return Message(3001,'该推广位已被抢光');
		}

		$chancewhere['c_ucode'] = $parr['ucode'];
		$chancewhere['c_rule'] = 5;
		$chancewhere['c_num'] = array('GT',0);
		$chanceinfo = M('Activity_lotterynum')->where($chancewhere)->getField('c_num');
		if (!$chanceinfo) {
			return Message(3001,'您还没有获得推广机会');
		}

		if (strtotime('+'.$setinfo['c_duehour'].' hours') <= strtotime($cardinfo['c_duetime'])) {
			$duetime = date('Y-m-d H:i:s',strtotime('+ '.$setinfo['c_duehour'].'hours'));
		} else {
			$duetime = $cardinfo['c_duetime'];
		}

		$db = M('');
		$db -> startTrans();

		//增加投放记录
		$add_data['c_ucode'] = $parr['acode'];
		$add_data['c_type'] = $parr['type'];
		$add_data['c_adid'] = $parr['cardid'];
		$add_data['c_order'] = $parr['order'];
		$add_data['c_duetime'] = $duetime;
		$add_data['c_addtime'] = gdtime();

		$result = M('A_advert')->add($add_data);

		if(!$result){
			$db->rollback();
			return Message(1001,'投放失败');
		}

		//修改商家被投放广告位数量
		$w1['c_ucode'] = $parr['acode'];
		$result = M('Users')->where($w1)->setInc('c_advertnum',1);

		if(!$result){
			$db->rollback();
			return Message(1003,'修改用户信息失败');
		}

		//扣除推广机会
		$result = M('Activity_lotterynum')->where($chancewhere)->setDec('c_num',1);
		if(!$result){
			$db->rollback();
			return Message(1003,'使用投放机会失败');
		}

		$db->commit();
		return Message(0,"投放成功");
	}

	//推荐产品
	public function TuijianProduce($acode,$num)
	{	
		$w['c_ucode'] = $acode;
		$w['c_ishow'] = 1;
		$w['c_isdele'] = 1;
		$field = 'c_name,c_pimg,c_desc,c_pcode,c_ucode,c_source,c_isagent,c_price';
		$data = M('Product')->where($w)->field($field)->limit($num)->order('rand()')->select();
		foreach ($data as $key => $value) {
			$data[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];
		}
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 广告牌领取
	 * @param ucode,acode,cardid,vid,type
	 */
	public function ReceiveAdvert($parr)
	{
		if (empty($parr['ucode'])) {
			return Message(1009,'请先登录再操作');
		}	

		//查询广告牌信息
		$w['c_id'] = $parr['cardid'];
		$w['c_num'] = array('GT',0);
		$w['c_status'] = 2;
		$w['c_delete'] = 2;
		$w['c_duetime'] = array('GT',gdtime());		
		$cardinfo = M('A_advert_card')->where($w)->find();
		if (!$cardinfo) {
			return Message(3000,'该卡劵已被领完');
		}

		$db = M('');
		$db->startTrans();
		//增加领取记录
		$logparr['ucode'] = $parr['ucode'];
        $logparr['acode'] = $parr['acode'];
        $logparr['adid'] = $parr['cardid'];
        $logparr['vid'] = $parr['vid'];
        $logparr['type'] = $parr['type'];
		$result = $this->AddGetlog($logparr);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}

		//卡劵领取
		$parr['cid'] = $cardinfo['c_pid'];
		$parr['sourceid'] = $parr['cardid'];
		$parr['joinaid'] = '';
		$parr['remark'] = '推广位';
		$result = IGD('Coupon','Newact')->ReceiveCoupon($parr);
		if ($result['code'] != 0) {
			$db->rollback();
			return $result;
		}

		//扣除广告牌剩余数量
		$result = M('A_advert_card')->where($w)->setDec('c_num',1);
		if (!$result) {
			$db->rollback();
			return Message(3002, '扣除奖项余额');
		}

		//领取完毕返回推广位
		$cw['c_id'] = $parr['cardid'];
		$cnum = M('A_advert_card')->where($cw)->getField('c_num');
		if ($cnum <= 0) {
			$save['c_status'] = 1;
			$vw['c_id'] = $parr['vid'];
			$vw['c_status'] = 2;
			$result = M('A_advert')->where($vw)->save($save);
			if (!$result) {
				$db->rollback();
				return Message(3000,'操作失败');
			}

			//退回商家被投放广告位数量
			$w1['c_ucode'] = $parr['acode'];
			$result = M('Users')->where($w1)->setDec('c_advertnum',1);
			if (!$result) {
				$db->rollback();
				return Message(3001,'操作失败');
			}
		}

		$db->commit();
		return Message(0,'领取成功');

	}

	/**
	 * 商家查询推广位数据
	 * @param acode,type,ucode
	 */
	public function GetShopAdvert($parr)
	{
		$w['a.c_ucode'] = $parr['acode'];
		$w['a.c_type'] = $parr['type'];
		$w['a.c_status'] = 2;
		$w['a.c_duetime'] = array('GT',gdtime());		
		$join = 'left join t_a_advert_card as b on a.c_adid=b.c_id';
		$field = 'a.*,b.c_pid,b.c_id as cardid,b.c_type as cardtype';
		$list = M('A_advert as a')->join($join)->where($w)->field($field)->order('a.c_order asc')->select();		
		$sg = 0;
		foreach ($list as $key => $value) {
			if ($value['cardtype'] == 1) { //查询卡劵
				$cardwhere['c_id'] = $value['c_pid'];
				$data[$sg] = M('A_actcard')->where($cardwhere)->find();
				$data[$sg]['cardid'] = $value['cardid'];
				$data[$sg]['vid'] = $value['c_id'];
				$sg++;			
			}
		}

		foreach ($data as $k => $v) {
			$data[$k]['c_starttime'] = str_replace('-', '.', $v['c_starttime']);
    		$data[$k]['c_endtime'] = str_replace('-', '.', $v['c_endtime']);
    		$data[$k]['c_img'] = GetHost() . '/' . $v['c_img'];

    		$userwhere['c_ucode'] = $v['c_ucode'];
        	$userfield = 'c_ucode,c_headimg,c_nickname';
            $userinfo = M('Users')->where($userwhere)->field($userfield)->find();
            $data[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data[$k]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

            $data[$k]['c_limit_money'] = round($v['c_limit_money']);
            $showstr = '全部商品';
        	if (!empty($v['c_pcodearr'])) {
        		$showstr = '部分商品';
        	}
            if ($v['c_type'] == 1) {
            	$data[$k]['c_money'] = round($v['c_money']);
            	$data[$k]['showstr'] = '满'.$data[$k]['c_limit_money'].'元可用';
            } else if ($v['c_type'] == 2) {
            	$data[$k]['c_money'] = round($v['c_money'],1);
            	$data[$k]['showstr'] = '最高折扣'.$data[$k]['c_limit_money'].'元';
            }

            $data[$k]['geturl'] = GetHost(1).'/index.php/Adposition/Index/getcoupon?type=1&acode='.$parr['acode'].'&cardid='.$v['cardid'].'&vid='.$v['vid'];
		}

		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 增加浏览记录
	 * @param ucode,acode,adid,vid,type
	 */
	public function AddScanlog($parr)
	{
		$where['c_ucode'] = $parr['ucode'];
		$where['c_adid'] = $parr['adid'];
		$where['c_vid'] = $parr['vid'];
		$where['c_acode'] = $parr['acode'];
		$where['c_type'] = $parr['type'];
		$seelog = M('A_adcard_seelog')->where($where)->find();

		$add['c_lasttime'] = gdtime();
		if (!$seelog) {
			$add['c_ucode'] = $parr['ucode'];			
			$add['c_adid'] = $parr['adid'];
			$add['c_vid'] = $parr['vid'];
			$add['c_acode'] = $parr['acode'];
			$add['c_type'] = $parr['type'];
			$add['c_num'] = 1;
			$add['c_addtime'] = gdtime();
			$result = M('A_adcard_seelog')->add($add);
		} else {
			$add['c_num'] = $seelog['c_num'] + 1;
			$result = M('A_adcard_seelog')->where($where)->save($add);
		}

		if (!$result) {
			return Message(3000,'记录失败');
		}

		$scanlog['c_id'] = $parr['adid'];
		$result = M('A_advert_card')->where($scanlog)->setInc('c_scannum',1);
		if (!$result) {
			return Message(3001,'记录失败');
		}

		return Message(0,'记录成功');
	}

	/**
	 * 增加领取记录
	 * @param ucode,acode,adid,vid,type
	 */
	public function AddGetlog($parr)
	{
		$where['c_ucode'] = $parr['ucode'];
		$where['c_adid'] = $parr['adid'];
		$where['c_vid'] = $parr['vid'];
		$where['c_acode'] = $parr['acode'];
		$where['c_type'] = $parr['type'];
		$seelog = M('A_adcard_getlog')->where($where)->find();
		if ($seelog) {
			return Message(3000,'您已经领取');
		}

		$add['c_ucode'] = $parr['ucode'];			
		$add['c_adid'] = $parr['adid'];
		$add['c_vid'] = $parr['vid'];
		$add['c_acode'] = $parr['acode'];
		$add['c_type'] = $parr['type'];
		$add['c_addtime'] = gdtime();
		$result = M('A_adcard_getlog')->add($add);
		if (!$result) {
			return Message(3000,'记录失败');
		}

		return Message(0,'记录成功');
	}	

	/**
	 * 用户每天进入推广位，平台发放推广机会
	 * @param ucode
	 */
	function GetGratisPrize($parr)
	{
		//查询今日是否已领取机会
		$lotterywhere['c_ucode'] = $parr['ucode'];
        $lotterywhere['c_rule'] = 5;
        $lotterywhere['c_addtime'] = array('BETWEEN',array(date('Y-m-d 00:00:00'),date('Y-m-d 23:59:59')));
        $lotteryUser = M('Activity_lotterynum')->where($lotterywhere)->order('c_addtime desc')->find();
        if ($lotteryUser) {
        	return Message(3000,'您今日已领取推广机会');
        }

		//查询平台商家福利活动
		$result = IGD('Index','Newact')->GetPlatActInfo(29);
		if ($result['code'] = 0) {
			return Message(3000,'平台尚未开发福利活动');
		}
		$actvitydata = $result['data'];

		//查询配置
		$clickconf = IGD('Redis', 'Redis')->Gethash('newact')['data'];
		$spandnum = $clickconf['spandnum'];
		if ($spandnum <= 0) {
			return Message(3001,'平台还没有发放推广机会');
		}

		//查询奖项
		$where['c_type'] = 5;
		$where['c_joinaid'] = $actvitydata['c_id'];
		$where['c_num'] = array('EGT',$spandnum);
		$prizedata = M('A_actprize')->where($where)->order('rand()')->find();
		if (!$prizedata) {
			return Message(3002,'推广机会被领完');
		}

		$db = M('');
		$db->startTrans();

		//扣除推广机会奖项库存
		$pw['c_id'] = $prizedata['c_id'];
		$pw['c_type'] = 5;
		$pw['c_joinaid'] = $actvitydata['c_id'];
		$pw['c_num'] = array('EGT',$spandnum);
		$result = M('A_actprize')->where($pw)->setDec('c_num',$spandnum);
		if (!$result) {
			$db->rollback();
			return Message(3003,'领取推广机会失败');
		}

		//增加推广机会
		$parr['rule'] = 5;
		$result = $this->AddActNum($parr,$spandnum);
		if ($result['code'] != 0) {
			$db->rollback();
			return Message(3003,'增加机会失败');
		}

		$db->commit();
		return MessageInfo(0,'领取成功',$spandnum);
	}

	/**
     * 赠送活动抽奖次数与机会
     * @param ucode,num,rule(2大转盘,4购物抽奖,5推广机会)
     */
    function AddActNum($parr,$num)
    {
        $lotterywhere['c_ucode'] = $parr['ucode'];
        $lotterywhere['c_rule'] = $parr['rule'];
        $lotteryUser = M('Activity_lotterynum')->where($lotterywhere)->order('c_addtime desc')->find();
        if (!$lotteryUser) {
            $lotterydata['c_ucode'] = $parr['ucode'];
            $lotterydata['c_num'] = $num;
            $lotterydata['c_rule'] = $parr['rule'];
            $lotterydata['c_addtime'] = gdtime();
            $result = M('Activity_lotterynum')->add($lotterydata);
            if (!$result) {
                return Message(3000,'用户次数新增失败');
            }
        } else {
            $lotterysave['c_num'] = $lotteryUser['c_num'] + $num;
        	$lotterysave['c_addtime'] = gdtime();
            $result = M('Activity_lotterynum')->where($lotterywhere)->save($lotterysave);
            if (!$result) {
                return Message(3000,'用户次数增加失败');
            }

        }
        return Message(0,'增加成功');
    }

    /**
	 * 定时器检测推广位位过期
	 * 
	 */
	public function CheckAdvert()
	{
		$w['c_status'] = 2;
		$w['c_duetime'] = array('ELT',gdtime());
		$data = M('A_advert')->where($w)->limit(20)->select();
		foreach ($data as $key => $value) {
			$db = M('');
			$db->startTrans();

			$save['c_status'] = 1;
			$w['c_id'] = $value['c_id'];
			$result = M('A_advert')->where($w)->save($save);
			if (!$result) {
				$db->rollback();
				return Message(3000,'操作失败');
			}

			//退回商家被投放广告位数量
			$w1['c_ucode'] = $value['c_ucode'];
			$result = M('Users')->where($w1)->setDec('c_advertnum',1);
			if (!$result) {
				$db->rollback();
				return Message(3001,'操作失败');
			}
			$db->commit();
		}
		
		return MessageInfo(0,'操作成功',$result);
	}
}