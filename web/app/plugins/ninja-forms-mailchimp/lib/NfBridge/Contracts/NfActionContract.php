<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

// phpcs:disable

/**
 * Interface NfActionContract
 *
 * Emulates API of NF_Abstracts_Action
 */
interface NfActionContract {

    /**
     *
     *
     * @param array $action_settings
     * @return mixed
     */
    public function save($action_settings);

    /**
     *
     * @param string $action_id
     * @param int $form_id
     * @param array$data
     * @return mixed
     */
    public function process($action_id, $form_id, $data);

    /**
     * Get Timing
     *
     * Returns the timing for an action.
     *
     * @return int
     */
    public function get_timing();

    /**
     * Get Priority
     *
     * Returns the priority for an action.
     *
     * @return int
     */
    public function get_priority();

    /**
     * Get Name
     *
     * Returns the name of an action.
     *
     * @return string
     */
    public function get_name();

    /**
     * Get Nicename
     *
     * Returns the nicename of an action.
     *
     * @return string
     */
    public function get_nicename();

    /**
     * Get Section
     *
     * Returns the drawer section for an action.
     *
     * @return string
     */
    public function get_section();

    /**
     * Get Image
     *
     * Returns the url of a branded action's image.
     *
     * @return string
     */
    public function get_image();

    /**
     * Get Settings
     *
     * Returns the settings for an action.
     *
     * @return array|mixed
     */
    public function get_settings();

    /**
     * Return NF Action plugin settings 
     * @return array
     */
    public function getPluginSettings();

    /**
     * Return NF Action plugin settings group
     * @return array
     */
    public function getPluginSettingsGroup();
}
