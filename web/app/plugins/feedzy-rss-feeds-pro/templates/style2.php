<?php
/**
 * Style 1 Template File for FEEDZY RSS Feeds
 * Styles work if Feed title is set to 'yes' when using this template
 * Another way is to write the styles in your theme style.css and
 * target the classe/id 's you add here
 *
 * @package feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/templates
 */
?>
<div class="<?php echo esc_attr( feedzy_feed_class() ); ?>">
	<?php if ( $feed_title['use_title'] ) { ?>
		<h2>
			<a href="<?php echo esc_url( feedzy_feed_link() ); ?>" class="<?php echo esc_attr( $feed_title['rss_title_class'] ); ?>" rel="noopener">
				<?php echo wp_kses_post( feedzy_feed_title() ); ?>
			</a>
			<span class="<?php echo esc_attr( $feed_title['rss_description_class'] ); ?>">
				<?php echo wp_kses_post( feedzy_feed_desc() ); ?>
			</span>
		</h2>
	<?php } ?>
	<ul class="feedzy-style2">
		<?php foreach ( $feed_items as $item ) { ?>
			<li <?php echo wp_kses_post( $item['itemAttr'] ); ?> >
				<?php if ( ! empty( $item['item_img'] ) && 'no' !== $sc['thumb'] ) { ?>
					<div class="<?php echo esc_attr( $item['item_img_class'] ); ?>" style="<?php echo esc_attr( $item['item_img_style'] ); ?>">
						<a href="<?php echo esc_url( feedzy_feed_item_link( $item ) ); ?>" target="<?php echo esc_attr( $item['item_url_target'] ); ?>" 
							rel="noopener <?php echo isset( $item['item_url_follow'] ) ? esc_attr( $item['item_url_follow'] ) : ''; ?>"
							title="<?php echo esc_attr( $item['item_url_title'] ); ?>"
							style="<?php echo esc_attr( $item['item_img_style'] ); ?>">
							<?php
							$allowed_html = array(
								'span' => array(
									'class' => true,
									'title' => true,
									'style' => true,
								),
								'amp-img' => array(
									'alt' => true,
									'src' => true,
									'width' => true,
									'height' => true,
									'layout' => true,
								),
							);
							add_filter( 'safecss_filter_attr_allow_css', '__return_true' );
							echo wp_kses( feedzy_feed_item_image( $item ), $allowed_html );
							remove_filter( 'safecss_filter_attr_allow_css', '__return_true' );
							?>
						</a>
					</div>
				<?php } ?>
				<div class="rss_content_wrap">
					<span class="title">
						<a href="<?php echo esc_url( feedzy_feed_item_link( $item ) ); ?>" target="<?php echo esc_attr( $item['item_url_target'] ); ?>"  rel="noopener <?php echo isset( $item['item_url_follow'] ) ? esc_attr( $item['item_url_follow'] ) : ''; ?>">
							<?php echo wp_kses_post( feedzy_feed_item_title( $item ) ); ?>
						</a>
					</span>
					<div class="<?php echo esc_attr( $item['item_content_class'] ); ?>"
							style="<?php echo esc_attr( $item['item_content_style'] ); ?>">
						<?php if ( ! empty( $item['item_meta'] ) ) : ?>
							<small class="meta"><?php echo wp_kses_post( feedzy_feed_item_meta( $item ) ); ?></small>
						<?php endif; ?>

						<?php if ( ! empty( $item['item_description'] ) ) : ?>
						<p class="description"><?php echo wp_kses_post( feedzy_feed_item_desc( $item ) ); ?></p>
						<?php endif; ?>

						<?php
							$allow_audio_tag = array(
								'audio' => array(
									'controlslist' => array(),
									'controls' => array(),
								),
								'source' => array(
									'type' => array(),
									'src' => array(),
								),
							);
							echo wp_kses( feedzy_feed_item_media( $item ), $allow_audio_tag );
						?>
						<?php if ( ! empty( $item['item_price'] ) && 'no' !== $sc['price'] ) { ?>
							<div class="price-wrap">
								<a href="<?php echo esc_url( feedzy_feed_item_link( $item ) ); ?>" target="_blank" rel="noopener"><button class="price"> <?php echo wp_kses_post( feedzy_feed_item_price( $item ) ); ?></button></a>
							</div>
						<?php } ?>
					</div>
				</div>
			</li>
			<?php
		}// End foreach.
		?>
	</ul>
</div>
