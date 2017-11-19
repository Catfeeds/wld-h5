<?php

namespace Home\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 我的活动,拼团，砍价
 */

class MyactsController extends BaseController {
	/*我的活动首页，拼团，砍价*/
    public function index(){
    	$this->show();
    }
    
    //我的参团记录
    public function MyJoinGroup()
    {
    	$parr['type'] = I('statu');
    	$parr['pagesize'] = 10;
        $parr['pageindex'] = I('pageindex');
        $parr['ucode'] = session('USER.ucode');
        if ($parr['type'] == 0) {
        	$result = IGD('Groupbuy','Newact')->MyJoinGroup($parr);
        } else {
        	$result = IGD('Bargain','Newact')->MyJoinBargin($parr);
        }
        $this->ajaxReturn($result);
    }

    //活动详情
    public function detail()
    {
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

		$this->orderid = I('orderid');
		
        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Shopping/Collage/pjoin?groupcode='.$this->groupcode.'&act_pcode='.$this->act_pcode;   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->data['share_img'];    
        $weixinshare["c_sharetitle"] = '我在小蜜团购：'.$this->data['c_name'];
        $weixinshare["c_discript"] = '在小蜜，一起拼团买好货，实惠划算。';
        $this->assign('weixinshare',$weixinshare);  
    	$this->show();
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

}