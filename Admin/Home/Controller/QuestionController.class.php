<?php
namespace Home\Controller;
use Think\Controller;
/**
*   权限管理
*/
class QuestionController extends BaseController {

	// 角色列表
	public function index()
	{
		$Questions = M('Questions');
		$where['c_qid'] = 0;
		$count = $Questions->where($where)->count();
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;
		$data = $Questions->where($where)->limit($limit)->order('c_addtime desc')->select();
		foreach ($data as $key => $value) {
			$data[$key]['c_activityname'] = M('Activity')->where('c_id='.$value['c_aid'])->getField('c_activityname');
			$data[$key]['child'] = $Questions->where('c_qid='.$value['c_id'])->select();
		}
		$this->data = $data;
		$this->page = $page->show();
		$this->assign('count',$count);
		$this->display();
	}

	//添加
	public function add()
	{
		$this->action = 'Question/add';
		if(IS_AJAX){
	     	$Questions = M('Questions');
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	$qname = array_keys($data);
	     	$qalue = array_values($data);
	     	$db = M('');
	     	$db->startTrans();
	     	if (empty($data['aid'])) {
	     		$this->ajaxReturn(Message(1000,'请选择活动'));
	     	}
	     	$questiondata['c_name'] = trim($data['answer_0']);
	     	if (empty($questiondata['c_name'])) {
	     		$this->ajaxReturn(Message(1001,'题目不能为空'));
	     	}
	     	if (empty($data['answerid'])) {
	     		$this->ajaxReturn(Message(1002,'请选择答案'));
	     	}
	     	$questiondata['c_qid'] = 0;
	     	$questiondata['c_aid'] = $data['aid'];
	     	$questiondata['c_addtime'] = date('Y-m-d H:i:s');
	     	$qid = M('Questions')->add($questiondata);
	     	if (!$qid) {
	     		$db->rollback();
	     		$this->ajaxReturn(Message(1003,'添加问题失败'));
	     	}

	     	foreach ($qname as $key => $value) {
	     		$signid = str_replace('answer_', '', $value);
	     		if ($signid > 0) {
	     			$questiondata['c_qid'] = $qid;
	     			$questiondata['c_name'] = trim($qalue[$key]);
	     			if (empty($questiondata['c_name'])) {
			     		$this->ajaxReturn(Message(1001,'答案不能为空'));
			     	}
	     			if ($data['answerid'] == $signid) {
	     				$questiondata['c_sign'] = 1;
	     			} else {
	     				$questiondata['c_sign'] = 0;
	     			}
	     			$result = M('Questions')->add($questiondata);
	     			if (!$result) {
	     				$db->rollback();
	     				$this->ajaxReturn(Message(1003,'添加答案失败'));
	     			}
	     		}
	     	}

	      	$db->commit();
            $this->ajaxReturn(Message(0,'添加成功'));

	    }
	    $this->activelist = M('Activity')->where('c_activitytype=5')->select();
		$this->show();
	}

	//编辑
  	public function edit(){
  		$this->action = 'Question/edit';
	    if(IS_AJAX){
	     	$Questions = M('Questions');
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	$qname = array_keys($data);
	     	$qalue = array_values($data);
	     	$db = M('');
	     	$db->startTrans();
	     	if (empty($data['aid'])) {
	     		$this->ajaxReturn(Message(1000,'请选择活动'));
	     	}
	     	$questiondata['c_name'] = trim($data['answer_0']);
	     	if (empty($questiondata['c_name'])) {
	     		$this->ajaxReturn(Message(1001,'题目不能为空'));
	     	}
	     	if (empty($data['answerid'])) {
	     		$this->ajaxReturn(Message(1002,'请选择答案'));
	     	}
	     	$where['c_id'] = $data['id'];
	     	$questiondata['c_qid'] = 0;
	     	$questiondata['c_aid'] = $data['aid'];
	     	$questiondata['c_updatetime'] = date('Y-m-d H:i:s');
	     	$qid = M('Questions')->where($where)->save($questiondata);
	     	if (!$qid) {
	     		$db->rollback();
	     		$this->ajaxReturn(Message(1003,'编辑问题失败'));
	     	}

	     	foreach ($qname as $key => $value) {
	     		$signsaveid = str_replace('answer', '', $value);
	     		$signid = str_replace('answer_', '', $value);
	     		if ($signid > 0 || $signsaveid > 0) {
	     			$questiondata['c_qid'] = $data['id'];
	     			$questiondata['c_name'] = trim($qalue[$key]);
	     			if (empty($questiondata['c_name'])) {
			     		$this->ajaxReturn(Message(1001,'答案不能为空'));
			     	}

	     			if ($data['answerid'] == $signid || $data['answerid'] == $signsaveid) {
	     				$questiondata['c_sign'] = 1;
	     			} else {
	     				$questiondata['c_sign'] = 0;
	     			}

	     			if ($signsaveid > 0) {
	     				$where['c_id'] = $signsaveid;
	     				$result = M('Questions')->where($where)->save($questiondata);
	     			} else {
	     				$questiondata['c_addtime'] = date('Y-m-d H:i:s');
	     				$result = M('Questions')->add($questiondata);
	     			}

	     			if (!$result) {
	     				$db->rollback();
	     				$this->ajaxReturn(Message(1003,'编辑答案失败'));
	     			}
	     		}
	     	}

	      	$db->commit();
            $this->ajaxReturn(Message(0,'编辑成功'));
	    }
	    $id = $_GET['Id'];
    	$vo = M('Questions')->where('c_id='.$id)->find();
    	$vo['answer'] = M('Questions')->where('c_qid='.$id)->select();
    	$this->assign('vo',$vo);
    	$this->activelist = M('Activity')->where('c_activitytype=5')->select();
	    $this->display('add');
  	}

  	// 删除
  	public function delete()
  	{
  		$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where[] = array('c_id in ('.$idstr.') or c_qid in ('.$idstr.')');
		$result = M('Questions')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
  	}

}