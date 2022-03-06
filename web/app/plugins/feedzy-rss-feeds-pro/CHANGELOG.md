##### [Version 1.7.5](https://github.com/Codeinwp/feedzy-rss-feeds-pro/compare/v1.7.4...v1.7.5) (2021-07-14)

* Fix WordAI rewrite content issue

##### [Version 1.7.4](https://github.com/Codeinwp/feedzy-rss-feeds-pro/compare/v1.7.3...v1.7.4) (2021-07-07)

- Fix WordAI compatibility with the new API
- Fix display feed item images issue in different feed template

##### [Version 1.7.3](https://github.com/Codeinwp/feedzy-rss-feeds-pro/compare/v1.7.2...v1.7.3) (2021-05-12)

* fix audio tag parsing
* fix display feed item images issues
* fix retrieve item custom tag value

##### [Version 1.7.2](https://github.com/Codeinwp/feedzy-rss-feeds-pro/compare/v1.7.1...v1.7.2) (2021-04-28)

* Fix save credentials issue for WordAI and SpinnerChief

##### [Version 1.7.1](https://github.com/Codeinwp/feedzy-rss-feeds-pro/compare/v1.7.0...v1.7.1) (2021-04-21)

* Fix broken language dropdown issue

#### [Version 1.7.0](https://github.com/Codeinwp/feedzy-rss-feeds-pro/compare/v1.6.14...v1.7.0) (2021-04-20)

### Fixes
* Fix randomness in custom tags retrieval
* Improve compatibility when Lite is not active and Pro version is
* Improve compatibility with latest PHP and WordPress versions

### v1.6.14 - 2020-12-25 
 **Changes:** 
 * [Feat] Support for using custom tags in the featured image field of the feed to post import
* [Fix] Exclude/include feed items based on keywords from the content
* [Fix] Incorrect import of custom tags, when keywords filters are used for feed to post import
 
 ### v1.6.13 - 2020-07-23 
 **Changes:** 
 * [Feat] Feed2Post - Add support for non-English text while extracting full content
* [Feat] Feed2Post - Update REST endpoint for full content import
* [Fix] Feed2Post - Missing Last Run Status option
 
 ### v1.6.12 - 2020-05-28 
 **Changes:** 
 * [Fix] TGMPA conflict with the Newspaper theme
* [Fix] Check Spinner Chief/WordAI account status before spinning
* [Fix] Error in parsing custom tags
* [Fix] Custom tags in ATOM feeds
 
 ### v1.6.11 - 2020-01-30 
 **Changes:** 
 * If feeds don't have images, inform the user instead of failing silently
* Add the ability to show the post author for imported posts both in frontend and backend
* Fix: Uncategorized is automatically added to all imported posts
* Fix: Additional Class(es) in Gutenberg were not used
* Fix: Youtube feed always shows summary even if summary is off in the shortcode
* Fix: Typo in tags available for use in the import to post feature
 
 ### v1.6.9 - 2019-12-13 
 **Changes:** 
 * Fix number of feed items to be imported defaults back to 10
* Fix widget to use all settings configured
* Add support for custom template tags
* Allow support for multiple taxonomies
 
 ### v1.6.8 - 2019-09-04 
 **Changes:** 
 * Add support to show featured image from a dynamic URL
* Full content to process only as many items as are being imported
* Add support to extract categories from feed
 
 ### v1.6.7 - 2019-07-24 
 **Changes:** 
 * Fix issue with delete posts created after X days deleting wrong data
 
 ### v1.6.6 - 2019-07-19 
 **Changes:** 
 * Show spinner while importing posts
* Fix issue with parsing custom tags
* Fix issue with custom fields getting wiped out when changing status of import
 
 ### v1.6.5 - 2019-05-30 
 **Changes:** 
 * fix: Posts not being assigned to taxonomy term when taxonomy name contains underscore
* feat: Auto delete posts after a specified time period
* feat: Referral URL now supports prefixes
 
 ### v1.6.4 - 2019-04-05 
 **Changes:** 
 * Add ability to spin full text content
* Extend custom magic tags to populate custom fields
 
 ### v1.6.3 - 2019-02-05 
 **Changes:** 
 * Fix issues with post title created by WordAI and SpinnerChief
* Fix issues with timeout while using WordAI and SpinnerChief
 
 ### v1.6.2 - 2019-01-31 
 **Changes:** 
 * Add MP3 as a supported format for
* Outgoing links should have rel=noopener
* Fix issue with SpinnerChief not spinning content
 
 ### v1.6.1 - 2018-12-22 
 **Changes:** 
 * Add support for extracting elements from custom feed tags
* Fix issue with Save button missing in Miscellaneous tab settings
* Fix issue with WordAI authentication
* Add Run Now button
* Add option to allow all items to be imported from the feed
 
 ### v1.6.0 - 2018-11-22 
 **Changes:** 
 * Add support for SpinnerChief
* Import Posts enabled for plan 1 users
* Fixed issues with posts spun with WordAI
* Number of posts imported can now be specified
* Fixed conflict with Avada
 
 ### v1.5.7 - 2018-06-26 
 **Changes:** 
 * Fixed problem with the price show/hide option not working with the custom templates
* Add error handling and support for the full content option
* Fixed Custom templates not working when a child theme is used
 
 ### v1.5.6 - 2018-05-14 
 **Changes:** 
 * New option for the Feed To Post feature - Get the full content from the feed item
* Updated the WordAI integration with logging and correct replacement of [#content_wordai]
 
 ### v1.5.5 - 2018-04-02 
 **Changes:** 
 * Remove redundant gr text on style2 template.
* Adds item description to magic tags.
* Adds compatibility with eBay feeds.
 
 ### v1.5.4 - 2018-03-07 
 **Changes:** 
 * Automatically adds canonical URLs for imported posts.
* Integrates canonical URL with the most popular SEO plugins.
 
 ### v1.5.3 - 2018-02-20 
 **Changes:** 
 * Improves Feed To Post feature.
 
 ### v1.5.2 - 2018-01-10 
 **Changes:** 
 * Fix older imports default status.
  
 ### v1.5.1 - 2018-01-05 
 **Changes:** 
 * Adds post status selector for importing schedule. 
* Adds hook after importing action.
* Adds full content import, if exists.
 
 ### v1.5.0 - 2017-11-03 
 **Changes:** 
 * Improvements to Feed to Post mechanism. 
* Changes to shop templates.
 
 ### v1.4.1 - 2017-08-17 
 **Changes:** 
 * Fixed assets loading.
* Improved features loading.
 
 ### v1.4.0 - 2017-08-17 
 **Changes:** 
 * Added content aware templates ( render audio markup for audio RSS feeds ) 
* Added integration with WordAI
* Fixed feed categories limit bug.
 
 ### v1.3.1 - 2017-07-17 
 **Changes:** 
 * FIX: ( Fixed links not displaying correctly if imported in content )
 
 ### v1.3.0 - 2017-06-21 
 **Changes:** 
 * Fixed category map for imported posts.
* Added item_link magic tag.
* Added next import ETA.
 
 ### v1.2.4 - 2017-05-31 
 **Changes:** 
 - Fixed notices in admin notifications.
- Added composer lock to repo.
 
 ### v1.2.3 - 2017-05-30 
 **Changes:** 
  
 ### v1.2.2 - 2017-05-29 
 **Changes:** 
  
 ### v1.2.1 - 2017-05-18 
 **Changes:** 
 - Fixed license filter.
 
 ### v1.2.0 - 2017-05-17 
 **Changes:** 
 - Added feed to post feature.
- Improved CI process.
 

### 1.1.2 - 03/02/2017

**Changes:** 

- Fixed fatal error when lite is not installed.



### 1.1.1 - 11/01/2017

**Changes:** 

- Fixed free file inclusion


### 1.1.0 - 10/01/2017

**Changes:** 

- Added tgm for free plugin

- Added keywords blacklist option


### 1.0.1 - 06/01/2017

**Changes:** 

- Added links for price label

- Changed textdomain to match the lite


### 1.0.0 - 03/01/2017

**Changes:** 

- Release 1.0.0
