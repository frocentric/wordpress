<?php
/**
 * This file handles the dynamic aspects of Block Elements.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Dynamic blocks.
 */
class GeneratePress_Block_Elements {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Initiator.
	 *
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Build it.
	 */
	public function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_assets' ), 8 );

		if ( version_compare( $GLOBALS['wp_version'], '5.8', '<' ) ) {
			add_filter( 'block_categories', array( $this, 'add_block_category' ) );
		} else {
			add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );
		}

		add_action( 'init', array( $this, 'register_dynamic_blocks' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
		add_filter( 'render_block', array( $this, 'render_blocks' ), 10, 2 );
		add_filter( 'generateblocks_background_image_url', array( $this, 'set_background_image_url' ), 10, 2 );
		add_filter( 'generateblocks_attr_container', array( $this, 'set_container_attributes' ), 10, 2 );
		add_filter( 'generateblocks_defaults', array( $this, 'set_defaults' ) );
		add_action( 'generateblocks_block_css_data', array( $this, 'generate_css' ), 10, 7 );
		add_filter( 'generateblocks_attr_container', array( $this, 'set_dynamic_container_url' ), 15, 2 );
		add_filter( 'generateblocks_attr_container-link', array( $this, 'set_dynamic_container_url' ), 15, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_css' ), 100 );
	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue_assets() {
		if ( 'gp_elements' !== get_post_type() ) {
			return;
		}

		wp_enqueue_script(
			'gp-premium-block-elements',
			GP_PREMIUM_DIR_URL . 'dist/block-elements.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			filemtime( GP_PREMIUM_DIR_PATH . 'dist/block-elements.js' ),
			true
		);

		wp_set_script_translations( 'gp-premium-block-elements', 'gp-premium', GP_PREMIUM_DIR_PATH . 'langs' );

		$taxonomies = get_taxonomies(
			apply_filters(
				'generate_get_block_element_taxonomies_args',
				array(
					'public' => true,
				)
			)
		);

		$parent_elements = get_posts(
			array(
				'post_type'     => 'gp_elements',
				'post_parent'   => 0,
				'no_found_rows' => true,
				'post_status'   => 'publish',
				'numberposts'   => 100,
				'fields'        => 'ids',
				'exclude'       => array( get_the_ID() ),
				'meta_query' => array(
					array(
						'key' => '_generate_block_type',
						'value' => 'content-template',
						'compare' => '=',
					),
				),
			)
		);

		$parent_elements_data = array();

		foreach ( (array) $parent_elements as $element ) {
			$parent_elements_data[] = array(
				'label' => get_the_title( $element ),
				'id' => $element,
			);
		}

		$image_sizes = get_intermediate_image_sizes();
		$image_sizes = array_diff( $image_sizes, array( '1536x1536', '2048x2048' ) );
		$image_sizes[] = 'full';

		$containerWidth = function_exists( 'generate_get_option' ) ? generate_get_option( 'container_width' ) : 1100;
		$rightSidebarWidth = apply_filters( 'generate_right_sidebar_width', '25' );
		$leftSidebarWidth = apply_filters( 'generate_left_sidebar_width', '25' );

		$containerWidth = floatval( $containerWidth );
		$leftSidebarWidth = '0.' . $leftSidebarWidth;
		$rightSidebarWidth = '0.' . $rightSidebarWidth;

		$leftSidebarWidth = $containerWidth - ( $containerWidth * $leftSidebarWidth );
		$rightSidebarWidth = $containerWidth - ( $containerWidth * $rightSidebarWidth );

		$leftSidebarWidth = $containerWidth - $leftSidebarWidth;
		$rightSidebarWidth = $containerWidth - $rightSidebarWidth;

		$contentWidth = $containerWidth - $rightSidebarWidth;

		wp_localize_script(
			'gp-premium-block-elements',
			'gpPremiumBlockElements',
			array(
				'isBlockElement' => 'gp_elements' === get_post_type(),
				'taxonomies' => $taxonomies,
				'rightSidebarWidth' => $rightSidebarWidth,
				'leftSidebarWidth' => $leftSidebarWidth,
				'contentWidth' => $contentWidth,
				'hooks' => GeneratePress_Elements_Helper::get_available_hooks(),
				'excerptLength' => apply_filters( 'excerpt_length', 55 ), // phpcs:ignore -- Core filter.
				'isGenerateBlocksActive' => function_exists( 'generateblocks_load_plugin_textdomain' ),
				'isGenerateBlocksInstalled' => file_exists( WP_PLUGIN_DIR . '/generateblocks/plugin.php' ) ? true : false,
				'isGenerateBlocksProActive' => function_exists( 'generateblocks_pro_init' ),
				'installLink' => wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=generateblocks' ), 'install-plugin_generateblocks' ),
				'activateLink' => wp_nonce_url( 'plugins.php?action=activate&amp;plugin=generateblocks/plugin.php&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_generateblocks/plugin.php' ),
				'imageSizes' => $image_sizes,
				'imageSizeDimensions' => $this->get_image_Sizes(),
				'featuredImagePlaceholder' => GP_PREMIUM_DIR_URL . 'elements/assets/admin/featured-image-placeholder.png',
				'authorImagePlaceholder' => GP_PREMIUM_DIR_URL . 'elements/assets/admin/author-image-placeholder.png',
				'bgImageFallback' => GP_PREMIUM_DIR_URL . 'elements/assets/admin/background-image-fallback.jpg',
				'templateImageUrl' => 'https://gpsites.co/files/element-library',
				'parentElements' => $parent_elements_data,
			)
		);

		wp_enqueue_style(
			'gp-premium-block-elements',
			GP_PREMIUM_DIR_URL . 'dist/block-elements.css',
			array( 'wp-edit-blocks' ),
			filemtime( GP_PREMIUM_DIR_PATH . 'dist/block-elements.css' )
		);
	}

	/**
	 * Add our block category.
	 *
	 * @param array $categories The existing categories.
	 */
	public function add_block_category( $categories ) {
		return array_merge(
			array(
				array(
					'slug'  => 'generatepress',
					'title' => __( 'GeneratePress', 'gp-premium' ),
				),
			),
			$categories
		);
	}

	/**
	 * Register our dynamic blocks.
	 */
	public function register_dynamic_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'generatepress/dynamic-content',
			array(
				'render_callback' => array( $this, 'do_dynamic_content_block' ),
				'attributes' => array(
					'contentType' => array(
						'type' => 'string',
						'default' => '',
					),
					'excerptLength' => array(
						'type' => 'number',
						'default' => apply_filters( 'excerpt_length', 55 ), // phpcs:ignore -- Core filter.
					),
					'useThemeMoreLink' => array(
						'type' => 'boolean',
						'defaut' => true,
					),
					'customMoreLink' => array(
						'type' => 'string',
						'default' => '',
					),
				),
			)
		);

		register_block_type(
			'generatepress/dynamic-image',
			array(
				'render_callback' => array( $this, 'do_dynamic_image_block' ),
				'attributes' => array(
					'imageType' => array(
						'type' => 'string',
						'default' => '',
					),
					'imageSource' => array(
						'type' => 'string',
						'default' => 'current-post',
					),
					'customField' => array(
						'type' => 'string',
						'default' => '',
					),
					'gpDynamicSourceInSameTerm' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'gpDynamicSourceInSameTermTaxonomy' => array(
						'tyoe' => 'string',
						'default' => 'category',
					),
					'imageSize' => array(
						'type' => 'string',
						'default' => 'full',
					),
					'linkTo' => array(
						'type' => 'string',
						'default' => '',
					),
					'linkToCustomField' => array(
						'type' => 'string',
						'default' => '',
					),
					'imageWidth' => array(
						'type' => 'number',
						'default' => null,
					),
					'imageHeight' => array(
						'type' => 'number',
						'default' => null,
					),
					'avatarSize' => array(
						'type' => 'number',
						'default' => 30,
					),
					'avatarRounded' => array(
						'type' => 'boolean',
						'default' => false,
					),
				),
			)
		);
	}

	/**
	 * Do our dynamic content block.
	 *
	 * @param array $attributes The attributes from this block.
	 */
	public function do_dynamic_content_block( $attributes ) {
		if ( empty( $attributes['contentType'] ) ) {
			return;
		}

		if ( 'post-content' === $attributes['contentType'] ) {
			return $this->do_content_block();
		}

		if ( 'post-excerpt' === $attributes['contentType'] ) {
			return $this->do_excerpt_block( $attributes );
		}

		if ( 'term-description' === $attributes['contentType'] ) {
			return sprintf(
				'<div class="dynamic-term-description">%s</div>',
				term_description()
			);
		}

		if ( 'author-description' === $attributes['contentType'] ) {
			return sprintf(
				'<div class="dynamic-author-description">%s</div>',
				get_the_author_meta( 'description' )
			);
		}
	}

	/**
	 * Build our content block.
	 */
	public function do_content_block() {
		// Prevents infinite loops while in the editor or autosaving.
		$nonpublic_post_types = array(
			'gp_elements',
			'revision',
		);

		if ( ! in_array( get_post_type(), $nonpublic_post_types ) && ! is_admin() ) {
			return sprintf(
				'<div class="dynamic-entry-content">%s</div>',
				apply_filters( 'the_content', str_replace( ']]>', ']]&gt;', get_the_content() ) ) // phpcs:ignore -- Core filter.
			);
		}
	}

	/**
	 * Build our excerpt block.
	 *
	 * @param array $attributes The block attributes.
	 */
	public function do_excerpt_block( $attributes ) {
		if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) {
			$filter_excerpt_length = function( $length ) use ( $attributes ) {
				return isset( $attributes['excerptLength'] ) ? $attributes['excerptLength'] : $length;
			};

			add_filter(
				'excerpt_length',
				$filter_excerpt_length,
				100
			);

			if ( isset( $attributes['useThemeMoreLink'] ) && ! $attributes['useThemeMoreLink'] ) {
				$filter_more_text = function() use ( $attributes ) {
					if ( empty( $attributes['customMoreLink'] ) ) {
						return ' ...';
					}

					return apply_filters(
						'generate_excerpt_block_more_output',
						sprintf(
							' ... <a title="%1$s" class="dynamic-read-more read-more" href="%2$s" aria-label="%4$s">%3$s</a>',
							the_title_attribute( 'echo=0' ),
							esc_url( get_permalink( get_the_ID() ) ),
							wp_kses_post( $attributes['customMoreLink'] ),
							sprintf(
								/* translators: Aria-label describing the read more button */
								_x( 'More on %s', 'more on post title', 'gp-premium' ),
								the_title_attribute( 'echo=0' )
							)
						)
					);
				};

				add_filter(
					'excerpt_more',
					$filter_more_text,
					100
				);
			}
		}

		if ( 'gp_elements' === get_post_type() || is_admin() ) {
			$post = get_posts(
				array(
					'post_type' => 'post',
					'numberposts' => 1,
				)
			);

			if ( ! empty( $post[0] ) ) {
				return sprintf(
					'<div class="dynamic-entry-excerpt">%s</div>',
					apply_filters( 'the_excerpt', get_the_excerpt( $post[0]->ID )  )  // phpcs:ignore -- Core filter.
				);
			} else {
				return sprintf(
					'<div class="dynamic-entry-excerpt"><p>%s</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pulvinar ligula augue, quis bibendum tellus scelerisque venenatis. Pellentesque porta nisi mi. In hac habitasse platea dictumst. Etiam risus elit, molestie non volutpat ac, pellentesque sed eros. Nunc leo odio, sodales non tortor at, porttitor posuere dui.</p></div>',
					__( 'This is a placeholder for your content.', 'gp-premium' )
				);
			}
		}

		$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() ); // phpcs:ignore -- Core filter.

		if ( isset( $filter_excerpt_length ) ) {
			remove_filter(
				'excerpt_length',
				$filter_excerpt_length,
				100
			);
		}

		if ( isset( $filter_more_text ) ) {
			remove_filter(
				'excerpt_more',
				$filter_more_text,
				100
			);
		}

		return sprintf(
			'<div class="dynamic-entry-excerpt">%s</div>',
			$excerpt
		);
	}

	/**
	 * Build our dynamic image block.
	 *
	 * @param array $attributes The block attributes.
	 */
	public function do_dynamic_image_block( $attributes ) {
		if ( empty( $attributes['imageType'] ) ) {
			return;
		}

		if ( 'featured-image' === $attributes['imageType'] ) {
			$image_source = ! empty( $attributes['imageSource'] ) ? $attributes['imageSource'] : 'current-post';
			$id = $this->get_source_id( $image_source, $attributes );

			if ( ! $id ) {
				return;
			}

			if ( has_post_thumbnail( $id ) ) {
				$size = ! empty( $attributes['imageSize'] ) ? $attributes['imageSize'] : 'full';
				$featured_image_classes = array( 'dynamic-featured-image' );

				if ( ! empty( $attributes['className'] ) ) {
					$featured_image_classes[] = $attributes['className'];
				}

				$featured_image = get_the_post_thumbnail( $id, $size, array( 'class' => implode( ' ', $featured_image_classes ) ) );

				// We can't alter the width/height generated by get_the_post_thumbnail(), so we need to resort to this.
				if ( ! empty( $attributes['imageWidth'] ) ) {
					$featured_image = preg_replace( '/width=[\"\'][0-9]+[\"\']/i', 'width="' . absint( $attributes['imageWidth'] ) . '"', $featured_image );
				}

				if ( ! empty( $attributes['imageHeight'] ) ) {
					$featured_image = preg_replace( '/height=[\"\'][0-9]+[\"\']/i', 'height="' . absint( $attributes['imageHeight'] ) . '"', $featured_image );
				}

				if ( $featured_image ) {
					if ( ! empty( $attributes['linkTo'] ) ) {
						if ( 'single-post' === $attributes['linkTo'] ) {
							$featured_image = sprintf(
								'<a href="%s">%s</a>',
								esc_url( get_permalink( $id ) ),
								$featured_image
							);
						}

						if ( 'custom-field' === $attributes['linkTo'] ) {
							$custom_field = get_post_meta( $id, $attributes['linkToCustomField'], true );

							if ( $custom_field ) {
								$featured_image = sprintf(
									'<a href="%s">%s</a>',
									esc_url( $custom_field ),
									$featured_image
								);
							}
						}
					}

					return $featured_image;
				}
			}
		}

		if ( 'post-meta' === $attributes['imageType'] ) {
			$image_source = ! empty( $attributes['imageSource'] ) ? $attributes['imageSource'] : 'current-post';
			$id = $this->get_source_id( $image_source, $attributes );

			if ( ! $id ) {
				return;
			}

			$image_field_name = ! empty( $attributes['customField'] ) ? $attributes['customField'] : '';

			if ( $image_field_name ) {
				$image = get_post_meta( $id, $image_field_name, true );

				if ( ctype_digit( $image ) ) {
					$size = ! empty( $attributes['imageSize'] ) ? $attributes['imageSize'] : 'full';
					$image_output = wp_get_attachment_image( $image, $size, false, array( 'class' => 'dynamic-meta-image' ) );

					// We can't alter the width/height generated by get_the_post_thumbnail(), so we need to resort to this.
					if ( ! empty( $attributes['imageWidth'] ) ) {
						$image_output = preg_replace( '/width=[\"\'][0-9]+[\"\']/i', 'width="' . absint( $attributes['imageWidth'] ) . '"', $image_output );
					}

					if ( ! empty( $attributes['imageHeight'] ) ) {
						$image_output = preg_replace( '/height=[\"\'][0-9]+[\"\']/i', 'height="' . absint( $attributes['imageHeight'] ) . '"', $image_output );
					}
				} else {
					$image_output = apply_filters(
						'generate_dynamic_custom_field_image',
						sprintf(
							'<img src="%1$s" class="dynamic-meta-image" alt="" width="%2$s" height="%3$s" />',
							$image,
							! empty( $attributes['imageWidth'] ) ? absint( $attributes['imageWidth'] ) : '',
							! empty( $attributes['imageHeight'] ) ? absint( $attributes['imageHeight'] ) : ''
						)
					);
				}

				if ( ! empty( $image_output ) ) {
					if ( ! empty( $attributes['linkTo'] ) ) {
						if ( 'single-post' === $attributes['linkTo'] ) {
							$image_output = sprintf(
								'<a href="%s">%s</a>',
								esc_url( get_permalink( $id ) ),
								$image_output
							);
						}

						if ( 'custom-field' === $attributes['linkTo'] ) {
							$custom_field = get_post_meta( $id, $attributes['linkToCustomField'], true );

							if ( $custom_field ) {
								$image_output = sprintf(
									'<a href="%s">%s</a>',
									esc_url( $custom_field ),
									$image_output
								);
							}
						}
					}

					return $image_output;
				}
			}
		}

		if ( 'author-avatar' === $attributes['imageType'] ) {
			global $post;
			$author_id = $post->post_author;
			$size = ! empty( $attributes['avatarSize'] ) ? $attributes['avatarSize'] : 30;
			$image_alt = apply_filters( 'generate_dynamic_author_image_alt', __( 'Photo of author', 'gp-premium' ) );

			$classes = array(
				'dynamic-author-image',
			);

			if ( ! empty( $attributes['avatarRounded'] ) ) {
				$classes[] = 'dynamic-author-image-rounded';
			}

			$avatar = get_avatar(
				$author_id,
				$size,
				'',
				esc_attr( $image_alt ),
				array(
					'class' => implode( ' ', $classes ),
				)
			);

			if ( $avatar ) {
				return $avatar;
			}
		}
	}

	/**
	 * Get our dynamic URL.
	 *
	 * @param string $link_type The kind of link to add.
	 * @param string $source The source of the dynamic data.
	 * @param array  $block The block we're working with.
	 */
	public function get_dynamic_url( $link_type, $source, $block ) {
		$id = $this->get_source_id( $source, $block['attrs'] );
		$author_id = $this->get_author_id( $source, $block['attrs'] );
		$url = '';

		if ( 'single-post' === $link_type ) {
			$url = get_permalink( $id );
		}

		if ( isset( $block['attrs']['gpDynamicLinkCustomField'] ) ) {
			if ( 'post-meta' === $link_type ) {
				$url = get_post_meta( $id, $block['attrs']['gpDynamicLinkCustomField'], true );
			}

			if ( 'user-meta' === $link_type ) {
				$url = $this->get_user_data( $author_id, $block['attrs']['gpDynamicLinkCustomField'] );
			}

			if ( 'term-meta' === $link_type ) {
				$url = get_term_meta( get_queried_object_id(), $block['attrs']['gpDynamicLinkCustomField'], true );
			}
		}

		if ( 'author-archives' === $link_type ) {
			$url = get_author_posts_url( $author_id );
		}

		if ( 'comments' === $link_type ) {
			$url = get_comments_link( $id );
		}

		if ( 'next-posts' === $link_type ) {
			global $paged, $wp_query;

			$max_page = 0;

			if ( ! $max_page ) {
				$max_page = $wp_query->max_num_pages;
			}

			$paged_num = isset( $paged ) && $paged ? $paged : 1;
			$nextpage = (int) $paged_num + 1;

			if ( ! is_single() && ( $nextpage <= $max_page ) ) {
				$url = next_posts( $max_page, false );
			}
		}

		if ( 'previous-posts' === $link_type ) {
			global $paged;

			if ( ! is_single() && (int) $paged > 1 ) {
				$url = previous_posts( false );
			}
		}

		return apply_filters( 'generate_dynamic_element_url', $url, $link_type, $source, $block );
	}

	/**
	 * Wrap our dynamic text in a link.
	 *
	 * @param string $text The text to wrap.
	 * @param string $link_type The kind of link to add.
	 * @param string $source The source of the dynamic data.
	 * @param array  $block The block we're working with.
	 */
	public function add_dynamic_link( $text, $link_type, $source, $block ) {
		if ( 'generateblocks/headline' === $block['blockName'] ) {
			$url = $this->get_dynamic_url( $link_type, $source, $block );

			if ( ! $url ) {
				return $text;
			}

			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				$text
			);
		}

		if ( 'generateblocks/button' === $block['blockName'] ) {
			$url = $this->get_dynamic_url( $link_type, $source, $block );

			// Since this is a button, we want to scrap the whole block if we don't have a link.
			if ( ! $url ) {
				return '';
			}

			$dynamic_url = sprintf(
				'href="%s"',
				esc_url( $url )
			);

			return str_replace( 'href="#"', $dynamic_url, $text );
		}
	}

	/**
	 * Get user data.
	 *
	 * @since 2.0.0
	 * @param int    $author_id The ID of the user.
	 * @param string $field The field to look up.
	 */
	public function get_user_data( $author_id, $field ) {
		$data = get_user_meta( $author_id, $field, true );

		if ( ! $data ) {
			$user_data_names = array(
				'user_nicename',
				'user_email',
				'user_url',
				'display_name',
			);

			if ( in_array( $field, $user_data_names ) ) {
				$user_data = get_userdata( $author_id );

				if ( $user_data ) {
					switch ( $field ) {
						case 'user_nicename':
							$data = $user_data->user_nicename;
							break;

						case 'user_email':
							$data = $user_data->user_email;
							break;

						case 'user_url':
							$data = $user_data->user_url;
							break;

						case 'display_name':
							$data = $user_data->display_name;
							break;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Add the dynamic bits to our blocks.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block info.
	 */
	public function render_blocks( $block_content, $block ) {
		if ( 'gp_elements' === get_post_type() || is_admin() ) {
			return $block_content;
		}

		if ( 'generateblocks/headline' === $block['blockName'] || 'generateblocks/button' === $block['blockName'] ) {
			if ( ! empty( $block['attrs']['gpDynamicTextType'] ) && ! empty( $block['attrs']['gpDynamicTextReplace'] ) ) {
				$text_to_replace = $block['attrs']['gpDynamicTextReplace'];
				$text_type = $block['attrs']['gpDynamicTextType'];
				$link_type = ! empty( $block['attrs']['gpDynamicLinkType'] ) ? $block['attrs']['gpDynamicLinkType'] : '';
				$source = ! empty( $block['attrs']['gpDynamicSource'] ) ? $block['attrs']['gpDynamicSource'] : 'current-post';
				$id = $this->get_source_id( $source, $block['attrs'] );

				if ( ! $id ) {
					return '';
				}

				if ( 'title' === $text_type ) {
					$post_title = get_the_title( $id );

					if ( ! in_the_loop() ) {
						if ( is_tax() || is_category() || is_tag() ) {
							$post_title = get_queried_object()->name;
						} elseif ( is_post_type_archive() ) {
							$post_title = post_type_archive_title( '', false );
						} elseif ( is_archive() && function_exists( 'get_the_archive_title' ) ) {
							$post_title = get_the_archive_title();

							if ( is_author() ) {
								$post_title = get_the_author();
							}
						} elseif ( is_home() ) {
							$page_for_posts = get_option( 'page_for_posts' );

							if ( ! empty( $page_for_posts ) ) {
								$post_title = get_the_title( $page_for_posts );
							} else {
								$post_title = __( 'Blog', 'gp-premium' );
							}
						}
					}

					$post_title = apply_filters( 'generate_dynamic_element_text', $post_title, $block );

					if ( $link_type ) {
						$post_title = $this->add_dynamic_link( $post_title, $link_type, $source, $block );
					}

					if ( ! empty( $block['attrs']['gpDynamicTextBefore'] ) ) {
						$post_title = $block['attrs']['gpDynamicTextBefore'] . $post_title;
					}

					$post_title = apply_filters( 'generate_dynamic_element_text_output', $post_title, $block );
					$block_content = str_replace( $text_to_replace, $post_title, $block_content );
				}

				if ( 'post-date' === $text_type ) {
					$updated_time = get_the_modified_time( 'U', $id );
					$published_time = get_the_time( 'U', $id ) + 1800;

					$post_date = sprintf(
						'<time class="entry-date published" datetime="%1$s">%2$s</time>',
						esc_attr( get_the_date( 'c', $id ) ),
						esc_html( get_the_date( '', $id ) )
					);

					$is_updated_date = isset( $block['attrs']['gpDynamicDateType'] ) && 'updated-date' === $block['attrs']['gpDynamicDateType'];

					if ( ! empty( $block['attrs']['gpDynamicDateUpdated'] ) || $is_updated_date ) {
						if ( $updated_time > $published_time ) {
							$post_date = sprintf(
								'<time class="entry-date updated-date" datetime="%1$s">%2$s</time>',
								esc_attr( get_the_modified_date( 'c', $id ) ),
								esc_html( get_the_modified_date( '', $id ) )
							);
						} elseif ( $is_updated_date ) {
							// If we're showing the updated date but no updated date exists, don't display anything.
							return '';
						}
					}

					$post_date = apply_filters( 'generate_dynamic_element_text', $post_date, $block );

					if ( $link_type ) {
						$post_date = $this->add_dynamic_link( $post_date, $link_type, $source, $block );
					}

					$before_text = '';

					if ( ! empty( $block['attrs']['gpDynamicTextBefore'] ) ) {
						$before_text = $block['attrs']['gpDynamicTextBefore'];
					}

					// Use the updated date before text if we're set to replace the published date with updated date.
					if ( ! empty( $block['attrs']['gpDynamicUpdatedDateBefore'] ) && ! empty( $block['attrs']['gpDynamicDateUpdated'] ) && $updated_time > $published_time ) {
						$before_text = $block['attrs']['gpDynamicUpdatedDateBefore'];
					}

					if ( ! empty( $before_text ) ) {
						$post_date = $before_text . $post_date;
					}

					$post_date = apply_filters( 'generate_dynamic_element_text_output', $post_date, $block );
					$block_content = str_replace( $text_to_replace, $post_date, $block_content );
				}

				if ( 'post-author' === $text_type ) {
					$author_id = $this->get_author_id( $source, $block['attrs'] );
					$post_author = get_the_author_meta( 'display_name', $author_id );
					$post_author = apply_filters( 'generate_dynamic_element_text', $post_author, $block );

					if ( empty( $post_author ) ) {
						return '';
					}

					if ( $link_type ) {
						$post_author = $this->add_dynamic_link( $post_author, $link_type, $source, $block );
					}

					if ( ! empty( $block['attrs']['gpDynamicTextBefore'] ) ) {
						$post_author = $block['attrs']['gpDynamicTextBefore'] . $post_author;
					}

					$post_author = apply_filters( 'generate_dynamic_element_text_output', $post_author, $block );
					$block_content = str_replace( $text_to_replace, $post_author, $block_content );
				}

				if ( 'terms' === $text_type && 'generateblocks/headline' === $block['blockName'] ) {
					if ( ! empty( $block['attrs']['gpDynamicTextTaxonomy'] ) ) {
						$terms = get_the_terms( $id, $block['attrs']['gpDynamicTextTaxonomy'] );

						if ( is_wp_error( $terms ) ) {
							return $block_content;
						}

						$term_items = array();

						foreach ( (array) $terms as $term ) {
							if ( ! isset( $term->name ) ) {
								continue;
							}

							if ( 'term-archives' === $link_type ) {
								$term_link = get_term_link( $term, $block['attrs']['gpDynamicTextTaxonomy'] );

								if ( ! is_wp_error( $term_link ) ) {
									$term_items[] = sprintf(
										'<span class="post-term-item term-%3$s"><a href="%1$s">%2$s</a></span>',
										esc_url( get_term_link( $term, $block['attrs']['gpDynamicTextTaxonomy'] ) ),
										$term->name,
										$term->slug
									);
								}
							} else {
								$term_items[] = sprintf(
									'<span class="post-term-item term-%2$s">%1$s</span>',
									$term->name,
									$term->slug
								);
							}
						}

						if ( empty( $term_items ) ) {
							return '';
						}

						$sep = isset( $block['attrs']['gpDynamicTextTaxonomySeparator'] ) ? $block['attrs']['gpDynamicTextTaxonomySeparator'] : ', ';
						$term_output = implode( $sep, $term_items );

						if ( ! empty( $block['attrs']['gpDynamicTextBefore'] ) ) {
							$term_output = $block['attrs']['gpDynamicTextBefore'] . $term_output;
						}

						$term_output = apply_filters( 'generate_dynamic_element_text_output', $term_output, $block );
						$block_content = str_replace( $text_to_replace, $term_output, $block_content );
					} else {
						return '';
					}
				}

				if ( 'comments-number' === $text_type ) {
					if ( ! post_password_required( $id ) && ( comments_open( $id ) || get_comments_number( $id ) ) ) {
						if ( ! isset( $block['attrs']['gpDynamicNoCommentsText'] ) ) {
							$block['attrs']['gpDynamicNoCommentsText'] = __( 'No Comments', 'gp-premium' );
						}

						if ( '' === $block['attrs']['gpDynamicNoCommentsText'] && get_comments_number( $id ) < 1 ) {
							return '';
						}

						$comments_text = get_comments_number_text(
							$block['attrs']['gpDynamicNoCommentsText'],
							! empty( $block['attrs']['gpDynamicSingleCommentText'] ) ? $block['attrs']['gpDynamicSingleCommentText'] : __( '1 Comment', 'gp-premium' ),
							! empty( $block['attrs']['gpDynamicMultipleCommentsText'] ) ? $block['attrs']['gpDynamicMultipleCommentsText'] : __( '% Comments', 'gp-premium' )
						);

						$comments_text = apply_filters( 'generate_dynamic_element_text', $comments_text, $block );

						if ( '' === $comments_text ) {
							return '';
						}

						if ( $link_type ) {
							$comments_text = $this->add_dynamic_link( $comments_text, $link_type, $source, $block );
						}

						if ( ! empty( $block['attrs']['gpDynamicTextBefore'] ) ) {
							$comments_text = $block['attrs']['gpDynamicTextBefore'] . $comments_text;
						}

						$comments_text = apply_filters( 'generate_dynamic_element_text_output', $comments_text, $block );
						$block_content = str_replace( $text_to_replace, $comments_text, $block_content );
					} else {
						return '';
					}
				}

				if ( 'post-meta' === $text_type || 'term-meta' === $text_type || 'user-meta' === $text_type ) {
					if ( ! empty( $block['attrs']['gpDynamicTextCustomField'] ) ) {
						$custom_field = get_post_meta( $id, $block['attrs']['gpDynamicTextCustomField'], true );

						if ( 'term-meta' === $text_type ) {
							$custom_field = get_term_meta( get_queried_object_id(), $block['attrs']['gpDynamicTextCustomField'], true );
						}

						if ( 'user-meta' === $text_type ) {
							$author_id = $this->get_author_id( $source, $block['attrs'] );
							$custom_field = $this->get_user_data( $author_id, $block['attrs']['gpDynamicTextCustomField'] );
						}

						$custom_field = apply_filters( 'generate_dynamic_element_text', $custom_field, $block );

						if ( $custom_field ) {
							if ( $link_type ) {
								$custom_field = $this->add_dynamic_link( $custom_field, $link_type, $source, $block );
							}

							if ( ! empty( $block['attrs']['gpDynamicTextBefore'] ) ) {
								$custom_field = $block['attrs']['gpDynamicTextBefore'] . $custom_field;
							}

							$custom_field = apply_filters( 'generate_dynamic_element_text_output', $custom_field, $block );
							$block_content = str_replace( $text_to_replace, $custom_field, $block_content );
						} else {
							$block_content = '';
						}
					} else {
						$block_content = '';
					}
				}
			}
		}

		if ( 'generateblocks/button' === $block['blockName'] ) {
			$link_type = ! empty( $block['attrs']['gpDynamicLinkType'] ) ? $block['attrs']['gpDynamicLinkType'] : '';

			if ( ! empty( $link_type ) && 'term-archives' !== $link_type ) {
				$source = ! empty( $block['attrs']['gpDynamicSource'] ) ? $block['attrs']['gpDynamicSource'] : 'current-post';
				$id = $this->get_source_id( $source, $block['attrs'] );

				if ( ! $id ) {
					return '';
				}

				if ( $link_type ) {
					$block_content = $this->add_dynamic_link( $block_content, $link_type, $source, $block );
				}
			}

			if ( ! empty( $block['attrs']['gpDynamicTextType'] ) && ! empty( $block['attrs']['gpDynamicTextReplace'] ) ) {
				$text_to_replace = $block['attrs']['gpDynamicTextReplace'];
				$text_type = $block['attrs']['gpDynamicTextType'];
				$link_type = ! empty( $block['attrs']['gpDynamicLinkType'] ) ? $block['attrs']['gpDynamicLinkType'] : '';
				$source = ! empty( $block['attrs']['gpDynamicSource'] ) ? $block['attrs']['gpDynamicSource'] : 'current-post';
				$id = $this->get_source_id( $source, $block['attrs'] );

				if ( ! $id ) {
					return '';
				}

				if ( 'terms' === $text_type ) {
					if ( ! empty( $block['attrs']['gpDynamicTextTaxonomy'] ) ) {
						$terms = get_the_terms( $id, $block['attrs']['gpDynamicTextTaxonomy'] );

						if ( is_wp_error( $terms ) ) {
							return '';
						}

						$term_buttons = array();

						foreach ( (array) $terms as $term ) {
							if ( ! isset( $term->name ) ) {
								continue;
							}

							$term_button = str_replace( $text_to_replace, $term->name, $block_content );

							if ( isset( $term->slug ) ) {
								$term_button = str_replace( 'dynamic-term-class', 'post-term-item term-' . $term->slug, $term_button );
							}

							if ( 'term-archives' === $link_type ) {
								$term_link = get_term_link( $term, $block['attrs']['gpDynamicTextTaxonomy'] );

								if ( ! is_wp_error( $term_link ) ) {
									$term_url = sprintf(
										'href="%s"',
										esc_url( $term_link )
									);

									$term_button = str_replace( 'href="#"', $term_url, $term_button );
								}
							}

							$term_buttons[] = $term_button;
						}

						if ( empty( $term_buttons ) ) {
							return '';
						}

						$block_content = implode( '', $term_buttons );
					} else {
						return '';
					}
				}
			}
		}

		if ( 'generateblocks/container' === $block['blockName'] ) {
			if ( ! empty( $block['attrs']['gpRemoveContainerCondition'] ) ) {
				$in_same_term = ! empty( $block['attrs']['gpAdjacentPostInSameTerm'] ) ? true : false;
				$term_taxonomy = ! empty( $block['attrs']['gpAdjacentPostInSameTermTax'] ) ? $block['attrs']['gpAdjacentPostInSameTermTax'] : 'category';

				if ( 'no-next-post' === $block['attrs']['gpRemoveContainerCondition'] ) {
					$next_post = get_next_post( $in_same_term, '', $term_taxonomy );

					if ( ! is_object( $next_post ) ) {
						if ( ! empty( $block['attrs']['isGrid'] ) && ! empty( $block['attrs']['uniqueId'] ) ) {
							return '<div class="gb-grid-column gb-grid-column-' . $block['attrs']['uniqueId'] . '"></div>';
						} else {
							return '';
						}
					}
				}

				if ( 'no-previous-post' === $block['attrs']['gpRemoveContainerCondition'] ) {
					$previous_post = get_previous_post( $in_same_term, '', $term_taxonomy );

					if ( ! is_object( $previous_post ) ) {
						if ( ! empty( $block['attrs']['isGrid'] ) && ! empty( $block['attrs']['uniqueId'] ) ) {
							return '<div class="gb-grid-column gb-grid-column-' . $block['attrs']['uniqueId'] . '"></div>';
						} else {
							return '';
						}
					}
				}

				if ( 'no-featured-image' === $block['attrs']['gpRemoveContainerCondition'] ) {
					if ( ! has_post_thumbnail() ) {
						return '';
					}
				}

				if ( 'no-post-meta' === $block['attrs']['gpRemoveContainerCondition'] && ! empty( $block['attrs']['gpRemoveContainerConditionPostMeta'] ) ) {
					$post_meta_check = get_post_meta( get_the_ID(), $block['attrs']['gpRemoveContainerConditionPostMeta'], true );

					if ( ! $post_meta_check ) {
						return '';
					}
				}
			} elseif ( ! empty( $block['attrs']['url'] ) && ! empty( $block['attrs']['gpDynamicLinkType'] ) ) {
				$source = ! empty( $block['attrs']['gpDynamicSource'] ) ? $block['attrs']['gpDynamicSource'] : 'current-post';

				$id = $this->get_source_id( $source, $block['attrs'] );

				if ( ! $id ) {
					return '';
				}
			}
		}

		return $block_content;
	}

	/**
	 * Set the featured image as a GB background.
	 *
	 * @param string $url The current URL.
	 * @param array  $settings The current settings.
	 */
	public function set_background_image_url( $url, $settings ) {
		if ( ! empty( $settings['gpDynamicImageBg'] ) ) {
			$custom_field = '';
			$source = ! empty( $settings['gpDynamicSource'] ) ? $settings['gpDynamicSource'] : 'current-post';
			$id = $this->get_source_id( $source, $settings );

			if ( ! $id ) {
				return '';
			}

			if ( 'post-meta' === $settings['gpDynamicImageBg'] ) {
				$custom_field = get_post_meta( $id, $settings['gpDynamicImageCustomField'], true );
			}

			if ( 'term-meta' === $settings['gpDynamicImageBg'] ) {
				$custom_field = get_term_meta( get_queried_object_id(), $settings['gpDynamicImageCustomField'], true );
			}

			if ( 'user-meta' === $settings['gpDynamicImageBg'] ) {
				$author_id = $this->get_author_id( $source, $settings );
				$custom_field = $this->get_user_data( $author_id, $settings['gpDynamicImageCustomField'] );
			}

			if ( 'featured-image' === $settings['gpDynamicImageBg'] && has_post_thumbnail( $id ) ) {
				$image_size = ! empty( $settings['bgImageSize'] ) ? $settings['bgImageSize'] : 'full';
				$url = get_the_post_thumbnail_url( $id, $image_size );
			} elseif ( ! empty( $custom_field ) ) {
				if ( is_numeric( $custom_field ) ) {
					$image_size = ! empty( $settings['bgImageSize'] ) ? $settings['bgImageSize'] : 'full';
					$url = wp_get_attachment_image_url( $custom_field, $image_size );
				} else {
					$url = $custom_field;
				}
			} elseif ( empty( $settings['gpUseFallbackImageBg'] ) ) {
				$url = '';
			}
		}

		return $url;
	}

	/**
	 * Set the attributes for our main Container wrapper.
	 *
	 * @param array $attributes The existing attributes.
	 * @param array $settings The settings for the block.
	 */
	public function set_container_attributes( $attributes, $settings ) {
		if ( ! empty( $settings['bgImage'] ) && in_the_loop() ) {
			if ( ! empty( $settings['gpDynamicImageBg'] ) ) {
				$custom_field = '';
				$source = ! empty( $settings['gpDynamicSource'] ) ? $settings['gpDynamicSource'] : 'current-post';
				$id = $this->get_source_id( $source, $settings );

				if ( ! $id ) {
					return $attributes;
				}

				if ( 'post-meta' === $settings['gpDynamicImageBg'] ) {
					$custom_field = get_post_meta( $id, $settings['gpDynamicImageCustomField'], true );
				}

				if ( 'term-meta' === $settings['gpDynamicImageBg'] ) {
					$custom_field = get_term_meta( get_queried_object_id(), $settings['gpDynamicImageCustomField'], true );
				}

				if ( 'user-meta' === $settings['gpDynamicImageBg'] ) {
					$author_id = $this->get_author_id( $source, $settings );
					$custom_field = $this->get_user_data( $author_id, $settings['gpDynamicImageCustomField'] );
				}

				if ( 'featured-image' === $settings['gpDynamicImageBg'] && has_post_thumbnail( $id ) ) {
					$image_size = ! empty( $settings['bgImageSize'] ) ? $settings['bgImageSize'] : 'full';
					$url = get_the_post_thumbnail_url( $id, $image_size );
				} elseif ( ! empty( $custom_field ) ) {
					if ( is_numeric( $custom_field ) ) {
						$image_size = ! empty( $settings['bgImageSize'] ) ? $settings['bgImageSize'] : 'full';
						$url = wp_get_attachment_image_url( $custom_field, $image_size );
					} else {
						$url = $custom_field;
					}
				} elseif ( ! empty( $settings['gpUseFallbackImageBg'] ) ) {
					if ( isset( $settings['bgImage']['id'] ) ) {
						$image_size = ! empty( $settings['bgImageSize'] ) ? $settings['bgImageSize'] : 'full';
						$image_src = wp_get_attachment_image_src( $settings['bgImage']['id'], $image_size );

						if ( is_array( $image_src ) ) {
							$url = $image_src[0];
						} else {
							$url = $settings['bgImage']['image']['url'];
						}
					} else {
						$url = $settings['bgImage']['image']['url'];
					}
				}

				if ( ! empty( $url ) ) {
					$attributes['style'] = '--background-url:url(' . esc_url( $url ) . ')';
					$attributes['class'] .= ' gb-has-dynamic-bg';
				} else {
					$attributes['class'] .= ' gb-no-dynamic-bg';
				}
			}
		}

		if ( ! empty( $settings['gpInlinePostMeta'] ) ) {
			$attributes['class'] .= ' inline-post-meta-area';
		}

		return $attributes;
	}

	/**
	 * Set GenerateBlocks defaults.
	 *
	 * @param array $defaults The current defaults.
	 */
	public function set_defaults( $defaults ) {
		$defaults['container']['gpInlinePostMeta'] = false;
		$defaults['container']['gpInlinePostMetaJustify'] = '';
		$defaults['container']['gpInlinePostMetaJustifyTablet'] = '';
		$defaults['container']['gpInlinePostMetaJustifyMobile'] = '';
		$defaults['container']['gpDynamicImageBg'] = '';
		$defaults['container']['gpDynamicImageCustomField'] = '';
		$defaults['container']['gpDynamicLinkType'] = '';
		$defaults['container']['gpDynamicSource'] = 'current-post';
		$defaults['container']['gpDynamicSourceInSameTerm'] = false;
		$defaults['headline']['gpDynamicTextTaxonomy'] = '';
		$defaults['headline']['gpDynamicTextTaxonomySeparator'] = ', ';

		return $defaults;
	}

	/**
	 * Generate our CSS for our options.
	 *
	 * @param string $name Name of the block.
	 * @param array  $settings Our available settings.
	 * @param object $css Current desktop CSS object.
	 * @param object $desktop_css Current desktop-only CSS object.
	 * @param object $tablet_css Current tablet CSS object.
	 * @param object $tablet_only_css Current tablet-only CSS object.
	 * @param object $mobile_css Current mobile CSS object.
	 */
	public function generate_css( $name, $settings, $css, $desktop_css, $tablet_css, $tablet_only_css, $mobile_css ) {
		if ( 'container' === $name ) {
			if ( ! empty( $settings['bgImage'] ) ) {
				if ( 'element' === $settings['bgOptions']['selector'] ) {
					$css->set_selector( '.gb-container-' . $settings['uniqueId'] . '.gb-has-dynamic-bg' );
				} elseif ( 'pseudo-element' === $settings['bgOptions']['selector'] ) {
					$css->set_selector( '.gb-container-' . $settings['uniqueId'] . '.gb-has-dynamic-bg:before' );
				}

				$css->add_property( 'background-image', 'var(--background-url)' );

				if ( 'element' === $settings['bgOptions']['selector'] ) {
					$css->set_selector( '.gb-container-' . $settings['uniqueId'] . '.gb-no-dynamic-bg' );
				} elseif ( 'pseudo-element' === $settings['bgOptions']['selector'] ) {
					$css->set_selector( '.gb-container-' . $settings['uniqueId'] . '.gb-no-dynamic-bg:before' );
				}

				$css->add_property( 'background-image', 'none' );
			}

			if ( ! empty( $settings['gpInlinePostMeta'] ) ) {
				$css->set_selector( '.gb-container-' . $settings['uniqueId'] . '.inline-post-meta-area > .gb-inside-container' );
				$css->add_property( 'display', 'flex' );
				$css->add_property( 'align-items', 'center' );
				$css->add_property( 'justify-content', $settings['gpInlinePostMetaJustify'] );

				$tablet_css->set_selector( '.gb-container-' . $settings['uniqueId'] . '.inline-post-meta-area > .gb-inside-container' );
				$tablet_css->add_property( 'justify-content', $settings['gpInlinePostMetaJustifyTablet'] );

				$mobile_css->set_selector( '.gb-container-' . $settings['uniqueId'] . '.inline-post-meta-area > .gb-inside-container' );
				$mobile_css->add_property( 'justify-content', $settings['gpInlinePostMetaJustifyMobile'] );
			}
		}
	}

	/**
	 * Set the attributes for our main Container wrapper.
	 *
	 * @param array $attributes The existing attributes.
	 * @param array $settings The settings for the block.
	 */
	public function set_dynamic_container_url( $attributes, $settings ) {
		$link_type = ! empty( $settings['gpDynamicLinkType'] ) ? $settings['gpDynamicLinkType'] : '';

		if (
			$link_type &&
			isset( $settings['url'] ) &&
			isset( $settings['linkType'] ) &&
			'' !== $settings['url'] &&
			( 'wrapper' === $settings['linkType'] || 'hidden-link' === $settings['linkType'] )
		) {
			if ( ! empty( $link_type ) ) {
				$source = ! empty( $settings['gpDynamicSource'] ) ? $settings['gpDynamicSource'] : 'current-post';
				$id = $this->get_source_id( $source, $settings );

				if ( ! $id ) {
					return $attributes;
				}

				if ( 'post' === $link_type ) {
					$attributes['href'] = esc_url( get_permalink( $id ) );
				}

				if ( 'post-meta' === $link_type ) {
					if ( ! empty( $settings['gpDynamicLinkCustomField'] ) ) {
						$custom_field = get_post_meta( $id, $settings['gpDynamicLinkCustomField'], true );

						if ( $custom_field ) {
							$attributes['href'] = esc_url( $custom_field );
						}
					}
				}
			}
		}

		return $attributes;
	}

	/**
	 * Get our needed source ID.
	 *
	 * @param string $source The source attribute.
	 * @param array  $attributes All block attributes.
	 */
	public function get_source_id( $source, $attributes = array() ) {
		$id = get_the_ID();

		if ( 'next-post' === $source ) {
			$in_same_term = ! empty( $attributes['gpDynamicSourceInSameTerm'] ) ? true : false;
			$term_taxonomy = ! empty( $attributes['gpDynamicSourceInSameTermTaxonomy'] ) ? $attributes['gpDynamicSourceInSameTermTaxonomy'] : 'category';
			$next_post = get_next_post( $in_same_term, '', $term_taxonomy );

			if ( ! is_object( $next_post ) ) {
				return false;
			}

			$id = $next_post->ID;
		}

		if ( 'previous-post' === $source ) {
			$in_same_term = ! empty( $attributes['gpDynamicSourceInSameTerm'] ) ? true : false;
			$term_taxonomy = ! empty( $attributes['gpDynamicSourceInSameTermTaxonomy'] ) ? $attributes['gpDynamicSourceInSameTermTaxonomy'] : 'category';
			$previous_post = get_previous_post( $in_same_term, '', $term_taxonomy );

			if ( ! is_object( $previous_post ) ) {
				return false;
			}

			$id = $previous_post->ID;
		}

		return apply_filters( 'generate_dynamic_element_source_id', $id, $source, $attributes );
	}

	/**
	 * Get our author ID.
	 *
	 * @param string $source The source attribute.
	 * @param array  $attributes All block attributes.
	 */
	public function get_author_id( $source, $attributes ) {
		global $post;
		$post_info = $post;

		if ( 'next-post' === $source ) {
			$in_same_term = ! empty( $attributes['gpDynamicSourceInSameTerm'] ) ? true : false;
			$term_taxonomy = ! empty( $attributes['gpDynamicSourceInSameTermTaxonomy'] ) ? $attributes['gpDynamicSourceInSameTermTaxonomy'] : 'category';
			$next_post = get_next_post( $in_same_term, '', $term_taxonomy );

			if ( ! is_object( $next_post ) ) {
				return '';
			}

			$post_info = $next_post;
		}

		if ( 'previous-post' === $source ) {
			$in_same_term = ! empty( $attributes['gpDynamicSourceInSameTerm'] ) ? true : false;
			$term_taxonomy = ! empty( $attributes['gpDynamicSourceInSameTermTaxonomy'] ) ? $attributes['gpDynamicSourceInSameTermTaxonomy'] : 'category';
			$previous_post = get_previous_post( $in_same_term, '', $term_taxonomy );

			if ( ! is_object( $previous_post ) ) {
				return '';
			}

			$post_info = $previous_post;
		}

		if ( isset( $post_info->post_author ) ) {
			return $post_info->post_author;
		}
	}

	/**
	 * Register our post meta.
	 */
	public function register_meta() {
		register_meta(
			'post',
			'_generate_block_element_editor_width',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_int' ),
			)
		);

		register_meta(
			'post',
			'_generate_block_element_editor_width_unit',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_meta(
			'post',
			'_generate_block_type',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_meta(
			'post',
			'_generate_hook',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_meta(
			'post',
			'_generate_custom_hook',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_custom_hook' ),
			)
		);

		register_meta(
			'post',
			'_generate_hook_priority',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_int' ),
			)
		);

		register_meta(
			'post',
			'_generate_post_meta_location',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_text_field' ),
			)
		);

		register_meta(
			'post',
			'_generate_post_loop_item_tagname',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_text_field' ),
			)
		);

		register_meta(
			'post',
			'_generate_disable_primary_post_meta',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_disable_secondary_post_meta',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_disable_title',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_disable_featured_image',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_use_theme_post_container',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_use_archive_navigation_container',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_disable_post_navigation',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_disable_archive_navigation',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'boolean',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'rest_sanitize_boolean' ),
			)
		);

		register_meta(
			'post',
			'_generate_post_loop_item_display',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_text_field' ),
			)
		);

		register_meta(
			'post',
			'_generate_post_loop_item_display_tax',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_text_field' ),
			)
		);

		register_meta(
			'post',
			'_generate_post_loop_item_display_term',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_text_field' ),
			)
		);

		register_meta(
			'post',
			'_generate_post_loop_item_display_post_meta',
			array(
				'object_subtype' => 'gp_elements',
				'type' => 'string',
				'show_in_rest' => true,
				'auth_callback' => '__return_true',
				'single' => true,
				'sanitize_callback' => array( $this, 'sanitize_text_field' ),
			)
		);
	}

	/**
	 * Sanitize our custom hook field.
	 *
	 * @param string $value The value to sanitize.
	 */
	public function sanitize_custom_hook( $value ) {
		$not_allowed = array(
			'muplugins_loaded',
			'registered_taxonomy',
			'plugins_loaded',
			'setup_theme',
			'after_setup_theme',
			'init',
			'widgets_init',
			'wp_loaded',
			'pre_get_posts',
			'wp',
			'template_redirect',
			'get_header',
			'wp_enqueue_scripts',
			'the_post',
			'dynamic_sidebar',
			'get_footer',
			'get_sidebar',
			'wp_print_footer_scripts',
			'shutdown',
		);

		if ( in_array( $value, $not_allowed ) ) {
			return '';
		}

		return sanitize_key( $value );
	}

	/**
	 * Sanitize number values that can be empty.
	 *
	 * @param int $value The value to sanitize.
	 */
	public function sanitize_int( $value ) {
		if ( ! is_numeric( $value ) ) {
			return '';
		}

		return absint( $value );
	}

	/**
	 * Get our image size names and dimensions.
	 */
	public function get_image_sizes() {
		global $_wp_additional_image_sizes;

		$default_image_sizes = get_intermediate_image_sizes();

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ]['width'] = intval( get_option( "{$size}_size_w" ) );
			$image_sizes[ $size ]['height'] = intval( get_option( "{$size}_size_h" ) );
			$image_sizes[ $size ]['crop'] = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
		}

		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		return $image_sizes;
	}

	/**
	 * Add front-end CSS.
	 */
	public function frontend_css() {
		require_once GP_LIBRARY_DIRECTORY . 'class-make-css.php';
		$css = new GeneratePress_Pro_CSS();

		$css->set_selector( '.dynamic-author-image-rounded' );
		$css->add_property( 'border-radius', '100%' );

		$css->set_selector( '.dynamic-featured-image, .dynamic-author-image' );
		$css->add_property( 'vertical-align', 'middle' );

		$css->set_selector( '.one-container.blog .dynamic-content-template:not(:last-child), .one-container.archive .dynamic-content-template:not(:last-child)' );
		$css->add_property( 'padding-bottom', '0px' );

		$css->set_selector( '.dynamic-entry-excerpt > p:last-child' );
		$css->add_property( 'margin-bottom', '0px' );

		wp_add_inline_style( 'generate-style', $css->css_output() );
	}
}

GeneratePress_Block_Elements::get_instance();
