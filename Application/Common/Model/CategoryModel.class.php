<?php

namespace Common\Model;

/**
 * ===================================
 * Description of CategoryModel File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-17
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class CategoryModel extends BaseModel {

    /**
     * @param type $order 
     * @param type $p 
     * @param type $pagesize   
     * @param type $where  
     */
    public function getCategoryList($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array()) {
        $where = is_array($where) ? $where : array();
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where($where)->order($order)->limit($limit)
                ->alias("cate")
                ->field("cate.*," . parent::$UserField)
                ->join('LEFT JOIN __MODERATE__ ON cate.id = __MODERATE__.cateid')
                ->join('LEFT JOIN __USER__ ON __MODERATE__.userid = __USER__.id')
                ->select();
        return $list;
    }

    public function getCategorys() {
        $list = $this->where("type = 1 and isshow =1")->select();
        return $list;
    }

    public function getCategoryNameByID($id) {
        $cate = $this->where("id = " . $id)->field("name")->find();
        return $cate['name'];
    }

}
