<?php

namespace Tests\Unit;

use Frocentric\Customizations\Tribe;

class TribeTest extends \Codeception\Test\Unit {

	protected function _before() {
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', '' );
		}
	}

	protected function strip_whitespace( $html) {
		$remove = array("\t", "\n", "\r", "\0", "\x0B");

		return str_replace($remove, '', $html);
	}

	// tests
	public function testEventbriteMarkupFormatted() {
		$markup = <<<MARKUP
		<div>Join us in Berlin for our new Germany chapter Launch</div>
		<div style="margin-top: 20px;">
		<div style="margin: 20px 0; line-height: 22px;"><img style="max-width: 100%; height: auto;" src="https://d3kjg0zldfafgn.cloudfront.net/uploads/2021/07/frocentric-tech-colour-black.svg" alt="" /></div>
		<div style="margin: 20px 10px; font-size: 15px; line-height: 22px; font-weight: 400; text-align: left;">
		<h3>A heading</h3>

		A paragraph of text would go here.
		</div>
		</div>
MARKUP;
		$cleaned_markup = <<<MARKUP2
		<div>Join us in Berlin for our new Germany chapter Launch</div>
		<div style="margin: 20px 0; line-height: 22px;"><img style="max-width: 100%; height: auto;" src="https://d3kjg0zldfafgn.cloudfront.net/uploads/2021/07/frocentric-tech-colour-black.svg" alt=""></div>
		<h3>A heading</h3>

		A paragraph of text would go here.
MARKUP2;

		$returned_markup = Tribe::fix_eventbrite_event_markup( $markup );
		verify( $this->strip_whitespace( $returned_markup ) )->equals( $this->strip_whitespace( $cleaned_markup ) );
	}
}
