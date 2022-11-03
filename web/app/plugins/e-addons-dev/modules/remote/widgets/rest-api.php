<?php

namespace EAddonsDev\Modules\Remote\Widgets;

use Elementor\Controls_Manager;
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
class Rest_Api extends Base_Widget {
    
    use \EAddonsDev\Modules\Remote\Traits\Cache;

    public function get_name() {
        return 'e-rest-api';
    }

    public function get_title() {
        return esc_html__('Rest API', 'e-addons');
    }

    public function get_pid() {
        return 217;
    }

    public function get_description() {
        return esc_html__('Connect to remote REST API', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-remote-rest-api';
    }

    protected function register_controls() {
        $this->start_controls_section(
                'section_rest_api', [
            'label' => esc_html__('Rest API', 'e-addons'),
                ]
        );


        $this->add_control(
                'url', [
            'label' => esc_html__('Endpoint URL', 'e-addons'),
            'description' => esc_html__('The full URL of Endpoint', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'https://e-addons.com/wp-json/wp/v2',
            'label_block' => true,
                ]
        );

        $this->add_remote_options();
        
        $this->add_control(
                'method', [
            'label' => esc_html__('Method', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'label_block' => true,
            'options' => [
                '' => esc_html__('Auto'),
                'GET' => esc_html__('GET'),
                'POST' => esc_html__('POST'),
            ],
                ]
        );
        
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control(
                'data_get_key', [
            'label' => esc_html__('Key', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_fields->add_control(
                'data_get_value', [
            'label' => esc_html__('Value', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $this->add_control(
                'data_get', [
            'label' => esc_html__('Get Arguments', 'e-addons'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_fields->get_controls(),
            'separator' => 'before',
            'title_field' => '{{{ data_get_key }}} = {{{ data_get_value }}}',
            'prevent_empty' => false,
            'condition' => [
                'method' => ['GET'],
            ],
                ]
        );
        
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control(
                'data_post_key', [
            'label' => esc_html__('Key', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_fields->add_control(
                'data_post_value', [
            'label' => esc_html__('Value', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $this->add_control(
                'data_post', [
            'label' => esc_html__('Post Arguments', 'e-addons'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_fields->get_controls(),
            'separator' => 'before',
            'title_field' => '{{{ data_post_key }}} = {{{ data_post_value }}}',
            'prevent_empty' => false,
            'condition' => [
                'method' => ['POST', ''],
            ],
                ]
        );
        
        $this->add_control(
                'query_debug', [
            'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show Remote data for DEBUG', 'e-addons') . '</span>',
            'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->add_control(
                'data_language', [
            'label' => esc_html__('Data Format', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'html' => 'HTML',
                'json' => 'JSON',
                'xml' => 'XML',
            ],
            'default' => 'json',
            'condition' => [
                'url!' => '',
            ],
            'separator' => 'before',
                ]
        );
        
        $this->add_control(
                'data_before', [
            'label' => esc_html__('Before the Archive', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'placeholder' => '<table>, <ul>',
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive',
            ],
                ]
        );
        $this->add_control(
                'data_template', [
            'label' => esc_html__('Data Template', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'default' => '<div class="e-rest-content"><h3 class="e-rest-content-title">{{body.title.rendered}}</h3><div class="e-rest-content-body">{{body.excerpt.rendered}}</div><a class="btn btn-primary" href="{{body.link}}">Read more</a></div>',
            'description' => 'Set a Custom Template for response data. Use Twig to represent data fields. Use "body" as response data var.',
            'condition' => [
                'url!' => '',
            ],
                ]
        );
        $this->add_control(
                'data_after', [
            'label' => esc_html__('After the Archive', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'placeholder' => '</table>, </ul>',
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive',
            ],
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
            //'description' => esc_html__('Is a Single element o an Archive?', 'e-addons'),
            'condition' => [
                'url!' => '',
            ],
                ]
        );

        $this->add_control(
                'archive_path', [
            'label' => esc_html__('Archive Array Sub path', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'body.results',
            'description' => esc_html__('Leave empty if json result is a direct array (like in WP Api). For a web service usually you might use "results". You can browse sub arrays separating them with a dot, like "body.people"', 'e-addons'),
            'condition' => [
                'url!' => '',
                'single_or_archive' => 'archive'
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
    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        if ($settings['url']) {
            $url = $settings['url'];
            
            if (filter_var($url, FILTER_VALIDATE_URL)) {

                $content = $this->maybe_get_cache();
                
                if ($content !== false && !is_wp_error($content)) {

                    //$content = str_replace('https', 'http', $content); // remove ssl

                    $response = $content;

                    switch ($settings['data_language']) {
                        case 'xml':
                            $response = simplexml_load_string($response);
                            $array_data = json_encode($response);
                        case 'json':
                            $array_data = json_decode($response, true);
                            break;
                        default: // html
                            $array_data = $response;
                    }
                    
                    //var_dump($array_data);

                    $response = array();
                    if (empty($settings['single_or_archive']) || $settings['single_or_archive'] == 'single') {
                        $response[] = $array_data;
                    } else {
                        $archive = $array_data;
                        if (!empty($settings['archive_path'])) {
                            $tmp = explode('.', $settings['archive_path']);
                            $sub_data = Utils::get_array_value($archive, $tmp);
                            if ($sub_data) {
                                $archive = $sub_data;
                            }
                        }
                        if (!empty($archive)) {
                            foreach ($archive as $data) {
                                if (!empty($data)) {
                                    $response[] = $data;
                                }
                            }
                        }
                    }

                    foreach ($response as $pkey => $pvalue) {
                        $response[$pkey] = Utils::get_dynamic_data($settings['data_template'], $pvalue, 'body');
                    }

                    if (!empty($settings['data_before']) && $settings['single_or_archive'] == 'archive') {
                        echo Utils::get_dynamic_data($settings['data_before']);
                    }
                    
                    $showed = 0;
                    $limit = intval($settings['limit_contents']) + intval($settings['offset_contents']);
                    foreach ($response as $key => $single) {
                        if ( $limit <= 0 || $showed < $limit) {
                            if ($key >= $settings['offset_contents']) {                                
                                echo $single;
                            }
                            $showed++;
                        }
                    }
                    
                    if (!empty($settings['data_after']) && $settings['single_or_archive'] == 'archive') {
                        echo Utils::get_dynamic_data($settings['data_after']);
                    }
                    
                } else {
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        _e('Error fetching response. Please check url', 'e-addons');
                    }
                }
            } else {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    _e('Sorry, the url is not valid', 'e-addons');
                }
            }
        } else {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                _e('Add remote url of the Endopoint to begin', 'e-addons');
            }
        }
    }
}
