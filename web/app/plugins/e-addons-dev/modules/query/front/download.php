<?php
//ini_set("pcre.backtrack_limit", "10000000");
if (!defined('WP_USE_THEMES')) {
    /** Loads the WordPress Environment and Template */
    define('WP_USE_THEMES', false);
    if (file_exists('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php')) {
        require('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php');
    } else if (file_exists('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php')) {
        require('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php');
    } else if (file_exists('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php')) {
        require('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php');
    } else if (file_exists('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php')) {
        require('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wp-blog-header.php');
    } else {
        echo 'ERROR: Wp Blog Header not found...why?';
        die();
    }
}

use \EAddonsForElementor\Core\Utils;

$element_id = empty($_GET['element_id']) ? 0 : $_GET['element_id'];
$post_id = empty($_GET['post_id']) ? 0 : intval($_GET['post_id']);

if ($element_id) {

    // prevent 404
    status_header(200);
    global $wp_query, $post;
    $wp_query->is_page = $wp_query->is_singular = true;
    $wp_query->is_404 = false;
    
    // set post
    if ($post) {
        $post = get_post($post_id);
        $wp_query->queried_object = $post;
        $wp_query->queried_object_id = $post_id;
    }
    
    // elementor data
    $element = Utils::get_element_instance_by_id($element_id);
    $settings = $element->get_settings_for_display();

    if (in_array($settings['_skin'], ['export', 'spreadsheet', 'json', 'xml'])) {
        $element->print_element();
        die();
    }
}

echo 'ERROR';
die();
