<?php
/**
 * OpenAI client
 *
 * @class       Utils
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric\Customizations\ContentClassifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * An OpenAI API client that allows interation with the API.
 */
class OpenAiApi {
	public static function get_response( $content, $model = 'gpt-4-turbo' ) {
		$client = \OpenAI::client( CLASSIFIER_RELEVANCE_THRESHOLD );

		$result = $client->chat()->create(
			array(
				'model' => $model,
				'messages' => array(
					array(
						'role' => 'user',
						'content' => $content,
					),
				),
			)
		);

		return $result->choices[0]->message->content;
	}
}
