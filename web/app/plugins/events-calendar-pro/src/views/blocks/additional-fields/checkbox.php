<?php
/**
 * Block: Additional Fields - Checkbox
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version5.1.2
 *
 */
$label  = $this->attr( 'label' );
$output = $this->attr( 'output' );

if ( empty( $output ) ) {
	return;
}

?>
<div class="tribe-block tribe-block__additional-field tribe-block__additional-field__checkbox">
	<h3><?php echo esc_html( $label ); ?></h3>
	<?php echo esc_html( $output ); ?>
</div>
