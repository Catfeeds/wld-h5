<?php
namespace Home\Controller;
use Think\Controller;

class UsernatureController extends BaseController{
	//行业列表
	public function industry_list(){
		$db = M('industry');
		//条件
    	$c_name = trim(I('c_name'));
        if (!empty($c_name)) {
           $w['c_name'] = array('like', "%{$c_name}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//行业添加
	public function industry_add(){
		$this->action = 'Usernature/industry_add';
		if(IS_AJAX){
			$db = M('industry');

		    $str = I('str');
		    $formbul = str_replace('&quot;', '"', $str);
		    $jsondata = objarray_to_array(json_decode($formbul));

		    if (empty($jsondata['c_name'])) {
		        $this->ajaxReturn(Message(1001,'行业名称不能为空'));
		    }

		    $data['c_name'] = $jsondata['c_name'];

		    $result = $db->add($data);
		    if($result) {
		        $this->ajaxReturn(Message(0,'添加成功'));
		    }else{
		        $this->ajaxReturn(Message(1000,'添加失败'));
		    }
		}
		$this->display();
	}

	//行业编辑
	public function industry_edit(){
		$Id = I('Id');
		$this->action = 'Usernature/industry_edit';
		$db = M('industry');

		$where['c_id'] = $Id;
		$this->vo = $db->where($where)->find();
		if(IS_AJAX){

		    $str = I('str');
		    $formbul = str_replace('&quot;', '"', $str);
		    $jsondata = objarray_to_array(json_decode($formbul));

		    if (empty($jsondata['c_name'])) {
		        $this->ajaxReturn(Message(1001,'行业名称不能为空'));
		    }

		    $data['c_name'] = $jsondata['c_name'];

		    $where['c_id'] = $jsondata['Id'];
		    $result = $db->where($where)->save($data);

		    if($result) {
		        $this->ajaxReturn(Message(0,'编辑成功'));
		    }else{
		        $this->ajaxReturn(Message(1000,'编辑失败'));
		    }
		}
		$this->display('industry_add');
	}

	//行业删除
    public function industry_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('industry')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

	//标签列表
	public function label_list(){
		$db = M('label');
		//条件
    	$c_name = trim(I('c_name'));
        if (!empty($c_name)) {
           $w['c_name'] = array('like', "%{$c_name}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_sort desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//行业添加
	public function label_add(){
		$this->action = 'Usernature/label_add';
		if(IS_AJAX){
			$db = M('label');

		    $str = I('str');
		    $formbul = str_replace('&quot;', '"', $str);
		    $jsondata = objarray_to_array(json_decode($formbul));

		    if (empty($jsondata['c_name'])) {
		        $this->ajaxReturn(Message(1001,'标签名称不能为空'));
		    }
		     if (empty($jsondata['c_sort'])) {
		        $this->ajaxReturn(Message(1001,'标签排序不能为空'));
		    }

		    $data['c_name'] = $jsondata['c_name'];
		    $data['c_sort'] = $jsondata['c_sort'];
		    $data['c_addtime'] = date('Y-m-d H:i:s',time());

		    $result = $db->add($data);
		    if($result) {
		        $this->ajaxReturn(Message(0,'添加成功'));
		    }else{
		        $this->ajaxReturn(Message(1000,'添加失败'));
		    }
		}
		$this->display();
	}

	//行业编辑
	public function label_edit(){
		$Id = I('Id');
		$this->action = 'Usernature/label_edit';
		$db = M('label');

		$where['c_id'] = $Id;
		$this->vo = $db->where($where)->find();
		if(IS_AJAX){

		    $str = I('str');
		    $formbul = str_replace('&quot;', '"', $str);
		    $jsondata = objarray_to_array(json_decode($formbul));

		    if (empty($jsondata['c_name'])) {
		        $this->ajaxReturn(Message(1001,'标签名称不能为空'));
		    }
		     if (empty($jsondata['c_sort'])) {
		        $this->ajaxReturn(Message(1001,'标签排序不能为空'));
		    }

		    $data['c_name'] = $jsondata['c_name'];
		    $data['c_sort'] = $jsondata['c_sort'];

		    $where['c_id'] = $jsondata['Id'];
		    $result = $db->where($where)->save($data);

		    if($result) {
		        $this->ajaxReturn(Message(0,'编辑成功'));
		    }else{
		        $this->ajaxReturn(Message(1000,'编辑失败'));
		    }
		}
		$this->display('label_add');
	}

	//行业删除
    public function label_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('label')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }
}