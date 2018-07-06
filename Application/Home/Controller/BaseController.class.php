<?php

namespace Home\Controller;

use Think\Controller;

/**
 * Description of BaseController
 *
 * @author hacker
 */
class BaseController extends Controller {

    public $TribuneCates;
    public $Barners;
    protected $_config = '';

    public function __construct() {

        if (isMobile()) {
//            $referer = $_SERVER['HTTP_REFERER'];
//            if ($referer) {
//                if (strstr($referer, "Home")) {
//                    $referer = str_replace("Home", "Mobile", $referer);
//                }
//                if (strstr($referer, "home")) {
//                    $referer = str_replace("home", "mobile", $referer);
//                }
//                header("Location:" . $referer);
//            } else {
            $this->redirect('Mobile/Index/index');
//            }
        }
        parent::__construct();
        initializeWebSiteConfig();
    }

    public function _initialize() {
        if (empty($this->TribuneCates)) {
            $this->TribuneCates = D('Common/category')->getCategorys();
        }
        $session = session(C('HOME_SESSION_NAME'));
        $this->assign('catelist', $this->TribuneCates);
        $this->assign('islogin', is_login_home());
        $this->assign('session', $session);

        $config = M('Config'); //实例化配置表
        if (empty($this->_config)) {
            $configInfo = $config->where(array('status' => 1))->order('sort ASC')->select();
            $this->_config = $this->arrToOne($configInfo);
        }
        $this->assign("user_agreements", $this->_config['user_agreements']);
        $this->assignBarnes();
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

    private function assignBarnes() {
        if ($this->Barners) {
            $this->assign("barners", $this->Barners);
            return;
        } else {
            $tempBarner = M('barner')->where('status = 1')->limit(5)->order("rand()")->select();
            $this->Barners = $tempBarner;
            $this->assign("barners", $this->Barners);
        }
    }

}
