<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
	<title>商品详情上传</title>
	<meta http-equiv="content-type" content="text/html" charset="utf-8">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/imgupload.css?v=1.3">
	<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/agent.js"></script>
</head>
<body>
<!-- wrap是最外面大的白色背景div -->
<div class="wrap">
	<!-- 商品主图上传模块 -->
    <div class="productmodular">
        <!-- 模块的标题 -->
    	<div class="productmodular-top">
    		<div class="producttitle">
    			商品主图
    		</div>
    	</div>

        <!-- 商品主图上传 -->
    	<div class="productmodular-bottom">
            <?php foreach ($vo['mainimg'] as $key => $value): ?>
            <?php if ($value['c_sign'] == 1): ?>
                 <div class="productmodular-img">
                    <img src="/wldApp/<?php echo $value['c_imgpath'] ?>">                
                </div>                
            <?php endif ?>               
            <?php endforeach ?>                   
    	</div>
    </div>

	<!-- 商品主图上传模块 -->
    <div class="productmodular">
        <!-- 模块的标题 -->
    	<div class="productmodular-top">
    		<div class="producttitle">
    			商品详情图
    		</div>
    	</div>
        <!-- 商品详情图上传 -->
    	<div class="productmodular-bottom">
            <?php foreach ($vo['mainimg'] as $key => $value): ?>
            <?php if ($value['c_sign'] == 2): ?>
                 <div class="productmodular-img">
                    <img src="/wldApp/<?php echo $value['c_imgpath'] ?>">                
                </div>                
            <?php endif ?>               
            <?php endforeach ?>  
    	</div>
    </div>
	<!-- 资料证书上传模块 -->
    <div class="productmodular">
        <!-- 模块的标题 -->
    	<div class="productmodular-top">
    		<div class="producttitle">
    			资料证书
    		</div>
    	</div>
        <!-- 资料证书上传 -->
    	<div class="productmodular-bottom">
            <?php foreach ($vo['mainimg'] as $key => $value): ?>
            <?php if ($value['c_sign'] == 3): ?>
                 <div class="productmodular-img">
                    <img src="/wldApp/<?php echo $value['c_imgpath'] ?>">                
                </div>                
            <?php endif ?>               
            <?php endforeach ?> 
    	</div>
    </div>
	<!-- 商品说明模块 -->
    <div class="productmodular">
        <!-- 模块的标题 -->
    	<div class="productmodular-top">
    		<div class="producttitle">
    			商品说明
    		</div>
    	</div>
        <!-- 商品说明内容 -->
    	<div class="productmodular-bottom">
    		<!-- 商品名称填写 -->
    		<div class="text-box">
					<div class="text-font">商品名称:</div>
					<div class="text-data">
						<input type="text" name="name" readonly="readonly" id="txt_productname"  value="<?php echo ($vo["c_name"]); ?>">	
					</div>
		    </div>
            <!-- 商品特点填写 -->
		    <div>
		    <div class="productfeature">
                <?php echo ($vo["c_desc"]); ?>
            </div>
		    </div>
            
    	</div>
    </div>
	<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/layer/1.9.3/layer.js"></script>
   
</div>
</body>
</html>