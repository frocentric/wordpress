<?php

namespace EAddonsForElementor\Modules\Query\Items;

use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Item;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Termstaxonomy extends Base_Item {
    
    public function __construct() {
        parent::__construct();
        add_filter('e_addons/query/post/item_types', [$this, 'register']);
        add_action('e_addons/query/item/content/controls', [$this, 'add_content_controls'], 10, 2);
    }

    public function get_name() {
        return 'item_termstaxonomy';
    }

    public function get_title() {
        return esc_html__('Terms', 'e-addons');
    }
    
    public function add_content_controls($widget, $target) {
        if ($widget instanceof \EAddonsForElementor\Modules\Query\Widgets\Query_Posts) {
            $target->add_control(
                    'separator_chart', [
                'label' => esc_html__('Separator', 'e-addons'),
                //'description' => esc_html__('Separator caracters.','e-addons'),
                'type' => Controls_Manager::TEXT,
                'default' => '/',
                'condition' => [
                    'item_type' => $this->get_name(),    
                ]
                    ]
            );
            $target->add_control(
                    'only_parent_terms', [
                'label' => esc_html__('Hierarchical view', 'e-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'both' => [
                        'title' => esc_html__('All', 'e-addons'),
                        'icon' => 'fa fa-sitemap',
                    ],
                    'yes' => [
                        'title' => esc_html__('First level (Root)', 'e-addons'),
                        'icon' => 'fa fa-female',
                    ],
                    'children' => [
                        'title' => esc_html__('Last level (Leaf)', 'e-addons'),
                        'icon' => 'fa fa-child',
                    ]
                ],
                'toggle' => false,
                'default' => 'both',
                'condition' => [
                    'item_type' => $this->get_name(),    
                ]
                    ]
            );
            $target->add_control(
                    'block_enable', [
                'label' => esc_html__('Block', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'block',
                'render_type' => 'template',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .e-add-term-item' => 'display: {{VALUE}}'
                ],
                'condition' => [
                    'item_type' => $this->get_name(),    
                ]
                    ]
            );
            $target->add_control(
                    'taxonomy_filter',
                    [
                        'label' => esc_html__('Filter Taxonomy', 'e-addons'),
                        'type' => 'e-query',
                        'placeholder' => esc_html__('Search Taxonomy', 'e-addons'),
                        'description' => esc_html__('Use only terms in selected taxonomies. If empty all terms will be used.', 'e-addons'),
                        'separator' => 'before',
                        'label_block' => true,
                        'query_type' => 'taxonomies',
                        'multiple' => true,
                        'condition' => [
                            'item_type' => $this->get_name(), 
                        ],
                    ]
            );
        }
    }

    public function render($settings, $item_index, $widget) {
        $skin = $widget->skin;

        // Settings ------------------------------
        $taxonomy_filter = $settings['taxonomy_filter'];
        $separator_chart = $settings['separator_chart'];
        $only_parent_terms = $settings['only_parent_terms'];
        $block_enable = $settings['block_enable']; //style
        $icon_enable = $settings['icon_enable'];
        //
        $use_link = $settings['use_link'];
        // ---------------------------------------

        $term_list = [];

        $taxonomy = get_post_taxonomies($skin->current_id);
        
        
        
        ob_start();
        // ------- Ciclo le taxonomy in automatico
        foreach ($taxonomy as $tax) {

            // @p se $taxonomy_filter è valorizzato filtro solo le taxonomy scelte
            if (!empty($taxonomy_filter)) {
                if (!in_array($tax, $taxonomy_filter)) {
                    continue;
                }
            }
            // ...da migliorarre...
            if ($tax != 'post_format') {

                $term_list = Utils::get_post_terms($skin->current_id, $tax);
                if ($term_list && is_array($term_list) && count($term_list) > 0) {

                    //if($cont == 1){
                    // @p La label
                    echo $skin->render_label_before_item($settings, get_taxonomy($tax)->labels->name . ': ');

                    if ($icon_enable) {
                        // @p l'icona
                        $icon = '';
                        if (is_taxonomy_hierarchical($tax)) {
                            $icon = '<i class="e-add-icon e-add-query-icon far fa-folder-open" aria-hidden="true"></i> ';
                        } else {
                            $icon = '<i class="e-add-icon e-add-query-icon far fa-tags" aria-hidden="true"></i> ';
                        }
                        echo $icon;
                    }
                    //}

                    echo '<ul class="e-add-terms-list e-add-taxonomy-' . $tax . '">';

                    // ------- Ciclo i termini
                    $cont = 1;
                    $divider = '';
                    foreach ($term_list as $term) {

                        if (!empty($only_parent_terms)) {
                            if ($only_parent_terms == 'yes') {
                                if ($term->parent)
                                    continue;
                            }
                            if ($only_parent_terms == 'children') {
                                if (!$term->parent)
                                    continue;
                            }
                        }

                        //@p il link del termine
                        $term_url = trailingslashit(get_term_link($term));

                        $linkOpen = '';
                        $linkClose = '';

                        if ($use_link) {
                            if ($use_link != 'yes') {
                                $term_url = $skin->get_item_link($settings);
                            }
                            $linkOpen = '<a class="e-add-link" href="' . $term_url . '"' . (!empty($settings['blanklink_enable']) ? ' target="_blank"' : '') . '>';
                            $linkClose = '</a>';
                        }
                        //@p il divisore in caso di inline
                        if ($cont > 1 && !$block_enable) {
                            $divider = '<span class="e-add-separator">' . $separator_chart . '</span>';
                        }
                        //@ stampo il termine
                        echo '<li class="e-add-term-item">';
                        echo $divider . '<span class="e-add-term e-add-term-' . $term->term_id . '" data-e-add-order="' . $term->term_order . '">' . $linkOpen . $term->name . $linkClose . '</span>';
                        echo '</li>';
                        //
                        $cont++;
                    } //end foreach terms
                    echo '</ul>';
                    echo $skin->render_label_after_item($settings);
                } //end if termslist
            } //end exclusion
        } //end foreach taxonomy	
        $taxs = ob_get_clean();        
        if ($taxs) {
            echo '<div class="e-add-post-terms">'.$taxs.'</div>';
        }
    }

}
