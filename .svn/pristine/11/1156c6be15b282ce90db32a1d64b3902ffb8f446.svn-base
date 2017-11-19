<?php

namespace Login\Interget;

use Think\Controller;

/**
 * 登录模块
 */
class IndexInterget extends Controller {

    public function index(){
    	dump(5555);die;
    	$result = D('Index','Login')->GetUserByCode();
    	$this->ajaxReturn($result);
    }
}