<?php

/**
 * 获取发现数据
 */
class GetdataInfo {
	/**
     *  获取用户列表
     *  @param pageindex,pagesize,longitude,latitude, usertype
     *
     */
    function CoalitionUserList($parr,$usertype) {
        //return $this->GetLocalUser($parr,$usertype);     
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        $pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $juli = $parr['juli'];//固定店铺使用到

        if($usertype == 1){
            $where[] = array("c_latitude1 is not null and c_longitude1 is not null and c_isfixed1=0");
        }else if($usertype == 2){
            $where[] = array("c_latitude1 is not null and c_longitude1 is not null  and c_isfixed1=1");
        }else if ($usertype == 3) {
            $where['c_shop'] = 1;
            $where[] = array("c_latitude1 is not null and c_longitude1 is not null");
        }else{
            $where[] = array("c_latitude1 is not null and c_longitude1 is not null");
        }

        if (!empty($longitude) && !empty($latitude)) {
            if ($juli > 0) {
                $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-c_latitude1)/360),2)+COS(PI()*33.07078170776367/180)* COS(c_latitude1 * PI()/180)*POW(SIN(PI()*(' . $longitude . '-c_longitude1)/360),2)))) <= ' .  $juli . '';
            }
            $order = 'case when ifnull(c_latitude1,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((c_latitude1 * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((c_latitude1 * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (c_longitude1 * 3.1415) / 180 ) ) * 6380 asc';
        } else {
            $order = 'c_id desc';
        }

        $field = "c_ucode,c_nickname,c_signature,c_province,c_sex,c_city,c_region,c_headimg,c_tab,c_trade,c_shop,c_latitude1,c_longitude1,c_address1,c_isfixed1,c_shopnum";

        $list = M('Users')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
            if (empty($value['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
            $list[$key]['c_friendstate'] = 0;

            if (empty($value['c_longitude1']) || empty($value['c_latitude1'])) {
                $strb = "未知距离";
            } else {
                $str1 = GetDistance($longitude, $latitude, $value['c_longitude1'], $value['c_latitude1']);
                $str2 = sprintf("%.2f", $str1);
                if ($str2 < 1) {
                    $a = bcmul($str1, 1000, 2);
                    if ($a <= 10) {
                        $strb = "＜10m";
                    } else if ($a > 10 && $a <= 100) {
                        $strb = "＜100m";
                    } else {
                        $strb = sprintf("%.0f", $a) . "m";
                    }
                } else {
                    $strb = $str2 . "km";
                }
            }
            $list[$key]['c_distance'] = $strb;

            if($value['c_isfixed1'] == 1){
            	$list[$key]['c_shop'] = 2;
            }
        }

        $count = M('Users as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  获取匹配省市用户列表
     *  @param pageindex,pagesize,longitude,latitude, usertype
     *
     */
    function CoalitionUserList1($parr,$usertype) {
        //$parr['order'] = 2;
        //return $this->GetLocalUser($parr,$usertype);
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        $pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        if($usertype == 1){
            $where[] = array("c_latitude1 is not null and c_longitude1 is not null and c_isfixed1=0");
        }else if($usertype == 2){
            $where[] = array("c_latitude1 is not null and c_longitude1 is not null  and c_isfixed1=1");
        }else if ($usertype == 3) {
            $where['c_shop'] = 1;
            // $where[] = array("c_latitude1 is not null and c_longitude1 is not null");
        }else{
            $where[] = array("c_latitude1 is not null and c_longitude1 is not null");
        }

        $city = IGD('Common', 'Redis')->Rediesgetucode('LBS'.$longitude.$latitude);
        if (!$city) {
            $getlocal = file_get_contents("http://api.map.baidu.com/cloudrgc/v1?location=".$latitude.",".$longitude."&geotable_id=135675&coord_type=bd09ll&ak=lIqrLulxigbplnce2Ol5IG46ePXX2KLS");
            $result = objarray_to_array(json_decode($getlocal));
            $city = $result['address_component']['city'];
            $city = str_replace('市', '', $city);
            IGD('Common', 'Redis')->RediesStoreSram('LBS'.$longitude.$latitude, $city,3600);
        }
        if ($city) {
            $order = "(c_city='$city') desc,case when ifnull(c_latitude1,'')='' then 0 else 1 end desc,rand()";
        } else {
           $order = "case when ifnull(c_latitude1,'')='' then 0 else 1 end desc,rand()"; 
        }
        
        $field = "c_ucode,c_nickname,c_signature,c_province,c_sex,c_city,c_region,c_headimg,c_tab,c_trade,c_shop,c_latitude1,c_longitude1,c_address1,c_isfixed1,c_shopnum";

        $list = M('Users')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
            if (empty($value['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
            $list[$key]['c_friendstate'] = 0;

            if (empty($value['c_longitude1']) || empty($value['c_latitude1'])) {
                $strb = "未知距离";
            } else {
                $str1 = GetDistance($longitude, $latitude, $value['c_longitude1'], $value['c_latitude1']);
                $str2 = sprintf("%.2f", $str1);
                if ($str2 < 1) {
                    $a = bcmul($str1, 1000, 2);
                    if ($a <= 10) {
                        $strb = "＜10m";
                    } else if ($a > 10 && $a <= 100) {
                        $strb = "＜100m";
                    } else {
                        $strb = sprintf("%.0f", $a) . "m";
                    }
                } else {
                    $strb = $str2 . "km";
                }
            }
            $list[$key]['c_distance'] = $strb;

            if($value['c_isfixed1'] == 1){
                $list[$key]['c_shop'] = 2;
            }
        }

        $count = M('Users as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  商盟产品查询
     *  @param longitude,latitude,cnum,juli(暂时未用到)
     *
     */
    function CoalitionProductList($parr) {
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];
        $cnum = $parr['cnum'];

        $where['a.c_ishow'] = 1;
        $where['a.c_isdele'] = 1;

        if (!empty($longitude) && !empty($latitude)) {
            if (!empty($parr['juli'])) {
                $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-a.c_latitude1)/360),2)+COS(PI()*33.07078170776367/180)* COS(a.c_latitude1 * PI()/180)*POW(SIN(PI()*(' . $longitude . '-a.c_longitude1)/360),2)))) <= ' . $parr['juli'] . '';
            }
            $order = 'ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((a.c_latitude1 * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((a.c_latitude1 * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (a.c_longitude1 * 3.1415) / 180 ) ) * 6380 asc';
        } else {
            $order = 'a.c_id desc';
        }

        $join = 'left join t_users as u on a.c_ucode=u.c_ucode';
        $field = 'a.c_pcode,a.c_name,a.c_pimg,a.c_longitude1,a.c_latitude1,a.c_address1,u.c_nickname,a.c_source';

        $list = M('Product as a')->join($join)->join($join)->where($where)->field($field)->order($order)->limit($cnum)->select();

        if (count($list) == 0) {
            $list = array();
            return MessageInfo(0, '查询成功', $list);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            if (empty($value['c_longitude1']) || empty($value['c_latitude1'])) {
                $strb = "未知距离";
            } else {
                $str1 = GetDistance($longitude, $latitude, $value['c_longitude1'], $value['c_latitude1']);
                $str1 = sprintf("%.2f", $str1);
                if ($str1 < 1) {
                    $a = bcmul($str1, 1000, 2);
                    if ($a <= 10) {
                        $strb = "＜10m";
                    } else if ($a > 10 && $a <= 100) {
                        $strb = "＜100m";
                    } else {
                        $strb = sprintf("%.0f", $a) . "m";
                    }
                } else {
                    $strb = $str1 . "km";
                }
            }
            $list[$key]['c_distance'] = $strb;
        }

        return MessageInfo(0, '查询成功', $list);
    }

    /**
     * 根据经纬度获取附近用户
     * @param pageindex,pagesize,longitude,latitude,juli
     * @param [type] $usertype [description]
     */
    function GetLocalUser($parr,$usertype) {

        if ($usertype == 1) {  //微商
            $keyname = 'LBS_online_';
        } else if ($usertype == 2) { //实体店
            $keyname = 'LBS_fiexd_';
        } else if ($usertype == 3) { //所有商家
            $keyname = 'LBS_shop_';
        } else { //全部用户
            $keyname = 'LBS_user_';
        }

        // 根据经纬度获取geohash
        $geohash = IGD('Geohash','Lbs')->encode($parr['latitude'],$parr['longitude']);
        // $geohash = substr($geohash,0,20);

        //获取缓存位置排序数据
        $usermap = IGD('Common', 'Redis')->Rediesgetucode($keyname.$geohash);

        if ($parr['longitude'] > 0 && $parr['latitude'] > 0 && empty($usermap)) {
            if (empty($parr['juli'])) {
                $parr['juli'] = 5000;
            }
            
            $usermap = IGD('Local','Lbs')->serach($parr,$usertype);
            IGD('Common', 'Redis')->RediesStoreSram($keyname.$geohash,$usermap,300);
        }
        
        $data = page_array($parr['pagesize'],$parr['pageindex'],$usermap,$parr['order']);

        $list = $data['list'];
        foreach ($list as $key => $value) {
            //查询用户信息
            $field = "c_ucode,c_nickname,c_signature,c_province,c_sex,c_city,c_region,c_headimg,c_tab,c_trade,c_shop,c_latitude1,c_longitude1,c_address1,c_isfixed1,c_shopnum";
            $uw['c_ucode'] = $value['name'];
            $userinfo = M('Users')->where($uw)->field($field)->find();
            if (empty($userinfo['c_headimg'])) {
                $userinfo['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
            }
            $userinfo['c_friendstate'] = 0;

            $str1 = $value['dist'];
            if ($str1 < 1) {
                $a = bcmul($str1, 1000, 2);
                if ($a <= 10) {
                    $strb = "＜10m";
                } else if ($a > 10 && $a <= 100) {
                    $strb = "＜100m";
                } else {
                    $strb = sprintf("%.0f", $a) . "m";
                }
            } else {
                $strb = $str1 . "km";
            }

            $userinfo['c_distance'] = $strb;

            if($userinfo['c_isfixed1'] == 1){
                $userinfo['c_shop'] = 2;
                if ($parr['trade'] == 1) {
                    $w['c_id'] = $userinfo['c_shoptrade'];
                    $tradeimg = M('Industry')->where($w)->getField('c_tradeimg');
                    if($tradeimg){
                        $userinfo['c_headimg'] = GetHost() . '/' .$tradeimg;
                    }else{
                        $userinfo['c_headimg'] = GetHost() . '/Uploads/Activity/shopimg/shopimg.png';
                    }
                }
            }

            $newlist[] = $userinfo;
        }

        $data['list'] = $newlist;
        return MessageInfo(0, '查询成功', $data);
    }
}