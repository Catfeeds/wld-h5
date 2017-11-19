<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
    <title>微商详情</title>
    <meta http-equiv="content-type" content="text/html" charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/userdetail.css">
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
            <div class="next">
                <span>查看详细信息</span>
            </div>
        </div>

        <div class="productmodular-bottom">
            <div class="basicinfo">
                <div class="userimg">
                    <img src="/wldApp/<?php echo ($vo["c_headimg"]); ?>" alt="个人图像">
                </div>
                <div class="userbasicinfo">
                    <div class="username"><?php echo ($vo["c_store_name"]); ?></div>
                    <div class="dentity userdentity">
                    <?php if ($vo['c_isstore'] == 1) { ?> 
                    店铺
                    <?php } else { ?> 
                    个人
                    <?php } ?>
                    </div>
                    <div class="grrencolor">账号：<span><?php echo ($vo["c_username"]); ?></span></div>
                    <div class="havestore"><span>
                        <?php if ($vo['c_isstore'] == 1) { ?>
                                有
                            <?php } else { ?>
                                无
                            <?php } ?>
                        </span>实体店</div>
                </div>
            </div><!-- basicinfo结束 -->

            <div class="detailinfo">
                <div class="detailinfo-top">
                    <div class="detailinfotitle">
                        基本信息
                    </div>
                </div>

                <div class="detailinfo-bottom ">
                    <div class="allinfo">
                        <div class="personalinfo">
                            <ul>
                                <li>申请人：
                                    <span><?php echo ($vo["c_name"]); ?></span>
                                </li>
                                <li>QQ：
                                    <span><?php echo ($vo["c_qq"]); ?></span>
                                </li>
                                <li>邮箱：
                                    <span><?php echo ($vo["c_email"]); ?></span>
                                </li>
                                <li>手机号码：
                                    <span><?php echo ($vo["c_phone"]); ?></span>
                                </li>                                
                            </ul>
                        </div>
                       <div  class="storeinfo">
                            <ul>
                                <li>店铺名称：
                                    <span><?php echo ($vo["c_store_name"]); ?></span>
                                </li>
                                <li>经营类别：
                                    <span><?php echo ($vo["c_store_type"]); ?></span>
                                </li>
                                <li>固定电话：
                                    <span><?php echo ($vo["c_store_phone"]); ?></span>
                                </li>
                                <li>营业面积：
                                    <span><?php echo ($vo["c_store_aire"]); ?></span>
                                </li>                                
                            </ul>   
                       </div>
                        <div  class="storeinfo">
                            <ul>
                                <li>店员人数
                                    <span><?php echo ($vo["c_store_people"]); ?></span>
                                </li>
                                <li>投入资金
                                    <span><?php echo ($vo["c_store_money"]); ?></span>
                                </li>
                                <li>店铺地址
                                    <span><?php echo ($vo["c_store_address"]); ?></span>
                                </li>
                            </ul>    
                        </div>
                        
                    </div><!-- allinfo结束 -->
                    
                    <div class="salesinfo">
                        营销策略：
                        <span><?php echo ($vo["c_store_plan"]); ?></span>
                    </div>
                     <div class="salesinfo">
                        销售渠道：
                        <span><?php echo ($vo["c_store_place"]); ?></span>
                    </div>
                     <div class="salesinfo">
                        客服服务方式及流程：
                        <span><?php echo ($vo["c_store_service"]); ?></span>
                    </div>

                    <div class="usercarID">
                        <div>申请人身份证</div>
                        <div class="carIDimg">
                            <?php foreach ($vo['imglist'] as $key => $value): ?>
                                <?php if ($value['c_type'] == 2 && $value['c_sign'] == 5): ?>
                                    <img src="/wldApp/<?php echo $value['c_imgpath'] ?>" alt="申请人身份证">                                
                                <?php endif ?>                                
                            <?php endforeach ?>
                        </div>
                    </div>
                    <div class="storedisplay">
                        <div>店铺展示照</div>
                        <div class="storeimg">
                            <?php foreach ($vo['imglist'] as $key => $value): ?>
                                <?php if ($value['c_type'] == 2 && $value['c_sign'] == 0): ?>
                                    <img src="/wldApp/<?php echo $value['c_imgpath'] ?>" alt="申请人身份证">                                
                                <?php endif ?>                                
                            <?php endforeach ?>
                        </div>
                    </div>

                </div><!-- detailinfo-bottom 结束 -->
                
            </div><!-- detailinfo结束 -->
        </div><!-- productmodular-bottom结束 -->


        
    </div><!-- productmodular结束 -->

</div><!-- wrap结束 -->
</body>
</html>