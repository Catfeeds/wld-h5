<?php
return array(

	'TMPL_PARSE_STRING'  => array(

	  '__HPUBLIC__' => __ROOT__.'/'.APP_PATH.'Home/View/Public', // 更改默认的/Public 替换规则

	  '__HJS__'     => __ROOT__.'/'.APP_PATH.'Home/View/Public/js', //js文件目录

	  '__HCSS__'    => __ROOT__.'/'.APP_PATH.'Home/View/Public/css', //css文件目录

	  '__HIMG__'    => __ROOT__.'/'.APP_PATH.'Home/View/Public/images', //图片文件目录

	  '__HSKIN__'   =>  __ROOT__.'/'.APP_PATH.'Home/View/Public/skin', //皮肤样式文件目录

	  '__HLIB__'	=>  __ROOT__.'/'.APP_PATH.'Home/View/Public/lib', //系统核心样式文件目录

	  '__HHOME__'   => WEB_HOST.'/admin.php/Home',  //项目控制器访问地址

	),

	// 活动信息配置
	'ActivityName' => require_once './data/config/activityname.php',

);