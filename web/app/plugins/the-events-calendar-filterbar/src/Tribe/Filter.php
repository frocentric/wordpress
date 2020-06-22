<?php
/**
 * Controls each individual filter.
 * Each filter is an instance of this class.
 */

use Tribe__Utils__Array as Arr;

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Filterbar__Filter' ) ) {
	class Tribe__Events__Filterbar__Filter {

		/**
		 * @var string The type of filter.
		 */
		public $type = 'checkbox';

		/**
		 * @var string The name of the filter.
		 */
		public $name;

		/**
		 * @var string The filter slug.
		 */
		public $slug;

		/**
		 * @var int The priority (order) of the filter.
		 */
		public $priority;

		/**
		 * @var array The possible values for the filter.
		 */
		public $values;

		/**
		 * @var mixed The current selected value.
		 */
		public $currentValue;

		/**
		 * @var bool If the filter is currently active.
		 */
		public $isActiveFilter = false;

		/**
		 * @var array The query args the filter should add.
		 */
		public $queryArgs = array();

		public $joinClause = '';
		public $whereClause = '';

		/**
		 * @param string $name The name of the filter.
		 * @param string $slug The filter's slug.
		 */
		public function __construct( $name, $slug ) {
			$this->name = $name;
			$this->slug = $slug;

			$this->settings();
			$this->priority = $this->get_priority();
			$this->isActiveFilter = $this->is_active();
			$this->currentValue = $this->get_submitted_value();

			$this->setup_query_filters();
			$this->addHooks();

			tribe_singleton( 'filterbar.integrations', 'Tribe__Events__Filterbar__Integrations__Manager', array( 'hook' ) );
			tribe( 'filterbar.integrations' );
		}

		protected function get_submitted_value() {
			if ( isset( $_REQUEST[ 'tribe_' . $this->slug ] ) ) {
				$value = $_REQUEST[ 'tribe_' . $this->slug ];

				if ( is_array( $value ) ) {
					foreach ( $value as &$item ) {
						$item = str_replace( ',', '-', $item );
					}
				}

				return $value;
			} elseif ( is_tax( Tribe__Events__Main::TAXONOMY ) && 'eventcategory' === $this->slug ) {
				$category = get_queried_object();

				return empty( $category->term_id ) ? null : $category->term_id;
			}
		}

		/**
		 * Add the necessary action and filter hooks.
		 *
		 * @return void
		 */
		public function addHooks() {
			if ( $this->is_filtering() ) {
				add_action( 'tribe_events_filter_view_do_display_filters', array( $this, 'displayFilter' ), $this->priority );
				add_action( 'tribe_events_pre_get_posts', array( $this, 'addQueryArgs' ), 10 );
				// Filter repository (ORM) queries too.
				add_action( 'tribe_repository_events_query', array( $this, 'addQueryArgs' ), 10 );
			}
			add_filter( 'tribe_events_all_filters_array', array( $this, 'allFiltersArray' ), 10, 1 );
		}

		public function is_filtering() {
			if ( ! $this->isActiveFilter ) {
				return false;
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return true;
			}
			return ! is_admin();
		}

		/**
		 * Add the proper query arguments to the query.
		 *
		 * @param WP_Query $query
		 * @return void
		 */
		public function addQueryArgs( $query ) {

			// Let's only filter event queries or event repository (ORM) queries.
			if ( ! $query->tribe_is_event && ! ( doing_action( 'tribe_repository_events_query' ) ) ) {
				return;
			}

			// For back-compatibility purposes ensure a flag about this being an event query is set.
			$query->tribe_is_event = true;

			// we only want to apply the filters to a default render context
			if ( 'default' !== tribe_get_render_context( $query ) ) {
				return;
			}

			if ( isset( $_REQUEST[ 'tribe_' . $this->slug ] ) && $_REQUEST[ 'tribe_' . $this->slug ] != '' ) {
				if ( ! empty( $this->joinClause ) ) {
					add_filter( 'posts_join', array( $this, 'addQueryJoin' ), 11, 2 );
				}
				if ( ! empty( $this->whereClause ) ) {
					add_filter( 'posts_where', array( $this, 'addQueryWhere' ), 11, 2 );
				}
				foreach ( $this->queryArgs as $key => $value ) {
					$query->set( $key, $value );
				}

				$this->pre_get_posts( $query );
			}
		}

		/**
		 * Provides an opportunity for filter implementations to modify the query object directly
		 * when required: it will only be called from addQueryArgs() within the appropriate query
		 * render context and when the user has actually applied the filter.
		 *
		 * @param WP_Query $query
		 */
		protected function pre_get_posts( WP_Query $query ) {}

		/**
		 * Add the proper where clause to the query.
		 *
		 * @param string $posts_where The current WHERE clause.
		 * @param object $query The current query.
		 * @return string The new WHERE clause.
		 */
		public function addQueryWhere( $posts_where, $query ) {
			// Make sure it's an events query or a repository (ORM) query.
			if (
				$query->tribe_is_event
				|| $query->tribe_is_event_category
				|| doing_action( 'tribe_repository_events_query' )
			) {
				$posts_where .= $this->whereClause;
			}

			remove_filter( 'posts_where', array( $this, 'addQueryWhere' ), 11, 2 );
			return $posts_where;
		}

		/**
		 * Add the proper join clause to the query.
		 *
		 * @param string $posts_join The current JOIN clause.
		 * @param object $query The current query.
		 * @return string The new JOIN clause.
		 */
		public function addQueryJoin( $posts_join, $query ) {
			// Make sure it's an events query or a repository (ORM) query.
			if (
				$query->tribe_is_event
				|| $query->tribe_is_event_category
				|| doing_action( 'tribe_repository_events_query' )
			) {
				$posts_join .= $this->joinClause;
			}

			remove_filter( 'posts_join', array( $this, 'addQueryJoin' ), 11, 2 );
			return $posts_join;
		}


		/**
		 * Setup values to use in select2 dropdown
		 *
		 * @param $values
		 * @param $dropdown
		 *
		 * @return array
		 */
		public function setup_dropdown_options( $values, $dropdown = false ) {

			$options = array();
			foreach ( $values as $value ) {
				//if there is depth add space to show hierarchy in the dropdown
				$depth     = isset( $value['depth'] ) ? str_repeat( '&nbsp;', $value['depth'] * 2 ) : '';
				$options[] = array(
					'text'  => $dropdown ? $depth . $value['name'] :  $value['name'],
					'id'    => str_replace( ',', '-', $value['value'] ),
					'value' => $value['value'],
				);

			}

			return $options;
		}

		/**
		 * Display the Active Filters on Initial Loading
		 *
		 * @since 4.5
		 *
		 * @param array  $values
		 * @param string $select_filters
		 * @param string $type
		 *
		 * @return string
		 */
		public function setup_current_value_display( $values, $select_filters, $type = 'select' ) {

			$plucked = [];

			if ( 'checkbox' === $type ) {
				$plucked = wp_list_pluck( $values, 'name', 'value' );
			} elseif ( 'multiselect' === $type ) {
				$plucked = wp_list_pluck( $values, 'text', 'id' );
			} elseif ( 'select' === $type ) {
				$display_current = wp_list_pluck( $values, 'text', 'id' );
				return ! empty( $display_current[ $select_filters ] ) ? $display_current[ $select_filters ] : '';
			}

			// Since values can be comma-delimited IDs, we need to build a map of single IDs to their
			// comma-delimited counterparts.
			$plucked_key_to_key_group_map = [];
			foreach ( $plucked as $key_group => $value ) {
				$key_ids = explode( ',' , $key_group );
				foreach ( $key_ids as $key_id ) {
					$plucked_key_to_key_group_map[ (string) $key_id ] = $key_group;
				}
			}

			$selected_vals = [];
			if ( ! is_array( $select_filters ) ) {
				$select_filters = explode( ',', $select_filters );
			}

			foreach ( $select_filters as $value ) {
				$value = str_replace( ',', '-', $value );

				if ( ! empty( $plucked_key_to_key_group_map[ $value ] ) ) {
					$value = $plucked_key_to_key_group_map[ $value ];
					$selected_vals[ $value ] = $plucked[ $value ];
				}
			}

			$additional_selections = '';
			if ( 1 < count( $selected_vals ) ) {
				$additional_selections = ' <span class="tribe-events-filter-count">+' . ( count( $selected_vals ) - 1 ) . '</span>';
			}

			return  esc_html( current( $selected_vals ) ) . $additional_selections;
		}

		/**
		 * Display the given filter in the list on the frontend.
		 *
		 * @return void
		 */
		public function displayFilter() {
			$values = apply_filters( 'tribe_events_filter_values', $this->get_values(), $this->slug );

			if ( ! empty( $values ) ) {
				?>
				<fieldset class="tribe_events_filter_item<?php echo 'horizontal' === tribe_get_option( 'events_filters_layout', 'vertical' ) ? ' closed' : '';
				echo ! empty( $this->currentValue ) ? ' active' : ''; ?>" id="tribe_events_filter_item_<?php echo esc_attr( $this->slug ); ?>">
					<?php
				switch ( $this->type ) {
					case 'select':
						// It's possible for multiple values to be specified in array form, but we can
						// only use one of those for the select filter
						$current_value = is_array( $this->currentValue ) ? implode( '-', $this->currentValue ) : $this->currentValue;

						//Setup options in Tribe Dropdown format
						$options = $this->setup_dropdown_options( $values, true );
						$selected_vals = $this->setup_current_value_display( $options, $current_value, 'select' );
						$section_title = esc_html( stripslashes( $this->title ) );
						$section_slug  = sanitize_html_class( $section_title );
						?>
						<legend class="tribe-events-filters-legend">
							<button class="tribe-events-filters-group-heading" type="button" aria-expanded="true" aria-controls="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
								<?php echo $section_title; ?><span class="horizontal-drop-indicator"></span>
								<span class="tribe-filter-status"><?php echo str_replace( '&nbsp;', '', $selected_vals ); ?></span>
							</button>
						</legend>
						<div class="tribe-events-filter-group tribe-events-filter-select2 tribe-events-filter-select" id="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
							<div class="tribe-section-content">
								<div class="tribe-section-content-field">
									<input
										class="tribe-dropdown"
										data-allow-html
										data-dropdown-css-width="false"
										data-options="<?php echo esc_attr( json_encode( $options ) ); ?>"
										name="<?php echo esc_attr( 'tribe_' . $this->slug ); ?>"
										placeholder="<?php esc_attr_e( 'Select', 'tribe-events-filter-view' ); ?>"
										type="hidden"
										value="<?php echo esc_attr( $current_value ); ?>"
										style="width: 100%;" <?php /* This is required for selectWoo styling to prevent select box overflow */ ?>
									>
								</div>
							</div>
						</div>
					<?php
					break;

					//Option for Select2 Dropdown
					case 'multiselect':
						//Setup options in Tribe Dropdown format
						$options        = $this->setup_dropdown_options( $values );
						$select_filters = $this->currentValue ? Arr::to_list( $this->currentValue ) : '';
						$selected_vals  = $this->setup_current_value_display( $options, $select_filters, 'multiselect' );
						$section_title  = esc_html( stripslashes( $this->title ) );
						$section_slug   = sanitize_html_class( $section_title );
						?>
						<legend class="tribe-events-filters-legend">
							<button class="tribe-events-filters-group-heading" type="button" aria-expanded="false" aria-controls="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
								<?php echo $section_title; ?><span class="horizontal-drop-indicator"></span>
								<span class="tribe-filter-status"><?php echo $selected_vals; ?></span>
							</button>
						</legend>
						<div class="tribe-events-filter-group tribe-events-filter-select2 tribe-events-filter-multiselect" id="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
							<div class="tribe-section-content">
								<div class="tribe-section-content-field">
									<input
										class="tribe-dropdown"
										data-allow-html
										data-dropdown-css-width="false"
										data-options="<?php echo esc_attr( json_encode( $options ) ); ?>"
										multiple
										name="<?php echo esc_attr( 'tribe_' . $this->slug ); ?>"
										placeholder="<?php esc_attr_e( 'Select an Item', 'tribe-events-filter-view' ); ?>"
										type="hidden"
										style="width: 100%;" <?php /* This is required for selectWoo styling to prevent select box overflow */ ?>
										value="<?php echo isset( $select_filters ) ? esc_attr( $select_filters ) : ''; ?>"
									>
								</div>
							</div>
						</div>
						<?php
					break;
					case 'checkbox':

						if ( ! isset( $this->currentValue ) ) {
							$this->currentValue = array();
						}

						$selected_vals = $this->setup_current_value_display( $values, $this->currentValue, 'checkbox' );
						$section_id    = sanitize_html_class( $this->title );
						$section_title = esc_html( stripslashes( $this->title ) );
						$section_slug  = sanitize_html_class( $section_title );

						if ( 'featuredevent' === $this->slug && $selected_vals ) {
							$selected_vals = _x( 'Active', 'Featured Events active filter display label', 'tribe-events-filter-view' );
						}
						?>
						<div role="group" id="<?php echo $section_id ?>" aria-label="<?php echo $section_id ?>">
							<legend class="tribe-events-filters-legend">
								<button class="tribe-events-filters-group-heading" type="button" aria-expanded="false" aria-controls="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
									<?php echo $section_title; ?>
									<span class="horizontal-drop-indicator"></span>
									<span class="tribe-filter-status"><?php echo $selected_vals; ?></span>
								</button>
							</legend>
							<div class="tribe-events-filter-group tribe-events-filter-checkboxes" id="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
								<ul>
									<?php foreach ( $values as $option ) {

										$data = array();
										if ( isset( $option['data'] ) && is_array( $option['data'] ) ) {
											foreach ( $option['data'] as $attr => $value ) {
												$data[] = 'data-' . esc_attr( $attr ) . '="' . trim( $value ) . '"';
											}
										}
										$data = join( ' ', $data );

										// Support CSS classes per list item
										$class = '';

										if ( ! empty( $option['class'] ) ) {
											$class = ' class="' . esc_attr( $option['class'] ) . '"';
										}

										// output option to screen
										echo '<li' . $class . '>';
										echo '<input type="checkbox" id="' . esc_html( str_replace( ' ', '-', stripslashes( strtolower( $option['name'] ) ) ) ) . '" value="' . esc_attr( $option['value'] ) . '" ' . checked( $this->is_selected( trim( $option['value'] ) ), true, false ) . ' name="' . esc_attr( 'tribe_' . $this->slug . '[]' ) . '" ' . $data . ' aria-labelledby="' . esc_html( str_replace( ' ', '-', stripslashes( strtolower( $option['name'] ) ) ) ) . ' ' . $section_id . '" />';
										echo '<label for="' . esc_html( str_replace( ' ', '-', stripslashes( strtolower( $option['name'] ) ) ) ) . '">';
										echo '<span>' . esc_html( stripslashes( $option['name'] ) ) . '</span>';
										echo '</label>';
										echo '</li>';

									}
									?>
								</ul>
							</div>
						</div>
						<?php
					break;
					case 'radio':

						if ( ! isset( $this->currentValue ) ) {
							$current_value = '';
						} else {
							$current_value = is_array( $this->currentValue ) ? current( $this->currentValue ) : $this->currentValue;
						}
						$section_title = esc_html( stripslashes( $this->title ) );
						$section_slug  = sanitize_html_class( $section_title );
						?>
						<legend class="tribe-events-filters-legend">
							<button class="tribe-events-filters-group-heading" type="button" aria-expanded="false" aria-controls="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
								<?php echo $section_title; ?><span class="horizontal-drop-indicator"></span>
								<span class="tribe-filter-status"><?php ?></span>
							</button>
						</legend>
						<div class="tribe-events-filter-group tribe-events-filter-radio" id="tribe-filter-<?php echo esc_attr( $section_slug ); ?>">
						<ul>
						<?php foreach ( $values as $option ):

							$data = array();
							if ( isset( $option['data'] ) && is_array( $option['data'] ) ) {
								foreach ( $option['data'] as $attr => $value ) {
									$data[] = 'data-' . esc_attr( $attr ) . '="' . trim( $value ) . '"';
								}
							}
							$data = join( ' ', $data );

							// Support CSS classes per list item
							$class = '';

							if ( isset( $option['class'] ) && ! empty( $option['class'] ) ) {
								$class = ' class="' . esc_attr( $option['class'] ) . '"';
							}

							// output option to screen
							echo '<li ' . $class . '>';
							echo '<input type="radio" id="' . esc_html( str_replace( ' ', '-', stripslashes( strtolower( $option['name'] ) ) ) ) . '" value="' . esc_attr( $option['value'] ) . '" ' . checked( trim( $option['value'] ), $current_value, false ) . ' name="' . esc_attr( 'tribe_' . $this->slug ) . '" ' . $data . ' />';
							echo '<label for="' . esc_html( str_replace( ' ', '-', stripslashes( strtolower( $option['name'] ) ) ) ) . '">';
							echo '<span>' . esc_html( stripslashes( $option['name'] ) ) . '</span>';
							echo '</label>';
							echo '</li>';
							?>
						<?php endforeach; ?>
						</ul>
						</div>
						<?php
					break;
					case 'range':
						if ( ! empty( $this->currentValue ) && is_array( $this->currentValue ) ) {
							$current = reset( $this->currentValue );
						} else {
							$current = $values;
						}

						$section_title = esc_html( stripslashes( $this->title ) );
						$section_slug  = sanitize_html_class( $section_title );

						$min_value = $this->to_int( $values['min'] );
						$max_value = $this->to_int( $values['max'], 'up' );

						$pos_2 = $display_value = '';

						// Get our currency symbol
						$currency_symbol = tribe_get_option( 'defaultCurrencySymbol' );
						//Check to see if currency position setting is in front of or behind the value
						$reverse_position = tribe_get_option( 'reverseCurrencyPosition', false );

						$pos_1 = $currency_symbol;
						if ( $reverse_position ) {
							$pos_1 = '';
							$pos_2 = $currency_symbol;
						}

						if ( ! empty( $current['min'] ) ) {
							$current['min'] = $this->to_int( $current['min'] );
						}

						if ( ! empty( $current['max'] ) ) {
							$current['max'] = $this->to_int( $current['max'], 'up' );
						}

						if ( $current['min'] != $min_value || $current['max'] != $max_value ) {
							$set_value = $current['min'] . '-' . $current['max'];
							$display_value = $pos_1 . $current['min'] . $pos_2 . ' - ' . $pos_1 . $current['max'] . $pos_2;;
						} else {
							$set_value = '';
						}

						?>
							<legend class="tribe-events-filters-legend">
								<button class="tribe-events-filters-group-heading" type="button" aria-expanded="false" aria-controls="tribe-filter-<?php echo $section_slug; ?>">
									<?php echo $section_title; ?><span class="horizontal-drop-indicator"></span>
									<span class="tribe-filter-status"><?php esc_html_e( $display_value ); ?></span>
								</button>
							</legend>
							<div class="tribe-events-filter-group tribe-events-filter-range" id="tribe-filter-<?php echo $section_slug; ?>">
								<span id="<?php echo esc_attr( 'tribe_events_filter_' . $this->slug ); ?>_display" class="tribe_events_slider_val"></span>
								<input type="hidden" id="<?php echo esc_attr( 'tribe_events_filter_' . $this->slug ); ?>" name="<?php echo esc_attr( 'tribe_' . $this->slug ); ?>" value="<?php echo esc_attr( $set_value ); ?>"  />
							<div id="<?php echo esc_attr( 'tribe_events_filter_' . $this->slug . '_slider' ); ?>"></div>
							</div>
							<script>
								jQuery(document).ready(function($) {
									$( "#<?php echo 'tribe_events_filter_' . $this->slug . '_slider'; ?>" ).slider({
										range: true,
										min: <?php echo $min_value; ?>,
										max: <?php echo $max_value; ?>,
										values: [ <?php echo $this->to_int( $current['min'] ); ?>, <?php echo $this->to_int( $current['max'], 'up' ); ?> ],
										slide: function( event, ui ) {
											<?php
												if ( $reverse_position ) {
											?>
											$( "#<?php echo 'tribe_events_filter_' . $this->slug; ?>_display" ).text( ui.values[ 0 ] + "<?php echo $currency_symbol; ?>" + "-" + ui.values[ 1 ] + "<?php echo $currency_symbol; ?>" );
											<?php } else { ?>
											$( "#<?php echo 'tribe_events_filter_' . $this->slug; ?>_display" ).text( "<?php echo $currency_symbol; ?>" + ui.values[ 0 ] + "-<?php echo $currency_symbol; ?>" + ui.values[ 1 ] );
											<?php } ?>
											if( ui.values[ 0 ] === <?php echo $min_value; ?> && <?php echo $max_value; ?> === ui.values[ 1 ] ) {
												$( "#<?php echo 'tribe_events_filter_' . $this->slug; ?>" ).val('');
											} else {
												$( "#<?php echo 'tribe_events_filter_' . $this->slug; ?>" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
											}
										}
									});
								<?php if ( $reverse_position ) { ?>
									$( "#<?php echo 'tribe_events_filter_' . $this->slug; ?>_display" ).text( $( "#<?php echo 'tribe_events_filter_' . $this->slug . '_slider'; ?>" ).slider( "values", 0 ) + "<?php echo $currency_symbol; ?>" + "-" + $( "#<?php echo 'tribe_events_filter_' . $this->slug . '_slider'; ?>" ).slider( "values", 1 ) + "<?php echo $currency_symbol; ?>" );
								<?php } else { ?>
									$( "#<?php echo 'tribe_events_filter_' . $this->slug; ?>_display" ).text( "<?php echo $currency_symbol; ?>" + $( "#<?php echo 'tribe_events_filter_' . $this->slug . '_slider'; ?>" ).slider( "values", 0 ) + "-<?php echo $currency_symbol; ?>" + $( "#<?php echo 'tribe_events_filter_' . $this->slug . '_slider'; ?>" ).slider( "values", 1 ) );
								<?php } ?>
								});
							</script>
						<?php
					break;
				}
				?>
				</fieldset>
				<?php
			}
		}

		/**
		 * Convert a string to an Integer rounding the value based on the direction param Up or Down.
		 *
		 * @since 4.5.7
		 *
		 * @param string $value
		 * @param string $direction
		 * @param int $default
		 *
		 * @return int
		 */
		protected function to_int( $value = '', $direction = 'down', $default = 0 ) {
			if ( ! is_numeric( $value ) ) {
				return $default;
			}

			$result = 'down' === $direction ? floor( $value ) : ceil( $value );
			return absint( $result );
		}

		/**
		 * Tests to see if the option either matches the current filter value or, in the
		 * case of multiple current values being set (such as if the filter is operating
		 * in checkbox mode) if the option is among those values.
		 *
		 * @param  $option
		 *
		 * @return bool
		 */
		protected function is_selected( $option ) {
			if ( is_array( $this->currentValue ) ) {
				return in_array( $option, $this->currentValue );
			} else {
				return $option == $this->currentValue;
			}
		}

		/**
		 * Add the filter to the All Filters array.
		 *
		 * @param array $filters The current array of filters.
		 * @return array The array of filters.
		 */
		public function allFiltersArray( $filters ) {
			$this_filter = array(
				'name' => $this->name,
				'type' => $this->type,
				'admin_form' => $this->get_admin_form(),
			);

			$filters[ $this->slug ] = $this_filter;

			return $filters;
		}

		public function get_admin_form() {
			if ( ! empty( $this->adminForm ) ) {
				return $this->adminForm;
			} else {
				return $this->get_title_field();
			}
		}

		protected function get_title_field() {
			$field = sprintf(
				__( 'Title: %s', 'tribe-events-filter-view' ),
				sprintf(
					'<input type="text" name="%s" value="%s">',
					$this->get_admin_field_name( 'title' ),
					esc_attr( stripslashes( $this->title ) )
				)
			);
			return $field;
		}

		/**
		 * Helper function: generates a list of field types that can be selected for those filters
		 * which support a choice of dropdown, checkbox and radio modes.
		 *
		 * @return string
		 */
		protected function get_multichoice_type_field() {
			$name  = $this->get_admin_field_name( 'type' );
			$field = sprintf( __( 'Type: %s %s %s', 'tribe-events-filter-view' ),
				sprintf( '<label><input type="radio" name="%s" value="select" %s /> %s</label>',
					$name,
					checked( $this->type, 'select', false ),
					__( 'Dropdown', 'tribe-events-filter-view' )
				),
				sprintf( '<label><input type="radio" name="%s" value="checkbox" %s /> %s</label>',
					$name,
					checked( $this->type, 'checkbox', false ),
					__( 'Checkboxes', 'tribe-events-filter-view' )
				),
				sprintf( '<label><input type="radio" name="%s" value="multiselect" %s /> %s</label>',
					$name,
					checked( $this->type, 'multiselect', false ),
					__( 'Multi-Select', 'tribe-events-filter-view' )
				)
			);

			return '<div class="tribe_events_active_filter_type_options">' . $field . '</div>';
		}

		protected function get_admin_field_name( $name ) {
			return 'tribe_filter_options['.$this->slug.']['.$name.']';
		}

		protected function settings() {
			$this->title = $this->get_title();
			$this->type = $this->get_type();
		}

		protected function get_title() {
			$current_active_filters = Tribe__Events__Filterbar__View::instance()->get_filter_settings();
			$title = isset( $current_active_filters[ $this->slug ]['title'] ) ? $current_active_filters[ $this->slug ]['title'] : $this->name;
			return apply_filters( 'tribe_events_filter_title', $title, $this->slug );
		}

		protected function get_type() {
			$current_active_filters = Tribe__Events__Filterbar__View::instance()->get_filter_settings();
			$type = isset( $current_active_filters[ $this->slug ]['type'] ) ? $current_active_filters[ $this->slug ]['type'] : $this->type;
			return apply_filters( 'tribe_events_filter_type', $type, $this->slug );
		}

		protected function get_priority() {
			$current_active_slugs = Tribe__Events__Filterbar__View::instance()->get_active_filters();
			$priority = array_search( $this->slug, $current_active_slugs );

			if ( $priority !== false ) {
				$priority = ++$priority;
			} else {
				$priority = 0;
			}
			return apply_filters( 'tribe_events_filter_priority', $priority, $this->slug );
		}

		protected function is_active() {
			$current_active_filters = get_option( Tribe__Events__Filterbar__Settings::OPTION_ACTIVE_FILTERS, false );
			if ( $current_active_filters === false ) {
				$active = true; // everything is active by default
			} else {
				$active = isset( $current_active_filters[ $this->slug ] );
			}
			return apply_filters( 'tribe_events_filter_is_active', $active, $this->slug );
		}

		protected function get_values() {
			// template method
			return array();
		}

		protected function setup_query_filters() {
			if ( $this->currentValue ) {
				$this->setup_query_args();
				$this->setup_join_clause();
				$this->setup_where_clause();
			}
		}

		protected function setup_query_args() {
			// template method
		}

		protected function setup_join_clause() {
			// template method.
		}

		protected function setup_where_clause() {
			// template method.
		}
	}
}
