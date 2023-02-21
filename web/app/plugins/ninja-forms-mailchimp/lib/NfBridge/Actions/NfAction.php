<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\FormProcessorsFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionProcessHandlerContract;
use NFMailchimp\EmailCRM\Shared\Contracts\WPContract;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionEntity;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionSettings;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSettings;
use NFMailchimp\EmailCRM\WpBridge\Contracts\WpHooksContract;

/**
 *
 */
class NfAction implements NfActionContract
{

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $nicename = '';

	/**
	 * @var string
	 */
	protected $section = 'installed';

	/**
	 * @var string
	 */
	protected $image = '';

	/**
	 * @var array
	 */
	protected $tags = [];

	/**
	 * @var string
	 */
	protected $timing = 'normal';

	/**
	 * @var int
	 */
	protected $priority = '10';

	/**
	 * @var array
	 */
	protected $settings = [];

	/**
	 * @var array
	 */
	protected $settingsAll = ['label', 'active'];

	/**
	 * @var array
	 */
	protected $settingsExclude = [];

	/**
	 * @var array
	 */
	protected $settingsOnly = [];

	/**
	 *
	 * @var WpHooksContract
	 */
	protected $wpHooks;

	/**
	 * Site-wide plugin settings
	 *
	 * Includes such items as API keys
	 * different than action settings used during form construction
	 *
	 * @var array
	 */
	protected $pluginSettings = [];

	/**
	 * Group for the site-wide plugin settings
	 *
	 * @var array
	 */
	protected $pluginSettingsGroup = [];

	/**
	 *
	 * @var ActionEntity
	 */
	protected $actionEntity;

	/**
	 *
	 * @var NfActionProcessHandlerContract
	 */
	protected $processHandler;

	/**
	 *
	 * @var FormProcessorsFactoryContract
	 */
	protected $formProcessorsFactory;

	/**
	 * Collection of NF bridge action settings
	 * @var ActionSettings
	 */
	protected $actionSettings;

	/**
	 * Collection of NF bridge API settings
	 * @var ApiSettings
	 */
	protected $apiSettings;

	/**
	 * Constructs NF Action for Registration
	 *
	 * @param ActionEntity $actionEntity
	 * @param NfActionProcessHandlerContract $processHandler
	 * @param FormProcessorsFactoryContract $formProcessorsFactory
	 * @param WpHooksContract $wpHooks
	 */
	public function __construct(
		ActionEntity $actionEntity,
		NfActionProcessHandlerContract $processHandler,
		FormProcessorsFactoryContract $formProcessorsFactory,
		WpHooksContract $wpHooks
	) {

		$this->actionEntity = $actionEntity;

		$this->processHandler = $processHandler;

		$this->formProcessorsFactory = $formProcessorsFactory;

		$this->wpHooks = $wpHooks;

		$this->extractParameters();

		$this->initActionSettings();

		$this->constructPluginSettings();

		$this->constructPluginSettingsGroup();
	}

	/**
	 * Extract action entity to properties
	 */
	protected function extractParameters()
	{
		$this->name = $this->actionEntity->getName();

		$this->nicename = $this->actionEntity->getNicename();

		$this->tags = $this->actionEntity->getTags();

		$this->timing = $this->actionEntity->getTiming();

		$this->priority = $this->actionEntity->getPriority();

		$this->actionSettings = $this->actionEntity->getActionSettings();

		$this->apiSettings = $this->actionEntity->getApiSettings();
	}

	/**
	 * Initialize action settings
	 */
	public function initActionSettings()
	{

		$this->settingsAll = $this->wpHooks->applyFilters('ninja_forms_actions_settings_all', $this->settingsAll);

		if (!empty($this->settingsOnly)) {
			$this->settings = array_merge($this->settings, $this->settingsOnly);
		} else {
			$this->settings = array_merge($this->settingsAll, $this->settings);
			$this->settings = array_diff($this->settings, $this->settingsExclude);
		}

		$this->settings = $this->loadSettings($this->settings);

		$this->settings = array_merge($this->settings, $this->actionSettings->outputConfiguration());
	}

	/**
	 * Construct plugins settings for the NF Action
	 */
	protected function constructPluginSettings()
	{

		$settingsConfigObj = $this->formProcessorsFactory->getConfigureApiSettings($this->apiSettings);

		$this->pluginSettings = $settingsConfigObj->getSettingsConfig();
	}

	/**
	 * Construct group for the plugin settings
	 */
	protected function constructPluginSettingsGroup()
	{

		$id = $this->apiSettings->getId();

		$label = $this->apiSettings->getLabel();

		$this->pluginSettingsGroup = [
			'id' => $id,
			'label' => $label
		];
	}

	/**
	 * Return NF Action plugin settings
	 * @return array
	 */
	public function getPluginSettings()
	{

		$id = $this->apiSettings->getId();

		$nfPluginSettings[$id] = $this->pluginSettings;

		return $nfPluginSettings;
	}

	/**
	 * Return NF Action plugin settings group
	 * @return array
	 */
	public function getPluginSettingsGroup()
	{
		$id = $this->apiSettings->getId();

		$nfPluginGroup[$id] = $this->pluginSettingsGroup;

		return $nfPluginGroup;
	}

	/**
	 * Save
	 */
	public function save($action_settings)
	{
		//@todo: Add save object
	}

	/**
	 * NF method called at form submission
	 *
	 * @param array $actionSettings NF Action settings at form submission
	 * @param int $formId
	 * @param array $data NF $data passed at form submission
	 * @return array
	 */
	public function process($actionSettings, $formId, $data)
	{

				$extractedData =$this->processHandler->extractFormFieldProcessingData($data);
				
		if (!empty($extractedData)) {
			$actionSettings= array_merge($actionSettings, $extractedData);
		}
				
		$submissionData = $this->formProcessorsFactory->getSubmissionData(
			$actionSettings,
			$this->actionSettings,
			$this->apiSettings
		);

		$form = $this->formProcessorsFactory
				->getForm(Ninja_Forms()->form($formId));

		$this->processHandler->handle($submissionData, $form);

				// after developing process data, append to $data
		return $data;
	}

	/**
	 * Get Timing
	 *
	 * Returns the timing for an action.
	 *
	 * @return mixed
	 */
	public function get_timing()
	{
		$timing = array('early' => -1, 'normal' => 0, 'late' => 1);

		return intval($timing[$this->timing]);
	}

	/**
	 * Get Priority
	 *
	 * Returns the priority for an action.
	 *
	 * @return int
	 */
	public function get_priority()
	{
		return intval($this->priority);
	}

	/**
	 * Get Name
	 *
	 * Returns the name of an action.
	 *
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * Get Nicename
	 *
	 * Returns the nicename of an action.
	 *
	 * @return string
	 */
	public function get_nicename()
	{
		return $this->nicename;
	}

	/**
	 * Get Section
	 *
	 * Returns the drawer section for an action.
	 *
	 * @return string
	 */
	public function get_section()
	{
		return $this->section;
	}

	/**
	 * Get Image
	 *
	 * Returns the url of a branded action's image.
	 *
	 * @return string
	 */
	public function get_image()
	{
		return $this->image;
	}

	/**
	 * Get Settings
	 *
	 * Returns the settings for an action.
	 *
	 * @return array|mixed
	 */
	public function get_settings()
	{
		return $this->settings;
	}

	/**
	 * Sort Actions
	 *
	 * A static method for sorting two actions by timing, then priority.
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public static function sort_actions($a, $b)
	{
		if (!isset(Ninja_Forms()->actions[$a->get_setting('type')])) {
			return 1;
		}
		if (!isset(Ninja_Forms()->actions[$b->get_setting('type')])) {
			return 1;
		}

		$a->timing = Ninja_Forms()->actions[$a->get_setting('type')]->get_timing();
		$a->priority = Ninja_Forms()->actions[$a->get_setting('type')]->get_priority();

		$b->timing = Ninja_Forms()->actions[$b->get_setting('type')]->get_timing();
		$b->priority = Ninja_Forms()->actions[$b->get_setting('type')]->get_priority();

		// Compare Priority if Timing is the same
		if ($a->timing == $b->timing) {
			return $a->priority > $b->priority ? 1 : -1;
		}

		// Compare Timing
		return $a->timing < $b->timing ? 1 : -1;
	}

	/**
	 * Loads settings array from FieldSettings config file
	 * @param array $onlySettings
	 * @return array
	 */
	protected function loadSettings($onlySettings = array())
	{
		$settings = array();

		// Loads a settings array from the FieldSettings configuration file.
		$allSettings = \Ninja_Forms::config('ActionSettings');

		foreach ($onlySettings as $setting) {
			if (isset($allSettings[$setting])) {
				$settings[$setting] = $allSettings[$setting];
			}
		}

		return $settings;
	}
}
