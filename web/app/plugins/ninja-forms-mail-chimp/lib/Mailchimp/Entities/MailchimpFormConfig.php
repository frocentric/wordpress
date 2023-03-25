<?php


namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * This class describes a form used for Mailchimp list signup
 *
 * It is MUCH to tied to Caldera Forms
 */
class MailchimpFormConfig extends SimpleEntity
{

	/** @var string */
	protected $id;
	/** @var string */
	protected $name;
	/** @var array */
	protected $fields;
	/** @var array */
	protected $processor;

	/**
	 * Get the form's procesors
	 *
	 * @return array
	 */
	public function getProcessors()
	{
		return [$this->processor];
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return is_string($this->id) ? $this->id : '';
	}

	/**
	 * @param string $id
	 * @return MailchimpFormConfig
	 */
	public function setId(string $id): MailchimpFormConfig
	{
		$this->id = $id;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return is_string($this->name) ? $this->name : '';
	}

	/**
	 * @param string $name
	 * @return MailchimpFormConfig
	 */
	public function setName(string $name): MailchimpFormConfig
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getFields(): array
	{
		return is_array($this->fields) ? $this->fields : [];
	}

	/**
	 * @param array $fields
	 * @return MailchimpFormConfig
	 */
	public function setFields(array $fields): MailchimpFormConfig
	{
		$this->fields = $fields;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getProcessor(): array
	{
		return $this->processor;
	}

	/**
	 * @param array $processor
	 */
	public function setProcessor(array $processor): MailchimpFormConfig
	{
		$this->processor = $processor;
		return $this;
	}


	/**
	 * @return string
	 */
	protected function getSubmitButtonId(): string
	{
		return 'mc-submit';
	}

	protected function getEmailFieldId(): string
	{
		return 'mc-email';
	}

	/**
	 * @inheritDoc
	 */
	public function toArray(): array
	{
		$array = parent::toArray();
		$procesor = $array['processor'];
		$array['processors'] = [$procesor];
		unset($array['processor']);
		$array['fields'][] = [
			'fieldType' => 'submit',
			'fieldId' => $this->getSubmitButtonId(),
			'label' => 'Subscribe'
		];
		$array['fields'][] = [
			'fieldType' => 'email',
			'fieldId' => $this->getEmailFieldId(),
			'label' => 'Email'
		];
		$array['rows'] = [
			[
				'rowId' => 'r1',
				'columns' => [
					[
						'columnId' => 'r1-c1',
						'width' => '1/2',
						'fields' => [$this->getEmailFieldId()]
					],
					[
						'columnId' => 'r1-c2',
						'width' => '1/2',
						'fields' => [$this->getProcessor()['mergeFields'][0]]
					]
				]
			]
		];

		if (isset($this->getProcessor()['mergeFields'][1])) {
			$columns = [
				[
					'columnId' => 'r2-c1',
					'width' => '1/2',
					'fields' => [$this->getProcessor()['mergeFields'][1]]
				],


			];
			if (isset($this->getProcessor()['mergeFields'][2])) {
				$columns[] = [
					'columnId' => 'r2-c2',
					'width' => '1/2',
					'fields' => [$this->getProcessor()['mergeFields'][2]]
				];
			}
			$array['rows'][] = [
				'rowId' => 'r2',
				'columns' => $columns
			];
		};
		$groupFields = $this->getProcessor()['groupFields'];
		if (!empty($groupFields)) {
			foreach ($groupFields as $fieldId) {
				$array['rows'][] = [
					'rowId' => "r-$fieldId",
					'columns' => [
						[
							'columnId' => $fieldId,
							'width' => '1/1',
							'fields' => [$fieldId]
						],
					]
				];
			}
		}
		$array['rows'][] = [
			'rowId' => "r-0",
			'columns' => [
				[
					'columnId' => 'r0-c1',
					'width' => '1/1',
					'fields' => [$this->getSubmitButtonId()]
				],
			]
		];

		return $array;
	}
}
