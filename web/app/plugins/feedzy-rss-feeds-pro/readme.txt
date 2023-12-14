=== FEEDZY RSS Feeds ===
Contributors: themeisle,codeinwp
Tags: RSS, SimplePie, shortcode, feed, thumbnail, image, rss feeds, aggregator, tinyMCE, WYSIWYG, MCE, UI, flux, plugin, WordPress, widget, importer, XML, ATOM, API, parser
Requires at least: 6.0
Requires PHP: 7.2 and above
Tested up to: latest
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
PRO version for Feedzy RSS Feeds FREE


== Features ==
1. Support for referral parameters on RSS feed links
2. Support for custom templates for more info check Documentation
3. Support for product feed prices, woocomerce (google/facebook product feed) or any feed with the tag `<price />`


== Documentation ==

==== 1. Referral parameters ====
You can add your referral parameters inside `[feedzy-rss ... referral_url="" ]` parameter.
Take note that you must include them w/o the '?' sign, it is added automatically if needed.

E.g: `[feedzy-rss ... referral_url="google_track_id=0123456" ]`

==== 2. Custom Templates ====
Custom templates can be added in the `templates` folder inside the plugin folder
or `feedzy_templates` inside the theme folder.

The templates currently definded are: `default.php` and `example.php` you can override
them with your own to use inside the shortcode ex: `[feedzy-rss ... template="example" ]`.

An example can be found inside the template folder as well as the default template.

The template files get passed two variables: `$feed_title` and `$feed_items`.
The `$feed_items` contains an array with all the items data sanitized.

The user can add his own templates in his theme folder inside a folder called `feedzy_templates`
and then replace the `[feedzy-rss ... template="" ]` parameter.

==== 3. Product feed prices ====

If `[feedzy-rss ... price="yes" ]` and the feed is a product feed with a `<price />` tag, you can
access the price via the `$feed_items` array.

E.g:
```
foreach ( $feed_items as $item ) {
    ...
    echo $item['item_price'];
    ...
}
```

