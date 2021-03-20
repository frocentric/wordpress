<?php
/**
 * View: Filter Bar
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2/filter-bar.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 4.9.0
 */

use Tribe\Events\Filterbar\Views\V2\Filters;

$layout  = tribe( Filters::class )->get_layout_setting();
$classes = [ 'tribe-events-filters-' . $layout, 'tribe-clearfix' ];
?>
<?php do_action( 'tribe_events_filter_view_before_template' ); ?>
	<div id="tribe_events_filters_wrapper" <?php tribe_classes( $classes ); ?>>
		<?php do_action( 'tribe_events_filter_view_before_filters' ); ?>

		<div class="tribe-events-filters-content tribe-clearfix">
			<label class="tribe-events-filters-label"><?php esc_html_e( 'Narrow Your Results', 'tribe-events-filter-view' ); ?></label>

				<div class="tribe_events_filter_control tribe-clearfix">
					<button class="tribe_events_filters_show_filters tribe-js-filters-toggle"><?php esc_html_e( 'Show Filters', 'tribe-events-filter-view' ); ?></button>
					<?php if ( 'horizontal' === $layout ) : ?>
						<button class="tribe_events_filters_close_filters tribe-js-filters-toggle" data-state="<?php esc_attr_e( 'Show Advanced Filters', 'tribe-events-filter-view' ); ?>"><?php esc_html_e( 'Collapse Filters', 'tribe-events-filter-view' ); ?></button>
						<button class="tribe_events_filters_reset tribe-js-filters-reset"><span class="dashicons dashicons-image-rotate tribe-reset-icon"></span><?php esc_html_e( 'Reset Filters', 'tribe-events-filter-view' ); ?></button>
					<?php endif; ?>
				</div>

			<?php do_action( 'tribe_events_ajax_accessibility_check' ); ?>

			<form id="tribe_events_filters_form" method="post" action="">

				<?php
				do_action( 'tribe_events_filter_view_do_display_filters', $this->get( 'view' )->get_context() );
				?>

				<input type="submit" value="<?php esc_attr_e( 'Submit', 'tribe-events-filter-view' ); ?>" class="tribe_events_filters_form_submit" tabindex="-1" />

				<?php if ( 'vertical' === $layout ) : ?>
					<button type="button" class="tribe_events_filters_reset tribe_events_filters_reset--desktop tribe-js-filters-reset"><span class="dashicons dashicons-image-rotate tribe-reset-icon"></span><?php esc_html_e( 'Reset Filters', 'tribe-events-filter-view' ); ?></button>
				<?php endif; ?>
			</form>
			<div class="tribe_events_filter_control tribe-events-filters-mobile-controls tribe-clearfix">
				<button class="tribe_events_filters_close_filters tribe_events_filters_toggle tribe-js-filters-toggle" data-state="<?php esc_attr_e( 'Show Advanced Filters', 'tribe-events-filter-view' ); ?>"><?php esc_html_e( 'Collapse Filters', 'tribe-events-filter-view' ); ?></button>
				<?php if ( 'horizontal' === $layout ) : ?>
					<button class="tribe_events_filters_show_filters tribe-js-filters-toggle"><?php esc_html_e( 'Show Filters', 'tribe-events-filter-view' ); ?></button>
				<?php endif; ?>
				<button class="tribe_events_filters_reset tribe-js-filters-reset"><span class="dashicons dashicons-image-rotate tribe-reset-icon"></span><?php esc_html_e( 'Reset Filters', 'tribe-events-filter-view' ); ?></button>
			</div>
		</div>

		<?php do_action( 'tribe_events_filter_view_after_filters' ); ?>

	</div>
<?php do_action( 'tribe_events_filter_view_after_template' ); ?>
