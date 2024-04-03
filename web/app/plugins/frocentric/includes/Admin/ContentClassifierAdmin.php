<?php
/**
 * Content Classifier admin page
 *
 * @package     Frocentric/Admin
 * @version     1.0.0
 */

namespace Frocentric\Admin;

use Frocentric\Assets as AssetsMain;
use Frocentric\Utils;
use Frocentric\ContentClassifier;

class ContentClassifierAdmin {

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'add_tools_menu' ) );
		add_action( 'wp_ajax_classify_content', array( __CLASS__, 'process_posts_ajax' ) );

		add_filter( 'frocentric_enqueue_scripts', array( __CLASS__, 'add_scripts' ), 9 );
	}

	/**
	 * Add scripts for the admin.
	 *
	 * @param  array $scripts Admin scripts.
	 * @return array<string,array>
	 */
	public static function add_scripts( $scripts ) {

		$scripts['classify-content-ajax'] = array(
			'src'  => AssetsMain::localize_asset( 'js/admin/classify-content.js' ),
			'data' => array(
				'ajax_url' => Utils::ajax_url(),
			),
		);

		return $scripts;
	}

	public static function add_tools_menu() {
		add_management_page(
			'Classify Content',
			'Classify Content',
			'manage_options',
			'classify-content',
			array( __CLASS__, 'render_admin_page' )
		);
	}

	public static function render_admin_page() {
		wp_localize_script(
			'classify-content-ajax',
			'classifyContentAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'classify_content_nonce' ),
			)
		);
		?>
		<div class="wrap">
			<h1><?php _e( 'Classify Content', 'frocentric' ); ?></h1>
			<p><?php _e( 'This tool teaches a Naive-Bayes model how to classify imported posts for a specific post type.', 'frocentric' ); ?></p>
			<hr class="wp-header-end">
			<h2><?php _e( 'Process Content Type', 'frocentric' ); ?></h2>
			<form id="classify-content-form">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="post_type"><?php _e( 'Post Type', 'frocentric' ); ?></label>
							</th>
							<td>
								<select name="post_type" id="post_type">
									<?php foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) : ?>
										<option value="<?php echo esc_attr( $post_type->name ); ?>"><?php echo esc_html( $post_type->label ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Start classification', 'frocentric' ), 'button' ); ?>
			</form>
		</div>
		<?php
	}

	public static function process_posts_ajax() {
		check_ajax_referer( 'classify_content_nonce', 'security' );

		$post_type = $_POST['post_type'] ?? '';
		// Validate $post_type or other security checks
		$paged = 1;
		$posts_per_page = 10;
		$processed = 0;
		$total = 0;
		$classifier = new ContentClassifier();

		$classifier->set_state( get_option( 'content_classifier_state', array() ) );
		$classifier->reset( $post_type );

		do {
			$query = new \WP_Query(
				array(
					'post_type' => $post_type,
					'posts_per_page' => $posts_per_page,
					'paged' => $paged,
					'fields' => 'ids',
				)
			);

			if ( $total === 0 ) {
				$total = $query->found_posts;
			}

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post_id ) {
					list($text, $labels) = self::extract_text_and_labels( $post_id );
					$classifier->learn( $post_type, $text, $labels );
					$processed++;
				}
			}

			$paged++;
		} while ( $query->have_posts() );

		update_option( 'content_classifier_state', $classifier->get_state() );

		wp_send_json_success(
			array(
				'processed' => $processed,
				'total' => $total,
			)
		);
	}

	protected static function extract_text_and_labels( $post_id ) {
		$post = get_post( $post_id );
		$text = strip_shortcodes( wp_strip_all_tags( get_the_content( null, false, $post ) ) );
		$labels = array();
		// Get all public taxonomies for the post type
		$taxonomies = get_object_taxonomies( $post->post_type, 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			if ( $taxonomy->public ) {
				$terms = wp_get_object_terms( $post_id, $taxonomy->name, array( 'fields' => 'names' ) );

				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$labels[ $taxonomy->name ] = array_map( 'strtolower', $terms );
				}
			}
		}

		return array( $text, $labels );
	}
}
