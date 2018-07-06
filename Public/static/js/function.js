/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function show_info(msg, timer, shade) {
    timer = timer ? timer : 2;
    shade = shade ? true : false;
    if (shade) {
        layer.msg(msg, {time: timer * 1000, shade: 0.1, shadeClose: true});
    } else {
        layer.msg(msg, {time: timer * 1000});
    }
}
//询问框
function edit_info(info, url, em) {
    var error = 0;
    var _attr = {};
    info = info ? info : '您确认要修改该信息？';
    $(em + ' input').each(function () {
        _attr[$(this).attr("name")] = $(this).val();
        if ($(this).val() == '') {
            error = 1;
            layer.msg('请填写完整后再提交', {time: 2000, icon: 2});
            return false;
        }
    });
    if (error == '0') {
        layer.confirm(info, {
            btn: ['确定', '取消'], //按钮
            shade: false //不显示遮罩
        }, function () {
            $.post(url, _attr, function (result) {
                show_info(result.info);
                if (result.status == 1) {
                    window.location.href = result.url;
                }
            });
        }, function () {

        });
    }
}
function postUrl(url, data) {
    datas = data ? data : {};
    $.post(url, datas, function (result) {
        show_info(result.info);
        if (result.status == 1) {
            window.location.href = result.url;
        }
        return false;
    });
}
/**
 * 通过id获取所有表单数据
 * @param {type} id_name
 * @returns {Object}
 */
function getdata_byid(id_name) {
    var x = document.getElementById(id_name);
    var par = new Object();
    for (var i = 0; i < x.length; i++)
    {
        var dataname = x.elements[i].name;
        if (dataname !== '') {
            par[dataname] = x.elements[i].value;
        }
    }
    return par;
}
function get_url(url, info) {
    info = info ? info : '您确认要修改该信息？';
    layer.confirm(info, {
        btn: ['确定', '取消'], //按钮
        shade: false //不显示遮罩
    }, function () {
        $.get(url, {}, function (result) {
            show_info(result.info);
            if (result.status == 1) {
                window.location.href = result.url;
            }
        });
    }, function () {

    });
}
function share_success(type) {
    var url = Share_success_url;
    $.post(url, {type: type}, function () {});
}