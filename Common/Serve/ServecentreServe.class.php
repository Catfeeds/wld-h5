<?php

/**
 * 服务中心及五公里商圈相关数据
 */
class ServecentreServe {

	//查询用户地址信息
	public function GetLocation($ucode){
		$w['c_ucode'] = $ucode;
        $localinfo = M('User_local')->where($w)->find();

        $longitude = $localinfo['c_longitude'];
        $latitude = $localinfo['c_latitude'];
        $address = $localinfo['c_address'];

        if (empty($longitude) || empty($latitude)  || $longitude == '0.0' || $latitude == '0.0') {
        	$localtion = GetAreafromIp();
            $longitude = $localtion['longitude'];
            $latitude = $localtion['latitude'];
        }

        $returndata['longitude'] = $longitude;
        $returndata['latitude'] = $latitude;
        $returndata['address'] = $address;

        return MessageInfo(0,"查询成功",$returndata);
	}

	//添加五公里商圈记录
	public function Addlogs($parr){
		$data['c_ucode'] = $parr['ucode'];
        $data['c_behavior'] = $parr['behavior'];
        $data['c_regionid'] = $parr['regionid'];
        $data['c_tag'] = $parr['tag'];
        $data['c_tagvalue'] = $parr['tagvalue'];
        $data['c_longitude'] = $parr['longitude'];
        $data['c_latitude'] = $parr['latitude'];
        $data['c_address'] = $parr['address'];
        $data['c_addtime'] = $parr['addtime'];

        $result = M('Business_area')->add($data);
        if (!$result) {
            return Message(1020,'记录创建失败');
        }

        return Message(0,'记录创建成功');
	}

    //判断返回字符串，为空返回0或者0.00
    public function IsNullNum($str, $sign) {
        if ($sign == 1) {
            if (empty($str)) {
                $str = 0.00;
            }
        } else {
            if (empty($str)) {
                $str = 0;
            }
        }
        return $str;
    }

	//获取横向菜单数据
	public function Getmenu($parr){
		$ucode = $parr['ucode'];

		//可用余额
		$w['c_ucode'] = $ucode;
		$userinfo = M('Users')->where($w)->field('c_money,c_shop')->find();
		$data['isshop'] = $userinfo['c_shop'];
		$data['balance'] = $this->IsNullNum($userinfo['c_money'],1);

		$db = M('');
		//已经提现金额
		$sql = "SELECT IFNULL(sum(c_money),0) as outmoney from t_users_drawing where c_ucode='$ucode' AND c_state=1";
		$result = $db->query($sql);
		$outmoney = $result[0]['outmoney'];
		$data['outmoney'] = $outmoney;
        $data['jsurl'] = GetHost(1).'/index.php/Home/Balance/index';

		//推广佣金
		$sql = "SELECT IFNULL(sum(a.c_rebate),0) as rebates from t_order_details as a inner join t_order as b on a.c_orderid=b.c_orderid where a.c_pucode='$ucode' and b.c_deliverystate=5 ";
		$result = $db->query($sql);
		$rebate = number_format($result[0]['rebates'], 2);
		$data['rebate'] = $rebate;
        if($rebate == 0){
            $data['msg1'] = '暂无推广获利';
        }

		//已经推广次数
		$sql = "SELECT IFNULL(sum(a.c_tgnum),0) as tgnum from t_users_spread as a inner join t_product as b on b.c_pcode=a.c_pcode where a.c_ucode='$ucode'  and a.c_isdele=1 and b.c_ishow=1 and b.c_isdele=1 ";
		$result = $db->query($sql);
		$tgnum = $this->IsNullNum($result[0]['tgnum'],2);
		$data['tgnum'] = $tgnum;

		//空间访问量、粉丝量
		$spaceinfo = M('Users_date')->where($w)->find();
		$data['pv'] = $this->IsNullNum($spaceinfo['c_pv'],2);
		$data['attention'] = $this->IsNullNum($spaceinfo['c_attention'],2);
        if($data['pv'] == 0){
             $data['msg2'] = '暂时没人来过，要更加勤奋哦！';
        }

		//商家中心
		if($userinfo['c_shop'] == 1){
			//推广佣金
			$sql = "SELECT count(*) as dfh from t_order where c_acode='$ucode' AND c_pay_state=1 AND c_order_state=2 AND c_deliverystate=0 ";
			$result = $db->query($sql);
			$dfh = $result[0]['dfh'];
			$data['dfh'] = $this->IsNullNum($dfh,2);

			//商家推荐会员
			$sql = "SELECT count(*) as tjhy from t_users_tuijian where c_pcode='$ucode'";
			$result = $db->query($sql);
			$tjhy = $result[0]['tjhy'];
			$data['tjhy'] = $this->IsNullNum($tjhy,2);
            if($data['dfh'] == 0){
                $data['msg3'] = '暂无相关订单!';
            }

            $data['orderurl'] = GetHost(1).'/index.php/Home/Store/orderlist';
		}

        $data['mystores'] = GetHost().'/Uploads/tempimg/mystores.png';
		return MessageInfo(0,'查询成功',$data);
	}

	//查询我的足迹数据
	public function Myfoot($parr){
		$ucode = $parr['ucode'];
		$list = array();

		//查询空间访问记录
		$w['a.c_ucode'] = $ucode;
        // $w['b.c_shop'] = 1;
		$w['a.c_type'] = 2;

        $field = "a.c_vucode,GROUP_CONCAT(DISTINCT a.c_addtime ORDER BY a.c_addtime DESC SEPARATOR '|') as lasttime";
        // $join = 'left join t_users as b on a.c_vucode=b.c_ucode';
		$spacelog = M('Users_spacelog as a')->where($w)->field($field)->order('lasttime desc')->group('a.c_vucode')->limit(4)->select();

		if(!empty($spacelog)){
            foreach ($spacelog as $key => $value) {
                $uw['c_ucode'] = $value['c_vucode'];
                $userinfo = M('Users')->where($uw)->field('c_nickname,c_headimg')->find();
                $spacelog[$key]['c_nickname'] = $userinfo['c_nickname'];
                $spacelog[$key]['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];

                //确定用户是否为商家或者店铺
                $lw['c_ucode'] = $value['c_vucode'];
                $isfixed = M('User_local')->where($lw)->getField('c_isfixed');

                $jumptype = $this->Userjump($value['c_shop'],$isfixed);

                $spacelog[$key]['jumptype'] =  $jumptype;
            }

            $lasttime = explode('|', $spacelog[0]['lasttime']);

			$list['spacelog_time'] = IGD('Resourcev2','Trade')->GetShowTime($lasttime[0],0);
			$list['spacelog'] = $spacelog;
		}else{
			$list['spacelog_time'] = '';
			$list['spacelog'] = array();
		}

		//查询商品访问记录
		$w1['l.c_ucode'] = $ucode;
        $w1['p.c_ishow'] = 1;
        $w1['p.c_isdele'] = 1;

		$field = "p.*,GROUP_CONCAT(DISTINCT l.c_addtime ORDER BY l.c_addtime DESC SEPARATOR '|') as lasttime";
		$order = 'lasttime desc';
		$join = 'inner join t_product as p on p.c_pcode=l.c_pcode';
		$productlog = M('Product_visit as l')->field($field)->join($join)->where($w1)->order($order)->group('l.c_pcode')->limit(1)->select();

        foreach ($productlog as $key => $value) {
            $productlog[$key]['c_pimg'] =  GetHost() . '/' . $value['c_pimg'];
        }

		if(!empty($productlog)){
            $lasttime = explode('|', $productlog[0]['lasttime']);

			$list['productlog_time'] = IGD('Resourcev2','Trade')->GetShowTime($lasttime[0],2);
			$list['productlog'] = $productlog;
		}else{
			$list['productlog_time'] = '';
			$list['productlog'] = array();
		}

		return MessageInfo(0,'查询成功',$list);
	}

    //转换显示时间
    public function TransferTime($oldtime){
        $time = time() - strtotime($oldtime);
        $hour = floor($time / 60 / 60 / 24);

        if($hour <= 3){
            $oldtime = IGD('Resourcev2','Trade')->GetShowTime($oldtime,0);
        }

        return $oldtime;
    }

	//查询五公里商圈数据
	public function Datalogs($parr){
		$ucode = $parr['ucode'];

		$pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        //查询自己位置
        // $result = $this->GetLocation($ucode);
        // $localtion = $result['data'];

        // $longitude = $localtion['longitude'];
        // $latitude = $localtion['latitude'];

        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        if (empty($longitude) || empty($latitude)  || $longitude == '0.0' || $latitude == '0.0') {
            $localtion = GetAreafromIp();
            $longitude = $localtion['longitude'];
            $latitude = $localtion['latitude'];
        }

        //查询条件、排序
        $where[] = array("b.c_ucode<>'".$ucode."'");
        $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-b.c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(b.c_latitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-b.c_longitude)/360),2)))) <= 5';

        $order = 'b.c_addtime desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((b.c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((b.c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (b.c_longitude * 3.1415) / 180 ) ) * 6380 asc';

        $join = 'inner join t_business_area as b on a.c_ucode=b.c_ucode';
        $field = "a.c_ucode,a.c_nickname,a.c_headimg,a.c_shop,b.*";

        $list = M('Users as a')->join($join)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('Users as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (count($list) == 0) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        //组装数据
        $arr = array();
        foreach ($list as $key => $value) {
        	$count = count($arr);
            $tempcount = 0;
            if ($count >= 1) {
                $tempcount = $count;
            }

            switch ($value['c_behavior']) {
                case 1:     //发布资源
                    $result = $this->Resources($longitude ,$latitude ,$value, $arr, $tempcount);

                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }
                    break;
                case 2:     //上传商品
                    $result = $this->UploadGoods($longitude ,$latitude ,$value, $arr, $tempcount);

                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }

                    break;
                case 3:     //商家红包
                	$result = $this->RedPacket($longitude ,$latitude ,$value, $arr, $tempcount);

                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }
                    break;
                case 4:     //购买商品
                	$result = $this->BuyGoods($longitude ,$latitude ,$value, $arr, $tempcount);

                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }
                    break;
                case 5:     //扫码支付
                    $result = $this-> QRCode($longitude ,$latitude ,$value, $arr, $tempcount);
                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }
                    break;
                default:
                    break;
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $arr);
        return MessageInfo(0, '查询成功', $data);
	}

    //判断用户头像跳转
    public function Userjump($isshop,$isfixed){
        $jumptype = 0;

        if($isshop == 1){
            $jumptype = 1;

            if($isfixed == 1){
                $jumptype = 2;
            }
        }

        return $jumptype;
    }

	//5公里数据 发布资源
	public function Resources($longitude ,$latitude ,$value, $arr, $tempcount){
		//查询资源关联数据
		$w['c_id'] = $value['c_regionid'];
		$resourceinfo = M('Resource')->where($w)->find();

		$resourceimg = IGD('Resourcev2','Trade')->get_imglist($resourceinfo['c_id']);
		$resourcepro = IGD('Resourcev2','Trade')->get_product($resourceinfo['c_id']);

		$headimg = GetHost() . '/' . $value['c_headimg'];

        if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
            $strb = "未知距离";
        } else {
            $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
            $str1 = sprintf("%.2f", $str1);
            if ($str1 < 1) {
                $a = bcmul($str1, 1000, 2);
                if ($a <= 10) {
                    $strb = "距离您"."＜10m";
                } else if ($a > 10 && $a <= 100) {
                    $strb = "距离您"."＜100m";
                } else {
                    $strb = "距离您".sprintf("%.0f", $a) . "m";
                }
            } else {
                $strb =  "距离您". $str1 . "km";
            }
        }

        //确定用户是否为商家或者店铺
        $lw['c_ucode'] = $value['c_ucode'];
        $isfixed = M('User_local')->where($lw)->getField('c_isfixed');

        $jumptype = $this->Userjump($value['c_shop'],$isfixed);

        //组装返回数据
        $arr[$tempcount]['behavior'] = $value['c_behavior'];//1-发布资源，2-上传商品，3-商家红包，4-购买商品，5-扫码支付
        $arr[$tempcount]['c_ucode'] = $value['c_ucode'];
        $arr[$tempcount]['c_nickname'] = $value['c_nickname'];
        $arr[$tempcount]['c_headimg'] = $headimg;
        $arr[$tempcount]['addtime'] = $this->TransferTime($value['c_addtime']);
        $arr[$tempcount]['localtion'] = $strb;
        $arr[$tempcount]['pimg'] = $resourcepro[0]['c_pimg'];
        $arr[$tempcount]['pname'] = $resourcepro[0]['c_name'];
        $arr[$tempcount]['property'] = $resourcepro[0]['c_price'];
        $arr[$tempcount]['property1'] = '';
        $arr[$tempcount]['property2'] = '';
        $arr[$tempcount]['property3'] = '';
        $arr[$tempcount]['c_source'] = '';
        $arr[$tempcount]['tag'] = $value['c_tag'];//跳转目标
        $arr[$tempcount]['tagvalue'] = $value['c_tagvalue'];
        $arr[$tempcount]['c_content'] = $resourceinfo['c_content'];
        $arr[$tempcount]['imglist'] = $resourceimg;
        $arr[$tempcount]['jumptype'] = $jumptype;

        return MessageInfo(0, "获取成功", $arr);
	}

	//5公里数据 上传商品
	public function UploadGoods($longitude ,$latitude ,$value, $arr, $tempcount){
		//查询资源关联数据
		$w['c_pcode'] = $value['c_regionid'];
		$productinfo = M('Product')->where($w)->find();

        if($productinfo['c_ishow'] == 2 || $productinfo['c_isdele'] == 2){
            return MessageInfo(0, "获取成功", $arr);
        }

		if(!empty($productinfo['c_pimg'])){
			$pimg = GetHost() . '/' . $productinfo['c_pimg'];
		}

		$headimg = GetHost() . '/' . $value['c_headimg'];

        //确定用户是否为商家或者店铺
        $lw['c_ucode'] = $value['c_ucode'];
        $isfixed = M('User_local')->where($lw)->getField('c_isfixed');

        $jumptype = $this->Userjump($value['c_shop'],$isfixed);

        //组装返回数据
        $arr[$tempcount]['behavior'] = $value['c_behavior'];//1-发布资源，2-上传商品，3-商家红包，4-购买商品，5-扫码支付
        $arr[$tempcount]['c_ucode'] = $value['c_ucode'];
        $arr[$tempcount]['c_nickname'] = $value['c_nickname'];
        $arr[$tempcount]['c_headimg'] = $headimg;
        $arr[$tempcount]['addtime'] = $this->TransferTime($value['c_addtime']);
        $arr[$tempcount]['localtion'] = $value['c_address'];
        $arr[$tempcount]['pimg'] = $pimg;
        $arr[$tempcount]['pname'] = $productinfo['c_name'];
        $arr[$tempcount]['property'] = $productinfo['c_isagent'];
        $arr[$tempcount]['property1'] = $productinfo['c_isrebate'];
        $arr[$tempcount]['property2'] = $productinfo['c_isspread'];
        $arr[$tempcount]['property3'] = $productinfo['c_isfree'];
        $arr[$tempcount]['c_source'] = $productinfo['c_source'];
        $arr[$tempcount]['tag'] = $value['c_tag'];//跳转目标
        $arr[$tempcount]['tagvalue'] = $value['c_tagvalue'];
        $arr[$tempcount]['c_content'] = "上传了一款新商品";
        $arr[$tempcount]['imglist'] = array();
        $arr[$tempcount]['jumptype'] = $jumptype;

        return MessageInfo(0, "获取成功", $arr);
	}

	//5公里数据 商家红包
	public function RedPacket($longitude ,$latitude ,$value, $arr, $tempcount){
		//查询资源关联数据
		$w['c_id'] = $value['c_regionid'];
        if ($value['c_tagvalue'] == 1) {
            $Activity_moneylog = M('Activity_moneylog');
        } else {
            $Activity_moneylog = M('Activity_log');
        }
		$resourceinfo = $Activity_moneylog->where($w)->find();

		$pvalue = $resourceinfo['c_value'];

		$headimg = GetHost() . '/' . $value['c_headimg'];

        if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
            $strb = "未知距离";
        } else {
            $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
            $str1 = sprintf("%.2f", $str1);
            if ($str1 < 1) {
                $a = bcmul($str1, 1000, 2);
                if ($a <= 10) {
                    $strb = "距离您"."＜10m";
                } else if ($a > 10 && $a <= 100) {
                    $strb = "距离您"."＜100m";
                } else {
                    $strb = "距离您".sprintf("%.0f", $a) . "m";
                }
            } else {
                $strb =  "距离您". $str1 . "km";
            }
        }

        //确定用户是否为商家或者店铺
        $lw['c_ucode'] = $value['c_ucode'];
        $isfixed = M('User_local')->where($lw)->getField('c_isfixed');

        $jumptype = $this->Userjump($value['c_shop'],$isfixed);

        //组装返回数据
        $arr[$tempcount]['behavior'] = $value['c_behavior'];//1-发布资源，2-上传商品，3-商家红包，4-购买商品，5-扫码支付
        $arr[$tempcount]['c_ucode'] = $value['c_ucode'];
        $arr[$tempcount]['c_nickname'] = $value['c_nickname'];
        $arr[$tempcount]['c_headimg'] = $headimg;
        $arr[$tempcount]['addtime'] = $this->TransferTime($value['c_addtime']);
        $arr[$tempcount]['localtion'] = $strb;
        if ($value['c_tagvalue'] == 1) {
            $arr[$tempcount]['pimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg6.png';
            $arr[$tempcount]['pname'] = "在发现抢到一个 普通红包";
        } else {
            $arr[$tempcount]['pimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg7.png';
            $arr[$tempcount]['pname'] = "在发现抢到一个 订单红包";
        }
        $arr[$tempcount]['property'] = '￥'.$pvalue;
        $arr[$tempcount]['property1'] = '';
        $arr[$tempcount]['property2'] = '';
        $arr[$tempcount]['property3'] = '';
        $arr[$tempcount]['c_source'] = '';
        $arr[$tempcount]['tag'] = $value['c_tag'];//跳转目标
        $arr[$tempcount]['tagvalue'] = $value['c_tagvalue'];
        $arr[$tempcount]['c_content'] = '';
        $arr[$tempcount]['imglist'] = array();
        $arr[$tempcount]['jumptype'] = $jumptype;

        return MessageInfo(0, "获取成功", $arr);
	}

	//5公里数据 购买商品
	public function BuyGoods($longitude ,$latitude ,$value, $arr, $tempcount){
		//查询资源关联数据
		$w['c_pcode'] = $value['c_regionid'];
		$productinfo = M('Product')->where($w)->find();

        if($productinfo['c_ishow'] == 2 || $productinfo['c_isdele'] == 2){
            return MessageInfo(0, "获取成功", $arr);
        }

		if(!empty($productinfo['c_pimg'])){
			$pimg = GetHost() . '/' . $productinfo['c_pimg'];
		}

		$headimg = GetHost() . '/' . $value['c_headimg'];

        //确定用户是否为商家或者店铺
        $lw['c_ucode'] = $value['c_ucode'];
        $isfixed = M('User_local')->where($lw)->getField('c_isfixed');

        $jumptype = $this->Userjump($value['c_shop'],$isfixed);

        //组装返回数据
        $arr[$tempcount]['behavior'] = $value['c_behavior'];//1-发布资源，2-上传商品，3-商家红包，4-购买商品，5-扫码支付
        $arr[$tempcount]['c_ucode'] = $value['c_ucode'];
        $arr[$tempcount]['c_nickname'] = $value['c_nickname'];
        $arr[$tempcount]['c_headimg'] = $headimg;
        $arr[$tempcount]['addtime'] = $this->TransferTime($value['c_addtime']);
        $arr[$tempcount]['localtion'] = $value['c_address'];
        $arr[$tempcount]['pimg'] = $pimg;
        $arr[$tempcount]['pname'] = $productinfo['c_name'];
        $arr[$tempcount]['property'] = $productinfo['c_isagent'];
        $arr[$tempcount]['property1'] = $productinfo['c_isrebate'];
        $arr[$tempcount]['property2'] = $productinfo['c_isspread'];
        $arr[$tempcount]['property3'] = $productinfo['c_isfree'];
        $arr[$tempcount]['c_source'] = $productinfo['c_source'];
        $arr[$tempcount]['tag'] = $value['c_tag'];//跳转目标
        $arr[$tempcount]['tagvalue'] = $value['c_tagvalue'];
        $arr[$tempcount]['c_content'] = "购买了一款新商品";
        $arr[$tempcount]['imglist'] = array();
        $arr[$tempcount]['jumptype'] = $jumptype;

        return MessageInfo(0, "获取成功", $arr);
	}

	//5公里数据 扫码支付
	public function QRCode($longitude ,$latitude ,$value, $arr, $tempcount){
		//查询资源关联数据
		$w['c_ncode'] = $value['c_regionid'];
		$resourceinfo = M('Scanpay')->where($w)->find();

		$pvalue = $resourceinfo['c_value'];

		$headimg = GetHost() . '/' . $value['c_headimg'];

        if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
            $strb = "未知距离";
        } else {
            $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
            $str1 = sprintf("%.2f", $str1);
            if ($str1 < 1) {
                $a = bcmul($str1, 1000, 2);
                if ($a <= 10) {
                    $strb = "距离您"."＜10m";
                } else if ($a > 10 && $a <= 100) {
                    $strb = "距离您"."＜100m";
                } else {
                    $strb = "距离您".sprintf("%.0f", $a) . "m";
                }
            } else {
                $strb =  "距离您". $str1 . "km";
            }
        }

        $acode = $value['c_tagvalue'];
        $where['c_ucode'] = $acode;
        $aname = M('Users')->where($where)->getField('c_nickname');

        //确定用户是否为商家或者店铺
        $lw['c_ucode'] = $value['c_ucode'];
        $isfixed = M('User_local')->where($lw)->getField('c_isfixed');

        $jumptype = $this->Userjump($value['c_shop'],$isfixed);

        //组装返回数据
        $arr[$tempcount]['behavior'] = $value['c_behavior'];//1-发布资源，2-上传商品，3-商家红包，4-购买商品，5-扫码支付
        $arr[$tempcount]['c_ucode'] = $value['c_ucode'];
        $arr[$tempcount]['c_nickname'] = $value['c_nickname'];
        $arr[$tempcount]['c_headimg'] = $headimg;
        $arr[$tempcount]['addtime'] = $this->TransferTime($value['c_addtime']);
        $arr[$tempcount]['localtion'] = $strb;
        $arr[$tempcount]['pimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg8.png';;
        $arr[$tempcount]['pname'] = $aname;
        $arr[$tempcount]['property'] = '';
        $arr[$tempcount]['property1'] = '';
        $arr[$tempcount]['property2'] = '';
        $arr[$tempcount]['property3'] = '';
        $arr[$tempcount]['c_source'] = '';
        $arr[$tempcount]['tag'] = $value['c_tag'];//跳转目标
        $arr[$tempcount]['tagvalue'] = $value['c_tagvalue'];
        $arr[$tempcount]['c_content'] = '';
        $arr[$tempcount]['imglist'] = array();
        $arr[$tempcount]['jumptype'] = $jumptype;

        return MessageInfo(0, "获取成功", $arr);
	}

	/**
	 * 访问的商家足迹
	 * @param ucode,pageindex,pagesize
	 */
	function VisitShopinfo($parr)
	{
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $whereinfo['a.c_ucode'] = $parr['ucode'];
        $whereinfo['a.c_type'] = 2;
        // $whereinfo['b.c_shop'] = 1;
        // $join = 'left join t_users as b on a.c_vucode=b.c_ucode';
        $field = "a.c_vucode,a.c_addtime,a.c_ucode,a.c_type,GROUP_CONCAT(DISTINCT a.c_addtime ORDER BY a.c_addtime DESC SEPARATOR '|') as lasttime";
        $order = 'lasttime desc';
        $list = M('Users_spacelog as a')->where($whereinfo)->field($field)->order($order)->group('a.c_vucode')->limit($countPage, $pageSize)->select();

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $uw['c_ucode'] = $value['c_vucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_headimg')->find();
            $list[$key]['c_nickname'] = $userinfo['c_nickname'];
            $list[$key]['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];

        	//计算时间
        	$lasttime = explode('|', $value['lasttime']);
            $rtime = date('Y-m-d', strtotime($lasttime[0]));
            $ttime = date('Y-m-d', time());
            $dectime = intval((strtotime($ttime) - strtotime($rtime))/86400);
            if ($dectime == 0) {
            	$list[$key]['timestr'] = '今天';
            } else if ($dectime == 1) {
            	$list[$key]['timestr'] = '一天前';
            } else if ($dectime == 2) {
            	$list[$key]['timestr'] = '两天前';
            } else if ($dectime == 3) {
            	$list[$key]['timestr'] = '三天前';
            } else {
            	$list[$key]['timestr'] = '';
            }
            $list[$key]['dectime'] = $dectime;
            $list[$key]['ytime'] = date('Y', strtotime($lasttime[0]));
            $list[$key]['mtime'] = date('m/d', strtotime($lasttime[0]));
            $list[$key]['htime'] = date('H:i:s', strtotime($lasttime[0]));
        }

        $count = M('Users_spacelog as a')->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 看过的商品足迹
	 * @param ucode
	 */
	function VisitGoodsinfo($parr)
	{
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $whereinfo['a.c_ucode'] = $parr['ucode'];
        $whereinfo['b.c_ishow'] = 1;
        $whereinfo['b.c_isdele'] = 1;
        $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
        $field = "a.c_addtime,b.c_pcode,b.c_name,b.c_pimg,b.c_price,b.c_isagent,b.c_isrebate,b.c_isspread,b.c_source,GROUP_CONCAT(DISTINCT a.c_addtime ORDER BY a.c_addtime DESC SEPARATOR '|') as lasttime";
        $order = 'lasttime desc';
        $list = M('Product_visit as a')->join($join)->where($whereinfo)->field($field)->order($order)->group('a.c_pcode')->limit($countPage, $pageSize)->select();

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
        	$lasttime = explode('|', $value['lasttime']);
        	$list[$key]['signtime'] = IGD('Resourcev2','Trade')->GetShowTime($lasttime[0], 0);
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
        }

        $count = M('Product_visit as a')->join($join)->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}
}