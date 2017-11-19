<?php

namespace Home\Controller;

use Base\Controller\BaseController;

/**
 * 微聊模块
 */
class MsgcentreController extends BaseController {

    //消息中心消息数量
    public function Getmsgnum() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;

        $ss = IGD('Msgcentre', 'Message');
        $data = $ss->Getmsgnum($parr);

        $this->ajaxReturn(MessageInfo(0, '查询成功', $data));
    }


    //获取对应消息的条数  add by james
    public function GetEachMsgCount(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $data =IGD('Msgcentre', 'Message')->GetEachCount($parr);
        $this->ajaxReturn(MessageInfo(0, '查询成功', $data));
    }

    //获取消息列表
    public function Getmsg() {
        $type = I('type');
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['pageindex'] = I('pageindex');
        $parr['ucode'] = $ucode;

        $parr['platform'] = $type;
        $parr['pagesize'] = 10;

        $ss = IGD('Msgcentre', 'Message');
        $result = $ss->Getsysmsg($parr);
        //修改成已读状态
        $read_result = $ss->ReadMsg($parr);
        if ($read_result['code'] !== 0) {
            $this->ajaxReturn($read_result);
        }
        $this->ajaxReturn($result);
    }

    //获取订单消息列表   add by james
    public function GetMsgList(){
        $platType =I('plat_type');
        $msgType =I('msgType');
        $key =I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['pageindex'] = I('pageindex');
        $parr['ucode'] = $ucode;
        $parr['platform'] = $platType;
        $parr['pagesize'] = 10;
        if($msgType ==0 ){  //  订单消息
            $result = IGD('Msgcentre', 'Message')->GetOrderMessages($parr);
        }elseif($msgType ==3){ //系统消息
            $result = IGD('Msgcentre', 'Message')->GetSysMessages($parr);
        }elseif($msgType ==1){  // 活动消息
            $result = IGD('Msgcentre', 'Message')->GetActiveMessages($parr);
        }elseif($msgType ==2){  //公告消息
            $result = IGD('Msgcentre', 'Message')->GetGongMessages($parr);
        }
        $this->ajaxReturn($result);

    }


    //消息滑动已读 或 未读

    public function MessageStatus(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['type'] =I('type');  //消息类别
        $result =IGD('Msgcentre','Message')->ChangeMessStatus($parr);
        $this->ajaxReturn($result);
    }

    //获取好友列表
    public function Getfriends() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;

        $ss = IGD('Msgcentre', 'Message');
        $result = $ss->get_friend($ucode);

        $this->ajaxReturn($result);
    }

    //获取用户是否开启到帐推送提醒功能
    public function GetPushstate() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;

        $ss = IGD('Msgcentre', 'Message');
        $result = $ss->GetPushstate($parr);

        $this->ajaxReturn($result);
    }

    //操作用户是否开启到帐推送提醒功能
    public function OperateState() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['user_action'] = I('user_action');//0-关闭，1-开启

        $ss = IGD('Msgcentre', 'Message');
        $result = $ss->OperateState($parr);

        $this->ajaxReturn($result);
    }

}
