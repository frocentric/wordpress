<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form input options for checkbox/radio fields.
 * This is used to add input options to the checkbox/radio fields in the event submission form that contains custom
 * field inputs.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/custom/fields/input-option.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  4.6.3
 * @since  4.7.1 Now using new tribe_community_events_field_classes function to set up classes for the input.
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 * @var array        $fields        List of form fields.
 * @var int          $post_id       Current Post ID.
 * @var array        $field         Current field data.
 * @var string       $field_classes List of field classes separated by space.
 * @var string       $field_name    Field name.
 * @var string       $field_label   Field label.
 * @var string       $field_type    Field type.
 * @var string       $field_id      Field HTML ID.
 * @var string|array $value         Current field value (or value from $_POST).
 * @var array        $options       List of options for checkbox/radio/dropdown fields.
 * @var string       $option        Option value.
 * @var string       $option_id     Option HTML ID.
 */
?>

<label
	for="<?php echo esc_attr( $option_id ); ?>"
>
	<input
		id="<?php echo esc_attr( $option_id ); ?>"
		type="<?php echo esc_attr( $field_type ); ?>"
		name="<?php echo esc_attr( $field_name ); ?>"
		class="<?php tribe_community_events_field_classes( $field_name, [] ); ?>"
		value="<?php echo esc_attr( $option ); ?>"
		<?php checked( in_array( $option, (array) $value, true ) ); ?>
	> <?php echo esc_html( $option ); ?>
</label>
