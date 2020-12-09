<?php
// Don't load directly
defined( 'WPINC' ) || die;

/**
 * My Events List Template
 * The template for a list of a users events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/event-list.php
 *
 * @since 2.1
 * @version 4.6.3
 */

$organizer_label_singular      = tribe_get_organizer_label_singular();
$venue_label_singular          = tribe_get_venue_label_singular();
$events_label_plural           = tribe_get_event_label_plural();
$events_label_plural_lowercase = tribe_get_event_label_plural_lowercase();

$columns = tribe_community_events_list_columns();
/**
 * Allows filtering for which columns cannot be hidden by users
 *
 * @param array $blocked
 */
$blocked_columns = apply_filters( 'tribe_community_events_list_columns_blocked', [ 'title' ] );
?>

<h2 class="tribe-community-events-list-title"><?php echo esc_html__( 'My Events', 'tribe-events-community' ); ?></h2>
<a
	class="tribe-button tribe-button-primary add-new"
	href="<?php echo esc_url( tribe_community_events_add_event_link() ); ?>"
>
	<?php echo apply_filters( 'tribe_community_events_add_event_label', __( 'Add New', 'tribe-events-community' ) ); ?>
</a>


<?php
/**
 * Allow developers to hook and add content to the begining of this section of content
 */
do_action( 'tribe_community_events_before_list_navigation' );
?>

<div class="tribe-event-list-search">
	<form role="search" method="get" class="tribe-search-form" action="">
		<div>
			<label class="screen-reader-text" for="s">Search for:</label>
			<input type="search" value="<?php echo isset( $_GET['event-search'] ) ? esc_html( $_GET['event-search'] ) : ''; ?>" name="event-search" placeholder="<?php echo esc_html__( 'Search Event Titles', 'tribe-events-community' ); ?>"" />
			<input type="submit" id="search-submit" value="Search"/>
		</div>
	</form>
</div>

<div class="tribe-nav tribe-nav-top">
	<div class="my-events-display-options ce-top">
		<?php tribe_community_events_prev_next_nav(); ?>
	</div>
	<div class="table-menu-wrapper ce-top">
		<?php if ( $events->have_posts() ) : ?>
			<a
				class="table-menu-btn button tribe-button tribe-button-tertiary tribe-button-activate"
				href="#"
			>
				<?php echo apply_filters( 'tribe_community_events_list_display_button_text', __( 'Display Option', 'tribe-events-community' ) ); ?>
			</a>
		<?php endif; ?>

		<?php
		/**
		 * Allow developers to hook and add content to the end of this section of content
		 */
		do_action( 'tribe_community_events_after_list_navigation_buttons' );
		?>

		<div class="table-menu table-menu-hidden">
			<ul>
				<?php foreach ( $columns as $column_slug => $column_label ) : ?>
					<?php $i = array_search( $column_slug, array_keys( $columns ) ); ?>
					<li>
						<label
							class="<?php echo sanitize_html_class( in_array( $column_slug, $blocked_columns ) ? 'tribe-hidden' : '' ) ?>"
							for="<?php echo sanitize_html_class( 'tribe-toggle-column-' . $column_slug ); ?>"
						>
							<input class="tribe-toggle-column" type="checkbox" id="<?php echo sanitize_html_class( 'tribe-toggle-column-' . $column_slug ); ?>"  checked />
							<?php echo esc_html( $column_label ); ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<?php // list pagination
	echo tribe_community_events_get_messages();
	echo $this->pagination( $events, '', $this->paginationRange );
	?>
</div>

<?php
/**
 * Allow developers to hook and add content to the begining of this section of content
 */
do_action( 'tribe_community_events_before_list_table' );
?>

<?php if ( $events->have_posts() ) : ?>
	<div class="tribe-responsive-table-container">
		<table
			id="tribe-community-events-list"
			class="tribe-community-events-list my-events display responsive stripe"
		>
			<thead>
				<tr>
					<?php foreach ( $columns as $column_slug => $column_label ) : ?>
						<th
							data-depends="#<?php echo sanitize_html_class( 'tribe-toggle-column-' . $column_slug ); ?>"
							data-condition-is-checked
							class="tribe-dependent column-header <?php echo sanitize_html_class( 'column-header-' . $column_slug ); ?>"
						>
							<?php echo esc_html( $column_label ); ?>
						</th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody>
				<?php while ( $events->have_posts() ) : ?>
					<?php $event = $events->next_post(); ?>
					<tr class="<?php echo sanitize_html_class( 1 === $events->current_post % 2 ? 'odd' : '' ); ?>">
						<?php foreach ( $columns as $column_slug => $column_label ) : ?>
							<?php
							$context = [
								'column_slug' => $column_slug,
								'column_label' => $column_label,
								'event' => $event,
							];
							?>
						<td
							data-depends="#<?php echo sanitize_html_class( 'tribe-toggle-column-' . $column_slug ); ?>"
							data-condition-is-checked
							class="tribe-dependent tribe-list-column <?php echo sanitize_html_class( 'tribe-list-column-' . $column_slug ); ?>"
						>
							<?php tribe_get_template_part( 'community/columns/' . sanitize_key( $column_slug ), null, $context ); ?>
						</td>
						<?php endforeach; ?>
					</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<div class="tribe-community-events-list tribe-community-no-items">
		<?php
		if ( isset( $_GET['eventDisplay'] ) && 'past' === $_GET['eventDisplay'] ) {
			$text = esc_html__( 'You have no past %s', 'tribe-events-community' );
		} else {
			$text = esc_html__( 'You have no upcoming %s', 'tribe-events-community' );
		}
		echo sprintf( $text, $events_label_plural_lowercase );
		?>
	</div>
<?php endif; ?>

<?php
/**
 * Allow developers to hook and add content to the end of this section of content
 */
do_action( 'tribe_community_events_after_list_table' );
?>

<div class="tribe-nav tribe-nav-bottom">
	<?php
	echo tribe_community_events_get_messages();
	echo $this->pagination( $events, '', $this->paginationRange );
	?>
</div>
