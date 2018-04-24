<?php
namespace Dao\Kuaizip;

class Phone_verify_code extends Kuaizip {

    public function create_table() {
        $sql = "
        CREATE TABLE `phone_verify_code` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `phone` varchar(11) DEFAULT NULL,
          `code` mediumint(9) DEFAULT NULL COMMENT '验证码',
          `dateline_create` int(10) unsigned DEFAULT NULL COMMENT '验证码生成时间戳',
          `is_used` tinyint(4) DEFAULT '0' COMMENT '是否已经使用，0:未使用,1:已使用',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='验证码表';
        ";
    }

    /**
     * @return \Dao\Kuaizip\Phone_verify_code
     */
    public static function get_instance(){ return parent::get_instance(); }


    /**
     * 检验证码
     * @param $phone
     * @param $code
     * @return boolean
     */
    public function check( $phone, $code ) {
        $arr_code_info         = $this->get_last( $phone );
        if ( empty($arr_code_info)
            || $code != $arr_code_info['code']
            || !empty($arr_code_info['is_used'])
            || time() - $arr_code_info['dateline_create'] > 3600
        ) return false;

        return true;
    }

    /**
     * 取最后一条验证码
     * @param $phone
     * @return array
     */
    public function get_last($phone) {
        $table_phone_verify_code = $this->_get_table_name();
        $sql = "
            SELECT
                *
            FROM
               {$table_phone_verify_code}
            WHERE
               phone='{$phone}'
            ORDER BY id DESC
            LIMIT 1
        ";
       $arr_data = $this->query( $sql );
       return !empty($arr_data) ? current( $arr_data) : array();
    }

    /**
     * 将验证码标记为已经使用
     * @param $id
     * @return integer
     */
    public function set_use( $id ) {
        $id = intval($id);
        return $this->update("id={$id}", array('is_used' => 1) );
    }
}