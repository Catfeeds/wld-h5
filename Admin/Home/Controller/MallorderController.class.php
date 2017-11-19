<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  小蜜商城订单管理
 */
class MallorderController extends BaseController {
	//订单列表
	public function order_list(){
		$db = M('supplier_order as o');
        //条件
        //买家微商
        // $ucode = trim(I('ucode'));
        // if (!empty($ucode)) {
        //     $w['u.c_ucode'] = $ucode;
        //     $this->ucode = $ucode;
        // }
        // $scode = trim(I('scode'));
        // if (!empty($pcode)) {
        //     $w['s.c_ucode'] = $scode;
        //     $this->pcode = $scode;
        // }

		$nickname = trim(I('nickname'));//购买人
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }
		$pnickname = trim(I('pnickname'));//微商
        if (!empty($pnickname)) {
            $w['pu.c_nickname'] = array('like', "%{$pnickname}%");
        }
        $name = trim(I('name'));//供货商
        if (!empty($name)) {
            $w['s.c_name'] = array('like', "%{$name}%");
        }

        $consignee = trim(I('consignee'));
        if (!empty($consignee)) {
            $w['adr.c_consignee'] = $consignee;
        }
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
            $w['o.c_orderid'] = $orderid;
        }
        $c_agent_orderid = trim(I('c_agent_orderid'));
        if (!empty($c_agent_orderid)) {
            $w['o.c_agent_orderid'] = $c_agent_orderid;
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
        $panrn['field'] = 'o.*,s.c_name,adr.*,u.c_nickname,pu.c_nickname as pname';
        $panrn['join'] = 'left join t_supplier as s on s.c_ucode=o.c_acode';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=o.c_ucode';
        $panrn['join2'] = 'left join t_users as pu on pu.c_ucode=o.c_scode';
        $panrn['join3'] = 'left join t_supplier_order_address as adr on adr.c_orderid=o.c_orderid';
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
                	'c_scode'=>$row['c_scode'],
                	'c_acode'=>$row['c_acode'],
                	'c_name'=>(!empty($row['c_name'])? $row['c_name'] : '无知'),//供货商
                	'c_nickname'=>(!empty($row['c_nickname'])? $row['c_nickname'] : '无知'),//购买人
                	'pname'=>(!empty($row['pname'])? $row['pname'] : '无知'),//微商
                    'c_orderid'=>$row['c_orderid'],
                    'c_agent_orderid'=>$row['c_agent_orderid'],
                    'c_severtype'=>$row['c_severtype'],
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
                    'c_activity_name'=>$row['c_activity_name'],
                    'c_addtime'=>(!empty($row['c_addtime'])? $row['c_addtime'] : '无知'),
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
        $Order = D('Mallorder','Behind');
        $Order -> sheetIndexnt();
    }

	//订单详情
    function get_details($orderid){
    	$db = M('supplier_order_details as od');
    	$w['od.c_orderid'] = $orderid;

    	$panrn['where'] = $w;
    	$panrn['field'] = 'od.c_pname,od.c_pprice,od.c_pmodel_name,od.c_pnum,od.c_ptotal,od.c_pimg,od.c_productstatus,od.c_detailid,u.c_nickname';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=od.c_ucode';
    	$list=D('Db','Behind');
        $date=$list->mate_select($db,$panrn);
        foreach ($date as $k => $v) {
                switch ($date[$k]['c_productstatus']) {
                    case '0':
                        $date[$k]['mystatus'] = '<font color="green">正常</font>';
                        break;
                    case '1':
                    // <a title="维权详情" href="'.C('TMPL_PARSE_STRING.__HHOME__').'/Mallorder/product_score?detailid={$v.c_detailid}"></a>;
                        $date[$k]['mystatus'] = '<font color="red">已退款</font>';
                        break;
                    case '2':
                        $date[$k]['mystatus'] = '<font color="red">退款退货</font>';
                        break;
                    case '3':
                        $date[$k]['mystatus'] = '<font color="red">换货</font>';
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
		$db = M('supplier_order as o');

		$orderid = I('Id');
		$w['o.c_orderid'] = $orderid;

        $panrn['where'] = $w;
        //分页显示数据
        $panrn['field'] = 'o.*,s.c_name,adr.*,u.c_nickname,pu.c_nickname as pnickname';
        $panrn['join'] = 'left join t_supplier as s on s.c_ucode=o.c_acode';
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=o.c_ucode';
        $panrn['join2'] = 'left join t_users as pu on u.c_ucode=o.c_scode';
        $panrn['join3'] = 'left join t_supplier_order_address as adr on adr.c_orderid=o.c_orderid';
        $list=D('Db','Behind');
        $order_info=$list->mate_find($db,$panrn);
        if(!empty($order_info)){
        	//订单状态
        	$order_info['mystatus'] = $this->get_status($order_info['c_order_state'],$order_info['c_pay_state'],$order_info['c_deliverystate']);

            $w1['c_orderid'] = $order_info['c_orderid'];
        	//订单详情
            $db1 = M('supplier_order_details as od');
        	$panrn1['where'] = $w1;
	        $panrn1['field'] = 'od.*';
	        // $panrn1['join'] = 'left join t_users as u on u.c_ucode=od.c_pucode';
            $order_details = $list->mate_select($db1,$panrn1);
            foreach ($order_details as $k => $v) {
                switch ($order_details[$k]['c_productstatus']) {
                    case '0':
                        $order_details[$k]['c_productstatus'] = '<font color="green">正常</font>';
                        break;
                    case '1':
                        $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="__HHOME__/Order/product_score?detailid={$v.c_detailid}"><font color="red">退款</font></a>';
                        break;
                    case '2':
                        $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="__HHOME__/Order/product_score?detailid={$v.c_detailid}"><font color="red">退款退货</font></a>';
                        break;
                    case '3':
                        $order_details[$k]['c_productstatus'] = "<font color='red'>换货</font>";
                        break;
                    case '4':
                        $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="__HHOME__/Order/product_score?detailid={$v.c_detailid}"><font color="green">商家同意</font></a>';
                        break;
                    default:
                        $order_details[$k]['c_productstatus'] = '<a title="维权详情" href="__HHOME__/Order/product_score?detailid={$v.c_detailid}"><font color="red">商家不同意</font></a>';
                        break;
                }
            }
            $order_info['order_details'] = $order_details;
            //订单支付记录
            $panrn2['where'] = $w1;
            $panrn1['field'] = 'op.*,p.c_payname';
            $panrn1['join'] = 'left join t_pay_type as p on p.c_payrule=op.c_payrule';
            $order_paylog = $list->mate_select(M('supplier_order_paylog as op'),$panrn1);
            $order_info['order_paylog'] = $order_paylog;
            //返回订单操作按钮
		    $order_info['order_action_button'] = $this->get_order_action_button($order_info['c_order_state'],$order_info['c_pay_state'],$order_info['c_deliverystate']);
        }
        $this->rt = $order_info;
        $this->show();
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

	//ajax修改订单快递信息
	public function change_express(){
		$c_orderid = I('oid');
		$c_expressname = trim(I('expname'));
		$c_expressnum = trim(I('val'));
		if(empty($c_orderid) || empty($c_expressname) || empty($c_expressnum)){
		   $this->ajaxReturn(Message(1001,"不允许操作!"));
		}
        //判断订单类型
        $w['c_orderid'] = $c_orderid;
        $agent_orderid = M('supplier_order')->where($w)->getField('c_agent_orderid');

		$model = M('supplier_order');
        $model->startTrans();

		$date['c_expressname'] = $c_expressname;
		$date['c_expressnum'] = $c_expressnum;

		$result = $model->where("c_orderid='".$c_orderid."'")->save($date);
        if(!empty($agent_orderid)){
            $result = M('order')->where("c_orderid='".$agent_orderid."'")->save($date);
        }

		if($result){
            $model -> commit();
		    $this->ajaxReturn(Message(0,"保存成功!"));
		}else{
            $model -> rollback();
            $this->ajaxReturn(Message(1002,"保存失败!"));
        }
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

    //ajax 修改订单状态
    public function ajax_op_status(){
        $opstatus = I('opstatus');//订单状态
        $orderid = I('opid');//订单ID

        //判断订单类型
        $w['c_orderid'] = $orderid;
        $agent_orderid = M('supplier_order')->where($w)->getField('c_agent_orderid');

        if(strlen($opstatus)!=3) {$this->ajaxReturn(Message(2001,"非法操作!"));}
        if(empty($orderid)){$this->ajaxReturn(Message(2002,"非法操作!"));}

        $datas['c_order_state'] = substr($opstatus,0,1);
        $datas['c_pay_state'] = substr($opstatus,1,1);
        $datas['c_deliverystate'] = substr($opstatus,-1);

        if($datas['c_deliverystate'] == 2){//发货
        	if(empty($agent_orderid)){
        		$panrn['orderid'] = $orderid;
        		$result = IGD('Supplyorder','Agorder')->Senddelivery($panrn);
        		$msg = $result;
        	}else{
        		$panrn['orderid'] = $agent_orderid;
        		$result = IGD('Order','Order')->Senddelivery($panrn);
        		$msg = $result;
        	}
        }else if($datas['c_deliverystate'] == 5){//确认订单
            if(empty($agent_orderid)){
        		$panrn['orderid'] = $orderid;
        		$result = IGD('Supplyorder','Agorder')->Confirmorder($panrn);
        		$msg = $result;
        	}else{
        		$panrn['orderid'] = $agent_orderid;
        		$result = IGD('Order','Order')->Confirmorder($panrn);
        		$msg = $result;
        	}
        }else if($datas['c_order_state'] == 1){//取消订单
            if(empty($agent_orderid)){
        		$panrn['orderid'] = $orderid;
        		$result = IGD('Supplyorder','Agorder')->CancelOrder($panrn);
        		$msg = $result;
        	}else{
        		$panrn['orderid'] = $agent_orderid;
        		$result = IGD('Order','Order')->CancelOrder($panrn);
        		$msg = $result;
        	}
        }else{
            //修改订单状态
            $result = M('supplier_order')->where("c_orderid='".$orderid."'")->save($datas);
            if(!empty($agent_orderid)){
                $result = M('order')->where("c_orderid='".$agent_orderid."'")->save($datas);
            }

            if($result <= 0){
                $msg = Message(2003,"操作失败！");
            }else{
                $msg = Message(0,"操作成功！");
            }
        }

        $this->ajaxReturn($msg);
    }

    //收货地址编辑
	public function order_address(){
		$orderid = I('Id');
		$parr['parentid'] = 1;
    	$parr['regiontype'] = 1;
    	$this->province = IGD('User','User')->GetAddress($parr);

    	$where['c_orderid'] = $orderid;
    	$this->data = M('supplier_order_address')->where($where)->find();

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
	     	$result = M('supplier_order_address')->where($w)->save($data);

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

    //维权列表
    public function order_refund(){
        $db = M('supplier_order_refund as r');
        //条件
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
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
            $w['r.c_detailid'] = $detailid;
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
        $panrn['field'] = 'r.*,s.c_name,u.c_nickname,pu.c_nickname as pname';
        $panrn['join'] = 'left join t_supplier as s on s.c_ucode=r.c_acode';//供货商
        $panrn['join1'] = 'left join t_users as u on u.c_ucode=r.c_ucode';//购买人
        $panrn['join2'] = 'left join t_users as pu on pu.c_ucode=r.c_scode';//商家
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    public function refund_info(){
        $refundcode = I('refundcode');
        $w['c_refundcode'] = $refundcode;
        $info = M('supplier_order_refund')->where($w)->find();
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
        $refundcode = I('refundcode');//小蜜商城

        $where['c_refundcode'] = $refundcode;
        $order_detailsid = M('supplier_order_refund')->where($where)->getField('c_orderdetailid');
        $arr['c_orderdetailid'] = substr($order_detailsid,1,strlen($order_detailsid));

        $fundcode = M('Order_refund')->where($arr)->getField('c_refundcode');

        if(!empty($fundcode)){
            $parr['rcode'] = $fundcode;
            $Refund = IGD('Refund','Order');
        }else{
            $parr['rcode'] = $refundcode;
            $Refund = IGD('Supplyrefund','Agorder');
        }

        //退款操作
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

    //订单维权记录
    public function order_refund_log(){
        $db = M('supplier_order_refund_log as l');
        //条件
        $refundcode = trim(I('refundcode'));
        if (!empty($refundcode)) {
            $w['l.c_refundcode'] = $refundcode;
        }

        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = $nickname;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'l.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,u.c_nickname';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->display();
    }

    //支付记录
    public function order_paylog(){
        $db = M('supplier_order_paylog as p');
        //条件
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
            $w['p.c_orderid'] = $orderid;
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
        $panrn['field'] = 'p.*,t.c_payname';
        $panrn['join'] = 'left join t_pay_type as t on p.c_payrule=t.c_payrule';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->paytype = M('pay_type')->field('c_payrule,c_payname')->select();
        $this->display();
    }


}