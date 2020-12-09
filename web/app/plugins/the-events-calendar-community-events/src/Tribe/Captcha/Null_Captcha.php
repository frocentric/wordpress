<?php

/**
 * Class Tribe__Events__Community__Captcha__Null_Captcha
 *
 * This is the captcha plugin used when all others have been disabled.
 * To force the use of this plugin, add this filter to your theme or
 * an mu-plugin:
 *
 * add_filter( 'tribe_community_events_captcha_plugin', '__return_null' );
 */
class Tribe__Events__Community__Captcha__Null_Captcha
	extends Tribe__Events__Community__Captcha__Abstract_Captcha {

	public function init() {
		// do nothing, say nothing, be nothing
	}
}