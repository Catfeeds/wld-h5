<?php

namespace Home\Controller;

use Think\Controller;
//use Base\Controller\BaseController;

/**
 * 搜索
 */

class SearchController extends Controller {
	/*搜索首页*/
    public function index(){
    	
    	/*搜索条件列表*/
    	$data = IGD('Mall','User')->GetSearchConditionList();    	
    	$this->bydata = $data['data'];
    	    	
    	$sstype = I('sstype');
    	if(empty($sstype)){
    		$this->sstype = 3;
    	}else{
    		$this->sstype = I('sstype');
    	}
    	
    	$this->categoryid = I('categoryid');
    	
    	$this->ucode = session('USER.ucode');
        $this->provincecode = I('provincecode');  
        $this->citycode = I('citycode');  
    	$this->returnurl = I('returnurl');
    	$this->circlename = I('circlename');
    	
    	$this->show();
    }
    
    //所有商品分类列表及搜索
    public function AllProductList(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $parr['pname'] = I('pname');
        $parr['categoryid'] = I('categoryid');
        $parr['order_type'] = I('order_type');

        $result = IGD('Mall','User')->AllProductList($parr);
        $this->ajaxReturn($result);
    }
    
  	/**
    * 添加密友 按条件查找商户
    * @param  pageindex,name,longitude,latitude
    */
    public function SeachShopusers(){
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $parr['name'] = I('name');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }
        $result = IGD('FriendProcess','Rongcloud')->SeachShopusers($parr);
        $this->ajaxReturn($result);
    }
  
}