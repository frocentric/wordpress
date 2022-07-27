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
 * Query XML
 *
 * Elementor widget for E-Addons
 *
 */
class Query_Xml extends Base_Query {
    
    use Traits\Common;
    use \EAddonsDev\Modules\Remote\Traits\Cache;

    public function get_pid() {
        return 30767;
    }
    
    public function get_icon() {
        return 'eadd-query-xml';
    }
    
    public function get_name() {
        return 'e-query-xml';
    }

    public function get_title() {
        return esc_html__('Query XML', 'e-addons');
    }

    public function get_categories() {
		return [ 'query-dev' ];
    }

    protected $querytype = 'xml';

    protected function register_controls() {
        parent::register_controls();

        $this->controls_dev_common_content();

        $this->start_controls_section(
                'section_query_xml', [
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
            'placeholder' => 'https://e-addons.com/xml/note.xml',
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

        $this->add_control(
                'archive_path', [
            'label' => esc_html__('Archive Array Sub path', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'separator' => 'before',
            'placeholder' => 'body.results',
            'description' => esc_html__('Leave empty if XML result is a direct array. You can browse sub arrays separating them with a dot, like "body.people"', 'e-addons'),
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
        
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control(
                'where_field',
                array(
                    "type" => Controls_Manager::TEXT,
                    "label" => esc_html__('Column', 'e-addons'),
        ));
        $repeater_fields->add_control(
                'where_operator',
                array(
                    'label' => esc_html__('Operator', 'e-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $this->operator_options,
                    'default' => 'equal',
        ));
        $repeater_fields->add_control(
                'where_value',
                array(
                    "type" => Controls_Manager::TEXT,
                    "label" => esc_html__('Value', 'e-addons'),
                    'condition' => array(
                        'where_operator' => $this->operator_with_value,
                    )
        ));
        $this->add_control(
                'where', [
            'label' => esc_html__('Filter', 'e-addons'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'title_field' => '{{{ where_field }}} {{{ where_operator }}} {{{ where_value }}}',
            'fields' => $repeater_fields->get_controls(),
            'prevent_empty' => false,
            'separator' => 'before',
                ]
        );

        $this->add_control(
                'orderby', [
            'label' => esc_html__('Order By', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'random',
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'order', [
            'label' => esc_html__('Order', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'ASC' => 'Ascending',
                'DESC' => 'Descending'
            ],
            'default' => 'DESC',
            'condition' => [
                'orderby!' => ['', 'random'],
            ],
                ]
        );

        $this->add_cache_options();
        
        $this->end_controls_section();
        
        $this->add_no_result_section();
    }

    // La QUERY
    public function query_the_elements() {
        
        $settings = $this->get_settings_for_display();

        $response = $this->maybe_get_cache();
        //echo '<pre>';var_dump($response);echo '</pre>';
        $rows = Utils::maybe_json_decode($response, true);
        if (is_array($rows)) {
            //echo '<pre>';var_dump($rows);echo '</pre>';
            //die();
        } else {
            $rows = simplexml_load_string($response, null, LIBXML_NOCDATA);
        }
        //var_dump($rows); die();
        if ($rows) {
            $rows = json_encode($rows);
            $rows = json_decode($rows, true);
            
            if (count($rows) == 1) {
                $tmp = reset($rows);
                // Checking for sequential keys of array
                if(array_keys($tmp) === range(0, count($tmp) - 1)) {  
                    // is sequential
                    $rows = $tmp;
                }                
            }

            if (!empty($settings['archive_path'])) {
                $tmp = explode('.', $settings['archive_path']);
                $sub_data = Utils::get_array_value($rows, $tmp);
                if ($sub_data) {
                    $rows = $sub_data;
                }
            }

            if ($settings['rows_per_page'] == 1) {
                $rows = array($rows);
            }

            if (!empty($settings['where'])) {
                foreach ($settings['where'] as $where) {
                    foreach ($rows as $key => $row) {
                        if ($where['where_field']) {
                            $ids = Utils::explode($where['where_field'], '.');
                            $value = Utils::get_array_value($row, $ids);                   
                            if (!$this->check_condition($value, $where['where_operator'], $where['where_value'])) {
                                unset($rows[$key]);
                            }
                        }
                    }
                }
            }

            $rows = array_values($rows); // reindex the array

            //echo '</table>' . PHP_EOL;
            if ($settings['orderby']) {
                if ($settings['orderby'] == 'random') {
                    shuffle($rows);
                } else {
                    global $widget_settings;
                    $widget_settings = $settings;
                    usort($rows, function ($a, $b) {
                        global $widget_settings;
                        $ids = Utils::explode($widget_settings['orderby'], '.');
                        if ($widget_settings['order'] == 'DESC') {
                            return Utils::get_array_value($b, $ids) <=> Utils::get_array_value($a, $ids);
                        }
                        return Utils::get_array_value($a, $ids) <=> Utils::get_array_value($b, $ids);
                    });
                }
            }
        }
        
        // DEBUG
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty($this->get_settings_for_display('query_debug'))) {
                echo '<pre>';
                var_dump($rows);
                echo '</pre>';
            }
        }

        $this->query = $rows;
    }

}
