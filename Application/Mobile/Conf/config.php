<?php

return array(
    //'配置项'=>'配置值'
    'SHOW_PAGE_TRACE' => false,//开启页面Treace调试
    'LOAD_EXT_CONFIG' => 'html', // 加载其他自定义配置文件 html.php
    'URL_HTML_SUFFIX' => 'html',
    'HTML_CACHE_ON' => true, // 开启静态缓存
    'HTML_CACHE_TIME' => 6, // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.html', //设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(//定义静态缓存规则
        // 定义格式1 数组方式
        //'静态地址' =>  array('静态规则', '有效期', '附加规则'),
        'index:index' => array('{:module}_{:controller}_{:action}', 6), // 首页静态缓存 60秒钟
    
    ),
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Public:error',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:success',
);
