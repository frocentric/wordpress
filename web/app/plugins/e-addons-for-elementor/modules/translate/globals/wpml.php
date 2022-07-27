<?php

namespace EAddonsForElementor\Modules\Translate\Globals;

use EAddonsForElementor\Base\Base_Global;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Translate extenstion
 *
 * @since 1.0.1
 */
class Wpml extends Base_Global {

    public function __construct() {
        parent::__construct();

        if (Utils::is_plugin_active('wpml')) {
            //https://wpml.org/documentation/plugins-compatibility/elementor/how-to-add-wpml-support-to-custom-elementor-widgets/
            add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter']);
        }
    }
    
    public function show_in_panel() {
        return Utils::is_plugin_active('wpml');
    }

    public function get_icon() {
        return 'eadd-e-addoons-wpml';
    }

    public function get_pid() {
        return 6760;
    }

    /**
     * Adds additional translatable nodes to WPML
     *
     * @since 1.5.4
     *
     * @param  array   $nodes_to_translate WPML nodes to translate
     * @return array   $nodes_to_translate Updated nodes
     */
    public function wpml_widgets_to_translate_filter($widgets) {
        $e_widgets = \Elementor\Plugin::instance()->widgets_manager->get_widget_types();
        foreach ($e_widgets as $widget) {
            if (is_subclass_of($widget, 'EAddonsForElementor\Base\Base_Widget')) {
                $fields = array();
                $controls = $widget->get_controls();
                //var_dump($stack); die();
                if (!empty($controls)) {
                    //var_dump($stack['controls']); die();
                    foreach ($controls as $akey => $control) {

                        /* if ($akey == 'list_items') {
                          echo '<pre>';var_dump($control); die();
                          } */
                        if ($control['tab'] == 'content') {
                            $type = false;
                            switch ($control['type']) {
                                case 'text':
                                //case 'heading':
                                    $type = 'LINE';
                                    break;
                                case 'textarea':
                                    $type = 'AREA';
                                    break;
                                case 'wysiwyg':
                                    $type = 'VISUAL';
                                    break;
                                case 'url':
                                    $type = 'LINK';
                                    break;

                                case 'repeater':
                                    if (!is_subclass_of($widget, 'EAddonsForElementor\Modules\Query\Base\Query')) {
                                        foreach ($control['fields'] as $rkey => $rcontrol) {
                                            $rtype = false;
                                            switch ($rcontrol['type']) {
                                                case 'text':
                                                //case 'heading':
                                                    $rtype = 'LINE';
                                                    break;
                                                case 'textarea':
                                                    $rtype = 'AREA';
                                                    break;
                                                case 'wysiwyg':
                                                    $rtype = 'VISUAL';
                                                    break;
                                                case 'url':
                                                    $rtype = 'LINK';
                                                    break;
                                            }
                                            if ($rtype) {
                                                $fields[] = array(
                                                    'field' => $rkey,
                                                    'type' => esc_html__($rcontrol['label'], 'e-addons'),
                                                    'editor_type' => $rtype, // 'LINE', 'VISUAL', 'AREA', 'LINK'
                                                );
                                            }
                                        }
                                    }
                                    break;
                            }

                            if ($type) {
                                if (!empty($control['label'])) {
                                    $fields[] = array(
                                        'field' => $akey,
                                        'type' => esc_html__($control['label'], 'e-addons'),
                                        'editor_type' => $type, // 'LINE', 'VISUAL', 'AREA', 'LINK'
                                    );
                                }
                            }
                        }
                    }
                }

                if (!empty($fields)) {
                    
                    $exclusions = array(
                        'carousel_', 'dualslider_', 'maps_', 'table_', 'timeline_', 'list_', 'grid_', 'cards_'
                    );
                    foreach ($fields as $key => $value) {
                        foreach ($exclusions as $excl) {
                            if (substr($value['field'], 0, strlen($excl)) == $excl) {
                                unset($fields[$key]);
                            }
                        }
                    }
                    
                    $widgets[$widget->get_name()] = array(
                        'conditions' => array('widgetType' => $widget->get_name()),
                        'fields' => $fields,
                    );
                    
                }
            }
        }

        foreach ($e_widgets as $query) {
            if ($query && $query instanceof \EAddonsForElementor\Modules\Query\Base\Query) {
                if (!empty($widgets[$query->get_name()])) {
                /*if (!class_exists('\EAddonsForElementor\Modules\Translate\Translations\Wpml_Query')) {
                    include_once(E_ADDONS_PATH.'modules'.DIRECTORY_SEPARATOR.'translate'.DIRECTORY_SEPARATOR.'translations'.DIRECTORY_SEPARATOR.'wpml-query.php');
                    $test = new \EAddonsForElementor\Modules\Translate\Translations\Wpml_Query();
                }*/
                    $widgets[$query->get_name()] = [
                        'conditions' => ['widgetType' => $query->get_name()],
                        'fields' => $widgets[$query->get_name()]['fields'],
                        'integration-class' => '\EAddonsForElementor\Modules\Translate\Translations\Wpml_Query',
                    ];
                }
            }
            
            if ($query->get_name() == 'e-typingmotion') {
                $widgets[$query->get_name()] = [
                    'conditions' => ['widgetType' => $query->get_name()],
                    'fields' => $widgets[$query->get_name()]['fields'],
                    'integration-class' => '\EAddonsForElementor\Modules\Translate\Translations\Wpml_Typingmotion',
                ];
            }
            
            if ( in_array($query->get_name(), ['e-accordion-section'])) {
                $widgets[$query->get_name()] = [
                    'conditions' => ['widgetType' => $query->get_name()],
                    'fields' => $widgets[$query->get_name()]['fields'],
                    'integration-class' => '\EAddonsForElementor\Modules\Translate\Translations\Wpml_Section',
                ];
            }
        }

        //echo '<pre>';var_dump($widgets); die();

        return $widgets;
    }

}
