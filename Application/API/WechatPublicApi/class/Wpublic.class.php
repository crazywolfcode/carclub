<?php

/**
 * Description of Wpublic
 * 微信公众号的基类
 * @author Crazycode
 * @date 2017-04-18 09:00:03
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
require_once (WP_API_PATH . "class/Error.class.php");
require_once (WP_API_PATH . "class/Http.class.php");

class Wpublic {

    private static $ACCESS_TOKEN;
    private static $ACCESS_TOKEN_LASTTIME; //上一次获取Token的时间
    private static $DEFAULT_EXPIRES = 7000;  
    private static $APPID;
    private static $APPSECRET;
    private $error;

    function __construct() {
        if (!self::$APPID) {
            $this->inintAPPID();
        }
        if (!self::$APPSECRET) {
            $this->inintAPPSECRET();
        }
        $this->error = new Error();
    }

    static function getAccess_token() {
        if (self::$ACCESS_TOKEN) {
            if ($this->checkExpires()) {               
                return self::$ACCESS_TOKEN;
            }
        } else {
            self::getAccessToken();
            return self::$ACCESS_TOKEN;
        }
    }

    static function setAccess_token($ACCESS_TOKEN) {
        self::$ACCESS_TOKEN = $ACCESS_TOKEN;
    }

    static function inintAPPID() {
        self::$APPID = C("WX_P_APPID");
    }

    static function inintAPPSECRET() {
        self::$APPSECRET = C("WX_P_APPSERET");
    }

    /**
     * 检查Access_token有效时间是否过期
     * @return boolean
     */
    private function checkExpires() {
        if (time() - self::$ACCESS_TOKEN_LASTTIME > self::$DEFAULT_EXPIRES) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * 从微信公众平台获取AccessToken
     */
    private static function getAccessToken() {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::$APPID . "&secret=" . self::$APPSECRET;         
        $http = new Http();
        $get = $http->httpRequest($url, "get");
        $data = json_decode($get, JSON_UNESCAPED_UNICODE);    
        if (!$data['errcode']) {
            self::$ACCESS_TOKEN = $data['access_token'];           
            self::$ACCESS_TOKEN_LASTTIME = time();
        } else {
            $this->error->showError($data['errcode']);
        }
    }

}
