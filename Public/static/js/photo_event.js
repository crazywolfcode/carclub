$(document).ready(function () {   
    function setviewType() {
        var $a_href = window.location.href;
        var pos = $a_href.indexOf('#');
        if (pos < 0)
        {
            mao = '1';
            window.location.href = window.location.href + "#" + mao;
        } else {
            mao = $a_href.substr(pos + 1);
            window.location.href = $a_href.substring(0, pos) + "#" + mao;
        }
        if (mao.indexOf('#') >= 0) {
            mao = mao.substring(mao.lastIndexOf("#"));
        }
        $("[id^='view_']").css("display", "none");
        if (mao == "list") {
            $("#view_" + mao).fadeIn("fast");
        } else {
            $("#view_imgview").fadeIn("fast");
            $("#mpv_view").attr("href", "#" + (mao));
        }
    }
    setviewType();

    $(".view_click").click(function () {
        setTimeout(function () {
            setviewType();
        }, 100);
    });

    if (mao == "list") {
        mao = 1;
    }

    $("#sliderphoto").slideFn({
        total: $("#sliderphoto").find("li").length, //� 
        type: 2, //1
        prev: true, //
        next: true, //
        nums: true, //��� 
        title: true, //���� 
        autoPlay: false, //���� 
        width: 1024, // 
        height: 683, //
        isfull: false, //� 
        numH: 260,
        ani_time: 100,
        indexs: mao - 1,
        lastpage: true,
        pho_obj: $("#last_photo")
    });


    $("#slider01").slideFn({
        total: $("#slider01").find("li").length,
        type: 1,
        prev: true,
        next: true,
        nums: false,  
        title: false,
        autoPlay: false,
        width: 1024,
        height: 570,
        isfull: false,
        numH: 0,
        ani_time: 300
    });
    //


    $("#reviews").click(function () {
        var $nums = $("#sliderphoto").find(".page").find("a.numbs");
        $nums.eq(0).trigger("click");
    });
    $(".ph_close").click(function () {
        var $nums = $("#sliderphoto").find(".page").find("a.numbs:last");
        $nums.trigger("click");
    });
    var search_d = function (obj) {
        obj.focus(function () {
            if ($(this).val() == this.defaultValue) {
                $(this).val("");
            }
        }).blur(function () {
            if ($(this).val() == '') {
                $(this).val(this.defaultValue);
            }
        });
    }
    //input�á�ʧ����
    search_d($("#he_se").find("input"));
});
