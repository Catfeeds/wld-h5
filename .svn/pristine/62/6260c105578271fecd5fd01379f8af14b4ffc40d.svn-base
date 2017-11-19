<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Home/View/Public/images/favicon.ico">
<title>商家后台管理--资料下载</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
	window.onload=function(){
		$('#li-5 a').addClass('hover');
	}
</script>

</head>

<body>

	<div id="top-bg">
		<div class="top-main-w">
			<div class="top-operate">
				<ul>
					<li>欢迎您：<?php echo (session('_ADMIN_NAME')); ?></li>
					<li><a href="javascript:;" style="border-right:solid 1px #999;padding-right:12px;">提取账号</a></li>
					<li><a href="">刷新</a></li>
					<li><a href="/wldApp/agent.php/Home/Login/logout">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Home/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Home/Information/index">公告</a></li>
						<li id="li-2"><a href="/wldApp/agent.php/Home/Agentntrol/index">代理管理</a></li>
						<li id="li-3"><a href="/wldApp/agent.php/Home/Shopcheck/index">商家审核</a></li>
						<li id="li-4"><a href="/wldApp/agent.php/Home/Stringcheck/index">串码审核</a></li>
						<li id="li-5"><a href="/wldApp/agent.php/Home/Download/index">资料下载</a></li>
						<li id="li-6">
							<a href="/wldApp/agent.php/Home/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-6" class="hidden-box">
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
	<div class="material">
		<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="material_top">
			<div class="material_title"><?php echo ($vo["c_name"]); ?></div>
		</div>
		<div class="material_mid none">
			<div class="fl"><input type="checkbox" id="checkall-<?php echo ($i); ?>">&nbsp;全选</div>
			<div class="material_del fl" onclick="dwonall();" id="download">批量下载</div>
		</div>
		<div class="material_list">				
			<table>	
			<?php foreach ($vo['chidren'] as $key => $value) { ?>				
				<tr>
					<td width="30"><input type="checkbox" name="Id" value="<?php echo $value['c_id'] ?>"></td>
					<td><?php echo $value['c_name'] ?></td>
					<td><?php echo $value['c_addtime'] ?></td>
					<td>
						<a href="/wldApp/agent.php/Home/Download/downfile?Id=<?php echo $value['c_id'] ?>" title="下载">
							<img src="/wldApp/Agent/Home/View/Public/images/index/down.png" alt="">
						</a>
					</td>
				</tr>
			<?php } ?>
			</table>				
		</div><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>

    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>

 

</div>

</body>
</html>