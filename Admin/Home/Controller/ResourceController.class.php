<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  资源管理
 */
class ResourceController extends BaseController {

 	// 资源列表
 	public function index()
 	{
        if (!empty($_POST['nickname'])) {
            $where['b.c_nickname'] = array('like','%'.$_POST['nickname'].'%');
        }
        if (!empty($_POST['title'])) {
            $where['a.c_title'] = array('like','%'.$_POST['title'].'%');
        }
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $Resource = M('Resource as a');
        $count = $Resource->join($join)->where($where)->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $data = $Resource->join($join)->limit($limit)->order('c_updatetime desc,c_addtime desc')->where($where)->field($field)->select();
        foreach ($data as $key => $value) {
            $imgwhere['c_sourceid'] = 2;
            $imgwhere['c_regionid'] = $value['c_id'];
            $data[$key]['c_img'] = M('Resource_img')->where($imgwhere)->field('c_img')->select();
        }
        $this->assign('data',$data);
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->show();
 	}

 	// 资源添加
    public function resource_add()
    {
        $this->action = 'resource_add';
        if (IS_POST) {
            if (empty($_POST['title'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('资源标题不能为空');
            }
            if (empty($_POST['ucode'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择对应的用户');
            }
            if (empty($_POST['content'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('资源内容不能为空');
            }
            if (count($_POST['img']) == 0) {
                A('Common')->del_img($_POST['img']);
                $this->error('图片不能为空');
            }
            if ($_POST['status'] == '') {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择资源发布状态');
            }

            $Model = M('');
            $Model->startTrans();
            $data['c_title'] = $_POST['title'];
            $data['c_content'] = $_POST['content'];
            $data['c_ucode'] = $_POST['ucode'];
            $data['c_click'] = $_POST['click'];
            $data['c_status'] = $_POST['status'];
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Resource')->add($data);
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
                $imglist['c_sourceid'] = 2;
                $result = M('Resource_img')->add($imglist);
                if(!$result){
                    A('Common')->del_img($_POST['img']);
                    $Model->rollback();
                    $this->error('添加失败');
                }
            }

            $Model->commit();
            echo '<script language="javascript">alert("添加成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Resource/index').'";</script>';die();
        }
        $this->display();
    }

    // 资源编辑
    public function resource_edit()
    {
        $this->action = 'resource_edit';
        $_where['a.c_id'] = I('Id');
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $vo = M('Resource as a')->join($join)->limit($limit)->where($_where)->field($field)->find();
        $imgwhere['c_sourceid'] = 2;
        $imgwhere['c_regionid'] = $vo['c_id'];
        $vo['c_img'] = M('Resource_img')->where($imgwhere)->field('c_img')->select();
        $this->assign('vo',$vo);

        if (IS_POST) {
            if (empty($_POST['title'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('资源标题不能为空');
            }
            if (empty($_POST['ucode'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择对应的用户');
            }
            if (empty($_POST['content'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('资源内容不能为空');
            }
            if (count($_POST['img']) == 0) {
                A('Common')->del_img($_POST['img']);
                $this->error('图片不能为空');
            }
            if ($_POST['status'] == '') {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择资源发布状态');
            }

            $Model = M('');
            $Model->startTrans();
            $data['c_title'] = $_POST['title'];
            $data['c_content'] = $_POST['content'];
            $data['c_ucode'] = $_POST['ucode'];
            $data['c_click'] = $_POST['click'];
            $data['c_status'] = $_POST['status'];
            $data['c_updatetime'] = date('Y-m-d H:i:s');
            $where['c_id'] = $_POST['Id'];
            $result = M('Resource')->where($where)->save($data);
            if(!$result){
                A('Common')->del_img($_POST['img']);
                $Model->rollback();
                $this->error('添加失败1');
            }

            $imgwhere['c_regionid'] = $_POST['Id'];
            $imgwhere['c_sourceid'] = 2;
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
                $imglist['c_sourceid'] = 2;
                $result = M('Resource_img')->add($imglist);
                if(!$result){
                    A('Common')->del_img($_POST['img']);
                    $Model->rollback();
                    $this->error('添加失败');
                }
            }

            $Model->commit();
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Resource/index').'";</script>';die();
        }
        $this->display('resource_add');
    }

    // 资源删除
    public function resource_delete()
    {
        $Model = M('');
        $Model->startTrans();
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Resource')->where($where)->delete();
        if (!$result) {
            $Model->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $imgarr = explode('|',$Id);
        foreach ($imgarr as $key => $value) {
            $imgwhere['c_regionid'] = $value;
            $imgwhere['c_sourceid'] = 2;
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


}