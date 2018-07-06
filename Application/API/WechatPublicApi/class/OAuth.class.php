<?php

/**
 * Description of Aouth
 * 微信网页授权
 * @author Crazycode
 * @date 2017-04-18 02:47:50
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
require_once (WP_API_PATH . "class/Error.class.php");

class OAuth {

    private static $default_expires = 7000;
    private static $last_get_WAT_time;
    private $appid;
    private $appsecret;

    function __construct($appid, $appsecret) {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }

    public function GetOpenid($redirect_uri) {
        if ($_SESSION['openid']) {
            $time = time() - self::$last_get_WAT_time;
            if ($time < self::$default_expires) {
                return $_SESSION['openid'];
            }
        }
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode($redirect_uri);
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $data = $this->getFromMp($code);
            $_SESSION['openid'] = $data['openid'];
            $_SESSION['web_access_token'] = $data['access_token'];
            self::$last_get_WAT_time = time();
            return $data['openid'];
        }
    }

    public function GetWebAccessToken() {
        if ($_SESSION['web_access_token']) {
            $time = time() - self::$last_get_WAT_time;
            if ($time < self::$default_expires) {
                return $_SESSION['web_access_token'];
            }
        }
        return null;
    }

    /**
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl) {
        $urlObj["appid"] = $this->appid;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
//        $urlObj["scope"] = "snsapi_base";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
        return $url;
    }

    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj) {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string code，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code) {
        $urlObj["appid"] = $this->appid;
        $urlObj["secret"] = $this->appsecret;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }

    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetFromMp($code) {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res, true);
        return $data;
    }

    /**
     *
     * 通过access_token openid 从工作平台获取UserInfo      
     * @return openid
     */
    public function getUserInfo($openid) {
        $web_access_token = $this->GetWebAccessToken();
        // 获取用户 信息
        $url = $this->__CreateOauthUrlForUserinfo($web_access_token, $openid);
        $ch = curl_init(); //初始化curl        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch); //运行curl，结果以jason形式返回            
        $data = json_decode($res, true); //取出openid access_token                
        curl_close($ch);      
        return $data;
    }

    /**
     *
     * 构造获取拉取用户信息(需scope为 snsapi_userinfo)的url地址     
     * @return 请求的url
     */
    private function __CreateOauthUrlForUserinfo($access_token, $openid) {
        $urlObj["access_token"] = $access_token;
        $urlObj["openid"] = $openid;
        $urlObj["lang"] = 'zh_CN';
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/userinfo?" . $bizString;
    }

}
