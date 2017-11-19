<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Home/View/Public/images/favicon.ico">
<title>后台管理中心--激活账号</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/login.css?v=1.7">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/jquery.cookie.js"></script>
<style type="text/css">
.inputli{width: 100%;overflow: hidden;font-size: 14px;border-bottom: 1px solid #ebebeb; padding-bottom: 3%;}
.inputli1{border:0px;margin-left: 8%;}
.agree_left{padding:2% 0px;}
.agree_right{padding:2% 0px;margin-left:5px;}
.agree_right a{color: #555;}

</style>
</head>
<body>

<form action="" method="post">
      <div class="login-main-bg">
            <div class="login-top-main">
                <div class="login-top-logo fl"><img src="/wldApp/Agent/Home/View/Public/images/login/login-02_03.png" alt=""></div>
                <div class="login-top-font fl">后台管理中心</div>
            </div>
            <div class="login-info-main">
                <div class="login-info-tit">激活账号</div>
                <div class="login-text">
                    <div class="login-input fl" style="width:70%;">
                        <input type="tel" class="usertext" name="incode" id="incode" placeholder="请输入<?php if($type==1){ ?>钻石卡号<?php }else{ ?>激活串码<?php } ?>">
                    </div>
                </div>
                <div class="login-text">
                    <div class="login-input fl">
                        <input type="tel" class="usertext" name="phone" id="phone" placeholder="请输入常用手机号">
                    </div>
                </div>
                <div class="login-text">
                    <div class="login-dx fr">
                        <div id="verify_btn1" onmouseup="getverify1();">语音验证码</div>
                    </div>
                    <div class="login-dx fr">
                        <div id="verify_btn" onmouseup="getverify(1);">短信验证码</div>
                    </div>
                    <div class="login-input fl" style="width:40%;">
                        <input type="tel" class="usertext" name="verify" id="verify" placeholder="输入验证码">
                    </div>
                </div>
                <div style="font-size:14px;color:#999999;margin:0 10% 5% 10%;">
                    *若未收到短信验证码，可选择语音验证（免费接听）
                </div>
                <div class="login-text">
                    <div class="login-input fl">
                        <input type="password" class="usertext" name="pwd" id="pwd" placeholder="请输入登录密码">
                    </div>
                </div>
                <?php if($type==3){ ?>
                <div class="inputli inputli1">
                    <div class="fl agree_left">
                        <input type="checkbox" id="chk_agree" checked="checked" name="chk_agree">
                    </div>
                    <div class="fl agree_right">
                        <a class="c6" href="/wldApp/agent.php/Home/Login/read">阅读并同意《微领地小蜜商户服务协议》</a>
                    </div>
                </div>
                <?php } ?>
                <div class="login-sub">
                    <div class="sub-login" onclick="registers()">激活账号</div>
                </div>

            </div>
            <img src="/wldApp/Agent/Home/View/Public/images/login/login-bg.jpg" alt="">
      </div>
</form>

    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>
</body>
<script type="text/javascript">

/*注册*/
function registers() {
    var type = '<?php echo $type ?>';
    var phone = $('#phone').val();
    var verify = $('#verify').val();
    var pwd = $('#pwd').val();
    var incode = $('#incode').val();
    if (phone == '' || verify == '' || pwd == '' || incode == '') {
        alert('请输入完整的信息');
        return false;
    }
    if(type==3){
        if(!$("#chk_agree").is(":checked")){
            alert('您还未阅读《微领地小蜜商户服务协议》');
            return false;
        }
    }
    $.post('/wldApp/agent.php/Home/Login/register', {
            pwd: pwd,
            verify: verify,
            incode: incode,
            phone: phone,
            type: "<?php echo $type ?>"
        },
        function(obj) {
            var msg = eval(obj);
            if (msg['code'] == 0) {
                alert('注册成功');
                window.location.href = '/wldApp/agent.php/Home/Login/index';
            } else {
                alert(msg['msg']);
            }
        });
}

/*验证手机号码*/
function phonetxt () {
    var phone = $('#phone').val();
    if (phone!="") {
        if (!(/^1[3|4|5|7|8][0-9]\d{8}$/.test(phone))) {
            alert("手机号码格式错误！");
            return false;
        }else{
            return true;
        }
    }else{
        alert("请输入手机号码！");
        return false;
    }

}

//发送验证码时添加cookie
function addCookie(name,value,expiresHours){
    var date = new Date();
    date.setTime(date.getTime()+120*1000);//只能这么写，10表示10秒钟
    $.cookie(name,value, {expires: date});
}
//根据名字获取cookie的值
function getCookieValue(name){
   return $.cookie(name);
}
/*清除cookie*/
function clearCookie(name) {
    return $.cookie(name,null);
}

//获取语音验证码
function getverify1() {
    var phonenum = $("#phone").val();
    var result = phonetxt();
    if(result){
        addCookie("secondsremained1",120,120);//添加cookie记录,有效时间120s
        settime1($("#verify_btn1"));
        doPostBack1(phonenum);
    }
}

v1 = getCookieValue("secondsremained1");//获取cookie值
if(v1>0){
    settime1($("#verify_btn1"));//开始倒计时
}

//开始倒计时
var countdown1;
function settime1(obj) {
    countdown1=getCookieValue("secondsremained1");
    if (countdown1 == 0) {
        document.getElementById('verify_btn1').onmouseup = function(){
            getverify1();
        }
        obj.removeAttr("disabled");
        obj.text("语音验证码");
        clearCookie('secondsremained1');
        return;
    } else {
        document.getElementById('verify_btn1').onmouseup = null;
        obj.attr("disabled", true);
        obj.text("" + countdown1 + "后重发");
        countdown1--;
        addCookie("secondsremained1",countdown1,countdown1+1);
    }
    setTimeout(function() { settime1(obj) },1000) //每1000毫秒执行一次
}
function doPostBack1(phonenum) {
    $.post('/wldApp/agent.php/Home/Login/SendMp3Verify', {
            phone: phonenum,
            card: $('#incode').val()
        },
        function(obj) {
            var msg = eval(obj);
            if (msg['code'] == 0) {
                alert(msg['msg']);
                return true;
            } else {
                alert(msg['msg']);
                clearCookie('secondsremained1');
                $("#verify_btn1").text("语音验证码");
                return false;
            }
        });
}

/*获取验证码*/
function getverify(type){
    var phonenum = $("#phone").val();
    var result = phonetxt();
    if(result){
        addCookie("secondsremained",120,120);//添加cookie记录,有效时间120s
        settime($("#verify_btn"));//开始倒计时
        doPostBack(phonenum,type);
    }
}

v = getCookieValue("secondsremained");//获取cookie值
if(v>0){
    settime($("#verify_btn"));//开始倒计时
}


//利用ajax提交到后台的发短信接口
function doPostBack(phonenum,type) {
    var type = type;
    $.post('/wldApp/agent.php/Home/Login/SendVerify', {
            type: type,
            phone: phonenum,
            card: $('#incode').val()
        },
        function(obj) {
            var msg = eval(obj);
            if (msg['code'] == 0) {
                alert(msg['msg']);
                return true;
            } else {
                alert(msg['msg']);
                clearCookie('secondsremained');
                $("#verify_btn").text("短信验证");
                return false;
            }
        });

}

//开始倒计时
var countdown;
function settime(obj) {
    countdown=getCookieValue("secondsremained");
    if (countdown == 0) {
        document.getElementById('verify_btn').onmouseup = function(){
            getverify(1);
        }
        obj.removeAttr("disabled");
        obj.text("短信验证");
        clearCookie('secondsremained');
        return;
    } else {
        document.getElementById('verify_btn').onmouseup = null;
        obj.attr("disabled", true);
        obj.text("" + countdown + "后重发");
        countdown--;
        addCookie("secondsremained",countdown,countdown+1);
    }
    setTimeout(function() { settime(obj) },1000) //每1000毫秒执行一次
}


</script>
</html>