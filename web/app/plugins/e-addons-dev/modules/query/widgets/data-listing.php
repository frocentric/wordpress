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
class Data_Listing extends Base_Query {

    use Traits\Common;

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
    }

    public function get_pid() {
      return 34898;
    }
     
    public function get_icon() {
        return 'eadd-query-datalisting';
    }

    public function get_name() {
        return 'e-data-listing';
    }

    public function get_title() {
        return esc_html__('Data Listing', 'e-addons');
    }

    public function get_categories() {
        return [ 'query-dev' ];
    }

    protected $querytype = 'data';

    protected function register_controls() {
        parent::register_controls();

        $this->controls_dev_common_content();

        $this->start_controls_section(
                'section_data_listing', [
            'label' => '<i class="eaddicon eicon-settings" aria-hidden="true"></i> ' . esc_html__('Data Rows', 'e-addons'),
            'tab' => 'e_query',
                ]
        );
        
        $this->add_control(
                'query_debug', [
            'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show source data for DEBUG', 'e-addons') . '</span>',
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
                'rows_dynamic', [
            'label' => esc_html__('Dynamic Rows', 'e-addons'),
            'type' => 'd-tag', //\Elementor\Controls_Manager::MEDIA,
            'description' => esc_html__('Use this Dynamic Tag as source of your data, it accept any kind of array'),
            'separator' => 'after',
                ]
        );
        
        $this->add_control(
                'rows_dynamic_custom', [
            'raw' => '<style>
                .elementor-control-rows_dynamic .elementor-control-media__content__upload-button,
                .elementor-control-rows_dynamic .elementor-control-media-area { display: none; }
                .elementor-control-rows_dynamic .elementor-fit-aspect-ratio { padding-bottom: 27px }
                .elementor-control-rows_dynamic .elementor-control-media__tools { bottom: 0 !important; }   
                .elementor-control-rows_dynamic .elementor-control-dynamic-switcher { width: 100%; }
                .elementor-control-rows_dynamic .elementor-control-media__tools > :not(:first-child) { margin-left: 0; }
                    </style>',
            'type' => \Elementor\Controls_Manager::RAW_HTML,
                ]
        );

        $this->add_control(
                'rows_per_page', [
            'label' => esc_html__('Number of Rows', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'description' => esc_html__('Number of Results per Page, leave empty for global configuration or -1 to display all'),
                ]
        );
        $this->add_control(
                'offset', [
            'label' => esc_html__('Rows Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
                ]
        );
        $this->add_control(
                'limit', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
                ]
        );

        
        $this->add_control(
                'rows_dynamic_hide', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<style>.elementor-control.elementor-control-rows_dynamic .elementor-control-media .elementor-control-media__tool {display: none;}</style>',
                ]
        );

        $this->add_control(
                'rows_fallback', [
            'label' => esc_html__('Fallback Rows', 'e-addons'),
            'type' => \Elementor\Controls_Manager::TEXTAREA, //CODE,
            'description' => esc_html__('Insert all Rows in JSON format'),
                ]
        );

        $this->end_controls_section();
        
        $this->add_no_result_section();
    }

    public function query_the_elements() {

        $settings = $this->get_settings_for_display();
        
        $rows = $settings['rows_dynamic'];
        //var_dump($rows);
        $rows = Utils::maybe_json_decode($rows, true);
        
        if (Utils::empty($rows)) {
            if (!empty($settings['rows_fallback'])) {
                //$rows = array();
                //foreach ($settings['rows_fallback'] as $row) {
                    $rows = json_decode($settings['rows_fallback'], true);
                //}
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
