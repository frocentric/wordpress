<?php
/**
 * View: Field Type Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/field-type.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var array<string,mixed> $data Data of field type, should contain `type` key.
 *
 * @version 5.0.0
 *
 */

if ( empty( $data['type'] ) ) {
	return;
}

switch ( $data['type'] ) {
	case 'checkbox':
		$this->template( 'components/checkbox', $data );
		break;
	case 'dropdown':
		$this->template( 'components/dropdown', $data );
		break;
	case 'multiselect':
		$this->template( 'components/multiselect', $data );
		break;
	case 'radio':
		$this->template( 'components/radio', $data );
		break;
	case 'range':
		$this->template( 'components/range', $data );
		break;
	default:
		break;
}
