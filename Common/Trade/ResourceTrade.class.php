<?php

/**
 * 商圈资源动态
 */
class ResourceTrade {
    /**
     *  获取某商圈资源列表
     *  @param ucode,pageindex,condition,provincecode,citycode
     */
    function GetCircleResource($parr) {
        $province = $parr['province'];
        $city = $parr['city'];
        $provincecode = $parr['provincecode'];
        $citycode = $parr['citycode'];

        //获取商圈编码
        if((empty($provincecode) || empty($citycode)) && (!empty($province) && !empty($city))){
            $param['province'] = $province;
            $param['city'] = $city;

            $result = IGD('Circle','Trade')->Getcirclecode($param);

            if($result['code'] != 0){
                return $result;
            }else{
                $provincecode = $result['data']['provincecode'];
                $citycode = $result['data']['citycode'];
            }
        }

        if(!empty($provincecode) && !empty($citycode)){
	        //获取所在商圈名
	        $mw['c_status'] = 1;
	        $mw['c_provincecode'] = $provincecode;
	        $mw['c_citycode'] = $citycode;
	        $circle_name = M('Circle')->where($mw)->getField('c_name');
	    }

        $db = M('Resource as r');

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['r.c_status'] = 1;
        if(!empty($provincecode) && !empty($citycode)) {
            $w[] = array("r.c_provincecode='$provincecode' or r.c_provincecode is null or r.c_provincecode=''");
            $w[] = array("r.c_citycode='$citycode' or r.c_citycode is null or r.c_citycode=''");
        }
        $sign = 0;//发表时间显示格式
        
        $is_condition = 0;
        if (!empty($parr['condition'])) {
            $condition = $parr['condition'];
            $w[] = array("r.c_content like '%" . $condition . "%' or u.c_nickname like '%" . $condition . "%' or u.c_shopnum='" . $condition . "'");
            $is_condition = 1;
        }
        
        //关联查询
        $join = "inner join t_users as u on r.c_ucode=u.c_ucode";
        $count = M('Resource as r')->join($join)->where($w)->count();

        $field = "r.*,u.c_nickname,u.c_headimg,u.c_shop";

        //判断本商圈是否发布资源信息
        if($count == 0 && $is_condition == 0){
            $w1['r.c_status'] = 1;
            // $w1['r.c_provincecode'] = $provincecode;
            if(!empty($provincecode) && !empty($citycode)) {
                $w1[] = array("r.c_provincecode='$provincecode' or r.c_provincecode is null or r.c_provincecode=''");
            }
            $rt = $db->field($field)->join($join)->where($w1)->order('r.c_id desc')->limit($countPage, $pageSize)->select();

            $count = M('Resource as r')->join($join)->where($w1)->count();
            $pageCount = ceil($count / $pageSize);
            if (count($rt) == 0) {
                $rt = array();
                $data = Page($pageIndex, $pageCount, $count, $rt);
                return MessageInfo(0, '查询成功', $data);
            }
        }else{
            $rt = $db->field($field)->join($join)->where($w)->order('r.c_istop desc,r.c_id desc')->limit($countPage, $pageSize)->select();

            $pageCount = ceil($count / $pageSize);
            if (count($rt) == 0) {
                $rt = array();
                $data = Page($pageIndex, $pageCount, $count, $rt);
                return MessageInfo(0, '查询成功', $data);
            }
        }

        $jumptype = 0;//资源跳转类型
        foreach ($rt as $key => $row) {
            if ($row['c_shop'] == 1) {
                //判断用户跳转
                $whereinfo['c_ucode'] = $row['c_ucode'];
                $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
                if ($c_isfixed == 1) {
                    $jumptype = 2;
                } else {
                    $jumptype = 1;
                }
            }

            if(empty($provincecode) || empty($citycode)) {
            	//商圈名称
	            $mw1['c_status'] = 1;
	            $mw1['c_provincecode'] = $row['c_provincecode'];
	            $mw1['c_citycode'] = $row['c_citycode'];
	            $circle_name1 = M('Circle')->where($mw1)->getField('c_name');
            } else {
            	//商圈名称
	            $mw1['c_status'] = 1;
	            $mw1['c_provincecode'] = $provincecode;
	            $mw1['c_citycode'] = $citycode;
	            $circle_name1 = M('Circle')->where($mw1)->getField('c_name');
            }

            if($circle_name1){
                $rt[$key]['circle_name'] = $circle_name1;
            }else{
                $rt[$key]['circle_name'] = $circle_name;
            }

            $rt[$key]['jumptype'] = $jumptype;
            $rt[$key]['c_headimg'] = GetHost() . '/' . $row['c_headimg'];
            $rt[$key]['c_content'] = subtext($row['c_content'], 86);
            $rt[$key]['c_like'] = $this->Toobig($row['c_like']);
            $rt[$key]['c_address'] = $this->IsNull($row['c_address']);
            $rt[$key]['switch_addtime'] = $this->GetShowTime($row['c_addtime'], $sign);
            $rt[$key]['imglist'] = $this->get_imglist($row['c_id'],$row['c_type']);//资源图片列表
            $rt[$key]['tj_product'] =  $this->get_product($row['c_id']);//推荐商品列表
            $rt[$key]['comment_list'] = $this->get_commentlist($row['c_id'], 0);//评论列表
            $rt[$key]['is_attention'] = $this->is_attention($parr['ucode'], $row['c_ucode']);//用户是否关注资源发布者
            $rt[$key]['is_delete'] = $this->is_delete($parr['ucode'], $row['c_ucode']);//是否显示删除
            $rt[$key]['is_like'] = $this->is_like($row['c_id'], $parr['ucode']);//是否点赞
            $rt[$key]['is_tip'] =$this->is_tip($row['c_id'],$parr['ucode']); //是否举报

            if(count($rt[$key]['imglist']) != 0){
                $rt[$key]['share_img'] = $rt[$key]['imglist'][0]['c_img'];
            }else{
                $rt[$key]['share_img'] = $rt[$key]['c_headimg'];
            }

            $rt[$key]['share_title'] = "分享标题";

            if($rt[$key]['c_content']){
                $rt[$key]['share_des'] = subtext($row['c_content'], 20);
            }else{
                $rt[$key]['share_des'] = "分享描述分享描述。。。";
            }

            $rt[$key]['share_url'] = GetHost(1)."/index.php/Trade/Index/shares?resourceid=".$row['c_id'];
        }

        $data = Page($pageIndex, $pageCount, $count, $rt);
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  获取个人空间全部资源信息列表
     *  @param ucode,pageindex,issue_ucode
     */
    function GetResourceList($parr) {
        $db = M('Resource as r');

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['r.c_status'] = 1;

        $sign = 1;
        $w['r.c_ucode'] = $parr['issue_ucode'];

        $field = "r.*,u.c_nickname,u.c_headimg,u.c_shop,c.c_name as circle_name";
        $join = "inner join t_users as u on r.c_ucode=u.c_ucode";
        $join1 = "left join t_circle as c on r.c_citycode=c.c_citycode";
        $rt = $db->field($field)->join($join)->join($join1)->where($w)->order('r.c_istop desc,r.c_id desc')->limit($countPage, $pageSize)->select();

        $count = M('Resource as r')->join($join)->join($join1)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (count($rt) == 0) {
            $rt = array();
            $data = Page($pageIndex, $pageCount, $count, $rt);
            return MessageInfo(0, '查询成功', $data);
        }

        $jumptype = 0;//资源跳转类型
        foreach ($rt as $key => $row) {
            if ($row['c_shop'] == 1) {
                //判断用户跳转
                $whereinfo['c_ucode'] = $row['c_ucode'];
                $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
                if ($c_isfixed == 1) {
                    $jumptype = 2;
                } else {
                    $jumptype = 1;
                }
            }

            $rt[$key]['jumptype'] = $jumptype;
            $rt[$key]['c_headimg'] = GetHost() . '/' . $row['c_headimg'];
            $rt[$key]['c_content'] = subtext($row['c_content'], 86);
            $rt[$key]['c_like'] = $this->Toobig($row['c_like']);
            $rt[$key]['c_address'] = $this->IsNull($row['c_address']);
            $rt[$key]['switch_addtime'] = $this->GetShowTime($row['c_addtime'], $sign);
            $rt[$key]['switch_addtime1'] = $this->GetShowTime($row['c_addtime'], 0);
            $rt[$key]['imglist'] = $this->get_imglist($row['c_id'],$row['c_type']);//资源图片列表
            $rt[$key]['tj_product'] =  $this->get_product($row['c_id']);//推荐商品列表
            $rt[$key]['comment_list'] = $this->get_commentlist($row['c_id'], 0);//评论列表
            $rt[$key]['is_attention'] = $this->is_attention($parr['ucode'], $row['c_ucode']);//用户是否关注资源发布者
            $rt[$key]['is_delete'] = $this->is_delete($parr['ucode'], $row['c_ucode']);//是否显示删除
            $rt[$key]['is_like'] = $this->is_like($row['c_id'], $parr['ucode']);//是否点赞

            if(count($rt[$key]['imglist']) != 0){
                $rt[$key]['share_img'] = $rt[$key]['imglist'][0]['c_img'];
            }else{
                $rt[$key]['share_img'] = $rt[$key]['c_headimg'];
            }

            $rt[$key]['share_title'] = "分享标题";

            if($rt[$key]['c_content']){
                $rt[$key]['share_des'] = subtext($row['c_content'], 20);
            }else{
                $rt[$key]['share_des'] = "分享描述分享描述。。。";
            }

            $rt[$key]['share_url'] = GetHost(1)."/index.php/Trade/Index/shares?resourceid=".$row['c_id'];
        }

        $data = Page($pageIndex, $pageCount, $count, $rt);
        return MessageInfo(0, "查询成功", $data);
    }


    //数量太大，返回999
    public function Toobig($num) {
        if (intval($num) > 1000) {
            $b = sprintf('%.2f',$num/1000);
            $num = $b.'k';
        }else if(intval($num) > 10000){
            $b = sprintf('%.2f',$num/10000);
            $num = $b.'w';
        }

        return $num;
    }

    //判断返回字符串，为空返回‘’
    public function IsNull($str, $sign) {
        if ($sign == 1) {
            if (empty($str)) {
                $str = '';
            } else {
                $str = GetHost() . '/' . $str;
            }
        } else {
            if (empty($str)) {
                $str = '';
            }
        }
        return $str;
    }

    /**
     *  获取资源详细信息
     *  @param sid 资源id
     */
    function GetResourceInfo($parr) {
        $db = M('Resource as r');

        $w['r.c_status'] = 1;
        $w['r.c_id'] = $parr['sid'];

        $field = "r.*,u.c_nickname,u.c_headimg,u.c_shop,c.c_name as circle_name";
        $join = "inner join t_users as u on r.c_ucode=u.c_ucode";
        $join1 = "left join t_circle as c on r.c_citycode=c.c_citycode";
        $rt = $db->field($field)->join($join)->join($join1)->where($w)->find();

        if (count($rt) == 0) {
            return MessageInfo(0, '查询成功', $rt);
        }

        $jumptype = 0;
        if ($rt['c_shop'] == 1) {
            //判断用户状态
            $whereinfo['c_ucode'] = $rt['c_ucode'];
            $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
            if ($c_isfixed == 1) {
                $jumptype = 2;
            } else {
                $jumptype = 1;
            }
        }

        $rt['jumptype'] = $jumptype;
        $rt['c_like'] = $this->Toobig($rt['c_like']);
        $rt['switch_addtime'] = $this->GetShowTime($rt['c_addtime'], 0);
        $rt['c_headimg'] = GetHost() . '/' . $rt['c_headimg'];
        $rt['imglist'] = $this->get_imglist($rt['c_id'],$rt['c_type']);
        $rt['tj_product'] = $this->get_product($rt['c_id']);
        $rt['comment_list'] = $this->get_commentlist($rt['c_id'], 1);
        //用户是否关注资源发布者
        $rt['is_attention'] = $this->is_attention($parr['ucode'], $rt['c_ucode']);
        //是否显示删除
        $rt['is_delete'] = $this->is_delete($parr['ucode'], $rt['c_ucode']);
        //是否点赞
        $rt['is_like'] = $this->is_like($rt['c_id'], $parr['ucode']);
        $rt['is_tip'] =$this->is_tip($rt['c_id'],$parr['ucode']); //是否举报

        if(count($rt['imglist']) != 0){
            $rt['share_img'] = $rt['imglist'][0]['c_img'];
        }else{
            $rt['share_img'] = $rt['c_headimg'];
        }

        $rt['share_title'] = "分享标题";

        if($rt['c_content']){
            $rt['share_des'] = subtext($rt['c_content'], 20);
        }else{
            $rt['share_des'] = "分享描述分享描述。。。";
        }

        $rt['share_url'] = GetHost(1)."/index.php/Trade/Index/shares?resourceid=".$rt['c_id'];

        //增加资源浏览量
        $arr['sid'] = $parr['sid'];
        $result = $this->ResourceClick($arr);
        if ($result['code'] != 0) {
            return $result;
        }

        return MessageInfo(0, "查询成功", $rt);
    }


    /**
     *  获取被举报资源详细信息
     *  @param sid 资源id
     */
    function GetTipInfo($parr) {
        $db = M('Resource as r');

        //$w['r.c_status'] = 1;
        $w['r.c_id'] = $parr['sid'];

        $field = "r.*,u.c_nickname,u.c_headimg,u.c_shop,c.c_name as circle_name";
        $join = "inner join t_users as u on r.c_ucode=u.c_ucode";
        $join1 = "left join t_circle as c on r.c_citycode=c.c_citycode";
        $rt = $db->field($field)->join($join)->join($join1)->where($w)->find();

        if (count($rt) == 0) {
            return MessageInfo(0, '查询成功', $rt);
        }

        $jumptype = 0;
        if ($rt['c_shop'] == 1) {
            //判断用户状态
            $whereinfo['c_ucode'] = $rt['c_ucode'];
            $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
            if ($c_isfixed == 1) {
                $jumptype = 2;
            } else {
                $jumptype = 1;
            }
        }

        $rt['jumptype'] = $jumptype;
        $rt['c_like'] = $this->Toobig($rt['c_like']);
        $rt['switch_addtime'] = $this->GetShowTime($rt['c_addtime'], 0);
        $rt['c_headimg'] = GetHost() . '/' . $rt['c_headimg'];
        $rt['imglist'] = $this->get_imglist($rt['c_id'],$rt['c_type']);
        $rt['tj_product'] = $this->get_product($rt['c_id']);
        $rt['comment_list'] = $this->get_commentlist($rt['c_id'], 1);
        //用户是否关注资源发布者
        $rt['is_attention'] = $this->is_attention($parr['ucode'], $rt['c_ucode']);
        //是否显示删除
        $rt['is_delete'] = $this->is_delete($parr['ucode'], $rt['c_ucode']);
        //是否点赞
        $rt['is_like'] = $this->is_like($rt['c_id'], $parr['ucode']);
        $rt['is_tip'] =$this->is_tip($rt['c_id'],$parr['ucode']); //是否举报
        $rt['is_appeal'] =$this->is_appeal($rt['c_id'],$parr['ucode']); //是否申诉 0 未申诉 1 已申诉

        if(count($rt['imglist']) != 0){
            $rt['share_img'] = $rt['imglist'][0]['c_img'];
        }else{
            $rt['share_img'] = $rt['c_headimg'];
        }
        $rt['share_title'] = "分享标题";
        if($rt['c_content']){
            $rt['share_des'] = subtext($rt['c_content'], 20);
        }else{
            $rt['share_des'] = "分享描述分享描述。。。";
        }
        $rt['share_url'] = GetHost(1)."/index.php/Trade/Index/shares?resourceid=".$rt['c_id'];

        //增加资源浏览量
        $arr['sid'] = $parr['sid'];
        $result = $this->ResourceClick($arr);
        if ($result['code'] != 0) {
            return $result;
        }

        return MessageInfo(0, "查询成功", $rt);
    }

    //根据id获取七牛视频第一帧图片
    public function GetVideoPic($picture_id){
        $durl = 'http://api.qiniu.com/status/get/prefop?id='.$picture_id;
        $arr = curlGet($durl);

        $picture = 'https://'.C(Explicit_Link).'/'.$arr['items'][0]['key'];

        return $picture;
    }

    //获取资源图片列表
    public function get_imglist($id,$type) {
        $w['c_regionid'] = $id;
        $w['c_sourceid'] = 2;
        $data = M('Resource_img')->field('c_img,c_thumbnail_img')->where($w)->select();

        if($type == 1){
            foreach ($data as $k => $v) {
                $data[$k]['c_img'] = GetHost() . '/' . $v['c_img'];
                $data[$k]['c_thumbnail_img'] = GetHost() . '/' . $v['c_img'];
            }
        }else if($type == 2){
            foreach ($data as $k => $v) {
                //根据id获取七牛视频第一帧图片
                $picture = $this->GetVideoPic($v['c_img']);
                $data[$k]['c_img'] = $picture;
                $data[$k]['c_thumbnail_img'] = $picture;
            }
        }
        
        if (count($data) == 0) {
            $data = array();
        }

        return $data;
    }

    //获取资源推荐商品信息
    public function get_product($id) {
        $db = M('Resource_product as r');
        $w['r.c_resourceid'] = $id;
        $field = 'r.c_pcode,p.c_name,p.c_price,p.c_pimg,p.c_source';
        $join = 'left join t_product as p on r.c_pcode=p.c_pcode';

        $data = $db->field($field)->join($join)->where($w)->select();

        foreach ($data as $key => $value) {
            $data[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $data[$key]['url'] = GetHost(1) . '/' . "index.php/Home/Shop/details?pcode=" . $value['c_pcode'];
        }

        if (count($data) == 0) {
            $data = array();
        }

        return $data;
    }

    //获取资源评论列表
    public function get_commentlist($id, $flag) {
        $db = M('resource_comment as r');

        $join = 'left join t_users as u on r.c_ucode=u.c_ucode';
        $join1 = 'left join t_users as pu on r.c_upucode=pu.c_ucode';
        $field = 'r.*,u.c_ucode,u.c_headimg,u.c_nickname,pu.c_ucode as upucode,pu.c_headimg as upheadimg,pu.c_nickname as upnickname';

        $commentwhere['r.c_resourceid'] = $id;
        $commentwhere['r.c_state'] = 1;

        $order = 'r.c_id desc';
        if ($flag == 0) {
            $comment = $db->join($join)->join($join1)->where($commentwhere)->order($order)->limit(3)->field($field)->select();
            foreach($comment as $key=>$value){
                $comment[$key]['jumpType']=$this->getUserType($value['c_ucode']);
            }
        } else {
            $comment = $db->join($join)->join($join1)->where($commentwhere)->order($order)->field($field)->select();
            foreach($comment as $key=>$value){
                $comment[$key]['jumpType']=$this->getUserType($value['c_ucode']);
            }
        }

        if ($comment) {
            $comment = $this->packedData($comment);
        }
        if (count($comment) == 0) {
            $comment = array();
        }
        return $comment;
    }

    //查询用户类型 0 会员 1 线上商家 2 线下商家
    public function getUserType($ucode){

        $info =M('Users')->where(array('c_ucode'=>$ucode))->find();
        if($info['c_shop']==0){ //非商家
            $flag =0;
        }else{
            $local =M('User_local')->where(array('c_ucode'=>$ucode))->find();
            if($local['c_isfixed']==0){
                $flag =1;
            }elseif($local['c_isfixed']==1){
                $flag =2;
            }
        }
        return $flag;
    }

    //组成评论显示数组
    public function packedData($data) {
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$k]['c_id'] = $v['c_id'];
            $arr[$k]['c_resourceid'] = $v['c_resourceid'];

            $arr[$k]['c_ucode'] = $v['c_ucode'];
            $arr[$k]['c_nickname'] = $v['c_nickname'];
            $arr[$k]['c_headimg'] = GetHost() . '/' . $v['c_headimg'];

            if (!empty($v['upucode'])) {
                $arr[$k]['upucode'] = $this->IsNull($v['upucode']);
                $arr[$k]['upheadimg'] = $this->IsNull($v['upheadimg'], 1);
                $arr[$k]['upnickname'] = $this->IsNull($v['upnickname']);
            }

            $arr[$k]['c_content'] = $v['c_content'];
            $arr[$k]['c_addtime'] = $v['c_addtime'];
            $arr[$k]['switch_addtime'] = $this->GetShowTime($v['c_addtime'], 0);
            $arr[$k]['jumptype'] =$v['jumpType'];
        }
        return $arr;
    }

    //用户是否关注资源发布者
    public function is_attention($c_ucode, $c_attention_ucode) {
        if (empty($c_ucode)) {
            return 0;
        }

        $w['c_ucode'] = $c_ucode;
        $w['c_attention_ucode'] = $c_attention_ucode;

        $count = M('Users_attention')->where($w)->count();

        if ($count == 0) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        return $flag;
    }

    //用户是否显示删除按钮
    public function is_delete($ucode, $pucode) {
        if ($ucode == $pucode) {
            $flag = 1;
        } else {
            $flag = 0;
        }
        return $flag;
    }


    //动态是否被 申诉过
    public function is_appeal($c_id,$ucode){
        $where['c_tip_id']=$c_id;
        $where['c_ucode']=$ucode;
        $find =M('Usertip_appeal')->where($where)->find();
        if(empty($find)){
            $flag =0;
        }else{
            $flag =1;
        }
        return $flag;
    }


    //用户对该动态是否举报

    public function is_tip($c_id,$ucode){

        if(empty($ucode)){
            return 0;
        }
        $where['c_content_id']=$c_id;
        $where['c_ucode']=$ucode;
        $find =M('Usertip_record')->where($where)->find();

        if(empty($find)){
            $flag =0;
        }else{
            $flag =1;
        }
        return $flag;

    }

    //用户是否点赞
    public function is_like($resourceid, $ucode) {
        if (empty($ucode)) {
            return 0;
        }

        $w['c_ucode'] = $ucode;
        $w['c_resourceid'] = $resourceid;

        $count = M('Resource_like')->where($w)->count();

        if ($count == 0) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        return $flag;
    }

    //获取前台显示时间
    public function GetShowTime($time, $sign) {
        if ($sign == 0) {
            $time = time() - strtotime($time);
            $year = floor($time / 60 / 60 / 24 / 365);
            $time -= $year * 60 * 60 * 24 * 365;
            $month = floor($time / 60 / 60 / 24 / 30);
            $time -= $month * 60 * 60 * 24 * 30;
            $week = floor($time / 60 / 60 / 24 / 7);
            $time -= $week * 60 * 60 * 24 * 7;
            $day = floor($time / 60 / 60 / 24);
            $time -= $day * 60 * 60 * 24;
            $hour = floor($time / 60 / 60);
            $time -= $hour * 60 * 60;
            $minute = floor($time / 60);
            $time -= $minute * 60;
            $second = $time;
            $elapse = '';

            $unitArr = array('年' => 'year', '个月' => 'month', '周' => 'week', '天' => 'day',
                '小时' => 'hour', '分钟' => 'minute', '秒' => 'second'
            );

            foreach ($unitArr as $cn => $u) {
                if ($$u > 0) {
                    $elapse = $$u . $cn;
                    break;
                }
            }
            if (empty($elapse)) {
                $backtime = '刚刚';
            } else {
                $backtime = $elapse . '前';
            }
        } else if ($sign == 1) {
            $backtime = $time;
            $month = substr($time, 5, 2);
            $day = substr($time, 8, 2);
            $ttime = substr($time, 11, 5);
            $backtime = $month . '/' . $day . '|' . $ttime;
        } else {
            $year = substr($time, 2, 2);
            $month = substr($time, 5, 2);
            $day = substr($time, 8, 2);
            $ttime = substr($time, 11, 5);
            $backtime = $year . '/' . $month . '/' . $day . ' ' . $ttime;
        }

        return $backtime;
    }

    /**
     *  添加资源
     *  @param ucode,content,pcode,isaddress,address,longitude,latitude,provincecode,citycode
     */
    public function AddResource($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }
        $add['c_ucode'] = $parr['ucode'];
        $add['c_content'] = $parr['content'];
        $add['c_status'] = 1;
        $add['c_longitude'] = $parr['longitude'];
        $add['c_latitude'] = $parr['latitude'];
        $add['c_isaddress'] = $parr['isaddress'];
        if ($parr['isaddress'] == 1) {
            if (empty($parr['address'])) {
                return Message(1024, '位置信息为空');
            }
            $add['c_address'] = $parr['address'];
        }
        $add['c_provincecode'] = $parr['provincecode'];
        $add['c_citycode'] = $parr['citycode'];
        $add['c_type'] = 1;
        $add['c_addtime'] = date('Y-m-d H:i:s', time());
        $add['c_updatetime'] = date('Y-m-d H:i:s', time());

        $db = M('');
        $db->startTrans();
        $result = M('Resource')->add($add);
        $rid = $result;
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '添加资源失败');
        }

        if (!empty($parr['pcode'])) {
            //添加推荐商品
            $pdata['c_resourceid'] = $rid;
            $pdata['c_pcode'] = $parr['pcode'];
            $result = M('resource_product')->add($pdata);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '推荐商品添加失败');
            }
        }

        //添加图片
        $imglist = $parr['imglist'];
        // $image = new \Think\Image();
        foreach ($imglist as $key => $value) {
            $imgdata['c_sourceid'] = 2;
            $imgdata['c_regionid'] = $rid;
            // $imgoption = SYS_PATH . str_replace('/', DS, $value);
            // $thumbimg = 'Uploads/source/thumb/' . date('Y-m-d') . '/tb_' . $key . time() . '.jpg';
            // $thumbimgpath = SYS_PATH . str_replace('/', DS, $thumbimg);
            // checkDir($thumbimgpath);
            // $image->open($imgoption)->thumb(150, 150)->save('./' . $thumbimg);
            $imgdata['c_img'] = $value;
            $imgdata['c_thumbnail_img'] = $value;
            $result = M('Resource_img')->add($imgdata);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '资源图片添加失败');
            }
        }

        //增加商圈资源数  
        $nw['c_provincecode'] = $parr['provincecode'];
        $nw['c_citycode'] = $parr['citycode'];
       
        $result = M('Circle')->where($nw)->setInc('c_resourcenum',1);

        //增加商圈人气  OptionMoods
        $param['provincecode'] = $parr['provincecode'];
        $param['citycode'] = $parr['citycode'];
        $param['add_moods'] = 5;
        $result = IGD('Circle','Trade')->OptionMoods($param);

        //给关注用户发送动态消息
        $userw['c_ucode'] = $parr['ucode'];
        $nickname = M('Users')->where($userw)->getField('c_nickname');

        $w['c_attention_ucode'] = $parr['ucode'];
        $ucode_list = M('Users_attention')->field('c_ucode')->where($w)->select(); //发送对象ucode

        if (!empty($ucode_list)) {
            foreach ($ucode_list as $key => $value) {
                $Msgcentre = IGD('Msgcentre', 'Message');
                $msgdata['ucode'] = $value['c_ucode'];
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['content'] = "您所关注的用户'" . $nickname . "'又发表资源动态了，前去围观！";
                $msgdata['tag'] = 10;
                $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $rid;
                $msgdata['tagvalue'] = $rid;
                $Msgcentre->CreateMessege($msgdata);
            }
        }

        $db->commit();
        return Message(0, '添加成功');
    }

    /**
     *  删除资源信息
     *  @param sid 资源id，ucode
     */
    public function DeleteResource($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $sid = $parr['sid'];

        $w['c_id'] = $sid;
        $resource_info = M('Resource')->where($w)->field('c_ucode,c_type,c_videourl')->find();
        if ($parr['ucode'] != $resource_info['c_ucode']) {
            return Message(1010, "您没有权限删除");
        }

        $db = M('');
        $db->startTrans();

        $result = M('Resource')->where($w)->delete();

        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除资源数据失败');
        }

        //删除图片
        $imgw['c_regionid'] = $sid;
        $imgw['c_sourceid'] = 2;

        $imglist = M('Resource_img')->field('c_thumbnail_img')->where($imgw)->select();
        if (!empty($imglist)) {
            foreach ($imglist as $key => $value) {
                unlink($value);
            }

            $result = M('Resource_img')->where($imgw)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '删除图片失败');
            }
        }

        $pw['c_resourceid'] = $sid;
        //删除推荐商品表
        $result = M('Resource_product')->where($pw)->delete();
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除推荐商品失败');
        }

        //删除资源评论表
        $result = M('Resource_comment')->where($pw)->delete();
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除评论列表失败');
        }

        //删除资源点赞表
        $result = M('Resource_like')->where($pw)->delete();
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除点赞记录表失败');
        }

        //删除视频资源
        if($resource_info['c_type'] == 2){
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey = C(Access_Key);
            $secretKey = C(Secret_Key);

            // 构建鉴权对象
            $auth = new \Com\Qiniu\src\Auth($accessKey,$secretKey);
            // 构建对象
            $BucketManager = new \Com\Qiniu\src\BucketManager($auth);

            // 空间名
            $bucket = C(Bucket_Name_Video);
            //去除外链域名，取出文件名
            $wipeoff = 'https://'.C(Explicit_Link).'/';
            $key = str_replace($wipeoff, '',$resource_info['c_videourl']);

            $result = $BucketManager->delete($bucket,$key);
        }

        //减少商圈资源数  
        $nw['c_provincecode'] = $resource_info['provincecode'];
        $nw['c_citycode'] = $resource_info['citycode'];
       
        $result = M('Circle')->where($nw)->setInc('c_resourcenum',1);

        $db->commit();
        return Message(0, '删除成功');
    }

    /**
     *  点赞、取消点赞
     *  @param sid,handle 1-点赞，0-取消点赞,ucode点赞用户编号，resourceid
     */
    public function ResourceLike($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录！");
        }

        $db = M('');
        $db->startTrans();

        $flage = $parr['handle'];
        $where['c_id'] = $parr['resourceid'];
        //查询记录条件
        $data['c_ucode'] = $parr['ucode'];
        $data['c_resourceid'] = $parr['resourceid'];

        $result = M('Resource_like')->where($data)->getField('c_id');

        if($flage == 0 && !$result){
            return Message(1010, "操作失败！");
        }

        if($flage == 1 && $result){
            return Message(0, "点赞成功");
        }

        if ($flage == 0) {
            $result = M('Resource_like')->where($data)->delete();
            if (!$result) {
                $db->rollback();
                return Message(1011, "删除点赞记录失败！");
            }

            $result = M('Resource')->where($where)->setDec('c_like', '1');
            if (!$result) {
                $db->rollback();
                return Message(1012, "减少点赞量统计失败！");
            }

            $succeed_msg = "取消点赞成功";
        } else {
            $data['c_addtime'] = date('Y-m-d H:i:s');

            $result = M('Resource_like')->add($data);
            if (!$result) {
                $db->rollback();
                return Message(1011, "添加点赞记录失败！");
            }

            $result = M('Resource')->where($where)->setInc('c_like', '1');
            if (!$result) {
                $db->rollback();
                return Message(1012, "增加点赞量统计失败！");
            }

            $succeed_msg = "点赞成功";

            //给资源发布者发送信息
            $userinfo = M('Resource')->field('c_ucode')->where($where)->find();
            if ($userinfo) {
                $pdata['rid'] = $parr['resourceid'];
                $pdata['pucode'] = $userinfo['c_ucode']; //发表者
                $pdata['ucode'] = $parr['ucode']; //点赞
                $this->PostMsg($pdata, 1);
            }

            //增加商圈人气  OptionMoods
            $param['provincecode'] = $parr['provincecode'];
            $param['citycode'] = $parr['citycode'];
            $param['add_moods'] = 2;
            $result = IGD('Circle','Trade')->OptionMoods($param);
        }

        $db->commit();
        return Message(0, $succeed_msg);
    }

    /**
     *  关注、取消关注
     *  @param handle 1-关注，0-取消关注,ucode用户编号,issue_ucode被关注用户编码
     */
    public function UserAttention($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录！");
        }

        if ($parr['ucode'] == $parr['issue_ucode']) {
            return Message(1008, "自己不能关注自己！");
        }

        $db = M('');
        $db->startTrans();

        $flage = $parr['handle'];
        //关注记录查询条件
        $w['c_ucode'] = $parr['ucode'];
        $w['c_attention_ucode'] = $parr['issue_ucode'];
        if ($flage == 1) {
            //添加关注记录
            $count = M('users_attention')->where($w)->count();

            if ($count > 0) {
                return Message(1010, "您已经关注该用户，无法重新关注！");
            }

            $save_date['c_ucode'] = $parr['ucode'];
            $save_date['c_attention_ucode'] = $parr['issue_ucode'];
            $save_date['c_addtime'] = date('Y-m-d H:i:s');

            $result = M('users_attention')->add($save_date);

            if (!$result) {
                $db->rollback();
                return Message(1011, "添加关注记录失败！");
            }

            //添加被关注统计
            $where['c_ucode'] = $parr['issue_ucode'];
            $count1 = M('users_date')->where($where)->count();
            if ($count1 == 0) {
                $save_date1['c_ucode'] = $parr['issue_ucode'];
                $save_date1['c_attention'] = 1;
                $result = M('users_date')->add($save_date1);
                if (!$result) {
                    $db->rollback();
                    return Message(1012, "添加关注统计记录失败！");
                }
            } else {
                $result = M('users_date')->where($where)->setInc('c_attention', '1');
                if (!$result) {
                    $db->rollback();
                    return Message(1013, "增加关注量统计失败！");
                }
            }
            $succeed_msg = "关注成功";

            //给被关注者发送信息
            if ($parr['issue_ucode']) {
                $pdata['pucode'] = $parr['issue_ucode'];
                $pdata['ucode'] = $parr['ucode'];
                $this->PostMsg($pdata, 4);
            }
        } else {

            $find = M('users_attention')->where($w)->find();
            if(empty($find)){
                return Message(1016, "您已经取消关注该用户了！");
            }

            //删除关注记录
            $result = M('users_attention')->where($w)->delete();

            if (!$result) {
                $db->rollback();
                return Message(1014, "删除关注记录失败！");
            }

            //减少关注统计量
            $where['c_ucode'] = $parr['issue_ucode'];
            $result = M('users_date')->where($where)->setDec('c_attention', '1');
            if (!$result) {
                $db->rollback();
                return Message(1015, "减少关注量统计失败！");
            }
            $succeed_msg = "取消关注成功";
        }

        $db->commit();
        return Message(0, $succeed_msg);
    }

    /**
     *  评论信息
     *  @param content,resourceid,ucode,bid
     */
    public function CommentResource($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $w['c_id'] = $parr['resourceid'];
        $count = M('Resource')->where($w)->count();
        if ($count == 0) {
            return Message(1000, '评论失败,资源不存在');
        }

        $data['c_content'] = $parr['content'];
        $data['c_resourceid'] = $parr['resourceid'];
        $data['c_ucode'] = $parr['ucode'];
        $upucode = '';
        if ($parr['bid'] != '' && $parr['bid'] != null && $parr['bid'] != 0) {
            $data['c_bid'] = $parr['bid'];

            $w1['c_id'] = $parr['bid'];
            $comment_uinfo = M('Resource_comment')->where($w1)->field('c_ptid,c_ucode')->find();
            // var_dump($comment_uinfo);die;
            $ptid = $comment_uinfo['c_ptid'];
            $upucode = $comment_uinfo['c_ucode'];

            $data['c_upucode'] = $upucode;

            if (!empty($ptid)) {
                $data['c_ptid'] = $ptid;
            } else {
                return Message(1001, '评论失败,该评论已经被删除');
            }
        } else {
            $data['c_bid'] = 0;
        }
        $data['c_addtime'] = date('Y-m-d H:i:s');

        $db = M('');
        $db->startTrans();
        $result = M('Resource_comment')->add($data);
        if (!$result) {
            $db->rollback();
            return Message(1002, '评论失败');
        }

        if ($parr['bid'] == '') {
            $ptid = $result;
        }

        $id = $result;

        if (empty($data['c_ptid'])) {
            $save['c_ptid'] = $ptid;
            $where['c_id'] = $id;
            $result = M('Resource_comment')->where($where)->save($save);
            if ($result <= 0) {
                $db->rollback();
                return Message(1003, '评论失败');
            }
        }

        //添加资源的评论数
        $arr['sid'] = $parr['resourceid'];
        $arr['handle'] = 1;
        $arr['num'] = 1;
        $result = $this->ResourceComment($arr);
        if ($result['code'] != 0) {
            $bd->rollback();
            return $result;
        }       

        //给资源发布者发送信息
        $w['c_id'] = $parr['resourceid'];
        $userinfo = M('Resource')->field('c_ucode')->where($w)->find();
        if ($userinfo) {
            $pdata['rid'] = $parr['resourceid'];
            $pdata['pucode'] = $userinfo['c_ucode'];
            $pdata['ucode'] = $parr['ucode'];
            $this->PostMsg($pdata, 2);
        }

        //给评论者发送消息
        if ($upucode != '') {
            $pdata1['rid'] = $parr['resourceid'];
            $pdata1['pucode'] = $upucode;
            $pdata1['ucode'] = $parr['ucode'];
            $this->PostMsg($pdata1, 3);
        }

        //返回评论数据
        $join = 'left join t_users as u on r.c_ucode=u.c_ucode';
        $join1 = 'left join t_users as pu on r.c_upucode=pu.c_ucode';
        $field = 'r.*,u.c_ucode,u.c_headimg,u.c_nickname,pu.c_ucode as upucode,pu.c_headimg as upheadimg,pu.c_nickname as upnickname';
        $commentwhere['r.c_id'] = $id;
        $comment = M('resource_comment as r')->join($join)->join($join1)->where($commentwhere)->field($field)->select();

        if ($comment) {
            $comment = $this->packedData($comment);
        }

        //增加商圈人气  OptionMoods
        $param['provincecode'] = $parr['provincecode'];
        $param['citycode'] = $parr['citycode'];
        $param['add_moods'] = 2;
        $result = IGD('Circle','Trade')->OptionMoods($param);

        $db->commit();
        return MessageInfo(0, '评论成功', $comment);
    }

    /**
     *  删除评论及子评论
     *  @param ucode,cid
     */
    public function DeleteComment($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $where['c_id'] = $parr['cid'];
        $comment_info = M('Resource_comment')->field('c_resourceid,c_ucode')->where($where)->find();

        // $where1['c_id'] = $comment_info['c_resourceid'];
        // $r_ucode = M('Resource')->where($where1)->getField('c_ucode');
        // if($parr['ucode'] != $r_ucode){
        if ($parr['ucode'] != $comment_info['c_ucode']) {
            return Message(1010, "您没有权限删除");
        }
        // }

        $db = M('');
        $db->startTrans();

        $cid = M('Resource_comment')->where($where)->getField('c_ptid');
        if ($cid == $parr['cid']) {
            $where2['c_ptid'] = $cid;
            $count = M('Resource_comment')->where($where2)->count();
            $result = M('Resource_comment')->where($where2)->delete();
        } else {
            $count = M('Resource_comment')->where($where)->count();
            $result = M('Resource_comment')->where($where)->delete();
        }

        if (!$result) {
            $db->rollback();
            return Message(1003, '删除失败');
        }

        //删除资源评论数
        $arr['sid'] = $comment_info['c_resourceid'];
        $arr['handle'] = 0;
        $arr['num'] = $count;

        $result = $this->ResourceComment($arr);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        $db->commit();
        return Message(0, '删除成功');
    }

    //给关注的用户发送动态消息,flage 1-资源点赞、2-资源评论
    public function PostMsg($parr, $flage) {
        $w['c_ucode'] = $parr['ucode'];
        $nickname = M('Users')->where($w)->getField('c_nickname');

        switch ($flage) {
            case 1:
                $content = $nickname . "赞了我！";
                $weburl = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['rid'];
                $tag = 10;
                $tagvalue = $parr['rid'];
                break;
            case 2:
                $content = $nickname . "评论了我的动态！";
                $weburl = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['rid'];
                $tag = 10;
                $tagvalue = $parr['rid'];
                break;
            case 3:
                $content = $nickname . "回复了我！";
                $weburl = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['rid'];
                $tag = 10;
                $tagvalue = $parr['rid'];
                break;
            default:
                $content = $nickname . "关注了我！";
                $weburl = GetHost(1) . '/index.php/Home/Myspace/myfans';
                $tag = 11;
                $tagvalue = 2;
                break;
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['pucode'];
        $msgdata['type'] = 0;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = $content;
        $msgdata['tag'] = $tag;
        $msgdata['weburl'] = $weburl;
        $msgdata['tagvalue'] = $tagvalue;
        $msgdata['platform'] = 1;        
       
        $Msgcentre->CreateMessege($msgdata);
    }

    /**
     *  个人空间头部数据(包括会员、线上商家、实体店铺)
     *  @param ucode,perucode
     */
    public function PersonalDate($parr) {
        $db = M('Users as u');
        $w['u.c_ucode'] = $parr['perucode']; //被访问者

        $join = 'left join t_users_date as d on d.c_ucode=u.c_ucode';
        $join1 = 'left join t_user_local as l on l.c_ucode=u.c_ucode';
        $field = 'u.c_ucode,u.c_headimg,u.c_nickname,u.c_signature,u.c_isagent,u.c_shop,u.c_shopnum,u.c_sex,u.c_tab,c_trade,d.c_pv,d.c_attention,l.c_address';
        $data = $db->join($join)->join($join1)->where($w)->field($field)->find();

        if (empty($data)) {
            return Message(1001, '你访问的用户信息不存在！');
        }

        $data['c_headimg'] = GetHost() . '/' . $data['c_headimg'];
        $data['is_attention'] = $this->is_attention($parr['ucode'], $parr['perucode']);
        $data['share_title'] = "邀你参观【" . $data['c_nickname'] . "】的小蜜空间";
        $data['share_des'] = '小伙伴们快到我的小蜜空间中看看吧';
        if($data['c_shop']==1){
       		$data['share_url'] = GetHost(1) . "/index.php/Store/Index/index?fromucode=" . $parr['perucode'];
        }else{        	
        	$data['share_url'] = GetHost(1) . "/index.php/Home/Myspace/index?fromucode=" . $parr['perucode'];
        }

        if (!empty($data['c_shopnum'])) {
            $shopnum = $data['c_shopnum'];
            $sw['c_shopnum'] = $shopnum;
            $img = M('Shop_num')->where($sw)->getField('c_img');
            $data['shopnum_img'] = GetHost() . '/' . $img;
        } else {
            $data['shopnum_img'] = '';
        }

        if (empty($data['c_pv'])) {
            $data['c_pv'] = 0;
        }

        if (empty($data['c_attention'])) {
            $data['c_attention'] = 0;
        }

        //查询是否关注
        $aw['c_ucode'] = $parr['ucode'];
        $aw['c_attention_ucode'] = $parr['perucode'];
        $count = M('Users_attention')->where($aw)->count();
        if($count == 0){
            $data['is_attention'] = 0;
        }else{
            $data['is_attention'] = 1;
        }

        $data['shop_location'] = '';//实体店铺位置

        if ($data['c_shop'] == 1) {

            $shopwehere['c_ucode'] = $data['c_ucode'];
            $shopuserinfo = M('User_local')->where($shopwehere)->find();

            if ($shopuserinfo['c_isfixed'] == 1) {
                $data['c_source'] = "2";
                $data['shop_location'] = $shopuserinfo['c_address'];
            } else {
                $data['c_source'] = "1";
            }
        } else {
            $data['c_source'] = "0";
        }

        if (!empty($parr['ucode']) && ($parr['perucode'] == $parr['ucode'])) {
            $where['c_ucode'] = $parr['ucode'];
            $data['my_attention'] = M('users_attention')->where($where)->count(); //我的关注
        } else {
            //增加个人空间浏览量
            $wv['ucode'] = $parr['ucode'];
            $wv['vucode'] = $parr['perucode'];
            $wv['source'] = $parr['source'];

            $w1['c_ucode'] = $parr['ucode'];
            $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
            $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];

            $wv['nickname'] = $userinfo['c_nickname'];
            $wv['headimg'] = $userinfo['c_headimg'];
            // $wv['browser'] = GetBrowser();
            $wv['ip'] = GetIP();
            $wv['type'] = 1;

            $result = $this->SpacePv($wv);
            if ($result['code'] != 0) {
                return $result;
            }
        }

        //查询商家用户归属商圈
        $circle_w['a.c_ucode'] = $parr['perucode'];
        $field = 'b.c_name';
        $join = "left join t_circle as b on a.c_citycode=b.c_citycode";

        $circle_info = M('Circle_visit as a')->field($field)->join($join)->where($circle_w)->find();

        $data['circle_name'] = $circle_info['c_name'];

        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  个人店铺头部数据
     *  @param ucode,perucode
     */
    public function PersonalShop($parr) {
        $db = M('Users as u');
        $w['u.c_ucode'] = $parr['perucode'];

        $join = 'left join t_users_date as d on d.c_ucode=u.c_ucode';
        $join1 = 'left join t_user_local as l on l.c_ucode=u.c_ucode';
        $field = 'u.c_ucode,u.c_headimg,u.c_nickname,u.c_shopnum,u.c_signature,u.c_isagent,u.c_shop,u.c_sex,u.c_tab,c_trade,d.c_pv,d.c_attention,l.c_address,l.c_isfixed';
        $data = $db->join($join)->join($join1)->where($w)->field($field)->find();

        if (empty($data)) {
            return Message(1001, "查询失败");
        }

        $whereinfo['c_ucode'] = $parr['perucode'];


        $shopuserinfo = M('User_local')->where($whereinfo)->field('c_isfixed,c_address')->find();

        $data['c_isfixed'] = $shopuserinfo['c_isfixed'];

        if ($shopuserinfo['c_isfixed'] == 1) {
            $data['c_source'] = "2";
            $data['shop_location'] = $shopuserinfo['c_address'];
        } else {
            $data['c_source'] = "1";
        }

        $data['is_attention'] = $this->is_attention($parr['ucode'], $parr['perucode']);
        $data['c_headimg'] = GetHost() . '/' . $data['c_headimg'];
        $data['share_title'] = "邀你参观【" . $data['c_nickname'] . "】的小蜜店铺";
        $data['share_des'] = '小伙伴们快到我的小蜜店铺中看看吧';
        $data['share_url'] = GetHost(1) . "/index.php/Store/Index/index?fromucode=" . $parr['perucode'];

        if (!empty($data['c_shopnum'])) {
            $shopnum = $data['c_shopnum'];
            $sw['c_shopnum'] = $shopnum;
            $img = M('Shop_num')->where($sw)->getField('c_img');
            $data['shopnum_img'] = GetHost() . '/' . $img;
        } else {
            $data['shopnum_img'] = '';
        }

        if (empty($data['c_pv'])) {
            $data['c_pv'] = 0;
        }

        if (empty($data['c_attention'])) {
            $data['c_attention'] = 0;
        }

        //查询商家用户归属商圈
        $circle_w['a.c_ucode'] = $parr['perucode'];
        $field = 'b.c_name';
        $join = "left join t_circle as b on a.c_citycode=b.c_citycode";

        $circle_info = M('Circle_visit as a')->field($field)->join($join)->where($circle_w)->find();

        $data['circle_name'] = $circle_info['c_name'];

        if ($parr['ucode'] != $parr['perucode']) {
	        //增加个人空间浏览量
	        $wv['ucode'] = $parr['ucode'];
	        $wv['vucode'] = $parr['perucode'];
	        $wv['source'] = $parr['source'];

	        $w1['c_ucode'] = $parr['ucode'];
	        $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
	        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];

	        $wv['nickname'] = $userinfo['c_nickname'];
	        $wv['headimg'] = $userinfo['c_headimg'];
	        // $wv['browser'] = GetBrowser();
	        $wv['ip'] = GetIP();
	        $wv['type'] = 2;

	        $result = $this->SpacePv($wv);
	    }

        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  增加资源浏览次数
     *  @param sid
     */
    public function ResourceClick($parr) {
        $where['c_id'] = $parr['sid'];
        $result = M('Resource')->where($where)->setInc('c_click', '1');
        if (!$result) {
            return Message(1000, "增加查看记录失败");
        }
        return Message(0, "查看成功");
    }

    /**
     *  增加资源浏览次数
     *  @param sid
     */
    public function ResourceComment($parr) {
        $handle = $parr['handle'];
        $num = $parr['num'];
        $id = $parr['sid'];

        $where['c_id'] = $id;
        if ($handle == 1) {
            $result = M('Resource')->where($where)->setInc('c_comment', $num);
        } else {
            $result = M('Resource')->where($where)->setDec('c_comment', $num);
        }
        if (!$result) {
            return Message(1000, "修改评论数量失败");
        }
        return Message(0, "修改成功");
    }

    /**
     *  添加个人空间浏览记录
     *  @param c_ucode，c_vucode
     */
    public function SpacePv($parr) {
        $db = M('');
        $db->startTrans();

        $add_date['c_ucode'] = $parr['ucode'];
        $add_date['c_vucode'] = $parr['vucode'];

        if (!empty($parr['nickname'])) {
            $add_date['c_username'] = $parr['nickname'];
        } else {
            $add_date['c_username'] = '未知用户';
        }

        if (!empty($parr['headimg'])) {
            $add_date['c_headimg'] = $parr['headimg'];
        } else {
            $add_date['c_headimg'] = GetHost() . '/Uploads/user.png';
        }

        $add_date['c_source'] = $parr['source'];
        $add_date['c_ip'] = $parr['ip'];
        $add_date['c_type'] = $parr['type'];
        $add_date['c_browser'] = $parr['browser'];
        $add_date['c_addtime'] = date('Y-m-d H:i:s');

        $result = M('Users_spacelog')->add($add_date);

        if (!$result) {
            $db->rollback();
            return Message(1000, "增加访问记录失败");
        }

        $w['c_ucode'] = $parr['vucode'];
        $count = M('users_date')->where($w)->count();

        if ($count == 0) {
            $adata['c_ucode'] = $parr['vucode'];
            $adata['c_pv'] = 1;
            $result1 = M('users_date')->add($adata);
        } else {
            $result1 = M('users_date')->where($w)->setInc('c_pv', '1');
        }

        if (!$result) {
            $db->rollback();
            return Message(1001, "增加个人空间访问量失败");
        }

        $db->commit();
        return Message(0, "查看成功");
    }

    /**
     *  添加推荐商品记录
     *  @param ucode，pcode
     */
    public function ProductVisit($parr) {
        $add_date['c_ucode'] = $parr['ucode'];
        $add_date['c_pcode'] = $parr['pcode'];

        if (!empty($parr['nickname'])) {
            $add_date['c_username'] = $parr['nickname'];
        } else {
            $add_date['c_username'] = '未知用户';
        }

        if (!empty($parr['headimg'])) {
            $add_date['c_headimg'] = $parr['headimg'];
        } else {
            $add_date['c_headimg'] = GetHost() . '/Uploads/user.png';
        }

        $add_date['c_source'] = $parr['source'];
        $add_date['c_ip'] = $parr['ip'];
        $add_date['c_browser'] = $parr['browser'];
        $add_date['c_addtime'] = date('Y-m-d H:i:s');

        $result = M('product_visit')->add($add_date);

        if (!$result) {
            return Message(1000, "增加访问记录失败");
        }

        return Message(0, "添加成功");
    }

    /**
     *  获取个人商铺商品数据
     *  @param shop_ucode，type，pageindex，pagesize
     */
    public function GetproductList($parr) {

        if (empty($parr['pageindex'])) {
            $pageindex = 1;
        } else {
            $pageindex = $parr['pageindex'];
        }
        $pagesize = $parr['pagesize'];
        $countPage = ($pageindex - 1) * $pagesize;

        $type = $parr['type'];

        $whereinfo = "c_ishow =1 and c_isdele=1 and c_ucode='" . $parr['shop_ucode'] . "'";
        $field = 'c_pcode,c_name,c_pimg,c_price';
        $orderid = '';
        if (!empty($type) && $type == 1) {
            $orderid .= 'c_addtime desc,';
        } else if (!empty($type) && $type == 2) {
            $orderid .= 'c_salesnum desc,';
        } else if (!empty($type) && $type == 3) {
            $orderid .= 'c_price asc,';
        } else if (!empty($type) && $type == 4) {
            $orderid .= 'c_price desc,';
        }

        $orderid .= 'c_id desc';
        $list = M('product')->where($whereinfo)->order($orderid)->limit($countPage, $pagesize)->select();

        $dataCount = M('product')->where($whereinfo)->count();
        $pageCount = ceil($dataCount / $pagesize);

        if (count($list) <= 0) {
            $list = array();
            $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
            return MessageInfo(0, '查询成功', $listinfo);
        }

        foreach ($list as $k => $v) {
            $list[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimg'];
        }

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return MessageInfo(0, '查询成功', $listinfo);
    }

    /**
     *  获取商铺全部商品数据
     *  @param type，pageindex，pagesize
     */
    public function GetAllproductList($parr) {

        if (empty($parr['pageindex'])) {
            $pageindex = 1;
        } else {
            $pageindex = $parr['pageindex'];
        }
        $pagesize = $parr['pagesize'];
        $countPage = ($pageindex - 1) * $pagesize;

        $type = $parr['type'];

        $whereinfo = "c_ishow =1 and c_isdele=1 ";
        $orderid = '';
        if (!empty($type) && $type == 1) {
            $orderid .= ' ';
        } else if (!empty($type) && $type == 2) {
            $orderid .= 'c_salesnum desc,';
        } else if (!empty($type) && $type == 3) {
            $orderid .= 'c_price asc,';
        } else if (!empty($type) && $type == 4) {
            $orderid .= 'c_price desc,';
        }

        $orderid .= 'c_id desc';
        $list = M('product')->where($whereinfo)->order($orderid)->limit($countPage, $pagesize)->select();

        $dataCount = M('product')->where($whereinfo)->count();
        $pageCount = ceil($dataCount / $pagesize);

        if (count($list) <= 0) {
            $list = array();
            $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
            return MessageInfo(0, '查询成功', $listinfo);
        }

        foreach ($list as $k => $v) {
            $list[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimg'];
        }

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return MessageInfo(0, '查询成功', $listinfo);
    }

    /**
     *  获取我的关注详细数据
     *  @param pageindex，pagesize，type
     */
    public function Myattention($parr) {
        if (empty($parr['pageindex'])) {
            $pageindex = 1;
        } else {
            $pageindex = $parr['pageindex'];
        }
        $pagesize = $parr['pagesize'];
        $countPage = ($pageindex - 1) * $pagesize;

        $ucode = $parr['ucode'];

        if ($parr['type'] == 1) {
            $w['t.c_ucode'] = $ucode;
            $join = 'left join t_users as u on t.c_attention_ucode=u.c_ucode';
        } else {
            $w['t.c_attention_ucode'] = $ucode;
            $join = 'left join t_users as u on t.c_ucode=u.c_ucode';
        }

        $field = 'u.c_ucode,u.c_nickname,u.c_headimg,u.c_shop,t.c_addtime,t.c_id';
        $orderid = 'u.c_id desc';

        $list = M('users_attention as t')->where($w)->field($field)->join($join)->order($orderid)->limit($countPage, $pagesize)->select();

        $dataCount = M('users_attention as t')->where($w)->join($join)->count();
        $pageCount = ceil($dataCount / $pagesize);

        if (count($list) <= 0) {
            $list = array();
            $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
            return MessageInfo(0, '查询成功', $listinfo);
        }

        if ($parr['type'] == 1) {
            foreach ($list as $k => $v) {
                $list[$k]['c_headimg'] = GetHost() . '/' . $list[$k]['c_headimg'];
                $list[$k]['c_addtime'] = $this->GetShowTime($list[$k]['c_addtime'], 0);
                $list[$k]['is_attention'] = 1;

                //头像跳转标识
                $jumptype = 0;
                if ($v['c_shop'] == 1) {
                    //判断用户状态
                    $whereinfo['c_ucode'] = $v['c_ucode'];
                    $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
                    if ($c_isfixed == 1) {
                        $jumptype = 2;
                    } else {
                        $jumptype = 1;
                    }
                }

                $list[$k]['jumptype'] = $jumptype;
            }
        } else {
            foreach ($list as $k => $v) {
                $list[$k]['c_headimg'] = GetHost() . '/' . $list[$k]['c_headimg'];
                $list[$k]['c_addtime'] = $this->GetShowTime($list[$k]['c_addtime'], 0);
                $list[$k]['is_attention'] = $this->is_attention($ucode, $list[$k]['c_ucode']);

                //头像跳转标识
                $jumptype = 0;
                if ($v['c_shop'] == 1) {
                    //判断用户状态
                    $whereinfo['c_ucode'] = $v['c_ucode'];
                    $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
                    if ($c_isfixed == 1) {
                        $jumptype = 2;
                    } else {
                        $jumptype = 1;
                    }
                }

                $list[$k]['jumptype'] = $jumptype;
            }
        }

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return MessageInfo(0, '查询成功', $listinfo);
    }

    /**
     *  获取用户所有产品列表
     *  @param  pageindex,pagesize,
     *      
     */
    function GetProduceList($parr) {
        $pageSize = $parr['pagesize'];
        $ucode = $parr['ucode'];
        $type = $parr['type'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;


        if ($type == 0) {
            $where['c_ishow'] = 1;
        }

        $where['c_isdele'] = 1;
        $where['c_isagent'] = 0;
        $where['c_ucode'] = $ucode;

        $field = "*,'' as c_longitude,'' as c_latitude,'' as local_time";
        $list = M('Product')->where($where)->field($field)->limit($countPage, $pageSize)->select();
        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }
        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['c_distance'] = 0;
            $list[$key]['url'] = GetHost(1) . '/' . "index.php/Home/Shop/details?type=1&pcode=" . $value['c_pcode'];
        }
        $count = M('Product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //获取七牛token
    public function GetQiniuToken(){
        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = C(Access_Key);
        $secretKey = C(Secret_Key);

        // 构建鉴权对象
        $auth = new \Com\Qiniu\src\Auth($accessKey,$secretKey);

        // 要上传的空间
        $bucket = C(Bucket_Name_Video);

        $token = $auth->uploadToken($bucket);

        if(!$token){
            return Message(1001,"获取七牛token失败");
        }

        $data['token'] = $token;

        //七牛服务器域名
        $data['qiniu_url'] = 'https://'.C(Explicit_Link);
        return MessageInfo(0,"获取成功",$data);
    }

    //添加视频资源
    public function Addvideo($parr){
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $db = M('');
        $db -> startTrans();

        //获取最后一个/后边的字符
        $arr=explode("/", $parr['videourl']);
        $key=$arr[count($arr)-1];
        //获取视频第一帧缩略图
        $picture_id = $this->Qiniu_vframe($key);

        if(empty($picture_id)){
            $db->rollback(); //不成功，则回滚
            return Message(1001,"添加视频资源失败");
        }

        $add['c_ucode'] = $parr['ucode'];
        $add['c_content'] = $parr['content'];
        $add['c_videourl'] = $parr['videourl'];
        $add['c_status'] = 1;
        $add['c_longitude'] = $parr['longitude'];
        $add['c_latitude'] = $parr['latitude'];
        $add['c_isaddress'] = $parr['isaddress'];
        if ($parr['isaddress'] == 1) {
            if (empty($parr['address'])) {
                return Message(1002, '位置信息为空');
            }
            $add['c_address'] = $parr['address'];
        }
        $add['c_provincecode'] = $parr['provincecode'];
        $add['c_citycode'] = $parr['citycode'];
        $add['c_type'] = 2;
        $add['c_addtime'] = date('Y-m-d H:i:s', time());
        $add['c_updatetime'] = date('Y-m-d H:i:s', time());

        $result = M('Resource')->add($add);
        $rid = $result;
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1003,"添加视频资源失败");
        }

        //添加图片资源
        $imgdata['c_sourceid'] = 2;
        $imgdata['c_regionid'] = $rid;
        $imgdata['c_img'] = $picture_id;
        $imgdata['c_thumbnail_img'] = $picture_id;
        $result = M('Resource_img')->add($imgdata);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1004, '资源图片添加失败');
        }

        //添加推荐商品
        if (!empty($parr['pcode'])) {
            //添加推荐商品
            $pdata['c_resourceid'] = $rid;
            $pdata['c_pcode'] = $parr['pcode'];
            $result = M('resource_product')->add($pdata);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1005, '推荐商品添加失败');
            }
        }

        //增加商圈资源数  
        $nw['c_provincecode'] = $parr['provincecode'];
        $nw['c_citycode'] = $parr['citycode'];
       
        $result = M('Circle')->where($nw)->setInc('c_resourcenum',1);

        //增加商圈人气  OptionMoods
        $param['provincecode'] = $parr['provincecode'];
        $param['citycode'] = $parr['citycode'];
        $param['add_moods'] = 5;
        $result = IGD('Circle','Trade')->OptionMoods($param);

        $db->commit();
        return Message(0,"添加成功");
    }

    //获取七牛视频第一帧缩略图
    public function Qiniu_vframe($key){
        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = C(Access_Key);
        $secretKey = C(Secret_Key);

        // 构建鉴权对象
        $bucket = C(Bucket_Name_Video);
        $auth = new \Com\Qiniu\src\Auth($accessKey,$secretKey);
        // 构建鉴权对象
        $PersistentFop = new \Com\Qiniu\src\PersistentFop($auth,$bucket);

        $key = $key;
        $fops = "vframe/png/offset/1/w";
        $result = $PersistentFop->execute($key,$fops);

        $picture_id = '';

        if($result[0]){
            $picture_id = $result[0];
        }

        return $picture_id;
    }


}
