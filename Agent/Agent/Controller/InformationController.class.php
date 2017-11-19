<?php
namespace Agent\Controller;
use Think\Controller;
/**
 *  公告控制器
 */
class InformationController extends BaseController{

    // 公告首页
	public function index()
	{
		$ucode = session('_AGENT_UCODE');
		$join = "left join t_check_infolog as b on a.c_id=b.c_infoid and b.c_ucode='$ucode'";		
        $whereinfo[] = array("a.c_ucode='' or a.c_ucode is null or a.c_ucode='$ucode'");
        $whereinfo[] = array("a.c_type=1 or a.c_type=3 or a.c_type=4");
		$order = 'case when ifnull(b.c_infoid,"")="" then 0 else 1 end asc,a.c_addtime desc';
		$field = 'a.*,b.c_infoid';
		$count = M('Check_info as a')->join($join)->where($whereinfo)->count();
		$page = getpage($count,20);
		$limit = $page->firstRow.','.$page->listRows; 
		$data = M('Check_info as a')->join($join)->where($whereinfo)->order($order)->limit($limit)->field($field)->select();
		$this->assign('data',$data); 
		$this->page = $page->show();
		$this->display();
	}	
 
 	// 公告详情页
 	public function detail()
 	{
 		$where['c_id'] = I('Id'); 		
 		$vo = M('Check_info')->where($where)->find();		
 		$this->assign('vo',$vo);
 		$this->display();
 	}

 	// 改变消息状态
	public function readinfo()
	{ 		
		$parr['ucode'] = session('_AGENT_UCODE');
		$parr['cid'] = I('Id');
		$result = D('Infomation','Service')->ReadMsg($parr);
		$this->ajaxReturn($result);
	}
	
		
	/*完善资料提醒*/
	public function ReadInfostatu()
	{
		$parr['ucode'] = session('_AGENT_UCODE');
		$result = D('Common','Service')->ReadInfostatu($parr);
		$this->ajaxReturn($result);
	}

	/*获取首页未读状态标志*/
	public function GetStatuMessage()
	{
		$parr['ucode'] = session('_AGENT_UCODE');
		$result = D('Common','Service')->GetStatuMessage($parr);
		$this->ajaxReturn($result);		
		// $publicnum = $result['data']['publicnum'];
		// $checknum = $result['data']['checknum'];
	}
}