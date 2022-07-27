<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Controls_Manager;
use EAddonsForElementor\Modules\Query\Skins\Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Grid Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Justifiedgrid extends Base {

    public function _register_controls_actions() {
        if ($this->parent) {
            parent::_register_controls_actions();
            add_action( 'elementor/element/'.$this->parent->get_name().'/section_e_query/after_section_end', [ $this, 'register_additional_justifiedgrid_controls' ], 20 );
            add_action( 'elementor/element/'.$this->parent->get_name().'/section_items/before_section_start', [ $this, 'register_reveal_controls' ], 20 );
        }
    }
    
    public function get_script_depends() {
        return ['imagesloaded', 'justifiedgallery', 'e-addons-query-justifiedgrid'];
    }

    public function get_style_depends() {
        return ['e-addons-common-query', 'justifiedgallery'];
    }
    
    public function get_id() {
        return 'justifiedgrid';
    }
    
    public function get_pid() {
        return 229;
    }

    public function get_title() {
        return esc_html__('Justified Grid', 'e-addons');
    }

    
    public function get_icon() {
        return 'eadd-gallery-grid-justified';
    }

    public function register_additional_justifiedgrid_controls() {
        $keyJustified = 'justified';

        /*
        http://miromannino.github.io/Justified-Gallery/options-and-events/
        Options:
        - rowHeight                 120
        - maxRowHeight              false
        - maxRowsCount              0
        - sizeRangeSuffixes         {}
        - thumbnailPath             undefined
        - lastRow                   'nojustify' [justify, hide, center, right]
        - captions                  true
        - margins                   1
        - border                    -1
        - rtl                       false
        - cssAnimation              true
        - imagesAnimationDuration   500
        */
        
        $this->start_controls_section(
            'section_'.$keyJustified.'grid', [
                'label' => '<i class="eaddicon eadd-gallery-grid-justified"></i> ' . esc_html__('Justified Grid', 'e-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        //@p l'altezza dei mattoncini
        $this->add_control(
            $keyJustified.'_rowHeight', [
                'label' => esc_html__('Bricks Height', 'e-addons'),
                'type' => Controls_Manager::SLIDER,
                'frontend_available' => true,
                'default' => [
                    'size' => '250',
                ],
                'range' => [
                    'px' => [
                        'min' => 150,
                        'max' => 800,
                        'step' => 1
                    ],
                ],
            ]
        );
        //@p la massimma altezza
        $this->add_control(
            $keyJustified.'_maxRowHeight', [
                'label' => esc_html__('Same Height', 'e-addons'),
                'description' => esc_html__('All raw will be the same height ', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'default' => '',
                'return_value' => '100',
            ]
        );
        // $this->add_control(
        //     $keyJustified.'_maxRowHeight', [
        //         'label' => esc_html__('Max Height', 'e-addons'),
        //         'type' => Controls_Manager::NUMBER,
        //         'frontend_available' => true,
        //         'default' => '',
        //         'min' => 0,
        //         'max' => 500,
        //         'step' => 1,
        //     ]
        // );
        $this->add_control(
            $keyJustified.'_maxRowsCount', [
                'label' => esc_html__('Max Row Count', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'frontend_available' => true,
                'default' => '',
                'separator' => 'before',
                'min' => 0,
                'max' => 12,
                'step' => 1,
            ]
        );
        $this->add_control(
            $keyJustified.'_margin', [
                'label' => esc_html__('Space', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'frontend_available' => true,
                'separator' => 'before',
                'default' => '',
                'min' => 0,
                'max' => 100,
                'step' => 1,
            ]
        );
        /*$this->add_responsive_control(
            $keyJustified.'_border', [
                'label' => esc_html__('Padding', 'e-addons'),
                'type' => Controls_Manager::NUMBER,
                'frontend_available' => true,
                'default' => -1,
                'min' => -1,
                'max' => 100,
                'step' => 1,
            ]
        );*/
        $this->add_control(
            $keyJustified.'_lastRow', [
                'label' => esc_html__('Options of Last-row', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'frontend_available' => true,
                'default' => 'justify',
                'options' => [
                    'justify' => 'Justify',
                    'nojustify' => 'No-Justify',
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                    'hide' => 'Hide',
                ],
            ]
        );
        $this->end_controls_section();
    }
    
    public function get_scrollreveal_class() {
        if ($this->get_instance_value('scrollreveal_effect_type'))
            return 'reveal-effect reveal-effect-' . $this->get_instance_value('scrollreveal_effect_type');
    }
    // Classes ----------
	public function get_container_class() {
		return 'e-add-skin-' . $this->get_id();
	}
    public function get_wrapper_class() {
        return 'e-add-wrapper-' . $this->get_id();
    }
    public function get_item_class() {
        return 'e-add-item-' . $this->get_id();
    }
}
