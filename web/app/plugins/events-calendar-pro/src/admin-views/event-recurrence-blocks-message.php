<?php
// Prevents this from showing when we shouldnt
if ( tribe( 'events-pro.editor.meta' )->show_recurrence_classic_meta( true, $post_id ) ) {
	return false;
}
?>

<tr class="tribe-events-recurrence-has-blocks-warning">
	<td colspan="2">
		<p>
			<?php esc_html_e( 'This event’s recurrence rules were created in the block editor. Please use the block editor to make any further changes.', 'tribe-events-calendar-pro' ); ?>
		</p>
	</td>
</tr>
