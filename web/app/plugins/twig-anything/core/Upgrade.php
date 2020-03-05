<?php

namespace TwigAnything;

class Upgrade
{
    public static function register()
    {
        add_filter('http_response', array(get_class(), 'wpFilter_http_response'), 10, 3);
        add_filter('plugins_api', array(get_class(), 'wpFilter_plugins_api'), 10, 3);
        //add_filter('http_request_args', [get_class(), 'wpFilter_http_request_args']);
    }

    public static function wpFilter_http_response($response, $args, $url)
    {
        # Control recursion
        static $recursion = false;
        if ($recursion) {
            return $response;
        }
        if (empty($response) || !is_array($response) || !isset($response['body'])) {
            return $response;
        }

        # Guess if it's time to take action
        if ($url !== 'https://api.wordpress.org/plugins/update-check/1.1/') {
            return $response;
        }

        $body = $response['body'];
        if (empty($body)) {
            return $response;
        }
        $body = json_decode($body, true);
        if (!is_array($body) || !array_key_exists('plugins', $body)) {
            return $response;
        }
        $plugins = $body['plugins'];
        if (!is_array($plugins)) {
            return $response;
        }

        $file = 'twig-anything/twig-anything.php';

        # Never override any data returned by official WP plugins repository API
        if (isset($plugins[$file])) {
            return $response;
        }

        $recursion = true;
        $vars = self::loadPluginData();
        $recursion = false;
        if (empty($vars)) {
            return $response;
        }

        # If the new version is different to the current one, only then add the info
        if (TwigAnything::VERSION == $vars['new_version']) {
            return $response;
        }

        $upgradeInfo = new \stdClass();
        $upgradeInfo->id = $vars['id'];
        $upgradeInfo->slug = $vars['slug'];
        $upgradeInfo->new_version = $vars['new_version'];
        $upgradeInfo->url = $vars['url'];
        $upgradeInfo->package = ''; // indicate no automatic update is available
        $upgradeInfo->plugin = $file;

        $plugins[$file] = $upgradeInfo;
        $body['plugins'] = $plugins;
        $response['body'] = json_encode($body, JSON_HEX_AMP|JSON_HEX_QUOT|JSON_HEX_APOS|JSON_HEX_TAG);
        return $response;
    }

    public static function wpFilter_plugins_api($value, $action, $args)
    {
        // If for some reason value available already, do not change it
        if (!empty($value)) {
            return $value;
        }

        if ($action != 'plugin_information' || !is_object($args) || !isset($args->slug) || empty($args->slug)) {
            return $value;
        }

        $vars = self::loadPluginData();
        if (empty($vars)) {
            return $value;
        }
        return (object)$vars['info'];
    }

    private static function loadPluginData()
    {
        # Cache data retrieved to avoid multiple HTTP calls
        # within a single page load
        static $data = false;
        if ($data !== false) {
            return $data;
        }

        $upgradeUrl = "https://twiganything.com/twig-anything-version.php";

        # Request latest version signature from custom url
        # and validate response variables
        $r = wp_remote_post($upgradeUrl, array(
            'method' => 'POST',
            'timeout' => 4,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => null,
            'cookies' => array(),
            'sslverify' => false
        ));

        if (is_wp_error($r) || !isset($r['body']) || empty($r['body'])) {
            return $data = null;
        }

        $vars = json_decode($r['body'], true);

        if (empty($vars)
            || !is_array($vars)
            || count($vars) > 4
            || !isset($vars['new_version'])
            || !isset($vars['url'])
            || !isset($vars['info']))
            return $data = null;

        $vars['id'] = 54777215;
        $vars['slug'] = 'twig_anything';

        # Sanitize variables of "info"
        if (!is_array($vars['info'])) {
            $vars['info'] = array();
        }

        $info = array();
        $goodInfoKeys = array('name','slug','version','author','author_profile','contributors','requires','tested', 'compatibility','rating','rating','num_ratings','downloaded','last_updated','added','homepage', 'sections','download_link','tags');
        foreach($vars['info'] as $key => $val) {
            if (!in_array($key, $goodInfoKeys)) {
                continue;
            }
            $info[$key] = $val;
        }
        $info['slug'] = 'twig_anything';
        $info['version'] = $vars['new_version'];
        $info['download_link'] = $vars['url'];
        $vars['info'] = $info;
        return $data = $vars;
    }
}
Upgrade::register();