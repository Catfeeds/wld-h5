<?php
namespace Home\Controller;
use Think\Controller;
/**
*   权限管理
*/
class RoleController extends BaseController {

	// 角色列表
	public function index()
	{
		$Admin_role = M('admin_role');
		$count = $Admin_role->count();
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;
		$this->data = $Admin_role->limit($limit)->order('c_addtime desc')->select();
		$this->page = $page->show();
		$this->assign('count',$count);
		$this->display();
	}

	//角色添加
	public function role_add()
	{
		$this->action = 'Role/role_add';
		if(IS_AJAX){
	     	$Admin_role = M('admin_role');
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['rolename'])) {
	     		$this->ajaxReturn(Message(1001,'角色名称不能为空'));
	     	}
	      	$_data['c_rolename'] = $data['rolename'];
	        $_data['c_addtime'] = date('Y-m-d H:i:s',time());
	        $result = $Admin_role->add($_data);
	        if($result) {
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1000,'添加失败'));
            }
	    }
		$this->show();
	}

	//角色编辑
  	public function role_edit(){
  		$this->action = 'Role/role_edit';
	    if(IS_AJAX){
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['rolename'])) {
	     		$this->ajaxReturn(Message(1001,'角色名称不能为空'));
	     	}
	     	$where['c_id'] = $data['Id'];
	      	$_data['c_rolename'] = $data['rolename'];
	        $_data['c_addtime'] = date('Y-m-d H:i:s',time());
	        $result = M('admin_role')->where($where)->save($_data);
	        if($result) {
                $this->ajaxReturn(Message(0,'编辑成功'));
            }else{
                $this->ajaxReturn(Message(1000,'编辑失败'));
            }
	    }
	    $id = $_GET['Id'];
    	$this->vo = M('admin_role')->where('c_id='.$id)->find();
	    $this->display('role_add');
  	}

  	// 角色删除
  	public function role_delete()
  	{
  		$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('admin_role')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
  	}

  	// 管理员列表
  	public function admin()
  	{
		$Admin_users = M('Admin_users');
		$count = $Admin_users->count();
		$this->assign('count',$count);
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;
		$roleList=M('Admin_role')->limit($limit)->select();
		$data = $Admin_users->field('c_id,c_username,c_fullname,c_phone,c_state,c_addtime')->select();
		foreach ($data as $k => $v) {
			$roleids = M('Admin_user_role')->where('c_userid='.$v['c_id'])->field('c_roleid')->select();
			foreach ($roleids as  $v1) {
				foreach ($roleList as  $v2) {
					if($v1['c_roleid']==$v2['c_id']){
						$data[$k]['c_rolename'] .=  isset($data[$k]['c_rolename'])?',【'.$v2['c_rolename'].'】' : '【'.$v2['c_rolename'].'】';
					}
				}
			}
		}
		$this->page=$page->show();
		$this->assign('data',$data);
		$this->display();
  	}

  	// 管理员添加
  	public function admin_add()
  	{
  		$this->action = 'admin_add';
  		$Admin_users = M('Admin_users');
  		$this->rolelist = M('admin_role')->select();
		if(IS_POST){
	     	$Admin_users->startTrans();
	     	if (empty($_POST['fullname'])) {
	     		$this->error('管理员全名不能为空');
	     	}
	     	if (empty($_POST['username'])) {
	     		$this->error('管理员帐号不能为空');
	     	}
	     	if (empty($_POST['password'])) {
	     		$this->error('管理员登录密码不能为空');
	     	}
	     	if (empty($_POST['phone'])) {
	     		$this->error('管理员手机号不能为空');
	     	}
	     	if ($_POST['state'] == '') {
	     		$this->error('管理员登录权限不能为空');
	     	}
	     	if (empty($_POST['roleid'])) {
	     		$this->error('管理员角色选择不能为空');
	     	}
	      	$data['c_username'] = $_POST['username'];
	      	$data['c_password'] = encrypt($_POST['password'],C('ENCRYPT_KEY'));
	      	$data['c_fullname'] = $_POST['fullname'];
	      	$data['c_phone'] = $_POST['phone'];
	      	$data['c_state'] = $_POST['state'];
	        $data['c_addtime'] = date('Y-m-d H:i:s',time());
	        $result = M('Admin_users')->add($data);
	        if(!$result) {
	        	$Admin_users->rollback();
	        	$this->error('添加失败');
            }

            $roledata['c_userid'] = $result;
            foreach ($_POST['roleid'] as $key => $value) {
            	$roledata['c_roleid'] = $value;
            	$result = M('admin_user_role')->add($roledata);
            	if(!$result) {
		        	$Admin_users->rollback();
		        	$this->error('添加失败');
	            }
            }
            $Admin_users->commit();
            echo '<script language="javascript">alert("添加成功");</script>';
		  	echo '<script language="javascript">window.parent.location.href="'.U('Role/admin').'";</script>';die();
	    }
  		$this->display();
  	}

  	// 管理员编辑
  	public function admin_edit()
  	{
  		$Id = I('Id');
  		$this->action = 'admin_edit';
  		$Admin_users = M('Admin_users');
  		$where['c_id'] = I('Id');
  		$vo = $Admin_users->where($where)->find();
  		$vo['c_password'] = decrypt($vo['c_password'],C('ENCRYPT_KEY'));
  		$this->assign('vo',$vo);
  		$this->rolelist = M('admin_role')->select();
  		$this->userrolelist = M('Admin_user_role')->where('c_userid='.$Id)->field('c_roleid')->select();

	    if(IS_POST){
	     	$Admin_users->startTrans();
	     	if (empty($_POST['fullname'])) {
	     		$this->error('管理员全名不能为空');
	     	}
	     	if (empty($_POST['username'])) {
	     		$this->error('管理员帐号不能为空');
	     	}
	     	if (empty($_POST['password'])) {
	     		$this->error('管理员登录密码不能为空');
	     	}
	     	if (empty($_POST['phone'])) {
	     		$this->error('管理员手机号不能为空');
	     	}
	     	if ($_POST['state'] == '') {
	     		$this->error('管理员登录权限不能为空');
	     	}
	     	if (empty($_POST['roleid'])) {
	     		$this->error('管理员角色选择不能为空');
	     	}
	     	$where['c_id'] = I('Id');
	      	$data['c_username'] = $_POST['username'];
	      	$data['c_password'] = encrypt($_POST['password'],C('ENCRYPT_KEY'));
	      	$data['c_fullname'] = $_POST['fullname'];
	      	$data['c_phone'] = $_POST['phone'];
	      	$data['c_state'] = $_POST['state'];
	        $result = $Admin_users->where($where)->save($data);

	        if($result < 0) {
	        	$Admin_users->rollback();
	        	$this->error('基本信息编辑失败');
            }

            $roledata['c_userid'] = I('Id');
            $result = M('admin_user_role')->where($roledata)->delete();
            foreach ($_POST['roleid'] as $key => $value) {
            	$roledata['c_roleid'] = $value;
            	$result = M('admin_user_role')->add($roledata);
            	if(!$result) {
		        	$Admin_users->rollback();
		        	$this->error('角色编辑失败');
	            }
            }
            $Admin_users->commit();
            echo '<script language="javascript">alert("编辑成功！");</script>';
		  	echo '<script language="javascript">window.parent.location.href="'.U('Role/admin').'";</script>';die();
	    }

  		$this->display('admin_add');
  	}

  	// 管理员删除
  	public function admin_delete()
  	{
  		$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('Admin_users')->where($where)->delete();
		$_where['c_userid'] = array('in',$idstr);
		$result = M('admin_user_role')->where($_where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
  	}

	// 菜单列表
  	public function menulist(){
	    $Admin_menu = M('admin_menu');
	    $menudata = $Admin_menu->order('c_id desc')->select();
	    $count = 0;
        foreach ($menudata as $key => $value) {
            if ($value['c_pid'] == 0) {
                $count1 = 0;
                $child = array();
                foreach ($menudata as $key1 => $value1) {
                    if ($value1['c_pid'] == $value['c_id']) {
                            $child[$count1] = $value1;
                            $count1++;
                    }
                }
                if (count($child) > 0) {
                    $menuinfo[$count]['c_icon'] = $value['c_icon'];
                    $menuinfo[$count]['c_id'] = $value['c_id'];
                    $menuinfo[$count]['c_mname'] = $value['c_mname'];
                    $menuinfo[$count]['c_murl'] = $value['c_murl'];
                    $menuinfo[$count]['c_pid'] = $value['c_pid'];
                    $menuinfo[$count]['child'] = $child;
                    $count++;
                }
            }
        }
	    $this->assign('data',$menuinfo);
	    $this->assign('count',$count);
	    $this->display();
    }

    // 菜单添加
    public function menu_add()
    {
    	$this->mainlist = M('admin_menu')->where('c_pid=0')->select();
    	$this->action = 'Role/menu_add';
    	if (IS_AJAX) {
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['mname'])) {
	     		$this->ajaxReturn(Message(1001,'菜单名称不能为空'));
	     	}

	     	if (!empty($data['pid'])) {
	     		if (empty($data['murl'])) {
		     		$this->ajaxReturn(Message(1001,'子菜单连接不能为空'));
		     	}
	     	}

	     	$admin_menu = M('admin_menu');
	     	$admin_menu->startTrans();
	     	$menudata['c_mname'] = $data['mname'];
	     	$menudata['c_murl'] = $data['murl'];
	     	$menudata['c_pid'] = $data['pid'];
	     	$menudata['c_icon'] = str_replace('amp;','',$data['icon']);
    		$result = $admin_menu->add($menudata);
    		if (!$result) {
    			$admin_menu->rollback();
    			$this->ajaxReturn(Message(1001,'提交失败！请刷新再试！'));
    		}
    		if (!empty($data['pid'])) {
	    		$functiondata['c_functionname'] = $data['mname'];
		     	$functiondata['c_functionkey'] = $data['murl'];
		     	$functiondata['c_menuid'] = $data['pid'];
	    		$result = M('Admin_function')->add($functiondata);
	    		if (!$result) {
	    			$admin_menu->rollback();
	    			$this->ajaxReturn(Message(1001,'提交失败！请刷新再试！'));
	    		}
	    	}
    		$admin_menu->commit();
    		$this->ajaxReturn(Message(0,'提交成功！'));
    	}
    	$this->display();
    }

    // 菜单编辑
    public function menu_edit()
    {
    	$where['c_id'] = I('Id');
    	$this->vo = M('admin_menu')->where($where)->find();
    	$this->mainlist = M('admin_menu')->where('c_pid=0')->select();
    	$this->action = 'Role/menu_edit';
    	if (IS_AJAX) {
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['mname'])) {
	     		$this->ajaxReturn(Message(1001,'菜单名称不能为空'));
	     	}

	     	if (!empty($data['pid'])) {
	     		if (empty($data['murl'])) {
		     		$this->ajaxReturn(Message(1001,'子菜单功能接不能为空'));
		     	}
	     	}
	     	$where['c_id'] = $data['Id'];
	     	$menudata['c_mname'] = $data['mname'];
	     	$menudata['c_murl'] = $data['murl'];
	     	$menudata['c_pid'] = $data['pid'];
	     	$menudata['c_icon'] = str_replace('amp;','',$data['icon']);
    		$result = M('admin_menu')->where($where)->save($menudata);

    		if (!$result) {
    			$this->ajaxReturn(Message(1001,'提交失败！请刷新再试！'));
    		}

    		if (!empty($data['pid'])) {
	    		$functiondata['c_functionname'] = $data['mname'];
		     	$functiondata['c_functionkey'] = $data['murl'];
		     	$_where['c_menuid'] = $data['pid'];
	    		$result = M('Admin_function')->where($_where)->save($functiondata);
	    		if (!$result) {
	    			$admin_menu->rollback();
	    			$this->ajaxReturn(Message(1001,'提交失败！请刷新再试！'));
	    		}
	    	}
    		$this->ajaxReturn(Message(0,'提交成功！'));
    	}
    	$this->display('menu_add');
    }

    // 菜单删除
    public function menu_delete()
    {
    	$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('admin_menu')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
    }

    // 用户权限列表
  	public function permission()
  	{
  		$Model = M('admin_role_function');
        $fModel = M('admin_function');
        $Id = I('Id');
        //当前角色拥有权限
        $Result = $Model->where('c_roleid='.$Id)->field('c_functionid')->select();

        $Model_2 = M('admin_menu')->where('c_pid=0')->select();
        foreach ($Model_2 as $k => $v) {
          	$Model_2[$k]['son'] = $fModel->where('c_menuid='.$v['c_id'])->select();
          	foreach ($Model_2[$k]['son'] as $key => $value) {
            	$Model_2[$k]['son'][$key]['Grandson'] = $fModel->where('c_pid='.$value['c_id'])->select();
          	}
        }

        $this->assign('Id',$Id);
        $this->assign('default',$Result);
        $this->assign('list',$Model_2);
        $this->display();
  	}

  	// 权限分配
  	Public function permission_add()
  	{
  		$functionid = I('c_functionid');
        $roleid = I('c_roleid');
		$Model = M('admin_role_function');
		$Model->startTrans();
		$select = $Model->where('c_roleid=%s',$roleid)->find();
		if ($select) {
			$del = $Model->where('c_roleid='.$roleid)->delete();
			if(!$del){
		  		$Model -> rollback();
		  		echo '<script language="javascript">alert("原权限删除失败,请重新尝试...！");</script>';
		  		echo '<script language="javascript">window.parent.location.href="'.U('Role/index').'";</script>';die();
		  	}
		}
		foreach ($functionid as $key => $value) {
			$data = array(
			  	'c_roleid'=> $roleid,
			  	'c_functionid'=>$value
			);
			$Result = $Model->add($data);
			if (!$Result) {
			  	$Model = rollback();
			    echo '<script language="javascript">alert("添加权限错误，请重新添加");</script>';
		  		echo '<script language="javascript">window.parent.location.href="'.U('Role/index').'";</script>';die();
			}
		}
		$Model->commit();
		echo '<script language="javascript">alert("恭喜，权限添加成功");</script>';
		echo '<script language="javascript">window.parent.location.href="'.U('Role/index').'";</script>';die();
    }

    // 功能列表
    public function functionlist()
    {
        $Id = I('Id');
        $this->assign('Id',$Id);
        if (!empty($Id)) {
        	$idstr = $Id;
        	$where['c_pid'] = array('in',$idstr);
        } else {
        	$Model_2 = M('admin_menu')->where('c_pid=0')->field('c_id')->order('c_id desc')->select();
	    	foreach ($Model_2 as $key => $value) {
	    		if ($key == 0) {
	    			$idstr .= $value['c_id'];
	    		} else {
	    			$idstr .= ','.$value['c_id'];
	    		}
	    	}
	    	$where['c_menuid'] = array('in',$idstr);
        }

    	$count = M('admin_function')->where($where)->count();
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;
    	$data = M('admin_function')->where($where)->limit($limit)->order('c_id desc')->select();
    	foreach ($data as $key => $value) {
            $data[$key]['son'] = M('admin_function')->where('c_pid='.$value['c_id'])->field('c_id,c_functionname')->select();
        }
        $this->page = $page->show();
        $this->count = $count;
        $this->assign('data',$data);
	    $this->display();
    }

    //功能子类
    public function function_add()
    {
    	$Model = M('admin_role_function');
        $fModel = M('admin_function');
        $Model_2 = M('admin_menu')->where('c_pid=0')->field('c_id')->order('c_id desc')->select();
    	foreach ($Model_2 as $key => $value) {
    		if ($key == 0) {
    			$idstr .= $value['c_id'];
    		} else {
    			$idstr .= ','.$value['c_id'];
    		}
    	}
		$where['c_menuid'] = array('in',$idstr);
    	$this->mainlist = M('admin_function')->where($where)->order('c_id desc')->select();
    	$this->action = 'Role/function_add';
    	if (IS_AJAX) {
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['pid'])) {
		     	$this->ajaxReturn(Message(1001,'功能所属上级不能为空'));
	     	}
	     	if (empty($data['functionname'])) {
	     		$this->ajaxReturn(Message(1001,'功能名称不能为空'));
	     	}
	     	if (empty($data['functionkey'])) {
		     	$this->ajaxReturn(Message(1001,'功能链接不能为空'));
	     	}
	     	$functiondata['c_functionname'] = $data['functionname'];
	     	$functiondata['c_functionkey'] = $data['functionkey'];
	     	$functiondata['c_pid'] = $data['pid'];
    		$result = M('Admin_function')->add($functiondata);
    		if (!$result) {
    			$this->ajaxReturn(Message(1001,'提交失败！请刷新再试！'));
    		}
    		$this->ajaxReturn(Message(0,'提交成功！'));
    	}
    	$this->display();
    }

    //功能编辑
    public function function_edit()
    {
    	$where['c_id'] = I('Id');
    	$this->vo = M('admin_function')->where($where)->find();
    	$Model_2 = M('admin_menu')->where('c_pid=0')->field('c_id')->order('c_id desc')->select();
    	foreach ($Model_2 as $key => $value) {
    		if ($key == 0) {
    			$idstr .= $value['c_id'];
    		} else {
    			$idstr .= ','.$value['c_id'];
    		}
    	}
		$_where['c_menuid'] = array('in',$idstr);
    	$this->mainlist = M('admin_function')->where($_where)->order('c_id desc')->select();
    	$this->action = 'Role/function_edit';
    	if (IS_AJAX) {
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['pid'])) {
		     	$this->ajaxReturn(Message(1001,'功能所属上级不能为空'));
	     	}
	     	if (empty($data['functionname'])) {
	     		$this->ajaxReturn(Message(1001,'功能名称不能为空'));
	     	}
	     	if (empty($data['functionkey'])) {
		     	$this->ajaxReturn(Message(1001,'功能链接不能为空'));
	     	}

	     	$where['c_id'] = $data['Id'];
	     	$functiondata['c_functionname'] = $data['functionname'];
	     	$functiondata['c_functionkey'] = $data['functionkey'];
	     	$functiondata['c_pid'] = $data['pid'];
    		$result = M('Admin_function')->where($where)->save($functiondata);
    		if (!$result) {
    			$this->ajaxReturn(Message(1001,'提交失败！请刷新再试！'));
    		}
    		$this->ajaxReturn(Message(0,'提交成功！'));
    	}
    	$this->display('function_add');
    }

    //功能删除
    public function function_del()
    {
    	$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('admin_function')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
    }


}

 ?>