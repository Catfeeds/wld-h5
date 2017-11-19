<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>激活码管理</title>
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/style.css?v=1.7">
	<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
</head>
<body>
	<div class="activation">
		<div class="activation_top">
			<div class="activation_title">激活串码</div>
		</div>
		<div class="activation_content">
			<li class="fl activation_list">
				<div class="activation_abimg">
					<img src="/wldApp/Agent/Home/View/Public/images/index/used.png" alt="">
				</div>
				<div class="activation_text">
					WLD8SZCM60
				</div>	
				<div class="activation_nobel">
					NO1
				</div>		
				<img src="/wldApp/Agent/Home/View/Public/images/index/yello.png" alt="">	
			</li>
			<li class="fl activation_list" onMouseOver="Overlist(this);" onMouseOut="Outlist(this);">
				<div class="activation_text">
					WLD8SZCM60
				</div>	
				<div class="activation_nobel">
					NO2
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
					<div class="activation_but" onclick="jionwrite(this,'a13213');">
						马上填写
					</div>
				</div>
				<img src="/wldApp/Agent/Home/View/Public/images/index/yello.png" id="actimg" alt="">
			</li>
			<li class="fl activation_list" onMouseOver="Overlist(this);" onMouseOut="Outlist(this);">
				<div class="activation_text">
					WLD8SZCM60
				</div>	
				<div class="activation_nobel">
					NO3
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
					<div class="activation_but" onclick="jionwrite(this,'b41515');">
						马上填写
					</div>
				</div>
				<img src="/wldApp/Agent/Home/View/Public/images/index/yello.png" id="actimg" alt="">
			</li>
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
			window.location.href = 'http://localhost/wldApp/Agent.php/Home/Activation/business?code='+code;
		} else {
			window.location.href = 'http://localhost/wldApp/Agent.php/Home/Activation/person?code='+code;	
		}			
	}
</script>
</html>