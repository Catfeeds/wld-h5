<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">

<title>商家后台管理</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/sellerIndex.css">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>

</head>

<body id="left_bg">

	<div class="pretant_type fl" id="channel">
		<ul>
			<li class="" id="channel00">
				<a href="<?php echo U('Information/index');?>" target="right">
				<img src="/wldApp/Agent/Agent/View/Public/images/index/notice.png">
				<span>公告</span>
				</a>
				<div class="li_bon">
					<img src="/wldApp/Agent/Agent/View/Public/images/index/bon.png" alt="">
				</div>				
			</li>	
			<li class="" id="channel01">				
				<a href="<?php echo U('Check/index');?>" target="right">
				<img src="/wldApp/Agent/Agent/View/Public/images/index/shopcheck.png">
				<span>微商申请</span>
				</a>
				<div class="li_bon">
					<img src="/wldApp/Agent/Agent/View/Public/images/index/bon.png" alt="">
				</div>
			</li>
			<li class="" id="channel02">				
				<a href="<?php echo U('Material/index');?>" target="right">
				<img src="/wldApp/Agent/Agent/View/Public/images/index/materirl.png">
				<span>资料库</span>
				</a>
				<div class="li_bon">
					<img src="/wldApp/Agent/Agent/View/Public/images/index/bon.png" alt="">
				</div>
			</li>
			<li class="" id="channel03">				
				<a href="<?php echo U('Check/Checklist');?>" target="right">
				<img src="/wldApp/Agent/Agent/View/Public/images/index/ico-weishang.png">
				<span>我的微商</span>
				</a>
				<div class="li_bon">
					<img src="/wldApp/Agent/Agent/View/Public/images/index/bon.png" alt="">
				</div>
			</li>
		</ul>
	</div>
	
	<div class="clear"></div>
<script type="text/javascript">	
   $(function() {
   	$("#channel > ul li:eq(0)").addClass("active");
   	$("#channel > ul li:eq(0)").find('.li_bon').show();
   	/*父栏目-子栏目切换*/
   	$("#channel ul li").each(function(index) {
   		$(this).click(function() {
   			$(".son_type_1").removeClass("block");
   			$("#channel ul li").removeClass("active");
   			$("#channel > ul li:eq(" + index + ")").addClass("active");
   			$('.li_bon').hide();
   			$("#channel > ul li:eq(" + index + ")").find('.li_bon').show();
   		});
   	});

   });
</script>
</body>
</html>