<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 
 */
class AgencyController extends BaseController {
   
        public  function bag ()
        {
            
            // //商家昵称
            // $c_nickname = trim(I('c_nickname'));
            // if (!empty($c_nickname)) {
            //     $w['u.c_nickname'] = $c_nickname;
            // }
            //商家手机号
            
            $c_phone = trim(I('c_phone'));
            if (!empty($c_phone)) {
                $wus['c_phone'] = $c_phone;
            }

            $c_username = trim(I('c_nickname'));
            if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
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

            //产品包名称
            $c_bag_name = trim(I('c_bag_name'));
            if (!empty($c_bag_name)) {
                $w['a.c_bag_name'] = $c_bag_name;
            }


            //是否上架
            $c_bag_status = trim(I('c_bag_status'));
            if (!empty($c_bag_status)) {
                $w['a.c_bag_status'] = $c_bag_status;
            }

            $db = M('Agency_bag as a');

            $panrn['where'] = $w;
            $parent = I('param.');
            $panrn['order'] = 'a.c_id desc';//排序
            $panrn['limit'] = 25;//分页数

            //分页显示数据
            $panrn['field'] = 'a.*';
            // $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
           
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
            // $this->list = $date['list'];
            $this->count = $date['count'];//分页\
            $this->page = $date['Page'];//分页
            $this->root_url = GetHost()."/";
            $this->post = $parent;
            // dump($date);die;
            $this->display();
        }   

        // //删除
        // public function delete()
        // {
        //     $Id = I('Id');
        //     $idstr = str_replace('|', ',', $Id);
        //     $where['c_id'] = array('in',$idstr);
        //     $result = M('Agency_bag')->where($where)->delete();
        //     if($result){
        //         $this->ajaxReturn(Message(0,'删除成功'));
        //     }else{
        //         $this->ajaxReturn(Message(1000,'删除失败'));
        //     }
        // }

        //产品包内产品列表
        public function pro_list()
        {
            $db = M('Agency_bag_product as a');

            $w['a.c_bag_code'] = I('bag_code');

            $panrn['where'] = $w;
            $parent = I('param.');
            $panrn['order'] = 'a.c_id desc';//排序
            $panrn['limit'] = 25;//分页数

            //分页显示数据
            $panrn['field'] = 'a.*,u.c_nickname,b.c_name';
            $panrn['join'] = 'left join t_product as b on b.c_pcode=a.c_pcode';
            $panrn['join1'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
            $list=D('Db','Behind');
            $date=$list->mate_select_pages($db,$panrn);
            $this->list = $date['list'];
            $this->count = $date['count'];//分页\
            $this->page = $date['Page'];//分页
            $this->root_url = GetHost()."/";
            $this->post = $parent;
            // dump($date);die;
            $this->display();
        }   
            

            //删除
        public function pro_delete()
        {
            $Id = I('Id');
            $idstr = str_replace('|', ',', $Id);
            $where['c_id'] = array('in',$idstr);
            $psave['c_isdele'] = 2;
            $result = M('Agency_bag_product')->where($where)->save($psave);
            if($result){
                $this->ajaxReturn(Message(0,'删除成功'));
            }else{
                $this->ajaxReturn(Message(1000,'删除失败'));
            }
        }
        /**
         * 代理商城 商家所有品牌包
         * @param acode,bag_code
         */
         public function AgencyBag($parr)
         {
            $w['c_ucode'] = $parr['acode'];
            $w['c_bag_status'] = 1;

            if ($parr['bag_code']) {
                $bag_code = $parr['bag_code'];
                $order = "case when bag_code='$bag_code' then 1 else 0 end desc,c_id desc";
            } else {
                $order = "c_id desc";
            }
            $list = M('Agency_bag')->where($w)->order('c_id desc')->select();

            if (!$list) {
                $list = array();
                return MessageInfo(0, "查询成功", $list);
            }

            foreach ($list as $key => $value) {
                $where['c_regionid'] = $value['c_id'];
                $where['c_sourceid'] = 5;

                $img_list = M('Resource_img')->where($where)->order('c_id asc')->select();
                foreach ($img_list as $k => $v) {
                    $img_list[$k]['c_thumbnail_img'] = GetHost().'/'.$v['c_thumbnail_img'];
                    $img_list[$k]['c_img'] = GetHost().'/'.$v['c_img'];
                }

                $list[$key]['img'] = $img_list[0]['c_thumbnail_img'];
                $list[$key]['imglist'] = $img_list;
            }

            return MessageInfo(0,"查询成功",$list);
        }
           
        /**
         *  品牌包内产品上下架
         *  @param pcode,status
         */
        public function BagProductStatus()
        {
            $pcode = I('pcode');
            $status = I('status');

            $w['c_pcode'] = $pcode;

            $db = M('');
            $db->startTrans();

            $savedata['c_status'] = $status;
            $result = M('Agency_bag_product')->where($w)->save($savedata);
            if ($result <= 0) {
                $db->rollback();
                return Message(1001,"操作失败！");
            }

            //同步所有代理产品下架
            $pw['c_isagent'] = 2;
            $pw['c_agent_pcode'] = $status;
            $info = M('Product')->where($pw)->select();
            if ($info) {
                $save['c_updatetime'] = gdtime();
                $save['c_ishow'] = $status;
                $result = M('Product')->where($pw)->save($save);
                if ($result <= 0) {
                    $db->rollback();
                    return Message(1002,"操作失败！");
                }
            }

            //改变产品可代理状态
            $agentwhere['c_pcode'] = $pcode;
            $prosave['c_updatetime'] = gdtime();
            $agentsign = 0;
            if ($status == 1) {
                $agentsign = 1;
            }
            $prosave['c_agentsign'] = $agentsign;
            $result = M('Product')->where($agentwhere)->save($prosave);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '修改状态失败');
            }

            $db->commit();
            return Message(0,"操作成功！");
        }


        /**
         *  产品包上下架
         *  @param bag_code,operate(1-上架，2-下架)
         */
            public function BagStatus()
            {
                $operate = I('operate');
                $bag_code = I('bag_code');

                $w['c_bag_code'] = $bag_code;
                if($operate == 1){
                    $w['c_status'] = 1;
                    $w['c_isdele'] = 1;
                    $produceinfo = M('Agency_bag_product')->where($w)->select();
                    if(!$produceinfo){
                        return Message(1001,'请先添加商品！');
                    }
                }

                $where['c_bag_code'] = $parr['bag_code'];
                $save['c_bag_status'] = $operate;
                $save['c_updatetime'] = gdtime();
                $result = M('Agency_bag')->where($where)->save($save);
                if(!$result){
                    return Message(1002,"操作失败！");
                }

                return Message(0,"操作成功！");      
            }

        //消费记录
        public  function xflog()
        {
        
            //用户昵称
            $c_nickname = trim(I('c_nickname'));
            if (!empty($c_nickname)) {
                $w['u.c_nickname'] = $c_nickname;
            }

            //用户手机号
            $c_phone = trim(I('c_phone'));
            if (!empty($c_phone)) {
                $w['u.c_phone'] = $c_phone;
            }
            
            //商家昵称
            $c_name = trim(I('c_name'));
            if (!empty($c_name)) {
                $w['ui.c_nickname'] = $c_name;
            }
            
            //商家手机号
            $c_phones = trim(I('c_phones'));
            if (!empty($c_phones)) {
                $w['ui.c_phone'] = $c_phones;
            }

            //产品名称
            $c_pcode = trim(I('c_pcode'));
            if (!empty($c_pcode)) {
                $w['a.c_pcode'] = $c_pcode;
            }

            //商品模型
            $c_mcode = trim(I('c_mcode'));
            if (!empty($c_mcode)) {
                $w['a.c_mcode'] = array('like', "%{$c_mcode}%");
            }

            //购买价格
            $c_price = trim(I('c_price'));
            if (!empty($c_price)) {
                $w['a.c_price'] = $c_price;
            }

            //购买数量
            $c_num = trim(I('c_num'));
            if (!empty($c_num)) {
                $w['a.c_num'] = $c_num;
            }

            //交易金额
            $c_money = trim(I('c_money'));
            if (!empty($c_money)) {
                $w['a.c_money'] = $c_money;
            }

           $db = M('Agency_jylog as a');

            $panrn['where'] = $w;
            $parent = I('param.');
            $panrn['order'] = 'a.c_id desc';//排序
            $panrn['limit'] = 25;//分页数

            //分页显示数据
            $panrn['field'] = 'a.*,u.c_nickname,u.c_phone,ui.c_nickname as c_name,ui.c_phone as c_phones';
            $panrn['join'] = 'left join t_product as p on p.c_ucode=a.c_ucode';
            $panrn['join1'] = 'left join t_users as u on a.c_ucode=u.c_ucode';
            $panrn['join2'] = 'left join t_users as ui on a.c_acode=ui.c_ucode';
            $list=D('Db','Behind');
            $date=$list->mate_select_pages($db,$panrn);
            $this->list = $date['list'];
            $this->count = $date['count'];//分页\
            $this->page = $date['Page'];//分页
            $this->root_url = GetHost()."/";
            $this->post = $parent;
            // dump($date);die;
            $this->display();
        }     
        
}