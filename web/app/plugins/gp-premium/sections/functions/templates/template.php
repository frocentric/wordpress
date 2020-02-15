<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Generate
 */

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
$sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_sections', TRUE) : '';
$sidebars = apply_filters( 'generate_sections_sidebars', false );
?>

	<div id="primary" <?php echo $sidebars ? generate_content_class() : 'class="content-area grid-parent grid-100"' ?>>
		<main id="main" <?php if ( function_exists( 'generate_main_class' ) ) generate_main_class(); ?>>
			<?php do_action('generate_before_main_content'); ?>

			 <?php if ( post_password_required() ) : ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php generate_article_schema( 'CreativeWork' ); ?>>
					<div class="inside-article">
						<div class="entry-content" itemprop="text">
							<?php the_content(); ?>
						</div><!-- .entry-content -->
					</div><!-- .inside-article -->
				</article><!-- #post-## -->

			<?php else : ?>

				<?php
				// check if the repeater field has rows of data
				if( $sections && '' !== $sections ) :

					// loop through the rows of data
					$i = 0;
					$return = '';
					foreach ( $sections['sections'] as $section ) :
						$i++;

						// Get the values
						$box_type 			= ( isset( $section['box_type'] ) ) ? $section['box_type'] : 'fluid';
						$inner_box_type 	= ( isset( $section['inner_box_type'] ) ) ? $section['inner_box_type'] : 'contained';
						$custom_classes 	= ( isset( $section['custom_classes'] ) ) ? $section['custom_classes'] : '';
						$custom_id 			= ( isset( $section['custom_id'] ) ) ? $section['custom_id'] : '';
						$parallax_effect 	= ( isset( $section['parallax_effect'] ) ) ? $section['parallax_effect'] : '';
						$content 			= ( isset( $section['content'] ) ) ? apply_filters( 'generate_the_section_content', $section['content'] ) : '';

						// Set up parallax
						$parallax = ( 'enable' == $parallax_effect ) ? ' enable-parallax' : '';
						$parallax_speed = apply_filters( 'generate_sections_parallax_speed', 6 );
						$parallax_data = ( 'enable' == $parallax_effect ) ? ' data-speed="' . intval( $parallax_speed ) . '"' : '';

						// Set up custom classes
						$classes = ( ! empty( $custom_classes ) ) ? ' ' . sanitize_text_field( $custom_classes ) : '';

						// Set up custom ID
						$custom_id = ( '' == $custom_id ) ? "generate-section-$i" : $custom_id;

						// Create container arrays
						$container = array();
						$inner_container = array();

						// Create container
						if ( 'contained' == $box_type ) :
							$container['before'] = '<div id="' . esc_attr( $custom_id ) . '" class="grid-container grid-parent generate-sections-container' . $parallax . $classes . '"' . $parallax_data . '>';
							$container['after'] = '</div>';
						else :
							$container['before'] = '<div id="' . esc_attr( $custom_id ) . '" class="generate-sections-container' . $parallax . $classes . '"' . $parallax_data . '>';
							$container['after'] = '</div>';
						endif;

						// Create inner container
						if ( 'fluid' == $inner_box_type ) :
							$inner_container['before'] = '<div class="generate-sections-inside-container" itemprop="text">';
							$inner_container['after'] = '</div>';
						else :
							$inner_container['before'] = '<div class="grid-container grid-parent generate-sections-inside-container" itemprop="text">';
							$inner_container['after'] = '</div>';
						endif;

						// Output the container
						$return .= $container['before'];
						$return .= $inner_container['before'];

							// Output the content
							// Add \n\n to fix issue where paragraph wrapping was off
							$return .= "\n\n" . $content;

						// Output the closing containers
						$return .= $container['after'];
						$return .= $inner_container['after'];

					endforeach;

					// Return our sections through the_content filter
					echo apply_filters( 'the_content', $return );
				else :
				?>
					<div class="generate-sections-inside-container inside-article">
						<div class="grid-container grid-parent generate-sections-inside-container inside-article">
							<?php _e( 'No sections added!', 'gp-premium' ); ?>
						</div>
					</div>


				<?php
				endif;
				?>
			<?php endif; ?>
			<?php do_action('generate_after_main_content'); ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
if ( $sidebars) do_action('generate_sidebars');
get_footer();
