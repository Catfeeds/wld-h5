<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  系统设置
 */
class SystemController extends BaseController {
	// 分红列表管理
    public function dividend_list()
    {
        $System_settings = M('System_settings');
        $count = $System_settings->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        $this->data = $System_settings->limit($limit)->order('c_addtime desc')->select();
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->display();
    }

    // 分红设置添加
    public function dividend_add()
    {
        $this->action = 'System/dividend_add';
        $System_settings = M('System_settings');
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['agent_scale'])) {
                $this->ajaxReturn(Message(1001,'抽代理商比列(小蜜商城)不能为空'));
            }
            if (empty($jsondata['agent_refreescale'])) {
                $this->ajaxReturn(Message(1001,'推荐人利润(小蜜商城)不能为空'));
            }

            if (empty($jsondata['shop_scale'])) {
                $this->ajaxReturn(Message(1001,'抽商家比例(商城)不能为空'));
            }
            if (empty($jsondata['shop_refreescale'])) {
                $this->ajaxReturn(Message(1001,'推荐人利润(商城)不能为空'));
            }

            if (empty($jsondata['area_scale'])) {
                $this->ajaxReturn(Message(1001,'区代分红比例不能为空'));
            }
            if (empty($jsondata['city_scale'])) {
                $this->ajaxReturn(Message(1001,'代理商分红比例不能为空'));
            }

            if (empty($jsondata['scanpay_shoprake'])) {
                $this->ajaxReturn(Message(1001,'抽商家(扫码支付)不能为空'));
            }
            if (empty($jsondata['scanpay_tjprofit'])) {
                $this->ajaxReturn(Message(1001,'推荐人利润(扫码支付)不能为空'));
            }
            if (empty($jsondata['scanpay_areaprofit'])) {
                $this->ajaxReturn(Message(1001,'区代分红(扫码支付)不能为空'));
            }
            if (empty($jsondata['scanpay_cityprofit'])) {
                $this->ajaxReturn(Message(1001,'代理商分红(扫码支付)不能为空'));
            }


            // if (empty($jsondata['sharespool'])) {
            //     $this->ajaxReturn(Message(1001,'股票池不能为空'));
            // }
            // if (empty($jsondata['shareprice'])) {
            //     $this->ajaxReturn(Message(1001,'股票当前价格不能为空'));
            // }
            // if (empty($jsondata['sharerose'])) {
            //     $this->ajaxReturn(Message(1001,'股票涨幅度不能为空'));
            // }

            if ($jsondata['state'] == '') {
                $this->ajaxReturn(Message(1001,'请选择是否启用'));
            }
            $data['c_agent_scale'] = $jsondata['agent_scale'];
            $data['c_shop_scale'] = $jsondata['shop_scale'];
            $data['c_agent_refreescale'] = $jsondata['agent_refreescale'];
            $data['c_shop_refreescale'] = $jsondata['shop_refreescale'];
            $data['c_area_scale'] = $jsondata['area_scale'];
            $data['c_city_scale'] = $jsondata['city_scale'];
            $data['c_red_scale'] = $jsondata['red_scale'];
            $data['c_state'] = $jsondata['state'];
            $data['c_scanpay_shoprake'] = $jsondata['scanpay_shoprake'];
            $data['c_scanpay_tjprofit'] = $jsondata['scanpay_tjprofit'];
            $data['c_scanpay_areaprofit'] = $jsondata['scanpay_areaprofit'];
            $data['c_scanpay_cityprofit'] = $jsondata['scanpay_cityprofit'];
            // $data['c_sharespool'] = $jsondata['sharespool'];
            // $data['c_shareprice'] = $jsondata['shareprice'];
            // $data['c_sharerose'] = $jsondata['sharerose'];
            $data['c_addtime'] = date('Y-m-d H:i:s',time());
            $result = $System_settings->add($data);
            if($result) {
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1000,'添加失败'));
            }
        }
        $this->display();
    }

    // 分红设置编辑
    public function dividend_edit()
    {
        $Id = I('Id');
        $this->action = 'System/dividend_edit';
        $System_settings = M('System_settings');
        $where['c_id'] = $Id;
        $this->vo = $System_settings->where($where)->find();
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['agent_scale'])) {
                $this->ajaxReturn(Message(1001,'抽代理商比列(小蜜商城)不能为空'));
            }
            if (empty($jsondata['agent_refreescale'])) {
                $this->ajaxReturn(Message(1001,'推荐人利润(小蜜商城)不能为空'));
            }

            if (empty($jsondata['shop_scale'])) {
                $this->ajaxReturn(Message(1001,'抽商家比例(商城)不能为空'));
            }
            if (empty($jsondata['shop_refreescale'])) {
                $this->ajaxReturn(Message(1001,'推荐人利润(商城)不能为空'));
            }

            if (empty($jsondata['area_scale'])) {
                $this->ajaxReturn(Message(1001,'区代分红比例不能为空'));
            }
            if (empty($jsondata['city_scale'])) {
                $this->ajaxReturn(Message(1001,'代理商分红比例不能为空'));
            }

            if (empty($jsondata['scanpay_shoprake'])) {
                $this->ajaxReturn(Message(1001,'抽商家(扫码支付)不能为空'));
            }
            if (empty($jsondata['scanpay_tjprofit'])) {
                $this->ajaxReturn(Message(1001,'推荐人利润(扫码支付)不能为空'));
            }
            if (empty($jsondata['scanpay_areaprofit'])) {
                $this->ajaxReturn(Message(1001,'区代分红(扫码支付)不能为空'));
            }
            if (empty($jsondata['scanpay_cityprofit'])) {
                $this->ajaxReturn(Message(1001,'代理商分红(扫码支付)不能为空'));
            }


            // if (empty($jsondata['sharespool'])) {
            //     $this->ajaxReturn(Message(1001,'股票池不能为空'));
            // }
            // if (empty($jsondata['shareprice'])) {
            //     $this->ajaxReturn(Message(1001,'股票当前价格不能为空'));
            // }
            // if (empty($jsondata['sharerose'])) {
            //     $this->ajaxReturn(Message(1001,'股票涨幅度不能为空'));
            // }

            if ($jsondata['state'] == '') {
                $this->ajaxReturn(Message(1001,'请选择是否启用'));
            }
            $data['c_agent_scale'] = $jsondata['agent_scale'];
            $data['c_shop_scale'] = $jsondata['shop_scale'];
            $data['c_agent_refreescale'] = $jsondata['agent_refreescale'];
            $data['c_shop_refreescale'] = $jsondata['shop_refreescale'];
            $data['c_area_scale'] = $jsondata['area_scale'];
            $data['c_city_scale'] = $jsondata['city_scale'];
            $data['c_red_scale'] = $jsondata['red_scale'];
            $data['c_state'] = $jsondata['state'];
            $data['c_scanpay_shoprake'] = $jsondata['scanpay_shoprake'];
            $data['c_scanpay_tjprofit'] = $jsondata['scanpay_tjprofit'];
            $data['c_scanpay_areaprofit'] = $jsondata['scanpay_areaprofit'];
            $data['c_scanpay_cityprofit'] = $jsondata['scanpay_cityprofit'];
            // $data['c_sharespool'] = $jsondata['sharespool'];
            // $data['c_shareprice'] = $jsondata['shareprice'];
            // $data['c_sharerose'] = $jsondata['sharerose'];
            $data['c_addtime'] = date('Y-m-d H:i:s',time());
            $where['c_id'] = $jsondata['Id'];
            $result = $System_settings->where($where)->save($data);
            if($result) {
                $this->ajaxReturn(Message(0,'编辑成功'));
            }else{
                $this->ajaxReturn(Message(1000,'编辑失败'));
            }
        }
        $this->display('dividend_add');
    }

    // 分红设置删除
    public function dividend_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('System_settings')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //支付方式列表
    public function payment(){
    	$db = M('pay_type');
    	$this->count = $db -> count();
    	$this->data = $db ->select();
    	$this->display();
    }

    //支付方式添加
    public function payment_add()
    {
        $this->action = 'System/payment_add';
        $db = M('pay_type');

        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['c_payname'])) {
                $this->ajaxReturn(Message(1001,'方式名称不能为空'));
            }
            if (empty($jsondata['c_payrule'])) {
                $this->ajaxReturn(Message(1001,'规则编号不能为空'));
            }else{
            	$where['c_payrule'] = $jsondata['c_payrule'];
            	$count = $db->where($where)->count();
            	if($count > 0){
            		$this->ajaxReturn(Message(1002,'规则编号已存在，请尽量按顺序填写规则编号'));
            	}
            }
            if (empty($jsondata['c_paydesc'])) {
                $this->ajaxReturn(Message(1001,'方式描述不能为空'));
            }

            $data['c_payname'] = $jsondata['c_payname'];
            $data['c_payrule'] = $jsondata['c_payrule'];
            $data['c_paydesc'] = $jsondata['c_paydesc'];
            $data['c_addtime'] = date('Y-m-d H:i:s',time());
            $result = $db->add($data);
            if($result) {
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1000,'添加失败'));
            }
        }
        $this->display();
    }

    //支付方式编辑
    public function payment_edit()
    {
        $this->action = 'System/payment_edit';
        $db = M('pay_type');
        $Id = I('Id');
        $where['c_id'] = $Id;
        $this->vo = $db->where($where)->find();
        if(IS_AJAX){
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['c_payname'])) {
                $this->ajaxReturn(Message(1001,'方式名称不能为空'));
            }
            if (empty($jsondata['c_payrule'])) {
                $this->ajaxReturn(Message(1001,'规则编号不能为空'));
            }else{
            	$where['c_payrule'] = $jsondata['c_payrule'];
            	$count = $db->where($where)->count();
            	if($count > 0){
            		$this->ajaxReturn(Message(1002,'规则编号已存在，请尽量按顺序填写规则编号'));
            	}
            }
            if (empty($jsondata['c_paydesc'])) {
                $this->ajaxReturn(Message(1001,'方式描述不能为空'));
            }

            $data['c_payname'] = $jsondata['c_payname'];
            $data['c_payrule'] = $jsondata['c_payrule'];
            $data['c_paydesc'] = $jsondata['c_paydesc'];
            $data['c_addtime'] = date('Y-m-d H:i:s',time());
            $w['c_id'] = $jsondata['Id'];
            $result = $db->where($w)->save($data);
            if($result) {
                $this->ajaxReturn(Message(0,'编辑成功'));
            }else{
                $this->ajaxReturn(Message(1000,'编辑失败'));
            }
        }
        $this->display('payment_add');
    }

    //支付方式删除
    public function payment_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('pay_type')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //首页配置信息
    public function homepage(){
        $Redis = IGD('Redis','Redis');
        $result = $Redis->Gethash('activity');
        // if($result['code'] != 0){
        //     $this->error($result['msg']);
        // }
        $this->data = $result['data'];

        $result = $Redis->Gethash('roulette');
        // if($result['code'] != 0){
        //     $this->error($result['msg']);
        // }
        $this->vo = $result['data'];

        $this->display();
    }

    //首页配置信息设置
    public function homepage_setting(){
        $a = array(
            'state' => $_POST['state'],
            'random' => $_POST['random'],
            'randnum' => $_POST['randnum'],
            'shownum' => $_POST['shownum'],
            'minclick' => $_POST['minclick'],
            'maxclick' => $_POST['maxclick'],
            'bargainnum' => $_POST['bargainnum'],
            'shopnum' => $_POST['shopnum'],
            'producenum' => $_POST['producenum'],
            'limittime' => $_POST['limittime'],
            'questionnum' => $_POST['questionnum'],
            'chipclick' => $_POST['chipclick'],
            'chipjuli' => $_POST['chipjuli'],
            'chiptime' => $_POST['chiptime'],
            'redclick' => $_POST['redclick'],
            'randchange' => $_POST['randchange'],

            'collecttime' => $_POST['collecttime'],//集福活动时间
            'collectnum' => $_POST['collectnum'],//红包雨红包数量
            'redrand' => $_POST['redrand'],//集福活动中红包范围
            'resrand' => $_POST['resrand'],//集福活动中福字范围
            'styleswitch' => $_POST['styleswitch'],//过年风格开关
            'portionlimit' => $_POST['portionlimit'],//聚宝限制份数
            'redstatus' => $_POST['redstatus'],//是否开启返利红包
            'redtimes' => $_POST['redtimes'],//返利红包时间
        );

        $result = IGD('Redis','Redis')->Sethash('activity',$a);

        if($result['code'] == 0){
            $this->success("保存成功");
        }else{
            $this->error($result['msg']);
        }
    }

    //大转盘活动配置信息设置
    public function roulette_setting(){
        $a = array(
            'statu' => $_POST['statu'],
            'num' => $_POST['num'],
            'prize1' => $_POST['prize1'],
            'prize2' => $_POST['prize2'],
            'prize3' => $_POST['prize3'],
            'prize4' => $_POST['prize4'],
            'prize5' => $_POST['prize5'],
            'prize6' => $_POST['prize6'],
            'repeat' => $_POST['repeat'],
            'luckuser' => $_POST['luckuser'],
            'minclick' => $_POST['minclick'],//二等奖
            'maxclick' => $_POST['maxclick'],//一等奖
            'redminclick' => $_POST['redminclick'],//四等奖
            'redmaxclick' => $_POST['redmaxclick'],//三等奖
            'midclick' => $_POST['midclick'],//五等奖
            'limittime' => $_POST['limittime'],
            );


        $result = IGD('Redis','Redis')->Sethash('roulette',$a);

        if($result['code'] == 0){
            $this->success("保存成功");
        }else{
            $this->error($result['msg']);
        }
    }

    //扫码支付配置
    public function scanpay(){
        $Industry = M('Shop_industry');

        //条件
        $where['c_pid'] = 0;
        $name = trim(I('industryname'));
        if (!empty($name)) {
           $where['c_name'] = array('like', "%{$name}%");
        }
        //分页
        $count = $Industry->where($where)->count();
        $page = getpage($count,25);
        $limit = $page->firstRow.','.$page->listRows;
        //数据
        $data = $Industry->where($where)->limit($limit)->order('c_id desc')->select();
        foreach ($data as $key => $value) {
            $data[$key]['child'] = $Industry->where('c_pid='.$value['c_id'])->select();
        }

        $this->data = $data;
        $this->post = I('param.');
        $this->page = $page->show();
        $this->assign('count',$count);
        $this->display();
    }

    //活动奖品验证数据
    function pcheckfrom($data){

        if (empty($data['c_name'])) {
            $this->error("名称不能为空");
        }

        $pid = $data['pid'];
        if(empty($pid)){
            if (empty($data['scanpay_shoprake'])) {
                $this->error("行业抽商家（扫码支付）必须填写");
            }
            if (empty($data['scanpay_tjprofit'])) {
                $this->error("行业推荐人利润（扫码支付）必须填写");
            }
            if (empty($data['scanpay_areaprofit'])){
                $this->error("行业代理商分红（扫码支付）必须填写");
            }
            if (empty($data['scanpay_cityprofit'])){
                $this->error("行业区代分红（扫码支付）必须填写");
            }
            if (empty($data['scanpay_redscale'])){
                $this->error("行业商家红包（扫码支付）必须填写");
            }

            if (empty($data['online_shoprake'])) {
                $this->error("行业线上抽商家（扫码支付）必须填写");
            }
            if (empty($data['online_tjprofit'])) {
                $this->error("行业线上推荐人利润（扫码支付）必须填写");
            }
            if (empty($data['online_cityprofit'])){
                $this->error("行业线上代理商分红（扫码支付）必须填写");
            }
            if (empty($data['online_areaprofit'])){
                $this->error("行业线上区代分红（扫码支付）必须填写");
            }
            if (empty($data['online_redscale'])){
                $this->error("行业线上商家红包（扫码支付）必须填写");
            }
        }
        if (empty($data['industrid'])){
            $this->error("银行行业类别编码必须填写");
        }
    }

    //添加配置
    public function scanpay_setting(){
        $this->action = 'scanpay_setting';
        $w['c_pid'] = 0;
        $this->pro_list = M('Shop_industry')->field('c_id,c_pid,c_name')->where($w)->select();

        if(IS_POST){
            $db = M('Shop_industry');

            $this->pcheckfrom($_POST);

            $pid = $_POST['pid'];
            if(!empty($pid)){
                $w['c_id'] = $pid;
                $pinfo = $db->where($w)->find();

                if(empty($_POST['scanpay_shoprake'])){
                    $_POST['scanpay_shoprake'] = $pinfo['c_scanpay_shoprake'];
                }
                if(empty($_POST['scanpay_tjprofit'])){
                    $_POST['scanpay_tjprofit'] = $pinfo['c_scanpay_tjprofit'];
                }
                if(empty($_POST['scanpay_cityprofit'])){
                    $_POST['scanpay_cityprofit'] = $pinfo['c_scanpay_cityprofit'];
                }
                if(empty($_POST['scanpay_areaprofit'])){
                    $_POST['scanpay_areaprofit'] = $pinfo['c_scanpay_areaprofit'];
                }
                if(empty($_POST['scanpay_redscale'])){
                    $_POST['scanpay_redscale'] = $pinfo['c_scanpay_redscale'];
                }

                if(empty($_POST['online_shoprake'])){
                    $_POST['online_shoprake'] = $pinfo['c_online_shoprake'];
                }
                if(empty($_POST['online_tjprofit'])){
                    $_POST['online_tjprofit'] = $pinfo['c_online_tjprofit'];
                }
                if(empty($_POST['online_cityprofit'])){
                    $_POST['online_cityprofit'] = $pinfo['c_online_cityprofit'];
                }
                if(empty($_POST['online_areaprofit'])){
                    $_POST['online_areaprofit'] = $pinfo['c_online_areaprofit'];
                }
                if(empty($_POST['online_redscale'])){
                    $_POST['online_redscale'] = $pinfo['c_online_redscale'];
                }
            }

            $data['c_name'] = $_POST['c_name'];
            $data['c_pid'] = $pid;

            $data['c_scanpay_shoprake'] = $_POST['scanpay_shoprake'];
            $data['c_scanpay_tjprofit'] = $_POST['scanpay_tjprofit'];
            $data['c_scanpay_areaprofit'] = $_POST['scanpay_areaprofit'];
            $data['c_scanpay_cityprofit'] = $_POST['scanpay_cityprofit'];
            $data['c_scanpay_redscale'] = $_POST['scanpay_redscale'];

            $data['c_online_shoprake'] = $_POST['online_shoprake'];
            $data['c_online_tjprofit'] = $_POST['online_tjprofit'];
            $data['c_online_cityprofit'] = $_POST['online_cityprofit'];
            $data['c_online_areaprofit'] = $_POST['online_areaprofit'];
            $data['c_online_redscale'] = $_POST['online_redscale'];
            $data['c_industrid'] = $_POST['industrid'];
            $data['c_addtime'] = Date('Y-m-d H:i:s');

            $result = $db->add($data);
            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/System/scanpay';
              echo '<script language="javascript">alert("添加成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('添加失败！');
            }
        }
        $this->display();
    }

    //编辑配置
    public function scanpay_edit(){
        $this->action = 'scanpay_edit';

        $Id = I('Id');
        $w['c_id'] = $Id;
        $arr = M('Shop_industry')->where($w)->find();

        $w1['c_pid'] = 0;
        $this->pro_list = M('Shop_industry')->field('c_id,c_pid,c_name')->where($w1)->select();

        $this->vo = $arr;
        if(IS_POST){
            $db = M('Shop_industry');

            $this->pcheckfrom($_POST);

            $pid = $_POST['pid'];
            if(!empty($pid)){
                $w['c_id'] = $pid;
                $pinfo = $db->where($w)->find();

                if(empty($_POST['scanpay_shoprake'])){
                    $_POST['scanpay_shoprake'] = $pinfo['c_scanpay_shoprake'];
                }
                if(empty($_POST['scanpay_tjprofit'])){
                    $_POST['scanpay_tjprofit'] = $pinfo['c_scanpay_tjprofit'];
                }
                if(empty($_POST['scanpay_areaprofit'])){
                    $_POST['scanpay_areaprofit'] = $pinfo['c_scanpay_areaprofit'];
                }
                if(empty($_POST['scanpay_cityprofit'])){
                    $_POST['scanpay_cityprofit'] = $pinfo['c_scanpay_cityprofit'];
                }
                if(empty($_POST['scanpay_redscale'])){
                    $_POST['scanpay_redscale'] = $pinfo['c_scanpay_redscale'];
                }

                if(empty($_POST['online_shoprake'])){
                    $_POST['online_shoprake'] = $pinfo['c_online_shoprake'];
                }
                if(empty($_POST['online_tjprofit'])){
                    $_POST['online_tjprofit'] = $pinfo['c_online_tjprofit'];
                }
                if(empty($_POST['online_cityprofit'])){
                    $_POST['online_cityprofit'] = $pinfo['c_online_cityprofit'];
                }
                if(empty($_POST['online_areaprofit'])){
                    $_POST['online_areaprofit'] = $pinfo['c_online_areaprofit'];
                }
                if(empty($_POST['online_redscale'])){
                    $_POST['online_redscale'] = $pinfo['c_online_redscale'];
                }
            }

            $data['c_name'] = $_POST['c_name'];
            $data['c_pid'] = $pid;

            $data['c_scanpay_shoprake'] = $_POST['scanpay_shoprake'];
            $data['c_scanpay_tjprofit'] = $_POST['scanpay_tjprofit'];
            $data['c_scanpay_areaprofit'] = $_POST['scanpay_areaprofit'];
            $data['c_scanpay_cityprofit'] = $_POST['scanpay_cityprofit'];
            $data['c_scanpay_redscale'] = $_POST['scanpay_redscale'];

            $data['c_online_shoprake'] = $_POST['online_shoprake'];
            $data['c_online_tjprofit'] = $_POST['online_tjprofit'];
            $data['c_online_cityprofit'] = $_POST['online_cityprofit'];
            $data['c_online_areaprofit'] = $_POST['online_areaprofit'];
            $data['c_online_redscale'] = $_POST['online_redscale'];
            $data['c_industrid'] = $_POST['industrid'];
            $data['c_addtime'] = Date('Y-m-d H:i:s');
            var_dump($data);die;
            $w2['c_id'] = $_POST['Id'];
            $result = $db->where($w2)->save($data);
            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/System/scanpay';
              echo '<script language="javascript">alert("编辑成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('编辑失败！');
            }
        }
        $this->display('scanpay_setting');
    }

    //删除配置
    public function scanpay_del(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $pinfo = M('Shop_industry')->where($where)->find();
        if($pinfo['c_pid'] == 0){
            $w['c_pid'] = $pinfo['c_id'];
            $result = M('Shop_industry')->where($w)->delete();
            if(!$result){
                $this->ajaxReturn(Message(1000,'删除失败'));
            }
        }

        $result = M('Shop_industry')->where($where)->delete();
        if(!$result){
            $this->ajaxReturn(Message(1000,'删除失败'));
        }

        $this->ajaxReturn(Message(0,'删除成功'));
    }

}