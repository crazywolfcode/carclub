<?php

return array(
    //'配置项'=>'配置值'
    //'DB_SQL_BUILD_CACHE' => true, // sql 缓存
    //'DB_SQL_BUILD_QUEUE' => 'File', // 文件缓存
    // 'DB_SQL_BUILD_LENGTH' => 200, // SQL缓存的队列长度
    //'DATA_CACHE_TIME' => 60,
    // 'DATA_CACHE_TYPE' =>  'File', 
    'LOAD_EXT_CONFIG' => 'html', // 加载其他自定义配置文件 html.php
    'URL_HTML_SUFFIX' => 'html',
    'HTML_CACHE_ON' => FALSE, // 开启静态缓存 到项目上线的时候设置 True
    'HTML_CACHE_TIME' => 60, // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(// 定义静态缓存规则
        // 定义格式1 数组方式
        //'静态地址'    =>     array('静态规则', '有效期', '附加规则'), 
        'index:index' => array('{:module}_{:controller}_{:action}', CACHE_TIME), // 首页静态缓存3600秒钟       
    ),
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Public:error',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:success',
    // 显示页面Trace信息
    'SHOW_PAGE_TRACE' => FALSE,
    'HOME_SESSION_NAME' => "user",
);
