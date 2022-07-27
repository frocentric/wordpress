<?php

namespace EAddonsDev\Modules\Query\Skins;

use Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Export Skin
 *
 * Elementor Skin for e-addons Query widgets
 *
 */
class Export extends Base {

    use \EAddonsForElementor\Base\Traits\Button;

    public function __construct($parent = []) {
        parent::__construct($parent);

        if (!$parent) {
            foreach (self::$widgets as $widget) {
                add_action('elementor/widget/' . $widget . '/skins_init', function ($query) {
                    $class = get_class($this);
                    $skin = new $class($query);
                    $query->add_skin($skin);
                });
            }
        }
    }

    public function _register_controls_actions() {
        if ($this->parent) {
            parent::_register_controls_actions();
            add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_controls'], 20);
        }
    }

    public function get_id() {
        return 'export';
    }

    public function get_pid() {
        return 57748;
    }

    public function get_title() {
        return esc_html__('Export', 'e-addons');
    }

    public function get_icon() {
        return 'eicon-export-kit';
    }

    public function register_additional_controls() {

        $querytype = $this->parent->get_querytype();

        $this->start_controls_section(
                'section_query_export', [
            'label' => esc_html__('Export', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT
                ]
        );

        $this->add_control(
                'name', [
            'label' => esc_html__('File Name', 'e-addons'),
            'label_block' => true,
            'type' => Controls_Manager::TEXT,
            'description' => esc_html__('Write here the file title without extension. If empty it will take automatically the current Post Name.', 'e-addons'),
                ]
        );

        $this->add_control(
                'format', [
            'label' => esc_html__('File Format', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'csv' => esc_html__('CSV', 'e-addons'),
                'xlsx' => esc_html__('XLSX', 'e-addons'),
                'xls' => esc_html__('XLS', 'e-addons'),
                'ods' => esc_html__('ODS', 'e-addons'),
                'json' => esc_html__('JSON', 'e-addons'),
                'xml' => esc_html__('XML', 'e-addons'),
                'rss' => esc_html__('RSS', 'e-addons'),
            ],
            'default' => 'csv',
                ]
        );

        $this->add_control(
                'header', [
            'label' => esc_html__('Add Header Row', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                $this->get_id() . '_format' => ['csv', 'xls', 'xlsx', 'ods'],
            ],
                ]
        );

        $this->add_control(
                'csv_delimiter', [
            'label' => esc_html__('Delimiter', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => ',',
            'condition' => [
                $this->get_id() . '_format' => 'csv',
            ],
                ]
        );
        $this->add_control(
                'csv_enclosure', [
            'label' => esc_html__('Enclosure', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => [
                $this->get_id() . '_format' => 'csv',
            ],
                ]
        );

        $this->add_control(
                'root', [
            'label' => esc_html__('Root Tag', 'e-addons'),
            'label_block' => true,
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'root',
            'condition' => [
                $this->get_id() . '_format' => 'xml',
            ],
                ]
        );
        $this->add_control(
                'item', [
            'label' => esc_html__('Item Tag', 'e-addons'),
            'label_block' => true,
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'item',
            'condition' => [
                $this->get_id() . '_format' => 'xml',
            ],
                ]
        );

        $this->end_controls_section();

        $this->add_button();
    }

    public function export() {
        $settings = $this->parent->get_settings_for_display();
        if (empty($settings))
            return false;

        $rows = $this->get_value();

        $format = $settings[$this->get_id() . '_format'];

        $fileName = $this->get_filename() . '.' . $format;
        //var_dump($fileName); die();
        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');

        switch ($format) {
            case 'csv':
                foreach ($rows as $r => $row) {
                    foreach ($row as $c => $col) {
                        $rows[$r][$c] = str_replace(PHP_EOL, ' ', $col);
                    }
                }
            case 'ods':
            case 'xls':
            case 'xlsx':
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->fromArray($rows, NULL, 'A1');
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, ucfirst($format));
                if ($format == 'csv') {
                    $writer->setDelimiter($settings[$this->get_id() . '_csv_delimiter']);
                    $writer->setEnclosure($settings[$this->get_id() . '_csv_enclosure']);
                }
                $writer->save('php://output');
                break;
            case 'json':
                echo wp_json_encode($rows);
                break;
            case 'xml':
                $root = empty($settings[$this->get_id() . '_root']) ? 'root' : sanitize_key($settings[$this->get_id() . '_root']);
                $item = empty($settings[$this->get_id() . '_item']) ? 'item' : sanitize_key($settings[$this->get_id() . '_item']);
                $xml = new \SimpleXMLElement('<' . $root . '/>');
                //array_walk_recursive($rows, array($xml, 'addChild'));
                $this->to_xml($xml, $rows, $item);
                echo $xml->asXML();
                break;
            case 'rss':
                $title = empty($settings[$this->get_id() . '_title']) ? get_bloginfo() : sanitize_key($settings[$this->get_id() . '_title']);
                $description = empty($settings[$this->get_id() . '_description']) ? get_bloginfo('description') : sanitize_key($settings[$this->get_id() . '_description']);
                $link = empty($settings[$this->get_id() . '_link']) ? get_bloginfo('url') : sanitize_key($settings[$this->get_id() . '_link']);
                
                $rss = new \SimpleXMLElement('<channel/>');
                $rss->addChild('title', $title);
                $rss->addChild('link', $link);
                $rss->addChild('description', $description);
                $rss->addChild('language', get_locale());
                $rss->addChild('pubDate', wp_date('r'));
                $this->to_xml($rss, $rows);
                
                echo '<?xml version="1.0" encoding="UTF-8" ?><rss version="2.0">' . $rss->asXML() . '</rss>';
                break;
            default:
                echo Utils::to_string($rows);
        }

        die();
    }

    public function to_xml(\SimpleXMLElement $object, array $data, $item = 'item') {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($key == (int) $key) {
                    $key = $item;
                }
                $new_object = $object->addChild($key);
                //if (!$new_object) var_dump($key);
                $this->to_xml($new_object, $value, $item);
            } else {
                // if the key is an integer, it needs text with it to actually work.
                if ($key != 0 && $key == (int) $key) {
                    $key = "key_$key";
                }

                $object->addChild($key, $value);
            }
        }
    }

    public function add_button() {
        $this->start_controls_section(
                'section_button',
                [
                    'label' => esc_html__('Button', 'elementor'),
                ]
        );

        $this->register_button_content_controls();

        $this->end_controls_section();

        $this->start_controls_section(
                'section_style',
                [
                    'label' => esc_html__('Button', 'elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->register_button_style_controls();

        $this->end_controls_section();
    }

    public function render() {
        //parent::render();

        if (!empty($_GET['e_act']) && $_GET['e_act'] == $this->get_id()) {
            $this->export();
            return;
        }

        $sprd_url = get_option('siteurl');
        $sprd_url .= '?e_act=' . $this->get_id();
        $sprd_url .= '&post_id=' . get_the_ID();
        $sprd_url .= '&element_id=' . $this->parent->get_id();
        if (!empty($_GET)) {
            foreach ($_GET as $gkey => $aget) {
                $sprd_url .= '&' . $gkey . '=' . $aget;
            }
        }

        $settings['link']['url'] = $sprd_url;
        $this->parent->add_link_attributes('button', $settings['link']);
        $this->parent->add_render_attribute('button', 'class', 'elementor-button-link');

        $this->render_button();
    }

    public function render_repeateritem_start($item, $tag = 'div') {
        echo '!!TD!!';
    }

    public function render_repeateritem_end($tag = 'div') {
        echo '!!/TD!!';
    }

    public function render_item_start($key = 'post') {
        
    }

    public function render_item_end() {
        echo '!!EOL!!';
    }

    public function render_loop_start() {
        
    }

    public function get_value() {

        $settings = $this->parent->get_settings_for_display();
        if (empty($settings))
            return false;

        ob_start();
        parent::render();
        $raw = ob_get_clean();
        //
        //var_dump($raw); die();
        $header = $keys = [];
        $_items = $this->parent->get_settings_for_display('list_items');
        // ITEMS ///////////////////////
        if ($this->parent->get_querytype() == 'attachment') {
            $header[] = esc_html__('Media');
        }
        foreach ($_items as $item) {
            $label = $this->get_item_label($item);
            $header[] = $label;
            $keys[] = sanitize_title($label);
        }

        $allowed_tags = ['img', 'a'];
        /*if ($format != 'csv') {
          $allowed_tags = ['img', 'b', 'a', 'strong'];
        }*/
        $raw = strip_tags($raw, $allowed_tags);
                
        $raw = $this->get_src($raw);
        
        $rows = explode('!!EOL!!', $raw);
        unset($rows[count($rows) - 1]);
        //var_dump($rows); die();
        foreach ($rows as $r => $row) {
            $tmp = explode('!!/TD!!', $row);
            unset($tmp[count($tmp) - 1]);
            $vals = [];
            foreach ($tmp as $t => $mp) {
                list($pre, $value) = explode('!!TD!!', $mp, 2);
                switch($keys[$t]) {
                    case 'description':
                        break;
                    case 'guid':
                    case 'link':
                        $value = $this->get_href($value);
                    //case 'image': $value = $this->get_src($value);
                    default: 
                        $value = strip_tags($value);
                }
                $value = trim($value);
                $tmp[$t] = $value;
                $vals[$keys[$t]] = $value;
            }
            $rows[$r] = $vals;
        }

        if (!empty($settings[$this->get_id() . '_header'])) {
            array_unshift($rows, $header);
        }

        //var_dump($rows); die();
        return $rows;
    }

    public function get_filename() {
        $settings = $this->parent->get_settings_for_display();
        if (empty($settings))
            return false;

        $fileName = $this->get_id();
        $post = get_post();
        if ($post) {
            $fileName = $post->post_name;
        }
        if (!empty($settings[$this->get_id() . '_name'])) {
            $fileName = $settings[$this->get_id() . '_name'];
        }
        $fileName = sanitize_file_name($fileName);
        return $fileName;
    }
    
    public function get_src($raw) {
        $imgs = explode('<img ', $raw);
        if (count($imgs) > 1) {
            $tmp = '';
            foreach ($imgs as $i => $img) {
                if ($i) {
                    list($pre, $more) = explode('src="', $img, 2);
                    list($src, $more) = explode('"', $more, 2);
                    list($aa, $more) = explode('>', $more, 2);
                    $img = $src . $more;
                }
                $tmp .= $img;
            }
            return $tmp;
        }
        return $raw;
    }
    public function get_href($raw) {
        $as = explode('<a ', $raw);
        $tmp = '';
        foreach ($as as $i => $a) {
            if ($i) {
                list($pre, $more) = explode('href="', $a, 2);
                list($href, $more) = explode('"', $more, 2);
                list($aa, $more) = explode('</a>', $more, 2);
                $a = $href . $more;
            }
            $tmp .= $a;
        }
        //var_dump($as); die();
        return $tmp;
    }

}
