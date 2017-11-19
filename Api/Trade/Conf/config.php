<?php
return array(

	//用户页面模板配置
	'TMPL_PARSE_STRING' => array(
        '__COMMON__' => __ROOT__ . '/Resource/Common', // 通用资源目录
        '__JS__' => __ROOT__ . '/Resource/' . MODULE_NAME . '/js', //js文件目录
        '__CSS__' => __ROOT__ . '/Resource/' . MODULE_NAME . '/css', //css文件目录
        '__IMG__' => __ROOT__ . '/Resource/' . MODULE_NAME . '/img', //图片文件目录
        '__HOME__' => __ROOT__ . '/index.php/'. MODULE_NAME, //项目控制器访问地址
    ),
);

?>
