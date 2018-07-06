<?php

return array(
    'VIEW_PATH' => './Template/Home/', // 改变某个模块的模板文件目录
    'DEFAULT_THEME' => 'default', // 模板名称
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Template/home/default/static', // 增加新的image  css js  访问路径 
        '__IMG__' => __ROOT__ . '/Template/home/default/static/img',
        '__JS__' => __ROOT__ . '/Template/home/default/static/js',
        '__CSS__' => __ROOT__ . '/Template/home/default/static/css',
        '__PLUGINS__' => __ROOT__ . '/Template/home/default/static/plugins',
        '__PUBLICIMG__' => __ROOT__ . '/public/static/img',
        '__PUBLICJS__' => __ROOT__ . '/public/static/js',
        '__PUBLICCSS__' => __ROOT__ . '/public/static/css',
        '__PUBLICVIDEO__' => __ROOT__ . '/public/static/video',
        '__PUBLIC_CSS_PLUGINS__' => __ROOT__ . '/public/static/css/plugins',
        '__PUBLIC_JS_PLUGINS__' => __ROOT__ . '/public/static/js/plugins',
        '__HOMELOGIN__' => __ROOT__ . '/public/static/login',
        '__ROOT__' => __ROOT__,
        'fa fa- '=>"icon-"
    ),
);
?>