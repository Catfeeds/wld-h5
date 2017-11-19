<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>企业代理申请</title>
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/style.css?v=1.7">
	<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
</head>
<body>
	<form action="" method="post" accept-charset="utf-8" onsubmit="return validateForm();" enctype="multipart/form-data">
	<div class="business">
		<div class="business_top">
			<div class="fl business_title">激活串码</div>
			<div class="fl business_sign">>企业代理申请</div>
			<div class="fl business_sign">>企业</div>
		</div>
		<div class="business_mid">
			<img src="/wldApp/Agent/Home/View/Public/images/index/step1.png" alt="">
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">申请单位名称：</div>
				<input type="text"  class="fl inpu_text" name="name" value="<?php echo ($vo["c_name"]); ?>" placeholder="填写申请单位名称">
			</div>
			<label class="fl"></label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">申请单位地址：</div>
				<input type="text"  class="fl inpu_text" name="addresss" value="<?php echo ($vo["c_addresss"]); ?>" placeholder="填写申请单位地址">
			</div>
			<label class="fl"></label>
		</div>

		<div class="business_content">
			<div class="fl inpu_info inpu_length">
				<div class="fl inpu_name inpu_lgname">邮政编码：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="postcode" value="<?php echo ($vo["c_postcode"]); ?>" placeholder="填写邮政编码">
			</div>
			<div class="fl inpu_info inpu_length" style="margin-left: 2.5%;">
				<div class="fl inpu_name inpu_lgname">移动电话：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="phone" value="<?php echo ($vo["c_phone"]); ?>" placeholder="填写移动电话">
			</div>
			<label class="fl"></label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info inpu_length">
				<div class="fl inpu_name inpu_lgname">固定电话：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="tel" value="<?php echo ($vo["c_tel"]); ?>" placeholder="填写固定电话">
			</div>
			<div class="fl inpu_info inpu_length" style="margin-left: 2.5%;">
				<div class="fl inpu_name inpu_lgname">QQ：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="qq" value="<?php echo ($vo["c_qq"]); ?>" placeholder="填写QQ">
			</div>
			<label class="fl"></label>
		</div>

		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">邮箱：</div>
				<input type="text"  class="fl inpu_text" name="email" value="<?php echo ($vo["c_email"]); ?>" placeholder="填写邮箱">
			</div>
			<label class="fl"></label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">申请日期：</div>
				<input type="text"  class="fl inpu_text" name="apply_time" value="<?php echo ($vo["c_apply_time"]); ?>" placeholder="填写申请日期">
			</div>
			<label class="fl">*日期格式：2016-04-22</label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">营业执照号：</div>
				<input type="text"  class="fl inpu_text" name="charter" value="<?php echo ($vo["c_charter"]); ?>" placeholder="填写营业执照号">
			</div>
			<label class="fl">*三证合一添营业执照</label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">企业代码证号：</div>
				<input type="text"  class="fl inpu_text" name="charter_code" value="<?php echo ($vo["c_charter_code"]); ?>" placeholder="填写企业代码证号">
			</div>	
			<label class="fl"></label>		
		</div>
		<div class="business_top" style="margin-top: 3%;">
			<div class="business_title">上传营业执照</div>			
		</div>
		<div class="business_img">
			<img src="/wldApp/Agent/Home/View/Public/images/index/nullimg.png" alt="" id="imgshows" onclick="dianji();">
			<img src="" alt="" id="ImgPr">
			<input type="hidden" name="code" value="<?php echo ($code); ?>">
			<input type="file"  id="file_upload" name="img" placeholder="" style="display:none;">
		</div>				
		<input type="submit" class="business_sub" value="提交">		
	</div>		
	</form>
	
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/imgshow.js"></script>
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/brows.js"></script>
</body>
<script type="text/javascript">
  /*上传图片*/
  function dianji() {
  	var wd = $('#imgshows').width();
  	var gd = $('#imgshows').height();
  	$("#file_upload").uploadPreview({
  		Img: "ImgPr",
  		Width: wd,
  		Height: gd
  	});
  	$('#pic_imgurl').val('');
  	var gg = document.getElementById("file_upload");
  	gg.click();
  	$('#imgshows').css('display', 'none');
  	$("#ImgPr").show();
  }

  // 表单提交验证
  function validateForm() {
  	if ($('input[name="name"]').val() == '') {
  		layer.msg('请填写申请单位名称',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="addresss"]').val() == '') {
  		layer.msg('请填写申请单位地址',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="postcode"]').val() == '') {
  		layer.msg('请填写邮政编码',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="phone"]').val() == '') {
  		layer.msg('请填写移动电话',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="tel"]').val() == '') {
  		layer.msg('请填写固定电话',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="qq"]').val() == '') {
  		layer.msg('请填写QQ',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="email"]').val() == '') {
  		layer.msg('请填写邮箱',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="apply_time"]').val() == '') {
  		layer.msg('请填写申请日期',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="charter"]').val() == '') {
  		layer.msg('请填写营业执照号',{icon:10,time:2000});   
  		return false;
  	}
  	if ($('input[name="charter_code"]').val() == '') {
  		layer.msg('请填写企业代码证号',{icon:10,time:2000});   
  		return false;
  	}
  	
  	return true;
  }
</script>
</html>