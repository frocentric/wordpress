<?php
/** Production */
ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
/** Disable all file modifications including updates and update notifications */
define('DISALLOW_FILE_MODS', true);
define('WP_ROCKET_EMAIL', env('WP_ROCKET_EMAIL') ?: '');
define('WP_ROCKET_KEY', env('WP_ROCKET_KEY') ?: '');
define('KINSTA_CDN_USERDIRS', 'app');
