<?php
namespace Common\Behind;

class ApplyforBehind{
	//订单记录导出Excel表格
	function sheetIndexnt(){
		$w = array();
        //条件
        $sign = trim(I('sign'));
        if (!empty($sign)) {
            $w[] = "d.c_sign = $sign ";
            $w[] = "d.c_state = 0 ";
        }

        $sign1 = trim(I('sign1'));
        if (!empty($sign1)) {
            $w[] = "d.c_sign = $sign1 ";
        }

        $phone = trim(I('c_phone'));
        if (!empty($phone)) {
            $w[] = "u.c_phone = $phone ";
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w[] = "u.c_ucode = $c_ucode ";
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w[] = "u.c_ucode = $c_ucode ";
        }

        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
        	$w[] = "u.c_nickname LIKE '%".$c_username."%' ";
        }

        $c_tx_code = trim(I('c_tx_code'));
        if (!empty($c_tx_code)) {
        	$w[] = "d.c_tx_code LIKE '%".$c_tx_code."%' ";
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');   
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "d.c_addtime between '".$begintime."' and '".$endtime."' ";
        }

        $begintime = I('begintime');
	    $endtime = I('endtime');   
	    if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
	        $w[] = "d.c_updatetime between '".$begintime."' and '".$endtime."'";
	        $w[] = "d.c_state = 1 ";
	    }
       
        $state = trim(I('c_state'));
        if (!empty($state)) {
            if($state == 'sqz'){
            	$w[] = "d.c_state = 0 ";
            }else{
            	$w[] = "d.c_state = $state ";
            }
            
        }

        $w[] = 'd.c_issupplier = 0';

        //数据数量
        $least=trim(I('least'));
		if(!empty($least)){
			$rise=$least;//起
		}
		$maximum=trim(I('maximum'));
		if(!empty($maximum)){
			$to=$maximum;//终至
		}
		$total=25;//总行
		$s=ceil($rise * $total) - $total;//当前页，第几条开始
		$scope=intval($to - $rise) + 1;
		$conud=ceil($scope * $total); //多少条

		$parent = I('param.');
		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}
		
		$sql="select d.*,u.c_nickname,u.c_ucode,u.c_phone from t_users_drawing as d left join t_users as u on u.c_ucode=d.c_ucode $w1 ORDER BY d.c_id desc LIMIT $s,$conud";
		
		$model = M('');
		$data = $model->query($sql);

		foreach ($data as $k=>$v) {
			switch ($v['c_state']) {
				case 0:
					$data[$k]['c_state'] = '申请中';
					break;
				case 1:
					$data[$k]['c_state'] = '申请成功';
					break;
				default:
					$data[$k]['c_state'] = '申请失败';
					break;
			}
		}

		$date[0][0]=array("省份","城市","提款编码","用户昵称","注册手机","银行卡姓名","银行名称","银行支行名称","银行卡号","提现金额","申请状态","备注","第三方流水号","申请时间","处理时间");
		$k1=1;
		foreach($data as $k=>$v){
			if (empty($v['c_province'])) {
				$v['c_province'] = ' ';
			}
			if (empty($v['c_city'])) {
				$v['c_city'] = ' ';
			}
			$k1++;
			$date[$k1][0] = array(
				$v['c_province'],
				$v['c_city'], 
				'\''.$v['c_tx_code'],
				$v['c_nickname'],
				$v['c_phone'],
				$v['c_uname'],
				$v['c_bankname'],
				$v['c_sub_bankname'],
				'\''.$v['c_banksn'],
				'￥'.$v['c_money'],
				$v['c_state'],
				$v['c_remarks'],
				'\''.$v['c_thirdparty_code'],
				$v['c_addtime'],
				$v['c_updatetime'],

			);
		}

		// $date[0][0]=array("序号","省份","城市","收款方开户行地区(省)","收款方开户行地区(市)","收款方银行名称","收款方帐号","收款方户名","金额（元）","收款方账户类型（对公填1\对私填0可不填 ）","附言","手机号");
		// $k1=1;
		// foreach($data as $k=>$v){
		// if (empty($v['c_province'])) {
			// 	$v['c_province'] = ' ';
			// }
			// if (empty($v['c_city'])) {
			// 	$v['c_city'] = ' ';
			// }
		// 	$status = '0';
		// 	if (strpos($v['c_uname'], "公司") !== false) {
		// 		$status = 1;
		// 	}
		// 	$k1++;
		// 	$date[$k1][0] = array(
		// 		$k+1,
		// 		 $v['c_province'], //省份
		//	     $v['c_city'],	  //城市
		// 		' ',
		// 		' ',
		// 		$v['c_sub_bankname'],
		// 		'\''.$v['c_banksn'],
		// 		$v['c_uname'],
		// 		'￥'.$v['c_money'],
		// 		$status,
		// 		'小蜜送钱到家',
		// 		$v['c_phone'],

		// 	);
		// }

		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");

		if($sign == 1){
			$filename="银行卡提现记录";
		}else if($sign == 2){
			$filename="微信提现记录";
		}else if($sign == 3){
			$filename="支付宝提现记录";
		}else{
			$filename="提现总记录";
		}
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('35');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('35');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
	
		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
		foreach ($data as $ke=>$row){
			foreach($row as $key=>$rows){
				if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:N{$column}");//设置边框
					array_unshift($rows,$rows['0']);
				 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:M{$column}");//合并单元格
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFont()->setSize(12);//字体
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFont()->setBold(true);//粗体
				 	//背景色填充
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:M{$column}");//设置边框
					}
				}
				if($rows['0']=='提款编码'){
					//背景色填充
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
				}
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getAlignment()->setWrapText(true);//换行
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

	//批量同意提现并输入流水
	function OptionApply($filepath)
	{
		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Reader.Excel5");
		import("Common.Org.PHPExcel.Reader.Excel2007");

		$file_path = str_replace('/',DS,$filepath);

		$file = SYS_PATH .$file_path;

        $PHPExcel = new \PHPExcel();
        
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($file)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($file)){
                return false;
            }
        }
        
        $E = $PHPReader->load($file);
        $cur = $E->getSheet(0);  // 读取第一个表
        $end = $cur->getHighestColumn(); // 获得最大的列数
        $line = $cur->getHighestRow(); // 获得最大总行数

        // 获取数据数组
        // 第一列为提现单号，第二列为流水号,第三列为提现备注
        $info = array();        
        for ($row = 2; $row <= $line; $row ++) {
            for ($column = 'A'; $column <= $end; $column ++) {                
                $val = $cur->getCellByColumnAndRow(ord($column) - 65, $row)->getValue();
                $info[$row][] = $val;
            }
        }
        
        $DB = M('Users_drawing');
        $DB -> startTrans();

        $num = 0;
        foreach ($info as $key => $value) {
        	if (!empty($info[$key][0]) && !empty($info[$key][1])) {
        		$w['c_tx_code'] = $info[$key][0];
	        	$w['c_state'] = 0;	
			    $drawing_info = $DB->where($w)->find();
			    $ucode = $drawing_info['c_ucode'];
			    $money = $drawing_info['c_money'];
			    if(!empty($ucode) && $money > 0){
			    	$save_data['c_updatetime'] = date('Y-m-d H:i:s');
			        $save_data['c_state'] = 1;
			        $save_data['c_thirdparty_code'] = $info[$key][1];
			        $save_data['c_remarks'] = $info[$key][3];
			        $result = $DB->where($w)->save($save_data);
			        if(!$result){
		        		$DB -> rollback();
		        		return Message(1001,"修改数据失败,请检查数据重新上传");break;
		        	}

		        	$content = '您提现余额￥'.$money.'申请，系统已同意，系统将进行转账处理';
			        $weburl = GetHost(1).'/index.php/Home/Balance/drawinglog';
			        //给用户发送相关消息
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
				    $Msgcentre->CreateMessegeInfo($msgdata);
				    $num++;
			    }
        	}
        }

        $DB -> commit();
        return MessageInfo(0,"操作成功",$num);
	}

	//传化导出Excel表格
	function cheetIndexnt(){
		$w = array();
        //条件
        $sign = trim(I('sign'));
        if (!empty($sign)) {
            $w[] = "d.c_sign = $sign ";
            $w[] = "d.c_state = 0 ";
        }

        $sign1 = trim(I('sign1'));
        if (!empty($sign1)) {
            $w[] = "d.c_sign = $sign1 ";
        }

        $phone = trim(I('c_phone'));
        if (!empty($phone)) {
            $w[] = "u.c_phone = $phone ";
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w[] = "u.c_ucode = $c_ucode ";
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w[] = "u.c_ucode = $c_ucode ";
        }

        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
        	$w[] = "u.c_nickname LIKE '%".$c_username."%' ";
        }

        $c_tx_code = trim(I('c_tx_code'));
        if (!empty($c_tx_code)) {
        	$w[] = "d.c_tx_code LIKE '%".$c_tx_code."%' ";
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');   
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "d.c_addtime between '".$begintime."' and '".$endtime."' ";
        }

        $begintime = I('begintime');
	    $endtime = I('endtime');   
	    if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
	        $w[] = "d.c_updatetime between '".$begintime."' and '".$endtime."'";
	        $w[] = "d.c_state = 1 ";
	    }
       
        $state = trim(I('c_state'));
        if (!empty($state)) {
            if($state == 'sqz'){
            	$w[] = "d.c_state = 0 ";
            }else{
            	$w[] = "d.c_state = $state ";
            }
            
        }

        $w[] = 'd.c_issupplier = 0';

        //数据数量
        $minimum=trim(I('minimum'));
		if(!empty($minimum)){
			$rise=$minimum;//起
		}
		$great=trim(I('great'));
		if(!empty($great)){
			$to=$great;//终至
		}
		$total=25;//总行
		$s=ceil($rise * $total) - $total;//当前页，第几条开始
		$scope=intval($to - $rise) + 1;
		$conud=ceil($scope * $total); //多少条

		$parent = I('param.');
		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}
		
		$sql="select d.*,u.c_nickname,u.c_ucode,u.c_phone from t_users_drawing as d left join t_users as u on u.c_ucode=d.c_ucode $w1 ORDER BY d.c_id desc LIMIT $s,$conud";
		
		$model = M('');
		$data = $model->query($sql);

		foreach ($data as $k=>$v) {
			switch ($v['c_state']) {
				case 0:
					$data[$k]['c_state'] = '申请中';
					break;
				case 1:
					$data[$k]['c_state'] = '申请成功';
					break;
				default:
					$data[$k]['c_state'] = '申请失败';
					break;
			}
		}

		$date[0][0]=array("商户流水号","收款方类型","账户性质","收款方姓名","开户银行名称","银行账号","银行所在省份","银行所在市","支行名称","提现金额","联行号","用途","备注");
		$k1=1;
		foreach($data as $k=>$v){
			if (empty($v['c_province'])) {
				$v['c_province'] = ' ';
			}
			if (empty($v['c_city'])) {
				$v['c_city'] = ' ';
			}
			$k1++;
			$date[$k1][0] = array(
				'\''.$v['c_tx_code'],
				'个人',
				'储蓄卡',
				$v['c_uname'],
				$v['c_bankname'],
				'\''.$v['c_banksn'],
				$v['c_province'],
				$v['c_city'],
				$v['c_sub_bankname'],
				'￥'.$v['c_money'],
				' ',
				' ',
				$v['c_remarks'],

			);
		}

		// $date[0][0]=array("序号","省份","城市","收款方开户行地区(省)","收款方开户行地区(市)","收款方银行名称","收款方帐号","收款方户名","金额（元）","收款方账户类型（对公填1\对私填0可不填 ）","附言","手机号");
		// $k1=1;
		// foreach($data as $k=>$v){
		// if (empty($v['c_province'])) {
			// 	$v['c_province'] = ' ';
			// }
			// if (empty($v['c_city'])) {
			// 	$v['c_city'] = ' ';
			// }
		// 	$status = '0';
		// 	if (strpos($v['c_uname'], "公司") !== false) {
		// 		$status = 1;
		// 	}
		// 	$k1++;
		// 	$date[$k1][0] = array(
		// 		$k+1,
		// 		 $v['c_province'], //省份
		//	     $v['c_city'],	  //城市
		// 		' ',
		// 		' ',
		// 		$v['c_sub_bankname'],
		// 		'\''.$v['c_banksn'],
		// 		$v['c_uname'],
		// 		'￥'.$v['c_money'],
		// 		$status,
		// 		'小蜜送钱到家',
		// 		$v['c_phone'],

		// 	);
		// }

		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");

		if($sign == 1){
			$filename="银行卡提现记录";
		}else if($sign == 2){
			$filename="微信提现记录";
		}else if($sign == 3){
			$filename="支付宝提现记录";
		}else{
			$filename="提现总记录";
		}
		
		$this->getExcel1($filename,$date);
	}
	
	//调用phpExcel
	private function getExcel1($fileName,$data){
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('35');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('35');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
	
		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
		foreach ($data as $ke=>$row){
			foreach($row as $key=>$rows){
				if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:N{$column}");//设置边框
					array_unshift($rows,$rows['0']);
				 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:M{$column}");//合并单元格
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFont()->setSize(12);//字体
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFont()->setBold(true);//粗体
				 	//背景色填充
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:M{$column}");//设置边框
					}
				}
				if($rows['0']=='提款编码'){
					//背景色填充
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
				}
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:M{$column}")->getAlignment()->setWrapText(true);//换行
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


	function bankCode(){

		$tlarr = array(
			'中国工商银行' => '102',
			'中国农业银行' => '103',
			'中国银行' => '104',
			'中国建设银行' => '105',
			'国家开发银行' => '201',
			'中国进出口银行' => '202',
			'中国农业发展银行' => '203',
			'交通银行' => '301',
			'中信银行' => '302',
			'华夏银行' => '304',
			'中国民生银行' => '305',
			'广东发展银行' => '306',
			'深圳发展银行' => '307',
			'招商银行' => '308',
			'兴业银行' => '309',
			'上海浦东发展银行' => '310',
			'城市商业银行' => '313',
			'农村商业银行' => '314',
			'恒丰银行' => '315',
			'浙商银行' => '316',
			'沈阳市商业银行' => '4172210',
			'洛阳市商业银行' => '4184930',
			'辽阳市商业银行' => '4192310',
			'大连市商业银行' => '4202220',
			'苏州市商业银行' => '4213050',
			'石家庄市商业银行' => '4221210',
			'杭州市商业银行' => '4233310',
			'南京市商业银行' => '4243010',
			'东莞银行' => '4256020',
			'金华市商业银行' => '4263380',
			'乌鲁木齐市商业银行' => '4278810',
			'绍兴市商业银行' => '4283370',
			'成都市商业银行' => '4296510',
			'抚顺市商业银行' => '4302240',
			'临沂市商业银行' => '4314730',
			'宜昌市商业银行' => '4325250',
			'葫芦岛市商业银行' => '4332350',
			'天津市商业银行' => '4341100',
			'郑州市商业银行' => '4354910',
			'银川市商业银行' => '4368710',
			'珠海市商业银行' => '4375850',
			'淄博市商业银行' => '4384530',
			'安庆商业银行' => '4843680',
			'绵阳商业银行' => '4856590',
			'泸州商业银行' => '4866570',
			'大同商业银行' => '4871620',
			'三门峡城市信用社' => '4885050',
			'张家口商业银行' => '4901380',
			'上海市农村信用社' => '14012900',
			'昆山市农村信用社' => '14023052',
			'常熟市农村信用社' => '14033055',
			'深圳市农村信用社' => '14045840',
			'广州市农村信用社' => '14055810',
			'杭州市萧山区农村信用社' => '14063317',
			'南海市农村信用社' => '14075882',
			'顺德市农村信用社' => '14085883',
			'昆明市农村信用社' => '14097310',
			'武汉市农村信用社' => '14105210',
			'徐州市市郊农村信用社' => '14113030',
			'重庆市农村信用社' => '14136530',
			'山东省市农村信用社' => '14144500',
			'青岛农村信用社' => '14144520',
			'东莞农村信用社' => '14156020',
			'张家港市农村商业银' => '14163056',

			'渤海银行' => '318',
			'徽商银行' => '319',
			'城市信用社' => '401',
			'农村信用社' => '402',
			'中国邮政储蓄银行' => '4437010',
			'汇丰银行' => '501',
			'东亚银行' => '502',
			'南洋商业银行' => '503',
			'恒生银行(中国)有限公司' => '504',
			'中国银行(香港)有限公司' => '505',
			'集友银行有限公司' => '506',
			'创兴银行有限公司' => '507',
			'星展银行(中国)有限公司' => '509',
			'意大利联合圣保罗银行股份有限公司' => '732',
			'瑞士信贷银行股份有限公司' => '741',
			'瑞士银行' => '742',
			'加拿大丰业银行有限公司' => '751',
			'加拿大蒙特利尔银行有限公司' => '752',
			'澳大利亚和新西兰银行集团有限公司' => '761',
			'摩根士丹利国际银行(中国)有限公司' => '771',
			'联合银行(中国)有限公司' => '775',
			'锦州市商业银行' => '4392270',
			'合肥市商业银行' => '4403610',
			'重庆市商业银行' => '4416530',
			'哈尔滨市商业银行' => '4422610',
			'贵阳市商业银行' => '4437010',
			'西安市商业银行' => '4447910',
			'无锡市商业银行' => '4453020',
			'丹东市商业银行' => '4462260',
			'兰州市商业银行' => '4478210',
			'南昌市商业银行' => '4484220',
			'太原市商业银行' => '4491610',
			'青岛市商业银行' => '4504520',
			'吉林市商业银行' => '4512420',
			'南通市商业银行' => '4523060',
			'扬州市商业银行' => '4533120',
			'九江市商业银行' => '4544240',
			'日照市商业银行' => '4554732',
			'鞍山市商业银行' => '4562230',
			'秦皇岛市商业银行' => '4571260',
			'西宁市商业银行' => '4588510',
			'台州市商业银行' => '4593450',
			'厦门市农村信用社' => '14173930',
			'北京农村信用联社' => '14181000',
			'天津市农村信用社' => '14191100',
			'宁波鄞州农村合作银行' => '14203320',
			'佛山市三水区农村信用社' => '14215881',
			'成都市农村信用社' => '14226510',
			'沧州市农村信用社' => '14231440',
			'江苏省农村信用社' => '14243000',
			'永隆银行' => '512',
			'永亨银行(中国)有限公司' => '510',
			'花旗银行(中国)有限公司' => '531',
			'美国银行有限公司' => '532',
			'摩根大通银行(中国)有限公司' => '533',
			'三菱东京日联银行(中国)有限公司' => '561',
			'日本三井住友银行股份有限公司' => '563',
			'瑞穗实业银行(中国)有限公司' => '564',
			'日本山口银行股份有限公司' => '565',
			'韩国外换银行股份有限公司' => '591',
			'友利银行(中国)有限公司' => '593',
			'韩国产业银行' => '594',
			'新韩银行(中国)有限公司' => '595',
			

			'深圳平安银行' => '4105840',
			'焦作市商业银行' => '4115010',
			'温州市商业银行' => '4123330',
			'广州市商业银行' => '4135810',
			'武汉市商业银行' => '4145210',
			'齐齐哈尔市商业银行' => '4162640',
			'湖北农信社' => '04025350',
			'乐山商行' => '03136650',
			'宁波银行' => '04083320',
			'昆仑银行' => '03138820',
			'南宁商业银行' => '4786110',
			'包头商业银行' => '4791920',
			'连云港商业银行' => '4803070',
			'威海商业银行' => '4814650',
			'淮北商业银行' => '4823660',
			'攀枝花商业银行' => '4836560',
			'农信社(北京)' => '04021000',
			'南京银行' => '04243010',
			'农信湖南' => '04025510',
			'东莞银行' => '04256020',
			'德意志银行(中国)有限公司' => '712',
			'德国商业银行股份有限公司' => '713',
			'德国西德银行股份有限公司' => '714',
			'德国巴伐利亚州银行' => '715',
			'德国北德意志州银行' => '716',
			'江西农信社' => '04024210',
			'山东农信社' => '14144500',
			'南昌银行' => '04484220',
			'农信安徽' => '04023610',
			'绵阳商行' => '04856590',
			'荷兰合作银行有限公司' => '776',
			'厦门国际银行' => '781',
			'法国巴黎银行(中国)有限公司' => '782',
			'华商银行' => '785',
			'华一银行' => '787',
			'(澳门地区)银行' => '969',
			'(香港地区)银行' => '989',
			'中信实业银行' => '3020000',
			'上海银行' => '4012900',
			'厦门市商业银行' => '4023930',
			'北京银行' => '4031000',
			'烟台市商业银行' => '4044560',
			'福州市商业银行' => '4053910',
			'长春市商业银行' => '4062410',
			'镇江市商业银行' => '4073140',
			'宁波市商业银行' => '4083320',
			'济南市商业银行' => '4094510',
			'盐城市商业银行' => '4603110',
			'长沙市商业银行' => '4615510',
			'潍坊市商业银行' => '4624580',
			'赣州市商业银行' => '4634280',
			'泉州银行' => '4643970',
			'营口市商业银行' => '4652280',
			'昆明市商业银行' => '4667310',
			'阜新市商业银行' => '4672290',
			'常州市商业银行' => '4683040',
			'淮安市商业银行' => '4693080',
			'嘉兴市商业银行' => '4703350',
			'芜湖市商业银行' => '4713620',
			'廊坊市商业银行' => '4721460',
			'台州市泰隆城市信用社' => '4733450',
			'呼和浩特市商业银行' => '4741900',
			'湖州市商业银行' => '4753360',
			'马鞍山商业银行' => '4773650',
			'韩国中小企业银行有限公司' => '596',
			'韩亚银行(中国)有限公司' => '597',
			'华侨银行(中国)有限公司' => '621',
			'大华银行(中国)有限公司' => '622',
			'星展银行(中国)有限公司' => '623',
			'泰国盘谷银行(大众有限公司)' => '631',
			'奥地利中央合作银行股份有限公司' => '641',
			'比利时联合银行股份有限公司' => '651',
			'比利时富通银行有限公司' => '652',
			'荷兰银行' => '661',
			'荷兰安智银行股份有限公司' => '662',
			'渣打银行' => '671',
			'英国苏格兰皇家银行公众有限公司' => '672',
			'法国兴业银行(中国)有限公司' => '691',
			'法国东方汇理银行股份有限公司' => '694',
			'法国外贸银行股份有限公司' => '695',
			'德国德累斯登银行股份公司' => '711'

		);
	return $tlarr;

	}

		
}