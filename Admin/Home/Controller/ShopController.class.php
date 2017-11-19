<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  商城管理
 */
class ShopController extends BaseController {

 	  // 产品列表
    public function index(){
        $hide = I('hide');
        if (!empty($hide)) {
            $w['p.c_ishow'] = 1;
            $w['p.c_isdele'] = 1;
            $this->hide = 'ctrl_hidden';
        }
        $db = M('product as p');
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['u.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['p.c_name'] = array('like', "%{$c_name}%");
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }
        $categoryid = trim(I('categoryid'));
        if (!empty($categoryid)) {
            $w['p.c_categoryid'] = $categoryid;
        }
        $ishow = trim(I('ishow'));
        if (!empty($ishow)) {
            $w['p.c_ishow'] = $ishow;
        }
        $source = trim(I('source'));
        if (!empty($source)) {
            $w['p.c_source'] = $source;
        }
        $pcode = trim(I('pcode'));
        if (!empty($pcode)) {
            $w['p.c_pcode'] = $pcode;
        }

        $w['c_isdele'] = 1;//显示不删除的产品
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'p.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,u.c_nickname,u.c_phone,c.c_category_name';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=p.c_ucode';
        $panrn['join1'] = 'left join t_category as c on c.c_id=p.c_categoryid';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->page = $date['Page'];//分页
        $this->count = $date['count'];//分页\
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->category = M('category')->select();
        $this->show();
    }

    // 产品添加
    public function product_add()
    {
        $this->action = 'product_add';
        if(IS_POST){
            $ucode = trim($_POST['ucode']);

            //查询商家类型 实体或者线上商家
            $w['c_ucode'] = $ucode;
            $shoptype = M('User_local')->where($w)->getField('c_isfixed');
            if($shoptype == 1){
                $data['c_source'] = 2;
            }

            $data['c_pcode'] = 'p'.time();
            $data['c_name'] = trim($_POST['c_name']);
            $data['c_ucode'] = $ucode;
            $data['c_desc'] = trim($_POST['c_desc']);
            $data['c_price'] = $_POST['c_price'];
            $data['c_num'] = $_POST['c_num'];
            $data['c_salesnum'] = $_POST['c_salesnum'];
            $data['c_categoryid'] = $_POST['c_categoryid'];
            $data['c_isfree'] = intval($_POST['c_isfree']);
            if($data['c_isfree'] == 2){
                $data['c_freeprice'] = $_POST['c_freeprice'];
            }else{
                $data['c_freeprice'] = '';
            }
            $data['c_isshoptuijian'] = $_POST['c_isshoptuijian'];
            $data['c_isrebate'] = intval($_POST['c_isrebate']);
            if($data['c_isrebate'] == 1){
                $data['c_rebate_proportion'] = $_POST['c_rebate_proportion'];
            }else{
                $data['c_rebate_proportion'] = '';
            }
            $data['c_isspread'] = intval($_POST['c_isspread']);
            if($data['c_isspread'] == 1){
                $data['c_spread_proportion'] = $_POST['c_spread_proportion'];
            }else{
                $data['c_spread_proportion'] = '';
            }

            $data['c_addtime'] = date('Y-m-d H:i:s',time());
            $data['c_updatetime'] = date('Y-m-d H:i:s',time());

            $result = M('product')->add($data);

            if(!$result) {
                $this->error('添加失败');
            }
            echo '<script language="javascript">alert("添加成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Shop/index').'";</script>';die();
        }
        $this->category = M('category')->select();
        $this->display();
    }

    /**
     *  生成产品编码
     *  @param
     */
    function CreateUcode($prefix) {
        //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 4);
        $uuid .= substr($str, 8, 2);
        $uuid .= substr($str, 12, 3);
        $uuid .= substr($str, 16, 2);
        $uuid .= substr($str, 20, 5);
        return $prefix . $uuid;
    }

    // 产品编辑
    public function product_edit()
    {
        $Id = I('Id');
        $where['c_id'] = intval($Id);
        $pr_info = M('Product')->where($where)->find();

        $ucode = $pr_info['c_ucode'];
        $w['c_ucode'] = $ucode;
        $pr_info['c_nickname'] = M('users')->where($w)->getField('c_nickname');

        $this->data = $pr_info;
    	$this->action = 'product_edit';
        $this->category = M('category')->select();
        $this->ucode = M('users')->where('c_shop=1')->getField('c_ucode,c_nickname');
        if(IS_POST){
            $where['c_id'] = $_POST['c_id'];
            $prinfo = M('Product')->where($where)->find();

            if(!empty($prinfo['c_agent_pcode'])){
                $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Shop/index';
                echo '<script language="javascript">alert("代理商品无权编辑");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }

            $data['c_id'] = $_POST['c_id'];
            $data['c_name'] = trim($_POST['c_name']);
            $data['c_ucode'] = trim($_POST['ucode']);
            $data['c_desc'] = trim($_POST['c_desc']);
            $data['c_price'] = $_POST['c_price'];
            $data['c_num'] = $_POST['c_num'];
            $data['c_salesnum'] = $_POST['c_salesnum'];
            $data['c_categoryid'] = $_POST['c_categoryid'];
            $data['c_isfree'] = intval($_POST['c_isfree']);
            if($data['c_isfree'] == 2){
                $data['c_freeprice'] = $_POST['c_freeprice'];
            }else{
                $data['c_freeprice'] = '';
            }
            $data['c_isshoptuijian'] = $_POST['c_isshoptuijian'];
            $data['c_isrebate'] = intval($_POST['c_isrebate']);
            if($data['c_isrebate'] == 1){
                $data['c_rebate_proportion'] = $_POST['c_rebate_proportion'];
            }else{
                $data['c_rebate_proportion'] = '';
            }
            $data['c_isspread'] = intval($_POST['c_isspread']);
            if($data['c_isspread'] == 1){
                $data['c_spread_proportion'] = $_POST['c_spread_proportion'];
            }else{
                $data['c_spread_proportion'] = '';
            }
            $data['c_updatetime'] = date('Y-m-d H:i:s',time());

            $result = M('product')->save($data);

            if(!$result) {
                $this->error('编辑失败');
            }
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Shop/index').'";</script>';die();
        }
    	$this->display('product_add');
    }

    //产品图片列表
    public function product_imgs(){
        $pcode = I('pcode');
        $w['c_pcode'] = $pcode;
        $parr['where'] = $w;
        $this->data = D('Db','Behind')->mate_select(M('product_img'),$parr);
        $this->count = M('product_img')->where($w)->count();
        $this->pcode = $pcode;
        $this->root_url = GetHost()."/";
        $this->display();
    }

    //产品图片设置为主图
    public function product_img_state()
    {
        $Id = I('gid');
        $sign = I('active');
        if(empty($Id)) die("非法操作，ID为空！");
        $where['c_id'] = $Id;
        $data['c_sign'] = $sign;
        $data['c_updatetime'] = date('Y-m-d H:i:s',time());
        $result = M('product_img')->where($where)->save($data);
        if(!$result){
            die("操作失败！");
        }
    }

    //产品图片添加
    public function product_img_add(){
        $pcode = I('pcode');
        $this->pcode = $pcode;
        $this->action = 'product_img_add';

        if(IS_POST){
            $pcode = $_POST['c_pcode'];
            $main_img = $_POST['main_img'];
            $imgs = $_POST['img'];

            if(empty($main_img)){
               $main_img = 0;
            }

            $db = M('product_img');
            $db -> startTrans();
            //是否存在主图
            $where['c_pcode'] = $pcode;
            $where['c_sign'] = 1;
            $count = $db->where($where)->count();

            if (count($imgs) == 0) {
                A('Common')->del_img($_POST['img']);
                $this->error('图片不能为空');
            }

            $main_imgpath = '';
            foreach ($imgs as $key => $value) {
                $imglist['c_pcode'] = $pcode;
                $imglist['c_pimgepath'] = $value;

                $mainId = intval($main_img)-1;
                if($count == 0 ){
                    if ($key == $mainId) {
                        $imglist['c_sign'] = 1;
                        $main_imgpath = $imgs[$mainId];
                    } else {
                        $imglist['c_sign'] = 0;
                    }
                }else{
                    if($main_img !== ''){
                        $Id = $db->where($where)->setField('c_sign',0);
                        if(!$Id){
                           $db->rollback();
                           $this->error('主图设置失败');
                        }
                        if($key == $mainId){
                            $imglist['c_sign'] = 1;
                            $main_imgpath = $imgs[$mainId];
                        }else{
                            $imglist['c_sign'] = 0;
                        }
                    }
                }
                $imglist['c_createtime'] = date('Y-m-d H:i:s',time());
                $imglist['c_updatetime'] = date('Y-m-d H:i:s',time());

                $result = $db->add($imglist);
                if(!$result){
                    A('Common')->del_img($_POST['img']);
                    $db->rollback();
                    $this->error('添加失败');
                }

                if($main_imgpath !== ''){
                    $whereinfo['c_pcode'] = $pcode;
                    $save_data['c_pimg'] = $main_imgpath;
                    $result1 = M('product')->where($whereinfo)->save($save_data);

                    if(!$result){
                        $db->rollback();
                        $this->error('主图保存失败');
                    }
                }
            }

            $db->commit();
            $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Shop/product_imgs?pcode='.$pcode;
            echo '<script language="javascript">alert("添加成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }
        $this->imgnum = 20;
        $this->display();
    }

    //产品图片编辑
    public function product_img_edit(){
        $pcode = I('pcode');
        $Id = I('Id');
        $where['c_id'] = $Id;
        $data = M('product_img')->where($where)->find();
        $data['c_img'] = array(array('c_img' => $data['c_pimgepath']));
        $this->vo = $data;
        $this->pcode = $pcode;
        $this->action = 'product_img_edit';
        if(IS_POST){
            $pcode = $_POST['c_pcode'];
            $c_sign = $_POST['c_sign'];
            $id = $_POST['c_id'];

            $main_imgpath = $_POST['img'][0];

            $db = M('product_img');
            $db -> startTrans();

            $where1['c_sign'] = 1;
            $isgn_id =  $db->where($where1)->getField('c_id');//主图id

            if($id !== $isgn_id){//不是主图
                if($c_sign == 1){
                    $result1 =  $db->where($where1)->setField('c_sign',0);
                    $w['c_pcode'] = $pcode;
                    $result2 = M('product')->where($w)->setField('c_pimg',$main_imgpath);
                    if(!$result1 || !$result2){
                        $db->rollback();
                        $this->error('主图设置失败');
                    }
                }
            }else{
                if($c_sign == 0){
                     $this->error('无法修改,产品必须存在主图');
                }
            }

            $imglist['c_id'] = $id;
            $imglist['c_sign'] = $c_sign;
            $imglist['c_pimgepath'] = $main_imgpath;
            $imglist['c_pcode'] = $pcode;
            $imglist['c_updatetime'] = date('Y-m-d H:i:s',time());

            $result =  $db->save($imglist);

            if($result || $result == 0){
                 $db->commit();
                 $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Shop/product_imgs?pcode='.$pcode;
                echo '<script language="javascript">alert("编辑成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
                A('Common')->del_img($_POST['img']);
                $db->rollback();
                $this->error('编辑失败');
            }
        }
        $this->imgnum = 1;
        $this->display('product_img_add');
    }

    //图片删除
    public function product_img_del(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('product_img')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }


    // 产品上下架
    public function product_state()
    {
    	$Id = I('gid');
        $ishow = I('active');
        if(empty($Id)) die("非法操作，ID为空！");

        $where['c_id'] = $Id;
        $productinfo = M('product')->where($where)->field('c_pcode,c_source,c_agent_pcode')->find();
        if(!empty($productinfo['c_agent_pcode'])){
            die("代理商品无权上下架！");
        }

        if($ishow == 1){
            if($productinfo['c_source'] == 1){
                $whereinfo['c_pcode'] =  $productinfo['c_pcode'];
                $model_list = M('product_model')->where($whereinfo)->select();
                if(count($model_list) == 0) die("产品型号为空，不能上架！");
                foreach ($model_list as $k => $v) {
                    $w['c_pcode'] = $v['c_pcode'];
                    $w['c_mcode'] = $v['c_mcode'];
                    $ladderprice = M('product_ladderprice')->where($w)->count();
                    if($ladderprice < 3) die("产品型号价必须存在三条，否则不能上架！");
                }
            }
        }

        $data['c_ishow'] = $ishow;
        $result = M('product')->where($where)->save($data);
        if(!$result){
            die("操作失败！");
        }
    }

    // 产品删除
    public function product_del()
    {
    	$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
        $data['c_isdele'] = 2;
		$result = M('Product')->where($where)->save($data);
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
    }


	// 产品型号列表
    public function productmodel_list()
    {
        $pcode = I('pcode');
        $w['c_pcode'] = $pcode;
        $parr['where'] = $w;
        $this->data = D('Db','Behind')->mate_select(M('product_model'),$parr);
        $this->count = M('product_model')->where($w)->count();
        $this->pcode = $pcode;
    	$this->display();
    }

    // 产品型号添加
    public function productmodel_add()
    {
        $pcode = I('pcode');
        $this->action = 'Shop/productmodel_add';
        $this->pcode = $pcode;
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));
            if (empty($data['c_name']) || empty($data['c_price']) || ($data['c_num'] == '')) {
                $this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
            }

            $db = M('product_model');
            $db -> startTrans();

            $where['c_pcode'] = $data['c_pcode'];
            $count = M('product_model')->where($where)->count();

            $data['c_mcode'] = $this->CreateUcode("m");
            $data['c_addtime'] = date('Y-m-d H:i:s',time());

            $result = $db->add($data);
            if(!$result){
                $db ->rollback();
                $this->ajaxReturn(Message(1002,"添加失败"));
            }
            //第一个添加，修改产品表c_ismodel字段
            if($count == 0){
                if(!(M('product')->where($where)->setField('c_ismodel',1))){
                    $db ->rollback();
                    $this->ajaxReturn(Message(1003,"修改产品表ismodel失败"));
                }
            }
            $db->commit();
            $this->ajaxReturn(Message(0,"添加成功"));
        }
    	$this->display();
    }

    // 产品型号编辑
    public function productmodel_edit()
    {
        $pcode = I('pcode');
        $mcode = I('mcode');
        $this->action = 'Shop/productmodel_edit';
        $this->pcode = $pcode;
        $where['c_mcode'] = $mcode;
        $this->data = M('product_model')->where($where)->find();
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));
            if (empty($data['c_name']) || empty($data['c_price']) || ($data['c_num'] == '')) {
                $this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
            }

            $result = M('product_model')->save($data);
            if(!$result){
                $this->ajaxReturn(Message(1002,"添加失败"));
            }
            $this->ajaxReturn(Message(0,"添加成功"));
        }
    	$this->display('productmodel_add');
    }

    // 产品型号删除
    public function productmodel_del()
    {
    	$Id = I('Id');
        $pcode = I('pcode');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);

        $db = M('Product_model');
        $db -> startTrans();

		$result = $db->where($where)->delete();

        if(!$result){
            $db ->rollback();
            $this->ajaxReturn(Message(1001,'删除失败'));
        }

        $where1['c_pcode'] = $pcode;
        $count = $db->where($where1)->count();

        if($count == 0){
            $result1 = M('product')->where($where1)->setField('c_ismodel',0);
            if(!$result){
                $db ->rollback();
                $this->ajaxReturn(Message(1002,"修改产品表ismodel失败"));
            }
        }

        $db -> commit();
		$this->ajaxReturn(Message(0,'删除成功'));
    }

    //型号阶梯价格列表
    public function ladderprice(){
        $pcode = I('pcode');
        $mcode = I('mcode');
        $w['c_pcode'] = $pcode;
        $w['c_mcode'] = $mcode;
        $parr['where'] = $w;
        $this->data = D('Db','Behind')->mate_select(M('product_ladderprice'),$parr);
        $this->count = M('product_ladderprice')->where($w)->count();
        $this->pcode = $pcode;
        $this->mcode = $mcode;
        $this->display();
    }

    // 型号阶梯价格添加
    public function ladderprice_add()
    {
        $pcode = I('pcode');
        $mcode = I('mcode');
        $this->action = 'Shop/ladderprice_add';
        $this->pcode = $pcode;
        $this->mcode = $mcode;
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));
            if ($data['c_minnum'] =='' || empty($data['c_maxnum']) || empty($data['c_price'])) {
                $this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
            }

            $result = M('product_ladderprice')->add($data);
            if(!$result){
                $this->ajaxReturn(Message(1002,"添加失败"));
            }
            $this->ajaxReturn(Message(0,"添加成功"));
        }
        $this->display();
    }

    //型号阶梯价格编辑
    public function ladderprice_edit()
    {
        $Id = I('Id');
        $pcode = I('pcode');
        $mcode = I('mcode');
        $this->action = 'Shop/ladderprice_edit';
        $this->pcode = $pcode;
        $this->mcode = $mcode;
        $where['c_id'] = $Id;
        $this->data = M('product_ladderprice')->where($where)->find();
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));
            if ($data['c_minnum'] =='' || empty($data['c_maxnum']) || empty($data['c_price'])) {
                $this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
            }

            $result = M('product_ladderprice')->save($data);
            if(!$result){
                $this->ajaxReturn(Message(1002,"添加失败"));
            }
            $this->ajaxReturn(Message(0,"添加成功"));
        }
        $this->display('ladderprice_add');
    }

    // 型号阶梯价格删除
    public function ladderprice_del()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('product_ladderprice')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //  平台产品分类列表
    public function categorylist()
    {
        if (!empty($_POST['category_name'])) {
            $where['c_category_name'] = array('like','%'.$_POST['category_name'].'%');
        }
        $Category = M('Category');
        $count = $Category->where($where)->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $this->data = $Category->limit($limit)->where($where)->order('c_addtime desc')->select();
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->display();
    }

    // 商家产品分类添加
    public function category_add()
    {
        $this->action = 'category_add';
        if (IS_POST) {

            if (empty($_POST['category_name'])) {
                $this->error('品类名不能为空');
            }
            if (empty($_POST['desc'])) {
                $this->error('品类描述不能为空');
            }
            if(empty($_FILES['img']['name'])){
                $this->error("展示图必须上传");
            }
            $fileresult = uploadimg('produce');
            if ($fileresult['code'] != 0) {
              $this->error($fileresult['msg']);
            }
            $data['c_category_name'] = $_POST['category_name'];
            $data['c_desc'] = $_POST['desc'];
            $data['c_img'] = $fileresult['data']['img'];
            $data['c_isshow'] = $_POST['isshow'];
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Category')->add($data);
            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Shop/categorylist';
              echo '<script language="javascript">alert("添加成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('添加失败！');
            }
        }
        $this->display();
    }

    //  平台产品分类编辑
    public function category_edit()
    {
        $this->action = 'category_edit';
        $where['c_id'] = I('Id');
        $this->vo = M('Category')->where($where)->find();
        if (IS_POST) {
            if (empty($_POST['category_name'])) {
                $this->error('品类名不能为空');
            }
            if (empty($_POST['desc'])) {
                $this->error('品类描述不能为空');
            }

            $fileresult = uploadimg('produce');
            if(!empty($_FILES['img']['name'])){
                if ($fileresult['code'] != 0) {
                  $this->error($fileresult['msg']);
                }
                $data['c_img'] = $fileresult['data']['img'];
            }

            $data['c_category_name'] = $_POST['category_name'];
            $data['c_desc'] = $_POST['desc'];
            $data['c_isshow'] = $_POST['isshow'];
            $where['c_id'] = $_POST['Id'];
            $result = M('Category')->where($where)->save($data);
            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Shop/categorylist';
              echo '<script language="javascript">alert("编辑成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('编辑失败！');
            }
        }
        $this->display('category_add');
    }

    //  平台产品分类删除
    public function category_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Category')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    // //  商家产品分类列表
    // public function typelist()
    // {
    //     if (!empty($_POST['c_category_name'])) {
    //         $where['a.c_category_name'] = array('like','%'.$_POST['c_category_name'].'%');
    //     }
    //     if (!empty($_POST['c_nickname'])) {
    //         $where['b.c_nickname'] = array('like','%'.$_POST['c_nickname'].'%');
    //     }

    //     $Product_category = M('Product_category as a');
    //     $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
    //     $field = 'a.*,b.c_nickname';
    //     $count = $Product_category->join($join)->where($where)->count();
    //     $page = getpage($count,25);
    //     $limit = $page->firstRow.','.$page->listRows;
    //     $this->data = $Product_category->join($join)->where($where)->limit($limit)->order('a.c_addtime desc')->select();
    //     $this->page = $page->show();
    //     $this->assign('count',$count);
    //     $this->display();
    // }
    
    //商家产品分类列表
    public function typelist(){
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
        
         $c_category_name = trim(I('c_category_name'));
        if (!empty($c_category_name)) {
            $w['a.c_category_name'] = array('like', "%{$c_category_name}%");
        }

        $db = M('Product_category as a');
        
        $w['c_isdel'] = 0;//显示正常的产品
        $panrn['where'] = $w;
        $panrn['order'] = 'a.c_order asc';//排序
        $parent = I('param.');
        $panrn['limit'] = 25;//分页数
        //分页显示数据
        $panrn['field'] = 'a.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
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
        // dump($data_list);die();
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;

        $this->display();

    }
    // 商家产品分类添加
    public function type_add()
    {
        $this->action = 'Shop/type_add';
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['ucode'])) {
                $this->ajaxReturn(Message(1000,'请选择用户'));
            }
            if (empty($jsondata['c_category_name'])) {
                $this->ajaxReturn(Message(1000,'分类名不能为空'));
            }
            if (empty($jsondata['c_desc'])) {
                $this->ajaxReturn(Message(1000,'分类描述不能为空'));
            }
            $data['c_ucode'] = $jsondata['ucode'];
            $data['c_category_name'] = $jsondata['c_category_name'];
            $data['c_desc'] = $jsondata['c_desc'];
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Product_category')->add($data);
            if($result){
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1000,'添加失败'));
            }
        }
        $this->display();
    }

    //  商家产品分类编辑
    public function type_edit()
    {
        $this->action = 'Shop/type_edit';
        $_where['a.c_id'] = I('Id');
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname';
        $this->vo = M('Product_category as a')->join($join)->where($_where)->field($field)->find();
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['ucode'])) {
                $this->ajaxReturn(Message(1000,'请选择用户'));
            }
            if (empty($jsondata['c_category_name'])) {
                $this->ajaxReturn(Message(1000,'分类名不能为空'));
            }
            if (empty($jsondata['c_desc'])) {
                $this->ajaxReturn(Message(1000,'分类描述不能为空'));
            }
            $data['c_ucode'] = $jsondata['ucode'];
            $data['c_category_name'] = $jsondata['c_category_name'];
             $data['c_desc'] = $jsondata['c_desc'];
            $where['c_id'] = $jsondata['Id'];
            $result = M('Product_category')->where($where)->save($data);
            if($result){
                $this->ajaxReturn(Message(0,'编辑成功'));
            }else{
                $this->ajaxReturn(Message(1000,'编辑失败'));
            }
        }
        $this->display('type_add');
    }

    //  商家产品分类删除
    public function type_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $save_data['c_isdel'] = 1;
        $result = M('Product_category')->where($where)->save($save_data);
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //产品评价表
    public function product_score()
    {
        $db = M('product_score as s');

        $w[] = array('s.c_pcode is not null');
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }

        $c_phones = trim(I('c_phones'));
        if (!empty($c_phones)) {
            $w['pu.c_phone'] = $c_phones;
        }
        $c_orderid = trim(I('c_orderid'));
        if (!empty($c_orderid)) {
            $w['s.c_orderid'] = $c_orderid;
        }
        $c_pname = trim(I('c_pname'));
        if (!empty($c_pname)) {
            $w['s.c_pname'] = array('like', "%{$c_pname}%");
        }

        $pcode = trim(I('pcode'));
        if (!empty($pcode)) {
            $w['s.c_pcode'] = $pcode;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 's.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 's.*,u.c_nickname,u.c_phone,pu.c_phone as c_phones,pu.c_nickname as dlname';
        $panrn['join'] = 'left join t_users as u on s.c_ucode=u.c_ucode';
        $panrn['join1'] = 'left join t_users as pu on s.c_acode=pu.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $list= $date['list'];
        foreach ($list as $key => $value) {
            $imgwhere['c_sourceid'] = 3;
            $imgwhere['c_regionid'] = $value['c_id'];
            $list[$key]['imgs'] = M('Resource_img')->where($imgwhere)->field('c_img')->select();
        }

        $this->list = $list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //产品评论添加
    public function score_add(){
        $this->action = 'score_add';
        if (IS_POST) {
            if (empty($_POST['pcode'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择对应的商品');
            }
            if (empty($_POST['ucode'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择对应的用户');
            }
            if (empty($_POST['content'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('评论内容不能为空');
            }

            $w['c_pcode'] = $_POST['pcode'];
            $pro_info = M('product')->field('c_ucode,c_name,c_pimg,c_agent_pcode')->where($w)->find();

            $Model = M('');
            $Model->startTrans();
            $data['c_ucode'] = $_POST['ucode'];
            $data['c_pcode'] = $_POST['pcode'];
            $data['c_agent_pcode'] = $pro_info['c_agent_pcode'];
            $data['c_acode'] = $pro_info['c_ucode'];
            $data['c_pname'] = $pro_info['c_name'];
            $data['c_pimg'] = $pro_info['c_pimg'];
            $data['c_score'] = $_POST['score'];
            $data['c_content'] = $_POST['content'];
            if(!empty($_POST['addtime'])){
                $data['c_addtime'] = $_POST['addtime'];
            }else{
                $data['c_addtime'] = date('Y-m-d H:i:s');
            }
            $result = M('product_score')->add($data);
            $regionid = $result;
            if(!$result){
                A('Common')->del_img($_POST['img']);
                $Model->rollback();
                $this->error('添加失败');
            }
            if(count($_POST['img']) !== 0){
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
                    $imglist['c_sourceid'] = 3;
                    $result = M('Resource_img')->add($imglist);
                    if(!$result){
                        A('Common')->del_img($_POST['img']);
                        $Model->rollback();
                        $this->error('添加失败');
                    }
                }
            }

            $Model->commit();
            echo '<script language="javascript">alert("添加成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Shop/product_score').'";</script>';die();
        }
        $this->display();
    }

    // 资源编辑
    public function score_edit()
    {
        $this->action = 'score_edit';
        $_where['s.c_id'] = I('Id');
        $join = 'left join t_users as b on s.c_ucode=b.c_ucode';
        $field = 's.*,b.c_nickname,b.c_headimg';
        $vo = M('product_score as s')->join($join)->limit($limit)->where($_where)->field($field)->find();
        $imgwhere['c_sourceid'] = 3;
        $imgwhere['c_regionid'] = $vo['c_id'];
        $vo['c_img'] = M('Resource_img')->where($imgwhere)->field('c_img')->select();
        $this->assign('vo',$vo);

        if (IS_POST) {

            if (empty($_POST['pcode'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择对应的商品');
            }
            if (empty($_POST['ucode'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('请选择对应的用户');
            }
            if (empty($_POST['content'])) {
                A('Common')->del_img($_POST['img']);
                $this->error('评论内容不能为空');
            }

            $w['c_pcode'] = $_POST['pcode'];
            $pro_info = M('product')->field('c_ucode,c_name,c_pimg,c_agent_pcode')->where($w)->find();

            $Model = M('');
            $Model->startTrans();
            $data['c_ucode'] = $_POST['ucode'];
            $data['c_pcode'] = $_POST['pcode'];
            $data['c_agent_pcode'] = $pro_info['c_agent_pcode'];
            $data['c_acode'] = $pro_info['c_ucode'];
            $data['c_pname'] = $pro_info['c_name'];
            $data['c_pimg'] = $pro_info['c_pimg'];
            $data['c_score'] = $_POST['score'];
            $data['c_content'] = $_POST['content'];
            if(!empty($_POST['addtime'])){
                $data['c_addtime'] = $_POST['addtime'];
            }else{
                $data['c_addtime'] = date('Y-m-d H:i:s');
            }
            $where['c_id'] = $_POST['Id'];
            $result = M('product_score')->where($where)->save($data);
            if(!$result){
                A('Common')->del_img($_POST['img']);
                $Model->rollback();
                $this->error('编辑失败');
            }

            if(count($_POST['img']) !== 0){
                $imgwhere['c_regionid'] = $_POST['Id'];
                $imgwhere['c_sourceid'] = 3;
                $result = M('Resource_img')->where($imgwhere)->delete();
                if(!$result){
                    A('Common')->del_img($_POST['img']);
                    $Model->rollback();
                    $this->error('删除图片失败');
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
                    $imglist['c_sourceid'] = 3;
                    $result = M('Resource_img')->add($imglist);
                    if(!$result){
                        A('Common')->del_img($_POST['img']);
                        $Model->rollback();
                        $this->error('添加失败');
                    }
                }
            }
            $Model->commit();
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Shop/product_score').'";</script>';die();
        }
        $this->display('score_add');
    }

    //产品评论删除
    public function score_del(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);

        $model = M('');
        $model->startTrans();

        $w['c_regionid'] = array('in',$idstr);
        $w['c_sourceid'] = 3;

        $imgs = M('Resource_img')->field('c_img')->where($w)->select();
        if(count($imgs) != 0){
            $result =   M('Resource_img')->where($w)->delete();
            if(!$result){
                $model->rollback();
                $this->ajaxReturn(Message(1000,'删除图片失败'));
            }
            A('Common')->del_img($imgs);
        }

        $where['c_id'] = array('in',$idstr);
        $result = M('product_score')->where($where)->delete();
        if(!$result){
            $model->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $model->commit();
        $this->ajaxReturn(Message(0,'删除成功'));
    }

    //产品访问记录
    public function product_visit()
    {
        $db = M('product_visit as p');

         //会员昵称       
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $wus['c_name'] = $c_name;
        }
        
        $c_pcode = trim(I('pcode'));
        if (!empty($c_pcode)) {
            $w['p.c_pcode'] = $c_pcode;
            $this->pcode = $c_pcode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Product')->where($wus)->field('c_pcode')->find();
            if ($usinfo) {
                $w['p.c_pcode'] = $usinfo['c_pcode'];
            }
        }
        // //条件
        // $name = trim(I('name'));
        // if (!empty($name)) {
        //     $w['b.c_name'] = array('like', "%{$name}%");
        // }
        $username = trim(I('username'));
        if (!empty($username)) {
            $w['p.c_username'] = array('like', "%{$username}%");
        }
        $pcode = trim(I('pcode'));
        if (!empty($pcode)) {
            $w['p.c_pcode'] = $pcode;
        }
        $source = trim(I('source'));
        if (!empty($source)) {
            $w['p.c_source'] = array('like', "%{$source}%");
        }

        $panrn['where'] = $w;
        $this->post = I('param.');
        $panrn['order'] = 'p.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*';
        // $panrn['join'] = 'left join t_product as b on p.c_pcode=b.c_pcode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_pcode'] = $v['c_pcode'];
            $userinfo = M('Product')->where($uw)->field('c_name,c_pimg')->find();
            $data_list[$k]['c_name'] = $userinfo['c_name'];
            $data_list[$k]['c_pimg'] = $userinfo['c_pimg'];
            
           
        }
        
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";

        $this->show();
    }


}