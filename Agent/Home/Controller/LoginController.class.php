<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  登录注册管理
 */
class LoginController extends Controller{

  	//公共处理器   判断是否登录
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

    Public function _initialize(){
        //移动设备浏览，则切换模板
        if (ismobile()) {
            //设置默认默认主题为 Mobile
            C('DEFAULT_THEME','Mobile');
        }
        //............你的更多代码.......

   		//初始化引入微信分享类
        vendor('Wxshare.wxshare');
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));        
        $signPackage = $wxshare->GetSignPackage();      
        $this->assign('signPackage',$signPackage);        
    }
    

    public function tsphone()
    {
        dump(S('15823360508'));
    }
    /*登录地址*/
    public function index()
    {
        $admin1 = cookie('_ADMIN_UCODE');
        $admin2 = cookie('_AGENT_UCODE');
        $admin3 = cookie('_SHOP_UCODE');

        if(!empty($admin1)){
            session('_ADMIN_UCODE', $admin1);
            session('_ADMIN_NAME', cookie('_ADMIN_NAME'));
            header("Location:" . WEB_HOST.'/agent.php/Home/Information/index');
        } else if (!empty($admin2)) {
            session('_AGENT_UCODE', $admin2);
            session('_AGENT_NAME', cookie('_AGENT_NAME'));
            header("Location:" . WEB_HOST.'/agent.php/Agent/Information/index');
        } else if (!empty($admin3)) {
            session('_SHOP_UCODE', $admin3);
            session('_SHOP_NAME', cookie('_SHOP_NAME'));
            header("Location:" . WEB_HOST.'/agent.php/Shop/Information/index');
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
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串
    public function check_verify($code, $id = '')
    {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    // 用户登录
    public function login()
    {
        $phone = I('phone');
        // if (!checkMobile($phone)) {
        //     $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        // }
        $pwd = I('pwd');
        if (empty($phone) || empty($pwd)) {
            $this->ajaxReturn(Message(1004, "请检查您的账号或密码"));
        }
        if (!$this->check_verify(I('verify'))) {
            $this->ajaxReturn(Message(1004, "验证码不正确"));
        }
        $pwd = encrypt($pwd,C('ENCRYPT_KEY'));
        $login = D('Login', 'Service');
        $result = $login->login($phone,$pwd);
        if ($result['code'] == 0) {
            $data = $result['data'];
            if ($data['c_isagent'] == 1) {
                session('_ADMIN_UCODE', $data['c_ucode']);
                session('_ADMIN_NAME', $data['admin_name']);
                cookie('_ADMIN_UCODE',$data['c_ucode'],3600*24*7);
                cookie('_ADMIN_NAME',$data['admin_name'],3600*24*7);
                $type = 1;
            } else if ($data['c_isagent'] == 2) {
                session('_AGENT_UCODE', $data['c_ucode']);
                session('_AGENT_NAME', $data['admin_name']);
                cookie('_AGENT_UCODE',$data['c_ucode'],3600*24*7);
                cookie('_AGENT_NAME',$data['admin_name'],3600*24*7);
                $type = 2;
            } else {
                session('_SHOP_UCODE', $data['c_ucode']);
                session('_SHOP_NAME', $data['admin_name']);
                cookie('_SHOP_UCODE',$data['c_ucode'],3600*24*7);
                cookie('_SHOP_NAME',$data['admin_name'],3600*24*7);
                $type = 3;
            }

            $this->ajaxReturn(MessageInfo(0,'登录成功',$type));
        }
        $this->ajaxReturn($result);
    }

    /**区代账号激活地址*/
    public function areaagent()
    {
        $this->type = 1;
        $this->display('activate');
    }
    /*市代账号激活地址*/
    public function cityagent()
    {
        $this->type = 2;
        $this->display('activate');
    }
    /*微商账号激活地址*/
    public function shopagent()
    {
        $this->type = 3;
        $this->code = I('num');
        
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];   
        $this->assign('signPackage',$signPackage);
        
        $weixinshare["c_pthumbnail"] = WEB_HOST."/Resource/Common/img/logo.png";    
        $weixinshare["c_sharetitle"] = '小蜜——商家账号注册激活';
        $weixinshare["c_discript"] = '一款帮助微个体经营个人流量的APP';
        $this->assign('weixinshare',$weixinshare); 
        
        $this->display('activate');        
    }

    //手机短信验证
    public function SendVerify()
    {
        if (IS_AJAX) {
            $parr['telephone'] = I('phone');
            $regnum = IGD('Common', 'Redis')->Rediesgetucode($parr['telephone']);
            if (I('type') == 1) {
                //查询卡信息
                $wherincode['c_code'] = I('card');
                $incodedata = M('Invite_code')->where($wherincode)->find();

                //查询串码信息
                $indata = M('Check_codelist')->where($wherincode)->find();
                if (!$incodedata && !$indata) {
                    $this->ajaxReturn(Message(1001,'请先输入正确的激活码'));
                }
            }
            
            if (!$regnum) {
                // 生成6位数验证码
                $regnum = rand(100000, 999999);
            }

            $parr['type'] = I('type');
            $parr['userid'] = C('TEl_USER');
            $parr['account'] = C('TEl_ACCESS');
            $parr['password'] = C('TEl_PASSWORD');
            $parr['content'] = "【微领地小蜜】尊敬的会员您好，验证码为：".$regnum."有效期120s，为保证您的账号安全，请勿外泄。感谢您的申请！";
            $register = D('Login', 'Service');
            $returndata = $register->SendVerify($parr);
            if ($returndata['code'] == 0) {
                IGD('Common', 'Redis')->RediesStoreSram($parr['telephone'], $regnum, 3600);
            }
            $this->ajaxReturn($returndata);
        } else {
            $this->ajaxReturn(Message(1000,'非法请求'));
        }
    }

    // 注册
    public function register()
    {
        if (IS_AJAX) {
            $phone = I('phone');
            if (!checkMobile($phone)) {
                $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
            }

            $verifyid = IGD('Common', 'Redis')->Rediesgetucode($phone);
            if ($verifyid == '') {
                $this->ajaxReturn(Message(1000, "验证码失效，请重新获取验证码！"));
            }

            $regverify = I('verify');
            if ($regverify != $verifyid) {
                $this->ajaxReturn(Message(1001, "验证码错误！"));
            }

            $pwd = I('pwd');
            $parr['phone'] = $phone;
            $parr['pwd'] = encrypt($pwd,C('ENCRYPT_KEY'));
            $parr['incode'] = I('incode');
            $parr['type'] = I('type');
            $register = D('Login','Service');
            $userinfo = $register->register($parr);
            $this->ajaxReturn($userinfo);
        }
    }


    //忘记密码
    function updatapwd()
    {
        if(IS_AJAX) {
            $phone = I('phone');
            if (!checkMobile($phone)) {
                $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
            }

            $verifyid = IGD('Common', 'Redis')->Rediesgetucode($phone);
            if ($verifyid == '') {
                $this->ajaxReturn(Message(1000, "验证码失效，请重新获取验证码！"));
            }

            $regverify = I('verify');
            if ($regverify != $verifyid) {
                $this->ajaxReturn(Message(1001, "验证码错误！"));
            }

            $pwd = I('pwd');
            // if (!checkPwd($pwd) || empty($pwd)) {
            //     $this->ajaxReturn(Message(1003, "密码过于简单，请设置6到16位字符！"));
            // }

            $parr['telephone'] = $phone;
            $parr['pwd'] = encrypt($pwd,C('ENCRYPT_KEY'));

            $forgetpwd = D('Login', 'Service');
            $userinfo = $forgetpwd->forgetpwd($parr);
            $this->ajaxReturn($userinfo);
        }
        $this->display();
    }

    //退出登录
    public function logout(){
        cookie('_ADMIN_UCODE',null);
        cookie('_ADMIN_NAME',null);
        session('_ADMIN_UCODE',null);
        session('_ADMIN_NAME',null);
        $this->redirect('Login/index');
    }

    //语音短信
    public function SendMp3Verify()
    {
        if(IS_AJAX) {
            $phone = I('phone');
            $regnum = IGD('Common', 'Redis')->Rediesgetucode($phone);
            //查询卡信息
            $wherincode['c_code'] = I('card');
            $incodedata = M('Invite_code')->where($wherincode)->find();

            //查询串码信息
            $indata = M('Check_codelist')->where($wherincode)->find();
            if (!$incodedata && !$indata) {
                $this->ajaxReturn(Message(1001,'请先输入正确的激活码'));
            }
            if (!$regnum) {
                // 生成6位数验证码
                $regnum = rand(100000, 999999);
            }

            $encode = 'UTF-8';  //页面编码和短信内容编码为GBK。重要说明：如提交短信后收到乱码，请将GBK改为UTF-8测试。如本程序页面为编码格式为：ASCII/GB2312/GBK则该处为GBK。如本页面编码为UTF-8或需要支持繁体，阿拉伯文等Unicode，请将此处写为：UTF-8
            $username = '18672391547';  //用户名
            $password_md5 = md5('meilian123');  //32位MD5密码加密，不区分大小写
            $apikey = 'd4fa34f84bb30ae84bc877d6dea86815';  //apikey秘钥（请登录 http://m.5c.com.cn 短信平台-->账号管理-->我的信息 中复制apikey）
            $content = '您好，您的验证码是：'.$regnum;  //要发送的短信内容，特别注意：签名必须设置，网页验证码应用需要加添加【图形识别码】。
            $result = $this->sendSMS($username,$password_md5,$apikey,$phone,$content,$encode);  //进行发送
            if(strpos($result,"success")>-1) {
                IGD('Common', 'Redis')->RediesStoreSram($phone, $regnum, 3600);
                //提交成功
                $this->ajaxReturn(Message(0,'发送成功'));
            } else {
                //提交失败
                $this->ajaxReturn(Message(1000,'发送失败'));
            }
        } else {
            $this->ajaxReturn(Message(1000,'非法请求'));
        }
    }

    //发送接口
    public function sendSMS($username,$password_md5,$apikey,$mobile,$contentUrlEncode,$encode)
    {
        //发送链接（用户名，密码，apikey，手机号，内容）
        $url = "http://m.5c.com.cn/api/send/index.php?";  //如连接超时，可能是您服务器不支持域名解析，请将下面连接中的：【m.5c.com.cn】修改为IP：【115.28.23.78】
        $data=array
        (
            'username'=>$username,
            'password_md5'=>$password_md5,
            'apikey'=>$apikey,
            'mobile'=>$mobile,
            'content'=>$contentUrlEncode,
            'encode'=>$encode,
        );
        $result = $this->curlSMS($url,$data);
        return $result;
    }

    public function curlSMS($url,$post_fields=array())
    {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);//用PHP取回的URL地址（值将被作为字符串）
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//使用curl_setopt获取页面内容或提交数据，有时候希望返回的内容作为变量存储，而不是直接输出，这时候希望返回的内容作为变量
        curl_setopt($ch,CURLOPT_TIMEOUT,30);//30秒超时限制
        curl_setopt($ch,CURLOPT_HEADER,1);//将文件头输出直接可见。
        curl_setopt($ch,CURLOPT_POST,1);//设置这个选项为一个零非值，这个post是普通的application/x-www-from-urlencoded类型，多数被HTTP表调用。
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);//post操作的所有数据的字符串。
        $data = curl_exec($ch);//抓取URL并把他传递给浏览器
        curl_close($ch);//释放资源
        $res = explode("\r\n\r\n",$data);//explode把他打散成为数组
        return $res[2]; //然后在这里返回数组。
    }
}