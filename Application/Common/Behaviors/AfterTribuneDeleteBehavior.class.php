<?php

namespace Common\Behaviors;

/**
 * Description of AfterTribuneDeleteBehavior
 * 当论版文章被删除成功，要删除基评论和评论的回复 
 * @author Crazycode
 * @date 2017-02-22 02:14:01
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class AfterTribuneDeleteBehavior extends \Think\Behavior {

    public function run(&$params) {
        //1.删除评论
        $where = $params;
        $list = M('comment')->where($where)->select();
        $ids = array();
        for ($i = 0; $i < count($list); $i++) {
            $ids[$i] = $list[$i]['id'];
            $res = M('comment')->where("id = " . $ids[$i])->save(array('isdelete' => 1));
            if (!$res) {
                unset($ids[$i]);
            }
        }
        WriteLog("删除｛count($list)｝条评论");
        //2.删除回复 
        for ($i = 0; $i < count($ids); $i++) {
            $res = M('comment')->where("parentid = " . $ids[$i] . " and type =" . $where['type'] . " and isreplay = 1")->save(array('isdelete' => 1));
            WriteLog("删除回复");
        }
    }

}
