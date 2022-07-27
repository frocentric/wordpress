<?php

namespace EAddonsDev\Modules\Query\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;
use EAddonsForElementor\Modules\Query\Base\Query as Base_Query;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Query Rest API
 *
 * Elementor widget for E-Addons
 *
 */
class Query_Rest_Api extends Base_Query {

    use Traits\Common;
    use \EAddonsDev\Modules\Remote\Traits\Cache;

    public function get_pid() {
        return 30784;
    }

    public function get_icon() {
        return 'eadd-query-restapi';
    }

    public function get_name() {
        return 'e-query-rest-api';
    }

    public function get_title() {
        return esc_html__('Query Rest API', 'e-addons');
    }

    public function get_categories() {
		return [ 'query-dev' ];
    }

    protected $querytype = 'api';

    protected function register_controls() {
        parent::register_controls();

        $this->controls_dev_common_content();

        $this->start_controls_section(
                'section_query_api', [
            'label' => '<i class="eaddicon eicon-settings" aria-hidden="true"></i> ' . esc_html__('Query', 'e-addons'),
            'tab' => 'e_query',
                ]
        );

        $this->add_control(
                'query_debug', [
            'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show return data for DEBUG', 'e-addons') . '</span>',
            'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->add_control(
                'query_type', [
            'type' => Controls_Manager::HIDDEN,
            'default' => 'automatic_mode',
                ]
        );

        $this->add_control(
                'url', [
            'label' => esc_html__('Endpoint URL', 'e-addons'),
            'description' => esc_html__('The full URL of Endpoint', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'https://wordpress.org/news/wp-json/wp/v2/posts',
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
                '' => esc_html__('Default (GET)'),
                'POST' => esc_html__('POST'),
                'DSL' => esc_html__('DSL (ElasticSearch)'),
                'WP' => esc_html__('WordPress'),
            ],
                ]
        );

        $this->add_control(
                'data_wp_post_type', [
            'label' => esc_html__('Post Type', 'e-addons'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'posts',
            'condition' => [
                'method' => 'WP',
            ],
                ]
        );
        $this->add_control(
                'data_wp_load_fields', [
            'label' => esc_html__('Load Fields', 'e-addons'),
            'type' => \Elementor\Controls_Manager::SELECT2,
            'options' => [
                'author' => esc_html__('Author'),
                'featured_media' => esc_html__('Featured Media'),
                'categories' => esc_html__('Categories'),
                'tags' => esc_html__('Tags'),
            ],
            'multiple' => true,
            'condition' => [
                'method' => 'WP',
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
            'title_field' => '{{{ data_get_key }}} = {{{ data_get_value }}}',
            'prevent_empty' => false,
            'condition' => [
                'method' => ['', 'WP'],
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
            'type' => Controls_Manager::TEXTAREA,
                ]
        );
        $this->add_control(
                'data_post', [
            'label' => esc_html__('Post Arguments', 'e-addons'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_fields->get_controls(),
            'title_field' => '{{{ data_post_key }}} = {{{ data_post_value }}}',
            'prevent_empty' => false,
            'condition' => [
                'method' => 'POST',
            ],
                ]
        );

        // query_string - https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
        // match - https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
        // term - https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html
        // range - https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html

        $this->add_control(
                'data_dsl', [
            'label' => esc_html__('DSL', 'e-addons'),
            'type' => \Elementor\Controls_Manager::CODE,
            'placeholder' => 'GET my-index-000001/_doc/0',
            'condition' => [
                'method' => 'DSL',
            ],
                ]
        );
        $this->add_control(
                'data_quote', [
            'label' => esc_html__('Remove Quote', 'e-addons'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => [
                'method' => 'DSL',
            ],
                ]
        );
        $this->add_control(
                'data_load_id', [
            'label' => esc_html__('Load Document ID Objects', 'e-addons'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => [
                'method' => 'DSL',
            ],
                ]
        );
        $this->add_control(
                'data_load_id_fields', [
            'label' => esc_html__('Load Document from Fields', 'e-addons'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'label_block' => true,
            'condition' => [
                'method' => 'DSL',
                'data_load_id!' => '',
            ],
                ]
        );
        $this->add_control(
                'data_load_id_depth', [
            'label' => esc_html__('Level Depth', 'e-addons'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 1,
            'condition' => [
                'method' => 'DSL',
                'data_load_id!' => '',
            ],
                ]
        );

        $this->add_control(
                'archive_path', [
            'label' => esc_html__('Archive Array Sub path', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'separator' => 'before',
            'placeholder' => 'body.results',
            'description' => esc_html__('Leave empty if json result is a direct array (like in WP Api). For a web service usually you might use "results". You can browse sub arrays separating them with a dot, like "body.people"', 'e-addons'),
                ]
        );

        $this->add_control(
                'rows_per_page', [
            'label' => esc_html__('Rows per Page', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'description' => esc_html__('Limit results for a specific amount. Set -1 for unlimited.', 'e-addons'),
            'min' => -1,
                ]
        );

        $this->add_control(
                'offset', [
            'label' => esc_html__('Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'description' => esc_html__('Set 0 or empty to start from the first.', 'e-addons'),
            'condition' => [
                'rows_per_page!' => 1,
            ],
                ]
        );
        $this->add_control(
                'limit', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'condition' => [
                'query_type' => ['get_cpt', 'automatic_mode'],
                'rows_per_page!' => 1,
            ],
                ]
        );

        $this->add_cache_options();

        $this->end_controls_section();

        $this->add_no_result_section();
    }

    // La QUERY
    public function query_the_elements() {

        $response = $this->maybe_get_cache();

        // DEBUG
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty($this->get_settings_for_display('query_debug'))) {
                echo '<pre>';
                $data = json_decode($response, true);
                var_dump($data);
                echo '</pre>';
            }
        }

        $this->query = $response;
    }

    public function loop($skin, $query) {

        $settings = $skin->parent->get_settings_for_display();

        $array_data = json_decode($query, true);
        //var_dump($array_data);
        //echo '<pre>';var_dump($array_data);echo '</pre>';

        $results = $archive = array();
        if (empty($settings['archive_path'])) {
            $archive = $array_data;
        } else {
            $tmp = explode('.', $settings['archive_path']);
            $sub_data = Utils::get_array_value($array_data, $tmp);
            if ($sub_data) {
                $archive = $sub_data;
            }
        }
        //var_dump($archive);
        if ($settings['rows_per_page'] == 1) {
            if (count($archive) > 1) {
                $archive = array($archive);
            }
        }
        if (!empty($archive)) {
            foreach ($archive as $data) {
                $results[] = $data;
            }
        }


        if (!empty($settings['data_quote'])) {
            $results = Utils::remove_quotes($results);
        }

        $i = $j = 0;
        $paged = $skin->parent->get_current_page();
        $rows_per_page = empty($settings['rows_per_page']) ? get_option('posts_per_page') : intval($settings['rows_per_page']);

        $start = 0;
        $stop = count($results);
        if ($settings['method'] != 'WP') {
            $start = $rows_per_page * ($paged - 1);
            $stop = $start + $rows_per_page;
        }
        if ($rows_per_page < 0) {
            $stop = count($results);
        }

        if ($settings['method'] == 'DSL' || $settings['data_dsl']) {
            // pagination managed by DSL
            $start = 0;
            $stop = count($results);
        }

        $offset = intval($settings['offset']);
        $limit = intval($settings['limit']);
        //var_dump($offset); var_dump($limit);
        foreach ($results as $key => $row) {
            if ($start <= $key && $stop > $key) {
                $i++;
                $continue = false;
                if ($limit) {
                    if ($offset) {
                        if ($i <= $offset) {
                            $continue = true;
                        }
                    }
                    if (!$continue) {
                        $j++;
                    }
                    if ($j > $limit) {
                        $continue = true;
                    }
                }
                if (!$continue) {
                    //$skin->current_permalink = get_permalink();
                    $skin->current_id = $key;
                    $skin->current_data = $row;
                    $skin->render_element_item();
                }
            }
        }
    }

    public function should_render($render, $skin, $query) {
        //$results = $skin->parent->maybe_get_cache();
        //if (empty($results)) {
        //var_dump($query);
        if (empty($query)) {
            return false;
        }

        $array_data = json_decode($query, true);
        //var_dump($array_data);
        //echo '<pre>';var_dump($array_data);echo '</pre>';
        // DSL
        if (isset($array_data['hits']['total']['value'])) {
            return $array_data['hits']['total']['value'];
        }

        $archive = array();
        $archive_path = $skin->parent->get_settings_for_display('archive_path');
        if (empty($archive_path)) {
            $archive = $array_data;
        } else {
            $tmp = explode('.', $archive_path);
            $sub_data = Utils::get_array_value($array_data, $tmp);
            if ($sub_data) {
                $archive = $sub_data;
            }
        }

        return !empty($archive);
    }

    public function pagination__page_limit($page_limit, $skin, $query, $settings) {
        //$no = $settings['rows_per_page'];
        $no = empty($settings['rows_per_page']) ? get_option('posts_per_page') : intval($settings['rows_per_page']);
        if ($no && !empty($query)) {
            $array_data = json_decode($query, true);
            if ($array_data) {
                $results = array();
                $archive = $array_data;
                if (!empty($settings['archive_path'])) {
                    $tmp = explode('.', $settings['archive_path']);
                    $sub_data = Utils::get_array_value($archive, $tmp);
                    if ($sub_data) {
                        $archive = $sub_data;
                    }
                }

                $total_rows = count($archive);
                if ($settings['method'] == 'WP') {
                    if (!empty(self::$headers[$settings['url']]['x-wp-totalpages'])) {
                        return self::$headers[$settings['url']]['x-wp-totalpages'];
                    } else if (!empty(self::$headers[$settings['url']]['x-wp-total'])) {
                        $total_rows = self::$headers[$settings['url']]['x-wp-total'];
                    }
                }
                if ($settings['method'] == 'DSL') {
                    if (!empty($array_data['hits']['total']['value'])) {
                        $total_rows = $array_data['hits']['total']['value'];
                    }
                }

                $page_limit = ceil($total_rows / $no);
            }
        }
        return $page_limit;
    }

}
