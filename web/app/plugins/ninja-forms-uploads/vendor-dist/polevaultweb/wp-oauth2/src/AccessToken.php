<?php

namespace NF_FU_VENDOR\Polevaultweb\WPOAuth2;

class AccessToken
{
    /**
     * @var string
     */
    protected $provider;
    /**
     * @var null|string
     */
    protected $token;
    /**
     * @var null|string
     */
    protected $refresh_token;
    const OPTION_KEY = 'wp-oauth2-tokens';
    /**
     * @var int|null
     */
    protected $expires;
    /**
     * AccessToken constructor.
     *
     * @param string      $provider
     * @param null|string $token
     * @param null|string $refresh_token
     * @param null|int    $expires
     */
    public function __construct($provider, $token = null, $refresh_token = null, $expires = null)
    {
        $this->provider = $provider;
        if ($token) {
            $this->token = $token;
        }
        if ($refresh_token) {
            $this->refresh_token = $refresh_token;
        }
        if ($expires) {
            $this->expires = $expires;
        }
    }
    /**
     * @return array
     */
    protected function get_tokens()
    {
        return get_site_option(self::OPTION_KEY, array());
    }
    /**
     * @param array $tokens
     *
     * @return mixed
     */
    protected function save_tokens($tokens)
    {
        return update_site_option(self::OPTION_KEY, $tokens);
    }
    /**
     * @param string $type
     *
     * @return bool|mixed
     */
    public function get($type = 'token')
    {
        $tokens = $this->get_tokens();
        if (isset($tokens[$this->provider])) {
            $data = $tokens[$this->provider];
            if (empty($type)) {
                return $data;
            }
            if (!\is_array($data)) {
                return $type == 'token' ? $data : \false;
            }
            return isset($data[$type]) ? $data[$type] : \false;
        }
        return \false;
    }
    public function save()
    {
        $tokens = $this->get_tokens();
        $data = array('token' => $this->token);
        if (!empty($this->refresh_token)) {
            $data['refresh_token'] = $this->refresh_token;
        }
        if (!empty($this->expires)) {
            $data['expires'] = $this->expires;
        }
        $tokens[$this->provider] = $data;
        $this->save_tokens($tokens);
    }
    public function delete()
    {
        $tokens = $this->get_tokens();
        unset($tokens[$this->provider]);
        $this->save_tokens($tokens);
    }
}
