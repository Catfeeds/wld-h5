<?php

namespace Home\Controller;

use Base\Controller\BaseController;

/**
 * 代理商模块
 */
class AgentController extends BaseController {

    //查询商家个人资料
    public function GetSelectStoreInfo(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;//session('_SHOP_UCODE');
        $result = IGD('Setinfo','Agent')->GetShopInfo($parr);
        //if(!empty($result['data']['c_county'])){
          //  $newarr = explode($result['data']['c_county'],$result['data']['address1']);
          //  $result['data']['address1'] = $newarr[1];
       // }
        /*判断：如果是获取的用户的名称则清空，输入真实姓名*/
        $needle = "小蜜";
        $namestr = explode($needle,$result['data']['c_name']);
        if(count($namestr)>1){
            $result['data']['c_name'] = "";
        }else{
            $result['data']['c_name'] = $result['data']['c_name'];
        }
        $this->ajaxReturn($result);
    }

    //获取行业列表

    public function GetIndustryList(){

        $result =IGD('Setinfo','Agent')->GetIndustry();
        $this->ajaxReturn($result);
    }

    //获取银行列表
    public function GetBankList(){
        $name =I('name');
        $parr['name']=$name;
        $result =IGD('Upay','Scanpay')->GetBankList($parr);
        $this->ajaxReturn($result);
    }

    //获取支行列表

    public function GetBankBranchList(){
        $name =I('name');
        $bankname =I('bankname');
        $parr['name'] =$name;
        $parr['bankname'] =$bankname;
        $result =IGD('Upay','Scanpay')->GetBranchList($parr);
        $this->ajaxReturn($result);
    }

    //商家激活串码

    public function ShopImeiActive(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $parr['incode'] = trim(I('code'));   //串码

        if (empty($parr['ucode']) || $parr['incode']=="") {
            $this->ajaxReturn(Message(1001, "参数缺失！"));
        }
        $result = IGD('Getbusiness', 'User')->BingIncode($parr);

        $this->ajaxReturn($result);

    }

    //获取个人银行卡类型列表

    public function getBanktypeList(){

        $list =M('User_banktype')->select();
        $msg['code'] =0;
        $msg['msg'] ='查询成功';
        $msg['data'] =$list;

        $this->ajaxReturn($msg);

    }

    /**
     * 检测老用户是否完善资料
     * @param ucode
    */
    public function CheckDataComplete(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;  //商户ucode
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->ajaxReturn($result);
    }

    // 银盛进件第1步
    public function newData1(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;  //商户ucode
        $parr['isfixed'] = I('isfixed');
        $parr['type'] = I('type');
        if (empty($parr['type']) || $parr['isfixed']=="") {
            $this->ajaxReturn(Message(1002, "请完善资料！"));
        }
        $result = IGD('Setinfo', 'Agent')->newData1($parr);
        $this->ajaxReturn($result);
    }
    // 银盛进件第2步
    public function newData2(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;  //商户ucode
        $parr['tid'] = I('tid');
        if (empty($parr['tid'])) {
            $this->ajaxReturn(Message(1007,'请选择所属行业'));
        }
        $result = IGD('Setinfo','Agent')->newData2($parr);
        $this->ajaxReturn($result);
    }
    // 银盛进件第3步
    public function newData3(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;  //商户ucode
        $parr['merchantname'] = I('merchantname');
        $parr['name'] = I('name');
        $parr['phone'] =I('phone');
        $parr['idcardinfo'] = I('idcardinfo');
        $parr['idcardstarttime'] = I('idcardstarttime');
        $parr['idcardendtime'] = I('idcardendtime');
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['district'] = I('district');
        $parr['address'] = I('address');
        $parr['charter'] =I('charter');
        $parr['lat'] =I('lat');
        $parr['lng'] =I('lng');
        if(empty($parr['merchantname']) || empty($parr['name'])||empty($parr['idcardinfo'])|| empty($parr['idcardstarttime'])||empty($parr['idcardendtime'])||empty($parr['province']) ||empty($parr['city']) ||empty($parr['district']) ||empty($parr['address'])){
            $this->ajaxReturn(Message(1001,'请完善资料'));
        }
        $result = IGD('Setinfo','Agent')->newData3($parr);
        $this->ajaxReturn($result);
    }
    // 银盛进件第4步
    public function newData4(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;  //商户ucode
        $parr['legalperson'] = I('legalperson');
        $parr['legalphone'] = I('legalphone');
        $parr['charter'] = I('charter');
        $parr['fee_name'] =I('fee_name');
        $parr['fee_cardnum'] =I('fee_cardnum');
        $parr['fee_bank'] =I('fee_bank');
        $parr['bankname'] =I('bankname');
        //$parr['bankcity'] =I('bankcity');
       // $parr['bankprovince'] =I('bankprovince');

        if(empty($parr['fee_name'])|| empty($parr['fee_cardnum'])||empty($parr['fee_bank'])||empty($parr['bankname'])){
            $this->ajaxReturn(Message(1001,'请完善资料'));
        }
        $result = IGD('Setinfo','Agent')->newData4($parr);
        $this->ajaxReturn($result);
    }
    // 银盛进件第5步

    public function newData5(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;  //商户ucode
        $result = uploadimg('shop');
        $imglist = array();
        if ($result['code'] == 0) {
            $imglist = array_values($result['data']);
        }
        if($result['code'] == 1006){
            $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
            $url = GetHost() . '/';
            foreach ($imglist1 as $key => $value) {
                $imglist[] = str_replace($url, "", $value['c_pimgepath']);
            }
        }
        $user =M('Check_shopinfo')->where(array('c_ucode'=>$ucode))->find();
        if($user['c_type']==1){ //个人
            $parr['idcardimg'] = $imglist[0];
            $parr['idcardimg1'] = $imglist[1];
            $parr['bankcardimg'] = $imglist[2];
            $parr['bankcardimg1'] = $imglist[3];
        }elseif($user['c_type']==2){  //企业
            $parr['idcardimg'] = $imglist[0];
            $parr['idcardimg1'] = $imglist[1];
            $parr['charterimg'] = $imglist[2];
            $parr['charterpubimg'] = $imglist[3];
        }elseif($user['c_type']==3){ // 个体户
            $parr['idcardimg'] = $imglist[0];
            $parr['idcardimg1'] = $imglist[1];
            $parr['bankcardimg'] = $imglist[2];
            $parr['bankcardimg1'] = $imglist[3];
            $parr['charterimg'] = $imglist[4];
        }
        $result = IGD('Setinfo','Agent')->newData5($parr);
        $this->ajaxReturn($result);

    }

    /** old
     * 添加与修改个人资料第一步 (商家类型、注册类型)
     * @param ucode,type,isfixed
     */
    public function SetInfo1(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        // $parr['ucode'] = I('ucode');
        $parr['isfixed'] = I('isfixed');
        $parr['type'] = I('type');
        if (empty($parr['type']) || $parr['isfixed']=="") {
            $this->ajaxReturn(Message(1002, "请完善资料！"));
        }
        $result = IGD('Setinfo', 'Agent')->SetInfo1($parr);
        $this->ajaxReturn($result);
    }

    /**old
     * 添加与修改个人资料第二步 (行业信息)
     * @param ucode,tid(商家行业id)
     */
    public function SetInfo2(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        // $parr['ucode'] = I('ucode');
        $parr['tid'] = I('tid');
        if (empty($parr['tid'])) {
            $this->ajaxReturn(Message(1007,'请选择所属行业'));
        }
        $result = IGD('Setinfo','Agent')->SetInfo2($parr);
        $this->ajaxReturn(Message(0,'保存成功'));
    }

    /**old
     * 添加与修改个人资料第三步
     * @param
     *  ucode,merchantname,merchantshortname,mchdealtype(type,company,address,postcode,charter)
     *
     */
    public function SetInfo3(){
//        $attrbul = I('attrbul');
//        $attrbul = str_replace('&quot;', '"', $attrbul);
//        $data = objarray_to_array(json_decode($attrbul));

        // $parr['ucode'] = I('ucode');
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $parr['merchantname'] = I('merchantname');
        $parr['merchantshortname'] = I('merchantshortname');
        $parr['mchdealtype'] = I('mchdealtype');
        $parr['type'] = I('ctype');
        $parr['company'] = I('company');
        $parr['address'] = I('address');
        $parr['postcode'] = I('postcode');
        $parr['charter'] = I('charter');

        $result = IGD('Setinfo','Agent')->SetInfo3($parr);
        $this->ajaxReturn($result);
    }
    /**old
     * 添加与修改个人资料第四步
     * @param
     *  ucode,name,phone,legalperson,idcard,feetype
     *
     */
    public function SetInfo4(){
//        $attrbul = I('attrbul');
//        $attrbul = str_replace('&quot;', '"', $attrbul);
//        $data = objarray_to_array(json_decode($attrbul));

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        // $parr['ucode'] = I('ucode');
        $parr['name'] = I('name');
        $parr['phone'] = I('phone');
        $parr['legalperson'] = I('legalperson');
        $parr['idcard'] = I('idcard');
        $parr['feetype'] = I('feetype');
        $result = IGD('Setinfo','Agent')->SetInfo4($parr);
        $this->ajaxReturn($result);
    }
    /**old
     * 添加与修改个人资料第五步
     * @param
     *  ucode,qq,email,home_tel
     *  lng,lat,address1,province,city,district
     */
    public function SetInfo5(){
//        $attrbul = I('attrbul');
//        $attrbul = str_replace('&quot;', '"', $attrbul);
//        $data = objarray_to_array(json_decode($attrbul));

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        //$parr['ucode'] = I('ucode');
        $parr['qq'] = I('qq');
        $parr['email'] = I('email');
        $parr['home_tel'] = I('home_tel');
        $parr['province'] = I('provincename');
        $parr['city'] = I('cityname');
        $parr['district'] = I('districtname');
        $parr['address1'] = I('provincename').I('cityname').I('districtname').I('address1');
        $parr['lng'] = I('lng');
        $parr['lat'] = I('lat');
        $result = IGD('Setinfo','Agent')->SetInfo5($parr);
        $this->ajaxReturn($result);
    }
    /**old
     * 添加与修改个人资料第六步
     * @param ucode,fee_bank,fee_name,fee_cardnum,accounttype,bankprovince,bankcity
     * bankname,contactline
     */
    public function SetInfo6(){
//        $attrbul = I('attrbul');
//        $attrbul = str_replace('&quot;', '"', $attrbul);
//        $data = objarray_to_array(json_decode($attrbul));

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        //$parr['ucode'] = I('ucode');
        $parr['fee_bank'] = I('fee_bank');
        $parr['fee_name'] = I('fee_name');
        $parr['fee_cardnum'] = I('fee_cardnum');
        $parr['accounttype'] = I('accounttype');
        $parr['bankprovince'] = I('provincename');
        $parr['bankcity'] = I('cityname');
        $parr['bankname'] = I('bankname');
        $parr['contactline'] = I('contactline');
        $result = IGD('Setinfo','Agent')->SetInfo6($parr);
        $this->ajaxReturn($result);
    }
    /**old
     * 添加与修改个人资料第七步
     * @param ucode,idcardtype,idcardinfo,banktel,fee_alipay,fee_weixin
     *
     */
    public function SetInfo7(){
//        $attrbul = I('attrbul');
//        $attrbul = str_replace('&quot;', '"', $attrbul);
//        $data = objarray_to_array(json_decode($attrbul));

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        //$parr['ucode'] = I('ucode');
        $parr['idcardtype'] = I('idcardtype');
        $parr['idcardinfo'] = I('idcardinfo');
        $parr['banktel'] = I('banktel');
        $parr['fee_alipay'] = I('fee_alipay');
        $parr['fee_weixin'] = I('fee_weixin');
        $result = IGD('Setinfo','Agent')->SetInfo7($parr);
        $this->ajaxReturn($result);
    }

    /**old
     * 添加与修改个人资料第八步 (上传图片)
     * @param ucode,idcard_img,idcard_img1(charter_img,company_sign)
     */
    public function SetInfo8()
    {
        //查询商家资料信息
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $result = IGD('Setinfo','Agent')->GetShopInfo($parr);
        $vodata = $result['data'];

        $result = uploadimg('agent');
        $imglist = array();
        if ($result['code'] == 0) {
            $imglist = array_values($result['data']);
        }

        if($result['code'] == 1006){
            $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
            $url = GetHost() . '/';
            foreach ($imglist1 as $key => $value) {
                $imglist[] = str_replace($url, "", $value['c_pimgepath']);
            }
        }
        $parr['idcard_img'] = $imglist[0];
        $parr['idcard_img1'] = $imglist[1];

        if (empty($parr['idcard_img']) || empty($parr['idcard_img1'])) {
            $this->ajaxReturn(Message(3001,'请上传身份证图'));
        }

        if ($vodata['c_type'] == 2) {
            $parr['charter_img'] = $imglist[2];
            $parr['company_sign'] = $imglist[3];
            if (empty($parr['charter_img']) || empty($parr['company_sign'])) {
                $this->ajaxReturn(Message(3002,'线下商家请上传营业执照与企业标识图'));
            }
        }

        $result = IGD('Setinfo','Agent')->SetInfo8($parr);
        $this->ajaxReturn($result);
    }



    /**
     * 添加与修改个人资料第一步 (商家类型、注册类型)
     * @param ucode,type,isfixed
     */
    public function SetInfo11(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $parr['isfixed'] = I('isfixed');
//        $parr['type'] = I('type');
//        if ($parr['isfixed']=="") {
//            $this->ajaxReturn(Message(1002, "请完善资料！"));
//        }
        $result = IGD('Setinfo', 'Agent')->SetInfo11($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 添加与修改个人资料第二步 (行业信息)
     * @param ucode,tid(商家行业id)
     */
    public function SetInfo21(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
       // $parr['ucode'] = I('ucode');
        $parr['tid'] = I('tid');
        if (empty($parr['tid'])) {
            $this->ajaxReturn(Message(1007,'请选择所属行业'));
        }
        $result = IGD('Setinfo','Agent')->SetInfo21($parr);
        $this->ajaxReturn(Message(0,'保存成功'));
    }

    /**
     * 添加与修改个人资料第三步
     * @param
     *  ucode,merchantname,merchantshortname,mchdealtype(type,company,address,postcode,charter)
     *
     */
    public function SetInfo31(){

       // $parr['ucode'] = I('ucode');
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);


        $parr['ucode'] = $ucode;  //商户ucode
        $parr['company'] = I('company');
        $parr['merchantname'] = I('merchantname');
        $parr['merchantshortname'] = I('merchantshortname');
        $parr['storetype'] = I('storetype');
        $parr['province'] =I('province');
        $parr['city'] =I('city');
        $parr['district'] =I('district');
        $parr['address'] = I('address');
        $parr['lng'] = I('lng');
        $parr['lat'] = I('lat');
        if(empty($parr['company'])||empty($parr['merchantname'])||empty($parr['merchantshortname'])||empty($parr['storetype'])||empty($parr['province']) ||empty($parr['city'])||empty($parr['district'])){
            $this->ajaxReturn(Message(1001,'请完善相关参数'));
        }

        $result = IGD('Setinfo','Agent')->SetInfo31($parr);
        $this->ajaxReturn($result);
    }
    /**
     * 添加与修改个人资料第四步
     * @param
     *  ucode,name,phone,legalperson,idcard,feetype
     *
     */
    public function SetInfo41(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $parr['chartertype'] = I('chartertype');
        $parr['charter'] = I('charter');
        $parr['charterstarttime'] = I('charterstarttime');
        $parr['charterendtime'] = I('charterendtime');
        $parr['email'] = I('email');
        $parr['home_tel'] =I('home_tel');

        if(empty($parr['chartertype'])||empty($parr['charter'])||empty($parr['charterstarttime'])||empty($parr['charterendtime'])||empty($parr['email']) ||empty($parr['home_tel'])){
            $this->ajaxReturn(Message(1001,'请完善相关参数'));
        }

        $result = IGD('Setinfo','Agent')->SetInfo41($parr);
        $this->ajaxReturn($result);
    }
    /**
     * 添加与修改个人资料第五步
     * @param
     *  ucode,qq,email,home_tel
     *  lng,lat,address1,province,city,district
     */
    public function SetInfo51(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode

        $parr['legalperson'] = I('legalperson');
        $parr['legalsex'] = I('legalsex');
        $parr['legalcountry'] = I('legalcountry');
        $parr['idcardtype'] = I('idcardtype');
        $parr['idcardinfo'] = I('idcardinfo');
        $parr['legalcardstarttime'] = I('legalcardstarttime');
        $parr['legalcardendtime'] = I('legalcardendtime');
        $parr['legalphone'] = I('legalphone');

        if(empty($parr['legalphone'])||empty($parr['legalperson']) || $parr['idcardtype']==""||empty($parr['legalsex'])||empty($parr['legalcountry'])||empty($parr['idcardinfo']) ||empty($parr['legalcardstarttime'])||empty($parr['legalcardendtime'])){
            $this->ajaxReturn(Message(1001,'请完善相关参数'));
        }


        $result = IGD('Setinfo','Agent')->SetInfo51($parr);
        $this->ajaxReturn($result);
    }
    /**
     * 添加与修改个人资料第六步
     * @param ucode,fee_bank,fee_name,fee_cardnum,accounttype,bankprovince,bankcity
     * bankname,contactline
     */
    public function SetInfo61(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $parr['fee_cardnum'] = I('fee_cardnum');
        $parr['fee_name'] = I('fee_name');
        $parr['accounttype'] = I('accounttype');
        $parr['fee_bank'] = I('fee_bank');
        $parr['bankname'] =I('bankname');
        $parr['bankprovince'] = I('bankprovince');
        $parr['bankcity'] = I('bankcity');
        $parr['balancecardtype'] = I('balancecardtype');
        $parr['balancenum'] = I('balancenum');
        
        if(empty($parr['fee_cardnum'])||empty($parr['fee_name'])||empty($parr['accounttype'])||empty($parr['fee_bank'])||empty($parr['bankname'])||empty($parr['bankprovince']) ||empty($parr['bankcity'])||$parr['balancecardtype']=="" ||empty($parr['balancenum'])){
            $this->ajaxReturn(Message(1001,'请完善相关参数'));
        }
        $result = IGD('Setinfo','Agent')->SetInfo61($parr);
        $this->ajaxReturn($result);
    }
    /**
     * 添加与修改个人资料第七步
     * @param ucode,idcardtype,idcardinfo,banktel,fee_alipay,fee_weixin
     *
     */
    public function SetInfo71(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $result = uploadimg('agent');
        $imglist = array();
        if ($result['code'] == 0) {
            $imglist = array_values($result['data']);
        }
        if($result['code'] == 1006){
            $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
            $url = GetHost() . '/';
            foreach ($imglist1 as $key => $value) {
                $imglist[] = str_replace($url, "", $value['c_pimgepath']);
            }
        }

        $parr['idcard_img'] = $imglist[0];
        $parr['idcard_img1'] = $imglist[1];
        $parr['bankcardimg'] = $imglist[2];
        $parr['bankcardimg1'] = $imglist[3];
        $parr['charter_img'] = $imglist[4];
        $parr['contractimg'] = $imglist[5];

        if (empty($parr['idcard_img']) || empty($parr['idcard_img1'])) {
            $this->ajaxReturn(Message(3001,'请上传身份证图'));
        }

        if (empty($parr['charter_img'])) {
            $this->ajaxReturn(Message(3002,'请上传营业执照'));
        }

        if(empty($parr['contractimg'])){
            $this->ajaxReturn(Message(3003,'请上传合同照片'));
        }

        if (empty($parr['bankcardimg']) || empty($parr['bankcardimg1'])) {
            $this->ajaxReturn(Message(3004,'请上传银行卡照片'));
        }

        $result = IGD('Setinfo','Agent')->SetInfo71($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 添加与修改个人资料第八步 (上传图片)
     * @param ucode,idcard_img,idcard_img1(charter_img,company_sign)
     */
    public function SetInfo81()
    {
        //查询商家资料信息
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;  //商户ucode
        $result = IGD('Setinfo','Agent')->GetShopInfo($parr);
        $vodata = $result['data'];

        $result = uploadimg('agent');
        $imglist = array();
        if ($result['code'] == 0) {
            $imglist = array_values($result['data']);
        }
        if($result['code'] == 1006){
            $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
            $url = GetHost() . '/';
            foreach ($imglist1 as $key => $value) {
                $imglist[] = str_replace($url, "", $value['c_pimgepath']);
            }
        }
        $parr['storeimg'] = $imglist[0];
        $parr['deskimg'] = $imglist[1];
        $parr['storeinsideimg'] = $imglist[2];

        if (empty($imglist[0]) || empty($imglist[1]) || empty($imglist[2])) {
            $this->ajaxReturn(Message(3001,'请上传店铺相关图片哦'));
        }

        $result = IGD('Setinfo','Agent')->SetInfo81($parr);
        $this->ajaxReturn($result);
    }
}
