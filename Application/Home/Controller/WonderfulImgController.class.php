<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

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
    private $pageSize = 6; //分页显示的条数

    public function index() {
        $this->display();
    }

    public function imgList() {
        $type = I("get.type", 1);
        $p = I("get.p", 1);
        switch ($type) {
            case 1:
                $alist = D("Common/action")->getActionList(null, $p, $this->pageSize, array('isdelete' => 0, 'ishow' => 1, 'status' => 2));
                for ($i = 0; $i < count($alist); $i++) {
                    $alist[$i]['imgs'] = D('Common/images')->getActionimgs($alist[$i]['id']);
                }
                $count = D("Common/action")->getCount(array('isdelete' => 0, 'ishow' => 1, 'status' => 2));
                $Page = new \Think\Page($count, $this->pageSize);
                $Page->setConfig('prev', '上一页');
                $Page->setConfig('next', '下一页');
                $this->assign("alist", $alist);
                $this->assign("page", $Page->show());
                $this->assign("type", $type);
                break;
            case 2:
                $count = D("Common/imglist")->getCount(array('isdelete' => 0, 'ishow' => 1));
                $ilist = D("Common/imglist")->getImgLists(null, $p, $this->pageSize, array('isdelete' => 0, 'ishow' => 1));
                for ($i = 0; $i < count($ilist); $i++) {
                    $ilist[$i]['imgs'] = D('Common/images')->getListimgs($ilist[$i]['id']);
                }
                $Page = new \Think\Page($count, $this->pageSize);
                $Page->setConfig('prev', '上一页');
                $Page->setConfig('next', '下一页');
                $this->assign("ilist", $ilist);
                $this->assign("page", $Page->show());
                $this->assign("type", $type);
                break;
            default:
                break;
        }
        //最新图片
        $newimglist = D('Common/images')->getNew();
        $this->assign("newimglist", $newimglist);
        //最新视频
        $newvideolist = D('Common/video')->getNew();
        $this->assign("newvideolist", $newvideolist);
        $this->display();
    }

    public function imgDetail() {
        $id = I("get.id");
        $currid = $id;
        $type = I("get.type");
        $list;
        $pre;
        $next;
        //type = 1 属性活动中的图片
        if ($type == 1) {
            $tempaction = M('images')->field("action_id")->find($id);
            $aid = $tempaction['action_id'];
            $commentType = 5;
            $collectType = 5;
            $commentparent = $aid;
            $action = M('action')->find($aid);
            $imagelist = D("Common/images")->getActionimgs($aid);
            $list = $action;
            //上一个
            //是否为第一个
            if ($this->checkFirst($aid, $type) == FALSE) {
                $temp = M('action')->order("id DESC")->where("id <" . $aid)->field("id")->find();
                $tempid = $temp['id'];
                $tempimg = D("Common/images")->getOne($type, $tempid);
                $pre["id"] = $tempimg['id'];
                $pre["url"] = $tempimg['url'];
                $pre['type'] = $tempimg['type'];
            }
            //下一个
            //是否为最后一个
            if ($this->checkLast($aid, $type) == FALSE) {
                $temp = M('action')->order("id ASC")->where("id >" . $aid)->field("id")->find();
                $tempid = $temp['id'];
                $tempimg = D("Common/images")->getOne($type, $tempid);
                $next["id"] = $tempimg['id'];
                $next["url"] = $tempimg['url'];
                $next['type'] = $tempimg['type'];
            }
        } else {
            $templistimg = M('images')->field("list_id")->find($id);
            $lid = $templistimg['list_id'];
            $commentType = 2;
            $collectType = 4;
            $commentparent = $lid;
            $imglist = M('imglist')->find($lid);
            $imagelist = D("Common/images")->getListimgs($lid);
            $list = $imglist;
            //上一个
            //是否为第一个
            if ($this->checkFirst($lid, $type) == FALSE) {
                $temp = M('imglist')->order("id DESC")->where("id <" . $lid)->field("id")->find();
                $tempid = $temp['id'];
                $tempimg = D("Common/images")->getOne($type, $tempid);
                $pre["id"] = $tempimg['id'];
                $pre["url"] = $tempimg['url'];
                $pre['type'] = $tempimg['type'];
            }
            //下一个
            //是否为最后一个
            if ($this->checkLast($lid, $type) == FALSE) {
                $temp = M('imglist')->order("id ASC")->where("id >" . $lid)->field("id")->find();
                $tempid = $temp['id'];
                $tempimg = D("Common/images")->getOne($type, $tempid);
                $next["id"] = $tempimg['id'];
                $next["url"] = $tempimg['url'];
                $next['type'] = $tempimg['type'];
            }
        }
        //浏览次数增加
        M('images')->where("id = " . $id)->setInc("views", 1);
        //评论
        $comentlist = D('Common/comment')->getComment($commentType, $currid); 
        $this->assign("pre", $pre);
        $this->assign("next", $next);
        $this->assign("list", $list);
        $this->assign("currid", $currid);
        $this->assign("imagelist", $imagelist);
        $this->assign("commenttype", $commentType);
        $this->assign("comentlist", $comentlist);
        $this->assign("collecttype", $collectType);
        $this->assign("commentparent", $currid); //为拉评论页面而设置，方便评论时候Parentid 好赋值
        $this->display();
    }

    /**
     * 是否为第一个
     * @param type $id
     * @param type $type
     */
    public function checkFirst($id, $type) {
        if ($type == 1) {
            $dbFirst = M('action')->field("id")->order("id ASC")->find();
            $dbFirstid = $dbFirst['id'];
            if ($dbFirstid < $id) {
                //不是第一个
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            $dbFirst = M('imglist')->field("id")->order("id ASC")->find();
            $dbFirstid = $dbFirst['id'];
            if ($dbFirstid < $id) {
                //不是第一个
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    /**
     * 是否为最后一个
     * @param type $id
     * @param type $type
     */
    public function checkLast($id, $type) {
        if ($type == 1) {
            $dbLast = M('action')->field("id")->order("id DESC")->find();
            $dbLastid = $dbLast['id'];
            if ($dbLastid > $id) {
                //不是最后一个
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            $dbLast = M('imglist')->field("id")->order("id DESC")->find();
            $dbLastid = $dbLast['id'];
            if ($dbLastid > $id) {
                //不是最后一个
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

}
