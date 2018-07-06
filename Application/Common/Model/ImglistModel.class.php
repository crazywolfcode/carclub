<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ===================================
 * Description of ActionModel File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-12
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */

namespace Common\Model;

class ImglistModel extends BaseModel {

//put your code here
    /**
     * 赵先方
     * @param type $where 查询条件
     * @param type $order 排序
     * @param type $p   页码 默认第一页
     * @param type $pagesize    每页显示多少条 默认6条
     */
    public function getImgLists($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array()) {
        $where = is_array($where) ? $where : array();
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where($where)->order($order)->limit($limit)->select();
        return $list;
    }

    /**
     * 按条件获取数量
     * @param type $where
     * @return int
     */
    public function getCount($where) {
        if (!is_array($where)) {
            return 0;
        }
        return $this->where($where)->count();
    }



}
