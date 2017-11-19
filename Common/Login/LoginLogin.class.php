<?php

class LoginLogin {

    /**
     * 根据用户key获取用户信息
     * @param key
     *
     */
    function GetUserByCode($ucode) {
        $whereinfo['c_ucode'] = $ucode;
        $whereinfo1['a.c_ucode'] = $ucode;
        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $userinfo = M('Users as a')->join($join)->where($whereinfo1)->field('a.*,b.c_isfixed')->find();
        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        $userinfo['c_rongyun_token'] = M('User_part')->where($whereinfo)->order('c_addtime desc')->getField('c_rongyun_token');

        if (count($userinfo) == 0) {
            return Message(1000, '查询失败');
        }

        //判断用户是否被绑定
        $wheretuijian['c_ucode'] = $ucode;
        $tuijiancount = M('Users_tuijian')->where($whereinfo)->count();

        if ($tuijiancount > 0) {
            $userinfo['tuijian'] = "1";
        } else {
            $userinfo['tuijian'] = "0";
        }

        //判断用户是否微信授权
        $whereweixin['c_ucode'] = $ucode;
        $whereweixin['c_type'] = 1;
        $tuijiancount = M('Users_auth')->where($whereweixin)->count();

        if ($tuijiancount > 0) {
            $userinfo['weixin'] = "1";
        } else {
            $userinfo['weixin'] = "0";
        }

        $whereweixin['c_type'] = 2;
        $alipaycount = M('Users_auth')->where($whereweixin)->count();
        if ($alipaycount > 0) {
            $userinfo['alipay'] = "1";
        } else {
            $userinfo['alipay'] = "0";
        }

        //查询商家用户归属商圈
        $circle_w['a.c_ucode'] = $ucode;
        $field = 'b.c_name';
        $join = "left join t_circle as b on a.c_citycode=b.c_citycode";

        $circle_info = M('Circle_shop as a')->field($field)->join($join)->where($circle_w)->find();

        $userinfo['circle_name'] = $circle_info['c_name'];
        $userinfo['c_isagent'] = 3;

        return MessageInfo(0, '查询成功', $userinfo);
    }

    //新版获取获取用户信息
    function GetUserByCode1($parr) {
        $ucode =$parr['ucode'];
        $whereinfo['c_ucode'] = $ucode;
        $whereinfo1['a.c_ucode'] = $ucode;
        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $userinfo = M('Users as a')->join($join)->where($whereinfo1)->field('a.*,b.c_isfixed')->find();
        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        $userinfo['c_rongyun_token'] = M('User_part')->where($whereinfo)->order('c_addtime desc')->getField('c_rongyun_token');

        if (count($userinfo) == 0) {
            return Message(1000, '查询失败');
        }

        //判断用户是否被绑定
        $wheretuijian['c_ucode'] = $ucode;
        $tuijiancount = M('Users_tuijian')->where($whereinfo)->count();

        if ($tuijiancount > 0) {
            $userinfo['tuijian'] = "1";
        } else {
            $userinfo['tuijian'] = "0";
        }

        //判断用户是否微信授权
        $whereweixin['c_ucode'] = $ucode;
        $whereweixin['c_type'] = 1;
        $tuijiancount = M('Users_auth')->where($whereweixin)->count();

        if ($tuijiancount > 0) {
            $userinfo['weixin'] = "1";
        } else {
            $userinfo['weixin'] = "0";
        }

        $whereweixin['c_type'] = 2;
        $alipaycount = M('Users_auth')->where($whereweixin)->count();
        if ($alipaycount > 0) {
            $userinfo['alipay'] = "1";
        } else {
            $userinfo['alipay'] = "0";
        }

        //查询商家用户归属商圈
        $circle_w['a.c_ucode'] = $ucode;
        $field = 'b.c_name';
        $join = "left join t_circle as b on a.c_citycode=b.c_citycode";

        $circle_info = M('Circle_shop as a')->field($field)->join($join)->where($circle_w)->find();

        $userinfo['circle_name'] = $circle_info['c_name'];
        if(empty($parr['app_version'])){
            $userinfo['c_isagent'] = 3;
        }
        
        return MessageInfo(0, '查询成功', $userinfo);
    }
    /**
     * 登录
     * @param phone,pwd,type,unionid
     *
     */
    function login($parr) {
        $phone = $parr['phone'];
        $pwd = $parr['pwd'];

        //第三方登录
        if (!empty($parr['type']) && !empty($parr['unionid'])) {
            $authparr['type'] = $parr['type'];
            $authparr['openid'] = $parr['unionid'];
            $result = IGD('Impower','Login')->AuthSeacher($authparr);
            if ($result['code'] != 0) {
                return MessageInfo(1000, '帐号不存在');
            }

            $userwhere['c_ucode'] = $result['data']['c_ucode'];
            $field = 'c_ucode,c_headimg,c_password';
            $userinfo = M('Users')->where($userwhere)->field($field)->find();
            if (count($userinfo) == 0) {
                return MessageInfo(1000, '帐号不存在');
            }
        } else {
            $userwhere['c_phone'] = $phone;
            $field = 'c_ucode,c_headimg,c_password';
            $userinfo = M('Users')->where($userwhere)->field($field)->find();
            if (count($userinfo) == 0) {
                return MessageInfo(1000, '帐号不存在');
            }

            $pwd = encrypt($pwd,C('ENCRYPT_KEY'));
            if ($userinfo['c_password'] != $pwd) {
                return MessageInfo(1001, '密码错误');
            }
        }

        //查询是否禁止登录
        $limparr['sign'] = 1;
        $limparr['ucode'] = $userinfo['c_ucode'];
        $result = $this->GetUserLimit($limparr);
        if ($result['code'] != 0) {
            return $result;
        }
        $db = M('');
        $db->startTrans();

        // 记录登录时间
        $userdata['c_lasttime'] = gdtime();
        $result = M('Users')->where($userwhere)->save($userdata);

        // 写入用户地理位置
        $localtion = GetAreafromIp();
        $addrparr['ucode'] = $userinfo['c_ucode'];
        $addrparr['longitude'] =  $localtion['longitude'];
        $addrparr['latitude'] = $localtion['latitude'];
        $addrparr['address'] = $localtion['address'];
        $data = IGD('User', 'User')->save_local($addrparr);

        // 保存第三方token
        $where['c_ucode'] = $userinfo['c_ucode'];
        $rwtoken = M('User_part')->where($where)->find();
        if (empty($rwtoken['c_rongyun_token'])) {
            $tokenresult = IGD('UserProcess', 'Rongcloud')->token($userinfo['c_ucode']);
            if ($tokenresult['code'] != 0) {
                $db->rollback();
                return Message(1005, '登录失败');
            }
        }

        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        $userinfo['openid'] = md5($userinfo['c_ucode']);
        IGD('Common', 'Redis')->RediesStoreSram($userinfo['openid'], $userinfo['c_ucode'],86400);
        $savedata['c_port'] = 'web';
        if (isset($parr['app_client'])) {
           $savedata['c_port'] = $parr['app_client'];
        }
        $savedata['c_sign'] = 1;
        $savedata['c_version'] = $parr['app_version'];
        $savedata['c_browser'] = GetBrowser();
        $savedata['c_ip'] = GetIP();
        $savedata['c_ucode'] = $userinfo['c_ucode'];
        $savedata['c_longitude'] =  $localtion['longitude'];
        $savedata['c_latitude'] = $localtion['latitude'];
        $savedata['c_address'] = $localtion['address'];
        $savedata['c_addtime'] = gdtime();

        $result = M('Login_record')->add($savedata);
        if (empty($result)) {
            $db->rollback();
            return Message(1006, '登录失败');
        } 
        $db->commit();
        return MessageInfo(0, '登录成功', $userinfo);
    }

    /**
     * 查询用户禁用信息
     * @param ucode,sign(1禁止登录,2禁止扫码)
     */
    function GetUserLimit($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_sign'] = $parr['sign'];
        $where['c_starttime'] = array('ELT', gdtime());
        $where['c_endtime'] = array('EGT', gdtime());
        $result = M('Users_limit')->where($where)->find();
        if ($result) {
            return Message(4000,$result['c_remarks']);
        }
        return Message(0,'没有禁用记录');
    }

    /**
     * 注册
     * @param phone,pwd,incode,unionid,headimg,nickname,sex
     *
     */
    function register($parr) {
        $incode = $parr['incode'];
        $phone = $parr['phone'];
        $pwd = encrypt($parr['pwd'],C('ENCRYPT_KEY'));

        //第三方授权注册
        if (!empty($parr['type']) && !empty($parr['unionid'])) {
            $authparr['type'] = $parr['type'];
            $authparr['openid'] = $parr['unionid'];
            $result = IGD('Impower','Login')->AuthSeacher($authparr);
            if ($result['code'] == 0) {
                $whereadd['openid'] = md5($result['data']['c_ucode']);
                IGD('Common', 'Redis')->RediesStoreSram($whereadd['openid'], $result['data']['c_ucode'],86400);
                return MessageInfo(0, '该账号已授权，可以进行授权登录', $whereadd);
            }

            $wherephone['c_phone'] = $phone;
            $userucode = M('Users')->where($wherephone)->getField('c_ucode');
            if ($userucode) {
                $parr['ucode'] = $userucode;
                return $this->AuthRegister($parr);
            }
        }

        $wherephone['c_phone'] = $phone;
        $userTelId = M('Users')->where($wherephone)->getField('c_id');
        if ($userTelId) {
            return Message(1000, '该手机号码已存在！');
        }


        $wherinfo['c_invitationcode'] = $incode;
        if (!empty($incode)) {
            $whereadd['c_level'] = 2; /* 初级会员 */
            $userinfo = M('Users')->where($wherinfo)->field("c_id,c_acode,c_num,c_ucode")->find();
            if (!$userinfo) {
                return Message(1001, '邀请码错误！');
            }
            if ($userinfo['c_num'] == 0) {
                return Message(1001, '邀请码已失效！');
            }
        } else {
            $whereadd['c_level'] = 1; /* 普通会员 */
        }

        $moheadimg = 'data/userheadimg/' . rand(11, 20) . '.jpg';
        if ($parr['sex'] == 1 || $parr['sex'] == 'M') {
            $whereadd['c_sex'] = '男';
        } else if ($parr['sex'] == 2 || $parr['sex'] == 'F') {
            $whereadd['c_sex'] = '女';
        }
        $ucode = CreateUcode();     /* 创建用户编码 */
        $whereadd['c_ucode'] = $ucode;
        $whereadd['c_phone'] = $phone;
        $whereadd['c_acode'] = $userinfo['c_acode'];  /* 继承推荐人商家id */
        $whereadd['c_signature'] = '蜜主很懒，没有什么个性签名！';
        $whereadd['c_password'] = $pwd;
        $whereadd['c_headimg'] = $moheadimg;
        $whereadd['c_addtime'] = gdtime();
        $whereadd['c_lasttime'] = gdtime();

        $db = M('');
        $db->startTrans(); /* 开启事务 */

        $result = M('Users')->add($whereadd);
        if (!$result) {
            $db->rollback();
            return Message(1002, '注册失败！');
        }

        $newwherinfo['c_id'] = $result;
        $newwheresave['c_nickname'] = '小蜜用户' . $result . CreateNick();   /* 默认昵称 */
        if (!empty($parr['nickname'])) {
            $newwheresave['c_nickname'] = filterEmoji($parr['nickname']);
        }
        $result = M('Users')->where($newwherinfo)->save($newwheresave);
        if (!$result) {
            $db->rollback();
            return Message(1002, '注册失败！');
        }

        if (!empty($incode)) {
            $result = M('Users')->where($wherinfo)->setDec('c_num', 1);
            if (!$result) {
                $db->rollback();
                return Message(1003, '修改推荐人数失败！');
            }

            $wheretj['c_source'] = 1;
            $wheretj['c_pcode'] = $userinfo['c_ucode'];
            $wheretj['c_ucode'] = $ucode;
            $wheretj['c_addtime'] = gdtime();
            $result = M('Users_tuijian')->add($wheretj);
            if (!$result) {
                $db->rollback();
                return Message(1004, '推荐关系绑定失败！');
            }
        }

        // 保存第三方token
        $tokenresult = IGD('UserProcess', 'Rongcloud')->token($ucode);
        if ($tokenresult['code'] != 0) {
            $db->rollback();
            return Message(1005, '保存第三方token失败！');
        }

        //新增写入第三方授权信息
        if (!empty($parr['type']) && !empty($parr['unionid'])) {

            //写入授权信息
            $authadd['c_type'] = $parr['type'];
            if ($parr['type'] == 1) {
                $authadd['c_unionid'] = $parr['unionid'];
            } else {
                $authadd['c_openid'] = $parr['unionid'];
            }

            $authadd['c_name'] = filterEmoji($parr['nickname']);
            $authadd['c_headimg'] = $parr['headimg'];
            $authadd['c_ucode'] = $ucode;
            $authadd['c_addtime'] = gdtime();
            $result = M('Users_auth')->add($authadd);
            if (!$result) {
                $db->rollback();
                return Message(1004,'授权失败');
            }

            //微信，支付宝被临时锁定同步小蜜锁定
            if (empty($incode)) {
                $bxparr['ucode'] = $ucode;
                $bxparr['openid'] = $parr['unionid'];
                $result = IGD('Impower','Login')->BindWxopendid($bxparr);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return Message(1004,'锁定关系失败');
                }
            }
        }

        $db->commit();

        $whereadd['openid'] = md5($whereadd['c_ucode']);
        IGD('Common', 'Redis')->RediesStoreSram($whereadd['openid'], $whereadd['c_ucode'],86400);
        return MessageInfo(0, '注册成功', $whereadd);
    }

    /**
     * 授权验证小蜜帐号
     * @param phone,incode,unionid,headimg,nickname,sex,type
     */
    function CheckXmphone($parr)
    {
        $phone = $parr['phone'];

        //第三方授权注册
        $authparr['type'] = $parr['type'];
        $authparr['openid'] = $parr['unionid'];
        $result = IGD('Impower','Login')->AuthSeacher($authparr);
        if ($result['code'] == 0) {
            $whereadd['openid'] = md5($result['data']['c_ucode']);
            IGD('Common', 'Redis')->RediesStoreSram($whereadd['openid'], $result['data']['c_ucode'],86400);
            return MessageInfo(0, '该账号已授权，可以进行授权登录', $whereadd);
        }

        $wherephone['c_phone'] = $phone;
        $userucode = M('Users')->where($wherephone)->getField('c_ucode');
        if ($userucode) {
            $parr['ucode'] = $userucode;
            return $this->AuthRegister($parr);
        }

        return Message(3000,'没有授权小蜜帐号');
    }

    /**
     * 第三方授权注册绑定
     * @param phone,pwd,incode,unionid,headimg,nickname,sex,ucode,type
     */
    function AuthRegister($parr)
    {
        $userucode = $parr['ucode'];

        $db = M('');
        $db->startTrans(); /* 开启事务 */

        //查询授权信息
        $auwhere['c_type'] = $parr['type'];
        $auwhere['c_ucode'] = $userucode;
        $authinfo = M('Users_auth')->where($auwhere)->field('c_id')->find();

        //写入授权信息
        $authadd['c_type'] = $parr['type'];
        if ($parr['type'] == 1) {
            $authadd['c_unionid'] = $parr['unionid'];
        } else {
            $authadd['c_openid'] = $parr['unionid'];
        }

        $authadd['c_name'] = filterEmoji($parr['nickname']);
        $authadd['c_headimg'] = $parr['headimg'];
        $authadd['c_ucode'] = $userucode;
        if ($authinfo) {
            $result = M('Users_auth')->where($auwhere)->save($authadd);
        } else {
            $authadd['c_addtime'] = gdtime();
            $result = M('Users_auth')->add($authadd);
        }
        if (!$result) {
            $db->rollback();
            return Message(1004,'授权失败');
        }

        //微信，支付宝被临时锁定同步小蜜锁定
        $bxparr['ucode'] = $userucode;
        $bxparr['openid'] = $parr['unionid'];
        $result = IGD('Impower','Login')->BindWxopendid($bxparr);
        if ($result['code'] != 0) {
            $db->rollback();
            return Message(1004,'锁定关系失败');
        }

        // 记录登录时间
        $userwhere['c_ucode'] = $userucode;
        $userdata['c_lasttime'] = gdtime();
        $result = M('Users')->where($userwhere)->save($userdata);
        if (!$result) {
            $db->rollback();
            return Message(1005,'记录失败');
        }

        $db->commit();
        $whereadd['openid'] = md5($userucode);
        IGD('Common', 'Redis')->RediesStoreSram($whereadd['openid'], $userucode,86400);
        return MessageInfo(0, '授权成功', $whereadd);
    }

    /**
     *  忘记密码
     *  @param telephone,pwd
     */
    function forgetpwd($parr) {
        $phone = $parr['telephone'];
        $pwd = encrypt($parr['pwd'],C('ENCRYPT_KEY'));
        $wherephone['c_phone'] = $phone;
        $userTelId = M('Users')->where($wherephone)->getField('c_id');
        if (!$userTelId) {
            return Message(1000, "手机号码不存在！");
        }

        $userdata['c_password'] = $pwd;
        $userdata['c_lasttime'] = gdtime();
        $data = M('Users')->where($wherephone)->save($userdata);
        if (!$data) {
            return Message(1001, "密码修改失败！");
        }

        return  MessageInfo(0, "密码修改成功！");
    }

    //退出登录
    function Loginout($parr) {
        $openid = $parr['openid'];
        $ucode = $parr['ucode'];

        if (!empty($ucode)) {   //用户编码不为空，清空极光token
            //清除极光token
            $w['c_ucode'] = $ucode;
            $save['c_jiguang_token'] = '';
            $result = M('user_part')->where($w)->save($save);
            // if ($result < 0) {
            //     return Message(1001, '清除极光token失败');
            // }
        }
         // 写入用户地理位置
        $localtion = GetAreafromIp();

        $savedata['c_port'] = 'web';
        if (isset($parr['app_client'])) {
           $savedata['c_port'] = $parr['app_client'];
        }
        $savedata['c_version'] = $parr['app_version'];
        $savedata['c_browser'] = GetBrowser();
        $savedata['c_ip'] = GetIP();
        $savedata['c_ucode'] = $ucode;
        $savedata['c_sign'] = 2;
        $savedata['c_longitude'] =  $localtion['longitude'];
        $savedata['c_latitude'] = $localtion['latitude'];
        $savedata['c_address'] = $localtion['address'];
        $savedata['c_addtime'] = gdtime();
        $result = M('Login_record')->add($savedata);

        // S($openid, null);
        IGD('Common', 'Redis')->RediesStoreSram($openid, null);
        IGD('Common', 'Redis')->RediesStoreSram('Menu0_'.$ucode,null);
        IGD('Common', 'Redis')->RediesStoreSram('Menu1_'.$ucode,null);
        IGD('Common', 'Redis')->RediesStoreSram('Menu2_'.$ucode,null);

        return Message(0, '退出成功');
    }

}
