<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * ===================================
 * Description of ImageListController File
 * @function:图集管理类
 * @author: 陈龙飞
 * @Date: 2017-1-24
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class ImageListController extends BaseController {

//put your code here
    private $ImgM;
    private $table;

    public function _initialize() {
        parent::_initialize();
        $this->table = "imglist";
        $this->ImgM = M($this->table);
    }

    /**
     * 管理页面 
     */
    public function manager() {
        $imgList = $this->ImgM->where($condition)->order('addtime DESC')->select();
        //获取图集的图片
        for ($i = 0; $i < count($imgList); $i++) {
            $imgList[$i]['imgs'] = D("Common/images")->getListimgs($imgList[$i]['id']);
        }
        $this->assign("imglists", $imgList);
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $admin = session('admin');
            $data['adduserid'] = $admin['id'];
            if ($admin['oauth'] == 1) {
                $data['addusername'] = $admin['wx_nick_name'];
            } elseif ($admin['oauth'] == 2) {
                $data['addusername'] = $admin['qq_nick_name'];
            } else {
                $data['addusername'] = $admin['name'];
            }
            $data['addtime'] = time();
            $res = M('imglist')->add($data);
            if ($res > 0) {
                $this->redirect('ImageList/manager');
                // $this->success('ImageList/manager');
            }
        } else {
            $this->display();
        }
    }

    /**
     * 删除 
     */
    public function delete() {
        $id = I("get.id");
        $isdel = I("get.isdel", 0);
        if ($id) {
            if ($isdel == 1) {
                $res = $this->ImgM->delete($id);
            } else {
                $data['isdelete'] = 1;
                $res = $this->ImgM->where("id = " . $id)->save($data);
            }
            if ($res > 0) {
                $return['status'] = 1;
                $return['msg'] = "删除成功";
            } else {
                $return['status'] = 0;
                $return['msg'] = "删除失败" . $this->ImgM->getError();
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
            $res = $this->ImgM->where($condition)->save($data);
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

    /**
     * 前台显示
     */
    public function show() {
        if (IS_AJAX) {
            $data['id'] = I('get.id');
            $data['isshow'] = 1;
            $res = $this->ImgM->updateTribune($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ImgM->getError());
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
            $res = $this->ImgM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->ImgM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 修改 
     */
    public function update() {
        if (IS_POST) {
            //POST 过来的数据必须要有ID
            $data = I('post.');
            $res = $this->ImgM->where("id = " . $data['id'])->save($data);
            if ($res) {
                $this->redirect('ImageList/manager');
            }
        } else {
            $id = I('get.id');
            $imglist = $this->ImgM->where('id = ' . $id)->find();
            $this->assign("imglist", $imglist);
            $this->display();
        }
    }

    function detail() {
        $id = I("get.id");
        $imglist = $this->ImgM->find($id);
        $listimgs = D("Common/images")->getListimgs($id);
        $this->changeimgnumber($id, count($listimgs));
        $this->assign("imglist", $imglist);
        $this->assign("listimgs", $listimgs);
        $this->display();
    }

    /**
     * 改变图集的图片数
     * @param type $id
     * @param type $num
     */
    public function changeimgnumber($id, $num) {
        if ($num > 0) {
            $data['images'] = $num;
        } else {
            return;
        }
        $this->ImgM->where("id =" . $id)->save($data);
    }

}
