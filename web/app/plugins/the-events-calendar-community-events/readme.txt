=== The Events Calendar: Community Events ===

Contributors: ModernTribe, brianjessee, camwynsp, paulkim, sc0ttkclark, aguseo, barry.hughes, bordoni, borkweb, brook-tribe, faction23, geoffgraham, ggwicz, jazbek, jbrinley, joshlimecuda, leahkoerper, lucatume, mastromktg, mat-lipe, mdbitz, neillmcshea, nicosantos, peterchester, reid.peifer, roblagatta, ryancurban, sc0ttkclark, shane.pearlman, thatdudebutch, trishasalas, zbtirrell
Tags: widget, events, simple, tooltips, grid, month, list, calendar, event, venue, community, registration, api, dates, date, plugin, posts, sidebar, template, theme, time, google maps, google, maps, conference, workshop, concert, meeting, seminar, summit, forum, shortcode, The Events Calendar, The Events Calendar PRO
Donate link: http://m.tri.be/29
Requires at least: 4.7
Tested up to: 5.3.2
Stable tag: 4.7.1.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Community Events is an add-on for The Events Calendar that empowers users to submit and manage their events on your website.

== Description ==

= The Events Calendar: Community Events =

* Frontend user event submission (anonymous & logged in)
* Decide whether submissions are published or saved to draft
* Style submission form using custom templates

Note: you'll need to have the latest version of <a href="http://m.tri.be/3j">The Events Calendar</a> installed for this plugin to function.

== Installation ==

= Install =

1. From the dashboard of your site, navigate to Plugins --> Add New.
2. Select the Upload option and hit "Choose File."
3. When the popup appears select the the-events-calendar-community-events.x.x.zip file from your desktop. (The 'x.x' will change depending on the current version number).
4. Follow the on-screen instructions and wait as the upload completes.
5. When it's finished, activate the plugin via the prompt. A message will show confirming activation was successful.
6. For access to new updates, make sure you have added your valid License Key under Events --> Settings --> Licenses.

= Requirements =

* PHP 5.2.4 or greater (recommended: PHP 5.4 or greater)
* WordPress 3.9 or above
* jQuery 1.11.x
* The Events Calendar 3.10 or above

== Documentation ==

Community Events extends the functionality of Modern Tribe's The Events Calendar (http://m.tri.be/3j) to allow for frontend event submission on your WordPress site. With pretty permalinks enabled, the frontend fields are accessible at the following URLs:

Events list: /events/community/list/
Specific page in events list: /events/community/list/page/[num]
Add a new event: /events/community/add/
Edit an already-submitted event: /events/community/edit/[id] ( redirects to /events/community/list/[post-type]/id )
Delete an already-submitted event: /events/community/delete/[id]

Where /events/ is the TEC slug defined on the main settings tab, and /community/ is the CE slug defined on the Community settings tab (e.g. you can tweak the first 2 parts of the URL).

= Shortcodes =

You can display the Community Events Submission Form on any page or post using:

[tribe_community_events] or [tribe_community_events view="submission_form"]

It is also possible to display events created by current user with [tribe_community_events view="my_events"]

If you want to create custom edit forms for specific events, venues or organizers, you can use the following shortcodes:

[tribe_community_events view="edit_event" id="your_event_id"]
[tribe_community_events view="edit_venue" id="your_venue_id"]
[tribe_community_events view="edit_organizer" id="your_organizer_id"]

PS: Don't forget to replace "your_*_id" with the relevant event, venue or organizer ID.

To modify the Add New button on the event list
[tribe_community_events new_event_url="url where you have entered the CE Submission Form Shortcode"]

To modify the Add New button on the event list
[tribe_community_events event_list_url="url where you have entered the CE Event List Shortcode"]

== Frequently Asked Questions ==

= Where do I go to file a bug or ask a question? =

Please visit the forum for questions or comments: http://m.tri.be/3h

== Contributors ==

The plugin is produced by <a href="http://m.tri.be/2s">Modern Tribe Inc</a>.

= Current Contributors =

<a href="https://profiles.wordpress.org/aguseo">Andras Guseo</a>
<a href="https://profiles.wordpress.org/barryhughes">Barry Hughes</a>
<a href="https://profiles.wordpress.org/brianjessee">Brian Jessee</a>
<a href="https://profiles.wordpress.org/brook-tribe">Brook Harding</a>
<a href="https://profiles.wordpress.org/cliffpaulick">Clifford Paulick</a>
<a href="https://profiles.wordpress.org/MZAWeb">Daniel Dvorkin</a>
<a href="https://profiles.wordpress.org/geoffgraham">Geoff Graham</a>
<a href="https://profiles.wordpress.org/ggwicz">George Gecewicz</a>
<a href="https://profiles.wordpress.org/bordoni">Gustavo Bordoni</a>
<a href="https://profiles.wordpress.org/jazbek">Jessica Yazbek</a>
<a href="https://profiles.wordpress.org/joshlimecuda">Josh Mallard</a>
<a href="https://profiles.wordpress.org/leahkoerper">Leah Koerper</a>
<a href="https://profiles.wordpress.org/lucatume">Luca Tumedei</a>
<a href="https://profiles.wordpress.org/borkweb">Matthew Batchelder</a>
<a href="https://profiles.wordpress.org/neillmcshea">Neill McShea</a>
<a href="https://profiles.wordpress.org/mastromktg">Nick Mastromattei</a>
<a href="https://profiles.wordpress.org/nicosantos”>Nico Santo</a>
<a href="https://profiles.wordpress.org/peterchester">Peter Chester</a>
<a href="https://profiles.wordpress.org/roblagatta">Rob La Gatta</a>
<a href="https://profiles.wordpress.org/reid.peifer">Reid Peifer</a>
<a href="https://profiles.wordpress.org/shane.pearlman">Shane Pearlman</a>
<a href="https://profiles.wordpress.org/camwynsp">Stephen Page</a>
<a href="https://profiles.wordpress.org/thatdudebutch">Wayne Stratton</a>
<a href="https://profiles.wordpress.org/trishasalas">Trisha Salas</a>
<a href="https://profiles.wordpress.org/zbtirrell">Zachary Tirrell</a>

= Past Contributors =

<a href="https://profiles.wordpress.org/caseypatrickdriscoll">Casey Driscoll</a>
<a href="https://profiles.wordpress.org/ckpicker">Casey Picker</a>
<a href="https://profiles.wordpress.org/dancameron">Dan Cameron</a>
<a href="https://profiles.wordpress.org/jkudish">Joachim Kudish</a>
<a href="https://profiles.wordpress.org/jgadbois">John Gadbois</a>
<a href="https://profiles.wordpress.org/jonahcoyote">Jonah West</a>
<a href="https://profiles.wordpress.org/jbrinley">Jonathan Brinley</a>
<a href="https://profiles.wordpress.org/justinendler/">Justin Endler</a>
<a href="https://profiles.wordpress.org/kellykathryn">Kelly Groves</a>
<a href="https://profiles.wordpress.org/kelseydamas">Kelsey Damas</a>
<a href="https://profiles.wordpress.org/kyleunzicker">Kyle Unzicker</a>
<a href="https://profiles.wordpress.org/mdbitz">Matthew Denton</a>
<a href="https://profiles.wordpress.org/mattwiebe">Matt Wiebe</a>
<a href="https://profiles.wordpress.org/mat-lipe">Mat Lipe</a>
<a href="https://profiles.wordpress.org/nickciske">Nick Ciske</a>
<a href="https://profiles.wordpress.org/paulhughes01">Paul Hughes</a>
<a href="https://profiles.wordpress.org/ryancurban">Ryan Urban</a>
<a href="https://profiles.wordpress.org/faction23">Samuel Estok</a>
<a href="https://profiles.wordpress.org/codearachnid">Timothy Wood</a>

= Translations =

Modern Tribe’s premium plugins are translated by volunteers at <a href=“http://m.tri.be/194h”>translations.theeventscalendar.com</a>. There you can find a list of available languages, download translation files, or help update the translations. Thank you to everyone who helps to maintain our translations!

== Add-Ons ==

But wait: there's more! We've got a whole stable of plugins available to help you be awesome at what you do. Check out a full list of the products below, and over at the <a href="http://m.tri.be/3k">Modern Tribe website.</a>

Our Free Plugins:

* <a href="https://wordpress.org/plugins/the-events-calendar/" target="_blank">The Events Calendar</a>
* <a href="http://m.tri.be/18vx" target="_blank">Event Tickets</a>
* <a href="http://wordpress.org/extend/plugins/advanced-post-manager/?ref=tec-readme" target="_blank">Advanced Post Manager</a>
* <a href="http://wordpress.org/plugins/blog-copier/?ref=tec-readme" target="_blank">Blog Copier</a>
* <a href="http://wordpress.org/plugins/image-rotation-repair/?ref=tec-readme" target="_blank">Image Rotation Widget</a>
* <a href="http://wordpress.org/plugins/widget-builder/?ref=tec-readme" target="_blank">Widget Builder</a>

Our Premium Plugins:

* <a href="http://m.tri.be/2c" target="_blank">Events Calendar PRO</a>
* <a href="http://m.tri.be/18vy" target="_blank">Event Tickets Plus</a>
* <a href="http://m.tri.be/2e" target="_blank">The Events Calendar: Eventbrite Tickets</a>
* <a href="http://m.tri.be/18vw" target="_blank">The Events Calendar: Community Tickets</a>
* <a href="http://m.tri.be/2h" target="_blank">The Events Calendar: Facebook Events</a>
* <a href="http://m.tri.be/18h9" target="_blank">The Events Calendar: iCal Importer</a>
* <a href="http://m.tri.be/fa" target="_blank">The Events Calendar: Filter Bar</a>

== Changelog ==

= [4.7.1.1] 2020-06-02 =

* Security - Better sanitizing of values on save (props to miha.jirov for reporting this).

= [4.7.1] 2020-05-20 =

* Feature - Add the "Terms of Submission" setting to allow requiring accepting the terms before submitting the events form. [CE-58]
* Fix - Fix JavaScript validation error when tinyMCE wasn't loaded. [CE-10]
* Tweak - Add generic JavaScript validation for the event community form. [CE-10]
* Language - 8 new strings added, 169 updated, 0 fuzzied, and 2 obsoleted

= [4.7.0] 2020-04-23 =

* Tweak - Deprecate Select2 3.5.4 in favor of SelectWoo
* Fix - Fix some issues that could cause PHP errors and notices during the plugin activation. [CE-47]
* Fix - Remove duplicate registration of the `[tribe_community_events]` shortcode to avoid PHP notice and correct loading of assets. [CE-52]
* Tweak - Load plugin text domain on the new 'tribe_load_text_domains' hook instead of the 'plugins_loaded' hook to support third-party translation providers. [CE-50]
* Language - 0 new strings added, 70 updated, 0 fuzzied, and 0 obsoleted

= [4.6.7] 2019-02-06 =

* Fix - Update WP Router library to avoid fatal errors with PHP 7.3 when using custom rewrite routes for Community Events URLs [CE-4]
* Fix - "Community URLs" section of admin settings warns if Pretty Permalinks are not enabled and fixes missing slash in URLs if site's "Homepage" option is set to "Main Events Page" [CE-5]
* Tweak - Changed views: `community/modules/venue-fields`
* Language - 1 new strings added, 162 updated, 0 fuzzied, and 9 obsoleted

= [4.6.6.1] 2019-10-16 =

* Fix - Resolved problem with CSS styles missing from the last release ZIP package [135851]

= [4.6.6] 2019-10-14 =

* Fix - Custom Rewrite URL slugs are no longer able to be accidentally reset by saving the settings page without making any changes [133395]
* Language - 1 new strings added, 73 updated, 1 fuzzied, and 1 obsoleted

= [4.6.5] 2019-09-16 =

* Fix - Prevent Community custom settings styles from "bleeding" into other settings tabs. [132357]
* Fix - "Email addresses to be notified" option now saves its input [131196]
* Fix - Enqueue Thickbox script on all admin pages when needed [131080]
* Language - 0 new strings added, 23 updated, 0 fuzzied, and 0 obsoleted

= [4.6.4] 2019-08-22 =

* Fix - Adjusted CSS/JS loading for Community Events frontend views [131669]
* Fix - Adjusted CSS for Community settings tab fields [131669]
* Language - 0 new strings added, 82 updated, 0 fuzzied, and 0 obsoleted

= [4.6.3] 2019-07-18 =

* Feature - Add shortcode attributes to change the Add New and View Your Submitted Events links to custom urls [128295]
* Tweak - A failed login now keeps the user on the front end login form, displays an error message, and the overall login form's styling is more consistent; added new `tribe_events_community_successful_login_redirect_to` filter [40584]
* Tweak - Added filters: `tribe_events_community_edit_event_page_title`, `tribe_events_community_submit_event_page_title`, `tribe_events_community_remove_event_page_title`, `tribe_events_community_event_list_page_title`, `tribe_events_community_submit_event_page_title`, `tribe_events_community_successful_login_redirect_to`, `tribe_events_community_my_events_query_orderby`, `tribe_events_community_my_events_query_order`, `tribe_events_community_my_events_query`, `tribe_events_community_logout_url_redirect_to`, `tribe_events_community_submit_event_page_title`
* Tweak - Removed filters: `tribe_ce_edit_event_page_title`, `tribe_ce_submit_event_page_title`, `tribe_ce_remove_event_page_title`, `tribe_ce_event_list_page_title`, `tribe_ce_submit_event_page_title`, `tribe_ce_my_events_query_orderby`, `tribe_ce_my_events_query_order`, `tribe_ce_my_events_query`, `tribe_community_events_allowed_taxonomies`, `tribe_events_community_required_venue_fields`, `tribe_ce_submit_event_page_title`
* Tweak - Added actions: `tribe_events_community_event_submission_login_form`, `tribe_events_community_before_event_submission_page`, `tribe_events_community_before_event_submission_page_template`, `tribe_events_community_before_event_list_page`, `tribe_events_community_before_event_list_page_template`, `tribe_tribe_events_community_event_list_login_form`, `tribe_events_community_event_submission_login_form`, `tribe_events_community_event_list_table_row_actions`
* Tweak - Removed actions: `tribe_ce_event_submission_login_form`, `tribe_ce_before_event_submission_page`, `tribe_ce_before_event_submission_page_template`, `tribe_ce_before_event_list_page`, `tribe_ce_before_event_list_page_template`, `tribe_ce_event_list_login_form`, `tribe_ce_event_submission_login_form`, `tribe_ce_event_list_table_row_actions`
* Tweak - Changed views: `community/columns/title`, `community/edit-event`, `community/edit-organizer`, `community/edit-venue`, `community/email-template`, `community/event-list-shortcode`, `community/event-list`, `community/modules/custom`, `community/modules/custom/fields/dropdown`, `community/modules/custom/fields/input-option`, `community/modules/custom/fields/text`, `community/modules/custom/fields/textarea`, `community/modules/custom/fields/url`, `community/modules/custom/table-row`, `community/modules/custom/table`, `community/modules/taxonomy`
* Fix - Logged-in users not allowed to access the WordPress Dashboard ("Roles to block" setting) can now be redirected to a custom URL, whether on-site or off-site; the new default URL is the Community Events List View instead of the site's homepage; added new `tribe_events_community_logout_url_redirect_to` filter [72214]
* Fix - tinyMCE.get(...) is null error on submit of event when visual editor is active [128515]
* Fix - Stop translating slugs and let the site owner set them if they so desire [98503]
* Fix - Events Calendar PRO Additional Fields section now renders well for accessibility (A11Y), has correct class names, and drop downs are enhanced with Select2 [127176]
* Fix - Change all namespacing of hooks to match plugin namespacing. Use `apply_filters_deprecated` and `do_action_deprecated` for backwards-compatibility [130084]
* Language: 29 new strings added, 164 updated, 0 fuzzied, and 9 obsoleted

= [4.6.2] 2019-06-20 =

* Feature - Shortcodes for Community Events. With the [tribe_community_events] shortcode, you can embed the Event Submission form, the "My Events" page and edit forms on posts and pages [78707]
* Feature - Deleting events from the "My Events" page is now done via ajax. [123620]
* Tweak - Reduced file size by removing .po files and directing anyone creating or editing local translations to translations.theeventscalendar.com
* Tweak - Clean up the layout styles for the ticket controls on small-medium screens [127193]
* Tweak - Ensure that "My Events" page defaults to sort by start date. Add `tribe_ce_my_events_query_orderby` and `tribe_ce_my_events_query_order` filters. Added comment blocks to all filters in the `doMyEvents` function [126393]
* Tweak - Added filters: `tribe_ce_my_events_query_orderby`, `tribe_ce_my_events_query_order`, `tribe_events_community_list_page_template_include`, `tribe_events_community_submission_url`, `tribe_events_community_shortcode_nav_link`, `tribe_community_events_list_columns_blocked`, `tribe_events_community_add_event_label`, `tribe_community_events_list_display_button_text`
* Tweak - Removed filters: `tribe_events_community_stylesheet_url` as it never worked correctly [125096]
* Tweak - Added actions: `tribe_ce_event_submission_login_form`, `tribe_events_community_before_shortcode`, `tribe_community_events_shortcode_before_list_navigation`, `tribe_community_events_shortcode_after_list_navigation_buttons`, `tribe_community_events_shortcode_before_list_table`, `tribe_community_events_shortcode_after_list_table`
* Tweak - Changed views: `community/event-list-shortcode`, `community/event-list`, `community/modules/delete`, `community/modules/submit`
* Fix - Add styles to prevent recurrence controls from showing on small screens until they are triggered [127193]
* Fix - Changed the text for the submit button to "Update", in the Community Events edit submission screen [123363]
* Fix - Move from deprecated hook to ensure license key fields show up properly in settings [125258]
* Fix - Move creation of `Tribe__Events__Community__Anonymous_Users` to `init` to be sure we have an authenticated user [124619]
* Fix - Correct our implementation of custom styles so that they are applied in addition to our base styles, rather than instead of them [125096]
* Fix - dependency-checker now correctly identifies missing TEC on activation/deactivation of TEC [122638]
* Language - 13 new strings added, 96 updated, 2 fuzzied, and 9 obsoleted

= [4.6.1.2] 2019-04-04 =

* Fix - Ensure Community Events filter does not remove the Attendee bulk actions in the admin area [124783]

= [4.6.1.1] 2019-03-06 =

* Fix - Ensure Community Events filter does not remove the Attendee bulk actions in the admin area [123608]

= [4.6.1] 2019-02-26 =

* Fix - allow event creators to check in attendees via the FE attendee list [118675]
* Language - 0 new strings added, 64 updated, 0 fuzzied, and 0 obsoleted

= [4.6] 2019-02-05 =

* Feature - Add check and enforce PHP 5.6 as the minimum version [116283]
* Feature - Add system to check plugin versions to inform you to update and prevent site breaking errors [116841]
* Tweak - Ensure we load the text domain before loading activation messages [104746]
* Tweak - Change whitespace positions for better and more consistent translations [120964]
* Tweak - Update plugin header [90398]
* Tweak - Added filters: `tribe_not_php_version_names`
* Deprecated - The function ` Tribe_CE_Load()` and constant `REQUIRED_TEC_VERSION`, `init_addon()` method has been deprecated in `Tribe__Events__Community__Main` in favor of Plugin Dependency Checking system

= [4.5.16] 2019-01-15 =

* Tweak - Scroll to top of form if submitted with errors [108095]
* Tweak - Improve the default styling for the list view on smaller screens [119166]
* Tweak - Updated view for e-mail template [119145]
* Tweak - Changed views: `community/email-template`
* Fix - Ensure featured image is not submitted if removed prior to submit [119247]
* Fix - Correct blank url in review submission email for anonymously submitted events [119145]
* Language - 7 new strings added, 106 updated, 0 fuzzied, and 1 obsoleted

= [4.5.15] 2018-12-05 =

* Feature - Added a new action, `tribe_community_events_before_form_messages`, to allow easier addition of content before various form messages are displayed [118438]
* Fix - Ensure images can be removed from events after uploading them--on both the submission form *and* the edit form for already-submitted events [104450]
* Fix - Ensure that fields required for Organizers work on both the submission form *and* the edit-Organizer form [110203]
* Fix - Prevent event-status tooltips from being cut off in the "My Events" list on the front end [116621]

= [4.5.14] 2018-11-13 =

* Feature - The email alert now displays a list with all organizers. The venue and the Organizers are now linked to their respective edit pages. New action hooks added before and after the email template. The email now indicates if an event is recurring [110657]
* Fix - Ensure that the form won't submit if new Venues or Organizers are being created on the form but the form's missing the required Event Title and Event Description fields [116196]
* Fix - The 'Show Google Map' and 'Show Google Maps Link' fields are now disabled when the `tribe_events_community_apply_map_defaults` filter is being used [97842]
* Tweak - Allow the HTML `<img>` tag in the Community Events submission form [111539]
* Tweak - Add a "Lost password?" URL to Community Events login forms so that users can reset their passwords, just like they can via other WordPress Core login forms (thanks to Karen White for suggesting this!) [105952]

= [4.5.13.1] 2018-08-27 =

* Fix - Don't dequeue `tribe-events-calendar-script` as it has too many dependents ( props @focusphoto, @tatel, @lindsayhanna17, and many others who reported this) [113083]

= [4.5.13] 2018-08-01 =

* Tweak - Manage plugin assets via `tribe_assets()` [40267]
* Tweak - Added new `tribe_events_community_form_before_linked_posts` and `tribe_events_community_form_after_linked_posts` action hooks within the Edit Event form to enhance customizability [109448]

= [4.5.12] 2018-05-29 =

* Fix - Added method with `tribe_community_events_max_file_size_allowed` filter to set the max file size allowed [61354]
* Tweak - Updated views: `src/views/community/modules/image.php`
* Language - 0 new strings added, 8 updated, 0 fuzzied, and 0 obsoleted

= [4.5.11] 2018-04-18 =

* Fix - Prevent multiple notification emails from being sent every time an already-submitted event is edited (thanks to @proactivedesign in the forums for flagging this bug!) [99244]
* Fix - Fixed JavaScript error with datepickers on small viewport sizes [98861]
* Tweak - Fixed some misalignment of buttons above Community Events' front-end "My Events" list in the Twenty Seventeen theme [99846]

= [4.5.10] 2018-03-28 =

* Feature - Added updater class to enable changes on future updates [84675]
* Fix - Prevented errors under PHP 7.2 in relation to the use of `create_function` [100037]
* Fix - Restored the ability of community organizers to email the attendee list, even if they are blocked from accessing the admin environment (thanks to mindaji in our forums for reporting this) [99979]

= [4.5.9] 2018-02-14 =

* Fix - Prevent the loss of event start date, end date, start time, end time, "all day" event, and timezone choices upon failed event submission (thanks to @netdesign and many others for highlighting this issue) [94010]
* Fix - Fixed additional fields from Events Calendar PRO on the event-submission form so that their values are saved upon a failed form submission (thanks @myrunningresource for flagging this bug!) [94908]
* Tweak - Fixed misalignment of "Display Options" button in the front-end "My Events" list [93521]
* Tweak - Adjust the edit-organizer and edit-venue form styles to make both forms more readable (props @artcantina for highlighting these issues) [95043]

= [4.5.8] 2017-11-21 =

* Tweak - Only display admin links in Community Tickets if user is able to access the admin [79565]
* Language - 0 new strings added, 11 updated, 0 fuzzied, and 0 obsoleted

= [4.5.7] 2017-11-16 =

* Fix - Improved translatability of the taxonomy dropdowns ("Searching..." placeholder can now be translated - our thanks to Oliver for flagging this!) [84926]
* Fix - Changed the attendee and other links exposed to event owners so that they stay in the frontend environment where possible (our thanks to Gurdeep Sandhu for flagging this problem) [89015]
* Fix - Added logic to prevent an event end time earlier than the event start time being set [89825]
* Fix - Enhanced ease of marking nested fields as required (our thanks to dsb cloud services GmbH & Co. KG for flagging the need for this) [86299]
* Fix - Ensure the correct wording is used for the Edit Venue and Edit Organizer pages [90154]
* Tweak - The options presented by the timezone picker are now filterable (when used alongside an up-to-date version of The Events Calendar) [92909]
* Language - 5 new strings added, 59 updated, 0 fuzzied, and 0 obsoleted

= [4.5.6] 2017-10-04 =

* Fix - Fixed issues with the jQuery Timepicker vendor script conflicting with other plugins' similar scripts (props: @hcny et al.) [74644]
* Fix - Fixed the creation of the "back" (to the events list) URL, so that translated slugs are used (our thanks to dezemberundjuli for flagging this) [85607]

= [4.5.5] 2017-08-24 =

* Fix - Set the Show Map and Show Map Link fields to true by default, restoring the behavior from past versions [84438]
* Tweak - Fixed some typos that would sometimes show up in the Organizer selection fields [84482]
* Tweak - Added filter: `tribe_events_community_apply_map_defaults` for filtering venue map fields
* Language - 0 new strings added, 28 updated, 0 fuzzied, and 0 obsoleted [tribe-events-community]

= [4.5.4] 2017-08-09 =

* Fix - Fix a conflict with the WP Edit plugin to ensure its "disable wpautop" option does not break the Community Events submission form (our thanks to Karen for highlighting this) [73898]
* Fix - Hide delete button for event recurrence rules [80491]
* Fix - Prevent a notice level error occuring when the edit page is accessed while the Divi theme is in use [72700]
* Fix - Add some responsive styling to account for a wide table on the My List page (our thanks to Iwan for flagging this problem) [79635]
* Fix - Added support for venue meta fields to front-end venue editor (thanks to Mario for flagging this issue) [77260]
* Fix - Fixed bug that erroneously showed the fields for a new linked post when there was an submission error [80389]
* Tweak - Enhance submission form labels for taxonomy fields so they use the real taxonomy name, not "categories" generically (our thanks to Hans-Gerd for bringing our attention to this issue) [80542]
* Tweak - Merged date format settings with The Events Calendar, can now be found in WP Admin > Settings > Display [44911]
* Tweak - Add some UI to the "Timezone" selection field on the submission form to help clarify that it can be edited [80423]
* Tweak - Added helper text to Admin settings clarifying that subscribers can only edit/remove their own submissions [77260]
* Tweak - Added styling for themes like Genesis that put too much padding in the datepicker [79636]
* Tweak - Enhance submission form labels for taxonomy fields so they use the real taxonomy name, not "categories" generically [80542]
* Compatibility - Minimum supported version of WordPress is now 4.5
* Language - 4 new strings added, 119 updated, 0 fuzzied, and 3 obsoleted

= [4.5.3] 2017-07-26 =

* Fix - Stop adjustments to ReCaptcha settings from impacting other Community Events settings and vice versa in a multisite context [79728]
* Tweak - Remove case sensitivty when specifying custom "required fields" via the tribe_events_community_required_fields filter [76297]
* Tweak - add filter to show event cost if filled in from the front end even if ticketing plugins active [80215]

= [4.5.2] 2017-06-28 =

* Fix - Improved handling of Venue fields that allows for better form validation in Community Events [76297]
* Fix - Ensure the "Users cannot create new Venues|Organizers" setting is respected [80487]
* Fix - Ensure the tribe-no-js class is removed when appropriate. [79335]
* Tweak - Do not render the venue or organizer template modules if the current user can neither select nor create those posts [80487]

= [4.5.1] 2017-06-14 =

* Fix - Preserve 'Event Options' when the Community form is submitted [72055]

= [4.5] 2017-06-06 =

* Feature - Post tags to the Community Events Editor [35822]
* Feature - Completely revamp the HTML and CSS for the Community "My Events" and "Events Editor" [76968]
* Feature - Increase the customization hook for all Community Event templates [76968]
* Feature - Improve user experience for featured image uploading on "Events Editor" [76948]
* Fix - Display of checkboxes in the additional field section to be one per line [74002]
* Tweak - Modify Categories user experience on the Community event editor [77125]
* Tweak - Adding community events options to sysinfo data available viewable in Events > Help [38730]
* Tweak - Event Editor now has a better Mobile CSS [77189]
* Tweak - Removed Class `Tribe__Events__Community__Modules__Taxonomy_Block`
* Tweak - Added Template tag: `tribe_community_events_list_columns`, `tribe_community_events_prev_next_nav`
* Tweak - Added filters: `tribe_community_events_allowed_taxonomies`, `tribe_community_events_list_columns`, `tribe_community_events_list_columns_blocked`, `tribe_community_events_add_event_label`, `tribe_community_events_list_display_button_text`, `tribe_events_community_custom_field_value`, `tribe_community_event_edit_button_label`
* Tweak - Removed filters: `tribe_community_events_form_spam_control`, `tribe_events_community_category_dropdown_shown_item_count`, `tribe_ce_event_update_button_text`, `tribe_ce_event_submit_button_text`, `tribe_ce_add_event_button_text`, `tribe_ce_event_list_display_button_text`, `tribe_community_custom_field_value`
* Tweak - Added actions: `tribe_community_events_before_list_navigation`, `tribe_community_events_after_list_navigation_buttons`, `tribe_community_events_before_list_table`, `tribe_community_events_after_list_table`, `tribe_events_community_section_before_captcha`, `tribe_events_community_section_after_captcha`, `tribe_events_community_section_before_cost`, `tribe_events_community_section_after_cost`, `tribe_events_community_section_before_custom_fields`, `tribe_events_community_section_after_custom_fields`, `tribe_events_community_section_before_datetime`, `tribe_events_community_section_after_datetime`, `tribe_events_community_section_before_description`, `tribe_events_community_section_after_description`, `tribe_events_community_section_before_featured_image`, `tribe_events_community_section_after_featured_image`, `tribe_events_community_section_before_organizer`, `tribe_events_community_section_after_organizer`, `tribe_events_community_section_before_honeypot`, `tribe_events_community_section_after_honeypot`, `tribe_events_community_section_before_submit`, `tribe_events_community_section_after_submit`, `tribe_events_community_section_before_taxonomy`, `tribe_events_community_section_after_taxonomy`, `tribe_events_community_section_before_title`, `tribe_events_community_section_after_title`, `tribe_events_community_section_before_venue`, `tribe_events_community_section_after_venue`, `tribe_events_community_section_before_website`, `tribe_events_community_section_after_website`
* Tweak - Removed actions: `tribe_events_community_before_the_event_title`, `tribe_events_community_after_the_event_title`, `tribe_events_community_before_the_content`, `tribe_events_community_after_the_content`, `tribe_events_community_before_form_submit`, `tribe_events_community_after_form_submit`, `tribe_ce_before_event_list_top_buttons`, `tribe_ce_after_event_list_top_buttons`, `tribe_ce_before_event_list_table`, `tribe_ce_after_event_list_table`, `tribe_events_community_before_the_captcha`, `tribe_events_community_after_the_captcha`, `tribe_events_community_before_the_cost`, `tribe_events_community_after_the_cost`, `tribe_events_community_before_the_datepickers`, `tribe_events_community_after_the_datepickers`, `tribe_events_community_before_the_featured_image`, `tribe_events_community_after_the_featured_image`, `tribe_events_community_before_the_categories`, `tribe_events_community_after_the_categories`, `tribe_events_community_before_the_website`, `tribe_events_community_after_the_website`
* Language - 17 new strings added, 134 updated, 0 fuzzied, and 13 obsoleted [events-community]

= [4.4.7] 2017-06-01 =

* Fix - Fixed the display of the submission form to be more consistent in different page templates in default themes [75545]
* Tweak - Added new hooks: 'tribe_community_before_login_form' and 'tribe_community_after_login_form' [67510]

= [4.4.6] 2017-05-17 =

* Tweak - Further adjustments made to our plugin licensing system [78506]

= [4.4.5] 2017-05-04 =

* Fix - Made timepicker compatible with 24hr time format. [72674]
* Fix - Fix a fatal error introduced in our last release, relating to venues being submitted for events (thanks to @artsgso for flagging this!) [77650]
* Tweak - adjustments made to our plugin licensing system

= [4.4.4] 2017-04-19 =

* Fix - Improvements to the submission scrubber to improve safety and avoid conflicts with other plugins (props: @georgestephanis, @cliffordp) [72412]

= [4.4.3] 2017-03-23 =

* Fix - Ensure the Google Map settings for submitted/edited events are saved as expected (Thanks @Werner for the report in our support forums) [72124]

= [4.4.2] 2017-02-09 =

* Fix - Fix a bug that caused the z-index in .min vendor CSS file to be different than the non-minified file.(thanks to @Nicholas) [72603]
* Fix - Fixed untranslated strings within the frontend submission form [72576]

= [4.4.1] 2017-01-26 =

* Fix - Prevent Fatals from happening due to Missing classes Di52 implementaton [71943]
* Fix - Comments showing again for Posts and Pages [71943]
* Fix - Corrected a closing div tag position in the modules/cost template[72204]

= [4.4] 2017-01-09 =

* Feature - Design refresh for the front end submission form. [68498]
* Feature - The front end submission form now satisfies requirements for WCAG 2.0 Level AA. [69553]
* Fix - Ensure the submission form retains expected styles when default permalinks are in use [32409]
* Tweak - Made customization of the start and end date fields easier [32412]
* Tweak - Moved reCAPTCHA API key fields to new API Settings Tab [62031]
* Tweak - Organizers and Venues now have a better and cleaner interface [68430, 38129]
* Tweak - Adjustments to recurring event support in order to match changes made in Events Calendar PRO [66717]

= [4.3.2] 2016-12-20 =

* Tweak - Updated the template override instructions in a number of templates [68229]

= [4.3.1] 2016-10-20 =

* Tweak - Added plugin dir and file path constants.
* Tweak - Registered plugin as active with Tribe Common. [66657]

= [4.3] 2016-10-13 =

* Add - Add Community Events links for the add and list pages into the system information [41136]
* Add - Better styling and datepicker support for Community Events pages embedded via the [tribe_community_events] shortcode [32409]
* Tweak - Adjust helper text for redirect URL setting [28029]

= [4.2.5] 2016-09-15 =

* Fix - Ensure sample URLs for the /add/ and /list/ pages provided in the settings page match those currently in use (our heartfelt thanks to Asko in the forums for highlighting this discrepancy)
* Fix - Improve interaction with reCaptcha service and avoid errors when handling the result (our thanks to Christine in the forums for highlighting this problem)

= [4.2.4] 2016-08-17 =

* Fix - Front-end event edit form not displaying an event assigned categories [62547] (Thank you @indycourses for the report in the support forums.)
* Fix - Improve organizer and venue validation and add in two filters to validate individual fields for their respective linked posts [63949]

= [4.2.3] 2016-07-20 =

* Fix - Always Show Google Map and Link checkbox when editing an event [Thanks to @groeteke for reporting this on our forums.]
* Fix - Enable logged in users same access to community events form when anonymous submissions disabled [Emily of @designagency took the time to report this one. Thanks Emily.]

= [4.2.2] 2016-07-06 =

* Fix - Event before and after HTML content appearing two times when when listing events
* Fix - Fill in venue fields when editing it on the front end [62685]

= [4.2.1] 2016-06-22 =

* Fix - Adjust layout for List View in Twenty Sixteen

= [4.2] 2016-06-08 =

* Tweak - Language files in the `wp-content/languages/plugins` path will be loaded before attempting to load internal language files (Thank you to user @aafhhl for bringing this to our attention!)
* Tweak - Move plugin CSS to PostCSS
* Tweak - Adjusted text directing people to the new user primer
* Tweak - Updated venue and organizer templates to use the new architecture for attaching custom post types to events

= [4.1.2] 2016-05-19 =

* Fix - Make the fields within the organizer section of the event submission form sticky

= [4.1.1] 2016-03-30 =

* Fix - Allow the organizer metabox on the community add form to be overridden by themers via the new template: community/modules/organizer-multiple.php (Props to Mad Dog for reporting this issue)
* Fix - Resolved issue where the "Before HTML" content was sometimes duplicated on community pages (props to Brent for reporting this!)
* Fix - Removed whitespace to fix translation of submitted events (Props to Oliver for this report)
* Fix - Resolved various capitalization issues with German translations (Props to Oliver for reporting this issue as well)

= [4.1] 2016-03-15 =

* Feature - Added filter for changing the number of categories to display on the event add form: tribe_events_community_category_dropdown_shown_item_count
* Feature - Added a checkbox to remove the country, state/province, and timezone selectors from the event add form
* Fix - Fixed bug that prevented logged in users from submitting new venues and organizers when anonymous submissions were enabled

= [4.0.6] 2016-03-02 =

* Fix - Category is now inserted when uploading a featured image
* Fix - Errors on the public submission no longer reset the event date

= [4.0.5] 2016-02-17 =

* Fix - Prevent information on the confirmation Email to be related to the featured Image
* Tweak - Only allow valid URLs on Events Pro custom fields when community events are submitted

= [4.0.4] 2016-01-15 =

* Security - Security fix with front end submissions (props to grandayjames for reporting this!)

= [4.0.3] 2015-12-22 =

* Tweak - Include Admin edit link on the notification email sent when a new Event is submitted (Thank you Judy for reporting this!)
* Fix - Prevents notices from happening on the Add New Event page

= [4.0.2] 2015-12-16 =

* Tweak - Ignore default venue values to reduce the amount of duplicate venues generated by community organizers (Thank you Carly!)

= [4.0.1] 2015-12-10 =

* Tweak - Respect venue and organizer post type permissions when providing add/edit fields to a user
* Tweak - Added better support for creating organizers as an anonymous user
* Fix - Fields with multiple values now are kept if you get an error on the Community Submit page
* Fix - Resolved issue where creating new organizers and venues was failing for anonymous users
* Fix - Resolved bug where community event submissions that resulted in an error would cause some fields to be cleared out

= [4.0] 2015-12-02 =

* Feature - Added new Filter on Community Events related pages (`tribe_ce_i18n_page_titles`) (Thank you Mad Dog!)
* Tweak - Add support for wp_get_document_title in response to the WordPress 4.4 deprecation of wp_title
* Tweak - Output the "Advanced Template Settings" custom HTML before and after the event list and event add form (Thank you Benjamin for the heads up!)
* Fix - My Events ordered now reverse chronologically, as it was intended (earlier first)
* Fix - Better CSS for when My Events page Navigation has multiple pages
* Fix - Make some strings translatable that were not (Props to Oliver for bringing it to our attention)
* Fix - Resolved an issue where translations sometimes failed to load (Thanks for the report and the fix Murat!)

= [3.12.1] 2015-11-04 =

* Feature - Added support for the new Events Community Tickets plugin

= [3.12] 2015-09-08 =

* Security - Resolved JS vulnerability in minified JS by upgrading to uglifyjs 2.4.24
* Feature - Added support for Events PRO's Arbitrary Recurrence for events in the event submission form
* Feature - Added none option for both Radio and Dropdown Additional Fields (Thanks to Justin on the forums!)
* Feature - Modified timezone handling to take advantage of new capabilities within The Events Calendar
* Tweak - Added currency position field to the event submission form
* Tweak - Submitting a featured image that is too large will now generate an error
* Tweak - Relocated the ReCaptcha class to avoid conflicts with other ReCaptcha enabled plugins (Props to ryandc for the original report!)
* Tweak - Disable the organizer email obfuscation message on the Community Add form (Thank you cliffy for bringing this to our attention!)
* Tweak - Default Country being respected without locking the user options
* Fix - Resolved bug that prevented organizers from being identified as present in the submitted form when they were set as required fields (That you Rob for the report!)
* Fix - Fixed an issue with the admin bar showing for user roles that were blocked from admin
* Fix - Fixed an issue with additional fields not showing as selected when a symbol is included in the label (Props to Justin!)
* Fix - Fixed issue where the start and end dates for events were defaulted to the current hour on the Community Add form rather than the defaults used in the dashboard

= [3.11] 2015-07-22 =

* Security - Added escaping to a number of previously un-escaped values
* Feature - Event Categories that contain a hierarchy will now display in a hierarchical format when creating/editing events (Thank you Christian K for suggesting this on UserVoice!)
* Tweak - Switched the "Back" link that appears after deleting a Community Event to an actual URL rather than a JS history call (Thanks to Pablo for reporting this!)
* Tweak - Conformed code to updated coding standards
* Tweak - Changed priority on the 'parse_request' hooked method for compatibility with Shopp
* Bug - Fixed an issue where the Community Events UI was tucked under the sidebar in the TwentyFourteen theme
* Bug - Removed double-wrapped paragraph tags in error messages (Props to operapreneur for finding this!)
* Bug - Resolved an issue where localizable URL parts were not getting localized (Thank you kiralybalazs for the heads up!)
* Bug - Fixed some display issues with the community submission form in TwentyFifteen
* Bug - Resolved some notices about undefined variables

= [3.10] 2015-06-16 =

* Bug - Ensured all recurrence fields are required when a recurring event is submitted (thanks to sean on the forums for the report!)
* Bug - Fixed an issue causing the submission form datepicker fonts to be huge in the 2014 theme
* Bug - Fixed an issue causing the events-per-page setting to be ignored
* Bug - Fixed an issue where the Google Maps Link and Venue URL values were not correctly displaying on the edit form (thanks to pasada on the PRO forums for the report!)
* Bug - Fixed an issue where Venue, Organizer, and Website values were not preserved after a validation error
* Tweak - Plugin code has been refactored to new standards: that did result in a new file structure and many renamed classes. Old class names will be deprecated in future releases and, while still working as expected, you can keep track of any deprecated classes yours or third party plugins are calling using the Log Deprecated Notices plugin (https://wordpress.org/plugins/log-deprecated-notices/)
* Tweak - Improved messaging shown to customers when they upload an image exceeding the max permitted size
* Tweak - Improved the admin access controls so that an unauthenticated user visiting wp-admin is taken to wp-login.php
* Tweak - Added some changelog formatting enhancements after seeing keepachangelog.com :)
* Tweak - Improved compatibility with The Events Calendar 3.10 default values
* Feature - Incorporated updated Finish translation files, courtesy of Ari-Pekka Koponen
* Feature - Incorporated updated German translation files, courtesy of Oliver Heinrich
* Feature - Incorporated updated French translation files, courtesy of Sylvain Delisle
* Feature - Incorporated new Bulgarian translation files, courtesy of Nedko Ivanov
* Feature - Incorporated new Swedish translation files, courtesy of Johan Falk

= 3.9.1 =

* Hardened URL output to protect against XSS attacks.

= 3.9 =

* Added spam filtering based on reCaptcha (thanks to Scott Fennell for setting the initial framework on this!)
* Incorporated updated German translation files, courtesy of Oliver Heinrich
* Incorporated new Russian translation files, courtesy of Evgenii Rybak

= 3.8.2 =

* Fixed an issue with URL parsing that could cause a nasty unexpected deletion of most/all events in some situations (thanks to kiralybalazs in the forum for reporting this!)

= 3.8.1 =

* Ensured that categories are saved from community-submitted events under certain settings combinations (thanks to presis on the forums for the report!)
* Removed the dependency on Events Calendar PRO for community default content settings

= 3.8 =

* Fixed an issue with shortcodes pre-populating in the form (thanks to jhatzi for the report!)
* Fix a bug causing post status of submitted events to always revert to draft (thanks to bodin on the forums for the first report here!)
* Added a venue website/URL field to the frontend submission form (thanks to persyst on the forums for bringing this up!)
* Improved support and detection of 24hr time formats to those which include hours without a leading zero
* Default values for the community submission form no longer depend on defaults enabled in PRO
* Incorporated new Portuguese translation files, courtesy of Sérgio Leite
* Incorporated updated Italian translation files, courtesy of Gabriele Taffi
* Incorporated updated German translation files, courtesy of Oliver Heinrich
* Incorporated updated Finnish translation files, courtesy of Elias Okkonen

= 3.7 =

* Incorporated new Chinese translation files, courtesy of Massound Huang
* Incorporated new Indonesian translation files, courtesy of Didik Priyanto
* Incorporated updated Spanish translation files, courtesy of Juanjo Navarro
* Corrected an issue where submitted events were losing categories and metadata (Thanks to immeemz on the  forums for reporting this)
* Improved the effects of the `tribe_events_community_required_fields` filter for marking required fields (Thank you to Chris for the idea!)
* Fixed duplicate HTML ID in submission templates (Thank you integrity for bringing this to our attention)
* Fixed a bug where a user could view drafts events they did not have permission to see
* Changed email notification markup to a template for customization (Thank you hackauf for bringing this up)
* Added better error handling for submitting images with invalid file types
* Added the ability to override the labels & slugs for venues and organizers
* Renamed files and classes to be inline with official naming scheme

= 3.6.1 =

* Fix minification bug.

= 3.6 =

* Fixed editing of recurring events when Pro is active
* Added a "Delete All" option for recurring events
* Incorporated new Ukranian translation files, courtesy of Vasily Vishnyakov
* Incorporated updated German translation files, courtesy of Dennis Gruebner

= 3.5 =

* Fixed handling of user roles blocked from admin for superadmins on multisite
* Fixed an issue where borders weren't displaying properly on the WYSIWYG editor (thanks to memeco on the forums for his report here!)
* Fixed inconsistencies in the event submission form when PRO and Community have different default venues or organizers
* Updated sanitization filters to allow shortcodes in event descriptions by default (thanks to elmalak on the forum for reporting this!)
* Fixed broken templates when editing venues and organizers while using the default events template
* Fixed a variety of untranslatable strings
* Incorporated updated Romanian translation files, courtesy of Cosmin Vaman
* Added updated German translation files, courtesy of Oliver Heinrich
* Added updated Brazilian Portuguese translation files, courtesy of by Emerson Marques
* Incorporated updated Dutch translation files, courtesy of J.F.M. Cornelissen
* Incorporated updated Spanish translation files, courtesy of Juan Jose Reparaz Sarasola

= 3.4 =

* Added a “View Submitted Event” link that appears after a submission has gone through
* Addressed an issue where the datepicker would not honor the core WordPress (thanks to lamagia on the forums for the report!)
* Fixed a bug for PRO users where the custom venue and organizer configured in PRO would remain even after that plugin was deactivated

= 3.3 =

* Community now uses the same events template setting as core plugin views
* Default Events Template can now be chosen to display the submission form etc (not previously allowed)
* User-submitted data is more thoroughly scrubbed for malicious data
* Incorporated updated German translation files, courtesy of Oliver Heinrich
* Incorporated updated French translation files, courtesy of Bastien BC

= 3.2 =

* Fixed a bug where recurring event instances were not visible on the "My Events" list under certain settings
* Fixed a handful of minor PHP notices
* Added a minor improvement to recurrence settings fieldset display
* Fixed a bug where the datepicker was huge in some themes
* Template overrides for Community Events in your theme should now all be inside the [your-theme]/tribe-events/community directory; a deprecated notice will be generated if they are directly in the [your-theme]/tribe-events folder
* Incorporated updated French translation files, courtesy of Ali Senhaji

= 3.1 =

= SUBSTANTIAL UPDATES TO TEMPLATES! =

If you have customized your community events templates you'll probably have to redo your customizations for this upgrade. Proceed with caution!

The templates have been completely reorganized and substantially cleaned up. This will make it much easier for theme developers to work with the templates in the future.

Additional Changes:

* Improved behavior of recurring events deletion from My Events list
* Cannot reach the community list page *
* Fixed bug where new venues submitted via Community weren't being published along with their event
* Community now uses the specified Events template under Settings > Display
* Improved spam prevention technique (honeypot) implemented on the Community submission form
* Community submission form now respects default venue setting and hides the other venue fields (address, etc.)
* Community submission form now respects default content fields
* Event Website URL field is no longer missing from the Community submission form
* Styles are no longer stripped from Community submissions
* Fixed bug where the saved venues dropdown wasn't displaying on the Community submit form
* New Venues and Organizers no longer overwrite existing ones when editing an event
* Fixed bug where submit form wasn't working properly for anonymous users in some cases
* Users can now always view their My Events listing
* Users will no longer be redirected to wp-login.php upon logout, if they do not have dashboard access
* Updated translations: Romanian (new), Finnish (new)
* Various minor bug and security fixes

= 3.0.1 =

* Performance improvements to the plugin update engine
* Fixed two strings that weren't being translated in the admin bar menu

= 3.0 =

Updated version number to 3.0.x for plugin version consistency

= 1.0.7 =

* Fix plugin update system on multisite installations

= 1.0.6 =

*Small features, UX and Content Tweaks:*

* Code modifications to ensure compatibility with The Events Calendar/Events Calendar PRO 3.0.
* Incorporated new Norwegian translation files, courtesy of Eyolf Steffensen.
* Incorporated new Polish translation files, courtesy of Lukasz Kruszewski-Zelman.
* Incorporated new Swedish translation files, courtesy of Ben Andersen.
* Incorporated new Croatian translation files, courtesy of Jasmina Kovacevic.

*Bug Fixes:*

* Addressed a vulnerability where certain shortcodes can be used to exploit  sites running older versions of Community Events.
* Custom field values are no longer wiped after submitting when failing to check the anti-spam checkbox.
* Frontend community form now loads properly in WordPress 3.5 environments.
* Reinforced capabilities blocking unwanted users from the site admin.
* Users lacking organizer/venue edit permissions now see an appropriate error message.
* By removing the Next Event widget from Events Calendar PRO (see 3.0 release notes), we've eliminated the problem where Community and the Next Event widget conflicted when placed together on a page.
* Addressed a warning ("Creating default object from empty value") that impacted certain users.
* Corrected untranslatable elements in event-form.php.
* Addressed a bug causing labels to appear below fields on the frontend submit form for certain users.
* Redirect URLS (as configured under Events -> Settings -> Community) now function as expected.
* Removed various styling problems on the Twenty Twelve theme in WP 3.5.

= 1.0.5 =

*Small features, UX and Content Tweaks:*

(None this time)

*Bug Fixes:*

* Various bug fixes.

= 1.0.4 =

*Small features, UX and Content Tweaks:*

* Incorporated updated German translation files, courtesy of Marc Galliath.

*Bug Fixes:*

* Fixed a bug that led to a fatal error in the WordPress 3.5 beta 2.
* Removed an illegal HTML style tag from the frontend Community form.

= 1.0.3 =

*Small features, UX and Content Tweaks:*

* Clarified messaging regarding pre-populated "Free" text on cost field.
* Disabled comments from the frontend submission form.
* Added a filter -- 'tribe_community_events_event_categories' -- to allow users to filter the category list that appears on the frontend submission form.
* Added a new hook -- $args = apply_filters( 'tribe_community_events_my_events_query', $args ); -- at a user's request. This alteration allows you to tap into the WotUser object and pull out a list of events the user has access to and add them into this query.
* Incorporated new Dutch language files, courtesy of Jurgen Michiels.
* Incorporated new French language files, courtesy of Vanessa Bianchi.
* Incorporated new Italian language files, courtesy of Marco Infussi.
* Incorporated new German language files, courtesy of Marc Galliath.
* Incorporated new Czech language files, courtesy of Petr Bastan.

*Bug Fixes:*

* Removed a duplicate name attribute from venue-meta-box.php.
* Categories now save on events submitted by subscriber-level members.
* Categories now save on events submitted by anonymous users.
* The default state selection as configured in Events Calendar PRO now appears (along with the country) on the frontend submission form.
* Subscriber-level users may now edit events when that option is enabled under Events --> Settings --> Community.
* Reconfigured the cost field to work for frontend submissions on sites running the Eventbrite Tickets add-on + Community Events.
* Removed any lingering redirects to the WP Router Placeholder Page.
* My Events filtering options no longer conflict with the calendar widget.
* Fixed a broken link in the message that appears when Community Events is activated without the core The Events Calendar.
* Removed code causing a division by zero error in tribe-community-events.class.php.
* Styles from Community-related pages (events-admin-ui.css) no longer load on non-Community pages.
* Cleared up untranslatable language strings found in the 1.0.2 POT file.

= 1.0.2 =

*Bug Fixes:*

* Removed unclear/confusing message warning message regarding the need for plugin consistency and added clearer warnings with appropriate links when plugins or add-ons are out date.

= 1.0.1 =

*Small features, UX and Content Tweaks:*

* Removed the pagination range setting from the Community tab on Settings -> The Events Calendar.
* Added body classes for both the community submit (tribe_community_submit) and list (tribe_community_list) pages.
* Incorporated new Spanish translation files, courtesy of Hector at Signo Creativo.
* Incorporated new German translation files, courtesy of Mark Galliath.
* Added boolean template tags for tribe_is_community_my_events_page() and tribe_is_community_edit_event_page()
* Added new "Events" admin bar menu with Community-specific options

*Bug Fixes:*

* Rewrite rules are now being flushed when the allowAnonymousSubmissions setting is changed.
* Duplicate venues and organizers are no longer created with each new submission.
* Community no longer deactivates the Events Calendar PRO advanced post manager.
* Clarified messaging regarding the difference between trash/delete settings options.
* Header for status column is no longer missing in My Events.

= 1.0 =

Initial release
