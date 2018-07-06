<?php
namespace Common\Model;

/**
 * ===================================
 * Description of ImagesModel File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-1-24
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
class ImagesModel extends BaseModel {

//put your code here
    /**
     * 获取活动中的图片 
     * @param type $aid 活动id
     * @param type $limit
     * @param type $order
     * @return type
     */
    public function getActionimgs($aid, $limit = 5, $order = null) {
        if (!$aid) {
            return null;
        }
        $list = $this->where("isshow=1 and isdelete=0 and type = 1 and action_id = " . $aid)->limit($limit)->order($order)->select();
        return $list;
    }

    /**
     * 后台 获取图集中的图片 
     * @param type $lid 
     * @return type
     */
    public function getListimgs($lid, $limit = null,$order = null) {
        if (!$lid) {
            return null;
        }
        $list = $this->where("isshow=1 and isdelete=0 and type =2 and list_id = " . $lid)              
                ->order($order)
                ->limit($limit)
                ->select();
        return $list;
    }

    /**
     * 获取最新的20张图片
     * @return type
     */
    public function getNew() {
        $list = $this->where("isshow=1 and isdelete=0 and type = 1 ")->order("addtime DESC")->limit(20)->select();
        return $list;
    }

    /**
     * 获取1张图片
     * @return type
     */
    public function getOne($type, $parentid) {
        if ($type == 1) {
            $temp = $this->where("type = " . $type . " and action_id = " . $parentid)->field("id,type,url")->find();
        } else {
            $temp = $this->where("type = " . $type . " and list_id = " . $parentid)->field("id,type,url")->find();
        }
        return $temp;
    }

    /**
     * 按条件获取数量
     * @param type $where
     * @return int
     */
    public function getAllImgByWhere($where, $field = null) {
        if (!is_array($where)) {
            return null;
        }
        return $this->where($where)->field($field)->select();
    }

}
