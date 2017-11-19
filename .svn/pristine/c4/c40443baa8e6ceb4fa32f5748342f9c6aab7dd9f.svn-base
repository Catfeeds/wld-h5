<?php

namespace Activity\Controller;

// use Think\Controller;
use Base\Controller\ComController;

/**
 * 活动模块
 */
class IndexController extends ComController {
	
	/*首页*/
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
    	$this->display();
    }

    // 大转盘活动主页
    public function roulette()
    {  
    	$this->ucode = session('USER.ucode');
        //查询用户是否有默认地址
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('User', 'User')->Getdefaultaddress($parr);
        $this->defaultaddr = 0;
        if (!empty($result['data']) || empty($parr['ucode'])) {
            $this->defaultaddr = 1;
        } else {
            $ucode = session('USER.ucode');
            // 获取地址列表
            $parr['ucode'] = $ucode;
            $addressinfo = IGD('User', 'User');
            $addressdata = $addressinfo->GetUserAddress($parr);
            $this->addresslist = $addressdata['data'];

            /*获取默认地址*/
            $defaultaddr = IGD('User', 'User');
            $defaults = $defaultaddr->Getdefaultaddress($parr);
            $this->defaultdz = $defaults['data'];

            /*获取省份*/
            $parrg['parentid'] = 1;
            $parrg['regiontype'] = 1;
            $region = IGD('User', 'User');
            $province_list = $region->GetAddress($parrg);
            $this->province=$province_list; 
        }
            

        $parr['ucode'] = session('USER.ucode'); 
        $result = IGD('Roulette','Activity')->GetPrizeList($parr);
        $data = $result['data'];
        $this->numtotal = IGD('Roulette','Activity')->GetAwardCount();       
        $this->theme = $data['theme'];
        $this->prizelist = $data['prizelist'];

        $url = encodeurl("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $this->loginurl =  GetHost(1) . '/index.php/Login/Index/index?url=' . $url;

        $pucode = I('pucode');
        $vcode = I('vcode');
        $this->pucode = $pucode;

        //添加浏览记录
        if (!empty($pucode) && !empty($vcode)) {
            $sparr['acode'] = $pucode;
            $sparr['ucode'] = session('USER.ucode');
            $sparr['openid'] = session('openid');
            $sparr['nickname'] = session('nickname');
            $sparr['headimg'] = session('headimgurl');
            $sparr['vcode'] = $vcode;
            $result = IGD('Roulette','Activity')->ViewShareLog($sparr); 
        }

        //添加大转盘转发记录
        if (empty($pucode) && empty($vcode)) {            
            $result = IGD('Roulette','Activity')->AddShareLog($parr);
            if ($result['code'] == 0) {
                $vcode = $result['data'];
                $pucode = $this->ucode;
            }
        }

        
        /*大转盘分享信息*/
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));        
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Activity/Index/roulette?pucode='.$pucode.'&vcode='.$vcode;   
        $this->assign('signPackage',$signPackage);
              
        $weixinshare["c_pthumbnail"] = GetHost()."/useimg/share/roushare.png";    
        $weixinshare["c_discript"] = '获赠抽奖机会，参与小蜜大转盘百分百中奖';
        $weixinshare["c_sharetitle"] = '小蜜大转盘，百分百中奖';
        $this->assign('weixinshare',$weixinshare);
        $this->show();
    }

    // 获取最新的20条大转盘获奖数据
    public function handdrawList() {
        $result = IGD('Roulette','Activity')->GetAwardList();
        $this->ajaxReturn($result);
    }

    //大转盘获点击开始抽奖
    public function run()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Roulette','Activity')->RouletteRun($parr);
        $this->ajaxReturn($result);
    }

    //购物抽奖页面
    public function lottery()
    {
        $this->ucode = session('USER.ucode');
       
        //查询奖品数据
        $parr['ucode'] = $this->ucode;
        $result = IGD('Ballot','Activity')->GetSlotPrize($parr);
        $this->theme = $result['data']['theme'];
        $this->prize = $result['data']['prizelist'];
                
        //获得20条中奖记录
        $result = IGD('Ballot','Activity')->GetSlotLog($parr);
        $this->slotlog = $result['data'];

        $this->aid = $this->prize['c_joinaid'];

        //查询用户是否有默认地址
        $parruser['ucode'] = $this->ucode;
        $result = IGD('User', 'User')->Getdefaultaddress($parruser);
        if (!empty($result['data'])) {
            $this->defaultaddr = 1;
        } else {
            $this->defaultaddr = 0; 
            // 获取地址列表
            $parr['ucode'] = $this->ucode;
            $addressinfo = IGD('User', 'User');
            $addressdata = $addressinfo->GetUserAddress($parr);
            $this->addresslist = $addressdata['data'];

            /*获取默认地址*/
            $defaultaddr = IGD('User', 'User');
            $defaults = $defaultaddr->Getdefaultaddress($parr);
            $this->defaultdz = $defaults['data'];

            /*获取省份*/
            $parrg['parentid'] = 1;
            $parrg['regiontype'] = 1;
            $region = IGD('User', 'User');
            $province_list = $region->GetAddress($parrg);
            $this->province = $province_list;            
        }

        $url = encodeurl("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $this->loginurl =  GetHost(1) . '/index.php/Login/Index/index?url=' . $url;

        /*大转盘分享信息*/
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));        
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = GetHost(1).'/index.php/Activity/Index/lottery?pucode='.$this->ucode; 
        $this->assign('signPackage',$signPackage);
              
        $weixinshare["c_pthumbnail"] = GetHost()."/useimg/share/roushare.png";    
        $weixinshare["c_discript"] = '来小蜜消费，好礼惊喜送不断~';
        $weixinshare["c_sharetitle"] = '您消费我送礼，好礼送不断，小蜜幸运老虎机';
        $this->assign('weixinshare',$weixinshare);

        $this->show();
    }

    //点击老虎机开始抽奖
    public  function SlotRun()
    {
        $parr['aid'] = I('aid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Ballot','Activity')->SlotRun($parr);
        $this->ajaxReturn($result);
    }

    //领取奖品操作
    public function RecieveWinPrize()
    {
        $parr['lid'] = I('lid');
        $parr['ucode'] = session('USER.ucode');
        $parr['openid'] = session('openid');
        $result = IGD('Ballot','Activity')->RecieveWinPrize($parr);
        $this->ajaxReturn($result);
    }
    
    
}