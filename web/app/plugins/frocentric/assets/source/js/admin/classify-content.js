/* eslint-disable no-undef */
jQuery(document).ready(function ($) {
  $("#classify-content-form").submit(function (e) {
    e.preventDefault();

    const formData = {
      action: "classify_content",
      security: classifyContentAjax.nonce,
      post_type: $("#post_type").val(),
    };
    const displayStatus = function (status) {
      const msg = `<div class="notice notice-${
        status.type
      } is-dismissible"><p><strong>${status.type.toUpperCase()}: </strong>${
        status.message
      }</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>`;
      $(".wp-header-end").after(msg);
      $(".notice-dismiss").click(function () {
        $(this)
          .parent()
          .slideUp("normal", function () {
            $(this).remove();
          });
        return false;
      });
      $("#submit").removeClass("disabled");
    };

    $("#submit").addClass("disabled");
    $.ajax({
      type: "POST",
      url: classifyContentAjax.ajax_url,
      data: formData,
      success: function (response) {
        const status = {};

        if (response.success) {
          status.type = "success";
          status.message = `${response.data.processed} of ${response.data.total} posts processed.`;
        } else {
          status.type = "error";
          status.message = response.error_message ?? "An error occurred.";
        }

        displayStatus(status);
      },
      error: function (response) {
        const status = {
          type: "error",
          message: response.error_message ?? "An error occurred.",
        };

        displayStatus(status);
      },
    });
  });
});
