<?php
namespace Home\Controller;
use Think\Controller;
class MemberController extends BaseController{
    //会员列表
    public function member_list(){
        $hide = I('hide');
        if (!empty($hide)) {
            $this->hide = 'ctrl_hidden';
        }
    	$db = M('users as u');
		//条件
    	$c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }
        $isshop = I('isshop');
        if(!empty($isshop)){
            if($isshop == 1){
                $w['u.c_shop'] = $isshop;
            }else{
                $w['u.c_shop'] = 0;
            }
        }

        $c_acode = trim(I('acode'));
        if (!empty($c_acode)) {
            $w['u.c_acode'] = $c_acode;
            $this->acode = $c_acode;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['u.c_ucode'] = $c_ucode;
        }

        $shop = I('shop');
        if(!empty($shop)){
            $w['u.c_shop'] = $shop;
        }

        $isagent = I('isagent');
        if(!empty($isagent)){
            $w['u.c_isagent'] = $isagent;
        }

        $agent = I('agent');
        if(!empty($agent)){
            $w = 'u.c_isagent <> 0';
        }

        $isfixed1 = I('isfixed1');
        if(!empty($isfixed1)){
            $w['u.c_isfixed1'] = $isfixed1;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'u.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'u.*';
        // $panrn['join'] = 'left join t_user_local as l on u.c_ucode=l.c_ucode';
        // $panrn['join1'] = 'left join t_users as ui on u.c_acode=ui.c_ucode';
        // ,ui.c_nickname as c_name,l.c_isfixed
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_acode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname')->find();
            $data_list[$k]['c_name'] = $userinfo['c_nickname'];
            $uws['c_ucode'] = $v['c_ucode'];
            $userinfor = M('User_local')->where($uws)->field('c_isfixed')->find();
            $data_list[$k]['c_isfixed'] = $userinfor['c_isfixed'];

        

           
        }
        // $data_list1= $date['list1'];
        // foreach ($data_list1 as $k => $v) {
        //     $uws['c_ucode'] = $v['c_ucode'];
        //     $userinfo = M('User_local')->where($uws)->field('c_isfixed')->find();
        //     $data_list1[$k]['c_isfixed'] = $userinfo['c_isfixed'];
            
        // }

        $this->list = $data_list;
        // $this->list1 = $data_list1;
        // $this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->display();
    }

    //会员详情
    public function member_show(){
    	$ucode = I('ucode');
    	$db = M('users as u');
    	$w['u.c_ucode'] = $ucode;
    	$panrn['where'] = $w;
        $panrn['field'] = 'u.*,p.c_rongyun_token';
        $panrn['join'] = 'left join t_user_part as p on p.c_ucode=u.c_ucode';
        $list = D('Db','Behind');
		$this->date = $list->mate_find($db,$panrn);
		$this->root_url = GetHost()."/";
    	$this->display();
    }

    //会员详情
    public function password_show(){
        $ucode = I('ucode');
        $db = M('users as u');
        $w['c_ucode'] = $ucode;
        $pwd = $db->where($w)->getField('c_password');

        $this->password = decrypt($pwd, C('ENCRYPT_KEY'));echo $this->password;die;
        $this->display();
    }

    //微信解绑
    public function auth_del(){
        $ucode = I('ucode');
        $db = M('Users_auth');
        $w['c_ucode'] = $ucode;
        $w['c_type'] = 1;

        $auth_info = $db->where($w)->find();
        if(!$auth_info){
            $this->ajaxReturn(Message(1001,"授权关系不存在"));
        }

        $result = $db->where($w)->delete();

        if(!$result){
            $this->ajaxReturn(Message(1002,"解绑失败"));
        }

        $this->ajaxReturn(Message(0,"解绑成功"));
    }

    //获取融云token
    public function get_rongyun(){
    	$str = I('str');
     	$formbul = str_replace('&quot;', '"', $str);
     	$data = objarray_to_array(json_decode($formbul));

     	$ucode = $data['ucode'];

    	$rongyun = IGD('UserProcess','Rongy');
    	$result = $rongyun -> token($ucode);

    	$this->ajaxReturn($result);
    }

    //会员添加
    public function member_add(){
    	$this->level = D('Db','Behind')->mate_select(M('user_level'),'');
    	$parr['parentid'] = 1;
    	$parr['regiontype'] = 1;
    	$this->province = IGD('User','User')->GetAddress($parr);
    	$this->display();
    }

    //会员添加提交数据
    public function ajax_member_add(){
    	$str = I('str');
     	$formbul = str_replace('&quot;', '"', $str);
     	$data = objarray_to_array(json_decode($formbul));

     	if(empty($data['phone']) || empty($data['pwd']) || empty($data['nickname']) || empty($data['c_level']) || empty($data['sex'])){
     		$this->ajaxReturn(Message(1006,"带*号的选项必须填写"));
     	}
        if(!empty($phone)){
            $result = checkMobile($data['phone']);
            if(!$result){
                $this->ajaxReturn(Message(1006,"注册手机号格式不正确"));
            }
        }

     	if($data['c_shop'] == 1){
            if(empty($data['tj_phone'])){
                $this->ajaxReturn(Message(1006,"开店必须填写推荐人注册号码"));
            }
     		if(empty($data['c_invitationcode']) || empty($data['c_num'])){
     			$this->ajaxReturn(Message(1006,"带*号的选项必须填写"));
     		}
     	}

     	$message = D('User','Behind')->userAdd($data);

		$this->ajaxReturn($message);
    }

    //根据地区名字获取地址
    public function loadRegion(){
    	$cname = I("cname");
    	$value = I("value");

    	$where['region_name'] = $cname;
    	$parentid = M('Region')->where($where)->getField('region_id');

    	$parr['parentid'] = intval($parentid);
    	$parr['regiontype'] = intval($value);

    	$date = IGD('User','User')->GetAddress($parr);
    	$this->ajaxReturn($date);
    }

    //会员编辑
    public function member_edit(){
    	$ucode = I('ucode');
    	$where['c_ucode'] = $ucode;
    	$userinfo = M('users')->where($where)->find();

    	$pcode = M('users_tuijian')->where($where)->getField('c_pcode');
    	$where1['c_ucode'] = $pcode;
    	$userinfo['tj_phone'] = M('users')->where($where1) ->getField('c_phone');

    	$this->level = D('Db','Behind')->mate_select(M('user_level'),'');
    	$parr['parentid'] = 1;
    	$parr['regiontype'] = 1;
    	$this->province = IGD('User','User')->GetAddress($parr);
    	$this->data = $userinfo;
    	$this->display();
    }

    //会员编辑提交数据
    public function ajax_member_edit(){
    	$str = I('str');
     	$formbul = str_replace('&quot;', '"', $str);
     	$data = objarray_to_array(json_decode($formbul));

     	if($data['c_shop'] == 1){
            if(empty($data['tj_phone'])){
                $this->ajaxReturn(Message(1006,"开店必须填写推荐人注册号码"));
            }
     		if(empty($data['c_invitationcode']) || $data['c_num'] == ''){
     			$this->ajaxReturn(Message(1006,"店铺推荐码或邀请码数量必须填写"));
     		}
     	}else{
     		$data['c_invitationcode'] = '';
     		$data['c_num'] = 0;
     	}

     	//操作推荐关系
     	$agentid_reslut = D('User','Behind')->GetAgentid($data);
     	if($agentid_reslut['code'] !== 0){
     		$this->ajaxReturn($agentid_reslut);
     	}

     	//保存数据
     	$message = D('User','Behind')->userEdit($data);

     	$this->ajaxReturn($message);
    }

    //ajax 修改用户余额
    public function ajax_change_money(){
    	$parr['ucode'] = I('ucode');
    	$parr['money'] = I('value');
    	$parr['desc'] = I('desc');

    	$handle = I('handle');
    	if($handle == "add"){
    		$parr['type'] = 1;
    	}else{
    		$parr['type'] = 0;
    	}

    	$parr['source'] = 2;
    	$parr['state'] = 1;
    	$parr['isagent'] = 0;

    	$result = IGD('Money','User')->OptionMoney($parr);

    	if($result['code'] == 0){
    		$where['c_ucode'] = I('ucode');
    		$user_money = M('Users')->where($where)->field('c_money')->find();
    		$this->ajaxReturn(MessageInfo(0,"修改成功",$user_money));
    	}

    	$this->ajaxReturn($result);
    }

    //会员修改密码
    public function change_password(){
    	$ucode = I('ucode');
    	$where['c_ucode'] = $ucode;
    	$this->data = M('users')->where($where)->field('c_ucode,c_nickname,c_phone')->find();
    	if(IS_AJAX){
	     	$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));

	     	if (empty($data['password']) || empty($data['password2'])) {
	     		$this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
	     	}

	     	$pwd = $data['password'];
	     	$jmpwd = encrypt($pwd,C('ENCRYPT_KEY'));

	      	$where['c_ucode'] = $data['ucode'];
	      	$save_data['c_password'] = $jmpwd;
	        $result = M('Users')->where($where)->save($save_data);
	        if($result || $result == 0) {
                $this->ajaxReturn(Message(0,'修改成功'));
            }else{
                $this->ajaxReturn(Message(1002,'修改失败'));
            }
	    }
    	$this->display();
    }

    //清空会员支付密码
    public function del_safepwd(){
        $ucode = I('ucode');
        if (empty($ucode)) {
            $this->ajaxReturn(Message(1000,'请选择用户'));
        }

        $where['c_ucode'] = $ucode;
        $save_data['c_safepwd'] = '';
        $result = M('Users')->where($where)->save($save_data);
        if(!$result) {
            $this->ajaxReturn(Message(1002,'操作失败'));
        }
        $this->ajaxReturn(Message(0,'操作成功'));
    }

    //非会员资料
    public function agent_info(){
        $ucode = I('ucode');
        $where['c_ucode'] = $ucode;
        $agentinfo = M('check_shopinfo')->where($where)->find();
        $agentinfo['c_nickname'] = M('users')->where($where)->getField('c_nickname');
        if(IS_POST){
          $filepath = 'Agent';
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
            $result = IGD('Setinfo','Agent')->syncData($ucode);  //同步数据
            if ($result['code'] != 0) {
                $this->error("同步数据失败");die;
            }
            $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Member/member_list';
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
          }else{
            $this->error("编辑失败");
          }
        }
        $this->root_url = GetHost()."/";
        $this->vo = $agentinfo;
        $this->display();
    }

    //升级代理商
    public function upgrade(){
        $this->ucode = I('ucode');
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));

            if($data['isagent'] == 2){
                if(empty($data['tj_phone']) && empty($data['invite_code'])){
                    $this->ajaxReturn(Message(1001,"5万代理商必须确定推荐关系"));
                }
                if(!empty($data['tj_phone']) && !empty($data['invite_code'])){
                    $this->ajaxReturn(Message(1002,"推荐代理商、邀请码编码只能填写一个"));
                }
            }
            //升级代理商
            $agentid_reslut = D('User','Behind')->UpGrade($data);
            $this->ajaxReturn($agentid_reslut);
        }
        $this->display();
    }

    //会员关系上一级
    public function userrelate_parent(){
        $ucode = I('ucode');
        $where['c_ucode'] = $ucode;
        $pcode = M('users_tuijian')->where($where)->getField('c_pcode');

        $db = M('users as u');
        //条件
        $w['u.c_ucode'] = $pcode;

        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }
        $isagent = I('isagent');
        if(!empty($isagent)){
            $w['u.c_isagent'] = $isagent;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'u.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'u.*,ui.c_nickname as c_name,l.c_level_name';
        $panrn['join'] = 'left join t_user_level as l on u.c_level=l.c_id';
        $panrn['join1'] = 'left join t_users as ui on u.c_acode=ui.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        
        $page_name = "会员上一级关系";
        $this->display('userrelate');
    }

    //会员关系下一级
    public function userrelate_own(){
        $ucode = I('ucode');

        $db = M('users as u');
        //条件
        $w['t.c_pcode'] = $ucode;

        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }
        $isagent = I('isagent');
        if(!empty($isagent)){
            $w['u.c_isagent'] = $isagent;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'u.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'u.*,ui.c_nickname as c_name,l.c_level_name';
        $panrn['join'] = 'left join t_user_level as l on u.c_level=l.c_id';
        $panrn['join1'] = 'left join t_users as ui on u.c_acode=ui.c_ucode';
        $panrn['join2'] = 'left join t_users_tuijian as t on t.c_ucode=u.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $page_name = "会员下一级关系";
        $this->display('userrelate');
    }

    //会员收货地址
    public function user_address(){
    	$ucode = I('ucode');
    	$w['c_ucode'] = $ucode;
    	$parr['where'] = $w;
    	$this->data = D('Db','Behind')->mate_select(M('users_address'),$parr);
    	$this->count = M('users_address')->where($w)->count();
    	$this->ucode = $ucode;
    	$this->display();
    }

    //会员收货地址添加
    public function address_add(){
        $ucode = I('ucode');
        $parr['parentid'] = 1;
    	$parr['regiontype'] = 1;
    	$this->province = IGD('User','User')->GetAddress($parr);
    	$this->action = 'Member/address_add';
    	$this->ucode = $ucode;
    	if(IS_AJAX){
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));
	     	if (empty($data['consignee']) || empty($data['mobile']) || empty($data['c_province']) || empty($data['c_city']) || empty($data['c_district']) || empty($data['ucode']) || empty($data['address'])) {
	     		$this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
	     	}

	     	$handle = 1;//添加标志
	     	$message = D('User','Behind')->addressAdd($data,$handle);

	     	$this->ajaxReturn($message);
    	}
    	$this->display();
    }

    //会员收货地址编辑
    public function address_edit(){
        $ucode = I('ucode');
        $parr['parentid'] = 1;
    	$parr['regiontype'] = 1;
    	$this->province = IGD('User','User')->GetAddress($parr);
    	$this->action = 'Member/address_edit';
    	$this->ucode = $ucode;

    	$addressid = I('addressid');
    	$where['c_id'] = intval($addressid);
    	$this->data = M('users_address')->where($where)->find();

    	if(IS_AJAX){
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));

	     	if (empty($data['consignee']) || empty($data['mobile']) || empty($data['c_province']) || empty($data['c_city']) || empty($data['c_district']) || empty($data['ucode']) || empty($data['address'])) {
	     		$this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
	     	}

	     	$handle = 2;//添加标志
	     	$message = D('User','Behind')->addressAdd($data,$handle);

	     	$this->ajaxReturn($message);
    	}
    	$this->addrId = intval($addressid);
    	$this->display('address_add');
    }

    //根据地区编号获取地址
    public function  GetAddr(){
    	$parentid = I('id');
    	$regiontype = I('value');

    	$parr['parentid'] = $parentid;
    	$parr['regiontype'] = $regiontype;
    	$date = IGD('User','User')->GetAddress($parr);
    	$this->ajaxReturn($date);
    }

    //会员收货地址删除
    public function address_delete(){
    	$Id = I('Id');
  		$idstr = str_replace('|', ',', $Id);
		$where['c_id'] = array('in',$idstr);
		$result = M('users_address')->where($where)->delete();
		if($result){
			$this->ajaxReturn(Message(0,'删除成功'));
		}else{
			$this->ajaxReturn(Message(1000,'删除失败'));
		}
    }

    public $mtype =array('全部','1普城订单','2后台','3活动','4蜜城订单','5普城跨界','6提现','7注册','8老注册','9扫码','10转发','11绑定','12跨界扫码','13普城购返','14普城推返','15蜜城跨界','16普通退款','17蜜城退款','18店铺红包');
    //会员账目明细
    public function detail_account(){
        $db = M('users_moneylog as m');
        //条件
        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $wus['c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['m.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['m.c_ucode'] = $usinfo['c_ucode'];
            }
        }

        $income = I('income');
        if ($income == 1) {
            $w['m.c_money'] = array('GT',0);
        } else if ($income == 2) {
            $w['m.c_money'] = array('LT',0);
        }

        $key = trim(I('key'));
        if (!empty($key)) {
            $w['m.c_key'] = $key;
        }        
        $c_state = trim(I('c_state'));
        if (!empty($c_state)) {
            if($c_state == 'dj'){
               $c_state = 0;
            }
            $w['m.c_state'] = $c_state;
        }

        $c_source = trim(I('c_source'));
        if (!empty($c_source)) {
            $w['m.c_source'] = $c_source;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'm.c_addtime desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'm.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=m.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            $data_list[$k]['c_headimg'] = $userinfo['c_headimg'];
            $data_list[$k]['c_source'] = $this->mtype[$data_list[$k]['c_source']];
            switch ($data_list[$k]['c_state']) {
                case 0:
                    $data_list[$k]['c_state'] = "<font color='#FF0000'>已冻结</font>";
                    break;
                case 1:
                    $data_list[$k]['c_state'] = "<font color='#00FF00'>已完成</font>";
                    break;
                default:
                    $data_list[$k]['c_state'] = "<font color='#808080'>已取消</font>";
                    break;
            }
        }

        //给单个用户统计
        if ($this->ucode) {
            $countarr = $this->Get_count($w);

            if($countarr[0]['d'] != 0){
                $this->warning = '警告：总收入减总支出不等于总余额。相差：'.$countarr[0]['d'].'(元)！';
            }
            $this->m_count = $countarr[0];
        }

        $this->gmtype = $this->mtype;
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //统计用户总收入、支出
    function Get_count($w){
        if(!empty($w)){
            $wkey = array_keys($w);
            $wvalues = array_values($w);
            $where = '';
            $flag = 0;
            foreach ($wkey as $key => $value) {
                if(strpos($value, 'm.c_ucode') !== false){
                    $where .= " and m.c_ucode='".$wvalues[$key]."'";
                }elseif(strpos($value, 'm.c_state') !== false){
                    $where .= " and m.c_state='".$wvalues[$key]."'";
                    $flag = 1;
                }elseif(strpos($value, 'm.c_source') !== false){
                    $where .= " and m.c_source='".$wvalues[$key]."'";
                    $flag = 1;
                }
            }
        }

        $model = M();
        $sql = "select sum(case when m.c_money<0 then m.c_money else 0 end) as a,
        sum(case when m.c_money>0 then m.c_money else 0 end) as b,
        sum(case when m.c_bkmoney>0 then m.c_bkmoney else 0 end) as bk from t_users_moneylog as m where 1=1 ".$where;
        $data = $model->query($sql);

        if($flag == 0){
            $sql1 = "select sum(u.c_money) as c from t_users as u where 1=1 and u.c_ucode='".$w['m.c_ucode']."'";
            $data1 = $model->query($sql1);
            $data[0]['c'] = $data1[0]['c'];
        }

        $data[0]['d'] = sprintf("%.2f", ($data1[0]['c']-$data[0]['b'])-$data[0]['a']+$data[0]['bk']);
        return $data;
    }

    //商家设置邮费列表
    public function postage(){
         $db = M('users_free as f');
        //条件
        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'f.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'f.*,u.c_nickname,u.c_headimg,pu.c_nickname as dlname';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=f.c_ucode';
        $panrn['join1'] = 'left join t_users as pu on u.c_ucode=pu.c_acode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //用户申请提款记录
    public function applyFor(){
        $db = M('users_drawing as d');
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['u.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['u.c_nickname'] = array('like', "%{$c_username}%");
        }
        $c_tx_code = trim(I('c_tx_code'));
        if (!empty($c_tx_code)) {
            $w['d.c_tx_code'] = array('like', "%{$c_tx_code}%");
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "d.c_addtime between '".$begintime."' and '".$endtime."'";
        }

        $c_state = trim(I('c_state'));
        if (!empty($c_state)) {
            if($c_state == 'sqz'){
               $w['d.c_state'] = 0;
            }else{
                $w['d.c_state'] = $c_state;
            }
        }

        $w['c_issupplier'] = 0;
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'd.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'd.*,u.c_nickname,u.c_ucode,u.c_phone,u.c_headimg';//,pu.c_nickname as dlname
        $panrn['join'] = 'left join t_users as u on u.c_ucode=d.c_ucode';
        // $panrn['join1'] = 'left join t_users as pu on u.c_ucode=pu.c_acode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            switch ($data_list[$k]['c_state']) {
                case 0:
                    $data_list[$k]['mystate'] = "<font color='#808080'>申请中</font>";
                    break;
                case 1:
                    $data_list[$k]['mystate'] = "<font color='#00FF00'>申请成功</font>";
                    break;
                default:
                    $data_list[$k]['mystate'] = "<font color='#FF0000'>申请失败</font>";
                    break;
            }
        }
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //Excel导出提款申请数据
    public function educeIndex(){
        $Order = D('Applyfor','Behind');
        $Order -> sheetIndexnt();
    }

    //提现结果处理
    public function ajax_apply_handle(){
        $db = M('users_drawing');

        $id = intval(I('Id'));
        $handle = intval(I('handle'));

        $drawing_info = $db->where('c_id='.$id)->find();
        $ucode = $drawing_info['c_ucode'];
        $money = $drawing_info['c_money'];


        $save_data['c_updatetime'] = date('Y-m-d H:i:s');

        if($handle == 1){//同意提款，只改变状态
            $save_data['c_state'] = 1;
            $r = $db->where('c_id='.$id)->save($save_data);
            $content = '您提现余额￥'.$money.'申请，系统已同意，系统将进行转账处理';
        }else{//不同意提款，退还余额
            $parr['ucode'] = $ucode;
            $parr['money'] = $money;
            $parr['source'] = 6;
            $parr['key'] = $drawing_info['c_tx_code'];
            $parr['desc'] = "提现余额退还";
            $parr['state'] = 1;
            $parr['type'] = 1;
            $parr['showimg'] = 'Uploads/settlementshow/tis.png';
            $parr['showtext'] = '提现失败';

            $result = IGD('Money','User')->OptionMoney($parr);

            if($result['code'] !== 0){
                $this->ajaxReturn($result);
            }

            $save_data['c_state'] = 2;
            $r = $db->where('c_id='.$id)->save($save_data);
            $content = '您提现余额￥'.$money.'申请，系统不同意，如有疑问请跟我们联系';
        }

        //给用户发送相关消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] =  $content;
        $msgdata['tag'] = 2;
        $msgdata['tagvalue'] = GetHost(1).'/index.php/Home/Balance/drawinglog';
        $msgdata['weburl'] = GetHost(1).'/index.php/Home/Balance/drawinglog';
        $Msgcentre->CreateMessege($msgdata);

        if(!$r){
            $this->ajaxReturn(Message(1001,"操作失败"));
        }

        $this->ajaxReturn(Message(0,"操作成功"));
    }

    //保存提现第三方单号
    public function save_thirdparty(){
        $txcode = I('txcode');
        $thirdparty_code = I('thirdparty');
        if(empty($txcode) || empty($thirdparty_code)){
          $this->ajaxReturn(Message(1001,"参数错误，不允许操作"));
        }
        $uu = "";
        $model = M('users_drawing');
        $where['c_tx_code'] = $txcode;
        $date['c_thirdparty_code'] = $thirdparty_code;
        $result = $model->where($where)->save($date);
        if($result){
            $this->ajaxReturn(Message(0,"操作成功"));
        }
        echo $uu;
    }

    //会员银行卡管理
    public function bank(){
        $db = M('users_bank as b');
        //条件
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

        $c_phoner = trim(I('phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }

        $c_uname = trim(I('c_uname'));
        if (!empty($c_uname)) {
            $w['b.c_uname'] = $c_uname;
        }

        $c_carid = trim(I('c_carid'));
        if (!empty($c_carid)) {
            $w['b.c_carid'] = $c_carid;
        }

        $c_shop = trim(I('c_shop'));
        if ($c_shop == 1) {
            $w['a.c_shop'] = 1;
        } else if ($c_shop == 2) {
            $w['a.c_shop'] = 0;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'b.c_id asc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'b.*,u.c_nickname';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=b.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //会员银行卡编辑
    public function bank_edit(){
        $Id = I('Id');
        $where['b.c_id'] = $Id;
        $this->data = M('users_bank as b')->field('b.*,u.c_nickname')->join('left join t_users as u on b.c_ucode=u.c_ucode')->where($where)->find();

        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));

            if (empty($data['c_uname']) || empty($data['c_carid'])) {
                $this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
            }

            $result = M('users_bank')->save($data);

            if($result || $result == 0){
                $this->ajaxReturn(Message(0,"编辑成功"));
            }else{
                $this->ajaxReturn(Message(1002,"编辑失败"));
            }
        }

            
        $this->display();
    }

    //会员银行卡信息删除
    public function bank_delete(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('users_bank')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    // 访问记录
    public function member_visit()
    {
        $db = M('users_spacelog as p');
        //条件
        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $wus['c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = array('like', "%{$c_username}%");
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['p.c_vucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->select();
            $ustr = arr_to_str($usinfo);
            if ($ustr) {
                $w['p.c_vucode'] = array('in',$ustr);
            }
        }
        
        $username = trim(I('username'));
        if (!empty($username)) {
            $w['p.c_username'] = array('like', "%{$username}%");
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
        // $panrn['join'] = 'left join t_users as b on p.c_vucode=b.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_vucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
        }
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->show();
    }

    // //访问记录添加
    // public function visit_add(){

    // }

    // //访问记录编辑
    // public function visit_edit(){

    // }

    //关注记录统计
    public function attention_list()
    {
        $db = M('users as p');
        //条件
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
            $w['b.c_attention_ucode'] = $ucode;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['p.c_nickname'] = array('like', "%{$nickname}%");
        }

        $panrn['where'] = $w;
        $this->post = I('param.');
        $panrn['order'] = 'p.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.c_nickname,p.c_headimg,b.*,c.c_nickname as c_name';
        $panrn['join'] = 'inner join t_users_attention as b on p.c_ucode=b.c_ucode';
        $panrn['join1'] = 'inner join t_users as c on c.c_ucode=b.c_attention_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->ucode = $ucode;
        $this->show();
    }

    //新增关注
    public function attention_add()
    {
        $this->ucode = I('ucode');
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));

            if (empty($data['ucode'])) {
                $this->ajaxReturn(Message(1003,'请选择用户'));
            }
            
            $db = M('');
            $db->startTrans();

            $attdata['c_ucode'] = $data['ucode'];
            $attdata['c_attention_ucode'] = $data['attention_ucode'];

            $count = M('Users_attention')->where($attdata)->count();
            if($count > 0){
                $this->ajaxReturn(Message(1000,'该用户已关注'));
            }

            $attdata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Users_attention')->add($attdata);
            if (!$result) {
                $db->rollback();
                $this->ajaxReturn(Message(1001,'添加记录失败'));
            }

            $w1['c_ucode'] = $data['attention_ucode'];
            $count1 = M('Users_date')->where($w1)->count();
            if($count1 == 0){
                $result = M('Users_date')->add($w1);
                if (!$result) {
                    $db->rollback();
                    $this->ajaxReturn(Message(1002,'添加失败'));
                }
            }

            $result = M('Users_date')->where($w1)->setInc('c_attention',1);
            if (!$result) {
                $db->rollback();
                $this->ajaxReturn(Message(1003,'添加失败'));
            }
            $db->commit();
            $this->ajaxReturn(Message(0,'添加成功'));
        }
        $this->display();
    }

    //操作用户记录
    public function optionuserlog()
    {
        $ucode = I('ucode');
        $where['c_ucode'] = $ucode;
        $data = M('users_date')->where($where)->find();
        $data['c_ucode'] = $ucode;
        $this->data = $data;
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $data = objarray_to_array(json_decode($formbul));
            if (empty($data['ucode'])) {
                $this->ajaxReturn(Message(1001,'请选择相关用户'));
            }
            if (!empty($data['pv'])) {
                if (!is_numeric($data['pv'])) {
                    $this->ajaxReturn(Message(1001,'访问量必须为数字'));
                }
            }
            // if (!empty($data['attention'])) {
            //     if (!is_numeric($data['attention'])) {
            //         $this->ajaxReturn(Message(1001,'关注量必须为数字'));
            //     }
            // }
            if (!empty($data['collect'])) {
                if (!is_numeric($data['collect'])) {
                    $this->ajaxReturn(Message(1001,'收藏量必须为数字'));
                }
            }

            $save_where['c_ucode'] = $data['ucode'];
            $save_data['c_ucode'] = $data['ucode'];
            $save_data['c_pv'] = $data['pv'];
            // $save_data['c_attention'] = $data['attention'];
            $save_data['c_collect'] = $data['collect'];
            if (M('users_date')->where($save_where)->find()) {
                $result = M('users_date')->where($save_where)->save($save_data);
            } else {
                $result = M('users_date')->add($save_data);
            }

            if(!$result) {
                $this->ajaxReturn(Message(1002,'保存失败'));
            }
            $this->ajaxReturn(Message(0,'保存成功'));
        }

        $this->show();
    }
        
    //授权列表
    public function authlist()
    {
        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        //用户帐号
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

        //ID
        $c_id = trim(I('c_id'));
        if (!empty($c_id)) {
            $w['a.c_id'] = $c_id;
        }

        //授权昵称
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['a.c_name'] = $c_name;
        }

        //授权标识
        $c_openid = trim(I('c_openid'));
        if (!empty($c_openid)) {
            $w['a.c_openid'] = $c_openid;
        }
        
        $c_unionid = trim(I('c_unionid'));
        if (!empty($c_unionid)) {
            $w['a.c_unionid'] = $c_unionid;
        }

        //授权类型
        $c_type = trim(I('c_type'));
        if (!empty($c_type)) {
            $w['a.c_type'] = $c_type;
        }

        $db = M('Users_auth as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        // $panrn['join1'] = 'left join t_users as ui on u.c_acode=ui.c_ucode';
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
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //授权删除
    public function auth_delete(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Users_auth')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }


    //商家账目
    public function account()
    {

        $db = M('Users');
        //条件
        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $w['c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['c_nickname'] = array('like', "%{$c_username}%");
        }
        $isshop = I('isshop');
        if(!empty($isshop)){
            if($isshop == 1){
                $w['c_shop'] = $isshop;
            }else{
                $w['c_shop'] = 0;
            }
        }

        $c_acode = trim(I('acode'));
        if (!empty($c_acode)) {
            $w['c_acode'] = $c_acode;
            $this->acode = $c_acode;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['c_ucode'] = $c_ucode;
        }

        $shop = I('shop');
        if(!empty($shop)){
            $w['c_shop'] = $shop;
        }

        $isagent = I('isagent');
        if(!empty($isagent)){
            $w['c_isagent'] = $isagent;
        }

        $agent = I('agent');
        if(!empty($agent)){
            $w = 'c_isagent <> 0';
        }

        $isfixed1 = I('isfixed1');
        if(!empty($isfixed1)){
            $w['c_isfixed1'] = $isfixed1;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = '*';
        
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $plist = $date['list'];
        foreach ($plist as $key => $value) {
            $plist[$key]['onincome'] = $this->CountMoney($value['c_ucode'],0,1);
            $plist[$key]['offincome'] = $this->CountMoney($value['c_ucode'],1,1);
            $plist[$key]['onpayout'] = $this->CountMoney($value['c_ucode'],0,0);
            $plist[$key]['offpayout'] = $this->CountMoney($value['c_ucode'],1,0);
        }

        $date['list'] = $plist;
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //统计收入支出
    function CountMoney($ucode,$sign,$type)
    {
        $where['c_ucode'] = $ucode;
        if ($sign == 1) {  //线下
            $where[] = array('c_source=9 or c_source=12');
        } else {
            $where[] = array('c_source<>9 and c_source<>12');
        }
        
        if ($type == 1) {  //收入
            $where['c_money'] = array('GT',0);
        } else {
            $where['c_money'] = array('LT',0);
        }

        $field = 'sum(c_money) as cmoney,sum(c_bkmoney) as bmoney';
        $count = M('Users_moneylog')->where($where)->field($field)->select();

        return $count[0]['cmoney']-$count[0]['c_bkmoney'];
    }   
    // 到账详情
    public function dateincome(){

        $db = M('users_moneylog as m');
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['m.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "m.c_addtime between '".$begintime."' and '".$endtime."'";
        }else{

            $begintime=date('Y-m-d 00:00:00', time());
            $endtime = date("Y-m-d H:i:s");
            $w[] = "m.c_addtime between '".$begintime."' and '".$endtime."'";
        }


        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'm.c_addtime desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'm.*';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            $data_list[$k]['c_headimg'] = $userinfo['c_headimg'];
            
        }

        //给单个用户统计
        if ($this->ucode) {
            $countarr = $this->Get_count($w);

            if($countarr[0]['d'] != 0){
                $this->warning = '警告：总收入减总支出不等于总余额。相差：'.$countarr[0]['d'].'(元)！';
            }
            $this->m_count = $countarr[0];
        }

        $this->gmtype = $this->mtype;
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();

    }    


    //银盛开户管理列表
    public function yspay(){
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
        

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "a.c_addtime between '".$begintime."' and '".$endtime."'";
        }


        //标志线下线上
        $c_storetype = trim(I('c_storetype'));
        if ($c_storetype == 1) {
            $w['a.c_storetype'] = 1;
        } else if ($c_storetype == 'sp') {
            $w['a.c_storetype'] = 0;
        }

        $c_merchant = trim(I('c_merchant'));
        if (!empty($c_merchant)) {
           $w['a.c_merchant'] = $c_merchant;
        }


        $c_person = trim(I('c_person'));
        if (!empty($c_person)) {
           $w['a.c_person'] = $c_person;
        }
        
        $c_storetials = trim(I('c_storetials'));
        if (!empty($c_storetials)) {
           $w['a.c_storetials'] = $c_storetials;
        }

        $c_reason = trim(I('c_reason'));
        if (!empty($c_reason)) {
            if ($c_reason == '已驳回') {
                $w[] = array("a.c_reason not like '加急%' and a.c_reason not like '可提交%' and a.c_reason is not null");
            } else if ($c_reason == '未处理') {
                $w[] = array("a.c_reason not like '加急%' and a.c_reason not like '可提交%'");
            } else if ($c_reason == '未备注') {
                $w['a.c_reason'] = array('exp', "is null");
            } else {
                $w['a.c_reason'] = array('like', "{$c_reason}%");
            }
        }


        //商户号
        $c_username = trim(I('c_username'));
        if (!empty($c_username)) {
           $w['a.c_username'] = $c_username;
        }

        $c_isagent = trim(I('c_isagent'));
        if ($c_isagent == 1) {
            $w['a.c_isagent'] = 1;
        } else if ($c_isagent == 'se') {
            $w['a.c_isagent'] = 0;
        }

        $c_isshop = trim(I('c_isshop'));
        if ($c_isshop == 1) {
            $w['a.c_isshop'] = 1;
        } else if ($c_isshop == 'sq') {
            $w['a.c_isshop'] = 0;
        }

        $c_openaccount = trim(I('c_openaccount'));
        if ($c_openaccount == 1) {
            $w['a.c_openaccount'] = 1;
            $panrn['order'] = 'a.c_updatetime desc';//排序
        } else if ($c_openaccount == 'er') {
            $w['a.c_openaccount'] = 0;
            $panrn['order'] = 'a.c_id desc';//排序
        } else if ($c_openaccount == 2) {
            $w['a.c_openaccount'] = 2;
            $panrn['order'] = 'a.c_id desc';//排序
        }

        $db = M('User_yspay as a');

        $panrn['where'] = $w;
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
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();

    }

    //Excel导出提款申请数据
    public function yspayinfo(){
        $Order = D('Yspay','Behind');
        $Order -> yspayinfo();
    }

    //Excel导入数据
    public function Leading_in(){
        if (!empty($_FILES['file_stu']['name'])){
            $tmp_file = $_FILES['file_stu']['tmp_name'];
            $file_types = explode( ".",$_FILES['file_stu']['name'] );
            $file_type = $file_types[count($file_types)- 1];

            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ($file_type) != "xls"){
                $this->error ('不是Excel文件，重新上传');
            }

            $fileresult = uploadfile('merchant');
            if ($fileresult['code'] != 0) {
                $this->error($fileresult['msg']);
            }
            $filepath = $fileresult['data']['file_stu'];

            $Order = D('Yspay','Behind');
            $result = $Order->Leading_in($filepath);
            if($result['code'] != 0){
                $this->error ($result['msg']);
            }
            $this->success('操作成功');
        } else {
            $this->error('请选择上传文件');
        }
    }

    //银盛开户资料
    public function yspay_info(){
        $Id = I('Id');
        $ucode = I('ucode');
        if (!empty($Id)) {
            $where['c_id'] = $Id;
        } else {
            $where['c_ucode'] = $ucode;
        }
      
        $agentinfo = M('User_yspay')->where($where)->find();
        $agentinfo['c_nickname'] = M('users')->where($where)->getField('c_nickname');
        if(IS_POST){
            foreach (array_keys($_POST) as $key => $value) {
                $narr = array_values($_POST)[$key];
                $needarr = array('c_email','c_qq','c_person','c_personphone','c_personidcard','c_bankuser','c_bankname','c_bankno','c_bankallname','c_bankcity','c_bankbranch',
                    'c_banktype','c_cashin','c_cashout','c_cashorder','c_cashcollect','c_cashpay','c_alipay','c_weixin','c_merchant','c_feeltype','c_merchantshort','c_storetype',
                    'c_isagent','c_isdataall','c_isshop','c_username');
                if (in_array($key, $needarr)) {
                    if (!isset($_POST[$value])) {
                        $this->error("带*内容项必须填写！");
                    }
                }
            }

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

            if($_FILES['c_bankcard_img']['name']!=''){
              $_POST['c_bankcard_img'] = upimg($filepath,$_FILES['c_bankcard_img']);
            }
            if($_FILES['c_bankcard_img1']['name']!=''){
              $_POST['c_bankcard_img1'] = upimg($filepath,$_FILES['c_bankcard_img1']);
            }

            //验证银行名
            $bw['c_name'] = $_POST['c_bankallname'];
            $bankinfo = M('Merchant_bank')->where($bw)->find();
            if (!$bankinfo) {
                $this->error("银行名称不符");
            }

            // //验证支行名与联行号
            // // $branw['c_bankname'] = $_POST['c_bankallname'];
            // $branw['c_name'] = $_POST['c_bankallname'];
            // $branw['c_code'] = $_POST['c_organino'];
            // $branceinfo = M('Merchant_branch')->where($branw)->find();
            // if (!$branceinfo) {
            //     $this->error("开户行、支行及联行号不匹配");
            // }

            // $_POST['c_checked'] = 3;

            $ucode = $_POST['ucode'];
            $w['c_ucode'] = $ucode;
            $result = M('User_yspay')->where($w)->save($_POST);
            if($result){
                $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Member/yspay';
                echo '<script language="javascript">alert("编辑成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
                $this->error("编辑失败");
            }
        }
        $this->vo = $agentinfo;
        $this->root_url = GetHost()."/";
        $this->display();
    }
    
    //ajax 修改活动开始
    public function dealyspay(){
        header('Content-type: text/json');
        $handle = I('handle');
        $Id = I('txcode');
        if(empty($Id) || empty($handle)) $this->ajaxReturn(Message(1011,'参数错误！'));      

        $db =  M('User_yspay');
        $db->startTrans();


        $yw['c_id'] = $Id;
        $yesinfo = $db->where($yw)->find();

        $ucode = $yesinfo['c_ucode'];
        $phone = $yesinfo['c_phone'];

        if(empty($ucode) || empty($phone)){
            $this->ajaxReturn(Message(1001,"信息有误"));
        }

        if ($handle == '1') {
            if ($yesinfo['c_banktype'] == 2) {
                $yesinfo['c_person'] = $yesinfo['c_merchant'];
            }
            //查询银盛金额并代付金额
            $result = IGD('Balance','User')->dfYesmoney($yesinfo);
            if ($result['code'] != 0) {
                $db->rollback();
                $this->ajaxReturn($result);
            }

            $where['c_id'] = $Id;
            $data['c_openaccount'] = 1;
            $data['c_updatetime'] = Date('Y-m-d H:i:s');
            $result = M('User_yspay')->where($where)->save($data);
            if(!$result){
                $db->rollback();
                $this->ajaxReturn(Message(1012,'操作失败！'));
            }

        } else if ($handle == '2') { //不通过
            if (empty($yesinfo['c_reason'])) {   //判断是否写入不通过的理由
                $db->rollback();
                $this->ajaxReturn(Message(1014,'请先填写不通过的原因'));
            }

            $where['c_id'] = $Id;
            $where['c_openaccount'] = 0;
            $data['c_openaccount'] = 2;
            $data['c_updatetime'] = Date('Y-m-d H:i:s');
            $result = M('User_yspay')->where($where)->save($data);
            if(!$result){
                $db->rollback();
                $this->ajaxReturn(Message(1012,'操作失败！'));
            }
            
            //通过请求发送消息
            $content = '由于：'.$yesinfo['c_reason'].',您提交的小蜜商家信息认证失败，请重新完善资料。';
            $weburl = GetHost(1) . '/index.php/Home/Getbusiness/index?type=1';
            $Msgcentre = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $ucode;
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['content'] =  $content;
            $msgdata['tag'] = 2;
            $msgdata['tagvalue'] = $weburl;
            $msgdata['weburl'] = $weburl;
            $Msgcentre->CreateMessege($msgdata);

            // 发送短信通知
            $content1 = "【微领地小蜜】由于：".$yesinfo['c_reason'].",您提交的小蜜商家信息认证失败，点击进入完善资料：http://t.cn/RCMGNvP";
            $parr['telephone'] = $yesinfo['c_phone'];
            $parr['type'] = 1000;
            $parr['userid'] = C('TEl_USER');
            $parr['account'] = C('TEl_ACCESS');
            $parr['password'] = C('TEl_PASSWORD');
            $parr['content'] = $content1;
            $result = IGD('Sendmsg', 'Login')->SendVerify($parr);
        }

        $db->commit();
        $this->ajaxReturn(Message(0,'操作成功！'));     
    }

    //保存错误原因
    public function save_thirdyspay(){
        $Id = I("Id");
        if(empty($Id)) $this->ajaxReturn(Message(1011,'参数错误！'));
        $where['c_id'] = $Id;
        $data['c_reason'] = I('thirdparty');
        $data['c_updatetime'] = Date('Y-m-d H:i:s');
        $result = M('User_yspay')->where($where)->save($data);
        if(!$result){
           $this->ajaxReturn(Message(1012,'操作失败！'));
        }
        $this->ajaxReturn(Message(0,'操作成功！'));
    }

    //分润明细
    public function share_list(){
        $db = M('Users_order_splitting as m');
        //条件
        $c_phoner = trim(I('c_phone'));
        if (!empty($c_phoner)) {
            $wus['c_phone'] = $c_phoner;
        }
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['m.c_ucode'] = $usinfo['c_ucode'];
            }
        }

        $key = trim(I('key'));
        if (!empty($key)) {
            $w['m.c_key'] = $key;
        }       

        $c_source = trim(I('c_source'));
        if (!empty($c_source)) {
            $w['m.c_source'] = $c_source;
        }

       
        $c_status = trim(I('c_status'));
        if ($c_status == 1) {
            $w['m.c_status'] = 1;
        } else if ($c_status == 'dj') {
            $w['m.c_status'] = 0;
        }else if ($c_status == '2') {
            $w['m.c_status'] = 2;
        }
        
        $c_sign = trim(I('c_sign'));
        if (!empty($c_sign)) {
            $w['m.c_sign'] = $c_sign;
        }
        // $c_sign = trim(I('c_sign'));
        // if (!empty($c_sign)) {
        //     if($c_state == 'dj'){
        //        $c_state = 0;
        //     }
        //     $w['m.c_sign'] = $c_sign;
        // }

        $c_type = trim(I('c_type'));
        if (!empty($c_type)) {
            $w['m.c_type'] = $c_type;
        }

        $c_settled = trim(I('c_settled'));
        if ($c_settled == 1) {
            $w['m.c_settled'] = 1;
        } else if ($c_settled == '2') {
            $w['m.c_settled'] = 2;
        } 
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'm.c_addtime desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'm.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=m.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
             $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
             $data_list[$k]['c_phone'] = $userinfo['c_phone'];

            // $uws['c_ucode'] = $v['c_scode'];
            // $userinfos = M('Users')->where($uws)->field('c_nickname')->find();
            //  $data_list[$k]['c_nicknames'] = $userinfos['c_nickname'];

            // $uwb['c_ucode'] = $v['c_bcode'];
            // $userinfob = M('Users')->where($uwb)->field('c_nickname')->find();
            // $data_list[$k]['c_nicknameb'] = $userinfob['c_nickname'];
           
         
            // $data_list[$k]['c_status'] = $this->mtype[$data_list[$k]['c_status']];
            // switch ($data_list[$k]['c_status']) {
            //     case 0:
            //         $data_list[$k]['c_status'] = "<font color='#FF0000'>未完成</font>";
            //         break;
            //     case 1:
            //         $data_list[$k]['c_status'] = "<font color='#00FF00'>已完成</font>";
            //         break;
            //     default:
            //         $data_list[$k]['c_status'] = "<font color='#808080'>已取消</font>";
            //         break;
            // }
        }
        
         
        //给单个用户统计
        if ($this->ucode) {
            $countarr = $this->Get_count($w);

            if($countarr[0]['d'] != 0){
                $this->warning = '警告：总收入减总支出不等于总余额。相差：'.$countarr[0]['d'].'(元)！';
            }
            $this->m_count = $countarr[0];
        }

        $this->gmtype = $this->mtype;
        $this->list = $data_list;
        $this->count = $date['count'];//分页
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();    

    }

    //Excel导出提款申请数据
    public function split(){
        $Split = D('Split','Behind');
        $Split->split_info();
    }


    //分润人添加   
    public function share_list_add(){
        $this->action = 'Member/share_list_add';
        if (IS_AJAX) 
        {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['ucode'])) {
                $this->ajaxReturn(Message(1003,'请选择分润人'));
            }
            
            if (empty($jsondata['c_source'])) {
                $this->ajaxReturn(Message(1000,'订单来源不能为空'));
            }
            if (empty($jsondata['c_desc'])) {
                $this->ajaxReturn(Message(1000,'来源描述不能为空'));
            }
            if (empty($jsondata['c_key'])) {
                $this->ajaxReturn(Message(1000,'来源关键字不能为空'));
            }
            if (empty($jsondata['c_money'])) {
                $this->ajaxReturn(Message(1000,'分到的金额不能为空'));
            }
            if (empty($jsondata['c_type'])) {
                $this->ajaxReturn(Message(1000,'结算方式不能为空'));
            }
            if (empty($jsondata['c_settled'])) {
                $this->ajaxReturn(Message(1000,'是否结算不能为空'));
            }
            if (empty($jsondata['c_sign'])) {
                $this->ajaxReturn(Message(1000,'状态不能为空'));
            }
            if (empty($jsondata['c_status'])) {
                $this->ajaxReturn(Message(1000,'不能为空'));
            }
            
            $data['c_orderid'] = CreateOrder('f');
            $data['c_ucode'] = $jsondata['ucode'];
            $data['c_source'] = $jsondata['c_source'];
            $data['c_desc'] = $jsondata['c_desc'];
            $data['c_key'] = $jsondata['c_key'];
            $data['c_money'] = $jsondata['c_money'];
            $data['c_type'] = $jsondata['c_type'];
            $data['c_settled'] = $jsondata['c_settled'];
            $data['c_sign'] = $jsondata['c_sign'];
            $data['c_status'] = $jsondata['c_status'];
            if ($jsondata['c_settled'] == 1) {
                $data['c_settledtime'] = gdtime();
            }
            
            $data['c_addtime'] = gdtime();
            $result = M('Users_order_splitting')->add($data);
            if($result){
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1002,'添加失败'));
            }  
        }
        $this->display();
    }
    
    //银盛资金管理
    public function yesmoney(){
        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }
        
        
        //注册手机号
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

        $db = M('Users_yesmoney as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
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
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();

    }

    //同步银盛金额
    public function Synchronization()
    {
        $parr['ucode'] = I('ucode');
        $result = IGD('Balance','User')->SyncYesMoney($parr);
        $this->ajaxReturn($result);
    }

    //盈盛账单
    public function ys_bill(){
        $this->display();

    }

    //代付账单下载
    public function get_dfbill(){
        $times = I('times');
        $parr['times'] = $times;
        if (empty($times)) {
             $this->ajaxReturn(Message(1002,'请选择下载的时间'));
        }
        vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
        $data = $pay->df_bill($parr);
        $result = $pay->curl_https_df_search($data);
        if ($result['ysepay_df_bill_downloadurl_get_response']['code'] != 10000) {
            $this->ajaxReturn(Message(1002,'下载失败'));
        };
        $this->ajaxReturn(MessageInfo(0,'操作成功',$result));
    }

    //代收账单下载
    public function get_dsbill(){
        $times = I('times');
        $parr['times'] = $times;
        if (empty($times)) {
             $this->ajaxReturn(Message(1002,'请选择下载的时间'));
        }
        vendor('Ysepay.Yse_pay');
        $pay = new \Yse_pay();
        $data = $pay->ds_bill($parr);
        $result = $pay->curl_https_ds_search($data);
        if ($result['ysepay_df_bill_downloadurl_get_response']['code'] != 10000) {
            $this->ajaxReturn(Message(1002,'下载失败'));
        };
        $this->ajaxReturn(MessageInfo(0,'操作成功',$result));
    }

}