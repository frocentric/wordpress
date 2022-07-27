<?php

namespace EAddonsForElementor\Core\Traits;

/**
 * Description of User
 *
 * @author fra
 */
trait User {

    public static function get_user_roles() {
        $wp_roles = wp_roles()->roles;
        $roles = array();
        foreach ($wp_roles as $key => $value) {
            $roles[$key] = $value['name'];
        }
        return $roles;
    }

    public static function get_user_role($user = null) {
        if (!$user) {
            $user = wp_get_current_user();
        } else {
            $user = get_user_by('ID', (int) $user);
        }
        if ($user) {
            $roles = $user->roles;
            if (!empty($roles)) {
                return reset($roles);
            }
        }
        return false;
    }

    public static function get_user_fields($filter = false, $info = true) {
        $fields = self::get_fields(wp_get_current_user(), $filter, $info);

        $pos_key = is_string($filter) ? stripos('avatar', $filter) : false;
        if (empty($filter) || !is_string($filter) || $pos_key !== false) {
            $fields['avatar'] = 'Avatar';
        }
        return $fields;
    }

    public static function get_user_field($field = 'display_name', $user_id = null, $single = null) {
        $value = null;
        if (is_numeric($field) && intval($field)) {
            $tmp = $user_id;
            $user_id = $field;
            $field = $tmp;
        }
        $user_id = (is_numeric($user_id) && intval($user_id)) ? intval($user_id) : get_current_user_id();
        $user_id = (is_numeric($user_id) && intval($user_id)) ? intval($user_id) : get_the_author_meta('ID');
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $value = self::get_wp_object_field($user, $field, $single);            
        }
        return self::adjust_data($value, $single);        
    }
    
    public static function get_user_url($id = null) {        
        if (!$id) {
            $id = get_current_user_id();
        }
        if (!$id) {
            $id = get_the_author_meta('ID');
        }
        return get_author_posts_url($id);
    }
    
    public static function is_userdata_field($field = false) {
        $fields = array('locale', 'syntax_highlighting', 'avatar', 'nickname', 'first_name', 'last_name', 'description', 'rich_editing', 'role', 'jabber', 'aim', 'yim', 'show_admin_bar_front');
        if (in_array($field, $fields) || !self::is_meta($field, 'user')) {
            return true;
        }
        return false;
    }

}
