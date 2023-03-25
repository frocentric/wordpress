<?php


namespace NFMailchimp\EmailCRM\RestApi;

use NFMailchimp\EmailCRM\RestApi\Contracts\HttpContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\HttpResponseContract;
use NFMailchimp\EmailCRM\RestApi\Contracts\ResponseContract;
use NFMailchimp\EmailCRM\RestApi\Traits\ProvidesHttpHeaders;
use NFMailchimp\EmailCRM\RestApi\Traits\ProvidesHttpMethod;

class Response implements ResponseContract
{
	use ProvidesHttpMethod, ProvidesHttpHeaders;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @var int
	 */
	protected $status;

	/**
	 * @inheritDoc
	 */
	public static function fromArray($items): HttpResponseContract
	{
		$obj = new static();
		if (isset($items['status'])) {
			$obj->setStatus($items['status']);
		}

		if (isset($items['method'])) {
			$obj->setHttpMethod($items['method']);
		}

		if (isset($items['headers'])) {
			if (is_array($items['headers'])) {
				$obj->setHeaders($items['headers']);
			}
		}

		if (isset($items['data'])) {
			if (is_array($items['data'])) {
				$obj->setData($items['data']);
			}
		}

		return $obj;
	}

	/**
	 * @inheritDoc
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @inheritDoc
	 */
	public function setData(array $data): HttpResponseContract
	{
		$this->data = $data;
		return  $this;
	}


	/**
	 * @inheritDoc
	 */
	public function getStatus(): int
	{
		return $this->status;
	}

	/**
	 * @inheritDoc
	 */
	public function setStatus(int $code): HttpResponseContract
	{
		$this->status = $code;
		return $this;
	}
}
