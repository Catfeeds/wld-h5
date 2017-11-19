<?php
namespace Home\Controller;
use Think\Controller;

class MerchantController extends BaseController {
  //商家列表
  public function shop_list(){
    $db = M('users as u');
    //条件
    $sign = trim(I('sign'));
    if (!empty($sign)) {
      $flag = 0;
      switch ($sign) {
        case 1:
          $w['a.c_checked'] = array(array('neq', 3),array('neq', 4),'and');
          $c_isfixed = I('c_isfixed');
          if(!empty($c_isfixed)){
              if($c_isfixed == 1){
                  $w['l.c_isfixed'] = $c_isfixed;
              }else{
                  $w['l.c_isfixed'] = 0;
              }
          }
          $flag = 1;
          $this->shopstype = "待审核商家列表";
          break;
        case 2:
          $w['a.c_checked'] = array('eq', 3);
          $c_isfixed = I('c_isfixed');
          if(!empty($c_isfixed)){
              if($c_isfixed == 1){
                  $w['l.c_isfixed'] = $c_isfixed;
              }else{
                  $w['l.c_isfixed'] = 0;
              }
          }
          $this->shopstype = "已审核商家列表";
          break;
        case 3:
          $w['a.c_checked'] = array('eq', 3);
          $w['l.c_isfixed'] = 1;
          $this->shopstype = "固定店铺列表";
          break;
        case 4:
          $w['a.c_checked'] = array('eq', 3);
          $w['l.c_isfixed'] = 0;
          $this->shopstype = "微商个体列表";
          break;
        case 5:
          $w['a.c_checked'] = array('eq', 4);
          $c_isfixed = I('c_isfixed');
          if(!empty($c_isfixed)){
              if($c_isfixed == 1){
                  $w['l.c_isfixed'] = $c_isfixed;
              }else{
                  $w['l.c_isfixed'] = 0;
              }
          }
          $this->shopstype = "待平台审核列表";
          break;
        default:
          $this->shopstype = "全部商家列表";
          break;
      }
    }

    $w['a.c_istore'] = array('eq', 1);

    $nickname = trim(I('nickname'));
    if (!empty($nickname)) {
        $w['u.c_nickname'] = array('like', "%{$nickname}%");
    }

    $phone = trim(I('phone'));
    if (!empty($phone)) {
        $w['u.c_phone'] = $phone;
    }

    $name = trim(I('name'));
    if (!empty($name)) {
        $w['a.c_name'] = array('like', "%{$name}%");
    }
    
    //资料手机号
    $c_phone = trim(I('c_phone'));
    if (!empty($c_phone)) {
        $w['a.c_phone'] = $c_phone;
    }

    $c_type = I('c_type');
    if (!empty($c_type)) {
        $w['a.c_type'] = $c_type;
    }

    
    $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "a.c_addtime between '".$begintime."' and '".$endtime."'";
        }

    $c_checked = trim(I('c_checked'));
    if ($c_checked == 1) {
        $w['a.c_checked'] = 1;
    } else if ($c_checked == 'se') {
        $w['a.c_checked'] = 0;
    } else if ($c_checked == '2') {
        $w['a.c_checked'] = 2;
    }
        

    if($flag == 1){
      $panrn['order'] = 'a.c_addtime desc';//排序
    }else{
      $panrn['order'] = 'a.c_updatetime desc,a.c_addtime desc';//排序
    }
    
    $parent = I('param.');
    $panrn['where'] = $w;
    $panrn['limit'] = 25;//分页数

    //分页显示数据
    $panrn['field'] = 'u.c_ucode,u.c_acode,u.c_nickname,u.c_headimg,u.c_phone as phone,u.c_invitationcode,a.*,pu.c_nickname as parent_name,l.c_isfixed';
    $panrn['join'] = 'left join t_check_shopinfo as a on u.c_ucode=a.c_ucode';
    $panrn['join1'] = 'left join t_users as pu on pu.c_ucode=u.c_acode';
    $panrn['join2'] = 'left join t_user_local as l on u.c_ucode=l.c_ucode';
    $list=D('Db','Behind');
    $date=$list->mate_select_pages($db,$panrn);
    $mylist = $date['list'];

    if($sign == 3){
      foreach ($mylist as $key => $value) {
        $w1['c_ucode'] = $value['c_ucode'];
        $storedatum = M('Store')->where($w1)->find();
        if($storedatum){
          $mylist[$key]['datum'] = 1;
        }else{
          $mylist[$key]['datum'] = 0;
        }

        //商家店铺模板
        $mw['c_ucode'] = $value['ucode'];
        $mylist[$key]['tplid'] = M('A_storetpl')->where($mw)->getField('c_tplid');
      }
    }
    
    $this->list = $mylist;
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->root_url = GetHost()."/";
    $this->post = $parent;
    $this->sign = $sign;
    $this->display();
  }

    //保存错误原因
    public function save_remark(){
        header('Content-type: text/json');
        $ucode = I('ucode');
        $remark = I('remark');
        if(empty($ucode) || empty($remark)) {
            $this->ajaxReturn(Message(1011,'参数错误！')); 
        }  

        $where['c_ucode'] = $ucode;
        $data['c_remark'] = $remark;
        $data['c_updatetime'] = Date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if(!$result){
           $this->ajaxReturn(Message(1012,'操作失败！'));
        }
        $this->ajaxReturn(Message(0,'操作成功！'));
    }

    //ajax 通过与驳回商家资料
    public function dealyaudit()
    {
        header('Content-type: text/json');
        $checked = I('checked');
        $ucode = I('ucode');
        if(empty($checked) || empty($ucode)) {
          $this->ajaxReturn(Message(1011,'参数错误！')); 
        }      

        $yw['checked'] = 4;
        $yw['c_ucode'] = $ucode;
        $shopinfo = M('Check_shopinfo')->where($yw)->find();
        if (!$shopinfo) {
            $this->ajaxReturn(Message(1012,'待审核商家记录不存在')); 
        }

        if ($checked == 1 && empty($shopinfo['c_remark'])) {
            $this->ajaxReturn(Message(1013,'请先填写审核不通过理由')); 
        }

        $parr['ucode'] = $ucode;
        $parr['checked'] = $checked;
        $parr['remark'] = $shopinfo['c_remark'];
        $result = IGD('Myagent','Agent')->AreaShenhe($parr);
        $this->ajaxReturn($result);
  }

  //商户资料导出
  public function educeshopinfo(){
      $Order = D('Ysepay','Behind');
      $Order -> educeshopinfo();
  }

  //商家资料
  public function shop_info(){
      $ucode = I('ucode');
      $where['c_ucode'] = $ucode;
      $agentinfo = M('check_shopinfo')->where($where)->find();
      $agentinfo['c_nickname'] = M('users')->where($where)->getField('c_nickname');
      if(IS_POST){
        $db = M('');
        $db->startTrans();

        $filepath = 'agent';
          if($_FILES['c_charter_img']['name']!=''){
          $_POST['c_charter_img'] = upimg($filepath,$_FILES['c_charter_img']);
        }
        if($_FILES['c_charterpub_img']['name']!=''){
          $_POST['c_charterpub_img'] = upimg($filepath,$_FILES['c_charterpub_img']);
        }
        if($_FILES['c_idcard_img']['name']!=''){
          $_POST['c_idcard_img'] = upimg($filepath,$_FILES['c_idcard_img']);
        }
        if($_FILES['c_idcard_img1']['name']!=''){
          $_POST['c_idcard_img1'] = upimg($filepath,$_FILES['c_idcard_img1']);
        }
        if($_FILES['c_bankcardimg']['name']!=''){
          $_POST['c_bankcardimg'] = upimg($filepath,$_FILES['c_bankcardimg']);
        }
        if($_FILES['c_bankcardimg1']['name']!=''){
          $_POST['c_bankcardimg1'] = upimg($filepath,$_FILES['c_bankcardimg1']);
        }

        // $_POST['c_checked'] = 3;

        $ucode = $_POST['ucode'];
        $w['c_ucode'] = $ucode;
        $result = M('check_shopinfo')->where($w)->save($_POST);
        if(!$result){
          $db->rollback();
          $this->error("编辑失败");
        }

        //同步银盛开户资料
        $result = IGD('Setinfo','Agent')->syncData($ucode);  //同步数据
        if($result['code'] != 0){
            $db->rollback();
            $this->error($result['msg']);
        }

        $db->commit();
        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Merchant/shop_list?sign=5';
        echo '<script language="javascript">alert("编辑成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
      }
      $this->root_url = GetHost()."/";
      $this->vo = $agentinfo;
      $this->display();
  }

  //驳回商家资料，改变商家审核状态
  public function shop_reject(){
    $ucode = I('ucode');
    $w['c_ucode'] = $ucode;

    $Model = M('');
    $Model -> startTrans();

    //修改商家用户状态
    $isshop['c_shop'] = 0;
    $result = M('Users')->where($w)->save($isshop);

    if(!$result){
      $Model->rollback();
      $this->ajaxReturn(Message(1001,"修改用户商家状态失败！"));
    }

    //修改商家审核状态
    $checked['c_checked'] = 1;
    $result = M('Check_shopinfo')->where($w)->save($checked);

    if(!$result){
      $Model->rollback();
      $this->ajaxReturn(Message(1002,"修改用户商家审核状态失败！"));
    }

    //给商家发后台公告
    $merchant['c_ucode'] = $ucode;
    $merchant['c_ptitle'] = "您的商家资格已被驳回，可以重新填写资料申请";
    $merchant['c_url'] = GetHost(3)."/agent.php/Shop/Personal";
    $merchant['c_type'] = 1;
    $merchant['c_addtime'] = date('Y-m-d H:i:s',time());

    $result = M('Check_info')->add($merchant);

    if(!$result){
      $Model->rollback();
      $this->ajaxReturn(Message(1003,"给商家发送后台公告失败！"));
    }

    //给代理商发后台公告
    $Id = M('Check_shopinfo')->where($w)->getField('c_id');
    $shopinfo = M('Users')->where($w)->field('c_acode,c_nickname')->find();

    $agent['c_ucode'] = $shopinfo['c_acode'];
    $agent['c_ptitle'] = "您的旗下商家【".$shopinfo['c_nickname']."】已被驳回";
    $agent['c_url'] = GetHost(3)."/agent.php/Agent/Shopcheck/details?Id=".$Id;
    $agent['c_type'] = 1;
    $agent['c_addtime'] = date('Y-m-d H:i:s',time());

    $result = M('Check_info')->add($agent);

    if(!$result){
      $Model->rollback();
      $this->ajaxReturn(Message(1004,"给代理商发送后台公告失败！"));
    }

    //给区代发后台公告
    $w1['c_ucode'] = $shopinfo['c_acode'];
    $areainfo = M('Users')->where($w1)->field('c_acode,c_nickname')->find();

    $area['c_ucode'] = $areainfo['c_acode'];
    $area['c_ptitle'] = "您所属区域的代理商【".$areainfo['c_nickname']."】旗下的商家【".$shopinfo['c_nickname']."】已被驳回";
    $area['c_url'] = GetHost(3)."/agent.php/Home/Shopcheck/details?Id=".$Id;
    $area['c_type'] = 1;
    $area['c_addtime'] = date('Y-m-d H:i:s',time());

    $result = M('Check_info')->add($area);

    if(!$result){
      $Model->rollback();
      $this->ajaxReturn(Message(1005,"给区代发送后台公告失败！"));
    }

    $Model->commit();
    $this->ajaxReturn(Message(0,"操作成功"));
  }  
  
  //店铺模板列表
  public function template_list(){
    $db = M('Shop_template as a');

    //条件
    $tplid = trim(I('tplid'));
    if (!empty($tplid)) {
      $w['a.c_id'] = $tplid;
    }

    $name = trim(I('name'));
    if (!empty($name)) {
      $w['a.c_name'] = array('like', "%{$name}%");
    }

    $nickname = trim(I('nickname'));
    if (!empty($nickname)) {
      $w['u.c_nickname'] = array('like', "%{$nickname}%");
    }
   
    $type = trim(I('type'));
    if (!empty($type)) {
      $w['a.c_type'] = $type;
    }

     $c_edite = trim(I('c_edite'));
    if (!empty($c_edite)) {
      $w['a.c_edite'] = $c_edite;
    }


    $panrn['where'] = $w;
    $parent = I('param.');
    $panrn['order'] = 'a.c_type asc';//排序
    $panrn['limit'] = 25;//分页数

    //分页显示数据
    $panrn['field'] = 'a.*,u.c_nickname';
    $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';

    //分页显示数据
    $list = D('Db','Behind');
    $date = $list->mate_select_pages($db,$panrn);

    $this->list = $date['list'];
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->root_url = GetHost()."/";
    $this->post = $parent;
    $this->display();
  }

  //店铺模板添加
  public function template_add(){
    $this->action = 'template_add';
  
    if(IS_POST){
      $db = M('Shop_template');

      if (empty($_POST['name'])) {
        $this->error("模板名称不能为空");
      }

      if (empty($_POST['ucode']) && $_POST['type'] == 1) {
        $this->error("用户只能创建自定义模板");
      }

      if(empty($_FILES['cover_img']['name'])){
        $this->error("模板封面图片必须上传");
      }
     
      $fileresult = uploadimg('shoptemplate');
      if ($fileresult['code'] != 0) {
        $this->error($fileresult['msg']);
      }

      $data['c_cover_img'] = $fileresult['data']['cover_img'];

      $data['c_ucode'] = $_POST['ucode'];
      $data['c_name'] = $_POST['name'];
      $data['c_type'] = $_POST['type'];
      $data['c_edite'] = $_POST['c_edite'];

      
      $data['c_addtime'] = Date('Y-m-d H:i:s');

      $result = $db->add($data);
      if($result){
        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Merchant/template_list';
          echo '<script language="javascript">alert("添加成功");</script>';
          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }else{
          $this->error('添加失败！');
        }
    }
    $this->display();
  }

  //店铺模板编辑
  public function template_edit(){
    $this->action = 'template_edit';

    $Id = I('Id');
    $w['c_id'] = $Id;
    $arr = M('Shop_template')->where($w)->find();

    $this->vo = $arr;
    if(IS_POST){
      $db = M('Shop_template');

      if (empty($_POST['name'])) {
        $this->error("模板名称不能为空");
      }

      if (empty($_POST['ucode']) && $_POST['type'] == 1) {
        $this->error("用户只能创建自定义模板");
      }      

      if(!empty($_FILES['cover_img']['name'])){
        $fileresult1 = uploadimg('shoptemplate');
        if ($fileresult1['code'] != 0) {
          $this->error($fileresult1['msg']);
        }
        $data['c_cover_img'] = $fileresult1['data']['cover_img'];
      }

      $data['c_ucode'] = $_POST['ucode'];
      $data['c_name'] = $_POST['name'];
      $data['c_type'] = $_POST['type'];
      $data['c_edite'] = $_POST['c_edite'];

      $w2['c_id'] = $_POST['Id'];
      $result = $db->where($w2)->save($data);

      if($result){
        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Merchant/template_list';
          echo '<script language="javascript">alert("编辑成功");</script>';
          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }else{
          $this->error('编辑失败！');
        }
    }
    $this->display('template_add');
  }

  //店铺模板删除
  public function template_delete(){
    $db = M('');
    $db -> startTrans();

    $Id = I('Id');
    $where['c_id'] = $Id;

    $result = M('Shop_template')->where($where)->delete();
    if(!$result){
      $db->rollback();
      $this->ajaxReturn(Message(1000,'删除失败'));
    }

    $w1['c_tempid'] = $Id;
    $contents = M('Shop_template_content')->where($w1)->select();
    if(count($contents) !=  0){
      $result = M('Shop_template_content')->where($w1)->delete();

      if(!$result){
        $db->rollback();
        $this->ajaxReturn(Message(1001,'删除模板内容失败'));
      }
    }

    $db->commit();
    $this->ajaxReturn(Message(0,'删除成功'));
  }

  //模板内容列表
  public function content_list(){
    $db = M('Shop_template_content');

    //条件
    $tempid = trim(I('tempid'));
    if (!empty($tempid)) {
        $w['c_tempid'] = $tempid;
        $this->tempid = $tempid;
    }   
    $w['c_isdel'] = 1;//显示正常的产品
    $panrn['where'] = $w;
    $parent = I('param.');
    $panrn['order'] = 'c_sort asc';//排序
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

  //店铺模板内容添加
  public function content_add(){
    $this->action = 'content_add';
    $this->tempid = I('tempid');

    if(IS_POST){
      $db = M('Shop_template_content');

      if (empty($_POST['function'])) {
        $this->error("功能名称不能为空");
      }
      if (empty($_POST['sort'])) {
        $this->error("位置排序不能为空");
      }
      if (empty($_POST['width'])) {
        $this->error("720所占的宽度不能为空");
      }

      if(empty($_FILES['img']['name'])){
        $this->error("展示图片必须上传");
      }
        
      $fileresult = uploadimg('shoptemplate');
      if ($fileresult['code'] != 0) {
        $this->error($fileresult['msg']);
      }

      $data['c_img'] = $fileresult['data']['img'];

      $data['c_function'] = $_POST['function'];
      $data['c_tempid'] = $_POST['tempid'];
      $data['c_sort'] = $_POST['sort'];
      $data['c_interface_type'] = $_POST['interface_type'];
      $data['c_interface_address'] = $_POST['interface_address'];
      $data['c_weburl'] = $_POST['weburl'];
      $data['c_width'] = $_POST['width'];
      $data['c_tplid'] = $_POST['c_tplid'];
      $data['c_sign'] = $_POST['c_sign'];
      $data['c_updtetime'] = Date('Y-m-d H:i:s');
      $data['c_addtime'] = Date('Y-m-d H:i:s');

      $result = $db->add($data);
      if($result){
          $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Merchant/content_list?tempid='.$_POST['tempid'];
          echo '<script language="javascript">alert("添加成功");</script>';
          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }else{
          $this->error('添加失败！');
        }
    }
    $this->display();
  }

    //店铺模板内容编辑
    public function content_edit(){
      $this->action = 'content_edit';
      $this->tempid = I('tempid');

      $Id = I('Id');
      $w['c_id'] = $Id;
      $arr = M('Shop_template_content')->where($w)->find();

      $this->vo = $arr;
      if(IS_POST){
        $db = M('Shop_template_content');
          
        if (empty($_POST['function'])) {
        $this->error("功能名称不能为空");
        }
        if (empty($_POST['sort'])) {
          $this->error("位置排序不能为空");
        }
        if (empty($_POST['width'])) {
          $this->error("720所占的宽度不能为空");
        }

        if(!empty($_FILES['img']['name'])){
          $fileresult = uploadimg('shoptemplate');
          if ($fileresult['code'] != 0) {
            $this->error($fileresult['msg']);
          }
          $data['c_img'] = $fileresult['data']['img'];
        }

        $data['c_function'] = $_POST['function'];
        $data['c_sort'] = $_POST['sort'];
        $data['c_interface_type'] = $_POST['interface_type'];
        $data['c_interface_address'] = $_POST['interface_address'];
        $data['c_weburl'] = $_POST['weburl'];
        $data['c_width'] = $_POST['width'];
        $data['c_tplid'] = $_POST['c_tplid'];
        $data['c_sign'] = $_POST['c_sign'];
        $data['c_updtetime'] = Date('Y-m-d H:i:s');

        $w2['c_id'] = $_POST['Id'];
        $result = $db->where($w2)->save($data);

        if($result){
          $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Merchant/content_list?tempid='.$_POST['tempid'];
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
          }else{
            $this->error('编辑失败！');
            }
      }
      $this->display('content_add');
    }

    //店铺模板内容删除
    public function content_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $data['c_isdel'] = 2;
        $result = M('Shop_template_content')->where($where)->save($data);
        // $result = M('Shop_template_content')->where($where)->delete();
        if(!$result){
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $this->ajaxReturn(Message(0,'删除成功'));
    }


}