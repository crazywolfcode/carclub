<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * ===================================
 * Description of TribuneController File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-17
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class TribuneController extends BaseController {

    private $TribuneD;

    public function _initialize() {
        parent::_initialize();
        $this->TribuneD = new \Common\Model\TribuneModel();
    }

//put your code here
    public function manager() {
        $status = I('get.status', 1);
        $type = I('get.type', 1);
        $cateid = I('get.cateid');
        $condition['status'] = $status;
        $condition['type'] = $type;
        if ($status > 0) {
            $condition['cateid'] = $cateid;
        }
        $condition['isdelete'] = 0;
        $tribuneList = $this->TribuneD->where($condition)->order('addtime DESC')->select();
        $this->assign("tribunelist", $tribuneList);
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['addtime'] = time();
            $data['status'] = 1; //后台增加的文章免审核
            $data['type'] = 1;
            $admin = session('admin');
            $data['adduserid'] = $admin['id'];
            if ($admin['oauth'] == 1) {
                $data['addusername'] = $admin['wx_nick_name'];
            } elseif ($admin['oauth'] == 2) {
                $data['addusername'] = $admin['qq_nick_name'];
            } else {
                $data['addusername'] = $admin['name'];
            }
            $res = $this->TribuneD->addTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功", U('manager', array('status' => 1, 'cateid' => $data['cateid'])));
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->TribuneD->getError());
                $this->ajaxReturn($return);
            }
        } else {
            $this->display();
        }
    }

    public function update() {
        if (IS_AJAX) {
            //POST 过来的数据必须要有ID
            $data = I('post.');
            $res = $this->TribuneD->updateTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功", U('manager', array('status' => 1, 'cateid' => $data['cateid'])));
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->TribuneD->getError());
                $this->ajaxReturn($return);
            }
        } else {
            $id = I('get.id');
            $tribune = $this->TribuneD->where('id = ' . $id)->find();
            $this->assign("tribune", $tribune);
            $this->display("add");
        }
    }

    public function checkok() {
        if (IS_AJAX) {
            $data['id'] = I('get.id');
            $data['status'] = 1;
            $res = $this->TribuneD->updateTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->TribuneD->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    public function checkno() {
        if (IS_AJAX) {
            $data['id'] = I('get.id');
            $data['status'] = 0;
            $res = $this->TribuneD->updateTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->TribuneD->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    public function deletelist() {
        $condition['isdelete'] = 1;
        $tribuneList = $this->TribuneD->where($condition)->select();
        $this->assign("tribunelist", $tribuneList);
        $this->display('manager');
    }

    public function delete() {
        $id = I("get.id");
        ////$isdel 是否为真删除 1 真正的删除 
        $isdel = I("get.isdel", 0);
        if ($id) {
            $condition['id'] = $id;
            $data['isdelete'] = 1;
            if ($isdel == 1) {
                $res = $this->TribuneD->delete($id);
            } else {
                $res = $this->TribuneD->where($condition)->save($data);
            }

            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->TribuneD->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "删除失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 
     * 将文章设为精华文章 或者 取消
     */
    public function boutique() {
        if (IS_AJAX) {
            $id = I('post.id');
            $data['isboutique'] = I('post.type');
            $res = M('tribune')->where("id = " . $id)->save($data);
            if ($res) {
                $this->ajaxReturn(arrayRes(1, "成功"));
            } else {
                $this->ajaxReturn(arrayRes(0, "失败"));
            }
        }
    }

}
