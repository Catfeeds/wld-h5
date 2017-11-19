<?php
namespace Home\Controller;
use Think\Controller;
/**
*   本地测试功能
*/
class TestController extends BaseController {
	//企业打款
	public function applyFor(){
		$trade_no = 'tk201609201737';
		$openid = 'oARqGxEP20y0oC-U3tw-SHkS7x8I';
		$amount = '1';
		$username = '谢秋林';

		$result = IGD('WxEnterprisepay','Weixin')->Pay($trade_no,$openid,$amount,$username);

		if($result['code']==0){
			echo($result['msg']);
		}else{
			echo($result['msg']);
		}
	}
}