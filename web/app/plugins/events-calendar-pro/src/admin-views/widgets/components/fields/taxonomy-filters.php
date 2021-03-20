<?php
/**
 * Admin View: Widget Selected Taxonomy Filters Component
 *
 * Administration Views cannot be overwritten by your theme.
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1aiy
 *
 * @since 5.2.0 Introduced.
 * @since 5.3.0 Moved to `components/fields`.
 *
 * @version 5.3.0
 *
 * @var array<string,string> $value      The input values to iterate through and display.
 * @var WP_Widget            $widget_obj The widget object.
 * @var string               $disabled   The already-selected items for select2 to disable.
 * @var array<string,mixed>  $list_items The array of items for our selected terms list.
 *                                       In the following format:
 *                                       [
 *                                           'tribe_events_cat = [
 *                                               'name' => 'Events Category',
 *                                               'terms' => [
 *                                                   [
 *                                                       'name' => 'Movie,
 *                                                       'id'   => 44,
 *                                                   ]
 *                                               ]
 *                                           ],
 *                                           'tags' => [
 *                                                etc...
 *                                           ],
 *                                       ];
 */

?>
<input
	type="hidden"
	name="<?php echo esc_attr( $name ); ?>"
	id="<?php echo esc_attr( $id ); ?>"
	class="calendar-widget-added-filters"
	<?php if ( ! empty( $value ) ) : ?>
		value="<?php echo esc_attr( $value ); ?>"
	<?php endif; ?>
/>
<ul class="calendar-widget-filter-list">
	<?php if ( ! empty( $list_items ) ) : ?>
		<?php foreach ( $list_items as $tax_name => $tax_obj ) : ?>
			<?php foreach ( $tax_obj['terms'] as $term_obj ) : ?>
				<li class="calendar-widget-filter-item">
					<?php echo esc_html( $tax_obj['name'] ); ?>: <?php echo esc_html( $term_obj['name'] ); ?>
					<a href="#" class="calendar-widget-remove-filter" data-tax="<?php echo esc_attr( $tax_name ); ?>" data-term="<?php echo esc_attr( $term_obj['id'] ); ?>">
						<?php echo esc_html_x( '(remove)', '"Remove" label for selected term.', 'tribe-events-calendar-pro' ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>
<?php
