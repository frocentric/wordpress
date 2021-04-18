<?php

require_once(NINJA_FORMS_UPLOADS_DIR. "/includes/lib/dropbox/API.php");
require_once(NINJA_FORMS_UPLOADS_DIR. "/includes/lib/dropbox/OAuth/Consumer/ConsumerAbstract.php");
require_once(NINJA_FORMS_UPLOADS_DIR. "/includes/lib/dropbox/OAuth/Consumer/Curl.php");

class nf_dropbox
{
    const CONSUMER_KEY = 'g80jscev5iosghi';
    const CONSUMER_SECRET = 'hsy0xtrr3gjkd0i';
    const RETRY_COUNT = 3;

    private static $instance = null;

    private
        $dropbox,
        $request_token,
        $access_token,
        $oauth_state,
        $oauth,
        $account_info_cache,
        $settings,
        $directory_cache = array()
    ;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->settings = get_option( 'ninja_forms_settings' );

        if (!extension_loaded('curl')) {
            throw new Exception(sprintf(
                __('The cURL extension is not loaded. %sPlease ensure its installed and activated.%s', 'ninja-forms-upload'),
                '<a href="http://php.net/manual/en/curl.installation.php">',
                '</a>'
            ));
        }

        $this->oauth = new Dropbox_OAuth_Consumer_Curl(self::CONSUMER_KEY, self::CONSUMER_SECRET);

        $this->oauth_state = $this->get_option('dropbox_oauth_state');
        $this->request_token = $this->get_token('request');
        $this->access_token = $this->get_token('access');

        if ($this->oauth_state == 'request') {
            //If we have not got an access token then we need to grab one
            try {
                $this->oauth->setToken($this->request_token);
                $this->access_token = $this->oauth->getAccessToken();
                $this->oauth_state = 'access';
                $this->oauth->setToken($this->access_token);
                $this->save_tokens();
                //Supress the error because unlink, then init should be called
            } catch (Exception $e) {}
        } elseif ($this->oauth_state == 'access') {
            $this->oauth->setToken($this->access_token);
        } else {
            //If we don't have an acess token then lets setup a new request
            $this->request_token = $this->oauth->getRequestToken();
            $this->oauth->setToken($this->request_token);
            $this->oauth_state = 'request';
            $this->save_tokens();
        }

        $this->dropbox = new Dropbox_API($this->oauth);
    }

    private function get_token($type)
    {
        $token = $this->get_option("dropbox_{$type}_token");
        $token_secret = $this->get_option("dropbox_{$type}_token_secret");

        $ret = new stdClass;
        $ret->oauth_token = null;
        $ret->oauth_token_secret = null;

        if ($token && $token_secret) {
            $ret = new stdClass;
            $ret->oauth_token = $token;
            $ret->oauth_token_secret = $token_secret;
        }

        return $ret;
    }

    public function is_authorized()
    {
        try {
            $this->get_account_info();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function get_authorize_url( $callback_url )
    {
        return $this->oauth->getAuthoriseUrl( $callback_url );
    }

    public function get_account_info()
    {
        if (!isset($this->account_info_cache)) {
            $response = $this->dropbox->accountInfo();
            $this->account_info_cache = $response['body'];
        }

        return $this->account_info_cache;
    }

    private function save_tokens()
    {
        $this->set_option('dropbox_oauth_state', $this->oauth_state);

        if ($this->request_token) {
            $this->set_option('dropbox_request_token', $this->request_token->oauth_token);
            $this->set_option('dropbox_request_token_secret', $this->request_token->oauth_token_secret);
        } else {
            $this->set_option('dropbox_request_token', null);
            $this->set_option('dropbox_request_token_secret', null);
        }

        if ($this->access_token) {
            $this->set_option('dropbox_access_token', $this->access_token->oauth_token);
            $this->set_option('dropbox_access_token_secret', $this->access_token->oauth_token_secret);
        } else {
            $this->set_option('dropbox_access_token', null);
            $this->set_option('dropbox_access_token_secret', null);
        }

        $this->save_options();

        return $this;
    }

    private function get_option( $key ) {
        if ( isset ( $key ) && isset( $this->settings[$key] ) ) {
            return $this->settings[$key];
        } else {
            return false;
        }
        
    }

    private function set_option( $key, $value ) {
        $this->settings[$key] = $value;
    }

    private function save_options() {
        update_option( 'ninja_forms_settings', $this->settings );
    }

    public function upload_file( $file, $filename, $path = '' ) {
        if ( $filename === '' ) {
            $filename = $this->remove_secret( $file );
        }
        $i = 0;
        while ( $i ++ < self::RETRY_COUNT ) {
            try {
                return $this->dropbox->putFile( $file, $filename, $path );
            } catch ( Exception $e ) {
            }
        }
        throw $e;
    }

	public function get_link( $path ) {
		$response = $this->dropbox->media( $path );
		if ( $response['code'] == 200 ) {
			return $response['body']->url;
		}

		return false;
	}

    public function chunk_upload_file($path, $file, $processed_file)
    {
        $offest = $upload_id = null;
        if ($processed_file) {
            $offest = $processed_file->offset;
            $upload_id = $processed_file->uploadid;
        }

        return $this->dropbox->chunkedUpload($file, $this->remove_secret($file), $path, true, $offest, $upload_id);
    }

    public function delete_file($file)
    {
        return $this->dropbox->delete($file);
    }

    public function create_directory($path)
    {
        try {
            $this->dropbox->create($path);
        } catch (Exception $e) {}
    }

    public function get_directory_contents($path)
    {
        if (!isset($this->directory_cache[$path])) {
            try {
                $this->directory_cache[$path] = array();
                $response = $this->dropbox->metaData($path);

                foreach ($response['body']->contents as $val) {
                    if (!$val->is_dir) {
                        $this->directory_cache[$path][] = basename($val->path);
                    }
                }
            } catch (Exception $e) {
                $this->create_directory($path);
            }
        }

        return $this->directory_cache[$path];
    }

    public function unlink_account()
    {
        $this->oauth->resetToken();
        $this->request_token = null;
        $this->access_token = null;
        $this->oauth_state = null;

        return $this->save_tokens();
    }

    public static function remove_secret($file, $basename = true)
    {
        if (preg_match('/-nf-secret$/', $file))
            $file = substr($file, 0, strrpos($file, '.'));

        if ($basename)
            return basename($file);

        return $file;
    }
}