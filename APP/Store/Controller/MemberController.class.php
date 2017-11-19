<?php

namespace Store\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 会员管理
 */
class MemberController extends BaseController {
	/*会员管理首页*/
    public function index(){
        $ucode = session('USER.ucode');
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->data = $result['data'];
        $this->checkedn = 200 - $this->data['c_num'];
        $incode = encrypt($this->data['c_invitationcode'],C('ENCRYPT_KEY'));
        $this->cururl = GetHost(1)."/index.php/Home/Impower/invite?incode=".$incode;
        if (!$this->apptype) {
            $this->apptype = get_device_type();
        }

        $this->count = IGD('User','User')->GetmyMembleCount($ucode);

        $parr['ucode'] = $ucode;
        $this->wxcount = IGD('User','User')->GetWxmebleCount($parr);
        $this->show();
    }    
    /**
     *会员管理，获取我的会员列表
     */
    public function GetmyMembleList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('User','User')->GetmyMembleList($parr);
        $this->ajaxReturn($result);
    }
    /**
     * 查询微信绑定的临时会员
     * @param ucode,pageindex,pagesize
     */
    public function GetWxmebleList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('User','User')->GetWxmebleList($parr);
        $this->ajaxReturn($result);
    }
    
    /**
     *查询我的推荐人
     */
    public function sjmember()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('User','User')->Getmysup($parr);
        $result['data']['c_time'] = date('Y/m/d',strtotime($result['data']['c_time']));
        $this->mysup = $result['data'];
        $this->show();
    }    
}