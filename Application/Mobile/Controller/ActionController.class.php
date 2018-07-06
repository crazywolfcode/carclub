<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mobile\Controller;

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
    private $pageSize = 4; //分页显示的条数
    private $AjaxpageSize = 2; //分页显示的条数
    private $currPage = 1; //当前的页码默认为第一页

//put your code here

    public function __construct() {
        parent::__construct();
    }

    public function _initialize() {
        parent::_initialize();
        $this->pageSize = 5;
        $this->initActionModel();
    }

    /**
     * 显示活动列表
     */
    public function actionList() {
        $actionStatus = I('get.status', 2);
        $this->currPage = I('get.p', 1);
        $conditon['status'] = $actionStatus;
        $conditon["is_delete"] = 0;
        $actionList = $this->actionModelD->getActionList($order = 'addtime DESC', $this->currPage, $this->pageSize, $conditon);
        $this->assign("actionlist", $actionList);
        $this->assign("currpage", $this->currPage);
        $this->display();
    }

    /**
     * 手下下拉加载
     */
    public function ajaxGetMore() {
        if (IS_AJAX) {
            //活动的状态，0未开始的活动 1 正在进行的活动 2已经结束的活动
            $p = I('get.p') ? I('get.p', 1, 'intval') : I('post.p', 1, 'intval');           
            $conditon['status'] = 2;
            $conditon["is_delete"] = 0;
            $actionList = $this->actionModelD->getActionList($order = 'addtime DESC', $p, $this->AjaxpageSize, $conditon);
            $this->assign("actionlist", $actionList);
            $html = $this->fetch("Action:listItem");
            $num = count($actionList);
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

    /**
     * 显示活动列表
     */
    public function actionDetail() {      
        //$id 为活动id 如果没有id 就跳转到 actionList
        //增加查看次数
        $id = I('get.id', 0);
        $this->actionModelM->where("id = " . $id)->setInc('views', 1);
        if ($id == 0) {
            $this->redirect('actionList');
        }
        $conditon['id'] = $id;
        $conditon["is_delete"] = 0;
        $action = $this->actionModelM->where($conditon)->find();
        if (!$action) {
            $this->redirect('actionList');
        }
        //活动中的图片//活动中的视频
        $action['imgs'] = D('Common/images')->getActionimgs($id);
        $action['videolist'] = D('Common/video')->getActionvideos($id);
        //获取的评论
        $comentlist = D('Common/comment')->getComment(4, $id);        
        $this->assign("topTwoTitle", "活动详情");
        $this->assign("action", $action);
        $this->assign("commentlist", $comentlist);
        $this->assign("commentparent", $id); //为拉评论页面而设置，方便评论时候Parentid 好赋值
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
