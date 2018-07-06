<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * ===================================
 * Description of ImageController File
 * @function:图片处理类
 * @author: hacker-陈龙飞
 * @Date: 2017-1-19
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class ImageController extends BaseController {

    private $ImagesM;
    private $table;

    public function _initialize() {
        parent::_initialize();
        $this->table = "images";
        $this->ImagesM = M($this->table);
    }

    /**
     * 管理页面 
     */
    public function manager() {
        $type = I('get.type');
        $condition['type'] = $type;
        $imgList = $this->ImagesM->where($condition)->order('addtime DESC')->select();
        $this->assign("imglist", $imgList);
        $this->display();
    }

    public function addimage() {
        $actionid = I("post.actionid", 0);
        $listid = I("post.listid", 0);
        $type = I("post.type", 1);
        $tempimages = I('post.imgs');
        $images = substr($tempimages, 0, strlen($tempimages) - 1);
        $data['action_id'] = $actionid;
        $data['list_id'] = $listid;
        $data['type'] = $type;
        $admin = session('admin');
        $data['adduserid'] = $admin['id'];
        if ($admin['oauth'] == 1) {
            $data['addusername'] = $admin['wx_nick_name'];
        } elseif ($admin['oauth'] == 2) {
            $data['addusername'] = $admin['qq_nick_name'];
        } else {
            $data['addusername'] = $admin['name'];
        }

        $imgarray = explode(",", $images);
        $res = 0;
        for ($i = 0; $i < count($imgarray); $i++) {
            $data['addtime'] = time();
            $data['url'] = $imgarray[$i];
            try {
                $res += $this->ImagesM->add($data);
            } catch (Exception $exc) {
                
            }
        }
        if ($res > 0) {
            $this->ajaxReturn(arrayRes(1, "成功"), "json");
        } else {
            $this->ajaxReturn(arrayRes(0, "失败"), "json");
        }
    }

    /**
     * 删除 
     */
    public function delete() {
        $id = I("get.id");
        if ($id) {
            $res = $this->ImagesM->delete($id);
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->ImagesM->getError();
            }
        } else {
            $return['status'] = 0;
            $return['msg'] = "删除失败,数据异常";
        }
        $this->ajaxReturn($return);
    }

    /**
     * 前台显示
     */
    public function show() {
        if (IS_AJAX) {
            $data['id'] = I('get.id');
            $data['isshow'] = 1;
            $res = $this->ImagesM->updateTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ImagesM->getError());
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
            $res = $this->ImagesM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ImagesM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

}
