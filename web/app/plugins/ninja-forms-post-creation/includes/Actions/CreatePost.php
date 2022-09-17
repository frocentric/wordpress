<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * Class NF_Action_CreatePost
 */
final class NF_CreatePost_Actions_CreatePost extends NF_Abstracts_Action
{
    /**
     * @var string
     */
    protected $_name  = 'create-post';

    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */
    protected $_timing = 'normal';

    /**
     * @var int
     */
    protected $_priority = '10';

    /**
     * @var array
     */
    protected $_excluded_post_types = array(
        'attachment',
        'revision',
        'nav_menu_item',
        'nf_sub'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Create Post', 'ninja-forms-create-post' );

        add_action( 'admin_init', array( $this, 'init_settings' ) );

        add_action( 'ninja_forms_builder_templates', array( $this, 'builder_templates' ) );

        add_filter( 'ninja_forms_field_settings_groups', array( $this, 'field_settings_groups' ) );
    }

    /**
     * Process Action
     *
     * @param $action_settings
     * @param $form_id
     * @param $data
     * @return mixed
     */
    public function process( $action_settings, $form_id, $data )
    {
        $post = array(
            'post_type'     => $action_settings[ 'post_type' ],
            'post_status'   => isset( $action_settings[ 'post_status' ] ) ? $action_settings['post_status'] : '',
        );

        // If we don't have a post author...
        if( ! isset( $action_settings[ 'post_author' ] ) || 0 == $action_settings[ 'post_author' ] ){
            // Get our current user and set them as the author.
            $post[ 'post_author' ] = get_current_user_id();
        } // Otherwise...
        else {
            // Set it.
            $post[ 'post_author' ] = $action_settings[ 'post_author' ];
        }

        // If our post type supports titles...
        if( post_type_supports( $action_settings[ 'post_type' ], 'title' ) ){
            // Set it.
            $post[ 'post_title' ] = $action_settings[ 'post_title' ];
        }

        // If our post type supports content...
        if( post_type_supports( $action_settings[ 'post_type' ], 'editor' ) ){
            // Set it.
            $post[ 'post_content' ] = $action_settings[ 'post_content' ];
        }

        // If our post type supports excerpts...
        if( post_type_supports( $action_settings[ 'post_type' ], 'excerpt' ) ){
            // AND If we have an excerpt...
            if ( ! empty( $action_settings[ 'post_excerpt' ] ) ) {
                // Set it.
                $post[ 'post_excerpt' ] = $action_settings[ 'post_excerpt' ];
            } // Otherwise...
            else {
                // To ensure we honor db settings,
                // make sure we send an empty value instead of null.
                $post[ 'post_excerpt' ] = '';
            }
        }

        /*
         * Create the Post
         */
        $post_id = wp_insert_post( $post );
        do_action( 'ninja_forms_create_post', $post_id, $action_settings, $form_id, $data );

        if( 'post' == $action_settings[ 'post_type' ] ) {
            $format = isset( $action_settings['post_format'] ) ? $action_settings['post_format'] : '';
            set_post_format($post_id, $format);
        }

        /*
         * Taxonomies
         */
        $taxonomies = get_object_taxonomies( $action_settings['post_type'] );
        foreach( $taxonomies as $taxonomy ){

            $terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
            foreach( $terms as $term ){
                if( ! isset( $action_settings[ $taxonomy . '_' . $term->term_id ] ) || ! $action_settings[ $taxonomy . '_' . $term->term_id ] ) continue;
                wp_set_object_terms( $post_id, $term->term_id, $taxonomy, TRUE );
            }

            if( isset( $action_settings[ $taxonomy ] ) ){
                $terms = explode( ',', $action_settings[ $taxonomy ] );
                foreach( $terms as $term_id ){
                    
                    // If we have a Term Name instead of a Term ID...
                    if ( ! is_numeric( $term_id ) && $term_id ) {
                        // Try to fetch the ID from the db.
                        $fetched = get_term_by( 'name', $term_id, $taxonomy );
                        if ( $fetched ) {
                            $term_id = $fetched->term_id;
                        }
                    }

                    // If we still have a Term Name instead of a Term ID...
                    if( ! is_numeric( $term_id ) && $term_id ){
                        // Assume it is a new Term and insert it to get the ID.
                        $new_term = wp_insert_term( $term_id, $taxonomy );

                        if( is_wp_error( $new_term ) ) continue;

                        $term_id = $new_term[ 'term_id' ];
                    }

                    wp_set_object_terms( $post_id, absint( $term_id ), $taxonomy, TRUE );
                }
            }
        }

        if( 'post' == $action_settings[ 'post_type' ] ){
            if( ! isset( $action_settings[ 'category_1' ] ) || ! $action_settings[ 'category_1' ] ) {
                wp_remove_object_terms( $post_id, 'uncategorized', 'category' );
            }
        }

        /*
         * Custom Meta
         */
        foreach( $action_settings[ 'custom_meta' ] as $meta ){
            $meta[ 'value' ] = apply_filters( 'ninja_forms_create_post_meta_value', $meta[ 'value' ], $post[ 'post_type' ], $meta[ 'key' ] );
            add_post_meta( $post_id, $meta[ 'key' ], $meta[ 'value' ] );
        }

        /*
         * Permalink Merge Tag
         */
        $merge_tags = Ninja_Forms()->merge_tags[ 'created_posts' ];
        $merge_tags->set_permalink( $post_id );

        return $data;
    }

    public function init_settings()
    {
        if( ! isset( $_GET['page'] ) ||
            'ninja-forms' !== $_GET['page'] ||
            ! isset( $_GET['form_id'] ) ||
            empty( $_GET['form_id'] ) ) {
            return false;
        }
        $settings = NF_CreatePost::config( 'ActionCreatePostSettings' );
        $settings = $this->add_post_type_settings( $settings );
        $settings = $this->add_post_status_settings( $settings );
        $this->_settings = array_merge( $this->_settings, $settings );
        $this->_settings['custom_meta']['columns']['key']['options'] = $this->get_unique_meta_keys();
    }

    public function builder_templates()
    {
        NF_CreatePost::template( 'custom-meta-repeater-row.html.php' );
    }

    private function add_post_type_settings( $settings )
    {
        $post_types = get_post_types( array(), 'objects' );
        $this->_excluded_post_types = apply_filters( 'ninja_forms_create_post_excluded_post_types', $this->_excluded_post_types );

        foreach( $post_types as $post_type ){

            if( in_array( $post_type->name, $this->_excluded_post_types ) ) continue;

            $settings[ 'post_type' ][ 'options' ][] = array(
                'label' => $post_type->label,
                'value' => $post_type->name
            );

            $settings = $this->add_taxonomy_settings( $settings, $post_type->name );

//            if( post_type_supports( $post_type->name, 'title' ) ){
//                $settings[ 'post_title' ][ 'deps' ][ 'post_type' ][] = $post_type->name;
//            }
//
//            if( post_type_supports( $post_type->name, 'editor' ) ){
//                $settings[ 'post_content' ][ 'deps' ][ 'post_type' ][] = $post_type->name;
//            }
        }

        $users = get_users( array( 'fields' => array( 'display_name', 'ID' ) ) );
        foreach( $users as $user ){
            $settings[ 'post_author' ][ 'options' ][] = array(
                'label' => $user->display_name,
                'value' => $user->ID
            );
        }

        return $settings;
    }

    private function add_taxonomy_settings( $settings, $post_type )
    {
        $taxonomies = get_object_taxonomies( $post_type, 'object' );

        if( ! empty( $taxonomies ) ) {
            foreach ($taxonomies as $taxonomy) {

                if ('post' == $post_type && 'post_format' == $taxonomy->name) continue;

                $adapter = new NF_CreatePost_TaxonomyAdapter($taxonomy);

                $taxonomy_settings[$post_type . '_' . $taxonomy->name . '_taxonomies'] = array(
                    'name' => $post_type . '_' . $taxonomy->name . '_taxonomies',
                    'type' => 'fieldset',
                    'group' => 'create_post_terms',
                    'label' => $taxonomy->labels->name,
                    'settings' => $adapter->get_term_settings(),
                    'width' => 'full',
                    'deps' => array(
                        'post_type' => $post_type,
                    )
                );

                $settings = array_merge($settings, $taxonomy_settings);
            }
        } else {
            $taxonomy_settings[ $post_type . '_taxonomies' ] = array(
                'name' => $post_type . '_taxonomies',
                'type' => 'html',
                'group' => 'create_post_terms',
                'label' => '',
                'value' => __( 'No Taxonomies available for the selected post type.', 'ninja-forms-create-post' ),
                'width' => 'full',
                'deps' => array(
                    'post_type' => $post_type
                )
            );

            $settings = array_merge($settings, $taxonomy_settings);
        }

        return $settings;
    }

    public function field_settings_groups( $groups )
    {
        $groups[ 'create_post_terms' ] = array(
            'id' => 'create_post_terms',
            'label' => __( 'Terms and Taxonomies', 'ninja-forms-create-post' )
        );

        return $groups;
    }

    private function add_post_status_settings( $settings )
    {
        foreach( get_post_statuses() as $value => $label ){
            $settings[ 'post_status' ][ 'options' ][] = array(
                'label' => $label,
                'value' => $value
            );
        }

        return $settings;
    }

    private function get_unique_meta_keys()
    {
        global $wpdb;
        $meta_keys = $wpdb->get_results( "SELECT DISTINCT meta_key FROM $wpdb->postmeta", ARRAY_A );

        $unique_keys = array();
        foreach( $meta_keys as $key ){

            if( '_' == substr( $key['meta_key'], 0, 1 ) ) continue;

            $unique_keys[] = array(
                'label' => $key['meta_key'],
                'value' => $key['meta_key']
            );
        }

        return array_unique( $unique_keys, SORT_REGULAR );
    }

}
