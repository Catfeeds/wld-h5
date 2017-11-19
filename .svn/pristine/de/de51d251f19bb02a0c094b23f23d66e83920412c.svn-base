<?php
namespace Shop\Controller;
use Think\Controller;
/**
 *  加盟店管理
 */
class LeagshopController extends BaseController{	
	
	// 首页
    public function index()
    {        
    	$this->display();
    }

    //获取加盟店列表
    public function LeagueList()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('League','Store')->LeagueList($parr);
        $this->ajaxReturn($result);
    }

    //加盟店详情
    public function detail()
    {
        $parr['fid'] = I('fid');
        $result = IGD('League','Store')->LeagueInfo($parr);

        $this->data = $result['data'];
        $this->show();
    }

    public function OptionLeague()
    {
        $parr['fid'] = I('fid');
        $parr['status'] = I('status');
        $result = IGD('League','Store')->OptionLeague($parr);
        $this->ajaxReturn($result);
    }

	/*邀请加盟*/
    public function inviteshop(){
    	$this->display();
    }

    //邀请加盟接口
    public function Confirmsubmit()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('_SHOP_UCODE');
        $parr['pucode'] = $data['pucode'];
        $parr['shopcode'] = $data['shopcode'];
        $result = IGD('League','Store')->Confirmsubmit($parr);
        $this->ajaxReturn($result);
    }

    //获取用户信息
    public function UserInfo()
    {
        $phone = I('phone');
        $result = IGD('League','Store')->UserInfo($phone);
        $this->ajaxReturn($result);
    }
  
}