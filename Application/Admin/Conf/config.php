<?php

return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__IMG__' => __ROOT__ . '/Public/static/img',
        '__JS__' => __ROOT__ . '/Public/static/js',
        '__CSS__' => __ROOT__ . '/Public/static/css',
        '__PLUGINS__' => __ROOT__ . '/Public/static/plugins',
        '__LAYER__' => __ROOT__ . '/Public/static/layer',
        '__LAYUI__' => __ROOT__ . '/Public/static/layui',
        '__PUBLICIMG__' => __ROOT__ . '/public/static/img',
        '__PUBLICJS__' => __ROOT__ . '/public/static/js',
        '__PUBLICCSS__' => __ROOT__ . '/public/static/css',
        '__ERROR__' => __ROOT__ . '/Public/static/error',
    ),
    // 显示页面Trace信息
    'SHOW_PAGE_TRACE' => FALSE,
    'ADMIN_SESSION_NAME' => 'admin_user',
    'WEB_ENCODE' => '', //网站加密字符串 动态获取的，不要给值
    'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文
);
