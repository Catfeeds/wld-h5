<?php
namespace Common\Behind;

class MerchantBehind{
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
		
		$filename="商家商户资料";		
		
		$this->getExcel($filename,$date);
	}
	
	/**
	 * 商户资料批量导出
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	function educeshopinfo()
	{
		$db = M('Merchant as u');
    
	    //条件
	    $nickname = trim(I('nickname'));
	    if (!empty($nickname)) {
	        $w['a.c_nickname'] = array('like', "%{$nickname}%");
	    }

	    $phone = trim(I('phone'));
	    if (!empty($phone)) {
	        $w['a.c_phone'] = $phone;
	    }

	    //标志线下线上
	    $c_isfixed1 = trim(I('c_isfixed1'));
	    if ($c_isfixed1 == 1) {
	        $w['a.c_isfixed1'] = 1;
	    } else if ($c_isfixed1 == 2) {
	        $w['a.c_isfixed1'] = 0;
	    } else {
	    	die("传入商户注册类型");
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


	    $order = 'u.c_status desc,u.c_checknum asc,u.c_id asc';//排序
	    $field = 'u.*,a.c_nickname,a.c_phone,a.c_headimg';
	    $join = 'left join t_users as a on u.c_ucode=a.c_ucode';

		$data = $db->join($join)->field($field)->where($w)->order($order)->limit($s,$conud)->select();

		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");
	    if ($c_isfixed1 == 1) { //企业
	    	$date[0][0]=array("姓名","邮箱地址","手机号码","证件类型","证件号码","证件有效期",
	    		"公司名称","商户简称","证照类型","证照号码","证照有效期","企业所在地省","企业所在地市","企业详细地址",
	    		"用户名","提现","转出","订单支付","代收付款","代付付款","授信额度",
				"银行帐号","户名","银行账户类型","行别","开户银行名称","开户银行所在地","资料提交时间",
				"身份证正面","身份证反面","营业执照");

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$v['c_idcardtype'] = ($v['c_idcardtype'] == 2)?'01':'00';
				$v['c_accounttype'] = ($v['c_mchdealtype'] == 1)?'21':'11';
				$v['c_bankcity'] = $this->GetLocalName($v['c_bankcity'],'2');
				$v['c_bankid'] = $this->GetBankName($v['c_bankid']);

				$cardimg = explode(';', $v['c_indentityphoto']);
				$card1 = $cardimg[0];
				$card2 = $cardimg[1];
				$sephoto = str_replace(GetHost().'/', '', $v['c_licensephoto']);
				$v['c_licensephoto'] = empty($sephoto)?' ':$v['c_licensephoto'];
				$username = $this->CreateUserName();

				$cfield = 'c_company,c_charter,c_address';
				$compnay = M('Check_shopinfo')->where(array('c_ucode'=>$v['c_ucode']))->field($cfield)->find();

				$date[$k1][0] = array(
					$v['c_principal'],
					$v['c_email'],
					'\''.$v['c_principalmobile'],
					'\''.$v['c_idcardtype'],
					'\''.$v['c_idcardinfo'],
					' ',
					$compnay['c_company'],
					$v['c_merchantshortname'],
					'19',
					'\''.$compnay['c_charter'],
					' ',
					' ',
					' ',
					$compnay['c_address'],
					$username,
					'Y',
					'Y',
					'Y',
					'Y',
					'Y',
					'\''.'0',
					'\''.$v['c_accountcode'],
					$v['c_accountname'],
					$v['c_accounttype'],
					$v['c_bankid'],
					$v['c_bankname'],
					$v['c_bankcity'],
					'\''.$v['c_addtime'],
					$card1,
					$card2,
					$v['c_licensephoto'],
				);
			}

			//dump($date);die;
			$filename="对公商户开户资料";	
			$this->getExcel2($filename,$date);
	    } else {
	    	$date[0][0]=array("姓名","邮箱地址","手机号码","证件类型","证件号码","证件有效期",
	    	"用户名","提现","转出","订单支付","代收付款","代付付款","授信额度",
			"银行帐号","户名","银行账户类型","行别","开户银行名称","开户银行所在地","资料提交时间",
			"身份证正面","身份证反面");

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$v['c_idcardtype'] = ($v['c_idcardtype'] == 2)?'01':'00';
				$v['c_accounttype'] = ($v['c_mchdealtype'] == 1)?'21':'11';
				$v['c_bankcity'] = $this->GetLocalName($v['c_bankcity'],'2');
				$v['c_bankid'] = $this->GetBankName($v['c_bankid']);
				$username = $this->CreateUserName();

				$cardimg = explode(';', $v['c_indentityphoto']);
				$card1 = $cardimg[0];
				$card2 = $cardimg[1];
				$sephoto = str_replace(GetHost(), '', $v['c_licensephoto']);
				$v['c_licensephoto'] = empty($sephoto)?' ':$v['c_licensephoto'];

				$date[$k1][0] = array(
					$v['c_principal'],
					$v['c_email'],
					'\''.$v['c_principalmobile'],
					'\''.$v['c_idcardtype'],
					'\''.$v['c_idcardinfo'],
					' ',
					$username,
					'Y',
					'Y',
					'Y',
					'Y',
					'Y',
					'\''.'0',
					'\''.$v['c_accountcode'],
					$v['c_accountname'],
					$v['c_accounttype'],
					$v['c_bankid'],
					$v['c_bankname'],
					$v['c_bankcity'],
					'\''.$v['c_addtime'],
					$card1,
					$card2,
				);
			}

			$filename="个人商户开户资料";	
			$this->getExcel1($filename,$date);
	    }

		// $date[0][0]=array("商户名称","商户简称","经营币种","经营类型","行业类别","省份","城市","区县","地址","邮箱","企业法人","客服电话","负责人",
		// 	"负责人手机号","负责人身份证","银行卡号","开户行","开户人","帐号类型","开户支行","支行省份","支行市区",
		// 	"证件类型","证件号","执卡人手机号","资料提交时间");
		// $k1=0;
		// foreach($data as $k=>$v){
		// 	$k1++;
		// 	$v['c_mchdealtype'] = ($v['c_mchdealtype'] == 1)?'实体':'虚拟';
		// 	$v['c_accounttype'] = ($v['c_mchdealtype'] == 1)?'企业':'个人';
		// 	$v['c_idcardtype'] = ($v['c_idcardtype'] == 1)?'身份证':'护照帐户类';
		// 	$v['c_province'] = $this->GetLocalName($v['c_province'],'1');
		// 	$v['c_city'] = $this->GetLocalName($v['c_city'],'2');
		// 	$v['c_county'] = $this->GetLocalName($v['c_county'],'3');
		// 	$v['c_bankprovince'] = $this->GetLocalName($v['c_bankprovince'],'1');
		// 	$v['c_bankcity'] = $this->GetLocalName($v['c_bankcity'],'2');
		// 	$v['c_bankid'] = $this->GetBankName($v['c_bankid']);
		// 	$v['c_industrid'] = $this->GetIndustryName($v['c_industrid']);

		// 	$date[$k1][0] = array(
		// 		$v['c_merchantname'],
		// 		$v['c_merchantshortname'],
		// 		$v['c_feetype'],
		// 		$v['c_mchdealtype'],
		// 		$v['c_industrid'],
		// 		$v['c_province'],
		// 		$v['c_city'],
		// 		$v['c_county'],
		// 		$v['c_address'],
		// 		$v['c_email'],
		// 		$v['c_legalperson'],
		// 		'\''.$v['c_customerphone'],
		// 		$v['c_principal'],
		// 		$v['c_principalmobile'],
		// 		'\''.$v['c_idcode'],
		// 		'\''.$v['c_accountcode'],
		// 		$v['c_bankid'],
		// 		$v['c_accountname'],
		// 		$v['c_accounttype'],
		// 		$v['c_bankname'],
		// 		$v['c_bankprovince'],
		// 		$v['c_bankcity'],
		// 		$v['c_idcardtype'],
		// 		'\''.$v['c_idcard'],
		// 		$v['c_banktel'],
		// 		'\''.$v['c_addtime'],
		// 	);
		// }

		// //导入PHPExcel类库
		// import("Common.Org.PHPExcel");
		// import("Common.Org.PHPExcel.Writer.Excel5");
		// import("Common.Org.PHPExcel.IOFactory.php");
		
		// $filename="商户提交资料";		
		
		// $this->getExcel($filename,$date);
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth('50');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth('50');//设置列宽

	
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
				if($rows['0']=='姓名'){
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth('50');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth('50');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth('50');//设置列宽

	
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
				if($rows['0']=='姓名'){
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

	//支付类型ID与支付中心名称对应数组
	function PaytypeName(){
		// $name_arr = array(
  //           '542' => '中信总行-微信2通道',
  //           '543' => '中信总行-微信2通道',
  //           '544' => '中信总行-微信2通道',
  //           '10000181' => '中信总行-支付宝2通道',
  //           '10000180' => '中信总行-支付宝2通道',
  //       );

		$name_arr = array(
            '扫码支付(中信总行)-微信' => '542',
            '线下小额支付(中信总行)-微信' => '543',
            '公众账号支付(中信总行)-微信' => '544',
            '服务窗支付(中信总行2)-支付宝' => '10000181',
            '线下小额支付(中信总行2)-支付宝' => '10000180',
        );
        return $name_arr;
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

	//导入文件修改数据
	public function Tolead($filepath){
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
        $info = array();        
        for ($row = 1; $row <= $line; $row ++) {
            for ($column = 'A'; $column <= $end; $column ++) {                
                $val = $cur->getCellByColumnAndRow(ord($column) - 65, $row)->getValue();
                $info[$row][] = $val;
            }
        }
        
        $DB = M('Merchant_id');
        $DB -> startTrans();

        foreach ($info as $key => $value) {
        	$w['c_merchantid'] = $info[$key][0];

        	$save['c_status'] = 1;

        	$result = $DB->where($w)->save($save);

        	if(!$result){
        		$DB -> rollback();
        		return Message(1001,"修改数据失败,请检查数据重新上传");break;
        	}
        }
        $DB -> commit();
        
        return Message(0,"操作成功");
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

	//生成唯一的用户编码
	function CreateUserName($prefix = "wld") {
	    //可以指定前缀
	    $str = md5(uniqid(mt_rand(), true));
	    $uuid = substr($str, 8, 1);
	    $uuid .= substr($str, 12, 2);
	    $uuid .= substr($str, 16, 3);
	    $uuid .= substr($str, 20, 3);
	    return $prefix .'-'. $uuid;
	}
}