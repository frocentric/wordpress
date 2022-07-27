<?php

namespace EAddonsDev\Modules\Query\Shortcodes;

use EAddonsForElementor\Base\Base_Shortcode;
use EAddonsForElementor\Core\Utils;

/**
 * Description of Elastic Form Options
 *
 * @author fra
 */
class Elastic_Options extends Base_Shortcode {

    use \EAddonsDev\Modules\Remote\Traits\Cache;

    public function get_name() {
        return 'eeesfo'; // AKA Elementor E-addons Elastic Search Form Options
    }

    public function get_pid() {
        return 31051;
    }

    public function get_icon() {
        return 'eadd-elasticsearch-shortcode';
    }

    /**
     * Execute the Shortcode
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function do_shortcode($atts) {
        $atts = shortcode_atts(
                array(
                    'id' => '',
                    'label' => '',
                    'value' => '',
                    'order' => '',
                    'term' => '',
                    'url' => '',
                    'authorization_user' => '',
                    'authorization_pass' => '',
                    'empty' => '',
                    'size' => 10000,
                ),
                $atts,
                $this->get_name()
        );

        // [eeesfo id="1669e02" term="table_name:category" label="_souce.name" order="" value="_id"]
        if (empty($atts['id']) && empty($atts['url'])) {
            // find Query Rest Api Widget in curren page/template
            $widgets = Utils::get_elementor_elements('e-query-rest-api', get_the_ID());
            //var_dump($widgets);
            if (!empty($widgets)) {
                $tmp = reset($widgets);
                foreach ($tmp as $element_id => $settings) {
                    if (!empty($settings['method']) && $settings['method'] == 'DSL') {
                        $atts['id'] = $element_id;
                    }
                }
                //var_dump($settings); die();
                //if (!empty($settings['url'])) { $atts['url'] = $settings['url']; }
                //if (!empty($settings['authorization_user'])) { $atts['authorization_user'] = $settings['authorization_user']; }
                //if (!empty($settings['authorization_pass'])) { $atts['authorization_pass'] = $settings['authorization_pass']; }
            }
        }

        if ($atts['id']) {
            $settings = Utils::get_settings_by_element_id($atts['id']);
            if (!empty($settings['data_dsl'])) {
                $parsed = self::parse_dsl($settings['data_dsl']);
                if (!empty($parsed['command']) && !empty($atts['term'])) {

                    $label = str_replace('_source.', '', $atts['label']);
                    $order = $atts['order'] ? $atts['order'] : $label . '.keyword';

                    $terms = array();
                    $tmp = Utils::explode($atts['term']);
                    foreach ($tmp as $term) {
                        list($field, $value) = explode(':', $term);
                        $terms[$field] = $value;
                    }
                    $query = '{ "term": { "' . $field . '": "' . $value . '" } }';
                    if (count($terms) > 1) {
                        $query = '{ "bool": { "must": [';
                        $i = 0;
                        foreach ($terms as $field => $value) {
                            if ($i) {
                                $query .= ',';
                            }
                            $query .= '{"match": {"' . $field . '": "' . $value . '"}}';
                            $i++;
                        }
                        $query .= '] } }';
                    }

                    $settings['data_dsl'] = $parsed['method'] . ' ' . $parsed['command'] . '
                    {
                        "size": ' . $atts['size'] . ',
                        "query": ' . $query . '
                        ' . ($order ?
                            ',"sort" : [
                            { "' . $order . '" : "asc" }
                        ]' : '') . '
                    }';
                    //var_dump($settings['data_dsl']); die();
                    $settings['data_load_id_depth'] = 1;
                    $settings['data_load_id_fields'] = '';
                    $settings['rows_per_page'] = -1;
                }
            }
            if (!empty($settings)) {
                foreach ($settings as $skey => $setting) {
                    if (empty($atts[$skey])) {
                        $atts[$skey] = $setting;
                    }
                }
            }
        }

        if (!empty($atts['authorization_user']) && !empty($atts['authorization_pass'])) {
            $atts['require_authorization'] = 'yes';
        }

        //var_dump($atts); die();
        $results = self::get_remote($atts);
        //var_dump($results); die();

        $options = '';
        if ($atts['empty']) {
            $options .= $atts['empty'] . '|';
        }
        if (!empty($results)) {
            $results = json_decode($results, true);
            //var_dump($results['hits']['hits']); die();
            if (!empty($results['hits']['hits'])) {
                $results = Utils::remove_quotes($results);
                $values = array();
                foreach ($results['hits']['hits'] as $index => $result) {
                    $ids = Utils::explode($atts['label'], '.');
                    $label = Utils::get_array_value($result, $ids);
                    if (is_array($label)) {
                        $result = $label;
                        $label = Utils::to_string($label);
                    }
                    if ($label) {
                        $option = $label;
                        if ($atts['value']) {
                            $ids = Utils::explode($atts['value'], '.');
                            $value = Utils::get_array_value($result, $ids);
                            if ($value) {
                                $option .= '|' . $value;
                            }
                        }
                        if (in_array($option, $values))
                            continue;
                        $values[$index] = $option;
                        if ($options)
                            $options .= PHP_EOL;
                        $options .= $option;
                    }
                }
            }
        }

        //var_dump($options); die();
        return $options;
    }

}
