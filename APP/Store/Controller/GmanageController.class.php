<?php

namespace Store\Controller;

use Base\Controller\ComController;

/**商品管理**/
class GmanageController extends ComController {

	//首页
	public function index(){
	    /*获取用户信息*/
        $ucode = session('USER.ucode');
        $userinfo = IGD('Login','Login')->GetUserByCode($ucode);
        $userdata = $userinfo['data'];
        $this->source = $userdata['c_isfixed'];

        $this->statu = I('statu');

		$this->display();
	}

    /**
     * 活动商品列表
     */
	public function actgoods(){
        $this->display();
    }

    /**
     *买过的用户列表页面
     */
    public function gcustomer(){
        $parr['ucode'] = session('USER.ucode');
        $this->pcode = I('pcode');

        $this->display();
    }

    /**
     * 搜索
     */
    public function gsearch(){
        $this->statu = I("statu");
        $this->show();
    }

    /**
     *批量管理
     */
    public function batchgoods(){
        $this->ucode = session('USER.ucode');
        $this->statu = I('statu');
        $this->categoryid = I('categoryid');
        $this->name = I('name');

        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Category','Store')->cateList($parr);
        $this->categorylist = $result['data'];

        $this->display();
    }

    //获取商品列表 add by hhs
    public function getProList(){
        $ucode = session('USER.ucode');
        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        /*1,上架；2,下架*/
        $parr['show'] = I('isshow');
        /*排序方式 1 时间降序 2时间升序 3 销量降序 4 销量升序*/
        $parr['order'] = I('order');
        /*搜索关键字*/
        $parr['key'] = I('key');
        $result = IGD('Shop','Store')->getProList($parr);
        $this->ajaxReturn($result);
    }

    //获取买过的人的列表 add by hhs
    public function getBuyUserList(){
        $ucode = session('USER.ucode');
        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Shop','Store')->getBuyUser($parr);
        $this->ajaxReturn($result);
    }

    //获取活动商品列表 add by hhs
    public function getActiveList(){
        $ucode = session('USER.ucode');
        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Shop','Store')->ActiveProductList($parr);

        $this->ajaxReturn($result);
    }

    //获取要上/下架的商品列表 要去除参与活动的商品
    public function getUpDownList(){
        $ucode = session('USER.ucode');
        $parr['ucode'] = $ucode;
        $parr['type'] = I('categoryid');
        $parr['ishow'] = I('isshow');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Shop','Store')->getUpDownList($parr);
        $this->ajaxReturn($result);
    }
    //商品批量上下架
    public function allShowProduct(){
        $ucode = session('USER.ucode');
        $ishow = I('isshow');
        $parr['ishow'] = $ishow;
        $parr['ucode'] = $ucode;
        $parr['ids'] = I('ids');
        $arr = '['.implode($parr['ids'],",").']';
        $parr['ids'] = $arr;
        $result = IGD('Businessv2','Store')->allshowProduct($parr);
        $this->ajaxReturn($result);
    }

    //获取某个商品参与的活动记录
    public function getRecord(){
        $ucode = session('USER.ucode');
        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result =IGD('Shop','Store')->getRecord($parr);
        $this->ajaxReturn($result);
    }


}