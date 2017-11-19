<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 老胡信箱
 */
class MailboxController extends BaseController {

    // 首页 index
    public function index(){
    	$this->ucode = session('USER.ucode');
        $this->statu = I('statu');
        $this->returnurl = I('returnurl');

        $this->show();
    }

    //反馈信息列表  1 已回复 2 未回复
    public function GetMessagesList(){
    	$parr['ucode']= session('USER.ucode');
    	$parr['pageindex'] = I('pageindex');
        $parr['type'] = I('type');

        $result = IGD('Infomation','Agent')->GetMessagesList($parr);

        $this->ajaxReturn($result);

    }


    // 信箱信息详情
    public function detail(){
    	$parr['ucode']= session('USER.ucode');
    	
        $parr['Id'] = I('cid');

        $result = IGD('Infomation','Agent')->MsgDetail($parr);
        $this->data = $result['data'];
        
        $this->statu = I('statu');
        $this->show();
    }

    // 提交反馈
    public function WriteMessages(){
    	$parr['ucode']= session('USER.ucode');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

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
    	$parr['ucode']= session('USER.ucode');
        $parr['content'] = I('content');
        $parr['Id'] = I('Id');
        $result =IGD('Infomation','Agent')->Reply($parr);
        $this->ajaxReturn($result);
    }


}