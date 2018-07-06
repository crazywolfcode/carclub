<?php

namespace Admin\Controller;

/**
 * Description of ManagerController
 *
 * @author Crazycode
 * @date 2017-03-02 08:53:02
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class ManagerController extends BaseController {

    private $pageSize = 9;
    private $currPage = 1;

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $type = I('get.type', 1);
        $where['type'] = $type;
        $p = I('get.p', 1);
        if ($type == 2) {
            //管理员
            $list = M('admin')->field('userid')->select();
            for ($i = 0; $i < count($list); $i++) {
                $ids[$i] = $list[$i]['userid'];
            }
            if ($list) {
                $where['id'] = array('in', array_unique($ids));
                $count = D('Common/user')->getCountByWhere($where);
                parent::newpage(count, $this->pageSize);
                $userList = D('Common/user')->getUserList($order = 'last_login_time DESC', $p, $this->pageSize, $where);
            }
        } else {
            //版主            
            $list = M('moderate')->field('userid,cateid')->page($p, $this->pageSize)->select();
            if ($list) {
                parent::newpage(count($list), $this->pageSize);
                for ($i = 0; $i < count($list); $i++) {
                    $ulist = M('user')->where("id =" . $list[$i]['userid'])->find();
                    $ulist['cateid'] = $list[$i]['cateid'];
                    $userList[$i] = $ulist;
                }
            }
        }
        $this->assign("userlist", $userList);
        $this->display();
    }

}
