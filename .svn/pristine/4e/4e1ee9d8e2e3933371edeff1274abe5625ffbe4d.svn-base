<?php
namespace Common\Behind;

class YsepayBehind{
	//订单记录导出Excel表格
	function sheetIndexnt(){
		$w = array();
        //条件
        $flag = 0;
        if (!empty($ucode)) {
            $w[] = 'a.c_ucode = $ucode';
            $this->ucode = $ucode;
            $flag = 1;
        }
        $this->flag = $flag;

        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w[] = "u.c_nickname LIKE '%".$nickname."%' ";
        }
        $phone = trim(I('phone'));
        if (!empty($phone)) {
            $w[] = 'u.c_phone = $phone';
        }

        $merchantname = trim(I('merchantname'));
        if (!empty($merchantname)) {
        	$w[] = "a.c_merchantname LIKE '%".$merchantname."%' ";
        }
        $merchantshortname = trim(I('merchantshortname'));
        if (!empty($merchantshortname)) {
        	$w[] = "a.c_merchantshortname LIKE '%".$merchantshortname."%' ";
        }

        $type = trim(I('c_type'));
        if (!empty($type)) {
        	$w[] = 'a.c_type = $type';
        }
        
        $status = trim(I('status'));
        if (!empty($status)) {
            if($status == 10){
            	$w[] = 'a.c_status = 0';
            }else{
            	$w[] = 'a.c_status = $status';
            }
        }

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "a.c_addtime between '".$begintime."' and '".$endtime."'";
        } 

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
		
		$sql="select a.*,u.c_nickname,u.c_phone from t_merchant_id as a left join t_users as u on u.c_ucode=a.c_ucode $w1 and a.c_paytypeid<>10000181 and a.c_paytypeid<>10000180 ORDER BY a.c_id desc LIMIT $s,$conud";
		
		$model = M('');
		$data = $model->query($sql);

		foreach ($data as $k=>$v) {
			$paytypeid = $v['c_paytypeid'];

	        $PaytypeName = $this->PaytypeName();
	        $data[$k]['paytype_name'] = array_search($paytypeid, $PaytypeName);
	        if (strpos($data[$k]['paytype_name'],'微信')) {
	        	$data[$k]['paytype_sta'] = '微信';
	        } else if (strpos($data[$k]['paytype_name'],'支付宝')) {
	        	$data[$k]['paytype_sta'] = '支付宝';
	        }
	        
	        $data[$k]['paytype_end'] = '‰费率';
	        $data[$k]['paytype_end1'] = '微信2‰费率';
		}

		$date[0][0]=array("商户编号","商户名称","支付中心名称","费率通道","交易识别码");
		$k1=0;
		foreach($data as $k=>$v){
			$k1++;
			$date[$k1][0] = array(
				'\''.$v['c_merchantid'],
				$v['c_merchantname'],
				$v['paytype_name'],
				$v['paytype_end1'],
				// $v['paytype_sta'].$v['c_billrate'].$v['paytype_end'],
				'34387808',
			);
		}

		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");
		
		$filename="用户开户资料";		
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth('22');//设置列宽

	
		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
		foreach ($data as $ke=>$row){
			foreach($row as $key=>$rows){
				if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:F{$column}");//设置边框
					array_unshift($rows,$rows['0']);
				 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:Z{$column}");//合并单元格
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFont()->setSize(12);//字体
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFont()->setBold(true);//粗体
				 	//背景色填充
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:Z{$column}");//设置边框
					}
				}
				if($rows['0']=='商户名称'){
					//背景色填充
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
				}
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getAlignment()->setWrapText(true);//换行
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
	
	/**
	 * 商户资料批量导出
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	function educeshopinfo()
	{
		$db = M('users as u');
	    //条件
	    $sign = trim(I('sign'));
	    if (!empty($sign)) {
	      $flag = 0;
	      switch ($sign) {
	        case 1:
	          $w['a.c_checked'] = array('neq', 3);
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

	    $c_type = I('c_type');
	    if (!empty($c_type)) {
	        $w['a.c_type'] = $c_type;
	    } else {
	    	die("传入商户注册类型");
	    }

	    $EntTime1 = I('EntTime1');
	    $EntTime2 = I('EntTime2');
	    if (!empty($EntTime1) && !empty($EntTime2)) {
	    	$w['a.c_addtime'] = array('bettwen',array($EntTime1,$EntTime2));
	    }
	   
	    if($flag == 1){
	      $order = 'a.c_addtime desc';//排序
	    }else{
	      $order = 'a.c_updatetime desc,a.c_addtime desc';//排序
	    }

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

	    $field = 'u.c_ucode,u.c_acode,u.c_nickname,u.c_headimg,u.c_phone as phone,u.c_invitationcode,a.*,pu.c_nickname as parent_name,l.c_isfixed';
	    $join = 'left join t_check_shopinfo as a on u.c_ucode=a.c_ucode';
	    $join1 = 'left join t_users as pu on pu.c_ucode=u.c_acode';
	    $join2 = 'left join t_user_local as l on u.c_ucode=l.c_ucode';

	    $data = $db->join($join)->join($join1)->join($join2)->field($field)->where($w)->order($order)->limit($s,$conud)->select();

	    //导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");
	    if ($c_type == 2) { //企业
	    	$date[0][0]=array("姓名","邮箱地址","手机号码","证件类型","证件号码","证件有效期",
	    		"公司名称","商户简称","证照类型","证照号码","证照有效期","企业所在地省","企业所在地市","企业详细地址",
	    		"用户名","提现","转出","订单支付","代收付款","代付付款","授信额度",
				"银行帐号","户名","银行账户类型","行别","开户银行名称","开户银行所在地","资料提交时间");

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$v['c_idcardtype'] = ($v['c_idcardtype'] == 2)?'01':'00';
				$v['c_idcardinfo'] = empty($v['c_idcardinfo'])?$v['c_idcard']:$v['c_idcardinfo'];
				$v['c_accounttype'] = ($v['c_mchdealtype'] == 1)?'21':'11';
				$v['c_merchantshortname'] = empty($v['c_merchantshortname'])?$v['c_name']:$v['c_merchantshortname'];


				foreach ($v as $key => $value) {
					if (empty($value)) {
						$v[$key] = ' ';
					}
				}

				$date[$k1][0] = array(
					$v['c_name'],
					$v['c_email'],
					'\''.$v['phone'],
					'\''.$v['c_idcardtype'],
					'\''.$v['c_idcardinfo'],
					' ',
					$v['c_company'],
					$v['c_merchantshortname'],
					'19',
					'\''.$v['c_charter'],
					' ',
					' ',
					' ',
					$v['c_address'],
					'wld-',
					'Y',
					'Y',
					'Y',
					'Y',
					'Y',
					'\''.'0',
					'\''.$v['c_fee_cardnum'],
					$v['c_fee_name'],
					$v['c_accounttype'],
					$v['c_fee_bank'],
					$v['c_bankname'],
					$v['c_bankcity'],
					'\''.$v['c_addtime'],
				);
			}

			//dump($date);die;
			$filename="对公商户开户资料";	
			$this->getExcel2($filename,$date);
	    } else {
	    	$date[0][0]=array("姓名","邮箱地址","手机号码","证件类型","证件号码","证件有效期",
	    	"用户名","提现","转出","订单支付","代收付款","代付付款","授信额度",
			"银行帐号","户名","银行账户类型","行别","开户银行名称","开户银行所在地","资料提交时间");

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$v['c_idcardtype'] = ($v['c_idcardtype'] == 2)?'01':'00';
				$v['c_accounttype'] = ($v['c_mchdealtype'] == 1)?'21':'11';

				foreach ($v as $key => $value) {
					if (empty($value)) {
						$v[$key] = ' ';
					}
				}

				$date[$k1][0] = array(
					$v['c_name'],
					$v['c_email'],
					'\''.$v['phone'],
					'\''.$v['c_idcardtype'],
					'\''.$v['c_idcardinfo'],
					' ',
					'wld-',
					'Y',
					'Y',
					'Y',
					'Y',
					'Y',
					'\''.'0',
					'\''.$v['c_fee_cardnum'],
					$v['c_fee_name'],
					$v['c_accounttype'],
					$v['c_fee_bank'],
					$v['c_bankname'],
					$v['c_bankcity'],
					'\''.$v['c_addtime'],
				);
			}

			$filename="个人商户开户资料";	
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('22');//设置列宽
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
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:T{$column}");//设置边框
					array_unshift($rows,$rows['0']);
				 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:T{$column}");//合并单元格
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFont()->setSize(12);//字体
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFont()->setBold(true);//粗体
				 	//背景色填充
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:T{$column}");//设置边框
					}
				}
				if($rows['0']=='姓名'){
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth('22');//设置列宽

	
		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
		foreach ($data as $ke=>$row){
			foreach($row as $key=>$rows){
				if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:AB{$column}");//设置边框
					array_unshift($rows,$rows['0']);
				 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:AB{$column}");//合并单元格
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getFont()->setSize(12);//字体
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getFont()->setBold(true);//粗体
				 	//背景色填充
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
				}else{
					if(!empty($rows)){
						$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:AB{$column}");//设置边框
					}
				}
				if($rows['0']=='姓名'){
					//背景色填充
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
				}
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:AB{$column}")->getAlignment()->setWrapText(true);//换行
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

	/**
	 * 根据编码获取位置
	 * @param name,type(1省，2市，3区)
	 */
	function GetLocalName($code,$type)
	{
		$where['c_upaycode'] = $code;
		$where['region_type'] = $type;
		if (!empty($parentid)) {
			$where['parent_id'] = $parentid;
		}
		$data = M('Region')->where($where)->find();
		return $data['region_name'];
	}

	function GetBankName($code)
	{
		$where['c_code'] = $code;
		$data = M('Merchant_bank')->where($where)->find();
		return $data['c_name'];
	}

	function GetIndustryName($code)
	{
		$where['c_industrid'] = $code;
		$data = M('Shop_industry')->where($where)->find();
		return $data['c_name'];
	}


}