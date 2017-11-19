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
	<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data"  onsubmit="return validateForm();">		
	<div class="business">		
		<div class="business_top">
			<div class="fl business_title">激活串码</div>
			<div class="fl business_sign">>企业代理申请</div>
			<div class="fl business_sign">>企业</div>
		</div>
		<div class="business_mid">
			<img src="/wldApp/Agent/Home/View/Public/images/index/step2.png" alt="">
		</div>
		<div class="business_tit">
			法定代表人
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

		<div class="business_tit" style="margin-top: 20px;">
			代理授权负责人
		</div>
		<div class="business_content"  style="margin-top: 3%;">
			<div class="fl inpu_info">
				<div class="fl inpu_name">姓名：</div>
				<input type="text"  class="fl inpu_text" name="person_name" value="" placeholder="填写负责人姓名">
			</div>
			<label class="fl"></label>
		</div>	
		<div class="business_content">
			<div class="fl inpu_info inpu_length">
				<div class="fl inpu_name inpu_lgname">邮政编码：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="person_postcode" value="" placeholder="填写邮政编码">
			</div>
			<div class="fl inpu_info inpu_length" style="margin-left: 2.5%;">
				<div class="fl inpu_name inpu_lgname">移动电话：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="person_phone" value="" placeholder="填写移动电话">
			</div>
			<label class="fl">*此电话可作为登录帐号</label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info inpu_length">
				<div class="fl inpu_name inpu_lgname">固定电话：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="person_tel" value="" placeholder="填写固定电话">
			</div>
			<div class="fl inpu_info inpu_length" style="margin-left: 2.5%;">
				<div class="fl inpu_name inpu_lgname">传真：</div>
				<input type="text"  class="fl inpu_text inpu_lgtxet" name="person_fax" value="" placeholder="填写传真或者QQ号">
			</div>
			<label class="fl"></label>
		</div>

		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">邮箱：</div>
				<input type="text"  class="fl inpu_text" name="person_email" value="" placeholder="填写邮箱">
			</div>
			<label class="fl"></label>
		</div>
		<div class="business_content">
			<div class="fl inpu_info">
				<div class="fl inpu_name">微信号：</div>
				<input type="text"  class="fl inpu_text" name="person_wx" value="" placeholder="填写微信号">
			</div>
			<label class="fl"></label>
		</div>
		
		<div class="business_top" style="margin-top: 3%;">
			<div class="fl business_title">上传身份证</div>
			<div class="fl business_sign">（清晰正反面）</div>			
		</div>
		<div class="productmodular-bottom">
            <div class="productmodular-img" onclick="dianji(this,'pic');"  id="picbox">
                <img src="/wldApp/Agent/Home/View/Public/images/index/nullimg.png">                
            </div> 
    	</div>

		<div class="business_top" style="margin-top: 3%;">
			<div class="fl business_title">上传授权书</div>		
		</div>
		<div class="productmodular-bottom">
            <div class="productmodular-img" onclick="dianji(this,'lce');"  id="lcebox">
                <img src="/wldApp/Agent/Home/View/Public/images/index/nullimg.png">                
            </div> 
    	</div>	

		<div class="business_desc">
			注：1、请如实填写申请表，并上传清晰图片，填写后提交。<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、您填写的内容和提供的资料，我方将绝对保密。
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
		if ($('input[name="person_name"]').val() == '') {
			layer.msg('请填写负责人姓名',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="person_postcode"]').val() == '') {
			layer.msg('请填写邮政编码',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="person_phone"]').val() == '') {
			layer.msg('请填写负责人移动电话',{icon:10,time:2000});   
			return false;
		}		
		if ($('input[name="person_tel"]').val() == '') {
			layer.msg('请填写负责人固定电话',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="person_fax"]').val() == '') {
			layer.msg('请填写负责人传真或者QQ号',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="person_email"]').val() == '') {
			layer.msg('请填写负责人邮箱',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="person_wx"]').val() == '') {
			layer.msg('请填写负责人微信号',{icon:10,time:2000});   
			return false;
		}
		
		return true;
	}
</script>
</html>