<?php

namespace EAddonsForElementor\Modules\User\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Avatar extends Base_Tag {
    
    use \EAddonsForElementor\Modules\User\Traits\Users;

    public $is_data = true;

    public function get_name() {
        return 'e-tag-user-avatar';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-user-avatar';
    }
    
    public function get_pid() {
        return 7450;
    }

    public function get_title() {
        return esc_html__('User Avatar', 'e-addons');
    }

    public function get_group() {
        return 'user';
    }
    public static function _group() {
        return self::_groups('user');
    }

    public function get_categories() {
        return [
            //'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'image', //\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
        ];
    }

    /**
     * @since 2.0.0
     * @access protected
     */
    protected function register_advanced_section() {
        
    }

    /*
     * @param array $args {
     *     Optional. Arguments to return instead of the default arguments.
     *
     *     @type int    $size           Height and width of the avatar in pixels. Default 96.
     *     @type string $default        URL for the default image or a default type. Accepts '404' (return
     *                                  a 404 instead of a default image), 'retro' (8bit), 'monsterid' (monster),
     *                                  'wavatar' (cartoon face), 'indenticon' (the "quilt"), 'mystery', 'mm',
     *                                  or 'mysteryman' (The Oyster Man), 'blank' (transparent GIF), or
     *                                  'gravatar_default' (the Gravatar logo). Default is the value of the
     *                                  'avatar_default' option, with a fallback of 'mystery'.
     *     @type bool   $force_default  Whether to always show the default image, never the Gravatar. Default false.
     *     @type string $rating         What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are
     *                                  judged in that order. Default is the value of the 'avatar_rating' option.
     *     @type string $scheme         URL scheme to use. See set_url_scheme() for accepted values.
     *                                  Default null.
     *     @type array  $processed_args When the function returns, the value will be the processed/sanitized $args
     *                                  plus a "found_avatar" guess. Pass as a reference. Default null.
     * }
     */

    /**
     * Register Controls
     *
     * Registers the Dynamic tag controls
     *
     * @since 2.0.0
     * @access protected
     *
     * @return void
     */
    protected function register_controls() {

        $this->add_control(
                'gravatar',
                [
                    'label' => esc_html__('Use Gravatar', 'elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'description' => esc_html__('Use the Gravatar service, used by default in Wordpress.').' <a target="_blank" href="https://en.gravatar.com/site/implement/images/">'.esc_html__('Read documentation for extra options').'</a>',
                ]
        );
        $this->add_control(
                'gravatar_default',
                [
                    'label' => esc_html__('Default', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'e-addons'),
                        'retro' => esc_html__('8bit', 'e-addons'),
                        'monsterid' => esc_html__('Monster', 'e-addons'),
                        'wavatar' => esc_html__('Cartoon face', 'e-addons'),
                        'identicon' => esc_html__('Geometric pattern', 'e-addons'),
                        'mp' => esc_html__('Mistery Person', 'e-addons'),
                        'robohash' => esc_html__('Robohash', 'e-addons'),
                        'blank' => esc_html__('Transparent GIF', 'e-addons'),
                        'gravatar_default' => esc_html__('Gravatar logo', 'e-addons'),
                    ],
                    'condition' => [
                        'gravatar!' => '',
                    ]
                ]
        );

        $this->add_control(
                'gravatar_force_default',
                [
                    'label' => esc_html__('Force Default', 'elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'gravatar!' => '',
                    ]
                ]
        );

        $this->add_control(
                'gravatar_size',
                [
                    'label' => esc_html__('Size', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 2048,
                        ],
                    ],
                    'condition' => [
                        'gravatar!' => '',
                    ]
                ]
        );

        $this->add_control(
                'gravatar_rating',
                [
                    'label' => esc_html__('Rating', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'e-addons'),
                        'G' => esc_html__('G', 'e-addons'),
                        'PG' => esc_html__('PG', 'e-addons'),
                        'R' => esc_html__('R', 'e-addons'),
                        'X' => esc_html__('X', 'e-addons'),
                    ],
                    'condition' => [
                        'gravatar!' => '',
                    ]
                ]
        );

        $this->add_control(
                'custom_avatar',
                [
                    'label' => esc_html__('Custom Meta', 'elementor'),
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'user',
                    'condition' => [
                        'gravatar' => '',
                    ]
                ]
        );

        $this->add_control(
                'custom_fallback_img',
                [
                    'label' => esc_html__('Fallback', 'elementor'),
                    'type' => Controls_Manager::MEDIA,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                    'condition' => [
                        'gravatar' => '',
                    ]
                ]
        );
        
        $this->add_source_controls();

        Utils::add_help_control($this);
    }

    public function get_value(array $options = []) {
        $settings = $this->get_settings();
        if (empty($settings))
            return;
        
        $user_id = $this->get_user_id();
        if (!$user_id) return;
        
        $id = '';
        $url = '';
        if ($user_id) {
            if ($settings['gravatar']) {
                $args = array();
                if (!empty($settings['gravatar_default'])) {
                    $args['default'] = $settings['gravatar_default'];
                }
                if (!empty($settings['gravatar_force_default'])) {
                    $args['force_default'] = (bool) $settings['gravatar_force_default'];
                }
                if (!empty($settings['gravatar_rating'])) {
                    $args['rating'] = $settings['gravatar_rating'];
                }
                if (!empty($settings['gravatar_size']['size'])) {
                    $args['size'] = $settings['gravatar_size']['size'];
                }
                $url = get_avatar_url($user_id, $args);
            } else {
                // custom field
                $meta = Utils::get_user_field($user_id, $settings['custom_avatar']);
                //var_dump($meta);
                $img = Utils::get_image($meta);
                if (!Utils::empty($img) && !empty($img['url'])) {
                    if (!empty($img['id'])) {
                        $id = $img['id'];
                    }
                    $url = $img['url'];
                } else {
                    if (!empty($settings['custom_fallback_img']['url'])) {
                        $id = $settings['custom_fallback_img']['id'];
                        $url = $settings['custom_fallback_img']['url'];
                    }
                }
            }
        }

        return [
            'id' => $id,
            'url' => $url,
        ];
    }

}
