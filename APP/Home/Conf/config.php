<?php
return array(
	//'配置项'=>'配置值'
	//用户页面模板配置
	'TMPL_PARSE_STRING' => array(
        '__RSC__' => __ROOT__ . '/Resource', // 通用资源目录
        '__COMMON__' => __ROOT__ . '/Resource/Common', // 通用资源目录
        '__JS__' => __ROOT__ . '/Resource/' . MODULE_NAME . '/js', //js文件目录
        '__CSS__' => __ROOT__ . '/Resource/' . MODULE_NAME . '/css', //css文件目录
        '__IMG__' => __ROOT__ . '/Resource/' . MODULE_NAME . '/img', //图片文件目录
        '__HOME__' => __ROOT__ . '/index.php/'. MODULE_NAME, //项目控制器访问地址
    ),

    /* 错误页面模板 */
    'TMPL_ACTION_ERROR' => APP_PATH . 'Base/View/Common/jump.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => APP_PATH . 'Base/View/Common/jump.html', // 默认成功跳转对应的模板文件
    // 'TMPL_EXCEPTION_FILE' => APP_PATH . 'Base/View/Common/exception.html',// 异常页面的模板文件
);