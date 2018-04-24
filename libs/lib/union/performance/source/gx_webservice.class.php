<?php
namespace Union\Performance\Source;

/**
 * 第三方业绩基类
 * Class Base
 * @package Union\Performance\Third
 */

class Gx_Webservice {

    /**
     * 从高欣webservice取业绩
     * @param $software_name 软件标识名,如:pplive
     * @param $ymd_start 开始日期年月日,格式:2015-3-9
     * @param $ymd_end 结束日期年月日,格式:2015-3-9
     * @return array
     */
    public  function get($software_name, $ymd_start, $ymd_end) {
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