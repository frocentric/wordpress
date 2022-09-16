<?php
namespace EAddonsDev\Modules\Query;

use EAddonsForElementor\Base\Module_Base;
use Elementor\Settings;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Query extends Module_Base {

    public function __construct() {
        parent::__construct();
        //add_action('elementor_pro/init', [$this, 'after_init_actions'], 11);  
        
        add_action("elementor/admin/after_create_settings/elementor", array($this, 'e_addons_db'));
        
        add_action( 'elementor_pro/init', function () {
            if (!empty($_GET['e_act']) && in_array($_GET['e_act'], ['export', 'spreadsheet', 'json', 'xml'])) {
                require_once \EAddonsForElementor\Core\Utils::get_plugin_path(__FILE__) . 'modules' . DIRECTORY_SEPARATOR . 'query' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'download.php';;
            }
        }, 9);

    }
    
    public function e_addons_db(Settings $settings) {
        
        $settings->add_section(Settings::TAB_INTEGRATIONS, 'db', [
            //'label' => esc_html__('Flatpickr Datepicker', 'elementor-pro'),
            'callback' => function() {
                    echo '<hr><h2>' . esc_html__( 'Alternative DB', 'elementor-pro' ) . '</h2>';                  
            },
            'fields' => [
                'db_user' => [
                    'label' => esc_html__('DB User', 'elementor-pro'),
                    'field_args' => [
                        'type' => 'text'
                    ],
                ],
                'db_password' => [
                    'label' => esc_html__('DB Password', 'elementor-pro'),
                    'field_args' => [
                        'type' => 'password'
                    ],
                ],
                'db_name' => [
                    'label' => esc_html__('DB Name', 'elementor-pro'),
                    'field_args' => [
                        'type' => 'text'
                    ],
                ],
                'db_host' => [
                    'label' => esc_html__('DB Host', 'elementor-pro'),
                    'field_args' => [
                        'type' => 'text'
                    ],
                ],
                'db_dns' => [
                    'label' => esc_html__('DB DNS (PDO)', 'elementor-pro'),
                    'field_args' => [
                        'type' => 'text'
                    ],
                ],
            ],
        ]);
    }

}
