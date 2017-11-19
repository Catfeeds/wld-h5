<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_PARSE_STRING'  => array(

	  '__PUBLIC__' => __ROOT__.'/'.APP_PATH.'Agent/View/Public', // 更改默认的/Public 替换规则

	  '__JS__'     => __ROOT__.'/'.APP_PATH.'Agent/View/Public/js', //js文件目录

	  '__LIB__'     => __ROOT__.'/'.APP_PATH.'Agent/View/Public/lib', //第三方框架目录
	  
	  '__CSS__'    => __ROOT__.'/'.APP_PATH.'Agent/View/Public/css', //css文件目录

	  '__IMG__'    => __ROOT__.'/'.APP_PATH.'Agent/View/Public/images', //图片文件目录	  

	  '__HOME__'   => WEB_HOST.'/agent.php/Agent',  //项目控制器访问地址
			
	),
);