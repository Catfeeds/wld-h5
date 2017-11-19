<?php

namespace Activity\Controller;

use Think\Controller;
// use Base\Controller\ComController;

/**
 * 线上商家推广计划
 */
class ExtgoodsController extends Controller {
	
	/*首页*/
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
    	$this->display();
    }

    
}