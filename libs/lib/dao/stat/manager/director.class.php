<?php
/**
 * 统计-市场经理-渠道主管
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Director extends Stat {
    /**
     * @return Director
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->drop();
        $sql = "
           create table `manager_director` (
                `director_uid` int(11) not null default '0' comment '渠道主管uid',
                `director_user_name` varchar(255) not null DEFAULT '' comment '渠道主管姓名',
                primary key(`director_uid`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '统计-市场经理-渠道主管'
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_ymd($ymd_start, $ymd_end) {
        return $this->sync_all();
    }


    public function sync_all() {
        $this->delete_all();
        $this->add(['director_uid' => 1, 'director_user_name' => 'admin']);//管理员
        $sql = "
            insert into `manager_director` (director_uid, director_user_name)
            select
                    userid as director_uid,
                    realname as director_user_name
            from
                `channel_7654`.`admin`
            WHERE
                roleid = 7
        ";
        return $this->exec( $sql );
    }

    /**
     *
     */
    public function get_list() {
        $table_name = $this->_get_table_name();
        $result = [];
        foreach ( $this->query("select * from {$table_name}") as $row )
            $result[] = ['director_uid' => intval( $row['director_uid']), 'director_user_name' => $row['director_user_name']];
        return $result;
    }
}