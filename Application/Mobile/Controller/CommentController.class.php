<?php

namespace Mobile\Controller;

/**
 * ===================================
 * Description of CommentController File
 * @function:评论管理
 * @author: hacker-陈龙飞
 * @Date: 2017-2-15
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class CommentController extends BaseController {

    private $CommentAjaxPageSize = 4;

    /**
     * 增加评论
     */
    public function addComment() {
        $returndata = $data = I('post.');
        $Model;
        $type;
//        if(session(C("HOME_SESSION_NAME"))['id'] == $data['userid']){
//            $this->ajaxReturn(arrayRes(0, '自己不可评论自己的文章'));
//        }
        //$type 1关于论版文章 2 关于图集 3关于视频 4关于活动 5关于其它
        switch ($data['controller']) {
            //视频
            case "WonderfulVideo":
                $Model = M('video');
                $type = 3;
                break;
            //图集
            case "WonderfulImg":
                $Model = M('imglist');
                $type = I("post.type", 2);
                break;
            //活动
            case "Action":
                $Model = M('Action');
                $type = 4;
                break;
            //论版
            case "Tribune":
                $Model = M('Tribune');
                $type = 1;
                break;
        }

        unset($data['controller']);
        unset($data['userhead']);
        $data['addtime'] = time();
        $data['status'] = 1;
        $data['type'] = $type;
        $res = M('comment')->add($data);
        if ($res) {
            try {
                //1.增加评论者的积分
                addUserPoint($data['userid'], C("COMMENT_POINT"));
                //2.增加被评论的内容的评价次数
                $Model->where("id = " . $data['parentid'])->setInc("comments", 1);
            } catch (Exception $exc) {
                
            }
            //3.返回本次评论的内容
            $this->assign("c", $returndata);
            $html = $this->fetch("Comment:comment_ajax_item");
            $return = arrayRes(1, "成功", null, $html);
            $this->ajaxReturn($return);
        }
    }

    /**
     * 评论回复
     */
    public function replay() {
        $returndata = $data = I('post.');
//        if(session(C("HOME_SESSION_NAME"))['id'] == $data['userid']){
//            $this->ajaxReturn(arrayRes(0, '自己不可评论自己的文章'));
//        }
        //$type 1关于论版文章 2 关于图集 3关于视频 4关于活动 5关于活动中的图集   
        unset($data['uhead']);
        $data['addtime'] = time();
        $data['status'] = 1;
        $res = M('comment')->add($data);
        if ($res) {
            //3.返回本次评论的内容
            $returndata['addtime'] = $data['addtime'];
            $returndata['id'] = $res;
            $this->assign("r", $returndata);
            $html = $this->fetch("Comment:comment_replay_item");
            $this->ajaxReturn(arrayRes(1, "成功", null, $html));
        } else {
            $this->ajaxReturn(arrayRes(1, "回复成功"));
        }
    }

    /*
     * 设置评论是不否有用
     * 不返回数据
     */

    public function setuseful() {
        if (IS_AJAX) {
            $id = I('post.id');
            $num = I('post.num');
            $authorid = I('post.authorid', 0);
            if ($id && $num) {
                $res = M('comment')->where("id  = " . $id)->setInc("usefuls", $num);
                if ($res && $authorid > 0) {
                    //为发布者加积分
                    try {
                        $num > 0 ? $point = C("TOPIC_HAS_USER") : $point = 0 - C("TOPIC_HAS_USER");
                        addUserPoint($authorid, $point);
                    } catch (Exception $exc) {
                        
                    }
                }
            }
        }
    }

    /**
     * 删除评论
     */
    public function delete() {
        if (IS_AJAX) {
            $id = I("get.id");
            $isreplay = I("get.isreplay", 0);
            if (!$id) {
                $this->ajaxReturn(arrayRes(0, "删除失败，数据异常"));
            }
            $res = M('comment')->where("id = " . $id)->delete();
            if ($res) {
                if ($isreplay == 1) {
                    try {
                        $this->deleteReplay($id);
                    } catch (Exception $exc) {
                        
                    }
                }
                $this->ajaxReturn(arrayRes(1, "删除成功"));
            } else {
                $this->ajaxReturn(arrayRes(0, "删除失败"));
            }
        }
    }

    /**
     * 删除评论的回复 
     * @param type $id 
     */
    private function deleteReplay($id) {
        M('comment')->where("parentid =" . $id . " and isreplay=1")->delete();
    }

    /**
     * 前台显示,也就是公开
     *  前台不显示，不开公，自己可以见
     */
    public function showorhide() {
        if (IS_AJAX) {
            $id = I('post.id');
            $data['status'] = I('post.type');
            $res = M('comment')->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败");
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 修改
     */
    public function update() {
        $id = I("post.id");
        $data['content'] = I("post.content");
        if ($id && $data) {
            $res = M('comment')->where("id =" . $id)->save($data);
            if ($res) {
                $this->ajaxReturn(arrayRes(1, "修改成功"));
            } else {
                $this->ajaxReturn(arrayRes(0, "修改失败"));
            }
        } else {
            $this->ajaxReturn(arrayRes(0, "修改失败，数据异常"));
        }
    }

    //评论的类型 1关于论坛文章 2 关于图集 3关于视频 4关于活动 5活动中的图片集合
    public function ajaxGetComment() {
        if (IS_AJAX) {
            $p = I('get.p');
            $type = I('get.type');
            $parentid = I('get.parentid');
            $commentList = D('Common/comment')->getComment($type, $parentid, $p, $pagesize = $this->CommentAjaxPageSize);
            $this->assign("commentlist", $commentList);
            $html = $this->fetch("Comment:comment_item");
            $num = count($commentList);
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $this->CommentAjaxPageSize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        }
    }

}
