<?php
namespace Union\Performance\Third;

/**
 * PPTV业绩处理
 * Class Funshion
 * @package Union\Performance\Third
 */
class Funshion extends Base{

    /**
     * 默认执行间隔当前时间的天数,如果值为2的时候2015-03-19执行程序处理的是2015-03-17的业绩
     * @var int
     */
    protected $_default_day_diff = 7;

    /**
     * 取业绩
     * @param string $ymd 年月日,如:20150312
     * @return array
     */
    public function fetch( $ymd = '' ) {
        $timestamp = strtotime( $ymd );
        //风行数据更新延后1周,即风行显示3月7日的数据，实际上是3月1日的业绩,所以在此加上时间偏移
        $timestamp = $timestamp + 86400 * 6;
        $ymd = date('Y-m-d',  $timestamp );
        $result = array();
        $list  = $this->_gx_webservice('fengxing', $ymd, $ymd);
        foreach ( $list as $item ) {
            $uid = intval( $item['ChildTN'] );
            if ( !empty( $uid ) ) $result[] = array(
                'uid' => $uid,
                'num' => intval( $item['InstallNum'] )
            );
        }
        return $result;
    }

    /**
     * 读取本地校验好的业绩文件数据
     * @param integer $ymd 年月日 20150313
     * @return mixed
     */
    public function data_get( $ymd ) {
        $result = \Io\File::csv_to_array( $this->_path_file_verify . 'funshion/' . $ymd . '.csv', array('uid', 'num'));
        return !empty($result) ? $result : array();
    }


}