<?php

namespace Shopping\Controller;

use Base\Controller\WxAuthController;

/**
 * 	砍价活动
 */
class BargainController extends WxAuthController {

	//砍价首页
    public function index()
    {
        $this->show();
    }

    //砍价商品详情页
    public function pdetail()
    {
        $this->act_pcode = I('act_pcode');

        //获取活动产品详情数据
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
        $weixinshare["c_sharetitle"] = '快来砍价商品：'.$this->data['share_title'];
        $weixinshare["c_discript"] = '来小蜜，一起拼享受实惠划算的好货。';
        $this->assign('weixinshare',$weixinshare);  
        $this->show();
    }

    //领取砍价商品
    //ucode,act_pcode,mcode,pnum
    public function Receive()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['act_pcode'] = I('act_pcode');
        $parr['mcode'] = I('mcode');
        $parr['pnum'] = I('pnum');
        $result = IGD('Bargain','Newact')->Receive($parr);
        $this->ajaxReturn($result);
    }

    //砍价商品详情页
    public function bdetail()
    {
        $this->groupcode = I('groupcode');
        $this->ucode = session('USER.ucode');

        $parr['ucode'] = session('USER.ucode');
        $parr['openid'] = session('openid');
        $parr['groupcode'] = $this->groupcode;
        $result = IGD('Bargain','Newact')->ProductDetails($parr);
        $this->groupinfo = $result['data'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Shopping/Bargain/bshare?groupcode='.$this->groupcode;   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->groupinfo['c_imgpath'];    
        $weixinshare["c_sharetitle"] = '我在小蜜抢到一个商品，朋友们快来帮我砍价吧~';
        $weixinshare["c_discript"] = '在小蜜，一起抢购买好货，实惠划算。';
        $this->assign('weixinshare',$weixinshare);  
        $this->show();
    }

    //获取砍价列表
    public function BarginLog()
    {
        $parr['groupcode'] = I('groupcode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Bargain','Newact')->BarginLog($parr);
        $this->ajaxReturn($result);
    }

    //砍价分享页
    public function bshare()
    {
        $this->groupcode = I('groupcode');
        $this->ucode = session('USER.ucode');
        $this->openid = session('openid');
        $this->nickname = session('nickname');
        $this->headimgurl = session('headimgurl');

        $parr['ucode'] = session('USER.ucode');
        $parr['openid'] = session('openid');
        $parr['groupcode'] = $this->groupcode;
        $result = IGD('Bargain','Newact')->ProductDetails($parr);
        $this->groupinfo = $result['data'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Shopping/Bargain/bshare?groupcode='.$this->groupcode;   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->groupinfo['c_imgpath'];    
        $weixinshare["c_sharetitle"] = '我在小蜜抢到一个商品，朋友们快来帮我砍价吧~';
        $weixinshare["c_discript"] = '在小蜜，一起抢购买好货，实惠划算。';
        $this->assign('weixinshare',$weixinshare);  
        $this->show();
    }

    //帮忙砍价
    public function Bargin()
    {
        $parr['groupcode'] = I('groupcode');
        $parr['ucode'] = session('USER.ucode');
        $parr['openid'] = session('openid');
        if (empty($parr['ucode']) && empty($parr['openid'])) {
            $this->ajaxReturn(Message(3000,'请从微信进入砍价'));
        }
        $parr['username'] = session('nickname');
        $parr['headimg'] = session('headimgurl');
        $result = IGD('Bargain','Newact')->Bargin($parr);
        $this->ajaxReturn($result);
    }

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
        $result = IGD('Bargain','Newact')->MyProductList($parr);
        $this->ajaxReturn($result);
    }

    public function DelMyProduct()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['act_pcode'] = I('act_pcode');
        $parr['useraction'] = I('useraction');
        $result = IGD('Bargain','Newact')->DelMyProduct($parr);
        $this->ajaxReturn($result);
    }

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

    //获取拼团列表
    public function ProductList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Bargain','Newact')->ProductList($parr);
        $this->ajaxReturn($result);
    }

    //根据产品编码查询所有型号
    public function GetModeList()
    {
        $parr['pcode'] = I('pcode');
        $result = IGD('Bargain','Newact')->ProductModel($parr);
        $this->ajaxReturn($result);
    }

    //创建拼团产品
    //ucode,pcode,mcode,actprice,israndom,usernum,totalnum,starttime,endtime
    public function CreateProduct(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = $data['selpcode'];
        $parr['mcode'] = $data['mcodelval'];
        $parr['actprice'] = $data['proprice'];
        $parr['israndom'] = $data['israndom'];
        $parr['targetnum'] = $data['groupnum'];
        $parr['usernum'] = $data['limitnum'];
        $parr['totalnum'] = $data['stocknum'];
        $parr['starttime'] = $data['startime'];
        $parr['endtime'] = $data['endtime'];
        $result = IGD('Bargain','Newact')->CreateProduct($parr);
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
        $result = IGD('Bargain','Newact')->MyProductGroup($parr);
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