<?php
/**
 * Filter the array of views that are registered for the tribe bar
 *
 * @param array $views {
 *     Array of views, where each view is itself represented by an associative array consisting of these keys:
 *
 *     @type string $displaying         slug for the view
 *     @type string $anchor             display text (i.e. "List" or "Month")
 *     @type string $event_bar_hook     not used
 *     @type string $url                url to the view
 * }
 *
 * @param boolean $context
 */
$views = apply_filters( 'tribe-events-bar-views', array(), false );

$enabled_views = tribe_get_option( 'tribeEnableViews', array() );

$views_options = array(
	'default' => esc_html__( 'Use Default View', 'tribe-events-calendar-pro' ),
);

foreach ( $views as $view ) {
	// Only include the enabled views on the default views array
	if ( in_array( $view['displaying'], $enabled_views ) ) {
		$views_options[ $view['displaying'] ] = $view['anchor'];
	}
}

$settings = Tribe__Main::array_insert_after_key(
	'viewOption',
	$settings,
	array(
		'mobile_default_view' => array(
			'type'            => 'dropdown',
			'label'           => esc_html__( 'Default mobile view', 'tribe-events-calendar-pro' ),
			'tooltip'         => esc_html__( 'Change the default view for Mobile users.', 'tribe-events-calendar-pro' ),
			'validation_type' => 'not_empty',
			'size'            => 'small',
			'default'         => 'default',
			'options'         => $views_options,
		),
	)
);

return $settings;