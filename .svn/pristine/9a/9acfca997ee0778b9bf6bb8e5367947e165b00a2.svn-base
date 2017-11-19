<?php

/**
 * 服务中心相关数据信息接口
 */
class MenuInfoServe {

	/*
     * 新版本获取服务中心菜单列表5.0
     *   @param version,ucode,terminal
     *
     */

    public function GetAppMenu($parr) {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];

        // $menuinfo = IGD('Common','Redis')->Rediesgetucode('Menu0_'.$ucode);
        // if ($menuinfo) {
        //     return MessageInfo(0, "查询成功", $menuinfo);
        // }

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->field('c_shop,c_isagent')->find();
        $isshop = $userinfoshop['c_shop'];
        $isfixed = M('User_local')->where($w1)->getField('c_isfixed');

        $field = 'c_id,c_pid,c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason,c_alias';
        $order = 'c_sort asc';

        /*分类查询*/
        // 头部通用菜单
        $w['c_pid'] = 0;
        $w['c_version_number'] = $version_number;
        $w['c_terminal_type'] = $terminal;
        $w['c_access'] = 4;
        $top = M('App_menu1')->where($w)->order($order)->field($field)->select();
        foreach ($top as $k1 => $v1) {
            $top[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
            $w['c_pid'] = $v1['c_id'];
            $top[$k1]['child_menu'] = M('App_menu1')->where($w)->order($order)->field($field)->select();
            foreach ($top[$k1]['child_menu'] as $k => $v) {
                $top[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            }
        }

        // 商家必备模块
        if ($isshop == 1) {
            //查询是否是联盟身份
            $cw['c_ucode'] = $parr['ucode'];
            $cw['c_status'] = 1;
            $chain_info = M('A_federation')->where($cw)->find();
            if ($chain_info['c_type'] == 1) {
                if ($chain_info['c_sign'] == 1) {
                    $w1[] = array("c_role=0 or c_role=3");
                } else {
                    $w1[] = array("c_role=0 or c_role=2");
                }                
            } else if ($chain_info['c_type'] == 2) {
                if ($chain_info['c_sign'] == 1) {
                    $w1[] = array("c_role=0 or c_role=5");
                } else {
                    $w1[] = array("c_role=0 or c_role=4");
                }
            } else {
                $w1[] = array("c_role=0");
            }

        	$w1['c_pid'] = 0;
        	$w1['c_version_number'] = $version_number;
        	$w1['c_terminal_type'] = $terminal;
        	if ($isfixed == 1) {
	            $w1[] = array('c_access=1 or c_access=3');
	        } else {
	            $w1[] = array('c_access=1 or c_access=2');
	        }
	        $mid = M('App_menu1')->where($w1)->order($order)->field($field)->select();
            foreach ($mid as $k1 => $v1) {
                $mid[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
                $w1['c_pid'] = $v1['c_id'];
                $mid[$k1]['child_menu'] = M('App_menu1')->where($w1)->order($order)->field($field)->select();
                foreach ($mid[$k1]['child_menu'] as $k => $v) {
                    $mid[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
                }
            }
        }

        //查询是否是收银员身份
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        if ($result['code'] == 0) {
            $w2[] = array("c_role=0 or c_role=1");
        } else {
            $w2[] = array("c_role=0");
        }

        // 通用会员必备模块
        $w2['c_pid'] = 0;
        $w2['c_version_number'] = $version_number;
        $w2['c_terminal_type'] = $terminal;
        $w2['c_access'] = 0;
        $bot = M('App_menu1')->where($w2)->order($order)->field($field)->select();
        foreach ($bot as $k1 => $v1) {
            $bot[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
            $w2['c_pid'] = $v1['c_id'];
            $bot[$k1]['child_menu'] = M('App_menu1')->where($w2)->order($order)->field($field)->select();
            foreach ($bot[$k1]['child_menu'] as $k => $v) {
                $bot[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            }
        }

        $data[0]['name'] = '';
        $data[0]['menu'] = $top;
        if (!$mid) {
            $data[1]['name'] = '用户必备';
            $data[1]['menu'] = $bot;
        } else {
            $data[1]['name'] = '商家必备';
            $data[1]['menu'] = $mid;
            $data[2]['name'] = '用户必备';
            $data[2]['menu'] = $bot;
        }

        // IGD('Common', 'Redis')->RediesStoreSram('Menu0_'.$ucode,$data,84600);
        return MessageInfo(0, "查询成功", $data);
    }


    /*
     * 新版本获取服务中心菜单列表3.0.1
     *   @param version,ucode,terminal
     *
     */

    public function GetAppMenu1($parr) {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];

        // $menuinfo = IGD('Common','Redis')->Rediesgetucode('Menu1_'.$ucode);
        // if ($menuinfo) {
        //     return MessageInfo(0, "查询成功", $menuinfo);
        // }

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->field('c_shop,c_isagent')->find();
        $isshop = $userinfoshop['c_shop'];
        $isfixed = M('User_local')->where($w1)->getField('c_isfixed');

        $field = 'c_id,c_pid,c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason,c_alias';
        $order = 'c_sort asc';

        /*分类查询*/
        // 头部通用菜单
        $w['c_pid'] = 0;
        $w['c_version_number'] = $version_number;
        $w['c_terminal_type'] = $terminal;
        $w['c_access'] = 4;
        $top = M('App_menu1')->where($w)->order($order)->field($field)->select();
        foreach ($top as $k1 => $v1) {
            $top[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
            $w['c_pid'] = $v1['c_id'];
            $top[$k1]['child_menu'] = M('App_menu1')->where($w)->order($order)->field($field)->select();
            foreach ($top[$k1]['child_menu'] as $k => $v) {
                $top[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            }
        }

        // 商家必备模块
        if ($isshop == 1) {
            //查询是否是联盟身份
            $cw['c_ucode'] = $parr['ucode'];
            $cw['c_status'] = 1;
            $chain_info = M('A_federation')->where($cw)->find();
            if ($chain_info['c_type'] == 1) {
                if ($chain_info['c_sign'] == 1) {
                    $w1[] = array("c_role=0 or c_role=3");
                } else {
                    $w1[] = array("c_role=0 or c_role=2");
                }                
            } else if ($chain_info['c_type'] == 2) {
                if ($chain_info['c_sign'] == 1) {
                    $w1[] = array("c_role=0 or c_role=5");
                } else {
                    $w1[] = array("c_role=0 or c_role=4");
                }
            } else {
                $w1[] = array("c_role=0");
            }

            $w1['c_pid'] = 0;
            $w1['c_version_number'] = $version_number;
            $w1['c_terminal_type'] = $terminal;
            if ($isfixed == 1) {
                $w1[] = array('c_access=1 or c_access=3');
            } else {
                $w1[] = array('c_access=1 or c_access=2');
            }
            $mid = M('App_menu1')->where($w1)->order($order)->field($field)->select();
            foreach ($mid as $k1 => $v1) {
                $mid[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
                $w1['c_pid'] = $v1['c_id'];
                $mid[$k1]['child_menu'] = M('App_menu1')->where($w1)->order($order)->field($field)->select();
                foreach ($mid[$k1]['child_menu'] as $k => $v) {
                    $mid[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
                }
            }
        }

        //查询是否是收银员身份
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        if ($result['code'] == 0) {
            $w2[] = array("c_role=0 or c_role=1");
        } else {
            $w2[] = array("c_role=0");
        }

        // 通用会员必备模块
        $w2['c_pid'] = 0;
        $w2['c_version_number'] = $version_number;
        $w2['c_terminal_type'] = $terminal;
        $w2['c_access'] = 0;
        $bot = M('App_menu1')->where($w2)->order($order)->field($field)->select();
        foreach ($bot as $k1 => $v1) {
            $bot[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
            $w2['c_pid'] = $v1['c_id'];
            $bot[$k1]['child_menu'] = M('App_menu1')->where($w2)->order($order)->field($field)->select();
            foreach ($bot[$k1]['child_menu'] as $k => $v) {
                $bot[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            }
        }

        $data[0]['name'] = '';
        $data[0]['menu'] = $top;
        if (!$mid) {
            $data[1]['name'] = '用户必备';
            $data[1]['menu'] = $bot;
        } else {
            $data[1]['name'] = '商家必备';
            $data[1]['menu'] = $mid;
            $data[2]['name'] = '用户必备';
            $data[2]['menu'] = $bot;
        }

        // IGD('Common', 'Redis')->RediesStoreSram('Menu1_'.$ucode,$data,84600);
        return MessageInfo(0, "查询成功", $data);
    }

	/*
     * 新版本获取服务中心菜单列表3.0.3
     *   @param version,ucode,terminal
     *
     */

    public function GetAppMenu2($parr) {
        if (empty($parr['ucode'])) {
            $parr['ucode'] = '';
        }
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];
        $version_number = $parr['version'];

        // $menuinfo = IGD('Common','Redis')->Rediesgetucode('Menu2_'.$ucode);
        // if ($menuinfo) {
        //     return MessageInfo(0, "查询成功", $menuinfo);
        // }

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->field('c_shop,c_isagent')->find();
        $isshop = $userinfoshop['c_shop'];
        $isfixed = M('User_local')->where($w1)->getField('c_isfixed');

        $field = 'c_id,c_pid,c_name,c_img,c_interface_type,c_interface_address,c_isskip,c_reason,c_alias';
        $order = 'c_sort asc';

        /*分类查询*/
        // 头部通用菜单
        $w['c_pid'] = 0;
        $w['c_version_number'] = $version_number;
        $w['c_terminal_type'] = $terminal;
        $w['c_access'] = 4;
        $top = M('App_menu1')->where($w)->order($order)->field($field)->select();
        foreach ($top as $k1 => $v1) {
            $top[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
            $w['c_pid'] = $v1['c_id'];
            $top[$k1]['child_menu'] = M('App_menu1')->where($w)->order($order)->field($field)->select();
            foreach ($top[$k1]['child_menu'] as $k => $v) {
                $top[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            }
        }

        // 商家必备模块
        if ($isshop == 1) {
            //查询是否是联盟身份
            $cw['c_ucode'] = $parr['ucode'];
            $cw['c_status'] = 1;
            $chain_info = M('A_federation')->where($cw)->find();
            if ($chain_info['c_type'] == 1) {
                if ($chain_info['c_sign'] == 1) {
                    $w1[] = array("c_role=0 or c_role=3");
                } else {
                    $w1[] = array("c_role=0 or c_role=2");
                }                
            } else if ($chain_info['c_type'] == 2) {
                if ($chain_info['c_sign'] == 1) {
                    $w1[] = array("c_role=0 or c_role=5");
                } else {
                    $w1[] = array("c_role=0 or c_role=4");
                }
            } else {
                $w1[] = array("c_role=0");
            }

        	$w1['c_pid'] = 0;
        	$w1['c_version_number'] = $version_number;
        	$w1['c_terminal_type'] = $terminal;
        	if ($isfixed == 1) {
	            $w1[] = array('c_access=1 or c_access=3');
	        } else {
	            $w1[] = array('c_access=1 or c_access=2');
	        }
	        $mid = M('App_menu1')->where($w1)->order($order)->field($field)->select();
            foreach ($mid as $k1 => $v1) {
                $mid[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
                $w1['c_pid'] = $v1['c_id'];
                $mid[$k1]['child_menu'] = M('App_menu1')->where($w1)->order($order)->field($field)->select();
                foreach ($mid[$k1]['child_menu'] as $k => $v) {
                    $mid[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
                }
            }
        }

        //查询是否是收银员身份
        $result = IGD('Cashier','User')->GetCashierInfo($parr);
        if ($result['code'] == 0) {
            $w2[] = array("c_role=0 or c_role=1");
        } else {
            $w2[] = array("c_role=0");
        }

        //查询特权
        $tqw['c_ucode'] = $parr['ucode'];
        $tqinfo = M('Canread_messages')->where($tqw)->getField('c_id');

        // 通用会员必备模块
        $w2['c_pid'] = 0;
        $w2['c_version_number'] = $version_number;
        $w2['c_terminal_type'] = $terminal;
        if ($userinfoshop['c_isagent'] == 1) {
            if ($tqinfo) {
                $w2[] = array('c_access=0 or c_access=6 or c_access=7');
            } else {
                $w2[] = array('c_access=0 or c_access=6');
            }
        } else if ($userinfoshop['c_isagent'] == 2) {
            if ($tqinfo) {
                $w2[] = array('c_access=0 or c_access=5 or c_access=7');
            } else {
                $w2[] = array('c_access=0 or c_access=5');
            }
        } else {
            if ($tqinfo) {
                $w2[] = array('c_access=0 or c_access=7');
            } else {
                $w2[] = array('c_access=0');
            }
        }
        $bot = M('App_menu1')->where($w2)->order($order)->field($field)->select();
        foreach ($bot as $k1 => $v1) {
            $bot[$k1]['c_img'] = GetHost() . '/' . $v1['c_img'];
            $w2['c_pid'] = $v1['c_id'];
            $bot[$k1]['child_menu'] = M('App_menu1')->where($w2)->order($order)->field($field)->select();
            foreach ($bot[$k1]['child_menu'] as $k => $v) {
                $bot[$k1]['child_menu'][$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            }

            // if (!empty($parr['app_version'])) {
            //     if ($v1['c_name'] == '我的活动' && $terminal != 3) {
            //         $bot[$k1]['c_interface_type'] = 3;
            //         $bot[$k1]['c_interface_address'] = '41';
            //     }
            // }
        }

        $data[0]['name'] = '';
        $data[0]['menu'] = $top;
        if (!$mid) {
            $data[1]['name'] = '用户必备';
            $data[1]['menu'] = $bot;
        } else {
            $data[1]['name'] = '商家必备';
            $data[1]['menu'] = $mid;
            $data[2]['name'] = '用户必备';
            $data[2]['menu'] = $bot;
        }

        // IGD('Common', 'Redis')->RediesStoreSram('Menu2_'.$ucode,$data,84600);
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 服务中心头部数据获取
     * @param ucode,terminal(1安卓,2IOS,3WEB)
     */
    function GetTopInfo($parr)
    {
        $ucode = $parr['ucode'];
        $terminal = $parr['terminal'];

        //判断用户权限
        $w1['c_ucode'] = $ucode;
        $userinfoshop = M('Users')->where($w1)->field('c_shop,c_isagent')->find();
        $isshop = $userinfoshop['c_shop'];
        $isfixed = M('User_local')->where($w1)->getField('c_isfixed');

        if ($terminal == 3) {
            $left['c_interface_type'] = 2;
            $left['c_interface_address'] = GetHost(1).'/index.php/Home/Myspace/myattention';
        } else {
            $left['c_interface_type'] = 3;
            $left['c_interface_address'] = 29;
        }
        if (!empty($ucode)) {
            $w['c_ucode'] = $ucode;
            $countleft = M('Users_attention')->where($w)->count();
            $left['num'] = ($countleft > 0)?changenum($countleft):'0';
        } else {
            $left['num'] = '0';
        }
        
        $left['name'] = '关注';
        $data[] = $left;

        
        if ($isshop == 1) {
            $mid['name'] = '店铺动态';
        } else {
            $mid['name'] = '空间动态';
        }
        if ($terminal == 3) {
            $mid['c_interface_type'] = 2;
            $mid['c_interface_address'] = GetHost(1).'/index.php/Home/Myspace/index';
        } else {
            if ($isshop == 1) {
                $mid['c_interface_type'] = 3;
                $mid['c_interface_address'] = 13;
            } else {
                $mid['c_interface_type'] = 3;
                $mid['c_interface_address'] = 5;
            }
        }
        
        if (!empty($ucode)) {
            $w1['c_ucode'] = $ucode;
            $w1['c_status'] =1;
            $countmid = M('Resource')->where($w1)->count();
            $mid['num'] = ($countmid > 0)?changenum($countmid):'0';  
        } else {
            $mid['num'] = '0';
        }
              
        $data[] = $mid;


        if ($terminal == 3) {
            $right['c_interface_type'] = 2;
            $right['c_interface_address'] = GetHost(1).'/index.php/Home/Myspace/myfans';
        } else {
            $right['c_interface_type'] = 3;
            $right['c_interface_address'] = 11;
        }
       
        if (!empty($ucode)) {
            $w2['c_attention_ucode'] = $ucode;
            $countright = M('Users_attention')->where($w2)->count();
            $right['num'] = ($countright > 0)?changenum($countright):'0';
        } else {
            $right['num'] = '0';
        }
        $right['name'] = '粉丝';
        $data[] = $right;

        return MessageInfo(0, "查询成功", $data);
    }
}
