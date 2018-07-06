<?php

require_once "Http.class.php";

class JSSDK {

    private static $appId;
    private static $appSecret;

    public function __construct($appId, $appSecret) {
        self::$appId = $appId;
        self::$appSecret = $appSecret;
    }

    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        // 注意 URL 一定要动态获取
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId" => self::$appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {

        if (session('jsapiTicket') && session('ticket_expire_time') > time()) {
            return session('jsapiTicket');
        }
        $accessToken = $this->getAccessToken();
        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $res = json_decode(Http::httpRequest($url, "get"));
        $ticket = $res->ticket;
        if ($ticket) {
            session('jsapiTicket', $ticket);
            session('ticket_expire_time', time() + 7000);
        }
        return session('jsapiTicket');
    }

    private function getAccessToken() {
        if (session('accessToken') && session('token_expire_time') > time()) {
            return session('accessToken');
        }
        // 如果是企业号用以下URL获取access_token
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::$appId . "&secret=" . self::$appSecret;
        $res = json_decode(Http::httpRequest($url, "get"));
        $access_token = $res->access_token;
        if ($access_token) {
            session('accessToken', $access_token);
            session('token_expire_time' . time() + 7000);
        }
        return session('accessToken');
    }

}
