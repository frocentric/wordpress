=== WPeMatico Full Content ===
Contributors: etruel, sniuk
Tags: autoblog, automatic, full, content, rss, feed, read, matic
Requires at least: 4.1
Tested up to: 5.3
Stable tag: 2.0

Description: Add On for WPeMatico plugin. Add Full Content Parser and editor of config files to get full content from almost all sites. 

== Description ==

WPeMatico is for autoblogging, automatically creating posts from the RSS/Atom feeds you choose, which are organized into campaigns. 

This add-on takes the title and permalink of every feed item and scratches its web page to find the full content.  
Works automatically with almost all sites, but if can’t find the correct content by itself or just gets blank content, you can add a config file for every custom website or domain. Also if you don’t have the time to do this, you can buy our service and we can make it for you.

Fix and correct wrong HTML on content
You can enable this in the txt config file, when you are getting the content through Full content feature. It’s using ‘Tidy’ to fix the incorrect HTML code.

Config Files Editor
Lets you specify exactly what is the full contents of every site of origin.
Then allows you to change the configuration files to the uploads folder to keep your files and changes when you upgrade the plugin. WPeMatico will see this folder automatically after it was created and will begin to use these configuration files from your preferred websites for fetching content.

But this is not the best! Also adds a file editor that allows testing of new sites on the fly.
You will see how the plugin will fetch the remote site content in your posts while you are editing the config file, then you could get the content from websites that the plugin can't recognize the article content for itself.
Just click on Preview button and will get the content below in Visual mode (As your post will be seen) or in Text mode (as HTML code).

Also have the Inline Help as Wordpress style with some tips and all the commands you will need to edit the config files. See screenshots.

NOTE: This plugin requires WPeMatico Free Version installed and activated.


== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

1. Unzip plugin archive and put the folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin from the Plugins menu.

== Frequently Asked Questions ==

= Can I use this plug-in without WPeMatico plugin? =

No. This plugin requires WPeMatico Free and PRO Versions installed and activated.

== Screenshots ==

1. Editor tab with listed files.  You can edit and add new config files for domains you want to get content...
2. Also have the Inline Help as Wordpress style with some tips and all the commands you will need to edit the config files.

== Changelog ==
= 2.0 Feb 13, 2020 =
* Improves compatibility with WPeMatico core 2.5+
* Improves design, styles and responsive in Settings screen.
* Some tweaks on javascript files.
* Fixes load config files from file list on settings.
* Fixes Warning on Helps tabs in settings screen.
* Fixes strip starting and ending spaces on config files names.

= 1.8 Aug 21, 2019 =
* Fixes automatic updater from inside WordPress.
* Many improvements in the JavaScript, CSS and PHP code.

= 1.7.6 Jul 27, 2019 =
* Fixes few html y CSS issues on Settings page.

= 1.7.5 Mar 7, 2019 =
* Fixes an issue when the source does not have an author.

= 1.7.4 Aug 29, 2018 =
* Many improvements in the code
* Better compatibility with Professional addon on keywords filters.
* Fixed an issue on notices of requirements.
* Fixes an issue getting the author from full content.

= 1.7.3 =
* Added validation to campaign type 'Youtube Fetcher'.

= 1.7.2 Nov 21, 2017 =
* Fixes an issue introduced in 1.7 with the meta tags for template vars and custom fields.

= 1.7.1 =
* Added support for Facebook embeds images and videos.

= 1.7 Oct 20, 2017 =
* Added the meta data tab and the possibility of being used in the campaign template.
* Added the functions and filters to get the HTML in the JavaScript sites. (A future feature)
* Improved the structure of the code in the main file.
* Fixes the new version notify and the updater in the Extensions page.
* Fixes an issue with test_url: in the siteconfig and the preview.
* Updated help tabs in the new functionalities of the editor.

= 1.6.2 Aug 25, 2017 =
* Updated lot of standard config files to get automatically the content of the websites.
* Fixes an issue with HTML5 lib that brokes some extractions.
* Fixes an issue with JQuery sometimes not loaded in the class extractor.
* Fixes an issue to new config files for domains with a blank in its value.

= 1.6.1 Aug 13, 2017 =
* Fixes a Fatal error on PHP prior to 5.5. Can't use function return value in write context

= 1.6 =
* Redesign and lot of improvements in the Config Files Editor screen. Settings, Full Content tab.
* Adds a class Inspector to get the xPATH for the correct content in each domain.
* Adds the feature to insert a new blank txt config file from scratch.

= 1.5.4 =
* Fixed issue with custom folder for config files.
* *ATTENTION* Files deletion risk: if you are using custom config files, MAKE A BACKUP OF THOSE FILES BEFORE UPDATE THE PLUGIN.
* Fixed issue on updater.
* Fixed issue about featured images with WPeMatico Professional Add-on.

= 1.5.3 =
* Fixed some issues with authors names for non ascii standards languages.

= 1.5.2 =
* Tweaks the way to get videos.

= 1.5.1 =
* Fixes an issue with featured images.
* Updated Updater class to 1.6.10.

= 1.5 =
* Added the option to add the og:image to the beginning of the content.
* Added the option to ignore the og:image if already are images in the content.
* Fixes the overwrite of the pro featured image with an empty og:image.
* Tweaks on a file that was took as a false positive virus.
* Improvements on the autodetect of charset encoding for titles and content.

= 1.4.2 =
* Added an autodetect of charset encoding for wrong coded websites or without meta tags.

= 1.4.1 =
* Added a converter of the Full Content to UTF-8. This affects only a minor group of websites.

= 1.4 =
* Added Wordpress filter to allow add video websites that are currently filtered.
* Fixes a bug with full content for test URL field and make me feed addon.
* Fixes to work with the Professional Addon feature of Keyword Filters that didn't work with full contents.
* Fixes and updates The license key form to work with the new core system for licenses.

= 1.3.9 =
* Added priority of OG:image over the RSS image in Professional Addon.

= 1.3.8 =
* Improvement: Now you can get the opengraph image without get the Full Content.
* Added Feature to test a single URL to see what content gets from there.
* Tweaks to get the contents when source permalinks redirects to the real source sites.
* Some new tips and instructions in Help Tab in Full Content settings.
* Improves performance.

= 1.3.7.2 =
* Added control if Readabilty class exists to avoid errors with third parties plugins.
* Fixes http to https of site plugin

= 1.3.7.1 =
* Added a feature to get the featured image from open graph or twitter image from source code.
* Fixes few filters that overwrite some campaign options.
* Fixes the bug that gets the full content only in manual mode.

= 1.3.7 =
* Change the order of the filter to get the full content.
* Updated Plugin Updater class.

= 1.3.6 =
* Fixes an error that avoids activate the plugin in some singular cases.

= 1.3.5 =
* Added feature to read complete content for multi-page articles.
* Added feature to get the title also from source web page instead of feed.
* Added feature to get the date of the post from the source web page instead of feed.
* Added feature to get the author also from source web page and optionally create it if not exist.
* Added if gets empty full content then takes the original feed item content.
* Updated Commands Reference in Help with examples.
* Added around of 1000 config files for predefined websites.
* Fixes and improved license key data and fields.
* Fixes some strings in the plugin metabox and settings.
* Fixes on checking prerequisites needed to works.
* Fixes the html id of campaign_usecurl field on metabox.
* Many, many code improvements.
* Updated plugin image.

= 1.3.4 =
* Updated content extractors to last versions.
* Better support for videos.
* Added support to get iframes, objects and embeds with videos.
* Added some wordpress filters to allow add more permitted video sites, besides html tags to check by code.

= 1.3.1 =
* Fixes on preview file in edition section.
* Added support for don't strip iframes with videos of youtube and also youtu.be

= 1.3 =
* Fusion of Full Content and TXT Config Files editor Plugins in this Add-on

= 1.0 =
* initial release

