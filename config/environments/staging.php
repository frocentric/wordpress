<?php
/**
 * Configuration overrides for WP_ENV === 'staging'
 */

use Roots\WPConfig\Config;
use function Env\env;

/**
 * You should try to keep staging as close to production as possible. However,
 * should you need to, you can always override production configuration values
 * with `Config::define`.
 *
 * Example: `Config::define('WP_DEBUG', true);`
 * Example: `Config::define('DISALLOW_FILE_MODS', false);`
 */
Config::define('SAVEQUERIES', env('SAVEQUERIES') ?? false);
Config::define('WP_DEBUG', env('WP_DEBUG') ?? false);
Config::define('WP_DEBUG_DISPLAY', env('WP_DEBUG_DISPLAY') ?? false);

/**
 * WP Rocket Settings
 */
Config::define('WP_ROCKET_EMAIL', '');
Config::define('WP_ROCKET_KEY', '');

/**
 * Jetpack Settings
 */
Config::define( 'JETPACK_STAGING_MODE', true );
