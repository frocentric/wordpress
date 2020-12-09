<?php
/**
 * Block: Additional Fields - Radio
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
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
<div class="tribe-block tribe-block__additional-field tribe-block__additional-field__radio">
	<h3><?php echo esc_html( $label ); ?></h3>
	<?php echo esc_html( $value ); ?>
</div>
