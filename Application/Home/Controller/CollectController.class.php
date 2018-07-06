<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * ===================================
 * Description of CollectController File
 * @function:收藏控制器
 * @author: hacker-陈龙飞
 * @Date: 2017-1-23
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class CollectController extends BaseController {

    private $CollectM;

    public function _initialize() {
        $this->CollectM = M('collect');
    }

    /**
     * 收藏 取消收藏
     */
    public function add() {
        //前面要传的值 userid type parentid option
        if (IS_AJAX) {
            $data = I("post.");
            //$option 收藏或者是取消收藏的操作标识
            $option = $data['option'];
            $type = $data['type'];
            unset($data['option']);
            if (isCollected($data['parentid'], $type, $data['userid'])) {
                $this->ajaxReturn(arrayRes(1, "收藏成功"));
                exit();
            }
            if ($option == 1) {
                $data['addtime'] = time();
                $data['remark'] = "PC收藏";
                $res = $this->CollectM->add($data);
                if ($res) {
                    //增加收藏内容的被收藏数
                    addCollects($type, $data['parentid']);
                    $this->ajaxReturn(arrayRes(1, "收藏成功"));
                } else {
                    $this->ajaxReturn(arrayRes(1, "收藏失败"));
                }
            } else {
                $where['parentid'] = $data['parentid'];
                $where['userid'] = $data['userid'];
                $where['type'] = $type;
                $res = $this->CollectM->where($where)->delete();
                if ($res) {
                    $this->ajaxReturn(arrayRes(1, "取消收藏成功"));
                } else {
                    $this->ajaxReturn(arrayRes(1, "取消收藏失败"));
                }
            }
        }
    }

    /**
     * 改变相应的收藏数量
     * @param type $id
     * @param type $type   
     * @param type $num  如果是-1取消收藏后要减少收藏数量
     * @return int
     */
    private function changeCollectNumber($id, $type, $num = 1) {
        $model;
        lw($type);
        switch ($type) {
            case 1:
                $model = M('action');
                break;
            case 2:
                $model = M('video');
                break;
            case 3:
                $model = M('tribune');
                break;
            default:
                break;
        }
        $model->where("id = " . $id)->setInc($num);
    }

    /**
     * 删除收藏
     */
    public function delete() {
        if (IS_AJAX) {
            $id = I("get.id", 0);
            if ($id > 0) {
                $res = M('collect')->where("id= " . $id)->delete();
                if ($res) {
                    $this->ajaxReturn(arrayRes(1, "删除成功"));
                } else {
                    $this->ajaxReturn(arrayRes(0, "删除失败"));
                }
            } else {
                $this->ajaxReturn(arrayRes(0, "删除失败，数据异常"));
            }
        }
    }

}
