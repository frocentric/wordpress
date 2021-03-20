<?php
/**
 * View: Filters
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/filter-bar/filters.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string       $layout  Layout of the filter bar, `vertical` or `horizontal`.
 * @var array<array> $filters Filters available for filter bar.
 *
 * @version 5.0.0
 */

use Tribe__Utils__Array as Arr;

if ( empty( $filters ) ) {
	return;
}

if ( 'horizontal' === $layout ) {
	$style = 'pill';
} elseif ( 'vertical' === $layout ) {
	$style = 'accordion';
}
?>
<div
	class="tribe-filter-bar__filters-container"
	data-js="tribe-filter-bar-filters-container"
>
	<div class="tribe-filter-bar__filters">
		<?php foreach ( $filters as $filter ) : ?>
			<?php
			if ( empty( $filter['toggle_id'] || empty( $filter['container_id'] ) ) ) {
				continue;
			}

			$container_labelledby = [ $filter['toggle_id'] ];

			if ( 'horizontal' === $layout && ! empty( $filter['pill_toggle_id'] ) ) {
				$container_labelledby[] = $filter['pill_toggle_id'];
			}

			$container_labelledby = implode( ' ', $container_labelledby );

			$this->template(
				'components/filter',
				[
					'style'                => $style,
					'label'                => Arr::get( $filter, 'label', '' ),
					'selections_count'     => Arr::get( $filter, 'selections_count', '' ),
					'selections'           => Arr::get( $filter, 'selections', '' ),
					'is_open'              => Arr::get( $filter, 'is_open', false ),
					'toggle_id'            => Arr::get( $filter, 'toggle_id', '' ),
					'container_id'         => Arr::get( $filter, 'container_id', '' ),
					'container_labelledby' => $container_labelledby,
					'fields'               => Arr::get( $filter, 'fields', [] ),
					'type'                 => Arr::get( $filter, 'type', '' ),
				]
			);
			?>
		<?php endforeach; ?>
	</div>
</div>
