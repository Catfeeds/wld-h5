<?php

/**
 * 融云即时通信获取用户信息
 * 与存储用户信息token
 */
class UserProcessRongcloud {

    /**
     * 获取token,并将token储存到数据库
     * @param string $currentUserId 本人用户编号
     */
    function token($currentUserId) {
        $where['c_ucode'] = $currentUserId;
        $userinfo = M('users')->where($where)->field('c_ucode,c_nickname')->find();

        if ($userinfo) {
            $userinfo['c_headimg'] = GetHost() . '/Uploads/logo.jpg';

            $App_Key = C(RONGCLOUD_APP_KEY);
            $App_Secret = C(RONGCLOUD_APP_SECRET);
            $RongCloud = new \Com\RongCloud\ServerAPI($App_Key, $App_Secret);
            $token = $RongCloud->getToken($userinfo['c_ucode'], $userinfo['c_nickname'], $userinfo['c_headimg']);

            if (!$token) {
                return Message(202, "服务器API错误");
            }

            $tokeArray = json_decode($token, true);

            if ($tokeArray['code'] != 200) {
                $message = array(
                    'errorMessage' => $tokeArray['errorMessage'],
                    'code' => $tokeArray['code'],
                );
                return MessageInfo(400, "获取失败", $message);
            } else {
                $db = M('user_part');
                $is_exist = $db->where($where)->count();

                if ($is_exist == 0) {
                    $insert_date = array(
                        'c_ucode' => $currentUserId,
                        'c_rongyun_token' => $tokeArray['token'],
                        'c_addtime' => date('Y-m-d H:i:s', time()),
                    );

                    $result = $db->add($insert_date);
                } else {
                    $update_date['c_rongyun_token'] = $tokeArray['token'];
                    $result = $db->where($where)->save($update_date);
                }

                if ($result) {
                    return Message(0, "token获取成功");
                } else {
                    return Message(203, "token 存储失败");
                }
            }
        } else {
            return Message(201, "用户不存在");
        }
    }

    /**
     * 刷新用户信息 方法  说明：当您的用户昵称和头像变更时，您的 App Server 应该调用此接口刷新在融云侧保存的用户信息，以便融云发送推送消息的时候，能够正确显示用户信息
     * @param String $ucode 用户编码
     */
    public function userRefresh($ucode) {
        $userinfo = M('users')->field('c_ucode,c_nickname,c_headimg,c_phone')->find();

        if (empty($userinfo)) {
            return Message(1001, "用户不存在");
        }

        $ucode = $userinfo['c_ucode'];
        $nickname = $userinfo['c_nickname'];
        $headimg = $userinfo['c_headimg'];
        $mobile = $userinfo['c_phone'];

        if (empty($nickname)) {
            $nickname = substr_replace($mobile, '****', 5, 4);
        }

        if (empty($headimg)) {
            $headimg = GetHost() . '/Uploads/logo.jpg';
        } else {
            $headimg = GetHost() . $headimg;
        }

        $App_Key = C(RONGCLOUD_APP_KEY);
        $App_Secret = C(RONGCLOUD_APP_SECRET);
        $RongCloud = new \Com\RongCloud\ServerAPI($App_Key, $App_Secret);
        $result = $RongCloud->userRefresh($ucode, $nickname, $headimg);

        $resultArray = json_decode($result, true);

        if ($tokeArray['code'] != 200) {
            return Message(1002, "用户信息刷新失败");
        }

        return Message(0, "用户信息刷新成功");
    }

    //根据融云token获取用户信息
    public function GetUserInfo($parr) {
        $join = 'LEFT JOIN t_user_part as b on a.c_ucode=b.c_ucode ';
        $field = "a.c_ucode,a.c_nickname,a.c_headimg,a.c_signature,b.c_rongyun_token,a.c_shop";

        $where['a.c_ucode'] = $parr['ucode'];
        $userinfo = M('Users as a')->join($join)->where($where)->field($field)->find();

        if (count($userinfo) == 0) {
            return Message(1029, "该用户不存在");
        }

        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];

        $where1['c_user_ucode'] = $parr['iucode'];
        $where1['c_friend_ucode'] = $parr['ucode'];
        $fuserinfo = M('Friend_relation')->where($where1)->find();

        if (count($fuserinfo) == 0) {
            $userinfo['state'] = 0;
        } else {
            if ($fuserinfo['c_status'] == 1) {
                $userinfo['state'] = 1;
            } else {
                $userinfo['state'] = 0;
            }
        }

        if(!empty($fuserinfo['c_remark_name'])){
            $userinfo['c_nickname'] = $fuserinfo['c_remark_name'];
        }

        $userinfo['c_nickname'] = empty($fuserinfo['c_remark_name'])?$userinfo['c_nickname']:$fuserinfo['c_remark_name'];
        $userinfo['c_remark_name'] = $fuserinfo['c_remark_name'];
        $userinfo['c_remark_phone'] = $fuserinfo['c_remark_phone'];
        $userinfo['c_remark_desc'] = $fuserinfo['c_remark_desc'];
        return MessageInfo(0, "获取用户信息成功", $userinfo);
    }

}
