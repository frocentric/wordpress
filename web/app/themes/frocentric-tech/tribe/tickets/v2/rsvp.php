<?php
/**
 * Block: RSVP
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link  https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 *
 * @version 5.0.0
 *
 * @var Tribe__Tickets__Editor__Template $this
 * @var WP_Post|int                      $post_id       The post object or ID.
 * @var boolean                          $has_rsvps     True if there are RSVPs.
 * @var array                            $active_rsvps  An array containing the active RSVPs.
 * @var string                           $block_html_id The unique HTML id for the block.
 */

// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
// phpcs:disable PHPCompatibility.Operators.NewOperators.t_spaceshipFound
// We don't display anything if there is no RSVP.
if ( ! $has_rsvps ) {
	return false;
}

// Bail if there are no active RSVP.
if ( empty( $active_rsvps ) ) {
	return;
}

// Check for specified sort order
// Accepted values: id | name | price | capacity
$key = 'ticket_sort_by';
$values = get_post_custom_values( $key, $post_id );

if ( is_array( $values ) && ! empty( $values[0] ) ) {
	$sort_field = $values[0];

	switch ( strtolower( $sort_field ) ) {
		case 'id':
			$callable = fn( $a, $b ) => $a->ID <=> $b->ID;
			break;
		case 'name':
			$callable = fn( $a, $b ) => strcmp( $a->name, $b->name );
			break;
		case 'price':
			$callable = fn( $a, $b ) => $a->price <=> $b->price;
			break;
		case 'capacity':
			$callable = fn( $a, $b ) => ( $a->capacity <=> $b->capacity ) * -1;
			break;
		default:
			$callable = null;
			break;
	}

	if ( $callable ) {
		usort( $active_rsvps, $callable );
	}
}

?>

<div
	id="<?php echo esc_attr( $block_html_id ); ?>"
	class="tribe-common event-tickets"
>
	<?php foreach ( $active_rsvps as $rsvp ) : ?>
		<div
			class="tribe-tickets__rsvp-wrapper"
			data-rsvp-id="<?php echo esc_attr( $rsvp->ID ); ?>"
		>
			<?php $this->template( 'v2/components/loader/loader' ); ?>
			<?php $this->template( 'v2/rsvp/content', array( 'rsvp' => $rsvp ) ); ?>

		</div>
	<?php endforeach; ?>
</div>
