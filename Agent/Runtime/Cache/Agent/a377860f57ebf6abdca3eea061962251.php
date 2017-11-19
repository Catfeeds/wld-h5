<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>公告详情</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/style.css?v=1.7">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/agent.js"></script>
</head>
<body> 
    <div class="information" style="padding:0;">
        <div class="detail_head">
            <?php echo ($vo["c_title"]); ?>
        </div>
        <div class="detail_mid">
            <div class="fl detail_midleft">
                发表时间：<?php echo ($vo["c_addtime"]); ?>
            </div>
            <div class="fr detail_midright">
                文章来源：<?php echo ($vo["c_origin"]); ?>
            </div>
        </div>
        <div class="detail_content">
            <p><?php echo ($vo["c_content"]); ?></p>
        </div>
    </div>

    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/layer/1.9.3/layer.js"></script>
</body> 
</html>