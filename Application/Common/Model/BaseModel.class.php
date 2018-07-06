<?php

namespace Common\Model;

use Think\Model;

/**
 * Description of BaseModel
 *
 * @author Crazycode
 * @date 2017-02-21 02:35:49
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
class BaseModel extends Model {

    public static $UserField = "user.id as uid,"
            . "user.qq_nick_name as uqqnickname,"
            . "user.oauth as uoauth,"
            . "user.wx_nick_name as uwxnickname,"
            . "user.name as uname,"
            . "user.wx_head as uwxhead,"
            . "user.qq_head as uqqhead,"
            . "user.level as ulevel,"
            . "user.oauth as uoauth,"
            . "user.point as upoint,"
            . "user.type as utype,"
            . "user.gender as ugender,"
            . "user.level_icon as ulevelicon,"
            . "user.status as ustatus";

}