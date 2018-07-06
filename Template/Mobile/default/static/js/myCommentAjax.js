
$("#detail-morecomment").click(function () {
    var _this = $(this);
    var tempurl = $(this).attr("data-url");
    var parentid = $(this).attr("data-parentid");
    var targetDoc = $(this).attr("data-target");
    var page = parseInt($(this).attr("data-page"));
    var url = tempurl.replace("CURRPAGE", (page + 1)).replace("PARENTID", parentid);
    var ll = layer.load();
    $.ajax({
        type: "post",
        dataType: "text",
        url: url.replace("CURRPAGE", (page + 1)),
        success: function (res) {
            var obj = $.parseJSON(res);
            if (obj.status == 0) {
                _this.fadeOut('slow');
            }
            if (obj.status > 0) {
                $(targetDoc).append(obj.data);
                _this.attr("data-page", (page + 1));
            }
            layer.close(ll);
        },
        error: function (e) {
            alert("访问出错");
            layer.close(ll);
        }
    });
});


