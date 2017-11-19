<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 *  个人资料中心
 */
class UsersController extends BaseController {

    //个人资料首页
    public function index(){
        $this->data = $this->GetUserByCode();
        $mytab = $this->data['c_tab'];
        if ($mytab != null) {
            $this->mylabel = explode('|', $mytab);
        }
        $this->display('myinfo');
    }

    //获取用户地址列表
    public function GetUserAddress()
    {
    	$parr['ucode'] = session('USER.ucode');
    	$mycadress = IGD('User','User');
    	$result = $mycart->GetUserAddress($parr);
    	$this->ajaxReturn($result['data']);
    }

    //获取省市区
    public function GetAddress()
    {
    	$parr['parentid'] = I('parentid');
    	$parr['regiontype'] = I('regiontype');
    	$myregion = IGD('User','User');
    	$result = $myregion->GetAddress($parr);
    	$this->ajaxReturn($result['data']);
    }

    //个人资料
	public function myinfo()
    {
        /*获取个人信息*/
        $this->data = $this->GetUserByCode();
        $mytab = $this->data['c_tab'];
        if ($mytab != null) {
            $this->mylabel = explode('|', $mytab);
        }
        $this->show();
    }

    //编辑个人资料
    public function editorinfo()
    {
        /*获取个人信息*/
        $this->data = $this->GetUserByCode();

        /*获取所有行业*/
        $trade = IGD('Common', 'Info');
        $tradedata = $trade->GetIndustry();
        $this->myjob=$tradedata['data'];

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province=$province_list;

        if (IS_POST) {
            $imgresult = uploadimg('headimg');
            // if ($imgresult['code'] != 0) {
            //     $this->error($imgresult['msg']);
            // }
            $parr1['headimg'] = implode('|',$imgresult['data']);
            $parr1['ucode'] = I('ucode');
            $parr1['sex'] = I('sex');
            $parr1['trade'] = I('trade');
            $parr1['province'] = I('province');
            $parr1['city'] = I('city');
            $parr1['region'] = I('region');
            $parr1['signature'] = I('signature');
            $result = IGD('User','User')->EditOther($parr1);
            if ($result['code'] != 0) {
                $this->error($result['msg']);
            }
            //session('time', null);
            $this->success($result['msg'],'myinfo');die;
        }

        // $time = time();
        // session('time', $time);    // 防止重复提交
        // $this->assign('time', $time);
        $this->show();
    }

    // 选择标签
    public function labelinfo()
    {
        /*获取个人信息*/
        $this->data = $this->GetUserByCode();
        $mytab = $this->data['c_tab'];
        if ($mytab) {
           $this->mylabel = explode('|', $mytab);
        } else {
           $this->mylabel = array();
        }
        if (IS_POST) {
            $ucode = session('USER.ucode');
            $tab = I('label');
            $parr1['ucode'] = $ucode;
            $parr1['tag'] = $tab;
            $result = IGD('User','User')->EditTag($parr1);
            if ($result['code'] != 0) {
                $this->error($result['msg']);
            }
            $this->success($result['msg'],'myinfo');die;
        }
        $this->show();
    }

    //收货地址
    public function myaddress()
    {
        $ucode = session('USER.ucode');
        // 获取地址列表
        $parr['ucode'] = $ucode;
        $addressinfo = IGD('User', 'User');
        $addressdata = $addressinfo->GetUserAddress($parr);
        $this->addresslist = $addressdata['data'];

        /*获取默认地址*/
        $defaultaddr = IGD('User', 'User');
        $defaults = $defaultaddr->Getdefaultaddress($parr);
        $this->defaultdz = $defaults['data'];

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province=$province_list;

        $this->show();
    }

    //修改昵称
    public function nickname()
    {
        /*获取个人信息*/
        $this->data = $this->GetUserByCode();

        if (IS_POST) {
            $parr['ucode'] = session('USER.ucode');
            $parr['nickname'] = strtrim1(I('c_nickname'));
            $result = IGD('User','User')->Editnickname($parr);
            if ($result['code'] != 0) {
                $this->error($result['msg']);
            }
            $this->success($result['msg'],'myinfo');die;
        }
        $this->show();
    }

    //检测昵称是否存在
    public function checknick()
    {
        $parr['nickname'] = I('nickname');
        $nickname = IGD('User','User')->Checknickname($parr);
        $this->ajaxReturn($nickname);
    }

    //账户与安全
    public function safeinfo()
    {
        /*获取个人信息*/
        $this->data = $this->GetUserByCode();
        $this->mobile = $this->data['c_phone'];

        $this->show();
    }

    // 修改密码
    public function updatepwd()
    {
        if(IS_AJAX)
        {
            $ucode = session('USER.ucode');
            $oldpwd = I('oldpwd');
            $pwd = I('newpwd');
            $confirmpwd = I('confirmpwd');

            if (!checkPwd($pwd) || empty($pwd)) {
                $this->ajaxReturn(Message(1003, "密码过于简单，请设置6到16位字符！"));
            }
            if ($pwd!=$confirmpwd) {
                $this->ajaxReturn(Message(1004, "两次密码输入不一致"));
            }
            $parr['ucode'] = $ucode;
            $parr['pwd'] = encrypt($oldpwd,C('ENCRYPT_KEY'));
            $parr['password'] = encrypt($pwd,C('ENCRYPT_KEY'));
            $newpwd = IGD('User', 'User')->Editpass($parr);
            if($newpwd['code'] == 0) {
                $this->ajaxReturn(Message(0, "密码修改成功！"));
            } else {
                $this->ajaxReturn($newpwd);
            }
        }
        $this->show();
    }

    /*验证原手机号码的校验码*/
    public function checkverify()
    {
        $phone = I('phone');
        $verifyid = IGD('Common', 'Redis')->Rediesgetucode($phone);
        $regverify = I('verify');
        if (!checkMobile($phone)) {
            $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        }
        else if ($verifyid == '') {
            $this->ajaxReturn(Message(1000, "校验码失效，请重新获取验证码！"));
        }
        else if ($regverify != $verifyid) {
            $this->ajaxReturn(Message(1001, "校验码错误！"));
        }
        $this->ajaxReturn(Message(0, "校验码输入正确！"));

    }

    //绑定手机号码
    public function bindtel()
    {
        /*获取个人信息*/
        $this->data = $this->GetUserByCode();
        $this->mobile = $this->data['c_phone'];

        if (IS_AJAX) {
            $phone = I('phone');
            if (!checkMobile($phone)) {
                $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
            }
            $verifyid = IGD('Common', 'Redis')->Rediesgetucode($phone);
            if ($verifyid == '') {
                $this->ajaxReturn(Message(1000, "校验码失效，请重新获取验证码！"));
            }
            $regverify = I('verify');
            if ($regverify != $verifyid) {
                $this->ajaxReturn(Message(1001, "校验码错误！"));
            }
            $parr['ucode'] = session('USER.ucode');
            $parr['phone'] = $phone;
            $bindtel = IGD('User','User')->EditPhone($parr);
            if ($bindtel['code'] == 0) {
                $returnstr = Message(0, "手机号码绑定成功！");
                $this->ajaxReturn($returnstr, 'JSON');
            }else {
                $this->ajaxReturn($bindtel);
            }
        }

        $this->show();
    }

    //获取个人信息
    private function GetUserByCode()
    {
        $ucode = session('USER.ucode');
        $userinfo = IGD('Login','Login')->GetUserByCode($ucode);
        $userdata = $userinfo['data'];
        return $userdata;
    }

    //获取标签
    public function getLabel()
    {
        $parr['pageIndex'] = I('page');
        $parr['pagesize'] = 40;
        $result = IGD('Common','Info')->GetLablist($parr);
        $this->ajaxReturn($result);
    }

    //查询我的推荐人
    public function sjmember()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('User','User')->Getmysup($parr);
        $result['data']['c_time'] = date('Y/m/d',strtotime($result['data']['c_time']));
        $this->mysup = $result['data'];
        $this->show();
    }

    //商家二维码页面
    public function qrcodeimg()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['qrcode_type'] = 1;
        $result = IGD('Qrcode', 'Store')->PuzzleQrcode($parr);
        if ($result['code'] != 0) {
            $this->error('您还不是商家，暂不能生成二维码',U('Index/index'));die;
        }
        $this->imgpath = $result['data']['img'];
        $this->show();
    }

    // 退出当前帐号
    public function loginout()
    {
        session('USER.ucode',null);
        $jumpurl = GetHost(1) . '/index.php/Login/Index/index';
        header("Location:" . $jumpurl);die();
    }


    /*设置提现安全密码*/
    public function setsafepwd()
    {
        $action = I('action');
        if($action == "add"){
            $this->pagetit = "设置安全密码";
        }else if($action == "modify"){
            $this->pagetit = "修改安全密码";
        }else{
            $this->pagetit = "确认安全密码";
        }
        $url = I('url');
        if (empty($url)) {
           $this->url = GetHost(1).'/index.php/Home/Users/safeinfo';
        } else {
            $this->url = decodeurl($url);
        }
        $this->show();
    }

    //设置提现安全密码
    public function Setpwd()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['safepwd'] = I('safepwd');
        $parr['affirm_safepwd'] = I('affirm_safepwd');
        $result = IGD('Security','User')->SetSafepwd($parr);
        $this->ajaxReturn($result);
    }

    //设置安全密码手机短信验证

    public function safepwdtel()
    {
        $this->action = I('action');
        if($this->action == "add"){
            $this->pagetit = "设置提现安全密码";
        }else{
            $this->pagetit = "修改提现安全密码";
        }

        /*获取个人信息*/
        $this->data = $this->GetUserByCode();
        $this->mobile = $this->data['c_phone'];

        $this->action = I('action');

        /*提现页面链接*/
        $this->url = I('url');

        $this->show();
    }

}