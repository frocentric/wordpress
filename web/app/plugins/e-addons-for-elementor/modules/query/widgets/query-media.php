<?php

namespace EAddonsForElementor\Modules\Query\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;
use EAddonsForElementor\Modules\Query\Base\Query as Base_Query;
use EAddonsForElementor\Modules\Query\Widgets\Query_Posts;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Query Posts (L'idea potrebbe essere che altri estendono query_base come: query_terms e query_users )
 *
 * Elementor widget for E-Addons
 *
 */
class Query_Media extends Query_Posts {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        //$this->register_script('assets/js/e-addons-query-grid.js'); // from module folder
        //$this->register_style('assets/css/e-addons-query-grid.css'); // from module folder           
    }

    public function get_pid() {
        return 8349;
    }

    public function get_name() {
        return 'e-query-media';
    }

    public function get_title() {
        return esc_html__('Query Media', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-query-media';
    }

    protected $querytype = 'attachment';
    
    public $skins = [
        '\EAddonsForElementor\Modules\Query\Skins\Grid',
        '\EAddonsForElementor\Modules\Query\Skins\Carousel',
        '\EAddonsForElementor\Modules\Query\Skins\Justifiedgrid',
        '\EAddonsForElementor\Modules\Query\Skins\Dualslider',
        '\EAddonsForElementor\Modules\Query\Skins\Table',
        '\EAddonsForElementor\Modules\Query\Skins\Simple_List',
    ];

    protected function register_controls() {
        Base_Query::register_controls();

        // ------------------------------------------------------------------ [SECTION ITEMS]
        $this->start_controls_section(
                'section_items', [
            'label' => '<i class="eaddicon eicon-radio" aria-hidden="true"></i> ' . esc_html__('Media Items', 'e-addons'),
            'condition' => [
                '_skin!' => ['nextpost', 'mosaic'],
                'style_items!' => 'template',
            ],
                ]
        );

        ////////////////////////////////////////////////////////////////////////////////
        // -------- ORDERING & DISPLAY items
        $repeater = new Repeater();
        /*
          //item_image
          item_date
          item_title
          item_termstaxonomy
          item_alternativetext
          item_caption
          item_content
          item_author
          item_readmore
          item_custommeta
          item_imagemeta
          item_mimetype
          item_label
          //da valutare: uploaded_to ...

         */
        
        $item_types = [];
        $item_types = apply_filters( 'e_addons/query/item_types', $item_types );
        $item_types = apply_filters( 'e_addons/query/post/item_types', $item_types );
        $item_types = apply_filters( 'e_addons/query/'.$this->querytype.'/item_types', $item_types );
        
        $repeater->add_control(
                'item_type', [
            'label' => esc_html__('Item type', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => $item_types,
            'default' => 'item_image',
                ]
        );

        $this->add_item_tabs($repeater);

        $this->add_control(
                'list_items',
                [
                    'label' => esc_html__('Items', 'e-addons'),
                    'show_label' => false,
                    'separator' => 'before',
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'prevent_empty' => false,
                    'default' => [
                    /* [
                      'item_type' => 'item_image',
                      ] */
                    ],
                    //item_type.replace("item_", "")
                    'title_field' => '<# var etichetta = item_type; etichetta = etichetta.replace("item_", ""); #><b class="e-add-item-name"><i class="fa {{{ item_type+"-ic" }}}" aria-hidden="true"></i> {{{item_text_label}}} | {{{ etichetta }}}</b>',
                ]
        );

        $this->controls_items_grid_debug($this);

        $this->end_controls_section();

        //@p il TAB Query
        // ------------------------------------------------------------------ [SECTION - QUERY MEDIA]
        $this->start_controls_section(
                'section_query_posts', [
            'label' => '<i class="eaddicon eicon-settings" aria-hidden="true"></i> ' . esc_html__('Query', 'e-addons'),
            'tab' => 'e_query',
                ]
        );
        /*
          'specific_posts'
          'get_attachments'
          'custommeta_source'
          'satic_list'
         */
        $this->add_control(
                'query_debug', [
            'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show query for DEBUG', 'e-addons') . '</span>',
            'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'query_type', [
            'label' => esc_html__('Query Type', 'e-addons'),
            'type' => 'ui_selector',
            'toggle' => false,
            'type_selector' => 'icon',
            'columns_grid' => 3,
            'separator' => 'before',
            'label_block' => true,
            'options' => [
                /*
                  'automatic_mode' => [
                  'title' => esc_html__('Automatic Mode','e-addons'),
                  'return_val' => 'val',
                  'icon' => 'fa fa-cogs',
                  ],
                 */
                'specific_posts' => [
                    'title' => esc_html__('Specific Attachment', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'eicon-gallery-grid',
                ],
                'get_attachments' => [
                    'title' => esc_html__('Media Library', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'eicon-photo-library',
                ],
                'custommeta_source' => [
                    'title' => esc_html__('Custom Field Gallery', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'eicon-check-circle',
                ],
            ],
            'default' => 'get_attachments',
                ]
        );

        // --------------------------------- [ CustomMeta source ]
        $this->custommeta_source_items($this, 'post');
        $this->add_control(
                'avviso_custommeta_source',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-alert"></i> ' . esc_html__('If custom field is empty you can set a Fallback image gallery with the below control.', 'e-addons'),
                    'content_classes' => 'e-add-info-panel',
                    'condition' => [
                        'query_type' => 'custommeta_source',
                    ],
                ]
        );
        // --------------------------------- [ Specific Posts-Pages ] 
        $this->add_control(
                'specific_attachments',
                [
                    'label' => esc_html__('Add Medias', 'elementor'),
                    'type' => Controls_Manager::GALLERY,
                    'default' => [],
                    'show_label' => false,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'query_type' => ['specific_posts', 'custommeta_source'],
                    ],
                ]
        );

        // --------------------------------- [ Automatic mode ]
        $this->add_automatic_mode_warning();

        // --------------------------------- [ Custom Post Type ]
        $this->add_wp_query_post_args();
        
        $this->end_controls_section();

        // --------------------------------- [SECTION QUERY-FILTER]
        /*
          'query_filter'
          'date'
          'term'
          'author'
          'metakey'
          'mimetype'

          -------- DATE -------
          ''
          'past'
          'today'
          'yesterday'
          'days'
          'weeks'
          'months'
          'years'
          'period'

          -------- TERMS TAX -------
          'term_from'
          'post_term'
          'include_term'
          'include_term_combination'
          'exclude_term'
          'exclude_term_combination'
          'custom_meta'
          'term_field_meta'
          'current_term'

          -------- AUTHORS -------
          'author_from'
          'post_author'
          'include_author'
          'exclude_author'
          'custom_meta'
          'author_field_meta'
          'current_autor'

          -------- META KEY -------
          'metakey_list' [REPEATER]
          'metakey_field_meta'
          'metakey_field_meta_compare'
          'metakey_field_meta_type'
          'metakey_field_meta_value'
          //'metakey_field_meta_value_num'

          'metakey_combination'

          -------- COMMENTS -------


         */
        $this->start_controls_section(
                'section_query_filter', [
            'label' => '<i class="eaddicon eicon-parallax" aria-hidden="true"></i> ' . esc_html__('Query Filter', 'e-addons'),
            'tab' => 'e_query',
            'condition' => [
                'query_type' => ['get_attachments', 'automatic_mode']
            ]
                ]
        );
        $this->add_control(
                'query_filter', [
            'label' => esc_html__('Filter by', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'options' => [
                'date' => esc_html__('Date', 'elementor'),
                'term' => esc_html__('Term', 'elementor'),
                'author' => esc_html__('Author', 'elementor'),
                'metakey' => esc_html__('Meta key', 'elementor'),
                'search' => esc_html__('Search', 'elementor'),
                'mimetype' => esc_html__('Mime Type', 'elementor'),
                'post' => esc_html__('Post Parent', 'elementor')
            ],
            'multiple' => true,
            'label_block' => true,
            'default' => [],
                ]
        );
        // ******************** MimeType
        // get_available_post_mime_types()
        // get_allowed_mime_types()
        // 
        $options = ['all' => esc_html__('All', 'elementor')];
        if (is_admin()) {
            $options = $options + Query_Utils::get_available_mime_types_options();
        }
        $this->add_control(
                'filter_mimetype', [
            'label' => esc_html__('Mime Types', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'label_block' => true,
            'options' => $options,
            'default' => 'all',
            'place_holder' => esc_html__('Select specific Mime Types', 'e-addons'),
            'toggle' => false,
            'condition' => [
                'query_filter' => 'mimetype'
            ]
                ]
        );
        // ******************** Search
        $this->add_control(
                'heading_query_filter_search',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-search" aria-hidden="true"></i> ' . esc_html__('Search Filters', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        'query_filter' => 'search'
                    ],
                ]
        );
        $this->add_control(
                'search_field_value', [
            'label' => esc_html__('Search Value', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => '',
            'condition' => [
                'query_filter' => 'search'
            ]
                ]
        );
        $this->add_control(
                'info_filter_search',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-info-circle-o" aria-hidden="true"></i> ' . esc_html__('Prepending a term with a hyphen will exclude posts matching that term. Eg, "pillow -sofa" will return posts containing "pillow" but not "sofa".', 'e-addons'),
                    'content_classes' => 'e-add-info-panel',
                    'condition' => [
                        'query_filter' => 'search'
                    ],
                ]
        );
        // +********************* Date
        $this->add_control(
                'heading_query_filter_date',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-calendar" aria-hidden="true"></i> ' . esc_html__('Date Filters', 'e-addons'),
                    'label_block' => false,
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        'query_filter' => 'date',
                    ],
                ]
        );
        $this->add_control(
                'querydate_mode', [
            'label' => esc_html__('Upload Date Filter', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'label_block' => true,
            'default' => 'past',
            'options' => $this->get_date_options(),
            'condition' => [
                'query_filter' => 'date',
            ],
                ]
        );

        $this->add_control(
                'querydate_range', [
            'label' => esc_html__('Time ago', 'e-addons'),
            'label_block' => false,
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'condition' => [
                'query_filter' => 'date',
                'querydate_mode' => self::$dates_ago,
            ]
                ]
        );
        $this->add_control(
                'querydate_date_from', [
            'label' => esc_html__('Date FROM', 'e-addons'),
            'type' => Controls_Manager::DATE_TIME,
            'label_block' => false,
            'condition' => [
                'query_filter' => 'date',
                'querydate_mode' => 'period',
            ],
                ]
        );
        $this->add_control(
                'querydate_date_to', [
            'label' => esc_html__('Date TO', 'e-addons'),
            'type' => Controls_Manager::DATE_TIME,
            'label_block' => false,
            'condition' => [
                'query_filter' => 'date',
                'querydate_mode' => 'period',
            ],
                ]
        );

        // +********************* Term Taxonomy
        $this->add_control(
                'heading_query_filter_term',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-folder-o" aria-hidden="true"></i> ' . esc_html__('Term Filters', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        'query_filter' => 'term'
                    ],
                ]
        );
        // From Post or Meta
        $this->add_control(
                'term_from', [
            'label' => esc_html__('Type', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'label_block' => false,
            'options' => [
                'post_term' => [
                    'title' => esc_html__('Select Term', 'e-addons'),
                    'icon' => 'fa fa-tag',
                ],
                'custom_meta' => [
                    'title' => esc_html__('Media Meta Term', 'e-addons'),
                    'icon' => 'fa fa-square',
                ],
                'current_term' => [
                    'title' => esc_html__('Current Term', 'e-addons'),
                    'icon' => 'fa fa-cog',
                ],
            ],
            'default' => 'post_term',
            'toggle' => false,
            'condition' => [
                'query_filter' => 'term'
            ]
                ]
        );
        // [Post Meta]
        $this->add_control(
                'term_field_meta',
                [
                    'label' => esc_html__('Media Term', 'elementor').' <b>'.esc_html__('custom meta field', 'e-addons').'</b>',
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Search Meta Field', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'attachment',
                    'description' => esc_html__('Selected Media Custom field. The meta must return an element of type array or comma separated string that contains the term type IDs. (ex: array [5,27,88] or 5,27,88).', 'e-addons'),
                    'condition' => [
                        'term_from' => 'custom_meta',
                        'query_filter' => 'term'
                    ]
                ]
        );

        // [Post Term]
        $this->add_control(
                'include_term',
                [
                    'label' => '<b>'.esc_html__('Include', 'elementor').'</b> '.esc_html__('Term', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Media Terms', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    'render_type' => 'template',
                    'multiple' => true,
                    'condition' => [
                        'query_filter' => 'term',
                        'term_from' => 'post_term'
                    ],
                ]
        );
        $this->add_control(
                'include_term_combination',
                [
                    'label' => '<b>'.esc_html__('Include', 'elementor').'</b> '.esc_html__('Combination', 'e-addons'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'OR' => [
                            'title' => esc_html__('OR', 'e-addons'),
                            'icon' => 'eicon-circle-o',
                        ],
                        'AND' => [
                            'title' => esc_html__('AND', 'e-addons'),
                            'icon' => 'eicon-circle',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'OR',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'query_filter',
                                'operator' => 'contains',
                                'value' => 'term',
                            ],
                            [
                                'name' => 'query_filter',
                                'operator' => '!=',
                                'value' => [],
                            ],
                            [
                                'name' => 'include_term',
                                'operator' => '!=',
                                'value' => '',
                            ],
                            [
                                'name' => 'include_term',
                                'operator' => '!=',
                                'value' => [],
                            ]/* ,
                          [
                          'name' => 'term_from',
                          'value' => 'post_term',
                          ], */
                        ]
                    ]
                ]
        );
        $this->add_control(
                'exclude_term',
                [
                    'label' => '<b>'.esc_html__('Exclude', 'elementor').'</b> '.esc_html__('Term', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Media Terms', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    'render_type' => 'template',
                    'multiple' => true,
                    'condition' => [
                        'query_filter' => 'term',
                    //'term_from' => 'post_term'
                    ],
                ]
        );
        $this->add_control(
                'exclude_term_combination',
                [
                    'label' => '<b>'.esc_html__('Exclude', 'elementor').'</b> '.esc_html__('Combination', 'e-addons'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'OR' => [
                            'title' => esc_html__('OR', 'e-addons'),
                            'icon' => 'eicon-circle-o',
                        ],
                        'AND' => [
                            'title' => esc_html__('AND', 'e-addons'),
                            'icon' => 'eicon-circle',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'OR',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'query_filter',
                                'operator' => 'contains',
                                'value' => 'term',
                            ],
                            [
                                'name' => 'query_filter',
                                'operator' => '!=',
                                'value' => [],
                            ],
                            [
                                'name' => 'exclude_term',
                                'operator' => '!=',
                                'value' => '',
                            ],
                            [
                                'name' => 'exclude_term',
                                'operator' => '!=',
                                'value' => [],
                            ]/* ,
                          [
                          'name' => 'term_from',
                          'value' => 'post_term',
                          ], */
                        ]
                    ]
                ]
        );

        // +********************* Author
        $this->add_control(
                'heading_query_filter_author',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-circle-o" aria-hidden="true"></i> ' . esc_html__('Author Filters', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        'query_filter' => 'author'
                    ],
                ]
        );
        // From: Post, Meta or Current
        $this->add_control(
                'author_from', [
            'label' => esc_html__('Type', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'label_block' => false,
            'options' => [
                'post_author' => [
                    'title' => esc_html__('Select Author', 'e-addons'),
                    'icon' => 'fa fa-users',
                ],
                'custom_meta' => [
                    'title' => esc_html__('Post Meta Author', 'e-addons'),
                    'icon' => 'fa fa-square',
                ],
                'current_author' => [
                    'title' => esc_html__('Current Author', 'e-addons'),
                    'icon' => 'fa fa-user-cog',
                ],
            ],
            'default' => 'post_author',
            'toggle' => false,
            'condition' => [
                'query_filter' => 'author'
            ],
                ]
        );
        // [Post Meta]
        $this->add_control(
                'author_field_meta',
                [
                    'label' => esc_html__('Media Author', 'elementor').' <b>'.esc_html__('custom meta field', 'e-addons').'</b>',
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Search Custom Author meta', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'attachment',
                    'default' => 'nickname',
                    'description' => esc_html__('Selected Media Meta value. Selected Media Custom field. The meta must return an element of type array or comma separated string that contains the term type IDs. (ex: array [5,27,88] or 5,27,88).', 'e-addons'),
                    'condition' => [
                        'author_from' => 'custom_meta',
                        'query_filter' => 'author'
                    ]
                ]
        );

        // [Select Authors]
        $this->add_control(
                'include_author',
                [
                    'label' => '<b>'.esc_html__('Include', 'elementor').'</b> '.esc_html__('Author', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Authors', 'e-addons'),
                    'label_block' => true,
                    'multiple' => true,
                    'query_type' => 'users',
                    //'object_type'   => 'editor',
                    'description' => esc_html__('Filter Medias by selected Authors', 'e-addons'),
                    'condition' => [
                        'query_filter' => 'author',
                        'author_from' => 'post_author'
                    ]
                ]
        );

        $this->add_control(
                'exclude_author',
                [
                    'label' => '<b>'.esc_html__('Exclude', 'elementor').'</b> '.esc_html__('Author', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Authors', 'e-addons'),
                    'label_block' => true,
                    'multiple' => true,
                    'query_type' => 'users',
                    //'object_type'   => 'editor',
                    'description' => esc_html__('Filter Medias by selected Authors', 'e-addons'),
                    'separator' => 'after',
                    'condition' => [
                        'query_filter' => 'author',
                        'author_from' => 'post_author'
                    ]
                ]
        );

        // ****************** Post Parent
        $this->add_control(
                'heading_query_filter_post',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-post" aria-hidden="true"></i> ' . esc_html__('Post Parent', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        'query_filter' => 'post'
                    ],
                ]
        );
        // [Post Uploaded]
        $this->add_control(
                'post_parent',
                [
                    'label' => esc_html__('Post from Media was uploaded', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Search Post', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'condition' => [
                        'query_filter' => 'post'
                    ]
                ]
        );

        // ****************** Meta key
        $this->add_control(
                'heading_query_filter_metakey',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fa fa-key" aria-hidden="true"></i> ' . esc_html__('Custom Meta Field Filters', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        'query_filter' => 'metakey'
                    ],
                ]
        );

        // [Post Meta]
        $repeater_metakeys = new Repeater();

        $repeater_metakeys->add_control(
                'metakey_field_meta',
                [
                    'label' => esc_html__('Media Custom Field', 'elementor').' <b>'.esc_html__('custom meta key', 'e-addons').'</b>',
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Name', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'attachment',
                    'description' => esc_html__('Selected Post Meta value. The meta must return an element of type array or comma separated string that contains IDs of type metakey . (es: array[5,27,88] o 5,27,88)', 'e-addons'),
                ]
        );
        $repeater_metakeys->add_control(
                'metakey_field_meta_type', [
            'label' => esc_html__('Value Type', 'elementor'),
            'description' => esc_html__('Custom field type. Default value is (CHAR)', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => Query_Utils::get_meta_comparetype(),
            'default' => 'CHAR',
            'label_block' => true
                ]
        );
        $repeater_metakeys->add_control(
                'metakey_field_meta_compare', [
            'label' => esc_html__('Compare Operator', 'elementor'),
            'description' => esc_html__('Comparison operator. Default value is (=)', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => Query_Utils::get_meta_compare(),
            'default' => '=',
            'label_block' => true
                ]
        );

        $repeater_metakeys->add_control(
                'metakey_field_meta_value', [
            'label' => esc_html__('Media Field Value', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'description' => esc_html__('The specific value of the Media Field', 'elementor'),
            'label_block' => true,
            'condition' => [
                'metakey_field_meta_compare!' => ['EXISTS', 'NOT EXISTS']
            ]
                ]
        );
        // il metakey REPEATER
        $this->add_control(
                'metakey_list',
                [
                    'label' => esc_html__('Custom Meta Fields', 'e-addons'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater_metakeys->get_controls(),
                    'title_field' => '{{{ metakey_field_meta }}}',
                    'prevent_empty' => false,
                    'condition' => [
                        'query_filter' => 'metakey',
                    ]
                ]
        );
        $this->add_control(
                'metakey_combination',
                [
                    'label' => '<b>'.esc_html__('Custom Field', 'elementor').'</b> '.esc_html__('Combination', 'e-addons'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'OR' => [
                            'title' => esc_html__('OR', 'e-addons'),
                            'icon' => 'fa fa-circle-o',
                        ],
                        'AND' => [
                            'title' => esc_html__('AND', 'e-addons'),
                            'icon' => 'fa fa-circle',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'OR',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'query_filter',
                                'operator' => 'contains',
                                'value' => 'metakey',
                            ],
                            [
                                'name' => 'query_filter',
                                'operator' => '!=',
                                'value' => [],
                            ],
                            [
                                'name' => 'metakey_list',
                                'operator' => '!=',
                                'value' => '',
                            ],
                            [
                                'name' => 'metakey_list',
                                'operator' => '!=',
                                'value' => [],
                            ]
                        ]
                    ]
                ]
        );
        $this->end_controls_section();

        $this->add_no_result_section();
    }

    // --------------------------------- [ Media Options ]
    //@p questo metodo ignetta nella prima section le opzioni per media: 
    /*
      - Lightbox
     */

    public function items_query_controls() {
        $this->add_control(
                'heading_imagelink',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-editor-external-link"></i> &nbsp;' . esc_html__('LINK & LIGHTBOX:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                ]
        );

        $options = [
            'file' => esc_html__('Media File', 'elementor'),
            'attachment' => esc_html__('Attachment Page', 'elementor'),
            'custom' => esc_html__('Custom'),
            'none' => esc_html__('None', 'elementor'),
        ];
        if (Utils::is_plugin_active('elementor-pro') && Utils::is_plugin_active('e-addons-extended')) {
            $options['popup'] = esc_html__('Open PopUp');
        }

        $this->add_control(
                'gallery_link',
                [
                    'label' => esc_html__('Link', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'file',
                    'options' => $options,
                ]
        );
        $this->add_control(
                'shortcode_link', [
            'label' => esc_html__('Custom link', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'gallery_link' => ['shortcode', 'custom'],
            ]
                ]
        );
        if (Utils::is_plugin_active('elementor-pro') || Utils::is_plugin_active('e-addons-extended')) {
            $this->add_control(
                    'popup_link', [
                'label' => esc_html__('Open PopUp', 'e-addons'),
                'type' => 'e-query',
                'placeholder' => esc_html__('Select PopUp', 'e-addons'),
                'label_block' => true,
                'query_type' => 'posts',
                'object_type' => 'elementor_library',
                'condition' => [
                    'gallery_link' => 'popup',
                ]
                    ]
            );
        }

        $this->add_control(
                'open_lightbox',
                [
                    'label' => esc_html__('Lightbox', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'default',
                    'options' => [
                        'default' => esc_html__('Default', 'elementor'),
                        'yes' => esc_html__('Yes', 'elementor'),
                        'no' => esc_html__('No', 'elementor'),
                    ],
                    'condition' => [
                        'gallery_link' => 'file',
                    ],
                ]
        );
        $this->add_control(
                'heading_imageoptions',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-image"></i> &nbsp;' . esc_html__('IMAGE OPTIONS:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                ]
        );
        $this->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'thumbnail_size',
            'label' => esc_html__('Image Format', 'e-addons'),
            'default' => 'large',
                ]
        );
        $this->add_responsive_control(
                'ratio_image', [
            'label' => esc_html__('Image Ratio', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'separator' => 'before',
            'range' => [
                'px' => [
                    'min' => 0.1,
                    'max' => 2,
                    'step' => 0.01
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-repeater-item-e-add-media-image .e-add-img' => 'padding-bottom: calc( {{SIZE}} * 100% );', '{{WRAPPER}}:after' => 'content: "{{SIZE}}";',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => '_skin',
                        'operator' => '!in',
                        'value' => ['justifiedgrid'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $this->add_responsive_control(
                'width_image', [
            'label' => esc_html__('Image Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%', 'px', 'vw'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1
                ],
                'vw' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1
                ],
                'px' => [
                    'min' => 1,
                    'max' => 800,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-repeater-item-e-add-media-image .e-add-post-image' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => '_skin',
                        'operator' => '!in',
                        'value' => ['justifiedgrid'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
                'use_bgimage', [
            'label' => esc_html__('Background mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'render_type' => 'template',
            'selectors' => [
                '{{WRAPPER}} .e-add-image-area, {{WRAPPER}}.e-add-posts-layout-default .e-add-post-bgimage' => 'position: relative;',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => '_skin',
                        'operator' => '!in',
                        'value' => ['justifiedgrid'],
                    ]
                ]
            ]
                ]
        );
        $this->add_responsive_control(
                'height_bgimage', [
            'label' => esc_html__('Height', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vh'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 800,
                    'step' => 1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-repeater-item-e-add-media-image .e-add-post-image.e-add-post-bgimage' => 'height: {{SIZE}}{{UNIT}};'
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'use_bgimage',
                        'operator' => '!=',
                        'value' => '',
                    ],
                    [
                        'name' => '_skin',
                        'operator' => '!in',
                        'value' => ['justifiedgrid'],
                    ]
                ]
            ]
                ]
        );
        $this->add_control(
                'use_overlay', [
            'label' => esc_html__('Overlay', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'prefix_class' => 'overlayimage-',
            'render_type' => 'template',
                ]
        );
        $this->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'overlay_color',
            'label' => esc_html__('Background', 'e-addons'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .elementor-repeater-item-e-add-media-image .e-add-post-image.e-add-post-overlayimage:after',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'use_overlay',
                        'operator' => '!==',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $this->add_responsive_control(
                'overlay_opacity', [
            'label' => esc_html__('Opacity (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 0.7,
            ],
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-repeater-item-e-add-media-image .e-add-post-image.e-add-post-overlayimage:after' => 'opacity: {{SIZE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'use_overlay',
                        'operator' => '!==',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
    }

    // 
    public function query_the_elements() {

        $query_vars = $this->get_query_args();

        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty($this->get_settings_for_display('query_debug'))) {
                echo '<pre>';
                var_dump($query_vars);
                echo '</pre>';
            }
        }

        $query_vars = apply_filters('e_addons/query_posts/query_vars', $query_vars);           
        $query_vars = apply_filters('e_addons/query_medias/query_vars', $query_vars);   

        $query_m = new \WP_Query($query_vars);

        do_action('elementor/query/query_results', $query_m, $this);

        $this->query = $query_m;
    }

    public function get_query_args() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $args = array();

        /*
          '1 - automatic_mode'
          '2 - all attachments'
          '3 - custommeta_source'
          '4 - specific_posts'

         */
        $query_type = $settings['query_type'];

        switch ($query_type) {
            case 'automatic_mode':
                global $wp_query;
            //echo '<pre>'; var_dump($wp_query); echo '</pre>';

            /** @var Module_Query $elementor_query */
            //$elementor_query = Module_Query::instance();
            //$this->query = $elementor_query->get_query( $this, 'posts', $query_args, [] );

            case 'custommeta_source':
                /*
                  $custommeta_source_key = $settings['custommeta_source_key'];
                  //$custommeta_source_type = $settings['custommeta_source_type'];
                  if (empty($custommeta_source_key))
                  return;
                  $type_of_location = Query_Utils::is_type_of();
                  $id_of_location = Query_Utils::is_id_of();
                  $custommeta_source_value = get_metadata($type_of_location, $id_of_location, $custommeta_source_key, true);
                 */
                $custommeta_source_value = $this->get_custom_meta_source_value($settings);
                //var_dump($custommeta_source_value);
                if (!empty($custommeta_source_value)) {
                    // default args
                    $args['posts_per_page'] = -1;
                    $args['orderby'] = 'post__in';
                    //
                    $args['post__in'] = Utils::explode($custommeta_source_value);
                    break;
                } else {
                    if (empty($settings['specific_attachments'])) {
                        $args['post__in'] = array(-1);
                        break;
                    }
                }

            case 'specific_posts':
                $images = [];
                $args['post__in'] = [-1];
                $specific_attachments = Utils::explode($settings['specific_attachments']);
                if (!empty($specific_attachments) && is_array($specific_attachments)) {
                    $items_specific_posts = array();
                    foreach ($specific_attachments as $item) {
                        if (!empty($item['id'])) {
                            //array_push($items_specific_posts, $item['id']);
                            $item_sp_posts = Utils::explode($item['id']);
                            foreach ($item_sp_posts as $aitem) {
                                array_push($items_specific_posts, $aitem);
                            }
                        } else {
                            if (is_string($item) && filter_var($item, FILTER_VALIDATE_URL)) {
                                $images[] = $item;
                            } else if (is_array($item)) {
                                if (!empty($item['url'])) {
                                    $images[] = $item['url'];
                                }
                            }
                        }
                    }
                    if (!empty($images)) {
                        $args['urls'] = $images;
                        $args['post__in'] = [-1];
                    }
                    if (count($items_specific_posts)) {
                        $args['posts_per_page'] = -1;
                        //$args['orderby'] = $settings['orderby'] == 'menu_order' ? 'post__in' : $settings['orderby'];
                        $args['orderby'] = 'post__in';
                        $args['post__in'] = $items_specific_posts;
                    }
                }
                break;
        }

        /*
          'post_type'
          --'posts_per_page'
          --'posts_offset'
          --'orderby'
          --'metakey' ...
          --'order'
          --'exclude_posts'
         */

        //@p è scontato che il type è "attachment"
        $args['post_type'] = 'attachment';
        $args['post_status'] = ['inherit', 'publish'];

        // limit posts per page
        if (!empty($settings['posts_per_page']))
            $args['posts_per_page'] = $settings['posts_per_page'];

        // offset
        if (!empty($settings['posts_offset']))
            $args['offset'] = $settings['posts_offset'];

        // paginazione
        if (!empty($settings['pagination_enable']) || !empty($settings['infiniteScroll_enable']))
            $args['paged'] = $this->get_current_page();


        // order by
        if (!empty($settings['orderby']))
            $args['orderby'] = $settings['orderby'];
        //meta key order
        if (!empty($settings['metakey']))
            $args['meta_key'] = $settings['metakey'];
        // order asc-desc
        if (!empty($settings['order']))
            $args['order'] = $settings['order'];

        // exclusion posts
        if (!empty($settings['include_posts'])) {
            $args['post__in'] = Utils::explode($settings['include_posts']);
        }

        // exclusion posts
        if (!empty($settings['exclude_posts'])) {
            $args['post__not_in'] = Utils::explode($settings['exclude_posts']);
        }

        /*
          'query_filter'
          'date'
          'term'
          'author'
          'metakey'
          'mimetype'
         */
        $query_filters = $settings['query_filter'];
        if (!empty($query_filters))
            foreach ($query_filters as $filter) {

                switch ($filter) {
                    case 'date':
                        $args = array_merge($args, $this->get_date_filter($settings));
                        break;
                    case 'term':
                        $args = array_merge($args, $this->get_terms_filter($settings));
                        break;
                    case 'author':
                        $args = array_merge($args, $this->get_author_filter($settings));
                        break;
                    case 'metakey':
                        $args = array_merge($args, $this->get_metakey_filter($settings));
                        break;
                    case 'search':
                        $args = array_merge($args, $this->get_search_filter($settings));
                        break;
                    case 'mimetype':
                        $args = array_merge($args, $this->get_mimetype_filter($settings));
                        break;
                    case 'post':
                        $args = array_merge($args, $this->get_post_filter($settings));
                        break;
                }
            }
        return $args;
    }

    protected function get_mimetype_filter($settings) {
        /*
          'mimetype_field_value'
         */
        $mimetype_args = array();
        $mimetypes_field_value = $settings['filter_mimetype'];
        $mimetype_args['post_mime_type'] = $mimetypes_field_value;
        return $mimetype_args;
    }

    protected function get_post_filter($settings) {

        $search_args = array();

        if (!empty($settings['post_parent'])) {
            $search_args['post_parent__in'] = Utils::explode($settings['post_parent'], null, null, 'intval');
            if (empty($search_args['post_parent__in'])) {
                unset($search_args['post_parent__in']);
            }
        }

        return $search_args;
    }

    public function loop($skin, $query) {
        /** @p qui identifico se mi trovo in un loop, altrimenti uso la wp_query */
        if ($query->in_the_loop) {
            $skin->current_permalink = get_permalink();
            $skin->current_id = get_the_ID();
            $skin->current_data = get_post(get_the_ID());
            //
            $skin->render_element_item();
        } else {
            global $post, $wp_query;
            $current_post = get_post();

            $i = 0;
            $j = 0;
            $offset = $skin->parent->get_settings_for_display('posts_offset');
            $limit = $skin->parent->get_settings_for_display('posts_limit');
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $i++;
                    $query->the_post();
                    $continue = false;
                    if ($limit) {
                        if ($offset) {
                            if ($i <= $offset) {
                                $continue = true;
                            }
                        }
                        if (!$continue) {
                            $j++;
                        }
                        if ($j > $limit) {
                            $continue = true;
                        }
                    }
                    if (!$continue) {
                        $skin->current_permalink = get_permalink();
                        $skin->current_id = get_the_ID();
                        $skin->current_data = get_post(get_the_ID());
                        //
                        $skin->render_element_item();
                    }
                }
            } else {
                //var_dump($query);
                if (!empty($query->query['urls'])) {
                    foreach ($query->query['urls'] as $key => $img_url) {
                        $skin->current_permalink = $img_url;
                        $skin->current_id = $key;
                        $skin->current_data = $img_url;
                        $skin->render_element_item();
                    }
                }
            }
        }

        wp_reset_postdata();
        if ($current_post) {
            $post = $current_post;
            if (isset($wp_query)) {
                if (wp_doing_ajax()) {
                    $wp_query->post = $post;
                    $wp_query->queried_object = $post;
                    $wp_query->queried_object_id = $post->ID;
                }
                $wp_query->setup_postdata($post);
            }
        }
    }

}