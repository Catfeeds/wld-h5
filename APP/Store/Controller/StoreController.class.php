<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 	商家信息管理
 */
class StoreController extends BaseController {
	// 首页 
    public function index()
    {
        $parr['ucode'] = session('USER.ucode');
        //查询用户选择模板信息
        $result = IGD('Store','Store')->GetShopTpl($parr);
        $this->tplid = $result['data']['tplid'];
        $this->compareid = $result['data']['compareid'];
        
        $ucode = session('USER.ucode');
        $userinfo = IGD('Login', 'Login')->GetUserByCode($ucode);        
        $this->storeinfo = $userinfo['data'];
        
    	$this->show();
    }
    
    /*设置线下店铺信息*/
    public function SetStoreInfo(){
        //获取店铺信息
        $parr['storeid'] = I('storeid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $storeinfo = $result['data'];

        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

    	$param['storeid'] = $data['storeid'];
        $param['ucode'] = session('USER.ucode');
        $param['name'] = $data['c_name'];
        $desc = $data['c_desc'];
        $qian = array(" ","　","\t","\n","\r");
        $hou = array("","","","","");
        $param['desc'] = str_replace($qian,$hou,$desc);
        $param['provice'] = $data['province'];
        $param['city'] = $data['city'];
        $param['district'] = $data['region'];
        $param['address'] = $data['address1'];
        $param['longitude'] = $data['lng'];
        $param['latitude'] = $data['lat'];
        $param['remind'] = htmlspecialchars($data['c_remind']);
        $param['opentime'] = $data['c_opentime'];
        $param['sourcearr'] = explode('|',substr($data['sourcearr'], 0));
        if (empty($param['name']) || empty($param['desc']) || empty($param['address']) || empty($param['provice'])
            || empty($param['city']) || empty($param['district']) || empty($param['opentime']) || empty($param['sourcearr'])) {
            $this->ajaxReturn(Message(3000,'请完善店铺资料信息！'));
        }

        //循环所有图片
        foreach (array_keys($data) as $k => $v) {
            if (strpos($v,'sub_img')  !== false) {
                if (!empty($data[$v])) {
                    $imgstr = str_replace(GetHost().'/', '', $data[$v]);
                    if (in_array($imgstr, $storeinfo['imglist'])) {
                        $imglist[] = $imgstr;
                    } else {
                        $imglist[] = copyFileToDIr($imgstr,'offstore')['data'];
                    }
                }
            }
        }

        if (count($imglist) <= 0) {
            $this->ajaxReturn(Message(3002,'图片上传失败！'));
        }

        $param['imglist'] = $imglist;

        $result = IGD('Store','Store')->AddStoreInfo($param);
        $this->ajaxReturn(Message(0,'编辑资料成功！'));
    }
    
    /*设置线上店铺信息*/
    function SetOnlineStore(){
        //获取店铺信息
        $parr['storeid'] = I('storeid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $storeinfo = $result['data'];
	   
        // 接收post数据
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        $param['storeid'] = $data['storeid'];
        $param['ucode'] = session('USER.ucode');
        $param['name'] = $data['c_name'];
        $desc = $data['c_desc'];
        $qian = array(" ","　","\t","\n","\r");
        $hou = array("","","","","");
        $param['desc'] = str_replace($qian,$hou,$desc);
        if (empty($param['name']) || empty($param['desc'])) {
            $this->ajaxReturn(Message(3000,'请完善店铺资料信息！'));
        }

        //循环所有图片
        foreach (array_keys($data) as $k => $v) {
            if (strpos($v,'online_sub_img')  !== false) {
                if (!empty($data[$v])) {
                    $imgstr = str_replace(GetHost().'/', '', $data[$v]);
                    if (in_array($imgstr, $storeinfo['imglist'])) {
                        $imglist[] = $imgstr;
                    } else {
                        $imglist[] = copyFileToDIr($imgstr,'offstore')['data'];
                    }
                }
            }
        }

        if (count($imglist) <= 0) {
            $this->ajaxReturn(Message(3002,'图片上传失败！'));
        }

        $param['imglist'] = $imglist;
        $result = IGD('Store','Store')->AddStoreInfoline($param);
        $this->ajaxReturn(Message(0,'编辑资料成功！'));
    }
        
    /**
     * 添加线下店铺信息
     */
    public function add()
    {
        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;
        
        /*店铺信息*/
        $parr['storeid'] = I('storeid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $storeinfo = $result['data'];
        $this->storeinfo = $storeinfo;

        /*获取服务项目*/
        $parr['storeid'] = $storeinfo['c_id'];
        $result = IGD('Store','Store')->GetServiceList($parr);
        $this->service = $result['data'];
        $this->subarr = $storeinfo['imglist'];

        $this->display();
    }

	/**
     * 添加线下店铺信息
     */
    public function onlineadd()
    {
        /*店铺信息*/
        $parr['storeid'] = I('storeid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $storeinfo = $result['data'];
        $this->storeinfo = $storeinfo;
    	$this->display();
	}

    /**
     * 店铺详情
     *
     */
    public function detail()
    {
        /*获取店铺详情*/
      
        $parr['ucode'] = session('USER.ucode');
        $parr['storeid'] = I("storeid");
        $parr['acode'] = I("acode");
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->datainfo = $result['data'];
        $this->imglist = $result['data']['imglist'];
        $this->service = $result['data']['service'];
        //$this->ajaxReturn($result);
        $this->preview = I("preview");

        /*商品评论*/
        $parrc['pageindex'] = 1;
        $parrc['pagesize'] = 10;
        $parrc['acode'] = $result['data']['c_ucode'];
        $comdata = IGD('Store','Store')->GetCommentList($parrc);
        $this->commentcount = $comdata['data']['dataCount'];
        $this->comment = $comdata['data']['list'][0];

        /*获取店铺购物车商品数量*/
        // $parrm['ucode'] = session('USER.ucode');
        // $parrm['acode'] = $this->datainfo['c_ucode'];
        // $mycart = D('Storecar','Service');
        // $result = $mycart->GetCount($parrm);
        // $this->datanum = $result['data']['count'];

        $this->ucode = session('USER.ucode');

        $this->show();
    }

    //店铺预览页面
    public function preview()
    {
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;

        //获取店铺模板
        $this->ucode = session('USER.ucode');
        $parr['ucode'] = $fromucode;
        $result = IGD('Store','Store')->GetShopTpl($parr);
        $this->tplid = $result['data']['tplid'];

        //获取预览模板内容
        $parr['tplid'] = $this->tplid;
        $parr['isprew'] = 1;
        $result = IGD('Store','Store')->GetShopTplContent($parr);
        foreach ($result['data'] as $key => $value) {
           if ($value['c_types'] == 1) {   //头部
                $this->topimg[] = $value['c_img'];
            } else if ($value['c_types'] == 2) {  //banner
                $this->bannerimg[] = $value['c_img'];
            } else if ($value['c_types'] == 3) {    //卡劵
                $this->cardimg[] = $value['c_img'];
            }
        }

        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parr['acode'] = $this->issue_ucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->storeinfo = $result['data'];
        $this->imglist = $result['data']['imglist'];

        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->storeinfo['shareurl'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->storeinfo['shareimg'];    
        $weixinshare["c_sharetitle"] = $this->storeinfo['sharetit'];
        $weixinshare["c_discript"] = $this->storeinfo['sharedesc'];
        $this->assign('weixinshare',$weixinshare);  

        $this->show();
    }

    //评论专区页面
    public function comment()
    {
        $this->ucode = session('USER.ucode');
        $this->statu = I('statu');
        $this->show();
    }

	/*评论专区*/
	public function GetAllScore(){
	    $parr['ucode'] = session('USER.ucode');
	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;
	    $parr['useraction'] = I('useraction');

	    $result = IGD('Productscore','Store')->GetAllScore($parr);
	    $this->ajaxReturn($result);		
	} 
	
	/*评论详情*/
	public function comdetail(){
		$this->ucode = session('USER.ucode');
		
		/*评论详情*/
	    $parr['ucode'] = session('USER.ucode');
	    $parr['sid'] = I('scoreid');
	    $result = IGD('Productscore','Store')->GetScoreInfo($parr);	    
	    $this->datainfo = $result['data'];
	    
	    $this->show();
	}
	

	/**
     *  点赞、点不赞
     *  @param handle 0-点赞，1-点不赞,scoreid
     */
    public function ScoreLike() {
    	$parr['ucode'] = session('USER.ucode');
    	$parr['scoreid'] = I('scoreid');
    	$parr['handle'] = I('handle');

    	$result = IGD('Productscore','Store')->ScoreLike($parr);
    	$this->ajaxReturn($result);
    }

    /**
    * 添加评论
    * @param  openid,scoreid,content,bid
    */
    public function AddComment(){
        $parr['ucode'] =  session('USER.ucode');
        $parr['scoreid'] = I('scoreid');
        $parr['content'] = I('content');
        $parr['bid'] = I('bid');
       
        $result = IGD('Productscore','Store')->CommentScore($parr);
        $this->ajaxReturn($result);
    }

    /**
	 *  删除评论及子评论
	 *  @param openid,cid
	 *
	 */
    public function DeleteComment(){
        $parr['ucode'] =  session('USER.ucode');
        $parr['cid'] = I('cid');
        
        $result = IGD('Productscore','Store')->DeleteComment($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 店铺相册
     */
    public function photo()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['storeid'] = I("storeid");
        $result = IGD('Store','Store')->GetStoreInfo($parr);

        $this->datainfo = $result['data'];
        $this->imglist = $result['data']['imglist'];
        $this->show();
    }

    /**
     * 获取店铺信息
     */
    public function StoreInfo()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['storeid'] = I("storeid");
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 获取商品列表
     */
    public function GetProduceList()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['gettype'] = I('gettype');
        $parr['acode'] = I('acode');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }

    //店铺购物车商品数量
    public function Getprocount() {
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');

        $mycart = IGD('Storecar', 'User');
        $result = $mycart->Getprocount($parr);
        $this->ajaxReturn($result);
    }
    

    //选择模板页面--旧版
    public function selecttmp()
    {
        $parr['ucode'] = session('USER.ucode');       
        $result = IGD('Store','Store')->TemplateList($parr); 
               
        $this->datalist = $result['data']['list'];
        $this->tplid = $result['data']['tplid']; 
        
        $this->assign('emptyval','<span class="baoqian" style="display:block;">暂时没有相关数据</span>');
        $this->show();
    }

    //选择模板页面--新版
	public function seltemp()
    {
        $parr['ucode'] = session('USER.ucode');       
        $result = IGD('Store','Store')->TemplateList($parr);
        $this->datalist = $result['data']['list'];
        /*应用的模板id*/
        $this->compareid = $result['data']['compareid'];
        /*生成的模板id*/
        $this->tplid = $result['data']['tplid'];

        $this->assign('emptyval','<span class="baoqian" style="display:block;">暂时没有相关数据</span>');
        $this->show();
    }
	
    //选择店铺模板
    public function CheckShopTpl()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['tplid'] = I('tplid');
        $parr['app_version'] = '3.0.5';
        $result = IGD('Store','Store')->CheckShopTpl($parr);
        $this->ajaxReturn($result);
    }

    /*应用模板*/
    public function ApplyModel(){
        $ucode = session('USER.ucode');
        $tplid = I('tplid');
        $parr['ucode'] = $ucode;
        $parr['tplid'] = $tplid;
        $parr['app_version'] = '3.0.5';
        $result = IGD('Store','Store')->ApplyModel($parr);
        $this->ajaxReturn($result);
    }

    //预览模板
    public function viewtemp(){
        $fromucode = I('fromucode');
        if (empty($fromucode)) {
            $fromucode = session('USER.ucode');
        }
        $this->issue_ucode = $fromucode;
        $this->ucode = session('USER.ucode');
        
        //获取店铺头部信息
        $params['perucode'] = $this->issue_ucode;
        $params['ucode'] = session('USER.ucode');
        
        //卡券信息
        $result = IGD('Coupon','Newact')->GetCouponInfo($params);
        $this->datacoupon = $result['data'];

        $result = IGD('Resource','Trade')->PersonalShop($params);
        $this->data = $result['data'];

        //获取店铺信息
        $parr['acode'] = $this->issue_ucode;
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $this->storeinfo = $result['data'];
        //查询用户选择模板信息
        $result = IGD('Store','Store')->GetShopTpl($parr);
        $this->compareid = $result['data']['compareid'];/*已应用的模板*/
        $this->tplid = $result['data']['tplid'];/*生成的模板*/

        $parr['tplid'] = $this->tplid;
        $parr['app_version'] = '3.0.5';
        $parr['isprew'] = 2;
        $result = IGD('Store','Store')->GetShopTplContent($parr);

        foreach ($result['data'] as $key => $value) {
            if ($value['c_types'] == 1) {   //头部
                $topimg[] = $value['c_img'];
            } else if ($value['c_types'] == 2) {  //banner
                $bannerimg[] = $value['c_img'];
            } else if ($value['c_types'] == 3) {    //卡劵
                $cardimg[] = $value['c_img'];
            }
        }
        $this->topimg = $topimg;
        $this->bannerimg = $bannerimg;
        $this->cardimg = $cardimg;

        $this->display('tempview'.$this->compareid);
    }

	/*发放优惠券*/
	public function couponlist(){
        $result = IGD('Index','Newact')->GetPlatActInfo(21,1);
        $this->joinaid = $result['data']['c_id'];
		$this->show();
	}
	
	/*选择优惠券*/
	public function selcoupon(){		
        $this->ucode = session('USER.ucode');        
		$this->show();
	}

    //获取可发放卡劵列表
    public function getCouponList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['type'] = 4;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->CouponList($parr);
        $this->ajaxReturn($result);
    }

    //查询店铺发放卡劵列表
    public function ShopCouponList()
    {
        $parr['acode'] = session('USER.ucode');
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->ShopCouponList($parr);
        $this->ajaxReturn($result);
    }

    //店铺发放卡劵
    public function GrantCoupon()
    {
        $parr['cid'] = I('cid');
        $parr['num'] = I('num');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Coupon','Newact')->GrantCoupon($parr);
        $this->ajaxReturn($result);
    }

    //取消发放卡劵
    public function CancelCouponCard()
    {
        $parr['awid'] = I('awid');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Coupon','Newact')->CancelCouponCard($parr);
        $this->ajaxReturn($result);
    }

	
	/*领取记录*/
	public function getclog(){
		$this->show();
	}

    //获取领取记录
    public function GetReceviLog()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Coupon','Newact')->GetReceviLog($parr);
        $this->ajaxReturn($result);
    }
    
    public function qrcode(){
    	$this->ucode = session('USER.ucode'); 
		$this->show();
    }
	
    //生成商家店铺二维码
    public function shopqrcode(){    	
        $parr['ucode'] = session('USER.ucode');
        $parr['qrcode_type'] = 2;

        $style_arr = array("#000000","#ffaa3c","#5ab9f9","#ff95ca","#5cadad","#f52f46");

        $random_num = I('random_num');

        $parr['bgcolor'] = $style_arr[$random_num];

        $bgcolor = I('bgcolor');
        if(!empty($bgcolor)){
            $parr['bgcolor'] = $bgcolor;
        }
        $Qrcode = IGD('Qrcode', 'Store');
        $result = $Qrcode->StoreQrcode($parr);
        
        $this->ajaxReturn($result);
    }

    //根据商品分类查询商品列表
    public function GetCategoryInfo(){
//        $acode = I('fromucode');
//        if (empty($acode)) {
//            $acode = session('USER.ucode');
//        }
        $parr['acode'] = I('fromucode');
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = I('cateid');
        $parr['pageindex'] = I('pageindex');
        $result = IGD('Store','Store')->GetCategoryInfo($parr);
        $this->ajaxReturn($result);
    }
}
	