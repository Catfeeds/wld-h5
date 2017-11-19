<?php
namespace Agent\Controller;
use Think\Controller;
/**
 *  激活串码管理
 */
class CodecheckController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

    //微商列表
	public function index()
	{
		$parr['ucode'] = session('_AGENT_UCODE');
		//$result = D('Codecheck','Service')->GetCodenum($parr);
       // $result = IGD('Codecheck', 'Agent')->GetCodenum($parr);


        $ac_total =M('Check_codelist')->where(array('c_acode'=>$parr['ucode'],'c_state'=>1))->count();
        $not_ac_total =M('Check_codelist')->where(array('c_acode'=>$parr['ucode'],'c_state'=>2))->count();
        $result['data']['active_total'] =$ac_total==null?0:$ac_total;
        $result['data']['not_active_total'] =$not_ac_total==null?0:$not_ac_total;
        //获取待审核个数
        $result['data']['unshen'] =IGD('Codecheck', 'Agent')->GetUnshencount($parr);

		$this->info = $result['data'];

          $num = IGD('Codecheck','Agent')->GetDiff($parr);
          $total =$num['data']['c_codenum'] -$num['data']['c_usenum'];
          if($total>0){
              $this->codePolish($total);
          }
        $this->statu = I('statu');
		$this->display();
	}

    //补齐 code串码

    public function codePolish($total){

        $parr['ucode'] = session('_AGENT_UCODE');
        $result = IGD('Codecheck','Agent')->GetStatuMessage($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        $parr['num'] = $total;
        $result =IGD('Codecheck','Agent')->ApplyCode($parr);
        if($result){
            for($i =0;$i<$total;$i++){
                IGD('Codecheck','Agent')->GrantCode($parr);
            }
        }else{
            return false;
        }


    }


    //市代申请串码
	//ucode,num
	public function ApplyCode(){
		$parr['ucode'] = session('_AGENT_UCODE');
		//$result = D('Common','Service')->GetStatuMessage($parr);
        $result = IGD('Codecheck','Agent')->GetStatuMessage($parr);
		if ($result['code'] != 0) {
			$this->ajaxReturn($result);
		}

		$parr['num'] = I('num');
		//$result = D('Codecheck','Service')->ApplyCode($parr);
        $result =IGD('Codecheck','Agent')->ApplyCode($parr);
        if($result){
            for($i =0;$i<$parr['num'];$i++){
                IGD('Codecheck','Agent')->GrantCode($parr);
            }
        }


		$this->ajaxReturn($result);
	}

	//发放激活串码
	public function GrantCode()
	{
		$parr['ucode'] = session('_AGENT_UCODE');
		//$result = D('Codecheck','Service')->GrantCode($parr);
        $result =IGD('Codecheck','Agent')->GrantCode($parr);
		$this->ajaxReturn($result);
	}

	//查询信息列表
	public function GetCodeInfoList()
	{
		$statu = I('statu');
		if ($statu == 0) {
			$type = 1;
		}elseif($statu ==1) {
			$type = 2;
		}

		$parr['type'] = $type;
		$parr['pageindex'] = I('pageindex');
		$parr['pagesize'] = 20;
		$parr['ucode'] = session('_AGENT_UCODE');
		//$result = D('Codecheck','Service')->GetCodeInfoList($parr);
        if($statu ==2){
            $result =IGD('Codecheck','Agent')->GetCodeUnshenList($parr);
        }else{
            $result =IGD('Codecheck','Agent')->GetCodeInfoList($parr);
        }
		$this->ajaxReturn($result);
	}


    public function test(){

        $parr['type'] = 2;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $parr['ucode'] = session('_AGENT_UCODE');
        $result =IGD('Codecheck','Agent')->GetCodeUnshenList($parr);

        var_dump($result['data']);

    }

	//串码列表
    public function GetNumList(){

        $type = I('type');
        $parr['type'] = $type;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['ucode'] = session('_AGENT_UCODE');

        $result =IGD('Codecheck','Agent')->GetCodeInfoList($parr);
        //$total = IGD('Codecheck', 'Agent')->GetCodenum($parr);  //获取已激活个数
        $total =M('Check_codelist')->where(array('c_acode'=>$parr['ucode'],'c_state'=>1))->count();
        $result['data']['jihuo_total'] =$total;

        $this->ajaxReturn($result);
    }

}