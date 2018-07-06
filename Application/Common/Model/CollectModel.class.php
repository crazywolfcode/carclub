<?php

namespace Common\Model;

/**
 * Description of CollectModel
 *
 * @author Crazycode
 * @date 2017-02-22 04:51:16
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class CollectModel extends BaseModel {

    /**
     * 得到收藏列表
     * @param type $order
     * @param type $p
     * @param type $pageSize
     * @param type $where
     * @return type
     */
    public function getCollectList($order = "addtime DESC", $p = 1, $pageSize = 8, $where = array()) {
        $where = is_array($where) ? $where : array();
        $limit = ($p - 1) * $pageSize . ',' . $pageSize;
        $list = $this->where($where)->order($order)->limit($limit)->select();
        return $list;
    }

}
