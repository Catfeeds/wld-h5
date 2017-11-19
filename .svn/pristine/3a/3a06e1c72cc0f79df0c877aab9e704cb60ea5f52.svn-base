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
	<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data" onsubmit="return validateForm();">
	<div class="content">
		<!-- head是微商申请的头部 -->
		<div class="head">
			<div class="head-left">
				微商申请
			</div>
		</div>
        <!-- 下面的图片是到了第几步的图片 -->
		<img src="/wldApp/Agent/Agent/View/Public/images/index/step1.png" class="step">
         
        <!-- 申请人信息填写部分 -->
		<div class="storeCondition">
			<div class="sc-top">
				申请人
			</div>
			<div class="sc-bottom">
				<div class="text-box">
					<div class="text-font">姓名:</div>
					<div class="text-data">
						<input type="text" name="name" id="txt_username" >
					</div>
				</div>

				<div class="text-box">
					<div class="text-font">邮箱:</div>
					<div class="text-data">
						<input type="text" name="email"  id="txt_useremail" >
					</div>
				</div>

				<div class="text-box">
					<div class="text-font">QQ:</div>
					<div class="text-data">
						<input type="text" name="qq" id="txt_userQQ" >	
					</div>
				</div>

				<div class="text-box">
					<div class="text-font">手机号码:</div>
					<div class="text-data">
						<input type="text" name="phone" id="txt_userphone"  placeholder="此手机号码可作为登录帐号">	
					</div>
				</div>
				
			</div>
		</div>

        <!-- 身份证上传与店铺信息填写部分 -->
        <div class="storedisplay">
        	<div class="head">
				<div class="head-left" style="width: 25%">
					上传身份证(清晰正、反面)
				</div>
		    </div>

            <!-- 没有照片的时候的空图片 -->
		    <div class="productmodular-bottom">
	            <div class="productmodular-img" onclick="dianji(this,'img');"  id="imgbox">
	                <img src="/wldApp/Agent/Agent/View/Public/images/index/nullimg.png">                
	            </div> 
	    	</div>


            <!-- 是否有实体店 -->
		    <div class="havetruestore text-box1">
                	<span>实体店</span>
					<input type="radio" name="isstore" value="1" onclick="showstore(1);"> 有
					<input type="radio" name="isstore" checked="checked" value="0" onclick="showstore(0);"> 无
            </div>

            <!-- 店铺具体信息 -->
            <div id="showstore">            	
            <div class="text-box text-box1">
					<div class="text-font">店铺名称:</div>
					<div class="text-data">
						<input type="text" name="store_name" id="txt_storename" >	
					</div>
		    </div>

		    <div class="text-box text-box1">
					<div class="text-font">店铺地址:</div>
					<div class="text-data">
						<input type="text" name="store_address"  id="txt_storeaddress" >	
					</div>
		    </div>

		    <div class="text-box text-box1">
					<div class="text-font">店铺电话:</div>
					<div class="text-data">
						<input type="text" name="store_phone" id="txt_storephone">	
					</div>
		    </div>   
            </div>
            <div class="text-box2">
				<div class="text-font">经营类别:</div>
				<div class="text-data">
					<input type="text" class="fl" name="store_type" style="width: 326px;" id="txt_storekind" placeholder="请点击选择经营类别" readonly="readonly">	
					<img src="/wldApp/Agent/Agent/View/Public/images/index/bondown.png" class="fl select_img" alt="">
				</div>
				<ul>
					<li class="fl li-style">服装鞋帽</li>
					<li class="fl li-style">生活用品</li>
					<li class="fl li-style">食品</li>
					<li class="fl li-style">保健品</li>
					<li class="fl li-style">数码产品</li>
					<li class="fl li-style">工业产品</li>
					<li class="fl li-style">其他</li>
				</ul>
		    </div>
        </div>
        
        <!-- 提交按钮 -->
        <div class="submit">
        	<input type="hidden" name="Id" value="<?php echo ($Id); ?>">
        	<input type="submit" name="submit" id="submit" value="提交" class="completed">
        </div>
        
	</div>
	</form>
</div>
	<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/layer/1.9.3/layer.js"></script>
    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/imgshow.js"></script>
    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/brows.js"></script>
</body>
<script type="text/javascript">
var lictrl = 0;
$(document).ready(function() {
	$("ul").hide();
	$(".select_img").bind("click", function() {
		if (lictrl == 0) {
			lictrl = 1;
			$('.select_img').attr('src','/wldApp/Agent/Agent/View/Public/images/index/bonup.png');
			$("ul").fadeIn(800);
		} else {
			lictrl = 0;
			$('.select_img').attr('src','/wldApp/Agent/Agent/View/Public/images/index/bondown.png');			
			$("ul").fadeOut(800);
		}
		
	});

	$("ul li").hover(function() {
		$(this).addClass("li-selected").siblings().removeClass("li-selected");
	}).bind("mouseup", function() {
		$("ul").fadeOut(1);
		var txt = $(this).html();
		$('#txt_storekind').val(txt);
		lictrl = 0;
		$('.select_img').attr('src','/wldApp/Agent/Agent/View/Public/images/index/bondown.png');
	});

});
    /*上传图片*/
    function dianji(tg,sg) {
        var html = '';
        var n = $(tg).parent().find('.productmodular-img').size();

        html += '<div style="display:none;" class="productmodular-img" id="' + sg + 'modular' + n + '" onmousemove="moveing(this);" onmouseout="outimg(this);">';
        html += '<div class="productmodular-position" onclick="delimg(this);">';
        html += '<img src="/wldApp/Agent/Agent/View/Public/images/index/delete.png" alt=""> ';                   
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
			layer.msg('请填写姓名',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="email"]').val() == '') {
			layer.msg('请填写邮箱',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="qq"]').val() == '') {
			layer.msg('请填写qq号',{icon:10,time:2000});   
			return false;
		}
		if ($('input[name="phone"]').val() == '') {
			layer.msg('请填写手机号码',{icon:10,time:2000});   
			return false;
		}
		if (!$('input[name="isstore"]:checked').val()) {
			layer.msg('请选择是否有实体店',{icon:10,time:2000});   
			return false;
		} else if ($('input[name="isstore"]:checked').val() == 1) {
			if ($('input[name="store_name"]').val() == '') {
				layer.msg('请填写店铺名称',{icon:10,time:2000});   
				return false;
			}
			if ($('input[name="store_address"]').val() == '') {
				layer.msg('请填写店铺地址',{icon:10,time:2000});   
				return false;
			}
			if ($('input[name="store_phone"]').val() == '') {
				layer.msg('请填写店铺电话',{icon:10,time:2000});   
				return false;
			}			
		}	
		if ($('input[name="store_type"]').val() == '') {
			layer.msg('请填写经营类别',{icon:10,time:2000});   
			return false;
		}	
		return true;
	}
	showstore(0);
	function showstore(sg) {
		if (sg == 1) {
			$('#showstore').show();
		} else {
			$('#showstore').hide();
		}
	}
</script>
</html>