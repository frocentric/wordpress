<?php

namespace EAddonsForElementor\Core\Controls\Traits;

use EAddonsForElementor\Core\Utils;

/**
 * @author francesco
 */
trait Base {

    /**
     * Register controls.
     *
     * Used to add new controls to any element type. For example, external
     * developers use this method to register controls in a widget.
     *
     * Should be inherited and register new controls using `add_control()`,
     * `add_responsive_control()` and `add_group_control()`, inside control
     * wrappers like `start_controls_section()`, `start_controls_tabs()` and
     * `start_controls_tab()`.
     *
     * @since 1.4.0
     * @access protected
     * @deprecated 3.1.0 Use `Controls_Stack::register_controls()` instead
     */
    /*protected function _register_controls() {
        $this->register_controls();
    }*/

}
