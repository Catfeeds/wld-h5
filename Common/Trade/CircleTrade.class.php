<?php
/**
 * 商圈模块
 */
class CircleTrade {
    /**
     *  根据省市名称获取商圈编码
     *  @param province,city
     *
     */
    function Getcirclecode($parr){
        $province = str_replace("省","",$parr['province']);
        $find = '市';
        if(strpos($province,$find)!==false){
            $province = str_replace("市","",$parr['province']);
        }
        $city = str_replace("市","",$parr['city']);
        
        //查询本地商圈信息
        $w['c_circle_name'] = $province;
        $w['c_type'] = 0;
        $provincecode = M('Circle_code')->where($w)->getField('c_code');

        if(!$provincecode){
            $provincecode = "G6Xw7P";
            // return Message(1001,"没有匹配到省份商圈");
        }

        $w1['c_parent_code'] = $provincecode;
        $w1['c_circle_name'] = $city;
        $w1['c_type'] = 1;
        $citycode = M('Circle_code')->where($w1)->getField('c_code');

        if(!$citycode){
            $citycode = "umSXPVTG";
            // return Message(1001,"没有匹配到城市商圈");
        }

        $data['provincecode'] = $provincecode;
        $data['citycode'] = $citycode;

        return MessageInfo(0,"查询成功",$data);
    }

    /**
     *  获取所在商圈
     *  @param ucode
     *
     */
    function Getcircleinfo($parr) {
        $ucode = $parr['ucode'];
        $province = str_replace("省","",$parr['province']);
        $find = '市';
        if(strpos($province,$find)!==false){
            $province = str_replace("市","",$parr['province']);
        }

        $city = str_replace("市","",$parr['city']);
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];

        //判断用户是否访问过商圈
        if($ucode){
            $where['c_ucode'] = $ucode;
            $circle_visit = M('Circle_visit')->where($where)->find();
        }        

        $isremind = 0; //是否提醒切换商圈

        if(empty($provincecode) || empty($citycode)){
            if ($circle_visit) {
                $provincecode = $circle_visit['c_provincecode'];
                $citycode = $circle_visit['c_citycode'];
            } else {
                $param['province'] = $province;
                $param['city'] = $city;
                $result = $this->Getcirclecode($param);
                if($result['code'] != 0){
                    return $result;
                }else{
                    $provincecode = $result['data']['provincecode'];
                    $citycode = $result['data']['citycode'];
                } 
            }
        }

        $circle_where['c_provincecode'] = $provincecode;
        $circle_where['c_citycode'] = $citycode;
        $circle_where['c_status'] = 1;

        //本地存在商圈
        if($province.$city != $circle_visit['c_address'] && $circle_visit && $province.$city){
            $isremind = 1;
        }
        
        $data = M('Circle')->where($circle_where)->find();
        if(!$data){
            return MessageInfo(0,'商圈不存在或者已关闭',$data);
        }

        $data['c_img'] = GetHost() . '/' . $data['c_img'];
        $data['isremind'] = $isremind;

        //获取商圈等级图标
        $lw['c_level'] = $data['c_level'];
        $levelimg = M('Circle_level')->where($lw)->getField('c_levelimg');
        $data['c_levelimg'] = GetHost() . '/' . $levelimg;

        if($isremind == 1){
            $param['province'] = $province;
            $param['city'] = $city;

            $result = $this->Getcirclecode($param);

            if($result['code'] != 0){
                return $result;
            }else{
                $data['provincecode1'] = $result['data']['provincecode'];
                $data['citycode1'] = $result['data']['citycode'];
            }
        }else{
            $data['provincecode1'] = '';
            $data['citycode1'] = '';
        }
       
        //获取当前商圈的省名和市名
        $mw['c_code'] = $provincecode;
        $data['provincename'] = M('Circle_code')->where($mw)->getField('c_circle_name');
        $mw1['c_code'] = $citycode;
        $data['cityname'] = M('Circle_code')->where($mw1)->getField('c_circle_name');        

        //记录用户访问商圈记录
        $vdata['ucode'] = $ucode;
        $vdata['provincecode'] = $provincecode;
        $vdata['citycode'] = $citycode;
        $vdata['address'] = $data['provincename'].$data['cityname'];
        $result = $this->Savevisitcircle($vdata);        

        //临时商家加入商圈方法
        $result = $this->JoinCircle($vdata);

        return MessageInfo(0,'查询成功',$data);
    }

     /**
     *  临时商家加入商圈方法
     *  @param ucode,provincecode,citycode,address
     *
     */
    function JoinCircle($parr) {
        $ucode = $parr['ucode'];
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];
        $address = $parr['address'];

        if(!$ucode || !$provincecode || !$citycode){
            return Message(1001,"用户编码或商圈编码不能为空");
        }
        
        $where['c_ucode'] = $ucode;

        //判断用户身份信息
        $isshop =  M('Users')->where($where)->getField('c_shop');
        if($isshop == 0){
            return Message(0,"普通用户不需加入商圈");
        }
        
        //判断商家是否加入过商圈
        $circle_shop = M('Circle_shop')->where($where)->find();

        if(!$circle_shop){
            $adata['c_ucode'] = $ucode;
            $adata['c_provincecode'] = $provincecode;
            $adata['c_citycode'] = $citycode;
            $adata['c_address'] = $address;
            $adata['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Circle_shop')->add($adata);

            if(!$result){
                return Message(1002,"操作失败");
            }
        }
       
        return Message(0,"操作成功");
    }

     /**
     *  商家加入商圈方法
     *  @param ucode,provincecode,citycode,address
     *
     */
    function ShopJoinCircle($parr) {
        $ucode = $parr['ucode'];
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];
        $address = $parr['address'];

        if(!$ucode || !$provincecode || !$citycode){
            return Message(1001,"用户编码或商圈编码不能为空");
        }
        
        $where['c_ucode'] = $ucode;

        //判断用户身份信息
        $isshop =  M('Users')->where($where)->getField('c_shop');
        if($isshop == 0){
            return Message(0,"普通用户不需加入商圈");
        }

        $adata['c_ucode'] = $ucode;
        $adata['c_provincecode'] = $provincecode;
        $adata['c_citycode'] = $citycode;
        $adata['c_address'] = $address;
        $adata['c_type'] = 2;
        $adata['c_addtime'] = date('Y-m-d H:i:s', time());
        
        //判断商家是否加入过商圈
        $circle_shop = M('Circle_shop')->where($where)->find();
        if(!$circle_shop){            
            $result = M('Circle_shop')->add($adata);
            if(!$result){
                return Message(1002,"操作失败");
            }
        } else {
            if ($circle_shop['c_type'] == 1) {
                $result = M('Circle_shop')->where($where)->save($adata);
                if(!$result){
                    return Message(1002,"操作失败");
                }
            }

        }
       
        return Message(0,"操作成功");
    }

    /**
     *  记录用户访问的商圈信息
     *  @param ucode,provincecode,citycode,address
     *
     */
    function Savevisitcircle($parr) {
        $ucode = $parr['ucode'];
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];
        $address = $parr['address'];

        if(!$ucode || !$provincecode || !$citycode){
            return Message(1001,"用户编码或商圈编码不能为空");
        }

        //判断用户是否存在访问商圈记录
        $where['c_ucode'] = $ucode;
        $circle_visit = M('Circle_visit')->where($where)->find();

        if($circle_visit){
            $sdata['c_provincecode'] = $provincecode;
            $sdata['c_citycode'] = $citycode;
            $sdata['c_address'] = $address;
            $sdata['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Circle_visit')->where($where)->save($sdata);
        }else{
            $adata['c_ucode'] = $ucode;
            $adata['c_provincecode'] = $provincecode;
            $adata['c_citycode'] = $citycode;
            $adata['c_address'] = $address;
            $adata['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = M('Circle_visit')->add($adata);
        }
        
        if(!$result){
            return Message(1002,"操作失败");
        }

        return Message(0,"操作成功");
    }

    /**
     *  获取商圈所有省列表
     *  @param 
     *
     */
    function Getprovinces() {
        $where['c_type'] = 0;

        $field = 'c_code,c_circle_name';
        $order = 'c_id asc';
        $list = M('circle_code')->where($where)->field($field)->order($order)->select();
        foreach ($list as $key => $value) {
            switch ($value['c_circle_name']) {
                case '广西壮族自治区':
                    $list[$key]['c_circle_name'] = '广西';
                    break;
                case '宁夏回族自治区':
                    $list[$key]['c_circle_name'] = '宁夏';
                    break;
                case '新疆维吾尔自治区':
                    $list[$key]['c_circle_name'] = '新疆';
                    break;
                case '澳门特别行政区':
                    $list[$key]['c_circle_name'] = '澳门';
                    break;
                case '西藏自治区':
                    $list[$key]['c_circle_name'] = '西藏';
                    break;                
                default:
                    break;
            }
        }

        return MessageInfo(0, '查询成功', $list);
    }

    /**
     *  推荐所在省的商圈列表
     *  @param pageindex,pagesize
     *
     */
    function Getcirclelist($parr) {
        //热门推荐商圈列表
        $whot['c_status'] = 1;
        $whot['_string'] = "c_citycode='umSXPVTG' or c_citycode='lqG4vZwo' or c_citycode='Gdo8gfxJ'";

        // $field = '*';
        $order = 'c_moods desc';
        // $hot_list = M('circle')->where($whot)->field($field)->order($order)->limit(3)->select();

        $hot_list = M('circle')->where($whot)->order($order)->select();

        if(!$hot_list){
            $hot_list = array();
        }

        foreach ($hot_list as $key => $value) {
            $hot_list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];

            $lw['c_level'] = $value['c_level'];
            $levelimg = M('Circle_level')->where($lw)->getField('c_levelimg');
            $hot_list[$key]['c_levelimg'] = GetHost() . '/' . $levelimg;
        }

        $arr[0]['title'] = "热门商圈";
        $arr[0]['list'] = $hot_list;

        //所在省的商圈列表
        $pw['c_code'] = $parr['provincecode'];
        $provincename = M('Circle_code')->where($pw)->getField('c_circle_name');

        $arr[1]['title'] = $provincename;

        $where['c_status'] = 1;
        $where['c_provincecode'] = $parr['provincecode'];
        
        $field = '*';
        $order = 'c_recommend desc,c_moods desc,c_id asc';
        $list = M('Circle')->where($where)->field($field)->order($order)->select();

        if(!$list){
            $list = array();
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];

            $lw['c_level'] = $value['c_level'];
            $levelimg = M('Circle_level')->where($lw)->getField('c_levelimg');
            $list[$key]['c_levelimg'] = GetHost() . '/' . $levelimg;
        }
        $arr[1]['list'] = $list;

        return MessageInfo(0, '查询成功', $arr);
    }

    /**
    * 获取商圈用户签到信息
    * @param  openid,province,city,provincecode,citycode
    */
    public function Signinfo($parr){
        $province = $parr['province'];
        $city = $parr['city'];
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];

        if(!$parr['ucode']){
            $data['signstate'] = 0;
            $data['msg'] = '签到';

            return MessageInfo(0,'查询信息',$data);
        }

        //获取商圈编码
        if(empty($provincecode) || empty($citycode)){
            $param['province'] = $province;
            $param['city'] = $city;

            $result = $this->Getcirclecode($param);

            if($result['code'] != 0){
                return $result;
            }else{
                $provincecode = $result['data']['provincecode'];
                $citycode = $result['data']['citycode'];
            }
        }

        $Circle_sign = M('Circle_sign');

        $w['c_ucode'] = $parr['ucode'];
        $w['c_provincecode'] = $provincecode;
        $w['c_citycode'] = $citycode;

        // 获取往日签到数据
        $sign_info = $Circle_sign->where($w)->find();

        //判断今日是否签到
        $w['c_sign_time'] = date('Y-m-d',time());
        $today_sign = $Circle_sign->where($w)->find();

        if($sign_info['c_sign_link'] > 0){
            if($today_sign){
                if($sign_info['c_sign_link'] < 7){
                    $moods = ($sign_info['c_sign_link'] * 2) + 2;

                    $data['signstate'] = 1;
                    $data['msg'] = '明日签到+'.$moods;
                }else{
                    $data['signstate'] = 1;
                    $data['msg'] = '明日签到+14';
                }
            }else{
                $data['signstate'] = 0;
                $data['msg'] = '签到';
            }
        }elseif($sign_info['c_sign_link'] == 0){
            if($today_sign){
                $data['signstate'] = 1;
                $data['msg'] = '明日签到+2';
            }else{
                $data['signstate'] = 0;
                $data['msg'] = '签到';
            }
        }else{
            $data['signstate'] = 0;
            $data['msg'] = '签到';
        }

        return MessageInfo(0,'查询信息',$data);
    }

    /**
    *  增加商圈人气
    *  @param provincecode,citycode
    *
    */
    function OptionMoods($parr){
        $w['c_provincecode'] = $parr['provincecode'];
        $w['c_citycode'] = $parr['citycode'];

        $result = M('Circle')->where($w)->setInc('c_moods', $parr['add_moods']);

        if(!$result){
            return Message(1001,'添加商圈人气失败');
        }

        $moods = M('Circle')->where($w)->getField('c_moods');

        //判断商圈等级
        $dlw['c_moods'] = array('ELT',$moods);
        $levelinfo = M('Circle_level')->where($dlw)->order('c_moods desc')->field('c_level')->find();
        $level = $levelinfo['c_level'];
        if (!$level) {
            $level = 1;
        }
        
        $sdata['c_level'] = $level;
        $save_date = M('Circle')->where($w)->save($sdata);

        if(!$sdata){
            return Message(1001,'修改商圈等级失败');
        }

        return Message(0,'操作成功');
    }

     /**
     *  商圈用户签到
     *  @param ucode,provincecode,citycode
     *
     */
    function Usersign($parr) {
        $Circle_sign = M('Circle_sign');  // 商圈用户签到表
        /* 数据事务处理 */
        $Circle_sign->startTrans();

        $w['c_ucode'] = $parr['ucode'];
        $w['c_provincecode'] = $parr['provincecode'];
        $w['c_citycode'] = $parr['citycode'];

        $sign_info = $Circle_sign->where($w)->find();// 获取签到数据

        //记录不存在则添加新纪录
        if(!$sign_info){
            $adata['c_ucode'] = $parr['ucode'];
            $adata['c_provincecode'] = $parr['provincecode'];
            $adata['c_citycode'] = $parr['citycode'];
            $adata['c_addtime'] = date('Y-m-d H:i:s', time());

            $result = $Circle_sign->add($adata);

            if(!$result){
                $Circle_sign->rollback();
                return Message(1001,"新增签到记录失败");
            }

            $sign_info = $Circle_sign->where($w)->find();// 获取签到数据
        }

        $w['c_sign_time'] = date('Y-m-d', time());
        $today_sign = $Circle_sign->where($w)->getField('c_id');

        if ($today_sign) {
            return Message(1002,"今天已签到");
        } else {
            $yesterday = date("Y-m-d", strtotime("-1 day"));   // 昨天的日期时间
            $w['c_sign_time'] = $yesterday;

            $yesterday_sign = $Circle_sign->where($w)->getField('c_id');   // 获取昨天的签到信息

            if($yesterday_sign){
                if($sign_info['c_sign_link'] <= 7){
                    $add_moods = $sign_info['c_sign_link'] * 2;
                }else{
                    $add_moods = 14;
                }
            }else{
                $add_moods = 2;
            }

            $sdata['c_sign_count'] = $sign_info['c_sign_count'] + 1;
            $sdata['c_sign_link'] = $sign_info['c_sign_link'] + 1;
            $sdata['c_sign_time'] = date('Y-m-d', time());
            $sdata['c_moods'] = $sign_info['c_moods'] + $add_moods;
            $sdata['c_addtime'] = date('Y-m-d H:i:s', time());
        }

        //添加
        $w1['c_ucode'] = $parr['ucode'];
        $w1['c_provincecode'] = $parr['provincecode'];
        $w1['c_citycode'] = $parr['citycode'];

        $result = $Circle_sign->where($w1)->save($sdata);

        if(!$result){
            $Circle_sign->rollback();
            return Message(1002,"添加签到记录失败");
        }

        $param['provincecode'] = $parr['provincecode'];
        $param['citycode'] = $parr['citycode'];
        $param['add_moods'] = $add_moods;

        $result = $this->OptionMoods($param);

        if($result['code'] != 0){
            $Circle_sign->rollback();
            return $result;
        }

        $Circle_sign->commit();
        $data['add_moods'] = $add_moods;
        return MessageInfo(0,"签到成功",$data);
    }

    /**
    * 商圈地图商家数据
    * @param provincecode,citycode（获取该商圈的地理位置）
    */
    public function Mapdata($parr){
        if(!empty($parr['longitude']) && !empty($parr['latitude'])){
            $param['longitude'] = $parr['longitude'];
            $param['latitude'] = $parr['latitude'];
        }elseif ($parr['provincecode'] && $parr['citycode']) {
            $w['c_provincecode'] = $parr['provincecode'];
            $w['c_citycode'] = $parr['citycode'];

            $localtion = M('Circle')->field('c_longitude,c_latitude')->find();
            $param['longitude'] = $localtion['c_longitude'];
            $param['latitude'] = $localtion['c_latitude'];
        }else{
            $localtion1 = GetAreafromIp();
            $param['longitude'] = $localtion1['longitude'];
            $param['latitude'] = $localtion1['latitude'];
        }

        $param['juli'] = 2;

        //取出微商用户、固定店铺商家 usertype 1-不分页
        $pagetype = 1;
        $result = $this->MapMerchant($param);

        $list = $result['data'];

        $showarr = array();
        foreach ($list as $key => $value) {
            $showarr[$key]['longitude'] = $value['c_longitude1'];
            $showarr[$key]['latitude'] = $value['c_latitude1'];

            //坐标转换
            $locations = $value['c_longitude1'].','.$value['c_latitude1'];
            $result = IGD('Coordinate','Lbs')->Convert($locations);
            $location_info = explode(',',$result['data']['locations']);
            $showarr[$key]['gd_longitude'] = $location_info[0];
            $showarr[$key]['gd_latitude'] = $location_info[1];


            $showarr[$key]['name'] = $value['c_nickname'];
            $showarr[$key]['isshop'] = $value['c_shop'];//1为商家用户；2为固定店铺商家；
            if (!empty($value['c_shopnum'])) {//是否靓铺
                $showarr[$key]['shopnumimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg5.png';
            } else {
                $showarr[$key]['shopnumimg'] = "";
            }

            if (empty($value['c_address1'])) {
                $showarr[$key]['address'] = "该用户正在附近潜水，点TA看看";
            } else {
                $showarr[$key]['address'] = $value['c_address1'];
            }

            $showarr[$key]['keyvalue'] = $value['c_ucode'];
            $showarr[$key]['basemap'] = $value['c_headimg'];

            if ($value['c_shop'] == 1 && $value['c_isfixed1'] == 0) {//微商用户
                $showarr[$key]['signimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg1.png';
            }else{
                $showarr[$key]['signimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg3.png';
            }
        }

        return MessageInfo(0, "查询成功", $showarr);
    }

    /**
    *  获取地图商家列表
    *  @param longitude,latitude
    *
    */
    function MapMerchant($parr){
        $parr['trade'] = 1;
        $result = IGD('Getdata','Info')->GetLocalUser($parr,3);
        return MessageInfo(0, '查询成功', $result['data']['list']);

        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        $juli = $parr['juli'];//附近多少公里数据 km
        $where[] = array("c_latitude1 is not null and c_longitude1 is not null and c_shop=1");

        if (!empty($longitude) && !empty($latitude)) {
            if ($juli > 0) {
                $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-c_latitude1)/360),2)+COS(PI()*33.07078170776367/180)* COS(c_latitude1 * PI()/180)*POW(SIN(PI()*(' . $longitude . '-c_longitude1)/360),2)))) <= ' .  $juli . '';
            }
            $order = 'case when ifnull(c_latitude1,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((c_latitude1 * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((c_latitude1 * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (c_longitude1 * 3.1415) / 180 ) ) * 6380 asc';
        } else {
            $order = 'c_id desc';
        }

        $field = "c_ucode,c_nickname,c_headimg,c_shoptrade,c_shop,c_latitude1,c_longitude1,c_address1,c_isfixed1,c_shopnum";

        $list = M('Users')->where($where)->field($field)->order($order)->select();

        if (!$list) {
            $list = array();
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
            if($value['c_isfixed1'] == 1){
                $list[$key]['c_shop'] = 2;

                $w['c_id'] = $value1['c_shoptrade'];
                $tradeimg = M('Industry')->where($w)->getField('c_tradeimg');
                if($tradeimg){
                    $list[$key]['c_headimg'] = GetHost() . '/' .$tradeimg;
                }else{
                    $list[$key]['c_headimg'] = GetHost() . '/Uploads/Activity/shopimg/shopimg.png';
                }
            }else{
                if (empty($value['c_headimg'])) {
                    $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
                } else {
                    $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
                }
            }
        }

        return MessageInfo(0, '查询成功', $list);
    }


    /**
    *  获取商圈商家列表
    *  @param pageindex,juli,longitude,latitude,gettype
    *
    */
   function Merchant($parr){
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        if (empty($longitude) || empty($latitude)) {
            $localtion = GetAreafromIp();
            $longitude = $localtion['longitude'];
            $latitude = $localtion['latitude'];                
        }

        //分页
        if (empty($parr['pageindex'])){
            $pageIndex = 1;
        }else{
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        //条件
        $shopw['s.c_provincecode'] = $parr['provincecode'];
        $shopw['s.c_citycode'] = $parr['citycode'];

        $gettype = $parr['gettype'];
        if ($gettype == 1){
            $order = 'b.c_shop desc,b.c_attention desc';
        }else if($gettype == 2) {
            $order = 'u.c_addtime desc';
        }else{
            $order = 'ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((u.c_latitude1 * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((u.c_latitude1 * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (u.c_longitude1 * 3.1415) / 180 ) ) * 6380 asc';
        }

        $field = "s.c_ucode,u.c_isfixed1,u.c_latitude1,u.c_longitude1,u.c_nickname,b.c_attention,b.c_shop";
        $join = "inner join t_users as u on s.c_ucode=u.c_ucode";
        $join1 = "inner join t_users_date as b on s.c_ucode=b.c_ucode";

        $list = M('Circle_shop as s')->where($where)->field($field)->join($join)->join($join1)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('Circle_shop as s')->where($where)->join($join)->join($join1)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
             $list = array();
             $data = Page($pageIndex, $pageCount, $count, $list);
             return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            //店铺资料
            $sw['c_ucode'] = $value['c_ucode'];

            $store_info = M('Store')->field('c_id,c_name,c_desc')->where($sw)->find();

            if(empty($store_info['c_name'])){
                $list[$key]['c_name'] = $value['c_nickname']; 
            }else{
                $list[$key]['c_name'] = $store_info['c_name']; 
            }

            if(empty($store_info['c_desc'])){
                $list[$key]['c_desc'] = "本店商品均属正品，假一罚十信誉保证。 欢迎广大顾客前来放心选购，我们将竭诚为您服务!";
            }else{
                $list[$key]['c_desc'] = $store_info['c_desc']; 
            }

            //店铺图片
            if($store_info){
                $imgw['c_regionid'] = $store_info['c_id'];

                $img = M('Resource_img')->field('c_img')->where($imgw)->find();

                if($img){
                    $list[$key]['c_img'] = GetHost() . '/' . $img['c_img'];
                }else{
                    $list[$key]['c_img'] = GetHost() . '/' . 'Uploads/logo.jpg';
                }
            }else{
                $list[$key]['c_img'] = GetHost() . '/' . 'Uploads/logo.jpg';
            }

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


        $data = Page($pageIndex, $pageCount, $count, $list);

        return MessageInfo(0, '查询成功', $data);
   }

    /**
    *  获取商圈活动跑马灯数据
    *  @param provincecode,citycode
    *
    */
    public function NewShopact($parr){
        $w['a.c_provincecode'] = I('provincecode');
        $w['a.c_citycode'] = I('citycode');

        $field = "a.c_provincecode,a.c_citycode,a.c_address,b.*";
        $join = "inner join t_circle_shopact as b on b.c_ucode=a.c_ucode";
        $order = "b.c_addtime desc";

        $result = M('Circle_shop as a')->field($field)->join($join)->where($w)->order($order)->limit(1)->find();

        if(!$result){
            return MessageInfo(0,"暂时无活动！",$data);
        }

        $uw['c_ucode'] = $result['c_ucode'];

        $nickname = M('Users')->where($uw)->getField('c_nickname');

        //组装数据
        $acttype = $result['c_acttype'];

        if($acttype == 2){
            $coupontype = $result['c_coupontype'];
            if($coupontype == 1){
                $content = $nickname.'正在发放'.round($result['c_money']).'元代金劵';
            }else{
                $content = $nickname.'正在发放'.round($result['c_money'],1).'折折扣劵';
            }
        }elseif($acttype == 1){
                $content = $nickname."发起了'".$result['c_pname']."' 拼团活动";
        }elseif($acttype == 3){
                $content = $nickname."发起了'".$result['c_pname']."' 秒杀活动";
        }else{
                $content = $nickname."发起了'".$result['c_pname']."' 砍价活动";
        }

        $data['content'] = $content;
        $data['weburl'] = $result['c_weburl'];

        return MessageInfo(0,"查询成功！",$data);
    }
    
    /**
    *  获取商圈活动页头部数据
    *  @param provincecode,citycode
    *
    */
    public function ShopactHead($parr){
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];

        $w[] = "a.c_ucode = b.c_ucode ";
        $w[] = "b.c_provincecode = '".$provincecode."' ";
        $w[] = "b.c_citycode = '".$citycode."' ";

        $w1 = ' WHERE '.@implode('AND ',$w);

        $db = M();
        $sql = "SELECT COUNT(1) AS allnum,a.c_acttype FROM t_circle_shopact as a WHERE (SELECT COUNT(1) FROM t_circle_shop as b $w1) = 1";
        
        $result = $db->query($sql);        
        $data = $result[0];
		
        return MessageInfo(0,"查询成功",$data);
    }
   
    /**
    *  获取商圈活动页数据
    *  @param pageindex,pagesize,provincecode,citycode,acttype
    *
    */
    public function ShopactData($parr){
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $acttype = $parr['acttype'];
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];
        
        if (!empty($acttype)) {
            $strsql = "AND a.c_acttype = '".$acttype."' ";
        }
        
        $w[] = "a.c_ucode = b.c_ucode ";
        $w[] = "b.c_provincecode = '".$provincecode."' ";
        $w[] = "b.c_citycode = '".$citycode."' ";

        $w1 = ' WHERE '.@implode('AND ',$w);
       

        $db = M();
        $sql = "SELECT COUNT(1) AS allnum FROM t_circle_shopact as a WHERE (SELECT COUNT(1) FROM t_circle_shop as b $w1) = 1 $strsql";
        
        $result = $db->query($sql);
        $count = $result[0]['allnum'];
        
        $sql1 = "SELECT a.* FROM t_circle_shopact as a WHERE (SELECT COUNT(1) FROM t_circle_shop as b $w1) = 1 $strsql order by a.c_addtime desc,a.c_turnnum desc limit ".$countPage.",".$pageSize;

        $list = $db->query($sql1);

        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        $arr = array();
        foreach ($list as $key => $value) {
            $list[$key]['stattime'] = strtotime($value['c_starttime'])-time();
            $list[$key]['endtime'] = strtotime($value['c_endtime'])-time();

            $uw['c_ucode'] = $value['c_ucode'];
            $userinfo = M('Users')->field('c_headimg,c_nickname')->where($uw)->find();

            $list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
            $list[$key]['c_nickname'] = $userinfo['c_nickname'];

            switch ($value['c_acttype']) {
                case '1':
                    $list[$key]['content'] = "<span>商家</span>发起了<span>".$value['c_pname']."</span> 拼团活动,赶紧来参加吧";
                    $sharedesc = '商家【'.$userinfo['c_nickname'].'】发起了,产品：'.$value['c_pname'].',拼团活动,赶紧来参加吧';
                    break;
                case '2':
                    if($value['c_coupontype'] == 1){
                        $list[$key]['content'] = '<span>商家</span>正在发放<span>'.round($value['c_money']).'元代金劵</span>,赶紧来领取吧';   
                        $sharedesc = '商家【'.$userinfo['c_nickname'].'正在发放'.round($value['c_money']).'元代金劵,赶紧来领取吧';
                    }else{
                        $list[$key]['content'] = '<span>商家</span>正在发放<span>'.round($value['c_money'],1).'折折扣劵</span>,赶紧来领取吧';
                        $sharedesc = '商家【'.$userinfo['c_nickname'].'】正在发放'.round($value['c_money'],1).'折折扣劵,赶紧来领取吧';
                    }                    
                    break;
                case '3':
                    $list[$key]['content'] = "<span>商家</span>发起了<span>".$value['c_pname']."</span> 秒杀活动,赶紧来参加吧";
                    $sharedesc = '商家【'.$userinfo['c_nickname'].'】发起了,产品：'.$value['c_pname'].',秒杀活动,赶紧来参加吧';
                    break;
                default:
                    $list[$key]['content'] = "<span>商家</span>发起了<span>".$value['c_pname']."</span> 砍价活动,赶紧来参加吧";
                    $sharedesc = '商家【'.$userinfo['c_nickname'].'】发起了,产品：'.$value['c_pname'].',砍价活动,赶紧来参加吧';
                    break;
            }

            $list[$key]['shareimg'] = GetHost().'/'.$userinfo['c_headimg'];;
            $list[$key]['sharetit'] = "小蜜优惠活动,大家齐分享！";
            $list[$key]['sharedesc'] = $sharedesc;
            $list[$key]['shareurl'] = $value['c_weburl'];
            $list[$key]['returnurl'] = GetHost(1).'/index.php/Trade/Index/TurnCallback?Id='.$value['c_id'];
            $list[$key]['apireturnurl'] = GetHost(2).'/api.php/Trade/Circle/TurnCallback?Id='.$value['c_id'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
    *  获取商圈活动转发回调接口
    *  @param Id
    *
    */
    public function TurnCallback($parr){
        $w['c_id'] = $parr['Id'];
        $result = M('Circle_shopact')->where($w)->setInc('c_turnnum',1);
        if(!$result){
            return Message(1001,"回调失败");
        }

        return Message(0,"回调成功");
    }
}
