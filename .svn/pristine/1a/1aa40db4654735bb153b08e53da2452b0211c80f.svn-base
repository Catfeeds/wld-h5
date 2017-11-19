<?php

namespace Trade\Controller;

use Think\Controller;
use Base\Controller\ComController;

/**
 * 商圈模块
 */
class IndexController extends ComController {
	
	/*商圈首页*/
    public function index(){
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
    	/*是否有消息*/
        $parr['ucode']= session('USER.ucode');
        $ordernum = IGD('Msgcentre','Message')->Getmsgnum($parr);
        $this->msgtag = 0;
        if (($ordernum['order_msg'] + $ordernum['sys_msg']) > 0) {
            $this->msgtag = 1;
        }
        
        $this->ucode = session('USER.ucode');

        $ucode = session('USER.ucode');
        $userinfo = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $userinfo['data'];

        
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
    	$this->provincecode = I('provincecode');
    	$this->citycode = I('citycode');
    	$this->display();
    }
    
    /*发布资源*/
    public function publish(){
    	if (!session('USER.ucode')) {
            $url = encodeurl("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $jumpurl = WEB_HOST . '/index.php/Login/Index/index?url=' . $url;
            header("Location:" . $jumpurl);
            die();
        }
        
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
    	
    	$this->province = I('province');
    	$this->city = I('city'); 
    	
    	$this->circlename = I('circlename');
    	
    	$this->provincecode = I('provincecode');
    	$this->citycode = I('citycode'); 
    	
    	$this->display();
    }

	/*资源详情*/
	public function redetail(){	  
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['sid'] = I('rid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->GetResourceInfo($parr);
        $this->data = $result['data'];
    		  	
    	$this->ucode = session('USER.ucode');
    	$this->circlename = I('circlename');
    	
    	$this->display();
	}

	
	/*切换商圈页面*/
	public function changetrade(){
		if(!session('USER.ucode')){
            $this->userlogin();die;
        }
    	$this->ucode = session('USER.ucode');
    	$this->returnurl = I('returnurl');
    	
    	/*省份列表*/
    	$result_p = IGD('Circle','Trade')->Getprovinces();
    	$this->provincedata = $result_p['data'];
    	    	
    	$this->provincecode = I('provincecode'); 
    	    		
    	$this->display();
	}

	/*商家*/		
	public function shopslist(){
		if(!session('USER.ucode')){
            $this->userlogin();die;
        }
    	$this->ucode = session('USER.ucode');
        $this->provincecode = I('provincecode');  
        $this->citycode = I('citycode');  
    	$this->returnurl = I('returnurl');	
    	$this->display();
	}

    /**
    * 商圈地图商家数据
    * 
    */
    public function Merchant() {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');

        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
       
        // $parr['juli'] = I('juli');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['gettype'] = I('gettype');//1最热，2最新，3最近
        
        $result = IGD('Circle','Trade')->Merchant($parr);
        $this->ajaxReturn($result);
    }
	
	/*活动列表*/
	public function activitylist(){
		if(!session('USER.ucode')){
            $this->userlogin();die;
        }
    	$this->ucode = session('USER.ucode');
        $this->provincecode = I('provincecode');  
        $this->citycode = I('citycode');  
    	$this->returnurl = I('returnurl');   	
    			
    	$this->display();
	}

    //获取商圈活动列表数据
    public function ShopactData()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');
        $parr['acttype'] = I('acttype');   //0全部，1拼团，2优惠券，3秒杀，4砍价
        $result = IGD('Circle','Trade')->ShopactData($parr);
        $this->ajaxReturn($result);
    }

    //活动分享回调
    public function TurnCallback()
    {
        $parr['Id'] = I('Id');
        $result = IGD('Circle','Trade')->TurnCallback($parr);
        $this->ajaxReturn($result);
    }
	
	
	
	
	/**
     * 获取推荐商品列表
     */
    public function GetProductList()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        //$result = IGD('Shop','Store')->GetProduceList($parr);
        $result = IGD('Resource','Trade')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }
    
    /**
     * 发布资源
     */
    public function SubResource(){
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
    	$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
    	$parr['ucode'] = session('USER.ucode');        
        $parr['isaddress'] = $data['isaddress'];
        $parr['address'] = $data['address'];
        $parr['pcode'] = $data['pcode'];
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['provincecode'] = $data['provincecode'];
        $parr['citycode'] = $data['citycode'];
        if (empty($parr['longitude']) || empty($parr['latitude'])) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        } 
        if(!empty($data['content'])){
        	$parr['content'] = $data['content'];
        }      
        if(!empty($data['imglist'])){
        	$imglist =	explode("|", $data['imglist']);
        	$parr['imglist'] = $imglist;
        }
        if(!empty($data['content']) && !empty($data['imglist'])){
        	$parr['content'] = $data['content'];
        	$imglist =	explode("|", $data['imglist']);
        	$parr['imglist'] = $imglist;
        }
        //dump($data);dump($parr);die;
        $result = IGD('Resource','Trade')->AddResource($parr);
        $this->ajaxReturn($result);
    }
    
    /**
     * 获取某商圈资源列表
     */
    public function GetCircleResource(){
    	$parr['ucode'] = session('USER.ucode');
    	$parr['provincecode'] = I('provincecode');
    	$parr['citycode'] = I('citycode');
    	$parr['pageindex'] = I('pageindex');
    	$parr['pagesize'] = 10;
    	$parr['condition'] = I('condition');
    	$result = IGD('Resource','Trade')->GetCircleResource($parr);
    	$this->ajaxReturn($result);
    }
    
    /**
     * 获取所在商圈
     */
    public function Getcircleinfo(){
    	$parr['ucode'] = session('USER.ucode');
    	$parr['province'] = I('province');
    	$parr['city'] = I('city');
    	$parr['provincecode'] = I('provincecode');
    	$parr['citycode'] = I('citycode');
    	$result = IGD('Circle','Trade')->Getcircleinfo($parr);
    	$this->ajaxReturn($result);
    }
    
    /**
     * 获取热门推荐商圈列表
     */
//  public function Gethotcircle(){
//  	$result = IGD('Circle','Trade')->Gethotcircle($parr);
//  	$this->ajaxReturn($result); 
//  }
    
    /**
     * 推荐所在省的商圈列表
     */
    public function Getcirclelist(){
    	$parr['ucode'] = session('USER.ucode');
    	$parr['provincecode'] = I('provincecode');
    	$result = IGD('Circle','Trade')->Getcirclelist($parr);
    	$this->ajaxReturn($result);       	
    }
    
    //获取商圈用户签到信息
    public function Signinfo(){
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        $result = IGD('Circle','Trade')->Signinfo($parr);
        $this->ajaxReturn($result);
    }

    //商圈用户签到操作
    public function Usersign(){
        $parr['ucode'] = session('USER.ucode');
        
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        $result = IGD('Circle','Trade')->Usersign($parr);
        $this->ajaxReturn($result);
    }
    
    //发表评论
    public function CommentResource()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['content'] = I('content');
        $parr['resourceid'] = I('resourceid');
        $parr['ucode'] = session('USER.ucode');
        $parr['bid'] = I('bid');
        $result = IGD('Resource','Trade')->CommentResource($parr);
        $this->ajaxReturn($result);
    }

    //提交点赞信息
    public function ResourceLike()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['handle'] = I('handle');
        $parr['resourceid'] = I('resourceid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->ResourceLike($parr);
        $this->ajaxReturn($result);
    }

    // 关注操作
    public function UserAttention()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['handle'] = I('handle');
        $parr['issue_ucode'] = I('issue_ucode');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->UserAttention($parr);
        $this->ajaxReturn($result);
    }

    // 删除资源
    public function DeleteResource()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['sid'] = I('sid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->DeleteResource($parr);
        $this->ajaxReturn($result);
    }

    //删除评论
    public function DeleteComment()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['cid'] = I('cid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->DeleteComment($parr);
        $this->ajaxReturn($result);
    }

    //商圈搜索
    public function searchtrade()
    {
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
    	$this->ucode = session('USER.ucode');
        $this->provincecode = I('provincecode');  
        $this->citycode = I('citycode');  
    	$this->returnurl = I('returnurl');
    	$this->circlename = I('circlename');	
        $this->display();
    }

    /**
    * 获取商圈活动跑马灯数据
    * @param  provincecode,citycode
    */
    public function NewShopact(){
        $parr['ucode'] = session('USER.ucode');       
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        $result = IGD('Circle','Trade')->NewShopact($parr);
        $this->ajaxReturn($result);
    }    
    
    /*商圈分享*/
	public function shares(){
		$parr['sid'] = I('resourceid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->GetResourceInfo($parr);
        $this->data = $result['data'];
        
		$wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->data['share_url'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->data['share_img'];    
        $weixinshare["c_sharetitle"] = $this->data['share_title'];
        $weixinshare["c_discript"] = $this->data['share_desc'];
        $this->assign('weixinshare',$weixinshare); 
        $this->display();
	}
	
	/*小蜜商圈分享*/
	public function sqshare(){
    	if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['sid'] = I('rid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->GetResourceInfo($parr);
        $this->data = $result['data'];
    		  	
    	$this->ucode = session('USER.ucode');
    	$this->circlename = I('circlename');
		$this->display();
	}
	

    /*加入商圈页面*/
    public function joincircle(){
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $this->ucode = session('USER.ucode');
        $this->returnurl = I('returnurl');
        
        /*省份列表*/
        $result_p = IGD('Circle','Trade')->Getprovinces();
        $this->provincedata = $result_p['data'];
        
        //获取用户所在省市区 
        $areainfo = GetIpLookup();
        foreach ($this->provincedata as $key => $value) {
            if (strpos($areainfo['province'],$value['c_circle_name']) !== false) {
                $provincecode = $value['c_code'];
            }
        }       
        $this->provincecode = $provincecode; 
                    
        $this->display();
    }

    //举报页面
    public function report()
    {
        $this->content_id = I('content_id');
        
        $data =IGD('Mall','User')->GetTipsLists();
        $this->tiplist = $data['data'];
        
    	$this->provincecode = I('provincecode');
    	$this->citycode = I('citycode');
    	
        $this->display();
    }

    //商家加入商圈
    public function ShopJoinCircle(){
        $parr['ucode'] = session('USER.ucode');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');
        $parr['address'] = I('address');
        $result = IGD('Circle','Trade')->ShopJoinCircle($parr);
        $this->ajaxReturn($result);
    }

    //提交举报信息
    public function PutTipInfos(){
        $ucode = session('USER.ucode');

        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['tip_id'] = $data['tip_id'];
        $parr['content'] = $data['content'];
        $parr['content_id'] = $data['content_id'];
        $parr['ucode'] = $ucode;

        $result =IGD('Mall','User')->PutTipInfos($parr);
        $this->ajaxReturn($result);
    }

}