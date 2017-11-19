<?php
namespace Agent\Controller;

use Base\Controller\BaseController;
/**
 *  公告控制器
 */
class MessagesController extends BaseController{


    // 判断今天是否提交了信息
    public function IsSummit(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        if(empty($ucode)){
            return Message(1001,'参数缺失');
        }
        $where['c_ucode']=$ucode;
        $where['c_addtime'] =array('egt',date('Y-m-d').' 00:00:00');

        $find =M('Users_messages')->where($where)->find();
        if($find && !empty($find)){
            $flag =1;
            $msg ='不能提交';
        }else{
            $flag =0;
            $msg ='能提交';
        }
        $data['code']=0;
        $data['flag']=$flag;
        $data['msg']=$msg;
        $this->ajaxReturn($data);
    }


    // 提交反馈
    public function WriteMessages()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['content']=I('content');

        $info = uploadimg('messages');
        $imglist = array();
        if ($info['code'] == 0) {
            $imglist = array_values($info['data']);
        }
        if($info['code'] == 1006){
            $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
            $url = GetHost() . '/';
            foreach ($imglist1 as $key => $value) {
                $imglist[] = str_replace($url, "", $value['c_pimgepath']);
            }
        }
        $parr['img1'] = $imglist[0];
        $parr['img2'] = $imglist[1];
        $parr['img3'] = $imglist[2];
        $result =IGD('Infomation','Agent')->PutMessages($parr);

        $this->ajaxReturn($result);
    }


    //回复反馈信息
    public function ReplyMessages(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['content']=I('content');
        $parr['Id'] =I('Id');

        $result =IGD('Infomation','Agent')->Reply($parr);

        $this->ajaxReturn($result);
    }

    //反馈信息列表  1 已回复 2 未回复
    public function GetMessagesList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['pageindex'] =I('pageindex');
        $parr['type'] =I('type');
        $parr['flag'] =I('flag');
        $result =IGD('Infomation','Agent')->GetMessagesList($parr);

        $this->ajaxReturn($result);

    }

    // 单条反馈信息详情
    public function GetDetail(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['Id'] =I('Id');
        $result =IGD('Infomation','Agent')->MsgDetail($parr);
        $this->ajaxReturn($result);
    }


    //判断用户是否可读
    public function IsCanRead(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $info =M('Canread_messages')->where(array('c_ucode'=>$ucode,'c_status'=>0))->find();
        if($info && !empty($info)){
            $data['code']=0;
            $data['msg']='此用户可读取信箱';
        }else{
            $data['code']=1001;
            $data['msg']='没权限，不可读';
        }
        $this->ajaxReturn($data);

    }

}