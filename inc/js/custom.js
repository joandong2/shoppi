jQuery(function ($) {
  var curr_id = "";
  $("#jc-tabbed-products li").on("click", function () {
    $(this).siblings("li").removeClass("active");
    $(this).addClass("active");
    $("#jc-tabbed-content").empty();
    $(".ripple").css("display", "block");
    curr_id = $(this).attr("id");
    (data = {
      action: "jo_load_more", // execute the function
      nonce: ajax_object.jo_nonce,
      curr_id: curr_id, // used as $_POST['val'] in ajax callback
    }),
      $.post(ajax_object.jo_ajaxurl, data, function (res) {
        if (res !== "") {
          setTimeout(function () {
            $(".ripple").css("display", "none");
            $("#jc-tabbed-content").append(res);
          }, 500);
        }
      });
  });

  $("article.product p").on("click", function (e) {
    e.preventDefault();
    var wish_id = $(this).attr("data-id");
    console.log(wish_id);
    (data = {
      action: "jo_add_to_wishlist", // execute the function
      nonce: ajax_object.jo_nonce,
      wish_id: wish_id, // used as $_POST['val'] in ajax callback
    }),
      $.post(ajax_object.jo_ajaxurl, data, function (res) {
        //console.log("ajax".res);
        // $(".product-intro svg[data-id='" + wish_id + "']").css(
        //   "color",
        //   "#eb5a46"
        // );
      });
  });
});
