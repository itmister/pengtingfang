<?php
/**
 * 统计-业绩基础
 */
namespace Dao\Stat\Base;
use \Dao\Stat\Stat;

class Performance extends Stat {

    /**
     * @return Performance
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query("drop table if exists `base_performance`");
        $sql = "
CREATE TABLE `base_performance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `c_id` int(11) DEFAULT NULL COMMENT 'credit_wait_confirm的id',
  `uid` int(11) DEFAULT NULL COMMENT '装机员uid',
  `user_name` varchar(40) DEFAULT NULL COMMENT '装机员名称',
  `credit` int(11) DEFAULT NULL COMMENT '积分',
  `type` tinyint(4) DEFAULT NULL COMMENT '类型,1:签到，2：推广，10：活动',
  `sub_type` tinyint(4) DEFAULT NULL COMMENT '子类型,当type=2,sub_type，2:软件，3:导航',
  `dateline` int(11) DEFAULT NULL COMMENT '发放时间戳',
  `ym` int(11) DEFAULT NULL COMMENT '业绩产生时间，6位年月,如201507',
  `ymd` int(11) DEFAULT NULL COMMENT '业绩产生时间，八位年月日,如20150714',
  `ip_count_org` int(11) DEFAULT NULL COMMENT '厂商返回量',
  `ip_count` int(11) DEFAULT NULL COMMENT '有效量',
  `soft_id` varchar(40) DEFAULT NULL COMMENT '推广的软件名称或活动代号',
  `promotion_id` int(11) DEFAULT NULL COMMENT '推广软件的id',
  `puid` int(11) DEFAULT NULL COMMENT '上级uid;F',
  `pname` varchar(40) DEFAULT NULL COMMENT '上级名称;F',
  `ppuid` int(11) DEFAULT NULL COMMENT '上上级uid;F',
  `ppname` varchar(40) DEFAULT NULL COMMENT '上上级用户名;F',
  PRIMARY KEY (`id`),
  KEY `c_id` (`c_id`),
  KEY `uid` (`uid`),
  KEY `dateline` (`dateline`),
  KEY `puid` (`puid`),
  KEY `ymd` (`ymd`),
  KEY `ppuid` (`ppuid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='业绩基础表 200w';
        ";
        return $this->exec( $sql );
    }

    /**
     * 同步
     */
    public function sync_all() {

        $this->delete_all();
        $this->_sync_credit_wait_confirm();
        $this->_sync_activity_hao123_vip_num();
        $this->_sync_activity_hao123_vip_num_new();

    }

    public function sync_ymd( $ymd_start , $ymd_end  ) {

        $ymd_start = intval( $ymd_start );
        $ymd_end = intval( $ymd_end );
        $this->delete("ymd between {$ymd_start} and {$ymd_end}");
        $this->_sync_credit_wait_confirm( $ymd_start, $ymd_end );
        $this->_sync_activity_hao123_vip_num( $ymd_start, $ymd_end );
        $this->_sync_activity_hao123_vip_num_new( $ymd_start, $ymd_end );

    }

    /**
     *
     * @param null $ymd_start
     * @param null $ymd_end
     * @return bool|int|string
     */
    protected function _sync_credit_wait_confirm( $ymd_start = null , $ymd_end = null ) {
        $where = '';
        if (isset($ymd_start) || isset($ymd_end)) {
            $ymd_start = intval( $ymd_start );
            $ymd_end = intval( $ymd_end );
            $ymd_end = empty($ymd_end) ? date('Ymd') : $ymd_end;
            $where = " WHERE c.ymd between {$ymd_start} and {$ymd_end} ";
        }
        $sql = "
          insert into `base_performance`(c_id, uid,user_name,credit,`type`,sub_type, dateline,ym,ymd,ip_count_org,ip_count,soft_id,promotion_id,puid,pname,ppuid,ppname)
          select
                c.id as c_id ,#credit_wait_confirm的id
                c.uid as uid,#用户id
                ub.user_name as user_name, #用户名
                c.credit as credit, #积分
                c.type as type,#类型,1:签到，2：推广，10：活动',
                (case when p.id = 6 OR p.id = 32 then 3 else 2 end) as sub_type,#子类型,当type=2,sub_type，2:软件，3:导航',#hao123网址导航,360网址导航sub_type归类到3
                c.dateline,#记录产生的时间戳
                c.ym + 200000 as ym,#6位年月年月
                c.ymd,#8位年月日
                f.f_num_original as org_ip_count,#产商返回量
                c.ip_count as ip_count,#有效量
                c.`name` as `soft_id`,#推广的软件名称或活动代号
                p.id as promotion_id,#推广软件的id
                ub.puid as puid,#上级uid;F
                ub.pname as pname,#上级名称;F
                ub.ppuid as ppuid,#上上级uid;F
                ub.ppname as ppname#上上级用户名;F
            FROM
                `union`.credit_wait_confirm c
                 LEFT JOIN `stat`.`base_user` ub on c.uid= ub.uid
                 LEFT JOIN `promotion` p on p.short_name = c.`name`
                 LEFT JOIN `union`.ad_product_record_fafang_log f on c.uid = f.f_uid and c.ymd=f.f_ymd and p.id=f.f_promotion_id#取产商反回量
            {$where}
        ";
        return $this->exec( $sql );
    }

    /**
     * hao123活动
     * @param $ymd_start
     * @param $ymd_end
     */
    protected function _sync_activity_hao123_vip_num( $ymd_start = null , $ymd_end = null ) {

        $where = '';
        if (isset($ymd_start) || isset($ymd_end)) {
            $ymd_start = intval( $ymd_start );
            $ymd_end = intval( $ymd_end );
            $ymd_end = empty($ymd_end) ? date('Ymd') : $ymd_end;
            $where = " WHERE h.ymd between {$ymd_start} and {$ymd_end} ";
        }
        $sql = "
        insert into `base_performance`(
				c_id, uid,user_name,`type`,sub_type,
				dateline,ym,ymd,credit,ip_count_org,ip_count,soft_id,promotion_id,puid,pname,ppuid,ppname)
        SELECT
						(h.id + 100000000) as c_id,#id基数1亿
             h.uid as uid,#uid
						ub.`user_name` as user_name,#用户名
             2 as `type`,#2:主类推广
             3 as sub_type,#3:子类导航

             h.dateline as dateline, #记录产生的时间戳
             FROM_UNIXTIME( UNIX_TIMESTAMP(h.ymd), '%Y%m' ) as ym,#6位年月
             h.ymd as ymd,#年月日
             0 as credit,#导航不发积分，只发有效量
             h.ip_count as ip_count,#有导航有效量
             h.ip_count as org_ip_count,
             'hao123' as `soft_id`,#推广的标识名
             6 as promotion_id,#推广的资源id

            ub.puid as puid,#上级uid;F
            ub.pname as pname,#上级名称;F
            ub.ppuid as ppuid,#上上级uid;F
            ub.ppname as ppname#上上级用户名;F
            FROM `union`.activity_hao123_vip_num h
              LEFT JOIN `stat`.`base_user` ub on h.uid= ub.uid
          {$where}
        ";
        $this->exec( $sql );
    }

    /**
     * 新活动表
     * @param $ymd_start
     * @param $ymd_end
     * @return bool|int|string
     */
    public function _sync_activity_hao123_vip_num_new( $ymd_start = null, $ymd_end = null ) {
        $where = '';
        if (isset($ymd_start) || isset($ymd_end)) {
            $ymd_start = intval( $ymd_start );
            $ymd_end = intval( $ymd_end );
            $ymd_end = empty($ymd_end) ? date('Ymd') : $ymd_end;
            $where = " WHERE h.ymd between {$ymd_start} and {$ymd_end} ";
        }
        $sql = "
insert into `base_performance`(
c_id, uid,user_name,credit,`type`,sub_type, dateline,ym,ymd,
ip_count_org,ip_count,soft_id,promotion_id,
puid,pname,ppuid,ppname)
              SELECT
                (h.id + 200000000) as c_id,#id基数2亿
                 h.uid as uid,#uid
								 ub.user_name,
								 0 as credit,#导航不发积分，只发有效量
                 2 as `type`,#2:主类推广
                 3 as sub_type,#3:子类导航
                 h.dateline as dateline, #记录产生的时间戳
                 FROM_UNIXTIME( UNIX_TIMESTAMP(h.ymd), '%Y%m' ) as ym,#六位年月
                 h.ymd as ymd,#年月日

                 h.ip_count as ip_count,#有导航有效量
                 h.ip_count as org_ip_count,
                 h.`name` as `soft_id`,#推广的标识名
                 p.id as promotion_id,#推广的资源id
                ub.puid as puid,#上级uid;F
                ub.pname as pname,#上级名称;F
                ub.ppuid as ppuid,#上上级uid;F
                ub.ppname as ppname#上上级用户名;F`
            FROM `union`.activity_hao123_vip_num_new h
              LEFT JOIN `stat`.`base_user` ub on h.uid= ub.uid
              LEFT JOIN `stat`.promotion p on h.`name` = p.short_name #取推广的资源名称
          {$where}
        ";
        return $this->exec( $sql );
    }


    /**
     * @param int $id_begin 开始id
     * @param int $num 一次取记录数量
     */
   public function yield_join_data( $id_begin = 0, $num = 1000 ) {

       $id_begin = intval($id_begin);
       $where = "WHERE c.id > {$id_begin}";
       $sql = "
            select
                c.id as c_id ,#credit_wait_confirm的id
                c.uid as uid,#用户id
                ub.user_name as user_name, #用户名
                c.credit as credit, #积分
                c.type as type,#类型,1:签到，2：推广，10：活动',
                (case when p.id = 6 OR p.id = 32 then 3 else 2 end) as sub_type,#子类型,当type=2,sub_type，2:软件，3:导航',#hao123网址导航,360网址导航sub_type归类到3
                c.dateline,#记录产生的时间戳
                c.ym + 200000 as ym,#6位年月年月
                c.ymd,#8位年月日
                f.f_num_original as org_ip_count,#产商返回量
                c.ip_count as ip_count,#有效量
                c.`name` as `soft_id`,#推广的软件名称或活动代号
                p.id as promotion_id,#推广软件的id
                ub.puid as puid,#上级uid;F
                ub.pname as pname,#上级名称;F
                ub.ppuid as ppuid,#上上级uid;F
                ub.ppname as ppname#上上级用户名;F
            FROM
                `union`.credit_wait_confirm c
                 LEFT JOIN `stat`.`user_base` ub on c.uid= ub.uid
                 LEFT JOIN `promotion` p on p.short_name = c.`name`
                 LEFT JOIN `union`.ad_product_record_fafang_log f on c.uid = f.f_uid and c.ymd=f.f_ymd and p.id=f.f_promotion_id#取产商反回量
            $where
            ORDER BY c.id
            LIMIT {$num}
";
       $int_field   = ['c_id', 'uid', 'credit', 'type', 'sub_type', 'dateline', 'ym', 'ymd', 'org_ip_count', 'ip_count', 'promotion_id',
           'puid', 'ppuid'];
       foreach ( $this->yield_result( $sql ) as $item ) {
           foreach ( $int_field as $field ) $item[$field] = intval($item[$field]);
           yield $item;
       }
   }

    public function yield_activity_hao123_vip_num( $id_begin = 0, $num = 1000) {
        $id_begin = intval($id_begin);
        $id_base = 100000000;
        if ( $id_begin > 0 ) $id_begin -= $id_base;
        $where = "WHERE h.id > {$id_begin}";
        $sql = "
        SELECT
            (h.id + 100000000) as c_id,#id基数1亿
             h.uid as uid,#uid
             2 as `type`,#2:主类推广
             3 as sub_type,#3:子类导航
             h.dateline as dateline, #记录产生的时间戳
             FROM_UNIXTIME( UNIX_TIMESTAMP(h.ymd), '%Y%m' ) as ym,#6位年月
             h.ymd as ymd,#年月日
             0 as credit,#导航不发积分，只发有效量
             h.ip_count as ip_count,#有导航有效量
             h.ip_count as org_ip_count,
             'hao123' as `soft_id`,#推广的标识名
             6 as promotion_id,#推广的资源id
             ub.`user_name` as user_name,#用户名
            ub.puid as puid,#上级uid;F
            ub.pname as pname,#上级名称;F
            ub.ppuid as ppuid,#上上级uid;F
            ub.ppname as ppname#上上级用户名;F
            FROM `union`.activity_hao123_vip_num h
              LEFT JOIN `stat`.`user_base` ub on h.uid= ub.uid
          $where
          limit {$num}
             ";
            $int_field   = ['c_id', 'uid', 'credit', 'type', 'sub_type', 'dateline', 'ym', 'ymd', 'org_ip_count', 'ip_count', 'promotion_id',
                'puid', 'ppuid'];
            foreach ( $this->yield_result( $sql ) as $item ) {
                foreach ( $int_field as $field ) $item[$field] = intval($item[$field]);
                yield $item;
            }
    }

    public function yield_activity_hao123_vip_num_new( $id_begin = 0, $num = 1000 ) {
        $id_begin = intval($id_begin);
        $id_base = 200000000;
        if ( $id_begin > 0 ) $id_begin -= $id_base;
        $where = "WHERE h.id > {$id_begin}";
        $sql = "
            SELECT
                (h.id + 200000000) as c_id,#id基数2亿
                 h.uid as uid,#uid
                 2 as `type`,#2:主类推广
                 3 as sub_type,#3:子类导航
                 h.dateline as dateline, #记录产生的时间戳
                 FROM_UNIXTIME( UNIX_TIMESTAMP(h.ymd), '%Y%m' ) as ym,#六位年月
                 h.ymd as ymd,#年月日
                 0 as credit,#导航不发积分，只发有效量
                 h.ip_count as ip_count,#有导航有效量
                 h.ip_count as org_ip_count,
                 h.`name` as `soft_id`,#推广的标识名
                 p.id as promotion_id,#推广的资源id
                 ub.`user_name` as user_name,#用户名
                ub.puid as puid,#上级uid;F
                ub.pname as pname,#上级名称;F
                ub.ppuid as ppuid,#上上级uid;F
                ub.ppname as ppname#上上级用户名;F
            FROM `union`.activity_hao123_vip_num_new h
              LEFT JOIN `stat`.`user_base` ub on h.uid= ub.uid
              LEFT JOIN `stat`.promotion p on h.`name` = p.short_name #取推广的资源名称
          $where
          limit {$num}
        ";
        $int_field   = ['c_id', 'uid', 'credit', 'type', 'sub_type', 'dateline', 'ym', 'ymd', 'org_ip_count', 'ip_count', 'promotion_id',
            'puid', 'ppuid'];
        foreach ( $this->yield_result( $sql ) as $item ) {
            foreach ( $int_field as $field ) $item[$field] = intval($item[$field]);
            yield $item;
        }
    }
}
