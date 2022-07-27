<?php

namespace EAddonsForElementor\Modules\Query\Skins\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of infinite-scroll
 *
 * @author fra
 */
trait Infinite_Scroll {

    public function register_style_infinitescroll_controls() {
        //@p la chiave
        $keyInfiniteScroll = 'infiniteScroll';

        $this->start_controls_section(
                'section_style_'.$keyInfiniteScroll, [
            'label' => esc_html__('InfiniteScroll', 'e-addons'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'infiniteScroll_enable' => 'yes'
            ],
                ]
        );
        $this->add_responsive_control(
                $keyInfiniteScroll.'_spacing', [
            'label' => esc_html__('Distance', 'elementor'),
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
                '{{WRAPPER}} .e-add-infiniteScroll' => 'margin-top: {{SIZE}}{{UNIT}};'
            ],
                ]
        );
        $this->add_control(
            $keyInfiniteScroll.'_heading_button_style', [
                'label' => esc_html__('Button', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',

                'condition' => [
                    $keyInfiniteScroll.'_trigger' => 'button',
                ],
            ]
        );
        $this->add_responsive_control(
            $keyInfiniteScroll.'_button_align', [
                'label' => esc_html__('Alignment', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementor'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} div.e-add-infiniteScroll' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    $keyInfiniteScroll.'_trigger' => 'button',
                ],
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $keyInfiniteScroll.'_button_typography',
				'selector' => '{{WRAPPER}} .e-add-infiniteScroll button',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => $keyInfiniteScroll.'_button_text_shadow',
				'selector' => '{{WRAPPER}} .e-add-infiniteScroll button',
			]
		);
        
        $this->start_controls_tabs($keyInfiniteScroll.'_button_colors');

        $this->start_controls_tab(
                $keyInfiniteScroll.'_button_text_colors', [
            'label' => esc_html__('Normal', 'e-addons'),
            'condition' => [
                $keyInfiniteScroll.'_trigger' => 'button',
            ],
                ]
        );

        $this->add_control(
                $keyInfiniteScroll.'_button_text_color', [
            'label' => esc_html__('Text Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-infiniteScroll button' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $keyInfiniteScroll.'_trigger' => 'button',
                //$this->get_control_id($keyInfiniteScroll.'_trigger') => 'button',
            ],
                ]
        );

        $this->add_control(
                $keyInfiniteScroll.'_button_background_color', [
            'label' => esc_html__('Background Color', 'e-addons'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .e-add-infiniteScroll button' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $keyInfiniteScroll.'_trigger' => 'button',
            ],
                ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
                $keyInfiniteScroll.'_button_text_colors_hover', [
            'label' => esc_html__('Hover', 'elementor'),
            'condition' => [
                $keyInfiniteScroll.'_trigger' => 'button',
            ],
                ]
        );
        $this->add_control(
                $keyInfiniteScroll.'_button_hover_color', [
            'label' => esc_html__('Text Color', 'elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-infiniteScroll button:hover' => 'color: {{VALUE}};',
            ],
            'condition' => [
                $keyInfiniteScroll.'_trigger' => 'button',
            ],
                ]
        );
        $this->add_control(
                $keyInfiniteScroll.'_button_background_hover_color', [
            'label' => esc_html__('Background Color', 'elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-infiniteScroll button:hover' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                $keyInfiniteScroll.'_trigger' => 'button',
            ],
                ]
        );
        $this->add_control(
                $keyInfiniteScroll.'_button_hover_border_color', [
            'label' => esc_html__('Border Color', 'elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .e-add-infiniteScroll button:hover' => 'border-color: {{VALUE}};',
            ],
            'condition' => [
                $keyInfiniteScroll.'_trigger' => 'button',
            ],
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                 'name' => $keyInfiniteScroll.'_button_border',
                 'label' => esc_html__('Border', 'elementor'),
                 
                 'selector' => '{{WRAPPER}} .e-add-infiniteScroll button',
             ]
         );
        $this->add_control(
            $keyInfiniteScroll.'_button_radius', [
                'label' => esc_html__('Border Radius', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .e-add-infiniteScroll button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    $keyInfiniteScroll.'_trigger' => 'button',
                ],
            ]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $keyInfiniteScroll.'_button_box_shadow',
				'selector' => '{{WRAPPER}} .e-add-infiniteScroll button',
			]
		);
		$this->add_control(
            $keyInfiniteScroll.'_button_padding', [
                'label' => esc_html__('Padding', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .e-add-infiniteScroll button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    $keyInfiniteScroll.'_trigger' => 'button',
                ],
                'separator' => 'before'
            ]
        );
        $this->end_controls_section();
    }

}