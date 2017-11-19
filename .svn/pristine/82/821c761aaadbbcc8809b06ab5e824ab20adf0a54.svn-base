<?php
namespace Home\Controller;
use Think\Controller;
//其他活动
class EventController extends BaseController{
	//限时抢购产品列表
	public function Buylimit(){
        $db = M('Product_promote as p');
		//条件
		$pname = trim(I('pname'));
        if (!empty($pname)) {
           $w['pro.c_name'] = array('like', "%{$pname}%");
        }

    	$nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

        $state = trim(I('state'));
         if(!empty($state)){
        	if ($state == 10) {
           		$w['p.c_state'] = 0;
        	}else if($state == 1){
        		$w['p.c_state'] = 1;
        	}else if($state == 2){
        		$w['p.c_state'] = 2;
        	}
        }
        $w['p.c_rule'] = 13;
        $w['p.c_delete'] = 0;
		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname,u.c_nickname,pro.c_pimg,pro.c_name';
        $panrn['join'] = 'left join t_activity as a on a.c_activitytype=p.c_rule';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=p.c_ucode';
        $panrn['join2'] = 'left join t_product as pro on pro.c_pcode=p.c_pcode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//ajax 修改产品审核状态
	public function product_state(){
		$Id = I('gid');
	    $state = I('active');
	    if(empty($Id)) die("非法操作，ID为空！");

	    $where['c_id'] = $Id;

	    $data['c_state'] = $state;
	    $data['c_checktime'] = Date('Y-m-d H:i:s');
	    $result = M('Product_promote')->where($where)->save($data);
	    if(!$result){
	        die("操作失败！");
	    }
	}

	//验证表单
	public function checkfrom($data){
		if (empty($data['pcode'])) {
			$this->error("请选择商品");
		}

		$startime = $data['startime'];
		if (empty($startime)) {
			$this->error("开始时间不能为空");
		}
		$endtime = $data['endtime'];
		if (empty($data['endtime'])) {
			$this->error("结束时间不能为空");
		}

		if(strtotime($startime) > strtotime($endtime)){
			$this->error("开始时间不能活动结束时间");
		}

		if($data['rule'] == 14){
			if (empty($data['price'])) {
				$this->error("夺宝价格不能为空");
			}

			if (empty($data['joinnum'])) {
				$this->error("开奖人次不能为空");
			}
		}
		if($data['rule'] == 13){
			if (empty($data['discount'])) {
				$this->error("折扣比例未选择");
			}
		}
		if (empty($data['totalnum']) && $data['totalnum'] != 0) {
			$this->error("限购库存不能为空");
		}
		if (empty($data['num']) && $data['num'] != 0) {
			$this->error("剩余库存不能为空");
		}
		if (empty($data['penum'])) {
			$this->error("限购人次不能为空");
		}
	}

	//限时抢购产品添加
	public function Buylimit_add(){
		$this->action = 'Buylimit_add';
		if(IS_POST){
			$db = M('Product_promote');
			$_POST['rule'] = 13;
		    $this->checkfrom($_POST);

		    //查询产品是否存在
	        $where['c_ishow'] = 1;
	        $where['c_isdele'] = 1;
	        $where['c_pcode'] = $_POST['pcode'];
	        $productinfo = M('Product')->where($where)->find();
	        if (!$productinfo) {
	            $this->error('选择的产品已删除或已下架');
	        }

	        //查询商品是否已参加限时抢购活动
	        $promowhere['c_pcode'] = $_POST['pcode'];
        	$promowhere['c_delete'] = 0;
        	$promoteinfo = M('Product_promote')->where($promowhere)->find();
        	if ($promoteinfo) {
	            $this->error('选择的商品正在参加活动');
	        }

		    $data['c_rule'] = $_POST['rule'];
		    $data['c_ucode'] = $productinfo['c_ucode'];
		    $data['c_pcode'] = $_POST['pcode'];
		    $data['c_price'] = bcmul($productinfo['c_price'], bcdiv($_POST['discount'], 10, 2), 2);

		    $data['c_discount'] = $_POST['discount'];
		    $data['c_totalnum'] = ($_POST['totalnum'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['totalnum'];
		    $data['c_num'] = ($_POST['num'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['num'];
		    $data['c_penum'] = $_POST['penum'];

		    $data['c_startime'] = $_POST['startime'];
		    $data['c_endtime'] = $_POST['endtime'];

		    $data['c_createtime'] = Date('Y-m-d H:i:s');
		    $data['c_edittime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Event/Buylimit';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//限时抢购产品编辑
	public function Buylimit_edit(){
		$Id = I('Id');
		$this->action = 'Buylimit_edit';
		$where['c_id'] = $Id;
		$promoteinfo = M('Product_promote')->where($where)->find();
		$pwhere['c_pcode'] = $promoteinfo['c_pcode'];
		$promoteinfo['produce'] = M('Product')->field('c_pcode,c_name')->where($pwhere)->find();
		$this->vo = $promoteinfo;

		if(IS_POST){
			$_POST['rule'] = 13;
		    $this->checkfrom($_POST);

		    //查询产品是否存在
	        $where1['c_ishow'] = 1;
	        $where1['c_isdele'] = 1;
	        $where1['c_pcode'] = $_POST['pcode'];
	        $productinfo = M('Product')->where($where1)->find();
	        if (!$productinfo) {
	            $this->error('选择的产品已删除或已下架');
	        }

		    $data['c_rule'] = $_POST['rule'];
		    $data['c_ucode'] = $productinfo['c_ucode'];
		    $data['c_pcode'] = $_POST['pcode'];
		    $data['c_price'] = bcmul($productinfo['c_price'], bcdiv($_POST['discount'], 10, 2), 2);

		    $data['c_discount'] = $_POST['discount'];
		    $data['c_totalnum'] = ($_POST['totalnum'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['totalnum'];
		    $data['c_num'] = ($_POST['num'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['num'];
		    $data['c_penum'] = $_POST['penum'];

		    $data['c_startime'] = $_POST['startime'];
		    $data['c_endtime'] = $_POST['endtime'];

		    $data['c_createtime'] = Date('Y-m-d H:i:s');
		    $data['c_edittime'] = Date('Y-m-d H:i:s');

		    $where['c_id'] = $_POST['Id'];
		    $result = M('Product_promote')->where($where)->save($data);

	    	if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Event/Buylimit';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('Buylimit_add');
	}

	//产品删除
    public function Buylimit_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $save['c_delete'] = 1;
        $result = M('Product_promote')->where($where)->save($save);
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //群龙夺宝产品列表
	public function snatch(){
        $db = M('Product_promote as p');
		//条件
		$pname = trim(I('pname'));
        if (!empty($pname)) {
           $w['pro.c_name'] = array('like', "%{$pname}%");
        }

    	$nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

        $state = trim(I('state'));
        if(!empty($state)){
        	if ($state == 10) {
           		$w['p.c_state'] = 0;
        	}else if($state == 1){
        		$w['p.c_state'] = 1;
        	}else if($state == 2){
        		$w['p.c_state'] = 2;
        	}
        }
        $w['p.c_rule'] = 14;
        $w['p.c_delete'] = 0;
		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'p.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,a.c_activityname,u.c_nickname,pro.c_pimg,pro.c_name';
        $panrn['join'] = 'left join t_activity as a on a.c_activitytype=p.c_rule';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=p.c_ucode';
        $panrn['join2'] = 'left join t_product as pro on pro.c_pcode=p.c_pcode';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
	}

	//群龙夺宝产品添加
	public function snatch_add(){
		$this->action = 'snatch_add';
		if(IS_POST){
			$db = M('Product_promote');
			$_POST['rule'] = 14;
		    $this->checkfrom($_POST);

		    //查询产品是否存在
	        $where['c_ishow'] = 1;
	        $where['c_isdele'] = 1;
	        $where['c_pcode'] = $_POST['pcode'];
	        $productinfo = M('Product')->where($where)->find();
	        if (!$productinfo) {
	            $this->error('选择的产品已删除或已下架');
	        }

	        //查询商品是否已参加限时抢购活动
	        $promowhere['c_pcode'] = $_POST['pcode'];
        	$promowhere['c_delete'] = 0;
        	$promoteinfo = M('Product_promote')->where($promowhere)->find();
        	if ($promoteinfo) {
	            $this->error('选择的商品正在参加活动');
	        }

		    $data['c_rule'] = $_POST['rule'];
		    $data['c_ucode'] = $productinfo['c_ucode'];
		    $data['c_pcode'] = $_POST['pcode'];
		    $data['c_price'] = $_POST['price'];

		    $data['c_joinnum'] = $_POST['joinnum'];
		    $data['c_totalnum'] = ($_POST['totalnum'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['totalnum'];
		    $data['c_num'] = ($_POST['num'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['num'];
		    $data['c_penum'] = $_POST['penum'];

		    $data['c_startime'] = $_POST['startime'];
		    $data['c_endtime'] = $_POST['endtime'];

		    $data['c_createtime'] = Date('Y-m-d H:i:s');
		    $data['c_edittime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);
		    if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Event/snatch';
	          echo '<script language="javascript">alert("添加成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('添加失败！');
	        }
		}
		$this->display();
	}

	//群龙夺宝产品编辑
	public function snatch_edit(){
		$Id = I('Id');
		$this->action = 'snatch_edit';
		$where['c_id'] = $Id;
		$promoteinfo = M('Product_promote')->where($where)->find();
		$pwhere['c_pcode'] = $promoteinfo['c_pcode'];
		$promoteinfo['produce'] = M('Product')->field('c_pcode,c_name')->where($pwhere)->find();
		$this->vo = $promoteinfo;

		if(IS_POST){
			$_POST['rule'] = 14;
		    $this->checkfrom($_POST);

		    //查询产品是否存在
	        $where1['c_ishow'] = 1;
	        $where1['c_isdele'] = 1;
	        $where1['c_pcode'] = $_POST['pcode'];
	        $productinfo = M('Product')->where($where1)->find();
	        if (!$productinfo) {
	            $this->error('选择的产品已删除或已下架');
	        }

		    $data['c_rule'] = $_POST['rule'];
		    $data['c_ucode'] = $productinfo['c_ucode'];
		    $data['c_pcode'] = $_POST['pcode'];
		    $data['c_price'] = $_POST['price'];

		    $data['c_joinnum'] = $_POST['joinnum'];
		    $data['c_totalnum'] = ($_POST['totalnum'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['totalnum'];
		    $data['c_num'] = ($_POST['num'] > $productinfo['c_num'])?$productinfo['c_num']:$_POST['num'];
		    $data['c_penum'] = $_POST['penum'];

		    $data['c_startime'] = $_POST['startime'];
		    $data['c_endtime'] = $_POST['endtime'];

		    $data['c_createtime'] = Date('Y-m-d H:i:s');
		    $data['c_edittime'] = Date('Y-m-d H:i:s');


		    $where['c_id'] = $_POST['Id'];
		    $result = M('Product_promote')->where($where)->save($data);

	    	if($result){
		      $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Event/snatch';
	          echo '<script language="javascript">alert("编辑成功");</script>';
	          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
	        }else{
	          $this->error('编辑失败！');
	        }
		}
		$this->display('snatch_add');
	}

	//商品审核
	public function Buylimit_check(){
		$Id = I('Id');
		$where['c_id'] = $Id;
		$this->vo = M('Product_promote')->where($where)->find();
		$this->display();
	}

	public function pcheck(){
		$Id = I('Id');
		$state = I('state');
		$reason = I('reason');

		if($state == ''){
			$this->error("请选择审核！");
		}

		if($state == 2 && empty($reason)){
			$this->error("请填写审核不通过的原因！");
		}

		$where['c_id'] = $Id;
		$rule = M('Product_promote')->where($where)->getField('c_rule');
		if($rule == 13){
			$back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Event/Buylimit';
		}else if($rule == 14){
			$back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Event/snatch';
		}

		$parr['id'] = $Id;
		$parr['state'] = $state;
		$parr['reason'] = $reason;
		$parr['atype'] = $atype;
		$result = D('Activity','Behind')->pcheck($parr);

		if($result['code'] == 0){
          echo '<script language="javascript">alert('.$result['msg'].');</script>';
          echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }else{
          $this->error($result['msg']);
        }
	}
}