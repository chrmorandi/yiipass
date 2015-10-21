/**
 * The fast copy functionality in bootstrap modal.
 */
$(".copy_button").click(function(e) {

    $("#mobileCopyModal .modal-title").html($(this).attr("data-title"));

    if ($(this).attr("data-username").length < 1) {
        // Hide field with no value
        $("#mobileCopyModal .modal-body #username_field").hide();
    } else {
        $("#mobileCopyModal .modal-body #username_field").show();
        $("#mobileCopyModal .modal-body #username").val($(this).attr("data-username"));
    }

    if ($(this).attr("data-password").length < 1) {
        // Hide field with no value
        $("#mobileCopyModal .modal-body #password_field").hide();
    } else {
        $("#mobileCopyModal .modal-body #password_field").show();
        $("#mobileCopyModal .modal-body #password").val($(this).attr("data-password"));
    }

});

/**
 * Select text in input after button click.
 */
$("#select-username").click(function(e) {
    $("#mobileCopyModal .modal-body #username").focus().select();
});

$("#select-password").click(function(e) {
    $("#mobileCopyModal .modal-body #password").focus().select();
});
