<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;
/**
 *  推广中心
 */
class ExpandController extends BaseController {
    public function index(){
        $this->apptype = I('type');
        if (!$this->apptype) {
            $this->apptype = get_device_type();
        }
        $this->statu = I('statu');
        $this->show();
    }

    //初始化引入微信分享类
    public function _initialize() {
        vendor('Wxshare.wxshare');
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));
        $signPackage = $wxshare->GetSignPackage();
        $this->assign('signPackage',$signPackage);
    }

    /**
     * 获取推广用户产品
     */
    public function GetUsertuiguang()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Shop','Store')->GetUsertuiguang($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 删除推广产品
     */
    public function ClearExpandpro()
    {
        $parr['id'] = I('id');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Shop','Store')->GetUser($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 产品分享记录添加
     */
    public function Spreadlog()
    {
        $ucode = session('USER.ucode');
        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $result = IGD('Shoppingcar', 'User')->Spreadlog($parr);
        $this->ajaxReturn($result);
    }

    /**
     * /所有具有推广佣金属性的产品
     * @return [type] [description]
     */
    public function allproduct(){
        $parr['pageindex'] = I('pageindex');
        $parr['name'] = I('name');
        $parr['pagesize'] = 10;

        $orderdb = IGD('Tgcenter', 'User');
        $result = $orderdb->Allproduct($parr);
        $this->ajaxReturn($result);
    }

    /**
     * /所有我的推广产品
     * @return [type] [description]
     */
    public function my_allproduct(){

        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $orderdb = IGD('Tgcenter', 'User');
        $result = $orderdb->My_Allproduct($parr);
        $this->ajaxReturn($result);
    }

    /**
     * /删除我的推广产品
     * @return [type] [description]
     */
    public function myproduct_del(){

        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');

        $orderdb = IGD('Tgcenter', 'User');
        $result = $orderdb->Myproduct_del($parr);
        $this->ajaxReturn($result);
    }
 
}