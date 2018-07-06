<?php

/**
 * ===================================
 * Description of function File
 * @function:
 * @author: hacker-陈龙飞
 * @Date: 2017-2-15
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 赵先方
 * @time 2015/12/3
 */
function is_login_mobile() {
    return session(C('HOME_SESSION_NAME')) ? TRUE : FALSE;
}
