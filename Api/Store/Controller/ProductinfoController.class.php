<?php
namespace Store\Controller;
use Base\Controller\BaseController;
/**
 * 	所有查询商品详情页模块
 */
class ProductinfoController extends BaseController {
	//商品基本信息
	public function GetProduceInfo()
	{
	    $key = I('openid');
	    $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

	    $parr['ucode'] = $ucode;
	    $parr['pcode'] = I('pcode');
	    $parr['ispreview'] = I('ispreview');//是否是预览 (0-否，1-是)

	    $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
	    $this->ajaxReturn($result);
	}

	//商品评论
	public function GetScore() {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $parr['pcode'] = I('pcode');

        if(!empty($parr['pageindex'])){
        	$result = IGD('Agency', 'Store')->GetAllScore($parr);
        }else{
        	$result = IGD('Agency', 'Store')->GetScore($parr);
        }

        $this->ajaxReturn($result);
    }
}