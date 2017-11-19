<?php

/**
 * 支付安全码
 *
 */
class SecurityUser {
    /**
     *  添加支付安全码
     *  @param ucode,safepwd,affirm_safepwd
     *
     */
    function SetSafepwd($parr) {
        $ucode = $parr['ucode'];
        $safepwd = $parr['safepwd'];
        $affirm_safepwd = $parr['affirm_safepwd'];

        if(empty($ucode) || empty($safepwd) || empty($affirm_safepwd)){
            return Message(1021, "数据错误！");
        }

        if($safepwd != $affirm_safepwd){
            return Message(1022, "两次输入安全码不一致！");
        }

        $w['c_ucode'] = $ucode;
        $save_data['c_safepwd'] = encrypt($safepwd, C('ENCRYPT_KEY'));
        $save_data['c_lasttime'] = date('Y-m-d H:i:s');

        $result = M('Users')->where($w)->save($save_data);

        if(!$result){
            return Message(1023, "安全码设置失败！");
        }
        $redisKey = "AQM-".$ucode;
        $redisTime = "AQMT-".$ucode;
        IGD('Common', 'Redis')->RediesStoreSram($redisKey,0,86400);
        IGD('Common', 'Redis')->RediesStoreSram($redisTime,"",86400);

        return Message(0, "安全码设置成功！");
    }


    /**
     * 检测是否设置安全码
     * @param ucode
    */
    public function checkNum($parr){
        $ucode = $parr['ucode'];
        if(empty($ucode)){
            return Message(1009, "参数缺失！");
        }
        $redisKey = "AQM-".$ucode;
        $redisTime = "AQMT-".$ucode;
        $w['c_ucode'] = $ucode;
        $info = M('Users')->where($w)->find();
        $key = IGD('Common','Redis')->Rediesgetucode($redisTime);
        $keyValue = IGD('Common','Redis')->Rediesgetucode($redisKey);
        if(!$key){  //30分钟已过
            if($keyValue>=5){   //小于5次错误不清除
                IGD('Common', 'Redis')->RediesStoreSram($redisKey,0,86400);
            }
            $state =2;
        }else{ // 未满30分钟
            $state =1;
        }
        if(empty($info['c_safepwd'])){
            return $this->MessageInfo(0, "暂时还没设置安全码",$state,1);
        }

        return $this->MessageInfo(0, "已设置安全码",$state,0);

    }

    /**
     * 验证安全码是否正确
     * @param  ucode，safepwd
    **/

    public function validateNum($parr){
        $ucode = $parr['ucode'];
        $safePwd = $parr['safepwd'];

        if(empty($ucode) || empty($safePwd)){
            return Message(1009, "参数缺失！");
        }

        $now =time();
        $tommorw = strtotime(date("Y-m-d",strtotime("+1 day")));
        $time =$tommorw-$now;

        $redisKey = "AQM-".$ucode;
        $redisTime = "AQMT-".$ucode;
        $key = IGD('Common','Redis')->Rediesgetucode($redisKey);
        $oldPwd = encrypt($safePwd, C('ENCRYPT_KEY'));
        $w['c_ucode'] = $ucode;
        $info = M('Users')->where($w)->find();
        if($oldPwd != $info['c_safepwd']){
            if(!$key){  //如果当前key值次数不存在  则存
                IGD('Common', 'Redis')->RediesStoreSram($redisKey,1,$time);
            }else{   //有则 +1
                IGD('Common', 'Redis')->RediesStoreSram($redisKey,$key+1,$time);
            }

            if($key==4){  // 当本次输入错误次数为第5次  则存一个redis值为 1800秒
                IGD('Common', 'Redis')->RediesStoreSram($redisTime,"aqm-t",1800);
            }
            return MessageInfo(0, "安全码验证失败！",$key+1);
        }else{
            //将安全码可输入次数清零 并且将 30分钟的key值清除
            IGD('Common', 'Redis')->RediesStoreSram($redisKey,0,$time);
            IGD('Common', 'Redis')->RediesStoreSram($redisTime,"",$time);
        }

        return MessageInfo(0, "恭喜您验证成功！",null);

    }


    function MessageInfo($code, $message, $data,$flag) {
        $msg = array();
        $msg["code"] = $code;
        $msg["msg"] = $message;
        $msg["data"] = $data;
        $msg["flag"] =$flag;
        return $msg;
    }


    //验证安全密码
    /*
     * 验证安全密码
     * ucode 用户编码
     * securitycode 用户输入的安全码
     *      */
    public function checksecurity($parr) {
        $ucode = $parr['ucode'];
        $securitycode = $parr['securitycode'];

        if (empty($ucode)) {
            return Message(1009, "登陆超时，请重新登陆");
        }

        if (empty($securitycode)) {
            return Message(1025, "请输入安全密码");
        }

        $whereinfo['c_ucode'] = $ucode;
        $safepwd = M('Users')->where($whereinfo)->getField('c_safepwd');
        $checkpwd = encrypt($securitycode, C('ENCRYPT_KEY'));
        if ($checkpwd != $safepwd) {
            return Message(1026, "安全密码验证失败");
        }

        return Message(0, "验证成功");
    }
}
