<?php
use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use ElementorPro\Plugin;

/**
 * Register feedzy elementor widget.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/elementor
 */
class Widget_Feedzy_Loop extends Elementor\Widget_Base {

	/**
	 * Widget name.
	 */
	public function get_name() {
		return 'feedzy-loop';
	}

	/**
	 * Widget title.
	 */
	public function get_title() {
		return __( 'Feedzy Loop', 'feedzy-rss-feeds' );
	}

	/**
	 * Widget icon.
	 */
	public function get_icon() {
		return 'dashicons dashicons-rss';
	}

	/**
	 * Widget search keywords.
	 */
	public function get_keywords() {
		return array( 'elementor', 'template', 'feed', 'rss', 'feedzy' );
	}

	/**
	 * Widget register controls.
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->start_controls_section(
			'config_feedzy_feeds',
			array(
				'label' => __( 'Config Feedzy RSS Feeds', 'feedzy-rss-feeds' ),
			)
		);

		$this->add_control(
			'feeds',
			array(
				'label' => __( 'Feed Source', 'feedzy-rss-feeds' ),
				'label_block' => true,
				'type'  => Controls_Manager::TEXT,
				'placeholder' => __( 'Feed Source', 'feedzy-rss-feeds' ),
				'description' => __( 'RSS Feed sources (comma separated URLs or Feed Categories slug).', 'feedzy-rss-feeds' ),
				'classes' => 'el-feedzy-feed-source',
			)
		);

		$this->add_control(
			'max',
			array(
				'label_block' => true,
				'label' => __( 'Number of items to display.', 'feedzy-rss-feeds' ),
				'type'  => Controls_Manager::TEXT,
				'placeholder' => __( '(eg: 5)', 'feedzy-rss-feeds' ),
				'default' => 5,
			)
		);

		$this->add_control(
			'offset',
			array(
				'label_block' => true,
				'label' => __( 'Ignore the first N items of the feed.', 'feedzy-rss-feeds' ),
				'type'  => Controls_Manager::TEXT,
				'placeholder' => __( '(eg: 5)', 'feedzy-rss-feeds' ),
				'description' => __( 'eg: 5, if you want to start from the 6th item.', 'feedzy-rss-feeds' ),
				'default' => 0,
			)
		);

		$this->add_control(
			'summarylength',
			array(
				'label_block' => true,
				'label' => __( 'Summary Length.', 'feedzy-rss-feeds' ),
				'type'  => Controls_Manager::TEXT,
				'placeholder' => __( '(eg: 160)', 'feedzy-rss-feeds' ),
				'description' => __( 'Crop description (summary) of the element after X characters.', 'feedzy-rss-feeds' ),
				'default' => '',
			)
		);

		$this->add_control(
			'refresh',
			array(
				'label_block' => true,
				'label' => __( 'For how long we will cache the feed results.', 'feedzy-rss-feeds' ),
				'type'  => Controls_Manager::SELECT,
				'default' => '12_hours',
				'description' => __( '(eg: 1_days, defaults: 12_hours)', 'feedzy-rss-feeds' ),
				'options' => array(
					'1_hours'  => wp_sprintf( '1 %1$s', __( 'Hour', 'feedzy-rss-feeds' ) ),
					'3_hours'  => wp_sprintf( '3 %1$s', __( 'Hour', 'feedzy-rss-feeds' ) ),
					'12_hours' => wp_sprintf( '12 %1$s', __( 'Hour', 'feedzy-rss-feeds' ) ),
					'1_days'   => wp_sprintf( '1 %1$s', __( 'Day', 'feedzy-rss-feeds' ) ),
					'3_days'   => wp_sprintf( '3 %1$s', __( 'Days', 'feedzy-rss-feeds' ) ),
					'15_days'  => wp_sprintf( '12 %1$s', __( 'Days', 'feedzy-rss-feeds' ) ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'feedzy_section_template',
			array(
				'label' => __( 'Template', 'feedzy-rss-feeds' ),
			)
		);

		$this->add_control(
			'feedzy_template_id',
			array(
				'label' => __( 'Choose Template', 'feedzy-rss-feeds' ),
				'type' => QueryControlModule::QUERY_CONTROL_ID,
				'label_block' => true,
				'autocomplete' => array(
					'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => array( 'feedzy-loop' ),
								'compare' => 'IN',
							),
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$feedzy_template_id = $this->get_settings( 'feedzy_template_id' );

		if ( 'publish' !== get_post_status( $feedzy_template_id ) ) {
			return;
		}

		$source = $this->get_settings( 'feeds' );
		$max = $this->get_settings( 'max' );
		$offset = $this->get_settings( 'offset' );
		$summarylength = $this->get_settings( 'summarylength' );

		$options = array(
			'feeds'   => $source,
			'max'     => $max ? $max : 5,
			'offset'  => $offset ? $offset : 0,
			'refresh' => $this->get_settings( 'refresh' ),
			'feed_title' => 'no',
			'target'         => '',
			'title'          => '',
			'meta'           => 'yes',
			'summary'        => 'yes',
			'summarylength'  => ! empty( $summarylength ) ? $summarylength : '',
			'thumb'          => 'auto',
			'size'    => '250',
			'default' => '',
			'keywords_title' => '',
			'keywords_ban'   => '',
			'columns'        => 1,
			'multiple_meta'  => 'no',
		);

		$template_content = Plugin::elementor()->frontend->get_builder_content_for_display( $feedzy_template_id );

		$admin = new \Feedzy_Rss_Feeds_Import( 'feedzy-rss-feeds', Feedzy_Rss_Feeds::get_version() );
		$feed_items = $admin->get_job_feed( $options, $template_content );

		$error_message = __( 'Feed has no items.', 'feedzy-rss-feeds' );
		if ( empty( $source ) ) {
			$error_message = __( 'Please enter a valid feed URL/feed categories slug in feed source setting.', 'feedzy-rss-feeds' );
		}

		?>
		<div class="elementor-template">
			<?php
			if ( ! empty( $feed_items ) ) {
				$feed_content = '';
				foreach ( $feed_items as $item ) {
					$item_date = gmdate( get_option( 'date_format' ) . ' at ' . get_option( 'time_format' ), $item['item_date'] );
					$item_url = $item['item_url'];
					$item_description = wp_kses_post( feedzy_feed_item_desc( $item ) );
					// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					$item_date = date( get_option( 'date_format' ) . ' at ' . get_option( 'time_format' ), $item['item_date'] );
					$item_date = $item['item_date_formatted'];

					// Default image tag.
					$item_img = FEEDZY_ABSURL . '/img/feedzy.svg?tag=[#item_img]';

					$author = '';
					if ( $item['item_author'] ) {
						if ( is_string( $item['item_author'] ) ) {
							$author = $item['item_author'];
						} elseif ( is_object( $item['item_author'] ) ) {
							$author = $item['item_author']->get_name();
							if ( empty( $author ) ) {
								$author = $item['item_author']->get_email();
							}
						}
					}
					$feed_content .= str_replace(
						array(
							'[#item_url]',
							esc_url( '[#item_url]' ),
							'[#item_title]',
							$item_img,
							'[#item_date]',
							'[#item_date_local]',
							'[#item_date_feed]',
							'[#item_author]',
							'[#item_description]',
							'[#item_content]',
							'[#item_source]',
						),
						array(
							$item_url,
							$item_url,
							$item['item_title'],
							! empty( $item['item_img_path'] ) ? $item['item_img_path'] : $item_img,
							$item_date,
							$item_date,
							$item_date,
							$author,
							$item['item_description'],
							$item_description,
							$item['item_source'],
						),
						$template_content
					);
				}
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $feed_content;
			} else {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $error_message;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render content template.
	 */
	protected function content_template() {}

	/**
	 * Render plain content.
	 *
	 * @param object $instance Field instance.
	 */
	public function render_plain_content( $instance = array() ) {}
}
