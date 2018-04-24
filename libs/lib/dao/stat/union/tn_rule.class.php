<?php
/**
 * 7654渠道分配规则
 */
namespace Dao\Stat\Union;
use \Dao\Stat\Stat;
use \Dao\Orm;

class Tn_rule extends Stat {
    use Orm;

    /**
     * @return Tn_rule
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create() {
        $sql = <<<eot
CREATE TABLE `union_tn_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` tinyint(4) DEFAULT NULL COMMENT '类型,1:固定对应,2:连续范围,3:指定范围',
  `software` varchar(40) DEFAULT NULL COMMENT '软件标识',
  `union_platform_id` int(11) DEFAULT NULL COMMENT '平台id,见union_platform',
  `data` text COMMENT '规则数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='7654渠道－平台分配规则';
eot;
        return $this->exec( $sql );
    }

    /**
     * @param $software
     * @return array
     */
    public function rule( $software ) {
        switch ( $software ) {
            //百度系列
            case 'bdbrowserv2':
            case 'bdpy':
            case 'bdsdv2':
            case 'bdwsv2':
            case 'bdpyv2':
            case 'bdbrowserv3':
            case 'bdws':
            case 'bdsd':
            case 'bdbrowser':
            case 'bdzm':
            case 'bdzmv3':
                $software = 'bd';
                break;
            default:
                $software = $software;
                break;
        }
        $result = [];
        foreach ( $this->fields('union_platform_id, `type`,`data`')->where( ['software' => $software] )->find() as $row ) {
            $row['type'] = intval( $row['type'] );
            switch ( $row['type'] ) {
                case 1 ://
                    $result[] = $row;
                case 2 ://连续范围
                    $arr = explode(',', $row['data']);
                    $row['data'] = ['start' => intval($arr[0]), 'end' => intval($arr[1])];
                    $result[] = $row;
                    break;
                case 3 ://指定范围
                    $arr = explode(',', $row['data']);
                    $row['data'] = array_combine($arr, array_fill(0, count($arr), 1));
                    $result[] = $row;
                    break;
            }
        }
        return $result;
    }
}