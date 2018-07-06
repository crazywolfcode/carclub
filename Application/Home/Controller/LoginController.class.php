<?php

namespace Home\Controller;

use Think\Controller;

require_once(QC_API_PATH . "qqConnectAPI.php");

class LoginController extends Controller {

    //来源 0 正常注册 1微信 2QQ互联
    const USER_OAUTH_TYPE_QQ = 2;
    const USER_OAUTH_TYPE_WX = 1;
    const USER_OAUTH_TYPE_CXTX = 0;
//用户状态；0未审核；1正常;2禁言3封号；
    const USER_STATUS_NOUSER = 0;
    const USER_STATUS_NOMAO = 1;
    const USER_STATUS_GAG = 2;
    const USER_STATUS_FH = 3;

    private $accesToken;
    private $openid;
    private $user_info;

    public function _initialize() {
        initializeWebSiteConfig();
        //用户每登陆成功后，根据积分将对应的等级设置在user 表里
        \Think\Hook::add("login_success", 'Common\\Behaviors\\SetUserLevelBehavior');
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
            //$map['type'] = array('eq', 0);
            $userInfo = $user->where($map)->find();
            if (empty($userInfo)) {
                $this->ajaxReturn(arrayRes(0, "登录名或者密码错误"));
            }
            if ($userInfo['status'] > 0) {
                $newPassword = en_password($password, $userInfo['solt']);
                if ($newPassword != $userInfo['password']) {
                    $this->ajaxReturn(arrayRes(0, "登录名或者密码错误"));
                }
                if ($userInfo['status'] == 3) {
                    $this->ajaxReturn(arrayRes(0, "该账号已封!"));
                }
                //保存登录信息
                $this->sign($userInfo);
                \Think\Hook::listen("login_success", $userInfo);
                if (I("post.type", 0) == 2) {
                    $this->ajaxReturn(arrayRes(1, "登录成功", U('User/index')));
                } else {
                    $this->ajaxReturn(arrayRes(1, "登录成功"));
                }
            } else {
                $this->ajaxReturn(arrayRes(0, "该账号暂未审核，请耐心等待审核吧！"));
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
            return '';
        } else {
            $admininfo = array(
                'id' => $userinfo['id'],
                'name' => $userinfo['name'],
                'head' => $userinfo['head'],
                'department' => $userinfo['department'],
                'status' => $userinfo['status']
            );
            session(C('HOME_SESSION_NAME'), $admininfo);
        }
    }

    public function logout() {
        if (is_login_home()) {
            session(C('HOME_SESSION_NAME'), null);
            session('[destroy]');
            if (IS_AJAX) {
                $this->ajaxReturn(array('status' => 1, 'info' => '退出成功！', 'url' => U('Index/index')));
            } else {
                //  $this->success('退出成功！', U('Index/index'));
                $this->redirect('Index/index');
            }
        } else {
            session(C('HOME_SESSION_NAME'), null);
            session('[destroy]');
            if (IS_AJAX) {
                $this->ajaxReturn(array('status' => 1, 'info' => '你还未登录！', 'url' => U('Index/index')));
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
        if (is_login_home()) {
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
     * 用户注册
     */
    public function register() {
        if (C('USER_ALLOW_REGISTER') != '1') {
            $this->error('注册已经关闭');
        }
        if (IS_AJAX) {
            $protocol = I('post.protocol');
            if ($protocol != 'on') {
                $this->error('请先勾选同意注册用户协议');
            }
            $password = I('post.password');
            $againpassword = I('post.againpassword');

            $solt = rend_char(6);
            $data['password'] = en_password($password, $solt);
            $data['solt'] = $solt;
            I('post.name') ? ($data['name'] = I('post.name')) : $this->error('请填写姓名');
            $data['reg_time'] = time();
            $data['type'] = '0';
            $data['status'] = '1';
            I('post.email') ? ($data['email'] = I('post.email')) : $this->error('请填写邮箱号');
            if (!check_email(I('post.email'))) {
                $this->error('邮箱格式错误');
            }
            I('post.mobile') ? ($data['mobile'] = I('post.mobile')) : $this->error('请填写手机号');
            if (!check_mobile(I('post.mobile'))) {
                $this->error('手机号码格式错误');
            }
            (I('post.gender') == 0 || I('post.gender') == 1) ? ($data['gender'] = I('post.gender')) : $this->error('请选择您的性别');
            if ($password !== $againpassword || empty($password)) {
                $this->error('两次输入密码不一致');
            }
            $User = M('User');
            $checkMobile = $User->where(array('mobile' => I('post.mobile')))->find();
            if ($checkMobile) {
                $this->error('该手机号已经被注册，不可重复注册');
            }
            $checkEmail = $User->where(array('email' => I('post.email')))->find();
            if ($checkEmail) {
                $this->error('该邮箱号已经被注册，不可重复注册');
            }
            $add = $User->add($data);
            if ($add === FALSE) {
                $this->error('注册失败！请重试');
            } else {
                //注册成功自动登陆
                $this->autologin($User->getLastInsID());
                //增加注册积分 
                addUserPoint($User->getLastInsID(), C('REG_SEND_POINT'));
                $this->success('注册成功！欢迎加入' . C('WEB_SITE_TITLE'));
            }
        } else {
            $this->error('非法请求');
        }
    }

    private function autologin($userid) {
        $userinfo = M('user')->find($userid);
        $info = array(
            'id' => $userinfo['id'],
            'name' => $userinfo['name'],
            'head' => $userinfo['head'],
            'department' => $userinfo['department']
        );
        session(C('HOME_SESSION_NAME'), $info);
    }

    /**
     * 登陆方式2 
     * 用于修改密码后的修改
     * todo 当用户oppenid时要修改 
     */
    public function logintwo() {
        $temp = session(C("HOME_SESSION_NAME"));
        $user['head'] = $temp['head'];
        $user['email'] = $temp['email'];
        $user['mobile'] = $temp['mobile'];
        $user['oppendid'] = $temp['oppendid'];
        session(C('HOME_SESSION_NAME'), null);
        session('[destroy]');
        if ($user['mobile']) {
            $user['loginName'] = $user['mobile'];
        } else {
            $user['loginName'] = $user['email'];
        }
        $this->assign("user", $user);
        $this->display();
    }

    /**
     * 显示QQ互联登录的界面
     */
    public function qclogin() {
        //记录点击来的地址，登陆成功后跳转回去
        $logintype = I('get.logintype', 'pc');
        session('loginType', $logintype);
        session('targetURL', $_SERVER['HTTP_REFERER']);
        $Oauth = new \Oauth();
        $Oauth->qq_login();
    }

    /**
     * 显示QQ互联返回的信息处理
     */
    public function qcback() {
        $Oauth = new \Oauth();
        $this->accesToken = $Oauth->getAccesToken();
        $this->openid = $Oauth->get_openid();
        if (!$this->openid) {
            exit("get_openid error");
        }
        $this->redirect("accountManager", array('openid' => $this->openid, 'accesToken' => $this->accesToken,));
    }

    /**
     * 账号管理 的方法 
     * 判断是否要写入数据
     * 判断是否要进行判定
     */
    public function accountManager() {
        $map['qq_openid'] = $openid = I("get.openid");
        if (!$openid) {
            $this->redirect('qclogin');
        }
        $map['oauth'] = self::USER_OAUTH_TYPE_QQ;
        $localUser = M('user')->where($map)->find();
        if ($localUser) {
            if ($localUser['status'] == self::USER_STATUS_FH) {
                //用户被封号 ，跳转到提示页面，再回首页
                $this->error("用户被封号", U('Index/index'), 3);
                exit();
            } else {
                ThirdLogin($openid, "qq");
                if (session('loginType') == 'pc') {
                    header("Location:" . session('targetURL'));
                } else {
                    $this->redirect('Mobile/Index/index');
                }
            }
        } else {
            //用户不存在本地。去开放平台获取          
            $accesToken = I('get.accesToken');
            $QC = new \QC($accesToken, $openid);
            $this->user_info = $QC->get_user_info();
            //跳转到绑定页面      
            $temp_user = $this->user_info;
            $temp_user['openid'] = $openid;
            session('temp_user',$temp_user);           
            $logintype = session('loginType');
            if ($logintype == 'mobile') {
                $this->redirect("Mobile/Login/binding");
            } else {
                $this->redirect("binding");
            }
        }
    }

    /**
     * 绑定用户信息
     * 
     */
    public function binding() {
        if (IS_POST) {
            $data = I('post.');
            $User = M('user');
            $userid = $data['id'];
            $res = 0;
            if ($userid) {
                //更新
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
                //增加
                $data['oauth'] = 2;
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
            if (!$user) {
                $this->redirect("Index/index");
            }
            $user['oauth'] = 2;
            $user['openid'] = session('QC_user.openid');
            session('temp_user', NULL);
            $this->assign("user", $user);
            $this->display();
        }
    }

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
