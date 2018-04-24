<?php
/**
 * 统计-资料完整技术员
 */
namespace Dao\Stat\Base;
use \Dao\Stat\Stat;

class User_info_complete extends Stat {
    /**
     * @return User_info_complete
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table( $drop_if_exists = false ) {
        if ( $drop_if_exists ) $this->query("drop table if exists `base_user_info_complete`");
        $sql = "
            create table `base_user_info_complete` (
                `uid` int(11) not null DEFAULT 0 comment '用户uid',
                PRIMARY key (`uid`)
            ) engine=INNODB default charset utf8 comment '资料完整技术员uid列表';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_all() {
        $table_name = $this->_get_table_name();
        $this->delete_all($table_name);
        $sql = "
            replace into base_user_info_complete (`uid`)
            select
                id
            from
                `union`.`user`
            WHERE info_is_complete = 1;
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }
}