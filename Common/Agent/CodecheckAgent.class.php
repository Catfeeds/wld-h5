<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/24
 * Time: 16:28
 */

class CodecheckAgent {

    /**
     * 获取代理商激活码头部数量
     * @param ucode
     */
    function GetCodenum($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_rule'] = 2;
        $data = M('Invite_code')->where($where)->find();

        $con['c_acode'] =$parr['ucode'];
        $con['c_state']=2;
        $data['count'] =M('Check_codelist')->where($con)->count();
        return MessageInfo(0,'查询成功',$data);
    }


    /**
     * 补齐  串码拥有数 和 串码可使用数
     *
     */
    function GetDiff($parr){
        $con['c_ucode'] =$parr['ucode'];
        $data =M('Invite_code')->where($con)->find();
        return MessageInfo(0,'查询成功',$data);
    }


    function GetUnshencount($parr){

        $ucode = $parr['ucode'];

        $join = 'left join t_check_shopinfo as b on b.c_ucode = a.c_ucode left join t_users as c on c.c_ucode=a.c_ucode';
        $where['a.c_ucode'] = array('eq',$ucode);
        $where['a.c_state'] = array('eq',1);
        $where['b.c_checked'] =array('neq',3);
        $data = M('Check_codelist as a')->join($join)->where($where)->count();
        return $data?$data:0;

    }

    /**
     *  获取首页未读状态标志
     *  @param ucode
     */
    function GetStatuMessage($parr)
    {
        $ucode = $parr['ucode'];
        $join = 'left join t_check_infolog as b on b.c_infoid=a.c_id';
        $whereinfo[] = array("a.c_ucode='' or a.c_ucode is null or a.c_ucode='$ucode'");
        $whereinfo[] = array("b.c_infoid is null");
        $whereinfo1[] = array("a.c_ucode='$ucode'");
        $whereinfo1[] = array("b.c_infoid is null");
        $data['publicnum'] = M('Check_info as a')->join($join)->where($whereinfo)->count();
        if (!$data['publicnum']) {
            $data['publicnum'] = 0;
        }

        $data['checknum'] = M('Check_info as a')->join($join)->where($whereinfo1)->count();
        if (!$data['checknum']) {
            $data['checknum'] = 0;
        }
        return MessageInfo(0,'查询成功',$data);
    }


    /**
     * 区代查询激活码审核信息列表
     * @param pageindex,pagesize,ucode
     */
    function GetCodeCheckList($parr)
    {
        $ucode = $parr['ucode'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $join = 'left join t_users as b on b.c_ucode=a.c_acode';
        $where['a.c_ucode'] = $ucode;
        $order = 'a.c_state desc,a.c_id desc';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $list = M('Check_codeinfo as a')->join($join)->where($where)->order($order)->limit($countPage, $pageSize)->field($field)->select();

        $count = M('Check_codeinfo as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0,'查询成功',$data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 区代查询审核数目信息
     * @param ucode
     */
    function GetCheckNum($parr)
    {
        // 已审核数目
        $where['c_ucode'] = $parr['ucode'];
        $where['c_state'] = 1;
        $checked = M('Check_codeinfo')->where($where)->sum('c_num');

        //未审核数量
        $where['c_state'] = 2;
        $nochecked = M('Check_codeinfo')->where($where)->sum('c_num');

        $data['checked'] = ($checked<=0)?0:$checked;
        $data['nochecked'] = ($nochecked<=0)?0:$nochecked;
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 区代审核激活码
     * @param ucode,cid,isfree(1已收费,2未收费)
     */
    function CheckCode($parr)
    {
        $isfree = $parr['isfree'];

        //查询审核信息
        $where['c_ucode'] = $parr['ucode'];
        $where['c_id'] = $parr['cid'];
        $where['c_state'] = 2;
        $data = M('Check_codeinfo')->where($where)->find();
        if (!$data) {
            return Message(3000,'该审核信息不存在');
        }

        //查询市代信息
        $userwhere['c_ucode'] = $data['c_acode'];
        $userinfo = M('Users')->where($userwhere)->find();
        if (!$userinfo) {
            return Message(3001,'代理信息不存在');
        }

        //查询市代串码信息
        $inwhere['c_ucode'] = $userinfo['c_ucode'];
        $inwhere['c_rule'] = 2;
        $invidata = M('Invite_code')->where($inwhere)->find();
        if (!$invidata) {
            return Message(3002,'代理信息不存在');
        }

        $db = M('');
        $db->startTrans();

        //修改审核信息
        $checksave['c_state'] = 1;
        $checksave['c_isfree'] = $isfree;
        $checksave['c_checktime'] = date('Y-m-d H:i:s');
        $result = M('Check_codeinfo')->where($where)->save($checksave);
        if (!$result) {
            $db->rollback();
            return Message(3003,'审核失败');
        }

        //修改市代拥有串码数量
        $insave['c_checknum'] = $invidata['c_checknum'] - $data['c_num'];
        $insave['c_codenum'] = $invidata['c_codenum'] + $data['c_num'];
        $result = M('Invite_code')->where($inwhere)->save($insave);
        if (!$result) {
            $db->rollback();
            return Message(3004,'修改串码数量失败');
        }

        //创建公告
        $parr1['ucode'] = $userinfo['c_ucode'];
        $parr1['ptitle'] = '您提交激活串码已经审核通过，可以前往串码中心发放串码给商家激活啦~';
        $parr1['url'] = GetHost(3).'/agent.php/Agent/Codecheck/index';
        $result = IGD('Infomation','Agent')->Create_information($parr1);
        if ($result['code'] != 0) {
            $db->rollback();
            return Message(1000,'创建信息失败');
        }

        // 发送短信通知
        $separr['telephone'] = $userinfo['c_phone'];
        $separr['content'] = "【微领地小蜜】尊敬的小蜜代理商您好，您提交激活串码已经审核通过，可以前往商家后台发放串码给商家激活啦~";
        $separr['userid'] = C('TEl_USER');
        $separr['account'] = C('TEl_ACCESS');
        $separr['password'] = C('TEl_PASSWORD');
        $register = IGD('Login', 'Sendmsg');
        $returndata = $register->SendVerify($separr);
        $db->commit();
        return Message(0,'审核成功');
    }


    /**
     * 市代查询激活码列表
     * @param pageindex,pagesize,ucode,type(1已激活的,2未激活的)
     */
    function GetCodeInfoList($parr)
    {
        $ucode = $parr['ucode'];
        $type = $parr['type'];
        if (empty($ucode)) {
            return Message(1009,'登录失效');
        }

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $join = 'left join t_users as b on b.c_ucode=a.c_ucode';
        $where['a.c_acode'] = $ucode;
        if ($type == 1) {
            $where['a.c_state'] = 1;
        } else {
            $where['a.c_state'] = 2;
        }
        $order = 'a.c_id desc';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $list = M('Check_codelist as a')->join($join)->where($where)->order($order)->limit($countPage, $pageSize)->field($field)->select();

        $count = M('Check_codelist as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0,'查询成功',$data);
        }

        foreach ($list as $key => $value) {
            if ($value['c_headimg']) {
                $list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
            }else{
                $list[$key]['c_headimg'] = '';
            }
            if (!$value['c_nickname']) {
                $list[$key]['c_nickname'] = '';
            }
            $list[$key]['share_title'] = '小蜜—商家账号注册激活';
            $list[$key]['share_des'] = '一款帮助微个体经营个人流量的APP';
            $list[$key]['share_img'] = 'https://m.weilingdi.com/Resource/Common/logo.png';
            $list[$key]['share_url'] = GetHost(1).'/agent.php/Home/Login/shopagent?num='.$value['c_code'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 市代查询待审核激活码列表
     * @param pageindex,pagesize,ucode,type(1已激活的,2未激活的)
     */

    function GetCodeUnshenList($parr){

        $ucode = $parr['ucode'];
        $type = $parr['type'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $join = 'left join t_check_shopinfo as b on b.c_ucode = a.c_ucode left join t_users as c on c.c_ucode=a.c_ucode';
        $where['a.c_ucode'] = array('eq',$ucode);
        $where['a.c_state'] = array('eq',1);
        $where['b.c_checked'] =array('neq',3);
        $order = 'a.c_id desc';
        $field = 'a.*,c.c_nickname,c.c_headimg,b.c_checked';
        $list = M('Check_codelist as a')->join($join)->where($where)->order($order)->limit($countPage, $pageSize)->field($field)->select();
        $count = M('Check_codelist as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0,'查询成功',$data);
        }

        foreach ($list as $key => $value) {
            if ($value['c_headimg']) {
                $list[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
            } else {
                $list[$key]['c_headimg'] = '';
            }
            if (!$value['c_nickname']) {
                $list[$key]['c_nickname'] = '';
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$data);

    }



    /**
     * 市代申请串码
     * @param ucode,num
     */
    function ApplyCode($parr)
    {
        $ucode = $parr['ucode'];
        $num = $parr['num'];

        // 获取区代编码
        $uw['c_ucode'] = $ucode;
        $acode = M('Users')->where($uw)->getField('c_acode');

        //查询区代信息
        $userwhere['c_isagent'] = 1;
        $userwhere['c_ucode'] = $acode;
        $userinfo = M('Users')->where($userwhere)->find();
        if (empty($userinfo)) {
            return Message(3000,'代理信息不存在1');
        }

        //查询市代串码信息
        $inwhere['c_ucode'] = $ucode;
        $inwhere['c_rule'] = 2;
        $invidata = M('Invite_code')->where($inwhere)->find();
        if (empty($invidata)) {
            return Message(3002,'代理信息不存在2');
        }

        $db = M('');
        $db->startTrans();

        //添加审核信息
        $codeadd['c_ucode'] = $acode;
        $codeadd['c_acode'] = $ucode;
        $codeadd['c_num'] = $num;
        $codeadd['c_state'] = 2;
        $codeadd['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Check_codeinfo')->add($codeadd);
        if (!$result) {
            $db->rollback();
            return Message(3000,'提交审核信息失败');
        }

        //修改市代拥有串码数量
        $insave['c_checknum'] = $invidata['c_checknum'] + $num;
        $result = M('Invite_code')->where($inwhere)->save($insave);
        if (!$result) {
            $db->rollback();
            return Message(3004,'修改串码数量失败');
        }

        //创建公告
//        $parr1['ucode'] = $userinfo['c_ucode'];
//        $parr1['ptitle'] = '您有代理提交商家激活串码申请，可以前往串码中心审核~';
//        $parr1['url'] = GetHost(3).'/agent.php/Home/Stringcheck/index';
//        $result = IGD('Infomation','Agent')->Create_information($parr1);
//        if ($result['code'] != 0) {
//            $db->rollback();
//
//            return Message(1000,'创建信息失败');
//        }

        // 发送短信通知
//        $separr['telephone'] = $userinfo['c_phone'];
//        $separr['content'] = "【微领地小蜜】尊敬的小蜜代理您好，您有代理提交商家激活串码申请，可以前往商家后台审核~";
//        $separr['userid'] = C('TEl_USER');
//        $separr['account'] = C('TEl_ACCESS');
//        $separr['password'] = C('TEl_PASSWORD');
//        $register = IGD('Sendmsg', 'Login');
//        $returndata = $register->SendVerify($separr);

        $db->commit();
        return Message(0,'申请成功');
    }

    /**
     * 市代发放激活串码
     * @param ucode
     */
    function GrantCode($parr)
    {
        $ucode = $parr['ucode'];

        //查询市代串码信息
        $inwhere['c_ucode'] = $ucode;
        $inwhere['c_rule'] = 2;
        $invidata = M('Invite_code')->where($inwhere)->find();
        if (!$invidata) {
            return Message(3002,'代理信息不存在3');
        }

        //$code = $this->getRandomString(12,'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $code =$this->getRandomCode(4,'ABCDEFGHIJKLMNOPQRSTUVWXYZ',8,'0123456789');

        $db = M('');
        $db->startTrans();

        $codeadd['c_code'] = $code;
        $codeadd['c_acode'] = $ucode;
        $codeadd['c_state'] = 2;
        $codeadd['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Check_codelist')->add($codeadd);
        if (!$result) {
            $db->rollback();
            return Message(3000,'发放失败');
        }

        //修改市代拥有串码数量
        $insave['c_usenum'] = $invidata['c_usenum'] + 1;
        $insave['c_codenum'] = $invidata['c_codenum'] - 1;
        $result = M('Invite_code')->where($inwhere)->save($insave);
        if (!$result) {
            $db->rollback();
            return Message(3004,'修改串码数量失败');
        }

        $db->commit();
        return MessageInfo(0,'发放成功',$codeadd);
    }

    function getRandomCode($len1,$chars1,$len2,$chars2){

        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str1 = '', $lc = strlen($chars1)-1; $i < $len1; $i++) {
            $str1 .= $chars1[mt_rand(0, $lc)];
        }

        for ($i = 0, $str2 = '', $lc = strlen($chars2)-1; $i < $len2; $i++) {
            $str2 .= $chars2[mt_rand(0, $lc)];
        }

        //查询是否唯一
        $result = M('Check_codelist')->where(array('c_code'=>$str1.$str2))->find();
        if ($result) {
            return $this->getRandomCode(4,'ABCDEFGHIJKLMNOPQRSTUVWXYZ',8,'0123456789');
        }

        return $str1.$str2;
    }


    function getRandomString($len, $chars=null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }

        //查询是否唯一
        $result = M('Check_codelist')->where(array('c_code'=>$str))->find();
        if ($result) {
            return $this->getRandomString(12,'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        }
        return $str;
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


}