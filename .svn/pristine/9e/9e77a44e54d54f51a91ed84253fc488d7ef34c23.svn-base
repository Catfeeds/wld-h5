<?php
namespace Home\Controller;
use Think\Controller;

class WordredController extends BaseController{
	//固定红包列表
	public function fixedword(){
		$db = M('Word_fixed as w');
		$aid = I('aid');
		//条件
        $wordname = trim(I('wordname'));
        if (!empty($wordname)) {
           $w['w.c_name'] = array('LIKE', "%{$wordname}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'w.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'w.*,a.c_activityname';
        $panrn['join'] = 'left join t_activity as a on a.c_id=w.c_aid';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);

		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->aid = $aid;
		$this->display();
	}

	//ajax 修改固定红包发放状态
	public function fixedword_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_status'] = $state;
	    $result = M('Word_fixed')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	public function checkfrom($data){
		$name = $data['name'];
		if (empty($name)) {
			$this->error("口令名称不能为空");
		}

		$totalnum = $data['totalnum'];
		if ($data['totalnum'] == '' || $data['totalnum'] == 0) {
			$this->error("口令红包总数量不能为空");
		}

		$remainnum = $data['remainnum'];
		if ($data['remainnum'] == '') {
			$this->error("口令红包剩余数量不能为空");
		}
	}

	//添加固定红包
	public function fixedword_add(){
		$this->action = 'fixedword_add';
		$aid = I('aid');
		$this->aid = $aid;
		if(IS_POST){
			$db = M('Word_fixed');
		    $this->checkfrom($_POST);

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_name'] = $_POST['name'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_remainnum'] = $_POST['remainnum'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_money'] = $_POST['money'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Wordred/fixedword?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//编辑固定红包
	public function fixedword_edit(){
		$Id = I('Id');
		$this->action = 'fixedword_edit';
		$db = M('Word_fixed');

		$where['c_id'] = $Id;
		$this->vo = $db->where($where)->find();
		$this->aid = $vo['aid'];
		if(IS_POST){
		    $this->checkfrom($_POST);

		    $data['c_name'] = $_POST['name'];
		    $data['c_totalnum'] = $_POST['totalnum'];
		    $data['c_remainnum'] = $_POST['remainnum'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_money'] = $_POST['money'];

		    $where['c_id'] = $_POST['Id'];
		    $result = $db->where($where)->save($data);

	    	if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Wordred/fixedword?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('fixedword_add');
	}

	//删除固定红包
	public function fixedword_del(){
		$Id = I('Id');
		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('Word_fixed')->where($where)->delete();
		if($result){
		    $this->ajaxReturn(Message(0,'删除成功'));
		}else{
		    $this->ajaxReturn(Message(1000,'删除失败'));
		}
	}

	//随机口令红包
	public function randomword(){
		$Word = M('Word');
		$aid = I('aid');
		$count = $Word->count();
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;
		$this->list = $Word->limit($limit)->order('c_addtime desc')->select();
		$this->page = $page->show();
		$this->assign('count',$count);
		$this->aid = $aid;
		$this->display();
	}

	//开启随机口令红包状态
	public function randomword_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_status'] = $state;
	    $result = M('Word')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	public function checkfrom1($data){
		$string = $data['string'];
		if (empty($string)) {
			$this->error("口令字符集不能为空");
		}

		$min = $data['min'];
		if ($data['min'] == '' || $data['min'] == 0) {
			$this->error("口令最小字数不能为空");
		}

		$max = $data['max'];
		if ($data['max'] == '' || $data['max'] == 0) {
			$this->error("口令最大字数不能为空");
		}

		$num = $data['num'];
		if ($data['num'] == '' || $data['num'] == 0) {
			$this->error("口令允许分享次数不能为空");
		}

		$limit = $data['limit'];
		if ($data['limit'] == '' || $data['limit'] == 0) {
			$this->error("每人每天限制获得次数不能为空");
		}
	}

	//随机口令红包添加
	public function randomword_add(){
		$this->action = 'randomword_add';
		$aid = I('aid');
		$this->aid = $aid;
		if(IS_POST){
			$db = M('Word');
		    $this->checkfrom1($_POST);

		    $data['c_aid'] = $_POST['aid'];
		    $data['c_string'] = $_POST['string'];
		    $data['c_min'] = $_POST['min'];
		    $data['c_max'] = $_POST['max'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_limit'] = $_POST['limit'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_updatetime'] = Date('Y-m-d H:i:s');
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Wordred/randomword?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//随机口令红包添加
	public function randomword_edit(){
		$Id = I('Id');
		$this->action = 'randomword_edit';
		$db = M('Word');

		$where['c_id'] = $Id;
		$this->vo = $db->where($where)->find();
		$this->aid = $vo['aid'];
		if(IS_POST){
		    $this->checkfrom1($_POST);

		    $data['c_string'] = $_POST['string'];
		    $data['c_min'] = $_POST['min'];
		    $data['c_max'] = $_POST['max'];
		    $data['c_num'] = $_POST['num'];
		    $data['c_limit'] = $_POST['limit'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_status'] = $_POST['status'];
		    $data['c_updatetime'] = Date('Y-m-d H:i:s');

		    $where['c_id'] = $_POST['Id'];
		    $result = $db->where($where)->save($data);

	    	if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Wordred/randomword?aid='.$_POST['aid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('randomword_add');
	}

	//删除固定红包
	public function randomword_del(){
		$Id = I('Id');
		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('Word')->where($where)->delete();
		if($result){
		    $this->ajaxReturn(Message(0,'删除成功'));
		}else{
		    $this->ajaxReturn(Message(1000,'删除失败'));
		}
	}

	//红包领取记录
	public function wordred_log(){
		$db = M('Word_red as w');
		//条件
		$phone = trim(I('phone'));
        if (!empty($phone)) {
           $w['u.c_phone'] = $phone;
        }
        $username = trim(I('username'));
        if (!empty($username)) {
           $w['w.c_username'] = array('LIKE', "%{$username}%");
        }

        $w['w.c_pid'] = 0;

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'w.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'w.*';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=w.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		foreach ($date['list'] as $key => $value) {
			$w1['w.c_pid'] = $value['c_id'];
			$date['list'][$key]['child'] = $db->field('w.*')->join('t_users as u on u.c_ucode=w.c_ucode')->where($w1)->select();
		}
		$this->list = $date['list'];

		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->display();
	}

}