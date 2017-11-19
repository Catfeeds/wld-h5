<?php
namespace Shop\Controller;
use Think\Controller;
/**
 *  连锁店管理
 */
class MultshopController extends BaseController{	
	
	// 首页
    public function index()
    {
    	$this->display();
    }

    //获取连锁店列表
    public function ChainList()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $parr['ucode'] = session('_SHOP_UCODE');
        $result = IGD('Chain','Store')->ChainList($parr);
        $this->ajaxReturn($result);
    }
	
	/*添加连锁店*/
	public function addshop(){
    	$this->display();
	}
	
    //验证用户信息
    public function UserInfo()
    {
        $phone = I('phone');
        $result = IGD('Chain','Store')->UserInfo($phone);
        $this->ajaxReturn($result);
    }

	/*获取验证码并验证手机号*/
 	public function sendVerify(){
 		if (IS_AJAX) {
            $parr['telephone'] = I('phone');
            $regnum = IGD('Common', 'Redis')->Rediesgetucode($parr['telephone']);
            if (!$regnum) {
                // 生成6位数验证码
                $regnum = rand(100000, 999999);
            }

            $parr['type'] = I('type');
            $parr['userid'] = C('TEl_USER');
            $parr['account'] = C('TEl_ACCESS');
            $parr['password'] = C('TEl_PASSWORD');
            $parr['content'] = "【微领地小蜜】尊敬的会员您好，验证码为：".$regnum."有效期120s，为保证您的账号安全，请勿外泄。感谢您的申请！";
            $register = D('Login', 'Service');
            $returndata = $register->SendVerify($parr);
            if ($returndata['code'] == 0) {
                IGD('Common', 'Redis')->RediesStoreSram($parr['telephone'], $regnum, 3600);
            }
            $this->ajaxReturn($returndata);
        } else {
            $this->ajaxReturn(Message(1000,'非法请求'));
        }
 	}

    public function Confirmsubmit()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $verifyid = IGD('Common', 'Redis')->Rediesgetucode($data['phone']);
        if ($verifyid == '') {
            $this->ajaxReturn(Message(1000, "验证码失效，请重新获取验证码！"));
        }

        $reverify =  $data['verify'];
        if ($reverify != $verifyid) {
            $this->ajaxReturn(Message(1001, "验证码错误！"));
        }

        $parr['phone'] =  $data['phone'];
        $parr['pwd'] =  $data['pwd'];
        $parr['ucode'] = session('_SHOP_UCODE');
        $parr['shopcode'] = $data['shopcode'];
        $result = IGD('Chain','Store')->Confirmsubmit($parr);
        $this->ajaxReturn($result);
    }
 	
 	/*第一步资料页面*/
 	public function step_1(){
        $this->fucode = I('fucode');
        //查询友收宝资料是否存在
        $parr['ucode'] = $this->fucode;
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];

        $result = D('Agent','Service')->GetShopInfo($parr);

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
 	
 	/*第二步资料页面*/
 	public function step_2(){
		$this->fucode = I('fucode');
        //查询友收宝资料是否存在
        $parr['ucode'] = $this->fucode;
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];

		$result = D('Agent','Service')->GetShopInfo($parr);
		$this->assign('vo',$result['data']);
		
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
        $this->ctype = I('ctype');
         		
    	$this->display();
 	} 
 	
 	
 	/*第三步资料页面*/
 	public function step_3(){		
		$this->fucode = I('fucode');
        //查询友收宝资料是否存在
        $parr['ucode'] = $this->fucode;
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];

        $result = D('Agent','Service')->GetShopInfo($parr);
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


    //保存资料第一步
    public function saveInfo1()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = $data['fucode'];
        $parr['merchantname'] = $data['merchantname'];
        $parr['merchantshortname'] = $data['merchantshortname'];
        $parr['mchdealtype'] = $data['mchdealtype'];
        $parr['type'] = $data['ctype'];
        $parr['company'] = $data['company'];
        $parr['address'] = $data['address'];
        $parr['postcode'] = $data['postcode'];
        $parr['charter'] = $data['charter'];
        $parr['name'] = $data['name'];
        $parr['phone'] = $data['phone'];
        $parr['home_tel'] = $data['home_tel'];
        $parr['legalperson'] = $data['legalperson'];
        $parr['idcard'] = $data['idcard'];
        $parr['feetype'] = $data['feetype'];
        $parr['qq'] = $data['qq'];
        $parr['email'] = $data['email'];
        $parr['tid'] = $data['tid'];
        $parr['province'] = $data['provincename'];
        $parr['city'] = $data['cityname'];
        $parr['district'] = $data['districtname'];
        $parr['address1'] = $data['address1'];
        $parr['lng'] = $data['lng'];
        $parr['lat'] = $data['lat'];
        $result = IGD('Getbusiness','User')->SaveAgentInfo3($parr);
        $this->ajaxReturn($result);
    }  

    //保存资料第二步
    public function saveInfo2()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = $data['fucode'];
        $parr['fee_bank'] = $data['fee_bank'];
        $parr['fee_branch'] = $data['fee_branch'];
        $parr['fee_cardnum'] = $data['fee_cardnum'];
        $parr['fee_name'] = $data['fee_name'];
        $parr['fee_alipay'] = $data['fee_alipay'];
        $parr['fee_weixin'] = $data['fee_weixin'];
        $parr['accounttype'] = $data['accounttype'];
        $parr['contactline'] = $data['contactline'];
        $parr['bankname'] = $data['bankname'];
        $parr['bankprovince'] = $data['provincename'];
        $parr['bankcity'] = $data['cityname'];
        $parr['idcardtype'] = $data['idcardtype'];
        $parr['idcardinfo'] = $data['idcardinfo'];
        $parr['banktel'] = $data['banktel'];  
        $result = IGD('Getbusiness','User')->SaveAgentInfo4($parr);
        $this->ajaxReturn($result);
    }

    //保存资料第三步
    public function saveInfo3()
    {   
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        //查询商家资料信息
        $parr['ucode'] = $data['fucode'];
        $result = D('Agent','Service')->GetShopInfo($parr);
        $vodata = $result['data'];

        $parr['idcard_img'] = $data['idcard_img'];
        $parr['idcard_img1'] = $data['idcard_img1'];
        $parr['charter_img'] = $data['charter_img'];
        $parr['company_sign'] = $data['company_sign'];

        if($parr['idcard_img'] != $vodata['c_idcard_img']){
            $parr['idcard_img'] = copyFileToDIr($parr['idcard_img'],'agent',1)['data'];
        }
        if($parr['idcard_img1'] != $vodata['c_idcard_img1']){
            $parr['idcard_img1'] = copyFileToDIr($parr['idcard_img1'],'agent',1)['data'];
        }
        if (empty($parr['idcard_img']) || empty($parr['idcard_img1'])) {
            $this->ajaxReturn(Message(3001,'请上传身份证图'));
        }

        if ($vodata['c_type'] == 2) {
            if($parr['charter_img'] != $vodata['c_charter_img']){
                $parr['charter_img'] = copyFileToDIr($parr['charter_img'],'agent',1)['data'];
            }
            if($parr['company_sign'] != $vodata['c_company_sign']){
                $parr['company_sign'] = copyFileToDIr($parr['company_sign'],'agent',1)['data'];
            }
            if (empty($parr['charter_img']) || empty($parr['company_sign'])) {
                $this->ajaxReturn(Message(3002,'线下商家请上传营业执照与企业标识图'));
            }
        }
        
        $result = IGD('Getbusiness','User')->SaveAgentInfo5($parr);
        $this->ajaxReturn($result);
    }
}