<?php

namespace Dao\Guangsuss_log;

use \Dao;

class Dosearch extends Guangsuss_log {

    /**
     * @return Dao\Guangsuss_log\Dosearch
     */
    public static function get_instance() {
        return parent::get_instance();
    }

    public function get_all_dosearch_count($ymd) {
        $sql = "SELECT count(DISTINCT UID) as num FROM `{$this->_realTableName}{$ymd}`";
        $data = $this->query($sql);
        return isset($data[0]['num']) && $data[0]['num'] > 0 ? $data[0]['num'] : 0;
    }

    public function get_all_dosearch($ymd, $limit = '') {
        $sql = "SELECT UID as uid,search_type,search_content,{$ymd} as ymd FROM `{$this->_realTableName}{$ymd}`";
        if ($limit) {
            $sql .= " limit {$limit}";
        }
        return $this->query($sql);
    }

}
