<?php

namespace EAddonsDev\Modules\Remote\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor PhpRaw
 *
 * Elementor widget for e-addons
 *
 */
class Rss extends Base_Widget {

    use \EAddonsDev\Modules\Remote\Traits\Cache;

    public function get_name() {
        return 'e-rss';
    }

    public function get_title() {
        return esc_html__('RSS Feed', 'e-addons');
    }

    public function get_pid() {
        return 17026;
    }

    public function get_icon() {
        return 'eadd-remote-rssfeed';
    }

    /**
     * Show in panel.
     *
     * Whether to show the widget in the panel or not. By default returns true.
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool Whether to show the widget in the panel or not.
     */
    /*public function show_in_panel() {
        return false;
    }*/

    protected function register_controls() {
        $this->start_controls_section(
                'section_rest_api', [
            'label' => esc_html__('RSS', 'e-addons'),
                ]
        );

        $this->add_control(
                'url', [
            'label' => esc_html__('RSS Feed URL', 'e-addons'),
            'description' => esc_html__('The full URL of RSS Feed', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'http://www.fao.org/biotech/biotech-news/rss/en/',
            'label_block' => true,
                ]
        );

        $this->add_control(
                'single_or_archive', [
            'label' => esc_html__('Single or Archive', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'single' => [
                    'title' => esc_html__('Single', 'e-addons'),
                    'icon' => 'eicon-single-post',
                ],
                'archive' => [
                    'title' => esc_html__('Archive', 'e-addons'),
                    'icon' => 'eicon-archive',
                ],
            ],
            'default' => 'single',
            'toggle' => false,
            'description' => esc_html__('Show Single info or the full News Archive', 'e-addons'),
            'condition' => [
                'url!' => '',
            ],
                ]
        );
        $this->add_control(
                'single_info', [
            'label' => esc_html__('RSS Extra Info', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'title',
            'default' => 'title',
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'single'
            ],
                ]
        );

        $this->add_control(
                'single_info_tag',
                [
                    'label' => esc_html__('HTML Tag', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                        'div' => 'div',
                        'span' => 'span',
                        'p' => 'p',
                        'template' => 'TEMPLATE',
                    ],
                    'default' => 'h2',
                    'condition' => [
                        'url!' => '',
                        'single_or_archive' => 'single'
                    ],
                ]
        );
        $this->add_control(
                'single_template', [
            'label' => esc_html__('Single Template', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'default' => '<h2 class="e-rss-title elementor-heading-title">[info]</h2>',
            'description' => 'Set a Custom Template for single info data. Use Twig to represent data fields, using "channel" as var (ex: "{{channel.copyright}}").',
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'single',
                'single_info_tag' => 'template',
            ],
                ]
        );

        $this->add_control(
                'data_before', [
            'label' => esc_html__('Before the Archive', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'default' => '<div class="rss-wrapper">',
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive',
            ],
                ]
        );
        $this->add_control(
                'data_template', [
            'label' => esc_html__('News Template', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'default' => '<div class="rss-content"><h3 class="e-rss-title elementor-heading-title">[title]</h3><div class="e-rss-description">[description]</div><div class="elementor-button-wrapper"><a class="e-rss-button elementor-button" href="[link]">Read more</a></div></div>',
            'description' => esc_html__('Set a Custom Template for response data. Use Twig to represent more data fields, using "rss" as var (example: "{{rss.PubDate}}").', 'e-addons'),
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive'
            ],
                ]
        );
        $this->add_control(
                'data_after', [
            'label' => esc_html__('After the Archive', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'default' => '</div>',
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive',
            ],
                ]
        );
        $this->add_control(
                'limit_contents', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'description' => esc_html__('Limit results for a specific amount. Set 0 or empty for unlimited.', 'e-addons'),
            'min' => 0,
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive'
            ],
                ]
        );

        $this->add_control(
                'offset_contents', [
            'label' => esc_html__('Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'description' => esc_html__('Set 0 or empty to start from the first.', 'e-addons'),
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive'
            ],
                ]
        );

        $this->add_cache_options();

        $this->end_controls_section();

        // STYLE

        $this->start_controls_section(
                'section_block_style',
                [
                    'label' => esc_html__('Block', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'single_or_archive' => 'archive',
                    ]
                ]
        );

        $this->add_responsive_control(
                'rss_margin',
                [
                    'label' => esc_html__('Margin', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rss-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .rss-wrapper' => 'margin: -{{TOP}}{{UNIT}} -{{RIGHT}}{{UNIT}} -{{BOTTOM}}{{UNIT}} -{{LEFT}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_responsive_control(
                'rss_padding',
                [
                    'label' => esc_html__('Padding', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rss-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $gallery_columns = range(1, 10);
        $gallery_columns = array_combine($gallery_columns, $gallery_columns);

        $this->add_responsive_control(
                'rss_columns',
                [
                    'label' => esc_html__('Columns', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 4,
                    'options' => $gallery_columns,
                    'selectors' => [
                        '{{WRAPPER}} .rss-item' => 'width: calc(100% / {{VALUE}}); flex: 0 1 calc( 100% / {{VALUE}} );',
                        '{{WRAPPER}} .rss-wrapper' => 'display: flex; flex-wrap: wrap;', // . $columns_margin,
                    ],
                ]
        );

        /* $columns_margin = is_rtl() ? '0 0 -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}};' : '0 -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} 0;';
          $columns_padding = is_rtl() ? '0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};' : '0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;';
          $this->add_control(
          'rss_spacing_custom',
          [
          'label' => esc_html__('Spacing', 'elementor'),
          'type' => Controls_Manager::SLIDER,
          'show_label' => false,
          'range' => [
          'px' => [
          'max' => 100,
          ],
          ],
          'selectors' => [
          '{{WRAPPER}} .rss-item' => 'padding:' . $columns_padding,
          '{{WRAPPER}} .rss-wrapper' => 'margin: ' . $columns_margin,
          ],
          ]
          ); */

        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'rss_border',
                    'selector' => '{{WRAPPER}} .rss-content',
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'rss_border_radius',
                [
                    'label' => esc_html__('Border Radius', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .rss-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'section_title_style',
                [
                    'label' => esc_html__('Title', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_responsive_control(
                'title_margin',
                [
                    'label' => esc_html__('Margin', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-heading-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_responsive_control(
                'title_align',
                [
                    'label' => esc_html__('Alignment', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left', 'elementor'),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__('Center', 'elementor'),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__('Right', 'elementor'),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-heading-title' => 'text-align: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'title_color',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                    ],
                    'selector' => '{{WRAPPER}} .elementor-heading-title',
                ]
        );

        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'title_text_shadow',
                    'selector' => '{{WRAPPER}} .elementor-heading-title',
                ]
        );

        $this->add_control(
                'blend_mode',
                [
                    'label' => esc_html__('Blend Mode', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Normal', 'elementor'),
                        'multiply' => 'Multiply',
                        'screen' => 'Screen',
                        'overlay' => 'Overlay',
                        'darken' => 'Darken',
                        'lighten' => 'Lighten',
                        'color-dodge' => 'Color Dodge',
                        'saturation' => 'Saturation',
                        'color' => 'Color',
                        'difference' => 'Difference',
                        'exclusion' => 'Exclusion',
                        'hue' => 'Hue',
                        'luminosity' => 'Luminosity',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-heading-title' => 'mix-blend-mode: {{VALUE}}',
                    ],
                    'separator' => 'none',
                ]
        );

        $this->end_controls_section();

        
        // Elementor Free Text Editor Widget Style
        // /elementor/includes/widgets/text-editor.php:177
        $this->start_controls_section(
                'section_description_style',
                [
                    'label' => esc_html__('Description', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'single_or_archive' => 'archive',
                    ]
                ]
        );
        $this->add_responsive_control(
                'description_margin',
                [
                    'label' => esc_html__('Margin', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .e-rss-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_responsive_control(
                'description_align',
                [
                    'label' => esc_html__('Alignment', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left', 'elementor'),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__('Center', 'elementor'),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__('Right', 'elementor'),
                            'icon' => 'eicon-text-align-right',
                        ],
                        'justify' => [
                            'title' => esc_html__('Justified', 'elementor'),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .e-rss-description' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'text_color',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .e-rss-description' => 'color: {{VALUE}};',
                    ],
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'description_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                    'selector' => '{{WRAPPER}} .e-rss-description',
                ]
        );
        $this->end_controls_section();

        // Elementor Free Button Widget Style
        // /elementor/includes/widgets/button.php
        $this->start_controls_section(
                'section_button_style',
                [
                    'label' => esc_html__('Button', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'single_or_archive' => 'archive',
                    ]
                ]
        );
        $this->add_responsive_control(
                'button_align',
                [
                    'label' => esc_html__('Alignment', 'elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left', 'elementor'),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__('Center', 'elementor'),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__('Right', 'elementor'),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button-wrapper' => 'text-align: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'button_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .elementor-button',
                ]
        );
        $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'text_shadow',
                    'selector' => '{{WRAPPER}} .elementor-button',
                ]
        );
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab(
                'tab_button_normal',
                [
                    'label' => esc_html__('Normal', 'elementor'),
                ]
        );
        $this->add_control(
                'button_text_color',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'background',
                    'label' => esc_html__('Background', 'elementor'),
                    'types' => ['classic', 'gradient'],
                    'exclude' => ['image'],
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                        'color' => [
                            'global' => [
                                'default' => Global_Colors::COLOR_ACCENT,
                            ],
                        ],
                    ],
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
                'tab_button_hover',
                [
                    'label' => esc_html__('Hover', 'elementor'),
                ]
        );
        $this->add_control(
                'hover_color',
                [
                    'label' => esc_html__('Text Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'button_background_hover',
                    'label' => esc_html__('Background', 'elementor'),
                    'types' => ['classic', 'gradient'],
                    'exclude' => ['image'],
                    'selector' => '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                    ],
                ]
        );
        $this->add_control(
                'button_hover_border_color',
                [
                    'label' => esc_html__('Border Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
                    ],
                ]
        );
        $this->add_control(
                'hover_animation',
                [
                    'label' => esc_html__('Hover Animation', 'elementor'),
                    'type' => Controls_Manager::HOVER_ANIMATION,
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border',
                    'selector' => '{{WRAPPER}} .elementor-button',
                    'separator' => 'before',
                ]
        );
        $this->add_control(
                'border_radius',
                [
                    'label' => esc_html__('Border Radius', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'button_box_shadow',
                    'selector' => '{{WRAPPER}} .elementor-button',
                ]
        );
        $this->add_responsive_control(
                'button_margin',
                [
                    'label' => esc_html__('Margin', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
        );
        $this->add_responsive_control(
                'text_padding',
                [
                    'label' => esc_html__('Padding', 'elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        if ($settings['url']) {
            $url = $settings['url'];

            if (filter_var($url, FILTER_VALIDATE_URL)) {

                $content = $this->maybe_get_cache();

                if ($content !== false && !is_wp_error($content)) {

                    //$content = str_replace('https', 'http', $content); // remove ssl
                    //$rss = simplexml_load_string($content);
                    //$rss = new \SimpleXMLElement($content);
                    $content = trim($content);
                    if (substr($content, 0, 1) == '<') {
                        $rss = simplexml_load_string($content, null, LIBXML_NOCDATA);
                        if ($rss) {
                            $namespaces = $rss->getNamespaces(true);
                            //var_dump($namespaces);
                            //echo '<pre>';var_dump($namespaces);echo '</pre>';
                            //$array_data = json_encode($array_data);
                            //$array_data = json_decode($array_data, true);
                            //echo '<pre>'; var_dump($rss->channel->item); echo '</pre>';

                            $response = array();
                            if (!empty($rss->channel)) {
                                if (!empty($settings['single_or_archive']) && $settings['single_or_archive'] == 'single') {
                                    if (!empty($rss->channel->{$settings['single_info']})) {
                                        $response = reset($rss->channel->{$settings['single_info']});
                                        if ($settings['single_info_tag'] == 'template') {
                                            $response = str_replace('[info]', $response, $settings['single_template']);
                                            $response = Utils::get_dynamic_data($response, $rss->channel, 'channel');
                                            echo $response;
                                        } else {
                                            echo '<' . $settings['single_info_tag'] . ' class="e-rss-title elementor-heading-title">' . $response . '</' . $settings['single_info_tag'] . '>';
                                        }
                                    }
                                } else {
                                    if (!empty($rss->channel)) {
                                        foreach ($rss->channel->item as $item) {
                                            //var_dump($node->children('media',true)->group); die();
                                            $medias = array();
                                            if (!empty($item->children('media', true)->group)) {
                                                $medias = $item->children('media', true)->group;
                                            } else {
                                                if (!empty($item->children('media', true))) {
                                                    $medias[] = $item->children('media', true);
                                                }
                                            }

                                            $node = $item;
                                            $item = (array) $item;

                                            //var_dump($item);
                                            if (!empty($medias)) {
                                                foreach ($medias as $media) {
                                                    if (empty($media->content->attributes())) {
                                                        $item['media'][] = $media;
                                                    } else {
                                                        $tmp = (array) $media->content->attributes();
                                                        //var_dump($tmp); die();
                                                        if (!empty($tmp["@attributes"])) {
                                                            $item['media'][] = $tmp["@attributes"];
                                                        }
                                                    }
                                                }
                                            }
                                            //var_dump($item['media']);
                                            $data = $settings['data_template'];
                                            $data = str_replace('[title]', $item['title'], $data);
                                            $description = empty($item['description']) ? '' : $item['description'];
                                            $data = str_replace('[description]', $description, $data);
                                            $data = str_replace('[link]', $item['link'], $data);

                                            //$item['media'] = $medias;
                                            $item['image'] = !empty($item['image']) ? $item['image'] : false;
                                            if (!empty($item['media'])) {
                                                $media = reset($item['media']);
                                                if (!empty($media['url'])) {
                                                    $item['image'] = $media['url'];
                                                    $data = str_replace('[image]', $media['url'], $data);
                                                }
                                            }
                                            $data = str_replace('[image]', '', $data);

                                            if (!empty($namespaces)) {
                                                foreach ($namespaces as $ns => $nsdtd) {
                                                    $item[$ns] = $node->children($nsdtd);
                                                }
                                            }

                                            foreach ($item as $ns => $it) {
                                                if (is_object($it)) {
                                                    $tmp = json_decode(json_encode($it), true);
                                                    $item[$ns] = $tmp;
                                                }
                                                if (isset($item[$ns]['@attributes'])) {
                                                    $item[$ns]['attributes'] = $item[$ns]['@attributes'];
                                                }
                                            }

                                            //echo '<pre>';var_dump($item);echo '</pre>';                                    
                                            $response[] = Utils::get_dynamic_data($data, $item, 'rss');
                                        }

                                        if (!empty($settings['data_before']) && $settings['single_or_archive'] == 'archive') {
                                            echo Utils::get_dynamic_data($settings['data_before']);
                                        }

                                        $showed = 0;
                                        $limit = intval($settings['limit_contents']) + intval($settings['offset_contents']);
                                        foreach ($response as $key => $single) {
                                            if ($limit <= 0 || $showed < $limit) {
                                                if ($key >= $settings['offset_contents']) {
                                                    echo '<div class="rss-item">' . $single . '</div>';
                                                }
                                                $showed++;
                                            }
                                        }

                                        if (!empty($settings['data_after']) && $settings['single_or_archive'] == 'archive') {
                                            echo Utils::get_dynamic_data($settings['data_after']);
                                        }
                                    }
                                }
                            }
                        } else {
                            if (Utils::is_preview()) {
                                esc_html_e('Not a valid RSS', 'e-addons');
                            }
                        }
                    } else {
                        if (Utils::is_preview()) {
                            esc_html_e('Not a valid XML', 'e-addons');
                        }
                    }
                } else {
                    if (Utils::is_preview()) {
                        esc_html_e('Error fetching response. Please check url', 'e-addons');
                    }
                }
            } else {
                if (Utils::is_preview()) {
                    esc_html_e('Sorry, the url is not valid', 'e-addons');
                }
            }
        } else {
            if (Utils::is_preview()) {
                esc_html_e('Add remote url of the RSS to begin', 'e-addons');
            }
        }
    }

}
