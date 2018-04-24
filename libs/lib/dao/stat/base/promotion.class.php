<?php
/**
 * 统计-市场经理-推广信息
 */
namespace Dao\Stat\Base;
use \Dao\Stat\Stat;

class Promotion extends Stat {
    //状态，上架
    const status_online = 1 ;

    //状态，下架
    const status_offline = 0;

    /**
     * @return Promotion
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `base_promotion`;");
        $sql = "
            CREATE TABLE `base_promotion` (
              `id` int(11) NOT NULL DEFAULT 0,
              `short_name` varchar(40) not null DEFAULT '',
                `name` VARCHAR(40) not null default '',
                `status` int(11) not null DEFAULT 0,
              PRIMARY KEY (`id`),
              UNIQUE KEY `short_name` (`short_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '推广软件列表';
        ";
        $this->query( $sql );
        return $this->affected_rows();
//        return $this->affected_rows();
    }

    public function sync_ymd($ymd_start, $ymd_end) {
        return $this->sync_all();
    }

    public function sync_all() {
        $this->delete_all();
        $sql = "
            insert into `base_promotion` (id,short_name, `name`, `status`)
            select
              id,
              short_name,
              `name`,
              `state` as `status`
            from
              `union`.promotion
            GROUP BY
              short_name
        ";
        return $this->exec( $sql );
    }

    public function get_list( $status = null ) {

        $table_name = $this->_get_table_name();
        $result = [];
        $where = '';
        if (isset($status)) {
            $status = intval( $status );
            $where = " WHERE `status`={$status}";
        }
        foreach ( $this->query("select id, `short_name`, `name` from {$table_name} {$where}") as $row ){
            $result[$row['short_name']] = $row;
        }
        return $result;
    }
}