<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use EAddonsForElementor\Core\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

/**
 * Description of label
 *
 * @author fra
 */
trait Items_Advanced {

    // NOTA: sono esclusi da advanced gli item_type
    // query-post: 'item_author','item_readmore','item_custommeta'
    // query-term: 'item_readmore','item_custommeta'
    // query-user: 'item_readmore','item_custommeta'
    // query-items: 'item_readmore'
    // ----------------------------------------------------------    
    public function controls_items_advanced($target) {
        //use_Link, Display(block,inline)
        $options = [
            '' => esc_html__('None', 'e-addons'),
            'yes' => esc_html__('Current', 'e-addons'),
            'shortcode' => esc_html__('Shortcode', 'e-addons'),
            'custom' => esc_html__('Custom', 'e-addons'),
        ];
        if (Utils::is_plugin_active('elementor-pro') && Utils::is_plugin_active('e-addons-extended')) {
            $options['popup'] = esc_html__('Open PopUp', 'e-addons');
        }
        $target->add_control(
            'use_link', [
                'label' => '<i class="fas fa-link"></i> ' . esc_html__('Use link', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => $options,
                'default' => 'yes',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => '!in',
                            'value' => [
                                'item_posttype',
                                'item_date',
                                'item_registered',
                                //'item_readmore',
                                //'item_termstaxonomy',
                                'item_content',
                                'item_excerpt',
                                'item_description',
                                'item_taxonomy',
                                'item_custommeta',
                                'item_caption',
                                'item_alternativetext',
                                'item_imagemeta',
                                'item_mimetype',
                                'item_counts',
                                'item_descriptiontext',
                                'item_subtitle'
                            ],
                        ]
                    ]
                ]
            ]
        );
        // blank
        $target->add_control(
            'blanklink_enable', [
                'label' => '<i class="fas fa-external-link-alt"></i> ' . esc_html__('Open in new window', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'item_type!' => [
                        'item_posttype',
                        'item_date',
                        'item_registered',
                        //'item_readmore',
                        //'item_termstaxonomy',
                        'item_content',
                        'item_excerpt',
                        'item_description',
                        'item_taxonomy',
                        'item_custommeta',
                        'item_caption',
                        'item_alternativetext',
                        'item_imagemeta',
                        'item_mimetype',
                        'item_counts',
                        'item_descriptiontext',
                        'item_subtitle'
                    ],
                    'use_link' => ['yes', 'shortcode', 'custom'],
                ]
            ]
        );
        $target->add_control(
            'shortcode_link', [
                'label' => esc_html__('Custom link', 'e-addons'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'use_link' => ['shortcode', 'custom'],
                ]
            ]
        );
        if (Utils::is_plugin_active('elementor-pro') || Utils::is_plugin_active('e-addons-extended')) {
            $target->add_control(
                    'popup_link', [
                'label' => esc_html__('Open PopUp', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => esc_html__('Select PopUp', 'e-addons'),
                'label_block' => true,
                'query_type' => 'posts',
                'object_type' => 'popup',
                'condition' => [
                    'use_link' => 'popup',
                ]
                    ]
            );
        }
        // custom class
        $target->add_control(
            'item_custom_class', [
                'label' => '<i class="fas fa-code"></i> '.esc_html__('CSS Class', 'e-addons'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('Add custom classes for this item. (ex: class1 class2)', 'e-addons'),
                'separator' => 'before',
            ]
        );
        $target->add_responsive_control(
            'display_inline', [
                'label' => '<i class="fas fa-clipboard-list"></i> ' . esc_html__('Display', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'default' => '',
                'separator' => 'before',
                'toggle' => true,
                'options' => [
                    'block' => [
                        'title' => esc_html__('Block', 'e-addons'),
                        'icon' => 'fas fa-bars',
                    ],
                    'inline-block' => [
                        'title' => esc_html__('Inline', 'e-addons'),
                        'icon' => 'fas fa-ellipsis-h',
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'display: {{VALUE}}; vertical-align: middle;',
                ],
                    /* 'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                    [
                    'name' => 'item_type',
                    'operator' => 'in',
                    'value' => ['item_title',
                    'item_date',
                    'item_registered',
                    'item_posttype',
                    'item_taxonomy',
                    'item_user',
                    'item_role',
                    'item_bio',
                    'item_firstname',
                    'item_lastname',
                    'item_displayname',
                    'item_nickname',
                    'item_email',
                    'item_label',
                    'item_counts',
                    'item_caption',
                    'item_alternativetext',
                    'item_imagemeta',
                    'item_mimetype',
                    'item_uploadedto'
                    ],
                    ],
                    [
                    'relation' => 'and',
                    'terms' => [
                    [
                    'name' => 'item_type',
                    'value' => 'item_custommeta',
                    ],
                    [
                    'name' => 'metafield_type',
                    'operator' => 'in',
                    'value' => ['file', 'text', 'number', 'image', 'array']
                    ]
                    ]
                    ]
                    ]
                ] */
            ]
        );

        $target->add_responsive_control(
                'width',
                [
                    'label' => '<i class="fas fa-columns"></i> ' . esc_html__('Column Width', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'separator' => 'before',
                    'options' => [
                        '100' => '100%',
                        '90' => '90%',
                        '80' => '80%',
                        '75' => '75%',
                        '70' => '70%',
                        '66' => '66%',
                        '60' => '60%',
                        '50' => '50%',
                        '40' => '40%',
                        '33' => '33%',
                        '30' => '30%',
                        '25' => '25%',
                        '20' => '20%',
                        '10' => '10%',
                        '' => esc_html__('Custom', 'e-addons'),
                    ],
                    'condition' => [
                        'display_inline!' => 'inline-block',
                        //'_skin!' => 'list'
                    ],
                    'default' => 100,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{VALUE}}%;',
                    ],
                ]
        );
        $target->add_responsive_control(
                'custom_width', [
            'label' => esc_html__('Custom Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%','px'],
            'default' => [ 
                'unit' => '%',
                'size' => '100',
            ],
            'tablet_default' => [
                'unit' => '%',
            ],
            'mobile_default' => [
                'unit' => '%',
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 1000,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'width' => '',
                'display_inline!' => 'inline-block',
            ]
                ]
        );

        $target->add_control(
                'icon_enable', [
            'label' => esc_html__('Icon', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_termstaxonomy',
                            'item_date',
                            'item_registered',
                        ],
                    ]
                ]
            ]
                ]
        );
        /*
          item_avatar
          item_displayname
          item_user
          item_role
          item_firstname
          item_lastname
          item_nickname
          item_email
          item_website
          item_bio
          item_custommeta
          item_readmore
          item_label
         */

        /*
          item_image
          item_date
          item_title
          item_termstaxonomy
          item_content
          item_author
          item_readmore
          item_posttype
          item_custommeta
          item_label
          todo: commets...
         */

        /*
          item_image
          item_title
          item_taxonomy
          item_counts
          item_description
          item_readmore
          item_custommeta
          item_label
         */

        /*
          item_image (or icon)
          item_date
          item_title
          item_subtitle
          item_descriptiontext
          item_readmore
         */

        $target->add_control(
                'use_label_before', [
            'label' => '<i class="fas fa-minus"></i> ' . esc_html__('Before', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'separator' => 'before',
            'description' => esc_html__('Print Label before the Field', 'e-addons'),
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_date',
                            'item_registered',
                            'item_termstaxonomy',
                            'item_posttype',
                            'item_counts',
                            'item_displayname',
                            'item_user',
                            'item_role',
                            'item_firstname',
                            'item_lastname',
                            'item_nickname',
                            'item_email',
                            'item_website',
                            'item_bio',
                            'item_counts',
                            'item_caption',
                            'item_alternativetext',
                            'item_imagemeta',
                            'item_mimetype',
                            'item_uploadedto',
                            'item_index'
                        ]
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => 'item_custommeta',
                            ],
                            [
                                'name' => 'metafield_type',
                                'operator' => 'in',
                                'value' => ['text', 'number', 'array']
                            ]
                        ]
                    ]
                ],
            ]
                ]
        );
        $target->add_control(
            'use_label_after', [
                'label' => esc_html__('After', 'e-addons') . ' <i class="fas fa-minus"></i>',
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('Print text after the Field', 'e-addons'),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => 'in',
                            'value' => [
                                'item_date',
                                'item_registered',
                                'item_termstaxonomy',
                                'item_posttype',
                                'item_counts',
                                'item_displayname',
                                'item_user',
                                'item_role',
                                'item_firstname',
                                'item_lastname',
                                'item_nickname',
                                'item_email',
                                'item_website',
                                'item_bio',
                                'item_counts',
                                'item_caption',
                                'item_alternativetext',
                                'item_imagemeta',
                                'item_mimetype',
                                'item_uploadedto',
                                'item_index'
                            ]
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'item_type',
                                    'value' => 'item_custommeta',
                                ],
                                [
                                    'name' => 'metafield_type',
                                    'operator' => 'in',
                                    'value' => ['text', 'number', 'array']
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        );
        $target->add_control(
            'use_fallback', [
                'label' => '<i class="fas fa-exclamation-triangle"></i> '.esc_html__('Fallback', 'e-addons'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('Alternative Text for empty values', 'e-addons'),
                'separator' => 'before',
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'value' => 'item_custommeta',
                        ],
                        [
                            'name' => 'metafield_type',
                            'operator' => 'in',
                            'value' => ['text', 'number', 'array']
                        ]
                    ],
                ]
            ]
        );
        $target->add_control(
            'use_fallback_img', [
                'label' => '<i class="fas fa-exclamation-triangle"></i> '.esc_html__('Fallback', 'e-addons'),
                'type' => Controls_Manager::MEDIA,
                'description' => esc_html__('Alternative Image for empty values', 'e-addons'),
                'separator' => 'before',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => 'in',
                            'value' => [
                                'item_image',
                                'item_avatar',
                            ]
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'item_type',
                                    'value' => 'item_custommeta',
                                ],
                                [
                                    'name' => 'metafield_type',
                                    'operator' => 'in',
                                    'value' => ['image']
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        );
    }
}