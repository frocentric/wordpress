=== Ninja Forms - Zapier ===
Contributors: davidhme, fatcatapps
Tags: form, forms, zapier
Requires at least: 3.3
Tested up to: 4.8.1
Stable tag: 3.0.8

License: GPLv2 or later

== Description ==
Ninja Forms - Zapier is a WP plugin that integrates [Ninja Forms](http://ninjaforms.com/) with [Zapier](http://zapier.com/).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms-zapier` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 3.0.8 (8 May 2018) =
* Form title is now sent to Zapier.
* List fields now send all available options alongside the selected option.
* Checkbox fields should now send their readable values.

= 3.0.7 (23 November 2017) =
* Plugin activation should now work properly from the plugin installation screen. 

= 3.0.6 (31 August 2017 ) =
* Add/fix i18n
* Improved integration with File Uploads plugin
* Convert false values to empty strings (these were previously interpreted as a 0 by Zapier)

= 3.0.5 (2 May 2017 ) =
* Remove hidden field types from Zapier submissions

= 3.0.4 (28 March 2017 ) =
* Fix license numbering

= 3.0.3 (24 March 2017 ) =
* Fixed fields with the same name not being sent to Zapier.
* Removed Zapier test sync on form save.  To test your connection with Zapier, submit a preview form.

= 3.0.2 (29 November 2016 ) =

* Added sequence number
* Fixed issue with File Uploads addon - uploaded file URLs will now display in Zapier

= 3.0.1 (06 September 2016 ) =

* Update to v3.0.1 to fix core compatibility issue

= 3.0 (22 March 2016 ) =

* Updated with Ninja Forms v3.x compatibility
* Converted form settings to form action
* Moved sync process to on form publish, removed sync button

* Deprecated Ninja Forms v2.9.x compatible code

= 1.1.2 =

* Made "Sync"-button message show in form_settings tab of Ninja Forms 2.9 

= 1.1.1 =

* Bugfix: Fixed incompatibility with Calculation (Output Calculation as HTML) - Field

= 1.1 =

* Added sync-button. You can now sync your form with Zapier without having having to do sample form-submissions.

= 1.0.2 =

* Updated readme.txt

= 1.0.1 =

* Minor bugfix

= 1.0.0 =

* Initial release
