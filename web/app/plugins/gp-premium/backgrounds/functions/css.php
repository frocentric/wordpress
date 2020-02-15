<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'GeneratePress_Backgrounds_CSS' ) ) {
	class GeneratePress_Backgrounds_CSS {

		protected $_selector = '';
		protected $_selector_output = '';
		protected $_css = '';
		protected $_output = '';

		public function set_selector( $selector = '' ) {
			// Render the css in the output string everytime the selector changes
			if ( $this->_selector !== '' ) {
				$this->add_selector_rules_to_output();
			}

			$this->_selector = $selector;

			return $this;
		}

		public function add_property( $property, $value, $url = '' ) {
			// If we don't have a value or our value is the same as our og default, bail
			if ( empty( $value ) ) {
				return false;
			}

			// Set up our background image URL param if needed
			$url_start = ( '' !== $url ) ? "url('" : "";
			$url_end = ( '' !== $url ) ? "')" : "";

			$this->_css .= $property . ':' . $url_start . $value . $url_end . ';';
			return $this;
		}

		private function add_selector_rules_to_output() {
			if ( ! empty( $this->_css ) ) {
				$this->_selector_output = $this->_selector;
				$selector_output = sprintf( '%1$s{%2$s}', $this->_selector_output, $this->_css );

				$this->_output .= $selector_output;

				// Reset the css
				$this->_css = '';
			}

			return $this;
		}

		public function css_output() {
			// Add current selector's rules to output
			$this->add_selector_rules_to_output();

			// Output minified css
			return $this->_output;
		}

	}
}
