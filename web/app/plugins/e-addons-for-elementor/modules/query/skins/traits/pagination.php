<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of Pagination
 *
 * @author fra
 */
trait Pagination {

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
                    $this->get_control_id('pagination_border_border!') => '',
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
