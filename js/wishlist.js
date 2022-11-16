jQuery(function ($) {
  $("article.product svg").on("click", function (e) {
    console.log($(this).attr("id"));
  });
});
