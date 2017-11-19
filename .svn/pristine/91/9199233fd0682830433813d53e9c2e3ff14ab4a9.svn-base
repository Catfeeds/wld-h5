<?php
namespace Common\Behind;

class YspayBehind{
	
	/**
	 * 商户资料批量导出
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	function yspayinfo()
	{
		$db = M('User_yspay as a');
    
	    //条件
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
 
        //标志线下线上
	    $c_storetype = trim(I('c_storetype'));
	    if ($c_storetype == 1) {
	        $w['a.c_storetype'] = 1;
	    } else if ($c_storetype == 'sp') {
	        $w['a.c_storetype'] = 0;
	    } else {
	    	die("传入商户注册类型");
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

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "a.c_addtime between '".$begintime."' and '".$endtime."'";
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
        } else if ($c_openaccount == 'er') {
            $w['a.c_openaccount'] = 0;
        } else if ($c_openaccount == 2) {
            $w['a.c_openaccount'] = 2;
        }

        $c_storetials = trim(I('c_storetials'));
        if (!empty($c_storetials)) {
            $w['a.c_storetials'] = $c_storetials;
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


	    $order = 'a.c_id asc';//排序
	    $field = 'a.*';        

		$data = $db->join($join)->field($field)->where($w)->order($order)->limit($s,$conud)->select();       

		//导入PHPExcel类库
		import("Common.Org.PHPExcel");
		import("Common.Org.PHPExcel.Writer.Excel5");
		import("Common.Org.PHPExcel.IOFactory.php");
	    if ($c_storetype == 1) { //企业
	    	$date[0][0]=array("姓名","邮箱地址","手机号码","证件类型","证件号码","证件有效期",
	    		"公司名称","商户简称","证照类型","证照号码","证照有效期","企业所在地省","企业所在地市","企业详细地址",
	    		"用户名","提现","转出","订单支付","代收付款","代付付款","授信额度",
				"银行帐号","户名",'银行账户类型',"行别","开户银行名称","开户银行所在地","资料提交时间",
				"身份证正面","身份证反面","营业执照",'银行卡正面','银行卡反面');

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$uw['c_ucode'] = $v['c_ucode'];
	            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
	            $v['c_nickname'] = $userinfo['c_nickname'];
	            $v['c_phone'] = $userinfo['c_phone'];
				$v['c_cardtype'] = ($v['c_cardtype'] == 2)?'01':'00';
				$v['c_banktype'] = ($v['c_banktype'] == 2)?'21':'11';
				foreach ($v as $key => $value) {
					if (empty($value)) {
						$v[$key] = ' ';
					}
				}

				$card1 = empty($v['c_idcard_img'])?' ':GetHost().'/'.$v['c_idcard_img'];
				$card2 = empty($v['c_idcard_img1'])?' ':GetHost().'/'.$v['c_idcard_img1'];
				$bank1 = empty($v['c_bankcard_img'])?' ':GetHost().'/'.$v['c_bankcard_img'];
				$bank2 = empty($v['c_bankcard_img1'])?' ':GetHost().'/'.$v['c_bankcard_img1'];
				$v['c_charter_img'] = empty($v['c_charter_img'])?' ':GetHost().'/'.$v['c_charter_img'];
			
				//$username = $this->CreateUserName();

				$date[$k1][0] = array(
					$v['c_person'],
					$v['c_email'],
					'\''.$v['c_phone'],
					'\''.$v['c_cardtype'],
					'\''.$v['c_personidcard'],
					$v['c_personidcardendtime'],
					$v['c_company'],
					$v['c_merchantshort'],
					'19',
					'\''.$v['c_charterno'],
					$v['c_charterendtime'],
					$v['c_province'],
					$v['c_city'],
					$v['c_address'],
					$v['c_username'],
					'Y',
					'Y',
					'Y',
					'N',
					'Y',
					'\''.'0',
					'\''.$v['c_bankno'],
					$v['c_bankuser'],
					$v['c_banktype'],
					$v['c_bankallname'],
					$v['c_bankbranch'],
					$v['c_bankcity'],
					'\''.$v['c_addtime'],
					$card1,
					$card2,
					$v['c_charter_img'],
					$bank1,
					$bank2,
				);
			}
			// dump($date);
   //     			die();

			//dump($date);die;
			$filename="对公商户开户资料";	
			$this->getExcel2($filename,$date);
	    } else {
	    	$date[0][0]=array("姓名","邮箱地址","手机号码","证件类型","证件号码","证件有效期",
	    	"用户名","提现","转出","订单支付","代收付款","代付付款","授信额度",
			"银行帐号","户名",'银行账户类型',"行别","开户银行名称","开户银行所在地","资料提交时间",
			"身份证正面","身份证反面",'银行卡正面','银行卡反面');	    	

			$k1=0;
			foreach($data as $k=>$v){
				$k1++;
				$uw['c_ucode'] = $v['c_ucode'];
	            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
	            $v['c_nickname'] = $userinfo['c_nickname'];
	            $v['c_phone'] = $userinfo['c_phone'];
				$v['c_cardtype'] = ($v['c_cardtype'] == 2)?'01':'00';
				$v['c_banktype'] = ($v['c_banktype'] == 2)?'21':'11';
				foreach ($v as $key => $value) {
					if (empty($value)) {
						$v[$key] = ' ';
					}
				}

				$card1 = empty($v['c_idcard_img'])?' ':GetHost().'/'.$v['c_idcard_img'];
				$card2 = empty($v['c_idcard_img1'])?' ':GetHost().'/'.$v['c_idcard_img1'];
				$bank1 = empty($v['c_bankcard_img'])?' ':GetHost().'/'.$v['c_bankcard_img'];
				$bank2 = empty($v['c_bankcard_img1'])?' ':GetHost().'/'.$v['c_bankcard_img1'];
			


				// $v['c_banktype'] = ($v['c_banktype'] == 1)?'21':'11';

				// $username = $this->CreateUserName();


				$date[$k1][0] = array(
					$v['c_person'],
					$v['c_email'],
					'\''.$v['c_phone'],
					'\''.$v['c_cardtype'],
					'\''.$v['c_personidcard'],
					$v['c_personidcardendtime'],
					$v['c_username'],
					'Y',
					'Y',
					'Y',
					'N',
					'Y',
					'\''.'0',
					'\''.$v['c_bankno'],
					$v['c_bankuser'],
					$v['c_banktype'],
					$v['c_bankallname'],
					$v['c_bankbranch'],
					$v['c_bankcity'],
					'\''.$v['c_addtime'],
					$card1,
					$card2,
					$bank1,
					$bank2,
				);
			}
			// dump($date);
   //     			die();

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
		$objPHPExcel->getActiveSheet()->getColumnDimension('w')->setWidth('80');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('x')->setWidth('80');//设置列宽

	
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth('80');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth('80');//设置列宽

	
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

	// //支付类型ID与支付中心名称对应数组
	// function PaytypeName(){
	// 	// $name_arr = array(
 //  //           '542' => '中信总行-微信2通道',
 //  //           '543' => '中信总行-微信2通道',
 //  //           '544' => '中信总行-微信2通道',
 //  //           '10000181' => '中信总行-支付宝2通道',
 //  //           '10000180' => '中信总行-支付宝2通道',
 //  //       );

	// 	$name_arr = array(
 //            '扫码支付(中信总行)-微信' => '542',
 //            '线下小额支付(中信总行)-微信' => '543',
 //            '公众账号支付(中信总行)-微信' => '544',
 //            '服务窗支付(中信总行2)-支付宝' => '10000181',
 //            '线下小额支付(中信总行2)-支付宝' => '10000180',
 //        );
 //        return $name_arr;
	// }
	
	// //调用phpExcel
	// private function getExcel($fileName,$data){
	// 	//对数据进行检验
	// 	if(empty($data)||!is_array($data)){
	// 		die("data must be a array");
	// 	}
	// 	$date=date("Y_m_d",time());
	// 	$fileName.="_{$date}.xls";
	// 	//创建PHPExcel对象，注意，不能少了\
	// 	$objPHPExcel=new \PHPExcel();
	// 	$objProps=$objPHPExcel->getProperties();
	// 	$column=1;
	// 	$objActSheet=$objPHPExcel->getActiveSheet();
	// 	$objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
	// 	$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);//设置默认高度
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth('22');//设置列宽
	// 	$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth('22');//设置列宽

	
	// 	//设置边框
	// 	$sharedStyle1=new \PHPExcel_Style();
	// 	$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
	// 	foreach ($data as $ke=>$row){
	// 		foreach($row as $key=>$rows){
	// 			if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
	// 				$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:F{$column}");//设置边框
	// 				array_unshift($rows,$rows['0']);
	// 			 	$objPHPExcel->getActiveSheet()->mergeCells("A{$column}:Z{$column}");//合并单元格
	// 			 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFont()->setSize(12);//字体
	// 			 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFont()->setBold(true);//粗体
	// 			 	//背景色填充
	// 			 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
	// 			 	$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
	// 			}else{
	// 				if(!empty($rows)){
	// 					$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:Z{$column}");//设置边框
	// 				}
	// 			}
	// 			if($rows['0']=='商户名称'){
	// 				//背景色填充
	// 				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
	// 				$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
	// 			}
	// 			$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
	// 			$objPHPExcel->getActiveSheet()->getStyle("A{$column}:Z{$column}")->getAlignment()->setWrapText(true);//换行
	// 			//行写入
	// 			$span = ord("A");
	
	// 			foreach($rows as $keyName=>$value){
	// 				// 列写入
	// 				$j=chr($span);
	// 				$objActSheet->setCellValue($j.$column, $value);
	// 				$span++;
	// 			}
	// 			$column++;
				
	// 		}
	// 	}

	// 	$fileName = iconv("utf-8", "gb2312", $fileName);
	// 	//设置活动单指数到第一个表,所以Excel打开这是第一个表
	// 	$objPHPExcel->setActiveSheetIndex(0);
	// 	header('Content-Type: application/vnd.ms-excel');
	// 	header("Content-Disposition: attachment;filename=\"$fileName\"");
	// 	header('Cache-Control: max-age=0');
	// 	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	// 	$objWriter->save('php://output'); //文件通过浏览器下载
	// 	exit;
	// }

    //导入文件修改数据
	public function Leading_in($filepath){
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
        for ($row = 2; $row <= $line; $row ++) {
            for ($column = 'A'; $column <= $end; $column ++) {                
                $val = $cur->getCellByColumnAndRow(ord($column) - 65, $row)->getValue();
                $info[$row][] = $val;
            }
        }
       	
       	$num = 0;$failnum = 0; 
        foreach ($info as $key => $value) {
        	$w['c_username'] = $info[$key][6];
        	$w['c_openaccount'] = array('neq',1);
        	$ysdata = M('User_yspay')->where($w)->find();
        	if ($ysdata) {
        		if ($ysdata['c_banktype'] == 2) {
		            $ysdata['c_person'] = $ysdata['c_merchant'];
		        }
        		$DB = M('User_yspay');
        		$DB->startTrans();

        		//查询银盛金额并代付金额
        		$result = IGD('Balance','User')->dfYesmoney($ysdata);
        		if ($result['code'] != 0) {
        			$DB->rollback();
	        		return Message(1001,$result['msg']."。已成功操作：".$num);break;
        		}

        		//操作开户状态
        		$save['c_openaccount'] = 1;
        		$save['c_updatetime'] = date('Y-m-d H:i:s');
	        	$result = $DB->where($w)->save($save);
	        	if(!$result){
	        		$DB->rollback();
	        		return Message(1001,"修改数据失败,请检查数据重新上传。已成功操作：".$num);break;
	        	}
	        	
	        	$num++;
	        	$DB->commit();
        	} else {
        		$w1['c_username'] = $info[$key][6];
        		$result = M('User_yspay')->where($w1)->getField('c_id');
        		if (!$result) {
        			$failnum++;
        			//记录日志
        			$this->debug('不存在的商户号',$info[$key][6]);
        		}
        	}
        }
        
        
        return MessageInfo(0,"操作成功".$num.",不存在".$failnum);
	}

	#日志记录
	function debug($tempType,$tempStr){
		$log_name = 'data/ysusername.txt';
		$tempStr = date('Y-m-d H:i:s').' '.$tempType."\r\n".$tempStr."\r\n\r\n";
		$myfile = fopen($log_name, "a");
		fwrite($myfile, $tempStr);
		fclose($myfile);
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