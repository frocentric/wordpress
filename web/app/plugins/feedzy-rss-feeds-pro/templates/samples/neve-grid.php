<?php
/**
 * This is a custom template of Feedzy RSS Feeds plugin
 * that can be used with the shortcode approach
 * https://docs.themeisle.com/article/1162-feedzy-custom-templates
 *
 * It is recommended to be used with Neve theme for a grid layout
 *
 * @package feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/templates/samples
 */
?>

<style>
	/* Add custom style */
	.feedzy-neve-grid-template a {
		text-decoration: none !important;
	}

	.feedzy-neve-grid-template a:hover {
		text-decoration: underline !important;
	}

	.feedzy-neve-grid-template .feedzy-rss ul {
		padding-left: 0;
		margin: 0;
	}
</style>

<div class="posts-wrapper row feedzy-neve-grid-template">

	<?php
	$index = 0;
	echo '<ul class="feedzy-rss">';
	foreach ( $feed_items as $item ) {
		$index ++;

		$post_template = '<li %1$s>
                <div class="content">
                        <div class="nv-post-thumbnail-wrap"><img src="%2$s" style="%3$s"></div>
                        <h2 class="blog-entry-title entry-title"><a href="%4$s">%5$s</a></h2>
                        <ul class="nv-meta-list"><li class="meta category">%6$s</li></ul>
                        <div class="excerpt-wrap entry-summary">%7$s</div>
                </div>
                </li>';

		$post_structure = sprintf(
			$post_template,
			$item['itemAttr'],
			$item['item_img_path'],
			$item['item_img_style'],
			feedzy_feed_item_link( $item ),
			feedzy_feed_item_title( $item ),
			feedzy_feed_item_meta( $item ),
			feedzy_feed_item_desc( $item )
		);


		echo wp_kses_post( $post_structure );

	}
	echo '</ul>';

	?>

</div>

