<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
	<title>微商列表</title>
	<meta http-equiv="content-type" content="text/html" charset="utf-8">
	<meta http-equiv="content-type" content="text/html" charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/main.css?v=1.3">
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
</head>
<body>
<!-- wrap是最外面大的白色背景div -->
<div class="wrap">
	<!-- 全部微商模块 -->
    <div class="productmodular businesslist-bgcolor">
        <!-- 模块的标题 -->
    	<div class="productmodular-top">
    		<div class="producttitle" style="padding-left: 20px;">
    			全部微商
    		</div>
            
    	</div>

        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="userdetail">
            <div class="userdata">
                <img src="/wldApp/<?php echo ($vo["c_headimg"]); ?>" alt="个人图像">

            </div><!-- userdata结束 -->
            <div class="userinfo">

                <div class="of">
                    <div class="username">
                    <?php if ($vo['c_isstore'] == 1) { ?>                    
                    <?php echo ($vo["c_store_name"]); ?>
                    <?php } else { ?>
                    <?php echo ($vo["c_name"]); ?>
                    <?php } ?> 
                    </div>
                    <div class="changepass ">
                    <!-- <a href="">修改密码</a> -->
                    </div>
                </div>
                <?php if ($vo['c_isstore'] == 1) { ?>                     
                <div class="dentity companydentity-bg">
                    店铺
                </div>
                <?php } else { ?>
                <div class="dentity userdentity">
                    个人
                </div>
                <?php } ?>

                <div class="name-phone">
                    <ul>
                        <li>帐号：
                            <span><?php echo ($vo["c_username"]); ?></span>
                        </li>
                        <li>
                            实体店：
                            <span><?php if ($vo['c_isstore'] == 1) { ?>
                                有
                            <?php } else { ?>
                                无
                            <?php } ?></span>
                        </li>
                        <li>电话：
                            <span><?php echo ($vo["c_phone"]); ?></span>
                        </li>
                        <li>行业：
                            <span><?php echo ($vo["c_store_type"]); ?></span>
                        </li>
                    </ul>
                </div><!-- name-phone结束 -->
            </div><!-- userinfo结束 -->

            <div class="userinfocheck">
                <a href="/wldApp/agent.php/Home/Check/detail?Id=<?php echo ($vo["c_id"]); ?>">
                    <div class="userdatalist userdatacheck-bg">
                        查看
                    </div>
                </a>
                <a href="javascript:;">
                    <?php if ($vo['c_checked'] == 1): ?>
                        <div class="userdatalist userdatastop-bg">
                            &nbsp;&nbsp;已激活
                        </div>
                    <?php else: ?>
                        <div class="userdatalist userdatacheck-bg" onclick="checkshop(this,'<?php echo ($vo["c_id"]); ?>',1);">
                            &nbsp;&nbsp;激活
                        </div>
                    <?php endif ?>
                   
                </a>
            </div><!-- userinfocheck结束 -->


    	</div><!-- userdetail结束 --><?php endforeach; endif; else: echo "" ;endif; ?>
        
    </div><!-- productmodular结束 -->

</div><!-- wrap结束 -->
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript">
    function checkshop(tg,id,cd) {
        $.post('/wldApp/agent.php/Home/Check/disable',{Id:id,Cd:cd}, function(obj) {
            var result = eval(obj);     
            if (result['code'] != 0) {
                layer.msg(result['msg'],{icon:10,time:2000});                   
            } else {
                layer.msg(result['msg'],{icon:1,time:2000});
                window.location.href = '';
            }        
        });
    }
</script>
</body>
</html>