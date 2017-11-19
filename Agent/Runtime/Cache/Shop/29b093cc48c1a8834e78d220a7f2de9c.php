<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Shop/View/Public/images/favicon.ico">
<title>商家后台管理--资料下载</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
	window.onload=function(){
		$('#li-4 a').addClass('hover');
	}
</script>

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
	<div class="material">
		<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="material_top">
			<div class="material_title"><?php echo ($vo["c_name"]); ?></div>
		</div>
		<div class="material_mid none">
			<div class="fl"><input type="checkbox">&nbsp;全选</div>
			<div class="material_del fl" onclick="dwonall();">批量下载</div>
		</div>
		<div class="material_list">				
			<table>	
			<?php foreach ($vo['chidren'] as $key => $value) { ?>				
				<tr>
					<td width="30"><input type="checkbox" name="Id" value="<?php echo $value['c_id'] ?>"></td>
					<td><?php echo $value['c_name'] ?></td>
					<td><?php echo $value['c_addtime'] ?></td>
					<td>
						<a href="/wldApp/agent.php/Shop/Download/downfile?Id=<?php echo $value['c_id'] ?>" title="下载">
							<img src="/wldApp/Agent/Shop/View/Public/images/index/down.png" alt="">
						</a>
					</td>
				</tr>
			<?php } ?>
			</table>				
		</div><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>

    <script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/layer/1.9.3/layer.js"></script>
    <script type="text/javascript">
    	// 图片批量打包下载
    	function dwonall(tg) {
    		
    	}
    </script>

</div>

</body>
</html>