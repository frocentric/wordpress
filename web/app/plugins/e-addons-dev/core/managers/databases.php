<?php

namespace EAddonsDev\Core\Managers;

use EAddonsForElementor\Core\Utils;

/**
 * Description of database
 *
 * @author Fra
 */
class Databases {
    
    public static $db = [];
    
    public static function get_db($settings = []) {
        global $wpdb;
        
        if ($settings['query_db_conn'] == 'db') {
            $settings['query_db_dns'] = get_option('elementor_db_dns');
            $settings['query_db_user'] = get_option('elementor_db_user');
            $settings['query_db_password'] = get_option('elementor_db_password');
            $settings['query_db_name'] = get_option('elementor_db_name');
            $settings['query_db_host'] = get_option('elementor_db_host');
        }
        $settings['query_db_dns'] = trim($settings['query_db_dns']);
        $settings['query_db_user'] = trim($settings['query_db_user']);
        $settings['query_db_password'] = trim($settings['query_db_password']);
        $settings['query_db_name'] = trim($settings['query_db_name']);
        $settings['query_db_host'] = trim($settings['query_db_host']);
        
        if (!empty($settings['query_db_dns'])) {
            $db_key = $settings['query_db_dns'].'-'.$settings['query_db_user'].'-'.$settings['query_db_password'];
            if (!empty(self::$db[$db_key])) {
                return self::$db[$db_key];
            }
            try {
                $conn = new \PDO($settings['query_db_dns'], $settings['query_db_user'], $settings['query_db_password']);
                if (WP_DEBUG) {
                    // set the PDO error mode to exception
                    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                }
                
                // add standard wpdb props
                foreach (['prefix','posts','postmeta','users','usermeta','terms','termmeta','comments','commentmeta'] as $prop) {
                    if (empty($conn->{$prop})) {
                        $conn->{$prop} = $wpdb->{$prop};
                    }
                }

                self::$db[$db_key] = $conn;
                return $conn;
            } catch(\PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }

        if (!empty($settings['query_db_user'])) {

            $db_name = $settings['query_db_name'] ? $settings['query_db_name'] : DB_NAME;
            $db_host = $settings['query_db_host'] ? $settings['query_db_host'] : DB_HOST;
            $mydb = new \wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); // prevent the critical error
            $mydb->dbuser = $settings['query_db_user'];
            $mydb->dbpassword = $settings['query_db_password'];
            $mydb->dbname = $db_name;
            $mydb->dbhost = $db_host;
            $mydb->show_errors(false);
            $db_key = $settings['query_db_user'].'-'.$settings['query_db_password'].'-'.$db_name.'-'.$db_host;
            //var_dump($db_key);
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
        }
        
        return $wpdb;
    }
    
    public static function is_read_only($sql) {
        return stripos($sql, 'update ') === false && stripos($sql, 'delete ') === false && stripos($sql, 'insert ') === false;
    }
}
