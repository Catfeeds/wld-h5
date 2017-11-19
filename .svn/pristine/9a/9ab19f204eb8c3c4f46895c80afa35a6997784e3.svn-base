<?php

/**
 * 用户信息、用户地址信息接口
 *
 */
class UserUser {

    /**
     * 获取用经纬度
     * @param string $ucode 用户编码
     * @param double $longitude 经度
     * @param double $latitude 纬度
     * @param string $address 地址
     */
    function save_local($parr) {
        $db = M('user_local');

        $ucode = $parr['ucode'];
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];
        $address = $parr['address'];

        // 判断经纬度是否为空
        if (empty($longitude) || empty($latitude) || $longitude <= 0 || $latitude <= 0) {
            $localresult = GetAreafromIp();
            $longitude = $localresult['longitude'];
            $latitude = $localresult['latitude'];
            $address = $localresult['address'];
        }

        if ($longitude == $latitude) {
            $localresult = GetAreafromIp();
            $longitude = $localresult['longitude'];
            $latitude = $localresult['latitude'];
            $address = $localresult['address'];
        }

        if (empty($longitude) || empty($latitude)) {
            return Message(2003, "地理位置获取失败");
        }

        if (empty($ucode)) {
            return Message(2004, "用户编码不存在");
        }        


        $db->startTrans();

        $where['c_ucode'] = $ucode;
        $usercal = $db->where($where)->find();
        if (!$usercal) {
            //根据经纬度获取省市
            $getlocal = file_get_contents("http://api.map.baidu.com/cloudrgc/v1?location=".$latitude.",".$longitude."&geotable_id=135675&coord_type=bd09ll&ak=lIqrLulxigbplnce2Ol5IG46ePXX2KLS");
            $result = objarray_to_array(json_decode($getlocal));
            $province = $result['address_component']['province'];
            $province = str_replace('省', '', $province);
            $province = str_replace('市', '', $province);

            $city = $result['address_component']['city'];
            $city = str_replace('市', '', $city);

            $county = $result['address_component']['district'];

            $add_data = array(
                'c_ucode' => $ucode,
                'c_longitude' => $longitude,
                'c_latitude' => $latitude,
                'c_province' => $province,
                'c_city' => $city,
                'c_county' => $county,
                'c_address' => $address,
                'c_updatetime' => date('Y-m-d H:i:s', time()),
                'c_addtime' => date('Y-m-d H:i:s', time()),
            );
            $result = $db->add($add_data);

            $users_save = array(
                'c_longitude1' => $longitude,
                'c_latitude1' => $latitude,
                'c_address1' => $address,
            );
            $result1 = M('Users')->where($where)->save($users_save);
        } else {
            if ($usercal['c_isfixed'] != 1 || ($usercal['c_isfixed'] == 1 && $usercal['c_longitude'] <= 0)) {
                //根据经纬度获取省市
                $getlocal = file_get_contents("http://api.map.baidu.com/cloudrgc/v1?location=".$latitude.",".$longitude."&geotable_id=135675&coord_type=bd09ll&ak=lIqrLulxigbplnce2Ol5IG46ePXX2KLS");
                $result = objarray_to_array(json_decode($getlocal));
                $province = $result['address_component']['province'];
                $province = str_replace('省', '', $province);
                $province = str_replace('市', '', $province);

                $city = $result['address_component']['city'];
                $city = str_replace('市', '', $city);

                $county = $result['address_component']['district'];

                $save_data = array(
                    'c_longitude' => $longitude,
                    'c_latitude' => $latitude,
                    'c_province' => $province,
                    'c_city' => $city,
                    'c_county' => $county,
                    'c_address' => $address,
                    'c_updatetime' => date('Y-m-d H:i:s', time()),
                );
                $result = $db->where($where)->save($save_data);

                $users_save = array(
                    'c_longitude1' => $longitude,
                    'c_latitude1' => $latitude,
                    'c_address1' => $address,
                );
                $result1 = M('Users')->where($where)->save($users_save);
            } else {
                $result = true;
            }
        }

        if (!$result) {
            $db->rollback();
            return Message(2001, "保存失败");
        }

        //同步redis位置信息
        $result = IGD('Local','Lbs')->UpinfoLocal($ucode);

        $db->commit();
        return Message(0, "保存成功");
    }

    /**
     * 用户资源添加或编辑
     * @param string $ucode 用户编码
     * @param array $parr 数据
     * @param int $action_flag 操作标识 0-添加，1-编辑
     */
    function handle_resource($ucode, $parr, $action_flag) {
        $db = M('resource');

        if ($action_flag == 0) {
            $data = array(
                'c_ucode' => $ucode,
                'c_title' => $parr['c_title'],
                'c_content' => $parr['c_content'],
                'c_addtime' => date('Y-m-d H:i:s', time()),
                'c_updatetime' => date('Y-m-d H:i:s', time()),
            );
            $result = $db->add($data);
        } else {
            $where['c_id'] = $parr['c_id'];
            $data = array(
                'c_title' => $parr['c_title'],
                'c_content' => $parr['c_content'],
                'c_updatetime' => date('Y-m-d H:i:s', time()),
            );
            $result = $db->where($where)->save($data);
        }

        if ($result) {
            return Message(0, "保存成功");
        } else {
            return Message(502, "保存失败");
        }
    }

    /**
     *  用户资源删除
     *  @param int $resource_id 资源ID
     */
    function delete_resource($resource_id) {
        $db = M('resource');
        $where['c_id'] = $resource_id;
        $data = array(
            'c_status' => 0,
            'c_updatetime' => date('Y-m-d H:i:s', time()),
        );

        $result = $db->where($where)->save($data);

        if ($result) {
            return Message(0, "删除成功");
        } else {
            return Message(502, "删除失败");
        }
    }

    /*
      添加用户收货地址
      @param array $parr
     */

    function UserAddress($parr) {

        $ucode = $parr['ucode'];

        $db = M('');
        $db->startTrans();
        $isdefault = $parr['isdefault'];

        if ($isdefault == 1) {
            $whereaddress['c_ucode'] = $ucode;
            $save['c_is_default'] = 0;
            $result = M('Users_address')->where($whereaddress)->save($save);
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1015, "地址添加失败1");
            }
        }

        $whereadd['c_ucode'] = $parr['ucode'];
        $whereadd['c_consignee'] = $parr['consignee'];
        $whereadd['c_mobile'] = $parr['mobile'];
        $whereadd['c_province'] = $parr['province'];
        $whereadd['c_city'] = $parr['city'];
        $whereadd['c_district'] = $parr['district'];
        $whereadd['c_address'] = $parr['address'];
        $whereadd['c_provincename'] = $parr['provincename'];
        $whereadd['c_cityname'] = $parr['cityname'];
        $whereadd['c_districtname'] = $parr['districtname'];
        $whereadd['c_addtime'] = date('Y-m-d H:i:s', time());

        if ($isdefault == 1) {
            $whereadd['c_is_default'] = 1;
        } else {
            $whereadd['c_is_default'] = 0;
        }


        $result = M('Users_address')->add($whereadd);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1015, "地址添加失败");
        }
        $db->commit();
        $whereadd['c_id'] = $result;
        return MessageInfo(0, "地址添加成功", $whereadd);
    }

    /**
     * 编辑用户收货地址
     * @param array $parr
     */
    function EditUserAddress($parr) {

        $ucode = $parr['ucode'];
        $db = M('');
        $db->startTrans();
        $isdefault = $parr['isdefault'];
        if ($isdefault == 1) {
            $whereaddress['c_ucode'] = $ucode;
            $save['c_is_default'] = 0;
            $result = M('Users_address')->where($whereaddress)->save($save);
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1015, "修改地址失败");
            }
        }


        $where['c_id'] = $parr['id'];

        $whereadd['c_ucode'] = $parr['ucode'];
        $whereadd['c_consignee'] = $parr['consignee'];
        $whereadd['c_mobile'] = $parr['mobile'];
        $whereadd['c_province'] = $parr['province'];
        $whereadd['c_city'] = $parr['city'];
        $whereadd['c_district'] = $parr['district'];
        $whereadd['c_address'] = $parr['address'];
        $whereadd['c_provincename'] = $parr['provincename'];
        $whereadd['c_cityname'] = $parr['cityname'];
        $whereadd['c_districtname'] = $parr['districtname'];
        $whereadd['c_addtime'] = date('Y-m-d H:i:s', time());

        if ($isdefault == 1) {
            $whereadd['c_is_default'] = 1;
        } else {
            $whereadd['c_is_default'] = 0;
        }

        $result = M('Users_address')->where($where)->save($whereadd);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1015, "修改地址失败");
        }

        $db->commit();
        $whereadd['c_id'] = $parr['id'];
        return MessageInfo(0, "地址添加成功", $whereadd);
    }

    /**
     * 设置用户默认收货地址
     * @param array $parr
     */
    function SetAddress($parr) {

        $ucode = $parr['ucode'];
        $db = M('');
        $db->startTrans();

        $whereaddress['c_ucode'] = $ucode;
        $save['c_is_default'] = 0;
        $result = M('Users_address')->where($whereaddress)->save($save);
        // if ($result < 0) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(1015, "设置失败");
        // }

        $where['c_id'] = $parr['id'];
        $save['c_is_default'] = 1;
        $result = M('Users_address')->where($where)->save($save);

        // if (!$result) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(1015, "设置失败");
        // }
        $db->commit();
        return Message(0, "设置默认收货地址成功");
    }

    /*
      删除用户收货地址
      @param int $id
     */

    function deleteUserAddress($parr) {
        $whereinfo['c_id'] = $parr['id'];
        $result = M('Users_address')->where($whereinfo)->delete();

        if (!$result) {
            return Message(1015, "删除失败");
        }
        return Message(0, "删除成功");
    }

    /*
      获取用户地址列表
      @param array $parr
     */

    function GetUserAddress($parr) {
        $whereadd['c_ucode'] = $parr['ucode'];
        $order = 'c_is_default desc';
        $list = M('Users_address')->where($whereadd)->order($order)->select();
        return MessageInfo(0, "查询成功", $list);
    }

    /*
      获取省市区
      @param array $parr
     */

    function GetAddress($parr) {
        $whereadd['parent_id'] = $parr['parentid'];
        $whereadd['region_type'] = $parr['regiontype'];
        $list = M('Region')->where($whereadd)->select();

        return $list;
    }

    /*
      获取单个地址信息
      @param array $parr
     */

    function FindAddress($parr) {
        $whereinfo['c_id'] = $parr['id'];
        $result = M('Users_address')->where($whereinfo)->find();
        return $result;
    }

    //获取默认地址
    public function Getdefaultaddress($parr) {
        $whereinfo['c_ucode'] = $parr['ucode'];
        $whereinfo['c_is_default'] = 1;
        $addressinfo = M('Users_address')->where($whereinfo)->find();
        return MessageInfo(0, "查询成功", $addressinfo);
    }

    //获取用户可用余额
    public function GetUserMoney($parr) {
        $ucode = $parr['ucode'];
        $whereinfo['c_ucode'] = $ucode;
        $field = 'c_money';
        $userinfo = M('Users')->where($whereinfo)->field($field)->find();

        if (count($userinfo) == 0) {
            return Message(1021, "查询用户金额失败");
        }

        if (empty($userinfo['c_money'])) {
            $userinfo['c_money'] = 0;
        }

        return MessageInfo(0, "获取用户余额成功", $userinfo);
    }

    //修改用户昵称
    public function Editnickname($parr) {

        $ucode = $parr['ucode'];
        $nickname = $parr['nickname'];

        $whereacodeinfo['c_ucode'] = array('NEQ', $ucode);
        $whereacodeinfo['c_nickname'] = array('EQ', $nickname);
        $count = M('Users')->where($whereacodeinfo)->count();

        if ($count > 0) {
            return Message(1021, "该昵称已被占用");
        }

        $wherestoreinfo['c_ucode'] = array('NEQ', $ucode);
        $wherestoreinfo['c_name'] = array('EQ', $nickname);
        $count1 = M('Store')->where($wherestoreinfo)->count();
        if ($count1 > 0) {
            return Message(1021, "该昵称已被占用");
        }

        $savewhere['c_ucode'] = $ucode;

         $save['c_nickname'] = $nickname;
        $resutl = M('Users')->where($savewhere)->save($save);

        if ($resutl > 0) {
            //刷新融云用户信息
            IGD('UserProcess', 'Rongcloud')->userRefresh($ucode);
             //如果用户是实体商家，修改店铺名称
            $w['c_ucode'] = $ucode;
            $isfixed = M('User_local')->where($w)->getField('c_isfixed');
            if($isfixed == 1){
                $savestore['c_name'] = $nickname;
                M('Store')->where($w)->save($savestore);
            }
            return Message(0, "修改成功");
        }

        return Message(1022, "该昵称修改失败");
    }

    public function Checknickname($parr) {
        $nickname = $parr['nickname'];
        $whereacodeinfo['c_nickname'] = array('EQ', $nickname);
        $whereacodeinfo['c_ucode'] = array('NEQ', $parr['ucode']);
        $count = M('Users')->where($whereacodeinfo)->count();

        if ($count > 0) {
            return Message(1025, "该昵称已被占用");
        }

        return Message(0, "该昵称没被占用");
    }

    //编辑标签
    public function EditTag($parr) {

        $whereinfo['c_ucode'] = $parr['ucode'];
        $save['c_tab'] = $parr['tag'];
        $resutl = M('Users')->where($whereinfo)->save($save);
        if ($resutl <= 0) {
            return Message(1021, "修改标签失败");
        }
        return Message(0, "修改成功");
    }

    /**
     * 上传用户头像
     * @param string $ucode 用户编码
     * @param string $file_path 头像上传路径
     */
    function upload_header($ucode, $file_path) {
        $db = M('users');
        $where['c_ucode'] = $ucode;
        $data['c_headimg'] = $file_path;
        $result = $db->where($where)->save($data);
        if ($result) {
            //刷新融云用户信息
            IGD('UserProcess', 'Rongcloud')->userRefresh($ucode);
            $info['c_headimg'] = GetHost().'/'.$file_path;
            return MessageInfo(0, "头像保存成功",$info);
        } else {
            return Message(1025, "头像保存失败");
        }
    }

    //编辑其他所有用户信息
    public function EditAllOther($parr) {

        $whereinfo['c_ucode'] = $parr['ucode'];
        $save['c_sex'] = $parr['sex'];
        $save['c_signature'] = $parr['signature'];
        $save['c_province'] = $parr['province'];
        $save['c_city'] = $parr['city'];
        $save['c_tab'] = $parr['tag'];
        $save['c_nickname'] = $parr['nickname'];
        $save['c_region'] = $parr['region'];
        $save['c_trade'] = $parr['trade'];

        $nickname = $parr['nickname'];
        $info['c_nickname'] = array('eq', trim($nickname));
        $info['c_ucode'] = array('neq', $parr['ucode']);
        $count = M('Users')->where($info)->find();
        if (!empty($count)) {
            return Message(1021, "该昵称已经被占用");
        }
        $resutl = M('Users')->where($whereinfo)->save($save);

        if ($resutl >= 0) {
            //刷新融云用户信息
            IGD('UserProcess', 'Rongcloud')->userRefresh($parr['ucode']);
            //如果用户是实体商家，修改店铺名称
            $w['c_ucode'] = $parr['ucode'];
            $isfixed = M('User_local')->where($w)->getField('c_isfixed');
            if($isfixed == 1){
                $savestore['c_name'] = $nickname;
                M('Store')->where($w)->save($savestore);
            }
            return Message(0, "修改成功");
        }

        return Message(1021, "修改信息失败");
    }

    //编辑其他信息
    public function EditOther($parr) {

        $whereinfo['c_ucode'] = $parr['ucode'];
        $save['c_sex'] = $parr['sex'];
        $save['c_signature'] = $parr['signature'];
        $save['c_province'] = $parr['province'];
        $save['c_city'] = $parr['city'];

        if (!empty($parr['headimg'])) {
             $save['c_headimg'] = $parr['headimg'];
        }

        $save['c_region'] = $parr['region'];
        $save['c_trade'] = $parr['trade'];

        $resutl = M('Users')->where($whereinfo)->save($save);
        if ($resutl <= 0) {
            return Message(1021, "您还尚未修改任何信息");
        }
        return Message(0, "修改成功");
    }

    //修改密码
    public function Editpass($parr) {
        $whereinfo['c_ucode'] = $parr['ucode'];

        $temppwd = $parr['pwd'];

        $password = M('Users')->where($whereinfo)->getfield('c_password');

        if ($temppwd != $password) {
            return Message(1021, "原密码错误");
        }

        $save['c_password'] = $parr['password'];

        $resutl = M('Users')->where($whereinfo)->save($save);
        if ($resutl <= 0) {
            return Message(1021, "新密码不能与原密码一致");
        }
        return Message(0, "修改成功");
    }

    //修改手机号码
    public function EditPhone($parr) {

        $whereinfo['c_phone'] = array('EQ', $parr['phone']);
        $count = M('Users')->where($whereinfo)->count();

        if ($count > 0) {
            return Message(1021, "该号码已经被占用");
        }

        $whereinfo1['c_ucode'] = $parr['ucode'];
        $save['c_phone'] = $parr['phone'];
        $resutl = M('Users')->where($whereinfo1)->save($save);
        if ($resutl <= 0) {
            return Message(1021, "修改手机号码失败");
        }
        return Message(0, "修改成功");
    }

    /**
     *  获取我的会员列表
     *  @param ucode
     */
    public function GetmyMembleList($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $join = 'inner join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.c_ucode,b.c_phone,b.c_headimg,a.c_addtime,b.c_shop,b.c_nickname,a.c_source';
        $where['a.c_pcode'] = $parr['ucode'];
        $order = 'a.c_addtime desc';
        $list = M('Users_tuijian as a')->join($join)->where($where)->field($field)->limit($countPage, $pageSize)->order($order)->select();

        foreach ($list as $key => $value) {

            if ($value['c_source'] == 1) {
                $list[$key]['c_sourcestr'] = "邀请码";
            } elseif ($value['c_source'] == 2) {
                $list[$key]['c_sourcestr'] = "商品购买";
            } elseif ($value['c_source'] == 3) {
                $list[$key]['c_sourcestr'] = "扫码支付";
            } else {
                $list[$key]['c_sourcestr'] = "邀请码";
            }

            if (empty($value['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
            $list[$key]['alerttime'] = date('Y/m/d H:i:s',strtotime($value['c_addtime']));

            //判断用户是否绑定微信
            $userwhere1['c_type'] = 1;
            $userwhere1['c_ucode'] = $value['c_ucode'];
            $countweixin = M('Users_auth')->where($userwhere1)->count();
            $list[$key]['iswx_auth'] = 0;
            if ($countweixin > 0) {
                $list[$key]['iswx_auth'] = 1;
            }

            //判断用户是否绑定支付宝
            $userwhere1['c_type'] = 2;
            $countalipay = M('Users_auth')->where($userwhere1)->count();
            $list[$key]['isal_auth'] = 0;
            if ($countalipay > 0) {
                $list[$key]['isal_auth'] = 1;
            }

            //判断用户是否实名认证
            $userwhere['c_ucode'] = $value['c_ucode'];
            $countcard = M('Users_bank')->where($userwhere)->count();
            $list[$key]['iscard_band'] = 0;
            if ($countcard > 0) {
                $list[$key]['iscard_band'] = 1;
            }

        }
        $count = $this->GetmyMembleCount($parr['ucode']);
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  查询我的会员总是数
     *  @param ucode
     */
    public function GetmyMembleCount($ucode) {
        $join = 'inner join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'b.c_phone,b.c_headimg,b.c_addtime,b.c_shop,b.c_nickname';
        $where['a.c_pcode'] = $ucode;
        $count = M('Users_tuijian as a')->join($join)->where($where)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

     /**
     *  查询我的推荐人
     *  @param ucode
     */
    public function Getmysup($parr) {
        //查询会员推荐人信息
        $uwhere['c_ucode'] = $parr['ucode'];
        $tj_info = M('Users_tuijian')->where($uwhere)->find();

        if(empty($tj_info)){
            return MessageInfo(0, "查询成功",$tj_info);
        }

        if($tj_info['c_source'] == 2){
            $source = "订单购买";
        }else if($tj_info['c_source'] == 3){
            $source = "扫码支付";
        }else{
            $source = "邀请码";
        }

        $w['u.c_ucode'] = $tj_info['c_pcode'];
        $db=M('Users as u ');
        $join = 'left join t_users_date as d on d.c_ucode=u.c_ucode';
        $field = 'u.c_ucode,u.c_headimg,u.c_nickname,u.c_signature,u.c_isagent,u.c_shop,u.c_shopnum,d.c_pv,d.c_attention';
        $data = $db->join($join)->where($w)->field($field)->find();

        if(empty($data)){
            return Message(1001, "数据出错");
        }

        $data['c_headimg'] = GetHost() . '/' . $data['c_headimg'];
        $data['c_binding'] = $source;
        $data['c_time'] = $tj_info['c_addtime'];

        if (empty($data['c_pv'])) {
            $data['c_pv'] = 0;
        }

        if (empty($data['c_attention'])) {
            $data['c_attention'] = 0;
        }

        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 查询微信绑定的临时会员
     * @param ucode,pageindex,pagesize,type(1微信，2支付宝)
     */
    function GetWxmebleList($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $field = '*';
        $where['c_lock'] = 2;
        $where['c_pcode'] = $parr['ucode'];
        if (!empty($parr['type'])) {
            $where['c_type'] = $parr['type'];
        }
        $order = 'c_id desc';
        $list = M('Scanpay_tuijian')->where($where)->field($field)->limit($countPage, $pageSize)->order($order)->select();

        foreach ($list as $key => $value) {
            if (!$value['c_name']) {
                if ($value['c_type'] == 1) {
                    $list[$key]['c_name'] = '微信用户'.$value['c_id'];
                } else if ($value['c_type'] == 2) {
                    $list[$key]['c_name'] = '支付宝用户'.$value['c_id'];
                }
            }

        }
        $count = $this->GetWxmebleCount($parr);
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 查询微信临时会员总数
     * @param ucode,type(1微信，2支付宝)
     */
    function GetWxmebleCount($parr)
    {
        if (empty($parr['ucode'])) {
            $parr['ucode'] = '';
        }
        $where['c_lock'] = 2;
        $where['c_pcode'] = $parr['ucode'];
        if (!empty($parr['type'])) {
            $where['c_type'] = $parr['type'];
        }
        $count = M('Scanpay_tuijian')->where($where)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }
}
