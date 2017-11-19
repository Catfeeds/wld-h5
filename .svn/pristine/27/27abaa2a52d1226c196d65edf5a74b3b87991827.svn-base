<?php
namespace Shop\Controller;
use Think\Controller;
class PersonalController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

   	//资料页面
	public function index()
	{
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];

		$result = IGD('Myagent','Agent')->GetShopInfo($parr);
		$newarr = explode($result['data']['c_county'],$result['data']['address1']);
		$result['data']['address1'] = $newarr[1];
        /*拆分地址address1：分割省，市，区*/
        $prove = explode("省", $result['data']['address1']);
        $citye = explode("市", $prove[1]);
        $district = str_replace($prove[0].'省'.$citye[0].'市', '', $result['data']['address1']);
        $whereadd['b.region_name'] = array('like','%'.$citye[0].'%');
        $whereadd['b.region_type'] = 2;
        $whereadd['a.region_type'] = 3;
        $join = 'left join t_region as b on a.parent_id=b.region_id';
        $list = M('Region as a')->join($join)->where($whereadd)->field('a.*')->select();
        foreach ($list as $key => $value) {
            if (strpos($district,$value['region_name']) !== false) {
                $newdistrict = $value['region_name'];
            }
        }
        $xsaddress = str_replace($newdistrict, '', $district);

        /*判断：如果是获取的用户的名称则清空，输入真实姓名*/
        $needle = "小蜜";
        $namestr = explode($needle,$result['data']['c_name']);
        if(count($namestr)>1){
            $result['data']['c_name'] = "";
        }else{
            $result['data']['c_name'] = $result['data']['c_name'];
        }

		//$this->assign('vo',$result['data']);
		//$this->assign('prove',$prove[0]);/*省*/
		//$this->assign('citye',$citye[0]);/*市*/
		//$this->assign('newdistrict',$newdistrict);/*区*/
		//$this->assign('xsaddress',$xsaddress);/*详细地址*/
        //新版修改        
        $this->assign('vo',$result['data']);
        $this->assign('prove',$result['data']['c_province']);/*省*/
        $this->assign('citye',$result['data']['c_city']);/*市*/
        $this->assign('newdistrict',$result['data']['c_county']);/*区*/
        $this->assign('xsaddress',$xsaddress);/*详细地址*/

        /*获取行业*/
        if ($result['data']['tradepid'] == 0) {
            $id = $result['data']['c_shoptrade'];
        }
        $induresult = D('Common','Service')->GetIndustry($id);
        $this->industry = $induresult['data'];

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = D('Common', 'Service');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;

        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }

		$this->display();
	}

    //获取银行列表
    public function GetBankList()
    {
        $parr['name'] = I('name');
        $result = IGD('Upay','Scanpay')->GetBankList($parr);
        $this->ajaxReturn($result);
    }

    //获取支行列表
    public function GetBranchList()
    {
        $parr['bankname'] = I('bankname');
        $parr['name'] = I('name');
        $result = IGD('Upay','Scanpay')->GetBranchList($parr);
        $this->ajaxReturn($result);
    }

    // 获取省市区数据列表
    public function getRegion() {
        $parr['parentid'] = I('parentid');
        $parr['regiontype'] = I('regiontype');
        $region = D('Common', 'Service');
        $result = $region->GetAddress($parr);
        $this->ajaxReturn($result);
    }
    
	// 修改密码
    public function updatepwd()
    {
    	if (IS_POST) {
    		$db = M('Users');
			if(!empty($_POST)){
				$pbusinesscode['c_ucode'] = session('_SHOP_UCODE');
				$loginpwd = $db->where($pbusinesscode)->getField('c_password');
				$pwd = encrypt($_POST['pwd_old'],C('ENCRYPT_KEY'));
				if($pwd != $loginpwd){
					 $this->error('旧密码不正确');die;
				}elseif(empty($_POST['pwd_new'])){
					$this->error('新密码不能为空');die;
				}elseif(md5($_POST['pwd_new']) != md5($_POST['loginpwd'])){
					$this->error('新密码与确认新密码不相同');die;
				}else{
					$data['c_password'] = encrypt($_POST['loginpwd'],C('ENCRYPT_KEY'));
					$result = $db->where($pbusinesscode)->save($data);
					if(!$result){
						$this->error('修改失败');
					}else{
						$this->success('修改成功！。。。请重新登录');
						session('_SHOP_UCODE',null);
						session('_ADMIN_NAME',null);
						echo '<script language="javascript">top.location="'.U('Login/index').'";</script>';
					}

				}
		 	}
    	}
    	$this->display();
    }

    //  我的上级
    public function shopinfo()
    {
        $parr1['ucode'] = session('_SHOP_UCODE');
        $result = D('Common','Service')->GetupsetInfo($parr1);
        $this->shopinfo = $result['data'];
        $this->show();
    }
   
    /*商家资料01*/
    public function info_1(){
		$parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];
        
		$result = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('vo',$result['data']);        
         	
   		$this->show();
    }
    
    /*商家资料02*/
    public function info_2(){	
    	$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];

		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('data',$result_2['data']); 
			
		/*判断商家属性,0为线上，1为线下,商家资质：1个人，2企业*/  
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }   
       	
       	$result_3 = D('Common','Service')->GetIndustry();
        $this->industry = $result_3['data'];
        
   		$this->show();
    }
    
    /*商家资料03*/
    public function info_3(){    	
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$newarr = explode($result_2['data']['c_county'],$result_2['data']['address1']);
		$result_2['data']['address1'] = $newarr[1];
		$this->assign('vo',$result_2['data']);

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = D('Common', 'Service');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;

        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }
        
   		$this->show();
    }
    
    /*商家资料04*/
    public function info_4(){
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('vo',$result_2['data']);  
        
        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }
            	
   		$this->show();
    }
    
    /*商家资料05*/
    public function info_5(){
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$newarr = explode($result_2['data']['c_county'],$result_2['data']['address1']);		
		$result_2['data']['address1'] = $newarr[count($newarr)-1];
		$this->assign('vo',$result_2['data']);  

        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }
              	
   		$this->show();
    }
    
    /*商家资料06*/
    public function info_6(){
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('vo',$result_2['data']);  
        
        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = D('Common', 'Service');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;
        
        
        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }

        /*账户类型*/
        $list = M('User_banktype')->select();
        $msg['code'] = 0;
        $msg['msg'] ='查询成功';
        $msg['data'] = $list;
        $this->accountlist = $msg['data'];

   		$this->show();
    }
        
    /*商家资料07*/
    public function info_7(){
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('vo',$result_2['data']);  
        
        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }    	
   		$this->show();
    }       
    
	/*上传证件图*/
	public function info_8(){
		
		$parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];
        
		$result = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('vo',$result['data']);
		
		/*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        $this->ctype = I('ctype');
		$this->display();
	}
    
    
    /*审核状态*/
    public function checkinfo(){
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('data',$result_2['data']);  
        
        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }    	
   		$this->show();
    }
	/*商家查看资料*/
    public function info_9(){
        //查询友收宝资料是否存在
		$parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
		$result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('data',$result_2['data']);  
        
        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }    	
   		$this->show();  
    }

    //过滤空格符
    public function strtrim1($str)
    {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }

    /**
     * 添加与修改个人资料第一步 (商家类型、注册类型)
      * @param ucode,type,isfixed
     */
    public function SetInfo1(){
        $parr['ucode'] = session('_SHOP_UCODE');
        $parr['isfixed'] = I('isfixed');
        $parr['type'] = I('type');
        if (empty($parr['type']) || $parr['isfixed']=="") {
            $this->ajaxReturn(Message(1002, "请完善资料！"));
        }
        $result = IGD('Setinfo', 'Agent')->newData1($parr);
        $this->ajaxReturn($result);    	
    }
    
    /**
     * 添加与修改个人资料第二步 (行业信息)
     * @param ucode,tid(商家行业id)
     */
    public function SetInfo2(){
        $parr['ucode'] = session('_SHOP_UCODE');
        $parr['tid'] = I('tid');
        if (empty($parr['tid'])) {
            $this->ajaxReturn(Message(1007,'请选择所属行业'));
        }
        $result = IGD('Setinfo','Agent')->newData2($parr);
        $this->ajaxReturn(Message(0,'保存成功'));    	
    }
    
    /**
     * 添加与修改个人资料第三步
      * @param 
     *  ucode,name,merchantname,idcardinfo,idcardstarttime,idcardendtime,phone,province,address,city,address,district,charter
     *  
     */
    public function SetInfo3(){    	
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('_SHOP_UCODE');
        $parr['name'] = strtrim1($data['name']);
        $parr['merchantname'] = strtrim1($data['merchantname']);
        $parr['idcardinfo'] = $data['idcardinfo'];
        $parr['idcardstarttime'] = $data['idcardstarttime'];
        $parr['idcardendtime'] = $data['idcardendtime'];
        $parr['phone'] = $data['phone'];
        $parr['province'] = $data['provincename'];
        $parr['city'] = $data['cityname'];
        $parr['district'] = $data['districtname'];
        $parr['address'] = $data['address'];
        $parr['charter'] = strtrim1($data['charter']);
        $result = IGD('Setinfo','Agent')->newData3($parr);
        $this->ajaxReturn($result);
    }
    
    /**
     * 添加与修改个人资料第四步
      * @param 
     *  ucode,legalperson,legalphone,charter,fee_cardnum,fee_name,fee_bank,bankname
     *  
     */
    public function SetInfo4(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('_SHOP_UCODE');
        $parr['legalperson'] = strtrim1($data['legalperson']);
        $parr['legalphone'] = $data['legalphone'];
        $parr['charter'] = $data['charter'];
        $parr['fee_cardnum'] = strtrim1($data['fee_cardnum']);
        $parr['fee_name'] = strtrim1($data['fee_name']);
        $parr['fee_bank'] = $data['fee_bank'];
        $parr['bankname'] = $data['bankname'];
        $parr['bankprovince'] = $data['provincename'];
        $parr['bankcity'] = $data['cityname'];
        $result = IGD('Setinfo','Agent')->newData4($parr);
        $this->ajaxReturn($result);
    }
      	/**
     * 添加与修改个人资料第五步
      * @param 
     *  ucode,legalperson,legalsex,legalcountry
     *  idcardtype,idcardinfo,legalcardstarttime,legalcardendtime,legalphone
     */
    public function SetInfo5(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        
        $parr['ucode'] = session('_SHOP_UCODE');
    	$parr['legalperson'] = $data['legalperson'];
        $parr['legalsex'] = $data['legalsex'];
        $parr['legalcountry'] = $data['legalcountry'];
        $parr['idcardtype'] = $data['idcardtype'];
        $parr['idcardinfo'] = $data['idcardinfo'];
        $parr['legalcardstarttime'] = $data['legalcardstarttime'];
        $parr['legalcardendtime'] = $data['legalcardendtime'];
        $parr['legalphone'] = $data['legalphone'];

        $result = IGD('Setinfo','Agent')->SetInfo51($parr);
        $this->ajaxReturn($result);
    }
    
    /**
     * 添加与修改个人资料第六步
      * @param ucode,fee_bank,fee_name,fee_cardnum,accounttype,bankprovince,bankcity
      * bankname,balancecardtype,balancenum
     */
    public function SetInfo6(){       	
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('_SHOP_UCODE');
        $parr['fee_bank'] = $data['fee_bank'];
        $parr['fee_name'] = $data['fee_name'];
        $parr['fee_cardnum'] = $data['fee_cardnum'];
        $parr['accounttype'] = $data['accounttype'];
        $parr['bankprovince'] = $data['provincename'];
        $parr['bankcity'] = $data['cityname'];
        $parr['bankname'] = $data['bankname'];
        $parr['balancecardtype'] = $data['balancecardtype'];
        $parr['balancenum'] = $data['balancenum'];

        $result = IGD('Setinfo','Agent')->SetInfo61($parr);
        $this->ajaxReturn($result);
    } 
    /**
     * 添加与修改个人资料第七步(上传图片)
      * @param ucode,idcard_img,idcard_img1,bankcardimg,bankcardimg1,charter_img,contractimg
      *
     */
    public function SetInfo7(){
        //查询商家资料信息
        $parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('Getbusiness','User')->GetShopInfo($parr);
        $vodata = $result['data'];

        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['idcardimg'] = $data['idcardimg'];
        $parr['idcardimg1'] = $data['idcardimg1'];
        $parr['bankcardimg'] = $data['bankcardimg'];
        $parr['bankcardimg1'] = $data['bankcardimg1'];
        $parr['charterimg'] = $data['charter_img'];
        $parr['charterpubimg'] = $data['charterpub_img'];

        $ctype = $vodata['c_type'];

        /*身份证图片*/
        if($parr['idcardimg'] != $vodata['c_idcard_img']){
            $parr['idcardimg'] = copyFileToDIr($parr['idcardimg'],'agent',1)['data'];
        }
        if($parr['idcardimg1'] != $vodata['c_idcard_img1']){
            $parr['idcardimg1'] = copyFileToDIr($parr['idcardimg1'],'agent',1)['data'];
        }
        if (empty($parr['idcardimg']) || empty($parr['idcardimg1'])) {
            $this->ajaxReturn(Message(3000,'请上传身份证图'));
        }
        if($ctype==1 || $ctype==3){
            /*银行卡图片*/
            if($parr['bankcardimg'] != $vodata['c_bankcardimg']){
                $parr['bankcardimg'] = copyFileToDIr($parr['bankcardimg'],'agent',1)['data'];
            }
            if($parr['bankcardimg1'] != $vodata['c_bankcardimg1']){
                $parr['bankcardimg1'] = copyFileToDIr($parr['bankcardimg1'],'agent',1)['data'];
            }
            if (empty($parr['bankcardimg']) || empty($parr['bankcardimg1'])) {
                $this->ajaxReturn(Message(3001,'请上传银行卡图'));
            }
        }else if($ctype==2 || $ctype==3){
            /*营业执照*/
            if($parr['charterimg'] != $vodata['c_charter_img']){
                $parr['charterimg'] = copyFileToDIr($parr['charterimg'],'agent',1)['data'];
            }
            if (empty($parr['charterimg'])) {
                $this->ajaxReturn(Message(3002,'请上传营业执照'));
            }
        }else if($ctype==2){
            if($parr['charterpubimg'] != $vodata['c_charterpub_img']){
                $parr['charterpubimg'] = copyFileToDIr($parr['charterpubimg'],'agent',1)['data'];
            }
            if (empty($parr['charterpubimg'])) {
                $this->ajaxReturn(Message(3002,'请上传公户开户许可证'));
            }
        }
        $result = IGD('Setinfo','Agent')->newData5($parr);
        $this->ajaxReturn($result);
    }    
    
    /**
     * 添加与修改个人资料第八步 (上传图片)
      * @param ucode,storeimg,deskimg,storeinsideimg
     */
    public function SetInfo8()
    {   
        //查询商家资料信息
        $parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('Myagent','Agent')->GetShopInfo($parr);
        $vodata = $result['data'];

        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['storeimg'] = $data['storeimg'];
        $parr['deskimg'] = $data['deskimg'];
        $parr['storeinsideimg'] = $data['storeinsideimg'];

        if($parr['storeimg'] != $vodata['c_storeimg']){
            $parr['storeimg'] = copyFileToDIr($parr['storeimg'],'agent',1)['data'];
        }
        if (empty($parr['storeimg'])) {
            $this->ajaxReturn(Message(3001,'请上传门店图'));
        }

        if($parr['deskimg'] != $vodata['c_deskimg']){
            $parr['deskimg'] = copyFileToDIr($parr['deskimg'],'agent',1)['data'];
        }
        if($parr['storeinsideimg'] != $vodata['c_storeinsideimg']){
            $parr['storeinsideimg'] = copyFileToDIr($parr['storeinsideimg'],'agent',1)['data'];
        }
        if (empty($parr['deskimg']) || empty($parr['storeinsideimg'])) {
            $this->ajaxReturn(Message(3002,'请上传收银台图与店内环境图'));
        }
        
        $result = IGD('Setinfo','Agent')->SetInfo81($parr);
        $this->ajaxReturn($result);
    }  

    /**3.0.4商家资料进件**/

    public function sub4_1(){
        $ucode = session('_SHOP_UCODE');
        $parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];

        $result = IGD('Myagent','Agent')->GetShopInfo($parr);
        $this->assign('vo',$result['data']);

        /*user信息*/
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $result['data'];

        /*新老用户补全资料*/
        $parr['ucode'] = $ucode;  //商户ucode
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        $this->show();
    }

    public  function  sub4_2(){
        $ucode = session('_SHOP_UCODE');

        $parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];

        $result_2 = IGD('Getbusiness','User')->GetShopInfo($parr);
        $this->assign('data',$result_2['data']);

        /*判断商家属性,0为线上，1为线下,商家资质：1个人，2企业*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }

        $result_3 = D('Common','Service')->GetIndustry();
        $this->industry = $result_3['data'];

        /*user信息*/
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $result['data'];

        /*新老用户补全资料*/
        $parr['ucode'] = $ucode;  //商户ucode
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        $this->show();
    }

    public  function  sub4_3(){
        //查询友收宝资料是否存在
        $ucode = session('_SHOP_UCODE');
        $parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];

        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
        $newarr = explode($result_2['data']['c_county'],$result_2['data']['address1']);
        $result_2['data']['address1'] = $newarr[1];
        $this->assign('vo',$result_2['data']);

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = D('Common', 'Service');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;

        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }

        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }

        /*user信息*/
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $result['data'];

        /*新老用户补全资料*/
        $parr['ucode'] = session('_SHOP_UCODE');  //商户ucode
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        $this->show();
    }

    public  function  sub4_4(){
        //查询友收宝资料是否存在
        $ucode = session('_SHOP_UCODE');
        $parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];

        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
        $this->assign('vo',$result_2['data']);

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = D('Common', 'Service');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;


        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }

        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }

        /*user信息*/
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $result['data'];

        /*新老用户补全资料*/
        $parr['ucode'] = session('_SHOP_UCODE');  //商户ucode
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        $this->show();
    }

    public  function  sub4_5(){
        $ucode = session('_SHOP_UCODE');
        $parr['ucode'] = session('_SHOP_UCODE');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];

        $result_2 = IGD('Getbusiness','User')->GetShopInfo($parr);
        $this->assign('vo',$result_2['data']);

        /*判断商家属性,0为线上，1为线下,商家资质：1个人，2企业*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result_2['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        $ctype = I('ctype');
        if($ctype==null){
            $this->ctype = $result_2['data']['c_type'];
        }else{
            $this->ctype = $ctype;
        }

        $result_3 = D('Common','Service')->GetIndustry();
        $this->industry = $result_3['data'];

        /*user信息*/
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->userdata = $result['data'];

        /*新老用户补全资料*/
        $parr['ucode'] = $ucode;  //商户ucode
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        $this->show();
    }
}