<?php
namespace Dao\Wplayer_log;
class Tpop extends Wplayer_log {
    
    protected $tpop = ['tpop3','tpop4'];
    protected $event_name = [
        'tpop3-1','tpop3-2','tpop3-3',
        'tpop3-1-sign','tpop3-2-sign','tpop3-3-sign',
        'tpop4-1','tpop4-2','tpop4-3',
    ];
    public static $type;   //tpop 类型
    /**
     * @return Tpop
     */
    public static function get_instance(){
        return parent::get_instance();
    }
    
    public function create_index($ymd){
        $sql = "show keys from `tpop3{$ymd}` WHERE key_name = 'event_name'";
        $index = $this->query($sql);
        if(!$index){
            $sql = "
                ALTER TABLE `tpop3{$ymd}`
                ADD INDEX `event_name` USING BTREE (`event_name`,`UID`,`event`),
                ADD INDEX `event` USING BTREE (`UID`,`event`);
            ";
            $this->query($sql);
        }
    }
    
    //tpop 数据
    public function tpop1_2_data($ymd){
        
        $sql = "
            SELECT
                {$ymd} AS ymd, 
                sum(case when a.`event` = 'run' then a.install_num end) AS run_install,
                sum(case when a.`event` = 'show' then a.install_num end) AS show_install,
                sum(case when a.`event` = 'click-close' then a.install_num end) AS click_close_install,
                sum(case when a.`event` = 'click-title' then a.install_num end) AS click_title_install,
                sum(case when a.`event` = 'click-image-url' then a.install_num end) AS click_image_url_install,
                sum(case when a.`event` = 'click-morenwes-url' then a.install_num end) AS click_morenwes_url_install,
                sum(case when a.`event` = 'failed-fullscreen' then a.install_num end) AS failed_fullscreen_install,
                sum(case when a.`event` = 'failed-isrun' then a.install_num end) AS failed_isrun_install,
                sum(case when a.`event` = 'failed-loadurl' then a.install_num end) AS failed_loadurl_install,
                sum(case when a.`event` = 'exit' then a.install_num end) AS exit_install
            FROM (
            	SELECT 
            	   `event`,count(DISTINCT UID) AS install_num 
            	FROM `tpop3{$ymd}` GROUP BY `event`
            ) AS a
            ";
        $query_res = current($this->query($sql));
        return $query_res ? $query_res : [];
    }
    
    //tpop 竞品数据
    public function tpop1_2_jinpin_data($ymd){
        $sql ="
            SELECT
                count(DISTINCT case when  software= '360aqws' then UID else 0 end) AS install_360,
                count(DISTINCT case when  software= 'qqgj' then UID else 0 end) AS install_qqgj,
                count(DISTINCT case when  software= 'jsdb' then UID else 0 end) AS install_jsdb,
                count(DISTINCT case when  software not in ('360aqws','qqgj','jsdb') then UID else 0 end) AS install_other
           FROM 
           `jingpin_temp{$ymd}`
            ";
        $query_res = current($this->query($sql));
        return $query_res ? $query_res : [];
    }
    //tpop 竞品数据明细
    public function tpop1_2_jinpin_data_detail($ymd){
        //创建临时表
        $temp_table_name = "temptpop3{$ymd}";
        $sql = "
            CREATE TABLE `{$temp_table_name}` (
            `event_name` varchar(32) NOT NULL,
            `install_num` int(10) NOT NULL,
            `jpname` varchar(32) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ";
        $this->query($sql);
        
        //批量添加数据
        $sql ="
        	SELECT 
        	t.`event` AS event_name,
        	count(
        		DISTINCT 
        			case j.software 
        				when '360aqws' then j.UID
        				when 'qqgj' then j.UID
        				when 'jsdb' then j.UID
        				else j.UID end
        		) AS install_num,
        		 case j.software 
        			when '360aqws' then j.software
        			when 'qqgj' then j.software
        			when 'jsdb' then j.software
        			else 'other' end AS jpname
        	FROM `tpop3{$ymd}` AS t 
        	INNER JOIN 
        	`jingpin_temp{$ymd}` AS j ON t.UID = j.UID GROUP BY t.`event`
        ";
        $data_list = $this->query($sql);
        $this->addAll($data_list,false,$temp_table_name);
        
        //获取数据
        $sql = "
            SELECT
                {$ymd} AS ymd,
            	concat('tpop3_1_2.',event_name) AS eventname,
            	jpname,
                sum(install_num) AS install
            FROM {$temp_table_name}
            GROUP BY event_name,jpname
        ";
        $query_res = $this->query($sql);
        
        //删除临时表
        $drop_sql = "DROP TABLE {$temp_table_name};";
        $this->query($drop_sql);
        
        return $query_res ? $query_res : [];
    }
    
    //可弹版本数量
    public function tpop_version_data($ymd){
        $params = [
            'field' => 'sum(online) AS start_install',
            'where' => "`ver` >= '1.2.0.12' AND `ymd` = '{$ymd}'",
        ];
        return \Dao\Huayangnianhua_admin\Stat\Ver_data::get_instance()->find($params);
    }
    
    //tpop 数据
    public function tpop_data($ymd,$event_name){
        if(!in_array($event_name,$this->event_name)){
            return false;
        }
        $sql = "
            SELECT
            	{$ymd} AS ymd,
            	'{$event_name}' AS `name`,
            	sum(case when a.`event` = 'run' then a.install_num end) AS run_install,
            	sum(case when a.`event` = 'show' then a.install_num end) AS show_install,
            	sum(case when a.`event` = 'click-close' then a.install_num end) AS click_close_install,
            	sum(case when a.`event` = 'click-title' then a.install_num end) AS click_title_install,
            	sum(case when a.`event` = 'click-image-url' then a.install_num end) AS click_image_url_install,
            	sum(case when a.`event` = 'click-morenwes-url' then a.install_num end) AS click_morenwes_url_install,
            	sum(case when a.`event` = 'failed-fullscreen' then a.install_num end) AS failed_fullscreen_install,
                sum(case when a.`event` = 'failed-isrun' then a.install_num end) AS failed_isrun_install,
                sum(case when a.`event` = 'failed-loadurl' then a.install_num end) AS failed_loadurl_install,
                sum(case when a.`event` = 'exit' then a.install_num end) AS exit_install
            FROM (
            	SELECT `event`,count(DISTINCT UID) AS install_num FROM `tpop3{$ymd}` WHERE `event_name` = '{$event_name}' GROUP BY `event`
            ) AS a
            ";
        $query_res = current($this->query($sql));
        return $query_res ? $query_res : [];
    }
    
    //tpop 竞品数据明细
    public function tpop_jinpin_data_detail($ymd,$event_name){
        if(!in_array($event_name,$this->event_name)){
            return false;
        }
        
        //创建临时表
        $temp_table_name = "temp".str_replace('-', '_', $event_name).$ymd;
        $sql = "
            CREATE TABLE `{$temp_table_name}` (
            `event_name` varchar(32) NOT NULL,
            `install_num` int(10) NOT NULL,
            `jpname` varchar(32) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ";
        $this->query($sql);
        
        //批量添加数据
        $sql ="
                SELECT
                    t.event,
                    count(
                        DISTINCT
                        case j.software
                            when '360aqws' then j.UID
                            when 'qqgj' then j.UID
                            when 'jsdb' then j.UID
                            else j.UID end
                    ) AS install_num,
                    case j.software
                        when '360aqws' then j.software
                        when 'qqgj' then j.software
                        when 'jsdb' then j.software
                        else 'other' end AS jpname
                FROM `tpop3{$ymd}` AS t
                INNER JOIN
                `jingpin_temp{$ymd}` AS j ON t.UID = j.UID 
                WHERE `event_name` =  '{$event_name}' GROUP BY t.event
        ";
        $data_list = $this->query($sql);
        $this->addAll($data_list,false,$temp_table_name);
        
        //获取数据
        $sql = "
            SELECT
                {$ymd} AS ymd,
                concat('{$event_name}.',event_name) AS eventname,
                jpname,
                sum(install_num) AS install
            FROM {$temp_table_name}
            GROUP BY event_name,jpname
        ";
        $query_res = $this->query($sql);
        
        //删除临时表
        $drop_sql = "DROP TABLE {$temp_table_name};";
        $this->query($drop_sql);
    
        return $query_res ? $query_res : [];
    }
}
