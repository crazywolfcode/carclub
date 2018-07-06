<?php

namespace Mobile\Controller;

/**
 * Description of CollectController
 *
 * @author Crazycode
 * @date 2017-02-22 06:30:17
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class CollectController extends BaseController {

//类型 1活动 2视频 3文章4图集
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 收藏 取消收藏
     */
    public function add() {
        //前面要传的值 userid type parentid option
        if (IS_AJAX) {
            $data = I("post.");
            //$option 收藏或者是取消收藏的操作标识
            $option = $data['option'];
            $type = $data['type'];
            unset($data['option']);
            if (isCollected($data['parentid'], $type, $data['userid'])) {
                $this->ajaxReturn(arrayRes(1, "收藏成功"));
                exit();
            }
            if ($option == 1) {
                $data['addtime'] = time();
                $data['remark'] = "手机收藏";
                $res = M('collect')->add($data);
                if ($res) {
                    //增加收藏内容的被收藏数
                    try {
                        addCollects($type, $data['parentid']);
                    } catch (Exception $exc) {
                        
                    }
                    $this->ajaxReturn(arrayRes(1, "收藏成功"));
                } else {
                    $this->ajaxReturn(arrayRes(1, "收藏失败"));
                }
            } else {
                $where['parentid'] = $data['parentid'];
                $where['userid'] = $data['userid'];
                $where['type'] = $type;
                $res = M('collect')->where($where)->delete();
                if ($res) {
                    $this->ajaxReturn(arrayRes(1, "取消收藏成功"));
                } else {
                    $this->ajaxReturn(arrayRes(1, "取消收藏失败"));
                }
            }
        }
    }

    /**
     * 删除收藏
     */
    public function delete() {
        if (IS_AJAX) {
            $id = I("get.id", 0);
            if ($id > 0) {
                $res = M('collect')->where("id= " . $id)->delete();
                if ($res) {
                    $this->ajaxReturn(arrayRes(1, "删除成功"));
                } else {
                    $this->ajaxReturn(arrayRes(0, "删除失败"));
                }
            } else {
                $this->ajaxReturn(arrayRes(0, "删除失败，数据异常"));
            }
        }
    }

}
