<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mobile\Controller;

/**
 * ===================================
 * Description of TribuneController File
 * @function:控制器
 * @author: hacker-陈龙飞
 * @Date: 2017-1-16
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class TribuneController extends BaseController {

//put your code here
    private $TribuneD;
    private $pageSize = 6; //分页显示的条数
    private $ajaxpageSize = 3; //分页显示的条数
    private $tagName = "afterTribuneDelete";

    public function _initialize() {
        parent::_initialize();
        $this->TribuneD = new \Common\Model\TribuneModel();
        //当文章被删除成功，要删除基评论和评论的回复 
        \Think\Hook::add($this->tagName, "Common\\Behaviors\\AfterTribuneDeleteBehavior");
    }

    public function tribuneList() {
        //$type的分类
        $cateid = I('get.cateid', 0);
        $p = I('get.p', 1);
        if ($cateid == 0) {
            //not get the cateid,get the baseController's TribuneCates
            $cateid = $this->TribuneCates[0]['id'];
        }
        $conditon['cateid'] = $cateid;
        $conditon['type'] = 1; //属于论版的文章    
        $tribunes = $this->TribuneD->getTribunebyCate($cateid, $p, $this->pageSize);
        $this->assign("tribunelist", $tribunes);
        $this->assign("cateid", $cateid);
        $this->display();
    }

    /**
     * 下拉加载
     */
    public function ajaxGetMore() {
        if (IS_AJAX) {
            $cateid = I('get.cateid');
            $p = I('get.p') ? I('get.p', 1, 'intval') : I('post.p', 1, 'intval');
            $conditon['cateid'] = $cateid;
            $conditon['type'] = 1; //属于论版的文章    
            $tribunes = $this->TribuneD->getTribunebyCate($cateid, $p, $this->ajaxpageSize);
            $this->assign("tribunelist", $tribunes);
            $html = $this->fetch("Tribune:listItem");
            $num = count($tribunes);
            //status 0 没有数据 1这次有数据，下次没有，2这次有数据，下次可能没有
            if ($num == 0) {
                $return = arrayRes(0);
            } else if ($num < $this->ajaxPageSize && $num > 0) {
                $return = arrayRes(1, null, null, $html);
            } else {
                $return = arrayRes(2, null, null, $html);
            }
            $this->ajaxReturn($return, "json");
        }
    }

    public function tribuneDetail() {
        //$id 论版ID
        $id = I('get.id');
        //增加查看次数
        M('tribune')->where("id = " . $id)->setInc('views', 1);
        $cateid = I('get.cateid');
        $tribune = M('tribune')->where("id = " . $id)->find();
        //获取的评论
        $comentlist = D('Common/comment')->getComment(1, $id);
        //获取同类的文章
        if ($cateid > 0) {
            $map['t.type'] = $tribune['type'];
            $map['cateid'] = $cateid;
            $map['_logic'] = "or";
            $samelist = $this->TribuneD->getTribuneList('addtime DESC', 1, 6, $map);
            $alist = $this->TribuneD->getTribunebyType(2);
        } else {
            $samelist = $this->TribuneD->getTribunebyType(2);
            $alist = $this->TribuneD->getTribunebyType(1);
        }
        $this->assign("alist", $alist);
        $this->assign("samelist", $samelist);
        $this->assign("commentlist", $comentlist);
        $this->assign("tribune", $tribune);
        $this->assign("cateid", $cateid);
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
     * 前台不显示，不开公，自己可以见
     */
    public function showorhide() {
        if (IS_AJAX) {
            $id = I('post.id');
            $data['status'] = I('post.type');
            $res = M('tribune')->where("id = " . $id)->save($data);
            if ($res) {                
                $this->ajaxReturn(arrayRes(1, "成功"));
            } else {              
                $this->ajaxReturn(arrayRes(0, "失败"));
            }
        }
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
