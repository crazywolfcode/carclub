<?php

namespace Mobile\Controller;

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

    private $pageSize = 8; //分页显示的条数
    private $ajaxPageSize = 4;
    private $currPage = 1; //当前的页码默认为第一页

    /**
     * 显示列表
     */

    public function videoList() {        
        $this->currPage = I('get.p') ? I('get.p', 1, 'intval') : I('post.p', 1, 'intval');
        $rerult = D('Common/Video')->getVideoList(null, $this->currPage, $this->pageSize, array('isdelete' => 0, 'isshow' => 1));
        $videoList = ($rerult['status'] == 1) ? $rerult['data'] : '';
        $this->assign('videolist', $videoList);
        $this->assign('currpage', $this->currPage);

        $this->display();
    }

    /**
     * 手下下拉加载
     */
    public function ajaxGetMore() {
        if (IS_AJAX) {
            $this->currPage = I('get.p') ? I('get.p', 1, 'intval') : I('post.p', 1, 'intval');
            $rerult = D('Common/Video')->getVideoList(null, $this->currPage, $this->ajaxPageSize, array('isdelete' => 0, 'isshow' => 1));
            $videoList = ($rerult['status'] == 1) ? $rerult['data'] : '';
            $this->assign('videolist', $videoList);
            $html = $this->fetch("WonderfulVideo:listItem");
            $num = count($videoList);
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $this->ajaxPageSize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        }
    }

    public function videoDetail() {
        $id = I('get.id');
        $aid = I('get.aid', 0);
        $type = I('get.type', 1);
        //增加查看次数
        M('video')->where("id = " . $id)->setInc('views', 1);

        $video = M('video')->find($id);
        //获取视频的评论
        $comentlist = D('Common/comment')->getComment(3, $id);
        if ($aid > 0) {
            $map['type'] = $type;
            $map['action_id'] = $aid;
            $map['_logic'] = "or";
            $resoult = D('Common/video')->getVideoList('addtime DESC,rand()', 1, 6, $map);
            $sameList = ($resoult['status'] == 1) ? $resoult['data'] : '';
            $ortherLiset = D('Common/video')->getVideosbyType($order = "rand()", $type);
        } else {
            $sameList = D('Common/video')->getVideosbyType($order = "rand()", 2);
            $ortherLiset = D('Common/video')->getVideosbyType($order = "rand()", $type);
        }
        $this->assign("video", $video);
        $this->assign("commentlist", $comentlist);
        $this->assign("samelist", $sameList);
        $this->assign("ortherlist", $ortherLiset);
        $this->assign("topTwoTitle", "视频播放");
        $this->assign("commentparent", $id); //为拉评论页面而设置，方便评论时候Parentid 好赋值
        $this->display();
    }

}
