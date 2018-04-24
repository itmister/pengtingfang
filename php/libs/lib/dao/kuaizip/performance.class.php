<?php
namespace Dao\Kuaizip;

class Performance extends Kuaizip {
    /**
     * @return \Dao\Kuaizip\Performance
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $sql = "
CREATE TABLE `performance` (
  `ymd` int(10) unsigned NOT NULL COMMENT '自增id',
  `technician_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发放人数',
  `ip_count` int(10) NOT NULL DEFAULT '0' COMMENT '发放总有效量',
  `dateline_confirm` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '确认时间戳',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态，1：未上传，2：待确认，3：已发放',
  PRIMARY KEY (`ymd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='业绩上传信息汇总表';
        ";
    }

    public function init_data() {
        $dateline_start = strtotime('20150611');
        $dateline_end = strtotime('20250911');
        $data_init = [];
        while ($dateline_start < $dateline_end ) {
            $ymd_now = date('Ymd', $dateline_start);
            $data_init[] = ['ymd' => $ymd_now];
            $dateline_start += 86400;
        }
        $this->add_all( $data_init );
    }

    /**
     * 取指定日期的业绩信息
     * @param $ymd
     */
    public function get_info($ymd) {
        $ymd = intval($ymd);
        $info = $this->get_row('ymd=' . $ymd);
        return $info;
    }


    /**
     * 取指定日期段业绩列表
     * @param $ymd_start
     * @param $ymd_end
     * @param null $status
     * @return mixed
     */
    public function get_list( $ymd_start, $ymd_end, $status = null ) {
        $where  = " WHERE ymd between {$ymd_start} and {$ymd_end} ";
        $status = intval( $status );
        if ( !empty($status) ) $where .= " AND `status`={$status}";

        $table = $this->_get_table_name();
        $sql = "SELECT * from {$table} {$where} ORDER by ymd DESC";
        return $this->query( $sql );
    }
}