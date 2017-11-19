<?php
namespace Home\Controller;
use Think\Controller;
/**
 *禁用用户
 */
class OffusersController extends BaseController{
	//禁用用户列表
	public function users_limit(){
    	$db = M('Users_limit as l');
		//条件
    	$c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }
        $sign = I('sign');
        if(!empty($sign)){
            $w['l.c_sign'] = $sign;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'l.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,u.c_nickname,u.c_headimg';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
	}

	//验证表单数据
	function pcheckfrom($data){
		if (empty($data['ucode'])) {
			$this->error("禁用用户未选择");
		}

		if (empty($data['remarks'])) {
	    	$this->error("禁用原因备注不能为空");
		}

		if (empty($data['starttime'])) {
	    	$this->error("禁用开始时间不能为空");
		}

		if (empty($data['endtime'])) {
	    	$this->error("禁用结束时间不能为空");
		}

		if(strtotime($starttime) > strtotime($endtime)){
			$this->error("禁用开始时间不能晚于禁用结束时间");
		}
	}

	//添加禁用用户
	public function limit_add(){
		$this->action = 'limit_add';

		if(IS_POST){
			$db = M('Users_limit');

		   	$this->pcheckfrom($_POST);

		    $data['c_ucode'] = $_POST['ucode'];
		    $data['c_sign'] = $_POST['sign'];
		    $data['c_remarks'] = $_POST['remarks'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		    	$openid = md5($_POST['ucode']);
		      IGD('Common', 'Redis')->RediesStoreSram($openid, null);
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Offusers/users_limit';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//编辑禁用用户
	public function limit_edit(){
		$this->action = 'limit_edit';
		$Id = I('Id');
		$w['l.c_id'] = $Id;
		$field = "l.*,u.c_nickname";
		$join = 'left join t_users as u on u.c_ucode=l.c_ucode';
		$arr = M('Users_limit as l')->field($field)->join($join)->where($w)->find();
		$this->vo = $arr;
		if(IS_POST){
			$db = M('Users_limit');

		    $this->pcheckfrom($_POST);

		   	$data['c_ucode'] = $_POST['ucode'];
		    $data['c_sign'] = $_POST['sign'];
		    $data['c_remarks'] = $_POST['remarks'];
		    $data['c_starttime'] = $_POST['starttime'];
		    $data['c_endtime'] = $_POST['endtime'];

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Offusers/users_limit';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('limit_add');
	}

	//删除禁用用户
    public function limit_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Users_limit')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }
    
    //用户登录记录
    public function user_login () {
		$db = M('Login_record as a ');
        
        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = $c_phone;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['a.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['a.c_ucode'] = $usinfo['c_ucode'];
            }
        }
        
        //用户编码
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
            $w['a.c_ucode'] = $ucode;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            
           
        }

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
}