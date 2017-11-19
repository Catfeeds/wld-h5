<?php

/**
 * 	首页邀请弹窗接口
 */
class AskApp {

    /**
     * 获取邀请信息
     * @param ucode
     */
    function GetAsk($parr)
    {
        if(empty($parr['ucode'])){
            return Message(3000,'没有相关信息');
        }
        $asw['c_read'] = 0;
        $asw['c_ucode'] = $parr['ucode'];
        $data1 = M('A_askinfo')->where($asw)->order('c_id desc')->find();
        $ucode =$parr['ucode'];
        if ($data1) {
            switch ($data1['c_type']) {
                case '1':  //邀请收银员
                    $rightkey = GetHost(1) . '/index.php/Home/Cashier/ask?askid='.$data1['c_id'];
                    break;
                case '2':  //邀请加盟
                    $rightkey = GetHost(1) . '/index.php/Store/Leagshop/jcheck?askid='.$data1['c_id'];
                    break;
                default:
                    $rightkey = GetHost(1) . '/index.php/Home/Cashier/ask?askid='.$data1['c_id'];
                    break;
            }
            $back =$this->check($ucode);
            $data['leftname'] = '稍后查看';
            $data['leftact'] = '3';
            $data['leftkey'] = '10000';
            $data['rightname'] = '立即查看';
            $data['rightact'] = '2';
            $data['rightkey'] = $rightkey;
            $data['flag'] =$back['flag'];
            $data['msg'] =$back['msg'];
            return MessageInfo($back['code'],'查询成功',$data);

        } else {
            $where['c_ucode'] = $parr['ucode'];
            //判断用户身份信息
            $isshop =  M('Users')->where($where)->getField('c_shop');
            if($isshop == 1){
                $where['c_type'] = 2;
                $data = M('Circle_shop')->where($where)->find();
                if (!$data) {
                    $rightkey = GetHost(1) . '/index.php/Trade/Index/joincircle';
                    $data['c_desc'] = '亲爱的小蜜商家，您还未加入任何商圈，赶快加入吧！';
                    $data['leftname'] = '稍后加入';
                    $data['leftact'] = '3';
                    $data['leftkey'] = '10000';
                    $data['rightname'] = '立即加入';
                    $data['rightact'] = '2';
                    $data['rightkey'] = $rightkey;
                    return MessageInfo(0,'查询成功',$data);                     
                } else {
                    $back =$this->check($ucode);
                    $ex =str_replace(".","",$parr['app_version']);
                    if($ex<304){
                        if ($back['code'] == 0) {
                            $rightkey = GetHost(1) . '/index.php/Home/Getbusiness/index?type=1';
                            $data['c_desc'] = '为了加强您的账户安全，请尽快完成商家信息认证';
                            $data['leftname'] = '稍后再去';
                            $data['leftact'] = '3';
                            $data['leftkey'] = '10000';
                            $data['rightname'] = '马上认证';
                            $data['rightact'] = '2';
                            $data['rightkey'] = $rightkey;
                            $data['flag'] =$back['flag'];
                            $data['msg'] =$back['msg'];
                            return MessageInfo($back['code'],'查询成功',$data);
                        }
                    }      
                }
            }   
        }

        return Message(3000,'没有相关信息');
    }

    function check($ucode){

        $user =M('Users')->where(array('c_ucode'=>$ucode))->find();
        $Info =M('Check_shopinfo')->where(array('c_ucode'=>$ucode))->find();

        if($user['c_shop']==0 || $user['c_isagent']!=0 || !$Info){
            return MessageInfo(1001,'您还不是商家',0);
        }

        switch($Info['c_type']){
            case 1:
                if(empty($Info['c_idcard_img']) || empty($Info['c_bankcardimg']) ||empty($Info['c_bankcardimg1'])){
                    $data['flag'] = 1; //需要完善资料
                    $data['msg'] ="需要完善资料";
                }else{
                    $data['flag'] = 2; //资料完整
                    $data['msg'] ="资料完整";
                }
                break;
            case 2:
                if(empty($Info['c_idcard_img']) || empty($Info['c_charter_img']) ||empty($Info['c_charterpub_img'])){
                    $data['flag'] = 1; //需要完善资料
                    $data['msg'] ="需要完善资料";
                }else{
                    $data['flag'] = 2; //资料完整
                    $data['msg'] ="资料完整";
                }
                break;
            case 3:
                if(empty($Info['c_idcard_img']) || empty($Info['c_charter_img']) ||empty($Info['c_bankcardimg'])){
                    $data['flag'] = 1; //需要完善资料
                    $data['msg'] ="需要完善资料";
                }else{
                    $data['flag'] = 2; //资料完整
                    $data['msg'] ="资料完整";
                }
                break;
        }
        $parr['ucode'] = $ucode;
        $upayinfo = IGD('Yspay','Scanpay')->FindYspayInfo($parr);
        if ($upayinfo['code']==0 && $upayinfo['data']['c_openaccount'] != 2) {
            $data['flag'] = 2; //资料完整
            $data['msg'] ="资料完整";
        }else{
            $data['flag'] = 1; //需要完善资料
            $data['msg'] ="需要完善资料";
        }

        if($data['flag']==1){
            $data['code'] = 0;
        }else{
            $data['code'] =1002;
        }
        return $data;
    }

    /**
     * 获取邀请详情
     * @param askid,ucode
     */
    function GetAskInfo($parr)
    {
        //读取邀请信息
        $this->ReadAskinfo($parr);

        $asw['c_id'] = $parr['askid'];
        $asw['c_ucode'] = $parr['ucode'];
        $data = M('A_askinfo')->where($asw)->order('c_id desc')->find();
        if (!$data) {
            return Message(3000,'没有相关信息');
        }
        $data['c_data'] = objarray_to_array(json_decode($data['c_data']));

        //查询邀请人信息
        $shpw['c_ucode'] = $data['c_acode'];
        $field = 'c_nickname,c_headimg,c_signature,c_phone,c_address1,c_shoptrade';
        $shopinfo = M('Users')->where($shpw)->field($field)->find();
        $shopinfo['c_headimg'] = GetHost().'/'.$shopinfo['c_headimg'];

        $shopinfo['tradename'] = M('Shop_industry')->where(array('c_id'=>$shopinfo['c_shoptrade']))->getField('c_name');

        $data['shopinfo'] = $shopinfo;
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 读取邀请信息
     * @param sid
     */
    function ReadAskinfo($parr)
    {
        $asw['c_id'] = $parr['askid'];
        $asw['c_ucode'] = $parr['ucode'];
        $save['c_read'] = 1;
        $save['c_readtime'] = gdtime();
        $data = M('A_askinfo')->where($asw)->save($save);
        // if ($data) {
        // 	return Message(3000,'读取失败');
        // }

        return Message(0,'读取成功');
    }

}








?>