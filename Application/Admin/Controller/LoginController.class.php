<?php

namespace Admin\Controller;

use Think\Controller;

/**
 * Description of BaseController
 *
 * @author 赵先方
 */
class LoginController extends Controller {

    private $NopmalType = 0; //普通会员
    private $ModerateType = 1; //论版分类的版主
    private $AdminType = 2; //管理员
    protected $_config = '';

    public function _initialize() {        
        initializeWebSiteConfig();
    }

    /**
     * 登录
     * 赵先方
     * @param type $array
     * @return type
     */
    public function index() {
        if (IS_AJAX) {
            $loginName = I('post.loginName');
            $password = I('post.password');
            if (!$loginName || !$password) {
                $this->ajaxReturn(array('status' => 0, 'info' => '登录名或密码不能为空！'));
            }
            $user = M('User');
            $where['email'] = array('eq', $loginName);
            $where['mobile'] = array('eq', $loginName);
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
            $map['type'] = array('eq', $this->AdminType);
            $userInfo = $user->where($map)->find();            
            if (empty($userInfo)) {
                $this->ajaxReturn(array('status' => 0, 'info' => '登录名或者密码错误！'));
            }
            if ($userInfo['status'] > 0) {
                $newPassword = en_password($password, $userInfo['solt']);
                if ($newPassword != $userInfo['password']) {
                    exit($newPassword . '/' . $userInfo['password']);
                    $this->ajaxReturn(array('status' => 0, 'info' => '登录名或者密码错误！'));
                }
                if ($userInfo['status'] == 3) {
                    $this->ajaxReturn(array('status' => 0, 'info' => '该账号已封！'));
                }
                //保存登录信息
                $this->sign($userInfo);               
                $this->ajaxReturn(array('status' => 1, 'info' => '登录成功！'));
            } else {
                $this->ajaxReturn(array('status' => 0, 'info' => '该账号暂未审核，请耐心等待审核吧！'));
            }
        } else {
            $this->display();
        }
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

    /*
      用户登录数据保存
     */

    protected function sign($userinfo) {
        if (empty($userinfo)) {
            $this->redirect('index');
        } else {
            session('admin', $userinfo);
        }
    }

    public function logout() {
        if (is_login()) {
            session('[destroy]');

            if (IS_AJAX) {
                $this->ajaxReturn(array('status' => 1, 'info' => '退出成功！', 'url' => U('Login/index')));
            } else {
                $this->success('退出成功！', U('index'));
            }
        } else {
            if (IS_AJAX) {
                $this->ajaxReturn(array('status' => 1, 'info' => '你还未登录！', 'url' => U('Login/index')));
            } else {
                $this->redirect('Login/index');
            }
        }
    }



}
