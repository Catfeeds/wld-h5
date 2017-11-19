<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link rel="shortcut icon" href="/wldApp/Agent/Agent/View/Public/images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no, email=no" />
<title>我的资料</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/mobile/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/mobile/index.css?v=1.2">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/common.js?v=2017325"></script>
<script type="text/javascript">var WEB_HOST = "<?php echo WEB_HOST ?>";</script>
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/ajaxfileupload.js"></script>
<script type="text/javascript">
	window.onload=function(){

		topstyle();/*头部样式*/

		$('#return-top').hide();
		$('#page-top-c-t').text("我的资料");

		var reh = $('.resource-list').height();
		$('.resource-list').css('line-height',reh+'px');

		re_type();

		var zw = $('.zjz-list').width();
		$('.zjz-list').width(zw);
		$('.zjz-list').height(zw);
		$('.zjz-list input[type="file"]').width(zw);
		$('.zjz-list input[type="file"]').height(zw);

	}

	/*选择资料类型*/
	function re_type () {
		// var selectedvalue = "<?php echo $vo['c_type'] ?>";
		// if (!selectedvalue) {
		// 	selectedvalue = $("input:radio[name='type']:checked").val();
		// }

		var selectedvalue = $("input:radio[name='type']:checked").val();
		if(selectedvalue==2){
			$(".qy").show();
			$(".gr").hide();
		}else{
			$(".qy").hide();
			$(".gr").show();
		}
	}
</script>

</head>

<body>


  <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/fastclick.js"></script>
  <div class="page-top">
    <div class="page-top-l fl">
     <a href="javascript:history.go(-1)" id="return-top"> <img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-jt.png" alt=""></a>
    </div>
    <div class="page-top-c fl" id="page-top-c-t">
      公告
    </div>
    <div class="page-top-r fr">
      <a href="javascript:;" id="icolink">
        <img src="/wldApp/Agent/Agent/View/Public/images/mobile/menu-ico-l.png" alt="">
      </a>
      <a href="javascript:;" id="icohover" style="display:none;">
        <img src="/wldApp/Agent/Agent/View/Public/images/mobile/menu-ico-h.png" alt="">
      </a>
    </div>
  </div>

  <div class="m-menu-bigbg" style="background:rgba(0,0,0,.5);width: 100%;height:100%;position: absolute;z-index: 99;left:0;top:0;display:none;"></div>
  <div class="page-menu-bg" style="display:none;">
    <div class="page-menu-logo">
      <a href="/wldApp/agent.php/Agent/Information"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-logo.png" alt=""></a>
    </div>
    <div class="page-m-loginname">
      欢迎您&nbsp;&nbsp;<?php echo (session('_AGENT_NAME')); ?>
    </div>
    <ul class="page-menu-list">
      <li>
        <a href="/wldApp/agent.php/Agent/Information">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-1.png" alt=""></div>
        <div class="fl p-m-text">公告</div>
        <div class="fl p-m-num" id="notice">0</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Shopcheck">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-3.png" alt=""></div>
        <div class="fl p-m-text">商家审核</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Codecheck">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/code_icon.png" alt=""></div>
        <div class="fl p-m-text">激活串码审核</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Download">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-4.png" alt=""></div>
        <div class="fl p-m-text">资料下载</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Personal/shopinfo">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-5.png" alt=""></div>
        <div class="fl p-m-text">上级代理</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Personal">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-5.png" alt=""></div>
        <div class="fl p-m-text">个人中心</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Login/logout" class="a-loginout">
          <img src="/wldApp/Agent/Agent/View/Public/images/mobile/loginout.png" alt="">
        </a>
      </li>
    </ul>

  </div>
<script type="text/javascript">

function topstyle () {
  // var pth = $('.page-top').height();
  // $('.page-top-c').css('line-height', pth + 'px');
  var topr = $('.page-top-r').width();
  $('.page-top-r').height(topr);
  var topl = $('.page-top-l').width();
  $('.page-top-l').height(topr);
}

function menustyle() {

	var pth = $('.page-top').height();
	$('.page-menu-bg').css('top', pth + 'px');

	var dh = $(document).height();
	$('.page-menu-bg').css('height', dh + 'px');

	// var icoh = $('.page-menu-list li').height();
	// $('.p-m-text').css('line-height', icoh + 'px');
	var numh = $('.p-m-num').height();
	$('.p-m-num').css('width', numh + 'px');
	$('.p-m-num').css('line-height', numh + 'px');
	$('.p-m-num').css('border-radius', numh + 'px');
}
$(function() {
  FastClick.attach(document.body);
  $('#icolink').click(function () {
    showmenu(1);
  });
  $('#icohover').click(function() {
    showmenu(2);
  });
});
function showmenu(v) {
	var v = v;
	if (v == 1) {
		$('.page-menu-bg').fadeIn(500);
		$('#icolink').hide();
		$('#icohover').show();
    $('.m-menu-bigbg').css("display","block");
    $('.m-menu-bigbg').height($(document).height());
    menustyle();
	} else {
		$('.page-menu-bg').fadeOut(300);
		$('#icolink').show();
		$('#icohover').hide();
    $('.m-menu-bigbg').css("display","none");
	}
}

$('.m-menu-bigbg').click(function () {
    $('.page-menu-bg').fadeOut(300);
    $('#icolink').show();
    $('#icohover').hide();
    $('.m-menu-bigbg').css("display","none");
});

getnum();

/*显示数字*/
function getnum(){
  var keyUrl = '/wldApp/agent.php/Agent/Information/GetStatuMessage';
  $.ajax({url:keyUrl,dataType:"json",async:false,
      success:function(data){
        var msg = eval(data);
        if (msg['code']==0) {
          $('#notice').text(msg['data']['publicnum']);
          $('#checka').text(msg['data']['checknum']);
        }else{
          alert(msg['msg']);
        }
      }
  });
}
</script>


<div class="wrap-page bgcolor">
	<form action="/wldApp/agent.php/Agent/Personal/SaveAgentInfo" method="post" accept-charset="utf-8" enctype="multipart/form-data" id="form1">

		<div class="personal-tab">
			<a href="/wldApp/agent.php/Agent/Personal/index" class="personal-t-a tab-hover fl">我的资料</a>
			<a href="/wldApp/agent.php/Agent/Personal/updatepwd" class="personal-t-a fr">修改密码</a>
		</div>
		<div class="shop-check" style="margin-top:5%;">
			<div class="check-pass fl"><input type="radio" <?php if ($vo['c_checked'] == 2 || $vo['c_checked'] == 3) { ?>disabled="disabled"<?php } ?> name="type" value="2" <?php if ($vo['c_type'] == 2) { ?>checked="checked" <?php } ?> onclick="re_type()">&nbsp;企业资料</div>
			<div class="check-pass fl"><input type="radio" <?php if ($vo['c_checked'] == 2 || $vo['c_checked'] == 3) { ?>disabled="disabled"<?php } ?> name="type" value="1" <?php if ($vo['c_type'] == 1) { ?>checked="checked" <?php } ?> onclick="re_type()">&nbsp;个人资料</div>
		</div>
		<div class="resource-main">
			<div class="qy">
				<div class="resource-list">
					<div class="resource-l fl"><span>*&nbsp;</span>申请单位名称：</div>
					<div class="resource-r fl"><input type="text" id="companey-name" value="<?php echo ($vo["c_company"]); ?>" name="company" class="resource-text"></div>
				</div>
				<div class="resource-list">
					<div class="resource-l fl"><span>*&nbsp;</span>申请单位地址：</div>
					<div class="resource-r fl"><input type="text" id="address-name" value="<?php echo ($vo["c_address"]); ?>" name="address" class="resource-text"></div>
				</div>
				<div class="resource-list">
					<div class="resource-l fl"><span>*&nbsp;</span>联系人：</div>
					<div class="resource-r fl"><input type="text" id="contact-name" value="<?php echo ($vo["c_name"]); ?>" name="name" class="resource-text"></div>
				</div>
			</div>
			<div class="gr">
				<div class="resource-list">
					<div class="resource-l fl"><span>*&nbsp;</span>名称：</div>
					<div class="resource-r fl"><input type="text" id="user-name" value="<?php echo ($vo["c_name"]); ?>" name="name1" class="resource-text" placeholder="请输入真实姓名"></div>
				</div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>移动电话：</div>
				<div class="resource-r fl"><input type="text" id="phone-name" value="<?php echo ($vo["c_phone"]); ?>" name="phone" class="resource-text"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>邮箱：</div>
				<div class="resource-r fl"><input type="text" id="email-name" value="<?php echo ($vo["c_email"]); ?>" name="email" class="resource-text"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>QQ：</div>
				<div class="resource-r fl"><input type="text" id="qq-name" value="<?php echo ($vo["c_qq"]); ?>" name="qq" class="resource-text"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl">固定电话：</div>
				<div class="resource-r fl"><input type="text" id="telephone-name" value="<?php echo ($vo["c_home_tel"]); ?>" name="home_tel" class="resource-text"></div>
			</div>

			<div class="qy">
				<div class="resource-list">
					<div class="resource-l fl"><span>*&nbsp;</span>邮政编码：</div>
					<div class="resource-r fl"><input type="text" id="post-name" value="<?php echo ($vo["c_postcode"]); ?>" name="postcode" class="resource-text"></div>
				</div>
				<div class="resource-list">
					<div class="resource-l fl"><span>*&nbsp;</span>营业执照：</div>
					<div class="resource-r fl"><input type="text" id="license-name" value="<?php echo ($vo["c_charter"]); ?>" name="charter" class="resource-text"></div>
				</div>
			</div>

			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>身份证号码：</div>
				<div class="resource-r fl"><input type="text" id="identity-name" value="<?php echo ($vo["c_idcard"]); ?>" name="idcard" class="resource-text"></div>
			</div>

			<div class="page-title">
				<div class="page-tit-name">上传身份证件</div>
			</div>
			<ul class="ul-certificate">
				<li>
					<div class="zjz-list">
						<img src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt="">
						<?php if($vo['c_idcard_img']!=null){ ?>
						<div class="pic-front"><img id="imgr1" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_idcard_img"]); ?>" alt=""></div>
						<?php }else{ ?>
						<div class="pic-front"><img id="imgr1" src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt=""></div>
						<?php } ?>
						<input type="hidden" name="idcard_img" value="<?php echo ($vo["c_idcard_img"]); ?>" id="saveimg_1">
						<input type="file" onclick="clickimg(1);" name="idcard_img" value="" id="file1" placeholder="" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] != 1){ ?> style="display:none;" <?php } ?> >
					</div>
					<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?> onclick="deleteimg(1)" id="delimg_1"><img src="/wldApp/Agent/Agent/View/Public/images/fc-16.png" alt=""></div>
				</li>
				<li>
					<div class="zjz-list">
						<img src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt="">

						<?php if($vo['c_idcard_img1']!=null){ ?>
						<div class="pic-front"><img id="imgr2" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_idcard_img1"]); ?>" alt=""></div>
						<?php }else{ ?>
						<div class="pic-front"><img id="imgr2" src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt=""></div>
						<?php } ?>
						<input type="hidden" name="idcard_img1" value="<?php echo ($vo["c_idcard_img1"]); ?>" id="saveimg_2">
						<input type="file" onclick="clickimg(2);" name="idcard_img1" value="" id="file2" placeholder="" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] != 1){ ?> style="display:none;" <?php } ?> >
					</div>
					<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?> onclick="deleteimg(2)" id="delimg_2"><img src="/wldApp/Agent/Agent/View/Public/images/fc-16.png" alt=""></div>
				</li>
			</ul>
			<div class="qy">
				<div class="page-title">
					<div class="page-tit-name">上传营业执照</div>
				</div>
				<ul class="ul-certificate">
					<li>
						<div class="zjz-list" >
							<img src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt="">
							<?php if($vo['c_charter_img']!=null){ ?>
							<div class="pic-front"><img id="imgr3" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_charter_img"]); ?>" alt=""></div>
							<?php }else{ ?>
							<div class="pic-front"><img id="imgr3" src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt=""></div>
							<?php } ?>
							<input type="hidden" name="charter_img" value="<?php echo ($vo["c_charter_img"]); ?>" id="saveimg_3">
							<input type="file" onclick="clickimg(3);" name="charter_img" value="" id="file3" placeholder="" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] != 1){ ?> style="display:none;" <?php } ?> >
						</div>
						<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?> onclick="deleteimg(3)" id="delimg_3"><img src="/wldApp/Agent/Agent/View/Public/images/fc-16.png" alt=""></div>
					</li>
				</ul>
				<div class="page-title">
					<div class="page-tit-name">上传企业标志</div>
				</div>
				<ul class="ul-certificate">
					<li>
						<div class="zjz-list" >
							<img src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt="">
							<?php if($vo['c_company_sign']!=null){ ?>
							<div class="pic-front"><img id="imgr4" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_company_sign"]); ?>" alt=""></div>
							<?php }else{ ?>
							<div class="pic-front"><img id="imgr4" src="/wldApp/Agent/Agent/View/Public/images/add.jpg" alt=""></div>
							<?php } ?>
							<input type="hidden" name="company_sign" value="<?php echo ($vo["c_company_sign"]); ?>" id="saveimg_4">
							<input type="file" onclick="clickimg(4);" name="company_sign" value="" id="file4" placeholder="" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] != 1){ ?> style="display:none;" <?php } ?> >
						</div>
						<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?> onclick="deleteimg(4)" id="delimg_4"><img src="/wldApp/Agent/Agent/View/Public/images/fc-16.png" alt=""></div>
					</li>
				</ul>
			</div>
			<?php if (!empty($vo['c_dcode']) && $vo['c_checked'] != 1) { ?>
			<div class="resource-sub sub-gray">提交</div>
			<?php } else { ?>
			<div class="resource-sub sub-blue" onclick="validateForm();">提交</div>
			<?php } ?>
		</div>
	</form>
</div>
<div class="mar30"></div>
<script type="text/javascript">
	//点击图片
	// function clickimg(id) {
	// 	$('#file' + id).uploadPreview({
	// 		Img: 'imr' + id,
	// 		Width: 188,
	// 		Height: 188
	// 	});
	// 	var gg = document.getElementById('file' + id);
	// 	gg.click();
	// 	$('#saveimg_' + id).val("");
	// }
	function clickimg(id) {
		document.getElementById('file' + id).onchange = function() {
			tempUploadimg('file' + id, 'imgr' + id, 'saveimg_' + id);
		}
		// var filestr = document.getElementById('file' + id);
		// filestr.click();
		$('#delimg_' + id).css("display", "block");
		//alert("外面的点击");
	}
	/*清空图片*/
	function deleteimg (id) {
		delUploadimg($('#saveimg_'+id).val());
		$('#saveimg_'+id).val("");
		$('#file'+id).val("");
		$('#imgr'+id).attr("src","/wldApp/Agent/Agent/View/Public/images/add.jpg");
		$('#delimg_'+id).css("display","none");
	}
	// 验证上传图片个数
	function checkimg(type, total) {
		var imgr = "/wldApp/Agent/Agent/View/Public/images/add.jpg";
		for (var i = 1; i <= total; i++) {
			var saveimg = $('#saveimg_' + i).val();
			beforei = $('#imgr' + i).attr("src");
			if (beforei == imgr || saveimg == "") {
				alert('请完善相关证件的图片！');
				return false;
				break;
			}
		};
		return true;
	}
	// 表单提交验证
	function validateForm() {
		var selectedvalue = $("input:radio[name='type']:checked").val();
		if(selectedvalue==2){
			if ($('input[name="company"]').val() == '') {
				alert('请输入申请单位名称');
				$('input[name="company"]').focus();
				return false;
			}
			if ($('input[name="address"]').val() == '') {
				alert('请输入申请单位地址');
				$('input[name="address"]').focus();
				return false;
			}
			if ($('input[name="name"]').val() == '') {
				alert('请输入联系人');
				$('input[name="name"]').focus();
				return false;
			}
			if ($('input[name="companey-name"]').val() == '') {
				alert('请输入申请单位地址');
				$('input[name="companey-name"]').focus();
				return false;
			}
		} else {
			if ($('input[name="name1"]').val() == '') {
				alert('请输入真实名称');
				$('input[name="name1"]').focus();
				return false;
			}
		}

		var match = /^1[3|4|5|7|8][0-9]\d{8}$/;
		if (!match.exec($('input[name="phone"]').val())) {
			alert('手机号码格式错误');
			$('input[name="phone"]').focus();
            return false;
        }
		if ($('input[name="email"]').val() == '') {
			alert('请输入邮箱号');
			$('input[name="email"]').focus();
			return false;
		}else{
	        var emailreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	        if(!emailreg.test($('input[name="email"]').val())){
	        	alert('请输入正确的电子邮箱');
	        	$('input[name="email"]').focus();
				return false;
	        }
		}
		if ($('input[name="qq"]').val() == '') {
			alert('请输入QQ号');
			$('input[name="qq"]').focus();
			return false;
		}else{
			// var qqreg = /^[1-9]\d{4,8}$/;
			// if(!qqreg.test($('input[name="qq"]').val())){
			// 	alert('请输入正确的QQ号');
			// 	$('input[name="qq"]').focus();
			// 	return false;
			// }
		}
		// if ($('input[name="home_tel"]').val() == '') {
		// 	alert('请输入固定电话');
		// 	$('input[name="home_tel"]').focus();
		// 	return false;
		// }else{
		// 	var phonereg =  /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/;
		// 	if (!phonereg.test($('input[name="home_tel"]').val())) {
		// 		alert('请输入正确的固定电话');
		// 		$('input[name="home_tel"]').focus();
		// 		return false;
		// 	}
		// }
		if(selectedvalue==2) {
			if ($('input[name="postcode"]').val() == '') {
				alert('请输入邮政编码');
				$('input[name="postcode"]').focus();
				return false;
			}else{
				var postreg = /^\d{6}$/;
				if(!postreg.test($('input[name="postcode"]').val())){
					alert('请输入正确的邮政编码');
					$('input[name="postcode"]').focus();
					return false;
				}
			}
			if ($('input[name="charter"]').val() == '') {
				alert('请输入企业营业执照号');
				$('input[name="charter"]').focus();
				return false;
			}
			if (!checkimg(2, 4)) {
				return false;
			}
		}
		if ($('input[name="idcard"]').val() == '') {
			alert('请输入身份证号');
			$('input[name="idcard"]').focus();
			return false;
		}else{
			if (!checkIdcard($('input[name="idcard"]').val())) {
				alert('请输入正确的身份证号码！');
				$('input[name="idcard"]').focus();
				return false;
			}
		}
		if (!checkimg(1, 2)) {
			return false;
		}


	    $('#form1').submit();
	}

	function checkIdcard (idcard) {
		var idreg = /^[0-9a-zA-Z]*$/g;
		// var idreg = /^(\d{18,18}|\d{15,15}|\d{17,17}x)$/;
		// switch (idcard.length) {
		// 	case 10: //台湾
		// 		if (idcard.indexOf("(") > 0) {
		// 			if (isNaN(idcard.substr(0,1))) {  //香港
		// 				idreg = /^[A-Z][0-9]{6}\([0-9A]\)$/;
		// 			} else {	//澳门
		// 				idreg = /^[157][0-9]{6}\([0-9]\)$/;
		// 			}
		// 		} else {   //台湾
		// 			idreg = /^[A-Z][0-9]{9}$/;
		// 		}
		// 		break;
		// 	default:
		// 		idreg = /^(\d{18,18}|\d{15,15}|\d{17,17}x)$/;
		// 		break;
		// }
		if (!idreg.test($('input[name="idcard"]').val())) {
			return false;
		}
		return true;
	}
</script>
<div class="wrap-page bgcolor">
	<form action="/wldApp/agent.php/Agent/Personal/SaveBankInfo" method="POST" id="form2">
		<div class="page-title">
			<div class="page-tit-name">收款账号</div>
		</div>
		<div class="resource-main">
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>银行开户行：</div>
				<div class="resource-r fl">
					<select name="fee_bank" id="bank-name" class="bank-name">
						<?php if ($vo['c_fee_bank']): ?>
						<option value="<?php echo ($vo["c_fee_bank"]); ?>"><?php echo ($vo["c_fee_bank"]); ?></option>
						<?php endif ?>
						<option value="">请选择银行</option>
						<option value="中国银行">中国银行</option>
				  		<option value="中国建设银行">中国建设银行</option>
				  		<option value="中国农业银行">中国农业银行</option>
				  		<option value="中国工商银行">中国工商银行</option>
				  		<option value="中国邮政银行">中国邮政银行</option>
				  		<option value="中国交通银行">中国交通银行</option>
				  		<option value="中国招商银行">中国招商银行</option>
					</select>
				</div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>支行名称：</div>
				<div class="resource-r fl"><input type="text" id="branch-name" name="fee_branch" class="resource-text" value="<?php echo ($vo["c_fee_branch"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>银行卡号：</div>
				<div class="resource-r fl"><input type="text" id="card-name" name="fee_cardnum" class="resource-text" value="<?php echo ($vo["c_fee_cardnum"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>持卡人姓名：</div>
				<div class="resource-r fl"><input type="text" id="have-name" name="fee_name" class="resource-text" value="<?php echo ($vo["c_fee_name"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>支付宝账号：</div>
				<div class="resource-r fl"><input type="text" id="alipay-name" name="fee_alipay" class="resource-text" value="<?php echo ($vo["c_fee_alipay"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>微信账号：</div>
				<div class="resource-r fl"><input type="text" id="weixin-name" name="fee_weixin" class="resource-text" value="<?php echo ($vo["c_fee_weixin"]); ?>"></div>
			</div>
			<div class="resource-sub sub-blue" onclick="subform();">保存</div>
		</div>
	</form>
</div>
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/imgshow.js"></script>
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/brows.js"></script>
<script type="text/javascript">
	function subform() {
		if ($('#bank-name').val() == '') {
			alert('请填写银行开户行');
			return false;
		}
		if ($('#branch-name').val() == '') {
			alert('请填写支行名称');
			return false;
		}
		if ($('#card-name').val() == '') {
			alert('请填写银行卡号');
			return false;
		}
		if ($('#have-name').val() == '') {
			alert('请填写持卡人姓名');
			return false;
		}
		if ($('#alipay-name').val() == '') {
			alert('请填写支付宝帐号');
			return false;
		}
		if ($('#weixin-name').val() == '') {
			alert('请填写微信帐号');
			return false;
		}

		$('#form2').submit();
	}
</script>
</body>
</html>