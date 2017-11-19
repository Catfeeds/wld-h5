<?php
namespace Common\Behind;

class MessageBehind{
	/**
	* 根据RegistrationID进行目标推送
	* @param string $ucode 用户编码
	* @param string $title 通知标题
	* @param string $content 通知内容
	* @param string $tag 发送类型（附加字段）
	* @param string $tagvalue 发送类型附加值（附加字段）
	* @param string $txcode 消息唯一标识
	*/
	function send_msg($parr){
		$ucode = $parr['ucode'];
		$title = $parr['title'];
		$tag = $parr['tag'];
		$tagvalue = $parr['tagvalue'];
		$content = $parr['content'];
		$txcode = $parr['txcode'];

		$where['c_ucode'] = $ucode;
		$user_info = M('user_part')->where($where)->field('c_jiguang_token,c_model')->find();
		
		if(empty($user_info) || empty($user_info['c_jiguang_token'])){
			return Message(2003,"用户极光token不存在");
		}

		$model = intval($user_info['c_model']);
		$RegistrationID = $user_info['c_jiguang_token'];

		$appkey = C(APPKEYS);
		$masterSecret = C(MASTERSECRET);
		$JPush = new \Com\JPush\JPush($appkey,$masterSecret);
		
		$result = $JPush->push()
		    ->setPlatform($platform)
		    ->addRegistrationID($RegistrationID)
		    ->setMessage($content, $title, 'text' , array($tag=>$tagvalue))
		    ->send();

		$return_m = objarray_to_array($result);

		$where1['c_txcode'] = $txcode;
		if($return_m['code'] == 2001){
			$msg_log['c_state'] = 0;
			$result = M('users_msg')->where($where1)->save($msg_log);
			if($result || $result == 0){
				$msg = json_decode($return_m['msg'], true); 
				$error_code = $msg['error']['code'];
				$error_message = $this->get_error($error_code);
				return Message(2001,$error_message);
			}else{
				return Message(2002,"消息记录存储失败");
			}
		}

		$msg_log1['c_state'] = 1;
		$result = M('users_msg')->where($where1)->save($msg_log1);
		if($result || $result == 0){
			return Message(0,"发送成功");
		}else{
			return Message(2002,"消息记录存储失败");
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
}