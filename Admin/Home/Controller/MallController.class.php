<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 代理商城
 */
class MallController extends BaseController {
	//供货商列表
	public function supplier_list(){
		$hide = I('hide');
        if (!empty($hide)) {
            $this->hide = 'ctrl_hidden';
        }

		$db = M('supplier');
		//条件
		$ucode = trim(I('ucode'));
        if (!empty($ucode)) {
           $w['c_ucode'] = $ucode;
        }
    	$name = trim(I('name'));
        if (!empty($name)) {
           $w['c_name'] = array('like', "%{$name}%");
        }
        $username = trim(I('username'));
        if (!empty($username)) {
           $w['c_username'] = array('like', "%{$username}%");
        }
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
           $w['c_phone'] = $c_phone;
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//修改供货商密码
	public function change_password(){
		$ucode = I('ucode');
    	$where['c_ucode'] = $ucode;
    	$this->data = M('supplier')->where($where)->field('c_ucode,c_username')->find();
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
	        $result = M('supplier')->where($where)->save($save_data);
	        if($result || $result == 0) {
                $this->ajaxReturn(Message(0,'修改成功'));
            }else{
                $this->ajaxReturn(Message(1002,'修改失败'));
            }
	    }
    	$this->display();
	}

	//添加供货商
	public function supplier_add(){
		$this->action = "ajax_supplier_add";
    	$this->display();
  	}

  	//验证提交数据
	function verify_data($data,$flag){
		if(empty($data['c_username'])){
	      $this->error("登录帐号必须填写");
	    }
	    if($flag == 0){
	    	if(empty($data['pwd'])){
	      		$this->error("登录密码必须填写");
	    	}
	    }
	    if(empty($data['c_name'])){
	      $this->error("供货商名称必须填写");
	    }
	    if(empty($data['c_person_name'])){
	      $this->error("负责人名称必须填写");
	    }
	    if(empty($data['c_phone'])){
	      $this->error("负责人手机号必须填写");
	    }else{
	      $result = checkMobile($data['c_phone']);
	      if(!$result){
	        $this->error("负责人手机号格式不正确");
	      }
	    }
	}

	/**
     *  生成供货商编码
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

  	//供货商添加提交数据
	public function ajax_supplier_add(){
	    $this->verify_data($_POST,0); //验证数据

	    $pwd = $_POST['pwd'];

	    $_POST['c_ucode'] = $this->CreateUcode("ghs");
	    $_POST['c_password'] = encrypt($pwd,C('ENCRYPT_KEY'));
	    $_POST['c_checked'] = 1;
	    $_POST['c_apply_time'] = date('Y-m-d H:i:s', time());
	    $_POST['c_addtime'] = date('Y-m-d H:i:s', time());

	    $result = M('supplier')->add($_POST);

	    if($result){
	      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mall/supplier_list';
	      echo '<script language="javascript">alert("添加成功");</script>';
	      echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	    }else{
	      $this->error("保存失败");
	    }
	}

	//供货商编辑
	public function supplier_edit(){
		$ucode = I('ucode');
		$where['c_ucode'] = $ucode;
		$supplierinfo = M('supplier')->where($where)->find();
		$this->action = "ajax_supplier_edit";
		$this->vo = $supplierinfo;
		$this->display('supplier_add');
	}

	//供货商编辑提交数据
	public function ajax_supplier_edit(){
	    $this->verify_data($_POST,1); //验证数据

	    $ucode = $_POST['ucode'];
	    $w['c_ucode'] = $ucode;

	    $result = M('supplier')->where($w)->save($_POST);

	    if($result){
	      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mall/supplier_list';
	      echo '<script language="javascript">alert("编辑成功");</script>';
	      echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	    }else{
	      $this->error("保存失败");
	    }
	}

	//供货商修改余额
	public function change_money(){
        $ucode = I('ucode');
        $w['c_ucode'] = $ucode;
        $this->data = M('supplier')->field('c_ucode,c_name,c_money')->where($w)->find();
		$this->display();
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

        $result = D('Mall','Behind')->OptionMoney($parr);

        if($result['code'] == 0){
            $where['c_ucode'] = I('ucode');
            $user_money = M('supplier')->where($where)->field('c_money')->find();
            $this->ajaxReturn(MessageInfo(0,"修改成功",$user_money));
        }

        $this->ajaxReturn($result);
    }

	//供货商商品列表
	public function product_list(){
		$db = M('supplier_product as p');
		//条件
		// $c_ucode = trim(I('ucode'));
		// if (!empty($c_ucode)) {
		//     $w['s.c_ucode'] = $c_ucode;
		//     $this->ucode = $c_ucode;
		// }
		$c_name = trim(I('c_name'));
		if (!empty($c_name)) {
		    $w['p.c_name'] = array('like', "%{$c_name}%");
		}
		$nickname = trim(I('nickname'));
		if (!empty($nickname)) {
		    $w['s.c_name'] = array('like', "%{$nickname}%");
		}
		$categoryid = trim(I('categoryid'));
		if (!empty($categoryid)) {
		    $w['p.c_categoryid'] = $categoryid;
		}
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['s.c_phone'] = $c_phone;
        }
		$ishow = trim(I('ishow'));
		if (!empty($ishow)) {
		    $w['p.c_ishow'] = $ishow;
		}
		// $pcode = trim(I('pcode'));
		// if (!empty($pcode)) {
		//     $w['p.c_pcode'] = $pcode;
		// }

		$w['c_isdele'] = 1;//显示不删除的产品
		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

		//分页显示数据
		$panrn['field'] = 'p.*,s.c_name as s_name,c.c_category_name,s.c_phone';
		$panrn['join'] = 'left join t_supplier as s on s.c_ucode=p.c_ucode';
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

	// 产品上下架
    public function product_state()
    {
    	$Id = I('gid');
        $ishow = I('active');
        if(empty($Id)) die("非法操作，ID为空！");

        $where['c_id'] = $Id;
        $pcode = M('supplier_product')->where($where)->getField('c_pcode');

        if($ishow == 1){
            $whereinfo['c_pcode'] = $pcode;
            $model_list = M('supplier_product_model')->where($whereinfo)->select();
            if(count($model_list) == 0) die("产品型号为空，不能上架！");
            foreach ($model_list as $k => $v) {
                $w['c_pcode'] = $v['c_pcode'];
                $w['c_mcode'] = $v['c_mcode'];
                $ladderprice = M('supplier_product_ladderprice')->where($w)->count();
                if($ladderprice < 3) die("产品型号价必须存在三条，否则不能上架！");
            }
        }

        $db = M('');
        $db->startTrans();

        $data['c_ishow'] = $ishow;
        $result = M('supplier_product')->where($where)->save($data);
        if(!$result){
            $db->rollback();
            die("操作失败！");
        }

        $result = D('Mall','Behind')->Sync_state($pcode,$ishow);

        if($result['code'] != 0){
            $db->rollback();
            die("同步代理商品状态操作失败！");
        }

        $db->commit();
    }

    //产品添加
    public function product_add(){
    	/*产品分类*/
        $protype = IGD('Common','Info');
        $proresult = $protype->GetCategory($parr);
        $this->category = $proresult['data'];
        if (IS_POST) {
            foreach ($_POST['mcode'] as $key => $value) {
                $modellist[$key]['mcode'] = $value;
                $modellist[$key]['mname'] = $_POST['mname'][$key];
                $modellist[$key]['num'] = $_POST['mnum'][$key];
                $k = $key + 1;
                $modellist[$key]['price1'] = $_POST['mprice'][$k*3-3];
                $modellist[$key]['price2'] = $_POST['mprice'][$k*3-2];
                $modellist[$key]['price3'] = $_POST['mprice'][$k*3-1];
                $modellist[$key]['minprice1'] = $_POST['minprice'][$k*3-3];
                $modellist[$key]['minprice2'] = $_POST['minprice'][$k*3-2];
                $modellist[$key]['minprice3'] = $_POST['minprice'][$k*3-1];
                $modellist[$key]['maxnum1'] = $_POST['maxnum'][$k*3-3];
                $modellist[$key]['maxnum2'] = $_POST['maxnum'][$k*3-2];
            }

            $imgresult = uploadimg('supplier');
            if ($imgresult['code'] != 0) {
                $this->error($imgresult['msg']);
            }
            $param['imglist'] = array_values($imgresult['data']);

            if (count($modellist) == 0) {
                $this->error('至少添加一个型号');
            }

            $param['ucode'] = $_POST['ucode'];
            $param['modellist'] = $modellist;
            $param['desc'] = $_POST['desc'];
            $param['name'] = $_POST['name'];
            $param['pminprice'] = $_POST['pminprice'];
            $isagent = $_POST['isagent'];
            $param['isagent'] = $isagent;
            if($isagent == 1){
            	$param['piece'] = $_POST['piece'];
            }
            $param['ishow'] = $_POST['ishow'];
            $param['freeprice'] = $_POST['freeprice'];
            $param['categoryid'] = $_POST['categoryid'];
            $param['salesnum'] = $_POST['salesnum'];

            $proinfo = D('Mall','Behind');
            $result = $proinfo->AddProudct($param);
            if($result['code'] == 0){
	            $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mall/product_list';
	            echo '<script language="javascript">alert("保存成功");</script>';
	            echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	            $this->error($result['msg']);
	        }
        }
    	$this->display();
    }

    // 商品编辑
    public function product_edit()
    {
        /*产品分类*/
        $protype = IGD('Common','Info');
        $proresult = $protype->GetCategory($parr);
        $this->category=$proresult['data'];

        $parr['ucode'] = I('ucode');
        $parr['pcode'] = I('pcode');
        $proinfo = D('Mall','Behind');
        $result = $proinfo->GetProductInfo($parr);
        $this->vo = $result['data'];

        if (IS_POST) {
            foreach ($_POST['mcode'] as $key => $value) {
                $modellist[$key]['mcode'] = $value;
                $modellist[$key]['mname'] = $_POST['mname'][$key];
                $modellist[$key]['num'] = $_POST['mnum'][$key];
                $k = $key + 1;
                $modellist[$key]['price1'] = $_POST['mprice'][$k*3-3];
                $modellist[$key]['price2'] = $_POST['mprice'][$k*3-2];
                $modellist[$key]['price3'] = $_POST['mprice'][$k*3-1];
                $modellist[$key]['minprice1'] = $_POST['minprice'][$k*3-3];
                $modellist[$key]['minprice2'] = $_POST['minprice'][$k*3-2];
                $modellist[$key]['minprice3'] = $_POST['minprice'][$k*3-1];
                $modellist[$key]['maxnum1'] = $_POST['maxnum'][$k*3-3];
                $modellist[$key]['maxnum2'] = $_POST['maxnum'][$k*3-2];
            }
            foreach (array_keys($_POST) as $k => $v) {
                if (strpos($v,'img')  !== false) {
                    if (!empty($_POST[$v])) {
                        $imglist[] = str_replace(GetHost().'/', '', $_POST[$v]);
                    }
                }
            }

            if (!empty($_FILES)) {
                 $imgresult = uploadimg('supplier');
                if ($imgresult['code'] != 0) {
                    $this->error($imgresult['msg']);
                }
                if (count($imglist) > 0) {
                    $param['imglist'] = array_merge($imglist,array_values($imgresult['data']));
                } else {
                    $param['imglist'] = array_values($imgresult['data']);
                }
            } else {
                $param['imglist'] = $imglist;
            }

            if (count($modellist) == 0) {
                $this->error('至少添加一个型号');
            }
            $param['pcode'] = $_POST['pcode'];
            $param['ucode'] = $_POST['ucode'];
            $param['modellist'] = $modellist;
            $param['desc'] = $_POST['desc'];
            $param['name'] = $_POST['name'];
            $param['pminprice'] = $_POST['pminprice'];
            $isagent = $_POST['isagent'];
            $param['isagent'] = $isagent;
            if($isagent == 1){
            	$param['piece'] = $_POST['piece'];
            }
            $param['ishow'] = $_POST['ishow'];
            $param['freeprice'] = $_POST['freeprice'];
            $param['categoryid'] = $_POST['categoryid'];
            $param['salesnum'] = $_POST['salesnum'];

            $proinfo = D('Mall','Behind');
            $result = $proinfo->UpdateProductInfo($param);
           	if($result['code'] == 0){
	            $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Mall/product_list';
	            echo '<script language="javascript">alert("编辑成功");</script>';
	            echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	            $this->error($result['msg']);
	        }
        }
    	$this->display('product_add');
    }

    // 产品删除
    public function product_del()
    {
    	$Id = I('Id');
		$where['c_id'] = $Id;

        $pcode = M('supplier_product')->where($where)->getField('c_pcode');

        $db = M('');
        $db->startTrans();

        $data['c_isdele'] = 2;
		$result = M('supplier_product')->where($where)->save($data);
        if(!$result){
            $db->rollback();
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        //代理商品商品删除状态同步
        $result = D('Mall','Behind')->Sync_del($pcode);
        if($result['code'] != 0){
            $db->rollback();
            $this->ajaxReturn($result);
        }

        $db->commit();
	    $this->ajaxReturn(Message(0,'删除成功'));
    }

     //产品图片列表
    public function product_imgs(){
        $pcode = I('pcode');
        $w['c_pcode'] = $pcode;
        $parr['where'] = $w;
        $this->data = D('Db','Behind')->mate_select(M('supplier_product_img'),$parr);
        $this->count = M('supplier_product_img')->where($w)->count();
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
        $result = M('supplier_product_img')->where($where)->save($data);
        if(!$result){
            die("操作失败！");
        }
    }

    //用户申请提款记录
    public function applyFor(){
        $db = M('users_drawing as d');
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['s.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['s.c_name'] = array('like', "%{$c_name}%");
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

        $w['c_issupplier'] = 1;
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'd.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'd.*,s.c_name,s.c_ucode';
        $panrn['join'] = 'left join t_supplier as s on s.c_ucode=d.c_ucode';
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
        $this->post = $parent;
        $this->display();
    }

    //Excel导出提款申请数据
    public function educeIndex(){
        $Order = D('Mall','Behind');
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
            // $content = '您提现余额￥'.$money.'申请，系统已同意，系统将进行转账处理';
        }else{//不同意提款，退还余额
            $parr['ucode'] = $ucode;
            $parr['money'] = $money;
            $parr['source'] = 3;
            $parr['key'] = "";
            $parr['desc'] = "余额提现";
            $parr['state'] = 1;
            $parr['type'] = 1;

            $result = D('Mall','Behind')->OptionMoney($parr);

            if($result['code'] !== 0){
                $this->ajaxReturn($result);
            }

            $save_data['c_state'] = 2;
            $r = $db->where('c_id='.$id)->save($save_data);
            // $content = '您提现余额￥'.$money.'申请，系统不同意，如有疑问请跟我们联系';
        }

        //给用户发送相关消息
        // $Msgcentre = IGD('Msgcentre', 'Message');
        // $msgdata['ucode'] = $ucode;
        // $msgdata['type'] = 0;
        // $msgdata['platform'] = 1;
        // $msgdata['sendnum'] = 1;
        // $msgdata['title'] = '系统消息';
        // $msgdata['content'] =  $content;
        // $msgdata['tag'] = 2;
        // $msgdata['tagvalue'] = GetHost(1).'/index.php/Home/Balance/drawinglog';
        // $msgdata['weburl'] = GetHost(1).'/index.php/Home/Balance/drawinglog';
        // $Msgcentre->CreateMessege($msgdata);

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

    //供货商账目明细
    public function detail_account(){
        $db = M('supplier_moneylog as m');
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['s.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['s.c_name'] = array('like', "%{$c_name}%");
        }

        $c_state = trim(I('c_state'));
        if (!empty($c_state)) {
            if($c_state == 'dj'){
               $w['m.c_state'] = 0;
            }
            $w['m.c_state'] = $c_state;
        }

        $c_source = trim(I('c_source'));
        if (!empty($c_source)) {
            $w['m.c_source'] = $c_source;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'm.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'm.*,s.c_name';
        $panrn['join'] = 'left join t_supplier as s on s.c_ucode=m.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            switch ($data_list[$k]['c_source']) {
                case 1:
                    $data_list[$k]['c_source'] = "源于订单";
                    break;
                case 2:
                    $data_list[$k]['c_source'] = "后台操作";
                    break;
                default:
                    $data_list[$k]['c_source'] = "余额提现";
                    break;
            }
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
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->display();
    }

}