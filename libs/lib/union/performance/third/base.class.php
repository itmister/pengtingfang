<?php
namespace Union\Performance\Third;

/**
 * 第三方业绩基类
 * Class Base
 * @package Union\Performance\Third
 */

abstract class Base {

    /**
     * 业绩文件存放路径
     * @var string
     */
    protected $_path_file = './Public/upload/auto/';

    /**
     * 校验好后的文件存放路径
     * @var string
     */
    protected $_path_file_verify = './Public/upload/auto/';

    /**
     * 默认执行间隔当前时间的天数,如2015-03-19执行脚本，如果值为2的时候执行程序处理的是2015-03-17的业绩
     * @var int
     */
    protected $_default_day_diff = 1;

    protected $_config = [
        'path_email_attach' => './Public/upload/'   //邮件路径
    ];

    /**
     * 取业绩数据
     * @param string $ymd 年月日,如:20150312
     * @return array
        array(
            uid:渠道id
            num:装机量
        )
        ...
     */
    public  abstract  function fetch( $ymd = '' );


    /**
     * 读取本地校验好的业绩文件数据
     * @param $ymd
     * @return mixed
     */
    public abstract function data_get( $ymd );

    public function __construct( $config = [] ) {
        $this->_path_file = !empty($config['path_file']) ? $config['path_file'] : ( SITE_DIR . '/Public/upload/auto/' );
        $this->_path_file_verify = !empty( $config['path_file_verify']) ? $config['path_file_verify'] : ( SITE_DIR . '/Public/upload/auto/' );
        if ( !empty($config) && is_array( $config ) ) $this->_config = array_merge( $this->_config, $config );
    }


    /**
     * 返回默认处理的年月日
     * @param string $ymd 年月日 默认值，如设置则直接返回此值
     * @return string
     */
    public function get_default_handle_ymd( $ymd = '' ) {
        $timestamp = strtotime( $ymd );
        return date( 'Ymd', !empty($timestamp) ? $timestamp :  time() - 86400 * intval($this->_default_day_diff ) );
    }

    /**
     * 从高欣webservice取业绩
     * @param $software_name 软件标识名,如:pplive
     * @param $ymd_start 开始日期年月日,格式:2015-3-9
     * @param $ymd_end 结束日期年月日,格式:2015-3-9
     * @return array
     */
    protected function _gx_webservice($software_name, $ymd_start, $ymd_end) {
        $result = array();
        if ( empty($software) ) $result;
        if ( empty($ymd_start) ) $ymd_start = date('Y-m-d', time() - 86400 );
        if ( empty($ymd_end) ) $ymd_end = date('Y-m-d', time() - 86400 );

        $objSoapClient = new \SoapClient("http://www.shgaoxin.net/ChannelData/WebApi/Common/GX_PromotionChildChannelEffect.asmx?wsdl");
        $param = array(
            "channelName"   => $software_name,
            'startDate'     => $ymd_start,
            'endDate'       => $ymd_end,
        );

        $obj = $objSoapClient->GetChildChannelEffectPageCount( $param );
        $pageCount = intval( $obj->GetChildChannelEffectPageCountResult );

        //页码从0开始
        for ( $i=0; $i < $pageCount; $i++ ) {
            $param['PageIndex'] = $i;
            $res_list = $objSoapClient->GetChildChannelEffectForPageIndex( $param );
            $list = json_decode( $res_list->GetChildChannelEffectForPageIndexResult, true );
            if ( !empty( $list ) ) $result = array_merge($result, $list );
        }
        return $result;
    }
}