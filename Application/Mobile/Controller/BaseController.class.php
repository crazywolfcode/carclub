<?php

namespace Mobile\Controller;

use Think\Controller;

require_once (WP_API_PATH . "class/OAuth.class.php");
require_once (WP_API_PATH . "class/jssdk.class.php");

/**
 * Description of BaseController
 *
 * @author hacker
 */
class BaseController extends Controller {

    public $user = array();
    public $user_id = 0;
    public $session_id;
    public $TribuneCates;

    /*
     * 初始化操作
     */

    public function _initialize() {
        try {
            if (!isMobile()) {
//                $referer = $_SERVER['HTTP_REFERER'];
//                if ($referer) {
//                    if (strstr($referer, "Mobile")) {
//                        $referer = str_replace("Mobile", "Home", $referer);
//                    }
//                    if (strstr($referer, "mobile")) {
//                        $referer = str_replace("mobile", "home", $referer);
//                    }
//                    header("Location:" . $referer);
//                } else {
                    $this->redirect('Home/Index/index');
//                }
            }
        } catch (Exception $exc) {
            writeLog("访问出错", "不是手机浏览器访问手机端" . $exc);
        }
        //初始化网站设置
        initializeWebSiteConfig();
        $this->session_id = session_id(); // 当前的 session_id
        if (session('?user')) {
            $user = session('user');
            //$user = M('user')->where("user_id = {$user['user_id']}")->find();
            $this->user = $user;
            $this->user_id = $user['id'];
            session('user', $user);
            $this->assign('user', $user); //存储用户信息            
        } else {
            $this->user[user_id] = 0;
        }
        $this->assign('user_id', $this->user_id);

        if (empty($this->TribuneCates)) {
            $this->TribuneCates = D('Common/category')->getCategorys();
        }
        $this->assign('catelist', $this->TribuneCates);

        // 判断当前用户是否手机                
        if (isMobile()) {
            cookie('is_mobile', '1', 3600);
        } else {
            cookie('is_mobile', '0', 3600);
        }
        //微信浏览器
        if ($this->isWeixinBrowser()) {
            if (!is_login_home()) {
                //去授权获取openid
                $oAuth = new \OAuth(C("WX_P_APPID"), C("WX_P_APPSERET"));
                $openid = $oAuth->GetOpenid($this->get_url());
                //判断本地是否已经有账号
                $localUser = getLocalUser($openid, $type = "wx");
                if (!$localUser) {
                    //本地没有，先获取再去邦绽
                    $user = $oAuth->getUserInfo($openid);
                    if ($user) {
                        //跳转到绊定页面                      
                        $user['oauth'] = 1;
                        session('temp_user', $user);
                        $this->redirect('Login/binding');
                    }
                }
                //用用户状态是否正常
                if ($localUser['status'] == 1) {
                    ThirdLogin($openid, "wx");
                }
            }
        }
        //初始化 JSSDK
        $jssdk = new \JSSDK(C('WX_P_APPID'), C('WX_P_APPSERET'));
        $signPackage = $jssdk->GetSignPackage();
        $this->assign("signPackage", $signPackage);
    }

    private function isWeixinBrowser() {
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            return ture;
        } else {
            return FALSE;
        }
    }

    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }

}
