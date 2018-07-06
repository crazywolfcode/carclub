<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;
use Think\Controller;
/**
 * Description of PublicColtroller
 *
 * @author Administrator
 */
class PublicController extends Controller{
    //put your code here
    /**
     * 判断是否登录过了
     */
    public function CheckIsLogin(){
        $status = 0;
        $info = '您尚未登录';
        if(is_login_home()){
            $status = 1;
            $info = '您已经登录过了';
        }
        if(IS_AJAX){
            $this->ajaxReturn(array('status'=>$status,'info'=>$info));
        }else{
            ($status == 1)?$this->success($info):$this->error($info);
        }
    }
}
