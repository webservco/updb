DROP TABLE IF EXISTS up_pub_download;
CREATE TABLE up_pub_download (
    `download_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `pub_issue_id` INT(10) UNSIGNED NOT NULL,
    file_name VARCHAR(90) NOT NULL,
    content_type VARCHAR(45) NOT NULL,

    PRIMARY KEY (download_id),
    KEY (pub_issue_id)

) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

DROP TABLE IF EXISTS up_pub_download_log;
CREATE TABLE up_pub_download_log (
    `log_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `download_id` INT(10) UNSIGNED NOT NULL,
    `user_id` INT(10) UNSIGNED NOT NULL,
    `user_ip` int(10) unsigned NOT NULL,
    `user_agent` varchar(255) NOT NULL DEFAULT '',
    log_when TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (log_id)

) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

