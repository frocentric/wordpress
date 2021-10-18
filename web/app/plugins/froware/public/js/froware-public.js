/* eslint-disable no-unused-vars,no-undef */
/**
 * All of the code for your public-facing JavaScript source
 * should reside in this file.
 *
 * @package           Froware
 *
 * Note: It has been assumed you will write jQuery code here, so the
 * $ function reference has been prepared for usage within the scope
 * of this function.
 *
 * This enables you to define handlers, for when the DOM is ready:
 *
 * $(function() {
 *
 * });
 *
 * When the window is loaded:
 *
 * $( window ).load(function() {
 *
 * });
 *
 * ...and/or other possibilities.
 *
 * Ideally, it is not considered best practise to attach more than a
 * single DOM-ready or window-load handler for a particular page.
 * Although scripts in the WordPress core, Plugins and Themes may be
 * practising this, we should strive to set a better example in our own work.
 */

(function ($) {
  "use strict";

  $(function () {
    $("#import-event").click(function (e) {
      let $button = $(this);
      let $input = $("#import-event-url");
      let $message = $(".tribe-section .validation-message");
      let url = $input.val();
      let title = $button.prop("value");
      let data = {
        action: "validate_event_url",
        event_url: url,
        import_form_nonce: $("#import_form_nonce").val(),
      };

      $button
        .width($button.width())
        .prop("value", "Importing...")
        .prop("disabled", true);
      $.post(settings.ajaxurl, data, function (response) {
        console.log(response);

        if ("object" === typeof response && "object" === typeof response.data) {
          $.post(settings.ajaxurl, response.data, function (response) {
            $message.text("");

            if (response.success && response.data && response.data.ID) {
              window.location =
                settings.homeurl +
                "/events/community/edit/event/" +
                response.data.ID;
            } else {
              $button.prop("value", title).prop("disabled", false);

              if (response.data && typeof response.data === "string") {
                $message.text(response.data);
              }
            }
          });
        } else {
          console.log(response);
          $button.prop("value", title).prop("disabled", false);

          if (response.data && typeof response.data === "string") {
            $message.text(response.data);
          }
        }
      });
    });

    $(
      ".ecs-post-loop.has-post-thumbnail .elementor-widget-image.post-author-default"
    ).remove();
  });
  function submit_event_url() {}
})(jQuery);
