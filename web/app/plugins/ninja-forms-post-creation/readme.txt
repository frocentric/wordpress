=== Ninja Forms - Post Creation Extension ===
Contributors: kbjohnson90, kstover, jameslaws
Donate link: http://ninjaforms.com
Tags: form, forms
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 3.0.7

License: GPLv2 or later

== Description ==
The Ninja Forms Post Creation Extension allows you to create posts from the front-end of your website. You can allow users to supply post title, content, tags, and categories, even custom taxonomies. Posts can be added to any post type.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the 'Forms' menu item in your admin sidebar
4. When you create a form, you will have post creation options on the form settings page.

== Use ==

For help and video tutorials, please visit our website: [NinjaForms.com](http://ninjaforms.com)

== Changelog ==

= 3.0.7 (26 April 2018) =

*Changes:*

* Added a new form template for creating a basic post.

= 3.0.6 (3 January 2018) =

*Bugs:*

* Resolved an issue that sometimes caused Posts to not be created if no excerpt was included.

= 3.0.5 (13 December 2017) =

*Bugs:*

* Termslist fields now proprely set terms and taxonomies upon submission.

= 3.0.4 (20 January 2017) =

*Changes:*

* Added a filter for the created post meta value.

= 3.0.3 (11 January 2017) =

*Bugs:*

* Fixed a bug with license keys and automatic updates.

= 3.0.2 (06 September 2016) =

*Changes:*

* Updated for Ninja Forms v3 compatibility.

= 3.0.1 (06 September 2016) =

*Changes:*

* Updated for Ninja Forms v3 compatibility.

= 3.0.0 =

*Changes:*

* Updated for Ninja Forms v3 compatibility.

= 1.0.13 (04 February 2016) =

*Changes:*

* Assigned post_author as current user.

= 1.0.12 (08 September 2015) =

*Changes:*

* Added a filter nf_post_creation_user_dropdown to disable the author dropdown. On sites with large numbers of users, this prevents pages from crashing.

= 1.0.11 (12 May 2015) =

*Bugs:*

* Term nesting should now work for more than two terms deep with the term field.
* Post meta should save and display properly.

= 1.0.10 (29 April 2015) =

*Bugs:*

* Fixed a bug that prevented post meta from saving properly.

= 1.0.9 (26 March 2015) =

*Bugs:*

* Fixed a bug that prevented Post Creation from working with version 2.9 of Ninja Forms core.

= 1.0.8 (17 November 2014) =

*Changes:*

* The extension should now be fully translatable.

= 1.0.7 =

*Bugs:*

* Fixed php notices.

= 1.0.6 =

*Changes:*

* Changed references to wpninjas.com to ninjaforms.com.

*Bugs:*

* Various minor bugfixes.

= 1.0.5 =

*Features:*

* Added a new option to the List field that allows it to be populated with a post term. This can be used in place of the Post Term field.

= 1.0.4 =

*Bugs:*

* Fixed a bug that prevented the post excerpt from saving properly.

= 1.0.3 =

*Bugs:*

* Fixed a bug that was causing an "Undefined notice" to appear upon form submission.

*Changes:*

* Changed the post elements (Title, content, etc.) so that they now save in the Ninja Forms submissions database. If you do not want to save created posts as submissions as well, please uncheck the "Save submission" box on the "Form Settings" tab.

* Added a new filter ninja_forms_add_post_meta_value that can be used to modify the user submitted value before it is inserted as custom post meta.

= 1.0.2 =

*Bugs:*

* Added shortcode parsing to the Default Post Title. This means that you can now use the [ninja_forms_field id=] shortcode there.

= 1.0.1 =

*Bugs:*

* Fixed a bug that was causing an output error when using the Post Tags field.

= 1.0 =

*Bugs:*

* Fixed a bug that prevented post creation from working properly with multi-part forms.

= 0.9 =

*Bugs:*
* Fixed a bug that was preventing some users from being able to create posts properly.

= 0.8 =

*Features:*

* Added a Post Excerpt field.

= 0.7 =

*Changes:*

* Modified the layout of the Post Creation metabox to make it easier to understand.

*Bugs:*

* Minor bug fixes and code reformatting.

= 0.6 =
* Slightly changed the display CSS.

= 0.5 =
* Fixed a bug that was causing media inserted with the tinyMCE editor to show up as links rather than embedded images.

= 0.4 =
* Various bug fixes including:
* A bug that prevented non-logged in users from posting to categories or terms.
* A bug that caused poor interaction with the Uploads Extension.

* Changed the advanced post content creator to a rich text area.

= 0.3 =
* Various bug fixes.

= 0.2 =
* Various bug fixes.
* Changed the way that javascript and css files are loaded in extensions.
