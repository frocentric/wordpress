<?php
/**
 * Sample custom template file for FEEDZY RSS Feeds for showcasing the functions that can be used.
 *
 * Create your own template instead of changing <br>This file.
 *
 * @package feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/templates/samples
 */
?>

<style>
	/* Add custom style */
</style>

<?php

// Show the class of the feedzy block
echo wp_kses_post( '<br>These are the classes of the feedzy block: ' . feedzy_feed_class() );

// Show the title of the feed as parsed by the shortcode.
echo wp_kses_post( '<br>This is the title of the feed: ' . feedzy_feed_title() );

// Show the url of the feed as parsed by the shortcode.
echo wp_kses_post( '<br>This is url title of the feed: ' . feedzy_feed_link() );

// Show the description of the feed as parsed by the shortcode.
echo wp_kses_post( '<br>This is the description of the feed: ' . feedzy_feed_desc() );

// Iterate over the items.
$index = 0;
foreach ( $feed_items as $item ) {
	$index++;
	// Show the item title as parsed by the shortcode.
	echo wp_kses_post( "<br>This is the title of item#{$index}: " . feedzy_feed_item_title( $item ) );

	// Show the item url as parsed by the shortcode.
	echo wp_kses_post( "<br>This is the url of item#{$index}: " . feedzy_feed_item_link( $item ) );

	// Show the item image as parsed by the shortcode.
	echo wp_kses_post( "<br>This is the image of item#{$index}: " . feedzy_feed_item_image( $item ) );

	// Show the item description as parsed by the shortcode.
	echo wp_kses_post( "<br>This is the description of item#{$index}: " . feedzy_feed_item_desc( $item ) );

	// Show the item meta as parsed by the shortcode.
	echo wp_kses_post( "<br>This is the meta of item#{$index}: " . feedzy_feed_item_meta( $item ) );

	// Show the item media element.
	echo wp_kses_post( "<br>This is the media element of item#{$index}: " . feedzy_feed_item_media( $item, true ) );

	// Show the item price as parsed by the shortcode.
	echo wp_kses_post( "<br>This is the price of item#{$index}: " . feedzy_feed_item_price( $item ) );
}
