<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 微信公众号管理
 */
class WeixingController extends BaseController{
	//微信菜单列表
	public function menus_list(){
		$db = M('Wxmenu');
		//条件
		$w['c_genreid'] = 0;
        $title = trim(I('title'));
        if (!empty($title)) {
           $w['c_title'] = array('LIKE', "%{$title}%");
        }

		$parent = I('param.');

		$date = $db->where($w)->order('c_sort desc')->select();
		foreach ($date as $key => $value) {
			$w1['c_genreid'] = $value['c_id'];
			$date[$key]['child'] = $db->where($w1)->order('c_sort desc')->select();
		}
		$this->list = $date;
        $this->post = $parent;
		$this->display();
	}

	//ajax 修改更新菜单显示状态
	public function startpage_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");
	    $where['c_id'] = $Id;
	    $data['c_is_show'] = $state;
	    $result = M('Wxmenu')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	function checkfrom($data){
		if (empty($data['title'])) {
	    	$this->error("菜单名称不能为空");
		}
		if(!empty($data['genreid'])){
			if (empty($data['keyword']) && empty($data['url'])) {
	    		$this->error("子菜单必须填写关键词或者跳转URL");
			}
		}
		if (empty($data['sort'])) {
	    	$this->error("排序必须填写");
		}
	}

	//菜单添加
	public function menu_add(){
		$this->action = 'menu_add';
		$w['c_genreid'] = 0;
		$menulist = M('Wxmenu')->field('c_id,c_genreid,c_title')->where($w)->select();
		$this->menu_list = $menulist;
		if(IS_POST){
			$db = M('Wxmenu');

			$this->checkfrom($_POST);

			$data['c_genreid'] = $_POST['genreid'];
		    $data['c_title'] = $_POST['title'];
		    $data['c_keyword'] = $_POST['keyword'];
		    $data['c_url'] = $_POST['url'];
		    $data['c_is_show'] = $_POST['is_show'];
		    $data['c_sort'] = $_POST['sort'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Weixing/menus_list';
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
		$this->vo = M('Wxmenu')->where($w)->find();

		$w1['c_genreid'] = 0;
		$menulist = M('Wxmenu')->field('c_id,c_genreid,c_title')->where($w1)->select();
		$this->menu_list = $menulist;

		if(IS_POST){
			$db = M('Wxmenu');

			$this->checkfrom($_POST);

		    $data['c_genreid'] = $_POST['genreid'];
		    $data['c_title'] = $_POST['title'];
		    $data['c_keyword'] = $_POST['keyword'];
		    $data['c_url'] = $_POST['url'];
		    $data['c_is_show'] = $_POST['is_show'];
		    $data['c_sort'] = $_POST['sort'];

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Weixing/menus_list';
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

        $db = M('');
        $db -> startTrans();

        $where['c_id'] = array('in',$idstr);
        $menu_info = M('Wxmenu')->where($where)->find();

        if($menu_info['c_genreid'] == 0){
        	$w['c_genreid'] = $Id;
        	$result = M('Wxmenu')->where($w)->delete();

        	if(!$result){
        		$db->rollback();
        		$this->ajaxReturn(Message(1000,'删除子菜单失败'));
        	}
        }

        $result = M('Wxmenu')->where($where)->delete();

        if(!$result){
        	$db->rollback();
        	$this->ajaxReturn(Message(1001,'删除失败'));
        }

       	$db->commit();
        $this->ajaxReturn(Message(0,'删除成功'));
    }

    //生成微信菜单
    public function create_menus(){
	 	$db = M('Wxmenu');

	 	$where['c_genreid'] = 0;
	 	$where['c_is_show'] = 1;
	 	$list = $db->where($where)->field('c_title,c_id,c_keyword,c_url,c_genreid')->order('c_sort desc')->select();

	 	foreach ($list as $k => $v){

	 		if(!empty($v['c_keyword'])){
	 			$list[$k]['type'] = 'click';
	 		}else{
	 			$list[$k]['type'] = 'view';
	 		}

	 		$whe['c_genreid'] = $v['c_id'];
	 		$whe['c_is_show'] = 1;
	 		$list[$k]['sub_button']=$db->where($whe)->field('c_title,c_id,c_keyword,c_url,c_genreid')->order('c_sort desc')->select();

	 		foreach ($list[$k]['sub_button'] as $k1 => $v1){
	 			if(!empty($v1['c_keyword'])){
	 				$list[$k]['sub_button'][$k1]['type'] = 'click';
	 			}else{
	 				$list[$k]['sub_button'][$k1]['type'] = 'view';
	 			}
	 		}
	 	}

	 	foreach ($list as $key => $value) {
	 		$button['list'][$key]['name'] = $value['c_title'];
		 	if (count($value['sub_button']) == 0) {
	 			$button['list'][$key]['type'] = $value['type'];
	 			if (!empty($value['c_keyword'])) {
	 				$button['list'][$key]['key'] = $value['c_keyword'];
	 			} else {
	 				$button['list'][$key]['url'] = $value['c_url'];
	 			}
	 		} else {
		 		foreach ($value['sub_button'] as $k => $v) {
		 			$button['list'][$key]['sub_button'][$k]['type'] = $v['type'];
		 			$button['list'][$key]['sub_button'][$k]['name'] = $v['c_title'];
		 			if (!empty($v['c_keyword'])) {
		 				$button['list'][$key]['sub_button'][$k]['key'] = $v['c_keyword'];
		 			} else {
		 				$button['list'][$key]['sub_button'][$k]['url'] = $v['c_url'];
		 			}
		 		}
	 		}
	 	}

	 	$buttons = serialize($button['list']);

	 	$result = IGD('Weixing','Weixin')->menuCreate($buttons);

	    $this->ajaxReturn($result);
    }

    //微信自动回复消息列表
    public function wxmessage_list(){
    	$db = M('Wxmessage');
		//条件
        $key = trim(I('key'));
        if (!empty($key)) {
           $w['c_key'] = array('LIKE', "%{$key}%");
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
		$this->count = $date['count'];//分页
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->root_url = GetHost()."/";
		$this->display();
    }

    function wxmessage_check($data){
		if (empty($data['text'])) {
	    	$this->error("标题/内容不能为空");
		}
    }

    //微信自动回复消息添加
    public function wxmessage_add(){
		$this->action = 'wxmessage_add';

		if(IS_POST){
			$db = M('Wxmessage');

			$this->wxmessage_check($_POST);

			if(!empty($_FILES['picurl']['name'])){
	    		$fileresult = uploadimg('weixing');
				if ($fileresult['code'] != 0) {
				    $this->error($fileresult['msg']);
				}
			}

			$data['c_msgcode'] = CreateUcode('wxmsg');
			$data['c_picurl'] = $fileresult['data']['picurl'];
		    $data['c_key'] = $_POST['key'];
		    $data['c_text'] = $_POST['text'];
		    $data['c_desc'] = $_POST['desc'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_url'] = $_POST['url'];
		    $data['c_sign'] = $_POST['sign'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Weixing/wxmessage_list';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
    }

    //微信自动回复消息编辑
    public function wxmessage_edit(){
    	$this->action = 'wxmessage_edit';
		$Id = I('Id');
		$w['c_id'] = $Id;
		$this->vo = M('Wxmessage')->where($w)->find();
		if(IS_POST){
			$db = M('Wxmessage');

			$this->wxmessage_check($_POST);

	    	if(!empty($_FILES['picurl']['name'])){
	    		$fileresult = uploadimg('weixing');
				if ($fileresult['code'] != 0) {
				  $this->error($fileresult['msg']);
				}
				$data['c_picurl'] = $fileresult['data']['picurl'];
	    	}

	    	$data['c_picurl'] = $fileresult['data']['picurl'];
		    $data['c_key'] = $_POST['key'];
		    $data['c_text'] = $_POST['text'];
		    $data['c_desc'] = $_POST['desc'];
		    $data['c_type'] = $_POST['type'];
		    $data['c_url'] = $_POST['url'];
		    $data['c_sign'] = $_POST['sign'];

		    $w2['c_id'] = $_POST['Id'];
		    $result = $db->where($w2)->save($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Weixing/wxmessage_list';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('wxmessage_add');
    }

    //微信自动回复消息删除
    public function wxmessage_del()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Wxmessage')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }
}