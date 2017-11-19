<?php
namespace Home\Controller;
use Think\Controller;
/**
*   首页管理
*/
class IndexController extends Controller {
    //编码控制
    public function __construct()
    {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    //登录页
    public function login()
    {

        if (IS_POST) {
            $username = trim(I('username'));
            $password = trim(I('password'));
            if(empty($username) || empty($password)){
                $this->error('账号或密码为空');die; 
                // echo '1';die();
            }
            if (!$this->check_verify(I('verify'))) {
                $this->error("验证码不正确！");die;
            }
            $db = M('admin_users');
            $row = $db->where("c_username='$username'")->find();
            if(empty($row)){
                $this->error('账号不存在');die;
            }
            $password = encrypt($password,C('ENCRYPT_KEY'));
            if($row['c_password'] != $password){
                $this->error('密码不正确');die;
            }

            $w['c_username'] = $username;
            $w['c_password'] = $password;
            $state = $db->where($w)->getField('c_state');

            if($state == 0){
                $this->error('此帐号已经被禁止登录');die;
            }

            $sql = "select * from t_admin_function where c_id in(select c_functionid from t_admin_role_function where c_roleid in(select c_roleid from t_admin_user_role where c_userid=" . $row['c_id'] . "))";
            $db = M('');
            $wldrolelist = $db->query($sql);
            if (!$wldrolelist) {
                $this->error('此账号没有分配权限！');
            }

            session('start');
            session('wldrolelist', $wldrolelist);  //设置session

            $row['c_addtime']=Date('Y-m-d H:i:s');
            session('zongwld',$row);

            session('ADMIN_NAME', $row['c_username']);  //设置session

            $this->success('登录成功',U('Index/index'));die;
        }
    	$this->show();
    }

     // 验证码方法
    Public function verify()
    {
        ob_clean();
        $config =    array(
            'fontSize'    =>    32,    // 验证码字体大小
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    false, // 关闭验证码杂点
            'codeSet'      =>    '0123456789',
        );
        $Verify =     new \Think\Verify($config);
        $Verify->entry();
    }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串
    public function check_verify($code, $id = '')
    {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    // 首页
    public function index()
    {
        $admin=session('zongwld');
        if(empty($admin)){
            echo '<script language="javascript">top.location="'.U('Index/login').'";</script>';
        }
        $this->menulist = $this->GetMenuList();
        // var_dump( $this->menulist);die();
        $this->show();
    }

    // 首页欢迎
    public function welcome()
    {
        $this->display();
    }


    //个人信息页面
    public function person()
    {
        $this->action = 'person';
        $Admin_users = M('Admin_users');
        $userwhere['c_username'] = array('like','%'.session('ADMIN_NAME').'%');
        $vo = $Admin_users->where($userwhere)->find();
        $vo['c_password'] = decrypt($vo['c_password'],C('ENCRYPT_KEY'));
        $this->assign('vo',$vo);
        $this->userrolelist = M('Admin_user_role as a')->join('left join t_admin_role as b on a.c_roleid=b.c_id')->where('a.c_userid='.$vo['c_id'])->field('b.c_rolename')->select();

        if(IS_POST){
            if (empty($_POST['fullname'])) {
                $this->error('管理员全名不能为空');
            }
            if (empty($_POST['username'])) {
                $this->error('管理员帐号不能为空');
            }
            if (empty($_POST['password'])) {
                $this->error('管理员登录密码不能为空');
            }
            if (empty($_POST['phone'])) {
                $this->error('管理员手机号不能为空');
            }

            $where['c_id'] = I('Id');
            $data['c_username'] = $_POST['username'];
            $data['c_password'] = encrypt($_POST['password'],C('ENCRYPT_KEY'));
            $data['c_fullname'] = $_POST['fullname'];
            $data['c_phone'] = $_POST['phone'];
            $result = $Admin_users->where($where)->save($data);
            if(!$result) {
                $Admin_users->rollback();
                $this->error('编辑失败');
            }

            echo '<script language="javascript">alert("保存成功！");</script>';
            echo '<script language="javascript">window.parent.location.href="'.U('Index/index').'";</script>';die();
        }

        $this->display();
    }

    //添加登录日志
    protected function InsertLog($userid, $username, $url, $parameter)
    {
        $data['c_userid'] = $userid;
        $data['c_username'] = $username;
        $data['c_url'] = $url;
        $data['c_parameter'] = $parameter;
        $data['c_ip'] = get_client_ip();
        $data['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = M('Admin_log')->add($data);
        return $result;
    }

    // 后台管理员session
    public function AdminSession() {
        $admin = session('zongwld');
        if(empty($admin)){
            echo '<script language="javascript">top.location="'.U('Index/login').'";</script>';
        }
        $url = $_SERVER['HTTP_HOST'];
        $url = $url . $_SERVER['REQUEST_URI'];
        $wldrolelist = session("wldrolelist");
        if ($wldrolelist == null) {
            // $this->error('您的账号未分配任何权限', U('Index/index'));
              echo '<script language="javascript">alert("您的帐号没有权限访问");</script>';die();
        }

        $result = $this->CheckAuthority($url);
        $admin = session('zongwld');
        $userid = $admin['c_id'];
        $username = $admin['c_username'];
        $parameter = $this->GetParameter();
        if (!$result) {
            // $this->error('您的帐号没有权限访问', U('Index/index'));
            echo '<script language="javascript">alert("您的帐号没有权限访问");</script>';die();
        } else {
            $this->InsertLog($userid, $username, $url, $parameter);
            return true;
        }
    }

    // 获取用户权限对应的栏目列表
    public function GetMenuList()
    {
        $menudata = M('Admin_menu')->select();
        $menuinfo = array();
        $count = 0;
        foreach ($menudata as $key => $value) {
            if ($value['c_pid'] == 0) {
                $count1 = 0;
                $child = array();
                foreach ($menudata as $key1 => $value1) {
                    if ($value1['c_pid'] == $value['c_id']) {
                        if ($this->CheckAuthority($value1['c_murl'])) {
                            $child[$count1] = $value1;
                            $count1++;
                        }
                    }
                }
                if (count($child) > 0) {
                    $menuinfo[$count]['c_icon'] = $value['c_icon'];
                    $menuinfo[$count]['c_mname'] = $value['c_mname'];
                    $menuinfo[$count]['child'] = $child;
                    $count++;
                }
            }
        }
        return $menuinfo;
    }

    // 验证权限
    protected function CheckAuthority($url) {
        $wldrolelist = session("wldrolelist");

        $result = false;
        if ($wldrolelist == null) {
            return $result;
        }

        foreach ($wldrolelist as $key => $value) {            
            $Matchingstr = $value["c_functionkey"];
            if (strpos($Matchingstr,'?') !== false) {
                $Matchingstr = explode('?', $Matchingstr)[0];
            }
            if (strpos($url,$Matchingstr) !== false) {
                $result = true;
                break;
            }
            if ($value["c_functionkey"] == $url) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    function Verifice($getstr) { //验证服务器方法
        if (empty($getstr)) {
            return false;
        }
        $data1 = "";
        foreach ($getstr as $key => $value) {
            if ($key != '_URL_') {
                $data1 = $data1 . $key . $value . "|";
            }
        }
        return $data1;
    }

    //获取参数
    protected function GetParameter() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $input = $this->Verifice($_POST);
                break;
            case 'PUT':
                parse_str(file_get_contents('php://input'), $input);
                break;
            default:
                $input = $this->Verifice($_GET);
        }
        return $input;
    }

    // 图标库
    public function iconfont()
    {
        $this->display();
    }


    //退出系统
    public function signout(){
        session('zongwld',null);
        session('_ADMIN_NAME',null);
        session("wldrolelist", null);
        $this->redirect('Index/index');
    }

}