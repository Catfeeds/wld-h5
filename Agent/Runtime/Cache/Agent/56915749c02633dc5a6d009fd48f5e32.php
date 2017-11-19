<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
	<title>微商申请</title>
	<meta http-equiv="content-type" content="text/html" charset="utf-8">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/main.css?v=1.7">
	<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/agent.js"></script>
</head>
<body>
<!-- wrap是最外面大的白色背景div -->
<div class="wrap">
	<form action="check3" method="post" accept-charset="utf-8" enctype="multipart/form-data">
	<div class="content">
		<!-- head是微商申请的头部 -->
		<div class="head">
			<div class="head-left">
				微商申请
			</div>
		</div>
        <!-- 下面的图片是到了第几步的图片 -->
		<img src="/wldApp/Agent/Agent/View/Public/images/index/step3.png" class="step">
		<div class="business_show">
			帐号正在激活中...，需审核后才能登录
		</div>
       	<div class="business_coun">
			生成帐号：<?php echo ($info["c_username"]); ?><br/>
			初始密码：<?php echo ($info["c_password"]); ?>
		</div>	
        
	</div>
	</form>
</div>
<style type="text/css">
	.business_show{margin-left: 40px;padding: 2% 0px;font-size: 16px;color: #333;}
	.business_coun{margin-left: 40px;font-size: 16px;color: #555;}
</style>
	<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/layer/1.9.3/layer.js"></script>
</body>
</html>