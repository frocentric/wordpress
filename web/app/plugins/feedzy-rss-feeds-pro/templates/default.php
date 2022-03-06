<?php
/**
 * Default Template File for FEEDZY RSS Feeds
 *
 * @package feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/templates
 */
?>
<div class="<?php echo esc_attr( feedzy_feed_class() ); ?>">
	<?php if ( $feed_title['use_title'] ) { ?>
		<div class="rss_header">
			<h2>
				<a href="<?php echo esc_url( feedzy_feed_link() ); ?>" class="<?php echo esc_attr( $feed_title['rss_title_class'] ); ?>" rel="noopener">
					<?php echo wp_kses_post( feedzy_feed_title() ); ?>
				</a>
				<span class="<?php echo esc_attr( $feed_title['rss_description_class'] ); ?>">
				<?php echo wp_kses_post( feedzy_feed_desc() ); ?>
			</span>
			</h2>
		</div>
	<?php } ?>
	<ul class="feedzy-default">
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
					<span class="title">
						<a href="<?php echo esc_url( feedzy_feed_item_link( $item ) ); ?>" target="<?php echo esc_attr( $item['item_url_target'] ); ?>"  rel="noopener <?php echo isset( $item['item_url_follow'] ) ? esc_attr( $item['item_url_follow'] ) : ''; ?>">
							<?php echo wp_kses_post( feedzy_feed_item_title( $item ) ); ?>
						</a>
					</span>
					<div class="<?php echo esc_attr( $item['item_content_class'] ); ?>"
						style="<?php echo esc_attr( $item['item_content_style'] ); ?>">

						<?php
						if ( ! empty( $item['item_meta'] ) ) {
							?>
							<small>
								<?php echo wp_kses_post( feedzy_feed_item_meta( $item ) ); ?>
							</small>
						<?php } ?>

						<?php
						if ( ! empty( $item['item_description'] ) ) {
							?>
							<p><?php echo wp_kses_post( feedzy_feed_item_desc( $item ) ); ?></p>
						<?php } ?>

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
					</div>
			</li>
			<?php
		}// End foreach.
		?>
	</ul>
</div>
