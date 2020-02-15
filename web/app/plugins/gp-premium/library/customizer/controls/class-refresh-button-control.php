<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'GeneratePress_Refresh_Button_Customize_Control' ) ) :
/**
 * Add a button to initiate refresh when changing featured image sizes
 */
class GeneratePress_Refresh_Button_Customize_Control extends WP_Customize_Control {
	public $type = 'refresh_button';
	
	public function to_json() {
		parent::to_json();
	}
	
	public function content_template() {
		?>
		<a class="button" onclick="wp.customize.previewer.refresh();" href="#">{{{ data.label }}}</a>
		<?php
	}
}
endif;