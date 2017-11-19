<?php

/**
 * 微信公众号
 * @author x
 */
class WeixingWeixin{
	/**
	 * 创建自定义菜单
	 * @param  array $button 符合规则的菜单数组，规则参见微信手册
	 */
	public function menuCreate($button){
		$appid = C('APPID');
		$appsecret = C('APPSECRET');
		$access_token = get_token();

		$weixin = new \Com\WechatAuth($appid,$appsecret,$access_token);

	    $formbul = str_replace('&quot;', '"', $button);
	    $button = unserialize($formbul);

	 	$result = $weixin->menuCreate($button);
		if ($result['errcode']=='0') {
			$resultdata = Message(0,'创建自定义菜单成功');
		} else {
			$resultdata = Message(1004,'创建自定义菜单失败');
		}

		return $resultdata;
	}

}