<?php
/*
Plugin Name: Twig Anything
Plugin URI:  http://twiganything.com
Description: Fetch data from JSON, CSV, XML, databases or local files and use Twig template language to render it anywhere in WordPress or create API Endpoints.
Version:     1.6.5
Author:      Anton Andriievskyi
Author URI:  https://twiganything.com
License:     Commercial
Domain Path: /languages
Text Domain: twig-anything

Copyright (c) 2015-present by Anton Andriievskyi, the plugin author.

Twig Anything is NOT free software. One license allows to run the plugin
with a single WordPress installation only.
You CANNOT redistribute it and/or modify it.

Twig Anything is sold in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details, sections
"15. Disclaimer of Warranty" and "16. Limitation of Liability"
by the following url: http://www.gnu.org/licenses/gpl-3.0.txt

You should have received a copy of the license in file LICENSE.txt
along with Twig Anything.

*/

namespace TwigAnything;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * A project-specific implementation of autoloader.
 * Source: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 *
 *      new \Foo\Bar\Baz\Qux;
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {

    # Project-specific namespace prefix
    $prefix = 'TwigAnything\\';

    # Base directory for the namespace prefix
    $base_dir = __DIR__.'/core/';

    # Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        # No, move to the next registered autoloader
        return;
    }

    # Get the relative class name
    $relative_class = substr($class, $len);

    # Replace the namespace prefix with the base directory, replace namespace
    # separators with directory separators in the relative class name, append
    # with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        require $file;
    }
});

global $twigAnything;
$twigAnything = new TwigAnything;
$twigAnything->setup();

/**
 * @return TwigAnything
 */
function twigAnything() {
    global $twigAnything;
    return $twigAnything;
}

// COMMENTED OUT - not stable yet
// Upgrade::register();