<?php


class LoginAgent {

    /**
     *  根据用户ucode获取商户信息
     *  @param ucode
     *
     */
    function GetUserByCode($ucode) {
        $whereinfo['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($whereinfo)->find();
        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        if (count($userinfo) == 0) {
            return Message(1000, '查询失败');
        }
        return MessageInfo(0, '查询成功', $userinfo);
    }

    /**
     *  商户登录
     *  @param phone,pwd
     *
     */
    function login($phone, $pwd) {
        $userinfo['c_phone'] = $phone;
        $userinfo = M('Users')->where($userinfo)->find();
        if (count($userinfo) == 0) {
            return MessageInfo(1000, '账号或密码错误');
        }

        $where['c_ucode'] = $userinfo['c_ucode'];
        $admin_name = M('Check_shopinfo')->where($where)->getField('c_id');
        if (!$admin_name) {
            return MessageInfo(1000, '账号或密码错误');
        }

        if ($userinfo['c_password'] != $pwd) {
            return MessageInfo(1001, '账号或密码错误');
        }


        // 记录登录时间
        $userdata['c_lasttime'] = date('Y-m-d H:i:s');
        $result = M('Users')->where($where)->save($userdata);
        $userinfo['admin_name'] = $userinfo['c_nickname'];
        return MessageInfo(0, '登录成功', $userinfo);
    }

    /**
     * 发送验证码入口
     * @param 参数
     * telephone手机号，teluserid第三方库id，telaccount第三方库登陆账号
     * password第三方库登陆密码，content发送消息内容
     */
    function SendVerify($parr) {
        if (!checkMobile($parr['telephone'])) {
            return Message(1001, "手机号码格式有误！");
        }
        $telwhere['c_phone'] = $parr['telephone'];
        $type = $parr['type'];
        if ($type == 1) {/* 商户注册 */
            //$usertelId = M('Users')->where($telwhere)->getField('c_id');
            //if ($usertelId) {
            //return Message(1002, "该手机号已被其他用户绑定，请更换手机号！");
            //}
        } else {/* 忘记密码 */
            $userTelId = M('Users')->where($telwhere)->getField('c_id');
            if (!$userTelId) {
                return Message(1000, "手机号码不存在！");
            }
        }
        $info['userid'] = $parr['userid']; //改为自己的id
        $info['account'] = $parr['account'];
        $info['password'] = $parr['password'];
        $info['content'] = $parr['content'];
        $info['mobile'] = $parr['telephone'];
        $info['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值

        $result = $this->SendPhone($info);

        if ($result['code'] == 0) {
            return Message(0, "发送成功！");
        }

        return Message(1000, "发送失败！");
    }

    //发送接口
    function SendPhone($parr) {
        vendor('SMS.SubmitMsg');
        $Msg = new \SubmitMsg();
        $wx_return_data = $Msg->SendMsg($parr);


        return Message(0, "发送成功！");

        //将XML转为array
        //$array_data = json_decode(json_encode(simplexml_load_string($wx_return_data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        // if ($array_data['message'] == 'ok') {
        //   return Message(0, "发送成功！");
        //} else {
        //    return Message(1000, "发送失败！");
        //}
    }

    /**
     *  商户注册
     *  @param phone,pwd,incode,type
     *
     */
    function register($parr) {
        $incode = $parr['incode'];
        $phone = $parr['phone'];
        $pwd = $parr['pwd'];
        $type = $parr['type'];

        $wherephone['c_phone'] = $phone;
        $userTelId = M('Users')->where($wherephone)->find();
        if ($userTelId['c_isagent'] == 1) {
            return Message(1001, '您的区域代理帐号已激活');
        }
        if ($userTelId['c_isagent'] == 2) {
            return Message(1001, '您的代理帐号已激活');
        }
        if ($userTelId['c_shop'] == 1) {
            return Message(1001, '您的微商帐号已激活');
        }

        if ($userTelId) {
            $shopinfowhere['c_ucode'] = $userTelId['c_ucode'];
            $shopinfoid = M('Check_shopinfo')->where($shopinfowhere)->getField('c_id');
            if ($shopinfoid) {
                return Message(1001, '您的帐号已激活');
            }
        }

        if (empty($incode)) {
            return Message(1000, '请输入激活码');
        }
        $wherincode['c_code'] = $incode;
        if ($type == 3) {
            $incodedata = M('Check_codelist')->where($wherincode)->find();
        } else {
            $incodedata = M('Invite_code')->where($wherincode)->find();
        }
        if (!$incodedata) {
            return Message(1000, '激活码不存在');
        }


        if ($type == 1) { //区代
            if ($incodedata['c_rule'] != 1 || !empty($incodedata['c_ucode'])) {
                return Message(1000, '激活码不正确或已失效');
            }

            $ucode = $this->CreateUcode('xms'); /* 创建用户编码 */
            $newwheresave['c_nickname'] = '小蜜代理';   /* 默认昵称 */
            $whereadd['c_isagent'] = 1;
            $whereadd['c_acode'] = '';
            $shopinfo['c_istore'] = 2;
        } else if ($type == 2) {  //市代
            $fincodewhere['c_code'] = $incodedata['c_fcode'];
            $fucode = M('Invite_code')->where($fincodewhere)->getField('c_ucode');
            if ($incodedata['c_rule'] != 2 || !empty($incodedata['c_ucode']) || empty($fucode)) {
                return Message(1000, '激活码不正确或已失效');
            }
            $ucode = $this->CreateUcode('xms'); /* 创建用户编码 */
            $newwheresave['c_nickname'] = '小蜜代理';
            $whereadd['c_isagent'] = 2;
            $whereadd['c_acode'] = $fucode;
            $shopinfo['c_istore'] = 2;
        } else if ($type == 3) {   //微商
            if ($incodedata['c_state'] != 2 || !empty($incodedata['c_ucode'])) {
                return Message(1000, '激活码不正确或已失效');
            }
            $ucode = $this->CreateUcode('xmw'); /* 创建用户编码 */
            $newwheresave['c_nickname'] = '小蜜微商';
            // $whereadd['c_shop'] = 1;
            $whereadd['c_acode'] = $incodedata['c_acode'];
            // $whereadd['c_num'] = 200;
            // $invitationcode = $this->CreateFcode();
            // $whereadd['c_invitationcode'] = $invitationcode;

            $shopinfo['c_istore'] = 1;
        }

        if (!$userTelId) {
            $whereadd['c_ucode'] = $ucode;
            $whereadd['c_signature'] = '蜜主很懒，没有什么个性签名！';
            $whereadd['c_level'] = 1; /* 普通会员 */
            $whereadd['c_headimg'] = 'data/userheadimg/' . rand(11, 20) . '.jpg';
            $whereadd['c_phone'] = $phone;
        }

        $whereadd['c_password'] = $pwd;

        $whereadd['c_addtime'] = date('Y-m-d H:i:s', time());

        $User = M('Users');
        $User->startTrans(); /* 开启事务 */
        if ($userTelId) {
            $result = $User->where($wherephone)->save($whereadd);
        } else {
            $result = $User->add($whereadd);
        }

        if (!$result) {
            $User->rollback();
            return Message(1002, '注册失败！');
        }

        $newwheresave['c_nickname'] = $newwheresave['c_nickname'] . $result;
        $newwherinfo['c_id'] = $result;
        if (!$userTelId) {
            $result = M('Users')->where($newwherinfo)->save($newwheresave);
        }
        if (!$result) {
            $User->rollback();
            return Message(1002, '注册失败！');
        }

        //去除推荐关系
        if ($type != 3 && $userTelId) {
            $tuijianucode['c_ucode'] = $userTelId['c_ucode'];
            $result = M('Users_tuijian')->where($tuijianucode)->delete();
            // if (!$result) {
            //     $User->rollback();
            //     return Message(1002, '注册失败！');
            // }
        }



        //新增资料信息表
        $shopinfo['c_addtime'] = date('Y-m-d H:i:s');
        if ($userTelId) {
            $ucode = $userTelId['c_ucode'];
        }
        $shopinfo['c_ucode'] = $ucode;
        $shopinfo['c_name'] = $newwheresave['c_nickname'];
        $shopinfo['c_phone'] = $phone;
        $shopinfo['c_checked'] = 0;
        $result = M('Check_shopinfo')->add($shopinfo);
        if (!$result) {
            $User->rollback();
            return Message(1002, '注册失败！');
        }

        // 改变邀请码相关状态
        if ($type == 3) {
            $inparr['ucode'] = $ucode;
            $inparr['code'] = $incode;
            $result = D('Codecheck','Service')->RegiterCode($inparr);
            if ($result['code'] != 0) {
                $User->rollback();
                return Message(1002, '注册失败！');
            }
            // $wherincode['c_num'] = array('GT', 0);
            // $result = M('Invite_code')->where($wherincode)->setDec('c_num', 1);
            // if (!$result) {
            //     $User->rollback();
            //     return Message(1002, '注册失败！');
            // }
        } else {
            $incodesave['c_ucode'] = $ucode;
            $result = M('Invite_code')->where($wherincode)->save($incodesave);
            if (!$result) {
                $User->rollback();
                return Message(1002, '注册失败！');
            }
        }

        $User->commit();
        return MessageInfo(0, '注册成功', $whereadd);
    }

    /**
     *  生成用户编码
     *  @param
     */
    function CreateUcode($prefix = "wld") {
        //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 4);
        $uuid .= substr($str, 8, 2);
        $uuid .= substr($str, 12, 3);
        $uuid .= substr($str, 16, 2);
        $uuid .= substr($str, 20, 5);
        return $prefix . $uuid;
    }

    /**
     *  忘记密码
     *  @param $phone,$pwd
     */
    function forgetpwd($parr) {
        $phone = $parr['telephone'];
        $pwd = $parr['pwd'];
        $wherephone['c_phone'] = $phone;
        $userTelId = M('Users')->where($wherephone)->getField('c_id');
        if (!$userTelId) {
            return Message(1000, "手机号码不存在！");
        }
        $userpwd = M('Users');
        $userdata['c_password'] = $pwd;
        $data = $userpwd->where($wherephone)->save($userdata);
        return MessageInfo(0, "密码修改成功！");
    }

    /**
     * 修改密码
     * @param ucode,password,pwd
     */
    function Editpass($parr) {
        $whereinfo['c_ucode'] = $parr['ucode'];
        $temppwd = $parr['pwd'];

        $password = M('Users')->where($whereinfo)->getField('c_password');
        if ($temppwd != $password) {
            return Message(1021, "原密码错误");
        }

        $save['c_password'] = $parr['password'];
        $resut = M('Users')->where($whereinfo)->save($save);
        if (!$resut) {
            return Message(1021, "新密码不能与原密码一致");
        }
        return Message(0, "修改成功");
    }

}
