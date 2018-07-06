<?php

namespace Admin\Controller;

/**
 * Description of BarnerController
 * PC首页轮播图片
 * @author Crazycode
 * @date 2017-05-16 01:55:43
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class BarnerController extends BaseController {

    private $BarnerM;
    private $table = 'barner';

    public function _initialize() {
        parent::_initialize();
        $this->BarnerM = M($this->table);
    }

    public function add() {
        $this->display();
    }

    /**
     * 管理
     */
    public function manager() {
        $barners = M('barner')->select();
        $this->assign("barners", $barners);
        $this->display();
    }

    public function addbarner() {
        $data['addtime'] = date("Y-m-d H:i:s");
        $data['status'] = 1;
        $data['url'] = I('post.imgurl');
        $res = $this->BarnerM->add($data);
        if ($res > 0) {
            $this->ajaxReturn(arrayRes(1, "成功"), "json");
        } else {
            $this->ajaxReturn(arrayRes(0, "失败"), "json");
        }
    }

    /**
     * 前台显示
     */
    public function show() {
        if (IS_AJAX) {
            $id = I('get.id');
            $data['status'] = 1;
            $res = $this->BarnerM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->BarnerM->getError());
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
            $data['status'] = 0;
            $res = $this->BarnerM->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败" . $this->BarnerM->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    public function delbarner() {
        $id = I('get.id');
        $res = $this->BarnerM->delete($id);
        if ($res > 0) {
            $this->ajaxReturn(arrayRes(1, "成功"), "json");
        } else {
            $this->ajaxReturn(arrayRes(0, "失败"), "json");
        }
    }

}
