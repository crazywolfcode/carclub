<?php

namespace Admin\Controller;

/**
 * ===================================
 * Description of ActionController File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-19
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class ActionController extends BaseController {

//put your code here
    private $ActionM;
    private $table;

    public function _initialize() {
        parent::_initialize();
        $this->table = "action";
        $this->ActionM = M($this->table);
        $this->autoStart();
    }

    /**
     * 管理页面 
     */
    public function manager() {
        $status = I('get.status');
        $isdelete = I('get.isdelete', 0);
        $condition['status'] = $status;
        $condition['isdelete'] = $isdelete;
        if ($isdelete == 1) {
            unset($condition['status']);
        }
        $actionList = $this->ActionM->where($condition)->order('addtime DESC')->select();
        $this->assign("actionlist", $actionList);
        $this->display();
    }

    /**
     * 详细
     */
    public function detail() {
        $id = I('get.id');
        $action = $this->ActionM->find($id);
        //获取活动的图片
        $actionImgs = M('images')->where("action_id = " . $id)->order("addtime ASC")->select();
        //取活动的视频
        $actionVideos = M('video')->where("action_id = " . $id)->order("addtime ASC")->select();
        //取活动的评论
        $actionComments = M('comment')->where("type = 1")->where("parentid = " . $id)->order("addtime ASC")->select();

        $this->assign("action", $action);
        $this->assign("actionimgs", $actionImgs);
        $this->assign("actionvideos", $actionVideos);
        $this->assign("actioncomments", $actionComments);
        $this->display();
    }

    /**
     * 新增 
     */
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['addtime'] = time();
            $data['isshow'] = 1;
            $admin = session('admin');
            $data['adduserid'] = $admin['id'];
            if ($admin['oauth'] == 1) {
                $data['addusername'] = $admin['wx_nick_name'];
            } elseif ($admin['oauth'] == 2) {
                $data['addusername'] = $admin['qq_nick_name'];
            } else {
                $data['addusername'] = $admin['name'];
            }
            $res = $this->ActionM->add($data);
            if ($res) {
                $return = arrayRes(1, "成功", U('manager', array('status' => 0)));
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ActionM->getError());
                $this->ajaxReturn($return);
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改 
     */
    public function update() {
        if (IS_AJAX) {
            //POST 过来的数据必须要有ID
            $data = I('post.');
            $res = $this->ActionM->where("id = " . $data['id'])->save($data);
            if ($res) {
                $return = arrayRes(1, "成功", U('manager', array('status' => $data['status'])));
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ActionM->getError());
                $this->ajaxReturn($return);
            }
        } else {
            $id = I('get.id');
            $action = $this->ActionM->where('id = ' . $id)->find();
            $this->assign("action", $action);
            $this->display("add");
        }
    }

    /**
     * 前台显示
     */
    public function show() {
        if (IS_AJAX) {
            $data['id'] = I('get.id');
            $data['isshow'] = 1;
            $res = $this->ActionM->updateTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ActionM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 前台不显示
     */
    public function hide() {
        if (IS_AJAX) {
            $id = I('get.id');
            $data['isshow'] = 0;
            $res = $this->ActionM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ActionM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 删除的活动 
     */
    public function deletelist() {
        $condition['isdelete'] = 1;
        $tribuneList = $this->ActionM->where($condition)->select();
        $this->assign("tribunelist", $tribuneList);
        $this->display('manager');
    }

    /**
     * 删除 
     */
    public function delete() {
        $id = I("get.id");
        ////$isdel 是否为真删除 1 真正的删除 
        $isdel = I("get.isdel", 0);
        if ($id) {
            $condition['id'] = $id;
            $data['isdelete'] = 1;
            if ($isdel == 1) {
                $res = $this->ActionM->delete($id);
            } else {
                $res = $this->ActionM->where($condition)->save($data);
            }
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->ActionM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "删除失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 恢复删除 
     */
    public function redelete() {
        $id = I("get.id");

        if ($id) {
            $condition['id'] = $id;
            $data['isdelete'] = 0;
            $res = $this->ActionM->where($condition)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "失败" . $this->ActionM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    public function start() {
        if (IS_AJAX) {
            $id = I('get.id');
            $data['status'] = 1;
            $date = Date("y-m-d H:i:s", time());
            $data['starttime'] = $date;
            $res = $this->ActionM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ActionM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    public function end() {
        if (IS_AJAX) {
            $id = I('get.id');
            $data['status'] = 2;
            $date = Date("y-m-d H:i:s", time());
            $data['endtime'] = $date;
            $res = $this->ActionM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ActionM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 自动开始所有时间到的活动
     */
    private function autoStart() {
        $condition['status'] = 0;
        $condition['isdelete'] = 0;
        $tribuneList = $this->ActionM->where($condition)->select();
        for ($i = 0; $i < count($tribuneList); $i++) {
            $date = Date("y-m-d H:i:s", time());
            if ($date > $tribuneList[$i]['starttime']) {
                $tribuneList[$i]['starttime'] = $date;
                $tribuneList[$i]['status'] = 1;
                $this->ActionM->where("id = " . $tribuneList[$i]['id'])->save($tribuneList[$i]);
            }
        }
    }

}
