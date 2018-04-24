<?php
namespace Dao\Union;
use \Dao;
class Stat_manager_active_ym extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Stat_manager_active_ym
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    /**
     * 同步记录
     * @param int $ym 四位年月,如1504
     * @return bool
     */
    public function sync( $ym = 0 ) {
        $ym                             = intval( $ym );
        if (empty($ym)) return false;
        $table_stat_manager_active_ym   = $this->_get_table_name();
        $table_log_register             = $this->_get_table_name('log_register');
        $table_log_credit               = $this->_get_table_name('log_credit');

        $sql            = "
replace into {$table_stat_manager_active_ym} (ym, uid,technician_num,user_name,area_id, technician_has_performance, install_num, month_technician_num, month_technician_has_performance, month_install_num)
SELECT
  r.ym,
  r.invite_uid AS uid,
  count(*) as technician_num,
  r.invite_user_name AS user_name,
  r.invite_area_id as area_id,
 (SELECT count( DISTINCT c.uid ) FROM {$table_log_credit} c where c.invite_uid = r.invite_uid) as technician_has_performance,
 (SELECT sum(ip_count) FROM {$table_log_credit} c where c.invite_uid = r.invite_uid ) as install_num,
 (SELECT count(*) FROM {$table_log_credit} r2 WHERE r2.invite_uid = r.invite_uid and r.ym = r2.ym  ) as month_technician_num,
 (SELECT count( DISTINCT c.uid) FROM {$table_log_credit} c WHERE c.invite_uid = r.invite_uid and c.ym = r.ym ) as month_technician_has_performance,
 (SELECT sum(ip_count) FROM {$table_log_credit} c where c.invite_uid=r.invite_uid and c.ym=r.ym ) as month_install_num

FROM {$table_log_register} r
WHERE r.invite_uid > 0 AND r.is_stat_manager > 0 and r.ym={$ym}
GROUP BY r.invite_uid
       ";
//        $this->query( $sql );
        $this->db()->query( $sql );
    }


}
