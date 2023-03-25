<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

/**
 * Description of label
 *
 * @author fra
 */
trait Items_Content {
    /*
      POSTS:
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
      USERS:
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
      TERMS:
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
      ITEMS:
      item_image (or icon)
      item_date
      item_title
      item_subtitle
      item_descriptiontext
      item_readmore
     */

    public function controls_items_image_content($target, $type = '') {
        do_action("e_addons/query/item_controls/item_image", $target, $type);
    }

    // ----------------------------------------------------------
    public function controls_items_icon_content($target) {
        //Icon color-size
        $target->add_control(
                'icon_content_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-star"></i> <b>' . esc_html__('Icon', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => [
                'item_type' => 'item_imageoricon'
            ]
                ]
        );

        $target->add_control(
                'color_item_iconorimage', [
            'label' => esc_html__('Icon Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_imageoricon .e-add-query-icon' => 'color: {{VALUE}};',
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_imageoricon svg.e-add-query-icon' => 'fill: {{VALUE}};'
            ],
            'condition' => [
                'item_type' => 'item_imageoricon'
            ]
                ]
        );
        $target->add_responsive_control(
                'icon_size_imageoricon', [
            'label' => esc_html__('Icon size', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 200,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_imageoricon .e-add-query-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_imageoricon svg.e-add-query-icon' => 'width: {{SIZE}}%;',
            ],
            'condition' => [
                'item_type' => 'item_imageoricon'
            ]
                ]
        );
    }

    // +********************* Post: Title / Term: Title / User: User,Role,FirstName, LastName, DisplayName, NickName
    public function controls_items_title_content($target, $type = '') {
        if (!$type) {
            $type = $this->get_querytype();
        }
        $defval = 'h3';
        if ($type == 'user') {
            $defval = '';
        }
        $target->add_control(
                'html_tag', [
            'label' => esc_html__('HTML Tag', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'h1' => esc_html__('H1', 'e-addons'),
                'h2' => esc_html__('H2', 'e-addons'),
                'h3' => esc_html__('H3', 'e-addons'),
                'h4' => esc_html__('H4', 'e-addons'),
                'h5' => esc_html__('H5', 'e-addons'),
                'h6' => esc_html__('H6', 'e-addons'),
                'p' => esc_html__('p', 'e-addons'),
                'div' => esc_html__('div', 'e-addons'),
                'span' => esc_html__('span', 'e-addons'),
            ],
            'default' => $defval,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => [
                            'item_title',
                            'item_date',
                            'item_subtitle',
                            'item_user',
                            'item_role',
                            'item_firstname',
                            'item_lastname',
                            'item_displayname',
                            'item_nickname',
                            'item_email',
                            'item_website',
                            'item_alternativetext',
                            'item_caption',
                            'item_mimetype',
                            'item_counts',
                            'item_uploadedto'
                        ],
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* Post: Content/Excerpt / term: Description / User: Biography-Description
    public function controls_items_contentdescription_content($target, $type = '') {
        if (!$type) {
            $type = $this->get_querytype();
        }

        $target->add_control(
                'textcontent_limit', [
            'label' => esc_html__('Number of characters', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'description' => esc_html__('Leave Empty for all text.', 'e-addons'),
            'min' => 1,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_content', 'item_excerpt', 'item_bio', 'item_description'],
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* Date
    public function controls_items_date_content($target, $type = '') {
        if (!$type) {
            $type = $this->get_querytype();
        }
        if ($type == 'post') {
            $target->add_control(
                    'date_type', [
                'label' => esc_html__('Date Type', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'publish' => esc_html__('Publish Date', 'e-addons'),
                    'modified' => esc_html__('Last Modified Date', 'e-addons'),
                    'meta' => esc_html__('Custom Meta Field', 'e-addons'),
                    'custom' => esc_html__('Custom Code', 'e-addons'),
                ],
                'default' => 'publish',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'value' => 'item_date',
                        ]
                    ]
                ]
                    ]
            );
            $target->add_control(
                    'date_meta', [
                'label' => esc_html__('Custom Meta Field', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => esc_html__('Search Date Custom Field', 'e-addons'),
                'query_type' => 'metas',
                'object_type' => 'post',
                'label_block' => true,
                'condition' => [
                    'item_type' => ['item_date', 'item_registered'],
                    'date_type' => 'meta',
                ]
                    ]
            );
            $target->add_control(
                    'date_custom', [
                'label' => esc_html__('Custom Code', 'e-addons'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => '{{post.date}}', //[my-date-shortcode]',
                'condition' => [
                    'item_type' => ['item_date', 'item_registered'],
                    'date_type' => 'custom',
                ]
                    ]
            );
        }


        // added block_enable
        $target->add_control(
                'date_format', [
            'label' => esc_html__('Date Format', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => 'd/<b>m</b>/y',
            'condition' => [
                'item_type' => ['item_date', 'item_registered'],
            ]
                ]
        );
    }

    public function controls_items_imagemeta_content($target) {
        $target->add_control(
                'imagemedia_sizes', [
            'label' => esc_html__('Image Size', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => 'full',
            'options' => Query_Utils::get_available_image_sizes_options(),
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_imagemeta',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'imagemedia_metas', [
            'label' => esc_html__('Show Additional Info', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'place_holder' => 'Select Additional Info',
            'default' => ['dimension'],
            'options' => [
                'dimension' => 'Dimension',
                'file' => 'File name',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'value' => 'item_imagemeta',
                    ]
                ]
            ]
                ]
        );
    }

    // +********************* ReadMore
    public function controls_items_readmore_content($target) {
        do_action("e_addons/query/item_controls/item_readmore", $target);
    }

    // +********************* Template item
    public function controls_items_template_content($target) {
        do_action("e_addons/query/item_controls/item_template", $target);
    }

    // -------------- Label Html ---------
    public function controls_items_label_content($target) {
        do_action("e_addons/query/item_controls/item_label", $target);
    }

}
