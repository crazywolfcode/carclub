<?php

namespace Admin\Controller;

class IndexController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $this->display();
    }

    /**
     * 赵先方
     * 网站基本参数配置
     */
    Public function seting() {
        if (IS_AJAX) {
            $data = I('post.');
            $config = M('Config');
            foreach ($data as $d => $v) {
                $config->where(array('name' => $d))->data(array('value' => $v))->save();
            }
            $this->ajaxReturn(array('stauts' => 1, 'info' => '修改成功！'));
        } else {
            $type = I('get.type', 1);
            $config = M('Config'); //实例化配置表
            $configInfo = $config->where(array('status' => 1))->where("type = " . $type)->order('sort ASC')->select(); //网站配置信息
            $this->assign('config', $configInfo);
            $this->display();
        }
    }

    /**
     * 赵先方
     * 俱乐部简介
     */
    public function summary() {
        $info = M('CarclubInfo');
        if (IS_AJAX) {
            $brief = I('post.brief');
            $id = I('post.id', 0, 'intval');
            if (!$brief || !$id) {
                $this->ajaxReturn(array('status' => 0, 'info' => '数据不能为空！'));
            } else {
                $saveInfo = $info->where(array('id' => $id))->data(array('brief' => $brief))->save();
                if ($saveInfo === FALSE) {
                    $this->ajaxReturn(array('status' => 0, 'info' => '修改失败！'));
                } else {
                    $this->ajaxReturn(array('status' => 1, 'info' => '修改成功！', 'url' => U()));
                }
            }
        } else {
//            $where = array('id'=>1);
            $carClub = $info->find(); //虽然只有一条，但是防止后面有扩展
            $this->assign('content', $carClub);
            $this->display();
        }
    }

    /**
     * 用户协议 
     */
    public function agreements() {
        if (IS_POST) {
            $map['id'] = I("post.id");
            $data['value'] = I("post.content");
            $res = M('config')->where($map)->save($data);
            if ($res) {
                C('user_agreements', $data['value']);
                $this->ajaxReturn(arrayRes(1, "修改成功！"));
            } else {
                $this->ajaxReturn(arrayRes(0, "修改失败！"));
            }
        } else {
            $where['type']=8;
            $where['name']="user_agreements";
            $agreements =  M('config')->where($where)->find();       
            $this->assign("agreements", $agreements);
            $this->display();
        }
    }

}
