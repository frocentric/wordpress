<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Label extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/item_types', [$this, 'register']);
    }

    public function get_name() {
        return 'item_label';
    }

    public function get_title() {
        return esc_html__('HTML', 'e-addons');
    }

    public function add_controls($target, $type = '') {
        if (!$type) {
            if ($target instanceof \EAddonsForElementor\Modules\Query\Base\Query) {
                $type = $target->get_querytype();
            }
        }
        //
        // +********************* LabelHtml
        $target->add_control(
                'label_html_type', [
            'label' => esc_html__('Label Type', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'label_block' => false,
            'options' => [
                'text' => [
                    'title' => esc_html__('Text', 'e-addons'),
                    'icon' => 'fas fa-font',
                ],
                'image' => [
                    'title' => esc_html__('Image', 'e-addons'),
                    'icon' => 'fas fa-image',
                ],
                'icon' => [
                    'title' => esc_html__('Icon', 'e-addons'),
                    'icon' => 'fas fa-icons',
                ],
                'wysiwyg' => [
                    'title' => esc_html__('Wysiwyg', 'e-addons'),
                    'icon' => 'fas fa-align-justify',
                ],
                'code' => [
                    'title' => esc_html__('Code', 'e-addons'),
                    'icon' => 'fas fa-code',
                ],
            ],
            'default' => 'code',
            'condition' => [
                'item_type' => $this->get_name(),
            ]
                ]
        );

        $target->add_control(
                'label_html_image',
                [
                    'label' => esc_html__('Image', 'e-addons'),
                    'type' => Controls_Manager::MEDIA,
                    'default' => [
                        'url' => '',
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => $this->get_name(),
                            ],
                            [
                                'name' => 'label_html_type',
                                'value' => 'image',
                            ]
                        ]
                    ]
                ]
        );

        $target->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'label_html_image_size',
            'label' => esc_html__('Image Format', 'e-addons'),
            'default' => 'large',
            'condition' => [
                'item_type' => $this->get_name(),
                'label_html_type' => 'image',
                'label_html_image[url]!' => '',
            ]
                ]
        );
        $target->add_responsive_control(
                'label_html_image_width', [
            'label' => esc_html__('Image Width', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%', 'px'],
            'range' => [
                '%' => [
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
                '{{WRAPPER}} {{CURRENT_ITEM}}.e-add-item_label img' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'item_type' => $this->get_name(),
                'label_html_type' => 'image',
                'label_html_image[url]!' => '',
            ]
                ]
        );
        $target->add_control(
                'label_html_icon',
                [
                    'label' => esc_html__('Icon', 'elementor'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => '',
                        'library' => 'fa-solid',
                    ],
                    'skin' => 'inline',
                    'label_block' => false,
                    'fa4compatibility' => 'labelicon',
                    'condition' => [
                        'item_type' => $this->get_name(),
                        'label_html_type' => 'icon',
                    ]
                ]
        );

        $target->add_control(
                'label_html_text',
                [
                    'label' => 'Text Label',
                    'type' => Controls_Manager::TEXT,
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => $this->get_name(),
                            ],
                            [
                                'name' => 'label_html_type',
                                'value' => 'text',
                            ]
                        ]
                    ]
                ]
        );

        $target->add_control(
                'label_html_code',
                [
                    'label' => 'Html Label',
                    'type' => Controls_Manager::CODE,
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => $this->get_name(),
                            ],
                            [
                                'name' => 'label_html_type',
                                'value' => 'code',
                            ]
                        ]
                    ]
                ]
        );

        $target->add_control(
                'label_html_wysiwyg',
                [
                    'label' => esc_html__('Wysiwyg Label', 'elementor'),
                    'type' => Controls_Manager::WYSIWYG,
                    'default' => esc_html__('', 'elementor'),
                    'show_label' => false,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'value' => $this->get_name(),
                            ],
                            [
                                'name' => 'label_html_type',
                                'value' => 'wysiwyg',
                            ]
                        ]
                    ]
                ]
        );
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        // Settings ------------------------------
        $label_html_type = $settings['label_html_type'];

        if (!empty($label_html_type)) {
            switch ($label_html_type) {
                case 'text':
                    $html_label = $settings['label_html_text'];

                    break;
                case 'image':
                    $setting_key = $settings['label_html_image_size_size'];
                    $image_id = $settings['label_html_image']['id'];
                    $image_attr = [
                        'class' => $skin->get_image_class()
                    ];
                    $html_label = wp_get_attachment_image($image_id, $setting_key, false, $image_attr);
                    break;
                case 'icon':

                    $html_label = $skin->render_item_icon($settings, 'label_html_icon', 'labelicon', 'e-add-query-icon');

                    break;
                case 'code':
                    $html_label = $settings['label_html_code'];

                    break;
                case 'wysiwyg':
                    $html_label = $settings['label_html_wysiwyg'];

                    break;
            }
            
            
            $html_label = $skin->get_dynamic_data($html_label, $widget);

            $use_link = $skin->get_item_link($settings);

            if (!empty($html_label)) {
                if ($use_link) {
                    $attribute_link = ' href="' . $use_link . '"';

                    $attribute_target = '';
                    if (!empty($settings['blanklink_enable']))
                        $attribute_target = ' target="_blank"';

                    echo '<a class="e-add-labelstatic"' . $attribute_link . $attribute_target . '>';
                } else {
                    echo '<span class="e-add-labelstatic">';
                }
                echo $html_label;
                if ($use_link) {
                    echo '</a>';
                } else {
                    echo '</span>';
                }
            }
        }
    }

}
