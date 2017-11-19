<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  代理商管理
 */
class AgentntrolController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

   	
	public function index()
	{
		$ucode = session('_ADMIN_UCODE');
		$join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
		$where['a.c_acode'] = $ucode;
		$where['a.c_isagent'] = 2;
		$where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
		$field = 'a.c_isagent,a.c_headimg,b.*';
		$order = 'b.c_checked=0,b.c_updatetime desc,b.c_addtime desc';
		$count = M('Users as a')->join($join)->where($where)->count();
		$page = getpage($count,20);
		$limit = $page->firstRow.','.$page->listRows; 
		$data = M('Users as a')->join($join)->where($where)->order($order)->limit($limit)->field($field)->select();
		$this->assign('data',$data); 
		$this->page = $page->show();
		$this->display();
	}

	public function details()
	{
		$parr['infoid'] = I('Id');
		$result = D('Agent','Service')->GetShopInfo($parr);
		$this->assign('data',$result['data']);
		$this->display();
	}
}