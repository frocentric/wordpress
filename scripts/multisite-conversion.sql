SET sql_mode = "";
-- To enable matching of old and new user ids.
ALTER TABLE wp_users
ADD COLUMN old_user_id bigint(20) unsigned;

INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name, old_user_id)
SELECT u2.user_login, u2.user_pass, u2.user_nicename, u2.user_email, u2.user_url, u2.user_registered, u2.user_activation_key, u2.user_status, u2.display_name, u2.id
FROM wp_2_users AS u2;

INSERT INTO wp_usermeta (user_id, meta_key, meta_value)
SELECT u.id, m2.meta_key, m2.meta_value
FROM wp_2_usermeta AS m2
JOIN wp_users AS u ON m2.user_id = u.old_user_id;

UPDATE wp_2_posts, wp_users
SET wp_2_posts.post_author = wp_users.id
WHERE wp_2_posts.post_author = wp_users.old_user_id;

UPDATE wp_2_comments, wp_users
SET wp_2_comments.user_id = wp_users.id
WHERE wp_2_comments.user_id = wp_users.old_user_id;

UPDATE wp_2_postmeta
SET wp_2_postmeta.meta_key = '_wp_attached_file'
WHERE wp_2_postmeta.meta_key = '_wp_2_attached_file';

ALTER TABLE wp_users
DROP COLUMN old_user_id;

alter table wp_2_nf3_action_meta convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_actions convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_chunks convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_field_meta convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_fields convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_form_meta convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_forms convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_object_meta convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_objects convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_relationships convert to character set utf8mb4 collate utf8mb4_general_ci;
alter table wp_2_nf3_upgrades convert to character set utf8mb4 collate utf8mb4_general_ci;

UPDATE wp_2_options SET option_value = REGEXP_REPLACE(option_value, "wp_2_", "wp_") WHERE option_value LIKE '%wp_2_%';
UPDATE wp_2_postmeta SET meta_value = REGEXP_REPLACE(meta_value, "wp_2_", "wp_") WHERE meta_value LIKE '%wp_2_%';