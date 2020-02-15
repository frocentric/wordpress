<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="generate_sections_control">

		<?php
		global $post;
	    $use_sections = get_post_meta( $post->ID, '_generate_use_sections', true );
        //$use_sections = isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ? true : false;
        wp_nonce_field( 'generate_sections_use_sections_nonce', '_generate_sections_use_sections_nonce' );
		?>
		<label for="_generate_use_sections[use_sections]">
			<input type="checkbox" class="use-sections-switch" name="_generate_use_sections[use_sections]" id="_generate_use_sections[use_sections]" value="true" <?php if ( isset ( $use_sections['use_sections'] ) ) checked( $use_sections['use_sections'], 'true', true );?> />
			<?php _e( 'Use Sections', 'gp-premium' ); ?>
		</label>
</div>
