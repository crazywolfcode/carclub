
wx.config({
    debug: false,
    appId: SDK_appId,
    timestamp: SDK_timestamp,
    nonceStr: SDK_nonceStr,
    signature: SDK_signature,
    jsApiList: [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareQZone'
    ]
});

wx.ready(function () {
    wx.checkJsApi({
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareQZone'
        ],
        success: function (res) {
        }
    });
    //分享给朋友圈
    wx.onMenuShareTimeline({
        title: ShareTitle,
        link: ShareLink,
        imgUrl: ShareImgUrl,
        trigger: function (res) {
        },
        success: function (res) {
            share_success(1);         
        },
        cancel: function (res) {
        },
        fail: function (res) {
        }
    });
    //分享到ＱＱ空间
    wx.onMenuShareQZone({
        title: ShareTitle,
        desc: ShareDesc,
        link: ShareLink,
        imgUrl: ShareImgUrl,
        trigger: function (res) {

        },
        complete: function (res) {

        },
        success: function (res) {
            share_success(2);           
        },
        cancel: function (res) {

        },
        fail: function (res) {

        }
    });
    //分享给朋友
    wx.onMenuShareAppMessage({
        title: ShareTitle,
        desc: ShareDesc,
        link: ShareLink,
        imgUrl: ShareImgUrl,
        success: function () {
            // 用户确认分享后执行的回调函数
            share_success(3);
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    //分享到ＱＱ
    wx.onMenuShareQQ({
        title: ShareTitle,
        desc: ShareDesc,
        link: ShareLink,
        imgUrl: ShareImgUrl,
        success: function () {
            // 用户确认分享后执行的回调函数
            share_success(4);
        },

        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
});
wx.error(function (res) {
    alert(res.errMsg);
});