<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Image extends Base_Item {

    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/item_types', [$this, 'register']);
        add_filter('e_addons/query/item_controls', [$this, 'add_controls']);
    }

    public function get_name() {
        return 'item_image';
    }

    public function get_title() {
        return esc_html__('Image', 'e-addons');
    }

    public function add_controls($target, $type = '') {
        if (!$type) {
            if ($target instanceof \EAddonsForElementor\Modules\Query\Base\Query) {
                $type = $target->get_querytype();
            }
        }
        
        //@p se mi trovo in post scelgo tra Featured o Custom image 
        //@p se mi trovo in user scelgo tra Avatar o Custom image
        //@p se mi trovo in repeater scego il subField repeater
        switch ($type) {
            case 'product':
            case 'post':
            case 'attachment':
            case 'user':
                //@p questa è solo l'etichetta string
                if (in_array($type, ['post', 'product', 'attachment'])) {
                    $defIm = 'featured';
                    $type = 'post';
                } else if ($type == 'user') {
                    $defIm = 'avatar';
                }

                $target->add_control(
                        'image_type', [
                    'label' => esc_html__('Image type', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'featuredimage' => esc_html__(ucfirst($defIm . ' image'), 'e-addons'),
                        'customimage' => esc_html__('Custom meta image', 'e-addons'),
                    ],
                    'default' => $defIm . 'image',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                            ]
                        ]
                    ]
                        ]
                );

                $target->add_control(
                        'image_custom_metafield', [
                    'label' => esc_html__('Image Meta Field', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Meta key', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => $type,
                    'separator' => 'after',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                            ],
                            [
                                'name' => 'image_type',
                                'value' => 'customimage'
                            ]
                        ]
                    ]
                        ]
                );
                break;

            case 'term':
                //@p altrimeti in termine è solo la custom
                $target->add_control(
                        'image_custom_metafield', [
                    'label' => esc_html__('Image Meta Field', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => esc_html__('Meta key', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => $type,
                    'separator' => 'after',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_image', 'item_imageoricon']
                            ]
                        ]
                    ]
                        ]
                );
                break;

            case 'repeater':
                break;
            
            default:
                //var_dump($type); die();
                $target->add_control(
                        'image_custom_metafield', [
                    'label' => esc_html__('Image Custom Field', 'e-addons'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'separator' => 'after',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'item_type',
                                'operator' => 'in',
                                'value' => ['item_image', 'item_imageoricon']
                            ]
                        ]
                    ]
                        ]
                );
        }

        $target->add_control(
                'image_content_heading', [
            'type' => Controls_Manager::RAW_HTML,
            'show_label' => false,
            'raw' => '<i class="fas fa-image"></i> <b>' . esc_html__('Image', 'e-addons') . '</b>',
            'content_classes' => 'e-add-inner-heading',
            'separator' => 'before',
            'condition' => [
                'item_type' => 'item_imageoricon'
            ]
                ]
        );
        
        $target->add_control(
                    'image_alt', [
                'label' => esc_html__('Image Custom Alt', 'e-addons'),
                'type' => Controls_Manager::TEXT,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => 'in',
                            'value' => ['item_avatar', 'item_image']
                        ]
                    ]
                ]
                    ]
            );
        
        $target->add_group_control(
                Group_Control_Image_Size::get_type(), [
            'name' => 'thumbnail_size',
            'label' => esc_html__('Image Format', 'e-addons'),
            'default' => 'large',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon']
                    ]
                ]
            ]
                ]
        );
        if ($type == 'user') {
            $target->add_control(
                    'avatar_size', [
                'label' => esc_html__('Avatar size', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 200,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'item_type',
                            'operator' => 'in',
                            'value' => ['item_avatar', 'item_imageoricon']
                        ]
                    ]
                ]
                    ]
            );
        }

        $target->add_responsive_control(
                'ratio_image', [
            'label' => esc_html__('Image Ratio', 'e-addons'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0.1,
                    'max' => 2,
                    'step' => 0.01
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-img' => 'padding-bottom: calc( {{SIZE}} * 100% );', '{{WRAPPER}}:after' => 'content: "{{SIZE}}"; display: none;',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'value' => '',
                    ]
                ]
            ],
            'dynamic' => [
                'active' => false
            ]
                ]
        );
        $target->add_responsive_control(
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
                'dynamic' => [
                    'active' => false
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}:not(.e-add-timeline) .e-add-post-image, {{WRAPPER}} .e-add-timeline {{CURRENT_ITEM}} .e-add-post-image img' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'use_bgimage', [
            'label' => esc_html__('Background Image', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'render_type' => 'template',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ]
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .e-add-image-area, {{WRAPPER}}.e-add-posts-layout-default .e-add-post-bgimage' => 'position: relative;',
            ],
                ]
        );
        $target->add_responsive_control(
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
                '{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} .e-add-image-area' => 'height: 100%',
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-image.e-add-post-bgimage' => 'height: {{SIZE}}{{UNIT}};'
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_bgimage',
                        'operator' => '!=',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $target->add_control(
                'use_overlay', [
            'label' => esc_html__('Overlay', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'prefix_class' => 'overlayimage-',
            'render_type' => 'template',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ]
                ]
            ]
                ]
        );
        $target->add_group_control(
                Group_Control_Background::get_type(), [
            'name' => 'overlay_color',
            'label' => esc_html__('Background', 'e-addons'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-image.e-add-post-overlayimage:after',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
                    [
                        'name' => 'use_overlay',
                        'operator' => '!==',
                        'value' => '',
                    ]
                ]
            ]
                ]
        );
        $target->add_responsive_control(
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
                '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-post-image.e-add-post-overlayimage:after' => 'opacity: {{SIZE}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'item_type',
                        'operator' => 'in',
                        'value' => ['item_image', 'item_imageoricon', 'item_avatar'],
                    ],
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

    public function render($settings, $i, $widget) {
        $skin = $widget->skin;

        $querytype = $widget->get_querytype();
        $image_id = false;
        $image_url = false;
        $thumbnail_html = '';
        //considero se l'immagine è un metavalue invece della featured image

        if (!empty($settings['image_custom_metafield'])) {
            $metakey = $settings['image_custom_metafield'];

            if (in_array($querytype, array('post', 'comment', 'term', 'user'))) {
                $meta_value = get_metadata($querytype, $skin->current_id, $metakey, true);
                if (!empty($meta_value)) {
                    $image_id = Utils::get_id($meta_value);
                }
            } else {
                $tmp = explode('.', $metakey);
                $metakey = array_shift($tmp);
                if (is_array($skin->current_data) && isset($skin->current_data[$metakey])) {
                    $meta_value = $skin->current_data[$metakey];
                    if (is_array($skin->current_data[$metakey]) && !empty($tmp)) {
                        $meta_value = Utils::get_array_value($skin->current_data[$metakey], $tmp);
                    }
                    if ($meta_value == 'null' || $meta_value == 'NULL') {
                        $meta_value = false;
                    }
                    if (intval($meta_value)) {
                        $image_id = $meta_value;
                    } else {
                        $image_url = $meta_value;
                    }
                }
            }
        } else {

            switch ($querytype) {
                case 'attachment':
                    //se mi trovo in media basta l'id dell'attachment
                    $image_id = get_the_ID();
                    break;
                case 'product':
                case 'post':
                    //se mi trovo in post
                    $image_id = get_post_thumbnail_id();
                    break;
                case 'term':
                    //se mi trovo in term (nnativamente non ho immagine )
                    $image_id = ''; //$meta_value;
                    break;
                case 'items':
                    //se mi trovo in item_list
                    $image_id = $skin->current_data['sl_image']['id'];
                    break;
                case 'repeater':
                    //se mi trovo in repeater
                    if (!empty($skin->current_data['item_image_' . $i])) {
                        $image_id = $skin->current_data['item_image_' . $i];
                    }
                    break;
            }
        }

        if (empty($image_id) && empty($image_url)) {
            if (!empty($settings['use_fallback'])) {
                $meta_value = $settings['use_fallback'];
                $image_url = $meta_value;
            }
            if (!empty($settings['use_fallback_img'])) {
                $meta_value = $settings['use_fallback_img'];
                $image_id = $meta_value['id'];
            }
        }

        // Settings ------------------------------
        $use_overlayimg_hover = $skin->get_instance_value('use_overlayimg_hover');
        //
        $bgimage = '';
        if (!empty($settings['use_bgimage'])) {
            $bgimage = ' e-add-post-bgimage';
        }
        $overlayimage = '';
        if (!empty($settings['use_overlay'])) {
            $overlayimage = ' e-add-post-overlayimage';
        }
        $overlayhover = '';
        if ($use_overlayimg_hover) {
            $overlayhover = ' e-add-post-overlayhover';
        }
        //
        // @p definisco se l'immagine è linkata
        $use_link = $skin->get_item_link($settings);

        // ---------------------------------------
        // @p preparo il dato in base a 'thumbnail_size'
        $image_size = empty($settings['thumbnail_size_size']) ? 'full' : $settings['thumbnail_size_size'];

        $image_attr = [
            'class' => $skin->get_image_class()
        ];

        //var_dump($skin->current_data);
        if (is_string($skin->current_data) && filter_var($skin->current_data, FILTER_VALIDATE_URL)) {
            $image_url = $skin->current_data;
        }
        if ($image_url) {
            $thumbnail_html = '<img class="' . $skin->get_image_class() . '" src="' . $image_url . '">';
        }
        
        if ($image_id) {
            $media_thumb = get_post_meta($image_id, '_thumbnail_id', true);
            if ($media_thumb) {
                $image_id = $media_thumb;
            }
        }

        if (!$image_id && !$thumbnail_html) {
            //var_dump($settings['use_fallback_img']);
            if (!empty($settings['use_fallback_img']['id'])) {
                $image_id = $settings['use_fallback_img']['id'];
            }
        }
        
        

        if ($image_id && !$thumbnail_html) {
            // @p questa è l'mmagine via HTML
            /* switch ($querytype) {
              case 'attachment':
              //$use_link = !empty($settings['gallery_link']) ? $skin->get_item_link($settings, $image_id) : '';
              $thumbnail_html = wp_get_attachment_image($image_id, $image_size, true, $image_attr);
              //if ($use_link) {
              //    $thumbnail_html = '<a href="'.$use_link.'" class="e-media-link'.((!empty($settings['open_lightbox']) && $settings['open_lightbox'] != 'no') ? ' elementor-clickable' : '').'">'.$thumbnail_html.'</a>';
              //}
              //echo $thumbnail_html;
              break;
              default:
              //se mi trovo in post
              $thumbnail_html = wp_get_attachment_image($image_id, $image_size, false, $image_attr);
              break;
              } */

            $settings_fake = array(
                'image' => array('id' => $image_id),
                //'thumbnail_size' => $settings['thumbnail_size'],
                'thumbnail_size_size' => $image_size,
                'thumbnail_size_custom_dimension' => empty($settings['thumbnail_size_custom_dimension']) ? false : $settings['thumbnail_size_custom_dimension'],
            );
            $thumbnail_html = wp_kses_post(\Elementor\Group_Control_Image_Size::get_attachment_image_html($settings_fake, 'thumbnail_size', 'image'));
            
            //maybe is a video or a doc?
            if (empty($thumbnail_html)) {
                //$media = get_post($image_id);
                $_wp_attachment_metadata = get_post_meta($image_id, '_wp_attachment_metadata', true);
                if (!empty($_wp_attachment_metadata['mime_type']) && strpos($_wp_attachment_metadata['mime_type'],'video') !== false) {
                    $video_url = get_the_guid($image_id);
                    /*switch ($settings['thumbnail_size_size']) {
                        case 'large':
                            $vwidth = 800;
                            $vheight = 600;
                            // width="'.$vwidth.'" height="'.$vheight.'"
                    }*/
                    $thumbnail_html = '<video class="elementor-video ' . $skin->get_image_class() . '" src="'. esc_attr( $video_url ).'" controls></video>';
                }
            }
            
            if (empty($thumbnail_html)) {
                $image_url = wp_get_attachment_image_src($image_id, $settings['thumbnail_size_size']); // $image_size);
                if (!empty($image_url)) {
                    $thumbnail_html = '<img class="' . $skin->get_image_class() . '" src="' . $image_url[0] . '" width="' . $image_url[1] . '" heigth="' . $image_url[1] . '">';
                }
            }
            
            if (in_array($querytype, array('post', 'product')) && $image_id) {
                $thumbnail_html = apply_filters( 'post_thumbnail_html', $thumbnail_html, $skin->current_id, $image_id, $image_size, ['class' => $skin->get_image_class()] );
            }
            
            // @p [lo lascio come appunto storico.] sarò scemo io ma dopo 3 ore che provo questo in tutti i modi, non funziona, ipotizzo perché il size è un control nel repeater quindi nidificato.
            //$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, $image_size );
        }/* else {
          return;
          } */
        
        if ($thumbnail_html && !empty($settings['image_alt'])) {
            $alt = $skin->get_dynamic_data($settings['image_alt'], $widget);
            $alt = 'alt="'.$alt.'" ';
            if (strpos($thumbnail_html, ' alt="')) {
                list($pre, $more) = explode(' alt="', $thumbnail_html, 2);
                list($alt_old, $next) = explode('"', $more, 2);
                $thumbnail_html = $pre.$alt.$next;
            } else {
                $thumbnail_html = str_replace('<img ', '<img '.$alt , $thumbnail_html);
            }
        }

        $html_tag = 'div';

        $attribute_link = '';
        $attribute_target = '';
        $mediaimage = '';
        if ($use_link) {
            $html_tag = 'a';
            if ($querytype == 'attachment') {
                $use_link = str_replace('?', '%3F', $use_link);
                $tmp = explode('%3F', $use_link);
                $use_link = reset($tmp);
                $mediaimage = ' e-media-link' . ((!empty($settings['open_lightbox']) && $settings['open_lightbox'] != 'no') ? ' elementor-clickable' : '');
                $attribute_link .= ' data-elementor-lightbox-slideshow="all-' . $widget->get_id() . '"';
                if (!empty($settings['open_lightbox']) && $settings['open_lightbox'] == 'no') {
                    $attribute_link .= ' data-elementor-open-lightbox = "no"';
                }
            }
            $attribute_link .= ' href="' . $use_link . '"';
            if (!empty($settings['blanklink_enable'])) {
                $attribute_target = ' target="_blank"';
            }
        }
        $skin->render_img_before();
        echo '<' . $html_tag . ' class="e-add-post-image' . $bgimage . $overlayimage . $overlayhover . $mediaimage . $skin->add_img_class() . '"' . $attribute_link . $attribute_target . '>';
        if (!empty($settings['use_bgimage'])) {
            // @p questa è l'mmagine via URL
            //$image_url = Group_Control_Image_Size::get_attachment_image_src($image_id, 'thumbnail_size', $settings);
            if (!$image_url) {
                $urls = wp_get_attachment_image_src($image_id, $image_size, true);
                $image_url = reset($urls);
            }
            echo '<figure class="e-add-img e-add-bgimage" style="background: url(' . $image_url . ') no-repeat center; background-size: cover; display: block;"></figure>';
        } else {
            if (strpos($thumbnail_html, '<video') === false) {
                $thumbnail_html = '<figure class="e-add-img">' . $thumbnail_html . '</figure>';
            }
            echo $thumbnail_html;
        }

        echo '</' . $html_tag . '>';
        $skin->render_img_after();
    }

}
