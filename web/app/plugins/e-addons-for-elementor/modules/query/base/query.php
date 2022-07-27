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
    public $is_first_section = true;

    public function __construct($data = [], $args = null) {
        $this->add_query_actions();
        parent::__construct($data, $args);
        
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
            /* 'font-awesome-5-all', 'font-awesome',*/ 'elementor-icons-fa-solid', 'animatecss',
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
                $tmp = explode('\\',$skin);
                $name = end($tmp);
                $name = strtolower($name);
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
    
    
    /****************************** PHP 8.1 FIX *******************************/

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
    
    /**************************************************************************/

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
                '_skin!' => ['justifiedgrid', 'timeline', 'nextpost', 'table', 'list', 'piling', 'mosaic', 'rapidimages', 'export'],
            ],
                ]
        );
        // ------------------------------------
        $this->add_control(
                'style_items', [
            'label' => esc_html__('Items Style', 'e-addons'),
            'type' => 'ui_selector',
            'label_block' => true,
            'toggle' => false,
            'type_selector' => 'image',
            'columns_grid' => 4,
            'separator' => 'before',
            'options' => [
                /* '' => [
                  'title' => esc_html__('Default','e-addons'),
                  'return_val' => 'val',
                  'image' => E_ADDONS_QUERY_URL . 'assets/img/layout/default.png',
                  ], */
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
                'template' => [
                    'title' => esc_html__('Elementor Template', 'e-addons'),
                    'return_val' => 'val',
                    'image' => E_ADDONS_URL . 'modules/query/assets/img/layout/template.png',
                ],
            ],
            'toggle' => false,
            'render_type' => 'template',
            'prefix_class' => 'e-add-posts-layout-', // ....da cambiare ......
            'default' => 'default',
            //'tablet_default' => '',
            //'mobile_default' => '',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
            ],
                ]
        );
        // +********************* Style: Left, Right, Alternate
        $this->add_responsive_control(
                'image_rate', [
            'label' => esc_html__('Distribution (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '50',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-image-area' => 'width: {{SIZE}}%;',
                '{{WRAPPER}} .e-add-content-area' => 'width: calc( 100% - {{SIZE}}% );',
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => ['left', 'right', 'alternate'],
            ],
                ]
        );

        // +********************* Float Hover style descripton:
        $this->add_control(
                'float_hoverstyle_description',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="eaddicon eicon-image-rollover" aria-hidden="true"></i> ' . esc_html__('Float style allows you to create animations between the content and the underlying image, from "Hover effect" Panel you can set the features.', 'e-addons'),
                    'content_classes' => 'e-add-info-panel',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                        'style_items' => ['float'],
                    ],
                ]
        );
        // +********************* Image Zone Style:
        $this->add_control(
                'heading_imagezone',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="far fa-image"></i> &nbsp;' . esc_html__('IMAGE:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll'],
                        'style_items!' => ['default', 'template'],
                    ],
                ]
        );

        /*

          // +********************* Image Zone: Mask
          $this->add_control(
          'imagemask_popover', [
          'label' => esc_html__('Mask', 'e-addons'),
          'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
          'label_off' => esc_html__('Default', 'e-addons'),
          'label_on' => esc_html__('Custom', 'e-addons'),
          'return_value' => 'yes',
          'condition' => [
          '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
          'style_items!' => ['default', 'template'],
          ],
          ]
          );
          $this->start_popover();
          $this->add_control(
          'mask_heading',
          [
          'label' => esc_html__('Mask', 'e-addons'),
          'description' => esc_html__('Shape Parameters', 'e-addons'),
          'type' => Controls_Manager::HEADING,
          'separator' => 'before',
          'condition' => [
          '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
          'style_items!' => ['default', 'template'],
          'imagemask_popover' => 'yes',
          ],
          ]
          );
          $this->add_group_control(
          Masking::get_type(),
          [
          'name' => 'mask',
          'label' => esc_html__('Mask', 'e-addons'),
          'selector' => '{{WRAPPER}} .e-add-posts-container .e-add-post-image',
          'condition' => [
          '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
          'style_items!' => ['default', 'template'],
          'imagemask_popover' => 'yes',
          ],
          ]
          );
          $this->end_popover();
          // +********************* Image Zone: Transforms
          $this->add_control(
          'imagetransforms_popover',
          [
          'label' => esc_html__('Transforms', 'plugin-name'),
          'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
          'return_value' => 'yes',
          'render_type' => 'ui',
          'condition' => [
          '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
          'style_items!' => ['default', 'template'],
          ],
          ]
          );
          $this->start_popover();

          $this->add_group_control(
          Transform::get_type(),
          [
          'name' => 'transform_image',
          'label' => 'Transform image',
          'selector' => '{{WRAPPER}} .e-add-post-item .e-add-image-area',
          'separator' => 'before',
          'condition' => [
          '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion','softscroll','expander'],
          'style_items!' => ['default', 'template'],
          'imagetransforms_popover' => 'yes',
          ],
          ]
          );
          $this->end_popover();

         */

        // +********************* Image Zone: Filters
        $this->add_group_control(
                Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'imagezone_filters',
                    'label' => 'Filters',
                    'render_type' => 'ui',
                    'selector' => '{{WRAPPER}} .e-add-post-block .e-add-post-image img',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                        'style_items!' => ['default', 'template'],
                    ],
                ]
        );
        // +********************* Content Zone Style:
        $this->add_control(
                'heading_contentzone',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-align-left"></i> &nbsp;' . esc_html__('CONTENT:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                        'style_items!' => ['default', 'template'],
                    ],
                ]
        );
        // +********************* Content Zone: Style
        $this->add_control(
                'contentstyle_popover', [
            'label' => esc_html__('Style', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => esc_html__('Default', 'e-addons'),
            'label_on' => esc_html__('Custom', 'e-addons'),
            'return_value' => 'yes',
            'render_type' => 'ui',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items!' => ['default', 'template'],
            ],
                ]
        );
        $this->start_popover();
        $this->add_control(
                'contentzone_bgcolor', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'background-color: {{VALUE}};'
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'contentzone_border',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-content-area',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_padding', [
            'label' => esc_html__('Padding', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'contentzone_border_radius', [
            'label' => esc_html__('Border Radius', 'e-addons'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            //'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-post-item .e-add-content-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items!' => ['default', 'template'],
                'contentstyle_popover' => 'yes',
            ],
                ]
        );

        $this->end_popover();

        // +********************* Content Zone Transform: Overlay, TextZone, Float
        $this->add_control(
                'contenttransform_popover', [
            'label' => esc_html__('Transform', 'e-addons'),
            'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
            'label_off' => esc_html__('Default', 'e-addons'),
            'label_on' => esc_html__('Custom', 'e-addons'),
            'return_value' => 'yes',
            'render_type' => 'ui',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->start_popover();
        $this->add_responsive_control(
                'contentzone_x', [
            'label' => esc_html__('X', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'range' => [
                '%' => [
                    'min' => -100,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'margin-left: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_y', [
            'label' => esc_html__('Y', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => -100,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'margin-top: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_width', [
            'label' => esc_html__('Width (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'width: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => ['overlay', 'textzone', 'float'],
            ],
                ]
        );
        $this->add_responsive_control(
                'contentzone_height', [
            'label' => esc_html__('Height (%)', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => '%',
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 0.1
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-content-area' => 'height: {{SIZE}}%;',
            ],
            'condition' => [
                'contenttransform_popover' => 'yes',
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => ['float'],
            ],
                ]
        );
        $this->end_popover();
        // +********************* Content Zone: BoxShadow
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'contentzone_box_shadow',
            'selector' => '{{WRAPPER}} .e-add-post-item .e-add-content-area',
            'condition' => [
                '_skin' => ['', 'grid', 'carousel', 'filters', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items!' => ['default', 'template'],
            ],
            'popover' => true
                ]
        );
        // +********************* Content Zone: Float interaction
        $this->add_control(
                'float_interaction', [
            'label' => '<i class="eaddicon fas fa-ban" aria-hidden="true"></i> ' . esc_html__('Stop interaction on content', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before',
            'prefix_class' => 'disable-float-interaction-',
            'description' => esc_html__('This option allows you to stop the interactions on the content to give priority to the image lying behind.', 'e-addons'),
            'condition' => [
                '_skin' => ['', 'grid', 'filters', 'carousel', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => 'float',
            ],
                ]
        );

        /* Responsive --------------- */
        $this->add_control(
                'force_layout_default', [
            'label' => '<i class="eaddicon eicon-device-mobile" aria-hidden="true"></i> ' . esc_html__('Force default layout on mobile', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'prefix_class' => 'force-default-mobile-',
            'condition' => [
                '_skin' => ['', 'grid', 'filters', 'carousel', 'dualslider', 'horizontalscroll', 'cards', 'maps', 'circular', 'accordion', 'softscroll', 'expander'],
                'style_items' => ['left', 'right', 'alternate']
            ],
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

    // -------------- Override Laghtbox (assurdo ... ma inevitabile .. da valutare) ---------
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
