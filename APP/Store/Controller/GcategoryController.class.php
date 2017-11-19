<?php

namespace Store\Controller;

use Base\Controller\ComController;
//use Think\Controller;

/**
 * 商品分类管理
 */
class GcategoryController extends ComController {
	
    //首页
    public function index()
    {
        $this->display();
    }

    //创建分类
    public function createCategory(){
        $name = I('name');
        $parr['name'] = $name;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Category','Store')->createCate($parr);
        $this->ajaxReturn($result);
    }
    //分类列表
    public function getList(){
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Category','Store')->cateList($parr);
        $this->ajaxReturn($result);
    }
    //分类编辑
    public function editCategory(){
        $id = I('cid');
        $name = I('name');
        $parr['name'] = $name;
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = $id;
        $result = IGD('Category','Store')->cateEdit($parr);
        $this->ajaxReturn($result);
    }
    //分类删除
    public function delCategory(){
        $id = I('cid');
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = $id;
        $result = IGD('Category','Store')->cateDel($parr);
        $this->ajaxReturn($result);
    }

    //分类上下移动
    public function moveCategory(){
        $parr['ucode'] = session('USER.ucode');
        $parr['id1'] = I('id1');
        $parr['id2']= I('id2');
        $result =IGD('Category','Store')->cateMove($parr);
        $this->ajaxReturn($result);
    }

    //执行商品分类，批量
    public function doCategory(){
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = I('id');
        $parr['ids'] = I('ids');
        $arr = '['.implode($parr['ids'],",").']';
        $parr['ids'] = $arr;
        $result = IGD('Category','Store')->doCategory($parr);
        $this->ajaxReturn($result);
    }

    //根据商品分类查询商品列表
    public function getCateData(){
//        $ucode = I('fromucode');
//        if (empty($ucode)) {
//            $ucode = session('USER.ucode');
//        }
        $parr['acode'] = I('fromucode');
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = I('cateid');
        $parr['order'] = I('order');
        $parr['key'] = I('key');
        $parr['pageindex'] = I('pageindex');
        $result = IGD('Category','Store')->getCateData($parr);
        $this->ajaxReturn($result);
    }

}