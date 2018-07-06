<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Common\Model;

use Think\Model;

/**
 * Description of VideoModel
 *
 * @author Administrator
 */
class VideoModel extends BaseModel {

    /**
     * 赵先方
     * @param type $where 查询条件
     * @param type $order 排序
     * @param type $p   页码 默认第一页
     * @param type $pagesize    每页显示多少条 默认6条
     */
    public function getVideoList($order = 'addtime DESC', $p = 1, $pagesize = 6, $where = array()) {
        $where = is_array($where) ? $where : array();
        $where = array_merge($where, array('isdelete' => 0));
        if (!$p || !$pagesize) {
            return array('status' => 0, 'info' => '传入参数错误！');
        }
        if ($p == '-1') {//统计条数
            $list = $this->where($where)->count();
        } else {
            $limit = ($p - 1) * $pagesize . ',' . $pagesize;
            $list = $this->where($where)->order($order)->limit($limit)->select();
            \Think\Log::write($this->getLastSql());
        }
        if (empty($list)) {
            return array('status' => 0, 'info' => '没有数据！', 'data' => $list);
        } else {
            return array('status' => 1, 'info' => '查询成功！', 'data' => $list);
        }
    }

    /**
     * 获取活动中的图片 
     * @param type $aid 活动的id 
     * @return type
     */
    public function getActionvideos($aid) {
        if (!$aid) {
            return null;
        }
        $list = $this->where("isshow=1 and isdelete=0 and type = 1 and action_id = " . $aid)->limit(5)->select();
        return $list;
    }

    /**
     * 获取最新的5个视频
     * @return type
     */
    public function getNew($page = 1, $pagesize = 5) {
        $limit = ($page - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("isshow=1 and isdelete=0 and type = 1 ")->order("addtime DESC")->limit($limit)->select();
        return $list;
    }

    /**
     * 获取热门视频 5 个 查看数的DESC
     * @return type
     */
    public function getHotVideos($page = 1, $pagesize = 12) {
        $limit = ($page - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("isshow=1 and isdelete=0 and type = 1 ")->order("views DESC, addtime DESC")->limit($limit)->select();
        return $list;
    }

    /**
     * 随机获取指定的条数
     * @param type $num 指定条数
     * @return type
     */
    public function getRands($num = 3) {
        return $this->where("isshow = 1 and isdelete = 0")->order("RAND()")->limit($num)->select();
    }

    public function getVideosbyType($order = "rand()", $type = 1, $p = 1, $pagesize = 9) {
        $limit = ($p - 1) * $pagesize . ',' . $pagesize;
        $list = $this->where("type= " . $type . " and isdelete = 0 and isshow= 1")
                ->order($order)
                ->limit($limit)
                ->select();
        return $list;
    }

}
