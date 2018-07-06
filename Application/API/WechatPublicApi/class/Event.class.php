<?php

require_once (WP_API_PATH . "class/ResponseMessage.class.php");

/**
 * Description of Event
 * 处理接收到事件推送
 * @author Crazycode
 * @date 2017-04-18 10:22:12
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class Event {

    private $EVENT_TYPE_subscribe = "subscribe"; //关注事件
    private $EVENT_TYPE_unsubscribe = "unsubscribe"; //取消关注事件
    private $EVENT_TYPE_SCAN = "SCAN"; //扫描带参数二维码事件
    private $EVENT_TYPE_LOCATION = "LOCATION"; //上报地理位置事件
    private $EVENT_TYPE_CLICK = "CLICK"; //点击菜单拉取消息时的事件
    private $EVENT_TYPE_VIEW = "VIEW"; //点击菜单跳转链接时的事件

    public function handleEvent($postObj) {
        $event = $postObj->Event;
        switch ($event) {
            case $this->EVENT_TYPE_subscribe:              
                $Response = new ResponseMessage();               
                $tpl = $Response->buildeTextTpl($postObj, $content ="think you subscribe my public!");              
                $Response->response($tpl);              
                break;
            case $this->EVENT_TYPE_unsubscribe:
                Think\Log::write("------------------>用户：{$postObj->FromUserName} 取消拉观注！");
                break;
            case $this->EVENT_TYPE_SCAN:
                Think\Log::write("------------------>用户：{$postObj->FromUserName} 扫码拉！");
                break;
            case $this->EVENT_TYPE_LOCATION:
                Think\Log::write("------------------>用户：{$postObj->FromUserName} 上报拉地理公位置！");
                break;
            case $this->EVENT_TYPE_CLICK:
                Think\Log::write("------------------>用户：{$postObj->FromUserName} $this->EVENT_TYPE_CLICK！");
                break;
            case $this->EVENT_TYPE_VIEW:
                Think\Log::write("------------------>用户：{$postObj->FromUserName} $this->EVENT_TYPE_VIEW！");
                break;
            default:
                break;
        }
    }

}
