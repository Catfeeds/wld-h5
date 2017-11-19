<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 商圈管理模块
 */
class CircleController extends BaseController {
	//商圈列表
	public function circle_list(){
	    $db = M('Circle as c');

		//条件
	  	$name = trim(I('name'));
	    if (!empty($name)) {
	        $w['c.c_name'] = array('like', "%{$name}%");
	    }

	    $province = trim(I('province'));
	    if (!empty($province)) {
	        $w['p.c_circle_name'] = array('like', "%{$province}%");
	    }

	    $citycode = trim(I('citycode'));
	    if (!empty($citycode)) {
	        $w['c.c_citycode'] = $citycode;
	    }

	    $status = trim(I('status'));
	    if (!empty($status)) {
	    	if($status == 10){
	    		$w['c.c_status'] = 0;
	    	}else{
	    		$w['c.c_status'] = 1;
	    	}
	    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c.c_id asc';//排序
		$panrn['limit'] = 25;//分页数

		//分页显示数据
        $panrn['field'] = 'c.*,p.c_circle_name';
        $panrn['join'] = 'left join t_circle_code as p on p.c_code=c.c_provincecode';

	    //分页显示数据
	    $list = D('Db','Behind');
		$date = $list->mate_select_pages($db,$panrn);

		//统计商圈访问记录、商圈商家加人数
		$arr_list = $date['list'];

		foreach ($arr_list as $key => $value) {
			$cw['c_provincecode'] = $value['c_provincecode'];
			$cw['c_citycode'] = $value['c_citycode'];

			$arr_list[$key]['visit_num'] = M('Circle_visit')->where($cw)->count();

			$arr_list[$key]['visit_shop'] = M('Circle_shop')->where($cw)->count();
		}

		$this->list = $arr_list;
		$this->count = $date['count'];//分页\
	    $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
	}

	//ajax 修改商圈开启状态
	public function circle_status(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_citycode'] = $Id;
	    $data['c_status'] = $state;

	    $result = M('Circle')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//商圈编辑
	public function circle_edit(){
		$this->action = 'circle_edit';

		$citycode = I('citycode');
		$w['c_citycode'] = $citycode;
		$arr = M('Circle')->where($w)->find();

		$this->vo = $arr;
		if(IS_POST){
			$db = M('Circle');

		    if (empty($_POST['name'])) {
	    		$this->error("商圈名称不能为空");
			}

		    if(!empty($_FILES['img']['name'])){
		    	$fileresult = uploadimg('circle');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img'] = $fileresult['data']['img'];
		    }

		    $data['c_name'] = $_POST['name'];
		    $data['c_level'] = $_POST['level'];
		    $data['c_moods'] = $_POST['moods'];
		    $data['c_resourcenum'] = $_POST['resourcenum'];
		    $data['c_recommend'] = $_POST['recommend'];
		    $data['c_longitude'] = $_POST['longitude'];
		    $data['c_latitude'] = $_POST['latitude'];
		    $data['c_address'] = $_POST['address'];
		    $data['c_status'] = $_POST['status'];
		
		    $w2['c_citycode'] = $_POST['citycode'];
		    $result = $db->where($w2)->save($data);

		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Circle/circle_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('circle_add');
	}

	//商圈用户访问记录
    public function circle_visit(){
    	$db = M('Circle_visit as a');
		//条件
		$citycode = trim(I('citycode'));
        if (!empty($citycode)) {
            $w['a.c_citycode'] = $citycode;
            $this->citycode = $citycode;
        }

    	$c_phoner = trim(I('phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
    }

    //商圈商家加入记录
    public function circle_shop(){
    	$db = M('Circle_shop as a');
		//条件
		$citycode = trim(I('citycode'));
        if (!empty($citycode)) {
            $w['a.c_citycode'] = $citycode;
            $this->citycode = $citycode;
        }

    	$c_phoner = trim(I('phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg,u.c_phone,l.c_isfixed';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $panrn['join1'] = 'left join t_user_local as l on a.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
    }

    //商圈签到记录
    public function circle_sign(){
    	$db = M('Circle_sign as a');
		//条件
		$citycode = trim(I('citycode'));
        if (!empty($citycode)) {
            $w['a.c_citycode'] = $citycode;
            $this->citycode = $citycode;
        }
		
    	$c_phoner = trim(I('phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_headimg,u.c_phone';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
    }

    //商圈等级
    public function circle_level(){
    	$db = M('Circle_level');
		//条件
		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_level asc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
    }

    //商圈等级添加
	public function level_add(){
		$this->action = 'level_add';

		if(IS_POST){
			$db = M('Circle_level');
			if (empty($_POST['level'])) {
	    		$this->error("等级不能为空");
			}		 
			
		   	if (empty($_POST['moods'])) {
	    		$this->error("等级所需人气不能为空");
			}

			if(empty($_FILES['levelimg']['name'])){
	    		$this->error("等级图标必须上传");
	    	}		  
	    	
		    $fileresult = uploadimg('circle');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$data['c_levelimg'] = $fileresult['data']['levelimg'];

			$data['c_level'] = $_POST['level'];
		    $data['c_moods'] = $_POST['moods'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Circle/circle_level';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//商圈等级编辑
	public function level_edit(){
		$this->action = 'level_edit';

		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('Circle_level')->where($w)->find();

		$this->vo = $arr;
		if(IS_POST){
			$db = M('Circle_level');
		   	
		   	if (empty($_POST['level'])) {
	    		$this->error("等级不能为空");
			}		 
			
		   	if (empty($_POST['moods'])) {
	    		$this->error("等级所需人气不能为空");
			}		  

		    if(!empty($_FILES['levelimg']['name'])){
		    	$fileresult = uploadimg('circle');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_levelimg'] = $fileresult['data']['levelimg'];
		    }

		    $data['c_level'] = $_POST['level'];
		    $data['c_moods'] = $_POST['moods'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);

		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Circle/circle_level';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('level_add');
	}

	//商圈等级删除
    public function level_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);

        $result = M('Circle_level')->where($where)->delete();
        if(!$result){
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $this->ajaxReturn(Message(0,'删除成功'));
    }
}