<?php
/**
 * Template for displaying WP upcoming events widget style 1
 *
 */
?>
<div class="wpea_widget_style1 wpea_widget" >
	<div class="event_details" style="height: auto;">
		<?php if( has_post_thumbnail() && $is_display_image ){
			$picture_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
			?>
			<div class="event_picture">
				<a href="<?php echo get_permalink(); ?>" <?php if( $is_new_window ){ echo 'target="_blank"'; } ?> >
				<img src="<?php echo $picture_url;?>" title="<?php echo get_the_title(); ?>" alt="<?php echo get_the_title(); ?>" >
				</a>
			</div>
			<?php
		} else {
			?>
			<div class="event_date">	
				<span class="month"><?php echo date_i18n('M', $event_start_str); ?></span>
				<span class="date"> <?php echo date_i18n('d', $event_start_str); ?> </span>
			</div>
			<?php
		} ?>					
		
		<div class="event_desc">
			<div class="event_name">
				<a href="<?php echo get_permalink(); ?>" rel="bookmark" <?php if( $is_new_window ){ echo 'target="_blank"'; } ?> >
					<?php echo get_the_title(); ?>
				</a>
			</div>
			<?php 
			if( $event_date != '' ){
				?><div class="event_dates"><i class="fa fa-calendar"></i> <?php echo $event_date; ?></div><?php
			}

			if( $event_address != '' && $is_display_location ){ ?>
				<div class="event_address"><i class="fa fa-map-marker"></i> <?php echo $event_address; ?></div>
			<?php }	?>

			<?php
			if( $is_display_desc ){ 
				$description = get_the_content();
				if( $description != '' ){
					?>
					<p class="description" >
						<?php echo substr( $description, 0, 100 ) . '...'; ?>
					</p>
					<?php	
				}
			}	?>
		</div>
		<div style="clear: both"></div>
	</div>
</div>
