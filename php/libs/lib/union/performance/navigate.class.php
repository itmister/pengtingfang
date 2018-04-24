<?php
namespace Union\Performance;

/**
 * 业绩-导航类
 * Class package
 * @package Union\Performance
 */

class Navigate {
    protected static $_instance = null;

    /**
     * @return Navigate
     */
    public static function i(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * 取限定发放日期推广总量列表 $promotion_short_name 空则按 $promotion_short_name 分组 uid 空则按uid 分组
     * @param string $promotion_short_name 推广软件short_name,如dgdh
     * @param integer $uid 用户uid
     * @param $dateline_start 发放开始时间戳
     * @param $dateline_end 发放结束时间戳
     * @param string $order 排序 desc:降序 asc:升序
     * @param integer $min 最小安装量
     * @param string $limit 数量限制
     * @return array
     */
    public function sum_install_list( $promotion_short_name = '', $uid = null, $dateline_start, $dateline_end, $order = 'desc', $min = 0, $limit ) {
        $result = \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->sum_install_list( $promotion_short_name, $uid, $dateline_start, $dateline_end, $order, $min, $limit);
        return $result;
    }

    /**
     * 取指定日期范围推广量
     * @param string $promotion_short_name
     * @param null $uid
     * @param $dateline_start
     * @param $dateline_end
     */
    public function sum_install( $promotion_short_name = '', $uid = null, $dateline_start, $dateline_end ) {
        $result = \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->sum_install($promotion_short_name, $uid, $dateline_start, $dateline_end );
//        die(\Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->get_error());
        return $result;
    }

}