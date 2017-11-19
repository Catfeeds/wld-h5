<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  新版系统设置
 */
class SystemsetController extends BaseController {
	//首页配置信息
	public function homepage(){
	    $Redis = IGD('Redis','Redis');
	    $result = $Redis->Gethash('newact');
	    // if($result['code'] != 0){
	    //     $this->error($result['msg']);
	    // }
	    $this->data = $result['data'];	   

	    $this->display();
	}

	//首页配置信息设置
	public function homepage_setting(){
	    $a = array(
	        'shownum' => $_POST['shownum'],
	        'redclick' => $_POST['redclick'],
	        'boxclick' => $_POST['boxclick'],
	        'airclick' => $_POST['airclick'],
	        'boxrand' => $_POST['boxrand'],
	        'airrand' => $_POST['airrand'],
	        'spandnum' => $_POST['spandnum'],
	    );

	    $result = IGD('Redis','Redis')->Sethash('newact',$a);

	    if($result['code'] == 0){
	        $this->success("保存成功");
	    }else{
	        $this->error($result['msg']);
	    }
	}
}