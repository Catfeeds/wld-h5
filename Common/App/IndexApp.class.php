<?php

/**
 *  软件版本控制及欢迎页控制
 */
class IndexApp {

    /**
     *  获取最新版本列表
     *  @param type,state
     */
    public function Getversion($parr) {
        $where['c_type'] = $parr['type'];
        $where['c_state'] = 1;
        $field = 'c_version,c_type,c_infro,c_url';
        $data = M('App_version')->where($where)->order('c_createtime desc')->field($field)->find();
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  获取app进入欢迎页引导图
     *  @param resolution,state,type
     */
    public function Getwelcome($parr) {
        $resolution = $parr['resolution'];

        $data['c_state'] = 1;
        $data['c_type'] = $parr['type'];
        $data['c_starttime'] = array('ELT', date('Y-m-d H:i:s'));
        $data['c_endtime'] = array('EGT', date('Y-m-d H:i:s'));

        $infro = M('App_welcome')->where($data)->order('c_addtime desc')->find();
        if (count($infro) == 0) {
            return Message(1002, "没有查询到需要的数据");
        }

        $datainfo['c_theme'] = $infro['c_theme'];
        $datainfo['c_type'] = $infro['c_type'];
        $datainfo['c_alias'] = $infro['c_alias'];
        if ($resolution == 1) {
            $datainfo['c_img'] = GetHost() . "/" . $infro['c_img480'];
        } elseif ($resolution == 2) {
            $datainfo['c_img'] = GetHost() . "/" . $infro['c_img720'];
        } elseif ($resolution == 3) {
            $datainfo['c_img'] = GetHost() . "/" . $infro['c_img1080'];
        } else {
            $datainfo['c_img'] = GetHost() . "/" . $infro['c_img1080'];
        }

        return MessageInfo(0, "查询成功", $datainfo);
    }

    /**
     *  获取服务中心菜单列表1.0
     *  @param version
     */
    public function Getmenu($parr) {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $isshop = M('Users')->where($w1)->getField('c_shop');

        $w['c_terminal_type'] = $terminal;
        $field = 'c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason';
        $order = 'c_sort asc';

        //会员权限
        if ($isshop == 0) {
            $w['c_access'] = 0;
        } else {
            $w['c_id'] = array('neq', 19);
        }

        if (!empty($version_number)) {
            $w['c_version_number'] = $version_number;
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        } else {
            $_field = 'max(c_discern) as discern';
            $discern = M('app_menu')->field($_field)->find();
            $w['c_discern'] = $discern['discern'];
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        }

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $data[$k]['c_img'];
        }
        if (!empty($data)) {
            return MessageInfo(0, "查询成功", $data);
        } else {
            return Message(2001, "查询失败");
        }
    }

    /*
     * 新版本获取服务中心菜单列表2.0
     *   @param version
     *   */

    public function GetNewmenu($parr) {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];
        $isshop = $parr['isshop'];

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->getField('c_shop');

        $w['c_terminal_type'] = $terminal;
        $field = 'c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason';
        $order = 'c_sort asc';

        //会员权限
        if ($isshop == 0 || empty($isshop)) {
            $w['c_access'] = 0;
        } else {
            $w['c_access'] = 1;
            $w[] = array('c_id<>24 and c_id<>25 and c_id<>26 and c_id<>27');
        }

        if (!empty($version_number)) {
            $w['c_version_number'] = $version_number;
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        } else {
            $_field = 'max(c_discern) as discern';
            $discern = M('app_menu')->field($_field)->find();
            $w['c_discern'] = $discern['discern'];
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        }

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $data[$k]['c_img'];
            $data[$k]['isshop'] = $userinfoshop;
            if ($v['c_id'] == 28 || $v['c_id'] == 30) {
                $data[$k]['c_img'] = 'http://m.iweilingdi.com/Uploads/Activity/sale/sale6.png';
            } elseif ($v['c_id'] == 29 || $v['c_id'] == 31) {
                $data[$k]['c_img'] = 'http://m.iweilingdi.com/Uploads/Activity/sale/sale5.png';
            }
        }
        if (!empty($data)) {
            return MessageInfo(0, "查询成功", $data);
        } else {
            return Message(2001, "查询失败");
        }
    }

    /*
     * 新版本获取服务中心菜单列表3.0
     *   @param version
     *   */

    public function GetNewmenuv2($parr) {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];
        $isshop = $parr['isshop'];

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->getField('c_shop');
        $isfixed = M('User_local')->where($w1)->getField('c_isfixed');

        $w['c_pid'] = 0;
        $w['c_terminal_type'] = $terminal;
        $field = 'c_id,c_pid,c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason';
        $order = 'c_sort asc';

        //会员权限
        if ($isshop == 0 || empty($isshop)) {
            $w['c_access'] = 0;
            $pwhere['c_access'] = 0;
        } else {
            if ($isfixed == 1) {
                $w[] = array('(c_access=1 or c_access=3) and c_id<>35 and c_id<>36 and c_id<>33 and c_id<>34');
                $pwhere[] = array('c_access=1 or c_access=3');
            } else {
                $w[] = array('c_access=1 or c_access=2');
                $pwhere[] = array('c_access=1 or c_access=2');
            }
        }

        if (!empty($version_number)) {
            $w['c_version_number'] = $version_number;
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        } else {
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        }

        foreach ($data as $k => $v) {

            if ($data[$k]['c_id'] == 1 or $data[$k]['c_id'] == 10) {
                $data[$k]['c_img'] = "http://wldappimg.iweilingdi.com/Uploads/appmenu/2016-12-16/jszx.png";
            } else if ($data[$k]['c_id'] == 2 or $data[$k]['c_id'] == 11) {
                $data[$k]['c_img'] = "http://wldappimg.iweilingdi.com/Uploads/appmenu/2016-12-16/fbzy.png";
            } else if ($data[$k]['c_id'] == 4 or $data[$k]['c_id'] == 13) {
                $data[$k]['c_img'] = "http://wldappimg.iweilingdi.com/Uploads/appmenu/2016-12-16/tgzx.png";
                $data[$k]['c_interface_type'] = 2;
                $data[$k]['c_interface_address'] = "http://m.iweilingdi.com/index.php/Home/Expand/index";
            } else {
                $data[$k]['c_img'] = GetHost() . '/' . $data[$k]['c_img'];
            }

            $data[$k]['isshop'] = $userinfoshop;

            $pwhere['c_pid'] = $v['c_id'];
            $pwhere['c_terminal_type'] = $terminal;
            $menu_list = M('app_menu')->where($pwhere)->order($order)->field($field)->select();
            foreach ($menu_list as $k1 => $v1) {
                $menu_list[$k1]['c_img'] = GetHost() . '/' . $menu_list[$k1]['c_img'];
            }

            $data[$k]['menu_list'] = $menu_list;
        }

        $count = count($data);
        if ($isshop == 0 || empty($isshop)) {
            $data[$count]['c_id'] = 0;
            $data[$count]['c_pid'] = 0;
            $data[$count]['c_name'] = "微商学院";
            $data[$count]['c_img'] = "http://wldappimg.iweilingdi.com/Uploads/appmenu/2016-12-16/wsxy.png";
            $data[$count]['c_interface_type'] = "2";
            $data[$count]['c_interface_address'] = "";
            $data[$count]['c_isskip'] = "1";
            $data[$count]['c_reason'] = "该功能还在开发中";
            $data[$count]['isshop'] = "0";
            $data[$count]['menu_list'];
        }

        if (!empty($data)) {
            return MessageInfo(0, "查询成功", $data);
        } else {
            return Message(2001, "查询失败");
        }
    }

    /*
     * 新版本获取服务中心菜单列表4.0
     *   @param version
     *   */

    public function GetNewmenuv3($parr) {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];
        $isshop = $parr['isshop'];

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->getField('c_shop');

        $isfixed = M('User_local')->where($w1)->getField('c_isfixed');

        $w['c_pid'] = 0;
        $w['c_terminal_type'] = $terminal;
        $field = 'c_id,c_pid,c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason';
        $order = 'c_sort asc';

        //会员权限
        if ($isshop == 0 || empty($isshop)) {
            $w['c_access'] = 0;
            $pwhere['c_access'] = 0;
        } else {
            if ($isfixed == 1) {
                $w[] = array('c_access=1 or c_access=3');
                $pwhere[] = array('c_access=1 or c_access=3');
            } else {
                $w[] = array('c_access=1 or c_access=2');
                $pwhere[] = array('c_access=1 or c_access=2');
            }
        }

        if (!empty($version_number)) {
            $w['c_version_number'] = $version_number;
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        } else {
            $_field = 'max(c_discern) as discern';
            $discern = M('app_menu')->field($_field)->find();
            $w['c_discern'] = $discern['discern'];
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        }

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $data[$k]['c_img'];
            $data[$k]['isshop'] = $userinfoshop;

            $pwhere['c_pid'] = $v['c_id'];
            $pwhere['c_terminal_type'] = $terminal;
            $menu_list = M('app_menu')->where($pwhere)->order($order)->field($field)->select();
            foreach ($menu_list as $k1 => $v1) {
                $menu_list[$k1]['c_img'] = GetHost() . '/' . $menu_list[$k1]['c_img'];
            }

            $data[$k]['menu_list'] = $menu_list;
        }
        if (!empty($data)) {
            return MessageInfo(0, "查询成功", $data);
        } else {
            return Message(2001, "查询失败");
        }
    }

    /*
     * 新版本获取服务中心菜单列表5.0
     *   @param version
     *   */

    public function GetNewmenuv4($parr) {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];
        $isshop = $parr['isshop'];

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->field('c_shop,c_isagent')->find();

        $isfixed = M('User_local')->where($w1)->getField('c_isfixed');

        $w['c_pid'] = 0;
        $w['c_terminal_type'] = $terminal;
        $field = 'c_id,c_pid,c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason';
        $order = 'c_sort asc';

        //会员权限
        if ($isshop == 0 || empty($isshop)) {
            $w['c_access'] = 0;
            $pwhere['c_access'] = 0;
        } else {
            if ($isfixed == 1) {
                $w[] = array('c_access=1 or c_access=3');
                $pwhere[] = array('c_access=1 or c_access=3');
            } else {
                $w[] = array('c_access=1 or c_access=2');
                $pwhere[] = array('c_access=1 or c_access=2');
            }
        }

        if (!empty($version_number)) {
            $w['c_version_number'] = $version_number;
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        } else {
            $_field = 'max(c_discern) as discern';
            $discern = M('app_menu')->field($_field)->find();
            $w['c_discern'] = $discern['discern'];
            $data = M('app_menu')->where($w)->order($order)->field($field)->select();
        }

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $data[$k]['c_img'];

            if (count($userinfoshop) == 0 || $userinfoshop['c_isagent'] > 0) {
                $data[$k]['isshop'] = 0;
            } elseif ($userinfoshop['c_shop'] == 1) {
                $data[$k]['isshop'] = 1;
            } else {
                $data[$k]['isshop'] = 2;
            }

            $pwhere['c_pid'] = $v['c_id'];
            $pwhere['c_terminal_type'] = $terminal;
            $menu_list = M('app_menu')->where($pwhere)->order($order)->field($field)->select();
            foreach ($menu_list as $k1 => $v1) {
                $menu_list[$k1]['c_img'] = GetHost() . '/' . $menu_list[$k1]['c_img'];
            }

            $data[$k]['menu_list'] = $menu_list;
        }
        if (!empty($data)) {
            return MessageInfo(0, "查询成功", $data);
        } else {
            return Message(2001, "查询失败");
        }
    }

    /**
     *  获取服务中心首页新闻列表
     */
    public function Getnews() {
        $w['c_type_rule'] = 1;
        $w['c_isshow'] = 1;
        $field = 'c_id,c_title,c_img,DATE_FORMAT(c_addtime,"%Y-%m-%d") as c_addtime';
        $order = 'c_istop desc,c_id desc,c_modify desc';
        $data = M('News')->where($w)->order($order)->field($field)->limit(2)->select();
        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $data[$k]['c_img'];
            $data[$k]['url'] = GetHost(1) . '/' . 'index.php/Home/News/details?id=' . $data[$k]['c_id'];
        }
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  获取所有文章列表
     */
    public function NewsList($parr) {
        $pageindex = $parr['pageindex'];
        $pagesize = $parr['pagesize'];
        $_where = "c_type_rule=1 and c_isshow=1";
        $order = 'c_istop desc,c_id desc,c_modify desc';
        if ($pageindex == 0 || $pageindex == '') {
            $pageindex = 1;
        }
        $countPage = ($pageindex - 1) * $pagesize;
        $_field = "c_id,c_title,c_img,c_anthor,c_click,c_addtime";
        $data = M('News')->where($_where)->order($order)->field($_field)->limit($countPage, $pagesize)->select();

        $dataCount = M('News')->where($_where)->count();
        $pageCount = ceil($dataCount / $pagesize);

        if (!$data) {
            $data = array();
            $list = Page($pageindex, $pageCount, $dataCount, $data);
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $data[$k]['c_img'];
            $data[$k]['url'] = GetHost(1) . '/' . 'index.php/Home/News/details?id=' . $data[$k]['c_id'];
        }

        $list = Page($pageindex, $pageCount, $dataCount, $data);
        return MessageInfo(0, "查询成功", $list);
    }

    /**
     *  获取新闻详情
     */
    public function Getdetails($Id) {
        $w['c_id'] = $Id;
        $result = M('News')->where($w)->setInc('c_click', '1');

        $_field = "c_title,c_anthor,c_addtime,c_content";
        $data = M('News')->where($w)->field($_field)->find();
        if (!empty($data)) {
            return MessageInfo(0, "查询成功", $data);
        } else {
            return Message(2001, "查询失败");
        }
    }

    /**
     * 首页活动中心
     * @param string $value [description]
     */

    /**
     * 查询平台活动列表
     * @param pageindex,pagesize
     */
    function ReferActivityList($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $activitywhere['c_state'] = 1;
        $activitywhere['c_show'] = 1;
        $limit = $countPage . ',' . $pageSize;
        $order = 'c_istop desc,c_ishot desc,c_activityendtime desc';
        $list = M('Activity')->where($activitywhere)->limit($limit)->order($order)->select();
        if (count($list) == 0) {
            return MessageInfo(0, "查询成功", $list);
        }
        foreach ($list as $key => $value) {
            $list[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['c_listimg'] = GetHost() . '/' . $value['c_listimg'];
            if (strtotime($value['c_activitystarttime']) > time()) {
                $list[$key]['progress'] = 0;
            } else if (strtotime($value['c_activitystarttime']) <= time() && strtotime($value['c_activityendtime']) >= time()) {
                $list[$key]['progress'] = 1;
            } else {
                $list[$key]['progress'] = 2;
            }
            $list[$key]['remind'] = '';
            $list[$key]['url'] = '';
            switch ($value['c_activitytype']) {
                case 1:     //常规红包
                    $list[$key]['remind'] = '全天24小时发送，点击首页面“发现”按钮搜索';
                    break;
                case 2:     //大转盘
                    $list[$key]['remind'] = '大转盘100%中奖，点击首页面“发现”按钮搜索';
                    break;
                case 6:     //商家红包
                    $list[$key]['remind'] = '每完成一笔跨商家消费累计红包，点击首页面“发现”按钮搜索';
                    break;
                case 11:
                    $list[$key]['url'] = GetHost(1) . '/index.php/Home/Promote/lottery';
                    break;
                case 13:
                    $list[$key]['url'] = GetHost(1) . '/index.php/Home/Promote/buying';
                    break;
                case 14:
                    $list[$key]['url'] = GetHost(1) . '/index.php/Home/Promote/seize';
                    break;
                case 15:
                    $list[$key]['url'] = GetHost(1) . '/index.php/Home/Jubao/index';
                    break;
                case 16:
                    $list[$key]['url'] = GetHost(1) . '/index.php/Home/H5Game';
                    break;
                case 18:
                    $list[$key]['url'] = GetHost(1) . '/index.php/Home/Valentines/index';
                    break;
                case 19:
                    $list[$key]['url'] = GetHost(1) . '/index.php/Home/Wordred/index';
                    break;
                default:
                    $list[$key]['url'] = '';
                    break;
            }
            if (strstr($value['c_activityname'], "老虎机")) {
                $list[$key]['url'] = '';
            }
            if (strstr($value['c_activityname'], "小蜜红包雨")) {
                $list[$key]['url'] = '';
                $list[$key]['remind'] = '每天12点和18点开始，每次持续15分钟';
            }
        }
        $count = M('Activity')->where($activitywhere)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

}
