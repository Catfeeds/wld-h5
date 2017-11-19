<?php

/**
 * 查询产品相关信息及产品推广信息
 */
class ShopStore {

    /**
     *  商盟产品查询
     *  @param pageindex,pagesize,ucode,pname,categoryid,juli,可传可不传(longitude,latitude)
     *
     */
    function CoalitionProductList($parr) {

        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

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
        $where['a.c_isdele'] = 1;
        $where['a.c_source'] = 1;
        if (!empty($parr['pname'])) {
            $where['a.c_name'] = array('like', '%' . $parr['pname'] . '%');
        }

        if (!empty($parr['categoryid'])) {
            $where['a.c_categoryid'] = $parr['categoryid'];
        }
        $gettype = $parr['gettype'];
        if ($gettype == 1) {
            $order = 'a.c_id desc';
        } else if ($gettype == 2) {
            $ucodearr = "select c_attention_ucode from t_users_attention where c_ucode='" . $parr['ucode'] . "'";
            $where[] = array('a.c_ucode in (' . $ucodearr . ')');
            $order = 'a.c_id desc';
        } else {
            if (!empty($longitude) && !empty($latitude)) {
                if (!empty($parr['juli'])) {
                    $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-b.c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(b.c_latitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-b.c_longitude)/360),2)))) <= ' . $parr['juli'] . '';
                }
                $order = 'ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((b.c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((b.c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (b.c_longitude * 3.1415) / 180 ) ) * 6380 asc';
            } else {
                $localtion = GetAreafromIp();
                $longitude = $localtion['longitude'];
                $latitude = $localtion['latitude'];
                $order = 'a.c_id desc';
            }
        }

        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_longitude,b.c_latitude,b.c_updatetime as local_time';

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
            if (empty($gettype)) {
                $list[$key]['c_distance'] = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
            }
            $list[$key]['url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?type=1&pcode=" . $value['c_pcode'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }


    function page($pageIndex, $pageCount, $dataCount1,$dataCount2, $data) {
        $list = array();
        $list["pageIndex"] = $pageIndex;
        $list["pageCount"] = $pageCount;
        $list["upCount"] = $dataCount1;
        $list["downCount"] = $dataCount2;
        $list["list"] = $data;
        return $list;
    }


    // 获取商品列表  add by  james
    function getProList($parr){
        $pageSize = $parr['pagesize'];
        $show = $parr['show'];
        $order =$parr['order'];
        $ucode = $parr['ucode'];
        $key =$parr['key'];
        if(empty($ucode)){
            return Message(1001,'ucode不能为空');
        }

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        if ($show == 2)  //查询下架
            $where['c_ishow'] = 2;
        else        //show 参数为空或者不传查询下架
            $where['c_ishow'] = 1;

        if($order==2)       //时间升序
            $orderby ="c_addtime desc";
        elseif($order==3)   //销量多到少
            $orderby ="c_salesnum desc";
        elseif($order==4)   //销量少到多
            $orderby ="c_salesnum asc";
        else
            $orderby ="c_addtime asc";

        $where['c_isagent'] = 0;
        $where['c_isdele'] = 1;
        $where['c_ucode'] = $ucode;
       // $where['c_source'] = 1;
        if(!empty($key)){
            //$where['c_name'] =array('like','%'.$key.'%');
            $where[] = array("c_name like '%$key%'");
        }

        $field = "*,'' as c_longitude,'' as c_latitude,'' as local_time";
        $list = M('Product')->where($where)->field($field)->limit($countPage, $pageSize)->order($orderby)->select();
        $count = M('Product')->where($where)->count();
        $all['c_isagent']=0;
        $all['c_isdele'] = 1;
        $all['c_ucode'] = $ucode;
       // $all['c_source'] = 1;
        $allCount =M('Product')->where($all)->count();

        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = page($pageIndex, $pageCount,$count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $buy =$this->getBuyInfo($value['c_pcode']);
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['c_addtime'] =substr($value['c_addtime'], 0, 4)."/".substr($value['c_addtime'], 5, 2)."/".substr($value['c_addtime'], 8, 2);
            $list[$key]['c_distance'] = 0;
            $list[$key]['sharetit'] = $value['c_name'];
            $list[$key]['sharedesc'] = $value['c_desc'];
            $list[$key]['shareimg'] = $list[$key]['c_pimg'];
            if ($value['c_source'] == 2) {
                $list[$key]['url'] =  GetHost(1) . '/index.php/Shopping/Entitymap/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
                $list[$key]['shareurl'] =  GetHost(1) . '/index.php/Shopping/Entitymap/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
            } else {
                $list[$key]['url'] =  GetHost(1) . '/index.php/Shopping/Index/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
                $list[$key]['shareurl'] =  GetHost(1) . '/index.php/Shopping/Index/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
            }
            
            $list[$key]['buyUserList'] =$buy['list'];
            $list[$key]['buyTotal'] =$buy['total'];
        }

        $data = page($pageIndex, $pageCount, $count, $list);


        return MessageInfo(0, '查询成功', $data);

    }
    //获取买过的产品的用户信息 add by james
    function getBuyInfo($pcode){
        $w['c.c_pcode']=$pcode;
        $w['b.c_pay_state']=1;
        $field ="a.c_headimg,a.c_ucode";
        $join1 ="t_order as b on b.c_ucode=a.c_ucode";
        $join2 ="t_order_details as c on c.c_orderid =b.c_orderid";
        $list =M('Users as a')->distinct('a.c_ucode')->join($join1)->join($join2)->field($field)->where($w)->select();
        $count =count($list);
        foreach($list as $key=> $value){
            $data['list'][$key]['headimg'] =GetHost() . '/' .$value['c_headimg'];
            $data['list'][$key]['ucode'] =$value['c_ucode'];
        }
        $data['total'] =$count;
        return $data;
    }


    /**
     *  获取微商所有产品列表
     *  @param  pageindex,pagesize,type
     *
     */
    function GetProduceList($parr) {
        $pageSize = $parr['pagesize'];
        $type = $parr['type'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;


        if ($type == 1) {
            $ucode = $parr['acode'];
            $where['c_ishow'] = 1;
        } else {
            $ucode = $parr['ucode'];
            if (empty($ucode)) {
                return Message(1009,'请先登录，再操作');
            }
        }

        $where['c_isagent'] = 0;
        $where['c_isdele'] = 1;
        $where['c_ucode'] = $ucode;
      //  $where['c_source'] = 1;

        $field = "*,'' as c_longitude,'' as c_latitude,'' as local_time";
        $list = M('Product')->where($where)->field($field)->limit($countPage, $pageSize)->order('c_id desc')->select();
        
        $count = M('Product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['c_distance'] = 0;
            $list[$key]['url'] =  GetHost(1) . '/index.php/Shopping/Index/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
            $list[$key]['sharetit'] = $value['c_name'];
            $list[$key]['sharedesc'] = $value['c_desc'];
            $list[$key]['shareimg'] = $list[$key]['c_pimg'];
            $list[$key]['shareurl'] =  GetHost(1) . '/index.php/Shopping/Index/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //获取买过的人的信息列表
    function getBuyUser($parr){
        $pageSize = $parr['pagesize'];
        $pcode = $parr['pcode'];
        $ucode = $parr['ucode'];

        if(empty($ucode) || empty($pcode)){
            return Message(1001,'ucode不能为空');
        }
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['c.c_pcode']=$pcode;
        $w['b.c_pay_state']=1;
        $field ="a.c_headimg,a.c_ucode,a.c_shop,a.c_nickname";
        $join1 ="t_order as b on b.c_ucode=a.c_ucode";
        $join2 ="t_order_details as c on c.c_orderid =b.c_orderid";

        $info =M('Users as a')->distinct('a.c_ucode')->join($join1)->join($join2)->field($field)->limit($countPage, $pageSize)->where($w)->select();
        $count =count($info);
        $pageCount = ceil($count / $pageSize);
        if (!$info) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach($info as $key=> $value){
            $list[$key]['headimg'] =GetHost() . '/' .$value['c_headimg'];
            $list[$key]['ucode'] =$value['c_ucode'];
            $list[$key]['c_nickname'] =$value['c_nickname'];
            $list[$key]['c_shop'] =$value['c_shop'];
            $list[$key]['total'] =$this->getBuyNum($value['c_ucode'],$pcode);
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);

    }

    //或取用户买过某产品的次数
    function getBuyNum($ucode,$pcode){
        $w['b.c_pcode']=$pcode;
        $w['a.c_pay_state']=1;
        $w['c.c_ucode']=$ucode;

        $join1 ="t_order_details as b on b.c_orderid=a.c_orderid";
        $join2 ="t_users as c on c.c_ucode =a.c_ucode";
        $count =count(M('Order as a')->join($join1)->join($join2)->where($w)->select());
        return $count;
    }

    //获取活动产品列表

    public function ActiveProductList($parr){

        if(empty($parr['ucode'])){
            return Message(1001,'参数缺失');
        }

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['a.c_ucode'] = $parr['ucode'];
        $w['a.c_state'] = array('neq',2);
        $w['a.c_isdel'] = 2;
        $w['a.c_num'] = array('GT',0);
        $w['c.c_ishow'] = 1;
        $w['c.c_isdele'] = 1;
        $join = 'left join t_product as c on a.c_pcode=c.c_pcode';
        $field = 'c.*';
        $list = M('Shopact_product as a')->distinct("a.c_pcode")->join($join)->where($w)->order('a.c_id desc')->limit($countPage, $pageSize)->field($field)->select();
        $count = M('Shopact_product as a')->distinct("a.c_pcode")->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $buy =$this->getBuyInfo($value['c_pcode']);
            $list[$key]['c_addtime'] =substr($value['c_addtime'], 0, 4)."/".substr($value['c_addtime'], 5, 2)."/".substr($value['c_addtime'], 8, 2);
            $list[$key]['buyUserList'] =$buy['list'];
            $list[$key]['buyTotal'] =$buy['total'];
            $list[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];

        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //获取某个产品参与的活动记录
    function getRecord($parr){
        $ucode =$parr['ucode'];
        $pcode =$parr['pcode'];
        if(empty($ucode)||empty($pcode)){
            return Message(1001,'参数缺失');
        }

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['a.c_pcode']=$pcode;
        $w['a.c_ucode']=$ucode;
        $field ="a.*,b.c_activitytype";
        $join ="left join t_activity as b on a.c_aid=b.c_id";
        $list =M('Shopact_product as a')->join($join)->where($w)->limit($countPage, $pageSize)->field($field)->select();
        $count = M('Shopact_product as a')->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }
        foreach ($list as $key => $value) {
            $list[$key]['c_addtime'] =substr($value['c_addtime'], 0, 4)."/".substr($value['c_addtime'], 5, 2)."/".substr($value['c_addtime'], 8, 2);
            $list[$key]['c_imgpath'] = GetHost().'/'.$value['c_pimg'];
            $acttype = $value['c_activitytype'];
            if ($acttype == 26) { //拼团
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


    // 获取要上/下架的商品列表 要去除参与活动的商品
    function getUpDownList($parr){
        $ucode =$parr['ucode'];
        $ishow =$parr['ishow'];
        $type =$parr['type'];
        if(empty($ucode)){
            return Message(1001,'参数缺失');
        }

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if($type!==""){    // 传了分类id  查询该类商品包括活动商品  上下架的都要
            $w['c_product_category']=$type;
            $w['c_isagent'] = 0;
            $w['c_isdele'] = 1;
            $w['c_ucode'] = $ucode;

            $list =M('Product')->where($w)->limit($countPage, $pageSize)->select();
            $count =M('Product')->where($w)->count();
            $pageCount = ceil($count / $pageSize);
        }else{                // 只查询普通商品  只查对应的上下架
            if($ishow==1){
                $w['a.c_ishow']=1;
            }else{
                $w['a.c_ishow']=2;
            }
            $w['a.c_isagent'] = 0;
            $w['a.c_isdele'] = 1;
            $w['a.c_ucode'] = $ucode;
            $w[] = array("b.c_pcode is null or c_state=2");

            $field ="a.*";
            $join ="left join t_shopact_product as b on b.c_pcode =a.c_pcode";
            $list =M('Product as a')->distinct("a.c_pcode")->join($join)->where($w)->limit($countPage, $pageSize)->field($field)->select();
            $count =M('Product as a')->distinct("a.c_pcode")->join($join)->where($w)->count();
            $pageCount = ceil($count / $pageSize);
        }

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['c_addtime'] =substr($value['c_addtime'], 0, 4)."/".substr($value['c_addtime'], 5, 2)."/".substr($value['c_addtime'], 8, 2);
            $list[$key]['categoryName'] =$value['c_product_category']==0?"未分类":M('Product_category')->where(array('c_id'=>$value['c_product_category']))->getField("c_category_name");
        }

        $data = Page($pageIndex, $pageCount, $count, $list);

        return MessageInfo(0,'查询成功',$data);

    }


    /**
     *  获取产品详情
     *  @param  pcode
     *
     */
    function GetProduceInfo($parr) {

        $ucode = $parr['ucode'];
        $type = $parr['type'];
        $where['c_pcode'] = $parr['pcode'];

        if ($parr['show'] != 1) {
            $where['c_ishow'] = 1;
        }


        $where['c_isdele'] = 1;
        $data = M('Product')->where($where)->find();
        if (!$data) {
            return Message(1017, '数据为空');
        }

        if (empty($ucode)) {
            $data['url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?pcode=" . $data['c_pcode'];
        } else {
            $data['url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?pcode=" . $data['c_pcode'] . "&pucode=" . $ucode;
        }

        if ($data['c_ishow'] == 2) {
            $data['geturl'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?type=$type&show=1&pcode=" . $data['c_pcode'];
        } else {
            $data['geturl'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?type=$type&pcode=" . $data['c_pcode'];
        }



        $data['c_pimg'] = GetHost() . '/' . $data['c_pimg'];

        $desc = $data['c_desc'];
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        $data['c_desc'] = str_replace($qian,$hou,$desc);

        $imgwhere['c_pcode'] = $parr['pcode'];

        $type = $parr['type'];
        if ($type == 1) {
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
        }

        $modelwhere['c_pcode'] = $parr['pcode'];
        $modellist = M('Product_model')->where($modelwhere)->select();

        $data['ladderprice'] = NULL;
        $data['modellist'] = NULL;
        $data['pricestr'] = "";
        if (count($modellist) > 0) {
            foreach ($modellist as $key1 => $value1) {

                $where1['c_pcode'] = $parr['pcode'];
                $where1['c_mcode'] = $value1['c_mcode'];
                $ladderprice = M('Product_ladderprice')->where($where1)->select();

                $str = "";
                $tempint = count($ladderprice);

                if ($tempint == 0) {
                    $ladderprice[0]['c_id'] = "0";
                    $ladderprice[0]['c_pcode'] = $value1['c_pcode'];
                    $ladderprice[0]['c_mcode'] = $value1['c_mcode'];
                    $ladderprice[0]['c_minnum'] = 1;
                    $ladderprice[0]['c_maxnum'] = $value1['c_num'];
                    $ladderprice[0]['c_price'] = $value1['c_price'];
                } else {
                    for ($x = 0; $x < $tempint; $x++) {
                        $min = $ladderprice[$x]['c_minnum'];
                        $max = $ladderprice[$x]['c_maxnum'];
                        $price = $ladderprice[$x]['c_price'];

                        if ($x == 2) {
                            $temp = "累计购买" . $min . "个或" . $min . "以上：单价￥" . $price;
                            $str.=$temp;
                            break;
                        } else {
                            $temp = "累计购买" . $min . "-" . $max . "个：单价￥" . $price . "|";
                            $str .=$temp;
                        }
                    }
                }


                $modellist[$key1]['ladderprice'] = $ladderprice;
                $modellist[$key1]['pricestr'] = $str;
            }
            $data['modellist'] = $modellist;
        } else {
            $ladderprice = M('Product_ladderprice')->where($where)->select();
            $str = "";
            $tempint = count($ladderprice);
            for ($x = 0; $x <= $tempint; $x++) {
                $min = $ladderprice[$x]['c_minnum'];
                $max = $ladderprice[$x]['c_maxnum'];
                $price = $ladderprice[$x]['c_price'];

                if ($x == 2) {
                    $temp = "累计购买" . $min . "个或" . $min . "以上：单价￥" . $price;
                    $str.=$temp;
                    break;
                } else {
                    $temp = "累计购买" . $min . "-" . $max . "个：单价￥" . $price . "|";
                    $str .=$temp;
                }
            }
            $data['ladderprice'] = $ladderprice;
            $data['pricestr'] = $str;
        }

        $count = 0;
        if (!empty($ucode)) {
            $count = IGD('Order', 'Order')->Getoldproduct($ucode, $parr['pcode']);
        }
        $data['cumulative'] = $count;
        return MessageInfo(0, '查询成功', $data);
    }

    //获取商品评论信息
    public function GetScore($parr) {
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

        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {

            //修改评论时间
            $list[$key]['c_addtime'] = date('Y-m-d', strtotime($value['c_addtime']));

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

        $count = M('product_score as a')->join($join)->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //获取用户推广的产品
    public function GetUsertuiguang($parr) {

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $ucode = $parr['ucode'];
        $sql = "select b.c_ucode,b.c_nickname,b.c_headimg from t_product as a INNER JOIN t_users as b on a.c_ucode=b.c_ucode  where a.c_pcode "
                . "in (select c_pcode from t_users_spread where c_ucode='$ucode') and a.c_ishow=1 and a.c_isdele=1 GROUP BY a.c_ucode limit $countPage,$pageSize";

        $list = M()->query($sql);

        foreach ($list as $key => $value) {

            $tempucode = $value['c_ucode'];
            $sql1 = "select b.c_id,a.c_pcode,a.c_name,a.c_desc,a.c_pimg,a.c_price,b.c_ucode from t_product as a INNER JOIN t_users_spread as b on a.c_pcode=b.c_pcode where a.c_ucode='$tempucode' and b.c_ucode='$ucode' and a.c_ishow=1 and a.c_isdele=1";
            $productlist = M()->query($sql1);

            foreach ($productlist as $key1 => $value1) {
                $productlist[$key1]['c_pimg'] = GetHost() . '/' . $value1['c_pimg'];
                if ($value1['c_source'] == 2) {
                    $productlist[$key1]['url'] = GetHost(1) . '/' . "index.php/Shopping/Entitymap/detail?pcode=" . $value1['c_pcode'] . "&pucode=" . $value1['c_ucode'];
                } else {
                    $productlist[$key1]['url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?pcode=" . $value1['c_pcode'] . "&pucode=" . $value1['c_ucode'];
                }
            }

            $list[$key]['list'] = $productlist;
            $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
        }



        $sql1 = "select count(a.c_ucode) as count1 from t_product as a INNER JOIN t_users as b on a.c_ucode=b.c_ucode  where c_pcode "
                . "in(select c_pcode from t_users_spread where c_ucode='$ucode') and c_ishow=1 and c_isdele=1 GROUP BY a.c_ucode limit $countPage,$pageSize";


        $count1 = M()->query($sql1);
        $count = $count1[0]['count1'];
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

    //删除用户推广的产品
    public function GetUser($parr) {
        $where['c_id'] = $parr['id'];
        $where['c_ucode'] = $parr['ucode'];
        $result = M('Users_spread')->where($where)->delete();

        if ($result <= 0) {
            return Message(1027, "删除推广产品失败");
        }
        return Message(0, "删除成功");
    }

    /**
     * 操作用户访问产品记录
     * @param pcode,ucode,username,headimg,source,browser
     *
     * */
    public function OptionProduceLog($parr) {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_addtime'] = array('EGT', date('Y-m-d H:i:s'));
        $producelogid = M('Product_visit')->where($where)->getField('c_id');
        if ($producelogid) {
            return Message(0, "记录已存在");
        }
        $localtion = GetAreafromIp();
        $data['c_pcode'] = $parr['pcode'];
        $data['c_ucode'] = $parr['ucode'];
        $data['c_username'] = $parr['username'];
        $data['c_headimg'] = $parr['headimg'];
        $data['c_source'] = $parr['source'];
        $data['c_ip'] = get_client_ip();
        $data['c_browser'] = $parr['browser'];
        $data['c_address'] = $localtion['localtion'];
        $result = M('Product_visit')->add($data);
        return Message(0, "记录成功");
    }

}
