<?php
namespace Home\Controller;
use Think\Controller;

class AppController extends BaseController{
	//启动页列表
	public function startpage(){
		$db = M('app_welcome');
		//条件
    	$theme = trim(I('theme'));
        if (!empty($theme)) {
           $w['c_theme'] = array('like', "%{$theme}%");
        }
        $type = trim(I('type'));
        if (!empty($type)) {
           $w['c_type'] = $type;
        }
        $state = trim(I('state'));
        if (!empty($state)) {
           $w['c_state'] = $state;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//ajax 修改更新状态
	public function startpage_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");
	    $where['c_id'] = $Id;
	    $data['c_state'] = $state;
	    $result = M('app_welcome')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	function checkfrom($data){
		if (empty($data['c_theme'])) {
	    	$this->error("主题名称不能为空");
		}
		if (empty($data['c_alias'])) {
	    	$this->error("更新别名不能为空");
		}
		if (empty($data['type'])) {
	    	$this->error("平台类型未选择");
		}
		if (empty($data['state'])) {
	    	$this->error("是否更新未选择");
		}

		if (empty($data['starttime'])) {
		    	$this->error("显示放开始时间不能为空");
		}
		if (empty($data['endtime'])) {
	    	$this->error("显示结束时间不能为空");
		}
		if(strtotime($starttime) > strtotime($endtime)){
			$this->error("显示开始时间不能晚于显示结束时间");
		}
	}


	//启动页添加
	public function startpage_add(){
		$this->action = 'startpage_add';

		if(IS_POST){
			$db = M('app_welcome');

			$this->checkfrom($_POST);

			if(empty($_FILES['img480']['name'])){
	    		$this->error("480p必须上传");
	    	}
	    	if(empty($_FILES['img720']['name'])){
	    		$this->error("720p必须上传");
	    	}
	    	if(empty($_FILES['img1080']['name'])){
	    		$this->error("1080p必须上传");
	    	}

		    $fileresult = uploadimg('app');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}
			$data['c_img480'] = $fileresult['data']['img480'];
			$data['c_img720'] = $fileresult['data']['img720'];
			$data['c_img1080'] = $fileresult['data']['img1080'];
		    $data['c_theme'] = $_POST['c_theme'];
		    $data['c_alias'] = $_POST['c_alias'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/App/startpage';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//启动页编辑
	public function startpage_edit(){
		$this->action = 'startpage_edit';
		$Id = I('Id');
		$w['c_id'] = $Id;
		$this->vo = M('app_welcome')->where($w)->find();
		if(IS_POST){
			$db = M('app_welcome');

			$this->checkfrom($_POST);

		    $fileresult = uploadimg('app');
		    if(!empty($_FILES['img480']['name'])){
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img480'] = $fileresult['data']['img480'];
		    }
		    if(!empty($_FILES['img720']['name'])){
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img720'] = $fileresult['data']['img720'];
		    }
		    if(!empty($_FILES['img1080']['name'])){
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img1080'] = $fileresult['data']['img1080'];
		    }

		    $data['c_theme'] = $_POST['c_theme'];
		    $data['c_alias'] = $_POST['c_alias'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/App/startpage';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('startpage_add');
	}

	//启动页删除
    public function startpage_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('app_welcome')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //版本列表
    public function versions(){
		$db = M('app_version');
		//条件
        $state = trim(I('state'));
        if (!empty($state)) {
           $w['c_state'] = $state;
        }
        $type = trim(I('type'));
        if (!empty($type)) {
           $w['c_type'] = $type;
        }
        $sign = trim(I('sign'));
        if (!empty($sign)) {
           $w['c_sign'] = $sign;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
    }

    //ajax 修改启用状态
	public function versions_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");
	    $where['c_id'] = $Id;
	    $data['c_state'] = $state;
	    $result = M('app_version')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//ajax 修改强制状态
	public function versions_sign(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");
	    $where['c_id'] = $Id;
	    $data['c_sign'] = $state;
	    $result = M('app_version')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	function checkfrom1($data){
		if (empty($data['version'])) {
	    	$this->error("版本号不能为空");
		}
		if (empty($data['infro'])) {
	    	$this->error("更新内容不能为空");
		}
		if (empty($data['type'])) {
	    	$this->error("平台类型未选择");
		}
		if (empty($data['state'])) {
	    	$this->error("是否启用未选择");
		}
		if (empty($data['sign'])) {
	    	$this->error("是否强制未选择");
		}
	}

	//版本添加
	public function versions_add(){
		$this->action = 'versions_add';

		if(IS_POST){
			$db = M('app_version');

			$this->checkfrom1($_POST);

			if(empty($_POST['url']) && empty($_FILES['fiel_url']['name'])){
	    		$this->error("更新地址不能为空，必须二选一");
	    	}
	    	if(empty($_POST['url'])){
	    		$fileresult = uploadfile('Appversion');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_url'] = GetHost().'/'.$fileresult['data']['fiel_url'];
	    	}else{
	    		$data['c_url'] = $_POST['url'];
	    	}

		    $data['c_version'] = $_POST['version'];
		    $data['c_infro'] = $_POST['infro'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_sign'] = $_POST['sign'];
		    $data['c_updatatime'] = Date('Y-m-d H:i:s');
		    $data['c_createtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/App/versions';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//版本编辑
	public function versions_edit(){
		$this->action = 'versions_edit';
		$Id = I('Id');
		$w['c_id'] = $Id;
		$this->vo = M('app_version')->where($w)->find();
		if(IS_POST){
			$db = M('app_version');

			$this->checkfrom1($_POST);

			if(empty($_POST['url']) && empty($_FILES['fiel_url']['name'])){
	    		$this->error("更新地址不能为空，必须二选一");
	    	}

	    	if(!empty($_FILES['fiel_url']['name'])){
	    		$fileresult = uploadfile('Appversion');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_url'] = GetHost().'/'.$fileresult['data']['fiel_url'];
	    	}else{
	    		$data['c_url'] = $_POST['url'];
	    	}

		    $data['c_version'] = $_POST['version'];
		    $data['c_infro'] = $_POST['infro'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_sign'] = $_POST['sign'];
		    $data['c_updatatime'] = Date('Y-m-d H:i:s');

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/App/versions';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('versions_add');
	}

	//版本删除
    public function versions_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('app_version')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //APP 用户反馈
    public function feedback(){
    	$db = M('feedback as f');
		//条件
    	$theme = trim(I('theme'));
        if (!empty($theme)) {
           $w['f.c_theme'] = array('like', "%{$theme}%");
        }
        $type = trim(I('type'));
        if (!empty($type)) {
           $w['f.c_type'] = $type;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'f.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        $panrn['field'] = 'f.*,u.c_nickname,u.c_headimg';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=f.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$arr = $date['list'];
		foreach ($arr as $k => $v) {
			if(!empty($arr[$k]['c_img'])){
				$arr[$k]['imgs'] = explode("|",$arr[$k]['c_img']);
			}else{
				$arr[$k]['imgs'] = '';
			}
		}
		$this->list = $arr;
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost().'/';
		$this->post = $parent;
		$this->display();
    }

    //用户反馈删除
    public function feedback_del()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $imgs = M('feedback')->field('c_img')->where($where)->find();
        $img_arr = explode('|',$imgs['c_img']);
        $result = M('feedback')->where($where)->delete();
        if($result){
        	foreach($img_arr as $row){
        		unlink($row);
        	}
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //app菜单管理
    public function menu_list(){
    	$db = M('App_menu1');
		//条件
		$w['c_pid'] = 0;
        $mname = trim(I('mname'));
        if (!empty($mname)) {
           $w['c_name'] = array('LIKE', "%{$mname}%");
        }
        $terminal_type = trim(I('terminal_type'));
        if (!empty($terminal_type)) {
           $w['c_terminal_type'] = $terminal_type;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		foreach ($date['list'] as $key => $value) {
			$w1['c_pid'] = $value['c_id'];
			$date['list'][$key]['child'] = $db->where($w1)->select();
		}
		$this->list = $date['list'];
		$this->count = $date['count'];//分页
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->display();
    }

    function checkmenu($data){
		if (empty($data['name'])) {
	    	$this->error("菜单名称不能为空");
		}
		if (empty($data['alias'])) {
	    	$this->error("更新别名不能为空");
		}
		if (empty($data['discern'])) {
	    	$this->error("菜单版本标识不能为空");
		}
		if (empty($data['sort'])) {
	    	$this->error("排序不能为空");
		}
		if (empty($data['interface_type'])) {
	    	$this->error("接口类型未选择");
		}
		if (empty($data['interface_address'])) {
	    	$this->error("接口地址不能为空");
		}
		if (empty($data['terminal_type'])) {
	    	$this->error("平台类型未选择");
		}
		if (empty($data['version_number'])) {
	    	$this->error("版本号不能为空");
		}
		if ($data['isskip'] == 1) {
			if($data['reason'] == ''){
	    		$this->error("不跳转时，需要填写不跳转原因");
			}
		}
    }

    //app菜单添加
    public function menu_add(){
		$this->action = 'menu_add';
		$w['c_pid'] = 0;
		$menulist = M('App_menu1')->field('c_id,c_pid,c_name,c_terminal_type')->where($w)->select();

		foreach ($menulist as $key => $value) {
			switch ($value['c_terminal_type']) {
				case 1:
					$menulist[$key]['c_name'] = $value['c_name'].'(Android)';
					break;

				default:
					$menulist[$key]['c_name'] = $value['c_name'].'(IOS)';
					break;
			}
		}

		$this->menu_list = $menulist;

		if(IS_POST){
			$db = M('App_menu1');

			$this->checkmenu($_POST);

    		$fileresult = uploadimg('appmenu');
			if ($fileresult['code'] != 0) {
			    $this->error($fileresult['msg']);
			}
			$data['c_pid'] = $_POST['pid'];
			$data['c_img'] = $fileresult['data']['img'];
		    $data['c_name'] = $_POST['name'];
		    $data['c_alias'] = $_POST['alias'];
		    $data['c_discern'] = $_POST['discern'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_interface_type'] = $_POST['interface_type'];
		    $data['c_interface_address'] = $_POST['interface_address'];
		    $data['c_terminal_type'] = $_POST['terminal_type'];
		    $data['c_version_number'] = $_POST['version_number'];
		    $data['c_access'] = $_POST['access'];
		    $data['c_role'] = $_POST['role'];
		    $data['c_isskip'] = $_POST['isskip'];
		    $data['c_reason'] = $_POST['reason'];
		    $data['c_updtetime'] = Date('Y-m-d H:i:s');
		    $data['c_createtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/App/menu_list';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
    }

    //菜单编辑
    public function menu_edit(){
    	$this->action = 'menu_edit';
		$Id = I('Id');
		$w['c_id'] = $Id;
		$this->vo = M('App_menu1')->where($w)->find();

		$w1['c_pid'] = 0;
		$menulist = M('App_menu1')->field('c_id,c_pid,c_name,c_terminal_type')->where($w1)->select();

		foreach ($menulist as $key => $value) {
			switch ($value['c_terminal_type']) {
				case 1:
					$menulist[$key]['c_name'] = $value['c_name'].'(Android)';
					break;

				default:
					$menulist[$key]['c_name'] = $value['c_name'].'(IOS)';
					break;
			}
		}

		$this->menu_list = $menulist;

		if(IS_POST){
			$db = M('App_menu1');

			$this->checkmenu($_POST);

	    	if(!empty($_FILES['img']['name'])){
	    		$fileresult = uploadimg('appmenu');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img'] = $fileresult['data']['img'];
	    	}

		    $data['c_pid'] = $_POST['pid'];
		    $data['c_name'] = $_POST['name'];
		    $data['c_alias'] = $_POST['alias'];
		    $data['c_discern'] = $_POST['discern'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_interface_type'] = $_POST['interface_type'];
		    $data['c_interface_address'] = $_POST['interface_address'];
		    $data['c_terminal_type'] = $_POST['terminal_type'];
		    $data['c_version_number'] = $_POST['version_number'];
		    $data['c_access'] = $_POST['access'];
		    $data['c_isskip'] = $_POST['isskip'];
		    $data['c_reason'] = $_POST['reason'];
		    $data['c_updtetime'] = Date('Y-m-d H:i:s');

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/App/menu_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('menu_add');
    }

    //菜单删除
    public function menu_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('App_menu1')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

}