<?php

namespace EAddonsForElementor\Modules\Query\Tabs;

use \EAddonsForElementor\Base\Base_Tab;

use Elementor\Controls_Manager;

/**
 * Description of display
 *
 * @author fra
 */
class Query extends Base_Tab {

    public function get_id() {
        return 'e_' . $this->get_name();
    }

    public function get_title() {
        return esc_html__(ucfirst($this->get_name()), 'elementor');
    }

}
