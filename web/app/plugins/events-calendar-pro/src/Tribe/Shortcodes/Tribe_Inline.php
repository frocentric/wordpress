<?php


class Tribe__Events__Pro__Shortcodes__Tribe_Inline {

	/**
	 * Container for the shortcode attributes.
	 *
	 * @var array
	 */
	public $atts = array();

	/**
	 * @var string
	 */
	public $content = '';

	/**
	 * @var string
	 */
	protected $output = '';


	/**
	 * Contruct with required shortcode attributes
	 *
	 * @param $atts
	 * @param $content
	 * @param $tag
	 */
	public function __construct( $atts, $content, $tag ) {
		$this->setup( $atts, $content, $tag );
		$this->parse();
		$this->render();
	}

	/**'
	 * Setup attributes into properties
	 *
	 * @param $atts
	 * @param $content
	 * @param $tag
	 */
	protected function setup( $atts, $content, $tag ) {

		$defaults = array(
			'id' => '',
		);

		$this->atts    = shortcode_atts( $defaults, $atts, 'tribe_events' );
		$this->content = $content;

	}

	/**
	 * Parse the inline content and return it to the content
	 */
	protected function parse() {

		$parsed_content = new Tribe__Events__Pro__Shortcodes__Inline__Parser( $this );

		$this->content = $parsed_content->output();

	}

	/**
	 * Send Content into Output Property
	 */
	public function render() {

		/**
		 * Filter the Output of the inline shortcode
		 *
		 * @param string                                       $html
		 * @param Tribe__Events__Pro__Shortcodes__Tribe_Inline $shortcode
		 */
		$this->output = apply_filters( 'tribe_events_pro_tribe_inline_shortcode_output', $this->content, $this );

	}

	/**
	 * Return Parsed Inline Content
	 *
	 * @return string
	 */
	public function output() {
		return $this->output;
	}

}
