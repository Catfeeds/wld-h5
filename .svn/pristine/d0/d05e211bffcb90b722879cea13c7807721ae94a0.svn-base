<?php
namespace Home\Controller;
use Think\Controller;

class MarketingController extends BaseController {


   //营销活动
   
    public function shopact (){
        // $hide = I('hide');
        // if (!empty($hide)) {
        //     $w['p.c_ishow'] = 1;
        //     $w['p.c_isdele'] = 1;
        //     $this->hide = 'ctrl_hidden';
        // }
        $db = M('Shopact_product as p');
        //条件
        //
        $c_mcode = trim(I('c_mcode'));
        if (!empty($c_ucode)) {
            $w['u.c_mcode'] = $c_mcode;
            $this->c_mcode = $c_mcode;
        }


        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['p.c_name'] = array('like', "%{$c_name}%");
        }

        $c_imgpath = trim(I('c_imgpath'));
        if (!empty($c_imgpath)) {
            $w['p.c_imgpath'] = $c_imgpath;
        }


        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['b.c_nickname'] = array('like', "%{$nickname}%");
        }
        //商家手机号
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['b.c_phone'] =$c_phone;
        }


         $c_activityname = trim(I('c_activityname'));
        if (!empty($c_activityname)) {
            $w['u.c_activityname'] = array('like', "%{$c_activityname}%");
        }



        $c_value = trim(I('c_value'));
        if (!empty($c_value)) {
            $w['p.c_value'] = $c_value;
        }


        $c_actprice = trim(I('c_actprice'));
        if (!empty($c_actprice)) {
            $w['p.c_actprice'] = $c_actprice;
        }


        $c_totalnum = trim(I('c_totalnum'));
        if (!empty($c_totalnum)) {
            $w['p.c_totalnum'] = $c_totalnum;
        }

        $c_num = trim(I('c_num'));
        if (!empty($c_num)) {
            $w['p.c_num'] = $c_num;
        }

        $c_usernum = trim(I('c_usernum'));
        if (!empty($c_usernum)) {
            $w['p.c_usernum'] = $c_usernum;
        }

        $c_bargainprice = trim(I('c_bargainprice'));
        if (!empty($c_bargainprice)) {
            $w['p.c_bargainprice'] = $c_bargainprice;
        }


        $c_targetnum = trim(I('c_targetnum'));
        if (!empty($c_targetnum)) {
            $w['p.c_targetnum'] = $c_targetnum;
        }



         $c_state = trim(I('c_state'));
        if (!empty($c_state)) {
            $w['p.c_state'] = $c_state;
        }


        // $w['c_isdel'] = 2;//显示不删除的产品
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'p.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'p.*,b.c_nickname,b.c_phone,u.c_activityname';
        $panrn['join'] = 'left join t_activity as u on u.c_id=p.c_aid';
        // $panrn['join1'] = 'left join t_activity as c on c.c_id=p.c_id';
        $panrn['join2'] = 'left join t_users as b on p.c_ucode=b.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->page = $date['Page'];//分页
        $this->count = $date['count'];//分页\
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->category = M('category')->select();
        $this->show();
    }
    
     //删除 修改状态 不删除记录
     public function product_del()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $data['c_isdel'] = 1;
        $result = M('Shopact_product')->where($where)->save($data);
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    
    // /**
     // *  生成产品编码
     // *  @param
     // */
    function CreateUcode($prefix) {
        //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 4);
        $uuid .= substr($str, 8, 2);
        $uuid .= substr($str, 12, 3);
        $uuid .= substr($str, 16, 2);
        $uuid .= substr($str, 20, 5);
        return $prefix . $uuid;
    }


 

   	//订单列表
	public function order()
	{    


		//订单号
        $c_orderid = trim(I('c_orderid'));
        if (!empty($c_orderid)) {
            $w['a.c_orderid'] = array('like', "%{$c_orderid}%");
        }


        //活动名称
        $c_activityname = trim(I('c_activityname'));
        if (!empty($c_activityname)) {
            $w['ui.c_activityname'] = array('like', "%{$c_activityname}%");
        }


        // //商家昵称
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['ud.c_nickname'] = $c_name;
        }

        //商家手机号
        $c_phones = trim(I('c_phones'));
        if (!empty($c_phones)) {
            $w['ud.c_phone'] = $c_phones;
        }


        // 用户昵称
        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = $c_nickname;
        }

        // 用户手机号
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }
        
        //  //产品名称
        $c_pname = trim(I('c_pname'));
        if (!empty($c_pname)) {
            $w['ui.c_pname'] = $c_pname;
        }
        // $c_pname = trim(I('c_pname'));
        // if (!empty($c_pname)) {
        //     $w['u.c_pname'] =$c_pname
         // }

         //产品价格
        $c_value = trim(I('c_value'));
        if (!empty($c_value)) {
            $w['u.c_value'] = array('like', "%{$c_value}%");
        }

         //产品图片
        $c_imgpath = trim(I('c_imgpath'));
        if (!empty($c_imgpath)) {
            $w['u.c_imgpath'] = array('like', "%{$c_imgpath}%");
        }

         //收银员昵称
        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = array('like', "%{$c_nickname}%");
        }


         //产品数量
        $c_pnum = trim(I('c_pnum'));
        if (!empty($c_pnum)) {
            $w['u.c_pnum'] = array('like', "%{$c_pnum}%");
        }

         //总金额
        $c_total_price = trim(I('c_total_price'));
        if (!empty($c_total_price)) {
            $w['u.c_total_price'] = array('like', "%{$c_total_price}%");
        }

		//邮费
        $c_free = trim(I('c_free'));
        if (!empty($c_free)) {
            $w['a.c_free'] = array('like', "%{$c_free}%");
        }
        
        //实际支付金额
        $c_actual_price = trim(I('c_actual_price'));
        if (!empty($c_actual_price)) {
            $w['u.c_actual_price'] = $c_actual_price;
        }

        //支付状态
        $c_pay_state = trim(I('c_pay_state'));
        if (!empty($c_pay_state)) {
            $w['a.c_pay_state'] = $c_pay_state;
        }

        

        //支付临时单号
        $c_payorderid = trim(I('c_payorderid'));
        if (!empty($c_payorderid)) {
            $w['ui.c_payorderid'] = $c_payorderid;
        }

        $db = M('Shopact_log as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,u.c_phone,ud.c_nickname as c_name,
                           ud.c_phone as c_phones,ui.c_activityname';
        $panrn['join'] = 'left join t_users as u on a.c_ucode=u.c_ucode';
        $panrn['join2'] = 'left join t_users as ud on a.c_acode =ud.c_ucode';
        $panrn['join1'] = 'left join t_activity as ui on a.c_aid=ui.c_id';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
    

    //产品评论删除
    public function score_del(){
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Shopact_product')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

}