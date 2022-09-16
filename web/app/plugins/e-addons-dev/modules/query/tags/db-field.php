<?php

namespace EAddonsDev\Modules\Query\Tags;

//use Elementor\Core\DynamicTags\Tag;
use \Elementor\Controls_Manager;
use EAddonsForElementor\Core\Utils;
use Elementor\Modules\DynamicTags\Module;
use EAddonsForElementor\Base\Base_Tag;
use EAddonsDev\Core\Managers\Databases;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Db_Field extends Base_Tag {

    public function get_name() {
        return 'e-tag-db-field';
    }
    /**
    public function get_icon() {
        return 'eadd-dynamic-tag-rowcustomfield';
    }
    
    public function get_pid() {
        return 35123;
    }
    */
    public function get_title() {
        return esc_html__('DB Field', 'e-addons');
    }

    public function get_group() {
        return 'site';
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
     */
    protected function register_controls() {

        $this->add_control(
                'query_db_custom', [
            'label' => esc_html__('Custom SQL', 'e-addons'),
            'type' => Controls_Manager::CODE,
            'rows' => 2,
                ]
        );
         
        $this->add_control(
                'query_db_conn', [
            'label' => esc_html__('DB Connection', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => esc_html__('Default', 'e-addons'),
                'mysql' => esc_html__('Custom MySQL', 'e-addons'),
                'pdo' => esc_html__('PDO', 'e-addons'),
                'db' => esc_html__('Elementor DB', 'e-addons'),
            ],
                ]
        );
        
        $this->add_control(
                'query_db_dns', [
            'label' => esc_html__('DNS'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'placeholder' => 'mysql:host=my_hostname;dbname=my_dbname',
            'condition' => [
                'query_db_conn' => 'pdo',
            ],
                ]
        );
        
        $this->add_control(
                'query_db_user', [
            'label' => esc_html__('DB User', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_db_conn' => ['pdo', 'mysql'],
            ],
                ]
        );
        $this->add_control(
                'query_db_password', [
            'label' => esc_html__('DB Password', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_db_user!' => '',
                'query_db_conn' => ['pdo', 'mysql'],
            ],
                ]
        );
        $this->add_control(
                'query_db_name', [
            'label' => esc_html__('DB Name', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'query_db_user!' => '',
                'query_db_conn' => 'mysql',
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
                'query_db_conn' => 'mysql',
            ],
                ]
        );
        
        $this->add_control(
                'query_db_custom_preview', [
            'label' => esc_html__('Disable execution in preview'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
                ]
        );
        
        /*$this->add_control(
                'json_decode',
                [
                    'label' => esc_html__('JSON Decode', 'e-addons') ,
                    'type' => Controls_Manager::SWITCHER,                    
                    'condition' => [
                        //'category' => ['array'],
                    ]
                ]
        );*/
        
        $this->add_control(
                'filters',
                [
                    'label' => esc_html__('Filters', 'e-addons') ,
                    'type' => Controls_Manager::TEXTAREA,                    
                    'placeholder' => 'trim',
                    'rows' => 2,
                ]
        );

        Utils::add_help_control($this);
    }
    
    public function render() {
        $settings = $this->get_settings();
        if (empty($settings))
            return;

        $value = $this->get_field_value();
        echo Utils::to_string($value);

    }

    public function get_field_value() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;
        
        $db = Databases::get_db($settings);
        //var_dump($db);
        if ($db) {    
            $sql = Utils::get_dynamic_data($settings['query_db_custom']);
            if (Utils::is_preview() && !empty($settings['query_db_custom_preview'])) {
                return $sql;
            }
            //var_dump($sql);
            $error = false;
            $values = false;
            if (is_a($db, 'wpdb')) {
                $values = $db->get_results($sql, ARRAY_A);
                if (empty($values)) {
                    $error = $db->last_error;
                }
            } else {
                $sth = $db->prepare($sql);
                if ($sth && $sth->execute()) {
                    $values = $sth->fetchAll(\PDO::FETCH_ASSOC);
                }
                if (empty($values)) {
                    $error = $db->errorInfo();
                }
            }
            //var_dump($values);
            //var_dump($error);
            if (empty($values)) {
                if ($error && Utils::is_preview()) {
                    return $error;
                }
            } else {
                if (count($values) == 1) {
                    $row = reset($values);
                    if (count($row) == 1) {
                        $field = reset($row);
                        //var_dump($field);
                        $field = maybe_unserialize($field);
                        $field = Utils::maybe_json_decode($field, true);
                        //var_dump($field);
                        if (!empty($settings['filters'])) {
                            $field = Utils::apply_filters($field, $settings['filters']);
                        }
                        return $field;
                    }
                } else {
                
                    $options = [];
                    foreach ($values as $row) {
                        if (count($row) == 2) {
                            foreach (['id', 'ID', 'Id'] as $id) {
                                if (isset($row[$id])) {
                                    $id = $row[$id];
                                    unset($row[$id]);
                                    $field = reset($row);
                                    $field = maybe_unserialize($field);
                                    $field = Utils::maybe_json_decode($field);
                                    if (!empty($settings['filters'])) {
                                        $field = Utils::apply_filters($field, $settings['filters']);
                                    }
                                    $options[$id] = $field;
                                }
                            }
                            if (empty($options)) {
                                $options[] = Utils::to_string($row);
                            }
                        } else {
                            $options[] = Utils::to_string($row);
                        }
                    }
                    return \EAddonsForElementor\Core\Utils\Form::array_to_options($options, null, null, true);
                    
                }
            }
        }
        
        return false;
        
    }
   
}