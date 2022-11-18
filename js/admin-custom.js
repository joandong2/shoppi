jQuery(function ($) {
  $("body").on("click", ".wc_multi_upload_image_button", function (e) {
    e.preventDefault();
    var button = $(this),
      custom_uploader = wp
        .media({
          title: "Insert image",
          button: { text: "Use this image" },
        })
        .on("select", function () {
          var attachment = custom_uploader.state().get("selection");
          $(button)
            .siblings("ul")
            .append(
              '<li data-attechment-id="' +
                attachment["_single"].id +
                '"><img src="' +
                attachment["_single"].attributes.url +
                '" /></li>'
            );

          $(button)
            .siblings(".attechments-ids")
            .attr("value", attachment["_single"].id);
          $(button).siblings(".wc_multi_remove_image_button").show();
        })
        .open();
  });

  $("body").on("click", ".wc_multi_remove_image_button", function () {
    $(this).hide().prev().val("").prev().html("Add Media");
    $(this).parent().find("ul").empty();
    return false;
  });
});
