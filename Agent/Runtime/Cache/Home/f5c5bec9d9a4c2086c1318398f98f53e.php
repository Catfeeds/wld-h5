<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
    <title>微商列表</title>
    <meta http-equiv="content-type" content="text/html" charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/vipdetail.css?v=1.5">
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
                全部代理商
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
                    <div class="username"><?php echo ($vo["c_name"]); ?></div>
                    <div class="dentity companydentity-bg">
                        <?php if ($vo['c_type'] == 1): ?>
                            企业
                        <?php else: ?>
                            个人   
                        <?php endif ?>
                    </div>
                    <div class="grrencolor">账号：<span><?php echo ($vo["c_username"]); ?></span></div>
                    <div class="havestore"><span>拥有&nbsp;&nbsp;<?php echo ($vo["shopnum"]); ?>&nbsp;&nbsp;</span>家微商</div>
                </div>
            </div><!-- basicinfo结束 -->

            <div class="detailinfo"> 

                <div class="detailinfo-top">
                    <div class="detailinfotitle selected">
                        基本信息
                    </div>
                    <div class="detailinfotitle ">
                        法定代表人
                    </div>
                    <div class="detailinfotitle ">
                        代理授权负责人
                    </div>
                </div>

                <div class="detailinfo-bottom ">
                    <div class="allinfo">
                        <div class="personalinfo">
                            <ul>
                                <li>申请<?php if ($vo['c_type'] == 1): ?>单位 <?php else: ?>人<?php endif ?>：
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
                                <li>申请日期：
                                    <span><?php echo mb_substr($vo['c_addtime'], 0, 10, 'utf8') ?></span>
                                </li>
                                <li>推荐微商剩余人数：
                                    <span><?php echo ($vo["c_num"]); ?></span>
                                </li> 
                                <li>使用邀请码：
                                    <span><?php echo ($vo["c_invite_code"]); ?></span>                                 
                                </li>                               
                                <li>店铺地址
                                    <span><?php echo ($vo["c_addresss"]); ?></span>
                                </li>
                            </ul>   
                       </div>
                        <div  class="storeinfo">
                            <ul>
                                <?php if ($vo['c_type'] == 1): ?>
                                <li>营业执照：
                                    <span><?php echo ($vo["c_charter"]); ?></span>
                                </li>
                                <li>营业代码证号：
                                    <span><?php echo ($vo["c_charter_code"]); ?></span>
                                </li>                                    
                                <?php endif ?>
                                <li>邮政编码：
                                    <span><?php echo ($vo["c_postcode"]); ?></span>
                                </li>
                                <li>固定电话：
                                    <span><?php echo ($vo["c_tel"]); ?></span>
                                </li>
                            </ul>    
                        </div>
                        
                    </div><!-- allinfo结束 -->

                    <div class="allinfo hide">
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
                                <li>固定电话：
                                    <span><?php echo ($vo["c_tel"]); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div><!-- allinfo结束 -->

                    <div class="allinfo hide">
                        <div class="personalinfo">
                            <ul>
                                <li>申请人：
                                    <span><?php echo ($vo["c_person_name"]); ?></span>
                                </li>
                                <li>QQ（传真）：
                                    <span><?php echo ($vo["c_person_fax"]); ?></span>
                                </li>
                                <li>邮箱：
                                    <span><?php echo ($vo["c_person_email"]); ?></span>
                                </li>
                                <li>手机号码：
                                    <span><?php echo ($vo["c_person_phone"]); ?></span>
                                </li>
                                <li>固定电话：
                                    <span><?php echo ($vo["c_person_tel"]); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div><!-- allinfo结束 -->
                    
                    
                </div><!-- detailinfo-bottom 结束 -->
                <div class="usercarID">
                    <div>申请人身份证</div>
                    <div class="carIDimg">
                        <?php foreach ($vo['imglist'] as $key => $value): ?>
                            <?php if ($value['c_type'] == 1 && $value['c_sign'] == 5): ?>
                                <img src="/wldApp/<?php echo $value['c_imgpath'] ?>" alt="申请人身份证">                                
                            <?php endif ?>                                
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="usercarID">
                    <div>授权人身份证</div>
                    <div class="carIDimg">
                        <?php foreach ($vo['imglist'] as $key => $value): ?>
                            <?php if ($value['c_type'] == 1 && $value['c_sign'] == 6): ?>
                                <img src="/wldApp/<?php echo $value['c_imgpath'] ?>" alt="申请人身份证">                                
                            <?php endif ?>                                
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="usercarID">
                    <div>授权证书</div>
                    <div class="carIDimg">
                        <?php foreach ($vo['imglist'] as $key => $value): ?>
                            <?php if ($value['c_type'] == 1 && $value['c_sign'] == 7): ?>
                                <img src="/wldApp/<?php echo $value['c_imgpath'] ?>" alt="申请人身份证">                                
                            <?php endif ?>                                
                        <?php endforeach ?>
                    </div>
                </div>
                <?php if ($vo['c_type'] == 1): ?>
                 <div class="usercarID">
                    <div>营业执照</div>
                    <div class="carIDimg">
                        <?php foreach ($vo['imglist'] as $key => $value): ?>
                            <?php if ($value['c_type'] == 1 && $value['c_sign'] == 4): ?>
                                <img src="/wldApp/<?php echo $value['c_imgpath'] ?>" alt="申请人身份证">                                
                            <?php endif ?>                                
                        <?php endforeach ?>
                    </div>
                </div>
                <?php endif ?>    

            </div><!-- detailinfo结束 -->
        </div><!-- productmodular-bottom结束 -->

    </div><!-- productmodular结束 -->

</div><!-- wrap结束 -->

<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript">
    $(function(){
        var $dinfo_top_div = $(".detailinfo-top div");
        $dinfo_top_div.click(function(){
            $(this).addClass("selected").siblings().removeClass("selected");
            var index = $dinfo_top_div.index(this);
            $(".detailinfo-bottom > .allinfo").eq(index).show();
            $(".detailinfo-bottom > .allinfo").eq(index).siblings().hide();

        })
    })
</script>
</body>
</html>