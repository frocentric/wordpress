<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Entity that constructs a Ninja forms modal
 *
 */
class Modal extends SimpleEntity
{

	/**
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 *
	 * @var string
	 */
	protected $templateDesc = '';

	/**
	 *
	 * @var string
	 */
	protected $type = 'ad';

	/**
	 *
	 * @var string
	 */
	protected $modalTitle = '';

	/**
	 *
	 * @var string
	 */
	protected $modalContent = '';

	/**
	 * Set  modal content
	 * @param string $content
	 * @return \NFMailchimp\NinjaForms\Mailchimp\Entities\Modal
	 */
	public function setModalContent(string $content): Modal
	{
		$this->modalContent = $content;

		return $this;
	}

	/**
	 * Append modal content
	 * @param string $content
	 * @return \NFMailchimp\NinjaForms\Mailchimp\Entities\Modal
	 */
	public function appendModalContent(string $content): Modal
	{
		$this->modalContent .= $content;

		return $this;
	}

	/** @inheritdoc */
	public function toArray(): array
	{
		$vars = get_object_vars($this);
		$array = [];
		foreach ($vars as $property => $value) {
			if (is_object($value) && is_callable([$value, 'toArray'])) {
				$value = $value->toArray();
			}
			$array[$property] = $value;
		}
		$array['template-desc']=$array['templateDesc'];
		$array['modal-title']=$array['modalTitle'];
		$array['modal-content']=$array['modalContent'];
		
		return $array;
	}

	/**
	 * Get modal Id
	 * @return string
	 */
	public function getId(): string
	{

		return $this->id;
	}
}
