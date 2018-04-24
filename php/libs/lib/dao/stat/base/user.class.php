<?php
/**
 * 统计-基础用户表
 */
namespace Dao\Stat\Base;
use \Dao\Stat\Stat;

class User extends Stat {

    /**
     * @return User
     */
    public static function get_instance() { return parent::get_instance(); }

    public function create_table() {
        $this->drop();
        $sql = "
            CREATE TABLE `base_user` (
              `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
              `type` tinyint(4) DEFAULT NULL COMMENT '用户类型,1:普通用户，2：见习市场经，3：见习作业中,4:正式'', F',
              `user_name` varchar(40) DEFAULT NULL COMMENT '用户名',
              `from_type` tinyint(4) DEFAULT NULL COMMENT '注册平台类型0:主站,1:手机,2:市场经理',
              `puid` int(11) DEFAULT NULL COMMENT '上级用户id,f',
              `pname` varchar(40) DEFAULT NULL COMMENT '上级用户名,f',
              `ppuid` int(11) DEFAULT NULL COMMENT '上上级用户id,f',
              `ppname` varchar(40) DEFAULT NULL COMMENT '上上级用户名,f',
              `info_is_complete` tinyint(4) DEFAULT NULL COMMENT '资料是否完整,f',
              `phone` varchar(15) DEFAULT NULL COMMENT '电话号码,f',
              `reg_dateline` int(11) DEFAULT NULL COMMENT '注册时间戳',
              `reg_ymd` int(11) DEFAULT NULL COMMENT '注册年月日20150701',
              `reg_ym` int(11) DEFAULT NULL COMMENT '注册年月201507',
              `bind_ymd` int(11) DEFAULT NULL COMMENT '用户绑定上级用户年月日',
              `invite_type` int(11) DEFAULT '0' COMMENT '邀请类型,0:默认,1:市场经理',
              `reg_ip` varchar(15) DEFAULT NULL,
              `qq` varchar(15) DEFAULT NULL COMMENT 'qq号,f',
              `real_name` varchar(40) DEFAULT NULL COMMENT '真实姓名,f',
              `sex` tinyint(4) DEFAULT NULL COMMENT '性别,0男 1女,f',
              `remark` varchar(40) DEFAULT NULL,
              `address` varchar(80) DEFAULT NULL COMMENT '地址,f',
              PRIMARY KEY (`uid`),
              KEY `puid` (`puid`),
              KEY `reg_dateline` (`reg_dateline`),
              KEY `phone` (`phone`),
              KEY `bind_ymd` (`bind_ymd`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户基础信息表';
        ";
        $this->query( $sql );
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        return $this->sync_all();//@todo 按日期同步待完善
    }


    /**
     * @return int
     */
   public function sync_all() {
        $this->delete_all();
       $sql = "
insert into base_user (`uid`,`type`,`user_name`,`from_type`,`info_is_complete`,`phone`,`reg_dateline`,
`reg_ym`,`reg_ymd`, `bind_ymd`,`reg_ip`,`invite_type`,`puid`,`pname`,`ppuid`,`ppname`,`qq`,`real_name`,`sex`,`remark`,`address`)
select
	u.id as uid,#用户id
	case
	  when m.`status` is null or m.`areaid` = 0 then 1
	  when m.`status` = 0 and mw.ymd_start is null then 2
	  when m.`status` = 0 and mw.ymd_end is not null then 3
	  when 1 then 4
	  end as type, #用户类型,1:普通用户，2：见习市场经，3：见习作业中,4:正式', F
	u.`name` as user_name,#用户名
	u.refer_type as from_type, #注册平台类型0:主站,1:手机,2:市场经理
	u.info_is_complete,#资料是否完整,F
	u.phone as phone,#电话号码,F
	u.reg_dateline, #注册时间戳
	FROM_UNIXTIME(u.`reg_dateline`,'%Y%m') as reg_ym,
	FROM_UNIXTIME(u.`reg_dateline`,'%Y%m%d') as reg_ymd,
	(case
		when u.bind_dateline <= 0 then FROM_UNIXTIME(u.`reg_dateline`,'%Y%m%d')
	  else  FROM_UNIXTIME(u.`bind_dateline`,'%Y%m%d')
	  end
	  ) as bind_ymd ,#绑定上级年月日
	u.reg_ip as reg_ip, #注册 ip
	u.invitetype as invite_type, #邀请类型,0:默认,1:市场经理
	u2.id as puid, #上级用户id,F
	u2.`name` as pname,#上级用户名,F
	u3.id as ppuid, #上上级用户id,F,
	u3.`name` as ppname, #上上级用户名,F
	ue.qq,#qq,F
	(case when mc.realname is null then ue.`name` else mc.realname end) as real_name,#真实姓名,F
	ue.sex,#性别,0男  1女,F
	ue.remark,#渠道主管对市场经理的备注,F
	ue.addr as address#地址,F
from `union`.`user` u
	left JOIN `union`.`user_info_ext` ue on u.id=ue.uid
	LEFT JOIN `channel_7654`.user_marketer m on u.id=m.userid
	LEFT JOIN `channel_7654`.user_marketer_children mc on u.id=mc.userid
	LEFT JOIN  `stat`.`manager_working` mw on u.id=mw.manager_uid
	left join `union`.`user` u2 on u.invitecode = u2.idcode
	LEFT JOIN `union`.`user` u3 on u2.invitecode = u3.idcode
";
       $this->query( $sql );
       return $this->affected_rows();
   }
}
