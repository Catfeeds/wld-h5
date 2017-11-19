<?php

namespace Activity\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 每日签到
 */
class SigninController extends BaseController {
	
	/*首页*/
    public function index(){    	
		
		$parr['ucode'] = session('USER.ucode');
        $result =IGD('Signup', 'Activity')->GetSignInfo($parr);
        $count = $result['data']['count'];
        //dump($count);die;
    	for ($i=0; $i < 7; $i++) { 
    		$signarr[$i]['t'] = $i + 1;
    		$signarr[$i]['state'] = 0;
    		if ($i < $count) {
    			$signarr[$i]['state'] = 1;
    		}
    	}
    	$this->signarr = $signarr;    	
    			
		$this->signed = $result['data']['sign'];
	
    	$this->display();
    }


	//获取用户签到信息
    public function GetUserSignInfo(){        
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Signup', 'Activity')->GetSignInfo($parr);
        $this->ajaxReturn($result);
    }

    //用户签到
    public function Signup(){
        $parr['ucode'] = session('USER.ucode');        
        $result = IGD('Signup', 'Activity')->SignUp($parr);
        $this->ajaxReturn($result);
    }
    
    /*用户签到记录*/
   public function GetSignRecord(){
    	$parr['pagesize'] = 10;
        $parr['pageindex'] = I('pageindex');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Signup', 'Activity')->GetSignRecord($parr);
        $this->ajaxReturn($result);
   }


}