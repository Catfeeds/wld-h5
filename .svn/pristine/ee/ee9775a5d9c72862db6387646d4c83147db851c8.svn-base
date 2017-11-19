<?php

namespace Home\Controller;

use Base\Controller\CheckController;

/**
 * 用户基本信息操作模块
 */
class IndexController extends CheckController {



    /**
     * 添加密友 按条件查找商户
     * @param  pageindex,name,longitude,latitude
     */
    public function SeachShopusers(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $parr['name'] = I('name');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('FriendProcess','Rongcloud')->SeachShopusers($parr);
        $this->ajaxReturn($result);
    }

    //修改用户信息
    public function EditUserInfo() {
        $ucode = $this->ucode;

        $parr['ucode'] = $ucode;
        $parr['sex'] = I('sex');
        $parr['signature'] = I('signature');
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['region'] = I('region');
        $parr['trade'] = I('trade');
        $parr['tag'] = I('tag');
        $parr['nickname'] = I('nickname');
        $UserCenter = IGD('User', 'User');

        $result1 = $UserCenter->EditAllOther($parr);
        $this->ajaxReturn($result1, 'JSON');
    }

    //验证用户昵称
    public function CheckUsernickname() {
        $ucode = $this->ucode;

        $parr['nickname'] = I('nickname');

        $UserCenter = IGD('User', 'User');
        $result = $UserCenter->Checknickname($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //用户头像上传
    public function Uploadhead() {
        $ucode = $this->ucode;
        $upload_path = 'header';

        if ($_FILES) {
            $result = uploadimg($upload_path);
            if ($result['code'] == 0) {
               // $file_path1 = array_values($result['data']);

                $file_path1 =explode("|",$result['data']);

                $file_path = $file_path1[0];
                $UserCenter = IGD('User', 'User');
                $data = $UserCenter->upload_header($ucode, $file_path);
                $returnstr = $data;
                $this->ajaxReturn($returnstr, 'JSON');
            } else {
                $returnstr = $result;
                $this->ajaxReturn($returnstr, 'JSON');
            }
        } else {
            $this->ajaxReturn(Message(0,'尚未修改'));
        }
    }

    //修改密码
    public function Editpass() {
        $ucode = $this->ucode;

        $pwd = I('pwd');
        $oldpwd = I('oldpwd');
        $parr['ucode'] = $ucode;
        $parr['password'] = encrypt($pwd, C('ENCRYPT_KEY'));
        $parr['pwd'] = encrypt($oldpwd, C('ENCRYPT_KEY'));

        $UserCenter = IGD('User', 'User');
        $result = $UserCenter->Editpass($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //编辑手机号码
    public function EditPhone() {
        $ucode = $this->ucode;

        $parr['ucode'] = $ucode;
        $parr['phone'] = I('phone');

        $UserCenter = IGD('User', 'User');
        $result = $UserCenter->EditPhone($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //用户标签、地区、行业、个性签名编辑
    public function other_Info() {
        $ucode = $this->ucode;

        $c_signature = trim(I('c_signature'));
        if (!empty($c_signature)) {
            $parr['c_signature'] = $c_signature;
        }

        $c_province = trim(I('c_province'));
        if (!empty($c_province)) {
            $parr['c_province'] = $c_province;
        }

        $c_city = trim(I('c_city'));
        if (!empty($c_city)) {
            $parr['c_city'] = $c_city;
        }

        $c_region = trim(I('c_region'));
        if (!empty($c_region)) {
            $parr['c_region'] = $c_region;
        }

        $c_tab = trim(I('c_tab'));
        if (!empty($c_tab)) {
            $parr['c_tab'] = $c_tab;
        }

        $c_trade = trim(I('c_trade'));
        if (!empty($c_trade)) {
            $parr['c_trade'] = $c_trade;
        }

        $UserCenter = IGD('User', 'User');
        $data = $UserCenter->save_other_Info($ucode, $parr);

        $returnstr = $data;
        $this->ajaxReturn($returnstr, 'JSON');
    }

    // 用户余额查询
    public function balance() {
        $ucode = $this->ucode;
        $User = IGD('Balance', 'User');
        $data = $User->GetBalance($ucode);
        if (empty($data)) {
            $returnstr = Message(402, "查询失败");
            $this->ajaxReturn($returnstr, 'JSON');
        } else {
            $returnstr = MessageInfo(200, "查询成功", $data);
            $this->ajaxReturn($returnstr, 'JSON');
        }
    }

    //获取用户地址列表
    public function GetUseraddress() {
        $ucode = $this->ucode;

        $User = IGD('User', 'User');
        $parr['ucode'] = $ucode;
        $result = $User->GetUserAddress($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //添加用户地址信息
    public function Address() {
        $ucode = $this->ucode;

        $User = IGD('User', 'User');
        $parr['ucode'] = $ucode;
        $parr['isdefault'] = I('isdefault');
        $parr['consignee'] = I('consignee');
        $parr['mobile'] = I('mobile');
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['district'] = I('district');
        $parr['address'] = I('address');
        $parr['provincename'] = I('provincename');
        $parr['cityname'] = I('cityname');
        $parr['districtname'] = I('districtname');
        $result = $User->UserAddress($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //编辑用户地址
    public function EditUserAddress() {
        $ucode = $this->ucode;

        $User = IGD('User', 'User');
        $parr['ucode'] = $ucode;
        $parr['id'] = I('id');
        $parr['isdefault'] = I('isdefault');
        $parr['consignee'] = I('consignee');
        $parr['mobile'] = I('mobile');
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['district'] = I('district');
        $parr['address'] = I('address');
        $parr['provincename'] = I('provincename');
        $parr['cityname'] = I('cityname');
        $parr['districtname'] = I('districtname');
        $result = $User->EditUserAddress($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //设置默认地址
    public function SetAddress() {
        $ucode = $this->ucode;

        $User = IGD('User', 'User');
        $parr['ucode'] = $ucode;
        $parr['id'] = I('id');
        $result = $User->SetAddress($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //删除用户地址
    public function deleteUserAddress() {
        $ucode = $this->ucode;

        $parr['id'] = I('id');
        $User = IGD('User', 'User');
        $result = $User->deleteUserAddress($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //获取单个用户地址
    public function FindAddress() {
        $ucode = $this->ucode;

        $User = IGD('User', 'User');
        $parr['id'] = I('id');
        $result = $User->FindAddress($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    public function GetDefaultaddress() {
        $ucode = $this->ucode;

        $User = IGD('User', 'User');
        $parr['ucode'] = $ucode;
        $result = $User->Getdefaultaddress($parr);

        $userinfo['address'] = $result['data'];

        $result2 = $User->GetUserMoney($parr);

        $usermoney = $result2['data'];
        $userinfo['money'] = $usermoney['c_money'];

        $result1 = MessageInfo(0, "查询成功", $userinfo);
        $this->ajaxReturn($result1, 'JSON');
    }

    //发送手机号码
    public function GetSendPhone() {

        $ucode = $this->ucode;

        $type = I('type');

        // 生成6位数验证码

        $UserCenter = IGD('Login', 'Login');

        if ($type == 1) {
            $userinfo = $UserCenter->GetUserByCode($ucode);
            $phone = $userinfo['data']['c_phone'];
        } else {
            $phone = I('phone');
        }

        if (empty($phone)) {
            $result = Message(1025, "请传入手机号码");
            $this->ajaxReturn($result);
        }

        $regnum = rand(100000, 999999);

        $info['userid'] = C('TEl_USER'); //改为自己的id
        $info['account'] = C('TEl_ACCESS');
        $info['password'] = C('TEl_PASSWORD');
        $info['content'] = "【微领地小蜜】尊敬的会员您好，校验码为：" . $regnum . "有效期120s，为保证您的账号安全，请勿外泄。感谢您的申请！";
        $info['mobile'] = $phone;
        $info['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值

        $returndata = IGD('Sendmsg', 'Login')->SendPhone($info);
        if ($returndata['code'] != 0) {
            $result = Message(1026, "短信发送失败");
            $this->ajaxReturn($result);
        }

        $data['number'] = $regnum;
        $result = MessageInfo(0, "发送成功", $data);
        $this->ajaxReturn($result);
    }

    /**
	* 保存用户地理位置
	* @param double $longitude 用户经度
	* @param double $latitude 用户纬度
	*/
	public function save_local(){
		$ucode = $this->ucode;

		$parr['ucode'] = $ucode;
		$parr['longitude'] =  I('longitude');
		$parr['latitude'] = I('latitude');
		$parr['address'] = I('address');
        $result = IGD('User', 'User')->save_local($parr);
    	$this->ajaxReturn($result);
	}

    //设置支付安全码
    public function SetSafepwd() {
        $ucode = $this->ucode;

        $safepwd = I('safepwd');
        $affirm_safepwd = I('affirm_safepwd');

        $parr['ucode'] = $ucode;
        $parr['safepwd'] = $safepwd;
        $parr['affirm_safepwd'] = $affirm_safepwd;

        $result = IGD('Security', 'User')->SetSafepwd($parr);

        $this->ajaxReturn($result, 'JSON');
    }

    //商家会员
    public function myupuser(){
        $ucode = $this->ucode;

        $parr['ucode'] = $ucode;

        $result = IGD('User','User')->Getmysup($parr);
        $this ->ajaxReturn($result);
    }

    //检查是否有安全码

    public function checkNo(){
        $ucode =$this->ucode;
        $parr['ucode'] =$ucode;

        $result =IGD('Security','User')->checkNum($parr);
        $this->ajaxReturn($result);
    }

    //验证安全码
    public function validateNo(){
        $ucode =$this->ucode;
        $parr['ucode'] =$ucode;
        $parr['safepwd'] =I('safepwd');

        $result =IGD('Security','User')->validateNum($parr);
        $this->ajaxReturn($result);
    }

}