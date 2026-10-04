DROP TABLE IF EXISTS `up_acl_object_user`;

CREATE OR REPLACE VIEW `up_acl_object_user` AS 
select `U`.*, `UC`.*
from (`up_acl_users` `U` left join `up_acl_users_custom` `UC` on((`UC`.`user_id_key` = `U`.`user_id`))) where (`U`.`user_status` = _utf8'active') order by `U`.`user_email`;

