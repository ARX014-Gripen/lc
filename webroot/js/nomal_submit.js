$(document).ready(function() {
    $('form').submit(function() {
        $(".submit-button").prop("disabled", true);
    });
});