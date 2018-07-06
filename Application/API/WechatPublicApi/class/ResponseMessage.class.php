<?php

/**
 * Description of ResponseMessage
 * 被动推送消息 
 * @author Crazycode
 * @date 2017-04-18 10:27:48
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class ResponseMessage {

    //文本消息
    private $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime><![CDATA[%s]]></CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";

    public function buildeTextTpl($postObj, $content) {
        $resultStr = sprintf($this->textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $content);
        return $resultStr;
    }

    private $imgTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime><![CDATA[%s]]></CreateTime>
                                <MsgType><![CDATA[image]]></MsgType>
                                <Image>
                                <MediaId><![CDATA[%s]]></MediaId>
                                </Image>
                                </xml>";

    public function buildeImageTpl($postObj, $mediaid) {
        $resultStr = sprintf($this->imgTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $mediaid);
        return $resultStr;
    }

    private $voiceTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime><![CDATA[%s]]></CreateTime>
                                <MsgType><![CDATA[voice]]></MsgType>
                                <Image>
                                <MediaId><![CDATA[%s]]></MediaId>
                                </Image>
                                </xml>";

    public function buildeVoiceTpl($postObj, $mediaid) {
        $resultStr = sprintf($this->voiceTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $mediaid);
        return $resultStr;
    }

    private $videoTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime><![CDATA[%s]]></CreateTime>
                                <MsgType><![CDATA[video]]></MsgType>
                                <Video>
                                <MediaId><![CDATA[%s]]></MediaId>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                </Video> 
                                </xml>";

    public function buildeVideoTpl($postObj, $arraydata) {
        $resultStr = sprintf($this->voiceTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $mediaid = $arraydata['mediaid'], $title = $arraydata['title'], $description = $arraydata['description']
        );
        return $resultStr;
    }

    public function buildeNewTpl($postObj, $arraydata, $ismore = false) {
        if (!$postObj || $arraydata) {
            return "";
        }
        if ($ismore) {
            $num = count($arraydata);
        } else {
            $num = 1;
        }
        $tpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime><![CDATA[%s]]></CreateTime>
                                <MsgType><![CDATA[news]]></MsgType>
                                <ArticleCount><![CDATA[%s]]></ArticleCount>
                                <Articles>";
        if ($ismore) {
            foreach ($arraydata as $v) {
                $item .= " <item>
                            <Title><![CDATA[" . $v['title'] . "]]></Title>
                            <Description><![CDATA[" . $v['desc'] . "]]></Description>
                            <PicUrl><![CDATA[" . $v['pic'] . "]]></PicUrl>
                            <Url><![CDATA[" . $v['url'] . "]]></Url>
                            </item>";
            }
        } else {
            $item = "<item>
                     <Title><![CDATA[" . $arraydata['title'] . "]]></Title>
                     <Description><![CDATA[" . $arraydata['desc'] . "]]></Description>
                     <PicUrl><![CDATA[" . $arraydata['pic'] . "]]></PicUrl>
                     <Url><![CDATA[" . $arraydata['url'] . "]]></Url>
                     </item>";
        }
        $tpl .= $item . " </Articles>
                            </xml>";
        $resultStr = sprintf($tpl, $postObj->FromUserName, $postObj->ToUserName, time(), $num);
        return $resultStr;
    }

    /**
     * 推送消息  
     */
    public function response($tpl) {
        exit($tpl);
    }

}
