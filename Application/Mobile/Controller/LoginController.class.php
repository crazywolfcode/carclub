<?php

namespace Mobile\Controller;

use Think\Controller;

/**
 * ===================================
 * Description of LoginController File
 * @function:手机登陆
 * @author: hacker-陈龙飞
 * @Date: 2017-2-8
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class LoginController extends Controller {

    public function _initialize() {
        initializeWebSiteConfig();
        //用户每登陆成功后，根据积分将对应的等级设置在user 表里
        \Think\Hook::add("login_success", 'Common\\Behaviors\\SetUserLevelBehavior');
    }

//put your code here
    public function login() {
        if (IS_AJAX) {
            $loginName = I('post.emailormobile');
            $password = I('post.password');
            if (!$loginName || !$password) {
                $this->ajaxReturn(array('status' => 0, 'info' => '登录名或密码不能为空！'));
            }
            $user = M('User');
            $where['email'] = array('eq', $loginName);
            $where['mobile'] = array('eq', $loginName);
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
            //$map['type'] = array('eq', 0);
            $userInfo = $user->where($map)->find();
            if (empty($userInfo)) {
                $this->ajaxReturn(array('status' => 0, 'info' => '登录名或者密码错误！'));
            }
            if ($userInfo['status'] > 0) {
                $newPassword = en_password($password, $userInfo['solt']);
                if ($newPassword != $userInfo['password']) {
                    $this->ajaxReturn(array('status' => 0, 'info' => '登录名或者密码错误!'));
                }
                if ($userInfo['status'] == 3) {
                    $this->ajaxReturn(array('status' => 0, 'info' => '该账号已封！'));
                }
                //保存登录信息
                session('admin', $userInfo);
                \Think\Hook::listen("login_success", $userInfo);
                $this->ajaxReturn(array('status' => 1, 'info' => '登录成功！'));
            } else {
                $this->ajaxReturn(array('status' => 0, 'info' => '该账号暂未审核，请耐心等待审核吧！'));
            }
        } else {
            $this->assign("topTwoTitle", "登录");
            $this->display();
        }
    }

    public function reg() {
        $this->assign("topTwoTitle", "用户注册");
        $this->display();
    }

    public function forgetPwd() {
        $this->assign("topTwoTitle", "找回密码");
        $this->display();
    }

    public function resetPwd() {
        $this->assign("topTwoTitle", "重置密码");
        $this->display();
    }

    public function logout() {
        if (is_login_mobile()) {
            session('admin', null);
            session('[destroy]');
            if (IS_AJAX) {
                $this->ajaxReturn(array('status' => 1, 'info' => '退出成功！', 'url' => U('Index/index')));
            } else {
                // $this->success('退出成功！', U('Index/index'));
                $this->redirect('Index/index');
            }
        } else {
            session('admin', null);
            session('[destroy]');
            if (IS_AJAX) {
                $this->ajaxReturn(array('status' => 1, 'info' => '你还未登录！', 'url' => U('Login/login')));
            } else {
                $this->redirect('Index/index');
            }
        }
    }

    /**
     * 判断是否登录过了
     */
    public function CheckIsLogin() {
        $status = 0;
        $info = '您尚未登录';
        if (is_login_mobile()) {
            $status = 1;
            $info = '您已经登录过了';
        }
        if (IS_AJAX) {
            $this->ajaxReturn(array('status' => $status, 'info' => $info));
        } else {
            ($status == 1) ? $this->success($info) : $this->error($info);
        }
    }

    /**
     * 绊定页面
     * 
     */
    public function binding() {
        if (IS_POST) {
            $data = I('post.');
            $User = M('user');
            $userid = $data['id'];
            $res = 0;
            if ($userid) {
                unset($data['id']);
                unset($data['emailormobile']);
                $tempUser = $User->where("id = " . $userid)->find();
                $solt = $tempUser['solt'];
                $newPwd = en_password($data['password'], $solt);
                if ($tempUser['password'] != $newPwd) {
                    $this->ajaxReturn(arrayRes(0, "绑定失败，密码错误！"));
                }
                unset($data['password']);
                if ($User->where('id = ' . $userid)->save($data)) {
                    $res = 1;
                }
                refalshSession($userid);
            } else {
                if (check_email($data['emailormobile'])) {
                    $data['email'] = $data['emailormobile'];
                }
                if (check_mobile($data['emailormobile'])) {
                    $data['mobile'] = $data['emailormobile'];
                }
                $data['solt'] = $solt = rend_char(6);
                $data['password'] = en_password($data['password'], $solt);
                $data['reg_time'] = time();
                $data['status'] = 1;
                unset($data['emailormobile']);
                $res = $lastUserid = $User->add($data);
                refalshSession($lastUserid);
            }
            $this->ajaxReturn(arrayRes($res));
        } else {
            $user = session('temp_user');           
            session('temp_user', NULL);
            if (!$user) {
                $this->redirect("Index/index");
            }
            $this->assign("user", $user);
            $this->display();
        }
    }

    /**
     * 刷新 Session 可传Userid 或者 UserInfor
     * @param type $userid
     * @param type $userinfo
     */

    /**
     * 绑定的时候检查账号是否存在
     */
    public function checkAccount() {
        $emailormobile = I('post.emailormobile');
        if (!$emailormobile) {
            $this->ajaxReturn(arrayRes(-1, "没有传入邮箱或者是手机号码"));
        }
        $User = M('user');
        if (check_email($emailormobile)) {
            $map['email'] = $emailormobile;
        } else if (check_mobile($emailormobile)) {
            $map['mobile'] = $emailormobile;
        } else {
            $this->ajaxReturn(arrayRes(-1, "所输入的邮箱或者手机号码格式不正确！"));
        }
        $user = $User->where($map)->find();
        if ($user) {
            $this->ajaxReturn(arrayRes(1, "用户已经存在，请输入密码进行绑定！", null, $user['id']));
        } else {
            $this->ajaxReturn(arrayRes(0, "用户不存在，请输入两次密码注册新的账号！"));
        }
    }

}
