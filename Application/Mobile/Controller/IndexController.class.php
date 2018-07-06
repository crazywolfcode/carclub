<?php

namespace Mobile\Controller;

class IndexController extends BaseController {

    private $indexActionPageSize = 6;
    private $indexActionsliderNum = 3;
    private $indexVideosliderNum = 3;
    private $indexImgCount = 8;
    private $indexImgFromActionNum = 5;
    private $indexVideoCount = 8;
    private $indexVideoFromActionNum = 5;
    private $indexTribunePagesize = 8;

    public function index() {        
        //获取手机的滚动内容
        $sliderList = $this->getSlider();
        //获取手机首页的活动
        $indexActinList = $this->getIndexActinList(1, $this->indexActionPageSize);
        // 获取手机首页的图集
        $indexImgLisg = $this->getIndexImgList(1, $this->indexImgFromActionNum);
        // 获取手机首页的视频
        $indexVideoList = $this->getIndexVideoList(1, $this->indexVideoFromActionNum);
        // 获取手机首页的话题文章

        $indexTribuneList = $this->getIndexTribuneList(1, $this->indexTribunePagesize);
        
        $this->assign("sliderlist", $sliderList);
        $this->assign("actionlist", $indexActinList);
        $this->assign("imglist", $indexImgLisg);
        $this->assign("videolist", $indexVideoList);
        $this->assign("tribunelist", $indexTribuneList);
        $this->display();
    }

    /**
     * 获取手机的滚动内容
     * @return type
     */
    public function getSlider() {
        //1 slider action and video 
        $sliderActionList = D("Common/action")->getRands($this->indexActionsliderNum);
        $sliderVideoList = D("Common/video")->getRands($this->indexVideosliderNum);
        for ($i = 0; $i < count($sliderVideoList); $i++) {
            $sliderList[] = array(
                id => $sliderVideoList[$i]['id'],
                img => $sliderVideoList[$i]['img_src'],
                title => $sliderVideoList[$i]['title'],
                url => U("WonderfulVideo/videoDetail", array('id' => $sliderVideoList[$i]['id']))
            );
        }
        for ($i = 0; $i < count($sliderActionList); $i++) {
            $sliderList[] = array(
                id => $sliderActionList[$i]['id'],
                img => $sliderActionList[$i]['propaganda_img'],
                title => $sliderActionList[$i]['title'],
                url => U("Action/actionDetail", array('id' => $sliderActionList[$i]['id']))
            );
        }
        return $sliderList;
    }

    /**
     * 获取手机首页的活动
     * @return type
     */
    public function getIndexActinList($p = 1, $pagesize = 5) {
        $map['isshow'] = 1;
        $map['isdelete'] = 0;
        return D("Common/action")->getActionList($order = 'addtime DESC', $p, $pagesize, $map);
    }

    /**
     * 获取手机首页的图集
     * @return type
     */
    public function getIndexImgList($p = 1, $pagesize = 5) {
        $map['isshow'] = 1;
        $map['isdelete'] = 0;
        //1.随机获取5个活动
        $aList = D("Common/action")->getActionList($order = 'addtime DESC,Rand()', $p, $pagesize, $map);
        //2.获取活动下的图片
        $anum = count($aList);
        for ($i = 0; $i < $anum; $i++) {
            $imgList = D("Common/images")->getActionimgs($aList[$i]['id']);
            $List[] = array(
                action_id => $aList[$i]['id'],
                title => $aList[$i]['title'],
                imgs => $imgList,
                views => $aList[$i]['views'],
                images => $aList[$i]['images'],
                addtime => $aList[$i]['addtime']
            );
        }
        $imglistnum = $this->indexImgCount - $anum;
        //3.随机获取剩下个数的图集
        $iList = D("Common/imglist")->getImgLists($order = 'addtime DESC,rand()', $p, $pagesize, $map);
        //4.获取图集下的图片
        for ($i = 0; $i < count($iList); $i++) {
            $imgList = D("Common/images")->getListimgs($iList[$i]['id']);
            $List[] = array(
                list_id => $iList[$i]['id'],
                title => $iList[$i]['title'],
                imgs => $imgList,
                views => $iList[$i]['views'],
                images => $iList[$i]['images'],
                addtime => $iList[$i]['addtime']
            );
        }
        return $List;
    }

    /**
     * 获取手机首页的视频
     * @return type
     */
    public function getIndexVideoList($p = 1, $pagesize = 5) {
        $map['isshow'] = 1;
        $map['isdelete'] = 0;
        $map['type'] = 1;
        //1.随机获取5个活动中的视频 
        $avList = D("Common/video")->getVideoList($order = 'addtime DESC,rand()', $p, $pagesize, $map);
        $anum = count($avList['data']);
        $orthernum = $this->indexVideoCount - $anum;
        //2.随机获取剩下个数的其它视频
        $map['type'] = 2;
        $List = D("Common/video")->getVideoList($order = 'addtime DESC,rand()', $p, $pagesize, $map);
        //3.合并返回
        return array_merge($avList['data'], $List['data']);
    }

    /**
     * 获取手机首页的话题文章
     * @return type
     */
    public function getIndexTribuneList($p = 1, $pagesize = 5) {
        $map['t.status'] = 1;
        $map['isdelete'] = 0;
        $list = D("Common/tribune")->getTribuneList($order = 'addtime DESC,rand(),comments DESC', $p, $pagesize, $map);
        return $list;
    }

}
