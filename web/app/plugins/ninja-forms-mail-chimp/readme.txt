=== Ninja Forms - MailChimp ===
Contributors: wpninjasllc, kbjohnson90, pippinsplugins, klhall1987
Tags: form, forms, ninja forms, mailpoet, wysija, newsletters, email
Requires at least: 4.7
Tested up to: 4.9
Stable tag: 3.1.9
License: GPLv2 or later

== Description ==

This extension integrates Ninja Forms with MailChimp by providing an option for your customers to signup for your newsletter lists while submitting a form.

= Features =

* Sign up for any MailChimp newsletter list with any Ninja Forms form submission

== Installation ==

This section describes how to install the plugin.

1. Upload the `ninja-forms-mail-chimp` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 3.1.9 (27 November 2018) =

* Resolved an issue that was causing the plugin to always appear as if an update was available.

= 3.1.8 (1 November 2018) =

* Added check to catch fatal errors if the API response is malformed.

= 3.1.7 (10 May 2018) =

* Resolved an error that sometimes occurred if only a single merge field was mapped to a form.

= 3.1.6 (17 April 2018) =

* Updating existing subscribers should no longer remove non-included information from their records.
* Added a new form template for basic MailChimp signup.

= 3.1.5 (26 March 2018) =

* Resolved an issue that was sometimes causing data to not be sent to MailChimp.

= 3.1.4 (12 March 2018) =

* Resolved an issue that sometimes caused a fatal error to be thrown when a bad API key was entered on the settings page.
* List fields and interest groups will now pull in more values if they are available. (Up to a maximum of 100, as allowed by the MailChimp API.)

= 3.1.3 (21 February 2018) =

* Pre-existing users who subscribe to a new list should now be updated properly.

= 3.1.2 (8 February 2018) =

* Fixed an issue that was causing the API to only import a maximum of 10 lists.

= 3.1.1 (6 February 2018) =

* Resolved an issue that sometimes caused interest groups to not be sent to MailChimp.

= 3.1.0 (5 February 2018) =

* MailChimp API version has been updated to 3.0.
* SSL Verify Peer setting is no longer necessary and has been removed.
* MailChimp actions malfunctioning as a result of removing lists/groups from MailChimp can now be fixed by refreshing list data in the MailChimp action.

= 3.0.5 (30 December 2017) =

* Raised the maximum number of lists that can be imported from 25 to 100.

= 3.0.4 (21 June 2017) =

* Fixed a bug that caused MailChimp to fail when using other MailChimp plugins.
* MailChimp actions should now fire after Collect Payment actions.

= 3.0.3 (31 October 2016 ) =

* Fixed a possible fatal error when saving incorrect API Keys.

= 3.0.2 (11 April 2016 ) =

* Compatibility with Ninja Forms Three.

= 3.0.1 (11 April 2016 ) =

* Fix an issue with licensing and automatic updates.

= 3.0 (7 March 2016 ) =

* Updated with Ninja Forms v3.x compatibility
* Deprecated Ninja Forms v2.9.x compatible code

= 1.3.4 (3 August 2015 ) =

* Fixed an undefined index when a list does not have any groups
* Fixed an error when a list does not have any groups

= 1.3.3 (27 July 2015 ) =

* Fixed a fatal error when Ninja Forms core is deactivated.

= 1.3.2 (27 May 2015)

* Fix fatal error when list has no interest groupings

= 1.3.1 (27 May 2015)

* Fix fatal error due to undefined class

= 1.3 (26 May 2015)

* Moved MailChimp integration options to Emails and Actions API
* Added support for multiple MailChimp subscriptions per form
* Added support for mapping form fields to merge fields in MailChimp
* Added support for MailChimp groups

= 1.2.1 (20 April 2015)
* Fixed invalid API key check when saving form settings

= 1.2 (16 April 2015)

* Added an option to disable SSL verification
* Improved error message when an API key is invalid

= 1.1.3 (7 February 2015)

* Cached the lists data in a transient
* Updated the settings field description to provide a sample API key
* Added API key validation to the save function to ensure a valid key is entered

= 1.1 (19 September 2014) =

* Updated the MailChimp API
* Added support for tracking Zip/Postal Code, Phone, and IP for subscribers

= 1.0.3 (20 August 2014) =

* Moved processing to the ninja_forms_post_process hook.