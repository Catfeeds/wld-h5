<?php
namespace Home\Controller;
use Think\Controller;
/**
*   加盟店、连锁店管理
*/
class FederationController extends BaseController{
	/**
	*   加盟店管理
	*/
	//加盟店列表
	public function league_list(){
		$db = M('A_federation as a');
		//条件
		$nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['a.c_name'] = array('like', "%{$name}%");
        }
        $status = trim(I('status'));
        if (!empty($status)) {
           $w['a.c_status'] = $status;
        }
       
        $w['a.c_sign'] = 1;//总店
        $w['a.c_type'] = 2;//加盟店        

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg,c.c_category_name';       
        $panrn['join'] = 'left join t_users as u on a.c_ucode=u.c_ucode';
        $panrn['join1'] = 'left join t_category as c on a.c_categoryid=c.c_id';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);	
		
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->activity = $activitys;
		$this->display();
	}

	//加盟店添加
	public function league_add(){
		$this->action = 'league_add';

		if(IS_POST){
			$db = M('A_federation');

			if (empty($_POST['ucode'])) {
		    	$this->error("未选择商家");
			}

			$parr['ucode'] = $_POST['ucode'];
    		$parr['itype'] = 2;
    
    		$result = IGD('Identity','Store')->AddIdentity($parr);

    		if($result['code'] != 0){
    			$this->error($result['msg']);
    		}		   
		
	        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Federation/league_list';
          	echo '<script language="javascript">alert("添加成功");</script>';
          	echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
		}
		$this->display();
	}

	//验证表单
	function checkfrom($data){
		if (empty($data['name'])) {
	    	$this->error("统一名称不能为空");
		}
		if (empty($data['shopcode'])) {
	    	$this->error("加盟店编号不能为空");
		}
		if (empty($data['categoryid'])) {
	    	$this->error("未选择加盟产品种类");
		}

		if (!is_int(intval($data['addnum']))) {
		    $this->error("数量必须为整数");
		}

		// if ($data['num'] == '') {
		//     	$this->error("子店数量不能为空");
		// }
		// if ($data['remain_num'] == '') {
	 //    	$this->error("剩余数量不能为空");
		// }
	}

	//加盟店编辑
	public function league_edit(){
		$this->action = 'league_edit';

		$Id = I('Id');
		$w['c_id'] = $Id;
		$this->vo = M('A_federation')->where($w)->find();

		$w1['c_isshow'] = 1;
		$this->category = M('Category')->field('c_id,c_category_name')->where($w1)->select();

		if(IS_POST){
			$db = M('A_federation');

			$this->checkfrom($_POST);

		    $data['c_name'] = $_POST['name'];
		    $data['c_shopcode'] = $_POST['shopcode'];
		    $data['c_categoryid'] = $_POST['categoryid'];
		    $data['c_status'] = $_POST['status'];

		    if ($_POST['addnum'] > 0) {
		    	$data['c_num'] = $_POST['addnum'] + $this->vo['c_num'];
		    	$data['c_remain_num'] = $_POST['addnum'] + $this->vo['c_remain_num'];
		    }

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Federation/league_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display();
	}

	/**
	*   连锁店管理
	*/
	//连锁店列表
	public function chain_list(){
		$db = M('A_federation as a');
		//条件
		$nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['a.c_name'] = array('like', "%{$name}%");
        }
        $status = trim(I('status'));
        if (!empty($status)) {
           $w['a.c_status'] = $status;
        }
       
        $w['a.c_sign'] = 1;//总店
        $w['a.c_type'] = 1;//连锁店        

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg,c.c_category_name';       
        $panrn['join'] = 'left join t_users as u on a.c_ucode=u.c_ucode';
        $panrn['join1'] = 'left join t_category as c on a.c_categoryid=c.c_id';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);	
		
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->activity = $activitys;
		$this->display();
	}

	//连锁店添加
	public function chain_add(){
		$this->action = 'chain_add';

		if(IS_POST){
			$db = M('A_federation');

			if (empty($_POST['ucode'])) {
		    	$this->error("未选择商家");
			}

			$parr['ucode'] = $_POST['ucode'];
    		$parr['itype'] = 1;
    
    		$result = IGD('Identity','Store')->AddIdentity($parr);

    		if($result['code'] != 0){
    			$this->error($result['msg']);
    		}		   
		
	        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Federation/chain_list';
          	echo '<script language="javascript">alert("添加成功");</script>';
          	echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
		}
		$this->display();
	}

	//验证表单
	function checkfrom1($data){
		if (empty($data['name'])) {
	    	$this->error("统一名称不能为空");
		}
		if (empty($data['shopcode'])) {
	    	$this->error("连锁店编号不能为空");
		}
		if (empty($data['categoryid'])) {
	    	$this->error("未选择连锁产品种类");
		}

		if (!is_int(intval($data['addnum']))) {
		    $this->error("数量必须为整数");
		}

		// if ($data['num'] == '') {
		//     	$this->error("子店数量不能为空");
		// }
		// if ($data['remain_num'] == '') {
	 //    	$this->error("剩余数量不能为空");
		// }
	}

	//连锁店编辑
	public function chain_edit(){
		$this->action = 'chain_edit';

		$Id = I('Id');
		$w['c_id'] = $Id;
		$this->vo = M('A_federation')->where($w)->find();

		$w1['c_isshow'] = 1;
		$this->category = M('Category')->field('c_id,c_category_name')->where($w1)->select();

		if(IS_POST){
			$db = M('A_federation');

			$this->checkfrom1($_POST);

		    $data['c_name'] = $_POST['name'];
		    $data['c_shopcode'] = $_POST['shopcode'];
		    $data['c_categoryid'] = $_POST['categoryid'];
		    $data['c_status'] = $_POST['status'];

		    if ($_POST['addnum'] > 0) {
		    	$data['c_num'] = $_POST['addnum'] + $this->vo['c_num'];
		    	$data['c_remain_num'] = $_POST['addnum'] + $this->vo['c_remain_num'];
		    }

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Federation/chain_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display();
	}

	//加盟、连锁分店列表
	public function subbranch(){
		$db = M('A_federation as a');
		//条件
		$nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['a.c_name'] = array('like', "%{$name}%");
        }
        $status = trim(I('status'));
        if (!empty($status)) {
           $w['a.c_status'] = $status;
        }
       
       	$w['a.c_pid'] = I('pid');
       	$w['a.c_type'] = I('type');
        $w['a.c_sign'] = 2;//分店

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg,c.c_category_name';       
        $panrn['join'] = 'left join t_users as u on a.c_ucode=u.c_ucode';
        $panrn['join1'] = 'left join t_category as c on a.c_categoryid=c.c_id';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);	
		
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->pid = I('pid');
		$this->type = I('type');
		$this->display();
	}


}