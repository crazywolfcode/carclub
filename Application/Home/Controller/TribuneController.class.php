<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * ===================================
 * Description of TribuneController File
 * @function:论版控制器
 * @author: hacker-陈龙飞
 * @Date: 2017-1-16
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class TribuneController extends BaseController {

//put your code here
    private $TribuneD;
    private $pageSize = 9; //分页显示的条数
    private $tagName = "afterTribuneDelete";

    public function _initialize() {
        parent::_initialize();
        $this->TribuneD = new \Common\Model\TribuneModel();
        \Think\Hook::add($this->tagName, "Common\\Behaviors\\AfterTribuneDeleteBehavior");
    }

    public function tribuneList() {
        //$type论版的分类
        $cateid = I('get.cateid', 12);
        $p = I('get.p', 1);
        $conditon['cateid'] = $cateid;
        $conditon['type'] = 1; //属于论版的文章 
        $count = $this->TribuneD->where("status = 1 and isdelete= 0  and type =1 and cateid = " . $cateid);
        $tribunes = $this->TribuneD->getTribunebyCate($cateid, $p, $this->pageSize);
        $Page = new \Think\Page($count, $this->pageSize);
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        //最新文章
        $newlist = $this->TribuneD->getNew();
        //最新会员 
        $ulist = D('Common/user')->getNew();
        $this->assign("tribunes", $tribunes);
        $this->assign("cateid", $cateid);
        $this->assign("page", $Page->show());
        $this->assign("newlist", $newlist);
        $this->assign("ulist", $ulist);
        $this->display();
    }

    public function tribuneDetail() {
        //$id 论版ID
        $id = I('get.id');
        $tribune = M('tribune')->where("id = " . $id)->find();
        $user = M('user')->where('id = '.$tribune['adduserid'])->find();
        //获取的评论
        $comentlist = D('Common/comment')->getComment(1, $id);
        //获取同类的文章
        $samelist = $this->TribuneD->getTribunebyType($tribune['type']);
        $alist = $this->TribuneD->getTribunebyType(2);

        //浏览次数增加
        M('tribune')->where("id = " . $id)->setInc("views", 1);
        $this->assign("alist", $alist);
        $this->assign("samelist", $samelist);
        $this->assign("comentlist", $comentlist);
        $this->assign("tribune", $tribune);
        $this->assign("author", $user);
        $this->assign("commentparent", $id); //为拉评论页面而设置，方便评论时候Parentid 好赋值
        $this->display();
    }

    public function delete() {
        if (IS_AJAX) {
            $id = I('get.id', 0);
            if ($id == 0) {
                $this->ajaxReturn(arrayRes(0, "删除失败，数据异常！"));
            }
            $data['isdelete'] = 1;
            $res = M('tribune')->where("id =" . $id)->save($data);
            if ($res) {
                $param = array('parentid' => $id, 'type' => 1);
                \Think\Hook::listen($this->tagName, $param);
                $this->ajaxReturn(arrayRes(1, "删除成功"));
            } else {
                $this->ajaxReturn(arrayRes(0, "删除失败"));
            }
        }
    }

    /**
     * 前台显示,也就是公开
     *  前台不显示，不开公，自己可以见
     */
    public function showorhide() {
        if (IS_AJAX) {
            $id = I('post.id');
            $data['status'] = I('post.type');
            $res = M('tribune')->where("id = " . $id)->save($data);
            if ($res) {
                $return = arrayRes(1, "成功");
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "失败");
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 会员发布文章
     */
    public function addtribune() {
        if (IS_AJAX) {
            $data = I('post.');
            $data['addtime'] = time();
            $data['status'] = 1;
            $data['type'] = 1;
            $user = session(C("HOME_SESSION_NAME"));
            $data['adduserid'] = $user['id'];
            if ($user['oauth'] == 1) {
                $data['addusername'] = $user['wx_nick_name'];
            } elseif ($user['oauth'] == 2) {
                $data['addusername'] = $user['qq_nick_name'];
            } else {
                $data['addusername'] = $user['name'];
            }
            $res = $this->TribuneD->addTribune($data);
            if ($res) {
                $return = arrayRes(1, "文章发布成功", U('User/mytribune', array("status" => 0)));
                $this->ajaxReturn($return);
            } else {
                $return = arrayRes(0, "文章发布失败" . $this->TribuneD->getError());
                $this->ajaxReturn($return);
            }
        }
    }

    /**
     * 修改文章
     */
    public function update() {
        if (IS_AJAX) {
            //POST 过来的数据必须要有ID
            $data = I('post.');
            $res = $this->TribuneD->updateTribune($data);
            if ($res) {
                $this->ajaxReturn(arrayRes(1, "成功", U('User/mytribune', array("status" => $data['status']))));
            } else {
                $this->ajaxReturn(arrayRes(0, "失败" . $this->TribuneD->getError()));
            }
        } else {
            $id = I('get.id');
            $tribune = $this->TribuneD->where('id = ' . $id)->find();
            $this->assign("tribune", $tribune);
            $this->display("User/addtribune");
        }
    }

    /**
     * 首页获取不同分类下的文章 
     */
    public function indextribuneajax() {
        $currcateid = $cateid = I("get.cateid");
        $where['cateid'] = $cateid;
        $where['isdelete'] = 0;
        $where['t.status'] = 1;
        $tribunelist = D('Common/tribune')->getTribuneList("addtime DESC", 1, 4, $where);
        $this->assign("tribunelist", $tribunelist);
        $this->assign("currcateid", $currcateid);
        $html = $this->fetch("Index:topic_item");
        $this->ajaxReturn(arrayRes(1, "获取成功", null, $html));
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
