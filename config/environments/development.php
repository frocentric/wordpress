<?php
/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;
use function Env\env;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_LOG', true);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('SCRIPT_DEBUG', true);

ini_set('display_errors', '1');

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);

/**
 * WP Rocket Settings
 */
Config::define('WP_ROCKET_EMAIL', '');
Config::define('WP_ROCKET_KEY', '');