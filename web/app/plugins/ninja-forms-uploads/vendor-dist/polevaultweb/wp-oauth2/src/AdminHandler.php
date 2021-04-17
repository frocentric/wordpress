<?php

namespace NF_FU_VENDOR\Polevaultweb\WPOAuth2;

class AdminHandler
{
    /**
     * @var string
     */
    protected $redirect;
    /**
     * @var TokenManager
     */
    protected $token_manager;
    /**
     * @var string
     */
    protected $openssl_encrypt_method;
    /**
     * Admin_Handler constructor.
     *
     * @param TokenManager $token_manager
     * @param string       $redirect
     * @param string       $openssl_encrypt_method
     */
    public function __construct($token_manager, $redirect, $openssl_encrypt_method)
    {
        $this->token_manager = $token_manager;
        $this->redirect = $redirect;
        $this->openssl_encrypt_method = $openssl_encrypt_method;
    }
    public function init()
    {
        add_action('admin_init', array($this, 'handle_redirect'));
        add_action('admin_init', array($this, 'handle_disconnect'));
        add_action('admin_init', array($this, 'handle_render_notice'));
    }
    public function handle_render_notice()
    {
        if (\defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        if (!$this->is_callback_page()) {
            return;
        }
        $notice = \filter_input(\INPUT_GET, 'notice');
        if (empty($notice)) {
            return;
        }
        add_action('admin_notices', array($this, 'render_' . $notice . '_notice'));
    }
    public function handle_disconnect()
    {
        if (\defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        if (!$this->is_callback_page()) {
            return;
        }
        $provider = \filter_input(\INPUT_GET, 'wp-oauth2');
        if (empty($provider)) {
            return;
        }
        $action = \filter_input(\INPUT_GET, 'action');
        if (empty($action) || 'disconnect' !== $action) {
            return;
        }
        $this->token_manager->remove_access_token($provider);
        $this->redirect('disconnection', $provider);
    }
    protected function redirect($notice = null, $provider = '')
    {
        $url = add_query_arg(array('notice' => $notice, 'wp-oauth2' => $provider), $this->redirect);
        wp_redirect($url);
        exit;
    }
    protected function is_callback_page()
    {
        $parts = \parse_url($this->redirect);
        if (!isset($parts['query'])) {
            // Check for full path? admin_url?
        }
        global $pagenow;
        if (!isset($pagenow)) {
            return \false;
        }
        if ($pagenow !== \basename($parts['path'])) {
            return \false;
        }
        \parse_str($parts['query'], $query);
        foreach ($query as $key => $value) {
            $param = \filter_input(\INPUT_GET, $key);
            if (empty($param)) {
                return \false;
            }
            if ($param != $value) {
                return \false;
            }
        }
        return \true;
    }
    public function handle_redirect()
    {
        if (\defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        if (!$this->is_callback_page()) {
            return;
        }
        $provider = \filter_input(\INPUT_GET, 'wp-oauth2');
        if (empty($provider)) {
            return;
        }
        $action = \filter_input(\INPUT_GET, 'action');
        if (empty($action) || 'connect' !== $action) {
            return;
        }
        $error = \filter_input(\INPUT_GET, 'error');
        if ($error) {
            // Show error notice
            $this->redirect('error', $provider);
        }
        $token = \filter_input(\INPUT_GET, 'token');
        $iv = \filter_input(\INPUT_GET, 'iv');
        if (empty($token) || empty($iv)) {
            $this->redirect('error');
        }
        $method = $this->openssl_encrypt_method;
        $keys = get_site_transient('wp-oauth2-key');
        if (!isset($keys[$provider])) {
            $this->redirect('error', $provider);
        }
        $key = $keys[$provider];
        $token = \openssl_decrypt($token, $method, $key, 0, \urldecode($iv));
        if (empty($token)) {
            $this->redirect('error', $provider);
        }
        $refresh_token_data = \filter_input(\INPUT_GET, 'refresh_token');
        $refresh_token = null;
        if ($refresh_token_data) {
            $refresh_token = \openssl_decrypt($refresh_token_data, $method, $key, 0, \urldecode($iv));
        }
        $expires = \filter_input(\INPUT_GET, 'expires', \FILTER_VALIDATE_INT);
        $token = new \NF_FU_VENDOR\Polevaultweb\WPOAuth2\AccessToken($provider, $token, $refresh_token, $expires);
        $token->save();
        $this->redirect('connection', $provider);
    }
    protected function get_provider_display_name()
    {
        $provider = \filter_input(\INPUT_GET, 'wp-oauth2');
        $name = \ucwords(\str_replace(array('_', '-'), ' ', $provider));
        return apply_filters('pvw_wp_oauth2_provider_display_name', $name, $provider);
    }
    public function render_error_notice()
    {
        $provider = $this->get_provider_display_name();
        $error_description = \filter_input(\INPUT_GET, 'error_description');
        $message = $error_description ? $error_description : __('An unknown error occurred.');
        $class = apply_filters('pvw_wp_oauth2_error_notice_class', 'error');
        \printf('<div class="' . $class . '"><p><strong>' . $provider . ' %s</strong> &mdash; %s</p></div>', __('Connection Error'), $message);
    }
    public function render_connection_notice()
    {
        $provider = $this->get_provider_display_name();
        $message = \sprintf(__('You have successfully connected with your %s account.'), $provider);
        $class = apply_filters('pvw_wp_oauth2_connection_notice_class', 'updated');
        \printf('<div class="' . $class . '"><p><strong>' . $provider . ' %s</strong> &mdash; %s</p></div>', __('Connected'), $message);
    }
    public function render_disconnection_notice()
    {
        $provider = $this->get_provider_display_name();
        $message = \sprintf(__('You have successfully disconnected your %s account.'), $provider);
        $class = apply_filters('pvw_wp_oauth2_disconnection_notice_class', 'updated');
        \printf('<div class="' . $class . '"><p><strong>' . $provider . ' %s</strong> &mdash; %s</p></div>', __('Disconnected'), $message);
    }
}
