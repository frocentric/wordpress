=== Ninja Forms - File Uploads Extension ===
Contributors: kstover, jameslaws
Donate link: http://ninjaforms.com
Tags: form, forms
Requires at least: 5.0
Tested up to: 5.7
Stable tag: 3.3.11

License: GPLv2 or later

== Description ==
The Ninja Forms File Uploads Extension allows users to upload files. These files are stored in a database that can be browsed or searched by an administrator. Files can be downloaded or deleted by administrators as well.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms-uploads` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the 'Forms' menu item in your admin sidebar
4. When you create a form, you can now add upload fields on the field edit page.
5. A "File Uploads" link will now appear underneath the main "Forms" admin menu.

== Use ==

For help and video tutorials, please visit our website: [NinjaForms.com](http://ninjaforms.com)

== Changelog ==

= 3.3.11 (24 Mar 2021) =

*New:*

* Tested up to WordPress 5.7
* Compatibility with PHP 8

*Bugs:*

* PHP notices when using PHP 8
* Cyrillic characters are removed from file names when uploading to Google Drive

= 3.3.10 (27 Oct 2020) =

*New:*

* Advanced setting in the External File Upload action to upload files in the background

*Bugs:*

* Fix fatal error 'Uncaught NF_FU_VENDOR\Google_Service_Exception' when Google Drive connection is changed when background uploading a large file
* Fix images not appearing when using the embed mergetag in PDFs
* Fix files with cyrillic characters in the filename being uploaded as unnamed-file

= 3.3.9 (6 Oct 2020) =

*Bugs:*

* Fix uploads taking a long time when renaming file directories
* Fix File Upload mergetags not available in PDFs created at form submission time
* Fix cancel button not using label defined in field display settings

= 3.3.8 (7 Sept 2020) =

*New:*

* Compatibility with the PDF Submissions addon

= 3.3.7 (10 Aug 2020) =

*Bugs:*

* Fix background upload jobs duplicating files when multiple file upload fields on a form

= 3.3.6 (4 Aug 2020) =

*Changes:*

* Tested on WordPress 5.5
* Default Amazon S3 ACL set to private
* Filter 'ninja_forms_uploads_default_region' added for the default region for S3 clients

*Bugs:*

* Fix uploading of any size file in the background to external services
* Fix background uploading not working if memory_limit defined in Gigabytes

= 3.3.5 (25 June 2020) =

*New:*

* Setting to make the URLs to files upload to external service providers accessible to non-logged in users

*Changes:*

* Updated jQuery File Upload library to v10.30.1.

*Bugs:*

* Fix Google Drive file preview URL not working
* Fix cancel button doesn't show to allow clearing upload errors
* Fix files with non UTF8 characters not reloaded when using the Save Progress addon

= 3.3.4 (10 June 2020) =

*Bugs:*

* Fix mergetags variations not rendering the correct value
* Fix external file url redirecting to site admin home if the service is no longer connected

= 3.3.3 (26 May 2020) =

*New:*

* Button to cancel a file upload, or clear an error after a failed upload

*Bugs:*

* File upload links being stripped from emails and success messages
* Fatal error when uploading multiple files to Google Drive and renaming the file names
* Required field validation error shows when the 'Select Files' button clicked
* Missing file upload when submitting a form that had previously been saved with the Save Progress addon
* Multiple file upload URLs appearing as one large broken link in emails
* PHP notices about mergetag values when saving a form
* Browse uploads table losing form filter when paginating or sorting

= 3.3.2 (23 March 2020) =

*Bugs:*

* Some large files corrupted when uploading to Dropbox
* Error not displayed when uploading a file larger than the maximum defined in settings
* Fix PHP Notice:  Undefined variable: mime_types_whitelist

*Changes:*

* Added new filter 'ninja_forms_uploads_tmp_dir' to change the temp directory for uploads
* Added external service $slug parameter to the filter 'ninja_forms_uploads_should_background_upload'

= 3.3.1 (9 March 2020) =

*Bugs:*

* Security flaw which could allow files with blacklisted file extensions to be uploaded

= 3.3.0 (2 March 2020) =

*New:*

* Files larger than the server maximum upload size can now be uploaded
* Large files uploaded to external services in the background, no longer holding up form submission

*Bugs:*

* Uploads and External settings page can't be saved due to missing nonce
* File uploaded to multiple external services being uploaded with the incorrect timestamp filename prefix

= 3.2.6 (21 January 2020) =

*Bugs:*

* Unknown upload error when the same form appears twice on a page

= 3.2.5 (16 January 2020) =

*Bugs:*

* Fatal error: Cannot redeclare pcntl_signal() on some install

= 3.2.4 (9 January 2020) =

*Bugs:*

* Select File button not working on Internet Explorer 11 still

= 3.2.3 (8 January 2020) =

*Changes:*

* Added new filter 'ninja_forms_uploads_s3_acl' to change the Amazon S3 ACL to 'private' when uploading to buckets that are set as private

*Bugs:*

* Drag and dropping files when using multiple File Upload fields results in uploads to all fields
* Google Drive connection getting disconnected incorrectly for some users
* Select File button not working on Internet Explorer 11
* Fatal errors on installs using PHP less than 7.1
* Translatable string containing file size data could not be translated

= 3.2.2 (28 November 2019) =

*Bugs:*

* Fatal error on some installs running plugins with the Google API SDK

= 3.2.1 (25 November 2019) =

*Bugs:*

* Fatal error Uncaught Error: Call to undefined method Composer\Autoload\ClassLoader::setClassMapAuthoritative()
* Fatal error intermittently after connecting to Google Drive
* Google Drive account disconnect when issue connecting to API

= 3.2.0 (5 November 2019) =

*New:*

* You can now use mergetags in the 'Rename Uploaded File' field setting
* Different mergetags for File Upload fields can now be selected, eg. {field:my_field_key:link}
* New mergetag for the filename of the file, eg. {field:my_field_key:filename}
* New mergetags for when the file has been added to the media library, eg. {field:my_field_key:attachment_url}, {field:my_field_key:attachment_embed}

*Changes:*

* Ability to set Amazon key and secret and other settings as constants: 'NF_FU_AMAZON_S3_ACCESS_KEY' and 'NF_FU_AMAZON_S3_SECRET_KEY'

*Bugs:*

* Nonce error when submitting a form on a page that has been cached
* Fatal error caused when running UpdraftPlus or BackupBuddy plugin

= 3.1.2 (17 October 2019) =

*Changes:*

* Use the external service name not the slug in the uploads browser table
* Make the external service 'Connect' button stand out from the 'Disconnect' button

*Bugs:*

* Fatal error caused when running the EDD Amazon S3 plugin
* Mergetag output blank when running the Conditional Logic plugin

= 3.1.1 (10 October 2019) =

*Bugs:*

* Fatal error when uploading to Dropbox on installs with legacy Dropbox API tokens
* Fatal error due to memory exhaustion on some installs due to large Google SDK

= 3.1.0 (8 October 2019) =

*New:*

* Google Drive support
* Full integration with Layout & Styles addon
* Adds support for File Upload field to the realistic form builder
* New minimum file size field setting
* Add 'View Submission' link for each upload in the 'Browse Uploads' screen

*Bugs:*

* Fixes mergetag string literal sent to actions when there are no files uploaded
* Fixes Undefined index: type PHP notice due to mergetag code
* Fixes links showing in submission table and 'Browse Uploads' screen for files that didn't exist

= 3.0.27 (1 July 2019) =

*Changes:*

* New filter: 'ninja_forms_upload_mime_types_whitelist' to amend the mime types global upload whitelist
* New filter: 'ninja_forms_upload_check_mime_types_whitelist' to stop checking the global mime types whitelist
* New translation pack for Polish, props [DreadsSupp](https://github.com/DreadsSupp)

= 3.0.26 (15 May 2019) =

*Bugs:*

* Fixes 'No field ID supplied' error message when post_max_size PHP config value is lower than upload_max_filesize.

= 3.0.25 (8 May 2019) =

*Bugs:*

* Nonce errors should no longer occur when multiple instances of the same form exist on a page.

= 3.0.24 (22 April 2019) =

*Bugs:*

* Fixes the bypassing of a required file upload field if a previously uploaded file is deleted.

*Changes:*

* Updated jQuery File Upload library to v9.30.0.

= 3.0.23 (11 April 2019) =

*Bugs:*

* Critical security flaws which could allow file extensions to be changed and files to be traversed (Props [Jasper Weijts, Onvio](https://www.onvio.nl))

= 3.0.22 (30 November 2018) =

*Changes:*

* Attachment ID mergetag variation to be used when saving file upload to the media library, eg. {field:file_upload_22:attachment_id}

= 3.0.21 (29 November 2018) =

*Bugs:*

* Form cannot be submitted if there was an error uploading a file, even after deleting it
* PHP Warning: count(): Parameter must be an array or an object that implements Countable

= 3.0.20 (11 October 2018) =

*Bugs:*

* Critical security flaw which could allow file extensions to be changed and files to be executed (Props [Frank Spierings from Warpnet](https://www.warpnet.nl/))
* Non UTF8 characters in the %filename% tag
* Embed mergetag variant not working if install was without Conditional Logic addon
* Animate CSS class clashing with other theme and plugin styles

= 3.0.19 (26 April 2018) =

*Changes:*

* Compatibility with form templates in Ninja Forms 3.2.23
* Use file extension whitelist to restrict the file upload select box

*Bugs:*

* upload_max_filesize php.ini config defined in units other than MB not working
* Dropbox connection removed if site cannot access Dropbox temporarily

= 3.0.18 (27 March 2018) =

*Changes:*

* Compatibility with Auto complete support in Ninja Forms 3.2.17

*Bugs:*

* Fatal error when Amazon Web Services plugin installed but not activated
* Timeout when adding new forms on certain installs

= 3.0.17 (18 November 2017) =

*Bugs:*

* Dropbox connection redirect 404ing on some installs
* Only the first 'Select Files' button text is used for multiple fields

*Changes:*

* Filter added for the cron time when deleting the temp file
* Filter added for the default 'Select Files' button text

= 3.0.16 (13 October 2017) =

*Bugs:*

* Fixed a bug that was preventing uploads from being re-saved when using the Save Progress add-on.

= 3.0.15 (1 Aug 2017) =

*Bugs:*

* Fix external URLs not sent to Zapier when using Zapier addon
* Fix field max upload size bigger than server limit

= 3.0.14 (11 Aug 2017) =

*Bugs:*

* Fix Dropbox file uploaded with full server path when having no custom upload path

= 3.0.13 (10 Aug 2017) =

*Features:*

* German translation file, thanks @christophrado!

*Changes:*

* Added original filename to `ninja_forms_uploads_[external service]_filename` filter
* Added more information to the help bubble for file renames to describe creating directories

*Bugs:*

* Fix isset weirdness when checking for old NF format of file value
* Fix method not exists when creating table if using older version of NF core.
* Fix custom upload path not replacing %field_ shortcodes
* Fix custom upload path not working for external (Dropbox/S3) file paths
* Fix %formtitle% not being replaced in field rename if no custom upload path

= 3.0.12 (28 July 2017) =

*Bugs:*

* Dropbox uploads failing when custom path defined

= 3.0.11 (25 July 2017) =

*Bugs:*

* File Uploads table not created on fresh installation
* jQuery File Upload JavaScript files clashed with Calendarize plugin

= 3.0.10 (12 July 2017) =

*Bugs:*

* File Uploads should work properly with non-standard WordPress databse prefixes.

= 3.0.9 (06 July 2017) =

*Bugs:*

* File Uploads should now work properly with Multi-Part Forms.
* Fixed a bug with non-English characters and file name encoding.

= 3.0.8 ( 26 June 2017) =

*Changes:*

* Supports Dropbox API v2
* Custom name of file now supports changing path with field specific data

*Bugs:*

* File appeared even if upload failed

= 3.0.7 ( 5 June 2017) =

*Bugs:*

* Amazon S3 uploads unable to use bucket in newer regions
* File not deleted from server when File Upload deleted in admin
* All File Uploads fields on form used the same nonce
* NF 2.9 submissions not displaying in admin
* File Upload field CSS too generic causing clashes with themes
* PHP notice on uploads table when submission from non-logged in user
* All Fields mergetag not using external URL
* Missing mergetag variations used for Post Creation
* Similar file extensions allowed even when not on file type whitelist for field

= 3.0.6 (12 April 2017) =

*Bugs:*

* Fixed the description text for custom file upload paths.
* Fixed PHP warnings related to uploading a file to a remote server.
* Links to uploaded files should now always show properly.
* Fixed a bug that could cause unexpected output when displaying a form.

= 3.0.5 (07 December 2016) =

*Bugs:*

* Fixed a bug that could cause file upload fields to fail with Ninja Forms versions > 3.0.17.

= 3.0.4 (3 November 2016) =

*Bugs:*

* Fixed a bug with the Max File Upload Size setting.
* Whitelisting file types should now work as explained in the help text.
* File names can now be based upon the values of other fields using merge tags.

*Changes:*

* Added missing help text to the admin.

= 3.0.3 (28 September 2016) =

*Bugs:*

* File Uploads should now show in Conditional Logic conditions.

= 3.0.2 (09 September 2016) =

* Update to 3.0.2

*Bugs:*

* Fixed SQL format that breaks dbdelta.
* Fixed Dropbox case sensitive issues.
* Fixed Multiple file selection bug.
* Fixed a bug with uploading .jpg files.

= 3.0.1 (06 September 2016) =

* Updated with Ninja Forms v3.x compatibility

= 3.0 (06 September 2016) =

* Updated with Ninja Forms v3.x compatibility
* Deprecated Ninja Forms v2.9.x compatible code

= 1.4.9 (09 May 2016) =

*Bugs:*

* Fixed a bug where duplicate file names could cause the extension to be changed. Credit to "fruh of citadelo" for reporting the security vulnerability.

= 1.4.8 (29 March 2016) =

*Bugs:*

* Fixed a bug with Dropbox that could cause uploading to Dropbox to fail.

= 1.4.7 (20 September 2015) =

*Bugs:*

* Fixed a bug related to buckets in Amazon S3.
* Improved how URLs are handled when saving submissions.

= 1.4.6 (24 August 2015) =

*Bugs:*

* Fixed an issue with connecting to Amazon accounts.
* Fixed several PHP notices that appeared on the uploads settings page.

= 1.4.5 (12 May 2015) =

*Bugs:*

* Featured images in the Post Creation extension should now function properly.
* Save Progress extension tables should now show File Upload fields properly.

= 1.4.4 (26 March 2015) =

*Bugs:*

* Multiple file uploads should work properly with external services.
* Fixed several PHP notices.

= 1.4.3 (12 January 2015) =

*Bugs:*

* Fixed a bug that could cause Dropbox to disconnect.
* Fixed a bug with multi-file uploads that could cause the wrong URL to be stored in the file uploads table.
* Fixed a PHP notice.

= 1.4.2 (9 December 2014) =

*Bugs:*

* Fixed a bug with PHP v5.6 and Dropbox uploads.
* Fixed a bug that caused file renaming to work incorrectly.

*Changes:*

* Added a new upload location of none, where files get removed after upload.

= 1.4.1 (17 November 2014) =

*Bugs*

* Fixed a bug caused by a bad commit in the previous version.

= 1.4 (17 November 2014) =

*Bugs:*

* Fixed two PHP notices.

*Changes:*

* Added filter for filename $file_name = apply_filters( 'nf_fu_filename' , $filename );
* The maximum file upload size can now not exceed the server PHP setting for max file uploads.

= 1.3.8 (15 September 2014 ) =

*Changes:*

* File Uploads should now be compatible with Ninja Forms version 2.8 and the new notification system.
* Performance should be noticeably increased.

= 1.3.7 (12 August 2014 ) =

*Bugs:*

* Fixed a bug with viewing files in the edit sub page.

= 1.3.6 (12 August 2014) =

*Bugs:*

* Fixing a bug with file exports and version 2.7+ of Ninja Forms.

* Fixed translation issues.

*Changes:*

* Added new .pot file.

= 1.3.5 (24 July 2014) =

*Changes:*

* Compatibility with Ninja Forms 2.7.

= 1.3.4 =

*Bugs:*

* Making sure the external upload doesn't fire if there is no file uploaded

= 1.3.3 =

*Bugs:*

* Fixed a bug with Dropbox that could cause file uploads to be sluggish.
* is_dir() and mkdir() warnings should be cleared up.
* Multi-file upload fields should now clear correctly when a form is submitted.

= 1.3.2 =

*Bugs:*

* Fixed a bug that could cause the plugin not to activate on some systems.

= 1.3.1 =

*Bugs:*

* The extension should now properly activate on all PHP versions.

= 1.3 =

*Features:*

* You can now store uploaded files in Dropbox or Amazon S3! Simply select the storage location on a per-upload-field basis.

*Bugs:*

* Fixed a PHP notice.
* Fixed a bug that could cause some installations to lose the ninja-forms/tmp/ directory.

= 1.2 =

*Bugs:*

* Fixed a bug that prevented required file uploads from being validated when using AJAX submissions.
* Fixed some php notices.

*Changes:*

* Added support for the new Ninja Forms loading class.
* Editing a submission from the wp-admin that includes a file will now show a link to that file instead of just the filename.

= 1.1 =

*Changes:*

* The format of date searching in the Browse Files tab will now be based upon the date format in Plugin Settings. i.e. you can now search dd/mm/yyyy.
* Added the option to name a directory/file with %userid%.

*Bugs:*

* Fixed a bug that caused file upload fields to load multiple instances or open with pre-filled, incorrect data.

= 1.0.11 =

*Changes:*

* Added a filter so that when a user uploads a file, they don't see the directory to which it was uploaded in their email.

= 1.0.10 =

*Changes:*

* Changed the license and auto-update system to the one available in Ninja Forms 2.2.47.

= 1.0.9 =

*Bugs:*

* Fixed a bug that could cause files to be added to the media library twice when used with the Post Creation extension.

= 1.0.8 =

*Changes:*

* Changed references to wpninjas.com to the new ninjaforms.com.

= 1.0.7 =

*Bugs:*

* Fixed a bug that prevented files from being emailed as attachments in multi-part forms.

= 1.0.6 =

*Changes:*

* Updates for compatibility with WordPress 3.6

= 1.0.5 =

*Bugs:*

* Fixed a bug that prevented Uploads from working properly with AJAX forms.
* Fixed a bug that prevented Uploads from working properly when they were set to required.

= 1.0.4 =

*Changes:*

* Added a filter so that File Uploads will work properly with the confirmation page option of Multi-Part Forms.

= 1.0.3 =

*Changes:*

* Changed the way that file uploads with duplicate names are handled. In previous versions, the new file would simply replace the older file with the same name; now, if a file already exists with the same name as an upload, the upload is renamed with a sequential number. e.g. my-file.jpg -> my-file-001.jpg -> my-file-002.jpg -> etc.

* Added an option to add files to the WordPress Media Library. On each file upload field, you'll find this new option.

* Added three new file renaming options: %displayname%, %firstname%, %lastname%. Each of these will be replaced with the appropriate user information.

* Added a new filter named: ninja_forms_uploads_dir. This filter can be used to modify the location Ninja Forms uploads files.

*Bugs:*

* Fixed a bug that could cause some files from uploading properly.

= 1.0.2 =

*Changes:*

* Added a new option to the [ninja_forms_field id=] shortcode. You can now use [ninja_forms_field id=2 method=url]. This will return just the url of the file. For example, you can now do something like this: <img src="[ninja_forms_field id=2 method=url]">.

= 1.0.1 =

*Changes:*

* Modified the way that the pre-processing is handled for more effeciency.

= 1.0 =

*Bugs:*

* Fixed a bug that prevented files from being replaced on the backend.

= 0.9 =

*Bugs:*

* Fixed a bug that prevented files from being replaced when editing user submissions.

= 0.8 =

*Features:*

* Added the ability to search for file uploads by User ID, User email, or User login.

= 0.7 =

* Updated code formatting.

= 0.6 =

* Fixed a bug that was causing the new [ninja_forms_field id=3] shortcode to fail when used in conjunction with the Uploads Extension.

= 0.5 =

* Changed the upload directory to the built-in WordPress uploads directory. This should help limit the cases of users not being able to upload files because of directory restrictions. Old files have not been moved over because it would be impossible to correctly fix links to new locations.
* Fixed a bug that was causing some users to lose their upload record when they deactivated and reactivated the plugin.
* Errors should now show properly for files that are over the file size limit set in the plugin settings.

= 0.4 =

* Various bug fixes including:
* A bug that prevented files from being moved to the proper directory.
* A bug that prevented the "update file" link from working on pages that already had a file uploaded.
* A bug that prevented the "featured image" functionality from working properly.

* Added a new setting to the upload settings page for file upload URLs.

= 0.3 =

* Various bug fixes.

= 0.2 =

* Various bug fixes.
* Changed the way that javascript and css files are loaded in extensions.