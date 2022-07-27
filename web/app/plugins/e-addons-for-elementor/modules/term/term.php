<?php

namespace EAddonsForElementor\Modules\Term;

use EAddonsForElementor\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Term extends Module_Base {

    public function __construct() {
        parent::__construct();
    }

    public function get_term_id() {
        $term = get_queried_object();
        if ($term && is_object($term) && get_class($term) == 'WP_Term') {
            return $term->term_id;
        }
        return false;
    }

}
