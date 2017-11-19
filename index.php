<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// WEB应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);
define('WEB_HOST','https://'.$_SERVER['HTTP_HOST']);   //本服务器域名

// 定义应用目录
define('APP_PATH','APP/');

define('COM_PATH','Common/');

//定义默认访问模块
define('ALLOW','Controller');

define('DS', DIRECTORY_SEPARATOR);

define('SYS_PATH',dirname(__FILE__).DS);
// 引入ThinkPHP入口文件
require './MiLib/base.php';


?>