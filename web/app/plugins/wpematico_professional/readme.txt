=== WPeMatico Professional ===
Contributors: etruel
Tags: RSS, Post, Posts, Feed, Feeds, RSS to Post, Feed to Post, admin, aggregation, atom, autoblogging, bot, content, syndication, writing
Requires at least: 4.1
Tested up to: 5.4
Stable tag: 2.5

WPeMatico is for autoblogging, automatically creating posts from the RSS/Atom feeds you choose. PRO Version extends WPeMatico free plugin.

== Description ==

WPeMatico PRO adds following features to WPeMatico.

Support Custom taxonomies for Custom Post Types.
Just activate the Professional plugin and your campaign will show the metaboxes to select the custom taxonomies for the Custom Post Type selected.

Fix and correct wrong HTML on content.
You can enable this on txt config file when you get the content through Full content feature.
		
Delete last HTML tag option.
Many sites add own custom data on the last html tag. May be <p> or <div> or <span>, anyway, you can take off here.
		
Strip HTML from content.
You can strip all HTML tags from content and saves it as pure text.
		
Import the URL feed list into a campaign.
If you have a list of feed that you want to add into a campaign, you can import all of them with a few simple clicks pasting the list as txt format.
		
Automatic assign 'per feed' author name.
Automatic assigns author names based on source feed or custom typed author.
Skip the posts by keywords in the author name.
		
Words counters filters.
Strip the HTML and count how many words or letters are in content and allows assign a category, cut the content or skip the post.
		
Keywords filtering. Regular expressions supported.
You can determine if skip or publish the post for certain words in title or content.
		
Ramdom Rewrites
Replace words by synonyms ramdomly.

Custom title with/out counter.
PRO Version allow change the title of original post and also you can add a counter in the title name to don’t get duplicated titles.

Extra filters to check Duplicates with Custom titles.
PRO Version allow enable an extra query when fetching to check the titles before insert the new post in database to skip inserting the post if gets duplicated titles.

AUTO Tags Feature.
Generate tags automatically taken from content words. You can filter bad tags and how many tags do you want on every post. (Also you can see our Cats2Tags Add-on, getting 50% discount buying PRO version)
		
Custom Feed Tags
Allows insert the names of any feed tags to get their values and insert them into the posts as content or custom field.

Custom fields with dynamic values.
Feature Custom fields with dynamic values allow you to add as many fields as you want with the possibility of add values as word templates replaced on the fly when the campaign is running. This allow add custom fields values like permalink, images urls, etc.
		
Default Featured image.
You can set the URL of a default Featured image if not found image on content.
		
Pro Options for images.
Overwrite, rename or keep the duplicated images by names.
		
Filter images by width or height.
You can set the min o max width or height to set the Featured image. Also Filter and delete images in posts content just by width or height of every image.

Import and export single campaign.
This feature allow you to export and download a file from a single campaign, then later you can upload and import the campaign in another or same wordpress with WPeMatico professional version installed.

== Installation ==

1. Unzip "wpematico_pro" archive and put the folder into your plugins folder (/wp-content/plugins/).
1. Same version of WPeMatico FREE must be installed and activated.
1. Activate PRO version through the 'Plugins' menu in WordPress.

= Upgrading =

* You will be notify when there is a new version then you can automatically upgrade from wordpress plugins page.

== Changelog ==
= 2.5 Mar 31, 2020 =
* Added a new feature to parse and flip the paragraphs of each post content.
* Improves a new Meta-Box to Custom Content Parsers with striping phrases features.
* Fixes an error in Remove last HTML tag feature.

= 2.4 Oct 28, 2019 =
* Fixes an error in ramdon rewrite feature.

= 2.3 Oct 18, 2019 =
* Moved two advanced features options to the sidebar in settings.
* Fixes a bug in words to taxonomy feature.
* Fixes a Warning in licenses page.

= 2.2 Oct 02, 2019 =
* Added new feature for globally set Words to taxonomy in Settings.
* Fixes issue in word to taxonomy feature for some CPT.
* Changes Settings tabs behaviors to Tab with Sections inside.
* Improves security on saving forms.

= 2.1.6 Sep 17, 2019 =
* Added Custom Statuses for Custom Post Types on campaigns.
* Tweaks on the php code.
* Improved security on campaign edit nonce.

= 2.1.5 Apr 3, 2019 =
* Tweaks on the default image metabox on campaign editing.
* Improved compatibility with XML campaign type.
* Improved help tips in the metaboxes of the XML campaign.
* Fixes some PHP warnings and Notices In campaign logs.
* Removed redirect to core welcome screen on update.

= 2.1.4 Mar 1, 2019 =
* Fixes an issue when logging in a non-admin user. 
* Sometimes generating 'Headers Already Sent' PHP warning preventing non-admin users access to the backend.

= 2.1.3 Feb 20, 2019 =
* More fixes for enclosure uploads of malformed feeds to get file type from image headers.

= 2.1.2 Jan 24, 2019 =
* Fixes enclosure uploads for feeds with media tags without file type, but medium='image'.

= 2.1.1 Jan 16, 2019 =
* Added options to fetch post categories and tags from comma separated XML tags.
* Avoid errors when the Professional is activated, and deactivated the WPeMatico Core.

= 2.1 Jan 10, 2019 =
* Added compatibility with XML Campaign type.
* Added option to fetch post categories, tags, authors and format from XML feeds.

= 2.0 Aug 29, 2018 =
* Added the Feed Name field in Advanced option to each feed URL
* Added {feed_name} tag in Post template feature inside campaign.
* Added option to follow redirections of audio or video URLs.
* Added option to decode html entities before download audio and video URLs.
* Added option to display Pro Settings Page in WPeMatico menu.
* Many tweaks in the code 
* Fixes an issue on keyword filtering with Full Content.

= 1.9.4 May 17, 2018 =
* Fixes an issue in javascript in campaign editing screen

= 1.9.3 May 17, 2018 =
* Added the 'Delete till end' feature on custom title. 
* Added the 'Cut at' feature on custom title.
* Added the 'Word to Taxonomy' feature for Custom Post types.
* Added Cookies support for external websites that use them.
* Removed redirect to core welcome screen on update.

= 1.9.2 Mar 14, 2018 =
* Fixes check requirements issue that avoid some executions for external cron.

= 1.9.1 Mar 10, 2018 =
* Added option to set the input chrset encoding in the feed advanced options.
* Fixes an issue on Image Renamer feature.
* Fixes some PHP Notices on helps.
* Fixes an issue on campaign fetch.
* Fixes an issue on meets requirements when doing cron.

= 1.9.0 Jan 31, 2018 =
* Added HTTP Cookies feature to allow fetch special websites and some cache systems.
* Tweaks improves performance when is uploading a default image.
* Tweaks on image rename of campaign on check and uncheck another options.
* Tweaks on audio and video options when is fetching.

= 1.8.3 Dec 4, 2017 =
* Fixes the popup of default featured image if not found image on content.
* Fixes an issue on uploading default featured image from URL.
* Fixes an issue on custom fields feature with empty featured image.

= 1.8.2 Nov 21, 2017 =
* Added an option to change the user-agent used to read the feeds.
* Added support for the new google news feeds formats.
* PRO Images and the Core Images Options metaboxes were mixed in one metabox.
* Tweak, removed useless 'Enable rename images' on PRO settings, the option is shown in campaign edit.
* Fixes to work more than once 'Delete phrases till the end of the line' feature.
* Fixes to use "Force Feed" and "user-agent" options in Test Feed and check fields on save campaign.
* Many code improvements.

= 1.8.1 Oct 31, 2017 = 
* Added new feature to skip the posts by author name.
* Improves the Custom Feed Tags feature. 
* Added support to use parents->childrens Tags inside each feed item.
* Added support to get the attributes of the tags in custom feeds.

= 1.8 Oct 20, 2017 =
* Added name spaces for feeds with Custom Tags to be used in the Campaign Template and Custom fields.
* Added a fixed icon for Feed Advanced Options in every feed row.
* Added an option to Force Feed when Simplepie gives error. Find it in the Feed Advanced Options Popup.
* Fixes the numeric name in the file for an exported campaign.
* Fixes the malformed JSON in the content sometimes when export campaigns.
* Added a list to include campaigns in the debug file to submit support tickets.

= 1.7.4 Aug 25, 2017 =
* Added new feature Custom Feed Tags.
* Added new feature to assign Parent page for campaigns inserting feed items as pages.
* Added new feature to strip images with incorrect content.
* Added new filters to allow the user manage the HTML tags stripped by a campaign.

= 1.7.3 =
* New feature to avoid upload the Default image again and again.
* Fixes a problem in campaigns import.
* Added plugin version validation to update the campaigns if required.

= 1.7.2 =
* Added a new option to delete phrases till the end of the line instead to end of content.
* Some tweaks in the new video/audio metaboxes.
* Fixes an error in partial_curl for some PHP versions.

= 1.7.1 =
* Added support to upload big file sizes by ranges for the new video/audio features.
* Fixes a bug for enclosures for mime types image/jpg.

= 1.7 =
* Added support for audio and video file types allowed by WP (<mp3, ogg, wav, wma, m4a> <mp4, m4v, mov, wmv, avi, mpg, ogv, 3gp, 3g2> )
* Added the feature to get the audio and video files from the feed enclosures and podcasts.
* Added some features for audio and video files:
  Strip the queries variables in the URLs of audio and video links.
  Audio and video filenames renamer.
  Strip audios and videos html tags from the content.
* Fixes the PHP notices on images filters in the campaign editing screen.

= 1.6.3 =
* Fixes issue on enclosures text.
* Fixes the enclosures images logic to "Only if no images on content.".
* Added support for '<image>URL</image>' in feed.

= 1.6.2 =
* Fixes some issues with Default Featured image to work after other addons.
* Tweak: Added filtering unformatted images to strip from contents.

= 1.6.1 =
* Fixed some issues with custom fields tag vars.

= 1.6 =
* New Feature: Added Ramdom Rewrites. A Big SEO improvement.
* Tweak: New option to add rel="nofollow" to links.
* Tweak: New option to use tags from feeds with <tag> if exists in the items.
* Tweak: Added bulk campaign import/export feature on bulk actions above the campaigns list.
* Tweak: Strip All HTML Tags, Strip Links and rel="nofollow" to links. 
* Cleans all the help strings in the campaign screen and added all tips to the Wordpress standard Help tab.
* Tweak: The old plugin updater was removed.
* Fixes the format for the first three campaigns in the debug file.
* Tweak: Added new license status on debug file.

= 1.5.2 =
* Fixes an issue in featured images.

= 1.5.1 =
* Fixes an issue with <author> tag to work also with emails as author names.
* Updated Updater class to 1.6.10.

= 1.5 =
* Added new feature for multipaged Feeds like https://etruel.com/feed/?paged=2.
* Added popup to add advanced options for each feed.
* Added new feature to allow strip text from a phrase till the end of the content.
* Added the option to get the author from the feed item <author> tag. If not exist, the user is created as author.
* Tweaks on "Discard the Post if NO Images in Content" to check also Featured images and added a filter to execute after Thumbnail Scratcher addon.
* Fixes a bug on showing the Image Renamer div area when activate it.
* Added Own debug info to the WPeMatico Debug Info file.
* Added 3 Campaign data to the WPeMatico Debug Info file.
* Some tweaks on texts.

= 1.4.1 =
* Fixes a bug in the keyword filter logic.
* Fixes the ajax in licenses page.
* Updated Updater class.

= 1.4 =
* Improves the Pro options for Images Metabox.
* Improves some filters to make featured the RSS images.
* Added an option to try to handle cases where images are delivered through a script and the correct file extension isn't available.
* Fixes the Image rename feature when the image extension is missed, by adding '.jpg'
* Fixes by adding the Featured Image as empty string to the post content when there is not a featured image.
* Improvements on Custom function for uploads.
* New feature to overwrite, rename or keep the duplicated images by names.

= 1.3.8.1 =
* Improves some filters to skip posts.
* Fixes an error on menus with custom taxonomies.
* Fixes an issue in the custom title counter.
* Fixes the permalink tag used in custom fields.
* Fixes a warning that break the screen when imports a campaign.
* Fixes the license key activation in some cases that failed.

= 1.3.8 =
* Adds a new feature: Rename the images uploaded to the website.
* Adds a new feature: Keyword Filter for source item categories.
* Many improvements on the Keywords filters when it's fetching sources.
* Some tweaks on Pro Options For images.
* Fixes [STRICT NOTICE] non-static method discardifnoimage called statically.
* Fixes PHP notice on exporting a campaign.

= 1.3.7.1 =
* Fixes the timeout error calling a function with cron in auto mode.

= 1.3.7 =
* New improved function to add the custom featured image.
* Improves the behaviour on cutting text with featured images.
* Fixes double display of categories in Quick edit actions.
* Fixes adding the featured images to custom fields.
* Updated Plugin Updater class.

= 1.3.6 =
* Fixes an error that avoid activate the plugin in some singular cases.

= 1.3.5 =
* Fixes to don't show Export campaign quick action in Trash.
* Fixes a function name to check duplicated custom titles.
* Uses Danger Zone Options to delete the Professional options when uninstall

= 1.3.4 =
* Fixes a debug notice with enclosure images.
* Some cosmetic tweaks on Custom Fields metabox.

= 1.3.3 =
* Added a feature to Keywords Filters to take one or all words.
* Added compatibility for free 1.3.3 version.
* Colored meta boxes titles on campaign editing.
* Fixes - when saving KeyWord filters for bad strip / slashes.
* Some cosmetic tweaks to Keywords filters Metabox.
* Some other tweaks and improvements.

= other versions =
* See on Changelog
