<?php

return array(
    'VIEW_PATH' => './Template/Mobile/', // 改变某个模块的模板文件目录
    'DEFAULT_THEME' => 'default', // 模板名称
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Template/Mobile/default/static', // 增加新的image  css js  访问路径 
        '__IMG__' => __ROOT__ . '/Template/Mobile/default/static/images',
        '__JS__' => __ROOT__ . '/Template/Mobile/default/static/js',
        '__CSS__' => __ROOT__ . '/Template/Mobile/default/static/css',
        '__PUBLICIMG__' => __ROOT__ . '/public/static/img',
          '__PUBLICSTATIC__' => __ROOT__ . '/public/static',
        '__PUBLICJS__' => __ROOT__ . '/public/static/js',
        '__PUBLICCSS__' => __ROOT__ . '/public/static/css',
        '__PUBLICVIDEO__' => __ROOT__ . '/public/static/video',
        '__ROOT__'=>__ROOT__,
    ),
);
?>