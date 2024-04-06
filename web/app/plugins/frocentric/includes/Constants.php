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
	 * The option key for persisting the content classifier's state
	 */
	const CLASSIFIER_STATE_OPTION = 'frocentric_classifier_state';

	/**
	 * The default stop word set for the content classifier.
	 */
	const CLASSIFIER_STOP_WORDS = array(
		'a',
		'afternoon',
		'all',
		'am',
		'an',
		'and',
		'are',
		'as',
		'at',
		'b',
		'be',
		'black',
		'bst',
		'by',
		'but',
		'c',
		'can',
		'cannot',
		"can't",
		'com',
		'd',
		'day',
		'did',
		'do',
		"don't",
		'dr',
		'e',
		'est',
		'evening',
		'f',
		'for',
		'from',
		'g',
		'get',
		'gmt',
		'h',
		'has',
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
		'like',
		'll',
		'm',
		'me',
		'month',
		'most',
		'my',
		'n',
		'new',
		'night',
		'no',
		'not',
		'now',
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
		'put',
		'q',
		'r',
		're',
		's',
		'so',
		'some',
		't',
		'tech',
		'technology',
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
		'want',
		'was',
		'we',
		'week',
		"we've",
		'where',
		'will',
		'win',
		'with',
		'www',
		'x',
		'y',
		'year',
		'yes',
		'you',
		'your',
		'yours',
		'z',
	);
}
