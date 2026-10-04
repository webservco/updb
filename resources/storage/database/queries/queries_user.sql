UPDATE up_acl_users SET user_status = 'suspended'
WHERE user_email NOT LIKE '%webserv.co%';