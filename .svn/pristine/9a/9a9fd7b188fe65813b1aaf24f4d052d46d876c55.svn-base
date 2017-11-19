<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldapp/Agent/Shop/View/Public/images/favicon.ico">
<title>商家后台管理--会员管理</title>
<link rel="stylesheet" type="text/css" href="/wldapp/Agent/Shop/View/Public/css/agent.css?v=1.3">
<script type="text/javascript" src="/wldapp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
	window.onload=function(){
		$('#li-3 a').addClass('hover');

		var imgh = $('.m-img').height();
		$('.member-c-list').css('line-height',imgh+'px');
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
					<li><a href="/wldapp/agent.php/Shop/Login/logout">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldapp/Agent/Shop/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldapp/agent.php/Shop/Information/index">公告</a></li>		
						<!-- <li id="li-2"><a href="/wldapp/agent.php/Shop/Shop/producelist">我的商品</a></li> -->
						<li id="li-3"><a href="/wldapp/agent.php/Shop/Member/index">会员管理</a></li>
						<li id="li-4"><a href="/wldapp/agent.php/Shop/Download/index">资料下载</a></li>
						<li id="li-5">
							<a href="/wldapp/agent.php/Shop/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-5" class="menubox hidden-box">						
						<ul id="son-menu">
							<li><a href="/wldapp/agent.php/Shop/Personal/index">资料设置</a></li>
							<li><a href="/wldapp/agent.php/Shop/Personal/updatepwd">密码修改</a></li>
							<li><a href="/wldapp/agent.php/Shop/Personal/shopinfo">上级代理</a></li>
						</ul>
					</div>
				    <!-- <div id="menubox-2" class="menubox hidden-box hidden-loc-us">						
						<ul id="son-menu-2">
							<li><a href="/wldapp/agent.php/Shop/Shop/index">上传商品</a></li>
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

<div class="content-s w_960" style="padding-bottom:40px;">	
	<div class="page-title">
		<div class="page-tit-name">我的邀请码</div>
	</div>
	<div class="member-check-num">
		<div class="member-c-num fl">
			<p style="padding-top:35px;"><?php echo 200 - $userinfo['c_num']; ?></p>
			<p>已激活人数</p>
		</div>
		<div class="member-f-num fl">
			<p style="padding-top:35px;"><?php echo $userinfo['c_num']; ?></p>
			<p>剩余免费激活人数</p>				
		</div>
	</div>
	<ul class="member-c-list">	
		<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
			<div class="m-time fr">邀请时间：<?php echo ($vo["c_addtime"]); ?></div>
			<div class="m-img fl"><img src="<?php echo ($vo["c_headimg"]); ?>"></div>
			<div class="m-name fl"><?php echo ($vo["c_nickname"]); ?></div>
			<div class="m-tel fl"><?php echo ($vo["c_phone"]); ?></div>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>		
	</ul>
	<div class="pages">
	    <div>
	     <?php echo ($page); ?>
	    </div>      
	  </div>
</div>

</body>
</html>