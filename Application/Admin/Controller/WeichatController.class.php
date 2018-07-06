<?php

namespace Admin\Controller;

/**
 * Description of WeichatController
 *
 * @author Crazycode
 * @date 2017-04-18 11:26:40
 * @copyright
 * @version V1.0
 * @connection 18087467482|QQ443055589
 * @home 
 */
require_once (WP_API_PATH . "class/Wpublic.class.php");
require_once (WP_API_PATH . "class/Http.class.php");
require_once (WP_API_PATH . "class/Error.class.php");

class WeichatController extends BaseController {

    public function menu() {
        if (IS_POST) {
            $post_menu = $_POST['menu'];
            //查询数据库是否存在
            $menu_list = M('wxmenu')->field("id")->select();
            foreach ($post_menu as $k => $v) {
                if (in_array($k, $menu_list)) {
                    //更新
                    M('wxmenu')->where(array('id' => $k))->save($v);
                } else {
                    //插入
                    M('wxmenu')->where(array('id' => $k))->add($v);
                }
            }
            $this->success('操作成功,请点击发布菜单');
            exit;
        }
        //获取最大ID      
        $max_id = M()->query("SHOW TABLE STATUS WHERE NAME = '__PREFIX__wxmenu'");
        $max_id = $max_id[0]['auto_increment'];

        //获取父级菜单
        $p_menus = M('wxmenu')->where(array('pid' => 0))->order('id ASC')->select();
        $p_menus = $this->convert_arr_key($p_menus, 'id');
        //获取二级菜单
        $c_menus = M('wxmenu')->where(array('pid' => array('gt', 0)))->order('id ASC')->select();
        $c_menus = $this->convert_arr_key($c_menus, 'id');
        $this->assign('p_lists', $p_menus);
        $this->assign('c_lists', $c_menus);
        $this->assign('max_id', $max_id ? $max_id - 1 : 0);
        $this->display();
    }

    /*
     * 删除菜单
     */

    public function del_menu() {
        $id = I('get.id');
        if (!$id) {
            exit('fail');
        }
        $row = M('wxmenu')->where(array('id' => $id))->delete();
        $row && M('wxmenu')->where(array('pid' => $id))->delete(); //删除子类
        if ($row) {
            exit('success');
        } else {
            exit('fail');
        }
    }

    /*
     * 生成微信菜单
     * 
     */

    public function pub_menu() {
        //获取菜单
        //获取父级菜单
        $p_menus = M('wxmenu')->where(array('pid' => 0))->order('id ASC')->limit(3)->select();
        $p_menus = $this->convert_arr_key($p_menus, 'id');
        $post_str = $this->convert_menu($p_menus);
        // http post请求
        if (!count($p_menus) > 0) {
            $this->error('没有菜单可发布', U('Weichat/menu'));
            exit;
        }
        $Wpublic = new \Wpublic();
        $access_token = $Wpublic->getAccess_token();
        if (!$access_token) {
            $this->error('获取access_token失败');
            exit;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        $return = \Http::httpRequest($url, 'POST', $post_str);
        $return = json_decode($return, 1);
        if ($return['errcode'] == 0) {
            $this->success('菜单已成功生成', U('Weichat/menu'));
        } else {
            $Error = new \Error();
            $Error->showError($return['errcode']);
            exit;
        }
    }

    //菜单转换
    private function convert_menu($p_menus) {
        $key_map = array(
            'scancode_waitmsg' => 'rselfmenu_0_0',
            'scancode_push' => 'rselfmenu_0_1',
            'pic_sysphoto' => 'rselfmenu_1_0',
            'pic_photo_or_album' => 'rselfmenu_1_1',
            'pic_weixin' => 'rselfmenu_1_2',
            'location_select' => 'rselfmenu_2_0',
        );
        $new_arr = array();
        $count = 0;
        foreach ($p_menus as $k => $v) {
            $new_arr[$count]['name'] = $v['name'];

            //获取子菜单
            $c_menus = M('wxmenu')->where(array('pid' => $k))->limit(5)->order("id ASC")->select();

            if ($c_menus) {
                foreach ($c_menus as $kk => $vv) {
                    $add = array();
                    $add['name'] = $vv['name'];
                    $add['type'] = $vv['type'];
                    // click类型
                    if ($add['type'] == 'click') {
                        $add['key'] = $vv['value'];
                    } elseif ($add['type'] == 'view') {
                        $add['url'] = $vv['value'];
                    } else {
                        $add['key'] = $key_map[$add['type']];
                    }
                    $add['sub_button'] = array();
                    if ($add['name']) {
                        $new_arr[$count]['sub_button'][] = $add;
                    }
                }
            } else {
                $new_arr[$count]['type'] = $v['type'];
                // click类型
                if ($new_arr[$count]['type'] == 'click') {
                    $new_arr[$count]['key'] = $v['value'];
                } elseif ($new_arr[$count]['type'] == 'view') {
                    //跳转URL类型
                    $new_arr[$count]['url'] = $v['value'];
                } else {
                    //其他事件类型
                    $new_arr[$count]['key'] = $key_map[$v['type']];
                }
            }
            $count++;
        }
        return json_encode(array('button' => $new_arr), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $arr
     * @param $key_name
     * @return array
     * 将数据库中查出的列表以指定的 id 作为数组的键名 
     */
    function convert_arr_key($arr, $key_name) {
        $arr2 = array();
        foreach ($arr as $key => $val) {
            $arr2[$val[$key_name]] = $val;
        }
        return $arr2;
    }

}
