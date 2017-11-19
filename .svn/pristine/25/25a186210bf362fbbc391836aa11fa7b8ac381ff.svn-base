<?php
namespace Common\Behind;

class ShareBehind{
	
	/**
	 * 商户资料批量导出
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	function split_info()
	{
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
        
        // $c_settled = trim(I('c_settled'));
        // if (!empty($c_settled)) {
        //     $w['m.c_settled'] = $c_settled;
        // }

         
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



	    $order = 'm.c_addtime asc';//排序
	    $field = 'm.*';        
         
		$data = $db->join($join)->field($field)->where($w)->order($order)->limit($s,$conud)->select();  

		// dump($data);die;     

		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");
	    if ($c_settled == 1) { //已结算
	    	$date[0][0]=array("分润人","订单来源","订单号","银盛统一单号","订单总金额","分到金额",
	    		"结算方式","是否结算","结算时间","状态","来源描述","来源关键字","是否完成","完成时间",
	    		"添加时间");

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$uw['c_ucode'] = $v['c_ucode'];
	            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
	            $v['c_nickname'] = $userinfo['c_nickname'];
	            $v['c_phone'] = $userinfo['c_phone'];


			 	foreach ($v as $key => $value) {
					if (empty($value)) {
						$v[$key] = ' ';
					}
				}
				// $username = $this->CreateUserName();

				$date[$k1][0] = array(
					$v['c_nickname'],
					$v['c_source'],
					'\''.$v['c_orderid'],
					'\''.$v['c_tcode'],
					'\''.$v['c_total_money'],
					$v['c_money'],
					$v['c_type'],
					$v['c_settled'],
					'\''.$v['c_settledtime'],
					$v['c_sign'],
					$v['c_desc'],
					$v['c_key'],
					$v['c_status'],
					'\''.$v['c_updatetime'],
					'\''.$v['c_addtime'],
				);
			}
			// dump($date);
   //     			die();

			//dump($date);die;
			$filename="已结算资料";	
			$this->getExcel2($filename,$date);
	    } else {
	    	$date[0][0]=array("分润人","订单来源","订单号","银盛统一单号","订单总金额","分到金额",
	    		"结算方式","是否结算","结算时间","状态","来源描述","来源关键字","是否完成","完成时间",
	    		"添加时间");	    	

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$uw['c_ucode'] = $v['c_ucode'];
	            $userinfo = M('Users')->where($uw)->field('c_nickname')->find();
	            $v['c_nickname'] = $userinfo['c_nickname'];

				$uws['c_ucode'] = $v['c_scode'];
				$userinfos = M('Users')->where($uws)->field('c_nickname')->find();
				$v['c_nicknames'] = $userinfos['c_nickname'];

				$uwb['c_ucode'] = $v['c_bcode'];
                $userinfob = M('Users')->where($uwb)->field('c_nickname')->find();
                $v['c_nicknameb'] = $userinfob['c_nickname'];

                $v['c_status'] = $this->mtype[$data_list[$k]['c_status']];
                switch ($v['c_status']) {
                case 0:
                    $v['c_status'] = "<font color='#FF0000'>未完成</font>";
                    break;
                case 1:
                    $v['c_status'] = "<font color='#00FF00'>已完成</font>";
                    break;
                default:
                    $v['c_status'] = "<font color='#808080'>已取消</font>";
                    break;
            }
			 	foreach ($v as $key => $value) {
					if (empty($value)) {
						$v[$key] = ' ';
					}
				}


				// $username = $this->CreateUserName();


				$date[$k1][0] = array(
					$v['c_nickname'],
					$v['c_source'],
					'\''.$v['c_orderid'],
					'\''.$v['c_tcode'],
					'\''.$v['c_total_money'],
					$v['c_money'],
					$v['c_type'],
					$v['c_settled'],
					'\''.$v['c_settledtime'],
					$v['c_sign'],
					$v['c_desc'],
					$v['c_key'],
					$v['c_status'],
					'\''.$v['c_updatetime'],
					'\''.$v['c_addtime'],
				);
			}
			// dump($date);
   //     			die();

			$filename="未结算资料";	
			$this->getExcel1($filename,$date);

			
	    }
          
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth('22');//设置列宽
		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
		foreach ($data as $ke=>$row){
			foreach($row as $key=>$rows){
				if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:V{$column}");//设置边框
					array_unshift($rows,$rows['0']);
				 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:V{$column}");//合并单元格
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFont()->setSize(12);//字体
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFont()->setBold(true);//粗体
				 	//背景色填充
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:V{$column}");//设置边框
					}
				}
				if($rows['0']=='分润人'){
					//背景色填充
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
				}
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getAlignment()->setWrapText(true);//换行
				//行写入
				$span = ord("A");
	
				foreach($rows as $keyName=>$value){
					// 列写入
					$j=chr($span);
					$value = !empty($value)?$value:' ';
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

	//调用phpExcel
	private function getExcel2($fileName,$data){
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth('22');//设置列宽

	
		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
		foreach ($data as $ke=>$row){
			foreach($row as $key=>$rows){
				if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:AE{$column}");//设置边框
					array_unshift($rows,$rows['0']);
				 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:AE{$column}");//合并单元格
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getFont()->setSize(12);//字体
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getFont()->setBold(true);//粗体
				 	//背景色填充
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:AE{$column}");//设置边框
					}
				}
				if($rows['0']=='分润人'){
					//背景色填充
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
				}
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AE{$column}")->getAlignment()->setWrapText(true);//换行
				//行写入
				$span = ord("A");  
		        $span2 = ord("@");
		        foreach($rows as $keyName=>$value){  
		            if($span>ord("Z")){  
		                $span2 += 1;  
		                $span = ord("A");  
		                $j = chr($span2).chr($span);//超过26个字母时才会启用  dingling 20150626  
		            }else{  
		                if($span2>=ord("A")){  
		                    $j = chr($span2).chr($span);  
		                }else{  
		                    $j = chr($span);  
		                }  
		            }
		            //$j = chr($span);  
		            $value = !empty($value)?$value:' ';
		            $objActSheet->setCellValue($j.$column,$value);  
		            $span++;  
		        }  
		
				// foreach($rows as $keyName=>$value){
				// 	// 列写入
				// 	$j=chr($span);
				// 	$value = !empty($value)?$value:' ';
				// 	$objActSheet->setCellValue($j.$column, $value);
				// 	$span++;
				// }
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


}