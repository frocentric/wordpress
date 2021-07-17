<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 *
 * @package Frocentric
 */

use Roots\WPConfig\Config;
use function Env\env;

/**
 * Directory containing all of the site's files
 *
 * @var string
 */
$root_dir = dirname( __DIR__ );

/**
 * Document Root
 *
 * @var string
 */
$webroot_dir = $root_dir . '/web';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = Dotenv\Dotenv::createUnsafeImmutable( $root_dir );
if ( file_exists( $root_dir . '/.env' ) ) {
	$dotenv->load();
	$dotenv->required( array( 'WP_HOME', 'WP_SITEURL' ) );
	if ( ! env( 'DATABASE_URL' ) ) {
		$dotenv->required( array( 'DB_NAME', 'DB_USER', 'DB_PASSWORD' ) );
	}
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define( 'WP_ENV', env( 'WP_ENV' ) ?? 'production' );

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if ( file_exists( $env_config ) ) {
	require_once $env_config;
}

/**
 * URLs
 */
Config::define( 'WP_HOME', env( 'WP_HOME' ) );
Config::define( 'WP_SITEURL', env( 'WP_SITEURL' ) );

/**
 * Custom Content Directory
 */
Config::define( 'CONTENT_DIR', '/app' );
Config::define( 'WP_CONTENT_DIR', $webroot_dir . Config::get( 'CONTENT_DIR' ) );
Config::define( 'WP_CONTENT_URL', Config::get( 'WP_HOME' ) . Config::get( 'CONTENT_DIR' ) );

/**
 * DB settings
 */
Config::define( 'DB_NAME', env( 'DB_NAME' ) );
Config::define( 'DB_USER', env( 'DB_USER' ) );
Config::define( 'DB_PASSWORD', env( 'DB_PASSWORD' ) );
Config::define( 'DB_HOST', env( 'DB_HOST' ) ?? 'localhost' );
Config::define( 'DB_CHARSET', 'utf8mb4' );
Config::define( 'DB_COLLATE', '' );
$table_prefix = env( 'DB_PREFIX' ) ?? 'wp_'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( env( 'DATABASE_URL' ) ) {
	$dsn = (object) wp_parse_url( env( 'DATABASE_URL' ) );

	Config::define( 'DB_NAME', substr( $dsn->path, 1 ) );
	Config::define( 'DB_USER', $dsn->user );
	Config::define( 'DB_PASSWORD', isset( $dsn->pass ) ? $dsn->pass : null );
	Config::define( 'DB_HOST', isset( $dsn->port ) ? "{$dsn->host}:{$dsn->port}" : $dsn->host );
}

if ( WP_ENV === 'development' && defined( 'WP_CLI' ) && WP_CLI ) {
	Config::define( 'DB_HOST', env( 'DB_HOST_LOCAL' ) );
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define( 'AUTH_KEY', env( 'AUTH_KEY' ) );
Config::define( 'SECURE_AUTH_KEY', env( 'SECURE_AUTH_KEY' ) );
Config::define( 'LOGGED_IN_KEY', env( 'LOGGED_IN_KEY' ) );
Config::define( 'NONCE_KEY', env( 'NONCE_KEY' ) );
Config::define( 'AUTH_SALT', env( 'AUTH_SALT' ) );
Config::define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
Config::define( 'LOGGED_IN_SALT', env( 'LOGGED_IN_SALT' ) );
Config::define( 'NONCE_SALT', env( 'NONCE_SALT' ) );
Config::define( 'JWT_AUTH_SECRET_KEY', env( 'JWT_AUTH_SECRET_KEY' ) );

/**
 * Custom Settings
 */
Config::define( 'AUTOMATIC_UPDATER_DISABLED', true );
Config::define( 'DISABLE_WP_CRON', env( 'DISABLE_WP_CRON' ) ?? false );
// Disable the plugin and theme file editor in the admin.
Config::define( 'DISALLOW_FILE_EDIT', true );
// Disable plugin and theme updates and installation from the admin.
Config::define( 'DISALLOW_FILE_MODS', true );

/**
 * Debugging Settings
 */
Config::define( 'WP_DEBUG_DISPLAY', false );
Config::define( 'WP_DEBUG_LOG', env( 'WP_DEBUG_LOG' ) ?? false );
Config::define( 'SCRIPT_DEBUG', false );

/**
 * Multisite Settings
 */
Config::define( 'WP_ALLOW_MULTISITE', true );
Config::define( 'MULTISITE', true );
Config::define( 'SUBDOMAIN_INSTALL', env( 'SUBDOMAIN_INSTALL' ) ?? true );
Config::define( 'DOMAIN_CURRENT_SITE', env( 'DOMAIN_CURRENT_SITE' ) );
Config::define( 'PATH_CURRENT_SITE', env( 'PATH_CURRENT_SITE' ) ?? '/' );
Config::define( 'SITE_ID_CURRENT_SITE', env( 'SITE_ID_CURRENT_SITE' ) ?? 1 );
Config::define( 'BLOG_ID_CURRENT_SITE', env( 'BLOG_ID_CURRENT_SITE' ) ?? 1 );
if ( ! defined( 'WP_CLI' ) ) {
	Config::define( 'COOKIE_DOMAIN', '.' . env( 'DOMAIN_CURRENT_SITE' ) ); // phpcs:ignore
}
Config::define('COOKIEPATH', '/');
Config::define('COOKIEHASH', md5( env( 'DOMAIN_CURRENT_SITE' ) ) ); // notice absence of a '.' in front
if ( env( 'HEADLESS_MODE_CLIENT_URL' ) ) {
	Config::define( 'HEADLESS_MODE_CLIENT_URL', env( 'HEADLESS_MODE_CLIENT_URL' ) );
}
$base = '/';

/**
 * WP Rocket Settings
 */
Config::define( 'WP_ROCKET_EMAIL', env( 'WP_ROCKET_EMAIL' ) ?? '' );
Config::define( 'WP_ROCKET_KEY', env( 'WP_ROCKET_KEY' ) ?? '' );

/**
 * Kinsta Settings
 */
Config::define( 'KINSTA_CDN_USERDIRS', 'app' );

/**
 * S3 configuration
 */
Config::define( 'S3_UPLOADS_BUCKET', env( 'S3_UPLOADS_BUCKET' ) );
Config::define( 'S3_UPLOADS_REGION', env( 'S3_UPLOADS_REGION' ) ); // the s3 bucket region (excluding the rest of the URL).
Config::define( 'S3_UPLOADS_KEY', env( 'S3_UPLOADS_KEY' ) );
Config::define( 'S3_UPLOADS_SECRET', env( 'S3_UPLOADS_SECRET' ) );
Config::define( 'S3_UPLOADS_ENDPOINT', env( 'S3_UPLOADS_ENDPOINT' ) );
Config::define( 'S3_UPLOADS_DEBUG', env( 'S3_UPLOADS_DEBUG' ) ?? false );
Config::define( 'S3_UPLOADS_USE_INSTANCE_PROFILE', env( 'S3_UPLOADS_USE_INSTANCE_PROFILE' ) ?? false );
Config::define( 'S3_UPLOADS_HTTP_CACHE_CONTROL', env( 'S3_UPLOADS_HTTP_CACHE_CONTROL' ) ?? null );
Config::define( 'S3_UPLOADS_AUTOENABLE', env( 'S3_UPLOADS_AUTOENABLE' ) ?? true );
Config::define( 'S3_UPLOADS_BUCKET_URL', env( 'S3_UPLOADS_BUCKET_URL' ) ?? null );
Config::define( 'S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL', env( 'S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL' ) ?? false );
Config::define( 'S3_UPLOADS_OBJECT_ACL', env( 'S3_UPLOADS_OBJECT_ACL' ) ?? null );

/**
 * Environment-specific plugin toggling
 */
if ( env( 'ENABLED_PLUGINS' ) ) {
	Config::define( 'ENABLED_PLUGINS', explode( ',', env( 'ENABLED_PLUGINS' ) ) );
}
if ( env( 'DISABLED_PLUGINS' ) ) {
	Config::define( 'DISABLED_PLUGINS', explode( ',', env( 'DISABLED_PLUGINS' ) ) );
}

/**
 * Discourse API configuration
 */
if ( env( 'DISCOURSE_API_KEY' ) ) {
	Config::define( 'DISCOURSE_API_KEY', env( 'DISCOURSE_API_KEY' ) );
}
if ( env( 'DISCOURSE_API_USERNAME' ) ) {
	Config::define( 'DISCOURSE_API_USERNAME', env( 'DISCOURSE_API_USERNAME' ) );
}

/**
 * WP Mail SMTP Settings
 */
Config::define( 'WPMS_ON', env( 'WPMS_ON' ) ?? true );
Config::define( 'WPMS_DO_NOT_SEND', env( 'WPMS_DO_NOT_SEND' ) ?? true );
Config::define( 'WPMS_MAIL_FROM', env( 'WPMS_MAIL_FROM' ) ?? 'noreply@frocentric.io' );
Config::define( 'WPMS_MAIL_FROM_FORCE', env( 'WPMS_MAIL_FROM_FORCE' ) ?? false );
Config::define( 'WPMS_MAIL_FROM_NAME', env( 'WPMS_MAIL_FROM_NAME' ) ?? 'Frocentric' );
Config::define( 'WPMS_MAIL_FROM_NAME_FORCE', env( 'WPMS_MAIL_FROM_NAME_FORCE' ) ?? false );
Config::define( 'WPMS_MAILER', env( 'WPMS_MAILER' ) );
Config::define( 'WPMS_SET_RETURN_PATH', env( 'WPMS_SET_RETURN_PATH' ) ?? true );
Config::define( 'WPMS_GMAIL_CLIENT_ID', env( 'WPMS_GMAIL_CLIENT_ID' ) );
Config::define( 'WPMS_GMAIL_CLIENT_SECRET', env( 'WPMS_GMAIL_CLIENT_SECRET' ) );

/**
 * Feedzy Settings
 */
Config::define( 'FEEDZY_ALLOW_UNSAFE_HTML', true );

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if ( file_exists( $env_config ) ) {
	require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $webroot_dir . '/wp/' );
}
