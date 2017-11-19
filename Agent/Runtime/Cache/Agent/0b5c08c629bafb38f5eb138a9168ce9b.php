<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Agent/View/Public/images/favicon.ico">
<title>商家后台管理--商家审核</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
	window.onload=function(){
		$('#li-3 a').addClass('hover');
	}
</script>

</head>

<body>

	<div id="top-bg">
		<div class="top-main-w">
			<div class="top-operate">
				<ul>
					<li>欢迎您：<?php echo (session('_AGENT_NAME')); ?></li>
					<li><a href="javascript:;" style="border-right:solid 1px #999;padding-right:12px;">提取账号</a></li>
					<li><a href="">刷新</a></li>
					<li><a href="/wldApp/agent.php/Agent/Login/logout">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Agent/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Agent/Information/index">公告</a></li>
						<li id="li-2"><a href="/wldApp/agent.php/Agent/Personal/shopinfo">上级代理</a></li>
						<li id="li-3"><a href="/wldApp/agent.php/Agent/Shopcheck/index">商家审核</a></li>
						<li id="li-4"><a href="/wldApp/agent.php/Agent/Codecheck/index">串码中心</a></li>
						<li id="li-5"><a href="/wldApp/agent.php/Agent/Download/index">资料下载</a></li>
						<li id="li-6">
							<a href="/wldApp/agent.php/Agent/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-6" class="hidden-box">
						<ul id="son-menu">
							<li><a href="/wldApp/agent.php/Agent/Personal/index">资料设置</a></li>
							<li><a href="/wldApp/agent.php/Agent/Personal/updatepwd">密码修改</a></li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>

<script type="text/javascript">

$(document).ready(function(){
    $('#li-6').hover(function(){
        $('#menubox-6').slideDown(300);
    },function(){
        $('#menubox-6').hide();
    });
    $('.hidden-box').hover(function(){
    	$("#li-6 a").addClass("hover");
        $(this).show();
    },function(){
        $(this).slideUp(200);
    	$("#li-6 a").removeClass("hover");
    });
});

function exitLogin()
{
	parent.window.location.href="<?php echo U('Index/index');?>";
	window.opener.location.reload();
}

</script>

<div class="content-s w_960">
	<div class="page-title">
		<div class="page-tit-name">全部商家</div>
	</div>
	<form id="form1" action="" method="get">
		<div id="search">
			<input type="text" name="keys" placeholder="输入搜索关键字">
			<input class="button" type="submit" value="搜索">
		</div>
	</form>
	<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "$baoqian" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="agent-list-box" onclick="window.location.href='/wldApp/agent.php/Agent/Shopcheck/details?Id=<?php echo ($vo["c_id"]); ?>'">
		<div class="agent-list-l fl">
			<a href="javascript:;">
				<img src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_headimg"]); ?>" alt="" class="agentimgs">
				<img src="/wldApp/Agent/Agent/View/Public/images/eye.png" alt="">
			</a>
		</div>
		<div class="agent-list-r fl">
			<div class="agent-n-s">
				<div class="agent-names"><?php echo ($vo["c_name"]); ?></div>
				<?php if ($vo['c_checked'] == 3) { ?>
				<div class="agent-state state1">已激活</div>
				<?php }else { ?>
				<div class="agent-state state2">未激活</div>
				<?php } ?>
			</div>
			<div class="agent-types types1">
				<?php if ($vo['c_type'] == 1) { ?>
				个人
				<?php } else { ?>
				企业
				<?php } ?>
			</div>


			<?php if ($vo['c_checked'] == 3) { ?>
			<div class="agent-company" style="color:#46aafa;font-size:18px;">已通过审核</div>
			<?php }else if($vo['c_checked'] == 2 ){ ?>
			<div class="agent-company" style="color:#46aafa;font-size:18px;">等待区代审核</div>
			<?php }else if($vo['c_checked'] == 0 ){ ?>
			<div class="agent-company" style="color:#46aafa;font-size:18px;">等待代理审核</div>
			<?php }else{ ?>
			<div class="agent-company" style="color:#ff7112;font-size:18px;">未通过审核</div>
			<?php } ?>

			<?php if ($vo['c_type'] == 2) { ?>
			<div class="agent-company">企业名称：<?php echo ($vo["c_company"]); ?></div>
			<?php } ?>
		</div>
	</div><?php endforeach; endif; else: echo "$baoqian" ;endif; ?>
	<div class="pages">
		<div>
		 <?php echo ($page); ?>
		</div>
	</div>

</div>


</body>
</html>