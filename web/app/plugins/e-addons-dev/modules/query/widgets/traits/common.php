<?php

namespace EAddonsDev\Modules\Query\Widgets\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

/**
 * Description of label
 *
 * @author fra
 */
trait Common {
    
    public function get_query_skins() { 
        return [
            '\EAddonsForElementor\Modules\Query\Skins\Grid',
            '\EAddonsForElementor\Modules\Query\Skins\Carousel',
            //'\EAddonsForElementor\Modules\Query\Skins\Dualslider',
            '\EAddonsForElementor\Modules\Query\Skins\Table',
            '\EAddonsForElementor\Modules\Query\Skins\Simple_List',
        ];
    }
    
    public function controls_dev_common_content() {
        $types = Utils::get_post_types();
        $taxonomies = Utils::get_taxonomies();

        // ------------------------------------------------------------------ [SECTION ITEMS]
        $this->start_controls_section(
                'section_items', [
            'label' => '<i class="eaddicon eicon-radio" aria-hidden="true"></i> ' . esc_html__('List of Items', 'e-addons'),
            'condition' => [
                '_skin!' => ['nextpost'],
                'style_items!' => 'template',
            ],
                ]
        );

        ////////////////////////////////////////////////////////////////////////////////
        // -------- ITEMS ORDERING
        $repeater = new Repeater();
        //$chid = $repeater->get_name();

        $repeater->add_control(
                'item_type', [
            'label' => esc_html__('Item type', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_item_type_options(),
            'default' => 'item_custommeta',
            'placeholder' => 'title',
                ]
        );

        // TABS ----------
        $repeater->start_controls_tabs('items_repeater_tab');

        $repeater->start_controls_tab('tab_content', [
            'label' => esc_html__('Content', 'e-addons'),
        ]);

        // CONTENT - TAB
        // +********************* Common
        $this->controls_items_common_content($repeater);
        
        // +********************* Image
        // @@@aggiungo meta image in caso di acf, pods ecc oppure woocommerce/edd image
        $this->controls_items_image_content($repeater);

        // +********************* Label
        $this->controls_items_label_content($repeater);

        // +********************* Template
        $this->controls_items_template_content($repeater);

        // +********************* CustoFields (ACF, Pods, Toolset, Metabox)
        $this->custommeta_items($repeater);

        // +********************* ReadMore
        $this->controls_items_readmore_content($repeater);

        $repeater->end_controls_tab();

        $repeater->start_controls_tab('tab_style', [
            'label' => esc_html__('Style', 'e-addons'),
        ]);

        // STYLE - TAB (6)
        // --------------- BASE
        //@p le caratteristiche base:
        //  - text-align, flex-align, typography, space 
        $this->controls_items_base_style($repeater);

        // -------- ICON
        //@p le carateristiche grafche del colore dimensione dell'icona
        $this->controls_items_icon_style($repeater);
        // -------- COLORS
        //@p le carateristiche grafche del colore testi e background
        $this->controls_items_colors_style($repeater);

        // -------- COLORS-HOVER
        //@p le carateristiche grafche del colore testi e background nello statoo di hover
        $this->controls_items_colorshover_style($repeater);

        // ------------ SPACES
        //@p le carateristiche grafche le spaziature Padding e margin
        $this->controls_items_spaces_style($repeater);

        // ------------ BORDERS & SHADOW
        //@p le carateristiche grafche: bordo, raggio-del-bordo, ombra del box
        $this->controls_items_bordersandshadow_style($repeater);

        $repeater->end_controls_tab();

        $repeater->start_controls_tab('tab_advanced', [
            'label' => esc_html__('Advanced', 'e-addons'),
        ]);

        // ------------ ADVANCED - TAB
        // @p considero i campi avanzati: se è linkato (use_link) e se l'item è Bloock o Inline
        $this->controls_items_advanced($repeater);

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
                'list_items',
                [
                    'label' => esc_html__('ITEMS', 'e-addons'),
                    'separator' => 'before',
                    'show_label' => false,
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    //item_type.replace("item_", "")
                    'title_field' => '<# var etichetta = item_type; etichetta = etichetta.replace("item_", ""); #><b class="e-add-item-name"><i class="fa {{{ item_type+"-ic" }}}" aria-hidden="true"></i> {{{item_text_label}}} | {{{ etichetta }}}</b>',
                    'default' => $this->get_repeater_default(),
                ]
        );

        $this->controls_items_grid_debug($this);

        $this->end_controls_section();
    }
    public function get_repeater_default() {
        return [];
    }
    public function get_item_type_options() {
        return [
            'item_custommeta' => esc_html__('Custom Field', 'e-addons'),
            'item_label' => esc_html__('HTML', 'e-addons'),
            'item_image' => esc_html__('Image', 'e-addons'),
            'item_index' => esc_html__('Loop Index', 'e-addons'),
            'item_template' => esc_html__('Template', 'e-addons'),
        ];
    }
    
    public function loop($skin, $query) {

        $settings = $skin->parent->get_settings_for_display();

        $url = $this->get_permalink($settings);
        //echo '<pre>';var_dump($query);echo '</pre>';

        $results = $query;
        if (is_string($results)) {
            $results = array($results);
        }

        $i = 0;
        $j = 0;
        $paged = $skin->parent->get_current_page();
        $rows_per_page = empty($settings['rows_per_page']) ? get_option('posts_per_page') : intval($settings['rows_per_page']);
        $start = $rows_per_page * ($paged - 1);
        $stop = $start + $rows_per_page;
        if ($rows_per_page < 0) {
            $stop = count($results);
        }

        $offset = intval($settings['offset']);
        $limit = empty($settings['limit']) ? count($results) : intval($settings['limit']);
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
                    $skin->current_id = $key;
                    $skin->current_data = $row;
                    $skin->current_permalink = $url;
                    $skin->render_element_item();
                }
            }
        }
    }

    public function should_render($render, $skin, $query) {
        //echo '<pre>';var_dump($query);echo '</pre>';
        if (empty($query)) {
            return false;
        }
        return $render;
    }
    
    public function pagination__page_limit($page_limit, $skin, $query, $settings) {
        $no = $settings['rows_per_page'];
        if ($no) {
            $total_rows = count($query);
            $page_limit = ceil($total_rows / $no);
        }
        return $page_limit;
    }
    
    public function get_permalink($settings) {
        $url = false;
        switch($this->querytype) {
            case 'spreadsheet':
                switch ($settings['query_type']) {
                    case 'media':
                        if (!empty($settings['file_id'])) {
                            $file_path = get_attached_file($settings['file_id']);
                        }
                        break;
                    case 'path':
                        $file_path = ABSPATH . $settings['file_path'];
                }
                $url = Utils::path_to_url($file_path);
                break;

            case 'xml':
            case 'api':
            case 'rss':
                $url = $settings['url'];
                break;
        }
        return $url;
    }

}
