<?php
/**
 * End-to-end test suite
 */

namespace Tests\EndToEnd;

use Tests\Support\EndToEndTester;

class ActivationCest {

	public function test_homepage_works( EndToEndTester $i ): void {
		$i->amOnPage( '/' );
		$i->seeElement( 'body' );
	}

	public function test_can_login_as_admin( EndToEndTester $i ): void {
		$i->loginAsAdmin();
		$i->amOnAdminPage( '/' );
		$i->seeElement( 'body.wp-admin' );
	}
}
