<?php

namespace Home\Controller;

/**
 * ===================================
 * Description of wonderfulVideoController File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-12
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class WonderfulVideoController extends BaseController {

    /**
     * 显示列表
     */
    public function videoList() {
        $video = D('Common/Video');
        $p = I('get.p') ? I('get.p', 1, 'intval') : I('post.p', 1, 'intval'); //页码
        $pagesize = 6; //每页6条
        $countRerult = $video->getVideoList(null, '-1');
        $count = $countRerult['data'] ? $countRerult['data'] : '0';
        $Page = new \Think\Page($count, $pagesize); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show = $Page->show(); // 分页显示输出
        $rerult = $video->getVideoList(null, $p, $pagesize, array('isdelete' => 0, 'isshow' => 1));
        $list = ($rerult['status'] == 1) ? $rerult['data'] : '';
        $pagetitle = "视频列表";
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign("pagetitle", $pagetitle);
        $this->display();
    }

    public function videoDetail() {

        $pagetitle = "播放精彩精彩";
        $id = I('get.id');
        $type = I('get.type', 1);
        $video = M('video')->find($id);
        //获取视频的评论
        $comentlist = D('Common/comment')->getComment(3, $id);
        if ($type == 1) {
            $sametitle = "同活动中的视频";
            $aid = $video['action_id'];
            if ($aid > 0) {
                //获取该活动下的视频
                $vlist = D('Common/video')->getActionvideos($aid);
                //如果该活动下没有上传视频，显示热门视频;
                if (!$vlist && count($vlist) == 0) {
                    $sametitle = "热门视频";
                    //获取热门视频
                    $vlist = D('video')->getHotVideos();
                }
            }
        } else {
            $sametitle = "热门视频";
            //获取热门视频
            $vlist = D('Common/video')->getHotVideos();
        }
        $UserInfo = session(C('HOME_SESSION_NAME'));
        //获取其它精彩视频
        $avlist = D('Common/video')->getVideoList($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array('isdelete' => 0, 'isshow' => 1, 'type' => 2));
        //浏览次数增加
        M('video')->where("id = " . $id)->setInc("views", 1);
        $this->assign("video", $video);
        $this->assign("pagetitle", $pagetitle);
        $this->assign("comentlist", $comentlist);
        $this->assign("sametitle", $sametitle);
        $this->assign("vlist", $vlist);
        $this->assign("avlist", $avlist['data']);
        $this->assign("user", $UserInfo);     //用户登录的信息  
        $this->assign("commentparent", $id); //为拉评论页面而设置，方便评论时候Parentid 好赋值
        $this->display();
    }

}
