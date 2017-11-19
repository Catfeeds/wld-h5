<?php
namespace Store\Controller;
use Base\Controller\CheckController;
/**
 * 微商代理系统 分销系统
 */
class AgencyController extends CheckController {
	//代理等级管理 等级列表
	public function AgencyGrade(){
	    $parr['ucode'] = $this->ucode;

	    $result = IGD('Agency', 'Store')->AgencyGrade($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//代理等级管理 等级添加编辑
	public function AddAgencyGrade(){
	    $parr['ucode'] = $this->ucode;

	    $parr['Id'] = I('Id');//编辑时传的参数
	    $parr['grade'] = I('grade');
	    $parr['grade_name'] = I('grade_name');
	    $parr['jy_money'] = I('jy_money');
	    $parr['desc'] = I('desc');

	    $result = IGD('Agency', 'Store')->AddAgencyGrade($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//代理包管理 品牌包列表
	public function AgencyBag(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;

	    $result = IGD('Agency', 'Store')->AgencyBag($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//代理包管理 品牌包上下架
	public function BagStatus(){
	    $parr['ucode'] = $this->ucode;

	    $parr['bag_code'] = I('bag_code');
	    $parr['operate'] = I('operate');//operate(1-上架，2-下架)

	    $result = IGD('Agency', 'Store')->BagStatus($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//代理包管理 品牌包添加编辑
	public function AddAgencyBag(){
	    $parr['ucode'] = $this->ucode;

	    $parr['bag_code'] = I('bag_code');//编辑时传的参数
	    $parr['bag_name'] = I('bag_name');
	    $parr['bag_desc'] = I('bag_desc');
	    $parr['status'] = I('status');

	    $upload_path = 'agencybag';
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

	    $parr['imglist'] = $imglist;

	    $result = IGD('Agency', 'Store')->AddAgencyBag($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//产品包 产品列表
	public function AgencyBagProduct(){
	    $parr['ucode'] = $this->ucode;

	    $parr['bag_code'] = I('bag_code');
	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;

	    $result = IGD('Agency', 'Store')->AgencyBagProduct($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//产品包 产品添加选择商品
	public function ProductList(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;

	    $result = IGD('Agency', 'Store')->ProductList($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//产品包 产品添加编辑
	public function AddBagProduct(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pcode'] = I('pcode');
	    $parr['bag_code'] = I('bag_code');
	    $parr['status'] = I('status');

	    $gradelist = objarray_to_array(json_decode(urldecode($_POST['gradelist'])));
	    
	    $modelist = array();

	    foreach ($gradelist as $key => $value) {
	        $mode['grade_name'] = $value['grade_name'];
	        $mode['grade'] = $value['grade'];
	        $mode['discount'] = $value['discount'];

	        $modelist[] = $mode;
	    }

	    $parr['gradelist'] = $modelist;

	    $result = IGD('Agency', 'Store')->AddBagProduct($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//品牌包内产品上下架
	public function BagProductStatus(){
	    $parr['ucode'] = $this->ucode;

	    $parr['bag_code'] = I('bag_code');
	    $parr['pcode'] = I('pcode');
	    $parr['status'] = I('status');

	    $result = IGD('Agency', 'Store')->BagProductStatus($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//品牌包内产品删除
	public function BagProductDel(){
	    $parr['ucode'] = $this->ucode;

	    $parr['bag_code'] = I('bag_code');
	    $parr['pcode'] = I('pcode');

	    $result = IGD('Agency', 'Store')->BagProductDel($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//分销商列表
	public function AgencyMember(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;

	    $result = IGD('Agency', 'Store')->AgencyMember($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//分销商代理商品列表
	public function AgencyMemberProduct(){
	    $parr['ucode'] = $this->ucode;

	    $parr['agentucode'] = I('agentucode');
	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;

	    $result = IGD('Agency', 'Store')->AgencyMemberProduct($parr);
	    $this->ajaxReturn($result, 'JSON');
	}

	//代理商购买全部记录
	public function BuyProductList(){
	    $parr['ucode'] = $this->ucode;

	    $parr['pcode'] = I('pcode');
	    $parr['agentucode'] = I('agentucode');
	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;

	    $result = IGD('Agency', 'Store')->BuyProductList($parr);
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