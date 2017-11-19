<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Home/View/Public/images/favicon.ico">
<title>后台管理登录</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/login.css?v=1.7">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/jquery.cookie.js"></script>
</head>
<body> 	

<form action="" method="post">
      <div class="login-main-bg">
            <div class="login-top-main">
                <div class="login-top-logo fl"><img src="/wldApp/Agent/Home/View/Public/images/login/login-02_03.png" alt=""></div>
                <div class="login-top-font fl">商家后台管理中心</div>
            </div>
            <div class="login-info-main">
                <div class="login-info-tit">登录您的帐号</div>
                <!-- <div class="login-font">输入您的用户名或手机号码</div> -->
                <div class="login-text">
                    <div class="login-icon fl">
                        <img src="/wldApp/Agent/Home/View/Public/images/login/login-02_03_2.png">
                    </div>
                    <div class="login-input fl">
                        <input type="tel" class="usertext" name="phone" id="phone" placeholder="输入您的用户名或手机号码">
                    </div>
                </div>
                <div class="login-text">
                    <div class="login-icon fl">
                        <img src="/wldApp/Agent/Home/View/Public/images/login/login-02_03_1.png">
                    </div>
                    <div class="login-input fl">
                        <input type="password" class="usertext" name="pwd" id="pwd" placeholder="输入您的密码">
                    </div>
                </div>
                <div class="login-verify">
                    <input name="verify" type="text" id="verify" class="usertext txt_yzm fl" placeholder="请输入验证码">
                    <div id="yzm_Img" class="fl">
                        <a href="javascript:;">
                            <img id="verifyImg" src="<?php echo U('verify');?>" onClick="fleshVerify();" title="单击刷新验证码">
                        </a>
                    </div>
                </div>
                <div class="login-remember"><input type="checkbox" id="ck_rmbUser">&nbsp;&nbsp;记住我</div>
                <div class="login-sub">
                    <div class="sub-login" onclick="logins();">登录</div>
                </div>
                <div class="login-get-update">
                    <a href="/wldApp/agent.php/Home/Login/updatepwd" class="fl">找回密码</a><a href="/wldApp/agent.php/Home/Login/getaccount" class="fr">提取账号</a>
                </div>
            </div>
            <img src="/wldApp/Agent/Home/View/Public/images/login/login-bg.jpg" alt="">
      </div>
</form>

    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>
</body> 
<script type="text/javascript">
    window.onload = function() {
     
    }
$(document).ready(function() {
    if ($.cookie("rmbUser") == "true") {
        $("#ck_rmbUser").prop("checked", true);
        $("#phone").val($.cookie("username"));
        $("#pwd").val($.cookie("password"));
    }
}); 

//记住用户名密码 
function save() {
    if ($("#ck_rmbUser").prop("checked")) {
        var username = $("#phone").val();
        var password = $("#pwd").val();
        $.cookie("rmbUser", "true", {
            expires: 1
        }); //存储一个带7天期限的cookie 
        $.cookie("username", username, {
            expires: 1
        });
        $.cookie("password", password, {
            expires: 1
        });
    } else {
        $.cookie("rmbUser", "false", {
            expire: -1
        });
        $.cookie("username", "", {
            expires: -1
        });
        $.cookie("password", "", {
            expires: -1
        });
    }
};

    /*登录页面*/
    function logins() {
        var phone = $('#phone').val();
        var pwd = $('#pwd').val();
        var verify = $('#verify').val();
        if (phone == "" || pwd == "") {
            alert('请输入账号密码');
            return false;
        }
        var datastr = "phone=" + phone + "&pwd=" + pwd +"&verify="+verify;
        $.ajax({
            type: 'get',
            url: '/wldApp/agent.php/Home/Login/login',
            data: datastr,
            cache: false,
            dataType: 'json',
            success: function(obj) {
                var msg = eval(obj);
                if (msg['code'] == 0) {
                   save();
                    if (msg['data'] == 1) {
                        var url = '/wldApp/agent.php/Home/Information/index';
                    } else if (msg['data'] == 2) {
                        var url = '/wldApp/agent.php/Agent/Information/index';
                    } else if (msg['data'] == 3) {
                        var url = '/wldApp/agent.php/Shop/Information/index';
                    }
                    window.location.href = url;
                } else {
                    alert(msg['msg']);                    
                }
            }
        });
    }

    //刷新验证码
    function fleshVerify() {
        var time = new Date().getTime();
        document.getElementById("verifyImg").src = "/wldApp/agent.php/Home/Login/verify/" + time;
    }
</script> 
</html>