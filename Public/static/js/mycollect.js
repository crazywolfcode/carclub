$(".collect-btn").click(function () {
    var T = $(this);
    var loginurl = T.attr("login-url");
    var adduserid = T.attr("data-adduserid");
    var userid = T.attr("data-userid");
    if (userid == null || userid.length < 1 || parseInt(userid) < 1) {
        var log = layer.confirm('您还没有登录！是否要登录？', {
            btn: ['是', '否'] //按钮
        }, function () {
            window.location.href = loginurl;
        }, function () {
            layer.close(log);
            return false;
        });
        return false;
    }
    if (adduserid == userid) {
        layer.msg("自己不可以收藏自己发布的内容", {time: 1200});
        return false;
    }
    var option = T.attr("data-option");
    var type = T.attr("data-type");
    var parentid = T.attr("data-parentid");
    var url = T.attr("data-url");
    $.post(url, {
        userid: userid, type: type, parentid: parentid, option: option
    }, function (res) {
        if (res.status == 1) {
            layer.msg(res.info, {time: 600});
            var children = T.children('span.collect-num');
            var num = parseInt(children.text());
            var myi = T.children('i.myi');
            if (option == 1) {
                children.text((num + 1));
                T.attr("data-option", "0");
                myi.removeClass().removeClass("collect").addClass(" myi collected");
            } else {
                children.text((num - 1));
                myi.removeClass().removeClass("collected").addClass(" myi collect");
                T.attr("data-option", "1");
            }
        } else {
            layer.msg(res.info, {time: 800});
        }
    });
});