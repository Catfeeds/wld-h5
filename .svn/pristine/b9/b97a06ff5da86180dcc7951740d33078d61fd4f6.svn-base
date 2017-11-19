<?php
namespace Common\Behind;

class OrderBehind{
	//订单所处状态
    function select_statue($id){
        if(empty($id)) return "";
        switch ($id){
            case '0':
                return "";
                break;
            case '1'://待付款
                return "o.c_order_state='2' AND o.c_deliverystate='0' AND o.c_pay_state='0'";
                break;
            case '2'://待发货
                return "o.c_order_state='2' AND o.c_deliverystate='0' AND o.c_pay_state='1'";
                break;
            case '3'://已发货
                return "o.c_order_state='2' AND o.c_deliverystate='2' AND o.c_pay_state='1'";
                break;
            case '4'://已取消
                return "o.c_order_state='1'";
                break;
            case '5'://已退货
                return "o.c_order_state='3'";
                break;
            case '6'://已退款
                return "o.c_pay_state='2'";
                break;
            case '7'://退货申请单
                return "o.c_order_state='6'";
                break;
            case '8'://换货申请单
                return "o.c_order_state='7'";
                break;
            case '9'://退款申请单
                return "o.c_order_state='5'";
                break;
            case '10': //已完成
                return "o.c_order_state='2' AND o.c_deliverystate='5' AND o.c_pay_state='1'";
                break;
            default :
                return "";
                break;
        }
    }
 
	//订单记录导出Excel表格
	function sheetIndexnt(){
		 $db = M('order as o');
		 $w = array();
        //条件
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
        	$w[] = "u.c_nickname LIKE '%".$nickname."%'";
        }
        $consignee = trim(I('consignee'));
        if (!empty($consignee)) {
        	$w[] = "adr.c_consignee = '$consignee'";
        }
        $orderid = trim(I('orderid'));
        if (!empty($orderid)) {
        	$w[] = "o.c_orderid = '$orderid'";
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
        $w[] = 'o.c_isagent = 0';
        //数据数量
        $least=trim(I('least'));
		if(!empty($least)){
			$rise=$least;//起
		}
		$maximum=trim(I('maximum'));
		if(!empty($maximum)){
			$to=$maximum;//终至
		}
		$total=10;//总行
		$s=ceil($rise * $total) - $total;//当前页，第几条开始
		$scope=intval($to - $rise) + 1;
		$conud=ceil($scope * $total); //多少条

		$parent = I('param.');
		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}

		$sql="select o.*,u.c_nickname,u.c_nickname,u.c_phone,au.c_nickname as agent_name,adr.* from t_order as o left join t_users as u on u.c_ucode=o.c_ucode left join t_users as au on au.c_ucode=o.c_acode left join t_order_address as adr on adr.c_orderid=o.c_orderid $w1 ORDER BY o.c_id desc LIMIT $s,$conud";
		
		$model = M('');
		$data = $model->query($sql);

	  	foreach ($data as $k1 => $v1){
	 		$wh['c_orderid'] = $v1['c_orderid'];
	 		$data[$k1]['goods'] = M('order_details')->where($wh)->select();
		} 

		$date[0][0]=array("订单号","订单状态","用户昵称","注册手机","商家名称","商品名称","商品类型","商品属性","商品单价(×数量)","总金额","邮费","商品状态","收货人","联系方式","收货地址","下单时间","支付时间","订单附言","活动名称");
		$k=1;
		foreach ($data as $k=>$v) {
			$k++;
			$dizhi = $v['c_province'].$v['c_cityname'].$v['c_district'].$v['c_address'];
			$mystatus = $this->get_status($v['c_order_state'],$v['c_pay_state'],$v['c_deliverystate']);
			$kuaidi = ($v['c_delivery'] == 1) ? "快递方式" : "面对面方式";
			$jystyle = ($v['c_isagent'] == 1) ? "代理商品" : '自营商品';
			$activity_name = ($v['c_activity_name'] == '') ? "不属于活动" : $v['c_activity_name'];
			
			foreach ($v['goods'] as $k2=>$v2){
				$productstatus = '';
				switch ($v2['c_productstatus']) {
	                    case '0':
	                    	$productstatus = '正常';
	                        break;
	                    case '1':
	                    	$productstatus = '退款';
	                        break;
	                    case '2':
	                    	$productstatus = '退款退货';
	                        break;
	                    case '3':
	                    	$productstatus = '换货';
	                        break;
	                    case '4':
	                    	$productstatus = '商家同意';
	                        break;
	                    default:
	                    	$productstatus = '商家不同意';
	                        break;
	                }
				$date[$k][$k2]=array(
						'\''.$v['c_orderid'],
						$mystatus,
						$v['c_nickname'],
						$v['c_phone'],
						$v['agent_name'],
						$v2['c_pname'],
						$jystyle,
						$v2['c_pmodel_name'],
						'￥'.$v2['c_pprice'].'(×'.$v2['c_pnum'].')',
						'￥'.$v2['c_ptotal'],
						'￥'.$v2['c_free'],
						$productstatus,
						$v['c_consignee'],
						$v['c_telphone'],
						$dizhi,
						$v['c_addtime'],
						$v['c_paytime'],
						$v['c_postscript'],
						$activity_name
				);
			}
		}
		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");
		$filename="订单记录";
		$this->getExcel($filename,$date);
	}
	
	//调用phpExcel
	private function getExcel($fileName,$data){
		//对数据进行检验
		if(empty($data)||!is_array($data)){
			die("data must be a array");
		}
		$date=date("Y_m_d",time());
		$fileName.="_{$date}.xls";
		//创建PHPExcel对象，注意，不能少了\
		$objPHPExcel=new \PHPExcel();
		$objProps=$objPHPExcel->getProperties();
		$column=1;
		$objActSheet=$objPHPExcel->getActiveSheet();
		$objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);//设置默认高度
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('5');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('15');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('35');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth('22');//设置列宽
		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
		foreach ($data as $ke=>$row){
			foreach($row as $key=>$rows){
				if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:S{$column}");//设置边框
					array_unshift($rows,$rows['0']);
		 	 $objPHPExcel->getActiveSheet()->mergeCells("A{$column}:T{$column}");//合并单元格
		 	 $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFont()->setSize(12);//字体
		 	 $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFont()->setBold(true);//粗体
		 	 //背景色填充
		 	 $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		 	 $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						array_unshift($rows,$key+1);
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:T{$column}");//设置边框
					}
				}
				if($rows['1']=='订单号'){
					//背景色填充
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
				}
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getAlignment()->setWrapText(true);//换行
				//行写入
				$span = ord("A");
	
				foreach($rows as $keyName=>$value){
					// 列写入
					$j=chr($span);
					$objActSheet->setCellValue($j.$column, $value);
					$span++;
				}
				$column++;
					
			}
		}
		$fileName = iconv("utf-8", "gb2312", $fileName);
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); //文件通过浏览器下载
		exit;
	}

	//订单的状态
    function get_status($oid=0,$pid=0,$sid=0){ //分别为：订单 支付 发货状态
        $str = '';
        switch($oid){
            case '0':
                $str .= '未确认,';
                break;
            case '1':
                $str .= '取消,';
                break;
            case '2':
                $str .= '确认,';
                break;
            case '3':
                $str .= '已退货,';
                break;
            case '4':
                $str .= '无效,';
                break;
            case '5':
                $str .= '申请退款,';
                break;
            case '6':
                $str .= '申请退货,';
                break;
           	case '7':
                $str .= '申请换货,';
                break;
        }

       switch($pid){
            case '0':
                $str .= '未付款,';
                break;
            case '1':
                $str .= '已付款,';
                break;
            case '2':
                $str .= '已退款,';
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

    //计算退款金额
    function refund_money($flag,$orderid){
    	//判断订单中是否有产品处于维权状态中
    	$owhere['c_orderid'] = $orderid;
    	$refund = M('order_refund')->where($owhere)->count();
    	$order_info = M('order')->field('c_free,c_total_price')->where($owhere)->find();
    	$actual_price = $order_info['c_free'] + $order_info['c_total_price'];

    	$money = 0; //应退金额
    	if($refund == 0){
    		if($flag == 0){
    			$money = $actual_price;
    		}else{
    			$money = $order_info['c_total_price'];
    		}
    	}else{
    		$order_refund = M('order_refund')->where($owhere)->select();

    		$total = 0;
    		foreach ($order_refund as $k => $v) {
    			$total = $total + $v['c_total'];
    		}

    		$money = $actual_price - $total;
    	}

    	return $money;
    }	
}