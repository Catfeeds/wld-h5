<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  订单管理
 */
class OrderController extends BaseController {
	//订单列表
    public function index(){
        $db = M('order as o');
        //条件
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
            $w['u.c_ucode'] = $ucode;
            $this->ucode = $ucode;
        }
        $pcode = trim(I('pcode'));
        if (!empty($pcode)) {
            $w['o.c_acode'] = $pcode;
            $this->pcode = $pcode;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
        //用户手机号
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }
        $pnickname = trim(I('pnickname'));
        if (!empty($pnickname)) {
            $w['au.c_nickname'] = array('like', "%{$pnickname}%");
        }
        //商家手机号 
        $c_phones = trim(I('c_phones'));
        if (!empty($c_phones)) {
            $w['au.c_phone'] = $c_phones;
        }
        $consignee = trim(I('consignee'));
        if (!empty($consignee)) {
            $w['adr.c_consignee'] = $consignee;
        }
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
            $w['o.c_orderid'] = $orderid;
        }
        $aid = trim(I('aid'));
        if (!empty($aid)) {
            $w['o.c_activity_id'] = $aid;
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "o.c_addtime between '".$begintime."' and '".$endtime."'";
        }

        $begintime1 = I('EntTime3');
        $endtime1 = I('EntTime4');
        if(isset($begintime1)&&!empty($begintime1) && isset($endtime1)&&!empty($endtime1)){
            $w[] = "o.c_paytime between '".$begintime1."' and '".$endtime1."'";
        }
        $status = I('status');
        if(isset($status)&&!empty($status)){
            $w[] = D('Order','Behind')->select_statue($status);
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'o.c_id desc';//排序
        $panrn['limit'] = 10;//分页数

        //分页显示数据
        $panrn['field'] = 'o.*,u.c_nickname,u.c_phone,au.c_nickname as agent_name,au.c_phone as c_phones,adr.*';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=o.c_ucode';
        $panrn['join1'] = 'left join t_users as au on au.c_ucode=o.c_acode';
        $panrn['join2'] = 'left join t_order_address as adr on adr.c_orderid=o.c_orderid';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $rt = $date['list'];
        $orderlist = array();
        if(!empty($rt)){
            foreach($rt as $row){
				if(empty($row['c_id'])||empty($row['c_orderid'])) continue;
                $orderlist[] = array(
                	'c_id'=>$row['c_id'],
                	'c_ucode'=>$row['c_ucode'],
                	'c_acode'=>$row['c_acode'],
                	'c_nickname'=>(!empty($row['c_nickname'])? $row['c_nickname'] : '无知'),
                	'agent_name'=>(!empty($row['agent_name'])? $row['agent_name'] : '无知'),
                    'c_orderid'=>$row['c_orderid'],
                    'c_delivery'=>($row['c_delivery'] == 1) ? "快递" : '面对面',//快递方式
                    //收货信息
                    'c_consignee'=>$row['c_consignee'],
                    'c_telphone'=>$row['c_telphone'],
                    'address'=>$row['c_province']."省&nbsp;".$row['c_cityname']."市&nbsp;".$row['c_district']."&nbsp;".$row['c_address'],
                    //订单详情
                    'order_details'=>$this->get_details($row['c_orderid']),
					'c_total_price'=>$row['c_total_price'],
					'c_actual_price'=>$row['c_actual_price'],
					'c_pay_state'=>$row['c_pay_state'],
					'c_pay_rule'=>$row['c_pay_rule'],
					'c_paytime'=>$row['c_paytime'],
					'c_free'=>$row['c_free'],
					'c_deliverytime'=>$row['c_deliverytime'],
					'c_confirmtime'=>$row['c_confirmtime'],
					'c_expressname'=>$row['c_expressname'],
					'c_expressnum'=>$row['c_expressnum'],
					'c_isagent'=>$row['c_isagent'],
                    'c_activity_name'=>$row['c_activity_name'],
                    'c_addtime'=>(!empty($row['c_addtime'])? $row['c_addtime'] : '无知'),
                    'c_source'=>$row['c_source'],
                    'mystatus'=>$this->get_status($row['c_order_state'],$row['c_pay_state'],$row['c_deliverystate'])
                );
            }
        }
        $this->list = $orderlist;
        $this->page = $date['Page'];
        $this->count = $date['count'];//分页\
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->category = M('category')->select();
        $this->show();
    }

    //Excel导出订单数据
    public function educeIndex(){
        $Order = D('Order','Behind');
        $Order -> sheetIndexnt();
    }

    //订单详情
    function get_details($orderid){
    	$db = M('order_details as od');
    	$w['od.c_orderid'] = $orderid;

    	$panrn['where'] = $w;
    	$panrn['field'] = 'od.c_pname,od.c_pprice,od.c_pmodel_name,od.c_pnum,od.c_ptotal,od.c_pimg,od.c_productstatus,od.c_detailid,u.c_nickname as tgname';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=od.c_pucode';
    	$list=D('Db','Behind');
        $date=$list->mate_select($db,$panrn);
        foreach ($date as $k => $v) {
                switch ($date[$k]['c_productstatus']) {
                    case '0':
                        $date[$k]['mystatus'] = '<font color="green">正常</font>';
                        break;
                    case '1':
                        // $date[$k]['mystatus'] = '<a title="维权详情" href="'.C('TMPL_PARSE_STRING.__HHOME__').'/Order/product_score?detailid={$v.c_detailid}"><font color="red">申请退款</font></a>';
                        $date[$k]['mystatus'] = '<font color="red">申请退款</font>';
                        break;
                    case '2':
                        $date[$k]['mystatus'] = '<font color="red">申请退款退货</font>';
                        break;
                    case '3':
                        $date[$k]['mystatus'] = '<font color="red">申请换货</font>';
                        break;
                    case '4':
                        $date[$k]['mystatus'] = '<font color="green">商家同意</font>';
                        break;
                    case '5':
                        $date[$k]['mystatus'] = '<font color="red">商家不同意</font>';
                        break;
                    default:
                        $date[$k]['mystatus'] = '<font color="green">维权已完成</font>';
                        break;
                }
            }
        return $date;
    }

    //订单的状态
    function get_status($oid=0,$pid=0,$sid=0){ //分别为：订单 支付 发货状态
        $str = '';
        switch($oid){
            case '0':
                $str .= '<font color="red">未确认</font>,';
                break;
            case '1':
                $str .= '<font color="red">取消</font>,';
                break;
            case '2':
                $str .= '确认,';
                break;
            case '3':
                $str .= '<font color="red">已退货</font>,';
                break;
            case '4':
                $str .= '<font color="red">无效</font>,';
                break;
            case '5':
                $str .= '<font color="red">申请退款</font>,';
                break;
            case '6':
                $str .= '<font color="red">申请退货</font>,';
                break;
           	case '7':
                $str .= '<font color="red">申请换货</font>,';
                break;
        }

       switch($pid){
            case '0':
                $str .= '未付款,';
                break;
            case '1':
                $str .= '<font color="#228B22">已付款</font>,';
                break;
            case '2':
                $str .= '<font color="red">已退款</font>,';
                break;
        }
       // 0未发货，1配送中，2已发货，4(退换货)成功，5已收货
        switch($sid){
            case '0':
                $str .= '未发货';
                break;
            case '1':
                $str .= '配货中';
                break;
            case '2':
                $str .= '已发货';
                break;
            case '3':
                $str .= '部分发货';
                break;
            case '4':
                $str .= '已退货换货';
                break;
            case '5':
                $str .= '已收货';
                break;
        }
        return $str;
    }

	//订单详情
	public function order_details(){
		$db = M('order as o');

		$orderid = I('Id');
		$w['o.c_orderid'] = $orderid;

        $panrn['where'] = $w;
        //分页显示数据
        $panrn['field'] = 'o.*,u.c_nickname,au.c_nickname as agent_name,adr.*';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=o.c_ucode';
        $panrn['join1'] = 'left join t_users as au on au.c_ucode=o.c_acode';
        $panrn['join2'] = 'left join t_order_address as adr on adr.c_orderid=o.c_orderid';
        $list=D('Db','Behind');
        $order_info=$list->mate_find($db,$panrn);
        if(!empty($order_info)){
        	//订单状态
        	$order_info['mystatus'] = $this->get_status($order_info['c_order_state'],$order_info['c_pay_state'],$order_info['c_deliverystate']);

            $w1['c_orderid'] = $order_info['c_orderid'];
        	//订单详情
            $db1 = M('order_details as od');
        	$panrn1['where'] = $w1;
	        $panrn1['field'] = 'od.*,u.c_nickname as tgname';
	        $panrn1['join'] = 'left join t_users as u on u.c_ucode=od.c_pucode';
            $order_details = $list->mate_select($db1,$panrn1);

            $homeurl = C('TMPL_PARSE_STRING')['__HHOME__'];
            foreach ($order_details as $k => $v) {
                switch ($order_details[$k]['c_productstatus']) {
                    case '0':
                        $order_details[$k]['c_productstatus'] = '<font color="green">正常</font>';
                        break;
                    case '1':
                        $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="'.$homeurl.'/Order/order_refund?detailid='.$v['c_detailid'].'"><font color="red">退款</font></a>';
                        break;
                    case '2':
                       $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="'.$homeurl.'/Order/order_refund?detailid='.$v['c_detailid'].'"><font color="red">退款退货</font></a>';
                        break;
                    case '3':
                        $order_details[$k]['c_productstatus'] = "<font color='red'>换货</font>";
                        break;
                    case '4':
                       $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="'.$homeurl.'/Order/order_refund?detailid='.$v['c_detailid'].'"><font color="red">商家同意</font></a>';
                        break;
                     case '5':
                        $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="'.$homeurl.'/Order/order_refund?detailid='.$v['c_detailid'].'"><font color="red">商家不同意</font></a>';
                        break;
                    default:
                        $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="'.$homeurl.'/Order/order_refund?detailid='.$v['c_detailid'].'"><font color="green">退款成功</font></a>';
                        break;
                }
            }
            $order_info['order_details'] = $order_details;
            //订单支付记录
            $panrn2['where'] = $w1;
            $panrn1['field'] = 'op.*,p.c_payname';
            $panrn1['join'] = 'left join t_pay_type as p on p.c_payrule=op.c_payrule';
            $order_paylog = $list->mate_select(M('order_paylog as op'),$panrn1);
            $order_info['order_paylog'] = $order_paylog;
            //返回订单操作按钮
		    $order_info['order_action_button'] = $this->get_order_action_button($order_info['c_order_state'],$order_info['c_pay_state'],$order_info['c_deliverystate']);
        }
        $this->rt = $order_info;
        $this->root_url = GetHost()."/";
        $this->show();
	}

	//ajax修改订单快递信息
	public function change_express(){
		$c_orderid = I('oid');
		$c_expressname = trim(I('expname'));
		$c_expressnum = trim(I('val'));
		if(empty($c_orderid) || empty($c_expressname) || empty($c_expressnum)){
            $this->ajaxReturn(Message(1001,"不允许操作！"));
		}

        $date['c_expressname'] = $c_expressname;
        $date['c_expressnum'] = $c_expressnum;

        $db = M('');
        $db -> startTrans();

        $where['c_orderid'] = $c_orderid;
        $result = M('order')->where($where)->save($date);
        if(!$result){
            $db -> rollback();
            $this->ajaxReturn(Message(1002,"操作失败！"));
        }

        $isagent = M('order')->where($where)->getField('c_isagent');
        if($isagent == 1){
            $w['c_agent_orderid'] = $c_orderid;
            $result = M('supplier_order')->where($w)->save($date);
            if(!$result){
                $db -> rollback();
                $this->ajaxReturn(Message(1003,"操作小蜜商城订单失败！"));
            }
        }

		$db->commit();
		$this->ajaxReturn(Message(0,"操作成功！"));
	}

    //查询物流信息
    public function query_express(){
        $expressname = I('expressname');
        $expressnum = I('expressnum');

        $parr['expressName'] = trim($expressname);
        $parr['expressId'] = trim($expressnum);

        $result = IGD('Express','Info')->GetQuery($parr);
        if($result['code'] !== 0){
            $this->error_msg = $result['msg'];
        }else{
            $this->list = $result['data']['list'];
        }
        $this->display();
    }

   	//获取订单操作按钮
   	 public function get_order_action_button($c_order_status,$c_pay_state,$c_deliverystate){
		$str = "";
		if($c_order_status==2){ //已经确认
		    if($c_pay_state==0){ //订单状态未支付
			    $str .= '<input value="取消" class="order_action" type="button" onClick=order_action("100");>'."\n";
			}else if($c_pay_state==1){ //已支付
				if($c_deliverystate==0){ //未发货
					 if($c_pay_state=='1') $s = '1'; else $s = '0';
				     $str .= '<input value="发货" class="order_action" type="button" onClick=order_action("2'.$s.'2");>'."\n";
                     $str .= '<input value="确认收货" class="order_action" type="button" onClick=order_action("215");>'."\n";
                     // $str .= '<input value="操作退款" class="refund_action" type="button" onClick=refund_action("220");>'."\n";
				 }else if($c_deliverystate==2){ //已发货
				     $str .= '<input value="设为未发货" class="order_action" type="button" onClick=order_action("210");>'."\n";
			   		 $str .= '<input value="确认收货" class="order_action" type="button" onClick=order_action("215");>'."\n";
                     // $str .= '<input value="操作退款退货" class="refund_action" type="button" onClick=refund_action("324");>'."\n";
				 }else if($c_deliverystate==5){ //已收货
				    $str .= '<font color="green">此订单已经完成！</font>'."\n";
				 }
			}else if($c_pay_state==2){ //已退款
				$str .= '<font color="red">此订单已经退款！</font>'."\n";
			}
		}else if($c_order_status==1){ //取消
		  	$str .= '<input value="确认" class="order_action" type="button" onClick=order_action("200");>'."\n";
		}else if($c_order_status==4){ //无效
		    $str .= '<input value="确认" class="order_action" type="button" onClick=order_action("200");>'."\n";
		}else if($c_order_status==3){ //已退货
			$str .= '<font color="red">此订单已经退货！</font>'."\n";
		}else if($c_order_status==5){ //申请退款
		    $str .= '<font color="red">此订单正在申请退款！</font>'."\n";
		}
		else if($c_order_status==6){ //申请退货
		    $str .= '<font color="red">此订单正在申请退货！</font>'."\n";
		}
		else if($c_order_status==7){//申请换货
		    $str .= '<font color="red">此订单正在申请换货！</font>'."\n";
		}
		return $str;
	}

    //ajax 修改订单状态
    public function ajax_op_status(){
        $opstatus = I('opstatus');//订单状态
        $orderid = I('opid');//订单ID
        if(strlen($opstatus)!=3) {echo  "非法操作"; exit; }
        if(empty($orderid)){echo  "非法操作"; exit; }

        $datas['c_order_state'] = substr($opstatus,0,1);
        $datas['c_pay_state'] = substr($opstatus,1,1);
        $datas['c_deliverystate'] = substr($opstatus,-1);

        $msg = '';
        $panrn['orderid'] = $orderid;
        $source = M('Order')->where($panrn)->getField('c_source');
        if($datas['c_deliverystate'] == 2){//发货
            $result = IGD('Order','Order')->Senddelivery($panrn);
            $msg = $result;
        }else if($datas['c_deliverystate'] == 5){//确认订单
            if($source == 2){
                $result = IGD('Storeorder','Order')->Confirmorder($panrn);
            }else{
                $result = IGD('Order','Order')->Confirmorder($panrn);
            }

            $msg = $result;
        }else if($datas['c_order_state'] == 1){//取消订单
            $result = IGD('Order','Order')->CancelOrder($panrn);
            $msg = $result;
        }else{
            //修改订单状态
            $where['c_orderid'] = $orderid;
            $result = M('order')->where($where)->save($datas);

            $isagent = M('order')->where($where)->getField('c_isagent');
            if($isagent == 1){
                $w['c_agent_orderid'] = $orderid;
                $result = M('supplier_order')->where($w)->save($datas);
            }

            if($result <= 0){
                $msg = Message(2002,"操作失败！");
            }else{
                $msg = Message(0,"操作成功！");
            }
        }

        $this->ajaxReturn($msg);
    }

    //维权操作
    public function refund_money(){
        $opstatus = I('opstatus');
        $orderid = I('orderid');

        $flag = substr($opstatus,-1);//操作标志（0-退款、4-退款退货）
        $this->total_money = D('Order','Behind')->refund_money($flag,$orderid);
        $this->opstatus = $opstatus;
        $this->orderid = $orderid;

        $this->display();
    }

    //获取操作状态按钮
    public function get_status_button(){
        $var = I('status');//订单状态
        if(strlen($var) != 3) return;
        $order_status = substr($var,0,1);
        $pay_status = substr($var,1,1);
        $shipping_status = substr($var,-1);
        $str = $this->get_order_action_button($order_status,$pay_status,$shipping_status);
        die($str);
    }

	//收货地址编辑
	public function order_address(){
		$orderid = I('Id');
		$parr['parentid'] = 1;
    	$parr['regiontype'] = 1;
    	$this->province = IGD('User','User')->GetAddress($parr);

    	$where['c_orderid'] = $orderid;
    	$this->data = M('order_address')->where($where)->find();

    	if(IS_AJAX){
    		$str = I('str');
	     	$formbul = str_replace('&quot;', '"', $str);
	     	$data = objarray_to_array(json_decode($formbul));

	     	if (empty($data['c_consignee']) || empty($data['c_telphone']) || empty($data['c_provinceid']) || empty($data['c_cityid']) || empty($data['c_districtid']) || empty($data['c_address'])) {
	     		$this->ajaxReturn(Message(1001,'带*号的选项必须填写'));
	     	}

	     	$data['c_province'] = $this->get_region_name($data['c_provinceid']);
	     	$data['c_cityname'] = $this->get_region_name($data['c_cityid']);
	     	$data['c_district'] = $this->get_region_name($data['c_districtid']);

	     	$w['c_id'] = $data['c_id'];
	     	$result = M('order_address')->where($w)->save($data);

	     	if($result == 0 || $result){
	     		$this->ajaxReturn(Message(0,"编辑成功"));
	     	}else{
	     		$this->ajaxReturn(Message(1002,"编辑失败"));
	     	}
    	}

		$this->display();
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

    //根据地址region_id查询地名
    public function get_region_name($region_id){
    	$where['region_id'] = $region_id;
    	$region_name = M('region')->where($where)->getField('region_name');
    	return $region_name;
    }

    //产品维权列表
    public function order_refund(){
        $db = M('order_refund as r');
        //条件
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
        //用户手机号
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }

        //商家手机号
        $c_phones = trim(I('c_phones'));
        if (!empty($c_phones)) {
            $w['u.c_phone'] = $c_phones;
        }
        $c_orderid = trim(I('c_orderid'));
        if (!empty($c_orderid)) {
            $w['r.c_orderid'] = $c_orderid;
        }
        $c_pname = trim(I('c_pname'));
        if (!empty($c_pname)) {
            $w['r.c_pname'] = array('like', "%{$c_pname}%");
        }
        $type = trim(I('type'));
        if (!empty($type)) {
            $w['r.c_type'] = $type;
        }
        $detailid = trim(I('detailid'));
        if (!empty($detailid)) {
            $w['r.c_orderdetailid'] = $detailid;
        }
        $refundcode = trim(I('refundcode'));
        if (!empty($refundcode)) {
            $w['r.c_refundcode'] = $refundcode;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'r.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'r.*,u.c_nickname,u.c_phone,pu.c_nickname as dlname,pu.c_phone as c_phones';
        $panrn['join'] = 'left join t_users as u on r.c_ucode=u.c_ucode';
        $panrn['join1'] = 'left join t_users as pu on r.c_acode=pu.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //维权详情
    public function refund_info(){
        $refundcode = I('refundcode');
        $w['c_refundcode'] = $refundcode;
        $info = M('order_refund')->where($w)->find();
        $str = '';
        if($info['c_type'] == 1){
            if($info['c_refundstate'] == 0){
                $str .= '<div class="btn btn-primary radius" onClick=refund_action("tk","0");>&nbsp;&nbsp;同意退款&nbsp;&nbsp;</div>'."\n";
                $str .= '<div class="btn btn-primary radius" onClick=refund_action("tk","1");>&nbsp;&nbsp;不同意退款&nbsp;&nbsp;</div>'."\n";
            }elseif($info['c_refundstate'] == 2){
                $str .= '<font color="red">商家不同意退款！</font>'."\n";
            }elseif($info['c_refundstate'] == 3){
                $str .= '<font color="greed">商家已同意退款！</font>'."\n";
            }
        }else{
            if($info['c_refundstate'] == 0){
                $str .= '<div class="btn btn-primary radius" onClick=refund_action("tkth","0");>&nbsp;&nbsp;同意申请退货&nbsp;&nbsp;</div>'."\n";
                $str .= '<div class="btn btn-primary radius" onClick=refund_action("tkth","1");>&nbsp;&nbsp;不同意申请退货&nbsp;&nbsp;</div>'."\n";
            }elseif($info['c_refundstate'] == 1){
                $str .= '<div class="btn btn-primary radius" onClick=refund_action("tkth","2");>&nbsp;&nbsp;同意退款&nbsp;&nbsp;</div>'."\n";
            }elseif($info['c_refundstate'] == 2){
                 $str .= '<font color="red">商家不同意退款退货！</font>'."\n";
            }elseif($info['c_refundstate'] == 3){
                $str .= '<font color="greed">商家已同意退款退货！</font>'."\n";
            }
        }
        $this->refund_action = $str;
        $info['c_img'] = explode('|',$info['c_img']);
        $this->data = $info;
        $this->display();
    }

    //ajax 修改维权状态
    public function ajax_op_refund(){
        $refundaction = I('refundaction');
        $id = I('id');
        $refundcode = I('refundcode');
        $parr['rcode'] = $refundcode;

        //退款操作
        $result = '';
        $Refund = IGD('Refund','Order');

        if($refundaction == 'tk'){
            if($id == 0){
                $result = $Refund->AgreeRefund($parr);
            }else{
                $result = $Refund->disagreeAgree($parr);
            }
        }else{//退款退货操作
            if($id == 0){
                $result = $Refund->AgreeRefund($parr);
            }elseif($id == 1){
                $result = $Refund->disagreeAgree($parr);
            }else{
                $result = $Refund->Refundreturn($parr);
            }
        }

        if(!empty($result)){
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn('2000','未知错误');
        }
    }

    //支付记录
    public function order_paylog(){
        $db = M('order_paylog as p');
        //订单编号
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
            $w['p.c_orderid'] = $orderid;
        }

        //第三方订单编号
        $c_thirdparty = trim(I('c_thirdparty'));
        if (!empty($c_thirdparty)) {
            $w['p.c_thirdparty'] = $c_thirdparty;
        }

        //友收包订单号
        $c_payorderid = trim(I('c_payorderid'));
        if (!empty($c_payorderid)) {
            $w['p.c_payorderid'] = $c_payorderid;
        }

        $payrule = trim(I('payrule'));
        if (!empty($payrule)) {
            $w['p.c_payrule'] = $payrule;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'p.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*';
        // $panrn['join'] = 'left join t_pay_type as t on p.c_payrule=t.c_payrule';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data = $date['list'];
        foreach ($data as $key => $value) {
            $data[$key]['c_payname'] = M('Pay_type')->where(array('c_payrule'=>$value['c_payrule']))->getField('c_payname');
            if(strstr($value['c_orderid'], 'n')){
                $data[$key]['url_flag'] = 1;
            }else{
                $data[$key]['url_flag'] = 2;
            }
        }

        $this->list = $data;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->paytype = M('pay_type')->field('c_payrule,c_payname')->select();
        $this->display();
    }

    //支付记录删除
    public function paylog_del(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('order_paylog')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //订单维权记录
    public function order_refund_log(){
        $db = M('order_refund_log as l');
        //条件
        $refundcode = trim(I('refundcode'));
        if (!empty($refundcode)) {
            $w['l.c_refundcode'] = $refundcode;
        }
        
         $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = $c_phone;
        }

         //昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['l.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['l.c_ucode'] = $usinfo['c_ucode'];
            }
        }
       

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'l.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
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
        $this->post = $parent;
        $this->display();
    }

    //订单维权记录删除
    public function refundlog_del(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('order_refund_log')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }
}