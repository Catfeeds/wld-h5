<?php

namespace Base\Controller;

use Think\Controller;

/**
 * 验证登录基类
 */
class CheckController extends BaseController {

    public $ucode;   //定义全局ucode

    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
        $key = I('openid');
        $this->ucode = IGD('Common','Redis')->Rediesgetucode($key);
        if ($this->ucode) {
            IGD('Common', 'Redis')->RediesStoreSram($key,$this->ucode,86400);
        } else {
            $this->ajaxReturn(Message(1009, '验证失效，请重新登录'));
        }
    }

}
