<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商家后台管理中心</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/style.css?v=1.7">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/agent.js"></script>
</head>
<body> 	
    <div class="login_top">
       <div class="login_img">
           <img src="/wldApp/Agent/Agent/View/Public/images/index/logo.png" alt="">
       </div>
    </div> 
    <form action="" method="post">
    <div class="login_body">
        <div class="login_box">
            <div class="box_img">
               <img src="/wldApp/Agent/Agent/View/Public/images/index/arealogin.png" alt="">
           </div>
           <div class="box_content">
                <div class="login_row">
                    <span><img src="/wldApp/Agent/Agent/View/Public/images/index/ico_Login_name.png">用户名</span>
                    <input name="username" type="text" id="sj_admin_name" class="txt_name" placeholder="请输入用户名">
                </div>
                <div class="clear"></div>
                <div class="login_row login_row_2">
                    <span><img src="/wldApp/Agent/Agent/View/Public/images/index/ico_login_pwd.png">密 码</span>
                    <input name="password" type="password" id="sj_admin_pwd" class="txt_name txt_pwd" placeholder="请输入密码">                  
                </div>
                <div class="clear"></div>
                <div class="login_row">
                    <span><img src="/wldApp/Agent/Agent/View/Public/images/index/ico_login_yzm.png">验证码</span>
                    <input name="verify" type="text" id="sj_admin_yzm" class="txt_name txt_yzm" placeholder="请输入验证码">
                    <div id="yzm_Img" class="fl">
                        <a href="javascript:;">
                            <img id="verifyImg" style="height:40px;width:90px;" src="<?php echo U('verify');?>" onClick="fleshVerify();" title="单击刷新验证码">
                        </a>
                    </div>      
                </div>
                <div class="clear"></div>

                <div class="login_btn">
                    <input type="submit" value="" name="seller_admin_login" id="seller_admin_login" class="seller-login-btn">
                </div>
           </div>
        </div>
    </div>       
    </form>

    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/layer/1.9.3/layer.js"></script>
</body> 
<script type="text/javascript">
    window.onload = function() {
        var ph = $(window).height();
        var th = $('.login_top').outerHeight();
        $('.login_body').css('height',ph - th);
    }
    
    //刷新验证码
    function fleshVerify() {
        var time = new Date().getTime();
        document.getElementById("verifyImg").src = "/wldApp/agent.php/Agent/Index/verify/" + time;
    }
</script> 
</html>