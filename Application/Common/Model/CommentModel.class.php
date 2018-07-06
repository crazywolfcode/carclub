<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Common\Model;

/**
 * ===================================
 * Description of CommentModel File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-24
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class CommentModel extends BaseModel {

    /**
     * @param type $where 查询条件
     * @param type $order 排序
     * @param type $p   页码 默认第一页
     * @param type $pagesize    每页显示多少条 默认6条
     */
    public function getCommentList($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array()) {
        $where = is_array($where) ? $where : array();
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where($where)
                ->alias("c")
                ->field("c.*," . parent::$UserField)
                ->join('LEFT JOIN __USER__ ON c.userid = __USER__.id')
                ->order($order)
                ->limit($limit)
                ->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['replay'] = $this->getCommentReplay($list[$i]['id']);
        }          
        return $list;
    }

    /**
     * 获取评论
     * @param type $type  1关于论版文章 2 关于图集 3关于视频 4关于活动 5关于其它
     * @param type $parentid
     * @param type $page
     * @param type $pagesize
     */
    public function getComment($type, $parentid, $page = 1, $pagesize = 4) {
        $limit = ($page - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("c.type = " . $type . " and parentid = " . $parentid)
                ->limit($limit)
                ->alias("c")
                ->order("addtime DESC")
                ->field("c.*," . parent::$UserField)
                ->join(' LEFT JOIN  __USER__ ON c.userid = __USER__.id')
                ->select();       
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['replay'] = $this->getCommentReplay($list[$i]['id']);
        }
        return $list;
    }

    /**
     * 返回获取评论
     * @param type $type  1关于论版文章 2 关于图集 3关于视频 4关于活动 5关于其它
     * @param type $parentid
     * @param type $page
     * @param type $pagesize
     */
    public function adminGetComment($type = null) {
        $condition['c.type'] = $type;
        $condition['isreplay'] = 0;
        $list = $this->where($condition)
                ->alias("c")
                ->field("c.*," . parent::$UserField)
                ->join('LEFT JOIN __USER__ ON c.userid = __USER__.id')
                ->order("addtime DESC")
                ->select();
        return $list;
    }

    /**
     * 获取评论的回复
     * @param type $parentid 评论id 
     * @return type
     */
    public function getCommentReplay($parentid, $type = null) {
        if ($type) {
            $condition['c.type'] = $type;
        }
        $condition['isreplay'] = 1;
        $condition['parentid'] = $parentid;
        $list = $this->where($condition)
                ->alias(c)
                ->field("c.*," . parent::$UserField)
                ->join('LEFT JOIN __USER__ ON c.userid = __USER__.id')
                ->order("addtime DESC")
                ->select();

        return $list;
    }

}
