<?php
namespace Home\Controller;
use Base\Controller\CheckController;
/**
 * 推广中心模块
 */
class TgcenterController extends CheckController {
	//所有具有推广返利属性的产品
    public function allproduct(){
        $parr['ucode'] = $this->ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['name'] = I('name');
        $parr['pagesize'] = 10;

        $result = IGD('Tgcenter', 'User')->Allproduct($parr);
        $this->ajaxReturn($result);
	}

	//所有我的推广产品
	public function my_allproduct(){
        $parr['ucode'] = $this->ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $result = IGD('Tgcenter', 'User')->My_Allproduct($parr);
        $this->ajaxReturn($result);
	}

	//删除我的推广产品
	public function myproduct_del(){
        $parr['ucode'] = $this->ucode;
        $parr['pcode'] = I('pcode');

        $result = IGD('Tgcenter', 'User')->Myproduct_del($parr);
        $this->ajaxReturn($result);
	}
}