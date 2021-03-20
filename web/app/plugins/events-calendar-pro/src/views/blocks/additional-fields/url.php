<?php
/**
 * Block: Additional Fields - URL
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/blocks/additional-fields/url.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1ajx
 *
 * @version5.1.2
 *
 */
$label = $this->attr( 'label' );
$value = $this->attr( 'value' );

if ( empty( $value ) ) {
	return;
}
?>
<div class="tribe-block tribe-block__additional-field tribe-block__additional-field__url">
	<h3><?php echo esc_html( $label ); ?></h3>
	<a href="<?php echo esc_url( $value ); ?>"><?php echo esc_html( $value ); ?></a>
</div>
