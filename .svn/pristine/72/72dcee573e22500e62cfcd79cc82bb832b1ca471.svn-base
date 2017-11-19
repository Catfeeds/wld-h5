<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 商城首页模块
 */
class MallhomeController extends BaseController {
	//首页板块列表
	public function platelist(){
	    $db = M('Mall_homepage');

		//条件
	    $type = trim(I('type'));
	    if (!empty($type)) {
	    	switch ($type) {
	    		case 1:
	    			$this->plate = "设计板块一";
	    			break;
	    		case 2:
	    			$this->plate = "设计板块二";
	    			break;
	    		case 3:
	    			$this->plate = "设计板块三";
	    			break;
	    		case 4:
	    			$this->plate = "设计板块四";
	    			break;
	    		case 5:
	    			$this->plate = "设计板块五";
	    			break;
	    		default:
	    			$this->plate = "设计板块六";
	    			break;
	    	}
	        $w['c_type'] = $type;
	        $this->type = $type;
	    }

	  	$theme = trim(I('theme'));
	    if (!empty($theme)) {
	        $w['c_theme'] = array('like', "%{$theme}%");
	    }

	    $state = trim(I('state'));
	    if (!empty($state)) {
	      $w['c_state'] = $state;
	    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_state desc,c_id desc';//排序
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

	//表单数据验证
	function pcheckfrom($data){
		if (empty($data['theme'])) {
	    	$this->error("主题不能为空");
		}
		if (empty($data['remarks'])) {
	    	$this->error("更多备注不能为空");
		}
		$iscountdown = $data['iscountdown'];
		if ($iscountdown == 1) {
	    	if (empty($data['begintime'])) {
		    	$this->error("倒计时开始时间不能为空");
			}
			if (empty($data['endtime'])) {
		    	$this->error("倒计时结束时间不能为空");
			}

			if(strtotime($data['endtime']) < strtotime($data['begintime'])){
				$this->error("倒计时结束时间不能早于倒计时开始时间");
			}
		}
	}

	//设计板块添加
	public function plate_add(){
		$this->action = 'plate_add';
		$this->type = I('type');
	
		if(IS_POST){
			$db = M('Mall_homepage');
		   	$this->pcheckfrom($_POST);

		    if(empty($_FILES['themeimg']['name'])){
	    		$this->error("主题图片必须上传");
	    	}
	    	
    		if(empty($_FILES['subimg']['name'])){
    			$this->error("更多图片必须上传");
    		}
	    	
		    $fileresult = uploadimg('mallhomepage');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$data['c_themeimg'] = $fileresult['data']['themeimg'];
			$data['c_subimg'] = $fileresult['data']['subimg'];

			$data['c_theme'] = $_POST['theme'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_iscountdown'] = $_POST['iscountdown'];
		    $data['c_isactivity'] = $_POST['isactivity'];
		    $data['c_remarks'] = $_POST['remarks'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_tag'] = $_POST['tag'];
		    $data['c_tagvalue'] = $_POST['tagvalue'];
		    $data['c_weburl'] = $_POST['weburl'];
		    $data['c_state'] = $_POST['state'];

		 //    $data['c_begintime'] = $_POST['begintime'];
			// $data['c_endtime'] = $_POST['endtime'];

		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mallhome/platelist?type='.$_POST['type'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//设计模块编辑
	public function plate_edit(){
		$this->action = 'plate_edit';
		$this->type = I('type');

		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('Mall_homepage')->where($w)->find();

		$this->vo = $arr;
		if(IS_POST){
			$db = M('Mall_homepage');
		    $this->pcheckfrom($_POST);

		    if(!empty($_FILES['themeimg']['name'])){
		    	$fileresult = uploadimg('mallhomepage');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_themeimg'] = $fileresult['data']['themeimg'];
		    }

    		if(!empty($_FILES['subimg']['name'])){
    			$fileresult1 = uploadimg('mallhomepage');
				if ($fileresult1['code'] != 0) {
				  $this->error($fileresult1['msg']);
				}
				$data['c_subimg'] = $fileresult1['data']['subimg'];
	    	}

		    $data['c_theme'] = $_POST['theme'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_iscountdown'] = $_POST['iscountdown'];
		    $data['c_isactivity'] = $_POST['isactivity'];
		    $data['c_remarks'] = $_POST['remarks'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_tag'] = $_POST['tag'];
		    $data['c_tagvalue'] = $_POST['tagvalue'];
		    $data['c_weburl'] = $_POST['weburl'];
		    $data['c_state'] = $_POST['state'];
		 //    $data['c_begintime'] = $_POST['begintime'];
			// $data['c_endtime'] = $_POST['endtime'];

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);

		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mallhome/platelist?type='.$_POST['type'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('plate_add');
	}

	//设计板块删除
    public function plate_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);

        $result = M('Mall_homepage')->where($where)->delete();
        if(!$result){
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $this->ajaxReturn(Message(0,'删除成功'));
    }

    //首页板块内容列表
	public function content_list(){
	    $db = M('Mall_homepage_img');

		//条件
	    $homeid = trim(I('homeid'));
	    if (!empty($homeid)) {
	        $w['c_homeid'] = $homeid;
	        $this->homeid = $homeid;
	    }

	    $type = trim(I('type'));
	    if (!empty($type)) {
	    	switch ($type) {
	    		case 1:
	    			$this->plate = "设计板块一";
	    			break;
	    		case 2:
	    			$this->plate = "设计板块二";
	    			break;
	    		case 3:
	    			$this->plate = "设计板块三";
	    			break;
	    		case 4:
	    			$this->plate = "设计板块四";
	    			break;
	    		case 5:
	    			$this->plate = "设计板块五";
	    			break;
	    		default:
	    			$this->plate = "设计板块六";
	    			break;
	    	}
	      	$this->type = $type;
	    }

	    $state = trim(I('state'));
	    if (!empty($state)) {
	      $w['c_state'] = $state;
	    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_state desc,c_id desc';//排序
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

	//设计板块内容添加
	public function content_add(){
		$this->action = 'content_add';
		$this->homeid = I('homeid');
		$this->type = I('type');

		if(IS_POST){
			$db = M('Mall_homepage_img');

		   	if (empty($_POST['title'])) {
	    		$this->error("图片主题不能为空");
			}

		    if(empty($_FILES['img']['name'])){
	    		$this->error("图片必须上传");
	    	}
	    	
		    $fileresult = uploadimg('mallhomepage');
			if ($fileresult['code'] != 0) {
			  $this->error($fileresult['msg']);
			}

			$data['c_img'] = $fileresult['data']['img'];

			$data['c_title'] = $_POST['title'];
		    $data['c_homeid'] = $_POST['homeid'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_tag'] = $_POST['tag'];
		    $data['c_tagvalue'] = $_POST['tagvalue'];
		    $data['c_weburl'] = $_POST['weburl'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mallhome/content_list?homeid='.$_POST['homeid'];
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//设计板块内容编辑
	public function content_edit(){
		$this->action = 'content_edit';
		$this->homeid = I('homeid');
		$this->type = I('type');

		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('Mall_homepage_img')->where($w)->find();

		$this->vo = $arr;
		if(IS_POST){
			$db = M('Mall_homepage_img');
		   	
		   	if (empty($_POST['title'])) {
	    		$this->error("图片主题不能为空");
			}

		    if(!empty($_FILES['img']['name'])){
		    	$fileresult = uploadimg('mallhomepage');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_img'] = $fileresult['data']['img'];
		    }

		    $data['c_title'] = $_POST['title'];
		    $data['c_homeid'] = $_POST['homeid'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_tag'] = $_POST['tag'];
		    $data['c_tagvalue'] = $_POST['tagvalue'];
		    $data['c_weburl'] = $_POST['weburl'];
		    $data['c_state'] = $_POST['state'];

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);

		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mallhome/content_list?homeid='.$_POST['homeid'];
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('content_add');
	}

	//设计板块内容删除
    public function content_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);

        $result = M('Mall_homepage_img')->where($where)->delete();
        if(!$result){
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $this->ajaxReturn(Message(0,'删除成功'));
    }

	//为您推荐
	public function refreegoods(){
	    $db = M('Product_tj as t');

		//条件
		$w['p.c_ishow'] = 1;
		$w['p.c_isdele'] = 1;

	  	$name = trim(I('name'));
	    if (!empty($name)) {
	        $w['p.c_name'] = array('like', "%{$name}%");
	    }

	    $nickname = trim(I('nickname'));
	    if (!empty($nickname)) {
	      $w['u.c_nickname'] = $nickname;
	    }

	    $state = trim(I('state'));
	    if (!empty($state)) {
	    	if($state == 10){
	    		$w['t.c_state'] = 0;
	    	}else{
	    		$w['t.c_state'] = 1;
	    	}
	    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 't.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

	    //分页显示数据
	    $panrn['field'] = 't.*,p.c_name,p.c_pimg,p.c_price,u.c_nickname,u.c_ucode';
        $panrn['join'] = 'inner join t_product as p on p.c_pcode=t.c_pcode';
        $panrn['join1'] = 'inner join t_users as u on p.c_ucode=u.c_ucode';
	    $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
	    $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
	}

	//推荐商品添加
	public function refreegoods_add(){
		$this->action = 'refreegoods_add';

		if(IS_POST){
			$db = M('Product_tj');
		   	if (empty($_POST['pcode'])) {
	    		$this->error("必须选择推荐商品");
			}
			if (empty($_POST['sort'])) {
	    		$this->error("必须选择推荐商品");
			}

			$data['c_pcode'] = $_POST['pcode'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_state'] = $_POST['state'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mallhome/refreegoods';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//设计板块内容编辑
	public function refreegoods_edit(){
		$this->action = 'refreegoods_edit';
	
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('Product_tj')->where($w)->find();

		$this->vo = $arr;
		if(IS_POST){
			$db = M('Product_tj');
		   	
		    if (empty($_POST['sort'])) {
	    		$this->error("必须选择推荐商品");
			}

		    $data['c_sort'] = $_POST['sort'];
		    $data['c_state'] = $_POST['state'];

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);

		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mallhome/refreegoods';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('refreegoods_add');
	}

	//设计板块内容删除
    public function refreegoods_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);

        $result = M('Product_tj')->where($where)->delete();
        if(!$result){
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $this->ajaxReturn(Message(0,'删除成功'));
    }
}