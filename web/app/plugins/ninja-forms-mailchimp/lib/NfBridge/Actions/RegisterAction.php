<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\Shared\Contracts\WPContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionContract;
use NFMailchimp\EmailCRM\WpBridge\Contracts\WpHooksContract;

/**
 * Registers actions added here with Ninja Forms
 *
 */
class RegisterAction
{

	/**
	 *
	 * @var WpHooksContract
	 */
	protected $wpHooks;

	/**
	 *
	 * @var NfActionContract[]
	 */
	protected $nfActions = [];

	/**
	 * Array of plugin settings from all added actions
	 * @var array
	 */
	protected $pluginSettings = [];

	/**
	 * Array of plugin settings groups from all added actions
	 * @var array
	 */
	protected $pluginSettingsGroups = [];

	/**
	 * Register NF Actions with Ninja Forms
	 * @param WPContract
	 */
	public function __construct(WpHooksContract $wpHooks)
	{

		$this->wpHooks = $wpHooks;

		$this->wpHooks->addFilter('ninja_forms_register_actions', [$this, 'registerActions']);
		$this->wpHooks->addFilter('ninja_forms_plugin_settings', [$this, 'registerPluginSettings'], 10, 1);
		$this->wpHooks->addFilter('ninja_forms_plugin_settings_groups', [$this, 'registerPluginSettingsGroups'], 10, 1);
	}

	/**
	 * Add a NF action for registration
	 * @param NfActionContract $nfAction
	 */
	public function addNfAction(NfActionContract $nfAction)
	{
		$this->nfActions[$nfAction->get_name()] = $nfAction;
		$this->pluginSettings = array_merge($this->pluginSettings, $nfAction->getPluginSettings());
		$this->pluginSettingsGroups = array_merge($this->pluginSettingsGroups, $nfAction->getPluginSettingsGroup());
	}

	/**
	 * Register actions in the Ninja Forms action registry
	 * @param array $actions
	 * @return array
	 */
	public function registerActions($actions)
	{
		$actions = array_merge($actions, $this->nfActions);

		return $actions;
	}

	/**
	 * Merge new API settings into settings collection
	 * @param array $settings
	 * @return array
	 */
	public function registerPluginSettings($settings)
	{

		$settings = array_merge($settings, $this->pluginSettings);

		return $settings;
	}

	/**
	 * Merge new action's settings groups into settings groups collection
	 * @param array $groups
	 * @return array
	 */
	public function registerPluginSettingsGroups($groups)
	{

		$mergedGroups = array_merge($groups, $this->pluginSettingsGroups);

		return $mergedGroups;
	}
}
