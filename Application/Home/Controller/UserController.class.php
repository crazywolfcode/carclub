<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * ===================================
 * Description of UserController File
 * @function:会员管理
 * @author: hacker-陈龙飞
 * @Date: 2017-1-16
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class UserController extends BaseController {

    private $currUser;
    private $isAdmin; //当前用户是否是超级管理员
    private $isTribuneManager; //当前用户是否是论版分类的管理员
    private $managerCate; //当前用户所管理的论版分类
    private $TribunePageSize = 8;
    private $CommentPageSize = 8;
    private $VideoPageSize = 8;
    private $ActionPageSize = 8;
    private $defaultGagDays = 3; //默认禁言的天数
    private $oneday = 86400; //一天的时间戳
    private $PageSize = 8;

    public function _initialize() {
        parent::_initialize();
        $this->currUser = session(C("HOME_SESSION_NAME"));
        if (empty($this->currUser)) {
            $this->redirect("Index/index");
        }
        if ($this->currUser['type'] == 2) {
            $this->isAdmin = isAdmin($this->currUser['id']);
        }
        if ($this->isAdmin) {
            $this->assign("isadmin", $this->isAdmin = 1);
        } else {
            $this->managerCate = isTribuneManager($this->currUser['id']);
            if ($this->managerCate) {
                $this->isTribuneManager = 1;
            } else {
                $this->isTribuneManager = 0;
            }
            $this->assign("cate", $this->managerCate);
            $this->assign("ismoderater", $this->isTribuneManager);
        }

        $this->userStatistics();
    }

    /**
     * 个人中心
     */
    public function index() {
        $this->assign("user", $this->currUser);
        $this->display();
    }

    /**
     * 完善信息
     * 加分未完成
     */
    public function finishInfo() {
        if (IS_AJAX) {
            $data = I("post.");
            $id = $data['id'];
            unset($data['id']);
            if (!$id) {
                $this->ajaxReturn(arrayRes(0, "保存失败"));
            }
            $res = M("user")->where("id =" . $id)->save($data);

            if ($res) {
                //保存成功更新 Session
                $this->currUser = $user = M("user")->where("id = " . $id)->find();
                session(C("HOME_SESSION_NAME"), $user);
                $this->ajaxReturn(arrayRes(1, "保存成功", U('User/index')));
                //Todo 加分
            } else {
                $this->ajaxReturn(arrayRes(0, "保存失败"));
            }
        } else {
            $user = session(C("HOME_SESSION_NAME"));
            $this->assign("user", $user);
            $this->display();
        }
    }

    /**
     * 修改密码
     */
    public function updatePwd() {
        if (IS_AJAX) {
            $user = session(C("HOME_SESSION_NAME"));
            $oldpwd = I("post.oldpwd");
            $tempOldPwd = en_password($oldpwd, $user['solt']);
            if ($tempOldPwd == $user['password']) {
                $newpwd = I("post.password");
                $data['password'] = en_password($newpwd, $user['solt']);
                $res = M('user')->where("id = " . $user['id'])->save($data);
                if ($res) {
                    $return = arrayRes(1, "修改成功");
                    $user['password'] = $data['password'];
                    session(C("HOME_SESSION_NAME"), $user);
                } else {
                    $return = arrayRes(0, "修改失败");
                }
            } else {
                $return = arrayRes(0, "密码不正确");
            }
            $this->ajaxReturn($return);
        } else {
            $this->display();
        }
    }

    public function userTopics() {
        $userId = I('get.user_id');
        $this->display();
    }

    /**
     * 我的文章 
     */
    public function mytribune() {
        $status = I('get.status', 1);
        $p = I('get.p', 1);
        $condition['t.status'] = $status;
        $condition['adduserid'] = $this->currUser['id'];
        $map['adduserid'] = $this->currUser['id'];
        $map['status'] = $status;
        $tribuneList = D('Common/tribune')->getTribuneList("addtime DESC", $p, $this->TribunePageSize, $condition);
        $count = M('tribune')->where($map)->count();
        parent::newpage($count, $this->TribunePageSize);
        $this->assign("tribunelist", $tribuneList);
        $this->assign("status", $status);
        $this->display();
    }

    /**
     * 我的评论 
     */
    public function mycomment() {
        $p = I('get.p', 1);
        $status = I('get.status', 1);
        $condition['userid'] = $this->currUser['id'];
        $condition['c.status'] = $status;
        $map['userid'] = $this->currUser['id'];
        $map['status'] = $status;
        $commentList = D('Common/comment')->getCommentList("addtime DESC", $p, $this->CommentPageSize, $condition);
        $count = M('comment')->where($map)->count();
        parent::newpage($count, $this->CommentPageSize);
        $this->assign("commentlist", $commentList);
        $this->assign("status", $status);
        $this->display();
    }

    /**
     * 收藏的文章
     * 类型 1活动 2视频 3文章4图集
     */
    public function collecttribune() {
        $type = 3;
        $p = I("get.p", 1);
        $collectList = $this->getMyCollect($p, $type, $this->TribunePageSize);
        $count = M('collect')->where("type = " . $type . " and userid = " . $this->currUser['id'] . " and isdelete = 0")->count();
        parent::newpage($count, $this->TribunePageSize);
        for ($i = 0; $i < $count; $i++) {
            $collectList[$i]['tribune'] = M('tribune')->where("id = " . $collectList[$i]['parentid'])->find();
        }
        $this->assign("collecttribune", $collectList);
        $this->display();
    }

    /**
     * 收藏的视频
     * 类型 1活动 2视频 3文章4图集
     */
    public function collectvideo() {
        $type = 2;
        $p = I("get.p", 1);
        $collectList = $this->getMyCollect($p, $type, $this->VideoPageSize);
        $count = M('collect')->where("type = " . $type . " and userid = " . $this->currUser['id'] . " and isdelete = 0")->count();
        parent::newpage($count, $this->TribunePageSize);
        for ($i = 0; $i < $count; $i++) {
            $collectList[$i]['video'] = M('video')->where("id = " . $collectList[$i]['parentid'] > 0 ? $collectList[$i]['parentid'] : 0)->find();
        }
        $this->assign("collectvideo", $collectList);
        $this->display();
    }

    /**
     * 收藏的图集
     * 类型 1活动 2视频 3文章 4图集
     */
    public function collectimglist() {
        $type = 4;
        $p = I("get.p", 1);
        $collectList = $this->getMyCollect($p, $type, $this->PageSize);
        $count = M('collect')->where("type = " . $type . " and userid = " . $this->currUser['id'] . " and isdelete = 0")->count();
        parent::newpage($count, $this->PageSize);
        $map['isdelete'] = 0;
        $map['isshow'] = 1;
        for ($i = 0; $i < $count; $i++) {
            $collectList[$i]['imglist'] = M('imglist')->where()->find();
            $map['_string'] = "list_id=" . $collectList[$i]['parentid'] . " or action_id =" . $collectList[$i]['parentid'];
            $collectList[$i]['imglist']['imgs'] = D('Common/images')->getImgLists($order = 'addtime DESC', $p, $this->PageSize, $map);
        }
        $this->assign("collectimglist", $collectList);
        $this->display();
    }

    /**
     * 收藏的活动
     * 类型 1活动 2视频 3文章4图集
     */
    public function collectaction() {
        $type = 1;
        $p = I("get.p", 1);
        $collectList = $this->getMyCollect($p, $type, $this->ActionPageSize);
        $count = M('collect')->where("type = " . $type . " and userid = " . $this->currUser['id'] . " and isdelete = 0")->count();
        parent::newpage($count, $this->ActionPageSize);
        for ($i = 0; $i < $count; $i++) {
            $collectList[$i]['action'] = M('action')->where("id = " . $collectList[$i]['parentid'])->find();
        }
        $this->assign("collectaction", $collectList);
        $this->display();
    }

    /**
     * 所管理分类下的文章
     * 如果是管理员，管理所有的文章
     */
    public function managertribune() {
        $cateid = I("get.cateid");
        $isadmin = I("get.isadmin", 0);
        $p = I("get.p", 1);
        if ($isadmin == 0) {
            $condition['cateid'] = $cateid;
        }
        $condition['isdelete'] = 0;
        $list = D("Common/tribune")->getTribuneList("addtime DESC ", $p, $this->TribunePageSize, $condition);
        $count = M("tribune")->where($condition)->count();
        parent::newpage($count, $this->TribunePageSize);
        $this->assign("tribunelist", $list);
        $this->display();
    }

    /**
     * 所管理分类下的文章
     * 如果是管理员，管理所有的文章
     */
    public function manageruser() {
        $cateid = I("get.cateid");
        $isadmin = I("get.isadmin", 0);
        $p = I("get.p", 1);
        if ($isadmin == 0) {
            $list = M('tribune')->where("id = " . $cateid)->field("adduserid as uid")->select();
            $count = count($list);
            if ($count == 0) {
                $condition['id'] = 0;
            } else {
                for ($i = 0; $i < $count; $i++) {
                    $ids[$i] = $list[$i]['uid'];
                }
                $condition['id'] = array("in", array_unique($ids));
            }
        } else {
            $condition['type'] = array('neq', 2);
        }
        $condition['isdelete'] = 0;
        $userlist = D("Common/user")->getUserList("last_login_time DESC ", $p, $this->TribunePageSize, $condition);
        $count = M("user")->where($condition)->count();
        parent::newpage($count, $this->TribunePageSize);
        $this->assign("userlist", $userlist);
        $this->assign("ismuser", 1);
        $this->display();
    }

    /**
     * 查看其它管理员
     */
    public function lookadmin() {
        $p = I("get.p", 1);
        $condition['isdelete'] = 0;
        $condition['type'] = 2;
        $condition['id'] = array('neq', $this->currUser['id']);
        $userlist = D("Common/user")->getUserList("last_login_time DESC ", $p, $this->PageSize, $condition);
        $count = M("user")->where($condition)->count();
        parent::newpage($count, $this->PageSize);
        $this->assign("userlist", $userlist);
        $this->display();
    }

    /**
     * 查看其它版主
     */
    public function lookmoderate() {
        $p = I("get.p", 1);
        $condition['isdelete'] = 0;
        $condition['type'] = 1;
        $condition['id'] = array('neq', $this->currUser['id']);
        $userlist = D("Common/user")->getUserList("last_login_time DESC ", $p, $this->PageSize, $condition);
        $count = M("user")->where($condition)->count();
        parent::newpage($count, $this->PageSize);
        $this->assign("userlist", $userlist);
        $this->assign("islookm", 1);
        $this->display();
    }

    /**
     * 会员的统计信息
     */
    private function userStatistics() {
        $user = $this->currUser;
        //my tribune show
        $statis['mytribuneshow'] = M('tribune')->where("adduserid =" . $user['id'] . " and isdelete = 0 and status =1")->count();
        //my tribune hide
        $statis['mytribunehide'] = M('tribune')->where("adduserid =" . $user['id'] . " and isdelete = 0 and status =0")->count();
        //my tribune 
        $statis['mytribune'] = $statis['mytribuneshow'] + $statis['mytribunehide'];

        //my comment show
        $statis['mycommentshow'] = M('comment')->where("userid =" . $user['id'] . " and isdelete = 0 and status =1")->count();
        //my comment hide
        $statis['mycommenthide'] = M('comment')->where("userid =" . $user['id'] . " and isdelete = 0 and status =0")->count();
        //my comment
        $statis['mycomment'] = $statis['mycommentshow'] + $statis['mycommenthide'];

        //collect action 1活动 2视频 3文章4图集
        $statis['collectaction'] = M('collect')->where("type =1 and isdelete = 0 and userid =" . $user['id'])->count();
        //collect video
        $statis['collectvideo'] = M('collect')->where("type =2 and isdelete = 0 and userid =" . $user['id'])->count();
        //collect tribune
        $statis['collecttribune'] = M('collect')->where("type =3 and isdelete = 0 and userid =" . $user['id'])->count();
        //collect imglist
        $statis['collectimglist'] = M('collect')->where("type =4 and isdelete = 0 and userid =" . $user['id'])->count();

        if ($this->isAdmin == 1) {
            //managet tribune
            $statis['managertribune'] = M('tribune')->where("isdelete = 0 and status =1")->count();
            //manager user 
            $statis['manageruser'] = M('tribune')->where(" isdelete = 0 and status =1")->group("adduserid")->count();
        } else if ($this->isTribuneManager) {
            //managet tribune
            $statis['managertribune'] = M('tribune')->where("cateid =" . $this->managerCate['id'] . " and isdelete = 0 and status =1")->count();

            //manager user 
            $statis['manageruser'] = M('tribune')->where("cateid =" . $this->managerCate['id'] . " and isdelete = 0 and status =1")->group("adduserid")->count();
        }
        $statis['admins'] = M('user')->where("type =2 ")->where(array("id" => array("neq", $this->currUser['id'])))->count();
        $statis['moderates'] = M('user')->where("type =1 ")->where(array("id" => array("neq", $this->currUser['id'])))->count();
        $this->assign("statis", $statis);
    }

    /**
     * 获取用户的收藏 
     * @param type $type
     * @return type
     */
    private function getMyCollect($p, $type, $pageSize) {
        $condition['type'] = $type;
        $condition['userid'] = $this->currUser['id'];
        $condition['isdelete'] = 0;
        $list = D("Common/collect")->getCollectList("addtime DESC", $p, $pageSize, $condition);
        return $list;
    }

    /**
     * 文章下面的评论
     */
    public function lookcomment() {
        $id = I("get.id");
        $p = I("get.p", 1);
        $status = I("get.status");
        $map['type'] = 1;
        $map['parentid'] = $id;
        $map['status'] = 1;
        $condition['c.type'] = 1;
        $condition['parentid'] = $id;
        $condition['c.status'] = 1;
        $count = M('comment')->where($map)->count();
        parent::newpage($count, $this->CommentPageSize);
        $commentList = D("Common/comment")->getCommentList(null, $p, $this->CommentPageSize, $condition);
        $this->assign("commentlist", $commentList);
        $this->assign("status", $status);
        $this->display();
    }

    /**
     * 减少积分 
     * todo 模板信息通知会员
     */
    public function reducepoint() {
        $point = I("post.point", 0);
        $userid = I("post.userid", 0);
        if ($userid == 0) {
            $this->ajaxReturn(arrayRes(0, "积分减少失败,数据异常"));
        }
        if (!is_numeric($point)) {
            $this->ajaxReturn(arrayRes(0, "输入错误"));
        }
        if ($point < 0) {
            $this->ajaxReturn(arrayRes(0, "输入错误"));
        }
        $res = M('user')->where("id =" . $userid)->setInc("point", $point);
        if ($res) {
            //todo
            $this->ajaxReturn(arrayRes(1, "积分减少成功"));
        } else {
            $this->ajaxReturn(arrayRes(0, "积分减少失败"));
        }
    }

}
