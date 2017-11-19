<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">

<title>商家后台管理--公告中心</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/agent.css?v=1.3">
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
	window.onload=function(){
		$('#li-1 a').addClass('hover');
	}
</script>

</head>

<body>

	<div id="top-bg">
		<div class="top-main-w">
			<div class="top-operate">
				<ul>
					<li>欢迎您：孙行者</li>
					<li><a href="javascript:;" style="border-right:solid 1px #999;padding-right:12px;">提取账号</a></li>
					<li><a href="javascript:;">刷新</a></li>
					<li><a href="javascript:;">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Shop/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Shop/Notice/index">公告</a></li>
						<li id="li-2"><a href="/wldApp/agent.php/Shop/Shop/producelist">我的商品</a></li>
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
						</ul>
					</div>
				    <div id="menubox-2" class="menubox hidden-box hidden-loc-us">						
						<ul id="son-menu-2">
							<li><a href="/wldApp/agent.php/Shop/Shop/index">上传商品</a></li>
						</ul>
					</div>
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
	<div class="notice-list">
		<div class="notice-img fl">
			<img src="/wldApp/Agent/Shop/View/Public/images/agent-typ-tip.jpg" alt="">
		</div>
		<div class="notice-info fl">
			<div class="notice-tit-tim">
				<div class="notice-tit fl"><a href="" class="notice-tit-a">为何出国旅游不再遥远</a></div>
				<div class="notice-time fr">2016-5-26</div>
			</div>
			<div class="notice-desc">
				为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远...<a href="" class="details">查看详情</a>
			</div>
		</div>
	</div>
	<div class="notice-list">
		<div class="notice-img fl">
			<img src="/wldApp/Agent/Shop/View/Public/images/agent-typ-tip.jpg" alt="">
		</div>
		<div class="notice-info fl">
			<div class="notice-tit-tim">
				<div class="notice-tit fl"><a href="" class="notice-tit-a">为何出国旅游不再遥远</a></div>
				<div class="notice-time fr">2016-5-26</div>
			</div>
			<div class="notice-desc">
				为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远...<a href="" class="details">查看详情</a>
			</div>
		</div>
	</div>
	<div class="notice-list">
		<div class="notice-img fl">
			<img src="/wldApp/Agent/Shop/View/Public/images/agent-typ-tip.jpg" alt="">
		</div>
		<div class="notice-info fl">
			<div class="notice-tit-tim">
				<div class="notice-tit fl"><a href="" class="notice-tit-a">为何出国旅游不再遥远</a></div>
				<div class="notice-time fr">2016-5-26</div>
			</div>
			<div class="notice-desc">
				为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远...<a href="" class="details">查看详情</a>
			</div>
		</div>
	</div>
	<div class="notice-list">
		<div class="notice-img fl">
			<img src="/wldApp/Agent/Shop/View/Public/images/agent-typ-tip.jpg" alt="">
		</div>
		<div class="notice-info fl">
			<div class="notice-tit-tim">
				<div class="notice-tit fl"><a href="" class="notice-tit-a">为何出国旅游不再遥远</a></div>
				<div class="notice-time fr">2016-5-26</div>
			</div>
			<div class="notice-desc">
				为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远为何出国旅游不再遥远...<a href="" class="details">查看详情</a>
			</div>
		</div>
	</div>


<!--提示资料未完成-->
<div class="agent-tip-bg"></div>
<div class="agent-tip-pop none" id="tip-step-1">
	<div class="agent-tip-step1">
		<img src="/wldApp/Agent/Shop/View/Public/images/agent-notice.png" alt="">
		<div class="agent-tip-font1">您的资料还未完成</div>
		<div class="agent-tip-font2">请立即填写<span>区域</span>代理资料</div>
		<div class="agent-tip-btn1">好</div>
	</div>
</div>

<div class="agent-tip-pop none" id="tip-step-2">
	<div class="agent-tip-step1">
		<div class="agent-tip-st2-tit">请选择区代代理资质</div>
		<div class="agent-tip-team"><input type="radio" name="agenttype"><span class="radio-font">企业区代资料</span></div>
		<div class="agent-tip-sign"><input type="radio" name="agenttype"><span class="radio-font">个人区代资料</span></div>
		<img src="/wldApp/Agent/Shop/View/Public/images/agent-noticebg.png" alt="">		
	</div>
</div>



</div>

</body>
</html>