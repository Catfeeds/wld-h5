<?php
/**
 * 	微商代理系统 代理微商模块（微店）
 *
 */
class SmallshopStore {
	/**
	 * 个人微店头部及推广数据
	 * @param ucode
	 */
	public function MySmallShop($parr){
		$w['c_ucode'] = $parr['ucode'];

		$userinfo = M('Users')->field('c_ucode,c_nickname,c_headimg')->where($w)->find();

		if(!$userinfo){
			return Message(1001,"没查询到相关用户信息！");
		}

		$userinfo['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
		$userinfo['share_img'] = GetHost().'/Uploads/logo.png';
		$userinfo['share_title'] = "邀你参观【" . $userinfo['c_nickname'] . "】的小蜜微店";
		$userinfo['share_des'] = '小伙伴们快到我的小蜜微店看看吧';
		$userinfo['share_url'] = GetHost(1) . "/index.php/Agency/User/myshop?fromucode=" . $userinfo['c_ucode'];

		return MessageInfo(0,"查询成功",$userinfo);
	}

	/**
	 * 查询用户是否有代理记录
	 * @param ucode
	 */
	public function FindAgentlog($parr)
	{
		$pw['c_ucode'] = $parr['ucode'];
		$data = M('Agency_member')->where($pw)->find();
		if (!$data) {
			return Message(3000,'没有代理记录');
		}

		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 个人微店商品 代理的所有代理商品列表
	 * @param ucode,pagesize,pageindex
	 */
	public function SmallShopProduct($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_isdele'] = 1;
		$pw['c_ishow'] = 1;
		$pw['c_isagent'] = 2;

		$field = "c_ucode,c_name,c_pimg,c_salesnum,c_pcode,c_price";

		$list = M('Product')->field($field)->where($pw)->limit($countPage, $pageSize)->order('c_salesnum desc')->select();

		$count = M('Product')->where($pw)->count();
        $pageCount = ceil($count / $pageSize);

		if(!$list){
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
	 * 个人微店商品管理 代理过的商家列表
	 * @param ucode,pagesize,pageindex
	 */
	public function AgencyMerchant($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$pw['a.c_ucode'] = $parr['ucode'];

		$field = "a.*,u.c_nickname,u.c_headimg";
		$join = "left join t_users as u on a.c_acode=u.c_ucode";

		$list = M('Agency_member as a')->field($field)->join($join)->where($pw)->limit($countPage, $pageSize)->order('a.c_grade desc,a.c_id desc')->select();

		$count = M('Agency_member as a')->join($join)->where($pw)->count();
        $pageCount = ceil($count / $pageSize);

		if(!$list){
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
			return MessageInfo(0, "查询成功", $data);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 分销商查看单个代理商详情
	 * @param acode,ucode
	 */
	public function GetOneShopInfo($parr)
	{
		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_acode'] = $parr['acode'];
		$info = M('Agency_member')->where($pw)->find();
		

		//查询商家关注信息
		$join = 'left join t_users_date as b on a.c_ucode=b.c_ucode';
		$usw['a.c_ucode'] = $parr['acode'];
		$field = 'a.c_ucode,a.c_nickname,a.c_shop,a.c_isfixed1 as c_isfixed,a.c_headimg,b.c_pv,b.c_attention';
		$user = M('Users as a')->join($join)->where($usw)->field($field)->find();
		$user['c_headimg'] = GetHost().'/'.$user['c_headimg'];
		$user['c_pv'] = changenum($user['c_pv']);
		$user['c_attention'] = changenum($user['c_attention']);

		//查询是否关注
		$aw['c_ucode'] = $parr['ucode'];
        $aw['c_attention_ucode'] = $parr['acode'];

        $count = M('Users_attention')->where($aw)->count();

        if($count == 0){
        	$user['is_attention'] = 0;
        }else{
        	$user['is_attention'] = 1;
        }

		//距离下一级还差多少元
		if($info['c_grade'] < 5){
			$gw['c_grade'] = $info['c_grade'] + 1;
			$gw['c_ucode'] = $info['c_acode'];
			$next_jymoney = M('Agency_grade')->where($gw)->getField('c_jy_money');
			$info['differ_money'] = sprintf('%.2f',$next_jymoney-$info['c_money']);
		}

		$data['info'] = $info;
		$data['user'] = $user;
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 个人微店商品管理 代理某个商家的所有代理商品列表
	 * @param ucode,acode,pagesize,pageindex
	 */
	public function AgencyProduct($parr){
		//查询商家商品编码
		$pw['c_ucode'] = $parr['acode'];
		$pw['c_isdele'] = 1;
		$pw['c_isagent'] = 0;

		$pcode_arr = M('Product')->field('c_pcode')->where($pw)->select();

		if(!$pcode_arr){
			return Message(1001,"没有查询到相关商品信息！");
		}

		$pcode_str = arr_to_str($pcode_arr);

		//查询代理的商品列表
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_isdele'] = 1;
		$pw['c_isagent'] = 2;
		$pw['c_agent_pcode'] = array('in',$pcode_str);

		$field = "c_ucode,c_pcode,c_name,c_pimg,c_num,c_salesnum,c_price,c_agent_pcode,c_agency_num";

		$list = M('Product')->field($field)->where($pw)->limit($countPage, $pageSize)->order('c_id desc')->select();

		$count = M('Product')->where($pw)->count();
        $pageCount = ceil($count / $pageSize);

		if(!$list){
			$list = array();
			$data = Page($pageIndex, $pageCount, $count, $list);
			return MessageInfo(0, "查询成功", $data);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];
			$list[$key]['kcnum'] = $value['c_agency_num'] - $value['c_salesnum'];

			//查询代理级别
            $pw['c_acode'] = $parr['acode'];
            $pw['c_ucode'] = $parr['ucode'];
            $level = M('Agency_member')->where($pw)->getField('c_grade');
            if ($level) {
                //查询代理产品等级价格优惠
                $disw['c_grade'] = $level;
                $disw['c_pcode'] = $value['c_agent_pcode'];
                $discount = M('Agency_product_dis')->where($disw)->getField('c_discount');
            
                if ($discount > 0) {
                    $list[$key]['profit'] = sprintf('%.2f',$value['c_price']*(10-$discount)/10);
                }
            }

			//分享数据
			$list[$key]['sharetit'] = $value['c_name'];
			$list[$key]['shareurl'] = GetHost(1)."/index.php/Agency/User/detail?pcode=".$value['c_pcode'];
			$list[$key]['shareimg'] = $list[$key]['c_pimg'];
			$list[$key]['sharedesc'] = $value['c_desc'];	
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 个人微店商品管理 修改商品库存
	 * @param ucode,pcode,num
	 */
	public function EditNum($parr){
		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_pcode'] = $parr['pcode'];

		$produceinfo = M('Product')->field('c_agency_num,c_salesnum')->where($pw)->find();

		if(!$produceinfo){
			return Message(1001,"没有查询到相关商品信息！");
		}

		$kcnum = $produceinfo['c_agency_num'] - $produceinfo['c_salesnum'];

		if($kcnum < $parr['num']){
			return Message(1002,"修改不能超过实际库存！");
		}

		$save_date['c_num'] = $parr['num'];
		$save_date['c_updatetime'] = gdtime();
		$result = M('Product')->where($pw)->save($save_date);
		if(!$result){
			return Message(1003,"操作失败！");
		}

		return Message(0,"操作成功！");
	}

	/**
	 * 个人微店商品管理 删除商品
	 * @param ucode,pcode
	 */
	public function ProductDel($parr){
		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_pcode'] = $parr['pcode'];
		$produceinfo = M('Product')->where($pw)->find();
		if(!$produceinfo){
			return Message(1001,"没有查询到相关商品信息！");
		}

		$save_date['c_isdele'] = 2;
		$save_date['c_updatetime'] = gdtime();
		$result = M('Product')->where($pw)->save($save_date);
		if(!$result){
			return Message(1002,"操作失败！");
		}

		return Message(0,"操作成功！");
	}

}