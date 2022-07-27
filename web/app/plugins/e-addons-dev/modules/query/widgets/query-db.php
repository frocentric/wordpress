<?php

namespace EAddonsDev\Modules\Query\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;
use EAddonsForElementor\Modules\Query\Base\Query as Base_Query;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Query Items
 *
 * Elementor widget for E-Addons
 *
 */
class Query_Db extends Base_Query {
    public static $db = [];

    public static $e_submissions_fields = array('id', 'type', 'hash_id', 'main_meta_id', 'post_id', 'referer', 'referer_title', 'element_id', 'form_name', 'campaign_id', 'user_id', 'user_ip', 'user_agent', 'actions_count', 'actions_succeeded_count', 'status', 'is_read', 'meta', 'created_at_gmt', 'updated_at_gmt', 'created_at', 'updated_at');

    use Traits\Common;

    public function get_pid() {
        return 8353;
    }

    public function get_icon() {
        return 'eadd-query-custom-db';
    }

    public function get_name() {
        return 'e-query-db';
    }

    public function get_title() {
        return esc_html__('Query DB', 'e-addons');
    }

    public function get_categories() {
        return ['query-dev'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 2.1.0
     * @access public
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return ['sql', 'custom', 'database'];
    }

    protected $querytype = 'db';

    protected function register_controls() {
        parent::register_controls();

        $this->controls_dev_common_content();

        $this->start_controls_section(
                'section_query_db', [
            'label' => '<i class="eaddicon eicon-settings" aria-hidden="true"></i> ' . esc_html__('Query', 'e-addons'),
            'tab' => 'e_query',
                ]
        );

        $this->add_control(
                'query_debug', [
            'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show query for DEBUG', 'e-addons') . '</span>',
            'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->add_control(
                'query_type', [
            'label' => esc_html__('Query Type', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'separator' => 'before',
            'label_block' => true,
            'options' => [
                '' => esc_html__('Custom SQL', 'e-addons'),
                'table' => esc_html__('Table', 'e-addons'),
                'e_submissions' => esc_html__('PRO Form Submission', 'e-addons'),
            ],
                ]
        );

        $this->add_control(
                'query_db_custom', [
            'label' => esc_html__('Custom SQL', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'separator' => 'before',
            'condition' => [
                'query_type' => '',
            ],
                ]
        );

        $this->add_control(
                'query_db_custom_preview', [
            'label' => esc_html__('Disable execution in preview'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'query_type' => '',
            ],
                ]
        );

        $this->add_control(
                'query_db_table', [
            'label' => esc_html__('DB Table', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_type' => 'table',
            ],
                ]
        );
        $this->add_control(
                'query_db_table_prefix', [
            'label' => esc_html__('Add WP Table Prefix', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'query_type' => ['table'],
                'query_db_table!' => '',
            ],
                ]
        );
        
        $this->add_control(
                'query_db_table_wp', [
            'label' => esc_html__('Add support for WP Meta tables'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'description' => esc_html__('Add all Object Meta values to current row. Working for "posts", "users", "terms" and "comments".'),
            'condition' => [
                'query_type' => ['table'],
                'query_db_table!' => '',
            ],
                ]
        );
        
        $this->add_control(
                'query_db_user', [
            'label' => esc_html__('DB User', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_type' => ['table', ''],
            ],
                ]
        );
        $this->add_control(
                'query_db_password', [
            'label' => esc_html__('DB Password', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_db_user!' => '',
            ],
                ]
        );
        $this->add_control(
                'query_db_name', [
            'label' => esc_html__('DB Name', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_db_user!' => '',
            ],
                ]
        );

        $this->add_control(
                'query_db_host', [
            'label' => esc_html__('DB Host', 'e-addons'),
            'placeholder' => 'localhost:3306',
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_db_user!' => '',
            ],
                ]
        );

        $this->add_control(
                'rows_per_page', [
            'label' => esc_html__('Number of Rows', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'default' => '10',
            'description' => esc_html__('Number of Results per Page, leave empty for global configuration or -1 to display all'),
            'condition' => [
                'query_type' => ['table', 'e_submissions'],
            ],
                ]
        );
        $this->add_control(
                'offset', [
            'label' => esc_html__('Rows Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'condition' => [
                'query_type' => ['table', 'e_submissions'],
            //'rows_per_page!' => '-1'
            ],
                ]
        );
        $this->add_control(
                'limit', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'condition' => [
                'query_type' => ['table', 'e_submissions'],
            //'rows_per_page!' => '-1'
            ],
                ]
        );

        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control(
                'where_field', [
            'label' => esc_html__('Field Key', 'e-addons'),
            'description' => esc_html__('Is the key of the Field in the DB Table', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );
        $repeater_fields->add_control(
                'where_operator', [
            'label' => esc_html__('Compare Operator', 'elementor'),
            'description' => esc_html__('Comparison operator. Default value is (=)', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => Query_Utils::get_meta_compare(),
            'default' => '=',
            'label_block' => true
                ]
        );
        $repeater_fields->add_control(
                'where_value', [
            'label' => esc_html__('Field Value', 'e-addons'),
            'description' => esc_html__('Is the value of the Field in the Form', 'e-addons'),
            'type' => Controls_Manager::TEXT,
                ]
        );

        $repeater_fields->add_control(
                'where_logic', [
            'label' => esc_html__('Field Logic', 'e-addons'),
            'default' => 'AND',
            'type' => Controls_Manager::HIDDEN,
                ]
        );
        $this->add_control(
                'where', [
            'label' => esc_html__('Where', 'e-addons'),
            'description' => esc_html__('All conditions are in AND, if you need a more complex logic switch to Custom SQL mode', 'e-addons'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater_fields->get_controls(),
            'title_field' => '{{{ where_field }}} {{{ where_operator }}} {{{ where_value }}}',
            'prevent_empty' => false,
            'condition' => [
                'query_type' => ['table', 'e_submissions'],
            ],
                ]
        );

        $this->add_control(
                'orderby', [
            'label' => esc_html__('Order By', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_type' => ['table', 'e_submissions'],
            ],
                ]
        );
        $this->add_control(
                'order', [
            'label' => esc_html__('Order', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'ASC' => 'Ascending',
                'DESC' => 'Descending'
            ],
            'default' => 'DESC',
            'condition' => [
                'query_type' => ['table', 'e_submissions'],
                'orderby!' => ['', 'random'],
            ],
                ]
        );

        $this->end_controls_section();
        
        $this->add_no_result_section();
    }
    
    public function get_table($settings) {
        $table = $settings['query_db_table'];
        if ($settings['query_db_table_prefix']) {
            global $wpdb;
            $table = $wpdb->prefix . $table;
        }
        return $table;
    }

    public static function get_db($settings = []) {
        /*
        if ($skin) {
            $settings = $skin->parent->get_settings_for_display();
        } else {
            $settings = $this->get_settings_for_display();
        }
        */
        if (!empty($settings['query_db_user'])) {
            $db_name = $settings['query_db_name'] ? $settings['query_db_name'] : DB_NAME;
            $db_host = $settings['query_db_host'] ? $settings['query_db_host'] : DB_HOST;
            $mydb = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); // prevent the critical error
            $mydb->dbuser = $settings['query_db_user'];
            $mydb->dbpassword = $settings['query_db_password'];
            $mydb->dbname = $db_name;
            $mydb->dbhost = $db_host;
            
            $db_key = $settings['query_db_user'].'-'.$settings['query_db_password'].'-'.$db_name.'-'.$db_host;
            if (!empty(self::$db[$db_key])) {
                return self::$db[$db_key];
            }
            //$mydb = new \wpdb($settings['query_db_user'], $settings['query_db_password'], $db_name, $db_host);
            //var_dump($mydb); die();
            if ($mydb->db_connect(false)) {
                //$mydb = new \wpdb($settings['query_db_user'], $settings['query_db_password'], $db_name, $db_host);
                self::$db[$db_key] = $mydb;
                return $mydb;
            } else {
                echo esc_html__('Error establishing a database connection') . ', ' . esc_html__('This either means that the username and password information is incorrect or we can&#8217;t contact the database server at '.$db_host.'. This could mean your host&#8217;s database server is down.');
            }
            /*
            try {
                $conn = new \PDO("mysql:host=".$db_host.";dbname=myDB", $settings['query_db_user'], $settings['query_db_password']);
                // set the PDO error mode to exception
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return $conn;
            } catch(\PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            */
        }

        global $wpdb;
        return $wpdb;
    }

    // La QUERY
    public function query_the_elements() {

        $settings = $this->get_settings_for_display();

        $wpdb = self::get_db($settings);

        switch ($settings['query_type']) {
            case 'table':
            case 'e_submissions':
                $sql = 'SELECT * FROM ';

                $table = false;
                if ($settings['query_type'] == 'e_submissions') {
                    $table .= $wpdb->prefix . 'e_submissions';
                    $sql .= '`' . $table . '`';
                } else if (!empty($settings['query_db_table'])) {
                    $table = $this->get_table($settings);
                    $sql .= '`' . $table . '`';
                }

                if (!empty($table)) {

                    //$mydb->hide_errors();

                    if (!empty($settings['where'])) {
                        $where = '';

                        $submissions_id = array();
                        foreach ($settings['where'] as $key => $afield) {

                            $value = $afield['where_value'];
                            $operator = $afield['where_operator'];
                            switch ($afield['where_operator']) {
                                case 'LIKE':
                                case 'RLIKE':
                                case 'NOT LIKE':
                                    $value = '%' . $value . '%';
                                    break;
                                case 'IN':
                                case 'NOT IN':
                                    $operator .= ' (' . $value . ')';
                                    $value = false;
                                    break;
                                case 'BEETWEEN':
                                case 'NOT BEETWEEN':
                                    list($start, $end) = Utils::explode($value);
                                    $operator = $operator . ' ' . $start . ' AND ' . $end;
                                    $value = false;
                                    break;
                            }

                            if ($settings['query_type'] == 'e_submissions') {
                                if (!in_array($afield['where_field'], self::$e_submissions_fields)) {
                                    $values = $wpdb->get_col("SELECT `submission_id` FROM `" . $wpdb->prefix . "e_submissions_values` WHERE `key` = '" . $afield['where_field'] . "' AND `value` " . $operator . " '" . $value . "'");
                                    $submissions_id = empty($submissions_id) ? $values : array_intersect($submissions_id, $values);
                                    continue;
                                }
                            }

                            if ($key) {
                                $where .= ' ' . $afield['where_logic'];
                            }
                            $where .= " `" . $afield['where_field'] . "` " . $operator . ($value !== false ? " '" . $value . "'" : '');
                        }
                        //if (strpos($sql, ' WHERE ') === false) {
                        $sql .= ' WHERE';
                        //}

                        if (!empty($submissions_id) && $settings['query_type'] == 'e_submissions') {
                            //$sql .= ' LEFT JOIN `'.$wpdb->prefix . 'e_submissions_values`';
                            //$sql .= ' ON `'.$wpdb->prefix . 'e_submissions`.`id` = `'.$wpdb->prefix . 'e_submissions_values`.`submission_id`';
                            if (!empty($where)) {
                                $sql .= ' AND';
                            }
                            $sql .= ' `id` IN (' . implode(',', $submissions_id) . ')';
                        }

                        $sql .= $where;
                    }

                    if (!empty($settings['orderby'])) {
                        $direction = $settings['order'] ? ' ' . $settings['order'] : '';
                        if ($settings['orderby'] == 'random') {
                            $orderby = 'RAND()';
                            $direction = '';
                        } else {
                            $orderby = '`' . $settings['orderby'] . '`';
                        }
                        $sql .= ' ORDER BY ' . $orderby . $direction;
                    }

                    if (!empty($settings['pagination_enable'])) {
                        $paged = $this->get_current_page();
                        $limit = $settings['rows_per_page'] ? $settings['rows_per_page'] : get_option('posts_per_page');                        
                        $start = $limit * ($paged - 1);
                        $sql .= ' LIMIT ' . $start . ',' . $limit; //.' OFFSET '.$offset;
                    } else {
                        if (!empty($settings['limit'])) {
                            $limit = $settings['limit'];
                            $offset = intval($settings['offset']);
                            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
                        }
                    }
                }

                break;
            default:
                $sql = $settings['query_db_custom'];
                $sql = Utils::get_dynamic_data($sql);
        }

        // DEBUG
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty($settings['query_debug'])) {
                echo '<pre>';
                echo $sql;
                echo '</pre>';
            }
        }
        //var_dump($sql);
        //do_action('elementor/query/query_results', $query, $this);         
        // secure 
        if (stripos($sql, 'update ') !== false || stripos($sql, 'delete ') !== false || stripos($sql, 'insert ') !== false) {
            return false;
        }
        
        $this->query = $sql;
    }

    public function loop($skin, $query) {
        $settings = $skin->parent->get_settings_for_display();

        if (!empty($settings['query_db_custom_preview']) && Utils::is_preview()) {
            return;
        }

        $wpdb = self::get_db($settings);
        $results = $wpdb->get_results($query, 'ARRAY_A');
        // use mysql
        //var_dump($results);

        $offset = intval($settings['offset']);
        $limit = count($results);
        if (empty($settings['pagination_enable'])) {
            if (intval($settings['rows_per_page']) > 0) {
                $limit = intval($settings['rows_per_page']);
            }
        }
        if (intval($settings['limit']) > 0) {
            $limit = intval($settings['limit']);
        }
        
        $i = $j = 0;
        foreach ($results as $key => $row) {

            $i++;
            $continue = false;
            if ($limit) {
                if ($offset) {
                    if ($i <= $offset) {
                        $continue = true;
                    }
                }
                if (!$continue) {
                    $j++;
                }
                if ($j > $limit) {
                    $continue = true;
                }
            }

            if (!$continue) {

                if ($settings['query_type'] == 'e_submissions') {
                    $values = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'e_submissions_values` WHERE `submission_id` = ' . $row['id'], 'ARRAY_A');
                    foreach ($values as $avalue) {
                        if (empty($row[$avalue['key']])) {
                            $row[$avalue['key']] = $avalue['value'];
                        }
                    }
                }
                
                if ($settings['query_type'] == 'table' && !empty($settings['query_db_table_wp'])) {
                    $table = $this->get_table($settings);
                    $table_meta = false;
                    if ($table == $wpdb->posts && !empty($row['ID'])) {
                        $table_meta = $wpdb->postmeta;
                        $obj_id = $row['ID'];
                        $obj_key = 'post_id';
                    }
                    if ($table == $wpdb->users && !empty($row['ID'])) {
                        $table_meta = $wpdb->usermeta;
                        $obj_id = $row['ID'];
                        $obj_key = 'user_id';
                    }
                    if ($table == $wpdb->terms && !empty($row['term_id'])) {
                        $table_meta = $wpdb->termmeta;
                        $obj_id = $row['term_id'];
                        $obj_key = 'term_id';
                    }
                    if ($table == $wpdb->comments && !empty($row['comment_ID'])) {
                        $table_meta = $wpdb->commentmeta;
                        $obj_id = $row['comment_ID'];
                        $obj_key = 'comment_id';
                    }
                    if ($table_meta) {
                        $sql = 'SELECT * FROM `' . $table_meta . '` WHERE `'.$obj_key.'` = ' . $obj_id;
                        //var_dump($sql);
                        $values = $wpdb->get_results($sql, 'ARRAY_A');
                        //var_dump($values);
                        foreach ($values as $avalue) {
                            if (empty($row[$avalue['meta_key']])) {
                                $row[$avalue['meta_key']] = $avalue['meta_value'];
                            }
                        }
                    }
                }

                //$skin->current_permalink = false;
                $skin->current_id = empty($row->ID) ? false : $row->ID;
                $skin->current_data = $row;
                //
                $skin->render_element_item();
            }
        }
    }

    public function should_render($render, $skin, $query) {
        $settings = $skin->parent->get_settings_for_display();
        $wpdb = self::get_db($settings);
        $results = $wpdb->get_results($query);
        //var_dump($results); 
        if (empty($results)) { //->num_rows)) {
            $render = false;
        }
        return $render;
    }

    public function pagination__page_limit($page_limit, $skin, $query, $settings) {
        $no = $settings['rows_per_page'];
        if ($no) {
            $mydb = self::get_db($settings);
            if (strpos($query, 'LIMIT') !== false) {
                list($query, $where) = explode('LIMIT', $query, 2);
            }
            $results = $mydb->get_results($query);
            //var_dump($results); die();
            //$total_rows = $results->num_rows;
            $total_rows = count($results);
            $page_limit = ceil($total_rows / $no);
        }
        return $page_limit;
    }

}
