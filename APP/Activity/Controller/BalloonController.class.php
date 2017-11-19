<?php

namespace Activity\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 热气球模块
 */
class BalloonController extends BaseController {
	
	/*首页*/
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->$returnurl = I('$returnurl');
    	
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Airbox','Newact')->AddActjoin($parr);
        $this->joinaid = $result['data']['joinaid'];
        
        $this->acttype = $result['data']['activitytype'];
    	/*获取宝箱气球【商品】奖项列表*/
    	$parrg['ucode'] = $this->ucode;
    	$parrg['joinaid'] = $this->joinaid;
        $resgood = IGD('Airbox','Newact')->GetGoodsList($parrg);
        $this->goodinfo = $resgood['data'];

		/*获取宝箱气球【卡劵】奖项列表*/ 
    	$parrc['ucode'] = $this->ucode;
    	$parrc['joinaid'] = $this->joinaid;
        $rescoupon = IGD('Airbox','Newact')->GetCouponList($parrc);
        $this->kqinfo = $rescoupon['data'];
        
		/*获取宝箱气球【红包】奖项列表*/ 
    	$parrr['ucode'] = $this->ucode;
    	$parrr['joinaid'] = $this->joinaid;
        $resred = IGD('Airbox','Newact')->GetRedsList($parrr);
        $this->redinfo = $resred['data'];
    	    	    	
    	/*获取宝箱气球【祝福语】奖项*/
    	$parrb['ucode'] = $this->ucode;
    	$parrb['joinaid'] = $this->joinaid;
        $resbless = IGD('Airbox','Newact')->GetSpeak($parrb);
        $this->beinfo = $resbless['data'];
      	//$this->ajaxReturn($resbless); 
      	
        IGD('Airbox','Newact')->CheckUserActivity($parrb);
    	$this->display();
    }
        

    /*商品详情*/
    public function pdetail(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');
        $this->display();
    }

    /*商品选择*/
    public function pselect(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');
        $this->display();
    }
    /*卡券详情*/
    public function cdetail(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');
        $this->display();
    }

    /*卡券选择*/
    public function cselect(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');
        $this->display();
    }
    /*红包详情*/
    public function rdetail(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');
        $this->display();
    }

    /*红包选择*/
    public function rselect(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');
        $this->display();
    }


    /*创建祝福语*/
    public function cblessing(){
        $this->joinaid = I('joinaid');
        $this->ucode = session('USER.ucode');
        
        $parrb['ucode'] = $this->ucode;
    	$parrb['joinaid'] = $this->joinaid;
        $resbless = IGD('Airbox','Newact')->GetSpeak($parrb);
        $this->beinfo = $resbless['data'];
        //$this->ajaxReturn($resbless); 
        $this->display();
    }

    /**
	 *  创建宝箱气球商品 选择产品列表
	 *  @param joinaid,ucode,pagesize,pageindex
	 */
	public function MyGoodsList(){
        $parr['ucode'] = session('USER.ucode');
        $parr['joinaid'] = I('joinaid');       
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->MyGoodsList($parr);
        $this->ajaxReturn($result);

	}
   
    /**
	 * 宝箱气球商品添加提交
	 * @param ucode,joinaid,pinfo(pcode,name,pimg,price,num,actnum,freeprice,starttime,endtime)
	 */
	function GoodsAddSubmit(){
		$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        
		$parr['ucode'] = session('USER.ucode');
		$parr['joinaid'] = $data['joinaid'];
		$pinfo = explode("|", $data['pinfo']);
		$parr['pinfo'][0]["pcode"] = $pinfo[0];
		$parr['pinfo'][0]["name"] = $pinfo[1];
		$parr['pinfo'][0]["pimg"] = $pinfo[2];
		$parr['pinfo'][0]["price"] = $pinfo[3];
		$parr['pinfo'][0]["num"] = $pinfo[4];
		$parr['pinfo'][0]["actnum"] = $pinfo[5];
		$parr['pinfo'][0]["freeprice"] = $pinfo[6];
		$parr['pinfo'][0]["starttime"] = $pinfo[7];
		$parr['pinfo'][0]["endtime"] = $pinfo[8];
		
        $result = IGD('Airbox','Newact')->GoodsAddSubmit($parr);
        $this->ajaxReturn($result);
	}
    
    /**
	 *  创建宝箱气球卡券 选择卡券数据列表
	 *  @param joinaid,ucode,pagesize,pageindex
	 */
	public function MyCoupondList(){
        $parr['ucode'] = session('USER.ucode');
        $parr['joinaid'] = I('joinaid');       
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->MyCoupondList($parr);
        $this->ajaxReturn($result);
	}
    
    /**
	 * 宝箱气球卡券添加提交
	 * @param ucode,joinaid,coupondinfo(id,name,actnum,type,money,limit_money,starttime,endtime)
	 */
    public function CoupondAddSubmit(){        
		$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

		$parr['ucode'] = session('USER.ucode');
		$parr['joinaid'] = $data['joinaid'];	
		$coupondinfo = explode("|", $data['coupondinfo']);	
        $parr['coupondinfo'][0]['id'] = $coupondinfo[0];        
        $parr['coupondinfo'][0]['name'] = $coupondinfo[1];
        $parr['coupondinfo'][0]['actnum'] = $coupondinfo[2];        
        $parr['coupondinfo'][0]['type'] = $coupondinfo[3];
        $parr['coupondinfo'][0]['money'] = $coupondinfo[4];
        $parr['coupondinfo'][0]['limit_money'] = $coupondinfo[5];
        $parr['coupondinfo'][0]['starttime'] = $coupondinfo[6];
        $parr['coupondinfo'][0]['endtime'] = $coupondinfo[7];  
        
        $result = IGD('Airbox','Newact')->CoupondAddSubmit($parr);
        $this->ajaxReturn($result);
    }
    
	/**
	 *  创建宝箱气球红包 选择红包
	 *  @param joinaid,ucode,pagesize,pageindex
	 */
	public function MyRedList(){
        $parr['ucode'] = session('USER.ucode');
        $parr['joinaid'] = I('joinaid');       
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->MyRedList($parr);
        $this->ajaxReturn($result);
	}    
	
	/**
	 * 宝箱气球红包添加提交
	 * @param ucode,joinaid,redinfo(id,name,actnum,type,money,remain_money)
	 */
	public function RedAddSubmit(){
		      
		$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

		$parr['ucode'] = session('USER.ucode');
		$parr['joinaid'] = $data['joinaid'];	
		$redinfo = explode("|", $data['redinfo']);	
        $parr['redinfo'][0]['id'] = $redinfo[0];        
        $parr['redinfo'][0]['name'] = $redinfo[1];
        $parr['redinfo'][0]['actnum'] = $redinfo[2];        
        $parr['redinfo'][0]['type'] = $redinfo[3];
        $parr['redinfo'][0]['money'] = $redinfo[4];
        $parr['redinfo'][0]['remain_money'] = $redinfo[5];
        
        $result = IGD('Airbox','Newact')->RedAddSubmit($parr);
        $this->ajaxReturn($result);
	}
    
    /**
	 * 宝箱气球空奖祝福语编辑提交
	 * @param speakid,ucode,blessing
	 */
	public function SpeakEditSubmit(){
        $parr['ucode'] = session('USER.ucode');
        $parr['speakid'] = I('speakid');  
        $parr['blessing'] = I('blessing');      
        $result = IGD('Airbox','Newact')->SpeakEditSubmit($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 获取宝箱气球商品奖项详情
	 * @param pageindex,pagesize,joinaid,ucode
	 */
	public function GetGoodsDetails(){
        $parr['ucode'] = session('USER.ucode');
        $parr['joinaid'] = I('joinaid');       
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->GetGoodsDetails($parr);
        $this->ajaxReturn($result);
	}
	/**
	 * 宝箱气球商品编辑提交
	 * @param goodid,ucode,num,freeprice,starttime,endtime
	 */
	public function GoodEditSubmit(){
        $parr['ucode'] = session('USER.ucode');
        $parr['goodid'] = I('goodid');       
        $parr['num'] = I('num');
        $parr['freeprice'] = I('freeprice');
        $parr['starttime'] = I('starttime');
        $parr['endtime'] = I('endtime');    
        $result = IGD('Airbox','Newact')->GoodEditSubmit($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 宝箱气球商品撤回
	 * @param goodid,ucode
	 */
	public function GooddDelete(){		
        $parr["ucode"] = session('USER.ucode');
        $parr["goodid"] = I('goodid');
        $result = IGD('Airbox','Newact')->GooddDelete($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 获取宝箱气球卡券奖项详情
	 * @param pageindex,pagesize,joinaid,ucode
	 */
	public function GetCouponDetails(){
        $parr['ucode'] = session('USER.ucode');
        $parr['joinaid'] = I('joinaid');       
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->GetCouponDetails($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 宝箱气球卡券编辑提交
	 * @param couponid,ucode,num
	 */
	public function CouponEditSubmit(){
        $parr['ucode'] = session('USER.ucode');
        $parr['couponid'] = I('couponid');       
        $parr['num'] = I('actnum');   
        $result = IGD('Airbox','Newact')->CouponEditSubmit($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 宝箱气球卡券撤回
	 * @param couponid,ucode
	 */
	public function CoupondDelete(){	
        $parr["ucode"] = session('USER.ucode');
        $parr["couponid"] = I('couponid');
        $result = IGD('Airbox','Newact')->CoupondDelete($parr);
        $this->ajaxReturn($result);
	}
	
	
	/**
	 * 获取宝箱气球红包奖项详情
	 * @param pageindex,pagesize,joinaid,ucode
	 */
	public function GetRedsDetails(){
        $parr['ucode'] = session('USER.ucode');
        $parr['joinaid'] = I('joinaid');       
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->GetRedsDetails($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 宝箱气球红包编辑提交
	 * @param redid,ucode,num
	 */
	public function RedEditSubmit(){
        $parr['ucode'] = session('USER.ucode');
        $parr['redid'] = I('redid');       
        $parr['num'] = I('num');   
        $result = IGD('Airbox','Newact')->RedEditSubmit($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 宝箱气球红包撤回
	 * @param redid,ucode
	 */
	public function RedDelete(){	
        $parr["ucode"] = session('USER.ucode');
        $parr["redid"] = I('redid');
        $result = IGD('Airbox','Newact')->RedDelete($parr);
        $this->ajaxReturn($result);
	}
	
	    
	/*宝藏记录*/
    public function record(){
    	$this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');

        /*商品统计信息*/
        $parrp['ucode'] = $this->ucode;
    	$parrp['joinaid'] = $this->joinaid;
        $resultpro = IGD('Airbox','Newact')->GoodsLogTj($parrp);
        $this->protj = $resultpro['data'];
        /*卡券统计信息*/
        $parrc['ucode'] = $this->ucode;
    	$parrc['joinaid'] = $this->joinaid;
        $resultc = IGD('Airbox','Newact')->CoupondLogTj($parrc);
        $this->coupontj = $resultc['data'];  
        /*红包统计信息*/
        $parrr['ucode'] = $this->ucode;
    	$parrr['joinaid'] = $this->joinaid;
        $resultr = IGD('Airbox','Newact')->RedLogTj($parrr);
        $this->redtj = $resultr['data']; 
        
        for ($i=0; $i < 5; $i++) {
            $datearr[$i]['name'] = date('m月',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['date'] = date('Y-m',strtotime('-'.($i+1).' months',time()));
        }

        $this->monthn = date('m月');/*当前月*/
        $this->monthdata = date('Y-m');/*当前月*/
        $this->datearr = $datearr;
                       
    	$this->display();
    }

    /*宝藏记录   商品*/
     public function precord(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');

        /*商品统计信息*/
        $parrp['ucode'] = $this->ucode;
        $parrp['joinaid'] = $this->joinaid;
        $resultpro = IGD('Airbox','Newact')->GoodsLogTj($parrp);
        $this->protj = $resultpro['data'];

        $this->display();
    }


    /*宝藏记录 卡券*/
    public function crecord(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');

        /*卡券统计信息*/
        $parrc['ucode'] = $this->ucode;
        $parrc['joinaid'] = $this->joinaid;
        $resultc = IGD('Airbox','Newact')->CoupondLogTj($parrc);
        $this->coupontj = $resultc['data'];

        $this->display();
    }


    /*宝藏记录  红包*/
    public function rrecord(){
        $this->ucode = session('USER.ucode');
        $this->joinaid = I('joinaid');

        /*红包统计信息*/
        $parrr['ucode'] = $this->ucode;
        $parrr['joinaid'] = $this->joinaid;
        $resultr = IGD('Airbox','Newact')->RedLogTj($parrr);
        $this->redtj = $resultr['data'];

        $this->display();
    }

    //气球  商品审核状态
    public function verify() {         
        $this->actid = I('actid');   
        $parr['pid'] = $this->actid;
        $result = IGD('Airbox','Newact')->GetPrizeDetail($parr);
        $this->assign('data',$result['data']);
        
        $this->display();
    }
	
	/**
	 * 确认投放
	 */
	public function ChangePrize(){      
        $parr['pid'] = I('actid');
        $result = IGD('Airbox','Newact')->ChangePrize($parr);
        $this->ajaxReturn($result);
	}

	/**
	 * 宝箱气球商品用户参与记录
	 * @param ucode,joinaid,pageindex
	 */
	public function GoodsLog(){
        $parr["ucode"] = session('USER.ucode');
        $parr["joinaid"] = I('joinaid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->GoodsLog($parr);
        $this->ajaxReturn($result);
	}
	
	/**
	 * 宝箱气球卡券用户参与记录
	 * @param ucode,joinaid,pageindex
	 */
	public function CoupondProLog(){
        $parr["ucode"] = session('USER.ucode');
        $parr["joinaid"] = I('joinaid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Airbox','Newact')->CoupondProLog($parr);
        $this->ajaxReturn($result);
	}

	/**
	 * 宝箱气球红包用户参与记录
	 * @param ucode,joinaid,month(格式：2017-05)
	 */
	public function CoupondLog(){
        $parr["ucode"] = session('USER.ucode');
        $parr["joinaid"] = I('joinaid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['day'] = I('month');
        $result = IGD('Airbox','Newact')->CoupondLog($parr);
        $this->ajaxReturn($result);
	}

    //线下兑换
    public function OfflineExchange()
    {
        $parr['sid'] = I('sid');
        $parr['status'] = 2;
        $result = IGD('Exchange', 'Newact')->OfflineExchange($parr);
        $this->ajaxReturn($result);
    }


}