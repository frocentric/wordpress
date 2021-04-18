<?php

namespace NF_FU_VENDOR\Polevaultweb\WPOAuth2;

class WPOAuth2
{
    /**
     * @var WPOAuth2
     */
    private static $instance;
    /**
     * @var string
     */
    protected $oauth_proxy_url;
    /**
     * @var TokenManager
     */
    public $token_manager;
    /**
     * @param string $oauth_proxy_url
     *
     * @return WPOAuth2 Instance
     */
    public static function instance($oauth_proxy_url)
    {
        if (!isset(self::$instance) && !self::$instance instanceof \NF_FU_VENDOR\Polevaultweb\WPOAuth2\WPOAuth2) {
            self::$instance = new \NF_FU_VENDOR\Polevaultweb\WPOAuth2\WPOAuth2();
            self::$instance->init($oauth_proxy_url);
        }
        return self::$instance;
    }
    /**
     * @param string $oauth_proxy_url
     */
    public function init($oauth_proxy_url)
    {
        $this->oauth_proxy_url = $oauth_proxy_url;
        $this->token_manager = new \NF_FU_VENDOR\Polevaultweb\WPOAuth2\TokenManager();
    }
    /**
     * @return string
     */
    public function get_oauth_proxy_url()
    {
        return $this->oauth_proxy_url;
    }
    /**
     * Register the admin hooks for the plugin.
     *
     * @param string $redirect_url
     */
    public function register_admin_handler($redirect_url)
    {
        $admin_handler = new \NF_FU_VENDOR\Polevaultweb\WPOAuth2\AdminHandler($this->token_manager, $redirect_url, $this->get_method());
        $admin_handler->init();
    }
    /**
     * Get the URL to the proxy server to redirect to, to start the auth process.
     *
     * @param string $provider
     * @param string $client_id
     * @param string $callback_url
     * @param array  $args
     *
     * @return string
     */
    public function get_authorize_url($provider, $client_id, $callback_url, $args = array())
    {
        $params = array('redirect_uri' => $callback_url, 'client_id' => $client_id, 'key' => $this->generate_key($provider), 'method' => $this->get_method());
        if (!empty($args)) {
            $params['args'] = \base64_encode(\serialize($args));
        }
        $url = $this->oauth_proxy_url . '?' . \http_build_query($params, '', '&');
        return $url;
    }
    /**
     * Send a refresh token to the proxy server for a client and get a new access token back.
     *
     * @param string $client_id
     * @param string $provider
     *
     * @return bool|string
     */
    public function refresh_access_token($client_id, $provider)
    {
        $refresh_token = $this->token_manager->get_refresh_token($provider);
        $params = array('client_id' => $client_id, 'refresh_token' => $refresh_token);
        $url = $this->oauth_proxy_url . '/refresh?' . \http_build_query($params, '', '&');
        $request = wp_remote_get($url);
        if (is_wp_error($request)) {
            return \false;
            // Bail early
        }
        $body = wp_remote_retrieve_body($request);
        $data = \json_decode($body, \true);
        if (!$data || !isset($data['token'])) {
            return \false;
        }
        $expires = isset($data['expires']) ? $data['expires'] : null;
        $this->token_manager->set_access_token($provider, $data['token'], $refresh_token, $expires);
        return $data['token'];
    }
    public function get_method()
    {
        $methods = \openssl_get_cipher_methods();
        return $methods[0];
    }
    /**
     * @param string $provider
     *
     * @return string
     */
    protected function generate_key($provider)
    {
        $keys = get_site_transient('wp-oauth2-key');
        if (!\is_array($keys)) {
            $keys = array();
        }
        $key = wp_generate_password();
        $keys[$provider] = $key;
        set_site_transient('wp-oauth2-key', $keys);
        return $key;
    }
    public function get_disconnect_url($provider, $url)
    {
        $url = add_query_arg(array('wp-oauth2' => $provider, 'action' => 'disconnect'), $url);
        return $url;
    }
    public function disconnect($provider)
    {
        $this->token_manager->remove_access_token($provider);
    }
    public function is_authorized($provider)
    {
        $token = $this->token_manager->get_access_token($provider);
        return (bool) $token;
    }
    /**
     * Protected constructor to prevent creating a new instance of the
     * class via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }
    /**
     * As this class is a singleton it should not be clone-able
     */
    protected function __clone()
    {
    }
    /**
     * As this class is a singleton it should not be able to be unserialized
     */
    public function __wakeup()
    {
    }
}
