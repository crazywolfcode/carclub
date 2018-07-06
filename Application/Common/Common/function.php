<?php

/*
 * 前后台通用的公共的函数
 * 
 */

//以下四个函数为方便调试而设置，项目上线可以删除
function lastSql() {
    dump(M()->getLastSql());
}

function writeLog($key, $string = NULL) {
    Think\Log::write("--------{$key}--->" . $string);
}

/**
 * initialize web site config
 * 初始化网站配置
 */
function initializeWebSiteConfig() {
    $Config = M('config');
    $configList = $Config->where('status = 1')->order('sort asc')->getField('name,value');
    C($configList);
}

/**
 * web site md5 cncrypt type
 * 网站的MD5 加密方式
 * @param type $str 要加密的字符串
 * @return type 加密后的字符串
 */
function encrypt($str) {
    return md5(C("AUTH_CODE") . $str);
}

/**
 * 赵先方
 * 密码加密
 * $pwd 加密的密码
 * $solt 加密字符串
 * C('WEB_ENCODE') 网站加密字符串
 */
function en_password($pwd, $solt) {
    $web_code = C('AUTH_CODE');
    return md5(md5($pwd . $web_code) . $solt);
}

/**
 * 友好时间显示
 * @param $time
 * @return bool|string
 */
function friend_date($time) {
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}

/**
 * 返回状态和信息
 * @param type $status
 * @param type $info
 * @param type $url
 * @param type $data
 * @return type
 */
function arrayRes($status, $info, $url = null, $data = null) {
    return array("status" => $status, "info" => $info, "url" => $url, "data" => $data);
}

/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
function check_mobile($mobile) {
    if (preg_match('/1[34578]\d{9}$/', $mobile)) {
        return true;
    }
    return false;
}

/**
 * 检查邮箱地址格式
 * @param $email 邮箱地址
 */
function check_email($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}

/**
 *   实现中文字串截取无乱码的方法
 */
function getSubstr($string, $start, $length) {
    if (mb_strlen($string, 'utf-8') > $length) {
        $str = mb_substr($string, $start, $length, 'utf-8');
        return $str . '...';
    } else {
        return $string;
    }
}

function getCateName($cateid) {
    $name = D('Common/category')->getCategoryNameByID($cateid);
    return $name;
}

function getCateNmaeUid($userid) {
    $data = M('moderate')->where("userid =" . $userid)->field('cateid')->find();
    if ($data) {
        return getCateName($data['cateid']);
    } else {
        return null;
    }
}

/**
 * 赵先方
 * 替换二维数组中 时间戳为正常时间
 * @param  [type] $arr     操作数组
 * @param  [type] $keyName 被替换的键名 可为数组或者字符串
 * @return [type]          [description]
 */
function array_str_time($arr, $keyName) {
    if (!$keyName || !is_array($arr)) {
        return $arr;
    }
    foreach ($arr as $key => &$val) {
        foreach ($val as $k => $v) {
            if (is_array($keyName)) {
                if (in_array($k, $keyName) && !empty($v)) {
                    $arr[$key][$k] = date('Y-m-d', $v);
                }
            } else {
                if (($k == $keyName) && !empty($v)) {
                    $arr[$key][$k] = date('Y-m-d', $v);
                }
            }
        }
    }
    return $arr;
}

/**
 * 赵先方
 * 二维数组指定键的值替换 可同时替换多个
 * @param  [type] &$arr    操作数组  例如 把键名为a值为1换成,101,键名为b值为ii换成ii0ii
 * @param  [type] $keyName 操作的键  例如 array('a','b');
 * @param  [type] $oldVal  原键的值  例如 array(array('1','2'),array('i','ii'));
 * @param  [type] $newVal  原键替换为新的值  例如  array(array('101','202'),array('i0i','ii0ii'));
 * @return [type]          [description]
 */
function array_rep_value($arr, $keyName, $oldVal, $newVal) {
    foreach ($arr as $key => $val) {
        foreach ($val as $k => $v) {
            if (in_array($k, $keyName)) {
                $i = array_search($k, $keyName); //返回存在的那个键值对
                if ($i !== false) {
                    $ii = array_search($v, $oldVal[$i]);
                    if ($ii !== false) {
                        $arr[$key][$k] = $newVal[$i][$ii];
                    }
                }
            }
        }
    }
    return $arr;
}

/**
 * 初始化收藏
 * @param type $id
 * @param type $type
 * @return string
 */
function checkCollect($id, $type) {
    $model = M('collect');
    $userid = session("user.id");
    switch ($type) {
        case 1:
            $condition['actionid'] = $id;
            break;
        case 2:
            $condition['videoid'] = $id;
            break;
        case 3:
            $condition['trbuneid'] = $id;
            break;
        default:
            break;
    }
    $condition['userid'] = $userid;
    $condition['type'] = $type;
    $collect = $model->where($condition)->field('id')->find();
    if ($collect && $collect['id'] > 0) {
        return "collected";
    } else {
        return "four";
    }
}

/**
 * 
 * @param type $str
 * @return string
 * 时间转换
 */
function str_time($str) {
    if (is_numeric($str)) {
        return date('Y年m月d日', $str);
    } else {
        return "时间格式不符";
    }
}

/**
  中文字符串截取
 */
function str_substr($str, $len) {
    $lenth = $len ? $len : 15;
    if (mb_strlen($str) >= $lenth) {
        $str = mb_substr($str, 0, $lenth, 'utf-8') . '...';
    }
    return $str;
}

/**
 * 去除 HTML、XML 以及 PHP 的标签
 * @param type $str
 * @return type
 */
function replaceHtml($str) {
    $strip_tags = strip_tags($str);
    return $strip_tags;
}

/**
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 * @return boolean
 */
function isMobile() {
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
    $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');
   return  CheckSubstrs($mobile_os_list, $useragent_commentsblock) || CheckSubstrs($mobile_token_list, $useragent);
}

function CheckSubstrs($substrs, $text) {
    foreach ($substrs as $substr)
        if (false !== strpos($text, $substr)) {
            return true;
        }
    return false;
}

/**
 * 获取用户信息
 * @param $user_id_or_name  用户id 邮箱 手机 第三方id
 * @param int $type  类型 0 user_id查找 1 邮箱查找 2 手机查找 3 第三方唯一标识查找
 * @param string $oauth  第三方来源
 * @return mixed
 */
function get_user_info($user_id_or_name, $type = 0, $oauth = "") {
    $map = array();
    switch ($type) {
        case 0:
            $map['user_id'] = $user_id_or_name;

            break;
        case 1:

            $map['email'] = $user_id_or_name;
            break;
        case 2:
            $map['mobile'] = $user_id_or_name;

            break;
        case 3:
            $map['openid'] = $user_id_or_name;
            $map['oauth'] = $oauth;
            break;
        default:
            break;
    }
    $user = M('users')->where($map)->find();
    return $user;
}

/**
 * 随机生成字符串
 * @param type $length
 * @return string
 */
function rend_char($length = 6) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1); 
        $str .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $str;
}

/**
 * 为用户增加积分 手机 PC 都会用到
 * @param type $userid
 * @param type $point
 * @return boolean
 */
function addUserPoint($userid, $point = 1) {
    if (!$userid) {
        return FALSE;
    }
    M('user')->where("id = " . $userid)->setInc("point", $point);
}

// 定义一个函数getIP() 客户端IP，
function getIP() {
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknow";
    return $ip;
}

/**
 * 判断会员是否是某个的板块的版主，返回分类信息
 * @param type $userid
 * @return type
 */
function isTribuneManager($userid) {
    if (!$userid) {
        return null;
    }
    $moderate = M('moderate')->where("userid =" . $userid)->find();
    if (!$moderate) {
        return null;
    }
    $cate = M('category')->where("id = " . $moderate['cateid'])->find();
    if ($cate) {
        return $cate;
    } else {
        return null;
    }
}

/**
 * 判断会员是否为超级管理员
 * @param type $userid
 * @return type
 */
function isAdmin($userid, $userType = null) {
    if (!$userid) {
        return null;
    }
    $moderate = M('admin')->where("userid =" . $userid)->find();
    if (!$moderate) {
        return null;
    } else {
        return TRUE;
    }
}

/**
 * 更新用户禁言表，增加和删除
 * @param type $status
 * @param type $userid
 * @param type $adduserid
 * @param type $date
 * @param type $days
 * @return int
 */
function updateUserGag($status, $userid, $adduserid = null, $date = null, $days = 0) {
    $UserGag = M('usergag');
    if ($status == 1) {
        $res = $UserGag->where("userid =" . $userid)->delete();
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    } else {
        $data['userid'] = $userid;
        $data['addtime'] = time();
        $data['endtime'] = $date;
        $data['adduserid'] = $adduserid;
        $data['days'] = $days;
        $res = $UserGag->add($data);
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }
}

/**
 * 是否收藏 是否曾经收藏过被取消
 * @param type $parentid 
 * @param type $type
 * @param type $userid
 */
function isCollected($parentid, $type, $userid) {
    if ($parentid && $type && $userid) {
        $map['parentid'] = $parentid;
        $map['userid'] = $userid;
        $map['type'] = $type;
        $res = M('collect')->where($map)->find();
        if ($res) {
            return TRUE;
        }
    } else {
        return false;
    }
}

/**
 * 增加收藏内容的被收藏数
 * @param int $type
 * @param int $id
 */
function addCollects($type, $id) {
    //类型 1活动 2视频 3文章4图集
    if (!$type) {
        return null;
    }
    $Model;
    switch ($type) {
        case 1:
            $Model = M("action");
            break;
        case 2:
            $Model = M("video");
            break;
        case 3:
            $Model = M("tribune");
            break;
        case 4:
            $Model = M("imglist");
            break;
    }
    if ($Model) {
        $Model->where("id =" . $id)->setInc("collects");
    }
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 赵先方
 * @time 2015/12/3
 */
function is_login_home() {
    return session(C('HOME_SESSION_NAME')) ? TRUE : FALSE;
}

/**
 * 当评论被删除后，修改评论数
 * @param type $type
 * @param type $parentid
 * @param type $opt
 */
function updateParentComments($type, $parentid, $opt = 1) {
    switch ($type) {
        case 1:
            $Model = M('Tribune');
            break;
        case 2:
            $Model = M('imglist');
            break;
        case 1:
            $Model = M('video');
            break;
        case 4:
            $Model = M('Action');
            break;
    }
    if ($opt > 0) {
        $Model->where('id =' . $parentid)->setInc('comments', 1);
    } else {
        $Model->where('id =' . $parentid)->setDec('comments', 1);
    }
}

/**
 * 删除评论的回复 
 * @param type $id 
 */
function deleteReplay($id) {
    M('comment')->where("parentid =" . $id . " and isreplay=1")->delete();
}

function getLocalUser($openid, $type) {
    //来源 0 正常注册 1微信 2QQ互联
    $user = NULL;
    $User = M('user');
    switch ($type) {
        case "wx":
            $map['wx_openid'] = $openid;

            break;
        case "qq":
            $map['wx_openid'] = $openid;

            break;
        default:
            break;
    }
    if ($map) {
        $user = $User->where($map)->find();
        return $user;
    }
    return null;
}

/**
 * QQ WX 登陆
 * @param type $openid
 * @param type $type
 */
function ThirdLogin($openid, $type) {
    //用户每登陆成功后，根据积分将对应的等级设置在user 表里
    \Think\Hook::add("login_success", 'Common\\Behaviors\\SetUserLevelBehavior');
    $User = M('user');
    if ($type == 'wx') {
        $map['wx_openid'] = $openid;
    } elseif ($type == 'qq') {
        $map['qq_openid'] = $openid;
    } else {
        $map['id'] = 0;
    }
    $user = $User->where($map)->find();
    if (!$user) {
        header("Location:" . U('Index/index'));
    }
    session(C('HOME_SESSION_NAME'), $user);
    if ($user) {
        \Think\Hook::listen("login_success", $user);
    }
}

/**
 * 刷新 Session 可传Userid 或者 UserInfor
 * @param type $userid
 * @param type $userinfo
 */
function refalshSession($userid = false, $userinfo = false) {
    if ($userinfo) {
        session(C('HOME_SESSION_NAME'), $userinfo);
    } else {
        if ($userid > 0) {
            $user = M('user')->where("id = " . $userid)->find();
            session(C('HOME_SESSION_NAME'), $user);
        }
    }
}
