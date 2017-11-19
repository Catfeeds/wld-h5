<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 扫码支付
 */
class ScanpayController extends BaseController {
    //扫码后首页
    public function order_list() {
        $db = M('Scanpay as s');
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['s.c_ucode'] = $c_ucode;
        }
        $c_acode = trim(I('acode'));
        if (!empty($c_acode)) {
            $w['s.c_acode'] = $c_acode;
        }

        $c_openid = trim(I('c_openid'));
        if (!empty($c_openid)) {
            $w['s.c_openid'] = $c_openid;
        }

        $c_unionid = trim(I('c_unionid'));
        if (!empty($c_unionid)) {
            $w['s.c_unionid'] = $c_unionid;
        }

        $min_money = I('min_money');
        $max_money = I('max_money');
        if(($min_money !='')&&($max_money !='')){
            if($min_money <= $max_money){
                $w[] = "s.c_money between '".$min_money."' and '".$max_money."'";
            }
        }

        $ncode = trim(I('ncode'));
        if (!empty($ncode)) {
            $w['s.c_ncode'] = $ncode;
        }
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['s.c_nickname'] = array('like', "%{$nickname}%");
        }

        //商家昵称
        $pnickname = trim(I('pnickname'));
        if (!empty($pnickname)) {
            $wus['c_nickname'] = array('like', "%{$pnickname}%");
            $usinfo = M('Users')->where($wus)->field('c_ucode')->select();
            $ustr = arr_to_str($usinfo);
            if ($ustr) {
                $w['s.c_acode'] = array('in',$ustr);
            }
        }
        
        //商家手机号
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = array('like', "%{$c_phone}%");
            $usinfo = M('Users')->where($wus)->field('c_ucode')->select();
            $ustr = arr_to_str($usinfo);
            if ($ustr) {
                $w['s.c_acode'] = array('in',$ustr);
            }
        }

        $state = trim(I('pay_state'));
        if (!empty($state)) {
            if($state == 10){
                $w['s.c_pay_state'] = 0;
            }else{
                $w['s.c_pay_state'] = 1;
            }
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 's.c_addtime desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 's.*';
        // $panrn['join'] = 'left join t_users as ui on s.c_acode=ui.c_ucode';
        $list=D('Db','Behind');
        $date = $list->mate_select_pages($db,$panrn);
        $dlist = $date['list'];
        foreach ($dlist as $key => $value) {
            $payrule = '';
            switch ($value['c_pay_rule']) {
                case 1:
                    $payrule = '支付宝支付';
                    break;
                case 2:
                    $payrule = '手机微信支付';
                    break;
                case 3:
                    $payrule = 'H5支付方式';
                    break;
                case 4:
                    $payrule = '余额支付';
                    break;
                case 5:
                    $payrule = '活动抵扣';
                    break;
                default:
                    $payrule = '未支付';
                    break;
            }
            $dlist[$key]['c_pay_rule'] = $payrule;

            $uw['c_ucode'] = $value['c_acode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $dlist[$key]['c_pname'] = $userinfo['c_nickname'];
             $dlist[$key]['c_phone'] = $userinfo['c_phone'];
        }
        $this->list = $dlist;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //扫码 锁定微信openid
    public function scanpay_tuijian(){
        $db = M('Scanpay_tuijian as t');
        //条件
        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['t.c_pcode'] = $c_ucode;
        }

        $pnickname = trim(I('pnickname'));
        if (!empty($pnickname)) {
            $wus['c_nickname'] = array('like', "%{$pnickname}%");
            $usinfo = M('Users')->where($wus)->field('c_ucode')->select();
            $ustr = arr_to_str($usinfo);
            if ($ustr) {
                $w['t.c_pcode'] = array('in',$ustr);
            }
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 't.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 't.*';
        // $panrn['join'] = 'left join t_users as u on t.c_pcode=u.c_ucode';
        $list=D('Db','Behind');
        $date = $list->mate_select_pages($db,$panrn);
        $dlist = $date['list'];
        foreach ($dlist as $key => $value) {
            $uw['c_ucode'] = $value['c_pcode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone,c_headimg')->find();
            $dlist[$key]['c_nickname'] = $userinfo['c_nickname'];
            $dlist[$key]['c_headimg'] = $userinfo['c_headimg'];
        }
        $this->list = $dlist;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //付款二维码
    public function qrcode(){
        $ucode = trim(I('ucode'));

        $parr['ucode'] = $ucode;
        $parr['qrcode_type'] = 1;

        $Qrcode = IGD('Qrcode', 'Store');
        $result = $Qrcode->PuzzleQrcode($parr);

        if($result['code'] != 0){
            $this->ajaxReturn(Message(1001,'生成二维码失败'));
        }

        $this->img = $result['data']['img'];
        $this->display();
    }

    //置顶商家行业
    public function shoptrade(){
        $ucode = trim(I('ucode'));

        $where['c_ucode'] = $ucode;
        $tradeid = M('Users')->where($where)->getfield('c_shoptrade');

        $w['c_id'] = $tradeid;
        $industry_info = M('Shop_industry')->where($w)->find();
        if($industry_info['c_pid'] == 0){
            $field = 'c_id as maintrade_id';
            $this->data = M('Shop_industry')->field($field)->where($w)->find();
        }else{
            $w1['a.c_id'] = $tradeid;
            $field = 'a.c_id as trade_id,a.c_name as tradename,m.c_id as maintrade_id,m.c_name as maintrade';
            $join = 'left join t_shop_industry as m on a.c_pid=m.c_id';
            $this->data = M('Shop_industry as a')->field($field)->join($join)->where($w1)->find();
        }

        $w2['c_pid'] = 0;
        $this->maintrades = M('Shop_industry')->field('c_id,c_name')->where($w2)->select();

        $this->ucode = $ucode;
        $this->display();
    }

    //联动选择行业
    public function Gettrade(){
        $tradeid = I('id');

        $where['c_pid'] = $tradeid;
        $trade = M('Shop_industry')->field('c_id as trade_id,c_name as tradename')->where($where)->select();
        $this->ajaxReturn($trade);
    }

    //保存商家行业
    public function savetrade(){
        $ucode = $_POST['ucode'];
        $tradeid = $_POST['trade'];
        $maintradeid = $_POST['maintrade'];

        if(empty($tradeid)){
            $tradeid = $maintradeid;
        }

        $where['c_ucode'] = $ucode;
        $save_data['c_shoptrade'] = $tradeid;
        $result = M('Users')->where($where)->save($save_data);

        if($result < 0){
            $this->error('编辑失败！');
        }else{
            $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Member/member_list';
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }
    }

    //指定用户地理位置
    public function userlocation(){
        $ucode = I('ucode');

        $where['c_ucode'] = $ucode;
        $this->vo = M('User_local')->where($where)->find();
        $this->ucode = $ucode;
        $this->display();
    }

    //保存地理位置
    public function savelocation(){
        $ucode = $_POST['ucode'];
        $isfixed = $_POST['isfixed'];

        $where['c_ucode'] = $ucode;

        if($isfixed == 1){
            $isshop = M('Users')->where($where)->getfield('c_shop');

            if($isshop == 0){
                $this->error('非商家不能固定地理位置！');
            }
        }

        $date['c_address'] = $_POST['address'];
        $date['c_longitude'] = $_POST['longitude'];
        $date['c_latitude'] = $_POST['latitude'];
        $date['c_isfixed'] = $isfixed;
        $date['c_updatetime'] = Date('Y-m-d H:i:s');

        $location_info = M('User_local')->where($where)->find();
        if($location_info){
            $result = M('User_local')->where($where)->save($date);
        }else{
            $date['c_ucode'] = $ucode;
            $date['c_addtime'] = Date('Y-m-d H:i:s');
            $result = M('User_local')->add($date);
        }

        //更新用户表的地址
        $users_data['c_address1'] = $_POST['address'];
        $users_data['c_longitude1'] = $_POST['longitude'];
        $users_data['c_latitude1'] = $_POST['latitude'];
        $users_data['c_isfixed1'] = $isfixed;

        $result1 = M('Users')->where($where)->save($users_data);

        //更新用户表的地址
        $store_data['c_address'] = $_POST['address'];
        $store_data['c_longitude'] = $_POST['longitude'];
        $store_data['c_latitude'] = $_POST['latitude'];

        $result2 = M('Store')->where($where)->save($store_data);

        if($result2 < 0){
            $this->error('编辑失败！');
        }else{
            $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Member/member_list';
            echo '<script language="javascript">alert("编辑成功");</script>';
            echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
        }
    }
}
