<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>个人代理申请</title>
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/style.css?v=1.7">
	<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
</head>
<body>
	<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data" onsubmit="return validateForm();">
	<div class="business">
		<div class="business_top">
			<div class="fl business_title">激活串码</div>
			<div class="fl business_sign">>个人代理申请</div>
			<div class="fl business_sign">>个人</div>
		</div>
		<div class="business_mid">
			<img src="/wldApp/Agent/Home/View/Public/images/index/step1.png" alt="">
		</div>
		<div class="business_tit">
			申请人
		</div>
		<div class="business_content" style="margin-top: 2%;">
			<div class="fl inpu_info">
				<div class="fl inpu_name">申请人姓名：</div>
				<input type="text"  class="fl inpu_text" name="name" value="<?php echo ($vo["c_name"]); ?>" placeholder="填写申请人姓名">
			</div>
			<label class="fl"></label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info inpu_length">
				<div class="fl inpu_name inpu_lgname">邮政编码：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="postcode" value="<?php echo ($vo["c_postcode"]); ?>" placeholder="填写邮政编码">
			</div>
			<div class="fl inpu_info inpu_length" style="margin-left: 2.5%;">
				<div class="fl inpu_name inpu_lgname">QQ：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="qq" value="<?php echo ($vo["c_qq"]); ?>" placeholder="填写QQ">
			</div>
			<label class="fl"></label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">固定电话：</div>
				<input type="text"  class="fl inpu_text" name="tel" value="<?php echo ($vo["c_phone"]); ?>" placeholder="填写固定电话">
			</div>
			<label class="fl"></label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">移动电话：</div>
				<input type="text"  class="fl inpu_text" name="phone" value="<?php echo ($vo["c_tel"]); ?>" placeholder="填写移动电话">
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
				<div class="fl inpu_name">地址：</div>
				<input type="text"  class="fl inpu_text" name="addresss" value="<?php echo ($vo["c_addresss"]); ?>" placeholder="填写地址">
			</div>	
			<label class="fl"></label>		
		</div>
		<div class="business_top" style="margin-top: 3%;">
			<div class="fl business_title">上传身份证</div>
			<div class="fl business_sign">（清晰正反面）</div>			
		</div>
	    <div class="productmodular-bottom">
            <div class="productmodular-img" onclick="dianji(this,'img');"  id="imgbox">
                <img src="/wldApp/Agent/Home/View/Public/images/index/nullimg.png">                
            </div> 
    	</div>		
    	<input type="hidden" name="code" value="<?php echo ($code); ?>">	
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
    function dianji(tg,sg) {
        var html = '';
        var n = $(tg).parent().find('.productmodular-img').size();

        html += '<div style="display:none;" class="productmodular-img" id="' + sg + 'modular' + n + '" onmousemove="moveing(this);" onmouseout="outimg(this);">';
        html += '<div class="productmodular-position" onclick="delimg(this);">';
        html += '<img src="/wldApp/Agent/Home/View/Public/images/index/delete.png" alt=""> ';                   
        html += '</div>';
        html += '<img src="" id="' + sg + 'path' + n + '">';
        html += '<input style="display:none;" type="file" name="' + sg + n +'" value="" id="' + sg + 'file1' + n + '">';
        html += '</div>';

        $('#' + sg + 'box').before(html);
        $("#"+sg + 'file1' + n).uploadPreview({
            Img: sg + 'path' + n,
            Width: 188,
            Height: 149
        });

       
       var gg = document.getElementById(sg + 'file1' + n);
       gg.click();
       $('#'+sg + 'modular' + n).show();
     
    }

    // 鼠标移上图片
    function moveing(tg) {
        $(tg).find('.productmodular-position').show();
    }

    // 鼠标移开图片
    function outimg(tg) {
        $(tg).find('.productmodular-position').hide();
    }

    // 删除图片
    function delimg(tg) {
        $(tg).parent().remove();
    }
 	 // 表单提交验证
	function validateForm() {
		if ($('input[name="name"]').val() == '') {
			layer.msg('请填写申请人姓名',{icon:10,time:2000});   
			return false;
		}		
		if ($('input[name="postcode"]').val() == '') {
			layer.msg('请填写邮政编码',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="qq"]').val() == '') {
			layer.msg('请填写QQ',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="tel"]').val() == '') {
			layer.msg('请填写固定电话',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="phone"]').val() == '') {
			layer.msg('请填写移动电话',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="email"]').val() == '') {
			layer.msg('请填写邮箱',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="addresss"]').val() == '') {
			layer.msg('请填写地址',{icon:10,time:2000});   
			return false;
		}		
		return true;
	}
</script>
</html>