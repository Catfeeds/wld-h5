<?php
/**
 * 	微商代理系统 代理商城模块
 *
 */
class AgencymallStore {
	/**
	 * 代理商城 商品列表
	 * @param ucode,pageindex,pname,categoryid
	 */
	public function MallProducts($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}

		if ($parr['categoryid'] == 50) {
		    $parr['categoryid'] = 0;
		}

		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$where['a.c_ishow'] = 1;
		$where['a.c_isagent'] = 0;
		$where['a.c_isdele'] = 1;
		
		$where['b.c_status'] = 1;
		$where['b.c_isdele'] = 1;
		$where['c.c_bag_status'] = 1;

		if (!empty($parr['pname'])) {
		    $where['a.c_name'] = array('like', '%' . $parr['pname'] . '%');
		}

		if (!empty($parr['categoryid'])) {
		    $where['a.c_categoryid'] = $parr['categoryid'];
		}

		$join = 'left join t_agency_bag_product as b on a.c_pcode=b.c_pcode left join t_agency_bag as c on b.c_bag_code=c.c_bag_code';
		$field = 'a.c_pcode,a.c_ucode,a.c_name,a.c_pimg,a.c_price,a.c_categoryid,b.c_bag_code,1 as c_grade';

		$list = M('Product as a')->join($join)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

		$count = M('Product as a')->join($join)->where($where)->count();
		$pageCount = ceil($count / $pageSize);

		if (count($list) == 0) {
		    $list = array();
		    $data = Page($pageIndex, $pageCount, $count, $list);
		    return MessageInfo(0, '查询成功', $data);
		}

		foreach ($list as $key => $value) {
		    $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
		    
		    //查讯一级代理价
		    $diswhere['c_pcode'] = $value['c_pcode'];
		    $diswhere['c_grade'] = 1;
		    $discount = M('Agency_product_dis')->where($diswhere)->getField('c_discount');
		    $list[$key]['agent_price'] = sprintf('%.2f',$value['c_price']*$discount/10);
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 代理商城 商家所有品牌包
	 * @param acode,bag_code
	 */
	public function AgencyBag($parr){
		$w['c_ucode'] = $parr['acode'];
		$w['c_bag_status'] = 1;

		if ($parr['bag_code']) {
			$bag_code = $parr['bag_code'];
			$order = "case when bag_code='$bag_code' then 1 else 0 end desc,c_id desc";
		} else {
			$order = "c_id desc";
		}
		$list = M('Agency_bag')->where($w)->order('c_id desc')->select();

        if (!$list) {
            $list = array();
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
        	$where['c_regionid'] = $value['c_id'];
        	$where['c_sourceid'] = 5;

        	$img_list = M('Resource_img')->where($where)->order('c_id asc')->select();
        	foreach ($img_list as $k => $v) {
        		$img_list[$k]['c_thumbnail_img'] = GetHost().'/'.$v['c_thumbnail_img'];
        		$img_list[$k]['c_img'] = GetHost().'/'.$v['c_img'];
        	}

        	$list[$key]['img'] = $img_list[0]['c_thumbnail_img'];
        	$list[$key]['imglist'] = $img_list;
        }

		return MessageInfo(0,"查询成功",$list);
	}

	/**
	 *  产品包 产品列表
	 *  @param acode,bag_code,grade(用户相对商家代理等级),pagesize,pageindex,(pcode)
	 */
	public function AgencyBagProduct($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['a.c_ucode'] = $parr['acode'];
		$w['a.c_bag_code'] = $parr['bag_code'];
		$w['a.c_status'] = 1;
		$w['a.c_isdele'] = 1;

		$field = "a.*,p.c_name,p.c_pimg,p.c_price,p.c_desc";
		$join = "inner join t_product as p on a.c_pcode=p.c_pcode";
		if ($parr['pcode']) {
			$popcode = $parr['pcode'];
			$order = "case when p.c_pcode='$popcode' then 1 else 0 end desc,a.c_id desc";
		} else {
			$order = "a.c_id desc";
		}
		
		$list = M('Agency_bag_product as a')->field($field)->join($join)->where($w)->limit($countPage, $pageSize)->order($order)->select();

		$count = M('Agency_bag_product as a')->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];

        	$list[$key]['c_name'] = subtext($value['c_name'], 6);

        	//计算代理价
        	$where['c_pcode'] = $value['c_pcode'];
        	$where['c_bag_code'] = $parr['bag_code'];
        	if($parr['grade']){
        		$where['c_grade'] = $parr['grade'];
        	}else{
        		$where['c_grade'] = 1;
        	}

        	$price_dis = M('Agency_product_dis')->where($where)->getField('c_discount');
        	$list[$key]['dis_price'] = sprintf('%.2f',$value['c_price']*$price_dis/10);
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}
}