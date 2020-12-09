<?php
/**
 * Upcoming Events for WP Event aggregator.
 *
 * @link       http://xylusthemes.com/
 * @since      1.2.1
 *
 * @package    WP_Event_Aggregator_Pro
 * @subpackage WP_Event_Aggregator_Pro/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Core class used to implement Upcoming Events widget
 *
 * @since 1..2.1
 *
 * @see WP_Widget
 */
class WP_Event_Aggregator_Pro_Upcoming_Widget extends WP_Widget {

	/**
	 * Defualt widget options
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public $default_options;

	/**
	 * widget style options
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public $display_styles;
	
	/**
	 * Sets up a new Upcoming Events widget instance.
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'WP_Event_Aggregator_Pro_Upcoming_Widget',
			'description' => __( 'Display Upcoming WP Events.' ),
		);
		$control_ops = array( 'width' => '', 'height' => '' );
		parent::__construct( 'wp_event_aggregator_upcoming_widget', __( 'Upcoming WP Events', 'wp-event-aggregator-pro' ), $widget_ops, $control_ops );

		$this->default_options = array(
			'title' 	 	=> '',
			'max_events' 	=> 10,
			'event_cats' 	=> array(),
			'start_date'	=> '',
			'end_date'		=> '',
			'display_style' => 'style1',
			'new_window' 	=> 0,
			'display_event_image' 	 => 1,
			'display_event_location' => 1,
			'display_event_enddate'  => 0,
			'display_event_desc'	 => 0,
		);

		$this->display_styles = array(
			'style1' => __( 'Style 1', 'wp-event-aggregator-pro' ),
			'style2' => __( 'Style 2 (coming soon)','wp-event-aggregator-pro' ),
			);
		
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __('Upcoming Events', 'wp-event-aggregator-pro') : $instance['title'], $instance, $this->id_base );
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
			<div class="wpea_events_widget">
				<?php $this->wpea_render_upcoming_events( $args, $instance ); ?>
			</div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current widget instance.
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['max_events'] = sanitize_text_field( $new_instance['max_events'] );
		$instance['event_cats'] = $new_instance['event_cats'];
		$instance['start_date'] = sanitize_text_field( $new_instance['start_date'] );
		$instance['end_date'] = sanitize_text_field( $new_instance['end_date'] );
		$instance['display_style'] = sanitize_text_field( $new_instance['display_style'] );
		$instance['new_window'] = $new_instance['new_window'] ? 1 : 0;
		$instance['display_event_image'] = $new_instance['display_event_image'] ? 1 : 0;
		$instance['display_event_location'] = $new_instance['display_event_location'] ? 1 : 0;
		$instance['display_event_enddate'] = $new_instance['display_event_enddate'] ? 1 : 0;
		$instance['display_event_desc'] = $new_instance['display_event_desc'] ? 1 : 0;
		return $instance;
	}

	/**
	 * Outputs the Upcoming WP Events widget settings form.
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default_options );
		extract( $instance );
		$title = sanitize_text_field( $instance['title'] );
		$this->render_input_field( 'title', $title, __( 'Title:', 'wp-event-aggregator-pro' ), 'text');
		$this->render_input_field( 'max_events', $max_events, __( 'Max. Events:', 'wp-event-aggregator-pro' ), 'number' );
		// category.
		$this->render_event_taxonomy_terms( 'event_cats[]', $event_cats, __( 'Select Event Category', 'wp-event-aggregator-pro' ) );
		// start date
		$this->render_input_field( 'start_date', $start_date, __( 'Start Date (Optional):', 'wp-event-aggregator-pro' ), 'date');
		// end date
		$this->render_input_field( 'end_date', $end_date, __( 'End Date (Optional):', 'wp-event-aggregator-pro' ), 'date');	
		$this->render_input_field( 'display_style', $display_style, __( 'Select Event listing style', 'wp-event-aggregator-pro' ), 'select', '', $this->display_styles );
		$this->render_input_field( 'display_event_image', $display_event_image, __( 'Display Event Image', 'wp-event-aggregator-pro' ), 'checkbox' );
		$this->render_input_field( 'display_event_location', $display_event_location, __( 'Display Event Location', 'wp-event-aggregator-pro' ), 'checkbox' );
		$this->render_input_field( 'display_event_enddate', $display_event_enddate, __( 'Display Event Enddate', 'wp-event-aggregator-pro' ), 'checkbox' );
		$this->render_input_field( 'display_event_desc', $display_event_desc, __( 'Display Event Description', 'wp-event-aggregator-pro' ), 'checkbox' );
		$this->render_input_field( 'new_window', $new_window, __( 'Open Events in new window', 'wp-event-aggregator-pro' ), 'checkbox' );
	}

	/**
	 * Generate and render HTML for input element.
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 */
	public function render_input_field( $name, $value, $title, $type = 'text', $description = '', $options = array() ){
		$name = $this->get_field_name( $name );
		$id = $this->get_field_id( $name );

		switch ( $type ) {
			case 'text':
				?>
				<p>
					<label for="<?php echo $id; ?>"><?php echo $title; ?></label>
					<input class="widefat" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="text" value="<?php echo esc_attr($value); ?>" />
				</p>
				<?php
				break;

			case 'number':
				?>
				<p>
					<label for="<?php echo $id; ?>"><?php echo $title; ?></label>
					<input class="widefat" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="number" min="0" value="<?php echo esc_attr($value); ?>" />
				</p>
				<?php
				break;


			case 'checkbox':
				?>
				<p>
					<input id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="checkbox"<?php checked( $value ); ?> />&nbsp;<label for="<?php echo $id; ?>"><?php echo $title; ?></label>
				</p>
				<?php
				break;

			case 'select':
				?>
				<p>
					<label for="<?php echo $id; ?>"><?php echo $title; ?></label>
					<select class="widefat" id="<?php echo $id; ?>" name="<?php echo $name; ?>">
					<?php 
					if( !empty( $options) ){
						foreach ($options as $key => $option) {
							echo '<option value="' . $key . '" ' . selected( $value, $key ) . '>' . $option . '</option>';
						}
					}
					?>
					</select>
				</p>
				<?php
				break;

			case 'date':
				?>
				<p>
					<label for="<?php echo $id; ?>"><?php echo $title; ?></label>
					<input class="widefat wpea_datepicker" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="text" value="<?php echo esc_attr($value); ?>" />
				</p>
				<?php
				break;

			default:
				break;
		}
	}

	/**
	 * Outputs Upcoming WP Events
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 */
	public function wpea_render_upcoming_events( $args, $instance ){
		global $importevents;
		$event_post_type = $importevents->wpea->get_event_posttype();
		$event_taxonomy = $importevents->wpea->get_taxonomy();
		//Default_options
		$posts_per_page = 10; 
		$display_style = 'style1';
		$is_display_image = $is_display_location = $is_display_enddate = $is_display_desc = $is_new_window = false;

		if( isset( $instance['max_events'] ) && $instance['max_events'] != '' ){
			$posts_per_page = esc_attr( $instance["max_events"] );
			if( !is_numeric( $posts_per_page ) ){
				$posts_per_page = 10;
			}
		}
		if(isset( $instance['display_style'] ) && $instance['display_style'] != '' ){
			$display_style = esc_attr( $instance['display_style']);
		}
		if( $instance['display_event_image'] ){
			$is_display_image = true;
		}
		if( $instance['display_event_location'] ){
			$is_display_location = true;
		}
		if( $instance['display_event_enddate'] ){
			$is_display_enddate = true;
		}
		if( $instance['display_event_desc'] ){
			$is_display_desc = true;
		}
		if( isset( $instance['new_window'] ) && $instance['new_window'] != 0 ){
			$is_new_window = true;
		}
		$wpea_args = array(
			'post_type'   	 => $event_post_type,
			'post_status' 	 => 'publish',
			'posts_per_page' => $posts_per_page,
			'meta_key'  	 => 'start_ts',
			'orderby'   	 => 'meta_value_num',
			'order' 		 => 'ASC'
		);

		$start_date_str = time();
		$end_date_str = '';

		if(isset( $instance['start_date'] ) && $instance['start_date'] != '' ){
			$start_date_str = strtotime( $instance['start_date'] );
		}
		if(isset( $instance['end_date'] ) && $instance['end_date'] != '' ){
			$end_date_str =  strtotime( $instance['end_date'] );
		}

		$wpea_args['meta_query'] = array(
			array(
				'key'     => 'start_ts',
				'value'   => $start_date_str,
				'compare' => '>=',
			),
		);

		if( $end_date_str != '' ){
			$wpea_args['meta_query'][] = array(
				'key'     => 'end_ts',
				'value'   => $end_date_str,
				'compare' => '<=',
			);
			$wpea_args['meta_query']['relation'] = 'AND';
		}

		if( !empty( $instance['event_cats'] ) ){
			$wpea_args['tax_query'] = array(
				array(
					'taxonomy' => $event_taxonomy,
					'field'    => 'term_id',
					'terms'    => $instance['event_cats'],
					'operator' => 'IN',
				)
			);

		}
		 
		$wpea_events = new WP_Query( $wpea_args );
		$wp_list_events = '';
		/* Start the Loop */
		ob_start();
		?>
		<div class="wpea_event_listing_widget">
			<?php
			if( $wpea_events->have_posts() ):
				while ( $wpea_events->have_posts() ) : $wpea_events->the_post();
					
					$event_id = get_the_ID();
					$event_start_str = get_post_meta( $event_id, 'start_ts', true );
					$event_end_str = get_post_meta( $event_id, 'end_ts', true );
					$event_address = get_post_meta( $event_id, 'venue_name', true );
					$venue_address = get_post_meta( $event_id, 'venue_address', true );
					if( $event_address != '' && $venue_address != '' ){
						$event_address .= ' - '.$venue_address;
					}elseif( $venue_address != '' ){
						$event_address = $venue_address;
					}

					$event_date = date_i18n('F j (h:i a)', $event_start_str );
					if( $is_display_enddate ){
						if( $event_start_str != $event_end_str ){
							if( date_i18n('Y-m-d', $event_start_str ) == date_i18n('Y-m-d', $event_end_str ) ){
								$event_date = date_i18n('F j', $event_start_str ) .' ('. date_i18n('h:i a', $event_start_str ) . ' - '. date_i18n('h:i a', $event_end_str ) .')';
							} else {
								$event_date = date_i18n('F j (h:i a)', $event_start_str ) . ' - ' . date_i18n('F j (h:i a)', $event_end_str );
							}
						}
					}
					if( file_exists( WPEAPRO_PLUGIN_DIR . '/templates/wpea-widget-' . $display_style . '.php' ) ){

						include WPEAPRO_PLUGIN_DIR . '/templates/wpea-widget-' . $display_style . '.php';
					} else {
						include WPEAPRO_PLUGIN_DIR . '/templates/wpea-widget-style1.php';
					}					
				endwhile; // End of the loop.
			endif;
			?>
		</div>
		<?php
		do_action( 'wpea_after_widget_event_list', $wpea_events );
		$wp_list_events = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();
		echo $wp_list_events;
	}

	/**
	 * Render event Terms Multi select 
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function render_event_taxonomy_terms( $name, $value, $title ) {
		global $importevents;
		if( is_null( $value ) ){ $value = array(); }
		
		$name = $this->get_field_name( $name );
		$id = $this->get_field_id( $name );
		$terms = array();
		$event_taxonomy = $importevents->wpea->get_taxonomy();
		if ( $event_taxonomy != '' ) {
			if( taxonomy_exists( $event_taxonomy ) ){
				$terms = get_terms( $event_taxonomy );
			}
		}
		if( ! empty( $terms ) ){ ?>
			<p>
			<label for="<?php echo $id; ?>"><?php echo $title; ?></label>
			<select name="<?php echo $name;?>" id="<?php echo $id;?>" multiple="multiple" class="widefat" >
		        <?php foreach ($terms as $term ) { ?>
					<option value="<?php echo $term->term_id; ?>" <?php if( in_array( $term->term_id, $value ) ){ echo 'selected="selected"'; } ?> >
	                	<?php echo $term->name; ?>                                	
	                </option>
				<?php } ?> 
			</select>
			</p>
			<?php
		}
	}
}
