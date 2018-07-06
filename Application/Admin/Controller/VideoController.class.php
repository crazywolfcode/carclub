<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * ===================================
 * Description of VideoController File
 * @function:视频处理类
 * @author: hacker-陈龙飞
 * @Date: 2017-1-19
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class VideoController extends BaseController {

    private $VideoM;
    private $table;

    public function _initialize() {
        parent::_initialize();
        $this->table = "video";
        $this->VideoM = M($this->table);
    }

    /**
     * 管理页面 
     */
    public function manager() {
        $type = I('get.type');
        $condition['type'] = $type;
        $videoList = $this->VideoM->where($condition)->order('addtime DESC')->select();
        $this->assign("videolist", $videoList);
        $this->display();
    }

//put your code here
    public function addvideo() {
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
            $res = $this->VideoM->add($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->VideoM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 前台显示
     */
    public function show() {
        if (IS_AJAX) {
            $data['id'] = I('get.id');
            $data['isshow'] = 1;
            $res = $this->VideoM->updateTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->VideoM->getError());
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
            $res = $this->VideoM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->VideoM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 删除的活动 
     */
    public function deletelist() {
        $condition['isdelete'] = 1;
        $tribuneList = $this->VideoM->where($condition)->select();
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
                $res = $this->VideoM->delete($id);
            } else {
                $res = $this->VideoM->where($condition)->save($data);
            }
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->VideoM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "删除失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 新增其它的视频
     */
    public function addorther() {
        if (IS_POST) {
            $data = I('post.');
            $data['addtime'] = time();
            $data['isshow'] = 1;
            $data['type'] = 2;
            $admin = session('admin');
            $data['adduserid'] = $admin['id'];
            $data['addusername'] = $admin['name'];
            $res = $this->VideoM->add($data);
            if ($res) {
                $return = arrayRes(1, "成功", U('manager', array('type' => 2)));
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->VideoM->getError());
                $this->ajaxReturn($return);
            }
        } else {
            $this->display();
        }
    }

    /**
     * 恢复删除 
     */
    public function redelete() {
        $id = I("get.id");
        if ($id) {
            $condition['id'] = $id;
            $data['isdelete'] = 0;
            $res = $this->VideoM->where($condition)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "失败" . $this->VideoM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 修改 
     */
    public function update() {
        if (IS_AJAX) {
            //POST 过来的数据必须要有ID
            $data = I('post.');
            $res = $this->VideoM->where("id = " . $data['id'])->save($data);
            if ($res) {
                $return = arrayRes(1, "成功", U('manager', array('type' => $data['type'])));
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->VideoM->getError());
                $this->ajaxReturn($return);
            }
        } else {
            $id = I('get.id');
            $video = $this->VideoM->where('id = ' . $id)->find();
            $this->assign("video", $video);
            $this->display("addorther");
        }
    }

    /**
     * 播放 
     */
    public function play() {
        $id = I('get.id');
        $video = $this->VideoM->where('id = ' . $id)->find();
        $this->assign("video", $video);
        $this->display();
    }

}
