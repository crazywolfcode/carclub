<?php

/**
 * 微信交互类
 */

namespace Home\Controller;

require_once(WP_API_PATH . "/class/Event.class.php");

class WeixinController extends BaseController {

    public $client;

    public function _initialize() {
        parent::_initialize();
        //获取微信配置信息
//        $wechat_list = M('wx_user')->select();
//        $wechat_config = $wechat_list[0];
//        $options = array(
//            'token' => $wechat_config['w_token'], //填写你设定的key
//            'encodingaeskey' => $wechat_config['aeskey'], //填写加密用的EncodingAESKey
//            'appid' => $wechat_config['appid'], //填写高级调用功能的app id
//            'appsecret' => $wechat_config['appsecret'], //填写高级调用功能的密钥
//        );
    }

    public function oauth() {
        
    }

    public function index() {
        $echostr = $_GET['echostr'];
        if ($echostr) {
            exit($echostr);
        } else {
            $this->handelmessage();
        }
    }

    public function handelmessage() {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //extract post data
        if (empty($postStr)) {
            exit("");
        }
        /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
          the best way is to check the validity of xml by yourself */
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);     
        $keyword = trim($postObj->Content);
        $msgType = trim($postObj->MsgType);
        if ($msgType) {
            $Event = new \Event();
            $Event->handleEvent($postObj);
            exit();
        }
        if (empty($keyword)) {
            exit("Input something...");
        }
        \Think\Log::write("---------------->".$keyword);
    }

}
