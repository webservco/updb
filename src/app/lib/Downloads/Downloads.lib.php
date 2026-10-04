<?php
if (!defined('_WEB')) {
    exit();
}

class Downloads extends Library {
    /**
     * App prefix.
     *
     * @var string
     */
    protected $_mAppPrefix = NULL;

    public function Init($appPrefix)
    {
        $this->_mAppPrefix = $appPrefix;
    }

    /**
     * Implement GetInstance() method.
     * This is required for PHP < 5.3 compatibility (no late static binding)
     *
     * @param string $className
     *        	class name to instantiate
     * @return object
     */
    public static function GetInstance($className = __CLASS__) {

        /**
         * Call parent method with current class name as parameter.
         */
        return parent::_GetInstance($className);
    }

    public function GetList(int $pubIssueId) {
        $data = array();

        $q = sprintf(
            "SELECT download_id, pub_issue_id, file_name 
            FROM {$this->_mAppPrefix}pub_download 
            WHERE pub_issue_id = %d ORDER BY file_name",
            $pubIssueId
        );

        $rs = Core::Database()->Query($q);

        $t = $rs->num_rows;

        if ($t > 0) {
            while ($r = $rs->fetch_assoc()) {
                $data[$r['download_id']] = $r;
            }
        }

        return $data;
    }

    public function GetItem(int $downloadId, int $pubIssueId) {
        $q = sprintf(
            "SELECT file_name, content_type 
            FROM {$this->_mAppPrefix}pub_download WHERE download_id = %d AND pub_issue_id = %d LIMIT 1",
            $downloadId,
            $pubIssueId,
        );

        $rs = Core::Database()->Query($q);

        $t = $rs->num_rows;

        return $t === 1 ? $rs->fetch_assoc(): FALSE;
    }

    public function StoreDownloadLog(int $downloadId, int $userId, string $userIp, string $userAgent) {
        $q = sprintf(
            "INSERT INTO {$this->_mAppPrefix}pub_download_log 
            (download_id, user_id, user_ip, user_agent) VALUES (%s, %s, '%s', '%s')",
            $downloadId,
            $userId,
            sprintf("%u", ip2long($userIp)),
            Core::Database()->Escape($userAgent),
        );

        Core::Database()->Query($q);

        return Core::Database()->GetInsertId();
    }
}