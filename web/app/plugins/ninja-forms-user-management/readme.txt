=== Ninja Forms - User Management Extension ===
Author: Saturday Drive
Author URI: http://ninjaforms.com/?utm_source=Ninja+Forms+Plugin&utm_medium=Plugins+WP+Dashboard
Requires at least: 6.1
Tested up to: 6.4.0
Stable tag: 3.2.1
Copyright: 2013 Saturday Drive

Download Instructions
1. Please navigate the to the Plugins > Add New page in the Wordpress admin area of your website.
2. Click "Upload Plugin", browse to the directory you downloaded your add-on, click on the zip file, and then click "Open"
3. Click the "Install Plugin" button
4. Activate the plugin

License: GPLv2 or later

== Description ==
The User Management add-on allows you to create easy to use create login pages, register new users, and update the
profile of current users.

== Screenshots ==

To see up to date screenshots, visit [NinjaForms.com](http://ninjaforms.com).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms-user-management` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add a login, user registration, or edit profile action.
4. Map the fields you would like to use in the action and you're all set.

== Use ==

For help and video tutorials, please visit our website: [Ninja Forms Documentation](http://docs.ninjaforms.com/)

== Changelog ==

= 3.2.1 (08 November 2023)
*Bug Fixes:*
- Ensure capabilities are honored for submission access
- Ensure identical values don't override meta keys
- Ensure User Access settings are present when selecting roles

*Other:*
- Update standard build automation
- Remove deprecated codebase

= 3.2.0 (06 September 2022)
* Enhancements *
* Enable Admin to set role permissions to view/edit submissions
* Enable filter usage to change action timing

= 3.1.0 (11 October 2021)
*Enhancements:*
* Enable the use of email fields for mapping to username

*Other:*
* Add automated testing and build

= 3.0.12 (30 September 2019) =

*Bugs:*

* Login forms should now display an error for invalid email addresses that are used as usernames.
* Resolved an error that was causing user registration to throw a silent error on some servers.
* Continue 2 warnings should no longer appear in the error logs on php 7.3 or higher.
* Forms with a disabled register user action will no longer immediately display the message "Please logout to view this form".
* Forms with a disabled update profile action will no longer immediately hide the form from non-authenticated users.
* Updated the login setting of our register user action to properly go through the WordPress filters when login occurs.
* Registration with a duplicate email address should now properly throw an error.
* Login forms will now properly clear errors if accidentally submitted while blank.

= 3.0.11 (21 September 2018) =

*Changes:*

* Migrated password fields from Ninja Forms core, where they are now deprecated.

= 3.0.10 (24 August 2018) =

*Bugs:*

* Resolved an issue that sometimes caused users that had just logged in to be immediately logged back out upon loading the admin dashboard.
* The update profile action should no longer generate a warning when the email field is left blank.

*Changes:*

* Added a merge tag to output a logout link in HTML fields.
* User management merge tags now appear under their own header in the merge tag editor.

= 3.0.9 (5 July 2018) =

*Bugs:*

* Newly registered users should now be listed as the author of any posts created by the same form submission.

*Changes:*

* All strings should now be translatable.

= 3.0.8 (13 June 2018) =

*Bugs:*

* Password should now properly appear in the field mapping settings for the register user action.

= 3.0.7 (26 March 2018) =

*Bugs:*

* Resolved an issue that was contributing to increased page load times.
* Newly registered users should no longer see a message reading "Please log out to view this message".

= 3.0.6 (12 March 2018) =

*Bugs:*

* Resolved an issue causing some users that had logged in to be immediately logged back out.

= 3.0.5 (26 January 2018) =

*Changes:*

* Added the ability to update user role on profile edit.
* Registration forms can now be previewed without having to log out.

= 3.0.4 (14 December 2017) =

*Bugs:*

* Fixed an issue that sometimes caused logins to fail on sites running WooCommerce.

= 3.0.3 (22 August 2017) =

*Changes:*

* Added the ability to use email addresses as usernames.

= 3.0.2 (02 August 2017) =

*Changes:*

* Added support for custom user roles when registering users.

= 3.0.1 (21 June 2017) =

*Bugs:*

* The default registration form should now properly default user roles to "subscriber."
* Fixed a possible 500 error when activating the User Management add-on.

= 3.0 (04 April 2017) =

* Initial release