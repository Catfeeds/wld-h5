<?php

namespace Common\Service;
/**
 *  代理商，商家资料提交审核
 */
class AgentService
{

    /**
     * 查询商家个人资料
     * @param ucode ,infoid
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
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 添加与修改个人资料
     * @param ucode ,type,istore,name,phone,email,qq,home_tel,(idcard,idcard_img)
     * ,(postcode,company,address,charter,charter_img,company_sign)
     *   ucode,lng,lat,isfixed,address,tid
     */
    function SaveAgentInfo($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009, '验证登录失效，请重新登录再操作');
        }
        //查询是否有资料
        $where['c_ucode'] = $parr['ucode'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if (!empty($angentinfo['c_dcode']) && $angentinfo['c_checked'] != 1) {
            return Message(2005, '资料已提交等待审核中，不能修改');
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
                $parr1['ptitle'] = '企业【' . $parr['company'] . '】申请商家,请点击查看,并做审核操作';
            } else {
                $parr1['ptitle'] = '个人【' . $parr['name'] . '】申请商家,请点击查看,并做审核操作';
            }
            $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
            $parr1['url'] = GetHost(3) . '/agent.php/Agent/Shopcheck/details?Id=' . $Id;
            $result = D('Infomation', 'Service')->Create_information($parr1);
            if ($result['code'] != 0) {
                $db->rollback();
                return Message(1000, '创建信息失败');
            }

        } else {
            $pfcode = M('Invite_code')->where($where)->getField('c_fcode');
            if (!empty($pfcode)) {
                $pfwhere['c_code'] = $pfcode;
                $parr1['ucode'] = M('Invite_code')->where($pfwhere)->getField('c_ucode');
                if ($parr['type'] == 2) {
                    $parr1['ptitle'] = '您有新的企业【' . $parr['company'] . '】代理,已提交资料';
                } else {
                    $parr1['ptitle'] = '您有新的个人【' . $parr['name'] . '】代理,已提交资料';
                }
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的代理提交资料";
                $parr1['url'] = GetHost(3) . '/agent.php/Home/Agentntrol/index';
                $result = D('Infomation', 'Service')->Create_information($parr1);
                if ($result['code'] != 0) {
                    $db->rollback();
                    return Message(1000, '创建信息失败');
                }
            }
        }

        if ($parr['istore'] == 1) {
            //修改商家地理位置
            $result = D('Common', 'Service')->EditShopLocal($parr);
            if ($result < 0) {
                $db->rollback();
                return Message(1000, '商家位置尚未修改！');
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
        $register = D('Login', 'Service');
        $returndata = $register->SendVerify($separr);

        $db->commit();
        return Message(0, '提交成功');
    }

    /**
     * 保存收款相关信息
     * @param ucode ,fee_bank,fee_branch,fee_cardnum,fee_name,fee_alipay,fee_weixin
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

        return Message(0, '保存成功');
    }

    /**
     * 市代审核商家
     * @param sid ,(checked),ucode
     */
    function AgentCheckShop($parr)
    {
        $where['c_id'] = $parr['sid'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if ($angentinfo['c_checked'] != 0) {
            return Message('您已通过审核，不能再操作');
        }

        if (empty($parr['checked'])) {
            $save['c_checked'] = 1;
        } else {
            $save['c_checked'] = 2;
        }
        $db = M('');
        $db->startTrans();
        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(2004, '操作失败');
        }

        /*查询区域经理信息*/
        $agwhere['c_ucode'] = $parr['ucode'];
        $agucode = M('Users')->where($agwhere)->getField('c_acode');
        if (!$agucode) {
            $db->rollback();
            return Message(2005, '代理商信息查询失败');
        }

        // 创建区代消息
//        $join = 'left join t_invite_code as b on a.c_fcode=b.c_code';
//        $agwhere['a.c_ucode'] = $parr['ucode'];
//        $agucode = M('Invite_code as a')->join($join)->where($agwhere)->getField('b.c_ucode');
//        if (!$agucode) {
//            $db->rollback();
//            return Message(2005,'代理商查询失败');
//        }

        if ($angentinfo['c_type'] == 2) {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3) . '/agent.php/Shop/Personal/sub4_1?isfixed=' . $angentinfo['c_isfixed'] . '&ctype=' . $angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='" . $agucode . "'")->getField('c_phone');
                $parr1['ptitle'] = '企业【' . $angentinfo['c_merchantname'] . '】申请商家,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3) . '/agent.php/Home/Shopcheck/details?Id=' . $parr['sid'];
            }
        } else if ($angentinfo['c_type'] == 1) {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3) . '/agent.php/Shop/Personal/sub4_1?isfixed=' . $angentinfo['c_isfixed'] . '&ctype=' . $angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='" . $agucode . "'")->getField('c_phone');
                $parr1['ptitle'] = '个人【' . $angentinfo['c_merchantname'] . '】申请商家,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3) . '/agent.php/Home/Shopcheck/details?Id=' . $parr['sid'];
            }
        } else {
            if ($save['c_checked'] == 1) {
                $parr1['ucode'] = $angentinfo['c_ucode'];
                $separr['telephone'] = $angentinfo['c_phone'];
                $parr1['ptitle'] = "尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
                $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
                $parr1['url'] = GetHost(3) . '/agent.php/Shop/Personal/sub4_1?isfixed=' . $angentinfo['c_isfixed'] . '&ctype=' . $angentinfo['c_type'];
            } else {
                $parr1['ucode'] = $agucode;
                $separr['telephone'] = M('Users')->where("c_ucode='" . $agucode . "'")->getField('c_phone');
                $parr1['ptitle'] = '个体户【' . $angentinfo['c_merchantname'] . '】申请商家,请点击查看,并做审核操作';
                $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您有新的商家提交资料，请尽快登录后台做审核";
                $parr1['url'] = GetHost(3) . '/agent.php/Home/Shopcheck/details?Id=' . $parr['sid'];
            }
        }

        $result = D('Infomation', 'Service')->Create_information($parr1);
        if ($result['code'] != 0) {
            $db->rollback();
            return Message(1000, '创建信息失败');
        }

        $db->commit();

        // 发送短信通知
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = D('Login', 'Service');
        $returndata = $register->SendVerify($separr);
        return Message(0, '操作成功');
    }

    /**
     * 区代审核商家
     * @param sid ,(checked)
     */
    function CheckShop($parr)
    {
        $where['c_id'] = $parr['sid'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if ($angentinfo['c_checked'] != 2) {
            return Message(2002, '您还不能做审核操作');
        }
        if (empty($parr['checked'])) {
            $save['c_checked'] = 1;

        } else {
            $save['c_checked'] = 4;
        }
        $db = M('');
        $db->startTrans();
        $save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Check_shopinfo')->where($where)->save($save);
        if (!$result) {
            return Message(2004, '操作失败');
        }
        if ($save['c_checked'] == 1) {
            $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
            $parr1['ptitle'] = '尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交';
        } else {
            $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交的商家申请资料已通过审核请等待平台审核";
            $parr1['ptitle'] = '尊敬的小蜜用户您好，您提交的商家申请资料已通过审核请等待平台审核';
        }
        $separr['telephone'] = $angentinfo['c_phone'];
        $parr1['ucode'] = $angentinfo['c_ucode'];
        $parr1['url'] = GetHost(3) . '/agent.php/Shop/Personal/info_9?isfixed=' . $angentinfo['c_isfixed'] . '&ctype=' . $angentinfo['c_type'];
        $result = D('Infomation', 'Service')->Create_information($parr1);
        if ($result['code'] != 0) {
            $db->rollback();
            return Message(1000, '创建信息失败');
        }
        $db->commit();
        // 发送短信通知
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = D('Login', 'Service');
        $returndata = $register->SendVerify($separr);
        return Message(0, '操作成功');

//       $where['c_id'] = $parr['sid'];
//        $angentinfo = M('Check_shopinfo')->where($where)->find();
//        if ($angentinfo['c_checked'] != 2) {
//            return Message(2002,'您还不能做审核操作');
//        }
//
//        if (empty($parr['checked'])) {
//            $save['c_checked'] = 1;
//
//        } else {
//            $save['c_checked'] = 4;
//        }
//
//        $db = M('');
//        $db->startTrans();
//        $save['c_updatetime'] = date('Y-m-d H:i:s');
//        $result = M('Check_shopinfo')->where($where)->save($save);
//        if (!$result) {
//            $db->rollback();
//            return Message(2004,'操作失败');
//        }
//
//        // 创建商家消息
//        if ($save['c_checked'] == 1) {
//            $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交";
//            $parr1['ptitle'] = '尊敬的小蜜用户您好，您提交的商家申请资料未审核通过，请核对资料后再提交';
//        } else {
//            //改变商家状态
//            $whereadd['c_shop'] = 1;
//            $whereadd['c_num'] = 200;
//            $whereadd['c_invitationcode'] =  $this->CreateFcode();
//            $userwhere['c_ucode'] = $angentinfo['c_ucode'];
//            $result = M('Users')->where($userwhere)->save($whereadd);
//            if (!$result) {
//                $db->rollback();
//                return Message(2002,'改变商家状态失败');
//            }
//            $separr['content'] = "【微领地小蜜】尊敬的小蜜用户您好，您提交的商家申请资料已通过审核，成为小蜜商家";
//            $parr1['ptitle'] = '恭喜！您提交的商家申请已通过审核';
//        }
//
//        $separr['telephone'] = $angentinfo['c_phone'];
//        $parr1['ucode'] = $angentinfo['c_ucode'];
//        $parr1['url'] = GetHost(3).'/agent.php/Shop/Personal/info_9?isfixed='.$angentinfo['c_isfixed'].'&ctype='.$angentinfo['c_type'];
//        $result = D('Infomation','Service')->Create_information($parr1);
//        if ($result['code'] != 0) {
//            $db->rollback();
//            return Message(1000,'创建信息失败');
//        }
//
//
//        //查询拥有联盟身份同步激活
//        $rw['c_ucode'] = $angentinfo['c_ucode'];
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
//
//
//        // 发送短信通知
//        $separr['userid'] = C('TEl_USER');
//        $separr['account'] = C('TEl_ACCESS');
//        $separr['password'] = C('TEl_PASSWORD');
//        $register = D('Login', 'Service');
//        $returndata = $register->SendVerify($separr);
//
//        //商户资料进件
//        if ($save['c_checked'] == 3) {
//            //改变友收宝资料可提交状态
//            $upwhere['c_ucode'] = $angentinfo['c_ucode'];
//            $upsave['c_status'] = 1;
//            $upayinfo = M('Merchant')->where($upwhere)->save($upsave);
//
//            //$uparr['ucode'] = $angentinfo['c_ucode'];
//            //$result = IGD('Upay','Scanpay')->PostAddmerchant($uparr);
//            //if ($result['code'] != 0) {
//                // return $result;
//            //}
//        }
//
//        $db->commit();
//
//        return Message(0,'操作成功');
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
