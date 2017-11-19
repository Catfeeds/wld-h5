<?php

namespace Store\Controller;

use Base\Controller\CheckController;

/**
 * 商家商品信息管理
 */
class BusinessController extends CheckController {
	//添加商品 第一步
	public function Addstep1(){
	    $parr['name'] = I('pname');
	    $parr['desc'] = I('pdesc');
	    $parr['ucode'] = $this->ucode;

	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->NewProudct($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//添加商品 第二步
	public function Addstep2(){
	    $parr['pcode'] = I('pcode');

	    $upload_path = 'store';
	    $result = uploadimg($upload_path);
	   
	    if ($result['code'] != 0) {
	        $this->ajaxReturn(Message(1003, $result['msg']), 'JSON');
	    }

	    $imglist = array_values($result['data']);

	    $parr['sign'] = 1;
	    $parr['imglist'] = $imglist;
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->UploadImgs($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//添加商品 第三步
	public function Addstep3(){
	    $parr['pcode'] = I('pcode');

	    $upload_path = 'store';
	    $result = uploadimg($upload_path);
	    
	    if ($result['code'] != 0) {
	        $this->ajaxReturn(Message(1003, $result['msg']), 'JSON');
	    }
	    
	    $imglist = array_values($result['data']);

	    $parr['sign'] = 0;
	    $parr['imglist'] = $imglist;
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->UploadImgs($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//添加商品 第四步
	public function Addstep4(){
	    $parr['pcode'] = I('pcode');

	    $tempmodelist = objarray_to_array(json_decode(urldecode($_POST['modellist'])));
	    
	    $modelist = array();

	    foreach ($tempmodelist as $key => $value) {
	        $mode['mname'] = $value['mname'];
	        $mode['mnum'] = $value['mnum'];
	        $mode['mprice'] = $value['mprice'];

	        $mode['price1'] = $value['price1'];
	        $mode['price2'] = $value['price2'];
	        $mode['price3'] = $value['price3'];
	        $mode['maxnum1'] = $value['maxnum1'];
	        $mode['maxnum2'] = $value['maxnum2'];
	        $modelist[] = $mode;
	    }

	    $parr['modellist'] = $modelist;

	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->AddModel($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//添加商品 第五步
	public function Addstep5(){
	    $parr['pcode'] = I('pcode');
	    $parr['categoryid'] = I('categoryid');
	    $parr['freeprice'] = I('freeprice');
	    $parr['ishow'] = I('ishow');

	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->AddElse($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//查询商品信息
	public function  ViewProduct(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->GetProductInfo($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//编辑保存商品 第一步
	public function Editstep1(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');
	    $parr['name'] = I('pname');
	    $parr['desc'] = I('pdesc');
	    
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->SaveProudct($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//编辑保存商品 第二步
	public function Editstep2(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');

	    $upload_path = 'store';
	    $result = uploadimg($upload_path);

	    $imglist = array();
	    if ($result['code'] == 0) {
	        $imglist = array_values($result['data']);
	    }

	    if($result['code'] == 1006){
	        $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
	        $url = GetHost() . '/';
	        foreach ($imglist1 as $key => $value) {
	            $imglist[] = str_replace($url, "", $value['c_pimgepath']);
	        }
	    }

	    if (count($imglist) == 0) {
	        return Message(1003, "请上传商品主图");
	    }

	    $parr['sign'] = 1;
	    $parr['imglist'] = $imglist; 
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->EditImgs($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//编辑保存商品 第三步
	public function Editstep3(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');

	    $upload_path = 'store';
	    $result = uploadimg($upload_path);

	    $imglist = array();
	    if ($result['code'] == 0) {
	        $imglist = array_values($result['data']);
	    }
	    
	    if($result['code'] == 1006){
	        $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
	        $url = GetHost() . '/';
	        foreach ($imglist1 as $key => $value) {
	            $imglist[] = str_replace($url, "", $value['c_pimgepath']);
	        }
	    }

	    if (count($imglist) == 0) {
	        return Message(1003, "请上传商品图片");
	    }

	    $parr['sign'] = 0;
	    $parr['imglist'] = $imglist;
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->EditImgs($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//编辑保存商品 第四步
	public function Editstep4(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');

	    //查询产品信息
	    $prowhere['c_pcode'] = I('pcode');
	    $produceinfo = M('Product')->where($prowhere)->find();
	    if (!$produceinfo) {
	        $this->ajaxReturn(Message(1001,'该产品不存在'), 'JSON');
	    }

	    if($produceinfo['c_isagent'] == 1){
	        $this->ajaxReturn(Message(0,'该产品型号不能编辑'), 'JSON');
	    }

	    $tempmodelist = objarray_to_array(json_decode(urldecode($_POST['modellist'])));

	    $modelist = array();

	    foreach ($tempmodelist as $key => $value) {
	        $mode['mcode'] = $value['mcode'];

	        $mode['mname'] = $value['mname'];
	        $mode['mnum'] = $value['mnum'];
	        $mode['mprice'] = $value['mprice'];

	        $mode['price1'] = $value['price1'];
	        $mode['price2'] = $value['price2'];
	        $mode['price3'] = $value['price3'];
	        $mode['maxnum1'] = $value['maxnum1'];
	        $mode['maxnum2'] = $value['maxnum2'];
	        $modelist[] = $mode;
	    }
	   
	    $parr['modellist'] = $modelist;

	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->EditModel($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//添加商品 第五步
	public function Editstep5(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');
	    $parr['categoryid'] = I('categoryid');
	    $parr['freeprice'] = I('freeprice');
	    $parr['ishow'] = I('ishow');

	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->AddElse($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//查询营销中心 所有产品数据
	public function Marketing(){
	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;

	    $parr['ucode'] = $this->ucode;
	    $parr['rtype'] = I('rtype'); //1-购买返利，2-分享返利
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->MarketProducts($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//查询营销中心 单个产品数据
	public function Marketing1(){
	    $parr['ucode'] = $this->ucode;
	    $parr['rtype'] = I('rtype'); //1-购买返利，2-分享返利、
	    $parr['pcode'] = I('pcode'); 
	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->MarketPro($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	//设置分享返利
	public function ProductSpread(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');
	    $parr['isspread'] = I('isspread');
	    $parr['spread_proportion'] = I('spread_proportion');

	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->ProductSpread($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}

	 //设置分享购买返利
	public function ProductRebate(){
	    $parr['ucode'] = $this->ucode;
	    $parr['pcode'] = I('pcode');
	    $parr['isrebate'] = I('isrebate');
	    $parr['rebate_proportion'] = I('rebate_proportion');

	    $business = IGD('Businessv2', 'Store');
	    $result1 = $business->ProductRebate($parr);
	    $this->ajaxReturn($result1, 'JSON');
	}
}