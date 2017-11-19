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
		<img src="/wldApp/Agent/Agent/View/Public/images/index/step2.png" class="step">

        <!-- 店铺情况填写 -->
		<div class="storeCondition">
			<div class="sc-top">
				店铺情况
			</div>
			<div class="sc-bottom">

			<div class="text-box">
				<div class="text-font">营业面积:</div>
				<div class="text-data">
					<input type="text" name="store_aire" id="txt_area" >	
				</div>
		    </div>

		    <div class="text-box">
				<div class="text-font">店员人数:</div>
				<div class="text-data">
					<input type="text" name="store_people"  id="txt_num" >	
				</div>
		    </div>

		    <div class="text-box">
				<div class="text-font">投入资金(万):</div>
				<div class="text-data">
					<input type="text" name="store_money" id="txt_money" >
				</div>
		    </div>
				
			</div>
		</div>

        <!-- 店铺展示和店铺介绍填写 -->
        <div class="storedisplay">
        	<div class="head">
				<div class="head-left">
					店铺展示照片
				</div>
		    </div>

		    <div class="productmodular-bottom">
	            <div class="productmodular-img" onclick="dianji(this,'img');"  id="imgbox">
	                <img src="/wldApp/Agent/Agent/View/Public/images/index/nullimg.png">                
	            </div> 
	    	</div>

            <div>
		    <textarea placeholder="销售策略:" name="store_plan" id="txta_strategy" class="sales"></textarea>
		    </div>

            <div>
		    <textarea placeholder="销售渠道:" name="store_place" id="txta_saleway" class="sales"></textarea>
		    </div>

            <div>
		    <textarea placeholder="客服服务方式及流程:" name="store_service" id="txta_connect" class="sales bigerheight"></textarea>
		    </div>
        </div>

        <!-- 是否同意我们的协议 -->
        <div class="isagree">
        	<p>
        			<input type="checkbox" name="agree" id="agree" style="width: 20px;height: 20px;border: none;vertical-align: middle" checked="checked">
        		    <span>我已同意《微商入驻协议》</span>
        	</p>
        	<p>
        		* 请根据实际情况选填个体经营户或个人资料
        	</p>
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
        if ($('input[name="store_aire"]').val() == '') {
            layer.msg('请填写营业面积',{icon:10,time:2000});   
            return false;
        }
        if ($('input[name="store_people"]').val() == '') {
            layer.msg('请填写店员人数',{icon:10,time:2000});   
            return false;
        }
        if ($('input[name="store_money"]').val() == '') {
            layer.msg('请填写投入资金',{icon:10,time:2000});   
            return false;
        }
        if ($('input[name="store_plan"]').val() == '') {
            layer.msg('请填写营销策略',{icon:10,time:2000});   
            return false;
        }
        if ($('input[name="store_place"]').val() == '') {
            layer.msg('请填写销售渠道',{icon:10,time:2000});   
            return false;
        }
        if ($('input[name="store_service"]').val() == '') {
            layer.msg('请填写客服服务方式及流程',{icon:10,time:2000});   
            return false;
        }
        return true;
    }
</script>
</html>