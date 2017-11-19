<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>公告详情</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/agent.css?v=1.7">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
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
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Home/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Home/Notice/index">公告</a></li>
						<li id="li-2"><a href="/wldApp/agent.php/Home/Agentntrol/index">代理管理</a></li>
						<li id="li-3"><a href="/wldApp/agent.php/Home/Shopcheck/index">微商审核</a></li>
						<li id="li-4"><a href="/wldApp/agent.php/Home/Download/index">资料下载</a></li>
						<li id="li-5">
							<a href="/wldApp/agent.php/Home/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-5" class="hidden-box">						
						<ul id="son-menu">
							<li><a href="/wldApp/agent.php/Home/Personal/index">资料设置</a></li>
							<li><a href="/wldApp/agent.php/Home/Personal/updatepwd">密码修改</a></li>
						</ul>
					</div>
				</div>
				
			</div>
		</div>
	</div>

<script type="text/javascript">

$(document).ready(function(){
    $('#li-5').hover(function(){        
        $('#menubox-5').slideDown(300);
    },function(){        
        $('#menubox-5').hide();
    });
    $('.hidden-box').hover(function(){
    	$("#li-5 a").addClass("hover");          
        $(this).show();
    },function(){
        $(this).slideUp(200);  
    	$("#li-5 a").removeClass("hover");        
    });
});	

function exitLogin()
{
	parent.window.location.href="<?php echo U('Index/index');?>";
	window.opener.location.reload(); 
}

</script>	
    <div class="content-s w_960">
        <div class="notice-details">
            <div class="notice-titles">
                <?php echo ($vo["c_ptitle"]); ?>
            </div>
            <div class="notice-time-re">
                <div class="notice-de-tim fl">发表时间：<?php echo ($vo["c_addtime"]); ?></div>
                <div class="notice-de-res fr">文章来源：<?php echo ($vo["c_origin"]); ?></div>
            </div>
            <div class="notice-con-zw">
                <?php echo ($vo["c_content"]); ?>
            </div>  

        </div>
        
    </div>
</body> 
</html>