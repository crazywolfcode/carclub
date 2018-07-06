<?php

namespace Admin\Controller;

/**
 * ===================================
 * Description of CommentCtoller File
 * @function: 评论管理
 * @author: hacker-陈龙飞
 * @Date: 2017-2-16
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class CommentController extends BaseController {

    private $CommentM;
    private $table = "comment";

    public function _initialize() {
        parent::_initialize();
        if (empty($this->CommentM)) {
            $this->CommentM = M($this->table);
        }
    }

    /**
     * 管理列表页面
     */
    public function manager() {
        $type = I('get.type', 1);
        $commentList = D("Common/comment")->adminGetComment($type);
        $this->assign("commentlist", $commentList);
        $this->display();
    }

    /**
     * 回复列表管理页面
     */
    public function replaymanager() {
        $parentid = I('get.id');
        $type = I('get.type');
        if ($parentid && $type) {
            $reList = D("Common/comment")->getCommentReplay($parentid, $type);
            $this->assign("relist", $reList);
        }
        $this->display();
    }

    /**
     * 修改评论和回复
     */
    public function update() {
        $id = I("post.id");
        $data['content'] = I("post.content");
        if ($id && $data) {
            $res = $this->CommentM->where("id =" . $id)->save($data);
            if ($res) {
                $return['status'] = 1;
                $return['msg'] = "修改成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "修改失败";
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "修改失败,数据异常";
        }

        $this->ajaxReturn($return);
    }

    /**
     * 不显示
     */
    public function hide() {
        $id = I("get.id");
        if ($id) {
            $condition['id'] = $id;
            $data['status'] = 0;
            $res = $this->CommentM->where($condition)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "操作成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "操作失败" . $this->CommentM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "操作失败";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 显示
     */
    public function show() {
        $id = I("get.id");
        if ($id) {
            $condition['id'] = $id;
            $data['status'] = 1;
            $res = $this->CommentM->where($condition)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "操作成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "操作失败" . $this->CommentM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "操作失败";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 删除
     */
    public function delete() {
        $id = I("get.id");
        $type = I("get.type");
        $isreplay = I("get.isreplay");
        if ($id) {
            $condition['id'] = $id;
            $res = $this->CommentM->where($condition)->delete();
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
                //删除其它下面的评论
                if ($isreplay == 0) {
                    $this->deletereplay($id, $type);
                }
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->CommentM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "删除失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 回复
     */
    public function repaly() {
        $data = I('post.');
        $user = session('admin');
        $data['userid'] = $user['id'];
        if ($admin['oauth'] == 1) {
            $data['addusername'] = $admin['wx_nick_name'];
        } elseif ($admin['oauth'] == 2) {
            $data['addusername'] = $admin['qq_nick_name'];
        } else {
            $data['addusername'] = $admin['name'];
        }
        $data['addtime'] = time();
        $data['status'] = 1;
        $res = $this->CommentM->add($data);
        if ($res > 0) {
            $return['status'] = 1;
            $return['msg'] = "回复成功";
        } else {
            $return['status'] = 0;
            $return['msg'] = "回复失败";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 删除评论的回复
     * @param type $parentid
     * @param type $type
     */
    private function deletereplay($parentid, $type) {
        $map['type'] = $type;
        $map['parentid'] = $parentid;
        $this->CommentM->where($map)->delete();
    }

}
