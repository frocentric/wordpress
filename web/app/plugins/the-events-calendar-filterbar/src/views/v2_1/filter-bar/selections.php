<?php
/**
 * View: Selections
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/filter-bar/selections.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var array<array> $selected_filters Filters that have been selected.
 *
 * @version 5.0.0
 */

use Tribe__Utils__Array as Arr;

if ( empty( $selected_filters ) ) {
	return;
}
?>
<div class="tribe-filter-bar__selected-filters">

	<header class="tribe-filter-bar__selected-filters-header">
		<span class="tribe-filter-bar__selected-filters-label tribe-common-h7">
			<?php esc_html_e( 'Your selections', 'tribe-events-filter-view' ); ?>
		</span>
		<?php $this->template( 'components/clear-button', [ 'classes' => [ 'tribe-filter-bar__selected-filters-clear-button' ] ] ); ?>
	</header>

	<div class="tribe-filter-bar__selected-filters-list-container">
		<div class="tribe-filter-bar__selected-filters-list">

			<?php foreach ( $selected_filters as $filter ) : ?>
				<div class="tribe-filter-bar__selected-filters-list-item">
					<?php
					$this->template(
						'components/pill',
						[
							'classes'    => [ 'tribe-filter-bar__selected-filter' ],
							'attrs'      => [
								'data-js'          => 'tribe-filter-bar__selected-filter',
								'data-filter-name' => Arr::get( $filter, 'name', '' ),
							],
							'label'      => Arr::get( $filter, 'label', '' ),
							'selections' => Arr::get( $filter, 'selections', '' ),
						]
					);
					?>
				</div>
			<?php endforeach; ?>

		</div>
	</div>

</div>
