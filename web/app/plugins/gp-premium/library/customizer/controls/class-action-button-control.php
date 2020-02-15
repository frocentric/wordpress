<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

if ( ! class_exists( 'GeneratePress_Action_Button_Control' ) ) {
	/**
	 * Add a button which needs javascript attached to it.
	 */
	class GeneratePress_Action_Button_Control extends WP_Customize_Control {
		public $type = 'gp_action_button';
		public $data_type = '';
		public $description = '';

		public function enqueue() {
			wp_enqueue_script( 'gp-button-actions', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/button-actions.js', array( 'customize-controls' ), GP_PREMIUM_VERSION, true );
		}

		public function to_json() {
			parent::to_json();

			$this->json['data_type'] = $this->data_type;
			$this->json['description'] = $this->description;
		}

		public function content_template() {
			?>
			<button class="button" data-type="{{{ data.data_type }}}">{{{ data.label }}}</button>
			<span class="description customize-control-description">
				<p>{{{ data.description }}}</p>
			</span>
			<?php
		}
	}
}
