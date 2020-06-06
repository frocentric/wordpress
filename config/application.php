<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;

/**
 * Directory containing all of the site's files
 *
 * @var string
 */
$root_dir = dirname(__DIR__);

/**
 * Document Root
 *
 * @var string
 */
$webroot_dir = $root_dir . '/web';

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = Dotenv\Dotenv::createImmutable($root_dir);
if (file_exists($root_dir . '/.env')) {
    $dotenv->load();
    $dotenv->required(['WP_HOME', 'WP_SITEURL']);
    if (!env('DATABASE_URL')) {
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
    }
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define('WP_ENV', env('WP_ENV') ?: 'production');

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config)) {
    require_once $env_config;
}

/**
 * URLs
 */
Config::define('WP_HOME', env('WP_HOME'));
Config::define('WP_SITEURL', env('WP_SITEURL'));

/**
 * Custom Content Directory
 */
Config::define('CONTENT_DIR', '/app');
Config::define('WP_CONTENT_DIR', $webroot_dir . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));

/**
 * DB settings
 */
Config::define('DB_NAME', env('DB_NAME'));
Config::define('DB_USER', env('DB_USER'));
Config::define('DB_PASSWORD', env('DB_PASSWORD'));
Config::define('DB_HOST', env('DB_HOST') ?: 'localhost');
Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

if (env('DATABASE_URL')) {
    $dsn = (object) parse_url(env('DATABASE_URL'));

    Config::define('DB_NAME', substr($dsn->path, 1));
    Config::define('DB_USER', $dsn->user);
    Config::define('DB_PASSWORD', isset($dsn->pass) ? $dsn->pass : null);
    Config::define('DB_HOST', isset($dsn->port) ? "{$dsn->host}:{$dsn->port}" : $dsn->host);
}

if (WP_ENV == 'development' && defined('WP_CLI') && WP_CLI) {
    Config::define('DB_HOST', env('DB_HOST_LOCAL'));
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define('AUTH_KEY', env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', env('NONCE_KEY'));
Config::define('AUTH_SALT', env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', env('NONCE_SALT'));

/**
 * Custom Settings
 */
Config::define('AUTOMATIC_UPDATER_DISABLED', true);
Config::define('DISABLE_WP_CRON', env('DISABLE_WP_CRON') ?: false);
// Disable the plugin and theme file editor in the admin
Config::define('DISALLOW_FILE_EDIT', true);
// Disable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', true);

/**
 * Debugging Settings
 */
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', env('WP_DEBUG_LOG') ?? false);
Config::define('SCRIPT_DEBUG', false);
ini_set('display_errors', '0');

/**
 * WP Rocket Settings
 */
Config::define('WP_ROCKET_EMAIL', env('WP_ROCKET_EMAIL') ?: '');
Config::define('WP_ROCKET_KEY', env('WP_ROCKET_KEY') ?: '');

/**
 * Kinsta Settings
 */
Config::define('KINSTA_CDN_USERDIRS', 'app');

/**
 * S3 configuration
 */
Config::define( 'S3_UPLOADS_BUCKET', env('S3_UPLOADS_BUCKET') );
Config::define( 'S3_UPLOADS_REGION', env('S3_UPLOADS_REGION') ); // the s3 bucket region (excluding the rest of the URL)
Config::define( 'S3_UPLOADS_KEY', env('S3_UPLOADS_KEY') );
Config::define( 'S3_UPLOADS_SECRET', env('S3_UPLOADS_SECRET') );
Config::define( 'S3_UPLOADS_ENDPOINT', env('S3_UPLOADS_ENDPOINT') );
Config::define( 'S3_UPLOADS_DEBUG', env('S3_UPLOADS_DEBUG') ?: false );
Config::define( 'S3_UPLOADS_USE_INSTANCE_PROFILE', env('S3_UPLOADS_USE_INSTANCE_PROFILE') ?: false );
Config::define( 'S3_UPLOADS_HTTP_CACHE_CONTROL', env('S3_UPLOADS_HTTP_CACHE_CONTROL') ?: null );
Config::define( 'S3_UPLOADS_AUTOENABLE', env('S3_UPLOADS_AUTOENABLE') ?: true );
Config::define( 'S3_UPLOADS_BUCKET_URL', env('S3_UPLOADS_BUCKET_URL') ?: null );
Config::define( 'S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL', env('S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL') ?: false );
Config::define( 'S3_UPLOADS_OBJECT_ACL', env('S3_UPLOADS_OBJECT_ACL') ?: null );

/**
 * Environment-specific plugin toggling
 */
if ( env('ENABLED_PLUGINS') ) {
    Config::define( 'ENABLED_PLUGINS', explode(',', env('ENABLED_PLUGINS')) );
}
if ( env('DISABLED_PLUGINS') ) {
    Config::define( 'DISABLED_PLUGINS', explode(',', env('DISABLED_PLUGINS')) );
}

/**
 * Discourse API credentials
 */
if ( env('DISCOURSE_API_KEY') ) {
	Config::define( 'DISCOURSE_API_KEY', env('DISCOURSE_API_KEY') );
}
if ( env('DISCOURSE_API_USERNAME') ) {
	Config::define( 'DISCOURSE_API_USERNAME', env('DISCOURSE_API_USERNAME') );
}

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config)) {
    require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', $webroot_dir . '/wp/');
}
