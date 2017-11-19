<?php

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 *  申请成为商家
 */
class GetbusinessController extends BaseController {

    //绑定邀请码   incode,ucode
    public function BingIncode()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['incode'] = I('incode');
        $result = IGD('Getbusiness', 'User')->BingIncode($parr);
        $this->ajaxReturn($result);
    }


    //查询邀请码
    public function IncodeUserinfo()
    {
        $incode = I('incode');
        $result = IGD('Getbusiness', 'User')->IncodeUserinfo($incode);
        $this->ajaxReturn($result);
    }

    /*商家资料01*/
    public function info_1(){
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result['data'];

        $result = IGD('Myagent','Agent')->GetShopInfo($parr);
        $this->assign('vo',$result['data']);

        $this->show();
    }

    /*商家资料02*/
    public function info_2(){
        $parr['ucode'] = session('USER.ucode');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];

        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
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

        $result_3 = IGD('Getbusiness','User')->GetIndustry();
        $this->industry = $result_3['data'];

        $this->show();
    }

    /*商家资料03*/
    public function info_3(){       
        //查询友收宝资料是否存在
        $parr['ucode'] = session('USER.ucode');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
        $newarr = explode($result_2['data']['c_county'],$result_2['data']['address1']);
        $result_2['data']['address1'] = $newarr[1];
        $this->assign('vo',$result_2['data']);

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
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
        $parr['ucode'] = session('USER.ucode');
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
        $parr['ucode'] = session('USER.ucode');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
        $newarr = explode($result_2['data']['c_county'],$result_2['data']['address1']);     
        $result_2['data']['address1'] = $newarr[count($newarr)-1];
        $this->assign('vo',$result_2['data']);  
        
        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
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
    
    /*商家资料06*/
    public function info_6(){
        //查询友收宝资料是否存在
        $parr['ucode'] = session('USER.ucode');
        $result_1 = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $this->upayinfo = $result_1['data'];
        
        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
        $this->assign('vo',$result_2['data']);  
        
        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
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
        $parr['ucode'] = session('USER.ucode');
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
        
        $parr['ucode'] = session('USER.ucode');
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
        $parr['ucode'] = session('USER.ucode');
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
    
    /*审核状态*/
    public function checkstate(){
        //查询友收宝资料是否存在
        $parr['ucode'] = session('USER.ucode');
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
        $parr['ucode'] = session('USER.ucode');
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


    /*商家资料01*/
    public function sub4_1(){
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Myagent','Agent')->GetShopInfo($parr);
        $this->assign('vo',$result['data']);

        /*新老用户补全资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        /*1，补全资料，2成为商家*/
        $this->hreftype = I('hreftype');

        $this->show();
    }

    /*商家资料02*/
    public function sub4_2(){
        $parr['ucode'] = session('USER.ucode');
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

        $result_3 = IGD('Getbusiness','User')->GetIndustry();
        $this->industry = $result_3['data'];

        /*新老用户补全资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        /*1，补全资料，2成为商家*/
        $this->hreftype = I('hreftype');

        $this->show();
    }

    /*商家资料03*/
    public function sub4_3(){
        //查询友收宝资料是否存在
        $parr['ucode'] = session('USER.ucode');

        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
        $newarr = explode($result_2['data']['c_county'],$result_2['data']['address1']);
        $result_2['data']['address1'] = $newarr[1];
        $this->assign('vo',$result_2['data']);

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
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

        /*新老用户补全资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        /*1，补全资料，2成为商家*/
        $this->hreftype = I('hreftype');

        $this->show();
    }
    
    /*商家资料04*/
    public function sub4_4(){
        //查询友收宝资料是否存在
        $parr['ucode'] = session('USER.ucode');

        $result_2 = IGD('Myagent','Agent')->GetShopInfo($parr);
        $this->assign('vo',$result_2['data']);

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
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

        /*新老用户补全资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        /*1，补全资料，2成为商家*/
        $this->hreftype = I('hreftype');

        $this->show();
    }

    /*商家资料05*/
    public function sub4_5(){
        //查询友收宝资料是否存在
        $parr['ucode'] = session('USER.ucode');

        $result_2 =  IGD('Getbusiness','User')->GetShopInfo($parr);
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

        /*新老用户补全资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        /*1，补全资料，2成为商家*/
        $this->hreftype = I('hreftype');

        $this->show();
    }

    /**
     * 添加与修改个人资料第一步 (商家类型、注册类型)
      * @param ucode,type,isfixed
     */
    public function SetInfo1(){
        $parr['ucode'] = session('USER.ucode');
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
        $parr['ucode'] = session('USER.ucode');
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
     *  ucode,name,merchantname,idcardinfo,idcardstarttime,idcardendtime,phone,province,address,city,address,district,charter
     *  
     */
    public function SetInfo3(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
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
        $parr['charter'] = $data['charter'];

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

        $parr['ucode'] = session('USER.ucode');
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
        
        $parr['ucode'] = session('USER.ucode');
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

        $parr['ucode'] = session('USER.ucode');
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
     * 添加与修改个人资料第七步
      * @param ucode,idcardimg,idcardimg1,bankcardimg,bankcardimg1,charter_img,contractimg
      *
     */
    public function SetInfo7(){
        //查询商家资料信息
        $parr['ucode'] = session('USER.ucode');
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
        $parr['ucode'] = session('USER.ucode');
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



/*----------------------------   之前接口数据  已整合邀请码绑定 、查询邀请码控制器于新增页面功能    -----------------------------*/


      /**
     * 首页
     * @return [type] [description]
     */
    public function index()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Getbusiness', 'User')->GetShopInfo($parr);
        $this->assign('data',$result['data']);

        $ucode = session('USER.ucode');
        $resultuser = IGD('Login','Login')->GetUserByCode($ucode);
        $this->info = $resultuser['data'];

        /*新老用户补全资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Setinfo','Agent')->CheckData($parr);
        $this->oldinfo = $result['data'];

        $this->hrefurl = I('type');/*1补全资料，2成为商家*/

        $this->show();
    }

    // 输入金卡卡号
    public function goldcard()
    {
        $this->show();
    }

    /**
     * 商家资料
     */
    public function shoperinfo()
    {
        /*获取行业*/
        if ($result['data']['tradepid'] == 0) {
            $id = $result['data']['c_shoptrade'];
        }
        $induresult = IGD('Getbusiness', 'User')->GetIndustry($id);
        foreach ($induresult['data'] as $k => $val) {
            $industry[$k/3][] = $val;
        }
        $this->industry = $industry;

        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('Getbusiness', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;

        /*获取商家资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Getbusiness', 'User')->GetShopInfo($parr);

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
        $this->assign('prove',$prove[0]);/*省*/
        $this->assign('citye',$citye[0]);/*市*/
        $this->assign('newdistrict',$newdistrict);/*区*/
        $this->assign('xsaddress',$xsaddress);/*详细地址*/

        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        $this->imgurl = GetHost().'/';

        $this->show();
    }
    /*银行基本信息*/
    public function step_2(){
        /*获取省份*/
        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('Getbusiness', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;

        /*获取商家资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Getbusiness', 'User')->GetShopInfo($parr);
                
        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        $this->ctype = I('ctype');
        
        $this->assign('vo',$result['data']);
        $this->show();
    }
    public function step_3(){
        /*获取商家资料*/
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Getbusiness', 'User')->GetShopInfo($parr);       
        /*判断商家属性,0为线上，1为线下*/
        $isfixed = I('isfixed');
        if($isfixed==null){
            $this->isfixed = $result['data']['c_isfixed'];
        }else{
            $this->isfixed = $isfixed;
        }
        $this->ctype = I('ctype');        
        $this->assign('vo',$result['data']);
        $this->show();
    }
    
    /**
     * 审核信息
     */
    /*public function checkinfo()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Getbusiness', 'User')->GetShopInfo($parr);
        $this->assign('data',$result['data']);
        $this->show();
    }
*/

    //保存行业
    public function EditIndustry()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['tid'] = I('tid');
        $result = IGD('Getbusiness', 'User')->EditIndustry($parr);
        $this->ajaxReturn($result);
    }

    // 获取省市区数据列表
    public function getRegion() {
        $parr['parentid'] = I('parentid');
        $parr['regiontype'] = I('regiontype');
        $region = IGD('Getbusiness', 'User');
        $result = $region->GetAddress($parr);
        $this->ajaxReturn($result);
    }

    // 添加与修改个人资料
    public function SaveAgentInfo()
    {
        $result = uploadimg('agent');
        //dump($_POST);dump($_FILES);die;
        if ($_POST['type'] == 2 || $_POST['isfixed'] == 1) {
            if (empty($_POST['charter_img']) || empty($_POST['company_sign'])) {
                if ($result['code'] != 0) {
                    $this->error('请上传企业相关证件图片');
                }
                $imgdata = $result['data'];
                if (empty($_POST['charter_img']) && !empty($_POST['company_sign'])) {
                    $imgdata['company_sign'] = $_POST['company_sign'];
                } else if (!empty($_POST['charter_img']) && empty($_POST['company_sign'])) {
                    $imgdata['charter_img'] = $_POST['charter_img'];
                }
            } else {
                $imgdata['charter_img'] = $_POST['charter_img'];
                $imgdata['company_sign'] = $_POST['company_sign'];
            }
        }
        if (empty($_POST['idcard_img']) || empty($_POST['idcard_img1'])) {
            if ($result['code'] != 0) {
                $this->error('请上传身份证图片');
            }
            $imgdata = $result['data'];
            if (empty($_POST['idcard_img']) && !empty($_POST['idcard_img1'])) {
                $imgdata['idcard_img1'] = $_POST['idcard_img1'];
            } else if (!empty($_POST['idcard_img']) && empty($_POST['idcard_img1'])) {
                $imgdata['idcard_img'] = $_POST['idcard_img'];
            }
        } else {
            $imgdata['idcard_img'] = $_POST['idcard_img'];
            $imgdata['idcard_img1'] = $_POST['idcard_img1'];
        }

        $parr['ucode'] = session('USER.ucode');
        $parr['type'] = I('type');
        $parr['istore'] = 1;
        $parr['phone'] = strtrim1(I('phone'));
        $parr['email'] = strtrim1(I('email'));
        $parr['qq'] = strtrim1(I('qq'));
        $parr['home_tel'] = strtrim1(I('home_tel'));

        //新增商家行业位置
        $parr['lng'] = I('lng');             //经度
        $parr['lat'] = I('lat');             //纬度
        $parr['isfixed'] = I('isfixed');     //0线上微商，1线下微商
        $parr['address1'] = strtrim1(I('address1'));     //位置详情
        $parr['tid'] = I('tid');             //行业id

        /*身份证*/
        $parr['idcard'] = strtrim1(I('idcard'));
        $parr['idcard_img'] = $imgdata['idcard_img'];
        $parr['idcard_img1'] = $imgdata['idcard_img1'];
        if(empty($parr['idcard'])|| empty($parr['idcard_img']) || empty($parr['idcard_img1'])){
            $this->error('请完善身份证信息');
        }
        if (empty($parr['type']) ||empty($parr['phone']) ||empty($parr['email']) ||empty($parr['qq']) ||$parr['isfixed']=='') {
            $this->error('请完善资料信息1');
        }
        if (empty($parr['lng']) ||empty($parr['lat']) ||empty($parr['address1'])||empty($parr['tid'])) {
            $this->error('请完善资料信息4');
        }
        if ($parr['type'] == 2) {
            $parr['name'] = strtrim1(I('name'));
            $parr['postcode'] = strtrim1(I('postcode'));
            $parr['company'] = strtrim1(I('company'));
            $parr['address'] = strtrim1(I('address'));
            $parr['charter'] = strtrim1(I('charter'));
            $parr['charter_img'] = $imgdata['charter_img'];
            $parr['company_sign'] = $imgdata['company_sign'];
            if (empty($parr['postcode'])|| empty($parr['name'])  || empty($parr['charter']) || empty($parr['charter_img']) || empty($parr['company_sign']) || empty($parr['company']) || empty($parr['address'])) {
                $this->error('请完善资料信息2');
            }
        } else {
            $parr['name'] = I('name1');
            if (empty($parr['name'])) {
                $this->error('请完善资料信息2');
            }
        }

        $parr['fee_bank'] = strtrim1(I('fee_bank'));
        $parr['fee_branch'] = strtrim1(I('fee_branch'));
        $parr['fee_cardnum'] = strtrim1(I('fee_cardnum'));
        $parr['fee_name'] = strtrim1(I('fee_name'));
        $parr['fee_alipay'] = strtrim1(I('fee_alipay'));
        $parr['fee_weixin'] = strtrim1(I('fee_weixin'));
        if (empty($parr['fee_bank'])|| empty($parr['fee_branch'])  || empty($parr['fee_cardnum']) || empty($parr['fee_name']) || empty($parr['fee_alipay']) || empty($parr['fee_weixin'])) {
            $this->error('请完善收款资料信息');
        }

        $result = IGD('Getbusiness', 'User')->SaveAgentInfo($parr);
        if ($result['code'] != 0) {
            $this->error($result['msg']);
        }
        $this->success('提交成功','checkinfo');

    }

     /**
     * 添加与修改商家资料第一步 (商家类型、注册类型)
      * @param ucode,type,isfixed
     */
    public function SaveAgentInfo1()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['isfixed'] = I('isfixed');
        $parr['type'] = I('type');
        if (empty($parr['type']) || $parr['isfixed']=="") {
            $this->ajaxReturn(Message(1002, "请选择商家资质！"));
        }
        $result = IGD('Getbusiness', 'User')->SaveAgentInfo1($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 添加与修改商家资料第二步 (行业信息)
     * @param ucode,tid(商家行业id)
     */
    public function SaveAgentInfo2()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['tid'] = I('tid');
        if (empty($parr['tid'])) {
            $this->ajaxReturn(Message(1003, "请选择商家所属行业！"));
        }
        $result = IGD('Getbusiness', 'User')->SaveAgentInfo2($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 添加与修改个人资料第三步 (基本信息)
     * @param ucode,type,istore,name,phone,email,qq,home_tel,(idcard,idcard_img)
     * ,(postcode,company,address,charter,charter_img,company_sign)
     */
    public function SaveAgentInfo3()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Getbusiness', 'User')->GetShopInfo($parr);
        $vodata =  $result['data'];

        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
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

    /**
     * 添加与修改个人资料第四步 (收款信息)
      * @param ucode,fee_bank,fee_branch,fee_cardnum,fee_name,fee_alipay,fee_weixin
     */
    public function SaveAgentInfo4()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
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

     /**
     * 添加与修改个人资料第五四步 (上传证件)
      * 
     */
    public function SaveAgentInfo5()
    {
        //查询商家资料信息
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Getbusiness', 'User')->GetShopInfo($parr);
        $vodata =  $result['data'];

        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

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
