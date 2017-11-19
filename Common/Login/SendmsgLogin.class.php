<?php

/**
 *  短信发送
 */
class SendmsgLogin {

    /**
     * 发送验证码入口
     * @param 参数
     * telephone手机号，teluserid第三方库id，telaccount第三方库登陆账号
     * password第三方库登陆密码，content发送消息内容
     */
    function SendVerify($parr) {

        if (!checkMobile($parr['telephone'])) {
            $returnstr = Message(1001, "手机号码格式有误！");
            return $returnstr;
        }

        $telwhere['c_phone'] = $parr['telephone'];
        $type = $parr['type'];
        if ($type == 1) {/* 注册 */
            $usertelId = M('Users')->where($telwhere)->getField('c_id');
            if ($usertelId) {
                $returnstr = Message(1002, "该手机号已被注册，请更换手机号！");
                return $returnstr;
            }
        } else if ($type == 1000) {

        } else {/* 忘记密码 */
            $userTelId = M('Users')->where($telwhere)->getField('c_id');
            if (!$userTelId) {
                $returnstr = Message(1000, "手机号码不存在！");
                return $returnstr;
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
            $returnstr = Message(0, "发送成功！");
            return $returnstr;
        }

        $returnstr = Message(1000, "发送失败！");
        return $returnstr;
    }

    //发送接口
    function SendPhone($parr) {

        vendor('SMS.SubmitMsg');
        $Msg = new \SubmitMsg();
        $wx_return_data = $Msg->FutureSendMsg($parr);
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($wx_return_data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $returnstr = Message(0, "发送成功！");
        return $returnstr;
    }

}
