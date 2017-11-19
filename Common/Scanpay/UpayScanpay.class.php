<?php

/**
 * 友收宝进件接口
 */
class UpayScanpay {

	/**
	 * 商户资料同步友收宝资料
	 * @param ucode
	 */
	function SynchronousInfo($parr,$status)
	{
		$ucode = $parr['ucode'];

		//查询商家资料
		$where['c_ucode'] = $parr['ucode'];
		$shopinfo = M('Check_shopinfo')->where($where)->find();
		if (!$shopinfo) {
			return Message(3000,'资料不存在');
		}

		//查询商家所处地理位置
		$localinfo = M('User_local')->where($where)->find();
		if (!$localinfo) {
			return Message(3001,'位置信息不存在');
		}

		$uw['c_ucode'] = $parr['ucode'];
		$upayinfo = M('Merchant')->where($uw)->find();
		if (!$upayinfo) {
			$data['c_outmerchantid'] = 'm'.time();
			$data['c_ucode'] = $shopinfo['c_ucode'];
			$data['c_addtime'] = gdtime();
		}
			
		//基本信息
		$data['c_merchantname'] = $shopinfo['c_merchantname'];
		$data['c_feetype'] = $shopinfo['c_feetype'];
		$data['c_mchdealtype'] = $shopinfo['c_mchdealtype'];
		$data['c_remark'] = $shopinfo['c_remark'];
		$data['c_merchantshortname'] = $shopinfo['c_merchantshortname'];
		$data['c_industrid'] = $this->GetIndustryCode($ucode);
		$data['c_province'] = $this->GetLocalCode($localinfo['c_province'],1);
		$data['c_city'] = $this->GetLocalCode($localinfo['c_city'],2,$this->GetIdbyCode($data['c_province'],$localinfo['c_province']));
		$data['c_county'] = $this->GetLocalCode($localinfo['c_county'],3,$this->GetIdbyCode($data['c_city'],$localinfo['c_city']));
		$data['c_address'] = $localinfo['c_address'];
		$data['c_tel'] = $shopinfo['c_phone'];
		$data['c_email'] = $shopinfo['c_email'];
		$data['c_legalperson'] = $shopinfo['c_legalperson'];
		$data['c_customerphone'] = $shopinfo['c_home_tel'];
		$data['c_principal'] = $shopinfo['c_name'];
		$data['c_principalmobile'] = $shopinfo['c_phone'];
		$data['c_idcode'] = $shopinfo['c_idcard'];
		$data['c_indentityphoto'] = GetHost().'/'.$shopinfo['c_idcard_img'].';'.GetHost().'/'.$shopinfo['c_idcard_img1'];
		$data['c_licensephoto'] = GetHost().'/'.$shopinfo['c_charter_img'];

		//收款帐号信息
		$data['c_accountcode'] = $shopinfo['c_fee_cardnum'];
		$data['c_bankid'] = $this->GetBankCode($shopinfo['c_fee_bank']);
		$data['c_accountname'] = $shopinfo['c_fee_name'];
		$data['c_accounttype'] = $shopinfo['c_accounttype'];
		$data['c_contactline'] = $shopinfo['c_contactline'];
		$data['c_bankname'] = $shopinfo['c_bankname'];
		$data['c_bankprovince'] = $this->GetLocalCode($shopinfo['c_bankprovince'],1);
		$data['c_bankcity'] = $this->GetLocalCode($shopinfo['c_bankcity'],2,$this->GetIdbyCode($data['c_bankprovince'],$shopinfo['c_bankprovince']));
		$data['c_idcardtype'] = $shopinfo['c_idcardtype'];
		$data['c_idcard'] = $shopinfo['c_idcardinfo'];
		$data['c_bankaddress'] = $shopinfo['c_address'];
		$data['c_banktel'] = $shopinfo['c_banktel'];
		$data['c_updatetime'] = gdtime();
		$data['c_status'] = $status;

		$result = $this->CheckUpayInfo($data);
		if ($result['code'] != 0) {
			return $result;
		}

		if ($upayinfo) {
			$result = M('Merchant')->where($uw)->save($data);
		} else {
			$result = M('Merchant')->add($data);
		}

		if (!$result) {
			return Message(3000,'资料同步失败');
		}

		return Message(0,'资料同步成功');
	}

	/**
	 * 根据编码获取id 
	 * @param code
	 */
	function GetIdbyCode($code,$region_name)
	{
		$where['c_upaycode'] = $code;
		$where['region_name'] = $region_name;
		$data = M('Region')->where($where)->find();
		return $data['region_id'];
	}

	/**
	 * 查询商家友收宝商户号
	 * @param ucode,type
	 */
	function GetShopMchid($ucode,$type)
	{
		return Message(3000,'商户号不存在');
		$where['c_status'] = 1;
		$where['c_ucode'] = $ucode;
		$where['c_type'] = 2;
		if ($type == 1) {
			$where['c_type'] = 1;
		}

		$data = M('Merchant_id')->where($where)->find();
		if (!$data || empty($data['c_merchantid'])) {
			return Message(3000,'商户号不存在');
		}

		return MessageInfo(0,'查询成功',$data);
	}

	//查询友收宝资料是否存在
	function FindUpayInfo($parr)
	{
		$where['c_ucode'] = $parr['ucode'];
		$upayinfo = M('Merchant')->where($where)->find();
		if (!$upayinfo) {
			return Message(3000,'资料不存在');
		}

		return MessageInfo(0,'资料已存在',$upayinfo);
	}

	/**
	 * 检查友收宝资料是否完善
	 * @param ucode
	 */
	function CheckUpayInfo($upayinfo)
	{
		$infokey = array_keys($upayinfo);
		$infovalue = array_values($upayinfo);
		$sign = 1;   				//已完善
		foreach ($infokey as $k => $v) {
			if ($v != 'c_bankaddress' && $v != 'c_tel' && $v != 'c_remark' && $v != 'c_licensephoto' 
				&& $v != 'c_checknum' && $v != 'c_status') {
				if (empty($infovalue[$k])) {
					$sign = 0;
				}
			}
		}

		// if ($sign == 1) {
			return Message(0,'资料已完善');
		// } else {
		// 	return Message(3001,'资料不完善');
		// }
	}

	/**
	 * 根据位置获取编码
	 * @param name,type(1省，2市，3区)
	 */
	function GetLocalCode($name,$type,$parentid)
	{
		$where['region_name'] = $name;
		$where['region_type'] = $type;
		if (!empty($parentid)) {
			$where['parent_id'] = $parentid;
		}
		$data = M('Region')->where($where)->find();
		return $data['c_upaycode'];
	}

	/**
	 * 查询商家行业编码
	 * @param ucode
	 */
	function GetIndustryCode($ucode)
	{
		$join = 'left join t_users as b on b.c_shoptrade=a.c_id';
		$where['b.c_ucode'] = $ucode;
		$field = 'a.c_industrid';
		$data = M('Shop_industry as a')->join($join)->where($where)->field($field)->find();
		return $data['c_industrid'];
	}

	/**
	 * 根据银行名获取银行编码
	 * @param [type] $name [description]
	 */
	function GetBankCode($name)
	{
		$where['c_name'] = $name;
		$data = M('Merchant_bank')->where($where)->find();
		return $data['c_code'];
	}

    function GetBanks()
    {
        $list = M('Merchant_bank')->select();

        return MessageInfo(0,'查询成功',$list);
    }


    function GetBranchs($parr)
    {
        $where['c_bankname'] = $parr['bankname'];
        $list = M('Merchant_branch')->where($where)->select();
        return MessageInfo(0,'查询成功',$list);
    }

	/**
	 * 获取银行数据列表(15条)
	 * @param name
	 */
	function GetBankList($parr)
	{
		$name = $parr['name'];
		if (!empty($name)) {
			$where[] = array("c_name like '%$name%' or c_code like '%$name%'");
		}
		$list = M('Merchant_bank')->where($where)->limit(15)->select();

		return MessageInfo(0,'查询成功',$list);
	}

	/**
	 * 获取联行号列表(100条)
	 * @param name,bankname
	 */
	function GetBranchList($parr)
	{
		$name = $parr['name'];
		if (!empty($name)) {
			$where[] = array("c_name like '%$name%' or c_code like '%$name%' or c_provincename like '%$name%' or c_cityname like '%$name%'");
		}
		$where['c_bankname'] = $parr['bankname'];
		$list = M('Merchant_branch')->where($where)->limit(100)->select();

		return MessageInfo(0,'查询成功',$list);
	}

	/**
	 * 友收宝提交资料入口
	 * @param ucode
	 */
	function PostAddmerchant($parr)
	{
		$where['c_ucode'] = $parr['ucode'];
		$upayinfo = M('Merchant')->where($where)->find();
		if (!$upayinfo) {
			return Message(3000,'资料不存在');
		}

		//提交商户普通费率资料
		$upayinfo['c_outmerchantid'] = 'mp'.time();		//生成平台商户号
		$result = $this->apiAddmerchant($upayinfo,C('UPAYRATE'),2);
		if ($result['code'] != 0) {
			return $result;
		}

		//获取平台抽成比例		
		$result = $this->GetIndustryInfo($parr['ucode']);
		if (!$result) {
			return Message(3001,'行业信息不存在');
		}

		if ($result['data']['c_isfixed1'] == 1) {
			$billRate = $result['c_scanpay_shoprake']*10;
		} else {
			$billRate = $result['c_online_shoprake']*10;
		}
		

		//提交随机商户
		$upayinfo['c_outmerchantid'] = 'mk'.time();		//生成平台商户号
		$result = $this->apiAddmerchant($upayinfo,$billRate,1);
		if ($result['code'] != 0) {
			return $result;
		}

		return Message(0,'提交成功');
	}

	/**
	 * 获取商家所属行信息
	 * @param string $value [description]
	 */
	function GetIndustryInfo($ucode)
	{
		$join = 'left join t_users as b on b.c_shoptrade=a.c_id';
		$where['b.c_ucode'] = $ucode;
		$field = 'a.*,b.c_isfixed1';
		$data = M('Shop_industry as a')->join($join)->where($where)->field($field)->find();
		return $data;
	}

	/**
	 * 友收宝商户进件资料提交
	 * @param billRate(费率)
	 * 
	 */
    function apiAddmerchant($parr,$billRate,$type)
	{
		$url = 'https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiAddmerchant';
		$result = $this->httpRequst($url, $this->MakeAskData($parr));
		if ($result['status'] != '0' || empty($result)) {
			return Message(3000,$result['msg']);
		}
		$merchantId = $result['merchantId'];

		//增加资料提交次数
		$mwhere['c_id'] = $parr['c_id'];
		$result = M('Merchant')->where($mwhere)->setInc('c_checknum',1);
		if (!$result) {
			return Message(3001,'增加提交次数失败');
		}

		//提交费率
		$parr['merchantId'] = $merchantId;
		$result = $this->apiPayconf($parr,$billRate,$type);
		if ($result['code'] != 0) {
			return Message(3002,'提交费率失败');
		}

		$data['merchantId'] = $merchantId;
		return MessageInfo(0,'商户资料进件成功',$data);
	}

	//curl、post提交数据
	public function httpRequst($urls,$datas){	//curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 200);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $urls);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type'=>'multipart/form-data'));
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		$result = curl_exec($ch);

		if ($result == NULL) {
			$result['status'] = '400';
			$result['msg'] = curl_error($ch);
			curl_close($ch);
			return $result;
		}
		curl_close($ch);
		$resultdata = json_decode($result);
        return objarray_to_array($resultdata);
	}


	/**
	 * 构造友收宝商户进件post请求数据
	 * @param string $value [description]
	 */
	function MakeAskData($parr)
	{
		//channel部分
		$data['channel']['auth'] = '0';                     //0友收宝隧道进件，1自己的隧道进件
		$data['channel']['channelId'] = '1801337226';		//友收宝隧道id
		$data['channel']['partner'] = C('UPAYPARTNER');   	//中信渠道号
		$data['channel']['key'] = C('UPAYKEY');			    //渠道秘钥

		//商户基本信息部分
		$data['merchant']['merchantName'] = $parr['c_merchantname'];    	//商户名称
		$data['merchant']['outMerchantId'] = $parr['c_outmerchantid'];  	//外商户号
		$data['merchant']['feeType'] = $parr['c_feetype'];			    	//币种
		$data['merchant']['mchDealType'] = $parr['c_mchdealtype'];	    	//商户经营类型	
		$data['merchant']['remark'] = $parr['c_remark'];				    //备注(非必传)

		//商户详情信息部分
		$data['merchant']['merchantDetail']['merchantShortName'] = $parr['c_merchantshortname'];	//商户简称
		$data['merchant']['merchantDetail']['industrId'] = $parr['c_industrid'];					//行业类别
		$data['merchant']['merchantDetail']['province'] = $parr['c_province'];						//省份
		$data['merchant']['merchantDetail']['city'] = $parr['c_city'];								//城市
		$data['merchant']['merchantDetail']['county'] = $parr['c_county'];							//区（县）
		$data['merchant']['merchantDetail']['address'] = $parr['c_address'];						//地址
		$data['merchant']['merchantDetail']['tel'] = $parr['c_tel'];								//电话(非必传)
		$data['merchant']['merchantDetail']['email'] = $parr['c_email'];							//邮箱	
		$data['merchant']['merchantDetail']['legalPerson'] = $parr['c_legalperson'];				//企业法人
		$data['merchant']['merchantDetail']['customerPhone'] = $parr['c_customerphone'];			//客服电话
		$data['merchant']['merchantDetail']['principal'] = $parr['c_principal'];					//负责人
		$data['merchant']['merchantDetail']['principalMobile'] = $parr['c_principalmobile'];		//负责人手机号
		$data['merchant']['merchantDetail']['idCode'] = $parr['c_idcode'];							//负责人身份证
		$data['merchant']['merchantDetail']['indentityPhoto'] = $parr['c_indentityphoto'];			//负责人身份证图片
		$licensephoto = str_replace(GetHost().'/','', $parr['c_licensephoto']);
		if (!empty($licensephoto)) {
			$data['merchant']['merchantDetail']['licensePhoto'] = $parr['c_licensephoto'];				//营业执照(非必传)
		}
		

		//银行账号信息部分
		$data['merchant']['bankAccount']['accountCode'] = $parr['c_accountcode'];		         //银行卡号
		$data['merchant']['bankAccount']['bankId'] = $parr['c_bankid'];							//开户银行	
		$data['merchant']['bankAccount']['accountName'] = $parr['c_accountname'];				//开户人
		$data['merchant']['bankAccount']['accountType'] = $parr['c_accounttype'];				//帐户类型(1企业，2个人)
		$data['merchant']['bankAccount']['contactLine'] = $parr['c_contactline'];				//联行号	
		$data['merchant']['bankAccount']['bankName'] = $parr['c_bankname'];						//开户支行名称
		$data['merchant']['bankAccount']['province'] = $parr['c_bankprovince'];					//开户支行所在省	
		$data['merchant']['bankAccount']['city'] = $parr['c_bankcity'];							//开户支行所在市
		$data['merchant']['bankAccount']['idCardType'] = $parr['c_idcardtype'];					//持卡人证件类型(企业非必传)
		$data['merchant']['bankAccount']['idCard'] = $parr['c_idcard'];							//持卡人证件号(企业非必传)
		$data['merchant']['bankAccount']['address'] = $parr['c_bankaddress'];					//持卡人地址(非必传)
		$data['merchant']['bankAccount']['tel'] = $parr['c_banktel'];							//持卡人手机号码

		return json_encode($data);
	}

	/**
	 * 友收宝商户费率提交
	 * @param merchantId,billRate
	 * 
	 */
    function apiPayconf($parr,$billRate,$type)
	{	
		//支付类型ID  前三个微信，后两个支付宝
		$payTypeIdArr = array('542','543','544','10000181','10000180');
		$url = 'https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPayconf';

		foreach ($payTypeIdArr as $key => $value) {
			$askdata['merchantId'] = $parr['merchantId'];			//商户号
			$askdata['payTypeId'] = $value;							//支付类型ID
			$askdata['billRate'] = $billRate;						//结算费率
			$askjson = json_encode($askdata);
			$result = $this->httpRequst($url, $askjson);
			$status = 0;
			if ($result['status'] != '0' || empty($result)) {
				$status = 2;
			}

			//写入提交记录
			$billdata['c_ucode'] = $parr['c_ucode'];
			$billdata['c_appid'] = C('APPID');
			$billdata['c_merchantid'] = $parr['merchantId'];
			$billdata['c_outmerchantid'] = $parr['c_outmerchantid'];
			$billdata['c_merchantname'] = $parr['c_merchantname'];
			$billdata['c_merchantshortname'] = $parr['c_merchantshortname'];
			$billdata['c_paytypeid'] = $value;
			$billdata['c_billrate'] = $billRate;
			$billdata['c_type'] = $type;
			$billdata['c_status'] = $status;
			$billdata['c_addtime'] = gdtime();
			$billdata['c_updatetime'] = gdtime();
			$result = M('Merchant_id')->add($billdata);
			if (!$result) {
				return Message(3001,'生成费率提交记录失败');
			}
		}

		return Message(0,'费率设置成功');
	}

	//测试提交app支付费率
	function csapiPayconf($merchantId,$billRate,$payTypeIdArr = array())
	{	
		//支付类型ID  前三个微信，后两个支付宝
		// $payTypeIdArr = array('542','543','544','10000181','10000180');
		$url = 'https://www.uuplus.cc/index.php?g=Wap&m=BankPay&a=apiPayconf';

		foreach ($payTypeIdArr as $key => $value) {
			$askdata['merchantId'] = $merchantId;			//商户号
			$askdata['payTypeId'] = $value;					//支付类型ID
			$askdata['billRate'] = $billRate;						//结算费率
			$askjson = json_encode($askdata);
			$result = $this->httpRequst($url, $askjson);
			dump($result);
		}

		return Message(0,'费率设置成功');
	}

}
