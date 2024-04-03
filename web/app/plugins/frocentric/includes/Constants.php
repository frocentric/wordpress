<?php
/**
 * Constants
 *
 * @class       Constants
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Constants class
 */
final class Constants {

	/**
	 * Represents a request to the admin area
	 */
	const ADMIN_REQUEST = 'admin';

	/**
	 * Represents an AJAX request
	 */
	const AJAX_REQUEST = 'ajax';

	/**
	 * Represents a cron request
	 */
	const CRON_REQUEST = 'cron';

	/**
	 * Defines the taxonomies that are represented as Discourse tags
	 */
	const DISCOURSE_TAG_TAXONOMIES = array( 'discipline', 'interest' );

	/**
	 * Represents a request to the front end
	 */
	const FRONTEND_REQUEST = 'frontend';

	/**
	 * Represents a request to the login page
	 */
	const LOGIN_REQUEST = 'login';

	/**
	 * Set the minimum required versions for the plugin.
	 */
	const PLUGIN_REQUIREMENTS = array(
		'php_version' => '8.0',
		'wp_version'  => '6.1',
	);

	/**
	 * The default stop word set for the content classifier.
	 */
	const CLASSIFIER_STOP_WORDS = array(
		'a',
		'am',
		'an',
		'and',
		'are',
		'as',
		'at',
		'b',
		'be',
		'bst',
		'by',
		'but',
		'c',
		'com',
		'd',
		'did',
		'do',
		"don't",
		'dr',
		'e',
		'est',
		'f',
		'for',
		'g',
		'gmt',
		'h',
		'have',
		'http',
		'https',
		'i',
		'if',
		'in',
		'io',
		'is',
		'it',
		'j',
		'k',
		'l',
		'm',
		'me',
		'my',
		'n',
		'not',
		'o',
		'of',
		'on',
		'or',
		'org',
		'our',
		'ours',
		'out',
		'p',
		'pm',
		'q',
		'r',
		're',
		's',
		'so',
		't',
		'th',
		'that',
		'the',
		'they',
		'this',
		'to',
		'u',
		'up',
		'us',
		'utc',
		'v',
		'via',
		'w',
		'was',
		'we',
		"we've",
		'with',
		'www',
		'x',
		'y',
		'you',
		'your',
		'yours',
		'z',
	);
}
