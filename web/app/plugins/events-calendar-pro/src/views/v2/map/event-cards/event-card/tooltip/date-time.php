<?php
/**
 * View: Map View - Tooltip Date Time
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/tooltip/date-time.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @since 5.0.0
 * @since 5.1.1 Moved icons out to separate templates.
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var object  $date_formats Object containing the date formats.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */
use Tribe__Date_Utils as Dates;

$event_date_attr = $event->dates->start->format( Dates::DBDATEFORMAT );
?>
<div class="tribe-events-pro-map__event-tooltip-datetime-wrapper tribe-common-b2 tribe-common-b3--min-medium">
	<?php $this->template( 'map/event-cards/event-card/tooltip/date-time/featured' ); ?>
	<time
		class="tribe-events-pro-map__event-tooltip-datetime"
		datetime="<?php echo esc_attr( $event_date_attr ); ?>"
	>
		<?php echo $event->schedule_details->value(); ?>
	</time>
</div>
