<?php
/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;
use function Env\env;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', ! ( defined( 'WP_CLI' ) && WP_CLI ) );
Config::define('WP_DEBUG_LOG', env( 'WP_DEBUG_LOG' ) ?? true);
Config::define('WP_DEBUG_DISPLAY', env( 'WP_DEBUG_DISPLAY' ) ?? false);
Config::define('SCRIPT_DEBUG', true);

ini_set('display_errors', '1');

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);

/**
 * WP Rocket Settings
 */
Config::define('WP_ROCKET_EMAIL', '');
Config::define('WP_ROCKET_KEY', '');

/**
 * Defines custom DB_HOST value when run outside container
 */
if ( defined( 'WP_CLI' ) && WP_CLI && ! env( 'LANDO' ) ) {
	Config::define( 'DB_HOST', env( 'DB_HOST_EXTERNAL' ) ?? Config::get( 'DB_HOST' ) );
}

// Handle WPBrowser test configuration
if (getenv('WORDPRESS_URL')) {
    Config::define('WP_HOME', getenv('WORDPRESS_URL'));
    Config::define('WP_SITEURL', Config::get('WP_HOME') . substr(env('WP_SITEURL'), strlen(env('WP_HOME'))));
}
