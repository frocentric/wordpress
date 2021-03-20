<?php
/**
 * View: Filters
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/filter-bar/filters.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @var string       $layout  Layout of the filter bar, `vertical` or `horizontal`.
 * @var array<array> $filters Filters available for filter bar.
 *
 * @version 5.0.0
 */

use Tribe__Utils__Array as Arr;

if ( 'horizontal' !== $layout ) {
	return;
}

if ( empty( $filters ) ) {
	return;
}
?>
<div
	class="tribe-filter-bar__filters-slider-container swiper-container"
	data-js="tribe-filter-bar-filters-slider-container"
>
	<div class="tribe-filter-bar__filters-slider-wrapper swiper-wrapper">
		<?php foreach ( $filters as $filter ) : ?>
			<div class="tribe-filter-bar__filters-slide swiper-slide">
				<?php
				$pill_toggle_id = $filter['pill_toggle_id'];
				$container_id   = $filter['container_id'];

				if ( empty( $pill_toggle_id || empty( $container_id ) ) ) {
					continue;
				}

				$this->template(
					'components/pill-button',
					[
						'classes'        => [ 'tribe-filter-bar__filters-slide-pill' ],
						'attrs'          => [ 'data-filter-name' => Arr::get( $filter, 'name', '' ) ],
						'label'          => Arr::get( $filter, 'label', '' ),
						'selections'     => Arr::get( $filter, 'selections', '' ),
						'button_classes' => [],
						'button_attrs'   => [
							'id'            => $pill_toggle_id,
							'aria-controls' => $container_id,
							'aria-expanded' => Arr::get( $filter, 'is_open', false ) ? 'true' : 'false',
							'data-js'       => 'tribe-events-accordion-trigger tribe-filter-bar-filters-slide-pill',
						],
					]
				);
				?>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="tribe-filter-bar__filters-slider-nav">
		<button
			class="tribe-filter-bar__filters-slider-nav-button tribe-filter-bar__filters-slider-nav-button--prev"
			data-js="tribe-filter-bar-filters-slider-nav-button tribe-filter-bar-filters-slider-nav-button-prev"
			type="button"
		>
			<?php $this->template( 'components/icons/caret-left', [ 'classes' => [ 'tribe-filter-bar__filters-slider-nav-button-icon' ] ] ); ?>
		</button>
		<button
			class="tribe-filter-bar__filters-slider-nav-button tribe-filter-bar__filters-slider-nav-button--next"
			data-js="tribe-filter-bar-filters-slider-nav-button tribe-filter-bar-filters-slider-nav-button-next"
			type="button"
		>
			<?php $this->template( 'components/icons/caret-right', [ 'classes' => [ 'tribe-filter-bar__filters-slider-nav-button-icon' ] ] ); ?>
		</button>
	</div>
</div>
