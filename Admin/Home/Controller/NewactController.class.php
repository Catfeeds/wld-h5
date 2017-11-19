<?php
namespace Home\Controller;
use Think\Controller;
/**
*   商家活动 卡券、广告位、红包
*/
class NewactController extends BaseController{
  //推广位设置
  public function advert_set(){
    $db = M('A_advert_set');
    //条件
    $panrn['order'] = 'c_id desc';//排序
    $panrn['limit'] = 25;//分页数

    //分页显示数据
    $list=D('Db','Behind');
    $date=$list->mate_select_pages($db,$panrn);    
    
    $this->list = $date['list'];
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->post = $parent;
    $this->activity = $activitys;
    $this->display();
  }

  //推广位设置添加
  public function advert_set_add(){
    $this->action = 'advert_set_add';
    if(IS_POST){
      $db = M('A_advert_set');

      if(empty($_POST['name'])){
        $this->error("推广位名称必须填写!");
      }

      if(empty($_POST['duehour'])){
        $this->error("每次投放的有效期必须填写!");
      }
      
      if($_POST['num'] == ''){
        $this->error("限制数量必须填写!");
      }

      $data['c_name'] = $_POST['name'];
      $data['c_type'] = $_POST['type'];
      $data['c_duehour'] = $_POST['duehour'];
      $data['c_num'] = $_POST['num'];
      $data['c_addtime'] = Date('Y-m-d H:i:s');

      $result = $db->add($data);
      if($result){
        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Newact/advert_set';
          echo '<script language="javascript">alert("添加成功");</script>';
          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }else{
          $this->error('添加失败！');
        }
    }
    $this->display();
  }

  //推广位设置编辑
  public function advert_set_edit(){
    $this->action = 'advert_set_edit';

    $Id = I('Id');
    $w['c_id'] = $Id;
    $this->data = M('A_advert_set')->where($w)->find();
  
    if(IS_POST){
      $db = M('A_advert_set');

      if(empty($_POST['name'])){
        $this->error("推广位名称必须填写!");
      }

      if(empty($_POST['duehour'])){
        $this->error("每次投放的有效期必须填写!");
      }
      
      if($_POST['num'] == ''){
        $this->error("限制数量必须填写!");
      }

      $data['c_name'] = $_POST['name'];
      $data['c_type'] = $_POST['type'];
      $data['c_duehour'] = $_POST['duehour'];
      $data['c_num'] = $_POST['num'];
      $data['c_addtime'] = Date('Y-m-d H:i:s');

      $w1['c_id'] = $_POST['Id'];
      $result = $db->where($w1)->save($data);
      if($result){
        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Newact/advert_set';
          echo '<script language="javascript">alert("编辑成功");</script>';
          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }else{
          $this->error('编辑失败！');
        }
    }
    $this->display('advert_set_add');
  }

 //推广位设置删除
  public function advert_set_delete()
  {
      $Id = I('Id');
      $idstr = str_replace('|', ',', $Id);
      $where['c_id'] = array('in',$idstr);
      $result = M('A_advert_set')->where($where)->delete();
      if($result){
          $this->ajaxReturn(Message(0,'删除成功'));
      }else{
          $this->ajaxReturn(Message(1000,'删除失败'));
      }
  }
}