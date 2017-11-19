<?php

/**
 *  代理商，商家资料提交审核
 */
class MyagentAgent{

    /**
     * 查询商家个人资料  新
     * @param ucode,infoid
     */
    function GetShopInfo($parr)
    {
        if (!empty($parr['infoid'])){
            $where['a.c_id'] = $parr['infoid'];
        }else{
            if (!empty($parr['ucode'])) {
                $where['a.c_ucode'] = $parr['ucode'];
            }
        }
        $join = 'left join t_users as b on a.c_ucode = b.c_ucode left join t_user_local as c on b.c_ucode = c.c_ucode left join t_shop_industry as d on b.c_shoptrade=d.c_id';
        $join .= ' left join t_shop_industry as f on f.c_id=b.c_shoptrade';
        $field = 'a.*,b.c_acode,b.c_headimg,d.c_name as industry_name,c.c_address as address1,c.c_longitude,c.c_latitude,c.c_isfixed,c.c_province,c.c_city,c.c_county,f.c_name as tradename,f.c_pid as tradepid';
        $data = M('Check_shopinfo as a')->join($join)->where($where)->field($field)->find();
        //  $agent =M('Check_shopinfo')->where(array('c_ucode'=>$data['c_acode']))->field('c_name,c_phone')->find();

        $aa['b.c_ucode']=$data['c_ucode'];
        $join_agent = 'left join t_users as b on a.c_ucode=b.c_acode';
        $agent = M('Check_shopinfo as a')->join($join_agent)->where($aa)->field('a.c_name,a.c_phone')->find();

        // $list0['type']='所属代理商';
//        $list0['content']['agent_name'] =$agent['c_name']==null?'':$agent['c_name'];
//        $list0['content']['agent_phone'] =$agent['c_phone']==null?'':$agent['c_phone'];
        $data['agent_name']=$agent['c_name']==null?'':$agent['c_name'];
        $data['agent_phone']=$agent['c_phone']==null?'':$agent['c_phone'];


        $data['c_bankcardimg'] = $data['c_bankcardimg']==null?'':GetHost().'/'.$data['c_bankcardimg'];
        $data['c_bankcardimg1'] = $data['c_bankcardimg1']==null?'':GetHost().'/'.$data['c_bankcardimg1'];
        $data['c_charterpub_img'] = $data['c_charterpub_img']==null?'':GetHost().'/'.$data['c_charterpub_img'];
        $data['c_storeimg'] = $data['c_storeimg']==null?'':GetHost().'/'.$data['c_storeimg'];
        $data['c_deskimg'] = $data['c_deskimg']==null?'':GetHost().'/'.$data['c_deskimg'];
        $data['c_storeinsideimg'] = $data['c_storeinsideimg']==null?'':GetHost().'/'.$data['c_storeinsideimg'];
        $data['c_idcard_img'] = $data['c_idcard_img']==null?'':GetHost().'/'.$data['c_idcard_img'];
        $data['c_idcard_img1'] = $data['c_idcard_img1']==null?'':GetHost().'/'.$data['c_idcard_img1'];
        $data['c_charter_img'] = $data['c_charter_img']==null?'':GetHost().'/'.$data['c_charter_img'];
        //$data['c_company_sign'] = $data['c_company_sign']==null?'':GetHost().'/'.$data['c_company_sign'];
        return MessageInfo(0,'查询成功',$data);

//        $list1['type']='商户资料';
//        $list1['content']['c_merchantname']=$data['c_merchantname'];
//        $list1['content']['c_merchantshortname']=$data['c_merchantshortname'];
//        $list1['content']['c_mchdealtype']=$data['c_mchdealtype'];
//        $list1['content']['c_type']=$data['c_type'];
//        $list1['content']['c_company']=$data['c_company'];
//        $list1['content']['c_address']=$data['c_address'];
//        $list1['content']['c_postcode']=$data['c_postcode'];
//        $list1['content']['c_charter']=$data['c_charter'];
//        $list1['content']['c_name']=$data['c_name'];
//        $list1['content']['c_phone']=$data['c_phone'];
//        $list1['content']['c_legalperson']=$data['c_legalperson'];
//        $list1['content']['c_idcardtype']=$data['c_idcardtype']==1?'身份证号码':'护照号码';
//        $list1['content']['c_idcardinfo']=$data['c_idcardinfo'];
//        $list1['content']['c_feetype']=$data['c_feetype'];
//        $list1['content']['c_mchdealtype1']=$data['c_isfixed'];
//        $list1['content']['industry_name']=$data['industry_name'];
//        $list1['content']['c_qq']=$data['c_qq'];
//        $list1['content']['c_email']=$data['c_email'];
//        $list1['content']['c_home_tel']=$data['c_home_tel'];
//        $list1['content']['c_province']=$data['c_province'].$data['c_city'].$data['c_county'];
////        if(!empty($data['c_county'])){
////            $newarr = explode($data['c_county'],$data['c_address1']);
////            $list1['content']['address1']=$newarr[1];
////        }else{
//            $list1['content']['address1']=$data['address1'];
//        //}
//
//        $list2['type']='身份证件';
//        if(empty($data['c_idcard_img']) ||empty($data['c_idcard_img1'])){
//           // $list2['content']=[];
//            $list2['content']['c_idcard_img']='';
//            $list2['content']['c_idcard_img1']='';
//        }else{
//           // $list2['content']=[$img1,$img2];
//            $list2['content']['c_idcard_img']=GetHost().'/'.$data['c_idcard_img'];
//            $list2['content']['c_idcard_img1']=GetHost().'/'.$data['c_idcard_img1'];
//        }
//        $list3['type']='营业执照';
//        if(empty($data['c_charter_img']))
//            $list3['content']['c_charter_img']='';
//        else
//            $list3['content']['c_charter_img']=GetHost().'/'.$data['c_charter_img'];
//        $list4['type']='企业标志';
//        if(empty($data['c_company_sign']))
//            $list4['content']['c_company_sign']='';
//        else
//            $list4['content']['c_company_sign']=GetHost().'/'.$data['c_company_sign'];
//
//        if($agent && $agent!==null){
//            if($data['c_type']==1){ //个人
//                return MessageInfo(0,'查询成功',[$list0,$list1,$list2]);
//            }elseif($data['c_type']==2){  //企业
//                return MessageInfo(0,'查询成功',[$list0,$list1,$list2,$list3,$list4]);
//            }
//        }else{
//            if($data['c_type']==1){ //个人
//                return MessageInfo(0,'查询成功',[$list1,$list2]);
//            }elseif($data['c_type']==2){  //企业
//                return MessageInfo(0,'查询成功',[$list1,$list2,$list3,$list4]);
//            }
//        }


    }


    /**
     * 查询商家个人资料   线上
     * @param ucode,infoid
     */
    function GetShopInfo1($parr)
    {
        if (!empty($parr['infoid'])){
            $where['a.c_id'] = $parr['infoid'];
        }else{
            if (!empty($parr['ucode'])) {
                $where['a.c_ucode'] = $parr['ucode'];
            }
        }
        $join = 'left join t_users as b on a.c_ucode = b.c_ucode left join t_user_local as c on b.c_ucode = c.c_ucode left join t_shop_industry as d on b.c_shoptrade=d.c_id';
        $join .= ' left join t_shop_industry as f on f.c_id=b.c_shoptrade';
        $field = 'a.*,b.c_acode,b.c_headimg,d.c_name as industry_name,c.c_address as address1,c.c_longitude,c.c_latitude,c.c_isfixed,c.c_province,c.c_city,c.c_county,f.c_name as tradename,f.c_pid as tradepid';
        $data = M('Check_shopinfo as a')->join($join)->where($where)->field($field)->find();

        $aa['b.c_ucode']=$data['c_ucode'];
        $join_agent = 'left join t_users as b on a.c_ucode=b.c_acode';
        $agent = M('Check_shopinfo as a')->join($join_agent)->where($aa)->field('a.c_name,a.c_phone')->find();

        $list0['type']='所属代理商';
        $list0['content']['agent_name'] =$agent['c_name']==null?'':$agent['c_name'];
        $list0['content']['agent_phone'] =$agent['c_phone']==null?'':$agent['c_phone'];

        $list1['type']='商户资料';
        $list1['content']['c_merchantname']=$data['c_merchantname'];
        $list1['content']['c_merchantshortname']=$data['c_merchantshortname'];
        $list1['content']['c_mchdealtype']=$data['c_mchdealtype'];
        $list1['content']['c_type']=$data['c_type'];
        $list1['content']['c_company']=$data['c_company'];
        $list1['content']['c_address']=$data['c_address'];
        $list1['content']['c_postcode']=$data['c_postcode'];
        $list1['content']['c_charter']=$data['c_charter'];
        $list1['content']['c_name']=$data['c_name'];
        $list1['content']['c_phone']=$data['c_phone'];
        $list1['content']['c_legalperson']=$data['c_legalperson'];
        $list1['content']['c_idcardtype']=$data['c_idcardtype']==1?'身份证号码':'护照号码';
        $list1['content']['c_idcardinfo']=$data['c_idcardinfo'];
        $list1['content']['c_feetype']=$data['c_feetype'];
        $list1['content']['c_mchdealtype1']=$data['c_isfixed'];
        $list1['content']['industry_name']=$data['industry_name'];
        $list1['content']['c_qq']=$data['c_qq'];
        $list1['content']['c_email']=$data['c_email'];
        $list1['content']['c_home_tel']=$data['c_home_tel'];
        $list1['content']['c_province']=$data['c_province'].$data['c_city'].$data['c_county'];
//        if(!empty($data['c_county'])){
//            $newarr = explode($data['c_county'],$data['c_address1']);
//            $list1['content']['address1']=$newarr[1];
//        }else{
        $list1['content']['address1']=$data['address1'];
        //}

        $list2['type']='身份证件';
        if(empty($data['c_idcard_img']) ||empty($data['c_idcard_img1'])){
            // $list2['content']=[];
            $list2['content']['c_idcard_img']='';
            $list2['content']['c_idcard_img1']='';
        }else{
            // $list2['content']=[$img1,$img2];
            $list2['content']['c_idcard_img']=GetHost().'/'.$data['c_idcard_img'];
            $list2['content']['c_idcard_img1']=GetHost().'/'.$data['c_idcard_img1'];
        }
        $list3['type']='营业执照';
        if(empty($data['c_charter_img']))
            $list3['content']['c_charter_img']='';
        else
            $list3['content']['c_charter_img']=GetHost().'/'.$data['c_charter_img'];
        $list4['type']='企业标志';
        if(empty($data['c_company_sign']))
            $list4['content']['c_company_sign']='';
        else
            $list4['content']['c_company_sign']=GetHost().'/'.$data['c_company_sign'];

        if($agent && $agent!==null){
            if($data['c_type']==2){ //企业
                return MessageInfo(0,'查询成功',[$list0,$list1,$list2,$list3,$list4]);
            }else{  //个人和个体户
                return MessageInfo(0,'查询成功',[$list0,$list1,$list2]);
            }
        }else{
            if($data['c_type']==2){ //企业
                return MessageInfo(0,'查询成功',[$list1,$list2,$list3,$list4]);
            }else{  //个人和个体户
                return MessageInfo(0,'查询成功',[$list1,$list2]);
            }
        }

    }


    /**
     * 查询代理商家个人资料
     * @param ucode,infoid
     */
    function GetAgentShopInfo($parr)
    {

        if (!empty($parr['infoid'])){
            $where['a.c_id'] = $parr['infoid'];
        }else{
            if (!empty($parr['ucode'])) {
                $where['a.c_ucode'] = $parr['ucode'];
            }
        }

        $join = 'left join t_users as b on a.c_ucode = b.c_ucode left join t_user_local as c on b.c_ucode = c.c_ucode left join t_shop_industry as d on b.c_shoptrade=d.c_id';
        $join .= ' left join t_shop_industry as f on f.c_id=b.c_shoptrade';
        $field = 'a.*,b.c_acode,b.c_headimg,d.c_name as industry_name,b.c_address1 as address1,c.c_longitude,c.c_latitude,c.c_isfixed,c.c_province,c.c_city,c.c_county,f.c_name as tradename,f.c_pid as tradepid';
        $data = M('Check_shopinfo as a')->join($join)->where($where)->field($field)->find();
        // $agent =M('Check_shopinfo')->where(array('c_ucode'=>$data['c_acode']))->field('c_name,c_phone')->find();


        $aa['b.c_ucode']=$data['c_ucode'];
        $join_agent = 'left join t_users as b on a.c_ucode=b.c_acode';
        //$w['b.c_ucode'] = $data['bcode'];
        $agent = M('Check_shopinfo as a')->join($join_agent)->where($aa)->field('a.c_name as c_name,a.c_phone as c_phone')->find();

        // $data['c_mchdealtype1']=$data['c_isfixed'];
        $data['agent_name']=$agent['c_name'];
        $data['agent_phone']=$agent['c_phone'];


        $data['c_company_sign'] =$data['c_company_sign']==null?'':GetHost().'/'.$data['c_company_sign'];
        $data['c_idcard_img'] =$data['c_idcard_img']==null?'':GetHost().'/'.$data['c_idcard_img'];
        $data['c_idcard_img1'] =$data['c_idcard_img1']==null?'':GetHost().'/'.$data['c_idcard_img1'];
        $data['c_charter_img'] =$data['c_charter_img']==null?'':GetHost().'/'.$data['c_charter_img'];
        $data['c_contractimg'] =$data['c_contractimg']==null?'':GetHost().'/'.$data['c_contractimg'];
        $data['c_storeimg'] =$data['c_storeimg']==null?'':GetHost().'/'.$data['c_storeimg'];
        $data['c_deskimg'] =$data['c_deskimg']==null?'':GetHost().'/'.$data['c_deskimg'];
        $data['c_storeinsideimg'] =$data['c_storeinsideimg']==null?'':GetHost().'/'.$data['c_storeinsideimg'];
        $data['c_charterpub_img'] =$data['c_charterpub_img']==null?'':GetHost().'/'.$data['c_charterpub_img'];

        $data['imgList']['c_idcard_img'] = $data['c_idcard_img']==null?'':$data['c_idcard_img'];
        $data['imgList']['c_idcard_img1'] = $data['c_idcard_img1']==null?'':$data['c_idcard_img1'];
        $data['imgList']['c_charter_img'] = $data['c_charter_img']==null?'':$data['c_charter_img'];
        $data['imgList']['c_company_sign'] = $data['c_company_sign']==null?'':$data['c_company_sign'];

        return MessageInfo(0,'查询成功',$data);
    }




    /**
     * 查询是否有个人资料
     */
    function CheckAgentInfo($parr){
        if (empty($parr['ucode'])) {
            return Message(1009,'验证登录失效，请重新登录再操作');
        }
        //查询是否有资料
        $where['c_ucode'] = $parr['ucode'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();

        if (!empty($angentinfo['c_dcode']) && $angentinfo['c_checked'] != 1) {
            return Message(2005,'资料已提交等待审核中，不能修改');
        }
        return MessageInfo(0,'查询成功',$angentinfo);
    }

    /**
     * 第一步
     */

    function First($parr){
        $result =$this->CheckAgentInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        if(empty($parr['ucode'])){
            return Message(1001,'参数缺失');
        }
        $type =$parr['type'];

        if($result['data']){  //有数据
            M('Check_shopinfo')->where(array('c_ucode'=>$parr['ucode']))->save(array('c_type'=>$type));
        }else{   //没数据
            $data['c_ucode']=$parr['ucode'];
            $data['c_type']=$type;
            $return =M('Check_shopinfo')->add($data);
            if(!$return){
                return Message(1001, '保存信息失败');
            }
        }

        return Message(0, '保存成功');
    }

    /**
     * 第二步
     */
    function Second($parr){
        $result =$this->CheckAgentInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        if(empty($parr['ucode'])){
            return Message(1001,'参数缺失');
        }
        if(empty($parr['name'])||empty($parr['phone'])||empty($parr['email'])||empty($parr['qq'])||empty($parr['idcard'])){
            return Message(1001,'请完善相关参数');
        }
        // $data['c_company'] =$parr['company'];
        //$data['c_address'] =$parr['address'];
        $data['c_name'] =$parr['name'];
        $data['c_phone'] =$parr['phone'];
        $data['c_email'] =$parr['email'];
        $data['c_qq'] =$parr['qq'];
        $data['c_idcard']=$parr['idcard'];
        $data['c_home_tel']=$parr['home_tel'];
        //$data['c_postcode']=$parr['postcode'];
        //$data['c_charter']=$parr['charter'];
        $where['c_ucode'] = $parr['ucode'];
        $type = M('Check_shopinfo')->where($where)->getField('c_type');
        if($type==2){
            $data['c_postcode'] = $parr['postcode'];
            $data['c_company'] = $parr['company'];
            $data['c_address'] = $parr['address'];
            $data['c_charter'] = $parr['charter'];
        }
        if(!empty($result['data'])){
            $result =M('Check_shopinfo')->where(array('c_ucode'=>$parr['ucode']))->save($data);
        }
//        if(!$result){
//            return Message(1001, '保存信息失败');
//        }
        return Message(0, '保存成功');

    }
    /**
     * 第三步
     */

    function Third($parr){
        $result =$this->CheckAgentInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        if(empty($parr['ucode'])){
            return Message(1001,'参数缺失');
        }
        if(empty($parr['fee_bank'])||empty($parr['fee_branch'])||empty($parr['fee_cardnum'])||empty($parr['fee_name'])||empty($parr['fee_alipay'])||empty($parr['fee_weixin'])){
            return Message(1001,'请完善相关参数');
        }
        $data['c_fee_bank'] =$parr['fee_bank'];
        $data['c_fee_branch'] =$parr['fee_branch'];
        $data['c_fee_cardnum']=$parr['fee_cardnum'];
        $data['c_fee_name']=$parr['fee_name'];
        $data['c_fee_alipay']=$parr['fee_alipay'];
        $data['c_fee_weixin']=$parr['fee_weixin'];

        if(!empty($result['data'])){
            $result =M('Check_shopinfo')->where(array('c_ucode'=>$parr['ucode']))->save($data);
        }
//        if(!$result){
//            return Message(1001, '保存信息失败');
//        }
        return Message(0, '保存成功');
    }

    /**
     * 第四步
     */
    function Fourth($parr){
        $result =$this->CheckAgentInfo($parr);
        if ($result['code'] != 0) {
            return $result;
        }
        if(empty($parr['ucode'])){
            return Message(1001,'参数缺失');
        }

        $data['c_dcode'] = CreateUcode('XWS');
        $data['c_idcard_img'] =$parr['idcard_img'];
        $data['c_idcard_img1'] =$parr['idcard_img1'];
        $data['c_charter_img']=$parr['charter_img'];
        $data['c_company_sign']=$parr['company_sign'];

        if ($result['data']['c_istore'] == 2) {
            $data['c_checked'] = 3;
        } else {
            $data['c_checked'] = 0;
        }


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
        $Id = $result['data']['c_id'];
        if(!empty($result['data'])){
            $result =M('Check_shopinfo')->where(array('c_ucode'=>$parr['ucode']))->save($data);
        }
//        if(!$result){
//            return Message(1001, '保存信息失败');
//        }

        // 创建审核消息
        if ($parr['istore'] == 1) {
            $parr1['ucode'] = M('Users')->where($where)->getField('c_acode');
            if ($parr['type'] == 2) {
                $parr1['ptitle'] = '企业【'.$parr['company'].'】申请商家,请点击查看,并做审核操作';
            } else {
                $parr1['ptitle'] = '个人【'.$parr['name'].'】申请商家,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
            $parr1['url'] = GetHost(3).'/agent.php/Agent/Shopcheck/details?Id='.$Id;
            $result = IGD('Infomation','Agent')->Create_information($parr1);
            if ($result['code'] != 0) {
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
                $result = IGD('Infomation','Agent')->Create_information($parr1);
                if ($result['code'] != 0) {
                    return Message(1000,'创建信息失败');
                }
            }
        }

        if ($parr['istore'] == 1) {
            //修改商家地理位置
            $result = IGD('Common','Agent')->EditShopLocal($parr);
            if ($result<0) {
                return Message(1000,'商家位置尚未修改！');
            }
        }

        // 发送短信通知
        $sewhere['c_ucode'] = $parr1['ucode'];
        $separr['telephone'] = M('Users')->where($sewhere)->getField('c_phone');
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = IGD('Login', 'Agent');
        $returndata = $register->SendVerify($separr);

        return Message(0, '保存成功');

    }

    /**
     * 添加与修改个人资料
     * @param ucode,type,istore,name,phone,email,qq,home_tel,(idcard,idcard_img)
     * ,(postcode,company,address,charter,charter_img,company_sign)
     *   ucode,lng,lat,isfixed,address,tid
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
                $parr1['ptitle'] = '企业【'.$parr['company'].'】申请商家,请点击查看,并做审核操作';
            } else {
                $parr1['ptitle'] = '个人【'.$parr['name'].'】申请商家,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
            $parr1['url'] = GetHost(3).'/agent.php/Agent/Shopcheck/details?Id='.$Id;
            $result = IGD('Infomation','Agent')->Create_information($parr1);
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
                $result = IGD('Infomation','Agent')->Create_information($parr1);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return Message(1000,'创建信息失败');
                }
            }
        }

        if ($parr['istore'] == 1) {
            //修改商家地理位置
            $result = IGD('Common','Agent')->EditShopLocal($parr);
            if ($result<0) {
                $db->rollback();
                return Message(1000,'商家位置尚未修改！');
            }

            // if ($parr['isfixed']==1) {
            //修改商家行业
            // $result = D('Common','Service')->EditIndustry($parr);
            // if ($result<0) {
            //     $db->rollback();
            //     return Message(1000,'商家行业信息尚未修改！');
            // }
            // }
        }

        // 发送短信通知
        $sewhere['c_ucode'] = $parr1['ucode'];
        $separr['telephone'] = M('Users')->where($sewhere)->getField('c_phone');
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = IGD('Login', 'Agent');
        $returndata = $register->SendVerify($separr);

        $db->commit();
        return Message(0,'提交成功');
    }

    /**
     * 保存收款相关信息
     * @param ucode,fee_bank,fee_branch,fee_cardnum,fee_name,fee_alipay,fee_weixin
     */
    function SaveBankInfo($parr)
    {
        //查询是否有资料
        $where['c_ucode'] = $parr['ucode'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();

        $data['c_fee_bank'] = $parr['fee_bank'];
        $data['c_fee_branch'] = $parr['fee_branch'];
        $data['c_fee_cardnum'] = $parr['fee_cardnum'];
        $data['c_fee_name'] = $parr['fee_name'];
        $data['c_fee_alipay'] = $parr['fee_alipay'];
        $data['c_fee_weixin'] = $parr['fee_weixin'];

        if ($angentinfo) {
            $data['c_updatetime'] = date('Y-m-d H:i:s');
            $result = M('Check_shopinfo')->where($where)->save($data);
        } else {
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $data['c_ucode'] = $parr['ucode'];
            $result = M('Check_shopinfo')->add($data);
        }

        if (!$result) {
            return Message('保存失败');
        }

        return Message(0,'保存成功');
    }

    /**
     * 市代审核商家
     * @param sid,(checked),ucode
     */
    function AgentCheckShop($parr)
    {
        $where['c_id'] = $parr['sid'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if ($angentinfo['c_checked'] != 0) {
            return Message('您已通过审核，不能再操作');
        }

        if ($parr['checked']==1) {
            $save['c_checked'] = 1;
        }elseif($parr['checked']==2){
            $save['c_checked'] = 2;
        }
        $db = M('');
        $db->startTrans();
        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(2004,'操作失败');
        }

        // 创建区代消息
        $join = 'left join t_invite_code as b on a.c_fcode=b.c_code';
        $agwhere['a.c_ucode'] = $parr['ucode'];
        $agucode = M('Invite_code as a')->join($join)->where($agwhere)->getField('b.c_ucode');
        if (!$agucode) {
            $db->rollback();
            return Message(2005,'代理商查询失败');
        }

        if ($angentinfo['c_type'] == 2)    {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/sub4_1?isfixed='.$angentinfo['c_isfixed'].'&ctype='.$angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='".$agucode."'")->getField('c_phone');
                $parr1['ptitle'] = '企业【'.$angentinfo['c_merchantname'].'】申请商家,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3).'/agent.php/Home/Shopcheck/details?Id='.$parr['sid'];
            }
        } elseif($angentinfo['c_type']==1) {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/sub4_1?isfixed='.$angentinfo['c_isfixed'].'&ctype='.$angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='".$agucode."'")->getField('c_phone');
                $parr1['ptitle'] = '个人【'.$angentinfo['c_merchantname'].'】申请商家,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3).'/agent.php/Home/Shopcheck/details?Id='.$parr['sid'];
            }
        } else {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/sub4_1?isfixed='.$angentinfo['c_isfixed'].'&ctype='.$angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='".$agucode."'")->getField('c_phone');
                $parr1['ptitle'] = '个体户【'.$angentinfo['c_merchantname'].'】申请商家,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3).'/agent.php/Home/Shopcheck/details?Id='.$parr['sid'];
            }
        }



        $result = IGD('Infomation','Agent')->Create_information($parr1);
        if ($result['code'] != 0) {
            $db->rollback();
            return Message(1000,'创建信息失败');
        }

        $db->commit();

        // 发送短信通知
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = IGD('Login', 'Agent');
        $returndata = $register->SendVerify($separr);

        //查询当前用户的上级信息

        $joins = 'left join t_users as b on b.c_acode=a.c_ucode';
        $where1['b.c_ucode'] = $parr['ucode'];
        $fields ='a.c_fee_name,a.c_phone,a.c_fee_bank,a.c_fee_cardnum,a.c_fee_alipay,a.c_fee_weixin';
        $upinfo = M('Check_shopinfo as a')->join($joins)->where($where1)->field($fields)->find();

        $list['c_desc']=$upinfo['c_fee_name']==null?'':$upinfo['c_fee_name'].'转款600元审核费用';
        $list['c_phone']=$upinfo['c_phone']==null?'':$upinfo['c_phone'];
        $list['c_fee_bank']=$upinfo['c_fee_bank']==null?'':$upinfo['c_fee_bank'];
        $list['c_fee_cardnum']=$upinfo['c_fee_cardnum']==null?'':$upinfo['c_fee_cardnum'];
        $list['c_fee_name']=$upinfo['c_fee_name']==null?'':$upinfo['c_fee_name'];
        $list['c_fee_alipay']=$upinfo['c_fee_alipay']==null?'':$upinfo['c_fee_alipay'];
        $list['c_fee_weixin']=$upinfo['c_fee_weixin']==null?'':$upinfo['c_fee_weixin'];
        return MessageInfo(0,'操作成功',$list);
    }

    /**
     * 区代审核商家
     * @param sid,(checked)
     */
    function CheckShop($parr)
    {
        $where['c_id'] = $parr['sid'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if ($angentinfo['c_checked'] != 2) {
            return Message(2002,'您还不能做审核操作');
        }
        if ($parr['checked']==1) {
            $save['c_checked'] = 1;
        }elseif($parr['checked']==2){
            $save['c_checked'] = 4;  // 4 等待平台审核
        }
        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($save);
        if (!$result) {
            return Message(2004,'操作失败');
        }
        return Message(0,'操作成功');

//        $where['c_id'] = $parr['sid'];
//        $shopData =M('Check_shopinfo')->where($where)->find();
//        if ($shopData['c_checked'] != 2) {
//            return Message(2002,'您还不能做审核操作');
//        }
//
//        $checked =$parr['checked'];
//
//        $db = M('');
//        $db->startTrans();
//
//        if ($checked==1) {
//            $save['c_checked'] = 1;
//        }elseif($checked==2){
//            $save['c_checked'] = 3;  // 通过
//        }
//        $save['c_updatetime'] = date('Y-m-d H:i:s');
//        $result = M('Check_shopinfo')->where($where)->save($save);
//        if (!$result) {
//            $db->rollback();
//            return Message(2004,'操作失败');
//        }
//
//        // 创建商家消息
//        if ($checked == 1) { //驳回
//            $separr['content'] = "【微领地小蜜】很抱歉，您提交的商家申请资料审核未通过，请核对资料后重新进行提交。";
//            $parr1['ptitle'] = '尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交';
//        } elseif($checked==2){
//            //改变商家状态
//            $whereadd['c_shop'] = 1;
//            $whereadd['c_num'] = 200;
//            $whereadd['c_invitationcode'] =  $this->CreateFcode();
//            $userwhere['c_ucode'] = $shopData['c_ucode'];
//            $result = M('Users')->where($userwhere)->save($whereadd);
//            if (!$result) {
//                $db->rollback();
//                return Message(2002,'改变商家状态失败');
//            }
//            $separr['content'] = "【微领地小蜜】您提交商家申请资料已通过审核，成为小蜜商家";
//            $parr1['ptitle'] = '恭喜！您提交的商家申请已通过审核';
//        }
//
//        $separr['telephone'] = $shopData['c_phone'];
//        $parr1['ucode'] = $shopData['c_ucode'];
//        $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/info_9?isfixed='.$shopData['c_isfixed'].'&ctype='.$shopData['c_type'];
//        $result = IGD('Infomation','Agent')->Create_information($parr1);
//        if ($result['code'] != 0) {
//            $db->rollback();
//            return Message(1000,'创建信息失败');
//        }
//        //查询拥有联盟身份同步激活
//        $rw['c_ucode'] = $shopData['c_ucode'];
//        $rw['c_type'] = 1;
//        $rw['c_status'] = 2;
//        $rw['c_sign'] = 2;
//        $roles = M('A_federation')->where($rw)->find();
//        if($roles) {    //同步激活连锁身份
//            $rolesave['c_status'] = 1;
//            $result = M('A_federation')->where($rw)->save($rolesave);
//            if (!$result) {
//                $db->rollback();
//                return Message(1002,'修改联盟信息失败');
//            }
//        }
//        // 发送短信通知
//        $separr['userid'] = C('TEl_USER');
//        $separr['account'] = C('TEl_ACCESS');
//        $separr['password'] = C('TEl_PASSWORD');
//        $register = IGD('Login', 'Agent');
//        $register->SendVerify($separr);
//
//        //商户资料进件
//        if ($checked == 2) {
//            //改变友收宝资料可提交状态
//            $upwhere['c_ucode'] = $shopData['c_ucode'];
//            $upsave['c_status'] = 1;
//            M('Merchant')->where($upwhere)->save($upsave);
//        }
//        $db->commit();
//
//        return Message(0,'操作成功');
    }

    /**
     * 平台审核
     *  ucode  用户code   checked  审核状态 1 驳回  3 通过
     */

    function AreaShenhe($parr){
        $ucode =$parr['ucode'];
        $checked =$parr['checked'];
        $remark =$parr['remark'];

        if(empty($ucode) ||empty($checked)){
            return Message(1001,'审核相关参数不能为空');
        }

        $where['c_ucode']=$ucode;
        $shopData =M('Check_shopinfo')->where($where)->find();

        $db = M('');
        $db->startTrans();
        $save['c_checked']=$checked;
        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(2004,'操作失败');
        }

        // 创建商家消息
        if ($checked == 1) { //驳回
            $separr['content'] = "【微领地小蜜】很抱歉，您提交的商家申请资料审核未通过，原因是：“".$remark."”，请核对资料后重新进行提交。";
            $parr1['ptitle'] = '尊敬的小蜜用户您好，您提交商家申请资料未审核通过，请核对资料后再提交';
        } elseif($checked==3){
            //改变商家状态
            $whereadd['c_shop'] = 1;
            $whereadd['c_num'] = 200;
            $whereadd['c_invitationcode'] =  $this->CreateFcode();
             $userwhere['c_ucode'] = $shopData['c_ucode'];
            $result = M('Users')->where($userwhere)->save($whereadd);
            if (!$result) {
                $db->rollback();
                return Message(2002,'改变商家状态失败');
            }
            $separr['content'] = "【微领地小蜜】由于:".$remark."，您提交商家申请资料已通过审核，成为小蜜商家";
            $parr1['ptitle'] = '恭喜！您提交的商家申请已通过审核';
        }

        $separr['telephone'] = $shopData['c_phone'];
        $parr1['ucode'] = $shopData['c_ucode'];
        $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/info_9?isfixed='.$shopData['c_isfixed'].'&ctype='.$shopData['c_type'];
        $result = IGD('Infomation','Agent')->Create_information($parr1);
        if ($result['code'] != 0) {
            $db->rollback();
            return Message(1000,'创建信息失败');
        }
        //查询拥有联盟身份同步激活
        $rw['c_ucode'] = $shopData['c_ucode'];
        $rw['c_type'] = 1;
        $rw['c_status'] = 2;
        $rw['c_sign'] = 2;
        $roles = M('A_federation')->where($rw)->find();
        if($roles) {    //同步激活连锁身份
            $rolesave['c_status'] = 1;
            $result = M('A_federation')->where($rw)->save($rolesave);
            if (!$result) {
                $db->rollback();
                return Message(1002,'修改联盟信息失败');
            }
        }
        // 发送短信通知
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = IGD('Login', 'Agent');
        $register->SendVerify($separr);

        //商户资料进件
        if ($checked == 2) {
            //改变友收宝资料可提交状态
            $upwhere['c_ucode'] = $shopData['c_ucode'];
            $upsave['c_status'] = 1;
            M('Merchant')->where($upwhere)->save($upsave);
        }

        $db->commit();

        return Message(0,'平台审核成功');


    }


    /**
     * 生成不重复的邀请码
     */
    function CreateFcode()
    {
        $fcode = random(8);
        $where['c_invitationcode'] = $fcode;
        $where['c_shop'] = 1;
        $result = M('Users')->where($where)->getField('c_id');
        if ($result) {
            $this->CreateFcode();
        }
        return $fcode;
    }

}
