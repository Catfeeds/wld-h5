<?php
namespace Store\Controller;
use Base\Controller\CheckController;
/**
 * 微商代理系统 微店管理
 */
class SmallshopController extends CheckController {
	//微店管理  头部及推广数据
	public function MySmallShop(){
	    $parr['ucode'] = $this->ucode;

	    $result = IGD('Smallshop', 'Store')->MySmallShop($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//微店管理 代理的所有代理商品列表
	public function SmallShopProduct(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pageindex'] = I('pageindex');
	    $parr['operate'] = 10;

	    $result = IGD('Smallshop', 'Store')->SmallShopProduct($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//商品管理 代理过的商家列表
	public function AgencyMerchant(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pageindex'] = I('pageindex');
	    $parr['operate'] = 10;

	    $result = IGD('Smallshop', 'Store')->AgencyMerchant($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//商品管理 代理某个商家的所有代理商品列表
	public function AgencyProduct(){
	    $parr['ucode'] = $this->ucode;

	    $parr['acode'] = I('acode');
	    $parr['pageindex'] = I('pageindex');
	    $parr['operate'] = 10;

	    $result = IGD('Smallshop', 'Store')->AgencyProduct($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//商品管理 修改商品库存
	public function EditNum(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pcode'] = I('pcode');
	    $parr['num'] = I('num');

	    $result = IGD('Smallshop', 'Store')->EditNum($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//商品管理 删除商品
	public function ProductDel(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pcode'] = I('pcode');

	    $result = IGD('Smallshop', 'Store')->ProductDel($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//商品管理 商家等级详情
	public function GradeDetails(){
	    $parr['apcode'] = I('acode');

	    $result = IGD('Smallshop', 'Store')->GradeDetails($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//商品详情（banner，详情）
	public function ProductDetails(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pcode'] = I('pcode');
	    $parr['bag_code'] = I('bag_code');

	    $result = IGD('Agency', 'Store')->GetProduceInfo($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//商品评论
	public function GetScore() {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $parr['pcode'] = I('pcode');

        if(empty($parr['pageindex'])){
        	$result = IGD('Agency', 'Store')->GetAllScore($parr);
        }else{
        	$result = IGD('Agency', 'Store')->GetScore($parr);
        }

        $this->ajaxReturn($result);
    }
}