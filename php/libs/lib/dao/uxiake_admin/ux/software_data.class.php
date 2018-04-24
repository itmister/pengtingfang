<?php
namespace Dao\Uxiake_admin\Ux;
use \Dao\Uxiake_admin\Uxiake_admin;
class Software_data extends Uxiake_admin{
    protected static $_instance = null;

    /**
     * @return Software_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function get_install_total_data($ymd){
        $sql = "
            SELECT
            	a.ymd,
            	a.uid,
            	a.soft_id,
            	a.qid,
            (SELECT SUM(`install`) FROM {$this->_get_table_name()} AS t WHERE t.ymd <= '{$ymd}' AND t.uid = a.uid AND t.soft_id = a.soft_id AND t.qid = a.qid) AS install_total
            FROM
            	{$this->_get_table_name()} AS a WHERE a.ymd = '{$ymd}'
            GROUP BY
            	a.uid,
            	a.soft_id,
            	a.qid
        ";
        return $this->query($sql);
    }
}
