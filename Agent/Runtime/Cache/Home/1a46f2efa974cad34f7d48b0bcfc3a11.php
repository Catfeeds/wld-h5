<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">

<title>区代理后台管理首页</title>
</head>
<frameset rows="60,*"  frameborder="NO" border="0" framespacing="0">
	<frame src="top.html" noresize="noresize" frameborder="NO" name="topFrame" scrolling="no" marginwidth="0" marginheight="0" target="top" />
  <frameset cols="120,*"  rows="100%,*" id="main">
	<frame src="left.html" name="leftFrame" noresize="noresize" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" target="left" />
	<frame src="<?php echo U('Information/index');?>" name="right" marginwidth="0" marginheight="0" frameborder="0" scrolling="auto" target="_self" />
  </frameset>
<noframes>
  <body></body>
    </noframes>
</html>