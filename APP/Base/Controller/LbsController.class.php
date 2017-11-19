<?php

namespace Base\Controller;

use Think\Controller;
use Com\RedisModel;

/**
 * 定时更新用户位置信息
 */
class LbsController extends Controller {

    public function __construct() {
        parent::__construct();
        ob_end_clean(); //清除缓冲区,避免乱码
        header('Content-Type:text/html; charset=utf-8');
    }

    //批量更新用户geo位置信息
    public function UpuserLocal()
    {
    	// $Redis = new RedisModel();
    	// $this->redis = $Redis->_REDIS;
    	// dump($this->redis->keys('Local_*'));
     //    $this->redis->delete($this->redis->keys('JLY_*'));   //批量删除包含的key
        
        // for ($i=0; $i < 60; $i++) { 
        	$result = IGD('Local','Lbs')->UpuserLocal();
    		dump($result);
        // }    	
    }

}
