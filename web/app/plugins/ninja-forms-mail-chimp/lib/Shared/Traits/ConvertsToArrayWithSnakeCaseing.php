<?php


namespace NFMailchimp\EmailCRM\Shared\Traits;

trait ConvertsToArrayWithSnakeCaseing
{

	/** @inheritdoc */
	public function toArray(): array
	{
		$vars = get_object_vars($this);
		$array = [];
		foreach ($vars as $property => $value) {
			if (is_object($value) && is_callable([$value, 'toArray'])) {
				$value = $value->toArray();
			} elseif (is_callable([$this,$this->getterName($property)])) {
				$value = call_user_func([$this,$this->getterName($property)]);
			}

			$array[$this->snake($property)] = $value;
		}
		return $array;
	}

	protected function getterName(string $property)
	{

		return 'get'. ucfirst($property);
	}


	/**
	 * Convert camel-cased string to snake case
	 *
	 * @see https://stackoverflow.com/a/1993772
	 * @param string $str
	 * @return string
	 */
	protected function snake(string $str): string
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}
}
