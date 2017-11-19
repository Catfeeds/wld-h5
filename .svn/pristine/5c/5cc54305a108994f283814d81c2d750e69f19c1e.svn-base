<?php
namespace Home\Controller;
//use Think\Controller;
use Base\Controller\BaseController;
/**
 *  营销管理中心，分享佣金，购买优惠
 * 
 */
class RebateController extends BaseController {
  	
  	// 首页 
    public function index()
    {         
    	$rtype = I('rtype');
    	$this->rtype = $rtype;       
    	$this->show();
    }

    /*营销中心获取商品列表*/
 	public function MarketProducts()
 	{
 		$rtype = I('rtype');

 		$parr['pageindex'] = I('pageindex');
 		$parr['pagesize'] = 10;
 		$parr['rtype'] = $rtype;
 		$parr['ucode'] = session('USER.ucode');

        $result = IGD('Businessv2','Store')->MarketProducts($parr);        
        $this->ajaxReturn($result);
 	}

 	/*营销中心 单个产品数据*/
 	public function detail()
 	{
 		$rtype = I('rtype');

 		$parr['rtype'] = $rtype;
 		$parr['ucode'] = session('USER.ucode');
 		$parr['pcode'] = I('pcode');

        $result = IGD('Businessv2','Store')->MarketPro($parr);        
        //$this->ajaxReturn($result);
        $this->data = $result['data'];
        $this->modellist = $result['data']['modellist'];
        $this->productladder = $result['data']['modellist']['ladderprice'];
 		$this->rtype = $rtype;
 		$this->pcode = I('pcode');
 		$this->show();
 	}

 	/**
 	 * 设置分享佣金 ucode,pcode,isspread,spread_proportion
 	 */
 	public function ProductSpread()
 	{
 		$parr['ucode'] = session('USER.ucode');
 		$parr['pcode'] = I('pcode');
 		$parr['isspread'] = I('isspread');
 		$parr['spread_proportion'] = I('spread_proportion'); 
 				
        $result = IGD('Businessv2','Store')->ProductSpread($parr);        
        $this->ajaxReturn($result);
 	}

 	/**
 	 *设置购买优惠 ucode,pcode,isrebate,rebate_proportion
 	 */
 	public function ProductRebate()
 	{
 		$parr['ucode'] = session('USER.ucode');
 		$parr['pcode'] = I('pcode');
 		$parr['isrebate'] = I('isrebate');
 		$parr['rebate_proportion'] = I('rebate_proportion'); 
 				
        $result = IGD('Businessv2','Store')->ProductRebate($parr);        
        $this->ajaxReturn($result);
 	}

}