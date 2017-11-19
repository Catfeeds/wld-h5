<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  
 */
class SigninController extends BaseController {

    public function index(){

    	//用户昵称
        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = array('like', "%{$c_nickname}%");
        }

        //共签到天数
        $c_count = trim(I('c_count'));
        if (!empty($c_count)) {
            $w['a.c_count'] = $c_count;
        }

        //状态
        $c_status = trim(I('c_status'));
        if (!empty($c_status)) {
            $w['a.c_status'] = $c_status;
        }


        $db = M('Users_signup as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
    

    //关系列表
    public function tuijian(){

        //用户昵称
        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = array('like', "%{$c_nickname}%");
        }

        //用户手机
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }

        // 商家昵称
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['ui.c_nickname'] = array('like', "%{$c_name}%");
        }

        // 商家手机
        $bus_phone = trim(I('bus_phone'));
        if (!empty($bus_phone)) {
            $w['ui.c_phone'] = $bus_phone;
        }

        
        //绑定关系来源
        $c_source = trim(I('c_source'));
        if (!empty($c_source)) {
            $w['a.c_source'] = $c_source;
        }


        $db = M('users_tuijian as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname,ui.c_nickname as c_name,u.c_phone,ui.c_phone as bus_phone';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $panrn['join1'] = 'left join t_users as ui on a.c_pcode=ui.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
    

    public function scanpay_tuijian(){

        //推荐人昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        //推荐人手机号
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = $c_phone;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['a.c_pcode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['a.c_pcode'] = $usinfo['c_ucode'];
            }
        }

        //绑定类型
        $c_type = trim(I('c_type'));
        if (!empty($c_type)) {
            $w['a.c_type'] = $c_type;
        }

        $c_openid = trim(I('c_openid'));
        if (!empty($c_openid)) {
            $w['a.c_openid'] = $c_openid;
        }
        
        $c_unionid = trim(I('c_unionid'));
        if (!empty($c_unionid)) {
            $w['a.c_unionid'] = $c_unionid;
        }
        //状态
        $c_lock = trim(I('c_lock'));
        if (!empty($c_lock)) {
            $w['a.c_lock'] = $c_lock;
        }



        $db = M('Scanpay_tuijian as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        // $panrn['join'] = 'left join t_users as u on a.c_pcode=u.c_ucode';
        // $panrn['join1'] = 'left join t_users as ui on a.c_pcode=ui.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];
        foreach ($data_list as $k => $v) {
            $uw['c_ucode'] = $v['c_pcode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname')->find();
            $data_list[$k]['c_nickname'] = $userinfo['c_nickname'];
            
        }


        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
        


    //银行管理

    public function merchant_branch(){

        //银行名称
        $c_bankname = trim(I('c_bankname'));
        if (!empty($c_bankname)) {
            $w['a.c_bankname'] =$c_bankname;
        }

        //城市
        $c_cityname = trim(I('c_cityname'));
        if (!empty($c_cityname)) {
            $w['a.c_cityname'] = $c_cityname;
        }

        //省份名称
        $c_provincename = trim(I('c_provincename'));
        if (!empty($c_provincename)) {
            $w['a.c_provincename'] = $c_provincename;
        }

        //支行名称
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['a.c_name'] = $c_name;
        }


        $db = M('Merchant_branch as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
    

    //银行添加   
    public function merchant_add(){
        $this->action = 'Signin/merchant_add';
        if (IS_AJAX) 
        {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            // if (empty($jsondata['code'])) {
            //     $this->ajaxReturn(Message(1000,'联行号名称不能为空'));
            // }
            if (empty($jsondata['name'])) {
                $this->ajaxReturn(Message(1000,'支行名称不能为空'));
            }
            // if (empty($jsondata['citycode'])) {
            //     $this->ajaxReturn(Message(1000,'银行ID不能为空'));
            // }
            if (empty($jsondata['c_bankname'])) {
                $this->ajaxReturn(Message(1000,'银行名称不能为空'));
            }
            // if (empty($jsondata['bankname'])) {
            //     $this->ajaxReturn(Message(1000,'省份ID不能为空'));
            // }
            // if (empty($jsondata['provincename'])) {
            //     $this->ajaxReturn(Message(1000,'省份名称不能为空'));
            // }
            // if (empty($jsondata['cityname'])) {
            //     $this->ajaxReturn(Message(1000,'城市ID不能为空'));
            // }
            // if (empty($jsondata['name'])) {
            //     $this->ajaxReturn(Message(1000,'城市名称不能为空'));
            // }
            
            $data['c_code'] = $jsondata['code'];
            $data['c_provincecode'] = $jsondata['provincecode'];
            $data['c_citycode'] = $jsondata['citycode'];
            $data['c_bankcode'] = $jsondata['bankcode'];
            $data['c_bankname'] = $jsondata['c_bankname'];
            $data['c_provincename'] = $jsondata['provincename'];
            $data['c_cityname'] = $jsondata['cityname'];
            $data['c_name'] = $jsondata['name'];
            // $data['c_ctime'] = date('Y-m-d H:i:s');
            $result = M('Merchant_branch')->add($data);
            if($result){
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1002,'添加失败'));
            }  
        }

        $this->banklist = M('Merchant_bank')->select();
        $this->display();
    }



    //地址管理

    public function region(){

        //地址名称
        $region_name = trim(I('region_name'));
        if (!empty($region_name)) {
            $w['a.region_name'] = $region_name;
        }


        $db = M('Region as a');

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.region_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*';
        // $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }



    //地址添加   
    public function region_add()
    {
        $this->action = 'Signin/region_add';
        if (IS_AJAX) {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['region_id'])) {
                $this->ajaxReturn(Message(1000,'地址ID不能为空'));
            }
            if (empty($jsondata['parent_id'])) {
                $this->ajaxReturn(Message(1000,'上级地址ID不能为空'));
            }
            if (empty($jsondata['name'])) {
                $this->ajaxReturn(Message(1000,'地址名称不能为空'));
            }
            if (empty($jsondata['type'])) {
                $this->ajaxReturn(Message(1000,'地址类型不能为空'));
            }
            // if (empty($jsondata['agency'])) {
            //     $this->ajaxReturn(Message(1000,'代理级别不能为空'));
            // }
            if (empty($jsondata['upaycode'])) {
                $this->ajaxReturn(Message(1000,'地址编码不能为空'));
            }
            $data['region_id'] = $jsondata['region_id'];
            $data['parent_id'] = $jsondata['parent_id'];
            $data['region_name'] = $jsondata['name'];
            $data['region_type'] = $jsondata['type'];
            // $data['agency_id'] = $jsondata['agency'];
            $data['c_upaycode'] = $jsondata['upaycode'];
            // $data['c_ctime'] = date('Y-m-d H:i:s');
            $result = M('Region')->add($data);
            if($result){
                $this->ajaxReturn(Message(0,'添加成功'));
            }else{
                $this->ajaxReturn(Message(1002,'添加失败'));
            }
        }
        $this->display();
    }

}