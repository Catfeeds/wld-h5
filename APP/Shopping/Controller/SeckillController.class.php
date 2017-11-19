<?php

namespace Shopping\Controller;

// use Think\Controller;
use Base\Controller\ComController;

/**
 * 秒杀
 */
class SeckillController extends ComController {
	
	/*首页*/
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
    	$this->display();
    }

    //秒杀商品详情页面
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
        $weixinshare["c_sharetitle"] = '快来秒杀商品：'.$this->data['share_title'];
        $weixinshare["c_discript"] = '在小蜜，一起秒杀买好货，实惠划算。';
        $this->assign('weixinshare',$weixinshare);  
        $this->show();
    }

    //秒杀商家首页
    public function sjindex()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $this->statu = I('statu');
        $this->show();
    }

    //商家秒杀商品管理
    public function MyProductList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['useraction'] = I('useraction');
        $result = IGD('Seckill','Newact')->MyProductList($parr);
        $this->ajaxReturn($result);
    }

    public function DelMyProduct()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['act_pcode'] = I('act_pcode');
        $parr['useraction'] = I('useraction');
        $result = IGD('Seckill','Newact')->DelMyProduct($parr);
        $this->ajaxReturn($result);
    }

    //创建秒杀
    public function sjcreate()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $ucode = session('USER.ucode');
        $userinfo = IGD('Login', 'Login')->GetUserByCode($ucode);
        $this->isfixed = $userinfo['data']['c_isfixed1'];
        $this->show();
    }

    //获取秒杀列表
    public function ProductList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Seckill','Newact')->ProductList($parr);
        $this->ajaxReturn($result);
    }

    //根据产品编码查询所有型号
    public function GetModeList()
    {
        $parr['pcode'] = I('pcode');
        $result = IGD('Seckill','Newact')->ProductModel($parr);
        $this->ajaxReturn($result);
    }

    //创建秒杀产品
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
        $result = IGD('Seckill','Newact')->CreateProduct($parr);
        $this->ajaxReturn($result);
    }
    
    /*秒杀记录*/
    public function sjrecord(){
        $this->act_pcode = I('act_pcode');
        $this->statu = I('statu');

        //获取秒杀详情数据
        $parr['act_pcode'] = $this->act_pcode;
        $result = IGD('Seckill','Newact')->ProductInfo($parr);
        $this->pinfo = $result['data'];
        $this->show();
    }

    //秒杀团记录
    //pageindex,act_pcode,logtype(0-待成团，1-已成团，2-秒杀失败)
    public function MyProductGroup()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['act_pcode'] = I('act_pcode');
        $parr['logtype'] = I('logtype');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Seckill','Newact')->MyProductGroup($parr);
        $this->ajaxReturn($result);
    }

    /*帮助*/
    public function help(){

        $this->display();
    }


  
   
}