=== Ninja Forms - Conditionals Extension ===
Contributors: kstover, jameslaws, kbjohnson, klhall1987, Much2tall, deckerweb
Donate link: http://ninjaforms.com
Tags: form, forms
Requires at least: 5.2
Tested up to: 5.4
Stable tag: 3.1

License: GPLv2 or later

== Description ==
The Ninja Forms Conditionals Extension allows you to create "smart" forms that can change dynamically based upon user input. Options can be added to dropdown lists based upon other input, or fields can be hidden or shown.

== Screenshots ==

To see up to date screenshots, visit [NinjaForms.com](http://ninjaforms.com).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the 'Forms' menu item in your admin sidebar
4. When you create a form, you can now add conditionals on the field edit page.

== Use ==

For help and video tutorials, please visit our website: [Ninja Forms Documentation](http://ninjaforms.com/documentation/intro/)

== Changelog ==

= 3.1 (21 April 2021) =

*Changes:*

* Conditions can now compare date fields. This allows users to create conditions that trigger when someone selects or enters a specific date or range.

= 3.0.28 (4 August 2020) =

*Bugs:*

* Resolved an issue that was preventing file uploads from being saved properly upon submission.
* Conditions should no longer lose reference to fields that have been renamed in the form builder.

= 3.0.27 (23 July 2020) =

*Bugs:*

* Resolved an issue that was causing conditions on actions to be ignored.

= 3.0.26.2 (21 July 2020) =

*Bugs:*

* Resolved an issue with the plugin auto-updater.

= 3.0.26.1 (20 July 2020) =

*Security:*

* Patched a data spoofing vulnerability that allowed required fields to be bypassed.

= 3.0.26 (25 September 2019) =

*Bugs:*

* Resolved an issue that sometimes prevented actions from firing, even when they had no attached conditions.
* Forms with a Stripe or PayPal action should now properly complete once returning from the payment screen.

= 3.0.25 (16 September 2019) =

*Bugs:*

* Hidden fields should now properly evaluate against empty.
* Recaptcha fields that are hidden will now properly render once shown.

*Changes:*

* Fields can now be conditionally set as required.

= 3.0.24 (23 January 2019) =

*Bugs:*

* Conditions based on calculations should now be properly triggered on form load.
* Resolved an issue that was sometimes causing actions to always fire, regardless of conditions.

*Changes:*

* Several incompatible field types have been removed from the list of fields that conditions can be based on.
* Inverse statements will no longer be created by default on new do statements.

= 3.0.23 (11 January 2019) =

*Bugs:*

* Action processing will now ignore incomplete conditional statements, which previously prevented the action from firing.

= 3.0.22 (14 June 2018) =

*Changes:*

* Fields now display admin labels (if they exist) instead of labels in condition blocks.

= 3.0.21 (3 May 2018) =

*Bugs:*

* Equals conditions based on calculations should now work properly.

= 3.0.20 (19 April 2018) =

*Bugs:*

* Duplicating a field should no longer cause conditional logic to lose track of it as a conditional trigger.

= 3.0.19 (26 March 2018) =

*Bugs:*

* Conditions based on the selection of single checkbox fields should now function properly.

= 3.0.18 (24 February 2018) =

*Bugs:*

* Checkbox values can now be updated via conditional logic again.

= 3.0.17 (22 August 2017) =

*Bugs:*

* Actions that use the greaterthan and lessthan comparators should work properly.
* Incorrectly setup conditions should no longer cause form display to crash.

= 3.0.16 (02 August 2017) =

*Bugs:*

* Action conditions should now properly support calculations.
* Fixed a bug that could cause calculations to fail when using Conditional Logic. 

= 3.0.15 (27 June 2017) =

*Changes:*

* When setting up conditions, fields should now appear in alphabetical order within the field list.

*Bugs:*

* Conditional Logic should now work properly with the Save Progress add-on.

= 3.0.14 (31 May 2017) =

*Bugs:*

* Tabbing through a checkbox list that has conditions will no longer trigger those conditions incorrectly.

= 3.0.13 (02 May 2017) =

*Bugs:*

* Fixed a fatal error with PHP version 7.1 and higher.

= 3.0.12 (11 April 2017) =

*Changes:*

* Actions like Stripe can now be conditionally ran.

*Bugs:*

* Fixed a bug that caused some conditions to evaluate improperly.

= 3.0.11 (19 January 2017) =

*Changes:*

* Textbox fields can now be compared to an empty string.

*Bugs:*

* Help text should render properly for conditionally shown/hidden fields.

= 3.0.10 (09 December 2016) =

*Bugs:*

* Fixed a bug that could cause the condition drawer to fail to open if a field was deleted.
* Conditional Logic shouldn't prevent or enable actions that are otherwise disabled.

= 3.0.9 (15 November 2016) =

*Bugs:*

* Fixed a bug with list field options incorrectly triggering conditions based on partial matches.
* Fixed a bug with missing field values causing the form to not submit properly.
* Fixed a bug with false-positives when tabbing through a checkbox field.

*Changes:*

* Use the form cache for getting field data.
* Corrected processing for different data structures.
* Added a check for manually disabled actions, so as to not re-enable with conditions.

= 3.0.8 (25 October 2016) =

*Bugs:*

* The "any" operator in actions should work properly in all cases.
* Fixed a bug that caused fatal errors when conditions weren't configured properly.

= 3.0.7 (13 October 2016) =

*Bugs:*

* Creating conditions can now properly be based upon calculations.
* Fixed a bug with radio lists and the select option trigger.

*Changes:*

* When building conditions, fields should now show up with their admin label if one is set.

= 3.0.6 (03 October 2016) =

*Bugs:*

* Required fields should no longer attempt to valide upon show.
* Country fields can now be used in conditions.
* Fixed a couple of conversion issues with older form imports.
* Conditionally shown/hidden fields should all show properly in submission data.

*Changes:*

* Conditions can now be created using > and < with textboxes and textareas.

= 3.0.5 (28 September 2016) =

*Bugs:*

* File Uploads should now show in Conditional Logic conditions.

= 3.0.4 (22 September 2016) =

*Bugs:*

* Fixed a bug that could cause the builder to crash when fields were removed if there was a condition based upon that field.

= 3.0.3 (11 September 2016) =

* Bugs:*

* Fixed a bug that caused the condition edit drawer to fail to open.

= 3.0.2 (09 September 2016) =

* Fixed a bug with conversion.

= 3.0.1 (06 September 2016) =

* Updated with Ninja Forms v3.x compatibility

= 3.0 (06 September 2016) =

* Updated with Ninja Forms v3.x compatibility
* Deprecated Ninja Forms v2.9.x compatible code

= 1.4.0 (13 April 2016) =

*Changes:*

* Update for compatibility with WordPress 4.5 ( specifically the underscore.js update ).

= 1.3.9 (26 May 2015) =

*Bugs:*

* Changed values should now reset to defaults when using the "clear form" setting.

= 1.3.8 (12 May 2015) =

*Bugs:*

* Array elements should now work properly with the "Contains" action conditionals.
* Fixed a PHP Notice.
* Decimals should now be compared properly.
* Fixed a bug that could cause a PHP error if asp style tags are enabled in PHP.

= 1.3.7 (18 March 2015) =

*Bugs:*

* Fixed a bug that could cause conditional field data to submit improperly.

= 1.3.6 (17 March 2015) =

*Bugs:*

* Fixed a bug that could cause JavaScript to load older versions of files.

= 1.3.5 (4 March 2015) =

*Bugs:*

* Fixed a bug that could cause conditionally hidden calculations to fail.
* List options should work properly in version 2.9 of Ninja Forms.

= 1.3.4 (3 March 2015) =

*Bugs:*

* Fixed a bug that could prevent new conditions from being added.

= 1.3.3 (27 February 2015) =

*Changes:*

* Preparing for the release of Ninja Forms version 2.9.

= 1.3.1 (17 November 2014) =

*Bugs:*

* Fixing bad domain/translation issues.
* Fixed a bug with checkbox lists and notification conditions.
* Duplicating a page with conditions using multi-part forms should now properly duplicate those conditions.
* Fixed several issues related to i18n.

= 1.3 (28 October 2014) =

*Features:*

* Conditional Logic now supports conditional notifications.
* Only show, display, or send a notification when a user submits specific form data.

*Changes:*

* Custom conditional triggers can be added for notifications.

*Bugs:*

* Fixed a bug that caused conditionals based upon other conditional fields to work improperly.
* Conditionally hidden totals should now be properly removed from the all fields table.

= 1.2.7 (24 July 2014) =

*Changes:*

* Compatibility with Ninja Forms 2.7.

= 1.2.6 =

*Bugs:*

* Fixed a bug that prevented some users from getting automatic updates.

= 1.2.5 =

*Bugs:*

* Fixed a bug that could cause conditions not to work in some AJAX setups.

= 1.2.4 =

*Changes:*

* Conditionals should now not be applied when editing a form in the wp-admin.

*Bugs:*

* Fixed a bug with the change value setting.

= 1.2.3 =

*Bugs:*

* Fixed a bug that prevented the 'add_value' and 'change_value' actions from working properly in some instances.

= 1.2.2 =

*Bugs:*

* Fixed a bug with required fields that were conditionally hidden.
* Removed console logs that were causing problems in IE9.
* Fixed a bug that caused the Add Value setting not to work properly.

= 1.2.1 =

*Bugs:*

* Fixed several bugs that related to pre-populating conditional fields with multi-part forms.

= 1.2 =

*Changes:*

* Added support for the new Ninja Forms loading system. This should significantly improve loading speed for forms that use conditionals.

= 1.1.1 =

*Bugs:*

* Fixed a bug that could prevent conditionals from working properly with required fields.
* Fixed a bug that could cause conditional logic to break when labels contained long strings of HTML.

*Changes:*

* Updating the JS so that when an element is shown/hidden, a jQuery event is fired after the show/hide is complete.
* Removed old licensing file.

= 1.1 =

*Bugs:*

* Fixed a bug that caused the "Change Value" conditional action to fail in some cases.
* Fixed a bug that prevented conditionals from working properly with hidden fields.
* Fixed several PHP Notices.

= 1.0.10 =

*Bugs:*

* Fixed a bug that prevented calculations from working properly when a field that the calculation was based upon was hidden with conditional logic.

= 1.0.9 =

*Changes:*

* Added a "visible" data attribute.
* Moved functions from Ninja Forms core to this extension.

*Bugs:*

* Fixed several bugs related to using calculation fields and conditionals.

= 1.0.8 =

*Changes:*

* Changed the license and auto-update system to the one available in Ninja Forms 2.2.47.

= 1.0.7 =

*Changes:*

* Changed references to wpninjas.com to ninjaforms.com.

= 1.0.6 =

*Bugs:*

* Fixed a bug that prevented conditionals from working properly in some installs.

= 1.0.5 =

* Fixed a bug that caused Conditionals to break calculation fields if they were hidden.

= 1.0.4 =

*Changes:*

* Updates for compatibility with WordPress 3.6

= 1.0.3 =

*Bugs:*

* Fixed a bug that prevented conditionals from working properly with calculation fields.

= 1.0.2 =

*Bugs:*

* Fixed a bug that caused conditionals with multiple criteria to fail when connected with the "All" parameter.

= 1.0.1 =

*Changes:*

* The Conditionals Extension can now be used with the Multi-Part Extension to show or hide entire pages.

= 1.0 =

*Bugs:*

* Fixed a bug that was causing dropdown list fields to work improperly with Conditional Logic.

= 0.9 =

*Bugs:*

* Fixed a bug that prevented conditionals from working properly with multi-checkbox lists and multi-radio button lists.

= 0.8 =

*Changes:*

* Changed the display JS slightly to be more efficient.

= 0.7 =

*Bugs:*

* Conditional fields should now behave as expected when editing user submissions.

= 0.6 =

*Bugs:*

* Fixed a bug that prevented conditionals from working properly with checkbox and radio list types.

= 0.5 =

*Changes:*

* Moved a JS function from ninja-forms-conditionals-admin.js to the ninja-forms-admin.js.

= 0.4 =
* Fixed a bug that prevented multiple forms with conditionals from being placed on the same page.

= 0.3 =
* Various bug fixes including:
* Adding multiple forms with conditions to a single page will now work normally.

= 0.2 =
* Various bug fixes.
* Changed the way that javascript and css files are loaded in extensions.