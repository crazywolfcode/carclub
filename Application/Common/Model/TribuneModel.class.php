<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Common\Model;

/**
 * ===================================
 * Description of TribuneModel File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-17
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class TribuneModel extends BaseModel {

    /**
     * @param type $where 查询条件
     * @param type $order 排序
     * @param type $p   页码 默认第一页
     * @param type $pagesize    每页显示多少条 默认6条
     */
    public function getTribuneList($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array()) {
        $where = is_array($where) ? $where : array();
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where($where)
                ->order($order)
                ->limit($limit)
                ->alias("t")
                ->field("t.*," . parent::$UserField)
                ->join('LEFT JOIN __USER__ ON t.adduserid = __USER__.id')
                ->select();
        return $list;
    }

    public function addTribune($data) {
        if (!$data) {
            return 0;
        }
        $res = $this->add($data);
        return $res;
    }

    public function updateTribune($data) {
        if (!$data) {
            return 0;
        }
        $res = $this->where('id = ' . $data['id'])->save($data);
        return $res;
    }

    public function getTribunebyCate($cateid = 1, $p = 1, $pagesize = 9) {
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("t.cateid = " . $cateid . " and isdelete = 0 and t.status= 1 and t.type = 1")
                ->limit($limit)
                ->alias("t")
                ->field("t.*," . parent::$UserField)
                ->order('addtime DESC')
                ->join('LEFT JOIN __USER__ ON t.adduserid = __USER__.id')
                ->select();
        return $list;
    }

    public function getTribunebyType($type = 1, $p = 1, $pagesize = 9) {
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("t.type= " . $type . " and isdelete = 0 and t.status= 1")
                ->limit($limit)
                ->alias("t")
                ->field("t.*," . parent::$UserField)
                ->join('__USER__ ON t.adduserid = __USER__.id')
                ->select();
        return $list;
    }

    public function getNew($page = 1, $pagesize = 5) {
        $limit = ($page - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("status=1 and isdelete=0 and type = 1 ")->order("addtime DESC")->limit($limit)->select();
        return $list;
    }

    /**
     * @param type $where 查询条件
     * @param type $order 排序
     * @param type $p   页码 默认第一页
     * @param type $pagesize    每页显示多少条 默认6条
     */
    public function getTribuneListWitchUser($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array()) {
        $where = is_array($where) ? $where : array();
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where($where)
                ->limit($limit)
                ->alias("t")
                ->field("t.*," . parent::$UserField)
                ->order($order)
                ->join('LEFT JOIN  __USER__ ON t.adduserid = __USER__.id')
                ->select();
        return $list;
    }

}
