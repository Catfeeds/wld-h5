<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  靓号管理
 */
class ShopnumController extends BaseController {
	//靓号列表
	public function shopnum_list(){
    $db = M('shop_num as s');
		//条件
  	$nickname = trim(I('nickname'));
    if (!empty($nickname)) {
        $w['u.c_nickname'] = array('like', "%{$nickname}%");
    }
    $shopnum = trim(I('shopnum'));
    if (!empty($shopnum)) {
        $w['s.c_shopnum'] = $shopnum;
    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 's.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

	  //分页显示数据
	  $panrn['field'] = 's.*,u.c_nickname';
	  $panrn['join'] = 'left join t_users as u on s.c_ucode=u.c_ucode';
	  $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
	  $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
	}

	//添加靓号
	public function shopnum_add(){
	    $this->action = 'shopnum_add';
	    if(IS_POST){
			$shopnum = $_POST['shopnum'];
			if (empty($shopnum)) {
			  	$this->error('靓号编码不能为空');
			}

			$w['c_shopnum'] = $shopnum;
			$codes = M('shop_num')->where($w)->count();
			if($codes != 0){
				$this->error('该靓号编码已存在');
			}

      //添加图片
      $shopnumarr = arr_split_zh($shopnum);
      foreach ($shopnumarr as $k => $v) {
        $imgage[$k] = './data/shopnum/'.$v.'.png';
      }
      $imgpath = combine_image($imgage,'./data/createnum/'.$shopnum,70,100,1,1,5);
      if (!$imgpath) {
        $this->error("生成图片失败");
      }

      $data['c_img'] = $imgpath;
			$data['c_shopnum'] = $shopnum;
			$data['c_addtime'] = date('Y-m-d H:i:s',time());
			$result = M('shop_num')->add($data);

			if($result){
				echo '<script language="javascript">alert("靓号添加成功");</script>';
				echo '<script language="javascript">window.parent.location.href="'.U('Shopnum/shopnum_list').'";</script>';die();
			}else{
				$this->error('靓号添加失败！');
			}
	    }
	    $this->display();
	}

  //靓号编辑
  public function shopnum_edit(){
    $this->action = 'shopnum_edit';
    $_where['c_id'] = I('Id');
    $this->vo = M('shop_num')->where($_where)->find();

    if(IS_POST){
      $shopnum = $_POST['shopnum'];
      if (empty($shopnum)) {
          $this->error('靓号编码不能为空');
      }

      $w['c_shopnum'] = $shopnum;
      $codes = M('shop_num')->where($w)->count();
      if($codes == 1){
        $this->error('靓号编码没有编辑或已存在');
      }

      $db = M('');
      $db->startTrans();

      //添加图片
      $shopnumarr = arr_split_zh($shopnum);
      foreach ($shopnumarr as $k => $v) {
        $imgage[$k] = './data/shopnum/'.$v.'.png';
      }
      $imgpath = combine_image($imgage,'./data/createnum/'.$shopnum,70,100,1,1,5);
      if (!$imgpath) {
        $this->error("生成图片失败");
      }

      $id = $_POST['c_id'];
      $data['c_shopnum'] = $shopnum;
      $data['c_img'] = $imgpath;

      $where['c_id'] = $id;
      $result = M('shop_num')->where($where)->save($data);

      if($result){
      	$shopnum_info = M('shop_num')->where($where)->find();
      	if(!empty($shopnum_info['c_ucode'])){
      		$uwhere['c_ucode'] = $shopnum_info['c_ucode'];
      		$sdata['c_shopnum'] = $shopnum;
      		$result1 = M('Users')->where($uwhere)->save($sdata);

      		if($result<=0){
      			$db->rollback();
        		$this->error('修改用户靓号信息失败！');
      		}
      	}
      	$db->commit();
        echo '<script language="javascript">alert("靓号编辑成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.U('Shopnum/shopnum_list').'";</script>';die();
      }else{
      	$db->rollback();
        $this->error('靓号编辑失败！');
      }
    }
    $this->display('shopnum_add');
  }

  //靓号删除
  public function shopnum_delete(){
    $Id = I('Id');
    $idstr = str_replace('|', ',', $Id);
    $where['c_id'] = array('in',$idstr);
    $result = M('shop_num')->where($where)->delete();
    if($result){
      $this->ajaxReturn(Message(0,'删除成功'));
    }else{
      $this->ajaxReturn(Message(1000,'删除失败'));
    }
  }

  	//靓号绑定
  	public function shopnum_binding(){
  		$_where['s.c_id'] = I('Id');
  		$join = 'left join t_users as u on u.c_ucode=s.c_ucode';
  		$field = 's.*,u.c_nickname';
    	$this->vo = M('shop_num as s')->field($field)->join($join)->where($_where)->find();
  		$this->display();
  	}

  	//绑定提交数据
  	public function bindding_user(){
  		$id = $_POST['c_id'];
  		$ucode = $_POST['ucode'];

  		$where['c_id'] = $id;
  		$shopnum_info = M('shop_num')->where($where)->find();

  		if(!empty($shopnum_info['c_ucode'])){
  			$this->error('该靓号已经被绑定，不能重新绑定！');
  		}

  		$uwhere['c_ucode'] = $ucode;
  		$user_info = M('Users')->field('c_shopnum,c_shop')->where($uwhere)->find();

      if($user_info['c_shop'] == 0){
        $this->error('该用户不是商家，不能绑定账号！');
      }

  		if(!empty($user_info['c_shopnum'])){
  			$this->error('该用户已经绑定了靓号，不能再绑定！');
  		}

  		$db = M('');
  		$db->startTrans();

  		$sdata['c_ucode'] = $ucode;
  		$sdata['c_binding_time'] = date('Y-m-d H:i:s',time());
  		$result = M('shop_num')->where($where)->save($sdata);
  		if(!$result){
  			$db->rollback();
  			$this->error('修改靓号信息失败');
  		}

  		$udata['c_shopnum'] = $shopnum_info['c_shopnum'];
  		$result = M('Users')->where($uwhere)->save($udata);
  		if(!$result){
  			$db->rollback();
  			$this->error('修改用户靓号信息失败');
  		}

  		$db->commit();
  		$this->success('绑定成功');
  	}
}