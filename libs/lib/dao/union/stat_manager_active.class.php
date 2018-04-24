<?php
namespace Dao\Union;
use \Dao;
class Stat_manager_active extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Stat_manager_active
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    public function sync( $ymd = 0 ) {

        $where_addition = '';
        $sql            = "
SELECT
	r.invite_uid AS uid,
    count(*) AS technician_num,
    r.invite_user_name AS user_name,
	r.invite_area_id AS area_id,
    u.phone,
	(SELECT COUNT( DISTINCT c.uid )  FROM log_credit c WHERE c.invite_uid = r.invite_uid ) AS technician_has_performance,
	(SELECT SUM(ip_count)             FROM log_credit c WHERE c.invite_uid = r.invite_uid ) AS install_num
FROM log_register r
LEFT JOIN channel_7654.user_marketer m ON r.invite_uid = m.userid
LEFT JOIN  `user` u ON r.invite_uid = u.id
WHERE r.invite_uid > 0 AND r.is_stat_manager > 0 {$where_addition}
GROUP BY r.invite_uid
       ";


    }


}
