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
// 应用入口文件
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', True);

// 定义应用目录
define('APP_PATH', './Application/');
// 定义ＱＱ互联API的地址
define('QC_API_PATH', './Application/API/QQConnectApi/');
// 定义WX公众号API的地址
define('WP_API_PATH', './Application/API/WechatPublicApi/');

//以下三条用不到
//define('UPLOAD_PATH','Public/upload/'); // 编辑器图片上传路径
//define('FILE_PATH','Public/upload/file/'); // 文件上传路径
//define('VIDEO_PATH','Public/upload/video/'); // 视频上传路径
define('CACHE_TIME', 3600); // 缓存时间一个小时
define('HTML_PATH', './Application/Runtime/Html/'); //静态缓存文件目录，HTML_PATH可任意设置，此处设为当前项目下新建的html目录  
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单