$("#share-cancel-btn,#mask-view").on("click", function () {
    $("#share-body").fadeOut();
    $("#share-body").addClass("hidden");
    return false;
});

$(".share-page-btn").on("click", function () {
    $("#share-body").removeClass("hidden");
    $("#share-body").fadeIn();
    var shareData = $("#shareData");
    shareData.attr("data-title", $(this).attr("data-title"));
    shareData.attr("data-desc", $(this).attr("data-desc"));
    shareData.attr("data-link", $(this).attr("data-link"));
    shareData.attr("data-imgUrl", $(this).attr("data-imgUrl"));

});