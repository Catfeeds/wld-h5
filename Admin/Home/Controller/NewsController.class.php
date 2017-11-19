<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  信息管理
 */
class NewsController extends BaseController 
{

 	// 资讯列表
    public function index(){
        $this->typelist = M('News_type')->select();
        if (!empty($_POST['type'])) {
            $where['a.c_type'] = $_POST['type'];
        }
        if (!empty($_POST['title'])) {
            $where['a.c_title'] = array('like','%'.$_POST['title'].'%');
        }

        $News = M('News as a');
        $count = $News->where($where)->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $join = 'left join t_news_type as b on a.c_type=b.c_id';
        $this->data = $News->join($join)->limit($limit)->order('c_addtime desc')->where($where)->field('a.*,b.c_name')->select();
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->show();
    }

    // 资讯添加
    public function new_add()
    {
        $this->action = 'new_add';
        $this->typelist = M('News_type')->select();
        if (IS_POST) {
            if (empty($_POST['title'])) {
                unlink($_POST['img'][0]);
                $this->error('资讯标题不能为空');
            }
            if (empty($_POST['type'])) {
                unlink($_POST['img'][0]);
                $this->error('资讯分类不能为空');
            }
            if ($_POST['istop'] == '') {
                unlink($_POST['img'][0]);
                $this->error('是否置顶不能为空');
            }
            if (empty($_POST['meta_key'])) {
                unlink($_POST['img'][0]);
                $this->error('关键字不能为空');
            }
            if (empty($_POST['desc'])) {
                unlink($_POST['img'][0]);
                $this->error('描述不能为空');
            }
            if (empty($_POST['anthor'])) {
                unlink($_POST['img'][0]);
                $this->error('作者不能为空');
            }
            if (count($_POST['img']) == 0) {
                unlink($_POST['img'][0]);
                $this->error('图片不能为空');
            }
            if (empty($_POST['content'])) {
                unlink($_POST['img'][0]);
                $this->error('内容不能为空');
            }
            $typeid = $_POST['type'];
            $data['c_title'] = $_POST['title'];
            $data['c_type'] = $typeid;
            $w['c_id'] = $typeid;
            $data['c_type_rule'] = M('News_type')->where($w)->getField('c_rule');
            $data['c_istop'] = $_POST['istop'];
            $data['c_meta_key'] = $_POST['meta_key'];
            $data['c_desc'] = $_POST['desc'];
            $data['c_click'] = $_POST['click'];
            $data['c_anthor'] = $_POST['anthor'];
            $data['c_content'] = $_POST['content'];
            $data['c_img'] = $_POST['img'][0];
            $data['c_isshow'] = $_POST['isshow'];
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $data['c_modify'] = date('Y-m-d H:i:s');
            $result = M('News')->add($data);
            if($result){
                echo '<script language="javascript">alert("添加成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.U('News/index').'";</script>';die();
            }else{
                $this->error('添加失败');
            }
        }
        $this->root_url = GetHost()."/";
    	$this->display();
    }

    // 资讯编辑
    public function new_edit()
    {
    	$this->action = 'new_edit';
        $where['c_id'] = I('Id');
        $this->vo = M('News')->where($where)->find();
        $this->typelist = M('News_type')->select();
        if (IS_POST) {
            if (empty($_POST['title'])) {
                unlink($_POST['img'][0]);
                $this->error('资讯标题不能为空');
            }
            if (empty($_POST['type'])) {
                unlink($_POST['img'][0]);
                $this->error('资讯分了不能为空');
            }
            if ($_POST['istop'] == '') {
                unlink($_POST['img'][0]);
                $this->error('是否置顶不能为空');
            }
            if (empty($_POST['meta_key'])) {
                unlink($_POST['img'][0]);
                $this->error('关键字不能为空');
            }
            if (empty($_POST['desc'])) {
                unlink($_POST['img'][0]);
                $this->error('描述不能为空');
            }
            if (empty($_POST['anthor'])) {
                unlink($_POST['img'][0]);
                $this->error('作者不能为空');
            }
            if (count($_POST['img']) == 0) {
                unlink($_POST['img'][0]);
                $this->error('图片不能为空');
            }
            if (empty($_POST['content'])) {
                unlink($_POST['img'][0]);
                $this->error('内容不能为空');
            }
            $typeid = $_POST['type'];
            $data['c_title'] = $_POST['title'];
            $data['c_type'] = $typeid;
            $w['c_id'] = $typeid;
            $data['c_type_rule'] = M('News_type')->where($w)->getField('c_rule');
            $data['c_istop'] = $_POST['istop'];
            $data['c_meta_key'] = $_POST['meta_key'];
            $data['c_desc'] = $_POST['desc'];
            $data['c_click'] = $_POST['click'];
            $data['c_anthor'] = $_POST['anthor'];
            $data['c_content'] = $_POST['content'];
            $data['c_img'] = $_POST['img'][0];
            $data['c_isshow'] = $_POST['isshow'];
            $data['c_modify'] = date('Y-m-d H:i:s');
            $where['c_id'] = $_POST['Id'];
            $result = M('News')->where($where)->save($data);
            if($result){
                echo '<script language="javascript">alert("编辑成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.U('News/index').'";</script>';die();
            }else{
                $this->error('编辑失败');
            }
        }
        $this->root_url = GetHost()."/";
    	$this->display('new_add');
    }

    // 资讯删除
    public function new_delete()
    {
    	$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('News')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
    }


	// 资讯分类列表
    public function type_list()
    {
        $News = M('News_type');
        $count = $News->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $arr = $News->limit($limit)->order('c_addtime desc')->select();
        foreach ($arr as $k => $v) {
            switch ($arr[$k]['c_rule']) {
                case 1:
                    $arr[$k]['c_rule'] = "新闻资讯";
                    break;
                case 2:
                    $arr[$k]['c_rule'] = "关于文档";
                    break;
                default:
                    $arr[$k]['c_rule'] = "帮助文档";
                    break;
            }
        }
        $this->data = $arr;
        $this->page = $page->show();
        $this->assign('count',$count);
    	$this->display();
    }
     
     
    // 资讯分类添加
    public function type_add()
    {
        $this->action = 'News/type_add';
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['name'])) {
                $this->ajaxReturn(Message(1000,'分类名不能为空'));
            }
            if (empty($jsondata['rule'])) {
                $this->ajaxReturn(Message(1001,'类别规则不能为空'));
            }
            $data['c_name'] = $jsondata['name'];
            $data['c_rule'] = $jsondata['rule'];
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('News_type')->add($data);
            if($result){
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1002,'添加失败'));
            }
        }
    	$this->display();
    }

    // 资讯分类编辑
    public function type_edit()
    {
    	$this->action = 'News/type_edit';
        $where['c_id'] = I('Id');
        $this->vo = M('News_type')->where($where)->find();
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['name'])) {
                $this->ajaxReturn(Message(1000,'分类名不能为空'));
            }
            if (empty($jsondata['rule'])) {
                $this->ajaxReturn(Message(1001,'类别规则不能为空'));
            }
            $data['c_name'] = $jsondata['name'];
            $data['c_rule'] = $jsondata['rule'];
            $where['c_id'] = $jsondata['Id'];
            $result = M('News_type')->where($where)->save($data);
            if($result){
                $this->ajaxReturn(Message(0,'编辑成功'));
            }else{
                $this->ajaxReturn(Message(1000,'编辑失败'));
            }
        }
    	$this->display('type_add');
    }

    // 资讯分类删除
    public function type_delete()
    {
    	$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('News_type')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
    }

    /**
     *  微商学院
     */
    // 商学院信息列表
    public function college_list()
    {
        if (!empty($_POST['type'])) {
            $where['c_type'] = $_POST['type'];
        }
        if (!empty($_POST['title'])) {
            $where['c_title'] = array('like','%'.$_POST['title'].'%');
        }

        $News = M('Business_college');
        $count = $News->where($where)->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $data = $News->limit($limit)->where($where)->order('c_addtime desc')->select();
        foreach ($data as $key => $value) {
            $imgwhere['c_sourceid'] = 1;
            $imgwhere['c_regionid'] = $value['c_id'];
            $data[$key]['c_img'] = M('Resource_img')->where($imgwhere)->field('c_img')->select();
        }

        $this->assign('data',$data);
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->display();
    }

    // 商学院信息添加
    public function college_add()
    {
        $this->action = 'college_add';
        if (IS_POST) {
            if (empty($_POST['title'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息标题不能为空');
            }
            if (empty($_POST['type'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息类型不能为空');
            }
            if ($_POST['type'] != 1 && empty($_POST['url'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息外链不能为空');
            }
            if (empty($_POST['desc'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息简介不能为空');
            }
            if (count($_POST['img']) == 0) {
                A('Common')->del_img($_POST['img']);
                $this->error('图片不能为空');
            }
            if ($_POST['type'] == 1 && empty($_POST['content'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息内容不能为空');
            }

            $Model = M('');
            $Model->startTrans();
            $data['c_title'] = $_POST['title'];
            $data['c_type'] = $_POST['type'];
            $data['c_desc'] = $_POST['desc'];
            $data['c_click'] = $_POST['click'];
            $data['c_url'] = $_POST['url'];
            $data['c_content'] = $_POST['content'];
            $data['c_isshow'] = $_POST['isshow'];
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Business_college')->add($data);
            $regionid = $result;
            if(!$result){
                A('Common')->del_img($_POST['img']);
                $Model->rollback();
                $this->error('添加失败');
            }

            $image = new \Think\Image();
            foreach ($_POST['img'] as $key => $value) {
                // 生成等比缩略图
                $imgoption = SYS_PATH.str_replace('/',DS, $value);
                $thumbimg = 'Uploads/thumb/'.date('Y-m-d').'/tb_'.$key.time().'.jpg';
                $thumbimgpath = SYS_PATH.str_replace('/',DS, $thumbimg);
                checkDir($thumbimgpath);
                $image->open($imgoption)->thumb(150, 150)->save('./'.$thumbimg);

                $imglist['c_img'] = $value;
                $imglist['c_thumbnail_img'] = $thumbimg;
                $imglist['c_regionid'] = $regionid;
                $imglist['c_sourceid'] = 1;
                $result = M('Resource_img')->add($imglist);
                if(!$result){
                    A('Common')->del_img($_POST['img']);
                    $Model->rollback();
                    $this->error('添加失败');
                }
            }

            $Model->commit();
            echo '<script language="javascript">alert("添加成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('News/college_list').'";</script>';die();
        }
        $this->display();
    }

    // 商学院信息编辑
    public function college_edit()
    {
        $this->action = 'college_edit';
        $where['c_id'] = I('Id');
        $vo = M('Business_college')->where($where)->find();
        $imgwhere['c_sourceid'] = 1;
        $imgwhere['c_regionid'] = $vo['c_id'];
        $vo['c_img'] = M('Resource_img')->where($imgwhere)->field('c_img')->select();

        $this->assign('vo',$vo);
        if (IS_POST) {
            if (empty($_POST['title'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息标题不能为空');
            }
            if (empty($_POST['type'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息类型不能为空');
            }
            if ($_POST['type'] != 1 && empty($_POST['url'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息外链不能为空');
            }
            if (empty($_POST['desc'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息简介不能为空');
            }
            if (count($_POST['img']) == 0) {
                A('Common')->del_img($_POST['img']);
                $this->error('图片不能为空');
            }
            if ($_POST['type'] == 1 && empty($_POST['content'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('信息内容不能为空');
            }
            $Model = M('');
            $Model->startTrans();

            $data['c_title'] = $_POST['title'];
            $data['c_type'] = $_POST['type'];
            $data['c_desc'] = $_POST['desc'];
            $data['c_click'] = $_POST['click'];
            $data['c_url'] = $_POST['url'];
            $data['c_content'] = $_POST['content'];
            $data['c_img'] = implode('|',$_POST['img']);
            $data['c_isshow'] = $_POST['isshow'];
            $where['c_id'] = $_POST['Id'];
            $result = M('Business_college')->where($where)->save($data);
            if(!$result){
                A('Common')->del_img($_POST['img']);
                $Model->rollback();
                $this->error('添加失败1');
            }

            $imgwhere['c_regionid'] = $_POST['Id'];
            $imgwhere['c_sourceid'] = 1;
            $result = M('Resource_img')->where($imgwhere)->delete();
            if(!$result){
                A('Common')->del_img($_POST['img']);
                $Model->rollback();
                $this->error('添加失败');
            }

            $image = new \Think\Image();
            foreach ($_POST['img'] as $key => $value) {
                // 生成等比缩略图
                $imgoption = SYS_PATH.str_replace('/',DS, $value);
                $thumbimg = 'Uploads/thumb/'.date('Y-m-d').'/tb_'.$key.time().'.jpg';
                $thumbimgpath = SYS_PATH.str_replace('/',DS, $thumbimg);
                checkDir($thumbimgpath);
                $image->open($imgoption)->thumb(150, 150)->save('./'.$thumbimg);

                $imglist['c_img'] = $value;
                $imglist['c_thumbnail_img'] = $thumbimg;
                $imglist['c_regionid'] = $_POST['Id'];
                $imglist['c_sourceid'] = 1;
                $result = M('Resource_img')->add($imglist);
                if(!$result){
                    A('Common')->del_img($_POST['img']);
                    $Model->rollback();
                    $this->error('添加失败');
                }
            }

            $Model->commit();
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('News/college_list').'";</script>';die();
        }
        $this->display('college_add');
    }

    // 商学院信息删除
    public function college_delete()
    {
        $Model = M('');
        $Model->startTrans();
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Business_college')->where($where)->delete();
        if (!$result) {
            $Model->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $imgarr = explode('|',$Id);
        foreach ($imgarr as $key => $value) {
            $imgwhere['c_regionid'] = $value;
            $imgwhere['c_sourceid'] = 1;
            $imgdata = M('Resource_img')->where($imgwhere)->select();
            if ($imgdata) {
                $result = M('Resource_img')->where($imgwhere)->delete();
                if (!$result) {
                    $Model->rollback();
                    $this->ajaxReturn(Message(1001,'删除失败'));
                }
            }
            foreach ($imgdata as $k => $v) {
                unlink($v['c_img']);
                unlink($v['c_thumbnail_img']);
            }
        }

        $Model->commit();
        $this->ajaxReturn(Message(0,'删除成功'));

    }
    

    //申诉列表
    public function appeal()
    {
        $db = M('Usertip_appeal as d');
        //条件
        $flag = 0;
        // // $sign = trim(I('sign'));
        // if (!empty($sign)) {
        //     $w['d.c_sign'] = $sign;
        //     $w['d.c_state'] = 0;
        //     $this->sign = $sign;
        //     $flag = 1;
        // }
        // $this->flag = $flag;

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['u.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }

        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = $c_nickname;
        }
        
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }
        
        $c_cont = trim(I('c_cont'));
        if (!empty($c_cont)) {
            $w['ui.c_content'] = $c_cont;
        }



        $c_tip_id = trim(I('c_tip_id'));
        if (!empty($c_tx_code)) {
            $w['d.c_tip_id'] = array('like', "%{$c_tip_id}%");
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "d.c_addtime between '".$begintime."' and '".$endtime."'";
        }


        $c_status = trim(I('c_status'));
        if (!empty($c_status)) {
            if($c_status == 'sqz'){
               $w['d.c_status'] = 0;
            }else{
                $w['d.c_status'] = $c_status;
            }
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'd.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'd.*,u.c_nickname,u.c_ucode,u.c_phone,ui.c_content as c_cont ';
        $panrn['join1'] = 'left join t_resource as ui on d.c_tip_id=ui.c_id';
        $panrn['join'] = 'left join t_users as u on d.c_ucode=u.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            switch ($data_list[$k]['c_status']) {
                case 0:
                    $data_list[$k]['mystate'] = "<font color='#808080'>未处理</font>";
                    break;
                case 1:
                    $data_list[$k]['mystate'] = "<font color='#00FF00'>申诉通过</font>";
                    break;
                default:
                    $data_list[$k]['mystate'] = "<font color='#FF0000'>驳回</font>";
                    break;
            }
        }

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        // $this->sum_money = empty($mdata[0]['sum_money']) ? "0.00" : $mdata[0]['sum_money'];
        $this->display();
    }



        //Excel导出提款申请数据
        public function educeIndex(){
            $Order = D('Appeal','Behind');
            $Order -> sheetIndexnt();
        }

        //申诉结果处理
        public function ajax_apply_handle()
        {

            $db = M('Usertip_appeal');

            $db -> startTrans();

            $txcode = trim(I('txcode'));
            $handle = intval(I('handle'));


            $w['c_tip_id'] = $txcode;

            $drawing_info = $db->where($w)->find();
            $ucode = $drawing_info['c_ucode'];

            if(empty($ucode) || !$drawing_info){
                $this->ajaxReturn(Message(1001,"申诉信息错误"));
            }

            $save_data['c_updatetime'] = date('Y-m-d H:i:s');

            $w['c_status'] = 0;

            if($handle == 1){//同意申诉，只改变状态
                //改变资源状态
                $sw['c_id'] = $drawing_info['c_tip_id'];
                $sw['c_status'] = 0;
                $ssave['c_status'] = 1;
                $result = M('resource')->where($sw)->save($ssave);
                // $ms = M("resource")->where($sw)->find();
          

                if (!$result) {

                    $db->rollback();
                     $this->ajaxReturn(Message(1000,"改变状态失败"));
                }

                //恢复次数
                $delw['c_tip_id'] = $drawing_info['c_tip_id'];
                $result = M('Usertip_record')->where($delw)->delete();
                // if (!$result) {
                //     $db->rollback();
                //     $this->ajaxReturn(Message(1000,"删除失败"));
                // }

                $save_data['c_status'] = 1;
              
                $r = $db->where($w)->save($save_data);
                $content = '您的申诉，系统已同意，系统将进行处理';
                $weburl =  GetHost(1) . '/index.php/Home/Index/comment?rid=' . $drawing_info['c_tip_id'];
            }else{//不同意申诉，驳回请求
                $save_data['c_status'] = 2;
                $r = $db->where($w)->save($save_data);
                $content = '您的申诉，系统不同意，已被驳回，如有疑问请跟我们联系';
                $weburl = GetHost(1).'/index.php/Home/News/details?id=120';
            }

            //给用户发送相关消息
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] =  $content;
            $msgdata['tag'] = 10000;
            $msgdata['tagvalue'] = -1;
            $msgdata['weburl'] = $weburl;
            $Msgcentre->CreateMessege($msgdata);

            if(!$r){
                $db->rollback();
                $this->ajaxReturn(Message(1001,"操作失败"));
            }

            $db->commit();
            $this->ajaxReturn(Message(0,"操作成功"));
        }

      

        //举报列表
        
        
        public function jubao_record()
        {
            //用户昵称
            $c_nickname = trim(I('c_nickname'));
            if (!empty($c_nickname)) {
                $w['u.c_nickname'] = array('like', "%{$c_nickname}%");
            }
            
            $c_phone = trim(I('c_phone'));
            if (!empty($c_phone)) {
                $w['u.c_phone'] = $c_phone;
            }

            $db = M('Usertip_record as a');

            $panrn['where'] = $w;
            $parent = I('param.');
            $panrn['order'] = 'a.c_id desc';//排序
            $panrn['limit'] = 25;//分页数

             //分页显示数据
            $panrn['field'] = 'a.*,u.c_nickname,u.c_phone,b.c_content as c_tent,ub.c_content as c_cont';
            $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
            $panrn['join1'] = 'left join t_tipsinfo as b on a.c_tip_id=b.c_tid';
            $panrn['join2'] = 'left join t_resource as ub on a.c_content_id=ub.c_id';
            $list=D('Db','Behind');
            $date=$list->mate_select_pages($db,$panrn);
            $this->list = $date['list'];
            $this->count = $date['count'];//分页\
            $this->page = $date['Page'];//分页
            $this->root_url = GetHost()."/";
            $this->post = $parent;
            $this->display();
        }

    
        //热词管理
        public function keywords()
        {   
             $c_key_name = trim(I('c_key_name'));
            if (!empty($c_key_name)) {
                $w['a.c_key_name'] = array('like', "%{$c_key_name}%");
            }
            
            

            $db = M('Keywords as a');

            $panrn['where'] = $w;
            $parent = I('param.');
            $panrn['order'] = 'a.c_id desc';//排序
            $panrn['limit'] = 25;//分页数

             //分页显示数据
            $panrn['field'] = 'a.*';
            $list=D('Db','Behind');
            $date=$list->mate_select_pages($db,$panrn);
            $this->list = $date['list'];
            $this->count = $date['count'];//分页\
            $this->page = $date['Page'];//分页
            $this->root_url = GetHost()."/";
            $this->post = $parent;
            $this->display();
        }

        //热词添加   
        public function keywords_list()
        {
            $this->action = 'News/keywords_list';
            if (IS_AJAX) {
                $str = I('str');
                $formbul = str_replace('&quot;', '"', $str);
                $jsondata = objarray_to_array(json_decode($formbul));
                if (empty($jsondata['content'])) {
                    $this->ajaxReturn(Message(1000,'热词不能为空'));
                }
                
                $data['c_key_name'] = $jsondata['content'];
                $data['c_ctime'] = date('Y-m-d H:i:s');
                $result = M('Keywords')->add($data);
                if($result){
                    $this->ajaxReturn(Message(0,'添加成功'));
                }else{
                    $this->ajaxReturn(Message(1002,'添加失败'));
                }
            }
            $this->display();
        }



         // 删除
        public function delete()
        {
            $Id = I('Id');
            $idstr = str_replace('|', ',', $Id);
            $where['c_id'] = array('in',$idstr);
            $result = M('Keywords')->where($where)->delete();
            if($result){
                $this->ajaxReturn(Message(0,'删除成功'));
            }else{
                $this->ajaxReturn(Message(1000,'删除失败'));
            }
        }

    public function retroact(){
        $db = M('Users_messages as m');
    
        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = $c_nickname;
        }

        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }

        $c_keyword = trim(I('c_keyword'));
        if (!empty($c_keyword)) {
            $w['m.c_content'] = array('like', "%{$c_keyword}%");
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "m.c_addtime between '".$begintime."' and '".$endtime."'";
        }

        $w['m.c_status'] = 0;

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'm.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'm.*,u.c_nickname,u.c_phone';
        $panrn['join'] = 'left join t_users as u on m.c_ucode=u.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];

        foreach ($data_list as $k => $v) {
            if ($v['c_flag']>0) {
                $data_list[$k]['mystate'] = "<font color='#808080'>有回复</font>";
            }else{
                $data_list[$k]['mystate'] = "<font color='#00FF00'>待回复</font>";
            }
           
        }

        $h_cound = $db->where('m.c_flag>0')->count();
        $w_cound = $date['count']-$h_cound;
        $this->assign('h_cound',$h_cound);
        $this->assign('w_cound',$w_cound);
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        // $this->sum_money = empty($mdata[0]['sum_money']) ? "0.00" : $mdata[0]['sum_money'];
        $this->display();

    }

    public function return_edit(){

        $Id = I('Id');
        $this->assign('Id',$Id);
        $this->display();
    }

    public function add_return(){

        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            $Id = $jsondata['Id'];
            $ucode = $jsondata['ucode'];
            $content = $jsondata['content'];
            if (empty($Id)) {
                $this->ajaxReturn(Message(1000,'参数错误'));
            }
            if (empty($ucode)) {
                $this->ajaxReturn(Message(1000,'请选择回复人'));
            }
            if (empty($content)) {
                $this->ajaxReturn(Message(1000,'回复内容不能为空'));
            }
            $w['c_id'] = $Id;
            $result = M('Users_messages')->field('c_ucode,c_flag')->where($w)->find();

            $data['c_to_id'] = $Id;
            $data['c_ucode'] = $ucode;
            $data['c_to_ucode'] = $result['c_ucode'];
            $data['content'] = $content;
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Reply_messages')->add($data);

            $res = M('Users_messages')->where($w)->setInc('c_flag');
            if($result || $res){
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1002,'添加失败'));
            }
        }

    }

    public function retmessage(){
        $db = M('Reply_messages as m');

        $w['c_to_id'] = I('Id');
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'm.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'm.*';
       
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
          $s['c_ucode'] = $v['c_ucode'];
          $result = M('Users')->field('c_nickname')->where($s)->find();
          $data_list[$k]['c_nickname'] = $result['c_nickname'];
        }

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();

    }  

    public function add_replylist(){

        $db = M('Users as u');
        $nickname = I('c_nickname');
        if (!empty($nickname)) {
            $w['u.c_nickname'] = $nickname;
        }
        $phone = I('c_phone');
        if (!empty($phone)) {
            $w['u.c_phone']   = $phone; 
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'u.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'u.*';
       
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    public function add_replyer(){
        $ucode = I('txcode');
        if (empty($ucode)) {
             $this->ajaxReturn(Message(1001,"参数错误"));
        }
        $w['c_ucode'] = I('txcode');
        $result = M('Canread_messages')->where($w)->find();

        if ($result) {
             $this->ajaxReturn(Message(1001,"这个帐号已经授权"));
        }
        $data['c_ucode'] = $ucode;
        $data['c_addtime'] = date('Y-m-d H:i:s');
        $res = M('Canread_messages')->add($data);

        if ($res) {
            $this->ajaxReturn(Message(0,"操作成功"));
        }

    }


   
}