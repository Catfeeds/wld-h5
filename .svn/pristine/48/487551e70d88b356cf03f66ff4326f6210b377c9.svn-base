<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 商家产品分类模块
 */
class ProductcategoryController extends BaseController {

    //创建分类
    public function createCategory(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $name =I('name');
        $parr['name']=$name;
        $parr['ucode']=$ucode;
        $result =IGD('Category','Store')->createCate($parr);
        $this->ajaxReturn($result);
    }
    //分类列表
    public function getList(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['acode']=I('acode');
        $result =IGD('Category','Store')->cateList($parr);
        $this->ajaxReturn($result);
    }
    //分类编辑
    public function editCategory(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $id =I('id');
        $name =I('name');
        $parr['name']=$name;
        $parr['ucode']=$ucode;
        $parr['id']=$id;
        $result =IGD('Category','Store')->cateEdit($parr);
        $this->ajaxReturn($result);
    }
    //分类删除
    public function delCategory(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $id =I('id');
        $parr['ucode']=$ucode;
        $parr['id']=$id;
        $result =IGD('Category','Store')->cateDel($parr);
        $this->ajaxReturn($result);
    }

    //分类上下移动
    public function moveCategory(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['id1'] =I('id1');
        $parr['id2']=I('id2');
        $result =IGD('Category','Store')->cateMove($parr);
        $this->ajaxReturn($result);
    }

    //执行商品分类，批量
    public function doCategory(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['ids'] =I('ids');
        $parr['id']=I('id');
        $result =IGD('Category','Store')->doCategory($parr);
        $this->ajaxReturn($result);
    }

    //根据商品分类查询商品列表
    public function getCateData(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['id'] =I('id');
        $parr['order'] =I('order');
        $parr['key'] =I('key');
        $parr['pageindex'] =I('pageindex');
        $parr['acode'] =I('acode');
        $result =IGD('Category','Store')->getCateData($parr);
        $this->ajaxReturn($result);
    }

    /*到店自提确认发货，确认收货*/
    public function delivery(){
        $parr['orderid'] = I('orderid');
        $result = IGD('Order','Order')->delivery($parr);
        $this->ajaxReturn($result);
    }



}