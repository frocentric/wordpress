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

class Field extends Base_Tag {
    
    use \EAddonsForElementor\Modules\User\Traits\Users;

    public function get_name() {
        return 'e-tag-user-field';
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-user-field';
    }

    public function get_pid() {
        return 7450;
    }

    public function get_title() {
        return esc_html__('User Field', 'e-addons');
    }

    public function get_group() {
        return 'user';
    }

    public static function _group() {
        return self::_groups('user');
    }

    /*
    public function get_categories() {
        return [
            'base', //\Elementor\Modules\DynamicTags\Module::BASE_GROUP
            'text', //\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            'url', //\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
        ];
    }
     * 
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
                'tag_field',
                [
                    'label' => esc_html__('Field', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'groups' => [
                        [
                            'label' => esc_html__('Custom', 'e-addons'),
                            'options' => [
                                '' => esc_html__('Custom', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Common', 'e-addons'),
                            'options' => [
                                'display_name' => esc_html__('Display Name', 'e-addons'),
                                'description' => esc_html__('Description (Bio)', 'e-addons'),
                                'user_login' => esc_html__('Login', 'e-addons'),
                                'user_email' => esc_html__('Email', 'e-addons'),
                                'user_url' => esc_html__('Url (Website)', 'e-addons'),
                                'user_registered' => esc_html__('Registered', 'e-addons'),
                                'roles' => esc_html__('Roles', 'e-addons'),                                
                            ],
                        ],
                        [
                            'label' => esc_html__('Link', 'e-addons'),
                            'options' => [
                                "link" => esc_html__('Link (to Posts Archive)', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Name', 'e-addons'),
                            'options' => [
                                'first_name' => esc_html__('First Name', 'e-addons'),
                                'last_name' => esc_html__('Last Name', 'e-addons'),
                                'nickname' => esc_html__('Nickname', 'e-addons'),
                                'user_nicename' => esc_html__('Nicename', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Other', 'e-addons'),
                            'options' => [
                                'ID' => esc_html__('ID', 'e-addons'),
                                'admin_color' => esc_html__('Color', 'e-addons'),
                                'comment_shortcuts' => esc_html__('Comment Shortcuts', 'e-addons'),
                                'user_activation_key' => esc_html__('Activation Key', 'e-addons'),
                                'user_pass' => esc_html__('Password', 'e-addons'),
                                'user_status' => esc_html__('Status', 'e-addons'),
                                'user_level' => esc_html__('Level', 'e-addons'),
                                'plugins_last_view' => esc_html__('Plugins last view', 'e-addons'),
                                'plugins_per_page' => esc_html__('Plugins per page', 'e-addons'),
                                'rich_editing' => esc_html__('Rich Editing', 'e-addons'),
                                'syntax_highlighting' => esc_html__('Syntax Highlighting', 'e-addons'),
                            ],
                        ],
                        [
                            'label' => esc_html__('Social (deprecated)', 'e-addons'),
                            'options' => [
                                'aim' => esc_html__('AIM', 'e-addons'),
                                'yim' => esc_html__('YIM', 'e-addons'),
                                'jabber' => esc_html__('Jabber', 'e-addons'),
                            ],
                        ],
                    //'user_description' => esc_html__('Description (Bio)', 'e-addons'),
                    //'user_firstname' => esc_html__('First Name', 'e-addons'),
                    //'user_lastname' => esc_html__('Last Name', 'e-addons'),
                    ],
                //'options' => [],
                //'default' => 'display_name',
                //'label_block' => true,
                ]
        );

        $this->add_control(
                'custom',
                [
                    'label' => esc_html__('Custom Meta', 'elementor'),
                    'type' => 'e-query',
                    'select2options' => ['tags' => true],
                    'placeholder' => esc_html__('Meta key or Field Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'user',
                    'condition' => [
                        'tag_field' => '',
                    ]
                ]
        );
        
        $this->add_source_controls();        

        Utils::add_help_control($this);
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;

        $user_id = $this->get_user_id();


        if (!empty($settings['tag_field'])) {
            switch ($settings['tag_field']) {
                case 'link':
                    $meta = get_author_posts_url($user_id);
                    break;
                case 'roles':
                    global $wp_roles;
                    //var_dump($wp_roles); die();
                    $user = get_userdata($user_id);
                    $roles = (array) $user->roles;
                    $meta = array();
                    if (!empty($roles)) {
                        foreach ($roles as $role) {
                            //$orole = get_role($role);
                            if (empty($wp_roles->roles[$role]['name'])) {
                                $meta[] = $role;
                            } else {
                                $meta[] = $wp_roles->roles[$role]['name'];
                            }
                        }
                    }
                    break;
                default:
                    $meta = get_the_author_meta($settings['tag_field'], $user_id);
            }
        } else {
            $meta = Utils::get_user_field($user_id, $settings['custom']);
        }

        echo Utils::to_string($meta);
    }

}
