<?php

namespace Base\Controller;

use Think\Controller;

/**
 * 验证签名基类
 */
class BaseController extends Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
//         $str = $_GET;
//         if (!Verification($str)) {
//			 $this->ajaxReturn(Message(1003, '服务器验证失败'));
//         }
    }

}
