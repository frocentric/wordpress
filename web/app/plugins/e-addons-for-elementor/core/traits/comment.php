<?php

namespace EAddonsForElementor\Core\Traits;

/**
 * Description of Comment
 *
 * @author fra
 */
trait Comment {
    
    public static function get_comment_post($comment_id) {        
        $comment = get_comment($comment_id);
        return get_post($comment->comment_post_ID);
    }

    public static function get_comment_fields($filter = false, $info = true) {
        $comment = get_comment(1);
        return self::get_fields($comment, $filter, $info);
    }

    public static function get_comment_field($comment = null, $field = 'comment_content', $single = null) {      
        $value = null;
        $comment = self::get_comment($comment);
        if ($comment) {
            $value = self::get_wp_object_field($term, $field, $single);
        }
        return self::adjust_data($value, $single);
    }
    
    public static function get_comment_url($id = null) {        
        $comment = self::get_comment($comment);
        return get_permalink($comment->comment_post_ID);
    }
    
}
