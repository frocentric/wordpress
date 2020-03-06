<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'ninja_forms_create_post_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Post Type
    |--------------------------------------------------------------------------
    */

    'post_type' => array(
        'name' => 'post_type',
        'type' => 'select',
        'group' => 'primary',
        'label' => __( 'Post Type', 'ninja-forms-create-post' ),
        'options' => array(
            array(
                'label' => '--',
                'value' => ''
            )
        ),
        'width' => 'full',
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Title
    |--------------------------------------------------------------------------
    */

    'post_title' => array(
        'name' => 'post_title',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => __( 'Post Title', 'ninja-forms-create-post' ),
        'width' => 'full',
        'use_merge_tags' => TRUE
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Content
    |--------------------------------------------------------------------------
    */

    'post_content' => array(
        'name' => 'post_content',
        'type' => 'textarea',
        'group' => 'primary',
        'label' => __( 'Post Content', 'ninja-forms-create-post' ),
        'width' => 'full',
        'use_merge_tags' => TRUE
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Author
    |--------------------------------------------------------------------------
    */

    'post_author' => array(
        'name' => 'post_author',
        'type' => 'select',
        'group' => 'advanced',
        'label' => __( 'Post Author', 'ninja-forms-create-post' ),
        'options' => array(
            array(
                'label' => '- ' . __( 'Themselves', 'ninja-forms-create-post' ),
                'value' => ''
            )
        ),
        'width' => 'full',
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Status
    |--------------------------------------------------------------------------
    */

    'post_status' => array(
        'name' => 'post_status',
        'type' => 'select',
        'group' => 'advanced',
        'label' => __( 'Post Status', 'ninja-forms-create-post' ),
        'options' => array(),
        'width' => 'full',
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Format
    |--------------------------------------------------------------------------
    */

    'post_format' => array(
        'name' => 'post_format',
        'type' => 'select',
        'group' => 'advanced',
        'label' => __( 'Post Format', 'ninja-forms-create-post' ),
        'options' => array(
            array(
                'label' => __( 'Standard' ),
                'value' => 'standard'
            ),
            array(
                'label' => __( 'Aside' ),
                'value' => 'aside'
            ),
            array(
                'label' => __( 'Chat' ),
                'value' => 'chat'
            ),
            array(
                'label' => __( 'Gallery' ),
                'value' => 'gallery'
            ),
            array(
                'label' => __( 'Link' ),
                'value' => 'link'
            ),
            array(
                'label' => __( 'Image' ),
                'value' => 'image'
            ),
            array(
                'label' => __( 'Quote' ),
                'value' => 'quote'
            ),
            array(
                'label' => __( 'Status' ),
                'value' => 'status'
            ),
            array(
                'label' => __( 'Video' ),
                'value' => 'video'
            ),
            array(
                'label' => __( 'Audio' ),
                'value' => 'audio'
            ),
        ),
        'width' => 'full',
        'deps' => array(
            'post_type' => 'post'
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Post Excerpt
    |--------------------------------------------------------------------------
    */

    'post_excerpt' => array(
        'name' => 'post_excerpt',
        'type' => 'textarea',
        'group' => 'advanced',
        'label' => __( 'Post Excerpt', 'ninja-forms-create-post' ),
        'width' => 'full',
        'use_merge_tags' => TRUE
    ),

    /*
    |--------------------------------------------------------------------------
    | Taxonomies
    |--------------------------------------------------------------------------
    */

    'create_post_not_selected' => array(
        'name' => 'create_post_not_selected',
        'type' => 'html',
        'group' => 'create_post_terms',
        'label' => '',
        'value' => __( 'No Post Type Selected' ),
        'width' => 'full',
        'deps' => array(
            'post_type' => ''
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Custom Meta
    |--------------------------------------------------------------------------
    */

    'custom_meta' => array(
        'name' => 'custom_meta',
        'type' => 'option-repeater',
        'label' => __( 'Custom Meta', 'ninja-forms-create-post' ) . ' <a href="#" class="nf-add-new">' . __( 'Add New' ) . '</a>',
        'width' => 'full',
        'group' => 'advanced',
        'tmpl_row' => 'tmpl-nf-create-post-custom-meta-repeater-row',
        'value' => array(),
        'columns'           => array(
            'key'          => array(
                'header'    => __( 'Meta Key', 'ninja-forms-create-post' ),
                'default'   => '',
                'options' => array()
            ),
            'value'          => array(
                'header'    => __( 'Meta Value', 'ninja-forms-create-post' ),
                'default'   => '',
            ),
        ),
    ),

) );
