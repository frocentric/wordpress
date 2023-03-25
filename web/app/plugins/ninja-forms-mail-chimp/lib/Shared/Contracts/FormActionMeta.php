<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Interface FormActionMeta
 *
 * This type describes the meta info -- name, author, etc -- of one Ninja Forms action or Caldera Forms processor or similar
 */
interface FormActionMeta extends Arrayable
{
	/**
	 * Get the allowed meta keys.
	 *
	 * @return array
	 */
	public static function getFieldKeys(): array;

	/**
	 * Get the processor's slug/ identifier
	 *
	 * @return string
	 */
	public function getSlug() : string;
}
