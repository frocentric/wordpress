<?php
/**
 * This file handles the Hook Element.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Execute our hook elements.
 *
 * @since 1.7
 */
class GeneratePress_Hook {

	/**
	 * Set our content variable.
	 *
	 * @since 1.7
	 * @var string The content.
	 */
	protected $content = '';

	/**
	 * Set our hook/action variable.
	 *
	 * @since 1.7
	 * @var string The hook.
	 */
	protected $hook = '';

	/**
	 * Set our custom hook variable.
	 *
	 * @since 1.7
	 * @var string The custom hook.
	 */
	protected $custom_hook = '';

	/**
	 * Set our disable site header variable.
	 *
	 * @since 1.7
	 * @var boolean Whether we're disabling the header.
	 */
	protected $disable_site_header = false;

	/**
	 * Set our disable footer variable.
	 *
	 * @since 1.7
	 * @var boolean Whether we're disabling the footer.
	 */
	protected $disable_site_footer = false;

	/**
	 * Set our priority variable.
	 *
	 * @since 1.7
	 * @var int The hook priority.
	 */
	protected $priority = 10;

	/**
	 * Set our execute PHP variable.
	 *
	 * @since 1.7
	 * @var boolean Whether we're executing PHP.
	 */
	protected $php = false;

	/**
	 * Set our execute shortcodes variable.
	 *
	 * @since 1.7
	 * @var boolean Whether we're executing shortcodes.
	 */
	protected $shortcodes = false;

	/**
	 * Set our location variable.
	 *
	 * @since 1.7
	 * @var array The conditions.
	 */
	protected $conditional = array();

	/**
	 * Set our exclusions variable.
	 *
	 * @since 1.7
	 * @var array The exclusions.
	 */
	protected $exclude = array();

	/**
	 * Set our user condition variable.
	 *
	 * @since 1.7
	 * @var array The user roles.
	 */
	protected $users = array();

	/**
	 * Set up our class and give variables their values.
	 *
	 * @param int $post_id The post ID of the element we're executing.
	 *
	 * @since 1.7
	 */
	public function __construct( $post_id ) {

		$this->hook = get_post_meta( $post_id, '_generate_hook', true );

		if ( empty( $this->hook ) ) {
			return;
		}

		$this->content = get_post_meta( $post_id, '_generate_element_content', true );

		if ( get_post_meta( $post_id, '_generate_custom_hook', true ) ) {
			$this->custom_hook = get_post_meta( $post_id, '_generate_custom_hook', true );
		}

		if ( get_post_meta( $post_id, '_generate_hook_disable_site_header', true ) ) {
			$this->disable_site_header = get_post_meta( $post_id, '_generate_hook_disable_site_header', true );
		}

		if ( get_post_meta( $post_id, '_generate_hook_disable_site_footer', true ) ) {
			$this->disable_site_footer = get_post_meta( $post_id, '_generate_hook_disable_site_footer', true );
		}

		if ( get_post_meta( $post_id, '_generate_hook_priority', true ) || '0' === get_post_meta( $post_id, '_generate_hook_priority', true ) ) {
			$this->priority = get_post_meta( $post_id, '_generate_hook_priority', true );
		}

		if ( get_post_meta( $post_id, '_generate_hook_execute_php', true ) ) {
			$this->php = get_post_meta( $post_id, '_generate_hook_execute_php', true );
		}

		if ( get_post_meta( $post_id, '_generate_hook_execute_shortcodes', true ) ) {
			$this->shortcodes = get_post_meta( $post_id, '_generate_hook_execute_shortcodes', true );
		}

		if ( get_post_meta( $post_id, '_generate_element_display_conditions', true ) ) {
			$this->conditional = get_post_meta( $post_id, '_generate_element_display_conditions', true );
		}

		if ( get_post_meta( $post_id, '_generate_element_exclude_conditions', true ) ) {
			$this->exclude = get_post_meta( $post_id, '_generate_element_exclude_conditions', true );
		}

		if ( get_post_meta( $post_id, '_generate_element_user_conditions', true ) ) {
			$this->users = get_post_meta( $post_id, '_generate_element_user_conditions', true );
		}

		if ( 'custom' === $this->hook && $this->custom_hook ) {
			$this->hook = $this->custom_hook;
		}

		$display = apply_filters( 'generate_hook_element_display', GeneratePress_Conditions::show_data( $this->conditional, $this->exclude, $this->users ), $post_id );

		/**
		 * Simplify filter name.
		 *
		 * @since 2.0.0
		 */
		$display = apply_filters(
			'generate_element_display',
			$display,
			$post_id
		);

		if ( $display ) {
			global $generate_elements;

			$generate_elements[ $post_id ] = array(
				'is_block_element' => false,
				'type' => 'hook',
				'id' => $post_id,
			);

			if ( 'generate_header' === $this->hook && $this->disable_site_header ) {
				remove_action( 'generate_header', 'generate_construct_header' );
			}

			if ( 'generate_footer' === $this->hook && $this->disable_site_footer ) {
				remove_action( 'generate_footer', 'generate_construct_footer' );
				add_filter( 'generate_footer_widgets', '__return_null' );
			}

			add_action( esc_attr( $this->hook ), array( $this, 'execute_hook' ), absint( $this->priority ) );
		}

	}

	/**
	 * Output our hook content.
	 *
	 * @since 1.7
	 */
	public function execute_hook() {

		$content = $this->content;

		if ( $this->shortcodes ) {
			$content = do_shortcode( $content );
		}

		if ( $this->php && GeneratePress_Elements_Helper::should_execute_php() ) {
			ob_start();
			eval( '?>' . $content . '<?php ' ); // phpcs:ignore -- Using eval() to execute PHP.
			echo ob_get_clean(); // phpcs:ignore -- Escaping not necessary.
		} else {
			echo $content; // phpcs:ignore -- Escaping not necessary.
		}

	}

}
