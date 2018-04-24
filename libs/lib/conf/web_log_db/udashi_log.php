<?php
//QQ管家统计配置
return [
    'udashi_log'     => [
        'software'             => 'udashi',
        'db_config_key'       => 'db_udashi_log',//数据配置config key
        'redis_key_prefix'    => 'Logs:udashi:', //redis队列key前缀
        'rc4_key'              => '',//rc4密钥

        'action_list' => [
            'record',//到达
            'active',//使用 及点击
        ],

        //记录的参数，客户端约定好顺序不能更改
        'params' => [
            //共公参数
            'common' => [
                'qid' => '',  // 子渠道号ID
                'uid' => '', //
                'os' => '',//
                'browser' => '',//
                'firstCtg' => '', //
                'secondCtg' => '',  //
                'thirdCtg' => '',  //
                'linkName' => '',  //
                'linkIdx' => '',   //操作系统
                'linkUrl' => '',  //
                'dhname' => '',//
                'colum1' => '',//
                'colum2' => '',//
            ],
            'record' => [
            ],
            'active' => [
            ],
        ],

        'log_table_sql' => [//日志表建表sql
            'record' => "
CREATE TABLE `record{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `timeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `ip` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `qid` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `uid` CHAR(50) DEFAULT NULL COMMENT '唯一标示',
  `os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `browser` VARCHAR(255) DEFAULT NULL COMMENT '浏览器',
  `firstCtg` VARCHAR(255) DEFAULT NULL COMMENT '一级分类',
  `secondCtg` VARCHAR(255) DEFAULT NULL COMMENT '二级分类',
  `thirdCtg` VARCHAR(255) DEFAULT NULL COMMENT '三级分类',
  `linkName` VARCHAR(255) DEFAULT NULL COMMENT '链接名',
  `linkIdx` VARCHAR(255) DEFAULT NULL COMMENT '链接顺序',
  `linkUrl` VARCHAR(255) DEFAULT NULL COMMENT '链接地址',
  `dhname` VARCHAR(255) DEFAULT NULL COMMENT '名称',
  `colum1` VARCHAR(5000) DEFAULT NULL COMMENT '',
  `colum2` VARCHAR(5000) DEFAULT NULL COMMENT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='到达接口数据';
            ",
            'active' => "
CREATE TABLE `active{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `timeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `ip` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `qid` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `uid` CHAR(50) DEFAULT NULL COMMENT '唯一标示',
  `os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `browser` VARCHAR(255) DEFAULT NULL COMMENT '浏览器',
  `firstCtg` VARCHAR(255) DEFAULT NULL COMMENT '一级分类',
  `secondCtg` VARCHAR(255) DEFAULT NULL COMMENT '二级分类',
  `thirdCtg` VARCHAR(255) DEFAULT NULL COMMENT '三级分类',
  `linkName` VARCHAR(255) DEFAULT NULL COMMENT '链接名',
  `linkIdx` VARCHAR(255) DEFAULT NULL COMMENT '链接顺序',
  `linkUrl` VARCHAR(255) DEFAULT NULL COMMENT '链接地址',
  `dhname` VARCHAR(255) DEFAULT NULL COMMENT '名称',
  `colum1` VARCHAR(5000) DEFAULT NULL COMMENT '',
  `colum2` VARCHAR(5000) DEFAULT NULL COMMENT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='使用接口数据';
            "
        ]
    ]
];
