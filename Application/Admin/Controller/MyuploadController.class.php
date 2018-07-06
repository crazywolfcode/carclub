<?php

namespace Admin\Controller;

/**
 * ===================================
 * Description of uplad File
 * @function:上传类
 * @author: hacker-陈龙飞
 * @Date: 2017-1-17
 * @Phone:18087467482|13769834314
 * @QQ :443055589|2667588454
 * =====================================
 */
use Think\Upload;
use Think\Controller;

class MyuploadController extends Controller {

    private $sub_name;
    private $rootPath;
    protected $config = array();

    public function _initialize() {
        $this->sub_name = date("y-m-d", time());
        $this->rootPath = "./Public/Uploads";
    }

    /**
     * @function imageUp
     */
    public function imageUp() {
        // 上传图片框中的描述表单名称，       
        $this->config = array(
            "rootPath" => $this->rootPath,
            "savePath" => "/img/",
            "maxSize" => 20000000, // 单位B
            "exts" => explode(",", 'gif,png,jpg,jpeg,bmp'),
            "subName" => $this->sub_name,
        );
        $path = I("post.path");
        if ($path) {
            $this->sub_name = $path;
        }
        $this->up();
    }

    /**
     * 上传文件
     */
    public function fileUp() {
        $this->config = array(
            "rootPath" => $this->rootPath,
            "savePath" => "/file/",
            "maxSize" => 0, // 单位B
            "exts" => explode(",", 'zip,rar,doc,docx,zip,pdf,txt,ppt,pptx,xls,xlsx'),
            "subName" => $this->sub_name,
        );
        $this->up();
    }

    /**
     * 上传视频
     */
    public function videoUp() {
        $this->config = array(
            "rootPath" => $this->rootPath,
            "savePath" => "/video/",
            "maxSize" => 0, // 单位B
            "exts" => explode(",", 'flv,mp4,mkv.real,3gp,wmv,navi'),
            "subName" => $this->sub_name,
        );
        $this->up();
    }

    function up() {      
        $upload = new Upload($this->config);
        $info = $upload->upload();
        if ($info) {
            $msg = "SUCCESS";
            $status = 1;
        } else {
            $msg = "ERROR" . $upload->getError();
            $status = 0;
        }
        if (!isset($info['upfile'])) {
            $info['upfile'] = $info['Filedata'];
        }
        $return_data['status'] = $status;
        $return_data['file_path'] = $info['url'];
        $return_data['msg'] = $msg;
        $this->ajaxReturn($return_data, 'json');
    }

}
