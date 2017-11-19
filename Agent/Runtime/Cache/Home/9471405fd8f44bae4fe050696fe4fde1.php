<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">

<title>商家后台管理</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/reset.css">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<style>
 ul{ list-style:none; display:block; width:90px; margin:0px; padding:0px; border:1px solid #ccc; border-top:0px; height:92px; overflow:scroll;overflow-x:hidden;}
 ul li{ font-size:12px; height:18px; line-height:18px; cursor:pointer;word-break:keep-all; overflow:hidden;}
 ul .selected{ background-color:#BC0902; color:#FFF;}
</style>
</head>
<body>
<div style=" border:1px solid #CCCCCC; width:90px; height:19px; line-height:20px;">
<input type="text" class="select_input" style="width:70px; border:0px;" value="请选择分类" readonly="readonly"><img src="select_option.jpg" class="select_img" height="18" style=" vertical-align:middle;cursor:pointer; "/></input>
</div>
<ul>
 <li class="selected">请选择分类</li>
    <li title="水果类">水果类</li>
    <li title="蔬菜类">蔬菜类</li>
    <li title="瓜果类">瓜果类</li>
    <li title="水果类2">水果类2</li>
    <li title="蔬菜类2">蔬菜类2</li>
    <li title="瓜果类2瓜果类大">瓜果类2瓜果类大</li>
    <li title="瓜果类3">瓜果类3</li>
    <li title="水果类3">水果类3</li>
    <li title="蔬菜类3">蔬菜类3</li>
    <li title="瓜果类3">瓜果类3</li>
</ul>
<script language="javascript">
$(document).ready(function(){
 $("ul").hide();
 $(".select_img").bind("click",function()
     {
       $("ul").fadeIn(800);
     }); 
 
 $("ul li").hover(function(){
     $(this).addClass("selected").siblings().removeClass("selected");
    }).bind("mouseup",function(){
     $("ul").fadeOut(1);
     var txt = $(this).html();
     var input = document.getElementsByTagName("input");
     input[0].value = txt;
       
    });
 $(this).bind("mouseup",function(){
     //如果ul还显示着，将其隐藏
     if($("ul").css("display") == "block")
     {
      $("ul").fadeOut(0); 
     }
    });
 
});
</script>
</body>
</html>