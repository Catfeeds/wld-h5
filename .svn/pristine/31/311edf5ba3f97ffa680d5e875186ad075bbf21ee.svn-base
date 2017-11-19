<?php

/**
 * 极光推送相关接口
 *
 */
class JPushJpush {

    /**
     * 更新用户极光注册设备ID 以及融云token
     * @param string $ucode 用户编码
     * @param string $platform 设备平台类型
     * @param string $RegistrationID 设备注册ID
     * @param string $systemVersion 系统版本ID
     */
    function save_info($ucode,$platform,$RegistrationID,$systemVersion=null){
        $db = M('user_part');
        $where['c_ucode'] = $ucode;

        //检测更新融云token
        $rongcloud_token = $db->where($where)->getField('c_rongyun_token');
        if(empty($rongcloud_token)){
            $RongCloud = IGD('UserProcess','RongCloud');
            $result = $RongCloud->token($ucode);
            if($result['code'] !== 0){
                return $result;
            }
            $rongcloud_token = $db->where($where)->getField('c_rongyun_token');
        }
        //更新用户极光注册设备ID
        if($platform == "IOS"){
            $save_data = array(
                'c_jiguang_token' => $RegistrationID,
                'c_model' => 2,
                'c_systemVersion'=>$systemVersion,
            );
            $result = $db->where($where)->save($save_data);
        }else{
            $save_data = array(
                'c_jiguang_token' => $RegistrationID,
                'c_model' => 1,
            );
            $result = $db->where($where)->save($save_data);
        }

        if($result == 0 || $result){
            return Message(0,"保存成功");
        }else{
            return Message(2002,"保存失败");
        }
    }

    /**
     * 极光推送自定义消息
     * @param string  c_ucode,c_title,c_txcode,c_tag,c_tagvalue,c_platform,c_content
     */
    function push_msg($parr){
        $ucode = $parr['c_ucode'];
        $title = $parr['c_title'];
        $txcode = $parr['c_txcode'];
        $tag = 'c_id';
        $tagvalue = $parr['c_tagvalue'];
        $platform = $parr['c_platform'];
        $content = $parr['c_content'];

        // $sendno = intval($parr['c_id']); //设置推送序号
        $timelive = 86400 * 3;//设置离线消息保留时长
        $production = (boolean)True;//True 表示推送生产环境，False 表示要推送开发环境； 如果不指定则为推送生产环境。

        $whereinfo['c_txcode'] = $txcode;
        $msg = M('users_msg')->where($whereinfo)->count();
        if($msg == 0){
            return Message(2000,"此消息不存在");
        }

        if(empty($content)){
            return Message(2001,"消息内容不能为空");
        }

        $appkey = C(APPKEYS);
        $masterSecret = C(MASTERSECRET);
        $JPush = new \Com\JPush\JPush($appkey,$masterSecret);

        if($ucode == ''){//发送公告
            if($platform == 1){
                $result = $JPush->push()
                    ->setPlatform('all')
                    ->addAllAudience()
                    ->setOptions(null,$timelive,null,$production)
                    ->setMessage($content, $title, 'text' , array($tag=>$tagvalue))
                    ->send();
            }else if($platform == 2){
                $result = $JPush->push()
                    ->setPlatform('ios')
                    ->addAllAudience()
                    ->setMessage($content, $title, 'text' , array($tag=>$tagvalue))
                    ->send();
            }else{
                $result = $JPush->push()
                    ->setPlatform('android')
                    ->addAllAudience()
                    ->setMessage($content, $title, 'text' , array($tag=>$tagvalue))
                    ->send();
            }
        }else{//根据用户RegistrationID 发送消息
            $where['c_ucode'] = $ucode;
            $user_info = M('user_part')->where($where)->field('c_jiguang_token,c_model')->find();

            if(empty($user_info) || empty($user_info['c_jiguang_token'])){
                return Message(2002,"用户极光token不存在");
            }

            $model = intval($user_info['c_model']);
            $RegistrationID = $user_info['c_jiguang_token'];

            if($model == 1){
                $platform = 'android';

            }else{
                $platform = 'ios';
            }

            $result = $JPush->push()
                ->setPlatform($platform)
                ->addRegistrationID($RegistrationID)
                ->setOptions(null,$timelive,null,$production)
                ->setMessage($content, $title, 'text' , array($tag=>$tagvalue))
                ->send();
        }

        $return_m = objarray_to_array($result);

        if($return_m['code'] == 2001){//发送失败
            $msg = json_decode($return_m['msg'], true);
            $error_code = $msg['error']['code'];
            $error_message = $this->get_error($error_code);
            $save_data['c_state'] = 2003;
            $save_data['c_reason'] = $error_message;
            M('users_msg')->where($whereinfo)->save($save_data);
            return Message(2003,$error_message);
        }

        $result = M('users_msg')->where($whereinfo)->setfield('c_state',1);
        if($result < 0){
            return Message(2004,"消息发送状态修改失败");
        }else{
            return Message(0,"发送成功");
        }
    }

    function curl($data)
    {
        $ch = curl_init("https://japi.weilingdi.com/xm-basic-app-consumer/xunfei/text2Video");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            var_dump($ch);
        } else {
            $response = json_decode($response, true);
            return $response;
        }
    }
    /**
     * 极光推送通知
     * @param string  c_ucode,c_title,c_txcode,c_tag,c_tagvalue,c_platform,c_content
     */
    function push_notification($parr){
        $ucode = $parr['c_ucode'];
        $title = $parr['c_title'];
        $txcode = $parr['c_txcode'];
        $tag = 'c_id';
        $tagvalue = $parr['c_tagvalue'];
        $platform = $parr['c_platform'];
        $content = $parr['c_content'];
        //是否为语音类消息
        $issound = $parr['c_issound'];


        if($issound == 1){
            //查询用户语音推送配置
            $w['c_ucode'] = $ucode;
            $ispush = M('User_part')->where($w)->find();

            if($ispush['c_ispush'] == 1){
                if($ispush['c_systemversion']){
                    $flag =explode(".",$ispush['c_systemversion'])[0];
                    if($flag==10){
                        $arr['text']=$parr['c_content'];
                        $data =$this->curl($arr);
                        if($data['code']=="ok"){
                            $url = $data['data']['mp3'];
                        }else{
                            $url ='';
                        }
                    }else{
                        $url = '';
                    }

                    $soundUrl = $url;
                    $soundurl = 'soundurl';
                    if($flag<10){
                        $sound = 'moneySound_def.caf';
                    }else{
                        $sound = 'moneySound.caf';
                    }
                }else{
                    $soundUrl = '';
                    $soundurl = 'soundurl';
                    $sound = 'moneySound.caf';
                }

                $category = 'money';
                $tag1 = 'issound';
                $tagvalue1 = 1;
            }else{
                $soundUrl = '';
                $soundurl = 'soundurl';
                $sound = 'default';
                $tag1 = 'issound';
                $tagvalue1 = 0;
            }
        }else{
            $soundUrl = '';
            $soundurl = 'soundurl';
            $sound = 'default';
            $tag1 = 'issound';
            $tagvalue1 = 0;
        }

        // $sendno = intval($parr['c_id']); //设置推送序号
        $timelive = 86400 * 3;//设置离线消息保留时长
        $production = (boolean)false;//True 表示推送生产环境，False 表示要推送开发环境； 如果不指定则为推送生产环境。

        $whereinfo['c_txcode'] = $txcode;
        $msg = M('users_msg')->where($whereinfo)->count();
        if($msg == 0){
            $save_data['c_state'] = 2000;
            $save_data['c_reason'] = '此消息不存在';
            M('users_msg')->where($whereinfo)->save($save_data);
            return Message(2000,"此消息不存在");
        }

        if(empty($content)){
            $save_data['c_state'] = 2001;
            $save_data['c_reason'] = '消息内容不能为空';
            M('users_msg')->where($whereinfo)->save($save_data);
            return Message(2001,"消息内容不能为空");
        }

        $appkey = C(APPKEYS);
        $masterSecret = C(MASTERSECRET);
        $JPush = new \Com\JPush\JPush($appkey,$masterSecret);

        if($ucode == ''){//发送公告
            if($platform == 1){
                $result = $JPush->push()
                    ->setPlatform('all')
                    ->addAllAudience()
                    ->setOptions(null,$timelive,null,$production)
                    ->addAndroidNotification($content, $title, 1,array($tag=>$tagvalue,$tag1=>$tagvalue1,$soundurl=>$soundUrl))
                    ->addIosNotification($content,$sound, null, true, '', array($tag=>$tagvalue,$soundurl=>$soundUrl))
                    ->send();
            }else if($platform == 2){
                $result = $JPush->push()
                    ->setPlatform('ios')
                    ->addAllAudience()
                    ->setOptions(null,$timelive,null,$production)
                    ->addIosNotification($content,$sound, null, true, '', array($tag=>$tagvalue,$soundurl=>$soundUrl))
                    ->send();
            }else{
                $result = $JPush->push()
                    ->setPlatform('android')
                    ->addAllAudience()
                    ->setOptions(null,$timelive,null,$production)
                    ->addAndroidNotification($content, $title, 1,array($tag=>$tagvalue,$tag1=>$tagvalue1,$soundurl=>$soundUrl))
                    ->send();
            }
        }else{//根据用户RegistrationID 发送消息
            $where['c_ucode'] = $ucode;
            $user_info = M('user_part')->where($where)->field('c_jiguang_token,c_model')->find();

            if(empty($user_info) || empty($user_info['c_jiguang_token'])){
                $save_data['c_state'] = 2002;
                $save_data['c_reason'] = '用户极光token不存在';
                M('users_msg')->where($whereinfo)->save($save_data);
                return Message(2002,"用户极光token不存在");
            }

            $model = intval($user_info['c_model']);
            $RegistrationID = $user_info['c_jiguang_token'];

            if($model == 1){
                $platform = 'android';
                $result = $JPush->push()
                    ->setPlatform($platform)
                    ->addRegistrationID($RegistrationID)
                    ->setOptions(null,$timelive,null,$production)
                    ->addAndroidNotification($content, $title, 1,array($tag=>$tagvalue,$tag1=>$tagvalue1,$soundurl=>$soundUrl))
                    ->send();
            }else{
                $platform = 'ios';
                $result = $JPush->push()
                    ->setPlatform($platform)
                    ->addRegistrationID($RegistrationID)
                    ->setOptions(null,$timelive,null,$production)
                    ->addIosNotification($content, $sound , null, true,$category, array($tag=>$tagvalue,$soundurl=>$soundUrl))
                    ->send();
            }
        }

        $return_m = objarray_to_array($result);

        if($return_m['code'] == 2001){//发送失败
            $msg = json_decode($return_m['msg'], true);
            $error_code = $msg['error']['code'];
            $error_message = $this->get_error($error_code);
            $save_data['c_state'] = 2003;
            $save_data['c_reason'] = $error_message;
            M('users_msg')->where($whereinfo)->save($save_data);
            return Message(2003,$error_message);
        }

        $result = M('users_msg')->where($whereinfo)->setfield('c_state',1);
        if($result < 0){
            return Message(2004,"消息发送状态修改失败");
        }else{
            return Message(0,"发送成功");
        }
    }

    /**
     * 获取推送返回错误
     */
    function get_error($error_code){
        $res_arr = '';
        switch (intval($error_code)) {
            case 1000:
                $res_arr = '系统内部错误';
                break;
            case 1001:
                $res_arr = '只支持 HTTP Post 方法，不支持 Get 方法';
                break;
            case 1002:
                $res_arr = '缺少了必须的参数';
                break;
            case 1003:
                $res_arr = '参数值不合法';
                break;
            case 1004:
                $res_arr = '验证失败';
                break;
            case 1005:
                $res_arr = '消息体太大';
                break;
            case 1007:
                $res_arr = 'receiver_value 参数 非法';
                break;
            case 1008:
                $res_arr = 'appkey参数非法';
                break;
            case 1010:
                $res_arr = 'msg_content 不合法';
                break;
            case 1011:
                $res_arr = '没有满足条件的推送目标';
                break;
            case 1012:
                $res_arr = 'iOS 不支持推送自定义消息。只有 Android 支持推送自定义消息';
                break;
            default:
                $res_arr = '未知错误';
                break;
        }
        return $res_arr;
    }

    //根据消息c_id查询消息 id消息id ucode可选
    public function Getmsg($parr){
        $id = $parr['id'];

        $w['c_id'] = $id;
        $msg_info = M('users_msg')->where($w)->find();

        if(empty($msg_info)){
            return Message(2001,"消息不存在");
        }

        $data['c_txcode'] = $msg_info['c_txcode'];
        $data['c_ucode'] = $msg_info['c_ucode'];

        $result = M('users_msglog')->add($data);
        if($result < 0){
            return Message(2002,"消息记录写入失败");
        }

        return MessageInfo(0,"查询成功",$msg_info);
    }
}