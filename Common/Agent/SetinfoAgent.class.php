<?php

/**
 * 商家提交资料接口
 */
class SetinfoAgent
{
    /**
     * 添加与修改个人资料 验证
     * @param ucode
     */
    function checkInfo($parr){
        if (empty($parr['ucode'])) {
            return Message(1009,'验证登录失效，请重新登录再操作');
        }

        //查询是否有资料
        $where['c_ucode'] = $parr['ucode'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();

        //查友收宝信息
        $upayinfo = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        if (!empty($angentinfo['c_dcode']) && $angentinfo['c_checked'] != 1 && $upayinfo['code'] == 0) {
            return Message(2005,'资料已提交等待审核中，不能修改');
        }
        return MessageInfo(0,'查询成功',$angentinfo);
    }

    function checkInfo1($parr){
        if (empty($parr['ucode'])) {
            return Message(1009,'验证登录失效，请重新登录再操作');
        }
        //查询是否有资料
        $where['c_ucode'] = $parr['ucode'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();

        //查友收宝信息
        $upayinfo = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        $isfixed =M('User_local')->where($where)->find();
        if($isfixed['c_isfixed']==0){
            if (!empty($angentinfo['c_dcode']) && $angentinfo['c_checked'] != 1 && !empty($angentinfo['c_contractimg'])) {
                return Message(2005,'资料已提交等待审核中，不能修改');
            }
        }else{
            if (!empty($angentinfo['c_dcode']) && $angentinfo['c_checked'] != 1 && !empty($angentinfo['c_contractimg']) && !empty($angentinfo['c_storeimg'])) {
                return Message(2005,'资料已提交等待审核中，不能修改');
            }
        }

        return MessageInfo(0,'查询成功',$angentinfo);
    }


    function checkInfo2($parr){
        if (empty($parr['ucode'])) {
            return Message(1009,'验证登录失效，请重新登录再操作');
        }
        //查询是否有资料
        $where['c_ucode'] = $parr['ucode'];
        $shop = M('Check_shopinfo')->where($where)->find();
        $upayinfo = IGD('Yspay','Scanpay')->FindYspayInfo($parr);
//        if ($upayinfo['code']!=0) {
//            //查友收宝信息
//            if($shop['c_type']==1){  // 个人
//                if (!empty($shop['c_dcode']) && $shop['c_checked'] != 1 && !empty($shop['c_idcard_img']) &&!empty($shop['c_bankcardimg'])) {
//                    return Message(2005,'资料已提交等待审核中，不能修改');
//                }
//            }elseif($shop['c_type']==2){ // 企业
//                if (!empty($shop['c_dcode']) && $shop['c_checked'] != 1 && !empty($shop['c_idcard_img']) && !empty($shop['c_charter_img']) && !empty($shop['c_charterpub_img'])) {
//                    return Message(2005,'资料已提交等待审核中，不能修改');
//                }
//
//            }elseif($shop['c_type']==3){ // 个体户
//                if (!empty($shop['c_dcode']) && $shop['c_checked'] != 1 && !empty($shop['c_idcard_img']) && !empty($shop['c_charter_img']) &&!empty($shop['c_bankcardimg'])) {
//                    return Message(2005,'资料已提交等待审核中，不能修改');
//                }
//            }
//        }
        
        if ($upayinfo['code']==0 && $upayinfo['data']['c_openaccount'] != 2 && $shop['c_checked'] != 1) {
            return Message(2005,'资料已提交等待审核中，不能修改');
        }
        return MessageInfo(0,'查询成功',$shop);
    }


    /**
     * 检测老用户数据是否完善
     * @param ucode
     */
    function CheckData($parr){
        $ucode =$parr['ucode'];

        $user =M('Users')->where(array('c_ucode'=>$ucode))->find();

        $Info =M('Check_shopinfo')->where(array('c_ucode'=>$ucode))->find();

        if($user['c_shop']==0 && !$Info){
            return MessageInfo(0,'您还不是商家',0);
        }

        // 通过判断商家是否上传合同图片  银行卡图片来决定是否要完善资料
        // $fixed =M('User_local')->where(array('c_ucode'=>$ucode))->find();

        switch($Info['c_type']){
            case 1:
                if(empty($Info['c_idcard_img']) || empty($Info['c_bankcardimg']) ||empty($Info['c_bankcardimg1'])){
                    $flag = 1; //需要完善资料
                    $msg ="需要完善资料";
                }else{
                    $flag = 2; //资料完整
                    $msg ="资料完整";
                }
                break;
            case 2:
                if(empty($Info['c_idcard_img']) || empty($Info['c_charter_img']) ||empty($Info['c_charterpub_img'])){
                    $flag = 1; //需要完善资料
                    $msg ="需要完善资料";
                }else{
                    $flag = 2; //资料完整
                    $msg ="资料完整";
                }
                break;
            case 3:
                if(empty($Info['c_idcard_img']) || empty($Info['c_charter_img']) ||empty($Info['c_bankcardimg'])){
                    $flag = 1; //需要完善资料
                    $msg ="需要完善资料";
                }else{
                    $flag = 2; //资料完整
                    $msg ="资料完整";
                }
                break;
        }
        $upayinfo = IGD('Yspay','Scanpay')->FindYspayInfo($parr);
        if ($upayinfo['code']==0 && $upayinfo['data']['c_openaccount'] != 2) {
            $flag = 2; //资料完整
            $msg ="资料完整";
        }else{
            $flag = 1; //需要完善资料
            $msg ="需要完善资料";
        }
        return MessageInfo(0,$msg,$flag);

    }

    /**
     * 查询对应的行业列表
     * @param id(商家行业列表)
     */
    function GetIndustry($id)
    {
        if (!empty($id)) {
            $where['c_pid'] = $id;
        } else {
            $where['c_pid'] = 0;
        }

        $where[] = array('c_id <> 21 and c_id <> 22 and c_id <> 23');
        $data = M('Shop_industry')->where($where)->select();
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 商家激活串码
     * @param code,ucode
     */
    function RegiterCode($parr)
    {
        $ucode = $parr['ucode'];
        $code = $parr['code'];

        //查询串码信息
        $where['c_code'] = $parr['code'];
        $data = M('Check_codelist')->where($where)->find();
        if (!$data) {
            return Message(3000,'串码信息不存在');
        }

        //查询市代串码信息
        $inwhere['c_ucode'] = $data['c_acode'];
        $inwhere['c_rule'] = 2;
        $invidata = M('Invite_code')->where($inwhere)->find();
        if (!$invidata) {
            return Message(3002,'代理信息不存在');
        }

        //改变串码状态
        $codesave['c_state'] = 1;
        $codesave['c_ucode'] = $ucode;
        $codesave['c_activitime'] = date('Y-m-d H:i:s');
        $result = M('Check_codelist')->where($where)->save($codesave);
        if (!$result) {
            return Message(3003,'改变激活状态失败');
        }

        //改变市代串码可用数量
        $insave['c_usenum'] = $invidata['c_usenum'] - 1;
        $result = M('Invite_code')->where($inwhere)->save($insave);
        if (!$result) {
            return Message(3004,'修改串码数量失败');
        }

        return Message(0,'激活成功');
    }


    // 银盛进件第1步
    function newData1($parr){
        $result = $this->checkInfo2($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        if (empty($parr['ucode']) || $parr['isfixed']=="" || empty($parr['type'])) {
            return Message(1001, '参数缺失');
        }
        $db = M('');
        $db->startTrans();
        $where['c_ucode'] = $parr['ucode'];
        $info_data['c_type'] = $parr['type'];
        $info_data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($info_data);
        if($result < 0){
            $db->rollback();
            return Message(1002, '保存商家类型失败');
        }
        //保存位置信息
        $localinfo = M('User_local')->where($where)->find();
        $data['c_ucode'] = $parr['ucode'];
        $data['c_isfixed'] = $parr['isfixed'];
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$localinfo) {
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($data);
        } else {
            $result = M('User_local')->where($where)->save($data);
        }
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存行业信息失败');
        }
        $db->commit();
        return Message(0, '保存成功');
    }
    // 银盛进件第2步
    function newData2($parr){
        $result = $this->checkInfo2($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        if (empty($parr['tid'])) {
            return Message(1001, '行业信息不能为空');
        }
        $where['c_ucode'] = $parr['ucode'];
        $save['c_shoptrade'] = $parr['tid'];
        $result = M('Users')->where($where)->save($save);
        if($result < 0){
            return Message(1001,'保存商家行业失败');
        }
        return Message(0, '保存成功');
    }
    // 银盛进件第3步
    function newData3($parr){

        $result = $this->checkInfo2($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $shopinfo =$result['data'];
        $db = M('');
        $db->startTrans();
        $where['c_ucode'] = $parr['ucode'];
        $localinfo = M('User_local')->where($where)->find();
        //保存地址信息
        $localdata['c_ucode'] = $parr['ucode'];
        $localdata['c_longitude'] = $parr['lng'];		//经度
        $localdata['c_latitude'] = $parr['lat'];		//纬度
        $localdata['c_province'] = $parr['province'];	//省
        $localdata['c_city'] = $parr['city'];			//市
        $localdata['c_county'] = $parr['district'];		//区
        $localdata['c_address'] = $parr['address'];	//详细地址
        $localdata['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$localinfo) {
            $localdata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($localdata);
        } else {
            $result = M('User_local')->where($where)->save($localdata);
        }
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存地址信息失败');
        }
        //同步修改用户地理位置
        $add_userdata['c_isfixed1'] = $localinfo['c_isfixed'];
        $add_userdata['c_longitude1'] = $parr['lng'];
        $add_userdata['c_latitude1'] = $parr['lat'];
        $add_userdata['c_address'] = $parr['address'];
        $result = M('Users')->where($where)->save($add_userdata);
        if($result < 0){
            $db->rollback();
            return Message(1002, '同步地址信息失败');
        }
        $where['c_ucode'] = $parr['ucode'];
        $data['c_merchantname'] = $parr['merchantname'];   //商户名称
        $data['c_name'] = $parr['name'];  // 负责人
        $data['c_phone'] =$parr['phone']; //手机号
        $data['c_idcardinfo'] =$parr['idcardinfo']; //负责人身份证号
        $data['c_address'] = $parr['address'];	 	//申请单位地址
        $data['c_idcardstarttime'] =$parr['idcardstarttime']; //
        $data['c_idcardendtime'] =$parr['idcardendtime']; //
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $data['c_idcardtype'] = 1;    //证件类型（1身份证，2护照）
        if($shopinfo['c_type']==3){ //个体户 有营业执照参数
            $data['c_charter'] =$parr['charter'];
        }
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }
        $db->commit();
        return Message(0, '保存成功');

    }
    // 银盛进件第4步
    function newData4($parr){
        $result = $this->checkInfo2($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $shopinfo =$result['data'];
        $data['c_fee_cardnum'] = $parr['fee_cardnum'];//银行卡号
        $data['c_fee_name'] = $parr['fee_name']; //开户人
        $data['c_fee_bank'] = $parr['fee_bank'];//开户银行
        $data['c_bankname'] =$parr['bankname'];    //开户支行名
        $data['c_bankprovince'] =$parr['bankprovince']; //支行所在省
        $data['c_bankcity'] =$parr['bankcity']; //支行所在市
        $data['c_accounttype'] = 1;
        if($shopinfo['c_type']==2){  //企业多个营业执照
            if(empty($parr['charter'])){
                return Message(1001,'企业相关信息不能为空');
            }
            $data['c_charter'] =$parr['charter']; //营业执照
//            $data['c_legalperson'] =$parr['legalperson']; //法人
//            $data['c_legalphone'] =$parr['legalphone']; //法人电话
            $data['c_accounttype'] = 2;    //账户类型（1个人，2企业）
        }
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $where['c_ucode'] = $parr['ucode'];
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            return Message(1001, '保存银行信息失败');
        }
        return Message(0, '保存成功');


    }
    // 银盛进件第5步

    function newData5($parr){
        $result = $this->checkInfo2($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $shopinfo = $result['data'];
        //图片信息


        $shopData =M('Check_shopinfo')->where(array('c_ucode'=>$parr['ucode']))->find();

        $data['c_idcard_img'] =$parr['idcardimg'];
        $data['c_idcard_img1'] =$parr['idcardimg1'];

        if(empty($shopData['c_idcard_img']) || empty($shopData['c_idcard_img'])){
            if(empty($parr['idcardimg'])|| empty($parr['idcardimg1'])){
                return Message(1002,'请上传身份证照片');
            }
        }
        if($shopinfo['c_type']==1){ //个人
            if(empty($shopData['c_bankcardimg']) ||empty($shopData['c_bankcardimg1'])){
                if(empty($parr['bankcardimg'])||empty($parr['bankcardimg1'])){
                    return Message(1003,'请上传银行卡照片');
                }
            }
            $data['c_bankcardimg'] =$parr['bankcardimg'];
            $data['c_bankcardimg1'] =$parr['bankcardimg1'];

        }elseif($shopinfo['c_type']==2){  //企业
            if(empty($shopData['c_charter_img']) || empty($shopData['c_charterpub_img'])){
                if(empty($parr['charterimg'])||empty($parr['charterpubimg'])){
                    return Message(1004,'请上传营业执照相关照片');
                }
            }
            $data['c_charter_img'] =$parr['charterimg'];
            $data['c_charterpub_img'] =$parr['charterpubimg'];

        }elseif($shopinfo['c_type']==3){ // 个体户
            if(empty($shopData['c_bankcardimg']) || empty($shopData['c_bankcardimg1'])||empty($shopData['c_charter_img'])){
                if(empty($parr['bankcardimg'])||empty($parr['bankcardimg1'])||empty($parr['charterimg'])){
                    return Message(1003,'请上传相关照片');
                }
            }
            $data['c_bankcardimg'] =$parr['bankcardimg'];
            $data['c_bankcardimg1'] =$parr['bankcardimg1'];
            $data['c_charter_img'] =$parr['charterimg'];
        }
        $db = M('');
        $db->startTrans();

        $where['c_ucode'] = $parr['ucode'];
        //提交审核
        $status = 1;
        if ($shopinfo['c_checked'] <2) {
            $status = 0;
            $data['c_checked'] = 0;
            $data['c_dcode'] = CreateUcode('XWS');
        }
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1003, '生成dcode信息失败');
        }

        $return =$this->syncData($parr['ucode']);  //同步数据
        if($return['code']!=0){
            $db->rollback();
            return $return;
        }

        $Id = $shopinfo['c_id'];

        //新增资料给代理发送相关通知
        if ($shopinfo['c_checked'] !=3 ){
            $parr1['ucode'] = M('Users')->where($where)->getField('c_acode');
            if ($shopinfo['c_type'] == 2) {
                $parr1['ptitle'] = '企业【'.$shopinfo['c_merchantname'].'】申请商家,请点击查看,并做审核操作';
            } else if($shopinfo['c_type'] == 1){
                $parr1['ptitle'] = '个人【'.$shopinfo['c_merchantname'].'】申请商家,请点击查看,并做审核操作';
            } else if($shopinfo['c_type'] == 3){
                $parr1['ptitle'] = '个体户【'.$shopinfo['c_merchantname'].'】申请商家,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
            $parr1['url'] = GetHost(3).'/agent.php/Agent/Shopcheck/details?Id='.$Id;
            $result = $this->Create_information($parr1);
            if ($result['code'] != 0) {
                $db->rollback();
                return Message(1004,'创建信息失败');
            }

            // 发送短信通知
            $sewhere['c_ucode'] = $parr1['ucode'];
            $separr['telephone'] = M('Users')->where($sewhere)->getField('c_phone');
            $separr['userid'] = C('TEl_USER');
            $separr['account'] = C('TEl_ACCESS');
            $separr['password'] = C('TEl_PASSWORD');
            $returndata = IGD('Sendmsg', 'Login')->SendVerify($separr);
        }

        $db->commit();
        return Message(0, '保存成功');

    }

    /**
     * 检查银盛资料是否完善
     * @param ysinfo 商家资料表信息
     * @param type 商家类型 0 线上  1 线下
     * @param flag 商家资质 1 个人 2 企业  3 个体户
     */
    function CheckYsInfo($ysinfo,$flag)
    {
        $infokey = array_keys($ysinfo);
        $infovalue = array_values($ysinfo);
        $sign = 1;   				//已完善
        foreach ($infokey as $k => $v) {
            switch($flag){
                case 1;  //个人
                    if ($v != 'c_charterno' &&  $v != 'c_charter_img'&& $v != 'c_charterpub_img' && $v!='c_personphone' && $v!='c_reason'){
                        if ($infovalue[$k]=='') {
                            $sign = 0;
                        }
                    }
                    break;
                case 2;  //企业
                    if ($v != 'c_bankcard_img' && $v != 'c_bankcard_img1' && $v!='c_reason'){
                        if ($infovalue[$k]=='') {
                            $sign = 0;
                        }
                    }
                    break;
                case 3;  //个体户
                    if ( $v != 'c_charterpub_img' && $v !='c_personphone' && $v!='c_reason'){
                        if ($infovalue[$k]=='') {
                            $sign = 0;
                        }
                    }
                    break;
            }
        }
        if ($sign == 1) {
            return Message(0,'资料已完善');
        } else {
            return Message(3001,'资料不完善!');
        }
    }


    // 申请成功则同步数据到 银盛用户表
    function  syncData($ucode){

        $where['c_ucode']=$ucode;
        $value =M('Check_shopinfo')->where($where)->find();
        $user =M('Users')->where($where)->find();
        $local =M('User_local')->where($where)->find();

        $insert['c_ucode'] = $ucode;
        $insert['c_isagent'] = $user['c_isagent'];
        $insert['c_isdataall']= 1;
        $insert['c_isshop']= 1;
        $insert['c_openaccount'] = "0";
        $insert['c_legalname']=$value['c_name'];
        $insert['c_merchant']=$value['c_merchantname'];
        $insert['c_charterno']=$value['c_charter'];
        $insert['c_province']=$local['c_province'];
        $insert['c_city']=$local['c_city'];
        $insert['c_address']=$local['c_address'];
        $insert['c_username']=$this->CreateUserName();
        $insert['c_creditlimit'] ="0";
        $insert['c_bankno']=$value['c_fee_cardnum'];
        // $insert['c_reason']=$value['c_remark'];
        $insert['c_bankuser']=$value['c_fee_name'];
        $insert['c_bankname']=$value['c_fee_bank'];
        $insert['c_bankallname']=$value['c_fee_bank'];
        $insert['c_bankbranch']=$value['c_bankname'];
       // $insert['c_bankcity']=$value['c_bankprovince'].$value['c_bankcity'];
        $insert['c_storetype']=$local['c_isfixed'];   //商家类型 线上 0  线下 1
        $insert['c_storetials']=$value['c_type'];   //商家资质 个人 1 企业 2  个体户 3
        $insert['c_industry']=$user['c_shoptrade'];
        $insert['c_person']=$value['c_name'];
        //$insert['c_personphone']=$value['c_legalphone'];
        $insert['c_personidcard']=$value['c_idcardinfo'];
        if ($value['c_idcardendtime'] == '长期') {
            $insert['c_personidcardendtime'] = '99991231';
        } else {
            $insert['c_personidcardendtime']= str_replace('-', '', $value['c_idcardendtime']);
        }
        $insert['c_phone']=$value['c_phone'];
        $insert['c_legalcardno']=$value['c_idcardinfo'];
        $insert['c_charter_img']=$value['c_charter_img'];
        $insert['c_idcard_img']=$value['c_idcard_img'];
        $insert['c_idcard_img1']=$value['c_idcard_img1'];
        $insert['c_bankcard_img']=$value['c_bankcardimg'];
        $insert['c_bankcard_img1']=$value['c_bankcardimg1'];
        $insert['c_charterpub_img']=$value['c_charterpub_img'];
        $insert['c_addtime'] =date('Y-m-d H:i:s');
		$insert['c_cardtype']=$value['c_idcardtype'];
        $insert['c_banktype']=$value['c_accounttype'];

        //检测同步到银盛的资料是否完善
        $res =$this->CheckYsInfo($insert,$value['c_type']);
        if($res['code']!=0){
            return $res;
        }

        $res =M('User_yspay')->where($where)->find();
        if(empty($res)){
            $result =M('User_yspay')->add($insert);
        }else{
            $result =M('User_yspay')->where($where)->save($insert);
        }
        if($result<0){
            return Message(1001,'同步数据失败');
        }else{
            return Message(0,'同步成功');
        }
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


    /**
     * 添加与修改个人资料第一步 (商家类型、注册类型)
     * @param ucode,type,isfixed
     */
    function SetInfo1($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        if($parr['isfixed'] == 1){
            $parr['type'] = 2;
        }

        if (empty($parr['ucode']) || $parr['isfixed']=="" || empty($parr['type'])) {
            return Message(1001, '保存信息有误');
        }

        $db = M('');
        $db->startTrans();

        $where['c_ucode'] = $parr['ucode'];

        $info_data['c_type'] = $parr['type'];
        $info_data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($info_data);

        if($result < 0){
            $db->rollback();
            return Message(1002, '保存商家信息失败');
        }

        //保存位置信息
        $localinfo = M('User_local')->where($where)->find();

        $data['c_ucode'] = $parr['ucode'];
        $data['c_isfixed'] = $parr['isfixed'];
        $data['c_updatetime'] = date('Y-m-d H:i:s');

        if (!$localinfo) {
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($data);
        } else {
            $result = M('User_local')->where($where)->save($data);
        }

        if($result < 0){
            $db->rollback();
            return Message(1001, '保存地址信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第二步 (行业信息)
     * @param ucode,tid(商家行业id)
     */
    function SetInfo2($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        if (empty($parr['tid'])) {
            return Message(1001, '保存信息有误');
        }

        $where['c_ucode'] = $parr['ucode'];
        $save['c_shoptrade'] = $parr['tid'];
        $result = M('Users')->where($where)->save($save);
        if($result < 0){
            return Message(1001,'保存商家行业失败');
        }

        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第三步
     * @param
     *  ucode,merchantname,merchantshortname,mchdealtype(type,company,address,postcode,charter)
     *
     */
    function SetInfo3($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $db = M('');
        $db->startTrans();

        $where['c_ucode'] = $parr['ucode'];
        $type = M('Check_shopinfo')->where($where)->getField('c_type');

        $data['c_merchantname'] = $parr['merchantname'];   //商户名称
        $data['c_merchantshortname'] = $parr['merchantshortname'];  //商户简称
        $data['c_mchdealtype'] = $parr['mchdealtype'];  //经营类型（1实体，2虚拟）
        if ($type == 2 || $parr['type'] == 2) {
            $data['c_type'] = 2;	//商家资质
            $data['c_company'] = $parr['company'];   	//申请单位名称
            $data['c_address'] = $parr['address'];	 	//申请单位地址
            $data['c_postcode'] = $parr['postcode'];	//邮政编码
            $data['c_charter'] = $parr['charter'];		//营业执照
        }

        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第四步
     * @param
     *  ucode,name,phone,legalperson,idcard,feetype
     *
     */
    function SetInfo4($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $db = M('');
        $db->startTrans();

        $data['c_name'] = $parr['name'];	//负责人
        $data['c_phone'] = $parr['phone'];	//联系电话
        $data['c_legalperson'] = $parr['legalperson'];  //企业法人
        $data['c_idcard'] = $parr['idcard'];	//身份证号码
        $data['c_feetype'] = $parr['feetype'];   //经营币种

        $data['c_updatetime'] = date('Y-m-d H:i:s');

        $where['c_ucode'] = $parr['ucode'];
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第五步
     * @param
     *  ucode,qq,email,home_tel
     *  lng,lat,address1,province,city,district
     */
    function SetInfo5($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $db = M('');
        $db->startTrans();

        $where['c_ucode'] = $parr['ucode'];
        $localinfo = M('User_local')->where($where)->find();

        //保存地址信息
        $localdata['c_ucode'] = $parr['ucode'];
        $localdata['c_longitude'] = $parr['lng'];		//经度
        $localdata['c_latitude'] = $parr['lat'];		//纬度
        $localdata['c_province'] = $parr['province'];	//省
        $localdata['c_city'] = $parr['city'];			//市
        $localdata['c_county'] = $parr['district'];		//区
        $localdata['c_address'] = $parr['address1'];	//详细地址
        $localdata['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$localinfo) {
            $localdata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($localdata);
        } else {
            $result = M('User_local')->where($where)->save($localdata);
        }

        if($result < 0){
            $db->rollback();
            return Message(1001, '保存地址信息失败');
        }

        //同步修改用户地理位置
        $add_userdata['c_isfixed1'] = $localinfo['c_isfixed'];
        $add_userdata['c_longitude1'] = $parr['lng'];
        $add_userdata['c_latitude1'] = $parr['lat'];
        $add_userdata['c_address1'] = $parr['address1'];
        $result = M('Users')->where($where)->save($add_userdata);
        if($result < 0){
            $db->rollback();
            return Message(1002, '同步地址信息失败');
        }

        $data['c_qq'] = $parr['qq'];				//QQ
        $data['c_email'] = $parr['email'];  		//邮箱
        $data['c_home_tel'] = $parr['home_tel'];	//客服电话

        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第六步
     * @param ucode,fee_bank,fee_name,fee_cardnum,accounttype,bankprovince,bankcity
     * bankname,contactline
     */
    function SetInfo6($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];
        $where['c_ucode'] = $parr['ucode'];

        $data['c_fee_bank'] = $parr['fee_bank'];		//开户银行
        $data['c_fee_name'] = $parr['fee_name'];		//开户人
        $data['c_fee_cardnum'] = $parr['fee_cardnum'];	//银行卡号
        $data['c_accounttype'] = $parr['accounttype'];  //账户类型（1企业，2个人）
        $data['c_bankprovince'] = $parr['bankprovince'];    //开户支行所在省份
        $data['c_bankcity'] = $parr['bankcity'];    //开户支行所在城市
        $data['c_bankname'] = $parr['bankname'];    //开户支行名
        $data['c_contactline'] = $parr['contactline'];  //支行联行号

        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            return Message(1001, '保存收款信息失败');
        }

        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第七步
     * @param ucode,idcardtype,idcardinfo,banktel,fee_alipay,fee_weixin
     *
     */
    function SetInfo7($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];
        $where['c_ucode'] = $parr['ucode'];

        $data['c_idcardtype'] = $parr['idcardtype'];  	//证件类型（1身份证，2护照）
        $data['c_idcardinfo'] = $parr['idcardinfo'];  	//证件号
        $data['c_banktel'] = $parr['banktel'];     		//执卡人手机号码
        $data['c_fee_alipay'] = $parr['fee_alipay'];	//支付宝帐号
        $data['c_fee_weixin'] = $parr['fee_weixin'];	//微信帐号

        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            return Message(1001, '保存收款信息失败');
        }

        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第八步 (上传图片)
     * @param ucode,idcard_img,idcard_img1(charter_img,company_sign)
     */
    function SetInfo8($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $where['c_ucode'] = $parr['ucode'];
        $type = M('Check_shopinfo')->where($where)->getField('c_type');

        if (empty($parr['idcard_img']) || empty($parr['idcard_img1'])) {
            return Message(3000,'请上传身份证图');
        }
        if ($type == 2) {
            if (empty($parr['charter_img']) || empty($parr['company_sign'])) {
                return Message(3001,'请上传营业执照与标志图');
            }
        }

        $db = M('');
        $db->startTrans();

        //图片信息
        $data['c_idcard_img'] = $parr['idcard_img'];
        $data['c_idcard_img1'] = $parr['idcard_img1'];
        $data['c_charter_img'] = $parr['charter_img'];
        $data['c_company_sign'] = $parr['company_sign'];

        //提交审核
        $status = 1;
//        if ($angentinfo['c_checked'] != 3) {
        $find =M('Check_shopinfo')->where($where)->find();
        if($find['c_checked'] !=3){
            $status = 0;
            $data['c_checked'] = 0;
            $data['c_dcode'] = CreateUcode('XWS');
        }
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1003, '生成dcode信息失败');
        }
        $Id = $angentinfo['c_id'];


        //查询友收宝资料是否存在
        $upayinfo = IGD('Upay','Scanpay')->FindUpayInfo($parr);
        if ($upayinfo['code'] != 0) {
            //同步友收宝资料
            $result = IGD('Upay','Scanpay')->SynchronousInfo($parr,$status);
            if ($result['code'] != 0) {
                $db->rollback();
                return $result;
            }
        }


        if ($upayinfo['code'] != 0) {
            //老商家补充资料  提交资料到友收宝
            if ($angentinfo['c_checked'] == 3) {
                /*$result = IGD('Upay','Scanpay')->PostAddmerchant($parr);
                if ($result['code'] != 0) {
                    // return $result;
                }*/
            }
        }

        //新增资料给代理发送相关通知
        if ($angentinfo['c_checked'] != 3) {
            $parr1['ucode'] = M('Users')->where($where)->getField('c_acode');
            if ($angentinfo['c_type'] == 2) {
                $parr1['ptitle'] = '企业【'.$angentinfo['c_company'].'】申请商家,请点击查看,并做审核操作';
            } else {
                $parr1['ptitle'] = '个人【'.$angentinfo['c_name'].'】申请商家,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
            $parr1['url'] = GetHost(3).'/agent.php/Agent/Shopcheck/details?Id='.$Id;
            $result = $this->Create_information($parr1);
            if ($result['code'] != 0) {
                $db->rollback();
                return Message(1004,'创建信息失败');
            }

            // 发送短信通知
            $sewhere['c_ucode'] = $parr1['ucode'];
            $separr['telephone'] = M('Users')->where($sewhere)->getField('c_phone');
            $separr['userid'] = C('TEl_USER');
            $separr['account'] = C('TEl_ACCESS');
            $separr['password'] = C('TEl_PASSWORD');
            $returndata = IGD('Sendmsg', 'Login')->SendVerify($separr);
        }

        $db->commit();
        return Message(0, '保存成功');
    }



    /**
     * 添加与修改个人资料第一步 (商家类型、注册类型)
     * @param ucode,type,isfixed
     */
    function SetInfo11($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        if (empty($parr['ucode']) || $parr['isfixed']=="") {
            return Message(1001, '参数缺失');
        }
        $db = M('');
        $db->startTrans();

        $where['c_ucode'] = $parr['ucode'];
        $info_data['c_type'] =2;
        $info_data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($info_data);

        if($result < 0){
            $db->rollback();
            return Message(1002, '保存商家信息失败');
        }

        //保存位置信息
        $localinfo = M('User_local')->where($where)->find();

        $data['c_ucode'] = $parr['ucode'];
        $data['c_isfixed'] = $parr['isfixed'];
        $data['c_updatetime'] = date('Y-m-d H:i:s');

        if (!$localinfo) {
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($data);
        } else {
            $result = M('User_local')->where($where)->save($data);
        }

        if($result < 0){
            $db->rollback();
            return Message(1001, '保存地址信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第二步 (行业信息)
     * @param ucode,tid(商家行业id)
     */
    function SetInfo21($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        if (empty($parr['tid'])) {
            return Message(1001, '保存信息有误');
        }

        $where['c_ucode'] = $parr['ucode'];
        $save['c_shoptrade'] = $parr['tid'];
        $result = M('Users')->where($where)->save($save);
        if($result < 0){
            return Message(1001,'保存商家行业失败');
        }

        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第三步
     * @param
     *  ucode,merchantname,merchantshortname,mchdealtype(type,company,address,postcode,charter)
     *
     */
    function SetInfo31($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $db = M('');
        $db->startTrans();

        $where['c_ucode'] = $parr['ucode'];
        $localinfo = M('User_local')->where($where)->find();

        //保存地址信息
        $localdata['c_ucode'] = $parr['ucode'];
        $localdata['c_longitude'] = $parr['lng'];		//经度
        $localdata['c_latitude'] = $parr['lat'];		//纬度
        $localdata['c_province'] = $parr['province'];	//省
        $localdata['c_city'] = $parr['city'];			//市
        $localdata['c_county'] = $parr['district'];		//区
        $localdata['c_address'] = $parr['address'];	//详细地址
        $localdata['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$localinfo) {
            $localdata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($localdata);
        } else {
            $result = M('User_local')->where($where)->save($localdata);
        }

        if($result < 0){
            $db->rollback();
            return Message(1001, '保存地址信息失败');
        }

        //同步修改用户地理位置
        $add_userdata['c_isfixed1'] = $localinfo['c_isfixed'];
        $add_userdata['c_longitude1'] = $parr['lng'];
        $add_userdata['c_latitude1'] = $parr['lat'];
        $add_userdata['c_address'] = $parr['address'];
        $result = M('Users')->where($where)->save($add_userdata);
        if($result < 0){
            $db->rollback();
            return Message(1002, '同步地址信息失败');
        }

        $where['c_ucode'] = $parr['ucode'];
        $data['c_company'] = $parr['company'];   	//公司名称
        $data['c_merchantname'] = $parr['merchantname'];   //商户名称
        $data['c_merchantshortname'] = $parr['merchantshortname'];  //商户简称
        $data['c_storetype'] =$parr['storetype']; //商户类型  1 个人 2 小商家户 3 企事业
        $data['c_address'] = $parr['address'];	 	//申请单位地址
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第四步
     * @param
     *  ucode,name,phone,legalperson,idcard,feetype
     *
     */
    function SetInfo41($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $db = M('');
        $db->startTrans();

        $data['c_chartertype'] = $parr['chartertype'];  // 证照类型
        $data['c_charter'] = $parr['charter']; // 证照号码
        $data['c_charterstarttime'] = $parr['charterstarttime'];  //证照生效时间
        $data['c_charterendtime'] = $parr['charterendtime']; // 证照截止时间
        $data['c_email'] = $parr['email']; // 邮箱
        $data['c_home_tel'] =$parr['home_tel'];// 客服电话
        $data['c_updatetime'] = date('Y-m-d H:i:s');

        $where['c_ucode'] = $parr['ucode'];
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第五步
     * @param
     *  ucode,qq,email,home_tel
     *  lng,lat,address1,province,city,district
     */
    function SetInfo51($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $db = M('');
        $db->startTrans();
        $where['c_ucode'] =$parr['ucode'];

        $data['c_legalperson'] = $parr['legalperson'];
        $data['c_legalsex'] = $parr['legalsex'];
        $data['c_legalcountry'] = $parr['legalcountry'];
        $data['c_idcardtype'] = $parr['idcardtype'];
        $data['c_idcardinfo'] = $parr['idcardinfo'];
        $data['c_legalcardstarttime'] = $parr['legalcardstarttime'];
        $data['c_legalcardendtime'] = $parr['legalcardendtime'];
        $data['c_legalphone'] = $parr['legalphone'];

        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第六步
     * @param ucode,fee_bank,fee_name,fee_cardnum,accounttype,bankprovince,bankcity
     * bankname,contactline
     */
    function SetInfo61($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];
        $where['c_ucode'] = $parr['ucode'];

        $data['c_fee_cardnum'] = $parr['fee_cardnum'];//银行卡号
        $data['c_fee_name'] = $parr['fee_name']; //开户人
        $data['c_accounttype'] = $parr['accounttype'];  //账户类型  银行卡类型
        $data['c_fee_bank'] = $parr['fee_bank'];//开户银行
        $data['c_bankname'] =$parr['bankname'];    //开户支行名
        $data['c_bankprovince'] = $parr['bankprovince'];    //开户支行所在省份
        $data['c_bankcity'] = $parr['bankcity'];    //开户支行所在城市
        $data['c_balancecardtype'] =$parr['balancecardtype'];   //结算账户身份类型
        $data['c_balancenum'] = $parr['balancenum']; //结算账户身份号

        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            return Message(1001, '保存收款信息失败');
        }

        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第七步
     * @param ucode,idcardtype,idcardinfo,banktel,fee_alipay,fee_weixin
     *
     */
    function SetInfo71($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];
        $where['c_ucode'] = $parr['ucode'];

        $data['c_idcard_img'] = $parr['idcard_img'];  //身份证正面
        $data['c_idcard_img1'] = $parr['idcard_img1'];//身份证反面
        $data['c_charter_img'] =$parr['charter_img'];//营业执照
        $data['c_contractimg'] = $parr['contractimg'];//合同照片
        $data['c_bankcardimg'] = $parr['bankcardimg'];//银行卡正面
        $data['c_bankcardimg1'] = $parr['bankcardimg1'];//银行卡反面

        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            return Message(1001, '保存收款信息失败');
        }

        $isfixed =M('User_local')->where($where)->find();

        if($isfixed['c_isfixed']==0){  //线上商家只有 7 步
            $db = M('');
            $db->startTrans();
            //提交审核
            $status = 1;
//        if ($angentinfo['c_checked'] != 3) {
            $find =M('Check_shopinfo')->where($where)->find();
            if($find['c_checked'] <2){
                $status = 0;
                $data['c_checked'] = 0;
                $data['c_dcode'] = CreateUcode('XWS');
            }
            $data['c_updatetime'] = date('Y-m-d H:i:s');
            $result = M('Check_shopinfo')->where($where)->save($data);
            if($result < 0){
                $db->rollback();
                return Message(1003, '生成dcode信息失败');
            }
            $Id = $angentinfo['c_id'];


            //新增资料给代理发送相关通知
            if ($angentinfo['c_checked'] != 3) {
                $parr1['ucode'] = M('Users')->where($where)->getField('c_acode');
                if ($angentinfo['c_type'] == 2) {
                    $parr1['ptitle'] = '企业【'.$angentinfo['c_company'].'】申请商家,请点击查看,并做审核操作';
                } else {
                    $parr1['ptitle'] = '个人【'.$angentinfo['c_name'].'】申请商家,请点击查看,并做审核操作';
                }
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3).'/agent.php/Agent/Shopcheck/details?Id='.$Id;
                $result = $this->Create_information($parr1);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return Message(1004,'创建信息失败');
                }

                // 发送短信通知
                $sewhere['c_ucode'] = $parr1['ucode'];
                $separr['telephone'] = M('Users')->where($sewhere)->getField('c_phone');
                $separr['userid'] = C('TEl_USER');
                $separr['account'] = C('TEl_ACCESS');
                $separr['password'] = C('TEl_PASSWORD');
                $returndata = IGD('Sendmsg', 'Login')->SendVerify($separr);
            }
            $db->commit();
        }



        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第八步 (上传图片)
     * @param ucode,idcard_img,idcard_img1(charter_img,company_sign)
     */
    function SetInfo81($parr)
    {
        $result = $this->checkInfo1($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $where['c_ucode'] = $parr['ucode'];
        $type = M('Check_shopinfo')->where($where)->getField('c_type');

        if (empty($parr['storeimg']) || empty($parr['deskimg']) ||empty($parr['storeinsideimg'])) {
            return Message(3000,'请上传店铺相关图片');
        }
        $db = M('');
        $db->startTrans();

        //图片信息
        $data['c_storeimg'] = $parr['storeimg'];
        $data['c_deskimg'] = $parr['deskimg'];
        $data['c_storeinsideimg'] = $parr['storeinsideimg'];

        //提交审核
        $status = 1;
//        if ($angentinfo['c_checked'] != 3) {
        $find =M('Check_shopinfo')->where($where)->find();
        if($find['c_checked'] !=3){
            $status = 0;
            $data['c_checked'] = 0;
            $data['c_dcode'] = CreateUcode('XWS');
        }
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($data);
        if($result < 0){
            $db->rollback();
            return Message(1003, '生成dcode信息失败');
        }
        $Id = $angentinfo['c_id'];


        //查询友收宝资料是否存在
//        $upayinfo = IGD('Upay','Scanpay')->FindUpayInfo($parr);
//        if ($upayinfo['code'] != 0) {
//            //同步友收宝资料
//            $result = IGD('Upay','Scanpay')->SynchronousInfo($parr,$status);
//            if ($result['code'] != 0) {
//                $db->rollback();
//                return $result;
//            }
//        }

//        if ($upayinfo['code'] != 0) {
//            //老商家补充资料  提交资料到友收宝
//            if ($angentinfo['c_checked'] == 3) {
//                /*$result = IGD('Upay','Scanpay')->PostAddmerchant($parr);
//                if ($result['code'] != 0) {
//                    // return $result;
//                }*/
//            }
//        }

        //新增资料给代理发送相关通知
        if ($angentinfo['c_checked'] <2) {
            $parr1['ucode'] = M('Users')->where($where)->getField('c_acode');
            if ($angentinfo['c_type'] == 2) {
                $parr1['ptitle'] = '企业【'.$angentinfo['c_company'].'】申请商家,请点击查看,并做审核操作';
            } else {
                $parr1['ptitle'] = '个人【'.$angentinfo['c_name'].'】申请商家,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
            $parr1['url'] = GetHost(3).'/agent.php/Agent/Shopcheck/details?Id='.$Id;
            $result = $this->Create_information($parr1);
            if ($result['code'] != 0) {
                $db->rollback();
                return Message(1004,'创建信息失败');
            }

            // 发送短信通知
            $sewhere['c_ucode'] = $parr1['ucode'];
            $separr['telephone'] = M('Users')->where($sewhere)->getField('c_phone');
            $separr['userid'] = C('TEl_USER');
            $separr['account'] = C('TEl_ACCESS');
            $separr['password'] = C('TEl_PASSWORD');
            $returndata = IGD('Sendmsg', 'Login')->SendVerify($separr);
        }

        $db->commit();
        return Message(0, '保存成功');
    }

    /**
     * 查询商家个人资料
     * @param ucode,infoid
     */
    function GetShopInfo($parr)
    {
        if (!empty($parr['ucode'])) {
            $where['a.c_ucode'] = $parr['ucode'];
        }

        if (!empty($parr['infoid'])) {
            $where['a.c_id'] = $parr['infoid'];
        }
        $join = 'left join t_users as b on a.c_ucode = b.c_ucode left join t_user_local as c on b.c_ucode = c.c_ucode';
        $join .= ' left join t_shop_industry as f on f.c_id=b.c_shoptrade';
        $field = 'a.*,b.c_headimg,b.c_shoptrade,c.c_address as address1,c.c_longitude,c.c_latitude,c.c_isfixed,c.c_province,c.c_city,c.c_county,f.c_name as tradename,f.c_pid as tradepid';
        $data = M('Check_shopinfo as a')->join($join)->where($where)->field($field)->find();

        //$data['c_address'] =$data['c_province'].$data['c_city'].$data['c_address'];
        $data['c_headimg'] =$data['c_headimg']==null?'':GetHost(1).'/'.$data['c_headimg'];

        $data['c_bankcardimg'] = $data['c_bankcardimg']==null?'':GetHost().'/'.$data['c_bankcardimg'];
        $data['c_bankcardimg1'] = $data['c_bankcardimg1']==null?'':GetHost().'/'.$data['c_bankcardimg1'];
        $data['c_contractimg'] = $data['c_contractimg']==null?'':GetHost().'/'.$data['c_contractimg'];
        $data['c_storeimg'] = $data['c_storeimg']==null?'':GetHost().'/'.$data['c_storeimg'];
        $data['c_deskimg'] = $data['c_deskimg']==null?'':GetHost().'/'.$data['c_deskimg'];
        $data['c_storeinsideimg'] = $data['c_storeinsideimg']==null?'':GetHost().'/'.$data['c_storeinsideimg'];
        $data['c_idcard_img'] = $data['c_idcard_img']==null?'':GetHost().'/'.$data['c_idcard_img'];
        $data['c_idcard_img1'] = $data['c_idcard_img1']==null?'':GetHost().'/'.$data['c_idcard_img1'];
        $data['c_charter_img'] = $data['c_charter_img']==null?'':GetHost().'/'.$data['c_charter_img'];
        $data['c_company_sign'] = $data['c_company_sign']==null?'':GetHost().'/'.$data['c_company_sign'];
        $data['c_charterpub_img'] = $data['c_charterpub_img']==null?'':GetHost().'/'.$data['c_charterpub_img'];

        $data['imgList']['c_idcard_img'] = $data['c_idcard_img']==null?'':$data['c_idcard_img'];
        $data['imgList']['c_idcard_img1'] = $data['c_idcard_img1']==null?'':$data['c_idcard_img1'];
        $data['imgList']['c_charter_img'] = $data['c_charter_img']==null?'':$data['c_charter_img'];
        $data['imgList']['c_company_sign'] = $data['c_company_sign']==null?'':$data['c_company_sign'];

        return MessageInfo(0,'查询成功',$data);
    }


    /**
     *  写入公告信息
     *  @param ucode,ptitle,title,origin,content
     */
    function Create_information($parr)
    {
        $data['c_ucode'] = $parr['ucode'];
        $data['c_ptitle'] = $parr['ptitle'];
        $data['c_title'] = $parr['title'];
        $data['c_origin'] = $parr['origin'];
        $data['c_content'] = $parr['content'];
        $data['c_url'] = $parr['url'];
        $data['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Check_info')->add($data);
        if (!$result) {
            return Message(1001,'创建失败');
        }
        return Message(0,'创建成功');
    }

}


?>