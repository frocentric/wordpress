<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Post Creation
 * Plugin URI: https://ninjaforms.com/extensions/
 * Description: Create posts, pages, or any custom post type from the front-end.
 * Version: 3.0.10
 * Author: The WP Ninjas
 * Author URI: http://ninjaforms.com
 * Text Domain: ninja-forms-create-post
 *
 * Copyright 2016 The WP Ninjas.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ){

    define("NINJA_FORMS_POST_DIR", WP_PLUGIN_DIR."/".basename( dirname( __FILE__ ) )."/deprecated" );
    define("NINJA_FORMS_POST_URL", plugins_url()."/".basename( dirname( __FILE__ ) )."/deprecated" );
    define("NINJA_FORMS_POST_VERSION", "3.0.10");

    include 'deprecated/post-creation.php';

} else {

    /**
     * Class NF_CreatePost
     */
    final class NF_CreatePost
    {
        const VERSION = '3.0.10';
        const SLUG = 'create-post';
        const NAME = 'Create Post';
        const AUTHOR = 'The WP Ninjas';
        const PREFIX = 'NF_CreatePost';

        /**
         * @var NF_CreatePost
         * @since 3.0
         */
        private static $instance;

        /**
         * Plugin Directory
         *
         * @since 3.0
         * @var string $dir
         */
        public static $dir = '';

        /**
         * Plugin URL
         *
         * @since 3.0
         * @var string $url
         */
        public static $url = '';

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_CreatePost Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_CreatePost)) {
                self::$instance = new NF_CreatePost();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register(array(self::$instance, 'autoloader'));
            }
        }

        public function __construct()
        {
            add_action('admin_init', array($this, 'setup_license'));
            add_filter('ninja_forms_register_actions', array($this, 'register_actions'));
            add_action('ninja_forms_register_merge_tags', array($this, 'register_merge_tags'));
            add_filter( 'ninja_forms_new_form_templates', array( $this, 'register_templates' ) );
        }


        /**
         * Register Actions
         *
         * @param array $actions
         * @return array $actions
         */
        public function register_actions($actions)
        {
            $actions['create-post'] = new NF_CreatePost_Actions_CreatePost();

            return $actions;
        }

        /**
         * Regsiter Merge Tags
         *
         * @param array $merge_tags
         * @return array $merge_tags
         */
        public function register_merge_tags($merge_tags)
        {
            $merge_tags['created_posts'] = new NF_CreatePost_MergeTags();

            return $merge_tags;
        }

        /**
         * Autoloader
         *
         * @param $class_name
         */
        public function autoloader($class_name)
        {
            if (class_exists($class_name)) return;

            if (false === strpos($class_name, self::PREFIX)) return;

            $class_name = str_replace(self::PREFIX, '', $class_name);
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }

        /**
         * Register Templates
         *
         * Registers our custom form templates.
         *
         * @param $templates
         * @return mixed
         */
        public function register_templates( $templates )
        {
            //Register the login form template.
            $templates[ 'create-post' ] = array(
                'id'            => 'create-post',
                'title'         => __( 'Create a Post', 'ninja-forms' ),
                'template-desc' => __( 'Allow users to create posts from the front-end using a form, including custom post meta!', 'ninja-forms' ),
                'form'          => self::form_templates( 'create-post.nff' ),
            );

            return $templates;
        }

        /*
         * STATIC METHODS
         */
    
        /**
         * Form Templates
         *
         * This method is used to load the form templates
         *
         * @param string $file_name
         * @param array $data
         * @return string
         */
        public static function form_templates( $file_name = '', array $data = array() )
        {
            $path = self::$dir . 'includes/Templates/' . $file_name;

            if( ! file_exists(  $path ) ) return '';

            extract( $data );

            ob_start();

            include $path;

            return ob_get_clean();
        }

        /**
         * Template
         *
         * @param string $file_name
         * @param array $data
         */
        public static function template($file_name = '', array $data = array())
        {
            if (!$file_name) return;

            extract($data);

            include self::$dir . 'includes/Templates/' . $file_name;
        }

        /**
         * Config
         *
         * @param $file_name
         * @return mixed
         */
        public static function config($file_name)
        {
            return include self::$dir . 'includes/Config/' . $file_name . '.php';
        }

        /**
         * Setup License
         */
        public function setup_license()
        {
            if (!class_exists('NF_Extension_Updater')) return;

            new NF_Extension_Updater( 'Front-End Posting', self::VERSION, 'WP Ninjas', __FILE__, 'post_creation' );
        }
    }

    /**
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * @since 3.0
     * @return NF_CreatePost
     */
    function NF_CreatePost()
    {
        return NF_CreatePost::instance();
    }

    NF_CreatePost();

}


add_filter( 'ninja_forms_upgrade_settings', 'NF_CreatePost_Upgrade' );
function NF_CreatePost_Upgrade( $data ){

    // Check to see if Post Creation is enabled.
    if( isset( $data[ 'settings' ][ 'create_post' ] ) ) {

        // Create a new action.
        $action = array(
            'type' => 'create-post',
            'name' => __( 'Create Post', 'ninja-forms-create-post' ),
            'active' => $data[ 'settings' ][ 'create_post' ],
        );

        // Map old form settings to the new action.
        foreach( array( 'post_as', 'post_status', 'post_type', 'post_title', 'post_content', 'post_excerpt' ) as $setting ){
            if( isset( $data[ 'settings' ][ $setting ] ) ){
                $action[ $setting ] = $data[ 'settings' ][ $setting ];
            }
        }

        // Convert the post_tags setting.
        if( isset( $data[ 'settings' ][ 'post_tags' ] ) ){
            $default_tags = explode( ',', $data[ 'settings' ][ 'post_tags' ] );
            $post_tags = get_terms( 'post_tag', array(
                'name' => $default_tags,
                'hide_empty' => false
            ) );

            foreach( $post_tags as $tag ){
                $action[ 'post_tag_' . $tag->term_id ] = 1;
            }
        }

        // Convert the Category (and other, non-post_tag, taxonomies) setting.
        if( isset( $data[ 'settings' ][ 'post_tax' ] ) ){
            foreach( $data[ 'settings' ][ 'post_tax' ] as $taxonomy ){

                if( ! isset( $data[ 'settings' ][ $taxonomy . '_terms' ] ) ) continue;

                foreach( $data[ 'settings' ][ $taxonomy . '_terms' ] as $term_id ){
                    $action[  $taxonomy . '_' . $term_id ] = 1;
                }
            }
        }

        // Convert old field types to action merge tags.
        foreach( $data[ 'fields' ] as $key => $field ){

            if( isset( $field[ 'data' ][ 'post_meta_value' ] ) && $field[ 'data' ][ 'post_meta_value' ] ){
                $action[ 'custom_meta' ][] = array(
                    'order' => count( $action[ 'custom_meta' ] ),
                    'key' => $field[ 'data' ][ 'post_meta_value' ],
                    'value' => '{field:' . $field[ 'id' ] . '}'
                );
            }

            switch( $field[ 'type' ] ){
                case '_post_title':
                    $field[ 'type' ] = 'textbox';
                    $field[ 'data' ][ 'default' ] = $action[ 'post_title' ];
                    $action[ 'post_title' ] = '{field:' . $field[ 'id' ] . '}';
                    break;
                case '_post_content':
                    $field[ 'type' ] = 'textarea';
                    if( isset( $data[ 'settings' ][ 'post_content_location' ] ) && 'append' == $data[ 'settings' ][ 'post_content_location' ] ) {
                        $action['post_content'] = '{field:' . $field['id'] . '}' . PHP_EOL . PHP_EOL . $action['post_content'];
                    } elseif( isset( $data[ 'settings' ][ 'post_content_location' ] ) && 'prepend' == $data[ 'settings' ][ 'post_content_location' ] ){
                        $action['post_content'] = $action['post_content'] . PHP_EOL . PHP_EOL . '{field:' . $field['id'] . '}';
                    }  else {
                        $field[ 'data' ][ 'default' ] = $action[ 'post_content' ];
                        $action[ 'post_content' ] = '{field:' . $field[ 'id' ] . '}';
                    }
                    break;
                case '_post_excerpt':
                    $field[ 'type' ] = 'textarea';
                    $field[ 'data' ][ 'default' ] = $action[ 'post_excerpt' ];
                    $action[ 'post_excerpt' ] = '{field:' . $field[ 'id' ] . '}';
                    break;
                case '_post_tags':
                    $field[ 'type' ] = 'terms';
                    $field[ 'taxonomy' ] = 'post_tag';
                    $action[ 'post_tag' ] = '{field:terms_' . $field[ 'id' ] . '}'; // Map the terms field.
                    $field[ 'add_new_terms' ] = ( isset( $field[ 'data' ][ 'adv_tags' ] ) && $field[ 'data' ][ 'adv_tags' ] ) ? 1 : 0;
                    break;
                case '_post_category':
                    $field[ 'type' ] = 'terms';
                    $field[ 'taxonomy' ] = 'category';
                    $action[ 'category' ] = '{field:terms_' . $field[ 'id' ] . '}'; // Map the category field.
                    $field[ 'add_new_terms' ] = ( isset( $field[ 'data' ][ 'add_category' ] ) && $field[ 'data' ][ 'add_category' ] ) ? 1 : 0;
                    break;
                default:
                    if( '_post_' != substr( $field[ 'type' ], 0, 6 ) ) continue 2;
                    $taxonomy = substr( $field[ 'type' ], 6 );
                    $field[ 'type' ] = 'terms';
                    $field[ 'taxonomy' ] = $taxonomy;
                    $action[ $taxonomy ] = '{field:terms_' . $field[ 'id' ] . '}'; // Map the category field.
                    $field[ 'add_new_terms' ] = ( isset( $field[ 'data' ][ 'add_' . $taxonomy ] ) && $field[ 'data' ][ 'add_' . $taxonomy ] ) ? 1 : 0;
            }

            // Initialize terms as available to match expected behavior.
            if( 'terms' == $field[ 'type' ] ){
                if( isset( $field[ 'taxonomy' ] ) ){
                    $terms = get_terms( $field[ 'taxonomy' ], array( 'hide_empty' => false ) );
                    if( $terms && is_array( $terms ) ){
                        foreach( $terms as $term ){
                            $setting = 'taxonomy_term_' . $term->term_id;
                            $field[ $setting ] = 1;
                        }
                    }
                }
            }

            // Update the field.
            $data[ 'fields' ][ $key ] = $field;
        }

        $data[ 'actions' ][] = $action;
    }

    return $data;
}
