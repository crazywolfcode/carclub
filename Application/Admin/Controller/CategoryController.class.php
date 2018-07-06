<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * ===================================
 * Description of CateGoryController File
 * @function:分类管理
 * @author: hacker-陈龙飞
 * @Date: 2017-1-16
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class CategoryController extends BaseController {

//put your code here
    private $CategoryM;
    private $table = "category";

    public function _initialize() {
        parent::_initialize();
        if (empty($this->CategoryM)) {
            $this->CategoryM = M($this->table);
        }
    }

    public function index() {
        $categorys = D("Common/category")->getCategoryList($order = 'sort ASC', null, null, null);
        $this->assign("categorys", $categorys);
        $this->display();
    }

    public function update() {
        $data = I("post.");
        if ($data) {
            $conditon['id'] = $data['id'];
            $res = $this->CategoryM->where($conditon)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "修改成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "修改失败" . $this->CategoryM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "修改失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    public function add() {
        $data = I("post.");
        if ($data) {
            $res = $this->CategoryM->add($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "增加成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "增加失败" . $this->CategoryM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "增加失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    public function hide() {
        $id = I("get.id");
        if ($id) {
            $condition['id'] = $id;
            $data['isshow'] = 0;
            $res = $this->CategoryM->where($condition)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "操作成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "操作失败" . $this->CategoryM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "操作失败";
        }
        $this->ajaxReturn($return);
    }

    public function show() {
        $id = I("get.id");
        if ($id) {
            $condition['id'] = $id;
            $data['isshow'] = 1;
            $res = $this->CategoryM->where($condition)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "操作成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "操作失败" . $this->CategoryM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "操作失败";
        }
        $this->ajaxReturn($return);
    }

    public function delete() {
        $id = I("get.id");
        if ($id) {
            $condition['id'] = $id;
            $res = $this->CategoryM->where($condition)->delete();
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->CategoryM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "删除失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 用户的选择
     */
    public function selectuser() {
        $where['type'] = array('eq', 0);
        $count = D('Common/user')->getCountByWhere($where);
        $this->newpage($count, $this->pageSize);
        $userList = D('Common/user')->getUserList($order = 'point DESC', $p, $this->pageSize, $where);
        $this->assign("userlist", $userList);
        $this->display();
    }

}
