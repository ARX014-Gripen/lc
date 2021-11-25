$(document).ready(function() {
  $("#close, div.modal-background").on("click", function() {
    $("div.modal").removeClass("is-active");
  });
  $('form').submit(function() {
    $(".submit-button").prop("disabled", true);
  });
});

function itemAdd(id){
  $("p.title-name").html($("p.title-name-"+id).html());
  $("div.item_id").empty();
  $("div.item_id").append("<input type='hidden' name='item_id' value='" + id + "' >");
  $("p.modal-image").empty();
  image = $("img.image-"+id).clone(true);
  $("p.modal-image").append(image);
  $(".submit-button").prop("disabled", false);
  $("div.modal").addClass("is-active");
}