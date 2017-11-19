<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>激活码管理</title>
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/style.css?v=1.7">
	<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
	<style type="text/css">
	.activation_content li{list-style: none;}
	</style>
</head>
<body>
	<div class="activation">
		<div class="activation_top">
			<div class="activation_title">激活串码</div>
		</div>
		<div class="activation_content">
			<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if ($vo['c_state'] == 1) { ?>
				<li class="fl activation_list" onclick="location.href='/wldApp/agent.php/Home/Activation/business3?code=<?php echo ($vo["c_code"]); ?>'">
					<div class="activation_abimg">
						<img src="/wldApp/Agent/Home/View/Public/images/index/used.png" alt="">
					</div>
					<div class="activation_text">
						<?php echo ($vo["c_code"]); ?>
					</div>	
					<div class="activation_nobel">
						NO.<?php echo ($i); ?>
					</div>		
					<img src="/wldApp/Agent/Home/View/Public/images/index/yello.png" alt="">	
				</li>
				<?php } else { ?>
				<li class="fl activation_list" onMouseOver="Overlist(this);" onMouseOut="Outlist(this);">
					<div class="activation_text">
						<?php echo ($vo["c_code"]); ?>
					</div>	
					<div class="activation_nobel">
						NO.<?php echo ($i); ?>
					</div>
					<div class="activation_form">
						<div class="activation_redio">
							<input type="radio" name="actid" value="1">
							《企业代理申请表》
						</div>
						<div class="activation_redio">
							<input type="radio" name="actid" value="2">
							《个人代理申请表》
						</div>
						<div class="activation_but" onclick="jionwrite(this,'<?php echo ($vo["c_code"]); ?>');">
							马上填写
						</div>
					</div>
					<img src="/wldApp/Agent/Home/View/Public/images/index/yello.png" id="actimg" alt="">
				</li>	
				<?php } endforeach; endif; else: echo "" ;endif; ?>

			
			
		</div>
	</div>

    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>
</body>
<script type="text/javascript">
	// 鼠标经过列表事件
	function Overlist(ts){		
		$(ts).find('.activation_text').hide();
		$(ts).find('.activation_nobel').hide();
		$(ts).find('#actimg').attr('src','/wldApp/Agent/Home/View/Public/images/index/block.png');
		$(ts).find('.activation_form').show();		
	}
	// 鼠标离开列表事件
	function Outlist(ts){
		$(ts).find('.activation_form').hide();
		$(ts).find('.activation_text').show();
		$(ts).find('.activation_nobel').show();
		$(ts).find('#actimg').attr('src','/wldApp/Agent/Home/View/Public/images/index/yello.png');	
	}

	function jionwrite(ts,code) {
		var od = $(ts).parent().find('input[type="radio"][name="actid"]:checked').val();
		if (!od) {
			layer.msg('请选择个人或者企业',{icon:10,time:2000}); 
			return;
		}
		if (od == 1) {  //企业
			window.location.href = 'http://localhost/wldApp/agent.php/Home/Activation/business?code='+code;
		} else {
			window.location.href = 'http://localhost/wldApp/agent.php/Home/Activation/person?code='+code;	
		}			
	}
</script>
</html>