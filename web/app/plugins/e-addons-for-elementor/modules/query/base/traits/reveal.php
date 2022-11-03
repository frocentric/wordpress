<?php
namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;

/**
 * Description of Animation Reveal
 *
 * @author fra
 */
trait Reveal {
    // ------------------------------------------------------------ [SECTION Animtion Reveal]
    public function register_reveal_controls() {
        $this->start_controls_section(
                'section_scrollreveal', [
                'label' => '<i class="eaddicon eicon-animation"></i> ' . esc_html__('Animation reveal', 'e-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    '_skin' => ['grid', 'mosaic', 'justified'],
                ],
            ]
        );
        $this->add_control(
            'scrollreveal_effect_type', [
                'label' => esc_html__('Effect', 'e-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => '0',
                'options' => [
                    '0' => 'None',
                    '1' => 'Opacity',
                    '2' => 'Move Up',
                    '3' => 'Scale Up',
                    '4' => 'Fall Perspective',
                    '5' => 'Fly',
                    '6' => 'Flip',
                    '7' => 'Helix',
                    '8' => 'Bounce',
                ],
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'scrollreveal_live', [
                'label' => esc_html__('Live', 'e-addons'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    'scrollreveal_effect_type!' => '0'
                ]
            ]
        ); 
        
        $this->end_controls_section();
    }
}
