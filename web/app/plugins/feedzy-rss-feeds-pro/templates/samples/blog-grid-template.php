<?php
/**
 * This is a custom template of Feedzy RSS Feeds plugin
 * that can be used with the shortcode approach
 * https://docs.themeisle.com/article/1162-feedzy-custom-templates
 *
 * It can be used with any WordPress theme, for a grid layout
 *
 * @package feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/templates/samples
 */
?>

<style>
	/* Add custom style */
	@media (min-width: 481px) and (max-width: 767px) {
		.feedzy-blog-grid-template .feedzy-rss {
			display: flex;
		}
	}
	@media (max-width: 767px) {
		.feedzy-blog-grid-template .feedzy-rss {
			display: block;
		}
		.feedzy-blog-grid-template .feedzy-rss li {
			text-align: center;
		}
	}
</style>

<div class="feedzy-blog-grid-template">

	<?php
	$index = 0;
	echo '<ul class="feedzy-rss">';
	foreach ( $feed_items as $item ) {
		$index ++;

		$post_template = '<li %1$s>
                <div class="content">
                        <div><img src="%2$s" style="%3$s"></div>
                        <h2><a href="%4$s">%5$s</a></h2>
                        <span>%6$s</span>
                        <div>%7$s</div>
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

