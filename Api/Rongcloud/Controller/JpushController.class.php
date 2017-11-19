<?php
namespace Rongcloud\Controller;
use Base\Controller\BaseController;
/**
 * 极光推送
 */
class JpushController extends BaseController {
	/**
	* 更新用户极光注册设备ID 以及融云token
	* @param string $platform 设备平台类型 IOS/Android
	* @param string $RegistrationID 设备注册ID
	*/
	public function get_info(){
		$key = I('openid');
        $this->ucode = IGD('Common','Redis')->Rediesgetucode($key);
        if (!empty($this->ucode)) {
        	$platform1 = $_GET['platform'];
        	$platform2 = $_POST['platform'];
        	$platform = empty($platform1)?$platform2:$platform1;

        	$RegistrationID1 = $_GET['RegistrationID'];
        	$RegistrationID2 = $_POST['RegistrationID'];
        	$RegistrationID = empty($RegistrationID1)?$RegistrationID2:$RegistrationID1;

        	$systemVersion1 = $_GET['systemVersion'];
        	$systemVersion2 = $_POST['systemVersion'];
        	$systemVersion = empty($systemVersion1)?$systemVersion2:$systemVersion1;
        	
   //      	$platform = I('platform');
			// $RegistrationID = I('RegistrationID');
   //          $systemVersion =I('systemVersion');
			$JPush = IGD('JPush','Jpush');
			$result = $JPush->save_info($this->ucode,$platform,$RegistrationID,$systemVersion);
			$this->ajaxReturn($result);
        } else {
        	$this->ajaxReturn(Message(1009, '验证失效，请重新登录'));
        }
	}

	 //根据消息c_id查询消息
    public function Getmsg() {
        $id = I('c_id');

        $panrn['id'] = $id;
        $Message = IGD('JPush','Jpush')->Getmsg($panrn);

        $this->ajaxReturn($Message);
    }
}