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
      const $button = $(this);
      const $message = $(".tribe-section .validation-message");
      const title = $button.prop("value");
      const data = {
        tribe_aggregator_nonce: $("#tribe_aggregator_nonce").val(),
        aggregator: {
          action: "new",
          origin: "eventbrite",
          post_status: "pending",
          eventbrite: {
            import_type: "manual",
            source: $("#import-event-url").val(),
          },
        },
      };

      // frequency at which we will poll for results
      let polling_frequency_index = 0;
      // Range of frequencies to poll at
      const polling_frequencies = [500, 1000, 5000, 20000];
      // track how many result fetches have been executed via polling
      let result_fetch_count = 0;
      // the maximum number of result fetches that can be done per frequency before erroring out
      const max_result_fetch_count = 15;
      let import_id;
      const display_fetch_error = (message) => {
        $button.prop("value", title).prop("disabled", false);
        $message.text(message);
      };

      /**
       * Poll for results from an import
       */
      const poll_for_results = () => {
        result_fetch_count++;

        var jqxhr = $.ajax({
          type: "GET",
          url:
            settings.ajaxurl +
            "?action=aggregator_fetch_import&import_id=" +
            import_id,
          dataType: "json",
        });

        jqxhr.done(function (response) {
          if (
            "undefined" !== typeof response.data.warning &&
            response.data.warning
          ) {
            display_fetch_error(response.data.warning);
          }

          if (!response.success) {
            var error_message;

            if ("undefined" !== typeof response.data.message) {
              error_message = response.data.message;
            } else if ("undefined" !== typeof response.data[0].message) {
              error_message = response.data[0].message;
            }

            display_fetch_error(error_message);

            return;
          }

          if ("error" === response.data.status) {
            display_fetch_error(response.data.message);
          } else if (!("post_id" in response.data.data)) {
            if (result_fetch_count > max_result_fetch_count) {
              polling_frequency_index++;
              result_fetch_count = 0;
            }

            if (
              "undefined" ===
              typeof polling_frequencies[polling_frequency_index]
            ) {
              display_fetch_error("Timeout occurred.");
            } else {
              setTimeout(
                poll_for_results,
                polling_frequencies[polling_frequency_index]
              );
            }
          } else {
            console.log(response.data.data);
            window.location =
              settings.homeurl +
              "/events/community/edit/event/" +
              response.data.data.post_id;
          }
        });
      };

      data["has-credentials"] = 1;
      $button
        .width($button.width())
        .prop("value", "Importing...")
        .prop("disabled", true);
      $.post(
        `${settings.ajaxurl}?action=tribe_aggregator_create_import`,
        data,
        function (response) {
          console.log(response);

          if (
            "object" === typeof response &&
            "object" === typeof response.data &&
            "success" === response.data.status
          ) {
            import_id = data.aggregator.import_id =
              response.data.data.import_id;
            $.post(
              `${settings.ajaxurl}?action=tribe_aggregator_create_import`,
              data,
              function (response) {
                $message.text("");
                console.log(response);

                if (response.success) {
                  setTimeout(
                    poll_for_results,
                    polling_frequencies[polling_frequency_index]
                  );
                } else {
                  display_fetch_error("Error occurred.");
                }
              }
            );
          } else {
            display_fetch_error(
              response.data && typeof response.data === "string"
                ? response.data
                : "Unknown error occurred."
            );
          }
        }
      );
    });

    $(
      ".ecs-post-loop.has-post-thumbnail .elementor-widget-image.post-author-default"
    ).remove();
    $(".ecs-post-loop")
      .not(".has-post-thumbnail")
      .each(function (index) {
        const $this = $(this);
        const $avatar = $this.find(".elementor-author-box__avatar img");
        const $link = $this.find(
          ".elementor-author-box__text .elementor-author-box__name"
        );
        const $author_image = $this.find(".post-author-default img");
        $author_image.attr("src", $avatar.attr("src"));
        $author_image.attr("alt", $link.text());
      });
  });
  function submit_event_url() {}
})(jQuery);
