<?php

namespace Base\Controller;

use Think\Controller;

/**
 *  公用页面控制器
 *
 */
class CommonController extends Controller {

  	// 支付成功页面
    public function success()
    {
        $orderid = I('orderid');
        if (substr($orderid,0,1) == 's') {
            $this->sign = 1;
        } else if (substr($orderid,0,1) == 'n') {
            $this->sign = 2;
        }  else if (substr($orderid,0,1) == 'c' || substr($orderid,0,1) == 'p') {
            $this->sign = 3;
        } else {
            $this->sign = 0;
        }
    	$this->show();
    }

    // 错误页面
    public function fail()
    {
        $this->sign = I('sign');
    	$this->show();
    }

    // 底部
    public function foot()
    {
    	$this->show();
    }

    // 跳转页面
    public function jump()
    {
    	$this->show();
    }

    // 跳转页面
    //  public function 404()
    // {
    //     $this->show();
    // }
    
    //系统错误页面
      public function exception()
    {
        $this->show();
    }
     //模板
      public function muban()
    {
        $this->show();
    }
}