jQuery(function ($) {
  var curr_id = "";
  $("#jc-tabbed-products li").on("click", function () {
    curr_id = $(this).attr("id");
    console.log(curr_id);
    (data = {
      action: "jo_load_more", // execute the function
      nonce: ajax_object.jo_nonce,
      curr_id: curr_id, // used as $_POST['val'] in ajax callback
    }),
      $.post(ajax_object.jo_ajaxurl, data, function (res) {
        $(this).siblings("li").removeClass("active");
        $(this).addClass("active");
        $("#jc-tabbed-content").empty();
        $("#jc-tabbed-content").append(res);
      });
  });
});
