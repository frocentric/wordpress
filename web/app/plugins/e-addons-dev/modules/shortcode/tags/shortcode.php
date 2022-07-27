<?php
namespace EAddonsDev\Modules\Shortcode\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Shortcode extends Base_Tag {

    public function get_name() {
        return 'e-tag-shortcode';
    }

    public function get_title() {
        return esc_html__('Advanced Shortcode', 'e-addons');
    }

    public function get_icon() {
        return 'eadd-dynamic-tag-token';
    }
    
    public function get_pid() {
        return 5228;
    }

    /**
     * Register Controls
     *
     * Registers the Dynamic tag controls
     *
     * @since 2.0.0
     * @access protected
     *
     * @return void
     * Extend Elementor PRO Dynamic Tag Shortcode
     * /elementor-pro/modules/dynamic-tags/tags/shortcode.php
     */
    public function register_controls() {

        $this->add_control(
                'e_shortcode',
                [
                        'label' => esc_html__( 'Enter your shortcode', 'elementor' ),
                        'type' => Controls_Manager::TEXTAREA,
                        'placeholder' => '[gallery id="123" size="medium"]',
                ]
        );
        
        $this->add_control(
                'e_shortcode_data',
                [
                    'label' => esc_html__('Return as Structured Data', 'e-addons-for-elementor'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'description' => esc_html__('Accepts Json, media ID or URL', 'e-addons-for-elementor'),
                ]
        );

    }

    public function render() {        
        $value = $this->get_value();       
        echo Utils::to_string($value);
    }
    
    public function get_value($options = []) {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;
        
        $shortcode_string = $settings['e_shortcode'];
        
        $shortcode_string = Utils::get_dynamic_data($shortcode_string);
        $value = do_shortcode($shortcode_string);
        $value = maybe_unserialize($value);
        $value = Utils::maybe_json_decode($value, true);        
        $value = Utils::maybe_media($value, $this);
        //var_dump($value);
        /**
         * Should Escape.
         *
         * Used to allow 3rd party to avoid shortcode dynamic from escaping
         *
         * @since 2.2.1
         *
         * @param bool defaults to true
         */
        $should_escape = apply_filters( 'elementor_pro/dynamic_tags/shortcode/should_escape', true );

        if ( is_string($value) && $should_escape ) {
                $value = wp_kses_post( $value );
        }
        // PHPCS - the variable $value is safe.        
        return $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    
    /**
     * @since 2.0.0
     * @access public
     *
     * @param array $options
     *
     * @return string
     */
    public function get_content(array $options = []) {
        $this->is_data = (bool)$this->get_settings('e_shortcode_data');        
        return parent::get_content($options);
    }

}
