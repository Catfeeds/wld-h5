<?php
/**
 * 	微商代理系统 商家模块
 *
 */
class AgencyStore {
	/**
	 * 等级列表
	 * @param ucode,agentucode
	 */
	public function AgencyGrade($parr){
		$w['c_ucode'] = $parr['ucode'];

		$data = M('Agency_grade')->field('c_id,c_grade_name,c_grade,c_jy_money,c_desc')->where($w)->order('c_grade asc')->select();

		if(!$data){
			$data = array();
		}
		$pw['c_acode'] = $parr['ucode'];
		$pw['c_ucode'] = $parr['agentucode'];
		$levelinfo = M('Agency_member')->where($pw)->find();
		foreach ($data as $key => $value) {
			$data[$key]['level'] = $levelinfo['c_grade'];
			$data[$key]['money'] = $levelinfo['c_money'];
		}

		return MessageInfo(0,"查询成功",$data);
	}

	//查询代理某个商家最低价格
	public function GetAgencyPrice($parr)
	{
		$gw['c_ucode'] = $parr['acode'];
		$data['agentprice'] = M('Agency_grade')->where($gw)->getField('c_jy_money');
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 查询单个等级详情
	 * @param ucode,grade
	 */
	public function FindOneGrade($parr)
	{
		$w['c_ucode'] = $parr['ucode'];
		$w['c_grade'] = $parr['grade'];
		$data = M('Agency_grade')->field('c_id,c_grade_name,c_grade,c_jy_money,c_desc')->where($w)->find();
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 代理商等级 添加编辑
	 * @param ucode,grade_name,jy_money,desc,添加，编辑 需传Id
	 */
	public function AddAgencyGrade($parr){
		if(empty($parr['ucode']) || empty($parr['grade_name']) || empty($parr['grade']) || empty($parr['jy_money'])){
			return Message(1001,"请填写完整信息！");
		}

		$data['c_grade_name'] = $parr['grade_name'];		
		$data['c_jy_money'] = $parr['jy_money'];
		$data['c_desc'] = $parr['desc'];
		$data['c_updatetime'] = gdtime();

		//查询等级
		$w['c_ucode'] = $parr['ucode'];
		$w['c_grade'] = $parr['grade'];
		$djinfo = M('Agency_grade')->where($w)->find();
		if(empty($djinfo)){
			$data['c_grade'] = $parr['grade'];
			$data['c_ucode'] = $parr['ucode'];
			$data['c_addtime'] = gdtime();
			
			$result = M('Agency_grade')->add($data);
		}else{
			$result = M('Agency_grade')->where($w)->save($data);
		}

		if(!$result){
			return Message(1002,"操作失败!");
		}

		return Message(0,"操作成功！");
	}

	/**
	 * 产品包列表
	 * @param ucode
	 */
	public function AgencyBag($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

		$w['c_ucode'] = $parr['ucode'];

		$list = M('Agency_bag')->where($w)->limit($countPage, $pageSize)->order('c_id desc')->select();

		$count = M('Agency_bag')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
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

        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 查询单个代理包详情
	 * @param ucode,bag_code
	 */
	public function GetOneBagsInfo($parr)
	{
		$w['c_ucode'] = $parr['ucode'];
		$w['c_bag_code'] = $parr['bag_code'];
		$data = M('Agency_bag')->where($w)->limit($countPage, $pageSize)->find();

    	$where['c_regionid'] = $data['c_id'];
    	$where['c_sourceid'] = 5;

    	$img_list = M('Resource_img')->where($where)->order('c_id asc')->select();
    	foreach ($img_list as $k => $v) {
    		$img_list[$k]['c_thumbnail_img'] = GetHost().'/'.$v['c_thumbnail_img'];
    		$img_list[$k]['c_img'] = GetHost().'/'.$v['c_img'];
    	}

    	$data['img'] = $img_list[0]['c_thumbnail_img'];
    	$data['imglist'] = $img_list;
		return MessageInfo(0,"查询成功",$data);
	}


	/**
	 *  产品包 添加编辑
	 *  @param ucode,bag_name,bag_desc,imglist
	 */
	public function AddAgencyBag($parr){
		if (!empty($parr['bag_code'])) {
			$bagwhere['c_ucode'] = $parr['ucode'];
			$bagwhere['c_bag_code'] = $parr['bag_code'];
			$baginfo = M('Agency_bag')->where($bagwhere)->find();
		}
		
		$bagdata['c_bag_name'] = $parr['bag_name'];
		$bagdata['c_bag_desc'] = $parr['bag_desc'];
		$bagdata['c_updatetime'] = gdtime();

		$db = M('');
		$db->startTrans();

		if(!$baginfo){
			//生成产品包编码
			$bag_code = 'bag'.time();
			$bagdata['c_bag_code'] = $bag_code;

			$bagdata['c_bag_status'] = 2;
			$bagdata['c_ucode'] = $parr['ucode'];
			$bagdata['c_addtime'] = gdtime();

			$result = M('Agency_bag')->add($bagdata);
			$bid = $result;
		}else{
			//判断产品包是否存在产品
			$w['c_bag_code'] = $baginfo['c_bag_code'];
			$w['c_status'] = 1;
			$w['c_isdele'] = 1;
			$produceinfo = M('Agency_bag_product')->where($w)->count();
			if($produceinfo > 0){
				$bagdata['c_bag_status'] = $parr['status'];
			}
			$result = M('Agency_bag')->where($bagwhere)->save($bagdata);
			$bid = $baginfo['c_id'];
		}

		if ($result <= 0) {
		    $db->rollback(); //不成功，则回滚
		    return Message(1025, '添加资源失败');
		}

		//删除图片
        $imgw['c_regionid'] = $bid;
        $imgw['c_sourceid'] = 5;
        $imgadata = M('Resource_img')->field('c_img')->where($imgw)->select();
        if (!empty($imgadata)) {
            foreach ($imgadata as $key => $value) {
                unlink($value);
            }

            $result = M('Resource_img')->where($imgw)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '删除图片失败');
            }
        }

		//添加图片
		$imglist = $parr['imglist'];
		
		foreach ($imglist as $key => $value) {
		    $imgdata['c_sourceid'] = 5;
		    $imgdata['c_regionid'] = $bid;
		    $imgdata['c_img'] = $value;
		    $imgdata['c_thumbnail_img'] = $value;
		    $result = M('Resource_img')->add($imgdata);
		    if ($result <= 0) {
		        $db->rollback(); //不成功，则回滚
		        return Message(1025, '图片添加失败');
		    }
		}

		$db->commit();
		return Message(0, '添加成功');
	}

	/**
	 *  产品包上下架
	 *  @param bag_code,ucode,operate(1-上架，2-下架)
	 */
	public function BagStatus($parr){
		$djw['c_ucode'] = $parr['ucode'];
		$djinfo = M('Agency_grade')->where($djw)->find();
		if (!$djinfo) {
			return Message(3001,'请先完善代理等级');
		}

		$operate = $parr['operate'];

		$w['c_bag_code'] = $parr['bag_code'];

		if($operate == 1){
			$w['c_status'] = 1;
			$w['c_isdele'] = 1;
			$produceinfo = M('Agency_bag_product')->where($w)->select();

			if(!$produceinfo){
				return Message(1001,'请先添加商品！');
			}
		}

		$where['c_bag_code'] = $parr['bag_code'];
		$where['c_ucode'] = $parr['ucode'];

		$save['c_bag_status'] = $operate;
		$save['c_updatetime'] = gdtime();
		$result = M('Agency_bag')->where($where)->save($save);

		if(!$result){
			return Message(1002,"操作失败！");
		}

		return Message(0,"操作成功！");		
	}

	/**
	 *  产品包 产品列表
	 *  @param ucode,bag_code,pagesize,pageindex
	 */
	public function AgencyBagProduct($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['a.c_ucode'] = $parr['ucode'];
		$w['a.c_bag_code'] = $parr['bag_code'];
		$w['a.c_isdele'] = 1;

		$field = "a.*,p.c_name,p.c_pimg,p.c_price,p.c_desc";
		$join = "inner join t_product as p on a.c_pcode=p.c_pcode";

		$list = M('Agency_bag_product as a')->field($field)->join($join)->where($w)->limit($countPage, $pageSize)->order('a.c_id desc')->select();

		$count = M('Agency_bag_product as a')->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];

        	$where['c_agent_pcode'] = $value['c_pcode'];
        	$where['c_isagent'] = 2;

        	$list[$key]['agency_num'] = M('Product')->where($where)->count();

        	$where1['c_pcode'] = $value['c_pcode'];
        	$where1['c_bag_code'] = $value['c_bag_code'];

        	$gradelist = M('Agency_product_dis')->where($where1)->select();
        	foreach ($gradelist as $key1 => $value1) {
        		$gradelist[$key1]['dis_price'] = sprintf('%.2f',$value['c_price']*$value1['c_discount']);
        	}

        	$list[$key]['gradelist'] = $gradelist;
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 *  产品包 选择产品
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

        //查询产品包
        $bagwhere['c_ucode'] = $parr['ucode'];
        $bagwhere['c_isdele'] = 1;
        $pcode_arr = M('Agency_bag_product')->where($bagwhere)->field('c_pcode')->select();

        $pcode_str = arr_to_str($pcode_arr);
        if (!empty($pcode_str)) {
        	$w['c_pcode'] = array('not in',$pcode_str);
        }
		$w['c_ucode'] = $parr['ucode'];
		$w['c_isagent'] = 0;
		$w['c_isdele'] = 1;
		$w['c_ishow'] = 1;

		$field = "c_pcode,c_name,c_pimg,c_price";

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
	 *  产品包 添加编辑产品
	 *  @param ucode,bag_code,pcode,status,gradelist
	 */
	public function AddBagProduct($parr){
		$w['c_ucode'] = $parr['ucode'];
		$w['c_bag_code'] = $parr['bag_code'];
		$w['c_pcode'] = $parr['pcode'];

		$produceinfo = M('Agency_bag_product')->where($w)->find();

		$db = M('');
		$db->startTrans();

		if(!$produceinfo){
			$prodata['c_ucode'] = $parr['ucode'];
			$prodata['c_pcode'] = $parr['pcode'];
			$prodata['c_bag_code'] = $parr['bag_code'];
			$prodata['c_addtime'] = gdtime();

			$result = M('Agency_bag_product')->add($prodata);
		}else{
			$prodata['c_status'] = $parr['status'];
			$prodata['c_isdele'] = 1;

			$result = M('Agency_bag_product')->where($w)->save($prodata);
		}

		if($result < 0){
			$db->rollback();
			return Message(1001,"操作失败");
		}

		//删除折扣
        $disw['c_pcode'] = $parr['pcode'];
        $disw['c_bag_code'] = $parr['bag_code'];
        $disdata = M('Agency_product_dis')->where($disw)->select();
        if (!empty($disdata)) {
            $result = M('Agency_product_dis')->where($disw)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '删除折扣设置失败');
            }
        }

		//添加折扣
		$gradelist = $parr['gradelist'];
		
		foreach ($gradelist as $key => $value) {
			if ($value['discount'] <= 0) {
				$db->rollback(); //不成功，则回滚
                return Message(1026, '请完善折扣信息');
			}
		    $imgdata['c_pcode'] = $parr['pcode'];
		    $imgdata['c_bag_code'] = $parr['bag_code'];
		    $imgdata['c_grade_name'] = $value['grade_name'];
		    $imgdata['c_grade'] = $value['grade'];
		    $imgdata['c_discount'] = $value['discount'];
		    $result = M('Agency_product_dis')->add($imgdata);
		    if ($result <= 0) {
		        $db->rollback(); //不成功，则回滚
		        return Message(1025, '折扣设置添加失败');
		    }
		}

		//改变产品可代理状态
		$agentwhere['c_pcode'] = $parr['pcode'];
		$prosave['c_updatetime'] = gdtime();
		$prosave['c_agentsign'] = 1;
		$prosave['c_bag_code'] = $parr['bag_code'];
		$result = M('Product')->where($agentwhere)->save($prosave);
		if ($result <= 0) {
	        $db->rollback(); //不成功，则回滚
	        return Message(1025, '修改状态失败');
	    }

		$db->commit();
		return Message(0, '操作成功');
	}

	/**
	 *  品牌包内产品上下架
	 *  @param ucode,bag_code,pcode,status
	 */
	public function BagProductStatus($parr){
		$djw['c_ucode'] = $parr['ucode'];
		$djinfo = M('Agency_grade')->where($djw)->select();
		if (!$djinfo) {
			return Message(3001,'请先完善代理等级');
		}

		//查询等级对应价格
		foreach ($djinfo as $key => $value) {
			$diswhere['c_pcode'] = $parr['pcode'];
		    $diswhere['c_bag_code'] = $parr['bag_code'];
		    $diswhere['c_grade'] = $value['c_grade'];
		    $disinfo = M('Agency_product_dis')->where($diswhere)->find();
		    if ($disinfo['c_discount'] <= 0) {
		        return Message(1025, '请设置对应的等级折扣价');
		    }
		}
		
		$w['c_ucode'] = $parr['ucode'];
		$w['c_bag_code'] = $parr['bag_code'];
		$w['c_pcode'] = $parr['pcode'];

		$db = M('');
		$db->startTrans();

		$savedata['c_status'] = $parr['status'];
		$result = M('Agency_bag_product')->where($w)->save($savedata);
		if ($result <= 0) {
			$db->rollback();
			return Message(1001,"操作失败！");
		}

		//同步所有代理产品下架
		$pw['c_isagent'] = 2;
		$pw['c_agent_pcode'] = $parr['pcode'];
		$info = M('Product')->where($pw)->select();
		if ($info) {
			$save['c_updatetime'] = gdtime();
			$save['c_ishow'] = $parr['status'];
			$result = M('Product')->where($pw)->save($save);
			if ($result <= 0) {
				$db->rollback();
				return Message(1002,"操作失败！");
			}
		}

		//改变产品可代理状态
		$agentwhere['c_pcode'] = $parr['pcode'];
		$prosave['c_updatetime'] = gdtime();
		$agentsign = 0;
		if ($parr['status'] == 1) {
			$agentsign = 1;
		}
		$prosave['c_agentsign'] = $agentsign;
		$result = M('Product')->where($agentwhere)->save($prosave);
		if ($result <= 0) {
	        $db->rollback(); //不成功，则回滚
	        return Message(1025, '修改状态失败');
	    }

		$db->commit();
		return Message(0,"操作成功！");
	}

	/**
	 *  品牌包内产品删除
	 *  @param ucode,bag_code,pcode
	 */
	public function BagProductDel($parr){
		$w['c_ucode'] = $parr['ucode'];
		$w['c_bag_code'] = $parr['bag_code'];
		$w['c_pcode'] = $parr['pcode'];

		$db = M('');
		$db->startTrans();

		$savedata['c_isdele'] = 2;
		$result = M('Agency_bag_product')->where($w)->save($savedata);
		if(!$result){
			$db->rollback();
			return Message(1001,"操作失败！");
		}

		//同步所有代理产品删除
		$pw['c_isagent'] = 2;
		$pw['c_agent_pcode'] = $parr['pcode'];
		$info = M('Product')->where($pw)->select();
		if ($info) {
			$save['c_updatetime'] = gdtime();
			$save['c_isdele'] = 2;
			$result = M('Product')->where($pw)->save($save);
			if ($result <= 0) {
				$db->rollback();
				return Message(1002,"操作失败！");
			}
		}

		$db->commit();
		return Message(0,"操作成功！");
	}

	/**
	 *  分销商列表
	 *  @param ucode,pagesize,pageindex
	 */
	public function AgencyMember($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['a.c_acode'] = $parr['ucode'];
		
		$field = "a.*,u.c_nickname,u.c_headimg";
		$join = "inner join t_users as u on a.c_ucode=u.c_ucode";

		$list = M('Agency_member as a')->field($field)->join($join)->where($w)->limit($countPage, $pageSize)->order('a.c_id desc')->select();

		$count = M('Agency_member as a')->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
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
	 * 查询分销商总人数与总金额
	 * @param ucode
	 */
	public function GetAgencyInfo($parr)
	{
		$w['c_acode'] = $parr['ucode'];
		$count = M('Agency_member')->where($w)->count();
		$money = M('Agency_member')->where($w)->sum('c_money');
		$data['num'] = ($count>0)?$count:'0';
		$data['money'] = ($money>0)?$money:'0.00';
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 查询单个分销商信息
	 * @param agentucode
	 */
	public function GetOneReseller($parr)
	{
		$w['a.c_ucode'] = $parr['agentucode'];
		$field = "a.*,u.c_nickname,u.c_headimg,u.c_isfixed1 as c_isfixed,u.c_shop";
		$join = "left join t_users as u on a.c_ucode=u.c_ucode";
		$data = M('Agency_member as a')->field($field)->join($join)->where($w)->find();
		if ($data) {
			$data['c_headimg'] = GetHost().'/'.$data['c_headimg'];
		}
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 *  分销商代理商品列表
	 *  @param ucode,agentucode,pagesize,pageindex
	 */
	public function AgencyMemberProduct($parr){
		//查询商家商品编码
		$pw['c_ucode'] = $parr['ucode'];
		$pw['c_isdele'] = 1;
		$pw['c_status'] = 1;

		$pcode_arr = M('Agency_bag_product')->field('c_pcode')->where($pw)->select();

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


		$pw1['c_ucode'] = $parr['agentucode'];
		$pw1['c_ishow'] = 1;
		$pw1['c_isdele'] = 1;
		$pw1['c_isagent'] = 2;
		$pw1['c_agent_pcode'] = array('in',$pcode_str);

		$field = "c_ucode,c_name,c_pimg,c_price,c_agent_pcode,c_num,c_salesnum";

		$produceinfo = M('Product')->field($field)->where($pw1)->limit($countPage, $pageSize)->order('c_id desc')->select();

		$count = M('Product')->where($pw1)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$produceinfo) {
            $produceinfo = array();
            $data = Page($pageIndex, $pageCount, $count, $produceinfo);
            return MessageInfo(0, "查询成功", $data);
        }

        $db = M('');

        foreach ($produceinfo as $key => $value) {
        	$produceinfo[$key]['kcnum'] = $value['c_num'] - $value['c_salesnum'];

        	$produceinfo[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];

        	$ucode = $value['c_ucode'];
        	$acode = $parr['ucode'];
        	$pcode = $value['c_agent_pcode'];
        	$sql = "SELECT sum(c_money) as buymoney,sum(c_num) as buynum from t_agency_jylog where c_ucode='$ucode' AND c_acode='$acode' AND c_pcode='$pcode' ";

        	$result = $db->query($sql);

        	$produceinfo[$key]['buynum'] = $result[0]['buynum'];
        	$produceinfo[$key]['buymoney'] = $result[0]['buymoney'];

        	$sql1 = "SELECT a.c_price,a.c_num,a.c_price,a.c_addtime,m.c_name from t_agency_jylog as a LEFT JOIN t_product_model as m ON a.c_mcode=m.c_mcode where a.c_ucode='$ucode' AND a.c_acode='$acode' AND a.c_pcode='$pcode' ORDER BY a.c_addtime desc LIMIT 3";

        	$produceinfo[$key]['buylist'] = $db->query($sql1);
        }

        $data = Page($pageIndex, $pageCount, $count, $produceinfo);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 查询分销商单个产品消费详情
	 * @param ucode,agentucode,pcode
	 */
	public function GetOneProductinfo($parr)
	{
		$pw1['c_ucode'] = $parr['agentucode'];
		$pw1['c_ishow'] = 1;
		$pw1['c_isdele'] = 1;
		$pw1['c_isagent'] = 2;
		$pw1['c_agent_pcode'] = $parr['pcode'];
		$field = "c_ucode,c_name,c_pimg,c_price,c_agent_pcode,c_num,c_salesnum";
		$produceinfo = M('Product')->field($field)->where($pw1)->find();
		if (!$produceinfo) {
			return Message(3000,'没有相关数据信息');
		}

		$db = M('');
		$ucode = $produceinfo['c_ucode'];
    	$acode = $parr['ucode'];
    	$pcode = $produceinfo['c_agent_pcode'];
    	$sql = "SELECT sum(c_money) as buymoney,sum(c_num) as buynum from t_agency_jylog where c_ucode='$ucode' AND c_acode='$acode' AND c_pcode='$pcode' ";
    	$result = $db->query($sql);

    	$produceinfo['c_pimg'] = GetHost().'/'.$produceinfo['c_pimg'];
    	$produceinfo['kcnum'] = $produceinfo['c_num'] - $produceinfo['c_salesnum'];
    	$produceinfo['buynum'] = $result[0]['buynum'];
        $produceinfo['buymoney'] = $result[0]['buymoney'];
        return MessageInfo(0,"查询成功",$produceinfo);
	}

	/**
	 *  代理商购买全部记录
	 *  @param ucode,agentucode,pcode,pagesize,pageindex
	 */
	public function BuyProductList($parr){
		$pageSize = $parr['pagesize'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['a.c_acode'] = $parr['ucode'];
        $w['a.c_ucode'] = $parr['agentucode'];
        $w['a.c_pcode'] = $parr['pcode'];

        $field = "a.c_price,a.c_num,a.c_price,a.c_addtime,m.c_name";
        $join = "left join t_product_model as m on a.c_mcode=m.c_mcode";

        $buyinfo = M('Agency_jylog as a')->field($field)->join($join)->where($w)->limit($countPage, $pageSize)->order('a.c_id desc')->select();

		$count = M('Agency_jylog as a')->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$buyinfo) {
            $buyinfo = array();
            $data = Page($pageIndex, $pageCount, $count, $buyinfo);
            return MessageInfo(0, "查询成功", $data);
        }

        $data = Page($pageIndex, $pageCount, $count, $buyinfo);
		return MessageInfo(0,"查询成功",$data);
	}

	/**
	 * 查询单个简单的产品信息
	 * @param pcode,ucode,bag_code
	 */
	public function GetOneProInfo($parr)
	{
		$pw1['c_ucode'] = $parr['ucode'];
		$pw1['c_pcode'] = $parr['pcode'];
		$pw1['c_isdele'] = 1;
		$pw1['c_ishow'] = 1;
		$field = "c_ucode,c_name,c_pimg,c_price,c_agent_pcode,c_num,c_salesnum";
		$data = M('Product')->field($field)->where($pw1)->find();
		$data['c_pimg'] = GetHost().'/'.$data['c_pimg'];

		$w['c_ucode'] = $parr['ucode'];
		$w['c_pcode'] = $parr['pcode'];
		$w['c_bag_code'] = $parr['bag_code'];
		$data['c_status'] = M('Agency_bag_product')->where($w)->getField('c_status');

		$disw['c_pcode'] = $parr['pcode'];
        $disw['c_bag_code'] = $parr['bag_code'];
        $disdata = M('Agency_product_dis')->where($disw)->order('c_grade asc')->select();
        $data['disdata'] = $disdata;
		return MessageInfo(0,"查询成功",$data);
	}

	/**
     *  获取产品详情
     *  @param  pcode,bag_code
     *      
     */
    function GetProduceInfo($parr) {
        $ucode = $parr['ucode'];
        $where['c_pcode'] = $parr['pcode'];

        $where['c_isdele'] = 1;
        $data = M('Product')->where($where)->find();
        if (!$data) {
            return Message(1017, '数据为空');
        }

        $data['c_pimg'] = GetHost() . '/' . $data['c_pimg'];

        $desc = $data['c_desc'];
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        $data['c_desc'] = str_replace($qian,$hou,$desc);

        $imgwhere['c_pcode'] = $parr['pcode'];
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

        //查询价格区间
        $disw['c_bag_code'] = $parr['bag_code'];
        $disw['c_pcode'] = $parr['pcode'];

        $dis_arr = M('Agency_product_dis')->field('c_grade,c_discount')->where($disw)->select();

        $b = array();

        foreach ($dis_arr as $key => $value) {
        	$a = $value['c_discount'];
        	$b = array_merge($a,$b);
        	sort($b);
        }

        $min_price = sprintf('%.2f',$date['c_price'] * $b[0]);
        $max_disprice = sprintf('%.2f',$date['c_price'] * $b[count($b)]);

        if(!empty($min_price) && !empty($max_price)){
        	$date['c_price'] = "￥".$min_price." ~ ￥".$max_price;
        }

        //统计评论条数
        $whereinfo['c_pcode'] = $parr['pcode'];
        $data['comment_num'] = M('product_score')->where($whereinfo)->count();   

        return MessageInfo(0, '查询成功', $data);
    }

    //获取部份商品评论信息
    public function GetScore($parr) {
        $pcode = $parr['pcode'];

        $whereinfo['a.c_pcode'] = $pcode;

        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $order = 'a.c_addtime desc';
        $list = M('product_score as a')->join($join)->where($whereinfo)->field($field)->order($order)->find();

        $count = M('product_score as a')->join($join)->where($whereinfo)->count();

        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        //修改评论时间
        $list['c_addtime'] = date('Y-m-d H:i', strtotime($list['c_addtime']));

        $list['c_pimg'] = GetHost() . '/' . $list['c_pimg'];

        $list['c_headimg'] = GetHost() . '/' . $list['c_headimg'];
        //查询评论图片
        $where1['c_regionid'] = $list['c_id'];
        $where1['c_sourceid'] = 3;
        $field = 'c_img,c_thumbnail_img';
        $imglist = M('Resource_img')->where($where1)->field($field)->select();

        foreach ($imglist as $key1 => $value1) {
            $imglist[$key1]['c_img'] = GetHost() . '/' . $value1['c_img'];
            $imglist[$key1]['c_thumbnail_img'] = GetHost() . '/' . $value1['c_img'];
        }
        $list["imglist"] = $imglist;
        $list["count_num"] = $count;
        
        return MessageInfo(0, '查询成功', $list);
    }

    //获取全部商品评论信息
    public function GetAllScore($parr) {
        $pcode = $parr['pcode'];

        $whereinfo['a.c_pcode'] = $pcode;

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

        $count = M('product_score as a')->join($join)->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
        	$list = array();
        	$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            //修改评论时间
            $list[$key]['c_addtime'] = date('Y-m-d H:i', strtotime($value['c_addtime']));

            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];

            $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            //查询评论图片
            $where1['c_regionid'] = $value['c_id'];
            $where1['c_sourceid'] = 3;
            $field = 'c_img,c_thumbnail_img';
            $imglist = M('Resource_img')->where($where1)->field($field)->select();

            foreach ($imglist as $key1 => $value1) {
                $imglist[$key1]['c_img'] = GetHost() . '/' . $value1['c_img'];
                $imglist[$key1]['c_thumbnail_img'] = GetHost() . '/' . $value1['c_img'];
            }
            $list[$key]["imglist"] = $imglist;
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }
}