<?php
namespace Common\Model;

/**
 * ===================================
 * Description of UserModel File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-24
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class UserModel extends BaseModel{

    /**
     * @param type $order 查询条件
     * @param type $p 排序
     * @param type $pagesize   页码 默认第一页
     * @param type $where    每页显示多少条 默认6条
     */
    public function getUserList($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array()) {
        $where = is_array($where) ? $where : array();
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where($where)->order($order)->limit($limit)->select();     
        return $list;
    }

    /**
     * 获取最新的12个用户
     * @return type
     */
    public function getNew($page = 1, $pagesize = 5) {
        $limit = ($page - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("type=0 and status = 1")->order("reg_time DESC")->limit($limit)->select();    
        return $list;
    }

    /*
     * 第三方登录
     */

    public function thirdLogin($data = array()) {
        $openid = $data['openid']; //第三方返回唯一标识
        $oauth = $data['oauth']; //来源
        if (!$openid || !$oauth)
            return array('status' => -1, 'msg' => '参数有误', 'result' => '');
        //获取用户信息
        $user = get_user_info($openid, 3, 1);
        if (!$user) {
            //账户不存在 注册一个
            $map['password'] = '';
            $map['openid'] = $openid;
            $map['name'] = $data['nickname'];
            $map['reg_time'] = time();
            $map['oauth'] = $oauth;
            $map['head'] = $data['head_pic'];
            $map['gender'] = $data['sex'];
            $row = M('users')->add($map);
            $user = get_user_info($openid, 3, $oauth);
        }
        $dat['last_login'] = time();
        $where['openid'] = $openid;
        M('users')->where($where)->save($dat);
        return array('status' => 1, 'msg' => '登陆成功', 'result' => $user);
    }

    /*
     * 依据条件获取数量
     * 
     */

    public function getCountByWhere($where) {
        $count = $this->where($where)->count();
        return $count;
    }

}
