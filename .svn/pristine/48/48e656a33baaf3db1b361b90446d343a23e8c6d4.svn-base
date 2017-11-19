<?php

namespace Shopping\Controller;

use Base\Controller\ComController;

/**
 * 	拼团活动
 */
class CollageController extends ComController {

	//拼团首页
    public function index()
    {
        $this->show();
    }

    //拼团人数详情列表
    public function plist()
    {
        $this->act_pcode = I('act_pcode');
    	$this->show();
    }

    //推荐参团列表
    public function TjgroupList()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['act_pcode'] = I('act_pcode');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Groupbuy','Newact')->TjgroupList($parr);
        $this->ajaxReturn($result);
    }

    //拼团商品详情页面
    public function pdetail()
    {
        $this->act_pcode = I('act_pcode');

        //获取拼团详情数据
        $parr['act_pcode'] = $this->act_pcode;
        $result = IGD('Groupbuy','Newact')->ProductInfo($parr);
        $this->pinfo = $result['data'];

        //推荐参团列表
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Groupbuy','Newact')->Tjgroup($parr);
        $this->tjgroup = $result['data'];

        $this->pcode = $this->pinfo['c_pcode'];
        $this->ucode = session('USER.ucode');
        //获得产品信息        
        $parr['pcode'] = $this->pcode;
        $parr['actsign'] = 1;
        $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
        $this->data = $result['data'];

        //获取商家信息
        $parr['ucode'] = session('USER.ucode');
        $parr['acode'] = $this->data['c_ucode'];
        $result = IGD('Red', 'Newact')->GetShopBaseInfo($parr);
        $this->user = $result['data'];

        /*商品单条评论*/
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 1;
        $parr['pcode'] = $this->pcode;
        if(empty($parr['pageindex'])){
            $result = IGD('Agency', 'Store')->GetAllScore($parr);
        }else{
            $result = IGD('Agency', 'Store')->GetScore($parr);
        }   
        $this->proscore = $result['data']['list'][0];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->data['share_url'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->data['share_img'];    
        $weixinshare["c_sharetitle"] = '快来团购商品：'.$this->data['share_title'];
        $weixinshare["c_discript"] = '在小蜜，一起拼团买好货，实惠划算。';
        $this->assign('weixinshare',$weixinshare);  
        $this->show();
    }

    //参团页面
    public function pjoin() {
        $this->groupcode = I('groupcode');
        $this->act_pcode = I('act_pcode');
        $parr['ucode'] = session('USER.ucode');
        $parr['groupcode'] = $this->groupcode;
        $result = IGD('Groupbuy','Newact')->GroupInfo($parr);
        $this->groupinfo = $result['data'];

        //获取拼团详情数据
        $parr['act_pcode'] = $this->act_pcode;
        $result = IGD('Groupbuy','Newact')->ProductInfo($parr);
        $this->pinfo = $result['data'];       

        $this->pcode = $this->pinfo['c_pcode'];
        $this->ucode = session('USER.ucode');
        //获得产品信息        
        $parr['pcode'] = $this->pcode;
        $parr['actsign'] = 1;
        $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
        $this->data = $result['data'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Shopping/Collage/pjoin?groupcode='.$this->groupcode.'&act_pcode='.$this->act_pcode;   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->data['share_img'];    
        $weixinshare["c_sharetitle"] = '我在小蜜发起团购：'.$this->data['c_name'];
        $weixinshare["c_discript"] = '在小蜜，一起拼团买好货，实惠划算。';
        $this->assign('weixinshare',$weixinshare);  
    	$this->show();
    }
    
    //拼团商家首页
    public function sjindex()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $this->statu = I('statu');
        $this->show();
    }

    //商家拼团商品管理
    public function MyProductList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['useraction'] = I('useraction');
        $result = IGD('Groupbuy','Newact')->MyProductList($parr);
        $this->ajaxReturn($result);
    }

    public function DelMyProduct()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['act_pcode'] = I('act_pcode');
        $parr['useraction'] = I('useraction');
        $result = IGD('Groupbuy','Newact')->DelMyProduct($parr);
        $this->ajaxReturn($result);
    }

    //创建拼团
    public function sjcreate()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $ucode = session('USER.ucode');
        $userinfo = IGD('Login', 'Login')->GetUserByCode($ucode);
        $this->isfixed = $userinfo['data']['c_isfixed1'];
        //$this->ajaxReturn($userinfo);
        
        $parr['ucode'] = session('USER.ucode');
        $acodeinfo = IGD('Groupbuy','Newact')->GetStoreLocal($parr);
        $this->storeinfo = $acodeinfo['data']; 
        
        $this->show();
    }

    //获取拼团列表
    public function ProductList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Groupbuy','Newact')->ProductList($parr);
        $this->ajaxReturn($result);
    }

    //根据产品编码查询所有型号
    public function GetModeList()
    {
        $parr['pcode'] = I('pcode');
        $result = IGD('Groupbuy','Newact')->ProductModel($parr);
        $this->ajaxReturn($result);
    }

    //创建拼团产品
    public function CreateProduct(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = $data['selpcode'];
        $parr['mcode'] = $data['mcodelval'];
        $parr['actprice'] = $data['proprice'];
        $parr['targetnum'] = $data['groupnum'];
        $parr['usernum'] = $data['limitnum'];
        $parr['totalnum'] = $data['stocknum'];
        $parr['starttime'] = $data['startime'];
        $parr['endtime'] = $data['endtime'];
        $parr['types'] = $data['types'];
        $result = IGD('Groupbuy','Newact')->CreateProduct($parr);
        $this->ajaxReturn($result);
    }
    
    /*拼团记录*/
    public function sjrecord(){
        $this->act_pcode = I('act_pcode');
        $this->statu = I('statu');

        //获取拼团详情数据
        $parr['act_pcode'] = $this->act_pcode;
        $result = IGD('Groupbuy','Newact')->ProductInfo($parr);
        $this->pinfo = $result['data'];
        $this->show();
    }

    //拼团团记录
    //pageindex,act_pcode,logtype(0-待成团，1-已成团，2-拼团失败)
    public function MyProductGroup()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['act_pcode'] = I('act_pcode');
        $parr['logtype'] = I('logtype');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Groupbuy','Newact')->MyProductGroup($parr);
        $this->ajaxReturn($result);
    }

    //帮助
    public function help()
    {
        $this->show();
    }

    //规则
    public function rule()
    {
        $this->show();
    }




}