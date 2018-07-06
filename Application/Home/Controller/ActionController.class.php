<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * ===================================
 * Description of ActionController File
 * @function:活动控制器
 * @author: hacker-陈龙飞
 * @Date: 2017-1-12
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class ActionController extends BaseController {

    public $actionModelM; //最后面的M 是M()标识，只是为好区分
    public $actionModelD; //最后面的M 是D()标识，
    private $pageSize; //分页显示的条数
    private $currPage = 1; //当前的页码默认为第一页

//put your code here

    public function __construct() {
        parent::__construct();
    }

    public function _initialize() {
        parent::_initialize();
        $this->pageSize = 3;
        $this->initActionModel();
    }

    /**
     * 显示活动列表
     */
    public function actionList() {
        //$actionStatus 为活动的状态，0未开始的活动 1 正在进行的活动 2已经结束的活动
        //$actionStatus = I('get.status');
        //$page 页码
        $this->currPage = I('get.p', 1);
        //$conditon['status'] = $actionStatus;
        $conditon["is_delete"] = 0;
        $actionList = $this->actionModelM->where($conditon)->page($this->currPage . ',' . $this->pageSize)->order('addtime DESC')->select();
        //活动中的图片//活动中的视频
        for ($i = 0; $i < count($actionList); $i++) {
            $actionList[$i]['imgs'] = D('Common/images')->getActionimgs($actionList[$i]['id']);
            $actionList[$i]['videos'] = D('Common/video')->getActionvideos($actionList[$i]['id']);
        }
        $count = $this->actionModelM->where($conditon)->count();
        $page = new \Think\Page($count, $this->pageSize);
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        $pagetitle = "活动列表";
        $this->assign("pagetitle", $pagetitle);
        $this->assign("page", $page->show());
        $this->assign("actionlist", $actionList);
        $this->display();
    }

    /**
     * 显示活动列表
     */
    public function actionDetail() {
        //$id 为活动id 如果没有id 就跳转到 actionList
        $id = I('get.id', 0);
        if ($id == 0) {
            $this->redirect(U('actionList'));
        }
        $conditon['id'] = $id;
        $conditon["is_delete"] = 0;
        $action = $this->actionModelM->where($conditon)->find();
        $pagetitle = $action['title'];
        //获取活动中的图片
        $actionimgs = D('Common/images')->getActionimgs($id, 12);
        //获取活动中的视频
        $actionvideos = D('Common/video')->getActionvideos($id);
        //获取的评论
        $comentlist = D('Common/comment')->getComment(4, $id);

        //随机获取其它活动
        $ortherlist = D('Common/action')->getRands(6);

        //浏览次数增加
        M('action')->where("id = " . $id)->setInc("views", 1);
        $this->assign("comentlist", $comentlist);
        $this->assign("pagetitle", $pagetitle);
        $this->assign("action", $action);
        $this->assign("commentparent", $id); //为拉评论页面而设置，方便评论时候Parentid 好赋值
        $this->assign("actionimgs", $actionimgs);
        $this->assign("actionvideos", $actionvideos);
        $this->assign("ortherlist", $ortherlist);
        $this->display();
    }

    /**
     * 初始化Model
     */
    public function initActionModel() {
        if (empty($this->actionModelD)) {
            $this->actionModelD = D('Common/action');
        }
        $this->actionModelM = M('action');
    }

}
