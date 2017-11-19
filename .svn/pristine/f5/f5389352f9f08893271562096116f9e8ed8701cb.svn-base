<?php

// 微资源信息


class WxapiWeixin {

    /**
     * 查询关键字对应信息
     * @param key,sign
     */
    function GetRuleMsg($parr)
    {
    	$where['c_sign'] = $parr['sign'];
    	if ($parr['sign'] == 1) {
    		$where['c_key'] = $parr['key'];
    	}
    	$data = M('Wxmessage')->where($where)->order('c_id desc')->find();
    	if (!$data) {
    		return Message(2000,'数据为空');
    	}
    	$data['c_picurl'] = GetHost().'/'.$data['c_picurl'];
    	return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 查询用户未读信息
     * @param ucode
     */
    function GetNoreadMsg($parr)
    {
    	$ucode = $parr['ucode'];
        $sql = "select count(c_txcode) as num from t_users_msg where c_txcode not in ";
        $sql .= "(select c_txcode from t_users_msglog where c_ucode='$ucode') ";
        $sql .= "and (c_ucode='' or c_ucode is null or c_ucode='$ucode') limit 1";
        $data = M('')->query($sql);
    }

    /**
     * 查询微信发送失败的消息
     * @param openid
     */
    function GetWxmsgError($parr)
    {
    	$where['c_state'] = 2;
        $where['c_openid'] = $parr['openid'];
    	$data = M('Wxmessage_log')->where($where)->select();
        if (!$data) {
            return Message(2000,'数据为空');
        }
    	foreach ($data as $key => $value) {
    		if (!empty($value['c_msgcode'])) {
    			$messagewhere['c_msgcode'] = $value['c_msgcode'];
    			$message = M('Wxmessage')->where($messagewhere)->find();
    			$data[$key]['c_text'] = $message['c_text'];
    			$data[$key]['c_type'] = $message['c_type'];
                $data[$key]['c_desc'] = $message['c_desc'];
                $data[$key]['c_url'] = $message['c_url'];
    			$data[$key]['c_picurl'] = GetHost().'/'.$message['c_picurl'];
    		} else {
                $data[$key]['c_text'] = $value['c_msg'];
                $data[$key]['c_type'] = $value['c_msgtype'];
            }
    	}

    	return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 报错与成功统计
     * @param mid,errcode,errmsg,state(1成功,2失败)
     */
    function Receivelog($parr) {
        $where['c_id'] = $parr['mid'];
        $saveinfo['c_updatetime'] = date('Y-m-d H:i:s');
        $saveinfo['c_state'] = $parr['state'];
        $saveinfo['c_reason'] = $parr['errcode'].'：'.$parr['errmsg'];
        $result = M('Wxmessage_log')->where($where)->save($saveinfo);
        if (!$result) {
            return Message(2000,'记录失败');
        }

        return Message(0,'记录成功');
    }

    /**
     * 添加发送消息记录
     * @param openid(ucode),msgcode(msg,msgtype)
     */
    function AddSendMsglog($parr)
    {
    	if (!empty($parr['msgcode'])) {
    		$data['c_msgcode'] = $parr['msgcode'];
    	} else {
    		$data['c_msg'] = $parr['msg'];
    		$data['c_msgtype'] = $parr['msgtype'];
    	}
    	$data['c_openid'] = $parr['openid'];
    	$data['c_ucode'] = $parr['ucode'];
    	$data['c_addtime'] = date('Y-m-d H:i:s');
    	$result = M('Wxmessage_log')->add($data);
    	if (!$result) {
    		return Message(2000,'记录失败');
    	}

    	$data['c_id'] = $result;
    	return MessageInfo(0,'记录成功',$data);
    }

}





?>