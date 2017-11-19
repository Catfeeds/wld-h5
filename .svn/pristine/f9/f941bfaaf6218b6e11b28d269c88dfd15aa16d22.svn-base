<?php
namespace Home\Controller;
use Think\Controller;
class AgentController extends BaseController {
	//代理商管理
	public function index(){
    $db = M('users as u');
		//条件
    $w['u.c_isagent'] = array('neq', 0);

  	$nickname = trim(I('nickname'));
    if (!empty($nickname)) {
        $w['u.c_nickname'] = array('like', "%{$nickname}%");
    }
    $c_name = trim(I('c_name'));
    if (!empty($c_name)) {
        $w['a.c_name'] = array('like', "%{$c_name}%");
    }
    $isagent = trim(I('isagent'));
    if (!empty($isagent)) {
        $w['u.c_isagent'] = $isagent;
    }
    $type = trim(I('type'));
    if (!empty($type)) {
      $w['a.c_type'] = $type;
    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'a.c_addtime desc,u.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

    //分页显示数据
    $panrn['field'] = 'u.c_nickname,u.c_isagent,u.c_acode,a.*,pu.c_nickname as parent_name,i.c_code';
    $panrn['join'] = 'left join t_check_shopinfo as a on u.c_ucode=a.c_ucode';
    $panrn['join1'] = 'left join t_users as pu on pu.c_ucode=u.c_acode';
    $panrn['join2'] = 'left join t_invite_code as i on i.c_ucode=u.c_ucode';
    $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
	}

  //商家列表
  public function shop_list(){
    $db = M('Merchant as u');
    
    //条件
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

    //标志线下线上
    $c_isfixed1 = trim(I('c_isfixed1'));
    if ($c_isfixed1 == 1) {
        $w['a.c_isfixed1'] = 1;
    } else if ($c_isfixed1 == 2) {
        $w['a.c_isfixed1'] = 0;
    }

    $c_merchantname = trim(I('c_merchantname'));
    if (!empty($c_merchantname)) {
        $w['u.c_merchantname'] = array('like', "%{$c_merchantname}%");
    }

    $name = trim(I('name'));
    if (!empty($name)) {
        $w['u.c_principal'] = array('like', "%{$name}%");
    }

    $c_mchdealtype = I('c_mchdealtype');
    if (!empty($c_mchdealtype)) {
        $w['u.c_mchdealtype'] = $c_mchdealtype;
    }

    //是否可进件
    $ismerch = trim(I('ismerch'));
    if ($ismerch == 1) {
      $w['u.c_status'] = 1;
    } else if ($ismerch == 2) {
      $w['u.c_status'] = 0;
    }

    //是否进件  
    $istijiao = trim(I('istijiao'));
    if ($istijiao == 1) {
      $w['u.c_checknum'] = array('GT',0);
    } else if ($istijiao == 2) {
      $w['u.c_checknum'] = 0;
    }

    $parent = I('param.');
    $panrn['where'] = $w;
    $panrn['limit'] = 25;//分页数
    $panrn['order'] = 'u.c_status desc,u.c_checknum asc,u.c_id asc';//排序

    //分页显示数据
    $panrn['field'] = 'u.*,a.c_nickname,a.c_phone,a.c_headimg';
    $panrn['join'] = 'left join t_users as a on u.c_ucode=a.c_ucode';
    $list=D('Db','Behind');
    $date=$list->mate_select_pages($db,$panrn);
    $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            
           
        }

    $this->list = $data_list;
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->root_url = GetHost()."/";
    $this->post = $parent;
    $this->display();
  }

  //Excel导出提款申请数据
  public function educeshopinfo(){
      $Order = D('Merchant','Behind');
      $Order -> educeshopinfo();
  }

  //商户进件接口
  public function PostAddmerchant()
  {
      $parr['ucode'] = I('ucode');
      if (empty($parr['ucode'])) {
          $this->ajaxReturn(Message(1009,'用户编码为空'));
      }
      $result = IGD('Upay','Scanpay')->PostAddmerchant($parr);
      $this->ajaxReturn($result);
  }

  //商家资料
  public function shop_info(){
      $Id = I('Id');
      $ucode = I('ucode');
      if (!empty($Id)) {
        $where['c_id'] = $Id;
      } else {
        $where['c_ucode'] = $ucode;
      }
      
      $agentinfo = M('check_shopinfo')->where($where)->find();
      $agentinfo['c_nickname'] = M('users')->where($where)->getField('c_nickname');
      if(IS_POST){

        if(empty($_POST['c_name']) || empty($_POST['c_email']) || empty($_POST['c_qq']) || empty($_POST['c_home_tel']) || empty($_POST['c_idcard']) || empty($_POST['c_fee_name']) || empty($_POST['c_fee_bank']) || empty($_POST['c_fee_cardnum']) || empty($_POST['c_fee_alipay']) || empty($_POST['c_fee_weixin']) || empty($_POST['c_merchantname']) || empty($_POST['c_merchantshortname']) || empty($_POST['c_legalperson']) || empty($_POST['c_contactline']) || empty($_POST['c_bankname']) || empty($_POST['c_bankprovince']) || empty($_POST['c_bankcity']) || empty($_POST['c_idcardinfo']) || empty($_POST['c_banktel'])){
          $this->error(1001,"带*内容项必须填写！");
        }

        $filepath = 'agent';
        if($_FILES['c_charter_img']['name']!=''){
          $_POST['c_charter_img'] = upimg($filepath,$_FILES['c_charter_img']);
        }
        if($_FILES['c_company_sign']['name']!=''){
          $_POST['c_company_sign'] = upimg($filepath,$_FILES['c_company_sign']);
        }
        if($_FILES['c_idcard_img']['name']!=''){
          $_POST['c_idcard_img'] = upimg($filepath,$_FILES['c_idcard_img']);
        }
        if($_FILES['c_idcard_img1']['name']!=''){
          $_POST['c_idcard_img1'] = upimg($filepath,$_FILES['c_idcard_img1']);
        }

        //验证银行名
        $bw['c_name'] = $_POST['c_fee_bank'];
        $bankinfo = M('Merchant_bank')->where($bw)->find();
        if (!$bankinfo) {
            $this->error("收款银行名称不符");
        }

        //验证支行名与联行号
        $branw['c_bankname'] = $_POST['c_fee_bank'];
        $branw['c_name'] = $_POST['c_bankname'];
        $branw['c_code'] = $_POST['c_contactline'];
        $branceinfo = M('Merchant_branch')->where($branw)->find();
        if (!$branceinfo) {
            $this->error("开户行、支行及联行号不匹配");
        }

        // $_POST['c_checked'] = 3;

        $ucode = $_POST['ucode'];
        $w['c_ucode'] = $ucode;
        $result = M('check_shopinfo')->where($w)->save($_POST);
        if($result){
          $result = IGD('Setinfo','Agent')->syncData($ucode);  //同步数据
          if ($result['code'] != 0) {
              $this->error("同步数据失败");die;
          }
            
          $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Agent/shop_list';
          echo '<script language="javascript">alert("编辑成功");</script>';
          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }else{
          $this->error("编辑失败");
        }
      }
      // $return =$this->syncData($parr['ucode']);  //同步数据
      $this->vo = $agentinfo;
      $this->root_url = GetHost()."/";
      $this->display();
  }

  //代理商添加
  public function agent_add(){
    $this->display();
  }

  //代理商添加提交数据
  public function ajax_agent_add(){
    $db = M('check_shopinfo');
    $db -> startTrans(); /* 开启事务 */

    $this->verify_data($_POST); //验证数据

    $parr['phone'] = $_POST['phone'];//负责人手机号
    $parr['pwd'] = encrypt($_POST['pwd'], C('ENCRYPT_KEY'));
    $parr['nickname'] = $_POST['nickname'];
    $parr['isagent'] =  $_POST['isagent'];
    $parr['tj_phone'] = $_POST['tj_phone'];
    $parr['invite_code'] = $_POST['invite_code'];

    //注册用户
    $result = D('User','Behind')->register($parr);
    if($result['code'] == 0){
      $ucode = $result['data']['ucode'];
      $dcode = $result['data']['ucode'];
    }else{
      $db -> rollback();
      $this->error($result['msg']);
    }

    //填写代理商信息
    if(!empty($_FILES['charter_img']['name']) || !empty($_FILES['company_sign']['name']) || !empty($_FILES['idcard_img']['name']) || !empty($_FILES['idcard_img1']['name'])){
        $fileresult = uploadimg('agent');
        if ($fileresult['code'] != 0) {
          $this->error($fileresult['msg']);
        }
    }

    $_POST['c_dcode'] = 'XWS'.mb_substr(time(), 5, 9, 'utf8').CreateOrder('CD');
    $_POST['c_checked'] = 3;
    $_POST['c_ucode'] = $ucode;
    $_POST['c_istore'] = 2;
    if(empty($_POST['c_name'])){
      $_POST['c_name'] = $_POST['nickname'];
    }
    $_POST['c_phone'] = $_POST['phone'];
    $_POST['c_charter_img'] = $fileresult['data']['charter_img'];;
    $_POST['c_company_sign'] = $fileresult['data']['company_sign'];;
    $_POST['c_idcard_img'] = $fileresult['data']['idcard_img'];;
    $_POST['c_idcard_img1'] = $fileresult['data']['idcard_img1'];;
    $_POST['c_addtime'] = date('Y-m-d H:i:s',time());
    $result = $db->add($_POST);

    if($result){
      $db->commit();
      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Agent/index';
      echo '<script language="javascript">alert("添加成功");</script>';
      echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
    }else{
      $db -> rollback();
      $this->error("保存失败");
    }
  }

   //代理商编辑
  public function agent_edit(){
  	$ucode = I('ucode');
  	$where['c_ucode'] = $ucode;
  	$agentinfo = M('check_shopinfo')->where($where)->find();
  	$agentinfo['c_nickname'] = M('users')->where($where)->getField('c_nickname');
    $this->vo = $agentinfo;
  	$this->display();
  }

  //代理商编辑提交数据
  public function ajax_agent_edit(){
    $filepath = 'agent';
  	if($_FILES['c_charter_img']['name']!=''){
      $_POST['c_charter_img'] = upimg($filepath,$_FILES['c_charter_img']);
    }
    if($_FILES['c_company_sign']['name']!=''){
      $_POST['c_company_sign'] = upimg($filepath,$_FILES['c_company_sign']);
    }
    if($_FILES['c_idcard_img']['name']!=''){
      $_POST['c_idcard_img'] = upimg($filepath,$_FILES['c_idcard_img']);
    }
    if($_FILES['c_idcard_img1']['name']!=''){
      $_POST['c_idcard_img1'] = upimg($filepath,$_FILES['c_idcard_img1']);
    }

    // $_POST['c_checked'] = 3;
    $ucode = $_POST['ucode'];
    $w['c_ucode'] = $ucode;
    $result = M('check_shopinfo')->where($w)->save($_POST);
    if($result){
      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Agent/index';
      echo '<script language="javascript">alert("编辑成功");</script>';
      echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
    }else{
      $this->error("编辑失败");
    }
  }

  //验证提交数据
  public function verify_data($data){
    if(empty($data['phone'])){
      $this->error("注册手机号必须填写");
    }else{
      $result = checkMobile($data['phone']);
      if(!$result){
        $this->error("注册手机号格式不正确");
      }
    }
    if(empty($data['nickname'])){
      $this->error("注册昵称必须填写");
    }
    if(empty($data['pwd'])){
      $this->error("注册密码必须填写");
    }
    if($data['isagent'] == 2){
      if(empty($data['tj_phone']) && empty($data['invite_code'])){
        $this->error("5万代理商必须确定推荐关系");
      }
      if(!empty($data['tj_phone']) && !empty($data['invite_code'])){
        $this->error("推荐代理商、邀请码编码只能填写一个");
      }
    }
  }

  //资料列表
  public function file_list(){
    $db = M('check_datum as tb1');
    //条件
    $w = "tb1.c_pid <> 0 ";
    $filename = trim(I('filename'));
    if (!empty($filename)) {
      $w .= " AND tb1.c_name like '%$filename%' ";
    }

    $pid = trim(I('pid'));
    if (!empty($pid)) {
      $w .= " AND tb1.c_pid =".$pid;
    }
    $panrn['where'] = $w;
    $parent = I('param.');
    $panrn['order'] = 'tb1.c_pid';//排序
    $panrn['limit'] = 25;//分页数

    //分页显示数据
    $panrn['field'] = 'tb1.*,tb2.c_name as parent_name,tb2.c_id as parentid';
    $panrn['join'] = 'left join t_check_datum as tb2 on tb2.c_id=tb1.c_pid';
    $list=D('Db','Behind');
    $date=$list->mate_select_pages($db,$panrn);
    $this->list = $date['list'];
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->root_url = GetHost()."/";
    $this->parentid = $db->where('c_pid=0')->select();
    $this->post = $parent;
    $this->display();
  }

  //资料上传
  public function uploadfile(){
    $db = M('check_datum');
    $this->mainlist = $db->where('c_pid=0')->select();
    $this->action = 'uploadfile';
    if(IS_POST){
      $filename = $_POST['filename'];
      $pid = $_POST['pid'];
      $downnum = $_POST['downnum'];

      if(empty($filename)){
        $this->error("文件名称必须填写！");
      }

      $data = array();
      if($pid == ''){//添加顶级文章分类
        $pid = 0;
      }else{
        $fileresult = uploadfile('file');
        if ($fileresult['code'] != 0) {
            $this->error($fileresult['msg']);
        }
        $data['c_filepath'] = $fileresult['data']['filepath'];
        $data['c_downnum'] = $downnum;
      }

      $data['c_pid'] = $pid;
      $data['c_name'] = $filename;
      $data['c_addtime'] = date('Y-m-d H:i:s',time());
      $result = M('check_datum')->add($data);

      if($result){
        echo '<script language="javascript">alert("文件添加成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.U('Agent/file_list').'";</script>';die();
      }else{
        $this->error('文件添加失败！');
      }
    }
    $this->display();
  }

  //文件编辑
  public function uploadfile_edit(){
    $db = M('check_datum');
    $Id = I('Id');
    $this->mainlist = $db->where('c_pid=0')->select();
    $this->action = 'uploadfile_edit';
    $where['c_id'] = $Id;
    $this->vo = $db->where($where)->find();

    if(IS_POST){
      $filename = $_POST['filename'];
      $pid = $_POST['pid'];
      $downnum = $_POST['downnum'];
      $id = $_POST['c_id'];

      if(empty($filename)){
        $this->error("文件名称必须填写！");
      }

      $data = array();
      if($pid == ''){//添加顶级文章分类
        $pid = 0;
      }else{
        if(!empty($_FILES['filepath']['name'])){
          $fileresult = uploadfile('file');
          if ($fileresult['code'] != 0) {
              $this->error($fileresult['msg']);
          }
          $data['c_filepath'] = $fileresult['data']['filepath'];
        }
        $data['c_downnum'] = $downnum;
      }

      $data['c_pid'] = $pid;
      $data['c_name'] = $filename;

      $where['c_id'] = $id;
      $result = M('check_datum')->where($where)->save($data);
      if($result){
        echo '<script language="javascript">alert("文件编辑成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.U('Agent/file_list').'";</script>';die();
      }else{
        $this->error('文件编辑失败！');
      }
    }

    $this->display('uploadfile');
  }

  //文件删除
  public function file_delete(){
    $Id = I('Id');
    $idstr = str_replace('|', ',', $Id);
    $where['c_id'] = array('in',$idstr);
    $file_info = M('check_datum')->where($where)->field('c_filepath')->select();
    $result = M('check_datum')->where($where)->delete();
    if($result){
      foreach ($file_info as $k => $v) {
        unlink($v['c_filepath']);
      }
      $this->ajaxReturn(Message(0,'删除成功'));
    }else{
      $this->ajaxReturn(Message(1000,'删除失败'));
    }
  }

  //后台公告列表
  public function notice_list(){
    $db = M('check_info as tb1');
    //条件
    $w = "1=1";
    $c_ptitle = trim(I('c_ptitle'));
    if (!empty($c_ptitle)) {
      $w .= " AND tb1.c_ptitle like '%$c_ptitle%' ";
    }

    $c_title = trim(I('c_title'));
    if (!empty($c_title)) {
      $w .= " AND tb1.c_title like '%$c_title%' ";
    }
    $c_type = trim(I('c_type'));
    if (!empty($c_type)) {
      $w .= " AND tb1.c_type=".$c_type;
    }
    $panrn['where'] = $w;
    $parent = I('param.');
    $panrn['order'] = 'tb1.c_id desc';//排序
    $panrn['limit'] = 25;//分页数

    //分页显示数据
    $panrn['field'] = 'tb1.*,tb2.c_nickname';
    $panrn['join'] = 'left join t_users as tb2 on tb2.c_ucode=tb1.c_ucode';
    $list=D('Db','Behind');
    $date=$list->mate_select_pages($db,$panrn);
    $this->list = $date['list'];
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->post = $parent;
    $this->display();
  }

  //添加公告
  public function notice_add(){
    $this->action = 'notice_add';
    if(IS_POST){
      if (empty($_POST['ptitle'])) {
          $this->error('封面展示标题不能为空');
      }
      if (empty($_POST['title'])) {
          $this->error('公告标题不能为空');
      }
      if (empty($_POST['origin'])) {
          $this->error('文章来源不能为空');
      }
      if (empty($_POST['content'])) {
          $this->error('公告内容不能为空');
      }

      $data['c_ptitle'] = $_POST['ptitle'];
      $data['c_ucode'] = $_POST['ucode'];
      $data['c_title'] = $_POST['title'];
      $data['c_origin'] = $_POST['origin'];
      $data['c_content'] = $_POST['content'];
      $data['c_type'] = $_POST['type'];
      $data['c_addtime'] = date('Y-m-d H:i:s',time());

      $result = M('check_info')->add($data);

      if($result){
        echo '<script language="javascript">alert("公告添加成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.U('Agent/notice_list').'";</script>';die();
      }else{
        $this->error('公告添加失败！');
      }
    }
    $this->display();
  }

  //添加编辑
  public function notice_edit(){
    $this->action = 'notice_edit';
    $_where['a.c_id'] = I('Id');
    $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
    $field = 'a.*,b.c_nickname';
    $this->vo = M('check_info as a')->join($join)->limit($limit)->where($_where)->field($field)->find();

    if(IS_POST){
      if (empty($_POST['ptitle'])) {
          $this->error('封面展示标题不能为空');
      }
      if (empty($_POST['title'])) {
          $this->error('公告标题不能为空');
      }
      if (empty($_POST['origin'])) {
          $this->error('文章来源不能为空');
      }
      if (empty($_POST['content'])) {
          $this->error('公告内容不能为空');
      }

      $id = $_POST['c_id'];
      $data['c_ptitle'] = $_POST['ptitle'];
      $data['c_ucode'] = $_POST['ucode'];
      $data['c_title'] = $_POST['title'];
      $data['c_origin'] = $_POST['origin'];
      $data['c_content'] = $_POST['content'];
      $data['c_type'] = $_POST['type'];
      $data['c_addtime'] = date('Y-m-d H:i:s',time());

      $where['c_id'] = $id;
      $result = M('check_info')->where($where)->save($data);

      if($result){
        echo '<script language="javascript">alert("公告编辑成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.U('Agent/notice_list').'";</script>';die();
      }else{
        $this->error('公告编辑失败！');
      }
    }
    $this->display('notice_add');
  }

  //公告删除
  public function notice_delete(){
    $Id = I('Id');
    $idstr = str_replace('|', ',', $Id);
    $where['c_id'] = array('in',$idstr);
    $result = M('check_info')->where($where)->delete();
    if($result){
      $this->ajaxReturn(Message(0,'删除成功'));
    }else{
      $this->ajaxReturn(Message(1000,'删除失败'));
    }
  }

  //邀请卡 钻卡列表
  public function invite_list(){
    $db = M('invite_code as tb1');
    //条件
    $w = "1=1 ";
    $code = trim(I('code'));
    $flag = 0;
    if (!empty($code)) {
      $w .= " AND tb1.c_code like '%$code%' ";

      $where['c_code'] = $code;
      $invit_info = M('Invite_code')->where($where)->find();
      if($invit_info['c_rule'] == 2){
        $flag = 1;
        $this->fcode = $invit_info['c_fcode'];
      }
    }else{
      $w .= "AND tb1.c_rule=1";
    }

    $panrn['where'] = $w;
    $parent = I('param.');
    $panrn['order'] = 'tb1.c_id desc';//排序
    $panrn['limit'] = 25;//分页数

    //分页显示数据
    $panrn['field'] = 'tb1.*,tb2.c_nickname';
    $panrn['join'] = 'left join t_users as tb2 on tb2.c_ucode=tb1.c_ucode';
    $list=D('Db','Behind');
    $date=$list->mate_select_pages($db,$panrn);
    $this->list = $date['list'];
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->post = $parent;
    if($flag == 1){
      $this->display('goldinvite_list');
    }else{
      $this->display();
    }
  }

  //添加钻卡
  public function invite_add(){
    $this->action = 'invite_add';
    if(IS_POST){
      $code = $_POST['code'];
      if (empty($code)) {
          $this->error('邀请卡编码不能为空');
      }

      $w['c_code'] = $code;
      $codes = M('invite_code')->where($w)->count();
      if($codes != 0){
        $this->error('邀请卡编码已存在');
      }

      $data['c_code'] = $code;
      $data['c_rule'] = 1;
      $data['c_addtime'] = date('Y-m-d H:i:s',time());
      $data['c_updatetime'] = date('Y-m-d H:i:s',time());

      $result = M('invite_code')->add($data);

      if($result){
        echo '<script language="javascript">alert("邀请卡添加成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.U('Agent/invite_list').'";</script>';die();
      }else{
        $this->error('邀请卡添加失败！');
      }
    }
    $this->display();
  }

  //钻卡编辑
  public function invite_edit(){
    $this->action = 'invite_edit';
    $_where['c_id'] = I('Id');
    $this->vo = M('invite_code')->where($_where)->find();

    if(IS_POST){
      $code = $_POST['code'];
      if (empty($code)) {
          $this->error('邀请卡编码不能为空');
      }

      $w['c_code'] = $code;
      $codes = M('invite_code')->where($w)->count();
      if($codes == 1){
        $this->error('邀请卡编码没有编辑或已存在');
      }

      $id = $_POST['c_id'];
      $data['c_code'] = $code;
      $data['c_updatetime'] = date('Y-m-d H:i:s',time());

      $where['c_id'] = $id;
      $result = M('invite_code')->where($where)->save($data);

      if($result){
        echo '<script language="javascript">alert("邀请卡编辑成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.U('Agent/invite_list').'";</script>';die();
      }else{
        $this->error('邀请卡编辑失败！');
      }
    }
    $this->display('invite_add');
  }

  //钻卡删除
  public function invite_delete(){
    $Id = I('Id');
    $idstr = str_replace('|', ',', $Id);
    $where['c_id'] = array('in',$idstr);
    $result = M('invite_code')->where($where)->delete();
    if($result){
      $this->ajaxReturn(Message(0,'删除成功'));
    }else{
      $this->ajaxReturn(Message(1000,'删除失败'));
    }
  }

  //金卡列表
  public function goldinvite_list(){
    $fcode = I('fcode');
    $db = M('invite_code as tb1');
    //条件
    $w = "tb1.c_rule=2 AND tb1.c_fcode='".$fcode."'";
    $code = trim(I('code'));
    if (!empty($code)) {
      $w .= " AND tb1.c_code like '%$code%' ";
    }

    $panrn['where'] = $w;
    $parent = I('param.');
    $panrn['order'] = 'tb1.c_id desc';//排序
    $panrn['limit'] = 25;//分页数

    //分页显示数据
    $panrn['field'] = 'tb1.*,tb2.c_nickname';
    $panrn['join'] = 'left join t_users as tb2 on tb2.c_ucode=tb1.c_ucode';
    $list=D('Db','Behind');
    $date=$list->mate_select_pages($db,$panrn);
    $this->list = $date['list'];
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->post = $parent;
    $this->fcode = $fcode;
    $this->display();
  }

  //添加金卡
  public function goldinvite_add(){
    $this->fcode = I('fcode');
    $this->action = 'goldinvite_add';
    if(IS_POST){
      $code = $_POST['code'];
      if (empty($code)) {
          $this->error('邀请卡编码不能为空');
      }

      $w['c_code'] = $code;
      $codes = M('invite_code')->where($w)->count();
      if($codes != 0){
        $this->error('邀请卡编码已存在');
      }

      $data['c_code'] = $code;
      $data['c_fcode'] = $_POST['fcode'];
      $data['c_rule'] = 2;
      $data['c_num'] = 200;
      $data['c_addtime'] = date('Y-m-d H:i:s',time());
      $data['c_updatetime'] = date('Y-m-d H:i:s',time());

      $result = M('invite_code')->add($data);

      if($result){
        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Agent/goldinvite_list?fcode='.$_POST['fcode'];
        echo '<script language="javascript">alert("邀请卡添加成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
      }else{
        $this->error('邀请卡添加失败！');
      }
    }
    $this->display();
  }

  //金卡编辑
  public function goldinvite_edit(){
    $this->action = 'goldinvite_edit';
    $this->fcode = I('fcode');
    $_where['c_id'] = I('Id');
    $this->vo = M('invite_code')->where($_where)->find();

    if(IS_POST){
      $code = $_POST['code'];
      if (empty($code)) {
          $this->error('邀请卡编码不能为空');
      }

      $w['c_code'] = $code;
      $codes = M('invite_code')->where($w)->count();
      if($codes == 1){
        $this->error('邀请卡编码没有编辑或已存在');
      }

      $id = $_POST['c_id'];
      $data['c_code'] = $code;
      $data['c_updatetime'] = date('Y-m-d H:i:s',time());

      $where['c_id'] = $id;
      $result = M('invite_code')->where($where)->save($data);

      if($result){
        $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Agent/goldinvite_list?fcode='.$_POST['fcode'];
        echo '<script language="javascript">alert("邀请卡添加成功");</script>';
        echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
      }else{
        $this->error('邀请卡编辑失败！');
      }
    }
    $this->display('goldinvite_add');
  }

  //金卡删除
  public function goldinvite_delete(){
    $Id = I('Id');
    $idstr = str_replace('|', ',', $Id);
    $where['c_id'] = array('in',$idstr);
    $result = M('invite_code')->where($where)->delete();
    if($result){
      $this->ajaxReturn(Message(0,'删除成功'));
    }else{
      $this->ajaxReturn(Message(1000,'删除失败'));
    }
  }

  //友收宝资料信息
  public function merchant_info(){
      $ucode = I('ucode');
      $where['c_ucode'] = $ucode;
      $merchantinfo = M('Merchant')->where($where)->find();

      $iw['c_id'] = $merchantinfo['c_industrid'];
      $merchantinfo['industry'] = M('Shop_industry')->where($iw)->getField('c_name');
      
      $this->rt = $merchantinfo;
      $this->display();
  }

  //同步商家资料到友收宝资料
  public function synchronize(){
      $ucode = I('ucode');

      $parr['ucode'] = $ucode;

      $result = IGD('Upay','Scanpay')->SynchronousInfo($parr,1);

      $this->ajaxReturn($result);     
  }

  //友收宝商家费率记录列表
  public function merchant_id(){
      $db = M('Merchant_id as a');
      //条件
      $flag = 0;
      $ucode = trim(I('ucode'));
      if (!empty($ucode)) {
          $w['a.c_ucode'] = $ucode;
          $this->ucode = $ucode;
          $flag = 1;
      }
      $this->flag = $flag;

      $nickname = trim(I('nickname'));
      if (!empty($nickname)) {
          $w['u.c_nickname'] = array('like', "%{$nickname}%");
      }
      $phone = trim(I('phone'));
      if (!empty($phone)) {
          $w['u.c_phone'] = $phone;
      }

      $merchantname = trim(I('merchantname'));
      if (!empty($merchantname)) {
          $w['a.c_merchantname'] = array('like', "%{$merchantname}%");
      }
      $merchantshortname = trim(I('merchantshortname'));
      if (!empty($merchantshortname)) {
          $w['a.c_merchantshortname'] = array('like', "%{$merchantshortname}%");
      }

      $type = trim(I('c_type'));
      if (!empty($type)) {
          $w['a.c_type'] = $type;
      }
     
      $status = trim(I('status'));
      if (!empty($status)) {
          if($status == 10){
            $w['a.c_status'] = 0;
          }else{
            $w['a.c_status'] = $status;
          }
      }

      $begintime = I('EntTime1');
      $endtime = I('EntTime2');
      if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
          $w[] = "a.c_addtime between '".$begintime."' and '".$endtime."'";
      } 
     
      $panrn['where'] = $w;
      $parent = I('param.');
      $panrn['order'] = 'c_id desc';//排序
      $panrn['limit'] = 25;//分页数

      //分页显示数据
      $panrn['field'] = 'a.*,u.c_nickname,u.c_phone';

      $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
      $list=D('Db','Behind');
      $date=$list->mate_select_pages($db,$panrn);
      $data_list = $date['list'];
      foreach ($data_list as $k => $v) {
          switch ($data_list[$k]['c_status']) {
              case 0:
                  $data_list[$k]['mystatus'] = "<font color='#808080'>审核中</font>";
                  break;
              case 1:
                  $data_list[$k]['mystatus'] = "<font color='#00FF00'>已审核</font>";
                  break;
              default:
                  $data_list[$k]['mystatus'] = "<font color='#FF0000'>审核不通过</font>";
                  break;
          }
      }

    //计算审核中的数量
    $mw['c_status'] = 0;
    $this->shz = M('Merchant_id')->where($mw)->count();    

    $this->list = $data_list;
    $this->count = $date['count'];//分页\
    $this->page = $date['Page'];//分页
    $this->root_url = GetHost()."/";
    $this->post = $parent;
    $this->display();
  }

  //Excel导出数据
  public function educeIndex(){
      $Order = D('Merchant','Behind');
      $Order -> sheetIndexnt();
  }

  //Excel导入数据
  public function Tolead(){
    if (!empty($_FILES['file_stu']['name'])){
      $tmp_file = $_FILES['file_stu']['tmp_name'];
      $file_types = explode( ".",$_FILES['file_stu']['name'] );
      $file_type = $file_types[count($file_types)- 1];

      /*判别是不是.xls文件，判别是不是excel文件*/
      if (strtolower ($file_type) != "xls"){
        $this->error ( '不是Excel文件，重新上传' );
      }

      $fileresult = uploadfile('merchant');
      if ($fileresult['code'] != 0) {
          $this->error($fileresult['msg']);
      }
      $filepath = $fileresult['data']['file_stu'];

      $Order = D('Merchant','Behind');

      $result = $Order -> Tolead($filepath);

      if($result['code'] != 0){
        $this->error ($result['msg']);
      }
    }
  }

  //指定商家连锁总店，加盟店身份
  function AddIdentity(){
    $ucode = I('ucode');
    $itype = I('itype');

    if(empty($ucode) && empty($ucode)){
       $this->ajaxReturn(Message(1001,"参数错误"));
    }

    $parr['ucode'] = $ucode;
    $parr['itype'] = $itype;
    
    $result = IGD('Identity','Store')->AddIdentity($parr);

    $this->ajaxReturn($result);
  } 

    //注销代理商
    function cancel_agent()
    {
        $code = I('code');

        //查询金卡是否绑定代理
        $where['c_code'] = $code;
        $where['c_rule'] = 2;
        $incodeinfo = M('Invite_code')->where($where)->find();
        if (empty($incodeinfo) || empty($incodeinfo['c_ucode'])) {
            $this->ajaxReturn(Message(1000,'该金卡不能操作'));
        }

        $db = M('');
        $db->startTrans();

        //清空绑定代理商
        $save['c_ucode'] = '';
        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Invite_code')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            $this->ajaxReturn(Message(1001,'操作失败'));
        }

        //改变代理商身份
        $usersave['c_isagent'] = 0;
        $usersave['c_acode'] = '';
        $userwhere['c_ucode'] = $incodeinfo['c_ucode'];
        $result = M('Users')->where($userwhere)->save($usersave);
        if (!$result) {
            $db->rollback();
            $this->ajaxReturn(Message(1002,'操作失败'));
        }

        //清空代理商资料表
        $agentwhere['c_ucode'] = $incodeinfo['c_ucode'];
        $result = M('Check_shopinfo')->where($agentwhere)->delete();
        if (!$result) {
            $db->rollback();
            $this->ajaxReturn(Message(1003,'操作失败'));
        }

        $db->commit();
        $this->ajaxReturn(Message(0,'操作成功'));
    } 

        
    //串码管理
    public function codelist(){

      //串码激活人昵称       
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

         //串码拥有人昵称       
        $c_username = trim(I('nickname'));
        if (!empty($c_username)) {
            $wuse['c_nickname'] = $c_username;
        }

        $phone = trim(I('phone'));
        if (!empty($phone)) {
            $wuse['c_phone'] = $phone;
        }

        $c_acode = trim(I('acode'));
        if (!empty($c_acode)) {
            $w['a.c_acode'] = $c_acode;
            $this->acode = $c_acode;
        }
        if (count($wuse) > 0) {
            $usinfon = M('Users')->where($wuse)->field('c_ucode')->find();
            if ($usinfon) {
                $w['a.c_acode'] = $usinfon['c_ucode'];
            }
        }
        
        //用户编码
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
            $w['a.c_ucode'] = $ucode;
        }

        // $acode = trim(I('acode'));
        // if (!empty($acode)) {
        //     $w['a.c_acode'] = $acode;
        // }

        $c_state = trim(I('c_state'));
        if (!empty($c_state)) {
            $w['a.c_state'] = $c_state;
        }
        
        $c_code = trim(I('c_code'));
        if (!empty($c_code)) {
            $w['a.c_code'] = $c_code;
        }



        $db = M('Check_codelist as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        // $panrn['join'] = 'left join t_users as u on a.c_ucode=u.c_ucode';
        // $panrn['join1'] = 'left join t_users as ui on a.c_acode=ui.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            $uwe['c_ucode'] = $v['c_acode'];
            $userinfon = M('Users')->where($uwe)->field('c_nickname,c_phone')->find();
            $data_list[$k]['nickname'] = $userinfon['c_nickname'];
            $data_list[$k]['phone'] = $userinfon['c_phone'];
            
           
        }

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

      //服务费管理列表
    public function serve(){
        $db = M('Order_agent as a');
        //条件
        $c_orderid = trim(I('c_orderid'));
        if (!empty($c_orderid)) {
            $w['a.c_orderid'] = array('like', "%{$c_orderid}%");
        }

        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['ui.c_nickname'] = array('like', "%{$c_name}%");
        }

        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = $c_nickname;
        }

        //支付状态
        // $c_pay_state = trim(I('c_pay_state'));
        // if (!empty($c_pay_state)) {
        //     $w['a.c_pay_state'] =  array('like', "%{$c_pay_state}%");
        // }

         $c_pay_state = trim(I('c_pay_state'));
        if (!empty($c_pay_state)) {
            if($c_pay_state == 'sqz'){
               $w['a.c_pay_state'] = 0;
            }else{
                $w['a.c_pay_state'] = $c_pay_state;
            }
        }


        
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,ui.c_nickname as c_name';
        $panrn['join'] = 'left join t_users as u on a.c_ucode=u.c_ucode';
        $panrn['join1'] = 'left join t_users as ui on a.c_acode=ui.c_ucode';
        // $panrn['join2'] = 'left join t_invite_code as i on i.c_ucode=u.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
      }

}