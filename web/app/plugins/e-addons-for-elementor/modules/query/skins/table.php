<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Table Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Table extends Base {

    public function get_script_depends() {
        return ['datatables-jquery', 'datatables-jszip', 'datatables-buttons', 'datatables-html5', 'datatables-responsive', 'datatables-fixedHeader', 'e-addons-query-table'];
    }

    public function get_style_depends() {
        return ['e-addons-common-query','datatables-jquery', 'datatables-buttons', 'datatables-responsive', 'datatables-fixedHeader'];
    }

    public function get_id() {
        return 'table';
    }
    
    public function get_pid() {
        return 13045;
    }

    public function get_title() {
        return esc_html__('Table', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-skin-table';
    }

    public function register_additional_controls() {
        //var_dump($this->get_id());
        //var_dump($this->parent->get_settings('_skin')); //->get_current_skin()->get_id();

        $this->start_controls_section(
                'section_table', [
            'label' => '<i class="eaddicon eadd-skin-table"></i> '. esc_html__('Table', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'datatables',
                [
                    'label' => esc_html__('Use DataTables', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'condition' => [
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );

        $this->add_control(
                'searching',
                [
                    'label' => esc_html__('Searching', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'frontend_available' => true,
                    'options' => [
                        '' => esc_html__('None'),
                        'general' => esc_html__('General'),
                        'fields' => esc_html__('Fields'),
                        'both' => esc_html__('Both'),
                    ],
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );
        $this->add_control(
                'ordering',
                [
                    'label' => esc_html__('Ordering', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );
        $this->add_control(
                'info',
                [
                    'label' => esc_html__('Info', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );
        /*$this->add_control(
                'scrollx',
                [
                    'label' => esc_html__('Scroll Horizontally', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'selectors' => [
                        '{{WRAPPER}} table' => 'max-width: 100%',
                    ],
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );*/
        $this->add_control(
                'responsive',
                [
                    'label' => esc_html__('Responsive', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'selectors' => [
                        '{{WRAPPER}} table' => 'max-width: 100%',
                    ],
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );
        $this->add_control(
                'buttons',
                [
                    'label' => esc_html__('Buttons', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );

        $this->add_control(
                'hide_header',
                [
                    'label' => esc_html__('Hide Header', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                ]
        );
        $this->add_control(
                'fixed_header',
                [
                    'label' => esc_html__('Fixed Header', 'e-addons'),
                    'type' => Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        $this->get_id() . '_hide_header' => '',
                        'infiniteScroll_enable' => '',
                    ]
                ]
        );
        
        $this->add_control(
                'language',
                [
                    'label' => esc_html__('Translation Code', 'e-addons'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => 'it_it', //'zeroRecords|Nothing found - sorry',
                    //'description' => esc_html__('Set each translation per line or insert full url of json translation, example "url|//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"'),
                    'description' => __('See here <a href="https://datatables.net/plug-ins/i18n/" target="_blank">full list of translation available</a>, or insert custom url to your json'),
                    'frontend_available' => true,
                    'condition' => [
                        $this->get_id() . '_datatables!' => '',
                        'infiniteScroll_enable' => '',
                    ],
                    'separator' => 'before',
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_filter input[type=search]' => 'width: auto;',
                    ]
                ]
        );

        $this->add_control(
                'mobile',
                [
                    'label' => esc_html__('Mobile Optimization', 'e-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'condition' => [
                        $this->get_id() . '_datatables' => ''
                    ]
                ]
        );

        $this->end_controls_section();
    }

    public function add_special_controls($selector = 'table', $selector_id = 'table', $conditions = array()) {
        $this->add_control(
                $selector_id . '_color', [
            'label' => esc_html__('Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} ' . $selector => 'color: {{VALUE}};'
            ],
            'condition' => $conditions,
                ]
        );
        $this->add_control(
                $selector_id . '_bgcolor', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} ' . $selector => 'background-color: {{VALUE}};'
            ],
            'condition' => $conditions,
                ]
        );
        if ($selector == 'table>tbody>tr') {
            $this->add_group_control(
                    Group_Control_Border::get_type(), [
                'name' => $selector_id . '_border',
                'selector' => $selector,
                'condition' => $conditions,
                    ]
            );
        }
    }

    public function add_common_controls($selector = 'table', $selector_id = 'table', $conditions = array()) {

        $selector_cell = '{{WRAPPER}} ' . $selector . '>td, {{WRAPPER}} ' . $selector . '>th';
        if ($selector == 'table' || strpos($selector, 'td') !== false || strpos($selector, 'th') !== false) {
            $selector_cell = '{{WRAPPER}} ' . $selector;
        }

        if ($selector != 'table') {
            $this->add_responsive_control(
                    $selector_id . '_padding', [
                'label' => esc_html__('Padding', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => $conditions,
                    ]
            );
        }
        if (strpos($selector_id, 'datatables') !== false) {
            $selector_cell = '{{WRAPPER}} ' . $selector;
            $this->add_responsive_control(
                    $selector_id . '_margin', [
                'label' => esc_html__('Margin', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => $conditions,
                    ]
            );
        }

        if ($selector_id == 'table' || $selector_id == 'table_th_style') {
            $this->add_responsive_control(
                    $selector_id . '_align', [
                'label' => esc_html__('Text Alignment', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => true,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'e-addons'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'e-addons'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'e-addons'),
                        'icon' => 'fa fa-align-right',
                    ]
                ],
                'default' => 'left',
                'prefix_class' => 'e-add-align%s-',
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => 'text-align: {{VALUE}};',
                ],
                'condition' => $conditions,
                    ]
            );
            $this->add_group_control(
                    Group_Control_Typography::get_type(), [
                'name' => $selector_id . '_typography',
                'label' => esc_html__('Typography', 'e-addons'),
                'selector' => '{{WRAPPER}} ' . $selector,
                'condition' => $conditions,
                    ]
            );
        }

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => $selector_id . '_border',
            'selector' => $selector_cell,
            'condition' => $conditions,
                ]
        );

        if ($selector_id == 'table' || $selector_id == 'table_th_style' || strpos($selector_id, 'datatables') !== false) {
            $this->add_control(
                    $selector_id . '_border_radius', [
                'label' => esc_html__('Border Radius', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} ' . $selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => $conditions,
                    ]
            );
        }
    }

    public function register_style_controls() {
        parent::register_style_controls();

        $this->start_controls_section(
                'section_style_table',
                [
                    'label' => '<i class="eaddicon eadd-skin-table"></i> ' .esc_html__('Table', 'e-addons'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'table_reset_style', [
            'label' => esc_html__('Reset Theme style', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'selectors' => [
                '{{WRAPPER}} table' => 'margin-bottom:0;font-size:1;',
                '{{WRAPPER}} table td, {{WRAPPER}} table th' => 'padding:0;line-height:1;border: none;',
                '{{WRAPPER}} table th' => 'font-weight: initial;',
                '{{WRAPPER}} table tbody>tr:nth-child(odd)>td, {{WRAPPER}} table tbody>tr:nth-child(odd)>th' => 'background: none;',
            /* table caption+thead tr:first-child td,
              table caption+thead tr:first-child th,
              table colgroup+thead tr:first-child td,
              table colgroup+thead tr:first-child th,
              table thead:first-child tr:first-child td,
              table thead:first-child tr:first-child th {
              border-top:1px solid #ccc
              }
              table tbody+tbody {
              border-top:2px solid #ccc
              }

              @media (max-width:767px) {
              table table {
              font-size:.8em
              }
              table table td,
              table table th {
              padding:7px;
              line-height:1.3
              }
              table table th {
              font-weight:400
              }
              }
             * 
             */
            ],
                ]
        );

        $this->add_control(
                'table_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-table"></i> <b>' . esc_html__('Table', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
                ]
        );
        $this->add_common_controls();

        $this->add_responsive_control(
                'vertical_align',
                [
                    'label' => esc_html__('Vertical align', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'e-addons'),
                        //'baseline' => esc_html__('Baseline', 'e-addons'),
                        'top' => esc_html__('Top', 'e-addons'),
                        'middle' => esc_html__('Middle', 'e-addons'),
                        'top' => esc_html__('Bottom', 'e-addons'),
                    ],
                    'selectors' => [
                        //'{{WRAPPER}} .e-add-posts-container' => 'column-gap: {{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} table td, {{WRAPPER}} table th' => 'vertical-align: {{VALUE}}',
                    ],
                ]
        );

        $this->add_responsive_control(
                'border_spacing',
                [
                    'label' => '<i class="fas fa-arrows-alt-h"></i>&nbsp;' . esc_html__('Border spacing', 'e-addons'),
                    'type' => Controls_Manager::SLIDER,
                    /* 'default' => [
                      'size' => 5,
                      ], */
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        //'{{WRAPPER}} .e-add-posts-container' => 'column-gap: {{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} table' => 'border-spacing: {{SIZE}}{{UNIT}}; border-collapse: separate;',
                    ],
                ]
        );

        // ROW
        // normal
        // even
        // odd
        // hover

        $this->add_control(
                'rows_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-bars"></i> <b>' . esc_html__('Rows', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
                ]
        );

        $selector = 'table>tbody>tr';
        $selector_id = 'table_tr_style';
        $this->start_controls_tabs($selector_id);

        $this->start_controls_tab(
                $selector_id . '_normal', [
            'label' => esc_html__('Normal', 'e-addons'),
                ]
        );
        $this->add_special_controls($selector, $selector_id . '_normal');
        $this->end_controls_tab();

        $this->start_controls_tab(
                $selector_id . '_even', [
            'label' => esc_html__('Even', 'e-addons'),
                ]
        );
        $this->add_special_controls($selector . ':nth-child(even)>td', $selector_id . '_even');
        $this->end_controls_tab();

        $this->start_controls_tab(
                $selector_id . '_odd', [
            'label' => esc_html__('Odd', 'e-addons'),
                ]
        );
        $this->add_special_controls($selector . ':nth-child(odd)>td', $selector_id . '_odd');
        $this->end_controls_tab();

        $this->start_controls_tab(
                $selector_id . '_hover', [
            'label' => esc_html__('Hover', 'e-addons'),
                ]
        );
        $this->add_special_controls($selector . ':hover>td', $selector_id . '_hover');
        $this->end_controls_tab();

        $this->end_controls_tabs();

        //$this->add_common_controls($selector, $selector_id);
        // TD
        // normal
        // even
        // odd

        $this->add_control(
                'cols_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-columns"></i> <b>' . esc_html__('Cells', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
                ]
        );

        $selector = 'table>tbody>tr>td';
        $selector_id = 'table_td_style';
        $this->start_controls_tabs($selector_id);

        $this->start_controls_tab(
                $selector_id . '_normal', [
            'label' => esc_html__('Normal', 'e-addons'),
                ]
        );
        $this->add_special_controls($selector, $selector_id . '_normal');
        $this->end_controls_tab();

        $this->start_controls_tab(
                $selector_id . '_even', [
            'label' => esc_html__('Even', 'e-addons'),
                ]
        );
        $this->add_special_controls($selector . ':nth-child(even)', $selector_id . '_even');
        $this->end_controls_tab();

        $this->start_controls_tab(
                $selector_id . '_odd', [
            'label' => esc_html__('Odd', 'e-addons'),
                ]
        );
        $this->add_special_controls($selector . ':nth-child(odd)', $selector_id . '_odd');
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_common_controls($selector, $selector_id);
        // TH
        // normal
        // even
        // odd

        $this->add_control(
                'heads_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-header"></i> <b>' . esc_html__('Head', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
                ]
        );

        $selector = 'table>thead>tr>th, {{WRAPPER}} table>tbody>tr>td:before, table.fixedHeader-floating>thead>tr>th';
        $selector_id = 'table_th_style';
        $this->add_special_controls($selector, $selector_id);
        $this->add_common_controls($selector, $selector_id);

        $conditions_datatables = array($this->get_id() . '_datatables!' => '');

        $this->add_control(
                'datatables_heading',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-table"></i>&nbsp;&nbsp;' . esc_html__('Datatables', 'e-addons'),
                    'label_block' => false,
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => $conditions_datatables,
                ]
        );
        $conditions_datatables_info = $conditions_datatables;
        $conditions_datatables_info[$this->get_id() . '_info!'] = '';
        /* $this->add_control(
          'datatables_info_heading', [
          'type' => Controls_Manager::HEADING,
          'label' => esc_html__('Info', 'e-addons'),
          'condition' => $conditions_datatables_info,
          ]
          ); */
        $this->add_control(
                'datatables_info_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-info"></i> <b>' . esc_html__('Info', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading', 'condition' => $conditions_datatables_info,
                ]
        );

        $selector_id = 'datatables_info';
        $selector = '.dataTables_wrapper .dataTables_info';
        $this->add_special_controls($selector, $selector_id, $conditions_datatables_info);
        $this->add_common_controls($selector, $selector_id, $conditions_datatables_info);

        $conditions_datatables_filter = $conditions_datatables;
        $conditions_datatables_filter[$this->get_id() . '_searching!'] = '';
        /* $this->add_control(
          'datatables_filter_heading', [
          'type' => Controls_Manager::HEADING,
          'label' => esc_html__('Search', 'e-addons'),
          'condition' => $conditions_datatables_filter,
          ]
          ); */
        $this->add_control(
                'datatables_filter_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-search"></i> <b>' . esc_html__('Search', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => $conditions_datatables_filter,
                ]
        );
        $selector_id = 'datatables_filter';
        $selector = '.dataTables_wrapper .dataTables_filter input';
        $this->add_special_controls($selector, $selector_id, $conditions_datatables_filter);
        $this->add_common_controls($selector, $selector_id, $conditions_datatables_filter);

        $conditions_datatables_buttons = $conditions_datatables;
        $conditions_datatables_buttons[$this->get_id() . '_buttons!'] = '';
        /* $this->add_control(
          'datatables_buttons_heading', [
          'type' => Controls_Manager::HEADING,
          'label' => esc_html__('Buttons', 'e-addons'),
          'condition' => $conditions_datatables_buttons,
          ]
          ); */
        $this->add_control(
                'datatables_buttons_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-mouse-pointer"></i> <b>' . esc_html__('Buttons', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => $conditions_datatables_buttons,
                ]
        );
        $selector_id = 'datatables_buttons';
        $selector = '.dataTables_wrapper .dt-buttons button';
        //$this->add_special_controls($selector, $selector_id, $conditions_datatables_buttons);
        $this->start_controls_tabs($selector_id);
        $this->start_controls_tab(
                $selector_id . '_normal', [
            'label' => esc_html__('Normal', 'e-addons'),
            'condition' => $conditions_datatables_buttons,
                ]
        );
        $this->add_special_controls($selector, $selector_id . '_normal', $conditions_datatables_buttons);
        $this->end_controls_tab();
        $this->start_controls_tab(
                $selector_id . '_hover', [
            'label' => esc_html__('Hover', 'e-addons'),
            'condition' => $conditions_datatables_buttons,
                ]
        );
        $this->add_special_controls($selector . ':hover', $selector_id . '_hover', $conditions_datatables_buttons);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_common_controls($selector, $selector_id, $conditions_datatables_buttons);

        $this->end_controls_section();
    }

    public function render_element_item() {
        
        $this->index++;

        $this->render_item_start();

        $this->render_items();

        $this->render_item_end();

        $this->counter++;
    }

    public function get_container_class() {
        $mobile = $this->parent->get_settings_for_display($this->get_id().'_mobile') ? ' no-more-tables' : '';
        return 'e-add-skin-' . $this->get_id().$mobile;
    }
    
    public function render_loop_start() {
        $this->parent->add_render_attribute('eaddposts_container', [
            'class' => [
                'e-table',
                'e-add-posts-container',
                'e-add-posts',
                $this->get_scrollreveal_class(), //@p prevedo le classi per generare il reveal,
                $this->get_container_class(), //@p una classe personalizzata per lo skin
            ],
        ]);
        ?>
        <?php
        echo '<table ' . $this->parent->get_render_attribute_string('eaddposts_container') . '>';

        $hide_header = $this->parent->get_settings($this->get_id() . '_hide_header');
        if (!$hide_header) {
            echo '<thead><tr>';
            $_items = $this->parent->get_settings_for_display('list_items');
            // ITEMS ///////////////////////
            if ($this->parent->get_querytype() == 'attachment') {
                echo '<th>' . esc_html__('Media') . '</th>';
            } 
            foreach ($_items as $item) {
                $label = $this->get_item_label($item);
                echo '<th>' . $label . '</th>';
            }
            echo '</tr></thead>';
        }

        echo '<tbody class="e-add-posts-wrapper">';
    }

    public function render_loop_end() {
        echo '</tbody></table>';
        
        if ($this->parent->get_settings_for_display($this->get_id().'_mobile')) {
            $selector = '.elementor-element-'.$this->parent->get_id().' table.no-more-tables';
            echo '<style>@media only screen and (max-width: '.$this->parent->get_settings_for_display($this->get_id().'_mobile').'px) {
		'.$selector.', '.$selector.' thead, '.$selector.' tbody, '.$selector.' th, '.$selector.' td, '.$selector.' tr { 
                    display: block; }
            '.$selector.' thead tr { 
                    position: absolute;
                    top: -9999px;
                    left: -9999px; }
            '.$selector.' td { 
                    width: 100% !important;
                    position: relative;
                    padding-left: 40%;
            }
            '.$selector.' td:before { 
                    content: attr(data-title);
                    position: absolute;
                    top: 0;
                    left: 0;
                    height: 100%;
                    width: 40%;
                    text-align:left;    
            }
            }</style>';
        }
    }

    public function render_item_start($key = 'post') {
        //@p una classe personalizzata per lo skin
        $item_class = ' ' . $this->get_item_class();
        ?>
        <tr<?php
        echo ' class="e-add-post e-add-post-item e-add-post-item-' . $this->parent->get_id() . $item_class . '"';
        //post_class(['e-add-post e-add-post-item e-add-post-item-' . $this->parent->get_id() . $item_class]);
        echo ' data-post-id="' . $this->current_id . '"'; //@p data post ID
        ?>><?php
    }

    public function render_item_end() {
        echo '</tr>';
    }

    /* public function render_items() {
      $_skin = $this->parent->get_settings_for_display('_skin');
      $this->render_items_content();
      } */

    public function render_repeateritem_start($item, $tag = 'td') {
        $tag = $this->parent->get_settings_for_display($this->get_id().'_mobile') ? $tag.' data-title="'.$this->get_item_label($item).'"' : $tag;
        parent::render_repeateritem_start($item, $tag);
    }

    public function render_repeateritem_end($tag = 'td') {
        parent::render_repeateritem_end($tag);
    }
    
}
