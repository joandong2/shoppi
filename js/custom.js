jQuery(function ($) {
  var wish_id = "";
  $("body").on("click", ".product-intro a", function (e) {
    e.preventDefault();
    wish_id = $(this).attr("id");
    (data = {
      action: "jo_add_to_wishlist", // execute the function
      wish_id: wish_id, // used as $_POST['val'] in ajax callback
    }),
      $.post(ajax_object.jo_ajaxurl, data, function (res) {
        // console.log(wish_id);
        // console.log($.trim(res));
        if ($.trim(res) === "removed") {
          console.log("remove");
          $(".product-intro a#" + wish_id).removeClass("active");
        } else {
          console.log("add");
          $(".product-intro a#" + wish_id).addClass("active");
        }
      });
  });

  var curr_id = "";
  $("body").on("click", "#jc-tabbed-products li", function () {
    $(this).siblings("li").removeClass("active");
    $(this).addClass("active");
    $("#jc-tabbed-content").empty();
    $(".ripple").css("display", "block");
    curr_id = $(this).attr("id");
    (data = {
      action: "jo_load_more", // execute the function
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

  $("body").on(
    {
      mouseenter: function () {
        $(this).parent().find("img.hover-thumbnail").css("opacity", "1");
        $(this).parent().find("img.main-thumbnail").css("opacity", "0");
      },
      mouseleave: function () {
        $(this).parent().find("img.hover-thumbnail").css("opacity", "0");
        $(this).parent().find("img.main-thumbnail").css("opacity", "1");
      },
    },
    ".product-image-with-hover"
  );
});
