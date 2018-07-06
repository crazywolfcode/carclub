<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mobile\Controller;

/**
 * ===================================
 * Description of wonderfulImgController File
 * @function:显示图片控制器
 * @author: hacker-陈龙飞
 * @Date: 2017-1-12
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class WonderfulImgController extends BaseController {

//put your code here
    private $pageSize = 8; //分页显示的条数
    private $AjaxpageSize = 4; //分页显示的条数
    private $currPage;
    private $currType;

    public function index() {
        $this->display();
    }

    public function imgList() {
        $this->currType = I("get.type", 1);
        $this->currPage = I("get.p", 1);
        switch ($this->currType) {
            case 1:
                $imglist = D("Common/action")->getActionList(null, $this->currPage, $this->pageSize, array('isdelete' => 0, 'ishow' => 1, 'status' => 2));
                for ($i = 0; $i < count($imglist); $i++) {
                    $imglist[$i]['imgs'] = D('Common/images')->getActionimgs($imglist[$i]['id']);
                }
                break;
            case 2:
                $imglist = D("Common/imglist")->getImgLists(null, $this->currPage, $this->pageSize, array('isdelete' => 0, 'ishow' => 1));
                for ($i = 0; $i < count($imglist); $i++) {
                    $imglist[$i]['imgs'] = D('Common/images')->getListimgs($imglist[$i]['id']);
                }
                break;
            default:
                break;
        }
        $this->assign("imglist", $imglist);
        $this->assign("currtype", $this->currType);
        $this->display();
    }

    /**
     * 手下下拉加载
     */
    public function ajaxGetMore() {
        if (IS_AJAX) {
            $this->currType = I("get.type", 1);
           $p= I('get.p') ? I('get.p', 1, 'intval') : I('post.p', 1, 'intval');
            switch ($this->currType) {
                case 1:
                    $imglist = D("Common/action")->getActionList(null, $p, $this->AjaxpageSize, array('isdelete' => 0, 'ishow' => 1, 'status' => 2));
                    for ($i = 0; $i < count($imglist); $i++) {
                        $imglist[$i]['imgs'] = D('Common/images')->getActionimgs($imglist[$i]['id']);
                    }
                    break;
                case 2:
                    $imglist = D("Common/imglist")->getImgLists(null,$p, $this->AjaxpageSize, array('isdelete' => 0, 'ishow' => 1));
                    for ($i = 0; $i < count($imglist); $i++) {
                        $imglist[$i]['imgs'] = D('Common/images')->getListimgs($imglist[$i]['id']);
                    }
                    break;
                default:
                    break;
            }
            $this->assign("imglist", $imglist);
            $html = $this->fetch("WonderfulImg:listItem");
            $num = count($imglist);
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

    public function imgDetail() {
        $aid = I("get.aid");
        $lid = I("get.lid");
        $this->currType = $type = I("get.type", 1);
        //type = 1 属性活动中的图片
        //$Tager 是图集或者是活动，主要是取里面的 Title
        $map['isshow'] = 1;
        $map['isdelete'] = 0;
        $likelist;
        if ($type == 1) {
            $commentType = 5;
            $collectType = 5;
            $commentparent = $aid;
            $map['action_id'] = $aid;
            $Tager = M('action')->find($aid);
            $imagelist = D("Common/images")->getAllImgByWhere($map, $field = "id,url");
            //猜你喜欢
            $likelist = D("Common/action")->getActionList($order = 'addtime DESC', 1, 8, array("isdelete " => 0, "isshow" => 1));
            for ($i = 0; $i < count($likelist); $i++) {
                $likelist[$i]['imgs'] = D("Common/images")->getActionimgs($likelist[$i]['id'], $limit = 3, $order = "rand(),addtime DESC");
            }
        } else {
            $commentType = 2;
            $collectType = 4;
            $commentparent = $lid;
            $map['list_id'] = $lid;
            $Tager = M('imglist')->find($lid);
            M('imglist')->where("id = " . $lid)->setInc("views", 1);
            $imagelist = D("Common/images")->getAllImgByWhere($map, $field = "id,url");
            //猜你喜欢
            $likelist = D("Common/imglist")->getImgLists($order = 'rand(),addtime DESC', 1, 8, array("isdelete " => 0, "isshow" => 1));
            for ($i = 0; $i < count($likelist); $i++) {
                $likelist[$i]['imgs'] = D("Common/images")->getListimgs($likelist[$i]['id'], $limit = 3, $order = "rand()");
            }
        }
        //评论
        $comentlist = D('Common/comment')->getComment($commentType, $commentparent);
        $this->assign("imgtotal", count($imagelist));
        $this->assign("tager", $Tager);
        $this->assign("imagelist", $imagelist);
        $this->assign("currtype", $this->currType);
        $this->assign("likelist", $likelist);
        $this->assign("commentparent", $commentparent); //为拉评论页面而设置，方便评论时候Parentid 好赋值
        $this->assign("commenttype", $commentType);
        $this->assign("commentlist", $comentlist);
        $this->assign("collecttype", $collectType);
        $this->display();
    }

}
