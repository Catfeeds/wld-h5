<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">

<title>商家后台管理</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/sellerIndex.css">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
</head>

<body id="top_bg">
	<div class="w_top_con w_100">
		
		<div class="fl top_left">
			<a href="javascript:void(0);">
				<img src="/wldApp/Agent/Agent/View/Public/images/sellerIndex/seller_logo.png">
			</a>
		</div>
		<div class="fr top_right">
			<ul>
				<li>欢迎您<span><a href="<?php echo U('Index/updatepassword');?>" target="right"><?php echo (session('_AGENT_NAME')); ?></a></span></li>
				<li>|</li>
				<li><a href="<?php echo U('Index/logout');?>" id="exitLogins" onclick="exitLogin()">退出登录</a></li>
				<li>
					<a href="javascript:;" onclick="window.parent.frames.right.location.reload()">
						<img src="/wldApp/Agent/Agent/View/Public/images/sellerIndex/ico_refresh.png">
					</a>
				</li>
			</ul>
			<div class="clear"></div>	
		</div>
		<div class="clear"></div>
		
	</div>
<script type="text/javascript">
	function exitLogin()
	{
		parent.window.location.href="<?php echo U('Index/index');?>";
		window.opener.location.reload(); 
	}
</script>

</body>
</html>