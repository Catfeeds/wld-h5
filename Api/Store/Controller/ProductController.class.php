<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 商家产品模块
 */
class ProductController extends BaseController {

    public function test(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $info =M('Product_category')->where(array('c_ucode'=>$ucode))->select();
        for($i=0;$i<count($info);$i++){
            $aa[$i]=$info[$i]['c_id'];
        }
        $bb =implode(',',$aa);

        dump($bb);

    }
    //查询微商自己的产品列表
    public function GetProduceList() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Shop','Store')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }

    //获取商品列表 add by james
    public function getProList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['show'] =I('show');
        $parr['order'] =I('order');
        $parr['key'] =I('key');
        $result = IGD('Shop','Store')->getProList($parr);
        $this->ajaxReturn($result);
    }

    //获取买过的人的列表 add by james

    public function getBuyUserList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['pcode'] =I('pcode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Shop','Store')->getBuyUser($parr);
        $this->ajaxReturn($result);
    }

    //获取活动商品列表 add by james
    public function getActiveList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result =IGD('Shop','Store')->ActiveProductList($parr);

        $this->ajaxReturn($result);
    }

    //获取某个商品参与的活动记录
    public function getRecord(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['pcode']=I('pcode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result =IGD('Shop','Store')->getRecord($parr);

        $this->ajaxReturn($result);
    }

    //获取要上/下架的商品列表 要去除参与活动的商品
    public function getUpDownList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['type'] =I('type_id');
        $parr['ishow'] = I('ishow');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result =IGD('Shop','Store')->getUpDownList($parr);
        $this->ajaxReturn($result);
    }



    //查询商品信息
    public function  ViewProduct(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $business = IGD('Businessv2', 'Store');
        $result1 = $business->GetProductInfo($parr);
        $this->ajaxReturn($result1, 'JSON');
    }

	//添加商品 第一步
    public function Addstep1(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['name'] = I('pname');
        $parr['desc'] = I('pdesc');
        $parr['ucode'] = $ucode;

        $business = IGD('Businessv2', 'Store');
        $result1 = $business->NewProudct($parr);
        $this->ajaxReturn($result1, 'JSON');
    }

    //编辑保存商品 第一步
    public function Editstep1(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['name'] = I('pname');
        $parr['desc'] = I('pdesc');

        $business = IGD('Businessv2', 'Store');
        $result1 = $business->SaveProudct($parr);
        $this->ajaxReturn($result1, 'JSON');
    }

    //编辑保存商品 第二步
    public function Editstep2(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
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
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
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
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
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
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['categoryid'] = I('categoryid');
        $parr['freeprice'] = I('freeprice');
        $parr['ishow'] = I('ishow');

        $business = IGD('Businessv2', 'Store');
        $result1 = $business->AddElse($parr);
        $this->ajaxReturn($result1, 'JSON');
    }


	//商品评论
    public function ProductScore() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $upload_path = 'score';
        $result = uploadimg($upload_path);

        $imglist = array();
        if ($result['code'] == 0) {
            $imglist = array_values($result['data']);
        }

        $parr['detailid'] = I('detailid');
        $parr['score'] = I('score');
        $parr['content'] = I('content');
        $parr['acode'] = I('acode');
        $parr['img'] = $imglist;
        $orderdb = IGD('Order', 'Order');
        $result = $orderdb->ProductScore($parr);
        $this->ajaxReturn($result);
    }

	//产品分享记录添加
    public function Spreadlog() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $ss = IGD('Shoppingcar', 'User');
        $result = $ss->Spreadlog($parr);
        $this->ajaxReturn($result);
    }

    // 评论管理首页
    public function comment()
    {
        $parr['pcode'] = I('pcode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $comment = IGD('Shop','Store');
        $result = $comment->GetScore($parr);            
        $this->ajaxReturn($result);
    }

    // 商品删除
    public function DeleteProduct()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  
        $parr['pcode'] = I('pcode');    
        $proinfo = IGD('Businessv2','Store');
        $result = $proinfo->DeleteProduct($parr);
        $this->ajaxReturn($result);
    }

    // 商品上下架
    public function showProduct()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $ishow = I('ishow');
        if ($ishow == 1) {
            $ishow = 1;
        } else {
            $ishow = 2;
        }
        $parr['ishow'] = $ishow; 
        $parr['ucode'] = $ucode;  
        $parr['pcode'] = I('pcode');    
        $proinfo = IGD('Businessv2','Store');
        $result = $proinfo->showProduct($parr);
        $this->ajaxReturn($result);
    }
    //商品批量上下架
    public function allShowProduct(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $ishow = I('ishow');
        $parr['ishow'] = $ishow;
        $parr['ucode'] = $ucode;
        $parr['ids'] = I('ids');
        $result = IGD('Businessv2','Store')->allshowProduct($parr);
        $this->ajaxReturn($result);
    }
}