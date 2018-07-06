
var currTop = 0;
$(window).on("scroll", function () {
    currTop = $(window).scrollTop();
    if (currTop > 200) {
        $(".navbar").css({"position": "fixed"})
                .css({"top": "0px"})
                .css({"background-color": "rgba(69, 149, 234, 0.8)"});
    } else {
        $(".navbar").removeAttr("style");
    }
    /*
     if ($(document).scrollTop() <= 0) {
     alert("滚动条已经到达顶部为0");
     }
     
     if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
     alert("滚动条已经到达底部为" + $(document).scrollTop());
     }
     */
});

