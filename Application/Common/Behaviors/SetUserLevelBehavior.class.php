<?php

namespace Common\Behaviors;

use Think\Behavior;

/**
 * Description of SetUserLevelBehaviors
 * 用户每登陆成功后，根据积分将对应的等级设置在user 表里
 * 用户禁言时间到自动取消禁言
 * 更新Session
 * @author Crazycode
 * @date 2017-02-17 04:34:00
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class SetUserLevelBehavior extends Behavior {

    private $logString = "login-success--- SetUserLevelBehavior--";

    public function run(&$user) {
        //1.设置级别
        $this->setLevel($user);
        //2.取消禁言
        $this->cancelGag($user);
        //3.更新用户信息        
       $ip= $user['last_login_ip'] = get_client_ip(0, TRUE);
        $user['last_login_time'] = time();
        //登陆赠送积分
        $user['point'] = $user['point'] + C("LOGIN_SEND_POINT");
        //浏览用户的所在城市呢？ 
        $res = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=$ip");
        $res = json_decode($res,TRUE);
        WriteLog($res["country"]);
        WriteLog($res["province"]);
        WriteLog($res["city"]);
        $this->updateUserInfor($user);
        //4.更新Session        
        session(C("HOME_SESSION_NAME"), $user);       
    }

    /**
     * 设置级别
     * @param type $userid
     */
    public function setLevel(&$user) {
        $userid = $user['id'];
        $levelname;
        $point = $user['point'];
        $userlevel = M('userlevel')->order("pointline DESC")->where("isdelete = 0")->select();
        if (!$userlevel) {
            return;
        }
        foreach ($userlevel as $level) {
            if ($point >= $level['pointline']) {
                $levelname = $level['name'];
                $user['level'] = $levelname;
                $user['level_icon'] =  $level['icon'];
                break;
            }
        }
        \Think\Log::write($this->logString . "设置级别成功");
    }

    public function cancelGag(&$user) {
        if ($user['status'] == 2) {
            $gag = M('usergag')->where("userid = " . $user['id'])->find();
            if ($gag) {
                $time = time();
                if ($gag['endtime'] > $time) {
                    //时间已经过删除记录
                    $user['status'] = 1;
                    M('usergag')->where("userid = " . $user['id']) - delete();
                    \Think\Log::write($this->logString . "取消禁言成功");
                }
            }
        }
    }

    public function updateUserInfor(&$user) {
        $map['id'] = $user['id'];        
        M('user')->where($map)->save($user);
        \Think\Log::write($this->logString . "更新用户信息成功");
    }

}
