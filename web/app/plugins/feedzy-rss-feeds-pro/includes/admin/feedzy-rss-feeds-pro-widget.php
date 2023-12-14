<?php
/**
 * The PRO Widget functionality of the plugin.
 *
 * @link       http://themeisle.com
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 */

/**
 * The PRO Widget functionality of the plugin.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 * @author     Themeisle <friends@themeisle.com>
 */
class Feedzy_Rss_Feeds_Pro_Widget {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @param   string $plugin_name    The name of this plugin.
	 * @param   string $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Updates the form for the widget.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @param   string $widget_form The widget form.
	 * @param   array  $instance    The instance.
	 * @param   array  $defaults    The defaults.
	 * @return string
	 */
	public function feedzy_pro_form_widget( $widget_form, $instance, $defaults ) {

		return $widget_form;
	}

	/**
	 *
	 * The update method filter hook method
	 *
	 * @since   1.0.0
	 * @access  public
	 * @param   array $instance     The current widget instance.
	 * @param   array $new_instance The new widget instance.
	 * @return array
	 */
	public function feedzy_pro_widget_update( $instance, $new_instance ) {
		$instance                 = $instance;
		if ( isset( $new_instance['referral_url'] ) ) {
			$instance['referral_url'] = strip_tags( $new_instance['referral_url'] );
		}
		if ( isset( $new_instance['price'] ) ) {
			$instance['price']        = strip_tags( $new_instance['price'] );
		}
		if ( isset( $new_instance['template'] ) ) {
			$instance['template']     = strip_tags( $new_instance['template'] );
		}
		if ( isset( $new_instance['columns'] ) ) {
			$instance['columns']      = strip_tags( $new_instance['columns'] );
		}

		return $instance;
	}

	/**
	 *
	 * The widget function
	 *
	 * @since   1.0.0
	 * @access  public
	 * @param   array $feedzy_widget_shortcode_attributes     The shortcode attributes.
	 * @param   array $args                                   The args to use.
	 * @param   array $instance                               The widget instance.
	 * @return array
	 */
	public function feedzy_pro_widget_shortcode_attributes( $feedzy_widget_shortcode_attributes, $args, $instance ) {

		$feedzy_widget_shortcode_attributes['referral_url'] = isset( $instance['referral_url'] ) ? $instance['referral_url'] : '';
		$feedzy_widget_shortcode_attributes['price']        = isset( $instance['price'] ) ? $instance['price'] : '';
		$feedzy_widget_shortcode_attributes['template']     = isset( $instance['template'] ) ? $instance['template'] : 'default';
		$feedzy_widget_shortcode_attributes['columns']      = isset( $instance['columns'] ) ? $instance['columns'] : '1';
		$feedzy_widget_shortcode_attributes['mapping']      = isset( $instance['mapping'] ) ? $instance['mapping'] : '';

		return $feedzy_widget_shortcode_attributes;

	}

}
