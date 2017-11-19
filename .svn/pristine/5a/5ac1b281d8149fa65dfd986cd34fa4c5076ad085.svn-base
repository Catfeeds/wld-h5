<?php

/**
 * 微商盟接口
 */
class CoalitionTrade {

    /**
     *  获取用户列表
     *  @param pageindex,pagesize,province,city,town,tab,name,trade,sex
     *
     */
    function CoalitionUserList($parr) {

        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];
        $juli = $parr['juli'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if (!empty($parr['province'])) {
            $where['c_province'] = array('like', '%' . $parr['province'] . '%');
        }

        if (!empty($parr['city'])) {
            $where['c_city'] = array('like', '%' . $parr['city'] . '%');
        }

        if (!empty($parr['town'])) {
            $where['c_region'] = array('like', '%' . $parr['town'] . '%');
        }

        if (!empty($parr['tab'])) {
            $tabarr = explode('|', $parr['tab']);
            foreach ($tabarr as $key => $value) {
                if ($key == 0) {
                    $tabwhere .= "c_tab like '%" . $value . "%'";
                } else {
                    $tabwhere .= " or c_tab like '%" . $value . "%'";
                }
            }
            $where[] = $tabwhere;
        }

        if (!empty($parr['name'])) {
            $where['c_nickname'] = array('like', '%' . $parr['name'] . '%');
        }

        if (!empty($parr['trade'])) {
            $tradebarr = explode('|', $parr['trade']);
            foreach ($tradebarr as $key => $value) {
                if ($key == 0) {
                    $tradwhere .= "c_trade like '%" . $value . "%'";
                } else {
                    $tradwhere .= " or c_trade like '%" . $value . "%'";
                }
            }
            $where[] = $tradwhere;
        }

        if (!empty($parr['sex'])) {
            $where['c_sex'] = array('like', '%' . $parr['sex'] . '%');
        }

        if (!empty($longitude) && !empty($latitude)) {
            if ($juli > 0) {
                $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-b.c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(b.c_latitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-b.c_longitude)/360),2)))) <= ' . $parr['juli'] . '';
            }
            $order = 'case when ifnull(c_latitude,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (c_longitude * 3.1415) / 180 ) ) * 6380 asc';
            // $tempwhere = 'c_latitude > ' . $latitude . '-1 and c_latitude < ' . $latitude . '+1 and c_longitude > ' . $longitude . '-1 and c_longitude < ' . $longitude . '+1';
            // $where[] = $tempwhere;
        } else {
            $order = 'a.c_id desc';
        }

        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $field = "a.c_ucode,a.c_nickname,a.c_signature,a.c_province,a.c_sex,a.c_city,a.c_region,a.c_headimg,a.c_tab,a.c_trade,a.c_shop,b.c_latitude,b.c_longitude,'' as c_rongyun_token";

        $list = M('Users as a')->join($join)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

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

            if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
                $strb = "未知距离";
            } else {

                $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
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

        $count = M('Users as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    // 为H5提供会员列表查询
    function CoalitionUserwebList($parr) {

        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];
        $juli = $parr['juli'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if (!empty($parr['province'])) {
            $where['c_province'] = array('like', '%' . $parr['province'] . '%');
        }

        if (!empty($parr['city'])) {
            $where['c_city'] = array('like', '%' . $parr['city'] . '%');
        }

        if (!empty($parr['town'])) {
            $where['c_region'] = array('like', '%' . $parr['town'] . '%');
        }

        if (!empty($parr['tab'])) {
            $tabarr = explode('|', $parr['tab']);
            foreach ($tabarr as $key => $value) {
                if ($key == 0) {
                    $tabwhere .= "c_tab like '%" . $value . "%'";
                } else {
                    $tabwhere .= " or c_tab like '%" . $value . "%'";
                }
            }
            $where[] = $tabwhere;
        }

        if (!empty($parr['name'])) {
            $where['c_nickname'] = array('like', '%' . $parr['name'] . '%');
        }

        if (!empty($parr['trade'])) {
            $tradebarr = explode('|', $parr['trade']);
            foreach ($tradebarr as $key => $value) {
                if ($key == 0) {
                    $tradwhere .= "c_trade like '%" . $value . "%'";
                } else {
                    $tradwhere .= " or c_trade like '%" . $value . "%'";
                }
            }
            $where[] = $tradwhere;
        }

        if (!empty($parr['sex'])) {
            $where['c_sex'] = array('like', '%' . $parr['sex'] . '%');
        }

        if (!empty($longitude) && !empty($latitude)) {
            if ($juli > 0) {
                $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-b.c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(b.c_latitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-b.c_longitude)/360),2)))) <= ' . $parr['juli'] . '';
            }
            $order = 'case when ifnull(c_latitude,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (c_longitude * 3.1415) / 180 ) ) * 6380 asc';
            // $tempwhere = 'c_latitude > ' . $latitude . '-1 and c_latitude < ' . $latitude . '+1 and c_longitude > ' . $longitude . '-1 and c_longitude < ' . $longitude . '+1';
            // $where[] = $tempwhere;
        } else {
            $order = 'a.c_id desc';
        }

        $where['_logic'] = 'OR';

        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $field = "a.c_ucode,a.c_nickname,a.c_signature,a.c_province,a.c_sex,a.c_city,a.c_region,a.c_headimg,a.c_tab,a.c_trade,a.c_shop,b.c_latitude,b.c_longitude,'' as c_rongyun_token";

        $list = M('Users as a')->join($join)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        if (!$list) {
            return MessageInfo(0, '查询成功', $list);
        }

        foreach ($list as $key => $value) {
            if (empty($value['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
            $list[$key]['c_friendstate'] = 0;

            if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
                $strb = "未知距离";
            } else {
                $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);

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

            $list[$key]['longitude'] = $value['c_longitude'];
            $list[$key]['latitude'] = $value['c_latitude'];
            $list[$key]['c_distance'] = $strb;
            //$list[$key]['c_distance'] = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
        }

        $count = M('Users as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  获取个人空间用户数据,并判断是否是是好友
     *  @param fromucode,
     * @param ucode
     *
     */
    public function GetUserInfo($parr) {
        $fromucode = $parr['fromucode'];
        $ucode = $parr['ucode'];

        $where['a.c_ucode'] = $fromucode;

        $join = 'left join t_user_part as b on a.c_ucode=b.c_ucode';
        $field = "a.c_ucode,a.c_nickname,a.c_signature,a.c_province,a.c_sex,a.c_city,a.c_region,a.c_headimg,a.c_tab,a.c_trade,a.c_shop,'' as c_latitude,'' as c_longitude,b.c_rongyun_token,'0' as c_distance";
        //查询用户信息
        $userinfo = M('Users as a')->join($join)->where($where)->field($field)->find();

        if (count($userinfo) == 0) {
            return MessageInfo(0, "查询成功", $userinfo);
        }

        //判断是否是好友
        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        if (!empty($ucode)) {
            $wherefriend['c_user_ucode'] = $fromucode;
            $wherefriend['c_friend_ucode'] = $fromucode;
            $wherefriend['c_status'] = 1;
            $count = M('Friend_relation')->where($wherefriend)->count();

            if ($count > 0) {
                $userinfo['c_friendstate'] = 1;
            } else {
                $userinfo['c_friendstate'] = 0;
            }
        } else {
            $userinfo['friendstate'] = 0;
        }

        return MessageInfo(0, "查询成功", $userinfo);
    }

    /**
     * 操作用户关注与取消关注
     * @param ucode,atucode
     */
    public function OptionUserAttention($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_attention_ucode'] = $parr['atucode'];
        $attentionid = M('Users_attention')->where($where)->getField('c_id');
        if ($attentionid) {
            $result = M('Users_attention')->where($where)->delete();
            if (!$result) {
                return Message(1000,'取消关注失败');
            }
            return Message(0,'取消关注成功');
        }

        $where['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Users_attention')->add($where);
        if (!$result) {
            return Message(1000,'关注失败');
        }
        return Message(0,'关注成功');
    }
}
