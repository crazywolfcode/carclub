<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * ===================================
 * Description of UserlevelController File
 * @function:会员等级管理 
 * @author: hacker-陈龙飞
 * @Date: 2017-2-14
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class UserlevelController extends BaseController {

    private $LevelM;
    private $table = 'userlevel';

    public function _initialize() {
        parent::_initialize();
        if (empty($this->LevelM)) {
            $this->LevelM = M($this->table);
        }
    }

    /**
     * 会员等级管理
     */
    public function index() {
        $LevelList = $this->LevelM->where()->select();

        $this->assign("levellist", $LevelList);
        $this->display();
    }

    public function add() {
        $data = I("post.");
        $data['icon'] = $this->upfile();
        if ($data) {
            $res = $this->LevelM->add($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "增加成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "增加失败" . $this->LevelM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "增加失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    public function delete() {
        $id = I("get.id");
        if ($id) {
            $condition['id'] = $id;
            $res = $this->LevelM->where($condition)->delete();
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->LevelM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "删除失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    public function update() {
        $data = I("post.");
        if (!empty($_FILES['icon'])) {
            $data['icon'] = $this->upfile();
        }
        if ($data) {
            $conditon['id'] = $data['id'];
            $res = $this->LevelM->where($conditon)->save($data);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "修改成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "修改失败" . $this->LevelM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "修改失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    public function upfile() {
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = './Public/Uploads'; // 设置附件上传根目录
        $upload->savePath = '/img/'; // 设置附件上传（子）目录
        $upload->sub_name = date("y-m-d", time());
        // 上传文件 
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            return "/Public/Uploads" . $info['icon']['savepath'] . $info['icon']['savename'];
        }
    }

}
