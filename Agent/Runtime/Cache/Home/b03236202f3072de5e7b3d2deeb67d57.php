<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
	<title>代理商列表</title>
	<meta http-equiv="content-type" content="text/html" charset="utf-8">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/imgupload.css?v=1.2">
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/agent.js"></script>
</head>
<body>
<!-- wrap是最外面大的白色背景div -->
<div class="wrap">
	<!-- 全部微商模块 -->
    <div class="productmodular businesslist-bgcolor">
        <!-- 模块的标题 -->
    	<div class="productmodular-top">
    		<div class="producttitle">
    			全部代理商
    		</div>
    	</div>

        <!-- 个人微商详细信息模块 -->
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="userdetail">
            <div class="userdata">
            	<div class="userdata_img">
                	<img src="/wldApp/<?php echo ($vo["c_headimg"]); ?>" alt="个人图像">            		
            	</div>
                <div class="userdatalist userdatacheck-bg" onclick="location.href='/wldApp/agent.php/Home/Member/detail?Id=<?php echo ($vo["c_id"]); ?>'">
                    查看
                </div>
                <?php if ($vo['c_checked'] == 1): ?>
                    <div class="userdatalist userdatastop-bg" onclick="checkshop(this,'<?php echo ($vo["c_id"]); ?>',0);">
                        &nbsp;&nbsp;停用
                    </div>
                <?php else: ?>
                    <div class="userdatalist userdatacheck-bg" onclick="checkshop(this,'<?php echo ($vo["c_id"]); ?>',1);">
                        &nbsp;&nbsp;激活
                    </div>
                <?php endif ?>                
            </div><!-- userdata结束 -->

            <div class="allinfo">
                <div class="allinfo-top">
                        <div class="username"><?php echo ($vo["c_name"]); ?></div>
                        <div class="changepass">
                            <!-- <a href="">修改密码</a> -->
                        </div>
                </div>

                <div class="allinfo-bottom">
                   <div class="userinfo">                    
                    	<?php if ($vo['c_type'] == 1): ?>
                    	<div class="dentity companydentity-bg">
                    		企业
                    	</div>
                    	<?php else: ?>
                    	<div class="dentity userdentity">
                        	个人   
                        </div>                 		
                    	<?php endif ?>
                    
                    <div class="name-phone">
                        <ul>
                            <li>帐号：
                                <span><?php echo ($vo["c_username"]); ?></span>
                            </li>
                            <li>电话：
                                <span><?php echo ($vo["c_tel"]); ?></span>
                            </li>
                            <li>地址：
                                <span><?php echo ($vo["c_addresss"]); ?></span>
                            </li>
                        </ul>
                    </div><!-- name-phone结束 -->
                   </div><!-- userinfo结束 -->

                   <div class="represent representcolor">
                    <div class="representdentity">
                        法定代表人
                    </div>
                    <div class="representdata">
                        <ul>
                            <li>姓名：
                                <span><?php echo ($vo["c_name"]); ?></span>
                            </li>
                            <li>电话：
                                <span><?php echo ($vo["c_phone"]); ?></span>
                            </li>
                            <li>邮箱：
                                <span><?php echo ($vo["c_email"]); ?></span>
                            </li>
                        </ul>
                    </div><!-- name-phone结束 -->
                   </div><!-- represent结束 -->

                   <div class="agent agentcolor">
                    <div class="agentdentity">
                        代理授权人
                    </div>
                    <div class="agentdata">
                        <ul>
                            <li>姓名：
                                <span><?php echo ($vo["c_person_name"]); ?></span>
                            </li>
                            <li>电话：
                                <span><?php echo ($vo["c_person_phone"]); ?></span>
                            </li>
                            <li>邮箱：
                                <span><?php echo ($vo["c_person_email"]); ?></span>
                            </li>
                        </ul>
                    </div><!-- name-phone结束 -->
                   </div><!-- agent结束 -->
                </div>
            </div>

    	</div><!-- userdetail结束 --><?php endforeach; endif; else: echo "" ;endif; ?>
    
    </div><!-- productmodular结束 -->

</div><!-- wrap结束 -->
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript">
    function checkshop(tg,id,cd) {
        $.post('/wldApp/agent.php/Home/Member/disable',{Id:id,Cd:cd}, function(obj) {
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