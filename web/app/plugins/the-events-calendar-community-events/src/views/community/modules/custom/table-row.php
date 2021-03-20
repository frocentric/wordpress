<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox Table Row For Custom Fields
 * This is used to add rows to the table in the event submission form that contains custom field inputs.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/custom/table-row.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since   4.6.3
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
 */

$data = compact( [
	'fields',
	'post_id',
	'field',
	'field_classes',
	'field_name',
	'field_label',
	'field_type',
	'field_id',
	'value',
	'options',
] );
?>

<tr class="<?php echo esc_attr( $field_classes ); ?>">
	<td class="tribe-section-content-label">
		<?php tribe_community_events_field_label( $field_id, sprintf( _x( '%s:', 'custom field label', 'tribe-events-community' ), $field_label ) ); ?>
	</td>
	<td class="tribe-section-content-field">
		<?php if ( in_array( $field_type, [ 'radio', 'checkbox' ], true ) ) : ?>
			<?php foreach ( $options as $key => $option ) : ?>
				<?php
				if ( '' === $key ) {
					$key = 'none';
				}

				$option    = stripslashes( $option );
				$option_id = $field_id . '-' . $key;

				$data['option']    = $option;
				$data['option_id'] = $option_id;
				?>
				<?php tribe_get_template_part( 'community/modules/custom/fields/input-option', null, $data ); ?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php tribe_get_template_part( 'community/modules/custom/fields/' . $field_type, null, $data ); ?>
		<?php endif; ?>
	</td>
</tr>
