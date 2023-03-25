<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one Mailchimp account
 */
class Account extends MailChimpEntity
{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $apiKey;
	/**
	 * @var string
	 */
	protected $mailChimpAccountId;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	public function getNameOrId():string
	{
		return ! empty($this->name) ? $this->getName() : $this->getMailChimpAccountId();
	}
	/**
	 * @param string $name
	 *
	 * @return Account
	 */
	public function setName(string $name): Account
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return ! empty($this->id) ? $this->id : 0;
	}

	/**
	 * @param int $id
	 *
	 * @return Account
	 */
	public function setId(int $id): Account
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getApiKey(): string
	{
		if (!$this->apiKey) {
			return '';
		}
		return $this->apiKey;
	}

	/**
	 * @param string $apiKey
	 *
	 * @return Account
	 */
	public function setApiKey(string $apiKey): Account
	{
		$this->apiKey = $apiKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMailChimpAccountId(): string
	{
		return $this->mailChimpAccountId;
	}

	/**
	 * @param string $mailChimpAccountId
	 *
	 * @return Account
	 */
	public function setMailChimpAccountId(string $mailChimpAccountId): Account
	{
		$this->mailChimpAccountId = $mailChimpAccountId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getListId(): string
	{
		return '';
	}

	public static function fromArray(array $items): SimpleEntity
	{
		if (isset($items[ 'api_key'])) {
			$items['apiKey' ] = $items[ 'api_key'];
		}
		if (isset($items[ 'mailchimp_account_id'])) {
			$items['mailChimpAccountId' ] = $items[ 'mailchimp_account_id'];
		}
		return parent::fromArray($items);
	}
}
