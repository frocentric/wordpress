<?php
namespace EAddonsDev\Modules\Query;

use EAddonsForElementor\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Query extends Module_Base {

    public function __construct() {
        parent::__construct();
        //add_action('elementor_pro/init', [$this, 'after_init_actions'], 11);  
        
        add_action( 'elementor_pro/init', function () {
            if (!empty($_GET['e_act']) && in_array($_GET['e_act'], ['export', 'spreadsheet', 'json', 'xml'])) {
                require_once \EAddonsForElementor\Core\Utils::get_plugin_path(__FILE__) . 'modules' . DIRECTORY_SEPARATOR . 'query' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'download.php';;
            }
        }, 9);

    }

}
