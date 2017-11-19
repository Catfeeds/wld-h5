<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Shop/View/Public/images/favicon.ico">
<title>商家后台管理--修改密码</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
	window.onload=function(){
		$('#li-5 a').addClass('hover');
	}
</script>
<style type="text/css">

	input[type="checkbox"]{ outline: none; width: 22px;height: 14px; background:none;visibility: hidden;}
	input[type="checkbox"]:checked{ outline: none; width: 22px;height: 14px; background:none;}
	.showpwd{position: relative;}
	input[type="checkbox"] + label{ cursor: pointer; display: block; width: 22px;height: 14px; background: url(/wldApp/Agent/Shop/View/Public/images/mobile/yanjing-hui.png) no-repeat;background-size: 100% 100%;position: absolute;right: 10%;top: 36%;}
	input[type="checkbox"]:checked + label{width: 22px;height: 14px; background: url(/wldApp/Agent/Shop/View/Public/images/mobile/yanjing.png) no-repeat;background-size: 100% 100%;}
	.resource-r{width: 52%;}
	.showpwd{width: 18%;color: #555;font-size: 14px;text-align: right; padding-right: 2%;}
</style>
</head>

<body>

	<div id="top-bg">
		<div class="top-main-w">
			<div class="top-operate">
				<ul>
					<li>欢迎您：<?php echo (session('_SHOP_NAME')); ?></li>
					<li><a href="javascript:;" style="border-right:solid 1px #999;padding-right:12px;">提取账号</a></li>
					<li><a href="">刷新</a></li>
					<li><a href="/wldApp/agent.php/Shop/Login/logout">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Shop/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Shop/Information/index">公告</a></li>		
						<!-- <li id="li-2"><a href="/wldApp/agent.php/Shop/Shop/producelist">我的商品</a></li> -->
						<li id="li-3"><a href="/wldApp/agent.php/Shop/Member/index">会员管理</a></li>
						<li id="li-4"><a href="/wldApp/agent.php/Shop/Download/index">资料下载</a></li>
						<li id="li-5">
							<a href="/wldApp/agent.php/Shop/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-5" class="menubox hidden-box">						
						<ul id="son-menu">
							<li><a href="/wldApp/agent.php/Shop/Personal/index">资料设置</a></li>
							<li><a href="/wldApp/agent.php/Shop/Personal/updatepwd">密码修改</a></li>
							<li><a href="/wldApp/agent.php/Shop/Personal/shopinfo">上级代理</a></li>
						</ul>
					</div>
				    <!-- <div id="menubox-2" class="menubox hidden-box hidden-loc-us">						
						<ul id="son-menu-2">
							<li><a href="/wldApp/agent.php/Shop/Shop/index">上传商品</a></li>
						</ul>
					</div> -->
				</div>
				
			</div>
		</div>
	</div>

<script type="text/javascript">

$(document).ready(function(){
    // $('#li-5').hover(function(){        
    //     $('#menubox-5').slideDown(300);
    // },function(){        
    //     $('#menubox-5').hide();
    // });
    // $('.hidden-box').hover(function(){
    // 	$("#li-5 a").addClass("hover");          
    //     $(this).show();
    // },function(){
    //     $(this).slideUp(200);  
    // 	$("#li-5 a").removeClass("hover");        
    // });   


    var num;
    $('.agent-channel>li[id]').hover(function(){    	
        /*下拉框出现*/
        var Obj = $(this).attr('id');
        num = Obj.substring(3, Obj.length);        
        $('#menubox-'+num).slideDown(300);
    },function(){
        /*下拉框消失*/
        $('#menubox-'+num).hide();
    });

    $('.hidden-box').hover(function(){
    	$("#li-"+num+" a").addClass("hover"); 
        $(this).show();
    },function(){
    	$("#li-"+num+" a").removeClass("hover"); 
        $(this).slideUp(200);
    });

});	

function exitLogin()
{
	parent.window.location.href="<?php echo U('Index/index');?>";
	window.opener.location.reload(); 
}

</script>	

<div class="content-s w_960">	
	<form action=""  method="post" id="form" name="forms">		
		<div class="page-title">
			<div class="page-tit-name">修改密码</div>
		</div>
		<div class="resource-main">
			<div class="resource-list">
				<div class="resource-l fl">旧密码：</div>
				<div class="resource-r fl">
					<input type="password" id="pwd-1" name="pwd_old" class="resource-text">
					<input type="text" id="text-1" name="txt-pwd_old" class="resource-text none">
				</div>
				<div id="click1" class="fl showpwd">
					<input type="checkbox" id="chb-1" onclick="showpwd(1)">
					<label for="chb-1"></label>
				</div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl">新密码：</div>		
				<div class="resource-r fl">
					<input type="password" id="pwd-2" name="pwd_new" class="resource-text">
					<input type="text" id="text-2" name="txt-pwd_new" class="resource-text none">
				</div>
				<div id="click2" class="fl showpwd">
					<input type="checkbox" id="chb-2" onclick="showpwd(2)">
					<label for="chb-2"></label>
				</div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl">确认密码：</div>
				<div class="resource-r fl">
					<input type="password" id="pwd-3" name="loginpwd" class="resource-text">
					<input type="text" id="text-3" name="txt-loginpwd" class="resource-text none">
				</div>
				<div id="click3" class="fl showpwd">
					<input type="checkbox" id="chb-3" onclick="showpwd(3)">
					<label for="chb-3"></label>
				</div>
			</div>	
			
			<div class="resource-sub" onclick="subform();"><img src="/wldApp/Agent/Shop/View/Public/images/editor.png" alt="" class="fl editor"></div>
		</div>		
	<form>	
</div>
<script type="text/javascript">
	function subform() {
		$('#form').submit();
	}
	function showpwd (n) {		
		var chb = $("input:checkbox[id='chb-"+n+"']:checked").val();
		if (chb) {
			if($("#text-"+n).val()!="" || $("#pwd-"+n).val()!=""){
				$("#text-"+n).val($("#pwd-"+n).val());
				$("#pwd-"+n).val($("#text-"+n).val());
				$("#pwd-"+n).hide();
				$("#text-"+n).show();			
			}else{
				alert("请输入密码！");return false;
			}				
		
		}else {
			if($("#text-"+n).val()!="" || $("#pwd-"+n).val()!=""){
				$("#pwd-"+n).val($("#text-"+n).val());
				$("#text-"+n).val($("#pwd-"+n).val());
				$("#text-"+n).hide();
				$("#pwd-"+n).show();
			}else{
				alert("请输入密码！");return false;
			}
		}		
	}	

</script>

</body>
</html>