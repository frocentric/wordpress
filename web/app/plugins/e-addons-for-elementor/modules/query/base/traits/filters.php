<?php

namespace EAddonsForElementor\Modules\Query\Base\Traits;

use Elementor\Controls_Manager;

/**
 * Description of label
 *
 * @author fra
 */
trait Filters {
    
    public static $dates_ago = ['days', 'weeks', 'months', 'years'];
    
    public function get_date_options($param = '') {
        $dates = array(
                'past' => esc_html__('Default (Past)', 'e-addons'),
                'period' => esc_html__('Period', 'e-addons'),
                'today' => esc_html__('Today', 'e-addons'),
                'yesterday' => esc_html__('Yesterday', 'e-addons'),                                               
            );
        
        foreach (self::$dates_ago as $date) {
            $dates[$date] = esc_html__(ucfirst($date).' ago', 'e-addons');
        }
        if ($param) {
            $dates[$param] = ucfirst($param);
        }
        return $dates;
    }

    public function get_date_filter($settings) {
        /* -------- DATE ------- */
        // compare by user registration date
        $date_args = array();
        if (!empty($settings['querydate_mode'])) {
            $tmp = array(
                'inclusive' => true,
            );
            if (!empty($settings['querydate_field'])) {
                $tmp['column'] = $settings['querydate_field'];
            }

            switch ($settings['querydate_mode']) {
                case 'period':
                    $tmp['after'] = $settings['querydate_date_from'];
                    $tmp['before'] = $settings['querydate_date_to'];
                    break;
                case 'today':
                    $tmp['after'] = date('Y-m-d 00:00:00');
                    $tmp['before'] = date('Y-m-d 23:23:59');
                    break;
                case 'yesterday':
                    $tmp['after'] = date('Y-m-d 00:00:00', strtotime('-1 day'));
                    $tmp['before'] = date('Y-m-d 23:23:59', strtotime('-1 day'));
                    break;
                case 'past':
                    $tmp['before'] = date('Y-m-d H:i:s');
                    break;
                default:
                    $tmp['after'] = '-' . $settings['querydate_range'] . ' ' . $settings['querydate_mode'];
                    $tmp['before'] = 'now';
                    break;
            }

            $date_args['date_query'] = array($tmp);
        }
        return $date_args;
    }

}
