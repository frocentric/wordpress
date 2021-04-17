<?php

namespace NF_FU_VENDOR\Polevaultweb\WPDropboxAPI;

class API
{
    const API_URL_BASE = 'https://api.dropboxapi.com/2/';
    const CONTENT_URL_BASE = 'https://content.dropboxapi.com/2/';
    protected $access_token;
    public function __construct($access_token = null)
    {
        $this->access_token = $access_token;
    }
    public function get_account_info()
    {
        $endpoint = 'users/get_current_account';
        $args = array('headers' => array('Content-Type' => 'application/json'), 'body' => \json_encode(null));
        return $this->post($endpoint, $args, \false);
    }
    public function put_file($file, $filename, $path)
    {
        $endpoint = 'files/upload';
        $path = trailingslashit($path) . $filename;
        $path = '/' . \ltrim($path, '/');
        $data = array('path' => $path, 'mode' => 'overwrite');
        $resource = @\fopen($file, 'r');
        $file_size = \filesize($file);
        $file_data = \fread($resource, $file_size);
        $args = array('headers' => array('Content-Type' => 'application/octet-stream', 'Dropbox-API-Arg' => \json_encode($data)), 'body' => $file_data, 'timeout' => 60);
        return $this->post($endpoint, $args);
    }
    /**
     * @param string $file_data
     * @param bool   $close
     *
     * @return bool|UploadSessionCursor
     */
    public function upload_session_start($file_data, $close = \false)
    {
        $endpoint = 'files/upload_session/start';
        $arguments = \compact('close');
        $args = array('headers' => array('Content-Type' => 'application/octet-stream', 'Dropbox-API-Arg' => \json_encode($arguments)), 'body' => $file_data, 'timeout' => 60);
        $response = $this->post($endpoint, $args);
        if (empty($response) || !isset($response->session_id)) {
            return \false;
        }
        return new \NF_FU_VENDOR\Polevaultweb\WPDropboxAPI\UploadSessionCursor($response->session_id, \strlen($file_data));
    }
    /**
     * @param UploadSessionCursor $cursor
     * @param string              $file_data
     * @param bool                $close
     *
     * @return bool|mixed
     */
    public function upload_session_append($cursor, $file_data, $close = \false)
    {
        $endpoint = 'files/upload_session/append_v2';
        $arguments = \compact('cursor', 'close');
        $args = array('headers' => array('Content-Type' => 'application/octet-stream', 'Dropbox-API-Arg' => \json_encode($arguments)), 'body' => $file_data, 'timeout' => 60);
        $response = $this->post($endpoint, $args);
        $cursor->offset += \strlen($file_data);
        return $cursor;
    }
    /**
     * @param UploadSessionCursor $cursor
     * @param string              $path
     * @param string              $mode
     * @param bool                $autorename
     * @param bool                $mute
     *
     * @return bool
     */
    public function upload_session_finish($cursor, $path, $mode = 'add', $autorename = \false, $mute = \false)
    {
        $endpoint = 'files/upload_session/finish';
        $arguments = \compact('cursor');
        $arguments['commit'] = \compact('path', 'mode', 'autorename', 'mute');
        $args = array('headers' => array('Content-Type' => 'application/octet-stream', 'Dropbox-API-Arg' => \json_encode($arguments)), 'timeout' => 60);
        $response = $this->post($endpoint, $args);
        return $response;
    }
    public function get_url($path)
    {
        $endpoint = 'files/get_temporary_link';
        $path = '/' . \ltrim($path, '/');
        $args = array('headers' => array('Content-Type' => 'application/json'), 'body' => \json_encode(array('path' => $path)));
        return $this->post($endpoint, $args, \false);
    }
    public function access_token_upgrade($app_key, $app_secret, $token, $token_secret)
    {
        $endpoint = 'auth/token/from_oauth1';
        $args = array('headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Basic ' . \base64_encode($app_key . ':' . $app_secret)), 'body' => \json_encode(array('oauth1_token' => $token, 'oauth1_token_secret' => $token_secret)));
        $new_token = $this->post($endpoint, $args, \false, \false);
        if ($new_token && isset($new_token->oauth2_token)) {
            return $new_token->oauth2_token;
        }
        return \false;
    }
    protected function post($endpoint, $args, $content_api = \true, $auth = \true)
    {
        $url = ($content_api ? self::CONTENT_URL_BASE : self::API_URL_BASE) . $endpoint;
        $defaults = array();
        if ($auth) {
            $defaults = array('headers' => array('Authorization' => 'Bearer ' . $this->access_token));
        }
        $args = \array_merge_recursive($args, $defaults);
        $result = wp_remote_post($url, $args);
        $response = wp_remote_retrieve_body($result);
        if (empty($response)) {
            return \false;
        }
        $body = \json_decode($response);
        if (empty($body)) {
            return \false;
        }
        return $body;
    }
}
