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
      let url = $input.val();
      let regExp = /.*-(\d+)(?:\/|\?)?.*$/g;
      let match = regExp.exec(url);
      let eventId = match[1];
      let title = $button.text();

      $button.width($button.width()).text("...").prop("disabled", true);
      let data = {
        action: "import_event",
        wpea_action: "wpea_import_submit",
        wpea_import_form_nonce: $("#wpea_import_form_nonce").val(),
        event_plugin: "tec",
        event_status: "pending",
        import_frequency: "daily",
        import_origin: "eventbrite",
        import_type: "onetime",
        eventbrite_import_by: "event_id",
        wpea_eventbrite_id: eventId,
      };
      $.post(settings.ajaxurl, data, function (response) {
        console.log(response);

        // enable button
        $button.text(title).prop("disabled", false);
      });
    });
  });
  function submit_event_url() {}
})(jQuery);
