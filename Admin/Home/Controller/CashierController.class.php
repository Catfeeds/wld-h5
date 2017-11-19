<?php
namespace Home\Controller;
use Think\Controller;
/**
*   收银员管理
*/
class CashierController extends BaseController {

	//收银员列表
	public function index()
	{

         $db = M('A_cashier as a');

		//收银员昵称
        $c_nickname = trim(I('c_nickname'));
        if (!empty($c_nickname)) {
            $w['u.c_nickname'] = array('like', "%{$c_nickname}%");
        }
        
        //收银员账号
        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $w['u.c_phone'] = $c_phone;
        }

        //收银员编号
        $c_name = trim(I('c_name'));
        if (!empty($c_name)) {
            $w['a.c_name'] = $c_name;
        }

        //所属商家昵称
        $anickname = trim(I('anickname'));
        if (!empty($anickname)) {
            $w['ui.c_nickname'] = array('like', "%{$anickname}%");
        }

        //所属商家账号
        $aphone = trim(I('aphone'));
        if (!empty($aphone)) {
            $w['ui.c_phone'] = $aphone;
        }

        //所属收银台
        $c_deskid = trim(I('c_deskid'));
        if (!empty($c_deskid)) {
            $w['a.c_deskid'] = $c_deskid;
        }
        
        //工作状态
        $c_work = trim(I('c_work'));
        if (!empty($c_work)) {
            $w['a.c_work'] = $c_work;
        }

        //激活状态
        $c_status = trim(I('c_status'));
        if (!empty($c_status)) {
            $w['a.c_status'] = $c_status;
        }

        //删除状态
        $c_delete = trim(I('c_delete'));
        if (!empty($c_delete)) {
            $w['a.c_delete'] = $c_delete;
        }
        
        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数
        
        //分页显示数据  
        $panrn['field'] = 'a.*,u.c_nickname,u.c_phone,ui.c_phone as aphone,ui.c_nickname as anickname,b.c_name as c_names';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $panrn['join1'] = 'left join t_users as ui on a.c_acode=ui.c_ucode';
        $panrn['join2'] = 'left join t_a_cashier_desk as b on b.c_id=a.c_deskid';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
  
    //签到记录
    public function signinfo() {
        $db = M('A_cashier_sign as a');

        $Id = I('Id');
        if (!empty($Id)) {
            $w['a.c_cashierid'] = $Id;
        }

        //日期
        $c_datetime = trim(I('c_datetime'));
        if (!empty($c_datetime)) {
            $w['a.c_datetime'] = $c_datetime;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_datetime desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'a.*,ui.c_name as c_names';
        $panrn['join'] = 'left join t_a_cashier as u on u.c_id=a.c_cashierid';
        $panrn['join1'] = 'left join t_a_cashier_desk as ui on ui.c_id=a.c_deskid';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);

        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
    

    //收银台列表
    public function cashier_desk()
    {

         $db = M('A_cashier_desk as a');

        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }
        
        //注册手机号
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
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }
    
    //收银台添加   
    public function modify(){
        $this->action = 'Cashier/modify';
        if (IS_AJAX) 
        {
            $str = I('str');
            $formbul = str_replace('&quot;', '"', $str);
            $jsondata = objarray_to_array(json_decode($formbul));
            if (empty($jsondata['ucode'])) {
                $this->ajaxReturn(Message(1003,'请选择商家'));
            }
                
            $parr['ucode'] = $jsondata['ucode'];
            $parr['sign'] = 1;
            $result = IGD('Cashier','Store')->AddDesk($parr);
            $this->ajaxReturn($result);
        }
        $this->display();
    }

    //修改收银台名称
    public function desk_info(){
        $Id = I('Id');
        if (!empty($Id)) {
            $where['c_id'] = $Id;
        } 
      
        $agentinfo = M('A_cashier_desk')->where($where)->find();
        $agentinfo['c_nickname'] = M('users')->where($where)->getField('c_nickname');
        if(IS_POST){
            foreach (array_keys($_POST) as $key => $value) {
                $narr = array_values($_POST)[$key];
                $needarr =  $c_name;
                if (in_array($key, $needarr)) {
                    if (!isset($_POST[$value])) {
                        $this->error("带*内容项必须填写！");
                     }
                }
            } 

            $filepath = 'agent';
            $Id = $_POST['Id'];
            $w['c_id'] = $Id;
            $result = M('A_cashier_desk')->where($w)->save($_POST);
            if($result){
                $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Cashier/cashier_desk';
                echo '<script language="javascript">alert("编辑成功");</script>';
                echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
                $this->error("编辑失败");
            }
        }
        $this->vo = $agentinfo;
        $this->root_url = GetHost()."/";
        $this->display();
    }

    //商家收银台已满列表
    public function cashier_number()
    {
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
                $w['c_ucode'] = $usinfo['c_ucode'];
            }
        }

        $w['c_delete'] = 2;
        $limit = 25;//分页数

        $count = M('A_cashier_desk')->where($w)->group('c_ucode')->having('COUNT(c_id)>=6')->count();
        $Page = getpage($count,$limit);
        $list = M('A_cashier_desk')->where($w)->group('c_ucode')->having('ct>=6')->limit($Page->firstRow.','.$Page->listRows)->field('*,COUNT(c_id) as ct')->order('ct desc')->select();
        foreach ($list as $key => $value) {
            $userinfo = M('Users')->where(array('c_ucode'=>$value['c_ucode']))->field('c_nickname,c_phone')->find();

            $list[$key]['c_nickname'] = $userinfo['c_nickname'];
            $list[$key]['c_phone'] = $userinfo['c_phone'];
        }
        // dump($data['list']);die;
        $this->list = $list;
        $this->page= $Page->show();// 分页显示输出
        $this->post = I('param.');//筛选返条件返回值
        $this->count = $count;//分页\      
        $this->root_url = GetHost()."/";

        $this->display();
    }
}