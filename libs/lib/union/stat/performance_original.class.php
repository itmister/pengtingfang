<?php
namespace Union\Stat;

/**
 * 统计-日志,记录一些中间数据便于统计分析
 * Class Performance_original
 * @package Union\Stat
 */
use Core\Single;
use \Dao\Stat\Union\Performance_original as Dao_performance_original;
use Union\Promotion;

class Performance_original extends Single {

    /**
     * @return Performance_original
     */
    public static function get_instance() { return parent::get_instance(); }

    /**
     * 批量导入
     * @param array $data_list 发放量数组，默认key:tn,value:ip_count,也可通过$field_map 参数来控制
        [
            tn : ip_count
        ]
     * @param $software 软件英文标识
     * @param $ymd 业绩年月日
     * @param array $field_map tn与ip_count字段映射，如果值为null则$data_list $key为tn, $value为ip_count
     * @param string $explode 对$data_list每一行进行explode分隔符
     * @return boolean
     */
    public function batch_add( $data_list, $software, $ymd, $field_map = [ 'tn' => 0, 'ip_count' => 1 ] , $explode = null ) {
        if ( empty($data_list) || empty($software) || empty($ymd) ) return false;
        if (is_numeric($software)) $software = Promotion::get_instance()->get_software_by_id($software);
        if (empty($software)) return false;

        $dateline       = time();
        $data_insert    = [];
        if ( null == $field_map ) foreach ($data_list as $key => $row ){
            if (!empty($explode)) $row = explode( $explode, trim($row) );
            $data_insert[] = [
                'tn'        => $key,
                'ip_count' => intval( $row ),
                'software' => $software,
                'ymd'       => $ymd,
                'dateline' => $dateline,
            ];
        }
        else foreach ($data_list as $key => $row ) {
            if (!empty($explode)) $row = explode( $explode, trim($row) );
            $data_insert[] =[
                'tn'        => $row[$field_map['tn']],
                'ip_count' => intval( $row[$field_map['ip_count']] ),
                'software' => $software,
                'ymd'       => $ymd,
                'dateline' => $dateline,
            ];
        }

        $dao_performance_original = Dao_performance_original::get_instance();
        $dao_performance_original->delete( ['software' => $software, 'ymd' => $ymd] );
        $dao_performance_original->add_all( $data_insert );
        return true;
    }

    /**
     * 取指定软件时间范围内原始发放量
     * @param $software
     * @param $ymd_start
     * @param $ymd_end
     * @return array
     * [
     *      ymd : ip_count
     * ]
     */
    public function get_performance_original( $software, $ymd_start, $ymd_end ) {
        $dao_performance_original = Dao_performance_original::get_instance();
        return $dao_performance_original->get_performance_original( $software, $ymd_start, $ymd_end );
    }

}