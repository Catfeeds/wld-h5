<?php
namespace Home\Controller;
use Think\Controller;
class GradeController extends BaseController {
	//等级列表
	public function grade_list(){
		$this->data = D('Db','Behind')->mate_select(M('user_level'),$parr);
    	$this->count = M('user_level')->where($w)->count();
		$this->display();
	}

	//会员等级添加
    public function grade_add(){
    	$this->action = 'Grade/grade_add';
    	if(IS_AJAX){
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));

	     	if (empty($data['c_level_name']) || empty($data['c_rule']) || empty($data['c_desc']) ) {
	     		$this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
	     	}

	     	$message = D('User','Behind')->gradeAdd($data);

	     	$this->ajaxReturn($message);
    	}
    	$this->display();
    }

    //会员等级编辑
    public function grade_edit(){
    	$Id = I('Id');
    	$where['c_id'] = $Id;
    	$this->data = M('user_level')->where($where)->find();
    	$this->action = 'Grade/grade_edit';
    	if(IS_AJAX){
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));

	     	if (empty($data['c_level_name']) || empty($data['c_rule']) || empty($data['c_desc']) ) {
	     		$this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
	     	}

	     	$message = D('User','Behind')->gradeAdd($data);

	     	$this->ajaxReturn($message);
    	}
    	$this->display('grade_add');
    }

    //会员等级删除
    public function grade_delete(){
    	$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('user_level')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
    }
}