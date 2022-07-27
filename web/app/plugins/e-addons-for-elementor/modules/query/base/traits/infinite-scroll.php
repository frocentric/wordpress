<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

/**
 * Description of infinite-scroll
 *
 * @author fra
 */
trait Infinite_Scroll {

    protected function add_infinite_scroll_section() {
        // ------------------------------------------------------------------ [SECTION INFINITE SCROLL]
        $this->start_controls_section(
                'section_infinitescroll', [
            'label' => '<i class="eaddicon eicon-navigation-horizontal" aria-hidden="true"></i> ' . esc_html__('Infinite Scroll', 'e-addons'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                '_skin' => ['', 'grid', 'filters', 'timeline', 'list', 'table'],
                'infiniteScroll_enable' => 'yes',
                'query_type' => ['automatic_mode', 'get_cpt', 'get_tax', 'get_users_and_roles', 'get_attachments']
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_trigger', [
            'label' => esc_html__('Trigger', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'default' => 'button',
            'frontend_available' => true,
            'options' => [
                'button' => esc_html__('On Click Button', 'e-addons'),
                'scroll' => esc_html__('On Scroll Page', 'e-addons'),
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_button', [
            'label' => esc_html__('Label Button', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('View more', 'e-addons'),
            'condition' => [
                'infiniteScroll_trigger' => 'button',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_enable_status', [
            'label' => esc_html__('Enable Status', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before',
                ]
        );

        $this->add_control(
                'infiniteScroll_show_preview', [
            'label' => esc_html__('Show Status PREVIEW in Editor Mode', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_loading_type', [
            'label' => esc_html__('Loading Type', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
            'options' => [
                'ellips' => [
                    'title' => esc_html__('Ellips', 'e-addons'),
                    'icon' => 'fa fa-ellipsis-h',
                ],
                'text' => [
                    'title' => esc_html__('Label Text', 'e-addons'),
                    'icon' => 'fa fa-font',
                ]
            ],
            'default' => 'ellips',
            'separator' => 'before',
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_loading', [
            'label' => esc_html__('Label Loading', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Loading...', 'e-addons'),
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
                'infiniteScroll_loading_type' => 'text',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_last', [
            'label' => esc_html__('Label Last', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('End of content', 'e-addons'),
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
                'infiniteScroll_loading_type' => 'text',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_label_error', [
            'label' => esc_html__('Label Error', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('No more articles to load', 'e-addons'),
            'condition' => [
                'infiniteScroll_enable_status' => 'yes',
                'infiniteScroll_loading_type' => 'text',
            ],
                ]
        );
        $this->add_control(
                'infiniteScroll_enable_history', [
            'label' => esc_html__('Enable History', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'infiniteScroll_prefill', [
            'label' => esc_html__('Prefill', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'frontend_available' => true,
                ]
        );

        $this->end_controls_section();
    }

    protected function render_infinite_scroll() {
        // Infinite scroll pagination -----------------------------------------------
        // @p ..infiniteScroll è abilitato e anche se i post generati sono maggiori dei post visualizzati..
        if ($this->get_settings_for_display('infiniteScroll_enable')) {
            $query = $this->get_query();
            $querytype = $this->get_querytype();
            $settings = $this->get_settings_for_display();

            $page_limit = intval(apply_filters('e_addons/query/page_limit/'.$querytype, 1, $this, $query, $settings));
            $per_page = intval(apply_filters('e_addons/query/per_page/'.$querytype, get_option('posts_per_page'), $this, $query, $settings));
            $page_length = intval(apply_filters('e_addons/query/page_length/'.$querytype, get_option('posts_per_page'), $this, $query, $settings));

            if (( $page_length >= $per_page && $per_page >= 0) ||
                    \Elementor\Plugin::$instance->editor->is_edit_mode()
            ) {
                // previewmode è una versione utile mentre mi trovo in editor per vedere la situazione degli status
                $preview_mode = '';
                if (\Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['infiniteScroll_show_preview']) {
                    $preview_mode = ' visible';
                }
                //  @p show status
                if ($settings['infiniteScroll_enable_status']) {
                    ?>
                    <nav class="e-add-infiniteScroll e-add-infiniteScroll-status" role="navigation">
                        <div class="e-add-page-load-status e-add-page-load-status-<?php echo $this->get_id() . $preview_mode; ?>">
                            <?php
                            if ($settings['infiniteScroll_loading_type'] == 'text') {
                                ?>
                                <div class="infinite-scroll-request status-text"><?php echo __($settings['infiniteScroll_label_loading'], 'e-addons' . '_strings'); ?></div>
                                <?php
                            } else if ($settings['infiniteScroll_loading_type'] == 'ellips') {
                                ?>
                                <div class="loader-ellips infinite-scroll-request">
                                    <span class="loader-ellips__dot"></span>
                                    <span class="loader-ellips__dot"></span>
                                    <span class="loader-ellips__dot"></span>
                                    <span class="loader-ellips__dot"></span>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="infinite-scroll-last status-text"><?php echo __($settings['infiniteScroll_label_last'], 'e-addons' . '_strings'); ?></div>
                            <div class="infinite-scroll-error status-text"><?php echo __($settings['infiniteScroll_label_error'], 'e-addons' . '_strings'); ?></div>

                            <div class="e-add-infinite-scroll-paginator" role="navigation">
                                <a class="e-add-infinite-scroll-paginator__next e-add-infinite-scroll-paginator__next-<?php echo $this->get_id(); ?>" href="<?php echo $this->get_next_pagination(); ?>"><?php //echo __('Next', 'e-addons');    ?></a>
                            </div>
                        </div>
                    </nav>
                    <?php
                } // end show status

                // The Button ...
                if ($settings['infiniteScroll_trigger'] == 'button') {
                    ?>
                    <div class="e-add-infiniteScroll e-add-infiniteScroll-btn">
                        <button class="e-add-view-more-button e-add-view-more-button-<?php echo $this->get_id(); ?>"><?php echo __($settings['infiniteScroll_label_button'], 'e-addons' . '_strings'); ?></button>
                    </div>
                    <?php
                }
            } // end infinitescroll enable
        }
        // --------------------------------------------------------------------
    }

    /*
    public static function infinite_scroll_pagination($settings) {
        $query = $this->get_query();
        $query_class = get_class($query);
        $iargs = $query->query_vars;
        $iargs['nopaging'] = false;
        $iargs['posts_per_page'] = -1;
        $iargs['fields'] = 'ids';
        $iquery = new $query_class($iargs);
        $count = $iquery->post_count;
        echo '<div class="">Hai visualizzato <span>'.$args['posts_per_page'].'</span> di <span>'.$count.'</span> prodotti</div>';
    }
    */

}