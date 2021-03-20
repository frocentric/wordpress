<?php
/**
 * View: Pill Button Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/pill-button.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var array<string>        $classes        Array of classes to add to pill.
 * @var array<string,string> $attrs          Associative array of key and value for attributes to add to pill.
 * @var array<string>        $button_classes Array of classes to add to button.
 * @var array<string,string> $button_attrs   Associative array of key and value for attributes to add to button.
 * @var string               $label          Label for the pill.
 * @var string               $selections     Selections of the filter labeled by pill.
 *
 * @version 5.0.0
 *
 */
$pill_classes = [ 'tribe-filter-bar-c-pill', 'tribe-filter-bar-c-pill--button' ];

if ( ! empty( $selections ) ) {
	$pill_classes[] = 'tribe-filter-bar-c-pill--has-selections';
}

if ( ! empty( $classes ) ) {
	$pill_classes = array_merge( $pill_classes, $classes );
}

$pill_button_classes = [ 'tribe-filter-bar-c-pill__pill', 'tribe-common-b2', 'tribe-common-b3--min-medium' ];

if ( ! empty( $button_classes ) ) {
	$pill_button_classes = array_merge( $pill_button_classes, $button_classes );
}
?>
<div <?php tribe_classes( $pill_classes ); ?> <?php tribe_attributes( $attrs ); ?> >
	<button
		<?php tribe_classes( $pill_button_classes ); ?>
		<?php tribe_attributes( $button_attrs ); ?>
		type="button"
	>
		<span class="tribe-filter-bar-c-pill__pill-label"><?php echo esc_html( $label ); ?></span><span class="tribe-filter-bar-c-pill__pill-label-colon">:</span>
		<span class="tribe-filter-bar-c-pill__pill-selections">
			<?php echo esc_html( $selections ); ?>
		</span>
	</button>
	<button class="tribe-filter-bar-c-pill__remove-button" data-js="tribe-filter-bar-c-pill__remove-button" type="button">
		<?php $this->template( 'components/icons/close-alt', [ 'classes' => [ 'tribe-filter-bar-c-pill__remove-button-icon' ] ] ); ?>
		<span class="tribe-filter-bar-c-pill__remove-button-text tribe-common-a11y-visual-hide">
			<?php esc_html_e( 'Remove filters', 'tribe-events-filter-view' ); ?>
		</span>
	</button>
</div>
