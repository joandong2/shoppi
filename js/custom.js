jQuery(function ($) {
  var wish_id = "",
    curr_id = "",
    curr_page = 1,
    max_page;

  var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
      sURLVariables = sPageURL.split("&"),
      sParameterName,
      i;

    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split("=");

      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined
          ? true
          : decodeURIComponent(sParameterName[1]);
      }
    }
    return false;
  };

  $("body").on("click", ".jo-wishlist-icon", function (e) {
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

  $("body").on("click", "#load-more", function () {
    var orderBy = getUrlParameter("orderby");
    var cat_name = $(this).attr("data-category");
    max_page = $(this).attr("data-total");
    curr_page++;
    $(".ripple").css("display", "block");
    (data = {
      action: "jc_load_more",
      paged: curr_page,
      orderBy: orderBy,
      cat_name: cat_name,
    }),
      $.post(ajax_object.jo_ajaxurl, data, function (res) {
        if (res !== "") {
          setTimeout(function () {
            $(".ripple").css("display", "none");
          }, 500);

          $("ul.products").append(res);

          if (parseInt(max_page) === curr_page) {
            $("#load-more").css("display", "none");
          }
        }
      });
  });
});
