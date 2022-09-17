<?php
/**
 * This file creates a class to build our CSS.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! class_exists( 'GeneratePress_Backgrounds_CSS' ) ) {
	/**
	 * Generate our background CSS.
	 */
	class GeneratePress_Backgrounds_CSS {

		/**
		 * The css selector that you're currently adding rules to
		 *
		 * @access protected
		 * @var string
		 */
		protected $_selector = ''; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

		/**
		 * Stores the final css output with all of its rules for the current selector.
		 *
		 * @access protected
		 * @var string
		 */
		protected $_selector_output = ''; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

		/**
		 * Stores all of the rules that will be added to the selector
		 *
		 * @access protected
		 * @var string
		 */
		protected $_css = ''; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

		/**
		 * The string that holds all of the css to output
		 *
		 * @access protected
		 * @var string
		 */
		protected $_output = ''; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

		/**
		 * Sets a selector to the object and changes the current selector to a new one
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param  string $selector - the css identifier of the html that you wish to target.
		 * @return $this
		 */
		public function set_selector( $selector = '' ) {
			// Render the css in the output string everytime the selector changes.
			if ( '' !== $this->_selector ) {
				$this->add_selector_rules_to_output();
			}

			$this->_selector = $selector;

			return $this;
		}

		/**
		 * Adds a css property with value to the css output
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param  string $property - the css property.
		 * @param  string $value - the value to be placed with the property.
		 * @param  string $url Whether we need to generate URL in the string.
		 * @return $this
		 */
		public function add_property( $property, $value, $url = '' ) {
			// If we don't have a value or our value is the same as our og default, bail.
			if ( empty( $value ) ) {
				return false;
			}

			// Set up our background image URL param if needed.
			$url_start = ( '' !== $url ) ? "url('" : ""; // phpcs:ignore -- need double quotes.
			$url_end = ( '' !== $url ) ? "')" : ""; // phpcs:ignore -- need double quotes.

			$this->_css .= $property . ':' . $url_start . $value . $url_end . ';';
			return $this;
		}

		/**
		 * Adds the current selector rules to the output variable
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return $this
		 */
		private function add_selector_rules_to_output() {
			if ( ! empty( $this->_css ) ) {
				$this->_selector_output = $this->_selector;
				$selector_output = sprintf( '%1$s{%2$s}', $this->_selector_output, $this->_css );

				$this->_output .= $selector_output;

				// Reset the css.
				$this->_css = '';
			}

			return $this;
		}

		/**
		 * Returns the minified css in the $_output variable
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @return string
		 */
		public function css_output() {
			// Add current selector's rules to output.
			$this->add_selector_rules_to_output();

			// Output minified css.
			return $this->_output;
		}

	}
}
