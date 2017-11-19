<?php
namespace Home\Controller;
use Think\Controller;
class MessageController extends BaseController {
	//极光消息列表
	public function message_list(){
    	$db = M('users_msg as m');
		//条件
        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $wus['c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = array('like', "%{$c_username}%");
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['m.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->select();
            $ustr = arr_to_str($usinfo);
            if ($ustr) {
                $w['m.c_ucode'] = array('in',$ustr);
            }
        }

    	$c_txcode = trim(I('c_txcode'));
        if (!empty($c_txcode)) {
            $w['m.c_txcode'] = $c_txcode;
        }       
     	$type = trim(I('type'));
	    if (!empty($type)) {
	        if($type == 10){
	          $w['m.c_type'] = 0;
	        }else if($type == 1){
	          $w['m.c_type'] = 1;
	        }else if($type == 2){
	          $w[] = array("m.c_ucode='' or m.c_ucode is null");
	        }
	    }
	    $state = trim(I('state'));
	    if (!empty($state)) {
	        if($state == 1){
	          $w['m.c_state'] = 1;
	        }else{
	          $w['m.c_state'] = 0;
	        }
	    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'm.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'm.*';
        // $panrn['join'] = 'left join t_users as u on m.c_ucode=u.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$data_list = $date['list'];
        foreach ($data_list as $k => $v) {
        	$uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            $data_list[$k]['c_headimg'] = $userinfo['c_headimg'];
        }
		$this->list = $data_list;
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//重新发送消息
	public function re_sendmsg(){
		$txcode = I('txcode');
		$where['c_txcode'] = $txcode;
		$msg_info = M('users_msg')->where($where)->find();

		$panrn['c_txcode'] = $txcode;
		$panrn['c_ucode'] = $msg_info['c_ucode'];
		$panrn['c_title'] = $msg_info['c_title'];
		$panrn['c_tag'] = $msg_info['c_tag'];
		$panrn['c_tagvalue'] = $msg_info['c_id'];//重新设置
		$panrn['c_content'] = $msg_info['c_content'];
		$panrn['c_platform'] = $msg_info['c_platform'];
		$panrn['c_issound'] = $msg_info['c_issound'];

		$result = IGD('JPush','Jpush')->push_notification($panrn);

		$this->ajaxReturn($result);
	}

	//删除消息记录
	public function msglog_del(){
		$Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('users_msg')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
	}

	//发送通知
	public function msg_add(){
		$this->display();
	}

	//ajax 发送通知
	public function send_notice(){
		$str = I('str');
     	$formbul = str_replace('&quot;', '"', $str);
     	$data = objarray_to_array(json_decode($formbul));

     	if (empty($data['title']) || empty($data['content']) || empty($data['tag']) || empty($data['tagvalue'])) {
     		$this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
     	}

     	$flag = 0;
     	if(!empty($data['timer'])){
     		$flag = 1;
     		$parr['timer'] = $data['timer'];
     		$parr['istimer'] = 1;
     	}

     	$parr['title'] = $data['title'];
     	$parr['content'] = $data['content'];
     	$parr['tag'] = $data['tag'];
     	$parr['tagvalue'] = $data['tagvalue'];
     	$parr['weburl'] = $data['weburl'];
     	$parr['platform'] = $data['platform'];
     	$parr['type'] = 0;

     	if($flag == 1){
     		$message = IGD('Msgcentre','Message')->CreateMessegeInfo($parr);
     	}else{
     		$message = IGD('Msgcentre','Message')->CreateMessege($parr);
     	}

     	$this->ajaxReturn($message);
	}
}