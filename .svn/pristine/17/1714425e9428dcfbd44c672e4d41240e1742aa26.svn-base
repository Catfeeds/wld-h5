<?php
namespace Agent\Controller;

use Base\Controller\BaseController;
/**
 *  公告控制器
 */
class ChuandataController extends BaseController{

    public function userData(){

        $field ='b.*,a.c_carid,a.c_bankname,a.c_uname,a.c_banksn,a.c_wxcard,a.c_alipaycard,a.c_sub_bankname,a.c_province as bprovince,a.c_city as bcity';
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $list = M('Users_bank as a')->join($join)->field($field)->select();
        $i=0;
        foreach($list as $value){
            $insert['c_ucode']=$value['c_ucode'];
            $insert['c_isagent']=$value['c_isagent']==0?0:1;
            $insert['c_isdataall']=0;
            $insert['c_isshop']=$value['c_shop'];
            $insert['c_cardtype']="00";
            $insert['c_province']=$value['c_province'];
            $insert['c_city']=$value['c_city'];
            $insert['c_address']=$value['c_address1'];
            $insert['c_username']=$this->CreateUserName();
            $insert['c_bankno']=$value['c_banksn'];
            $insert['c_bankuser']=$value['c_uname'];
            $insert['c_bankname']=$value['c_bankname'];
            $insert['c_bankallname']=$value['c_bankname'];
            $insert['c_bankcity']=$value['bcity'];
            $insert['c_person']=$value['c_uname'];
            $insert['c_personphone']=$value['c_phone'];
            $insert['c_personidcard']=$value['c_carid'];
            $insert['c_alipay']=$value['c_wxcard'];
            $insert['c_weixin']=$value['c_alipaycard'];
            $insert['c_addtime'] =date('Y-m-d H:i:s');
            $res =M('User_yspay')->where(['c_ucode'=>$value['c_ucode']])->find();
            if(empty($res)){
                $result =M('User_yspay')->add($insert);
                if($result){
                    echo "success".$i++;
                }else{
                    echo "fail";
                }
            }
        }
    }

    public function shopData(){
        $field ='a.*,b.c_isagent,c.c_isfixed,c.c_province,c.c_city';
        $join1 ='left join t_users as b on a.c_ucode=b.c_ucode';
        $join ='left join t_user_local as c on a.c_ucode=c.c_ucode';
        $list = M('Check_shopinfo as a')->join($join)->join($join1)->field($field)->select();
        $i=0;
        foreach($list as $value){
            $insert['c_ucode']=$value['c_ucode'];
            $insert['c_isagent']=$value['c_isagent']==0?0:1;
            $insert['c_isdataall']=0;
            $insert['c_isshop']=1;
            $insert['c_openaccount'] =0;
            $insert['c_legalname']=$value['c_legalperson'];
            $insert['c_email']=$value['c_email'];
            $insert['c_qq']=$value['c_qq'];
            $insert['c_home_tel']=$value['c_home_tel'];
            $insert['c_company']=$value['c_company'];
            $insert['c_merchant']=$value['c_merchantname'];
            $insert['c_merchantshot']=$value['c_merchantshortname'];
            $insert['c_feeltype'] =$value['c_feetype'];
            $insert['c_chartertype']=$value['c_chartertype'];
            $insert['c_charterno']=$value['c_charter'];
            $insert['c_charterendtime']=$value['c_charterendtime'];
            $insert['c_province']=$value['c_province'];
            $insert['c_city']=$value['c_city'];
            $insert['c_address']=$value['c_address1'];
            $insert['c_username']=$this->CreateUserName();
            $insert['c_creditlimit']=0;
            $insert['c_bankno']=$value['c_fee_cardnum'];
            $insert['c_bankuser']=$value['c_fee_name'];
            $insert['c_bankname']=$value['c_fee_bank'];
            $insert['c_banktype']=$value['c_accounttype'];
            $insert['c_bankallname']=$value['c_fee_bank'];
            $insert['c_bankbranch']=$value['c_bankname'];
            $insert['c_bankcity']=$value['c_bankcity'];
            $insert['c_storetype']=$value['c_isfixed'];   //商家类型 线上 0  线下 1
            $insert['c_storetials']=$value['c_type'];   //商家资质 个人 1 企业 2
            $insert['c_industry']='';
            $insert['c_bankcity']=$value['c_bankcity'];
            $insert['c_person']=$value['c_name'];
            $insert['c_personphone']=$value['c_phone'];
            $insert['c_personidcard']=$value['c_idcardinfo'];
            $insert['c_personidcardendtime']=$value['c_idcardendtime'];
            $insert['c_phone']=$value['c_legalphone'];
            $insert['c_cardtype']=$value['c_idcardtype'];
            $insert['c_legalcardno']=$value['c_idcardinfo'];
            $insert['c_legalcardendtime']=$value['c_legalcardendtime'];
            $insert['c_alipay']=$value['c_fee_alipay'];
            $insert['c_weixin']=$value['c_fee_weixin'];
            $insert['c_charter_img']=$value['c_charter_img'];
            $insert['c_idcard_img']=$value['c_idcard_img'];
            $insert['c_idcard_img1']=$value['c_idcard_img1'];
            $insert['c_bankcard_img']=$value['c_bankcardimg'];
            $insert['c_bankcard_img1']=$value['c_bankcardimg1'];
            $insert['c_charterpub_img']='';
            $insert['c_addtime'] =date('Y-m-d H:i:s');
            $res =M('User_yspay')->where(['c_ucode'=>$value['c_ucode']])->find();
            if(empty($res)){
                $result =M('User_yspay')->add($insert);
                if($result){
                    echo "success".$i++;
                }else{
                    echo "fail";
                }
            }
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
        return $prefix .'_'. $uuid;
    }

}