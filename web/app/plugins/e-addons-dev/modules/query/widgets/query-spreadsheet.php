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
 * Query CSV
 *
 * Elementor widget for E-Addons
 *
 */
class Query_Spreadsheet extends Base_Query {

    use Traits\Common;

    public function get_pid() {
      return 30528;
    }

    public function get_icon() {
        return 'eadd-query-csv';
    }

    public function get_name() {
        return 'e-query-spreadsheet';
    }

    public function get_title() {
        return esc_html__('Query Spreadsheet', 'e-addons');
    }

    public function get_categories() {
		return [ 'query-dev' ];
    }

    protected $querytype = 'spreadsheet';

    protected function register_controls() {
        parent::register_controls();

        $this->controls_dev_common_content();

        $this->start_controls_section(
                'section_query_spreadsheet', [
            'label' => '<i class="eaddicon eicon-settings" aria-hidden="true"></i> ' . esc_html__('Spreadsheet', 'e-addons'),
            'tab' => 'e_query',
                ]
        );

        $this->add_control(
                'query_type', [
            'label' => esc_html__('Query Type', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'separator' => 'before',
            'label_block' => true,
            'options' => [
                'path' => esc_html__('Path', 'e-addons'),
                'media' => esc_html__('Media', 'e-addons'),
            //'url' => esc_html__('Url', 'e-addons'),
            ],
            'default' => 'media',
                ]
        );

        $this->add_control(
                'file_id', [
            'label' => esc_html__('Media from Library', 'e-addons'),
            'type' => 'file',
            'condition' => [
                'query_type' => 'media',
            ],
                ]
        );

        $this->add_control(
                'file_path', [
            'label' => esc_html__('File position', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'my-folder/sub-folder/my-file.xls',
            'description' => esc_html__('Insert file path starting from WP root folder', 'e-addons'),
            'condition' => [
                'query_type!' => 'media',
            ],
                ]
        );

        $this->add_control(
                'spreadsheet_header', [
            'label' => esc_html__('Has Header Row', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'description' => esc_html__('Use first row to identify the cols.', 'e-addons'),
                ]
        );
        $this->add_control(
                'spreadsheet_empty', [
            'label' => esc_html__('Clean empty rows', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->add_control(
                'rows_per_page', [
            'label' => esc_html__('Number of Rows', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '10',
            'description' => esc_html__('Number of Results per Page, leave empty for global configuration or -1 to display all'),
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'offset', [
            'label' => esc_html__('Rows Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
                ]
        );
        $this->add_control(
                'limit', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
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

        $this->end_controls_section();
    }

    // La QUERY
    public function query_the_elements() {

        $settings = $this->get_settings_for_display();

        $file_path = false;
        switch ($settings['query_type']) {
            case 'media':
                if (!empty($settings['file_id'])) {
                    $file_path = get_attached_file($settings['file_id']);
                }
                break;
            case 'path':
                $file_path = ABSPATH . $settings['file_path'];
        }

        $rows = array();
        if ($file_path && file_exists($file_path) && is_file($file_path)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
            
            $worksheet = $spreadsheet->getActiveSheet();
            $headers = [];
            //echo '<table>' . PHP_EOL;
            $index = 0;
            foreach ($worksheet->getRowIterator() as $row_id => $row) {

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $cell) {
                    $coordinates = $cell->getCoordinate();
                    $value = $cell->getFormattedValue();
                    //echo '<td>' . $value .'</td>' . PHP_EOL;
                    //$row_id = $key + 1;
                    $col = str_replace($row_id, '', $coordinates);

                    if ($row_id == 1 && $settings['spreadsheet_header']) {
                        $headers[$col]['ID'] = sanitize_key($value);
                        $headers[$col]['value'] = $value;
                    } else {
                        $rows[$index][$col] = $value;
                        if (!empty($headers[$col])) {
                            $rows[$index][$headers[$col]['ID']] = $value;
                        }
                    }
                }
                if ($row_id > 1 || empty($settings['spreadsheet_header'])) {
                    if (!empty($settings['spreadsheet_empty']) && Utils::empty($rows[$index])) {
                        unset($rows[$index]);
                    } else {
                        $rows[$index]['ID'] = $row_id;
                        $index++;
                    }
                }

                //echo '</tr>' . PHP_EOL;
            }


            if (!empty($settings['where'])) {
                foreach ($settings['where'] as $where) {
                    foreach ($rows as $key => $row) {
                        if ($where['where_field'] && isset($row[$where['where_field']])) {
                            if (!$this->check_condition($row[$where['where_field']], $where['where_operator'], $where['where_value'])) {
                                unset($rows[$key]);
                            }
                        }
                    }
                }
            }

            //echo '</table>' . PHP_EOL;
            if ($settings['orderby']) {
                if ($settings['orderby'] == 'random') {
                    shuffle($rows);
                } else {
                    global $widget_settings;
                    $widget_settings = $settings;
                    usort($rows, function ($a, $b) {
                        global $widget_settings;
                        if ($widget_settings['order'] == 'DESC') {
                            return $b[$widget_settings['orderby']] <=> $a[$widget_settings['orderby']];
                        }
                        return $a[$widget_settings['orderby']] <=> $b[$widget_settings['orderby']];
                    });
                }
            }
            //var_dump($rows);
            //var_dump($headers);
        }
        $this->query = $rows;
    }

}
