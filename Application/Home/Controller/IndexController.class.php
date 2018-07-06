<?php

namespace Home\Controller;

class IndexController extends BaseController {

    public function testphpinfor() {
        phpinfo();
    }

    public function index() {
        //俱乐部简介
        $info = M('CarclubInfo');
        $carClub = $info->find(); //虽然只有一条，但是防止后面有扩展      
        $this->assign('content', $carClub['brief']);

        //随机的精彩图片10张
        $imglist = M('images')->where('isdelete = 0 and isshow = 1')->order(" rand() ")->limit(10)->select();
        $this->assign("imglist", $imglist);
        //随机的精彩视频10个
        $videolist = M('video')->where('isdelete = 0 and isshow = 1')->order(" rand() ")->limit(10)->select();
        $this->assign("videolist", $videolist);

        //历史活动6个，线束时间的DESC
        $actionlist = M('action')->where("isdelete = 0 and isshow = 1")->order("endtime DESC")->limit(6)->select();

        //活动中的图片//活动中的视频

        for ($i = 0; $i < count($actionlist); $i++) {
            $actionlist[$i]['imgs'] = D('Common/images')->getActionimgs($actionlist[$i]['id']);
            $actionlist[$i]['videos'] = D('Common/video')->getActionvideos($actionlist[$i]['id']);
        }
        $this->assign("actionlist", $actionlist);
        //首页显示的会员
        //首页热门话题
        $cate = D("common/category")->getCategorys();
        $topicList = array();
        for ($i = 0; $i < count($cate); $i++) {
            $where = array('t.cateid' => $cate[$i]['id'], 'isdelete' => 0, 't.status' => 1);
            $TemptopicList = D("Common/tribune")->getTribuneListWitchUser($order = 'addtime DESC', $p = 1, $pagesize = 6, $where);
            $topicList = array_merge($topicList, $TemptopicList);
        }
        $this->assign("topiclist", $topicList);
        //最新会员20个
        $userlist = M('user')->where(array('type' => array('neq', 3), 'status' => 1))->order("reg_time DESC")->limit(20)->select();
        $this->assign("userlist", $userlist);
        $this->display();
    }

    /**
     * 分享成功时按条件加分
     * @return type
     */
    public function sharesuccess() {
        $type = I('post.type');
        if (!$type) {
            return;
        }
        $point = 0;
        switch ($type) {
            case 1:
                $point = C('share_timeline_SEND_POINT');
                break;
            case 2:
                $point = C('share_qqzone_SEND_POINT');
                break;
            case 3:
                break;
            case 4:
                break;
        }
        if ($point > 0) {
            $map['id'] = session(C('HOME_SESSION_NAME'))['id'];
            try {
                M('user')->where($map)->setInc("point", $point);
            } catch (Exception $ex) {
                
            }
        }
    }

}
