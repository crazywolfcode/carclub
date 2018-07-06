<?php

return array(
    //'配置项'=>'配置值'
    // 加载扩展配置文件
    'LOAD_EXT_CONFIG' => 'db,constract', //将数据库的配制独立//将所有的常量独立
  
    'TMPL_PARSE_STRING' => array(
        '__SYSSTATIC__' => __ROOT__ . '/Public/static',
        '__IMG__' => __ROOT__ . '/Public/static/img',
        '__JS__' => __ROOT__ . '/Public/static/js',
        '__CSS__' => __ROOT__ . '/Public/static/css',
        '__PLUGINS__' => __ROOT__ . '/Public/static/plugins',
        '__LAYER__' => __ROOT__ . '/Public/static/layer',
        '__LAYUI__' => __ROOT__ . '/Public/static/layui',
        '__SYSIMG__' => __ROOT__ . '/public/static/img',
        '__SYSJS__' => __ROOT__ . '/public/static/js',
        '__SYSSS__' => __ROOT__ . '/public/static/css',
        '__ERROR__' => __ROOT__ . '/Public/static/error',
    ),
    
    // 显示页面Trace信息
    'SHOW_PAGE_TRACE' => true,
    'ADMIN_SESSION_NAME' => 'admin_session',
    'HOME_SESSION_NAME' => 'user',
    
    
);
