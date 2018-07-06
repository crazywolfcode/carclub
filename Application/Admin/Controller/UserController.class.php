<?php

namespace Admin\Controller;

/**
 * Description of UserController
 *
 * @author Administrator
 */
class UserController extends BaseController {

    private $pageSize = 9;
    private $currPage = 1;

    public function _initialize() {
        parent::_initialize();
    }

    public function _empty() {
        $this->error('404');
    }

    /**
     * 会员管理 会员列表
     */
    public function userlist() {
        // $map['id'] = array(array('neq', 6), array('gt', 3), 'and');
        $point = I("get.pointline", 0);
        $uppoint = I("get.uppointline", 0);
        $p = I("get.p", 1);
        if ($uppoint == 0) {
            $where['point'] = array('egt', $point);
        } else {
            $where['point'] = array(array('egt', $point), array('lt', $uppoint), 'and');
        }
        //超级管理员和版主不在会员里面显示
        $where['type'] = array('eq', 0);
        $count = D('Common/user')->getCountByWhere($where);
        $this->newpage($count, $this->pageSize);
        $userList = D('Common/user')->getUserList($order = 'last_login_time DESC', $p, $this->pageSize, $where);
        $this->assign("userlist", $userList);
        $this->display();
    }

    /**
     * 设置或取消版主
     */
    public function setmoderater() {
        if (IS_AJAX) {
            $userid = I('post.userid');
            $type = I('post.type', 0);
            $cateid = I('post.cateid');
            //设置版主之前，先处理原来的版主
            if ($type != 0) {
                $bool = $this->handleOldModerate($cateid);
                if (!$bool) {
                    $this->ajaxReturn(arrayRes(0, "设置失败,先处理原来的版主失败"));
                }
            }
            if ($userid && $cateid) {
                if ($type == 0) {
                    $res = M('moderate')->where("userid =" . $userid . " and cateid =" . $cateid)->delete();
                } else {
                    $data['userid'] = $userid;
                    $data['cateid'] = $cateid;
                    $data['addtime'] = time();
                    $res = M('moderate')->add($data);
                }
                if ($res) {
                    //更新用户表
                    try {
                        if ($type == 0) {
                            M('user')->where("id = " . $userid)->save(array('type' => 0));
                        } else {
                            M('user')->where("id = " . $userid)->save(array('type' => 1));
                        }
                    } catch (Exception $exc) {
                        M('moderate')->delete($res);
                        M('user')->where("id =" . $userid)->save(array("type" => (1 - $type)));
                        $this->ajaxReturn(arrayRes(1, "设置失败，更新用户表时有错"));
                    }
                    $this->ajaxReturn(arrayRes(1, "设置成功"));
                } else {
                    $this->ajaxReturn(arrayRes(0, "设置失败"));
                }
            } else {
                $this->ajaxReturn(arrayRes(0, "设置失败，数据异常"));
            }
        }
    }

    /**
     * 设置或取消超级管理员
     */
    public function setadmin() {
        if (IS_AJAX) {
            $userid = I('post.userid');
            $type = I('post.type', 0);
            if (!$userid) {
                $this->ajaxReturn(arrayRes(0, "设置失败，数据异常"));
            }
            if ($type == 0) {
                $res = M('admin')->where("userid =" . $userid)->delete();
            } else {
                $data['userid'] = $userid;
                $data['addtime'] = time();
                $res = M('admin')->add($data);
            }
            if ($res) {
                //更新用户表  
                if ($type == 0) {
                    if ($this->isModerate($userid)) {
                        M('user')->where("id = " . $userid)->save(array('type' => 1));
                    } else {
                        M('user')->where("id = " . $userid)->save(array('type' => 0));
                    }
                } else {
                    M('user')->where("id = " . $userid)->save(array('type' => 2));
                }
                $this->ajaxReturn(arrayRes(1, "设置成功", null, $res));
            } else {
                $this->ajaxReturn(arrayRes(0, "设置失败"));
            }
        }
    }

    /**
     * 是否已经是版主
     * @param type $userid
     * @return boolean
     */
    private function isModerate($userid) {
        $res = M("moderate")->where("userid =" . $userid)->find();
        if ($res) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 是否已经是管理员
     * @param type $userid
     * @return boolean
     */
    private function isadmin($userid) {
        $res = M("admin")->where("userid =" . $userid)->find();
        if ($res) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 设置版主之前，先处理原来的版主
     * @param type $cateid
     * @return boolean
     */
    private function handleOldModerate($cateid) {
        $moderate = M('moderate')->where("cateid =" . $cateid)->find();
        if (!$moderate) {
            return TRUE; //表中没有数据，可以设置版主
        } else {
            //表中没有数据，先删除
            M('moderate')->delete($moderate['id']);
        }

        $userid = $moderate['userid'];
        if ($this->isadmin($userid)) {
            return TRUE; //是管理员，可以设置版主
        } else {
            //将原来的版主设置成普通会员
            $res = M('user')->where('id =' . $userid)->save(array('type' => 0));
            if ($res) {
                return TRUE;
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
        $res = M('user')->where("id =" . $userid)->setDec("point", $point);
        if ($res) {
            //todo
            $this->ajaxReturn(arrayRes(1, "积分减少成功"));
        } else {
            $this->ajaxReturn(arrayRes(0, "积分减少失败"));
        }
    }

    /*
     *  封号 解封 等处理操作
     * todo模板消息通知会员
     */

    public function lockuser() {
        if (IS_AJAX) {
            $data['status'] = $status = I("post.type");
            $text = I("post.text", "操作");
            $userid = I("post.userid");
            $days = I("post.days", $this->defaultGagDays);
            $date = time() + $days * $this->oneday;
            if ($data && $userid) {
                $res = M('user')->where("id = " . $userid)->save($data);
                if ($res) {
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
     * 放言 禁言 
     * todo模板消息通知会员
     * 用户状态；0未审核；1正常;2禁言3封号；
     */
    public function gag() {
        if (IS_AJAX) {
            $data['status'] = $status = I("post.type");
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

}
