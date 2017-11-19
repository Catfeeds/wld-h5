<?php

namespace Home\Interget;

use Think\Controller;

/**
 * 登录模块
 */
class IndexInterget extends Controller {

    public function index(){
    	dump(11111);die;
    	$result = D('Index','Login')->GetUserByCode();
    	$this->ajaxReturn($result);
    }
}