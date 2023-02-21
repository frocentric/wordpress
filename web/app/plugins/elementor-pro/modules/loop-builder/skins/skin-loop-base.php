<?php
namespace ElementorPro\Modules\LoopBuilder\Skins;

use Elementor\Core\Files\CSS\Post;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Files\CSS\Post_Preview;
use Elementor\Icons_Manager;
use ElementorPro\Modules\LoopBuilder\Documents\Loop;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use ElementorPro\Modules\LoopBuilder\Module;
use ElementorPro\Modules\LoopBuilder\Widgets\Base as Loop_Widget_Base;
use ElementorPro\Modules\Posts\Skins\Skin_Base;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Related;
use ElementorPro\Plugin;
use ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Dynamic_CSS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Loop Base
 *
 * Base Skin for Loop widgets.
 *
 * @since 3.8.0
 */
class Skin_Loop_Base extends Skin_Base {

	public function get_id() {
		return MODULE::LOOP_BASE_SKIN_ID;
	}

	public function get_title() {
		return esc_html__( 'Loop Base', 'elementor-pro' );
	}

	/**
	 * Register Query Controls
	 *
	 * Registers the controls for the query used by the Loop.
	 *
	 * @since 3.8.0
	 */
	public function register_query_controls( Loop_Widget_Base $widget ) {
		$this->parent = $widget;

		$this->add_group_control(
			Group_Control_Related::get_type(),
			[
				'name' => Module::QUERY_ID,
				'presets' => [ 'full' ],
				'exclude' => [
					'posts_per_page', // Use the one from Layout section
				],
			]
		);
	}

	private function maybe_add_load_more_wrapper_class() {
		$settings = $this->parent->get_settings_for_display();
		/** @var Loop_Widget_Base $widget */
		$widget = $this->parent;

		if ( isset( $settings['pagination_type'] ) && 'load_more_on_click' === $settings['pagination_type'] ) {
			// If Pagination is enabled with the Load More On Click option, a class is needed for targeting.
			// The 'wrapper' element tag is used by the Button Widget Trait.
			$widget->add_render_attribute( 'wrapper', 'class', 'e-loop__load-more' );
		}
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();
		$is_edit_mode = Plugin::elementor()->editor->is_edit_mode();
		/** @var Loop_Widget_Base $widget */
		$widget = $this->parent;

		if ( ! empty( $settings['template_id'] ) ) {
			$this->maybe_add_load_more_wrapper_class();

			$widget->before_skin_render();

			parent::render();

			$widget->after_skin_render();
		} else if ( $is_edit_mode ) {
			$this->render_empty_view();
		}
	}

	protected function get_loop_header_widget_classes() {
		/** @var Loop_Widget_Base $widget */
		$widget = $this->parent;

		$classes = $widget->get_loop_header_widget_classes();

		$classes[] = 'elementor-loop-container';

		return $classes;
	}

	protected function _register_controls_actions() {
		add_action( 'elementor/element/' . $this->parent->get_name() . '/section_query/after_section_start', [ $this, 'register_query_controls' ] );
	}

	/**
	 * Render Post
	 *
	 * Uses the chosen custom template to render Loop posts.
	 *
	 * @since 3.8.0
	 */
	protected function render_post() {
		$settings = $this->parent->get_settings_for_display();
		$loop_item_id = get_the_ID();

		/** @var LoopDocument $document */
		$document = Plugin::elementor()->documents->get( $settings['template_id'] );

		if ( ! $document ) {
			return;
		}

		$this->print_dynamic_css( $loop_item_id, $settings['template_id'] );
		$document->print_content();
	}

	protected function print_dynamic_css( $post_id, $post_id_for_data ) {
		$document = Plugin::elementor()->documents->get_doc_for_frontend( $post_id_for_data );

		if ( ! $document ) {
			return;
		}

		Plugin::elementor()->documents->switch_to_document( $document );

		$css_file = Loop_Dynamic_CSS::create( $post_id, $post_id_for_data );
		$post_css = $css_file->get_content();

		if ( empty( $post_css ) ) {
			return;
		}

		$css = '';
		$css = str_replace( '.elementor-' . $post_id, '.e-loop-item-' . $post_id, $post_css );
		$css = sprintf( '<style id="%s">%s</style>', 'loop-dynamic-' . $post_id_for_data, $css );

		echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		Plugin::elementor()->documents->restore_document();
	}

	protected function render_loop_header() {
		/** @var Loop_Widget_Base $widget */
		$widget = $this->parent;
		$config = $widget->get_config();

		if ( $config['add_parent_render_header'] ) {
			parent::render_loop_header();
		}

		$widget->render_loop_header();
	}

	protected function render_loop_footer() {
		/** @var Loop_Widget_Base $widget */
		$widget = $this->parent;
		$config = $widget->get_config();

		if ( $config['add_parent_render_footer'] ) {
			parent::render_loop_footer();
		}

		$widget->render_loop_footer();
	}

	/**
	 * Render Empty View
	 *
	 * Renders the Loop widget's view if there is no default template (empty view).
	 *
	 * @since 3.8.0
	 */
	protected function render_empty_view() {
		?>
		<div class="e-loop-empty-view__wrapper"><!-- Will be filled with JS --></div>
		<?php
	}
}
