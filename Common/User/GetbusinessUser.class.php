<?php

/**
 * 用户申请成为商家接口
 */
class GetbusinessUser {

    /**
     * 查询商家个人资料
     * @param ucode(infoid)
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
        $field = 'a.*,b.c_headimg,b.c_shoptrade,c.c_address as address1,c.c_longitude,c.c_latitude,c.c_isfixed,c.c_province,c.c_city,c.c_county,f.c_name as tradename,f.c_pid as tradepid,b.c_acode';
        $data = M('Check_shopinfo as a')->join($join)->where($where)->field($field)->find();
        $wherincode['c_ucode'] = $data['c_acode'];
        $data['incode'] =  M('Invite_code')->where($wherincode)->getField('c_code');
        $data['agentname'] = $this->IncodeUserinfo($data['incode'])['c_name'];
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 绑定邀请码
     * @param incode,ucode
     */
    function BingIncode($parr)
    {
        //查询用户信息
        $userwhere['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($userwhere)->find();
        if (!$userinfo) {
            return Message(1009,'用户信息不存在');
        }

        //查询商户信息
        $result = M('Check_shopinfo')->where($userwhere)->find();
        if ($result) {
            return Message(1001,'您已绑定过激活码');
        }

        $db = M('');
        $db->startTrans(); /* 开启事务 */

        $wherincode['c_code'] = $parr['incode'];
        $incodedata = M('Check_codelist')->where($wherincode)->find();
        if ($incodedata['c_state'] != 2 || !empty($incodedata['c_ucode'])) {
            return Message(1000, '激活码错误');
        }
        
        $whereadd['c_acode'] = $incodedata['c_acode'];
        $result = M('Users')->where($userwhere)->save($whereadd);
        // if (!$result) {
        //     $db->rollback();
        //     return Message(1002, '保存失败！');
        // }

        //去除推荐关系
        // $tuijianucode['c_ucode'] = $parr['ucode'];
        // $result = M('Users_tuijian')->where($tuijianucode)->delete();

        //新增资料信息表
        $shopinfo['c_istore'] = 1;
        $shopinfo['c_ucode'] = $parr['ucode'];
        $shopinfo['c_name'] = '小蜜微商'. CreateOrder();
        $shopinfo['c_phone'] = $userinfo['c_phone'];
        $shopinfo['c_checked'] = 0;
        $shopinfo['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->add($shopinfo);
        if (!$result) {
            $db->rollback();
            return Message(1002, '保存失败！');
        }


        // 改变邀请码相关状态
        $inparr['ucode'] = $parr['ucode'];
        $inparr['code'] = $parr['incode'];
        $result = $this->RegiterCode($inparr);
        if ($result['code'] != 0) {
            $db->rollback();
            return Message(1002, '注册失败！');
        }

        $db->commit();
        return Message(0, '提交成功');
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

    /**
     * 查出邀请码绑定的代理商
     * @param incode
     */
    function IncodeUserinfo($incode)
    {
        $wherincode['c_code'] = $incode;
        $incodedata = M('Check_codelist')->where($wherincode)->field('c_acode')->find();

        $where['c_ucode'] = $incodedata['c_acode'];
        $field = "c_ucode,c_istore,c_name,c_phone";
        $data = M('Check_shopinfo')->where($where)->field($field)->find();
        return $data;
    }

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

     /**
     * 添加与修改个人资料第一步 (商家类型、注册类型)
      * @param ucode,type,isfixed
     */
     function SaveAgentInfo1($parr)
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
     function SaveAgentInfo2($parr)
    {
       $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $where['c_ucode'] = $parr['ucode'];
        $save['c_shoptrade'] = $parr['tid'];
        $result = M('Users')->where($where)->save($save);
        if($result < 0){
            return Message(1001,'保存商家行业失败');
        }

        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第三步 (基本信息)
      * @param type,ucode,name,phone,email,qq,home_tel,idcard,(postcode,company,address,charter)
     *  merchantname,merchantshortname,feetype,mchdealtype,legalperson
     *  lng,lat,isfixed,address1,province,city,district
     */
     function SaveAgentInfo3($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $where['c_ucode'] = $parr['ucode'];
        $localinfo = M('User_local')->where($where)->find();

        $db = M('');
        $db->startTrans();

        //保存地址信息
        $localdata['c_ucode'] = $parr['ucode'];
        $localdata['c_longitude'] = $parr['lng'];
        $localdata['c_latitude'] = $parr['lat'];
        $localdata['c_province'] = $parr['province'];
        $localdata['c_city'] = $parr['city'];
        $localdata['c_county'] = $parr['district'];
        $localdata['c_address'] = $parr['address1'];
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

        $where['c_ucode'] = $parr['ucode'];
        $type = M('Check_shopinfo')->where($where)->getField('c_type');

        $data['c_name'] = $parr['name'];
        $data['c_phone'] = $parr['phone'];
        $data['c_email'] = $parr['email'];
        $data['c_qq'] = $parr['qq'];
        $data['c_home_tel'] = $parr['home_tel'];
        $data['c_type'] = 1;

        if ($type == 2 || $parr['type'] == 2) {
            $data['c_type'] = 2;
            $data['c_postcode'] = $parr['postcode'];
            $data['c_company'] = $parr['company'];
            $data['c_address'] = $parr['address'];
            $data['c_charter'] = $parr['charter'];
        }

        $data['c_idcard'] = $parr['idcard'];
        $data['c_updatetime'] = date('Y-m-d H:i:s');

        //新增参数
        $data['c_merchantname'] = $parr['merchantname'];   //商户名称
        $data['c_merchantshortname'] = $parr['merchantshortname'];  //商户简称
        $data['c_feetype'] = $parr['feetype'];   //经营币种
        $data['c_mchdealtype'] = $parr['mchdealtype'];  //经营类型（1实体，2虚拟）
        $data['c_legalperson'] = $parr['legalperson'];  //企业法人        

        $result = M('Check_shopinfo')->where($where)->save($data);

        if($result < 0){
            $db->rollback();
            return Message(1001, '保存基本信息失败');
        }

        $db->commit();

        return Message(0, '保存成功');
    }

    /**
     * 添加与修改个人资料第四步 (收款信息)
      * @param ucode,fee_bank,fee_branch,fee_cardnum,fee_name,fee_alipay,fee_weixin
      * accounttype,contactline,bankname,bankprovince,bankcity,idcardtype,idcardinfo,banktel
     */
     function SaveAgentInfo4($parr)
    {
        $result = $this->checkInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        $angentinfo = $result['data'];

        $where['c_ucode'] = $parr['ucode'];
        //收款信息资料
        $data['c_fee_bank'] = $parr['fee_bank'];
        $data['c_fee_branch'] = $parr['fee_branch'];
        $data['c_fee_cardnum'] = $parr['fee_cardnum'];
        $data['c_fee_name'] = $parr['fee_name'];
        $data['c_fee_alipay'] = $parr['fee_alipay'];
        $data['c_fee_weixin'] = $parr['fee_weixin'];
        $data['c_updatetime'] = date('Y-m-d H:i:s');

        //新增参数
        $data['c_accounttype'] = $parr['accounttype'];  //开户类型（1企业，2个人）
        $data['c_contactline'] = $parr['contactline'];  //支行联行号 
        $data['c_bankname'] = $parr['bankname'];    //开户支行名    
        $data['c_bankprovince'] = $parr['bankprovince'];    //开户支行所在省份
        $data['c_bankcity'] = $parr['bankcity'];    //开户支行所在城市
        $data['c_idcardtype'] = $parr['idcardtype'];  //证件类型（1身份证，2护照）
        $data['c_idcardinfo'] = $parr['idcardinfo'];  //证件号
        $data['c_banktel'] = $parr['banktel'];     //执卡人手机号码

        $result = M('Check_shopinfo')->where($where)->save($data);

        if($result < 0){
            return Message(1001, '保存收款信息失败');
        }

        return Message(0, '保存成功');
    }

     /**
     * 添加与修改个人资料第五步 (上传图片)
      * @param ucode,idcard_img,idcard_img1(charter_img,company_sign)
     */
     function SaveAgentInfo5($parr)
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
        if ($angentinfo['c_checked'] != 3) {
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
                $parr1['ptitle'] = '企业【'.$angentinfo['c_company'].'】申请微商,请点击查看,并做审核操作';
            } else {
                $parr1['ptitle'] = '个人【'.$angentinfo['c_name'].'】申请微商,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的微商提交资料，请尽快登录后台做审核";
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
     * 添加与修改个人资料
     * @param ucode,type,istore,name,phone,email,qq,home_tel,(idcard,idcard_img)
     * ,(postcode,company,address,charter,charter_img,company_sign)
     *   ucode,lng,lat,isfixed,address,tid
     *   fee_bank,fee_branch,fee_cardnum,fee_name,fee_alipay,fee_weixin
     */
    function SaveAgentInfo($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009,'验证登录失效，请重新登录再操作');
        }
        //查询是否有资料
        $where['c_ucode'] = $parr['ucode'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if (!empty($angentinfo['c_dcode']) && $angentinfo['c_checked'] != 1) {
            return Message(2005,'资料已提交等待审核中，不能修改');
        }
        $db = M('');
        $db->startTrans();

        $data['c_dcode'] = CreateUcode('XWS');
        $data['c_type'] = $parr['type'];
        $data['c_istore'] = $parr['istore'];
        $data['c_name'] = $parr['name'];
        $data['c_phone'] = $parr['phone'];
        $data['c_email'] = $parr['email'];
        $data['c_qq'] = $parr['qq'];
        $data['c_home_tel'] = $parr['home_tel'];

        if ($parr['type'] == 2) {
            $data['c_postcode'] = $parr['postcode'];
            $data['c_company'] = $parr['company'];
            $data['c_address'] = $parr['address'];
            $data['c_charter'] = $parr['charter'];
            $data['c_charter_img'] = $parr['charter_img'];
            $data['c_company_sign'] = $parr['company_sign'];
        }
        $data['c_idcard'] = $parr['idcard'];
        $data['c_idcard_img'] = $parr['idcard_img'];
        $data['c_idcard_img1'] = $parr['idcard_img1'];


        if ($parr['istore'] == 2) {
            $data['c_checked'] = 3;
        } else {
            $data['c_checked'] = 0;
        }

        //收款信息资料
        $data['c_fee_bank'] = $parr['fee_bank'];
        $data['c_fee_branch'] = $parr['fee_branch'];
        $data['c_fee_cardnum'] = $parr['fee_cardnum'];
        $data['c_fee_name'] = $parr['fee_name'];
        $data['c_fee_alipay'] = $parr['fee_alipay'];
        $data['c_fee_weixin'] = $parr['fee_weixin'];
        if ($angentinfo) {
            $data['c_updatetime'] = date('Y-m-d H:i:s');
            $result = M('Check_shopinfo')->where($where)->save($data);
            $Id = $angentinfo['c_id'];
        } else {
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $data['c_updatetime'] = date('Y-m-d H:i:s');
            $data['c_ucode'] = $parr['ucode'];
            $result = M('Check_shopinfo')->add($data);
            $Id = $result;
        }

        if (!$result) {
            $db->rollback();
            return Message('提交失败');
        }

        // 创建审核消息
        if ($parr['istore'] == 1) {
            $parr1['ucode'] = M('Users')->where($where)->getField('c_acode');
            if ($parr['type'] == 2) {
                $parr1['ptitle'] = '企业【'.$parr['company'].'】申请微商,请点击查看,并做审核操作';
            } else {
                $parr1['ptitle'] = '个人【'.$parr['name'].'】申请微商,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的微商提交资料，请尽快登录后台做审核";
            $parr1['url'] = GetHost(3).'/agent.php/Agent/Shopcheck/details?Id='.$Id;
            $result = $this->Create_information($parr1);
            if ($result['code'] != 0) {
                $db->rollback();
                return Message(1000,'创建信息失败');
            }

        } else {
            $pfcode = M('Invite_code')->where($where)->getField('c_fcode');
            if (!empty($pfcode)) {
                $pfwhere['c_code'] = $pfcode;
                $parr1['ucode'] = M('Invite_code')->where($pfwhere)->getField('c_ucode');
                if ($parr['type'] == 2) {
                    $parr1['ptitle'] = '您有新的企业【'.$parr['company'].'】代理,已提交资料';
                } else {
                    $parr1['ptitle'] = '您有新的个人【'.$parr['name'].'】代理,已提交资料';
                }
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的代理提交资料";
                $parr1['url'] = GetHost(3).'/agent.php/Home/Agentntrol/index';
                $result = $this->Create_information($parr1);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return Message(1000,'创建信息失败');
                }
            }
        }

        if ($parr['istore'] == 1) {
            //修改商家地理位置
            $result = $this->EditShopLocal($parr);
            if ($result<0) {
                $db->rollback();
                return Message(1000,'商家位置尚未修改！');
            }

            //修改商家行业
            $result = $this->EditIndustry($parr);
            if ($result<0) {
                $db->rollback();
                return Message(1000,'商家行业信息尚未修改！');
            }
        }

        // 发送短信通知
        $sewhere['c_ucode'] = $parr1['ucode'];
        $separr['telephone'] = M('Users')->where($sewhere)->getField('c_phone');
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $returndata = IGD('Sendmsg', 'Login')->SendVerify($separr);

        $db->commit();
        return Message(0,'提交成功');
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
     * 修改商家行业
     * @param ucode,tid(商家行业id)
     */
    function EditIndustry($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $save['c_shoptrade'] = $parr['tid'];
        $result = M('Users')->where($where)->save($save);

        return $result;
    }

    /**
     * 修改商家地理位置
     * @param ucode,lng,lat,isfixed,address1
     */
    function EditShopLocal($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $localinfo = M('User_local')->where($where)->find();
        $data['c_ucode'] = $parr['ucode'];
        $data['c_longitude'] = $parr['lng'];
        $data['c_latitude'] = $parr['lat'];
        $data['c_isfixed'] = $parr['isfixed'];
        $data['c_address'] = $parr['address1'];
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$localinfo) {
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($data);
        } else {
            $result = M('User_local')->where($where)->save($data);
        }

        //同步修改用户地理位置
        $add_userdata['c_isfixed1'] = $parr['isfixed'];
        $add_userdata['c_longitude1'] = $parr['lng'];
        $add_userdata['c_latitude1'] = $parr['lat'];
        $add_userdata['c_address1'] = $parr['address1'];
        $result = M('Users')->where($where)->save($add_userdata);
        return $result;
    }

    /*
      获取省市区
      @param array $parr
     */
    function GetAddress($parr) {
        $whereadd['parent_id'] = $parr['parentid'];
        $whereadd['region_type'] = $parr['regiontype'];
        $list = M('Region')->where($whereadd)->select();

        return $list;
    }

}
