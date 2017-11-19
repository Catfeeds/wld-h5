<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 消息中心
 */
class MessageController extends BaseController {

    // 消息中心首页
    public function index(){      
        //获取显示消息记录的条数
        $ucode = session('USER.ucode');
        $parr['ucode']= $ucode;
        $this->ordernum = IGD('Msgcentre','Message')->Getmsgnum($parr);        
        $this->show();
    }

    //联系人
    public function contact()
    {   
        if (IS_AJAX) {            
            $parr['ucode'] = session('USER.ucode');
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 20;
            $result = IGD('Msgcentre','Message')->Getfriends($parr);              
            $this->ajaxReturn($result);
        }
        $this->show();
    }

    //查看系统消息
    public function systemmsg()
    {
        if (IS_AJAX) {
            $parr['type'] = 0;
            $parr['ucode'] = session('USER.ucode');
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 20;
            $result = IGD('Msgcentre','Message')->Getsysmsg($parr);    
            $this->ajaxReturn($result); 
        }
        $parr['ucode']= session('USER.ucode');
        $parr['type'] = 0;
        $result = IGD('Msgcentre','Message')->ReadMsg($parr);
        $this->show();
    }

    //查看订单消息
    public function ordermsg()
    {
        if (IS_AJAX) {
            $parr['type'] = 1;
            $parr['ucode'] = session('USER.ucode');
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 20;
            $result = IGD('Msgcentre','Message')->Getsysmsg($parr);            
            $this->ajaxReturn($result); 
        }
        $parr['ucode']= session('USER.ucode');
        $parr['type'] = 1;
        $result = IGD('Msgcentre','Message')->ReadMsg($parr);
        $this->show();
    }


    //消息列表
    public function message()
    {
        $this->display();
    }

    //获取消息列表数据
    public function GetMessageList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Msgcentre','Message')->Getsysmsg($parr);
        $this->ajaxReturn($result);
    }

    public function newindex()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Msgcentre', 'Message')->GetEachCount($parr);
        $this->data = $result;
        $this->display();
    }

    //小助手首页
    public function msginfo()
    {
        $this->mtype = I('mtype');
        if ($this->mtype == 0) {
            $title = '订单消息';
        } else if ($this->mtype == 1) {
            $title = '活动消息';
        } else if ($this->mtype == 2) {
            $title = '公告';
        } else if ($this->mtype == 3) {
            $title = '小蜜书';
        }

        $this->title = $title;
        $this->display();
    }

    //获取订单消息列表   add by james
    public function GetMsgList(){
        $platType =I('plat_type');
        $msgType =I('msgType');
        $ucode = session('USER.ucode');

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


}