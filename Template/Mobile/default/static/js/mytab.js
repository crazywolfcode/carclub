$(".act-tab-opt .tab-control").click(function () {
    if ($(this).parent().hasClass("active")) {
        //点击TAb为以当前激活，做刷新操作
    } else {
        //点击TAb不为当前的TAb;切换
        $(".act-tab-opt ul li").removeClass("active");
        $(this).parent().addClass("active");
        var targer = $(this).attr('href');
        $(".tab-item").addClass("hidden");
        $(targer).removeClass("hidden");
    }
    return false;
});