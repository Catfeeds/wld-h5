<?php
namespace Rongcloud\Controller;
use Base\Controller\CheckController;
/**
 * 融云即时通信
 */
class RongcloudController extends CheckController {
    /**
    * 查询标签列表
    * @param  openid,pageindex
    */
    public function GetLablist() {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 1000;

        $result = IGD('Common','Info')->GetLablist($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 查询行业信息
    * @param  openid,pageindex
    */
    public function GetIndustry() {
        $result = IGD('Common','Info')->GetIndustry();
        $this->ajaxReturn($result);
    }
    
	/**
    * 添加密友 按条件查找蜜友（用户昵称、性别、行业、标签、所在地）
    * @param  pageindex,province,city,town,tab,name,trade,sex,longitude,latitude
    */
    public function Seachusers(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['town'] = I('town');
        $parr['tab'] = I('tab');
        $parr['name'] = I('name');
        $parr['trade'] = I('trade');
        $parr['sex'] = I('sex');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('FriendProcess','Rongcloud')->Seachusers($parr);
        $this->ajaxReturn($result);
    }

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



    /**
    * 添加密友 查找附近的人（距离）
    * @param  pageindex,longitude,latitude
    */
    public function Seachnearby(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;

        $parr['juli'] = 10; //附近10km内
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $result = IGD('FriendProcess','Rongcloud')->Seachusers($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 发送好友请求
    * @param string to_ucode 被添加好友的用户编码
    * @param string verifyMsg 验证消息
    */
    public function request_friend(){
        $verifyMsg = I('verifyMsg');
        $ucode = $this->ucode;

        $to_ucode = I('to_ucode');

        $data = IGD('FriendProcess','Rongcloud')->request_friend($ucode,$to_ucode,$verifyMsg);
        $this->ajaxReturn($data);
    }

    /**
    * 处理好友请求
    * @param string $to_ucode 被添加好友的用户编码
    * @param int $handle 0-拒绝，1-接受
    */
    public function handle_request_friend(){
        $ucode = $this->ucode;

        $to_ucode = I('to_ucode');
        $handle = I('handle');

        $data = IGD('FriendProcess','Rongcloud')->process_request_friend($ucode,$to_ucode,$handle);
        $this->ajaxReturn($data);
    }

    /**
    * 修改好友备注 备注名称、备注电话号码、备注描述
    * @param openid,fucode,remarkname,remarkphone,remarkdesc
    */
    public function Editremark(){
        $ucode = $this->ucode;

        $parr['ucode'] = $ucode;
        $parr['fucode'] = I('fucode');
        $parr['remarkname'] = I('remarkname');
        $parr['remarkphone'] = I('remarkphone');
        $parr['remarkdesc'] = I('remarkdesc');

        $data = IGD('FriendProcess','Rongcloud')->Editremark($parr);
        $this->ajaxReturn($data);
    }

    /**
    * 删除好友
    * @param string $to_ucode 被删除好友的用户编码
    */
    public function delete_friend(){
        $ucode = $this->ucode;

        $to_ucode = I('to_ucode');
        $ucode_arr = explode('|', $to_ucode);

        foreach ($ucode_arr as $key => $value) {
            $result = IGD('FriendProcess','Rongcloud')->delete_friend($ucode,$value);
            if($result['code'] != 0){
                $this->ajaxReturn($result);die;
            }
        }

        $this->ajaxReturn($result);
    }

    /**
    * 不分页获取好友列表
    */
    public function get_friend(){
        $ucode = $this->ucode;

        $friendlist = IGD('FriendProcess','Rongcloud')->get_friend($ucode);
        $this->ajaxReturn(MessageInfo(0,"查询成功",$friendlist));
    }

    //根据融云获取用户信息
    public function GetRongUserInfo() {

        $parr['iucode'] = $this->ucode;
        $parr['ucode'] = I('ucode');

        $result = IGD('UserProcess','Rongcloud')->GetUserInfo($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 聊天页面头部数据
    * @param openid,to_ucode
    */
    public function chat_head(){
        $parr['ucode'] = $this->ucode;

        $parr['to_ucode'] = I('to_ucode');

        $data = IGD('FriendProcess','Rongcloud')->chat_head($parr);
        $this->ajaxReturn($data);
    }

}