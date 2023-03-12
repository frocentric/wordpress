<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use EAddonsForElementor\Core\Managers\Assets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

use EAddonsForElementor\Core\Utils;

/**
 * Description of infinite-scroll
 *
 * @author fra
 */
trait Pagination {

    public function paginations_enable_controls() {
        // +********************* Pagination ()
        $pagination_support = ['automatic_mode', 'get_cpt', 'get_tax', 'specific_posts', 'get_users_and_roles', 'get_attachments', 'table', 'e_submissions', 'path', 'media', 'product', 'sale', 'featured'];
        
        $this->add_control(
                'heading_pagination',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<i class="fas fa-pager"></i> &nbsp;' . esc_html__('PAGINATION:', 'e-addons'),
                    'content_classes' => 'e-add-icon-heading',
                    'condition' => [
                        //@p il massimo è che la paginazione funzioni con tutti gli skins...
                        '_skin' => ['', 'grid', 'carousel', 'filters', 'justifiedgrid'],
                        'infiniteScroll_enable' => '',
                        'query_type' => $pagination_support,
                    ],
                ]
        );
        
        $this->add_control(
                'pagination_enable', [
            'label' => '<i class="eaddicon eicon-post-navigation" aria-hidden="true"></i> ' . esc_html__('Pagination', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                //@p il massimo è che la paginazione funzioni con tutti gli skins...
                '_skin' => ['', 'grid', 'carousel', 'filters', 'justifiedgrid', 'list', 'table'],
                'infiniteScroll_enable' => '',
                'query_type' => $pagination_support,
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_enable', [
            'label' => '<i class="eaddicon eicon-navigation-horizontal" aria-hidden="true"></i> ' . esc_html__('Infinite Scroll', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'frontend_available' => true,
            'condition' => [
                '_skin' => ['', 'grid', 'filters', 'list', 'table'],
                'pagination_enable!' => ''
            ],
                ]
        );
    }

    protected function add_pagination_section() {
        // ------------------------------------------------------------------ [SECTION PAGINATION]
        $this->start_controls_section(
                'section_pagination', [
            'label' => '<i class="eaddicon eicon-post-navigation" aria-hidden="true"></i> ' . esc_html__('Pagination', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'pagination_enable!' => '',
                'infiniteScroll_enable' => ''
            ],
                ]
        );
        $this->add_control(
                'pagination_show_numbers', [
            'label' => esc_html__('Show Numbers', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
                ]
        );
        $this->add_control(
                'pagination_range', [
            'label' => esc_html__('Range of numbers', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '4',
            'condition' => [
                'pagination_show_numbers!' => '',
            ]
                ]
        );
        // Prev/Next
        $this->add_control(
                'pagination_show_prevnext', [
            'label' => esc_html__('Show Prev/Next', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'pagination_icon_prevnext',
                [
                    'label' => esc_html__('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fa fa-arrow-right',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'recommended' => [
                        'fa-solid' => [
                            'arrow-right',
                            'chevron-right',
                            'angle-right',
                            'chevron-circle-right',
                            'angle-double-right',
                            'caret-right',
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-circle-right',
                            'arrow-alt-circle-right',
                            'long-arrow-alt-right'
                        ],
                        'fa-regular' => [
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-alt-circle-right',
                        ],
                    ],
                    'fa4compatibility' => 'icon',
                    'condition' => [
                        'pagination_show_prevnext' => 'yes',
                    ],
                ]
        );
        $this->add_control(
                'pagination_prev_label', [
            'label' => esc_html__('Previous Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Previous', 'e-addons'),
            'condition' => [
                'pagination_show_prevnext' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'pagination_next_label', [
            'label' => esc_html__('Next Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Next', 'e-addons'),
            'condition' => [
                'pagination_show_prevnext' => 'yes',
            ],
                ]
        );
        // first/last
        $this->add_control(
                'pagination_show_firstlast', [
            'label' => esc_html__('Show First/Last', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before'
                ]
        );
        $this->add_control(
                'pagination_icon_firstlast',
                [
                    'label' => esc_html__('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fa fa-arrow-right',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'recommended' => [
                        'fa-solid' => [
                            'arrow-right',
                            'chevron-right',
                            'angle-right',
                            'chevron-circle-right',
                            'angle-double-right',
                            'caret-right',
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-circle-right',
                            'arrow-alt-circle-right',
                            'long-arrow-alt-right'
                        ],
                        'fa-regular' => [
                            'caret-square-right',
                            'hand-point-right',
                            'arrow-alt-circle-right',
                        ],
                    ],
                    'fa4compatibility' => 'icon',
                    'condition' => [
                        'pagination_show_firstlast!' => '',
                    ],
                ]
        );
        $this->add_control(
                'pagination_first_label', [
            'label' => esc_html__('First Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('First', 'e-addons'),
            'condition' => [
                'pagination_show_firstlast!' => '',
            ],
                ]
        );
        $this->add_control(
                'pagination_last_label', [
            'label' => esc_html__('Last Label', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Last', 'e-addons'),
            'condition' => [
                'pagination_show_firstlast!' => '',
            ],
                ]
        );
        $this->add_control(
                'pagination_show_progression', [
            'label' => esc_html__('Show Progression', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before'
                ]
        );
        
        $this->add_control(
                'heading_pagination_ajax',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'separator' => 'before',
                    'raw' => esc_html__('Ajax', 'e-addons'),
                    'content_classes' => 'e-add-inner-heading',
                ]
        );
        $this->add_control(
                'pagination_ajax', [
            'label' => esc_html__('Reload Archive in AJAX', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'pagination_ajax_url', [
            'label' => esc_html__('Update Windows Location', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                'pagination_ajax!' => '',
            ],
                ]
        );
        $this->add_control(
                'pagination_ajax_top', [
            'label' => esc_html__('Scroll to Top', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                'pagination_ajax!' => '',
            ],
                ]
        );
        $this->end_controls_section();
    }

    /// questo è il valore di paged
    public function get_current_page() {
        if ('' === $this->get_settings_for_display('pagination_enable')) {
            return 1;
        }
        return Utils::get_current_page_num();
        
    }

    ///
    public function get_next_pagination() {
        //global $paged;
        $paged = $this->get_current_page(); //max(1, get_query_var('paged'), get_query_var('page'));

        if (empty($paged))
            $paged = 1;

        $link_next = Utils::get_linkpage($paged + 1);

        return $link_next;
    }

    ///
    public function render_pagination() {
        //@p qui renderizzo la paginazione se abilitata
        // ....
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        //$this->get_instance_value('pagination_enable');
        //var_dump($settings);
        // Numeric pagination -----------------------------------------------
        if (!empty($settings['pagination_enable'])) {
            $query = $this->get_query();
            $querytype = $this->get_querytype();

            $page_limit = apply_filters('e_addons/query/page_limit/'.$this->get_id(), 1, $this, $query, $settings);
            
            $this->numeric_query_pagination($page_limit, $settings);
        }

        // Infinite scroll pagination -----------------------------------------------
        $this->render_infinite_scroll();
        // --------------------------------------------------------------------
    }

    ///
    public function numeric_query_pagination($pages, $settings) {
        $icon_first = '';
        $icon_last = '';
        if (!empty($settings['pagination_icon_prevnext']['value'])) {
            if ($settings['pagination_icon_prevnext']['value']) {
                $icon_prevnext = str_replace('right', '', $settings['pagination_icon_prevnext']['value']);
                $icon_prev = '<i class="' . $icon_prevnext . 'left"></i> ';
                $icon_next = '<i class="' . $icon_prevnext . 'right"></i> ';
            }
        }
        if (!empty($settings['pagination_icon_firstlast']['value'])) {
            if ($settings['pagination_icon_firstlast']['value']) {
                $icon_firstlast = str_replace('right', '', $settings['pagination_icon_firstlast']['value']);
                $icon_first = '<i class="' . $icon_firstlast . 'left"></i> ';
                $icon_last = '<i class="' . $icon_firstlast . 'right"></i> ';
            }
        }
        $range = (int) $settings['pagination_range'] - 1; //la quantità di numeri visualizzati alla volta
        //@p in questo passaggio ho dei dubbi ..vedo il risultato..
        $showitems = ($range)/* - 1 */;
        //$showitems = ($range * 2)/* - 1*/;

        $paged = Utils::get_current_page_num();

        if (empty($paged))
            $paged = 1;


        if ($pages == '') {
            global $wp_query;

            $pages = $wp_query->max_num_pages;

            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages) {
            
            //$anchor = '#'.$this->get_id();
            
            echo '<nav class="elementor-pagination e-add-pagination" role="navigation" aria-label="Pagination">';

            //Progression
            if ($settings['pagination_show_progression'])
                echo '<span class="progression">' . $paged . ' / ' . $pages . '</span>';

            /* echo "<span>paged: ".$paged."</span>";
              echo "<span>range: ".$range."</span>";
              echo "<span>showitems: ".$showitems."</span>";
              echo "<span>pages: ".$pages."</span>"; */

            //First
            if ($settings['pagination_show_firstlast'])
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages)
                    echo '<a href="' . Utils::get_linkpage(1) . '" class="pagefirst">' . $icon_first . ' ' . esc_html__($settings['pagination_first_label'], 'e-addons' . '_strings') . '</a>';

            //Prev
            if ($settings['pagination_show_prevnext'])
                if ($paged > 1 && $showitems < $pages)
                    echo '<a href="' . Utils::get_linkpage($paged - 1) . '" class="pageprev">' . $icon_prev . ' ' . esc_html__($settings['pagination_prev_label'], 'e-addons' . '_strings') . '</a>';

            //Numbers
            if ($settings['pagination_show_numbers'])
                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                        echo ($paged == $i) ? '<span class="current">' . $i . '</span>' : '<a href="' . Utils::get_linkpage($i) . '" class="inactive">' . $i . '</a>';
                    }
                }

            //Next
            if ($settings['pagination_show_prevnext'])
                if ($paged < $pages && $showitems < $pages)
                    echo '<a href="' . Utils::get_linkpage($paged + 1) . '" class="pagenext">' . esc_html__($settings['pagination_next_label'], 'e-addons' . '_strings') . $icon_next . '</a>';

            //Last
            if ($settings['pagination_show_firstlast'])
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
                    echo '<a href="' . Utils::get_linkpage($pages) . '" class="pagelast">' . esc_html__($settings['pagination_last_label'], 'e-addons' . '_strings') . $icon_last . '</a>';

            echo '</nav>';
        }
    }
    
    // @p questa è la parte di style relativa alla paginazione
    public function register_style_pagination_controls() {
        $this->start_controls_section(
            'section_style_pagination', [
                'label' => esc_html__('Pagination', 'e-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_enable' => 'yes',
                    'infiniteScroll_enable' => ''
                ],
            ]
        );
        
        //@p per tutto l'elemento di paginazione
        $this->add_responsive_control(
            'pagination_align', [
                'label' => esc_html__('Alignment', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'e-addons'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'e-addons'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'e-addons'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination' => 'justify-content: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'pagination_space', [
                'label' => esc_html__('Space', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                        'min' => 0,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination' => 'padding-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );


        // ***************************************************
        // base

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'pagination_typography',
                'label' => esc_html__('Typography', 'e-addons'),
                'selector' => '{{WRAPPER}} .e-add-pagination',
            ]
        );

        
        //@p le spaziature di tutti gli elementi
        $this->add_control(
            'pagination_heading_spacing', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' =>  '<i class="fas fa-arrows-alt-h"></i>&nbsp; <b>'.esc_html__('Spacing', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'separator' => 'before',
            ]
        );
        
        $this->add_responsive_control(
            'pagination_spacing', [
                'label' => esc_html__('Horizontal Spacing', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                        'min' => 0,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span, {{WRAPPER}} .e-add-pagination a' => 'margin-right: {{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_control(
            'pagination_padding', [
                'label' => esc_html__('Padding', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span, {{WRAPPER}} .e-add-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'pagination_radius', [
                'label' => esc_html__('Border Radius', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span, {{WRAPPER}} .e-add-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        

        //@p i colori di tutti gli elementi
        $this->add_control(
            'pagination_heading_colors', [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' =>  '<i class="fas fa-tint"></i>&nbsp; <b>'.esc_html__('Colors', 'e-addons') . '</b>',
                'content_classes' => 'e-add-inner-heading',
                'separator' => 'before',
            ]
        );
        

        $this->start_controls_tabs('pagination_colors');

        $this->start_controls_tab(
            'pagination_text_colors', [
                'label' => esc_html__('Normal', 'e-addons'),
            ]
        );

        $this->add_control(
            'pagination_text_color', [
                'label' => esc_html__('Text Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span, {{WRAPPER}} .e-add-pagination a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_background_color', [
                'label' => esc_html__('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span, {{WRAPPER}} .e-add-pagination a' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'pagination_border',
                'label' => esc_html__('Border', 'e-addons'),
                'selector' => '{{WRAPPER}} .e-add-pagination span, {{WRAPPER}} .e-add-pagination a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_text_colors_hover', [
                'label' => esc_html__('Hover', 'e-addons'),
            ]
        );
        $this->add_control(
            'pagination_hover_color', [
                'label' => esc_html__('Text Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'pagination_background_hover_color', [
                'label' => esc_html__('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination a:hover' => 'background-color: {{VALUE}};',
                ]
            ]
        );
        $this->add_control(
            'pagination_hover_border_color', [
                'label' => esc_html__('Border Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'pagination_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_text_colors_current', [
                'label' => esc_html__('Current', 'e-addons'),
            ]
        );
        $this->add_control(
            'pagination_current_color', [
                'label' => esc_html__('Text Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span.current' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'pagination_background_current_color', [
                'label' => esc_html__('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span.current' => 'background-color: {{VALUE}};',
                ]
            ]
        );
        $this->add_control(
            'pagination_current_border_color', [
                'label' => esc_html__('Border Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                'pagination_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination span.current' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        

        $this->generate_register_style_pagination('prevnext', 'Buttons Prev & Next', ['.pageprev','.pagenext']);
        $this->generate_register_style_pagination('firstlast', 'Button First & Last', ['.pagefirst','.pagelast']);
        $this->generate_register_style_pagination('progression', 'Fraction Progression', ['.progression','.progression']);

        $this->end_controls_section();
    }


    // $keypag '_prevnex' '' '' ''
    // $labelpag 'Prev/Next' '' '' ''
    // $classpag ['.pageprev','.pagenext']
    public function generate_register_style_pagination($keypag, $labelpag, $classpag){
        // heading ******************************************
        $this->add_control(
            'pagination_heading_'.$keypag, [
                'type' => Controls_Manager::RAW_HTML,
                'show_label' => false,
                'raw' =>  '<b>'.esc_html__($labelpag, 'e-addons') . '</b>',
                'content_classes' => 'e-add-icon-heading',
                'separator' => 'before',
                'condition' => [
                    'pagination_show_'.$keypag => 'yes',
                ],
            ]
        );
        // spacing ******************************************
        $this->add_responsive_control(
            'pagination_spacing_'.$keypag, [
                'label' => esc_html__('Spacing', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '',
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                        'min' => 0,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination '.$classpag[1] => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .e-add-pagination '.$classpag[0] => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'pagination_show_'.$keypag => 'yes',
                ]
            ]
        );
        // icon (heading, size, spacing) ******************************************
        if($keypag != 'progression'){
            $this->add_control(
                'pagination_heading_icons_'.$keypag, [
                    'label' => esc_html__('Icons', 'e-addons'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                    ]
                ]
            );
            $this->add_responsive_control(
                'pagination_icon_spacing_'.$keypag, [
                    'label' => esc_html__('Icon Spacing', 'e-addons'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'max' => 50,
                            'min' => 0,
                            'step' => 1,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-add-pagination '.$classpag[0].' i' => 'margin-right: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .e-add-pagination '.$classpag[1].' i' => 'margin-left: {{SIZE}}{{UNIT}};'
                    ],
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                    ]
                ]
            );
            $this->add_responsive_control(
                'pagination_icon_size_'.$keypag, [
                    'label' => esc_html__('Icon Size', 'e-addons'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'max' => 100,
                            'min' => 0,
                            'step' => 1,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-add-pagination '.$classpag[0].' i' => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .e-add-pagination '.$classpag[1].' i' => 'font-size: {{SIZE}}{{UNIT}};'
                    ],
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                    ]
                ]
            );
        }
        // tabs: ******************************************
        // normal - hover - [active] .. text/bg/border
        if($keypag != 'progression'){
            $this->start_controls_tabs('pagination_'.$keypag.'_colors');

            $this->start_controls_tab(
                'pagination_'.$keypag.'_text_colors', [
                    'label' => esc_html__('Normal', 'e-addons'),
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                    ]
                ]
            );
        }
        $this->add_control(
            'pagination_'.$keypag.'_text_color', [
                'label' => esc_html__('Text Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination '.$classpag[0].', 
                     {{WRAPPER}} .e-add-pagination '.$classpag[1] => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'pagination_show_'.$keypag => 'yes',
                ]
            ]
        );
        $this->add_control(
            'pagination_'.$keypag.'_background_color', [
                'label' => esc_html__('Background Color', 'e-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination '.$classpag[0].', 
                     {{WRAPPER}} .e-add-pagination '.$classpag[1] => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'pagination_show_'.$keypag => 'yes',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'pagination_'.$keypag.'_border',
                'label' => esc_html__('Border', 'e-addons'),
                'selector' => '{{WRAPPER}} .e-add-pagination '.$classpag[0].', 
                               {{WRAPPER}} .e-add-pagination '.$classpag[1],
                'condition' => [
                    'pagination_show_'.$keypag => 'yes',
                ]
            ]
        );
        
        if($keypag != 'progression'){
            $this->end_controls_tab();

            $this->start_controls_tab(
                'pagination_'.$keypag.'_text_colors_hover', [
                    'label' => esc_html__('Hover', 'e-addons'),
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'pagination_'.$keypag.'_hover_color', [
                    'label' => esc_html__('Text Color', 'e-addons'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .e-add-pagination '.$classpag[0].':hover, 
                        {{WRAPPER}} .e-add-pagination '.$classpag[1].':hover' => 'color: {{VALUE}};',
                    ],
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'pagination_'.$keypag.'_background_hover_color', [
                    'label' => esc_html__('Background Color', 'e-addons'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .e-add-pagination '.$classpag[0].':hover, 
                        {{WRAPPER}} .e-add-pagination '.$classpag[1].':hover' => 'background-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'pagination_'.$keypag.'_hover_border_color', [
                    'label' => esc_html__('Border Color', 'e-addons'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .e-add-pagination '.$classpag[0].':hover, 
                        {{WRAPPER}} .e-add-pagination '.$classpag[1].':hover' => 'border-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'pagination_show_'.$keypag => 'yes',
                        'pagination_'.$keypag.'_border_border!' => '',
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();
        }
        // radius ******************************************
        $this->add_control(
            'pagination_'.$keypag.'_radius', [
                'label' => esc_html__('Border Radius', 'e-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .e-add-pagination '.$classpag[0].', 
                     {{WRAPPER}} .e-add-pagination '.$classpag[1] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'pagination_show_'.$keypag => 'yes',
                ]
            ]
        );
    }
    

}
