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

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Query Posts (L'idea potrebbe essere che altri estendono query_base come: query_terms e query_users )
 *
 * Elementor widget for E-Addons
 *
 */
class Query_Posts extends Base_Query {
    
    public $list_items_default = ['item_image', 'item_title', 'item_date'];

    public function get_pid() {
        return 7574;
    }

    public function get_name() {
        return 'e-query-posts';
    }

    public function get_title() {
        return esc_html__('Query Posts', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-query-posts';
    }

    protected $querytype = 'post';
    public $skins = [
        '\EAddonsForElementor\Modules\Query\Skins\Grid',
        '\EAddonsForElementor\Modules\Query\Skins\Carousel',
        '\EAddonsForElementor\Modules\Query\Skins\Timeline',
        '\EAddonsForElementor\Modules\Query\Skins\Dualslider',
        '\EAddonsForElementor\Modules\Query\Skins\Table',
        '\EAddonsForElementor\Modules\Query\Skins\Simple_List',
        //'\EAddonsSkins\Modules\Query\Skins\RapidImages',
    ];

    protected function register_controls() {
        parent::register_controls();

        $types = Utils::get_post_types();
        
        $this->add_section_items();

        //@p il TAB Query
        // ------------------------------------------------------------------ [SECTION - QUERY POSTS]
        $this->start_controls_section(
                'section_query_posts', [
            'label' => '<i class="eaddicon eicon-settings" aria-hidden="true"></i> ' . esc_html__('Query', 'e-addons'),
            'tab' => 'e_query',
                ]
        );
        /*
          'automatic_mode'
          'get_cpt'
          'post_parent'
          'custommeta_source'
          'specific_posts'
          'satic_list'
         */
        $this->add_control(
                'query_debug', [
            'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show query for DEBUG', 'e-addons') . '</span>',
            'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'query_id', [
            'label' => esc_html__('Query ID', 'elementor-pro'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'description' => esc_html__('Give your Query a custom unique id to allow server side filtering', 'elementor-pro'),
                ]
        );

        $this->add_control(
                'query_type', [
            'label' => esc_html__('Query Type', 'e-addons'),
            'type' => 'ui_selector',
            'toggle' => false,
            'type_selector' => 'icon',
            'columns_grid' => 5,
            'separator' => 'before',
            'label_block' => true,
            'options' => [
                'automatic_mode' => [
                    'title' => esc_html__('Automatic', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'fa fa-cogs',
                ],
                'get_cpt' => [
                    'title' => esc_html__('Post Type', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'fas fa-thumbtack',
                ],
                'post_parent' => [
                    'title' => esc_html__('Post Parent', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'fa fa-sitemap',
                ],
                'custommeta_source' => [
                    'title' => esc_html__('Custom Meta Field', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'fas fa-check-double',
                ],
                'specific_posts' => [
                    'title' => esc_html__('Specific Posts', 'e-addons'),
                    'return_val' => 'val',
                    'icon' => 'far fa-copy',
                ],
            ],
            'default' => 'get_cpt',
                ]
        );

        // --------------------------------- [ Post Parent ]
        /*
          'specific_page_parent'
          'dynamic_my_siblings'
          'dynamic_my_children'
         */
        $this->add_control(
                'specific_page_parent',
                [
                    'label' => esc_html__('Show the children of this parent', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Page/Post Title', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'multiple' => true,
                    //'object_type' => 'page',
                    'condition' => [
                        'query_type' => 'post_parent',
                    ],
                ]
        );
        
        $this->add_control(
                'specific_page_parent_ancestor',
                [
                    'label' => esc_html__('Add all descendants', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => esc_html__('Include also children of the children of selected post and all its direct descendants', 'elementor-pro'),
                    'condition' => [
                        'query_type' => 'post_parent',
                        'specific_page_parent!' => ''
                    ],
                ]
        );

        $this->add_specific_posts_repeater();

        // --------------------------------- [ CustomMeta source ]
        $this->custommeta_source_items($this, 'post');

        // --------------------------------- [ Automatic mode ]
        $this->add_automatic_mode_warning();

        // I fratelli
        $this->add_control(
                'dynamic_my_siblings', [
            'label' => esc_html__('Show Siblings', 'e-addons'),
            'description' => esc_html__('Display current post Siblings (posts with same parent).', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'query_type' => 'automatic_mode',
                'dynamic_my_children' => ''
            ],
                ]
        );
        // I figli
        $this->add_control(
                'dynamic_my_children', [
            'label' => esc_html__('Show Children', 'e-addons'),
            'description' => esc_html__('Display current post Children (if any).', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'query_type' => 'automatic_mode',
                'dynamic_my_siblings' => ''
            ],
                ]
        );
        // --------------------------------- [ Custom Post Type ]

        /*
          'post_type'
          'post_status'
          'ignore_sticky_posts'
          'posts_per_page'
          'posts_offset'
          'orderby'
          'metakey' ...
          'order'
          'exclude_myself'
          'exclude_posts'
         */
        $this->add_control(
                'post_type', [
            'label' => esc_html__('Post Type', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'options' => $types,
            'multiple' => true,
            'label_block' => true,
            'default' => [],
            'condition' => [
                'query_type' => 'get_cpt',
            ],
                ]
        );

        $this->add_wp_query_args();

        $this->end_controls_section();

        // ------------------------------------------------------------------ [SECTION QUERY-FILTER]
        $this->add_wp_query_filters();

        $this->add_no_result_section();
    }
    
    public function add_item_tabs($repeater) {
        // TABS ----------
        $repeater->start_controls_tabs('items_repeater_tab');

        // CONTENT - TAB
        $repeater->start_controls_tab('tab_content', [
            'label' => esc_html__('Content', 'e-addons'),
        ]);
        $this->add_controls_content($repeater);
        do_action("e_addons/query/item_controls/content", $repeater, $this->get_querytype());
        $repeater->end_controls_tab();

        // STYLE - TAB
        $repeater->start_controls_tab('tab_style', [
            'label' => esc_html__('Style', 'e-addons'),
            'condition' => [
                'item_type!' => 'item_template'
            ]
        ]);
        $this->add_controls_style($repeater);
        do_action("e_addons/query/item_controls/style", $repeater, $this->get_querytype());
        $repeater->end_controls_tab();

        // ADVANCED - TAB
        $repeater->start_controls_tab('tab_advanced', [
            'label' => esc_html__('Advanced', 'e-addons'),
        ]);
        // @p considero i campi avanzati: se è linkato (use_link) e se l'item è Block o Inline
        $this->controls_items_advanced($repeater);
        do_action("e_addons/query/item_controls/advanced", $repeater, $this->get_querytype());
        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();
    }

    public function add_wp_query_filters() {
        /*
          'query_filter'
          'date'
          'term'
          'author'
          'metakey'
          'comments'

          -------- DATE -------
          'querydate_mode'
          ''
          'past'
          'querydate_field_meta_format'
          'future'
          'querydate_field_meta_future'
          'querydate_field_meta_future_format'
          'today'

          'yesterday'

          'days'
          'weeks'
          'months'
          'years'
          'querydate_range'
          'period'
          'querydate_date_type'
          'querydate_date_to'
          'querydate_date_from_dynamic'
          'querydate_date_to_dynamic'

          'querydate_field'
          'publish_date'
          //'post_modified'
          'custom_meta'

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
                //'query_type' => ['get_cpt', 'automatic_mode', 'custommeta_source']
            ]
                ]
        );
        $this->add_control(
                'query_filter', [
            'label' => esc_html__('By', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'options' => [
                'date' => 'Date',
                'term' => 'Term',
                'author' => 'Author',
                'metakey' => 'Meta key',
                'search' => 'Search'
            //'comments' => 'Comments' ...TO DO
            ],
            'multiple' => true,
            'label_block' => true,
            'default' => [],
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
            'label' => esc_html__('Serch Value', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
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
                    'raw' => '<i class="eicon-info" aria-hidden="true"></i> ' . esc_html__('Prepending a term with a hyphen will exclude posts matching that term. Eg, "pillow -sofa" will return posts containing "pillow" but not "sofa".', 'e-addons'),
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

        $date_options = $this->get_date_options('future');
        $this->add_control(
                'querydate_mode', [
            'label' => esc_html__('Date Filter', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'label_block' => true,
            'default' => 'past',
            'options' => $date_options,
            'condition' => [
                'query_filter' => 'date',
            ],
                ]
        );

        $this->add_control(
                'querydate_field', [
            'label' => esc_html__('Date Field', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'label_block' => false,
            'options' => [
                'publish_date' => [
                    'title' => esc_html__('Publish Date', 'e-addons'),
                    'icon' => 'eicon-calendar',
                ],
                'post_modified' => [
                    'title' => esc_html__('Modified Date', 'e-addons'),
                    'icon' => 'eicon-edit',
                ],
                'comment_date' => [
                    'title' => esc_html__('Comment Date', 'e-addons'),
                    'icon' => 'eicon-comments',
                ],
            /* 'custom_meta' => [
              'title' => esc_html__('Post Meta', 'e-addons'),
              'icon' => 'eicon-square',
              ], */
            ],
            'default' => 'publish_date',
            'toggle' => false,
            'condition' => [
                'query_filter' => 'date',
            //'querydate_mode!' => ['', 'future'],
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
                    'raw' => '<i class="eicon-folder-o" aria-hidden="true"></i> ' . esc_html__(' Term Filters', 'e-addons'),
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
                    'title' => esc_html__('Post Meta Term', 'e-addons'),
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
            ],
                ]
        );

        $this->add_control(
                'current_term_taxonomy',
                [
                    'label' => '<b>' . esc_html__('Include', 'elementor') . '</b> ' . esc_html__('Taxonomies', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Meta key or Name', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'taxonomies',
                    'multiple' => true,
                    'description' => esc_html__('Include Current Terms only of this specific Taxonomies', 'e-addons'),
                    'condition' => [
                        'term_from' => 'current_term',
                        'query_filter' => 'term'
                    ]
                ]
        );

        // [Post Meta]
        $this->add_control(
                'term_field_meta',
                [
                    'label' => esc_html__('Post Term', 'elementor') . ' <b>' . esc_html__('custom meta field', 'elementor') . '</b>',
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Name', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'post',
                    'description' => esc_html__('Selected Post Meta value. The meta must return an element of type array or comma separated string that contains the term type IDs. (ex: array [5,27,88] or 5,27,88).', 'e-addons'),
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
                    'label' => '<b>' . esc_html__('Include', 'elementor') . '</b> ' . esc_html__('Term', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('All terms', 'e-addons'),
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
                'include_term_children',
                [
                    'label' => esc_html__('Include Term Children', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'description' => esc_html__('Whether or not to include children for hierarchical taxonomies', 'e-addons'),
                    'default' => 'yes',
                    'condition' => [
                        'query_filter' => 'term',
                    ],
                ]
        );

        $this->add_control(
                'include_term_combination',
                [
                    'label' => '<b>' . esc_html__('Include', 'elementor') . '</b> ' . esc_html__('Combination', 'e-addons'),
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
                /* 'conditions' => [
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
                  ],
                  [
                  'name' => 'term_from',
                  'value' => 'post_term',
                  ],
                  ]
                  ] */
                ]
        );
        $this->add_control(
                'exclude_term',
                [
                    'label' => '<b>' . esc_html__('Exclude', 'elementor') . '</b> ' . esc_html__('Term', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select terms', 'e-addons'),
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
                    'label' => '<b>' . esc_html__('Exclude', 'elementor') . '</b> ' . esc_html__('Combination', 'elementor'),
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
                    'raw' => '<i class="eicon-circle-o" aria-hidden="true"></i> ' . esc_html__(' Author Filters', 'e-addons'),
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
                'author_role' => [
                    'title' => esc_html__('Role', 'e-addons'),
                    'icon' => 'fa fa-user-cog',
                ],
                'current_autor' => [
                    'title' => esc_html__('Current Author', 'e-addons'),
                    'icon' => 'fa fa-user',
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
                    'label' => esc_html__('Post author', 'elementor') . ' <b>' . esc_html__('custom meta field', 'elementor') . '</b>',
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Name', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'post',
                    'default' => 'nickname',
                    'description' => esc_html__('Selected Post Meta value. The meta must return an element of type array or comma separated string containing author IDs. (es: array[5,27,88] o 5,27,88)', 'e-addons'),
                    'condition' => [
                        'author_from' => 'custom_meta',
                        'query_filter' => 'author'
                    ]
                ]
        );

        $this->add_control(
                'author_role',
                [
                    'label' => esc_html__('Author', 'elementor') . ' <b>' . esc_html__('role', 'elementor') . '</b>',
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Search Roles', 'e-addons'),
                    'multiple' => true,
                    'query_type' => 'users',
                    'object_type' => 'role',
                    'label_block' => true,
                    'condition' => [
                        'author_from' => 'author_role',
                        'query_filter' => 'author'
                    ]
                ]
        );

        // [Select Authors]
        $this->add_control(
                'include_author',
                [
                    'label' => '<b>' . esc_html__('Include') . ' </b>' . esc_html__('Author', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('All', 'e-addons'),
                    'label_block' => true,
                    'multiple' => true,
                    'query_type' => 'users',
                    //'object_type'   => 'editor',
                    'description' => esc_html__('Filter posts by selected Authors', 'e-addons'),
                    'condition' => [
                        'query_filter' => 'author',
                        'author_from' => 'post_author'
                    ]
                ]
        );

        $this->add_control(
                'exclude_author',
                [
                    'label' => '<b>' . esc_html__('Exclude') . ' </b>' . esc_html__('Author', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Author', 'e-addons'),
                    'label_block' => true,
                    'multiple' => true,
                    'query_type' => 'users',
                    //'object_type'   => 'editor',
                    'description' => esc_html__('Filter posts by selected Authors', 'e-addons'),
                    'separator' => 'after',
                    'condition' => [
                        'query_filter' => 'author',
                        'author_from' => 'post_author'
                    ]
                ]
        );

        $this->add_controls_metaquery();
        
        $this->end_controls_section();
    }

    public function add_wp_query_args() {
        $options_stati = get_post_stati();
        //var_dump($options_stati); die();
        $this->add_control(
                'post_status', [
            'label' => esc_html__('Post Status', 'e-addons'),
            'type' => Controls_Manager::SELECT2,
            'separator' => 'before',
            'options' => $options_stati + ['any' => __('Any')],
            'multiple' => true,
            'label_block' => true,
            'default' => ['publish'],
            'condition' => [
                //'query_type' => ['get_cpt', 'automatic_mode', 'specific_posts'],
            ],
                ]
        );
        $this->add_control(
                'ignore_sticky_posts', [
            'label' => esc_html__('Ignore Sticky Posts', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                //'query_type' => ['get_cpt', 'automatic_mode']
            ]
                ]
        );
        
        $this->add_wp_query_post_args();
        
    }
    
    public function add_wp_query_post_args() {
        $this->add_control(
                'hr_query',
                [
                    'type' => Controls_Manager::DIVIDER,
                    'style' => 'thick',
                ]
        );
        $this->add_control(
                'posts_per_page', [
            'label' => esc_html__('Number of Blocks', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            //'default' => '10',
            'description' => esc_html__('Number of result per Page, leave empty for global configuration or -1 to display all'),
            'condition' => [
                //'query_type' => ['get_cpt', 'get_attachments', 'automatic_mode', 'post_parent'],
            ],
                ]
        );
        $this->add_control(
                'posts_offset', [
            'label' => esc_html__('Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'condition' => [
                //'query_type' => ['get_cpt', 'get_attachments', 'automatic_mode'],
                'posts_per_page!' => '-1'
            ],
                ]
        );
        $this->add_control(
                'posts_limit', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'condition' => [
                //'query_type' => ['get_cpt', 'get_attachments', 'automatic_mode'],
                'posts_per_page!' => '-1',
            ],
                ]
        );
        
        $orderby = Query_Utils::get_post_orderby_options();
        $orderby = apply_filters('e_addons/query/orderby', $orderby);
        
        $this->add_control(
                'orderby', [
            'label' => esc_html__('Order By', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => $orderby,
            'default' => 'date',
            'condition' => [
                //'query_type' => ['get_cpt', 'get_attachments', 'automatic_mode', 'custommeta_source', 'specific_posts'],
            ],
                ]
        );
        $this->add_control(
                'metakey', [
            'label' => esc_html__('Meta Field', 'e-addons'),
            'type' => 'e-query',
            'select2options' => ['tags' => true],
            'placeholder' => esc_html__('Meta key', 'e-addons'),
            'label_block' => true,
            'query_type' => 'metas',
            'object_type' => 'post',
            //'description' => esc_html__('Selected Post Meta value must be stored if format "Ymd", like ACF Date', 'e-addons'),
            'separator' => 'after',
            'condition' => [
                //'query_type' => ['get_cpt', 'automatic_mode'],
                'orderby' => ['meta_value', 'meta_value_date', 'meta_value_num'],
            ]
                ]
        );
        $this->add_control(
                'order', [
            'label' => esc_html__('Order', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'ASC' => 'Ascending',
                'DESC' => 'Descending'
            ],
            'default' => 'DESC',
            'condition' => [
                //'query_type' => ['get_cpt','get_attachments',  'automatic_mode', 'custommeta_source', 'specific_posts'],
                'orderby!' => ['', 'rand', 'post__in'],
            ],
                ]
        );
        
        // --------------------------------- [ Posts Inclusion ]
        $this->add_control(
                'heading_query_include',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eicon-plus-circle-o" aria-hidden="true"></i> &nbsp;<b>' . __('Include', 'e-addons') . '</b>',
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        //'query_type!' => ['specific_posts', 'automatic_mode'],
                        'query_type' => ['get_attachments', 'get_cpt']
                    ]
                ]
        );

        $this->add_control(
                'include_posts', [
            'label' => __($this->get_querytype() == 'attachment' ? 'Select Media' : 'Post In', 'e-addons'),
            'type' => $this->get_querytype() == 'attachment' ? 'file' : 'e-query',
            'query_type' => 'posts',
            'placeholder' => __('Select '.$this->get_querytype(), 'e-addons'),
            'label_block' => true,
            'multiple' => true,
            'condition' => [
                //'query_type!' => ['specific_posts', 'automatic_mode'],
                'query_type' => ['get_attachments', 'get_cpt']
            ],
                ]
        );
        
        // --------------------------------- [ Posts Exclusion ]
        $this->add_control(
                'heading_query_exclude',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-ban" aria-hidden="true"></i> &nbsp;<b>' . esc_html__('Exclude', 'e-addons') . '</b>',
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        //'query_type' => ['get_cpt', 'automatic_mode']
                    ]
                ]
        );
        $this->add_control(
                'exclude_myself', [
            'label' => esc_html__('Exclude Current Post', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            //'default' => 'yes',
            'condition' => [
                //'query_type' => ['get_cpt', 'get_attachments']
            ]
                ]
        );
        /* $this->add_control(
          'exclude_page_parent', [
          'label' => esc_html__('Page parent', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          'condition' => [
          'query_type' => ['get_cpt', 'automatic_mode']
          ]
          ]
          ); */
        $this->add_control(
                'exclude_posts', [
            'label' => esc_html__('Posts Not In', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => esc_html__('Search Title', 'e-addons'),
            'label_block' => true,
            'query_type' => 'posts',
            'multiple' => true,
            'condition' => [
                'include_posts' => '',
                //'query_type' => ['get_cpt', 'get_attachments', 'automatic_mode'],
            ],
                ]
        );
    }

    public function add_automatic_mode_warning() {
        $this->add_control(
                'avviso_automatic_mode',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-exclamation-circle"></i> ' . __('With this option will be used current posts in the global Query. <div class="eadd-automatic-info">Ideal for native Archives pages: <ul><li>Templates in posts, pages or single cpt;</li> <li>Terms archives;</li> <li>Authors archives; </li></ul></div>', 'e-addons'),
                    'content_classes' => 'e-add-info-panel',
                    'condition' => [
                        'query_type' => 'automatic_mode',
                    ],
                ]
        );

        $this->add_control(
                'options_heading', [
            'label' => esc_html__('Options', 'e-addons'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'query_type' => ['get_cpt', 'automatic_mode'],
            ],
                ]
        );
    }

    public function add_controls_content($repeater, $type = 'post') {
        // CONTENT - TAB
        // +********************* Common
        $this->controls_items_common_content($repeater);

        // +********************* Label
        $this->controls_items_label_content($repeater);

        // +********************* Image
        $this->controls_items_image_content($repeater, $type);

        // +********************* Title
        $this->controls_items_title_content($repeater, $type);

        // +********************* Date
        $this->controls_items_date_content($repeater, $type);

        // +********************* Content/Excerpt
        $this->controls_items_contentdescription_content($repeater, $type);

        // +********************* ReadMore
        $this->controls_items_readmore_content($repeater);

        // +********************* Template
        $this->controls_items_template_content($repeater);

        // +********************* CustoFields (ACF, Pods, Toolset, Metabox)
        $this->custommeta_items($repeater, $type);
        
        do_action('e_addons/query/item/content/controls', $this, $repeater);
    }

    public function add_controls_style($repeater) {
        // +********************* Image
        // +********************* Title
        // +********************* Date
        // +********************* Terms of Taxonomy (Category, Tag, CustomTax)
        // +********************* Content/Excerpt
        // +********************* ReadMore
        // +********************* Author user
        // +********************* Post Type
        // +********************* CustoFields (ACF, Pods, Toolset, Metabox)
        // --------------- BASE
        //@p le caratteristiche grafiche base:
        //  - text-align, flex-align, typography, space
        $this->controls_items_base_style($repeater);

        // --------------- AUTHOR BOX
        //@p le carateristiche grafche dell'auhor-box per il widget post
        $this->controls_items_author_style($repeater);

        // -------- COLORS
        //@p le carateristiche grafche del colore testi e background
        $this->controls_items_colors_style($repeater);

        // -------- COLORS-HOVER
        //@p le carateristiche grafche del colore testi e background nello statoo di hover
        $this->controls_items_colorshover_style($repeater);

        // --------------- ICON
        //@p le caratteristiche grafiiche dell'icona
        $this->controls_items_icon_style($repeater);

        // --------------- LABEL BEFORE
        //@p le caratteristiche grafiiche dell'a label
        $this->controls_items_label_style($repeater);

        // ------------ SPACES
        //@p le carateristiche grafche le spaziature Padding e margin
        $this->controls_items_spaces_style($repeater);

        // ------------ BORDERS & SHADOW
        //@p le carateristiche grafche: bordo, raggio-del-bordo, ombra del box
        $this->controls_items_bordersandshadow_style($repeater);
    }

    public function add_specific_posts_repeater() {
        // --------------------------------- [ Specific Posts-Pages ]
        $repeater_specific_posts = new Repeater();

        $repeater_specific_posts->add_control(
                'the_post',
                [
                    'label' => esc_html__('Select Post', 'e-addons'),
                    'type' => 'e-query',
                    'show_label' => false,
                    'placeholder' => esc_html__('Select post', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                //'object_type' => get_post_types(array('public' => true))
                ]
        );
        $this->add_control(
                'repeater_specific_posts', [
            'label' => esc_html__('Specific Posts', 'e-addons'),
            'type' => Controls_Manager::REPEATER,
            'prevent_empty' => false,
            'default' => [
            ],
            'separator' => 'after',
            'fields' => $repeater_specific_posts->get_controls(),
            'title_field' => 'ID: {{{ the_post }}}',
            'condition' => [
                'query_type' => 'specific_posts',
            ],
                ]
        );
    }

    public function query_posts() {
        $this->query_the_elements();
    }

    // La QUERY
    public function query_the_elements() {

        /** @var Module_Query $elementor_query */
        //$elementor_query = Module_Query::instance();
        //$this->query = $elementor_query->get_query( $this, 'posts', $query_args, [] );

        $query_vars = $this->get_query_args();

        $query_vars = apply_filters('e_addons/query_posts/query_vars', $query_vars);

        // DEBUG
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty($this->get_settings_for_display('query_debug'))) {
                echo '<pre>';
                var_dump($query_vars);
                echo '</pre>';
            }
        }

        $query_id = $this->get_settings_for_display('query_id');
        if ($query_id) {
            add_action('pre_get_posts', [$this, 'pre_get_posts_query_filter']);
        }

        // query optimization
        $query_vars['update_post_meta_cache'] = false;
	$query_vars['update_post_term_cache'] = false;
        //$query_vars['lazy_load_term_meta'] = true;
        
        //echo '<pre>'; var_dump($query_vars); echo '</pre>';
        $query = new \WP_Query($query_vars);
        //echo '<pre>'; var_dump($query); echo '</pre>';
        //$query->query($query_vars);

        remove_action('pre_get_posts', [$this, 'pre_get_posts_query_filter']);

        do_action('elementor/query/query_results', $query, $this);

        //echo '<pre>';var_dump($query->query_vars);var_dump(wp_list_pluck($query->posts, 'ID'));echo '</pre>';
        //var_dump(get_class($this));
        $this->query = $query;
    }

    /**
     * @param \WP_Query $query
     */
    public function pre_get_posts_query_filter($query) {
        $query_id = $this->get_settings_for_display('query_id');
        if ($query_id) {
            $widget_name = $this->get_name();
            /**
             * Query Posts widget Query args.
             *
             * It allows developers to alter individual posts widget queries.
             *
             * The dynamic portions of the hook name, `$widget_name` & `$query_id`, refers to the Widget name and Query ID respectively.
             *
             * @since 2.1.0
             *
             * @param \WP_Query     $query
             * @param Widget_Base   $this
             */
            do_action("elementor/query/{$query_id}", $query, $this);
        }
    }

    public function get_query_args() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $args = array();
        /*
          '1 - automatic_mode'
          '2 - get_cpt'
          '3 - post_parent'
          '4 - custommeta_source'
          '5 - specific_posts'
          'x - custom_query'
         */
        $query_type = $settings['query_type'];
        switch ($query_type) {
            case 'automatic_mode':
                global $wp_query;
                //echo '<pre>'; var_dump($wp_query); echo '</pre>';
                if (is_singular()) {
                    $post = get_post();
                    $args['post_type'] = $post->post_type;
                    if ($settings['dynamic_my_siblings']) {
                        $args['child_of'] = $post->post_parent;
                        $args['exclude'] = $post->ID;
                    }
                    if ($settings['dynamic_my_children']) {
                        $args['child_of'] = $post->ID;
                    }
                } else {
                    $args = $wp_query->query;
                }
                break;
            case 'get_cpt':
                if ($settings['post_type']) {
                    $args['post_type'] = $settings['post_type'];
                }
                break;
            case 'post_parent':
                // default args
                $args['post_type'] = 'any';
                $args['orderby'] = 'menu_order';
                $args['order'] = 'asc';
                //
                // single
                //$args['post_parent'] = $settings['specific_page_parent'];
                // multiple
                $post_parent = !empty($settings['specific_page_parent']) ? $settings['specific_page_parent'] : get_the_ID();
                $post_parent__in = Utils::explode($post_parent, null, null, 'intval');
                if (!empty($settings['specific_page_parent_ancestor'])) {
                    // like term child_of
                    foreach ($post_parent__in as $post_id) {
                        $post_parent__in = array_merge($post_parent__in, Utils::get_post_descendants($post_id));
                    }
                }
                //var_dump($post_parent__in); die();
                if (Utils::is_plugin_active('wpml')) {
                    $tmp = [];
                    foreach ($post_parent__in as $post_id) {
                        $tmp[] = apply_filters('wpml_object_id', $post_id, get_post_type($post_id), true);
                    }
                    $post_parent__in = $tmp;
                }
                $args['post_parent__in'] = $post_parent__in;
                break;
            case 'custommeta_source':

                // default args
                $args['posts_per_page'] = -1;
                $args['orderby'] = $settings['orderby'] == 'menu_order' ? 'post__in' : $settings['orderby']; //'post__in';
                $args['post_type'] = 'any';
                if (!empty($settings['custommeta_source_reverse']) && !empty($settings['custommeta_source_key_post'])) {
                    $post_id = $settings['custommeta_source_post'] ? $settings['custommeta_source_post'] : get_the_ID();
                    $args['meta_query'] = array(
                        array(
                            // ACF
                            'key' => $settings['custommeta_source_key_post'],
                            'value' => '"' . $post_id . '"',
                            'compare' => 'LIKE',
                        ),
                        array(
                            // PODS
                            'key' => $settings['custommeta_source_key_post'],
                            'value' => $post_id,
                        ),
                        'relation' => 'OR',
                    );
                } else {
                    $custommeta_source_value = $this->get_custom_meta_source_value($settings);
                    if (!empty($custommeta_source_value)) {
                        if (!empty($custommeta_source_value['ID'])) {
                            $custommeta_source_value = $custommeta_source_value['ID'];
                        }
                        if (!empty($custommeta_source_value[0]['ID'])) {
                            $custommeta_source_value = wp_list_pluck($custommeta_source_value, 'ID');
                        }
                        $args['post__in'] = Utils::explode($custommeta_source_value, ',', 0, 'intval');
                    } else {
                        $args['post__in'] = array(-1);
                    }
                }
                break;
            case 'specific_posts':
                if (!empty($settings['repeater_specific_posts'])) {
                    $items_specific_posts = array();
                    foreach ($settings['repeater_specific_posts'] as $item_sp) {
                        if (!empty($item_sp['the_post'])) {
                            $item_sp_posts = Utils::explode($item_sp['the_post']);
                            foreach ($item_sp_posts as $aitem) {
                                array_push($items_specific_posts, $aitem);
                                //$items_specific_posts = array_merge($items_specific_posts, ));
                            }
                        }
                    }
                    if (count($items_specific_posts)) {
                        $posts_per_page = -1;
                        if (!empty($settings['posts_per_page'])) {
                            $posts_per_page = $settings['posts_per_page'];
                        }
                        if (!empty($settings['pagination_enable'])) {
                            $posts_per_page = empty($settings['posts_per_page']) ? get_option('posts_per_page') : $settings['posts_per_page'];
                        }
                        $args['posts_per_page'] = $posts_per_page;
                        $args['orderby'] = 'post__in';
                        $args['post_type'] = 'any';
                        $args['post__in'] = $items_specific_posts;
                    }
                    //var_dump($items_specific_posts);
                } else {
                    $args['post__in'] = array(-1);
                }
                break;
            case 'custom_query':

                break;
        }
        
        if (!empty($args['post__in'])) {
            if (Utils::is_plugin_active('wpml')) {
                $tmp = [];
                $include = Utils::explode($args['post__in']);
                foreach ($include as $post_id) {
                    $tmp[] = apply_filters('wpml_object_id', $post_id, get_post_type($post_id), true);
                }
                $args['post__in'] = $tmp;
            }
            if (empty($args['post__in'])) {
                $args['post_parent'] = 1; // return zero results
                $args['posts_per_page'] = get_option('posts_per_page'); // prevent the list of ALL terms
            }   
        }
        
        $args = $this->set_wp_query_args($args, $settings);
        $args = $this->set_wp_query_filters($args, $settings);
        //var_dump($args);
        return $args;
    }

    public function set_wp_query_args($args, $settings) {
        /*
          'post_type'
          --'post_status'
          --'ignore_sticky_posts'
          --'posts_per_page'
          --'posts_offset'
          --'orderby'
          --'metakey' ...
          --'order'
          --'exclude_myself'
          --'exclude_posts'
         */

        // Status
        if (!empty($settings['post_status'])) {
            $args['post_status'] = $settings['post_status'];
            if (in_array('any', $args['post_status'])) {
                $args['post_status'] = 'any';
            }
        }
        // limit posts per page
        if (!empty($settings['posts_per_page'])) {
            $args['posts_per_page'] = $settings['posts_per_page'];
        }
        // offset
        if (!empty($settings['posts_offset'])) {
            $args['offset'] = $settings['posts_offset'];
        }

        if (!empty($settings['posts_limit'])) {
            unset($args['offset']);
        }

        // paginazione
        if ((!empty($settings['pagination_enable']) ) || !empty($settings['infiniteScroll_enable'])) {
            $args['paged'] = $this->get_current_page();
        }

        // order by
        if (!empty($settings['orderby'])) {
            $args['orderby'] = $settings['orderby'];
        }
        //meta key order
        if (!empty($settings['metakey'])) {
            $args['meta_key'] = $settings['metakey'];
        }
        // order asc-desc
        if (!empty($settings['order'])) {
            $args['order'] = $settings['order'];
        }
        
        // exclusion posts
        if (!empty($settings['include_posts'])) {
            $args['post__in'] = Utils::explode($settings['include_posts']);
        }

        // exclusion posts
        $excludedPosts = array();
        if (!empty($settings['exclude_posts'])) {
            $excludedPosts = Utils::explode($settings['exclude_posts']);
        }
        if (!empty($settings['exclude_myself'])) {
            array_push($excludedPosts, get_the_ID());
        }

        if (!empty($excludedPosts)) {
            $args['post__not_in'] = $excludedPosts;
        }

        // ignore_sticky_posts

        if (!empty($settings['ignore_sticky_posts'])) {
            $args['ignore_sticky_posts'] = true;
            //$args['post__in'] = get_option('sticky_posts');
        }

        return $args;
    }

    public function set_wp_query_filters($args, $settings) {
        /*
          'query_filter'
          'date'
          'term'
          'author'
          'metakey'
          'comments'
         */
        if (!empty($settings['query_filter'])) {
            $query_filters = $settings['query_filter'];
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
                    case 'comments':

                        break;
                }
            }
        }
        return $args;
    }

    protected function get_search_filter($settings) {
        /*
          'search_field_value'
         */
        $search_args = array();
        if (!empty($settings['search_field_value'])) {
            $search_args['s'] = $settings['search_field_value'];
        }
        /*
          $args = array(
          'search'         => 'Rami',
          'search_columns' => array( 'user_login', 'user_email' )
          );
         */
        return $search_args;
    }

    protected function get_author_filter($settings) {
        /*
          -------- AUTHORS -------
          'author_from'
          'post_author'
          'include_author'
          'exclude_author'
          'custom_meta'
          'author_field_meta'
          'current_autor'

         */
        $author_args = array();
        switch ($settings['author_from']) {
            case 'post_author':
                if (!empty($settings['include_author'])) {
                    $author_args['author__in'] = Utils::explode($settings['include_author']);
                }
                break;
            case 'custom_meta':
                if (!empty($settings['author_field_meta'])) {
                    $author_args['author__in'] = Utils::explode($settings['author_field_meta']);
                }
                break;
            case 'current_autor':
                $author_id = get_the_author_meta('ID');
                $author_args['author'] = $author_id;
                break;
            case 'author_role':
                if (!empty($settings['author_role'])) {
                    $author_ids = get_users(array('role__in' => $settings['author_role'], 'fields' => 'ID'));
                    $author_args['author'] = implode(',', $author_ids);
                }
                break;
        }
        //
        if (!empty($settings['exclude_author'])) {
            $author_args['author__not_in'] = Utils::explode($settings['exclude_author']);
        }
        return $author_args;
    }

    protected function get_terms_filter($settings) {
        /*
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
         */
        $terms_args = array();
        $post = get_post();
        $post_types = empty($settings['post_type']) ? [$this->get_querytype()] : Utils::explode($settings['post_type']);
        $taxonomies_from_type = [];
        foreach ($post_types as $post_type) {
            $taxonomies_from_type = array_merge($taxonomies_from_type, get_object_taxonomies($post_type));
        }
        //var_dump($taxonomies_from_type);
        
        $terms_included = array();
        $terms_excluded = array();
        $taxquery = array();
        $taxquery_inc = array();
        $taxquery_exc = array();

        switch ($settings['term_from']) {
            case 'post_term':
                if (!empty($settings['include_term'])) {
                    $terms_included = $settings['include_term'];
                }
                break;
            case 'custom_meta':
                if (!empty($settings['term_field_meta'])) {
                    $terms_included = get_post_meta(get_the_ID(), $settings['term_field_meta'], true);
                }
                break;
            case 'current_term':
                //$settings['include_term_combination'] = 'OR';
                if (is_tax() || is_category() || is_tag()) {
                    // taxonomy archive page
                    $term = get_queried_object();
                    if ($term && is_object($term) && get_class($term) == 'WP_Term') {
                        array_push($taxquery_inc, array(
                            'taxonomy' => $term->taxonomy,
                            'terms' => $term->term_id,
                        ));
                    }
                } else {
                    // singular post
                    if (empty($settings['current_term_taxonomy'])) {
                        $taxonomies_from_post = get_object_taxonomies($post);
                    } else {
                        $taxonomies_from_post = $settings['current_term_taxonomy'];
                    }
                    foreach ($taxonomies_from_post as $tax) {
                        $currentpost_terms = get_the_terms(get_the_ID(), $tax);
                        if (!empty($currentpost_terms)) {
                            $terms_included = array();
                            foreach ($currentpost_terms as $term) {
                                array_push($terms_included, $term->term_id);
                            }
                            $terms_included = Utils::explode($terms_included);
                            array_push($taxquery_inc, array(
                                'taxonomy' => $tax,
                                //'field' => 'term_id',
                                'terms' => $terms_included,
                            ));
                        }
                    }
                }
                break;
        }
        //var_dump($taxquery_inc);
        // l'esclusione vale in ogni caso, permette di mmodellare la query in caso di termini multipli
        if (!empty($settings['exclude_term'])) {
            $terms_excluded = Utils::explode($settings['exclude_term']);
        }
        $terms_included = Utils::explode($terms_included);
        //risolvo bug: quando il dato è una stringa o numero e non Array, quindi converto.
        //var_dump($taxonomies_from_type);
        if (Utils::is_plugin_active('wpml')) {
            if (!empty($terms_included)) {
                foreach ($terms_included as $ti_key => $ti_id) {
                    $ti_term = get_term($ti_id);
                    if ($ti_term) {
                        $terms_included[$ti_key] = apply_filters('wpml_object_id', $ti_id, $ti_term->taxonomy, true);
                    }
                }
            }
            if (!empty($terms_excluded)) {
                foreach ($terms_excluded as $te_key => $te_id) {
                    $te_term = get_term($te_id);
                    if ($te_term) {
                        $terms_excluded[$te_key] = apply_filters('wpml_object_id', $te_id, $te_term->taxonomy, true);
                    }
                }
            }
        }

        if (!empty($terms_included) && empty($taxquery_inc) && $settings['term_from'] != 'current_term') {
            $incl_terms = get_terms(array(
                'include' => $terms_included,
                'hide_empty' => false,
            ));
            if (!empty($taxonomies_from_type) && !empty($incl_terms)) {
                foreach ($taxonomies_from_type as $tax) {
                    foreach ($incl_terms as $term) {
                        if ($term->taxonomy == $tax) {
                            array_push($taxquery_inc, array(
                                'taxonomy' => $tax,
                                //'field' => 'term_id',
                                'terms' => $term->term_id,
                                'include_children' => (bool) $settings['include_term_children'],
                            ));
                        }
                    }
                }
            }
        }

        if (!empty($terms_excluded)) {
            $excl_terms = get_terms(array(
                'include' => $terms_excluded,
                'hide_empty' => false,
            ));
            if (!empty($taxonomies_from_type) && !empty($excl_terms)) {
                foreach ($taxonomies_from_type as $tax) {
                    foreach ($excl_terms as $term) {
                        if ($term->taxonomy == $tax) {
                            array_push($taxquery_exc, array(
                                'taxonomy' => $tax,
                                //'field' => 'term_id',
                                'terms' => $term->term_id,
                                'operator' => 'NOT IN'
                            ));
                        }
                    }
                }
            }
        }

        if (!empty($taxquery_inc) && !empty($settings['include_term_combination'])) {
            $taxquery_inc['relation'] = $settings['include_term_combination'];
            $taxquery[] = $taxquery_inc;
        }

        if (!empty($taxquery_exc) && !empty($settings['exclude_term_combination'])) {
            $taxquery_exc['relation'] = $settings['exclude_term_combination'];
            $taxquery[] = $taxquery_exc;
        }

        if (!empty($taxquery)) {
            $taxquery['relation'] = 'AND';
            $terms_args['tax_query'] = $taxquery;
        }
        //var_dump($taxquery);
        //
        return $terms_args;
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
            
            //$queried_object = get_queried_object();
            //$queried_object_id = get_queried_object_id();
            $current_post = get_post();

            $i = 0;
            $j = 0;
            $offset = $skin->parent->get_settings_for_display('posts_offset');
            $limit = $skin->parent->get_settings_for_display('posts_limit');
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
                    //$wp_query->queried_object = $wp_query->post = $skin->current_data;
                    //$wp_query->queried_object_id = $skin->current_id;
                    //
                    $skin->render_element_item();
                }
            }
        }

        wp_reset_postdata();
        if ($current_post) {
            $post = $current_post;
            if (isset($wp_query)) {
                /*if (wp_doing_ajax()) {
                    $wp_query->post = $current_post;
                    $wp_query->queried_object = $queried_object;
                    $wp_query->queried_object_id = $queried_object_id;
                }
                $wp_query->setup_postdata($post);*/
            }
        }
    }

    public function should_render($render, $skin, $query) {
        if (!$query->found_posts && empty($query->query['urls'])) {
            $render = false;
        }
        return $render;
    }

    public function pagination__page_limit($page_limit, $skin, $query, $settings) {
        $page_limit = $query->max_num_pages;
        return $page_limit;
    }

    public function pagination__page_length($page_length, $skin, $query, $settings) {
        return $query->post_count;
    }

}
