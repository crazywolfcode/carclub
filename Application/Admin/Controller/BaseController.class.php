<?php

namespace Admin\Controller;

use Think\Controller;

/**
 * Description of BaseController
 *
 * @author 赵先方
 */
class BaseController extends Controller {

    public $TribuneCates;
    private $UserLevel;

    public function _initialize() {
        //到后面再打开登录
        if (!is_login()) {
            $this->error('您未登录，请先登录后再操作', U('Login/index'));
        }
        initializeWebSiteConfig();
        //获取论版的分类
        if (empty($this->TribuneCates)) {
            $this->TribuneCates = D('Common/category')->getCategorys();
        }
        $this->assign('catelist', $this->TribuneCates);
        //获取会员等级
        if (empty($this->UserLevel)) {
            $this->UserLevel = M('userlevel')->select();
        }
        $this->assign('userLevellist', $this->UserLevel);
        $en_str = C('WEB_ENCODE'); 
    }

    /**
     * 配置的二维数组转一维
     * 赵先方
     * @param type $array
     * @return type
     */
    protected function arrToOne($array) {
        $arr = array();
        foreach ($array as $key => $val) {
            $arr[$val['name']] = $val['value'];
        }
        return $arr;
    }

    /**
     * 生成分页并赋值页面
     * @param type $count
     * @param type $pageSize
     */
    public function newpage($count, $pageSize) {
        $Page = new \Think\Page($count, $pageSize);
        $Page->setConfig("prev", "上一页");
        $Page->setConfig("next", "下一页");
        $this->assign("page", $Page->show());
    }

}
