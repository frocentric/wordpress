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
		public $nonce = '';

		public function enqueue() {
			wp_enqueue_script( 'gp-button-actions', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/button-actions.js', array( 'customize-controls' ), GP_PREMIUM_VERSION, true );
			wp_enqueue_style( 'gp-button-actions', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/button-actions.css', array(), GP_PREMIUM_VERSION );
		}

		public function to_json() {
			parent::to_json();

			$this->json['data_type'] = $this->data_type;
			$this->json['description'] = $this->description;
			$this->json['nonce'] = $this->nonce;
		}

		public function content_template() {
			?>
			<button class="button" data-type="{{{ data.data_type }}}" data-nonce="{{{ data.nonce }}}">{{{ data.label }}}</button>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">
					<p>{{{ data.description }}}</p>
				</span>
			<# } #>
			<?php
		}
	}
}
