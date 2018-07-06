
var currTop = 0;
var currpage = 2;
var isScroll = false;
var haveData = 1; //是否有可以加载 的数据，默认为有;
$(window).on("scroll", function () {
    currTop = $(window).scrollTop();
    var scrollHeight = $(document).scrollTop();
    var documentHeight = $(document).height();
    var windowHeight = $(window).height();
    /*
     if ($(document).scrollTop() <= 0) {
     alert("滚动条已经到达顶部为0");
     }
     */
    isScroll = true;
    if (scrollHeight >= documentHeight - windowHeight) {
//  alert("滚动条已经到达底部为" + $(document).scrollTop());
        if (isScroll == false) {
            return;
        } else {
            isScroll = false;
        }
        if (haveData == 0) {
            return;
        }
        var loadingtext = "正在加载....";
        var loadedtext = "加载完成";
        var nodatatext = "没有数据拉";
        var loadmore = $(".myloadmore");
        var hint_text = $(".hint-text");
        var hint_img = $(".hint-img");
        var url = loadmore.attr("data-url").trim();
        var target = loadmore.attr("data-target");
        loadmore.removeClass("hidden");
        getdata(url, target);
//恢复
        hint_img.removeClass("hidden");
        hint_text.text(loadingtext);
    }


    function getdata(url, target) {
        $.ajax({
            type: "post",
            dataType: "text",
            url: url.replace("CURRPAGE", (currpage + 1)),
            data: ({}),
            success: function (res) {
                var obj = $.parseJSON(res);
                handlemyloader(obj.status);
                if (obj.status > 0) {
                    $(target).append(obj.data);
                    currpage++;
                }
            },
            error: function (e) {
                alert(e.msg);
                handlemyloader(0);
            }
        });
    }


    function handlemyloader(status) {
        if (status == 0) {
            hint_text.text(nodatatext);
            haveData = 0;
        } else if (status == 1) {
            haveData = 0;
        } else {
            haveData = 1;
        }
        setTimeout(function () {
            hint_img.addClass("hidden");
            hint_text.text(loadedtext);
        }, 200);
        setTimeout(function () {
            loadmore.addClass("hidden");
        }, 1000);
    }
});


