<?php

namespace EAddonsForElementor\Base;

use EAddonsForElementor\Core\Utils;
use Elementor\Element_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Base_Global extends Element_Base {

    use \EAddonsForElementor\Base\Traits\Base;

    public function get_icon() {
        return 'eicon-globe';
    }

}
