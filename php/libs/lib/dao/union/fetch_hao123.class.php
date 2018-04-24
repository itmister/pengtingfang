<?php
/**
 * hao123业绩抓取记录
 */
namespace Dao\Union;
use \Dao;

class Fetch_hao123 extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Fetch_hao123
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 取指定日期所有tn业绩
     * @param $ymd_start
     * @param $ymd_end
     * @return [
     * data_list[
     * ymd :[
     *  tn : [
     *      ip : ip
     *      ip1000 : 12
     *      income : 22
     *  ]
     *  ...
     * ]
     *  ...
     * ]
     * tn_list [
     *  tn
     *  ...
     * ]
     * ]
     */
    public function get_data_by_ymd( $ymd_start, $ymd_end ) {

        $ymd_start        = intval($ymd_start);
        $ymd_end          = intval($ymd_end);
        $table_name = $this->_get_table_name();
        $sql        = "SELECT * from {$table_name} WHERE ymd BETWEEN {$ymd_start} AND {$ymd_end} ORDER BY ymd asc";
        $arr        = $this->query( $sql );
        $data_list  = [];
        $tn_list    = [];
        if ( !empty($arr) ) foreach( $arr as $item ){
            $data_list[$item['ymd']][$item['tn']] = [
                'ip'        => $item['ip'],
                'ip1000'   => $item['ip1000'],
                'income'    => $item['income']
            ];
            if ( !in_array($item['tn'], $tn_list) ) $tn_list[] = $item['tn'];
        }

        return [
            'data_list' => $data_list,
            'tn_list'   => $tn_list
        ];
    }
}
