<?php

namespace Mobile\Controller;

/**
 * Description of UserController
 *
 * @author Hacker Sun
 * @date 2017-02-17 09:43:01
 * @copyright畅想IT工作室 
 * @version V1.0
 * @home https://my.oschina.net/sunhacker
 */
class UserController extends BaseController {

    private $currUser;
    private $isAdmin = false;
    private $isTribuneManager;
    private $myTribunePageSize = 6;
    private $myCommentPageSize = 6;
    private $myVideoPageSize = 8;
    private $defaultGagDays = 3; //默认禁言的天数
    private $oneday = 86400; //一天的时间戳
    private $pageSize = 6;

    public function _initialize() {
        parent::_initialize();
        if (!$this->currUser) {
            $this->currUser = session(C("HOME_SESSION_NAME"));
        }
        if (!$this->currUser) {
            $this->redirect("Login/login");
        }
        if ($this->currUser['type'] == 2) {
            $this->isAdmin = isAdmin($this->currUser['id']);
        }
        if ($this->isAdmin) {
            $this->assign("isadmin", 1);
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
    }

    public function index() {
        if(!is_login_mobile()){
            $this->redirect('Login/login');
        }
        $collectnum = M("collect")->where("userid =" . $this->currUser['id'])->count();
        $tribunenum = M("tribune")->where("adduserid =" . $this->currUser['id'])->count();
        $this->assign("collectnum", $collectnum);
        $this->assign("tribunenum", $tribunenum);
        $this->assign("topTwoTitle", "个人中心");
        $this->display();
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
            $this->assign("topTwoTitle", "修改密码");
            $this->display();
        }
    }

    /**
     * 查看其它管理员
     */
    public function lookadmin() {
        if (IS_AJAX) {
            $p = I("get.p", 1);
            $condition['isdelete'] = 0;
            $condition['type'] = 2;
            $pagesize = $this->pageSize / 2;
            $condition['id'] = array('neq', $this->currUser['id']);
            $userlist = D("Common/user")->getUserList("last_login_time DESC ", $p, $pagesize, $condition);
            $this->assign('userlist', $userlist);
            $html = $this->fetch("User:user-item2");
            $num = count($userlist);
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $p = I("get.p", 1);
            $condition['isdelete'] = 0;
            $condition['type'] = 2;
            $condition['id'] = array('neq', $this->currUser['id']);
            $userlist = D("Common/user")->getUserList("last_login_time DESC ", $p, $this->PageSize, $condition);
            $this->assign('userlist', $userlist);
            $this->assign("topTwoTitle", "其它管理员");
            $this->display();
        }
    }

    /**
     * 查看其它版主
     */
    public function lookmoderate() {
        if (IS_AJAX) {
            $p = I("get.p", 1);
            $condition['isdelete'] = 0;
            $condition['type'] = 1;
            $pagesize = $this->pageSize / 2;
            $condition['id'] = array('neq', $this->currUser['id']);
            $userlist = D("Common/user")->getUserList("last_login_time DESC ", $p, $pagesize, $condition);
            $this->assign("islookm", 1);
            $this->assign('userlist', $userlist);
            $html = $this->fetch("User:user_item2");
            $num = count($userlist);
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $p = I("get.p", 1);
            $condition['isdelete'] = 0;
            $condition['type'] = 1;
            $condition['id'] = array('neq', $this->currUser['id']);
            $userlist = D("Common/user")->getUserList("last_login_time DESC ", $p, $this->PageSize, $condition);
            $this->assign('userlist', $userlist);
            $this->assign("islookm", 1);
            $this->assign("topTwoTitle", "其它版主");
            $this->display();
        }
    }

    /**
     * 基本信息
     */
    public function info() {
        $this->assign("user", $this->currUser);
        $this->assign("topTwoTitle", "个人信息");
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
                $this->ajaxReturn(arrayRes(1, "保存成功"));
                //Todo 加分
            } else {
                $this->ajaxReturn(arrayRes(0, "保存失败"));
            }
        } else {
            $user = session(C("HOME_SESSION_NAME"));
            $this->assign("user", $user);
            $this->assign("topTwoTitle", "完善信息");
            $this->display();
        }
    }

    /**
     * 修改头像
     * 未完成
     */
    public function updatehead() {
        if (IS_AJAX) {
            $id = I('post.id');
            $data['head'] = I('post.head');
            if ($id && $data) {
                $res = M('')->where("id = " . $id)->save($data);
                if ($res) {
                    $rerurn = arrayRes(1, "修改成功");
                } else {
                    $rerurn = arrayRes(0, "修改修改");
                }
            } else {
                $return = arrayRes(0, "数据异常");
            }
            $this->ajaxReturn($return);
        } else {
            $user = session(C('HOME_SESSION_NAME'));
            if (!$user) {
                $this->redirect("Login/login");
            }
            $this->assign("user", $user);
            $this->assign("topTwoTitle", "个人信息");
            $this->display();
        }
    }

    public function mytribune() {
        if (IS_AJAX) {
            $p = I("get.p", 1);
            $pagesize = $this->myTribunePageSize / 2;
            $where['adduserid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $list = D("Common/tribune")->getTribuneList("addtime DESC", $p, $pagesize, $where);
            $this->assign('tribunelist', $list);
            $html = $this->fetch("User:my_tribune_item");
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            $num = count($list);
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $where['adduserid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $list = D("Common/tribune")->getTribuneList("addtime DESC", 1, $this->myTribunePageSize, $where);
            $this->assign('tribunelist', $list);
            $this->assign("topTwoTitle", "我的文章");
            $this->display();
        }
    }

    public function mycomment() {
        if (IS_AJAX) {
            $p = I("get.p", 1);
            $pagesize = $this->myCommentPageSize / 2;
            $where['userid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $list = D("Common/comment")->getCommentList(null, $p, $pagesize, $where);
            $this->assign('comentlist', $list);
            $html = $this->fetch("User:my_comment_item");
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            $num = count($list);
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $where['userid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $list = D("Common/comment")->getCommentList($order = 'addtime DESC', 1, $this->myCommentPageSize, $where);
            $this->assign('comentlist', $list);
            $this->assign("topTwoTitle", "我的评论");
            $this->display();
        }
    }

    /**
     * 会员所收藏的视频
     */
    public function myvideo() {
        if (IS_AJAX) {
            $p = I("get.p", 1);
            $pagesize = $this->myVideoPageSize / 2;
            $where['userid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $where['type'] = 2;
            $list = D("Common/collect")->getCollectList(null, $p, $pagesize, $where);
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['video'] = M('video')->where("id =" . $list[$i]['parentid'])->find();
            }
            $this->assign('videolist', $list);
            $html = $this->fetch("User:my_video_item");
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            $num = count($list);
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $where['userid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $where['type'] = 2;
            $list = D("Common/collect")->getCollectList(null, 1, $this->myVideoPageSize, $where);
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['video'] = M('video')->where("id =" . $list[$i]['parentid'])->find();
            }
            $this->assign('videolist', $list);
            $this->assign("topTwoTitle", "我收藏的视频");
            $this->display();
        }
    }

    /**
     * 所管论版下面的所有文章
     */
    public function managertribuner() {
        $cateid = I("get.cateid");
        $catename = I("get.catename");
        $where['t.status'] = 1;
        $where['isdelete'] = 0;
        $isadmin = I("get.isadmin", 0);
        if ($isadmin == 0) {
            $where['cateid'] = $cateid;
        }
        if (IS_AJAX) {
            $p = I("get.p");
            $pagesize = $this->myTribunePageSize / 2;
            $list = D("Common/tribune")->getTribuneList(null, $p, $pagesize, $where);
            $this->assign('tribunelist', $list);
            $html = $this->fetch("User:my_tribune_item");
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            $num = count($list);
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            if ($isadmin == 1) {
                $this->assign("topTwoTitle", "所有文章");
            } else {
                $this->assign("topTwoTitle", "{$catename}板块的文章");
            }
            $tribunelist = D("Common/tribune")->getTribuneList(null, 1, $this->myTribunePageSize, $where);
            $this->assign('tribunelist', $tribunelist);
            $this->assign('catename', $catename);
            $this->display();
        }
    }

    /**
     * 管理会员
     */
    public function manageruser() {
        $cateid = I("get.cateid");
        $where['cateid'] = $cateid;
        $catename = I("get.catename");
        $isadmin = I("get.isadmin", 0);
        if (IS_AJAX) {
            $p = I("get.p");
            $pagesize = $this->pageSize / 2;
            $userlist = $this->getData($cateid, $p, $pagesize, $isadmin);
            $this->assign('userlist', $userlist);
            $html = $this->fetch("User:user_item");
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            $num = count($userlist);
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $userlist = $this->getData($cateid, 1, $this->pageSize, $isadmin);
            $this->assign('userlist', $userlist);
            if ($isadmin == 1) {
                $this->assign("topTwoTitle", "所有会员");
            } else {
                $this->assign("topTwoTitle", "{$catename}相关的会员");
            }
            $this->display();
        }
    }

    /*
     * 放言 禁言 封号 解封 等处理操作
     * todo模板消息通知会员
     */

    public function handle() {
        if (IS_AJAX) {
            $data['status'] = $status = I("post.status");
            $text = I("post.text", "操作");
            $userid = I("post.userid");
            $days = I("post.days", $this->defaultGagDays);
            $date = time() + $days * $this->oneday;
            if ($data && $userid) {
                $res = M('user')->where("id = " . $userid)->save($data);
                if ($res) {
                    //禁言表的更新 
                    if ($status == 1 || $status == 2) {
                        try {
                            updateUserGag($status, $userid, $this->currUser['id'], $date, $days);
                        } catch (Exception $exc) {
                            
                        }
                    }
                    // todo 模板消息通知会员
                    $this->ajaxReturn(arrayRes(1, "{$text}成功"));
                } else {
                    $this->ajaxReturn(arrayRes(0, "{$text}失败"));
                }
            } else {
                $this->ajaxReturn(arrayRes(0, "{$text}失败，数据异常"));
            }
        }
    }

    /**
     * 增加积分 
     * todo 模板信息通知会员
     */
    public function addpoint() {
        $point = I("post.point", 0);
        $userid = I("post.userid", 0);
        if ($userid == 0) {
            $this->ajaxReturn(arrayRes(0, "积分增加失败,数据异常"));
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
            $this->ajaxReturn(arrayRes(1, "积分增加成功"));
        } else {
            $this->ajaxReturn(arrayRes(0, "积分增加失败"));
        }
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

    /**
     * 获取数据为拉少写而特意封装的
     * @param type $cateid
     * @param type $p
     */
    private function getData($cateid, $p = 1, $pageSize = 6, $isadmin = 0) {
        if ($isadmin == 0) {
            //1.先得到该分类下的得到发表过文章的会员 id
            $list = M('tribune')->where("cateid =" . $cateid)->field("adduserid")->group("adduserid")->select();
            for ($i = 0; $i < count($list); $i++) {
                $userids[$i] = $list[$i]['adduserid'];
            }
            //2.得到评论过的会员id
            $listTwo = M('comment')->field("userid")->group("userid")->select();
            for ($i = 0; $i < count($listTwo); $i++) {
                $useridsTwo[$i] = $listTwo[$i]['userid'];
            }
            //3.合并两处的会员id
            $uids = array_merge($userids, $useridsTwo);
            //4. 去重 和去除自己;
            $idarray = array_unique($uids);
            foreach ($idarray as $key => $value) {
                if ($value === $this->currUser['id']) {
                    unset($idarray[$key]);
                    break;
                }
            }
            //5.获取会员信息
            $condition['id'] = array("in", $idarray);
        }
        //超级管理员不在会员里面显示
        $condition['type'] = array('eq', 0);
        $userlist = D('Common/user')->getUserList("point DESC", $p, $pageSize, $condition);
        return $userlist;
    }

    /**
     * 会员所收藏的论版文章
     */
    public function myliketribune() {
        if (IS_AJAX) {
            $p = I("get.p", 1);
            $pagesize = $this->myTribunePageSize / 2;
            $where['userid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $where['type'] = 3;
            $list = D("Common/collect")->getCollectList(null, $p, $pagesize, $where);
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['tribune'] = M('tribune')->where("id =" . $list[$i]['parentid'])->find();
            }
            $this->assign('tribunelist', $list);
            $html = $this->fetch("User:my_collect_tribune_item");
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            $num = count($list);
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $where['userid'] = $this->currUser['id'];
            $where['isdelete'] = 0;
            $where['type'] = 3;
            $list = D("Common/collect")->getCollectList(null, 1, 8, $where);
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['tribune'] = M('tribune')->where("id =" . $list[$i]['parentid'])->find();
            }
            $this->assign('tribunelist', $list);
            $this->assign("topTwoTitle", "我收藏的文章");
            $this->display();
        }
    }

    /**
     * 我的文章和我所管理的文章下面的评论和回复
     */
    public function managercomment() {
        if (IS_AJAX) {
            $p = I("get.p", 1);
            $pagesize = $this->myCommentPageSize / 2;
            $where['parentid'] = I("get.id");
            $where['c.type'] = 1;
            $where['isdelete'] = 0;
            $list = D("Common/comment")->getTribuneList(null, $p, $pagesize, $where);
            $this->assign('comentlist', $list);
            $html = $this->fetch("User:my_manager_comment_item");
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            $num = count($list);
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $pagesize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        } else {
            $where['parentid'] = I("get.id");
            $where['c.type'] = 1;
            $where['isdelete'] = 0;
            $list = D("Common/comment")->getCommentList($order = 'addtime DESC', 1, 9, $where);
            $this->assign('comentlist', $list);
            $this->assign("topTwoTitle", "文章下的评论");
            $this->display();
        }
    }

    /**
     * 显示会员发布文章界面
     */
    public function addtribune() {
        $cateid = I("get.cateid");
        $this->assign("currcateid", $cateid);
        $this->display();
    }
}
