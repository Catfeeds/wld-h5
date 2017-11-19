<?php

namespace Shopping\Controller;

use Base\Controller\ComController;

/**
 * 	活动专区
 */
class ActareaController extends ComController {

	//活动专区首页
    public function index()
    {
        $actlist[0]['name'] = '拼团'; 
        $actlist[0]['id'] = 1;
        $actlist[1]['name'] = '砍价'; 
        $actlist[1]['id'] = 2;
        $actlist[2]['name'] = '秒杀'; 
        $actlist[2]['id'] = 3;
        $this->actlist = $actlist;
		$this->statu = I('statu');
        $this->show();
    }

    //活动中心数据
    public function ShopProductList()
    {
        $statu = I('statu');
        $parr['pagesize'] = 10;
        $parr['pageindex'] = I('pageindex');
        if ($statu == 1) {
            $result = IGD('Groupbuy','Newact')->ShopProductList($parr);
        } else if ($statu == 2) {
            $result = IGD('Bargain','Newact')->ShopProductList($parr);
        } else {
            $result = IGD('Seckill','Newact')->ShopProductList($parr);
        }
        $this->ajaxReturn($result);
    }



}