<?php
/**
 * This template renders the RSVP ticket form quantity input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/sidebar/quantity.php
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @since 4.12.3
 *
 * @version 4.12.3
 */

?>
<div class="tribe-tickets__rsvp-ar-quantity">
	<span class="tribe-common-h7 tribe-common-h--alt">
		<?php
		echo esc_html(
			sprintf(
				/* Translators: %s Guest label for RSVP attendee registration sidebar title. */
				__( 'Total %s', 'event-tickets' ),
				tribe_get_guest_label_plural( 'RSVP attendee registration sidebar title' )
			)
		);
		?>
	</span>

	<div class="tribe-tickets__rsvp-ar-quantity-input">
		<?php
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		$this->template( 'v2/rsvp/ari/sidebar/quantity/minus' );
		?>

		<?php
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		$this->template( 'v2/rsvp/ari/sidebar/quantity/input', array( 'rsvp' => $rsvp ) );
		?>

		<?php
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		$this->template( 'v2/rsvp/ari/sidebar/quantity/plus' );
		?>
	</div>
	<?php
	/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
	$tickets_handler = tribe( 'tickets.handler' );
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
	$max_at_a_time = $tickets_handler->get_ticket_max_purchase( $rsvp->ID );

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
	if ( $max_at_a_time < $rsvp->remaining() ) :
		?>
	<div class="tribe-common-b3 tribe-tickets__form-field-description">
		<?php
		echo esc_html(
			sprintf(
				/* Translators: %s Guest label for RSVP attendee registration sidebar title. */
				__( 'Max. allowed: %s', 'froware' ),
				$max_at_a_time
			)
		);
		?>
	</div>
	<?php endif; ?>
</div>
