<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 资金风控管理
 */
class FundsController extends BaseController{

    //每日账目
    public function daily_accounts () {
		$db = M('Users_moneydate as a ');
        
        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = $c_phone;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['a.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['a.c_ucode'] = $usinfo['c_ucode'];
            }
        }
        
        //用户编码
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
            $w['a.c_ucode'] = $ucode;
        }

        //金额类型
		$c_type = trim(I('c_type'));
		if (!empty($c_type)) {
			$w['a.c_type'] = $c_type;
		}

        //收支明细
		$c_sign = trim(I('c_sign'));
		if (!empty($c_sign)) {
			$w['a.c_sign'] = $c_sign;
		} else {
            $w['a.c_sign'] = 1;
        }

        //日期
        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "a.c_datetime between '".$begintime."' and '".$endtime."'";
        }


        //排序方式
        $order = trim(I('order'));
        if ($order == 2) {  //时间排序
            $order1 = 'a.c_id desc';
        } else {  //默认金额排序
            if ($c_sign == 2) {
                $order1 = 'a.c_datetime desc,a.c_money asc';
            } else {
                $order1 = 'a.c_datetime desc,a.c_money desc';
            }
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = $order1;//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            
           
        }

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //每日利润
    public function profit()
    {
        $db = M('System_profit');
        //条件
        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "c_datetime between '".$begintime."' and '".$endtime."'";
        }
        
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        // dump($date);die();
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->display();
    }

    //Excel导出每日利润数据
    public function profitIndex(){
        $Order = D('Funds','Behind');
        $Order -> profitIndexnt();
    }

    //Excel导出每日账目数据
    public function accountsIndex(){
        $Order = D('Funds','Behind');
        $Order -> accountsIndex();
    }
    

    //交易风控规则列表
    public function transaction() {
        $db = M('Trade_setting as a ');
        
        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }
        
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = $c_phone;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['a.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['a.c_ucode'] = $usinfo['c_ucode'];
            }
        }
        
        //用户编码
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
            $w['a.c_ucode'] = $ucode;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = $order1;//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            
           
        }

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
    

    //启用与禁用规则
    public function transaction_state() {
        $Id = I('gid');
        $state = I('active');
        if(empty($Id)) die("非法操作，ID为空！");

        $where['c_id'] = $Id;

        $data['c_state'] = $state;
        $data['c_updatetime'] = Date('Y-m-d H:i:s');
        $result = M('Trade_setting')->where($where)->save($data);
        if(!$result){
            die("操作失败！");
        }
    }
    
    //删除规则
    public function transaction_delete() {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Trade_setting')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }
    
    //验证表单数据
    function pcheckfrom($data){
        // if (empty($data['ucode'])) {
        //     $this->error("用户未选择");
        // }

        if (empty($data['c_spenoney'])) {
            $this->error("单笔交易金额阀值不能为空");
        }

        if (empty($data['c_sdaymoney'])) {
            $this->error("单日交易金额阀值不能为空");
        }

        if (empty($data['c_spenextract'])) {
            $this->error("单笔提款金额阀值不能为空");
        }

        if (empty($data['c_sdayextract'])) {
            $this->error("单日提款金额阀值不能为空");
        }

        if (empty($data['c_state'])) {
            $this->error("是否启用不能为空");
        }
        
    }

    //交易风控规则添加   
    public function transaction_add(){
        $this->action = 'transaction_add';
        if(IS_POST){
            $db = M('Trade_setting');
            $this->pcheckfrom($_POST);

            $data['c_ucode'] = $_POST['ucode'];
            $data['c_spenoney'] = $_POST['c_spenoney'];
            $data['c_sdaymoney'] = $_POST['c_sdaymoney'];
            $data['c_spenextract'] = $_POST['c_spenextract'];
            $data['c_sdayextract'] = $_POST['c_sdayextract'];
            $data['c_state'] = $_POST['c_state'];
            $data['c_addtime'] = gdtime();
            $data['c_updatetime'] = gdtime();
            $result = $db->add($data);
            if($result){
                $openid = md5($_POST['ucode']);
                IGD('Common', 'Redis')->RediesStoreSram($openid, null);
                $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Funds/transaction';
                echo '<script language="javascript">alert("添加成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('添加失败！');
            }
        }

        $this->display();
    }

    //编辑交易风控规则
    public function transaction_info(){
        $this->action = 'transaction_info';
        $Id = I('Id');
        $w['l.c_id'] = $Id;
        $field = "l.*,u.c_nickname";
        $join = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $arr = M('Trade_setting as l')->field($field)->join($join)->where($w)->find();
        $this->vo = $arr;
        if(IS_POST){
            $db = M('Trade_setting');
            $this->pcheckfrom($_POST);

            $data['c_ucode'] = $_POST['ucode'];
            $data['c_spenoney'] = $_POST['c_spenoney'];
            $data['c_sdaymoney'] = $_POST['c_sdaymoney'];
            $data['c_spenextract'] = $_POST['c_spenextract'];
            $data['c_sdayextract'] = $_POST['c_sdayextract'];
            $data['c_state'] = $_POST['c_state'];
            $data['c_updatetime'] = gdtime();

            $w1['c_id'] = $_POST['Id'];
            $result = $db->where($w1)->save($data);
            if($result){
                $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Funds/transaction';
                echo '<script language="javascript">alert("编辑成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
                $this->error('编辑失败！');
            }
        }

        $this->display('transaction_add');
    }

    //风控禁用列表
    public function disable(){
        $db = M('Trade_limit as l');
        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }
        
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = $c_phone;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['a.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['l.c_ucode'] = $usinfo['c_ucode'];
            }
        }
        $sign = I('sign');
        if(!empty($sign)){
            $w['l.c_sign'] = $sign;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'l.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            $data_list[$k]['c_phone'] = $userinfo['c_phone'];
            $data_list[$k]['c_headimg'] = $userinfo['c_headimg'];
            
           
        }

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //验证表单数据
    function disablefromn($data){
        if (empty($data['ucode'])) {
            $this->error("风控用户未选择");
        }

        if (empty($data['remarks'])) {
            $this->error("风控原因备注不能为空");
        }

        if (empty($data['starttime'])) {
            $this->error("风控开始时间不能为空");
        }

        if (empty($data['endtime'])) {
            $this->error("风控结束时间不能为空");
        }

        if(strtotime($starttime) > strtotime($endtime)){
            $this->error("风控开始时间不能晚于禁用结束时间");
        }
    }

    //添加风控用户
    public function disable_add(){
        $this->action = 'disable_add';

        if(IS_POST){
            $db = M('Trade_limit');
            $this->disablefromn($_POST);

            $data['c_ucode'] = $_POST['ucode'];
            $data['c_sign'] = $_POST['sign'];
            $data['c_remarks'] = $_POST['remarks'];
            $data['c_starttime'] = $_POST['starttime'];
            $data['c_endtime'] = $_POST['endtime'];
            $data['c_addtime'] = Date('Y-m-d H:i:s');

            $result = $db->add($data);
            if($result){
                $openid = md5($_POST['ucode']);
                IGD('Common', 'Redis')->RediesStoreSram($openid, null);
                $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Funds/disable';
                echo '<script language="javascript">alert("添加成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
                $this->error('添加失败！');
            }
        }
        $this->display();
    }

    //编辑风控用户
    public function disable_edit(){
        $this->action = 'disable_edit';
        $Id = I('Id');
        $w['l.c_id'] = $Id;
        $field = "l.*,u.c_nickname";
        $join = 'left join t_users as u on u.c_ucode=l.c_ucode';
        $arr = M('Trade_limit as l')->field($field)->join($join)->where($w)->find();
        $this->vo = $arr;
        if(IS_POST){
            $db = M('Trade_limit');
            $this->disablefromn($_POST);

            $data['c_ucode'] = $_POST['ucode'];
            $data['c_sign'] = $_POST['sign'];
            $data['c_remarks'] = $_POST['remarks'];
            $data['c_starttime'] = $_POST['starttime'];
            $data['c_endtime'] = $_POST['endtime'];

            $w1['c_id'] = $_POST['Id'];
            $result = $db->where($w1)->save($data);
            if($result){
                $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Funds/disable';
                echo '<script language="javascript">alert("编辑成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
                $this->error('编辑失败！');
            }
        }
        $this->display('disable_add');
    }

    //删除风控用户
    public function disable_del()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Trade_limit')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }
}