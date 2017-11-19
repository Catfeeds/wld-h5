<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  资源管理
 */
class Resourcev2Controller extends BaseController {

 	// 资源列表
 	public function index()
 	{
        $Id = I('Id');
        if (!empty($Id)) {
            $where['a.c_id'] = $Id;
        }
        if (!empty($_GET['nickname'])) {
            $where['b.c_nickname'] = array('like','%'.$_GET['nickname'].'%');
        }
        if (!empty($_GET['title'])) {
            $where['a.c_content'] = array('like','%'.$_GET['title'].'%');
        }
        $citycode = I('citycode');
        if (!empty($citycode)) {
            $where['a.c_citycode'] = $citycode;
        }
        $name = I('name');
        if (!empty($name)) {
            $where['c.c_name'] = array('like','%'.$name.'%');
        }
        $parent = I('param.');
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $join1 = 'left join t_circle as c on a.c_citycode=c.c_citycode';
        $Resource = M('Resource as a');

        $count = $Resource->join($join)->join($join1)->where($where)->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $field = 'a.*,b.c_nickname,b.c_headimg,c.c_name as circlename';
        $order = 'a.c_istop desc,a.c_id desc';
        $data = $Resource->join($join)->join($join1)->limit($limit)->order('c_addtime desc')->where($where)->field($field)->order($order)->select();
        foreach ($data as $key => $value) {
            $imgwhere['c_sourceid'] = 2;
            $imgwhere['c_regionid'] = $value['c_id'];
            $data[$key]['c_img'] = M('Resource_img')->where($imgwhere)->field('c_img')->select();
            $productwhere['a.c_resourceid'] = $value['c_id'];
            $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
            $field = 'b.c_name,a.c_pcode';
            $data[$key]['produce'] = M('Resource_product as a')->join($join)->where($productwhere)->field($field)->select();
        }
        $this->assign('data',$data);
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->post = $parent;
        $this->show();
 	}

 	// 资源添加
    public function resource_add()
    {
        $this->action = 'resource_add';
        if (IS_POST) {
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
            if ($_POST['isaddress'] == 1) {
                if (empty($_POST['longitude']) || empty($_POST['latitude']) || empty($_POST['address'])) {
                    A('Common')->del_img($_POST['img']);
                    $this->error('请完善地址信息');
                }
            }

            $Model = M('');
            $Model->startTrans();
            $data['c_content'] = $_POST['content'];
            $data['c_ucode'] = $_POST['ucode'];
            $data['c_click'] = $_POST['click'];
            $data['c_status'] = $_POST['status'];
            $data['c_isaddress'] = $_POST['isaddress'];
            $data['c_latitude'] = $_POST['latitude'];
            $data['c_longitude'] = $_POST['longitude'];
            $data['c_address'] = $_POST['address'];
            $data['c_istop'] = $_POST['istop'];
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

            //添加关联产品
            if (!empty($_POST['pcode'])) {
                $relaproduce['c_resourceid'] = $regionid;
                $relaproduce['c_pcode'] = $_POST['pcode'];
                $result = M('Resource_product')->add($relaproduce);
                if (!$result) {
                    A('Common')->del_img($_POST['img']);
                    $Model->rollback();
                    $this->error('添加失败');
                }
            }


            $Model->commit();
            echo '<script language="javascript">alert("添加成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Resourcev2/index').'";</script>';die();
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
        $productwhere['a.c_resourceid'] = $vo['c_id'];
        $join = 'left join t_product as b on a.c_pcode=b.c_pcode';
        $field = 'b.c_name,a.c_pcode';
        $vo['produce'] = M('Resource_product as a')->join($join)->where($productwhere)->field($field)->select();
        $this->assign('vo',$vo);

        if (IS_POST) {
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
            if ($_POST['isaddress'] == 1) {
                if (empty($_POST['longitude']) || empty($_POST['latitude']) || empty($_POST['address'])) {
                    A('Common')->del_img($_POST['img']);
                    $this->error('请完善地址信息');
                }
            }
            $Model = M('');
            $Model->startTrans();
            $data['c_content'] = $_POST['content'];
            $data['c_ucode'] = $_POST['ucode'];
            $data['c_click'] = $_POST['click'];
            $data['c_status'] = $_POST['status'];
            $data['c_isaddress'] = $_POST['isaddress'];
            $data['c_latitude'] = $_POST['latitude'];
            $data['c_longitude'] = $_POST['longitude'];
            $data['c_address'] = $_POST['address'];
            $data['c_istop'] = $_POST['istop'];
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

            //添加关联产品
            if (!empty($_POST['pcode'])) {
                $relaproduce['c_resourceid'] = $_POST['Id'];
                $result = M('Resource_product')->where($relaproduce)->delete();
                $relaproduce['c_pcode'] = $_POST['pcode'];
                $result = M('Resource_product')->add($relaproduce);
                if (!$result) {
                    A('Common')->del_img($_POST['img']);
                    $Model->rollback();
                    $this->error('添加失败');
                }
            }


            $Model->commit();
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Resourcev2/index').'";</script>';die();
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

        $lcwhere['c_resourceid'] = array('in',$idstr);
        $result = M('Resource_like')->where($lcwhere)->delete();
        $result = M('Resource_comment')->where($lcwhere)->delete();
        $Model->commit();
        $this->ajaxReturn(Message(0,'删除成功'));

    }

    //点赞列表
    public function praiselist()
    {
        $this->rid = I('rid');
        if (!empty($_GET['nickname'])) {
            $where['b.c_nickname'] = array('like','%'.$_GET['nickname'].'%');
        }
        $where['a.c_resourceid'] = $this->rid;
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $Resource = M('Resource_like as a');
        $count = $Resource->join($join)->where($where)->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $data = $Resource->join($join)->limit($limit)->order('c_addtime desc')->where($where)->field($field)->select();
        $this->assign('data',$data);
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->display();
    }

    //新增点赞
    public function praiseadd()
    {
        $this->rid = I('rid');
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));

            if (empty($data['ucode'])) {
                $this->ajaxReturn(Message(1003,'请选择用户'));
            }
            $prasedata['c_ucode'] = $data['ucode'];
            $prasedata['c_resourceid'] = $data['rid'];
            $result = M('Resource_like')->where($prasedata)->find();
            if ($result) {
                $this->ajaxReturn(Message(1000,'该用户已点赞'));
            }
            $db = M('');
            $db->startTrans();
            $prasedata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Resource_like')->add($prasedata);
            if (!$result) {
                $db->rollback();
                $this->ajaxReturn(Message(1000,'添加失败'));
            }

            $result = M('Resource')->where('c_id='.$data['rid'])->setInc('c_like',1);
            if (!$result) {
                $db->rollback();
                $this->ajaxReturn(Message(1000,'添加失败'));
            }
            $db->commit();
            $this->ajaxReturn(Message(0,'添加成功'));
        }
        $this->display();
    }

    // 点赞删除
    public function praisedel()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $db = M('');
        $db->startTrans();
        $result = M('Resource_like')->where($where)->delete();
        if (!$result) {
            $db->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
        $arr = explode('|',$Id);
        $rwhere['c_id'] = I('rid');
        $result = M('Resource')->where($rwhere)->setDec('c_like',count($arr));
        if (!$result) {
            $db->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
        $db->commit();
        $this->ajaxReturn(Message(0,'删除成功'));
    }

    //评论列表
    public function commentlist()
    {
        $this->rid = I('rid');
        $this->data = $this->get_commentlist($this->rid,1);
        $this->display();
    }

    //评论添加
    public function commentadd()
    {
        $this->rid = I('rid');
        $this->bid = I('bid');
        $this->ptid = I('ptid');
        $this->upucode = I('upucode');
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));

            if (empty($data['ucode'])) {
                $this->ajaxReturn(Message(1003,'请选择用户'));
            }
            $prasedata['c_ucode'] = $data['ucode'];
            $prasedata['c_resourceid'] = $data['rid'];
            $db = M('');
            $db->startTrans();
            if (!empty($data['bid'])) {
                $prasedata['c_bid'] = $data['bid'];
                $prasedata['c_upucode'] = $data['upucode'];
                $prasedata['c_ptid'] = $data['ptid'];
            }
            $prasedata['c_state'] = $data['state'];
            $prasedata['c_content'] = $data['content'];
            $prasedata['c_address'] = $data['address'];
            $prasedata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Resource_comment')->add($prasedata);
            $tpid = $result;
            if (!$result) {
                $db->rollback();
                $this->ajaxReturn(Message(1000,'添加失败'));
            }

            if (empty($data['bid'])) {
                $prasesave['c_ptid'] = $tpid;
                $curwhere['c_id'] = $tpid;
                $result = M('Resource_comment')->where($curwhere)->save($prasesave);
                if (!$result) {
                    $db->rollback();
                    $this->ajaxReturn(Message(1000,'添加失败'));
                }
            }
            $result = M('Resource')->where('c_id='.$data['rid'])->setInc('c_comment',1);
            if (!$result) {
                $db->rollback();
                $this->ajaxReturn(Message(1000,'添加失败'));
            }
            $db->commit();
            $this->ajaxReturn(Message(0,'添加成功'));
        }
        $this->display();
    }

    //评论删除
    public function commentdel()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $db = M('');
        $db->startTrans();
        $result = M('Resource_comment')->where($where)->delete();
        if (!$result) {
            $db->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $where1['c_ptid'] = array('in',$idstr);
        $result = M('Resource_comment')->where($where1)->delete();
        // if (!$result) {
        //     $db->rollback();
        //     $this->ajaxReturn(Message(1000,'删除失败'));
        // }

        $arr = explode('|',$Id);
        $rwhere['c_id'] = I('rid');
        $result = M('Resource')->where($rwhere)->setDec('c_comment',count($arr));
        if (!$result) {
            $db->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
        $db->commit();
        $this->ajaxReturn(Message(0,'删除成功'));
    }

    //获取资源评论列表
    public function get_commentlist($id,$flag)
    {
        $db = M('resource_comment as r');

        $join = 'left join t_users as u on r.c_ucode=u.c_ucode';
        $join1 = 'left join t_users as pu on r.c_upucode=pu.c_ucode';
        $field = 'r.*,u.c_ucode,u.c_headimg,u.c_nickname,pu.c_ucode as upucode,pu.c_headimg as upheadimg,pu.c_nickname as upnickname';

        $commentwhere['r.c_resourceid'] = $id;
        $commentwhere['r.c_state'] = 1;

        $order = 'r.c_ptid asc,r.c_addtime asc';
        if($flag == 0){
            $comment = $db->join($join)->join($join1)->where($commentwhere)->order($order)->limit(3)->field($field)->select();
        }else{
            $comment = $db->join($join)->join($join1)->where($commentwhere)->order($order)->field($field)->select();
        }

        foreach ($comment as $key => $value) {
            $comment[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
            $comment[$key]['upucode'] = IGD('Resourcev2','Trade')->IsNull($value['upucode']);
            $comment[$key]['upheadimg'] = IGD('Resourcev2','Trade')->IsNull($value['upheadimg'],1);
            $comment[$key]['upnickname'] = IGD('Resourcev2','Trade')->IsNull($value['upnickname']);
        }
        if ($comment) {
            $comment = $this->toLayer($comment,'child',0,$parr['lastptid']);
        }
        if(count($comment) == 0){
            $comment = array();
        }
        return $comment;
    }

    //组成多维数组
    public function toLayer($cate, $name = 'child', $pid = 0){
        $arr = array();
        foreach ($cate as $v) {
            if ($v['c_bid'] == $pid) {
                $v[$name] = self::toLayer($cate, $name, $v['c_id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }


    //banner列表
    public function banner_list(){
        $db = M('banner as b');
        //条件

        $c_title = trim(I('c_title'));
        if (!empty($c_title)) {
            $w['b.c_title'] = array('like', "%{$c_title}%");
        }
        $source = trim(I('source'));
        if (!empty($source)) {
            $w['b.c_source'] = $source;
        }
        $state = trim(I('state'));
        if (!empty($state)) {
            if($state == 1){
              $w['b.c_state'] = 1;
            }else{
              $w['b.c_state'] = 0;
            }
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'b.c_sort asc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->root_url = GetHost().'/';
        $this->display();
    }

    function checkmenu($data){
        if (empty($data['title'])) {
            $this->error("标题不能为空");
        }
        if (empty($data['tagvalue'])) {
            $this->error("附加值不能为空");
        }
        if (empty($data['weburl'])) {
            $this->error("网页链接不能为空");
        }
        if (empty($data['sort'])) {
            $this->error("排序不能为空");
        }
    }

    //banner 添加
    public function banner_add(){
        $this->action = 'banner_add';

        if(IS_POST){
            $db = M('banner');

            $this->checkmenu($_POST);

            $fileresult = uploadimg('banner');
            if ($fileresult['code'] != 0) {
                $this->error($fileresult['msg']);
            }
            $data['c_img'] = $fileresult['data']['img'];
            $data['c_title'] = $_POST['title'];
            $data['c_weburl'] = $_POST['weburl'];
            $data['c_tag'] = $_POST['tag'];
            $data['c_sort'] = $_POST['sort'];
            $data['c_tagvalue'] = $_POST['tagvalue'];
            $data['c_source'] = $_POST['source'];
            $data['c_state'] = $_POST['state'];
            $data['c_addtime'] = Date('Y-m-d H:i:s');

            $result = $db->add($data);
            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Resourcev2/banner_list';
              echo '<script language="javascript">alert("添加成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('添加失败！');
            }
        }
        $this->display();
    }

    //banner 编辑
    public function banner_edit(){
        $this->action = 'banner_edit';
        $Id = I('Id');
        $w['c_id'] = $Id;
        $this->vo = M('banner')->where($w)->find();
        if(IS_POST){
            $db = M('banner');

            $this->checkmenu($_POST);

            if(!empty($_FILES['img']['name'])){
                $fileresult = uploadimg('appmenu');
                if ($fileresult['code'] != 0) {
                  $this->error($fileresult['msg']);
                }
                $data['c_img'] = $fileresult['data']['img'];
            }

            $data['c_title'] = $_POST['title'];
            $data['c_weburl'] = $_POST['weburl'];
            $data['c_tag'] = $_POST['tag'];
            $data['c_sort'] = $_POST['sort'];
            $data['c_tagvalue'] = $_POST['tagvalue'];
            $data['c_source'] = $_POST['source'];
            $data['c_state'] = $_POST['state'];
            $data['c_addtime'] = Date('Y-m-d H:i:s');

            $w1['c_id'] = $_POST['Id'];
            $result = $db->where($w1)->save($data);
            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Resourcev2/banner_list';
              echo '<script language="javascript">alert("编辑成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('编辑失败！');
            }
        }
        $this->display('banner_add');
    }

    //banner 删除
    public function banner_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $imgs = M('banner')->field('c_img')->where($where)->select();
        $result = M('banner')->where($where)->delete();
        if($result){
            foreach($imgs as $key=>$value){
                unlink($value['c_img']);
            }
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }



    //举报规则列表
    public function tipsinfo_list()
    {   
    
        $Resourcev2 = M('Tipsinfo');
        $count = $Resourcev2->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $arr = $Resourcev2->limit($limit)->order('c_ctime desc')->select();
        $this->data = $arr;
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->display();
    }

    //规则添加   
    public function tipsinfo_list_add()
    {
        $this->action = 'Resourcev2/tipsinfo_list_add';
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['content'])) {
                $this->ajaxReturn(Message(1000,'规则不能为空'));
            }
            if ($jsondata['flag'] == '') {
                $this->ajaxReturn(Message(1001,'选择是否启用'));
            }
            $data['c_content'] = $jsondata['content'];
            $data['c_flag'] = $jsondata['flag'];
            $data['c_ctime'] = date('Y-m-d H:i:s');
            $result = M('Tipsinfo')->add($data);
            if($result){
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1002,'添加失败'));
            }
        }
        $this->display();
    }


    // 编辑
    public function type_edit()
    {
        $this->action = 'Resourcev2/type_edit';
        $where['c_tid'] = I('Id');
        $this->vo = M('Tipsinfo')->where($where)->find();
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['content'])) {
                $this->ajaxReturn(Message(1000,'规则不能为空'));
            }
            if ($jsondata['flag'] == '') {
                $this->ajaxReturn(Message(1001,'选择是否启用'));
            }
            $data['c_content'] = $jsondata['content'];
            $data['c_flag'] = $jsondata['flag'];
            $where['c_tid'] = $jsondata['Id'];
            $result = M('Tipsinfo')->where($where)->save($data);
            if($result){
                $this->ajaxReturn(Message(0,'编辑成功'));
            }else{
                $this->ajaxReturn(Message(1000,'编辑失败'));
            }
        }
        $this->display('tipsinfo_list_add');
    }


    // 规则删除
    public function type_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_tid'] = array('in',$idstr);
        $result = M('Tipsinfo')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

}