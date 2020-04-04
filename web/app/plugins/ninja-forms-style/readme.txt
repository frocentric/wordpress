=== Ninja Forms - Layout & Styles Extension ===
Contributors: kstover, jameslaws, kbjohnson90
Donate link: http://ninjaforms.com
Tags: form, forms, CSS
Requires at least: 5.0
Tested up to: 5.2
Stable tag: 3.0.28

License: GPLv2 or later

== Description ==
The Ninja Forms Layout & Styles Extension allows users to create very complex layouts and styles with little to no experience with CSS right in their WordPress admin.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms-style` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Use ==

For help and video tutorials, please visit our website: [NinjaForms.com](http://ninjaforms.com)

== Changelog ==

= 3.0.28 (9 October 2019) =

*Bugs:*

* Styles should now be honored on all forms when there are multiple copies of the same form on a page.

*Changes:*

* Added support for styling file upload fields.

= 3.0.27 (12 June 2019) =

*Changes:*

* Cleaned up a few older scripts that were slowing things down in the form builder. Drag and drop should now be more performant.

= 3.0.26 (4 April 2019) =

*Bugs:*

* Resolved an issue that was flooding error logs with warnings in php 7.3.

= 3.0.25 (19 September 2017) =

*Bugs:*

* Fixed a bug with Required Fields not being properly validated on submission.

= 3.0.24 (15 September 2017) =

*Changes:*

* Reverts unnecessary changes, where the compatibility issue with Multi-Part is solved in Multi-Part.

= 3.0.23 (14 September 2017) =

*Changes:*

* Re-release with missing files.

= 3.0.22 (13 September 2017) =

*Bugs:*

* Fixed a bug with PHP Warning messages in the generated CSS output.
* Fixed a bug with field duplication.

= 3.0.21 (02 August 2017) =

*Bugs:*

* Fixed a bug that caused calculations to fail when using Layout & Styles, Multi-Part Forms, and Conditional Logic.

= 3.0.20 (21 June 2017) =

*Changes:*

* Added a button to "remove all styles" from the plugin-wide styling section.

= 3.0.19 (02 May 2017) =

*Changes:*

* Added Multi-Part Forms plugin styles if the Multi-Part Forms add-on is active.

= 3.0.18 (11 April 2017) =

*Bugs:*

* Forms should now import with proper field orders in all installations.

= 3.0.17 (17 March 2017) =

*Bugs:*

* Completed a deep-dive of all plugin-wide styling settings. They should all work properly now. 

= 3.0.16 (07 March 2017) =

*Bugs:*

* Fixed a bug that could cause the builder to crash without warning in some instances.

= 3.0.15 (02 Februrary 2017) =

*Bugs:*

* Fixed a PHP warning about unset variables.

= 3.0.14 (26 January 2017) =

*Changes:*

* Added a filter for Ninja Forms version 3.0.25 that allows Layout & Styles to correctly order fields in submissions.

= 3.0.13 (19 January 2017) =

*Bugs:*

* Saved fields can now be properly dragged onto rows and dividers.

= 3.0.12 (21 November 2016) =

*Bugs:*

* Field styles should apply properly on the front-end in all cases.

= 3.0.11 (26 October 2016) =

*Bugs:*

* Fixed a bug that was introduced in version 3.0.10 that caused rows with empty columns to be removed.
* Sometimes column widths were totalling over 100%. This caused fields to wrap around to a new row.

= 3.0.10 (17 October 2016) =

*Bugs:*

* Corrupt data should no longer cause Layouts to crash the builder with an "undefined ParentNode" error.

= 3.0.9 (12 October 2016) =

*Changes:*

* Added better error handling for missing field types. This should prevent some JS errors from being thrown on display.

= 3.0.8 (28 September 2016) =

*Bugs:*

* Fixed a bug that caused field order to randomise when sorting fields between cells and rows.
* Fixed a bug with template imports that caused templates to fail.
* Added version number to script inclusion to help prevent caching issues.
* Fixed a bug with hover styles not being applied to submit buttons.

= 3.0.7 (26 September 2016) =

*Bugs:*

* Fixed a bug with conversion from the RC of Ninja Forms.

= 3.0.6 (11 September 2016) =

*Bugs:*

* Fixed a bug with conversion and invalid form layouts.

= 3.0.5 (09 September 2016) =

* Fixed a bug with conversion.

= 3.0.4 (08 September 2016) =

*Bugs:*

* Fixed a bug with applying plugin wide styles.

= 3.0.3 (06 September 2016) =

*Bugs:*

* Compatibility with Multi-Part Forms v3.0.

= 3.0.2 (06 September 2016) =

*Bugs:*

* Improved conversion efficiency.
* Compatibility with Multi-Part Forms v3.0.

= 3.0.1 (27 July 2016) =

*Bugs:*

* Fixed a bug that could break form conversion in Ninja Forms.

= 3.0.0 =

* Updated with Ninja Forms v3.x compatibility
* Deprecated Ninja Forms v2.9.x compatible code

= 1.2.7 (12 May 2015) =

*Bugs:*

* Fixed a bug that could cause column layouts to fail even when they are correct.

= 1.2.6 (17 March 2015) =

*Bugs:*

* Fixed an issue with browser caching that could cause older versions of JavaScript files to load.

= 1.2.5 (4 March 2015) =

*Bugs:*

* Fixed a bug that removed the Multi-Part Styles section if that extension was also activated.

= 1.2.4 (3 March 2015) =

*Bugs:*

* Fixed a bug that could cause a fatal error if Ninja Forms core was deactivated.

= 1.2.3 (4 February 2015) =
*Changes:*

* Preparing for compatibility with Ninja Forms version 2.9.

= 1.2.2 (24 November 2014) =

*Bugs:*

* Fixed a bug that could prevent field layouts from saving properly.

= 1.2.1 (17 November 2014) =

*Changes:*

* Adding a warning for a common invalid column layout error.

*Bugs:*

* Fixed a bug with default styling.
* Updated support for i18n.

= 1.2 =

*Bugs:*

* Fixed a bug that prevented the new options from showing up.

*Changes:*

* Added rating-specific styles on a per-field basis.
* Added individual styling to rating fields.
* Converting Layout and Styles over to the new Ninja Forms loading class.
* Added per form title styling.

= 1.1.1 =

*Bugs:*

* Fixed a bug that prevented multi-part pages from being added on the Layout and Styles tab.
* Fixed some CSS specificity errors with textboxes and textareas.

*Changes:*

* Admin scripts should now load the min or dev versions based on the NINJA_FORMS_JS_DEBUG constant.
* Added display selector.
* Adjusted what's advanced and what's basic.
* Limited some selectors from Default Field Styles.
* Moved styles to be output before form and not after.

= 1.1 =

*Bugs:*

* Fixed a bug that could cause multi-part forms to behave incorrectly when styled.
* Adjusted a CSS selector that could cause styles from not applying properly.

= 1.0.9 =

*Bugs:*

* Fixed a fairly major bug with Layout & Styles and Multi-Part forms that could cause multi-columned pages to behave incorrectly.

= 1.0.8 =

*Changes:*

* Added additional styles for core such as an error selector fix, form title, button and hover. Also added styles for MP Page Titles, and pre / next hovers.

= 1.0.7 =

*Changes:*

* Changed the license and auto-update system to the one available in Ninja Forms 2.2.47.

= 1.0.6 =

*Bugs:*

* Fixed a bug that prevented previous and next button in multi-part forms to be styles.

*Changes:*

* improved i18n compatibility.

= 1.0.5 =

*Changes:*

* Changed references to wpninjas.com to the new ninjaforms.com.

= 1.0.4 =

*Bugs:*

* Fixed a bug that prevented the per form hover state styles being applied to submit buttons.

= 1.0.3 =

*Bugs:*

* Fixed a bug that prevented List fields from working properly on the Default Field Styles tab.

= 1.0.2 =

*Changes:*

* Updates for compatibility with WordPress 3.6

= 1.0.1 =

*Bugs:*

* Fixed a visual bug with the placement of the Form Settings metabox.

= 1.0 =

*Bugs:*

* Fixed a bug that was preventing the "Field Type Settings" tab from working properly.

= 0.9 =

*Changes:*

* The selector used for the "next" and "previous" buttons in Multi-Part Forms has been changed.
* Added "Page" styles for use with AJAX submissions and Multi-Part Forms.

= 0.8 =

*Features:*

* Added new AJAX submissions and Multi-Part Forms styling options.

= 0.7 =

*Changes:*

* Added a filter to the fields array that is output on the layout editing screen.

= 0.6 =

*Bugs:*

*Bugs:*

* Fixed a bug that could cause the "Error Message Styles" from saving properly.

= 0.5 =

*Bugs:*

* The admin JS file should now include properly on sites using versions of WordPress before 3.5.

= 0.4 =

*Features:*

* Added styling options for Multi-Part Forms elements.

= 0.3 =
* Fixed a bug in the minified JS.

= 0.2 =
* Fixed a bug that prevented some users from activating their installations.

= 0.1 =
* Initial release
