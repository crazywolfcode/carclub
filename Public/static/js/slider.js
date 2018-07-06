(function ($) {
    $.fn.slideFn = function (options) {
        var defaults = {
            total: 5, //总共多少条切换
            type: 1, //1代表左右，2代表透明滤镜
            prev: true, //上一个切换button
            next: true, //下一个切换button
            nums: true, //是否显示序号
            title: true, //是否显示标题
            autoPlay: true, //是否自动播放
            width: 1366, //图片的宽度
            height: 768, //图片的高度
            isfull: true, //控制全屏,
            numH: 30,
            ani_time: 500,
            lastpage: false,
            pho_obj: null
        }

        if (options)
            var opt = $.extend(defaults, options);
        else
            var opt = defaults;
        var $this = $(this);//获取当前对象
        this.each(function () {
            var d_w = 0, d_h = 0;//定义一个对象的宽高
            var ul_w = 0, ul_h = 0;
            var tit = null;
            var love = null;
            var page = null;
            var ind = parseInt(opt.indexs) || 0;//
            var inter = null;
            var pre = (parseInt(opt.indexs) - 1 < 0 ? 0 : parseInt(opt.indexs) - 1) || 0;
            var fades = true;
            var curInds = parseInt(parseInt(opt.indexs) / 4) || 0;
            var $a_href = window.location.href;
            var pos = $a_href.indexOf('#');
            var $a_str = $a_href.substr(0, pos) + "#";

            var childs = $this.children(":first");
            var childs_li = childs.children();


            //设置标题函数
            function setTit(obj, str) {
                if (opt.title) {
                    obj.html(str);
                }
            }
            //设置标题函数
            function setLove(obj, loves) {
                obj.html(loves);
            }
            function preLoad(obj) {
                var loadSrc = "<img alt=\"图片加载中..\" title=\"图片加载中...\" src=\"http://static.fblife.com/static/images/loading.gif\" border=\"0\" style=\"position:absolute; top:50%; margin-top:-20px; left:50%;margin-left:-20px;width:50px!important; height:54px!important;\" />";
                obj.each(function () {
                    var $thiss = $(this);
                    var hrefs = $thiss.find("img").eq(0).attr("src");
                    $thiss.find("img").eq(0).hide();
                    $thiss.find("img").eq(0).after(loadSrc);

                    var img = new Image();
                    img.src = hrefs;
                    //自动缩放图片大小 end	
                    if (img.complete) {
                        $thiss.find("img").eq(0).attr("src", img.src);
                        $thiss.find("img").eq(0).show();
                        $thiss.find("img").eq(1).remove();
                    } else {
                        img.onload = function () {
                            $thiss.find("img").eq(0).attr("src", img.src);
                            $thiss.find("img").eq(0).show();
                            $thiss.find("img").eq(1).remove();
                        }
                    }

                });
            }

            //初始化对象的宽度高度
            function init_div() {
                if (opt.isfull) {
                    d_w = $(window).width();
                    var wids = opt.width;
                    var heis = opt.height;
                    var newHei = d_w * heis / wids;
                    if (newHei < $(window).height()) {
                        d_h = newHei;
                    } else {
                        d_h = $(window).height();
                    }

                } else {
                    d_w = opt.width;
                    d_h = opt.height;
                }
                //定义ul的宽高
                if (opt.type == 1) {
                    ul_w = d_w * (opt.total * 2);
                    childs.html(childs.html() + childs.html());
                    childs_li = childs.children();
                } else if (opt.type == 2) {
                    ul_w = d_w;
                    childs_li.each(function () {
                        $(this).css({"position": "absolute"});
                        if ($(this).index() == ind) {
                            $(this).css({"z-index": 2});
                        }
                        if ($(this).index() == ind + 1) {
                            $(this).css({"z-index": 1});
                        }
                    });
                }

                ul_h = d_h;
                preLoad(childs_li);
                $this.width(d_w);
                $this.height(d_h + opt.numH);
                childs_li.width(d_w);
                childs_li.height(d_h);

                childs.width(ul_w);
                childs.height(ul_h);

                $this.find(".totInd").html(opt.total);
                $this.find(".curInd").html(parseInt(ind) + 1);
                //判断是否有左右切换按钮
                if (opt.prev != "" || opt.next != "") {
                    var prev = $("<span id='prev'></span>");
                    var next = $("<span id='next'></span>");
                    prev.css({"margin-top": -opt.numH / 2 - 50});
                    next.css({"margin-top": -opt.numH / 2 - 50});
                    $this.append(prev);
                    $this.append(next);

                    prev.click(function () {
                        if (inter != null) {
                            clearInterval(inter);
                            inter = null;
                        }
                        if (opt.type == 1)
                            goRight();
                        if (opt.type == 2 && fades)
                            fadeRi();
                    });
                    next.click(function () {
                        if (inter != null) {
                            clearInterval(inter);
                            inter = null;
                        }
                        if (opt.type == 1)
                            goLeft();
                        if (opt.type == 2 && fades)
                            fade();
                    });

                    if (true) {
                        $(document).keydown(function (e) {
                            var code = e.keyCode;
                            if (code == 37) {
                                if (inter != null) {
                                    clearInterval(inter);
                                    inter = null;
                                }
                                if (opt.type == 1)
                                    goRight();
                                if (opt.type == 2 && fades)
                                    fadeRi();
                            } else if (code == 39) {
                                if (inter != null) {
                                    clearInterval(inter);
                                    inter = null;
                                }
                                if (opt.type == 1)
                                    goLeft();
                                if (opt.type == 2 && fades)
                                    fade();
                            }
                        });
                    }



                    prev.mouseout(function () {
                        if (inter == null && opt.autoPlay) {
                            inter = setInterval(function () {
                                if (opt.type == 1)
                                    goLeft();
                                if (opt.type == 2)
                                    fade();
                            }, opt.ani_time0);
                        }
                    });

                    next.mouseout(function () {
                        if (inter == null && opt.autoPlay) {
                            inter = setInterval(function () {
                                if (opt.type == 1)
                                    goLeft();
                                if (opt.type == 2)
                                    fade();
                            }, opt.ani_time0);
                        }
                    });
                }


                if (opt.title) {
                    tit = $this.find(".desp");
                }
                love = $this.find(".love");

                if (opt.nums) {
                    var html = "";
                    if ($this.find(".page").length > 0) {
                    } else {
                        html += "<div class='page'></div>";
                        $this.append(html);
                    }
                }

                if (opt.nums) {
                    page = $this.find(".page");
                    var $a = "";
                    for (var i = 0; i < opt.total; i++) {
                        if (i == ind)
                            $a += "<a href='javascript:;' class='active numbs'></a>";
                        else
                            $a += "<a href='javascript:;' class='numbs'></a>";
                    }
                    if ($this.find(".page").find("a.numbs").length > 0) {
                        $this.find(".page").find("a.numbs").eq(ind).addClass("active").siblings().removeClass("active");
                    } else {
                        page.html($a);
                    }
                    var $nums = page.find("a.numbs");

                    $nums.click(function () {
                        pre = ind;
                        if (inter != null) {
                            clearInterval(inter);
                            inter = null;
                        }
                        ind = $(this).index();
                        $(this).addClass("active");
                        page.find("a.numbs").not($(this)).removeClass("active");
                        setTit(tit, childs_li.eq(ind).attr("data-desp"));
                        setLove(love, childs_li.eq(ind).attr("data-loves"));
                        $this.find(".curInd").html(ind + 1);
                        if (opt.type == 1) {
                            childs.animate({"margin-left": (-1) * ind * d_w}, opt.ani_time);

                        } else if (opt.type == 2) {
                            if (pre == ind) {
                                curInds = parseInt(ind / 4);
                                return;
                            }
                            if (fades) {
                                fades = false;
                                curInds = parseInt(ind / 4);
                                window.location.href = $a_str + (ind + 1);
                                $("#mpv_view").attr("href", "#" + (ind + 1));
                                childs_li.not(childs_li.eq(ind)).css({"z-index": 1});
                                childs_li.eq(pre).css({"z-index": 3});
                                childs_li.eq(ind).css({"z-index": 2});
                                childs_li.eq(pre).fadeOut(opt.ani_time, function () {
                                    childs_li.eq(pre).css({"display": "none"});
                                });
                                childs_li.eq(ind).fadeIn(opt.ani_time, function () {
                                    fades = true;
                                    var priMar = page.children(".pageNei");
                                    var priMarWid = parseInt(priMar.width());
                                    priMar.stop(false, true);
                                    priMar.animate({"scrollLeft": curInds * priMarWid});
                                });
                                if (opt.lastpage && opt.pho_obj.css("display") == "block") {
                                    opt.pho_obj.stop(false, true);
                                    opt.pho_obj.css({"display": "none", "margin-top": 0});
                                }
                            }
                        }

                    });



                    page.mouseout(function () {
                        if (inter == null && opt.autoPlay) {
                            inter = setInterval(function () {
                                if (opt.type == 1)
                                    goLeft();
                            }, opt.ani_time0);
                        }
                    });
                    //底部的图片切换


                    if ($this.find(".small_nums").length > 0) {
                        totalInds = Math.ceil(page.find("a.numbs").length / 4) - 1;
                        var priMar = page.children(".pageNei");
                        var priMarWid = parseInt(priMar.width());
                        priMar.stop(false, true);
                        priMar.animate({"scrollLeft": curInds * priMarWid});
                        $this.find(".page_prev").click(function () {
                            priMar.stop(false, true);
                            if (curInds <= 0) {
                                $(this).addClass("default");
                                return;
                            }
                            $this.find(".small_nums").removeClass("default");
                            curInds--;
                            priMar.animate({"scrollLeft": curInds * priMarWid});
                        });
                        $this.find(".page_next").click(function () {
                            priMar.stop(false, true);
                            if (curInds >= totalInds) {
                                $(this).addClass("default");
                                return;
                            }
                            $this.find(".small_nums").removeClass("default");
                            curInds++;
                            priMar.animate({"scrollLeft": curInds * priMarWid});
                        });
                    }

                }

            }
            init_div();
            //往左边滚动
            function goLeft() {
                childs.stop(true, true);
                ind++;
                childs.animate({"margin-left": (-1) * ind * d_w}, opt.ani_time, function () {
                    childs.css({"margin-left": (-1) * ind * d_w});
                    if (ind >= opt.total) {
                        ind = 0;
                        childs.css({"margin-left": 0});
                    }
                    if (opt.nums) {
                        var $this_page = page.find("a.numbs").eq(ind);
                        page.find("a.numbs").eq(ind).addClass("active");
                        page.find("a.numbs").not($this_page).removeClass("active");
                    }

                    setTit(tit, childs_li.eq(ind).attr("data-desp"));
                    setLove(love, childs_li.eq(ind).attr("data-loves"));
                });
            }


            //往右边滚动
            function goRight() {
                childs.stop(true, true);
                ind--;
                if (opt.nums) {
                    if (ind < 0) {
                        var $this_page = page.find("a.numbs").eq(opt.total - 1);
                        page.find("a.numbs").eq(opt.total - 1).addClass("active");
                        page.find("a.numbs").not($this_page).removeClass("active");
                    } else {
                        var $this_page = page.find("a.numbs").eq(ind);
                        page.find("a.numbs").eq(ind).addClass("active");
                        page.find("a.numbs").not($this_page).removeClass("active");
                    }
                }
                if (ind < 0) {
                    ind = opt.total;
                    childs.css({"margin-left": (-1) * ind * d_w});
                    ind = opt.total - 1;
                }
                childs.animate({"margin-left": (-1) * ind * d_w}, opt.ani_time, function () {
                    childs.css({"margin-left": (-1) * ind * d_w});
                    setTit(tit, childs_li.eq(ind).attr("data-desp"));
                });
            }


            //顺序渐变
            function fade() {
                childs.stop(false, true);
                fades = false;
                pre = ind;
                ind++;

                if (ind > opt.total - 1) {
                    if (opt.lastpage) {
                        opt.pho_obj.stop(false, true);
                        opt.pho_obj.animate({"margin-top": 220}, {queue: false, duration: 100}).fadeIn();
                        if (ind > opt.total) {
                            ind--;
                        }
                    } else {
                        ind--;
                    }
                    fades = true;
                    return;
                    ind = 0;
                }
                window.location.href = $a_str + (ind + 1);
                $("#mpv_view").attr("href", "#" + (ind + 1));
                curInds = Math.floor(ind / 4) - 1;
                $this.find(".page_next").trigger("click");
                childs_li.not(childs_li.eq(ind)).css({"z-index": 1});
                childs_li.eq(pre).css({"z-index": 3});
                childs_li.eq(ind).css({"z-index": 2});
                childs_li.eq(pre).stop(false, true);
                childs_li.eq(ind).stop(false, true);
                setTit(tit, childs_li.eq(ind).attr("data-desp"));
                setLove(love, childs_li.eq(ind).attr("data-loves"));
                $this.find(".curInd").html(ind + 1);
                childs_li.eq(pre).fadeOut(opt.ani_time);
                childs_li.eq(ind).fadeIn(opt.ani_time, function () {
                    fades = true;
                    if (opt.nums) {
                        var $this_page = page.find("a.numbs").eq(ind);
                        page.find("a.numbs").eq(ind).addClass("active");
                        page.find("a.numbs").not($this_page).removeClass("active");
                    }
                });
            }



            function fadeRi() {
                childs.stop(true, true);
                fades = false;
                pre = ind;
                ind--;
                if (opt.lastpage && opt.pho_obj.css("display") == "block") {
                    opt.pho_obj.stop(false, true);
                    opt.pho_obj.css({"display": "none", "margin-top": 0});
                }
                if (ind < 0) {
                    ind++;
                    fades = true;
                    return;
                    ind = opt.total - 1;
                }

                window.location.href = $a_str + (ind + 1);
                $("#mpv_view").attr("href", "#" + (ind + 1));
                curInds = Math.floor(ind / 4) + 1;
                $this.find(".page_prev").trigger("click");
                childs_li.not(childs_li.eq(ind)).css({"z-index": 1});
                childs_li.eq(pre).css({"z-index": 3});
                childs_li.eq(ind).css({"z-index": 2});
                setTit(tit, childs_li.eq(ind).attr("data-desp"));
                setLove(love, childs_li.eq(ind).attr("data-loves"));
                $this.find(".curInd").html(ind + 1);
                childs_li.eq(pre).fadeOut(opt.ani_time);
                childs_li.eq(ind).fadeIn(opt.ani_time, function () {
                    fades = true;
                    if (opt.nums) {
                        var $this_page = page.find("a.numbs").eq(ind);
                        page.find("a.numbs").eq(ind).addClass("active");
                        page.find("a.numbs").not($this_page).removeClass("active");
                    }
                });
            }

            //判断是否自动滚动
            if (opt.autoPlay) {
                inter = setInterval(function () {
                    if (opt.type == 1)
                        goLeft();
                    else if (opt.type == 2)
                        fade();
                }, opt.ani_time0);
            }

            $this.mouseover(function () {
                $this.find("#prev").show();
                $this.find("#next").show();
            });
            $this.mouseout(function () {
                $this.find("#prev").hide();
                $this.find("#next").hide();
            });



            $(window).bind("resize", function () {
                if (opt.isfull) {
                    d_w = $(window).width();
                    var wids = opt.width;
                    var heis = opt.height;
                    var newHei = d_w * heis / wids;
                    if (newHei < $(window).height()) {
                        d_h = newHei;
                    } else {
                        d_h = $(window).height();
                    }
                    //定义ul的宽高
                    if (opt.type == 1) {
                        ul_w = d_w * (opt.total * 2);
                        childs.html(childs.html() + childs.html());
                        childs_li = childs.children();
                    } else if (opt.type == 2) {
                        ul_w = d_w;
                    }
                    ul_h = d_h;
                    $this.width(d_w);
                    $this.height(d_h);
                    childs_li.width(d_w);
                    childs_li.height(d_h);

                    childs.width(ul_w);
                    childs.height(ul_h);
                    if (opt.type == 1) {
                        childs.css({"margin-left": (-1) * ind * d_w}, opt.ani_time);
                    }
                }
            });
        });
    }

    $.extend($.fn.slideFn, {version: "2.0", author: "wanghao"});
})(jQuery);
