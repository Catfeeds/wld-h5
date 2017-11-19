<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Home/View/Public/images/favicon.ico">
<title>商家后台管理--商家审核</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/agent.css">
<link rel="stylesheet" href="/wldApp/Agent/Home/View/Public/css/viewer.min.css">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
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

<div class="content-s w_960" style="padding-bottom: 40px;">
	<div class="page-title">
		<div class="page-tit-name fl" style="color:#999;">全部代理</div><div class="page-tit-next fl"> > <?php echo ($data["c_name"]); ?>详情</div>
		<div class="clear"></div>
	</div>

	<div class="agent-details">
		<div class="agent-info-desc">
			<ul class="ul-details fl">
				<li>
					<div class="agent-d-left fl"><span class="span-name">姓名： <?php echo ($data['c_name']); ?></span></div>
					<div class="agent-d-right fl"><span>状态：</span>
					<?php if ($data['c_checked'] == 3){ ?>
						已激活
					<?php } else { ?>
						未激活
					<?php } ?>
					</div>
				</li>
				<li>
					<div class="agent-d-left fl"><span>代理编号：</span><?php echo ($data['c_dcode']); ?></div>
					<div class="agent-d-right fl"><span>商家类型：</span>
					<?php if ($data['c_type'] == 1) { ?>
					个人
					<?php } else { ?>
					企业
					<?php } ?>
					</div>
				</li>
				<li>
					<div class="agent-d-left fl"><span>手机号码：</span><?php echo ($data['c_phone']); ?></div>
					<div class="agent-d-right fl"><span>固定号码：</span><?php echo ($data['c_home_tel']); ?></div>
				</li>
				<li>
					<div class="agent-d-left fl"><span>邮箱：</span><?php echo ($data['c_email']); ?></div>
					<div class="agent-d-right fl"><span>QQ：</span><?php echo ($data['c_qq']); ?></div>
				</li>
				<!-- 企业 -->
				<?php if ($data['c_type'] == 2) { ?>
				<li>
					<div class="agent-d-left fl"><span>编码：</span><?php echo ($data['c_postcode']); ?></div>
					<div class="agent-d-right fl"><span>公司名称：</span><?php echo ($data['c_company']); ?></div>
				</li>
				<li>
					<div class="agent-d-left fl"><span>公司地址：</span><?php echo ($data['c_address']); ?></div>
				</li>
				<?php } else { ?>

				<?php } ?>
			</ul>
		</div>

		<?php if ($data['c_type'] == 2) { ?>
		<ul class="ul-certificate">
			<li>
				<div class="zjz-list">
					<div class="pic-front"><img data-original="<?php echo GetHost(2); ?>/<?php echo ($data['c_charter_img']); ?>" src="<?php echo GetHost(2); ?>/<?php echo ($data['c_charter_img']); ?>" alt=""></div>
					<img src="/wldApp/Agent/Home/View/Public/images/add.png" alt="">
				</div>
			</li>
			<li>
				<div class="zjz-list">
					<div class="pic-front"><img data-original="<?php echo GetHost(2); ?>/<?php echo ($data['c_company_sign']); ?>" src="<?php echo GetHost(2); ?>/<?php echo ($data['c_company_sign']); ?>" alt=""></div>
					<img src="/wldApp/Agent/Home/View/Public/images/add.png" alt="">
				</div>
			</li>
		</ul>
		<div class="zjz-list-side">
			<div class="pic-side fl">营业执照</div>
			<div class="pic-side fl" style="margin-left:155px;">企业标志</div>
		</div>
		<?php } else { ?>
		<ul class="ul-certificate">
			<li>
				<div class="zjz-list">
					<div class="pic-front"><img data-original="<?php echo GetHost(2); ?>/<?php echo ($data['c_idcard_img']); ?>" src="<?php echo GetHost(2); ?>/<?php echo ($data['c_idcard_img']); ?>" alt=""></div>
					<img src="/wldApp/Agent/Home/View/Public/images/add.png" alt="">
				</div>
			</li>
			<li>
				<div class="zjz-list">
					<div class="pic-front"><img data-original="<?php echo GetHost(2); ?>/<?php echo ($data['c_idcard_img1']); ?>" src="<?php echo GetHost(2); ?>/<?php echo ($data['c_idcard_img1']); ?>" alt=""></div>
					<img src="/wldApp/Agent/Home/View/Public/images/add.png" alt="">
				</div>
			</li>
		</ul>
		<div class="zjz-list-side">
			<div class="pic-side fl">正面</div>
			<div class="pic-side fl" style="margin-left:155px;">反面</div>
		</div>
		<?php } ?>
	</div>

	<?php if ($data['c_checked'] == 2) { ?>
	<div class="shop-check">
		<div class="check-pass fl"><input type="radio" value="1" checked="checked" name="check">&nbsp;通过审核</div>
		<div class="check-pass fl"><input type="radio" value="0" name="check">&nbsp;资料有误</div>
	</div>
	<div class="check-btn-pass" onclick="checked();">
		<img src="/wldApp/Agent/Home/View/Public/images/checkbtn1.png" alt="">
	</div>
	<?php } else if ($data['c_checked'] == 3) { ?>
	<div class="shop-check-font">已通过审核</div>
	<?php } else if ($data['c_checked'] == 0) { ?>
	<div class="shop-check-font">等待代理审核</div>
	<?php }else{ ?>
	<div class="shop-check-font">未通过审核</div>
	<?php } ?>
</div>
<script type="text/javascript">
function checked() {
	var keyUrl = '/wldApp/agent.php/Home/Information/ReadInfostatu';
	$.ajax({
		url: keyUrl,
		dataType: "json",
		async: false,
		success: function(data) {
			var msg = eval(data);
			if (msg['code'] == 0) {
				var checked = $('input[name=check]:checked').val();
				$.post('/wldApp/agent.php/Home/Shopcheck/checked', {
					checked: checked,
					sid: '<?php echo $sid ?>'
				}, function(obj) {
					var result = eval(obj);
					if (result['code'] == 0) {
						alert(result['msg']);
						window.location.href = '';
					} else {
						alert(result['msg']);
					}
				});
			} else {
				$('.check-btn-pass').removeAttr('onclick');
				alert('您提交的资料还未通过审核，暂不能做微商审核');
			}
		}
	});
}

</script>
<script src="/wldApp/Agent/Home/View/Public/js/viewer.min.js"></script>
<script type="text/javascript">
$(function(){
	$('.pic-front img').click(function(){
		$('.ul-certificate').viewer({url:'data-original',navbar: false,toolbar:true,keyboard:false,movable:false,scalable:false,fullscreen:false});
	setTimeout("$('.viewer-play').css(\"display\",\"none\");$('.viewer-prev').css(\"display\",\"none\");$('.viewer-next').css(\"display\",\"none\");",100);
	});
});
</script>
</body>
</html>