<?php

namespace EAddonsForElementor\Modules\Query\Base;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;
use EAddonsForElementor\Core\Controls\Groups\Transform;
use EAddonsForElementor\Core\Controls\Groups\Masking;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Query
 *
 * Elementor widget for E-Addons
 *
 */
class Query extends Base_Widget {

    use Traits\Common;
    use Traits\Pagination;
    use Traits\Infinite_Scroll;
    use Traits\Custommeta;

//use Traits\Label;
    use Traits\Hover;
    use Traits\Reveal;
    use Traits\Items_Content;
    use Traits\Items_Style;
    use Traits\Items_Advanced;
    use Traits\Filters;

    //@ questa è una variabile globale che memorizza la query in corso
    protected $query = null;
    //@ questa è una variabile globale che memorizza se la query è: 1-post, 2-user, 3-term, 4-repeater_list 5-items
    protected $querytype = null;
    //@ questo serve a rimuovere lo skin default perché non voglio fare nessun render direttamente nel widget
    protected $_has_template_content = false;
    //@ active skin
    public $skin = null;
    public $skins = [];
    public $items = [];
    public $list_items = [];
    public $list_items_default = [];
    public $is_first_section = true;
    public static $filters = [];

    public function __construct($data = [], $args = null) {
        $this->add_query_actions();
        parent::__construct($data, $args);

        $filters = [
            'register_controls_hovereffects' => 'elementor/element/' . $this->get_name() . '/section_items/before_section_start',
            'register_reveal_controls' => 'elementor/element/' . $this->get_name() . '/section_items/before_section_start',
            'register_controls_layout' => 'elementor/element/' . $this->get_name() . '/section_items/after_section_end',
        ];
        foreach ($filters as $fnc => $filter) {
            if (!has_action($filter, [$this, $fnc]) && (empty(self::$filters[$filter]) || !in_array($fnc, self::$filters[$filter]))) {
                add_action($filter, [$this, $fnc]);
                self::$filters[$filter][] = $fnc;
            }
        }
        //var_dump(get_class($this));
    }

    public function add_query_actions() {
        if (!has_action('e_addons/query/' . $this->get_querytype())) { // TODO: check why istantiate multiple times
            add_action('e_addons/query/' . $this->get_querytype(), [$this, 'loop'], 10, 2);
        }
        if (!has_filter('e_addons/query/should_render/' . $this->get_querytype())) { // TODO: check why istantiate multiple times
            add_filter('e_addons/query/should_render/' . $this->get_querytype(), [$this, 'should_render'], 10, 3);
        }
        if (!has_filter('e_addons/query/page_limit/' . $this->get_querytype())) { // TODO: check why istantiate multiple times
            add_filter('e_addons/query/page_limit/' . $this->get_querytype(), [$this, 'pagination__page_limit'], 10, 4);
        }
        if (!has_filter('e_addons/query/per_page/' . $this->get_querytype())) { // TODO: check why istantiate multiple times
            add_filter('e_addons/query/per_page/' . $this->get_querytype(), [$this, 'pagination__per_page'], 10, 4);
        }
        if (!has_filter('e_addons/query/page_length/' . $this->get_querytype())) { // TODO: check why istantiate multiple times
            add_filter('e_addons/query/page_length/' . $this->get_querytype(), [$this, 'pagination__page_length'], 10, 4);
        }
    }

    public function get_name() {
        return 'e-query-base';
    }

    public function get_categories() {
        return ['query'];
    }

    public function get_script_depends() {
        return [
            'jquery-fitvids',
            'infiniteScroll',
            'e-addons-frontend-query',
            'e-addons-query-base',
        ];
    }

    //
    public function get_style_depends() {
        return [
            /* 'font-awesome-5-all', 'font-awesome', */ 'elementor-icons-fa-solid', 'animatecss',
        ];
    }

    function get_query_skins() {
        return $this->skins;
    }

    protected function register_skins() {
        $disabled = get_option('e_addons_disabled', array());
        foreach ($this->get_query_skins() as $skin) {
            if (class_exists($skin)) {
                //var_dump($skin); var_dump(get_class($this));
                //var_dump($disabled);
                $tmp = explode('\\', $skin);
                $name = end($tmp);
                $name = strtolower($name);
                $name = str_replace('_', '-', $name);
                //var_dump($name);
                $is_disabled = false;
                if (!empty($disabled['skins'])) {
                    foreach ($disabled['skins'] as $module) {
                        if (!$is_disabled) {
                            $is_disabled = in_array($name, $module);
                        }
                    }
                }
                if (!$is_disabled) {
                    $this->add_skin(new $skin($this));
                }
            }
        }
        //$skins_manager = \Elementor\Plugin::$instance->skins_manager;
        //var_dump($skins_manager->get_skins($this)); die();
    }

    //@ questo metodo restituisce la query in corso
    public function get_query() {
        return $this->query;
    }

    //@ questo metodo restituisce il tipo di query in corso
    public function get_querytype() {
        return $this->querytype;
    }

    /*     * **************************** PHP 8.1 FIX ****************************** */

    /**
     * Start widget controls section.
     *
     * Used to add a new section of controls to the widget. Regular controls and
     * skin controls.
     *
     * Note that when you add new controls to widgets they must be wrapped by
     * `start_controls_section()` and `end_controls_section()`.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $section_id Section ID.
     * @param array  $args       Section arguments Optional.
     */
    public function start_controls_section($section_id, array $args = []) {
        //parent::start_controls_section($section_id, $args);
        \Elementor\Controls_Stack::start_controls_section($section_id, $args);
        //var_dump($this->is_first_section);
        if ($this->is_first_section) {
            $this->register_skin_control();
            $this->is_first_section = false;
        }
    }

    /**
     * Register the Skin Control if the widget has skins.
     *
     * An internal method that is used to add a skin control to the widget.
     * Added at the top of the controls section.
     *
     * @since 2.0.0
     * @access private
     */
    public function register_skin_control() {
        //var_dump($this->get_controls('_skin'));
        if (empty($this->get_controls('_skin'))) {
            $skins = $this->get_skins();
            if (!empty($skins)) {
                $skin_options = [];

                if ($this->_has_template_content) {
                    $skin_options[''] = esc_html__('Default', 'elementor');
                }

                foreach ($skins as $skin_id => $skin) {
                    $skin_options[$skin_id] = $skin->get_title();
                }

                // Get the first item for default value
                $default_value = array_keys($skin_options);
                $default_value = array_shift($default_value);

                if (1 >= count($skin_options)) {
                    $this->add_control(
                            '_skin',
                            [
                                'label' => esc_html__('Skin', 'elementor'),
                                'type' => Controls_Manager::HIDDEN,
                                'default' => $default_value,
                            ]
                    );
                } else {
                    $this->add_control(
                            '_skin',
                            [
                                'label' => esc_html__('Skin', 'elementor'),
                                'type' => Controls_Manager::SELECT,
                                'default' => $default_value,
                                'options' => $skin_options,
                            ]
                    );
                }
            }
        }
    }

    /*     * *********************************************************************** */

    protected function register_controls() {

        $this->start_controls_section(
                'section_e_query', [
            'label' => '<i class="eaddicon ' . $this->get_icon() . '"></i><i class="eadd-logo-e-addons eadd-ic-right"></i> ' . $this->get_title(),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        // skin: Template
        $this->add_control(
                'skin_dis_customtemplate',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-circle"></i><i class="eaddicon-skin eicon-elementor-square"></i>',
                    //'raw' => '<img src="'.E_ADDONS_QUERY_URL . 'assets/img/skins/template.png'.'" />',
                    'content_classes' => 'e-add-skin-dis e-add-ect-dis',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider'],
                        'style_items' => 'template',
                    ],
                ]
        );

        do_action('e_addons/query/skin_icon', $this);

        // skin: pagination classic
        $this->add_control(
                'skin_dis_pagination',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-numeric-pagination"></i>',
                    //'raw' => '<img src="'.E_ADDONS_QUERY_URL . 'assets/img/skins/pagination.png'.'" />',
                    'content_classes' => 'e-add-skin-dis e-add-pagination-dis',
                    'condition' => [
                        //@p il massimo è che la paginazione funzioni con tutti gli skins...
                        //'_skin' => ['', 'grid', 'filters', 'timeline'],
                        'pagination_enable' => 'yes',
                        'infiniteScroll_enable' => ''
                    ],
                ]
        );
        // skin: infinitescroll
        $this->add_control(
                'skin_dis_infinitescroll',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon-skin eadd-infinite-pagination"></i>',
                    //'raw' => '<img src="'.E_ADDONS_QUERY_URL . 'assets/img/skins/infinitescroll.png'.'" />',
                    'content_classes' => 'e-add-skin-dis e-add-pagination-dis',
                    'condition' => [
                        '_skin' => ['', 'grid', 'list', 'table', 'filters', 'timeline'],
                        'pagination_enable' => 'yes',
                        'infiniteScroll_enable' => 'yes'
                    ],
                ]
        );

        //@p qui infilo i controllo relativamente agli items..
        $this->items_query_controls();

        //@p questo metodo produce i 2 switcher per abilitare la paginazione in caso di items_list è vuoto)
        $this->paginations_enable_controls();

        $this->end_controls_section();

        // ------------------------------------------------------------------ [SECTION LAYOUTS BLOCKS ]
        $this->start_controls_section(
                'section_layout_blocks', [
            'label' => '<i class="eaddicon eicon-info-box" aria-hidden="true"></i> ' . esc_html__('Block Layout', 'e-addons'),
            'condition' => [
                '_skin!' => ['justifiedgrid', 'accordion', 'timeline', 'nextpost', 'table', 'list', 'piling', 'mosaic', 'rapidimages', 'export'],
            ],
                ]
        );

        $item_types = [];
        $item_types = apply_filters('e_addons/query/item_types', $item_types);
        if (is_subclass_of($this, 'EAddonsForElementor\Modules\Query\Widgets\Query_Posts')) {
            $item_types = apply_filters('e_addons/query/post/item_types', $item_types);
        }
        $item_types = apply_filters('e_addons/query/' . $this->get_querytype() . '/item_types', $item_types);

        $this->items = $item_types;

        $condition = [];
        $default = 'template';
        $columns_grid = 2;
        $style_items = [
            'html' => [
                'title' => esc_html__('Custom HTML', 'e-addons'),
                'return_val' => 'val',
                'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/html.png',
            ],
            'template' => [
                'title' => esc_html__('Elementor Template', 'e-addons'),
                'return_val' => 'val',
                'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/template.png',
            ],
        ];
        if (!empty($item_types)) {
            $style_items = [
                'default' => [
                    'title' => esc_html__('Default', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/top.png',
                ],
                'left' => [
                    'title' => esc_html__('Left', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/left.png',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/right.png',
                ],
                'alternate' => [
                    'title' => esc_html__('Alternate', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/alternate.png',
                ],
                'textzone' => [
                    'title' => esc_html__('Text Zone', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/textzone.png',
                ],
                'overlay' => [
                    'title' => esc_html__('Overlay', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/overlay.png',
                ],
                'float' => [
                    'title' => esc_html__('Float', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/float.png',
                ],
                    ] + $style_items;
            $columns_grid = 3;
            $default = 'default';
        }

        // ------------------------------------
        $this->add_control(
                'style_items', [
            'label' => esc_html__('Block Style', 'e-addons'),
            'type' => 'ui_selector',
            'label_block' => true,
            'toggle' => false,
            'type_selector' => 'image',
            'columns_grid' => $columns_grid,
            'separator' => 'before',
            'options' => $style_items,
            'toggle' => false,
            'render_type' => 'template',
            'prefix_class' => 'e-add-posts-layout-', // ....da cambiare ......
            'default' => $default,
            //'tablet_default' => '',
            //'mobile_default' => '',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
            ],
                ]
        );

        if (!empty($item_types)) {
            // +********************* Style: Items
            $this->add_controls_block_style_items();
        }

        // +********************* Style: Custom HTML
        $this->add_control(
                'block_custom_html',
                [
                    'label' => esc_html__('Custom HTML', 'elementor'),
                    'type' => Controls_Manager::CODE,
                    'condition' => [
                        'style_items' => 'html',
                    ],
                    'description' => esc_html__('Write here full Block HTML, you can insert Shortcodes or Twig (use "block" var)', 'e-addons'),
                ]
        );

        // +********************* Style: Elementor TEMPLATE
        $this->add_control(
                'template_id',
                [
                    'label' => esc_html__('Template', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Template', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'render_type' => 'template',
                    'object_type' => 'elementor_library',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                        'style_items' => 'template',
                    //'native_templatemode_enable' => ''
                    ],
                ]
        );
        $this->add_control(
                'templatemode_enable_2', [
            'label' => esc_html__('Template ODD', 'e-addons'),
            'description' => esc_html__('Enable a template to manage the appearance of the odd elements.', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'render_type' => 'template',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => 'template',
            //'native_templatemode_enable' => '',
            ],
                ]
        );

        $this->add_control(
                'template_2_id',
                [
                    'label' => esc_html__('Template odd', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Select Template', 'e-addons'),
                    'label_block' => true,
                    'show_label' => false,
                    'query_type' => 'posts',
                    'object_type' => 'elementor_library',
                    'render_type' => 'template',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                        'style_items' => 'template',
                        'templatemode_enable_2!' => '',
                    //'native_templatemode_enable' => ''
                    ],
                ]
        );

        /*
          $this->add_control(
          'templatemode_linkable', [
          'label' => esc_html__('Linkable', 'e-addons'),
          'description' => esc_html__('Extended link on the full block.', 'e-addons'),
          'type' => Controls_Manager::SWITCHER,
          //'options' => $options,
          'separator' => 'before',
          'render_type' => 'template',
          //'condition' => [
          //    '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
          //    'style_items' => 'template',
          //],
          ]
          );
         */

        $this->add_control(
                'templatemode_linkable', [
            'label' => esc_html__('Block Link to', 'e-addons'),
            'description' => esc_html__('Link on the whole block', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'separator' => 'before',
            'options' => [
                '' => esc_html__('None', 'e-addons'),
                'yes' => strtoupper($this->get_querytype()) . ' URL',
                'custom' => esc_html__('Custom URL', 'e-addons'),
            ],
            'condition' => [
                'style_items' => ['default', 'template'],
                '_skin' => ['', 'grid'],
            ],
                ]
        );
        $this->add_control(
                'templatemode_linkable_link', [
            'label' => esc_html__('Link', 'e-addons'),
            'type' => Controls_Manager::URL,
            'placeholder' => esc_html__('http://your-link.com', 'e-addons'),
            'condition' => [
                'templatemode_linkable' => 'custom',
            ],
            'show_label' => false,
                ]
        );

        $this->end_controls_section();

        $this->add_pagination_section();

        $this->add_infinite_scroll_section();
    }
    
    
    public function set_default_item_type($default = false, $options = []) {
        if (!empty($options)) {
            $items = array_keys($options);
            $default = in_array($default, $items) ? $default : reset($items);
        }
        return $default;
    }

    public function add_section_items() {
        
        $item_types = [];
        $item_types = apply_filters('e_addons/query/item_types', $item_types);
        if (is_subclass_of($this, 'EAddonsForElementor\Modules\Query\Widgets\Query_Posts')) {
            $item_types = apply_filters('e_addons/query/post/item_types', $item_types);
        }
        $item_types = apply_filters('e_addons/query/' . $this->get_querytype() . '/item_types', $item_types);

        // limit available items
        if (!empty($this->list_items)) {
            foreach ($item_types as $ikey => $ivalue) {
                if (!in_array($ikey, $this->list_items)) {
                    unset($item_types[$ikey]);
                }
            }
        }
        
        // set dafault items
        $list_items_default = [];
        if (!empty($this->list_items_default)) {
            foreach ($this->list_items_default as $item) {
                $list_items_default[] = [
                    'item_type' => $item,
                ];
            }
        }
        //var_dump($item_types);
        
        $condition = [
            '_skin' => '', // this section will be hidden if no items type available
        ];
        if (!empty($item_types)) {
            $condition = [
                '_skin!' => ['nextpost', 'mosaic'],
                'style_items!' => ['template', 'html'],
            ];
        }

        // ------------------------------------------------------------------ [SECTION ITEMS]
        $this->start_controls_section(
                'section_items', [
            'label' => '<i class="eaddicon eicon-radio" aria-hidden="true"></i> ' . esc_html__('Block Items', 'e-addons'),
            'condition' => $condition,
                ]
        );

        if (!empty($item_types)) {
            
            $repeater = new Repeater();
            $repeater->add_control(
                    'item_type', [
                'label' => esc_html__('Item Type', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => $item_types,
                'default' => $this->set_default_item_type('item_title', $item_types),
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
                        'default' => $list_items_default,
                        //item_type.replace("item_", "")
                        'prevent_empty' => $this->get_querytype() != 'attachment',
                        'title_field' => '<# var etichetta = item_type; etichetta = etichetta.replace("item_", ""); #><b class="e-add-item-name"><i class="fa {{{ item_type+"-ic" }}}" aria-hidden="true"></i> {{{item_text_label}}} | {{{ etichetta }}}</b>',
                    ]
            );

            $this->controls_items_grid_debug($this);
        }

        $this->end_controls_section();
    }

    public function register_controls_layout() {
        //$this->parent = $widget;
        // BLOCKS generic style
        $this->register_style_controls();
        // PAGINATION style
        $this->register_style_pagination_controls();
        //INFINITE SCROLL style
        $this->register_style_infinitescroll_controls();
    }

    public function add_no_result_section() {
        //@p il TAB Query
        // ------------------------------------------------------------------ [SECTION - QUERY no_result]
        $this->start_controls_section(
                'section_query_no_result', [
            'label' => '<i class="eaddicon eicon-warning" aria-hidden="true"></i> ' . esc_html__('No results', 'e-addons'),
            'tab' => 'e_query',
                ]
        );
        $this->add_control(
                'query_no_result', [
            'label' => esc_html__('Print a Fallback Content', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'selectors' => [
                '{{WRAPPER}}:after' => 'display: none;',
            ]
                ]
        );
        $this->add_control(
                'query_no_result_txt', [
            'label' => esc_html__('No results Text', 'e-addons'),
            'default' => esc_html__('Sorry, no results found', 'e-addons'),
            'type' => Controls_Manager::WYSIWYG,
            'condition' => [
                'query_no_result!' => '',
            ]
                ]
        );
        $this->end_controls_section();
    }

    // -------------- Render method ---------
    public function render() {
        //@per riconoscere dal _wrappe/scope il tipo e lo skin
        $querytype = $this->get_querytype();
        $queryskin = $this->get_settings_for_display('_skin');
        $this->add_render_attribute('_wrapper', 'class', ['e-add-querytype-' . $querytype, 'e-add-queryskin-' . $queryskin]);

        /*
          $is_imagemask = $this->get_settings('imagemask_popover');
          if ($is_imagemask) {
          $mask_shape_type = $this->get_settings('mask_shape_type');
          //$this->render_svg_mask($mask_shape_type);
          }
         */

        \Elementor\Icons_Manager::enqueue_shim();
    }

    // -------------- Loop method ---------
    public function loop($skin, $query) {
        
    }

    public function should_render($render, $skin, $query) {
        return $render;
    }

    public function pagination__page_limit($page_limit, $skin, $query, $settings) {
        return $page_limit;
    }

    public function pagination__per_page($per_page, $skin, $query, $settings) {
        $querytype = $this->get_querytype() == 'attachment' ? 'post' : $this->get_querytype();
        $per_page = empty($settings[$querytype . 's_per_page']) ? $per_page : intval($settings[$querytype . 's_per_page']);
        return $per_page;
    }

    public function pagination__page_length($page_length, $skin, $query, $settings) {
        return $page_length;
    }

    // -------------- Override Lightbox (assurdo ... ma inevitabile .. da valutare) ---------
    public function add_lightbox_data_attributes($element, $id = null, $lightbox_setting_key = null, $group_id = null, $overwrite = false) {
        $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();

        $is_global_image_lightbox_enabled = 'yes' === $kit->get_settings('global_image_lightbox');

        if ('no' === $lightbox_setting_key) {
            if ($is_global_image_lightbox_enabled) {
                $this->add_render_attribute($element, 'data-elementor-open-lightbox', 'no', true); //<-- @p !!savebbe da aggiungerre questo true per evitare il mio override.
            }

            return $this;
        }

        if ('yes' !== $lightbox_setting_key && !$is_global_image_lightbox_enabled) {
            return $this;
        }

        $attributes['data-elementor-open-lightbox'] = 'yes';

        if ($group_id) {
            $attributes['data-elementor-lightbox-slideshow'] = $group_id;
        }

        if ($id) {
            $lightbox_image_attributes = \Elementor\Plugin::$instance->images_manager->get_lightbox_image_attributes($id);

            if (!empty($lightbox_image_attributes['title'])) {
                $attributes['data-elementor-lightbox-title'] = $lightbox_image_attributes['title'];
            }

            if (!empty($lightbox_image_attributes['description'])) {
                $attributes['data-elementor-lightbox-description'] = $lightbox_image_attributes['description'];
            }
        }

        $this->add_render_attribute($element, $attributes, null, $overwrite);

        return $this;
    }

    // -------------- Methods ---------
    // @p questo metodo viene usato da items_list per igniettare gli elementi ripetitore
    public function items_query_controls() {
        
    }

    // @p questo metodo viene usato da repeater per igniettare gli elementi ripetitore
    public function repeater_query_controls() {
        
    }

    // il metodo (che viene ereditato) e che esegue le query su: POSTS - USERS - TERMS
    public function query_the_elements() {
        
    }

    public function get_item_class() {
        return '';
    }

    public function get_wrapper_class() {
        return '';
    }

    public function get_container_class() {
        return '';
    }

    protected function add_controls_metaquery() {

        $querytype = $this->get_querytype();

        // ****************** Meta key
        $this->add_control(
                'heading_query_filter_metakey',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fa fa-key" aria-hidden="true"></i> ' . esc_html__(' Metakey Filters', 'e-addons'),
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
                    'label' => $querytype . esc_html__(' Field') . ' <b>' . esc_html__('custom meta key', 'e-addons') . '</b>',
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Name', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => $querytype,
                    'description' => esc_html__('Selected ' . $querytype . ' Meta value.', 'e-addons'),
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
            'label' => esc_html__('Post Field Value', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'description' => esc_html__('The specific value of the Post Field', 'elementor'),
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
                    'label' => '<b>' . esc_html__('Metakey') . '</b> ' . esc_html__('Combination', 'e-addons'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'OR' => [
                            'title' => esc_html__('OR', 'e-addons'),
                            'icon' => 'eicon-circle-o',
                        ],
                        'AND' => [
                            'title' => esc_html__('AND', 'e-addons'),
                            'icon' => 'eicon-circle',
                        ],
                        'XPR' => [
                            'title' => esc_html__('Expression', 'e-addons'),
                            'icon' => 'eicon-edit',
                        ],
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
        $this->add_control(
                'metakey_combination_xpr', [
            'label' => esc_html__('Combination Expression', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => '( 1 AND 2 ) OR ( ( 3 OR 4 ) AND ( 5 AND 6 ) )',
            'description' => __('The custom expression of meta field combination.', 'elementor'),
            'label_block' => true,
            'condition' => [
                'metakey_combination' => ['XPR']
            ]
                ]
        );
        $this->add_control(
                'metakey_combination_xpr_rule', [
            'type' => Controls_Manager::RAW_HTML,
            'content_classes' => 'elementor-control-field-description',
            'raw' => 'Use the number of the previous Repeater Meta filters Row (starting by 1). // Use only round brackets // Separate every character by a space // Max 2 conditions per level are accepted, for example "1 AND 2 OR 3" is not valid, write "( 1 AND 2 ) OR 3" instead',
            'condition' => [
                'metakey_combination' => ['XPR']
            ]
                ]
        );
    }

    protected function get_metakey_filter($settings) {
        /*
          -------- META KEY -------
          'metakey_list' [REPEATER]
          'metakey_field_meta'
          'metakey_field_meta_compare'
          'metakey_field_meta_type'
          'metakey_field_meta_value'
          //'metakey_field_meta_value_num'

          'metakey_combination'
         */
        $metakey_args = array();
        $keysquery = array();

        $metakey_list = $settings['metakey_list'];
        foreach ($metakey_list as $item) {
            $_id = $item['_id'];
            if (!empty($item['metakey_field_meta'])) {
                $metakey_field_meta = $item['metakey_field_meta'];
                $metakey_field_meta_type = $item['metakey_field_meta_type'];
                $metakey_field_meta_compare = $item['metakey_field_meta_compare'];
                $metakey_field_meta_value = $item['metakey_field_meta_value'];
                //$metakey_field_meta_value_num = $item['metakey_field_meta_value_num'];
                if (in_array($metakey_field_meta_compare, array('IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'))) {
                    $metakey_field_meta_value = Utils::explode($metakey_field_meta_value);
                }
                $metakey_query = array(
                    'key' => $metakey_field_meta,
                    //'value' => $metakey_field_meta_value,
                    'type' => $metakey_field_meta_type,
                    'compare' => $metakey_field_meta_compare
                );
                if (!in_array($metakey_field_meta_compare, array('EXISTS', 'NOT EXISTS'))) {
                    $metakey_query['value'] = $metakey_field_meta_value;
                }
                if ($metakey_field_meta_compare == '!=') {
                    // include also not set
                    array_push($keysquery, [
                        'relation' => 'OR',
                        [
                            'key' => $metakey_field_meta,
                            'compare' => 'NOT EXISTS'
                        ],
                        $metakey_query,
                    ]);
                } else {
                    array_push($keysquery, $metakey_query);
                }
            }
        }

        if (!empty($keysquery)) {
            $keysquery['relation'] = $settings['metakey_combination'];
            if ($settings['metakey_combination'] == 'XPR' && !empty($settings['metakey_combination_xpr'])) {
                //$expr = "( 1 AND 2 ) OR ( ( 3 OR 4 ) AND ( 5 AND 6 ) )";
                $expr = $settings['metakey_combination_xpr'];
                $pieces = Utils::explode($expr, ' ');
                $cond = [];
                $comb = [];
                $keys = [];
                $level = 0;
                foreach ($pieces as $pkey => $piece) {
                    if ($piece == '(') {
                        // add level
                        $val = Utils::get_array_value($cond, $keys);
                        $keys[] = empty($val) ? 0 : count($val);
                        $level++;
                    }
                    if ($piece == 'AND' || $piece == 'OR') {
                        // set level comb
                        $comb[$level] = $piece;
                        //$comb = Utils::set_array_value($conb, $keys, $piece);;
                    }
                    if (is_numeric($piece)) {
                        $index = intval($piece) - 1;
                        if (isset($keysquery[$index])) {
                            //$cond[$level][] = $keysquery[$index];
                            $keys[] = $index;
                            $cond = Utils::set_array_value($cond, $keys, $keysquery[$index]);
                            array_pop($keys);
                            //var_dump($keys);
                            //echo '<pre>';var_dump($cond);echo '</pre>';
                        }
                    }
                    if ($piece == ')') {
                        // close level
                        $keys[] = 'relation';
                        //$cnb = Utils::get_array_value($conb, $keys);
                        $cnb = $comb[$level];
                        $cond = Utils::set_array_value($cond, $keys, $cnb);
                        $level--;
                        array_pop($keys);
                        array_pop($keys);
                        //echo '<pre>';var_dump($cond);echo '</pre>';
                    }
                    if ($pkey == count($pieces) - 1 && !$level) {
                        //var_dump($comb);
                        $keys = ['relation'];
                        $cond = Utils::set_array_value($cond, $keys, $comb[0]);
                    }
                }
                /* [
                  [
                  1,
                  2,
                  AND,
                  ],
                  [
                  [
                  3,
                  4,
                  OR,
                  ],
                  [

                  5,
                  6,
                  AND,
                  ],
                  AND,
                  ],
                  OR,
                  ] */
                //echo '<pre>';var_dump(json_encode($cond));echo '</pre>';
                $keysquery = $cond;
            }
            $metakey_args['meta_query'] = $keysquery;
        }
        //var_dump($taxquery);
        //
        return $metakey_args;
    }

    public function render_svg_mask($mask_shape_type) {
        $widgetId = $this->get_id();
        $shape_numbers = $this->get_settings('shape_numbers');

        /* $image_url = Group_Control_Image_Size::get_attachment_image_src($this->get_settings('mask_image')['id'], 'image', $settings);
          $image_masking_url = Group_Control_Image_Size::get_attachment_image_src($this->get_settings('image_masking')['id'], 'size_masking', $settings); */

        if ($this->get_settings('image_masking')['url']) {
            $image_masking_url = $this->get_settings('image_masking')['url'];
        }
    }

}
