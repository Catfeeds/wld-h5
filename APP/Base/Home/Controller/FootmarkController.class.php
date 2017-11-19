<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;
/**
 *  我的足迹
 */
class FootmarkController extends BaseController {

    //足迹首页
    public function index()
    {
        $this->ucode = session('USER.ucode');
        if (is_weixin()) {
            $this->apptype = 4;
        } else {
            $this->apptype = get_device_type();
        }
        $this->show();
    }

    //获取浏览过的商家列表
    public function VisitShopinfo()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Servecentre','Serve')->VisitShopinfo($parr);
        $this->ajaxReturn($result);
    }

    //获取浏览过的商品列表
    public function VisitGoodsinfo()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Servecentre','Serve')->VisitGoodsinfo($parr);
        $this->ajaxReturn($result);
    }

}
