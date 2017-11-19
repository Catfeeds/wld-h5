<?php
namespace Shop\Controller;
use Think\Controller;
class MemberController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

   	//我的会员
	public function index()
	{
		$uwhere['c_ucode'] = session('_SHOP_UCODE');
		$this->userinfo = M('Users')->where($uwhere)->find();

		$join = 'inner join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'b.c_phone,b.c_headimg,b.c_addtime,b.c_shop,b.c_nickname';
        $where['a.c_pcode'] = session('_SHOP_UCODE');
		
		$count = M('Users_tuijian as a')->join($join)->where($where)->count();
		$page = getpage($count,20);
		$limit = $page->firstRow.','.$page->listRows; 
		$data = M('Users_tuijian as a')->join($join)->where($where)->field($field)->limit($limit)->select();
		foreach ($data as $key => $value) {
            if (empty($value['c_headimg'])) {
                $data[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $data[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
        }
		$this->assign('data',$data); 
		$this->page = $page->show();

		$this->display();
	}

	
}